<?php
	require_once("../../../class/jdf.php");
	class SSP {
		/**
		* Create the data output array for the DataTables rows
		*
		*  @param  array $columns Column information array
		*  @param  array $data    Data from the SQL get
		*  @return array          Formatted data in a row based format
		*/
		static function data_output ( $columns, $data )
		{
			$out = array();

			for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
				$row = array();

				for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
					$column = $columns[$j];

					// Is there a formatter?
					if( isset( $column['formatter'] ) ) {
						if(empty($column['db'])){
							$row[ $column['dt'] ] = $column['formatter']( $data[$i] );
						}
						else{
							$row[ $column['dt'] ] = $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
						}
					}
					else {
						if(!empty($column['db'])){
							$row[ $column['dt'] ] = $data[$i][ $columns[$j]['db'] ];
						}
						else{
							$row[ $column['dt'] ] = "";
						}
					}
				}

				$out[] = $row;
			}

			return $out;
		}


		/**
		* Database connection
		*
		* Obtain an PHP PDO connection from a connection details array
		*
		*  @param  array $conn SQL connection details. The array should have
		*    the following properties
		*     * host - host name
		*     * db   - database name
		*     * user - user name
		*     * pass - user password
		*  @return resource PDO connection
		*/
		static function db ( $conn )
		{
			if( is_array( $conn ) ) {
				return self::sql_connect( $conn );
			}

			return $conn;
		}


		/**
		* Paging
		*
		* Construct the LIMIT clause for server-side processing SQL query
		*
		*  @param  array $request Data sent to server by DataTables
		*  @param  array $columns Column information array
		*  @return string SQL limit clause
		*/
		static function limit ( $request, $columns )
		{
			$limit = '';

			if( isset($request['start']) && $request['length'] != -1 ) {
				$limit = "LIMIT ".intval($request['start']).", ".intval($request['length']);
			}

			return $limit;
		}


		/**
		* Ordering
		*
		* Construct the ORDER BY clause for server-side processing SQL query
		*
		*  @param  array $request Data sent to server by DataTables
		*  @param  array $columns Column information array
		*  @return string SQL order by clause
		*/
		static function order ( $request, $columns )
		{
			$order = '';

			if( isset($request['order']) && count($request['order']) ) {
				$orderBy = array();
				$dtColumns = self::pluck( $columns, 'dt' );

				for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
					// Convert the column index into the column data property
					$columnIdx = intval($request['order'][$i]['column']);
					$requestColumn = $request['columns'][$columnIdx];

					$columnIdx = array_search( $requestColumn['data'], $dtColumns );
					$column = $columns[ $columnIdx ];

					if( $requestColumn['orderable'] == 'true' ) {
						$dir = $request['order'][$i]['dir'] === 'asc' ?
							'ASC' :
							'DESC';

						$orderBy[] = '`'.$column['db'].'` '.$dir;
					}
				}

				if( count( $orderBy ) ) {
					$order = 'ORDER BY '.implode(', ', $orderBy);
				}
			}

			return $order;
		}


		/**
		* Searching / Filtering
		*
		* Construct the WHERE clause for server-side processing SQL query.
		*
		* NOTE this does not match the built-in DataTables filtering which does it
		* word by word on any field. It's possible to do here performance on large
		* databases would be very poor
		*
		*  @param  array $request Data sent to server by DataTables
		*  @param  array $columns Column information array
		*  @param  array $bindings Array of values for PDO bindings, used in the
		*    sql_exec() function
		*  @return string SQL where clause
		*/
		static function filter ( $request, $columns, &$bindings )
		{
			$globalSearch = array();
			$columnSearch = array();
			$dtColumns = self::pluck( $columns, 'dt' );

			if( isset($request['search']) && $request['search']['value'] != '' ) {
				$str = $request['search']['value'];

				for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
					$requestColumn = $request['columns'][$i];
					$columnIdx = array_search( $requestColumn['data'], $dtColumns );
					$column = $columns[ $columnIdx ];

					if( $requestColumn['searchable'] == 'true' ) {
						if(!empty($column['db'])){
							$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
							$globalSearch[] = "`".$column['db']."` LIKE ".$binding;
						}
					}
				}
			}

			// Individual column filtering
			if( isset( $request['columns'] ) ) {
				for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
					$requestColumn = $request['columns'][$i];
					$columnIdx = array_search( $requestColumn['data'], $dtColumns );
					$column = $columns[ $columnIdx ];

					$str = $requestColumn['search']['value'];

					if( $requestColumn['searchable'] == 'true' &&
					$str != '' ) {
						if(!empty($column['db'])){
							$binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
							$columnSearch[] = "`".$column['db']."` LIKE ".$binding;
						}
					}
				}
			}

			// Combine the filters into a single string
			$where = '';

			if( count( $globalSearch ) ) {
				$where = '('.implode(' OR ', $globalSearch).')';
			}

			if( count( $columnSearch ) ) {
				$where = $where === '' ?
					implode(' AND ', $columnSearch) :
					$where .' AND '. implode(' AND ', $columnSearch);
			}

			if( $where !== '' ) {
				$where = 'WHERE '.$where;
			}

			return $where;
		}


		/**
		* Perform the SQL queries needed for an server-side processing requested,
		* utilising the helper functions of this class, limit(), order() and
		* filter() among others. The returned array is ready to be encoded as JSON
		* in response to an SSP request, or can be modified if needed before
		* sending back to the client.
		*
		*  @param  array $request Data sent to server by DataTables
		*  @param  array|PDO $conn PDO connection resource or connection parameters array
		*  @param  string $table SQL table to query
		*  @param  string $primaryKey Primary key of the table
		*  @param  array $columns Column information array
		*  @return array          Server-side processing response array
		*/
		static function simple ( $request, $conn, $table, $primaryKey, $columns )
		{
			$bindings = array();
			$db = self::db( $conn );

			// Build the SQL query string from the request
			$limit = self::limit( $request, $columns );
			$order = self::order( $request, $columns );
			$where = self::filter( $request, $columns, $bindings );

			// Main query to actually get the data
			$data = self::sql_exec( $db, $bindings,
				"SELECT `".implode("`, `", self::pluck($columns, 'db'))."`
				FROM `$table`
				$where
				$order
				$limit"
			);

			// Data set length after filtering
			$resFilterLength = self::sql_exec( $db, $bindings,
				"SELECT COUNT(`{$primaryKey}`)
				FROM   `$table`
				$where"
			);
			$recordsFiltered = $resFilterLength[0][0];

			$recordsTotal = $GLOBALS["connection"]->query("SELECT * FROM " . $table)->rowCount();

			/*
			* Output
			*/
			return array(
				"draw"            => isset ( $request['draw'] ) ?
					intval( $request['draw'] ) :
					0,
				"recordsTotal"    => intval( $recordsTotal ),
				"recordsFiltered" => intval( $recordsFiltered ),
				"data"            => self::data_output( $columns, $data )
			);
		}


		/**
		* The difference between this method and the `simple` one, is that you can
		* apply additional `where` conditions to the SQL queries. These can be in
		* one of two forms:
		*
		* * 'Result condition' - This is applied to the result set, but not the
		*   overall paging information query - i.e. it will not effect the number
		*   of records that a user sees they can have access to. This should be
		*   used when you want apply a filtering condition that the user has sent.
		* * 'All condition' - This is applied to all queries that are made and
		*   reduces the number of records that the user can access. This should be
		*   used in conditions where you don't want the user to ever have access to
		*   particular records (for example, restricting by a login id).
		*
		*  @param  array $request Data sent to server by DataTables
		*  @param  array|PDO $conn PDO connection resource or connection parameters array
		*  @param  string $table SQL table to query
		*  @param  string $primaryKey Primary key of the table
		*  @param  array $columns Column information array
		*  @param  string $whereResult WHERE condition to apply to the result set
		*  @param  string $whereAll WHERE condition to apply to all queries
		*  @return array          Server-side processing response array
		*/
		static function complex ( $request, $conn, $table, $primaryKey, $columns, $whereResult=null, $whereAll=null )
		{
			$bindings = array();
			$db = self::db( $conn );
			$localWhereResult = array();
			$localWhereAll = array();
			$whereAllSql = '';

			// Build the SQL query string from the request
			$limit = self::limit( $request, $columns );
			$order = self::order( $request, $columns );
			$where = self::filter( $request, $columns, $bindings );

			$whereResult = self::_flatten( $whereResult );
			$whereAll = self::_flatten( $whereAll );

			if( $whereResult ) {
				$where = $where ?
					$where .' AND '.$whereResult :
					'WHERE '.$whereResult;
			}

			if( $whereAll ) {
				$where = $where ?
					$where .' AND '.$whereAll :
					'WHERE '.$whereAll;

				$whereAllSql = 'WHERE '.$whereAll;
			}

			// Main query to actually get the data
			$data = self::sql_exec( $db, $bindings,
				"SELECT `".implode("`, `", self::pluck($columns, 'db'))."`
				FROM `$table`
				$where
				$order
				$limit"
			);

			// Data set length after filtering
			$resFilterLength = self::sql_exec( $db, $bindings,
				"SELECT COUNT(`{$primaryKey}`)
				FROM   `$table`
				$where"
			);
			$recordsFiltered = $resFilterLength[0][0];

			$recordsTotal = $GLOBALS["connection"]->query("SELECT * FROM " . $table." ".$whereAllSql)->rowCount();

			/*
			* Output
			*/
			return array(
				"draw"            => isset ( $request['draw'] ) ?
					intval( $request['draw'] ) :
					0,
				"recordsTotal"    => intval( $recordsTotal ),
				"recordsFiltered" => intval( $recordsFiltered ),
				"data"            => self::data_output( $columns, $data )
			);
		}

		static function table_view ( $request, $conn, $table, $primaryKey, $columns, $whereResult=null, $whereAll=null )
		{
			$bindings = array();
			$db = self::db( $conn );
			$localWhereResult = array();
			$localWhereAll = array();
			$whereAllSql = '';

			// Build the SQL query string from the request
			$limit = self::limit( $request, $columns );
			$order = self::order( $request, $columns );
			$where = self::filter( $request, $columns, $bindings );

			$whereResult = self::_flatten( $whereResult );
			$whereAll = self::_flatten( $whereAll );

			if( $whereResult ) {
				$where = $where ?
					$where .' AND '.$whereResult :
					'WHERE '.$whereResult;
			}

			if( $whereAll ) {
				$where = $where ?
					$where .' AND '.$whereAll :
					'WHERE '.$whereAll;

				$whereAllSql = 'WHERE '.$whereAll;
			}

			// Main query to actually get the data
			$data = self::sql_exec( $db, $bindings,
				"SELECT `".implode("`, `", self::pluck($columns, 'db'))."`
				FROM `$table`
				$where
				$order
				$limit"
			);
			if($table!=$GLOBALS['sub_name']."table_config"){
				$table_got = $GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_config WHERE current_name='".$table."'")->fetch();
				foreach ($data as $key => $value) {
					foreach ($value as $column_name => $data_value) {
						if(!is_int($column_name) && $column_name!="act" && $column_name!="ordering" && $column_name!="id"){
							$column_got = $GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE current_name='".$column_name."' AND table_id='".$table_got['id']."'")->fetch();
							?><?php //info search this_is_modes_for_data_tables for see all things about this part ?><?php
							switch ($column_got['mode']) {//tables_mode_code
								case '1':case 1:case '2':case 2:case "18":case 18://info search case 18 for see all things about this part//info search case 1 for see all things about this part//info search case 2 for see all things about this part
									if(strlen($data[$key][$column_name])>=35){
										$customText="...";
									}else{
										$customText="";
									}
									$data[$key][$column_name]="<label class='hide_data hide'>" . $data[$key][$column_name] . "</label><label class='show_data'>".mb_substr($data[$key][$column_name], 0, 35)."&nbsp;".$customText."</label>";
								break;
								case '3':case 3: //info search case 3 for see all things about this part
									$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."yes_no_question_options WHERE table_id='".$table_got['id']."' AND column_id='".$column_got['id']."' AND act=1");
									$options=$res_options->fetch();
									if($res_options->rowCount()){
										if($options['yes_value']==$data[$key][$column_name]){
											$data[$key][$column_name]=$options['yes_option'];
										}else if($options['no_value']==$data[$key][$column_name]){
											$data[$key][$column_name]=$options['no_option'];
										}else{
											$data[$key][$column_name]=($GLOBALS['user_language']=="en" ? "Unknow":"نا مشخص");
										}
									}else{
										$data[$key][$column_name]=($data[$key][$column_name] ? ($GLOBALS['user_language']=="en" ? "Yes":"بله"):($GLOBALS['user_language']=="en" ? "No":"نه"));
									}
									if(strlen($data[$key][$column_name])>=35){
										$customText="...";
									}else{
										$customText="";
									}
									$data[$key][$column_name]="<label class='hide_data hide'>" . $data[$key][$column_name] . "</label><label class='show_data'>".mb_substr($data[$key][$column_name], 0,35)."&nbsp;".$customText."</label>";
								break;
								case '4':case 4://info search case 4 for see all things about this part
									$newxdata="";
									unset($newxdata);
									$res_options_setting=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options_setting WHERE table_id='".$table_got['id']."' AND column_id='".$column_got['id']."' AND act=1");
									if($res_options_setting->rowCount()){
										$options_setting=$res_options_setting->fetch();
									}else{
										$options_setting=["is_multiple"=>0,"is_forced"=>0,"min_allowed"=>0,"max_allowed"=>0];
									}
									if($options_setting['is_multiple']){
										if(strpos($data[$key][$column_name],'_-.,.-_')){
											$data[$key][$column_name]=explode("_-.,.-_",$data[$key][$column_name]);
										}else{
											$data[$key][$column_name]=[$data[$key][$column_name]];
										}
										foreach ($data[$key][$column_name] as &$xvalue) {
											if(strpos($xvalue,'_-...-_')){
												$xdata=explode("_-...-_",$xvalue)[1];
											}else{
												$xdata=$xvalue;
											}
											if(strpos($xdata,'_-..-_')){
												$xdata_optVAL=explode("_-..-_",$xdata)[1];
												$xdata_optID=explode("_-..-_",$xdata)[0];
												$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_got['id']."' AND column_id='".$column_got['id']."' AND id='".$xdata_optID."' AND act=1");
												if($res_options->rowCount()){
													$options=$res_options->fetch();
													if($options['connected_table']>0){
														$res_connected_table=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_config WHERE id='".$options['connected_table']."'");
														if($res_connected_table->rowCount()){
															$connected_table=$res_connected_table->fetch();
															$res_connected_table_column=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE table_id='".$options['connected_table']."' AND id='".$options['option_value']."'");
															$res_connected_table_column_show=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE table_id='".$options['connected_table']."' AND id='".$options['option_text']."'");
															if(($res_connected_table_column->rowCount() || $options['option_value']==0) && ($res_connected_table_column_show->rowCount() || $options['option_text']==0)){
																$connected_table_column=$res_connected_table_column->fetch();
																$connected_table_column_show=$res_connected_table_column_show->fetch();
																$res_data_connected_table=$GLOBALS['connection']->query("SELECT * FROM ".$connected_table['current_name']." WHERE ".($options['option_value'] ? $connected_table_column['current_name']:"id")."='".$xdata_optVAL."'");
																if($res_data_connected_table->rowCount()){
																	$data_connected_table=$res_data_connected_table->fetch();
																	$xsdata=$data_connected_table[($options['option_text'] ? $connected_table_column_show['current_name']:"id")];
																}else{
																	$xsdata=$xdata;
																}
															}else{
																$xsdata=$xdata;
															}
														}else{
															$xsdata=$xdata;
														}
													}else{
														$xsdata=$xdata;
													}
												}else{
													$xsdata=$xdata;
												}
											}else{
												if(strpos($xdata,'_-.-_')){
													$xdata=explode("_-.-_",$xvalue)[1];
													$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_got['id']."' AND column_id='".$column_got['id']."' AND option_value='".$xdata."' AND act=1");
													if($res_options->rowCount()){
														$options=$res_options->fetch();
														$xsdata=$options['option_text'];
													}else{
														$xsdata=$xdata;
													}
												}else{
													$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_got['id']."' AND column_id='".$column_got['id']."' AND option_value='".$xdata."' AND act=1");
													if($res_options->rowCount()){
														$options=$res_options->fetch();
														$xsdata=$options['option_text'];
													}else{
														$xsdata=$xdata;
													}
												}
											}
											if(isset($newxdata) && $newxdata!="" && !empty($newxdata)){
												$newxdata.=",".$xsdata;
											}else{
												$newxdata=$xsdata;
											}
										}
										$data[$key][$column_name]=$newxdata;
									}else{
										if(strpos($data[$key][$column_name],'_-.,.-_')){
											$data[$key][$column_name]=explode("_-.,.-_",$data[$key][$column_name]);
										}else{
											$data[$key][$column_name]=[$data[$key][$column_name]];
										}
										foreach ($data[$key][$column_name] as &$xvalue) {
											if(strpos($xvalue,'_-...-_')){
												$xdata=explode("_-...-_",$xvalue)[1];
											}else{
												$xdata=$xvalue;
											}
											if(strpos($xdata,'_-..-_')){
												$xdata_optVAL=explode("_-..-_",$xdata)[1];
												$xdata_optID=explode("_-..-_",$xdata)[0];
												$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_got['id']."' AND column_id='".$column_got['id']."' AND id='".$xdata_optID."' AND act=1");
												if($res_options->rowCount()){
													$options=$res_options->fetch();
													if($options['connected_table']>0){
														$res_connected_table=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_config WHERE id='".$options['connected_table']."'");
														if($res_connected_table->rowCount()){
															$connected_table=$res_connected_table->fetch();
															$res_connected_table_column=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE table_id='".$options['connected_table']."' AND id='".$options['option_value']."'");
															$res_connected_table_column_show=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE table_id='".$options['connected_table']."' AND id='".$options['option_text']."'");
															if(($res_connected_table_column->rowCount() || $options['option_value']==0) && ($res_connected_table_column_show->rowCount() || $options['option_text']==0)){
																$connected_table_column=$res_connected_table_column->fetch();
																$connected_table_column_show=$res_connected_table_column_show->fetch();
																$res_data_connected_table=$GLOBALS['connection']->query("SELECT * FROM ".$connected_table['current_name']." WHERE ".($options['option_value'] ? $connected_table_column['current_name']:"id")."='".$xdata_optVAL."'");
																if($res_data_connected_table->rowCount()){
																	$data_connected_table=$res_data_connected_table->fetch();
																	$xsdata=$data_connected_table[($options['option_text'] ? $connected_table_column_show['current_name']:"id")];
																}else{
																	$xsdata=$xdata;
																}
															}else{
																$xsdata=$xdata;
															}
														}else{
															$xsdata=$xdata;
														}
													}else{
														$xsdata=$xdata;
													}
												}else{
													$xsdata=$xdata;
												}
											}else{
												if(strpos($xdata,'_-.-_')){
													$xdata=explode("_-.-_",$xvalue)[1];
													$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_got['id']."' AND column_id='".$column_got['id']."' AND option_value='".$xdata."' AND act=1");
													if($res_options->rowCount()){
														$options=$res_options->fetch();
														$xsdata=$options['option_text'];
													}else{
														$xsdata=$xdata;
													}
												}else{
													$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_got['id']."' AND column_id='".$column_got['id']."' AND option_value='".$xdata."' AND act=1");
													if($res_options->rowCount()){
														$options=$res_options->fetch();
														$xsdata=$options['option_text'];
													}else{
														$xsdata=$xdata;
													}
												}
											}
											if(isset($newxdata) && $newxdata!="" && !empty($newxdata)){
												$newxdata.=",".$xsdata;
											}else{
												$newxdata=$xsdata;
											}
										}
										$data[$key][$column_name]=$newxdata;
									}
									if(strlen($data[$key][$column_name])>=35){
										$customText="...";
									}else{
										$customText="";
									}
									$data[$key][$column_name]="<label class='hide_data hide'>" . $data[$key][$column_name] . "</label><label class='show_data'>".mb_substr($data[$key][$column_name], 0,35)."&nbsp;".$customText."</label>";
								break;
								case '5':case 5://info search case 5 for see all things about this part
									$data[$key][$column_name]='<span class="badge color-shows"'."style='background: ".$data[$key][$column_name].";'".'>'.($GLOBALS['user_language']=="en" ? "Color":"رنگ").'</span>';
								break;
								case '6':case 6://info search case 6 for see all things about this part
									$data[$key][$column_name]="<i class='far fa-shield text-success fa-2x'></i>";
								break;
								case '7':case 7://info search case 7 for see all things about this part
									$data[$key][$column_name]='<a href="'.(strlen($data[$key][$column_name]) ? $data[$key][$column_name]:"javascript:void(0)").'" target="'.(strlen($data[$key][$column_name]) ? "_blank":"").'" style="margin-bottom: 0px !important;"><i class="far fa-link text-info fa-2x"></i></a>';
								break;
								case '8':case 8:case '19':case 19://info search case 8 for see all things about this part//info search case 19 for see all things about this part
									$data[$key][$column_name]='
										<a href="javascript:void(0)" data-toggle="modal" data-target="#tid_'.$table_got['id'].'-cid_'.$column_got['id'].'-did_'.$data[$key]['id'].'">
											<i class="far fa-box-open text-primary fa-2x"></i>
										</a>
										<div class="modal" id="tid_'.$table_got['id'].'-cid_'.$column_got['id'].'-did_'.$data[$key]['id'].'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
											<div class="modal-dialog modal-xl" style="transform: translateY(0%);">
												<div class="modal-content bg-dark">
													<div class="modal-body">
														<ckeditor>
															'.$data[$key][$column_name].'
														</ckeditor>
													</div>
													<div class="modal-footer justify-content-center">
														<button type="button" class="btn btn-warning btn-round" data-dismiss="modal">'.($GLOBALS['user_language']=="en" ? "Close":"خروج").'</button>
													</div>
												</div>
											</div>
										</div>
									';
								break;
								case '9':case 9://info search case 9 for see all things about this part
									$values=0;
									if(strlen($data[$key][$column_name])){
										foreach (explode("_-...-_",$data[$key][$column_name]) as &$value) {
											if($values==0){
												$values.=",".$value;
											}else{
												$values=$value;
											}
										}
									}
									$data[$key][$column_name]="";
									$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."checkbox_options WHERE table_id='".$table_got['id']."' AND column_id='".$column_got['id']."' AND option_value IN (".$values.") AND act=1");
									while ($options=$res_options->fetch()) {
										if(strlen($data[$key][$column_name])){
											$data[$key][$column_name].=",".$options["option_name"];
										}else{
											$data[$key][$column_name]=$options["option_name"];
										}
									}
									if(strlen($data[$key][$column_name])>=35){
										$customText="...";
									}else{
										$customText="";
									}
									$data[$key][$column_name]="<label class='hide_data hide'>" . $data[$key][$column_name] . "</label><label class='show_data'>".mb_substr($data[$key][$column_name], 0,35)."&nbsp;".$customText."</label>";
								break;
								case '10':case 10://info search case 10 for see all things about this part
									$data[$key][$column_name]="<i class='".$data[$key][$column_name]." text-primary fa-2x'></i>";
								break;
								case '12':case 12://info search case 12 for see all things about this part
									$data[$key][$column_name]=(strlen($data[$key][$column_name])>3 ? date("d/m/Y",mb_substr($data[$key][$column_name], 0, -3)):"");
									if(strlen($data[$key][$column_name])>=35){
										$customText="...";
									}else{
										$customText="";
									}
									$data[$key][$column_name]="<label class='hide_data hide'>" . $data[$key][$column_name] . "</label><label class='show_data'>".mb_substr($data[$key][$column_name], 0,35)."&nbsp;".$customText."</label>";
								break;
								case '13':case 13://info search case 13 for see all things about this part
									$data[$key][$column_name]=(strlen($data[$key][$column_name])>3 ? date("d/m/Y H:i:s",mb_substr($data[$key][$column_name], 0, -3)):"");
									if(strlen($data[$key][$column_name])>=35){
										$customText="...";
									}else{
										$customText="";
									}
									$data[$key][$column_name]="<label class='hide_data hide'>" . $data[$key][$column_name] . "</label><label class='show_data'>".mb_substr($data[$key][$column_name], 0,35)."&nbsp;".$customText."</label>";
								break;
								case '14':case 14://info search case 14 for see all things about this part
									if($data[$key][$column_name]!=0 && !empty($data[$key][$column_name]) && !$data[$key][$column_name]==""){
										$data[$key][$column_name]=(strlen($data[$key][$column_name])>3 ? jdate("Y/m/d",mb_substr($data[$key][$column_name], 0, -3),'','','en'):"");
										if(strlen($data[$key][$column_name])>=35){
											$customText="...";
										}else{
											$customText="";
										}
									}else{
										$data[$key][$column_name]="";
									}
									$data[$key][$column_name]="<label class='hide_data hide'>" . $data[$key][$column_name] . "</label><label class='show_data'>".mb_substr($data[$key][$column_name], 0,35)."&nbsp;".$customText."</label>";
								break;
								case '15':case 15://info search case 15 for see all things about this part
									if($data[$key][$column_name]!=0 && !empty($data[$key][$column_name]) && !$data[$key][$column_name]==""){
										$data[$key][$column_name]=(strlen($data[$key][$column_name])>3 ? jdate("Y/m/d H:i:s",mb_substr($data[$key][$column_name], 0, -3),'','','en'):"");
										if(strlen($data[$key][$column_name])>=35){
											$customText="...";
										}else{
											$customText="";
										}
									}else{
										$data[$key][$column_name]="";
									}
									$data[$key][$column_name]="<label class='hide_data hide'>" . $data[$key][$column_name] . "</label><label class='show_data'>".mb_substr($data[$key][$column_name], 0,35)."&nbsp;".$customText."</label>";
								break;
								case '16':case 16://info search case 16 for see all things about this part
									$data[$key][$column_name]=(strlen($data[$key][$column_name])>3 ? date("H:i:s",mb_substr($data[$key][$column_name], 0, -3)):"");
									if(strlen($data[$key][$column_name])>=35){
										$customText="...";
									}else{
										$customText="";
									}
									$data[$key][$column_name]="<label class='hide_data hide'>" . $data[$key][$column_name] . "</label><label class='show_data'>".mb_substr($data[$key][$column_name], 0,35)."&nbsp;".$customText."</label>";
								break;
							}
						}else if($column_name=="act"){
							if($_SESSION["username"]!=getSetting("op_admin")){
								$data[$key][$column_name]=($data[$key][$column_name] ? ($GLOBALS['user_language']=="en" ? "Active":"فعال"):($GLOBALS['user_language']=="en" ? "Deactive":"غیرفعال"));
							}
						}
					}
				}
			}

			// Data set length after filtering
			$resFilterLength = self::sql_exec( $db, $bindings,
				"SELECT COUNT(`{$primaryKey}`)
				FROM   `$table`
				$where"
			);
			$recordsFiltered = $resFilterLength[0][0];

			$recordsTotal = $GLOBALS["connection"]->query("SELECT * FROM " . $table." ".$whereAllSql)->rowCount();

			/*
			* Output
			*/
			return array(
				"draw"            => isset ( $request['draw'] ) ?
					intval( $request['draw'] ) :
					0,
				"recordsTotal"    => intval( $recordsTotal ),
				"recordsFiltered" => intval( $recordsFiltered ),
				"data"            => self::data_output( $columns, $data )
			);
		}

		static function complex_table_columns ( $request, $conn, $table, $primaryKey, $columns, $whereResult=null, $whereAll=null )
		{
			$bindings = array();
			$db = self::db( $conn );
			$localWhereResult = array();
			$localWhereAll = array();
			$whereAllSql = '';

			// Build the SQL query string from the request
			$limit = self::limit( $request, $columns );
			$order = self::order( $request, $columns );
			$where = self::filter( $request, $columns, $bindings );

			$whereResult = self::_flatten( $whereResult );
			$whereAll = self::_flatten( $whereAll );

			$res_table_config = $GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"] . "table_config WHERE lock_admin_id='" . $_SESSION["username"] . "'");
			$table_id = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch()['id'] : 0);

			$my_custom_where="table_id='".$table_id."' AND current_name!='ordering' AND current_name!='act'";

			$where=($where=="" ? "WHERE ".$my_custom_where:$where." AND ".$my_custom_where);

			if( $whereResult ) {
				$where = $where ?
					$where .' AND '.$whereResult :
					'WHERE '.$whereResult;
			}

			if( $whereAll ) {
				$where = $where ?
					$where .' AND '.$whereAll :
					'WHERE '.$whereAll;

				$whereAllSql = 'WHERE '.$whereAll;
			}

			$whereAllSql=($whereAllSql=="" ? "WHERE ".$my_custom_where:" AND ".$whereAllSql." AND ".$my_custom_where);

			// Main query to actually get the data
			$data = self::sql_exec( $db, $bindings,
				"SELECT `".implode("`, `", self::pluck($columns, 'db'))."`
				FROM `$table`
				$where
				$order
				$limit"
			);

			// Data set length after filtering
			$resFilterLength = self::sql_exec( $db, $bindings,
				"SELECT COUNT(`{$primaryKey}`)
				FROM   `$table`
				$where"
			);
			$recordsFiltered = $resFilterLength[0][0];

			$recordsTotal = $GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"] . "table_column_config WHERE ".$my_custom_where)->rowCount();

			/*
				* Output
			*/
			return array(
				"draw"            => isset ( $request['draw'] ) ?
					intval( $request['draw'] ) :
					0,
				"recordsTotal"    => intval( $recordsTotal ),
				"recordsFiltered" => intval( $recordsFiltered ),
				"data"            => self::data_output( $columns, $data )
			);
		}

		static function select_option_table ( $request, $conn, $table, $primaryKey, $columns, $whereResult=null, $whereAll=null)
		{
			$bindings = array();
			$db = self::db( $conn );
			$localWhereResult = array();
			$localWhereAll = array();
			$whereAllSql = '';

			// Build the SQL query string from the request
			$limit = self::limit( $request, $columns );
			$order = self::order( $request, $columns );
			$where = self::filter( $request, $columns, $bindings );

			$table_id=$whereResult[0];
			$column_id=$whereResult[1];

			$whereResult = self::_flatten( $whereResult );
			$whereAll = self::_flatten( $whereAll );

			$my_custom_where="table_id IN (".($table_id ? $table_id:0).") AND column_id IN (".($column_id ? $column_id:0).")";

			$where=($where=="" ? "WHERE ".$my_custom_where:$where." AND ".$my_custom_where);
			// WHERE (`current_name` LIKE :binding_0 OR `option_value` LIKE :binding_1) AND table_id IN (23) AND column_id IN (220)

			if( $whereResult ) {
				$where = $where ?
					$where .' AND '.$whereResult :
					'WHERE '.$whereResult;
			}

			if( $whereAll ) {
				$where = $where ?
					$where .' AND '.$whereAll :
					'WHERE '.$whereAll;

				$whereAllSql = 'WHERE '.$whereAll;
			}

			$whereAllSql=($whereAllSql=="" ? "WHERE ".$my_custom_where:$whereAllSql." AND ".$my_custom_where);

			// Main query to actually get the data
			$data = self::sql_exec( $db, $bindings,
				"SELECT `".implode("`, `", self::pluck($columns, 'db'))."`
				FROM `$table`
				$where
				$order
				$limit"
			);

			// Data set length after filtering
			$resFilterLength = self::sql_exec( $db, $bindings,
				"SELECT COUNT(`{$primaryKey}`)
				FROM   `$table`
				$where"
			);
			$recordsFiltered = $resFilterLength[0][0];

			$recordsTotal = $GLOBALS["connection"]->query("SELECT * FROM " . $table." ".$whereAllSql)->rowCount();

			/*
				* Output
			*/
			$datas=$GLOBALS["connection"]->query("SELECT * FROM ".$table." WHERE ".$my_custom_where." AND connected_table>0")->fetchAll();
			$comma_fixer=0;
			$table_data_id="";
			$column_data_id="";
			$column_data_id="";
			$datas_id="";
			foreach ($datas as $i => $value) {
				$comma=($comma_fixer ? ",":"");
				$comma_fixer=1;
				$table_data_id.=(!in_array($datas[$i]['connected_table'], explode(",", $table_data_id)) ? $comma.$datas[$i]['connected_table']:"");
				$column_data_id.=(!in_array($datas[$i]['option_text'], explode(",", $column_data_id)) ? $comma.$datas[$i]['option_text']:"");
				$column_data_id.=(!in_array($datas[$i]['option_value'], explode(",", $column_data_id)) ? $comma.$datas[$i]['option_value']:"");
				$datas_id.=(!in_array($datas[$i]['id'], explode(",", $datas_id)) ? $comma.$datas[$i]['id']:"");
			}
			foreach (array_filter($data, fn($e) => $e['connected_table']>0) as $i => $value) {
				//info you can get help by searching this #duplicate-2 this is duplicated code and you have to change all duplicates to gether
				$table_name=$GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"]."table_config WHERE id='".$data[$i]['connected_table']."'")->fetch();
				$current_custom_query=$GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"]."table_column_config WHERE table_id='".$data[$i]['connected_table']."' AND id='".$data[$i]['option_text']."'");
				$option_name=($data[$i]['option_text'] && $current_custom_query->rowCount() ? $current_custom_query->fetch()["current_name"]:"id");
				$option_value=($data[$i]['option_value'] ? $GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"]."table_column_config WHERE table_id='".$data[$i]['connected_table']."' AND id='".$data[$i]['option_value']."'")->fetch()["current_name"]:"id");
				$get_optgroup=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE id='".$data[$i]['optgroup_id']."'");
				$data[$i]['option_text']=preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $table_name['current_name'], 1)." -> ".$option_name;
				$data[$i]['option_value']=preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $table_name['current_name'], 1)." -> ".$option_value;
				$data[$i]['optgroup_id']=($data[$i]['optgroup_id']=="-1" || $data[$i]['optgroup_id']=="-2" ? ($data[$i]['optgroup_id']==-2 ? preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $table_name['description_name_en'], 1):preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $table_name['description_name_fa'], 1)):($get_optgroup->rowCount() ? $get_optgroup->fetch()['option_text']:"-"));
				//info you can get help by searching this #duplicate-2 this is duplicated code and you have to change all duplicates to gether
			}
			foreach (array_filter($data, fn($e) => $e['optgroup_id']!="" && $e['optgroup_id']!="-" && !empty($e['optgroup_id']) && intval($e['optgroup_id'])) as $i => $value) {
				$optgroup=$GLOBALS["connection"]->query("SELECT * FROM ".$table." WHERE id='".$data[$i]['optgroup_id']."'")->fetch();
				$data[$i]['optgroup_id']=$optgroup['option_text'];
			}
			if(trim($request['search']['value'])!=""){
				$default_search_key=$request['search']['value'];
				$spliter=explode(" -> ",$request['search']['value']);
				$search_key=(count($spliter)==2 ? $spliter:[$request['search']['value']]);
				$search_tools=$search_key[0];
				$res_tables=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE id IN (".($table_data_id ? $table_data_id:0).") AND (current_name LIKE '%$search_tools%' OR current_name LIKE '%$default_search_key%')");
				while($tables=$res_tables->fetch()){
					$res_data_get=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE id IN (".($datas_id ? $datas_id:0).") AND connected_table='".$tables['id']."'");
					while ($data_get=$res_data_get->fetch()) {
						if(count(array_filter($data, fn($e) => $e['id']==$data_get['id']))==0){
							$recordsFiltered++;
							$current_custom_query2=$GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"]."table_column_config WHERE table_id='".$data_get['connected_table']."' AND id='".$data_get['option_text']."'");
							$option_name=($data_get['option_text'] && $current_custom_query2->rowCount() ? $current_custom_query2->fetch()["current_name"]:"id");
							$option_value=($data_get['option_value'] ? $GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"]."table_column_config WHERE table_id='".$data_get['connected_table']."' AND id='".$data_get['option_value']."'")->fetch()["current_name"]:"id");
							$get_optgroup=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE id='".$data_get['optgroup_id']."'");
							$data[count($data)]=[
								"optgroup_id" => ($data_get['optgroup_id']=="-1" || $data_get['optgroup_id']=="-2" ? ($data_get['optgroup_id']==-2 ? preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $tables['description_name_en'], 1):preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $tables['description_name_fa'], 1)):($get_optgroup->rowCount() ? $get_optgroup->fetch()['option_text']:"-")),
								"option_text" => preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $tables['current_name'], 1)." -> ".$option_name,
								"option_value" => preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $tables['current_name'], 1)." -> ".$option_value,
								"is_optgroup" => $data_get['is_optgroup'],
								"id" => $data_get['id'],
								"connected_table" => $data_get['connected_table']
							];
						}
					}
				}
				$search_tools=(count($search_key)==2 ? $search_key[1]:$search_key[0]);
				$res_columns=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id IN (".($table_data_id ? $table_data_id:0).") AND (current_name LIKE '%$search_tools%' OR current_name LIKE '%$default_search_key%')");
				while($get_columns=$res_columns->fetch()){
					$res_data_gets=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE id IN (".($datas_id ? $datas_id:0).") AND connected_table IN (".($table_data_id ? $table_data_id:0).") AND (option_text='".$get_columns['id']."' OR option_value='".$get_columns['id']."')");
					while ($data_gets=$res_data_gets->fetch()) {
						if(count(array_filter($data, fn($e) => $e['id']==$data_gets['id']))==0){
							$recordsFiltered++;
							$tables=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE id='".$get_columns['table_id']."'")->fetch();
							$option_name=($data_gets['option_text'] ? $get_columns["current_name"]:"id");
							$option_value=($data_gets['option_value'] ? $get_columns["current_name"]:"id");
							$get_optgroup=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE id='".$data_gets['optgroup_id']."'");
							$data[count($data)]=[
								"optgroup_id" => ($data_gets['optgroup_id']=="-1" || $data_gets['optgroup_id']=="-2" ? ($data_gets['optgroup_id']==-2 ? preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $tables['description_name_en'], 1):preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $tables['description_name_fa'], 1)):($get_optgroup->rowCount() ? $get_optgroup->fetch()['option_text']:"-")),
								"option_text" => preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $tables['current_name'], 1)." -> ".$option_name,
								"option_value" => preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $tables['current_name'], 1)." -> ".$option_value,
								"is_optgroup" => $data_gets['is_optgroup'],
								"id" => $data_gets['id'],
								"connected_table" => $data_gets['connected_table']
							];
						}
					}
				}
				if($search_tools=="id"){
					$res_data_gets=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE id IN (".($datas_id ? $datas_id:0).") AND connected_table IN (".($table_data_id ? $table_data_id:0).") AND (option_text='0' OR option_value='0')");
					while ($data_gets=$res_data_gets->fetch()) {
						if(count(array_filter($data, fn($e) => $e['id']==$data_gets['id']))==0){
							$recordsFiltered++;
							$tables=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE id='".$data_gets['connected_table']."'")->fetch();
							$option_name=($data_gets['option_text'] ? $GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE id='".$data_gets['option_text']."'")->fetch()['current_name']:"id");
							$option_value=($data_gets['option_value'] ? $GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE id='".$data_gets['option_value']."'")->fetch()['current_name']:"id");
							$get_optgroup=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE id='".$data_gets['optgroup_id']."'");
							$data[count($data)]=[
								"optgroup_id" => ($data_gets['optgroup_id']=="-1" || $data_gets['optgroup_id']=="-2" ? ($data_gets['optgroup_id']==-2 ? preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $tables['description_name_en'], 1):preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $tables['description_name_fa'], 1)):($get_optgroup->rowCount() ? $get_optgroup->fetch()['option_text']:"-")),
								"option_text" => preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $tables['current_name'], 1)." -> ".$option_name,
								"option_value" => preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $tables['current_name'], 1)." -> ".$option_value,
								"is_optgroup" => $data_gets['is_optgroup'],
								"id" => $data_gets['id'],
								"connected_table" => $data_gets['connected_table']
							];
						}
					}
				}
				$res_optgroups=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE $my_custom_where AND is_optgroup=1 AND option_text LIKE '%$default_search_key%'");
				while($get_optgroups=$res_optgroups->fetch()){
					$res_data_gets=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE $my_custom_where AND optgroup_id='".$get_optgroups['id']."'");
					while ($data_gets=$res_data_gets->fetch()) {
						if(count(array_filter($data, fn($e) => $e['id']==$data_gets['id']))==0){
							$recordsFiltered++;
							$tables=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE id='".$get_optgroups['table_id']."'")->fetch();
							$option_name=($data_gets['option_text'] ? $data_gets['option_text']:"id");
							$option_value=($data_gets['option_value'] ? $data_gets['option_value']:"id");
							$get_optgroup=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE id='".$data_gets['optgroup_id']."'");
							$yet_data_i=count($data);
							$data[$yet_data_i]=[
								"optgroup_id" => $data_gets['optgroup_id'],
								"option_text" => $data_gets['option_text'],
								"option_value" => $data_gets['option_value'],
								"is_optgroup" => $data_gets['is_optgroup'],
								"id" => $data_gets['id'],
								"connected_table" => $data_gets['connected_table']
							];
							$sql_table_column_config_name=$GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"]."table_column_config WHERE table_id='".$data[$yet_data_i]['connected_table']."' AND id='".$data[$yet_data_i]['option_text']."'");
							$sql_table_column_config_value=$GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"]."table_column_config WHERE table_id='".$data[$yet_data_i]['connected_table']."' AND id='".$data[$yet_data_i]['option_text']."'");
							//info you can get help by searching this #duplicate-2 this is duplicated code and you have to change all duplicates to gether
							$table_name=$GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"]."table_config WHERE id='".$data[$yet_data_i]['connected_table']."'")->fetch();
							$option_name=($data[$yet_data_i]['option_text'] ? ($sql_table_column_config_name->rowCount() ? $sql_table_column_config_name->fetch()["current_name"]:"-"):"id");
							$option_value=($data[$yet_data_i]['option_value'] ? ($sql_table_column_config_value->rowCount() ? $sql_table_column_config_value->fetch()["current_name"]:"-"):"id");
							$get_optgroup=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE id='".$data[$yet_data_i]['optgroup_id']."'");
							if($data_gets['connected_table']>0){
								$data[$yet_data_i]['option_text']=preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $table_name['current_name'], 1)." -> ".$option_name;
								$data[$yet_data_i]['option_value']=preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $table_name['current_name'], 1)." -> ".$option_value;
							}
							$data[$yet_data_i]['optgroup_id']=($data[$yet_data_i]['optgroup_id']=="-1" || $data[$yet_data_i]['optgroup_id']=="-2" ? ($data[$yet_data_i]['optgroup_id']==-2 ? preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $table_name['description_name_en'], 1):preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $table_name['description_name_fa'], 1)):($get_optgroup->rowCount() ? $get_optgroup->fetch()['option_text']:"-"));
							//info you can get help by searching this #duplicate-2 this is duplicated code and you have to change all duplicates to gether
						}
					}
				}
				if(strpos("optgroup",strtolower($default_search_key))!==false || strpos("option",strtolower($default_search_key))!==false){
					if(strpos("optgroup",strtolower($default_search_key))!==false && strpos("option",strtolower($default_search_key))!==false){
						$is_optgroup_get="(is_optgroup='1' OR is_optgroup='0')";
					}elseif(strpos("optgroup",strtolower($default_search_key))!==false){
						$is_optgroup_get="is_optgroup='1'";
					}elseif(strpos("option",strtolower($default_search_key))!==false){
						$is_optgroup_get="is_optgroup='0'";
					}else{
						$is_optgroup_get=0;
					}
					$res_data_gets=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE $my_custom_where AND $is_optgroup_get");
					while ($data_gets=$res_data_gets->fetch()) {
						if(count(array_filter($data, fn($e) => $e['id']==$data_gets['id']))==0){
							$recordsFiltered++;
							$tables=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE id='".$data_gets['table_id']."'")->fetch();
							$option_name=($data_gets['option_text'] ? $data_gets['option_text']:"id");
							$option_value=($data_gets['option_value'] ? $data_gets['option_value']:"id");
							$get_optgroup=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE id='".$data_gets['optgroup_id']."'");
							$yet_data_i=count($data);
							$data[$yet_data_i]=[
								"optgroup_id" => $data_gets['optgroup_id'],
								"option_text" => $data_gets['option_text'],
								"option_value" => $data_gets['option_value'],
								"is_optgroup" => $data_gets['is_optgroup'],
								"id" => $data_gets['id'],
								"connected_table" => $data_gets['connected_table']
							];
							if($data_gets['is_optgroup']==0){
								//info you can get help by searching this #duplicate-2 this is duplicated code and you have to change all duplicates to gether
								$table_name=$GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"]."table_config WHERE id='".$data[$yet_data_i]['connected_table']."'")->fetch();
								$option_name=($data[$yet_data_i]['option_text'] ? $GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"]."table_column_config WHERE table_id='".$data[$yet_data_i]['connected_table']."' AND id='".$data[$yet_data_i]['option_text']."'")->fetch()["current_name"]:"id");
								$option_value=($data[$yet_data_i]['option_value'] ? $GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"]."table_column_config WHERE table_id='".$data[$yet_data_i]['connected_table']."' AND id='".$data[$yet_data_i]['option_value']."'")->fetch()["current_name"]:"id");
								$get_optgroup=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE id='".$data[$yet_data_i]['optgroup_id']."'");
								if($data_gets['connected_table']>0){
									$data[$yet_data_i]['option_text']=preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $table_name['current_name'], 1)." -> ".$option_name;
									$data[$yet_data_i]['option_value']=preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $table_name['current_name'], 1)." -> ".$option_value;
								}
								$data[$yet_data_i]['optgroup_id']=($data[$yet_data_i]['optgroup_id']=="-1" || $data[$yet_data_i]['optgroup_id']=="-2" ? ($data[$yet_data_i]['optgroup_id']==-2 ? preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $table_name['description_name_en'], 1):preg_replace('/' . preg_quote($GLOBALS["sub_name"], '/') . '/', "", $table_name['description_name_fa'], 1)):($get_optgroup->rowCount() ? $get_optgroup->fetch()['option_text']:"-"));
								//info you can get help by searching this #duplicate-2 this is duplicated code and you have to change all duplicates to gether
							}
						}
					}
				}
			}

			return array(
				"draw"            => isset ( $request['draw'] ) ?
					intval( $request['draw'] ) :
					0,
				"recordsTotal"    => intval( $recordsTotal ),
				"recordsFiltered" => intval( $recordsFiltered ),
				"data"            => self::data_output( $columns, $data )
			);
		}

		static function checkbox_option_table ( $request, $conn, $table, $primaryKey, $columns, $whereResult=null, $whereAll=null)
		{
			$bindings = array();
			$db = self::db( $conn );
			$localWhereResult = array();
			$localWhereAll = array();
			$whereAllSql = '';

			// Build the SQL query string from the request
			$limit = self::limit( $request, $columns );
			$order = self::order( $request, $columns );
			$where = self::filter( $request, $columns, $bindings );

			$table_id=$whereResult[0];
			$column_id=$whereResult[1];

			$whereResult = self::_flatten( $whereResult );
			$whereAll = self::_flatten( $whereAll );

			$my_custom_where="table_id IN (".($table_id ? $table_id:0).") AND column_id IN (".($column_id ? $column_id:0).")";

			$where=($where=="" ? "WHERE ".$my_custom_where:$where." AND ".$my_custom_where);
			// WHERE (`current_name` LIKE :binding_0 OR `option_value` LIKE :binding_1) AND table_id IN (23) AND column_id IN (220)

			if( $whereResult ) {
				$where = $where ?
					$where .' AND '.$whereResult :
					'WHERE '.$whereResult;
			}

			if( $whereAll ) {
				$where = $where ?
					$where .' AND '.$whereAll :
					'WHERE '.$whereAll;

				$whereAllSql = 'WHERE '.$whereAll;
			}

			$whereAllSql=($whereAllSql=="" ? "WHERE ".$my_custom_where:$whereAllSql." AND ".$my_custom_where);

			// Main query to actually get the data
			$data = self::sql_exec( $db, $bindings,
				"SELECT `".implode("`, `", self::pluck($columns, 'db'))."`
				FROM `$table`
				$where
				$order
				$limit"
			);

			// Data set length after filtering
			$resFilterLength = self::sql_exec( $db, $bindings,
				"SELECT COUNT(`{$primaryKey}`)
				FROM   `$table`
				$where"
			);
			$recordsFiltered = $resFilterLength[0][0];

			$recordsTotal = $GLOBALS["connection"]->query("SELECT * FROM " . $table." ".$whereAllSql)->rowCount();

			/*
				* Output
			*/

			return array(
				"draw"            => isset ( $request['draw'] ) ?
					intval( $request['draw'] ) :
					0,
				"recordsTotal"    => intval( $recordsTotal ),
				"recordsFiltered" => intval( $recordsFiltered ),
				"data"            => self::data_output( $columns, $data )
			);
		}

		static function complex_permission_check ( $request, $conn, $table, $primaryKey, $columns, $whereResult=null, $whereAll=null, $custom_filter=null )
		{
			$bindings = array();
			$db = self::db( $conn );
			$localWhereResult = array();
			$localWhereAll = array();
			$whereAllSql = '';

			// Build the SQL query string from the request
			$limit = self::limit( $request, $columns );
			$order = self::order( $request, $columns );
			$where = self::filter( $request, $columns, $bindings );

			$whereResult = self::_flatten( $whereResult );
			$whereAll = self::_flatten( $whereAll );

			if( $whereResult ) {
				$where = $where ?
					$where .' AND '.$whereResult :
					'WHERE '.$whereResult;
			}

			if( $whereAll ) {
				$where = $where ?
					$where .' AND '.$whereAll :
					'WHERE '.$whereAll;

				$whereAllSql = 'WHERE '.$whereAll;
			}

			if($custom_filter!=null){
				if($where==""){
					$custom_where=str_replace("WHERE","",str_replace("where","",$where))."1";
				}else{
					$custom_where=str_replace("WHERE","",str_replace("where","",$where));
				}

				$table_ids=$custom_filter[0];
				$admin_ids=$custom_filter[1];
				$permission_names=$custom_filter[2];
				$permission_values=$custom_filter[3];

				if(count($table_ids)!=0 || count($admin_ids)!=0 || count($permission_names)!=0 || count($permission_values)!=0){
					$custom_filter_text="WHERE ".$custom_where." AND ";
				}else{
					$custom_filter_text="WHERE ".$custom_where;
				}

				if(count($table_ids)!=0){
					$custom_filter_text.="table_id IN (";
					foreach ($table_ids as $key => $value) {
						if(($key)!=(count($table_ids)-1)){
							$extension=",";
						}else{
							$extension="";
						}
						$custom_filter_text.="'".$value."'".$extension;
					}
					$custom_filter_text.=")";
				}

				if(count($admin_ids)!=0){
					if(count($table_ids)!=0){
						$custom_filter_text.=" AND ";
					}
					$custom_filter_text.="admin_id IN (";
					foreach ($admin_ids as $key => $value) {
						if(($key)!=(count($admin_ids)-1)){
							$extension=",";
						}else{
							$extension="";
						}
						$custom_filter_text.="'".$value."'".$extension;
					}
					$custom_filter_text.=")";
				}

				if(count($permission_names)!=0){
					if(count($table_ids)!=0 || count($admin_ids)!=0){
						$custom_filter_text.=" AND ";
					}
					$custom_filter_text.="permission_name IN (";
					foreach ($permission_names as $key => $value) {
						if(($key)!=(count($permission_names)-1)){
							$extension=",";
						}else{
							$extension="";
						}
						$custom_filter_text.="'".$value."'".$extension;
					}
					$custom_filter_text.=")";
				}

				if(count($permission_values)!=0){
					if(count($table_ids)!=0 || count($admin_ids)!=0 || count($permission_names)!=0){
						$custom_filter_text.=" AND ";
					}
					$custom_filter_text.="permission_value IN (";
					foreach ($permission_values as $key => $value) {
						if(($key)!=(count($permission_values)-1)){
							$extension=",";
						}else{
							$extension="";
						}
						$custom_filter_text.="'".$value."'".$extension;
					}
					$custom_filter_text.=")";
				}

				$where=$custom_filter_text;

			}

			if($_SESSION['username']!=getSetting("op_admin")){
				$id_in="(";
				$res_columns_check=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_permissions WHERE admin_id!='".getSetting("op_admin")."'");
				while($columns_check=$res_columns_check->fetch()){
					if(checkPermission(1, $columns_check['table_id'], 'read', $columns_check['permission_value'], '')){
						$id_in.=$columns_check['id'].",";
					}
				}
				$id_in.="0)";

				if($where==""){
					$where=" WHERE id IN ".$id_in;
				}else{
					;
				}

				$where = ($where ? $where." AND id IN ".$id_in : " WHERE id IN ".$id_in);

				$whereAllSql = ($whereAllSql ? $whereAllSql." AND id IN ".$id_in : " WHERE id IN ".$id_in);
			}

			// Main query to actually get the data
			$data = self::sql_exec( $db, $bindings,
				"SELECT ".implode(", ", self::pluck($columns, 'db'))."
				FROM $table
				$where
				$order
				$limit"
			);

			// Data set length after filtering
			$resFilterLength = self::sql_exec( $db, $bindings,
				"SELECT COUNT(`{$primaryKey}`)
				FROM   `$table`
				$where"
			);
			$recordsFiltered = $resFilterLength[0][0];

			$recordsTotal = $GLOBALS["connection"]->query("SELECT * FROM " . $table." ".$whereAllSql)->rowCount();

			/*
			* Output
			*/
			return array(
				"draw"            => isset ( $request['draw'] ) ?
					intval( $request['draw'] ) :
					0,
				"recordsTotal"    => intval( $recordsTotal ),
				"recordsFiltered" => intval( $recordsFiltered ),
				"data"            => self::data_output( $columns, $data )
			);
		}

		static function complex_permission_column_check ( $request, $conn, $table, $primaryKey, $columns, $whereResult=null, $whereAll=null, $custom_filter=null )
		{
			$bindings = array();
			$db = self::db( $conn );
			$localWhereResult = array();
			$localWhereAll = array();
			$whereAllSql = '';

			// Build the SQL query string from the request
			$limit = self::limit( $request, $columns );
			$order = self::order( $request, $columns );
			$where = self::filter( $request, $columns, $bindings );

			$whereResult = self::_flatten( $whereResult );
			$whereAll = self::_flatten( $whereAll );

			if( $whereResult ) {
				$where = $where ?
					$where .' AND '.$whereResult :
					'WHERE '.$whereResult;
			}

			if( $whereAll ) {
				$where = $where ?
					$where .' AND '.$whereAll :
					'WHERE '.$whereAll;

				$whereAllSql = 'WHERE '.$whereAll;
			}

			if($custom_filter!=null){
				if($where==""){
					$custom_where=str_replace("WHERE","",str_replace("where","",$where))."1";
				}else{
					$custom_where=str_replace("WHERE","",str_replace("where","",$where));
				}

				$table_ids=$custom_filter[0];
				$admin_ids=$custom_filter[1];
				$permission_names=$custom_filter[2];
				$permission_values=$custom_filter[3];

				if(count($table_ids)!=0 || count($admin_ids)!=0 || count($permission_names)!=0 || count($permission_values)!=0){
					$custom_filter_text="WHERE ".$custom_where." AND ";
				}else{
					$custom_filter_text="WHERE ".$custom_where;
				}

				if(count($table_ids)!=0){
					$custom_filter_text.="column_id IN (";
					foreach ($table_ids as $key => $value) {
						if(($key)!=(count($table_ids)-1)){
							$extension=",";
						}else{
							$extension="";
						}
						$custom_filter_text.="'".$value."'".$extension;
					}
					$custom_filter_text.=")";
				}

				if(count($admin_ids)!=0){
					if(count($table_ids)!=0){
						$custom_filter_text.=" AND ";
					}
					$custom_filter_text.="admin_id IN (";
					foreach ($admin_ids as $key => $value) {
						if(($key)!=(count($admin_ids)-1)){
							$extension=",";
						}else{
							$extension="";
						}
						$custom_filter_text.="'".$value."'".$extension;
					}
					$custom_filter_text.=")";
				}

				if(count($permission_names)!=0){
					if(count($table_ids)!=0 || count($admin_ids)!=0){
						$custom_filter_text.=" AND ";
					}
					$custom_filter_text.="permission_name IN (";
					foreach ($permission_names as $key => $value) {
						if(($key)!=(count($permission_names)-1)){
							$extension=",";
						}else{
							$extension="";
						}
						$custom_filter_text.="'".$value."'".$extension;
					}
					$custom_filter_text.=")";
				}

				if(count($permission_values)!=0){
					if(count($table_ids)!=0 || count($admin_ids)!=0 || count($permission_names)!=0){
						$custom_filter_text.=" AND ";
					}
					$custom_filter_text.="permission_value IN (";
					foreach ($permission_values as $key => $value) {
						if(($key)!=(count($permission_values)-1)){
							$extension=",";
						}else{
							$extension="";
						}
						$custom_filter_text.="'".$value."'".$extension;
					}
					$custom_filter_text.=")";
				}

				$where=$custom_filter_text;

			}

			if($_SESSION['username']!=getSetting("op_admin")){
				$id_in="(";
				$res_columns_check=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."column_permissions WHERE admin_id!='".getSetting("op_admin")."'");
				while($columns_check=$res_columns_check->fetch()){
					if(checkPermission(2,$columns_check['column_id'],"read",$columns_check['permission_value'],$columns_check['table_id'])){
						$id_in.=$columns_check['id'].",";
					}
				}
				$id_in.="0)";

				if($where==""){
					$where=" WHERE id IN ".$id_in;
				}else{
					;
				}

				$where = ($where ? $where." AND id IN ".$id_in : " WHERE id IN ".$id_in);

				$whereAllSql = ($whereAllSql ? $whereAllSql." AND id IN ".$id_in : " WHERE id IN ".$id_in);
			}

			// Main query to actually get the data
			$data = self::sql_exec( $db, $bindings,
				"SELECT ".implode(", ", self::pluck($columns, 'db'))."
				FROM $table
				$where
				$order
				$limit"
			);

			// Data set length after filtering
			$resFilterLength = self::sql_exec( $db, $bindings,
				"SELECT COUNT(`{$primaryKey}`)
				FROM   `$table`
				$where"
			);
			$recordsFiltered = $resFilterLength[0][0];

			$recordsTotal = $GLOBALS["connection"]->query("SELECT * FROM " . $table." ".$whereAllSql)->rowCount();

			/*
			* Output
			*/

			return array(
				"draw"            => isset ( $request['draw'] ) ?
					intval( $request['draw'] ) :
					0,
				"recordsTotal"    => intval( $recordsTotal ),
				"recordsFiltered" => intval( $recordsFiltered ),
				"data"            => self::data_output( $columns, $data )
			);

		}

		static function complex_permission_menu_check ( $request, $conn, $table, $primaryKey, $columns, $whereResult=null, $whereAll=null, $custom_filter=null )
		{
			$bindings = array();
			$db = self::db( $conn );
			$localWhereResult = array();
			$localWhereAll = array();
			$whereAllSql = '';
			$deleted_row=0;

			// Build the SQL query string from the request
			$limit = self::limit( $request, $columns );
			$order = self::order( $request, $columns );
			$where = self::filter( $request, $columns, $bindings );

			$whereResult = self::_flatten( $whereResult );
			$whereAll = self::_flatten( $whereAll );

			if( $whereResult ) {
				$where = $where ?
					$where .' AND '.$whereResult :
					'WHERE '.$whereResult;
			}

			if( $whereAll ) {
				$where = $where ?
					$where .' AND '.$whereAll :
					'WHERE '.$whereAll;

				$whereAllSql = 'WHERE '.$whereAll;
			}

			if($custom_filter!=null){
				if($where==""){
					$custom_where=str_replace("WHERE","",str_replace("where","",$where))."1";
				}else{
					$custom_where=str_replace("WHERE","",str_replace("where","",$where));
				}

				$table_ids=$custom_filter[0];
				$admin_ids=$custom_filter[1];
				$permission_values=$custom_filter[2];

				if(count($table_ids)!=0 || count($admin_ids)!=0 || count($permission_values)!=0){
					$custom_filter_text="WHERE ".$custom_where." AND ";
				}else{
					$custom_filter_text="WHERE ".$custom_where;
				}

				if(count($table_ids)!=0){
					$custom_filter_text.="menu_id IN (";
					foreach ($table_ids as $key => $value) {
						if(($key)!=(count($table_ids)-1)){
							$extension=",";
						}else{
							$extension="";
						}
						$custom_filter_text.="'".$value."'".$extension;
					}
					$custom_filter_text.=")";
				}

				if(count($admin_ids)!=0){
					if(count($table_ids)!=0){
						$custom_filter_text.=" AND ";
					}
					$custom_filter_text.="admin_id IN (";
					foreach ($admin_ids as $key => $value) {
						if(($key)!=(count($admin_ids)-1)){
							$extension=",";
						}else{
							$extension="";
						}
						$custom_filter_text.="'".$value."'".$extension;
					}
					$custom_filter_text.=")";
				}

				if(count($permission_values)!=0){
					if(count($table_ids)!=0 || count($admin_ids)!=0){
						$custom_filter_text.=" AND ";
					}
					$custom_filter_text.="permission_value IN (";
					foreach ($permission_values as $key => $value) {
						if(($key)!=(count($permission_values)-1)){
							$extension=",";
						}else{
							$extension="";
						}
						$custom_filter_text.="'".$value."'".$extension;
					}
					$custom_filter_text.=")";
				}

				$where=$custom_filter_text;

			}

			if($_SESSION['username']!=getSetting("op_admin")){
				$table_get=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_config WHERE current_name='".$GLOBALS['sub_name']."menu_permissions'")->fetch();
				$id_in="(";
				$res_columns_check=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."menu_permissions WHERE admin_id!='".getSetting("op_admin")."'");
				while($columns_check=$res_columns_check->fetch()){
					if(checkPermission(1, $table_get['id'], "read", $columns_check['permission_value'], "")){
						$id_in.=$columns_check['id'].",";
					}
				}
				$id_in.="0)";

				if($where==""){
					$where=" WHERE id IN ".$id_in;
				}else{
					;
				}

				$where = ($where ? $where." AND id IN ".$id_in : " WHERE id IN ".$id_in);

				$whereAllSql = ($whereAllSql ? $whereAllSql." AND id IN ".$id_in : " WHERE id IN ".$id_in);
			}

			// Main query to actually get the data
			$data = self::sql_exec( $db, $bindings,
				"SELECT ".implode(", ", self::pluck($columns, 'db'))."
				FROM $table
				$where
				$order
				$limit"
			);

			// Data set length after filtering
			$resFilterLength = self::sql_exec( $db, $bindings,
				"SELECT COUNT(`{$primaryKey}`)
				FROM   `$table`
				$where"
			);
			$recordsFiltered = $resFilterLength[0][0]-$deleted_row;

			$recordsTotal = $GLOBALS["connection"]->query("SELECT * FROM " . $table." ".$whereAllSql)->rowCount()-$deleted_row;

			/*
			* Output
			*/

			return array(
				"draw"            => isset ( $request['draw'] ) ?
					intval( $request['draw'] ) :
					0,
				"recordsTotal"    => intval( $recordsTotal ),
				"recordsFiltered" => intval( $recordsFiltered ),
				"data"            => self::data_output( $columns, $data )
			);
		}

		/**
		* Connect to the database
		*
		* @param  array $sql_details SQL server connection details array, with the
		*   properties:
		*     * host - host name
		*     * db   - database name
		*     * user - user name
		*     * pass - user password
		* @return resource Database connection handle
		*/
		static function sql_connect ( $sql_details )
		{
			try {
				$db = @new PDO(
					"mysql:host={$sql_details['host']};dbname={$sql_details['db']};charset=UTF8",
					$sql_details['user'],
					$sql_details['pass'],
					array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION )
				);
			}
			catch (PDOException $e) {
				self::fatal(
					"An error occurred while connecting to the database. ".
					"The error reported by the server was: ".$e->getMessage()
				);
			}

			return $db;
		}


		/**
		* Execute an SQL query on the database
		*
		* @param  resource $db  Database handler
		* @param  array    $bindings Array of PDO binding values from bind() to be
		*   used for safely escaping strings. Note that this can be given as the
		*   SQL query string if no bindings are required.
		* @param  string   $sql SQL query to execute.
		* @return array         Result from the query (all rows)
		*/
		static function sql_exec ( $db, $bindings, $sql=null )
		{
			// Argument shifting
			if( $sql === null ) {
				$sql = $bindings;
			}

			$stmt = $db->prepare( $sql );
			//echo $sql;

			// Bind parameters
			if( is_array( $bindings ) ) {
				for ( $i=0, $ien=count($bindings) ; $i<$ien ; $i++ ) {
					$binding = $bindings[$i];
					$stmt->bindValue( $binding['key'], $binding['val'], $binding['type'] );
				}
			}

			// Execute
			try {
				$stmt->execute();
			}
			catch (PDOException $e) {
				self::fatal( "An SQL error occurred: ".$e->getMessage() );
			}

			// Return all
			return $stmt->fetchAll( PDO::FETCH_BOTH );
		}


		/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		* Internal methods
		*/

		/**
		* Throw a fatal error.
		*
		* This writes out an error message in a JSON string which DataTables will
		* see and show to the user in the browser.
		*
		* @param  string $msg Message to send to the client
		*/
		static function fatal ( $msg )
		{
			echo json_encode( array(
				"error" => $msg
			) );

			exit(0);
		}

		/**
		* Create a PDO binding key which can be used for escaping variables safely
		* when executing a query with sql_exec()
		*
		* @param  array &$a    Array of bindings
		* @param  *      $val  Value to bind
		* @param  int    $type PDO field type
		* @return string       Bound key to be used in the SQL where this parameter
		*   would be used.
		*/
		static function bind ( &$a, $val, $type )
		{
			$key = ':binding_'.count( $a );

			$a[] = array(
				'key' => $key,
				'val' => $val,
				'type' => $type
			);

			return $key;
		}


		/**
		* Pull a particular property from each assoc. array in a numeric array, 
		* returning and array of the property values from each item.
		*
		*  @param  array  $a    Array to get data from
		*  @param  string $prop Property to read
		*  @return array        Array of property values
		*/
		static function pluck ( $a, $prop )
		{
			$out = array();

			for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
				if(empty($a[$i][$prop])){
					continue;
				}
				//removing the $out array index confuses the filter method in doing proper binding,
				//adding it ensures that the array data are mapped correctly
				$out[$i] = $a[$i][$prop];
			}

			return $out;
		}


		/**
		* Return a string from an array or a string
		*
		* @param  array|string $a Array to join
		* @param  string $join Glue for the concatenation
		* @return string Joined string
		*/
		static function _flatten ( $a, $join = ' AND ' )
		{
			if( ! $a ) {
				return '';
			}
			else if( $a && is_array($a) ) {
				return implode( $join, $a );
			}
			return $a;
		}
	}
?>
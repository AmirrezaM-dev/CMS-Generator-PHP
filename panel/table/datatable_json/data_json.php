<?php
    $conn_dir="../../../connection/connect.php";
	if(session_status() == PHP_SESSION_NONE) {
        session_start(['cookie_lifetime' => 86400]);
    }
	require_once("../../config.php");
	require_once("../../setting/check_database.php");
	if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_SESSION['username']."' AND act=1");
			$user_stats=$res_user->rowCount();
			if($user_stats==1 || isset($op_admin) && $op_admin){
                $table_name = (isset($_POST['table_name']) ? $_POST['table_name']:"");
                $res_table_id=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE current_name='".$table_name."' AND created=1 AND visible=1 OR current_name='".$table_name."' AND created=1 AND '".$op_admin."'=1");
                if($res_table_id->rowCount()!=0){
                    $table_get=$res_table_id->fetch();
                    $table_id=$table_get['id'];
                    if(isset($op_admin) && $op_admin==1 || $table_get['visible']==1){
                        if(checkPermission(1,$table_id,"read",$table_get['act'],"")==1){
                            $sql_saves=[
                                ["search",$_POST['search']['value']],
                                ["order_column",$_POST['order'][0]['column']],
                                ["order_dir",$_POST['order'][0]['dir']],
                                ["start",$_POST['start']],
                                ["length",$_POST['length']]
                            ];
                            foreach ($sql_saves as $value) {
                                $sql_saves[$value[0]]=$connection->query("SELECT * FROM ".$sub_name."table_user_setting WHERE table_id='".$table_id."' AND admin_id='".$_SESSION['username']."' AND save_name='".$value[0]."' AND act=1");
                                if($sql_saves[$value[0]]->rowCount()==0){
                                    $connection->query("DELETE FROM ".$sub_name."table_user_setting WHERE table_id='".$table_id."' AND admin_id='".$_SESSION['username']."' AND save_name='".$value[0]."' AND act=1");
                                    $connection->query("INSERT INTO ".$sub_name."table_user_setting (table_id,admin_id,save_name,save_value,ordering,act) VALUES ('".$table_id."','".$_SESSION['username']."','".$value[0]."','".$value[1]."',0,1)");
                                }else{
									$connection->query("UPDATE ".$sub_name."table_user_setting SET save_value='".$value[1]."' WHERE table_id='".$table_id."' AND admin_id='".$_SESSION['username']."' AND save_name='".$value[0]."' AND act=1");
                                }
                            }
                            $primaryKey = $_POST['primaryKey'];
                            $columns = [];
                            array_push($columns, ['db' => 'ordering', 'dt' => 0]);
                            $tableId=0;
							$res_table_col=$connection->query($_POST['table_col_sql']);
							while($table_col=$res_table_col->fetch()){
								if(isset($op_admin) && $op_admin==1 || $table_col['visible']==1){
									if(checkPermission(2,$table_col['id'],"read",$table_col['act'],$table_id)==1){
										if($table_col['current_name']!="ordering"){
											$tableId++;
											if($GLOBALS['table_name']!=$GLOBALS['sub_name']."table_config"){
												array_push($columns, [
													'db' => $table_col['current_name'],
													'dt' => $tableId
												]);
											}else{
												array_push($columns, [
													'db' => $table_col['current_name'],
													'dt' => $tableId,
													'formatter' => function( $d, $row ) {
														return preg_replace('/' . preg_quote($GLOBALS['sub_name'], '/') . '/', "", $d, 1);
													}
												]);
											}
										}else{
											$tableId++;
											array_push($columns, ['db' => "id", 'dt' => $tableId]);
										}
									}
								}
							}
							if($GLOBALS['table_name']!=$GLOBALS['sub_name']."table_config"){
								array_push($columns, [
									'db' => 'id',
									'dt' => $res_table_col->rowCount()+1,
									'formatter' => function( $d, $row ) {
										$returns="";
										if(checkPermission(1,getTableByName($GLOBALS['sub_name'].preg_replace('/' . preg_quote($GLOBALS['sub_name'], '/') . '/', "", $GLOBALS['table_name'], 1))['id'],"update",getTableByName($GLOBALS['sub_name'].preg_replace('/' . preg_quote($GLOBALS['sub_name'], '/') . '/', "", $GLOBALS['table_name'], 1))['act'],"")){
											$returns.='<a href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm edit" onclick="pageLoader(\'tables?name='.preg_replace('/' . preg_quote($GLOBALS['sub_name'], '/') . '/', "", $GLOBALS['table_name'], 1).'&action=edit&id='.$d.'\');"><i class="tim-icons icon-pencil"></i></a>';
										}
										if(checkPermission(1,getTableByName($GLOBALS['sub_name'].preg_replace('/' . preg_quote($GLOBALS['sub_name'], '/') . '/', "", $GLOBALS['table_name'], 1))['id'],"create",getTableByName($GLOBALS['sub_name'].preg_replace('/' . preg_quote($GLOBALS['sub_name'], '/') . '/', "", $GLOBALS['table_name'], 1))['act'],"")){
											$returns.='<a href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm edit" onclick="copyDataOfTable('.$d.');"><i class="tim-icons icon-single-copy-04"></i></a>';
										}
										if(checkPermission(1,getTableByName($GLOBALS['sub_name'].preg_replace('/' . preg_quote($GLOBALS['sub_name'], '/') . '/', "", $GLOBALS['table_name'], 1))['id'],"delete",getTableByName($GLOBALS['sub_name'].preg_replace('/' . preg_quote($GLOBALS['sub_name'], '/') . '/', "", $GLOBALS['table_name'], 1))['act'],"")){
											$returns.='<a href="javascript:void(0)" class="btn btn-link btn-danger btn-icon btn-sm" onclick="deleteDataOfTable('.$d.');"><i class="tim-icons icon-simple-remove"></i></a>';
										}
										if(checkPermission(1,getTableByName($GLOBALS['sub_name'].preg_replace('/' . preg_quote($GLOBALS['sub_name'], '/') . '/', "", $GLOBALS['table_name'], 1))['id'],"update",getTableByName($GLOBALS['sub_name'].preg_replace('/' . preg_quote($GLOBALS['sub_name'], '/') . '/', "", $GLOBALS['table_name'], 1))['act'],"")){
											$current=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"].preg_replace('/' . preg_quote($GLOBALS['sub_name'], '/') . '/', "", $GLOBALS['table_name'], 1)." WHERE id='".$d."'")->fetch();
											$upable=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"].preg_replace('/' . preg_quote($GLOBALS['sub_name'], '/') . '/', "", $GLOBALS['table_name'], 1)." WHERE ordering<'".$current['ordering']."'")->rowCount();
											$downable=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"].preg_replace('/' . preg_quote($GLOBALS['sub_name'], '/') . '/', "", $GLOBALS['table_name'], 1)." WHERE ordering>'".$current['ordering']."'")->rowCount();
											if($downable || $upable){
												$returns.='
													<a href="javascript:void(0)" onmousedown="if(myDataTable.order()[0][0]!=0 || myDataTable.order()[0][1]!=\'asc\'){callDataTable();reOrderTable();}" class="drag_move_table btn btn-link btn-info btn-icon btn-sm"><i class="fal fa-arrows-alt-v"></i></a>
												';
											}else{
												$returns.='
													<a href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm disabled"><i class="fal fa-arrows-alt-v"></i></a>
												';
											}
											if($downable){
												$returns.='
													<a href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm" onclick="dataMover(\'moveDown\',\''.$d.'\');$(this).addClass(\'disabled\');setTimeout(function(){$(this).removeClass(\'disabled\'),500});"><i class="fas fa-level-down"></i></a>
												';
											}else{
												$returns.='
													<a href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm disabled"><i class="fas fa-level-down"></i></a>
												';
											}
											if($upable){
												$returns.='
													<a href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm" onclick="dataMover(\'moveUp\',\''.$d.'\');$(this).addClass(\'disabled\');setTimeout(function(){$(this).removeClass(\'disabled\'),500});"><i class="fas fa-level-up"></i></a>
												';
											}else{
												$returns.='
													<a href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm disabled"><i class="fas fa-level-up"></i></a>
												';
											}
										}
										return '
											<a href="javascript:void(0)" onclick="callInformation('.$d.');" class="btn btn-link btn-info btn-icon btn-sm"><i class="tim-icons icon-alert-circle-exc"></i></a>
										'.$returns;
									}
								]);
							}else{
								array_push($columns, [
									'db' => 'current_name',
									'dt' => $res_table_col->rowCount()+1,
									'formatter' => function( $d, $row ) {
										$returns="";
										if(checkPermission(1,getTableByName($GLOBALS['sub_name']."table_config")['id'],"read",getTableByName($GLOBALS['sub_name']."table_config")['act'],"")){
											$returns.='
												<a href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm edit" onclick="window.location.hash=\'tables?name='.preg_replace('/' . preg_quote($GLOBALS['sub_name'], '/') . '/', "", $d, 1).'\';return false;"><i class="far fa-server"></i></a>
											';
										}
										if(checkPermission(1,getTableByName($GLOBALS['sub_name']."table_config")['id'],"update",getTableByName($GLOBALS['sub_name']."table_config")['act'],"")){
											$returns.='
												<a href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm edit" onclick="enableTable(\''.$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE current_name='".$d."'")->fetch()["id"].'\',\'edit\');return false;"><i class="far fa-cogs"></i></a>
											';
										}
										if(checkPermission(1,getTableByName($GLOBALS['sub_name']."table_config")['id'],"delete",getTableByName($GLOBALS['sub_name']."table_config")['act'],"")){
											$returns.='
												<a href="javascript:void(0)" class="btn btn-link btn-danger btn-icon btn-sm" onclick="enableTable(\''.$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE current_name='".$d."'")->fetch()["id"].'\',\'delete\');return false;"><i class="tim-icons icon-simple-remove"></i></a>
											';
										}
										return $returns.'
											<!-- <a href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm edit" onclick="enableTable(\''.$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE current_name='".$d."'")->fetch()["id"].'\',\'download\');return false;"><i class="far fa-file-archive"></i></a> -->
										';
									}
								]);
							}
							array_push($columns, [
								'db' => 'id',
								'dt' => 'DT_RowId',
								'formatter' => function( $d, $row ) {
									return $GLOBALS['table_name'].'_'.$d;
								}
							]);

							$sql_details = array(
								'user' => getSetting("database_username"),
								'pass' => getSetting("database_password"),
								'db'   => getSetting("database_table"),
								'host' => getSetting("database_server")
							);
							require( '../../class/ssp.class.php' );
							$txt=json_encode(
								SSP::table_view( $_POST, $sql_details, $table_name, $primaryKey, $columns )
							);
							echo $txt;
                        }
                    }
                }
            }
        }
    }
?>
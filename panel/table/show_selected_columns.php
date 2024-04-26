<?php
	require_once("../class/jdf.php");
?>
<div class="modal-header justify-content-center">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
		<i class="tim-icons icon-simple-remove"></i>
	</button>
	<h6 class="title title-up text-white"><?php print_r($GLOBALS['user_language']=="en" ? "List of selected items":"لیست گزینه های منتخب"); ?></h6>
</div>
<div class="modal-body">
	<table id="rows_informations" class="table table-striped disable_custom_table">
		<thead>
			<tr>
				<?php
					$isFirst=0;
					$res_table_col=$connection->query($table_col_sql);
					while($table_col=$res_table_col->fetch()){
						if($table_col['visible']==1 || isset($op_admin) && $op_admin){
							if(checkPermission(2,$table_col['id'],"read",$table_col['act'],$table_id)==1){
								if($table_col['current_name']!="ordering"){
									$isFirst++;
				?>
					<th <?php if($isFirst==1){?>data-priority="2"<?php } ?> >
						<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="<?php print_r($table_col['description_name_en']); ?><?php if(isset($op_admin) && $op_admin){ ?> <small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_col['current_name'], 1)); ?>)</small><?php } ?>" data-text-fa="<?php print_r($table_col['description_name_fa']); ?><?php if(isset($op_admin) && $op_admin){ ?> <small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_col['current_name'], 1)); ?>)</small><?php }?>">
							<?php print_r($GLOBALS['user_language']=="en" ? $table_col['description_name_en']:$table_col['description_name_fa']); ?>
							<?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_col['current_name'], 1)); ?>)</small><?php } ?>
						</label>
					</th>
				<?php
								}else{
									$isFirst++;
				?>
					<th <?php if($isFirst==1){?>data-priority="2"<?php } ?> >
						<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="ID" data-text-fa="آیدی"><?php print_r($GLOBALS['user_language']=="en" ? "ID":"آیدی"); ?></label>
					</th>
				<?php
								}
							}
						}
					}
				?>
			</tr>
		</thead>
		<tbody>
			<?php
				while ($dataGet=$res_dataGet->fetch()) {
					$res_primary=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_get['id']."' AND primarys=1 ORDER BY id DESC");
			?>
				<tr>
					<?php
						$res_table_col=$connection->query($table_col_sql);
						while($table_col=$res_table_col->fetch()){
							if($table_col['visible']==1 || isset($op_admin) && $op_admin){
								if(checkPermission(2,$table_col['id'],"read",$table_col['act'],$table_id)==1){
									if($table_col['current_name']!="ordering"){
										?>
											<td>
												<?php
													switch ($table_col['mode']) {
														case '1':case 1:case '2':case 2://info search case 1 for see all things about this part//info search case 2 for see all things about this part
															if(strlen($dataGet[$table_col['current_name']])>=35){
																$customText="...";
															}else{
																$customText="";
															}
															$dataGet[$table_col['current_name']]="<label class='hide_data hide'>" . $dataGet[$table_col['current_name']] . "</label><label class='show_data'>".mb_substr($dataGet[$table_col['current_name']], 0,35)."&nbsp;".$customText."</label>";
														break;
														case '3':case 3: //info search case 3 for see all things about this part
															if($_SESSION["username"]!=getSetting("op_admin")){
																$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."yes_no_question_options WHERE table_id='".$table_get['id']."' AND column_id='".$table_col['id']."' AND act=1");
																$options=$res_options->fetch();
																if($res_options->rowCount()){
																	if($options['yes_value']==$dataGet[$table_col['current_name']]){
																		$dataGet[$table_col['current_name']]=$options['yes_option'];
																	}else if($options['no_value']==$dataGet[$table_col['current_name']]){
																		$dataGet[$table_col['current_name']]=$options['no_option'];
																	}else{
																		$dataGet[$table_col['current_name']]=($GLOBALS['user_language']=="en" ? "Unknow":"نا مشخص");
																	}
																}else{
																	$dataGet[$table_col['current_name']]=($dataGet[$table_col['current_name']] ? ($GLOBALS['user_language']=="en" ? "Yes":"بله"):($GLOBALS['user_language']=="en" ? "No":"نه"));
																}
																if(strlen($dataGet[$table_col['current_name']])>=35){
																	$customText="...";
																}else{
																	$customText="";
																}
																$dataGet[$table_col['current_name']]="<label class='hide_data hide'>" . $dataGet[$table_col['current_name']] . "</label><label class='show_data'>".mb_substr($dataGet[$table_col['current_name']], 0,35)."&nbsp;".$customText."</label>";
															}
														break;
														case '4':case 4://info search case 4 for see all things about this part
															$newxdata="";
															unset($newxdata);
															$res_options_setting=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options_setting WHERE table_id='".$table_get['id']."' AND column_id='".$table_col['id']."' AND act=1");
															if($res_options_setting->rowCount()){
																$options_setting=$res_options_setting->fetch();
															}else{
																$options_setting=["is_multiple"=>0,"is_forced"=>0,"min_allowed"=>0,"max_allowed"=>0];
															}
															if($options_setting['is_multiple']){
																if(strpos($dataGet[$table_col['current_name']],'_-.,.-_')){
																	$dataGet[$table_col['current_name']]=explode("_-.,.-_",$dataGet[$table_col['current_name']]);
																}else{
																	$dataGet[$table_col['current_name']]=[$dataGet[$table_col['current_name']]];
																}
																foreach ($dataGet[$table_col['current_name']] as &$xvalue) {
																	if(strpos($xvalue,'_-...-_')){
																		$xdata=explode("_-...-_",$xvalue)[1];
																	}else{
																		$xdata=$xvalue;
																	}
																	if(strpos($xdata,'_-..-_')){
																		$xdata_optVAL=explode("_-..-_",$xdata)[1];
																		$xdata_optID=explode("_-..-_",$xdata)[0];
																		$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_get['id']."' AND column_id='".$table_col['id']."' AND id='".$xdata_optID."' AND act=1");
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
																			$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_get['id']."' AND column_id='".$table_col['id']."' AND option_value='".$xdata."' AND act=1");
																			if($res_options->rowCount()){
																				$options=$res_options->fetch();
																				$xsdata=$options['option_text'];
																			}else{
																				$xsdata=$xdata;
																			}
																		}else{
																			$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_get['id']."' AND column_id='".$table_col['id']."' AND option_value='".$xdata."' AND act=1");
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
																$dataGet[$table_col['current_name']]=$newxdata;
															}else{
																if(strpos($dataGet[$table_col['current_name']],'_-.,.-_')){
																	$dataGet[$table_col['current_name']]=explode("_-.,.-_",$dataGet[$table_col['current_name']]);
																}else{
																	$dataGet[$table_col['current_name']]=[$dataGet[$table_col['current_name']]];
																}
																foreach ($dataGet[$table_col['current_name']] as &$xvalue) {
																	if(strpos($xvalue,'_-...-_')){
																		$xdata=explode("_-...-_",$xvalue)[1];
																	}else{
																		$xdata=$xvalue;
																	}
																	if(strpos($xdata,'_-..-_')){
																		$xdata_optVAL=explode("_-..-_",$xdata)[1];
																		$xdata_optID=explode("_-..-_",$xdata)[0];
																		$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_get['id']."' AND column_id='".$table_col['id']."' AND id='".$xdata_optID."' AND act=1");
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
																			$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_get['id']."' AND column_id='".$table_col['id']."' AND option_value='".$xdata."' AND act=1");
																			if($res_options->rowCount()){
																				$options=$res_options->fetch();
																				$xsdata=$options['option_text'];
																			}else{
																				$xsdata=$xdata;
																			}
																		}else{
																			$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$table_get['id']."' AND column_id='".$table_col['id']."' AND option_value='".$xdata."' AND act=1");
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
																$dataGet[$table_col['current_name']]=$newxdata;
															}
															if(strlen($dataGet[$table_col['current_name']])>=35){
																$customText="...";
															}else{
																$customText="";
															}
															$dataGet[$table_col['current_name']]="<label class='hide_data hide'>" . $dataGet[$table_col['current_name']] . "</label><label class='show_data'>".mb_substr($dataGet[$table_col['current_name']], 0,35)."&nbsp;".$customText."</label>";
														break;
														case '5':case 5://info search case 5 for see all things about this part
															$dataGet[$table_col['current_name']]='<span class="badge color-shows"'."style='background: ".$dataGet[$table_col['current_name']].";'".'>'.($GLOBALS['user_language']=="en" ? "Color":"رنگ").'</span>';
														break;
														case '6':case 6://info search case 6 for see all things about this part
															$dataGet[$table_col['current_name']]="<i class='far fa-shield text-success fa-2x'></i>";
														break;
														case '7':case 7://info search case 7 for see all things about this part
															$dataGet[$table_col['current_name']]='<a href="'.(strlen($dataGet[$table_col['current_name']]) ? $dataGet[$table_col['current_name']]:"javascript:void(0)").'" target="'.(strlen($dataGet[$table_col['current_name']]) ? "_blank":"").'" style="margin-bottom: 0px !important;"><i class="far fa-link text-info fa-2x"></i></a>';
														break;
														case '8':case 8:case '19':case 19://info search case 8 for see all things about this part//info search case 19 for see all things about this part
															$dataGet[$table_col['current_name']]='
																<ckeditor>'.$dataGet[$table_col['current_name']].'</ckeditor>
															';
														break;
														case '9':case 9://info search case 9 for see all things about this part
															$values=0;
															if(strlen($dataGet[$table_col['current_name']]) && $dataGet[$table_col['current_name']]!=0){
																foreach (explode("_-...-_",$dataGet[$table_col['current_name']]) as &$value) {
																	if($values==0){
																		$values.=",".$value;
																	}else{
																		$values=$value;
																	}
																}
															}
															$dataGet[$table_col['current_name']]="";
															$res_options=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."checkbox_options WHERE table_id='".$table_get['id']."' AND column_id='".$table_col['id']."' AND option_value IN (".$values.") AND act=1");
															while ($options=$res_options->fetch()) {
																if(strlen($dataGet[$table_col['current_name']])){
																	$dataGet[$table_col['current_name']].=",".$options["option_name"];
																}else{
																	$dataGet[$table_col['current_name']]=$options["option_name"];
																}
															}
															if(strlen($dataGet[$table_col['current_name']])>=35){
																$customText="...";
															}else{
																$customText="";
															}
															$dataGet[$table_col['current_name']]="<label class='hide_data hide'>" . $dataGet[$table_col['current_name']] . "</label><label class='show_data'>".mb_substr($dataGet[$table_col['current_name']], 0,35)."&nbsp;".$customText."</label>";
														break;
														case '10':case 10://info search case 10 for see all things about this part
															$dataGet[$table_col['current_name']]="<i class='".$dataGet[$table_col['current_name']]." text-primary fa-2x'></i>";
														break;
														case '12':case 12://info search case 12 for see all things about this part
															$dataGet[$table_col['current_name']]=(strlen($dataGet[$table_col['current_name']])>3 ? date("d/m/Y",mb_substr($dataGet[$table_col['current_name']], 0, -3)):"");
															if(strlen($dataGet[$table_col['current_name']])>=35){
																$customText="...";
															}else{
																$customText="";
															}
															$dataGet[$table_col['current_name']]="<label class='hide_data hide'>" . $dataGet[$table_col['current_name']] . "</label><label class='show_data'>".mb_substr($dataGet[$table_col['current_name']], 0,35)."&nbsp;".$customText."</label>";
														break;
														case '13':case 13://info search case 13 for see all things about this part
															$dataGet[$table_col['current_name']]=(strlen($dataGet[$table_col['current_name']])>3 ? date("d/m/Y H:i:s",mb_substr($dataGet[$table_col['current_name']], 0, -3)):"");
															if(strlen($dataGet[$table_col['current_name']])>=35){
																$customText="...";
															}else{
																$customText="";
															}
															$dataGet[$table_col['current_name']]="<label class='hide_data hide'>" . $dataGet[$table_col['current_name']] . "</label><label class='show_data'>".mb_substr($dataGet[$table_col['current_name']], 0,35)."&nbsp;".$customText."</label>";
														break;
														case '14':case 14://info search case 14 for see all things about this part
															if(strlen($dataGet[$table_col['current_name']]) && $dataGet[$table_col['current_name']]!=0){
																$dataGet[$table_col['current_name']]=(strlen($dataGet[$table_col['current_name']])>3 ? jdate("Y/m/d",mb_substr($dataGet[$table_col['current_name']], 0, -3),'','','en'):"");
															}
															if(strlen($dataGet[$table_col['current_name']])>=35){
																$customText="...";
															}else{
																$customText="";
															}
															$dataGet[$table_col['current_name']]="<label class='hide_data hide'>" . $dataGet[$table_col['current_name']] . "</label><label class='show_data'>".mb_substr($dataGet[$table_col['current_name']], 0,35)."&nbsp;".$customText."</label>";
														break;
														case '15':case 15://info search case 15 for see all things about this part
															if(strlen($dataGet[$table_col['current_name']]) && $dataGet[$table_col['current_name']]!=0){
																$dataGet[$table_col['current_name']]=(strlen($dataGet[$table_col['current_name']])>3 ? jdate("Y/m/d H:i:s",mb_substr($dataGet[$table_col['current_name']], 0, -3),'','','en'):"");
															}
															if(strlen($dataGet[$table_col['current_name']])>=35){
																$customText="...";
															}else{
																$customText="";
															}
															$dataGet[$table_col['current_name']]="<label class='hide_data hide'>" . $dataGet[$table_col['current_name']] . "</label><label class='show_data'>".mb_substr($dataGet[$table_col['current_name']], 0,35)."&nbsp;".$customText."</label>";
														break;
														case '16':case 16://info search case 16 for see all things about this part
															$dataGet[$table_col['current_name']]=(strlen($dataGet[$table_col['current_name']])>3 ? date("H:i:s",mb_substr($dataGet[$table_col['current_name']], 0, -3)):"");
															if(strlen($dataGet[$table_col['current_name']])>=35){
																$customText="...";
															}else{
																$customText="";
															}
															$dataGet[$table_col['current_name']]="<label class='hide_data hide'>" . $dataGet[$table_col['current_name']] . "</label><label class='show_data'>".mb_substr($dataGet[$table_col['current_name']], 0,35)."&nbsp;".$customText."</label>";
														break;
														case '16':case 16://info search case 16 for see all things about this part
															$dataGet[$table_col['current_name']]=(strlen($dataGet[$table_col['current_name']])>3 ? date("H:i:s",mb_substr($dataGet[$table_col['current_name']], 0, -3)):"");
															if(strlen($dataGet[$table_col['current_name']])>=35){
																$customText="...";
															}else{
																$customText="";
															}
															$dataGet[$table_col['current_name']]="<label class='hide_data hide'>" . $dataGet[$table_col['current_name']] . "</label><label class='show_data'>".mb_substr($dataGet[$table_col['current_name']], 0,35)."&nbsp;".$customText."</label>";
														break;
													}
													print_r($dataGet[$table_col['current_name']]);
												?>
											</td>
										<?php
									}else{
										?>
											<td>
												<?php print_r($dataGet['id']); ?>
											</td>
										<?php
									}
								}
							}
						}
					?>
				</tr>
			<?php
				}
			?>
		</tbody>
		<tfoot>
			<tr>
				<?php
					$isFirst=0;
					$res_table_col=$connection->query($table_col_sql);
					while($table_col=$res_table_col->fetch()){
						if($table_col['visible']==1 || isset($op_admin) && $op_admin){
							if(checkPermission(2,$table_col['id'],"read",$table_col['act'],$table_id)==1){
								if($table_col['current_name']!="ordering"){
									$isFirst++;
				?>
					<th <?php if($isFirst==1){?>data-priority="2"<?php } ?> >
						<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="<?php  print_r($table_col['description_name_en']); if(isset($op_admin) && $op_admin){ ?> <small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_col['current_name'], 1)); ?>)</small><?php }?>" data-text-fa="<?php  print_r($table_col['description_name_fa']); if(isset($op_admin) && $op_admin){ ?> <small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_col['current_name'], 1)); ?>)</small><?php }?>">
							<?php print_r($GLOBALS['user_language']=="en" ? $table_col['description_name_en']:$table_col['description_name_fa']); ?>
							<?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_col['current_name'], 1)); ?>)</small><?php }?>
						</label>
					</th>
				<?php
								}else{
									$isFirst++;
				?>
					<th <?php if($isFirst==1){?>data-priority="2"<?php } ?> >
						<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="ID" data-text-fa="آیدی"><?php print_r($GLOBALS['user_language']=="en" ? "ID":"آیدی"); ?></label>
					</th>
				<?php
								}
							}
						}
					}
				?>
			</tr>
		</tfoot>
	</table>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-info <?php print_r($GLOBALS['user_language']=="en" ? "ml-auto mr-2":"mr-auto ml-2"); ?>" data-dismiss="modal"><?php print_r($GLOBALS['user_language']=="en" ? "Close":"خروج"); ?></button>
	<button type="button" class="btn btn-info <?php print_r($GLOBALS['user_language']=="en" ? "mr-auto ml-2":"ml-auto mr-2"); ?>" data-dismiss="modal" onclick="<?php print_r($_GET["operation_value"]); ?>"><?php print_r($_GET["operation_name"]); ?></button>
</div>
<script>
	$('#rows_informations').DataTable({
		"drawCallback": function( settings ) {pscrollbarUpdate();},
		"responsive": true,
		"order": [[ 0, "asc" ]],
		"language": langObjs()
	});
	$.fn.dataTable.ext.errMode = 'none';
	$('#rows_informations').on( 'error.dt', function ( e, settings, techNote, message ) {
		// console.log( 'An error has been reported by DataTables: ', message );
		// window.location.reload();
	}).DataTable();
</script>
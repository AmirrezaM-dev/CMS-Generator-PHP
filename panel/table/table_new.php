<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-md-6 col-form-label text-center-custom <?php if($GLOBALS['user_language']=='fa'){?>text-right<?php }else{?>text-left<?php } ?>">
						<h4 class="card-title data-text" data-text-en="<?php print_r("<b>Adding to :</b> ".$table_get['description_name_en']."(". preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_name, 1).")"); ?>" data-text-fa="<?php print_r("<b>افزودن به :</b> ".$table_get['description_name_fa']."(". preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_name, 1).")"); ?>">
							<?php print_r($GLOBALS['user_language']=="en" ? "<b>Adding to :</b> ".$table_get['description_name_en']."(". preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_name, 1).")":"<b>افزودن به :</b> ".$table_get['description_name_fa']."(". preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_name, 1).")"); ?>
						</h4>
					</div>
					<div class="col-md-6 col-form-label text-center-custom <?php if($GLOBALS['user_language']=='fa'){?>text-left<?php }else{?>text-right<?php } ?>">
						<div class="row">
							<div class="col-xl-2 col-lg-3 col-md-12 col-form-label text-center">
								<label class="col-sm-12 data-text" data-text-en="font size: " data-text-fa="سایز فونت: "><?php print_r($GLOBALS['user_language']=="en" ? "font size: ":"سایز فونت: "); ?></label>
							</div>
							<div class="col-xl-10 col-lg-9 col-md-12 form-group text-center custom-overflow-selectpicker">
								<select id="editor_fontsize_changer" class="selectpicker" data-fatext="انتخاب سایز" data-entext="Select Size" data-style="btn btn-primary" <?php if($GLOBALS['user_language']=='fa'){?>title="انتخاب سایز"<?php }else{?>title="Select Size"<?php } ?> data-size="7">
									<option value="12" <?php if(getUserSetting('default-editor-fontsize')=='12'){?>selected<?php } ?> >12</option>
									<option value="14" <?php if(getUserSetting('default-editor-fontsize')=='14'){?>selected<?php } ?> >14</option>
									<option value="16" <?php if(getUserSetting('default-editor-fontsize')=='16'){?>selected<?php } ?> >16</option>
									<option value="18" <?php if(getUserSetting('default-editor-fontsize')=='18'){?>selected<?php } ?> >18</option>
									<option value="20" <?php if(getUserSetting('default-editor-fontsize')=='20'){?>selected<?php } ?> >20</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<form class="form-horizontal" onsubmit="return false;">
					<?php
						$res_table_column=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_id."' AND created=1 AND current_name!='ordering' ORDER BY column_number ASC");
						while($table_column=$res_table_column->fetch()){
							if(isset($op_admin) && $op_admin && $table_column['current_name']=="act"){
								$table_column['mode']=4;
							}
							if($table_column['mode']!=11 && $table_column['mode']!=20 && $table_column['mode']!=21){//info search case 11 for see all things about this part//info search case 20 for see all things about this part//info search case 21 for see all things about this part
								if(isset($op_admin) && $op_admin || $table_column['visible']==1){
									if(checkPermission(2,$table_column['id'],"read",$table_column['act'],$table_id)){
										if(isset($op_admin) && $op_admin || $table_column['editable']==1){
											if(checkPermission(2,$table_column['id'],"update",$table_column['act'],$table_id)){
												?>
													<div class="row custom-font-size mb-5">
														<div class="col-md-2 pt-0 col-form-label text-center-custom <?php if($GLOBALS['user_language']=='fa'){?>label-on-right<?php }else{?>label-on-left<?php } ?>">
															<label class="col-sm-12 data-text" data-text-en="<?php print_r($table_column['description_name_en']); ?> <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?><?php if($table_column['importants']){?> *<?php } ?>" data-text-fa="<?php print_r($table_column['description_name_fa']); ?> <?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?><?php if($table_column['importants']){?> *<?php } ?>">
																<?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_name_en']:$table_column['description_name_fa']); ?>
																<?php if(isset($op_admin) && $op_admin){ ?><small class='developer_mode'>(<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>)</small><?php }?><?php if($table_column['importants']){?> *<?php } ?>
															</label>
														</div>
														<div class="col-md-6 form-group text-center-custom">
															<?php //info search this_is_modes_for_data_tables for see all things about this part ?>
															<?php
																switch ($table_column['mode']) {//tables_mode_code
																	//edit_data_tables_mode_input
																	case '1':case 1://info search case 1 for see all things about this part
																		?>
																			<textarea id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" class="form-control input-group save_data_table <?php if($table_column['importants']){?>save_important<?php } ?>"></textarea>
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																		<?php
																	break;
																	case '2':case 2://info search case 2 for see all things about this part
																		?>
																			<input id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="number" class="form-control save_data_table <?php if($table_column['importants']){?>save_important<?php } ?>" value="">
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																		<?php
																	break;
																	case '3':case 3://info search case 3 for see all things about this part
																		$res_yes_no=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."yes_no_question_options WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."'");
																		if($res_yes_no->rowCount()){
																			$yes_no=$res_yes_no->fetch();
																		}else{
																			$yes_no["no_option"]="";
																			$yes_no["yes_option"]="";
																			$yes_no["yes_fa_icon"]="";
																			$yes_no["no_fa_icon"]="";
																			$yes_no["no_value"]=0;
																			$yes_no["yes_value"]=1;
																		}
																		?>
																			<?php print_r($yes_no['no_option']); ?><lable class="ml-2 mr-2"></lable><input <?php if($table_column['current_name']=="act"){ ?> checked <?php } ?> id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="checkbox" name="checkbox" class="bootstrap-switch save_data_table <?php if($table_column['importants']){?>save_important<?php } ?>"  data-on-label="<i class='<?php print_r($yes_no['yes_fa_icon']); ?>'></i>" data-off-label="<i class='<?php print_r($yes_no['no_fa_icon']); ?>'></i>"><lable class="ml-2 mr-2"></lable><?php print_r($yes_no['yes_option']); ?>
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																		<?php
																	break;
																	case '4':case 4://info search case 4 for see all things about this part
																		$res_selectbox_setting=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options_setting WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."'");
																			if($res_selectbox_setting->rowCount()){
																				$selectbox_setting=$res_selectbox_setting->fetch();
																			}else{
																				$selectbox_setting=["is_multiple"=>0,"is_forced"=>0,"min_allowed"=>0,"max_allowed"=>0];
																			}
																		$in_optgroup='';
																		?>
																			<select id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" class="selectpicker save_data_table <?php if($table_column['importants']){?>save_important<?php } ?> data-title" data-style="btn btn-primary disable-hover" <?php if($selectbox_setting['is_multiple']){?>multiple<?php } ?> title="<?php print_r($GLOBALS['user_language']=="en" ? "Select":"انتخاب کنید"); ?>" data-size="7" data-live-search="true">
																				<?php
																					$res_selectbox_optgroups=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."' AND is_optgroup='1'");
																					while ($selectbox_optgroups=$res_selectbox_optgroups->fetch()) {
																				?>
																					<optgroup label="<?php print_r($selectbox_optgroups['option_text']); ?>">
																						<?php
																							$res_selectbox_option=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE is_optgroup=0 AND table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."' AND optgroup_id='".$selectbox_optgroups['id']."'");
																							while ($selectbox_option=$res_selectbox_option->fetch()) {
																								$in_optgroup.=($in_optgroup=="" ? "":",").$selectbox_option['id'];
																								if($selectbox_option['connected_table']>0){
																									$res_connected_table=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE id='".$selectbox_option['connected_table']."'");
																									$connected_table=($res_connected_table->rowCount() ? $res_connected_table->fetch()['current_name']:0);
																									if($connected_table){
																										if($selectbox_option['option_text']){
																											$res_column_text=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$selectbox_option['connected_table']."' AND id='".$selectbox_option['option_text']."'");
																											$column_text=($res_column_text->rowCount() ? $res_column_text->fetch()['current_name']:0);
																										}else{
																											$column_text="id";
																										}
																										if($selectbox_option['option_value']){
																											$res_column_value=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$selectbox_option['connected_table']."' AND id='".$selectbox_option['option_value']."'");
																											$column_value=($res_column_value->rowCount() ? $res_column_value->fetch()['current_name']:0);
																										}else{
																											$column_value="id";
																										}
																										if(in_array($connected_table, $GLOBALS['show_tables'])){
																											$res_data=$GLOBALS["connection"]->query("SELECT * FROM ".$connected_table);
																											while ($data=$res_data->fetch()) {
																												$text=$data[$column_text];
																												$value=$data[$column_value];
																												?>
																													<option value="<?php print_r($selectbox_optgroups['option_text']."_-...-_".$selectbox_option['id']."_-..-_".$value); ?>"><?php print_r($text); ?></option>
																												<?php
																											}
																										}
																									}
																								}else{
																									$text=$selectbox_option['option_text'];
																									$value=$selectbox_option['option_value'];
																									?>
																										<option value="<?php print_r($selectbox_optgroups['option_text']."_-...-_".$selectbox_option['id']."_-.-_".$value); ?>"><?php print_r($text); ?></option>
																									<?php
																								}
																							}
																						?>
																					</optgroup>
																				<?php
																					}
																					$res_selectbox_optgroups=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."' AND optgroup_id!='' AND optgroup_id!='-' AND optgroup_id<'0' AND connected_table>0 ORDER BY ordering ASC");
																					while ($selectbox_optgroups=$res_selectbox_optgroups->fetch()) {
																						if(!strpos($in_optgroup,$selectbox_optgroups['id'])){
																							$in_optgroup.=($in_optgroup=="" ? "":",").$selectbox_optgroups['id'];
																							$res_connected_table=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE id='".$selectbox_optgroups['connected_table']."' ORDER BY ordering ASC");
																							$connected_table=($res_connected_table->rowCount() ? $res_connected_table->fetch():0);
																							$optgroup_label=($selectbox_optgroups['optgroup_id']==-1 ? $connected_table["description_name_fa"]:$connected_table["description_name_en"]);
																							if($connected_table){
																								if(in_array($connected_table['current_name'], $GLOBALS['show_tables'])){
																									?>
																										<optgroup label="<?php print_r($optgroup_label); ?>">
																											<?php
																												$res_selectbox_option=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE is_optgroup=0 AND table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."' AND optgroup_id='".$selectbox_optgroups['optgroup_id']."' AND connected_table='".$connected_table['id']."' ORDER BY ordering ASC");
																												while ($selectbox_option=$res_selectbox_option->fetch()) {
																													$in_optgroup.=($in_optgroup=="" ? "":",").$selectbox_option['id'];
																													if($selectbox_option['connected_table']>0){
																														$res_connected_table=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE id='".$selectbox_option['connected_table']."' ORDER BY ordering ASC");
																														$connected_table=($res_connected_table->rowCount() ? $res_connected_table->fetch()['current_name']:0);
																														if($connected_table){
																															if($selectbox_option['option_text']){
																																$res_column_text=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$selectbox_option['connected_table']."' AND id='".$selectbox_option['option_text']."' ORDER BY ordering ASC");
																																$column_text=($res_column_text->rowCount() ? $res_column_text->fetch()['current_name']:0);
																															}else{
																																$column_text="id";
																															}
																															if($selectbox_option['option_value']){
																																$res_column_value=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$selectbox_option['connected_table']."' AND id='".$selectbox_option['option_value']."' ORDER BY ordering ASC");
																																$column_value=($res_column_value->rowCount() ? $res_column_value->fetch()['current_name']:0);
																															}else{
																																$column_value="id";
																															}
																															$res_data=$GLOBALS["connection"]->query("SELECT * FROM ".$connected_table." ORDER BY ordering ASC");
																															while ($data=$res_data->fetch()) {
																																$text=$data[$column_text];
																																$value=$data[$column_value];
																																?>
																																	<option value="<?php print_r($optgroup_label."_-...-_".$selectbox_option['id']."_-..-_".$value); ?>"><?php print_r($text); ?></option>
																																<?php
																															}
																														}
																													}else{
																														$text=$selectbox_option['option_text'];
																														$value=$selectbox_option['option_value'];
																														?>
																															<option value="<?php print_r($optgroup_label."_-...-_".$selectbox_option['id']."_-.-_".$value); ?>"><?php print_r($text); ?></option>
																														<?php
																													}
																												}
																											?>
																										</optgroup>
																									<?php
																								}
																							}
																						}
																					}
																					$res_selectbox_option=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE is_optgroup=0 AND id NOT IN ('".$in_optgroup."') AND table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."' AND (optgroup_id='-' OR optgroup_id='')");
																					while ($selectbox_option=$res_selectbox_option->fetch()) {
																						if($selectbox_option['connected_table']>0){
																							$res_connected_table=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE id='".$selectbox_option['connected_table']."'");
																							$connected_table=($res_connected_table->rowCount() ? $res_connected_table->fetch()['current_name']:0);
																							if($connected_table){
																								if($selectbox_option['option_text']){
																									$res_column_text=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$selectbox_option['connected_table']."' AND id='".$selectbox_option['option_text']."'");
																									$column_text=($res_column_text->rowCount() ? $res_column_text->fetch()['current_name']:0);
																								}else{
																									$column_text="id";
																								}
																								if($selectbox_option['option_value']){
																									$res_column_value=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$selectbox_option['connected_table']."' AND id='".$selectbox_option['option_value']."'");
																									$column_value=($res_column_value->rowCount() ? $res_column_value->fetch()['current_name']:0);
																								}else{
																									$column_value="id";
																								}
																								if(in_array($connected_table, $GLOBALS['show_tables'])){
																									$res_data=$GLOBALS["connection"]->query("SELECT * FROM ".$connected_table);
																									while ($data=$res_data->fetch()) {
																										$text=$data[$column_text];
																										$value=$data[$column_value];
																										?>
																											<option value="<?php print_r($selectbox_option['id']."_-..-_".$value); ?>"><?php print_r($text); ?></option>
																										<?php
																									}
																								}
																							}
																						}else{
																							$text=$selectbox_option['option_text'];
																							$value=$selectbox_option['option_value'];
																							?>
																								<option value="<?php print_r($selectbox_option['id']."_-.-_".$value); ?>"><?php print_r($text); ?></option>
																							<?php
																						}
																					}
																					if(isset($op_admin) && $op_admin && $table_column['current_name']=="act"){
																						foreach ($permission_power_list as $key => $value) {
																							?>
																								<option <?php if($value[1]==1){?>selected<?php } ?> value="<?php print_r($value[1]); ?>"><?php print_r($value[1]); ?></option>
																							<?php
																						}
																					}
																				?>
																			</select>
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																		<?php
																	break;
																	case '5':case 5://info search case 5 for see all things about this part
																		?>
																			<input id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" class="form-control colorpicker save_data_table <?php if($table_column['importants']){?>save_important<?php } ?> data-title" title="<?php print_r($GLOBALS['user_language']=="en" ? "Select":"انتخاب کنید"); ?>" value=''>
																			<script>
																				$('#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>').spectrum({
																					locale: language,
																					type: "flat",
																					togglePaletteOnly: "true",
																					showInput: "true",
																					showInitial: "true",
																					showButtons: "false",
																					allowEmpty: "false"
																					<?php
																						/*
																							color: tinycolor,
																							type: sting, // text, component, color, flat
																							showInput: bool,
																							showInitial: bool,
																							allowEmpty: bool,
																							showAlpha: bool,
																							disabled: bool,
																							localStorageKey: string,
																							showPalette: bool,
																							showPaletteOnly: bool,
																							togglePaletteOnly: bool,
																							showSelectionPalette: bool,
																							clickoutFiresChange: bool,
																							containerClassName: string,
																							replacerClassName: string,
																							preferredFormat: string,
																							maxSelectionSize: int,
																							palette: [[string]],
																							selectionPalette: [string],
																							// specify locale
																							locale: string,
																							// or directly change the translations
																							cancelText: string,
																							chooseText: string,
																							togglePaletteMoreText: string,
																							togglePaletteLessText: string,
																							clearText: string,
																							noColorSelectedText: string,

																						*/
																					?>
																				});
																			</script>
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																		<?php
																	break;
																	case '6':case 6://info search case 6 for see all things about this part
																		?>
																			<input id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="password" class="form-control save_data_table <?php if($table_column['importants']){?>save_important<?php } ?>" onkeypress="$('#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_keep_old_password').prop('checked', false).change();">
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																		<?php
																	break;
																	case '7':case 7://info search case 7 for see all things about this part
																		$res_file_uploader_setting=$connection->query("SELECT * FROM ".$sub_name."file_uploader_setting WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."'");
																		if($res_file_uploader_setting->rowCount()!=0){
																			$file_uploader_setting=$res_file_uploader_setting->fetch();
																			$size_limit=$file_uploader_setting['max_size'];
																			$file_types_limit=$file_uploader_setting['allowed_type'];
																		}else{
																			$size_limit='';
																			$file_types_limit='';
																		}
																		?>
																			<div class="input-group mb-3 <?php if($table_column['importants']){?>save_important<?php } ?>">
																				<div class="custom-file">
																					<input class="custom-file-input on-file-change save_data_table <?php if($table_column['importants']){?>save_important<?php } ?>" id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_file" data-dataID="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="file" <?php if(getSetting("op_admin")!=$_SESSION["username"] && $size_limit!="" && !empty($size_limit) && intval($size_limit)==0){ ?>onchange="if(typeof $(this)[0].files[0] !== 'undefined'){if(parseInt($(this)[0].files[0].size/1024/1024)>parseInt('<?php print_r($size_limit); ?>')){$('#' + $(this).attr('data-dataID') + '_file').val('');$('#' + $(this).attr('data-dataID') + '_file').next().html(language=='en' ? 'Choose':'انتخاب');$(this).prev().removeClass('activated-file');$(this).addClass('activated-file');$('#' + $(this).attr('data-dataID') + '_-_keep_old_file').prop('checked', false).change();Swal.fire({text: (language=='en' ? 'Maximum allowed size is : <?php print_r($size_limit); ?> MB':'حد مجاز آپلود <?php print_r($size_limit); ?> مگابایت است'),icon: 'error',showConfirmButton: false,showCancelButton: false,timer: 1500});}}"<?php } ?> <?php if(getSetting("op_admin")!=$_SESSION["username"] && $file_types_limit!="" && !empty($file_types_limit)){ ?>accept="<?php print_r($file_types_limit); ?>"<?php } ?> >
																					<label class="custom-file-label data-text" data-text-en="Choose" data-text-fa="انتخاب" for="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_file"><?php print_r($GLOBALS['user_language']=="en" ? "Choose":"انتخاب"); ?></label>
																				</div>
																				<input class="form-control save_data_table <?php if($table_column['importants']){?>save_important<?php } ?> data-placeholder on-fileurl-change" data-placeholder-en="File URL" data-placeholder-fa="آدرس فایل" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "File URL":"آدرس فایل"); ?>" id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_url" data-dataID="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="text" value="">
																			</div>
																			<div id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_progress" class="progress-container progress-sm not-90 hide">
																				<div class="progress">
																					<span class="progress-value">0%</span>
																					<div class="progress-bar bg-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
																				</div>
																			</div>
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																		<?php
																	break;
																	case '8':case 8://info search case 8 for see all things about this part
																		?>
																			<ckeditor id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" class="form-control ckeditor_start input-group save_data_table <?php if($table_column['importants']){?>save_important<?php } ?>"></ckeditor>
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																			<script>
																				$(document).ready(function () {
																					CKEDITOR.replace("save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>");
																					$("#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>").addClass("ckeditor_started").removeClass("ckeditor_start");
																					if(language=="fa"){
																						$(".ckeditor_started").next().addClass("cke_rtl");
																					}else{
																						$(".ckeditor_started").next().removeClass("cke_rtl");
																					}
																				});
																			</script>
																		<?php
																	break;
																	case '9':case 9://info search case 9 for see all things about this part
																		$res_checkbox_options_setting=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."checkbox_options_setting WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."'");
																		$checkbox_options_setting=($res_checkbox_options_setting->rowCount() ? $res_checkbox_options_setting->fetch():["id"=>0,"table_id"=>$table_column['table_id'],"column_id"=>$table_column['id'],"is_multiple"=>0,"is_forced"=>1,"ordering"=>1,"act"=>1]);
																		$res_checkbox_options=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."checkbox_options WHERE table_id='".$table_column['table_id']."' AND column_id='".$table_column['id']."'");
																		if(!$checkbox_options_setting['is_multiple']){
																			?><div class="form-check pl-2 pr-2"><?php
																		}
																		while($checkbox_options=$res_checkbox_options->fetch()){
																			if($checkbox_options_setting['is_multiple']){
																				?>
																					<div class="form-check-inline form-check form-check-radio">
																						<label class="form-check-label">
																							<input data-likeid="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="radio" class="form-check-input save_data_table <?php if($table_column['importants']){?>save_important<?php } ?>" value="<?php print_r($checkbox_options['option_value']); ?>" data-falsevalue="<?php print_r($checkbox_options['option_false']); ?>" name="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>">
																							<span class="form-check-sign"></span>
																							<?php print_r($checkbox_options['option_name']); ?>
																						</label>
																					</div>
																				<?php
																			}else{
																				?>
																					<div class="form-check form-check-inline">
																						<label class="form-check-label">
																							<input data-likeid="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="checkbox" class="form-check-input save_data_table <?php if($table_column['importants']){?>save_important<?php } ?>" value="<?php print_r($checkbox_options['option_value']); ?>" data-falsevalue="<?php print_r($checkbox_options['option_false']); ?>">
																							<span class="form-check-sign"></span>
																							<?php print_r($checkbox_options['option_name']); ?>
																						</label>
																					</div>
																				<?php
																			}
																		}
																		if(!$checkbox_options_setting['is_multiple']){
																			?></div><?php
																		}
																		?>
																			<label id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" class="hide reset_data <?php if($table_column['importants']){?>save_important<?php } ?>"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																		<?php
																	break;
																	case '10':case 10://info search case 10 for see all things about this part
																		?>
                                                                            <div class="input-group">
                                                                                <div class="input-group-prepend">
                                                                                    <div class="input-group-text">
                                                                                        <i class="far fa-question-circle"></i>
                                                                                    </div>
                                                                                </div>
																				<input id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="text" class="form-control save_data_table <?php if($table_column['importants']){?>save_important<?php } ?> pl-0 pr-0 fa-on-change" value="">
                                                                            </div>
                                                                            <label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																		<?php
																	break;
																	case '12':case 12://info search case 12 for see all things about this part
																		?>
																			<input id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="text" class="form-control save_data_table <?php if($table_column['importants']){?>save_important<?php } ?> fa_datetimepicker" value="">
																			<div id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_alt"></div>
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																			<script>
																				$fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"]=$("#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_alt").persianDatepicker({
																					inline: true,
																					altField: '#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>',
																					altFormat: 'MM/DD/YYYY',
																					format: 'MM/DD/YYYY',
																					// format: 'LLLL',
																					// format: 'YYYY/MM/DD H:m:s a',
																					calendar:{
																						persian: {
																							locale: 'en'
																						},
																						gregorian: {
																							locale: language,
																						}
																					},
																					navigator: {
																						enabled: true,
																						scroll: {
																							enabled: false
																						},
																						text: {
																							btnNextText: "<",
																							btnPrevText: ">"
																						}
																					},
																					calendarType: 'gregorian',
																					formatter: function(unix){
																						return new persianDate(unix).toLocale('en').format('MM/DD/YYYY');
																					},
																					initialValue: false,
																					toolbox: {
																						submitButton: {
																							onSubmit: function (this_elem) {
																								$fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"].setDate(parseInt(this_elem.state.view.unixDate));
																							}
																						}
																					}
																				});
																				$fa_datetimepicker.push($fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"]);
																			</script>
																		<?php
																	break;
																	case '13':case 13://info search case 13 for see all things about this part
																		?>
																			<input id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="text" class="form-control save_data_table <?php if($table_column['importants']){?>save_important<?php } ?> fa_datetimepicker" value="">
																			<div id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_alt"></div>
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																			<script>
																				$fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"]=$("#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_alt").persianDatepicker({
																					inline: true,
																					altField: '#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>',
																					altFormat: 'MM/DD/YYYY H:m a',
																					format: 'MM/DD/YYYY H:m a',
																					calendar:{
																						persian: {
																							locale: 'en'
																						},
																						gregorian: {
																							locale: language,
																						}
																					},
																					navigator: {
																						enabled: true,
																						scroll: {
																							enabled: false
																						},
																						text: {
																							btnNextText: "<",
																							btnPrevText: ">"
																						}
																					},
																					calendarType: 'gregorian',
																					timePicker: {
																						enabled: true,
																						meridiem: {
																							enabled: true
																						}
																					},
																					formatter: function(unix){
																						return new persianDate(unix).toLocale('en').format('MM/DD/YYYY H:m a');
																					},
																					initialValue: false,
																					toolbox: {
																						submitButton: {
																							onSubmit: function (this_elem) {
																								$fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"].setDate(parseInt(this_elem.state.view.unixDate));
																							}
																						}
																					}
																				});
																				$fa_datetimepicker.push($fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"]);
																			</script>
																		<?php
																	break;
																	case '14':case 14://info search case 14 for see all things about this part
																		?>
																			<input id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="text" class="form-control save_data_table <?php if($table_column['importants']){?>save_important<?php } ?> fa_datetimepicker" value="">
																			<div id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_alt"></div>
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																			<script>
																				$fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"]=$("#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_alt").persianDatepicker({
																					inline: true,
																					altField: '#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>',
																					altFormat: 'YYYY/MM/DD',
																					format: 'YYYY/MM/DD',
																					calendar:{
																						persian: {
																							locale: language
																						}
																					},
																					navigator: {
																						enabled: true,
																						scroll: {
																							enabled: false
																						},
																						text: {
																							btnNextText: "<",
																							btnPrevText: ">"
																						}
																					},
																					formatter: function(unix){
																						return new persianDate(unix).toLocale('en').format('YYYY/MM/DD');
																					},
																					initialValue: false,
																					toolbox: {
																						submitButton: {
																							onSubmit: function (this_elem) {
																								$fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"].setDate(parseInt(this_elem.state.view.unixDate));
																							}
																						}
																					}
																				});
																				$fa_datetimepicker.push($fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"]);
																			</script>
																		<?php
																	break;
																	case '15':case 15://info search case 15 for see all things about this part
																		?>
																			<input id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="text" class="form-control save_data_table <?php if($table_column['importants']){?>save_important<?php } ?> fa_datetimepicker" value="">
																			<div id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_alt"></div>
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																			<script>
																				$fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"]=$("#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_alt").persianDatepicker({
																					inline: true,
																					altField: '#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>',
																					altFormat: 'YYYY/MM/DD H:m a',
																					format: 'YYYY/MM/DD H:m a',
																					calendar:{
																						persian: {
																							locale: language
																						}
																					},
																					timePicker: {
																						enabled: true,
																						meridiem: {
																							enabled: true
																						}
																					},
																					navigator: {
																						enabled: true,
																						scroll: {
																							enabled: false
																						},
																						text: {
																							btnNextText: "<",
																							btnPrevText: ">"
																						}
																					},
																					formatter: function(unix){
																						return new persianDate(unix).toLocale('en').format('YYYY/MM/DD H:m a');
																					},
																					initialValue: false,
																					toolbox: {
																						submitButton: {
																							onSubmit: function (this_elem) {
																								$fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"].setDate(parseInt(this_elem.state.view.unixDate));
																							}
																						}
																					}
																				});
																				$fa_datetimepicker.push($fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"]);
																			</script>
																		<?php
																	break;
																	case '16':case 16://info search case 16 for see all things about this part
																		?>
																			<input id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="text" class="form-control save_data_table <?php if($table_column['importants']){?>save_important<?php } ?> fa_datetimepicker" value="">
																			<div id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_alt"></div>
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																			<script>
																				$fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"]=$("#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_alt").persianDatepicker({
																					inline: true,
																					altField: '#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>',
																					altFormat: 'H:m a',
																					format: 'H:m a',
																					onlyTimePicker: true,
																					calendar:{
																						persian: {
																							locale: 'en'
																						}
																					},
																					navigator: {
																						enabled: true,
																						scroll: {
																							enabled: false
																						},
																						text: {
																							btnNextText: "<",
																							btnPrevText: ">"
																						}
																					},
																					initialValue: false,
																					formatter: function(unix){
																						return new persianDate(unix).toLocale('en').format('H:m a');
																					},
																					toolbox: {
																						submitButton: {
																							onSubmit: function (this_elem) {
																								$fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"].setDate(parseInt(this_elem.state.view.unixDate));
																							}
																						}
																					}
																				});
																				$fa_datetimepicker.push($fa_datetimepicker["#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>"]);
																			</script>
																		<?php
																	break;
																	case '17':case 17://info search case 17 for see all things about this part
																		?>
																			<div id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" class="slider slider-maker"></div>
																			<br>
																			<!-- <div id="sliderDouble" class="slider slider-primary mb-3"></div> -->
																			<label class="hide reset_data">0</label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																			<script>
																				noUiSlider.create(document.getElementById('save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>'), {
																					start: 0,
																					connect: true,
																					direction: (language=="en" ? "ltr":"rtl"),
																					range: {
																						min: 0,
																						max: 100
																					}
																				});

																				<?php /*
																				// var slider2 = document.getElementById('sliderDouble');

																				// noUiSlider.create(slider2, {
																				//	 start: [ 0, 100 ],
																				//	 connect: true,
																				//	 direction: (language=="en" ? "ltr":"rtl"),
																				//	 range: {
																				//		 min:  0,
																				//		 max:  100
																				//	 }
																				// });
																				*/ ?>
																			</script>
																		<?php
																	break;
																	case '18':case 18://info search case 18 for see all things about this part
																		?>
																			<input id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="text" class="form-control save_data_table <?php if($table_column['importants']){?>save_important<?php } ?> tagsinput" value="" data-role="tagsinput" data-color="warning">
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																			<script>
																				$("#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>").tagsinput();
																			</script>
																		<?php
																	break;
																	case '19':case 19://info search case 19 for see all things about this part
																		?>
																			<ckeditor id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>" type="number" class="form-control save_data_table <?php if($table_column['importants']){?>save_important<?php } ?>"></ckeditor>
																			<label class="hide reset_data"></label>
																			<span class="form-text data-text text-center-custom m-i-i-column" data-text-en="<?php print_r($table_column['description_info_en']); ?>" data-text-fa="<?php print_r($table_column['description_info_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $table_column['description_info_en']:$table_column['description_info_fa']); ?></span>
																			<script>
																				$(document).ready(function () {
																					CKEDITOR.replace("save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>",{
																						toolbar : [],
																					});
																					$("#save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>").addClass("ckeditor_started").removeClass("ckeditor_start");
																					if(language=="fa"){
																						$(".ckeditor_started").next().addClass("cke_rtl");
																					}else{
																						$(".ckeditor_started").next().removeClass("cke_rtl");
																					}
																				});
																			</script>
																		<?php
																	break;
																}
															?>
														</div>
														<div class="col-md-2 pt-0 col-form-label text-center-custom <?php if($GLOBALS['user_language']=='fa'){?>label-on-left<?php }else{?>label-on-right<?php } ?>">
															<?php
																switch ($table_column['mode']) {//tables_mode_code
																	//edit_data_tables_mode_input_more
																	case '6':case 6://info search case 6 for see all things about this part
																		?>
																			<label class="col-sm-12 data-text" data-text-en="More information or operation" data-text-fa="اطلاعات یا امکانات اضافه"><?php print_r($GLOBALS['user_language']=="en" ? "More information or operation":"اطلاعات یا امکانات اضافه"); ?></label>
																			<label class="col-sm-12 hide">
																				<div class="form-check">
																					<label class="form-check-label data-text" data-text-en='<input checked id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_keep_old_password" class="form-check-input" type="checkbox"><span class="form-check-sign"></span> Keep the old password ?' data-text-fa='<input checked id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_keep_old_password" class="form-check-input" type="checkbox"><span class="form-check-sign"></span> کلمه عبور قبلی نگهداری شود ؟'>
																						<input checked id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_keep_old_password" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>
																						<?php print_r($GLOBALS['user_language']=="en" ? "Keep the old password ?":"کلمه عبور قبلی نگهداری شود ؟"); ?>
																					</label>
																				</div>
																			</label>
																		<?php
																	break;
																	case '7':case 7://info search case 7 for see all things about this part
																		?>
																			<label class="col-sm-12 data-text" data-text-en="More information or operation" data-text-fa="اطلاعات یا امکانات اضافه"><?php print_r($GLOBALS['user_language']=="en" ? "More information or operation":"اطلاعات یا امکانات اضافه"); ?></label>
																			<label class="col-sm-12 hide">
																				<div class="form-check">
																					<label class="form-check-label data-text" data-text-en='<input checked id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_keep_old_file" class="form-check-input" type="checkbox"><span class="form-check-sign"></span> Keep the old file ?' data-text-fa='<input checked id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_keep_old_file" class="form-check-input" type="checkbox"><span class="form-check-sign"></span> فایل قبلی نگهداری شود ؟'>
																						<input checked id="save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($table_column['mode']); ?>_-_keep_old_file" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>
																						<?php print_r($GLOBALS['user_language']=="en" ? "Keep the old file ?":"فایل قبلی نگهداری شود ؟"); ?>
																					</label>
																				</div>
																			</label>
																		<?php
																	break;
																	case '10':case 10://info search case 10 for see all things about this part
																		?>
																			<label class="col-sm-12">
																				<a href="https://fontawesome.com/icons?d=gallery" target="_blank" class="text-info data-text" data-text-en='Font Awesome icon list' data-text-fa='لیست آیکون های فونت آوسم'><?php print_r($GLOBALS['user_language']=="en" ? "Font Awesome icon list":"لیست آیکون های فونت آوسم"); ?></a>
																			</label>
																		<?php
																	break;
																	default:
																		?>
																			<label class="col-sm-12 data-text" data-text-en="More information or operation" data-text-fa="اطلاعات یا امکانات اضافه"><?php print_r($GLOBALS['user_language']=="en" ? "More information or operation":"اطلاعات یا امکانات اضافه"); ?></label>
																		<?php
																	break;
																}
															?>
														</div>
														<div class="col-md-2 pt-0 col-form-label text-center-custom <?php if($GLOBALS['user_language']=='fa'){?>label-on-left<?php }else{?>label-on-right<?php } ?>">
															<button onclick="NEWcleanThis('save_-_<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', '', $table_column['current_name'], 1)); ?>_-_<?php print_r($table_column['id']); ?>_-_<?php print_r($_SESSION["username"]==getSetting("op_admin") && $table_column['current_name']=="act" ? 4:$table_column['mode']); ?>');" class="btn btn-warning custom-edit-buttons data-original-title clear_single_column" rel="tooltip" data-original-title-en="Clean this field !" data-original-title-fa="پاکسازی این فیلد !" data-original-title="<?php print_r($GLOBALS['user_language']=="en" ? "Clean this field !":"پاکسازی این فیلد !"); ?>" data-placement="top">
																<i style="font-size: 24px !important;" class="fas fa-eraser"></i>
															</button>
														</div>
													</div>
												<?php
											}
										}
									}
								}
							}
						}
					?>
				</form>
			</div>
			<div class="card-footer">
				<div class="form-check <?php if($GLOBALS['user_language']=='fa'){?>pull-right<?php }else{?>pull-left<?php } ?>">
					<button onclick="NEWbackToTable();" class="btn btn-primary data-original-title data-text" rel="tooltip" data-original-title-en="Going back to table of this form ." data-original-title-fa="بازگشت به جدول مربوط به این فرم ." data-original-title="<?php print_r($GLOBALS['user_language']=="en" ? "Going back to table of this form .":"بازگشت به جدول مربوط به این فرم ."); ?>" data-placement="top" data-text-en="Back" data-text-fa="بازگشت"><?php print_r($GLOBALS['user_language']=="en" ? "Back":"بازگشت"); ?></button>
				</div>
				<div class="form-check <?php if($GLOBALS['user_language']=='fa'){?>pull-left<?php }else{?>pull-right<?php } ?>">
					<button onclick="NEWclearInputs();" class="btn btn-warning data-text data-original-title" rel="tooltip" data-original-title-en="You can clear everything by pressing this button !" data-original-title-fa="شما میتوانید با فشردن این دکمه شما میتوانید تمام موارد را پاکسازی کنید !" data-original-title="<?php print_r($GLOBALS['user_language']=="en" ? "You can clear everything by pressing this button !":"شما میتوانید با فشردن این دکمه شما میتوانید تمام موارد را پاکسازی کنید !"); ?>" data-placement="top" data-text-en="Clear All" data-text-fa="پاکسازی همه"><?php print_r($GLOBALS['user_language']=="en" ? "Clear All":"پاکسازی همه"); ?></button>

					<button onclick="NEWresetInputs();" class="btn btn-warning data-text data-original-title" rel="tooltip" data-original-title-en="You can reset all of your changes to the first by pressing this button !" data-original-title-fa="با فشردن این دکمه شما میتوانید تمام تغییرات اعمال شده را به حالت اصلی باز گردانید !" data-original-title="<?php print_r($GLOBALS['user_language']=="en" ? "You can reset all of your changes to the first by pressing this button !":"با فشردن این دکمه شما میتوانید تمام تغییرات اعمال شده را به حالت اصلی باز گردانید !"); ?>" data-placement="top" data-text-en="Recovery" data-text-fa="بازیابی"><?php print_r($GLOBALS['user_language']=="en" ? "Recovery":"بازیابی"); ?></button>

					<button onclick="NEWdeleteThis();" class="btn btn-danger data-text data-original-title" rel="tooltip" data-original-title-en="You can delete this data from selected table by pressing this button !" data-original-title-fa="با فشردن این دکمه شما میتوانید این دیتا را از جدول مربوطه حذف کنید !" data-original-title="<?php print_r($GLOBALS['user_language']=="en" ? "You can delete this data from selected table by pressing this button !":"با فشردن این دکمه شما میتوانید این دیتا را از جدول مربوطه حذف کنید !"); ?>" data-placement="top" data-text-en="Delete" data-text-fa="حذف"><?php print_r($GLOBALS['user_language']=="en" ? "Delete":"حذف"); ?></button>

					<button onclick="NEWsaveInputs('',this);" class="btn btn-success data-text data-original-title save_all_this" rel="tooltip" data-original-title-en="You can save your changes by pressing this button ! (You can return it to first by clicking recovery until you are here so don't leave if you saved this data and you are not sure !)" data-original-title-fa="با فشردن این دکمه شما میتوانید تغییرات اعمال شده را ذخیره کنید (شما میتوانید با فشردن دکمه بازیابی تا زمانی که صفحه را ترک نکرده اید تمام تغییرات اعمال شده و ذخیره شده را به حالت اصلی بازگردانید پس اگر تغییرات را ذخیره کردید و مطمئن نیستید صفحه را ترک نکنید !)" data-original-title="<?php print_r($GLOBALS['user_language']=="en" ? "You can save your changes by pressing this button ! (You can return it to first by clicking recovery until you are here so don't leave if you saved this data and you are not sure !)":"با فشردن این دکمه شما میتوانید تغییرات اعمال شده را ذخیره کنید (شما میتوانید با فشردن دکمه بازیابی تا زمانی که صفحه را ترک نکرده اید تمام تغییرات اعمال شده و ذخیره شده را به حالت اصلی بازگردانید پس اگر تغییرات را ذخیره کردید و مطمئن نیستید صفحه را ترک نکنید !)"); ?>" data-placement="top" data-text-en="Save" data-text-fa="ذخیره"><?php print_r($GLOBALS['user_language']=="en" ? "Save":"ذخیره"); ?></button>

					<button onclick="NEWsaveInputs('back',this);" class="btn btn-success data-text data-original-title save_all_this_back" rel="tooltip" data-original-title-en="!! Careful !! You can save your changes as well like save button by pressing this button but you can't use recovery button for return changes back to first and its gonna return you back to selected table after pressing this button !" data-original-title-fa="!! مراقب باشید !! با فشردن این دکمه شما میتوانید تغییرات اعمال شده را ذخیره کنید همانند دکمه ذخیره با این تفاوت که دیگر قادر نخواهید بود از دکمه بازیابی استفاده کنید و پس از فشردن دکمه ذخیره به جدول مربوطه منتقل خواهید شد !" data-original-title="<?php print_r($GLOBALS['user_language']=="en" ? "!! Careful !! You can save your changes as well like save button by pressing this button but you can't use recovery button for return changes back to first and its gonna return you back to selected table after pressing this button !":"!! مراقب باشید !! با فشردن این دکمه شما میتوانید تغییرات اعمال شده را ذخیره کنید همانند دکمه ذخیره با این تفاوت که دیگر قادر نخواهید بود از دکمه بازیابی استفاده کنید و پس از فشردن دکمه ذخیره به جدول مربوطه منتقل خواهید شد !"); ?>" data-placement="top" data-text-en="Save & Back" data-text-fa="ذخیره و بازگشت"><?php print_r($GLOBALS['user_language']=="en" ? "Save & Back":"ذخیره و بازگشت"); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<?php require_once("table/js/table_new.php"); ?>
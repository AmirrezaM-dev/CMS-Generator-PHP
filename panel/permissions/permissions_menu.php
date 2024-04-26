<?php
	$table_name = $sub_name."menu_permissions";
	$res_table_id = $connection->query("SELECT * FROM " . $sub_name . "table_config WHERE current_name='" . $table_name . "' AND created=1 AND visible=1 OR current_name='" . $table_name . "' AND created=1 AND '" . $op_admin . "'=1");
	if($res_table_id->rowCount() != 0){
		$table_get = $res_table_id->fetch();
		$table_id = $table_get['id'];
		if(isset($op_admin) && $op_admin == 1 || $table_get['visible'] == 1){
			if(checkPermission(1, $table_id, "read", $table_get['act'], "")){
?>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-md-12 mb-4">
							<h4 class="card-title data-text" data-text-en="<?php print_r($languages_data["en"]['permissions'][$last_name['permissions']['menu_permission'].'_permission']['title']); ?>" data-text-fa="<?php print_r($languages_data["fa"]['permissions'][$last_name['permissions']['menu_permission'].'_permission']['title']); ?>"><?php print_r($languages_data[$GLOBALS['user_language']]['permissions'][$last_name['permissions']['menu_permission'].'_permission']['title']); ?></h4>
							<div class="row mb-5">
								<?php
									$css_classes="col-lg-4 col-md-6 col-sm-12";
								?>
								<div class="<?php print_r($css_classes); ?>">
									<div class="form-group">
										<select id="perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?>" class="selectpicker data-title select-all-opt" data-title-en="Users, Ranks" data-title-fa="کاربران ، مقام ها" data-style="btn btn-primary" multiple title="<?php if($GLOBALS['user_language']=='en'){?>Users, Ranks<?php }else{?>کاربران ، مقام ها<?php } ?>" data-size="7" data-live-search="true">
											<option class="data-text" data-text-en="Select All" data-text-fa="انتخاب همه" value="-1"><?php if($GLOBALS['user_language']=='en'){?>Select All<?php }else{?>انتخاب همه<?php } ?></option>
											<optgroup label="<?php if($GLOBALS['user_language']=='en'){?>Users<?php }else{?>کاربران<?php } ?>" class="data-label" data-label-en="Users" data-label-fa="کاربران">
												<option data-tokens="Users کاربران" class="data-text" data-text-en="Select All Users" data-text-fa="انتخاب همه کاربران" value="-2"><?php if($GLOBALS['user_language']=='en'){?>Select All Users<?php }else{?>انتخاب همه کاربران<?php } ?></option>
												<?php
													$res_users=$connection->query("SELECT * FROM ".$sub_name."admins");
													while($users=$res_users->fetch()){
														if(getSetting("op_admin")!=$users['username']){
															if(checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"")==1 && checkPermission(2,getColumnByName($sub_name."admins","username")['id'],"read",getColumnByName($sub_name."admins","username")['act'],getTableByName($sub_name."admins")['id'])==1){
												?>
													<option data-tokens="Users کاربران" value="<?php print_r($users['username']); ?>" class="users-option data-text" data-text-fa="<?php print_r($users['username']); ?>" data-text-en="<?php print_r($users['username']); ?>">
														<?php print_r($users['username']); ?>
													</option>
												<?php
															}
														}
													}
												?>
											</optgroup>
											<optgroup label="<?php if($GLOBALS['user_language']=='en'){?>Ranks<?php }else{?>مقام ها<?php } ?>" class="data-label" data-label-en="Ranks" data-label-fa="مقام ها">
												<option data-tokens="Ranks مقام ها" class="data-text" data-text-en="Select All Ranks" data-text-fa="انتخاب همه مقام ها" value="-3"><?php if($GLOBALS['user_language']=='en'){?>Select All Ranks<?php }else{?>انتخاب همه مقام ها<?php } ?></option>
												<?php
													$res_ranks=$connection->query("SELECT * FROM ".$sub_name."rank");
													while($ranks=$res_ranks->fetch()){
														if(checkPermission(1,getTableByName($sub_name."rank")['id'],"read",getTableByName($sub_name."rank")['act'],"")==1 && checkPermission(2,getColumnByName($sub_name."rank","rank_name_fa")['id'],"read",getColumnByName($sub_name."rank","rank_name_fa")['act'],getTableByName($sub_name."rank")['id'])==1 && checkPermission(2,getColumnByName($sub_name."rank","rank_name_en")['id'],"read",getColumnByName($sub_name."rank","rank_name_en")['act'],getTableByName($sub_name."rank")['id'])==1){
												?>
													<option data-tokens="Ranks مقام ها" class="ranks-option data-text" value="<?php print_r($ranks['id']); ?>" data-text-fa="<?php print_r($ranks['rank_name_fa']); ?>" data-text-en="<?php print_r($ranks['rank_name_en']); ?>">
														<?php if($GLOBALS['user_language']=='en'){print_r($ranks['rank_name_en']);}else{print_r($ranks['rank_name_fa']);} ?>
													</option>
												<?php
														}
													}
												?>
											</optgroup>
										</select>
										<span class="form-text data-text" data-text-en="Select a user or rank which you want change a permission of" data-text-fa="انتخاب کاربر برای ایجاد تغیرات در دسترسی آن"><?php print_r($GLOBALS['user_language']=="en" ? "Select a user or rank which you want change a permission of":"انتخاب کاربر برای ایجاد تغیرات در دسترسی آن"); ?></span>
									</div>
								</div>
								<div class="<?php print_r($css_classes); ?>">
									<div class="form-group">
										<select id="perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?>" class="selectpicker data-title select-all-opt select-all-table_id" data-title-en="Menus" data-title-fa="منو ها" data-style="btn btn-primary" multiple title="<?php if($GLOBALS['user_language']=='en'){?>Menus<?php }else{?>منو ها<?php } ?>" data-size="7" data-live-search="true">
											<option class="data-text" data-text-en="Select All" data-text-fa="انتخاب همه" value="-1"><?php if($GLOBALS['user_language']=='en'){?>Select All<?php }else{?>انتخاب همه<?php } ?></option>
											<?php
												$res_tables=$connection->query("SELECT * FROM ".$sub_name."menu");
												while($tables=$res_tables->fetch()){
													if(
														checkPermission(1, getTableByName($sub_name."menu")['id'], "read", getTableByName($sub_name."menu")['act'], "") &&
														checkPermission(2,getColumnByName($sub_name."menu","menu_name_en")['id'],"read",$tables['act'],getTableByName($sub_name."menu")['id']) &&
														checkPermission(2,getColumnByName($sub_name."menu","menu_name_fa")['id'],"read",$tables['act'],getTableByName($sub_name."menu")['id'])
													){
											?>
													<option class="data-text" value="<?php print_r($tables['id']); ?>" data-text-en="<?php print_r($tables['menu_name_en']); ?>" data-text-fa="<?php print_r($tables['menu_name_fa']); ?>">
														<?php if($GLOBALS['user_language']=='en'){print_r($tables['menu_name_en']);}else{print_r($tables['menu_name_fa']);} ?>
													</option>
											<?php
													}
												}
											?>
										</select>
										<span class="form-text data-text" data-text-en="Select a menu which you want change a permission of" data-text-fa="انتخاب منو برای ایجاد تغیرات در دسترسی آن"><?php print_r($GLOBALS['user_language']=="en" ? "Select a menu which you want change a permission of":"انتخاب منو برای ایجاد تغیرات در دسترسی آن"); ?></span>
									</div>
								</div>
								<div class="<?php print_r($css_classes); ?>">
									<div class="form-group">
										<select id="perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?>" class="selectpicker data-title select-all-opt select-<?php print_r($last_name['permissions']['menu_permission']); ?>-first" data-title-en="Permissions Power" data-title-fa="سطح دسترسی ها" data-style="btn btn-primary" multiple title="<?php if($GLOBALS['user_language']=='en'){?>Permissions Power<?php }else{?>سطح دسترسی ها<?php } ?>" data-size="7" data-live-search="true">
											<option class="data-text" data-text-en="<?php print_r($data_text['en']['a']); ?>" data-text-fa="<?php print_r($data_text['fa']['a']); ?>" value="-1"><?php if($GLOBALS['user_language']=='en'){?><?php print_r($data_text['en']['a']); ?><?php }else{?><?php print_r($data_text['fa']['a']); ?><?php } ?></option>
											<?php
												foreach ($permission_power_list as $key => $value) {
													if($value[1]!="-1"){
														if(checkPermission(2,getColumnByName($sub_name."menu_permissions","act")['id'],"read",$value[1],getTableByName($sub_name."menu_permissions")['id'])){
											?>
												<option value="<?php print_r($value[1]); ?>"><?php print_r($value[1]); ?></option>
											<?php
														}
													}
												}
											?>
										</select>
										<span class="form-text data-text" data-text-en="Select a permission Power which you want to change" data-text-fa="انتخاب سطح دسترسی برای ایجاد تغیرات"><?php print_r($GLOBALS['user_language']=="en" ? "Select a permission Power which you want to change":"انتخاب سطح دسترسی برای ایجاد تغیرات"); ?></span>
									</div>
								</div>
								<div class="col-12 text-center mt-2">
									<button id="addMenuPermission" class="btn btn-success animation-on-hover data-text" onclick="permissionOperator_<?php print_r($last_name['permissions']['menu_permission']); ?>(this,'addMenuPermission')" data-text-en="Add" data-text-fa="افزودن" type="button"><?php if($GLOBALS['user_language']=='en'){?>Add<?php }else{?>افزودن<?php } ?></button>
									<button id="addAndEnableMenuPermission" class="btn btn-success animation-on-hover data-text" onclick="permissionOperator_<?php print_r($last_name['permissions']['menu_permission']); ?>(this,'addAndEnableMenuPermission')" data-text-en="Add And Enable" data-text-fa="افزودن و فعال سازی" type="button"><?php if($GLOBALS['user_language']=='en'){?>Add And Enable<?php }else{?>افزودن و فعال سازی<?php } ?></button>
									<button id="enableMenuPermission" class="btn btn-success animation-on-hover data-text" onclick="permissionOperator_<?php print_r($last_name['permissions']['menu_permission']); ?>(this,'enableMenuPermission')" data-text-en="Enable" data-text-fa="فعال سازی" type="button"><?php if($GLOBALS['user_language']=='en'){?>Enable<?php }else{?>فعال سازی<?php } ?></button>
									<button id="disableMenuPermission" class="btn btn-warning animation-on-hover data-text" onclick="permissionOperator_<?php print_r($last_name['permissions']['menu_permission']); ?>(this,'disableMenuPermission')" data-text-en="Disable" data-text-fa="غیرفعال سازی" type="button"><?php if($GLOBALS['user_language']=='en'){?>Disable<?php }else{?>غیرفعال سازی<?php } ?></button>
									<button id="deletePermission" class="btn btn-danger animation-on-hover data-text" onclick="permissionOperator_<?php print_r($last_name['permissions']['menu_permission']); ?>(this,'deleteMenuPermission')" data-text-en="Remove" data-text-fa="حذف" type="button"><?php if($GLOBALS['user_language']=='en'){?>Remove<?php }else{?>حذف<?php } ?></button>
									<button class="btn btn-info animation-on-hover data-text" onclick="$('.filterig-<?php print_r($last_name['permissions']['menu_permission']); ?>').toggleClass('hide');$('.start-filtering-<?php print_r($last_name['permissions']['menu_permission']); ?>').selectpicker('val', []).selectpicker('refresh');$('#<?php print_r($last_name['permissions']['menu_permission']); ?>_permission').DataTable().ajax.reload( null, false );" data-text-en="Filtering" data-text-fa="فیلتر کردن" type="button"><?php if($GLOBALS['user_language']=='en'){?>Filtering<?php }else{?>فیلتر کردن<?php } ?></button>
									<button class="btn animation-on-hover data-text" onclick="action_permission_<?php print_r($GLOBALS['last_name']['permissions']['menu_permission']); ?>('selectAll');" data-text-en="Select All" data-text-fa="انتخاب همه" type="button"><?php if($GLOBALS['user_language']=='en'){?>Select All<?php }else{?>انتخاب همه<?php } ?></button>
									<button class="btn animation-on-hover data-text" onclick="action_permission_<?php print_r($GLOBALS['last_name']['permissions']['menu_permission']); ?>('deselectAll');" data-text-en="Deselect All" data-text-fa="لغو انتخاب همه" type="button"><?php if($GLOBALS['user_language']=='en'){?>Deselect All<?php }else{?>لغو انتخاب همه<?php } ?></button>
								</div>
							</div>
							<div class="row mt-5 mb-1 filterig-<?php print_r($last_name['permissions']['menu_permission']); ?> hide">
								<div class="<?php print_r($css_classes); ?>">
									<div class="form-group">
										<select id="perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?>-filter" class="selectpicker data-title select-all-opt start-filtering-<?php print_r($last_name['permissions']['menu_permission']); ?> select-all-table_id" data-title-en="Menus" data-title-fa="منو ها" data-style="btn btn-primary" multiple title="<?php if($GLOBALS['user_language']=='en'){?>Menus<?php }else{?>منو ها<?php } ?>" data-size="7" data-live-search="true">
											<?php
												$res_tables=$connection->query("SELECT * FROM ".$sub_name."menu");
												while($tables=$res_tables->fetch()){
													if(checkPermission(1, getTableByName($sub_name."menu")['id'], "read", getTableByName($sub_name."menu")['act'], "")){
											?>
													<option class="data-text" value="<?php print_r($tables['id']); ?>" data-text-en="<?php print_r($tables['menu_name_en']); ?>" data-text-fa="<?php print_r($tables['menu_name_fa']); ?>">
														<?php if($GLOBALS['user_language']=='en'){print_r($tables['menu_name_en']);}else{print_r($tables['menu_name_fa']);} ?>
													</option>
											<?php
													}
												}
											?>
										</select>
										<span class="form-text data-text" data-text-en="Filter menus name" data-text-fa="فیلتر اسم منو ها"><?php print_r($GLOBALS['user_language']=="en" ? "Filter menus name":"فیلتر اسم منو ها"); ?></span>
									</div>
								</div>
								<div class="<?php print_r($css_classes); ?>">
									<div class="form-group">
										<select id="perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?>-filter" class="selectpicker data-title select-all-opt start-filtering-<?php print_r($last_name['permissions']['menu_permission']); ?>" data-title-en="Users, Ranks" data-title-fa="کاربران ، مقام ها" data-style="btn btn-primary" multiple title="<?php if($GLOBALS['user_language']=='en'){?>Users, Ranks<?php }else{?>کاربران ، مقام ها<?php } ?>" data-size="7" data-live-search="true">
											<optgroup label="<?php if($GLOBALS['user_language']=='en'){?>Users<?php }else{?>کاربران<?php } ?>" class="data-label" data-label-en="Users" data-label-fa="کاربران">
												<?php
													$res_users=$connection->query("SELECT * FROM ".$sub_name."admins");
													while($users=$res_users->fetch()){
														if(getSetting("op_admin")!=$users['username']){
															if(checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"")==1 && checkPermission(2,getColumnByName($sub_name."admins","username")['id'],"read",getColumnByName($sub_name."admins","username")['act'],getTableByName($sub_name."admins")['id'])==1){
												?>
													<option data-tokens="Users کاربران" value="<?php print_r($users['username']); ?>" class="users-option data-text" data-text-fa="<?php print_r($users['username']); ?>" data-text-en="<?php print_r($users['username']); ?>">
														<?php print_r($users['username']); ?>
													</option>
												<?php
															}
														}
													}
												?>
											</optgroup>
											<optgroup label="<?php if($GLOBALS['user_language']=='en'){?>Ranks<?php }else{?>مقام ها<?php } ?>" class="data-label" data-label-en="Ranks" data-label-fa="مقام ها">
												<?php
													$res_ranks=$connection->query("SELECT * FROM ".$sub_name."rank");
													while($ranks=$res_ranks->fetch()){
														if(checkPermission(1,getTableByName($sub_name."rank")['id'],"read",getTableByName($sub_name."rank")['act'],"")==1 && checkPermission(2,getColumnByName($sub_name."rank","rank_name_fa")['id'],"read",getColumnByName($sub_name."rank","rank_name_fa")['act'],getTableByName($sub_name."rank")['id'])==1 && checkPermission(2,getColumnByName($sub_name."rank","rank_name_en")['id'],"read",getColumnByName($sub_name."rank","rank_name_en")['act'],getTableByName($sub_name."rank")['id'])==1){
												?>
													<option data-tokens="Ranks مقام ها" class="ranks-option data-text" value="<?php print_r($ranks['id']); ?>" data-text-fa="<?php print_r($ranks['rank_name_fa']); ?>" data-text-en="<?php print_r($ranks['rank_name_en']); ?>">
														<?php if($GLOBALS['user_language']=='en'){print_r($ranks['rank_name_en']);}else{print_r($ranks['rank_name_fa']);} ?>
													</option>
												<?php
														}
													}
												?>
											</optgroup>
										</select>
										<span class="form-text data-text" data-text-en="Filter users and ranks" data-text-fa="فیلتر کاربر ها و مقام ها"><?php print_r($GLOBALS['user_language']=="en" ? "Filter users and ranks":"فیلتر کاربر ها و مقام ها"); ?></span>
									</div>
								</div>
								<div class="<?php print_r($css_classes); ?>">
									<div class="form-group">
										<select id="perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?>-filter" class="selectpicker data-title select-all-opt select-<?php print_r($last_name['permissions']['menu_permission']); ?>-first-filter start-filtering-<?php print_r($last_name['permissions']['menu_permission']); ?>" data-title-en="Permissions Power" data-title-fa="سطح دسترسی ها" data-style="btn btn-primary" multiple title="<?php if($GLOBALS['user_language']=='en'){?>Permissions Power<?php }else{?>سطح دسترسی ها<?php } ?>" data-size="7" data-live-search="true">
											<?php
												foreach ($permission_power_list as $key => $value) {
													if($value[1]!="-1"){
											?>
												<option value="<?php print_r($value[1]); ?>"><?php print_r($value[1]); ?></option>
											<?php
													}
												}
											?>
										</select>
										<span class="form-text data-text" data-text-en="Filter permission power" data-text-fa="فیلتر سطح دسترسی"><?php print_r($GLOBALS['user_language']=="en" ? "Filter permission power":"فیلتر سطح دسترسی"); ?></span>
									</div>
								</div>
							</div>
							<table id="<?php print_r($last_name['permissions']['menu_permission']); ?>_permission" class="table table-striped w-100 disable_custom_table">
								<thead>
									<tr>
										<th data-priority="3">
											<label class="data-text" data-text-en="Relevant menu" data-text-fa="منو مربوطه" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Relevant menu":"منو مربوطه"); ?></label>
										</th>
										<th data-priority="4">
											<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Relevant admin" data-text-fa="ادمین مربوطه"><?php print_r($GLOBALS['user_language']=="en" ? "Relevant admin":"ادمین مربوطه"); ?></label>
										</th>
										<th data-priority="6">
											<label class="data-text" data-text-en="Permission Power" data-text-fa="سطح دسترسی" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Permission Power":"سطح دسترسی"); ?></label>
										</th>
										<th data-priority="2">
											<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات"><?php print_r($GLOBALS['user_language']=="en" ? "Operation":"عملیات"); ?></label>
										</th>
										<th data-priority="7"></th>
									</tr>
								</thead>
								<tbody>

								</tbody>
								<tfoot>
									<tr>
										<th>
											<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Relevant menu" data-text-fa="منو مربوطه"><?php print_r($GLOBALS['user_language']=="en" ? "Relevant menu":"منو مربوطه"); ?></label>
										</th>
										<th>
											<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Relevant admin" data-text-fa="ادمین مربوطه"><?php print_r($GLOBALS['user_language']=="en" ? "Relevant admin":"ادمین مربوطه"); ?></label>
										</th>
										<th>
											<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Permission power" data-text-fa="سطح دسترسی"><?php print_r($GLOBALS['user_language']=="en" ? "Permission power":"سطح دسترسی"); ?></label>
										</th>
										<th>
											<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات"><?php print_r($GLOBALS['user_language']=="en" ? "Operation":"عملیات"); ?></label>
										</th>
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
			<!-- end card -->
		</div>
	</div>
	<div class="modal bd-example-modal-xl" id="rows_information_<?php print_r($last_name['permissions']['menu_permission']); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl" style="transform: translateY(0%);">
			<div class="modal-content bg-dark">
				<div class="modal-header justify-content-center">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						<i class="tim-icons icon-simple-remove"></i>
					</button>
					<span class="badge badge-pill badge-success log_Added" data-text-en="Added" data-text-fa="افزوده شده">Loading ...</span>&nbsp;&nbsp;&nbsp;<span class="badge badge-pill badge-info log_Modified" data-text-en="Modified" data-text-fa="اصلاح شده">Loading ...</span>&nbsp;&nbsp;&nbsp;<span class="badge badge-pill badge-warning log_Removed" data-text-en="Removed" data-text-fa="حذف شده">Loading ...</span>&nbsp;&nbsp;&nbsp;<span class="badge badge-pill badge-danger log_Failed" data-text-en="Failed" data-text-fa="ناموفق">Loading ...</span>
				</div>
				<div class="modal-body">
					<table id="rows_informations_<?php print_r($last_name['permissions']['menu_permission']); ?>" class="table table-striped disable_custom_table" style="width: 100% !important;">
						<thead>
							<tr>
								<th scope="col" data-text-en="Username" data-text-fa="نام کاربری"><?php if($GLOBALS['user_language']=="fa"){?>نام کاربری<?php }else{ ?>Username<?php } ?></th>
								<th scope="col" data-text-en="Menu Name" data-text-fa="نام منو"><?php if($GLOBALS['user_language']=="fa"){?>نام منو<?php }else{ ?>Menu Name<?php } ?></th>
								<th scope="col" data-text-en="Permission Power" data-text-fa="سطح دسترسی"><?php if($GLOBALS['user_language']=="fa"){?>سطح دسترسی<?php }else{ ?>Permission Power<?php } ?></th>
								<th scope="col" data-text-en="Status" data-text-fa="وضعیت"><?php if($GLOBALS['user_language']=="fa"){?>وضعیت<?php }else{ ?>Status<?php } ?></th>
							</tr>
						</thead>
						<tbody id="permission_logs_<?php print_r($last_name['permissions']['menu_permission']); ?>">
							<tr><td colspan="5" class="dataTables_empty text-center">Loading <i class="fas fa-spin fa-spinner-third"></i></td></tr>
						</tbody>
						<tfoot>
							<tr>
								<th scope="col" data-text-en="Username" data-text-fa="نام کاربری"><?php if($GLOBALS['user_language']=="fa"){?>نام کاربری<?php }else{ ?>Username<?php } ?></th>
								<th scope="col" data-text-en="Menu Name" data-text-fa="نام منو"><?php if($GLOBALS['user_language']=="fa"){?>نام منو<?php }else{ ?>Menu Name<?php } ?></th>
								<th scope="col" data-text-en="Permission Power" data-text-fa="سطح دسترسی"><?php if($GLOBALS['user_language']=="fa"){?>سطح دسترسی<?php }else{ ?>Permission Power<?php } ?></th>
								<th scope="col" data-text-en="Status" data-text-fa="وضعیت"><?php if($GLOBALS['user_language']=="fa"){?>وضعیت<?php }else{ ?>Status<?php } ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal"><?php print_r($GLOBALS['user_language']=="en" ? "Close":"خروج"); ?></button>
				</div>
			</div>
		</div>
	</div>
	<script>
		// Menu Permission

		var $permission_<?php print_r($last_name['permissions']['menu_permission']); ?>_value=$("#perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?>").val().toString().split(",");

		$(document).on("change",".start-filtering-<?php print_r($last_name['permissions']['menu_permission']); ?>",function(){
			$("#<?php print_r($last_name['permissions']['menu_permission']); ?>_permission").DataTable().ajax.reload( null, false );
		});

		function getFilter_<?php print_r($last_name['permissions']['menu_permission']); ?>(){
			$username=$("#perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?>-filter").val();
			$<?php print_r($last_name['permissions']['menu_permission']); ?>_id=$("#perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?>-filter").val();
			$permission_value=$("#perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?>-filter").val();
			return JSON.stringify([$<?php print_r($last_name['permissions']['menu_permission']); ?>_id,$username,$permission_value]);
		}

		function callDataTable_<?php print_r($last_name['permissions']['menu_permission']); ?>(){
			$('#<?php print_r($last_name['permissions']['menu_permission']); ?>_permission').dataTable().fnDestroy();
			$('#<?php print_r($last_name['permissions']['menu_permission']); ?>_permission').DataTable({
				"drawCallback": function( settings ) {
					pscrollbarUpdate();
				},
				"processing": true,
				"serverSide": true,
				"ajax": {
					url: 'permissions/datatable_json/permissions_<?php print_r($last_name['permissions']['menu_permission']); ?>_json.php',
					type: 'POST',
					"data": function ( d ) {
						d.<?php print_r($last_name['permissions']['menu_permission']); ?>_name = "<?php print_r($sub_name); ?>menu_permissions";
						d.custom_filter = getFilter_<?php print_r($last_name['permissions']['menu_permission']); ?>();
						d.primaryKey = "id";
					}
				},
				"columns": [
					{},
					{},
					{},
					{
						"class": "actions_dir",
						"orderable": false,
						// "data": null,
						// "defaultContent": '<input type="checkbox" checked name="checkbox" class="bootstrap-switch" data-on-label="<i class='+"'tim-icons icon-check-2'"+'></i>" data-off-label="<i class='+"'tim-icons icon-simple-remove'"+'></i>">'
					},
					{}
				],
				// "order": default_datatable_order,
				"pagingType": "full_numbers",
				// "pageLength": default_datatable_length,
				"lengthMenu": [
					[10, 25, 50, 100, 250 , 500, -1],
					[10, 25, 50, 100, 250 , 500, "All"]
				],
				responsive: true,
				"language": langObjs(),
				"columnDefs": [
					// {
					//	 "targets": [5],
					//	 "visible": false,
					//	 "searchable": true
					// },
					{ "class":"text-right" , "targets": 3 },
					{ "visible": false, "targets": [4] }
				]
			});
		}

		$('#<?php print_r($last_name['permissions']['menu_permission']); ?>_permission').on( 'error.dt', function ( e, settings, techNote, message ) {
			// console.log( 'An error has been reported by DataTables: ', message );
			// window.location.reload();
		}).DataTable();

		function permissionOperator_<?php print_r($last_name['permissions']['menu_permission']); ?>($button,$content){
			$username=$("#perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?>").val();
			$<?php print_r($last_name['permissions']['menu_permission']); ?>_id=$("#perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?>").val();
			$permission_value=$("#perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?>").val();

			if($username=="" || $username==[] || $username.length==0 || $username=="-"){
				callSelectInput("#perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?>");
				$($button).html($($button).attr("data-text-"+language)).removeClass("disabled");
			}else if($<?php print_r($last_name['permissions']['menu_permission']); ?>_id=="" || $<?php print_r($last_name['permissions']['menu_permission']); ?>_id==[] || $<?php print_r($last_name['permissions']['menu_permission']); ?>_id.length==0 || $<?php print_r($last_name['permissions']['menu_permission']); ?>_id=="-"){
				callSelectInput("#perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?>");
				$($button).html($($button).attr("data-text-"+language)).removeClass("disabled");
			}else if($permission_value=="" || $permission_value==[] || $permission_value.length==0 || $permission_value=="-"){
				callSelectInput("#perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?>");
				$($button).html($($button).attr("data-text-"+language)).removeClass("disabled");
			}else{
				$.when($($button).html("<i class='fal fa-spinner-third fa-spin'></i>").addClass("disabled")).done(function(){
					$.when($.post("class/action.php?"+$content,{
						username : $username,
						<?php print_r($last_name['permissions']['menu_permission']); ?>_id : $<?php print_r($last_name['permissions']['menu_permission']); ?>_id,
						permission_value : $permission_value
					},function(data, status){
						$response=data.split("_.._");
						if($response[$response.length-1]=="done"){
							if($response.length>1){
								$added_save=0;
								$failed_save=0;
								$modified_save=0;
								$removed_save=0;
								$status_save="";
								for($i=0;$i<=$response.length-2;$i++){
									$first_class="";
									$first_text="";
									$status_save+="<tr>";
									$data=$response[$i].split("_._");
									for($ii=0;$ii<=$data.length-1;$ii++){
										if($ii==0){
											switch ($data[$data.length-1]) {
												case "added":
													$added_save++;
													$first_class="success";
													if(language=="fa"){
														$first_text="افزوده شده";
													}else{
														$first_text="Added";
													}
												break;
												case "deleted":
													$removed_save++;
													$first_class="warning";
													$first_text="Removed";
													if(language=="fa"){
														$first_text="حذف شده";
													}else{
														$first_text="Removed";
													}
												break;
												case "modified":
													$modified_save++;
													$first_class="info";
													if(language=="fa"){
														$first_text="اصلاح شده";
													}else{
														$first_text="Modified";
													}
												break;
												case "failed":
													$failed_save++;
													$first_class="danger";
													if(language=="fa"){
														$first_text="ناموفق";
													}else{
														$first_text="Failed";
													}
												break;
											}
										}
										if($ii!=$data.length-1){
											$status_save+="<td>"+$data[$ii]+"</td>";
										}else{
											$status_save+='<td><span class="badge badge-pill badge-'+$first_class+'">'+$first_text+'</span></td>';
										}
									}
									$status_save+="</tr>";
								}

								if(language=="fa"){
									$Username_Username="نام کاربری";
									$<?php print_r($last_name['permissions']['menu_permission']); ?>_Name="نام منو";
									$Permission_Power="سطح دسترسی";
									$Status_Status="وضعیت";
								}else{
									$Username_Username="Username";
									$<?php print_r($last_name['permissions']['menu_permission']); ?>_Name="Menu Name";
									$Permission_Power="Permission Power";
									$Status_Status="Status";
								}

								$('#rows_informations_<?php print_r($last_name['permissions']['menu_permission']); ?> thead, #rows_informations_<?php print_r($last_name['permissions']['menu_permission']); ?> tfoot').empty().append(
									'<tr>' +
										'<th scope="col">'+$Username_Username+'</th>' +
										'<th scope="col">'+$<?php print_r($last_name['permissions']['menu_permission']); ?>_Name+'</th>' +
										'<th scope="col">'+$Permission_Power+'</th>' +
										'<th scope="col">'+$Status_Status+'</th>' +
									'</tr>'
								);

								$(".log_Added").empty().text($added_save+" "+$(".log_Added").attr("data-text-"+language));
								$(".log_Modified").empty().text($modified_save+" "+$(".log_Modified").attr("data-text-"+language));
								$(".log_Removed").empty().text($removed_save+" "+$(".log_Removed").attr("data-text-"+language));
								$(".log_Failed").empty().text($failed_save+" "+$(".log_Failed").attr("data-text-"+language));

								$("#permission_logs_<?php print_r($last_name['permissions']['menu_permission']); ?>").empty();
								$('#rows_informations_<?php print_r($last_name['permissions']['menu_permission']); ?>').DataTable().destroy();
								$("#permission_logs_<?php print_r($last_name['permissions']['menu_permission']); ?>").empty().append($status_save);
								// $('#rows_informations_<?php print_r($last_name['permissions']['menu_permission']); ?>').DataTable({
								// 	"drawCallback": function( settings ) {pscrollbarUpdate();},
								// 	"responsive": true,
								// 	"language": langObjs()
								// });

								if($("#menu_permission").hasClass("active")){
									$("#rows_information_<?php print_r($last_name['permissions']['menu_permission']); ?>").modal("show");
								}else{
									$("a[href='#menu_permission']").append('<span id="badage-menu_permission" class="badge badge-light">'+($("#badage-menu_permission").length ? parseInt($("#badage-menu_permission").html())+1:1)+'</span>');
									$modal_interval_menu_permission=setInterval(() => {
										if($("#menu_permission").hasClass("active")){
											$("#rows_information_<?php print_r($last_name['permissions']['menu_permission']); ?>").modal("show");
											$("#badage-menu_permission").remove();
											clearInterval($modal_interval_menu_permission);
										}
									}, 500);
								}

								$.fn.dataTable.ext.errMode = 'none';
								$('#rows_informations_<?php print_r($last_name['permissions']['menu_permission']); ?>').on( 'error.dt', function ( e, settings, techNote, message ) {
									// console.log( 'An error has been reported by DataTables: ', message );
									// window.location.reload();
								}).DataTable();
							}else{
								Swal.fire({
									title: (language=="en" ? "Operating Permissions":"در حال عملیات بر روی دسترسی ها"),
									text: (language=="en" ? "Permissions are all already modified!":"تمام دسترسی های منتخب از قبل اعمال شده!"),
									icon: 'info',
									showCloseButton: false,
									showCancelButton: false,
									focusConfirm: true,
									confirmButtonText: (language=="en" ? "Ok":"تایید")
								});
							}
						}else{
							feedbackOperations(data);
						}
						if(language=="fa"){

						}else{

						}
					})).done(function(){
						$("#<?php print_r($last_name['permissions']['menu_permission']); ?>_permission").DataTable().ajax.reload( null, false );
						$($button).html($($button).attr("data-text-"+language)).removeClass("disabled");
					});
				});
			}
		}

		function action_permission_<?php print_r($GLOBALS['last_name']['permissions']['menu_permission']); ?>($action,$element,$id,$event){
			switch ($action) {
				case "edit":
					var $editor=[[],[],[]];
					$.post("class/action.php?edit_permission_<?php print_r($GLOBALS['last_name']['permissions']['menu_permission']); ?>",{
						id : $id
					},function(data, status){
						if(status=="success"){
							if(data!=0){
								$editor=JSON.parse(data);
							}else{
								$editor=0;
							}
						}else{
							$editor=0;
						}
					}).done(function(){
						$($element).children().removeClass("far fa-spin fa-spinner").addClass("tim-icons icon-pencil");
						if($editor!=0){

							$username=$("#perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?>").val();
							$<?php print_r($last_name['permissions']['menu_permission']); ?>_id=$("#perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?>").val();
							$permission_value=$("#perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?>").val();

							if($event.ctrlKey){
								if($username.indexOf("-1")==-1){
									$username.push($editor[0]);
									$("#perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $username).selectpicker('refresh');
								}

								if($<?php print_r($last_name['permissions']['menu_permission']); ?>_id.indexOf("-1")==-1){
									$<?php print_r($last_name['permissions']['menu_permission']); ?>_id.push($editor[1]);
									$("#perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $<?php print_r($last_name['permissions']['menu_permission']); ?>_id).selectpicker('refresh');
								}

								if($permission_value.indexOf("-1")==-1){
									$permission_value.push($editor[2]);
									$("#perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $permission_value).selectpicker('refresh');
								}

								$("#<?php print_r($sub_name."menu"); ?>_permissions_"+$id).addClass("selected");
							}else if($event.altKey){

								if($username.indexOf("-1")!=-1){
									$("#perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?> option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",false);
								}
								if($<?php print_r($last_name['permissions']['menu_permission']); ?>_id.indexOf("-1")!=-1){
									$("#perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?> option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",false);
								}
								if($permission_value.indexOf("-1")!=-1){
									$("#perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?> option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",false);
								}

								$username=[];
								$("#perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $username).selectpicker('refresh');

								$<?php print_r($last_name['permissions']['menu_permission']); ?>_id=[];
								$("#perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $<?php print_r($last_name['permissions']['menu_permission']); ?>_id).selectpicker('refresh');

								$permission_value=[];
								$("#perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $permission_value).selectpicker('refresh');

								$("tr.selected").removeClass("selected"); 
							}else{

								if($username.indexOf("-1")!=-1){
									$("#perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?> option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",false);
								}
								if($<?php print_r($last_name['permissions']['menu_permission']); ?>_id.indexOf("-1")!=-1){
									$("#perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?> option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",false);
								}
								if($permission_value.indexOf("-1")!=-1){
									$("#perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?> option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",false);
								}

								$username=[$editor[0]];
								$("#perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $username).selectpicker('refresh');

								$<?php print_r($last_name['permissions']['menu_permission']); ?>_id=[$editor[1]];
								$("#perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $<?php print_r($last_name['permissions']['menu_permission']); ?>_id).selectpicker('refresh');

								$permission_value=[$editor[2]];
								$("#perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $permission_value).selectpicker('refresh');

								$("tr.selected").removeClass("selected");
								$("#<?php print_r($sub_name."menu"); ?>_permissions_"+$id).addClass("selected");
							}
						}else{

						}
					});
				break;
				case "selectAll":

					$permission_<?php print_r($last_name['permissions']['menu_permission']); ?>_value=["-1","_all"].toString().split(",");

					$username=['-1','_all'];
					$("#perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?> option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",true);
					$("#perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $username).selectpicker('refresh').change();

					$<?php print_r($last_name['permissions']['menu_permission']); ?>_id=['-1','_all'];
					$("#perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?> option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",true);
					$("#perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $<?php print_r($last_name['permissions']['menu_permission']); ?>_id).selectpicker('refresh').change();

					$permission_value=['-1','_all'];
					$("#perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?> option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",true);
					$("#perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $permission_value).selectpicker('refresh').change();

					$("tr.selected").removeClass("selected");
				break;
				case "deselectAll":

					$username=[];
					$("#perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?> option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",false);
					$("#perm-user_rank-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $username).selectpicker('refresh').change();

					$<?php print_r($last_name['permissions']['menu_permission']); ?>_id=[];
					$("#perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?> option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",false);
					$("#perm-table-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $<?php print_r($last_name['permissions']['menu_permission']); ?>_id).selectpicker('refresh').change();

					$permission_value=[];
					$("#perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?> option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",false);
					$("#perm-permission_value-<?php print_r($last_name['permissions']['menu_permission']); ?>").selectpicker('val', $permission_value).selectpicker('refresh').change();

					$("tr.selected").removeClass("selected");
				break;
			}
		}
		// Menu Permission
	</script>
<?php
			}else{
				echo $outofpermission;
			}
		}else{
			echo $outofpermission;
		}
	}else{
?>
	<script>
		window.location="setup/";
	</script>
<?php
	}
?>
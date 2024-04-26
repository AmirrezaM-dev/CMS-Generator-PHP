<?php
	if($table_get['visible']==1 || isset($op_admin) && $op_admin){
		if(checkPermission(1,$table_id,"read",$table_get['act'],"")){
			$sql_get_search=$connection->query("SELECT * FROM ".$sub_name."table_user_setting WHERE table_id='".$table_id."' AND admin_id='".$_SESSION['username']."' AND save_name='search' AND act=1");
			$sql_get_order_column=$connection->query("SELECT * FROM ".$sub_name."table_user_setting WHERE table_id='".$table_id."' AND admin_id='".$_SESSION['username']."' AND save_name='order_column' AND act=1");
			$sql_get_order_dir=$connection->query("SELECT * FROM ".$sub_name."table_user_setting WHERE table_id='".$table_id."' AND admin_id='".$_SESSION['username']."' AND save_name='order_dir' AND act=1");
			$sql_get_start=$connection->query("SELECT * FROM ".$sub_name."table_user_setting WHERE table_id='".$table_id."' AND admin_id='".$_SESSION['username']."' AND save_name='start' AND act=1");
			$sql_get_length=$connection->query("SELECT * FROM ".$sub_name."table_user_setting WHERE table_id='".$table_id."' AND admin_id='".$_SESSION['username']."' AND save_name='length' AND act=1");
			$search=($sql_get_search->rowCount()!=0 ? $sql_get_search->fetch()['save_value']:"");
			$order_column=($sql_get_order_column->rowCount()!=0 ? $sql_get_order_column->fetch()['save_value']:0);
			$order_dir=($sql_get_order_dir->rowCount()!=0 ? $sql_get_order_dir->fetch()['save_value']:"asc");
			$start=($sql_get_start->rowCount()!=0 ? $sql_get_start->fetch()['save_value']:0);
			$length=($sql_get_length->rowCount()!=0 ? $sql_get_length->fetch()['save_value']:10);
			$res_menu_activition=$connection->query("SELECT * FROM ".$sub_name."menu WHERE act=1");
			// while($menu_activition=$res_menu_activition->fetch()){
			//     if($menu_activition['menu_mode']!=2){
			?>
				<script>
					// if(current_page=="<?php //print_r($menu_activition['menu_link']); ?>"){
					//     $("#navbarLoader li.active").removeClass("active");
					//     $(".<?php //print_r(str_replace(".","_-_",str_replace("?","_Q_",str_replace("=","_E_",$menu_activition['menu_link'])))); ?>_NAV_").addClass("active");
					//     <?php
					//         if($menu_activition['is_child']==1){
					//             $res_parent_menu=$connection->query("SELECT * FROM ".$sub_name."menu WHERE id='".$menu_activition['parent_id']."' AND act=1 AND is_parent=1");
					//             if($res_parent_menu->rowCount()==1){
					//                 $parent_menu=$res_parent_menu->fetch();
					//                 $menu_code=($parent_menu['menu_name_en']."-".$parent_menu['id']);
					//                 if($parent_menu['menu_mode']!=2 && $parent_menu['menu_link']!="" && !empty($parent_menu['menu_link'])){
					//     ?>
					//         $(".<?php //print_r(str_replace(".","_-_",str_replace("?","_Q_",str_replace("=","_E_",$parent_menu['menu_link'])))); ?>_NAV_").addClass("active");
					//     <?php
					//                 }else{
					//     ?>
					//         $("#menu_parent_<?php //print_r($parent_menu['id']); ?>").addClass("active");
					//     <?php
					//                 }
					//     ?>
					//         $("#<?php //print_r($menu_code); ?>").addClass("show");
					//         $($("#<?php //print_r($menu_code); ?>").parent().children()[1]).removeClass("collapsed").attr("aria-expanded","true");
					//     <?php
					//             }
					//         }
					//     ?>
					// }
				</script>
			<?php
			//     }
			// }
			?>
				<script>
					var default_datatable_order=[[<?php print_r($order_column); ?>, '<?php print_r($order_dir); ?>']],
						default_datatable_length=<?php print_r($length); ?>;
						default_datatable_search="<?php print_r($search); ?>";
						default_datatable_start=<?php print_r($start); ?>;
						default_datatable_page=default_datatable_start/default_datatable_length;
				</script>
				<?php
					/*
						<div class="col-md-8 ml-auto mr-auto">
							<h2 class="text-center">Managing your Database table</h2>
							<p class="text-center">The best cms for manage your Database table is here</a>
							</p>
						</div>
					*/
				?>
				<div class="row mt-5">
					<div class="col-md-12">
						<div class="card">
							<div class="card-body">
								<div class="toolbar text-center">
									<button class="btn data-text" onclick="$('#datatable').DataTable().ajax.reload(null, false);return false;" data-text-en="Refresh" data-text-fa="بروزرسانی"><?php print_r($GLOBALS['user_language']=="en" ? "Refresh":"بروزرسانی"); ?></button>

									<?php
										if(isset($_GET["name"]) && $_GET["name"]=="table_config"){
									?>
										<?php
											if(checkPermission(1,getTableByName($sub_name."table_config")['id'],"create",getTableByName($sub_name."table_config")['act'],"")){
										?>
											<button class="btn btn-success animation-on-hover data-text" data-text-en="Add New" data-text-fa="افزودن جدید" type="button" onclick="enableTable('','new');return false;"><?php print_r($GLOBALS['user_language']=="en" ? "Add New":"افزودن جدید"); ?></button>
										<?php
											}
											if(checkPermission(1,getTableByName($sub_name."table_config")['id'],"update",getTableByName($sub_name."table_config")['act'],"")){
										?>
										<button class="btn btn-info animation-on-hover data-text" type="button" onclick="window.location.href='#tables?create';return false;" data-text-en="Edit Current Table" data-text-fa="ویرایش جدول فعلی"><?php print_r($GLOBALS['user_language']=="en" ? "Edit Current Table":"ویرایش جدول فعلی"); ?></button>
										<?php
											}
										?>
									<?php
										}
										if(isset($_GET["name"]) && $_GET["name"]!="table_config" || !isset($_GET["name"])){
									?>
										<button class="btn btn-info data-text" data-text-en="Select All" data-text-fa="انتخاب همه" onclick="selectAllRowsDataTable();"><?php print_r($GLOBALS['user_language']=="en" ? "Select All":"انتخاب همه"); ?></button>
										<button class="btn btn-info data-text" data-text-en="Deselect All" data-text-fa="لغو انتخاب همه" onclick="deselectAllRowsDataTable();"><?php print_r($GLOBALS['user_language']=="en" ? "Deselect All":"لغو انتخاب همه"); ?></button>
										<button class="btn btn-info data-text" data-text-en="Reverse Selected" data-text-fa="معکوس کردن منتخب" onclick="reverseSelectedRowsDataTable();"><?php print_r($GLOBALS['user_language']=="en" ? "Reverse Selected":"معکوس کردن منتخب"); ?></button>
										<?php
											if(checkPermission(1,getTableByName($sub_name.$_GET["name"])['id'],"create",getTableByName($sub_name.$_GET["name"])['act'],"")){
										?>
											<button class="btn btn-success animation-on-hover data-text" data-text-en="Add New" data-text-fa="افزودن جدید" type="button" onclick="window.location.href='#tables?name=<?php print_r($_GET['name']); ?>&action=new';return false;"><?php print_r($GLOBALS['user_language']=="en" ? "Add New":"افزودن جدید"); ?></button>
											<button class="btn btn-info data-text" data-text-en="Copy Selected" data-text-fa="کپی منتخب" onclick="copySelectedDataOfTable();"><?php print_r($GLOBALS['user_language']=="en" ? "Copy Selected":"کپی منتخب"); ?></button>
										<?php
											}
											if(checkPermission(1,getTableByName($sub_name.$_GET["name"])['id'],"update",getTableByName($sub_name.$_GET["name"])['act'],"")){
										?>
											<button class="btn btn-info data-text" data-text-en="Edit Selected" data-text-fa="ویرایش منتخب" onclick="editSelectedDataOfTable();"><?php print_r($GLOBALS['user_language']=="en" ? "Edit Selected":"ویرایش منتخب"); ?></button>
										<?php
											}
											if(checkPermission(1,getTableByName($sub_name.$_GET["name"])['id'],"delete",getTableByName($sub_name.$_GET["name"])['act'],"")){
										?>
											<button class="btn btn-danger data-text" data-text-en="Delete Selected" data-text-fa="حذف منتخب" onclick="deleteSelectedDataOfTable();"><?php print_r($GLOBALS['user_language']=="en" ? "Delete Selected":"حذف منتخب"); ?></button>
											<button class="btn btn-danger data-text" data-text-en="Delete All" data-text-fa="حذف همه" onclick="deleteAllDataOfTable();"><?php print_r($GLOBALS['user_language']=="en" ? "Delete All":"حذف همه"); ?></button>
										<?php
											}
										?>
										<!-- <button class="btn btn-info">Copy & Edit Selected</button> -->
										<!-- <button class="btn btn-warning">Automatic Sorting</button> -->
										<!-- <button class="btn btn-primary">Selection : ON</button> -->
									<?php
										}
									?>

									<button class="btn <?php if(getUserSetting('table-order-mode')=="true"){?>btn-success<?php } ?> animation-on-hover table_order_mode_btn data-text" onclick="if($(this).hasClass('btn-success')){order_id_mode('false');callDataTable();}else{order_id_mode('true');callDataTable();}" data-text-en="Show orders" data-text-fa="نمایش ترتیب"><?php print_r($GLOBALS['user_language']=="en" ? "Show orders":"نمایش ترتیب"); ?></button>
								</div>
								<br>
								<table id="datatable" class="table table-striped">
									<thead>
										<tr>
											<th data-priority="1">
												<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Order" data-text-fa="ترتیب"><?php print_r($GLOBALS['user_language']=="en" ? "Order":"ترتیب"); ?></label>
											</th>
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
											<th class="sorting_desc_disabled sorting_asc_disabled" data-priority="3" style="margin-bottom: 0px !important;">
												<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Actions" data-text-fa="عملیات"><?php print_r($GLOBALS['user_language']=="en" ? "Actions":"عملیات"); ?></label>
											</th>
										</tr>
									</thead>
									<tbody>

									</tbody>
									<tfoot>
										<tr>
											<th data-priority="1">
												<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Order" data-text-fa="ترتیب"><?php print_r($GLOBALS['user_language']=="en" ? "Order":"ترتیب"); ?></label>
											</th>
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
											<th class="sorting_desc_disabled sorting_asc_disabled" data-priority="3" style="margin-bottom: 0px !important;">
												<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Actions" data-text-fa="عملیات"><?php print_r($GLOBALS['user_language']=="en" ? "Actions":"عملیات"); ?></label>
											</th>
										</tr>
									</tfoot>
								</table>
								<br>
								<div class="toolbar text-center">
									<button class="btn data-text" onclick="$('#datatable').DataTable().ajax.reload(null, false);return false;" data-text-en="Refresh" data-text-fa="بروزرسانی"><?php print_r($GLOBALS['user_language']=="en" ? "Refresh":"بروزرسانی"); ?></button>

									<?php
										if(isset($_GET["name"]) && $_GET["name"]=="table_config"){
									?>
										<?php
											if(checkPermission(1,getTableByName($sub_name."table_config")['id'],"create",getTableByName($sub_name."table_config")['act'],"")){
										?>
											<button class="btn btn-success animation-on-hover data-text" data-text-en="Add New" data-text-fa="افزودن جدید" type="button" onclick="enableTable('','new');return false;"><?php print_r($GLOBALS['user_language']=="en" ? "Add New":"افزودن جدید"); ?></button>
										<?php
											}
											if(checkPermission(1,getTableByName($sub_name."table_config")['id'],"update",getTableByName($sub_name."table_config")['act'],"")){
										?>
										<button class="btn btn-info animation-on-hover data-text" type="button" onclick="window.location.href='#tables?create';return false;" data-text-en="Edit Current Table" data-text-fa="ویرایش جدول فعلی"><?php print_r($GLOBALS['user_language']=="en" ? "Edit Current Table":"ویرایش جدول فعلی"); ?></button>
										<?php
											}
										?>
									<?php
										}
										if(isset($_GET["name"]) && $_GET["name"]!="table_config" || !isset($_GET["name"])){
									?>
										<button class="btn btn-info data-text" data-text-en="Select All" data-text-fa="انتخاب همه" onclick="selectAllRowsDataTable();"><?php print_r($GLOBALS['user_language']=="en" ? "Select All":"انتخاب همه"); ?></button>
										<button class="btn btn-info data-text" data-text-en="Deselect All" data-text-fa="لغو انتخاب همه" onclick="deselectAllRowsDataTable();"><?php print_r($GLOBALS['user_language']=="en" ? "Deselect All":"لغو انتخاب همه"); ?></button>
										<button class="btn btn-info data-text" data-text-en="Reverse Selected" data-text-fa="معکوس کردن منتخب" onclick="reverseSelectedRowsDataTable();"><?php print_r($GLOBALS['user_language']=="en" ? "Reverse Selected":"معکوس کردن منتخب"); ?></button>
										<?php
											if(checkPermission(1,getTableByName($sub_name.$_GET["name"])['id'],"create",getTableByName($sub_name.$_GET["name"])['act'],"")){
										?>
											<button class="btn btn-success animation-on-hover data-text" data-text-en="Add New" data-text-fa="افزودن جدید" type="button" onclick="window.location.href='#tables?name=<?php print_r($_GET['name']); ?>&action=new';return false;"><?php print_r($GLOBALS['user_language']=="en" ? "Add New":"افزودن جدید"); ?></button>
											<button class="btn btn-info data-text" data-text-en="Copy Selected" data-text-fa="کپی منتخب" onclick="copySelectedDataOfTable();"><?php print_r($GLOBALS['user_language']=="en" ? "Copy Selected":"کپی منتخب"); ?></button>
										<?php
											}
											if(checkPermission(1,getTableByName($sub_name.$_GET["name"])['id'],"update",getTableByName($sub_name.$_GET["name"])['act'],"")){
										?>
											<button class="btn btn-info data-text" data-text-en="Edit Selected" data-text-fa="ویرایش منتخب" onclick="editSelectedDataOfTable();"><?php print_r($GLOBALS['user_language']=="en" ? "Edit Selected":"ویرایش منتخب"); ?></button>
										<?php
											}
											if(checkPermission(1,getTableByName($sub_name.$_GET["name"])['id'],"delete",getTableByName($sub_name.$_GET["name"])['act'],"")){
										?>
											<button class="btn btn-danger data-text" data-text-en="Delete Selected" data-text-fa="حذف منتخب" onclick="deleteSelectedDataOfTable();"><?php print_r($GLOBALS['user_language']=="en" ? "Delete Selected":"حذف منتخب"); ?></button>
											<button class="btn btn-danger data-text" data-text-en="Delete All" data-text-fa="حذف همه" onclick="deleteAllDataOfTable();"><?php print_r($GLOBALS['user_language']=="en" ? "Delete All":"حذف همه"); ?></button>
										<?php
											}
										?>
										<!-- <button class="btn btn-info">Copy & Edit Selected</button> -->
										<!-- <button class="btn btn-warning">Automatic Sorting</button> -->
										<!-- <button class="btn btn-primary">Selection : ON</button> -->
									<?php
										}
									?>

									<button class="btn <?php if(getUserSetting('table-order-mode')=="true"){?>btn-success<?php } ?> animation-on-hover table_order_mode_btn data-text" onclick="if($(this).hasClass('btn-success')){order_id_mode('false');callDataTable();}else{order_id_mode('true');callDataTable();}" data-text-en="Show orders" data-text-fa="نمایش ترتیب"><?php print_r($GLOBALS['user_language']=="en" ? "Show orders":"نمایش ترتیب"); ?></button>
								</div>
							</div>
							<!-- end content-->
						</div>
						<!--  end card  -->
					</div>
					<!-- end col-md-12 -->
				</div>
				<!-- end row -->
				<div class="modal bd-example-modal-xl" id="rows_information" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-xl"  style="transform: translateY(0%);">
						<div class="modal-content bg-dark">
							<div class="modal-header justify-content-center">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
									<i class="tim-icons icon-simple-remove"></i>
								</button>
								<h6 class="title title-up"><?php print_r($GLOBALS['user_language']=="en" ? "Loading":"بارگذاری"); ?> ...</h6>
							</div>
							<div class="modal-body">
								<i class="fas fa-spin fa-loading">
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-danger" data-dismiss="modal"><?php print_r($GLOBALS['user_language']=="en" ? "Cancel":"لغو"); ?></button>
							</div>
						</div>
					</div>
				</div>
				<?php
					require_once("js/table_view.php");
				?>
			<?php
		}
	}else{
		echo $outofpermission;
	}
?>
<?php
	if(!isset($_GET['name'])){
		$_GET["name"]="table_config";
	}
	$table_name=$sub_name.$_GET['name'];
	$res_table_id=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE current_name='".$table_name."' AND created=1");
	if($res_table_id->rowCount()!=0){
		$table_get=$res_table_id->fetch();
		$table_id=$table_get['id'];
		$table_col_sql="SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_id."' AND created=1 AND (visible=1 AND visible_table=1 OR '".$op_admin."'=1) ORDER BY column_number ASC";
		if(isset($_GET['action'])){
			switch($_GET['action']){
				case "rows_information":
					function fullInfoError(){
						return '<div class="modal-header justify-content-center"> <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> <i class="tim-icons icon-simple-remove"></i> </button> <h6 class="title title-up text-white">Not Loaded</h6></div><div class="modal-body text-center"> <i class="fas fa-exclamation-triangle display-1 text-white"></i></div><div class="modal-footer"> <button type="button" class="btn btn-danger" data-dismiss="modal">'.($GLOBALS['user_language']=="en" ? "Close":"خروج").'</button></div>';
					}
					if(isset($_GET['id']) && $table_get['visible']==1 || isset($_GET['id']) && isset($op_admin) && $op_admin){
						if(checkPermission(1,$table_id,"read",$table_get['act'],"")){
							$res_dataGet=$connection->query("SELECT * FROM ".$table_name." WHERE id='".$_GET['id']."'");
							if($res_dataGet->rowCount()!=0){
								$dataGet=$res_dataGet->fetch();
								$res_primary=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_get['id']."' AND primarys=1 ORDER BY id DESC");
								if($res_primary->rowCount()!=0){
									$primary=$res_primary->fetch();
									if(isset($op_admin) && $op_admin || $primary['visible']==1){
										if(checkPermission(2,$primary['id'],"read",$primary['act'],$table_id)==1){
											$primary=preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $dataGet[$primary['current_name']], 1);
										}else{
											$primary="";
										}
									}else{
										$primary="";
									}
								}else{
									$primary="";
								}
								require_once("table/table_full_info.php");
							}else{
								print_r(fullInfoError());
							}
						}else{
							print_r(fullInfoError());
						}
					}else{
						print_r(fullInfoError());
					}
				break;
				case "selected_columns":
					function fullInfoError(){
						return '<div class="modal-header justify-content-center"> <button type="button" class="close" data-dismiss="modal" aria-hidden="true"> <i class="tim-icons icon-simple-remove"></i> </button> <h6 class="title title-up text-white">Not Loaded</h6></div><div class="modal-body text-center"> <i class="fas fa-exclamation-triangle display-1 text-white"></i></div><div class="modal-footer"> <button type="button" class="btn btn-danger" data-dismiss="modal">'.($GLOBALS['user_language']=="en" ? "Close":"خروج").'</button></div>';
					}
					if(isset($_GET['id']) && $table_get['visible']==1 || isset($_GET['id']) && isset($op_admin) && $op_admin){
						if(checkPermission(1,$table_id,"read",$table_get['act'],"")){
							$res_dataGet=$connection->query("SELECT * FROM ".$table_name." WHERE id IN (".str_replace("_",",",$_GET['id']).")");
							if($res_dataGet->rowCount()!=0){
								require_once("table/show_selected_columns.php");
							}else{
								print_r(fullInfoError());
							}
						}else{
							print_r(fullInfoError());
						}
					}else{
						print_r(fullInfoError());
					}
				break;
				case "edit":
					if(isset($_GET['id']) && $table_get['editable']==1 || isset($_GET['id']) && isset($op_admin) && $op_admin){
						if(checkPermission(1,$table_id,"update",$table_get['act'],"")){
							$res_dataGet=$connection->query("SELECT * FROM ".$table_name." WHERE id IN (".str_replace("_",",",$_GET['id']).")");
							if($res_dataGet->rowCount()!=0){
								require_once("table/table_edit.php");
							}else{
								?>
									<div class="alert alert-danger">
										<button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
											<i class="tim-icons icon-simple-remove"></i>
										</button>
										<span>
											<b class="data-text" data-text-en=" Data not found ! " data-text-fa=" داده یافت نشد ! "> <?php print_r($GLOBALS['user_language']=="en" ? "Data not found !":"داده یافت نشد !"); ?> </b>
										</span>
									</div>
								<?php
							}
						}else{
							echo $outofpermission;
						}
					}else{
						echo $outofpermission;
					}
				break;
				case "new":
					if($table_get['creatable']==1 || isset($op_admin) && $op_admin){
						if(checkPermission(1,$table_id,"create",$table_get['act'],"")){
							require_once("table/table_new.php");
						}else{
							echo $outofpermission;
						}
					}else{
						echo $outofpermission;
					}
				break;
			}
		}else if(!isset($_GET['action'])){
			require_once("table/table_view.php");
		}
	}else{
		?>
			<div class="alert alert-danger">
				<button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
					<i class="tim-icons icon-simple-remove"></i>
				</button>
				<span>
					<b class="data-text" data-text-en=" Table not found ! " data-text-fa=" جدول یافت نشد ! "> <?php print_r($GLOBALS['user_language']=="en" ? "Table not found !":"جدول یافت نشد !"); ?> </b>
				</span>
			</div>
		<?php
	}
?>
<?php
	$conn_dir="../connection/connect.php";
	if(session_status() == PHP_SESSION_NONE) {
		session_start(['cookie_lifetime' => 86400]);
	}
	require_once("config.php");
	require_once("setting/check_database.php");
	require_once("setting/language.php");
	if(isset($connected) && $connected == 1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables)) >= count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user = $connection->query("SELECT * FROM " . $sub_name . "admins WHERE username='" . $_SESSION['username'] . "' AND act=1");
			$user_stats = $res_user->rowCount();
			if($user_stats == 1 && (checkPermission(1,getTableByName($sub_name."file_manager")['id'],"create",getTableByName($sub_name."file_manager")['act'],"") && checkPermission(1,getTableByName($sub_name."file_manager")['id'],"read",getTableByName($sub_name."file_manager")['act'],"") && checkPermission(1,getTableByName($sub_name."file_manager")['id'],"update",getTableByName($sub_name."file_manager")['act'],"") && checkPermission(1,getTableByName($sub_name."file_manager")['id'],"delete",getTableByName($sub_name."file_manager")['act'],"")) || isset($op_admin) && $op_admin){
				if(isset($_GET["download"])){
					$res_download=$connection->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$_GET["download"]."'");
					if($res_download->rowCount()){
						$download=$res_download->fetch();
						$is_secure=($download['is_secure'] ? "secure_files":"files");
						$file_url = "../".$is_secure."/".$download['real_name'];
						if (file_exists($file_url)) {
							header('Content-Type: application/octet-stream');
							header("Content-Transfer-Encoding: Binary");
							header("Content-disposition: attachment; filename=\"" . basename($download['display_name']) . "\"");
							readfile($file_url);
						}else{
							echo "404 not found !";
						}
					}else{
						echo "404 not found !";
					}
				}else{
					$file_manager_direction_id=(getUserSetting("file_manager_direction")=="false" ? 0:getUserSetting("file_manager_direction"));
					$file_manager_direction_id=($connection->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$file_manager_direction_id."'")->rowCount() ? $file_manager_direction_id:0);
					$file_manager_copy=(getUserSetting("file_manager_copy")=="false" ? 0:getUserSetting("file_manager_copy"));
					$file_manager_cut=(getUserSetting("file_manager_cut")=="false" ? 0:getUserSetting("file_manager_cut"));
					$current_folder=$connection->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE folder_file=0 AND id='".$file_manager_direction_id."' AND act=1")->fetch();
?>
	<script>
		var $data_sync=[];
	</script>
	<div class="row">
		<div class="col-12 file_manager">
			<div class="card file_manager_card">
				<div class="card-header">
					<div class="row">
						<div class="col-12 col-sm-9 col-md-8 col-lg-9 col-xl-10 pb-2 pt-2 text-center <?php print_r($GLOBALS['user_language']=="en" ? "text-sm-left":"text-sm-right"); ?>">
							<!-- <div class="btn-group">
								<button class="btn btn-icon btn-simple btn-github custom-text-dark">
									<i class="far fa-home"></i>
								</button>
								<button class="btn btn-icon btn-simple btn-github custom-text-dark">
									<i class="far fa-server"></i>
								</button>
								<button class="btn btn-icon btn-simple btn-github custom-text-dark">
									<i class="far fa-trash-alt"></i>
								</button>
							</div> -->
							<div class="btn-group">
								<button class="btn btn-icon btn-simple btn-github custom-text-dark" onclick='openFolderFileManager($(".menu_file_manager_folder_0").children("a"),0);'>
									<i class="far fa-home"></i>
								</button>
								<button class="btn btn-icon btn-simple btn-github custom-text-dark" onclick='createFolder();'>
									<i class="far fa-folder-plus"></i>
								</button>
								<button class="btn btn-icon btn-simple btn-github custom-text-dark" onclick='fileUploader();'>
									<i class="fas fa-upload"></i>
								</button>
							</div>
							<div class="btn-group">
								<button class="btn btn-icon btn-simple btn-github custom-text-dark jump-up-filemanager" <?php if($file_manager_direction_id==0){?>disabled<?php } ?> onclick="getUpFileManager();">
									<i class="fas fa-level-up-alt"></i>
								</button>
								<button class="btn btn-icon btn-simple btn-github custom-text-dark data-text go-back-filemanager" disabled onclick='getBackFileManager();' data-text-en='<i class="fas fa-long-arrow-left"></i>' data-text-fa='<i class="fas fa-long-arrow-right"></i>'>
									<?php print_r($GLOBALS['user_language']=="en" ? '<i class="fas fa-long-arrow-left"></i>':'<i class="fas fa-long-arrow-right"></i>'); ?>
								</button>
								<button class="btn btn-icon btn-simple btn-github custom-text-dark data-text go-next-filemanager" disabled onclick='getNextFileManager();' data-text-en='<i class="fas fa-long-arrow-right"></i>' data-text-fa='<i class="fas fa-long-arrow-left"></i>'>
								<?php print_r($GLOBALS['user_language']=="en" ? '<i class="fas fa-long-arrow-right"></i>':'<i class="fas fa-long-arrow-left"></i>'); ?>
								</button>
							</div>
							<div class="btn-group">
								<button class="btn btn-icon btn-simple btn-github custom-text-dark" onclick='$(".selectable_item").addClass("ui-selected");'>
									<i class="fas fa-check-square"></i>
								</button>
								<button class="btn btn-icon btn-simple btn-github custom-text-dark" onclick='$(".selectable_item").removeClass("ui-selected");'>
									<i class="fas fa-square"></i>
								</button>
								<button class="btn btn-icon btn-simple btn-github custom-text-dark" onclick='openFolderFileManager($(".menu_file_manager_folder_"+$current_folder).children("a"),$current_folder,1);'>
									<i class="fas fa-sync-alt"></i>
								</button>
							</div>
							<div class="btn-group">
								<button class="btn btn-icon btn-simple btn-github custom-text-dark" onclick='refreshFileManagerMenu(0,0,"slideDown");'>
									<i class="fas fa-folder-open"></i>
								</button>
								<button class="btn btn-icon btn-simple btn-github custom-text-dark" onclick='refreshFileManagerMenu(0,0,"slideUp");'>
									<i class="fas fa-folder"></i>
								</button>
							</div>
						</div>
						<div class="col-12 col-sm-3 col-md-4 col-lg-3 col-xl-2 pb-2 pt-2 text-center <?php print_r($GLOBALS['user_language']=="en" ? "text-sm-right":"text-sm-left"); ?>">
							<div class="input-group mb-0">
								<div class="input-group-prepend">
									<div class="input-group-text file_manager_custom_input">
										<i class="fad fa-file-search"></i>
									</div>
								</div>
								<input type="search" autocomplete="off" name="search" class="form-control file_manager_custom_input data-placeholder file_manager_search_input" data-placeholder-en="Search here ..." data-placeholder-fa="جست و جو ..." placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Search here ...":"جست و جو ..."); ?>">
							</div>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row menu_place">
						<div id="jquery-accordion-menu" class="jquery-accordion-menu col-12 col-sm-6 col-md-5 col-lg-4 col-xl-3">
							<ul>
								<li class="<?php print_r($file_manager_direction_id=="0" || $file_manager_direction_id=="false" ? "active":""); ?> files_nf menu_file_manager_folder_0"><a href="javascript:void(0)" onclick="openFolderFileManager($(this),0);"><i class='far fa-hdd'></i> <?php print_r($GLOBALS['user_language']=="en" ? "Files":"فایل ها"); ?></a>
									<ul class="submenu files_nf" style="display: block;">
										<?php
											$mustBeOpen=[];
											$current_folder_sql="SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE folder_file=0 AND id='".$file_manager_direction_id."' AND act=1";
											$gotUserSetting_fileId=($file_manager_direction_id!="false" && $GLOBALS["connection"]->query($current_folder_sql)->rowCount() ? $GLOBALS["connection"]->query($current_folder_sql)->fetch()['level']:1);
											for($i=$gotUserSetting_fileId;$i>=1;$i--){
												$res_files_opened=$GLOBALS["connection"]->query($current_folder_sql);
												if($res_files_opened->rowCount()){
													$files_opened=$res_files_opened->fetch();
													$mustBeOpen[count($mustBeOpen)]=$files_opened['id'];
													$current_folder_sql="SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE folder_file=0 AND id='".$files_opened['parent_id']."' AND act=1";
												}
											}
											function getFolderMenu($level=1,$parent_id="1"){
												$res_files=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE folder_file=0 AND level='".$level."' AND $parent_id AND act=1 ORDER BY folder_file ASC");
												if($res_files->rowCount()!=0){
													if($level>1){
														$isActive=($GLOBALS['file_manager_direction_id']==str_replace("parent_id=","",$parent_id) ? 1:(in_array(str_replace("parent_id=","",$parent_id), $GLOBALS['mustBeOpen'])));
														?><ul class="submenu" style="display: <?php print_r($isActive==1 ? "block":"none"); ?>;"><?php
													}
													while ($files=$res_files->fetch()) {
														$isActive=($GLOBALS['file_manager_direction_id']==$files['id'] ? 1:(in_array($files['id'], $GLOBALS['mustBeOpen'])));
														?>
															<li class="<?php print_r($GLOBALS['file_manager_direction_id']==$files['id'] ? "active":""); ?> menu_file_manager_folder_<?php print_r($files['id']); ?>"><a href="javascript:void(0)" <?php if($files['folder_file']==0){?>onclick="openFolderFileManager($(this),<?php print_r($files['id']); ?>);"<?php } ?> class="<?php print_r($isActive==1 ? "submenu-indicator-minus":""); ?>"><i class="far fa-<?php print_r($files['folder_file']==0 ? ($files['fa_icon']!="" ? $files['fa_icon']:($isActive==1 ? "folder-open":"folder")):($files['fa_icon']!="" ? $files['fa_icon']:"file")); ?>"></i><?php print_r($files['display_name']); ?> </a>
																<?php
																	getFolderMenu(($level+1),'parent_id='.$files['id']);
																?>
															</li>
														<?php
													}
													if($level>1){
														?></ul><?php
													}
												}
											}
											getFolderMenu();
										?>
										<li class="files_nf <?php print_r($file_manager_direction_id=="-1" ? "active":""); ?> menu_file_manager_folder_-1"><a href="javascript:void(0)" onclick="openFolderFileManager($(this),-1);" class="data-text" data-text-en="<i class='far fa-server'></i>  Database Upload Center" data-text-fa="<i class='far fa-server'></i>  مرکز آپلود پایگاه داده ها"><i class='far fa-server'></i> <?php print_r($GLOBALS['user_language']=="en" ? "Database Upload Center":"مرکز آپلود پایگاه داده ها"); ?> </a>
										<!-- <li class="files_nf <?php print_r($file_manager_direction_id=="-2" ? "active":""); ?> menu_file_manager_folder_-2"><a href="javascript:void(0)" onclick="openFolderFileManager($(this),-2);" class="data-text" data-text-en="<i class='far fa-trash-alt'></i> Trash" data-text-fa="<i class='far fa-trash-alt'></i> زباله ها"><i class='far fa-trash-alt'></i> <?php print_r($GLOBALS['user_language']=="en" ? "Trash":"زباله ها"); ?></a> -->
									</ul>
								</li>
							</ul>
						</div>
						<div class="col-12 col-sm-6 col-md-7 col-lg-8 col-xl-9">
							<div class="card-header pl-1 pr-1">
								<div class="row">
									<div class="col-xl-10 col-lg-10 col-md-9 col-sm-7 col-8">
										<div class="input-group">
											<div class="input-group-prepend">
												<div class="input-group-text file_manager_custom_input">
													<i class="fad fa-folder-open"></i>
												</div>
											</div>
											<?php
												$file_manager_direction="";
												$current_folder_sql="SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE folder_file=0 AND id='".$file_manager_direction_id."' AND act=1";
												$gotUserSetting_fileId=($file_manager_direction_id!="false" && $GLOBALS["connection"]->query($current_folder_sql)->rowCount() ? $GLOBALS["connection"]->query($current_folder_sql)->fetch()['level']:1);
												for($i=$gotUserSetting_fileId;$i>=1;$i--){
													$res_files_opened=$GLOBALS["connection"]->query($current_folder_sql);
													if($res_files_opened->rowCount()){
														$files_opened=$res_files_opened->fetch();
														$file_manager_direction=$files_opened['display_name']."/".$file_manager_direction;
														$current_folder_sql="SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE folder_file=0 AND id='".$files_opened['parent_id']."' AND act=1";
													}
												}
												if($file_manager_direction_id<0){
													switch ($file_manager_direction_id) {
														case -1:
														case "-1":
															$file_manager_direction=($GLOBALS['user_language']=="en" ? "Files":"فایل ها")."/".($GLOBALS['user_language']=="en" ? "Database Upload Center":"مرکز آپلود پایگاه داده ها")."/";
														break;
														case -2:
														case "-2":
															$file_manager_direction=($GLOBALS['user_language']=="en" ? "Files":"فایل ها")."/".($GLOBALS['user_language']=="en" ? "Trash":"زباله ها")."/";
														break;
													}
												}
												if($file_manager_direction_id==-3){
													$file_manager_direction=($GLOBALS['user_language']=="en" ? "Searching":"در حال جست و جو");
												}else{
													$file_manager_direction=($file_manager_direction_id >= 0 ? $file_manager_direction:($file_manager_direction_id==-2 ? ($GLOBALS['user_language']=="en" ? "Trash":"زباله ها"):($GLOBALS['user_language']=="en" ? "Database Upload Center":"مرکز آپلود پایگاه داده ها"))."/");
												}
											?>
											<input type="text" disabled class="form-control file_manager_custom_input file_manager_custom_input_address data-value" data-value-en="Files" data-value-fa="فایل ها" value="<?php print_r($GLOBALS['user_language']=="en" ? "Files":"فایل ها"); ?>/<?php print_r($file_manager_direction); ?>">
										</div>
									</div>
									<div class="col-xl-2 col-lg-2 col-md-3 col-sm-5 col-4">
										<div class="btn-group">
											<button class="btn btn-icon btn-simple btn-github custom-text-dark mt-0 list_type_item <?php if(getUserSetting("file_manager_mode")=="item"){ ?>btn-primary<?php } ?>" onclick='switchFileView("item");$(this).attr("disabled",true);'>
												<i class="fas fa-th"></i>
											</button>
											<button class="btn btn-icon btn-simple btn-github custom-text-dark mt-0 list_type_list <?php if(getUserSetting("file_manager_mode")=="list"){ ?>btn-primary<?php } ?>" onclick='switchFileView("list");$(this).attr("disabled",true);'>
												<i class="fas fa-list"></i>
											</button>
										</div>
									</div>
								</div>
							</div>
							<div class="card-body folders_loader">
								<?php if(getUserSetting("file_manager_mode")=="item"){ ?>
									<script>var $list_type_mode="item";</script>
								<?php }else{?>
									<script>var $list_type_mode="list";</script>
								<?php }?>
								<script>
									$(document).ready(function(){
										openFolderFileManager($(".menu_file_manager_folder_<?php print_r($file_manager_direction_id!="false" ? $file_manager_direction_id:0); ?>").children("a"),'<?php print_r($file_manager_direction_id!="false" ? $file_manager_direction_id:0); ?>');
									});
								</script>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-center">
					<span class='free-space data-text' data-text-en="<?php print_r(sizetostring(disk_free_space("../"))); ?> free" data-text-fa="<?php print_r(sizetostring(disk_free_space("../"))); ?> خالی"><?php print_r(sizetostring(disk_free_space("../"))); ?> <?php print_r($GLOBALS['user_language']=="en" ? "free":"خالی"); ?></span>
				</div>
			</div>
		</div>
	</div>
	<?php
		require_once("file_manager/js/js.php");
	?>
<?php
				}
			}else{
				echo $outofpermission;
			}
		}else{
?>
	<script>
		window.location="login/";
	</script>
<?php
		}
	}else{
?>
	<script>
		window.location="setup/";
	</script>
<?php
	}
?>

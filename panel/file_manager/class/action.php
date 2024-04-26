<?php
    if(session_status() == PHP_SESSION_NONE) {
        session_start(['cookie_lifetime' => 86400]);
    }
	require_once("../../config.php");
    $conn_dir="../../../connection/connect.php";
	if(file_exists($conn_dir)){
		require_once($conn_dir);
		$connection_checker=new connection();
		$connection_check=$connection_checker->checkConnection();
		if($connection_check==0){
			echo "redirect_._setup/";
		}else{
			$connection=$connection_checker->connect();
			$connected=1;
		}
	}else{
		echo "redirect_._setup/";
	}
	if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_SESSION['username']."' AND act=1");
			$user_stats=$res_user->rowCount();
			if($user_stats==1 && (checkPermission(1,getTableByName($sub_name."file_manager")['id'],"create",getTableByName($sub_name."file_manager")['act'],"") && checkPermission(1,getTableByName($sub_name."file_manager")['id'],"read",getTableByName($sub_name."file_manager")['act'],"") && checkPermission(1,getTableByName($sub_name."file_manager")['id'],"update",getTableByName($sub_name."file_manager")['act'],"") && checkPermission(1,getTableByName($sub_name."file_manager")['id'],"delete",getTableByName($sub_name."file_manager")['act'],"")) || getSetting("op_admin")==$_SESSION['username']){
				if(isset($_GET["load"]) && isset($_POST["folder_id"])){
					$file_manager_mode=getUserSetting("file_manager_mode");
					if($_POST["folder_id"]==-3){
						$res_search=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE display_name LIKE '%".getUserSetting("file_manager_search")."%' OR display_name LIKE '%".getUserSetting("file_manager_search")."' OR display_name LIKE '".getUserSetting("file_manager_search")."%'");
						while ($search=$res_search->fetch()) {
							if($file_manager_mode=="item"){
								?>
									<div data-filemanagerid="<?php print_r($search['id']); ?>" class="selectable_item its_folder font-icon-list col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 file_manager_id-<?php print_r($search['id']); ?>">
										<div data-filemanagerid="<?php print_r($search['id']); ?>" class="its_folder font-icon-detail go_pr_1 go_pr">
											<i data-filemanagerid="<?php print_r($search['id']); ?>" class="its_folder far <?php print_r($search['fa_icon']!="" ? $search['fa_icon']:"fa-file"); ?> go_pr_2 go_pr"></i>
											<p data-filemanagerid="<?php print_r($search['id']); ?>" class="its_folder go_pr_2 go_pr"><?php print_r($search['display_name']); ?></p>
										</div>
										<div data-filemanagerid="<?php print_r($search['id']); ?>" class="more_tools hide"><div class="file_size">
											<?php print_r(sizetostring($search['file_size'])); ?>
										</div><div class="last_modify">
											<?php print_r(timetostr($search['last_modify'])); ?>
										</div></div>
									</div>
								<?php
							}elseif($file_manager_mode=="list"){
								?>
									<tr data-filemanagerid="<?php print_r($search['id']); ?>" class="selectable_item its_folder file_manager_id-<?php print_r($search['id']); ?>">
										<th data-filemanagerid="<?php print_r($search['id']); ?>" class="its_folder go_pr_1 go_pr" scope="row"><i data-filemanagerid="<?php print_r($search['id']); ?>" class='far <?php print_r($search['fa_icon']!="" ? $search['fa_icon']:"fa-file"); ?> its_folder go_pr_2 go_pr'></i> <?php print_r($search['display_name']); ?></th>
										<td data-filemanagerid="<?php print_r($search['id']); ?>" class="its_folder go_pr_1 go_pr">
											<?php print_r(sizetostring($search['file_size'])); ?>
										</td>
										<td data-filemanagerid="<?php print_r($search['id']); ?>" class="its_folder go_pr_1 go_pr <?php print_r($GLOBALS['user_language']=="en" ? "text-right":"text-left"); ?>">
											<?php print_r(timetostr($search['last_modify'])); ?>
										</td>
									</tr>
								<?php
							}
						}
						?><!--success--><?php
					}else{
						$current_folder=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$_POST["folder_id"]."' AND act=1 ORDER BY folder_file ASC")->fetch();
						$level=($current_folder ? $current_folder['level']+1:($_POST["folder_id"]==0 ? 1:2));
						$parent_id=($_POST["folder_id"] ? "parent_id=".$_POST["folder_id"]:1);
						$res_files=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE level='".$level."' AND $parent_id AND act=1 ORDER BY folder_file ASC");
						if($res_files->rowCount()!=0){
							while ($files=$res_files->fetch()) {
								if($file_manager_mode=="item"){
									?>
										<div data-filemanagerid="<?php print_r($files['id']); ?>" class="selectable_item <?php print_r($files['folder_file']==0 ? "its_folder":"its_file"); ?> font-icon-list col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 file_manager_id-<?php print_r($files['id']);?>">
											<div data-filemanagerid="<?php print_r($files['id']); ?>" class="<?php print_r($files['folder_file']==0 ? "its_folder":"its_file"); ?> font-icon-detail go_pr_1 go_pr">
												<i data-filemanagerid="<?php print_r($files['id']); ?>" class="<?php print_r($files['folder_file']==0 ? "its_folder":"its_file"); ?> far fa-<?php print_r($files['fa_icon']!="" ? $files['fa_icon']:($files['folder_file']==0 ? "folder":"file")); ?> go_pr_2 go_pr"></i>
												<p data-filemanagerid="<?php print_r($files['id']); ?>" class="<?php print_r($files['folder_file']==0 ? "its_folder":"its_file"); ?> go_pr_2 go_pr"><?php print_r($files['display_name']); ?></p>
											</div>
											<div class="more_tools hide"><div class="file_size"><?php print_r(sizeToString($files['file_size'])); ?></div><div class="last_modify"><?php print_r(timetostr($files['last_modify'])); ?></div></div>
										</div>
									<?php
								}elseif($file_manager_mode=="list"){
									?>
										<tr data-filemanagerid="<?php print_r($files['id']); ?>" class="selectable_item <?php print_r($files['folder_file']==0 ? "its_folder":"its_file"); ?> file_manager_id-<?php print_r($files['id']);?>">
											<th data-filemanagerid="<?php print_r($files['id']); ?>" class="<?php print_r($files['folder_file']==0 ? "its_folder":"its_file"); ?> go_pr_1 go_pr" scope="row">
												<i data-filemanagerid="<?php print_r($files['id']); ?>" class="far fa-<?php print_r($files['fa_icon']!="" ? $files['fa_icon']:($files['folder_file']==0 ? "folder":"file")); ?> <?php print_r($files['folder_file']==0 ? "its_folder":"its_file"); ?> go_pr_2 go_pr"></i> <?php print_r($files['display_name']); ?>
											</th>
											<td data-filemanagerid="<?php print_r($files['id']); ?>" class="<?php print_r($files['folder_file']==0 ? "its_folder":"its_file"); ?> go_pr_1 go_pr"><?php print_r(sizeToString($files['file_size'])); ?></td>
											<td data-filemanagerid="<?php print_r($files['id']); ?>" class="<?php print_r($files['folder_file']==0 ? "its_folder":"its_file"); ?> go_pr_1 go_pr <?php print_r($GLOBALS['user_language']=="en" ? "text-right":"text-left"); ?>"><?php print_r(timetostr($files['last_modify'])); ?></td>
										</tr>
									<?php
								}
							}
						}
						if($_POST["folder_id"]==0){
							if($file_manager_mode=="item"){
								?>
									<div data-filemanagerid="-1" class="selectable_item its_folder font-icon-list col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 file_manager_id-_1">
										<div data-filemanagerid="-1" class="its_folder font-icon-detail go_pr_1 go_pr">
											<i data-filemanagerid="-1" class="its_folder far fa-server go_pr_2 go_pr"></i>
											<p data-filemanagerid="-1" class="its_folder go_pr_2 go_pr data-text" data-text-en="Database Upload Center" data-text-fa="مرکز آپلود پایگاه داده ها"><?php print_r($GLOBALS['user_language']=="en" ? "Database Upload Center":"مرکز آپلود پایگاه داده ها"); ?></p>
										</div>
										<div data-filemanagerid="-1" class="more_tools hide"><div class="file_size">
											<?php
												$sizeofthis_1=$connection->query("SELECT SUM(file_size) AS TOTAL FROM ".$sub_name."file_manager WHERE parent_id='-1' ORDER BY ordering DESC")->fetch(PDO::FETCH_ASSOC)['TOTAL'];
												print_r(sizetostring($sizeofthis_1 ? $sizeofthis_1:0));
											?>
										</div><div class="last_modify">
											<?php
												$lasttimeofthis_1=$connection->query("SELECT * FROM ".$sub_name."file_manager WHERE parent_id='-1' ORDER BY last_modify DESC");
												print_r(timetostr($lasttimeofthis_1->rowCount() ? $lasttimeofthis_1->fetch()['last_modify']:-1));
											?>
										</div></div>
									</div>
									<!-- <div data-filemanagerid="-2" class="selectable_item its_folder font-icon-list col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 file_manager_id-_2">
										<div data-filemanagerid="-2" class="its_folder font-icon-detail go_pr_1 go_pr">
											<i data-filemanagerid="-2" class="its_folder far fa-trash-alt go_pr_2 go_pr"></i>
											<p data-filemanagerid="-2" class="its_folder go_pr_2 go_pr data-text" data-text-en="Trash" data-text-fa="زباله ها"><?php print_r($GLOBALS['user_language']=="en" ? "Trash":"زباله ها"); ?></p>
										</div>
										<div data-filemanagerid="-2" class="more_tools hide"><div class="file_size">
											<?php
												$sizeofthis_2=$connection->query("SELECT SUM(file_size) AS TOTAL FROM ".$sub_name."file_manager WHERE parent_id='-2' ORDER BY ordering DESC")->fetch(PDO::FETCH_ASSOC)['TOTAL'];
												print_r(sizetostring($sizeofthis_2 ? $sizeofthis_2:0));
											?>
										</div><div class="last_modify">
											<?php
												$lasttimeofthis_2=$connection->query("SELECT * FROM ".$sub_name."file_manager WHERE parent_id='-2' ORDER BY last_modify DESC");
												print_r(timetostr($lasttimeofthis_2->rowCount() ? $lasttimeofthis_2->fetch()['last_modify']:-1));
											?>
										</div></div>
									</div> -->
								<?php
							}elseif($file_manager_mode=="list"){
								?>
									<tr data-filemanagerid="-1" class="selectable_item its_folder file_manager_id-_1">
										<th data-filemanagerid="-1" class="its_folder go_pr_1 go_pr data-text" scope="row" data-text-en="<i data-filemanagerid='-1' class='far fa-server its_folder go_pr_2 go_pr'></i> Database Upload Center" data-text-fa="<i data-filemanagerid='-1' class='far fa-server its_folder go_pr_2 go_pr'></i> مرکز آپلود پایگاه داده ها"><i data-filemanagerid='-1' class='far fa-server its_folder go_pr_2 go_pr'></i> <?php print_r($GLOBALS['user_language']=="en" ? "Database Upload Center":"مرکز آپلود پایگاه داده ها"); ?></th>
										<td data-filemanagerid="-1" class="its_folder go_pr_1 go_pr">
											<?php
												$sizeofthis_1=$connection->query("SELECT SUM(file_size) AS TOTAL FROM ".$sub_name."file_manager WHERE parent_id='-1' ORDER BY ordering DESC")->fetch(PDO::FETCH_ASSOC)['TOTAL'];
												print_r(sizetostring($sizeofthis_1 ? $sizeofthis_1:0));
											?>
										</td>
										<td data-filemanagerid="-1" class="its_folder go_pr_1 go_pr <?php print_r($GLOBALS['user_language']=="en" ? "text-right":"text-left"); ?>">
											<?php
												$lasttimeofthis_1=$connection->query("SELECT * FROM ".$sub_name."file_manager WHERE parent_id='-1' ORDER BY last_modify DESC");
												print_r(timetostr($lasttimeofthis_1->rowCount() ? $lasttimeofthis_1->fetch()['last_modify']:-1));
											?>
										</td>
									</tr>
									<!-- <tr data-filemanagerid="-2" class="selectable_item its_folder file_manager_id-_2">
										<th data-filemanagerid="-2" class="its_folder go_pr_1 go_pr data-text" scope="row" data-text-en="<i data-filemanagerid='-2' class='far fa-trash-alt its_folder go_pr_2 go_pr'></i> Trash" data-text-fa="<i data-filemanagerid='-2' class='far fa-trash-alt its_folder go_pr_2 go_pr'></i> زباله ها"><i data-filemanagerid='-2' class='far fa-trash-alt its_folder go_pr_2 go_pr'></i> <?php print_r($GLOBALS['user_language']=="en" ? "Trash":"زباله ها"); ?></th>
										<td data-filemanagerid="-2" class="its_folder go_pr_1 go_pr">
											<?php
												$sizeofthis_2=$connection->query("SELECT SUM(file_size) AS TOTAL FROM ".$sub_name."file_manager WHERE parent_id='-2' ORDER BY ordering DESC")->fetch(PDO::FETCH_ASSOC)['TOTAL'];
												print_r(sizetostring($sizeofthis_2 ? $sizeofthis_2:0));
											?>
										</td>
										<td data-filemanagerid="-2" class="its_folder go_pr_1 go_pr <?php print_r($GLOBALS['user_language']=="en" ? "text-right":"text-left"); ?>">
											<?php
												$lasttimeofthis_2=$connection->query("SELECT * FROM ".$sub_name."file_manager WHERE parent_id='-2' ORDER BY last_modify DESC");
												print_r(timetostr($lasttimeofthis_2->rowCount() ? $lasttimeofthis_2->fetch()['last_modify']:-1));
											?>
										</td>
									</tr> -->
								<?php
							}
						}
						?><!--success--><?php
					}
				}
				if(isset($_GET["getDataInfo"]) && isset($_POST["folder_id"])){
					$current_folder=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$_POST["folder_id"]."' AND act=1 ORDER BY folder_file ASC")->fetch();
					$level=($current_folder ? $current_folder['level']+1:($_POST["folder_id"]==0 ? 1:2));
					$parent_id=($_POST["folder_id"] ? "parent_id=".$_POST["folder_id"]:1);
					$res_files=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE level='".$level."' AND $parent_id AND act=1 ORDER BY folder_file ASC");
					$data_class='';
					if($res_files->rowCount()!=0){
						while ($files=$res_files->fetch()) {
							$data_class.=$files['id'].$files['display_name'].$files['fa_icon'].$files['file_size'].$files['last_modify']."_-...-_";
						}
					}
					echo $data_class."success";
				}
				if(isset($_GET["fileManagerMenu"])){
					$file_manager_direction_id=getUserSetting("file_manager_direction");
					?>
						<ul>
							<li class="<?php print_r($file_manager_direction_id=="0" || $file_manager_direction_id=="false" ? "active":""); ?> files_nf menu_file_manager_folder_0"><a href="javascript:void(0)" onclick="openFolderFileManager($(this),0);"><i class='far fa-hdd'></i> <?php print_r($GLOBALS['user_language']=="en" ? "Files":"فایل ها"); ?></a>
								<ul class="submenu files_nf" style="display: block;">
									<?php
										$mustBeOpen=[];
										$current_folder_sql="SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE folder_file=0 AND id='".$file_manager_direction_id."' AND act=1";
										$gotUserSetting_fileId=($file_manager_direction_id!="false" ? ($GLOBALS["connection"]->query($current_folder_sql)->rowCount() ? $GLOBALS["connection"]->query($current_folder_sql)->fetch()['level']:1):1);
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
									<li class="files_nf <?php print_r(getUserSetting("file_manager_direction")=="-1" ? "active":""); ?> menu_file_manager_folder_-1"><a href="javascript:void(0)" onclick="openFolderFileManager($(this),-1);" class="data-text" data-text-en="<i class='far fa-server'></i> Database Upload Center" data-text-fa="<i class='far fa-server'></i> مرکز آپلود پایگاه داده ها"><i class='far fa-server'></i> <?php print_r($GLOBALS['user_language']=="en" ? "Database Upload Center":"مرکز آپلود پایگاه داده ها"); ?> </a>
									<!-- <li class="files_nf <?php print_r(getUserSetting("file_manager_direction")=="-2" ? "active":""); ?> menu_file_manager_folder_-2"><a href="javascript:void(0)" onclick="openFolderFileManager($(this),-2);" class="data-text" data-text-en="<i class='far fa-trash-alt'></i> Trash" data-text-fa="<i class='far fa-trash-alt'></i> زباله ها"><i class='far fa-trash-alt'></i> <?php print_r($GLOBALS['user_language']=="en" ? "Trash":"زباله ها"); ?></a> -->
								</ul>
							</li>
						</ul><!--success-->
					<?php
				}
				if(isset($_GET["countFolder"])){
					echo $res_files=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE folder_file=0 AND act=1")->rowCount()."_-...-_success";
				}
				if(isset($_GET["jump-up"]) && isset($_POST["folder_id"])){
					$parent_id=($GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$_POST["folder_id"]."' AND act=1")->rowCount() ? $GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$_POST["folder_id"]."' AND act=1")->fetch()['parent_id']:0);
					echo ($parent_id ? $GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$parent_id."' AND act=1")->fetch()['id']:0)."_-...-_success";
				}
				if(isset($_GET["create_folder"]) && isset($_POST["folder_id"]) && isset($_POST["name"])){
					$exist=/*$connection->query("SELECT * FROM ".$sub_name."file_manager WHERE parent_id='".$_POST["folder_id"]."' AND display_name='".$_POST["name"]."'")->rowCount()*/0;
					if($exist==0){
						$res_folder=$connection->query("SELECT * FROM ".$sub_name."file_manager WHERE id='".$_POST["folder_id"]."'");
						if($res_folder->rowCount() || $_POST["folder_id"]<=0){
							$folder=$res_folder->fetch();
							$level=($_POST["folder_id"]>0 ? $folder['level']+1:($_POST["folder_id"]==0 ? 1:2));
							$ordering=(getLastItemByOrdering("file_manager") ? getLastItemByOrdering("file_manager")['ordering']+1:0);
							$name=$_POST["name"];
							if($connection->query("INSERT INTO ".$sub_name."file_manager (folder_file,parent_id,level,display_name,real_name,fa_icon,file_size,is_secure,last_modify,ordering,act) VALUES (0,'".$_POST['folder_id']."','".$level."','".$name."','".$name."','',0,0,'".strtotime("now")."','".$ordering."',1)")){
								echo "success";
							}else{
								echo "error";
							}
						}else{
							echo "error";
						}
					}else{
						echo "exist_-...-_success";
					}
				}
				if(isset($_GET["upload"]) && isset($_POST["current_folder"]) && isset($_POST["is_secure"])){
					$folder=$connection->query("SELECT * FROM ".$sub_name."file_manager WHERE id='".$_POST["current_folder"]."'")->fetch();
					$level=($_POST["current_folder"]>0 ? ($folder['level']+1):($_POST["current_folder"]==0 ? 1:2));
					$files=["status"=>"success"];
					$max_file_size=((int)(ini_get('upload_max_filesize'))>=(int)(ini_get('post_max_size')) ? (int)(ini_get('upload_max_filesize')):(int)(ini_get('post_max_size')));
					$new_name=strtotime("now");
					if($_POST["is_secure"]==1){
						$target_dir = "../../../secure_files/";
					}else{
						$target_dir = "../../../files/";
					}
					function fileNameChecker_Changer(){
						if (file_exists($GLOBALS['target_file'])) {
							$GLOBALS['generated_name']=$GLOBALS['new_name']."-".generateRandomString().".".$GLOBALS['FileType'];
							$GLOBALS['target_file'] = $GLOBALS['target_dir'] . $GLOBALS['new_name']."-".generateRandomString().".".$GLOBALS['FileType'];
							fileNameChecker_Changer();
						}
					}
					function changeSize($folder_id,$type,$size){
						$res_folder=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."file_manager WHERE id='".$folder_id."'");
						if($res_folder->rowCount()){
							$folder=$res_folder->fetch();
							switch ($type) {
								case 'add':
									$sizes=$folder['file_size']+$size;
								break;
								case 'remove':
									$sizes=$folder['file_size']-$size;
								break;
							}
							$GLOBALS['connection']->query("UPDATE ".$GLOBALS['sub_name']."file_manager SET file_size='".$sizes."', last_modify='".strtotime("now")."' WHERE id='".$folder_id."'");
							changeSize($folder['parent_id'],$type,$size);
						}
					}
					foreach ($_FILES as $key => $value) {
						$upload_name=$key;
						$real_name=basename($_FILES[$upload_name]["name"]);
						$target_file = $target_dir . basename($_FILES[$upload_name]["name"]);
						$uploadOk = 1;
						$FileType = pathinfo($target_file,PATHINFO_EXTENSION);
						$generated_name=$new_name."-".generateRandomString().".".$FileType;
						$target_file = $target_dir . $generated_name;
						fileNameChecker_Changer();
						if ($_FILES[$upload_name]["size"]/1024/1024 <= $max_file_size && $_FILES[$upload_name]["size"]<=disk_free_space($target_dir)) {
							if (move_uploaded_file($_FILES[$upload_name]["tmp_name"], $target_file)) {
								$ordering=(getLastItemByOrdering("file_manager") ? getLastItemByOrdering("file_manager")['ordering']+1:0);
								if(in_array(strtolower($FileType),$audio_format)){
									$fa_icon="far fa-file-audio";
								}else if(in_array(strtolower($FileType),$video_format)){
									$fa_icon="far fa-file-video";
								}else if(in_array(strtolower($FileType),$image_formats)){
									$fa_icon="far fa-file-image";
								}else if(in_array(strtolower($FileType),$zipped_format)){
									$fa_icon="far fa-file-archive";
								}else if(in_array(strtolower($FileType),$disk_format)){
									$fa_icon="far fa-disc-drive";
								}else if(in_array(strtolower($FileType),$threeds_format)){
									$fa_icon="far fa-cube";
								}else if(in_array(strtolower($FileType),$electronic_design_format)){
									$fa_icon="far fa-microchip";
								}else if(in_array(strtolower($FileType),$database_format)){
									$fa_icon="far fa-database";
								}else if(in_array(strtolower($FileType),$document_format)){
									$fa_icon="far fa-file-alt";
								}
								$fa_icon=(isset($fa_icon) ? $fa_icon:"");
								$connection->query("INSERT INTO ".$sub_name."file_manager (folder_file,parent_id,level,display_name,real_name,fa_icon,file_size,is_secure,last_modify,ordering,act) VALUES (1,'".$_POST['current_folder']."','".$level."','".$real_name."','".$generated_name."','".$fa_icon."','".$_FILES[$upload_name]["size"]."','".$_POST['is_secure']."','".strtotime("now")."','".$ordering."',1)");
								changeSize($_POST['current_folder'],'add',$_FILES[$upload_name]["size"]);
								if($_POST["is_secure"]){
									$download_link=getSetting("admin_url")."files.php?download=1";
								}else{
									$download_link=getSetting("upload_url").$generated_name;
								}
								$files[$key]=[
									"object_id"=>$key,
									"real_name" => $real_name,
									"is_secure" => $_POST["is_secure"],
									"download_link" => $download_link
								];
							}
						}else{
							echo "error";
						}
					}
					echo json_encode($files);
				}
				if(isset($_GET["getName"]) && isset($_POST["file_id"])){
					$res_name=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$_POST["file_id"]."'");
					if($res_name->rowCount()){
						$name=$res_name->fetch();
						echo $name['display_name']."_-...-_success";
					}else{
						echo "notfound";
					}
				}
				if(isset($_GET["rename_file"]) && isset($_POST["file_id"]) && isset($_POST["name"])){
					$res_name=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$_POST["file_id"]."'");
					if($res_name->rowCount()){
						if($GLOBALS["connection"]->query("UPDATE ".$GLOBALS["sub_name"]."file_manager SET display_name='".$_POST["name"]."' WHERE id='".$_POST["file_id"]."'")){
							echo "success";
						}else{
							echo "error";
						}
					}else{
						echo "notfound";
					}
				}
				if(isset($_GET["delete"]) && isset($_POST["file_id"])){
					function changeSize($folder_id,$type,$size){
						$res_folder=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."file_manager WHERE id='".$folder_id."'");
						if($res_folder->rowCount()){
							$folder=$res_folder->fetch();
							switch ($type) {
								case 'add':
									$sizes=$folder['file_size']+$size;
								break;
								case 'remove':
									$sizes=$folder['file_size']-$size;
								break;
							}
							$GLOBALS['connection']->query("UPDATE ".$GLOBALS['sub_name']."file_manager SET file_size='".$sizes."', last_modify='".strtotime("now")."' WHERE id='".$folder_id."'");
							changeSize($folder['parent_id'],$type,$size);
						}
					}
					function sonFileManager($id){
						$res_file=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE parent_id='".$id."'");
						if($res_file->rowCount()){
							while($file=$res_file->fetch()){
								$GLOBALS["connection"]->query("DELETE FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$file["id"]."'");
								$is_secure=($file['is_secure'] ? "secure_files":"files");
								if($file['folder_file'] && file_exists("../../../".$is_secure."/".$file['real_name']) && $file['real_name']!=""){
									unlink("../../../".$is_secure."/".$file['real_name']);
								}else{
									sonFileManager($file['id']);
								}
							}
						}
					}
					$res_name=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$_POST["file_id"]."'");
					if($res_name->rowCount()){
						$name=$res_name->fetch();
						$is_secure=($name['is_secure'] ? "secure_files":"files");
						if($name['folder_file'] && file_exists("../../../".$is_secure."/".$name['real_name']) && $name['real_name']!=""){
							unlink("../../../".$is_secure."/".$name['real_name']);
						}else{
							sonFileManager($name['id']);
						}
						changeSize($name['parent_id'],'remove',$name['file_size']);
						if($GLOBALS["connection"]->query("DELETE FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$_POST["file_id"]."'")){
							echo "success";
						}else{
							echo "error";
						}
					}else{
						echo "notfound";
					}
				}
				if(isset($_GET["getDownload"]) && isset($_POST["file_id"])){
					$res_name=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$_POST["file_id"]."'");
					if($res_name->rowCount()){
						$name=$res_name->fetch();
						$data=[];
						$data['name']=$name['display_name'];
						$is_secure=($name['is_secure'] ? "secure_files":"files");
						if($name['folder_file'] && file_exists("../../../".$is_secure."/".$name['real_name']) && $name['real_name']!="" && $name['is_secure']==0){
							$data['public']=getSetting("upload_url").$name['real_name'];
							$data['force']=getSetting("site_url")."dl.php?d=".$name['id'];
						}
						$data=json_encode($data);
						echo $data."_-...-_success";
					}else{
						echo "notfound";
					}
				}
				if(isset($_GET["paste"]) && isset($_POST["current_folder"])){
					$file_manager_copy=(getUserSetting("file_manager_copy")=="false" ? 0:getUserSetting("file_manager_copy"));
					$file_manager_cut=(getUserSetting("file_manager_cut")=="false" ? 0:getUserSetting("file_manager_cut"));
					$paste_id=($file_manager_cut ? $file_manager_cut:$file_manager_copy);
					$res_about_file=$connection->query("SELECT * FROM ".$sub_name."file_manager WHERE id='".$paste_id."'");
					if($paste_id && ($_POST["current_folder"]!=$file_manager_cut && $file_manager_cut || $file_manager_cut==0) && $res_about_file->rowCount()){
						$about_file=$res_about_file->fetch();
						$res_curfolder=$connection->query("SELECT * FROM ".$sub_name."file_manager WHERE id='".$_POST["current_folder"]."'");
						function fileNameChecker_Changer($name,$is_secure,$about_file){
							$is_secure=($about_file['is_secure'] ? "secure_files":"files");
							if (file_exists("../../../".$is_secure."/".$name)) {
								$filetype=pathinfo($name,PATHINFO_EXTENSION);
								$name=strtotime("now")."-".generateRandomString().".".$filetype;
								fileNameChecker_Changer($name,($is_secure=="secure_files" ? 1:0),$about_file);
							}else{
								$GLOBALS['real_name']=$name;
							}
						}
						function changeSize($folder_id,$type,$size){
							$res_folder=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."file_manager WHERE id='".$folder_id."'");
							if($res_folder->rowCount()){
								$folder=$res_folder->fetch();
								switch ($type) {
									case 'add':
										$sizes=$folder['file_size']+$size;
									break;
									case 'remove':
										$sizes=$folder['file_size']-$size;
									break;
								}
								$GLOBALS['connection']->query("UPDATE ".$GLOBALS['sub_name']."file_manager SET file_size='".$sizes."', last_modify='".strtotime("now")."' WHERE id='".$folder_id."'");
								changeSize($folder['parent_id'],$type,$size);
							}
						}
						function cutHleper($file_id,$level){
							$level=$level+1;
							$res_cutHelper=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE parent_id='".$file_id."'");
							while ($cutHelper=$res_cutHelper->fetch()) {
								if($GLOBALS["connection"]->query("UPDATE ".$GLOBALS["sub_name"]."file_manager SET level='".$level."', last_modify='".strtotime("now")."' WHERE id='".$cutHelper['id']."'")){
									cutHleper($cutHelper['id'],$level);
								}
							}
						}
						function copyHelper($folder,$parent_id,$level){
							$res_copyHelping=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE parent_id='".$parent_id."'");
							while ($about_file=$res_copyHelping->fetch()) {
								if($about_file['folder_file']){
									$GLOBALS['real_name']="";
									fileNameChecker_Changer($about_file['real_name'],$about_file['is_secure'],$about_file);
									$is_secure=($about_file['is_secure'] ? "secure_files":"files");
									copy("../../../".$is_secure."/".$about_file['real_name'], "../../../".$is_secure."/".$GLOBALS['real_name']);
								}else{
									$GLOBALS['real_name']=$about_file['real_name'];
								}
								$ordering=(getLastItemByOrdering("file_manager") ? getLastItemByOrdering("file_manager")['ordering']+1:0);
								changeSize($_POST["current_folder"],'add',$about_file['file_size']);
								$GLOBALS["connection"]->exec("INSERT INTO ".$GLOBALS["sub_name"]."file_manager (folder_file,parent_id,level,display_name,real_name,fa_icon,file_size,is_secure,last_modify,ordering,act) VALUES ('".$about_file['folder_file']."','".$folder."','".$level."','".$about_file['display_name']."','".$GLOBALS['real_name']."','".$about_file['fa_icon']."','".$about_file['file_size']."','".$about_file['is_secure']."','".strtotime("now")."','".$ordering."','".$about_file['act']."')");
								if(!$about_file['folder_file']){
									copyHelper($GLOBALS["connection"]->lastInsertId(),$about_file['id'],$level+1);
								}
							}
						}
						$level=1;
						if($res_curfolder->rowCount()){
							$curfolder=$res_curfolder->fetch();
							$level=$curfolder['level']+1;
						}
						if($file_manager_cut){
							$GLOBALS["connection"]->query("UPDATE ".$GLOBALS["sub_name"]."file_manager SET parent_id='".$_POST["current_folder"]."', level='".$level."', last_modify='".strtotime("now")."' WHERE id='".$paste_id."'");
							cutHleper($paste_id,$level);
							changeSize($about_file['parent_id'],'remove',$about_file['file_size']);
							changeSize($_POST["current_folder"],'add',$about_file['file_size']);
						}else{
							if($about_file['folder_file']){
								$real_name="";
								fileNameChecker_Changer($about_file['real_name'],$about_file['is_secure'],$about_file);
								$is_secure=($about_file['is_secure'] ? "secure_files":"files");
								copy("../../../".$is_secure."/".$about_file['real_name'], "../../../".$is_secure."/".$real_name);
							}else{
								$real_name=$about_file['real_name'];
							}
							$ordering=(getLastItemByOrdering("file_manager") ? getLastItemByOrdering("file_manager")['ordering']+1:0);
							changeSize(0,'add',$about_file['file_size']);
							$GLOBALS["connection"]->exec("INSERT INTO ".$GLOBALS["sub_name"]."file_manager (folder_file,parent_id,level,display_name,real_name,fa_icon,file_size,is_secure,last_modify,ordering,act) VALUES ('".$about_file['folder_file']."','0','1','".$about_file['display_name']."','".$real_name."','".$about_file['fa_icon']."','".$about_file['file_size']."','".$about_file['is_secure']."','".strtotime("now")."','".$ordering."','".$about_file['act']."')");
							if(!$about_file['folder_file']){
								$have_to_cut=$connection->lastInsertId();
								copyHelper($connection->lastInsertId(),$about_file['id'],2);
								$GLOBALS["connection"]->query("UPDATE ".$GLOBALS["sub_name"]."file_manager SET parent_id='".$_POST["current_folder"]."', level='".$level."', last_modify='".strtotime("now")."' WHERE id='".$have_to_cut."'");
								cutHleper($have_to_cut,$level);
								changeSize(0,'remove',$about_file['file_size']);
								changeSize($_POST["current_folder"],'add',$about_file['file_size']);
							}
						}
						echo "success";
					}else{
						echo "notallowed_-...-_success";
					}
				}
            }else{
				echo "redirect_._login/";
			}
		}else{
			echo "redirect_._login/";
		}
	}else{
		echo "redirect_._setup/";
	}
?>
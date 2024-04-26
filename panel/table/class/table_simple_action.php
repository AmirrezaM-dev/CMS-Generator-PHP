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
			if($user_stats==1 || getSetting("op_admin")==$_SESSION['username']){
				$res_table_id = $connection->query("SELECT * FROM " . $sub_name . "table_config WHERE ((lock_admin_id=0 || lock_admin_id='') OR lock_admin_id='" . $_SESSION["username"] . "') AND current_name='".$sub_name."table_config"."' AND created=1 AND ((read_power=1 OR no_power=1 OR visible=1) OR '".$op_admin."'=1)");
				$table_get=($res_table_id->rowCount() ? $res_table_id->fetch():0);
				$table_id = ($table_get ? $table_get['id']:0);
				if(isset($_GET["update_single_column"]) && isset($_POST["table_id"]) && isset($_POST["column_id"]) && isset($_POST["id"])){
					$res_table=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE id='".$_POST["table_id"]."' AND created=1 AND ((read_power=1 OR visible=1) AND (create_power=1 OR creatable=1) OR '".$op_admin."'=1)");
					$res_column=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$_POST["column_id"]."' AND created=1 AND ((read_power=1 OR visible=1) AND (create_power=1 OR creatable=1) OR '".$op_admin."'=1)");
					$error=0;
					$table=($res_table->rowCount() ? $res_table->fetch():0);
					$res_update_time_code=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table["id"]."' AND mode=11 AND created=1 AND ((read_power=1 OR visible=1) AND (create_power=1 OR creatable=1) OR '".$op_admin."'=1)");//info search case 11 for see all things about this part
					while ($update_time_code=$res_update_time_code->fetch()) {
						$connection->query("UPDATE ".$table['current_name']." SET ".$update_time_code['current_name']."='".intval(strtotime("now"))."' WHERE id='".$_POST["id"]."'");
					}
					$column=($res_column->rowCount() ? $res_column->fetch():0);
					if($_SESSION["username"]==getSetting("op_admin") && $column['current_name']=="act"){
						$column['mode']=4;
					}
					if($table && $column && checkPermission(1, $table['id'], "read", $table['act'], "") && checkPermission(1, $table['id'], "update", $table['act'], "") && checkPermission(2, $column['id'], "read", $column['act'], $table['id']) && checkPermission(2, $column['id'], "update", $column['act'], $table['id'])){
						?><?php //info search this_is_modes_for_data_tables for see all things about this part ?><?php
						switch ($column['mode']) {//tables_mode_code
							case '1':case 1:case '2':case 2:case '5':case 5:case '10':case 10:case "12":case 12:case "13":case 13:case "14":case 14:case "15":case 15:case "16":case 16:case '17':case 17:case '18':case 18://info search case 18 for see all things about this part//info search case 13 for see all things about this part//info search case 16 for see all things about this part//info search case 12 for see all things about this part//info search case 1 for see all things about this part//info search case 2 for see all things about this part//info search case 5 for see all things about this part//info search case 10 for see all things about this part//info search case 14 for see all things about this part//info search case 15 for see all things about this part//info search case 17 for see all things about this part
								if(isset($_POST["normal_data"])){
									$normal_data=textCleaner($_POST["normal_data"]);
									try{
										$connection->beginTransaction();
										if(!$column['importants'] || strlen($normal_data)){
											$connection->exec("UPDATE ".$table['current_name']." SET ".$column['current_name']."='".$normal_data."' WHERE id='".$_POST["id"]."'");
										}else{
											echo 'alert_._<label class="data-text error-php" data-text-en="Fill important fields !" data-text-fa="فیلد های مهم را پر نمایید !">'.($GLOBALS['user_language']=="en" ? "Fill important fields !":"فیلد های مهم را پر نمایید !").'</label>';
										}
										if($connection->inTransaction()==true){
											if($connection->commit()){
												echo "success";
											}else{$error=1;}
										}else{$error=1;}
										if($error){
											echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
										}
									}catch(Exception $e){
										if($connection->inTransaction()==true){
											$connection->rollBack();
										}
										echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
									}
								}
							break;
							case 3:case '3'://info search case 3 for see all things about this part
								if(isset($_POST["yes_no_data"])){
									$yes_no_data=textCleaner($_POST["yes_no_data"]);
									$res_yes_no=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."yes_no_question_options WHERE table_id='".$table['id']."' AND column_id='".$column['id']."'");
									if($res_yes_no->rowCount()){
										$yes_no=$res_yes_no->fetch();
										$normal_data=($yes_no_data ? ($yes_no['yes_value']!="" ? $yes_no['yes_value']:1):($yes_no['no_value']!="" ? $yes_no['no_value']:0));
									}else{
										$normal_data=$yes_no_data;
									}
									try{
										$connection->beginTransaction();
										if(!$column['importants'] || strlen($normal_data)){
											$connection->exec("UPDATE ".$table['current_name']." SET ".$column['current_name']."='".$normal_data."' WHERE id='".$_POST["id"]."'");
										}else{
											echo 'alert_._<label class="data-text error-php" data-text-en="Fill important fields !" data-text-fa="فیلد های مهم را پر نمایید !">'.($GLOBALS['user_language']=="en" ? "Fill important fields !":"فیلد های مهم را پر نمایید !").'</label>';
										}
										if($connection->inTransaction()==true){
											if($connection->commit()){
												echo "success";
											}else{$error=1;}
										}else{$error=1;}
										if($error){
											echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
										}
									}catch(Exception $e){
										if($connection->inTransaction()==true){
											$connection->rollBack();
										}
										echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
									}
								}
							break;
							case 4:case '4'://info search case 4 for see all things about this part
								if(isset($_POST["normal_data"])){
									if($_SESSION["username"]==getSetting("op_admin") && $column['current_name']=="act"){
										$normal_data=(strlen($_POST["normal_data"]) ? $_POST["normal_data"]:0);
										$connection->exec("UPDATE ".$table['current_name']." SET ".$column['current_name']."='".$normal_data."' WHERE id='".$_POST["id"]."'");
										echo "success";
									}else{
										$res_selectbox_setting=$res_yes_no=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options_setting WHERE table_id='".$table['id']."' AND column_id='".$column['id']."'");
										$selectbox_setting=($res_selectbox_setting->rowCount() ? $res_selectbox_setting->fetch():["is_multiple"=>0,"is_forced"=>0,"min_allowed"=>0,"max_allowed"=>0]);
										$normal_datas="";
										if($selectbox_setting['is_multiple']){
											$data_arr=json_decode($_POST["normal_data"]);
											foreach ($data_arr as &$value) {
												if(isset($normal_data) && $normal_data!="" && !empty($normal_data)){
													$normal_data.="_-.,.-_".($value);
													if(strpos($value, "_-.-_")){
														$normal_datas.="_-.,.-_".(explode("_-.-_",$value)[1]);
													}elseif(strpos($value, "_-..-_")){
														$normal_datas.="_-.,.-_".(explode("_-..-_",$value)[1]);
													}else{
														$normal_datas.="_-.,.-_".($value);
													}
												}else{
													$normal_data=($value);
													if(strpos($value, "_-.-_")){
														$normal_datas=(explode("_-.-_",$value)[1]);
													}elseif(strpos($value, "_-..-_")){
														$normal_datas=(explode("_-..-_",$value)[1]);
													}else{
														$normal_datas=($value);
													}
												}
											}
										}else{
											$normal_data=($_POST["normal_data"]);
											if(strpos($_POST["normal_data"], "_-.-_")){
												$normal_datas=(explode("_-.-_",$_POST["normal_data"])[1]);
											}elseif(strpos($_POST["normal_data"], "_-..-_")){
												$normal_datas=(explode("_-..-_",$_POST["normal_data"])[1]);
											}else{
												$normal_datas=($_POST["normal_data"]);
											}
										}
										try{
											if(isset($normal_data)){
												$connection->beginTransaction();
												if(!$column['importants'] || strlen($normal_data)){
													$connection->exec("UPDATE ".$table['current_name']." SET ".$column['current_name']."='".$normal_data."', ".$column['current_name']."_justvalue='".$normal_datas."' WHERE id='".$_POST["id"]."'");
												}else{
													echo 'alert_._<label class="data-text error-php" data-text-en="Fill important fields !" data-text-fa="فیلد های مهم را پر نمایید !">'.($GLOBALS['user_language']=="en" ? "Fill important fields !":"فیلد های مهم را پر نمایید !").'</label>';
												}
												if($connection->inTransaction()==true){
													if($connection->commit()){
														echo "success";
													}else{$error=1;}
												}else{$error=1;}
												if($error){
													echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
												}
											}else{
												$connection->beginTransaction();
												if(!$column['importants']){
													$connection->exec("UPDATE ".$table['current_name']." SET ".$column['current_name']."='', ".$column['current_name']."_justvalue='' WHERE id='".$_POST["id"]."'");
												}else{
													echo 'alert_._<label class="data-text error-php" data-text-en="Fill important fields !" data-text-fa="فیلد های مهم را پر نمایید !">'.($GLOBALS['user_language']=="en" ? "Fill important fields !":"فیلد های مهم را پر نمایید !").'</label>';
												}
												if($connection->inTransaction()==true){
													if($connection->commit()){
														echo "success";
													}else{$error=1;}
												}else{$error=1;}
												if($error){
													echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
												}
												// echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
											}
										}catch(Exception $e){
											if($connection->inTransaction()==true){
												$connection->rollBack();
											}
											echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
										}
									}
								}
							break;
							case '6':case 6://info search case 6 for see all things about this part
								if(isset($_POST["normal_data"]) && isset($_POST["keep_password"])){
									if($_POST["keep_password"]==0){
										$normal_data=password_hash($_POST["normal_data"],PASSWORD_DEFAULT);
										try{
											$connection->beginTransaction();
											if(!$column['importants'] || strlen($normal_data)){
												$connection->exec("UPDATE ".$table['current_name']." SET ".$column['current_name']."='".$normal_data."' WHERE id='".$_POST["id"]."'");
											}else{
												echo 'alert_._<label class="data-text error-php" data-text-en="Fill important fields !" data-text-fa="فیلد های مهم را پر نمایید !">'.($GLOBALS['user_language']=="en" ? "Fill important fields !":"فیلد های مهم را پر نمایید !").'</label>';
											}
											if($connection->inTransaction()==true){
												if($connection->commit()){
													echo "success";
												}else{$error=1;}
											}else{$error=1;}
											if($error){
												echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
											}
										}catch(Exception $e){
											if($connection->inTransaction()==true){
												$connection->rollBack();
											}
											echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
										}
									}else{
										echo "success";
									}
								}else{
									echo "success";
								}
							break;
							case '7':case 7://info search case 7 for see all things about this part
								$error=0;
								if((isset($_POST["normal_data"]) || isset($_FILES["normal_data"])) && isset($_POST["keep_old_file"])){
									if($_POST["keep_old_file"]==0){
										if(isset($_FILES['normal_data'])){
											$res_file_uploader_setting=$connection->query("SELECT * FROM ".$sub_name."file_uploader_setting WHERE table_id='".$table['id']."' AND column_id='".$column['id']."'");
											if($res_file_uploader_setting->rowCount()!=0){
												$file_uploader_setting=$res_file_uploader_setting->fetch();
												$size_limit=$file_uploader_setting['max_size'];
												$file_types_limit=$file_uploader_setting['allowed_type'];
											}else{
												$size_limit='';
												$file_types_limit='';
											}
											$folder=$connection->query("SELECT * FROM ".$sub_name."file_manager WHERE id='-1'")->fetch();
											$level=(-1>0 ? ($folder['level']+1):(-1==0 ? 1:2));
											$max_file_size=((int)(ini_get('upload_max_filesize'))>=(int)(ini_get('post_max_size')) ? (int)(ini_get('upload_max_filesize')):(int)(ini_get('post_max_size')));
											$new_name=strtotime("now");
											if(0==1){
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
											$upload_name='normal_data';
											$real_name=basename($_FILES[$upload_name]["name"]);
											$target_file = $target_dir . basename($_FILES[$upload_name]["name"]);
											$uploadOk = 1;
											$FileType = pathinfo($target_file,PATHINFO_EXTENSION);
											if(getSetting("op_admin")==$_SESSION["username"] || ($size_limit=="" || intval($size_limit)==0 || $size_limit>=$_FILES[$upload_name]["size"]/1024/1024) && ($file_types_limit=="" || (in_array($FileType, explode(",",$file_types_limit)) || in_array(strtolower($FileType), explode(",",$file_types_limit)) || in_array(".".$FileType, explode(",",$file_types_limit)) || in_array(strtolower(".".$FileType), explode(",",$file_types_limit))))){
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
														$connection->query("INSERT INTO ".$sub_name."file_manager (folder_file,parent_id,level,display_name,real_name,fa_icon,file_size,is_secure,last_modify,ordering,act) VALUES (1,'-1','".$level."','".$real_name."','".$generated_name."','".$fa_icon."','".$_FILES[$upload_name]["size"]."','0','".strtotime("now")."','".$ordering."',1)");
														changeSize('-1','add',$_FILES[$upload_name]["size"]);
														if(0){
															$download_link=getSetting("admin_url")."files.php?download=1";
														}else{
															$download_link=getSetting("upload_url").$generated_name;
														}
													}
												}else{
													echo "error";
												}
												$normal_data=$download_link;
											}else{
												if(($size_limit=="" || intval($size_limit)==0 || $size_limit>=$_FILES[$upload_name]["size"]/1024/1024)){
													$error=3;
												}else{
													$error=2;
												}
											}
										}else{
											$normal_data=$_POST["normal_data"];
										}
										try{
											if(!$error){
												$connection->beginTransaction();
												if(!$column['importants'] || strlen($normal_data)){
													$connection->exec("UPDATE ".$table['current_name']." SET ".$column['current_name']."='".$normal_data."' WHERE id='".$_POST["id"]."'");
												}else{
													echo 'alert_._<label class="data-text error-php" data-text-en="Fill important fields !" data-text-fa="فیلد های مهم را پر نمایید !">'.($GLOBALS['user_language']=="en" ? "Fill important fields !":"فیلد های مهم را پر نمایید !").'</label>';
												}
												if($connection->inTransaction()==true){
													if($connection->commit()){
														echo "success";
													}else{$error=1;}
												}else{$error=1;}
												if($error){
													echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
												}
											}elseif($error==2){
												echo 'alert_._<label class="data-text error-php" data-text-en="Maximum allowed size is : '.$size_limit.' MB" data-text-fa="حد مجاز آپلود '.$size_limit.' مگابایت است">'.($GLOBALS['user_language']=="en" ? "Maximum allowed size is : $size_limit MB":"حد مجاز آپلود $size_limit مگابایت است").'</label>';
											}elseif($error==3){
												echo 'alert_._<label class="data-text error-php" data-text-en="Allowed file types is '.$file_types_limit.' !" data-text-fa="پسوند های مجاز برای آپلود '.$file_types_limit.' !">'.($GLOBALS['user_language']=="en" ? "Allowed file types is $file_types_limit !":"پسوند های مجاز برای آپلود $file_types_limit !").'</label>';
											}
										}catch(Exception $e){
											if($connection->inTransaction()==true){
												$connection->rollBack();
											}
											echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
										}
									}else{
										echo "success";
									}
								}else{
									echo "success";
								}
							break;
							case '8':case 8:case '19':case 19://info search case 19 for see all things about this part//info search case 8 for see all things about this part
								if(isset($_POST["normal_data"])){
									$normal_data=$_POST["normal_data"];
									try{
										$connection->beginTransaction();
										if(!$column['importants'] || strlen($normal_data)){
											$connection->exec("UPDATE ".$table['current_name']." SET ".$column['current_name']."='".$normal_data."' WHERE id='".$_POST["id"]."'");
										}else{
											echo 'alert_._<label class="data-text error-php" data-text-en="Fill important fields !" data-text-fa="فیلد های مهم را پر نمایید !">'.($GLOBALS['user_language']=="en" ? "Fill important fields !":"فیلد های مهم را پر نمایید !").'</label>';
										}
										if($connection->inTransaction()==true){
											if($connection->commit()){
												echo "success";
											}else{$error=1;}
										}else{$error=1;}
										if($error){
											echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
										}
									}catch(Exception $e){
										if($connection->inTransaction()==true){
											$connection->rollBack();
										}
										echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
									}
								}
							break;
							case '9':case 9://info search case 9 for see all things about this part
								if(isset($_POST["normal_data"])){
									$normal_data=$_POST["normal_data"];
									try{
										$connection->beginTransaction();
										if(!$column['importants'] || strlen($normal_data)){
											$connection->exec("UPDATE ".$table['current_name']." SET ".$column['current_name']."='".$normal_data."' WHERE id='".$_POST["id"]."'");
										}else{
											echo 'alert_._<label class="data-text error-php" data-text-en="Fill important fields !" data-text-fa="فیلد های مهم را پر نمایید !">'.($GLOBALS['user_language']=="en" ? "Fill important fields !":"فیلد های مهم را پر نمایید !").'</label>';
										}
										if($connection->inTransaction()==true){
											if($connection->commit()){
												echo "success";
											}else{$error=1;}
										}else{$error=1;}
										if($error){
											echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
										}
									}catch(Exception $e){
										if($connection->inTransaction()==true){
											$connection->rollBack();
										}
										echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
									}
								}
							break;
						}
					}else{
						echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
					}
				}
				if(isset($_GET["deleteThis"]) && isset($_POST["table_id"]) && isset($_POST["id"])){
					$res_table=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE id='".$_POST["table_id"]."' AND created=1 AND ((read_power=1 OR visible=1) AND (create_power=1 OR creatable=1) OR '".$op_admin."'=1)");
					$table=($res_table->rowCount() ? $res_table->fetch():0);
					if($connection->query("DELETE FROM ".$table['current_name']." WHERE id='".$_POST["id"]."'")){
						echo "success";
					}
				}
				if(isset($_GET["copyThis"]) && isset($_POST["table_id"]) && isset($_POST["id"])){
					try{
						$res_table=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE id='".$_POST["table_id"]."' AND created=1 AND ((read_power=1 OR visible=1) AND (create_power=1 OR creatable=1) OR '".$op_admin."'=1)");
						$table=($res_table->rowCount() ? $res_table->fetch():0);
						$res_column=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$_POST["table_id"]."' ORDER BY column_number");
						$columns="";
						while ($column=$res_column->fetch()) {
							if($column!="ordering" && $column!="id"){
								if(!strpos($columns,$column['current_name']." ")){
									if(strlen($columns)){
										$columns.=" ,".$column['current_name'];
									}else{
										$columns=$column['current_name']." ";
									}
								}
								if($column['mode']==4 && !strpos($columns,$column['current_name']."_justvalue ")){//info search case 4 for see all things about this part
									if(strlen($columns)){
										$columns.=",".$column['current_name']."_justvalue ";
									}else{
										$columns=$column['current_name']."_justvalue ";
									}
								}
							}
						}
						$ordering=getNewOrderId($table['current_name']);
						$connection->exec("INSERT INTO ".$table['current_name']." (".$columns.") SELECT ".$columns." FROM ".$table['current_name']." WHERE id='".$_POST["id"]."'");
						$copied_id=$connection->lastInsertId();
						$connection->exec("UPDATE ".$table['current_name']." SET ordering='".$ordering."' WHERE id='".$copied_id."'");
						print_r(json_encode(["status"=>"success","id"=>$copied_id]));
					}catch(Exception $e){
						echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
					}
				}
				if(isset($_GET["createNew"]) && isset($_POST["table_id"])){
					$res_table=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE id='".$_POST["table_id"]."' AND created=1 AND ((read_power=1 OR visible=1) AND (create_power=1 OR creatable=1) OR '".$op_admin."'=1)");
					$table=($res_table->rowCount() ? $res_table->fetch():0);
					$insert_names="";
					$insert_values="";
					$res_newtable_column=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table['id']."' AND created=1 AND current_name!='ordering' ORDER BY column_number ASC");
					while($newtable_column=$res_newtable_column->fetch()){
						if($_SESSION["username"]==getSetting("op_admin") && $newtable_column['current_name']=="act"){
							$newtable_column['mode']=4;
						}
						if(!strpos($newtable_column['current_name'],"justvalue")){
							if(isset($op_admin) && $op_admin || $newtable_column['creatable']==1){
								$e_id="save_-_".preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $newtable_column['current_name'], 1)."_-_".$newtable_column['id']."_-_".$newtable_column['mode'];
								if($insert_names==""){
									$insert_names=$newtable_column['current_name'];
								}else{
									$insert_names.=",".$newtable_column['current_name'];
								}
								switch ($newtable_column['mode']) {//tables_mode_code
									case '1':case 1:case '2':case 2:case '5':case 5:case '10':case 10:case "12":case 12:case "13":case 13:case "14":case 14:case "15":case 15:case "16":case 16:case '17':case 17:case '18':case 18://info search case 18 for see all things about this part//info search case 13 for see all things about this part//info search case 16 for see all things about this part//info search case 12 for see all things about this part//info search case 1 for see all things about this part//info search case 2 for see all things about this part//info search case 5 for see all things about this part//info search case 10 for see all things about this part//info search case 14 for see all things about this part//info search case 15 for see all things about this part//info search case 17 for see all things about this part
										$_POST[$e_id]=textCleaner($_POST[$e_id]);
										if($insert_values==""){
											$insert_values="'".($_POST[$e_id])."'";
										}else{
											$insert_values.=",'".($_POST[$e_id])."'";
										}
										if(!strlen($_POST[$e_id]) && $newtable_column['importants']){
											echo 'alert_._<label class="data-text error-php" data-text-en="Filling important inputs are required !" data-text-fa="پر کردن فیلد های مهم مورد نیاز است !">'.($GLOBALS['user_language']=="en" ? "Filling important inputs are required !":"پر کردن فیلد های مهم مورد نیاز است !").'</label>';
											exit();
										}
									break;
									case 3:case '3'://info search case 3 for see all things about this part
										$yes_no_data=textCleaner($_POST[$e_id]);
										$res_yes_no=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."yes_no_question_options WHERE table_id='".$table['id']."' AND column_id='".$newtable_column['id']."'");
										if($res_yes_no->rowCount()){
											$yes_no=$res_yes_no->fetch();
											$normal_data=($yes_no_data ? ($yes_no['yes_value']!="" ? $yes_no['yes_value']:1):($yes_no['no_value']!="" ? $yes_no['no_value']:0));
										}else{
											$normal_data=$yes_no_data;
										}
										if($insert_values==""){
											$insert_values="'".($normal_data)."'";
										}else{
											$insert_values.=",'".($normal_data)."'";
										}
									break;
									case 4:case '4'://info search case 4 for see all things about this part
										$res_selectbox_setting=$res_yes_no=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options_setting WHERE table_id='".$table['id']."' AND column_id='".$newtable_column['id']."'");
										$selectbox_setting=($res_selectbox_setting->rowCount() ? $res_selectbox_setting->fetch():["is_multiple"=>0,"is_forced"=>0,"min_allowed"=>0,"max_allowed"=>0]);
										$normal_datas="";
										if($selectbox_setting['is_multiple']){
											$data_arr=json_decode($_POST[$e_id]);
											if(is_array($data_arr)){
												foreach ($data_arr as &$value) {
													if(isset($normal_data) && $normal_data!="" && !empty($normal_data)){
														$normal_data.="_-.,.-_".($value);
														if(strpos($value, "_-.-_")){
															$normal_datas.="_-.,.-_".(explode("_-.-_",$value)[1]);
														}elseif(strpos($value, "_-..-_")){
															$normal_datas.="_-.,.-_".(explode("_-..-_",$value)[1]);
														}else{
															$normal_datas.="_-.,.-_".($value);
														}
													}else{
														$normal_data=($value);
														if(strpos($value, "_-.-_")){
															$normal_datas=(explode("_-.-_",$value)[1]);
														}elseif(strpos($value, "_-..-_")){
															$normal_datas=(explode("_-..-_",$value)[1]);
														}else{
															$normal_datas=($value);
														}
													}
												}
											}
										}else{
											$normal_data=($_POST[$e_id]);
											if(strpos($_POST[$e_id], "_-.-_")){
												$normal_datas=explode("_-.-_",$_POST[$e_id])[1];
											}elseif(strpos($_POST[$e_id], "_-..-_")){
												$normal_datas=explode("_-..-_",$_POST[$e_id])[1];
											}else{
												$normal_datas=$_POST[$e_id];
											}
										}
										if($newtable_column['current_name']!="act"){
											if($insert_values==""){
												$insert_values="'".($normal_data)."'";
											}else{
												$insert_values.=",'".($normal_data)."'";
											}
											if($insert_names==""){
												$insert_names=$newtable_column['current_name']."_justvalue";
											}else{
												$insert_names.=",".$newtable_column['current_name']."_justvalue";
											}
											if($insert_values==""){
												$insert_values="'".($normal_datas)."'";
											}else{
												$insert_values.=",'".($normal_datas)."'";
											}
										}else{
											if($insert_values==""){
												$insert_values=(strlen($normal_data) ? $normal_data:0);
											}else{
												$insert_values.=",".(strlen($normal_data) ? $normal_data:0);
											}
										}
										if(!strlen($normal_data) && $newtable_column['importants']){
											echo 'alert_._<label class="data-text error-php" data-text-en="Filling important inputs are required !" data-text-fa="پر کردن فیلد های مهم مورد نیاز است !">'.($GLOBALS['user_language']=="en" ? "Filling important inputs are required !":"پر کردن فیلد های مهم مورد نیاز است !").'</label>';
											exit();
										}
									break;
									case '6':case 6://info search case 6 for see all things about this part
										$normal_data=password_hash($_POST[$e_id],PASSWORD_DEFAULT);
										if($insert_values==""){
											$insert_values="'".($normal_data)."'";
										}else{
											$insert_values.=",'".($normal_data)."'";
										}
										if(!strlen($normal_data) && $newtable_column['importants']){
											echo 'alert_._<label class="data-text error-php" data-text-en="Filling important inputs are required !" data-text-fa="پر کردن فیلد های مهم مورد نیاز است !">'.($GLOBALS['user_language']=="en" ? "Filling important inputs are required !":"پر کردن فیلد های مهم مورد نیاز است !").'</label>';
											exit();
										}
									break;
									case '7':case 7://info search case 7 for see all things about this part
										$error=0;
										if(isset($_FILES[$e_id])){
											$res_file_uploader_setting=$connection->query("SELECT * FROM ".$sub_name."file_uploader_setting WHERE table_id='".$table['id']."' AND column_id='".$newtable_column['id']."'");
											if($res_file_uploader_setting->rowCount()!=0){
												$file_uploader_setting=$res_file_uploader_setting->fetch();
												$size_limit=$file_uploader_setting['max_size'];
												$file_types_limit=$file_uploader_setting['allowed_type'];
											}else{
												$size_limit='';
												$file_types_limit='';
											}
											$folder=$connection->query("SELECT * FROM ".$sub_name."file_manager WHERE id='-1'")->fetch();
											$level=(-1>0 ? ($folder['level']+1):(-1==0 ? 1:2));
											$max_file_size=((int)(ini_get('upload_max_filesize'))>=(int)(ini_get('post_max_size')) ? (int)(ini_get('upload_max_filesize')):(int)(ini_get('post_max_size')));
											$new_name=strtotime("now");
											if(0==1){
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
											$upload_name=$e_id;
											$real_name=basename($_FILES[$upload_name]["name"]);
											$target_file = $target_dir . basename($_FILES[$upload_name]["name"]);
											$uploadOk = 1;
											$FileType = pathinfo($target_file,PATHINFO_EXTENSION);
											if(getSetting("op_admin")==$_SESSION["username"] || ($size_limit=="" || intval($size_limit)==0 || $size_limit>=$_FILES[$upload_name]["size"]/1024/1024) && ($file_types_limit=="" || (in_array($FileType, explode(",",$file_types_limit)) || in_array(strtolower($FileType), explode(",",$file_types_limit)) || in_array(".".$FileType, explode(",",$file_types_limit)) || in_array(strtolower(".".$FileType), explode(",",$file_types_limit))))){
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
														$connection->query("INSERT INTO ".$sub_name."file_manager (folder_file,parent_id,level,display_name,real_name,fa_icon,file_size,is_secure,last_modify,ordering,act) VALUES (1,'-1','".$level."','".$real_name."','".$generated_name."','".$fa_icon."','".$_FILES[$upload_name]["size"]."','0','".strtotime("now")."','".$ordering."',1)");
														changeSize('-1','add',$_FILES[$upload_name]["size"]);
														if(0){
															$download_link=getSetting("admin_url")."files.php?download=1";
														}else{
															$download_link=getSetting("upload_url").$generated_name;
														}
													}
												}else{
													echo "error";
												}
												$normal_data=$download_link;
											}else{
												if(($size_limit=="" || intval($size_limit)==0 || $size_limit>=$_FILES[$upload_name]["size"]/1024/1024)){
													$error=3;
												}else{
													$error=2;
												}
											}
										}else{
											$normal_data=$_POST[$e_id];
										}
										if($insert_values==""){
											$insert_values="'".($normal_data)."'";
										}else{
											$insert_values.=",'".($normal_data)."'";
										}
										if(!strlen($normal_data) && $newtable_column['importants']){
											echo 'alert_._<label class="data-text error-php" data-text-en="Filling important inputs are required !" data-text-fa="پر کردن فیلد های مهم مورد نیاز است !">'.($GLOBALS['user_language']=="en" ? "Filling important inputs are required !":"پر کردن فیلد های مهم مورد نیاز است !").'</label>';
											exit();
										}
									break;
									case '8':case 8:case '19':case 19://info search case 19 for see all things about this part//info search case 8 for see all things about this part
										$normal_data=$_POST[$e_id];
										if($insert_values==""){
											$insert_values="'".($normal_data)."'";
										}else{
											$insert_values.=",'".($normal_data)."'";
										}
										if(!strlen($normal_data) && $newtable_column['importants']){
											echo 'alert_._<label class="data-text error-php" data-text-en="Filling important inputs are required !" data-text-fa="پر کردن فیلد های مهم مورد نیاز است !">'.($GLOBALS['user_language']=="en" ? "Filling important inputs are required !":"پر کردن فیلد های مهم مورد نیاز است !").'</label>';
											exit();
										}
									break;
									case '9':case 9://info search case 9 for see all things about this part
										$normal_data=$_POST[$e_id];
										if($insert_values==""){
											$insert_values="'".($normal_data)."'";
										}else{
											$insert_values.=",'".($normal_data)."'";
										}
										if(!strlen($normal_data) && $newtable_column['importants']){
											echo 'alert_._<label class="data-text error-php" data-text-en="Filling important inputs are required !" data-text-fa="پر کردن فیلد های مهم مورد نیاز است !">'.($GLOBALS['user_language']=="en" ? "Filling important inputs are required !":"پر کردن فیلد های مهم مورد نیاز است !").'</label>';
											exit();
										}
									break;
									case 11:case "11"://info search case 11 for see all things about this part
									case 21:case "21"://info search case 11 for see all things about this part
										if($insert_values==""){
											$insert_values="'".strtotime("now")."'";
										}else{
											$insert_values.=",'".strtotime("now")."'";
										}
									break;
								}
							}else{
								echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
								exit();
							}
						}
					}
					if($insert_names==""){
						$insert_names="ordering";
					}else{
						$insert_names.=","."ordering";
					}
					$new_order_id=getNewOrderId($table['current_name']);
					if($insert_values==""){
						$insert_values="'".$new_order_id."'";
					}else{
						$insert_values.=",'".$new_order_id."'";
					}
					try {
						$connection->exec("INSERT INTO ".$table['current_name']." (".$insert_names.") VALUES (".$insert_values.")");
						$goTo=$connection->lastInsertId();
						echo json_encode(["status"=>"success","goTo"=>$goTo]);
					}catch(Exception $e) {
						echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
					}
				}
				if(isset($_GET["deleteAll"]) && isset($_POST["table_id"])){
					$res_table=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE id='".$_POST["table_id"]."' AND created=1 AND ((read_power=1 OR visible=1) AND (create_power=1 OR creatable=1) OR '".$op_admin."'=1)");
					$table=($res_table->rowCount() ? $res_table->fetch():0);
					if($table["removable"] || isset($op_admin) && $op_admin){
						if(isset($op_admin) && $op_admin){
							$connection->query("DELETE FROM ".$table['current_name']);
						}else{
							$res_delete=$connection->query("SELECT * FROM ".$table['current_name']);
							while ($delete=$res_delete->fetch()) {
								if(checkPermission(1, $_POST["table_id"], "delete", $delete['act'], "")){
									$connection->query("DELETE FROM ".$table['current_name']." WHERE id='".$delete['id']."'");
								}
							}
						}
						echo "success";
					}
				}
				if(isset($_GET["moveUp"]) && isset($_POST["id"]) && isset($_POST["table_id"])){
					$res_table_config = $connection->query("SELECT * FROM " . $sub_name . "table_config WHERE id='" . $_POST['table_id'] . "'");
					$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
					if($table_config){
						$id=$_POST["id"];
						$current=$connection->query("SELECT * FROM ".$table_config['current_name']." WHERE id='".$id."'");
						if($current->rowCount()){
							$current=$current->fetch();
							$other=$connection->query("SELECT * FROM ".$table_config['current_name']." WHERE ordering<'".$current['ordering']."' ORDER BY ordering DESC");
							if($other->rowCount()){
								$other=$other->fetch();
								$first=$other['ordering'];
								$second=$current['ordering'];
								$connection->query("UPDATE ".$table_config['current_name']." SET ordering='".$first."' WHERE id='".$current['id']."'");
								$connection->query("UPDATE ".$table_config['current_name']." SET ordering='".$second."' WHERE id='".$other['id']."'");
								echo "success";
							}else{
								echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
							}
						}else{
							echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
						}
					}
				}
				if(isset($_GET["moveDown"]) && isset($_POST["id"]) && isset($_POST["table_id"])){
					$res_table_config = $connection->query("SELECT * FROM " . $sub_name . "table_config WHERE id='" . $_POST['table_id'] . "'");
					$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
					if($table_config){
						$id=$_POST["id"];
						$current=$connection->query("SELECT * FROM ".$table_config['current_name']." WHERE id='".$id."'");
						if($current->rowCount()){
							$current=$current->fetch();
							$other=$connection->query("SELECT * FROM ".$table_config['current_name']." WHERE ordering>'".$current['ordering']."' ORDER BY ordering ASC");
							if($other->rowCount()){
								$other=$other->fetch();
								$first=$other['ordering'];
								$second=$current['ordering'];
								$connection->query("UPDATE ".$table_config['current_name']." SET ordering='".$first."' WHERE id='".$current['id']."'");
								$connection->query("UPDATE ".$table_config['current_name']." SET ordering='".$second."' WHERE id='".$other['id']."'");
								echo "success";
							}else{
								echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
							}
						}else{
							echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
						}
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
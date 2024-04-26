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
				$table_name = $sub_name."table_config";
				$res_table_id = $connection->query("SELECT * FROM " . $sub_name . "table_config WHERE current_name='" . $table_name . "' AND created=1 AND creatable=1 AND visible=1 OR current_name='" . $table_name . "' AND created=1 AND '" . $op_admin . "'=1");
				if($res_table_id->rowCount() != 0){
					$table_get = $res_table_id->fetch();
					$table_id = $table_get['id'];
					$res_table_config = $connection->query("SELECT * FROM " . $sub_name . "table_config WHERE lock_admin_id='" . $_SESSION["username"] . "'");
					if(isset($_GET["create_table_name"]) && isset($_POST["current_name"]) && isset($_POST["description_name_fa"]) && isset($_POST["description_info_fa"]) && isset($_POST["description_name_en"]) && isset($_POST["description_info_en"])){
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);

						//* list of all of tables
						$show_tables=[];
						for($i=0;$i<count($connection->query("show tables")->fetchAll());$i++){$show_tables[count($show_tables)]=$connection->query("show tables")->fetchAll()[$i][0];}
						//* list of all of tables

						$current_name=mb_strtolower(textCleaner($_POST["current_name"]),'UTF-8');
						$description_name_fa=mb_strtolower(textCleaner($_POST["description_name_fa"]),'UTF-8');
						$description_info_fa=mb_strtolower(textCleaner($_POST["description_info_fa"]),'UTF-8');
						$description_name_en=mb_strtolower(textCleaner($_POST["description_name_en"]),'UTF-8');
						$description_info_en=mb_strtolower(textCleaner($_POST["description_info_en"]),'UTF-8');

						$checking_name=0;
						$res_checking_name=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE lock_admin_id='".$_SESSION['username']."'");
						$res_checking_name1=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE current_name='".$sub_name.$current_name."' AND created=0 AND lock_admin_id='".$_SESSION['username']."' OR lock_admin_id='".$_SESSION['username']."' AND created=0");
						$res_checking_name2=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE new_name='".$sub_name.$current_name."' AND lock_admin_id!='".$_SESSION['username']."'");
						$res_checking_name3=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE current_name='".$sub_name.$current_name."' AND lock_admin_id!='".$_SESSION['username']."'");
						if($res_checking_name1->rowCount()==1 && $res_checking_name2->rowCount()==0 && $res_checking_name->rowCount()!=0 || $res_checking_name2->rowCount()==0 && $res_checking_name3->rowCount()==0 && $res_checking_name->rowCount()!=0){
							$checking_name=$res_checking_name->fetch();
							if($checking_name['created']==0 && $checking_name['lock_admin_id']==$_SESSION["username"]){
								$sql="UPDATE ".$sub_name."table_config SET current_name='".$sub_name.$current_name."', description_name_fa='".$description_name_fa."', description_info_fa='".$description_info_fa."', description_name_en='".$description_name_en."', description_info_en='".$description_info_en."' WHERE lock_admin_id='".$_SESSION["username"]."' AND created=0";
							}else if($checking_name['created']==1 && $checking_name['lock_admin_id']==$_SESSION["username"]){
								$sql="UPDATE ".$sub_name."table_config SET new_name='".$sub_name.$current_name."', description_name_fa='".$description_name_fa."', description_info_fa='".$description_info_fa."', description_name_en='".$description_name_en."', description_info_en='".$description_info_en."' WHERE lock_admin_id='".$_SESSION["username"]."' AND created=1";
							}
						}elseif($res_checking_name1->rowCount()==0 && $res_checking_name2->rowCount()==0){
							$checking_name=2;
							$columns=getTableColumnsName($connection,"table_config");
							$ordering=getLastItemByOrdering("table_config")['ordering']+1;
							$sql="INSERT INTO ".$sub_name."table_config (".$columns.") VALUES ('".$sub_name.$current_name."','','".$description_name_fa."','".$description_info_fa."','".$description_name_en."','".$description_info_en."','".$_SESSION["username"]."','0','1','1','1','1','0','0','0','0','0','0','".$ordering."','1')";
						}
						$error=0;
						if(ctype_alnum(str_replace("_","",$current_name))){
							if(!in_array($sub_name.$current_name, $show_tables) && $checking_name || $table_config['created']==1 && isset($sql) && $sql){
								$connection->beginTransaction();
								try{
									$connection->exec($sql);
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
								echo "error_._name_duplicated";
							}
						}else{
							if(!ctype_alnum(str_replace("_","",$current_name))){
								echo "error_._bad_name_._current_name";
							}elseif(!ctype_alnum(str_replace("_","",$description_name_fa))){
								echo "error_._bad_name_._description_name_fa";
							}elseif(!ctype_alnum(str_replace("_","",$description_name_en))){
								echo "error_._bad_name_._description_name_en";
							}
						}
					}
					if(isset($_GET["update_setting"]) && isset($_GET["name"]) && isset($_POST["data"])){
						$data=textCleaner($_POST["data"]);
						$name=textCleaner($_GET["name"]);
						$connection->beginTransaction();
						try{
							$connection->exec("UPDATE ".$sub_name."table_config SET ".str_replace("_table","",$name)."='".$data."' WHERE lock_admin_id='".$_SESSION["username"]."' AND created=0");
							if($connection->inTransaction()==true){
								if($connection->commit()){
									echo "success";
								}else{
									echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
								}
							}else{
								echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
							}
						} catch(Exception $e){
							if($connection->inTransaction()==true){
								$connection->rollBack();
							}
							echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
						}
					}
					if(isset($_GET["stepTwo"]) && isset($_POST["creatable"]) && isset($_POST["visible"]) && isset($_POST["editable"]) && isset($_POST["removable"])){
						$connection->beginTransaction();
						try{
							$connection->exec("UPDATE ".$sub_name."table_config SET creatable='".$_POST["creatable"]."', visible='".$_POST["visible"]."', editable='".$_POST["editable"]."', removable='".$_POST["removable"]."' WHERE lock_admin_id='".$_SESSION["username"]."' AND created=0");
							if($connection->inTransaction()==true){
								if($connection->commit()){
									echo "success";
								}else{
									echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
								}
							}else{
								echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
							}
						} catch(Exception $e){
							if($connection->inTransaction()==true){
								$connection->rollBack();
							}
							echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
						}
					}
					if(isset($_GET["update_level"]) && isset($_POST["level"])){
						$connection->beginTransaction();
						try{
							$connection->exec("UPDATE ".$sub_name."table_config SET level='".$_POST["level"]."' WHERE lock_admin_id='".$_SESSION["username"]."'");
							if($connection->inTransaction()==true){
								if($connection->commit()){
									echo "success";
								}else{
									echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
								}
							}else{
								echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
							}
						} catch(Exception $e){
							if($connection->inTransaction()==true){
								$connection->rollBack();
							}
							echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
						}
					}
					if(isset($_GET["saveNewColumn"]) && isset($_POST["current_name"]) && isset($_POST["description_name_fa"]) && isset($_POST["description_info_fa"]) && isset($_POST["description_name_en"]) && isset($_POST["description_info_en"]) && isset($_POST["input_select"]) && isset($_POST["input_creatable"]) && isset($_POST["input_visible"]) && isset($_POST["input_editable"]) && isset($_POST["input_removable"]) && isset($_POST["input_visible_table"]) && isset($_POST["input_primary"]) && isset($_POST["input_important"]) && isset($_POST["input_extra"])){
						$current_name=mb_strtolower(textCleaner($_POST['current_name']),'UTF-8');
						$description_name_fa=textCleaner($_POST['description_name_fa']);
						$description_info_fa=textCleaner($_POST['description_info_fa']);
						$description_name_en=textCleaner($_POST['description_name_en']);
						$description_info_en=textCleaner($_POST['description_info_en']);
						$input_select=textCleaner($_POST['input_select']);
						$input_creatable=textCleaner($_POST['input_creatable']);
						$input_visible=textCleaner($_POST['input_visible']);
						$input_editable=textCleaner($_POST['input_editable']);
						$input_removable=textCleaner($_POST['input_removable']);
						$input_visible_table=textCleaner($_POST['input_visible_table']);
						$input_primary=textCleaner($_POST['input_primary']);
						$input_important=textCleaner($_POST['input_important']);
						$input_extra=json_decode($_POST['input_extra']);
						if($current_name!="" && $description_name_en!="" && $description_name_fa!=""){
							if(ctype_alnum(str_replace("_","",$current_name))){
								$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
								if($table_config!=0){
									$res_column_check=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE current_name='".$current_name."' AND table_id='".$table_config['id']."' OR new_name='".$current_name."' AND table_id='".$table_config['id']."'");
									if($res_column_check->rowCount()==0){
										$error=0;
										if($input_primary==1){
											$res_column_primary_check=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE primarys=1 AND table_id='".$table_config['id']."'");
											if($res_column_primary_check->rowCount()!=0){
												$error=1;
											}
										}
										if($error==0){
											$last_column_number=getLastColumnNumber($table_config['id']);
											$last_column_number=($last_column_number>1 ? $last_column_number:$last_column_number+1);
											$last_ordering=getLastItemByOrdering('table_column_config')['ordering']+1;
											$sql = "INSERT INTO ".$sub_name."table_column_config (table_id, current_name, new_name, column_number, description_name_fa, description_info_fa, description_name_en, description_info_en, created, creatable, visible, editable, removable, visible_table, create_power, read_power, update_power, delete_power, no_power, mode, new_mode, editing, primarys, importants, ordering, act) VALUES ('".$table_config['id']."','".$current_name."','','".$last_column_number."','".$description_name_fa."','".$description_info_fa."','".$description_name_en."','".$description_info_en."','0','".$input_creatable."','".$input_visible."','".$input_editable."','".$input_removable."','".$input_visible_table."','0','0','0','0','0','".$input_select."',0,1,'".$input_primary."','".$input_important."','".$last_ordering."',1)";
											$connection->exec($sql);
											$last_id = $connection->lastInsertId();
											?><?php //info search this_is_modes_for_data_tables for see all things about this part ?><?php
											switch ($input_select) {//tables_mode_code
												//savecolumn_create_tables_mode_php_extra
												case "3":case 3://info search case 3 for see all things about this part
													$res_yes_no_setting=$connection->query("SELECT * FROM ".$sub_name."yes_no_question_options WHERE table_id='".$table_config['id']."' AND column_id='".$last_id."'");
													if($res_yes_no_setting->rowCount()!=0){
														$sql="UPDATE ".$sub_name."yes_no_question_options SET yes_option='".$input_extra[0]."', no_option='".$input_extra[1]."', yes_value='".($input_extra[2]!="" ? $input_extra[2]:1)."', no_value='".($input_extra[3]!="" ? $input_extra[3]:0)."', yes_fa_icon='".$input_extra[4]."', no_fa_icon='".$input_extra[5]."' WHERE table_id='".$table_config['id']."' AND column_id='".$last_id."'";
														$connection->exec($sql);
													}else{
														$last_ordering=getNewOrderId('yes_no_question_options');
														$sql="INSERT INTO ".$sub_name."yes_no_question_options (table_id,column_id,yes_option,no_option,yes_value,no_value,yes_fa_icon,no_fa_icon,ordering,act) VALUES ('".$table_config['id']."','".$last_id."','".$input_extra[0]."','".$input_extra[1]."','".($input_extra[2]!="" ? $input_extra[2]:1)."','".($input_extra[3]!="" ? $input_extra[3]:0)."','".$input_extra[4]."','".$input_extra[5]."','".$last_ordering."',1)";
														$connection->exec($sql);
													}
												break;
												case "4":case 4://info search case 4 for see all things about this part
													$res_selectbox_setting=$connection->query("SELECT * FROM ".$sub_name."select_options_setting WHERE table_id='".$table_config['id']."' AND column_id='".$last_id."'");
													if($res_selectbox_setting->rowCount()){
														$sql="UPDATE ".$sub_name."select_options_setting SET is_multiple='".($input_extra[0]!="" ? 1:0)."', is_forced='".($input_extra[1]!="" ? 1:0)."', min_allowed='".intval($input_extra[4])."', max_allowed='".intval($input_extra[5])."' WHERE table_id='".$table_config['id']."' AND column_id='".$last_id."'";
														$connection->exec($sql);
													}else{
														$last_ordering=getNewOrderId('select_options_setting');
														$sql="INSERT INTO ".$sub_name."select_options_setting (table_id,column_id,is_multiple,is_forced,min_allowed,max_allowed,ordering,act) VALUES ('".$table_config['id']."','".$last_id."','".($input_extra[0]!="" ? 1:0)."','".($input_extra[1]!="" ? 1:0)."','".intval($input_extra[2])."','".intval($input_extra[3])."','".$last_ordering."',1)";
														$connection->exec($sql);
													}
												break;
												case "7":case 7://info search case 7 for see all things about this part
													$res_file_uploader_setting=$connection->query("SELECT * FROM ".$sub_name."file_uploader_setting WHERE table_id='".$table_config['id']."' AND column_id='".$last_id."'");
													if($res_file_uploader_setting->rowCount()!=0){
														$sql="UPDATE ".$sub_name."file_uploader_setting SET max_size='".intval($input_extra[0])."', allowed_type='".$input_extra[1]."' WHERE table_id='".$table_config['id']."' AND column_id='".$last_id."'";
														$connection->exec($sql);
													}else{
														$last_ordering=getNewOrderId('file_uploader_setting');
														$sql="INSERT INTO ".$sub_name."file_uploader_setting (table_id,column_id,max_size,allowed_type,ordering,act) VALUES ('".$table_config['id']."','".$last_id."','".intval($input_extra[0])."','".$input_extra[1]."','".$last_ordering."',1)";
														$connection->exec($sql);
													}
												break;
												case "9":case 9://info search case 9 for see all things about this part
													$res_checkbox_setting=$connection->query("SELECT * FROM ".$sub_name."checkbox_options_setting WHERE table_id='".$table_config['id']."' AND column_id='".$last_id."'");
													if($res_checkbox_setting->rowCount()!=0){
														$sql="UPDATE ".$sub_name."checkbox_options_setting SET is_multiple='".($input_extra[0]!="" ? 1:0)."', is_forced='".($input_extra[1]!="" ? 1:0)."' WHERE table_id='".$table_config['id']."' AND column_id='".$last_id."'";
														$connection->exec($sql);
													}else{
														$last_ordering=getNewOrderId('checkbox_options_setting');
														$sql="INSERT INTO ".$sub_name."checkbox_options_setting (table_id,column_id,is_multiple,is_forced,ordering,act) VALUES ('".$table_config['id']."','".$last_id."','".($input_extra[0]!="" ? 1:0)."','".($input_extra[1]!="" ? 1:0)."','".$last_ordering."',1)";
														$connection->exec($sql);
													}
												break;
											}
											echo "success_._".$last_id;
										}else{
											echo "error_._primary";
										}
									}else{
										echo "error_._name_taken";
									}
								}else{
									echo "error_._goToStep1";
								}
							}else{
								if(!ctype_alnum(str_replace("_","",$current_name))){
									echo "error_._bad_name_._current_name";
								}elseif(!ctype_alnum(str_replace("_","",$description_name_fa))){
									echo "error_._bad_name_._description_name_fa";
								}elseif(!ctype_alnum(str_replace("_","",$description_name_en))){
									echo "error_._bad_name_._description_name_en";
								}
							}
						}else{
							echo "error_._empty_name";
						}
					}
					if(isset($_GET["updateColumn"]) && isset($_POST["column_id"]) && isset($_POST["current_name"]) && isset($_POST["description_name_fa"]) && isset($_POST["description_info_fa"]) && isset($_POST["description_name_en"]) && isset($_POST["description_info_en"]) && isset($_POST["input_select"]) && isset($_POST["input_creatable"]) && isset($_POST["input_visible"]) && isset($_POST["input_editable"]) && isset($_POST["input_removable"]) && isset($_POST["input_visible_table"]) && isset($_POST["input_primary"]) && isset($_POST["input_important"]) && isset($_POST["input_extra"])){
						$column_id=$_POST['column_id'];
						$current_name=textCleaner($_POST['current_name']);
						$description_name_fa=textCleaner($_POST['description_name_fa']);
						$description_info_fa=textCleaner($_POST['description_info_fa']);
						$description_name_en=textCleaner($_POST['description_name_en']);
						$description_info_en=textCleaner($_POST['description_info_en']);
						$input_select=textCleaner($_POST['input_select']);
						$input_creatable=textCleaner($_POST['input_creatable']);
						$input_visible=textCleaner($_POST['input_visible']);
						$input_editable=textCleaner($_POST['input_editable']);
						$input_removable=textCleaner($_POST['input_removable']);
						$input_visible_table=textCleaner($_POST['input_visible_table']);
						$input_primary=textCleaner($_POST['input_primary']);
						$input_important=textCleaner($_POST['input_important']);
						$input_extra=json_decode($_POST['input_extra']);
						if($current_name!="" && $description_name_en!="" && $description_name_fa!=""){
							if(ctype_alnum(str_replace("_","",$current_name))){
								$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
								if($table_config!=0){
									$res_column_check=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE current_name='".$current_name."' AND table_id='".$table_config['id']."' AND id!='".$column_id."' OR new_name='".$current_name."' AND table_id='".$table_config['id']."' AND id!='".$column_id."'");
									if($res_column_check->rowCount()==0){
										$error=0;
										if($input_primary==1){
											$res_column_primary_check=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE primarys=1 AND table_id='".$table_config['id']."' AND id!='".$column_id."'");
											if($res_column_primary_check->rowCount()!=0){
												$error=1;
											}
										}
										if($error==0){
											$res_column_update=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$column_id."'");
											if($res_column_update->rowCount()!=0){
												$column_update=$res_column_update->fetch();
												$updater_name=($column_update['created']==0 ? "current_name":"new_name");
												$updater_mode=($column_update['created']==0 ? "mode":"new_mode");
												$connection->beginTransaction();
												$sql = "UPDATE ".$sub_name."table_column_config SET ".$updater_name."='".$current_name."', description_name_fa='".$description_name_fa."', description_info_fa='".$description_info_fa."', description_name_en='".$description_name_en."', description_info_en='".$description_info_en."', ".$updater_mode."='".$input_select."', creatable='".$input_creatable."', visible='".$input_visible."', editable='".$input_editable."', removable='".$input_removable."', visible_table='".$input_visible_table."', primarys='".$input_primary."', importants='".$input_important."' WHERE id='".$column_id."'";
												$connection->exec($sql);
												?><?php //info search this_is_modes_for_data_tables for see all things about this part ?><?php
												switch ($input_select) {//tables_mode_code
													//updatecolumn_create_tables_mode_php_extra
													case "3":case 3://info search case 3 for see all things about this part
														$res_yes_no=$connection->query("SELECT * FROM ".$sub_name."yes_no_question_options WHERE table_id='".$column_update['table_id']."' AND column_id='".$column_id."'");
														if($res_yes_no->rowCount()!=0){
															$sql="UPDATE ".$sub_name."yes_no_question_options SET yes_option='".$input_extra[0]."', no_option='".$input_extra[1]."', yes_value='".($input_extra[2]!="" ? $input_extra[2]:1)."', no_value='".($input_extra[3]!="" ? $input_extra[3]:0)."', yes_fa_icon='".$input_extra[4]."', no_fa_icon='".$input_extra[5]."' WHERE table_id='".$column_update['table_id']."' AND column_id='".$column_id."'";
															$connection->exec($sql);
														}else{
															$last_ordering=getNewOrderId('yes_no_question_options');
															$sql="INSERT INTO ".$sub_name."yes_no_question_options (table_id,column_id,yes_option,no_option,yes_value,no_value,yes_fa_icon,no_fa_icon,ordering,act) VALUES ('".$column_update['table_id']."','".$column_id."','".$input_extra[0]."','".$input_extra[1]."','".($input_extra[2]!="" ? $input_extra[2]:1)."','".($input_extra[3]!="" ? $input_extra[3]:0)."','".$input_extra[4]."','".$input_extra[5]."','".$last_ordering."',1)";
															$connection->exec($sql);
														}
													break;
													case "4":case 4://info search case 4 for see all things about this part
														$res_selectbox_setting=$connection->query("SELECT * FROM ".$sub_name."select_options_setting WHERE table_id='".$column_update['table_id']."' AND column_id='".$column_id."'");
														if($res_selectbox_setting->rowCount()){
															$sql="UPDATE ".$sub_name."select_options_setting SET is_multiple='".($input_extra[0]!="" ? 1:0)."', is_forced='".($input_extra[1]!="" ? 1:0)."', min_allowed='".intval($input_extra[2])."', max_allowed='".intval($input_extra[3])."' WHERE table_id='".$column_update['table_id']."' AND column_id='".$column_id."'";
															$connection->exec($sql);
														}else{
															$last_ordering=getNewOrderId('select_options_setting');
															$sql="INSERT INTO ".$sub_name."select_options_setting (table_id,column_id,is_multiple,is_forced,min_allowed,max_allowed,ordering,act) VALUES ('".$column_update['table_id']."','".$column_id."','".($input_extra[0]!="" ? 1:0)."','".($input_extra[1]!="" ? 1:0)."','".intval($input_extra[2])."','".intval($input_extra[3])."','".$last_ordering."',1)";
															$connection->exec($sql);
														}
													break;
													case "7":case 7://info search case 7 for see all things about this part
														$res_file_uploader_setting=$connection->query("SELECT * FROM ".$sub_name."file_uploader_setting WHERE table_id='".$column_update['table_id']."' AND column_id='".$column_id."'");
														if($res_file_uploader_setting->rowCount()!=0){
															$sql="UPDATE ".$sub_name."file_uploader_setting SET max_size='".$input_extra[0]."', allowed_type='".$input_extra[1]."' WHERE table_id='".$column_update['table_id']."' AND column_id='".$column_id."'";
															$connection->exec($sql);
														}else{
															$last_ordering=getNewOrderId('file_uploader_setting');
															$sql="INSERT INTO ".$sub_name."file_uploader_setting (table_id,column_id,max_size,allowed_type,ordering,act) VALUES ('".$column_update['table_id']."','".$column_id."','".$input_extra[0]."','".$input_extra[1]."','".$last_ordering."',1)";
															$connection->exec($sql);
														}
													break;
													case "9":case 9://info search case 9 for see all things about this part
														$res_selectbox_setting=$connection->query("SELECT * FROM ".$sub_name."checkbox_options_setting WHERE table_id='".$column_update['table_id']."' AND column_id='".$column_id."'");
														if($res_selectbox_setting->rowCount()!=0){
															$sql="UPDATE ".$sub_name."checkbox_options_setting SET is_multiple='".($input_extra[0]!="" ? 1:0)."', is_forced='".($input_extra[1]!="" ? 1:0)."' WHERE table_id='".$column_update['table_id']."' AND column_id='".$column_id."'";
															$connection->exec($sql);
														}else{
															$last_ordering=getNewOrderId('checkbox_options_setting');
															$sql="INSERT INTO ".$sub_name."checkbox_options_setting (table_id,column_id,is_multiple,is_forced,ordering,act) VALUES ('".$column_update['table_id']."','".$column_id."','".($input_extra[0]!="" ? 1:0)."','".($input_extra[1]!="" ? 1:0)."','".$last_ordering."',1)";
															$connection->exec($sql);
														}
													break;
												}
												if($connection->inTransaction()==true){
													if($connection->commit()){
														echo 'success';
													}else{
														echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
													}
												}else{
													echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
												}
											}else{
												echo 'alert_._<label class="data-text error-php" data-text-en="Column not found !" data-text-fa="ستون پیدا نشد !">'.($GLOBALS['user_language']=="en" ? "Column not found !":"ستون پیدا نشد !").'</label>';
											}
										}else{
											echo "error_._primary";
										}
									}else{
										echo "error_._name_taken";
									}
								}else{
									echo "error_._goToStep1";
								}
							}else{
								if(!ctype_alnum(str_replace("_","",$current_name))){
									echo "error_._bad_name_._current_name";
								}elseif(!ctype_alnum(str_replace("_","",$description_name_fa))){
									echo "error_._bad_name_._description_name_fa";
								}elseif(!ctype_alnum(str_replace("_","",$description_name_en))){
									echo "error_._bad_name_._description_name_en";
								}
							}
						}else{
							echo "error_._empty_name";
						}
					}
					if(isset($_GET["delete"]) && isset($_POST["id"])){
						$id=$_POST["id"];
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config!=0){
							$res_column=$connection->query("SELECT * FROM " . $sub_name . "table_column_config WHERE id='".$id."'");
							if($res_column->rowCount()){
								$column=$res_column->fetch();
								try{
									$connection->beginTransaction();
									$error=0;
									//Delete Table Operation
									$sql="DELETE FROM " . $sub_name . "table_column_config WHERE id='".$id."' AND table_id='".$table_config['id']."'";
									$connection->exec($sql);
									$show_tables=[];for($i=0;$i<count($connection->query("show tables")->fetchAll());$i++){$show_tables[count($show_tables)]=$connection->query("show tables")->fetchAll()[$i][0];}
									if(in_array($table_config['current_name'], $show_tables)){
										if (count($connection->query("SHOW COLUMNS FROM " . $table_config['current_name'] . " LIKE '".$column['current_name']."'")->fetchAll())) {
											if($column['mode']==4 && count($connection->query("SHOW COLUMNS FROM " . $table_config['current_name'] . " LIKE '".$column['current_name']."_justvalue"."'")->fetchAll())){//info search case 4 for see all things about this part
												$sql="ALTER TABLE " . $table_config['current_name'] . " DROP ".$column['current_name']."_justvalue";
												$connection->exec($sql);
											}
											$sql="ALTER TABLE " . $table_config['current_name'] . " DROP ".$column['current_name'];
											$connection->exec($sql);
										}
									}
									$res_fix_column_number=$connection->query("SELECT * FROM " . $sub_name . "table_column_config WHERE column_number>'".$column['column_number']."' AND table_id='".$table_config['id']."' ORDER BY column_number ASC");
									while($fix_column_number=$res_fix_column_number->fetch()){
										$new_column_number=$fix_column_number['column_number']-1;
										$new_ordering=$fix_column_number['ordering']-1;
										$sql="UPDATE " . $sub_name . "table_column_config SET column_number='".$new_column_number."', ordering='".$new_ordering."' WHERE id='".$fix_column_number['id']."'";
										$connection->exec($sql);
									}
									$sql="DELETE FROM " . $sub_name . "yes_no_question_options WHERE column_id='".$id."' AND table_id='".$column['table_id']."'";
									$connection->exec($sql);
									// ordering fixer
									$sql="DELETE FROM " . $sub_name . "select_options WHERE column_id='".$id."' AND table_id='".$column['table_id']."' OR connected_table='".$column['table_id']."' AND option_text='".$id."' OR connected_table='".$column['table_id']."' AND option_value='".$id."'";
									$connection->exec($sql);
									// ordering fixer
									$sql="DELETE FROM " . $sub_name . "select_options_setting WHERE column_id='".$id."' OR table_id='".$column['table_id']."'";
									$connection->exec($sql);
									// ordering fixer
									$sql="DELETE FROM " . $sub_name . "checkbox_options WHERE column_id='".$id."' AND table_id='".$column['table_id']."' OR connected_table='".$column['table_id']."' AND option_text='".$id."' OR connected_table='".$column['table_id']."' AND option_value='".$id."'";
									$connection->exec($sql);
									// ordering fixer
									$sql="DELETE FROM " . $sub_name . "checkbox_options_setting WHERE column_id='".$id."' OR table_id='".$column['table_id']."'";
									$connection->exec($sql);
									// ordering fixer
									$sql="DELETE FROM " . $sub_name . "file_uploader_setting WHERE column_id='".$id."' OR table_id='".$column['table_id']."'";
									$connection->exec($sql);
									// ordering fixer
									if($connection->inTransaction()==true && $error==0){
										if($connection->commit()){
											echo "success";
										}
									}
								}catch (Exception $e){

								}
							}
						}
					}
					if(isset($_GET["closeEditing"]) && isset($_POST["id"])){
						$id=$_POST["id"];
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config!=0){
							$res_column=$connection->query("SELECT * FROM " . $sub_name . "table_column_config WHERE id='".$id."'");
							if($res_column->rowCount()){
								$column=$res_column->fetch();
								$connection->beginTransaction();
								$sql="UPDATE " . $sub_name . "table_column_config SET editing=0 WHERE id='".$id."'";
								$connection->exec($sql);
								if($connection->inTransaction()==true){
									if($connection->commit()){
										echo "success";
									}
								}
							}
						}
					}
					if(isset($_GET["edit"]) && isset($_POST["id"])){
						$id=$_POST["id"];
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config!=0){
							$res_column=$connection->query("SELECT * FROM " . $sub_name . "table_column_config WHERE id='".$id."'");
							if($res_column->rowCount()){
								$column=$res_column->fetch();
								$connection->beginTransaction();
								$sql="UPDATE " . $sub_name . "table_column_config SET editing=1 WHERE id='".$id."'";
								$connection->exec($sql);
								if($connection->inTransaction()==true){
									if($connection->commit()){
										$input_extra="";
										if($column['created']!=1){
											$modeGet=$column['mode'];
										}else{
											if($column['new_mode']!=0){
												$modeGet=$column['new_mode'];
											}else if($column['mode']!=0){
												$modeGet=$column['mode'];
											}else{
												$modeGet=1;
											}
										}
										?><?php //info search this_is_modes_for_data_tables for see all things about this part ?><?php
										switch ($modeGet) {//tables_mode_code
											case "3":case 3://info search case 3 for see all things about this part
												$res_yes_no=$connection->query("SELECT * FROM ".$sub_name."yes_no_question_options WHERE table_id='".$column['table_id']."' AND column_id='".$column['id']."'");
												if($res_yes_no->rowCount()!=0){
													$yes_no=$res_yes_no->fetch();
													$yes_option=$yes_no['yes_option'];
													$no_option=$yes_no['no_option'];
													$yes_value=($yes_no['yes_value']!="" ? $yes_no['yes_value']:1);
													$no_value=($yes_no['no_value']!="" ? $yes_no['no_value']:0);
													$yes_fa_icon=$yes_no['yes_fa_icon'];
													$no_fa_icon=$yes_no['no_fa_icon'];
												}else{
													$yes_option='';
													$no_option='';
													$yes_value='';
													$no_value='';
													$yes_fa_icon='';
													$no_fa_icon='';
												}
												$input_extra=json_encode([$yes_option,$no_option,$yes_value,$no_value,$yes_fa_icon,$no_fa_icon]);
											break;
											case "4":case 4://info search case 4 for see all things about this part
												$res_selectbox_setting=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options_setting WHERE table_id='".$column['table_id']."' AND column_id='".$column['id']."'");
												if($res_selectbox_setting->rowCount()){
													$selectbox_setting=$res_selectbox_setting->fetch();
													$is_multiple=$selectbox_setting['is_multiple'];
													$is_forced=$selectbox_setting['is_forced'];
													$min_allowed=$selectbox_setting['min_allowed'];
													$max_allowed=$selectbox_setting['max_allowed'];
												}else{
													$is_multiple='';
													$is_forced='';
													$min_allowed='';
													$max_allowed='';
												}
												$input_extra=json_encode([$is_multiple,$is_forced,$min_allowed,$max_allowed]);
											break;
											case "7":case 7://info search case 7 for see all things about this part
												$res_file_uploader_setting=$connection->query("SELECT * FROM ".$sub_name."file_uploader_setting WHERE table_id='".$column['table_id']."' AND column_id='".$column['id']."'");
												if($res_file_uploader_setting->rowCount()!=0){
													$file_uploader_setting=$res_file_uploader_setting->fetch();
													$max_size=$file_uploader_setting['max_size'];
													$allowed_type=$file_uploader_setting['allowed_type'];
												}else{
													$max_size='';
													$allowed_type='';
												}
												$input_extra=json_encode([$max_size,$allowed_type]);
											break;
											case "9":case 9://info search case 9 for see all things about this part
												$res_selectbox_setting=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."checkbox_options_setting WHERE table_id='".$column['table_id']."' AND column_id='".$column['id']."'");
												if($res_selectbox_setting->rowCount()){
													$selectbox_setting=$res_selectbox_setting->fetch();
													$is_multiple=$selectbox_setting['is_multiple'];
													$is_forced=$selectbox_setting['is_forced'];
												}else{
													$is_multiple='';
													$is_forced='';
												}
												$input_extra=json_encode([$is_multiple,$is_forced]);
											break;
										}
										echo ($column['created']==0 ? $column['current_name']:($column['new_name'] ? $column['new_name']:$column['current_name']))."_._".$column['description_name_fa']."_._".$column['description_info_fa']."_._".$column['description_name_en']."_._".$column['description_info_en']."_._".$modeGet."_._".$column['creatable']."_._".$column['visible']."_._".$column['editable']."_._".$column['removable']."_._".$column['visible_table']."_._".$column['primarys']."_._".$column['importants']."_._".$input_extra."_._success";
									}
								}
							}
						}
					}
					if(isset($_GET['openAll'])){
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config){
							$res_editing=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_config['id']."' AND current_name!='ordering' AND current_name!='act' ORDER BY column_number ASC");
							while($editing=$res_editing->fetch()){
								echo $editing['id']."_._";
							}
							echo "success";
						}
					}
					if(isset($_GET['deleteAll'])){
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config){
							//Delete Table Operation
							$connection->exec("DELETE FROM ".$sub_name."table_column_config WHERE table_id='".$table_config['id']."' AND current_name!='ordering' AND current_name!='act'");
							// ordering fixer
							$connection->exec("DELETE FROM ".$sub_name."column_permissions WHERE table_id='".$table_config['id']."'");
							// ordering fixer
							$connection->exec("DELETE FROM ".$sub_name."yes_no_question_options WHERE table_id='".$table_config['id']."'");
							// ordering fixer
							$connection->exec("DELETE FROM ".$sub_name."select_options WHERE table_id='".$table_config['id']."'");
							// ordering fixer
							$connection->exec("DELETE FROM ".$sub_name."select_options_setting WHERE table_id='".$table_config['id']."'");
							// ordering fixer
							$connection->exec("DELETE FROM ".$sub_name."checkbox_options WHERE table_id='".$table_config['id']."'");
							// ordering fixer
							$connection->exec("DELETE FROM ".$sub_name."checkbox_options_setting WHERE table_id='".$table_config['id']."'");
							// ordering fixer
							$connection->exec("DELETE FROM ".$sub_name."file_uploader_setting WHERE table_id='".$table_config['id']."'");
							// ordering fixer
							$connection->exec("DELETE FROM ".$sub_name."table_property_config WHERE table_id='".$table_config['id']."'");
							// ordering fixer
							$connection->exec("DELETE FROM ".$sub_name."table_user_setting WHERE table_id='".$table_config['id']."'");
							// ordering fixer
							if($connection->inTransaction()==true){
								if($connection->commit()){
									echo "success";
								}
							}else{
								echo "success";
							}
						}
					}
					if(isset($_GET['getColumns']) && isset($_POST['table_id'])){
						$tables_id=$_POST['table_id'];

						if(in_array("-1",$tables_id) || in_array("_all",$tables_id)){
							$tables_id=[];
							$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_config");
							while($tables=$res_tables->fetch()){
								if(checkPermission(1,$tables['id'],"read",$tables['act'],null)==1){
									array_push($tables_id,$tables['id']);
								}
							}
						}
						?><option value="-1" class="data-text" data-text-fa="<?php print_r($data_text['fa']['a']); ?>" data-text-en="<?php print_r($data_text['en']['a']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['a']); ?></option><?php
						foreach ($tables_id as $key => $value) {
							$res_tabless=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE id='".$value."'");
							$tabless=$res_tabless->fetch();
						?>
							<optgroup label="<?php print_r($tabless['description_name_'.$GLOBALS['user_language']]); ?>" class="data-label" data-label-en="<?php print_r($tabless['description_name_en']); ?>" data-label-fa="<?php print_r($tabless['description_name_fa']); ?>">
								<option data-tokens="<?php print_r($tabless['description_name_en']); ?> <?php print_r($tabless['description_name_fa']); ?>" class="data-text" data-text-en="Select All of this table" data-text-fa="انتخاب همه از این جدول" value="selectall_<?php print_r($tabless['id']); ?>"><?php print_r(($GLOBALS['user_language']=="en" ? "Select All of this table":"انتخاب همه از این جدول"));?></option>
								<option data-tokens="<?php print_r($tabless['description_name_en']); ?> <?php print_r($tabless['description_name_fa']); ?>" class="data-text" data-text-en="Deselect All of this table" data-text-fa="لغو انتخاب همه از این جدول" value="deselectall_<?php print_r($tabless['id']); ?>"><?php print_r(($GLOBALS['user_language']=="en" ? "Deselect All of this table":"لغو انتخاب همه از این جدول"));?></option>
						<?php
							$res_column=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$value."' AND current_name!='ordering' AND current_name!='act'");
							while($column=$res_column->fetch()){
								if(checkPermission(2,$column['id'],"read",$column['act'],$value)==1){
						?>
							<option data-tokens="<?php print_r($tabless['description_name_en']); ?> <?php print_r($tabless['description_name_fa']); ?>" value="<?php print_r($column['id']); ?>" class="data-text column-table_id-<?php print_r($tabless['id']); ?>" data-text-fa="<?php print_r($column['description_name_fa']); ?>" data-text-en="<?php print_r($column['description_name_en']); ?>"><?php print_r($column['description_name_'.$GLOBALS['user_language']]); ?></option>
						<?php
								}
							}
						?>
							</optgroup>
						<?php
						}
					}
					if(isset($_GET["gotToStep3"])){
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config){
							$res_editing=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_config['id']."' AND current_name!='act' AND current_name!='ordering' ORDER BY column_number ASC");
							$amonter=$res_editing->rowCount();
							$amont=$connection->query("SELECT SUM(column_number) AS TOTAL FROM ".$sub_name."table_column_config WHERE table_id='".$table_config['id']."' ORDER BY column_number ASC")->fetch(PDO::FETCH_ASSOC);
							$amont=$amont['TOTAL'];
							if($amonter){
								$connection->beginTransaction();
								if((($amonter*($amonter+1))/2)!=$amont){
									$column_number_fixer=1;
									while($editing=$res_editing->fetch()){
										$column_number_fixer++;
										$connection->exec("UPDATE ".$sub_name."table_column_config SET column_number='".$column_number_fixer."' WHERE id='".$editing['id']."'");
									}
									$column_number_fixer++;
									$connection->exec("UPDATE ".$sub_name."table_column_config SET column_number='".$column_number_fixer."' WHERE current_name='act' AND table_id='".$table_config['id']."'");
								}
								if($connection->inTransaction()==true){
									if($connection->commit()){
										echo "success";
									}
								}
							}else{
								echo 'alert_._<label class="data-text error-php" data-text-en="At least one column is required !" data-text-fa="حداقل یک ستون لازم است">'.($GLOBALS['user_language']=="en" ? "At least one column is required !":"حداقل یک ستون لازم است").'</label>';
							}
						}
					}
					if(isset($_GET["column_numbers"]) && isset($_POST['numbers'])){
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config){
							$connection->beginTransaction();
							$numbers=$_POST["numbers"];
							$exploded=explode("_.._", $numbers);
							foreach ($exploded as &$value) {
								$update_data=explode("_._", $value);
								$connection->exec("UPDATE ".$sub_name."table_column_config SET column_number='-".$update_data[1]."' WHERE table_id='".$table_config['id']."' AND column_number='".$update_data[0]."'");
							}
							$connection->exec("UPDATE ".$sub_name."table_column_config SET column_number=abs(column_number) WHERE table_id='".$table_config['id']."' AND column_number<0");
							if($connection->inTransaction()==true){
								if($connection->commit()){
									echo "success";
								}
							}
							$res_editing=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_config['id']."' ORDER BY column_number ASC");
							$column_number_fixer=0;
							while($editing=$res_editing->fetch()){
								$column_number_fixer++;
								$connection->query("UPDATE ".$sub_name."table_column_config SET column_number='".$column_number_fixer."' WHERE id='".$editing['id']."'");
							}
						}
					}
					if(isset($_GET["goUp"]) && isset($_POST["id"])){
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config){
							$id=$_POST["id"];
							$column_config=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$id."'")->fetch();
							if($column_config['column_number']!=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_config['id']."' AND current_name!='ordering' AND current_name!='act' ORDER BY column_number ASC")->fetch()['column_number']){
								$update_column_number=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE column_number='".($column_config['column_number']-1)."' AND table_id='".$table_config['id']."'")->fetch();
								$connection->beginTransaction();
								$connection->exec("UPDATE ".$sub_name."table_column_config SET column_number='-".$update_column_number['column_number']."' WHERE id='".$id."'");
								$connection->exec("UPDATE ".$sub_name."table_column_config SET column_number='-".$column_config['column_number']."' WHERE id='".$update_column_number['id']."'");
								$connection->exec("UPDATE ".$sub_name."table_column_config SET column_number=abs(column_number) WHERE table_id='".$table_config['id']."' AND column_number<0");
								if($connection->inTransaction()==true){
									if($connection->commit()){
										echo "success";
									}
								}
							}
						}
					}
					if(isset($_GET["goDown"]) && isset($_POST["id"])){
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config){
							$id=$_POST["id"];
							$column_config=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$id."'")->fetch();
							if($column_config['column_number']!=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_config['id']."' AND current_name!='ordering' AND current_name!='act' ORDER BY column_number DESC")->fetch()['column_number']){
								$update_column_number=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE column_number='".($column_config['column_number']+1)."' AND table_id='".$table_config['id']."'")->fetch();
								$connection->beginTransaction();
								$connection->exec("UPDATE ".$sub_name."table_column_config SET column_number='-".$update_column_number['column_number']."' WHERE id='".$id."'");
								$connection->exec("UPDATE ".$sub_name."table_column_config SET column_number='-".$column_config['column_number']."' WHERE id='".$update_column_number['id']."'");
								$connection->exec("UPDATE ".$sub_name."table_column_config SET column_number=abs(column_number) WHERE table_id='".$table_config['id']."' AND column_number<0");
								if($connection->inTransaction()==true){
									if($connection->commit()){
										echo "success";
									}
								}
							}
						}
					}
					if(isset($_GET["create"])){
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config){
							function database_manager($table_name,$table_columns,$clear_columns,$insert_columns){
								$GLOBALS['show_tables']=[];
								for($i=0;$i<count($GLOBALS['connection']->query("show tables")->fetchAll());$i++){$GLOBALS['show_tables'][count($GLOBALS['show_tables'])]=$GLOBALS['connection']->query("show tables")->fetchAll()[$i][0];}
								$create_column_sql="";
								for($i=0;$i<count($table_columns);$i++){
									switch ($table_columns[$i][1]) {
										case 0:
											$create_column_sql.=$table_columns[$i][0]." int(11) NOT NULL, ";
										break;
										case 1:
											$create_column_sql.=$table_columns[$i][0]." text COLLATE utf8_persian_ci NOT NULL, ";
										break;
										default:
											$create_column_sql.=$table_columns[$i][0]." text COLLATE utf8_persian_ci NOT NULL, ";
										break;
									}
								}
								if(!in_array($GLOBALS['sub_name'].$table_name, $GLOBALS['show_tables'])){
									try{
										$GLOBALS['connection']->beginTransaction();
										$GLOBALS['connection']->exec("CREATE TABLE ".$GLOBALS['sub_name'].$table_name." (
											id int(11) NOT NULL,
											".$create_column_sql."
											ordering int(11) NOT NULL,
											act int(11) NOT NULL
										) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;");
										$GLOBALS['connection']->exec("ALTER TABLE ".$GLOBALS['sub_name'].$table_name." ADD PRIMARY KEY (id);");
										$GLOBALS['connection']->exec("ALTER TABLE ".$GLOBALS['sub_name'].$table_name." MODIFY id int(11) NOT NULL AUTO_INCREMENT;");
										if(isset($insert_columns) && !empty($insert_columns) && $insert_columns!=""){
											for($i=0;$i<count($insert_columns);$i++){
												$GLOBALS['connection']->exec("INSERT INTO ".$GLOBALS['sub_name'].$table_name." ".$insert_columns[$i][0]);
											}
										}
										if($GLOBALS['connection']->inTransaction()==true){
											$GLOBALS['connection']->commit();
										}
									}catch (PDOException $e){
										echo $e->getMessage();
										$GLOBALS['error_det']=1;
										if($GLOBALS['connection']->inTransaction()==true){
											$GLOBALS['connection']->rollback();
										}
										try{
											//grey #duplicate-1
											$GLOBALS['connection']->exec("DELETE FROM ".$GLOBALS['sub_name']."table_column_config WHERE table_id='".$GLOBALS['table_config']['id']."'");
											// ordering fixer
											$GLOBALS['connection']->exec("DELETE FROM ".$GLOBALS['sub_name']."yes_no_question_options WHERE table_id='".$GLOBALS['table_config']['id']."'");
											// ordering fixer
											$GLOBALS['connection']->exec("DELETE FROM ".$GLOBALS['sub_name']."select_options WHERE table_id='".$GLOBALS['table_config']['id']."'");
											// ordering fixer
											$GLOBALS['connection']->exec("DELETE FROM ".$GLOBALS['sub_name']."column_permissions WHERE table_id='".$GLOBALS['table_config']['id']."'");
											// ordering fixer
											$GLOBALS['connection']->exec("DELETE FROM ".$GLOBALS['sub_name']."table_permissions WHERE table_id='".$GLOBALS['table_config']['id']."'");
											// ordering fixer
											$GLOBALS['connection']->exec("DELETE FROM ".$GLOBALS['sub_name']."table_property_config WHERE table_id='".$GLOBALS['table_config']['id']."'");
											// ordering fixer
											$GLOBALS['connection']->exec("DELETE FROM ".$GLOBALS['sub_name']."table_user_setting WHERE table_id='".$GLOBALS['table_config']['id']."'");
											// ordering fixer
											$GLOBALS['connection']->exec("DELETE FROM ".$GLOBALS['sub_name']."table_config WHERE id='".$GLOBALS['table_config']['id']."'");
											// ordering fixer
											//grey #duplicate-1
											if(in_array($GLOBALS['sub_name'].$table_name, $GLOBALS['connection']->query("show tables")->fetchAll())){
												if($GLOBALS['connection']->exec("DROP TABLE ".$GLOBALS['sub_name'].$table_name)){
													if($GLOBALS['connection']->commit()){
														echo "unable_create_".$table_name;
													}else{
														echo "unable_create_".$table_name;
													}
												}else{
													echo "unable_create_".$table_name;
												}
											}else{
												echo "unable_create_".$table_name;
											}
										}catch (PDOException $e){
											echo "unable_create_".$table_name;
										}
									}
								}else{
									if(isset($clear_columns) && !empty($clear_columns) && $clear_columns!=""){
										try{
											$GLOBALS['connection']->beginTransaction();
											$GLOBALS['connection']->exec("DELETE FROM ".$GLOBALS['sub_name'].$table_name." WHERE ".$clear_columns);
											$GLOBALS['connection']->commit();
										}catch (PDOException $e){
											$GLOBALS['error_det']=1;
											$GLOBALS['connection']->rollback();
											try{
												if(in_array($GLOBALS['sub_name'].$table_name, $GLOBALS['connection']->query("show tables")->fetchAll())){
													if($GLOBALS['connection']->exec("DROP TABLE ".$GLOBALS['sub_name'].$table_name)){
														if($GLOBALS['connection']->commit()){
															echo "unable_clear_".$table_name;
														}else{
															echo "unable_clear_".$table_name;
														}
													}else{
														echo "unable_clear_".$table_name;
													}
												}else{
													echo "unable_clear_".$table_name;
												}
											}catch (PDOException $e){
												echo "unable_clear_".$table_name;
											}
										}
									}
									try{
										$table_avaible_columns=$GLOBALS['connection']->prepare("DESCRIBE ".$GLOBALS['sub_name'].$table_name);
										$table_avaible_columns->execute();
										$table_avaible_columns = $table_avaible_columns->fetchAll(PDO::FETCH_COLUMN);
										$GLOBALS['connection']->beginTransaction();
										for($i=0;$i<count($table_columns);$i++){
											if(!in_array($table_columns[$i][0], $table_avaible_columns)){
												switch ($table_columns[$i][1]) {
													case 0:
														$GLOBALS['connection']->exec("ALTER TABLE ".$GLOBALS['sub_name'].$table_name." ADD ".$table_columns[$i][0]." INT NOT NULL");
													break;
													case 1:
														$GLOBALS['connection']->exec("ALTER TABLE ".$GLOBALS['sub_name'].$table_name." ADD ".$table_columns[$i][0]." TEXT NOT NULL");
													break;
													default:
														$GLOBALS['connection']->exec("ALTER TABLE ".$GLOBALS['sub_name'].$table_name." ADD ".$table_columns[$i][0]." TEXT NOT NULL");
													break;
												}
											}
										}
										if(isset($insert_columns) && !empty($insert_columns) && $insert_columns!=""){
											for($i=0;$i<count($insert_columns);$i++){
												if(isset($insert_columns[$i][0]) && $insert_columns[$i][0]!=""){
													if(isset($insert_columns[$i][1]) && $insert_columns[$i][1]!=""){
														if($GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name'].$table_name." WHERE ".$insert_columns[$i][1])->rowCount()==0){
															$GLOBALS['connection']->exec("INSERT INTO ".$GLOBALS['sub_name'].$table_name." ".$insert_columns[$i][0]);
														}else if(isset($insert_columns[$i][2]) && $insert_columns[$i][2]!=""){
															$GLOBALS['connection']->exec("UPDATE ".$GLOBALS['sub_name'].$table_name." SET ".$insert_columns[$i][2]." WHERE ".$insert_columns[$i][1]);
														}
													}else{
														$GLOBALS['connection']->exec("INSERT INTO ".$GLOBALS['sub_name'].$table_name." ".$insert_columns[$i][0]);
													}
												}
											}
										}
										$GLOBALS['connection']->commit();
									}catch (PDOException $e){
										echo $e->getMessage();
										$GLOBALS['error_det']=1;
										$GLOBALS['connection']->rollback();
										try{
											if(in_array($GLOBALS['sub_name'].$table_name, $GLOBALS['connection']->query("show tables")->fetchAll())){
												if($GLOBALS['connection']->exec("DROP TABLE ".$GLOBALS['sub_name'].$table_name)){
													if($GLOBALS['connection']->commit()){
														echo "unable_alert_".$table_name;
													}else{
														echo "unable_alert_".$table_name;
													}
												}else{
													echo "unable_alert_".$table_name;
												}
											}else{
												echo "unable_alert_".$table_name;
											}
										}catch (PDOException $e){
											echo "unable_alert_".$table_name;
										}
									}
								}
							}
							if($table_config['created']==0){
								$connection->beginTransaction();
								$last_ordering=getLastItemByOrdering('table_column_config')['ordering']+1;
								$sql = "INSERT INTO ".$sub_name."table_column_config (table_id, current_name, new_name, column_number, description_name_fa, description_info_fa, description_name_en, description_info_en, created, creatable, visible, editable, removable, visible_table, create_power, read_power, update_power, delete_power, no_power, mode, new_mode, editing, primarys, importants, ordering, act) VALUES ('".$table_config['id']."','ordering','',1,'مرتب سازی','شمارش گر برای مرتب سازی','Ordering','Counter for sorting','1','".$table_config['creatable']."','".$table_config['visible']."','".$table_config['editable']."','".$table_config['removable']."','0','0','0','0','0','0','2',0,1,0,0,'".$last_ordering."',1)";
								$connection->exec($sql);
								$last_column_number=getLastColumnNumber($table_config['id']);
								$last_ordering=getLastItemByOrdering('table_column_config')['ordering']+2;
								$sql = "INSERT INTO ".$sub_name."table_column_config (table_id, current_name, new_name, column_number, description_name_fa, description_info_fa, description_name_en, description_info_en, created, creatable, visible, editable, removable, visible_table, create_power, read_power, update_power, delete_power, no_power, mode, new_mode, editing, primarys, importants, ordering, act) VALUES ('".$table_config['id']."','act','','".$last_column_number."','وضعیت','کنترل وضعیت و دسترسی','Status','Status and access control','1','".$table_config['creatable']."','".$table_config['visible']."','".$table_config['editable']."','".$table_config['removable']."','0','0','0','0','0','0','3',0,1,0,0,'".$last_ordering."',1)";
								$columns_array=[];
								$res_table_column_config=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_config['id']."' AND created=0");
								while($table_column_config=$res_table_column_config->fetch()){
									$connection->exec("UPDATE ".$sub_name."table_column_config SET created=1 WHERE id='".$table_column_config['id']."'");
									$columns_array[count($columns_array)]=[$table_column_config['current_name'],$table_column_config['mode']];
									if($table_column_config['mode']=="4"){//info search case 4 for see all things about this part
										$columns_array[count($columns_array)]=[$table_column_config['current_name']."_justvalue",$table_column_config['mode']];
									}
								}
								$connection->exec($sql);
								try{
									if($connection->inTransaction()!=true){
										$connection->beginTransaction();
									}
									$connection->exec("UPDATE ".$sub_name."table_config SET created=1 WHERE id='".$table_config['id']."'");
									if($connection->inTransaction()==true){
										if($connection->commit()){
											database_manager(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_config['current_name'], 1),$columns_array,"","");
											echo "success";
										}
									}
								}catch(PDOException $e){
									if($connection->inTransaction()==true){
										$connection->rollBack();
									}
								}
							}else{
								$res_table_column_config=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_config['id']."' AND created=0");
								while($table_column_config=$res_table_column_config->fetch()){
									$connection->exec("UPDATE ".$sub_name."table_column_config SET created=1 WHERE id='".$table_column_config['id']."'");
									$mode=$connection->query("SELECT * FROM ".$sub_name."table_column_mode WHERE id='".$table_column_config['mode']."'")->fetch()['mode'];
									$connection->exec("ALTER TABLE ".$table_config['current_name']." ADD ".$table_column_config['current_name']." ".($mode==0 ? "INT":"TEXT")." NOT NULL;");
									$searching_just_value=$GLOBALS['connection']->prepare("DESCRIBE ".$table_config['current_name']);
									$searching_just_value->execute();
									$searching_just_value = $searching_just_value->fetchAll(PDO::FETCH_COLUMN);
									if($table_column_config['mode']=="4"){//info search case 4 for see all things about this part
										$connection->exec("ALTER TABLE ".$table_config['current_name']." ADD ".$table_column_config['current_name']."_justvalue ".($mode==0 ? "INT":"TEXT")." NOT NULL;");
									}
								}

								$res_table_column_config=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_config['id']."' AND created=1 AND new_name!='' AND new_name!=current_name");
								while($table_column_config=$res_table_column_config->fetch()){
									$connection->exec("UPDATE ".$sub_name."table_column_config SET new_name='', current_name='".$table_column_config['new_name']."' WHERE id='".$table_column_config['id']."'");
									$mode=$connection->query("SELECT * FROM ".$sub_name."table_column_mode WHERE id='".$table_column_config['mode']."'")->fetch()['mode'];
									$searching_just_value=$GLOBALS['connection']->prepare("DESCRIBE ".$table_config['current_name']);
									$searching_just_value->execute();
									$searching_just_value = $searching_just_value->fetchAll(PDO::FETCH_COLUMN);
									if($table_column_config['mode']=="4"){//info search case 4 for see all things about this part
										if(in_array($table_column_config['current_name']."_justvalue", $searching_just_value)){
											$connection->exec("ALTER TABLE ".$table_config['current_name']." CHANGE ".$table_column_config['current_name']."_justvalue ".$table_column_config['new_name']."_justvalue"." ".($mode==0 ? "INT(11)":"TEXT")." NOT NULL;");
										}else{
											$connection->exec("ALTER TABLE ".$table_config['current_name']." ADD ".$table_column_config['current_name']."_justvalue ".($mode==0 ? "INT":"TEXT")." NOT NULL;");
										}
									}
									$connection->exec("ALTER TABLE ".$table_config['current_name']." CHANGE ".$table_column_config['current_name']." ".$table_column_config['new_name']." ".($mode==0 ? "INT(11)":"TEXT")." NOT NULL;");
								}

								$res_table_column_config=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_config['id']."' AND created=1 AND new_mode!='' AND new_mode!=mode");
								while($table_column_config=$res_table_column_config->fetch()){
									$connection->exec("UPDATE ".$sub_name."table_column_config SET mode='".$table_column_config['new_mode']."', new_mode=0 WHERE id='".$table_column_config['id']."'");
									$mode=$connection->query("SELECT * FROM ".$sub_name."table_column_mode WHERE id='".$table_column_config['mode']."'")->fetch()['mode'];
									$searching_just_value=$GLOBALS['connection']->prepare("DESCRIBE ".$table_config['current_name']);
									$searching_just_value->execute();
									$searching_just_value = $searching_just_value->fetchAll(PDO::FETCH_COLUMN);
									if($table_column_config['mode']=="4"){//info search case 4 for see all things about this part
										if(in_array($table_column_config['current_name']."_justvalue", $searching_just_value)){
											$connection->exec("ALTER TABLE ".$table_config['current_name']." CHANGE ".$table_column_config['current_name']."_justvalue ".$table_column_config['new_name']."_justvalue"." ".($mode==0 ? "INT(11)":"TEXT")." NOT NULL;");
										}else{
											$connection->exec("ALTER TABLE ".$table_config['current_name']." ADD ".$table_column_config['current_name']."_justvalue ".($mode==0 ? "INT":"TEXT")." NOT NULL;");
										}
									}
									$connection->exec("ALTER TABLE ".$table_config['current_name']." CHANGE ".$table_column_config['current_name']." ".$table_column_config['new_name']." ".($mode==0 ? "INT(11)":"TEXT")." NOT NULL;");
								}

								if($table_config['new_name']!="" && $table_config['current_name']!=$table_config['new_name']){
									$connection->exec("UPDATE ".$sub_name."table_config SET current_name='".$table_config['new_name']."', new_name='' WHERE id='".$table_config['id']."'");
									$connection->exec("RENAME TABLE ".$table_config['current_name']." TO ".$table_config['new_name'].";");
								}

								echo "success";
							}
						}
					}
					if(isset($_GET["skip"])){
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config){
							$connection->beginTransaction();
							$connection->exec("UPDATE ".$sub_name."table_config SET lock_admin_id='0' WHERE id='".$table_config['id']."'");
							if($connection->inTransaction()==true){
								if($connection->commit()){
									echo "success";
								}else if($table_config['lock_admin_id']==0){
									echo "success";
								}
							}else if($table_config['lock_admin_id']==0){
								echo "success";
							}
						}else{
							echo "success";
						}
					}
					if(isset($_GET["delete_table"])){
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config){
							try{
								$connection->beginTransaction();
								//Delete Table Operation
								$connection->exec("DELETE FROM ".$sub_name."table_column_config WHERE table_id='".$table_config['id']."'");
								// ordering fixer
								$connection->exec("DELETE FROM ".$sub_name."column_permissions WHERE table_id='".$table_config['id']."'");
								// ordering fixer
								$connection->exec("DELETE FROM ".$sub_name."yes_no_question_options WHERE table_id='".$table_config['id']."'");
								// ordering fixer
								$connection->exec("DELETE FROM ".$sub_name."select_options WHERE table_id='".$table_config['id']."'");
								// ordering fixer
								$connection->exec("DELETE FROM ".$sub_name."select_options_setting WHERE table_id='".$table_config['id']."'");
								// ordering fixer
								$connection->exec("DELETE FROM ".$sub_name."checkbox_options WHERE table_id='".$table_config['id']."'");
								// ordering fixer
								$connection->exec("DELETE FROM ".$sub_name."checkbox_options_setting WHERE table_id='".$table_config['id']."'");
								// ordering fixer
								$connection->exec("DELETE FROM ".$sub_name."file_uploader_setting WHERE table_id='".$table_config['id']."'");
								// ordering fixer
								$connection->exec("DELETE FROM ".$sub_name."table_permissions WHERE table_id='".$table_config['id']."'");
								// ordering fixer
								$connection->exec("DELETE FROM ".$sub_name."table_property_config WHERE table_id='".$table_config['id']."'");
								// ordering fixer
								$connection->exec("DELETE FROM ".$sub_name."table_user_setting WHERE table_id='".$table_config['id']."'");
								// ordering fixer
								$connection->exec("DELETE FROM ".$sub_name."table_config WHERE id='".$table_config['id']."'");
								// ordering fixer
								$show_tables=[];for($i=0;$i<count($check_tables_connection->query("show tables")->fetchAll());$i++){$show_tables[count($show_tables)]=$check_tables_connection->query("show tables")->fetchAll()[$i][0];}
								if(in_array($table_config['current_name'], $show_tables)){
									$connection->exec("DROP TABLE ".$table_config['current_name']);
								}
								if($connection->inTransaction()==true){
									if($connection->commit()){
										echo "success";
									}
								}else{
									echo "success";
								}
							}catch (PDOException $e){
								echo 'alert_._<label class="error error-php">'.$e->getMessage().'</label>';
								if($connection->inTransaction()==true){
									$connection->rollBack();
								}
							}
						}else{
							echo "success";
						}
					}
					if(isset($_GET["enable_table"]) && isset($_POST["name"])){
						$connection->beginTransaction();
						$is_free=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE id='".$_POST["name"]."' AND (lock_admin_id=0 || lock_admin_id='')")->rowCount();
						if($is_free || $op_admin){
							$connection->exec("UPDATE ".$sub_name."table_config SET lock_admin_id='0' WHERE lock_admin_id='".$_SESSION["username"]."'");
							$connection->exec("UPDATE ".$sub_name."table_config SET lock_admin_id='".$_SESSION["username"]."' WHERE id='".$_POST["name"]."'");
							if($connection->inTransaction()==true){
								if($connection->commit()){
									echo "success";
								}else{
									$connection->rollBack();
									echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
								}
							}
						}else{
							echo 'alert_._<label class="data-text error-php" data-text-en="This table is currently being edited !" data-text-fa="این جدول در حال حاضر در حال ویرایش است !">'.($GLOBALS['user_language']=="en" ? "This table is currently being edited !":"این جدول در حال حاضر در حال ویرایش است !").'</label>';
						}
					}
					if(isset($_GET["changeMode"]) && isset($_POST["id"]) && isset($_POST["mode"])){
						$id=$_POST["id"];
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config!=0){
							$res_column=$connection->query("SELECT * FROM " . $sub_name . "table_column_config WHERE id='".$id."'");
							if($res_column->rowCount()){
								$column=$res_column->fetch();
								$connection->beginTransaction();
								$sql="UPDATE " . $sub_name . "table_column_config SET editing=1 WHERE id='".$id."'";
								$connection->exec($sql);
								if($connection->inTransaction()==true){
									if($connection->commit()){
										$input_extra="";
										?><?php //info search this_is_modes_for_data_tables for see all things about this part ?><?php
										switch ($_POST["mode"]) {//tables_mode_code
											case "3":case 3://info search case 3 for see all things about this part
												$res_yes_no=$connection->query("SELECT * FROM ".$sub_name."yes_no_question_options WHERE table_id='".$column['table_id']."' AND column_id='".$column['id']."'");
												if($res_yes_no->rowCount()!=0){
													$yes_no=$res_yes_no->fetch();
													$yes_option=$yes_no['yes_option'];
													$no_option=$yes_no['no_option'];
													$yes_value=($yes_no['yes_value']!="" ? $yes_no['yes_value']:1);
													$no_value=($yes_no['no_value']!="" ? $yes_no['no_value']:0);
													$yes_fa_icon=$yes_no['yes_fa_icon'];
													$no_fa_icon=$yes_no['no_fa_icon'];
												}else{
													$yes_option='';
													$no_option='';
													$yes_value='';
													$no_value='';
													$yes_fa_icon='';
													$no_fa_icon='';
												}
												$input_extra=json_encode([$yes_option,$no_option,$yes_value,$no_value,$yes_fa_icon,$no_fa_icon]);
											break;
											case "4":case 4://info search case 4 for see all things about this part
												$res_selectbox_setting=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options_setting WHERE table_id='".$column['table_id']."' AND column_id='".$column['id']."'");
												if($res_selectbox_setting->rowCount()){
													$selectbox_setting=$res_selectbox_setting->fetch();
													$is_multiple=$selectbox_setting['is_multiple'];
													$is_forced=$selectbox_setting['is_forced'];
													$min_allowed=$selectbox_setting['min_allowed'];
													$max_allowed=$selectbox_setting['max_allowed'];
												}else{
													$is_multiple='';
													$is_forced='';
													$min_allowed='';
													$max_allowed='';
												}
												$input_extra=json_encode([$is_multiple,$is_forced,$min_allowed,$max_allowed]);
											break;
											case "7":case 7://info search case 7 for see all things about this part
												$res_file_uploader_setting=$connection->query("SELECT * FROM ".$sub_name."file_uploader_setting WHERE table_id='".$column['table_id']."' AND column_id='".$column['id']."'");
												if($res_file_uploader_setting->rowCount()!=0){
													$file_uploader_setting=$res_file_uploader_setting->fetch();
													$max_size=$file_uploader_setting['max_size'];
													$allowed_type=$file_uploader_setting['allowed_type'];
												}else{
													$max_size='';
													$allowed_type='';
												}
												$input_extra=json_encode([$max_size,$allowed_type]);
											break;
											case "9":case 9://info search case 9 for see all things about this part
												$res_selectbox_setting=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."checkbox_options_setting WHERE table_id='".$column['table_id']."' AND column_id='".$column['id']."'");
												if($res_selectbox_setting->rowCount()){
													$selectbox_setting=$res_selectbox_setting->fetch();
													$is_multiple=$selectbox_setting['is_multiple'];
													$is_forced=$selectbox_setting['is_forced'];
												}else{
													$is_multiple='';
													$is_forced='';
												}
												$input_extra=json_encode([$is_multiple,$is_forced]);
											break;
										}
										echo $input_extra."_._success";
									}
								}
							}
						}
					}
					if(isset($_GET["select_option_loader"]) && isset($_POST["column_id"]) && isset($_POST["connected_table"])){
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config!=0){
							$table_id=$table_config['id'];
							$column_id=$_POST["column_id"];
							$connected_table=$_POST["connected_table"];
							?>
								<option class="data-text" value="0" data-text-en="ID" data-text-fa="آیدی">
									<?php print_r($GLOBALS['user_language']=="en" ? "ID":"آیدی"); ?>
								</option>
							<?php
							$res_columns=$connection->query("SELECT * FROM " . $sub_name . "table_column_config WHERE table_id='".$connected_table."'");
							while ($columns=$res_columns->fetch()) {
							?>
								<option class="data-text" value="<?php print_r($columns['id']); ?>" data-text-en="<?php print_r($columns['description_name_en']); ?>" data-text-fa="<?php print_r($columns['description_name_fa']); ?>">
									<?php print_r($GLOBALS['user_language']=="en" ? $columns['description_name_en']:$columns['description_name_fa']); ?>
								</option>
							<?php
							}
						}
					}
					if(isset($_GET["selectOption"]) && isset($_POST["column_id"])){
						$error=0;
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config!=0){
							$table_id=$table_config['id'];
							$column_id=$_POST["column_id"];
							switch ($_GET["selectOption"]) {
								case 'add':
									if (isset($_POST["is_optgroup_opt"]) && isset($_POST["connected_table"]) && isset($_POST["connected_name"]) && isset($_POST["connected_value"]) && isset($_POST["option_name"]) && isset($_POST["option_value"]) && isset($_POST["optgroup_id"]) && isset($_POST["optgroup_text"]) && isset($_POST["new_optgroup_text"])) {
										if($_POST["is_optgroup_opt"]==1 && $_POST["optgroup_text"]!="" && !empty($_POST["optgroup_text"])){
											try {
												if($connection->inTransaction()!=true){
													$connection->beginTransaction();
												}
												$new_order_id=getNewOrderId("select_options");
												$connection->exec("INSERT INTO ".$sub_name."select_options (table_id,column_id,is_optgroup,optgroup_id,connected_table,option_text,option_value,ordering,act) VALUES ('".$table_id."','".$column_id."',1,'','','".$_POST["optgroup_text"]."','-','".$new_order_id."',1)");
												if($connection->inTransaction()==true){
													if($connection->commit()){
														echo "success";
													}
												}
											} catch (Exception $e) {
												if($connection->inTransaction()==true){
													$connection->rollBack();
												}
											}
										}elseif($_POST["is_optgroup_opt"]!=1){
											try {
												if($connection->inTransaction()!=true){
													$connection->beginTransaction();
												}
												$is_optgroup=$_POST["is_optgroup_opt"];
												$optgroup_id=$_POST["optgroup_id"];
												$optgroup_id=($connection->query("SELECT * FROM ".$sub_name."select_options WHERE id='".$_POST["optgroup_id"]."'")->rowCount()!=0 ? $optgroup_id:$optgroup_id);
												if($optgroup_id=="*"){
													if($_POST["new_optgroup_text"]!="" && !empty($_POST["new_optgroup_text"])){
														try{
															$new_order_id=getNewOrderId("select_options");
															$connection->exec("INSERT INTO ".$sub_name."select_options (table_id,column_id,is_optgroup,optgroup_id,connected_table,option_text,option_value,ordering,act) VALUES ('".$table_id."','".$column_id."',1,'','','".$_POST["new_optgroup_text"]."','','".$new_order_id."',1)");
															$optgroup_id=$connection->lastInsertId();
														}catch (Exception $e){
															$connection->rollback();
															$error=1;
															echo "error";
														}
													}else{
														echo "error";
													}
												}
												if($error==0){
													$connected_table=$_POST["connected_table"];
													$option_text=($connected_table==0 ? $_POST["option_name"]:$_POST["connected_name"]);
													$option_value=($connected_table==0 ? $_POST["option_value"]:$_POST["connected_value"]);
													$new_order_id=getNewOrderId("select_options");
													if($option_text!="" && !empty($option_text) && ($option_value!="" && !empty($option_value) || $option_value===0 || $option_value==="0") || $connected_table!=0){
														$connection->exec("INSERT INTO ".$sub_name."select_options (table_id,column_id,is_optgroup,optgroup_id,connected_table,option_text,option_value,ordering,act) VALUES ('".$table_id."','".$column_id."','".$is_optgroup."','".$optgroup_id."','".$connected_table."','".$option_text."','".$option_value."','".$new_order_id."',1)");
														if($connection->inTransaction()==true){
															if($connection->commit()){
																echo "success".$optgroup_id;
															}
														}
													}else{
														echo "error";
													}
												}
											} catch (Exception $e) {
												if($connection->inTransaction()==true){
													$connection->rollBack();
												}
											}
										}
									}
								break;
								case 'delete':
									try {
										if($connection->inTransaction()!=true){
											$connection->beginTransaction();
										}
										$id=$connection->query("SELECT * FROM ".$sub_name."select_options WHERE id='".$_POST["option_id"]."' AND column_id='".$column_id."' AND table_id='".$table_id."'");
										$id=($id->rowCount() ? $id->fetch()['id']:0);
										$connection->exec("UPDATE ".$sub_name."select_options SET optgroup_id='-' WHERE optgroup_id='".$id."'");
										$connection->exec("DELETE FROM ".$sub_name."select_options WHERE id='".$_POST["option_id"]."' AND column_id='".$column_id."' AND table_id='".$table_id."'");
										if($connection->inTransaction()==true){
											if($connection->commit()){
												echo "success";
											}
										}
									} catch (Exception $e) {
										if($connection->inTransaction()==true){
											$connection->rollBack();
										}
									}
								break;
								case 'save':
									if (isset($_POST["option_id"]) && isset($_POST["is_optgroup_opt"]) && isset($_POST["connected_table"]) && isset($_POST["connected_name"]) && isset($_POST["connected_value"]) && isset($_POST["option_name"]) && isset($_POST["option_value"]) && isset($_POST["optgroup_id"]) && isset($_POST["optgroup_text"]) && isset($_POST["new_optgroup_text"])) {
										if($connection->query("SELECT * FROM ".$sub_name."select_options WHERE id='".$_POST["option_id"]."'")->rowCount()!=0){
											if($_POST["is_optgroup_opt"]==1 && $_POST["optgroup_text"]!="" && !empty($_POST["optgroup_text"])){
												try {
													if($connection->inTransaction()!=true){
														$connection->beginTransaction();
													}
													$connection->exec("UPDATE ".$sub_name."select_options SET is_optgroup=1, optgroup_id='', connected_table='".$_POST["connected_table"]."', option_text='".$_POST["optgroup_text"]."' WHERE id='".$_POST["option_id"]."' AND table_id='".$table_id."' AND column_id='".$column_id."'");
													if($connection->inTransaction()==true){
														if($connection->commit()){
															echo "success";
														}
													}
												} catch (Exception $e) {
													if($connection->inTransaction()==true){
														$connection->rollBack();
													}
												}
											}elseif($_POST["is_optgroup_opt"]!=1){
												try {
													if($connection->inTransaction()!=true){
														$connection->beginTransaction();
													}
													$is_optgroup=$_POST["is_optgroup_opt"];
													$optgroup_id=$_POST["optgroup_id"];
													if($optgroup_id=="*"){
														if($_POST["new_optgroup_text"]!="" && !empty($_POST["new_optgroup_text"])){
															try{
																$new_order_id=getNewOrderId("select_options");
																$connection->exec("INSERT INTO ".$sub_name."select_options (table_id,column_id,is_optgroup,optgroup_id,connected_table,option_text,option_value,ordering,act) VALUES ('".$table_id."','".$column_id."',1,'','','".$_POST["new_optgroup_text"]."','','".$new_order_id."',1)");
																$optgroup_id=$connection->lastInsertId();
															}catch (Exception $e){
																$connection->rollback();
																$error=1;
																echo "error";
															}
														}else{
															echo "error";
														}
													}
													$optgroup_id=($connection->query("SELECT * FROM ".$sub_name."select_options WHERE id='".$_POST["optgroup_id"]."'")->rowCount()!=0 ? $optgroup_id:$optgroup_id);
													if($error==0){
														$connected_table=$_POST["connected_table"];
														$option_text=($connected_table==0 ? $_POST["option_name"]:$_POST["connected_name"]);
														$option_value=($connected_table==0 ? $_POST["option_value"]:$_POST["connected_value"]);
														if($option_text!="" && !empty($option_text) && $option_value!="" && !empty($option_value) || $connected_table!=0){
															$connection->exec("UPDATE ".$sub_name."select_options SET is_optgroup=0, optgroup_id='".$optgroup_id."', connected_table='".$connected_table."', option_text='".$option_text."', option_value='".$option_value."' WHERE id='".$_POST["option_id"]."' AND table_id='".$table_id."' AND column_id='".$column_id."'");
															if($connection->inTransaction()==true){
																if($connection->commit()){
																	echo "success";
																}
															}
														}else{
															echo "error";
														}
													}else{
														echo "error";
													}
												} catch (Exception $e) {
													if($connection->inTransaction()==true){
														$connection->rollBack();
													}
													echo $e->getMessage();
												}
											}
										}else{
											echo "deleted";
										}
									}
								break;
								case 'edit':
									$id=$_POST["option_id"];
									$res_edit=$connection->query("SELECT * FROM ".$sub_name."select_options WHERE id='".$id."' AND table_id='".$table_id."' AND column_id='".$column_id."'");
									$edit=$res_edit->fetch();
									print_r($edit['is_optgroup']."_._".$edit['optgroup_id']."_._".$edit['connected_table']."_._".$edit['option_text']."_._".$edit['option_value']);
									echo "_._success";
								break;
							}
						}
					}
					if(isset($_GET["checkboxOption"]) && isset($_POST["column_id"])){
						$error=0;
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config!=0){
							$table_id=$table_config['id'];
							$column_id=$_POST["column_id"];
							switch ($_GET["checkboxOption"]) {
								case 'add':
									if (isset($_POST["option_name"]) && isset($_POST["option_value"]) && isset($_POST["option_false"])) {
										try {
											if($connection->inTransaction()!=true){
												$connection->beginTransaction();
											}
											if($error==0){
												$option_text=$_POST["option_name"];
												$option_value=$_POST["option_value"];
												$option_false=$_POST["option_false"];
												$new_order_id=getNewOrderId("checkbox_options");
												if($option_text!="" && !empty($option_text) && $option_value!="" && !empty($option_value)){
													$connection->exec("INSERT INTO ".$sub_name."checkbox_options (table_id,column_id,option_name,option_value,option_false,ordering,act) VALUES ('".$table_id."','".$column_id."','".$option_text."','".$option_value."','".$option_false."','".$new_order_id."',1)");
													if($connection->inTransaction()==true){
														if($connection->commit()){
															echo "success";
														}
													}
												}else{
													echo "error";
												}
											}
										} catch (Exception $e) {
											if($connection->inTransaction()==true){
												$connection->rollBack();
											}
										}
									}
								break;
								case 'delete':
									try {
										if($connection->inTransaction()!=true){
											$connection->beginTransaction();
										}
										$connection->exec("DELETE FROM ".$sub_name."checkbox_options WHERE id='".$_POST["option_id"]."' AND column_id='".$column_id."' AND table_id='".$table_id."'");
										if($connection->inTransaction()==true){
											if($connection->commit()){
												echo "success";
											}
										}
									} catch (Exception $e) {
										if($connection->inTransaction()==true){
											$connection->rollBack();
										}
									}
								break;
								case 'save':
									if (isset($_POST["option_name"]) && isset($_POST["option_value"]) && isset($_POST["option_false"])) {
										if($connection->query("SELECT * FROM ".$sub_name."checkbox_options WHERE id='".$_POST["option_id"]."'")->rowCount()!=0){
											try {
												if($connection->inTransaction()!=true){
													$connection->beginTransaction();
												}
												if($error==0){
													$option_text=$_POST["option_name"];
													$option_value=$_POST["option_value"];
													$option_false=$_POST["option_false"];
													if($option_text!="" && !empty($option_text) && $option_value!="" && !empty($option_value) || $connected_table!=0){
														$connection->exec("UPDATE ".$sub_name."checkbox_options SET option_name='".$option_text."', option_value='".$option_value."', option_false='".$option_false."' WHERE id='".$_POST["option_id"]."' AND table_id='".$table_id."' AND column_id='".$column_id."'");
														if($connection->inTransaction()==true){
															if($connection->commit()){
																echo "success";
															}
														}
													}else{
														echo "error";
													}
												}else{
													echo "error";
												}
											} catch (Exception $e) {
												if($connection->inTransaction()==true){
													$connection->rollBack();
												}
												echo $e->getMessage();
											}
										}else{
											echo "deleted";
										}
									}
								break;
								case 'edit':
									$id=$_POST["option_id"];
									$res_edit=$connection->query("SELECT * FROM ".$sub_name."checkbox_options WHERE id='".$id."' AND table_id='".$table_id."' AND column_id='".$column_id."'");
									$edit=$res_edit->fetch();
									print_r($edit['option_name']."_._".$edit['option_value']."_._".$edit['option_false']);
									echo "_._success";
								break;
							}
						}
					}
					if(isset($_GET["optgroups"]) && isset($_POST["column_id"])){
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config!=0){
							$table_id=$table_config['id'];
							$column_id=$_POST["column_id"];
							try {
								if($connection->inTransaction()!=true){
									$connection->beginTransaction();
								}
								$res_optGroup=$connection->query("SELECT * FROM ".$sub_name."select_options WHERE table_id='".$table_id."' AND column_id='".$column_id."' AND is_optgroup=1");
								?>
									<option value="-" selected class="data-text" data-text-en="None of them" data-text-fa="هیچکدام"><?php print_r($GLOBALS['user_language']=="en" ? "None of them":"هیچکدام"); ?></option>
								<?php
								while ($optGroup=$res_optGroup->fetch()) {
									?>
										<option value="<?php print_r($optGroup['id']); ?>">
											<?php print_r($optGroup['option_text']); ?>
										</option>
									<?php
								}
								?>
									<option value="*" class="data-text" data-text-en="New Optgroup" data-text-fa="سرگروه جدید"><?php print_r($GLOBALS['user_language']=="en" ? "New Optgroup":"سرگروه جدید"); ?></option>
								<?php
							} catch (Exception $e) {
								if($connection->inTransaction()==true){
									$connection->rollBack();
								}
							}
						}
					}
					if(isset($_GET["order_numbers"]) && isset($_POST['numbers']) && isset($_POST['table_id'])){
						$res_table_config = $connection->query("SELECT * FROM " . $sub_name . "table_config WHERE id='" . $_POST['table_id'] . "'");
						$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
						if($table_config){
							$connection->beginTransaction();
							$numbers=$_POST["numbers"];
							$exploded=explode("_.._", $numbers);
							foreach ($exploded as &$value) {
								$update_data=explode("_._", $value);
								$connection->exec("UPDATE ".$table_config['current_name']." SET ordering='-".$update_data[1]."' WHERE ordering='".$update_data[0]."'");
							}
							$connection->exec("UPDATE ".$table_config['current_name']." SET ordering=abs(ordering) WHERE ordering<0");
							if($connection->inTransaction()==true){
								if($connection->commit()){
									echo "success";
								}
							}
							$res_editing=$connection->query("SELECT * FROM ".$table_config['current_name']." ORDER BY ordering ASC");
							$column_number_fixer=0;
							while($editing=$res_editing->fetch()){
								$column_number_fixer++;
								$connection->query("UPDATE ".$table_config['current_name']." SET ordering='".$column_number_fixer."' WHERE id='".$editing['id']."'");
							}
						}
					}
				}else{
					echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
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
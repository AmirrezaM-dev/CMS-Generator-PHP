<?php
	require_once("../../config.php");
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;
	require '../../../class/PHPMailer/src/Exception.php';
	require '../../../class/PHPMailer/src/PHPMailer.php';
	require '../../../class/PHPMailer/src/SMTP.php';
	$mail = new PHPMailer(true);
	$error_det=0;
	$show_tables=[];
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
		$admin_url=explode("setup",'https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
		$admin_url_length=count($admin_url)-2;
		if($admin_url_length==0){
			$admin_url=$admin_url[$admin_url_length];
		}else{
			$admin_urls="";
			for($i=0;$i<=$admin_url_length;$i++){
				if($i!=0){
					$admin_urls.='setup'.$admin_url[$i];
				}else{
					$admin_urls.=$admin_url[$i];
				}
			}
			$admin_url=$admin_urls;
		}
		$site_url=explode("panel",'https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
		$site_url_length=count($site_url)-2;
		if($site_url_length==0){
			$site_url=$site_url[$site_url_length];
		}else{
			$site_urls="";
			for($i=0;$i<=$site_url_length;$i++){
				if($i!=0){
					$site_urls.="panel".$site_url[$i];
				}else{
					$site_urls.=$site_url[$i];
				}
			}
			$site_url=$site_urls;
		}
	}else{
		$admin_url=explode("setup",'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
		$admin_url_length=count($admin_url)-2;
		if($admin_url_length==0){
			$admin_url=$admin_url[$admin_url_length];
		}else{
			$admin_urls="";
			for($i=0;$i<=$admin_url_length;$i++){
				if($i!=0){
					$admin_urls.='setup'.$admin_url[$i];
				}else{
					$admin_urls.=$admin_url[$i];
				}
			}
			$admin_url=$admin_urls;
		}
		$site_url=explode("panel",'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
		$site_url_length=count($site_url)-2;
		if($site_url_length==0){
			$site_url=$site_url[$site_url_length];
		}else{
			$site_urls="";
			for($i=0;$i<=$site_url_length;$i++){
				if($i!=0){
					$site_urls.="panel".$site_url[$i];
				}else{
					$site_urls.=$site_url[$i];
				}
			}
			$site_url=$site_urls;
		}
	}
	if(mb_substr($admin_url, -1)!="/"){
		$admin_url.="/";
	}
	if(mb_substr($site_url, -1)!="/"){
		$site_url.="/";
	}
	if(isset($_GET['verify_email'])){
		if($_SERVER["REMOTE_ADDR"]=="::1" || $_SERVER["REMOTE_ADDR"]=="127.0.0.1"){
			echo "success";
		}else{
			if(isset($_POST['host_email']) && isset($_POST['username_email']) && isset($_POST['password_email']) && isset($_POST['port_email']) && isset($_POST['sender_name_email_en']) && isset($_POST['sender_name_email_fa']) && isset($_POST["$sub_name".'email'])){
				if($_POST['port_email']!="" && !empty($_POST['port_email'])){
					$port_email=$_POST['port_email'];
				}else{
					$port_email=587;
				}

				try {
					$mail->isSMTP();
					$mail->Host       = $_POST['host_email'];
					$mail->SMTPAuth   = true;
					$mail->Username   = $_POST['username_email'];
					$mail->Password   = $_POST['password_email'];
					$mail->SMTPSecure = "tls";
					$mail->Port       = $port_email;
					$mail->CharSet = 'UTF-8';
					$mail->setFrom($_POST['username_email'], $_POST['sender_name_email_en']);
					$mail->addAddress("test@technosha.com");
					$mail->isHTML(true);
					$mail->Subject = 'Test Subject';
					$mail->Body    = '<p>Test Body</p>';
					$mail->AltBody = 'Test Alt Body';
					if($mail->send()){
						echo "success";
					}else{
						echo "inv_email";
					}
				} catch (PDOException $e) {
					//echo "Connection failed: ".$e->getMessage();
					echo "inv_email";
				}
			}else{echo "data_missing";}
		}
	}
	elseif(isset($_GET['connect_database'])){
		if(isset($_POST['server_name']) && isset($_POST['table_name']) && isset($_POST['username']) && isset($_POST['password'])){
			$servername=$_POST['server_name'];
			$table_name=$_POST['table_name'];
			$username=$_POST['username'];
			$password=$_POST['password'];
			$charset="utf8mb4";
			try{
				$dsn="mysql:host=".$servername.";dbname=".$table_name.";charset=".$charset;
				$pdo=new PDO($dsn, $username, $password);
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				echo "success";
			}catch (PDOException $e){
				//echo "Connection failed: ".$e->getMessage();
				echo "inv_database";
			}
		}else{echo "data_missing";}
	}
	elseif(isset($_GET['grecaptcha'])){
		if($_SERVER["REMOTE_ADDR"]=="::1" || $_SERVER["REMOTE_ADDR"]=="127.0.0.1"){
			echo "success";
		}else{
			if(isset($_POST['grecaptcha_secretkey']) && isset($_POST['grecaptcha_token'])){
				$ch = curl_init();
				curl_setopt_array($ch, [
					CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => [
						'secret' => $_POST['grecaptcha_secretkey'],
						'response' => $_POST['grecaptcha_token'],
						'remoteip' => $_SERVER['REMOTE_ADDR']
					],
					CURLOPT_RETURNTRANSFER => true
				]);
				$output = curl_exec($ch);
				curl_close($ch);
				$reCapthcaResult = json_decode($output);
				if($reCapthcaResult->success==1){
					echo "success";
				}else{
					echo "inv_grecaptcha";
				}
			}else{
				echo "data_missing";
			}
		}
	}
	elseif(isset($_GET['setup'])){
		if((isset($_POST['server_name']) && isset($_POST['table_name']) && isset($_POST['username_data']) && isset($_POST['password_data']) && isset($_POST['license_data']) && isset($_POST['email_panel']) && isset($_POST['username_panel']) && isset($_POST['password_panel']) && isset($_POST["$sub_name".'email']) && isset($_POST['site_name_en']) && isset($_POST['site_mini_name_en']) && isset($_POST['sender_name_email_en']) && isset($_POST['site_name_fa']) && isset($_POST['site_mini_name_fa']) && isset($_POST['sender_name_email_fa']) && isset($_POST['grecaptcha_sitekey']) && isset($_POST['grecaptcha_secretkey']) && isset($_POST['grecaptcha_token']) && isset($_POST['lang']) && ($_SERVER["REMOTE_ADDR"]!="::1" || $_SERVER["REMOTE_ADDR"]!="127.0.0.1")) || (isset($_POST['server_name']) && isset($_POST['table_name']) && isset($_POST['username_data']) && isset($_POST['password_data']) && isset($_POST['license_data']) && isset($_POST['email_panel']) && isset($_POST['username_panel']) && isset($_POST['password_panel']) && isset($_POST["$sub_name".'email']) && isset($_POST['site_name_en']) && isset($_POST['site_mini_name_en']) && isset($_POST['sender_name_email_en']) && isset($_POST['site_name_fa']) && isset($_POST['site_mini_name_fa']) && isset($_POST['sender_name_email_fa']) && isset($_POST['grecaptcha_sitekey']) && isset($_POST['grecaptcha_secretkey']) && isset($_POST['grecaptcha_token']) && isset($_POST['lang']) && ($_SERVER["REMOTE_ADDR"]=="::1" || $_SERVER["REMOTE_ADDR"]=="127.0.0.1"))){
			if($_SERVER["REMOTE_ADDR"]=="::1" || $_SERVER["REMOTE_ADDR"]=="127.0.0.1"){
				$successCaptcha=1;
			}else{
				$ch = curl_init();
				curl_setopt_array($ch, [
					CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => [
						'secret' => $_POST['grecaptcha_secretkey'],
						'response' => $_POST['grecaptcha_token'],
						'remoteip' => $_SERVER['REMOTE_ADDR']
					],
					CURLOPT_RETURNTRANSFER => true
				]);
				$output = curl_exec($ch);
				curl_close($ch);
				$reCapthcaResult = json_decode($output);
				$successCaptcha=$reCapthcaResult->success;
			}
			if($successCaptcha){
				try{
					$license_data=$_POST['license_data'];
					$servername=$_POST['server_name'];
					$table_name=$_POST['table_name'];
					$username=$_POST['username_data'];
					$password=$_POST['password_data'];
					$charset="utf8mb4";
					$dsn="mysql:host=".$servername.";dbname=".$table_name.";charset=".$charset;
					$pdo=new PDO($dsn, $username, $password);
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					if(file_exists("../../../connection/index.php")){
						unlink("../../../connection/index.php");
					}
					if(file_exists("../../../connection/connect.php")){
						unlink("../../../connection/connect.php");
					}
					if(is_dir('../../../connection')){
						rmdir('../../../connection');
					}
					mkdir("../../../connection");
					$conn_dir="../../../connection/connect.php";
					$conn_help="../../../connection/index.php";
					if(!file_exists($conn_help)){
						$help_file = fopen("../../../connection/index.php", "w") or die("Unable to open file!");
						$help_file_text ='<?php header("location: ../"); ?>';
						fwrite($help_file, $help_file_text);
						fclose($help_file);
					}
					$connection_file = fopen("../../../connection/connect.php", "w") or die("Unable to open file!");
					fwrite($connection_file, $license_data);
					fclose($connection_file);
					require_once($conn_dir);
					$connection=new connection();
					$conn=$connection->connect();
					if($connection->checkConnection()==1){
						if(!is_dir("../../../files/")){
							mkdir("../../../files/");
						}
						if(!file_exists("../../../files/index.php")){
							$upload_help_file = fopen("../../../files/index.php", "w") or die("Unable to open file!");
							$upload_help_file_text ='<?php header("location: ../"); ?>';
							fwrite($upload_help_file, $upload_help_file_text);
							fclose($upload_help_file);
						}
						if(!is_dir("../../../secure_files/")){
							mkdir("../../../secure_files/");
						}
						if(file_exists("../../../secure_files/.htaccess")){
							unlink("../../../secure_files/.htaccess");
						}
						if(!file_exists("../../../secure_files/index.php")){
							$upload_help_file = fopen("../../../secure_files/index.php", "w") or die("Unable to open file!");
							$upload_help_file_text ='<?php header("location: ../"); ?>';
							fwrite($upload_help_file, $upload_help_file_text);
							fclose($upload_help_file);
						}
						$upload_help_file2 = fopen("../../../secure_files/.htaccess", "w") or die("Unable to open file!");
						$upload_help_file2_text ='Deny from all';
						fwrite($upload_help_file2, $upload_help_file2_text);
						fclose($upload_help_file2);
						try{
							for($i=0;$i<count($conn->query("show tables")->fetchAll());$i++){$show_tables[count($show_tables)]=$conn->query("show tables")->fetchAll()[$i][0];}
							function database_manager($table_name,$table_columns,$clear_columns,$insert_columns){
								$create_column_sql="";
								for($i=0;$i<count($table_columns);$i++){
									switch ($table_columns[$i][1]) {
										case 2:
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
									if($GLOBALS['conn']->inTransaction()!=true){
										$GLOBALS['conn']->beginTransaction();
									}
									try{
										$GLOBALS['conn']->exec("CREATE TABLE ".$GLOBALS['sub_name'].$table_name." (
											id int(11) NOT NULL,
											".$create_column_sql."
											ordering int(11) NOT NULL,
											act int(11) NOT NULL
										) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;");
										$GLOBALS['conn']->exec("ALTER TABLE ".$GLOBALS['sub_name'].$table_name." ADD PRIMARY KEY (id);");
										$GLOBALS['conn']->exec("ALTER TABLE ".$GLOBALS['sub_name'].$table_name." MODIFY id int(11) NOT NULL AUTO_INCREMENT;");
										if(isset($insert_columns) && !empty($insert_columns) && $insert_columns!=""){
											for($i=0;$i<count($insert_columns);$i++){
												$GLOBALS['conn']->exec("INSERT INTO ".$GLOBALS['sub_name'].$table_name." ".$insert_columns[$i][0]);
											}
										}
									}catch (PDOException $e){
										echo $e->getMessage();
										$GLOBALS['error_det']=1;
										$GLOBALS['conn']->rollback();
										try{
											// delete everything about this table from other tables like permissions and user settings and others
											//info you can get help by searching this #duplicate-1 (remember to delete #duplicate-1 after fix)
											if(in_array($GLOBALS['sub_name'].$table_name, $GLOBALS['conn']->query("show tables")->fetchAll())){
												if($GLOBALS['conn']->exec("DROP TABLE ".$GLOBALS['sub_name'].$table_name)){
													if($GLOBALS['conn']->inTransaction()==true){
														if($GLOBALS['conn']->commit()){
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
											}else{
												echo "unable_create_".$table_name;
											}
										}catch (PDOException $e){
											echo "unable_create_".$table_name;
										}
									}
								}else{
									if(isset($clear_columns) && !empty($clear_columns) && $clear_columns!=""){
										if($GLOBALS['conn']->inTransaction()!=true){
											$GLOBALS['conn']->beginTransaction();
										}
										try{
											$GLOBALS['conn']->exec("DELETE FROM ".$GLOBALS['sub_name'].$table_name." WHERE ".$clear_columns);
											if($GLOBALS['conn']->inTransaction()==true){
												$GLOBALS['conn']->commit();
											}
										}catch (PDOException $e){
											$GLOBALS['error_det']=1;
											$GLOBALS['conn']->rollback();
											try{
												if(in_array($GLOBALS['sub_name'].$table_name, $GLOBALS['conn']->query("show tables")->fetchAll())){
													if($GLOBALS['conn']->exec("DROP TABLE ".$GLOBALS['sub_name'].$table_name)){
														if($GLOBALS['conn']->inTransaction()==true){
															if($GLOBALS['conn']->commit()){
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
												}else{
													echo "unable_clear_".$table_name;
												}
											}catch (PDOException $e){
												echo "unable_clear_".$table_name;
											}
										}
									}
									if($GLOBALS['conn']->inTransaction()!=true){
										$GLOBALS['conn']->beginTransaction();
									}
									try{
										$table_avaible_columns=$GLOBALS['conn']->prepare("DESCRIBE ".$GLOBALS['sub_name'].$table_name);
										$table_avaible_columns->execute();
										$table_avaible_columns = $table_avaible_columns->fetchAll(PDO::FETCH_COLUMN);
										for($i=0;$i<count($table_columns);$i++){
											if(!in_array($table_columns[$i][0], $table_avaible_columns)){
												switch ($table_columns[$i][1]) {
													case 2:
														$GLOBALS['conn']->exec("ALTER TABLE ".$GLOBALS['sub_name'].$table_name." ADD ".$table_columns[$i][0]." INT NOT NULL");
													break;
													case 1:
														$GLOBALS['conn']->exec("ALTER TABLE ".$GLOBALS['sub_name'].$table_name." ADD ".$table_columns[$i][0]." TEXT NOT NULL");
													break;
													default:
														$GLOBALS['conn']->exec("ALTER TABLE ".$GLOBALS['sub_name'].$table_name." ADD ".$table_columns[$i][0]." TEXT NOT NULL");
													break;
												}
											}
										}
										if(isset($insert_columns) && !empty($insert_columns) && $insert_columns!=""){
											for($i=0;$i<count($insert_columns);$i++){
												if(isset($insert_columns[$i][0]) && $insert_columns[$i][0]!=""){
													if(isset($insert_columns[$i][1]) && $insert_columns[$i][1]!=""){
														if($GLOBALS['conn']->query("SELECT * FROM ".$GLOBALS['sub_name'].$table_name." WHERE ".$insert_columns[$i][1])->rowCount()==0){
															$GLOBALS['conn']->exec("INSERT INTO ".$GLOBALS['sub_name'].$table_name." ".$insert_columns[$i][0]);
														}else if(isset($insert_columns[$i][2]) && $insert_columns[$i][2]!=""){
															$GLOBALS['conn']->exec("UPDATE ".$GLOBALS['sub_name'].$table_name." SET ".$insert_columns[$i][2]." WHERE ".$insert_columns[$i][1]);
														}
													}else{
														$GLOBALS['conn']->exec("INSERT INTO ".$GLOBALS['sub_name'].$table_name." ".$insert_columns[$i][0]);
													}
												}
											}
										}
										if($GLOBALS['conn']->inTransaction()==true){
											$GLOBALS['conn']->commit();
										}
									}catch (PDOException $e){
										echo $e->getMessage();
										$GLOBALS['error_det']=1;
										$GLOBALS['conn']->rollback();
										try{
											if(in_array($GLOBALS['sub_name'].$table_name, $GLOBALS['conn']->query("show tables")->fetchAll())){
												if($GLOBALS['conn']->exec("DROP TABLE ".$GLOBALS['sub_name'].$table_name)){
													if($GLOBALS['conn']->inTransaction()==true){
														if($GLOBALS['conn']->commit()){
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
											}else{
												echo "unable_alert_".$table_name;
											}
										}catch (PDOException $e){
											echo "unable_alert_".$table_name;
										}
									}
								}
							}
							database_manager(
								"setting",

								[
									['setting_name',1],
									['setting_value',1]
								],

								" 1=1 ",

								[
									[
										"(setting_name, setting_value,ordering,act) VALUES ('database_server', '".$_POST['server_name']."',1,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('database_table', '".$_POST['table_name']."',2,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('database_username', '".$_POST['username_data']."',3,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('database_password', '".$_POST['password_data']."',4,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('server_email', '".$_POST['host_email']."',5,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('username_email', '".$_POST['username_email']."',6,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('password_email', '".$_POST['password_email']."',7,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('port_email', '".$_POST['port_email']."',8,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('sender_name_email_en', '".$_POST['sender_name_email_en']."',9,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('site_name_en', '".$_POST['site_name_en']."',10,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('site_mini_name_en', '".$_POST['site_mini_name_en']."',11,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('sender_name_email_fa', '".$_POST['sender_name_email_fa']."',12,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('site_name_fa', '".$_POST['site_name_fa']."',13,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('site_mini_name_fa', '".$_POST['site_mini_name_fa']."',14,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('site_url', '".$site_url."',15,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('admin_url', '".$admin_url."',16,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('upload_url', '".$site_url."files/',17,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('op_admin', '".$_POST['username_panel']."',18,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('grecaptcha_sitekey', '".$_POST['grecaptcha_sitekey']."',19,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('grecaptcha_secretkey', '".$_POST['grecaptcha_secretkey']."',20,1)","",""
									],
									[
										"(setting_name, setting_value,ordering,act) VALUES ('".$sub_name."version', '4.062',21,1)","",""
									]
								]
							);
							$ordering=getNewOrderId('admins');
							database_manager(
								"admins",

								[
									['username',1],
									['password',1],
									['email',1],
									['picture',1]
								],

								"",

								[
									[
										"(username, password, email, picture, ordering, act) VALUES ('".$_POST['username_panel']."', '".password_hash($_POST['password_panel'],PASSWORD_DEFAULT)."', '".$_POST['email_panel']."','','".$ordering."',1)",

										"username='".$_POST['username_panel']."' OR email='".$_POST['email_panel']."'",

										"username='".$_POST['username_panel']."', password='".password_hash($_POST['password_panel'],PASSWORD_DEFAULT)."', email='".$_POST['email_panel']."'"
									]
								]
							);
							$ordering=getNewOrderId('user_setting');
							if($_POST['lang']!=""){
								$panel_language="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','panel-language', '".$_POST['lang']."','".$ordering."',1)";
							}else{
								$panel_language="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','panel-language', 'en','".$ordering."',1)";
							}
							$ordering=getNewOrderId('user_setting');
							if(getUserSetting('current-page')!=""){
								$current_page="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','current-page', '".getUserSetting('current-page')."','".$ordering."',1)";
							}else{
								$current_page="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','current-page', 'dashboard.php','".$ordering."',1)";
							}
							$ordering=getNewOrderId('user_setting');
							if(getUserSetting('maximum-page')!=""){
								$maximum_page="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','maximum-page', '".getUserSetting('maximum-page')."','".$ordering."',1)";
							}else{
								$maximum_page="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','maximum-page', '10','".$ordering."',1)";
							}
							$ordering=getNewOrderId('user_setting');
							if(getUserSetting('data-color-default')!=""){
								$data_color_default="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','data-color-default', '".getUserSetting('data-color-default')."','".$ordering."',1)";
							}else{
								$data_color_default="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','data-color-default', 'primary','".$ordering."',1)";
							}
							$ordering=getNewOrderId('user_setting');
							if(getUserSetting('theme-default')!=""){
								$theme_default="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','theme-default', '".getUserSetting('theme-default')."','".$ordering."',1)";
							}else{
								$theme_default="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','theme-default', 'black','".$ordering."',1)";
							}
							$ordering=getNewOrderId('user_setting');
							if(getUserSetting('sidebar-minimize')!=""){
								$sidebar_minimize="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','sidebar-minimize', '".getUserSetting('sidebar-minimize')."','".$ordering."',1)";
							}else{
								$sidebar_minimize="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','sidebar-minimize', 'false','".$ordering."',1)";
							}
							$ordering=getNewOrderId('user_setting');
							if(getUserSetting('scroll-top')!=""){
								$scroll_top="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','scroll-top', '".getUserSetting('scroll-top')."','".$ordering."',1)";
							}else{
								$scroll_top="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','scroll-top', 'false','".$ordering."',1)";
							}
							$ordering=getNewOrderId('user_setting');
							if(getUserSetting('move-top')!=""){
								$move_top="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','move-top', '".getUserSetting('move-top')."','".$ordering."',1)";
							}else{
								$move_top="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','move-top', 'false','".$ordering."',1)";
							}
							$ordering=getNewOrderId('user_setting');
							if(getUserSetting('move-bottom')!=""){
								$move_bottom="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','move-bottom', '".getUserSetting('move-bottom')."','".$ordering."',1)";
							}else{
								$move_bottom="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','move-bottom', 'false','".$ordering."',1)";
							}
							$ordering=getNewOrderId('user_setting');
							if(getUserSetting('scroll-bottom')!=""){
								$scroll_bottom="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','scroll-bottom', '".getUserSetting('scroll-bottom')."','".$ordering."',1)";
							}else{
								$scroll_bottom="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','scroll-bottom', 'true','".$ordering."',1)";
							}
							$ordering=getNewOrderId('user_setting');
							if(getUserSetting('fixed-menu')!=""){
								$fixed_menu="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','fixed-menu', '".getUserSetting('fixed-menu')."','".$ordering."',1)";
							}else{
								$fixed_menu="(username, setting_name, setting_value,ordering,act) VALUES ('".$_POST['username_panel']."','fixed-menu', 'true','".$ordering."',1)";
							}
							database_manager(
								"user_setting",

								[
									['username',1],
									['setting_name',1],
									['setting_value',1]
								],

								"username='".$_POST['username_panel']."'",

								[
									[
										$panel_language,"",""
									],
									[
										$current_page,"",""
									],
									[
										$maximum_page,"",""
									],
									[
										$data_color_default,"",""
									],
									[
										$theme_default,"",""
									],
									[
										$sidebar_minimize,"",""
									],
									[
										$scroll_top,"",""
									],
									[
										$move_top,"",""
									],
									[
										$move_bottom,"",""
									],
									[
										$scroll_bottom,"",""
									],
									[
										$fixed_menu,"",""
									]
								]
							);
							database_manager(
								"table_config",

								[
									['current_name',1],
									['new_name',1],
									['description_name_fa',1],
									['description_info_fa',1],
									['description_name_en',1],
									['description_info_en',1],
									['lock_admin_id',1],
									['created',2],
									['creatable',2],
									['visible',2],
									['editable',2],
									['removable',2],
									['create_power',2],
									['read_power',2],
									['update_power',2],
									['delete_power',2],
									['no_power',2],
									['level',2]
								],

								"",

								""
							);
							database_manager(
								"table_column_config",

								[
									['table_id',2],
									['current_name',1],
									['new_name',1],
									['column_number',2],
									['description_name_fa',1],
									['description_info_fa',1],
									['description_name_en',1],
									['description_info_en',1],
									['created',2],
									['creatable',2],
									['visible',2],
									['editable',2],
									['removable',2],
									['visible_table',2],
									['create_power',2],
									['read_power',2],
									['update_power',2],
									['delete_power',2],
									['no_power',2],
									['mode',2],
									['new_mode',2],
									['editing',2],
									['primarys',2],
									['importants',2]
								],

								"",

								""
							);
							database_manager(
								"table_property_config",

								[
									['table_id',2],
									['column_id',2],
									['propproperty_name',1],
									['propproperty_value',1]
								],

								"",

								""
							);
							database_manager(
								"menu",

								[
									['menu_name_en',1],
									['menu_name_fa',1],
									['menu_mini_name_en',1],
									['menu_mini_name_fa',1],
									['menu_link',1],
									['menu_mode',1],
									['menu_mode_justvalue',1],
									['menu_target_mode',1],
									['menu_target_mode_justvalue',1],
									['description_name_fa',1],
									['description_info_fa',1],
									['description_name_en',1],
									['description_info_en',1],
									['fa_icon',1],
									['visible',2],
									['is_parent',2],
									['is_child',2],
									['parent_id',1],
									['parent_id_justvalue',1]
								],

								"",/*menu_name_en in ('Panel Menus','File Manager','Permissions','Tables','Update','Admins','Banned IPs','Ranks','Admins Ranks') && menu_link in ('#tables?name=menu','#files','#permissions','#tables','#update','#tables?name=admins','#tables?name=ban_list','#tables?name=rank','#tables?name=rank_granted')*/

								[]
							);
							database_manager(
								"table_permissions",

								[
									['table_id',2],
									['admin_id',1],
									['permission_name',1],
									['permission_value',1],
									['table_fa_name',1],
									['table_en_name',1],
									['permission_fa_name',1],
									['permission_en_name',1]
								],

								"",

								""
							);
							database_manager(
								"column_permissions",

								[
									['table_id',2],
									['admin_id',1],
									['column_id',2],
									['permission_name',1],
									['permission_value',1],
									['table_fa_name',1],
									['table_en_name',1],
									['column_fa_name',1],
									['column_en_name',1],
									['permission_fa_name',1],
									['permission_en_name',1]
								],

								"",

								""
							);
							database_manager(
								"menu_permissions",

								[
									['admin_id',1],
									['menu_id',2],
									['permission_value',1],
									['menu_fa_name',1],
									['menu_en_name',1]
								],

								"",

								""
							);
							database_manager(
								"rank",

								[
									['rank_name_fa',1],
									['rank_name_en',1],
									['description_name_fa',1],
									['description_info_fa',1],
									['description_name_en',1],
									['description_info_en',1]
								],

								"",

								""
							);
							database_manager(
								"rank_granted",

								[
									['admin_id',1],
									['admin_id_justvalue',1],
									['rank_id',1],
									['rank_id_justvalue',1]
								],

								"",

								""
							);
							database_manager(
								"log",

								[
									['admin_id',1],
									['menu_id',2],
									['mode',2],
									['description_name_fa',1],
									['description_info_fa',1],
									['description_name_en',1],
									['description_info_en',1],
									['page_url',1],
									['ip',1],
									['browser',1],
									['windows',1],
									['time_en',1],
									['date_en',1],
									['time_fa',1],
									['date_fa',1],
									['str_time',1],
								],

								"",

								""
							);
							database_manager(
								"ban_list",

								[
									['user_ip',1],
									['ban_time',2]
								],

								"",

								""
							);
							database_manager(
								"forgot_list",

								[
									['user_id',2],
									['str_time',1],
									['code',1]
								],

								"",

								""
							);
							database_manager(
								"table_user_setting",

								[
									['table_id',2],
									['admin_id',1],
									['save_name',1],
									['save_value',1]
								],

								"",

								""
							);
							database_manager(
								"table_column_mode",

								[
									['description_name_fa',1],
									['description_info_fa',1],
									['description_name_en',1],
									['description_info_en',1],
									['mode',2]
								],

								" 1=1 ",

								[
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (1,'Normal Text','Normal Text','متن ساده','متن ساده',1,1,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (2,'Normal Number','Normal Number','عدد ساده','عدد ساده',2,2,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (3,'Yes/No Question','Yes/No Question','پرسش بله/خیر','پرسش بله/خیر',2,3,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (4,'Select Box','Select Box','جعبه انتخاب گزینه','جعبه انتخاب گزینه',1,4,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (5,'Color Picker','Color Picker','انتخاب کننده رنگ','انتخاب کننده رنگ',1,5,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (6,'Password Encoder','Password Encoder','رمزگذار کلمه عبور','رمزگذار کلمه عبور',1,6,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (7,'File Selector','File Selector','انتخاب فایل','انتخاب فایل',1,7,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (8,'CK Editor','CK Editor','ویرایش گر حرفه ای','ویرایش گر حرفه ای',1,8,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (9,'Checkbox & Radio','Checkbox & Radio','گزینه و گزینه گروهی','گزینه و گزینه گروهی',1,9,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (10,'Font Awesome ICON','Font Awesome ICON','آیکون فونت آوسم','آیکون فونت آوسم',1,10,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (11,'Update Time Code','Update Time Code','کد زمان بروزرسانی','کد زمان بروزرسانی',1,11,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (12,'Date','Date','تاریخ میلادی','تاریخ میلادی',1,12,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (13,'Date and Time','Date and Time','تاریخ میلادی و ساعت','تاریخ میلادی و ساعت',1,13,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (14,'Shamsi Date','Shamsi Date','تاریخ شمسی','تاریخ شمسی',1,14,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (15,'Shamsi Date and Time','Shamsi Date and Time','تاریخ شمسی و ساعت','تاریخ شمسی و ساعت',1,15,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (16,'Time','Time','ساعت','ساعت',1,16,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (17,'Slider','Slider','اسلایدر','اسلایدر',1,17,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (18,'Tags','Tags','کلمات کلیدی','کلمات کلیدی',1,18,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (19,'Simple Text Editor','Simple Text Editor','ویرایش گر متن ساده','ویرایش گر متن ساده',1,19,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (20,'Hide','Hide','مخفی','مخفی',1,20,1)",

										"",

										""
									],
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (21,'Add Time Code','Add Time Code','کد زمان فزودن','کد زمان فزودن',1,21,1)",

										"",

										""
									]/*,
									[
										"(id, description_name_en, description_info_en, description_name_fa, description_info_fa, mode, ordering, act) VALUES (8,'asdasdasd','asdasdasd','شسیشسیشسی','شسیشسیشسی',1,1,1)",

										"",

										""
									],*/
								]
							);
							database_manager(
								"yes_no_question_options",

								[
									['table_id',2],
									['column_id',2],
									['yes_option',1],
									['no_option',1],
									['yes_value',1],
									['no_value',1],
									['yes_fa_icon',1],
									['no_fa_icon',1]
								],

								"",

								""
							);
							database_manager(
								"select_options",

								[
									['table_id',2],
									['column_id',2],
									['is_optgroup',2],
									['optgroup_id',1],
									['connected_table',1],
									['option_text',1],
									['option_value',1]
								],

								"",

								""
							);
							database_manager(
								"select_options_setting",

								[
									['table_id',2],
									['column_id',2],
									['is_multiple',1],
									['is_forced',1],
									['min_allowed',1],
									['max_allowed',1]
								],

								"",

								""
							);
							database_manager(
								"admin_log",

								[
									['log_title',1],
									['log_info',1],
									['more_info',1],
									['log_type',1]
								],

								"",

								""
							);
							database_manager(
								"file_manager",

								[
									['folder_file',2],
									['parent_id',2],
									['level',2],
									['display_name',1],
									['real_name',1],
									['fa_icon',1],
									['file_size',1],
									['is_secure',2],
									['last_modify',1]
								],

								"",

								""
							);
							database_manager(
								"checkbox_options",

								[
									['table_id',2],
									['column_id',2],
									['option_name',1],
									['option_value',1],
									['option_false',1]
								],

								"",

								""
							);
							database_manager(
								"checkbox_options_setting",

								[
									['table_id',2],
									['column_id',2],
									['is_multiple',1],
									['is_forced',1]
								],

								"",

								""
							);
							database_manager(
								"file_uploader_setting",

								[
									['table_id',2],
									['column_id',2],
									['max_size',2],
									['allowed_type',1]
								],

								"",

								""
							);
							database_manager(
								"messages_users",

								[
									['name',1],
									['email',1],
									['ip',1],
									['os',1],
									['join_time',1],
									['last_activity',1]
								],

								"",

								""
							);
							database_manager(
								"messages_titles",

								[
									['sender_id',1],
									['sender_id_justvalue',1],
									['reciver_id',1],
									['reciver_id_justvalue',1],
									['title',1],
									['mode',2],
									['mode_justvalue',2]
								],

								"",

								""
							);
							database_manager(
								"messages",

								[
									['sender_id',1],
									['sender_id_justvalue',1],
									['reciver_id',1],
									['reciver_id_justvalue',1],
									['send_time',1],
									['recive_time',1],
									['title_id',2],
									['title_id_justvalue',2],
									['message',1]
								],

								"",

								""
							);

							// add more tables below there
							$show_tables=null;$show_tables=[];for($i=0;$i<count($conn->query("show tables")->fetchAll());$i++){$show_tables[count($show_tables)]=$conn->query("show tables")->fetchAll()[$i][0];}
							if(in_array($sub_name.'table_config', $show_tables) && in_array($sub_name.'table_column_config', $show_tables)){
								if($GLOBALS['conn']->inTransaction()!=true){
									$GLOBALS['conn']->beginTransaction();
								}
								try{
									$get_table_config=[];
									$get_column_name=[];
									$res_get_tables=$conn->query("SELECT * FROM ".$sub_name."table_config");
									while($get_tables=$res_get_tables->fetch()){
										$get_table_config[count($get_table_config)]=$get_tables['current_name'];
									}
									foreach($show_tables as $table_name) {
										$get_column_name[$table_name]=[];
										$res_column=$conn->query("DESCRIBE ".$table_name);
										while($column=$res_column->fetch()){
											$get_column_name[$table_name][count($get_column_name[$table_name])]=$column['Field'];
											if($column['Type']=="text"){
												$get_column_name[$table_name][$column['Field']]=1;
											}elseif($column['Type']=="int(11)"){
												$get_column_name[$table_name][$column['Field']]=2;
											}
											//print_r($column['Field']);
										}
										if(!in_array($table_name, $get_table_config)){
											$ordering=getNewOrderId($table_name);
											$table_new_name="";
											$table_description_name_fa=preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_name, 1);
											$table_description_info_fa='این جدول به صورت خودکار اضافه شده و برای این پنل مورد نیاز است !';
											$table_description_name_en=preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_name, 1);
											$table_description_info_en='This table has been added automatically and is required for this panel!';
											$table_lock_admin_id='0';
											$table_created='1';
											$table_creatable=0;
											$table_editable='0';
											$table_removable='0';
											$table_visible='0';
											$table_create_power='0';
											$table_read_power='0';
											$table_update_power='0';
											$table_delete_power='0';
											$table_no_power='0';
											$table_act=2;
											$table_level=0;
											switch ($table_name) {//tables_mode_code
												case $sub_name."setting":
													$table_editable='1';
													$table_visible='1';
												break;
												case $sub_name."admins":case $sub_name."admin_log":case $sub_name."ban_list":case $sub_name."rank":case $sub_name."rank_granted":case $sub_name."table_config":case $sub_name."table_column_config":case $sub_name."file_manager":case $sub_name."log":case $sub_name."menu_permissions":case $sub_name."column_permissions":case $sub_name."table_permissions":case $sub_name."menu":case $sub_name."messages":case $sub_name."messages_titles":case $sub_name."messages_users":
													$table_creatable='1';
													$table_editable='1';
													$table_removable='1';
													$table_visible='1';
												break;
											}
											switch ($table_name) {//tables_mode_code
												case $sub_name."admins":
													$table_description_name_fa="مدیران";
													$table_description_info_fa="کاربرانی که میتوانند به پنل مدیریت دسترسی داشته باشند !";
													$table_description_name_en="admins";
													$table_description_info_en="Users who can have access to the panel !";
												break;
												case $sub_name."admin_log":
													$table_description_name_fa="لاگ های پنل مدیریت";
													$table_description_info_fa="لاگ هایی که پنل مدیریت ایجاد میکند در این جدول ذخیره میشود !";
													$table_description_name_en="Panel Logs";
													$table_description_info_en="The logs that panel generate will save to this table !";
												break;
												/*case $sub_name."table_name":
													$table_description_name_fa="";
													$table_description_info_fa="";
													$table_description_name_en="";
													$table_description_info_en="";
												break;*/
											}
											$stmt_tc = $conn->prepare("INSERT INTO ".$sub_name."table_config (current_name,new_name,description_name_fa,description_info_fa,description_name_en,description_info_en,lock_admin_id,created,creatable,editable,removable,visible,create_power,read_power,update_power,delete_power,no_power,ordering,act,level) VALUES (:current_name,:new_name,:description_name_fa,:description_info_fa,:description_name_en,:description_info_en,:lock_admin_id,:created,:creatable,:editable,:removable,:visible,:create_power,:read_power,:update_power,:delete_power,:no_power,:ordering,:act,:level)");
											$stmt_tc->bindParam(':current_name', $table_name);
											$stmt_tc->bindParam(':new_name', $table_new_name);
											$stmt_tc->bindParam(':description_name_fa', $table_description_name_fa);
											$stmt_tc->bindParam(':description_info_fa', $table_description_info_fa);
											$stmt_tc->bindParam(':description_name_en', $table_description_name_en);
											$stmt_tc->bindParam(':description_info_en', $table_description_info_en);
											$stmt_tc->bindParam(':lock_admin_id', $table_lock_admin_id);
											$stmt_tc->bindParam(':created', $table_created);
											$stmt_tc->bindParam(':creatable', $table_creatable);
											$stmt_tc->bindParam(':editable', $table_editable);
											$stmt_tc->bindParam(':removable', $table_removable);
											$stmt_tc->bindParam(':visible', $table_visible);
											$stmt_tc->bindParam(':create_power', $table_create_power);
											$stmt_tc->bindParam(':read_power', $table_read_power);
											$stmt_tc->bindParam(':update_power', $table_update_power);
											$stmt_tc->bindParam(':delete_power', $table_delete_power);
											$stmt_tc->bindParam(':no_power', $table_no_power);
											$stmt_tc->bindParam(':ordering', $ordering);
											$stmt_tc->bindParam(':act', $table_act);
											$stmt_tc->bindParam(':level', $table_level);
											$stmt_tc->execute();
											$last_id = $conn->lastInsertId();
										}else{
											$last_id=$conn->query("SELECT * FROM ".$sub_name."table_config WHERE current_name='".$table_name."'")->fetch()['id'];
										}
										for($i=2;($i<=count($get_column_name[$table_name])-1);$i++){
											$is_true=$conn->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$last_id."' AND current_name='".$get_column_name[$table_name][$i]."' ORDER BY column_number DESC")->rowCount();
											if($is_true==0){
												$res_last_column_number=$conn->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$last_id."' ORDER BY column_number DESC");
												if($res_last_column_number->rowCount()!=0){
													$last_column_number=$res_last_column_number->fetch()['column_number']+1;
												}else{
													$last_column_number=1;
												}
												$ordering=getNewOrderId($table_name);
												$saveMode=($get_column_name[$table_name][$i]!="act" ? $get_column_name[$table_name][$get_column_name[$table_name][$i]]:3);
												$new_name='';
												$description_name_fa=preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $get_column_name[$table_name][$i], 1);
												$description_info_fa='این ستون به صورت خودکار اضافه شده و برای این پنل مورد نیاز است !';
												$description_name_en=preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $get_column_name[$table_name][$i], 1);
												$description_info_en='This column has been added automatically and its required for this panel!';
												$created='1';
												$creatable='0';
												$editable='0';
												$removable='0';
												$visible='0';
												$visible_table='0';
												$create_power='0';
												$read_power='0';
												$update_power='0';
												$delete_power='0';
												$no_power='0';
												$new_mode=0;
												$primarys=0;
												$importants=0;
												$editing=0;
												$act=2;
												switch ($table_name) {//tables_mode_code
													case $sub_name."admins":
														$creatable='1';
														$editable='1';
														$removable='1';
														$visible='1';
														switch ($get_column_name[$table_name][$i]) {
															case "username":
																$visible_table='1';
																$primarys=1;
																$importants=1;
															break;
															case "email":
																$importants=1;
																$visible_table='1';
															break;
															case "password":
																$importants=1;
																$saveMode=6;
															break;
															case "picture":
																$saveMode=7;
															break;
														}
													break;
													case $sub_name."admin_log":
														$creatable='1';
														$editable='1';
														$removable='1';
														$visible='1';
														switch ($get_column_name[$table_name][$i]) {
															case "log_title":
																$visible_table='1';
																$primarys=1;
															break;
															case "log_info":case "more_info":case "log_type":
																$visible_table='1';
															break;
														}
													break;
													case $sub_name."ban_list":
														$creatable='1';
														$editable='1';
														$removable='1';
														$visible='1';
														switch ($get_column_name[$table_name][$i]) {
															case "user_ip":
																$visible_table='1';
																$primarys=1;
															break;
															case "ban_time":
																$visible_table='1';
															break;
														}
													break;
													case $sub_name."rank":
														$creatable='1';
														$editable='1';
														$removable='1';
														$visible='1';
														switch ($get_column_name[$table_name][$i]) {
															case "rank_name_fa":
																$visible_table='1';
																$primarys=1;
															break;
															case "rank_name_en":
															case "description_name_fa":
															case "description_name_en":
																$visible_table='1';
															break;
														}
													case $sub_name."rank_granted":
														$creatable='1';
														$editable='1';
														$removable='1';
														$visible='1';
														switch ($get_column_name[$table_name][$i]) {
															case "admin_id":
																$visible_table='1';
																$primarys=1;
																$saveMode=4;
															break;
															case "rank_id":
																$visible_table='1';
																$saveMode=4;
															break;
															case "admin_id_justvalue":case "rank_id_justvalue":
																$visible_table='0';
																$saveMode=20;
															break;
														}
													break;
													case $sub_name."setting":
														$editable='1';
														$visible='1';
														switch ($get_column_name[$table_name][$i]) {
															case "setting_name":
																$visible_table='1';
																$primarys=1;
															break;
															case "setting_value":
																$visible_table='1';
															break;
														}
													break;
													case $sub_name."table_config":
													case $sub_name."table_column_config":
													case $sub_name."file_manager":
													case $sub_name."log":
													case $sub_name."menu_permissions":
													case $sub_name."column_permissions":
													case $sub_name."table_permissions":
														$creatable='1';
														$editable='1';
														$removable='1';
														$visible='1';
													break;
													case $sub_name."menu":
														$creatable='1';
														$editable='1';
														$removable='1';
														$visible='1';
														switch ($get_column_name[$table_name][$i]) {
															case "menu_name_fa":
																$visible_table='1';
																$primarys=1;
															break;
															case "menu_link":
																$visible_table='1';
															break;
															case "fa_icon":
																$saveMode=10;
															break;
															case "visible":case "is_parent":case "is_child":
																$saveMode=3;
															break;
															case "parent_id":case "menu_target_mode":case "menu_mode":
																$saveMode=4;
															break;
															case "menu_mode_justvalue":case "menu_target_mode_justvalue":case "parent_id_justvalue":
																$visible_table='0';
																$saveMode=20;
															break;
														}
													break;
													case $sub_name."messages":
														$creatable='1';
														$editable='1';
														$removable='1';
														$visible='1';
														switch ($get_column_name[$table_name][$i]) {
															case "title_id":
																$primarys=1;
																$visible_table='1';
																$saveMode=4;
															break;
															case "message":
																$visible_table='1';
															break;
															case "send_time":
																$visible_table='1';
																$saveMode=16;
															break;
															case "recive_time":
																$saveMode=16;
															break;
															case "sender_id":case "reciver_id":
																$visible_table='1';
																$saveMode=4;
															break;
															case "sender_id_justvalue":case "reciver_id_justvalue":case "title_id_justvalue":
																$visible_table='0';
																$saveMode=20;
															break;
														}
													break;
													case $sub_name."messages_users":
														$creatable='1';
														$editable='1';
														$removable='1';
														$visible='1';
														switch ($get_column_name[$table_name][$i]) {
															case "email":
																$primarys=1;
																$visible_table='1';
															break;
															case "ip":case "name":case "os":
																$visible_table='1';
															break;
															case "join_time":case "last_activity":
																$visible_table='1';
																$saveMode=16;
															break;
														}
													break;
													case $sub_name."messages_titles":
														$creatable='1';
														$editable='1';
														$removable='1';
														$visible='1';
														switch ($get_column_name[$table_name][$i]) {
															case "title":
																$primarys=1;
																$visible_table='1';
															break;
															case "mode":
																$saveMode=4;
															break;
															case "sender_id":case "reciver_id":
																$visible_table='1';
																$saveMode=4;
															break;
															case "sender_id_justvalue":case "reciver_id_justvalue":case "mode_justvalue":
																$visible_table='0';
																$saveMode=20;
															break;
														}
													break;
												}
												$stmt_tcc = $conn->prepare("INSERT INTO ".$sub_name."table_column_config (table_id,current_name,new_name,column_number,description_name_fa,description_info_fa,description_name_en,description_info_en,created,creatable,editable,removable,visible,visible_table,create_power,read_power,update_power,delete_power,no_power,mode,new_mode,primarys,importants,editing,ordering,act) VALUES (:table_id, :current_name, :new_name, :column_number, :description_name_fa, :description_info_fa, :description_name_en, :description_info_en, :created, :creatable, :editable, :removable, :visible, :visible_table, :create_power, :read_power, :update_power, :delete_power, :no_power, :mode, :new_mode, :primarys, :importants, :editing, :ordering, :act)");
												$stmt_tcc->bindParam(':table_id',$last_id);
												$stmt_tcc->bindParam(':current_name',$get_column_name[$table_name][$i]);
												$stmt_tcc->bindParam(':new_name',$new_name);
												$stmt_tcc->bindParam(':column_number',$last_column_number);
												$stmt_tcc->bindParam(':description_name_fa',$description_name_fa);
												$stmt_tcc->bindParam(':description_info_fa',$description_info_fa);
												$stmt_tcc->bindParam(':description_name_en',$description_name_en);
												$stmt_tcc->bindParam(':description_info_en',$description_info_en);
												$stmt_tcc->bindParam(':created',$created);
												$stmt_tcc->bindParam(':creatable',$creatable);
												$stmt_tcc->bindParam(':editable',$editable);
												$stmt_tcc->bindParam(':removable',$removable);
												$stmt_tcc->bindParam(':visible',$visible);
												$stmt_tcc->bindParam(':visible_table',$visible_table);
												$stmt_tcc->bindParam(':create_power',$create_power);
												$stmt_tcc->bindParam(':read_power',$read_power);
												$stmt_tcc->bindParam(':update_power',$update_power);
												$stmt_tcc->bindParam(':delete_power',$delete_power);
												$stmt_tcc->bindParam(':no_power',$no_power);
												$stmt_tcc->bindParam(':mode',$saveMode);
												$stmt_tcc->bindParam(':new_mode',$new_mode);
												$stmt_tcc->bindParam(':primarys',$primarys);
												$stmt_tcc->bindParam(':importants',$importants);
												$stmt_tcc->bindParam(':editing',$editing);
												$stmt_tcc->bindParam(':ordering',$ordering);
												$stmt_tcc->bindParam(':act',$act);
												if(!strpos($get_column_name[$table_name][$i],"_justvalue")){
													$stmt_tcc->execute();
												}
											}
											$i++;
										}
									}

									$menu_parent_id = $conn->prepare("INSERT INTO ".$sub_name."select_options (table_id, column_id, is_optgroup, optgroup_id, connected_table, option_text, option_value, ordering, act) VALUES (:table_id, :column_id, :is_optgroup, :optgroup_id, :connected_table, :option_text, :option_value, :ordering, :act)");
									$menu_parent_id->bindParam(':table_id', $menu_parent_id_table_id);
									$menu_parent_id->bindParam(':column_id', $menu_parent_id_column_id);
									$menu_parent_id->bindParam(':is_optgroup', $menu_parent_id_is_optgroup);
									$menu_parent_id->bindParam(':optgroup_id', $menu_parent_id_optgroup_id);
									$menu_parent_id->bindParam(':connected_table', $menu_parent_id_connected_table);
									$menu_parent_id->bindParam(':option_text', $menu_parent_id_option_text);
									$menu_parent_id->bindParam(':option_value', $menu_parent_id_option_value);
									$menu_parent_id->bindParam(':ordering', $menu_parent_id_ordering);
									$menu_parent_id->bindParam(':act', $menu_parent_id_act);
									$menu_table_config=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE current_name='".$sub_name."menu'")->fetch();
									$menu_column_config=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$menu_table_config['id']."' AND current_name='parent_id'")->fetch();
									$menu_option_text=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$menu_table_config['id']."' AND current_name='menu_name_en'")->fetch();
									$menu_parent_id_table_id=$menu_table_config['id'];
									$menu_parent_id_column_id=$menu_column_config['id'];
									$menu_parent_id_is_optgroup=0;
									$menu_parent_id_optgroup_id='-';
									$menu_parent_id_connected_table=$menu_table_config['id'];
									$menu_parent_id_option_text=$menu_option_text['id'];
									$menu_parent_id_option_value=0;
									$menu_parent_id_ordering=0;
									$menu_parent_id_act=1;
									if(!$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE table_id='".$menu_parent_id_table_id."' AND column_id='".$menu_parent_id_column_id."' AND is_optgroup='".$menu_parent_id_is_optgroup."' AND optgroup_id='".$menu_parent_id_optgroup_id."' AND connected_table='".$menu_parent_id_connected_table."' AND option_text='".$menu_parent_id_option_text."' AND option_value='".$menu_parent_id_option_value."' AND act='".$menu_parent_id_act."'")->rowCount()){
										$menu_parent_id->execute();
									}
									$menu_column_config=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$menu_table_config['id']."' AND current_name='menu_target_mode'")->fetch();
									$menu_parent_id_table_id=$menu_table_config['id'];
									$menu_parent_id_column_id=$menu_column_config['id'];
									$menu_parent_id_is_optgroup=0;
									$menu_parent_id_optgroup_id='-';
									$menu_parent_id_connected_table=0;
									$menu_parent_id_ordering=0;
									$menu_parent_id_act=1;
									$menu_parent_id_option_text="_blank";
									$menu_parent_id_option_value="_blank";
									if(!$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE table_id='".$menu_parent_id_table_id."' AND column_id='".$menu_parent_id_column_id."' AND is_optgroup='".$menu_parent_id_is_optgroup."' AND optgroup_id='".$menu_parent_id_optgroup_id."' AND connected_table='".$menu_parent_id_connected_table."' AND option_text='".$menu_parent_id_option_text."' AND option_value='".$menu_parent_id_option_value."' AND act='".$menu_parent_id_act."'")->rowCount()){
										$menu_parent_id->execute();
									}
									$menu_parent_id_option_text="_self";
									$menu_parent_id_option_value="_self";
									if(!$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE table_id='".$menu_parent_id_table_id."' AND column_id='".$menu_parent_id_column_id."' AND is_optgroup='".$menu_parent_id_is_optgroup."' AND optgroup_id='".$menu_parent_id_optgroup_id."' AND connected_table='".$menu_parent_id_connected_table."' AND option_text='".$menu_parent_id_option_text."' AND option_value='".$menu_parent_id_option_value."' AND act='".$menu_parent_id_act."'")->rowCount()){
										$menu_parent_id->execute();
									}
									$menu_parent_id_option_text="_parent";
									$menu_parent_id_option_value="_parent";
									if(!$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE table_id='".$menu_parent_id_table_id."' AND column_id='".$menu_parent_id_column_id."' AND is_optgroup='".$menu_parent_id_is_optgroup."' AND optgroup_id='".$menu_parent_id_optgroup_id."' AND connected_table='".$menu_parent_id_connected_table."' AND option_text='".$menu_parent_id_option_text."' AND option_value='".$menu_parent_id_option_value."' AND act='".$menu_parent_id_act."'")->rowCount()){
										$menu_parent_id->execute();
									}
									$menu_parent_id_option_text="_top";
									$menu_parent_id_option_value="_top";
									if(!$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE table_id='".$menu_parent_id_table_id."' AND column_id='".$menu_parent_id_column_id."' AND is_optgroup='".$menu_parent_id_is_optgroup."' AND optgroup_id='".$menu_parent_id_optgroup_id."' AND connected_table='".$menu_parent_id_connected_table."' AND option_text='".$menu_parent_id_option_text."' AND option_value='".$menu_parent_id_option_value."' AND act='".$menu_parent_id_act."'")->rowCount()){
										$menu_parent_id->execute();
									}
									$menu_column_config=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$menu_table_config['id']."' AND current_name='menu_mode'")->fetch();
									$menu_parent_id_column_id=$menu_column_config['id'];
									$menu_parent_id_option_text="Normal";
									$menu_parent_id_option_value=0;
									if(!$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE table_id='".$menu_parent_id_table_id."' AND column_id='".$menu_parent_id_column_id."' AND is_optgroup='".$menu_parent_id_is_optgroup."' AND optgroup_id='".$menu_parent_id_optgroup_id."' AND connected_table='".$menu_parent_id_connected_table."' AND option_text='".$menu_parent_id_option_text."' AND option_value='".$menu_parent_id_option_value."' AND act='".$menu_parent_id_act."'")->rowCount()){
										$menu_parent_id->execute();
									}
									$menu_parent_id_option_text="PageLoader";
									$menu_parent_id_option_value=1;
									if(!$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE table_id='".$menu_parent_id_table_id."' AND column_id='".$menu_parent_id_column_id."' AND is_optgroup='".$menu_parent_id_is_optgroup."' AND optgroup_id='".$menu_parent_id_optgroup_id."' AND connected_table='".$menu_parent_id_connected_table."' AND option_text='".$menu_parent_id_option_text."' AND option_value='".$menu_parent_id_option_value."' AND act='".$menu_parent_id_act."'")->rowCount()){
										$menu_parent_id->execute();
									}

									$menu_table_config=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE current_name='".$sub_name."rank_granted'")->fetch();
									$menu_column_config=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$menu_table_config['id']."' AND current_name='admin_id'")->fetch();
									$menu_connected_table_config=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE current_name='".$sub_name."admins'")->fetch();
									$menu_option_text=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$menu_connected_table_config['id']."' AND current_name='username'")->fetch();
									$menu_parent_id_table_id=$menu_table_config['id'];
									$menu_parent_id_column_id=$menu_column_config['id'];
									$menu_parent_id_is_optgroup=0;
									$menu_parent_id_optgroup_id='-';
									$menu_parent_id_connected_table=$menu_connected_table_config['id'];
									$menu_parent_id_option_text=$menu_option_text['id'];
									$menu_parent_id_option_value=$menu_option_text['id'];
									$menu_parent_id_ordering=0;
									$menu_parent_id_act=1;
									if(!$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE table_id='".$menu_parent_id_table_id."' AND column_id='".$menu_parent_id_column_id."' AND is_optgroup='".$menu_parent_id_is_optgroup."' AND optgroup_id='".$menu_parent_id_optgroup_id."' AND connected_table='".$menu_parent_id_connected_table."' AND option_text='".$menu_parent_id_option_text."' AND option_value='".$menu_parent_id_option_value."' AND act='".$menu_parent_id_act."'")->rowCount()){
										$menu_parent_id->execute();
									}
									$menu_column_config=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$menu_table_config['id']."' AND current_name='rank_id'")->fetch();
									$menu_connected_table_config=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE current_name='".$sub_name."rank'")->fetch();
									$menu_option_text=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$menu_connected_table_config['id']."' AND current_name='rank_name_en'")->fetch();
									$menu_parent_id_table_id=$menu_table_config['id'];
									$menu_parent_id_column_id=$menu_column_config['id'];
									$menu_parent_id_is_optgroup=0;
									$menu_parent_id_optgroup_id='-';
									$menu_parent_id_connected_table=$menu_connected_table_config['id'];
									$menu_parent_id_option_text=$menu_option_text['id'];
									$menu_parent_id_option_value=0;
									$menu_parent_id_ordering=0;
									$menu_parent_id_act=1;
									if(!$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE table_id='".$menu_parent_id_table_id."' AND column_id='".$menu_parent_id_column_id."' AND is_optgroup='".$menu_parent_id_is_optgroup."' AND optgroup_id='".$menu_parent_id_optgroup_id."' AND connected_table='".$menu_parent_id_connected_table."' AND option_text='".$menu_parent_id_option_text."' AND option_value='".$menu_parent_id_option_value."' AND act='".$menu_parent_id_act."'")->rowCount()){
										$menu_parent_id->execute();
									}

									$tables=$conn->query("show tables")->fetchAll();
									for($i=0;$i<count($tables);$i++){
										$table_name=$tables[$i][0];
										$res_data_table=$conn->query("SELECT * FROM ".$table_name." ORDER BY id ASC");
										$ii=0;
										while($data_table=$res_data_table->fetch()){
											$ii++;
											$conn->query("UPDATE ".$table_name." SET ordering='".$ii."' WHERE id='".$data_table['id']."'");
										}
									}
									if($GLOBALS['conn']->inTransaction()==true){
										$GLOBALS['conn']->commit();
									}
								}catch(PDOException $e){
									$error_det=1;
									$conn->rollback();
									echo "unable_auto_add"." ".$e->getMessage();
								}
							}

							if($error_det==0){
								//^ adding default menus
								$stmt_select_options = $conn->prepare("INSERT INTO ".$sub_name."select_options (table_id, column_id, is_optgroup, optgroup_id, connected_table, option_text, option_value, ordering, act) VALUES (:table_id, :column_id, :is_optgroup, :optgroup_id, :connected_table, :option_text, :option_value, :ordering, :act)");
								$stmt_select_options_check = $conn->prepare("SELECT * FROM ".$sub_name."select_options WHERE table_id=:table_id AND column_id=:column_id AND is_optgroup=:is_optgroup AND optgroup_id=:optgroup_id AND connected_table=:connected_table AND option_text=:option_text AND option_value=:option_value AND act=:act");
								$stmt_select_options->bindParam(':table_id',$table_id);
								$stmt_select_options->bindParam(':column_id',$column_id);
								$stmt_select_options->bindParam(':is_optgroup',$is_optgroup);
								$stmt_select_options->bindParam(':optgroup_id',$optgroup_id);
								$stmt_select_options->bindParam(':connected_table',$connected_table);
								$stmt_select_options->bindParam(':option_text',$option_text);
								$stmt_select_options->bindParam(':option_value',$option_value);
								$stmt_select_options->bindParam(':ordering',$ordering);
								$stmt_select_options->bindParam(':act',$act);
								$stmt_select_options_check->bindParam(':table_id',$table_id);
								$stmt_select_options_check->bindParam(':column_id',$column_id);
								$stmt_select_options_check->bindParam(':is_optgroup',$is_optgroup);
								$stmt_select_options_check->bindParam(':optgroup_id',$optgroup_id);
								$stmt_select_options_check->bindParam(':connected_table',$connected_table);
								$stmt_select_options_check->bindParam(':option_text',$option_text);
								$stmt_select_options_check->bindParam(':option_value',$option_value);
								$stmt_select_options_check->bindParam(':act',$act);

								$stmt_menu = $conn->prepare("INSERT INTO ".$sub_name."menu (menu_name_en, menu_name_fa, menu_mini_name_en, menu_mini_name_fa, menu_link, menu_mode, menu_mode_justvalue, menu_target_mode, menu_target_mode_justvalue, description_name_fa, description_info_fa, description_name_en, description_info_en, fa_icon, visible, is_parent, is_child, parent_id, parent_id_justvalue, ordering, act) VALUES (:menu_name_en, :menu_name_fa, :menu_mini_name_en, :menu_mini_name_fa, :menu_link, :menu_mode, :menu_mode_justvalue, :menu_target_mode, :menu_target_mode_justvalue, :description_name_fa, :description_info_fa, :description_name_en, :description_info_en, :fa_icon, :visible, :is_parent, :is_child, :parent_id, :parent_id_justvalue, :ordering, :act)");
								$stmt_menu_check = $conn->prepare("SELECT * FROM ".$sub_name."menu WHERE menu_name_en = :menu_name_en AND menu_name_fa = :menu_name_fa AND menu_mini_name_en = :menu_mini_name_en AND menu_mini_name_fa = :menu_mini_name_fa AND menu_link = :menu_link AND menu_mode = :menu_mode AND menu_mode_justvalue = :menu_mode_justvalue AND menu_target_mode = :menu_target_mode AND menu_target_mode_justvalue = :menu_target_mode_justvalue AND description_name_fa = :description_name_fa AND description_info_fa = :description_info_fa AND description_name_en = :description_name_en AND description_info_en = :description_info_en AND fa_icon = :fa_icon AND visible = :visible AND is_parent = :is_parent AND is_child = :is_child AND parent_id = :parent_id AND parent_id_justvalue = :parent_id_justvalue AND act = :act");
								$stmt_menu->bindParam(':menu_name_en', $menu_name_en);
								$stmt_menu->bindParam(':menu_name_fa', $menu_name_fa);
								$stmt_menu->bindParam(':menu_mini_name_en', $menu_mini_name_en);
								$stmt_menu->bindParam(':menu_mini_name_fa', $menu_mini_name_fa);
								$stmt_menu->bindParam(':menu_link', $menu_link);
								$stmt_menu->bindParam(':menu_mode', $menu_mode);
								$stmt_menu->bindParam(':menu_mode_justvalue', $menu_mode_justvalue);
								$stmt_menu->bindParam(':menu_target_mode', $menu_target_mode);
								$stmt_menu->bindParam(':menu_target_mode_justvalue', $menu_target_mode_justvalue);
								$stmt_menu->bindParam(':description_name_fa', $description_name_fa);
								$stmt_menu->bindParam(':description_info_fa', $description_info_fa);
								$stmt_menu->bindParam(':description_name_en', $description_name_en);
								$stmt_menu->bindParam(':description_info_en', $description_info_en);
								$stmt_menu->bindParam(':fa_icon', $fa_icon);
								$stmt_menu->bindParam(':visible', $visible);
								$stmt_menu->bindParam(':is_parent', $is_parent);
								$stmt_menu->bindParam(':is_child', $is_child);
								$stmt_menu->bindParam(':parent_id', $parent_id);
								$stmt_menu->bindParam(':parent_id_justvalue', $parent_id_justvalue);
								$stmt_menu->bindParam(':ordering', $ordering);
								$stmt_menu->bindParam(':act', $act);
								$stmt_menu_check->bindParam(':menu_name_en', $menu_name_en);
								$stmt_menu_check->bindParam(':menu_name_fa', $menu_name_fa);
								$stmt_menu_check->bindParam(':menu_mini_name_en', $menu_mini_name_en);
								$stmt_menu_check->bindParam(':menu_mini_name_fa', $menu_mini_name_fa);
								$stmt_menu_check->bindParam(':menu_link', $menu_link);
								$stmt_menu_check->bindParam(':menu_mode', $menu_mode);
								$stmt_menu_check->bindParam(':menu_mode_justvalue', $menu_mode_justvalue);
								$stmt_menu_check->bindParam(':menu_target_mode', $menu_target_mode);
								$stmt_menu_check->bindParam(':menu_target_mode_justvalue', $menu_target_mode_justvalue);
								$stmt_menu_check->bindParam(':description_name_fa', $description_name_fa);
								$stmt_menu_check->bindParam(':description_info_fa', $description_info_fa);
								$stmt_menu_check->bindParam(':description_name_en', $description_name_en);
								$stmt_menu_check->bindParam(':description_info_en', $description_info_en);
								$stmt_menu_check->bindParam(':fa_icon', $fa_icon);
								$stmt_menu_check->bindParam(':visible', $visible);
								$stmt_menu_check->bindParam(':is_parent', $is_parent);
								$stmt_menu_check->bindParam(':is_child', $is_child);
								$stmt_menu_check->bindParam(':parent_id', $parent_id);
								$stmt_menu_check->bindParam(':parent_id_justvalue', $parent_id_justvalue);
								$stmt_menu_check->bindParam(':act', $act);

								$menu_table_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE current_name='".$GLOBALS["sub_name"]."menu'")->fetch()['id'];
								$menu_mode_column_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$menu_table_id."' AND current_name='menu_mode'")->fetch()['id'];
								$menu_target_mode_column_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$menu_table_id."' AND current_name='menu_target_mode'")->fetch()['id'];
								$menu_parent_id_column_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$menu_table_id."' AND current_name='parent_id'")->fetch()['id'];
								$menu_name_en_column_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$menu_table_id."' AND current_name='menu_name_en'")->fetch()['id'];

								$table_id=$menu_table_id;
								$column_id=$menu_parent_id_column_id;
								$is_optgroup=0;
								$optgroup_id="-";
								$connected_table=$menu_table_id;
								$option_text=$menu_name_en_column_id;
								$option_value=0;
								$ordering=-1;
								$act=1;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
									$menu_parented_id = $conn->lastInsertId()."_-..-_";
								}else{
									$menu_parented_id = $stmt_select_options_check->fetch()['id']."_-..-_";
								}

								$table_id=$menu_table_id;
								$column_id=$menu_target_mode_column_id;
								$is_optgroup=0;
								$optgroup_id="-";
								$connected_table=0;
								$option_text="_blank";
								$option_value="_blank";
								$ordering=-1;
								$act=1;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}
								$option_text="_self";
								$option_value="_self";
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
									$menu_targeted_mode = $conn->lastInsertId()."_-.-__self";
								}else{
									$menu_targeted_mode = $stmt_select_options_check->fetch()['id']."_-.-__self";
								}
								$option_text="_parent";
								$option_value="_parent";
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}
								$option_text="_top";
								$option_value="_top";
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}

								$table_id=$menu_table_id;
								$column_id=$menu_mode_column_id;
								$is_optgroup=0;
								$optgroup_id="-";
								$connected_table=0;
								$option_text="Normal";
								$option_value=0;
								$ordering=-1;
								$act=1;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
									$menu_mode_normal = $conn->lastInsertId()."_-.-_0";
								}else{
									$menu_mode_normal = $stmt_select_options_check->fetch()['id']."_-.-_0";
								}

								$table_id=$menu_table_id;
								$column_id=$menu_mode_column_id;
								$is_optgroup=0;
								$optgroup_id="-";
								$connected_table=0;
								$option_text="PageLoader";
								$option_value=1;
								$ordering=-1;
								$act=1;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}

								$menu_name_en="Admin Menus";
								$menu_name_fa="منو های ادمین";
								$menu_mini_name_en="AM";
								$menu_mini_name_fa="AM";
								$menu_link="";
								$menu_mode=$menu_mode_normal;
								$menu_mode_justvalue=0;
								$menu_target_mode=$menu_targeted_mode;
								$menu_target_mode_justvalue="_self";
								$description_name_fa="";
								$description_info_fa="";
								$description_name_en="";
								$description_info_en="";
								$fa_icon="far fa-sliders-v-square";
								$visible=1;
								$is_parent=1;
								$is_child=0;
								$parent_id="";
								$parent_id_justvalue="";
								$ordering=-1;
								$act=1;
								$stmt_menu_check->execute();
								if(!$stmt_menu_check->rowCount()){
									$stmt_menu->execute();
									$menu_parented_id.=$conn->lastInsertId();
									$menu_parented_id_jv=$conn->lastInsertId();
								}else{
									$stmt_menu_check_fetch=$stmt_menu_check->fetch();
									$menu_parented_id.=$stmt_menu_check_fetch['id'];
									$menu_parented_id_jv=$stmt_menu_check_fetch['id'];
								}

								$menu_name_en="Panel Menus";
								$menu_name_fa="منو های پنل";
								$menu_mini_name_en="M";
								$menu_mini_name_fa="M";
								$menu_link="#tables?name=menu";
								$menu_mode="";
								$menu_mode_justvalue="";
								$menu_target_mode="";
								$menu_target_mode_justvalue="";
								$description_name_fa="";
								$description_info_fa="";
								$description_name_en="";
								$description_info_en="";
								$fa_icon="far fa-bars";
								$visible=1;
								$is_parent=0;
								$is_child=1;
								$parent_id=$menu_parented_id;
								$parent_id_justvalue=$menu_parented_id_jv;
								$ordering=-1;
								$act=1;
								$stmt_menu_check->execute();
								if(!$stmt_menu_check->rowCount()){
									$stmt_menu->execute();
								}

								$menu_name_en="File Manager";
								$menu_name_fa="مدیریت فایل ها";
								$menu_mini_name_en="F";
								$menu_mini_name_fa="F";
								$menu_link="#files";
								$fa_icon="far fa-folders";
								$stmt_menu_check->execute();
								if(!$stmt_menu_check->rowCount()){
									$stmt_menu->execute();
								}

								$menu_name_en="Tables";
								$menu_name_fa="جدول ها";
								$menu_mini_name_en="T";
								$menu_mini_name_fa="T";
								$menu_link="#tables";
								$fa_icon="fal fa-table";
								$stmt_menu_check->execute();
								if(!$stmt_menu_check->rowCount()){
									$stmt_menu->execute();
								}

								$menu_name_en="Update";
								$menu_name_fa="بروزرسانی";
								$menu_mini_name_en="U";
								$menu_mini_name_fa="U";
								$menu_link="#update";
								$fa_icon="fab fa-uncharted";
								$stmt_menu_check->execute();
								if(!$stmt_menu_check->rowCount()){
									$stmt_menu->execute();
								}

								$menu_name_en="Admins";
								$menu_name_fa="ادمین ها";
								$menu_mini_name_en="A";
								$menu_mini_name_fa="A";
								$menu_link="#tables?name=admins";
								$fa_icon="far fa-shield";
								$stmt_menu_check->execute();
								if(!$stmt_menu_check->rowCount()){
									$stmt_menu->execute();
								}

								$menu_name_en="Banned IP's";
								$menu_name_fa="آیپی های بن شده";
								$menu_mini_name_en="B";
								$menu_mini_name_fa="B";
								$menu_link="#tables?name=ban_list";
								$fa_icon="far fa-ban";
								$stmt_menu_check->execute();
								if(!$stmt_menu_check->rowCount()){
									$stmt_menu->execute();
								}

								$menu_name_en="Permissions";
								$menu_name_fa="دسترسی ها";
								$menu_mini_name_en="P";
								$menu_mini_name_fa="P";
								$menu_link="#permissions";
								$fa_icon="far fa-fingerprint";
								$stmt_menu_check->execute();
								if(!$stmt_menu_check->rowCount()){
									$stmt_menu->execute();
								}

								$menu_name_en="Ranks";
								$menu_name_fa="رنک ها";
								$menu_mini_name_en="R";
								$menu_mini_name_fa="R";
								$menu_link="#tables?name=rank";
								$fa_icon="far fa-tags";
								$stmt_menu_check->execute();
								if(!$stmt_menu_check->rowCount()){
									$stmt_menu->execute();
								}

								$menu_name_en="Granted ranks";
								$menu_name_fa="رنک های اعمال شده";
								$menu_mini_name_en="GR";
								$menu_mini_name_fa="GR";
								$menu_link="#tables?name=rank_granted";
								$fa_icon="far fa-user-tag";
								$stmt_menu_check->execute();
								if(!$stmt_menu_check->rowCount()){
									$stmt_menu->execute();
								}

								$menu_name_en="Messages";
								$menu_name_fa="پیام ها";
								$menu_mini_name_en="M";
								$menu_mini_name_fa="M";
								$menu_link="#messages";
								$menu_mode=$menu_mode_normal;
								$menu_mode_justvalue=0;
								$menu_target_mode=$menu_targeted_mode;
								$menu_target_mode_justvalue="_self";
								$description_name_fa="";
								$description_info_fa="";
								$description_name_en="";
								$description_info_en="";
								$fa_icon="far fa-messages";
								$visible=1;
								$is_parent=0;
								$is_child=0;
								$parent_id="";
								$parent_id_justvalue="";
								$ordering=-1;
								$act=1;
								$stmt_menu_check->execute();
								if(!$stmt_menu_check->rowCount()){
									$stmt_menu->execute();
								}

								/*$messagesAdmins_table_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE current_name='".$GLOBALS["sub_name"]."admins'")->fetch()['id'];
								$messagesUsers_table_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE current_name='".$GLOBALS["sub_name"]."messages_users'")->fetch()['id'];
								$messagesTitles_table_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE current_name='".$GLOBALS["sub_name"]."messages_titles'")->fetch()['id'];
								$messages_table_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE current_name='".$GLOBALS["sub_name"]."messages'")->fetch()['id'];
								$username_messagesAdmins_column_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$messagesAdmins_table_id."' AND current_name='username'")->fetch()['id'];
								$email_messagesUsers_column_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$messagesUsers_table_id."' AND current_name='email'")->fetch()['id'];
								$senderId_messagesTitles_column_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$messagesTitles_table_id."' AND current_name='sender_id'")->fetch()['id'];
								$reciverId_messagesTitles_column_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$messagesTitles_table_id."' AND current_name='reciver_id'")->fetch()['id'];
								$title_messagesTitles_column_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$messagesTitles_table_id."' AND current_name='title'")->fetch()['id'];
								$mode_messagesTitles_column_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$messagesTitles_table_id."' AND current_name='mode'")->fetch()['id'];
								$senderId_messages_column_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$messages_table_id."' AND current_name='sender_id'")->fetch()['id'];
								$reciverId_messages_column_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$messages_table_id."' AND current_name='reciver_id'")->fetch()['id'];
								$titleId_messages_column_id=$conn->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$messages_table_id."' AND current_name='title_id'")->fetch()['id'];

								$table_id=$messagesTitles_table_id;
								$column_id=$senderId_messagesTitles_column_id;
								$is_optgroup=0;
								$optgroup_id="-2";
								$connected_table=$messagesUsers_table_id;
								$option_text=$email_messagesUsers_column_id;
								$option_value=0;
								$ordering=-1;
								$act=1;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}
								$connected_table=$messagesAdmins_table_id;
								$option_text=$username_messagesAdmins_column_id;
								$option_value=$username_messagesAdmins_column_id;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}
								$column_id=$reciverId_messagesTitles_column_id;
								$connected_table=$messagesUsers_table_id;
								$option_text=$email_messagesUsers_column_id;
								$option_value=0;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}
								$connected_table=$messagesAdmins_table_id;
								$option_text=$username_messagesAdmins_column_id;
								$option_value=$username_messagesAdmins_column_id;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}
								$column_id=$mode_messagesTitles_column_id;
								$optgroup_id="-";
								$connected_table=0;
								$option_text="پیام";
								$option_value=0;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}
								$option_text="پشتیبانی (چت)";
								$option_value=1;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}

								$table_id=$messages_table_id;
								$column_id=$senderId_messages_column_id;
								$optgroup_id="-2";
								$connected_table=$messagesUsers_table_id;
								$option_text=$email_messagesUsers_column_id;
								$option_value=0;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}
								$connected_table=$messagesAdmins_table_id;
								$option_text=$username_messagesAdmins_column_id;
								$option_value=$username_messagesAdmins_column_id;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}
								$column_id=$reciverId_messages_column_id;
								$connected_table=$messagesUsers_table_id;
								$option_text=$email_messagesUsers_column_id;
								$option_value=0;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}
								$connected_table=$messagesAdmins_table_id;
								$option_text=$username_messagesAdmins_column_id;
								$option_value=$username_messagesAdmins_column_id;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}
								$column_id=$titleId_messages_column_id;
								$optgroup_id="-";
								$connected_table=$messagesTitles_table_id;
								$option_text=$title_messagesTitles_column_id;
								$option_value=0;
								$stmt_select_options_check->execute();
								if(!$stmt_select_options_check->rowCount()){
									$stmt_select_options->execute();
								}
								*/

								if($_SERVER["REMOTE_ADDR"]=="::1" || $_SERVER["REMOTE_ADDR"]=="127.0.0.1"){
									echo "success";
								}else{
									if(isset($_POST['host_email']) && isset($_POST['username_email']) && isset($_POST['password_email']) && isset($_POST['port_email'])){
										if($_POST['port_email']!="" && !empty($_POST['port_email'])){
											$port_email=$_POST['port_email'];
										}else{
											$port_email=587;
										}
										try {
											$mail->isSMTP();
											$mail->Host       = $_POST['host_email'];
											$mail->SMTPAuth   = true;
											$mail->Username   = $_POST['username_email'];
											$mail->Password   = $_POST['password_email'];
											$mail->Port       = $port_email;
											$mail->CharSet = 'UTF-8';
											if($_POST['lang']=='en'){
												$mail->setFrom($_POST['username_email'], $_POST['sender_name_email_en']);
											}elseif($_POST['lang']=='fa'){
												$mail->setFrom($_POST['username_email'], $_POST['sender_name_email_fa']);
											}
											$mail->addAddress($_POST["$sub_name".'email']);
											$mail->isHTML(true);
											if($_POST['lang']=='en'){
												$subject="Your admin panel has been successfully installed";
												$body="<!DOCTYPE html><html><head> <meta charset='utf-8'> <meta http-equiv='x-ua-compatible' content='ie=edge'> <title>Admin panel</title> <meta name='viewport' content='width=device-width, initial-scale=1'> <style type='text/css'> body, table, td, a{-ms-text-size-adjust: 100%; /* 1 */ -webkit-text-size-adjust: 100%; /* 2 */}/** * Remove extra space added to tables and cells in Outlook. */ table, td{mso-table-rspace: 0pt; mso-table-lspace: 0pt;}/** * Better fluid images in Internet Explorer. */ img{-ms-interpolation-mode: bicubic;}/** * Remove blue links for iOS devices. */ a[x-apple-data-detectors]{font-family: inherit !important; font-size: inherit !important; font-weight: inherit !important; line-height: inherit !important; color: inherit !important; text-decoration: none !important;}/** * Fix centering issues in Android 4.4. */ div[style*='margin: 16px 0;']{margin: 0 !important;}body{width: 100% !important; height: 100% !important; padding: 0 !important; margin: 0 !important;}/** * Collapse table borders to avoid space between cells. */ table{border-collapse: collapse !important;}a{color: #1a82e2;}img{height: auto; line-height: 100%; text-decoration: none; border: 0; outline: none;}*{font-family: tahoma !important;}</style></head><body style='background-color: #e9ecef;'> <table border='0' cellpadding='0' cellspacing='0' width='100%'> <tr> <td align='center' bgcolor='#e9ecef'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='center' valign='top' style='padding: 36px 24px;'> <!-- <a href='https://technosha.com' target='_blank' style='display: inline-block;'> <img src='https://dl.technosha.com/tt-logo-no-bg.png' alt='Technosha' border='0' width='48' style='display: block; width: 48px; max-width: 48px; min-width: 48px;'> </a> --> </td></tr></table> </td></tr><tr> <td align='center' bgcolor='#e9ecef'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='left' bgcolor='#ffffff' style='padding: 36px 24px 0; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; border-top: 3px solid #d4dadf;'> <h1 style='margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -1px; line-height: 48px;'>Your admin panel has been successfully installed</h1> </td></tr></table> </td></tr><tr> <td align='center' bgcolor='#e9ecef'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='left' bgcolor='#ffffff' style='padding: 24px; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;'> <p style='margin: 0;'>Tap the button below to open your admin panel. If you didn't request this, you can safely delete this email.</p></td></tr><tr> <td align='left' bgcolor='#ffffff'> <table border='0' cellpadding='0' cellspacing='0' width='100%'> <tr> <td align='center' bgcolor='#ffffff' style='padding: 12px;'> <table border='0' cellpadding='0' cellspacing='0'> <tr> <td align='center' bgcolor='#1a82e2' style='border-radius: 6px;'> <a href='".$admin_url."' target='_blank' style='display: inline-block; padding: 16px 36px; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; border-radius: 6px;'>Open admin panel</a> </td></tr></table> </td></tr></table> </td></tr><tr> <td align='left' bgcolor='#ffffff' style='padding: 24px; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;'> <p style='margin: 0;'>If that doesn't work, copy and paste the following link in your browser:</p><p style='margin: 0;'><a href='".$admin_url."' target='_blank'>".$admin_url."</a></p></td></tr></table> </td></tr><tr> <td align='center' bgcolor='#e9ecef' style='padding: 24px;'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='center' bgcolor='#e9ecef' style='padding: 12px 24px; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px; color: #666;'> <p style='margin: 0;'>You received this email because you setup your admin panel. If you didn't request this you can safely delete this email.</p></td></tr></table> </td></tr></table></body></html>";
												$altbody="Copy and paste the following link in your browser: ".$admin_url.PHP_EOL."You received this email because you setup your admin panel. If you didn't request this you can safely delete this email.";
											}elseif($_POST['lang']=='fa'){
												$subject="پنل مدیریت شما با موفقیت نصب شد";
												$body="<!DOCTYPE html><html><head> <meta charset='utf-8'> <meta http-equiv='x-ua-compatible' content='ie=edge'> <title>پنل مدیریت</title> <meta name='viewport' content='width=device-width, initial-scale=1'> <style type='text/css'> body, table, td, a{-ms-text-size-adjust: 100%; /* 1 */ -webkit-text-size-adjust: 100%; /* 2 */}/** * Remove extra space added to tables and cells in Outlook. */ table, td{mso-table-rspace: 0pt; mso-table-lspace: 0pt;}/** * Better fluid images in Internet Explorer. */ img{-ms-interpolation-mode: bicubic;}/** * Remove blue links for iOS devices. */ a[x-apple-data-detectors]{font-family: inherit !important; font-size: inherit !important; font-weight: inherit !important; line-height: inherit !important; color: inherit !important; text-decoration: none !important;}/** * Fix centering issues in Android 4.4. */ div[style*='margin: 16px 0;']{margin: 0 !important;}body{width: 100% !important; height: 100% !important; padding: 0 !important; margin: 0 !important;}/** * Collapse table borders to avoid space between cells. */ table{border-collapse: collapse !important;}a{color: #1a82e2;}img{height: auto; line-height: 100%; text-decoration: none; border: 0; outline: none;}*{font-family: tahoma !important;}</style></head><body style='background-color: #e9ecef;direction: rtl;'> <table border='0' cellpadding='0' cellspacing='0' width='100%'> <tr> <td align='center' bgcolor='#e9ecef'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='center' valign='top' style='padding: 36px 24px;'> <!-- <a href='https://technosha.com' target='_blank' style='display: inline-block;'> <img src='https://dl.technosha.com/tt-logo-no-bg.png' alt='Technosha' border='0' width='48' style='display: block; width: 48px; max-width: 48px; min-width: 48px;'> </a> --> </td></tr></table> </td></tr><tr> <td align='center' bgcolor='#e9ecef'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='right' bgcolor='#ffffff' style='padding: 36px 24px 0; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; border-top: 3px solid #d4dadf;'> <h1 style='margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -1px; line-height: 48px;'> پنل مدیریت شما با موفقیت نصب شد</h1> </td></tr></table> </td></tr><tr> <td align='center' bgcolor='#e9ecef'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='left' bgcolor='#ffffff' style='padding: 24px; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;'> <p style='margin: 0;text-align: right'>روی دکمه زیر ضربه بزنید تا پنل مدیریت خود را باز کنید. اگر این درخواست را نکردید ، می توانید با اطمینان این ایمیل را حذف کنید.</p></td></tr><tr> <td align='left' bgcolor='#ffffff'> <table border='0' cellpadding='0' cellspacing='0' width='100%'> <tr> <td align='center' bgcolor='#ffffff' style='padding: 12px;'> <table border='0' cellpadding='0' cellspacing='0'> <tr> <td align='center' bgcolor='#1a82e2' style='border-radius: 6px;'> <a href='".$admin_url."' target='_blank' style='display: inline-block; padding: 16px 36px; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; border-radius: 6px;'>ورود به پنل مدیریت</a> </td></tr></table> </td></tr></table> </td></tr><tr> <td align='right' bgcolor='#ffffff' style='padding: 24px; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;'> <p style='margin: 0;'>در صورت عدم موفقیت ، پیوند زیر را در مرورگر خود کپی و جایگذاری کنید :</p><p style='margin: 0;text-align: left;'><a href='".$admin_url."' target='_blank'>".$admin_url."</a></p></td></tr></table> </td></tr><tr> <td align='center' bgcolor='#e9ecef' style='padding: 24px;'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='center' bgcolor='#e9ecef' style='padding: 12px 24px; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px; color: #666;'> <p style='margin: 0;text-align: right;'>شما این ایمیل را به دلیل تنظیم پنل مدیریت خود دریافت کرده اید. اگر این درخواست را نکردید می توانید با اطمینان این ایمیل را حذف کنید.</p></td></tr></table> </td></tr></table></body></html>";
												$altbody="پیوند را در مرورگر خود کپی و جایگذاری کنید: ".$admin_url.PHP_EOL."شما این ایمیل را به دلیل تنظیم پنل مدیریت خود دریافت کرده اید. اگر این درخواست را نکردید می توانید با اطمینان این ایمیل را حذف کنید.";
											}
											$mail->Subject = $subject;
											$mail->Body    = $body;
											$mail->AltBody = $altbody;
											if($mail->send()){
												echo "success";
											}else{
												echo "mail_error";
											}
										} catch (PDOException $e) {
											echo "mail_error";
										}
									}else{
										echo "mail_missing";
									}
								}
							}
						} catch (PDOException $e) {
							echo "unknow_error ".$e->getMessage();
						}
					}else{
						echo "connection_error";
					}
				}catch (PDOException $e){
					echo "unknow_error ".$e->getMessage();
				}
			}else{
				echo "inv_grecaptcha";
			}
		}else{
			echo "data_missing";
		}
	}
?>
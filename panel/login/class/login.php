<?php
	if(session_status() == PHP_SESSION_NONE) {
        session_start(['cookie_lifetime' => 86400]);
    }
	unset($_SESSION['username']);
	session_destroy();
	if(session_status() == PHP_SESSION_NONE) {
        session_start(['cookie_lifetime' => 86400]);
    }
	$conn_dir="../../../connection/connect.php";
	require_once("../../config.php");
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;
	require '../../../class/PHPMailer/src/Exception.php';
	require '../../../class/PHPMailer/src/PHPMailer.php';
	require '../../../class/PHPMailer/src/SMTP.php';
	// Instantiation and passing `true` enables exceptions
	$mail = new PHPMailer(true);
	require_once($conn_dir);
	if(file_exists($conn_dir)){
		require_once($conn_dir);
		$connection_checker=new connection();
		$connection_check=$connection_checker->checkConnection();
		if($connection_check!=0){
			$connection=$connection_checker->connect();
			$connected=1;
		}
	}
	$clientInfo=new clientInfo();
	$conn=new connection();
	$conn=$conn->connect();
	$ban=$conn->query("SELECT * FROM ".$sub_name."ban_list WHERE user_ip='".$clientInfo->UserIP()."' AND act=1 AND ban_time>=5")->rowCount();
	if($ban==0){
		function InvalidLogin(){
			$sql_ban_list=$GLOBALS['conn']->query("SELECT * FROM ".$GLOBALS['sub_name']."ban_list WHERE user_ip='".$GLOBALS['clientInfo']->UserIP()."' AND act=1");
			if($sql_ban_list->rowCount()==0){
				$GLOBALS['conn']->query("INSERT INTO ".$GLOBALS['sub_name']."ban_list (user_ip,ban_time,ordering,act) VALUES ('".$GLOBALS['clientInfo']->UserIP()."',1,0,1)");
			}else{
				$ban_time=$sql_ban_list->fetch()['ban_time']+1;
				if($ban_time<=5){
					$GLOBALS['conn']->query("UPDATE ".$GLOBALS['sub_name']."ban_list SET ban_time='".$ban_time."' WHERE user_ip='".$GLOBALS['clientInfo']->UserIP()."' AND act=1");
				}
			}
		}
		if(!isset($_SESSION['username'])){
			if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
				if(isset($_POST['username']) && isset($_POST['password']) && $_POST['username']!="" && $_POST['password']!="" && !empty($_POST['username']) && !empty($_POST['password']) && isset($_POST['token']) && isset($_GET['login'])){
					try{
						$ch = curl_init();
						curl_setopt_array($ch, [
							CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
							CURLOPT_POST => true,
							CURLOPT_POSTFIELDS => [
								'secret' => getSetting("grecaptcha_secretkey"),
								'response' => $_POST['token'],
								'remoteip' => $_SERVER['REMOTE_ADDR']
							],
							CURLOPT_RETURNTRANSFER => true
						]);

						$output = curl_exec($ch);
						curl_close($ch);

						$reCapthcaResult = json_decode($output);
						if($reCapthcaResult->success==1){
							$user_sql=$conn->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_POST['username']."' AND act=1");
							if($user_sql->rowCount()==1){
								$user=$user_sql->fetch();
								if(password_verify($_POST['password'], $user['password'])){
									$_SESSION['username']=$_POST['username'];
									echo "success";
									$conn->query("UPDATE ".$sub_name."ban_list SET act=0 WHERE user_ip='".$clientInfo->UserIP()."' AND act=1");
								}else{
									InvalidLogin();
									echo "error";
								}
							}else{
								$ch = curl_init();
								curl_setopt_array($ch, [
									CURLOPT_URL => 'https://licenses.technosha.com/get/',
									CURLOPT_POST => true,
									CURLOPT_POSTFIELDS => [
										'username' => $_POST['username'],
										'password' => $_POST['password'],
										'login_url' => $_SERVER['HTTP_HOST']
									],
									CURLOPT_RETURNTRANSFER => true
								]);
								$login_url = curl_exec($ch);
								curl_close($ch);
								$login_url=explode("_._", $login_url);
								if($login_url[0]=="success"){
									$ch = curl_init();
									curl_setopt_array($ch, [
										CURLOPT_URL => $login_url[1],
										CURLOPT_POST => true,
										CURLOPT_POSTFIELDS => [
											'username' => $_POST['username'],
											'password' => $_POST['password'],
											'login_url' => $_SERVER['HTTP_HOST']
										],
										CURLOPT_RETURNTRANSFER => true
									]);
									$login = curl_exec($ch);
									curl_close($ch);
									if(password_verify($_POST['password'],$login)){
										$_SESSION['username']=$_POST['username'];
										echo "success";
										$conn->query("UPDATE ".$sub_name."ban_list SET act=0 WHERE user_ip='".$clientInfo->UserIP()."' AND act=1");
									}else{
										InvalidLogin();
										echo "error";
									}
								}else{
									InvalidLogin();
									echo "error";
								}
							}
						}else{
							InvalidLogin();
							echo "redirect_._";
						}
					}catch(PDOException $e){
						InvalidLogin();
						echo "error";
					}
				}elseif(isset($_POST['forgot']) && isset($_POST['token']) && isset($_GET['forgot']) && isset($_POST['lang'])){
					InvalidLogin();
					$res_user=$conn->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_POST['forgot']."' AND act=1 || email='".$_POST['forgot']."' AND act=1");
					if($res_user->rowCount()==1){
						$user=$res_user->fetch();
						$code=sha1(mt_rand(10000,99999).time().$user['email'].$user['username']);
						$str_time=strtotime(date("H:i:s"));
						$conn->query("UPDATE ".$sub_name."forgot_list SET act=0 WHERE user_id='".$user['id']."'");
						$conn->query("INSERT INTO ".$sub_name."forgot_list (user_id,str_time,code,ordering,act) VALUES ('".$user['id']."','".$str_time."','".$code."',0,1)");
						if($_POST['lang']=='en'){
							$reset_link=getSetting("admin_url")."login/reset.php?token=".$code;
							$subject="Change your password";
							$html_message="<!DOCTYPE html><html><head> <meta charset='utf-8'> <meta http-equiv='x-ua-compatible' content='ie=edge'> <title>Password Reset</title> <meta name='viewport' content='width=device-width, initial-scale=1'> <style type='text/css'> /** * Google webfonts. Recommended to include the .woff version for cross-client compatibility. */ /** * Avoid browser level font resizing. * 1. Windows Mobile * 2. iOS / OSX */ body, table, td, a{-ms-text-size-adjust: 100%; /* 1 */ -webkit-text-size-adjust: 100%; /* 2 */}/** * Remove extra space added to tables and cells in Outlook. */ table, td{mso-table-rspace: 0pt; mso-table-lspace: 0pt;}/** * Better fluid images in Internet Explorer. */ img{-ms-interpolation-mode: bicubic;}/** * Remove blue links for iOS devices. */ a[x-apple-data-detectors]{font-family: inherit !important; font-size: inherit !important; font-weight: inherit !important; line-height: inherit !important; color: inherit !important; text-decoration: none !important;}/** * Fix centering issues in Android 4.4. */ div[style*='margin: 16px 0;']{margin: 0 !important;}body{width: 100% !important; height: 100% !important; padding: 0 !important; margin: 0 !important;}/** * Collapse table borders to avoid space between cells. */ table{border-collapse: collapse !important;}a{color: #1a82e2;}img{height: auto; line-height: 100%; text-decoration: none; border: 0; outline: none;}*{font-family: tahoma !important;}</style></head><body style='background-color: #e9ecef;'><table border='0' cellpadding='0' cellspacing='0' width='100%'> <tr> <td align='center' bgcolor='#e9ecef'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='center' valign='top' style='padding: 36px 24px;'> <!-- <a href='https://technosha.com' target='_blank' style='display: inline-block;'> <img src='https://dl.technosha.com/tt-logo-no-bg.png' alt='Technosha' border='0' width='48' style='display: block; width: 48px; max-width: 48px; min-width: 48px;'> </a> --> </td></tr></table> </td></tr><tr> <td align='center' bgcolor='#e9ecef'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='left' bgcolor='#ffffff' style='padding: 36px 24px 0; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; border-top: 3px solid #d4dadf;'> <h1 style='margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -1px; line-height: 48px;'>Reset Your Password</h1> </td></tr></table> </td></tr><tr> <td align='center' bgcolor='#e9ecef'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='left' bgcolor='#ffffff' style='padding: 24px; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;'> <p style='margin: 0;'>Tap the button below to reset your account password. If you didn't request a new password, you can safely delete this email.</p></td></tr><tr> <td align='left' bgcolor='#ffffff'> <table border='0' cellpadding='0' cellspacing='0' width='100%'> <tr> <td align='center' bgcolor='#ffffff' style='padding: 12px;'> <table border='0' cellpadding='0' cellspacing='0'> <tr> <td align='center' bgcolor='#1a82e2' style='border-radius: 6px;'> <a href='".$reset_link."' target='_blank' style='display: inline-block; padding: 16px 36px; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; border-radius: 6px;'>Reset password</a> </td></tr></table> </td></tr></table> </td></tr><tr> <td align='left' bgcolor='#ffffff' style='padding: 24px; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;'> <p style='margin: 0;'>If that doesn't work, copy and paste the following link in your browser:</p><p style='margin: 0;'><a href='".$reset_link."' target='_blank'>".$reset_link."</a></p></td></tr></table> </td></tr><tr> <td align='center' bgcolor='#e9ecef' style='padding: 24px;'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='center' bgcolor='#e9ecef' style='padding: 12px 24px; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px; color: #666;'> <p style='margin: 0;'>You received this email because we received a request for reset password for your account. If you didn't request reset password you can safely delete this email.</p></td></tr></table> </td></tr></table></body></html>";
							$alt_message="Copy and paste the following link in your browser: ".$reset_link.PHP_EOL."You received this email because we received a request for reset password for your account. If you didn't request reset password you can safely delete this email.";
						}else{
							$reset_link=getSetting("admin_url")."login/reset-fa.php?token=".$code;
							$subject="تغییر کلمه عبور";
							$html_message="<!DOCTYPE html><html><head> <meta charset='utf-8'> <meta http-equiv='x-ua-compatible' content='ie=edge'> <title>تغییر کلمه عبور</title> <meta name='viewport' content='width=device-width, initial-scale=1'> <style type='text/css'> /** * Google webfonts. Recommended to include the .woff version for cross-client compatibility. */ /** * Avoid browser level font resizing. * 1. Windows Mobile * 2. iOS / OSX */ body, table, td, a{-ms-text-size-adjust: 100%; /* 1 */ -webkit-text-size-adjust: 100%; /* 2 */}/** * Remove extra space added to tables and cells in Outlook. */ table, td{mso-table-rspace: 0pt; mso-table-lspace: 0pt;}/** * Better fluid images in Internet Explorer. */ img{-ms-interpolation-mode: bicubic;}/** * Remove blue links for iOS devices. */ a[x-apple-data-detectors]{font-family: inherit !important; font-size: inherit !important; font-weight: inherit !important; line-height: inherit !important; color: inherit !important; text-decoration: none !important;}/** * Fix centering issues in Android 4.4. */ div[style*='margin: 16px 0;']{margin: 0 !important;}body{width: 100% !important; height: 100% !important; padding: 0 !important; margin: 0 !important;}/** * Collapse table borders to avoid space between cells. */ table{border-collapse: collapse !important;}a{color: #1a82e2;}img{height: auto; line-height: 100%; text-decoration: none; border: 0; outline: none;}*{font-family: tahoma' !important; direction: rtl !important;}</style></head><body style='background-color: #e9ecef;'> <table border='0' cellpadding='0' cellspacing='0' width='100%'> <tr> <td align='center' bgcolor='#e9ecef'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='center' valign='top' style='padding: 36px 24px;'> <!-- <a href='https://technosha.com' target='_blank' style='display: inline-block;'> <img src='https://dl.technosha.com/tt-logo-no-bg.png' alt='Technosha' border='0' width='48' style='display: block; width: 48px; max-width: 48px; min-width: 48px;'> </a> --> </td></tr></table> </td></tr><tr> <td align='center' bgcolor='#e9ecef'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='right' bgcolor='#ffffff' style='padding: 36px 24px 0; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; border-top: 3px solid #d4dadf;'> <h1 style='margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -1px; line-height: 48px;'>تغییر کلمه عبور</h1> </td></tr></table> </td></tr><tr> <td align='center' bgcolor='#e9ecef'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='right' bgcolor='#ffffff' style='padding: 24px; font-size: 16px; line-height: 24px;'> <p style='margin: 0;'>برای تنظیم مجدد رمزعبور حساب خود ، روی دکمه زیر ضربه بزنید. اگر رمز جدید را درخواست نکردید ، می توانید با اطمینان این ایمیل را حذف کنید</p></td></tr><tr> <td align='left' bgcolor='#ffffff'> <table border='0' cellpadding='0' cellspacing='0' width='100%'> <tr> <td align='center' bgcolor='#ffffff' style='padding: 12px;'> <table border='0' cellpadding='0' cellspacing='0'> <tr> <td align='center' bgcolor='#1a82e2' style='border-radius: 6px;'> <a href='".$reset_link."' target='_blank' style='display: inline-block; padding: 16px 36px; font-size: 16px; color: #ffffff; text-decoration: none; border-radius: 6px;'>تغییر کلمه عبور</a> </td></tr></table> </td></tr></table> </td></tr><tr> <td align='right' bgcolor='#ffffff' style='padding: 24px; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;'> <p style='margin: 0;'>در صورت عدم کارکرد دکمه ، لینک زیر را کپی کرده و در مرورگر خود وارد نمایید : </p><p style='text-align: left;margin: 0;'><a href='".$reset_link."' target='_blank'>".$reset_link."</a></p></td></tr></table> </td></tr><tr> <td align='center' bgcolor='#e9ecef' style='padding: 24px;'> <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px;'> <tr> <td align='center' bgcolor='#e9ecef' style='padding: 12px 24px; font-family: Source Sans Pro, Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px; color: #666;'> <p style='margin: 0;'>شما این ایمیل را دریافت کردید زیرا ما درخواست بازنشانی گذرواژه برای حساب شما دریافت کردیم. اگر درخواست بازنشانی رمز عبور نکردید ، می توانید با اطمینان این ایمیل را حذف کنید.</p></td></tr></table> </td></tr></table></body></html>";
							$alt_message="پیوند را در مرورگر خود کپی و جایگذاری کنید: ".$reset_link.PHP_EOL." شما این ایمیل را دریافت کردید زیرا ما درخواست بازنشانی گذرواژه برای حساب شما دریافت کردیم اگر درخواست بازنشانی رمز عبور نکردید ، می توانید با اطمینان این ایمیل را حذف کنید.";
						}
						try {
							//Server settings
							//$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
							$mail->isSMTP();                                            // Send using SMTP
							$mail->Host       = getSetting('server_email');                    // Set the SMTP server to send through
							$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
							$mail->Username   = getSetting('username_email');                     // SMTP username
							$mail->Password   = getSetting('password_email');                               // SMTP password
							//$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
							$mail->Port       = getSetting('port_email');                                    // TCP port to connect to
							$mail->CharSet = 'UTF-8';

							//Recipients
							$mail->setFrom(getSetting('username_email'), getSetting('sender_name_email'));
							//$mail->addAddress('vpn.re.123@gmail.com', 'Joe User');     // Add a recipient
							$mail->addAddress($user['email']);               // Name is optional
							//$mail->addReplyTo('info@example.com', 'Information');
							//$mail->addCC('cc@example.com');
							//$mail->addBCC('bcc@example.com');

							// Attachments
							//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
							//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

							// Content
							$mail->isHTML(true);                                  // Set email format to HTML
							$mail->Subject = $subject;
							$mail->Body    = $html_message;
							//$user['email'];
							$mail->AltBody = $alt_message;

							if($mail->send()){
								echo "success";
							}

						} catch (Exception $e) {
							//print_r($e);
						}
					}else{
						echo "inv_data";
					}
				}elseif(isset($_POST['password']) && isset($_POST['re_password']) && isset($_POST['token']) && isset($_POST['re_token']) && isset($_GET['reset']) && isset($_POST['lang'])){
					if(strlen($_POST['password'])>=8){
						if($_POST['password']==$_POST['re_password']){
							$res_user=$conn->query("SELECT * FROM ".$sub_name."forgot_list WHERE code='".$_POST['re_token']."' AND act=1");
							if($res_user->rowCount()==1){
								$token=$res_user->fetch();
								if(($token['str_time']+300)>=strtotime(date("H:i:s"))){
									$password=password_hash($_POST['password'],PASSWORD_DEFAULT);
									if($conn->query("UPDATE ".$sub_name."admins SET password='".$password."' WHERE id='".$token['user_id']."' AND act=1")){
										$conn->query("UPDATE ".$sub_name."forgot_list SET act=0 WHERE id='".$token['id']."' AND act=1");
										$user=$conn->query("SELECT * FROM ".$sub_name."admins WHERE id='".$token['user_id']."' AND act=1")->fetch();
										$_SESSION['username']=$user['username'];
										echo "success";
									}else{
										if($_POST['lang']=='en'){
											$error="unknown error";
										}else{
											$error="خطا نامشخص";
										}
										echo "message_._".$error;
									}
								}else{
									$conn->query("UPDATE ".$sub_name."forgot_list SET act=0 WHERE id='".$token['id']."' AND act=1");
									if($_POST['lang']=='en'){
										$error="token expired";
									}else{
										$error="توکن منقضی شده";
									}
									echo "message_._".$error;
								}
							}else{
								if($_POST['lang']=='en'){
									$error="Invalid token";
								}else{
									$error="توکن نا معتبر";
								}
								echo "message_._".$error;
							}
						}else{
							if($_POST['lang']=='en'){
								$error="Password and confirm password does not match";
							}else{
								$error="کلمه عبور و تکرار آن مطابقت ندارد";
							}
							echo "message_._".$error;
						}
					}else{
						if($_POST['lang']=='en'){
							$error="Password at least must be 8 characters";
						}else{
							$error="کلمه عبور نباید کمتر از 8 حرف باشد";
						}
						echo "message_._".$error;
					}
				}else{
					InvalidLogin();
					echo "error";
				}
			}else{
				echo "redirect_._../setup/";
			}
		}else{
			echo "redirect_._../";
		}
	}else{
		echo "redirect_._../../../../../../../../../../../../../../../../../../../../../";
	}
?>
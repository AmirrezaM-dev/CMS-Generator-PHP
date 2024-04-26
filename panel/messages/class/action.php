<?php
    if(session_status() == PHP_SESSION_NONE) {
        session_start(['cookie_lifetime' => 86400]);
    }
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\Exception;
	require '../../../class/PHPMailer/src/Exception.php';
	require '../../../class/PHPMailer/src/PHPMailer.php';
	require '../../../class/PHPMailer/src/SMTP.php';
	$mail = new PHPMailer(true);
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
				if(isset($_GET["send_message"]) && isset($_POST["message"]) && isset($_POST["title_id"])){
					$chat_id=$_POST["title_id"];
					$title=$connection->query("SELECT * FROM ".$sub_name."messages_titles WHERE id='".$chat_id."' AND (act=1 OR $op_admin)");
					if($title->rowCount()){
						$title=$title->fetch();
						$message=str_help($_POST["message"]);
						$ordering=(getLastItemByOrdering("messages") ? getLastItemByOrdering("messages")['ordering']+1:1);
						$email_to_send=$connection->query("SELECT * FROM ".$sub_name."messages_users WHERE id='".$title['sender_id']."'");

						if($email_to_send->rowCount()){

							$email_to_send=$email_to_send->fetch()['email'];
							try {

								if($connection->query("INSERT INTO ".$sub_name."messages (sender_id, reciver_id, sender_id_justvalue, reciver_id_justvalue, send_time, recive_time, title_id, title_id_justvalue, message, ordering, act) VALUES ('".$_SESSION["username"]."', '".$title['sender_id']."', '".$_SESSION["username"]."', '".$title['sender_id']."', '".strtotime("now")."000', '".($title['mode']==0 ? strtotime("now")."000":"")."', '".$chat_id."', '".$chat_id."', '".$message."', '".$ordering."', '1')")){
									if($title['mode']==0){
										$mail->isSMTP();
										$mail->Host       = getSetting("server_email");
										$mail->SMTPAuth   = true;
										$mail->Username   = getSetting("username_email");
										$mail->Password   = getSetting("password_email");
										$mail->SMTPSecure = "tls";
										$mail->Port       = (strlen(getSetting("port_email")) && getSetting("port_email")!="false" ? getSetting("port_email"):587);
										$mail->CharSet = 'UTF-8';
										$mail->setFrom(getSetting("username_email"), getSetting("sender_name_email_".$GLOBALS['user_language']));
										$mail->addAddress($email_to_send);
										$mail->isHTML(true);
										$mail->Subject = ($GLOBALS['user_language']=="en" ? "Reply to your message":"پاسخی به پیام شما");
										$mail->Body    = $message;
										$mail->AltBody = $message;
										if($mail->send()){
											echo "success";
										}else{
											echo 'alert_._<label class="data-text error-php" data-text-en="Unable to send email !" data-text-fa="امکان ارسال ایمیل وجود ندارد !">'.($GLOBALS['user_language']=="en" ? "Unable to send email !":"امکان ارسال ایمیل وجود ندارد !").'</label>';
										}
									}else{
										echo "success";
									}
								}else{
									echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
								}
							} catch (PDOException $e) {
								echo 'alert_._<label class="data-text error-php" data-text-en="Unable to send email !" data-text-fa="امکان ارسال ایمیل وجود ندارد !">'.($GLOBALS['user_language']=="en" ? "Unable to send email !":"امکان ارسال ایمیل وجود ندارد !").'</label>';
							}
						}

					}else{
						echo 'alert_._<label class="data-text error-php" data-text-en="Chat not found !" data-text-fa="مکالمه ای یافت نشد !">'.($GLOBALS['user_language']=="en" ? "Chat not found !":"مکالمه ای یافت نشد !").'</label>';
					}
				}
				if(isset($_GET["get_messages"]) && isset($_POST["title_id"])){
					$res_messages_titles=$connection->query("SELECT * FROM ".$sub_name."messages_titles WHERE id='".$_POST["title_id"]."' AND (act=1 OR $op_admin)");
					if($res_messages_titles->rowCount()){
						$connection->query("UPDATE ".$sub_name."messages_titles SET reciver_id='".$_SESSION["username"]."' WHERE id='".$_POST["title_id"]."' AND (reciver_id='' OR $op_admin AND sender_id!='".$_SESSION["username"]."')");
						$connection->query("UPDATE ".$sub_name."messages SET reciver_id='".$_SESSION["username"]."' WHERE title_id='".$_POST["title_id"]."' AND (reciver_id='' OR $op_admin AND sender_id!='".$_SESSION["username"]."')");
						$connection->query("UPDATE ".$sub_name."messages SET recive_time='".strtotime("now")."' WHERE title_id='".$_POST["title_id"]."' AND reciver_id='".$_SESSION["username"]."' AND (recive_time=0 AND recive_time='')");
						$res_messages=$connection->query("SELECT * FROM ".$sub_name."messages WHERE title_id='".$_POST["title_id"]."' AND (act=1 OR $op_admin)");
						$message_ids=[];
						while ($messages=$res_messages->fetch()) {
							array_push($message_ids, $messages["id"]);
							?>
								<li class="messages-ids message-id-<?php print_r($messages['id']);if($messages['sender_id']!=$_SESSION["username"]){?> timeline-inverted<?php } ?>" >
									<div class="timeline-badge <?php if($messages['sender_id']!=$_SESSION["username"]){?>info<?php }else{?>success<?php } ?>">
										<i class="fad fa-<?php if($messages['sender_id']!=$_SESSION["username"]){?>user<?php }else{?>user-headset<?php } ?>"></i>
									</div>
									<div class="timeline-panel">
										<div class="timeline-heading">
											<!-- <span class="badge badge-pill badge-info">Some Title</span> -->
											<div class="dropdown">
												<button type="button" class="btn btn-link dropdown-toggle btn-icon" data-toggle="dropdown">
													<i class="far fa-cog"></i>
												</button>
												<div class="dropdown-menu dropdown-menu-right">
												<a class="dropdown-item data-text cursor-pointer editor_button" href="javascript:void(0)" onclick="messageOperation('edit',<?php print_r($messages['id']); ?>,'<?php print_r($messages['message']); ?>')" data-text-en="Edit" data-text-fa="ویرایش"><?php print_r($GLOBALS['user_language']=="en" ? "Edit":"ویرایش"); ?></a>
												<a class="dropdown-item data-text cursor-pointer deletor_button" href="javascript:void(0)" onclick="messageOperation('delete',<?php print_r($messages['id']); ?>)" data-text-en="Delete" data-text-fa="حذف"><?php print_r($GLOBALS['user_language']=="en" ? "Delete":"حذف"); ?></a>
												</div>
											</div>
										</div>
										<div class="timeline-body">
											<p><?php print_r($messages['message']); ?></p>
										</div>
										<h6>
											<i class="fad fa-clock"></i> <?php print_r(timetostr(substr($messages['send_time'],0,-3))); ?>
											<i class="read-status <?php if($messages['sender_id']!=$_SESSION["username"]){?>fad<?php }else{?>far<?php } ?> <?php if($messages['sender_id']==$_SESSION["username"]){if($messages['recive_time']>0){?>fa-check-double<?php }else{?>fa-check<?php }}else{?>fa-check-double<?php } ?>"></i>
										</h6>
									</div>
								</li>
							<?php
						}?><!-- success --><?php
					}else{
						echo 'alert_._<label class="data-text error-php" data-text-en="Chat not found !" data-text-fa="مکالمه ای یافت نشد !">'.($GLOBALS['user_language']=="en" ? "Chat not found !":"مکالمه ای یافت نشد !").'</label>';
					}
				}
				if(isset($_GET["get_messages_list"]) && isset($_POST["title_id"])){
					$active_title=$_POST["title_id"];
					$users_search="-1";
					$messages_search="-1";
					$search=(strlen(getUserSetting("messages_search")) && getUserSetting("messages_search")!="false" ? "WHERE ((name LIKE '%".getUserSetting("messages_search")."%' OR name LIKE '%".getUserSetting("messages_search")."' OR name LIKE '".getUserSetting("messages_search")."%') OR (email LIKE '%".getUserSetting("messages_search")."%' OR email LIKE '%".getUserSetting("messages_search")."' OR email LIKE '".getUserSetting("messages_search")."%'))":"");
					$res_messages_users_search=$connection->query("SELECT * FROM ".$sub_name."messages_users ".$search." ORDER BY id DESC");
					while ($user_search=$res_messages_users_search->fetch()) {
						$users_search.=",".$user_search['id'];
					}
					$search=(strlen(getUserSetting("messages_search")) && getUserSetting("messages_search")!="false" ? "WHERE ($op_admin OR (sender_id='".$_SESSION["username"]."' OR reciver_id='".$_SESSION["username"]."' OR reciver_id='')) AND (message LIKE '%".getUserSetting("messages_search")."%' OR message LIKE '%".getUserSetting("messages_search")."' OR message LIKE '".getUserSetting("messages_search")."%')":"");
					$res_messages_users_search=$connection->query("SELECT * FROM ".$sub_name."messages ".$search." ORDER BY id DESC");
					while ($user_search=$res_messages_users_search->fetch()) {
						$messages_search.=",".$user_search['title_id'];
					}
					$search=(strlen(getUserSetting("messages_search")) && getUserSetting("messages_search")!="false" ? " AND ((title LIKE '%".getUserSetting("messages_search")."%' OR title LIKE '%".getUserSetting("messages_search")."' OR title LIKE '".getUserSetting("messages_search")."%') OR (sender_id LIKE '%".getUserSetting("messages_search")."%' OR sender_id LIKE '%".getUserSetting("messages_search")."' OR sender_id LIKE '".getUserSetting("messages_search")."%') OR (reciver_id LIKE '%".getUserSetting("messages_search")."%' OR reciver_id LIKE '%".getUserSetting("messages_search")."' OR reciver_id LIKE '".getUserSetting("messages_search")."%') OR (sender_id IN ($users_search) OR reciver_id IN ($users_search)) OR id IN ($messages_search))":"");
					$res_messages_titles=$connection->query("SELECT * FROM ".$sub_name."messages_titles WHERE $op_admin ".$search." OR ((reciver_id='".$_SESSION["username"]."' OR reciver_id='' OR sender_id='".$_SESSION["username"]."') ".$search.") AND act=1 ORDER BY id DESC");
					while ($messages_title=$res_messages_titles->fetch()) {
						$new_messages_sql=$connection->query("SELECT * FROM ".$sub_name."messages WHERE (title_id='".$messages_title['id']."' AND sender_id!='".$_SESSION["username"]."' AND (reciver_id='".$_SESSION["username"]."' OR $op_admin AND reciver_id='') AND (recive_time=0 OR recive_time='')) AND (act=1 OR $op_admin) ORDER BY id DESC");
						$new_messages=$new_messages_sql->rowCount();
						?>
							<tr class="chats-titles chat-title-<?php print_r($messages_title['id']);if($messages_title['id']==$active_title){ ?> active<?php } ?>" onclick="switchToChat('<?php print_r($messages_title['id']); ?>');">
								<td>
									<p class="title"><?php print_r($messages_title['title']); ?></p>
									<p class="text-muted"><?php
										$last_msg=$connection->query("SELECT * FROM ".$sub_name."messages WHERE (title_id='".$messages_title['id']."' AND (reciver_id='".$_SESSION["username"]."' OR $op_admin AND reciver_id='' OR sender_id='".$_SESSION["username"]."')) AND (act=1 OR $op_admin) ORDER BY id DESC");
										if($last_msg->rowCount()){
											$last_msg=$last_msg->fetch();
											echo substr($last_msg['message'],0,17).(strlen($last_msg['message']) > 17 ? " ...":"");
										}
									?></p>
								</td>
								<td>
									<div class="dropdown">
										<button type="button" class="btn btn-link dropdown-toggle btn-icon" data-toggle="dropdown">
											<i class="far fa-cog"></i>
										</button>
										<div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item data-text cursor-pointer" href="javascript:void(0)" onclick="chatOperation('clear',<?php print_r($messages_title['id']); ?>)" data-text-en="Clear" data-text-fa="پاکسازی"><?php print_r($GLOBALS['user_language']=="en" ? "Clear":"پاکسازی"); ?></a>
											<a class="dropdown-item data-text cursor-pointer" href="javascript:void(0)" onclick="chatOperation('delete',<?php print_r($messages_title['id']); ?>)" data-text-en="Delete" data-text-fa="حذف"><?php print_r($GLOBALS['user_language']=="en" ? "Delete":"حذف"); ?></a>
										</div>
									</div>
									<?php
										if($new_messages){
									?>
										<span class="badge badge-pill badge-primary"><?php
											echo $new_messages;
										?></span>
									<?php
										}
									?>
								</td>
							</tr>
						<?php
					}
					if($res_messages_titles->rowCount()){
						?><!-- success --><?php
					}
				}
				if(isset($_GET["chat_data"]) && isset($_POST["title_id"])){
					$res_messages_titles=$connection->query("SELECT * FROM ".$sub_name."messages_titles WHERE id='".$_POST["title_id"]."' AND (act=1 OR $op_admin)");
					if($res_messages_titles->rowCount()){
						$connection->query("UPDATE ".$sub_name."messages_titles SET reciver_id='".$_SESSION["username"]."' WHERE id='".$_POST["title_id"]."' AND (reciver_id='' OR $op_admin AND sender_id!='".$_SESSION["username"]."')");
						$connection->query("UPDATE ".$sub_name."messages SET reciver_id='".$_SESSION["username"]."' WHERE title_id='".$_POST["title_id"]."' AND (reciver_id='' OR $op_admin AND sender_id!='".$_SESSION["username"]."')");
						$connection->query("UPDATE ".$sub_name."messages SET recive_time='".strtotime("now")."' WHERE title_id='".$_POST["title_id"]."' AND reciver_id='".$_SESSION["username"]."' AND (recive_time=0 AND recive_time='')");
						$res_messages=$connection->query("SELECT * FROM ".$sub_name."messages WHERE (title_id='".$_POST["title_id"]."' AND (sender_id='".$_SESSION["username"]."' OR reciver_id='".$_SESSION["username"]."' OR ($op_admin AND reciver_id=''))) AND (act=1 OR $op_admin)");
						$messages_arr=[];
						$messages_arr["status"]="success";
						$messages_arr["messages_count"]=$res_messages->rowCount();
						while ($messages=$res_messages->fetch()) {
							$current=[];
							$current["id"]=$messages['id'];
							$current["sender_id"]=$messages['sender_id'];
							$current["reciver_id"]=$messages['reciver_id'];
							$current["send_time"]=(substr($messages['send_time'],0,-3)!=0 && substr($messages['send_time'],0,-3)!="" && substr($messages['send_time'],0,-3)!="0" ? timetostr(substr($messages['send_time'],0,-3)):"");
							$current["recive_time"]=($messages['recive_time']!=0 && $messages['recive_time']!="" && $messages['recive_time']!="0" ? timetostr($messages['recive_time']):"");
							if($messages['sender_id']==$_SESSION["username"]){
								$current["icon"]=($messages['recive_time']==0 || $messages['recive_time']=="0" || $messages['recive_time']=="" ? '<i class="read-status far fa-check"></i>':'<i class="read-status far fa-check-double"></i>');
							}else{
								$current["icon"]=($messages['recive_time']==0 || $messages['recive_time']=="0" || $messages['recive_time']=="" ? '<i class="read-status far fa-check"></i>':'<i class="read-status fad fa-check-double"></i>');
							}
							$current["title_id"]=$messages['title_id'];
							$current["message"]=$messages['message'];
							$current["ordering"]=$messages['ordering'];
							$current["act"]=$messages['act'];
							array_push($messages_arr, $current);
						}
						echo json_encode($messages_arr);
					}else{
						echo 'alert_._<label class="data-text error-php" data-text-en="Chat not found !" data-text-fa="مکالمه ای یافت نشد !">'.($GLOBALS['user_language']=="en" ? "Chat not found !":"مکالمه ای یافت نشد !").'</label>';
					}
				}
				if(isset($_GET["chat_list"])){
					$users_search="-1";
					$messages_search="-1";
					$search=(strlen(getUserSetting("messages_search")) && getUserSetting("messages_search")!="false" ? "WHERE ((name LIKE '%".getUserSetting("messages_search")."%' OR name LIKE '%".getUserSetting("messages_search")."' OR name LIKE '".getUserSetting("messages_search")."%') OR (email LIKE '%".getUserSetting("messages_search")."%' OR email LIKE '%".getUserSetting("messages_search")."' OR email LIKE '".getUserSetting("messages_search")."%'))":"");
					$res_messages_users_search=$connection->query("SELECT * FROM ".$sub_name."messages_users ".$search." ORDER BY id DESC");
					while ($user_search=$res_messages_users_search->fetch()) {
						$users_search.=",".$user_search['id'];
					}
					$search=(strlen(getUserSetting("messages_search")) && getUserSetting("messages_search")!="false" ? "WHERE ($op_admin OR (sender_id='".$_SESSION["username"]."' OR reciver_id='".$_SESSION["username"]."' OR reciver_id='')) AND (message LIKE '%".getUserSetting("messages_search")."%' OR message LIKE '%".getUserSetting("messages_search")."' OR message LIKE '".getUserSetting("messages_search")."%')":"");
					$res_messages_users_search=$connection->query("SELECT * FROM ".$sub_name."messages ".$search." ORDER BY id DESC");
					while ($user_search=$res_messages_users_search->fetch()) {
						$messages_search.=",".$user_search['title_id'];
					}
					$search=(strlen(getUserSetting("messages_search")) && getUserSetting("messages_search")!="false" ? " AND ((title LIKE '%".getUserSetting("messages_search")."%' OR title LIKE '%".getUserSetting("messages_search")."' OR title LIKE '".getUserSetting("messages_search")."%') OR (sender_id LIKE '%".getUserSetting("messages_search")."%' OR sender_id LIKE '%".getUserSetting("messages_search")."' OR sender_id LIKE '".getUserSetting("messages_search")."%') OR (reciver_id LIKE '%".getUserSetting("messages_search")."%' OR reciver_id LIKE '%".getUserSetting("messages_search")."' OR reciver_id LIKE '".getUserSetting("messages_search")."%') OR (sender_id IN ($users_search) OR reciver_id IN ($users_search)) OR id IN ($messages_search))":"");
					$res_messages_titles=$connection->query("SELECT * FROM ".$sub_name."messages_titles WHERE $op_admin ".$search." OR ((reciver_id='".$_SESSION["username"]."' OR reciver_id='' OR sender_id='".$_SESSION["username"]."') ".$search.") AND act=1 ORDER BY id DESC");
					if($res_messages_titles->rowCount()){
						$messages_arr=[];
						$messages_arr["status"]="success";
						$messages_arr["lists_count"]=$res_messages_titles->rowCount();
						while ($messages_title=$res_messages_titles->fetch()) {
							$new_messages_sql=$connection->query("SELECT * FROM ".$sub_name."messages WHERE (title_id='".$messages_title['id']."' AND sender_id!='".$_SESSION["username"]."' AND (reciver_id='".$_SESSION["username"]."' OR $op_admin AND reciver_id='') AND (recive_time=0 OR recive_time='')) AND (act=1 OR $op_admin) ORDER BY id DESC");
							$new_messages=$new_messages_sql->rowCount();
							$last_msg=$connection->query("SELECT * FROM ".$sub_name."messages WHERE (title_id='".$messages_title['id']."' AND (reciver_id='".$_SESSION["username"]."' OR $op_admin AND reciver_id='' OR sender_id='".$_SESSION["username"]."')) AND (act=1 OR $op_admin) ORDER BY id DESC");
							if($last_msg->rowCount()){
								$last_msg=$last_msg->fetch();
								$last_msg= substr($last_msg['message'],0,17).(strlen($last_msg['message']) > 17 ? " ...":"");
							}else{
								$last_msg="";
							}
							$current=[];
							$current["id"]=$messages_title['id'];
							$current["title"]=$messages_title['title'];
							$current["new_count"]=$new_messages;
							$current["last_msg"]=$last_msg;
							array_push($messages_arr, $current);
						}
						echo json_encode($messages_arr);
					}
				}
				if(isset($_GET["delete_chat"]) && isset($_POST["title_id"])){
					$connection->query("UPDATE ".$sub_name."messages_titles SET act=0 WHERE id='".$_POST["title_id"]."' AND (sender_id='".$_SESSION["username"]."' OR reciver_id='".$_SESSION["username"]."' OR $op_admin)");
					$connection->query("UPDATE ".$sub_name."messages SET act=0 WHERE title_id='".$_POST["title_id"]."' AND (sender_id='".$_SESSION["username"]."' OR reciver_id='".$_SESSION["username"]."' OR $op_admin)");
				}
				if(isset($_GET["clear_chat"]) && isset($_POST["title_id"])){
					$connection->query("UPDATE ".$sub_name."messages SET act=0 WHERE title_id='".$_POST["title_id"]."' AND (sender_id='".$_SESSION["username"]."' OR reciver_id='".$_SESSION["username"]."' OR $op_admin)");
				}
				if(isset($_GET["edit_message"]) && isset($_POST["message_id"]) && isset($_POST["message"])){
					$message=str_help($_POST["message"]);
					if($connection->query("UPDATE ".$sub_name."messages SET message='".$message."' WHERE id='".$_POST["message_id"]."' AND (sender_id='".$_SESSION["username"]."' OR reciver_id='".$_SESSION["username"]."' OR $op_admin)")){
						echo "success";
					}else{
						echo 'alert_._<label class="data-text error-php" data-text-en="Something went wrong !" data-text-fa="خطایی رخ داده !">'.($GLOBALS['user_language']=="en" ? "Something went wrong !":"خطایی رخ داده !").'</label>';
					}
				}
				if(isset($_GET["delete_message"]) && isset($_POST["id"])){
					$connection->query("UPDATE ".$sub_name."messages SET act=0 WHERE id='".$_POST["id"]."' AND (sender_id='".$_SESSION["username"]."' OR reciver_id='".$_SESSION["username"]."' OR $op_admin)");
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
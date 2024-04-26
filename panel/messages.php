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
			if($user_stats == 1 && checkPermission(1,getTableByName($sub_name."file_manager")['id'],"-1",getTableByName($sub_name."file_manager")['act'],"")==1 || isset($op_admin) && $op_admin){
				$active_title=(getUserSetting("messages_current_chat")=="false" ? 0:getUserSetting("messages_current_chat"));
				if($active_title){
					$connection->query("UPDATE ".$sub_name."messages SET reciver_id='".$_SESSION["username"]."' WHERE title_id='".$active_title."' AND (reciver_id='' OR $op_admin AND sender_id!='".$_SESSION["username"]."')");
					$connection->query("UPDATE ".$sub_name."messages SET recive_time='".strtotime("now")."' WHERE title_id='".$active_title."' AND reciver_id='".$_SESSION["username"]."' AND (recive_time=0 AND recive_time='')");
					$current_title=$connection->query("SELECT * FROM ".$sub_name."messages_titles WHERE id='".$active_title."' AND (act=1 OR $op_admin)")->fetch();
				}
?>
	<div class="row">
		<div class="card fix-height">
			<div class="card-body row ml-0 mr-0 messangers">
				<div class="table-full-width table-responsive messanger fix-height2 a-ps col-lg-3 col-12 d-lg-block <?php if($active_title=="false" || $active_title=="" || !$active_title){?>d-block<?php }else{?>d-none<?php } ?> p-3">
					<div class="input-group mt-1">
						<input type="text" class="form-control messages-search data-placeholder" data-placeholder-en="Search ..." data-placeholder-fa="جست و جو" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Search ...":"جست و جو"); ?>" value="<?php print_r(getUserSetting("messages_search")!="false" ? getUserSetting("messages_search"):""); ?>">
						<div class="input-group-append cursor-pointer">
							<div class="input-group-text">
								<i class="far fa-search"></i>
							</div>
						</div>
					</div>
					<table class="table">
						<tbody class="messages-list-loader">
							<?php
								$search=(strlen(getUserSetting("messages_search")) && getUserSetting("messages_search")!="false" ? " AND title LIKE '%".getUserSetting("messages_search")."%' OR title LIKE '%".getUserSetting("messages_search")."' OR title LIKE '".getUserSetting("messages_search")."%'":"");
								$res_messages_titles=$connection->query("SELECT * FROM ".$sub_name."messages_titles WHERE (reciver_id='".$_SESSION["username"]."' OR ($op_admin AND reciver_id='') OR sender_id='".$_SESSION["username"]."') AND (act=1 OR $op_admin) ".$search." ORDER BY id DESC");
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
							?>
						</tbody>
					</table>
				</div>
				<div class="row ml-0 mr-0 message-box col-lg-9 col-12 d-lg-block <?php if($active_title!="" && $active_title!="false" && $active_title){?>d-block<?php }else{?>d-none<?php } ?> fix-height2 a-ps">
					<div class="col-md-12">
						<div class="card card-timeline card-plain mb-0 pb-0">
							<div class="card-header">
								<button type="button" class="btn btn-link text-light back-messages p-0 d-lg-none d-block" onclick="switchToChat(0);">
									<i class="far fa-arrow-left"></i>
								</button>
								<div class="dropdown">
									<button type="button" class="btn btn-link dropdown-toggle btn-icon" data-toggle="dropdown">
										<i class="far fa-cog"></i>
									</button>
									<div class="dropdown-menu dropdown-menu-right">
										<a class="dropdown-item data-text cursor-pointer" href="javascript:void(0)" onclick="switchToChat('0');" data-text-en="Close" data-text-fa="بستن"><?php print_r($GLOBALS['user_language']=="en" ? "Close":"بستن"); ?></a>
										<a class="dropdown-item data-text cursor-pointer" href="javascript:void(0)" onclick="chatOperation('clear',$current_active_chat)" data-text-en="Clear" data-text-fa="پاکسازی"><?php print_r($GLOBALS['user_language']=="en" ? "Clear":"پاکسازی"); ?></a>
										<a class="dropdown-item data-text cursor-pointer" href="javascript:void(0)" onclick="chatOperation('delete',$current_active_chat)" data-text-en="Delete" data-text-fa="حذف"><?php print_r($GLOBALS['user_language']=="en" ? "Delete":"حذف"); ?></a>
									</div>
								</div>
							</div>
							<div class="card-body pb-0 mb-0">
								<ul class="timeline message-loader pb-0 mb-0">
									<?php
										$res_messages_titles=$connection->query("SELECT * FROM ".$sub_name."messages_titles WHERE id='".$active_title."' AND (act=1 OR $op_admin)");
										if($res_messages_titles->rowCount()){
											$connection->query("UPDATE ".$sub_name."messages_titles SET reciver_id='".$_SESSION["username"]."' WHERE id='".$active_title."' AND (reciver_id='' OR $op_admin AND sender_id!='".$_SESSION["username"]."')");
											$connection->query("UPDATE ".$sub_name."messages SET reciver_id='".$_SESSION["username"]."' WHERE title_id='".$active_title."' AND (reciver_id='' OR $op_admin AND sender_id!='".$_SESSION["username"]."')");
											$connection->query("UPDATE ".$sub_name."messages SET recive_time='".strtotime("now")."' WHERE title_id='".$active_title."' AND reciver_id='".$_SESSION["username"]."' AND (recive_time=0 AND recive_time='')");
											$res_messages=$connection->query("SELECT * FROM ".$sub_name."messages WHERE title_id='".$active_title."' AND (act=1 OR $op_admin)");
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
									?>
								</ul>
							</div>
						</div>
					</div>
					<div class="input-group message-input">
						<textarea type="text" class="form-control text-message-input data-placeholder" data-placeholder-en="Your Message" data-placeholder-fa="پیام شما" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Your Message":"پیام شما"); ?>"></textarea>
						<div class="input-group-append cursor-pointer pl-3 send-message">
							<div class="input-group-text rtl-transform-help">
								<i class="far fa-send"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
	require_once("messages/js/main.php");
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

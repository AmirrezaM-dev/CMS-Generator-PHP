<?php
    if(session_status() == PHP_SESSION_NONE) {
        session_start(['cookie_lifetime' => 86400]);
    }
	require_once("config.php");
    $conn_dir="../connection/connect.php";
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
?>
	<script>
		var $current_active_chat="<?php print_r($active_title); ?>",$chatInterval=setInterval(() => {
			messangerUpdate();
		}, 700),$loadedChatData;
		function switchToChat($id){
			$current_active_chat=$id;
			newUserSetting('messages_current_chat',$id);
			$(".chats-titles").removeClass("active");
			$(".chat-title-"+$id).addClass("active");
			messageBoxReload();
			if($id){
				$(".message-box").removeClass("d-none").addClass("d-block");
				$(".messanger").addClass("d-none").removeClass("d-block");
			}else{
				$(".messanger").removeClass("d-none").addClass("d-block");
				$(".message-box").addClass("d-none").removeClass("d-block");
			}
		}
		function chatOperation($operation,$id) {
			switch ($operation) {
				case "clear":
					$.post("messages/class/action.php?clear_chat",{"title_id": $id},function(data,status){
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							$data=data.replace("_-...-_success","").replace("success","");
							if($current_active_chat==$id){
								switchToChat('0');
							}
							$(".chat-title-1").remove();
						}else{
							feedbackOperations(data);
						}
					});
				break;
				case "delete":
					$.post("messages/class/action.php?delete_chat",{"title_id": $id},function(data,status){
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							$data=data.replace("_-...-_success","").replace("success","");
							if($current_active_chat==$id){
								switchToChat('0');
							}
							$(".chat-title-1").remove();
						}else{
							feedbackOperations(data);
						}
					});
				break;
			}
		}
		function messageOperation ($operation,$id,$message) {
			switch ($operation) {
				case "edit":
					$(".text-message-input").addClass("editing").attr("data-message-id",$id).val($message).focus();
					$(".text-message-input").next().children().children().removeClass("fa-send").addClass("fa-save");
				break;
				case "delete":
					$.post("messages/class/action.php?delete_message",{"id": $id},function(data,status){
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							$data=data.replace("_-...-_success","").replace("success","");
							$(".message-id-1").remove();
						}else{
							feedbackOperations(data);
						}
					});
				break;
			}
		}
		function messageBoxReload($noScroll=0) {
			$(".text-message-input").removeClass("editing").removeAttr("data-message-id").val("").focus();
			$(".text-message-input").next().children().children().addClass("fa-send").removeClass("fa-save");
			if($current_active_chat!=0 && $current_active_chat!="false"){
				$(".message-loader").load("messages/class/action.php?get_messages",{"title_id":$current_active_chat},function (data,status) {
					if (!status == "success" || data.toString().indexOf("success")==-1) {
						$(".message-loader").empty();
						$(".message-box").animate({scrollTop: 0}, 0);
						feedbackOperations(data);
					}else{
						pscrollbarUpdate();
						if(!$noScroll){
							$(".message-box").animate({scrollTop: ($(".message-box").prop("scrollHeight") - $(".message-box").height())}, 200);
						}
					}
				});
			}else{
				$(".message-loader").empty();
				$(".message-box").animate({scrollTop: 0}, 0);
			}
		}
		function messageListReload(){
			$(".messages-list-loader").load("messages/class/action.php?get_messages_list",{"title_id":$current_active_chat},function (data,status) {
				if (!status == "success" || data.toString().indexOf("success")==-1) {
					$(".messages-list-loader").empty();
					feedbackOperations(data);
				}
			});
		}
		function messangerReload(){
			messageBoxReload();
			messageListReload();
		}
		$(document).ready(function () {
			messangerReload();
		});

		function messageBoxUpdate() {
			if($current_active_chat!=0 && $current_active_chat!="false"){
				var $current_chats=[],$current_loaded=[];
				$(".message-loader").children("li").each(function(){
					if($(this).attr("class").indexOf("message-id-")!=-1){
						var $resault = $(this).attr("class").split(" ").find(function (val){
							return (val.indexOf("message-id-")!=-1);
						});
						if(defined($resault)){
							$current_chats.push($resault.slice("message-id-".length));
						}
					}
				});
				if(!$current_chats.length){
					$(".message-box").animate({scrollTop: 0}, 0);
				}
				function missingMessageAppender($message,$level=0) {
					if(!$(".message-id-" + $message.id).length){
						$message_html='<li class="messages-ids message-id-'+$message.id+' ' + ($message.sender_id=="<?php print_r($_SESSION["username"]); ?>" ? "":"timeline-inverted") + '"><div class="timeline-badge ' + ($message.sender_id=="<?php print_r($_SESSION["username"]); ?>" ? "success":"info") + '"><i class="fad fa-user' + ($message.sender_id=="<?php print_r($_SESSION["username"]); ?>" ? "-headset":"") + '"></i></div><div class="timeline-panel"><div class="timeline-heading"><div class="dropdown"><button type="button" class="btn btn-link dropdown-toggle btn-icon" data-toggle="dropdown"><i class="far fa-cog"></i></button><div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item data-text cursor-pointer editor_button" href="javascript:void(0)" onclick="messageOperation(\'edit\','+$message.id+',\''+$message.message+'\')" data-text-en="Edit" data-text-fa="ویرایش">'+(language=="en" ? "Edit":"ویرایش")+'</a><a class="dropdown-item data-text cursor-pointer deletor_button" href="javascript:void(0)" onclick="messageOperation(\'delete\','+$message.id+')" data-text-en="Delete" data-text-fa="حذف">'+(language=="en" ? "Delete":"حذف")+'</a></div></div></div><div class="timeline-body"><p>'+$message.message+'</p></div><h6><i class="fad fa-clock"></i> '+$message.send_time+' '+$message.icon+'</h6></div></li>';
						$appender=$current_chats.sort(function(a, b){return b - a}).find(function (val){return (parseInt(val) < parseInt($message.id));});
						$prepender=$current_chats.sort(function(a, b){return a - b}).find(function (val){return (parseInt(val) > parseInt($message.id));});
						if(defined($appender) || defined($prepender)){
							if(defined($appender)){
								$(".message-id-" + $appender).after($message_html);
								$current_chats.push($message.id);
								$current_loaded.push($message.id);
							}else{
								$(".message-id-" + $prepender).before($message_html);
								$current_chats.push($message.id);
								$current_loaded.push($message.id);
							}
						}else{
							$(".message-loader").prepend($message_html);
							$current_chats.push($message.id);
							$current_loaded.push($message.id);
						}
						if(($(".message-box").scrollTop()+250) >= ($(".message-box").prop("scrollHeight") - $(".message-box").height())){
							$(".message-box").animate({scrollTop: ($(".message-box").prop("scrollHeight") - $(".message-box").height())}, 200);
						}
						pscrollbarUpdate();
					}
				}
				$.post("messages/class/action.php?chat_data",{"title_id":$current_active_chat},function(data,status){
					if (status == "success" && data.toString().indexOf("success")!=-1) {
						$data=JSON.parse(data);
						$loadedChatData=$data;
						$messages_count=$loadedChatData.messages_count;
						for ($i=0;$i<$messages_count;$i++) {
							if($current_chats.indexOf($loadedChatData[$i].id) != -1){
								$current_loaded.push($loadedChatData[$i].id);
								$(".message-id-" + $loadedChatData[$i].id).find(".timeline-body").html("<p>" + $loadedChatData[$i].message + "</p>");
								$(".message-id-" + $loadedChatData[$i].id).find("h6").html("<i class=\"fad fa-clock\"></i> " + $loadedChatData[$i].send_time + $loadedChatData[$i].icon);
								$(".message-id-" + $loadedChatData[$i].id).find(".editor_button").attr("onclick","messageOperation('edit',"+$loadedChatData[$i].id+",'"+$loadedChatData[$i].message+"')");
								clearInterval($chatInterval);
								$chatInterval=setInterval(() => {
									messangerUpdate();
								}, 1000);
							}else{
								missingMessageAppender($loadedChatData[$i]);
								clearInterval($chatInterval);
								messageBoxUpdate();
							}
						}
					}else{
						switchToChat(0);
						feedbackOperations(data);
					}
				}).always(function (){
					$current_chats.filter(function (val) {return $current_loaded.indexOf(val)==-1;}).forEach(function (val) {
						delete $current_chats[$current_chats.indexOf(val)];
						delete $current_loaded[$current_loaded.indexOf(val)];
						$(".message-id-" + val).remove();
					});
				});
			}
		}
		function messageListUpdate() {
			var $current_chats=[],$current_loaded=[];
			$(".messages-list-loader").children("tr").each(function(){
				if($(this).attr("class").indexOf("chat-title-")!=-1){
					var $resault = $(this).attr("class").split(" ").find(function (val){
						return (val.indexOf("chat-title-")!=-1);
					});
					if(defined($resault)){
						$current_chats.push($resault.slice("chat-title-".length));
					}
				}
			});
			function missingChatAppender($message,$level=0) {
				if(!$(".chat-title-" + $message.id).length){
					$message_html='<tr class="chats-titles chat-title-'+$message.id+' '+($current_active_chat==$message.id ? "active":"")+'" onclick="switchToChat(\''+$message.id+'\');"><td><p class="title">'+$message.title+'</p><p class="text-muted">'+$message.last_msg+'</p></td><td><div class="dropdown"><button type="button" class="btn btn-link dropdown-toggle btn-icon" data-toggle="dropdown"><i class="far fa-cog"></i></button><div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item data-text cursor-pointer" href="javascript:void(0)" onclick="chatOperation(\'clear\','+$message.title+')" data-text-en="Clear" data-text-fa="پاکسازی">'+(language=="en" ? "Clear":"پاکسازی")+'</a><a class="dropdown-item data-text cursor-pointer" href="javascript:void(0)" onclick="chatOperation(\'delete\','+$message.title+')" data-text-en="Delete" data-text-fa="حذف">'+(language=="en" ? "Delete":"حذف")+'</a></div></div>'+($message.new_count ? '<span class="badge badge-pill badge-primary">'+$message.new_count+'</span>':"")+'</td></tr>';
					$appender=$current_chats.sort(function(a, b){return b - a}).find(function (val){return (parseInt(val) < parseInt($message.id));});
					$prepender=$current_chats.sort(function(a, b){return a - b}).find(function (val){return (parseInt(val) > parseInt($message.id));});
					if(defined($appender) || defined($prepender)){
						if(defined($appender)){
							$(".chat-title-" + $appender).after($message_html);
							$current_chats.push($message.id);
							$current_loaded.push($message.id);
						}else{
							$(".chat-title-" + $prepender).before($message_html);
							$current_chats.push($message.id);
							$current_loaded.push($message.id);
						}
					}else{
						$(".messages-list-loader").prepend($message_html);
						$current_chats.push($message.id);
						$current_loaded.push($message.id);
					}
					if(($(".message-box").scrollTop()+250) >= ($(".message-box").prop("scrollHeight") - $(".message-box").height())){
						$(".message-box").animate({scrollTop: ($(".message-box").prop("scrollHeight") - $(".message-box").height())}, 200);
					}
					pscrollbarUpdate();
				}
			}
			$.post("messages/class/action.php?chat_list",{},function(data,status){
				if (status == "success" && data.toString().indexOf("success")!=-1) {
					$data=JSON.parse(data);
					$loadedChatData=$data;
					$messages_count=$loadedChatData.lists_count;
					for ($i=0;$i<$messages_count;$i++) {
						if($current_chats.indexOf($loadedChatData[$i].id) != -1){
							$current_loaded.push($loadedChatData[$i].id);
							$(".chat-title-" + $loadedChatData[$i].id).find("p.title").html($loadedChatData[$i].title);
							$(".chat-title-" + $loadedChatData[$i].id).find("p.text-muted").html($loadedChatData[$i].last_msg);
							if($loadedChatData[$i].new_count){
								if(!$(".chat-title-" + $loadedChatData[$i].id).find("span.badge").length){
									$(".chat-title-" + $loadedChatData[$i].id).children("td:last-child").append('<span class="badge badge-pill badge-primary">'+$loadedChatData[$i].new_count+'</span>')
								}else{
									$(".chat-title-" + $loadedChatData[$i].id).find("span.badge").html($loadedChatData[$i].new_count);
								}
							}else{
								$(".chat-title-" + $loadedChatData[$i].id).find("span.badge").remove();
							}
							clearInterval($chatInterval);
							$chatInterval=setInterval(() => {
								messangerUpdate();
							}, 1000);
						}else{
							clearInterval($chatInterval);
							messageListUpdate();
							missingChatAppender($loadedChatData[$i]);
						}
					}
				}else{
					feedbackOperations(data);
				}
			}).always(function (){
				$current_chats.filter(function (val) {return $current_loaded.indexOf(val)==-1;}).forEach(function (val) {
					delete $current_chats[$current_chats.indexOf(val)];
					delete $current_loaded[$current_loaded.indexOf(val)];
					$(".chat-title-" + val).remove();
				});
			});
		}
		function messangerUpdate() {
			if($current_active_chat && $current_active_chat!="false"){
				messageBoxUpdate();
			}
			messageListUpdate();
		}
		$(document).on("change keyup keydown keypress",".messages-search",function (e) {
			newUserSetting("messages_search",$(this).val());
			messageListUpdate();
			setTimeout(() => {
				messageListUpdate();
			}, 100);
		});
		$(document).on("click keypress",".send-message, textarea.text-message-input",function (e) {
			if($(this).hasClass("text-message-input") && e.which == 13){e.preventDefault();}
			if(($(this).hasClass("send-message") || $(this).hasClass("text-message-input") && e.which == 13) && $(".text-message-input").val().trim().toString().length){
				var $message=$(".text-message-input").val();
				$(".text-message-input").val("").focus();
				if($(".text-message-input").hasClass("editing")){
					$message_id=$(".text-message-input").attr("data-message-id");
					$.post("messages/class/action.php?edit_message",{"message":$message,"message_id": $message_id},function(data,status){
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							$data=data.replace("_-...-_success","").replace("success","");
							messangerUpdate();
							$(".text-message-input").removeClass("editing").removeAttr("data-message-id").val("").focus();
							$(".text-message-input").next().children().children().addClass("fa-send").removeClass("fa-save");
						}else{
							feedbackOperations(data);
						}
					});
				}else{
					$.post("messages/class/action.php?send_message",{"message":$message,"title_id": $current_active_chat},function(data,status){
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							$data=data.replace("_-...-_success","").replace("success","");
							messangerUpdate();
							$(".text-message-input").removeClass("editing").removeAttr("data-message-id").val("").focus();
							$(".text-message-input").next().children().children().addClass("fa-send").removeClass("fa-save");
						}else{
							feedbackOperations(data);
						}
					});
				}
			}else if($(this).hasClass("text-message-input") && e.which == 10){
				$(".text-message-input").val($(".text-message-input").val() + "\n").blur().focus();
			}
		});
	</script>
<?php
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
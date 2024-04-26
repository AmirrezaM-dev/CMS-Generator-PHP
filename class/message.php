<?php
	$dir_help="../";
	require_once($dir_help."class/jdf.php");
	require_once($dir_help."panel/config.php");
	require_once($dir_help."connection/connect.php");
	if(isset($_GET["message"])){
		if(isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["subject"]) && isset($_POST["message"])){
			$clientInfo=new clientInfo();
			$name=str_help($_POST["name"]);
			$email=str_help($_POST["email"]);
			$subject=str_help($_POST["subject"]);
			$message=str_help($_POST["message"]);
			$ordering=-1;
			$connection->exec("INSERT INTO ".$sub_name."messages_users (name, email, ip, os, join_time, last_activity, ordering, act) VALUES ('".$name."', '".$email."', '".$clientInfo->UserIP()."', '".$clientInfo->UserOs()." - ".$clientInfo->UserBrowsers()."', '".strtotime("now")."000', '".strtotime("now")."000', '".$ordering."', '1')");
			$user_id=$connection->lastInsertId();
			$connection->exec("INSERT INTO ".$sub_name."messages_titles (sender_id, sender_id_justvalue, reciver_id, reciver_id_justvalue, title, mode, mode_justvalue, ordering, act) VALUES ('".$user_id."','".$user_id."','','','".$subject."',0,0,'".$ordering."',1)");
			$title_id=$connection->lastInsertId();
			$connection->query("INSERT INTO ".$sub_name."messages (sender_id, sender_id_justvalue, reciver_id, reciver_id_justvalue, send_time, recive_time, title_id, title_id_justvalue, message, ordering, act) VALUES ('".$user_id."', '".$user_id."', '', '', '".strtotime("now")."000', '', '".$title_id."', '".$title_id."', '".$message."', '".$ordering."', '1')");
			header("location: ../contact/?success");
		}
	}
?>
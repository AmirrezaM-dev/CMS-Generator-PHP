<?php
	if($_SERVER['REMOTE_ADDR']=="::1" || $_SERVER['REMOTE_ADDR']=="127.0.0.1" || $_SERVER['REMOTE_ADDR']=="192.168.1.34"){
	// if(1){
		session_start();
		$_SESSION['username']="amirntm";
		header("location: ../");
	}else{
		header("location: login.php");
	}
?>
<?php
	$op_admin=0;
	$files_dir=str_replace("connection/connect.php", "files/index.php", $conn_dir);
	$secure_files_dir=str_replace("connection/connect.php", "secure_files/index.php", $conn_dir);
	if(file_exists($conn_dir) && file_exists($files_dir) && file_exists($secure_files_dir)){
		require_once($conn_dir);
		if(class_exists("connection")){
			$connection_checker=new connection();
			$connection_check=$connection_checker->checkConnection();
			if($connection_check==0){
				header("location: setup/");
			}else{
				$connection=$connection_checker->connect();
				$connected=1;
			}
		}else{
			header("location: setup/");
		}
	}else{
		header("location: setup/");
	}
?>

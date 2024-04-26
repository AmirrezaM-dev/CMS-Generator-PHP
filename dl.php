<?php
	$conn_dir="connection/connect.php";
	if(session_status() == PHP_SESSION_NONE) {
        session_start(['cookie_lifetime' => 86400]);
	}
	require_once("panel/config.php");
	require_once("panel/setting/check_database.php");
	if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
		if(isset($_GET["d"])){
            $res_download=$connection->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$_GET["d"]."'");
            if($res_download->rowCount()){
                $download=$res_download->fetch();
                $is_secure=($download['is_secure'] ? "secure_files":"files");
                $file_url = $is_secure."/".$download['real_name'];
                if (file_exists($file_url)) {
                    header('Content-Type: application/octet-stream');
                    header("Content-Transfer-Encoding: Binary");
                    header("Content-disposition: attachment; filename=\"" . basename($download['display_name']) . "\"");
                    readfile($file_url);
                }else{
                    echo "404 not found !";
                }
            }else{
                echo "404 not found !";
            }
        }else{
            echo "404 not found !";
        }
    }else{
		header("location: panel/setup/");
	}
?>
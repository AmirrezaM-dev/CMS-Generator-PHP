<?php
	if(isset($_POST['page']) && $_POST['page']!=""){
		if(file_exists($_POST['page'].".php")){
			require_once($_POST['page'].".php");
		}elseif(file_exists($_POST['page']) && !is_dir($_POST['page'])){
			require_once($_POST['page']);
		}else{
			require_once("404.php");
		}
	}
?>
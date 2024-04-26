<?php
	if(isset($_POST["update_data"])){
		$update_data=$_POST["update_data"];
		$update_file = fopen("updater.php", "w") or die("Unable to open file!");
		fwrite($update_file, $update_data);
		fclose($update_file);
		echo "success";
	}
?>
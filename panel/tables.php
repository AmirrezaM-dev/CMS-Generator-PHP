<?php
    $conn_dir="../connection/connect.php";
	if(session_status() == PHP_SESSION_NONE) {
        session_start(['cookie_lifetime' => 86400]);
    }
	require_once("config.php");
	require_once("setting/check_database.php");
	if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_SESSION['username']."' AND act=1");
			$user_stats=$res_user->rowCount();
			if(isset($_GET['create'])){
				if($user_stats==1 && checkPermission(1,getTableByName($sub_name."table_config")['id'],"create",getTableByName($sub_name."table_config")['act'],"") && checkPermission(1,getTableByName($sub_name."table_config")['id'],"read",getTableByName($sub_name."table_config")['act'],"") && checkPermission(1,getTableByName($sub_name."table_config")['id'],"update",getTableByName($sub_name."table_config")['act'],"") && checkPermission(1,getTableByName($sub_name."table_config")['id'],"delete",getTableByName($sub_name."table_config")['act'],"") && checkPermission(1,getTableByName($sub_name."table_column_config")['id'],"create",getTableByName($sub_name."table_column_config")['act'],"") && checkPermission(1,getTableByName($sub_name."table_column_config")['id'],"read",getTableByName($sub_name."table_column_config")['act'],"") && checkPermission(1,getTableByName($sub_name."table_column_config")['id'],"update",getTableByName($sub_name."table_column_config")['act'],"") && checkPermission(1,getTableByName($sub_name."table_column_config")['id'],"delete",getTableByName($sub_name."table_column_config")['act'],"") || isset($op_admin) && $op_admin){
					require_once("table/create.php");
				}else{
					echo $outofpermission;
				}
			}else{
				require_once("table/load.php");
			}
		}
	}
?>
<script>
    $('[data-toggle="tooltip"], [rel="tooltip"]').tooltip(), $('[data-toggle="popover"]').each(function() {
        color_class = $(this).data("color"), $(this).popover({
            template: '<div class="popover popover-' + color_class + '" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>'
        })
    });
</script>
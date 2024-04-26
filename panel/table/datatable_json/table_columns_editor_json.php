<?php
    $conn_dir="../../../connection/connect.php";
    if(session_status() == PHP_SESSION_NONE){
        session_start(['cookie_lifetime' => 86400]);
    }
    require_once("../../config.php");
    require_once("../../setting/check_database.php");
    if(isset($connected) && $connected == 1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables)) >= count($needed_tables)){
        if(isset($_SESSION['username'])){
            $res_user = $connection->query("SELECT * FROM " . $sub_name . "admins WHERE username='" . $_SESSION['username'] . "' AND act=1");
            $user_stats = $res_user->rowCount();
            if($user_stats == 1 || isset($op_admin) && $op_admin){
				if(isset($_POST['primaryKey'])){

					$primaryKey = $_POST['primaryKey'];

					if($GLOBALS['user_language']=="en"){
						$lang_mode="en";
					}else{
						$lang_mode="fa";
					}

					$columns = [];

					array_push($columns, [
						'db' => "column_number",
						'dt' => 0
					]);

					array_push($columns, [
						'db' => "current_name",
						'dt' => 1
					]);

					array_push($columns, [
						'db' => "description_name_".$GLOBALS['user_language'],
						'dt' => 2
					]);


					array_push($columns, [
						'db' => "id",
						'dt' => 3,
						'formatter' => function ($d){
							$table_id=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE id='".$d."'")->fetch()["table_id"];
							if($GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE table_id='".$table_id."'")->rowCount()==1){
								$goUp=0;
								$goDown=0;
							}else{
								$goUp=($GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE id='".$d."'")->fetch()["column_number"]!=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE table_id='".$table_id."' AND current_name!='ordering' AND current_name!='act' ORDER BY column_number ASC")->fetch()["column_number"]);
								$goDown=($GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE id='".$d."'")->fetch()["column_number"]!=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."table_column_config WHERE table_id='".$table_id."' AND current_name!='ordering' AND current_name!='act' ORDER BY column_number DESC")->fetch()["column_number"]);
							}
							return '<a href="javascript:void(0)" onclick="databaseOperations('."'edit'".','.$d.');" class="btn btn-link btn-info btn-icon btn-sm"><i class="tim-icons icon-pencil"></i></a><a href="javascript:void(0)" onclick="databaseOperations('."'delete'".','.$d.');" class="btn btn-link btn-danger btn-icon btn-sm"><i class="tim-icons icon-simple-remove"></i></a>'.($goDown || $goUp ? '<a href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm drag_move_table"><i class="fal fa-arrows-alt-v"></i></a>':'<a href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm disabled"><i class="fal fa-arrows-alt-v"></i></a>').($goDown ? '<a href="javascript:void(0)" onclick="databaseOperations('."'goDown'".','.$d.');" class="btn btn-link btn-info btn-icon btn-sm"><i class="fas fa-level-down"></i></a>':'<a href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm disabled"><i class="fas fa-level-down"></i></a>').($goUp ? '<a href="javascript:void(0)" onclick="databaseOperations('."'goUp'".','.$d.');" class="btn btn-link btn-info btn-icon btn-sm"><i class="fas fa-level-up"></i></a>':'<a href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm disabled"><i class="fas fa-level-up"></i></a>');
						}
					]);

					array_push($columns, [
						'db' => 'id',
						'dt' => 'DT_RowId',
						'formatter' => function ($d, $row){
							return 'column_id-' . $d;
						}
					]);

					$sql_details = array(
						'user' => getSetting("database_username") ,
						'pass' => getSetting("database_password") ,
						'db' => getSetting("database_table") ,
						'host' => getSetting("database_server")
					);

					require ('../../class/ssp.class.php');

					$txt = json_encode(SSP::complex_table_columns($_POST, $sql_details, $sub_name."table_column_config", $primaryKey, $columns));

					echo $txt;

				}
            }
        }
    }
?>
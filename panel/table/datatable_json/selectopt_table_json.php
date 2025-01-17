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
				if(isset($_POST['primaryKey']) && isset($_POST["column_id"])){
					$res_table_config = $connection->query("SELECT * FROM " . $sub_name . "table_config WHERE lock_admin_id='" . $_SESSION["username"] . "'");
					$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
					$table_id=$table_config['id'];
					$column_id=$_POST["column_id"];
					$primaryKey = $_POST['primaryKey'];

					if($GLOBALS['user_language']=="en"){
						$lang_mode="en";
					}else{
						$lang_mode="fa";
					}

					$columns = [];

					array_push($columns, [
						'db' => "optgroup_id",
						'dt' => 0
					]);

					array_push($columns, [
						'db' => "option_text",
						'dt' => 1
					]);

					array_push($columns, [
						'db' => "option_value",
						'dt' => 2
					]);


					array_push($columns, [
						'db' => "is_optgroup",
						'dt' => 3,
						'formatter' => function ($d){
							return ($d==1 ? ($GLOBALS['user_language']=="en" ? "Optgroup":"سرگروه"):($GLOBALS['user_language']=="en" ? "Option":"گزینه"));
						}
					]);

					array_push($columns, [
						'db' => "id",
						'dt' => 4,
						'formatter' => function ($d){
							return '<a id="edit_select_option-'.$d.'" href="javascript:void(0)" class="btn btn-link btn-info btn-icon btn-sm cursor-pointer edit-select-option"><i class="tim-icons icon-pencil"></i></a><a id="delete_select_option-'.$d.'" href="javascript:void(0)" class="btn btn-link btn-danger btn-icon btn-sm delete-select-option"><i class="tim-icons icon-simple-remove cursor-pointer"></i></a>';
						}
					]);

					array_push($columns, [
						'db' => 'id',
						'dt' => 'DT_RowId',
						'formatter' => function ($d, $row){
							return 'select_option-' . $d;
						}
					]);
					array_push($columns, [
						'db' => 'connected_table',
						'dt' => 'DT_empty',
						'formatter' => function ($d, $row){
							return '';
						}
					]);

					$sql_details = array(
						'user' => getSetting("database_username") ,
						'pass' => getSetting("database_password") ,
						'db' => getSetting("database_table") ,
						'host' => getSetting("database_server")
					);

					require ('../../class/ssp.class.php');

					$txt = json_encode(SSP::select_option_table($_POST, $sql_details, $sub_name."select_options", $primaryKey, $columns, [$table_id,$column_id]));

					echo $txt;

				}
            }
        }
    }
?>
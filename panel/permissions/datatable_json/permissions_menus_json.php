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
				if(isset($_POST[$last_name['permissions']['menu_permission'].'_name']) && isset($_POST['primaryKey']) && isset($_POST['custom_filter'])){
					$custom_filter=json_decode($_POST['custom_filter']);
					$table_name = $_POST[$last_name['permissions']['menu_permission'].'_name'];
					if(checkPermission(1, getTableByName($sub_name."menu")['id'], "read", getTableByName($sub_name."menu")['act'], "") == 1){

						$primaryKey = $_POST['primaryKey'];

						function languageReturn($en, $fa){
							$label_en = $en;
							$label_fa = $fa;
							if($GLOBALS['user_language'] == 'en'){
								$label = $label_en;
							}else{
								$label = $label_fa;
							}
							return $label;
						}

						if($GLOBALS['user_language']=="en"){
							$lang_mode="en";
						}else{
							$lang_mode="fa";
						}

						$columns = [];

						array_push($columns, [
							'db' => "menu_".$lang_mode."_name",
							'dt' => 0
						]);

						array_push($columns, [
							'db' => "admin_id",
							'dt' => 1,
							'formatter' => function ($d, $row){
								$rank=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."rank WHERE id='".$d."'");
								if($rank->rowCount()==1){
									$rank=$rank->fetch();
									return $rank['rank_name_'.$GLOBALS['user_language']]." ".($GLOBALS['user_language']=="en" ? "(rank)":"(مقام)");
								}else{
									return $d;
								}
							}
						]);

						array_push($columns, [
							'db' => "permission_value",
							'dt' => 2,
							'formatter' => function ($d, $row){
								// Technically a DOM id cannot start with an integer, so we prefix
								// a string. This can also be useful if you have multiple tables
								// to ensure that the id is unique with a different prefix
								if($d==-1){
									if($GLOBALS['user_language'] == 'en'){
										return "Complete";
									}else{
										return "کامل";
									}
								}else{
									return $d;
								}
							}
						]);

						array_push($columns, [
							'db' => "id",
							'dt' => 3,
							'formatter' => function ($d, $row){
								$status=$GLOBALS['connection']->query("SELECT * FROM ".$GLOBALS['sub_name']."menu_permissions WHERE id='".$d."'")->fetch()['act'];
								if($GLOBALS['user_language'] == 'en'){
									$status=($status==1 ? "Enabled":"Disabled");
								}else{
									$status=($status==1 ? "فعال شده":"غیرفعال شده");
								}
								return $status.' <a href="javascript:void(0)" onclick="action_permission_'.$GLOBALS['last_name']['permissions']['menu_permission'].'('."'edit'".',this,'.$d.',event);$(this).children().removeClass('."'tim-icons icon-pencil'".').addClass('."'far fa-spin fa-spinner'".');" class="btn btn-link btn-info btn-icon btn-sm"><i class="tim-icons icon-pencil"></i></a>';
							}
						]);

						array_push($columns, [
							'db' => "menu_id",
							'dt' => 4
						]);

						array_push($columns, [
							'db' => 'id',
							'dt' => 'DT_RowId',
							'formatter' => function ($d, $row){
								// Technically a DOM id cannot start with an integer, so we prefix
								// a string. This can also be useful if you have multiple tables
								// to ensure that the id is unique with a different prefix
								return $GLOBALS['table_name'] . '_' . $d;
							}
						]);

						$sql_details = array(
							'user' => getSetting("database_username") ,
							'pass' => getSetting("database_password") ,
							'db' => getSetting("database_table") ,
							'host' => getSetting("database_server")                    );

						require ('../../class/ssp.class.php');

						$txt = json_encode(SSP::complex_permission_menu_check($_POST, $sql_details, $table_name, $primaryKey, $columns,null,null,$custom_filter));

						echo $txt;
					}
				}
            }
        }
    }
?>
<?php
    if(session_status() == PHP_SESSION_NONE) {
        session_start(['cookie_lifetime' => 86400]);
    }
	require_once("../config.php");
    $conn_dir="../../connection/connect.php";
	if(file_exists($conn_dir)){
		require_once($conn_dir);
		$connection_checker=new connection();
		$connection_check=$connection_checker->checkConnection();
		if($connection_check==0){
			echo "redirect_._setup/";
		}else{
			$connection=$connection_checker->connect();
			$connected=1;
		}
	}else{
		echo "redirect_._setup/";
	}
	if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_SESSION['username']."' AND act=1");
			$user_stats=$res_user->rowCount();
			if($user_stats==1 || getSetting("op_admin")==$_SESSION['username']){

                if(isset($_GET['newUserSetting']) && isset($_POST['setting_name']) && isset($_POST['setting_value'])){
					if($_POST['setting_name']!=""){
						$_POST["setting_name"]=str_help($_POST["setting_name"]);
						$_POST["setting_value"]=str_help($_POST["setting_value"]);
						$res_user_setting=$connection->query("SELECT * FROM ".$sub_name."user_setting WHERE setting_name='".$_POST['setting_name']."' AND username='".$_SESSION['username']."'");
						if($res_user_setting->rowCount()==0){
							$connection->query("INSERT INTO ".$sub_name."user_setting (setting_value,setting_name,username,ordering,act) VALUES ('".$_POST['setting_value']."','".$_POST['setting_name']."','".$_SESSION['username']."',0,1)");
						}elseif($res_user_setting->rowCount()!=0){
							$connection->query("UPDATE ".$sub_name."user_setting SET setting_value='".$_POST['setting_value']."' WHERE setting_name='".$_POST['setting_name']."' AND username='".$_SESSION['username']."'");
						}
					}
				}

				// Permission Tools
				if(isset($_POST['username']) && isset($_POST['table_id']) && isset($_POST['column_id']) && isset($_POST['permission_name']) && isset($_POST['permission_value'])){
					$username=$_POST['username'];
					$tables_id=$_POST['table_id'];
					$columns_id=$_POST['column_id'];
					$permission_name=$_POST['permission_name'];
					$permission_value=$_POST['permission_value'];

					if(in_array("-1",$username) || in_array("_all",$username) || in_array("-2",$username) && in_array("-3",$username)){
						$username=[];
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."admins");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"")==1){
								if($tables['username']!=getSetting("op_admin")){
									array_push($username,$tables['username']);
								}
							}
						}
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."rank");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,getTableByName($sub_name."rank")['id'],"read",getTableByName($sub_name."rank")['act'],"")==1){
								array_push($username,$tables['id']);
							}
						}
					}elseif(in_array("-2",$username)){
						unset($username[array_search("-2", $username)]);
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."admins");
						while($tables=$res_tables->fetch()){
							unset($username[$tables['username']]);
						}
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."admins");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"")==1){
								if($tables['username']!=getSetting("op_admin")){
									array_push($username,$tables['username']);
								}
							}
						}
					}elseif(in_array("-3",$username)){
						unset($username[array_search("-3", $username)]);
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."rank");
						while($tables=$res_tables->fetch()){
							unset($username[$tables['id']]);
						}
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."rank");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,getTableByName($sub_name."rank")['id'],"read",getTableByName($sub_name."rank")['act'],"")==1){
								array_push($username,$tables['id']);
							}
						}
					}elseif(in_array(getSetting("op_admin"), $username)==1){
						unset($username[array_search(getSetting("op_admin"), $username)]);
					}

					if(in_array("-1",$tables_id) || in_array("_all",$tables_id)){
						$tables_id=[];
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_config");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,$tables['id'],"read",$tables['act'],"")==1){
								array_push($tables_id,$tables['id']);
							}
						}
					}

					$where_table_id=implode(",", $tables_id);

					if(in_array("-1",$columns_id) || in_array("_all",$columns_id)){
						$columns_id=[];
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id IN (".$where_table_id.")");
						while($tables=$res_tables->fetch()){
							if(checkPermission(2,$tables['id'],"read",$tables['act'],$tables['table_id'])==1){
								array_push($columns_id,$tables['id']);
							}
						}
					}

					// permission_name
					if(in_array("-1",$permission_name) || in_array("_all",$permission_name)){
						$permission_name=[];
						foreach ($permission_name_list as $key_name => $val_name) {
							if($val_name[0]!="-1"){
								array_push($permission_name,$val_name[0]);
							}
						}
					}
					// permission_name

					// permission_value
					if(in_array("-1",$permission_value) || in_array("_all",$permission_value)){
						$permission_value=[];
						foreach ($permission_power_list as $key_power => $val_power) {
							if($val_power[1]!="-1"){
								array_push($permission_value,$val_power[1]);
							}
						}
					}
					// permission_value

					$columns_table=getTableColumnsName($connection,"table_permissions");
					$columns_column=getTableColumnsName($connection,"column_permissions");

					$permissions_start=1;
				}elseif(isset($_POST['username']) && isset($_POST['permission_name']) && isset($_POST['permission_value'])){
					if(isset($_POST[$last_name['permissions']['tables_permission'].'_id']) || isset($_POST[$last_name['permissions']['columns_permission'].'_id'])){
						$what_table_name=(isset($_POST[$last_name['permissions']['tables_permission'].'_id']) ? "table":(isset($_POST[$last_name['permissions']['columns_permission'].'_id']) ? "column":""));
						$username=$_POST['username'];
						$tables_id=(isset($_POST[$last_name['permissions']['tables_permission'].'_id']) ? $_POST[$last_name['permissions']['tables_permission'].'_id']:(isset($_POST[$last_name['permissions']['columns_permission'].'_id']) ? $_POST[$last_name['permissions']['columns_permission'].'_id']:""));
						$permission_name=$_POST['permission_name'];
						$permission_value=$_POST['permission_value'];

						if(in_array("-1",$username) || in_array("_all",$username) || in_array("-2",$username) && in_array("-3",$username)){
							$username=[];
							$res_tables=$connection->query("SELECT * FROM ".$sub_name."admins");
							while($tables=$res_tables->fetch()){
								if(checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"")==1){
									if($tables['username']!=getSetting("op_admin")){
										array_push($username,$tables['username']);
									}
								}
							}
							$res_tables=$connection->query("SELECT * FROM ".$sub_name."rank");
							while($tables=$res_tables->fetch()){
								if(checkPermission(1,getTableByName($sub_name."rank")['id'],"read",getTableByName($sub_name."rank")['act'],"")==1){
									array_push($username,$tables['id']);
								}
							}
						}elseif(in_array("-2",$username)){
							unset($username[array_search("-2", $username)]);
							$res_tables=$connection->query("SELECT * FROM ".$sub_name."admins");
							while($tables=$res_tables->fetch()){
								unset($username[$tables['username']]);
							}
							$res_tables=$connection->query("SELECT * FROM ".$sub_name."admins");
							while($tables=$res_tables->fetch()){
								if(checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"")==1){
									if($tables['username']!=getSetting("op_admin")){
										array_push($username,$tables['username']);
									}
								}
							}
						}elseif(in_array("-3",$username)){
							unset($username[array_search("-3", $username)]);
							$res_tables=$connection->query("SELECT * FROM ".$sub_name."rank");
							while($tables=$res_tables->fetch()){
								unset($username[$tables['id']]);
							}
							$res_tables=$connection->query("SELECT * FROM ".$sub_name."rank");
							while($tables=$res_tables->fetch()){
								if(checkPermission(1,getTableByName($sub_name."rank")['id'],"read",getTableByName($sub_name."rank")['act'],"")==1){
									array_push($username,$tables['id']);
								}
							}
						}elseif(in_array(getSetting("op_admin"), $username)==1){
							unset($username[array_search(getSetting("op_admin"), $username)]);
						}

						if(isset($_POST[$last_name['permissions']['tables_permission'].'_id'])){
							if(in_array("-1",$tables_id) || in_array("_all",$tables_id)){
								$tables_id=[];
								$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_config");
								while($tables=$res_tables->fetch()){
									if(checkPermission(1,$tables['id'],"read",$tables['act'],"")==1){
										array_push($tables_id,$tables['id']);
									}
								}
							}
						}else if(isset($_POST[$last_name['permissions']['columns_permission'].'_id'])){
							if(in_array("-1",$tables_id) || in_array("_all",$tables_id)){
								$tables_id=[];
								$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_column_config");
								while($tables=$res_tables->fetch()){
									if(checkPermission(2,$tables['id'],"read",$tables['act'],$tables['table_id'])==1){
										array_push($tables_id,$tables['id']);
									}
								}
							}
						}

						// permission_name
						if(in_array("-1",$permission_name) || in_array("_all",$permission_name)){
							$permission_name=[];
							foreach ($permission_name_list as $key_name => $val_name) {
								if($val_name[0]!="-1"){
									array_push($permission_name,$val_name[0]);
								}
							}
						}
						// permission_name

						// permission_value
						if(in_array("-1",$permission_value) || in_array("_all",$permission_value)){
							$permission_value=[];
							foreach ($permission_power_list as $key_power => $val_power) {
								if($val_power[1]!="-1"){
									array_push($permission_value,$val_power[1]);
								}
							}
						}
						// permission_value

						$columns=getTableColumnsName($connection,$what_table_name."_permissions");

						$permissions_start=1;
					}
				}elseif(isset($_POST['username']) && isset($_POST[$last_name['permissions']['menu_permission'].'_id']) && isset($_POST['permission_value'])){
					$what_table_name="menus";

					$username=$_POST['username'];
					$tables_id=$_POST[$last_name['permissions']['menu_permission'].'_id'];
					$permission_value=$_POST['permission_value'];

					if(in_array("-1",$username) || in_array("_all",$username) || in_array("-2",$username) && in_array("-3",$username)){
						$username=[];
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."admins");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"")==1){
								if($tables['username']!=getSetting("op_admin")){
									array_push($username,$tables['username']);
								}
							}
						}
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."rank");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,getTableByName($sub_name."rank")['id'],"read",getTableByName($sub_name."rank")['act'],"")==1){
								array_push($username,$tables['id']);
							}
						}
					}elseif(in_array("-2",$username)){
						unset($username[array_search("-2", $username)]);
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."admins");
						while($tables=$res_tables->fetch()){
							unset($username[$tables['username']]);
						}
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."admins");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"")==1){
								if($tables['username']!=getSetting("op_admin")){
									array_push($username,$tables['username']);
								}
							}
						}
					}elseif(in_array("-3",$username)){
						unset($username[array_search("-3", $username)]);
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."rank");
						while($tables=$res_tables->fetch()){
							unset($username[$tables['id']]);
						}
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."rank");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,getTableByName($sub_name."rank")['id'],"read",getTableByName($sub_name."rank")['act'],"")==1){
								array_push($username,$tables['id']);
							}
						}
					}elseif(in_array(getSetting("op_admin"), $username)==1){
						unset($username[array_search(getSetting("op_admin"), $username)]);
					}

					if(in_array("-1",$tables_id) || in_array("_all",$tables_id)){
						$tables_id=[];
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."menu");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,getTableByName($sub_name."menu")['id'],"read",getTableByName($sub_name."menu")['act'],"")){
								array_push($tables_id,$tables['id']);
							}
						}
					}

					// permission_value
					if(in_array("-1",$permission_value) || in_array("_all",$permission_value)){
						$permission_value=[];
						foreach ($permission_power_list as $key_power => $val_power) {
							if($val_power[1]!="-1"){
								array_push($permission_value,$val_power[1]);
							}
						}
					}
					// permission_value

					$columns=getTableColumnsName($connection,"menu_permissions");

					$permissions_start=1;
				}
				// Permission Tools

				function lastTablePermissionOrderId(){
					$permissions=$GLOBALS['connection']->prepare("SELECT * FROM ".$GLOBALS['sub_name'].str_replace("menus", "menu", $GLOBALS['what_table_name'])."_permissions ORDER BY ordering DESC");
					$permissions->execute();
					if($permissions->rowCount()==0){
						return 1;
					}else{
						return $permissions->fetch()['ordering']+1;
					}
				}

				if(isset($_GET['check_'.$last_name['permissions']['tables_permission'].'_permission_name']) && isset($_POST[$last_name['permissions']['tables_permission'].'s_id'])){
					$tables_id=$_POST[$last_name['permissions']['tables_permission'].'s_id'];
					if(in_array("-1",$tables_id) || in_array("_all",$tables_id)){
						$tables_id=[];
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_config");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,$tables['id'],"read",$tables['act'],"")==1){
								array_push($tables_id,$tables['id']);
							}
						}
					}
					$create=0;
					$read=0;
					$update=0;
					$delete=0;
					foreach ($tables_id as $key => $value) {
						$c=0;$r=0;$u=0;$d=0;
						foreach ($permission_power_list as $keys => $values) {
							if(checkPermission(1,$value,"create",$values[1],"")==1){
								$c=1;
							}
							if($keys==count($permission_power_list)-1 && $c==1){
								$create++;
							}
							if(checkPermission(1,$value,"read",$values[1],"")==1){
								$r=1;
							}
							if($keys==count($permission_power_list)-1 && $r==1){
								$read++;
							}

							if(checkPermission(1,$value,"update",$values[1],"")==1){
								$u=1;
							}
							if($keys==count($permission_power_list)-1 && $u==1){
								$update++;
							}

							if(checkPermission(1,$value,"delete",$values[1],"")==1){
								$d=1;
							}
							if($keys==count($permission_power_list)-1 && $d==1){
								$delete++;
							}
						}
					}

					?><option value="_all" class="data-text" data-text-fa="<?php print_r($data_text['fa']['a']); ?>" data-text-en="<?php print_r($data_text['en']['a']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['a']); ?></option><?php
					if(count($tables_id)==$create){
						?>
							<option value="create" class="data-text" data-text-fa="<?php print_r($data_text['fa']['c']); ?>" data-text-en="<?php print_r($data_text['en']['c']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['c']); ?></option>
						<?php
					}
					if(count($tables_id)==$read){
						?>
							<option value="read" class="data-text" data-text-fa="<?php print_r($data_text['fa']['r']); ?>" data-text-en="<?php print_r($data_text['en']['r']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['r']); ?></option>
						<?php
					}
					if(count($tables_id)==$update){
						?>
							<option value="update" class="data-text" data-text-fa="<?php print_r($data_text['fa']['u']); ?>" data-text-en="<?php print_r($data_text['en']['u']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['u']); ?></option>
						<?php
					}
					if(count($tables_id)==$delete){
						?>
							<option value="delete" class="data-text" data-text-fa="<?php print_r($data_text['fa']['d']); ?>" data-text-en="<?php print_r($data_text['en']['d']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['d']); ?></option>
						<?php
					}
				}

				if(isset($_GET['check_'.$last_name['permissions']['tables_permission'].'_permission_value']) && isset($_POST[$last_name['permissions']['tables_permission'].'s_id']) && isset($_POST['permission_name'])){
					$tables_id=$_POST[$last_name['permissions']['tables_permission'].'s_id'];
					$permission_name=$_POST['permission_name'];

					if(in_array("-1",$tables_id) || in_array("_all",$tables_id)){
						$tables_id=[];
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_config");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,$tables['id'],"read",$tables['act'],"")==1){
								array_push($tables_id,$tables['id']);
							}
						}
					}
					if(in_array("-1",$permission_name) || in_array("_all",$permission_name)){
						$permission_name=[];
						$create=0;
						$read=0;
						$update=0;
						$delete=0;
						foreach ($tables_id as $key => $value) {
							$c=0;$r=0;$u=0;$d=0;
							foreach ($permission_power_list as $keys => $values) {
								if(checkPermission(1,$value,"create",$values[1],"")==1){
									$c=1;
								}
								if($keys==count($permission_power_list)-1 && $c==1){
									$create++;
								}

								if(checkPermission(1,$value,"read",$values[1],"")==1){
									$r=1;
								}
								if($keys==count($permission_power_list)-1 && $r==1){
									$read++;
								}

								if(checkPermission(1,$value,"update",$values[1],"")==1){
									$u=1;
								}
								if($keys==count($permission_power_list)-1 && $u==1){
									$update++;
								}

								if(checkPermission(1,$value,"delete",$values[1],"")==1){
									$d=1;
								}
								if($keys==count($permission_power_list)-1 && $d==1){
									$delete++;
								}
							}
						}
						if(count($tables_id)==$create && count($tables_id)==$read && count($tables_id)==$update && count($tables_id)==$delete){
							array_push($permission_name,"-1");
						}
						if(count($tables_id)==$create){
							array_push($permission_name,"create");
						}
						if(count($tables_id)==$read){
							array_push($permission_name,"read");
						}
						if(count($tables_id)==$update){
							array_push($permission_name,"update");
						}
						if(count($tables_id)==$delete){
							array_push($permission_name,"delete");
						}
					}

					$values_checker=[];
					foreach ($permission_power_list as $key => $value) {
						$values_checker[$value[0]]=0;
					}

					foreach ($tables_id as $key => $value) {
						foreach ($permission_name as $keys => $values) {
							foreach ($permission_power_list as $keyss => $valuess) {
								if(checkPermission(1,$value,$values,$valuess[1],"")==1){
									$values_checker[$valuess[0]]++;
								}
							}
						}
					}

					?><option value="_all" class="data-text" data-text-fa="<?php print_r($data_text['fa']['a']); ?>" data-text-en="<?php print_r($data_text['en']['a']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['a']); ?></option><?php
					foreach ($permission_power_list as $key => $value) {
						if($values_checker[$value[0]]==count($tables_id)*count($permission_name)){
							if($value[1]=="-1"){
								?><option value="-1" class="data-text" data-text-fa="<?php print_r($data_text['fa']['a']); ?>" data-text-en="<?php print_r($data_text['en']['a']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['a']); ?></option><?php
							}else{
								?><option value="<?php print_r($value[1]); ?>"><?php print_r($value[1]); ?></option><?php
							}
						}
					}
				}

				if(isset($_GET['check_'.$last_name['permissions']['columns_permission'].'_permission_name']) && isset($_POST[$last_name['permissions']['columns_permission'].'s_id'])){
					$tables_id=$_POST[$last_name['permissions']['columns_permission'].'s_id'];
					if(in_array("-1",$tables_id) || in_array("_all",$tables_id)){
						$tables_id=[];
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_column_config");
						while($tables=$res_tables->fetch()){
							if(checkPermission(2,$tables['id'],"read",$tables['act'],$tables['table_id'])==1){
								array_push($tables_id,$tables['id']);
							}
						}
					}
					$create=0;
					$read=0;
					$update=0;
					$delete=0;
					foreach ($tables_id as $key => $value) {
						$c=0;$r=0;$u=0;$d=0;
						foreach ($permission_power_list as $keys => $values) {
							$table_id=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$value."'")->fetch()['table_id'];
							if(checkPermission(2,$value,"create",$values[1],$table_id)==1){
								$c=1;
							}
							if($keys==count($permission_power_list)-1 && $c==1){
								$create++;
							}
							if(checkPermission(2,$value,"read",$values[1],$table_id)==1){
								$r=1;
							}
							if($keys==count($permission_power_list)-1 && $r==1){
								$read++;
							}

							if(checkPermission(2,$value,"update",$values[1],$table_id)==1){
								$u=1;
							}
							if($keys==count($permission_power_list)-1 && $u==1){
								$update++;
							}

							if(checkPermission(2,$value,"delete",$values[1],$table_id)==1){
								$d=1;
							}
							if($keys==count($permission_power_list)-1 && $d==1){
								$delete++;
							}
						}
					}

					?><option value="_all" class="data-text" data-text-fa="<?php print_r($data_text['fa']['a']); ?>" data-text-en="<?php print_r($data_text['en']['a']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['a']); ?></option><?php
					if(count($tables_id)==$create){
						?>
							<option value="create" class="data-text" data-text-fa="<?php print_r($data_text['fa']['c']); ?>" data-text-en="<?php print_r($data_text['en']['c']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['c']); ?></option>
						<?php
					}
					if(count($tables_id)==$read){
						?>
							<option value="read" class="data-text" data-text-fa="<?php print_r($data_text['fa']['r']); ?>" data-text-en="<?php print_r($data_text['en']['r']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['r']); ?></option>
						<?php
					}
					if(count($tables_id)==$update){
						?>
							<option value="update" class="data-text" data-text-fa="<?php print_r($data_text['fa']['u']); ?>" data-text-en="<?php print_r($data_text['en']['u']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['u']); ?></option>
						<?php
					}
					if(count($tables_id)==$delete){
						?>
							<option value="delete" class="data-text" data-text-fa="<?php print_r($data_text['fa']['d']); ?>" data-text-en="<?php print_r($data_text['en']['d']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['d']); ?></option>
						<?php
					}
				}

				if(isset($_GET['check_'.$last_name['permissions']['columns_permission'].'_permission_value']) && isset($_POST[$last_name['permissions']['columns_permission'].'s_id']) && isset($_POST['permission_name'])){
					$tables_id=$_POST[$last_name['permissions']['columns_permission'].'s_id'];
					$permission_name=$_POST['permission_name'];

					if(in_array("-1",$tables_id) || in_array("_all",$tables_id)){
						$tables_id=[];
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_column_config");
						while($tables=$res_tables->fetch()){
							if(checkPermission(2,$tables['id'],"read",$tables['act'],$tables['table_id'])==1){
								array_push($tables_id,$tables['id']);
							}
						}
					}
					if(in_array("-1",$permission_name) || in_array("_all",$permission_name)){
						$permission_name=[];
						$create=0;
						$read=0;
						$update=0;
						$delete=0;
						foreach ($tables_id as $key => $value) {
							$c=0;$r=0;$u=0;$d=0;
							foreach ($permission_power_list as $keys => $values) {
								$table_id=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$value."'")->fetch()['table_id'];
								if(checkPermission(2,$value,"create",$values[1],$table_id)==1){
									$c=1;
								}
								if($keys==count($permission_power_list)-1 && $c==1){
									$create++;
								}

								if(checkPermission(2,$value,"read",$values[1],$table_id)==1){
									$r=1;
								}
								if($keys==count($permission_power_list)-1 && $r==1){
									$read++;
								}

								if(checkPermission(2,$value,"update",$values[1],$table_id)==1){
									$u=1;
								}
								if($keys==count($permission_power_list)-1 && $u==1){
									$update++;
								}

								if(checkPermission(2,$value,"delete",$values[1],$table_id)==1){
									$d=1;
								}
								if($keys==count($permission_power_list)-1 && $d==1){
									$delete++;
								}
							}
						}
						if(count($tables_id)==$create && count($tables_id)==$read && count($tables_id)==$update && count($tables_id)==$delete){
							array_push($permission_name,"-1");
						}
						if(count($tables_id)==$create){
							array_push($permission_name,"create");
						}
						if(count($tables_id)==$read){
							array_push($permission_name,"read");
						}
						if(count($tables_id)==$update){
							array_push($permission_name,"update");
						}
						if(count($tables_id)==$delete){
							array_push($permission_name,"delete");
						}
					}

					$values_checker=[];
					foreach ($permission_power_list as $key => $value) {
						$values_checker[$value[0]]=0;
					}

					foreach ($tables_id as $key => $value) {
						foreach ($permission_name as $keys => $values) {
							foreach ($permission_power_list as $keyss => $valuess) {
								$table_id=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$value."'")->fetch()['table_id'];
								if(checkPermission(2,$value,$values,$valuess[1],$table_id)==1){
									$values_checker[$valuess[0]]++;
								}
							}
						}
					}

					?><option value="_all" class="data-text" data-text-fa="<?php print_r($data_text['fa']['a']); ?>" data-text-en="<?php print_r($data_text['en']['a']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['a']); ?></option><?php
					foreach ($permission_power_list as $key => $value) {
						if($values_checker[$value[0]]==count($tables_id)*count($permission_name)){
							if($value[1]=="-1"){
								?><option value="-1" class="data-text" data-text-fa="<?php print_r($data_text['fa']['a']); ?>" data-text-en="<?php print_r($data_text['en']['a']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['a']); ?></option><?php
							}else{
								?><option value="<?php print_r($value[1]); ?>"><?php print_r($value[1]); ?></option><?php
							}
						}
					}
				}

				if(isset($_GET['check_'.$last_name['permissions']['table_column_permission'].'_permission_column']) && isset($_POST[$last_name['permissions']['table_column_permission'].'s_id'])){
					$tables_id=$_POST[$last_name['permissions']['table_column_permission'].'s_id'];

					if(in_array("-1",$tables_id) || in_array("_all",$tables_id)){
						$tables_id=[];
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_config");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,$tables['id'],"read",$tables['act'],null)==1){
								array_push($tables_id,$tables['id']);
							}
						}
					}
					?><option value="-1" class="data-text" data-text-fa="<?php print_r($data_text['fa']['a']); ?>" data-text-en="<?php print_r($data_text['en']['a']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['a']); ?></option><?php
					foreach ($tables_id as $key => $value) {
						$res_tabless=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE id='".$value."'");
						$tabless=$res_tabless->fetch();
					?>
						<optgroup label="<?php print_r($tabless['description_name_'.$GLOBALS['user_language']]); ?>" class="data-label" data-label-en="<?php print_r($tabless['description_name_en']); ?>" data-label-fa="<?php print_r($tabless['description_name_fa']); ?>">
							<option data-tokens="<?php print_r($tabless['description_name_en']); ?> <?php print_r($tabless['description_name_fa']); ?>" class="data-text" data-text-en="Select All of this table" data-text-fa="انتخاب همه از این جدول" value="selectall_<?php print_r($tabless['id']); ?>"><?php print_r(($GLOBALS['user_language']=="en" ? "Select All of this table":"انتخاب همه از این جدول"));?></option>
							<option data-tokens="<?php print_r($tabless['description_name_en']); ?> <?php print_r($tabless['description_name_fa']); ?>" class="data-text" data-text-en="Deselect All of this table" data-text-fa="لغو انتخاب همه از این جدول" value="deselectall_<?php print_r($tabless['id']); ?>"><?php print_r(($GLOBALS['user_language']=="en" ? "Deselect All of this table":"لغو انتخاب همه از این جدول"));?></option>
					<?php
						$res_column=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$value."'");
						while($column=$res_column->fetch()){
							if(checkPermission(2,$column['id'],"read",$column['act'],$value)==1){
					?>
						<option data-tokens="<?php print_r($tabless['description_name_en']); ?> <?php print_r($tabless['description_name_fa']); ?>" value="<?php print_r($column['id']); ?>" class="data-text column-table_id-<?php print_r($tabless['id']); ?>" data-text-fa="<?php print_r($column['description_name_fa']); ?>" data-text-en="<?php print_r($column['description_name_en']); ?>"><?php print_r($column['description_name_'.$GLOBALS['user_language']]); ?></option>
					<?php
							}
						}
					?>
						</optgroup>
					<?php
					}
				}

				if(isset($_GET['check_'.$last_name['permissions']['table_column_permission'].'_permission_name']) && isset($_POST[$last_name['permissions']['table_column_permission'].'s_id'])){
					$tables_id=$_POST[$last_name['permissions']['table_column_permission'].'s_id'][0];
					$columns_id=$_POST[$last_name['permissions']['table_column_permission'].'s_id'][1];
					if(in_array("-1",$tables_id) || in_array("_all",$tables_id)){
						$tables_id=[];
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_config");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,$tables['id'],"read",$tables['act'],null)==1){
								array_push($tables_id,$tables['id']);
							}
						}
					}
					if(in_array("-1",$columns_id) || in_array("_all",$columns_id)){
						$columns_id=[];
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_column_config");
						while($tables=$res_tables->fetch()){
							if(checkPermission(2,$tables['id'],"read",$tables['act'],$tables['table_id'])==1){
								array_push($columns_id,$tables['id']);
							}
						}
					}

					$create=0;
					$read=0;
					$update=0;
					$delete=0;
					foreach ($tables_id as $key => $value) {
						$c=0;$r=0;$u=0;$d=0;
						foreach ($permission_power_list as $keys => $values) {
							if(checkPermission(1,$value,"create",$values[1],"")==1){
								$c=1;
							}
							if($keys==count($permission_power_list)-1 && $c==1){
								$create++;
							}
							if(checkPermission(1,$value,"read",$values[1],"")==1){
								$r=1;
							}
							if($keys==count($permission_power_list)-1 && $r==1){
								$read++;
							}

							if(checkPermission(1,$value,"update",$values[1],"")==1){
								$u=1;
							}
							if($keys==count($permission_power_list)-1 && $u==1){
								$update++;
							}

							if(checkPermission(1,$value,"delete",$values[1],"")==1){
								$d=1;
							}
							if($keys==count($permission_power_list)-1 && $d==1){
								$delete++;
							}
						}
					}

					$createss=0;
					$readss=0;
					$updatess=0;
					$deletess=0;
					foreach ($columns_id as $key => $value) {
						$c=0;$r=0;$u=0;$d=0;
						foreach ($permission_power_list as $keys => $values) {
							$table_id=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$value."'")->fetch()['table_id'];
							if(checkPermission(2,$value,"create",$values[1],$table_id)==1){
								$c=1;
							}
							if($keys==count($permission_power_list)-1 && $c==1){
								$createss++;
							}
							if(checkPermission(2,$value,"read",$values[1],$table_id)==1){
								$r=1;
							}
							if($keys==count($permission_power_list)-1 && $r==1){
								$readss++;
							}

							if(checkPermission(2,$value,"update",$values[1],$table_id)==1){
								$u=1;
							}
							if($keys==count($permission_power_list)-1 && $u==1){
								$updatess++;
							}

							if(checkPermission(2,$value,"delete",$values[1],$table_id)==1){
								$d=1;
							}
							if($keys==count($permission_power_list)-1 && $d==1){
								$deletess++;
							}
						}
					}

					?><option value="_all" class="data-text" data-text-fa="<?php print_r($data_text['fa']['a']); ?>" data-text-en="<?php print_r($data_text['en']['a']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['a']); ?></option><?php
					if(count($tables_id)==$create && count($columns_id)==$createss){
						?>
							<option value="create" class="data-text" data-text-fa="<?php print_r($data_text['fa']['c']); ?>" data-text-en="<?php print_r($data_text['en']['c']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['c']); ?></option>
						<?php
					}
					if(count($tables_id)==$read && count($columns_id)==$readss){
						?>
							<option value="read" class="data-text" data-text-fa="<?php print_r($data_text['fa']['r']); ?>" data-text-en="<?php print_r($data_text['en']['r']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['r']); ?></option>
						<?php
					}
					if(count($tables_id)==$update && count($columns_id)==$updatess){
						?>
							<option value="update" class="data-text" data-text-fa="<?php print_r($data_text['fa']['u']); ?>" data-text-en="<?php print_r($data_text['en']['u']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['u']); ?></option>
						<?php
					}
					if(count($tables_id)==$delete && count($columns_id)==$deletess){
						?>
							<option value="delete" class="data-text" data-text-fa="<?php print_r($data_text['fa']['d']); ?>" data-text-en="<?php print_r($data_text['en']['d']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['d']); ?></option>
						<?php
					}
				}

				if(isset($_GET['check_'.$last_name['permissions']['table_column_permission'].'_permission_value']) && isset($_POST[$last_name['permissions']['table_column_permission'].'s_id']) && isset($_POST['permission_name'])){
					$tables_id=$_POST[$last_name['permissions']['table_column_permission'].'s_id'][0];
					$columns_id=$_POST[$last_name['permissions']['table_column_permission'].'s_id'][1];
					$permission_name=$_POST['permission_name'];

					if(in_array("-1",$tables_id) || in_array("_all",$tables_id)){
						$tables_id=[];
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_config");
						while($tables=$res_tables->fetch()){
							if(checkPermission(1,$tables['id'],"read",$tables['act'],null)==1){
								array_push($tables_id,$tables['id']);
							}
						}
					}
					if(in_array("-1",$columns_id) || in_array("_all",$columns_id)){
						$columns_id=[];
						$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_column_config");
						while($tables=$res_tables->fetch()){
							if(checkPermission(2,$tables['id'],"read",$tables['act'],$tables['table_id'])==1){
								array_push($columns_id,$tables['id']);
							}
						}
					}

					if(in_array("-1",$permission_name) || in_array("_all",$permission_name)){
						$permission_name=[];

						$create=0;
						$read=0;
						$update=0;
						$delete=0;
						foreach ($tables_id as $key => $value) {
							$c=0;$r=0;$u=0;$d=0;
							foreach ($permission_power_list as $keys => $values) {
								if(checkPermission(1,$value,"create",$values[1],"")==1){
									$c=1;
								}
								if($keys==count($permission_power_list)-1 && $c==1){
									$create++;
								}

								if(checkPermission(1,$value,"read",$values[1],"")==1){
									$r=1;
								}
								if($keys==count($permission_power_list)-1 && $r==1){
									$read++;
								}

								if(checkPermission(1,$value,"update",$values[1],"")==1){
									$u=1;
								}
								if($keys==count($permission_power_list)-1 && $u==1){
									$update++;
								}

								if(checkPermission(1,$value,"delete",$values[1],"")==1){
									$d=1;
								}
								if($keys==count($permission_power_list)-1 && $d==1){
									$delete++;
								}
							}
						}

						$createss=0;
						$readss=0;
						$updatess=0;
						$deletess=0;
						foreach ($columns_id as $key => $value) {
							$c=0;$r=0;$u=0;$d=0;
							foreach ($permission_power_list as $keys => $values) {
								$table_id=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$value."'")->fetch()['table_id'];
								if(checkPermission(2,$value,"create",$values[1],$table_id)==1){
									$c=1;
								}
								if($keys==count($permission_power_list)-1 && $c==1){
									$createss++;
								}

								if(checkPermission(2,$value,"read",$values[1],$table_id)==1){
									$r=1;
								}
								if($keys==count($permission_power_list)-1 && $r==1){
									$readss++;
								}

								if(checkPermission(2,$value,"update",$values[1],$table_id)==1){
									$u=1;
								}
								if($keys==count($permission_power_list)-1 && $u==1){
									$updatess++;
								}

								if(checkPermission(2,$value,"delete",$values[1],$table_id)==1){
									$d=1;
								}
								if($keys==count($permission_power_list)-1 && $d==1){
									$deletess++;
								}
							}
						}

						if(count($tables_id)==$create && count($tables_id)==$read && count($tables_id)==$update && count($tables_id)==$delete && count($columns_id)==$createss && count($columns_id)==$readss && count($columns_id)==$updatess && count($columns_id)==$deletess){
							array_push($permission_name,"-1");
						}
						if(count($tables_id)==$create && count($columns_id)==$createss){
							array_push($permission_name,"create");
						}
						if(count($tables_id)==$read && count($columns_id)==$readss){
							array_push($permission_name,"read");
						}
						if(count($tables_id)==$update && count($columns_id)==$updatess){
							array_push($permission_name,"update");
						}
						if(count($tables_id)==$delete && count($columns_id)==$deletess){
							array_push($permission_name,"delete");
						}
					}

					$values_checker=[];
					foreach ($permission_power_list as $key => $value) {
						$values_checker[$value[0]]=0;
					}

					$values_checkerss=[];
					foreach ($permission_power_list as $key => $value) {
						$values_checkerss[$value[0]]=0;
					}

					foreach ($tables_id as $key => $value) {
						foreach ($permission_name as $keys => $values) {
							foreach ($permission_power_list as $keyss => $valuess) {
								if(checkPermission(1,$value,$values,$valuess[1],"")==1){
									$values_checker[$valuess[0]]++;
								}
							}
						}
					}

					foreach ($columns_id as $key => $value) {
						foreach ($permission_name as $keys => $values) {
							foreach ($permission_power_list as $keyss => $valuess) {
								$table_id=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$value."'")->fetch()['table_id'];
								if(checkPermission(2,$value,$values,$valuess[1],$table_id)==1){
									$values_checkerss[$valuess[0]]++;
								}
							}
						}
					}

					?><option value="_all" class="data-text" data-text-fa="<?php print_r($data_text['fa']['a']); ?>" data-text-en="<?php print_r($data_text['en']['a']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['a']); ?></option><?php
					foreach ($permission_power_list as $key => $value) {
						if($values_checker[$value[0]]==count($tables_id)*count($permission_name) && $values_checkerss[$value[0]]==count($columns_id)*count($permission_name)){
							if($value[1]=="-1"){
								?><option value="-1" class="data-text" data-text-fa="<?php print_r($data_text['fa']['a']); ?>" data-text-en="<?php print_r($data_text['en']['a']); ?>"><?php print_r($data_text[$GLOBALS['user_language']]['a']); ?></option><?php
							}else{
								?><option value="<?php print_r($value[1]); ?>"><?php print_r($value[1]); ?></option><?php
							}
						}
					}
				}

				if(isset($_GET['edit_permission_'.$last_name['permissions']['tables_permission']]) && isset($_POST["id"])){
					$id=$_POST['id'];
					$res_permission=$connection->query("SELECT * FROM ".$sub_name."table_permissions WHERE id='".$id."'");
					if($res_permission->rowCount()==1){
						$permission=$res_permission->fetch();
						echo json_encode([[$permission['admin_id']],[$permission['table_id']],[$permission['permission_name']],[$permission['permission_value']]]);
					}else{
						echo json_encode(0);
					}
				}

				if(isset($_GET['edit_permission_'.$last_name['permissions']['columns_permission']]) && isset($_POST["id"])){
					$id=$_POST['id'];
					$res_permission=$connection->query("SELECT * FROM ".$sub_name."column_permissions WHERE id='".$id."'");
					if($res_permission->rowCount()==1){
						$permission=$res_permission->fetch();
						echo json_encode([[$permission['admin_id']],[$permission['column_id']],[$permission['permission_name']],[$permission['permission_value']]]);
					}else{
						echo json_encode(0);
					}
				}

				if(isset($_GET['edit_permission_'.$last_name['permissions']['menu_permission']]) && isset($_POST["id"])){
					$id=$_POST['id'];
					$res_permission=$connection->query("SELECT * FROM ".$sub_name."menu_permissions WHERE id='".$id."'");
					if($res_permission->rowCount()==1){
						$permission=$res_permission->fetch();
						echo json_encode([[$permission['admin_id']],[$permission['menu_id']],[$permission['permission_value']]]);
					}else{
						echo json_encode(0);
					}
				}

				if(isset($_GET['addTablePermission']) && isset($permissions_start) && $permissions_start==1 || isset($_GET['addAndEnableTablePermission']) && isset($permissions_start) && $permissions_start==1){

					$connection->beginTransaction();
					$error=0;

					foreach ($username as $key => $value) {
						foreach ($tables_id as $keys => $values) {
							foreach ($permission_name as $keyss => $valuess) {
								foreach ($permission_value as $keysss => $valuesss) {
									if(isset($_GET['addAndEnableTablePermission'])){
										$res_check=$connection->query("SELECT * FROM ".$sub_name.$what_table_name."_permissions WHERE
											admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."' AND act=1
											||
											admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name=-1 AND permission_value=-1 AND act=1
											||
											admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name='".$valuess."' AND permission_value=-1 AND act=1
											||
											admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name=-1 AND permission_value='".$valuesss."' AND act=1
										");
									}elseif(isset($_GET['addTablePermission']) && isset($permissions_start) && $permissions_start==1){
										$res_check=$connection->query("SELECT * FROM ".$sub_name.$what_table_name."_permissions WHERE
											admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."'
											||
											admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name=-1 AND permission_value=-1
											||
											admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name='".$valuess."' AND permission_value=-1
											||
											admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name=-1 AND permission_value='".$valuesss."'
										");
									}
									if($res_check->rowCount()==0){

										if(isset($_GET['addAndEnableTablePermission'])){
											$res_check=$connection->query("SELECT * FROM ".$sub_name.$what_table_name."_permissions WHERE admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."' AND act=0 || admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name=-1 AND permission_value=-1 AND act=0 || admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name='".$valuess."' AND permission_value=-1 AND act=0 || admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name=-1 AND permission_value='".$valuesss."' AND act=0");if($res_check->rowCount()==1){$check=$res_check->fetch();}
										}

										if($what_table_name=="column"){$table_id=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."'")->fetch()['table_id'];}
										if(
											(isset($_POST[$last_name['permissions']['tables_permission'].'_id']) ? checkPermission(1,$values,$valuess,$valuesss,""):(isset($_POST[$last_name['permissions']['columns_permission'].'_id']) ? checkPermission(2,$values,$valuess,$valuesss,$table_id):"")) &&
											checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"") &&
											checkPermission(1,getTableByName($sub_name.$what_table_name."_permissions")['id'],"create",getTableByName($sub_name.$what_table_name."_permissions")['act'],"")
										){

											if(isset($_POST[$last_name['permissions']['tables_permission'].'_id'])){
												$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."table_config WHERE id='".$values."' AND ".checkPermission(4,$values,$valuess,$valuesss,"",$value)."=1");
											}else if(isset($_POST[$last_name['permissions']['columns_permission'].'_id'])){
												$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."' AND ".checkPermission(4,$values,$valuess,$valuesss,"",$value)."=1");
											}
											$sql_table->execute();

											if($sql_table->rowCount()==1){
												$table=$sql_table->fetch();
												$table_name=$table['description_name_'.$GLOBALS['user_language']];

												if($error==0){
													try{
														if(isset($check) && $check['act']==0){
															$sql="UPDATE ".$sub_name.$what_table_name."_permissions SET act=1 WHERE id='".$check['id']."'";
															if($connection->exec($sql)){
																$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._modified_.._";$value=$current_value;
															}else{
																$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
															}
														}else{
															$last_ordering=lastTablePermissionOrderId();
															if(isset($_POST[$last_name['permissions']['tables_permission'].'_id'])){
																$sql="INSERT INTO ".$sub_name.$what_table_name."_permissions (".$columns.") VALUES ('".$values."','".$value."','".$valuess."','".$valuesss."','".$table['description_name_fa']."','".$table['description_name_en']."','".$data_text['fa'][$valuess]."','".$data_text['en'][$valuess]."',".$last_ordering.",1)";
															}else if(isset($_POST[$last_name['permissions']['columns_permission'].'_id'])){
																$tabless=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE id='".$table['table_id']."'")->fetch();
																$sql="INSERT INTO ".$sub_name.$what_table_name."_permissions (".$columns.") VALUES ('".$tabless['id']."','".$value."','".$values."','".$valuess."','".$valuesss."','".$tabless['description_name_fa']."','".$tabless['description_name_en']."','".$table['description_name_fa']."','".$table['description_name_en']."','".$data_text['fa'][$valuess]."','".$data_text['en'][$valuess]."',".$last_ordering.",1)";
															}
															if($connection->exec($sql)){
																$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._added_.._";$value=$current_value;
															}else{
																$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
															}
														}
													}catch(Exception $e){
														$error=1;
														$connection->rollBack();
														$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
													}
												}else{
													$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
												}
											}else{
												$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
											}
										}
									}
								}
							}
						}
					}

					if($connection->inTransaction()==true){
						$connection->commit();
					}

					echo "done";

				}

				if(isset($_GET['enableTablePermission']) && isset($permissions_start) && $permissions_start==1 || isset($_GET['disableTablePermission']) && isset($permissions_start) && $permissions_start==1){

					if(isset($_GET['enableTablePermission'])){
						$acted=1;
						$deacted=0;
					}elseif(isset($_GET['disableTablePermission'])){
						$acted=0;
						$deacted=1;
					}

					$connection->beginTransaction();
					$error=0;

					foreach ($username as $key => $value) {
						foreach ($tables_id as $keys => $values) {
							foreach ($permission_name as $keyss => $valuess) {
								foreach ($permission_value as $keysss => $valuesss) {
									$res_check=$connection->query("SELECT * FROM ".$sub_name.$what_table_name."_permissions WHERE admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."' AND act='".$deacted."'");
									if($res_check->rowCount()!=0){
										if($what_table_name=="column"){$table_id=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."'")->fetch()['table_id'];}
										if(
											(isset($_POST[$last_name['permissions']['tables_permission'].'_id']) ? checkPermission(1,$values,$valuess,$valuesss,""):(isset($_POST[$last_name['permissions']['columns_permission'].'_id']) ? checkPermission(2,$values,$valuess,$valuesss,$table_id):"")) &&
											checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"") &&
											checkPermission(1,getTableByName($sub_name.$what_table_name."_permissions")['id'],"update",getTableByName($sub_name."table_permissions")['act'],"")
										){

											if(isset($_POST[$last_name['permissions']['tables_permission'].'_id'])){
												$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."table_config WHERE id='".$values."' AND ".checkPermission(4,$values,$valuess,$valuesss,"",$value)."=1");
											}else if(isset($_POST[$last_name['permissions']['columns_permission'].'_id'])){
												$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."' AND ".checkPermission(4,$values,$valuess,$valuesss,"",$value)."=1");
											}
											$sql_table->execute();

											if($sql_table->rowCount()==1){
												$table=$sql_table->fetch();
												$table_name=$table['description_name_'.$GLOBALS['user_language']];

												if($error==0){
													try{
														$sql="UPDATE ".$sub_name.$what_table_name."_permissions SET act='".$acted."' WHERE admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."' AND act='".$deacted."'";
														if($connection->exec($sql)){
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._modified_.._";$value=$current_value;
														}else{
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
														}
													}catch(Exception $e){
														$error=1;
														$connection->rollBack();
														$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
													}
												}else{
													$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
												}
											}else{
												$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
											}
										}
									}
								}
							}
						}
					}

					if($connection->inTransaction()==true){
						$connection->commit();
					}

					echo "done";

				}

				if(isset($_GET['deleteTablePermission']) && isset($permissions_start) && $permissions_start==1){

					$connection->beginTransaction();
					$error=0;

					foreach ($username as $key => $value) {
						foreach ($tables_id as $keys => $values) {
							foreach ($permission_name as $keyss => $valuess) {
								foreach ($permission_value as $keysss => $valuesss) {
									$res_check=$connection->query("SELECT * FROM ".$sub_name.$what_table_name."_permissions WHERE admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."'");
									if($res_check->rowCount()!=0){
										if($what_table_name=="column"){$table_id=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."'")->fetch()['table_id'];}
										if(
											(isset($_POST[$last_name['permissions']['tables_permission'].'_id']) ? checkPermission(1,$values,$valuess,$valuesss,""):(isset($_POST[$last_name['permissions']['columns_permission'].'_id']) ? checkPermission(2,$values,$valuess,$valuesss,$table_id):"")) &&
											checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"") &&
											checkPermission(1,getTableByName($sub_name.$what_table_name."_permissions")['id'],"delete",getTableByName($sub_name."table_permissions")['act'],"")
										){

											if(isset($_POST[$last_name['permissions']['tables_permission'].'_id'])){
												$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."table_config WHERE id='".$values."' AND ".checkPermission(4,$values,$valuess,$valuesss,"",$value)."=1");
											}else if(isset($_POST[$last_name['permissions']['columns_permission'].'_id'])){
												$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."' AND ".checkPermission(4,$values,$valuess,$valuesss,"",$value)."=1");
											}
											$sql_table->execute();

											if($sql_table->rowCount()==1){
												$table=$sql_table->fetch();
												$table_name=$table['description_name_'.$GLOBALS['user_language']];

												if($error==0){
													try{
														$sql="DELETE FROM ".$sub_name.$what_table_name."_permissions WHERE admin_id='".$value."' AND ".$what_table_name."_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."'";
														if($connection->exec($sql)){
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._deleted_.._";$value=$current_value;
														}else{
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
														}
													}catch(Exception $e){
														$error=1;
														$connection->rollBack();
														$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
													}
												}else{
													$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
												}
											}else{
												$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
											}
										}
									}
								}
							}
						}
					}

					if($connection->inTransaction()==true){
						$connection->commit();
					}

					echo "done";

				}

				if(isset($_GET['addMenuPermission']) && isset($permissions_start) && $permissions_start==1 || isset($_GET['addAndEnableMenuPermission']) && isset($permissions_start) && $permissions_start==1){

					$connection->beginTransaction();
					$error=0;

					foreach ($username as $key => $value) {
						foreach ($tables_id as $keys => $values) {
							foreach ($permission_value as $keysss => $valuesss) {
								if(isset($_GET['addAndEnableMenuPermission'])){
									$res_check=$connection->query("SELECT * FROM ".$sub_name."menu_permissions WHERE
										admin_id='".$value."' AND menu_id='".$values."' AND permission_value='".$valuesss."' AND act=1
										||
										admin_id='".$value."' AND menu_id='".$values."' AND permission_value=-1 AND act=1
									");
								}elseif(isset($_GET['addMenuPermission']) && isset($permissions_start) && $permissions_start==1){
									$res_check=$connection->query("SELECT * FROM ".$sub_name."menu_permissions WHERE
										admin_id='".$value."' AND menu_id='".$values."' AND permission_value='".$valuesss."'
										||
										admin_id='".$value."' AND menu_id='".$values."' AND permission_value=-1
									");
								}
								if($res_check->rowCount()==0){

									if(isset($_GET['addAndEnableMenuPermission'])){
										$res_check=$connection->query("SELECT * FROM ".$sub_name."menu_permissions WHERE
											admin_id='".$value."' AND menu_id='".$values."' AND permission_value='".$valuesss."' AND act=0
											||
											admin_id='".$value."' AND menu_id='".$values."' AND permission_value=-1 AND act=0
										");if($res_check->rowCount()==1){$check=$res_check->fetch();}
									}

									if(
										checkPermission(2,getColumnByName($sub_name."menu_permissions","act")['id'],"create",$valuesss,getTableByName($sub_name."menu_permissions")['id']) &&
										checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"") &&
										checkPermission(1,getTableByName($sub_name."menu_permissions")['id'],"create",getTableByName($sub_name."menu_permissions")['act'],"")
									){

										$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."menu WHERE id='".$values."'");
										$sql_table->execute();

										if($sql_table->rowCount()==1){
											$table=$sql_table->fetch();
											$table_name=$table['menu_name_'.$GLOBALS['user_language']];

											if($error==0){
												try{
													if(isset($check) && $check['act']==0){
														$sql="UPDATE ".$sub_name."menu_permissions SET act=1 WHERE id='".$check['id']."'";
														if($connection->exec($sql)){
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._modified_.._";$value=$current_value;
														}else{
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
														}
													}else{
														$last_ordering=lastTablePermissionOrderId();
														$sql="INSERT INTO ".$sub_name."menu_permissions (".$columns.") VALUES ('".$value."','".$values."','".$valuesss."','".$table['menu_name_fa']."','".$table['menu_name_en']."',".$last_ordering.",1)";
														if($connection->exec($sql)){
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._added_.._";$value=$current_value;
														}else{
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
														}
													}
												}catch(Exception $e){
													$error=1;
													$connection->rollBack();
													$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
												}
											}else{
												$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
											}
										}else{
											$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
										}
									}
								}
							}
						}
					}

					if($connection->inTransaction()==true){
						$connection->commit();
					}

					echo "done";

				}

				if(isset($_GET['enableMenuPermission']) && isset($permissions_start) && $permissions_start==1 || isset($_GET['disableMenuPermission']) && isset($permissions_start) && $permissions_start==1){

					if(isset($_GET['enableMenuPermission'])){
						$acted=1;
						$deacted=0;
					}elseif(isset($_GET['disableMenuPermission'])){
						$acted=0;
						$deacted=1;
					}

					$connection->beginTransaction();
					$error=0;

					foreach ($username as $key => $value) {
						foreach ($tables_id as $keys => $values) {
							foreach ($permission_value as $keysss => $valuesss) {
								$res_check=$connection->query("SELECT * FROM ".$sub_name."menu_permissions WHERE admin_id='".$value."' AND menu_id='".$values."' AND permission_value='".$valuesss."' AND act='".$deacted."'");
								if($res_check->rowCount()!=0){
									if($what_table_name=="column"){$table_id=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."'")->fetch()['table_id'];}
									if(
										checkPermission(2,getColumnByName($sub_name."menu_permissions","act")['id'],"update",$valuesss,getTableByName($sub_name."menu_permissions")['id']) &&
										checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"") &&
										checkPermission(1,getTableByName($sub_name."menu_permissions")['id'],"update",getTableByName($sub_name."menu_permissions")['act'],"")
									){

										$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."menu WHERE id='".$values."'");
										$sql_table->execute();

										if($sql_table->rowCount()==1){
											$table=$sql_table->fetch();
											$table_name=$table['menu_name_'.$GLOBALS['user_language']];

											if($error==0){
												try{
													$sql="UPDATE ".$sub_name."menu_permissions SET act='".$acted."' WHERE admin_id='".$value."' AND menu_id='".$values."' AND permission_value='".$valuesss."' AND act='".$deacted."'";
													if($connection->exec($sql)){
														$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._modified_.._";$value=$current_value;
													}else{
														$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
													}
												}catch(Exception $e){
													$error=1;
													$connection->rollBack();
													$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
												}
											}else{
												$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
											}
										}else{
											$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
										}
									}
								}
							}
						}
					}

					if($connection->inTransaction()==true){
						$connection->commit();
					}

					echo "done";

				}

				if(isset($_GET['deleteMenuPermission']) && isset($permissions_start) && $permissions_start==1){

					$connection->beginTransaction();
					$error=0;

					foreach ($username as $key => $value) {
						foreach ($tables_id as $keys => $values) {
							foreach ($permission_value as $keysss => $valuesss) {
								$res_check=$connection->query("SELECT * FROM ".$sub_name."menu_permissions WHERE admin_id='".$value."' AND menu_id='".$values."' AND permission_value='".$valuesss."'");
								if($res_check->rowCount()!=0){
									if($what_table_name=="column"){$table_id=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."'")->fetch()['table_id'];}
									if(
										checkPermission(2,getColumnByName($sub_name."menu_permissions","act")['id'],"delete",$valuesss,getTableByName($sub_name."menu_permissions")['id']) &&
										checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"") &&
										checkPermission(1,getTableByName($sub_name."menu_permissions")['id'],"delete",getTableByName($sub_name."menu_permissions")['act'],"")
									){

										$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."menu WHERE id='".$values."'");
										$sql_table->execute();

										if($sql_table->rowCount()==1){
											$table=$sql_table->fetch();
											$table_name=$table['menu_name_'.$GLOBALS['user_language']];

											if($error==0){
												try{
													$sql="DELETE FROM ".$sub_name."menu_permissions WHERE admin_id='".$value."' AND menu_id='".$values."' AND permission_value='".$valuesss."'";
													if($connection->exec($sql)){
														$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._deleted_.._";$value=$current_value;
													}else{
														$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
													}
												}catch(Exception $e){
													$error=1;
													$connection->rollBack();
													$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
												}
											}else{
												$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
											}
										}else{
											$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".$table_name."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
										}
									}
								}
							}
						}
					}

					if($connection->inTransaction()==true){
						$connection->commit();
					}

					echo "done";

				}

				if(isset($_GET['addTableColumnPermission']) && isset($permissions_start) && $permissions_start==1 || isset($_GET['addAndEnableTableColumnPermission']) && isset($permissions_start) && $permissions_start==1){

					$connection->beginTransaction();
					$error=0;

					foreach ($username as $key => $value) {
						foreach ($tables_id as $keys => $values) {
							foreach ($permission_name as $keyss => $valuess) {
								foreach ($permission_value as $keysss => $valuesss) {
									if(isset($_GET['addAndEnableTableColumnPermission'])){
										$res_check=$connection->query("SELECT * FROM ".$sub_name."table_permissions WHERE
											admin_id='".$value."' AND table_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."' AND act=1
											||
											admin_id='".$value."' AND table_id='".$values."' AND permission_name=-1 AND permission_value=-1 AND act=1
											||
											admin_id='".$value."' AND table_id='".$values."' AND permission_name='".$valuess."' AND permission_value=-1 AND act=1
											||
											admin_id='".$value."' AND table_id='".$values."' AND permission_name=-1 AND permission_value='".$valuesss."' AND act=1
										");
									}elseif(isset($_GET['addTableColumnPermission']) && isset($permissions_start) && $permissions_start==1){
										$res_check=$connection->query("SELECT * FROM ".$sub_name."table_permissions WHERE
											admin_id='".$value."' AND table_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."'
											||
											admin_id='".$value."' AND table_id='".$values."' AND permission_name=-1 AND permission_value=-1
											||
											admin_id='".$value."' AND table_id='".$values."' AND permission_name='".$valuess."' AND permission_value=-1
											||
											admin_id='".$value."' AND table_id='".$values."' AND permission_name=-1 AND permission_value='".$valuesss."'
										");
									}
									if($res_check->rowCount()==0){

										if(isset($_GET['addAndEnableTableColumnPermission'])){
											$res_check=$connection->query("SELECT * FROM ".$sub_name."table_permissions WHERE admin_id='".$value."' AND table_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."' AND act=0 || admin_id='".$value."' AND table_id='".$values."' AND permission_name=-1 AND permission_value=-1 AND act=0 || admin_id='".$value."' AND table_id='".$values."' AND permission_name='".$valuess."' AND permission_value=-1 AND act=0 || admin_id='".$value."' AND table_id='".$values."' AND permission_name=-1 AND permission_value='".$valuesss."' AND act=0");if($res_check->rowCount()==1){$check=$res_check->fetch();}
										}

										if(
											checkPermission(1,$values,$valuess,$valuesss,"") &&
											checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"") &&
											checkPermission(1,getTableByName($sub_name."table_permissions")['id'],"create",getTableByName($sub_name."table_permissions")['act'],"")
										){

											$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."table_config WHERE id='".$values."' AND ".checkPermission(4,$values,$valuess,$valuesss,"",$value)."=1");
											$sql_table->execute();

											if($sql_table->rowCount()==1){
												$table=$sql_table->fetch();
												$table_name=$table['description_name_'.$GLOBALS['user_language']];

												if($error==0){
													try{
														if(isset($check) && $check['act']==0){
															$sql="UPDATE ".$sub_name."table_permissions SET act=1 WHERE id='".$check['id']."'";
															if($connection->exec($sql)){
																$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._modified_.._";$value=$current_value;
															}else{
																$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
															}
														}else{
															$what_table_name="table";
															$last_ordering=lastTablePermissionOrderId();
															$sql="INSERT INTO ".$sub_name."table_permissions (".$columns_table.") VALUES ('".$values."','".$value."','".$valuess."','".$valuesss."','".$table['description_name_fa']."','".$table['description_name_en']."','".$data_text['fa'][$valuess]."','".$data_text['en'][$valuess]."',".$last_ordering.",1)";
															if($connection->exec($sql)){
																$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._added_.._";$value=$current_value;
															}else{
																$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
															}
														}
													}catch(Exception $e){
														$error=1;
														$connection->rollBack();
														$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
													}
												}else{
													$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
												}
											}else{
												$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
											}
										}
									}
								}
							}
						}
						foreach ($columns_id as $keys => $values) {
							$table_report=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE id='".$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."'")->fetch()['table_id']."'")->fetch();
							foreach ($permission_name as $keyss => $valuess) {
								foreach ($permission_value as $keysss => $valuesss) {
									if(isset($_GET['addAndEnableTableColumnPermission'])){
										$res_check=$connection->query("SELECT * FROM ".$sub_name."column_permissions WHERE
											admin_id='".$value."' AND column_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."' AND act=1
											||
											admin_id='".$value."' AND column_id='".$values."' AND permission_name=-1 AND permission_value=-1 AND act=1
											||
											admin_id='".$value."' AND column_id='".$values."' AND permission_name='".$valuess."' AND permission_value=-1 AND act=1
											||
											admin_id='".$value."' AND column_id='".$values."' AND permission_name=-1 AND permission_value='".$valuesss."' AND act=1
										");
									}elseif(isset($_GET['addTableColumnPermission']) && isset($permissions_start) && $permissions_start==1){
										$res_check=$connection->query("SELECT * FROM ".$sub_name."column_permissions WHERE
											admin_id='".$value."' AND column_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."'
											||
											admin_id='".$value."' AND column_id='".$values."' AND permission_name=-1 AND permission_value=-1
											||
											admin_id='".$value."' AND column_id='".$values."' AND permission_name='".$valuess."' AND permission_value=-1
											||
											admin_id='".$value."' AND column_id='".$values."' AND permission_name=-1 AND permission_value='".$valuesss."'
										");
									}
									if($res_check->rowCount()==0){

										if(isset($_GET['addAndEnableTableColumnPermission'])){
											$res_check=$connection->query("SELECT * FROM ".$sub_name."column_permissions WHERE admin_id='".$value."' AND column_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."' AND act=0 || admin_id='".$value."' AND column_id='".$values."' AND permission_name=-1 AND permission_value=-1 AND act=0 || admin_id='".$value."' AND column_id='".$values."' AND permission_name='".$valuess."' AND permission_value=-1 AND act=0 || admin_id='".$value."' AND column_id='".$values."' AND permission_name=-1 AND permission_value='".$valuesss."' AND act=0");if($res_check->rowCount()==1){$check=$res_check->fetch();}
										}

										$table_id=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."'")->fetch()['table_id'];

										if(
											checkPermission(2,$values,$valuess,$valuesss,$table_id) &&
											checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"") &&
											checkPermission(1,getTableByName($sub_name."column_permissions")['id'],"create",getTableByName($sub_name."column_permissions")['act'],"")
										){

											$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."' AND ".checkPermission(4,$values,$valuess,$valuesss,"",$value)."=1");
											$sql_table->execute();

											if($sql_table->rowCount()==1){
												$table=$sql_table->fetch();
												$table_name=$table['description_name_'.$GLOBALS['user_language']];

												if($error==0){
													try{
														if(isset($check) && $check['act']==0){
															$sql="UPDATE ".$sub_name."column_permissions SET act=1 WHERE id='".$check['id']."'";
															if($connection->exec($sql)){
																$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._modified_.._";$value=$current_value;
															}else{
																$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
															}
														}else{
															$what_table_name="column";
															$last_ordering=lastTablePermissionOrderId();
															$tabless=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE id='".$table['table_id']."'")->fetch();
															$sql="INSERT INTO ".$sub_name."column_permissions (".$columns_column.") VALUES ('".$tabless['id']."','".$value."','".$values."','".$valuess."','".$valuesss."','".$tabless['description_name_fa']."','".$tabless['description_name_en']."','".$table['description_name_fa']."','".$table['description_name_en']."','".$data_text['fa'][$valuess]."','".$data_text['en'][$valuess]."',".$last_ordering.",1)";
															if($connection->exec($sql)){
																$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._added_.._";$value=$current_value;
															}else{
																$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
															}
														}
													}catch(Exception $e){
														$error=1;
														$connection->rollBack();
														$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
													}
												}else{
													$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
												}
											}else{
												$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
											}
										}
									}
								}
							}
						}
					}

					if($connection->inTransaction()==true){
						$connection->commit();
					}

					echo "done";

				}

				if(isset($_GET['enableTableColumnPermission']) && isset($permissions_start) && $permissions_start==1 || isset($_GET['disableTableColumnPermission']) && isset($permissions_start) && $permissions_start==1){

					if(isset($_GET['enableTableColumnPermission'])){
						$acted=1;
						$deacted=0;
					}elseif(isset($_GET['disableTableColumnPermission'])){
						$acted=0;
						$deacted=1;
					}

					$connection->beginTransaction();
					$error=0;

					foreach ($username as $key => $value) {
						foreach ($tables_id as $keys => $values) {
							foreach ($permission_name as $keyss => $valuess) {
								foreach ($permission_value as $keysss => $valuesss) {
									$res_check=$connection->query("SELECT * FROM ".$sub_name."table_permissions WHERE admin_id='".$value."' AND "."table_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."' AND act='".$deacted."'");
									if($res_check->rowCount()!=0){

										if(
											checkPermission(1,$values,$valuess,$valuesss,"") &&
											checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"") &&
											checkPermission(1,getTableByName($sub_name."table_permissions")['id'],"update",getTableByName($sub_name."table_permissions")['act'],"")
										){

											$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."table_config WHERE id='".$values."' AND ".checkPermission(4,$values,$valuess,$valuesss,"",$value)."=1");
											$sql_table->execute();

											if($sql_table->rowCount()==1){
												$table=$sql_table->fetch();
												$table_name=$table['description_name_'.$GLOBALS['user_language']];

												if($error==0){
													try{
														$sql="UPDATE ".$sub_name."table_permissions SET act='".$acted."' WHERE admin_id='".$value."' AND "."table_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."' AND act='".$deacted."'";
														if($connection->exec($sql)){
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._modified_.._";$value=$current_value;
														}else{
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
														}
													}catch(Exception $e){
														$error=1;
														$connection->rollBack();
														$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
													}
												}else{
													$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
												}
											}else{
												$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
											}
										}
									}
								}
							}
						}
						foreach ($columns_id as $keys => $values) {
							$table_report=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE id='".$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."'")->fetch()['table_id']."'")->fetch();
							foreach ($permission_name as $keyss => $valuess) {
								foreach ($permission_value as $keysss => $valuesss) {
									$res_check=$connection->query("SELECT * FROM ".$sub_name."column_permissions WHERE admin_id='".$value."' AND "."column_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."' AND act='".$deacted."'");
									if($res_check->rowCount()!=0){
										$table_id=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."'")->fetch()['table_id'];
										if(
											checkPermission(2,$values,$valuess,$valuesss,$table_id) &&
											checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"") &&
											checkPermission(1,getTableByName($sub_name."column_permissions")['id'],"update",getTableByName($sub_name."table_permissions")['act'],"")
										){

											$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."' AND ".checkPermission(4,$values,$valuess,$valuesss,"",$value)."=1");
											$sql_table->execute();

											if($sql_table->rowCount()==1){
												$table=$sql_table->fetch();
												$table_name=$table['description_name_'.$GLOBALS['user_language']];

												if($error==0){
													try{
														$sql="UPDATE ".$sub_name."column_permissions SET act='".$acted."' WHERE admin_id='".$value."' AND "."column_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."' AND act='".$deacted."'";
														if($connection->exec($sql)){
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._modified_.._";$value=$current_value;
														}else{
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
														}
													}catch(Exception $e){
														$error=1;
														$connection->rollBack();
														$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
													}
												}else{
													$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
												}
											}else{
												$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
											}
										}
									}
								}
							}
						}
					}

					if($connection->inTransaction()==true){
						$connection->commit();
					}

					echo "done";

				}

				if(isset($_GET['deleteTableColumnPermission']) && isset($permissions_start) && $permissions_start==1){

					$connection->beginTransaction();
					$error=0;

					foreach ($username as $key => $value) {
						foreach ($tables_id as $keys => $values) {
							foreach ($permission_name as $keyss => $valuess) {
								foreach ($permission_value as $keysss => $valuesss) {
									$res_check=$connection->query("SELECT * FROM ".$sub_name."table_permissions WHERE admin_id='".$value."' AND "."table_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."'");
									if($res_check->rowCount()!=0){
										if(
											checkPermission(1,$values,$valuess,$valuesss,"") &&
											checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"") &&
											checkPermission(1,getTableByName($sub_name."table_permissions")['id'],"delete",getTableByName($sub_name."table_permissions")['act'],"")
										){

											$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."table_config WHERE id='".$values."' AND ".checkPermission(4,$values,$valuess,$valuesss,"",$value)."=1");
											$sql_table->execute();

											if($sql_table->rowCount()==1){
												$table=$sql_table->fetch();
												$table_name=$table['description_name_'.$GLOBALS['user_language']];

												if($error==0){
													try{
														$sql="DELETE FROM ".$sub_name."table_permissions WHERE admin_id='".$value."' AND "."table_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."'";
														if($connection->exec($sql)){
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._deleted_.._";$value=$current_value;
														}else{
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
														}
													}catch(Exception $e){
														$error=1;
														$connection->rollBack();
														$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
													}
												}else{
													$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
												}
											}else{
												$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "(table) ":"(جدول) ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
											}
										}
									}
								}
							}
						}
						foreach ($columns_id as $keys => $values) {
							$table_report=$connection->query("SELECT * FROM ".$sub_name."table_config WHERE id='".$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."'")->fetch()['table_id']."'")->fetch();
							foreach ($permission_name as $keyss => $valuess) {
								foreach ($permission_value as $keysss => $valuesss) {
									$res_check=$connection->query("SELECT * FROM ".$sub_name."column_permissions WHERE admin_id='".$value."' AND "."column_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."'");
									if($res_check->rowCount()!=0){
										$table_id=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."'")->fetch()['table_id'];
										if(
											checkPermission(2,$values,$valuess,$valuesss,$table_id) &&
											checkPermission(1,getTableByName($sub_name."admins")['id'],"read",getTableByName($sub_name."admins")['act'],"") &&
											checkPermission(1,getTableByName($sub_name."column_permissions")['id'],"delete",getTableByName($sub_name."table_permissions")['act'],"")
										){

											$sql_table = $connection->prepare("SELECT * FROM ".$sub_name."table_column_config WHERE id='".$values."' AND ".checkPermission(4,$values,$valuess,$valuesss,"",$value)."=1");
											$sql_table->execute();

											if($sql_table->rowCount()==1){
												$table=$sql_table->fetch();
												$table_name=$table['description_name_'.$GLOBALS['user_language']];

												if($error==0){
													try{
														$sql="DELETE FROM ".$sub_name."column_permissions WHERE admin_id='".$value."' AND "."column_id='".$values."' AND permission_name='".$valuess."' AND permission_value='".$valuesss."'";
														if($connection->exec($sql)){
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._deleted_.._";$value=$current_value;
														}else{
															$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
														}
													}catch(Exception $e){
														$error=1;
														$connection->rollBack();
														$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
													}
												}else{
													$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
												}
											}else{
												$current_value=$value;$username_valid=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$value."'")->rowCount();if($username_valid==0){$rank=$connection->query("SELECT * FROM ".$sub_name."rank WHERE id='".$value."'");if($rank->rowCount()==1){$rank=$rank->fetch();$value="<label class='data-text' data-text-en='".$rank['rank_name_en']." (rank)' data-text-fa='".$rank['rank_name_fa']." (مقام)'>".$rank['rank_name_'.$GLOBALS['user_language']].($GLOBALS['user_language']=="en" ? " (rank)":" (مقام)")."</label>";}}echo $value."_._".($GLOBALS['user_language']=="en" ? "[".$table_report['description_name_en']."] ":"[".$table_report['description_name_fa']."] ").$table_name."_._".$data_text[$GLOBALS['user_language']][$valuess]."_._".$data_text[$GLOBALS['user_language']][$valuesss]."_._failed_.._";$value=$current_value;
											}
										}
									}
								}
							}
						}
					}

					if($connection->inTransaction()==true){
						$connection->commit();
					}

					echo "done";

				}

            }else{
				echo "redirect_._login/";
			}
		}else{
			echo "redirect_._login/";
		}
	}else{
		echo "redirect_._setup/";
	}
?>
<?php
	$conn_dir="../connection/connect.php";
	if(session_status() == PHP_SESSION_NONE) {
		session_start(['cookie_lifetime' => 86400]);
	}
	require_once("config.php");
	require_once("setting/check_database.php");
	require_once("setting/language.php");
	if(isset($connected) && $connected == 1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables)) >= count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user = $connection->query("SELECT * FROM " . $sub_name . "admins WHERE username='" . $_SESSION['username'] . "' AND act=1");
			$user_stats = $res_user->rowCount();
			if($user_stats == 1 && (checkPermission(1,getTableByName($sub_name."file_manager")['id'],"create",getTableByName($sub_name."file_manager")['act'],"") && checkPermission(1,getTableByName($sub_name."file_manager")['id'],"read",getTableByName($sub_name."file_manager")['act'],"") && checkPermission(1,getTableByName($sub_name."file_manager")['id'],"update",getTableByName($sub_name."file_manager")['act'],"") && checkPermission(1,getTableByName($sub_name."file_manager")['id'],"delete",getTableByName($sub_name."file_manager")['act'],"")) || isset($op_admin) && $op_admin){
				if(isset($_GET["download"])){
					$res_download=$connection->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$_GET["download"]."'");
					if($res_download->rowCount()){
						$download=$res_download->fetch();
						$is_secure=($download['is_secure'] ? "secure_files":"files");
						$file_url = "../".$is_secure."/".$download['real_name'];
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
					$file_manager_direction_id=(getUserSetting("file_manager_direction")=="false" ? 0:getUserSetting("file_manager_direction"));
					$file_manager_direction_id=($connection->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE id='".$file_manager_direction_id."'")->rowCount() ? $file_manager_direction_id:0);
					$file_manager_copy=(getUserSetting("file_manager_copy")=="false" ? 0:getUserSetting("file_manager_copy"));
					$file_manager_cut=(getUserSetting("file_manager_cut")=="false" ? 0:getUserSetting("file_manager_cut"));
					$current_folder=$connection->query("SELECT * FROM ".$GLOBALS["sub_name"]."file_manager WHERE folder_file=0 AND id='".$file_manager_direction_id."' AND act=1")->fetch();
?>
	<div class="row">
		<div class="col-lg-3 col-md-6">
			<div class="card card-stats">
				<div class="card-body">
					<div class="row">
						<div class="col-7">
							<div class="numbers">
								<p class="card-category text-right">تعداد کاربران سایت</p>
								<h3 class="card-title text-right"><?php
									$res_site_users=$GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"] . "site_users");
									print_r($res_site_users->rowCount());
								?></h3>
							</div>
						</div>
						<div class="col-5">
							<div class="info-icon text-center icon-success mr-auto">
								<i class="far fa-user pt-2"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6">
			<div class="card card-stats">
				<div class="card-body">
					<div class="row">
						<div class="col-7">
							<div class="numbers">
								<p class="card-category text-right">تعداد ادمین های سایت</p>
								<h3 class="card-title text-right"><?php
									$res_site_users=$GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"] . "admins");
									print_r($res_site_users->rowCount());
								?></h3>
							</div>
						</div>
						<div class="col-5">
							<div class="info-icon text-center icon-success mr-auto">
								<i class="far fa-user-shield pt-2"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6">
			<div class="card card-stats">
				<div class="card-body">
					<div class="row">
						<div class="col-7">
							<div class="numbers">
								<p class="card-category text-right">پروژه ها</p>
								<h3 class="card-title text-right"><?php
									$res_site_users=$GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"] . "projects");
									print_r($res_site_users->rowCount());
								?></h3>
							</div>
						</div>
						<div class="col-5">
							<div class="info-icon text-center icon-info mr-auto">
								<i class="far fa-coins pt-2"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6">
			<div class="card card-stats">
				<div class="card-body">
					<div class="row">
						<div class="col-7">
							<div class="numbers">
								<p class="card-category text-right">نظرات پروژه ها</p>
								<h3 class="card-title text-right"><?php
									$res_site_users=$GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"] . "project_comments");
									print_r($res_site_users->rowCount());
								?></h3>
							</div>
						</div>
						<div class="col-5">
							<div class="info-icon text-center icon-info mr-auto">
								<i class="far fa-comment pt-2"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6">
			<div class="card card-stats">
				<div class="card-body">
					<div class="row">
						<div class="col-7">
							<div class="numbers">
								<p class="card-category text-right">تبلیغات</p>
								<h3 class="card-title text-right"><?php
									$res_site_users=$GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"] . "banners");
									print_r($res_site_users->rowCount());
								?></h3>
							</div>
						</div>
						<div class="col-5">
							<div class="info-icon text-center icon-info mr-auto">
								<i class="far fa-images pt-2"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6">
			<div class="card card-stats">
				<div class="card-body">
					<div class="row">
						<div class="col-7">
							<div class="numbers">
								<p class="card-category text-right">گزارشات اسکم</p>
								<h3 class="card-title text-right"><?php
									$res_site_users=$GLOBALS["connection"]->query("SELECT * FROM " . $GLOBALS["sub_name"] . "scam_reports");
									print_r($res_site_users->rowCount());
								?></h3>
							</div>
						</div>
						<div class="col-5">
							<div class="info-icon text-center icon-warning mr-auto">
								<i class="far fa-flag pt-2"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
				}
			}else{
				echo $outofpermission;
			}
		}else{
?>
	<script>
		window.location="login/";
	</script>
<?php
		}
	}else{
?>
	<script>
		window.location="setup/";
	</script>
<?php
	}
?>

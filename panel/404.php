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
			if($user_stats == 1 || isset($op_admin) && $op_admin){
?>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-12">
							<img src="<?php print_r($GLOBALS['user_language']=="en" ? "assets/img/404.png":"assets/img/404-fa.png"); ?>" data-src-fa="assets/img/404-fa.png" data-src-en="assets/img/404.png" class="img-fluid data-src" alt="Responsive image">
						</div>
					</div>
				</div>
			</div>
			<!-- end card -->
		</div>
	</div>
<?php
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
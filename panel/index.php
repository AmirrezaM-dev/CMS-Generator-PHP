<?php
	$conn_dir="../connection/connect.php";
	if(session_status() == PHP_SESSION_NONE) {
        session_start(['cookie_lifetime' => 86400]);
	}
	require_once("config.php");
	require_once("../class/jdf.php");
	require_once("setting/check_database.php");
	if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_SESSION['username']."' AND act=1");
			$user_stats=$res_user->rowCount();
			$user_info=$res_user->fetch();
			if($user_stats==1 || getSetting("op_admin")==$_SESSION['username']){
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="apple-touch-icon" sizes="76x76" href="assets/img/ico.png">
	<link rel="icon" type="image/png" href="assets/img/ico.png">
	<title>
		Welcome
	</title>
	<?php
		/* loading css Scripts */
		require_once("loading-css.php");
		/* loading css Scripts */
	?>

	<!-- jquery library -->
	<script src="assets/js/core/jquery.min.js"></script>

	<!-- custom context menu -->
	<link rel="stylesheet" href="css/basicContext.min.css">
	<link rel="stylesheet" href="css/popin.min.css">

	<!-- eng font-->
	<link href="assets/google-font.css" rel="stylesheet">

	<!-- fontawesome -->
	<?php
		if($_SERVER["REMOTE_ADDR"]=="::1" || $_SERVER["REMOTE_ADDR"]=="127.0.0.1"){
	?>
		<link href="http://localhost/local_cdn/cdn/fa/css/all.min.css" rel="stylesheet">
	<?php
		}else{
	?>
		<link href="https://cdn.technosha.com/fa/css/all.min.css" rel="stylesheet">
	<?php
		}
	?>

	<!-- Nucleo Icons -->
	<link href="assets/css/nucleo-icons.css" rel="stylesheet">

	<!-- CSS Files -->
	<link href="assets/css/black-dashboard.min.css" rel="stylesheet">
	<link href="assets/css/toggle-switch.css" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="./css/sa2-dark.css" media="all">
	<link type="text/css" rel="stylesheet" href="./assets/css/spectrum.min.css" media="all">
	<link rel="stylesheet" href="assets/css/theme/persian-datepicker-dark.min.css">

	<!-- jQuery UI -->
	<script src="js/jquery-ui.min.js"></script>

	<?php require_once("js/my-custom.php"); ?>

	<link type="text/css" rel="stylesheet" href="css/my-custom.css" media="all">
</head>
<body class="last-custom" data="primary">
	<div class="preloader">
		<div class="loading-icon">
			<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
		</div>
	</div>
	<div class="wrapper">
		<div class="navbar-minimize-fixed">
			<button class="minimize-sidebar btn btn-link btn-just-icon">
				<i class="tim-icons icon-align-center visible-on-sidebar-regular text-muted"></i>
				<i class="tim-icons icon-bullet-list-67 visible-on-sidebar-mini text-muted"></i>
			</button>
		</div>
		<div class="sidebar" data="primary" id="navbarLoader">

		</div>
		<div class="main-panel" data="primary">
			<!-- Navbar -->
			<?php require_once("navbar-2.php"); ?>
			<!-- End Navbar -->
			<div class="content" id="bodyLoader">

			</div>
			<footer class="footer">
				<?php require_once("footer-pages.php"); ?>
			</footer>
		</div>
	</div>
	<div id="scroller_btn" class="<?php if(getUserSetting('scroll-top')=="false" && getUserSetting('move-top')=="false" && getUserSetting('move-bottom')=="false" && getUserSetting('scroll-bottom')=="false"){?>hide<?php } ?>">
		<button onclick="scroller('up')" class="scroll-btn scroll-btn-top scroll-btn-color <?php if(getUserSetting('scroll-top')=="false"){?>hide<?php } ?>"><i class="fas fa-angle-double-up"></i></button>
		<button onclick="scroller('ups')" class="scroll-btn scroll-btn-up scroll-btn-color <?php if(getUserSetting('move-top')=="false"){?>hide<?php } ?>"><i class="fas fa-chevron-up"></i></button>
		<button onclick="scroller('downs')" class="scroll-btn scroll-btn-bottom scroll-btn-color <?php if(getUserSetting('move-bottom')=="false"){?>hide<?php } ?>"><i class="fas fa-chevron-down"></i></button>
		<button onclick="scroller('down')" class="scroll-btn scroll-btn-down scroll-btn-color <?php if(getUserSetting('scroll-bottom')=="false"){?>hide<?php } ?>"><i class="fas fa-angle-double-down"></i></button>
	</div>
	<script src="assets/js/core/popper.min.js"></script>
	<script src="assets/js/core/bootstrap.min.js"></script>
	<script src="assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
	<script src="assets/js/plugins/moment.min.js"></script>

	<script src="assets/js/plugins/bootstrap-tagsinput.js"></script>

	<script src="assets/js/plugins/sweetalert2.all.min.js"></script>

	<script src="assets/js/plugins/jquery.dataTables.min.js"></script>

	<script src="assets/js/plugins/bootstrap-select/js/bootstrap-select.min.js"></script>

	<script src="assets/js/plugins/bootstrap-switch.js"></script>

	<script src="assets/js/plugins/jquery.validate.min.js"></script>

	<script src="assets/js/plugins/jquery.bootstrap.wizard.min.js"></script>

	<script src="assets/js/plugins/bootstrap-notify.js"></script>

	<script src="assets/js/plugins/bootstrap-datetimepicker.js"></script>

	<script src="assets/js/plugins/nouislider.min.js"></script>

	<script src="js/polyfill.min.js"></script>

	<script src="assets/js/plugins/spectrum.min.js"></script>

	<script src="assets/ck4/ckeditor.js"></script>

	<script src="js/basicContext.min.js"></script>

	<script src="assets/js/persian-date.min.js"></script>
	<script src="assets/js/persian-datepicker.js"></script>

	<div id="script-loader"></div>
	<div id="script-appender"></div>
	<div id="create_table_scripts"></div>
</body>
</html>
<?php
			}else{
				header("location: login/");
			}
		}else{
			header("location: login/");
		}
	}else{
		header("location: setup/");
	}
?>

<?php
    if(session_status() == PHP_SESSION_NONE) {
        session_start(['cookie_lifetime' => 86400]);
    }
    if(isset($_GET['token'])){
        $token=$_GET['token'];
    }else{
        $token='';
    }
	unset($_SESSION['username']);
	session_destroy();
	require_once("../config.php");
	$conn_dir="../../connection/connect.php";
	if(file_exists($conn_dir)){
		require_once($conn_dir);
		$connection_checker=new connection();
		$connection_check=$connection_checker->checkConnection();
		if($connection_check==0){
			header("location: ../setup/");
		}else{
			$connection=$connection_checker->connect();
			$connected=1;
			if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && isset($setting_arr) && isset($need_setting) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables) && count(array_intersect($setting_arr, $need_setting))>=count($need_setting)){
?>
<!DOCTYPE html>
<html lang="fa">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="apple-touch-icon" sizes="76x76" href="../assets/img/ico.png">
	<link rel="icon" type="image/png" href="../assets/img/ico.png">
	<script src="../assets/js/core/jquery.min.js"></script>
	<title>
		Reset Password
	</title>
	<style>
		/* loading css Scripts */

		#preloader{position:absolute;opacity: 0.8;width:100%;height:100%;z-index:99999;top:0;left:0;background:linear-gradient(#1e1e2f,#1e1e24);display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center}.preloader{position:fixed;width:100%;height:100%;z-index:99999;top:0;left:0;background:linear-gradient(#1e1e2f,#1e1e24);display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center}.loading-icon{position:absolute;width:80px;height:80px;left:50%;top:50%;margin-left:-40px;margin-top:-40px}.lds-spinner{/*color:official; my-custom*/display:inline-block;position:relative;width:80px;height:80px}.lds-spinner div{transform-origin:40px 40px;animation:lds-spinner 1.2s linear infinite}.lds-spinner div:after{content:" ";display:block;position:absolute;top:3px;left:37px;width:6px;height:18px;border-radius:20%;background:#fff}.lds-spinner div:nth-child(1){transform:rotate(0);animation-delay:-1.1s}.lds-spinner div:nth-child(2){transform:rotate(30deg);animation-delay:-1s}.lds-spinner div:nth-child(3){transform:rotate(60deg);animation-delay:-.9s}.lds-spinner div:nth-child(4){transform:rotate(90deg);animation-delay:-.8s}.lds-spinner div:nth-child(5){transform:rotate(120deg);animation-delay:-.7s}.lds-spinner div:nth-child(6){transform:rotate(150deg);animation-delay:-.6s}.lds-spinner div:nth-child(7){transform:rotate(180deg);animation-delay:-.5s}.lds-spinner div:nth-child(8){transform:rotate(210deg);animation-delay:-.4s}.lds-spinner div:nth-child(9){transform:rotate(240deg);animation-delay:-.3s}.lds-spinner div:nth-child(10){transform:rotate(270deg);animation-delay:-.2s}.lds-spinner div:nth-child(11){transform:rotate(300deg);animation-delay:-.1s}.lds-spinner div:nth-child(12){transform:rotate(330deg);animation-delay:0s}@keyframes lds-spinner{0%{opacity:1}100%{opacity:0}}

		/* loading css Scripts */
	</style>
	<script>
		var messagesArr=[];
		messagesArr['forgot']=[];
		messagesArr['forgot']['success']="کلمه عبور شما به ایمیل شما ارسال شد";
		messagesArr['forgot']['failed']="ایمیل یا نام کاربری وارد شده در دسترس نیست";
        messagesArr['forgot']['8chr']="کلمه عبور نباید کمتر از 8 حرف باشد";
		messagesArr['forgot']['repass']="Password and confirm password does not match";
		$(window).on('load', function() {
			$('#preloader').fadeOut('0');
			$('.preloader').fadeOut('1000');
		});
	</script>
	<!--     Fonts and icons     -->
	<link href="../assets/google-font.css" rel="stylesheet">
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
	<link href="../assets/css/nucleo-icons.css" rel="stylesheet">
	<!-- CSS Files -->
	<link href="../assets/css/black-dashboard.min.css?v=1.1.1" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/my-custom.css" media="all">
    <link type="text/css" rel="stylesheet" href="../css/my-custom-rtl.css" media="all">
	<script>var grecaptcha_sitekey_get="<?php print_r(getSetting("grecaptcha_sitekey")); ?>";</script>
	<script src="js/login.js"></script>
	<?php if($_SERVER["REMOTE_ADDR"]!="::1" && $_SERVER["REMOTE_ADDR"]!="127.0.0.1"){?><script src="https://www.google.com/recaptcha/api.js?render=<?php print_r(getSetting("grecaptcha_sitekey")); ?>"></script><?php }?>
</head>

<body class="rtl login-page">
	<div class="preloader">
		<div class="loading-icon">
			<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
		</div>
	</div>
	<!-- Navbar -->
	<nav class="navbar navbar-expand-lg navbar-absolute navbar-transparent fixed-top">
		<div class="container-fluid">
			<div class="navbar-wrapper">
				<div class="navbar-toggle d-inline">
					<button type="button" class="navbar-toggler">
						<span class="navbar-toggler-bar bar1"></span>
						<span class="navbar-toggler-bar bar2"></span>
						<span class="navbar-toggler-bar bar3"></span>
					</button>
				</div>
				<a class="navbar-brand" href="javascript:void(0)">تنظیم مجدد کلمه عبور</a>
			</div>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-bar navbar-kebab"></span>
				<span class="navbar-toggler-bar navbar-kebab"></span>
				<span class="navbar-toggler-bar navbar-kebab"></span>
			</button>
			<div class="collapse navbar-collapse" id="navigation">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item ">
						<a href="reset.php?token=<?php if(isset($_GET['token'])){print_r($token);} ?>" class="nav-link" style="font-family: IranSans !important;">
							<i class="fad fa-language"></i> English
						</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>
	<!-- End Navbar -->
	<div class="wrapper wrapper-full-page ">
		<div class="full-page login-page ">
			<!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
			<div class="content">
				<div class="container">
					<div class="col-lg-4 col-md-6 ml-auto mr-auto">
						<div id="preloader">
							<div class="loading-icon">
								<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
							</div>
						</div>
						<form class="form" onSubmit="$('#reset-click').click();return false;">
							<div class="card card-login card-white">
								<div class="card-header">
									<img src="../assets/img/card-primary.png" alt="">
									<h1 class="card-title">بازیابی</h1>
								</div>
								<div class="card-body">
									<div id="login-div" class="should-hide">
										<input id="token" type="hidden" disabled value="<?php if(isset($_GET['token'])){print_r($token);} ?>">
                                        <div class="input-group">
											<div class="input-group-prepend">
												<div class="input-group-text">
													<i class="tim-icons icon-lock-circle"></i>
												</div>
											</div>
											<input id="password" type="password" placeholder="کلمه عبور جدید" class="form-control input-login">
                                        </div>
                                        <div class="input-group">
											<div class="input-group-prepend">
												<div class="input-group-text">
													<i class="tim-icons icon-lock-circle"></i>
												</div>
											</div>
											<input id="re-password" type="password" placeholder="تکرار کلمه عبور جدید" class="form-control input-login">
										</div>
									</div>
								</div>
								<div class="card-footer">
									<div id="login-footer" class="should-hide">
										<a style="cursor: pointer;" id="reset-click" href="javascript:void(0)" class="btn btn-primary btn-lg btn-block mb-3">ارسال</a>
										<div class="">
											<h6>
												<a href="login-fa.php" class="link footer-link">بازگشت به فرم ورود</a>
											</h6>
                                        </div>
									</div>
								</div>
							</div>
							<input type="submit" class="hide">
						</form>
					</div>
				</div>
			</div>
			<footer class="footer">
				<?php require_once("../footer-pages.php"); ?>
			</footer>
		</div>
	</div>
	<!--   Core JS Files   -->
	<script src="../assets/js/core/popper.min.js"></script>
	<script src="../assets/js/core/bootstrap.min.js"></script>
	<script src="../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
	<script src="../assets/js/plugins/moment.min.js"></script>
	<!-- Forms Validations Plugin -->
	<script src="../assets/js/plugins/jquery.validate.min.js"></script>
	<!--  Notifications Plugin    -->
	<script src="../assets/js/plugins/bootstrap-notify.js"></script>
	<!-- Control Center for Black Dashboard: parallax effects, scripts for the example pages etc -->
	<script src="../assets/js/black-dashboard-rtl.min.js"></script>
</body>

</html>
<?php
			}else{
				header("location: ../setup/");
			}
		}
	}else{
		header("location: ../setup/");
	}
?>
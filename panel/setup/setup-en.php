<?php
	$custom_lang="en";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="apple-touch-icon" sizes="76x76" href="../assets/img/ico.png">
		<link rel="icon" type="image/png" href="../assets/img/ico.png">
		<script src="../assets/js/core/jquery.min.js"></script>
		<title>
			Setup your database
		</title>
		<style>
			/* loading css Scripts */

			#preloader{position:absolute;opacity: 0.8;width:100%;height:100%;z-index:99999;top:0;left:0;background:linear-gradient(#1e1e2f,#1e1e24);display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center}.preloader{position:fixed;width:100%;height:100%;z-index:99999;top:0;left:0;background:linear-gradient(#1e1e2f,#1e1e24);display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-align:center;-ms-flex-align:center;align-items:center;-webkit-box-pack:center;-ms-flex-pack:center;justify-content:center}.loading-icon{position:absolute;width:80px;height:80px;left:50%;top:50%;margin-left:-40px;margin-top:-40px}.lds-spinner{/*color:official; my-custom*/display:inline-block;position:relative;width:80px;height:80px}.lds-spinner div{transform-origin:40px 40px;animation:lds-spinner 1.2s linear infinite}.lds-spinner div:after{content:" ";display:block;position:absolute;top:3px;left:37px;width:6px;height:18px;border-radius:20%;background:#fff}.lds-spinner div:nth-child(1){transform:rotate(0);animation-delay:-1.1s}.lds-spinner div:nth-child(2){transform:rotate(30deg);animation-delay:-1s}.lds-spinner div:nth-child(3){transform:rotate(60deg);animation-delay:-.9s}.lds-spinner div:nth-child(4){transform:rotate(90deg);animation-delay:-.8s}.lds-spinner div:nth-child(5){transform:rotate(120deg);animation-delay:-.7s}.lds-spinner div:nth-child(6){transform:rotate(150deg);animation-delay:-.6s}.lds-spinner div:nth-child(7){transform:rotate(180deg);animation-delay:-.5s}.lds-spinner div:nth-child(8){transform:rotate(210deg);animation-delay:-.4s}.lds-spinner div:nth-child(9){transform:rotate(240deg);animation-delay:-.3s}.lds-spinner div:nth-child(10){transform:rotate(270deg);animation-delay:-.2s}.lds-spinner div:nth-child(11){transform:rotate(300deg);animation-delay:-.1s}.lds-spinner div:nth-child(12){transform:rotate(330deg);animation-delay:0s}@keyframes lds-spinner{0%{opacity:1}100%{opacity:0}}

			/* loading css Scripts */
		</style>
		<script>
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
		<?php require_once("js/setup.php"); ?>
	</head>

	<body class="register-page">
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
					<a class="navbar-brand" href="javascript:void(0)">Database setup</a>
				</div>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-bar navbar-kebab"></span>
					<span class="navbar-toggler-bar navbar-kebab"></span>
					<span class="navbar-toggler-bar navbar-kebab"></span>
				</button>
				<div class="collapse navbar-collapse" id="navigation">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item ">
							<a href="setup-fa.php" style="font-family: IranSans; " class="nav-link">
								<i class="fad fa-language"></i> فارسی
							</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="modal modal-search fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModal" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<input type="text" class="form-control" id="inlineFormInputGroup" placeholder="SEARCH">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<i class="tim-icons icon-simple-remove"></i>
						</button>
					</div>
				</div>
			</div>
		</div>
		<!-- End Navbar -->
		<div class="wrapper wrapper-full-page ">
			<div class="full-page register-page">
				<div class="content">
					<div class="container">
						<div class="row">
							<div class="col-md-5 ml-auto">
								<div class="info-area info-horizontal mt-5">
									<div class="icon icon-warning">
										<i class="fad fa-users-cog"></i>
									</div>
									<div class="description">
										<h3 class="info-title">Users</h3>
										<p class="description">
											Create a user for database if you dont have and put the username and password of user on the fields.
										</p>
									</div>
								</div>
								<div class="info-area info-horizontal">
									<div class="icon icon-primary">
										<i class="fad fa-database"></i>
									</div>
									<div class="description">
										<h3 class="info-title">Database</h3>
										<p class="description">
											Create new database if you dont have and put the name of database on the filed.
										</p>
									</div>
								</div>
								<div class="info-area info-horizontal">
									<div class="icon icon-info">
										<i class="fad fa-cogs"></i>
									</div>
									<div class="description">
										<h3 class="info-title">Configuration</h3>
										<p class="description">
											Make sure added the user to the database.
										</p>
									</div>
								</div>
								<div class="info-area info-horizontal">
									<div class="icon icon-danger">
										<i class="fad fa-exclamation-triangle"></i>
									</div>
									<div class="description">
										<h3 class="info-title">Warning</h3>
										<p class="description">
											Its might delete everything if you already had database.
										</p>
									</div>
								</div>
							</div>
							<div class="col-md-7 mr-auto">
								<div id="preloader">
									<div class="loading-icon">
										<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
									</div>
								</div>
								<div class="card card-register card-white">
									<div class="card-header">
										<img class="card-img" src="../assets/img/card-primary.png" alt="Card image">
										<h4 class="card-title">Setup</h4>
									</div>
									<div class="card-body">
										<form class="form" onSubmit="startProgress('en'); return false;">
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fad fa-database"></i>
													</div>
												</div>
												<input id="server-name" type="text" class="form-control input-setup" placeholder="Server Address">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fal fa-table"></i>
													</div>
												</div>
												<input id="table-name" type="text" class="form-control input-setup" placeholder="Table Name">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fad fa-user"></i>
													</div>
												</div>
												<input id="username" type="text" placeholder="Username" class="form-control input-setup">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="tim-icons icon-lock-circle"></i>
													</div>
												</div>
												<input id="password" type="password" class="form-control input-setup" placeholder="Password">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-mail-bulk"></i>
													</div>
												</div>
												<input id="host-email" type="text" class="form-control input-setup" placeholder="Email Host">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-user-friends"></i>
													</div>
												</div>
												<input id="username-email" type="text" class="form-control input-setup" placeholder="Email Username">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="tim-icons icon-lock-circle"></i>
													</div>
												</div>
												<input id="password-email" type="password" class="form-control input-setup" placeholder="Email Password">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-project-diagram"></i>
													</div>
												</div>
												<input id="port-email" type="text" class="form-control input-setup" placeholder="Email Port default:587">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-id-badge"></i>
													</div>
												</div>
												<input id="sender-name-email-en" type="text" class="form-control input-setup" placeholder="Sender name for email" dir="ltr">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-id-badge"></i>
													</div>
												</div>
												<input id="sender-name-email-fa" type="text" class="form-control input-setup" placeholder="نام ارسال کننده ایمیل" dir="rtl" style="font-family: IRANSans;">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-id-badge"></i>
													</div>
												</div>
												<input id="site-name-en" type="text" class="form-control input-setup" placeholder="Site name" dir="ltr">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-id-badge"></i>
													</div>
												</div>
												<input id="site-name-fa" type="text" class="form-control input-setup" placeholder="نام سایت" dir="rtl" style="font-family: IRANSans;">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-id-badge"></i>
													</div>
												</div>
												<input id="site-mini-name-en" type="text" class="form-control input-setup" placeholder="Site mini name" dir="ltr">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-id-badge"></i>
													</div>
												</div>
												<input id="site-mini-name-fa" type="text" class="form-control input-setup" placeholder="نام کوچک سایت" dir="rtl" style="font-family: IRANSans;">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-at"></i>
													</div>
												</div>
												<input id="panel-email" type="text" class="form-control input-setup" placeholder="Your Email">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fad fa-user-cog"></i>
													</div>
												</div>
												<input id="panel-username" type="text" class="form-control input-setup" placeholder="License Username">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="tim-icons icon-lock-circle"></i>
													</div>
												</div>
												<input id="panel-password" type="password" class="form-control input-setup" placeholder="License Password">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-user-secret"></i>
													</div>
												</div>
												<input id="grecaptcha_sitekey" type="text" class="form-control input-setup" placeholder="grecaptcha Site-Key">
											</div>
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">
														<i class="fas fa-user-secret"></i>
													</div>
												</div>
												<input id="grecaptcha_secretkey" type="text" class="form-control input-setup" placeholder="grecaptcha Secret-Key">
											</div>
											<input type="submit" class="hide submiter">
										</form>
									</div>
									<div class="card-footer">
										<a href="javascript:void(0)" onclick="startProgress('en');" class="btn btn-primary btn-round btn-lg submiter">Finish</a>
									</div>
								</div>
							</div>
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
		<script src="../assets/js/black-dashboard.min.js?v=1.1.1"></script>
	</body>
</html>
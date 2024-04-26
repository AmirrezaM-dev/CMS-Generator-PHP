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
			if($user_stats == 1 && checkPermission(1,getTableByName($sub_name."file_manager")['id'],"-1",getTableByName($sub_name."file_manager")['act'],"")==1 || isset($op_admin) && $op_admin){
?>
	<div class="row">
		<div class="col-12">
			<div class="card text-center">
				<div class="card-header">

				</div>
				<div class="card-body col-6 ml-auto mr-auto">
					<h1 class="card-title data-text last-version-update" data-text-en="Lastest version is : <i class='fad fa-spinner-third fa-spin'></i>" data-text-fa="آخرین نسخه : <i class='fad fa-spinner-third fa-spin'></i>"><?php print_r($GLOBALS['user_language']=="en" ? "Lastest version is : <i class='fad fa-spinner-third fa-spin'></i>":"آخرین نسخه : <i class='fad fa-spinner-third fa-spin'></i>"); ?></h1>
					<h3 class="card-category data-text" data-text-en="Current version is : <?php print_r(getSetting($sub_name."version")); ?>" data-text-fa="نسخه فعلی : <?php print_r(getSetting($sub_name."version")); ?>"><?php print_r($GLOBALS['user_language']=="en" ? "Current version is : ".getSetting($sub_name."version"):"نسخه فعلی : ".getSetting($sub_name."version")); ?></h3>
					<p class="card-description">
						<div class="form-group">
							<input id="license-username" type="email" class="form-control disabled data-placeholder" data-placeholder-en="License username" data-placeholder-fa="نام کاربری مجوز" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "License username":"نام کاربری مجوز"); ?>" disabled>
						</div>
						<div class="form-group">
							<input id="license-password" type="password" class="form-control disabled data-placeholder" data-placeholder-en="License password" data-placeholder-fa="کلمه عبور مجوز" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "License password":"کلمه عبور مجوز"); ?>" disabled>
						</div>
					</p>
				</div>
				<hr>
				<div class="card-footer">
					<button class="btn btn-success disabled data-text update-version-button" data-text-en="Update" data-text-fa="بروزرسانی" disabled onclick="adminUpdator();"><?php print_r($GLOBALS['user_language']=="en" ? "Update":"بروزرسانی"); ?></button>
				</div>
			</div>
		</div>
	</div>
	<script>
		var $update_version="0",$current_update_version=<?php print_r(getSetting($sub_name."version")); ?>;
		$(document).ready(function () {
			$.post("<?php print_r($_SERVER["REMOTE_ADDR"]=="::1" || $_SERVER["REMOTE_ADDR"]=="127.0.0.1" ? "http://localhost/local_cdn/licenses/update.php?version":"https://licenses.technosha.com/update.php?version"); ?>", {}, function (data, status) {
				if(status == "success") {
					data_detector = data.split("_-...-_");
					if(data_detector[0] == "success") {
						$update_version = data_detector[1];
						$(".last-version-update").text((language=="en" ? "Lastest version is : "+$update_version : "آخرین نسخه : "+$update_version));
						$(".last-version-update").attr("data-text-en","Lastest version is : "+$update_version);
						$(".last-version-update").attr("data-text-fa","آخرین نسخه : "+$update_version);
						if($current_update_version<$update_version){
							$("#license-username").removeClass("disabled").removeAttr("disabled");
							$("#license-password").removeClass("disabled").removeAttr("disabled");
							$(".update-version-button").removeClass("disabled").removeAttr("disabled");
						}else{
							$("#license-username").addClass("disabled").attr("disabled",true);
							$("#license-password").addClass("disabled").attr("disabled",true);
							$(".update-version-button").addClass("disabled").attr("disabled",true);
						}
					} else {
						$update_version=-1;
						feedbackOperations(data);
					}
				} else {
					$update_version=-1;
					feedbackOperations(data);
				}
			});
		});
		function adminUpdator(){
			if($("#license-username").val().length && $("#license-password").val().length){
				var $percentage=0,updateInterval=setInterval(() => {
					$percentage++;
					$(".swal2-content-update-progress-bar").css('width',$percentage+"%").attr("aria-valuenow" , $percentage);
					if($percentage>=10){
						clearInterval(updateInterval);
						updateInterval=setInterval(() => {
							$percentage++;
							$(".swal2-content-update-progress-bar").css('width',$percentage+"%").attr("aria-valuenow" , $percentage);
							if($percentage>=30){
								clearInterval(updateInterval);
								updateInterval=setInterval(() => {
									$percentage++;
									$(".swal2-content-update-progress-bar").css('width',$percentage+"%").attr("aria-valuenow" , $percentage);
									if($percentage>=50){
										clearInterval(updateInterval);
										updateInterval=setInterval(() => {
											$percentage++;
											$(".swal2-content-update-progress-bar").css('width',$percentage+"%").attr("aria-valuenow" , $percentage);
											if($percentage>=70){
												clearInterval(updateInterval);
												updateInterval=setInterval(() => {
													$percentage++;
													$(".swal2-content-update-progress-bar").css('width',$percentage+"%").attr("aria-valuenow" , $percentage);
													if($percentage>=90){
														clearInterval(updateInterval);
													}
												}, 5000);
											}
										}, 2000);
									}
								}, 1000);
							}
						}, 700);
					}
				}, 500);
				Swal.fire({
					title: (language=="en" ? "Updating":"بروزرسانی"),
					html:
						'<div class="swal2-content-update-progress progress">'+
							'<div class="swal2-content-update-progress-bar progress-bar bg-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>'+
						'</div>',
					focusConfirm: false,
					showConfirmButton: false,
					showCancelButton: false,
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
						var update_data = "";
						$.post("<?php print_r($_SERVER["REMOTE_ADDR"]=="::1" || $_SERVER["REMOTE_ADDR"]=="127.0.0.1" ? "http://localhost/local_cdn/licenses/update.php":"https://licenses.technosha.com/update.php"); ?>", {
							username: $("#license-username").val(),
							password: $("#license-password").val()
						}, function (data, status) {
							if(status == "success") {
								data_detector = data.split("_-...-_");
								if(data_detector[0] == "success") {
									update_data = data_detector[1];
								} else {
									feedbackOperations(data);
								}
							} else {
								feedbackOperations(data);
							}
						}).always(function () {
							if(update_data!=""){
								$.post("update/update_helper.php", {
									update_data: update_data
								}, function (data, status) {
									if(status == "success" && data.indexOf("success")>=0) {
										$.post("update/updater.php", {}, function (data, status) {
											if(status == "success" && data.indexOf("success")>=0) {
												$percentage=100;
												$(".swal2-content-update-progress-bar").css('width',$percentage+"%").attr("aria-valuenow" , $percentage).addClass("bg-success");
												swal.hideLoading();
												setTimeout(() => {
													$(".swal2-content-update-progress-bar").removeClass("bg-info");
													setTimeout(() => {
														window.location.reload();
													}, 500);
												}, 500);
											} else {
												clearInterval(updateInterval);
												$(".swal2-content-update-progress-bar").css('width',$percentage+"%").attr("aria-valuenow" , $percentage).addClass("bg-danger");
												swal.hideLoading();
												setTimeout(() => {
													$(".swal2-content-update-progress-bar").removeClass("bg-info");
													setTimeout(() => {
														$(".swal2-content-update-progress").addClass("hide");
														Swal.showValidationMessage((language=="en" ? "Something went wrong":"مشکلی پیش آمده"));
														setTimeout(() => {
															swal.close();
														}, 1500);
													}, 500);
												}, 500);
												feedbackOperations(data);
											}
										});
									} else {
										clearInterval(updateInterval);
										$(".swal2-content-update-progress-bar").css('width',$percentage+"%").attr("aria-valuenow" , $percentage).addClass("bg-danger");
										swal.hideLoading();
										setTimeout(() => {
											$(".swal2-content-update-progress-bar").removeClass("bg-info");
											setTimeout(() => {
												$(".swal2-content-update-progress").addClass("hide");
												Swal.showValidationMessage((language=="en" ? "Something went wrong":"مشکلی پیش آمده"));
												setTimeout(() => {
													swal.close();
												}, 1500);
											}, 500);
										}, 500);
										feedbackOperations(data);
									}
								});
							}else{
								clearInterval(updateInterval);
								$(".swal2-content-update-progress-bar").css('width',$percentage+"%").attr("aria-valuenow" , $percentage).addClass("bg-danger");
								swal.hideLoading();
								setTimeout(() => {
									$(".swal2-content-update-progress-bar").removeClass("bg-info");
									setTimeout(() => {
										$(".swal2-content-update-progress").addClass("hide");
										Swal.showValidationMessage((language=="en" ? "Unable to update":"امکان بروزرسانی وجود ندارد"));
										setTimeout(() => {
											swal.close();
										}, 1500);
									}, 500);
								}, 500);
							}
						});
					}
				});
			}else{
				$("#license-username").parent().addClass($("#license-username").val().length ? "":"has-danger");
				$("#license-password").parent().addClass($("#license-password").val().length ? "":"has-danger");
			}
		}
	</script>
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

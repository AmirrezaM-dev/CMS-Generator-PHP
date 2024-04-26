// JavaScript Document
$(document).on('click', '#reset-click', function () {
	$(".input-group").removeClass("has-danger has-success")
	$("#messanger-messanger").remove();
	var error_detector = 0;
	$.when($(".input-login").blur()).done(function () {
		$("#preloader").fadeIn("slow", function () {
			if($("#password").val() == "") {
				$("#password").parent().addClass('has-danger');
				$("#password").focus();
				if($("#re-password").val() == "") {
					$("#re-password").parent().addClass('has-danger');
					error_detector = 1;
				}
				$('#preloader').fadeOut('slow');
				error_detector = 1;
			}else{
				if($("#password").val().length>=8){
					if($("#re-password").val() == "") {
						$("#re-password").parent().addClass('has-danger');
						$("#re-password").focus();
						$('#preloader').fadeOut('slow');
						error_detector = 1;
					} else {
						if($("#password").val() == $("#re-password").val()) {
							if ($("#reset-click").hasClass("en")) {
								langs = "en";
							} else if ($("#reset-click").hasClass("fa")) {
								langs = "fa";
							} else {
								langs = "en";
							}
							grecaptcha.ready(function () {
								grecaptcha.execute(grecaptcha_sitekey_get, {
									action: 'homepage'
								}).then(function (token) {
									$.post("class/login.php?reset", {
										password: $("#password").val(),
										re_password: $("#re-password").val(),
										token: token,
										re_token: $("#token").val(),
										lang: langs
									}, function (data, status) {
										console.log(data);
										if(status == "success") {
											if(data == 'success') {
												$("#password").parent().removeClass('has-danger').addClass('has-success');
												$("#re-password").parent().removeClass('has-danger').addClass('has-success');
											} else {
												var toDo = data.split("_._");
												switch (toDo[0]) {
													case "redirect":
														window.location = toDo[1];
													break;
													case "message":
														$("#password").parent().addClass('has-danger');
														$("#re-password").parent().addClass('has-danger');
														$.when($(".card-footer").append("<div id='messanger-messanger' class='alert alert-warning'><span>" + toDo[1] + "</span><button class='close'><i class='far fa-exclamation-triangle'></i></button></div>")).done(function () {});
														$('#preloader').fadeOut('slow');
													break;
												}
												error_detector = 1;
												$("#password").parent().addClass('has-danger');
												$("#re-password").parent().addClass('has-danger');
											}
										} else {
											error_detector = 1;
											$("#password").parent().addClass('has-danger');
											$("#re-password").parent().addClass('has-danger');
										}
									}).always(function (response) {
										if(error_detector == 0) {
											window.location = "../";
											$("#preloader").fadeOut("slow");
										} else {
											$("#preloader").fadeOut("slow");
										}
									});
								});
							});
						} else {
							$("#password").parent().addClass('has-danger');
							$("#re-password").parent().addClass('has-danger');
							$("#re-password").focus();
							$.when($(".card-footer").append("<div id='messanger-messanger' class='alert alert-warning'><span>" + messagesArr['forgot']['repass'] + "</span><button class='close'><i class='far fa-exclamation-triangle'></i></button></div>")).done(function () {});
							$('#preloader').fadeOut('slow');
						}
					}
				}else{
					$("#password").parent().addClass('has-danger');
					$("#re-password").parent().addClass('has-danger');
					$("#password").focus();
					$.when($(".card-footer").append("<div id='messanger-messanger' class='alert alert-warning'><span>"+messagesArr['forgot']['8chr']+"</span><button class='close'><i class='far fa-exclamation-triangle'></i></button></div>")).done(function () {});
					$('#preloader').fadeOut('slow');
				}
			}
		});
	});
});
$(document).on('click', '#login-click',function(){
	$(".input-group").removeClass("has-danger has-success")
	$("#messanger-messanger").remove();
	var error_detector=0;
	$.when($(".input-login").blur()).done(function(){
		$("#preloader").fadeIn("slow",function(){
			if(login_mode==1){
				if($("#username").val()==""){
					$("#username").parent().addClass('has-danger');
					$("#username").focus();
					$('#preloader').fadeOut('slow');
					error_detector=1;
				}else{
					$("#username").parent().removeClass('has-danger');
					
					if($("#password").val()==""){
						$("#password").parent().addClass('has-danger');
						$("#password").focus();
						$('#preloader').fadeOut('slow');
						error_detector=1;
					}else{
						$("#password").parent().removeClass('has-danger');
						grecaptcha.ready(function() {
							grecaptcha.execute(grecaptcha_sitekey_get, {
									action: 'homepage'
								}).then(function (token) {
								$.post("class/login.php?login",{
									username: $("#username").val(),
									password: $("#password").val(),
									token: token
								},function(data, status){
									if(status=="success"){
										if(data=='success'){
											$("#username").parent().removeClass('has-danger').addClass('has-success');
											$("#password").parent().removeClass('has-danger').addClass('has-success');
										}else{
											var toDo=data.split("_._");
											switch(toDo[0]){
												case "redirect":
													window.location=toDo[1];
												break;
											}
											error_detector=1;
											$("#username").parent().addClass('has-danger');
											$("#password").parent().addClass('has-danger');
										}
									}else{
										error_detector=1;
										$("#username").parent().addClass('has-danger');
										$("#password").parent().addClass('has-danger');
									}
								}).always(function(response) {
									if(error_detector==0){
										window.location="../";
										$("#preloader").fadeOut("slow");
									}else{
										$("#preloader").fadeOut("slow");
									}
								});
							});
						});
					}
				}
			}else{
				if($("#forgot").val() == "") {
					$("#forgot").parent().addClass('has-danger');
					$("#forgot").focus();
					$('#preloader').fadeOut('slow');
					error_detector = 1;
				} else {
					$("#forgot").parent().removeClass('has-danger');
					if ($("#login-click").hasClass("en")){
						langs="en";
					}else if($("#login-click").hasClass("fa")){
						langs = "fa";
					}else{
						langs="en";
					}
					grecaptcha.ready(function () {
						grecaptcha.execute(grecaptcha_sitekey_get, {
							action: 'homepage'
						}).then(function (token) {
							$.post("class/login.php?forgot", {
								forgot: $("#forgot").val(),
								token: token,
								lang: langs
							}, function (data, status) {
								if(status == "success") {
									if(data == 'success') {
										$("#forgot").parent().removeClass('has-danger').addClass('has-success');
									} else {
										var toDo = data.split("_._");
										switch (toDo[0]) {
											case "redirect":
												window.location = toDo[1];
											break;
											case "inv_data":
												if(messagesArr['forgot']['failed'] != "" && messagesArr['forgot']['failed'] != undefined && messagesArr['forgot']['failed'] != null && messagesArr['forgot']['failed'] != "undefined" && messagesArr['forgot']['failed'] != "null") {
													$.when($(".card-footer").append("<div id='messanger-messanger' class='alert alert-danger'><span>" + messagesArr['forgot']['failed'] + "</span><button class='close'><i class='far fa-exclamation-triangle'></i></button></div>")).done(function () {});
												}
											break;
										}
										error_detector = 1;
										$("#forgot").parent().addClass('has-danger');
									}
								} else {
									error_detector = 1;
									$("#forgot").parent().addClass('has-danger');
								}
							}).always(function (response) {
								if(error_detector == 0) {
									if(messagesArr['forgot']['success'] != "" && messagesArr['forgot']['success'] != undefined && messagesArr['forgot']['success'] != null && messagesArr['forgot']['success'] != "undefined" && messagesArr['forgot']['success'] != "null"){
										$.when($(".card-footer").append("<div id='messanger-messanger' class='alert alert-success'><span>"+messagesArr['forgot']['success']+"</span><button class='close'><i class='far fa-thumbs-up'></i></button></div>")).done(function () {});
									}
									$("#preloader").fadeOut("slow");
								} else {
									$("#preloader").fadeOut("slow");
								}
							});
						});
					});
				}
			}
		});
	});
});
var login_mode=1;
$(document).on('change', '.input-login', function () {
	$(this).parent().removeClass('has-danger');
});
$(document).on('click', '#help-click',function(){
	$(".should-hide").addClass("hide");
	$("#help-div").removeClass("hide");
});

$(document).on('click', '#back-login-click',function(){
	login_mode=1;
	$(".should-hide").addClass("hide");
	$("#login-div").removeClass("hide");
	$("#login-footer").removeClass("hide");
});

$(document).on('click', '#forgot-click', function () {
	login_mode=2;
	$(".should-hide").addClass("hide");
	$("#forgot-div").removeClass("hide");
	$("#login-footer").removeClass("hide");
});
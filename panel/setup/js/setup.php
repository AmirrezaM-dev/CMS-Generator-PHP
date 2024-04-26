<?php
    require_once("../config.php");
?>
<script>
    var timeOutLimit = 120,
        timeOutDone = 0,
        process = 0,
        saved_process = 0,
        timeOutVar = '',
        fieldsName = [
            '#server-name',
            '#table-name',
            '#username',
            '#password',
            '#host-email',
            '#username-email',
            '#password-email',
            '#port-email',
            '#sender-name-email-en',
            '#site-name-en',
            '#site-mini-name-en',
            '#sender-name-email-fa',
            '#site-name-fa',
            '#site-mini-name-fa',
            '#panel-email',
            '#panel-username',
            '#panel-password',
            '#grecaptcha_sitekey',
            '#grecaptcha_secretkey'
        ];
    window.onerror = function (errorMsg, url, lineNumber, column, errorObj) {
        grecaptcha_stats = 0;
        if(errorMsg.indexOf('Invalid site key') != -1) {
            $("#grecaptcha_sitekey").parent().addClass('has-danger');
        } else if(errorMsg.indexOf('Invalid reCAPTCHA') != -1) {
            $("#grecaptcha_sitekey").parent().addClass('has-danger');
        }
        clearInterval(timeOutVar);
        $(".submiter").show().attr("disabled", false);
        $(".input-setup").attr("disabled", false);
        $('#preloader').fadeOut('slow');
    }

    function showMessage(per_message, eng_message, type_message, icon, lange) {
        clearInterval(timeOutVar);
        $.when($("#messanger-messanger").fadeOut("slow")).done(function () {
            $.when($("#messanger-messanger").remove()).done(function () {
                if(lange == 'en') {
                    var messageYet = eng_message;
                } else {
                    var messageYet = per_message;
                }
                if(icon != "") {
                    iconer = "<i class='" + icon + "'></i>";
                } else {
                    iconer = "";
                }
                $.when($(".card-footer").append("<div id='messanger-messanger' class='alert alert-" + type_message + " hide'><span>" + messageYet + "</span><button class='close'>" + iconer + "</button></div>")).done(function () {
                    $.when($("#messanger-messanger").fadeOut('slow')).done(function () {
                        $.when($("#messanger-messanger").removeClass("hide").css("display", "none")).done(function () {
                            $.when($("#messanger-messanger").fadeIn("slow")).done(function () {

                            });
                        });
                    });
                });
            });
        });
    }

    function reCheckAllFields() {
        clearInterval(timeOutVar);
        for (iFieldsName = 0; iFieldsName < fieldsName.length; iFieldsName++) {
            // empty value
            //if($(fieldsName[iFieldsName]).val() == "") {
            // empty value
                $(fieldsName[iFieldsName]).parent().addClass('has-danger');
            // empty value
            //}
            // empty value
        }
        $(".submiter").show().attr("disabled", false);
        $(".input-setup").attr("disabled", false);
        $('#preloader').fadeOut('slow');
    }

    function startProgress(lange) {
        timeOutLimit = 120;
        timeOutDone = 0;
        process = 0;
        saved_process = 0;
        timeOutVar = setInterval(function(){
            if(process <= saved_process) {
                process++;
            } else if((process + 2) >= saved_process) {
                timeOutDone++;
            }
            if(timeOutLimit == timeOutDone) {
                if(lange == 'en') {
                    var messageYet = 'Installation failed. Make sure everything filled correctly ! Check google reCaptcha keys first !!';
                } else {
                    var messageYet = 'نصب انجام نشد. اطمینان حاصل کنید که همه چیز به درستی پر شده است ! ابتدا کلیدهای Google reCaptcha را بررسی کنید !!';
                }
                $.when($(".card-footer").append("<div id='messanger-messanger' class='alert alert-warning hide'><span>" + messageYet + "</span><button class='close'><i class='far fa-exclamation-triangle'></i></button></div>")).done(function () {
                    $.when($("#messanger-messanger").fadeOut('slow')).done(function () {
                        $.when($("#messanger-messanger").removeClass("hide").css("display", "none")).done(function () {
                            $.when($("#messanger-messanger").fadeIn("slow")).done(function () {

                            });
                        });
                    });
                });
                $("html, body").animate({
                    scrollTop: $(document).height()
                }, "slow");
                $(".submiter").show().attr("disabled", false);
                $(".input-setup").attr("disabled", false);
                $('#preloader').fadeOut('slow');
                clearInterval(timeOutVar);
                timeOutVar = null;
            }
        }, 1000);
        var license_url = "",
            license_data = "";
        $.when($(".input-setup").blur()).done(function () {
            var error_detector = 0;
            $(".submiter").hide().attr("disabled", true);
            $.when($('#preloader').fadeIn('slow')).done(function () {
                for (iFieldsNames = 0; iFieldsNames < fieldsName.length; iFieldsNames++) {
                    if(error_detector == 0) {
                        // empty value
                        // if($(fieldsName[iFieldsNames]).val() == "") {
                        //     $(fieldsName[iFieldsNames]).parent().addClass('has-danger');
                        //     $(fieldsName[iFieldsNames]).focus();
                        //     error_detector = 1;
                        //     clearInterval(timeOutVar);

                        //     reCheckAllFields();
                        // } else {
                        // empty value
                            if(iFieldsNames == (fieldsName.length - 1)) {
                                <?php
                                    if($_SERVER["REMOTE_ADDR"]!="::1" && $_SERVER["REMOTE_ADDR"]!="127.0.0.1"){
                                ?>
                                $.getScript("https://www.google.com/recaptcha/api.js?render=" + $("#grecaptcha_sitekey").val(), function () {
                                    saved_process++;
                                    setTimeout(function(){
                                        grecaptcha.ready(function () {
                                            grecaptcha.execute($("#grecaptcha_sitekey").val(), {
                                                action: 'homepage'
                                            }).then(function (token) {
                                                $.post("class/action.php?grecaptcha", {
                                                    grecaptcha_sitekey: $("#grecaptcha_sitekey").val(),
                                                    grecaptcha_secretkey: $("#grecaptcha_secretkey").val(),
                                                    grecaptcha_token: token,
                                                }, function (data, status) {
                                                    saved_process++;
                                                    if(status == "success") {
                                                        if(data == 'success') {
                                                            $("#grecaptcha_sitekey").parent().removeClass('has-danger').addClass('has-success');
                                                            $("#grecaptcha_secretkey").parent().removeClass('has-danger').addClass('has-success');
                                                        } else {
                                                            error_detector = data;

                                                            $("#grecaptcha_secretkey").parent().addClass('has-danger');
                                                        }
                                                    } else {
                                                        error_detector = 1;
                                                        clearInterval(timeOutVar);

                                                        $("#grecaptcha_secretkey").parent().addClass('has-danger');
                                                    }
                                                }).always(function () {
                                <?php
                                    }
                                ?>
                                                    if(error_detector == 0) {
                                                        // empty value
                                                        //if($("#panel-username").val() != "" && $("#panel-password").val() != "") {
                                                        // empty value
                                                            $.post("<?php print_r($_SERVER["REMOTE_ADDR"]=="::1" || $_SERVER["REMOTE_ADDR"]=="127.0.0.1" ? "http://localhost/local_cdn/licenses/get/":"https://licenses.technosha.com/get/"); ?>", {
                                                                username: $("#panel-username").val(),
                                                                password: $("#panel-password").val()
                                                            }, function (data, status) {
                                                                saved_process++;
                                                                if(status == "success") {
                                                                    data_detector = data.split("_._");
                                                                    if(data_detector[0] == "success") {
                                                                        license_url = data_detector[1];
                                                                    } else {
                                                                        error_detector = data;
                                                                    }
                                                                } else {
                                                                    error_detector = 1;
                                                                    clearInterval(timeOutVar);
                                                                }
                                                            }).always(function () {
                                                                if(license_url != "" && error_detector == 0) {
                                                                    if(error_detector == 0) {
                                                                        if(error_detector == 0) {
                                                                            $.post("class/action.php?connect_database", {
                                                                                server_name: $("#server-name").val(),
                                                                                table_name: $("#table-name").val(),
                                                                                username: $("#username").val(),
                                                                                password: $("#password").val()
                                                                            }, function (data, status) {
                                                                                saved_process++;
                                                                                if(status == "success") {
                                                                                    if(data == 'success') {
                                                                                        $("#server-name").parent().removeClass('has-danger').addClass('has-success');
                                                                                        $("#table-name").parent().removeClass('has-danger').addClass('has-success');
                                                                                        $("#username").parent().removeClass('has-danger').addClass('has-success');
                                                                                        $("#password").parent().removeClass('has-danger').addClass('has-success');
                                                                                    } else {
                                                                                        error_detector = data;
                                                                                        $("#server-name").parent().addClass('has-danger');
                                                                                        $("#table-name").parent().addClass('has-danger');
                                                                                        $("#username").parent().addClass('has-danger');
                                                                                        $("#password").parent().addClass('has-danger');
                                                                                        $("#server-name").focus();
                                                                                    }
                                                                                } else {
                                                                                    error_detector = 1;
                                                                                    clearInterval(timeOutVar);
                                                                                    $("#server-name").parent().addClass('has-danger');
                                                                                    $("#table-name").parent().addClass('has-danger');
                                                                                    $("#username").parent().addClass('has-danger');
                                                                                    $("#password").parent().addClass('has-danger');
                                                                                    $("#server-name").focus();
                                                                                }
                                                                            }).always(function () {
                                                                                if(error_detector == 0) {
                                                                                    $.post(license_url, {
                                                                                        username: $("#panel-username").val(),
                                                                                        password: $("#panel-password").val(),
                                                                                        email: $("#panel-email").val(),
                                                                                        server_name: $("#server-name").val(),
                                                                                        table_name: $("#table-name").val(),
                                                                                        username_data: $("#username").val(),
                                                                                        password_data: $("#password").val(),
                                                                                        host_email: $("#host-email").val(),
                                                                                        username_email: $("#username-email").val(),
                                                                                        password_email: $("#password-email").val(),
                                                                                        port_email: $("#port-email").val()
                                                                                    }, function (data, status) {
                                                                                        saved_process++;
                                                                                        if(status == "success") {
                                                                                            data_detector = data.split("_._");
                                                                                            if(data_detector[0] == "success") {
                                                                                                license_data = data_detector[1];
                                                                                            } else {
                                                                                                error_detector = data;
                                                                                            }
                                                                                        } else {
                                                                                            error_detector = 1;
                                                                                            clearInterval(timeOutVar);
                                                                                        }
                                                                                    }).always(function () {
                                                                                        $.post("class/action.php?verify_email", {
                                                                                            host_email: $("#host-email").val(),
                                                                                            username_email: $("#username-email").val(),
                                                                                            password_email: $("#password-email").val(),
                                                                                            port_email: $("#port-email").val(),
                                                                                            <?php print_r($sub_name); ?>email : $("#panel-email").val(),
                                                                                            sender_name_email_en : $("#sender-name-email-en").val(),
                                                                                            sender_name_email_fa: $("#sender-name-email-fa").val()
                                                                                        }, function (data, status) {
                                                                                            saved_process++;
                                                                                            if(status == "success") {
                                                                                                if(data == 'success') {
                                                                                                    $("#host-email").parent().removeClass('has-danger').addClass('has-success');
                                                                                                    $("#username-email").parent().removeClass('has-danger').addClass('has-success');
                                                                                                    $("#password-email").parent().removeClass('has-danger').addClass('has-success');
                                                                                                    $("#port-email").parent().removeClass('has-danger').addClass('has-success');
                                                                                                    $("#sender-name-email-en").parent().removeClass('has-danger').addClass('has-success');
                                                                                                    $("#sender-name-email-fa").parent().removeClass('has-danger').addClass('has-success');
                                                                                                    $("#panel-email").parent().removeClass('has-danger').addClass('has-success');
                                                                                                } else {
                                                                                                    error_detector = data;
                                                                                                    $("#host-email").parent().addClass('has-danger');
                                                                                                    $("#username-email").parent().addClass('has-danger');
                                                                                                    $("#password-email").parent().addClass('has-danger');
                                                                                                    $("#port-email").parent().addClass('has-danger');
                                                                                                    $("#sender-name-email-en").parent().addClass('has-danger');
                                                                                                    $("#sender-name-email-fa").parent().addClass('has-danger');
                                                                                                    $("#panel-email").parent().addClass('has-danger');
                                                                                                    $("#host-email").focus();
                                                                                                }
                                                                                            } else {
                                                                                                error_detector = 1;
                                                                                                clearInterval(timeOutVar);
                                                                                                $("#host-email").parent().addClass('has-danger');
                                                                                                $("#username-email").parent().addClass('has-danger');
                                                                                                $("#password-email").parent().addClass('has-danger');
                                                                                                $("#port-email").parent().addClass('has-danger');
                                                                                                $("#sender-name-email-en").parent().addClass('has-danger');
                                                                                                $("#sender-name-email-fa").parent().addClass('has-danger');
                                                                                                $("#panel-email").parent().addClass('has-danger');
                                                                                                $("#host-email").focus();
                                                                                            }
                                                                                        }).always(function () {
                                                                                            $('#preloader').fadeOut('slow');
                                                                                            if(error_detector == 0) {
                                                                                                $(".submiter").hide().attr("disabled", true);
                                                                                                $(".input-setup").attr("disabled", true);
                                                                                                $(".input-group-text").css("background", '#e3e3e3');
                                                                                                $.when($("#messanger-messanger").fadeOut("slow")).done(function () {
                                                                                                    $.when($("#messanger-messanger").remove()).done(function () {
                                                                                                        if(lange == 'en') {
                                                                                                            var messageYet = "Information was correct please don't leave this page and wait ! ";
                                                                                                        } else {
                                                                                                            var messageYet = "اطلاعات وارد شده صحیح بود لطفا صفحه را ترک نکنید تا منتقل شوید ! ";
                                                                                                        }
                                                                                                        $.when($(".card-footer").append("<div id='messanger-messanger' class='alert alert-info hide'><span>" + messageYet + "</span><button class='close'><i class='fas fa-spin fa-spinner'></i></button></div>")).done(function () {
                                                                                                            $.when($("#messanger-messanger").fadeOut('slow')).done(function () {
                                                                                                                $.when($("#messanger-messanger").removeClass("hide").css("display", "none")).done(function () {
                                                                                                                    $.when($("#messanger-messanger").fadeIn("slow")).done(function () {
                                                                                                                        <?php
                                                                                                                            if($_SERVER["REMOTE_ADDR"]!="::1" && $_SERVER["REMOTE_ADDR"]!="127.0.0.1"){
                                                                                                                        ?>
                                                                                                                        grecaptcha.ready(function () {
                                                                                                                            grecaptcha.execute($("#grecaptcha_sitekey").val(), {
                                                                                                                        <?php
                                                                                                                            }
                                                                                                                        ?>
                                                                                                                                action: 'homepage'
                                                                                                                            }).then(function (token) {
                                                                                                                                $.post("class/action.php?setup", {
                                                                                                                                    username_panel: $("#panel-username").val(),
                                                                                                                                    password_panel: $("#panel-password").val(),
                                                                                                                                    email_panel: $("#panel-email").val(),
                                                                                                                                    server_name: $("#server-name").val(),
                                                                                                                                    table_name: $("#table-name").val(),
                                                                                                                                    username_data: $("#username").val(),
                                                                                                                                    password_data: $("#password").val(),
                                                                                                                                    host_email: $("#host-email").val(),
                                                                                                                                    username_email: $("#username-email").val(),
                                                                                                                                    password_email: $("#password-email").val(),
                                                                                                                                    port_email: $("#port-email").val(),
                                                                                                                                    <?php print_r($sub_name); ?>email : $("#panel-email").val(),
                                                                                                                                    sender_name_email_en : $("#sender-name-email-en").val(),
                                                                                                                                    sender_name_email_fa: $("#sender-name-email-fa").val(),
                                                                                                                                    site_name_en: $("#site-name-en").val(),
                                                                                                                                    site_mini_name_en: $("#site-mini-name-en").val(),
                                                                                                                                    site_name_fa: $("#site-name-fa").val(),
                                                                                                                                    site_mini_name_fa: $("#site-mini-name-fa").val(),
                                                                                                                                    <?php
                                                                                                                                        if($_SERVER["REMOTE_ADDR"]!="::1" && $_SERVER["REMOTE_ADDR"]!="127.0.0.1"){
                                                                                                                                    ?>
                                                                                                                                    grecaptcha_sitekey: $("#grecaptcha_sitekey").val(),
                                                                                                                                    grecaptcha_secretkey: $("#grecaptcha_secretkey").val(),
                                                                                                                                    grecaptcha_token: token,
                                                                                                                                    <?php
                                                                                                                                        }else{
                                                                                                                                    ?>
                                                                                                                                    grecaptcha_sitekey: "",
                                                                                                                                    grecaptcha_secretkey: "",
                                                                                                                                    grecaptcha_token: "",
                                                                                                                                    <?php
                                                                                                                                        }
                                                                                                                                    ?>
																																	license_data: license_data,
																																	lang: "<?php print_r($custom_lang); ?>"
                                                                                                                                }, function (data, status) {
                                                                                                                                    saved_process++;
                                                                                                                                    if(status == "success") {
                                                                                                                                        if(data != 'success') {
                                                                                                                                            error_detector = data;
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        error_detector = 1;
                                                                                                                                        clearInterval(timeOutVar);
                                                                                                                                    }
                                                                                                                                }).always(function () {
                                                                                                                                    $('#preloader').fadeOut('slow');
                                                                                                                                    if(error_detector == 0) {
                                                                                                                                        $.when($("#messanger-messanger").fadeOut("slow")).done(function () {
                                                                                                                                            $.when($("#messanger-messanger").remove()).done(function () {
                                                                                                                                                if(lange == 'en') {
                                                                                                                                                    var messageYet = "Everything installed you will be return to the panel soon. ";
                                                                                                                                                } else {
                                                                                                                                                    var messageYet = "همه چیز نصب شده و به زودی به صفحه مورد نظر منتقل خواهید شد. ";
                                                                                                                                                }
                                                                                                                                                $.when($(".card-footer").append("<div id='messanger-messanger' class='alert alert-success hide'><span>" + messageYet + "</span><button class='close'><i class='far fa-thumbs-up'></i></button></div>")).done(function () {
                                                                                                                                                    $.when($("#messanger-messanger").fadeOut('slow')).done(function () {
                                                                                                                                                        $.when($("#messanger-messanger").removeClass("hide").css("display", "none")).done(function () {
                                                                                                                                                            $.when($("#messanger-messanger").fadeIn("slow")).done(function () {
                                                                                                                                                                <?php
																																									if($_SERVER['REMOTE_ADDR']!="::1" && $_SERVER['REMOTE_ADDR']!="127.0.0.1"){
																																								?>
																																									window.location = "../";
																																								<?php
																																									}
																																								?>
                                                                                                                                                            });
                                                                                                                                                        });
                                                                                                                                                    });
                                                                                                                                                });
                                                                                                                                            });
                                                                                                                                        });
                                                                                                                                    } else if(error_detector != 1) {
                                                                                                                                        error_show(error_detector, lange);
                                                                                                                                    } else {
                                                                                                                                        $.when($("#messanger-messanger").fadeOut("slow")).done(function () {
                                                                                                                                            $.when($("#messanger-messanger").remove()).done(function () {
                                                                                                                                                if(lange == 'en') {
                                                                                                                                                    var messageYet = "Something went wrong. ";
                                                                                                                                                } else {
                                                                                                                                                    var messageYet = "خطایی رخ داده. ";
                                                                                                                                                }
                                                                                                                                                $.when($(".card-footer").append("<div id='messanger-messanger' class='alert alert-danger hide'><span>" + messageYet + "</span><button class='close'><i class='far fa-exclamation-triangle'></i></button></div>")).done(function () {
                                                                                                                                                    $.when($("#messanger-messanger").fadeOut('slow')).done(function () {
                                                                                                                                                        $.when($("#messanger-messanger").removeClass("hide").css("display", "none")).done(function () {
                                                                                                                                                            $.when($("#messanger-messanger").fadeIn("slow")).done(function () {
                                                                                                                                                                $(".submiter").show().attr("disabled", false);
                                                                                                                                                                $(".input-setup").attr("disabled", false);
                                                                                                                                                                $("div[style='background: rgb(227, 227, 227) none repeat scroll 0% 0%;']").css("background-color", "");
                                                                                                                                                            });
                                                                                                                                                        });
                                                                                                                                                    });
                                                                                                                                                });
                                                                                                                                            });
                                                                                                                                        });
                                                                                                                                    }
                                                                                                                                });
                                                                                                                        <?php
                                                                                                                            if($_SERVER["REMOTE_ADDR"]!="::1" && $_SERVER["REMOTE_ADDR"]!="127.0.0.1"){
                                                                                                                        ?>
                                                                                                                            });
                                                                                                                        });
                                                                                                                        <?php
                                                                                                                            }
                                                                                                                        ?>
                                                                                                                    });
                                                                                                                });
                                                                                                            });
                                                                                                        });
                                                                                                    });
                                                                                                });
                                                                                            } else if(error_detector != 1) {
                                                                                                error_show(error_detector, lange);
                                                                                            }
                                                                                        });
                                                                                    });
                                                                                } else if(error_detector != 1) {
                                                                                    error_show(error_detector, lange);
                                                                                }
                                                                            });
                                                                        } else if(error_detector != 1) {
                                                                            error_show(error_detector, lange);
                                                                        }
                                                                    } else {
                                                                        $(".submiter").show().attr("disabled", false);
                                                                        $(".input-setup").attr("disabled", false);
                                                                        $('#preloader').fadeOut('slow');
                                                                    }
                                                                } else if(error_detector != 1) {
                                                                    error_show(error_detector, lange);
                                                                }
                                                            });
                                                        // empty value
                                                        //}
                                                        // empty value
                                                    } else {
                                                        error_show(error_detector, lange);
                                                    }
                                <?php
                                    if($_SERVER["REMOTE_ADDR"]!="::1" && $_SERVER["REMOTE_ADDR"]!="127.0.0.1"){
                                ?>
                                                });
                                            });
                                        });
                                    }, 1000);
                                });
                                <?php
                                    }
                                ?>
                            }
                        // empty value
                        // }
                        // empty value
                    }
                }
            });
        });
    }

    function error_show(error_detector, lange) {
        // console.log(error_detector);
        switch (error_detector) {
            case "inv_email":
                showMessage("امکان ارسال ایمیل با اطلاعات وارد شده وجود ندارد !", "Cannot send email with entered information !", "warning", 'far fa-exclamation-triangle', lange);
                break;
            case "inv_database":
                $("#server-name").parent().addClass('has-danger');
                $("#table-name").parent().addClass('has-danger');
                $("#username").parent().addClass('has-danger');
                $("#password").parent().addClass('has-danger');
                showMessage("امکان اتصال به دیتابیس با اطلاعات وارد شده وجود ندارد !", "Unable to connect to database with entered information !", "warning", 'far fa-exclamation-triangle', lange);
                break;
            case "inv_user":
                $("#panel-username").parent().addClass('has-danger');
                $("#panel-password").parent().addClass('has-danger');
                showMessage("نام کاربری و کلمه عبور همخوانی !", "The username and password is incorrect !", "warning", 'far fa-exclamation-triangle', lange);
                break;
            case "inv_domain":
                showMessage("این دامنه تحت پوشش لایسنس نیست !", "This domain is not licensed !", "danger", 'far fa-exclamation-triangle', lange);
                break;
            case "inv_grecaptcha":
                showMessage("کد grecaptcha صحیح نیست !", "The grecaptcha code (Secret Key) is not valid", "danger", 'far fa-exclamation-triangle', lange);
                break;
            default:
                showMessage("خطا ناشناخته !", "Unknown error !", "danger", 'far fa-exclamation-triangle', lange);
        }
        clearInterval(timeOutVar);
        $(".submiter").show().attr("disabled", false);
        $(".input-setup").attr("disabled", false);
        $("div[style='background: rgb(227, 227, 227) none repeat scroll 0% 0%;']").css("background-color", "");
        $('#preloader').fadeOut('slow');
    }
    $(document).on('keydown', '.input-setup', function () {
        // empty value
        // if($(this).val() == "") {
        //     setTimeout(function(){
        //         if($(this).val() != "") {
        //             $(this).parent().removeClass('has-danger');
        //         }
        //     }, 100);
        // } else {
        // empty value
            if(typeof temp_data_val === "undefined"){
                temp_data_val = $(this).val();
            }
            if($(this).val() != temp_data_val) {
                $(this).parent().removeClass('has-danger');
            }
            temp_data_val = $(this).val();
        // empty value
        // }
        // empty value
    });
    $(document).on('change', '.input-setup', function () {
        // empty value
        // if($(this).val() != "") {
        // empty value
            $(this).parent().removeClass('has-danger');
        // empty value
        // }
        // empty value
    });
</script>
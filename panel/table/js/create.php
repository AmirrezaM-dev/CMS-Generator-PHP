<?php
	$conn_dir="../../../connection/connect.php";
	if(session_status() == PHP_SESSION_NONE) {
        session_start(['cookie_lifetime' => 86400]);
	}
	require_once("../../config.php");
	require_once("../../setting/check_database.php");
	if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_SESSION['username']."' AND act=1");
			$user_stats=$res_user->rowCount();
			if($user_stats==1 || getSetting("op_admin")==$_SESSION['username']){
				$res_table_config = $connection->query("SELECT * FROM " . $sub_name . "table_config WHERE lock_admin_id='" . $_SESSION["username"] . "'");
				$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
				$table_name = $sub_name."table_config";
				$res_table_id = $connection->query("SELECT * FROM " . $sub_name . "table_config WHERE current_name='" . $table_name . "' AND created=1 AND creatable=1 && visible=1 OR current_name='" . $table_name . "' AND created=1 AND '" . $op_admin . "'=1");
				if($res_table_id->rowCount() != 0){
					$table_get = $res_table_id->fetch();
					$table_id = $table_get['id'];
					if(checkPermission(1, $table_id, "read", $table_get['act'], "") && checkPermission("group_array_full", getTableByName($sub_name."table_column_mode")["id"], "read", getTableByName($sub_name."table_column_mode")["act"], null) && checkPermission("group_array_full", getTableByName($sub_name."table_config")["id"], "create", getTableByName($sub_name."table_config")["act"], null)){
						$custom_create_class = "col-xl-2 col-lg-2 col-md-4 col-sm-6 col-12";
						$primary_buttons_en='<button onclick="createButtonOperations(\'save\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-success data-original-title data-text save-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will save this column and its will still stay here for edit !" data-original-title-fa="با فشردن این دکمه شما این ستون را ذخیره خواهید نمود و این ستون برای بازنگری و ویرایش همچنان اینجا باقی میماند !" data-text-en="Save" data-text-fa="ذخیره" data-original-title="By pressing this button you will save this column and its will still stay here for edit !" data-placement="top">Save</button> <button onclick="createButtonOperations(\'save_close\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-info data-original-title data-text save_close-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will save this column and its will close !" data-original-title-fa="با فشردن این دکمه شما این ستون را ذخیره خواهید نمود و بسته میشود !" data-text-en="Save & Close" data-text-fa="ذخیره و خروج" data-original-title="By pressing this button you will save this column and its will close !" data-placement="top">Save & Close</button> <button onclick="createButtonOperations(\'add\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-info data-original-title data-text add-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will copy all fields of this column !" data-original-title-fa="با فشردن این دکمه تمام فیلد ها کپی خواهند شد !" data-text-en="Copy" data-text-fa="کپی" data-original-title="By pressing this button you will copy all fields of this column !" data-placement="top">Copy</button> <button onclick="createButtonOperations(\'clear\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-warning data-original-title data-text clear-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will clear all fields of this column !" data-original-title-fa="با فشردن این دکمه تمام فیلد ها پاکسازی خواهند شد !" data-text-en="Clear" data-text-fa="پاکسازی" data-original-title="By pressing this button you will clear all fields of this column !" data-placement="top">Clear</button> <button onclick="createButtonOperations(\'reset\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-warning data-original-title data-text reset-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will reset all fields of this column !" data-original-title-fa="با فشردن این دکمه تمام فیلد ها بازیابی خواهند شد !" data-text-en="Reset" data-text-fa="بازیابی" data-original-title="By pressing this button you will reset all fields of this column !" data-placement="top">Reset</button> <button onclick="createButtonOperations(\'skip\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-warning data-original-title data-text skip-button-operation" rel="tooltip" data-original-title-en="Skip this column !" data-original-title-fa="رد کردن این ستون !" data-text-en="Skip" data-text-fa="رد کردن" data-original-title="Skip this column !" data-placement="top">Skip</button> <button onclick="createButtonOperations(\'delete\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-danger data-original-title data-text delete-button-operation" rel="tooltip" data-original-title-en="Delete this column for ever !" data-original-title-fa="حذف این ستون برای همیشه !" data-text-en="Delete" data-text-fa="حذف" data-original-title="Delete this column for ever !" data-placement="top">Delete</button>';
						$primary_buttons_fa='<button onclick="createButtonOperations(\'save\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-success data-original-title data-text save-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will save this column and its will still stay here for edit !" data-original-title-fa="با فشردن این دکمه شما این ستون را ذخیره خواهید نمود و این ستون برای بازنگری و ویرایش همچنان اینجا باقی میماند !" data-text-en="Save" data-text-fa="ذخیره" data-original-title="با فشردن این دکمه شما این ستون را ذخیره خواهید نمود و این ستون برای بازنگری و ویرایش همچنان اینجا باقی میماند !" data-placement="top">ذخیره</button> <button onclick="createButtonOperations(\'save_close\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-info data-original-title data-text save_close-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will save this column and its will close !" data-original-title-fa="با فشردن این دکمه شما این ستون را ذخیره خواهید نمود و بسته میشود !" data-text-en="Save & Close" data-text-fa="ذخیره و خروج" data-original-title="با فشردن این دکمه شما این ستون را ذخیره خواهید نمود و بسته میشود !" data-placement="top">ذخیره و خروج</button> <button onclick="createButtonOperations(\'add\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-info data-original-title data-text add-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will copy all fields of this column !" data-original-title-fa="با فشردن این دکمه تمام فیلد ها کپی خواهند شد !" data-text-en="Copy" data-text-fa="کپی" data-original-title="با فشردن این دکمه تمام فیلد ها کپی خواهند شد !" data-placement="top">کپی</button> <button onclick="createButtonOperations(\'clear\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-warning data-original-title data-text clear-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will clear all fields of this column !" data-original-title-fa="با فشردن این دکمه تمام فیلد ها پاکسازی خواهند شد !" data-text-en="Clear" data-text-fa="پاکسازی" data-original-title="با فشردن این دکمه تمام فیلد ها پاکسازی خواهند شد !" data-placement="top">پاکسازی</button> <button onclick="createButtonOperations(\'reset\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-warning data-original-title data-text reset-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will reset all fields of this column !" data-original-title-fa="با فشردن این دکمه تمام فیلد ها بازیابی خواهند شد !" data-text-en="Reset" data-text-fa="بازیابی" data-original-title="با فشردن این دکمه تمام فیلد ها بازیابی خواهند شد !" data-placement="top">بازیابی</button> <button onclick="createButtonOperations(\'skip\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-warning data-original-title data-text skip-button-operation" rel="tooltip" data-original-title-en="Skip this column !" data-original-title-fa="رد کردن این ستون !" data-text-en="Skip" data-text-fa="رد کردن" data-original-title="رد کردن این ستون !" data-placement="top">رد کردن</button> <button onclick="createButtonOperations(\'delete\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-danger data-original-title data-text delete-button-operation" rel="tooltip" data-original-title-en="Delete this column for ever !" data-original-title-fa="حذف این ستون برای همیشه !" data-text-en="Delete" data-text-fa="حذف" data-original-title="حذف این ستون برای همیشه !" data-placement="top">حذف</button>';

?>
<script>
	var $where_we_come_from,
		$where_we_go,
		$database_level = <?php print_r($table_config != 0 ? $table_config["level"] : 0); ?>,
		$create_level = <?php print_r($table_config != 0 ? $table_config["level"] : 0); ?>,
		$loading_create_table = 0,
		$disable_alert=0,
		$table_loaded=0,
		$do_select_column=[],
		$dataTableInstaller,
		$save_icons = [];
		$save_icons[0] = "fal fa-server";
		$save_icons[1] = "fal fa-cogs";
		$save_icons[2] = "fal fa-line-columns";
		$save_icons[3] = "fal fa-bars";
		$save_icons["loading"] = "fad fa-spin fa-spinner-third";
		$wizzard_nav="";

	function updateTable(){
		$("#tables_columns").DataTable().ajax.reload( null, false );
		$('table.selectOpt_table').each(function () {
			$(this).DataTable().ajax.reload( null, false );
		});
	}

	function createOperations($start, $nav, $current_level) {
		if (typeof $start !== "undefined") {
			if ($start >= 0 && $create_level >= 0) {

				$loading_create_table = 1;
				for ($i = $current_level; $i <= $start; $i++) {
					$($nav.find("li a i")[$i]).attr("class", "").addClass($save_icons["loading"]);
				}
				$nav.find("li a").addClass("create_table_loading");
				getElement(".btn-wd").attr("disabled", true);

				if (getValue("#current_name") != "" && getValue("#current_name").length != 0 && getValue("#description_name_fa") != "" && getValue("#description_name_fa").length != 0 && getValue("#description_name_en") != "" && getValue("#description_name_en").length != 0) {
					$.when($("#current_name").parent().removeClass("has-success has-danger").children("label.error").remove()).done(function() {
						delete $first_error;
						var $first_error = 0;
						$first_error = 0;
						$.post("table/class/action.php?create_table_name", {
							current_name: getValue("#current_name"),
							description_name_fa: getValue("#description_name_fa"),
							description_info_fa: getValue("#description_info_fa"),
							description_name_en: getValue("#description_name_en"),
							description_info_en: getValue("#description_info_en")
						}, function(data, status) {
							if (status == "success" && data == "success" && $first_error == 0) {
								getElement("#current_name").parent().attr("class", "input-group has-success").children("label.error").remove();
							} else if (status == "success" && data.indexOf("error") != -1) {
								$first_error = 1;
								if(data.split("_._").length>=2){
									$data = data.split("_._")[1].split("_.._");
									switch ($data[0]) {
										case "name_duplicated":
											getElement("#current_name").parent(".input-group").attr("class", "input-group has-danger").children("label.error").remove();
											getElement("#current_name").focus().parent(".input-group").append('<label class="error error-duplicate data-text" data-text-en="This name has already been taken !" data-text-fa="این نام قبلاً گرفته شده است !">'+(language=="en" ? "This name has already been taken":"این نام قبلاً گرفته شده است")+' !</label>');
										break;
										case "bad_name":
											if($data[2]=="current_name"){
												getElement("#current_name").parent(".input-group").attr("class", "input-group has-danger").children("label.error").remove();
												getElement("#current_name").focus().parent(".input-group").append('<label class="error error-duplicate data-text" data-text-en="This name is not allowed (only letters and numbers and underline) !" data-text-fa="این نام مجاز نیست (فقط حروف و اعداد و آندرلاین) !">'+(language=="en" ? "This name is not allowed (only letters and numbers and underline) !":"این نام مجاز نیست (فقط حروف و اعداد و آندرلاین) !")+'</label>');
											}else if($data[2]=="description_name_fa"){
												getElement("#description_name_fa").parent(".input-group").attr("class", "input-group has-danger").children("label.error").remove();
												getElement("#description_name_fa").focus().parent(".input-group").append('<label class="error error-duplicate data-text" data-text-en="This name is not allowed (only letters and numbers and underline) !" data-text-fa="این نام مجاز نیست (فقط حروف و اعداد و آندرلاین) !">'+(language=="en" ? "This name is not allowed (only letters and numbers and underline) !":"این نام مجاز نیست (فقط حروف و اعداد و آندرلاین) !")+'</label>');
											}else if($data[2]=="description_name_en"){
												getElement("#description_name_en").parent(".input-group").attr("class", "input-group has-danger").children("label.error").remove();
												getElement("#description_name_en").focus().parent(".input-group").append('<label class="error error-duplicate data-text" data-text-en="This name is not allowed (only letters and numbers and underline) !" data-text-fa="این نام مجاز نیست (فقط حروف و اعداد و آندرلاین) !">'+(language=="en" ? "This name is not allowed (only letters and numbers and underline) !":"این نام مجاز نیست (فقط حروف و اعداد و آندرلاین) !")+'</label>');
											}
										break;
									}
								}
							} else {
								feedbackOperations(data);
								$first_error = 1;
							}
						}).always(function() {
							if ($first_error == 0) {
								updateLevel(1);
								if($start >=1){
									$($nav.find('li a i')[0]).attr("class", "far fa-check").parent("a").addClass("checked");
								}

								if ($start < 2) {
									for ($i = 1; $i <= $start; $i++) {
										if ($i != 0) {
											$($nav.find('li a i')[$i]).attr("class", "").addClass($save_icons[$i]);
										}
									}
								}

								if ($start >= 2) {

									delete $second_error;
									var $second_error = 0;
									$second_error = 0;
									$.post("table/class/action.php?stepTwo", {
										creatable: ($("#creatable_table").is(':checked') ? 1:0),
										visible: ($("#visible_table").is(':checked') ? 1:0),
										editable: ($("#editable_table").is(':checked') ? 1:0),
										removable: ($("#removable_table").is(':checked') ? 1:0)
									}, function(data, status) {
										if (status != "success" || data != "success" || $second_error == 1) {
											feedbackOperations(data);
											$second_error = 1;
										}
									}).always(function() {
										if ($second_error == 0) {
											updateLevel(2);
											$($nav.find('li a i')[1]).attr("class", "far fa-check").parent("a").addClass("checked");
											if ($start < 3) {
												for ($i = 2; $i <= $start; $i++) {
													if ($i != 0) {
														$($nav.find('li a i')[$i]).attr("class", "").addClass($save_icons[$i]);
													}
												}
											}

											if ($start >= 3) {

												var $saveInterval;
												$("#stack-of-columns").children().each(function(){
													$disable_alert=1;
													$(this).find(".save_close-button-operation").click();
												});
												$saveInterval=setInterval(() => {
													if($("#stack-of-columns").children().length==0){
														clearInterval($saveInterval);
														$disable_alert=0;
														$.post("table/class/action.php?gotToStep3", {}, function(data, status) {
															$loading_create_table = 0;
															getElement(".btn-wd").attr("disabled", false);
															$nav.find('li a').removeClass("create_table_loading");
															if (status == "success" && data.toString().indexOf("success")!=-1) {
																updateLevel(3);
																$($nav.find('li a i')[2]).attr("class", "far fa-check").parent("a").addClass("checked");
																if ($start < 4) {
																	for ($i = 3; $i <= $start; $i++) {
																		if ($i != 0) {
																			$($nav.find('li a i')[$i]).attr("class", "").addClass($save_icons[$i]);
																		}
																	}
																}
																$($nav.find('li a')[3]).click();
															}else{
																feedbackOperations(data);
																updateLevel(2);
																if ($start < 4) {
																	for ($i = 2; $i <= $start; $i++) {
																		if ($i != 0) {
																			$($nav.find('li a i')[$i]).attr("class", "").addClass($save_icons[$i]);
																		}
																	}
																}
																$($nav.find('li a')[2]).click();
															}
														});
													}
												}, 500);

											} else if ($start == 2) {
												$loading_create_table = 0;
												getElement(".btn-wd").attr("disabled", false);
												$nav.find('li a').removeClass("create_table_loading");
												$($nav.find('li a')[$start]).click();
											} else if ($start <= 1) {
												$loading_create_table = 0;
												getElement(".btn-wd").attr("disabled", false);
												$nav.find('li a').removeClass("create_table_loading");
											}
										} else {
											updateLevel(0);
											$loading_create_table = 0;
											$.when($($nav.find('li a')[0]).click()).done(function() {
												for ($i = $current_level; $i <= $start; $i++) {
													if ($i != 0) {
														$($nav.find('li a i')[$i]).attr("class", "").addClass($save_icons[$i]);
													} else {
														$($nav.find('li a i')[$i]).attr("class", "").addClass('far fa-exclamation-triangle');
													}
												}
												getElement(".btn-wd").attr("disabled", false);
												$nav.find('li a').removeClass("create_table_loading");
											});
										}
									});

								} else if ($start == 1) {
									$loading_create_table = 0;
									getElement(".btn-wd").attr("disabled", false);
									$nav.find('li a').removeClass("create_table_loading");
									$($nav.find('li a')[$start]).click();
								} else if ($start == 0) {
									$loading_create_table = 0;
									getElement(".btn-wd").attr("disabled", false);
									$nav.find('li a').removeClass("create_table_loading");
								}
							} else {
								updateLevel(0);
								$loading_create_table = 0;
								$.when($($nav.find('li a')[0]).click()).done(function() {
									for ($i = $current_level; $i <= $start; $i++) {
										if ($i != 0) {
											$($nav.find('li a i')[$i]).attr("class", "").addClass($save_icons[$i]);
										} else {
											$($nav.find('li a i')[$i]).attr("class", "").addClass('far fa-exclamation-triangle');
										}
									}
									getElement(".btn-wd").attr("disabled", false);
									$nav.find('li a').removeClass("create_table_loading");
								});
							}
						});
					});

				} else {
					var $error_input=(getValue("#current_name") == "" || getValue("#current_name").length == 0 ? "#current_name":(getValue("#description_name_fa") == "" || getValue("#description_name_fa").length == 0 ? "#description_name_fa":(getValue("#description_name_en") == "" || getValue("#description_name_en").length == 0 ? "#description_name_en":"")));
					updateLevel(0);
					$loading_create_table = 0;
					$.when($($error_input).parent().addClass("shaker has-danger").removeClass("has-success")).done(function() {
						$.when($($nav.find('li a')[0]).click()).done(function() {
							$.when(($($error_input).focus().parent().children("label.error").length == 0 ? $($error_input).focus().parent().append('<label class="error error-empty data-text" data-text-en="This field is required !" data-text-fa="این فیلد مورد نیاز است !">' + (language == "en" ? "This field is required !" : "این فیلد مورد نیاز است !") + '</label>') : $($error_input).focus().parent().children("label.error").attr({
								"class": "error error-empty data-text",
								"data-text-en": "This field is required !",
								"data-text-fa": "این فیلد مورد نیاز است !"
							}).text((language == "en" ? "This field is required !" : "این فیلد مورد نیاز است !")))).done(function() {
								setTimeout(() => {
									$.when($($error_input).focus().parent().removeClass("shaker")).done(function() {
										for ($i = $current_level; $i <= $start; $i++) {
											if ($i != 0) {
												$($nav.find('li a i')[$i]).attr("class", "").addClass($save_icons[$i]);
											} else {
												$($nav.find('li a i')[$i]).attr("class", "").addClass('far fa-exclamation-triangle');
											}
										}
										getElement(".btn-wd").attr("disabled", false);
										$nav.find('li a').removeClass("create_table_loading");
									});
								}, 500);
							});
						});
					});
				}
			}
		}
	}

	function createTable_wizzard() {
		if (operationIn == 0) {
			$.when(setOperationIn(1, "createTable_wizzard")).done(function() {
				$.when($('.card-wizard').bootstrapWizard({
					'tabClass': 'nav nav-pills',
					'nextSelector': '.btn-next',
					'previousSelector': '.btn-previous',

					onInit: function(tab, navigation, index) {
						var $total = navigation.find('li').length;
						$width = 100 / $total;
						navigation.find('li').css('width', $width + '%');
					},

					onNext: function(tab, navigation, index) {
						$where_we_come_from = index - 1;
						$where_we_go = index;
						if ($loading_create_table == 0 && $where_we_come_from != $where_we_go) {
							createOperations(index, navigation, $where_we_come_from);
							if ($create_level < $where_we_go) {
								return false;
							}
						} else {
							return false;
						}
					},

					onTabClick: function(tab, navigation, index, $where_we_go) {
						if ($loading_create_table == 0 && index != $where_we_go) {
							createOperations($where_we_go, navigation, index);
							if ($create_level < $where_we_go) {
								return false;
							}
						} else {
							return false;
						}
					},

					onTabShow: function(tab, navigation, index) {
						bsSwitcher();
						if(index==2){
							if($table_loaded==0){
								setTimeout(() => {
									$table_loaded=1;
									callDataTable_tables_columns();
								}, 1000);
							}else{
								callDataTable_tables_columns();
							}
						}

						if($database_level==0 && $(".preloader_table").length){
							$wizzard_nav=$(".wizard-navigation ul.custom_transition.nav.nav-pills");
							$(".preloader_table").fadeOut(700);
							setTimeout(() => {
								$(".preloader_table").remove();
							}, 700);
							$("#current_name").lettersOnly();
							// $("#description_name_fa").lettersOnly();
							$("#description_name_en").lettersOnly();
							$(".input_current_name").lettersOnly();
							$(".input_description_name_fa").lettersOnly();
							$(".input_description_name_en").lettersOnly();
						}else if($(".preloader_table_2").length){
							$wizzard_nav=$(".wizard-navigation ul.custom_transition.nav.nav-pills");
							$(".preloader_table_2").fadeOut(700);
							setTimeout(() => {
								$(".preloader_table_2").remove();
							}, 700);
							$("#current_name").lettersOnly();
							// $("#description_name_fa").lettersOnly();
							$("#description_name_en").lettersOnly();
							$(".input_current_name").lettersOnly();
							$(".input_description_name_fa").lettersOnly();
							$(".input_description_name_en").lettersOnly();
						}
						updateLevel(index,1,navigation);

						var $total = navigation.find('li').length;
						var $current = index + 1;
						var $wizard = navigation.closest('.card-wizard');

						if ($current >= $total) {
							$($wizard).find('.btn-next').hide();
							$($wizard).find('.btn-finish').show();
						} else {
							$($wizard).find('.btn-next').show();
							$($wizard).find('.btn-finish').hide();
						}

						var move_distance = 100 / $total;
						move_distance = move_distance * (index) + move_distance / 2;

						$wizard.find($('.progress-bar')).css({
							width: move_distance + '%'
						});
						<?php
							if($table_config && $table_config['created']==1){
						?>
							$.when($(navigation.find('li a i')).attr("class", "far fa-check").parent("a").addClass("checked")).done(function(){
								$.when($(navigation.find('li a i')[index]).attr("class", "").addClass($save_icons[index])).done(function(){
									$.when($(".card-wizard").find($('.progress-bar')).css({width: '100%'})).done(function(){

									});
								});
							});
						<?php
							}
						?>
					}
				})).done(function() {
					$('.set-full-height').css('height', 'auto');
					$('.card.card-wizard').addClass('active');
					setOperationIn(0, "createTable_wizzard");
				});
			});
		} else {
			doOperations('createTable_wizzard');
		}
	}

	function updateLevel($level,$dont,$nav) {
		$("#wizardProfile .card-footer").css("height","78px");
		if($dont!=1){
			$create_level=$level;
		}else{
			if($level==0 && $database_level!=0){
				if (operationIn == 0) {
					$.when(setOperationIn(1, "updateLevel")).done(function() {
						var $database_level_saver=$database_level;
						$database_level=0;
						$.when($($nav.find('li a')[$database_level_saver]).click()).done(function(){
							setOperationIn(0, "updateLevel");
						});
					});
				} else {
					doOperations('updateLevel', [$level,$dont,$nav])
				}
			}else{
				$.post("table/class/action.php?update_level", {
					"level" : $level
				}, function(data, status) {
					if (status != "success" || data != "success") {
						feedbackOperations(data);
					}
				});
			}
		}
	}

	function newEditor($got_id,$isSaveds,$current_name,$description_name_fa,$description_name_en,$description_info_fa,$description_info_en,$input_creatable,$input_visible,$input_editable,$input_removable,$input_visible_table,$input_select,$input_important,$input_primary,$input_extra_val,$noScroll,$operation) {
		var $string_extra="";
		<?php //info search this_is_modes_for_data_tables for see all things about this part ?>
		switch ($input_select) {//tables_mode_code
			case "3":case 3://info search case 3 for see all things about this part
				if($input_extra_val.length!=6){
					$input_extra_val=["","","","","",""];
				}
				$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
					$string_extra+='<div class="form-group"> ';
						$string_extra+='<input class="form-control data-placeholder data-original-title input_yes_option input_text on-press-enter-column" rel="tooltip" data-original-title-en="Enabled/Yes" data-original-title-fa="فعال/بله" data-original-title="'+(language=="en" ? "Enabled/Yes":"فعال/بله")+'" data-placement="top" data-placeholder-en="Enabled/Yes" data-placeholder-fa="فعال/بله" placeholder="'+(language=='en' ? "Enabled/Yes":"فعال/بله")+'" type="text" data-reset-value="'+$input_extra_val[0]+'" value="'+$input_extra_val[0]+'"> ';
					$string_extra+='</div> ';
				$string_extra+='</div> ';
				$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
					$string_extra+='<div class="form-group"> ';
						$string_extra+='<input class="form-control data-placeholder data-original-title input_no_option input_text on-press-enter-column" rel="tooltip" data-original-title-en="Disable/No" data-original-title-fa="غیرفعال/خیر" data-original-title="'+(language=="en" ? "Disable/No":"غیرفعال/خیر")+'" data-placement="top" data-placeholder-en="Disable/No" data-placeholder-fa="غیرفعال/خیر" placeholder="'+(language=="en" ? "Disable/No":"غیرفعال/خیر")+'" type="text" data-reset-value="'+$input_extra_val[1]+'" value="'+$input_extra_val[1]+'"> ';
					$string_extra+='</div> ';
				$string_extra+='</div> ';
				$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
					$string_extra+='<div class="form-group"> ';
						$string_extra+='<input class="form-control data-placeholder data-original-title input_yes_value input_text on-press-enter-column" rel="tooltip" data-original-title-en="1/true" data-original-title-fa="1/true1" data-original-title="'+(language=="en" ? "1/true":"1/true1")+'" data-placement="top" data-placeholder-en="1/true" data-placeholder-fa="1/true1" placeholder="'+(language=="en" ? "1/true":"1/true1")+'" type="text" data-reset-value="'+($input_extra_val[2]!="" ? $input_extra_val[2]:1)+'" value="'+($input_extra_val[2]!="" ? $input_extra_val[2]:1)+'"> ';
					$string_extra+='</div> ';
				$string_extra+='</div> ';
				$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
					$string_extra+='<div class="form-group"> ';
						$string_extra+='<input class="form-control data-placeholder data-original-title input_no_value input_text on-press-enter-column" rel="tooltip" data-original-title-en="0/false" data-original-title-fa="0/false" data-original-title="'+(language=="en" ? "0/false":"0/false")+'" data-placement="top" data-placeholder-en="0/false" data-placeholder-fa="0/false" placeholder="'+(language=="en" ? "0/false":"0/false")+'" type="text" data-reset-value="'+($input_extra_val[3]!="" ? $input_extra_val[3]:0)+'" value="'+($input_extra_val[3]!="" ? $input_extra_val[3]:0)+'"> ';
					$string_extra+='</div> ';
				$string_extra+='</div>';
				$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
					$string_extra+='<div class="form-group"> ';
						$string_extra+='<input class="form-control data-placeholder data-original-title input_yes_icon input_text on-press-enter-column" rel="tooltip" data-original-title-en="far fa-check" data-original-title-fa="far fa-check" data-original-title="'+(language=="en" ? "far fa-check":"far fa-check")+'" data-placement="top" data-placeholder-en="far fa-check" data-placeholder-fa="far fa-check" placeholder="'+(language=="en" ? "far fa-check":"far fa-check")+'" type="text" data-reset-value="'+$input_extra_val[4]+'" value="'+$input_extra_val[4]+'"> ';
					$string_extra+='</div> ';
				$string_extra+='</div>';
				$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
					$string_extra+='<div class="form-group"> ';
						$string_extra+='<input class="form-control data-placeholder data-original-title input_no_icon input_text on-press-enter-column" rel="tooltip" data-original-title-en="far fa-square" data-original-title-fa="far fa-square" data-original-title="'+(language=="en" ? "far fa-square":"far fa-square")+'" data-placement="top" data-placeholder-en="far fa-square" data-placeholder-fa="far fa-square" placeholder="'+(language=="en" ? "far fa-square":"far fa-square")+'" type="text" data-reset-value="'+$input_extra_val[5]+'" value="'+$input_extra_val[5]+'"> ';
					$string_extra+='</div> ';
				$string_extra+='</div>';
			break;
			case "4":case 4://info search case 4 for see all things about this part
				if($isSaveds=="new-column"){
					$string_extra='<div class="alert alert-info col-12">';
						$string_extra+='<span class="data-text" data-text-en="You have to save this column to appear this part" data-text-fa="برای نمایان شدن این قسمت باید این ستون را ذخیره نمایید">'+(language=="en" ? "You have to save this column to see this part":"برای نمایان شدن این قسمت باید این ستون را ذخیره نمایید")+'</span>';
					$string_extra+='</div>';
				}else if($isSaveds=="saved_column"){
					if($input_extra_val.length!=4){
						$input_extra_val=[0,0,0,0];
					}
					$string_extra='<div class="col-md-12 row mr-auto ml-auto pb-3">';
						$string_extra+='<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto">';
							$string_extra+='<div class="form-check cursor-pointer">';
								$string_extra+='<label class="form-check-label">';
									$string_extra+='<input class="form-check-input input_is_multiple input_checkbox" '+($input_extra_val[0].toString()=="1" ? "checked":"")+' type="checkbox" data-reset-value="">';
									$string_extra+='<span class="form-check-sign"></span>';
									$string_extra+='<label class="data-text" data-text-en="Multiple ?" data-text-fa="چندتایی ؟">'+(language=="en" ? "Multiple ?" : "چندتایی ؟")+'</label>';
								$string_extra+='</label>';
							$string_extra+='</div>';
						$string_extra+='</div>';
						$string_extra+='<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto hide">';
							$string_extra+='<div class="form-check cursor-pointer">';
								$string_extra+='<label class="form-check-label">';
									$string_extra+='<input class="form-check-input input_is_forced input_checkbox" '+/*($input_extra_val[1].toString()=="1" ? "checked":"")*/'checked'+' type="checkbox" data-reset-value="">';
									$string_extra+='<span class="form-check-sign"></span>';
									$string_extra+='<label class="data-text" data-text-en="Forced ?" data-text-fa="اجباری ؟">'+(language == "en" ? "Forced ?" : "اجباری ؟")+'</label>';
								$string_extra+='</label>';
							$string_extra+='</div>';
						$string_extra+='</div>';
						<?php
							/*
								$string_extra+='<div class="col-lg-3 col-12 mb-2 ml-auto mr-auto">';
									$string_extra+='<div class="form-group">';
										$string_extra+='<input class="form-control data-placeholder data-original-title input_min_allowed input_text" rel="tooltip" data-original-title-en="Minimum amount allowed" data-original-title-fa="کمترین مقدار مجاز" data-original-title="'+(language== "en" ? "Minimum amount allowed" : "کمترین مقدار مجاز")+'" data-placement="top" data-placeholder-en="Minimum amount allowed" data-placeholder-fa="کمترین مقدار مجاز" placeholder="'+(language=="en" ? "Minimum amount allowed":"کمترین مقدار مجاز")+'" type="number" data-reset-value="'+$input_extra_val[2]+'" value="'+$input_extra_val[2]+'">';
									$string_extra+='</div>';
								$string_extra+='</div>';
								$string_extra+='<div class="col-lg-3 col-12 mb-2 ml-auto mr-auto">';
									$string_extra+='<div class="form-group">';
										$string_extra+='<input class="form-control data-placeholder data-original-title input_max_allowed input_text" rel="tooltip" data-original-title-en="Maximum amount allowed" data-original-title-fa="بیشترین مقدار مجاز" data-original-title="'+(language == "en" ? "Maximum amount allowed" : "بیشترین مقدار مجاز")+'" data-placement="top" data-placeholder-en="Maximum amount allowed" data-placeholder-fa="بیشترین مقدار مجاز" placeholder="'+(language=="en" ? "Maximum amount allowed":"بیشترین مقدار مجاز")+'" type="number" data-reset-value="'+$input_extra_val[3]+'" value="'+$input_extra_val[3]+'">';
									$string_extra+='</div>';
								$string_extra+='</div>';
							*/
						?>
					$string_extra+='</div>';
					$string_extra+='<div class="col-md-12 row mr-auto ml-auto">';
						$string_extra+='<div class="col-lg-6 col-12 row mb-2 ml-auto mr-auto">';
						$string_extra+='<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto">';
						$string_extra+='<div class="form-check cursor-pointer checkbox_changer">';
						$string_extra+='<label class="form-check-label">';
						$string_extra+='<input class="form-check-input is_optgroup_opt" type="checkbox">';
						$string_extra+='<span class="form-check-sign"></span>';
						$string_extra+='<label class="data-text" data-text-en="Optgroup ?" data-text-fa="سرگروه ؟">'+(language == "en" ? "Optgroup ?" : "سرگروه ؟")+'</label>';
						$string_extra+='</label>';
						$string_extra+='</div>';
						$string_extra+='</div>';
						$string_extra+='<select class="selectpicker data-title col-lg-4 col-12 mb-2 ml-auto mr-auto connected_table" data-title-en="Tables" data-title-fa="جدول ها" data-style="btn btn-primary" title="'+(language=="en" ? "Tables":"جدول ها")+'" data-size="7" data-live-search="true">';
						$string_extra+='<option class="data-text" value="0" data-text-en="Manual" data-text-fa="دستی">';
							$string_extra+=(language=="en" ? "Manual":"دستی");
						$string_extra+='</option>';
						<?php
							$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_config ORDER BY ordering ASC");
							while($tables=$res_tables->fetch()){
								if(checkPermission(1,$tables['id'],"read",$tables['act'],null)==1){
						?>
							$string_extra+='<option class="data-text" value="<?php print_r($tables['id']); ?>" data-text-en="<?php print_r($tables['description_name_en']); ?>" data-text-fa="<?php print_r($tables['description_name_fa']); ?>">';
								$string_extra+=(language=="en" ? "<?php print_r($tables['description_name_en']); ?>":"<?php print_r($tables['description_name_fa']); ?>");
							$string_extra+='</option>';
						<?php
								}
							}
						?>
						$string_extra+='</select>';
						$string_extra+='</div>';
						$string_extra+='<div class="col-lg-6 col-12 row mb-2 ml-auto mr-auto">';
						$string_extra+='<select disabled class="selectpicker data-title col-lg-4 col-12 mb-2 ml-auto mr-auto connected_name" data-title-en="Columns" data-title-fa="ستون ها" data-style="btn btn-primary" title="'+(language=="en" ? "Columns":"ستون ها")+'" data-size="7" data-live-search="true">';
						$string_extra+='<option selected class="data-text" data-text-en="Please select a table" data-text-fa="لطفاً یک جدول انتخاب کنید" value="-">'+(language=="en" ? "Please select a table":"لطفاً یک جدول انتخاب کنید")+'</option>';
						$string_extra+='</select>';
						$string_extra+='<select disabled class="selectpicker data-title col-lg-4 col-12 mb-2 ml-auto mr-auto connected_value" data-title-en="Columns" data-title-fa="ستون ها" data-style="btn btn-primary" title="'+(language=="en" ? "Columns":"ستون ها")+'" data-size="7" data-live-search="true">';
						$string_extra+='<option selected class="data-text" data-text-en="Please select a table" data-text-fa="لطفاً یک جدول انتخاب کنید" value="-">'+(language=="en" ? "Please select a table":"لطفاً یک جدول انتخاب کنید")+'</option>';
						$string_extra+='</select>';
						$string_extra+='<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto option_name_div hide">';
						$string_extra+='<div class="form-group">';
						$string_extra+='<input class="form-control data-placeholder data-original-title option_name select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Name" data-original-title-fa="نام گزینه" data-original-title="'+(language == "en" ? "Option Name" : "نام گزینه")+'" data-placement="top" data-placeholder-en="Option Name" data-placeholder-fa="نام گزینه" placeholder="'+(language=="en" ? "Option Name":"نام گزینه")+'" type="text">';
						$string_extra+='</div>';
						$string_extra+='</div>';
						$string_extra+='<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto option_value_div hide">';
						$string_extra+='<div class="form-group">';
						$string_extra+='<input class="form-control data-placeholder data-original-title option_value select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Value" data-original-title-fa="مقدار گزینه" data-original-title="'+(language == "en" ? "Option Value" : "مقدار گزینه")+'" data-placement="top" data-placeholder-en="Option Value" data-placeholder-fa="مقدار گزینه" placeholder="'+(language=="en" ? "Option Value":"مقدار گزینه")+'" type="text">';
						$string_extra+='</div>';
						$string_extra+='</div>';
						$string_extra+='<select class="selectpicker data-title col-lg-4 col-12 optgroup_id" data-title-en="Optgroups" data-title-fa="سرگروه ها" data-style="btn btn-primary" title="'+(language=="en" ? "Optgroups":"سرگروه ها")+'" data-size="7" data-live-search="true">';
						$string_extra+='<option value="-" selected class="data-text" data-text-en="None of them" data-text-fa="هیچکدام">';
							$string_extra+=(language=="en" ? "None of them":"هیچکدام");
						$string_extra+='</option>';
						$string_extra+='<option value="*" class="data-text" data-text-en="New Optgroup" data-text-fa="سرگروه جدید">'+(language=="en" ? "New Optgroup":"سرگروه جدید")+'</option>';
						$string_extra+='</select>';
						$string_extra+='<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto optgroup_new_div hide">';
						$string_extra+='<div class="input-group">';
						$string_extra+='<input class="form-control data-placeholder data-original-title new_optgroup_text select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Optgroup Text" data-original-title-fa="متن سرگروه" data-original-title="'+(language == "en" ? "Optgroup Text" : "متن سرگروه")+'" data-placement="top" data-placeholder-en="Optgroup Text" data-placeholder-fa="متن سرگروه" placeholder="'+(language=="en" ? "Optgroup Text":"متن سرگروه")+'" type="text">';
						$string_extra+='<div class="input-group-append cursor-pointer optgroup-list">';
						$string_extra+='<div class="input-group-text">';
						$string_extra+='<i class="fad fa-times"></i>';
						$string_extra+='</div>';
						$string_extra+='</div>';
						$string_extra+='</div>';
						$string_extra+='</div>';
						$string_extra+='<div class="col-12 mb-2 ml-auto mr-auto optgroup_text_div hide">';
						$string_extra+='<div class="form-group">';
						$string_extra+='<input class="form-control data-placeholder data-original-title optgroup_text select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Optgroup Text" data-original-title-fa="متن سرگروه" data-original-title="'+(language == "en" ? "Optgroup Text" : "متن سرگروه")+'" data-placement="top" data-placeholder-en="Optgroup Text" data-placeholder-fa="متن سرگروه" placeholder="'+(language=="en" ? "Optgroup Text":"متن سرگروه")+'" type="text">';
						$string_extra+='</div>';
						$string_extra+='</div>';
						$string_extra+='</div>';
						$string_extra+='<div class="col-12 row mb-2 ml-auto mr-auto">';
						$string_extra+='<i class="fas fa-plus-octagon display-4 mt-2 '+(language=="en" ? "ml-auto mr-1":"mr-auto ml-1")+' text-success cursor-pointer data-original-title add_select_options" rel="tooltip" data-original-title-en="Add Option" data-original-title-fa="افزودن گزینه" data-original-title="'+(language == "en" ? "Add Option" : "افزودن گزینه")+'"></i>';
						$string_extra+='<i class="fas fa-eraser display-4 mt-2 '+(language=="en" ? "ml-1 mr-auto":"mr-1 ml-auto")+' text-warning cursor-pointer data-original-title clear_select_options" rel="tooltip" data-original-title-en="Clear Option" data-original-title-fa="پاکسازی گزینه" data-original-title="'+(language == "en" ? "Clear Option" : "پاکسازی گزینه")+'"></i>';
						$string_extra+='';
						$string_extra+='<i class="fad fa-save display-4 mt-2 '+(language=="en" ? "ml-auto mr-1":"mr-auto ml-1")+' text-success cursor-pointer data-original-title save_select_options hide" rel="tooltip" data-original-title-en="Save Option" data-original-title-fa="ذخیره گزینه" data-original-title="'+(language == "en" ? "Save Option" : "ذخیره گزینه")+'"></i>';
						$string_extra+='<i class="fad fa-clone display-4 mt-2 ml-1 mr-1 text-info cursor-pointer data-original-title copy_select_options hide" rel="tooltip" data-original-title-en="Copy Option" data-original-title-fa="کپی گزینه" data-original-title="'+(language == "en" ? "Copy Option" : "کپی گزینه")+'"></i>';
						$string_extra+='<i class="fad fa-forward display-4 mt-2 ml-1 mr-1 cursor-pointer data-original-title skip_select_options hide" rel="tooltip" data-original-title-en="Skip Editing" data-original-title-fa="رد کردن ویرایش" data-original-title="'+(language == "en" ? "Skip Editing" : "رد کردن ویرایش")+'"></i>';
						$string_extra+='<i class="fas fa-times-octagon display-4 mt-2 '+(language=="en" ? "ml-1 mr-auto":"mr-1 ml-auto")+' text-danger cursor-pointer data-original-title delete_select_options hide" rel="tooltip" data-original-title-en="Delete Option" data-original-title-fa="حذف گزینه" data-original-title="'+(language == "en" ? "Delete Option" : "حذف گزینه")+'"></i>';
						$string_extra+='</div>';
						$string_extra+='<!-- table of select options -->';
						$string_extra+='<div class="col-12">';
							$string_extra+='<table class="table table-striped w-100 selectOpt_table">';
								$string_extra+='<thead>';
									$string_extra+='<tr>';
										$string_extra+='<th data-priority="4">';
											$string_extra+='<label class="data-text" data-text-en="Optgroup" data-text-fa="سرگروه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Optgroup":"سرگروه")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th data-priority="1">';
											$string_extra+='<label class="data-text" data-text-en="Name" data-text-fa="نام" style="margin-bottom: 0px !important;">'+(language=="en" ? "Name":"نام")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th data-priority="6">';
											$string_extra+='<label class="data-text" data-text-en="Value" data-text-fa="مقدار" style="margin-bottom: 0px !important;">'+(language=="en" ? "Value":"مقدار")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th data-priority="3">';
											$string_extra+='<label class="data-text" data-text-en="Type" data-text-fa="حالت" style="margin-bottom: 0px !important;">'+(language=="en" ? "Type":"حالت")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th data-priority="2">';
											$string_extra+='<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات">'+(language=="en" ? "Operation":"عملیات")+'</label>';
										$string_extra+='</th>';
									$string_extra+='</tr>';
								$string_extra+='</thead>';
								$string_extra+='<tbody>';
									$string_extra+='';
								$string_extra+='</tbody>';
								$string_extra+='<tfoot>';
									$string_extra+='<tr>';
										$string_extra+='<th>';
											$string_extra+='<label class="data-text" data-text-en="Optgroup" data-text-fa="سرگروه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Optgroup":"سرگروه")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th>';
											$string_extra+='<label class="data-text" data-text-en="Name" data-text-fa="نام" style="margin-bottom: 0px !important;">'+(language=="en" ? "Name":"نام")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th>';
											$string_extra+='<label class="data-text" data-text-en="Value" data-text-fa="مقدار" style="margin-bottom: 0px !important;">'+(language=="en" ? "Value":"مقدار")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th>';
											$string_extra+='<label class="data-text" data-text-en="Type" data-text-fa="حالت" style="margin-bottom: 0px !important;">'+(language=="en" ? "Type":"حالت")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th>';
											$string_extra+='<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات">'+(language=="en" ? "Operation":"عملیات")+'</label>';
										$string_extra+='</th>';
									$string_extra+='</tr>';
								$string_extra+='</tfoot>';
							$string_extra+='</table>';
						$string_extra+='</div>';
					$string_extra+='</div>';
				}
			break;
			case "7":case 7://info search case 7 for see all things about this part
				if($input_extra_val.length!=2){
					$input_extra_val=["",""];
				}
				$string_extra+='<div class="col-md-6 col-12">';
					$string_extra+='<div class="form-group">';
						$string_extra+='<input class="form-control data-placeholder data-original-title input_size_limit input_text on-press-enter-column" rel="tooltip" data-original-title-en="Size Limit MB (0 = unlimited)" data-original-title-fa="محدودیت سایز MB (0 = نامحدود)" data-original-title="'+(language=="en" ? "Size Limit MB (0 = unlimited)" : "محدودیت سایز MB (0 = نامحدود)")+'" data-placement="top" data-placeholder-en="Size Limit MB (0 = unlimited)" data-placeholder-fa="محدودیت سایز MB (0 = نامحدود)" placeholder="'+(language=="en" ? "Size Limit MB (0 = unlimited)":"محدودیت سایز MB (0 = نامحدود)")+'" type="text" data-reset-value="'+$input_extra_val[0]+'" value="'+$input_extra_val[0]+'">';
					$string_extra+='</div>';
				$string_extra+='</div>';
				$string_extra+='<div class="col-md-6 col-12 text-center-custom">';
					$string_extra+='<input class="form-control tagsinput data-placeholder data-original-title input_file_types_limit input_text on-press-enter-column" rel="tooltip" data-original-title-en="Format (empty = all) , (.jpg,...)" data-original-title-fa="پسوند (خالی = همه) (.jpg,...)" data-original-title="'+(language=="en" ? "Format (empty = all) , (.jpg,...)" : "پسوند (خالی = همه) (.jpg,...)")+'" data-placement="top" data-placeholder-en="Format (empty = all) , (.jpg,...)" data-placeholder-fa="پسوند (خالی = همه) (.jpg,...)" placeholder="'+(language=="en" ? "Format (empty = all) , (.jpg,...)":"پسوند (خالی = همه) (.jpg,...)")+'" type="text" data-reset-value="'+$input_extra_val[1]+'" value="'+$input_extra_val[1]+'">';
				$string_extra+='</div>';
				$string_extra+='<script>$(".'+$isSaveds+'").find(".input_file_types_limit").tagsinput();<' + '/' + 'script>';
			break;
			case "9":case 9://info search case 9 for see all things about this part
				if($isSaveds=="new-column"){
					$string_extra='<div class="alert alert-info col-12">';
						$string_extra+='<span class="data-text" data-text-en="You have to save this column to appear this part" data-text-fa="برای نمایان شدن این قسمت باید این ستون را ذخیره نمایید">'+(language=="en" ? "You have to save this column to see this part":"برای نمایان شدن این قسمت باید این ستون را ذخیره نمایید")+'</span>';
					$string_extra+='</div>';
				}else if($isSaveds=="saved_column"){
					if($input_extra_val.length!=2){
						$input_extra_val=[0,0];
					}
					$string_extra='<div class="col-md-12 row mr-auto ml-auto pb-3">';
						$string_extra+='<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto">';
							$string_extra+='<div class="form-check cursor-pointer">';
								$string_extra+='<label class="form-check-label">';
									$string_extra+='<input class="form-check-input input_is_multiple input_checkbox" '+($input_extra_val[0].toString()=="1" ? "checked":"")+' type="checkbox" data-reset-value="">';
									$string_extra+='<span class="form-check-sign"></span>';
									$string_extra+='<label class="data-text" data-text-en="Multiple ?" data-text-fa="چندتایی ؟">'+(language=="en" ? "Multiple ?" : "چندتایی ؟")+'</label>';
								$string_extra+='</label>';
							$string_extra+='</div>';
						$string_extra+='</div>';
						$string_extra+='<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto hide">';
							$string_extra+='<div class="form-check cursor-pointer">';
								$string_extra+='<label class="form-check-label">';
									$string_extra+='<input class="form-check-input input_is_forced input_checkbox" '+/*($input_extra_val[1].toString()=="1" ? "checked":"")*/'checked'+' type="checkbox" data-reset-value="">';
									$string_extra+='<span class="form-check-sign"></span>';
									$string_extra+='<label class="data-text" data-text-en="Forced ?" data-text-fa="اجباری ؟">'+(language == "en" ? "Forced ?" : "اجباری ؟")+'</label>';
								$string_extra+='</label>';
							$string_extra+='</div>';
						$string_extra+='</div>';
					$string_extra+='</div>';
					$string_extra+='<div class="col-md-12 row mr-auto ml-auto">';
						$string_extra+='<div class="form-group col-4">';
							$string_extra+='<input class="form-control data-placeholder data-original-title checkbox_name checkbox-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Name" data-original-title-fa="نام گزینه" data-original-title="'+(language=="en" ? "Option Name" : "نام گزینه")+'" data-placement="top" data-placeholder-en="Option Name" data-placeholder-fa="نام گزینه" placeholder="'+(language=="en" ? "Option Name":"نام گزینه")+'" type="text">';
						$string_extra+='</div>';
						$string_extra+='<div class="form-group col-4">';
							$string_extra+='<input class="form-control data-placeholder data-original-title checkbox_value checkbox-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Value" data-original-title-fa="خروجی گزینه" data-original-title="'+(language=="en" ? "Option Value" : "خروجی گزینه")+'" data-placement="top" data-placeholder-en="Option Value" data-placeholder-fa="خروجی گزینه" placeholder="'+(language=="en" ? "Option Value":"خروجی گزینه")+'" type="text">';
						$string_extra+='</div>';
						$string_extra+='<div class="form-group col-4">';
							$string_extra+='<input class="form-control data-placeholder data-original-title checkbox_false checkbox-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option False" data-original-title-fa="خروجی منفی گزینه" data-original-title="'+(language=="en" ? "Option False" : "خروجی منفی گزینه")+'" data-placement="top" data-placeholder-en="Option False" data-placeholder-fa="خروجی منفی گزینه" placeholder="'+(language=="en" ? "Option False":"خروجی منفی گزینه")+'" type="text">';
						$string_extra+='</div>';
						$string_extra+='<div class="col-12 row mb-2 ml-auto mr-auto">';
							$string_extra+='<i class="fas fa-plus-octagon display-4 mt-2 '+(language=="en" ? "ml-auto mr-1":"mr-auto ml-1")+' text-success cursor-pointer data-original-title add_checkbox_options" rel="tooltip" data-original-title-en="Add Option" data-original-title-fa="افزودن گزینه" data-original-title="'+(language=="en" ? "Add Option" : "افزودن گزینه")+'"></i>';
							$string_extra+='<i class="fas fa-eraser display-4 mt-2 '+(language=="en" ? "ml-1 mr-auto":"mr-1 ml-auto")+' text-warning cursor-pointer data-original-title clear_checkbox_options" rel="tooltip" data-original-title-en="Clear Option" data-original-title-fa="پاکسازی گزینه" data-original-title="'+(language=="en" ? "Clear Option" : "پاکسازی گزینه")+'"></i>';
							$string_extra+='<i class="fad fa-save display-4 mt-2 '+(language=="en" ? "ml-auto mr-1":"mr-auto ml-1")+' text-success cursor-pointer data-original-title save_checkbox_options hide" rel="tooltip" data-original-title-en="Save Option" data-original-title-fa="ذخیره گزینه" data-original-title="'+(language=="en" ? "Save Option" : "ذخیره گزینه")+'"></i>';
							$string_extra+='<i class="fad fa-clone display-4 mt-2 ml-1 mr-1 text-info cursor-pointer data-original-title copy_checkbox_options hide" rel="tooltip" data-original-title-en="Copy Option" data-original-title-fa="کپی گزینه" data-original-title="'+(language=="en" ? "Copy Option" : "کپی گزینه")+'"></i>';
							$string_extra+='<i class="fad fa-forward display-4 mt-2 ml-1 mr-1 cursor-pointer data-original-title skip_checkbox_options hide" rel="tooltip" data-original-title-en="Skip Editing" data-original-title-fa="رد کردن ویرایش" data-original-title="'+(language=="en" ? "Skip Editing" : "رد کردن ویرایش")+'"></i>';
							$string_extra+='<i class="fas fa-times-octagon display-4 mt-2 '+(language=="en" ? "ml-1 mr-auto":"mr-1 ml-auto")+' text-danger cursor-pointer data-original-title delete_checkbox_options hide" rel="tooltip" data-original-title-en="Delete Option" data-original-title-fa="حذف گزینه" data-original-title="'+(language=="en" ? "Delete Option" : "حذف گزینه")+'"></i>';
						$string_extra+='</div>';
						$string_extra+='<!-- table of select options -->';
						$string_extra+='<div class="col-12">';
							$string_extra+='<table class="table table-striped w-100 checkboxOpt_table">';
								$string_extra+='<thead>';
									$string_extra+='<tr>';
										$string_extra+='<th data-priority="4">';
											$string_extra+='<label class="data-text" data-text-en="Option Name" data-text-fa="نام گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option Name":"نام گزینه")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th data-priority="1">';
											$string_extra+='<label class="data-text" data-text-en="Option Value" data-text-fa="خروجی گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option Value":"خروجی گزینه")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th data-priority="6">';
											$string_extra+='<label class="data-text" data-text-en="Option false value" data-text-fa="خروجی منفی گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option false value":"خروجی منفی گزینه")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th data-priority="2">';
											$string_extra+='<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات">'+(language=="en" ? "Operation":"عملیات")+'</label>';
										$string_extra+='</th>';
									$string_extra+='</tr>';
								$string_extra+='</thead>';
								$string_extra+='<tbody>';
									$string_extra+='<!---->';
								$string_extra+='</tbody>';
								$string_extra+='<tfoot>';
									$string_extra+='<tr>';
										$string_extra+='<th>';
											$string_extra+='<label class="data-text" data-text-en="Option Name" data-text-fa="نام گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option Name":"نام گزینه")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th>';
											$string_extra+='<label class="data-text" data-text-en="Option Value" data-text-fa="خروجی گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option Value":"خروجی گزینه")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th>';
											$string_extra+='<label class="data-text" data-text-en="Option false value" data-text-fa="خروجی منفی گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option false value":"خروجی منفی گزینه")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th>';
											$string_extra+='<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات">'+(language=="en" ? "Operation":"عملیات")+'</label>';
										$string_extra+='</th>';
									$string_extra+='</tr>';
								$string_extra+='</tfoot>';
							$string_extra+='</table>';
						$string_extra+='</div>';
					$string_extra+='</div>';
				}
			break;

		}
		$.when($("#stack-of-columns").append(
			'<div class="row justify-content-center text-center mt-3 mb-3 pt-3 pb-3 create-columns-manager '+$got_id+' '+$isSaveds+' column-saver"> ' +
				'<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12"> ' +
					'<div class="form-group"> ' +
						'<input class="form-control data-placeholder data-original-title input_current_name input_text on-press-enter-column convert-to-lowercase just-english force-left" rel="tooltip" data-original-title-en="Column name" data-original-title-fa="نام ستون" data-original-title="'+(language=="en" ? "Column name":"نام ستون")+'" data-placement="top" data-placeholder-en="Column name" data-placeholder-fa="نام ستون" placeholder="'+(language=="en" ? "Column name":"نام ستون")+'" type="text" data-reset-value="'+$current_name+'" value="'+$current_name+'"> ' +
					'</div> ' +
				'</div> ' +
				'<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12"> ' +
					'<div class="form-group"> ' +
						'<input class="form-control data-placeholder data-original-title input_description_name_fa input_text on-press-enter-column force-right '+($description_name_fa.length ? "text-right font-persian":"")+'" rel="tooltip" data-original-title-en="Persian descriptive name" data-original-title-fa="نام توصیفی فارسی" data-original-title="'+(language=="en" ? "Persian descriptive name":"نام توصیفی فارسی")+'" data-placement="top" data-placeholder-en="Persian descriptive name" data-placeholder-fa="نام توصیفی فارسی" placeholder="'+(language=="en" ? "Persian descriptive name":"نام توصیفی فارسی")+'" type="text" data-reset-value="'+$description_name_fa+'" value="'+$description_name_fa+'"> ' +
					'</div> ' +
				'</div> ' +
				'<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12"> ' +
					'<div class="form-group"> ' +
						'<input class="form-control data-placeholder data-original-title input_description_name_en input_text on-press-enter-column force-left '+($description_name_en.length ? "text-left font-english":"")+'" rel="tooltip" data-original-title-en="English descriptive name" data-original-title-fa="نام توصیفی انگلیسی" data-original-title="'+(language=="en" ? "English descriptive name":"نام توصیفی انگلیسی")+'" data-placement="top" data-placeholder-en="English descriptive name" data-placeholder-fa="نام توصیفی انگلیسی" placeholder="'+(language=="en" ? "English descriptive name":"نام توصیفی انگلیسی")+'" type="text" data-reset-value="'+$description_name_en+'" value="'+$description_name_en+'"> ' +
					'</div> ' +
				'</div> ' +
				'<div class="col-12"> ' +
					'<div class="form-group"> ' +
						'<textarea class="form-control data-placeholder data-original-title input_description_info_fa input_text on-press-enter-column force-right '+($description_info_fa.length ? "text-right font-persian":"")+'" rel="tooltip" data-original-title-en="Persian description" data-original-title-fa="توضیحات فارسی" data-original-title="'+(language=="en" ? "Persian description":"توضیحات فارسی")+'" data-placement="top" data-placeholder-en="Persian description" data-placeholder-fa="توضیحات فارسی" placeholder="'+(language=="en" ? "Persian description":"توضیحات فارسی")+'" type="text" data-reset-value="'+$description_info_fa+'">'+$description_info_fa+'</textarea> ' +
					'</div> ' +
				'</div> ' +
				'<div class="col-12"> ' +
					'<div class="form-group"> ' +
						'<textarea class="form-control data-placeholder data-original-title input_description_info_en input_text on-press-enter-column force-left '+($description_info_en.length ? "text-left font-english":"")+'" rel="tooltip" data-original-title-en="English description" data-original-title-fa="توضیحات انگلیسی" data-original-title="'+(language=="en" ? "English description":"توضیحات انگلیسی")+'" data-placement="top" data-placeholder-en="English description" data-placeholder-fa="توضیحات انگلیسی" placeholder="'+(language=="en" ? "English description":"توضیحات انگلیسی")+'" type="text" data-reset-value="'+$description_info_en+'">'+$description_info_en+'</textarea> ' +
					'</div> ' +
				'</div> ' +
				'<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios"> ' +
					'<div class="form-check cursor-pointer"> ' +
						'<label class="form-check-label"> ' +
							'<input '+($input_creatable ? "checked":($isSaveds=="new-column" ? "checked":""))+' class="form-check-input input_creatable input_checkbox" type="checkbox" data-reset-value="'+($input_creatable ? "1":($isSaveds=="new-column" ? "1":""))+'"> ' +
							'<span class="form-check-sign"></span> ' +
							'<label class="data-text" data-text-en="Insertable ?" data-text-fa="قابل درج ؟">'+(language=="en" ? "Insertable ?":"قابل درج ؟")+'</label> ' +
						'</label> ' +
					'</div> ' +
				'</div> ' +
				'<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios"> ' +
					'<div class="form-check cursor-pointer"> ' +
						'<label class="form-check-label"> ' +
							'<input '+($input_visible ? "checked":($isSaveds=="new-column" ? "checked":""))+' class="form-check-input input_visible input_checkbox" type="checkbox" data-reset-value="'+($input_visible ? "1":($isSaveds=="new-column" ? "1":""))+'"> ' +
							'<span class="form-check-sign"></span> ' +
							'<label class="data-text" data-text-en="Visible ?" data-text-fa="نمایان ؟">'+(language=="en" ? "Visible ?":"نمایان ؟")+'</label> ' +
						'</label> ' +
					'</div> ' +
				'</div> ' +
				'<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios"> ' +
					'<div class="form-check cursor-pointer"> ' +
						'<label class="form-check-label"> ' +
							'<input '+($input_editable ? "checked":($isSaveds=="new-column" ? "checked":""))+' class="form-check-input input_editable input_checkbox" type="checkbox" data-reset-value="'+($input_editable ? "1":($isSaveds=="new-column" ? "1":""))+'"> ' +
							'<span class="form-check-sign"></span> ' +
							'<label class="data-text" data-text-en="Editable ?" data-text-fa="قابل ویرایش ؟">'+(language=="en" ? "Editable ?":"قابل ویرایش ؟")+'</label> ' +
						'</label> ' +
					'</div> ' +
				'</div> ' +
				'<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios"> ' +
					'<div class="form-check cursor-pointer"> ' +
						'<label class="form-check-label"> ' +
							'<input '+($input_removable ? "checked":($isSaveds=="new-column" ? "checked":""))+' class="form-check-input input_removable input_checkbox" type="checkbox" data-reset-value="'+($input_removable ? "1":($isSaveds=="new-column" ? "1":""))+'"> ' +
							'<span class="form-check-sign"></span> ' +
							'<label class="data-text" data-text-en="Removable ?" data-text-fa="قابل حذف ؟">'+(language=="en" ? "Removable ?":"قابل حذف ؟")+'</label> ' +
						'</label> ' +
					'</div> ' +
				'</div> ' +
				'<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios"> ' +
					'<div class="form-check cursor-pointer"> ' +
						'<label class="form-check-label"> ' +
							'<input '+($input_visible_table ? "checked":"")+' class="form-check-input input_visible_table input_checkbox" type="checkbox" data-reset-value="'+($input_visible_table ? "1":"")+'"> ' +
							'<span class="form-check-sign"></span> ' +
							'<label class="data-text" data-text-en="Visible in table ?" data-text-fa="نمایان در جدول ؟">'+(language=="en" ? "Visible in table ?":"نمایان در جدول ؟")+'</label> ' +
						'</label> ' +
					'</div> ' +
				'</div> ' +
				'<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12"> ' +
					'<div class="form-group"> ' +
						'<select class="selectpicker data-title input_mode input_select" data-title-en="Modes" data-title-fa="حالت ها" data-style="btn btn-primary" title="'+(language=="en" ? "Modes":"حالت ها")+'" data-size="7" data-live-search="true" data-reset-value="'+$input_select+'"> ' +
							'<?php
								$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_column_mode");
								while($tables=$res_tables->fetch()){
							?> ' +
									'<option class="data-text" '+($input_select=="<?php print_r($tables["id"]); ?>" ? "selected":"")+' value="<?php print_r($tables["id"]); ?>" data-text-en="<?php print_r($tables["description_name_en"]); ?>" data-text-fa="<?php print_r($tables["description_name_fa"]); ?>"> ' + (language=="en" ? "<?php print_r($tables["description_name_en"]); ?>":"<?php print_r($tables["description_name_fa"]); ?>") +
									'</option> ' +
							'<?php
								}
							?> ' +
						'</select> ' +
					'</div> ' +
				'</div> ' +
				'<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios"> ' +
					'<div class="form-check cursor-pointer"> ' +
						'<label class="form-check-label"> ' +
							'<input '+($input_important ? "checked":"")+' class="form-check-input input_important input_checkbox" type="checkbox" data-reset-value="'+($input_important ? "1":"")+'"> ' +
							'<span class="form-check-sign"></span> ' +
							'<label class="data-text" data-text-en="Important ?" data-text-fa="پر اهمیت ؟">'+(language=="en" ? "Important ?":"پر اهمیت ؟")+'</label> ' +
						'</label> ' +
					'</div> ' +
				'</div> ' +
				'<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 form-check-radio checkbox-radios"> ' +
					'<div class="form-check cursor-pointer"> ' +
						'<label class="form-check-label"> ' +
							'<input '+($input_primary ? "checked":"")+' class="form-check-input input_primary input_checkbox" type="radio" name="primary" data-reset-value=""> ' +
							'<span class="form-check-sign"></span> ' +
							'<label class="data-text" data-text-en="Primary?" data-text-fa="اصلی؟">'+(language=="en" ? "Primary ?":"اصلی ؟")+'</label> ' +
						'</label> ' +
					'</div> ' +
				'</div> ' +
				'<div class="col-12 extra-options row"> ' +
					$string_extra +
				'</div> ' +
				'<div class="col-12 mt-2"> ' +
					(language=="en" ? '<?php print_r(str_replace("'","'+\"'\"+'",$primary_buttons_en)); ?>':'<?php print_r(str_replace("'","'+\"'\"+'",$primary_buttons_fa)); ?>') +
				'</div> ' +
			'</div>'
		)).done(function(){
			$.when($(".selectpicker").selectpicker({iconBase: "tim-icons",tickIcon: "icon-check-2"})).done(function(){
				$.when($("#stack-of-columns").children("div:last").addClass("has-info")).done(function(){
					$.when(bsSwitcher()).done(function(){
						if($noScroll!=1){
							$.when(scrollToElement($("#stack-of-columns").children("div:last"))).done(function(){
								$("#stack-of-columns").children("div:last").each(function () {
									if($isSaveds=="saved_column"){
										$element=$(this);
										var $savedIDs="";
										$element.attr("class").split(" ").forEach(function(class_name){
											$class_name=class_name.split("-");
											if($class_name[0]=="column_saved_id"){
												$savedIDs=$class_name[1];
											}
										});
										$element.find("select.optgroup_id").load("table/class/action.php?optgroups", { "column_id": $savedIDs },function () {
											$element.find("select.optgroup_id").selectpicker("val","-").selectpicker("refresh").change();
										});
										if($input_select==4 || $input_select=="4"){
											callDataTable_selectOpt_table($element,$savedIDs);
										}
										if($input_select==9 || $input_select=="9"){
											callDataTable_checkboxOpt_table($element,$savedIDs);
										}
									}
									setTimeout(() => {
										$(this).removeClass("has-info");
										if($operation=="editOthers_save"){
											$(this).find("button.save-button-operation").click();
										}
									}, 1300);
								});
							});
						}else{
							$("#stack-of-columns").children("div:last").each(function () {
								if($isSaveds=="saved_column"){
									$element=$(this);
									var $savedIDs="";
									$element.attr("class").split(" ").forEach(function(class_name){
										$class_name=class_name.split("-");
										if($class_name[0]=="column_saved_id"){
											$savedIDs=$class_name[1];
										}
									});
									$element.find("select.optgroup_id").load("table/class/action.php?optgroups", { "column_id": $savedIDs },function () {
										$element.find("select.optgroup_id").selectpicker("val","-").selectpicker("refresh").change();
									});
									if($input_select==4 || $input_select=="4"){
										callDataTable_selectOpt_table($element,$savedIDs);
									}
									if($input_select==9 || $input_select=="9"){
										callDataTable_checkboxOpt_table($element,$savedIDs);
									}
								}
								setTimeout(() => {
									$(this).removeClass("has-info");
									if($operation=="editOthers_save"){
											$(this).find("button.save-button-operation").click();
										}
								}, 1300);
							});
						}
					});
				});
			});
		});
	}

	function createButtonOperations($operation,$element,$button,$isScroll) {
		var $button_text=($button.length ? $button.text():"");
		var $isNew=($element.length ? ($element.hasClass("new-column") ? 1:0):0);
		var $isSaved=($element.length ? ($element.hasClass("saved_column") ? 1:0):0),$savedID=0;
		if($element.length){
			$element.attr("class").split(" ").forEach(function(class_name){
				$class_name=class_name.split("-");
				if($class_name[0]=="column_saved_id"){
					$savedID=$class_name[1];
				}
			});
		}

		function changeColumnStatus($element,$button,$status) {
			if($operation!="add"){
				$element.removeClass("has-success has-danger has-loading has-info");
			}
			switch ($status) {
				case "info":
					if($element.length){
						$element.addClass("has-info");$button.html((language=="en" ? "Done":"انجام شد")+" <label class='fas fa-check'></label>");
					}
				break;
				case "success":
					if($element.length){
						$element.addClass("has-success");$button.html((language=="en" ? "Done":"انجام شد")+" <label class='fas fa-check'></label>");
					}
				break;
				case "danger":
					if($element.length){
						$element.addClass("has-danger");$button.html((language=="en" ? "Failed":"ناموفق")+" <label class='fas fa-exclamation-triangle'></label>").addClass("shaker btn-danger");
						if($isScroll){scrollToElement($element);}
					}
				break;
				case "loading":
					if($element.length){
						$element.addClass("has-loading");
					}
					$button.html("<label class='fad fa-spin fa-spinner-third'></label>").attr("disabled",true);
				break;
			}
			if($status!="loading"){
				setTimeout(() => {
					$button.html($button_text).attr("disabled",false).removeClass((($button.hasClass("shaker") && $button.attr("onclick").indexOf("delete")==-1) ? "shaker btn-danger":"shaker"));
					if($element.length){
						if($isScroll){
							$element.removeClass("has-success has-danger has-loading has-info");
						}else{
							$element.removeClass("has-success has-info");
							if($status!="danger"){
								$element.removeClass("has-danger");
							}
						}
					}
				}, 700);
			}
		}

		changeColumnStatus($element,$button,"loading");

		var $current_name=($element.length ? ($element.find(".input_current_name").length ? $element.find(".input_current_name"):""):""),
			$description_name_fa=($element.length ? ($element.find(".input_description_name_fa").length ? $element.find(".input_description_name_fa"):""):""),
			$description_info_fa=($element.length ? ($element.find(".input_description_info_fa").length ? $element.find(".input_description_info_fa"):""):""),
			$description_name_en=($element.length ? ($element.find(".input_description_name_en").length ? $element.find(".input_description_name_en"):""):""),
			$description_info_en=($element.length ? ($element.find(".input_description_info_en").length ? $element.find(".input_description_info_en"):""):""),
			$input_select=($element.length ? ($element.find("select.input_select").length ? $element.find("select.input_select"):""):""),
			$input_creatable=($element.length ? ($element.find(".input_creatable").length ? $element.find(".input_creatable"):""):""),
			$input_visible=($element.length ? ($element.find(".input_visible").length ? $element.find(".input_visible"):""):""),
			$input_editable=($element.length ? ($element.find(".input_editable").length ? $element.find(".input_editable"):""):""),
			$input_removable=($element.length ? ($element.find(".input_removable").length ? $element.find(".input_removable"):""):""),
			$input_visible_table=($element.length ? ($element.find(".input_visible_table").length ? $element.find(".input_visible_table"):""):""),
			$input_primary=($element.length ? ($element.find(".input_primary").length ? $element.find(".input_primary"):""):""),
			$input_important=($element.length ? ($element.find(".input_important").length ? $element.find(".input_important"):""):""),
			$input_extra="";
			if($input_select.length){
				<?php //info search this_is_modes_for_data_tables for see all things about this part ?>
				switch ($input_select.val()) {//tables_mode_code
					case "3":case 3://info search case 3 for see all things about this part
						$input_extra=[(typeof $element.find(".input_yes_option") !== "undefined" && $element.find(".input_yes_option").length ? $element.find(".input_yes_option").val():""),(typeof $element.find(".input_no_option") !== "undefined" && $element.find(".input_no_option").length ? $element.find(".input_no_option").val():""),(typeof $element.find(".input_yes_value") !== "undefined" && $element.find(".input_yes_value").length ? ($element.find(".input_yes_value").val().length ? $element.find(".input_yes_value").val():1):""),(typeof $element.find(".input_no_value") !== "undefined" && $element.find(".input_no_value").length ? ($element.find(".input_no_value").val().length ? $element.find(".input_no_value").val():0):""),(typeof $element.find(".input_yes_icon") !== "undefined" && $element.find(".input_yes_icon").length ? $element.find(".input_yes_icon").val():""),(typeof $element.find(".input_no_icon") !== "undefined" && $element.find(".input_no_icon").length ? $element.find(".input_no_icon").val():"")];
					break;
					case "4":case 4://info search case 4 for see all things about this part
						$input_extra=[(typeof $element.find(".input_is_multiple") !=="undefined" && $element.find(".input_is_multiple").length ? $element.find(".input_is_multiple").is(":checked"):0),(typeof $element.find(".input_is_forced") !=="undefined" && $element.find(".input_is_forced").length ? $element.find(".input_is_forced").is(":checked"):0),(typeof $element.find(".input_min_allowed") !=="undefined" && $element.find(".input_min_allowed").length ? $element.find(".input_min_allowed").val():0),(typeof $element.find(".input_max_allowed") !=="undefined" && $element.find(".input_max_allowed").length ? $element.find(".input_max_allowed").val():0)];
					break;
					case "7":case 7://info search case 7 for see all things about this part
						$input_extra=($element.length ? ($element.find(".input_size_limit").length && $element.find(".input_file_types_limit").length ? [($element.find(".input_size_limit").val().length ? $element.find(".input_size_limit").val():0),$element.find(".input_file_types_limit").val()]:["",""]):"");
					break;
					case "9":case 9://info search case 9 for see all things about this part
						$input_extra=($element.length ? ($element.find(".input_is_multiple").length && $element.find(".input_is_forced").length ? [$element.find(".input_is_multiple").is(":checked"),$element.find(".input_is_forced").is(":checked")]:["",""]):"");
					break;
				}
			}

		switch ($operation) {
			case "add":
				$.when(newEditor('','new-column',($current_name.length ? $current_name.val():""),($description_name_fa.length ? $description_name_fa.val():""),($description_name_en.length ? $description_name_en.val():""),($description_info_fa.length ? $description_info_fa.val():""),($description_info_en.length ? $description_info_en.val():""),($input_creatable.length ? ($input_creatable.is(":checked") ? "checked":""):""),($input_visible.length ? ($input_visible.is(":checked") ? "checked":""):""),($input_editable.length ? ($input_editable.is(":checked") ? "checked":""):""),($input_removable.length ? ($input_removable.is(":checked") ? "checked":""):""),($input_visible_table.length ? ($input_visible_table.is(":checked") ? "checked":""):""),($input_select.length ? $input_select.val():""),($input_important.length ? ($input_important.is(":checked") ? "checked":""):""),''/* primary disabled because its only one */,$input_extra,0,$operation)).done(function(){
					changeColumnStatus($element,$button,"none");
				});
			break;
			case "save":
			case "save_close":
				var $error_fields=["Filling this field is important !","پر کردن این فیلد اجباریست !"];
				if($current_name.val().replace(/[0-9]/g, '').toString().length>0){
					if($current_name.val()==""){
						$current_name.parent(".form-group").children("label.error").remove();
						$current_name.focus().parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
						changeColumnStatus($element,$button,"danger");
					}else{
						$.when($current_name.parent(".form-group").attr("class","form-group").children("label.error").remove()).done(function(){

							if($description_name_fa.val()==""){
								$description_name_fa.parent(".form-group").children("label.error").remove();
								$description_name_fa.focus().parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
								changeColumnStatus($element,$button,"danger");
							}else{
								$.when($description_name_fa.parent(".form-group").attr("class","form-group").children("label.error").remove()).done(function(){

									if($description_name_en.val()==""){
										$description_name_en.parent(".form-group").children("label.error").remove();
										$description_name_en.focus().parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
										changeColumnStatus($element,$button,"danger");
									}else{
										$.when($description_name_en.parent(".form-group").attr("class","form-group").children("label.error").remove()).done(function(){

											if($input_select.val().length==0){
												callSelectInput($input_select);
												changeColumnStatus($element,$button,"danger");
											}else{

												if($isNew){
													$element.removeClass("new-column");
													var $save_column_error=0;
													$.post("table/class/action.php?saveNewColumn", {
														current_name: $current_name.val(),
														description_name_fa: $description_name_fa.val(),
														description_info_fa: $description_info_fa.val(),
														description_name_en: $description_name_en.val(),
														description_info_en: $description_info_en.val(),
														input_select: $input_select.val(),
														input_creatable: ($input_creatable.is(":checked") ? 1:0),
														input_visible: ($input_visible.is(":checked") ? 1:0),
														input_editable: ($input_editable.is(":checked") ? 1:0),
														input_removable: ($input_removable.is(":checked") ? 1:0),
														input_visible_table: ($input_visible_table.is(":checked") ? 1:0),
														input_primary: ($input_primary.is(":checked") ? 1:0),
														input_important: ($input_important.is(":checked") ? 1:0),
														input_extra: (JSON.stringify($input_extra))
													}, function(data, status) {
														$data=data.split("_._");
														if($data[0]=="success" && status=="success"){
															$.when($element.removeClass("new-column").addClass("column_saved_id-"+$data[1]+" saved_column")).done(function(){
																if($input_select.val()==4 || $input_select.val()=="4"){//info search case 4 for see all things about this part
																	$.when($("select.connected_table").change()).done(function(){
																		$string_extra='<div class="col-md-12 row mr-auto ml-auto pb-3">';
																			$string_extra+='<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto">';
																				$string_extra+='<div class="form-check cursor-pointer">';
																					$string_extra+='<label class="form-check-label">';
																						$string_extra+='<input class="form-check-input input_is_multiple input_checkbox" type="checkbox" data-reset-value="">';
																						$string_extra+='<span class="form-check-sign"></span>';
																						$string_extra+='<label class="data-text" data-text-en="Multiple ?" data-text-fa="چندتایی ؟">'+(language=="en" ? "Multiple ?" : "چندتایی ؟")+'</label>';
																					$string_extra+='</label>';
																				$string_extra+='</div>';
																			$string_extra+='</div>';
																			$string_extra+='<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto hide">';
																				$string_extra+='<div class="form-check cursor-pointer">';
																					$string_extra+='<label class="form-check-label">';
																						$string_extra+='<input class="form-check-input input_is_forced input_checkbox" checked type="checkbox" data-reset-value="">';
																						$string_extra+='<span class="form-check-sign"></span>';
																						$string_extra+='<label class="data-text" data-text-en="Forced ?" data-text-fa="اجباری ؟">'+(language == "en" ? "Forced ?" : "اجباری ؟")+'</label>';
																					$string_extra+='</label>';
																				$string_extra+='</div>';
																			$string_extra+='</div>';
																			<?php
																				/*
																					$string_extra+='<div class="col-lg-3 col-12 mb-2 ml-auto mr-auto">';
																						$string_extra+='<div class="form-group">';
																							$string_extra+='<input class="form-control data-placeholder data-original-title input_min_allowed input_text" rel="tooltip" data-original-title-en="Minimum amount allowed" data-original-title-fa="کمترین مقدار مجاز" data-original-title="'+(language== "en" ? "Minimum amount allowed" : "کمترین مقدار مجاز")+'" data-placement="top" data-placeholder-en="Minimum amount allowed" data-placeholder-fa="کمترین مقدار مجاز" placeholder="'+(language=="en" ? "Minimum amount allowed":"کمترین مقدار مجاز")+'" type="number" data-reset-value="" value="">';
																						$string_extra+='</div>';
																					$string_extra+='</div>';
																					$string_extra+='<div class="col-lg-3 col-12 mb-2 ml-auto mr-auto">';
																						$string_extra+='<div class="form-group">';
																							$string_extra+='<input class="form-control data-placeholder data-original-title input_max_allowed input_text" rel="tooltip" data-original-title-en="Maximum amount allowed" data-original-title-fa="بیشترین مقدار مجاز" data-original-title="'+(language == "en" ? "Maximum amount allowed" : "بیشترین مقدار مجاز")+'" data-placement="top" data-placeholder-en="Maximum amount allowed" data-placeholder-fa="بیشترین مقدار مجاز" placeholder="'+(language=="en" ? "Maximum amount allowed":"بیشترین مقدار مجاز")+'" type="number" data-reset-value="" value="">';
																						$string_extra+='</div>';
																					$string_extra+='</div>';
																				*/
																			?>
																		$string_extra+='</div>';
																		$string_extra+='<div class="col-md-12 row mr-auto ml-auto">';
																			$string_extra+='<div class="col-lg-6 col-12 row mb-2 ml-auto mr-auto">';
																				$string_extra+='<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto">';
																					$string_extra+='<div class="form-check cursor-pointer checkbox_changer">';
																						$string_extra+='<label class="form-check-label">';
																							$string_extra+='<input class="form-check-input is_optgroup_opt" type="checkbox">';
																							$string_extra+='<span class="form-check-sign"></span>';
																							$string_extra+='<label class="data-text" data-text-en="Optgroup ?" data-text-fa="سرگروه ؟">'+(language == "en" ? "Optgroup ?" : "سرگروه ؟")+'</label>';
																						$string_extra+='</label>';
																					$string_extra+='</div>';
																				$string_extra+='</div>';
																				$string_extra+='<select class="selectpicker data-title col-lg-4 col-12 mb-2 ml-auto mr-auto connected_table" data-title-en="Tables" data-title-fa="جدول ها" data-style="btn btn-primary" title="'+(language=="en" ? "Tables":"جدول ها")+'" data-size="7" data-live-search="true">';
																					$string_extra+='<option class="data-text" value="0" data-text-en="Manual" data-text-fa="دستی">';
																						$string_extra+=(language=="en" ? "Manual":"دستی");
																					$string_extra+='</option>';
																					<?php
																						$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_config ORDER BY ordering ASC");
																						while($tables=$res_tables->fetch()){
																							if(checkPermission(1,$tables['id'],"read",$tables['act'],null)==1){
																					?>
																						$string_extra+='<option class="data-text" value="<?php print_r($tables['id']); ?>" data-text-en="<?php print_r($tables['description_name_en']); ?>" data-text-fa="<?php print_r($tables['description_name_fa']); ?>">';
																							$string_extra+=(language=="en" ? "<?php print_r($tables['description_name_en']); ?>":"<?php print_r($tables['description_name_fa']); ?>");
																						$string_extra+='</option>';
																					<?php
																							}
																						}
																					?>
																				$string_extra+='</select>';
																			$string_extra+='</div>';
																			$string_extra+='<div class="col-lg-6 col-12 row mb-2 ml-auto mr-auto">';
																				$string_extra+='<select disabled class="selectpicker data-title col-lg-4 col-12 mb-2 ml-auto mr-auto connected_name" data-title-en="Columns" data-title-fa="ستون ها" data-style="btn btn-primary" title="'+(language=="en" ? "Columns":"ستون ها")+'" data-size="7" data-live-search="true">';
																					$string_extra+='<option selected class="data-text" data-text-en="Please select a table" data-text-fa="لطفاً یک جدول انتخاب کنید" value="-">'+(language=="en" ? "Please select a table":"لطفاً یک جدول انتخاب کنید")+'</option>';
																				$string_extra+='</select>';
																				$string_extra+='<select disabled class="selectpicker data-title col-lg-4 col-12 mb-2 ml-auto mr-auto connected_value" data-title-en="Columns" data-title-fa="ستون ها" data-style="btn btn-primary" title="'+(language=="en" ? "Columns":"ستون ها")+'" data-size="7" data-live-search="true">';
																					$string_extra+='<option selected class="data-text" data-text-en="Please select a table" data-text-fa="لطفاً یک جدول انتخاب کنید" value="-">'+(language=="en" ? "Please select a table":"لطفاً یک جدول انتخاب کنید")+'</option>';
																				$string_extra+='</select>';
																				$string_extra+='<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto option_name_div hide">';
																					$string_extra+='<div class="form-group">';
																						$string_extra+='<input class="form-control data-placeholder data-original-title option_name select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Name" data-original-title-fa="نام گزینه" data-original-title="'+(language == "en" ? "Option Name" : "نام گزینه")+'" data-placement="top" data-placeholder-en="Option Name" data-placeholder-fa="نام گزینه" placeholder="'+(language=="en" ? "Option Name":"نام گزینه")+'" type="text">';
																					$string_extra+='</div>';
																				$string_extra+='</div>';
																				$string_extra+='<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto option_value_div hide">';
																					$string_extra+='<div class="form-group">';
																						$string_extra+='<input class="form-control data-placeholder data-original-title option_value select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Value" data-original-title-fa="مقدار گزینه" data-original-title="'+(language == "en" ? "Option Value" : "مقدار گزینه")+'" data-placement="top" data-placeholder-en="Option Value" data-placeholder-fa="مقدار گزینه" placeholder="'+(language=="en" ? "Option Value":"مقدار گزینه")+'" type="text">';
																					$string_extra+='</div>';
																				$string_extra+='</div>';
																				$string_extra+='<select class="selectpicker data-title col-lg-4 col-12 optgroup_id" data-title-en="Optgroups" data-title-fa="سرگروه ها" data-style="btn btn-primary" title="'+(language=="en" ? "Optgroups":"سرگروه ها")+'" data-size="7" data-live-search="true">';
																					$string_extra+='<option value="-" selected class="data-text" data-text-en="None of them" data-text-fa="هیچکدام">';
																						$string_extra+=(language=="en" ? "None of them":"هیچکدام");
																					$string_extra+='</option>';
																					$string_extra+='<option value="*" class="data-text" data-text-en="New Optgroup" data-text-fa="سرگروه جدید">'+(language=="en" ? "New Optgroup":"سرگروه جدید")+'</option>';
																				$string_extra+='</select>';
																				$string_extra+='<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto optgroup_new_div hide">';
																					$string_extra+='<div class="input-group">';
																						$string_extra+='<input class="form-control data-placeholder data-original-title new_optgroup_text select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Optgroup Text" data-original-title-fa="متن سرگروه" data-original-title="'+(language == "en" ? "Optgroup Text" : "متن سرگروه")+'" data-placement="top" data-placeholder-en="Optgroup Text" data-placeholder-fa="متن سرگروه" placeholder="'+(language=="en" ? "Optgroup Text":"متن سرگروه")+'" type="text">';
																						$string_extra+='<div class="input-group-append cursor-pointer optgroup-list">';
																							$string_extra+='<div class="input-group-text">';
																								$string_extra+='<i class="fad fa-times"></i>';
																							$string_extra+='</div>';
																						$string_extra+='</div>';
																					$string_extra+='</div>';
																				$string_extra+='</div>';
																				$string_extra+='<div class="col-12 mb-2 ml-auto mr-auto optgroup_text_div hide">';
																					$string_extra+='<div class="form-group">';
																						$string_extra+='<input class="form-control data-placeholder data-original-title optgroup_text select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Optgroup Text" data-original-title-fa="متن سرگروه" data-original-title="'+(language == "en" ? "Optgroup Text" : "متن سرگروه")+'" data-placement="top" data-placeholder-en="Optgroup Text" data-placeholder-fa="متن سرگروه" placeholder="'+(language=="en" ? "Optgroup Text":"متن سرگروه")+'" type="text">';
																					$string_extra+='</div>';
																				$string_extra+='</div>';
																			$string_extra+='</div>';
																			$string_extra+='<div class="col-12 row mb-2 ml-auto mr-auto">';
																				$string_extra+='<i class="fas fa-plus-octagon display-4 mt-2 '+(language=="en" ? "ml-auto mr-1":"mr-auto ml-1")+' text-success cursor-pointer data-original-title add_select_options" rel="tooltip" data-original-title-en="Add Option" data-original-title-fa="افزودن گزینه" data-original-title="'+(language == "en" ? "Add Option" : "افزودن گزینه")+'"></i>';
																				$string_extra+='<i class="fas fa-eraser display-4 mt-2 '+(language=="en" ? "ml-1 mr-auto":"mr-1 ml-auto")+' text-warning cursor-pointer data-original-title clear_select_options" rel="tooltip" data-original-title-en="Clear Option" data-original-title-fa="پاکسازی گزینه" data-original-title="'+(language == "en" ? "Clear Option" : "پاکسازی گزینه")+'"></i>';
																				$string_extra+='';
																				$string_extra+='<i class="fad fa-save display-4 mt-2 '+(language=="en" ? "ml-auto mr-1":"mr-auto ml-1")+' text-success cursor-pointer data-original-title save_select_options hide" rel="tooltip" data-original-title-en="Save Option" data-original-title-fa="ذخیره گزینه" data-original-title="'+(language == "en" ? "Save Option" : "ذخیره گزینه")+'"></i>';
																				$string_extra+='<i class="fad fa-clone display-4 mt-2 ml-1 mr-1 text-info cursor-pointer data-original-title copy_select_options hide" rel="tooltip" data-original-title-en="Copy Option" data-original-title-fa="کپی گزینه" data-original-title="'+(language == "en" ? "Copy Option" : "کپی گزینه")+'"></i>';
																				$string_extra+='<i class="fad fa-forward display-4 mt-2 ml-1 mr-1 cursor-pointer data-original-title skip_select_options hide" rel="tooltip" data-original-title-en="Skip Editing" data-original-title-fa="رد کردن ویرایش" data-original-title="'+(language == "en" ? "Skip Editing" : "رد کردن ویرایش")+'"></i>';
																				$string_extra+='<i class="fas fa-times-octagon display-4 mt-2 '+(language=="en" ? "ml-1 mr-auto":"mr-1 ml-auto")+' text-danger cursor-pointer data-original-title delete_select_options hide" rel="tooltip" data-original-title-en="Delete Option" data-original-title-fa="حذف گزینه" data-original-title="'+(language == "en" ? "Delete Option" : "حذف گزینه")+'"></i>';
																			$string_extra+='</div>';
																			$string_extra+='<!-- table of select options -->';
																			$string_extra+='<div class="col-12">';
																				$string_extra+='<table class="table table-striped w-100 selectOpt_table">';
																					$string_extra+='<thead>';
																						$string_extra+='<tr>';
																							$string_extra+='<th data-priority="4">';
																								$string_extra+='<label class="data-text" data-text-en="Optgroup" data-text-fa="سرگروه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Optgroup":"سرگروه")+'</label>';
																							$string_extra+='</th>';
																							$string_extra+='<th data-priority="1">';
																								$string_extra+='<label class="data-text" data-text-en="Name" data-text-fa="نام" style="margin-bottom: 0px !important;">'+(language=="en" ? "Name":"نام")+'</label>';
																							$string_extra+='</th>';
																							$string_extra+='<th data-priority="6">';
																								$string_extra+='<label class="data-text" data-text-en="Value" data-text-fa="مقدار" style="margin-bottom: 0px !important;">'+(language=="en" ? "Value":"مقدار")+'</label>';
																							$string_extra+='</th>';
																							$string_extra+='<th data-priority="3">';
																								$string_extra+='<label class="data-text" data-text-en="Type" data-text-fa="حالت" style="margin-bottom: 0px !important;">'+(language=="en" ? "Type":"حالت")+'</label>';
																							$string_extra+='</th>';
																							$string_extra+='<th data-priority="2">';
																								$string_extra+='<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات">'+(language=="en" ? "Operation":"عملیات")+'</label>';
																							$string_extra+='</th>';
																						$string_extra+='</tr>';
																					$string_extra+='</thead>';
																					$string_extra+='<tbody>';
																						$string_extra+='';
																					$string_extra+='</tbody>';
																					$string_extra+='<tfoot>';
																						$string_extra+='<tr>';
																							$string_extra+='<th>';
																								$string_extra+='<label class="data-text" data-text-en="Optgroup" data-text-fa="سرگروه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Optgroup":"سرگروه")+'</label>';
																							$string_extra+='</th>';
																							$string_extra+='<th>';
																								$string_extra+='<label class="data-text" data-text-en="Name" data-text-fa="نام" style="margin-bottom: 0px !important;">'+(language=="en" ? "Name":"نام")+'</label>';
																							$string_extra+='</th>';
																							$string_extra+='<th>';
																								$string_extra+='<label class="data-text" data-text-en="Value" data-text-fa="مقدار" style="margin-bottom: 0px !important;">'+(language=="en" ? "Value":"مقدار")+'</label>';
																							$string_extra+='</th>';
																							$string_extra+='<th>';
																								$string_extra+='<label class="data-text" data-text-en="Type" data-text-fa="حالت" style="margin-bottom: 0px !important;">'+(language=="en" ? "Type":"حالت")+'</label>';
																							$string_extra+='</th>';
																							$string_extra+='<th>';
																								$string_extra+='<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات">'+(language=="en" ? "Operation":"عملیات")+'</label>';
																							$string_extra+='</th>';
																						$string_extra+='</tr>';
																					$string_extra+='</tfoot>';
																				$string_extra+='</table>';
																			$string_extra+='</div>';
																		$string_extra+='</div>';
																		$.when($element.find(".extra-options").empty().append($string_extra)).done(function(){
																			$.when($element.find(".selectpicker").selectpicker({iconBase: "tim-icons",tickIcon: "icon-check-2"})).done(function(){
																				$element.find("select.optgroup_id").load("table/class/action.php?optgroups", { "column_id": $data[1] },function () {
																					$element.find("select.optgroup_id").selectpicker("val","-").selectpicker("refresh").change();
																				});
																				callDataTable_selectOpt_table($element,$data[1]);
																			});
																		});
																	});
																}else if($input_select.val()==9 || $input_select.val()=="9"){//info search case 9 for see all things about this part
																	$string_extra='<div class="col-md-12 row mr-auto ml-auto pb-3">';
																		$string_extra+='<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto">';
																			$string_extra+='<div class="form-check cursor-pointer">';
																				$string_extra+='<label class="form-check-label">';
																					$string_extra+='<input class="form-check-input input_is_multiple input_checkbox" type="checkbox" data-reset-value="">';
																					$string_extra+='<span class="form-check-sign"></span>';
																					$string_extra+='<label class="data-text" data-text-en="Multiple ?" data-text-fa="چندتایی ؟">'+(language=="en" ? "Multiple ?":"چندتایی ؟")+'</label>';
																				$string_extra+='</label>';
																			$string_extra+='</div>';
																		$string_extra+='</div>';
																		$string_extra+='<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto hide">';
																			$string_extra+='<div class="form-check cursor-pointer">';
																				$string_extra+='<label class="form-check-label">';
																					$string_extra+='<input class="form-check-input input_is_forced input_checkbox" checked type="checkbox" data-reset-value="">';
																					$string_extra+='<span class="form-check-sign"></span>';
																					$string_extra+='<label class="data-text" data-text-en="Forced ?" data-text-fa="اجباری ؟">'+(language=="en" ? "Forced ?":"اجباری ؟")+'</label>';
																				$string_extra+='</label>';
																			$string_extra+='</div>';
																		$string_extra+='</div>';
																	$string_extra+='</div>';
																	$string_extra+='<div class="col-md-12 row mr-auto ml-auto">';
																		$string_extra+='<div class="form-group col-4">';
																			$string_extra+='<input class="form-control data-placeholder data-original-title checkbox_name select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Name" data-original-title-fa="نام گزینه" data-original-title="'+(language=="en" ? "Option Name" : "نام گزینه")+'" data-placement="top" data-placeholder-en="Option Name" data-placeholder-fa="نام گزینه" placeholder="'+(language=="en" ? "Option Name":"نام گزینه")+'" type="text">';
																		$string_extra+='</div>';
																		$string_extra+='<div class="form-group col-4">';
																			$string_extra+='<input class="form-control data-placeholder data-original-title checkbox_value select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Value" data-original-title-fa="خروجی گزینه" data-original-title="'+(language=="en" ? "Option Value" : "خروجی گزینه")+'" data-placement="top" data-placeholder-en="Option Value" data-placeholder-fa="خروجی گزینه" placeholder="'+(language=="en" ? "Option Value":"خروجی گزینه")+'" type="text">';
																		$string_extra+='</div>';
																		$string_extra+='<div class="form-group col-4">';
																			$string_extra+='<input class="form-control data-placeholder data-original-title checkbox_false select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option False" data-original-title-fa="خروجی منفی گزینه" data-original-title="'+(language=="en" ? "Option False" : "خروجی منفی گزینه")+'" data-placement="top" data-placeholder-en="Option False" data-placeholder-fa="خروجی منفی گزینه" placeholder="'+(language=="en" ? "Option False":"خروجی منفی گزینه")+'" type="text">';
																		$string_extra+='</div>';
																		$string_extra+='<div class="col-12 row mb-2 ml-auto mr-auto">';
																			$string_extra+='<i class="fas fa-plus-octagon display-4 mt-2 '+(language=="en" ? "ml-auto mr-1":"mr-auto ml-1")+' text-success cursor-pointer data-original-title add_checkbox_options" rel="tooltip" data-original-title-en="Add Option" data-original-title-fa="افزودن گزینه" data-original-title="'+(language=="en" ? "Add Option" : "افزودن گزینه")+'"></i>';
																			$string_extra+='<i class="fas fa-eraser display-4 mt-2 '+(language=="en" ? "ml-1 mr-auto":"mr-1 ml-auto")+' text-warning cursor-pointer data-original-title clear_checkbox_options" rel="tooltip" data-original-title-en="Clear Option" data-original-title-fa="پاکسازی گزینه" data-original-title="'+(language=="en" ? "Clear Option" : "پاکسازی گزینه")+'"></i>';
																			$string_extra+='<i class="fad fa-save display-4 mt-2 '+(language=="en" ? "ml-auto mr-1":"mr-auto ml-1")+' text-success cursor-pointer data-original-title save_checkbox_options hide" rel="tooltip" data-original-title-en="Save Option" data-original-title-fa="ذخیره گزینه" data-original-title="'+(language=="en" ? "Save Option" : "ذخیره گزینه")+'"></i>';
																			$string_extra+='<i class="fad fa-clone display-4 mt-2 ml-1 mr-1 text-info cursor-pointer data-original-title copy_checkbox_options hide" rel="tooltip" data-original-title-en="Copy Option" data-original-title-fa="کپی گزینه" data-original-title="'+(language=="en" ? "Copy Option" : "کپی گزینه")+'"></i>';
																			$string_extra+='<i class="fad fa-forward display-4 mt-2 ml-1 mr-1 cursor-pointer data-original-title skip_checkbox_options hide" rel="tooltip" data-original-title-en="Skip Editing" data-original-title-fa="رد کردن ویرایش" data-original-title="'+(language=="en" ? "Skip Editing" : "رد کردن ویرایش")+'"></i>';
																			$string_extra+='<i class="fas fa-times-octagon display-4 mt-2 '+(language=="en" ? "ml-1 mr-auto":"mr-1 ml-auto")+' text-danger cursor-pointer data-original-title delete_checkbox_options hide" rel="tooltip" data-original-title-en="Delete Option" data-original-title-fa="حذف گزینه" data-original-title="'+(language=="en" ? "Delete Option" : "حذف گزینه")+'"></i>';
																		$string_extra+='</div>';
																		$string_extra+='<!-- table of select options -->';
																		$string_extra+='<div class="col-12">';
																			$string_extra+='<table class="table table-striped w-100 checkboxOpt_table">';
																				$string_extra+='<thead>';
																					$string_extra+='<tr>';
																						$string_extra+='<th data-priority="4">';
																							$string_extra+='<label class="data-text" data-text-en="Option Name" data-text-fa="نام گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option Name":"نام گزینه")+'</label>';
																						$string_extra+='</th>';
																						$string_extra+='<th data-priority="1">';
																							$string_extra+='<label class="data-text" data-text-en="Option Value" data-text-fa="خروجی گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option Value":"خروجی گزینه")+'</label>';
																						$string_extra+='</th>';
																						$string_extra+='<th data-priority="6">';
																							$string_extra+='<label class="data-text" data-text-en="Option false value" data-text-fa="خروجی منفی گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option false value":"خروجی منفی گزینه")+'</label>';
																						$string_extra+='</th>';
																						$string_extra+='<th data-priority="2">';
																							$string_extra+='<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات">'+(language=="en" ? "Operation":"عملیات")+'</label>';
																						$string_extra+='</th>';
																					$string_extra+='</tr>';
																				$string_extra+='</thead>';
																				$string_extra+='<tbody>';
																					$string_extra+='<!---->';
																				$string_extra+='</tbody>';
																				$string_extra+='<tfoot>';
																					$string_extra+='<tr>';
																						$string_extra+='<th>';
																							$string_extra+='<label class="data-text" data-text-en="Option Name" data-text-fa="نام گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option Name":"نام گزینه")+'</label>';
																						$string_extra+='</th>';
																						$string_extra+='<th>';
																							$string_extra+='<label class="data-text" data-text-en="Option Value" data-text-fa="خروجی گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option Value":"خروجی گزینه")+'</label>';
																						$string_extra+='</th>';
																						$string_extra+='<th>';
																							$string_extra+='<label class="data-text" data-text-en="Option false value" data-text-fa="خروجی منفی گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option false value":"خروجی منفی گزینه")+'</label>';
																						$string_extra+='</th>';
																						$string_extra+='<th>';
																							$string_extra+='<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات">'+(language=="en" ? "Operation":"عملیات")+'</label>';
																						$string_extra+='</th>';
																					$string_extra+='</tr>';
																				$string_extra+='</tfoot>';
																			$string_extra+='</table>';
																		$string_extra+='</div>';
																	$string_extra+='</div>';
																	$.when($element.find(".extra-options").empty().append($string_extra)).done(function(){
																		callDataTable_checkboxOpt_table($element,$data[1]);
																	});
																}
															});
														}else if($data[0]=="error"){
															$save_column_error=1;
															$element.addClass("new-column");
															switch ($data[1]) {
																case "empty_name":
																	$current_name.parent(".form-group").children("label.error").remove();
																	$current_name.parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	$description_name_fa.parent(".form-group").children("label.error").remove();
																	$description_name_fa.parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	$description_name_en.parent(".form-group").children("label.error").remove();
																	$description_name_en.parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	changeColumnStatus($element,$button,"danger");
																	if($isScroll){scrollToElement($element);}
																break;
																case "bad_name":
																	$error_fields=["Just Egnlish letter and number and underline allowed !","فقط حرف انگلیسی و اعداد و آندرلاین مجاز است !"];
																	if($data[2]=="current_name"){
																		$current_name.parent(".form-group").children("label.error").remove();
																		$current_name.parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	}else if($data[2]=="description_name_fa"){
																		$description_name_fa.parent(".form-group").children("label.error").remove();
																		$description_name_fa.parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	}else if($data[2]=="description_name_en"){
																		$description_name_en.parent(".form-group").children("label.error").remove();
																		$description_name_en.parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	}
																	changeColumnStatus($element,$button,"danger");
																	if($isScroll){scrollToElement($element);}
																break;
																case "name_taken":
																	$error_fields=["This name has already been taken !","این نام قبلاً گرفته شده است !"];
																	$current_name.parent(".form-group").children("label.error").remove();
																	$current_name.focus().parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	changeColumnStatus($element,$button,"danger");
																	if($isScroll){scrollToElement($element);}
																break;
																case "goToStep1":
																	if($wizzard_nav==""){
																		$wizzard_nav=$(".wizard-navigation ul.custom_transition.nav.nav-pills");
																	}
																	$create_level=0;
																	$.when(changeColumnStatus($element,$button,"none")).done(function(){
																		$.when($($wizzard_nav.find('li a')[0]).click()).done(function(){
																			setTimeout(() => {
																				$.when(getElement("#current_name").val("").focus().parent(".input-group").attr("class", "input-group input-group-focus")).done(function(){
																					if($isScroll){
																						if($isScroll){scrollToElement("#current_name");}
																					}
																				});
																			}, 150);
																		});
																	});
																break;
																case "primary":
																	$error_fields=["The primary column is already present ! (Click to uncheck)","ستون اصلی در حال حاضر موجود است ! (کلیک کنید تا لغو شود)"];
																	$input_primary.parent().parent().parent().addClass("has-danger shaker");
																	$input_primary.parent().parent(".form-check.cursor-pointer").children("label.error").remove();
																	$input_primary.parent().parent(".form-check.cursor-pointer").append("<label class='error data-text' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"' onclick=''>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	changeColumnStatus($element,$button,"danger");
																	if($isScroll){scrollToElement($element);}
																	setTimeout(() => {
																		$input_primary.parent().parent().parent().removeClass("shaker");
																	}, 1000);
																break;
															}
														}else{
															$save_column_error=1;
															$element.addClass("new-column");
															feedbackOperations(data);
															changeColumnStatus($element,$button,"danger");
															if($isScroll){scrollToElement($element);}
														}
													}).always(function() {
														if($save_column_error==0){
															updateTable();
															if($operation=="save_close"){
																function saveAndCloseF() {
																	if($element.find(".skip-button-operation").length){
																		$disable_alert=1;
																		$.when($element.find(".skip-button-operation").click()).done(function(){
																			if(typeof $current_interval !== "undefined"){
																				clearInterval($current_interval);
																				delete $current_interval;
																			}
																			var $current_interval;
																			$current_interval = setInterval(() => {
																				if($element.length==0){
																					clearInterval($current_interval);
																					delete $current_interval;
																					$disable_alert=0;
																				}
																			}, 500);
																		});
																	}else{
																		if(typeof $current_interval !== "undefined"){
																			clearInterval($current_interval);
																			delete $current_interval;
																		}
																		var $current_interval;
																		$current_interval = setInterval(() => {
																			if($element.length==0){
																				clearInterval($current_interval);
																				delete $current_interval;
																				$disable_alert=0;
																			}
																		}, 500);
																	}
																}
																try {
																	if(alertShowerSetting['saveAndCloseCreateTable_showAlert']=="true" || $disable_alert==1){
																		saveAndCloseF();
																	}else{
																		if(language=="fa"){
																			Swal.fire({
																				title: 'آیا مطمئن  هستید؟',
																				text: "آیا میخواهید این ستون را ببندید ؟",
																				icon: 'warning',
																				showCancelButton: true,
																				customClass: {
																					confirmButton: 'btn btn-success',
																					cancelButton: 'btn btn-danger'
																				},
																				buttonsStyling: false,
																				confirmButtonText: 'بله',
																				cancelButtonText: 'لغو',
																				footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'saveAndCloseCreateTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
																			}).then((result) => {
																				if(result.value) {
																					saveAndCloseF();
																				}else{
																					changeColumnStatus($element,$button,"success");
																				}
																			});
																		}else{
																			Swal.fire({
																				title: 'Are you sure?',
																				text: "Are you really want to close this column ?",
																				icon: 'warning',
																				showCancelButton: true,
																				customClass: {
																					confirmButton: 'btn btn-success',
																					cancelButton: 'btn btn-danger'
																				},
																				buttonsStyling: false,
																				confirmButtonText: 'Yes',
																				footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'saveAndCloseCreateTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
																			}).then((result) => {
																				if(result.value) {
																					saveAndCloseF();
																				}else{
																					changeColumnStatus($element,$button,"success");
																				}
																			});
																		}
																	}
																} catch(err) {
																	alertShowerSetting['saveAndCloseCreateTable_showAlert']="false";
																	changeColumnStatus($element,$button,"danger");
																}
															}else{
																changeColumnStatus($element,$button,"success");
															}
														}else{
															if(typeof $saveInterval !== "undefined"){
																clearInterval($saveInterval);
																$($(".wizard-navigation ul").find('li a i')[2]).click();
															}
														}
													});
												}else if($isSaved && $savedID){
													var $save_column_error=0;
													$.post("table/class/action.php?updateColumn", {
														column_id: $savedID,
														current_name: $current_name.val(),
														description_name_fa: $description_name_fa.val(),
														description_info_fa: $description_info_fa.val(),
														description_name_en: $description_name_en.val(),
														description_info_en: $description_info_en.val(),
														input_select: $input_select.val(),
														input_creatable: ($input_creatable.is(":checked") ? 1:0),
														input_visible: ($input_visible.is(":checked") ? 1:0),
														input_editable: ($input_editable.is(":checked") ? 1:0),
														input_removable: ($input_removable.is(":checked") ? 1:0),
														input_visible_table: ($input_visible_table.is(":checked") ? 1:0),
														input_primary: ($input_primary.is(":checked") ? 1:0),
														input_important: ($input_important.is(":checked") ? 1:0),
														input_extra: (JSON.stringify($input_extra))
													}, function(data, status) {
														$data=data.split("_._");
														if($data[0]=="success" && status=="success"){

														}else if($data[0]=="error"){
															$save_column_error=1;
															switch ($data[1]) {
																case "empty_name":
																	$current_name.parent(".form-group").children("label.error").remove();
																	$current_name.parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	$description_name_fa.parent(".form-group").children("label.error").remove();
																	$description_name_fa.parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	$description_name_en.parent(".form-group").children("label.error").remove();
																	$description_name_en.parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	changeColumnStatus($element,$button,"danger");
																	if($isScroll){scrollToElement($element);}
																break;
																case "bad_name":
																	$error_fields=["Just Egnlish letter and number and underline allowed !","فقط حرف انگلیسی و اعداد و آندرلاین مجاز است !"];
																	if($data[2]=="current_name"){
																		$current_name.parent(".form-group").children("label.error").remove();
																		$current_name.parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	}else if($data[2]=="description_name_fa"){
																		$description_name_fa.parent(".form-group").children("label.error").remove();
																		$description_name_fa.parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	}else if($data[2]=="description_name_en"){
																		$description_name_en.parent(".form-group").children("label.error").remove();
																		$description_name_en.parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	}
																	changeColumnStatus($element,$button,"danger");
																	if($isScroll){scrollToElement($element);}
																break;
																case "name_taken":
																	$error_fields=["This name has already been taken !","این نام قبلاً گرفته شده است !"];
																	$current_name.parent(".form-group").children("label.error").remove();
																	$current_name.focus().parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	changeColumnStatus($element,$button,"danger");
																	if($isScroll){scrollToElement($element);}
																break;
																case "goToStep1":
																	if($wizzard_nav==""){
																		$wizzard_nav=$(".wizard-navigation ul.custom_transition.nav.nav-pills");
																	}
																	$create_level=0;
																	$.when(changeColumnStatus($element,$button,"none")).done(function(){
																		$.when($($wizzard_nav.find('li a')[0]).click()).done(function(){
																			setTimeout(() => {
																				$.when(getElement("#current_name").val("").focus().parent(".input-group").attr("class", "input-group input-group-focus")).done(function(){
																					if($isScroll){
																						if($isScroll){scrollToElement("#current_name");}
																					}
																				});
																			}, 150);
																		});
																	});
																break;
																case "primary":
																	$error_fields=["The primary column is already present ! (Click to uncheck)","ستون اصلی در حال حاضر موجود است ! (کلیک کنید تا لغو شود)"];
																	$input_primary.parent().parent().parent().addClass("has-danger shaker");
																	$input_primary.parent().parent(".form-check.cursor-pointer").children("label.error").remove();
																	$input_primary.parent().parent(".form-check.cursor-pointer").append("<label class='error data-text' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"' onclick=''>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
																	changeColumnStatus($element,$button,"danger");
																	if($isScroll){scrollToElement($element);}
																	setTimeout(() => {
																		$input_primary.parent().parent().parent().removeClass("shaker");
																	}, 1000);
																break;
															}
														}else{
															$save_column_error=1;
															feedbackOperations(data);
															changeColumnStatus($element,$button,"danger");
															if($isScroll){scrollToElement($element);}
														}
													}).always(function() {
														updateTable();
														if($save_column_error==0){
															if($operation=="save_close"){
																try {
																	function closeColumn() {
																		$.post("table/class/action.php?closeEditing", {
																			"id" : $savedID
																		}, function(data, status) {
																			if(data=="success" && status=="success"){
																				$element.remove();
																			}else if (status != "success" || data != "success") {
																				feedbackOperations(data);
																				changeColumnStatus($element,$button,"info");
																			}else{
																				changeColumnStatus($element,$button,"info");
																			}
																		});
																	}
																	if(alertShowerSetting['saveAndCloseCreateTable_showAlert']=="true" || $disable_alert==1){
																		closeColumn();
																	}else{
																		if(language=="fa"){
																			Swal.fire({
																				title: 'آیا مطمئن  هستید؟',
																				text: "آیا میخواهید این ستون را ببندید ؟",
																				icon: 'warning',
																				showCancelButton: true,
																				customClass: {
																					confirmButton: 'btn btn-success',
																					cancelButton: 'btn btn-danger'
																				},
																				buttonsStyling: false,
																				confirmButtonText: 'بله',
																				cancelButtonText: 'لغو',
																				footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'saveAndCloseCreateTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
																			}).then((result) => {
																				if(result.value) {
																					closeColumn();
																				}else{
																					changeColumnStatus($element,$button,"success");
																				}
																			});
																		}else{
																			Swal.fire({
																				title: 'Are you sure?',
																				text: "Are you really want to close this column ?",
																				icon: 'warning',
																				showCancelButton: true,
																				customClass: {
																					confirmButton: 'btn btn-success',
																					cancelButton: 'btn btn-danger'
																				},
																				buttonsStyling: false,
																				confirmButtonText: 'Yes',
																				footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'saveAndCloseCreateTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
																			}).then((result) => {
																				if(result.value) {
																					closeColumn();
																				}else{
																					changeColumnStatus($element,$button,"success");
																				}
																			});
																		}
																	}
																} catch(err) {
																	alertShowerSetting['saveAndCloseCreateTable_showAlert']="false";
																	changeColumnStatus($element,$button,"danger");
																}
															}else{
																changeColumnStatus($element,$button,"success");
															}
														}else{
															if(typeof $saveInterval !== "undefined"){
																clearInterval($saveInterval);
																$($(".wizard-navigation ul").find('li a i')[2]).click();
															}
														}
													});
												}
											}

										});
									}

								});
							}

						});
					}
				}else{
					var $error_fields=["This name is not allowed !","این نام مجاز نیست !"];
					$current_name.parent(".form-group").children("label.error").remove();
					$current_name.focus().parent(".form-group").attr("class", "form-group has-danger").append("<label class='error data-text error-empty' data-text-en='"+$error_fields[0]+"' data-text-fa='"+$error_fields[1]+"'>"+$error_fields[(language=="en" ? 0:1)]+"</label>");
					changeColumnStatus($element,$button,"danger");
				}
			break;
			case "clear":
				function clearColumn() {
					$element.find(".input_text").val("");
					$element.find(".input_select:not(.input_mode)").selectpicker("val",'').selectpicker("refresh");
					<?php
						$last_mode=$connection->query("SELECT * FROM ".$sub_name."table_column_mode WHERE act=1 ORDER BY ordering ASC")->fetch()['id'];
					?>
					$element.find(".input_select.input_mode").selectpicker("val",'<?php print_r($last_mode); ?>').selectpicker("refresh");
					$element.find(".input_checkbox").prop("checked",false);
					changeColumnStatus($element,$button,"info");
				}
				try {
					if(alertShowerSetting['clearCreateTable_showAlert']=="true" || $disable_alert==1){
						clearColumn();
					}else{
						if(language=="fa"){
							Swal.fire({
								title: 'آیا مطمئن  هستید؟',
								text: "آیا میخواهید تمام فیلد های این ستون را پاکسازی نمایید ؟",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'بله',
								cancelButtonText: 'لغو',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'clearCreateTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
							}).then((result) => {
								if(result.value) {
									clearColumn();
								}else{
									changeColumnStatus($element,$button,"none");
								}
							});
						}else{
							Swal.fire({
								title: 'Are you sure?',
								text: "Are you really want to clear all fields of this column ?",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'Yes',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'clearCreateTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
							}).then((result) => {
								if(result.value) {
									clearColumn();
								}else{
									changeColumnStatus($element,$button,"none");
								}
							});
						}
					}
				} catch(err) {
					alertShowerSetting['clearCreateTable_showAlert']="false";
					changeColumnStatus($element,$button,"danger");
				}
			break;
			case "reset":
				function resetFunction($element) {
					$element.find('[data-reset-value]').each(function () {
						var $this=$(this),
							$reset=$this.attr("data-reset-value");
						switch ($this.prop("tagName")) {
							case "INPUT":
								switch ($this.attr("type").toString().toLowerCase()) {
									case "text":case "number":
										$this.val($reset);
									break;
									case "select":
										$this.selectpicker('val', $reset).selectpicker("refresh");
									break;
									case "checkbox":
										$this.prop("checked",($reset=="true" || $reset==true ? true:false));
									break;
									case "radio":
										$this.prop("checked",($reset=="true" || $reset==true ? true:false));
									break;
								}
							break;
							case "SELECT":
								$this.selectpicker("val",$reset).selectpicker("refresh");
							break;
						}
					});
					changeColumnStatus($element,$button,"info");
				}
				try {
					if(alertShowerSetting['resetCreateTable_showAlert']=="true" || $disable_alert==1){
						resetFunction($element);
					}else{
						if(language=="fa"){
							Swal.fire({
								title: 'آیا مطمئن  هستید؟',
								text: "از آنجا که این ستون جدید است ، با تنظیم مجدد این ستون تمام اطلاعات پاک می شود !",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'بله',
								cancelButtonText: 'لغو',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'resetCreateTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
							}).then((result) => {
								if(result.value) {
									resetFunction($element);
								}else{
									changeColumnStatus($element,$button,"none");
								}
							});
						}else{
							Swal.fire({
								title: 'Are you sure?',
								text: "Since this column is new, resetting this column will erase all of information !",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'Yes',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'resetCreateTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
							}).then((result) => {
								if(result.value) {
									resetFunction($element);
								}else{
									changeColumnStatus($element,$button,"none");
								}
							});
						}
					}
				} catch(err) {
					alertShowerSetting['resetCreateTable_showAlert']="false";
				}
			break;
			case "skip":
				function skipColumn() {
					if($isNew){
						$element.remove();
					}else if($isSaved){
						$.post("table/class/action.php?closeEditing", {
							"id" : $savedID
						}, function(data, status) {
							if(data=="success" && status=="success"){
								$element.remove();
							}else if (status != "success" || data != "success") {
								feedbackOperations(data);
								changeColumnStatus($element,$button,"danger");
							}else{
								changeColumnStatus($element,$button,"danger");
							}
						});
					}
				}
				try {
					if(alertShowerSetting['skipNewCreateTable_showAlert']=="true" || $disable_alert==1){
						skipColumn();
					}else{
						if(language=="fa"){
							Swal.fire({
								title: 'آیا مطمئن  هستید؟',
								text: "اگر تغیری در این ستون ایجاد کرده اید و هنوز آن را ذخیره نکرده اید با رد کردن این ستون تغیرات فعلی ذخیره نمی شود !",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'بله',
								cancelButtonText: 'لغو',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'skipNewCreateTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
							}).then((result) => {
								if(result.value) {
									skipColumn();
								}else{
									changeColumnStatus($element,$button,"none");
								}
							});
						}else{
							Swal.fire({
								title: 'Are you sure?',
								text: "If you have made a change in this column and you have not saved it yet, skipping this column will not save the current changes !",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'Yes',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'skipNewCreateTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
							}).then((result) => {
								if(result.value) {
									skipColumn();
								}else{
									changeColumnStatus($element,$button,"none");
								}
							});
						}
					}
				} catch(err) {
					alertShowerSetting['skipNewCreateTable_showAlert']="false";
					changeColumnStatus($element,$button,"danger");
				}
			break;
			case "delete":
				function removeColumn() {
					if($isNew){
						$element.remove();
					}else if($isSaved){
						$.post("table/class/action.php?delete", {
							"id" : $savedID
						}, function(data, status) {
							if(data=="success" && status=="success"){
								$.when($element.remove()).done(function(){
									updateTable();
								});
							}else if (status != "success" || data != "success") {
								feedbackOperations(data);
								changeColumnStatus($element,$button,"danger");
							}else{
								changeColumnStatus($element,$button,"danger");
							}
						});
					}else{
						changeColumnStatus($element,$button,"danger");
					}
				}
				try {
					if(alertShowerSetting['deleteCreateTable_showAlert']=="true" || $disable_alert==1){
						removeColumn();
					}else{
						if(language=="fa"){
							Swal.fire({
								title: 'آیا مطمئن  هستید؟',
								text: "آیا میخواهید این ستون را برای همیشه حذف نمایید ؟ (پس از حذف آن دیگر امکان بازگشت آن وجود ندارد)، اگر میخواهید از حالت ویرایش خارج شود میتوانید با دکمه رد کردن آن را از حالت ویرایش خارج نمایید !",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'بله',
								cancelButtonText: 'لغو',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteCreateTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
							}).then((result) => {
								if(result.value) {
									removeColumn();
								}else{
									changeColumnStatus($element,$button,"none");
								}
							});
						}else{
							Swal.fire({
								title: 'Are you sure?',
								text: "Do you want to delete this column forever? (After deleting it, it's no longer possible to return it), If you want to exit editing mode, you can exit editing mode by clicking the skip button !",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'Yes',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteCreateTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
							}).then((result) => {
								if(result.value) {
									removeColumn();
								}else{
									changeColumnStatus($element,$button,"none");
								}
							});
						}
					}
				} catch(err) {
					alertShowerSetting['deleteCreateTable_showAlert']="false";
					changeColumnStatus($element,$button,"danger");
				}
			break;
		}
	}

	function doWithEach($button){
		var $numberOfEach=0,$numberOfElement=$($button).length,$intervalControl;
		$disable_alert=1;
		$($button).each(function(){
			var $operationss=$(this).attr('onclick').split("'")[1].toString();
			$numberOfEach++;
			createButtonOperations($operationss,$(this).parent().parent(),$(this),0);
			if($numberOfElement==$numberOfEach){
				if($button==",save_close-button-operation"){
					$intervalControl=setInterval(() => {
						if($("#stack-of-columns").children().length==0){
							clearInterval($intervalControl);
							$disable_alert=0;
						}
					}, 500);
				}else{
					setTimeout(function(){$disable_alert=0;},1000);
				}
				return false;
			}
		});
	}

	function saveAll($is) {
		if($is){
			doWithEach('.save-button-operation');
		}else{
			questionSWAL('saveAllCreateTable_showAlert',saveAll,'fa_title','آیا میخواهید تمام ستون ها ذخیره شود ؟','en_title','Are you really want to save all of columns ?','warning');
		}
	}

	function saveAndCloseAll($is) {
		if($is){
			doWithEach('.save_close-button-operation');
		}else{
			questionSWAL('saveAndCloseAllCreateTable_showAlert',saveAndCloseAll,'fa_title','آیا میخواهید تمام ستون ها ذخیره و بسته شود ؟','en_title','Are you really want to save and close all of columns ?','warning');
		}
	}

	function clearAll($is) {
		if($is){
			doWithEach('.clear-button-operation');
		}else{
			questionSWAL('clearAllCreateTable_showAlert',clearAll,'fa_title','آیا میخواهید تمام ستون ها پاکسازی شود ؟','en_title','Are you really want to clear all of columns ?','warning');
		}
	}

	function resetAll($is) {
		if($is){
			doWithEach('.reset-button-operation');
		}else{
			questionSWAL('resetAllCreateTable_showAlert',resetAll,'fa_title','آیا میخواهید تمام ستون ها بازیابی شود ؟','en_title','Are you really want to reset all of columns ?','warning');
		}
	}

	function skipAll($is) {
		if($is){
			doWithEach('.skip-button-operation');
		}else{
			questionSWAL('skipNewAllCreateTable_showAlert',skipAll,'fa_title','آیا میخواهید تمام ستون ها رد شود ؟','en_title','Are you really want to skip all of columns ?','warning');
		}
	}

	function deleteAll($is) {
		if($is){
			doWithEach('.delete-button-operation');
		}else{
			questionSWAL('skipNewAllCreateTable_showAlert',deleteAll,'fa_title','آیا میخواهید تمام ستون ها حذف شود ؟','en_title','Are you really want to delete all of columns ?','warning');
		}

	}

	function databaseOperations($operation,$id,$noScroll) {
		$element_checker=$(".column_saved_id-"+$id);
		switch ($operation) {
			case "edit":
			case "editOthers":
			case "editOthers_save":
				if($operation=="edit"){
					var $got_id='column_saved_id-'+$id;
					var $isSaveds="saved_column";
				}else if($operation=="editOthers" || $operation=="editOthers_save"){
					var $got_id='new-column';
					var $isSaveds="";
				}
				if($element_checker.length==0){
					$.post("table/class/action.php?edit", {
						"id" : $id
					}, function(data, status) {
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							$data=data.split("_._");
							var $current_name=$data[0],
								$description_name_fa=$data[1],
								$description_info_fa=$data[2],
								$description_name_en=$data[3],
								$description_info_en=$data[4],
								$input_select=parseInt($data[5]),
								$input_creatable=parseInt($data[6]),
								$input_visible=parseInt($data[7]),
								$input_editable=parseInt($data[8]),
								$input_removable=parseInt($data[9]),
								$input_visible_table=parseInt($data[10]),
								$input_primary=parseInt($data[11]),
								$input_important=parseInt($data[12]);
								$input_extra=$data[13];
								<?php //info search this_is_modes_for_data_tables for see all things about this part ?>
								switch ($input_select) {//tables_mode_code
									case "3":case 3:case "4":case 4:case "7":case 7:case "9":case 9://info search case 9 for see all things about this part//info search case 7 for see all things about this part//info search case 4 for see all things about this part//info search case 3 for see all things about this part
										$input_extra=JSON.parse($input_extra);
									break;
								}
								newEditor($got_id,$isSaveds,$current_name,$description_name_fa,$description_name_en,$description_info_fa,$description_info_en,$input_creatable,$input_visible,$input_editable,$input_removable,$input_visible_table,$input_select,$input_important,$input_primary,$input_extra,$noScroll,$operation);
						}else{
							feedbackOperations(data);
						}
					});
				}else{
					if($noScroll!=1){
						scrollToElement("."+$got_id+":last");
						$("."+$got_id+":last").addClass("has-info");
						setTimeout(() => {
							$("."+$got_id+":last").removeClass("has-info");
						}, 1000);
					}
				}
			break;
			case "delete":
				var $got_id='column_saved_id-'+$id;
				$.post("table/class/action.php?delete", {
					"id" : $id
				}, function(data, status) {
					if(data=="success" && status=="success"){
						updateTable();
						$("."+$got_id).remove();
						$("#column_id-"+$id).remove();
					}else if (status != "success" || data != "success") {
						feedbackOperations(data);
					}
				}).always(function (){
					updateTable();
				});
			break;
			case "openAll":
				var $button=$(".open-all"),$button_text=($button.length ? $button.text():"");
				$button.html((language=="en" ? "Done":"انجام شد")+" <label class='fas fa-check'></label>").attr("disabled",true);
				$.post("table/class/action.php?openAll", {}, function(data, status) {
					if (status == "success" && data.toString().indexOf("success")!=-1) {
						var $data=data.split("_._");
						$data.forEach(function(val,key){
							$.when(databaseOperations('edit',val,1)).done(function(){
								if(key==$data.length-1){
									setTimeout(() => {
										$button.html($button_text).attr("disabled",false);
									}, 700);
								}
							});
						});
					}else{
						feedbackOperations(data);
					}
				});
			break;
			case "deleteAll":
				$.post("table/class/action.php?deleteAll", {}, function(data, status) {
					if (status == "success" && data=="success") {
						updateTable();
						$(".saved_column, .new-column").remove();
					}else{
						feedbackOperations(data);
					}
				});
			break;
			case "copyDatabaseColumns":
			case "copyDatabaseColumns_save":
				if($("#do-select-column").val().toString().indexOf("-1")!=-1){
					$("#do-select-column").children().each(function(){
						if($(this).prop("tagName")=="OPTION"){
							if($(this).attr("value").indexOf("-1")==-1 && $(this).attr("value").indexOf("selectall")==-1){
								if($operation=="copyDatabaseColumns"){
									databaseOperations("editOthers",parseInt($(this).attr("value")),1);
								}else if($operation=="copyDatabaseColumns_save"){
									databaseOperations("editOthers_save",parseInt($(this).attr("value")),1);
								}
							}
						}else if($(this).prop("tagName")=="OPTGROUP"){
							$(this).children().each(function(){
								if($(this).attr("value").indexOf("-1")==-1 && $(this).attr("value").indexOf("selectall")==-1){
									if($operation=="copyDatabaseColumns"){
										databaseOperations("editOthers",parseInt($(this).attr("value")),1);
									}else if($operation=="copyDatabaseColumns_save"){
										databaseOperations("editOthers_save",parseInt($(this).attr("value")),1);
									}
								}
							});
						}
					});
				}else{
					$("#do-select-column").val().forEach(function(val,key){
						if($operation=="copyDatabaseColumns"){
							databaseOperations("editOthers",parseInt(val),1);
						}else if($operation=="copyDatabaseColumns_save"){
							databaseOperations("editOthers_save",parseInt(val),1);
						}
					});
				}
			break;
			case "goUp":
			case "goDown":
				$.post("table/class/action.php?"+$operation, {
					"id" : $id
				}, function(data, status) {
					if(data=="success" && status=="success"){
						updateTable();
					}else if (status != "success" || data != "success") {
						feedbackOperations(data);
					}
				});
			break;
		}
	}

	$(document).on('change', '#wizard-picture', function() {
		readURL_createTable_wizzard(this);
	});

	$(document).on('change', '#do-select-table', function() {
		var selectTable=[],$table_id=$(this).val();
		if($table_id!=[] && $table_id!=""){
			if($table_id.toString().indexOf("-1")!=-1){
				$(this).selectpicker('val', [-1]);
			}
			selectTable["fa"]="در حال بارگذاری ...";
			selectTable["en"]="Loading ...";
			$("#do-select-column").addClass("loading-cursor").empty().append('<option selected class="data-text" data-text-en="'+selectTable['en']+'" data-text-fa="'+selectTable['fa']+'" value="-">'+selectTable[language]+'</option>').selectpicker('refresh').attr("disabled",true).load( "table/class/action.php?getColumns", { "table_id": $table_id }, function( data, status, xhr ) {
				if(data.indexOf("_._")==-1){
					if (status!="success") {
						selectTable["fa"]="مشکلی پیش آمده !";
						selectTable["en"]="Something went wrong !";
						$("#do-select-column").addClass("loading-cursor").empty().append('<option selected class="data-text" data-text-en="'+selectTable['en']+'" data-text-fa="'+selectTable['fa']+'" value="-">'+selectTable[language]+'</option>').attr("disabled",true).selectpicker('refresh');
					}else{
						$("#do-select-column").attr("disabled",false).selectpicker('val', $do_select_column).selectpicker('refresh');
						$("#do-select-column").removeClass("loading-cursor").parent().removeClass("loading-cursor");
					}
				}else{
					feedbackOperations(data);
				}
			});
		}else{
			selectTable["fa"]="لطفا یک جدول انتخاب کنید !";
			selectTable["en"]="Please select a table !";
			$("#do-select-column").empty().append('<option selected class="data-text" data-text-en="'+selectTable['en']+'" data-text-fa="'+selectTable['fa']+'" value="-">'+selectTable[language]+'</option>').attr("disabled",true).selectpicker('refresh');
			$("#do-select-column").removeClass("loading-cursor").parent().removeClass("loading-cursor");
		}
	});

	$(document).on('change', '#do-select-column', function() {
		var $column_id=$(this).val();
		$do_select_column=$column_id;
		if($column_id!=[] && $column_id!=""){
			if($column_id.toString().indexOf("-1")!=-1){
				$(this).selectpicker('val', [-1]);
			}
		}
		var $this=$(this),$val=$this.val(),$split_val="";
		$val.forEach(function(item, index){
			if(item.indexOf("deselectall_")!=-1){
				$(".column-table_id-"+$val[$val.indexOf(item)].toString().split("selectall_")[1]).each(function() {
					delete $val[$val.indexOf($(this).attr("value"))];
				});
				delete $val[$val.indexOf(item)];
			}else if(item.indexOf("selectall_")!=-1){
				$(".column-table_id-"+$val[$val.indexOf(item)].toString().split("selectall_")[1]).each(function() {
					if($val.indexOf($(this).attr("value"))==-1){
						$val.push($(this).attr("value"));
					}
				});
				delete $val[$val.indexOf(item)];
			}
		});
		$this.selectpicker('val', $val).selectpicker('refresh');
	});

	function readURL_createTable_wizzard(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function(e) {
				$('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
			}
			reader.readAsDataURL(input.files[0]);
		}
	}

	$(document).on('click', '[data-toggle="wizard-radio"]', function() {
		wizard = $(this).closest('.card-wizard');
		wizard.find('[data-toggle="wizard-radio"]').removeClass('active');
		$(this).addClass('active');
		$(wizard).find('[type="radio"]').removeAttr('checked');
		$(this).find('[type="radio"]').attr('checked', 'true');
	});

	$(document).on('click', '.cooldown-click', function() {
		var $button=$(this),$button_text=($button.length ? $button.text():"");
		$button.html((language=="en" ? "Done":"انجام شد")+" <label class='fas fa-check'></label>").attr("disabled",true);
		setTimeout(() => {
			$button.html($button_text).attr("disabled",false);
		}, 1000);
	});

	$(document).on('click', '[data-toggle="wizard-checkbox"]', function() {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).find('[type="checkbox"]').removeAttr('checked');
		} else {
			$(this).addClass('active');
			$(this).find('[type="checkbox"]').attr('checked', 'true');
		}
	});

	$(document).on('keydown', '.on-press-enter', function() {
		if($loading_create_table==0){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){
				$(".btn-wd.btn-next").click();
				return false;
			}
		}
	});

	$(document).on('keydown', '.on-press-enter-column', function() {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			return false;
		}
	});

	$(document).on('keydown', '.select-option-on-press-enter', function() {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			var $element=$(this).parent().parent().parent().parent().parent().parent();
			// $element.find(".add_select_options").click();
			return false;
		}
	});

	$(document).on('focus', '.input_text', function() {
		var $value=$(this).val();
		$(this).val("").val($value);
	});

	$(document).on("change",".start-filtering-tables_columns",function(){
		$("#tables_columns").DataTable().ajax.reload( null, false );
	});

	$(document).on('click', '.checkbox_changer', function(e) {
		$this=$(e.target);
		if($this.prop("tagName")=="LABEL" && $this.hasClass("data-text")){
			$(this).find("input[type='checkbox']").each(function() {
				$(this).prop('checked', ($(this).is(':checked') ? false : true)).change();
			});
		}
	});

	$(document).on('keyup', '.on-change-remove-class', function() {
		$(this).parent().removeClass("shaker has-danger has-success");
	});

	$(document).on("change","select.connected_table",function(){//info search case 4 for see all things about this part
		var $element=$(this).parent().parent().parent().parent().parent(), $val=$(this).val();
		if($element.hasClass("row") && $element.hasClass("justify-content-center")){
			var $savedIDs="";
			$element.attr("class").split(" ").forEach(function(class_name){
				$class_name=class_name.split("-");
				if($class_name[0]=="column_saved_id"){
					$savedIDs=$class_name[1];
				}
			});
			$element.find(".table_optgroup_options").remove();
			if($val==0){
				$element.find(".connected_name, .connected_value").addClass("hide");
				$element.find(".option_name_div, .option_value_div").removeClass("hide");
				$element.find("select.connected_name, select.connected_value").attr("disabled",true).selectpicker("refresh");
				$element.find("selected.connected_name").empty().selectpicker("refresh");
				$element.find("selected.connected_value").empty().selectpicker("refresh");
			}else{
				$element.find(".option_name_div, .option_value_div").addClass("hide");
				$element.find(".connected_name, .connected_value").removeClass("hide");
				var $table_optgroup_options='<option class="data-text table_optgroup_options" value="-2" data-text-en="Persian name of selected table" data-text-fa="نام فارسی جدول انتخاب شده">'+(language=="en" ? "Persian name of selected table":"نام فارسی جدول انتخاب شده")+'</option><option class="data-text table_optgroup_options" value="-1" data-text-en="English name of selected table" data-text-fa="نام انگلیسی جدول انتخاب شده">'+(language=="en" ? "English name of selected table":"نام انگلیسی جدول انتخاب شده")+'</option>';
				$element.find("select.optgroup_id").prepend($table_optgroup_options).selectpicker("refresh");
				$element.find("select.connected_name, select.connected_value").each(function () {
					var $current_val=$(this).val();
					$(this).empty().load("table/class/action.php?select_option_loader=", {column_id: $savedIDs, connected_table: $val},function () {
						if($current_val==0 && $current_val!=""){
							$($(this).children("option")[0]).attr("selected",true);
							$(this).attr("disabled",false).selectpicker("refresh");
						}else{
							$(this).attr("disabled",false).selectpicker("val", $current_val).selectpicker("refresh");
						}
					});
				});
			}
		}
	});
	$(document).on("change","select.optgroup_id",function(){//info search case 4 for see all things about this part
		var $element=$(this).parent().parent().parent().parent().parent(), $val=$(this).val();
		if($element.hasClass("row") && $element.hasClass("justify-content-center")){
			var $savedIDs="";
			$element.attr("class").split(" ").forEach(function(class_name){
				$class_name=class_name.split("-");
				if($class_name[0]=="column_saved_id"){
					$savedIDs=$class_name[1];
				}
			});
			if($val=="*"){
				$element.find(".optgroup_id").addClass("hide");
				$element.find(".optgroup_new_div").removeClass("hide");
				$element.find("input.new_optgroup_text").focus();
			}else{
				$element.find(".optgroup_id").removeClass("hide");
				$element.find(".optgroup_new_div").addClass("hide");
			}
		}
	});
	$(document).on('click', '.optgroup-list', function(e) {//info search case 4 for see all things about this part
		var $element=$(this).parent().parent().parent().parent().parent().parent();
		if($element.hasClass("row") && $element.hasClass("justify-content-center")){
			var $savedIDs="";
			$element.attr("class").split(" ").forEach(function(class_name){
				$class_name=class_name.split("-");
				if($class_name[0]=="column_saved_id"){
					$savedIDs=$class_name[1];
				}
			});
			$element.find(".optgroup_id").removeClass("hide");
			$element.find(".optgroup_new_div").addClass("hide");
			$element.find("select.optgroup_id").selectpicker("val",'-').selectpicker("refresh");
			$("input.new_optgroup_text").val("").change();
		}
	});
	$(document).on('change', 'input.is_optgroup_opt', function() {//info search case 4 for see all things about this part
		var $element=$(this).parent().parent().parent().parent().parent().parent().parent();
		if($element.hasClass("row") && $element.hasClass("justify-content-center")){
			if($(this).is(":checked")){
				$element.find(".connected_table, .option_name_div, .option_value_div, .connected_name, .connected_value, .optgroup_id, .multiple-option, .optgroup_new_div").addClass("hide");
				$element.find(".optgroup_active, .optgroup_text_div").removeClass("hide");
			}else{
				$element.find(".optgroup_active, .optgroup_text_div").addClass("hide");
				$element.find(".connected_table, .multiple-option").removeClass("hide");
				if($element.find("select.optgroup_id").val()=="*"){
					$element.find(".optgroup_new_div").removeClass("hide");
				}else{
					$element.find(".optgroup_id").removeClass("hide");
				}
				if($element.find("select.connected_table").val()=='0'){
					$element.find(".connected_name, .connected_value").addClass("hide");
					$element.find(".table_optgroup_options").remove();
					$element.find(".optgroup_id").selectpicker("refresh");
					$element.find(".option_name_div, .option_value_div").removeClass("hide");
				}else{
					$element.find(".option_name_div, .option_value_div").addClass("hide");
					$element.find(".connected_name, .connected_value").removeClass("hide");
					$element.find(".table_optgroup_options").remove();
					var $table_optgroup_options='<option class="data-text table_optgroup_options" value="-2" data-text-en="Persian name of selected table" data-text-fa="نام فارسی جدول انتخاب شده">'+(language=="en" ? "Persian name of selected table":"نام فارسی جدول انتخاب شده")+'</option><option class="data-text table_optgroup_options" value="-1" data-text-en="English name of selected table" data-text-fa="نام انگلیسی جدول انتخاب شده">'+(language=="en" ? "English name of selected table":"نام انگلیسی جدول انتخاب شده")+'</option>';
					$element.find("select.optgroup_id").prepend($table_optgroup_options).selectpicker("refresh");
				}
			}
		}
	});
	function deleteSelectOption($element,$savedIDs,$ID) {//info search case 4 for see all things about this part
		$.post("table/class/action.php?selectOption=delete", {
			"column_id" : $savedIDs,
			"option_id" : $ID,
		}, function(data, status) {
			if (status == "success" && data.toString().indexOf("success")!=-1) {
				$element.find(".selectOpt_table").DataTable().ajax.reload( null, false );
				if($element.find(".skip_select_options").attr("id")!="undefined" && $element.find(".skip_select_options").attr("id")!=undefined){
					if($element.find(".skip_select_options").attr("id").split("-").length>=2){
						if(parseInt($element.find(".skip_select_options").attr("id").split("-")[1])==$ID){
							$element.find(".skip_select_options").click();
						}
					}
				}
			}else{
				feedbackOperations(data);
			}
		});
	}
	$(document).on('click', '.add_select_options', function() {//info search case 4 for see all things about this part
		$(this).removeClass("fa-plus-octagon").addClass("fa-spin fa-spinner");
		var $element=$(this).parent().parent().parent().parent(),$this=$(this);
		if($element.hasClass("row") && $element.hasClass("justify-content-center")){
			var $savedIDs="";
			$element.attr("class").split(" ").forEach(function(class_name){
				$class_name=class_name.split("-");
				if($class_name[0]=="column_saved_id"){
					$savedIDs=$class_name[1];
				}
			});
			if($savedIDs!=""){
				var $is_optgroup_opt=($element.find("input.is_optgroup_opt ").is(":checked") ? 1:0),
					$connected_table=$element.find("select.connected_table").val(),
					$connected_name=$element.find("select.connected_name").val(),
					$connected_value=$element.find("select.connected_value").val(),
					$option_name=$element.find("input.option_name").val(),
					$option_value=$element.find("input.option_value").val(),
					$optgroup_id=$element.find("select.optgroup_id").val(),
					$optgroup_text=$element.find("input.optgroup_text").val(),
					$new_optgroup_text=$element.find("input.new_optgroup_text").val(),
					error_selectoption=0;
				error_selectoption=($is_optgroup_opt==1 ? ($optgroup_text.length>0 ? 0:$element.find("input.optgroup_text").parent().addClass("has-danger remove-after-clear").children("input.optgroup_text").focus()):($connected_table.length==0 ? setTimeout(() => {$element.find("select.connected_table").next().click()}, 100):($connected_table==0 ? ($option_name.length>0 ? ($option_value.length>0 ? 0:$element.find("input.option_value").parent().addClass("has-danger remove-after-clear").children("input.option_value").focus()):$element.find("input.option_name").parent().addClass("has-danger remove-after-clear").children("input.option_name").focus()):($connected_name.length==0 ? setTimeout(() => {$element.find("select.connected_name").next().click()}, 100):($connected_value.length==0 ? setTimeout(() => {$element.find("select.connected_value").next().click()}, 100):0)))));
				error_selectoption=(error_selectoption==0 ? ($optgroup_id=="*" ? ($new_optgroup_text.length>0 ? 0:$element.find("input.new_optgroup_text").parent().addClass("has-danger remove-after-clear").children("input.new_optgroup_text").focus()):0):error_selectoption);
				if(error_selectoption==0){
					$.post("table/class/action.php?selectOption=add", {
						"is_optgroup_opt" : $is_optgroup_opt,
						"connected_table" : $connected_table,
						"connected_name" : $connected_name,
						"connected_value" : $connected_value,
						"option_name" : $option_name,
						"option_value" : $option_value,
						"optgroup_id" : $optgroup_id,
						"optgroup_text" : $optgroup_text,
						"new_optgroup_text" : $new_optgroup_text,
						"column_id" : $savedIDs
					}, function(data, status) {
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							$this.removeClass("fa-spin fa-spinner").addClass("fa-check");
							$.when($element.find('table.selectOpt_table').DataTable().ajax.reload( null, false )).done(function(){
								setTimeout(() => {
									$this.removeClass("fa-check").addClass("fa-plus-octagon");
								}, 1000);
							});
							$element.find(".clear_select_options").click();
						}else{
							feedbackOperations(data);
						}
					});
				}else{
					$this.removeClass("fa-spin fa-spinner").addClass("fa-exclamation-triangle");
					setTimeout(() => {
						$this.removeClass("fa-exclamation-triangle").addClass("fa-plus-octagon");
					}, 700);
				}
			}
		}
	});
	$(document).on('click', '.clear_select_options', function() {//info search case 4 for see all things about this part
		var $element=$(this).parent().parent().parent().parent();
		if($element.hasClass("row") && $element.hasClass("justify-content-center")){
			var $savedIDs="";
			$element.attr("class").split(" ").forEach(function(class_name){
				$class_name=class_name.split("-");
				if($class_name[0]=="column_saved_id"){
					$savedIDs=$class_name[1];
				}
			});
			if($savedIDs!=""){
				$element.find("input.is_optgroup_opt").prop('checked', false).change();
				$element.find("select.connected_table").selectpicker("val","").selectpicker("refresh").change();
				$element.find("input.option_name").val("").change();
				$element.find("input.option_value").val("").change();
				$element.find("input.optgroup_text").val("").change();
				$element.find("input.new_optgroup_text").val("").change();
				$element.find(".option_name_div").addClass("hide");
				$element.find(".option_value_div").addClass("hide");
				var $default_selected_data='<option disabled selected class="data-text" data-text-en="Please select a table" data-text-fa="لطفاً یک جدول انتخاب کنید" value="-">'+(language=="en" ? "Please select a table":"لطفاً یک جدول انتخاب کنید")+'</option>';
				$element.find("select.connected_name").empty().append($default_selected_data).selectpicker("refresh").change();
				$element.find("select.connected_value").empty().append($default_selected_data).selectpicker("refresh").change();
				$element.find(".connected_name").removeClass("hide");
				$element.find(".connected_value").removeClass("hide");
				$element.find("select.optgroup_id").load("table/class/action.php?optgroups", { "column_id": $savedIDs },function () {
					$element.find("select.optgroup_id").selectpicker("val","-").selectpicker("refresh").change();
				});
				$element.find(".remove-after-clear").removeClass("shaker has-danger has-success");
			}
		}
	});
	$(document).on('click', '.save_select_options', function() {//info search case 4 for see all things about this part
		var $element=$(this).parent().parent().parent().parent(),$this=$(this);
		var $ID=$(this).attr("id").split("-");
		$ID=($ID.length==2 ? $ID[1]:"-");
		if($element.hasClass("row") && $element.hasClass("justify-content-center") && $ID!="-"){
			var $savedIDs="";
			$element.attr("class").split(" ").forEach(function(class_name){
				$class_name=class_name.split("-");
				if($class_name[0]=="column_saved_id"){
					$savedIDs=$class_name[1];
				}
			});
			if($savedIDs!=""){
				var $is_optgroup_opt=($element.find("input.is_optgroup_opt ").is(":checked") ? 1:0),
					$connected_table=$element.find("select.connected_table").val(),
					$connected_name=$element.find("select.connected_name").val(),
					$connected_value=$element.find("select.connected_value").val(),
					$option_name=$element.find("input.option_name").val(),
					$option_value=$element.find("input.option_value").val(),
					$optgroup_id=$element.find("select.optgroup_id").val(),
					$optgroup_text=$element.find("input.optgroup_text").val(),
					$new_optgroup_text=$element.find("input.new_optgroup_text").val(),
					error_selectoption=0;
				error_selectoption=($is_optgroup_opt==1 ? ($optgroup_text.length>0 ? 0:$element.find("input.optgroup_text").parent().addClass("has-danger remove-after-clear").children("input.optgroup_text").focus()):($connected_table.length==0 ? setTimeout(() => {$element.find("select.connected_table").next().click()}, 100):($connected_table==0 ? ($option_name.length>0 ? ($option_value.length>0 ? 0:$element.find("input.option_value").parent().addClass("has-danger remove-after-clear").children("input.option_value").focus()):$element.find("input.option_name").parent().addClass("has-danger remove-after-clear").children("input.option_name").focus()):($connected_name.length==0 ? setTimeout(() => {$element.find("select.connected_name").next().click()}, 100):($connected_value.length==0 ? setTimeout(() => {$element.find("select.connected_value").next().click()}, 100):0)))));
				error_selectoption=(error_selectoption==0 ? ($optgroup_id=="*" ? ($new_optgroup_text.length>0 ? 0:$element.find("input.new_optgroup_text").parent().addClass("has-danger remove-after-clear").children("input.new_optgroup_text").focus()):0):error_selectoption);
				if(error_selectoption==0){
					$.post("table/class/action.php?selectOption=save", {
						"is_optgroup_opt" : $is_optgroup_opt,
						"connected_table" : $connected_table,
						"connected_name" : $connected_name,
						"connected_value" : $connected_value,
						"option_name" : $option_name,
						"option_value" : $option_value,
						"optgroup_id" : $optgroup_id,
						"optgroup_text" : $optgroup_text,
						"new_optgroup_text" : $new_optgroup_text,
						"column_id" : $savedIDs,
						"option_id" : $ID
					}, function(data, status) {
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							$this.removeClass("fa-spin fa-spinner").addClass("fa-check");
							$.when($element.find('table.selectOpt_table').DataTable().ajax.reload( null, false )).done(function(){
								$.when($this.removeClass("fa-check save_select_options").addClass("fa-plus-octagon save_select_options")).done(function(){
									$element.find(".skip_select_options").click();
								});
							});
							$element.find(".clear_select_options").click();
						}else if (status == "success" && data.toString().indexOf("deleted")!=-1) {
							$element.find(".add_select_options").click();
						}else{
							feedbackOperations(data);
						}
					});
				}else{
					$this.removeClass("fa-spin fa-spinner").addClass("fa-exclamation-triangle");
					setTimeout(() => {
						$this.removeClass("fa-exclamation-triangle").addClass("fa-plus-octagon");
					}, 700);
				}
			}
		}
	});
	$(document).on('click', '.copy_select_options', function() {//info search case 4 for see all things about this part
		var $element=$(this).parent().parent().parent().parent();
		if($element.hasClass("row") && $element.hasClass("justify-content-center")){
			$element.find(".add_select_options, .clear_select_options, .save_select_options , .copy_select_options, .skip_select_options, .delete_select_options").removeAttr("id");
			$element.find(".add_select_options, .clear_select_options").removeClass("hide");
			$element.find(".save_select_options , .copy_select_options, .skip_select_options, .delete_select_options").addClass("hide");
			$element.find(".add_select_options").click();
		}
	});
	$(document).on('click', '.skip_select_options', function() {//info search case 4 for see all things about this part
		var $element=$(this).parent().parent().parent().parent();
		if($element.hasClass("row") && $element.hasClass("justify-content-center")){
			$element.find(".add_select_options, .clear_select_options, .save_select_options , .copy_select_options, .skip_select_options, .delete_select_options").removeAttr("id");
			$element.find(".add_select_options, .clear_select_options").removeClass("hide");
			$element.find(".save_select_options , .copy_select_options, .skip_select_options, .delete_select_options").addClass("hide");
			$element.find(".clear_select_options").click();
		}
	});
	$(document).on('click', '.delete_select_options', function() {//info search case 4 for see all things about this part
		var $element=$(this).parent().parent().parent().parent();
		var $ID=$(this).attr("id").split("-");
		$ID=($ID.length==2 ? $ID[1]:"-");
		if($element.hasClass("row") && $element.hasClass("justify-content-center") && $ID!="-"){
			var $savedIDs="";
			$element.attr("class").split(" ").forEach(function(class_name){
				$class_name=class_name.split("-");
				if($class_name[0]=="column_saved_id"){
					$savedIDs=$class_name[1];
				}
			});
			if($savedIDs!=""){
				deleteSelectOption($element,$savedIDs,$ID);
			}
		}
	});
	$(document).on('click', '.edit-select-option', function(e) {//info search case 4 for see all things about this part
		if($(this).children().hasClass("stop-working")==false){
			var $element=$(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent();
			var $ID=$(this).attr("id").split("-");
			$ID=($ID.length==2 ? $ID[1]:"-");
			$element.find(".edit-select-option").children("i").removeClass("tim-icons icon-pencil").addClass("fad fa-spin fa-spinner stop-working");
			if($element.hasClass("row") && $element.hasClass("justify-content-center") && $ID!="-"){
				var $savedIDs="";
				$element.attr("class").split(" ").forEach(function(class_name){
					$class_name=class_name.split("-");
					if($class_name[0]=="column_saved_id"){
						$savedIDs=$class_name[1];
					}
				});
				$.when($element.find(".clear_select_options").click()).done(function(){
					try {
						function doTheOperation() {
							$element.find(".add_select_options, .clear_select_options").addClass("hide");
							$element.find(".save_select_options , .copy_select_options, .skip_select_options, .delete_select_options").removeClass("hide").each(function () {
								var $class_name=$(this).attr("class").split(" ");
								$class_name=$class_name[$class_name.length-1];
								$(this).attr("id",$class_name+"-"+$ID);
								if($(this).hasClass("delete_select_options")){
									$.post("table/class/action.php?selectOption=edit", {
										"column_id" : $savedIDs,
										"option_id" : $ID
									}, function(data, status) {
										if (status == "success" && data.toString().indexOf("success")!=-1) {
											var $data=data.split("_._");
											if($data.length>=1){
												$.when($element.find("input.is_optgroup_opt").prop("checked", ($data[0]==1 ? true:false)).change()).done(function(){
													if($data.length>=2){
														if($data[0]==1){
															$.when($element.find("input.optgroup_text").val($data[3]).change()).done(function(){
																$element.find(".edit-select-option").children("i").addClass("tim-icons icon-pencil").removeClass("fad fa-spin fa-spinner stop-working");
																scrollToElement($element.find("input.is_optgroup_opt"));
															});
														}else{
															if($data.length>=2){
																$.when($element.find("select.optgroup_id").selectpicker("val",$data[1]).change()).done(function(){
																	if($data.length>=3){
																		if($data[2]=="0"){
																			$.when($element.find("select.connected_table").selectpicker("val",$data[2]).change()).done(function(){
																				if($data.length>=4){
																					$.when($element.find("input.option_name").val($data[3]).change()).done(function(){
																						if($data.length>=5){
																							$.when($element.find("input.option_value").val($data[4]).change()).done(function(){
																								$element.find(".edit-select-option").children("i").addClass("tim-icons icon-pencil").removeClass("fad fa-spin fa-spinner stop-working");
																								scrollToElement($element.find("input.is_optgroup_opt"));
																							});
																						}
																					});
																				}
																			});
																		}else{
																			$.when($element.find("select.connected_table").selectpicker("val",$data[2])).done(function(){
																				$element.find("select.connected_name, select.connected_value").each(function () {
																					$(this).empty().load("table/class/action.php?select_option_loader=", {column_id: $savedIDs, connected_table: $data[2]},function () {
																						scrollToElement($element.find("input.is_optgroup_opt"));
																						if($(this).hasClass("connected_name")){
																							var $what_is_it=3;
																						}else if($(this).hasClass("connected_value")){
																							var $what_is_it=4;
																						}
																						if($data[$what_is_it]==0 && $data[$what_is_it]!=""){
																							$($(this).children("option")[0]).attr("selected",true);
																							$(this).attr("disabled",false).selectpicker("refresh");
																						}else{
																							$(this).attr("disabled",false).selectpicker("val", $data[$what_is_it]).selectpicker("refresh");
																						}
																						if($(this).hasClass("connected_value")){
																							$element.find(".edit-select-option").children("i").addClass("tim-icons icon-pencil").removeClass("fad fa-spin fa-spinner stop-working");
																						}
																					});
																				});
																			});
																		}
																	}
																});
															}
														}
													}else{
														$element.find(".skip_select_options").click();
													}
												});
											}else{
												$element.find(".skip_select_options").click();
											}
										}else{
											feedbackOperations(data);
										}
									});
								}
							});
						}
						if(alertShowerSetting['copySelectOpt_showAlert']=="true" || $disable_alert==1){
							doTheOperation();
						}else{
							if(language=="fa"){
								Swal.fire({
									title: 'آیا مطمئن  هستید؟',
									text: "آیا میخواهید این گزینه ویرایش شود ؟",
									icon: 'warning',
									showCancelButton: true,
									customClass: {
										confirmButton: 'btn btn-success',
										cancelButton: 'btn btn-danger'
									},
									buttonsStyling: false,
									confirmButtonText: 'بله',
									cancelButtonText: 'لغو',
									footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'copySelectOpt_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
								}).then((result) => {
									if(result.value) {
										doTheOperation();
									}else{
										$element.find(".edit-select-option").children("i").addClass("tim-icons icon-pencil").removeClass("fad fa-spin fa-spinner stop-working");
									}
								});
							}else{
								Swal.fire({
									title: 'Are you sure?',
									text: "Are you really want to edit this option ?",
									icon: 'warning',
									showCancelButton: true,
									customClass: {
										confirmButton: 'btn btn-success',
										cancelButton: 'btn btn-danger'
									},
									buttonsStyling: false,
									confirmButtonText: 'Yes',
									footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'copySelectOpt_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
								}).then((result) => {
									if(result.value) {
										doTheOperation();
									}else{
										$element.find(".edit-select-option").children("i").addClass("tim-icons icon-pencil").removeClass("fad fa-spin fa-spinner stop-working");
									}
								});
							}
						}
					} catch(err) {
						alertShowerSetting['copySelectOpt_showAlert']="false";
					}
				});
			}
		}
	});
	$(document).on('click', '.delete-select-option', function(e) {//info search case 4 for see all things about this part
		var $element=$(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent();
		var $ID=$(this).attr("id").split("-");
		$ID=($ID.length==2 ? $ID[1]:"-");
		if($element.hasClass("row") && $element.hasClass("justify-content-center") && $ID!="-"){
			var $savedIDs="";
			$element.attr("class").split(" ").forEach(function(class_name){
				$class_name=class_name.split("-");
				if($class_name[0]=="column_saved_id"){
					$savedIDs=$class_name[1];
				}
			});
			try {
				function doTheOperation() {
					deleteSelectOption($element,$savedIDs,$ID);
				}
				if(alertShowerSetting['deleteSelectOpt_showAlert']=="true" || $disable_alert==1){
					doTheOperation();
				}else{
					if(language=="fa"){
						Swal.fire({
							title: 'آیا مطمئن  هستید؟',
							text: "آیا میخواهید این گزینه حذف شود ؟",
							icon: 'warning',
							showCancelButton: true,
							customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
							buttonsStyling: false,
							confirmButtonText: 'بله',
							cancelButtonText: 'لغو',
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteSelectOpt_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
						}).then((result) => {
							if(result.value) {
								doTheOperation();
							}
						});
					}else{
						Swal.fire({
							title: 'Are you sure?',
							text: "Are you really want to delete this option ?",
							icon: 'warning',
							showCancelButton: true,
							customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
							buttonsStyling: false,
							confirmButtonText: 'Yes',
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteSelectOpt_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
						}).then((result) => {
							if(result.value) {
								doTheOperation();
							}
						});
					}
				}
			} catch(err) {
				alertShowerSetting['deleteSelectOpt_showAlert']="false";
			}
		}
	});

	function deleteCheckOption($element,$savedIDs,$ID) {//info search case 9 for see all things about this part
		$.post("table/class/action.php?checkboxOption=delete", {
			"column_id" : $savedIDs,
			"option_id" : $ID,
		}, function(data, status) {
			if (status == "success" && data.toString().indexOf("success")!=-1) {
				$element.find(".checkboxOpt_table").DataTable().ajax.reload( null, false );
				if($element.find(".skip_checkbox_options").attr("id")!="undefined" && $element.find(".skip_checkbox_options").attr("id")!=undefined){
					if($element.find(".skip_checkbox_options").attr("id").split("-").length>=2){
						if(parseInt($element.find(".skip_checkbox_options").attr("id").split("-")[1])==$ID){
							$element.find(".skip_checkbox_options").click();
						}
					}
				}
			}else{
				feedbackOperations(data);
			}
		});
	}
	$(document).on('click', '.add_checkbox_options', function() {//info search case 9 for see all things about this part
		$(this).removeClass("fa-plus-octagon").addClass("fa-spin fa-spinner");
		var $element=$(this).parent().parent().parent().parent(),$this=$(this);
		if($element.hasClass("row") && $element.hasClass("justify-content-center")){
			var $savedIDs="";
			$element.attr("class").split(" ").forEach(function(class_name){
				$class_name=class_name.split("-");
				if($class_name[0]=="column_saved_id"){
					$savedIDs=$class_name[1];
				}
			});
			if($savedIDs!=""){
				var $option_name=$element.find("input.checkbox_name").val(),
					$option_value=$element.find("input.checkbox_value").val(),
					$option_false=$element.find("input.checkbox_false").val(),
					error_selectoption=0;
				error_selectoption=($option_name.length ? ($option_value.length ? 0:$element.find("input.checkbox_value").focus().parent().addClass("has-danger shaker")):$element.find("input.checkbox_name").focus().parent().addClass("has-danger shaker"));
				if(error_selectoption==0){
					$.post("table/class/action.php?checkboxOption=add", {
						"option_name" : $option_name,
						"option_value" : $option_value,
						"option_false" : $option_false,
						"column_id" : $savedIDs
					}, function(data, status) {
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							$this.removeClass("fa-spin fa-spinner").addClass("fa-check");
							$.when($element.find('table.checkboxOpt_table').DataTable().ajax.reload( null, false )).done(function(){
								setTimeout(() => {
									$this.removeClass("fa-check").addClass("fa-plus-octagon");
								}, 1000);
							});
							$element.find(".clear_checkbox_options").click();
						}else{
							feedbackOperations(data);
						}
					});
				}else{
					$this.removeClass("fa-spin fa-spinner").addClass("fa-exclamation-triangle");
					setTimeout(() => {
						$this.removeClass("fa-exclamation-triangle").addClass("fa-plus-octagon");
					}, 700);
				}
			}
		}
	});
	$(document).on('click', '.clear_checkbox_options', function() {//info search case 9 for see all things about this part
		var $element=$(this).parent().parent().parent().parent();
		if($element.hasClass("row") && $element.hasClass("justify-content-center")){
			var $savedIDs="";
			$element.attr("class").split(" ").forEach(function(class_name){
				$class_name=class_name.split("-");
				if($class_name[0]=="column_saved_id"){
					$savedIDs=$class_name[1];
				}
			});
			if($savedIDs!=""){
				$element.find("input.checkbox_name").val("").change();
				$element.find("input.checkbox_value").val("").change();
				$element.find("input.checkbox_false").val("").change();
				$element.find(".remove-after-clear").removeClass("shaker has-danger has-success");
			}
		}
	});
	$(document).on('click', '.save_checkbox_options', function() {//info search case 9 for see all things about this part
		var $element=$(this).parent().parent().parent().parent(),$this=$(this);
		var $ID=$(this).attr("id").split("-");
		$ID=($ID.length==2 ? $ID[1]:"-");
		if($element.hasClass("row") && $element.hasClass("justify-content-center") && $ID!="-"){
			var $savedIDs="";
			$element.attr("class").split(" ").forEach(function(class_name){
				$class_name=class_name.split("-");
				if($class_name[0]=="column_saved_id"){
					$savedIDs=$class_name[1];
				}
			});
			if($savedIDs!=""){
				var $option_name=$element.find("input.checkbox_name").val(),
					$option_value=$element.find("input.checkbox_value").val(),
					$option_false=$element.find("input.checkbox_false").val(),
					error_selectoption=0;
				error_selectoption=($option_name.length ? ($option_value.length ? 0:$element.find("input.checkbox_value").focus().parent().addClass("has-danger shaker")):$element.find("input.checkbox_name").focus().parent().addClass("has-danger shaker"));
				if(error_selectoption==0){
					$.post("table/class/action.php?checkboxOption=save", {
						"option_name" : $option_name,
						"option_value" : $option_value,
						"option_false" : $option_false,
						"column_id" : $savedIDs,
						"option_id" : $ID
					}, function(data, status) {
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							$this.removeClass("fa-spin fa-spinner").addClass("fa-check");
							$.when($element.find('table.checkboxOpt_table').DataTable().ajax.reload( null, false )).done(function(){
								$.when($this.removeClass("fa-check save_checkbox_options").addClass("fa-plus-octagon save_checkbox_options")).done(function(){
									$element.find(".skip_checkbox_options").click();
								});
							});
							$element.find(".clear_checkbox_options").click();
						}else if (status == "success" && data.toString().indexOf("deleted")!=-1) {
							$element.find(".add_checkbox_options").click();
						}else{
							feedbackOperations(data);
						}
					});
				}else{
					$this.removeClass("fa-spin fa-spinner").addClass("fa-exclamation-triangle");
					setTimeout(() => {
						$this.removeClass("fa-exclamation-triangle").addClass("fa-plus-octagon");
					}, 700);
				}
			}
		}
	});
	$(document).on('click', '.copy_checkbox_options', function() {//info search case 9 for see all things about this part
		var $element=$(this).parent().parent().parent().parent();
		if($element.hasClass("row") && $element.hasClass("justify-content-center")){
			$element.find(".add_checkbox_options, .clear_checkbox_options, .save_checkbox_options , .copy_checkbox_options, .skip_checkbox_options, .delete_checkbox_options").removeAttr("id");
			$element.find(".add_checkbox_options, .clear_checkbox_options").removeClass("hide");
			$element.find(".save_checkbox_options , .copy_checkbox_options, .skip_checkbox_options, .delete_checkbox_options").addClass("hide");
			$element.find(".add_checkbox_options").click();
		}
	});
	$(document).on('click', '.skip_checkbox_options', function() {//info search case 9 for see all things about this part
		var $element=$(this).parent().parent().parent().parent();
		if($element.hasClass("row") && $element.hasClass("justify-content-center")){
			$element.find(".add_checkbox_options, .clear_checkbox_options, .save_checkbox_options , .copy_checkbox_options, .skip_checkbox_options, .delete_checkbox_options").removeAttr("id");
			$element.find(".add_checkbox_options, .clear_checkbox_options").removeClass("hide");
			$element.find(".save_checkbox_options , .copy_checkbox_options, .skip_checkbox_options, .delete_checkbox_options").addClass("hide");
			$element.find(".clear_checkbox_options").click();
		}
	});
	$(document).on('click', '.delete_checkbox_options', function() {//info search case 9 for see all things about this part
		var $element=$(this).parent().parent().parent().parent();
		var $ID=$(this).attr("id").split("-");
		$ID=($ID.length==2 ? $ID[1]:"-");
		if($element.hasClass("row") && $element.hasClass("justify-content-center") && $ID!="-"){
			var $savedIDs="";
			$element.attr("class").split(" ").forEach(function(class_name){
				$class_name=class_name.split("-");
				if($class_name[0]=="column_saved_id"){
					$savedIDs=$class_name[1];
				}
			});
			if($savedIDs!=""){
				deleteCheckOption($element,$savedIDs,$ID);
			}
		}
	});
	$(document).on('click', '.edit-checkbox-option', function(e) {//info search case 9 for see all things about this part
		if($(this).children().hasClass("stop-working")==false){
			var $element=$(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent();
			var $ID=$(this).attr("id").split("-");
			$ID=($ID.length==2 ? $ID[1]:"-");
			$element.find(".edit-checkbox-option").children("i").removeClass("tim-icons icon-pencil").addClass("fad fa-spin fa-spinner stop-working");
			if($element.hasClass("row") && $element.hasClass("justify-content-center") && $ID!="-"){
				var $savedIDs="";
				$element.attr("class").split(" ").forEach(function(class_name){
					$class_name=class_name.split("-");
					if($class_name[0]=="column_saved_id"){
						$savedIDs=$class_name[1];
					}
				});
				$.when($element.find(".clear_checkbox_options").click()).done(function(){
					try {
						function doTheOperation() {
							$element.find(".add_checkbox_options, .clear_checkbox_options").addClass("hide");
							$element.find(".save_checkbox_options , .copy_checkbox_options, .skip_checkbox_options, .delete_checkbox_options").removeClass("hide").each(function () {
								var $class_name=$(this).attr("class").split(" ");
								$class_name=$class_name[$class_name.length-1];
								$(this).attr("id",$class_name+"-"+$ID);
								if($(this).hasClass("delete_checkbox_options")){
									$.post("table/class/action.php?checkboxOption=edit", {
										"column_id" : $savedIDs,
										"option_id" : $ID
									}, function(data, status) {
										if (status == "success" && data.toString().indexOf("success")!=-1) {
											var $data=data.split("_._");
											if($data.length>=1){
												$.when($element.find("input.checkbox_name").val($data[0]).change()).done(function(){
													if($data.length>=2){
														$.when($element.find("input.checkbox_value").val($data[1]).change()).done(function(){
															if($data.length>=3){
																$.when($element.find("input.checkbox_false").val($data[2]).change()).done(function(){
																	$element.find(".edit-checkbox-option").children("i").addClass("tim-icons icon-pencil").removeClass("fad fa-spin fa-spinner stop-working");
																	scrollToElement($element.find("input.checkbox_name"));
																});
															}
														});
													}
												});
											}
										}else{
											feedbackOperations(data);
										}
									});
								}
							});
						}
						if(alertShowerSetting['copyCheckboxOpt_showAlert']=="true" || $disable_alert==1){
							doTheOperation();
						}else{
							if(language=="fa"){
								Swal.fire({
									title: 'آیا مطمئن  هستید؟',
									text: "آیا میخواهید این گزینه ویرایش شود ؟",
									icon: 'warning',
									showCancelButton: true,
									customClass: {
										confirmButton: 'btn btn-success',
										cancelButton: 'btn btn-danger'
									},
									buttonsStyling: false,
									confirmButtonText: 'بله',
									cancelButtonText: 'لغو',
									footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'copyCheckboxOpt_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
								}).then((result) => {
									if(result.value) {
										doTheOperation();
									}else{
										$element.find(".edit-checkbox-option").children("i").addClass("tim-icons icon-pencil").removeClass("fad fa-spin fa-spinner stop-working");
									}
								});
							}else{
								Swal.fire({
									title: 'Are you sure?',
									text: "Are you really want to edit this option ?",
									icon: 'warning',
									showCancelButton: true,
									customClass: {
										confirmButton: 'btn btn-success',
										cancelButton: 'btn btn-danger'
									},
									buttonsStyling: false,
									confirmButtonText: 'Yes',
									footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'copyCheckboxOpt_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
								}).then((result) => {
									if(result.value) {
										doTheOperation();
									}else{
										$element.find(".edit-checkbox-option").children("i").addClass("tim-icons icon-pencil").removeClass("fad fa-spin fa-spinner stop-working");
									}
								});
							}
						}
					} catch(err) {
						alertShowerSetting['copyCheckboxOpt_showAlert']="false";
					}
				});
			}
		}
	});
	$(document).on('click', '.delete-checkbox-option', function(e) {//info search case 9 for see all things about this part
		var $element=$(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent();
		var $ID=$(this).attr("id").split("-");
		$ID=($ID.length==2 ? $ID[1]:"-");
		if($element.hasClass("row") && $element.hasClass("justify-content-center") && $ID!="-"){
			var $savedIDs="";
			$element.attr("class").split(" ").forEach(function(class_name){
				$class_name=class_name.split("-");
				if($class_name[0]=="column_saved_id"){
					$savedIDs=$class_name[1];
				}
			});
			try {
				function doTheOperation() {
					deleteCheckOption($element,$savedIDs,$ID);
				}
				if(alertShowerSetting['deleteCheckboxOpt_showAlert']=="true" || $disable_alert==1){
					doTheOperation();
				}else{
					if(language=="fa"){
						Swal.fire({
							title: 'آیا مطمئن  هستید؟',
							text: "آیا میخواهید این گزینه حذف شود ؟",
							icon: 'warning',
							showCancelButton: true,
							customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
							buttonsStyling: false,
							confirmButtonText: 'بله',
							cancelButtonText: 'لغو',
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteCheckboxOpt_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
						}).then((result) => {
							if(result.value) {
								doTheOperation();
							}
						});
					}else{
						Swal.fire({
							title: 'Are you sure?',
							text: "Are you really want to delete this option ?",
							icon: 'warning',
							showCancelButton: true,
							customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
							buttonsStyling: false,
							confirmButtonText: 'Yes',
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteCheckboxOpt_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
						}).then((result) => {
							if(result.value) {
								doTheOperation();
							}
						});
					}
				}
			} catch(err) {
				alertShowerSetting['deleteCheckboxOpt_showAlert']="false";
			}
		}
	});

	$(document).on('click', '.changer', function(e) {
		$this=$(e.target);
		if($this.prop("tagName")=="LABEL" && $this.hasClass("data-text")){
			$(this).find("input[type='checkbox']").each(function() {
				$(this).prop('checked', ($(this).is(':checked') ? false : true));
				$(this).parent().parent().parent().prev().children().each(function () {
					$(this).toggleClass("hide");
				});
			});
		}
	});

	$(document).on('click', '.btn-finish', function() {
		var $nav=$(".wizard-navigation ul.custom_transition.nav.nav-pills");
		$(".card-wizard").find($('.progress-bar')).css({width: '100%'});
		getElement(".btn-wd").attr("disabled", true);
		$nav.find('li a').addClass("create_table_loading");
		$.post("table/class/action.php?finish", {}, function(data, status) {
			if (status == "success" && data.toString().indexOf("success")!=-1) {
				updateLevel(2);
			}else{
				feedbackOperations(data);
			}
		});
	});

	$(document).on("change",".changer input[type='checkbox']",function(){
		$(this).parent().parent().parent().prev().children().each(function () {
			$(this).toggleClass("hide");
		});
	});

	$(document).on("change","select.input_mode.input_select",function(){
		var $element=$(this).parent().parent().parent().parent(),$isSaved=($element.length ? ($element.hasClass("saved_column") ? 1:0):0),$datas="",$this=$(this);
		var $savedIDs="";
		$element.attr("class").split(" ").forEach(function(class_name){
			$class_name=class_name.split("-");
			if($class_name[0]=="column_saved_id"){
				$savedIDs=$class_name[1];
			}
		});
		$element.find(".extra-options").empty();
		<?php //info search this_is_modes_for_data_tables for see all things about this part ?>
		switch ($(this).val()) {//tables_mode_code
			case "3":case 3://info search case 3 for see all things about this part
				if($isSaved){
					$.post("table/class/action.php?changeMode", {
						"id" : $savedIDs,
						"mode" : $this.val(),
					}, function(data, status) {
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							var $data=data.split("_._");
							$datas=($data[0]!="" ? JSON.parse($data[0]):["","","",""]);
							$string_extra="";
							$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
								$string_extra+='<div class="form-group"> ';
									$string_extra+='<input class="form-control data-placeholder data-original-title input_yes_option input_text on-press-enter-column" rel="tooltip" data-original-title-en="Enabled/Yes" data-original-title-fa="فعال/بله" data-original-title="'+(language == "en" ? "Enabled/Yes" : "فعال/بله")+'" data-placement="top" data-placeholder-en="Enabled/Yes" data-placeholder-fa="فعال/بله" placeholder="'+(language=="en" ? "Enabled/Yes":"فعال/بله")+'" type="text" data-reset-value="'+$datas[0]+'" value="'+$datas[0]+'"> ';
								$string_extra+='</div> ';
							$string_extra+='</div> ';
							$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
								$string_extra+='<div class="form-group"> ';
									$string_extra+='<input class="form-control data-placeholder data-original-title input_no_option input_text on-press-enter-column" rel="tooltip" data-original-title-en="Disable/No" data-original-title-fa="غیرفعال/خیر" data-original-title="'+(language == "en" ? "Disable/No" : "غیرفعال/خیر")+'" data-placement="top" data-placeholder-en="Disable/No" data-placeholder-fa="غیرفعال/خیر" placeholder="'+(language=="en" ? "Disable/No":"غیرفعال/خیر")+'" type="text" data-reset-value="'+$datas[1]+'" value="'+$datas[1]+'"> ';
								$string_extra+='</div> ';
							$string_extra+='</div> ';
							$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
								$string_extra+='<div class="form-group"> ';
									$string_extra+='<input class="form-control data-placeholder data-original-title input_yes_value input_text on-press-enter-column" rel="tooltip" data-original-title-en="1/true" data-original-title-fa="1/true1" data-original-title="'+(language == "en" ? "1/true" : "1/true1")+'" data-placement="top" data-placeholder-en="1/true" data-placeholder-fa="1/true1" placeholder="'+(language=="en" ? "1/true":"1/true1")+'" type="text" data-reset-value="'+($datas[2]!="" ? $datas[2]:1)+'" value="'+($datas[2]!="" ? $datas[2]:1)+'"> ';
								$string_extra+='</div> ';
							$string_extra+='</div> ';
							$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
								$string_extra+='<div class="form-group"> ';
									$string_extra+='<input class="form-control data-placeholder data-original-title input_no_value input_text on-press-enter-column" rel="tooltip" data-original-title-en="0/false" data-original-title-fa="0/false" data-original-title="'+(language == "en" ? "0/false" : "0/false")+'" data-placement="top" data-placeholder-en="0/false" data-placeholder-fa="0/false" placeholder="'+(language=="en" ? "0/false":"0/false")+'" type="text" data-reset-value="'+($datas[3]!="" ? $datas[3]:0)+'" value="'+($datas[3]!="" ? $datas[3]:0)+'"> ';
								$string_extra+='</div> ';
							$string_extra+='</div>';
							$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
								$string_extra+='<div class="form-group"> ';
									$string_extra+='<input class="form-control data-placeholder data-original-title input_yes_icon input_text on-press-enter-column" rel="tooltip" data-original-title-en="far fa-check" data-original-title-fa="far fa-check" data-original-title="'+(language=="en" ? "far fa-check":"far fa-check")+'" data-placement="top" data-placeholder-en="far fa-check" data-placeholder-fa="far fa-check" placeholder="'+(language=="en" ? "far fa-check":"far fa-check")+'" type="text" data-reset-value="'+$datas[4]+'" value="'+$datas[4]+'"> ';
								$string_extra+='</div> ';
							$string_extra+='</div>';
							$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
								$string_extra+='<div class="form-group"> ';
									$string_extra+='<input class="form-control data-placeholder data-original-title input_no_icon input_text on-press-enter-column" rel="tooltip" data-original-title-en="far fa-square" data-original-title-fa="far fa-square" data-original-title="'+(language=="en" ? "far fa-square":"far fa-square")+'" data-placement="top" data-placeholder-en="far fa-square" data-placeholder-fa="far fa-square" placeholder="'+(language=="en" ? "far fa-square":"far fa-square")+'" type="text" data-reset-value="'+$datas[5]+'" value="'+$datas[5]+'"> ';
								$string_extra+='</div> ';
							$string_extra+='</div>';
							$element.find(".extra-options").append($string_extra);
						}else{
							feedbackOperations(data);
						}
					});
				}else{
					$datas=["","","","","",""];
					$string_extra="";
					$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
						$string_extra+='<div class="form-group"> ';
							$string_extra+='<input class="form-control data-placeholder data-original-title input_yes_option input_text on-press-enter-column" rel="tooltip" data-original-title-en="Enabled/Yes" data-original-title-fa="فعال/بله" data-original-title="'+(language == "en" ? "Enabled/Yes" : "فعال/بله")+'" data-placement="top" data-placeholder-en="Enabled/Yes" data-placeholder-fa="فعال/بله" placeholder="'+(language=="en" ? "Enabled/Yes":"فعال/بله")+'" type="text" data-reset-value="'+$datas[0]+'" value="'+$datas[0]+'"> ';
						$string_extra+='</div> ';
					$string_extra+='</div> ';
					$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
						$string_extra+='<div class="form-group"> ';
							$string_extra+='<input class="form-control data-placeholder data-original-title input_no_option input_text on-press-enter-column" rel="tooltip" data-original-title-en="Disable/No" data-original-title-fa="غیرفعال/خیر" data-original-title="'+(language == "en" ? "Disable/No" : "غیرفعال/خیر")+'" data-placement="top" data-placeholder-en="Disable/No" data-placeholder-fa="غیرفعال/خیر" placeholder="'+(language=="en" ? "Disable/No":"غیرفعال/خیر")+'" type="text" data-reset-value="'+$datas[1]+'" value="'+$datas[1]+'"> ';
						$string_extra+='</div> ';
					$string_extra+='</div> ';
					$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
						$string_extra+='<div class="form-group"> ';
							$string_extra+='<input class="form-control data-placeholder data-original-title input_yes_value input_text on-press-enter-column" rel="tooltip" data-original-title-en="1/true" data-original-title-fa="1/true1" data-original-title="'+(language == "en" ? "1/true" : "1/true1")+'" data-placement="top" data-placeholder-en="1/true" data-placeholder-fa="1/true1" placeholder="'+(language=="en" ? "1/true":"1/true1")+'" type="text" data-reset-value="'+$datas[2]+'" value="'+$datas[2]+'"> ';
						$string_extra+='</div> ';
					$string_extra+='</div> ';
					$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
						$string_extra+='<div class="form-group"> ';
							$string_extra+='<input class="form-control data-placeholder data-original-title input_no_value input_text on-press-enter-column" rel="tooltip" data-original-title-en="0/false" data-original-title-fa="0/false" data-original-title="'+(language == "en" ? "0/false" : "0/false")+'" data-placement="top" data-placeholder-en="0/false" data-placeholder-fa="0/false" placeholder="'+(language=="en" ? "0/false":"0/false")+'" type="text" data-reset-value="'+$datas[3]+'" value="'+$datas[3]+'"> ';
						$string_extra+='</div> ';
					$string_extra+='</div>';
					$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
						$string_extra+='<div class="form-group"> ';
							$string_extra+='<input class="form-control data-placeholder data-original-title input_yes_icon input_text on-press-enter-column" rel="tooltip" data-original-title-en="far fa-check" data-original-title-fa="far fa-check" data-original-title="'+(language=="en" ? "far fa-check":"far fa-check")+'" data-placement="top" data-placeholder-en="far fa-check" data-placeholder-fa="far fa-check" placeholder="'+(language=="en" ? "far fa-check":"far fa-check")+'" type="text" data-reset-value="'+$datas[4]+'" value="'+$datas[4]+'"> ';
						$string_extra+='</div> ';
					$string_extra+='</div>';
					$string_extra+='<div class="col-lg-2 col-md-6 col-12"> ';
						$string_extra+='<div class="form-group"> ';
							$string_extra+='<input class="form-control data-placeholder data-original-title input_no_icon input_text on-press-enter-column" rel="tooltip" data-original-title-en="far fa-square" data-original-title-fa="far fa-square" data-original-title="'+(language=="en" ? "far fa-square":"far fa-square")+'" data-placement="top" data-placeholder-en="far fa-square" data-placeholder-fa="far fa-square" placeholder="'+(language=="en" ? "far fa-square":"far fa-square")+'" type="text" data-reset-value="'+$datas[5]+'" value="'+$datas[5]+'"> ';
						$string_extra+='</div> ';
					$string_extra+='</div>';
					$element.find(".extra-options").append($string_extra);
				}
			break;
			case "4":case 4://info search case 4 for see all things about this part
				if($isSaved){
					$.post("table/class/action.php?changeMode", {
						"id" : $savedIDs,
						"mode" : $this.val(),
					}, function(data, status) {
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							var $data=data.split("_._");
							$datas=($data[0]!="" ? JSON.parse($data[0]):["","","",""]);
							$string_extra='<div class="col-md-12 row mr-auto ml-auto pb-3">';
								$string_extra+='<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto">';
									$string_extra+='<div class="form-check cursor-pointer">';
										$string_extra+='<label class="form-check-label">';
											$string_extra+='<input class="form-check-input input_is_multiple input_checkbox" type="checkbox" '+($datas[0] ? "checked":"")+' data-reset-value="'+$datas[0]+'">';
											$string_extra+='<span class="form-check-sign"></span>';
											$string_extra+='<label class="data-text" data-text-en="Multiple ?" data-text-fa="چندتایی ؟">'+(language=="en" ? "Multiple ?" : "چندتایی ؟")+'</label>';
										$string_extra+='</label>';
									$string_extra+='</div>';
								$string_extra+='</div>';
								$string_extra+='<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto hide">';
									$string_extra+='<div class="form-check cursor-pointer">';
										$string_extra+='<label class="form-check-label">';
											$string_extra+='<input class="form-check-input input_is_forced input_checkbox" checked type="checkbox" '+($datas[1] ? "checked":"")+' data-reset-value="'+$datas[1]+'">';
											$string_extra+='<span class="form-check-sign"></span>';
											$string_extra+='<label class="data-text" data-text-en="Forced ?" data-text-fa="اجباری ؟">'+(language == "en" ? "Forced ?" : "اجباری ؟")+'</label>';
										$string_extra+='</label>';
									$string_extra+='</div>';
								$string_extra+='</div>';
								<?php
									/*
										$string_extra+='<div class="col-lg-3 col-12 mb-2 ml-auto mr-auto">';
											$string_extra+='<div class="form-group">';
												$string_extra+='<input class="form-control data-placeholder data-original-title input_min_allowed input_text" rel="tooltip" data-original-title-en="Minimum amount allowed" data-original-title-fa="کمترین مقدار مجاز" data-original-title="'+(language== "en" ? "Minimum amount allowed" : "کمترین مقدار مجاز")+'" data-placement="top" data-placeholder-en="Minimum amount allowed" data-placeholder-fa="کمترین مقدار مجاز" placeholder="'+(language=="en" ? "Minimum amount allowed":"کمترین مقدار مجاز")+'" type="number" data-reset-value="'+$datas[2]+'" value="'+$datas[2]+'">';
											$string_extra+='</div>';
										$string_extra+='</div>';
										$string_extra+='<div class="col-lg-3 col-12 mb-2 ml-auto mr-auto">';
											$string_extra+='<div class="form-group">';
												$string_extra+='<input class="form-control data-placeholder data-original-title input_max_allowed input_text" rel="tooltip" data-original-title-en="Maximum amount allowed" data-original-title-fa="بیشترین مقدار مجاز" data-original-title="'+(language == "en" ? "Maximum amount allowed" : "بیشترین مقدار مجاز")+'" data-placement="top" data-placeholder-en="Maximum amount allowed" data-placeholder-fa="بیشترین مقدار مجاز" placeholder="'+(language=="en" ? "Maximum amount allowed":"بیشترین مقدار مجاز")+'" type="number" data-reset-value="'+$datas[3]+'" value="'+$datas[3]+'">';
											$string_extra+='</div>';
										$string_extra+='</div>';
									*/
								?>
							$string_extra+='</div>';
							$string_extra+='<div class="col-md-12 row mr-auto ml-auto">';
								$string_extra+='<div class="col-lg-6 col-12 row mb-2 ml-auto mr-auto">';
								$string_extra+='<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto">';
								$string_extra+='<div class="form-check cursor-pointer checkbox_changer">';
								$string_extra+='<label class="form-check-label">';
								$string_extra+='<input class="form-check-input is_optgroup_opt" type="checkbox">';
								$string_extra+='<span class="form-check-sign"></span>';
								$string_extra+='<label class="data-text" data-text-en="Optgroup ?" data-text-fa="سرگروه ؟">'+(language == "en" ? "Optgroup ?" : "سرگروه ؟")+'</label>';
								$string_extra+='</label>';
								$string_extra+='</div>';
								$string_extra+='</div>';
								$string_extra+='<select class="selectpicker data-title col-lg-4 col-12 mb-2 ml-auto mr-auto connected_table" data-title-en="Tables" data-title-fa="جدول ها" data-style="btn btn-primary" title="'+(language=="en" ? "Tables":"جدول ها")+'" data-size="7" data-live-search="true">';
								$string_extra+='<option class="data-text" value="0" data-text-en="Manual" data-text-fa="دستی">';
									$string_extra+=(language=="en" ? "Manual":"دستی");
								$string_extra+='</option>';
								<?php
									$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_config ORDER BY ordering ASC");
									while($tables=$res_tables->fetch()){
										if(checkPermission(1,$tables['id'],"read",$tables['act'],null)==1){
								?>
									$string_extra+='<option class="data-text" value="<?php print_r($tables['id']); ?>" data-text-en="<?php print_r($tables['description_name_en']); ?>" data-text-fa="<?php print_r($tables['description_name_fa']); ?>">';
										$string_extra+=(language=="en" ? "<?php print_r($tables['description_name_en']); ?>":"<?php print_r($tables['description_name_fa']); ?>");
									$string_extra+='</option>';
								<?php
										}
									}
								?>
								$string_extra+='</select>';
								$string_extra+='</div>';
								$string_extra+='<div class="col-lg-6 col-12 row mb-2 ml-auto mr-auto">';
								$string_extra+='<select disabled class="selectpicker data-title col-lg-4 col-12 mb-2 ml-auto mr-auto connected_name" data-title-en="Columns" data-title-fa="ستون ها" data-style="btn btn-primary" title="'+(language=="en" ? "Columns":"ستون ها")+'" data-size="7" data-live-search="true">';
								$string_extra+='<option selected class="data-text" data-text-en="Please select a table" data-text-fa="لطفاً یک جدول انتخاب کنید" value="-">'+(language=="en" ? "Please select a table":"لطفاً یک جدول انتخاب کنید")+'</option>';
								$string_extra+='</select>';
								$string_extra+='<select disabled class="selectpicker data-title col-lg-4 col-12 mb-2 ml-auto mr-auto connected_value" data-title-en="Columns" data-title-fa="ستون ها" data-style="btn btn-primary" title="'+(language=="en" ? "Columns":"ستون ها")+'" data-size="7" data-live-search="true">';
								$string_extra+='<option selected class="data-text" data-text-en="Please select a table" data-text-fa="لطفاً یک جدول انتخاب کنید" value="-">'+(language=="en" ? "Please select a table":"لطفاً یک جدول انتخاب کنید")+'</option>';
								$string_extra+='</select>';
								$string_extra+='<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto option_name_div hide">';
								$string_extra+='<div class="form-group">';
								$string_extra+='<input class="form-control data-placeholder data-original-title option_name select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Name" data-original-title-fa="نام گزینه" data-original-title="'+(language == "en" ? "Option Name" : "نام گزینه")+'" data-placement="top" data-placeholder-en="Option Name" data-placeholder-fa="نام گزینه" placeholder="'+(language=="en" ? "Option Name":"نام گزینه")+'" type="text">';
								$string_extra+='</div>';
								$string_extra+='</div>';
								$string_extra+='<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto option_value_div hide">';
								$string_extra+='<div class="form-group">';
								$string_extra+='<input class="form-control data-placeholder data-original-title option_value select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Value" data-original-title-fa="مقدار گزینه" data-original-title="'+(language == "en" ? "Option Value" : "مقدار گزینه")+'" data-placement="top" data-placeholder-en="Option Value" data-placeholder-fa="مقدار گزینه" placeholder="'+(language=="en" ? "Option Value":"مقدار گزینه")+'" type="text">';
								$string_extra+='</div>';
								$string_extra+='</div>';
								$string_extra+='<select class="selectpicker data-title col-lg-4 col-12 optgroup_id" data-title-en="Optgroups" data-title-fa="سرگروه ها" data-style="btn btn-primary" title="'+(language=="en" ? "Optgroups":"سرگروه ها")+'" data-size="7" data-live-search="true">';
								$string_extra+='<option value="-" selected class="data-text" data-text-en="None of them" data-text-fa="هیچکدام">';
									$string_extra+=(language=="en" ? "None of them":"هیچکدام");
								$string_extra+='</option>';
								$string_extra+='<option value="*" class="data-text" data-text-en="New Optgroup" data-text-fa="سرگروه جدید">'+(language=="en" ? "New Optgroup":"سرگروه جدید")+'</option>';
								$string_extra+='</select>';
								$string_extra+='<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto optgroup_new_div hide">';
								$string_extra+='<div class="input-group">';
								$string_extra+='<input class="form-control data-placeholder data-original-title new_optgroup_text select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Optgroup Text" data-original-title-fa="متن سرگروه" data-original-title="'+(language == "en" ? "Optgroup Text" : "متن سرگروه")+'" data-placement="top" data-placeholder-en="Optgroup Text" data-placeholder-fa="متن سرگروه" placeholder="'+(language=="en" ? "Optgroup Text":"متن سرگروه")+'" type="text">';
								$string_extra+='<div class="input-group-append cursor-pointer optgroup-list">';
								$string_extra+='<div class="input-group-text">';
								$string_extra+='<i class="fad fa-times"></i>';
								$string_extra+='</div>';
								$string_extra+='</div>';
								$string_extra+='</div>';
								$string_extra+='</div>';
								$string_extra+='<div class="col-12 mb-2 ml-auto mr-auto optgroup_text_div hide">';
								$string_extra+='<div class="form-group">';
								$string_extra+='<input class="form-control data-placeholder data-original-title optgroup_text select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Optgroup Text" data-original-title-fa="متن سرگروه" data-original-title="'+(language == "en" ? "Optgroup Text" : "متن سرگروه")+'" data-placement="top" data-placeholder-en="Optgroup Text" data-placeholder-fa="متن سرگروه" placeholder="'+(language=="en" ? "Optgroup Text":"متن سرگروه")+'" type="text">';
								$string_extra+='</div>';
								$string_extra+='</div>';
								$string_extra+='</div>';
								$string_extra+='<div class="col-12 row mb-2 ml-auto mr-auto">';
								$string_extra+='<i class="fas fa-plus-octagon display-4 mt-2 '+(language=="en" ? "ml-auto mr-1":"mr-auto ml-1")+' text-success cursor-pointer data-original-title add_select_options" rel="tooltip" data-original-title-en="Add Option" data-original-title-fa="افزودن گزینه" data-original-title="'+(language == "en" ? "Add Option" : "افزودن گزینه")+'"></i>';
								$string_extra+='<i class="fas fa-eraser display-4 mt-2 '+(language=="en" ? "ml-1 mr-auto":"mr-1 ml-auto")+' text-warning cursor-pointer data-original-title clear_select_options" rel="tooltip" data-original-title-en="Clear Option" data-original-title-fa="پاکسازی گزینه" data-original-title="'+(language == "en" ? "Clear Option" : "پاکسازی گزینه")+'"></i>';
								$string_extra+='';
								$string_extra+='<i class="fad fa-save display-4 mt-2 '+(language=="en" ? "ml-auto mr-1":"mr-auto ml-1")+' text-success cursor-pointer data-original-title save_select_options hide" rel="tooltip" data-original-title-en="Save Option" data-original-title-fa="ذخیره گزینه" data-original-title="'+(language == "en" ? "Save Option" : "ذخیره گزینه")+'"></i>';
								$string_extra+='<i class="fad fa-clone display-4 mt-2 ml-1 mr-1 text-info cursor-pointer data-original-title copy_select_options hide" rel="tooltip" data-original-title-en="Copy Option" data-original-title-fa="کپی گزینه" data-original-title="'+(language == "en" ? "Copy Option" : "کپی گزینه")+'"></i>';
								$string_extra+='<i class="fad fa-forward display-4 mt-2 ml-1 mr-1 cursor-pointer data-original-title skip_select_options hide" rel="tooltip" data-original-title-en="Skip Editing" data-original-title-fa="رد کردن ویرایش" data-original-title="'+(language == "en" ? "Skip Editing" : "رد کردن ویرایش")+'"></i>';
								$string_extra+='<i class="fas fa-times-octagon display-4 mt-2 '+(language=="en" ? "ml-1 mr-auto":"mr-1 ml-auto")+' text-danger cursor-pointer data-original-title delete_select_options hide" rel="tooltip" data-original-title-en="Delete Option" data-original-title-fa="حذف گزینه" data-original-title="'+(language == "en" ? "Delete Option" : "حذف گزینه")+'"></i>';
								$string_extra+='</div>';
								$string_extra+='<!-- table of select options -->';
								$string_extra+='<div class="col-12">';
								$string_extra+='<table class="table table-striped w-100 selectOpt_table">';
								$string_extra+='<thead>';
								$string_extra+='<tr>';
								$string_extra+='<th data-priority="4">';
								$string_extra+='<label class="data-text" data-text-en="Optgroup" data-text-fa="سرگروه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Optgroup":"سرگروه")+'</label>';
								$string_extra+='</th>';
								$string_extra+='<th data-priority="1">';
								$string_extra+='<label class="data-text" data-text-en="Name" data-text-fa="نام" style="margin-bottom: 0px !important;">'+(language=="en" ? "Name":"نام")+'</label>';
								$string_extra+='</th>';
								$string_extra+='<th data-priority="6">';
								$string_extra+='<label class="data-text" data-text-en="Value" data-text-fa="مقدار" style="margin-bottom: 0px !important;">'+(language=="en" ? "Value":"مقدار")+'</label>';
								$string_extra+='</th>';
								$string_extra+='<th data-priority="3">';
								$string_extra+='<label class="data-text" data-text-en="Type" data-text-fa="حالت" style="margin-bottom: 0px !important;">'+(language=="en" ? "Type":"حالت")+'</label>';
								$string_extra+='</th>';
								$string_extra+='<th data-priority="2">';
								$string_extra+='<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات">'+(language=="en" ? "Operation":"عملیات")+'</label>';
								$string_extra+='</th>';
								$string_extra+='</tr>';
								$string_extra+='</thead>';
								$string_extra+='<tbody>';
								$string_extra+='';
								$string_extra+='</tbody>';
								$string_extra+='<tfoot>';
								$string_extra+='<tr>';
								$string_extra+='<th>';
								$string_extra+='<label class="data-text" data-text-en="Optgroup" data-text-fa="سرگروه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Optgroup":"سرگروه")+'</label>';
								$string_extra+='</th>';
								$string_extra+='<th>';
								$string_extra+='<label class="data-text" data-text-en="Name" data-text-fa="نام" style="margin-bottom: 0px !important;">'+(language=="en" ? "Name":"نام")+'</label>';
								$string_extra+='</th>';
								$string_extra+='<th>';
								$string_extra+='<label class="data-text" data-text-en="Value" data-text-fa="مقدار" style="margin-bottom: 0px !important;">'+(language=="en" ? "Value":"مقدار")+'</label>';
								$string_extra+='</th>';
								$string_extra+='<th>';
								$string_extra+='<label class="data-text" data-text-en="Type" data-text-fa="حالت" style="margin-bottom: 0px !important;">'+(language=="en" ? "Type":"حالت")+'</label>';
								$string_extra+='</th>';
								$string_extra+='<th>';
								$string_extra+='<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات">'+(language=="en" ? "Operation":"عملیات")+'</label>';
								$string_extra+='</th>';
								$string_extra+='</tr>';
								$string_extra+='</tfoot>';
								$string_extra+='</table>';
								$string_extra+='</div>';
							$string_extra+='</div>';
							$.when($element.find(".extra-options").append($string_extra)).done(function(){
								$.when($element.find(".selectpicker").selectpicker({iconBase: "tim-icons",tickIcon: "icon-check-2"})).done(function(){
									$element.find("select.optgroup_id").load("table/class/action.php?optgroups", { "column_id": $savedIDs },function () {
										$element.find("select.optgroup_id").selectpicker("val","-").selectpicker("refresh").change();
									});
									callDataTable_selectOpt_table($element,$savedIDs);
								});
							});
						}else{
							feedbackOperations(data);
						}
					});
				}else{
					$string_extra='<div class="alert alert-info col-12">';
						$string_extra+='<span class="data-text" data-text-en="You have to save this column to appear this part" data-text-fa="برای نمایان شدن این قسمت باید این ستون را ذخیره نمایید">'+(language=="en" ? "You have to save this column to see this part":"برای نمایان شدن این قسمت باید این ستون را ذخیره نمایید")+'</span>';
					$string_extra+='</div>';
					$element.find(".extra-options").append($string_extra);
				}
			break;
			case "7":case 7://info search case 7 for see all things about this part
				if($isSaved){
					$.post("table/class/action.php?changeMode", {
						"id" : $savedIDs,
						"mode" : $this.val(),
					}, function(data, status) {
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							var $data=data.split("_._");
							$datas=($data[0]!="" ? JSON.parse($data[0]):["",""]);
							$string_extra="";
							$string_extra+='<div class="col-md-6 col-12">';
								$string_extra+='<div class="form-group">';
									$string_extra+='<input class="form-control data-placeholder data-original-title input_size_limit input_text on-press-enter-column" rel="tooltip" data-original-title-en="Size Limit MB (0 = unlimited)" data-original-title-fa="محدودیت سایز MB (0 = نامحدود)" data-original-title="'+(language=="en" ? "Size Limit MB (0 = unlimited)" : "محدودیت سایز MB (0 = نامحدود)")+'" data-placement="top" data-placeholder-en="Size Limit MB (0 = unlimited)" data-placeholder-fa="محدودیت سایز MB (0 = نامحدود)" placeholder="'+(language=="en" ? "Size Limit MB (0 = unlimited)":"محدودیت سایز MB (0 = نامحدود)")+'" type="text" data-reset-value="'+$datas[0]+'" value="'+$datas[0]+'">';
								$string_extra+='</div>';
							$string_extra+='</div>';
							$string_extra+='<div class="col-md-6 col-12 text-center-custom">';
								$string_extra+='<input class="form-control tagsinput data-placeholder data-original-title input_file_types_limit input_text on-press-enter-column" rel="tooltip" data-original-title-en="Format (empty = all) , (.jpg,...)" data-original-title-fa="پسوند (خالی = همه) (.jpg,...)" data-original-title="'+(language=="en" ? "Format (empty = all) , (.jpg,...)" : "پسوند (خالی = همه) (.jpg,...)")+'" data-placement="top" data-placeholder-en="Format (empty = all) , (.jpg,...)" data-placeholder-fa="پسوند (خالی = همه) (.jpg,...)" placeholder="'+(language=="en" ? "Format (empty = all) , (.jpg,...)":"پسوند (خالی = همه) (.jpg,...)")+'" type="text" data-reset-value="'+$datas[1]+'" value="'+$datas[1]+'">';
							$string_extra+='</div>';
							$element.find(".extra-options").append($string_extra);
							$element.find(".input_file_types_limit").tagsinput();
						}else{
							feedbackOperations(data);
						}
					});
				}else{
					$datas=["",""];
					$string_extra="";
					$string_extra+='<div class="col-md-6 col-12">';
						$string_extra+='<div class="form-group">';
							$string_extra+='<input class="form-control data-placeholder data-original-title input_size_limit input_text on-press-enter-column" rel="tooltip" data-original-title-en="Size Limit MB (0 = unlimited)" data-original-title-fa="محدودیت سایز MB (0 = نامحدود)" data-original-title="'+(language=="en" ? "Size Limit MB (0 = unlimited)" : "محدودیت سایز MB (0 = نامحدود)")+'" data-placement="top" data-placeholder-en="Size Limit MB (0 = unlimited)" data-placeholder-fa="محدودیت سایز MB (0 = نامحدود)" placeholder="'+(language=="en" ? "Size Limit MB (0 = unlimited)":"محدودیت سایز MB (0 = نامحدود)")+'" type="text" data-reset-value="'+$datas[0]+'" value="'+$datas[0]+'">';
						$string_extra+='</div>';
					$string_extra+='</div>';
					$string_extra+='<div class="col-md-6 col-12 text-center-custom">';
						$string_extra+='<input class="form-control tagsinput data-placeholder data-original-title input_file_types_limit input_text on-press-enter-column" rel="tooltip" data-original-title-en="Format (empty = all) , (.jpg,...)" data-original-title-fa="پسوند (خالی = همه) (.jpg,...)" data-original-title="'+(language=="en" ? "Format (empty = all) , (.jpg,...)" : "پسوند (خالی = همه) (.jpg,...)")+'" data-placement="top" data-placeholder-en="Format (empty = all) , (.jpg,...)" data-placeholder-fa="پسوند (خالی = همه) (.jpg,...)" placeholder="'+(language=="en" ? "Format (empty = all) , (.jpg,...)":"پسوند (خالی = همه) (.jpg,...)")+'" type="text" data-reset-value="'+$datas[1]+'" value="'+$datas[1]+'">';
					$string_extra+='</div>';
					$element.find(".extra-options").append($string_extra);
					$element.find(".input_file_types_limit").tagsinput();
				}
			break;
			case "9":case 9://info search case 9 for see all things about this part
				if($isSaved){
					$string_extra='<div class="col-md-12 row mr-auto ml-auto pb-3">';
						$string_extra+='<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto">';
							$string_extra+='<div class="form-check cursor-pointer">';
								$string_extra+='<label class="form-check-label">';
									$string_extra+='<input class="form-check-input input_is_multiple input_checkbox" type="checkbox" data-reset-value="">';
									$string_extra+='<span class="form-check-sign"></span>';
									$string_extra+='<label class="data-text" data-text-en="Multiple ?" data-text-fa="چندتایی ؟">'+(language=="en" ? "Multiple ?":"چندتایی ؟")+'</label>';
								$string_extra+='</label>';
							$string_extra+='</div>';
						$string_extra+='</div>';
						$string_extra+='<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto hide">';
							$string_extra+='<div class="form-check cursor-pointer">';
								$string_extra+='<label class="form-check-label">';
									$string_extra+='<input class="form-check-input input_is_forced input_checkbox" checked type="checkbox" data-reset-value="">';
									$string_extra+='<span class="form-check-sign"></span>';
									$string_extra+='<label class="data-text" data-text-en="Forced ?" data-text-fa="اجباری ؟">'+(language=="en" ? "Forced ?":"اجباری ؟")+'</label>';
								$string_extra+='</label>';
							$string_extra+='</div>';
						$string_extra+='</div>';
					$string_extra+='</div>';
					$string_extra+='<div class="col-md-12 row mr-auto ml-auto">';
						$string_extra+='<div class="form-group col-4">';
							$string_extra+='<input class="form-control data-placeholder data-original-title checkbox_name select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Name" data-original-title-fa="نام گزینه" data-original-title="'+(language=="en" ? "Option Name" : "نام گزینه")+'" data-placement="top" data-placeholder-en="Option Name" data-placeholder-fa="نام گزینه" placeholder="'+(language=="en" ? "Option Name":"نام گزینه")+'" type="text">';
						$string_extra+='</div>';
						$string_extra+='<div class="form-group col-4">';
							$string_extra+='<input class="form-control data-placeholder data-original-title checkbox_value select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Value" data-original-title-fa="خروجی گزینه" data-original-title="'+(language=="en" ? "Option Value" : "خروجی گزینه")+'" data-placement="top" data-placeholder-en="Option Value" data-placeholder-fa="خروجی گزینه" placeholder="'+(language=="en" ? "Option Value":"خروجی گزینه")+'" type="text">';
						$string_extra+='</div>';
						$string_extra+='<div class="form-group col-4">';
							$string_extra+='<input class="form-control data-placeholder data-original-title checkbox_false select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option False" data-original-title-fa="خروجی منفی گزینه" data-original-title="'+(language=="en" ? "Option False" : "خروجی منفی گزینه")+'" data-placement="top" data-placeholder-en="Option False" data-placeholder-fa="خروجی منفی گزینه" placeholder="'+(language=="en" ? "Option False":"خروجی منفی گزینه")+'" type="text">';
						$string_extra+='</div>';
						$string_extra+='<div class="col-12 row mb-2 ml-auto mr-auto">';
							$string_extra+='<i class="fas fa-plus-octagon display-4 mt-2 '+(language=="en" ? "ml-auto mr-1":"mr-auto ml-1")+' text-success cursor-pointer data-original-title add_checkbox_options" rel="tooltip" data-original-title-en="Add Option" data-original-title-fa="افزودن گزینه" data-original-title="'+(language=="en" ? "Add Option" : "افزودن گزینه")+'"></i>';
							$string_extra+='<i class="fas fa-eraser display-4 mt-2 '+(language=="en" ? "ml-1 mr-auto":"mr-1 ml-auto")+' text-warning cursor-pointer data-original-title clear_checkbox_options" rel="tooltip" data-original-title-en="Clear Option" data-original-title-fa="پاکسازی گزینه" data-original-title="'+(language=="en" ? "Clear Option" : "پاکسازی گزینه")+'"></i>';
							$string_extra+='<i class="fad fa-save display-4 mt-2 '+(language=="en" ? "ml-auto mr-1":"mr-auto ml-1")+' text-success cursor-pointer data-original-title save_checkbox_options hide" rel="tooltip" data-original-title-en="Save Option" data-original-title-fa="ذخیره گزینه" data-original-title="'+(language=="en" ? "Save Option" : "ذخیره گزینه")+'"></i>';
							$string_extra+='<i class="fad fa-clone display-4 mt-2 ml-1 mr-1 text-info cursor-pointer data-original-title copy_checkbox_options hide" rel="tooltip" data-original-title-en="Copy Option" data-original-title-fa="کپی گزینه" data-original-title="'+(language=="en" ? "Copy Option" : "کپی گزینه")+'"></i>';
							$string_extra+='<i class="fad fa-forward display-4 mt-2 ml-1 mr-1 cursor-pointer data-original-title skip_checkbox_options hide" rel="tooltip" data-original-title-en="Skip Editing" data-original-title-fa="رد کردن ویرایش" data-original-title="'+(language=="en" ? "Skip Editing" : "رد کردن ویرایش")+'"></i>';
							$string_extra+='<i class="fas fa-times-octagon display-4 mt-2 '+(language=="en" ? "ml-1 mr-auto":"mr-1 ml-auto")+' text-danger cursor-pointer data-original-title delete_checkbox_options hide" rel="tooltip" data-original-title-en="Delete Option" data-original-title-fa="حذف گزینه" data-original-title="'+(language=="en" ? "Delete Option" : "حذف گزینه")+'"></i>';
						$string_extra+='</div>';
						$string_extra+='<!-- table of select options -->';
						$string_extra+='<div class="col-12">';
							$string_extra+='<table class="table table-striped w-100 checkboxOpt_table">';
								$string_extra+='<thead>';
									$string_extra+='<tr>';
										$string_extra+='<th data-priority="4">';
											$string_extra+='<label class="data-text" data-text-en="Option Name" data-text-fa="نام گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option Name":"نام گزینه")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th data-priority="1">';
											$string_extra+='<label class="data-text" data-text-en="Option Value" data-text-fa="خروجی گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option Value":"خروجی گزینه")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th data-priority="6">';
											$string_extra+='<label class="data-text" data-text-en="Option false value" data-text-fa="خروجی منفی گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option false value":"خروجی منفی گزینه")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th data-priority="2">';
											$string_extra+='<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات">'+(language=="en" ? "Operation":"عملیات")+'</label>';
										$string_extra+='</th>';
									$string_extra+='</tr>';
								$string_extra+='</thead>';
								$string_extra+='<tbody>';
									$string_extra+='<!---->';
								$string_extra+='</tbody>';
								$string_extra+='<tfoot>';
									$string_extra+='<tr>';
										$string_extra+='<th>';
											$string_extra+='<label class="data-text" data-text-en="Option Name" data-text-fa="نام گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option Name":"نام گزینه")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th>';
											$string_extra+='<label class="data-text" data-text-en="Option Value" data-text-fa="خروجی گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option Value":"خروجی گزینه")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th>';
											$string_extra+='<label class="data-text" data-text-en="Option false value" data-text-fa="خروجی منفی گزینه" style="margin-bottom: 0px !important;">'+(language=="en" ? "Option false value":"خروجی منفی گزینه")+'</label>';
										$string_extra+='</th>';
										$string_extra+='<th>';
											$string_extra+='<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات">'+(language=="en" ? "Operation":"عملیات")+'</label>';
										$string_extra+='</th>';
									$string_extra+='</tr>';
								$string_extra+='</tfoot>';
							$string_extra+='</table>';
						$string_extra+='</div>';
					$string_extra+='</div>';
					$.when($element.find(".extra-options").empty().append($string_extra)).done(function(){
						callDataTable_checkboxOpt_table($element,$data[1]);
					});
				}else{
					$string_extra='<div class="alert alert-info col-12">';
						$string_extra+='<span class="data-text" data-text-en="You have to save this column to appear this part" data-text-fa="برای نمایان شدن این قسمت باید این ستون را ذخیره نمایید">'+(language=="en" ? "You have to save this column to see this part":"برای نمایان شدن این قسمت باید این ستون را ذخیره نمایید")+'</span>';
					$string_extra+='</div>';
					$element.find(".extra-options").append($string_extra);
				}
			break;
		}
	});

	$(document).on("error.dt","#tables_columns, .selectOpt_table",function(e, settings, techNote, message){
		// console.log( 'An error has been reported by DataTables: ', message );
		// window.location.reload();
	});

	function callDataTable_selectOpt_table($element,$column_id){
		$element.find("table.selectOpt_table").dataTable().fnDestroy();
		var $dataTableInstaller = $element.find("table.selectOpt_table").DataTable({
			"drawCallback": function( settings ) {
				pscrollbarUpdate();
			},
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: 'table/datatable_json/selectopt_table_json.php',
				type: 'POST',
				"data": function ( d ) {
					d.primaryKey = "id";
					d.column_id = $column_id;
				}
			},
			"columns": [
				{"searchable": false},
				{},
				{},
				{"searchable": false},
				{
					"orderable": false,
					"class": "actions_dir",
					"orderable": false,
					"searchable": false
				}
			],
			"pagingType": "full_numbers",
			"lengthMenu": [
				[10, 25, 50, 100, 250 , 500, -1],
				[10, 25, 50, 100, 250 , 500, "All"]
			],
			responsive: true,
			"order": [[ 0, "asc" ]],
			"language": langObjs(),
			"columnDefs": [
				{"class":"text-right","targets":4}
			]
		});
	}

	function callDataTable_checkboxOpt_table($element,$column_id){
		$element.find("table.checkboxOpt_table").dataTable().fnDestroy();
		var $dataTableInstaller = $element.find("table.checkboxOpt_table").DataTable({
			"drawCallback": function( settings ) {
				pscrollbarUpdate();
			},
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: 'table/datatable_json/checkboxopt_table_json.php',
				type: 'POST',
				"data": function ( d ) {
					d.primaryKey = "id";
					d.column_id = $column_id;
				}
			},
			"columns": [
				{},
				{},
				{},
				{
					"orderable": false,
					"class": "actions_dir",
					"orderable": false,
					"searchable": false
				}
			],
			"pagingType": "full_numbers",
			"lengthMenu": [
				[10, 25, 50, 100, 250 , 500, -1],
				[10, 25, 50, 100, 250 , 500, "All"]
			],
			responsive: true,
			"order": [[ 0, "asc" ]],
			"language": langObjs(),
			"columnDefs": [
				{"class":"text-right","targets":3}
			]
		});
	}

	function callDataTable_tables_columns(){
		$('#tables_columns').dataTable().fnDestroy();
		var $dataTableInstaller = $('#tables_columns').DataTable({
			"drawCallback": function( settings ) {
				pscrollbarUpdate();
			},
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: 'table/datatable_json/table_columns_editor_json.php',
				type: 'POST',
				"data": function ( d ) {
					d.primaryKey = "id";
				}
			},
			"columns": [
				{},
				{"orderable": false},
				{"orderable": false},
				{
					"orderable": false,
					"class": "actions_dir",
					"orderable": false,
					"searchable": false
				}
			],
			"pagingType": "full_numbers",
			"lengthMenu": [
				[10, 25, 50, 100, 250 , 500, -1],
				[10, 25, 50, 100, 250 , 500, "All"]
			],
			responsive: true,
			rowReorder: {
				update: false,
				snapX: -5,
				selector: '.drag_move_table',
			},
			"order": [[ 0, "asc" ]],
			"language": langObjs(),
			"columnDefs": [
				{"targets": 0,"visible": false},
				{"class":"text-right","targets":2}
			]
		});
		$dataTableInstaller.on( 'row-reorder', function ( e, diff, edit ) {
			var $changer="";
			for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
				if(i==ien-1){
					$explode_helper="";
				}else{
					$explode_helper="_.._";
				}
				$changer+=diff[i].oldData+"_._"+diff[i].newData+$explode_helper;
			}
			if($changer!=""){
				$.post("table/class/action.php?column_numbers", {
					"numbers" : $changer
				}, function(data, status) {
					if (status == "success" && data.toString().indexOf("success")!=-1) {
						updateTable();
					}else{
						feedbackOperations(data);
					}
				});
			}
		});
	}

	function repairTables() {
		$("table.selectOpt_table").each(function () {
			$element=$(this).parent().parent().parent().parent().parent().parent().parent();
			if($element.hasClass("row") && $element.hasClass("justify-content-center")){
				var $savedIDs="";
				$element.attr("class").split(" ").forEach(function(class_name){
					$class_name=class_name.split("-");
					if($class_name[0]=="column_saved_id"){
						$savedIDs=$class_name[1];
					}
				});
				callDataTable_selectOpt_table($element,$savedIDs);
			}
		});
		callDataTable_tables_columns();
	}

	function buttonsOperations($operation,$button) {
		//need buttons
		var $button_text=$($button).html();
		$($button).attr("disabled",true).html((language=="en" ? "Doing":"در حال انجام")+" <label class='fad fa-spinner-third fa-spin'></label>");
		switch ($operation) {
			case "save":
			case "save_close":
				var $saveInterval, $nav=$(".wizard-navigation ul.custom_transition.nav.nav-pills");
				$("#stack-of-columns").children().each(function(){
					$disable_alert=1;
					$(this).find(".save_close-button-operation").click();
				});
				$saveInterval=setInterval(() => {
					if($("#stack-of-columns").children().length==0){
						clearInterval($saveInterval);
						$disable_alert=0;
						$.post("table/class/action.php?gotToStep3", {}, function(data, status) {
							if (status == "success" && data.toString().indexOf("success")!=-1) {
								$.post("table/class/action.php?create", {}, function(data, status) {
									if (status == "success" && data.toString().indexOf("success")!=-1) {
										var $nav=$(".wizard-navigation ul.custom_transition.nav.nav-pills");
										$($nav.find('li a i')).attr("class", "far fa-check").parent("a").addClass("checked");
										$(".card-wizard").find($('.progress-bar')).css({width: '100%'});
										$($button).attr("disabled",false).html($button_text);
										if($operation=="save_close"){
											buttonsOperations('skip',"");
										}
									}else{
										feedbackOperations(data);
										$($button).attr("disabled",false).html($button_text);
									}
								});
							}else{
								feedbackOperations(data);
								$($button).attr("disabled",false).html($button_text);
							}
						});
					}
				}, 500);
				setTimeout(() => {
					if(typeof $saveInterval !== "undefined"){
						clearInterval($saveInterval);
						$($button).attr("disabled",false).html($button_text);
					}
				}, 10000);
			break;
			case "skip":
				try {
					function doTheOperation() {
						$.post("table/class/action.php?skip", {}, function(data, status) {
							if (status == "success" && data.toString().indexOf("success")!=-1) {
								var navigation=$(".wizard-navigation ul.custom_transition.nav.nav-pills");
								var $total = navigation.find('li').length;
								$width = 100 / $total;
								navigation.find('li').css('width', $width + '%');
								$(navigation.find('li a:not(:first-child)')).removeClass("checked");
								delete page_loaded['tables?create'];
								$("#tables_-_QQ_-_create").remove();
								window.location.href="#tables";
								$($button).attr("disabled",false).html($button_text);
							}else{
								feedbackOperations(data);
								$($button).attr("disabled",false).html($button_text);
							}
						});
					}
					if(alertShowerSetting['skipDatabaseTable_showAlert']=="true" || $disable_alert==1){
						doTheOperation();
					}else{
						if(language=="fa"){
							Swal.fire({
								title: 'آیا مطمئن  هستید؟',
								text: "آیا میخواهید از این جدول خارج شوید ؟",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'بله',
								cancelButtonText: 'لغو',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'skipDatabaseTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
							}).then((result) => {
								if(result.value) {
									doTheOperation();
								}else{
									$($button).attr("disabled",false).html($button_text);
								}
							});
						}else{
							Swal.fire({
								title: 'Are you sure?',
								text: "Are you really want to close this table ?",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'Yes',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'skipDatabaseTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
							}).then((result) => {
								if(result.value) {
									doTheOperation();
								}else{
									$($button).attr("disabled",false).html($button_text);
								}
							});
						}
					}
				} catch(err) {
					alertShowerSetting['skipDatabaseTable_showAlert']="false";
					buttonsOperations($operation,$button);
				}
			break;
			case "delete":
				try {
					function doTheOperation() {
						$.post("table/class/action.php?delete_table", {}, function(data, status) {
							if (status == "success" && data.toString().indexOf("success")!=-1) {
								var navigation=$(".wizard-navigation ul.custom_transition.nav.nav-pills");
								var $total = navigation.find('li').length;
								$width = 100 / $total;
								navigation.find('li').css('width', $width + '%');
								$(navigation.find('li a:not(:first-child)')).removeClass("checked");
								delete page_loaded['tables?create'];
								$("#tables_-_QQ_-_create").remove();
								window.location.href="#tables";
								$($button).attr("disabled",false).html($button_text);
							}else{
								feedbackOperations(data);
								$($button).attr("disabled",false).html($button_text);
							}
						});
					}
					if(alertShowerSetting['deleteDatabaseTable_showAlert']=="true" || $disable_alert==1){
						doTheOperation();
					}else{
						if(language=="fa"){
							Swal.fire({
								title: 'آیا مطمئن  هستید؟',
								text: "آیا میخواهید این جدول را حذف کنید ؟",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'بله',
								cancelButtonText: 'لغو',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteDatabaseTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
							}).then((result) => {
								if(result.value) {
									doTheOperation();
								}else{
									$($button).attr("disabled",false).html($button_text);
								}
							});
						}else{
							Swal.fire({
								title: 'Are you sure?',
								text: "Are you really want to delete this table ?",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'Yes',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteDatabaseTable_showAlert'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
							}).then((result) => {
								if(result.value) {
									doTheOperation();
								}else{
									$($button).attr("disabled",false).html($button_text);
								}
							});
						}
					}
				} catch(err) {
					alertShowerSetting['deleteDatabaseTable_showAlert']="false";
					buttonsOperations($operation,$button);
				}
			break;
			default:
				$($button).attr("disabled",false).html($button_text);
			break;
		}
	}
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
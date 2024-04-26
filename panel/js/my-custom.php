<?php
	if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_SESSION['username']."' AND act=1");
			$user_stats=$res_user->rowCount();
			if($user_stats==1 || getSetting("op_admin")==$_SESSION['username']){
?>
	<script>
		let myDataTable,
		operationIn=0,
		operationIns=[],
		operationQueues=[],
		$pscrollbars=[],
		operationPrimary=['reLoaderTable','reloadDataTable','reloadDataTable2','resizeTable','pageLoader','pageLoader2'];
		operationIns["true"]=0;
		operationIns["false"]=0,
		$fa_datetimepicker=[],
		currentScrollPosition=0;
		function setOperationIn(val,info){
			operationIn = val;

			if(val==1){
				operationIns["true"]++;
			}else{
				operationIns["false"]++;
			}

			pscrollbarUpdate();

			if(typeof info !== "undefined"){
				if(val==1){
					// console.log(info + " started.");
				}else{
					// console.log(info + " has been finished.");
				}
			}
			if(operationQueues.length>0){
				RnOp=operationQueues.shift();
				ToDo=operationQueues[RnOp].shift();
				doOperations(ToDo[0],ToDo[1],ToDo[2]);
			}
		}
		function doOperations(operations,datas,skip_val){
			if(operationIn==0){
				// console.log("---------------- Operaton Restarting ---------------- " + operations,datas,skip_val);
				setTimeout(function() {
					switch(operations){
						case "pageLoader":
							pageLoader(datas[0]);
						break;
						case "pageLoader2":
							pageLoader2(datas[0]);
						break;
						case "reLoaderTable":
							reLoaderTable();
						break;
						case "reloadDataTable":
							reloadDataTable();
						break;
						case "reloadDataTable2":
							reloadDataTable2();
						break;
						case "resizeTable":
							resizeTable(datas[0]);
						break;
						case "createTable_wizzard":
							createTable_wizzard();
						break;
						case "updateLevel":
							updateLevel(datas[0],datas[1],datas[2]);
						break;
						case "saveInputs":
							saveInputs(datas[0],datas[1]);
						break;
						case "saveThis":
							saveThis(datas[0],datas[1]);
						break;
						case "backToTable":
							backToTable(datas[0]);
						break;
					}
				}, 500);
			}else{
				if(operationQueues.toString().indexOf(operations)==-1 || operationPrimary.toString().indexOf(operations)==-1){
					newQueueNumber=operationQueues.length;
					operationQueues[newQueueNumber]=operations;
				}
				if(operationQueues[operations]==undefined){
					operationQueues[operations]=[];
					operationQueues[operations].push([operations,datas,skip_val]);
				}else{
					if(operationPrimary.toString().indexOf(operations)==-1){
						operationQueues[operations].push([operations,datas,skip_val]);
					}else{
						delete operationQueues[operations];
						operationQueues[operations]=[];
						operationQueues[operations].push([operations,datas,skip_val]);
					}
				}
			}
		}
		function pscrollbarUpdate() {
			if ($pscrollbars.length != 0) {
				for ($i = 0; $i < $pscrollbars.length; $i++) {
					if (typeof $pscrollbars[$i] !== "undefined") {
						$pscrollbars[$i].update();
					}
				}
			}
		}
		function defined($item){
			return (typeof $item !== "undefined" && typeof $item !== "null");
		}

		function toggleTheme(theme) {
			theme_def = theme;
			newUserSetting("theme-default",theme);
			if(theme=="white"){
				$("body").addClass("white-content");
				$("nav.navbar").addClass("bg-white");
				$(".night_mode_btn").children("i").removeClass("fa-sun").addClass("fa-moon");
				$(".night_mode_btn").children("p").attr("data-text-fa","تاریک").attr("data-text-en","Dark").html(language=="en" ? "Dark":"تاریک");
				technoshaScriptLoader("assets/css/persian-datepicker.css", "css", "head");
				technoshaScriptUnLoader("assets/css/theme/persian-datepicker-dark.min.css", "css", "head");
			}else if(theme=="black"){
				$("body").removeClass("white-content");
				$("nav.navbar").removeClass("bg-white");
				$(".night_mode_btn").children("i").removeClass("fa-moon").addClass("fa-sun");
				$(".night_mode_btn").children("p").attr("data-text-fa","نورانی").attr("data-text-en","Light").html(language=="en" ? "Light":"نورانی");
				technoshaScriptLoader("assets/css/theme/persian-datepicker-dark.min.css", "css", "head");
				technoshaScriptUnLoader("assets/css/persian-datepicker.css", "css", "head");
			}
		}

		$(document).on("hidden.bs.modal", ".modal", function() {
			if ($(this).hasClass("closing")) {
				if($(this).hasClass("parent_hidden")){
					$(this).removeClass("parent_hidden");
					$(this).parent().hide();
				}
				$(this).removeClass("closing");
				$("#navbarLoader")[0].style.setProperty('z-index', '1042', 'important');
				$("html").addClass("perfect-scrollbar-on");
				main_panel_scroll.animate({
					scrollTop: currentScrollPosition
				}, 0);
				pscrollbarUpdate();
			} else {
				$(this).addClass("closing").modal("show");
			}
		});

		$(document).on("shown.bs.modal", ".modal", function() {
			if ($(this).hasClass("closing")) {
				$(this).css("opacity", 0);
				setTimeout(function() {
					$(".modal.show").modal("hide");
				}, 500);
			} else {
				if($(this).parent().css("display")=="none"){
					$(this).addClass("parent_hidden");
					$(this).parent().show();
				}
				$(this).css("opacity", 1);
				$("#navbarLoader")[0].style.setProperty('z-index', '1040', 'important');
				currentScrollPosition = main_panel_scroll.scrollTop();
				$("html").removeClass("perfect-scrollbar-on");
			}
		});

		$('.dataTable').on('page.dt', function() {
			pscrollbarUpdate();
		});

		$(document).ready(function() {
			if (typeof main_panel_scroll === "undefined") {
				if (navigator.appVersion.indexOf("Windows") != -1 || navigator.appVersion.indexOf("windows") != -1) {
					scroll_buttons_help = 10;
					main_panel_scroll = $(".main-panel");
				} else {
					scroll_buttons_help = 24;
					main_panel_scroll = $("html");
				}
			}
		});

		var filesadded = "", // files will be load on this var
			page_loaded = [], // name of pages will be stack in there
			alertShowerSetting = [], // Default data don't change
			language = '<?php print_r($GLOBALS['user_language']); ?>', // Default language (its should be en and don't change it !)
			default_editor_fontsize = '<?php print_r(getUserSetting('default-editor-fontsize')); ?>', // default-editor-fontsize (its should be 12 and don't change it !)
			theme_def = '<?php print_r(getUserSetting('theme-default')); ?>', // this is the default theme and don't change it
			data_color_def = '<?php print_r(getUserSetting('data-color-default')); ?>', // this is the default theme and dont change it
			sidebar_minimize_def = '<?php print_r(getUserSetting('sidebar-minimize')); ?>', // this is the default mode for side bar and dont change it

			<?php if (isset($op_admin) && $op_admin) { ?>developer_mode_def = '<?php print_r(getUserSetting('developer-mode')); ?>', // this is the default mode for side bar and dont change it<?php } ?>

			table_order_mode_def = '<?php print_r(getUserSetting('table-order-mode')); ?>', // this is the default mode for side bar and dont change it
			fixed_menu = '<?php print_r(getUserSetting('fixed-menu')); ?>', // this is the default mode for side bar and dont change it
			current_page = "<?php print_r(str_replace("&amp;amp;","&",getUserSetting('current-page'))); ?>", // this is the first page for load
			current_element = "#" + current_page.replace(".", "_-_"), // this is the first element for load
			number_of_loaded_page = 0, // this is the number of loaded pages
			name_of_loaded_page = [], // this is the name of loaded pages
			maximum_number_of_allowed_page = '<?php print_r(getUserSetting('maximum-page')); ?>', // this is default maximum_number_of_allowed_page

			scroll_top = '<?php print_r(getUserSetting('scroll-top')); ?>', // this is the default mode for side bar and dont change it
			move_top = '<?php print_r(getUserSetting('move-top')); ?>', // this is the default mode for side bar and dont change it
			move_bottom = '<?php print_r(getUserSetting('move-bottom')); ?>', // this is the default mode for side bar and dont change it
			scroll_bottom = '<?php print_r(getUserSetting('scroll-bottom')); ?>', // this is the default mode for side bar and dont change it

			LoadingScreenVar; // loading screen controler

			alertShowerSetting['backToTable_showAlert'] = '<?php print_r(getUserSetting('backToTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['clearInputs_showAlert'] = '<?php print_r(getUserSetting('clearInputs_showAlert')); ?>', // Default data don't change
			alertShowerSetting['resetInputs_showAlert'] = '<?php print_r(getUserSetting('resetInputs_showAlert')); ?>', // Default data don't change
			alertShowerSetting['deleteThis_showAlert'] = '<?php print_r(getUserSetting('deleteThis_showAlert')); ?>'; // Default data don't change
			alertShowerSetting['deleteAllThis_showAlert'] = '<?php print_r(getUserSetting('deleteAllThis_showAlert')); ?>'; // Default data don't change
			alertShowerSetting['reorderTable_showAlert'] = '<?php print_r(getUserSetting('reorderTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['clearCreateTable_showAlert'] = '<?php print_r(getUserSetting('clearCreateTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['skipNewCreateTable_showAlert'] = '<?php print_r(getUserSetting('skipNewCreateTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['skipNewAllCreateTable_showAlert'] = '<?php print_r(getUserSetting('skipNewAllCreateTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['deleteCreateTable_showAlert'] = '<?php print_r(getUserSetting('deleteCreateTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['deleteAllCreateTable_showAlert'] = '<?php print_r(getUserSetting('deleteAllCreateTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['resetCreateTable_showAlert'] = '<?php print_r(getUserSetting('resetCreateTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['resetAllCreateTable_showAlert'] = '<?php print_r(getUserSetting('resetAllCreateTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['saveAndCloseCreateTable_showAlert'] = '<?php print_r(getUserSetting('saveAndCloseCreateTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['saveAndCloseAllCreateTable_showAlert'] = '<?php print_r(getUserSetting('saveAndCloseAllCreateTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['saveAllCreateTable_showAlert'] = '<?php print_r(getUserSetting('saveAllCreateTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['clearAllCreateTable_showAlert'] = '<?php print_r(getUserSetting('clearAllCreateTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['deleteDatabaseTable_showAlert'] = '<?php print_r(getUserSetting('deleteDatabaseTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['skipDatabaseTable_showAlert'] = '<?php print_r(getUserSetting('skipDatabaseTable_showAlert')); ?>', // Default data don't change
			alertShowerSetting['copySelectOpt_showAlert'] = '<?php print_r(getUserSetting('copySelectOpt_showAlert')); ?>', // Default data don't change
			alertShowerSetting['deleteSelectOpt_showAlert'] = '<?php print_r(getUserSetting('deleteSelectOpt_showAlert')); ?>', // Default data don't change
			alertShowerSetting['copyCheckboxOpt_showAlert'] = '<?php print_r(getUserSetting('copyCheckboxOpt_showAlert')); ?>', // Default data don't change
			alertShowerSetting['deleteCheckboxOpt_showAlert'] = '<?php print_r(getUserSetting('deleteCheckboxOpt_showAlert')); ?>', // Default data don't change
			alertShowerSetting['deleteFileManager_showAlert'] = '<?php print_r(getUserSetting('deleteFileManager_showAlert')); ?>', // Default data don't change
			reloadTableInterval = "",
			dataTableReloader = 0;

		firstLoad = 0; // Don't Toch It !
		firstPageLoaded = 1; // Don't Toch It !

		if(fixed_menu=="true"){
			var scroll_help=68;
		}else{
			var scroll_help=0;
		}

		$(window).on("hashchange", function() {

			if (operationIn == 0) {
				if (location.hash != "" && location.hash.length > 0 && location.hash != undefined && location.hash != null) {
					pageLoader2(location.hash);
				}
			} else {
				doOperations('pageLoader2', [location.hash]);
			}

		});

		$(document).ready(function() {
			if (typeof main_panel_scroll === "undefined") {
				if (navigator.appVersion.indexOf("Windows") != -1 || navigator.appVersion.indexOf("windows") != -1) {
					scroll_buttons_help = 10;
					main_panel_scroll = $(".main-panel");
				} else {
					scroll_buttons_help = 24;
					main_panel_scroll = $("html");
				}
			}
			if (language != "undefined") {
				if (language == "fa") {
					changeLanguage('fa', "no_warn", "no_update");
					changeLanguage('en', "no_warn", "no_update");
					changeLanguage('fa', "no_warn");
					$(".navbar-nav").removeClass("ml-auto").addClass("mr-auto");
				} else {
					changeLanguage('en', "no_warn", "no_update");
					changeLanguage('fa', "no_warn", "no_update");
					changeLanguage('en', "no_warn");
					$(".navbar-nav").removeClass("mr-auto").addClass("ml-auto");
				}
				if (firstPageLoaded == 1 && typeof firstLoad !== 'undefined' && typeof firstLoad !== 'null') {
					firstLoad++;
				}
			}

			if (default_editor_fontsize != "undefined") {
				$("#script-appender").prepend("<style>.custom-font-size *:not(small):not(.small){font-size: " + default_editor_fontsize + "px" + "}</style>");
				if (firstPageLoaded == 1 && typeof firstLoad !== 'undefined' && typeof firstLoad !== 'null') {
					firstLoad++;
				}
			}

			<?php if (isset($op_admin) && $op_admin) { ?>

				if (developer_mode_def != "undefined") {
					developer_mode(developer_mode_def, "");
					if (firstPageLoaded == 1 && typeof firstLoad !== 'undefined' && typeof firstLoad !== 'null') {
						firstLoad++;
					}
				}

			<?php
			} else {
			?>
				firstLoad++;
			<?php
			}
			?>

			if (data_color_def != "undefined") {
				$("[data='red']").attr("data", data_color_def);
				$("[data='primary']").attr("data", data_color_def);
				$("[data='green']").attr("data", data_color_def);
				$("[data='blue']").attr("data", data_color_def);
				$("[data='orange']").attr("data", data_color_def);
				if (firstPageLoaded == 1 && typeof firstLoad !== 'undefined' && typeof firstLoad !== 'null') {
					firstLoad++;
				}
			}

			if (theme_def != "undefined") {
				if (theme_def == "white") {
					$("body").addClass("white-content");
					$("nav.navbar").addClass("bg-white");
					technoshaScriptLoader("assets/css/persian-datepicker.css", "css", "head");
				} else {
					$("body").removeClass("white-content");
					$("nav.navbar").removeClass("bg-white");
					technoshaScriptLoader("assets/css/theme/persian-datepicker-dark.min.css", "css", "head");
				}
				if (firstPageLoaded == 1 && typeof firstLoad !== 'undefined' && typeof firstLoad !== 'null') {
					firstLoad++;
				}
			}

			if (sidebar_minimize_def != "undefined") {
				switch (sidebar_minimize_def) {
					case "true":
						$('body').addClass('sidebar-mini');
						sidebar_mini_active = "true";
					break;
					case "false":
						$('body').removeClass('sidebar-mini');
						sidebar_mini_active = "false";
					break;
				}
				if (firstPageLoaded == 1 && typeof firstLoad !== 'undefined' && typeof firstLoad !== 'null') {
					firstLoad++;
				}
			}

			if (table_order_mode_def != "undefined") {
				order_id_mode(table_order_mode_def);
				if (firstPageLoaded == 1 && typeof firstLoad !== 'undefined' && typeof firstLoad !== 'null') {
					firstLoad++;
				}
			}

			$("#navbarLoader").load("navbar.php", function(response, status, xhr) {
				if (status == "success") {
					if (firstPageLoaded == 1 && typeof firstLoad !== 'undefined' && typeof firstLoad !== 'null') {
						firstLoad++;
					}
				}
			});
			if (location.hash == "") {
				pageLoader(current_page);
			} else {
				pageLoader(location.hash);
			}
			$('.preloader').fadeOut('1000', function() {
				<?php
					if($_SESSION["username"]!="amirntms"){
				?>
					if (language == 'en') {
						Swal.fire({
							title: 'Loading',
							html: '',
							timer: 600000,
							showConfirmButton: false,
							willOpen: () => {
								Swal.showLoading();
								LoadingTimer = setInterval(function() {
									if (firstLoad >= 9 && typeof firstLoad !== 'undefined' && typeof firstLoad !== 'null') {
										delete firstLoad;
										firstPageLoaded = 0;
										clearInterval(LoadingTimer);
										swal.close();
									}
								}, 1000);
							},
							willClose: () => {
								Swal.fire({
									position: 'center',
									icon: 'success',
									title: '',
									showConfirmButton: false,
									timer: 1500,
									allowOutsideClick: false
								});
							},
							allowOutsideClick: false
						});
					} else if (language == 'fa') {
						Swal.fire({
							title: 'بارگذاری',
							html: '',
							timer: 600000,
							showConfirmButton: false,
							willOpen: () => {
								Swal.showLoading();
								LoadingTimer = setInterval(function() {
									if (firstLoad >= 9 && typeof firstLoad !== 'undefined' && typeof firstLoad !== 'null') {
										delete firstLoad;
										firstPageLoaded = 0;
										clearInterval(LoadingTimer);
										swal.close();
									}
								}, 1000);
							},
							willClose: () => {
								Swal.fire({
									position: 'center',
									icon: 'success',
									title: '',
									showConfirmButton: false,
									timer: 1500,
									allowOutsideClick: false
								});
							},
							allowOutsideClick: false
						});
					}
				<?php
					}
				?>
			});

			$(window).resize(function() {
				pscrollbarUpdate();
			});

			if (main_panel_scroll.scrollTop() <= 0) {
				$(".scroll-btn-top, .scroll-btn-up").css("opacity", "0");
			}
			$(main_panel_scroll).on('touchmove', onScroll);
			$(document.body).on('touchmove', onScroll);
			$(document.html).on('touchmove', onScroll);
			$(window).on('scroll', onScroll);
			$(main_panel_scroll).on('scroll', onScroll);

			function onScroll() {
				if (main_panel_scroll.scrollTop() > 0) {
					if (fixed_menu == "true") {
						$(".custom_navbar").addClass("fixed_nav");
						$("body").addClass("fixed-nav");
					}
					$(".scroll-btn-top, .scroll-btn-up").css("opacity", "1");
				} else {
					if (fixed_menu == "true") {
						$(".custom_navbar").removeClass("fixed_nav");
						newUserSetting("fixed-menu", "false");
					}
					$(".scroll-btn-top, .scroll-btn-up").css("opacity", "0")
				}
				if (main_panel_scroll.scrollTop() + scroll_buttons_help < (main_panel_scroll.prop("scrollHeight") - $("body").height())) {
					$(".scroll-btn-down, .scroll-btn-bottom").css("opacity", "1");
				} else {
					$(".scroll-btn-down, .scroll-btn-bottom").css("opacity", "0");
				}
			}
		});

		$(document).on('click', '.minimize-sidebar', function() {
			newUserSetting("sidebar-minimize", sidebar_mini_active);
			sidebar_minimize_def = sidebar_mini_active;
			resizeTable();
			switch (sidebar_mini_active) {
				case "true":
					$('body').addClass('sidebar-mini');
				break;
				case "false":
					$('body').removeClass('sidebar-mini');
				break;
			}
		});

		$(document).on('keydown keyup', '.convert-to-lowercase', function() {
			$(this).val($(this).val().toLowerCase());
		});

		$(document).on('keydown keyup', '.just-english', function(e) {
			let value = e.target.value;
			value = value.replace(/[^A-Za-z]/ig, '');
			$(this).val(value)
		});

		$(document).on('mousedown', "[aria-describedby]", function() {
			$("[aria-describedby]").tooltip('hide');
		});

		function langObjs() {
			if (language == "en") {
				return ({
					"decimal": ".",
					"emptyTable": "No data available in table",
					"info": "Showing _START_ to _END_ of _TOTAL_ entries",
					"infoEmpty": "Showing 0 to 0 of 0 entries",
					"infoFiltered": "(filtered from _MAX_ total entries)",
					"infoPostFix": "",
					"thousands": ",",
					"lengthMenu": "Show _MENU_ entries",
					"loadingRecords": "Loading...",
					"processing": "Processing...",
					"search": "Search:",
					"zeroRecords": "No matching records found",
					"paginate": {
						"first": "First",
						"last": "Last",
						"next": "Next",
						"previous": "Previous"
					},
					"searchPlaceholder": "Type here",
					"aria": {
						"sortAscending": ": activate to sort column ascending",
						"sortDescending": ": activate to sort column descending"
					}
				});
			} else if (language == "fa") {
				return ({
					"decimal": ".",
					"emptyTable": "هیچ ورودی وجود ندارد",
					"info": "نمایش _START_ تا _END_ از _TOTAL_ ورودی",
					"infoEmpty": "نمایش 0 تا 0 از 0 ورودی",
					"infoFiltered": "(جست و جو در _MAX_ ورودی)",
					"infoPostFix": "",
					"thousands": ",",
					"lengthMenu": "نمایش _MENU_ ورودی",
					"loadingRecords": "بارگذاری...",
					"processing": "در حال پردازش...",
					"search": "جست و جو:",
					"zeroRecords": "هیچ ورودی پیدا نشد",
					"searchPlaceholder": "اینجا بنویسید",
					"paginate": {
						"first": "نخست",
						"last": "آخرین",
						"next": "بعدی",
						"previous": "قبلی"
					},
					"aria": {
						"sortAscending": ": مرتب سازی به صورت صعودی",
						"sortDescending": ": مرتب سازی به صورت نزولی"
					}
				});
			}
		}

		function dontShowAlert(setting_name, setting_value) {
			alertShowerSetting[setting_name] = (setting_value ? "true":"false");
			newUserSetting(setting_name, setting_value);
		}

		function pageLoader2(pageHash) {
			// if (operationIn == 0) {
				$.when($("[aria-describedby]").tooltip('hide')).done(function() {
					$.when($(".modal").modal("hide")).done(function() {
						$.when(setOperationIn(1, "pageLoader2", pageHash)).done(function() {
							$.when(actived_page = $(".actived").attr("id")).done(function() {
								if (actived_page != undefined && actived_page != null && actived_page != "" && actived_page != "undefined" && actived_page != "null") {
									$.when(actived_page = actived_page.toString().replace("#", "").replace(/_-_QQ_-_/gi, "?").replace(/_-_EE_-_/gi, "=").replace(/_-_AA_-_/gi, "&").replace(/_-_--_-_/gi, ".")).done(function() {
										if (page_loaded[actived_page] != undefined && page_loaded[actived_page] != null && page_loaded[actived_page] != "") {
											$.when(actived_page_url = actived_page.split("?")).done(function() {
												$.when($.post("loader.php?" + actived_page_url[1], {
													page: actived_page_url[0]
												}, function(data, status) {
													page_loaded[actived_page] = data;
												})).done(function() {

												});
											});
										}
									});
								}
							});
							$.when($("*").scrollTop(0)).done(function() {
								$.when(page = pageHash.replace("#", "").replace(/_-_QQ_-_/gi, "?").replace(/_-_EE_-_/gi, "=").replace(/_-_AA_-_/gi, "&").replace(/_-_--_-_/gi, ".")).done(function() {
									$.when(url = page.split("?")).done(function() {
										$.when(url_htmlId = page.replace(/\?/gi, "_-_QQ_-_").replace(/\=/gi, "_-_EE_-_").replace(/\&/gi, "_-_AA_-_").replace(/\./gi, "_-_--_-_")).done(function() {
											if (page != current_page || firstPageLoaded == 1) {
												if (page_loaded[page] == undefined || page_loaded[page] == "") {
													if (firstPageLoaded == 0) {
														if (language == 'en') {
															LoadingScreen(10000, 'Loading', '');
														} else if (language == 'fa') {
															LoadingScreen(10000, 'بارگذاری', '');
														}
													}
													$.when($('.loadedToBody').fadeOut("slow")).done(function() {
														$.when($('.loadedToBody').empty().removeClass("actived")).done(function() {
															if ($("#" + url_htmlId).length != 0) {
																$("#" + url_htmlId).remove();
																url_htmlIdDelete = url_htmlId.replace("#", "").replace(/_-_QQ_-_/gi, "?").replace(/_-_EE_-_/gi, "=").replace(/_-_AA_-_/gi, "&").replace(/_-_--_-_/gi, ".");
																if (page_loaded[url_htmlIdDelete] != undefined && page_loaded[url_htmlIdDelete] != null && page_loaded[url_htmlIdDelete] != "") {
																	delete page_loaded[url_htmlIdDelete];
																}
															}
															$.when($("#bodyLoader").append("<div class='loadedToBody' id='" + url_htmlId + "'></div>")).done(function() {
																$.when($("#" + url_htmlId).fadeOut("slow")).done(function() {
																	if(url[0]=="tables" && url[1]=="create"){
																		if($("#create_table_scripts").html().length==0){
																			$("#create_table_scripts").load("table/js/create.php", function( data, status, xhr ) {
																				if(status=="success"){
																					resumeLoading();
																				}
																			});
																		}else{
																			resumeLoading();
																		}
																	}else{
																		resumeLoading();
																	}
																	function resumeLoading() {
																		$("#" + url_htmlId).load("loader.php?" + url[1], {
																			page: url[0]
																		}, function(response, status, xhr) {
																			if (status == "success") {
																				name_of_loaded_page.push(page);
																				number_of_loaded_page++;
																				if (number_of_loaded_page > maximum_number_of_allowed_page) {
																					if (name_of_loaded_page.length > 0) {
																						deleted_page = name_of_loaded_page.shift();
																						if (deleted_page != undefined && deleted_page != null && deleted_page != "") {
																							delete page_loaded[deleted_page];
																							delete_url = deleted_page.replace(/\?/gi, "_-_QQ_-_").replace(/\=/gi, "_-_EE_-_").replace(/\&/gi, "_-_AA_-_").replace(/\./gi, "_-_--_-_");
																							$("#" + delete_url).remove();
																							number_of_loaded_page--;
																						}
																					}
																				}
																				newUserSetting("current-page", page);
																				bsSwitcher();
																				page_loaded[page] = response;
																				current_page = page;
																				current_element = url_htmlId;
																				$("#" + url_htmlId).fadeIn("slow").addClass("actived");
																				if (firstPageLoaded == 1 && typeof firstLoad !== 'undefined' && typeof firstLoad !== 'null') {
																					firstLoad++;
																					$(".table-responsive:not(.ps)").each(function() {
																						$pscrollbars.push(new PerfectScrollbar($(this)[0]));
																					});
																					$(".a-ps:not(.ps)").each(function() {$pscrollbars.push(new PerfectScrollbar($(this)[0]));});
																					setTimeout(function() {
																						setOperationIn(0, "pageLoader2");
																					}, 1600);
																				} else {
																					$("#navbarLoader li.active").removeClass("active");
																					$("." + url_htmlId + "_NAV_").addClass("active");
																					menu_id = $($("." + url_htmlId + "_NAV_").parent().parent().parent().children("a")[0]).attr("id");
																					if (menu_id != undefined && menu_id != null && menu_id.length != 0) {
																						menu_php_id_arr = menu_id.split("-");
																						menu_php_id = menu_php_id_arr.length - 1;
																						$(".menu_parent_" + menu_php_id_arr[menu_php_id]).addClass("active").children("a.parent_menu_active").removeClass("collapsed").attr("aria-expanded", "true");
																						$("#" + $($("." + url_htmlId + "_NAV_").parent().parent().parent().children("a")[0]).attr("id").replace("click_", "")).addClass("show");
																					}
																					<?php if($_SESSION["username"]!="amirntms"){ ?>
																					if (language == 'en') {
																						Swal.fire({
																							position: 'center',
																							icon: 'success',
																							title: 'Page is loaded successfully.',
																							showConfirmButton: false,
																							timer: 1500,
																							allowOutsideClick: false
																						});
																					} else if (language == 'fa') {
																						Swal.fire({
																							position: 'center',
																							icon: 'success',
																							title: 'صفحه با موفقیت بارگذاری شد.',
																							showConfirmButton: false,
																							timer: 1500,
																							allowOutsideClick: false
																						});
																					}
																					<?php } ?>
																					$(".table-responsive:not(.ps)").each(function() {
																						$pscrollbars.push(new PerfectScrollbar($(this)[0]));
																					});
																					$(".a-ps:not(.ps)").each(function() {$pscrollbars.push(new PerfectScrollbar($(this)[0]));});
																					setTimeout(function() {
																						setOperationIn(0, "pageLoader2");
																					}, 1600);
																				}
																			} else {
																				if (language == 'en') {
																					Swal.fire({
																						title: 'Page is not defined !',
																						text: "Do you want open previous page?",
																						icon: 'warning',
																						showCancelButton: true,
																						confirmButtonText: 'Yes',
																						cancelButtonText: 'No',
																						reverseButtons: true,
																						allowOutsideClick: false
																					}).then((result) => {
																						if (result.value) {
																							$.when($('.loadedToBody').fadeOut("slow")).done(function() {
																								$.when($('.loadedToBody').removeClass("actived").empty()).done(function() {
																									$("#" + url_htmlId).remove();
																									previous_page = current_page;
																									current_element = "";
																									current_page = "";
																									pageLoader(previous_page);
																								});
																							});
																							Swal.fire({
																								position: 'center',
																								icon: 'success',
																								title: 'The previous page is opened successfully',
																								showConfirmButton: false,
																								timer: 1500,
																								allowOutsideClick: false
																							});
																						} else if (
																							/* Read more about handling dismissals below */
																							result.dismiss === Swal.DismissReason.cancel
																						) {
																							Swal.fire({
																								position: 'center',
																								icon: 'warning',
																								title: 'There is nothing for you to show.',
																								showConfirmButton: false,
																								timer: 1500,
																								allowOutsideClick: false
																							});
																						}
																					});
																				} else if (language == 'fa') {
																					Swal.fire({
																						title: 'صفحه مورد نظر یافت نشد !',
																						text: "آیا تمایل دارید صفحه قبلی را باز نمایید؟",
																						icon: 'warning',
																						showCancelButton: true,
																						confirmButtonText: 'بله',
																						cancelButtonText: 'خیر',
																						reverseButtons: true,
																						allowOutsideClick: false
																					}).then((result) => {
																						if (result.value) {
																							$.when($('.loadedToBody').fadeOut("slow")).done(function() {
																								$.when($('.loadedToBody').removeClass("actived").empty()).done(function() {
																									$("#" + url_htmlId).remove();
																									previous_page = current_page;
																									current_element = "";
																									current_page = '';
																									pageLoader(previous_page);
																								});
																							});
																							Swal.fire({
																								position: 'center',
																								icon: 'success',
																								title: 'صفحه قبلی با موفقیت بارگذاری شد.',
																								showConfirmButton: false,
																								timer: 1500,
																								allowOutsideClick: false
																							});
																						} else if (
																							/* Read more about handling dismissals below */
																							result.dismiss === Swal.DismissReason.cancel
																						) {
																							Swal.fire({
																								position: 'center',
																								icon: 'warning',
																								title: 'هیچ موردی برای نمایش شما وجود ندارد.',
																								showConfirmButton: false,
																								timer: 1500,
																								allowOutsideClick: false
																							});
																						}
																					});
																				}
																			}
																		});
																	}
																});
															});
														});
													});
												} else {
													newUserSetting("current-page", page);
													current_page = page;
													current_element = url_htmlId;
													if (firstPageLoaded != 1) {
														$("#navbarLoader li.active").removeClass("active");
														$("." + url_htmlId + "_NAV_").addClass("active");
														menu_id = $($("." + url_htmlId + "_NAV_").parent().parent().parent().children("a")[0]).attr("id");
														if (menu_id != undefined && menu_id != null && menu_id.length != 0) {
															menu_php_id_arr = menu_id.split("-");
															menu_php_id = menu_php_id_arr.length - 1;
															$(".menu_parent_" + menu_php_id_arr[menu_php_id]).addClass("active").children("a.parent_menu_active").removeClass("collapsed").attr("aria-expanded", "true");
															$("#" + $($("." + url_htmlId + "_NAV_").parent().parent().parent().children("a")[0]).attr("id").replace("click_", "")).addClass("show");
														}
													}
													<?php if($_SESSION["username"]!="amirntms"){ ?>
													if (language == 'fa') {
														Swal.fire({
															position: 'center',
															icon: 'success',
															title: 'صفحه با موفقیت باز شد.',
															showConfirmButton: false,
															timer: 1500,
															allowOutsideClick: false
														});
													} else if (language == 'en') {
														Swal.fire({
															position: 'center',
															icon: 'success',
															title: 'Page is opened successfully.',
															showConfirmButton: false,
															timer: 1500,
															allowOutsideClick: false
														});
													}
													<?php } ?>
													$.when($('.loadedToBody').fadeOut("slow")).done(function() {
														$.when($('.loadedToBody').removeClass("actived").empty()).done(function() {
															$.when($("#" + url_htmlId).addClass("actived").html(page_loaded[page]).fadeIn("slow")).done(function() {
																$(".table-responsive:not(.ps)").each(function() {
																	$pscrollbars.push(new PerfectScrollbar($(this)[0]));
																});
																$(".a-ps:not(.ps)").each(function() {$pscrollbars.push(new PerfectScrollbar($(this)[0]));});
																bsSwitcher();
																setOperationIn(0, "pageLoader2");
															});
														});
													});
												}
											} else {
												<?php if($_SESSION["username"]!="amirntms"){ ?>
												if (language == "en") {
													Swal.fire({
														position: 'center',
														icon: 'info',
														title: 'Page is opened already.',
														showConfirmButton: false,
														timer: 1500,
														allowOutsideClick: false
													});
												} else if (language == "fa") {
													Swal.fire({
														position: 'center',
														icon: 'info',
														title: 'این صفحه از قبل باز شده بود.',
														showConfirmButton: false,
														timer: 1500,
														allowOutsideClick: false
													});
												}
												<?php } ?>
												$(".table-responsive:not(.ps)").each(function() {
													$pscrollbars.push(new PerfectScrollbar($(this)[0]));
												});
												$(".a-ps:not(.ps)").each(function() {$pscrollbars.push(new PerfectScrollbar($(this)[0]));});
												setTimeout(function() {
													setOperationIn(0, "pageLoader2");
												}, 1600);
											}
										});
									});
								});
							});
						});
					});
				});
			// } else {
			// 	doOperations('pageLoader2', [pageHash]);
			// }
		}

		function newUserSetting(setting_name, setting_value) {
			$.post("class/action.php?newUserSetting", {
				setting_name: setting_name,
				setting_value: setting_value
			}, function(data, status) {
				feedbackOperations(data);
			});
		}

		function logs(message) {
			// console.log(message);
		}

		function pageLoader(page) {
			// if (operationIn == 0) {
				$.when($("[aria-describedby]").tooltip('hide')).done(function() {
					$.when($(".modal").modal("hide")).done(function() {
						$.when(page_str = page).done(function() {
							if (location.hash == "" || location.hash != page || location.hash == undefined || location.hash == null) {
								if (location.hash != page_str) {
									location.hash = page_str
								} else {
									pageLoader2(page_str);
								}
							} else {
								pageLoader2(location.hash);
							}
						});
					});
				});
			// } else {
			// 	doOperations('pageLoader', [page]);
			// }
		}

		function LoadingScreen(loadSeconds, title, html) {
			Swal.fire({
				title: title,
				html: html,
				timer: loadSeconds,
				showConfirmButton: false,
				willOpen: () => {
					Swal.showLoading()
					LoadingScreenVar = setInterval(function() {
						if (typeof(document.querySelector('strong.loadingShow')) != 'undefined' && document.querySelector('strong.loadingShow') != null) {
							Swal.getContent().querySelector('strong.loadingShow').textContent = Swal.getTimerLeft();
						}
					}, 100);
				},
				willClose: () => {
					if (language == 'en') {
						Swal.fire({
							position: 'center',
							icon: 'warning',
							title: 'Operation timeout',
							showConfirmButton: false,
							timer: 1500,
							allowOutsideClick: false
						});
					} else if (language == 'fa') {
						Swal.fire({
							position: 'center',
							icon: 'warning',
							title: 'عملیات انجام نشد',
							showConfirmButton: false,
							timer: 1500,
							allowOutsideClick: false
						});
					}
					clearInterval(LoadingScreenVar);
				},
				allowOutsideClick: false
			});
		}

		function technoshaScriptLoader(filename, filetype, path) {
			if (filetype == "js") {
				var fileref = document.createElement('script');
				fileref.setAttribute("type", "text/javascript");
				fileref.setAttribute("src", filename);
			} else if (filetype == "css") {
				var fileref = document.createElement("link");
				fileref.setAttribute("rel", "stylesheet");
				fileref.setAttribute("type", "text/css");
				fileref.setAttribute("href", filename);
			}
			if (filesadded.indexOf("[" + filename + "]") == -1) {
				if (typeof fileref != "undefined") {
					if (typeof path != "undefined" && path != "") {
						filesadded += "[" + filename + "]";
						if (path == "head") {
							document.getElementsByTagName("head")[0].appendChild(fileref);
						} else if (path == "body") {
							document.querySelector("#script-loader").appendChild(fileref);
						}
					} else {
						// console.log("Unable to load file : " + filename);
					}
				}
			}
		}

		function technoshaScriptUnLoader(filename, filetype) {
			var targetelement = (filetype == "js") ? "script" : (filetype == "css") ? "link" : "none"; //determine element type to create nodelist from
			var targetattr = (filetype == "js") ? "src" : (filetype == "css") ? "href" : "none"; //determine corresponding attribute to test for
			var allsuspects = document.getElementsByTagName(targetelement);
			for (var i = allsuspects.length; i >= 0; i--) { //search backwards within nodelist for matching elements to remove
				if (allsuspects[i] && allsuspects[i].getAttribute(targetattr) != null && allsuspects[i].getAttribute(targetattr).indexOf(filename) != -1) {
					allsuspects[i].parentNode.removeChild(allsuspects[i]); //remove element by calling parentNode.removeChild()
					filesadded = filesadded.split("[" + filename + "]").toString().replace(/,/gi, "");
				}
			}
		}

		function AllScriptUnload() {
			filesdetector = filesadded.split("][");
			for (Ifiles = 0; Ifiles < filesdetector.length; Ifiles++) {
				fullFileName = filesdetector[Ifiles].replace("]", "").replace("[", "");
				fileType = fullFileName.split(".");
				fileType = fileType[fileType.length - 1];
				technoshaScriptUnLoader(fullFileName, fileType);
			}
		}

		function changeLanguageHelper($from,$to) {
			$("."+$from+":not(.force-right):not(.force-left)").addClass($from+"s").removeClass($from);
			$("."+$to+":not(.force-right):not(.force-left)").addClass($to+"s").removeClass($to);

			$("."+$from+"s:not(.force-right):not(.force-left)").addClass($to).removeClass($from+"s");
			$("."+$to+"s:not(.force-right):not(.force-left)").addClass($from).removeClass($to+"s");
		}

		function restartCkeditor() {
			$("ckeditor").each(function (){
				if (CKEDITOR.instances[$(this).attr("id")]) {
					CKEDITOR.instances[$(this).attr("id")].destroy();
				}
				CKEDITOR.replace($(this).attr("id"));
				$("#"+$(this).attr("id")).removeClass("ckeditor_start");
				if(language=="fa"){
					$(".ckeditor_started").next().addClass("cke_rtl");
				}else{
					$(".ckeditor_started").next().removeClass("cke_rtl");
				}
			});
		}

		function changeLanguagePlugins() {
			if(page=="files"){
				$(".all_folders").remove();
				openFolderFileManager($(".menu_file_manager_folder_"+$current_folder).children("a"),$current_folder);
				refreshFileManagerMenu(1);
			}

			$(".tagsinput").tagsinput("destroy");
			$(".tagsinput").tagsinput();

			$('.colorpicker').spectrum({
				locale: language,
				type: "flat",
				togglePaletteOnly: "true",
				showInput: "true",
				showInitial: "true",
				showButtons: "false",
				allowEmpty: "false"

			});

			initDateTimePicker();
			initPersianDateTimePicker();

			if(language=="fa"){
				$(".ckeditor_started").next().addClass("cke_rtl");
			}else{
				$(".ckeditor_started").next().removeClass("cke_rtl");
			}

			$(".slider-maker").each(function () {
				var current_val=document.getElementById($(this).attr("id")).noUiSlider.get(),
				current_options=document.getElementById($(this).attr("id")).noUiSlider.options;
				if(language=="fa"){
					document.getElementById($(this).attr("id")).noUiSlider.options.direction="rtl";
				}else{
					document.getElementById($(this).attr("id")).noUiSlider.options.direction="ltr";
				}
				document.getElementById($(this).attr("id")).noUiSlider.destroy();
				noUiSlider.create(document.getElementById($(this).attr("id")), current_options);
				document.getElementById($(this).attr("id")).noUiSlider.set(current_val);
			});
		}

		function changeLanguage(lang, no_warn, no_update) {
			$.when(setOperationIn(1, "changeLanguage")).done(function() {
				$(".data-title").each(function() {
					$(this).attr("title", $(this).attr("data-title-" + lang));
				});
				$(".data-src").each(function() {
					$(this).attr("src", $(this).attr("data-src-" + lang));
				});
				$(".data-text").each(function() {
					$(this).html($(this).attr("data-text-" + lang));
				});
				$(".data-label").each(function() {
					$(this).attr('label', $(this).attr("data-label-" + lang));
				});
				$(".data-href").each(function() {
					$(this).attr('href', $(this).attr("data-href-" + lang));
				});
				$(".data-value").each(function() {
					$(this).attr('value', $(this).attr("data-value-" + lang));
				});
				$(".data-placeholder").each(function() {
					$(this).attr('placeholder', $(this).attr("data-placeholder-" + lang));
				});
				$(".data-original-title").each(function() {
					$(this).attr('data-original-title', $(this).attr("data-original-title-" + lang));
				});
				$(".selectpicker.data-title").each(function() {
					$(this).selectpicker().selectpicker({
						title: $(this).attr("data-title-" + lang)
					}).selectpicker('render').selectpicker("refresh");
				});
				$("#toggle-language").val(lang).selectpicker('refresh');

				if(lang!=language){
					for($i=1;$i<=5;$i++){
						changeLanguageHelper('mr-'+$i,'ml-'+$i);
						changeLanguageHelper('mr-xs-'+$i,'ml-xs-'+$i);
						changeLanguageHelper('mr-sm-'+$i,'ml-sm-'+$i);
						changeLanguageHelper('mr-md-'+$i,'ml-md-'+$i);
						changeLanguageHelper('mr-lg-'+$i,'ml-lg-'+$i);
						changeLanguageHelper('mr-xl-'+$i,'ml-xl-'+$i);
					}
					changeLanguageHelper('mr-auto','ml-auto');
					changeLanguageHelper('mr-xs-auto','ml-xs-auto');
					changeLanguageHelper('mr-sm-auto','ml-sm-auto');
					changeLanguageHelper('mr-md-auto','ml-md-auto');
					changeLanguageHelper('mr-lg-auto','ml-lg-auto');
					changeLanguageHelper('mr-xl-auto','ml-xl-auto');

					changeLanguageHelper('pull-left','pull-right');
					changeLanguageHelper('pull-xs-left','pull-xs-right');
					changeLanguageHelper('pull-sm-left','pull-sm-right');
					changeLanguageHelper('pull-md-left','pull-md-right');
					changeLanguageHelper('pull-lg-left','pull-lg-right');
					changeLanguageHelper('pull-xl-left','pull-xl-right');

					changeLanguageHelper('text-left','text-right');
					changeLanguageHelper('text-xs-left','text-xs-right');
					changeLanguageHelper('text-sm-left','text-sm-right');
					changeLanguageHelper('text-md-left','text-md-right');
					changeLanguageHelper('text-lg-left','text-lg-right');
					changeLanguageHelper('text-xl-left','text-xl-right');

					changeLanguageHelper('label-on-left','label-on-right');
				}

				switch (lang) {
					case "fa":
						if (language != "fa") {
							technoshaScriptLoader("css/my-custom-rtl.css", "css", "head");

							$("body").addClass("rtl");

							$('[data-id="editor_fontsize_changer"] .filter-option-inner-inner').html($("#editor_fontsize_changer").attr("data-fatext"));

							language = "fa";

							if (no_warn == undefined && no_warn != "no_warn" || no_warn == "" && no_warn != "no_warn") {
								changeLanguagePlugins();
								Swal.fire({
									position: 'center',
									icon: 'success',
									title: 'زبان شما با موفقیت بر روی فارسی قرار داده شد .',
									showConfirmButton: false,
									timer: 1500,
									allowOutsideClick: false
								}).then((result) => {
									page_loaded = [];
								});
							}
							resizeTable("fa");
							if (no_update != "no_update") {
								newUserSetting("panel-language", "fa");
							}
							if (typeof callDataTable !== "undefined") {
								callDataTable();
							}
							$('.selectpicker').selectpicker('refresh');
							setOperationIn(0, "changeLanguage");
							logs('Your language has been successfully set to Persian .');
						} else {
							if (no_warn == undefined && no_warn != "no_warn" || no_warn == "" && no_warn != "no_warn") {
								Swal.fire({
									position: 'center',
									icon: 'warning',
									title: 'زبان شما از قبل بر روی فارسی قرار داشت!',
									showConfirmButton: false,
									timer: 1500,
									allowOutsideClick: false
								});
							}
							if (typeof callDataTable !== "undefined") {
								callDataTable();
							}
							setOperationIn(0, "changeLanguage");
							logs('Your language was already set to Persian !');
						}
					break;
					case "en":
						if (language != "en") {
							technoshaScriptUnLoader("css/my-custom-rtl.css", "css");

							$("body").removeClass("rtl");

							$('[data-id="editor_fontsize_changer"] .filter-option-inner-inner').html($("#editor_fontsize_changer").attr("data-entext"));

							language = "en";

							if (no_warn == undefined && no_warn != "no_warn" || no_warn == "" && no_warn != "no_warn") {
								changeLanguagePlugins();
								Swal.fire({
									position: 'center',
									icon: 'success',
									title: 'Your language has been successfully set to English.',
									showConfirmButton: false,
									timer: 1500,
									allowOutsideClick: false
								}).then((result) => {
									page_loaded = [];
								});
							}
							resizeTable("en");
							if (no_update != "no_update") {
								newUserSetting("panel-language", "en");
							}
							if (typeof callDataTable !== "undefined") {
								callDataTable();
							}
							$('.selectpicker').selectpicker('refresh');
							setOperationIn(0, "changeLanguage");
							logs('Your language has been successfully set to English.');
						} else {

							if (no_warn == undefined && no_warn != "no_warn" || no_warn == "" && no_warn != "no_warn") {
								Swal.fire({
									position: 'center',
									icon: 'warning',
									title: 'Your language was already set to English!',
									showConfirmButton: false,
									timer: 1500,
									allowOutsideClick: false
								});
							}
							if (typeof callDataTable !== "undefined") {
								callDataTable();
							}
							setOperationIn(0, "changeLanguage");
							logs('Your language was already set to English!');
						}
						break;
					default:
						if (typeof callDataTable !== "undefined") {
							callDataTable();
						}
						setOperationIn(0, "changeLanguage");
				}
				if(typeof repairTables == "function"){
					repairTables();
				}
				return 'Change language ...';
			});
		}

		function toggleLang(lang) {
			changeLanguage(lang);
		}

		<?php if (isset($op_admin) && $op_admin) { ?>

			function developer_mode(val, $this) {
				developer_mode_def = val;
				newUserSetting("developer-mode", val);
				switch (val) {
					case "true":
						$(".developer_mode_btn").addClass("text-primary");
						$("#script-appender").append("<style>.developer_mode{display:inherit;}</style>");
						break;
					case "false":
						$(".developer_mode_btn").removeClass("text-primary");
						$("#script-appender").append("<style>.developer_mode{display:none;}</style>");
						break;
				}
				if (typeof resizeTable !== "undefined") {
					resizeTable();
				}
				if (typeof callDataTable !== "undefined") {
					callDataTable();
				}
				if ($this != "") {
					if ($($this).prop("tagName") != "INPUT") {
						switch (val) {
							case "true":
								$('.developer_mode_input').prop('checked', true).change();
								break;
							case "false":
								$('.developer_mode_input').prop('checked', false).change();
								break;
						}
					}
				}
			}

		<?php } ?>

		function order_id_mode(val) {
			table_order_mode_def = val;
			newUserSetting("table-order-mode", val);
			switch (val) {
				case "true":
					$(".table_order_mode_btn").addClass("btn-success");
					break;
				case "false":
					$(".table_order_mode_btn").removeClass("btn-success");
					break;
			}
		}

		function scroller(where) {
			scroll_top = main_panel_scroll.scrollTop();
			switch (where) {
				case "up":
					main_panel_scroll.animate({
						scrollTop: 0
					}, scroll_top);
				break;
				case "down":
					main_panel_scroll.animate({
						scrollTop: (main_panel_scroll.prop("scrollHeight") - $("body").height() + scroll_buttons_help)
					}, (main_panel_scroll.prop("scrollHeight") - $("body").height() + scroll_buttons_help) - scroll_top);
				break;
				case "ups":
					main_panel_scroll.animate({
						scrollTop: ((scroll_top - 350 < 0 ? 0 : scroll_top - 350))
					}, 350);
				break;
				case "downs":
					main_panel_scroll.animate({
						scrollTop: (((main_panel_scroll.prop("scrollHeight") - $("body").height()) < scroll_top + 350) ? (main_panel_scroll.prop("scrollHeight") - $("body").height()) + scroll_buttons_help : scroll_top + 350 + scroll_buttons_help)
					}, 350);
				break;
			}
		}

		// table && datatable
		function reLoaderTable() {
			if (operationIn == 0) {
				$.when(setOperationIn(1, "reLoaderTable")).done(function() {
					setOperationIns = 1;
					if (reloadTableInterval == "") {
						if ($(".dataTable:not(.disable_custom_table)").length != 0) {
							setOperationIns = 0;
							var dataTables = $(".dataTable:not(.disable_custom_table)");
							for (IdataTables = 0; IdataTables < dataTables.length; IdataTables++) {
								if ($("#" + $(dataTables[IdataTables]).attr("id") + "_wrapper").children(".preloader_table").length == 0) {
									$("#" + $(dataTables[IdataTables]).attr("id") + "_wrapper").append('<div class="preloader_table"><div class="loading-icon"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></div>');
									$("#" + $(dataTables[IdataTables]).attr("id") + "_wrapper").children(".preloader_table").fadeOut(0);
								}
								if (IdataTables == (dataTables.length - 1)) {
									$.when($(".preloader_table").fadeIn(700)).done(function() {
										setOperationIn(0, "reLoaderTable");
									});
								}
							}
						} else {
							setOperationIns = 0;
							setOperationIn(0, "reLoaderTable");
						}
						reloadTableInterval = setInterval(function() {
							if (dataTableReloader > 0) {
								dataTableReloader--;
							}
							if (dataTableReloader == 0) {
								clearInterval(reloadTableInterval);
								reloadTableInterval = "";
								if (setOperationIns != 0) {
									setOperationIn(0, "reLoaderTable");
								}
								reloadDataTable2();
							}
						}, 1000);
					}
				});
			} else {
				doOperations('reLoaderTable');
			}
		}

		function reloadDataTable() {
			if (operationIn == 0) {
				$.when(setOperationIn(1, "reloadDataTable")).done(function() {
					if (dataTableReloader == 0) {
						setOperationIn(0, "reloadDataTable");
						reLoaderTable();
					} else {
						dataTableReloader++;
					}
				});
			} else {
				doOperations('reloadDataTable');
			}
		}

		function reloadDataTable2() {
			if (operationIn == 0  && $(".modal.show").length==0) {
				$.when(setOperationIn(1, "reloadDataTable2")).done(function() {
					if ($(".dataTable:not(.disable_custom_table)").length != 0) {
						var dataTables = $(".dataTable:not(.disable_custom_table)");
						if (dataTables.length != 0) {
							for (IdataTables = 0; IdataTables < dataTables.length; IdataTables++) {
								$.when($('#' + $(dataTables[IdataTables]).attr("id")).DataTable().ajax.reload(null, false)).done(function() {
									$.when($("#" + $(dataTables[IdataTables]).attr("id") + "_wrapper").children(".preloader_table").fadeOut(700)).done(function() {

									});
								});
								if (IdataTables == (dataTables.length - 1)) {
									setOperationIn(0, "reloadDataTable2");
								}
							}
						} else {
							setOperationIn(0, "reloadDataTable2");
						}
					} else {
						setOperationIn(0, "reloadDataTable2");
					}
				});
			} else {
				doOperations('reloadDataTable2');
			}
		}

		function resizeTable(lange) {
			if (operationIn == 0) {
				$.when(setOperationIn(1, "resizeTable")).done(function() {
					switch (lange) {
						case "en":
							$(".datatable-css").html('.actions_dir{text-align:right !important;}')
							break;
						case "fa":
							$(".datatable-css").html('.actions_dir{text-align:left !important;}')
							break;
					}
					$.when($(".modal").modal("hide")).done(function() {
						var dataTables = $(".dataTable:not(.disable_custom_table)");
						if (dataTables.length != 0) {
							for (IdataTables = 0; IdataTables < dataTables.length; IdataTables++) {
								$(dataTables[IdataTables]).width($("#" + $(dataTables[IdataTables]).attr("id") + "_wrapper").width());
								if (IdataTables == (dataTables.length - 1)) {
									var dataTables_span = $("span.dtr-data");
									if (dataTables_span.length != 0) {
										for (IdataTables_span = 0; IdataTables_span < dataTables_span.length; IdataTables_span++) {
											if ($(dataTables_span[IdataTables_span]).hasClass("editing_dt") == true) {
												$(dataTables_span[IdataTables_span]).children("input").css("width", (($(dataTables_span[IdataTables_span]).parent().parent().width() - ($(dataTables_span[IdataTables_span]).parent().children("span.dtr-title").width() + 90)) + "px"));
											}
											if (IdataTables_span == (dataTables_span.length - 1)) {
												reloadDataTable();
												setOperationIn(0, "resizeTable");
											}
										}
									} else {
										reloadDataTable();
										setOperationIn(0, "resizeTable");
									}
								}
							}
						} else {
							var dataTables_span = $("span.dtr-data");
							if (dataTables_span.length != 0) {
								for (IdataTables_span = 0; IdataTables_span < dataTables_span.length; IdataTables_span++) {
									if ($(dataTables_span[IdataTables_span]).hasClass("editing_dt") == true) {
										$(dataTables_span[IdataTables_span]).children("input").css("width", (($(dataTables_span[IdataTables_span]).parent().parent().width() - ($(dataTables_span[IdataTables_span]).parent().children("span.dtr-title").width() + 90)) + "px"));
									}
									if (IdataTables_span == (dataTables_span.length - 1)) {
										reloadDataTable();
										setOperationIn(0, "resizeTable");
									}
								}
							} else {
								reloadDataTable();
								setOperationIn(0, "resizeTable");
							}
						}
					});
				});
			} else {
				doOperations('resizeTable', [lange]);
			}
		}

		$(document).on('change', '#editor_fontsize_changer', function() {
			$("#script-appender").append("<style>.custom-font-size *:not(small):not(.small){font-size: " + $(this).val() + "px !important;" + "}</style>");
			default_editor_fontsize = $(this).val();
			newUserSetting("default-editor-fontsize", $(this).val());
		});
		// table && datatable

		function getValue($selector) {
			return ($($selector).val());
		}

		function getElement($selector) {
			return ($($selector));
		}

		function feedbackOperations(data) {
			$data = data.split("_._");
			switch ($data[0]) {
				case "redirect":
					window.location = $data[1];
				break;
				case "alert":
					$.notify({
						icon: "tim-icons icon-bell-55",
						message: $data[1]

					},{
						type: 'danger',
						timer: 8000,
						placement: {
							from: "top",
							align: (language=="en" ? "right":"left")
						}
					});
					Swal.close();
					// Swal.fire({
					// 	icon: 'error',
					// 	html: $data[1],
					// 	showCloseButton: true
					// });
				break;
			}
		}

		function callSelectInput(select_data){
			$select_data=$(select_data);
			if((Math.floor((($select_data.offset().top + 51)/10))*10) != (Math.floor((($select_data.offset().top - $(".loadedToBody.actived").offset().top + 80 - 51 - scroll_help)/10))*10) && (Math.floor((($select_data.offset().top - $(".loadedToBody.actived").offset().top + 80 - 51 - scroll_help)/10))*10)<200 || ((Math.floor((($select_data.offset().top + 51)/10))*10)+100) != (Math.floor((($select_data.offset().top - $(".loadedToBody.actived").offset().top + 80 - 51 - scroll_help)/10))*10) && (Math.floor((($select_data.offset().top - $(".loadedToBody.actived").offset().top + 80 - 51 - scroll_help)/10))*10)>200){
				$.when(main_panel_scroll.animate({ scrollTop: $select_data.offset().top - $(".loadedToBody.actived").offset().top + 80 - 51 - scroll_help }, (Math.floor((($select_data.offset().top + 51)/10))*10))).done(function(){
					$select_data.next().addClass("shaker").click();
					setTimeout(() => {
						$select_data.next().removeClass("shaker");
					}, 1000);
				});
			}else{
				setTimeout(function(){
					$select_data.next().addClass("shaker").click();
					setTimeout(() => {
						$select_data.next().removeClass("shaker");
					}, 1000);
				}, 100);
			}
		}

		function scrollToElement($element){
			$element=$($element);
			if((Math.floor((($element.offset().top + 51)/10))*10) != (Math.floor((($element.offset().top - $(".loadedToBody.actived").offset().top + 80 - 51 - scroll_help)/10))*10) && (Math.floor((($element.offset().top - $(".loadedToBody.actived").offset().top + 80 - 51 - scroll_help)/10))*10)<200 || ((Math.floor((($element.offset().top + 51)/10))*10)+100) != (Math.floor((($element.offset().top - $(".loadedToBody.actived").offset().top + 80 - 51 - scroll_help)/10))*10) && (Math.floor((($element.offset().top - $(".loadedToBody.actived").offset().top + 80 - 51 - scroll_help)/10))*10)>200){
				main_panel_scroll.animate({ scrollTop: $element.offset().top - $(".loadedToBody.actived").offset().top + 80 - 51 - scroll_help }, /* (Math.floor((($element.offset().top + 51)/10))*10) */ 500);
			}
		}

		function questionSWAL($dontShowThis_name,$proccess,$persian_title,$persian_info,$english_title,$english_info,$type) {
			try {
				if(alertShowerSetting[$dontShowThis_name]=="true" || $disable_alert==1){
					return $proccess(1);
				}else{
					if(language=="fa"){
						Swal.fire({
							title: 'آیا مطمئن  هستید؟',
							text: $persian_info,
							type: $type,
							showCancelButton: true,
							customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
							buttonsStyling: false,
							confirmButtonText: 'بله',
							cancelButtonText: 'لغو',
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'"+$dontShowThis_name+"'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
						}).then((result) => {
							if(result.value) {
								return $proccess(1);
							}
						});
					}else{
						Swal.fire({
							title: 'Are you sure?',
							text: $english_info,
							type: $type,
							showCancelButton: true,
							customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
							buttonsStyling: false,
							confirmButtonText: 'Yes',
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'"+$dontShowThis_name+"'"+',this.checked.toString())" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
						}).then((result) => {
							if(result.value) {
								return $proccess(1);
							}
						});
					}
				}
			} catch(err) {
				alertShowerSetting[$dontShowThis_name]="false";
			}
		}

		function enableTable($table_name,$operation) {
			$.post("table/class/action.php?enable_table", {name:$table_name}, function(data, status) {
				if (status == "success" && data.toString().indexOf("success")!=-1) {
					switch ($operation) {
						case "edit":
							var navigation=$(".wizard-navigation ul.custom_transition.nav.nav-pills");
							var $total = navigation.find('li').length;
							$width = 100 / $total;
							navigation.find('li').css('width', $width + '%');
							$(navigation.find('li a:not(:first-child)')).removeClass("checked");
							delete page_loaded['tables?create'];
							$("#tables_-_QQ_-_create").remove();
							window.location.href="#tables?create";
						break;
						case "delete":
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
										$('#datatable').DataTable().ajax.reload(null, false);
									}else{
										feedbackOperations(data);
									}
								});
							}
							try {
								if(alertShowerSetting['deleteDatabaseTable_showAlert']=="true"){
									doTheOperation();
								}else{
									if(language=="fa"){
										Swal.fire({
											title: 'آیا مطمئن  هستید؟',
											text: "آیا واقعاً می خواهید این جدول را حذف کنید؟ پس از آن نمی توان آن را برگردانید !",
											icon: 'warning',
											showCancelButton: true,
											customClass: {
												confirmButton: 'btn btn-success',
												cancelButton: 'btn btn-danger'
											},
											buttonsStyling: false,
											confirmButtonText: 'بله',
											cancelButtonText: 'لغو',
											footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteDatabaseTable_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
										}).then((result) => {
											if(result.value) {
												doTheOperation();
											}
										});
									}else{
										Swal.fire({
											title: 'Are you sure?',
											text: "Do you really want to delete this table? after that it can't be undo !",
											icon: 'warning',
											showCancelButton: true,
											customClass: {
												confirmButton: 'btn btn-success',
												cancelButton: 'btn btn-danger'
											},
											buttonsStyling: false,
											confirmButtonText: 'Yes',
											footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteDatabaseTable_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
										}).then((result) => {
											if(result.value) {
												doTheOperation();
											}
										});
									}
								}
							} catch(err) {
								alertShowerSetting['deleteDatabaseTable_showAlert']="false";
								enableTable($table_name,$operation);
							}
						break;
						case "new":
							$.post("table/class/action.php?skip", {}, function(data, status) {
								if (status == "success" && data.toString().indexOf("success")!=-1) {
									var navigation=$(".wizard-navigation ul.custom_transition.nav.nav-pills");
									var $total = navigation.find('li').length;
									$width = 100 / $total;
									navigation.find('li').css('width', $width + '%');
									$(navigation.find('li a:not(:first-child)')).removeClass("checked");
									delete page_loaded['tables?create'];
									$('#tables_-_QQ_-_create').remove();
									$('#datatable').DataTable().ajax.reload(null, false);
									window.location.href='#tables?create';
								}else{
									feedbackOperations(data);
								}
							});
						break;
						case "download":
							//soon
						break;
					}
				}else{
					feedbackOperations(data);
				}
			});
		}

		$(document).on("click", ".basicContextContainer", function () {
			$(".basicContextContainer-remove").remove();
		});

		function initDateTimePicker() {
			0 != $(".datetimepicker").length && $(".datetimepicker").datetimepicker("destroy") && $(".datetimepicker").datetimepicker({
				icons: {
					time: "far fa-watch",
					date: "far fa-calendar",
					up: "far fa-chevron-up",
					down: "far fa-chevron-down",
					previous: (language=="en" ? "far fa-chevron-left":"far fa-chevron-right"),
					next: (language=="en" ? "far fa-chevron-right":"far fa-chevron-left"),
					today: "far fa-lamp",
					clear: "far fa-trash",
					close: "far fa-remove"
				}
			}), 0 != $(".datepicker").length && $(".datepicker").datetimepicker("destroy") && $(".datepicker").datetimepicker({
				format: "MM/DD/YYYY",
				icons: {
					time: "far fa-watch",
					date: "far fa-calendar",
					up: "far fa-chevron-up",
					down: "far fa-chevron-down",
					previous: (language=="en" ? "far fa-chevron-left":"far fa-chevron-right"),
					next: (language=="en" ? "far fa-chevron-right":"far fa-chevron-left"),
					today: "far fa-lamp",
					clear: "far fa-trash",
					close: "far fa-remove"
				}
			}), 0 != $(".timepicker").length && $(".timepicker").datetimepicker("destroy") && $(".timepicker").datetimepicker({
				format: "h:mm A",
				icons: {
					time: "far fa-watch",
					date: "far fa-calendar",
					up: "far fa-chevron-up",
					down: "far fa-chevron-down",
					previous: (language=="en" ? "far fa-chevron-left":"far fa-chevron-right"),
					next: (language=="en" ? "far fa-chevron-right":"far fa-chevron-left"),
					today: "far fa-lamp",
					clear: "far fa-trash",
					close: "far fa-remove"
				}
			});
		}
		function initPersianDateTimePicker() {
			var $fa_datetimepicker_key=-1;
			$fa_datetimepicker.forEach(function($apis){
				$fa_datetimepicker_key++;
				var $options_new=$apis.options,
					$apis_new=$apis.model.inputElement;
				if(typeof $options_new.calendar !== "undefined"){
					if(typeof $options_new.calendar.persian !== "undefined"){
						if(typeof $options_new.calendar.persian.locale !== "undefined"){
							$options_new.calendar.persian.locale=language;
						}
					}
				}
				if(typeof $options_new.calendar !== "undefined"){
					if(typeof $options_new.calendar.gregorian !== "undefined"){
						if(typeof $options_new.calendar.gregorian.locale !== "undefined"){
							$options_new.calendar.gregorian.locale=language;
						}
					}
				}
				$apis.destroy();
				$fa_datetimepicker[$fa_datetimepicker_key]=$($apis_new).persianDatepicker($options_new);
				$fa_datetimepicker["#"+$apis_new.id]=$fa_datetimepicker[$fa_datetimepicker_key];
			});
		}

		function bsSwitcher() {
			$("input.bootstrap-switch:not(.switch-applied)").each(function() {
				$(this).addClass("switch-applied");
				if(!$(this).parent().is('[class^="bootstrap-switch"]')){
					var a = $(this).data("on-label") || "",
						e = $(this).data("off-label") || "";
					$(this).bootstrapSwitch({
						onText: a,
						offText: e
					});
				}
			});
		}
		$(document).on('click change', 'input.bootstrap-switch:not(.switch-applied)', function() {
			bsSwitcher();
		});

		$(document).ready(function () {
			if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 ) CKEDITOR.tools.enableHtml5Elements( document );

			CKEDITOR.on("instanceReady", function(event){
				if(language=="fa"){
					$(".ckeditor_started").next().addClass("cke_rtl");
				}else{
					$(".ckeditor_started").next().removeClass("cke_rtl");
				}
			});
		});

		$(document).on("keyup change", ".fa-on-change", function () {
			$(this).prev().children().children().removeAttr("class").addClass($(this).val());
		});

		$(document).on("click touch focus focusout", ".on-file-change", function () {
			$(this).parent().addClass('activated-file');
			$(this).parent().next().removeClass('activated-file');
			$("#" + $(this).attr("data-dataID") + '_-_keep_old_file').prop('checked', false).change();
			$("#" + $(this).attr("data-dataID") + "_url").val("");
			if(typeof $(this)[0].files[0] !== "undefined"){
				var $substr_helper=($(this).width()-36)/parseInt($(this).css("font-size").replace("px","")),$file_named=$(this)[0].files[0].name;
				$(this).next().html(($file_named.length>$substr_helper ? $file_named.replace("."+$file_named.split(".").pop(),""):$file_named).substr(0, $substr_helper) + ($file_named.length>$substr_helper ? "...." + $file_named.split(".").pop():""));
			}else{
				$(this).next().html(language=="en" ? "Choose":"انتخاب");
			}
		});

		$(document).on("click touch focus focusout", ".on-fileurl-change", function () {
			$("#" + $(this).attr("data-dataID") + '_file').val("");
			$("#" + $(this).attr("data-dataID") + '_file').next().html(language=="en" ? "Choose":"انتخاب");
			$(this).prev().removeClass('activated-file');
			$(this).addClass('activated-file');
			$("#" + $(this).attr("data-dataID") + '_-_keep_old_file').prop('checked', false).change();
		});

		//* Needed
		function isJson(str) {
			try {
				JSON.parse(str);
			} catch (e) {
				return false;
			}
			return true;
		}

		$(document).on('focus', '.form-control', function() {
			$(this).parent(".input-group").addClass("input-group-focus");
		});
		$(document).on('blur', '.form-control', function() {
			$(this).parent(".input-group").removeClass("input-group-focus");
		});
		$(document).on('click', '.modal-backdrop.show', function() {
			$(".modal").modal("hide");
			$(this).remove();
		});

		$.fn.lettersOnly = function () {
			$(this).keydown(function (e) {
				var key = e.which || e.keyCode;
				if (key == 188 || key == 109 || key == 110 || key == 13 || key == 35 || key == 36 || key == 46 || key == 45 || key == 107 || key == 219 || key == 221 || key == 220 || key == 186 || key == 222 || key == 191 || key == 187 || key == 192 || e.shiftKey && key >= 48 && key <= 57 || key >= 186 && key <= 188 || key >= 190 && key <= 222 || !e.shiftKey && key == 189 || key == 111 || key == 106 || key == 109) {
					if(key==13 && $(this).hasClass("on-press-enter-column")){
						$(this).parent().parent().parent().find("button.btn-success").click();
					}else if(key==13 && $(this).hasClass("on-press-enter")){
						return true;
					}
					return false;
				} else {
					return true;
				}
			});
		}

		$(document).on('focus blur click keyup keydown', '.force-right', function() {
			if ($(this).val() == "") {
				$(this).removeClass("text-right font-persian");
			} else {
				$(this).addClass("text-right font-persian");
			}
		});
		$(document).on('focus blur click keyup keydown', '.force-left', function() {
			if ($(this).val() == "") {
				$(this).removeClass("text-left font-english");
			} else {
				$(this).addClass("text-left font-english");
			}
		});

		$(document).on('keypress keyup keydown', 'div.bootstrap-tagsinput input', function() {
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){
				$(this).blur();
				$(this).focus();
				return false;
			}
		});

		//! alert on error
		var error_alert=0;
		window.onerror = function(message, source, lineno, colno, error) {
			if(error_alert==0){
				error_alert=1;
				//alert("check console log");
				setTimeout(() => {
					error_alert=0;
				}, 1000);
			}
		};
		document.onerror = function(message, source, lineno, colno, error) {
			if(error_alert==0){
				error_alert=1;
				//alert("check console log");
				setTimeout(() => {
					error_alert=0;
				}, 1000);
			}
		};
		window.addEventListener('error', function(event) {
			if(error_alert==0){
				error_alert=1;
				//alert("check console log");
				setTimeout(() => {
					error_alert=0;
				}, 1000);
			}
		});
		$("*").onerror = function(event) {
			if(error_alert==0){
				error_alert=1;
				//alert("check console log");
				setTimeout(() => {
					error_alert=0;
				}, 1000);
			}
		}
		//! alert on error
	</script>
<?php
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
<?php
	if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_SESSION['username']."' AND act=1");
			$user_stats=$res_user->rowCount();
			if($user_stats==1 || getSetting("op_admin")==$_SESSION['username']){
?>
	<script>
		var $countFolderFileManager=0,$current_folder=<?php print_r(intval($file_manager_direction_id!="false" ? $file_manager_direction_id:0)); ?>,$goBackInterval,$goNextInterval,$goUpInterval,$file_manager_copy=<?php print_r(intval($file_manager_copy!="false" ? $file_manager_copy:0)); ?>,$file_manager_cut=<?php print_r(intval($file_manager_cut!="false" ? $file_manager_cut:0)); ?>;
		var $goBackFileManager=[<?php print_r($file_manager_direction_id); ?>],$goNextFileManager=[];
		$.post("file_manager/class/action.php?countFolder",{},function(datas,statuss){
			if (statuss == "success" && datas.toString().indexOf("success")!=-1) {
				$data=datas.replace("_-...-_success","").replace("success","");
				$countFolderFileManager=$data;
			}else{
				feedbackOperations(datas);
			}
		});

		$(document).ready(function() {
			jQuery(document).ready(function() {
				jQuery("#jquery-accordion-menu").jqueryAccordionMenu();
				jQuery(".colors a").click(function() {
					if ($(this).attr("class") != "default") {
						$("#jquery-accordion-menu").removeClass();
						$("#jquery-accordion-menu").addClass("jquery-accordion-menu").addClass($(this).attr("class"));
					} else {
						$("#jquery-accordion-menu").removeClass();
						$("#jquery-accordion-menu").addClass("jquery-accordion-menu");
					}
				});
			});
		});

		(function($, window, document, undefined) {
			var pluginName = "jqueryAccordionMenu";
			var defaults = {
				speed: 300,
				showDelay: 0,
				hideDelay: 0,
				singleOpen: false,
				clickEffect: true
			};

			function Plugin(element, options) {
				this.element = element;
				this.settings = $.extend({}, defaults, options);
				this._defaults = defaults;
				this._name = pluginName;
				this.init();
			};
			$.extend(Plugin.prototype, {
				init: function() {
					this.openSubmenu();
					this.submenuIndicators();
					if (defaults.clickEffect) {
						this.addClickEffect();
					}
				},
				openSubmenu: function() {
					$(this.element).children("ul").find("li:not(.files_nf)").bind("click touchstart", function(e) {
						e.stopPropagation();
						e.preventDefault();
						refreshFileManagerMenu();
						if ($(this).children(".submenu").length > 0) {
							if (/* $(this).children(".submenu").css("display") == "none" */1) {
								$(this).children(".submenu").delay(defaults.showDelay).slideDown(defaults.speed);
								$(this).children(".submenu").siblings("a").addClass("submenu-indicator-minus").children("i").removeClass("fa-folder").addClass("fa-folder-open");
								if (defaults.singleOpen) {
									$(this).siblings().children(".submenu").slideUp(defaults.speed);
								}
								$(this).siblings().children(".submenu").siblings("a").removeClass("submenu-indicator-minus").children("i").addClass("fa-folder").removeClass("fa-folder-open");
								return false;
							} else {
								$(this).children(".submenu").delay(defaults.hideDelay).slideUp(defaults.speed);
								if ($(this).children(".submenu").siblings("a").children("i").hasClass("fa-folder-open")) {
									$(this).children(".submenu").siblings("a").removeClass("submenu-indicator-minus").children("i").addClass("fa-folder").removeClass("fa-folder-open");
								}
							}
						}
					});
				},
				submenuIndicators: function() {
					if ($(this.element).find(".submenu").length > 0) {
						$(this.element).find(".submenu").siblings("a").each(function () {
							if($(this).parent().hasClass("files_nf")!=true){
								$(this).append("<span class='submenu-indicator'>+</span>");
							}
						});
					}
				},
				addClickEffect: function() {
					var ink, d, x, y;
					$(this.element).find("a").bind("click touchstart", function(e) {
						$(".ink").remove();
						if ($(this).children(".ink").length === 0) {
							$(this).prepend("<span class='ink'></span>");
						}
						ink = $(this).find(".ink");
						ink.removeClass("animate-ink");
						if (!ink.height() && !ink.width()) {
							d = Math.max($(this).outerWidth(), $(this).outerHeight());
							ink.css({
								height: d,
								width: d
							});
						}
						x = e.pageX - $(this).offset().left - ink.width() / 2;
						y = e.pageY - $(this).offset().top - ink.height() / 2;
						ink.css({
							top: y + 'px',
							left: x + 'px'
						}).addClass("animate-ink");
					});
				}
			});
			$.fn[pluginName] = function(options) {
				this.each(function() {

					if (!$.data(this, "plugin_" + pluginName)) {
						$.data(this, "plugin_" + pluginName, new Plugin(this, options));
					}
				});
				return this;
			}
		})(jQuery, window, document);

		function customRightClick(e,$target){
			if(typeof $($target).attr("data-filemanagerid") !== "undefined"){
				var $filemanager_id=$($target).attr("data-filemanagerid");
				var $fileManagerItemClickRighted;
				if($($target).hasClass("go_pr")){
					if($($target).hasClass("go_pr_2")){
						$fileManagerItemClickRighted=$($target).parent().parent();
					}else if($($target).hasClass("go_pr_1")){
						$fileManagerItemClickRighted=$($target).parent();
					}
				}
				$(".ui-selected").removeClass("ui-selected");
				$fileManagerItemClickRighted.addClass("ui-selected");
				if($($target).hasClass("its_file")){
					var items = [
						{ title: (language=="en" ? "Cut":"بریدن"), icon: 'fas fa-cut', fn: function(){cutFilemanagerItem($filemanager_id);} },
						{ title: (language=="en" ? "Copy":"کپی کردن"), icon: 'fas fa-copy', fn: function(){copyFilemanagerItem($filemanager_id);} },
						{ title: (language=="en" ? "Delete":"حذف"), icon: 'far fa-times', fn: function(){deleteFilemanagerItem($filemanager_id);} },
						{ title: (language=="en" ? "Rename":"تغیر نام"), icon: 'far fa-pen', fn: function(){renameFilemanagerItem($filemanager_id);} },
						{ title: (language=="en" ? "Download Link":"لینک دانلود"), icon: 'far fa-link', fn: function(){downloadFilemanagerItem($filemanager_id);} }
					];
				}else if($($target).hasClass("its_folder")){
					var items = [
						{ title: (language=="en" ? "Open":"باز شود"), icon: 'far fa-folder-open', fn: function(){openFolderFileManager($(".menu_file_manager_folder_"+$filemanager_id).children("a"),$filemanager_id);} },
						{ title: (language=="en" ? "Cut":"بریدن"), icon: 'fas fa-cut', fn: function(){cutFilemanagerItem($filemanager_id);}, disabled: ($filemanager_id<0 ? true:false) },
						{ title: (language=="en" ? "Copy":"کپی کردن"), icon: 'fas fa-copy', fn: function(){copyFilemanagerItem($filemanager_id);}, disabled: ($filemanager_id<0 ? true:false) },
						{ title: (language=="en" ? "Delete":"حذف"), icon: 'far fa-times', fn: function(){deleteFilemanagerItem($filemanager_id);}, disabled: ($filemanager_id<0 ? true:false) },
						{ title: (language=="en" ? "Rename":"تغیر نام"), icon: 'far fa-pen', fn: function(){renameFilemanagerItem($filemanager_id);}, disabled: ($filemanager_id<0 ? true:false) }
					];
				}
			}else{
				if($current_folder>=0){
					var items = [
						{ title: (language=="en" ? "Add Folder":"افزودن پوشه"), icon: 'fas fa-plus', fn: function(){createFolder()} },
						{ title: (language=="en" ? "Refresh":"بازسازی"), icon: 'fas fa-sync-alt', fn: function () {openFolderFileManager($(".menu_file_manager_folder_"+$current_folder).children("a"),$current_folder,1)} },
						{ title: (language=="en" ? "Paste":"چسباندن"), icon: 'fas fa-paste', fn: function(){pasteFilemanageItem();}, disabled: ($current_folder>=0 ? ($file_manager_cut ? ($file_manager_cut!=$current_folder ? false:true):($file_manager_copy ? false:true)):true) }
					];
				}else{
					var items = [
						{ title: (language=="en" ? "Refresh":"بازسازی"), icon: 'fas fa-sync-alt', fn: function () {openFolderFileManager($(".menu_file_manager_folder_"+$current_folder).children("a"),$current_folder,1)} }
					];

				}
				$(".ui-selected").removeClass("ui-selected");
			}
			basicContext.show(items, e);
		}
		$(document).on("contextmenu",".basicContextContainer",function (e) {
			if($(e.target).hasClass("basicContextContainer")){
				e.preventDefault();
				$(".basicContextContainer").remove();
				customRightClick(e,document.elementFromPoint(e.pageX, e.pageY));
			}else{
				e.preventDefault();
			}
		});
		$(document).on("contextmenu",".context_menu",function (e) {
			customRightClick(e,e.target);
		});

		function makeItSelectAble($element) {
			$( $element ).selectable({
				filter: "*:not(table, thead, thead th, thead th i, tbody, th, td)",
				distance: 1,
				selecting: function(e, ui) {
					if($(ui.selecting).hasClass("selectable_item")){
						if (e.altKey){
							// $(ui.selecting).addClass("ui-deselect_order");
						}
					}
				},
				selected: function() {
					$.when($( ".ui-selected", this ).each(function() {
						if($(this).hasClass("selectable_item")!=true){$(this).removeClass("ui-selected");}else{if($(this).hasClass("ui-deselect_order")){$(this).removeClass("ui-selected");}}})).done(function(){
						$(".ui-deselect_order").removeClass("ui-deselect_order");
					});
				}
			});
			var isDragging = false;
			$($element).mousedown(function(e) {
				isDragging = false;
			}).mousemove(function(e) {
				isDragging = true;
			}).mouseup(function(e) {
				var wasDragging = isDragging;
				isDragging = false;
				if (!wasDragging) {
					if($(e.target).hasClass("its_file") || $(e.target).hasClass("its_folder")){
						var $this=$(e.target);
						if($this.hasClass("go_pr")){
							if($this.hasClass("go_pr_2")){
								$this=$(e.target).parent().parent();
							}else if($this.hasClass("go_pr_1")){
								$this=$(e.target).parent();
							}
						}
						if(e.ctrlKey){
							$this.addClass("ui-selected");
						}else if (e.altKey){
							$this.removeClass("ui-selected");
						}else{
							$(".ui-selected").removeClass("ui-selected");
							$this.addClass("ui-selected");
						}
					}else{
						if (e.ctrlKey==false && e.altKey==false){
							$(".ui-selected").removeClass("ui-selected");
						}
					}
				}
			});
		}
		// makeItSelectAble(".selectable_area"); //asd

		$(document).on("keypress",".file_manager_search_input",function (e) {
			if(e.which == 13) {
				if($('.file_manager_search_input').val().length){
					var $val=$('.file_manager_search_input').val();
					newUserSetting("file_manager_search",$val);
					if($current_folder!=-3){
						$(".all_folders").fadeOut();
					}
					$("#folder_-3").fadeOut(300);
					setTimeout(() => {
						$("#folder_-3").remove();
						openFolderFileManager($("empty").children("a"),-3,0,1,1);
					}, 300);
				}else{
					openFolderFileManager($(".menu_file_manager_folder_"+0).children("a"),0);
				}
			}
		});

		$(document).on("dblclick",".its_folder",function (e) {
			if(!e.ctrlKey && !e.altKey){
				var $this=$(e.target);
				if($this.hasClass("go_pr")){
					if($this.hasClass("go_pr_2")){
						$this=$(e.target).parent().parent();
					}else if($this.hasClass("go_pr_1")){
						$this=$(e.target).parent();
					}
				}
				var $fileIDs="";
				$this.attr("class").split(" ").forEach(function(class_name){
					$class_name=class_name.split("-");
					if($class_name[0]=="file_manager_id"){
						$fileIDs=$class_name[1].replace("_","-");
					}
				});
				$(".ui-selected").removeClass("ui-selected");
				var $this_a=$(".menu_file_manager_folder_"+$fileIDs).children("a");
				openFolderFileManager($this_a,$fileIDs);
			}
		});

		function refreshFileManagerMenu($force=0,$not=0,$mode=0) {
			$.post("file_manager/class/action.php?countFolder",{},function(datas,statuss){
				if (statuss == "success" && datas.toString().indexOf("success")!=-1) {
					$data=datas.replace("_-...-_success","").replace("success","");
					if($data!=$countFolderFileManager || $force){
						$countFolderFileManager=$data;
						$.when($("#jquery-accordion-menu").remove()).done(function(){
							$.when($(".menu_place").prepend('<div id="jquery-accordion-menu" class="jquery-accordion-menu col-12 col-sm-6 col-md-5 col-lg-4 col-xl-3"></div>')).done(function(){
								$("#jquery-accordion-menu").load("file_manager/class/action.php?fileManagerMenu",function (data,status) {
									if (status == "success" && data.toString().indexOf("success")!=-1) {
										$("#jquery-accordion-menu").find(".submenu").siblings("a").each(function () {
											if($(this).parent().hasClass("files_nf")!=true){
												$(this).append("<span class='submenu-indicator'>+</span>");
											}
										});
										$("#jquery-accordion-menu").jqueryAccordionMenu();
										if($mode!=0){
											switch ($mode) {
												case "slideDown":
													$("#jquery-accordion-menu li:not(.files_nf) ul.submenu").each(function () {
														if($(this).css("display")=="none"){
															$(this).slideDown(300);
														}
													});
													$("#jquery-accordion-menu").find("li:not(.files_nf) a").addClass("submenu-indicator-minus");
													if($("#jquery-accordion-menu").find("li:not(.files_nf) i").length){
														if($("#jquery-accordion-menu").find("li:not(.files_nf) i").attr("class").indexOf("fa-folder")!=-1){
															$("#jquery-accordion-menu").find("li:not(.files_nf) i").addClass("fa-folder-open").removeClass("fa-folder");
														}
													}
												break;
												case "slideUp":
													$("#jquery-accordion-menu li:not(.files_nf) ul.submenu").each(function () {
														if($(this).css("display")!="none"){
															$(this).slideUp(300);
														}
													});
													$("#jquery-accordion-menu").find("li:not(.files_nf) a").removeClass("submenu-indicator-minus");
													if($("#jquery-accordion-menu").find("li:not(.files_nf) i").length){
														if($("#jquery-accordion-menu").find("li:not(.files_nf) i").attr("class").indexOf("fa-folder")!=-1){
															$("#jquery-accordion-menu").find("li:not(.files_nf) i").addClass("fa-folder").removeClass("fa-folder-open");
														}
													}
												break;
											}
										}
										if(!$not){
											openFolderFileManager($(".menu_file_manager_folder_"+$current_folder).children("a"),$current_folder,1);
										}
									}else{
										feedbackOperations(data);
									}
								});
							});
						});
					}else{
						if($mode!=0){
							switch ($mode) {
								case "slideDown":
									$("#jquery-accordion-menu").find("li:not(.files_nf) ul.submenu").each(function () {
										if($(this).css("display")=="none"){
											$(this).slideDown(300);
										}
									});
									$("#jquery-accordion-menu").find("li:not(.files_nf) a").addClass("submenu-indicator-minus");
									if($("#jquery-accordion-menu").find("li:not(.files_nf) i").length){
										if($("#jquery-accordion-menu").find("li:not(.files_nf) i").attr("class").indexOf("fa-folder")!=-1){
											$("#jquery-accordion-menu").find("li:not(.files_nf) i").addClass("fa-folder-open").removeClass("fa-folder");
										}
									}
								break;
								case "slideUp":
									$("#jquery-accordion-menu").find("li:not(.files_nf) ul.submenu").each(function () {
										if($(this).css("display")!="none"){
											$(this).slideUp(300);
										}
									});
									$("#jquery-accordion-menu").find("li:not(.files_nf) a").removeClass("submenu-indicator-minus");
									if($("#jquery-accordion-menu").find("li:not(.files_nf) i").length){
										if($("#jquery-accordion-menu").find("li:not(.files_nf) i").attr("class").indexOf("fa-folder")!=-1){
											$("#jquery-accordion-menu").find("li:not(.files_nf) i").addClass("fa-folder").removeClass("fa-folder-open");
										}
									}
								break;
							}
						}
					}
				}else{
					feedbackOperations(datas);
				}
			});
		}

		function switchFileView($how) {
			switch ($how) {
				case "list":
					if($list_type_mode!="list"){
						newUserSetting("file_manager_mode",'list');
						$(".all_folders.hide").remove();
						$list_type_mode="list";
						$(".list_type_item").removeClass("btn-primary").attr("disabled",false);
						$(".list_type_list").addClass("btn-primary").attr("disabled",true);
						var $files_table="";
						$.when($(".selectable_area:not(.hide) div.selectable_item").each(function(){
							var $fileIDs="",$this=$(this);
							$this.attr("class").split(" ").forEach(function(class_name){
								$class_name=class_name.split("-");
								if($class_name[0]=="file_manager_id"){
									$fileIDs=$class_name[1];
								}
							});
							var $icon=$($(this).children("div")[0]).children("i").attr("class"),$its_folder_file=($icon.indexOf("its_folder")>=0 ? "its_folder":"its_file"),$is_selected=($(this).attr("class").indexOf("ui-selected")!=-1 ? "ui-selected":"ui-selectee");
							$files_table+='<tr class="selectable_item '+$its_folder_file+' file_manager_id-'+$fileIDs+' '+$is_selected+'"><th class="'+$its_folder_file+' go_pr_1 go_pr" scope="row"><i class="'+$icon+'"></i> '+$($(this).children("div")[0]).children("p").html()+'</th><td class="'+$its_folder_file+' go_pr_1 go_pr">'+$(this).children("div.more_tools").children("div.file_size").html()+'</td><td class="'+$its_folder_file+' go_pr_1 go_pr '+(language=="en" ? "text-right":"text-left")+'">'+$(this).children("div.more_tools").children("div.last_modify").html()+'</td></tr>';
						})).done(function(){
							$(".selectable_area:not(.hide)").fadeOut(300,function(){
								$(".selectable_area:not(.hide) div.selectable_item").remove();
								$(".selectable_area:not(.hide)").empty();
								$(".selectable_area:not(.hide)").append('<table class="table table-striped table_list_file_manager"><thead><tr><th scope="col" class="data-text" data-text-en="<i class=\'far fa-file\'></i> Name" data-text-fa="<i class=\'far fa-file\'></i> نام"><i class=\'far fa-file\'></i> '+(language=="en" ? "Name":"نام")+'</th><th scope="col" class="data-text" data-text-en="Size" data-text-fa="حجم فایل">'+(language=="en" ? "Size":"حجم فایل")+'</th><th class="'+(language=="en" ? "text-right":"text-left")+' data-text" scope="col" data-text-en="Last Modified" data-text-fa="آخرین بروزرسانی">'+(language=="en" ? "Last Modified":"آخرین بروزرسانی")+'</th></tr></thead><tbody class="files_table"></tbody></table>').hide();
								$(".selectable_area:not(.hide) .files_table").append($files_table);
								$(".selectable_area:not(.hide)").fadeIn(300);
							});
						});
					}
				break;
				case "item":
					if($list_type_mode!="item"){
						newUserSetting("file_manager_mode",'item');
						$(".all_folders.hide").remove();
						$list_type_mode="item";
						$(".list_type_item").addClass("btn-primary").attr("disabled",true);
						$(".list_type_list").removeClass("btn-primary").attr("disabled",false);
						$(".selectable_area:not(.hide)").fadeOut(300,function(){
							$(".selectable_area:not(.hide) .files_table").children("tr").each(function(){
								var $fileIDs="",$this=$(this);
								$this.attr("class").split(" ").forEach(function(class_name){
									$class_name=class_name.split("-");
									if($class_name[0]=="file_manager_id"){
										$fileIDs=$class_name[1];
									}
								});
								if($(this).children("td").length>=2){
									var $file_size=$($(this).children("td")[0]).html(),$last_modify=$($(this).children("td")[1]).html();
								}else{
									var $file_size="unknow",$last_modify="unknow";
								}
								var $icon=$(this).children("th").children("i").attr("class"),$its_folder_file=(typeof $icon !== "undefined" ? ($icon.indexOf("its_folder")>=0 ? "its_folder":"its_file"):""),$is_selected=($(this).attr("class").indexOf("ui-selected")!=-1 ? "ui-selected":"ui-selectee");
								$(this).children("th").children("i").remove();
								var $name=$(this).children("th").html();
								$(this).remove();
								$(".selectable_area:not(.hide)").append('<div class="selectable_item '+$its_folder_file+' font-icon-list col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 file_manager_id-'+$fileIDs+' '+$is_selected+'"><div class="'+$its_folder_file+' font-icon-detail go_pr_1 go_pr"><i class="'+$icon+'"></i><p class="'+$its_folder_file+' go_pr_2 go_pr">'+$name+'</p></div><div class="more_tools hide"><div class="file_size">'+$file_size+'</div><div class="last_modify">'+$last_modify+'</div></div></div>');
							});
							$.when($(".selectable_area:not(.hide) .table_list_file_manager").remove()).done(function(){
								$(".selectable_area:not(.hide)").fadeIn(300);
							});
						});
					}
				break;
			}
		}

		function getBackFileManager() {
			if(!$(".file_manager").find(".fa-spin:not(.not-spin)").length){
				if(typeof $goBackFileManager[$goBackFileManager.length-2] !== "undefined"){
					$goNextFileManager.push($goBackFileManager.pop());
					var $whereToGo=$goBackFileManager[$goBackFileManager.length-1];
					openFolderFileManager($(".menu_file_manager_folder_"+$whereToGo).children("a"),$whereToGo,0,1);
					if($goBackFileManager.length<=1){
						$(".go-back-filemanager").attr("disabled",true);
					}
					$(".go-next-filemanager").attr("disabled",false);
					if($whereToGo!=0){
						$(".jump-up-filemanager").attr("disabled",false);
					}else{
						$(".jump-up-filemanager").attr("disabled",true);
					}
				}
			}else{
				$(".go-back-filemanager").children("i").removeClass((language=="en" ? "fa-long-arrow-left":"fa-long-arrow-right")).addClass("fa-spin fa-spinner-third not-spin");
				$goBackInterval=setInterval(() => {
					if(!$(".file_manager").find(".fa-spin:not(.not-spin)").length){
						$(".go-back-filemanager").children("i").addClass((language=="en" ? "fa-long-arrow-left":"fa-long-arrow-right")).removeClass("fa-spin fa-spinner-third not-spin");
						clearInterval($goBackInterval);
					}
				}, 500);
			}
		}
		function getNextFileManager() {
			if(!$(".file_manager").find(".fa-spin:not(.not-spin)").length){
				if(typeof $goNextFileManager[$goNextFileManager.length-1] !== "undefined"){
					var $whereToGo=$goNextFileManager.pop();
					openFolderFileManager($(".menu_file_manager_folder_"+$whereToGo).children("a"),$whereToGo,0,1);
					$goBackFileManager.push($whereToGo);
					if(!$goNextFileManager.length){
						$(".go-next-filemanager").attr("disabled",true);
					}
					if($goBackFileManager.length>1){
						$(".go-back-filemanager").attr("disabled",false);
					}
					if($whereToGo!=0){
						$(".jump-up-filemanager").attr("disabled",false);
					}else{
						$(".jump-up-filemanager").attr("disabled",true);
					}
				}
			}else{
				$(".go-next-filemanager").children("i").removeClass((language=="en" ? "fa-long-arrow-right":"fa-long-arrow-left")).addClass("fa-spin fa-spinner-third not-spin");
				$goNextInterval=setInterval(() => {
					if(!$(".file_manager").find(".fa-spin:not(.not-spin)").length){
						$(".go-next-filemanager").children("i").addClass((language=="en" ? "fa-long-arrow-right":"fa-long-arrow-left")).removeClass("fa-spin fa-spinner-third not-spin");
						clearInterval($goNextInterval);
					}
				}, 500);
			}
		}
		function getUpFileManager() {
			if(!$(".file_manager").find(".fa-spin:not(.not-spin)").length){
				$.post("file_manager/class/action.php?jump-up",{"folder_id":$current_folder},function(data,status){
					if (status == "success" && data.toString().indexOf("success")!=-1) {
						$whereToGo=data.replace("_-...-_success","").replace("success","");
						openFolderFileManager($(".menu_file_manager_folder_"+$whereToGo).children("a"),$whereToGo);
						$(".go-next-filemanager").attr("disabled",true);
						if($goBackFileManager.length>1){
							$(".go-back-filemanager").attr("disabled",false);
						}
						if($whereToGo!=0){
							$(".jump-up-filemanager").attr("disabled",false);
						}else{
							$(".jump-up-filemanager").attr("disabled",true);
						}
					}else{
						feedbackOperations(data);
					}
				});
			}else{
				$(".jump-up-filemanager").children("i").removeClass((language=="en" ? "fa-long-arrow-right":"fa-long-arrow-left")).addClass("fa-spin fa-spinner-third not-spin");
				$goUpInterval=setInterval(() => {
					if(!$(".file_manager").find(".fa-spin:not(.not-spin)").length){
						$(".jump-up-filemanager").children("i").addClass((language=="en" ? "fa-long-arrow-right":"fa-long-arrow-left")).removeClass("fa-spin fa-spinner-third not-spin");
						clearInterval($goUpInterval);
					}
				}, 500);
			}
		}

		function createFolder() {
			if($current_folder>=0){
				Swal.fire({
					title: (language=="en" ? "Create your new folder in current folder":"افزودن پوشه جدید در پوشه فعلی"),
					input: 'text',
					inputAttributes: {
						autocapitalize: 'off'
					},
					showCancelButton: true,
					confirmButtonText: (language=="en" ? "Create":"افزودن"),
					cancelButtonText: (language=="en" ? "Cancel":"لغو"),
					customClass: {
						confirmButton: 'btn btn-info',
						cancelButton: 'btn btn-warning'
					},
					buttonsStyling: false,
					showLoaderOnConfirm: true,
					preConfirm: (name) => {
						if(name.length){
							return new Promise(function(resolve) {
								$.post("file_manager/class/action.php?create_folder",{"folder_id":$current_folder,"name":name},function(data,status){
									if (status == "success" && data.toString().indexOf("success")!=-1) {
										$data=data.replace("_-...-_success","").replace("success","");
										if($data==""){
											openFolderFileManager($(".menu_file_manager_folder_"+$current_folder).children("a"),$current_folder,1);
											Swal.fire({
												position: 'center',
												icon: 'success',
												title: (language=="en" ? "Created !":"افزوده شد !"),
												showConfirmButton: false,
												timer: 1500
											});
										}else if($data=="exist"){
											Swal.showValidationMessage('Folder with this name already exist!');
											Swal.hideLoading();
										}else{
											Swal.showValidationMessage('Something went wrong!');
											Swal.hideLoading();
											feedbackOperations(data);
										}
									}else{
										Swal.showValidationMessage('Something went wrong!');
										Swal.hideLoading();
										feedbackOperations(data);
									}
								});
							});
						}else{
							Swal.showValidationMessage((language=="en" ? "Please fill the new folder name !":"لطفا نامه پوشه جدید را وارد نمایید !"));
						}
					},
					allowOutsideClick: () => !Swal.isLoading()
				}).then((result) => {
					if(result.value=="success"){
						Swal.fire({
							position: 'center',
							icon: 'success',
							title: 'Created!',
							showConfirmButton: false,
							timer: 1500
						});
					}
				});
			}else{
				Swal.fire({
					icon: 'error',
					title: (language=="en" ? "Warning":"هشدار"),
					text: (language=="en" ? "Creating folder in this folder is not allowed !":"افزودن پوشه در این پوشه مجاز نیست !"),
					showConfirmButton: false,
					timer: 2000
				});
			}
		}

		function renameFilemanagerItem($file_id) {
			if($file_id>0){
				var $yet_name;
				$.post("file_manager/class/action.php?getName",{'file_id':$file_id},function(data,status){
					if (status == "success" && data.toString().indexOf("succes")!=-1) {
						$data=data.replace("_-...-_success","").replace("success","");
						$yet_name=$data;
						Swal.fire({
							title: (language=="en" ? "Renaming selected item":"تغییر نام مورد انتخاب شده"),
							input: 'text',
							inputValue: $yet_name,
							inputAttributes: {
								autocapitalize: 'off'
							},
							showCancelButton: true,
							confirmButtonText: (language=="en" ? "Save":"ذخیره"),
							cancelButtonText: (language=="en" ? "Cancel":"لغو"),
							customClass: {
								confirmButton: 'btn btn-info',
								cancelButton: 'btn btn-warning'
							},
							buttonsStyling: false,
							showLoaderOnConfirm: true,
							preConfirm: (name) => {
								if(name.length){
									return new Promise(function(resolve) {
										$.post("file_manager/class/action.php?rename_file",{"file_id":$file_id,"name":name},function(data,status){
											if (status == "success" && data.toString().indexOf("success")!=-1) {
												$data=data.replace("_-...-_success","").replace("success","");
												if($data==""){
													openFolderFileManager($(".menu_file_manager_folder_"+$current_folder).children("a"),$current_folder,1);
													Swal.fire({
														position: 'center',
														icon: 'success',
														title: (language=="en" ? "Saved !":"ذخیره شد !"),
														showConfirmButton: false,
														timer: 1500
													});
												}else if($data=="exist"){
													Swal.showValidationMessage('Folder with this name already exist!');
													Swal.hideLoading();
												}else{
													Swal.showValidationMessage('Something went wrong!');
													Swal.hideLoading();
													feedbackOperations(data);
												}
											}else{
												Swal.showValidationMessage('Something went wrong!');
												Swal.hideLoading();
												feedbackOperations(data);
											}
										});
									});
								}else{
									Swal.showValidationMessage((language=="en" ? "Please fill the new folder name !":"لطفا نامه پوشه جدید را وارد نمایید !"));
								}
							},
							allowOutsideClick: () => !Swal.isLoading()
						}).then((result) => {
							if(result.value=="success"){
								Swal.fire({
									position: 'center',
									icon: 'success',
									title: 'Created!',
									showConfirmButton: false,
									timer: 1500
								});
							}
						});
					}else{
						if(data=="notfound"){
							openFolderFileManager($(".menu_file_manager_folder_"+$current_folder).children("a"),$current_folder);
							Swal.fire({
								icon: 'error',
								title: (language=="en" ? "Warning":"هشدار"),
								text: (language=="en" ? "Item not found !":"این مورد یافت نشد !"),
								showConfirmButton: false,
								timer: 2000
							});
						}else{
							feedbackOperations(data);
						}
					}
				});
			}else{
				Swal.fire({
					icon: 'error',
					title: (language=="en" ? "Warning":"هشدار"),
					text: (language=="en" ? "This is not allowed !":"این مجاز نیست !"),
					showConfirmButton: false,
					timer: 2000
				});
			}
		}

		function downloadFilemanagerItem($file_id){
			// <a href='"+item.download_link+"' target='_blank' class='btn btn-link "+(item.is_secure==1 ? "btn-warning":"btn-info")+" btn-sm col-12'><i class='fas "+(item.is_secure==1 ? "fa-lock":"fa-globe")+" ml-1 mr-1'></i>" + item.real_name.substr(0, 25) + (item.real_name.length>25 ? " ...":"") + "<i class='fas fa-external-link ml-1 mr-1'></i></a>
			$.post("file_manager/class/action.php?getDownload",{"file_id":$file_id},function(data,status){
				if (status == "success" && data.toString().indexOf("success")!=-1) {
					$data=data.replace("_-...-_success","").replace("success","");
					$data=JSON.parse($data);
					var $download_link="",$name_link="",$force_download_link="";
					if(typeof $data['name'] !== "undefined"){
						$name_link=$data['name'].substr(0, 25) + ($data['name'].length>25 ? " ...":"");
					}
					if(typeof $data['public'] !== "undefined"){
						$download_link=$data['public'];
						$download_link='<a href="'+$download_link+'" target="_blank" class="btn btn-link btn-info btn-sm col-12"><i class="fas fa-globe ml-1 mr-1"></i>'+$name_link+'<i class="fas fa-external-link ml-1 mr-1"></i></a>';
					}
					if(typeof $data['force'] !== "undefined"){
						$force_download_link=$data['force'];
						$force_download_link='<a href="'+$force_download_link+'" target="_blank" class="btn btn-link btn-success btn-sm col-12"><i class="fas fa-download ml-1 mr-1"></i>'+$name_link+'<i class="fas fa-external-link ml-1 mr-1"></i></a>';
					}
					Swal.fire({
						title: (language=="en" ? "Download link":"لینک دانلود"),
						html: '<div id="swal2-content" class="swal2-html-container" style="display: block;"></div>'+
								'<div id="swal2-content-files">'+
									'<div class="swal2-content-file">'+
										$force_download_link +
										$download_link +
										'<a href="files.php?download='+$file_id+'" target="_blank" class="btn btn-link btn-warning btn-sm col-12"><i class="fas fa-lock ml-1 mr-1"></i>'+$name_link+'<i class="fas fa-external-link ml-1 mr-1"></i></a>'+
									'</div>'+
								'</div>'+
							'</div>',
						showCancelButton: false,
						confirmButtonText: (language=="en" ? "Close":"خروج")
					});
				}else{
					feedbackOperations(data);
				}
			});
		}

		function deleteFilemanagerItem($file_id){
			if($file_id>0){
				function deleteSelectedFile_FileManager($file_id){
					$.post("file_manager/class/action.php?delete",{"file_id":$file_id},function(data,status){
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							$data=data.replace("_-...-_success","").replace("success","");
							if($data==""){
								openFolderFileManager($(".menu_file_manager_folder_"+$current_folder).children("a"),$current_folder,1);
								if(alertShowerSetting['deleteFileManager_showAlert']=="false"){
									Swal.fire({
										position: 'center',
										icon: 'info',
										title: (language=="en" ? "Deleted !":"حذف شد !"),
										showConfirmButton: false,
										timer: 1500
									});
								}
							}else{
								Swal.showValidationMessage('Something went wrong!');
								Swal.hideLoading();
								feedbackOperations(data);
							}
						}else{
							Swal.showValidationMessage('Something went wrong!');
							Swal.hideLoading();
							feedbackOperations(data);
						}
					});
				}
				try {
					if(alertShowerSetting['deleteFileManager_showAlert']=="true"){
						deleteSelectedFile_FileManager($file_id);
					}else{
						if(language=="fa"){
							Swal.fire({
								title: 'آیا مطمئن  هستید؟',
								text: "با حذف این مورد دیگر نمیتوانید آن را برگردانید !",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'بله',
								cancelButtonText: 'لغو',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteFileManager_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
							}).then((result) => {
								if(result.value) {
									deleteSelectedFile_FileManager($file_id);
								}
							});
						}else{
							Swal.fire({
								title: 'Are you sure?',
								text: "By deleting this item you can't return it back !",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'Yes',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteFileManager_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
							}).then((result) => {
								if(result.value) {
									deleteSelectedFile_FileManager($file_id);
								}
							});
						}
					}
				} catch(err) {
					alertShowerSetting['deleteFileManager_showAlert']="false";
					deleteFilemanagerItem();
				}
			}else{
				Swal.fire({
					icon: 'error',
					title: (language=="en" ? "Warning":"هشدار"),
					text: (language=="en" ? "This is not allowed !":"این مجاز نیست !"),
					showConfirmButton: false,
					timer: 2000
				});
			}
		}

		function cutFilemanagerItem($file_id){
			$file_manager_cut=$file_id;
			$file_manager_copy=0;
			newUserSetting("file_manager_cut",$file_id);
			newUserSetting("file_manager_copy",0);
		}

		function copyFilemanagerItem($file_id){
			$file_manager_copy=$file_id;
			$file_manager_cut=0;
			newUserSetting("file_manager_copy",$file_id);
			newUserSetting("file_manager_cut",0);
		}

		function pasteFilemanageItem(){
			if($current_folder>=0 ? ($file_manager_cut ? ($file_manager_cut!=$current_folder ? true:false):($file_manager_copy ? true:false)):false){
				var $file_id=($file_manager_cut ? ($file_manager_cut!=$current_folder ? true:false):($file_manager_copy ? true:false));
				Swal.fire({
					title: (language=="en" ? "Pasting ...":"چسباندن ..."),
					didOpen: () => {
						Swal.showLoading();
					},
					showConfirmButton: false,
					allowOutsideClick: false,
				});
				$.post("file_manager/class/action.php?paste",{"current_folder":$current_folder},function(data,status){
					if (status == "success" && data.toString().indexOf("success")!=-1) {
						$data=data.replace("_-...-_success","").replace("success","");
						if($data==""){
							$file_manager_copy=0;
							newUserSetting("file_manager_copy",0);
							$file_manager_cut=0;
							newUserSetting("file_manager_cut",0);
							openFolderFileManager($(".menu_file_manager_folder_"+$current_folder).children("a"),$current_folder,1);
							refreshFileManagerMenu(1);
							swal.close();
						}else if($data=="notallowed"){
							Swal.fire({
								icon: 'error',
								title: (language=="en" ? "Warning":"هشدار"),
								text: (language=="en" ? "This is not allowed !":"این مجاز نیست !"),
								showConfirmButton: false,
								timer: 2000
							});
						}
					}else{
						feedbackOperations(data);
					}
				});
			}else{
				Swal.fire({
					icon: 'error',
					title: (language=="en" ? "Warning":"هشدار"),
					text: (language=="en" ? "This is not allowed !":"این مجاز نیست !"),
					showConfirmButton: false,
					timer: 2000
				});
			}
		}

		function openFolderFileManager($menu,$folder_id,$no_refresh=0,$no_save=0,$searching=0) {
			if($folder_id!=-3){
				$(".file_manager_search_input").val("");
				newUserSetting("file_manager_search","");
			}else{
				if($searching==0){
					$("#folder_-3").fadeOut(300);
					setTimeout(() => {
						$("#folder_-3").remove();
					}, 300);
					refreshFileManagerMenu();
				}
			}
			$current_folder=$folder_id;
			if($no_refresh==0){
				refreshFileManagerMenu(0,1);
			}
			if(!$(".file_manager").find(".fa-spin:not(.not-spin)").length){
				if(typeof $menu.children("i").attr("class") !=="undefined"){
					var $current_i_class=($menu.children("i").attr("class").indexOf("fa-folder")!=-1 ? $menu.children("i").attr("class").replace("fa-folder-open", "fas-folder-open").replace("fa-folder","fa-folder-open").replace("fas-folder-open", "fa-folder-open"):$menu.children("i").attr("class"));
					$menu.children("i").removeAttr("class").addClass("fad fa-spin fa-spinner-third");
					$("#jquery-accordion-menu").find(".active").removeClass("active");
				}else{
					var $current_i_class="";
				}
				$(".all_folders:not(#folder_"+$folder_id+")").fadeOut(300);
				setTimeout(() => {
					$.when($(".all_folders:not(#folder_"+$folder_id+")").addClass("hide")).done(function(){
						$menu.parent("li").addClass("active");
						newUserSetting("file_manager_direction",$folder_id);
						if(typeof $menu.children("i").attr("class") !=="undefined"){
							var $input_direction="";
							function fixFileManagerMenu($menu,$first=0) {
								if($current_folder==-3){
									$(".file_manager_custom_input_address").val((language=="en" ? "Files":"فایل ها")+"/"+(language=="en" ? "Searching":"در حال جست و جو"));
								}else{
									if($menu.prop("tagName")=="A"){
										var $removed_indicator=$menu.children(".submenu-indicator");
										$menu.children(".submenu-indicator").remove();
										var $text_address=$menu.text().replace(/(<([^>]+)>)/gi, "").trim();
										$input_direction=$text_address+"/"+$input_direction;
										$menu.addClass("submenu-indicator-minus");
										$menu.append($removed_indicator);
									}else if($menu.prop("tagName")=="UL"){
										$menu.slideDown(300);
									}else if($menu.prop("tagName")=="LI"){
										if($first!=0){
											var $removed_indicator=$menu.children("a").children(".submenu-indicator");
											$menu.children("a").children(".submenu-indicator").remove();
											var $text_address=$menu.children("a").text().replace(/(<([^>]+)>)/gi, "").trim();
											$menu.children("a").addClass("submenu-indicator-minus");
											$input_direction=$text_address+"/"+$input_direction;
											$menu.children("a").append($removed_indicator);
										}
										$menu.children("ul.submenu").slideDown(300);
									}
									if($menu.hasClass("files_nf")!=true){
										fixFileManagerMenu($menu.parent(),$first-1);
									}else{
										if($input_direction!=(language=="en" ? "Files":"فایل ها")+"/"){
											$(".file_manager_custom_input_address").val((language=="en" ? "Files":"فایل ها")+"/"+$input_direction);
										}else{
											$(".file_manager_custom_input_address").val((language=="en" ? "Files":"فایل ها")+"/");
										}
									}
								}
							}
							fixFileManagerMenu($menu,1);
						}
						if($("#folder_"+$folder_id).length){
							if(typeof $data_sync[$folder_id] !== "undefined"){
								var $data="",$not_sync=0;
								$.post("file_manager/class/action.php?getDataInfo",{"folder_id":$folder_id},function(datas,statuss){
									if (statuss == "success" && datas.toString().indexOf("success")!=-1) {
										$data=datas.replace("_-...-_success","").replace("success","");
									}else{
										feedbackOperations(datas);
									}
								}).always(function(){
									$.when($data=($data!="" ? $data.split("_-...-_"):[])).done(function(){
										if(typeof $data_sync[$folder_id] !== "undefined"){
											$.when($data.forEach(function(this_arr){
												if($data_sync[$folder_id].indexOf(this_arr)==-1){
													$not_sync=1;
												}
											}),$data_sync[$folder_id].forEach(function(this_arr){
												if($data.indexOf(this_arr)==-1 && $data.indexOf(this_arr.toString())==-1){
													$not_sync=1;
												}
											})).done(function(){
												if($not_sync==0 || $data_sync[$folder_id].length==0 && $data.length==0){
													if($("#folder_"+$folder_id).hasClass("hide")){
														$.when($("#folder_"+$folder_id).removeClass("hide").fadeOut(0,function(){$(this).fadeIn(300);})).done(function(){
															if($current_i_class.length){
																$menu.children("i").removeAttr("class").addClass($current_i_class);
															}
															if($folder_id!=0){
																$(".jump-up-filemanager").attr("disabled",false);
															}else{
																$(".jump-up-filemanager").attr("disabled",true);
															}
															if($goBackFileManager[$goBackFileManager.length-1]!=$folder_id && $no_save==0){
																$(".go-back-filemanager").attr("disabled",false);
																$goBackFileManager.push($folder_id);
																$goNextFileManager=[];
																$(".go-next-filemanager").attr("disabled",true);
															}
														});
													}else{
														if($current_i_class.length){
															$menu.children("i").removeAttr("class").addClass($current_i_class);
														}
													}
												}else{
													$.when($current_i_class.length ? $menu.children("i").removeAttr("class").addClass($current_i_class):1).done(function(){
														$data_sync[$folder_id]=[];delete $data_sync[$folder_id];
														$("#folder_"+$folder_id).fadeOut(300,function (){
															$.when($(this).remove()).done(function(){
																openFolderFileManager($menu,$folder_id);
															});
														});
													});
												}
											});
										}else{
											//# error 1
											Swal.fire({
												icon: 'error',
												title: (language=="en" ? "Something went wrong ...":"مشکلی رخ داده است ..."),
												text: "error code 1"
											});
											//# error 1
										}
									});
								});
							}else{
								if($current_i_class.length){
									$menu.children("i").removeAttr("class").addClass($current_i_class.replace("fa-folder-open","fa-folder"));
								}
								$("#folder_"+$folder_id).fadeOut(300,function (){
									$.when($(this).remove()).done(function(){
										openFolderFileManager($menu,$folder_id);
									});
								});
							}
						}else{
							var $if_is_mode_is_table=($list_type_mode=="list" ? '<table class="table table-striped table_list_file_manager"><thead><tr><th scope="col" class="data-text" data-text-en="<i class=\'far fa-file\'></i> Name" data-text-fa="<i class=\'far fa-file\'></i> نام"><i class=\'far fa-file\'></i> '+(language=="en" ? "Name":"نام")+'</th><th scope="col" class="data-text" data-text-en="Size" data-text-fa="حجم فایل">'+(language=="en" ? "Size":"حجم فایل")+'</th><th class="'+(language=="en" ? "text-right":"text-left")+' data-text" scope="col" data-text-en="Last Modified" data-text-fa="آخرین بروزرسانی">'+(language=="en" ? "Last Modified":"آخرین بروزرسانی")+'</th></tr></thead><tbody class="files_table"></tbody></table>':"");
							var $whereToLoad=($list_type_mode=="list" ? "#folder_"+$folder_id+" .files_table":"#folder_"+$folder_id);
							$(".folders_loader").append('<div id="folder_'+$folder_id+'" class="row context_menu selectable_area pb-5 all_folders hide">'+$if_is_mode_is_table+'</div>');
							// makeItSelectAble('#folder_'+$folder_id); //asd
							$.post("file_manager/class/action.php?getDataInfo",{"folder_id":$folder_id},function(datas,statuss){
								if (statuss == "success" && datas.toString().indexOf("success")!=-1) {
									var $data=datas.replace("_-...-_success","").replace("success",""),$data_split=($data!="" ? $data.split("_-...-_"):[]);
									$data_sync[$folder_id]=$data_split;
								}else{
									feedbackOperations(datas);
								}
							}).always(function() {
								$($whereToLoad).load("file_manager/class/action.php?load",{"folder_id":$folder_id},function (data,status) {
									if (status == "success" && data.toString().indexOf("success")!=-1) {
										$.when($("#folder_"+$folder_id).removeClass("hide").fadeOut(0,function(){$(this).fadeIn(300);})).done(function(){
											if($(".folders_loader").children().length>5){
												$(".folders_loader").children(":not(#folder_"+$folder_id+"):first").remove();
											}
											if($current_i_class.length){
												$menu.children("i").removeAttr("class").addClass($current_i_class);
											}
										});
										if($folder_id!=0){
											$(".jump-up-filemanager").attr("disabled",false);
										}else{
											$(".jump-up-filemanager").attr("disabled",true);
										}
										if($goBackFileManager[$goBackFileManager.length-1]!=$folder_id && $no_save==0){
											$(".go-back-filemanager").attr("disabled",false);
											$goBackFileManager.push($folder_id);
											$goNextFileManager=[];
											$(".go-next-filemanager").attr("disabled",true);
										}
									}else{
										feedbackOperations(data);
									}
								});
							});
						}
					});
				}, 300);
			}
		}

		var $fileupload_id=0;
		async function fileUploader(){
			if($current_folder>=0){
				$fileupload_id++;
				var $maximum_file_size=<?php print_r((int)(ini_get('upload_max_filesize'))>=(int)(ini_get('post_max_size')) ? (int)(ini_get('upload_max_filesize')):(int)(ini_get('post_max_size'))); ?>;
				const { value: formValues } = await Swal.fire({
					title: (language=="en" ? "Upload file":"آپلود فایل"),
					html:
						'<div id="swal2-content-files">'+
							'<input type="checkbox" class="swal2-is-safe bootstrap-switch" data-off-label="<i class=\'fas fa-globe\'></i>" data-on-label="<i class=\'fas fa-lock\'></i>">'+
							'<div class="swal2-content-file">'+
								'<input type="file" class="swal2-files swal2-file col-10" id="fileupload_id-'+$fileupload_id+'">'+
								'<a href="javascript:void(0)" onclick="$(this).parent().remove()" class="btn btn-link btn-danger btn-sm col-2 p-0 m-0 remove-upload"><i class="tim-icons icon-simple-remove"></i></a>'+
							'</div>'+
						'</div>'+
						'<div class="swal2-content-files-progress progress hide">'+
							'<div class="swal2-content-files-progress-bar progress-bar bg-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>'+
						'</div>',
					focusConfirm: false,
					footer: '<a class="swal2-content-files-addmore" href="javascript:void(0);" onclick="$fileupload_id++;$(\'#swal2-content-files\').append(\'<div class=\\\'swal2-content-file\\\'><input type=\\\'file\\\' class=\\\'swal2-files swal2-file col-10 file_uploading_id\\\'><a href=\\\'javascript:void(0);\\\' onclick=\\\'$(this).parent().remove()\\\' class=\\\'btn btn-link btn-danger btn-sm col-2 p-0 m-0 remove-upload\\\'><i class=\\\'tim-icons icon-simple-remove\\\'></i></a></div>\');$(\'.file_uploading_id:last\').removeClass(\'file_uploading_id\').attr(\'id\',\'fileupload_id-\'+$fileupload_id);">'+(language=="en" ? "Add more":"افزودن بیشتر")+'</a>',
					// showConfirmButton: false,
					showCancelButton: true,
					confirmButtonText: (language=="en" ? "Upload":"آپلود"),
					cancelButtonText: (language=="en" ? "Close":"خروج"),
					showLoaderOnConfirm: true,
					allowOutsideClick: () => !Swal.isLoading(),
					didOpen: () => {
						bsSwitcher();
					},
					preConfirm: () => {
						function cancelUploading() {
							Swal.hideLoading();
							$(".swal2-content-files-progress").fadeOut("slow").addClass("hide");
							$(".swal2-is-safe").parent().parent().show();
							$(".swal2-content-files-addmore").show();
							$(".swal2-cancel.swal2-styled").show();
							$(".remove-upload").show();
						}
						if($("input.swal2-files.swal2-file").length){
							var formData = new FormData(),$files=0;
							formData.append("current_folder", $current_folder);
							formData.append("is_secure", ($(".swal2-is-safe").is(":checked")==true ? 1:0));
							$(".delete_error_file_manager").remove();
							$(".swal2-content-files-progress-bar").attr("aria-valuenow",0).css("width",0);
							$(".swal2-is-safe").parent().parent().hide();
							$(".swal2-cancel.swal2-styled").hide();
							$(".swal2-content-files-addmore").hide();
							$(".remove-upload").hide();
							$("input.swal2-files.swal2-file").each(function () {
								if($(this)[0].files.length!==0){
									if($(".swal2-content-files-progress").hasClass("hide")){
										$(".swal2-content-files-progress").fadeOut("0",function () {
											$(this).removeClass("hide").fadeIn('slow');
										});
									}
									if($(this)[0].files[0].size/1024/1024<=$maximum_file_size){
										if($files!=-1){
											$files++;
											formData.append($(this).attr("id"), $(this)[0].files[0]);
											$(this).attr("disabled",true);
										}
									}else{
										var $showMaxFileSize=Math.round(($maximum_file_size/1024 + Number.EPSILON) * 100) / 100;
										$(this).parent().append('<label class="error delete_error_file_manager">'+(language=="en" ? "Maximum allowed size for upload is : "+$showMaxFileSize+" GB":"حداکثر اندازه مجاز برای بارگذاری "+$showMaxFileSize+" گیگابایت است")+'</label>');
										$(this).addClass("shaker");
										setTimeout(() => {
											$(this).removeClass("shaker");
										}, 500);
										cancelUploading();
										$files=-1;
									}
								}else if($(this)[0].files.length==0){
									$(this).addClass("shaker");
									setTimeout(() => {
										$(this).removeClass("shaker");
									}, 500);
									cancelUploading();
									$files=-1;
								}
							});
							return new Promise(function(resolve) {
								// resolve(); // means ended
								if($files>0){
									$.ajax({
										// Your server script to process the upload
										url: 'file_manager/class/action.php?upload',
										type: 'POST',

										// Form data
										data: formData,

										// Tell jQuery not to process data or worry about content-type
										// You *must* include these options!
										cache: false,
										contentType: false,
										processData: false,

										// Custom XMLHttpRequest
										xhr: function () {
											var myXhr = $.ajaxSettings.xhr();
											if (myXhr.upload) {
												// For handling the progress of the upload
												myXhr.upload.addEventListener('progress', function (e) {
													if (e.lengthComputable) {
														percentageUpdater=Math.round(e.loaded/e.total*100);
														$(".swal2-content-files-progress-bar").attr("aria-valuenow",percentageUpdater).css("width",percentageUpdater+'%');
													}
												}, false);
											}
											return myXhr;
										},
										complete: function(xhr,status) {
											$(".remove-upload").show();
											var data=xhr.responseText;
											data=JSON.parse(data);
											if(typeof data['status'] !== "undefined"){
												delete data['status'];
												data=Object.values(data);
												data.forEach(function (item) {
													var $objectId="#"+item.object_id;
													var $name="<a href='"+item.download_link+"' target='_blank' class='btn btn-link "+(item.is_secure==1 ? "btn-warning":"btn-info")+" btn-sm col-12'><i class='fas "+(item.is_secure==1 ? "fa-lock":"fa-globe")+" ml-1 mr-1'></i>" + item.real_name.substr(0, 25) + (item.real_name.length>25 ? " ...":"") + "<i class='fas fa-external-link ml-1 mr-1'></i></a>";
													$($objectId).parent().append($name);
													$($objectId).parent().children(".remove-upload").remove();
													$($objectId).remove();
												});
											}
											openFolderFileManager($(".menu_file_manager_folder_"+$current_folder).children("a"),$current_folder,1);
											Swal.hideLoading();
											$(".swal2-is-safe").parent().parent().show();
											$(".swal2-content-files-addmore").show();
											$(".swal2-cancel.swal2-styled").show();
											$(".swal2-content-files-progress").children().addClass("bg-success").removeClass("bg-info",function (){
												setTimeout(() => {
													$(".swal2-content-files-progress").fadeOut("slow",function () {
														$(".swal2-content-files-progress").children().removeClass("bg-success").addClass("bg-info");
														$(this).addClass('hide');
													});
													$("input.swal2-files.swal2-file").each(function () {
														if($(this)[0].files.length==0){
															$(this).addClass("shaker");
															setTimeout(() => {
																$(this).removeClass("shaker");
															}, 500);
														}
													});
												}, 700);
											});
										}
									});
								}else{
									$("input.swal2-files.swal2-file").each(function () {
										if($(this)[0].files.length==0){
											$(this).addClass("shaker");
											setTimeout(() => {
												$(this).removeClass("shaker");
											}, 500);
										}
									});
									cancelUploading();
								}
							});
						}else{
							return new Promise(function(resolve) {
								cancelUploading();
							});
						}
					}
				});
			}else{
				Swal.fire({
					icon: 'error',
					title: (language=="en" ? "Warning":"هشدار"),
					text: (language=="en" ? "Uploading in this folder is not allowed !":"بارگذاری در این پوشه مجاز نیست !"),
					showConfirmButton: false,
					timer: 2000
				});
			}
		}
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
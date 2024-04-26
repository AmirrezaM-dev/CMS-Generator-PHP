<?php
	$conn_dir="../connection/connect.php";
	if(session_status() == PHP_SESSION_NONE) {
        session_start(['cookie_lifetime' => 86400]);
    }
	require_once("config.php");
	require_once("setting/check_database.php");
	if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_SESSION['username']."' AND act=1");
			$user_stats=$res_user->rowCount();
			if($user_stats==1 || isset($op_admin) && $op_admin){
?>
<div class="sidebar-wrapper">
	<div class="logo">
		<a href="javascript:void(0)" class="simple-text logo-mini data-text" data-text-en="<?php print_r(getSetting("site_mini_name_en")); ?>" data-text-fa="<?php print_r(getSetting("site_mini_name_fa")); ?>">
			<?php print_r($GLOBALS['user_language']=="en" ? getSetting("site_mini_name_en"):getSetting("site_mini_name_fa")); ?>
		</a>
		<a href="javascript:void(0)" class="simple-text logo-normal data-text" data-text-en="<?php print_r(getSetting("site_name_en")); ?>" data-text-fa="<?php print_r(getSetting("site_name_fa")); ?>">
			<?php print_r($GLOBALS['user_language']=="en" ? getSetting("site_name_en"):getSetting("site_name_fa")); ?>
		</a>
	</div>
	<ul class="nav" id="sidebarLoader">
		<?php
			$idHelper=0;
			$menu_table_config=$connection->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_config WHERE current_name='".$sub_name."menu'");
			if($menu_table_config->rowCount()){
				$menu_table_config=$menu_table_config->fetch();
				$menu_column_config=$connection->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$menu_table_config['id']."' AND current_name='parent_id'");
				if($menu_column_config->rowCount()){
					$menu_column_config=$menu_column_config->fetch();
					$menu_option_text=$connection->query("SELECT * FROM ".$GLOBALS["sub_name"]."table_column_config WHERE table_id='".$menu_table_config['id']."' AND current_name='menu_name_en'");
					if($menu_option_text->rowCount()){
						$menu_option_text=$menu_option_text->fetch();
						$menu_parent_id_table_id=$menu_table_config['id'];
						$menu_parent_id_column_id=$menu_column_config['id'];
						$menu_parent_id_is_optgroup=0;
						$menu_parent_id_optgroup_id='-';
						$menu_parent_id_connected_table=$menu_table_config['id'];
						$menu_parent_id_option_text=$menu_option_text['id'];
						$menu_parent_id_option_value=0;
						$menu_parent_id_ordering=0;
						$menu_parent_id_act=1;
						$res_idHelper=$connection->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options WHERE table_id='".$menu_parent_id_table_id."' AND column_id='".$menu_parent_id_column_id."' AND is_optgroup='".$menu_parent_id_is_optgroup."' AND optgroup_id='".$menu_parent_id_optgroup_id."' AND connected_table='".$menu_parent_id_connected_table."' AND option_text='".$menu_parent_id_option_text."' AND option_value='".$menu_parent_id_option_value."' AND act='".$menu_parent_id_act."'");
						if($res_idHelper->rowCount()){
							$idHelper=$res_idHelper->fetch();
						}
					}
				}
			}
			if($idHelper && isset($idHelper['id'])){
				$idHelper=$idHelper['id']."_-..-_";
			}
			$res_menu=$connection->query("SELECT * FROM ".$sub_name."menu WHERE act=1 AND is_child=0 ORDER BY ordering ASC");
			while($menu=$res_menu->fetch()){
				if(checkPermission(3,$menu['id'],'',$menu['act']) || (checkPermission(1,getTableByName($sub_name."menu")['id'],"read",$menu['act'],"") && checkPermission(2,getColumnByName($sub_name."menu","menu_name_en")['id'],"read",$menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_name_fa")['id'],"read",$menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_mini_name_en")['id'],"read",$menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_mini_name_fa")['id'],"read",$menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_link")['id'],"read",$menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_mode")['id'],"read",$menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_target_mode")['id'],"read",$menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","fa_icon")['id'],"read",$menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","visible")['id'],"read",$menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","is_parent")['id'],"read",$menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","is_child")['id'],"read",$menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","parent_id")['id'],"read",$menu['act'],getTableByName($sub_name."menu")['id']))){
					$res_menus=$connection->query("SELECT * FROM ".$sub_name."menu WHERE act=1 AND is_parent=0 AND is_child=1 AND parent_id='".$idHelper.$menu['id']."' ORDER BY ordering ASC");
					$menu_code=("menu_code"."-".$menu['id']);
					?>
						<?php
							if($menu['is_parent']==1){
						?>
							<li <?php if($menu['menu_mode_justvalue']!=2 && $menu['menu_link']!="" && !empty($menu['menu_link'])){ ?> class="<?php print_r(str_replace(".","_-_--_-_",str_replace("?","_-_QQ_-_",str_replace("=","_-_EE_-_",str_replace("&","_-_AA_-_",$menu['menu_link']))))); ?>_NAV_" <?php }else{?>class="menu_parent_<?php print_r($menu['id']); ?>"<?php } ?>>
								<a id="<?php print_r("click_".$menu_code); ?>" class="hide" <?php if($menu['menu_mode_justvalue']==0 && $menu['menu_link']!="" && !empty($menu['menu_link'])){ ?>onclick='window.open("<?php print_r($menu['menu_link']); ?>");'<?php }elseif($menu['menu_mode_justvalue']==1 && $menu['menu_link']!="" && !empty($menu['menu_link'])){ ?>onclick="pageLoader('<?php print_r($menu['menu_link']); ?>');" <?php } ?> ></a>
								<a class="parent_menu_active" <?php if(isset($menu['menu_target_mode']) && $menu['menu_target_mode']!="" && !empty($menu['menu_target_mode'])){ ?>target="<?php print_r($menu['menu_target_mode_justvalue']); ?>"<?php } ?> onclick="$('#<?php print_r("click_".$menu_code); ?>').click();" data-toggle="collapse" href="#<?php print_r($menu_code); ?>">
									<i class="<?php if($menu['fa_icon']=="" || empty($menu['fa_icon'])){ ?>far fa-bars<?php }else{print_r($menu['fa_icon']);} ?>"></i>
									<p class="data-text" data-text-en="<?php print_r($menu['menu_name_en']); ?> <?php if($res_menus->rowCount()!=0){ ?><b class='caret'></b><?php } ?>" data-text-fa="<?php print_r($menu['menu_name_fa']); ?> <?php if($res_menus->rowCount()!=0){ ?><b class='caret'></b><?php } ?>">
										<?php print_r($GLOBALS['user_language']=="en" ? $menu['menu_name_en']:$menu['menu_name_fa']); ?>
										<?php if($res_menus->rowCount()!=0){ ?><b class='caret'></b><?php } ?>
									</p>
								</a>
								<div class="collapse" id="<?php print_r($menu_code); ?>">
									<ul class="nav">
										<?php
											while($menus=$res_menus->fetch()){
												if(checkPermission(3,$menus['id'],'',$menus['act']) || (checkPermission(1,getTableByName($sub_name."menu")['id'],"read", $menus['act'],"") && checkPermission(2,getColumnByName($sub_name."menu","menu_name_en")['id'],"read", $menus['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_name_fa")['id'],"read", $menus['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_mini_name_en")['id'],"read", $menus['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_mini_name_fa")['id'],"read", $menus['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_link")['id'],"read", $menus['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_mode")['id'],"read", $menus['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_target_mode")['id'],"read", $menus['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","fa_icon")['id'],"read", $menus['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","visible")['id'],"read", $menus['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","is_parent")['id'],"read", $menus['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","is_child")['id'],"read", $menus['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","parent_id")['id'],"read", $menus['act'],getTableByName($sub_name."menu")['id']))){
													?>
														<li <?php if($menus['menu_mode_justvalue']!=2 && $menus['menu_link']!="" && !empty($menus['menu_link'])){ ?> class="<?php print_r(str_replace(".","_-_--_-_",str_replace("?","_-_QQ_-_",str_replace("=","_-_EE_-_",str_replace("&","_-_AA_-_",$menus['menu_link']))))); ?>_NAV_" <?php }else{?>id="menu_child_<?php print_r($menus['id']); ?>"<?php } ?>>
															<a class="child_menu_active" <?php if($menus['menu_target_mode']!="" && !empty($menus['menu_target_mode'])){ ?>target="<?php print_r($menus['menu_target_mode_justvalue']); ?>"<?php } ?> <?php if($menu['menu_link']!="" && !empty($menu['menu_link'])){ ?>href="<?php print_r($menus['menu_link']); ?>"<?php }elseif($menus['menu_mode_justvalue']==1 && $menus['menu_link']!="" && !empty($menus['menu_link'])){ ?> onclick="pageLoader('<?php print_r($menus['menu_link']); ?>');" <?php }elseif($menus['menu_link']!="" && !empty($menus['menu_link'])){ ?> href="<?php print_r($menus['menu_link']); ?>" <?php } ?> >
																<i class="<?php if($menus['fa_icon']=="" || empty($menus['fa_icon'])){ ?>far fa-bars<?php }else{print_r($menus['fa_icon']);} ?>"></i>
																<span class="sidebar-mini-icon data-text" data-text-en="<?php print_r($menus['menu_mini_name_en']); ?>" data-text-fa="<?php print_r($menus['menu_mini_name_fa']); ?>">
																	<?php print_r($GLOBALS['user_language']=="en" ? $menus['menu_mini_name_en']:$menus['menu_mini_name_fa']); ?>
																</span>
																<span class="sidebar-normal data-text" data-text-en="<i class='fa-mini <?php if($menus['fa_icon']=="" || empty($menus['fa_icon'])){ ?>far fa-bars<?php }else{print_r($menus['fa_icon']);} ?>'></i> <?php print_r($menus['menu_name_en']); ?>" data-text-fa="<i class='fa-mini <?php if($menus['fa_icon']=="" || empty($menus['fa_icon'])){ ?>far fa-bars<?php }else{print_r($menus['fa_icon']);} ?>'></i> <?php print_r($menus['menu_name_fa']); ?>">
																	<i class='fa-mini <?php if($menus['fa_icon']=="" || empty($menus['fa_icon'])){ ?>far fa-bars<?php }else{print_r($menus['fa_icon']);} ?>'></i>
																	<?php print_r($GLOBALS['user_language']=="en" ? $menus['menu_name_en']:$menus['menu_name_fa']); ?>
																</span>
															</a>
														</li>
													<?php
												}
											}
										?>
									</ul>
								</div>
							</li>
						<?php
							}else{
						?>
							<li <?php if($menu['menu_mode_justvalue']!=2 && $menu['menu_link']!="" && !empty($menu['menu_link'])){ ?> class="<?php print_r(str_replace(".","_-_--_-_",str_replace("?","_-_QQ_-_",str_replace("=","_-_EE_-_",str_replace("&","_-_AA_-_",$menu['menu_link']))))); ?>_NAV_" <?php }else{?>class="menu_parent_<?php print_r($menu['id']); ?>"<?php } ?>>
								<a class="parent_menu_active" <?php if($menu['menu_target_mode']!="" && !empty($menu['menu_target_mode'])){ ?>target="<?php print_r($menu['menu_target_mode_justvalue']); ?>"<?php } ?> <?php if($menu['menu_mode_justvalue']==0 || $menu['menu_mode_justvalue']==""){ ?>href="<?php print_r($menu['menu_link']); ?>"<?php }elseif($menu['menu_mode_justvalue']==1 && $menu['menu_link']!="" && !empty($menu['menu_link'])){ ?> onclick="pageLoader('<?php print_r($menu['menu_link']); ?>');" <?php } ?> >
									<i class="<?php if($menu['fa_icon']=="" && empty($menu['fa_icon'])){ ?>far fa-bars<?php }else{print_r($menu['fa_icon']);} ?>"></i>
									<p class="data-text" data-text-en="<?php print_r($menu['menu_name_en']); ?>" data-text-fa="<?php print_r($menu['menu_name_fa']); ?>"><?php print_r($GLOBALS['user_language']=="en" ? $menu['menu_name_en']:$menu['menu_name_fa']); ?></p>
								</a>
							</li>
						<?php
							}
						?>
					<?php
				}
			}
		?>
		<script>
			<?php
				$res_menu_activition=$connection->query("SELECT * FROM ".$sub_name."menu WHERE act=1 ORDER BY ordering ASC");
				while($menu_activition=$res_menu_activition->fetch()){
					if(checkPermission(3,$menu_activition['id'],'',$menu_activition['act']) || (checkPermission(1,getTableByName($sub_name."menu")['id'],"read", $menu_activition['act'],"") && checkPermission(2,getColumnByName($sub_name."menu","menu_name_en")['id'],"read", $menu_activition['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_name_fa")['id'],"read", $menu_activition['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_mini_name_en")['id'],"read", $menu_activition['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_mini_name_fa")['id'],"read", $menu_activition['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_link")['id'],"read", $menu_activition['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_mode")['id'],"read", $menu_activition['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_target_mode")['id'],"read", $menu_activition['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","fa_icon")['id'],"read", $menu_activition['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","visible")['id'],"read", $menu_activition['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","is_parent")['id'],"read", $menu_activition['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","is_child")['id'],"read", $menu_activition['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","parent_id")['id'],"read", $menu_activition['act'],getTableByName($sub_name."menu")['id']))){
						if($menu_activition['menu_mode_justvalue']!=2){
							?>
								if(current_page=="<?php print_r($menu_activition['menu_link']); ?>"){
									$("#navbarLoader li.active").removeClass("active");
									$(".<?php print_r(str_replace(".","_-_--_-_",str_replace("?","_-_QQ_-_",str_replace("=","_-_EE_-_",str_replace("&","_-_AA_-_",$menu_activition['menu_link']))))); ?>_NAV_").addClass("active");
									<?php
										if($menu_activition['is_child']==1){
											$res_parent_menu=$connection->query("SELECT * FROM ".$sub_name."menu WHERE id='".$menu_activition['parent_id']."' AND act=1 AND is_parent=1 ORDER BY ordering ASC");
											if($res_parent_menu->rowCount()==1){
												$parent_menu=$res_parent_menu->fetch();
												if(checkPermission(3,$parent_menu['id'],'',$parent_menu['act']) || (checkPermission(1,getTableByName($sub_name."menu")['id'],"read", $parent_menu['act'],"") && checkPermission(2,getColumnByName($sub_name."menu","menu_name_en")['id'],"read", $parent_menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_name_fa")['id'],"read", $parent_menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_mini_name_en")['id'],"read", $parent_menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_mini_name_fa")['id'],"read", $parent_menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_link")['id'],"read", $parent_menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_mode")['id'],"read", $parent_menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","menu_target_mode")['id'],"read", $parent_menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","fa_icon")['id'],"read", $parent_menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","visible")['id'],"read", $parent_menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","is_parent")['id'],"read", $parent_menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","is_child")['id'],"read", $parent_menu['act'],getTableByName($sub_name."menu")['id']) && checkPermission(2,getColumnByName($sub_name."menu","parent_id")['id'],"read", $parent_menu['act'],getTableByName($sub_name."menu")['id']))){
													$menu_code=("menu_code"."-".$parent_menu['id']);
													if($parent_menu['menu_mode_justvalue']!=2 && $parent_menu['menu_link']!="" && !empty($parent_menu['menu_link'])){
														?>
															$(".<?php print_r(str_replace(".","_-_--_-_",str_replace("?","_-_QQ_-_",str_replace("=","_-_EE_-_",str_replace("&","_-_AA_-_",$parent_menu['menu_link']))))); ?>_NAV_").addClass("active");
														<?php
													}else{
														?>
															$(".menu_parent_<?php print_r($parent_menu['id']); ?>").addClass("active");
														<?php
													}
												}
									?>
										$("#<?php print_r($menu_code); ?>").addClass("show");
										$($("#<?php print_r($menu_code); ?>").parent().children()[1]).removeClass("collapsed").attr("aria-expanded","true");
									<?php
											}
										}
									?>
								}
							<?php
						}
					}
				}
			?>
			$(document).on("click", ".parent_menu_active", function() {
				var aThis=$($($(this).parent().html())[0]);
				if(aThis.attr("onclick")!=undefined && aThis.attr("onclick")!=null && aThis.attr("onclick")!="" && aThis.attr("onclick").indexOf("pageLoader(")!=-1){
					$("#navbarLoader li.active").removeClass("active");
					$(this).parent().addClass('active');
				}
			});
			$(document).on("click", ".child_menu_active", function() {
				if($(this).attr("onclick")!=undefined && $(this).attr("onclick")!=null && $(this).attr("onclick")!="" && $(this).attr("onclick").indexOf("pageLoader(")!=-1){
					$("#navbarLoader li.active").removeClass("active");
					$(this).parent().addClass('active');
					$($(this).parent().parent().parent().parent()[0]).addClass("active");
				}
			});
		</script>
	</ul>
</div>
<?php
	if(!isset($_GET['no_script'])){
?>
	<script>
		var navbar_menu_visible=0,sidebar_mini_active = !1,navbar_visible=0,global_width=[];
		$(document).ready(function(){
			if(navigator.platform.indexOf("Win") > -1) {
				if(0 != $(".main-panel").length){
					$pscrollbars.push(new PerfectScrollbar(".main-panel:not(.ps)", {
						wheelSpeed: 1,
						wheelPropagation: !0,
						minScrollbarLength: 20,
						suppressScrollX: !0
					}));
				}
				if(0 != $(".sidebar .sidebar-wrapper").length) {
					$pscrollbars.push(new PerfectScrollbar(".sidebar:not(.ps) .sidebar-wrapper:not(.ps)", {
						wheelSpeed: 1,
						wheelPropagation: !0,
						minScrollbarLength: 20,
						suppressScrollX: !0
					}));
				}
				$(".table-responsive:not(.ps)").each(function() {
					$pscrollbars.push(new PerfectScrollbar($(this)[0], {
						wheelSpeed: 1,
						wheelPropagation: !0,
						minScrollbarLength: 20,
						suppressScrollX: !0
					}));
				});
				$(".a-ps:not(.ps)").each(function() {$pscrollbars.push(new PerfectScrollbar($(this)[0]));});
				$("html").addClass("perfect-scrollbar-on");
			} else {
				$("html").addClass("perfect-scrollbar-off");
			}
			0 != $(".navbar[color-on-scroll]").length && $(window).on("scroll"), 0 == $(".full-screen-map").length && 0 == $(".bd-docs").length && $(".navbar-toggler").click(function() {
				$(".collapse").on("show.bs.collapse", function() {
					$(this).closest(".navbar").removeClass("navbar-transparent").addClass("bg-white");
					if(theme_def=="black"){
						$("nav.navbar").removeClass("bg-white");
					}
				}).on("hide.bs.collapse", function() {
					$(this).closest(".navbar").addClass("navbar-transparent").removeClass("bg-white");
				});
				$(".navbar").css("transition", "");
			});
			if(typeof sidebar_mini_active == undefined || sidebar_mini_active == ""){
				sidebar_mini_active= !1;
			}
			setTimeout(function(){
				0 != $(".sidebar-mini").length && (sidebar_mini_active = !0), $(".minimize-sidebar").click(function() {
					1 == sidebar_mini_active ? ($("body").removeClass("sidebar-mini"), sidebar_mini_active = !1 /*,blackDashboard.showSidebarMessage("Sidebar mini deactivated...") my custom*/ ) : ($("body").addClass("sidebar-mini"), sidebar_mini_active = !0 /*,blackDashboard.showSidebarMessage("Sidebar mini activated...") my custom*/ );
					var a = setInterval(function() {
						window.dispatchEvent(new Event("resize"))
					}, 180);
					setTimeout(function() {
						clearInterval(a)
					}, 1e3)
				});
			},100);
			$(".navbar").css({
				top: "0",
				transition: "all .5s linear"
			});
			(navigator.platform.indexOf("Win") > -1 ? $(".ps") : $(window)).scroll(function() {
				$(this).scrollTop() > 50 ? $(".navbar-minimize-fixed").css("opacity", "1") : $(".navbar-minimize-fixed").css("opacity", "0")
			});
			$("#search-button").click(function() {
				$(this).closest(".navbar-collapse").removeClass("show"), $(".navbar").addClass("navbar-transparent").removeClass("bg-white");
			});
		});
		$(document).mouseup(function(e){
			var container = $(".navbar");
			if(!container.is(e.target) && container.has(e.target).length === 0) {
				if($("#navigation").hasClass("show") && $(window).width()<992){
					$("#navbar-toggler-2").click();
				}
			}
		});
		$(document).on("click", ".navbar-toggle", function() {
			if($("#navigation").hasClass("show")){
				navbar_visible=1;
			}else if(navbar_visible!=2){
				navbar_visible=0;
			}
			var a = $(this);
			if(1 == navbar_menu_visible) $("html").removeClass("nav-open"), navbar_menu_visible = 0, setTimeout(function() {
				a.removeClass("toggled"), $(".bodyClick").remove();
				if(navbar_visible==1){
					navbar_visible=0;
					$("#navbar-toggler-2").click();
				}
			}, 550);
			else {
				if(navbar_visible==1){
					$("#navbar-toggler-2").click();
				}
				setTimeout(function() {
					a.addClass("toggled")
				}, 580);
				$('body.rtl').css('right','0');
				$('<div class="bodyClick"></div>').appendTo("body").click(function() {
					$("html").removeClass("nav-open");navbar_menu_visible = 0, setTimeout(function() {
						a.removeClass("toggled"), $(".bodyClick").remove();
						$('body.rtl').removeAttr("style");
						if(navbar_visible==1){
							navbar_visible=0;
							$("#navbar-toggler-2").click();
						}
						$("html").removeClass("nav-was-open");
					}, 550)
				}), $("html").addClass("nav-open nav-was-open"), navbar_menu_visible = 1
			}
		});
		var $window_width,$window_height;
		$(document).ready(function () {
			$window_width=$( window ).width();
			$window_height=$( window ).height();
		});
		$(window).resize(function() {

			if($window_width!=$( window ).width()){
				var dataTables=$(".dataTable:not(.disable_custom_table)");
				for(IdataTables=0;IdataTables<dataTables.length;IdataTables++){
					if(typeof global_width[IdataTables] === "undefined" || global_width[IdataTables]==undefined || global_width[IdataTables]==null){
						global_width[IdataTables]=$("#" + $(dataTables[IdataTables]).attr("id") + "_wrapper").parent().parent().width();
						resizeTable();
						$(".modal").modal("hide");
					}else if(global_width[IdataTables]!=$("#" + $(dataTables[IdataTables]).attr("id") + "_wrapper").parent().parent().width()){
						global_width[IdataTables]=$("#" + $(dataTables[IdataTables]).attr("id") + "_wrapper").parent().parent().width();
						resizeTable();
						$(".modal").modal("hide");
					}
				}
			}
			if(seq = seq2 = 0, 0 == $(".full-screen-map").length && 0 == $(".bd-docs").length) {
				var a = $(".navbar").find('[data-toggle="collapse"]').attr("aria-expanded");
				$(".navbar").hasClass("bg-white") && $(window).width() > 991 ? $(".navbar").removeClass("bg-white").addClass("navbar-transparent") : $(".navbar").hasClass("navbar-transparent") && $(window).width() < 991 && "false" != a && $(".navbar").addClass("bg-white").removeClass("navbar-transparent");
				if(theme_def=="black"){
					$("nav.navbar").removeClass("bg-white");
				}
				if($(window).width() > 991 && navbar_visible==1 && $(window).width()<1200){
					navbar_visible=2;
					$("#navbar-toggler-2").click();
				}else if($(window).width() < 991 && navbar_visible==2 && $(window).width()<1200){
					navbar_visible=1;
					$("#navbar-toggler-2").click();
				}
			}

			pscrollbarUpdate();

			$window_width=$( window ).width();
			$window_height=$( window ).height();

		});
	</script>
<?php
	}
?>
<?php
			}
		}
	}
?>

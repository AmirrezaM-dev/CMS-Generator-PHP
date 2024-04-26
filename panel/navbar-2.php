<?php
	if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_SESSION['username']."' AND act=1");
			$user_stats=$res_user->rowCount();
			if($user_stats==1 || getSetting("op_admin")==$_SESSION['username']){
?>
	<nav class="navbar navbar-expand-lg navbar-absolute navbar-transparent custom_navbar">
		<div class="container-fluid">
			<div class="navbar-wrapper">
				<div class="navbar-minimize d-inline">
					<button class="minimize-sidebar btn btn-link btn-just-icon" rel="tooltip" data-original-title="Sidebar toggle" data-placement="right">
						<i class="tim-icons icon-align-center visible-on-sidebar-regular"></i>
						<i class="tim-icons icon-bullet-list-67 visible-on-sidebar-mini"></i>
					</button>
				</div>
				<div class="navbar-toggle d-inline">
					<button type="button" class="navbar-toggler">
						<span class="navbar-toggler-bar bar1"></span>
						<span class="navbar-toggler-bar bar2"></span>
						<span class="navbar-toggler-bar bar3"></span>
					</button>
				</div>
				<a id="page_name" class="navbar-brand data-text" href="javascript:void(0)" data-text-en="Dashboard" data-text-fa="داشبورد"><?php print_r(($GLOBALS['user_language']=="en" ? "Dashboard":"داشبورد")); ?></a>
			</div>
			<button id="navbar-toggler-2" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-bar navbar-kebab"></span>
				<span class="navbar-toggler-bar navbar-kebab"></span>
				<span class="navbar-toggler-bar navbar-kebab"></span>
			</button>
			<div class="collapse navbar-collapse" id="navigation">
				<ul class="navbar-nav ml-auto">
					<?php
						/*
						<li class="search-bar input-group">
							<a href="javascript:void(0)" class="btn btn-link" id="search-button" data-toggle="modal" data-target="#searchModal">
								<i class="tim-icons icon-zoom-split"></i>
								<p class="d-lg-none d-md-block">Search</p>
							</a>
						</li>
						*/
						if(isset($op_admin) && $op_admin){
					?>
						<li class="nav-item">
							<a href="javascript:void(0)" class="nav-link developer_mode_btn <?php if(getUserSetting("developer-mode")=="true"){?>text-primary<?php } ?>" onclick="if($(this).hasClass('text-primary')){developer_mode('false',this);}else{developer_mode('true',this);}">
								<i class="fad fa-laptop-code"></i>
								<p class="d-lg-none data-text" data-text-en="Developer Mode" data-text-fa="حالت توسعه دهنده">
									<?php print_r($GLOBALS['user_language']=="en" ? "Developer Mode":"حالت توسعه دهنده"); ?>
								</p>
							</a>
						</li>
					<?php
						}
					?>
					<li class="nav-item">
						<a href="javascript:void(0)" class="nav-link night_mode_btn" onclick="if(theme_def=='black'){toggleTheme('white');}else{toggleTheme('black');}">
							<i class="fad <?php if(getUserSetting('theme-default')=="white"){?>fa-moon<?php }else{?>fa-sun<?php } ?>"></i>
							<p class="d-lg-none data-text" data-text-en="<?php if(getUserSetting('theme-default')=="white"){?>Dark<?php }else{?>Light<?php } ?>" data-text-fa="<?php if(getUserSetting('theme-default')=="white"){?>تاریک<?php }else{?>نورانی<?php } ?>">
								<?php print_r($GLOBALS['user_language']=="en" ? (getUserSetting('theme-default')=="white" ? "Dark":"Light"):(getUserSetting('theme-default')=="white" ? "تاریک":"نورانی"));?>
							</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="#setting" class="nav-link">
							<i class="fad fa-cogs"></i>
							<p class="d-lg-none data-text" data-text-en="Setting" data-text-fa="تنظیمات">
								<?php print_r($GLOBALS['user_language']=="en" ? "Setting":"تنظیمات"); ?>
							</p>
						</a>
					</li>
					<li class="dropdown nav-item">
						<a href="javascript:void(0)" class="dropdown-toggle nav-link" data-toggle="dropdown">

							<i class="fad fa-language"></i>
							<b class="caret d-none d-lg-block d-xl-block"></b>
							<p class="d-lg-none data-text" data-text-en="Language" data-text-fa="زبان">
								<?php print_r($GLOBALS['user_language']=="en" ? "Language":"زبان"); ?>
							</p>
						</a>
						<ul class="dropdown-menu dropdown-menu-right dropdown-navbar">
							<li class="nav-link">
								<a href="javascript:void(0)" class="nav-item dropdown-item" onclick="changeLanguage('en');" dir="ltr" style="text-align: left !important;">English</a>
							</li>
							<li class="nav-link">
								<a href="javascript:void(0)" class="nav-item dropdown-item" onclick="changeLanguage('fa');"  dir="rtl" style="text-align: right !important;font-family: IRANSans !important;">فارسی</a>
							</li>
						</ul>
					</li>
					<?php
						/*
						<li class="dropdown nav-item">
							<a href="javascript:void(0)" class="dropdown-toggle nav-link" data-toggle="dropdown">
								<div class="notification d-none d-lg-block d-xl-block"></div>
								<i class="tim-icons icon-sound-wave"></i>
								<b class="caret d-none d-lg-block d-xl-block"></b>
								<p class="d-lg-none">
									Notifications
								</p>
							</a>
							<ul class="dropdown-menu dropdown-menu-right dropdown-navbar">
								<li class="nav-link">
									<a href="#" class="nav-item dropdown-item">Mike John responded to your email</a>
								</li>
								<li class="nav-link">
									<a href="javascript:void(0)" class="nav-item dropdown-item">You have 5 more tasks</a>
								</li>
								<li class="nav-link">
									<a href="javascript:void(0)" class="nav-item dropdown-item">Your friend Michael is in town</a>
								</li>
								<li class="nav-link">
									<a href="javascript:void(0)" class="nav-item dropdown-item">Another notification</a>
								</li>
								<li class="nav-link">
									<a href="javascript:void(0)" class="nav-item dropdown-item">Another one</a>
								</li>
							</ul>
						</li>
						*/
					?>
					<li class="dropdown nav-item">
						<a href="javascript:void(0)" class="dropdown-toggle nav-link" data-toggle="dropdown">
							<?php
								$all_new_messages_sql=$connection->query("SELECT * FROM ".$sub_name."messages WHERE sender_id!='".$_SESSION["username"]."' AND (reciver_id='".$_SESSION["username"]."' OR $op_admin AND reciver_id='') AND (recive_time=0 OR recive_time='') ORDER BY ordering DESC");
								if($all_new_messages_sql->rowCount()){
							?>
								<div class="notification d-none d-lg-block"></div>
							<?php
								}
							?>
							<i class="fad fa-messages"></i>
							<p class="d-lg-none data-text" data-text-en="Notifications" data-text-fa="اعلانات">
								<?php print_r($GLOBALS['user_language']=="en" ? "Notifications":"اعلانات"); ?>
							</p>
						</a>
						<ul class="dropdown-menu dropdown-menu-right dropdown-navbar">
							<?php
								$res_messages_titles=$connection->query("SELECT * FROM ".$sub_name."messages WHERE sender_id!='".$_SESSION["username"]."' AND (reciver_id='".$_SESSION["username"]."' OR $op_admin AND reciver_id='') AND (recive_time=0 OR recive_time='') ORDER BY ordering DESC LIMIT 5");
								$claimed_titles=[];
								while ($messages_title=$res_messages_titles->fetch()) {
									$title=$connection->query("SELECT * FROM ".$sub_name."messages_titles WHERE id='".$messages_title['title_id']."'")->fetch();
									if(!in_array($title['id'],$claimed_titles)){
										array_push($claimed_titles,$title['id']);
							?>
								<li class="nav-link">
									<a href="#" class="nav-item dropdown-item"><?php print_r($title['title']); ?></a>
								</li>
							<?php
									}
								}
							?>
							<li class="nav-link">
								<a href="#messages" class="nav-item dropdown-item data-text" data-text-en="All Messages" data-text-fa="همه پیام ها"><?php print_r($GLOBALS['user_language']=="en" ? "All Messages":"همه پیام ها"); ?></a>
							</li>
						</ul>
					</li>
					<li class="dropdown nav-item">
						<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
							<?php
								if(strlen($user_info['picture'])){
							?>
								<div class="photo">
									<img src="<?php print_r($user_info['picture']); ?>">
								</div>
							<?php
								}else{
							?>
								<i class="far fa-user-shield"></i>
							<?php
								}
							?>
							<b class="caret d-none d-lg-block d-xl-block"></b>
							<p class="d-lg-none">
								<?php print_r($user_info['username']); ?>
							</p>
						</a>
						<ul class="dropdown-menu dropdown-navbar">
							<li class="nav-link">
								<a href="#tables?name=admins&action=edit&id=<?php print_r($user_info['id']); ?>" class="nav-item dropdown-item data-text" data-text-en="User settings" data-text-fa="تنظیمات حساب"><?php print_r($GLOBALS['user_language']=="en" ? "User settings":"تنظیمات حساب"); ?></a>
							</li>
							<li class="dropdown-divider"></li>
							<li class="nav-link">
								<a href="login/login<?php print_r($GLOBALS['user_language']=="en" ? "":"-rtl"); ?>.php" data-href-fa="login/login.php" data-href-en="login/login.php" class="nav-item dropdown-item data-href data-text" data-text-en="Log out" data-text-fa="خروج"><?php print_r($GLOBALS['user_language']=="en" ? "Log out":"خروج"); ?></a>
							</li>
						</ul>
					</li>
					<li class="separator d-lg-none"></li>
				</ul>
			</div>
		</div>
	</nav>
	<div class="modal modal-search" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModal" aria-hidden="true">
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
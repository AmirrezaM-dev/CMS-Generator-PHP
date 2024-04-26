<?php
	$conn_dir="../connection/connect.php";
	if(session_status() == PHP_SESSION_NONE) {
        session_start(['cookie_lifetime' => 86400]);
    }
	require_once("config.php");
	require_once("setting/check_database.php");
?>
<div class="row">
	<div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title data-text" data-text-en="General Settings" data-text-fa="تنظیمات عمومی"><?php print_r($GLOBALS['user_language']=="en" ? "General Settings":"تنظیمات عمومی"); ?></h4>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12 mb-4">
						<h4 class="card-title data-text" data-text-en="Choose your language" data-text-fa="زبان خود را انتخاب نمایید"><?php print_r($GLOBALS['user_language']=="en" ? "Choose your language":"زبان خود را انتخاب نمایید"); ?></h4>
						<div class="row">
							<div class="col-12">
								<select id="toggle-language" class="selectpicker disabled-primary data-title" data-size="7" data-style="btn btn-primary" data-title-en="Select Language" data-title-fa="انتخاب زبان" title="<?php if($GLOBALS['user_language']=='fa'){?>انتخاب زبان<?php }elseif($GLOBALS['user_language']=='en'){?>Select Language<?php } ?>" onchange="toggleLang($(this).val())">
									<option class="data-text" data-text-fa="فارسی" data-text-en="Persian" <?php if($GLOBALS['user_language']=='fa'){?>selected<?php } ?> value="fa"><?php if($GLOBALS['user_language']=='fa'){?>فارسی<?php }elseif($GLOBALS['user_language']=='en'){?>Persian<?php } ?></option>
									<option class="data-text" data-text-fa="انگلیسی" data-text-en="English" <?php if($GLOBALS['user_language']=='en'){?>selected<?php } ?> value="en"><?php if($GLOBALS['user_language']=='fa'){?>انگلیسی<?php }elseif($GLOBALS['user_language']=='en'){?>English<?php } ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12 mb-4">
						<h4 class="card-title data-text" data-text-en="Maximum number of page for load" data-text-fa="حداکثر تعداد صفحه برای بارگذاری"><?php print_r($GLOBALS['user_language']=="en" ? "Maximum number of page for load":"حداکثر تعداد صفحه برای بارگذاری"); ?></h4>
						<div class="row">
							<div class="col-12">
								<select id="toggle-maximum" class="selectpicker" data-size="7" data-style="btn btn-primary" title="Select Number">
									<option <?php if(getUserSetting('maximum-page')=='1'){?>selected<?php } ?> value="1">1</option>
									<option <?php if(getUserSetting('maximum-page')=='10'){?>selected<?php } ?> value="10">10</option>
									<option <?php if(getUserSetting('maximum-page')=='15'){?>selected<?php } ?> value="15">15</option>
									<option <?php if(getUserSetting('maximum-page')=='20'){?>selected<?php } ?> value="20">20</option>
									<option <?php if(getUserSetting('maximum-page')=='25'){?>selected<?php } ?> value="25">25</option>
									<option <?php if(getUserSetting('maximum-page')=='50'){?>selected<?php } ?> value="50">50</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title data-text" data-text-en="Theme Settings" data-text-fa="تنظیمات پوسته"><?php print_r($GLOBALS['user_language']=="en" ? "Theme Settings":"تنظیمات پوسته"); ?></h4>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12 mb-4">
						<h4 class="card-title data-text" data-text-en="Choose your theme" data-text-fa="پوسته خود را انتخاب نمایید"><?php print_r($GLOBALS['user_language']=="en" ? "Choose your theme":"پوسته خود را انتخاب نمایید"); ?></h4>
						<div class="row">
							<div class="col-12">
								<select id="toggle-theme" class="selectpicker disabled-primary data-title" data-size="7" data-style="btn btn-primary" data-title-en="Select Theme" data-title-fa="انتخاب پوسته" title="<?php if($GLOBALS['user_language']=='fa'){?>انتخاب پوسته<?php }elseif($GLOBALS['user_language']=='en'){?>Select Theme<?php } ?>" onchange="toggleTheme($(this).val())">
									<option class="data-text" data-text-fa="مشکی" data-text-en="Black" <?php if(getUserSetting('theme-default')=='black'){?>selected<?php } ?> value="black"><?php if($GLOBALS['user_language']=='fa'){?>مشکی<?php }elseif($GLOBALS['user_language']=='en'){?>Black<?php } ?></option>
									<option class="data-text" data-text-fa="سفید" data-text-en="White" <?php if(getUserSetting('theme-default')=='white'){?>selected<?php } ?> value="white"><?php if($GLOBALS['user_language']=='fa'){?>سفید<?php }elseif($GLOBALS['user_language']=='en'){?>White<?php } ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12 mb-4">
						<h4 class="card-title data-text" data-text-en="Choose your color" data-text-fa="انتخاب رنگ"><?php print_r($GLOBALS['user_language']=="en" ? "Choose your color":"انتخاب رنگ"); ?></h4>
						<div class="row">
							<div class="col-12">
								<select id="toggle-color" class="selectpicker disabled-primary data-title" data-size="7" data-style="btn btn-primary" data-title-en="Select Color" data-title-fa="انتخاب رنگ" title="<?php if($GLOBALS['user_language']=='fa'){?>انتخاب رنگ<?php }elseif($GLOBALS['user_language']=='en'){?>Select Color<?php } ?>">
									<option <?php if(getUserSetting('data-color-default')=='primary'){?>selected<?php } ?> value="primary" class="data-text" data-text-fa="صورتی" data-text-en="Pink"><?php if($GLOBALS['user_language']=='fa'){?>صورتی<?php }elseif($GLOBALS['user_language']=='en'){?>Pink<?php } ?></option>
									<option <?php if(getUserSetting('data-color-default')=='blue'){?>selected<?php } ?> value="blue" class="data-text" data-text-fa="آبی" data-text-en="Blue"><?php if($GLOBALS['user_language']=='fa'){?>آبی<?php }elseif($GLOBALS['user_language']=='en'){?>Blue<?php } ?></option>
									<option <?php if(getUserSetting('data-color-default')=='green'){?>selected<?php } ?> value="green" class="data-text" data-text-fa="سبز" data-text-en="Green"><?php if($GLOBALS['user_language']=='fa'){?>سبز<?php }elseif($GLOBALS['user_language']=='en'){?>Green<?php } ?></option>
									<option <?php if(getUserSetting('data-color-default')=='orange'){?>selected<?php } ?> value="orange" class="data-text" data-text-fa="نارنجی" data-text-en="Orange"><?php if($GLOBALS['user_language']=='fa'){?>نارنجی<?php }elseif($GLOBALS['user_language']=='en'){?>Orange<?php } ?></option>
									<option <?php if(getUserSetting('data-color-default')=='red'){?>selected<?php } ?> value="red" class="data-text" data-text-fa="قرمز" data-text-en="Red"><?php if($GLOBALS['user_language']=='fa'){?>قرمز<?php }elseif($GLOBALS['user_language']=='en'){?>Red<?php } ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title data-text" data-text-en="Panel Settings" data-text-fa="تنظیمات پنل"><?php print_r($GLOBALS['user_language']=="en" ? "Panel Settings":"تنظیمات پنل"); ?></h4>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12 mb-4">
						<h4 class="card-title data-text" data-text-en="Navbar Setting" data-text-fa="تنظیمات منو"><?php print_r($GLOBALS['user_language']=="en" ? "Navbar Setting":"تنظیمات منو"); ?></h4>
						<div class="row mb-3">
							<div class="col-md-12">
								<p class="category data-text" data-text-en="Fixed menu" data-text-fa="منوئ ثابت"><?php print_r($GLOBALS['user_language']=="en" ? "Fixed menu":"منوئ ثابت"); ?></p>
								<input id="toggle-fixed_navbar" type="checkbox" <?php if(getUserSetting('fixed-menu')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" onchange="navbarFix(this);" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
							</div>
							<div class="col-md-12">
								<p class="category data-text" data-text-en="Minimized navbar" data-text-fa="منو کوچک شده"><?php print_r($GLOBALS['user_language']=="en" ? "Minimized navbar":"منو کوچک شده"); ?></p>
								<input id="toggle-sidebar_mini" type="checkbox" onchange="if($(this).is(':checked')){$('body').addClass('sidebar-mini');sidebar_mini_active = 'true';}else{$('body').removeClass('sidebar-mini');sidebar_mini_active = 'false';}newUserSetting('sidebar-minimize', sidebar_mini_active);resizeTable();" <?php if(getUserSetting('sidebar-minimize')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12 mb-4">
						<h4 class="card-title data-text" data-text-en="Scroller button settings" data-text-fa="تنظیمات دکمه های اسکرول"><?php print_r($GLOBALS['user_language']=="en" ? "Scroller button settings":"تنظیمات دکمه های اسکرول"); ?></h4>
						<div class="row mb-3">
							<div class="col-md-12">
								<p class="category data-text" data-text-en="Scroll to top" data-text-fa="رفتن به بالا"><?php print_r($GLOBALS['user_language']=="en" ? "Scroll to top":"رفتن به بالا"); ?></p>
								<input id="toggle-scroll_top" type="checkbox" onchange="scrollSettings('scroll-top',$(this))" <?php if(getUserSetting('scroll-top')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
							</div>
							<div class="col-md-12">
								<p class="category data-text" data-text-en="Move to top" data-text-fa="حرکت به بالا"><?php print_r($GLOBALS['user_language']=="en" ? "Move to top":"حرکت به بالا"); ?></p>
								<input id="toggle-move_top" type="checkbox" onchange="scrollSettings('move-top',$(this))" <?php if(getUserSetting('move-top')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
							</div>
							<div class="col-md-12">
								<p class="category data-text" data-text-en="Move to bottom" data-text-fa="حرکت به پایین"><?php print_r($GLOBALS['user_language']=="en" ? "Move to bottom":"حرکت به پایین"); ?></p>
								<input id="toggle-move_bottom" type="checkbox" onchange="scrollSettings('move-bottom',$(this))" <?php if(getUserSetting('move-bottom')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
							</div>
							<div class="col-md-12">
								<p class="category data-text" data-text-en="Scroll to bottom" data-text-fa="رفتن به پایین"><?php print_r($GLOBALS['user_language']=="en" ? "Scroll to bottom":"رفتن به پایین"); ?></p>
								<input id="toggle-scroll_bottom" type="checkbox" onchange="scrollSettings('scroll-bottom',$(this))" <?php if(getUserSetting('scroll-bottom')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title data-text" data-text-en="Memorized questions" data-text-fa="سوال های به خاطر سپرده شده"><?php print_r($GLOBALS['user_language']=="en" ? "Memorized questions":"سوال های به خاطر سپرده شده"); ?></h4>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12 mb-4">
						<h4 class="card-title data-text" data-text-en="Table operations" data-text-fa="عملیات در جدول"><?php print_r($GLOBALS['user_language']=="en" ? "Table operations":"عملیات در جدول"); ?></h4>
						<div class="row mb-3">
							<div class="col-md-12">
								<p class="category data-text" data-text-en="Back to table" data-text-fa="بازگشت به جدول"><?php print_r($GLOBALS['user_language']=="en" ? "Back to table":"بازگشت به جدول"); ?></p>
								<input id="toggle-backToTable_showAlert" type="checkbox" onchange="dontShowAlert('backToTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('backToTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
							</div>
							<div class="col-md-12">
								<p class="category data-text" data-text-en="Clear all inputs" data-text-fa="پاکسازی تمام ورودی ها"><?php print_r($GLOBALS['user_language']=="en" ? "Clear all inputs":"پاکسازی تمام ورودی ها"); ?></p>
								<input id="toggle-clearInputs_showAlert" type="checkbox" onchange="dontShowAlert('clearInputs_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('clearInputs_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
							</div>
							<div class="col-md-12">
								<p class="category data-text" data-text-en="Reset all inputs" data-text-fa="بازگشت ورودی ها به حالت اولیه"><?php print_r($GLOBALS['user_language']=="en" ? "Reset all inputs":"بازگشت ورودی ها به حالت اولیه"); ?></p>
								<input id="toggle-resetInputs_showAlert" type="checkbox" onchange="dontShowAlert('resetInputs_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('resetInputs_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
							</div>
							<div class="col-md-12">
								<p class="category data-text" data-text-en="Delete of table" data-text-fa="حذف از جدول"><?php print_r($GLOBALS['user_language']=="en" ? "Delete of table":"حذف از جدول"); ?></p>
								<input id="toggle-deleteThis_showAlert" type="checkbox" onchange="dontShowAlert('deleteThis_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('deleteThis_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
							</div>
							<div class="col-md-12">
								<p class="category data-text" data-text-en="Delete all of table" data-text-fa="حذف همه از جدول"><?php print_r($GLOBALS['user_language']=="en" ? "Delete all of table":"حذف همه از جدول"); ?></p>
								<input id="toggle-deleteAllThis_showAlert" type="checkbox" onchange="dontShowAlert('deleteAllThis_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('deleteAllThis_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
							</div>
							<div class="col-md-12">
								<p class="category data-text" data-text-en="ًReset order table view" data-text-fa="تنظیم مجدد ترتیب در جدول"><?php print_r($GLOBALS['user_language']=="en" ? "ًReset order table view":"تنظیم مجدد ترتیب در جدول"); ?></p>
								<input id="toggle-reorderTable_showAlert" type="checkbox" onchange="dontShowAlert('reorderTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('reorderTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
							</div>
						</div>
						<h4 class="card-title data-text" data-text-en="File manager" data-text-fa="مدیریت فایل ها"><?php print_r($GLOBALS['user_language']=="en" ? "File manager":"مدیریت فایل ها"); ?></h4>
						<div class="row mb-3">
							<div class="col-md-12">
								<p class="category data-text" data-text-en="Delete file, folder" data-text-fa="حذف فایل و پوشه"><?php print_r($GLOBALS['user_language']=="en" ? "Delete file, folder":"حذف فایل و پوشه"); ?></p>
								<input id="toggle-deleteFileManager_showAlert" type="checkbox" onchange="dontShowAlert('deleteFileManager_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('deleteFileManager_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
							</div>
						</div>
						<?php if(isset($op_admin) && $op_admin){ ?>
							<h4 class="card-title data-text" data-text-en="Columns (table adding)" data-text-fa="ستون ها (افزودن جدول)"><?php print_r($GLOBALS['user_language']=="en" ? "Columns (table adding)":"ستون ها (افزودن جدول)"); ?></h4>
							<div class="row mb-3">
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Clear column info" data-text-fa="حذف اطلاعات ستون"><?php print_r($GLOBALS['user_language']=="en" ? "Clear column info":"حذف اطلاعات ستون"); ?></p>
									<input id="toggle-clearCreateTable_showAlert" type="checkbox" onchange="dontShowAlert('clearCreateTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('clearCreateTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Skip column" data-text-fa="رد کردن ستون "><?php print_r($GLOBALS['user_language']=="en" ? "Skip column":"رد کردن ستون "); ?></p>
									<input id="toggle-skipNewCreateTable_showAlert" type="checkbox" onchange="dontShowAlert('skipNewCreateTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('skipNewCreateTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Skip all columns" data-text-fa="رد کردن همه ستون ها"><?php print_r($GLOBALS['user_language']=="en" ? "Skip all columns":"رد کردن همه ستون ها"); ?></p>
									<input id="toggle-skipNewAllCreateTable_showAlert" type="checkbox" onchange="dontShowAlert('skipNewAllCreateTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('skipNewAllCreateTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Delete column" data-text-fa="خذف ستون"><?php print_r($GLOBALS['user_language']=="en" ? "Delete column":"خذف ستون"); ?></p>
									<input id="toggle-deleteCreateTable_showAlert" type="checkbox" onchange="dontShowAlert('deleteCreateTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('deleteCreateTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Delete all column" data-text-fa="حذف همه ستون ها"><?php print_r($GLOBALS['user_language']=="en" ? "Delete all column":"حذف همه ستون ها"); ?></p>
									<input id="toggle-deleteAllCreateTable_showAlert" type="checkbox" onchange="dontShowAlert('deleteAllCreateTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('deleteAllCreateTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Reset column info" data-text-fa="بازگردانی اطلاعات ستون به حالت اولیه"><?php print_r($GLOBALS['user_language']=="en" ? "Reset column info":"بازگردانی اطلاعات ستون به حالت اولیه"); ?></p>
									<input id="toggle-resetCreateTable_showAlert" type="checkbox" onchange="dontShowAlert('resetCreateTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('resetCreateTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Reset all columns info" data-text-fa="بازگردانی اطلاعات تمام ستون ها (افزوند جدول)"><?php print_r($GLOBALS['user_language']=="en" ? "Reset all columns info":"بازگردانی اطلاعات تمام ستون ها (افزوند جدول)"); ?></p>
									<input id="toggle-resetAllCreateTable_showAlert" type="checkbox" onchange="dontShowAlert('resetAllCreateTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('resetAllCreateTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Save and Close column" data-text-fa="ذخیره و بستن ستون"><?php print_r($GLOBALS['user_language']=="en" ? "Save and Close column":"ذخیره و بستن ستون"); ?></p>
									<input id="toggle-saveAndCloseCreateTable_showAlert" type="checkbox" onchange="dontShowAlert('saveAndCloseCreateTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('saveAndCloseCreateTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Save and Close all columns" data-text-fa="ذخیره و بستن تمام ستون ها"><?php print_r($GLOBALS['user_language']=="en" ? "Save and Close all columns":"ذخیره و بستن تمام ستون ها"); ?></p>
									<input id="toggle-saveAndCloseAllCreateTable_showAlert" type="checkbox" onchange="dontShowAlert('saveAndCloseAllCreateTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('saveAndCloseAllCreateTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Save all columns" data-text-fa="ذخیره تمام ستون ها"><?php print_r($GLOBALS['user_language']=="en" ? "Save all columns":"ذخیره تمام ستون ها"); ?></p>
									<input id="toggle-saveAllCreateTable_showAlert" type="checkbox" onchange="dontShowAlert('saveAllCreateTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('saveAllCreateTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Clear all columns info" data-text-fa="پاکسازی اطلاعات تمام ستون ها"><?php print_r($GLOBALS['user_language']=="en" ? "Clear all columns info":"پاکسازی اطلاعات تمام ستون ها"); ?></p>
									<input id="toggle-clearAllCreateTable_showAlert" type="checkbox" onchange="dontShowAlert('clearAllCreateTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('clearAllCreateTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Edit select box option" data-text-fa="ویرایش گزینه از جعبه انتخاب"><?php print_r($GLOBALS['user_language']=="en" ? "Edit select box option":"ویرایش گزینه از جعبه انتخاب"); ?></p>
									<input id="toggle-copySelectOpt_showAlert" type="checkbox" onchange="dontShowAlert('copySelectOpt_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('copySelectOpt_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Delete select box option" data-text-fa="حذف گزینه از جعبه انتخاب"><?php print_r($GLOBALS['user_language']=="en" ? "Delete select box option":"حذف گزینه از جعبه انتخاب"); ?></p>
									<input id="toggle-deleteSelectOpt_showAlert" type="checkbox" onchange="dontShowAlert('deleteSelectOpt_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('deleteSelectOpt_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Edit Checkbox Option" data-text-fa="ویرایش گزینه (checkbox)"><?php print_r($GLOBALS['user_language']=="en" ? "Edit Checkbox Option":"ویرایش گزینه (checkbox)"); ?></p>
									<input id="toggle-copyCheckboxOpt_showAlert" type="checkbox" onchange="dontShowAlert('copyCheckboxOpt_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('copyCheckboxOpt_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Delete checkbox option" data-text-fa="حذف گزینه (checkbox)"><?php print_r($GLOBALS['user_language']=="en" ? "Delete checkbox option":"حذف گزینه (checkbox)"); ?></p>
									<input id="toggle-deleteCheckboxOpt_showAlert" type="checkbox" onchange="dontShowAlert('deleteCheckboxOpt_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('deleteCheckboxOpt_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
							</div>
							<h4 class="card-title data-text" data-text-en="Table adding" data-text-fa="افزودن جدول"><?php print_r($GLOBALS['user_language']=="en" ? "Table adding":"افزودن جدول"); ?></h4>
							<div class="row mb-3">
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Delete table" data-text-fa="حذف جدول"><?php print_r($GLOBALS['user_language']=="en" ? "Delete table":"حذف جدول"); ?></p>
									<input id="toggle-deleteDatabaseTable_showAlert" type="checkbox" onchange="dontShowAlert('deleteDatabaseTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('deleteDatabaseTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
								<div class="col-md-12">
									<p class="category data-text" data-text-en="Skip database" data-text-fa="رد کردن جدول"><?php print_r($GLOBALS['user_language']=="en" ? "Skip database":"رد کردن جدول"); ?></p>
									<input id="toggle-skipDatabaseTable_showAlert" type="checkbox" onchange="dontShowAlert('skipDatabaseTable_showAlert', ($(this).is(':checked') ? true:false));" <?php if(getUserSetting('skipDatabaseTable_showAlert')=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php if(isset($op_admin) && $op_admin){ ?>
		<div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
			<div class="card">
				<div class="card-header">
					<h4 class="card-title data-text" data-text-en="Developer Settings" data-text-fa="تنظیمات توسعه دهنده"><?php print_r($GLOBALS['user_language']=="en" ? "Developer Settings":"تنظیمات توسعه دهنده"); ?></h4>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12 mb-4">
							<h4 class="card-title data-text" data-text-en="Developer mode" data-text-fa="حالت توسعه دهنده"><?php print_r($GLOBALS['user_language']=="en" ? "Developer mode":"حالت توسعه دهنده"); ?></h4>
							<div class="row mb-3">
								<div class="col-md-12">
									<input id="toggle-developer_mode" type="checkbox" onchange="developer_mode($(this).is(':checked').toString(),this)" <?php if(getUserSetting("developer-mode")=="true"){?>checked<?php } ?> name="checkbox" class="bootstrap-switch developer_mode_input" data-on-label="<i class='tim-icons icon-check-2'></i>" data-off-label="<i class='tim-icons icon-simple-remove'></i>">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php }?>
</div>
<script>
	function scrollSettings($name,$val){
		switch ($name) {
			case "scroll-top":
				$needToHide=$(".scroll-btn-top");
				$last_val=$($val).is(':checked').toString();
				scroll_top=$last_val;
				if($last_val=="true"){
					$needToHide.removeClass("hide");
				}else{
					$needToHide.addClass("hide");
				}
				if(scroll_top=="false" && move_top=="false" && move_bottom=="false" && scroll_bottom=="false"){
					$("#scroller_btn").addClass("hide");
				}else{
					$("#scroller_btn").removeClass("hide");
				}
			break;
			case "move-top":
				$needToHide=$(".scroll-btn-up");
				$last_val=$($val).is(':checked').toString();
				move_top=$last_val;
				if($last_val=="true"){
					$needToHide.removeClass("hide");
				}else{
					$needToHide.addClass("hide");
				}
				if(scroll_top=="false" && move_top=="false" && move_bottom=="false" && scroll_bottom=="false"){
					$("#scroller_btn").addClass("hide");
				}else{
					$("#scroller_btn").removeClass("hide");
				}
			break;
			case "move-bottom":
				$needToHide=$(".scroll-btn-bottom");
				$last_val=$($val).is(':checked').toString();
				move_bottom=$last_val;
				if($last_val=="true"){
					$needToHide.removeClass("hide");
				}else{
					$needToHide.addClass("hide");
				}
				if(scroll_top=="false" && move_top=="false" && move_bottom=="false" && scroll_bottom=="false"){
					$("#scroller_btn").addClass("hide");
				}else{
					$("#scroller_btn").removeClass("hide");
				}
			break;
			case "scroll-bottom":
				$needToHide=$(".scroll-btn-down");
				$last_val=$($val).is(':checked').toString();
				scroll_bottom=$last_val;
				if($last_val=="true"){
					$needToHide.removeClass("hide");
				}else{
					$needToHide.addClass("hide");
				}
				if(scroll_top=="false" && move_top=="false" && move_bottom=="false" && scroll_bottom=="false"){
					$("#scroller_btn").addClass("hide");
				}else{
					$("#scroller_btn").removeClass("hide");
				}
			break;
		}
		newUserSetting($name,$last_val);
	}
	function toggleColor(color) {
		$("[data='red']").attr("data", color);
		$("[data='primary']").attr("data", color);
		$("[data='green']").attr("data", color);
		$("[data='blue']").attr("data", color);
		$("[data='orange']").attr("data", color);
		data_color_def = color;
		newUserSetting("data-color-default",color);
	}
	function navbarFix($this){
		if($($this).is(':checked')==true){
			fixed_menu="true";
			newUserSetting("fixed-menu","true");
			if(main_panel_scroll.scrollTop()>0){
				if(fixed_menu=="true"){
					$(".custom_navbar").addClass("fixed_nav");
					$("body").addClass("fixed-nav");
				}
			}
		}else{
			$(".custom_navbar").removeClass("fixed_nav");
			$("body").removeClass("fixed-nav");
			fixed_menu="false";
			newUserSetting("fixed-menu","false");
		}
	}
	$(document).on('change', '#toggle-color', function() {
		toggleColor($(this).val());
	});
	$(document).on('change', '#toggle-maximum', function() {
		maximum_number_of_allowed_page=$(this).val();
		reload_current_page=0;
		newUserSetting("maximum-page",$(this).val());
		if(number_of_loaded_page>$(this).val()){
			extra_loaded_page=(number_of_loaded_page-$(this).val());
			for(i=0;i<=extra_loaded_page-1;i++){
				deleted_page=name_of_loaded_page.shift();
				if(deleted_page==current_page){
					reload_current_page==1;
				}
				delete page_loaded[deleted_page];
				delete_urlHtmlId = deleted_page.replace(/\?/gi, "_-_QQ_-_").replace(/\=/gi, "_-_EE_-_").replace(/\&/gi, "_-_AA_-_").replace(/\./gi, "_-_--_-_");
				$("#"+delete_urlHtmlId).remove();
				number_of_loaded_page--;
				if(i==(extra_loaded_page-1)){
					pageLoader("reload?page=" + current_page.replace(/\?/gi, "_-_QQQ_-_").replace(/\=/gi, "_-_EEE_-_").replace(/\&/gi, "_-_AAA_-_").replace(/\./gi, "_-_---_-_"));
					reload_current_page=0;
				}
			}
		}
	});
	$(".selectpicker").selectpicker({
		iconBase: "tim-icons",
		tickIcon: "icon-check-2"
	});
	bsSwitcher();
	$("#toggle-language").selectpicker("val",language);
	$("#toggle-maximum").selectpicker("val",maximum_number_of_allowed_page);
	$("#toggle-theme").selectpicker("val",theme_def);
	$("#toggle-color").selectpicker("val",data_color_def);
	$("#toggle-fixed_navbar").prop("checked",(fixed_menu=="true" ? true:false)).change();
	$("#toggle-sidebar_mini").prop("checked",(sidebar_mini_active==true ? true:false)).change();
	$("#toggle-scroll_top").prop("checked",(scroll_top=="true" ? true:false)).change();
	$("#toggle-move_top").prop("checked",(move_top=="true" ? true:false)).change();
	$("#toggle-move_bottom").prop("checked",(move_bottom=="true" ? true:false)).change();
	$("#toggle-scroll_bottom").prop("checked",(scroll_bottom=="true" ? true:false)).change();
	<?php if (isset($op_admin) && $op_admin) { ?>
	$("#toggle-developer_mode").prop("checked",(developer_mode_def=="true" ? true:false)).change();
	<?php } ?>

	$("#toggle-backToTable_showAlert").prop("checked",(alertShowerSetting['backToTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-clearInputs_showAlert").prop("checked",(alertShowerSetting['clearInputs_showAlert']=="true" ? true:false)).change();
	$("#toggle-resetInputs_showAlert").prop("checked",(alertShowerSetting['resetInputs_showAlert']=="true" ? true:false)).change();
	$("#toggle-deleteThis_showAlert").prop("checked",(alertShowerSetting['deleteThis_showAlert']=="true" ? true:false)).change();
	$("#toggle-deleteAllThis_showAlert").prop("checked",(alertShowerSetting['deleteAllThis_showAlert']=="true" ? true:false)).change();
	$("#toggle-reorderTable_showAlert").prop("checked",(alertShowerSetting['reorderTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-clearCreateTable_showAlert").prop("checked",(alertShowerSetting['clearCreateTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-skipNewCreateTable_showAlert").prop("checked",(alertShowerSetting['skipNewCreateTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-skipNewAllCreateTable_showAlert").prop("checked",(alertShowerSetting['skipNewAllCreateTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-deleteCreateTable_showAlert").prop("checked",(alertShowerSetting['deleteCreateTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-deleteAllCreateTable_showAlert").prop("checked",(alertShowerSetting['deleteAllCreateTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-resetCreateTable_showAlert").prop("checked",(alertShowerSetting['resetCreateTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-resetAllCreateTable_showAlert").prop("checked",(alertShowerSetting['resetAllCreateTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-saveAndCloseCreateTable_showAlert").prop("checked",(alertShowerSetting['saveAndCloseCreateTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-saveAndCloseAllCreateTable_showAlert").prop("checked",(alertShowerSetting['saveAndCloseAllCreateTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-saveAllCreateTable_showAlert").prop("checked",(alertShowerSetting['saveAllCreateTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-clearAllCreateTable_showAlert").prop("checked",(alertShowerSetting['clearAllCreateTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-deleteDatabaseTable_showAlert").prop("checked",(alertShowerSetting['deleteDatabaseTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-skipDatabaseTable_showAlert").prop("checked",(alertShowerSetting['skipDatabaseTable_showAlert']=="true" ? true:false)).change();
	$("#toggle-copySelectOpt_showAlert").prop("checked",(alertShowerSetting['copySelectOpt_showAlert']=="true" ? true:false)).change();
	$("#toggle-deleteSelectOpt_showAlert").prop("checked",(alertShowerSetting['deleteSelectOpt_showAlert']=="true" ? true:false)).change();
	$("#toggle-copyCheckboxOpt_showAlert").prop("checked",(alertShowerSetting['copyCheckboxOpt_showAlert']=="true" ? true:false)).change();
	$("#toggle-deleteCheckboxOpt_showAlert").prop("checked",(alertShowerSetting['deleteCheckboxOpt_showAlert']=="true" ? true:false)).change();
	$("#toggle-deleteFileManager_showAlert").prop("checked",(alertShowerSetting['deleteFileManager_showAlert']=="true" ? true:false)).change();
</script>

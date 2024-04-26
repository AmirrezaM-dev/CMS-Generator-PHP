<!-- fad fa-spin fa-spinner-third -->
<!-- create-columns-success -->
<?php
	$res_table_config = $connection->query("SELECT * FROM " . $sub_name . "table_config WHERE lock_admin_id='" . $_SESSION["username"] . "'");
	$table_config = ($res_table_config->rowCount() != 0 ? $res_table_config->fetch() : 0);
	$table_name = $sub_name."table_config";
	$res_table_id = $connection->query("SELECT * FROM " . $sub_name . "table_config WHERE current_name='" . $table_name . "' AND created=1 AND creatable=1 AND visible=1 OR current_name='" . $table_name . "' AND created=1 AND '" . $op_admin . "'=1");
	if($res_table_id->rowCount() != 0){
		$table_get = $res_table_id->fetch();
		$table_id = $table_get['id'];
		if(checkPermission(1, $table_id, "read", $table_get['act'], "") && checkPermission("group_array_full", getTableByName($sub_name."table_column_mode")["id"], "read", getTableByName($sub_name."table_column_mode")["act"], null) && checkPermission("group_array_full", getTableByName($sub_name."table_config")["id"], "create", getTableByName($sub_name."table_config")["act"], null)){
			$primary_buttons='<button onclick="createButtonOperations(\'save\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-success data-original-title data-text save-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will save this column and its will still stay here for edit !" data-original-title-fa="با فشردن این دکمه شما این ستون را ذخیره خواهید نمود و این ستون برای بازنگری و ویرایش همچنان اینجا باقی میماند !" data-text-en="Save" data-text-fa="ذخیره" data-original-title="'.($GLOBALS['user_language'] == "en" ? "By pressing this button you will save this column and its will still stay here for edit !" : "با فشردن این دکمه شما این ستون را ذخیره خواهید نمود و این ستون برای بازنگری و ویرایش همچنان اینجا باقی میماند !").'" data-placement="top">'.($GLOBALS['user_language'] == "en" ? "Save" : "ذخیره").'</button> <button onclick="createButtonOperations(\'save_close\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-info data-original-title data-text save_close-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will save this column and its will close !" data-original-title-fa="با فشردن این دکمه شما این ستون را ذخیره خواهید نمود و بسته میشود !" data-text-en="Save & Close" data-text-fa="ذخیره و خروج" data-original-title="'.($GLOBALS['user_language'] == "en" ? "By pressing this button you will save this column and its will close !" : "با فشردن این دکمه شما این ستون را ذخیره خواهید نمود و بسته میشود !").'" data-placement="top">'.($GLOBALS['user_language'] == "en" ? "Save & Close" : "ذخیره و خروج").'</button> <button onclick="createButtonOperations(\'add\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-info data-original-title data-text add-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will copy all fields of this column !" data-original-title-fa="با فشردن این دکمه تمام فیلد ها کپی خواهند شد !" data-text-en="Copy" data-text-fa="کپی" data-original-title="'.($GLOBALS['user_language'] == "en" ? "By pressing this button you will copy all fields of this column !" : "با فشردن این دکمه تمام فیلد ها کپی خواهند شد !").'" data-placement="top">'.($GLOBALS['user_language'] == "en" ? "Copy" : "کپی").'</button> <button onclick="createButtonOperations(\'clear\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-warning data-original-title data-text clear-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will clear all fields of this column !" data-original-title-fa="با فشردن این دکمه تمام فیلد ها پاکسازی خواهند شد !" data-text-en="Clear" data-text-fa="پاکسازی" data-original-title="'.($GLOBALS['user_language'] == "en" ? "By pressing this button you will clear all fields of this column !" : "با فشردن این دکمه تمام فیلد ها پاکسازی خواهند شد !").'" data-placement="top">'.($GLOBALS['user_language'] == "en" ? "Clear" : "پاکسازی").'</button> <button onclick="createButtonOperations(\'reset\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-warning data-original-title data-text reset-button-operation" rel="tooltip" data-original-title-en="By pressing this button you will reset all fields of this column !" data-original-title-fa="با فشردن این دکمه تمام فیلد ها بازیابی خواهند شد !" data-text-en="Reset" data-text-fa="بازیابی" data-original-title="'.($GLOBALS['user_language'] == "en" ? "By pressing this button you will reset all fields of this column !" : "با فشردن این دکمه تمام فیلد ها بازیابی خواهند شد !").'" data-placement="top">'.($GLOBALS['user_language'] == "en" ? "Reset" : "بازیابی").'</button> <button onclick="createButtonOperations(\'skip\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-warning data-original-title data-text skip-button-operation" rel="tooltip" data-original-title-en="Skip this column !" data-original-title-fa="رد کردن این ستون !" data-text-en="Skip" data-text-fa="رد کردن" data-original-title="'.($GLOBALS['user_language'] == "en" ? "Skip this column !" : "رد کردن این ستون !").'" data-placement="top">'.($GLOBALS['user_language'] == "en" ? "Skip" : "رد کردن").'</button> <button onclick="createButtonOperations(\'delete\',$(this).parent().parent(),$(this),1);return false;" class="btn btn-danger data-original-title data-text delete-button-operation" rel="tooltip" data-original-title-en="Delete this column for ever !" data-original-title-fa="حذف این ستون برای همیشه !" data-text-en="Delete" data-text-fa="حذف" data-original-title="'.($GLOBALS['user_language'] == "en" ? "Delete this column for ever !" : "حذف این ستون برای همیشه !").'" data-placement="top">'.($GLOBALS['user_language'] == "en" ? "Delete" : "حذف").'</button>';
?>
<div class="col-md-10 mr-auto ml-auto create_database">
	<?php
		if(($table_config != 0 ? $table_config['level'] : 0)!=0){
	?>
		<div class="preloader_table"><div class="loading-icon"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></div>
	<?php
		}else{
	?>
		<div class="preloader_table_2"><div class="loading-icon"><div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></div>
	<?php
		}
	?>
	<!-- Wizard container -->
	<div class="wizard-container">
		<div class="card card-wizard" data-color="primary" id="wizardProfile">
			<form onsubmit="return false;">
				<div class="card-header text-center">
					<h3 class="card-title data-text" data-text-en="Build your database table" data-text-fa="ساخت جدول برای دیتابیس">
						<?php print_r($GLOBALS['user_language'] == "en" ? "Build your database table" : "ساخت جدول برای دیتابیس"); ?>
					</h3>
					<!-- <h5 class="description">This will create table inside of database and its will able to manage from panel</h5> -->
					<div class="wizard-navigation">
						<div class="progress-with-circle">
							<div class="progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="1" aria-valuemax="3" style="width: 21%;"></div>
						</div>
						<ul class="custom_transition">
							<li class="nav-item">
								<a class="nav-link active" href="#table_information" data-toggle="tab">
									<i id="table_information-icon" class="fal fa-server"></i>
									<p class="data-text" data-text-fa="اطلاعات" data-text-en="Information"><?php print_r($GLOBALS['user_language'] == "en" ? "Information" : "اطلاعات"); ?></p>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#table_setting" data-toggle="tab">
									<i id="table_setting-icon" class="fal fa-cogs"></i>
									<p class="data-text" data-text-fa="تنظیمات" data-text-en="Settings"><?php print_r($GLOBALS['user_language'] == "en" ? "Settings" : "تنظیمات"); ?></p>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#table_columns" data-toggle="tab">
									<i id="table_columns-icon" class="fal fa-line-columns"></i>
									<p class="data-text" data-text-fa="ستون ها" data-text-en="Columns"><?php print_r($GLOBALS['user_language'] == "en" ? "Columns" : "ستون ها"); ?></p>
								</a>
							</li>
							<?php
								if(checkPermission("group_array_full", getTableByName($sub_name."menu")["id"], "create", getTableByName($sub_name."menu")["act"], null) && 0){
							?>
								<li class="nav-item">
									<a class="nav-link" href="#table_menu" data-toggle="tab">
										<i id="table_menu-icon" class="fal fa-bars"></i>
										<p class="data-text" data-text-fa="منو" data-text-en="Menu"><?php print_r($GLOBALS['user_language'] == "en" ? "Menu" : "منو"); ?></p>
									</a>
								</li>
							<?php
								}
							?>
						</ul>
					</div>
				</div>
				<div class="row justify-content-center text-center mb-5">
					<div class="col-12">
						<button onclick="buttonsOperations('save',this);return false;" class="btn btn-success data-text" rel="tooltip" data-text-en="Save" data-text-fa="ذخیره"><?php print_r($GLOBALS['user_language'] == "en" ? "Save" : "ذخیره"); ?></button>

						<button onclick="buttonsOperations('save_close',this);return false;" class="btn btn-info data-text" data-text-en="Save & Close" data-text-fa="ذخیره و خروج"><?php print_r($GLOBALS['user_language'] == "en" ? "Save & Close" : "ذخیره و خروج"); ?></button>

						<button onclick="buttonsOperations('skip',this);return false;" class="btn btn-warning data-text" data-text-en="Skip" data-text-fa="رد کردن"><?php print_r($GLOBALS['user_language'] == "en" ? "Skip" : "رد کردن"); ?></button>

						<button onclick="buttonsOperations('delete',this);return false;" class="btn btn-danger data-text" data-text-en="Delete" data-text-fa="حذف"><?php print_r($GLOBALS['user_language'] == "en" ? "Delete" : "حذف"); ?></button>
					</div>
				</div>
				<div class="card-body">
					<div class="tab-content">
						<div class="tab-pane show active" id="table_information">
							<h5 class="info-text data-text" data-text-en="Basic table informations" data-text-fa="اطلاعات پایه جدول"><?php print_r($GLOBALS['user_language'] == "en" ? "Basic table informations" : "اطلاعات پایه جدول"); ?></h5>
							<div class="row justify-content-center mt-5">
								<div class="col-lg-8 col-md-6 col-sm-6">
									<div class="input-group <?php print_r($table_config != 0 ? "has-success" : ""); ?>">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<i class="far fa-database"></i>
											</div>
										</div>
										<input type="text" id="current_name" data-placeholder-en="Table Name" data-placeholder-fa="نام جدول" placeholder="<?php print_r($GLOBALS['user_language'] == "en" ? "Table Name" : "نام جدول"); ?>" autocomplete="off" class="form-control data-placeholder force-left on-press-enter convert-to-lowercase" <?php print_r($table_config != 0 ? "value='" . preg_replace('/' . preg_quote($sub_name, '/') . '/', "", ($table_config['created']==0 ? $table_config['current_name']:($table_config['new_name'] ? $table_config['new_name']:$table_config['current_name'])), 1) . "'" : ""); ?> >
									</div>
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<i class="fal fa-info"></i>
											</div>
										</div>
										<input type="text" id="description_name_fa" data-placeholder-en="Persian descriptive name" data-placeholder-fa="نام توصیفی فارسی" placeholder="<?php print_r($GLOBALS['user_language'] == "en" ? "Persian descriptive name" : "نام توصیفی فارسی"); ?>" autocomplete="off" class="form-control data-placeholder force-right on-press-enter convert-to-lowercase <?php print_r($table_config != 0 ? "text-right font-persian" : ""); ?>" <?php print_r($table_config != 0 ? "value='" . $table_config['description_name_fa'] . "'" : ""); ?>>
									</div>
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<i class="fal fa-info-circle"></i>
											</div>
										</div>
										<textarea id="description_info_fa" data-placeholder-en="Persian description" data-placeholder-fa="توضیحات فارسی" placeholder="<?php print_r($GLOBALS['user_language'] == "en" ? "Persian description" : "توضیحات فارسی"); ?>" autocomplete="off" class="form-control data-placeholder force-right on-press-enter convert-to-lowercase <?php print_r($table_config != 0 ? "text-right font-persian" : ""); ?>"><?php print_r($table_config != 0 ? $table_config['description_info_fa'] : ""); ?></textarea>
									</div>
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<i class="fal fa-info"></i>
											</div>
										</div>
										<input type="text" id="description_name_en" data-placeholder-en="English descriptive name" data-placeholder-fa="نام توصیفی انگلیسی" placeholder="<?php print_r($GLOBALS['user_language'] == "en" ? "English descriptive name" : "نام توصیفی انگلیسی"); ?>" autocomplete="off" class="form-control data-placeholder force-left on-press-enter convert-to-lowercase <?php print_r($table_config != 0 ? "text-left font-english" : ""); ?>" <?php print_r($table_config != 0 ? "value='" . $table_config['description_name_en'] . "'" : ""); ?>>
									</div>
									<div class="input-group">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<i class="fal fa-info-circle"></i>
											</div>
										</div>
										<textarea id="description_info_en" data-placeholder-en="English description" data-placeholder-fa="توضیحات انگلیسی" placeholder="<?php print_r($GLOBALS['user_language'] == "en" ? "English description" : "توضیحات انگلیسی"); ?>" autocomplete="off" class="form-control data-placeholder force-left on-press-enter convert-to-lowercase <?php print_r($table_config != 0 ? "text-left font-english" : ""); ?>"><?php print_r($table_config != 0 ? $table_config['description_info_en'] : ""); ?></textarea>
									</div>
								</div>
							</div>
							<script>
								$(document).on('focus blur click keyup keydown', '#current_name, #description_name_fa, #description_name_en, .input_current_name, .input_description_name_fa, .input_description_name_en', function() {
									if (getValue(this) != "" && getValue(this).length != "" && $(this).parent().children("label.error").hasClass("error-empty")) {
										$(this).parent().removeClass("has-danger").children("label.error").remove();
									}
								});
								$(document).on('change', '#current_name', function() {
									if (getValue(this) != "" && getValue(this).length != "" && $(this).parent().children("label.error").hasClass("error-duplicate")) {
										$(this).parent().removeClass("has-danger").children("label.error").remove();
									}
								});
							</script>
						</div>
						<div class="tab-pane" id="table_setting">
							<h5 class="info-text data-text" data-text-en="Table Settings" data-text-fa="تنظیمات جدول"><?php print_r($GLOBALS['user_language'] == "en" ? "Table Settings" : "تنظیمات جدول"); ?></h5>
							<div class="row justify-content-center mt-5">
								<div class="col-lg-8 col-md-6 col-sm-6">
									<div class="row">
										<label class="col-sm-2 col-form-label <?php print_r($GLOBALS['user_language'] == "en" ? "text-right" : "text-left"); ?> data-text" data-text-en="Insertable ?" data-text-fa="قابل درج ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Insertable ?" : "قابل درج ؟"); ?></label>
										<div class="col-sm-10 col-sm-offset-1 checkbox-radios cursor-pointer create_setting create_setting">
											<div class="form-check">
												<label class="form-check-label create-setting-input">
													<input id="creatable_table" class="form-check-input" type="checkbox" <?php print_r($table_config != 0 ?  ($table_config['creatable']==1 ? "checked":""): "checked"); ?> >
													<span class="form-check-sign"></span>
													<label class="data-text" data-text-en="By checking this, admins will able to insert on this table by having enough permission !" data-text-fa="با تایید این گزینه ، مدیران قادر خواهند بود تا با داشتن دسترسی کافی در این جدول درج نمایند !"><?php print_r($GLOBALS['user_language'] == "en" ? "By checking this, admins will able to insert on this table by having enough permission !" : "با تایید این گزینه ، مدیران قادر خواهند بود تا با داشتن دسترسی کافی در این جدول درج نمایند !"); ?></label>
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-8 col-md-6 col-sm-6">
									<div class="row">
										<label class="col-sm-2 col-form-label <?php print_r($GLOBALS['user_language'] == "en" ? "text-right" : "text-left"); ?> data-text" data-text-en="Visible ?" data-text-fa="نمایان ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Visible ?" : "نمایان ؟"); ?></label>
										<div class="col-sm-10 col-sm-offset-1 checkbox-radios cursor-pointer create_setting">
											<div class="form-check">
												<label class="form-check-label create-setting-input">
													<input id="visible_table" class="form-check-input" type="checkbox" <?php print_r($table_config != 0 ?  ($table_config['visible']==1 ? "checked":""): "checked"); ?> >
													<span class="form-check-sign"></span>
													<label class="data-text" data-text-en="By checking this, admins will able to see inside of this table by having enough permission !" data-text-fa="با تایید این گزینه ، مدیران قادر خواهند بود تا تمامی موارد این جدول را با داشتن دسترسی کافی مشاهده نمایند !"><?php print_r($GLOBALS['user_language'] == "en" ? "By checking this, admins will able to see inside of this table by having enough permission !" : "با تایید این گزینه ، مدیران قادر خواهند بود تا تمامی موارد این جدول را با داشتن دسترسی کافی مشاهده نمایند !"); ?></label>
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-8 col-md-6 col-sm-6">
									<div class="row">
										<label class="col-sm-2 col-form-label <?php print_r($GLOBALS['user_language'] == "en" ? "text-right" : "text-left"); ?> data-text" data-text-en="Editable ?" data-text-fa="قابل ویرایش ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Editable ?" : "قابل ویرایش ؟"); ?></label>
										<div class="col-sm-10 col-sm-offset-1 checkbox-radios cursor-pointer create_setting">
											<div class="form-check">
												<label class="form-check-label create-setting-input">
													<input id="editable_table" class="form-check-input" type="checkbox" <?php print_r($table_config != 0 ?  ($table_config['editable']==1 ? "checked":""): "checked"); ?> >
													<span class="form-check-sign"></span>
													<label class="data-text" data-text-en="By checking this, admins will able to edit everything inside of this table by having enough permission !" data-text-fa="با تایید این گزینه ، مدیران قادر خواهند بود تا با داشتن دسترسی کافی تمامی موارد این جدول را ویرایش نمایند !"><?php print_r($GLOBALS['user_language'] == "en" ? "By checking this, admins will able to edit everything inside of this table by having enough permission !" : "با تایید این گزینه ، مدیران قادر خواهند بود تا با داشتن دسترسی کافی تمامی موارد این جدول را ویرایش نمایند !"); ?></label>
												</label>
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-8 col-md-6 col-sm-6">
									<div class="row">
										<label class="col-sm-2 col-form-label <?php print_r($GLOBALS['user_language'] == "en" ? "text-right" : "text-left"); ?> data-text" data-text-en="Removable ?" data-text-fa="قابل حذف ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Removable ?" : "قابل حذف ؟"); ?></label>
										<div class="col-sm-10 col-sm-offset-1 checkbox-radios cursor-pointer create_setting">
											<div class="form-check">
												<label class="form-check-label create-setting-input">
													<input id="removable_table" class="form-check-input" type="checkbox" <?php print_r($table_config != 0 ?  ($table_config['removable']==1 ? "checked":""): "checked"); ?> >
													<span class="form-check-sign"></span>
													<label class="data-text" data-text-en="By checking this, admins will able to delete everything inside of this table by having enough permission !" data-text-fa="با تایید این گزینه ، مدیران قادر خواهند بود تا با داشتن دسترسی کافی تمامی موارد این جدول را حذف نمایند !"><?php print_r($GLOBALS['user_language'] == "en" ? "By checking this, admins will able to delete everything inside of this table by having enough permission !" : "با تایید این گزینه ، مدیران قادر خواهند بود تا با داشتن دسترسی کافی تمامی موارد این جدول را حذف نمایند !"); ?></label>
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="table_columns">
							<h5 class="info-text data-text" data-text-en="Table Columns" data-text-fa="ستون های جدول"><?php print_r($GLOBALS['user_language'] == "en" ? "Table Columns" : "ستون های جدول"); ?></h5>
							<div class="row justify-content-center text-center mb-5">
								<div class="col-12">

									<button onclick="createButtonOperations('add','',$(this),0);return false;" class="btn btn-success data-original-title data-text" rel="tooltip" data-original-title-en="Add new column to the table" data-original-title-fa="افزودن ستون جدید به جدول" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Add new column to the table" : "افزودن ستون جدید به جدول"); ?>" data-placement="top" data-text-en="Add" data-text-fa="افزودن"><?php print_r($GLOBALS['user_language'] == "en" ? "Add" : "افزودن"); ?></button>

									<button onclick="saveAll()" class="btn btn-success data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Save all of columns" data-original-title-fa="ذخیره همه ستون ها" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Save all of columns" : "ذخیره همه ستون ها"); ?>" data-placement="top" data-text-en="Save" data-text-fa="ذخیره"><?php print_r($GLOBALS['user_language'] == "en" ? "Save" : "ذخیره"); ?></button>

									<button onclick="saveAndCloseAll()" class="btn btn-info data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Save all of columns and close" data-original-title-fa="ذخیره همه ستون ها و خروج" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Save all of columns and close" : "ذخیره همه ستون ها و خروج"); ?>" data-placement="top" data-text-en="Save & Close" data-text-fa="ذخیره و خروج"><?php print_r($GLOBALS['user_language'] == "en" ? "Save & Close" : "ذخیره و خروج"); ?></button>

									<button onclick="clearAll()" class="btn btn-warning data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Clear all columns which were going to add to the table !" data-original-title-fa="تمام ستون هایی را که قرار بود به جدول اضافه شوند پاکسازی کنید !" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Clear all columns which were going to add to the table !" : "تمام ستون هایی را که قرار بود به جدول اضافه شوند پاکسازی کنید !"); ?>" data-placement="top" data-text-en="Clear" data-text-fa="پاکسازی"><?php print_r($GLOBALS['user_language'] == "en" ? "Clear" : "پاکسازی"); ?></button>

									<button onclick="resetAll()" class="btn btn-warning data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Reset every fields which was editing" data-original-title-fa="بازیابی تمام فیلد هایی که در حال ویرایش بودند" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Reset every fields which was editing" : "بازیابی تمام فیلد هایی که در حال ویرایش بودند"); ?>" data-placement="top" data-text-en="Reset" data-text-fa="بازیابی"><?php print_r($GLOBALS['user_language'] == "en" ? "Reset" : "بازیابی"); ?></button>

									<button onclick="skipAll()" class="btn btn-warning data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Skip every columns which was editing" data-original-title-fa="رد کردن تمام ستون هایی که در حال ویرایش بودند" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Skip every columns which was editing" : "رد کردن تمام ستون هایی که در حال ویرایش بودند"); ?>" data-placement="top" data-text-en="Skip" data-text-fa="رد کردن"><?php print_r($GLOBALS['user_language'] == "en" ? "Skip" : "رد کردن"); ?></button>

									<button onclick="deleteAll()" class="btn btn-danger data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Delete all columns which were going to add to the table !" data-original-title-fa="تمام ستون هایی را که قرار بود به جدول اضافه شوند حذف کنید !" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Delete all columns which were going to add to the table !" : "تمام ستون هایی را که قرار بود به جدول اضافه شوند حذف کنید !"); ?>" data-placement="top" data-text-en="Delete" data-text-fa="حذف"><?php print_r($GLOBALS['user_language'] == "en" ? "Delete" : "حذف"); ?></button>

									<button onclick="$('#database_option').toggleClass('hide');return false;" class="btn data-original-title data-text" rel="tooltip" data-original-title-en="Use the options that are related to the database !" data-original-title-fa="استفاده از امکاناتی که مرتبط با دیتابیس میباشد !" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Use the options that are related to the database !" : "استفاده از امکاناتی که مرتبط با دیتابیس میباشد !"); ?>" data-placement="top" data-text-en="Database Options" data-text-fa="امکانات دیتابیس"><?php print_r($GLOBALS['user_language'] == "en" ? "Database Options" : "امکانات دیتابیس"); ?></button>

									<!-- <button onclick="$('#regex_option').toggleClass('hide');return false;" class="btn data-original-title data-text" rel="tooltip" data-original-title-en="*" data-original-title-fa="*" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "*" : "*"); ?>" data-placement="top" data-text-en="Regular expressions" data-text-fa="عبارات منظم"><?php print_r($GLOBALS['user_language'] == "en" ? "Regular expressions" : "عبارات منظم"); ?></button> -->

									<!-- <button onclick="$('#regex_option, #database_option').addClass('hide');return false;" class="btn data-original-title data-text" rel="tooltip" data-original-title-en="Disable all custom options" data-original-title-fa="لغو گزینه های سفارشی سازی" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Disable all custom options" : "لغو گزینه های سفارشی سازی"); ?>" data-placement="top" data-text-en="Disable Custom" data-text-fa="لغو سفارشی سازی"><?php print_r($GLOBALS['user_language'] == "en" ? "Disable Custom" : "لغو سفارشی سازی"); ?></button> -->

								</div>
							</div>

							<div id="database_option" class="row justify-content-center text-center mb-5 mt-5 hide create-columns-manager">
								<h5 class="info-text data-text col-12" data-text-en="Database Options" data-text-fa="امکانات دیتابیس"><?php print_r($GLOBALS['user_language'] == "en" ? "Database Options" : "امکانات دیتابیس"); ?></h5>
								<div class="col-lg-4 col-md-6 col-12">
									<div class="form-group">
										<select id="do-select-table" class="selectpicker data-title select-all-opt" data-title-en="Tables" data-title-fa="جدول ها" data-style="btn btn-primary" multiple title="<?php if($GLOBALS['user_language']=='en'){?>Tables<?php }else{?>جدول ها<?php } ?>" data-size="7" data-live-search="true">
											<option class="data-text" data-text-en="Select All" data-text-fa="انتخاب همه" value="-1"><?php if($GLOBALS['user_language']=='en'){?>Select All<?php }else{?>انتخاب همه<?php } ?></option>
											<?php
												$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_config");
												while($tables=$res_tables->fetch()){
													if(checkPermission(1,$tables['id'],"read",$tables['act'],null)==1){
											?>
													<option class="data-text" value="<?php print_r($tables['id']); ?>" data-text-en="<?php print_r($tables['description_name_en']); ?>" data-text-fa="<?php print_r($tables['description_name_fa']); ?>">
														<?php print_r($tables['description_name_'.$GLOBALS['user_language']]); ?>
													</option>
											<?php
													}
												}
											?>
										</select>
										<span class="form-text data-text" data-text-en="Select a table which you want change a permission of" data-text-fa="انتخاب جدول برای ایجاد تغیرات در دسترسی آن"><?php print_r($GLOBALS['user_language']=="en" ? "Select a table which you want change a permission of":"انتخاب جدول برای ایجاد تغیرات در دسترسی آن"); ?></span>
									</div>
								</div>
								<div class="col-lg-4 col-md-6 col-12">
									<div class="form-group">
										<select id="do-select-column" disabled class="selectpicker data-title select-all-opt" data-title-en="Columns" data-title-fa="ستون ها" data-style="btn btn-primary" multiple title="<?php print_r($GLOBALS['user_language']=="en" ? "Columns":"ستون ها"); ?>" data-size="7" data-live-search="true">
											<option disabled selected class="data-text" data-text-en="Please select a table" data-text-fa="لطفاً یک جدول انتخاب کنید" value="-"><?php if($GLOBALS['user_language']=='en'){?>Please select a table<?php }else{?>لطفاً یک جدول انتخاب کنید<?php } ?></option>
										</select>
										<span class="form-text data-text" data-text-en="Select a table which you want change a permission of" data-text-fa="انتخاب جدول برای ایجاد تغیرات در دسترسی آن"><?php print_r($GLOBALS['user_language']=="en" ? "Select a table which you want change a permission of":"انتخاب جدول برای ایجاد تغیرات در دسترسی آن"); ?></span>
									</div>
								</div>
								<div class="col-lg-4 col-12 mb-4">
									<button onclick="databaseOperations('copyDatabaseColumns');return false;" class="btn btn-info data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Copy selected columns to current table columns !" data-original-title-fa="کپی برداری از ستون های انتخاب شده برای ستون های جدول فعلی !" data-text-en="Copy" data-text-fa="کپی" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Copy selected columns to current table columns !" : "کپی برداری از ستون های انتخاب شده برای ستون های جدول فعلی !"); ?>" data-placement="top"><?php print_r($GLOBALS['user_language'] == "en" ? "Copy" : "کپی"); ?></button>
									<button onclick="databaseOperations('copyDatabaseColumns_save');return false;" class="btn btn-warning data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Copy selected columns to current table columns and save them to current table !" data-original-title-fa="کپی برداری از ستون های انتخاب شده برای ستون های جدول فعلی و ذخیره خودکار آن ها !" data-text-en="Copy & Save" data-text-fa="کپی و ذخیره" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Copy selected columns to current table columns and save them to current table !" : "کپی برداری از ستون های انتخاب شده برای ستون های جدول فعلی و ذخیره خودکار آن ها !"); ?>" data-placement="top"><?php print_r($GLOBALS['user_language'] == "en" ? "Copy & Save" : "کپی و ذخیره"); ?></button>
								</div>

								<div class="col-12 mt-4">
									<button onclick="databaseOperations('openAll');return false;" class="btn btn-info data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Edit all of saved columns in current table !" data-original-title-fa="ویرایش تمام ستون های ذخیره شده در جدول فعلی !" data-text-en="Edit saved columns" data-text-fa="ویرایش ستون های ذخیره شده" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Edit all of saved columns in current table !" : "ویرایش تمام ستون های ذخیره شده در جدول فعلی !"); ?>" data-placement="top"><?php print_r($GLOBALS['user_language'] == "en" ? "Edit saved columns" : "ویرایش ستون های ذخیره شده"); ?></button>
									<button onclick="databaseOperations('deleteAll');return false;" class="btn btn-danger data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Delete all of columns of current table !" data-original-title-fa="حذف تمام ستون های جدول فعلی !" data-text-en="Delete all saved columns" data-text-fa="حذف همه ستون ها" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Delete all of columns of current table !" : "حذف تمام ستون های جدول فعلی !"); ?>" data-placement="top"><?php print_r($GLOBALS['user_language'] == "en" ? "Delete all saved columns" : "حذف همه ستون ها"); ?></button>
								</div>
							</div>
							<div id="regex_option" class="row justify-content-center text-center mb-5 mt-5 hide create-columns-manager">
								<h5 class="info-text data-text" data-text-en="Regular Expressions" data-text-fa="عبارات منظم"><?php print_r($GLOBALS['user_language'] == "en" ? "Regular Expressions" : "عبارات منظم"); ?></h5>
								<div class="col-12">
									<button onclick="return false;" class="btn btn-success data-text data-original-title" rel="tooltip" data-original-title-en="By pressing this button you will save this column and its will still stay here for edit !" data-original-title-fa="با فشردن این دکمه شما این ستون را ذخیره خواهید نمود و این ستون برای بازنگری و ویرایش همچنان اینجا باقی میماند !" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "By pressing this button you will save this column and its will still stay here for edit !" : "با فشردن این دکمه شما این ستون را ذخیره خواهید نمود و این ستون برای بازنگری و ویرایش همچنان اینجا باقی میماند !"); ?>" data-placement="top" data-text-en="Save" data-text-fa="ذخیره"><?php print_r($GLOBALS['user_language'] == "en" ? "Save" : "ذخیره"); ?></button>
								</div>
							</div>
							<div id="stack-of-columns">
								<?php
									if($table_config){
										$res_column_editing=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_config['id']."' AND editing=1 ORDER BY column_number");
										while($column_editing=$res_column_editing->fetch()){
											if($column_editing['current_name']!="ordering" && $column_editing['current_name']!="act"){
								?>
									<div class="row justify-content-center text-center mt-3 mb-3 pt-3 pb-3 create-columns-manager column_saved_id-<?php print_r($column_editing['id']); ?> saved_column column-saver">
										<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
											<div class="form-group">
												<input class="form-control data-placeholder data-original-title input_current_name input_text on-press-enter-column convert-to-lowercase just-english force-left" rel="tooltip" data-original-title-en="Column name" data-original-title-fa="نام ستون" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Column name" : "نام ستون"); ?>" data-placement="top" data-placeholder-en="Column name" data-placeholder-fa="نام ستون" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Column name":"نام ستون"); ?>" type="text" data-reset-value="<?php print_r($column_editing['created']==0 ? $column_editing['current_name']:($column_editing['new_name'] ? $column_editing['new_name']:$column_editing['current_name'])); ?>" value="<?php print_r($column_editing['created']==0 ? $column_editing['current_name']:($column_editing['new_name'] ? $column_editing['new_name']:$column_editing['current_name'])); ?>">
											</div>
										</div>
										<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
											<div class="form-group">
												<input class="form-control data-placeholder data-original-title input_description_name_fa input_text on-press-enter-column force-right <?php if($column_editing['description_name_fa']!=""){?>text-right font-persian<?php } ?>" rel="tooltip" data-original-title-en="Persian descriptive name" data-original-title-fa="نام توصیفی فارسی" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Persian descriptive name" : "نام توصیفی فارسی"); ?>" data-placement="top" data-placeholder-en="Persian descriptive name" data-placeholder-fa="نام توصیفی فارسی" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Persian descriptive name":"نام توصیفی فارسی"); ?>" type="text" data-reset-value="<?php print_r($column_editing['description_name_fa']); ?>" value="<?php print_r($column_editing['description_name_fa']); ?>">
											</div>
										</div>
										<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
											<div class="form-group">
												<input class="form-control data-placeholder data-original-title input_description_name_en input_text on-press-enter-column force-left <?php if($column_editing['description_name_en']!=""){?>text-left font-english<?php } ?>" rel="tooltip" data-original-title-en="English descriptive name" data-original-title-fa="نام توصیفی انگلیسی" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "English descriptive name" : "نام توصیفی انگلیسی"); ?>" data-placement="top" data-placeholder-en="English descriptive name" data-placeholder-fa="نام توصیفی انگلیسی" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "English descriptive name":"نام توصیفی انگلیسی"); ?>" type="text" data-reset-value="<?php print_r($column_editing['description_name_en']); ?>" value="<?php print_r($column_editing['description_name_en']); ?>">
											</div>
										</div>
										<div class="col-12">
											<div class="form-group">
												<textarea class="form-control data-placeholder data-original-title input_description_info_fa input_text on-press-enter-column force-right <?php if($column_editing['description_info_fa']!="" && !empty($column_editing['description_info_fa'])){?>text-right font-persian<?php } ?>" rel="tooltip" data-original-title-en="Persian description" data-original-title-fa="توضیحات فارسی" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Persian description" : "توضیحات فارسی"); ?>" data-placement="top" data-placeholder-en="Persian description" data-placeholder-fa="توضیحات فارسی" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Persian description":"توضیحات فارسی"); ?>" type="text" data-reset-value="<?php print_r($column_editing['description_info_fa']); ?>"><?php print_r($column_editing['description_info_fa']); ?></textarea>
											</div>
										</div>
										<div class="col-12">
											<div class="form-group">
												<textarea class="form-control data-placeholder data-original-title input_description_info_en input_text on-press-enter-column force-left <?php if($column_editing['description_info_en']!=""){?>text-left font-english<?php } ?>" rel="tooltip" data-original-title-en="English description" data-original-title-fa="توضیحات انگلیسی" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "English description" : "توضیحات انگلیسی"); ?>" data-placement="top" data-placeholder-en="English description" data-placeholder-fa="توضیحات انگلیسی" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "English description":"توضیحات انگلیسی"); ?>" type="text" data-reset-value="<?php print_r($column_editing['description_info_en']); ?>"><?php print_r($column_editing['description_info_en']); ?></textarea>
											</div>
										</div>
										<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios">
											<div class="form-check cursor-pointer">
												<label class="form-check-label">
													<input class="form-check-input input_creatable input_checkbox" type="checkbox" data-reset-value="<?php print_r($column_editing['creatable']); ?>" <?php if($column_editing['creatable']==1){?>checked<?php } ?> >
													<span class="form-check-sign"></span>
													<label class="data-text" data-text-en="Insertable ?" data-text-fa="قابل درج ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Insertable ?" : "قابل درج ؟"); ?></label>
												</label>
											</div>
										</div>
										<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios">
											<div class="form-check cursor-pointer">
												<label class="form-check-label">
													<input class="form-check-input input_visible input_checkbox" type="checkbox" data-reset-value="<?php print_r($column_editing['visible']); ?>" <?php if($column_editing['visible']==1){?>checked<?php } ?> >
													<span class="form-check-sign"></span>
													<label class="data-text" data-text-en="Visible ?" data-text-fa="نمایان ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Visible ?" : "نمایان ؟"); ?></label>
												</label>
											</div>
										</div>
										<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios">
											<div class="form-check cursor-pointer">
												<label class="form-check-label">
													<input class="form-check-input input_editable input_checkbox" type="checkbox" data-reset-value="<?php print_r($column_editing['editable']); ?>" <?php if($column_editing['editable']==1){?>checked<?php } ?> >
													<span class="form-check-sign"></span>
													<label class="data-text" data-text-en="Editable ?" data-text-fa="قابل ویرایش ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Editable ?" : "قابل ویرایش ؟"); ?></label>
												</label>
											</div>
										</div>
										<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios">
											<div class="form-check cursor-pointer">
												<label class="form-check-label">
													<input class="form-check-input input_removable input_checkbox" type="checkbox" data-reset-value="<?php print_r($column_editing['removable']); ?>" <?php if($column_editing['removable']==1){?>checked<?php } ?> >
													<span class="form-check-sign"></span>
													<label class="data-text" data-text-en="Removable ?" data-text-fa="قابل حذف ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Removable ?" : "قابل حذف ؟"); ?></label>
												</label>
											</div>
										</div>
										<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios">
											<div class="form-check cursor-pointer">
												<label class="form-check-label">
													<input class="form-check-input input_visible_table input_checkbox" type="checkbox" data-reset-value="<?php print_r($column_editing['visible_table']); ?>" <?php if($column_editing['visible_table']==1){?>checked<?php } ?> >
													<span class="form-check-sign"></span>
													<label class="data-text" data-text-en="Visible in table ?" data-text-fa="نمایان در جدول ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Visible in table ?" : "نمایان در جدول ؟"); ?></label>
												</label>
											</div>
										</div>
										<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12">
											<div class="form-group">
												<select class="selectpicker data-title input_mode input_select" data-title-en="Modes" data-title-fa="حالت ها" data-style="btn btn-primary" title="<?php if($GLOBALS['user_language']=="en"){?>Modes<?php }else{?>حالت ها<?php } ?>" data-size="7" data-live-search="true" data-reset-value="<?php print_r($column_editing['mode']); ?>">
													<?php
														if($column_editing['created']!=1){
															$modeGet=$column_editing['mode'];
														}else{
															if($column_editing['new_mode']!=0){
																$modeGet=$column_editing['new_mode'];
															}else if($column_editing['mode']!=0){
																$modeGet=$column_editing['mode'];
															}else{
																$modeGet=1;
															}
														}
														?><?php //info search this_is_modes_for_data_tables for see all things about this part ?><?php
														$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_column_mode");
														while($tables=$res_tables->fetch()){
													?>
															<option class="data-text" <?php if($modeGet==$tables["id"]){?>selected<?php } ?> value="<?php print_r($tables["id"]); ?>" data-text-en="<?php print_r($tables["description_name_en"]); ?>" data-text-fa="<?php print_r($tables["description_name_fa"]); ?>">
																<?php print_r($tables["description_name_".$GLOBALS['user_language']]); ?>
															</option>
													<?php
														}
													?>
												</select>
											</div>
										</div>
										<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios">
											<div class="form-check cursor-pointer">
												<label class="form-check-label">
													<input class="form-check-input input_important input_checkbox" type="checkbox" data-reset-value="<?php print_r($column_editing['importants']); ?>" <?php if($column_editing['importants']==1){?>checked<?php } ?> >
													<span class="form-check-sign"></span>
													<label class="data-text" data-text-en="Important ?" data-text-fa="پر اهمیت ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Important ?" : "پر اهمیت ؟"); ?></label>
												</label>
											</div>
										</div>
										<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 form-check-radio checkbox-radios">
											<div class="form-check cursor-pointer">
												<label class="form-check-label">
													<input class="form-check-input input_primary input_checkbox" type="radio" name="primary" data-reset-value="<?php print_r($column_editing['primarys']); ?>" <?php if($column_editing['primarys']==1){?>checked<?php } ?> >
													<span class="form-check-sign"></span>
													<label class="data-text" data-text-en="Primary?" data-text-fa="اصلی؟"><?php print_r($GLOBALS['user_language']=="en" ? "Primary?":"اصلی؟"); ?></label>
												</label>
											</div>
										</div>
										<div class="col-12 extra-options row">
											<?php
												if($column_editing['created']!=1){
													$modeGet=$column_editing['mode'];
												}else{
													if($column_editing['new_mode']!=0){
														$modeGet=$column_editing['new_mode'];
													}else if($column_editing['mode']!=0){
														$modeGet=$column_editing['mode'];
													}else{
														$modeGet=1;
													}
												}
												?><?php //info search this_is_modes_for_data_tables for see all things about this part ?><?php
												switch ($modeGet) {//tables_mode_code
													//create_tables_mode_html_raw
													case "3":case 3://info search case 3 for see all things about this part
														$res_yes_no=$connection->query("SELECT * FROM ".$sub_name."yes_no_question_options WHERE table_id='".$column_editing['table_id']."' AND column_id='".$column_editing['id']."'");
														if($res_yes_no->rowCount()!=0){
															$yes_no=$res_yes_no->fetch();
															$yes_option=$yes_no['yes_option'];
															$no_option=$yes_no['no_option'];
															$yes_value=($yes_no['yes_value']!="" ? $yes_no['yes_value']:1);
															$no_value=($yes_no['no_value']!="" ? $yes_no['no_value']:0);
															$yes_fa_icon=$yes_no['yes_fa_icon'];
															$no_fa_icon=$yes_no['no_fa_icon'];
														}else{
															$yes_option='';
															$no_option='';
															$yes_value='';
															$no_value='';
															$yes_fa_icon='';
															$no_fa_icon='';
														}
														?>
															<div class="col-lg-2 col-md-6 col-12">
																<div class="form-group">
																	<input class="form-control data-placeholder data-original-title input_yes_option input_text on-press-enter-column" rel="tooltip" data-original-title-en="Enabled/Yes" data-original-title-fa="فعال/بله" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Enabled/Yes" : "فعال/بله"); ?>" data-placement="top" data-placeholder-en="Enabled/Yes" data-placeholder-fa="فعال/بله" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Enabled/Yes":"فعال/بله"); ?>" type="text" data-reset-value="<?php print_r($yes_option); ?>" value="<?php print_r($yes_option); ?>">
																</div>
															</div>
															<div class="col-lg-2 col-md-6 col-12">
																<div class="form-group">
																	<input class="form-control data-placeholder data-original-title input_no_option input_text on-press-enter-column" rel="tooltip" data-original-title-en="Disable/No" data-original-title-fa="غیرفعال/خیر" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Disable/No" : "غیرفعال/خیر"); ?>" data-placement="top" data-placeholder-en="Disable/No" data-placeholder-fa="غیرفعال/خیر" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Disable/No":"غیرفعال/خیر"); ?>" type="text" data-reset-value="<?php print_r($no_option); ?>" value="<?php print_r($no_option); ?>">
																</div>
															</div>
															<div class="col-lg-2 col-md-6 col-12">
																<div class="form-group">
																	<input class="form-control data-placeholder data-original-title input_yes_value input_text on-press-enter-column" rel="tooltip" data-original-title-en="1/true" data-original-title-fa="1/true1" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "1/true" : "1/true1"); ?>" data-placement="top" data-placeholder-en="1/true" data-placeholder-fa="1/true1" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "1/true":"1/true1"); ?>" type="text" data-reset-value="<?php print_r($yes_value); ?>" value="<?php print_r($yes_value); ?>">
																</div>
															</div>
															<div class="col-lg-2 col-md-6 col-12">
																<div class="form-group">
																	<input class="form-control data-placeholder data-original-title input_no_value input_text on-press-enter-column" rel="tooltip" data-original-title-en="0/false" data-original-title-fa="0/false" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "0/false" : "0/false"); ?>" data-placement="top" data-placeholder-en="0/false" data-placeholder-fa="0/false" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "0/false":"0/false"); ?>" type="text" data-reset-value="<?php print_r($no_value); ?>" value="<?php print_r($no_value); ?>">
																</div>
															</div>
															<div class="col-lg-2 col-md-6 col-12">
																<div class="form-group">
																	<input class="form-control data-placeholder data-original-title input_yes_icon input_text on-press-enter-column" rel="tooltip" data-original-title-en="far fa-check" data-original-title-fa="far fa-check" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "far fa-check" : "far fa-check"); ?>" data-placement="top" data-placeholder-en="far fa-check" data-placeholder-fa="far fa-check" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "far fa-check":"far fa-check"); ?>" type="text" data-reset-value="<?php print_r($yes_fa_icon); ?>" value="<?php print_r($yes_fa_icon); ?>">
																</div>
															</div>
															<div class="col-lg-2 col-md-6 col-12">
																<div class="form-group">
																	<input class="form-control data-placeholder data-original-title input_no_icon input_text on-press-enter-column" rel="tooltip" data-original-title-en="far fa-square" data-original-title-fa="far fa-square" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "far fa-square" : "far fa-square"); ?>" data-placement="top" data-placeholder-en="far fa-square" data-placeholder-fa="far fa-square" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "far fa-square":"far fa-square"); ?>" type="text" data-reset-value="<?php print_r($no_fa_icon); ?>" value="<?php print_r($no_fa_icon); ?>">
																</div>
															</div>
														<?php
													break;
													case "4":case 4://info search case 4 for see all things about this part
														$res_selectbox_setting=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."select_options_setting WHERE table_id='".$column_editing['table_id']."' AND column_id='".$column_editing['id']."'");
														$selectbox_setting=($res_selectbox_setting->rowCount() ? $res_selectbox_setting->fetch():["is_multiple"=>0,"is_forced"=>0,"min_allowed"=>0,"max_allowed"=>0]);
														?>
															<div class="col-md-12 row mr-auto ml-auto pb-3">
																<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto">
																	<div class="form-check cursor-pointer">
																		<label class="form-check-label">
																			<input class="form-check-input input_is_multiple input_checkbox" type="checkbox" data-reset-value="<?php print_r($selectbox_setting['is_multiple']); ?>" <?php if($selectbox_setting && $selectbox_setting['is_multiple']==1){?>checked<?php } ?> >
																			<span class="form-check-sign"></span>
																			<label class="data-text" data-text-en="Multiple ?" data-text-fa="چندتایی ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Multiple ?" : "چندتایی ؟"); ?></label>
																		</label>
																	</div>
																</div>
																<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto hide">
																	<div class="form-check cursor-pointer">
																		<label class="form-check-label">
																			<input class="form-check-input input_is_forced input_checkbox" type="checkbox" data-reset-value="<?php /*print_r($selectbox_setting['is_forced']);*/echo 1; ?>" <?php /* if($selectbox_setting && $selectbox_setting['is_forced']==1){?>checked<?php } */echo "checked"; ?> >
																			<span class="form-check-sign"></span>
																			<label class="data-text" data-text-en="Forced ?" data-text-fa="اجباری ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Forced ?" : "اجباری ؟"); ?></label>
																		</label>
																	</div>
																</div>
																<?php
																	/*
																		<div class="col-lg-3 col-12 mb-2 ml-auto mr-auto">
																			<div class="form-group">
																				<input class="form-control data-placeholder data-original-title input_min_allowed input_text" rel="tooltip" data-original-title-en="Minimum amount allowed" data-original-title-fa="کمترین مقدار مجاز" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Minimum amount allowed" : "کمترین مقدار مجاز"); ?>" data-placement="top" data-placeholder-en="Minimum amount allowed" data-placeholder-fa="کمترین مقدار مجاز" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Minimum amount allowed":"کمترین مقدار مجاز"); ?>" type="number" data-reset-value="<?php print_r($selectbox_setting['min_allowed']); ?>" value="<?php print_r($selectbox_setting['min_allowed']); ?>">
																			</div>
																		</div>
																		<div class="col-lg-3 col-12 mb-2 ml-auto mr-auto">
																			<div class="form-group">
																				<input class="form-control data-placeholder data-original-title input_max_allowed input_text" rel="tooltip" data-original-title-en="Maximum amount allowed" data-original-title-fa="بیشترین مقدار مجاز" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Maximum amount allowed" : "بیشترین مقدار مجاز"); ?>" data-placement="top" data-placeholder-en="Maximum amount allowed" data-placeholder-fa="بیشترین مقدار مجاز" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Maximum amount allowed":"بیشترین مقدار مجاز"); ?>" type="number" value="<?php print_r($selectbox_setting['max_allowed']); ?>" data-reset-value="<?php print_r($selectbox_setting['max_allowed']); ?>">
																			</div>
																		</div>
																	*/
																?>
															</div>
															<div class="col-md-12 row mr-auto ml-auto">
																<div class="col-lg-6 col-12 row mb-2 ml-auto mr-auto">
																	<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto">
																		<div class="form-check cursor-pointer checkbox_changer">
																			<label class="form-check-label">
																				<input class="form-check-input is_optgroup_opt" type="checkbox">
																				<span class="form-check-sign"></span>
																				<label class="data-text" data-text-en="Optgroup ?" data-text-fa="سرگروه ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Optgroup ?" : "سرگروه ؟"); ?></label>
																			</label>
																		</div>
																	</div>
																	<select class="selectpicker data-title col-lg-4 col-12 mb-2 ml-auto mr-auto connected_table" data-title-en="Tables" data-title-fa="جدول ها" data-style="btn btn-primary" title="<?php if($GLOBALS['user_language']=='en'){?>Tables<?php }else{?>جدول ها<?php } ?>" data-size="7" data-live-search="true">
																		<option class="data-text" value="0" data-text-en="Manual" data-text-fa="دستی">
																			<?php print_r($GLOBALS['user_language']=="en" ? "Manual":"دستی"); ?>
																		</option>
																		<?php
																			$res_tables=$connection->query("SELECT * FROM ".$sub_name."table_config ORDER BY ordering ASC");
																			while($tables=$res_tables->fetch()){
																				if(checkPermission(1,$tables['id'],"read",$tables['act'],null)==1){
																		?>
																				<option class="data-text" value="<?php print_r($tables['id']); ?>" data-text-en="<?php print_r($tables['description_name_en']); ?>" data-text-fa="<?php print_r($tables['description_name_fa']); ?>">
																					<?php print_r($tables['description_name_'.$GLOBALS['user_language']]); ?>
																				</option>
																		<?php
																				}
																			}
																		?>
																	</select>
																</div>
																<div class="col-lg-6 col-12 row mb-2 ml-auto mr-auto">
																	<select disabled class="selectpicker data-title col-lg-4 col-12 mb-2 ml-auto mr-auto connected_name" data-title-en="Columns" data-title-fa="ستون ها" data-style="btn btn-primary" title="<?php if($GLOBALS['user_language']=='en'){?>Columns<?php }else{?>ستون ها<?php } ?>" data-size="7" data-live-search="true">
																		<option selected class="data-text" data-text-en="Please select a table" data-text-fa="لطفاً یک جدول انتخاب کنید" value="-"><?php if($GLOBALS['user_language']=='en'){?>Please select a table<?php }else{?>لطفاً یک جدول انتخاب کنید<?php } ?></option>
																	</select>
																	<select disabled class="selectpicker data-title col-lg-4 col-12 mb-2 ml-auto mr-auto connected_value" data-title-en="Columns" data-title-fa="ستون ها" data-style="btn btn-primary" title="<?php if($GLOBALS['user_language']=='en'){?>Columns<?php }else{?>ستون ها<?php } ?>" data-size="7" data-live-search="true">
																		<option selected class="data-text" data-text-en="Please select a table" data-text-fa="لطفاً یک جدول انتخاب کنید" value="-"><?php if($GLOBALS['user_language']=='en'){?>Please select a table<?php }else{?>لطفاً یک جدول انتخاب کنید<?php } ?></option>
																	</select>
																	<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto option_name_div hide">
																		<div class="form-group">
																			<input class="form-control data-placeholder data-original-title option_name select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Name" data-original-title-fa="نام گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Option Name" : "نام گزینه"); ?>" data-placement="top" data-placeholder-en="Option Name" data-placeholder-fa="نام گزینه" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Option Name":"نام گزینه"); ?>" type="text">
																		</div>
																	</div>
																	<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto option_value_div hide">
																		<div class="form-group">
																			<input class="form-control data-placeholder data-original-title option_value select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Value" data-original-title-fa="مقدار گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Option Value" : "مقدار گزینه"); ?>" data-placement="top" data-placeholder-en="Option Value" data-placeholder-fa="مقدار گزینه" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Option Value":"مقدار گزینه"); ?>" type="text">
																		</div>
																	</div>
																	<select class="selectpicker data-title col-lg-4 col-12 optgroup_id" data-title-en="Optgroups" data-title-fa="سرگروه ها" data-style="btn btn-primary" title="<?php if($GLOBALS['user_language']=="en"){?>Optgroups<?php }else{?>سرگروه ها<?php } ?>" data-size="7" data-live-search="true">
																		<option value="-" selected class="data-text" data-text-en="None of them" data-text-fa="هیچکدام">
																			<?php print_r($GLOBALS['user_language']=="en" ? "None of them":"هیچکدام"); ?>
																		</option>
																		<?php
																			$res_optGroup=$connection->query("SELECT * FROM ".$sub_name."select_options WHERE table_id='".$column_editing['table_id']."' AND column_id='".$column_editing['id']."' AND is_optgroup=1");
																			while($optGroup=$res_optGroup->fetch()){
																		?>
																			<option value="<?php print_r($optGroup['id']); ?>">
																				<?php print_r($optGroup['option_text']); ?>
																			</option>
																		<?php
																			}
																		?>
																		<option value="*" class="data-text" data-text-en="New Optgroup" data-text-fa="سرگروه جدید"><?php print_r($GLOBALS['user_language']=="en" ? "New Optgroup":"سرگروه جدید"); ?></option>
																	</select>
																	<div class="col-lg-4 col-12 mb-2 ml-auto mr-auto optgroup_new_div hide">
																		<div class="input-group">
																			<input class="form-control data-placeholder data-original-title new_optgroup_text select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Optgroup Text" data-original-title-fa="متن سرگروه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Optgroup Text" : "متن سرگروه"); ?>" data-placement="top" data-placeholder-en="Optgroup Text" data-placeholder-fa="متن سرگروه" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Optgroup Text":"متن سرگروه"); ?>" type="text">
																			<div class="input-group-append cursor-pointer optgroup-list">
																				<div class="input-group-text">
																					<i class="fad fa-times"></i>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="col-12 mb-2 ml-auto mr-auto optgroup_text_div hide">
																		<div class="form-group">
																			<input class="form-control data-placeholder data-original-title optgroup_text select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Optgroup Text" data-original-title-fa="متن سرگروه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Optgroup Text" : "متن سرگروه"); ?>" data-placement="top" data-placeholder-en="Optgroup Text" data-placeholder-fa="متن سرگروه" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Optgroup Text":"متن سرگروه"); ?>" type="text">
																		</div>
																	</div>
																</div>
																<div class="col-12 row mb-2 ml-auto mr-auto">
																	<i class="fas fa-plus-octagon display-4 mt-2 <?php print_r($GLOBALS['user_language']=="en" ? "ml-auto mr-1":"mr-auto ml-1"); ?> text-success cursor-pointer data-original-title add_select_options" rel="tooltip" data-original-title-en="Add Option" data-original-title-fa="افزودن گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Add Option" : "افزودن گزینه"); ?>"></i>
																	<i class="fas fa-eraser display-4 mt-2 <?php print_r($GLOBALS['user_language']=="en" ? "ml-1 mr-auto":"mr-1 ml-auto"); ?> text-warning cursor-pointer data-original-title clear_select_options" rel="tooltip" data-original-title-en="Clear Option" data-original-title-fa="پاکسازی گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Clear Option" : "پاکسازی گزینه"); ?>"></i>

																	<i class="fad fa-save display-4 mt-2 <?php print_r($GLOBALS['user_language']=="en" ? "ml-auto mr-1":"mr-auto ml-1"); ?> text-success cursor-pointer data-original-title save_select_options hide" rel="tooltip" data-original-title-en="Save Option" data-original-title-fa="ذخیره گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Save Option" : "ذخیره گزینه"); ?>"></i>
																	<i class="fad fa-clone display-4 mt-2 ml-1 mr-1 text-info cursor-pointer data-original-title copy_select_options hide" rel="tooltip" data-original-title-en="Copy Option" data-original-title-fa="کپی گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Copy Option" : "کپی گزینه"); ?>"></i>
																	<i class="fad fa-forward display-4 mt-2 ml-1 mr-1 cursor-pointer data-original-title skip_select_options hide" rel="tooltip" data-original-title-en="Skip Editing" data-original-title-fa="رد کردن ویرایش" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Skip Editing" : "رد کردن ویرایش"); ?>"></i>
																	<i class="fas fa-times-octagon display-4 mt-2 <?php print_r($GLOBALS['user_language']=="en" ? "ml-1 mr-auto":"mr-1 ml-auto"); ?> text-danger cursor-pointer data-original-title delete_select_options hide" rel="tooltip" data-original-title-en="Delete Option" data-original-title-fa="حذف گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Delete Option" : "حذف گزینه"); ?>"></i>
																</div>
																<!-- table of select options -->
																<div class="col-12">
																	<table class="table table-striped w-100 selectOpt_table">
																		<thead>
																			<tr>
																				<th data-priority="4">
																					<label class="data-text" data-text-en="Optgroup" data-text-fa="سرگروه" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Optgroup":"سرگروه"); ?></label>
																				</th>
																				<th data-priority="1">
																					<label class="data-text" data-text-en="Name" data-text-fa="نام" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Name":"نام"); ?></label>
																				</th>
																				<th data-priority="6">
																					<label class="data-text" data-text-en="Value" data-text-fa="مقدار" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Value":"مقدار"); ?></label>
																				</th>
																				<th data-priority="3">
																					<label class="data-text" data-text-en="Type" data-text-fa="حالت" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Type":"حالت"); ?></label>
																				</th>
																				<th data-priority="2">
																					<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات"><?php print_r($GLOBALS['user_language']=="en" ? "Operation":"عملیات"); ?></label>
																				</th>
																			</tr>
																		</thead>
																		<tbody>
																			
																		</tbody>
																		<tfoot>
																			<tr>
																				<th>
																					<label class="data-text" data-text-en="Optgroup" data-text-fa="سرگروه" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Optgroup":"سرگروه"); ?></label>
																				</th>
																				<th>
																					<label class="data-text" data-text-en="Name" data-text-fa="نام" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Name":"نام"); ?></label>
																				</th>
																				<th>
																					<label class="data-text" data-text-en="Value" data-text-fa="مقدار" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Value":"مقدار"); ?></label>
																				</th>
																				<th>
																					<label class="data-text" data-text-en="Type" data-text-fa="حالت" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Type":"حالت"); ?></label>
																				</th>
																				<th>
																					<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات"><?php print_r($GLOBALS['user_language']=="en" ? "Operation":"عملیات"); ?></label>
																				</th>
																			</tr>
																		</tfoot>
																	</table>
																</div>
															</div>
															<script>
																callDataTable_selectOpt_table($(".column_saved_id-<?php print_r($column_editing['id']); ?>"),<?php print_r($column_editing['id']); ?>);
															</script>
														<?php
													break;
													case "7":case 7://info search case 7 for see all things about this part
														$res_file_uploader_setting=$connection->query("SELECT * FROM ".$sub_name."file_uploader_setting WHERE table_id='".$column_editing['table_id']."' AND column_id='".$column_editing['id']."'");
														if($res_file_uploader_setting->rowCount()!=0){
															$file_uploader_setting=$res_file_uploader_setting->fetch();
															$size_limit=$file_uploader_setting['max_size'];
															$file_types_limit=$file_uploader_setting['allowed_type'];
														}else{
															$size_limit='';
															$file_types_limit='';
														}
														?>
															<div class="col-md-6 col-12">
																<div class="form-group">
																	<input class="form-control data-placeholder data-original-title input_size_limit input_text on-press-enter-column" rel="tooltip" data-original-title-en="Size Limit MB (0 = unlimited)" data-original-title-fa="محدودیت سایز MB (0 = نامحدود)" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Size Limit MB (0 = unlimited)" : "محدودیت سایز MB (0 = نامحدود)"); ?>" data-placement="top" data-placeholder-en="Size Limit MB (0 = unlimited)" data-placeholder-fa="محدودیت سایز MB (0 = نامحدود)" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Size Limit MB (0 = unlimited)":"محدودیت سایز MB (0 = نامحدود)"); ?>" type="text" data-reset-value="<?php print_r($size_limit); ?>" value="<?php print_r($size_limit); ?>">
																</div>
															</div>
															<div class="col-md-6 col-12 text-center-custom">
																<input class="form-control tagsinput data-placeholder data-original-title input_file_types_limit input_text on-press-enter-column" rel="tooltip" data-original-title-en="Format (empty = all) , (.jpg,...)" data-original-title-fa="پسوند (خالی = همه) (.jpg,...)" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Format (empty = all) , (.jpg,...)" : "پسوند (خالی = همه) (.jpg,...)"); ?>" data-placement="top" data-placeholder-en="Format (empty = all) , (.jpg,...)" data-placeholder-fa="پسوند (خالی = همه) (.jpg,...)" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Format (empty = all) , (.jpg,...)":"پسوند (خالی = همه) (.jpg,...)"); ?>" type="text" data-reset-value="<?php print_r($file_types_limit); ?>" value="<?php print_r($file_types_limit); ?>">
															</div>
														<?php
													break;
													case "9":case 9://info search case 9 for see all things about this part
														$res_checkbox_setting=$GLOBALS["connection"]->query("SELECT * FROM ".$GLOBALS["sub_name"]."checkbox_options_setting WHERE table_id='".$column_editing['table_id']."' AND column_id='".$column_editing['id']."'");
														$checkbox_setting=($res_checkbox_setting->rowCount() ? $res_checkbox_setting->fetch():0);
														?>
															<div class="col-md-12 row mr-auto ml-auto pb-3">
																<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto">
																	<div class="form-check cursor-pointer">
																		<label class="form-check-label">
																			<input class="form-check-input input_is_multiple input_checkbox" type="checkbox" data-reset-value="<?php print_r($checkbox_setting['is_multiple']); ?>" <?php if($checkbox_setting && $checkbox_setting['is_multiple']==1){?>checked<?php } ?> >
																			<span class="form-check-sign"></span>
																			<label class="data-text" data-text-en="Multiple ?" data-text-fa="چندتایی ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Multiple ?" : "چندتایی ؟"); ?></label>
																		</label>
																	</div>
																</div>
																<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 checkbox-radios mr-auto ml-auto hide">
																	<div class="form-check cursor-pointer">
																		<label class="form-check-label">
																			<input class="form-check-input input_is_forced input_checkbox" type="checkbox" data-reset-value="<?php /*print_r($checkbox_setting['is_forced']);*/echo 1; ?>" <?php /* if($checkbox_setting && $checkbox_setting['is_forced']==1){?>checked<?php } */echo "checked"; ?> >
																			<span class="form-check-sign"></span>
																			<label class="data-text" data-text-en="Forced ?" data-text-fa="اجباری ؟"><?php print_r($GLOBALS['user_language'] == "en" ? "Forced ?" : "اجباری ؟"); ?></label>
																		</label>
																	</div>
																</div>
															</div>
															<div class="col-md-12 row mr-auto ml-auto">
																<div class="form-group col-4">
																	<input class="form-control data-placeholder data-original-title checkbox_name select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Name" data-original-title-fa="نام گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Option Name" : "نام گزینه"); ?>" data-placement="top" data-placeholder-en="Option Name" data-placeholder-fa="نام گزینه" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Option Name":"نام گزینه"); ?>" type="text">
																</div>
																<div class="form-group col-4">
																	<input class="form-control data-placeholder data-original-title checkbox_value select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option Value" data-original-title-fa="خروجی گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Option Value" : "خروجی گزینه"); ?>" data-placement="top" data-placeholder-en="Option Value" data-placeholder-fa="خروجی گزینه" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Option Value":"خروجی گزینه"); ?>" type="text">
																</div>
																<div class="form-group col-4">
																	<input class="form-control data-placeholder data-original-title checkbox_false select-option-on-press-enter on-change-remove-class" rel="tooltip" data-original-title-en="Option False" data-original-title-fa="خروجی منفی گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Option False" : "خروجی منفی گزینه"); ?>" data-placement="top" data-placeholder-en="Option False" data-placeholder-fa="خروجی منفی گزینه" placeholder="<?php print_r($GLOBALS['user_language']=="en" ? "Option False":"خروجی منفی گزینه"); ?>" type="text">
																</div>
																<div class="col-12 row mb-2 ml-auto mr-auto">
																	<i class="fas fa-plus-octagon display-4 mt-2 <?php print_r($GLOBALS['user_language']=="en" ? "ml-auto mr-1":"mr-auto ml-1"); ?> text-success cursor-pointer data-original-title add_checkbox_options" rel="tooltip" data-original-title-en="Add Option" data-original-title-fa="افزودن گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Add Option" : "افزودن گزینه"); ?>"></i>
																	<i class="fas fa-eraser display-4 mt-2 <?php print_r($GLOBALS['user_language']=="en" ? "ml-1 mr-auto":"mr-1 ml-auto"); ?> text-warning cursor-pointer data-original-title clear_checkbox_options" rel="tooltip" data-original-title-en="Clear Option" data-original-title-fa="پاکسازی گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Clear Option" : "پاکسازی گزینه"); ?>"></i>

																	<i class="fad fa-save display-4 mt-2 <?php print_r($GLOBALS['user_language']=="en" ? "ml-auto mr-1":"mr-auto ml-1"); ?> text-success cursor-pointer data-original-title save_checkbox_options hide" rel="tooltip" data-original-title-en="Save Option" data-original-title-fa="ذخیره گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Save Option" : "ذخیره گزینه"); ?>"></i>
																	<i class="fad fa-clone display-4 mt-2 ml-1 mr-1 text-info cursor-pointer data-original-title copy_checkbox_options hide" rel="tooltip" data-original-title-en="Copy Option" data-original-title-fa="کپی گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Copy Option" : "کپی گزینه"); ?>"></i>
																	<i class="fad fa-forward display-4 mt-2 ml-1 mr-1 cursor-pointer data-original-title skip_checkbox_options hide" rel="tooltip" data-original-title-en="Skip Editing" data-original-title-fa="رد کردن ویرایش" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Skip Editing" : "رد کردن ویرایش"); ?>"></i>
																	<i class="fas fa-times-octagon display-4 mt-2 <?php print_r($GLOBALS['user_language']=="en" ? "ml-1 mr-auto":"mr-1 ml-auto"); ?> text-danger cursor-pointer data-original-title delete_checkbox_options hide" rel="tooltip" data-original-title-en="Delete Option" data-original-title-fa="حذف گزینه" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Delete Option" : "حذف گزینه"); ?>"></i>
																</div>
																<!-- table of select options -->
																<div class="col-12">
																	<table class="table table-striped w-100 checkboxOpt_table">
																		<thead>
																			<tr>
																				<th data-priority="4">
																					<label class="data-text" data-text-en="Option Name" data-text-fa="نام گزینه" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Option Name":"نام گزینه"); ?></label>
																				</th>
																				<th data-priority="1">
																					<label class="data-text" data-text-en="Option Value" data-text-fa="خروجی گزینه" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Option Value":"خروجی گزینه"); ?></label>
																				</th>
																				<th data-priority="6">
																					<label class="data-text" data-text-en="Option false value" data-text-fa="خروجی منفی گزینه" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Option false value":"خروجی منفی گزینه"); ?></label>
																				</th>
																				<th data-priority="2">
																					<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات"><?php print_r($GLOBALS['user_language']=="en" ? "Operation":"عملیات"); ?></label>
																				</th>
																			</tr>
																		</thead>
																		<tbody>
																			
																		</tbody>
																		<tfoot>
																			<tr>
																				<th>
																					<label class="data-text" data-text-en="Option Name" data-text-fa="نام گزینه" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Option Name":"نام گزینه"); ?></label>
																				</th>
																				<th>
																					<label class="data-text" data-text-en="Option Value" data-text-fa="خروجی گزینه" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Option Value":"خروجی گزینه"); ?></label>
																				</th>
																				<th>
																					<label class="data-text" data-text-en="Option false value" data-text-fa="خروجی منفی گزینه" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Option false value":"خروجی منفی گزینه"); ?></label>
																				</th>
																				<th>
																					<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات"><?php print_r($GLOBALS['user_language']=="en" ? "Operation":"عملیات"); ?></label>
																				</th>
																			</tr>
																		</tfoot>
																	</table>
																</div>
															</div>
															<script>
																callDataTable_checkboxOpt_table($(".column_saved_id-<?php print_r($column_editing['id']); ?>"),<?php print_r($column_editing['id']); ?>);
															</script>
														<?php
													break;
												}
											?>
										</div>
										<div class="col-12 mt-2">
											<?php print_r($primary_buttons); ?>
										</div>
									</div>
								<?php
											}
										}
									}
								?>
							</div>
							<div class="row justify-content-center text-center mt-5 mb-5">
								<div class="col-12">
									<button onclick="createButtonOperations('add','',$(this),0);return false;" class="btn btn-success data-original-title data-text" rel="tooltip" data-original-title-en="Add new column to the table" data-original-title-fa="افزودن ستون جدید به جدول" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Add new column to the table" : "افزودن ستون جدید به جدول"); ?>" data-placement="top" data-text-en="Add" data-text-fa="افزودن"><?php print_r($GLOBALS['user_language'] == "en" ? "Add" : "افزودن"); ?></button>

									<button onclick="saveAll()" class="btn btn-success data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Save all of columns" data-original-title-fa="ذخیره همه ستون ها" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Save all of columns" : "ذخیره همه ستون ها"); ?>" data-placement="top" data-text-en="Save" data-text-fa="ذخیره"><?php print_r($GLOBALS['user_language'] == "en" ? "Save" : "ذخیره"); ?></button>

									<button onclick="saveAndCloseAll()" class="btn btn-info data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Save all of columns and close" data-original-title-fa="ذخیره همه ستون ها و خروج" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Save all of columns and close" : "ذخیره همه ستون ها و خروج"); ?>" data-placement="top" data-text-en="Save & Close" data-text-fa="ذخیره و خروج"><?php print_r($GLOBALS['user_language'] == "en" ? "Save & Close" : "ذخیره و خروج"); ?></button>

									<button onclick="clearAll()" class="btn btn-warning data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Clear all columns which were going to add to the table !" data-original-title-fa="تمام ستون هایی را که قرار بود به جدول اضافه شوند پاکسازی کنید !" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Clear all columns which were going to add to the table !" : "تمام ستون هایی را که قرار بود به جدول اضافه شوند پاکسازی کنید !"); ?>" data-placement="top" data-text-en="Clear" data-text-fa="پاکسازی"><?php print_r($GLOBALS['user_language'] == "en" ? "Clear" : "پاکسازی"); ?></button>

									<button onclick="resetAll()" class="btn btn-warning data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Reset every fields which was editing" data-original-title-fa="بازیابی تمام فیلد هایی که در حال ویرایش بودند" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Reset every fields which was editing" : "بازیابی تمام فیلد هایی که در حال ویرایش بودند"); ?>" data-placement="top" data-text-en="Reset" data-text-fa="بازیابی"><?php print_r($GLOBALS['user_language'] == "en" ? "Reset" : "بازیابی"); ?></button>

									<button onclick="skipAll()" class="btn btn-warning data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Skip every columns which was editing" data-original-title-fa="رد کردن تمام ستون هایی که در حال ویرایش بودند" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Skip every columns which was editing" : "رد کردن تمام ستون هایی که در حال ویرایش بودند"); ?>" data-placement="top" data-text-en="Skip" data-text-fa="رد کردن"><?php print_r($GLOBALS['user_language'] == "en" ? "Skip" : "رد کردن"); ?></button>

									<button onclick="deleteAll()" class="btn btn-danger data-original-title data-text cooldown-click" rel="tooltip" data-original-title-en="Delete all columns which were going to add to the table !" data-original-title-fa="تمام ستون هایی را که قرار بود به جدول اضافه شوند حذف کنید !" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Delete all columns which were going to add to the table !" : "تمام ستون هایی را که قرار بود به جدول اضافه شوند حذف کنید !"); ?>" data-placement="top" data-text-en="Delete" data-text-fa="حذف"><?php print_r($GLOBALS['user_language'] == "en" ? "Delete" : "حذف"); ?></button>

									<!-- <button onclick="$('#regex_option').toggleClass('hide');return false;" class="btn data-original-title data-text" rel="tooltip" data-original-title-en="*" data-original-title-fa="*" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "*" : "*"); ?>" data-placement="top" data-text-en="Regular expressions" data-text-fa="عبارات منظم"><?php print_r($GLOBALS['user_language'] == "en" ? "Regular expressions" : "عبارات منظم"); ?></button> -->

									<!-- <button onclick="$('#regex_option, #database_option').addClass('hide');return false;" class="btn data-original-title data-text" rel="tooltip" data-original-title-en="Disable all custom options" data-original-title-fa="لغو گزینه های سفارشی سازی" data-original-title="<?php print_r($GLOBALS['user_language'] == "en" ? "Disable all custom options" : "لغو گزینه های سفارشی سازی"); ?>" data-placement="top" data-text-en="Disable Custom" data-text-fa="لغو سفارشی سازی"><?php print_r($GLOBALS['user_language'] == "en" ? "Disable Custom" : "لغو سفارشی سازی"); ?></button> -->

								</div>
							</div>
							<div class="row justify-content-center mt-5">
								<div class="col-md-12">
									<div class="card">
										<div id="accordion_columns" role="tablist" aria-multiselectable="true" class="card-collapse">
											<div class="card card-plain">
												<div class="card-header text-center cursor-pointer" id="headingOne">
													<a data-toggle="collapse" data-parent="#accordion_columns" href="#collapseColumns" aria-expanded="false" aria-controls="collapseColumns">
														<label class="data-text" data-text-en="Manage Columns" data-text-fa="مدیریت ستون ها">
															<?php print_r($GLOBALS['user_language']=="en" ? "Manage Columns":"مدیریت ستون ها"); ?>
														</label>
														<i class="tim-icons icon-minimal-down"></i>
													</a>
												</div>
												<div id="collapseColumns" class="collapse" role="tabpanel" aria-labelledby="headingColumns">
													<div class="card-body">
														<!-- table of columns -->
														<table id="tables_columns" class="table table-striped w-100">
															<thead>
																<tr>
																	<th data-priority="4">
																		<label class="data-text" data-text-en="Column ID" data-text-fa="آیدی ستون" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Column ID":"آیدی ستون"); ?></label>
																	</th>
																	<th data-priority="1">
																		<label class="data-text" data-text-en="Column Name" data-text-fa="نام ستون" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Column Name":"نام ستون"); ?></label>
																	</th>
																	<th data-priority="3">
																		<label class="data-text" data-text-en="Descriptive Name" data-text-fa="نام توصیفی" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Descriptive Name":"نام توصیفی"); ?></label>
																	</th>
																	<th data-priority="2">
																		<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات"><?php print_r($GLOBALS['user_language']=="en" ? "Operation":"عملیات"); ?></label>
																	</th>
																</tr>
															</thead>
															<tbody>
																
															</tbody>
															<tfoot>
																<tr>
																	<th>
																		<label class="data-text" data-text-en="Column ID" data-text-fa="آیدی ستون" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Column ID":"آیدی ستون"); ?></label>
																	</th>
																	<th>
																		<label class="data-text" data-text-en="Column Name" data-text-fa="نام ستون" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Column Name":"نام ستون"); ?></label>
																	</th>
																	<th>
																		<label class="data-text" data-text-en="Descriptive Name" data-text-fa="نام توصیفی" style="margin-bottom: 0px !important;"><?php print_r($GLOBALS['user_language']=="en" ? "Descriptive Name":"نام توصیفی"); ?></label>
																	</th>
																	<th>
																		<label class="data-text" style="margin-bottom: 0px !important;" data-text-en="Operation" data-text-fa="عملیات"><?php print_r($GLOBALS['user_language']=="en" ? "Operation":"عملیات"); ?></label>
																	</th>
																</tr>
															</tfoot>
														</table>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php
							if(checkPermission("group_array_full", getTableByName($sub_name."menu")["id"], "create", getTableByName($sub_name."menu")["act"], null) && 0){
						?>
							<div class="tab-pane" id="table_menu">
								<h5 class="info-text data-text" data-text-en="Menu for link to the table" data-text-fa="منو برای اتصال به جدول"><?php print_r($GLOBALS['user_language'] == "en" ? "Menu for link to the table" : "منو برای اتصال به جدول"); ?></h5>
								<div class="row justify-content-center mt-5">
									<div class="col-lg-8 col-md-6 col-sm-6">
										<div class="row justify-content-center text-center mt-3 mb-3 pt-3 pb-3">
											<div class="col-xl-4 col-lg-4 col-md-4 col-sm-10 col-10">
												<div class="form-group hide">
													<input class="form-control" type="text">
												</div>
												<div class="form-group">
													<select class="selectpicker data-title input_mode input_select" data-title-en="Modes" data-title-fa="حالت ها" data-style="btn btn-primary" title="<?php if($GLOBALS['user_language']=="en"){?>Modes<?php }else{?>حالت ها<?php } ?>" data-size="7" data-live-search="true">
														<option class="data-text" data-text-en="Select All" data-text-fa="انتخاب همه" value="-1"><?php if($GLOBALS['user_language']=='en'){?>Select All<?php }else{?>انتخاب همه<?php } ?></option>
													</select>
												</div>
											</div>
											<div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-2">
												<div class="form-check cursor-pointer checkbox-radios changer">
													<label class="form-check-label">
														<input class="form-check-input" type="checkbox">
														<span class="form-check-sign"></span>
														<label class="data-text" data-text-en="New ?" data-text-fa="جدید ؟"><?php print_r($GLOBALS['user_language']=="en" ? "New ?":"جدید ؟"); ?></label>
													</label>
												</div>
											</div>
											<div class="col-xl-4 col-lg-4 col-md-4 col-sm-10 col-10">
												<div class="form-group hide">
													<input class="form-control" type="text">
												</div>
												<div class="form-group">
													<select class="selectpicker data-title input_mode input_select" data-title-en="Modes" data-title-fa="حالت ها" data-style="btn btn-primary" title="<?php if($GLOBALS['user_language']=="en"){?>Modes<?php }else{?>حالت ها<?php } ?>" data-size="7" data-live-search="true">
														<option class="data-text" data-text-en="Select All" data-text-fa="انتخاب همه" value="-1"><?php if($GLOBALS['user_language']=='en'){?>Select All<?php }else{?>انتخاب همه<?php } ?></option>
													</select>
												</div>
											</div>
											<div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-2">
												<div class="form-check cursor-pointer checkbox-radios changer">
													<label class="form-check-label">
														<input class="form-check-input" type="checkbox">
														<span class="form-check-sign"></span>
														<label class="data-text" data-text-en="New ?" data-text-fa="جدید ؟"><?php print_r($GLOBALS['user_language']=="en" ? "New ?":"جدید ؟"); ?></label>
													</label>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php
							}
						?>
					</div>
				</div>
				<div class="card-footer">
					<div class="<?php print_r($GLOBALS['user_language']=="en" ? "pull-right":"pull-left"); ?>">
						<input type='button' class='btn btn-next btn-fill btn-primary btn-wd data-value' name='next' data-value-en="Next" data-value-fa="بعدی" value="<?php print_r($GLOBALS['user_language'] == "en" ? "Next" : "بعدی"); ?>">
						<!-- <input type='button' class='btn btn-finish btn-fill btn-primary btn-wd data-value' name='finish' data-value-en="Finish" data-value-fa="" value="<?php print_r($GLOBALS['user_language'] == "en" ? "Finish" : "پایان"); ?>"> -->
					</div>
					<div class="<?php print_r($GLOBALS['user_language']=="en" ? "pull-left":"pull-right"); ?>">
						<input type='button' class='btn btn-previous btn-fill btn-default btn-wd data-value' name='previous' data-value-en="Previous" data-value-fa="قبلی" value="<?php print_r($GLOBALS['user_language'] == "en" ? "Previous" : "قبلی"); ?>">
					</div>
					<div class="clearfix"></div>
				</div>
			</form>
		</div>
	</div>
	<!-- wizard container -->
</div>
<script>
	if(typeof createTable_wizzard !== "undefined"){
		createTable_wizzard();
	}
	var $database_level = <?php print_r($table_config != 0 ? $table_config["level"] : 0); ?>,
		$create_level = <?php print_r($table_config != 0 ? $table_config["level"] : 0); ?>;
</script>
<script>
	// Needed
	$(".create_database select.selectpicker").selectpicker({
		iconBase: "tim-icons",
		tickIcon: "icon-check-2"
	});
	$(document).on('click', '.create_database .checkbox-radios .form-check', function(e) {
		if($(this).hasClass("create_setting")){
			$(this).find("input").each(function() {
				$.post("table/class/action.php?update_setting&name="+$(this).attr("id"), {
					"data" : ($(this).is(':checked') ? 0:1)
				}, function(data, status) {
					if (status != "success" || data != "success") {
						feedbackOperations(data);
					}
				});
			});
		}
		$(this).find("input").each(function() {
			if($(this).attr("type")=="radio"){
				if($(e.target).hasClass("error")){
					$(this).prop('checked', false);
					$(this).parent().parent().parent().removeClass("has-danger");
					$(this).parent().parent(".form-check.cursor-pointer").children("label.error").remove();
				}else{
					$(this).prop('checked', true);
				}
			}else{
				$(this).prop('checked', ($(this).is(':checked') ? false : true));
			}
		});
	});
	// case 3
	if($(".tagsinput").length && $(".tagsinput").tagsinput()){
		$(".bootstrap-tagsinput").find(".tag").addClass("badge-" + $(".tagsinput").data("color"));
	}
</script>
<?php
		}else{
			echo $outofpermission;
		}
	}else{
?>
	<script>
		window.location="setup/";
	</script>
<?php
	}
?>
<?php
	if(isset($connection)){
		if(isset($custom_lang) && $custom_lang!="" && !empty($custom_lang)){
			$GLOBALS['user_language']=$custom_lang;
		}elseif(!isset($GLOBALS['user_language']) || $GLOBALS['user_language']=="" || empty($GLOBALS['user_language'])){
			$GLOBALS['user_language']="en";
		}
?>
<div class="container-fluid">
	<ul class="nav">
		<li class="nav-item">
			<a href="<?php print_r(getSetting("site_url")); ?>" target="_blank" class="nav-link data-text" data-text-en="<?php print_r(getSetting("site_name_en")); ?>" data-text-fa="<?php print_r(getSetting("site_name_fa")); ?>">
				<?php print_r($GLOBALS['user_language']=="en" ? getSetting("site_name_en"):getSetting("site_name_fa")); ?>
			</a>
		</li>
		<li class="nav-item">
			<a href="https://amirntm.ir/contact" target="_blank" class="nav-link data-text" data-text-en="Report a problem" data-text-fa="گزارش مشکل">
				<?php print_r($GLOBALS['user_language']=="en" ? "Report a problem":"گزارش مشکل"); ?>
			</a>
		</li>
	</ul>
	<div class="copyright data-text" data-text-en="© 2020 Made with <i class='tim-icons icon-heart-2'></i> by <a href='https://technosha.com/' target='_blank'>Technosha</a> for a better web." data-text-fa="© 2020 طراحی شده با <i class='tim-icons icon-heart-2'></i> توسط <a href='https://technosha.com/' target='_blank'>تکنوشا</a> برای بهترین وب سایت ها.">
		<?php print_r($GLOBALS['user_language']=="en" ? "© 2020 Made with <i class='tim-icons icon-heart-2'></i> by <a href='https://technosha.com/' target='_blank'>Technosha</a> for a better web.":"© 2020 طراحی شده با <i class='tim-icons icon-heart-2'></i> توسط <a href='https://technosha.com/' target='_blank'>تکنوشا</a> برای بهترین وب سایت ها."); ?>
	</div>
</div>
<?php
	}
?>
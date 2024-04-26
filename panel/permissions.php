<?php
	$conn_dir="../connection/connect.php";
	if(session_status() == PHP_SESSION_NONE) {
		session_start(['cookie_lifetime' => 86400]);
	}
	require_once("config.php");
	require_once("setting/check_database.php");
	require_once("setting/language.php");
	if(isset($connected) && $connected == 1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables)) >= count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user = $connection->query("SELECT * FROM " . $sub_name . "admins WHERE username='" . $_SESSION['username'] . "' AND act=1");
			$user_stats = $res_user->rowCount();
			if($user_stats == 1 && (checkPermission(1,getTableByName($sub_name."table_permissions")['id'],"create",getTableByName($sub_name."table_permissions")['act'],"") && checkPermission(1,getTableByName($sub_name."table_permissions")['id'],"read",getTableByName($sub_name."table_permissions")['act'],"") && checkPermission(1,getTableByName($sub_name."table_permissions")['id'],"update",getTableByName($sub_name."table_permissions")['act'],"") && checkPermission(1,getTableByName($sub_name."table_permissions")['id'],"delete",getTableByName($sub_name."table_permissions")['act'],"")) && (checkPermission(1,getTableByName($sub_name."menu_permissions")['id'],"create",getTableByName($sub_name."menu_permissions")['act'],"") && checkPermission(1,getTableByName($sub_name."menu_permissions")['id'],"read",getTableByName($sub_name."menu_permissions")['act'],"") && checkPermission(1,getTableByName($sub_name."menu_permissions")['id'],"update",getTableByName($sub_name."menu_permissions")['act'],"") && checkPermission(1,getTableByName($sub_name."menu_permissions")['id'],"delete",getTableByName($sub_name."menu_permissions")['act'],"")) || isset($op_admin) && $op_admin){
?>
<div class="row">
	<div class="col-12 ml-auto mr-auto">
		<div class="card card-subcategories card-plain">
			<div class="card-body">
				<!--
				color-classes: "nav-pills-primary", "nav-pills-info", "nav-pills-success", "nav-pills-warning","nav-pills-danger"
				-->
				<ul class="nav nav-pills nav-pills-primary nav-pills-icons justify-content-center">
					<li class="nav-item">
						<a class="subcategories-link nav-link data-text active" data-toggle="tab" data-text-en='<i class="fal fa-table"></i> Tables and Columns' data-text-fa='<i class="fal fa-table"></i> جدول ها و ستون ها' href="#table_column_permission">
							<i class="fal fa-table"></i> <?php if($GLOBALS['user_language']=='en'){?>Tables and Columns<?php }else{?>جدول ها و ستون ها<?php } ?>
						</a>
					</li>
					<li class="nav-item">
						<a class="subcategories-link nav-link data-text" data-toggle="tab" data-text-en='<i class="fal fa-table"></i> <?php print_r($languages_data['en']['permissions'][$last_name['permissions']['tables_permission'].'_permission']['menu_title']); ?>' data-text-fa='<i class="fal fa-table"></i> <?php print_r($languages_data['fa']['permissions'][$last_name['permissions']['tables_permission'].'_permission']['menu_title']); ?>' href="#<?php print_r($last_name['permissions']['tables_permission']); ?>_permission_call">
							<i class="far fa-table"></i> <?php print_r($languages_data[$GLOBALS['user_language']]['permissions'][$last_name['permissions']['tables_permission'].'_permission']['menu_title']); ?>
						</a>
					</li>
					<li class="nav-item">
						<a class="subcategories-link nav-link data-text" data-toggle="tab" data-text-en='<i class="fal fa-line-columns"></i> Columns' data-text-fa='<i class="fal fa-line-columns"></i> ستون ها' href="#<?php print_r($last_name['permissions']['columns_permission']); ?>_permission_call">
							<i class="fal fa-line-columns"></i> <?php if($GLOBALS['user_language']=='en'){?>Columns<?php }else{?>ستون ها<?php } ?>
						</a>
					</li>
					<li class="nav-item">
						<a class="subcategories-link nav-link data-text" data-toggle="tab" data-text-en='<i class="fal fa-bars"></i> Menus' data-text-fa='<i class="fal fa-bars"></i> منو ها' href="#menu_permission">
							<i class="fal fa-bars"></i> <?php if($GLOBALS['user_language']=='en'){?>Menus<?php }else{?>منو ها<?php } ?>
						</a>
					</li>
				</ul>
				<div class="tab-content tab-space tab-subcategories">
					<div class="tab-pane active" id="table_column_permission">
						<?php require_once("permissions/permissions_table_column.php"); ?>
					</div>
					<div class="tab-pane" id="<?php print_r($last_name['permissions']['tables_permission']); ?>_permission_call">
						<?php require_once("permissions/permissions_table.php"); ?>
					</div>
					<div class="tab-pane" id="<?php print_r($last_name['permissions']['columns_permission']); ?>_permission_call">
						<?php require_once("permissions/permissions_column.php"); ?>
					</div>
					<div class="tab-pane" id="menu_permission">
						<?php require_once("permissions/permissions_menu.php"); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var $modal_interval_table_column_permission="",
	$modal_interval_table_permission_call="",
	$modal_interval_column_permission_call="",
	$modal_interval_menu_permission="";
	$(document).ready(function(){
		callDataTable();
		$.fn.dataTable.ext.errMode = 'none';
	});

	function selectAllFixer($this){
		var $this=$($this);
		var $val=$this.val();
		var $scroll_top=$("button[data-id='"+$this.attr("id")+"']").next().children(".inner.show").scrollTop();

		if($this.val().indexOf('-1')!="-1" || $this.val().indexOf('_all')!="-1"){
			$("#"+$this.attr("id")+" option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",true);
			$this.val(['-1','_all']).selectpicker('refresh');
		}else if($this.val().toString().indexOf("-2")!=-1 && $this.val().toString().indexOf("-3")!=-1){
			$("#"+$this.attr("id")+" option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",true);
			$this.val(['-1','_all']).selectpicker('refresh');
		}else if($this.val().toString().indexOf("-2")!=-1){
			$("#"+$this.attr("id")+" option.users-option").each(function() {
				delete $val[$val.indexOf($(this).val())];
			});
			$("#"+$this.attr("id")+" option.users-option").attr("disabled",true);
			$this.val($val).selectpicker('refresh');
		}else if($this.val().toString().indexOf("-3")!=-1){
			$("#"+$this.attr("id")+" option.ranks-option").each(function() {
				delete $val[$val.indexOf($(this).val())];
			});
			$("#"+$this.attr("id")+" option.ranks-option").attr("disabled",true);
			$this.val($val).selectpicker('refresh');
		}else{
			$("#"+$this.attr("id")+" option:not(.select-all-opt):not([value='-1']):not([value='_all'])").attr("disabled",false);
			$this.selectpicker('refresh');
		}

		$("button[data-id='"+$this.attr("id")+"']").next().children(".bs-searchbox").children("input[type='search']").trigger('propertychange');
		$("button[data-id='"+$this.attr("id")+"']").next().children(".inner.show").scrollTop($scroll_top);
	}

	$(document).on("change","select.select-all-opt",function(){
		selectAllFixer(this);
	});

	$(document).on("change","select.selectpicker",function(){
		$(this).next().next().children(".bs-searchbox").children("input[type='search']").blur();
	});

	function callDataTable(){
		if (typeof callDataTable_<?php print_r($last_name['permissions']['tables_permission']); ?> !== "undefined") {
			callDataTable_<?php print_r($last_name['permissions']['tables_permission']); ?>();
		}
		if (typeof callDataTable_<?php print_r($last_name['permissions']['columns_permission']); ?> !== "undefined") {
			callDataTable_<?php print_r($last_name['permissions']['columns_permission']); ?>();
		}
		if (typeof callDataTable_<?php print_r($last_name['permissions']['menu_permission']); ?> !== "undefined") {
			callDataTable_<?php print_r($last_name['permissions']['menu_permission']); ?>();
		}
	}

	// Needed
	$("select.selectpicker").selectpicker({
		iconBase: "tim-icons",
		tickIcon: "icon-check-2"
	});
</script>
<?php
			}else{
				echo $outofpermission;
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
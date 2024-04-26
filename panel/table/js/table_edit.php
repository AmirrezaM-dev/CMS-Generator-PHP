<?php
	if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_SESSION['username']."' AND act=1");
			$user_stats=$res_user->rowCount();
			if($user_stats==1 || getSetting("op_admin")==$_SESSION['username']){
?>
	<script>
		var $saving_this_are=0, $is_savethis_finished,$bg_color_controler_empty="";
		function backToTable(no_warn){
			if(no_warn){
				window.location.hash="tables?name=<?php print_r($_GET['name']); ?>";
			}else{
				try {
					if(alertShowerSetting['backToTable_showAlert']=="true"){
						window.location.hash="tables?name=<?php print_r($_GET['name']); ?>";
					}else{
						if(language=="fa"){
							Swal.fire({
								title: 'آیا مطمئن  هستید؟',
								text: "با بازگشت به صفحه قبل تغییرات وارد شده ثبت نخواهند شد !",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'بله',
								cancelButtonText: 'لغو',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'backToTable_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
							}).then((result) => {
								if(result.value) {
									window.location.hash="tables?name=<?php print_r($_GET['name']); ?>";
								}
							});
						}else{
							Swal.fire({
								title: 'Are you sure?',
								text: "With returning to the previous page, the changes made will not be recorded!",
								icon: 'warning',
								showCancelButton: true,
								customClass: {
									confirmButton: 'btn btn-success',
									cancelButton: 'btn btn-danger'
								},
								buttonsStyling: false,
								confirmButtonText: 'Yes',
								footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'backToTable_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
							}).then((result) => {
								if(result.value) {
									window.location.hash="tables?name=<?php print_r($_GET['name']); ?>";
								}
							});
						}
					}
				} catch(err) {
					alertShowerSetting['backToTable_showAlert']="false";
					backToTable();
				}
			}
		}
		function clearInputs(){
			try{
				function clearDatas() {
					$(".clear_single_column").click();
				}
				if(alertShowerSetting['clearInputs_showAlert']=="true"){
					clearDatas();
				}else{
					if(language=="fa"){
						Swal.fire({
							title: 'آیا مطمئن  هستید؟',
							text: "آیا شما میخواهید تمام موارد در تمام فیلد ها را پاک کنید؟",
							icon: 'warning',
							showCancelButton: true,
							customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
							buttonsStyling: false,
							confirmButtonText: 'بله',
							cancelButtonText: 'نه منصرف شدم',
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'clearInputs_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
						}).then((result) => {
							if(result.value) {
								clearDatas();
							}
						});
					}else{
						Swal.fire({
							title: 'Are you sure?',
							text: "Do you want to delete all items in all fields?",
							icon: 'warning',
							showCancelButton: true,
							customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
							buttonsStyling: false,
							confirmButtonText: 'Yes',
							cancelButtonText: 'No im not sure',
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'clearInputs_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
						}).then((result) => {
							if(result.value) {
								clearDatas();
							}
						});
					}
				}
			} catch(err) {
				alertShowerSetting['clearInputs_showAlert']="false";
				clearInputs();
			}
		}
		function resetInputs(){
			try{
				function resetDatas() {
					$(".reset_single_column").click();
				}
				if(alertShowerSetting['resetInputs_showAlert']=="true"){
					resetDatas();
				}else{
					if(language=="fa"){
						Swal.fire({
							title: 'آیا مطمئن  هستید؟',
							text: "شما میخواهید همه چیز به حالت اصلی باز گردد؟",
							icon: 'warning',
							showCancelButton: true,
							customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
							buttonsStyling: false,
							confirmButtonText: 'بله',
							cancelButtonText: 'نه منصرف شدم',
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'resetInputs_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
						}).then((result) => {
							if(result.value) {
								resetDatas();
							}
						});
					}else{
						Swal.fire({
							title: 'Are you sure?',
							text: "Do you want everything to become like first?",
							icon: 'warning',
							showCancelButton: true,
							customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
							buttonsStyling: false,
							confirmButtonText: 'Yes',
							cancelButtonText: 'No im not sure',
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'resetInputs_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
						}).then((result) => {
							if(result.value) {
								resetDatas();
							}
						});
					}
				}
			} catch(err) {
				alertShowerSetting['resetInputs_showAlert']="false";
				resetInputs();
			}
		}
		function deleteThis(){
			try{
				function deleteThisColumn() {
					LoadingScreen("",(language=="en" ? "Deleting ...":"در حال حذف ..."),(language=="en" ? "It's will take few seconds !":"ممکن است چند ثانیه طول بکشد ..."));
					$.post("table/class/table_simple_action.php?deleteThis",{"table_id":<?php print_r($table_get['id']); ?>,"id":"<?php print_r($_GET["id"]); ?>"},function(data,status){
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							Swal.fire({
								icon: 'success',
								title: (language=="en" ? "Successfully deleted":"با موفقیت حذف شد"),
								timer: 1000,
								showConfirmButton: false,
								willClose: function () {backToTable(1);}
							});
						}else{
							feedbackOperations(data);
						}
					});
				}
				if(alertShowerSetting['deleteThis_showAlert']=="false"){
					if(language=="fa"){
						Swal.fire({
							title: 'آیا مطمئن  هستید؟',
							text: "شما میخواهید تمام این موارد حذف شود؟",
							icon: 'warning',
							showCancelButton: true,
							customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
							buttonsStyling: false,
							confirmButtonText: 'بله',
							cancelButtonText: 'نه منصرف شدم',
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteThis_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
						}).then((result) => {
							if(result.value) {
								deleteThisColumn();
							}
						});
					}else{
						Swal.fire({
							title: 'Are you sure?',
							text: "Do you want delete all of this data?",
							icon: 'warning',
							showCancelButton: true,
							customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
							buttonsStyling: false,
							confirmButtonText: 'Yes',
							cancelButtonText: 'No im not sure',
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteThis_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
						}).then((result) => {
							if(result.value) {
								deleteThisColumn();
							}
						});
					}
				}else{
					deleteThisColumn();
				}
			} catch(err) {
				alertShowerSetting['deleteThis_showAlert']="false";
				deleteThis();
			}
		}
		function cleanThis(e_id){
			<?php //info search this_is_modes_for_data_tables for see all things about this part ?>
			var $input_options_arr=e_id.split("_-_");
			switch ($input_options_arr[3]) {//tables_mode_code
				case "1":case 1:case "5":case 5:case "12":case 12:case "13":case 13:case "14":case 14:case "15":case 15:case "16":case 16://info search case 13 for see all things about this part//info search case 16 for see all things about this part//info search case 12 for see all things about this part//info search case 1 for see all things about this part//info search case 5 for see all things about this part//info search case 14 for see all things about this part//info search case 15 for see all things about this part
					$("#" + e_id).val("");
				break;
				case "2":case 2://info search case 2 for see all things about this part
					$("#" + e_id).val(0);
				break;
				case "3":case 3://info search case 3 for see all things about this part
					$("#" + e_id).prop('checked', false).change();
				break;
				case "4":case 4://info search case 4 for see all things about this part
					$("#" + e_id).selectpicker("val","");
				break;
				case "6":case 6://info search case 6 for see all things about this part
					$("#" + e_id).val("");
					$("#" + e_id + "_-_keep_old_password").prop('checked', false).change();
				break;
				case "7":case 7://info search case 7 for see all things about this part
					$("#" + e_id + "_url").val("").removeClass("activated-file");
					$("#" + e_id + "_file").val("").removeClass("activated-file");
					$("#" + e_id + "_file").next().html(language=="en" ? "Choose":"انتخاب");
					$("#" + e_id + "_-_keep_old_file").prop('checked', false).change();
				break;
				case "8":case 8:case '19':case 19://info search case 19 for see all things about this part//info search case 8 for see all things about this part
					CKEDITOR.instances[e_id].setData("");
				break;
				case "9":case 9://info search case 9 for see all things about this part
					$("[data-likeid='"+e_id+"']").prop('checked', false).change();
				break;
				case "10":case 10://info search case 10 for see all things about this part
					$("#" + e_id).prev().children().children().removeAttr().addClass("far fa-question-circle");
					$("#" + e_id).val("").change();
				break;
				case "12":case 12:case "13":case 13:case "14":case 14:case "15":case 15:case "16":case 16://info search case 12 for see all things about this part//info search case 13 for see all things about this part//info search case 14 for see all things about this part//info search case 15 for see all things about this part//info search case 16 for see all things about this part
					$("#" + e_id).val("").change();
				break;
				case '17':case 17://info search case 17 for see all things about this part
					document.getElementById(e_id).noUiSlider.set(0);
				break;
				case '18':case 18://info search case 18 for see all things about this part
					$("#" + e_id).tagsinput("destroy");
					$("#" + e_id).val("");
					$("#" + e_id).tagsinput();
				break;
				default:
					$("#" + e_id).val("");
				break;
			}
		}
		function resetThis(e_id){
			<?php //info search this_is_modes_for_data_tables for see all things about this part ?>
			var $input_options_arr=e_id.split("_-_");
			switch ($input_options_arr[3]) {//tables_mode_code
				case "1":case 1://info search case 1 for see all things about this part
					var resetData=$("#" + e_id).parent().children(".reset_data"),resetText=resetData.html();
					$("#" + e_id).val(resetText);
				break;
				case "2":case 2://info search case 2 for see all things about this part
					var resetData=$("#" + e_id).parent().children(".reset_data"),resetText=resetData.html();
					$("#" + e_id).val(resetText.length ? parseInt(resetText):0);
				break;
				case "3":case 3://info search case 3 for see all things about this part
					var resetData=$("#" + e_id).parent().parent().parent().children(".reset_data"),resetText=resetData.html();
					if(resetText=="checked"){
						$("#" + e_id).prop('checked', true).change();
					}else{
						$("#" + e_id).prop('checked', false).change();
					}
				break;
				case "4":case 4://info search case 4 for see all things about this part
					var resetData=$("#" + e_id).parent().parent().children(".reset_data"),resetText=resetData.html();
					if(resetText.indexOf("_-.,.-_")){
						resetText=resetText.split("_-.,.-_");
					}
					$("#" + e_id).selectpicker("val",resetText);
				break;
				case "5":case 5://info search case 5 for see all things about this part
					var resetData=$("#" + e_id).parent().parent().children(".reset_data"),resetText=resetData.html();
					$("#" + e_id).val(resetText);
					$("#" + e_id).spectrum("set", resetText);
				break;
				case "6":case 6://info search case 6 for see all things about this part
					$("#" + e_id).val("");
					$("#" + e_id + "_-_keep_old_password").prop('checked', true).change();
				break;
				case "7":case 7://info search case 7 for see all things about this part
					var resetData=$("#" + e_id + "_progress").next(".reset_data"),resetText=resetData.html();
					$("#" + e_id+ "_url").val(resetText);
					$("#" + e_id+ "_file").val("");
					$("#" + e_id + "_file").next().html(language=="en" ? "Choose":"انتخاب");
					$("#" + e_id + "_-_keep_old_file").prop('checked', true).change();
				break;
				case "8":case 8:case '19':case 19://info search case 19 for see all things about this part//info search case 8 for see all things about this part
					var resetData=$("#" + e_id).parent().children(".reset_data"),resetText=resetData.html();
					CKEDITOR.instances[e_id].setData(resetText);
				break;
				case "9":case 9://info search case 9 for see all things about this part
					var resetData=$("[data-likeid='"+e_id+"']:first");
					resetData=(resetData.attr("type")=="radio" ? resetData.parent().parent().parent().children(".reset_data"):resetData.parent().parent().parent().parent().children(".reset_data"));
					var resetText=resetData.html();
					resetText.split("_-...-_").forEach(function(this_arr){
						$("[data-likeid='"+e_id+"'][value='"+this_arr+"']").prop('checked', true).change();
					});
				break;
				case "10":case 10://info search case 10 for see all things about this part
					var resetData=$("#" + e_id).parent().parent().children(".reset_data"),resetText=resetData.html();
					if(resetText==""){
						$("#" + e_id).prev().children().children().removeAttr().addClass("far fa-question-circle");
					}
					$("#" + e_id).val(resetText).change();
					if(resetText.length==0){
						$("#" + e_id).parent().children(".input-group-prepend").children(".input-group-text").children("i").addClass("far fa-question-circle");
					}
				break;
				case "12":case 12:case "13":case 13:case "14":case 14:case "15":case 15:case "16":case 16://info search case 12 for see all things about this part//info search case 13 for see all things about this part//info search case 14 for see all things about this part//info search case 15 for see all things about this part//info search case 16 for see all things about this part
					var resetData=$("#" + e_id).parent().children(".reset_data"),resetText=resetData.html();
					if(resetText.length!=0 && parseInt(resetText)!=0){
						$fa_datetimepicker["#" + e_id].setDate(parseInt(resetText));
					}
				break;
				case '17':case 17://info search case 17 for see all things about this part
					var resetData=$("#" + e_id).parent().children(".reset_data"),resetText=resetData.html();
					document.getElementById(e_id).noUiSlider.set(parseInt(resetText));
				break;
				case '18':case 18://info search case 18 for see all things about this part
					var resetData=$("#" + e_id).parent().children(".reset_data"),resetText=resetData.html();
					$("#" + e_id).prev().children("input").val("").focusin().focusout().val(resetText).focusin().focusout();
				break;
				default:
					var resetData=$("#" + e_id).parent().children(".reset_data"),resetText=resetData.html();
					$("#" + e_id).val(resetText);
				break;
			}
		}
		function saveThis(e_id,buttons){
			if(typeof buttons !== "undefined"){
				$(buttons).attr("disabled",true).children("i").removeClass("fa-save fas").addClass("fad fa-spinner-third fa-spin");
			}
			var $column_infos=e_id.split("_-_"),$bg_color_controler,$empty_error=0;
			if($column_infos.length>=5){
				$("#" + e_id).addClass("saving-input");
				<?php //info search this_is_modes_for_data_tables for see all things about this part ?>
				switch ($column_infos[3]) {//tables_mode_code
					case "1":case 1://info search case 1 for see all things about this part
						if($("#" + e_id).hasClass("save_important") && $("#" + e_id).val().length==0){
							$empty_error=1;
						}
						$bg_color_controler=$("#" + e_id);
						var $postObjects={"table_id":<?php print_r($table_get['id']); ?>,"column_id":$column_infos[2],"id":$column_infos[4],"normal_data":$("#" + e_id).val()};
					break;
					case "2":case 2://info search case 2 for see all things about this part
						if($("#" + e_id).hasClass("save_important") && $("#" + e_id).val().length==0){
							$empty_error=1;
						}
						$bg_color_controler=$("#" + e_id);
						var $postObjects={"table_id":<?php print_r($table_get['id']); ?>,"column_id":$column_infos[2],"id":$column_infos[4],"normal_data":(parseInt($("#" + e_id).val()).toString().length ? parseInt($("#" + e_id).val()):0)};
					break;
					case '3':case 3://info search case 3 for see all things about this part
						$bg_color_controler=$(".bootstrap-switch-id-" + e_id);
						var $postObjects={"table_id":<?php print_r($table_get['id']); ?>,"column_id":$column_infos[2],"id":$column_infos[4],"yes_no_data":($("#" + e_id).is(":checked") ? 1:0)};
					break;
					case '4':case 4://info search case 4 for see all things about this part
						if($("#" + e_id).hasClass("save_important") && $("#" + e_id).val().length==0){
							$empty_error=1;
						}
						if(typeof $("#" + e_id).val() == "string"){
							$bg_color_controler=$("[data-id='" + e_id+"']");
							var $postObjects={"table_id":<?php print_r($table_get['id']); ?>,"column_id":$column_infos[2],"id":$column_infos[4],"normal_data":$("#" + e_id).val()};
						}else{
							$bg_color_controler=$("[data-id='" + e_id+"']");
							var $postObjects={"table_id":<?php print_r($table_get['id']); ?>,"column_id":$column_infos[2],"id":$column_infos[4],"normal_data":JSON.stringify($("#" + e_id).val())};
						}
					break;
					case "5":case 5://info search case 5 for see all things about this part
						if($("#" + e_id).hasClass("save_important") && $("#" + e_id).val().length==0){
							$empty_error=1;
						}
						$bg_color_controler=$("#" + e_id).next();
						var $postObjects={"table_id":<?php print_r($table_get['id']); ?>,"column_id":$column_infos[2],"id":$column_infos[4],"normal_data":$("#" + e_id).val()};
					break;
					case '6':case 6://info search case 6 for see all things about this part
						if($("#" + e_id).hasClass("save_important") && $("#" + e_id).val().length==0 && !$("#" + e_id + "_-_keep_old_password").is(":checked")){
							$empty_error=1;
						}
						$bg_color_controler=$("#" + e_id);
						var $postObjects={"table_id":<?php print_r($table_get['id']); ?>,"column_id":$column_infos[2],"id":$column_infos[4],"normal_data":$("#" + e_id).val(),"keep_password":($("#" + e_id + "_-_keep_old_password").is(":checked") ? 1:0)};
					break;
					case '7':case 7://info search case 7 for see all things about this part
						if($("#" + e_id + "_file").parent().hasClass("activated-file")){
							$bg_color_controler=$("#" + e_id + "_file").parent();
							if(typeof $("#" + e_id + "_file")[0].files[0] !== "undefined"){
								var $val_val=$("#" + e_id + "_file")[0].files[0];
							}else{
								var $val_val="";
								if($("#" + e_id + "_file").hasClass("save_important") && !$("#" + e_id + "_-_keep_old_file").is(":checked")){
									$empty_error=1;
								}
							}
						}else if($("#" + e_id + "_url").hasClass("activated-file")){
							$bg_color_controler=$("#" + e_id + "_url");
							var $val_val=$("#" + e_id + "_url").val();
							if($("#" + e_id + "_url").hasClass("save_important") && !$("#" + e_id + "_-_keep_old_file").is(":checked") && $val_val.length==0){
								$empty_error=1;
							}
						}else{
							$bg_color_controler=$("#" + e_id + "_url").parent();
							var $val_val="";
							if($("#" + e_id + "_url").parent().hasClass("save_important") && !$("#" + e_id + "_-_keep_old_file").is(":checked")){
								$empty_error=1;
							}
						}
						var $postObjects=new FormData();
						$postObjects.append("table_id", <?php print_r($table_get['id']); ?>);
						$postObjects.append("column_id", $column_infos[2]);
						$postObjects.append("id", $column_infos[4]);
						$postObjects.append("normal_data", $val_val);
						$postObjects.append("keep_old_file", ($("#" + e_id + "_-_keep_old_file").is(":checked") ? 1:0));
						$("#" + e_id + "_-_keep_old_file").prop("checked",true).change();
					break;
					case '8':case 8:case '19':case 19://info search case 19 for see all things about this part//info search case 8 for see all things about this part
						$bg_color_controler=$("#" + CKEDITOR.instances[e_id].id + "_top, #" + CKEDITOR.instances[e_id].id + "_bottom");
						if($("#" + e_id).hasClass("save_important") && CKEDITOR.instances[e_id].getData().length==0){
							$empty_error=1;
						}
						var $postObjects={"table_id":<?php print_r($table_get['id']); ?>,"column_id":$column_infos[2],"id":$column_infos[4],"normal_data":CKEDITOR.instances[e_id].getData()};
					break;
					case '9':case 9://info search case 9 for see all things about this part
						$bg_color_controler=$("[data-likeid='"+e_id+"']:first").parent().parent().parent();
						$normal_data="";
						$("[data-likeid='"+e_id+"']").each(function(){
							if($(this).is(":checked")){
								if($normal_data==""){
									$normal_data=$(this).val();
								}else{
									$normal_data+="_-...-_"+$(this).val();
								}
							}
						});
						if($("#" + e_id).hasClass("save_important") && $normal_data.length==0){
							$empty_error=1;
						}
						var $postObjects={"table_id":<?php print_r($table_get['id']); ?>,"column_id":$column_infos[2],"id":$column_infos[4],"normal_data":$normal_data};
					break;
					case '10':case 10://info search case 10 for see all things about this part
						if($("#" + e_id).hasClass("save_important") && $("#" + e_id).val().length==0){
							$empty_error=1;
						}
						$bg_color_controler=$("#" + e_id).parent();
						var $postObjects={"table_id":<?php print_r($table_get['id']); ?>,"column_id":$column_infos[2],"id":$column_infos[4],"normal_data":$("#" + e_id).val()};
					break;
					case "12":case 12:case "13":case 13:case "14":case 14:case "15":case 15:case "16":case 16://info search case 12 for see all things about this part//info search case 13 for see all things about this part//info search case 14 for see all things about this part//info search case 15 for see all things about this part//info search case 16 for see all things about this part
						$bg_color_controler=$("#" + e_id);
						var $val_to_send=($("#" + e_id).val().length ? $fa_datetimepicker["#" + e_id].getState().view.unixDate:"");
						if($("#" + e_id).val().length==0){
							$val_to_send=0;
							if($("#" + e_id).hasClass("save_important")){
								$empty_error=1;
							}
						}
						var $postObjects={"table_id":<?php print_r($table_get['id']); ?>,"column_id":$column_infos[2],"id":$column_infos[4],"normal_data":$val_to_send};
					break;
					case '17':case 17://info search case 17 for see all things about this part
						$bg_color_controler=$("#" + e_id);
						if($("#" + e_id).hasClass("save_important") && document.getElementById(e_id).noUiSlider.get().length==0){
							$empty_error=1;
						}
						var $postObjects={"table_id":<?php print_r($table_get['id']); ?>,"column_id":$column_infos[2],"id":$column_infos[4],"normal_data":document.getElementById(e_id).noUiSlider.get()};
					break;
					case '18':case 18://info search case 18 for see all things about this part
						if($("#" + e_id).hasClass("save_important") && $("#" + e_id).val().length==0){
							$empty_error=1;
						}
						$bg_color_controler=$("#" + e_id).prev();
						var $postObjects={"table_id":<?php print_r($table_get['id']); ?>,"column_id":$column_infos[2],"id":$column_infos[4],"normal_data":$("#" + e_id).val()};
					break;
					default:
						if($("#" + e_id).hasClass("save_important") && $("#" + e_id).val().length==0){
							$empty_error=1;
						}
						$bg_color_controler=$("#" + e_id);
						var $postObjects={"table_id":<?php print_r($table_get['id']); ?>,"column_id":$column_infos[2],"id":$column_infos[4]};
					break;
				}
				$.when($bg_color_controler.removeClass("bg-success bg-warning").addClass("bg-info")).done(function(){
					if($empty_error==0){
						if($column_infos[3]=="7" || $column_infos[3]==7){
							$.ajax({
								url: 'table/class/table_simple_action.php?update_single_column',
								type: 'POST',
								data: $postObjects,
								cache: false,
								contentType: false,
								processData: false,
								xhr: function () {
									var myXhr = $.ajaxSettings.xhr();
									if (myXhr.upload) {
										myXhr.upload.addEventListener('progress', function (e) {
											if (e.lengthComputable) {
												percentageUpdater=Math.round(e.loaded/e.total*100);
												$("#" + e_id + "_progress").removeClass("hide");
												$("#" + e_id + "_progress").children(".progress").children(".progress-bar").attr("aria-valuenow",percentageUpdater).css("width",percentageUpdater+'%');
												$("#" + e_id + "_progress").children(".progress").children(".progress-value").html(percentageUpdater + "%");
											}
										}, false);
									}
									return myXhr;
								},
								complete: function(xhr,status) {
									var data=xhr.responseText;
									if (status == "success" && data.toString().indexOf("success")!=-1) {
										$bg_color_controler.removeClass("bg-info").addClass("bg-success");
										setTimeout(function(){
											$bg_color_controler.removeClass("bg-success");
										}, 500);
										$("#" + e_id + "_progress").children(".progress").children(".progress-bar").addClass("bg-success").removeClass("bg-info");
										$("#" + e_id + "_file").val("");
										$("#" + e_id + "_file").next().html((language=="en" ? "Uploaded":"ارسال شد"));
										$saving_this_are++;
									}else{
										$bg_color_controler.removeClass("bg-info bg-success").addClass("bg-warning");
										setTimeout(function(){
											$bg_color_controler.removeClass("bg-warning");
										}, 500);
										cancelSaveAllThis();
										feedbackOperations(data);
										$("#" + e_id + "_progress").children(".progress").children(".progress-bar").addClass("bg-danger").removeClass("bg-info");
									}
									$("#" + e_id + "_progress").fadeOut("500");
									setTimeout(() => {
										if(typeof buttons !== "undefined"){
											$(buttons).attr("disabled",false).children("i").removeClass("fa-spinner-third fa-spin fad").addClass("fas fa-save");
										}
										$("#" + e_id + "_progress").addClass("hide").fadeIn("0");
										$("#" + e_id + "_progress").children(".progress").children(".progress-bar").attr("aria-valuenow",0).css("width",0+'%').removeClass("bg-success bg-danger").addClass("bg-info");
										$("#" + e_id + "_progress").children(".progress").children(".progress-value").html(0 + "%");
									}, 500);
								}
							});
						}else{
							$.post("table/class/table_simple_action.php?update_single_column",$postObjects,function(data,status){
								if (status == "success" && data.toString().indexOf("success")!=-1) {
									$bg_color_controler.removeClass("bg-info").addClass("bg-success");
									setTimeout(function(){
										$bg_color_controler.removeClass("bg-success");
									}, 500);
									$saving_this_are++;
								}else{
									$bg_color_controler.removeClass("bg-info bg-success").addClass("bg-warning");
									setTimeout(function(){
										$bg_color_controler.removeClass("bg-warning");
									}, 500);
									cancelSaveAllThis();
									feedbackOperations(data);
								}
								setTimeout(() => {
									if(typeof buttons !== "undefined"){
										$(buttons).attr("disabled",false).children("i").removeClass("fa-spinner-third fa-spin fad").addClass("fas fa-save");
									}
								}, 500);
							}).done(function (data,status,$more) {

							}).fail(function ($more,error,not_found) {

							}).always(function (data,status,$more) {
								
							});
						}
					}else{
						$bg_color_controler.removeClass("bg-info bg-success").addClass("bg-warning shaker");
						if($bg_color_controler_empty=="") $bg_color_controler_empty=$bg_color_controler;
						setTimeout(() => {
							$bg_color_controler.removeClass("bg-info bg-success bg-warning shaker");
						}, 1000);
						$(buttons).attr("disabled",false).children("i").removeClass("fa-spinner-third fa-spin fad").addClass("fas fa-save");
						setTimeout(() => {
							cancelSaveAllThis(language=="en" ? "Filling important inputs are required !":"پر کردن فیلد های مهم مورد نیاز است !");
						}, 1500);
					}
				});
			}
		}
		function saveInputs(vals,buttons){
			$bg_color_controler_empty="";
			$saving_this_are=0;
			var saveText=$(buttons).html();
			$(buttons).attr("disabled",true).html("").append("<i class='fad fa-spinner-third fa-spin'></i>");
			$(".save_single_column").click();
			if(language=='en'){
				LoadingScreen("","Saving");
			}else if(language=='fa'){
				LoadingScreen("","ذخیره سازی");
			}
			$is_savethis_finished=setInterval(() => {
				if($saving_this_are>=$(".save_single_column").length){
					$(buttons).html(saveText).attr("disabled",false).children("i").remove();
					swal.fire({icon:"success",timer: 1000,showConfirmButton: false});
					clearInterval($is_savethis_finished);
					$is_savethis_finished="";
					if(typeof vals !== "undefined" && vals=="back"){
						backToTable(1);
					}
				}
			}, 500);
		}
		function cancelSaveAllThis($message){
			$(".save_all_this, .save_all_this_back").attr("disabled",false).children("i").remove();
			$(".save_all_this").html(language=="en" ? "Save":"ذخیره");
			$(".save_all_this_back").html(language=="en" ? "Save & Back":"ذخیره و بازگشت");
			if($bg_color_controler_empty!=""){
				var $empty_detected=$bg_color_controler_empty;
				$bg_color_controler_empty="";
				scrollToElement($empty_detected);
				$empty_detected.addClass("bg-warning shaker").removeClass("bg-info bg-success");
				setTimeout(() => {
					$empty_detected.removeClass("bg-warning shaker bg-info bg-success");
				}, 1500);
			}
			$message=(typeof $message !== "undefined" ? $message:"");
			$saving_this_are=0;
			clearInterval($is_savethis_finished);
			$is_savethis_finished="";
			Swal.fire({
				icon: 'error',
				title: ($message.length ? $message:(language=="en" ? "Something went wrong ...":"مشکلی رخ داده است ...")),
				timer: 1500,
				showConfirmButton: false
			});
			// if(typeof swal.close() !=="undefined"){swal.close();}
		}

		$(".selectpicker").selectpicker({
			iconBase: "far",
			tickIcon: "fa-check"
		});
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
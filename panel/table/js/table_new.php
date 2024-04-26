<?php
	if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_SESSION['username']."' AND act=1");
			$user_stats=$res_user->rowCount();
			if($user_stats==1 || getSetting("op_admin")==$_SESSION['username']){
?>
	<script>
		function NEWbackToTable(no_warn){
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
					NEWbackToTable();
				}
			}
		}
		function NEWclearInputs(){
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
		function NEWresetInputs(){
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
		function NEWdeleteThis(){
			try{
				function deleteThisColumn() {
					NEWbackToTable();
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
		function NEWcleanThis(e_id){
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
		function NEWresetThis(e_id){
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
		function NEWsaveInputs(vals,buttons){
			var $bg_color_controler="",$haveToSave=new FormData(),$empty_error=0;
			var saveText=$(buttons).html();
			$(buttons).attr("disabled",true).html("").append("<i class='fad fa-spinner-third fa-spin'></i>");
			if(language=='en'){
				LoadingScreen("","Saving");
			}else if(language=='fa'){
				LoadingScreen("","ذخیره سازی");
			}
			<?php
				$res_newtable_column=$connection->query("SELECT * FROM ".$sub_name."table_column_config WHERE table_id='".$table_id."' AND created=1 AND current_name!='ordering' ORDER BY column_number ASC");
				while($newtable_column=$res_newtable_column->fetch()){
					if($newtable_column['mode']!=11 && $newtable_column['mode']!=20 && $newtable_column['mode']!=21){//info search case 11 for see all things about this part//info search case 20 for see all things about this part//info search case 21 for see all things about this part
						if(checkPermission(2,$newtable_column['id'],"create",$newtable_column['act'],$table_id)){
							if(isset($op_admin) && $op_admin || $newtable_column['creatable']==1){
								if($newtable_column['current_name']=="act"){
									$newtable_column['mode']=4;
								}
								$e_id="save_-_".preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $newtable_column['current_name'], 1)."_-_".$newtable_column['id']."_-_".$newtable_column['mode'];
								switch ($newtable_column['mode']) {//tables_mode_code
									//edit_data_tables_mode_input
									case '1':case 1:case '2':case 2:case '6':case 6://info search case 6 for see all things about this part//info search case 2 for see all things about this part//info search case 1 for see all things about this part
										?>
											if(<?php if($newtable_column['importants']){echo 1;}else{echo 0;} ?>==0 || $("#<?php print_r($e_id); ?>").val().length!=0){
												$haveToSave.append("<?php print_r($e_id); ?>", $("#<?php print_r($e_id); ?>").val());
											}else{
												$empty_error=1;
												if($bg_color_controler=="") $bg_color_controler=$("#<?php print_r($e_id); ?>");
											}
										<?php
									break;
									case '3':case 3://info search case 3 for see all things about this part
										?>
											$haveToSave.append("<?php print_r($e_id); ?>", ($("#<?php print_r($e_id); ?>").is(":checked") ? 1:0));
										<?php
									break;
									case '4':case 4://info search case 4 for see all things about this part
										?>
											if(<?php if($newtable_column['importants']){echo 1;}else{echo 0;} ?>==0 || $("#<?php print_r($e_id); ?>").val().length!=0){
												if(typeof $("#<?php print_r($e_id); ?>").val() == "string"){
													$haveToSave.append("<?php print_r($e_id); ?>", $("#<?php print_r($e_id); ?>").val());
												}else{
													$haveToSave.append("<?php print_r($e_id); ?>", JSON.stringify($("#<?php print_r($e_id); ?>").val()));
												}
											}else{
												$empty_error=1;
												if($bg_color_controler=="") $bg_color_controler=$("[data-id='<?php print_r($e_id); ?>']");
											}
										<?php
									break;
									case '5':case 5://info search case 5 for see all things about this part
										?>
											if(<?php if($newtable_column['importants']){echo 1;}else{echo 0;} ?>==0 || $("#<?php print_r($e_id); ?>").val().length!=0){
												$haveToSave.append("<?php print_r($e_id); ?>", $("#<?php print_r($e_id); ?>").val());
											}else{
												$empty_error=1;
												if($bg_color_controler=="") $bg_color_controler=$("#<?php print_r($e_id); ?>").next();
											}
										<?php
									break;
									case '7':case 7://info search case 7 for see all things about this part
										?>
											if($("#<?php print_r($e_id); ?>_file").parent().hasClass("activated-file")){
												if(typeof $("#<?php print_r($e_id); ?>_file")[0].files[0] !== "undefined"){
													var $val_val=$("#<?php print_r($e_id); ?>_file")[0].files[0];
												}else{
													var $val_val="";
													if($("#<?php print_r($e_id); ?>_file").hasClass("save_important")){
														$empty_error=1;
														if($bg_color_controler=="") $bg_color_controler=$("#<?php print_r($e_id); ?>_file").parent();
													}
												}
											}else if($("#<?php print_r($e_id); ?>_url").hasClass("activated-file")){
												var $val_val=$("#<?php print_r($e_id); ?>_url").val();
												if($("#<?php print_r($e_id); ?>_url").hasClass("save_important") && $val_val.length==0){
													if($bg_color_controler=="") $bg_color_controler=$("#<?php print_r($e_id); ?>_url");
													$empty_error=1;
												}
											}else{
												var $val_val="";
												if($("#<?php print_r($e_id); ?>_url").parent().hasClass("save_important")){
													$empty_error=1;
													if($bg_color_controler=="") $bg_color_controler=$("#<?php print_r($e_id); ?>_url").parent();
												}
											}
											$haveToSave.append("<?php print_r($e_id); ?>", $val_val);
										<?php
									break;
									case '8':case 8:case '19':case 19://info search case 19 for see all things about this part//info search case 8 for see all things about this part
										?>
											if($("#<?php print_r($e_id); ?>").hasClass("save_important") && CKEDITOR.instances["<?php print_r($e_id); ?>"].getData().length==0){
												$empty_error=1;
												if($bg_color_controler=="") $bg_color_controler=$("#" + CKEDITOR.instances["<?php print_r($e_id); ?>"].id + "_top, #" + CKEDITOR.instances["<?php print_r($e_id); ?>"].id + "_bottom");
											}
											$haveToSave.append("<?php print_r($e_id); ?>", CKEDITOR.instances["<?php print_r($e_id); ?>"].getData());
										<?php
									break;
									case '9':case 9://info search case 9 for see all things about this part
										?>
											$normal_data="";
											$("[data-likeid='<?php print_r($e_id); ?>']").each(function(){
												if($(this).is(":checked")){
													if($normal_data==""){
														$normal_data=$(this).val();
													}else{
														$normal_data+="_-...-_"+$(this).val();
													}
												}
											});
											if($("#<?php print_r($e_id); ?>").hasClass("save_important") && $normal_data.length==0){
												$empty_error=1;
												if($bg_color_controler=="") $bg_color_controler=$("[data-likeid='<?php print_r($e_id); ?>']:first").parent().parent().parent();
											}
											$haveToSave.append("<?php print_r($e_id); ?>", $normal_data);
										<?php
									break;
									case '10':case 10://info search case 10 for see all things about this part
										?>
											if($("#<?php print_r($e_id); ?>").hasClass("save_important") && $("#<?php print_r($e_id); ?>").val().length==0){
												$empty_error=1;
												if($bg_color_controler=="") $bg_color_controler=$("#<?php print_r($e_id); ?>").parent();
											}
											$haveToSave.append("<?php print_r($e_id); ?>", $("#<?php print_r($e_id); ?>").val());
										<?php
									break;
									case "12":case 12:case "13":case 13:case "14":case 14:case "15":case 15:case "16":case 16://info search case 12 for see all things about this part//info search case 13 for see all things about this part//info search case 14 for see all things about this part//info search case 15 for see all things about this part//info search case 16 for see all things about this part
										?>
											var $val_to_send=($("#<?php print_r($e_id); ?>").val().length ? $fa_datetimepicker["#<?php print_r($e_id); ?>"].getState().view.unixDate:"");
											if($("#<?php print_r($e_id); ?>").val().length==0){
												$val_to_send=0;
												if($("#<?php print_r($e_id); ?>").hasClass("save_important")){
													$empty_error=1;
													if($bg_color_controler=="") $bg_color_controler=$("#<?php print_r($e_id); ?>");
												}
											}
											$haveToSave.append("<?php print_r($e_id); ?>", $val_to_send);
										<?php
									break;
									case '17':case 17://info search case 17 for see all things about this part
										?>
											if($("#<?php print_r($e_id); ?>").hasClass("save_important") && document.getElementById("<?php print_r($e_id); ?>").noUiSlider.get().length==0){
												$empty_error=1;
												if($bg_color_controler=="") $bg_color_controler=$("#<?php print_r($e_id); ?>");
											}
											$haveToSave.append("<?php print_r($e_id); ?>", document.getElementById("<?php print_r($e_id); ?>").noUiSlider.get());
										<?php
									break;
									case '18':case 18://info search case 18 for see all things about this part
										?>
											if($("#<?php print_r($e_id); ?>").hasClass("save_important") && $("#<?php print_r($e_id); ?>").val().length==0){
												$empty_error=1;
												if($bg_color_controler=="") $bg_color_controler=$("#<?php print_r($e_id); ?>").prev();
											}
											$haveToSave.append("<?php print_r($e_id); ?>", $("#<?php print_r($e_id); ?>").val());
										<?php
									break;
								}
							}
						}
					}
				}
			?>
			$haveToSave.append("table_id", <?php print_r($table_get['id']); ?>);
			if($empty_error){
				$bg_color_controler.addClass("bg-warning shaker").removeClass("bg-info bg-success");
				setTimeout(() => {
					$bg_color_controler.removeClass("bg-info bg-success bg-warning shaker");
				}, 1500);
				scrollToElement($bg_color_controler);
				NEWcancelSaveAllThis(language=="en" ? "Filling important inputs are required !":"پر کردن فیلد های مهم مورد نیاز است !");
			}else{
				$.ajax({
					url: 'table/class/table_simple_action.php?createNew',
					type: 'POST',
					data: $haveToSave,
					cache: false,
					contentType: false,
					processData: false,
					xhr: function () {
						var myXhr = $.ajaxSettings.xhr();
						if (myXhr.upload) {
							myXhr.upload.addEventListener('progress', function (e) {
								if (e.lengthComputable) {
									percentageUpdater=Math.round(e.loaded/e.total*100);
								}
							}, false);
						}
						return myXhr;
					},
					complete: function(xhr,status) {
						var data=xhr.responseText;
						// console.log(data);
						if(isJson(data)){
							data=JSON.parse(data);
							if (status == "success" && data['status']=="success") {
								swal.fire({icon:"success",timer: 1000,showConfirmButton: false});
								if(typeof vals !== "undefined" && vals=="back"){
									NEWbackToTable(1);
								}else{
									window.location.hash="tables?name=<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_name, 1)); ?>&action=edit&id="+data['goTo'];
								}
							}else{
								feedbackOperations(data);
							}
						}else{
							feedbackOperations(data);
						}
					}
				});
			}
			$(buttons).html(saveText).attr("disabled",false).children("i").remove();
		}
		function NEWcancelSaveAllThis($message){
			$(".save_all_this, .save_all_this_back").attr("disabled",false).children("i").remove();
			$(".save_all_this").html(language=="en" ? "Save":"ذخیره");
			$(".save_all_this_back").html(language=="en" ? "Save & Back":"ذخیره و بازگشت");
			$message=(typeof $message !== "undefined" ? $message:"");
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
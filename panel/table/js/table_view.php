<?php
	if(isset($connected) && $connected==1 && isset($show_tables) && isset($needed_tables) && count(array_intersect($show_tables, $needed_tables))>=count($needed_tables)){
		if(isset($_SESSION['username'])){
			$res_user=$connection->query("SELECT * FROM ".$sub_name."admins WHERE username='".$_SESSION['username']."' AND act=1");
			$user_stats=$res_user->rowCount();
			if($user_stats==1 || getSetting("op_admin")==$_SESSION['username']){
?>
	<script>
		var selected = [],editor;
		function dataMover($operation,$id){
			$.post("table/class/table_simple_action.php?"+$operation, {
				"id" : $id,
				"table_id" : <?php print_r($table_id); ?>
			}, function(data, status) {
				if(data=="success" && status=="success"){
					$('#datatable').DataTable().ajax.reload(null, false);
				}else if (status != "success" || data != "success") {
					feedbackOperations(data);
				}
			});
		}
		function deleteDataOfTable($id,no_success) {
			try{
				function deleteThisColumn() {
					if(!no_success){
						LoadingScreen("",(language=="en" ? "Deleting ...":"در حال حذف ..."),(language=="en" ? "It's will take few seconds !":"ممکن است چند ثانیه طول بکشد ..."));
					}
					$.post("table/class/table_simple_action.php?deleteThis",{"table_id":<?php print_r($table_id); ?>,"id":$id},function(data,status){
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							$('#datatable').DataTable().ajax.reload(null, false);
							if(!no_success){
								Swal.fire({
									icon: 'success',
									title: (language=="en" ? "Successfully deleted":"با موفقیت حذف شد"),
									timer: 1000,
									showConfirmButton: false
								});
							}
						}else{
							feedbackOperations(data);
						}
					});
				}
				if(alertShowerSetting['deleteThis_showAlert']=="false" && !no_success){
					if(language=="fa"){
						Swal.fire({
							title: 'آیا مطمئن  هستید؟',
							text: "شما میخواهید این موارد حذف شود؟",
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
							text: "Do you want delete this data?",
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
				if(typeof no_success === "undefined"){
					no_success=0;
				}
				deleteDataOfTable($id,no_success);
			}
		}
		function deleteAllDataOfTable() {
			try{
				function deleteThisColumn() {
					LoadingScreen("",(language=="en" ? "Deleting ...":"در حال حذف ..."),(language=="en" ? "It's will take few seconds !":"ممکن است چند ثانیه طول بکشد ..."));
					$.post("table/class/table_simple_action.php?deleteAll",{"table_id":<?php print_r($table_id); ?>},function(data,status){
						if (status == "success" && data.toString().indexOf("success")!=-1) {
							$('#datatable').DataTable().ajax.reload(null, false);
							Swal.fire({
								icon: 'success',
								title: (language=="en" ? "Successfully deleted":"با موفقیت حذف شد"),
								timer: 1000,
								showConfirmButton: false
							});
						}else{
							feedbackOperations(data);
						}
					});
				}
				if(alertShowerSetting['deleteAllThis_showAlert']=="false"){
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
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteAllThis_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
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
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'deleteAllThis_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
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
				alertShowerSetting['deleteAllThis_showAlert']="false";
				deleteAllDataOfTable();
			}
		}
		function copyDataOfTable($id) {
			$.post("table/class/table_simple_action.php?copyThis",{"table_id":<?php print_r($table_id); ?>,"id":$id},function(data,status){
				if (status == "success" && data.toString().indexOf("success")!=-1) {
					data=JSON.parse(data);
					$('#datatable').DataTable().ajax.reload(null, false);
				}else{
					feedbackOperations(data);
				}
			});
		}
		$('#rows_information').on('shown.bs.modal', function () {
			if($(this).hasClass("closing")){
				setTimeout(function(){
					$("#rows_information .modal-content").empty().append('<div class="modal-header justify-content-center"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="tim-icons icon-simple-remove"></i></button><h6 class="title title-up">Loading <i class="fad fa-spin fa-spinner-third"></i></h6></div><div class="modal-body"><p>Loading <i class="fad fa-spin fa-spinner-third"></i></p></div><div class="modal-footer"><button type="button" class="btn btn-danger" data-dismiss="modal">'+(language=="en" ? "Cancel":"لغو")+'</button></div>');
				}, 500);
			}
		});
		function callInformation(ID){
			$("#rows_information .modal-content").empty().append('<div class="modal-header justify-content-center"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="tim-icons icon-simple-remove"></i></button><h6 class="title title-up">Loading <i class="fad fa-spin fa-spinner-third"></i></h6></div><div class="modal-body"><p>Loading <i class="fad fa-spin fa-spinner-third"></i></p></div><div class="modal-footer"><button type="button" class="btn btn-danger" data-dismiss="modal">'+(language=="en" ? "Cancel":"لغو")+'</button></div>').load("tables.php?action=rows_information&name=<?php print_r($_GET['name']); ?>&id="+ID);
			$("#rows_information").modal("show");
		}
		function callDataTable(){
			$('#datatable').dataTable().fnDestroy();
			if(table_order_mode_def=="false"){
				visable_first_column={ "visible":false , "targets": 0 };
			}else{
				visable_first_column="";
			}
			myDataTable = $('#datatable').DataTable({
				"drawCallback": function( settings ) {pscrollbarUpdate();},
				"processing": true,
				"serverSide": true,
				"ajax": {
					url: 'table/datatable_json/data_json.php',
					type: 'POST',
					"data": function ( d ) {
						d.table_name = "<?php print_r($table_name); ?>";
						d.table_col_sql = "<?php print_r($table_col_sql); ?>";
						d.primaryKey = "id";
					},
					// dataFilter: function(data){
					// 	var json = jQuery.parseJSON( data );
					// 	json.recordsTotal = json.total;
					// 	json.recordsFiltered = json.total;
					// 	json.data = json.list;

					// 	return JSON.stringify( json ); // return JSON string
					// }
				},
				"rowCallback": function( row, data, index, a) {
					if( $.inArray(data.DT_RowId, selected) !== -1 ) {
						$(row).addClass('selected');
					}
					$(row).addClass('datatable_data_view');
				},
				rowReorder: {
					update: false,
					snapX: -5,
					selector: 'td.actions_dir a.drag_move_table',
				},
				<?php
					if(isset($_GET["name"]) && $_GET["name"]!="table_config" || !isset($_GET["name"])){
				?>
					select: {
						style: 'multi',
						selector: 'table.dataTable.dtr-inline.collapsed>tbody>tr[role=row]>td:not(:first-child):not(.editing_dt):not(.actions_dir) , table.dataTable.dtr-inline.collapsed>tbody>tr[role=row]>th:not(:first-child):not(.editing_dt):not(.actions_dir) , table.dataTable.dtr-inline:not(.collapsed)>tbody>tr[role=row]>td:not(.editing_dt):not(.actions_dir) , table.dataTable.dtr-inline:not(.collapsed)>tbody>tr[role=row]>th:not(.editing_dt):not(.actions_dir)'
					},
				<?php
					}
				?>
				"autoWidth": false,
				'createdRow': function( row, data, dataIndex ) {
					$(row).attr('data-column-id', $(row).attr("id").replace("<?php print_r($sub_name.$_GET['name']."_"); ?>",""));
				},
				"columnDefs": [
					visable_first_column
					// { "visible":false , "targets": 0 }
					// { "width": "200px", "targets": -1 }
				],
				"columns": [
					{
						"class": "first-child"
					},
					<?php
						$res_table_col=$connection->query($table_col_sql);
						while($table_col=$res_table_col->fetch()){
							if($table_col['visible']==1 || isset($op_admin) && $op_admin){
								if(checkPermission(2,$table_col['id'],"read",$table_col['act'],$table_id)==1){
									if($table_col['current_name']!="ordering"){
					?>
						{
							<?php
								if($table_col['editable']==1 || isset($op_admin) && $op_admin){
									if(checkPermission(2,$table_col['id'],"update",$table_col['act'],$table_id)==1){
							?>
								"class": "db-edit edit_mode_<?php print_r($table_col['mode']); ?>",
							<?php
									}else{
							?>
								"class": "editable no_permission_edit",
							<?php
									}
								}else{
							?>
								"class": "unable_edit",
							<?php
								}
							?>
							// render: function ( data, type, row ) {
							// 	if(data.length>35){
							// 		customText="...";
							// 	}else{
							// 		customText="";
							// 	}
							// 	if(data.search("color-shows")>=0){ //info search case 5 for see all things about this part
							// 		return data;
							// 	}else{
							// 		return "<label class='hide_data hide'>" + data + "</label><label class='show_data'>"+data.substr( 0, 35 )+customText+"</label>";
							// 	}
							// }
						},
					<?php
									}else{
					?>
						{
							"class": "real_id",
						},
					<?php
									}
								}
							}
						}
					?>
					{
						"class": "actions_dir",
						"orderable": false,
						"searchable": false
					}
				],
				// columns: [
				//     { responsivePriority: 4 },
				//     { responsivePriority: 3 },
				//     { responsivePriority: 2 },
				//     { responsivePriority: 1 },
				//     { responsivePriority: 5 }
				// ]
				// "deferLoading": 1, // default value before process
				// "columns": [
						// {
						//     "class":          "details-control",
						//     "orderable":      false,
						//     "data":           null,
						//     "defaultContent": ""
						// },
				//     { "data": "first_name" },
				//     { "data": "last_name" },
				//     { "data": "position" },
				//     { "data": "office" },
				//     { "data": "start_date" },
				//     { "data": "salary" }
				// ],

				"order": default_datatable_order,
				"pagingType": "full_numbers",
				"pageLength": default_datatable_length,
				"lengthMenu": [
					[10, 25, 50, 100, 250 , 500, -1],
					[10, 25, 50, 100, 250 , 500, "All"]
				],
				responsive: {
					details: {
						renderer: function ( api, rowIdx, columns ) {
							colRowIdx=0;
							var data = $.map( columns, function ( col, i ) {
								colRowIdx=col.rowIndex;
								customClass="";
								editables=[];
								<?php
									$res_table_col=$connection->query($table_col_sql);
									while($table_col=$res_table_col->fetch()){
										if($table_col['visible']==1 || isset($op_admin) && $op_admin){
											if(checkPermission(2,$table_col['id'],"read",$table_col['act'],$table_id)==1){
												if($table_col['editable']==1 || isset($op_admin) && $op_admin){
													if(checkPermission(2,$table_col['id'],"update",$table_col['act'],$table_id)==1){
														if($table_col['current_name']!="ordering"){
								?>
									editables.push('<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_col["current_name"], 1)); ?>');
									editables['<?php print_r(preg_replace('/' . preg_quote($sub_name, '/') . '/', "", $table_col["current_name"], 1)); ?>']="edit_mode_<?php print_r($table_col['mode']); ?>";
								<?php
														}
													}
												}
											}
										}
									}
								?>
								if(col.title.indexOf("Actions")==-1){
									for(iUpdatePower=0;iUpdatePower<editables.length;iUpdatePower++){
										if(col.title.indexOf(editables[iUpdatePower])!=-1){
											customClass="db-edit "+editables[editables[iUpdatePower]];
										}
									}
								}else{
									customClass="";
								}
								return col.hidden ?
									'<li class="' + customClass + '" data-dtr-index="'+col.columnIndex+'" data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'" onclick="">'+
										'<span class="dtr-title">'+col.title+':'+'</span>'+
										'<span class="dtr-data">'+col.data+'</span>'+
									'</li>':
									'';
							} ).join('');
							return data ?$('<ul data-dtr-index="'+colRowIdx+'" class="dtr-details">').append( data ) :false;
						}
					}
				},
				"language": langObjs(),
				// "drawCallback": function( settings ) {
					
				// }
				// "initComplete": function(settings, json) {

				// }
			});
			if(default_datatable_search.length){
				myDataTable.search(default_datatable_search).draw();
			}
			setTimeout(function() {
				var myDataTables = $('#datatable').dataTable().api();
				myDataTables.page(default_datatable_page).draw("page");
			}, 1000);
		}
		function reOrderTable(){
			try {
				if(alertShowerSetting['reorderTable_showAlert']=="false"){
					ad_backToTable="";
					if(language=="fa"){
						Swal.fire({
							title: 'برای انجام انتقال نیاز به تغییر در تنظیمات جدول وجود دارد !',
							text: "شما میبایست ترتبیب جدول را بر اساس شماره ترتیب قرار دهید آیا تمایل دارید؟",
							icon: 'warning',
							showCancelButton: true,
							customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
							buttonsStyling: false,
							confirmButtonText: 'بله',
							cancelButtonText: 'لغو',
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'reorderTable_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>همیشه بله .</label></div>'
						}).then((result) => {
							if(result.value) {
								myDataTable.order( [ 0, 'asc' ] ).draw();reloadDataTable();
								Swal.fire({
									position: 'center',
									icon: 'success',
									title: 'تنظیمات برای فعال سازی ابزار جا به جایی اعمال شد !',
									showConfirmButton: false,
									timer: 1500,
									allowOutsideClick: false
								});
							}
						});
					}else{
						Swal.fire({
							title: 'For doing this you need to change setting in table !',
							text: "This table is not ordered by order id, do you like to do this?",
							icon: 'warning',
							showCancelButton: true,
							customClass: {
								confirmButton: 'btn btn-success',
								cancelButton: 'btn btn-danger'
							},
							buttonsStyling: false,
							confirmButtonText: 'Yes',
							footer: '<div class="form-check"><label class="form-check-label"><input onchange="dontShowAlert('+"'reorderTable_showAlert'"+',this.checked)" class="form-check-input" type="checkbox"><span class="form-check-sign"></span>Always yes .</label></div>'
						}).then((result) => {
							if(result.value) {
								myDataTable.order( [ 0, 'asc' ] ).draw();reloadDataTable();
								Swal.fire({
									position: 'center',
									icon: 'success',
									title: "Settings applied for move tool !",
									showConfirmButton: false,
									timer: 1500,
									allowOutsideClick: false
								});
							}
						});
					}
				}else{
					myDataTable.order( [ 0, 'asc' ] ).draw();reloadDataTable();
					if(language=="en"){
						Swal.fire({
							position: 'center',
							icon: 'success',
							title: "Settings applied for move tool !",
							showConfirmButton: false,
							timer: 1500,
							allowOutsideClick: false
						});
					}else if(language=="fa"){
						Swal.fire({
							position: 'center',
							icon: 'success',
							title: 'تنظیمات برای فعال سازی ابزار جا به جایی اعمال شد !',
							showConfirmButton: false,
							timer: 1500,
							allowOutsideClick: false
						});
					}
				}
			} catch(err) {
				alertShowerSetting['reorderTable_showAlert']="false";
				reOrderTable();
			}
		}
		$(document).ready(function() {
			$(document).on("click",'.sorting, .sorting_asc, .sorting_desc',function(){
				setTimeout(function(){
					default_datatable_order=[[myDataTable.order()[0][0], myDataTable.order()[0][1]]];
				}, 0);
			});
			$(document).on("change",'#datatable_length',function(){
				setTimeout(function(){
					default_datatable_length=$("#datatable").DataTable().page.len();
					default_datatable_page=0;
				}, 0);
			});
			$(document).on("keyup",'#datatable_filter input[type=search]',function(){
				setTimeout(function(){
					default_datatable_search=$("#datatable_filter input[type=search]").val();
				}, 0);
			});
			$(document).on("click",'.paginate_button',function(){
				setTimeout(function(){
					default_datatable_page=myDataTable.page.info().page;
				}, 0);
			});
			$('#datatable tbody').on( 'click', 'tr td:first-child', function () {
				if($(this).hasClass("editing_dt")){
					var row = myDataTable.row($(this).closest('tr'));
					setTimeout(function(){
						row.child.hide();
					}, 0);
				}
			} );
			$(document).on("focus",".dt_editor",function(){
				trElement=$("tbody tr:not(.parent) td.db-edit:not(.editing_dt):not(:first-child)").parent(":not(.editing_tr)");
				if(trElement.prop("tagName")=="TR"){
					divElement=$(this).parent();
					tdElement=$(this).parent().parent();
					trWidth=(trElement.width()-15);
					secElm=$(this).parent().parent().parent().parent();
					inputWidth=trWidth-90;
					trHeight=(trElement.height());
					trHeight2=(trHeight/2);
					trPaddingTB=(trHeight2/2);
					if($("#" + secElm.attr("id")).parent().parent().parent().parent().parent().parent().length!=0){
						trLeftFixer=$("#" + secElm.attr("id")).parent().parent().parent().parent().parent().parent().offset().left;
						trLeft=((tdElement.offset().left-30)-trLeftFixer);
					}
					$(this).css({"width" : "100%"});
					tdElement.addClass("no-button no-space");
					divElement.css({"width" : "calc(100% - 90px)"});
				}
			});

			$(document).on("click", ".editing_dt", function() {
				if($(this).hasClass("first-child")==true && $(this).hasClass("clicked-child")!=true){
					$(this).addClass("clicked-child").click();
					setTimeout(function() {
						$(this).removeClass("clicked-child");
					}, 100);
				}
			});

			$.fn.dataTable.ext.errMode = 'none';
			$('#datatable').on( 'error.dt', function ( e, settings, techNote, message ) {
				console.log(e);
				console.log(settings);
				console.log(techNote);
				console.log(message);
				console.log( 'An error has been reported by DataTables: ', message );
				// window.location.reload();
			}).DataTable();

			callDataTable();

			myDataTable.on( 'row-reorder', function ( e, diff, edit ) {
				if(myDataTable.order()[0][0]==0 && myDataTable.order()[0][1]=="asc"){
					// console.log(edit.triggerRow.data());
					// var result = 'Reorder started on row: '+edit.triggerRow.data()[1]+'<br>';

					// for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
					// 	var rowData = myDataTable.row( diff[i].node ).data();

					// 	result += rowData[1]+' updated to be in position '+
					// 		diff[i].newData+' (was '+diff[i].oldData+')<br>';
					// }

					// console.log( 'Event result:<br>'+result );
					var $changer="";
					for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
						if(i==ien-1){
							$explode_helper="";
						}else{
							$explode_helper="_.._";
						}
						$changer+=diff[i].oldData+"_._"+diff[i].newData+$explode_helper;
					}
					if($changer!=""){
						$.post("table/class/action.php?order_numbers", {
							"numbers" : $changer,
							"table_id" : <?php print_r($table_id); ?>
						}, function(data, status) {
							if (status == "success" && data.toString().indexOf("success")!=-1) {
								$('#datatable').DataTable().ajax.reload(null, false);
							}else{
								feedbackOperations(data);
							}
						});
					}
				}else{
					// myDataTable.order( [ 0, 'asc' ] ).draw();
					// reloadDataTable();
					// if(language=='en'){
					//     Swal.fire({
					//         position: 'center',
					//         icon: 'info',
					//         title: "Please try again in second later.",
					//         showConfirmButton: false,
					//         timer: 3500,
					//         allowOutsideClick: false
					//     });
					// }else if(language=='fa'){
					//     Swal.fire({
					//         position: 'center',
					//         icon: 'info',
					//         title: 'این عملیات انجام نمیشود زیرا این جدول با شماره ترتیب ، مرتب نشده است !',
					//         showConfirmButton: false,
					//         timer: 3500,
					//         allowOutsideClick: false
					//     });
					// }
				}
			} );
			$.fn.dataTable.ext.errMode = function ( settings, helpPage, message ) {
				//window.location="";
			};
			function removeQuickEdit(){
				var NewEdits=$(".dt_editors");
				for(iNewEdits=0;iNewEdits<NewEdits.length;iNewEdits++){
					$(NewEdits[iNewEdits]).parent().children(".show_data").removeClass("hide").parent().children(".dt_editors").remove();
				}
				$(".input_dt_editor").removeClass("input_dt_editor");
				$(".editing_tr").removeClass("editing_tr");
				$(".editing_dt").removeClass("editing_dt");
				$(".no-space").removeClass("no-space");
			}
			function getNewEdit(firstVal,CustomWidth,modes){
				removeQuickEdit();
				if(modes!=undefined && modes!=null && modes!=""){
					modes=parseInt(modes);
					<?php //info search this_is_modes_for_data_tables for see all things about this part ?>
					switch (modes) {
						case 1://info search case 1 for see all things about this part
							return "<div class='dt_editors' style='display: inline-block;'><input style='width:" + CustomWidth + ";' type='text' value='" + firstVal + "' class='dt_editor input_dt_editor'>" + '<a href="javascript:void(0)" class="save_quick_edit quick_operation btn btn-link btn-success btn-icon btn-sm"><i class="tim-icons icon-check-2"></i></a><a href="javascript:void(0)" class="reset_quick_edit quick_operation btn btn-link btn-warning btn-icon btn-sm"><i class="tim-icons icon-refresh-01"></i></a><a href="javascript:void(0)" class="cancel_quick_edit quick_operation btn btn-link btn-danger btn-icon btn-sm"><i class="tim-icons icon-simple-remove"></i></a></div>';
						break;
						case 2://info search case 2 for see all things about this part
							return "<div class='dt_editors' style='display: inline-block;'><input style='width:" + CustomWidth + ";' type='number' value='" + firstVal + "' class='dt_editor input_dt_editor'>" + '<a href="javascript:void(0)" class="save_quick_edit quick_operation btn btn-link btn-success btn-icon btn-sm"><i class="tim-icons icon-check-2"></i></a><a href="javascript:void(0)" class="reset_quick_edit quick_operation btn btn-link btn-warning btn-icon btn-sm"><i class="tim-icons icon-refresh-01"></i></a><a href="javascript:void(0)" class="cancel_quick_edit quick_operation btn btn-link btn-danger btn-icon btn-sm"><i class="tim-icons icon-simple-remove"></i></a></div>';
						break;
						case 3://info search case 3 for see all things about this part
							// return "<div class='dt_editors' style='display: inline-block;'><input type='checkbox' name='checkbox' class='bootstrap-switch' onchange='' data-on-label='<i class="+'"'+"far fa-link"+'"'+"></i>' data-off-label='<i class="+'"'+"far fa-unlink"+'"'+"></i>'>" + '<a href="javascript:void(0)" class="save_quick_edit quick_operation btn btn-link btn-success btn-icon btn-sm"><i class="tim-icons icon-check-2"></i></a><a href="javascript:void(0)" class="reset_quick_edit quick_operation btn btn-link btn-warning btn-icon btn-sm"><i class="tim-icons icon-refresh-01"></i></a><a href="javascript:void(0)" class="cancel_quick_edit quick_operation btn btn-link btn-danger btn-icon btn-sm"><i class="tim-icons icon-simple-remove"></i></a></div>';
						break;
						case 5://info search case 5 for see all things about this part
							// return "<div class='dt_editors' style='display: inline-block;'><input style='width:" + CustomWidth + ";' type='text' value='" + firstVal + "' class='dt_editor input_dt_editor colorpicker'>" + '<a href="javascript:void(0)" class="save_quick_edit quick_operation btn btn-link btn-success btn-icon btn-sm"><i class="tim-icons icon-check-2"></i></a><a href="javascript:void(0)" class="reset_quick_edit quick_operation btn btn-link btn-warning btn-icon btn-sm"><i class="tim-icons icon-refresh-01"></i></a><a href="javascript:void(0)" class="cancel_quick_edit quick_operation btn btn-link btn-danger btn-icon btn-sm"><i class="tim-icons icon-simple-remove"></i></a></div><script>'+"$('.colorpicker').spectrum({locale: language,type: 'component',showPaletteOnly: 'true',togglePaletteOnly: 'true',hideAfterPaletteSelect: 'true',showInput: 'true',showInitial: 'true',allowEmpty:'true'});"+'<'+'/'+'script>';
						break;
					}
				}
			}
			$('#datatable tbody').on('click', '.save_quick_edit', function (e) {
				dataMemory=$(this).parent().parent().children(".hide_data");
				dataShow=$(this).parent().parent().children(".show_data");
				quickInput=$(this).parent().children(".input_dt_editor");
				if(quickInput.hasClass("input_dt_editor")==true){
					dataMemory.html(quickInput.val());
					if(quickInput.val().length>50){
						customTextInput="...";
					}else{
						customTextInput="";
					}
					dataShow.html(quickInput.val().substr( 0, 50 )+customTextInput);
				}
				removeQuickEdit();
				reloadDataTable();
			});
			$('#datatable tbody').on('click', '.reset_quick_edit', function (e) {
				dataMemory=$(this).parent().parent().children(".hide_data");
				quickInput=$(this).parent().children(".input_dt_editor");
				if(quickInput.hasClass("input_dt_editor")==true){
					quickInput.val("").val(dataMemory.html()).focus();
				}
			});
			$('#datatable tbody').on('click', '.cancel_quick_edit', function (e) {
				removeQuickEdit();
			});

			$(document).on('keydown', function(event) {
				if (event.key == "Escape") {
					var $editors=$(".editing_dt");
					if($editors.length!=0){
						$(".cancel_quick_edit").click();
					}
				}
			});
			$(document).on('keydown', function(event) {
				if (event.key == "Enter") {
					var $editors=$(".editing_dt");
					if($editors.length!=0){
						$(".save_quick_edit").click();
					}
				}
			});

			<?php
				if(isset($_GET["name"]) && $_GET["name"]!="table_config" || !isset($_GET["name"])){
			?>
				// $('#datatable tbody').on('dblclick', 'tr', function (e) {
				// 	var mouseClicked=$(e.target),edit_mode="";
				// 	if(mouseClicked.hasClass("db-edit")){
				// 		classes=mouseClicked.attr("class").split(" ");
				// 	}else if(mouseClicked.parent().hasClass("db-edit")){
				// 		classes=mouseClicked.parent().attr("class").split(" ");
				// 	}else if(mouseClicked.parent().parent().hasClass("db-edit")){
				// 		classes=mouseClicked.parent().parent().attr("class").split(" ");
				// 	}else{
				// 		classes=[];
				// 	}
				// 	for(iClass=0;iClass<classes.length;iClass++){
				// 		if(classes[iClass].indexOf("edit_mode_")!=-1){
				// 			edit_mode=classes[iClass].split("edit_mode_")[1];
				// 		}
				// 	}
				// 	if(edit_mode!=""){
				// 		if($(this).hasClass("child")){
				// 			mouseClickedTagName=$($(e.target)[0]).prop("tagName");
				// 			if(mouseClickedTagName=="LABEL" && mouseClickedTagName!="INPUT" || mouseClickedTagName=="LI" && mouseClickedTagName!="INPUT" || mouseClickedTagName=="SPAN" && mouseClickedTagName!="INPUT"){
				// 				var firstVal=0,$mouseClickedHandler;
				// 				switch(mouseClickedTagName){
				// 					case "LI":
				// 						$mouseClickedHandler=mouseClicked;
				// 					break;
				// 					case "SPAN":
				// 						$mouseClickedHandler=mouseClicked.parent();
				// 					break;
				// 					case "LABEL":
				// 						$mouseClickedHandler=mouseClicked.parent().parent();
				// 					break;
				// 				}
				// 				if($mouseClickedHandler.hasClass("db-edit")==true){
				// 					firstVal=$mouseClickedHandler.children("span.dtr-data").children(".hide_data").html();
				// 					if(firstVal!="undefined" && firstVal!="null" && firstVal!=undefined && firstVal!=null){
				// 						if($mouseClickedHandler.children("span.dtr-data").hasClass("editing_dt")!=true){
				// 							customCss=($mouseClickedHandler.width()-($mouseClickedHandler.children("span.dtr-title").width()+90))+"px";
				// 							$mouseClickedHandler.children("span.dtr-data").children(".show_data").addClass("hide").parent().append(getNewEdit(firstVal,customCss,edit_mode)).addClass("editing_dt").children(".input_dt_editor");
				// 							bsSwitcher();
				// 							$(this).addClass("editing_tr");
				// 							$(".reset_quick_edit").click()
				// 							$(".input_dt_editor").focus();
				// 						}
				// 					}
				// 				}
				// 			}
				// 		}else{
				// 			if(mouseClicked.hasClass("editing_dt")!=true && mouseClicked.hasClass("db-edit")==true && $($(e.target)[0]).prop("tagName")!="INPUT" || mouseClicked.parent().hasClass("editing_dt")!=true && mouseClicked.parent().hasClass("db-edit")==true && $($(e.target)[0]).prop("tagName")!="INPUT"){
				// 				var firstVal=null;
				// 				customCss="calc(100% - 90px)";
				// 				switch($($(e.target)[0]).prop("tagName")){
				// 					case "LABEL":
				// 						firstVal=$(mouseClicked[0]).parent().children(".hide_data").html();
				// 						if(typeof firstVal!=="undefined"){
				// 							mouseClicked.parent().children(".show_data").addClass("hide").parent().append(getNewEdit(firstVal,customCss,edit_mode)).addClass("editing_dt").children(".input_dt_editor");
				// 						}
				// 					break;
				// 					case "TD":
				// 						firstVal=$(mouseClicked[0]).children(".hide_data").html();
				// 						if(firstVal!="undefined" && firstVal!="null" && firstVal!=undefined && firstVal!=null){
				// 							mouseClicked.children(".show_data").addClass("hide").parent().append(getNewEdit(firstVal,customCss,edit_mode)).addClass("editing_dt").children(".input_dt_editor");
				// 						}
				// 					break;
				// 				}
				// 				if(firstVal!=null){
				// 					bsSwitcher();
				// 					$(this).addClass("editing_tr");
				// 					$(".reset_quick_edit").click();
				// 					$(".input_dt_editor").focus();
				// 				}
				// 			}
				// 		}
				// 	}
				// });
			<?php
				}
			?>
		});
		function reverseSelectedRowsDataTable(datatable_data_view){
			$(".datatable_data_view.selected").each(function (){
				$(this).addClass("dont-select-now");
				myDataTable.rows('.datatable_data_view.selected').deselect();
			});
			$(".datatable_data_view:not(.selected):not(.dont-select-now)").each(function (){
				myDataTable.rows('.datatable_data_view:not(.selected):not(.dont-select-now)').select();
			});
			$(".dont-select-now").removeClass("dont-select-now")
		}
		function selectAllRowsDataTable(datatable_data_view){
			myDataTable.rows('.datatable_data_view').select();
		}
		function deselectAllRowsDataTable(datatable_data_view){
			myDataTable.rows('.datatable_data_view').deselect();
		}
		function editSelectedDataOfTable($do=0) {
			var $selected_columns_of_row=0;
			$(".datatable_data_view.selected").each(function (){
				if($selected_columns_of_row){
					$selected_columns_of_row+="_"+$(this).attr("data-column-id");
				}else{
					$selected_columns_of_row=$(this).attr("data-column-id");
				}
			});
			if($selected_columns_of_row){
				if($do){
					window.location.hash="tables?name=<?php print_r($_GET['name']); ?>&action=edit&id="+$selected_columns_of_row;
				}else{
					$("#rows_information .modal-content").empty().append('<div class="modal-header justify-content-center"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="tim-icons icon-simple-remove"></i></button><h6 class="title title-up">Loading <i class="fad fa-spin fa-spinner-third"></i></h6></div><div class="modal-body"><p>Loading <i class="fad fa-spin fa-spinner-third"></i></p></div><div class="modal-footer"><button type="button" class="btn btn-danger" data-dismiss="modal">'+(language=="en" ? "Cancel":"لغو")+'</button></div>').load("tables.php?action=selected_columns&name=<?php print_r($_GET['name']); ?>&operation_name="+(language=="en" ? "Edit":"ویرایش")+"&id="+$selected_columns_of_row+"&operation_value=editSelectedDataOfTable(1);");
					$("#rows_information").modal("show");
				}
			}else{
				Swal.fire({
					icon: 'info',
					title: (language=="en" ? "Nothing selected":"موردی انتخاب نشده"),
					timer: 1000,
					showConfirmButton: false
				});
			}
		}
		function copySelectedDataOfTable($do=0) {
			var $selected_columns_of_row=0;
			$(".datatable_data_view.selected").each(function (){
				if($selected_columns_of_row){
					$selected_columns_of_row+="_"+$(this).attr("data-column-id");
				}else{
					$selected_columns_of_row=$(this).attr("data-column-id");
				}
			});
			if($selected_columns_of_row){
				if($do){
					$(".datatable_data_view.selected").each(function (){
						copyDataOfTable($(this).attr("data-column-id"));
					});
				}else{
					$("#rows_information .modal-content").empty().append('<div class="modal-header justify-content-center"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="tim-icons icon-simple-remove"></i></button><h6 class="title title-up">Loading <i class="fad fa-spin fa-spinner-third"></i></h6></div><div class="modal-body"><p>Loading <i class="fad fa-spin fa-spinner-third"></i></p></div><div class="modal-footer"><button type="button" class="btn btn-danger" data-dismiss="modal">'+(language=="en" ? "Cancel":"لغو")+'</button></div>').load("tables.php?action=selected_columns&name=<?php print_r($_GET['name']); ?>&operation_name="+(language=="en" ? "Copy":"کپی")+"&id="+$selected_columns_of_row+"&operation_value=copySelectedDataOfTable(1);");
					$("#rows_information").modal("show");
				}
			}else{
				Swal.fire({
					icon: 'info',
					title: (language=="en" ? "Nothing selected":"موردی انتخاب نشده"),
					timer: 1000,
					showConfirmButton: false
				});
			}
		}
		function deleteSelectedDataOfTable($do=0) {
			var $selected_columns_of_row=0;
			$(".datatable_data_view.selected").each(function (){
				if($selected_columns_of_row){
					$selected_columns_of_row+="_"+$(this).attr("data-column-id");
				}else{
					$selected_columns_of_row=$(this).attr("data-column-id");
				}
			});
			if($selected_columns_of_row){
				if($do){
					$selected_columns_of_row.split("_").forEach(function(item, index){
						deleteDataOfTable(item,1);
					});
					// $(".datatable_data_view.selected").each(function (){
					// 	deleteDataOfTable($(this).attr("data-column-id"),1);
					// });
				}else{
					$("#rows_information .modal-content").empty().append('<div class="modal-header justify-content-center"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="tim-icons icon-simple-remove"></i></button><h6 class="title title-up">Loading <i class="fad fa-spin fa-spinner-third"></i></h6></div><div class="modal-body"><p>Loading <i class="fad fa-spin fa-spinner-third"></i></p></div><div class="modal-footer"><button type="button" class="btn btn-danger" data-dismiss="modal">'+(language=="en" ? "Cancel":"لغو")+'</button></div>').load("tables.php?action=selected_columns&name=<?php print_r($_GET['name']); ?>&operation_name="+(language=="en" ? "Delete":"خذف")+"&id="+$selected_columns_of_row+"&operation_value=deleteSelectedDataOfTable(1);");
					$("#rows_information").modal("show");
				}
			}else{
				Swal.fire({
					icon: 'info',
					title: (language=="en" ? "Nothing selected":"موردی انتخاب نشده"),
					timer: 1000,
					showConfirmButton: false
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
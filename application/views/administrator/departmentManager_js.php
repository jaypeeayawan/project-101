<script type="text/javascript">
	$(document).ready(function(){

		var dataTable = $('table#datatable-department').DataTable({
			"processing" : true,
			"serverSide" : true,
			"ajax" : {
				"url" : "<?php echo base_url().''.$getController; ?>/departmentJson/",
				"type" : "POST",
				"dataType": "json"
			},
			"columns" : [
				{ "data" : "department_code" },
				{ "data" : "department_title" },
	            { "data" : null,
	                "render" : function (row) {
	                    return '<a href="javascript:;" id="'+row.department_id+'_'+row.department_code+'_'+row.department_title+'" class="btn btn-info btn-xs updateBtn"><i class="fa fa-pencil"></i> Edit</a><a href="javascript:;" id="'+row.department_id+'_'+row.department_code+'_'+row.department_title+'" class="btn btn-danger btn-xs deleteBtn"><i class="fa fa-times"></i> Delete</a>';
	                }
	            },				
			],
			"columnDefs" : [
	            { "searchable": false, "targets": [2]},
	            { "orderable": false, "targets": [2]}
			],
          	//pageLength : 50,
          	"order": [[1, "asc"]]			
		});

		//update	
		$(document).on('click','a.updateBtn', function(e){
			var ids = new String($(this).attr('id'));
			ids = ids.split('_');

			var department_id = ids[0];
			var department_code = ids[1];
			var department_title = ids[2];

			$('#update-modal').modal({ show: 'show', backdrop: 'static' });
			$('#dept_code').html($('<input type="text" id="dept-code" class="form-control" value="'+department_code+'" data-parsley-required-message="This field is required" required="required">'));
			$('#dept_title').html($('<input type="text" id="dept-title" class="form-control" value="'+department_title+'" data-parsley-required-message="This field is required" required="required">'));

			$('#dept-update-form .myFormBtnSubmit').on('click', function(e){
				var form = $('#dept-update-form');
				form.parsley().validate();
				if(form.parsley().isValid()){
					var url = '<?php echo base_url().''.$getController; ?>/updateDepartmentManager/'+department_id+'/';
					var postData = new Array();
					postData.push($('#dept_code #dept-code').val());
					postData.push($('#dept_title #dept-title').val());
					$.post(url,{ postData:postData }, function(data){
						$('#update-modal').modal('hide'); // start hiding
							window.location.reload();
					});				
				}
				e.preventDefault();
				e.stopPropagation();
			});
			e.preventDefault();
			e.stopPropagation();	
		});	

		//delete
		$(document).on('click','a.deleteBtn', function(e){
			var ids = new String($(this).attr('id'));
			ids = ids.split('_');

			var department_id = ids[0];
			var department_code = ids[1];
			var department_title = ids[2];

			$('#delete-modal').modal({ show: 'show', backdrop: 'static' });
			$('#dept').html($('<p style="color: #800000; text-indent: 1em;">'+department_code+' - '+department_title+'</p>'));		

			$('#delete-dept').on('click', function() {
				var url = '<?php echo base_url().''.$getController; ?>/deleteDepartmentManager/'+department_id+'/';
				$.ajax({
					type: "POST",
					url: url,
					data: $(this).serialize(),
					success: function() {
						window.location.reload();
					},
					error: function(){
						window.location.reload();
					}
				});
			});
			e.preventDefault();
			e.stopPropagation();			
		});

		//create
		$('#dept-create-form .myFormBtnSubmit').on('click', function(e){
			var form = $('#dept-create-form');
			form.parsley().validate();
			if(form.parsley().isValid()){
				var url = '<?php echo base_url().''.$getController; ?>/createDepartmentManager/';
				var postData = new Array();
				postData.push($('#dept-code').val());
				postData.push($('#dept-title').val());
				$.ajax({
					type: "POST",
					url: url,
					data: { postData:postData }
				}).done(function(obj){
					if(obj == 1){
						window.location.reload();
					}else{
						window.location.reload();
					}
				});
			}
			e.preventDefault();
			e.stopPropagation();
		});

		$('#dept-create-form .myFormBtnCancel').on('click', function(){
			var form = $('#dept-create-form');
			form.parsley().reset();
			form.trigger('reset');
		});

	});
</script>
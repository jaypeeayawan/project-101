<script type="text/javascript">
	$(document).ready(function(){

		function pageReload(){
			return window.location.reload();
		}
		//update	
		$('.updateBtn').on('click', function(e){
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
						pageReload();
					});				
				}
				e.preventDefault();
				e.stopPropagation();
			});
			e.preventDefault();
			e.stopPropagation();	
		});	

		//delete
		$('.deleteBtn').on('click', function(e){
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
						$('#delete-modal').modal('hide');
						new PNotify({
							title: 'Request Information',
							text: 'Deleted department successfully!',
							type: 'success',
							styling: 'bootstrap3',
							remove: true,
							delay: 6000
						});
						setTimeout(function(){
							pageReload();
						}, 6000);
					},
					error: function(){
						$('#delete-modal').modal('hide');
						new PNotify({
							title: 'Request Information',
							text: 'Unable to process request, some file is using this record!',
							type: 'error',
							styling: 'bootstrap3',
							remove: true,
							delay: 6000
						});
						setTimeout(function(){
							pageReload();
						}, 6000);
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
				$.post(url,{ postData:postData }, function(data){
					pageReload();
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
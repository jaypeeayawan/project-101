<script type="text/javascript">
	$(document).ready(function(){

		//create
		$('#emp-create-form .myFormBtnSubmit').on('click', function(){
			var form = $('#emp-create-form');
			form.parsley().validate();
			if(form.parsley().isValid()){
				var url = '<?php echo base_url().''.$getController; ?>/createEmployeeManager/';
				var postData = new Array();
				postData.push($('#idNumber').val());
				postData.push($('#lname').val());
				postData.push($('#fname').val());
				postData.push($('#mname').val());
				postData.push($('#ename').val());
				postData.push($('#etitle').val());
				postData.push($('#edepartment').val());
				$.post(url, { postData:postData }, function(data){
					window.location.reload();
				});
			}
		});

		$('#emp-create-form .myFormBtnCancel').on('click', function(){
			var form = $('#emp-create-form');
			form.parsley().reset();
			form.trigger('reset');
		});

		// update
		$('.updateBtn').on('click', function(e){

			e.preventDefault();
			e.stopPropagation();

			var ids = new String($(this).attr('id'));
			ids = ids.split('_');
			
			var personId = ids[0];
			var idNumber = ids[1];
			var lastName = ids[2];
			var firstName = ids[3];
			var middleInitial = ids[4];
			var extName = ids[5];
			var title = ids[6];
			var deptCode = ids[7];
			var deptId = ids[8];

			// alert(personId);

			$('#update-modal').modal({ show: 'show', backdrop: 'static'});

			$('#idnumber').html($('<input type="text" id="id-number" class="form-control" value="'+idNumber+'" data-parsley-required-message="This field is required" required="required">'));
			$('#lastname').html($('<input type="text" id="last-name" class="form-control" value="'+lastName+'" data-parsley-required-message="This field is required" required="required">'));
			$('#firstname').html($('<input type="text" id="first-name" class="form-control" value="'+firstName+'" data-parsley-required-message="This field is required" required="required">'));
			$('#middleinitial').html($('<input type="text" id="middle-initial" class="form-control" value="'+middleInitial+'" data-parsley-required-message="This field is required" required="required">'));
			$('#extname').html($('<input type="text" id="ext-name" class="form-control" value="'+extName+'">'));
			$('#emptitle').html($('<input type="text" id="emp-title" class="form-control" value="'+title+'" data-parsley-required-message="This field is required" required="required">'));
			$('select#edepartment').prepend('<option value="'+deptId+'" selected="selected">'+deptCode+'</option>');

			$('#employee-update-form .myFormBtnSubmit').on('click', function(){
				
				var form = $('#employee-update-form');
				form.parsley().validate();

				if(form.parsley().isValid()){
					var postData = new Array();
					postData.push($('#id-number').val());
					postData.push($('#last-name').val());
					postData.push($('#first-name').val());
					postData.push($('#middle-initial').val());
					postData.push($('#ext-name').val());
					postData.push($('#emp-title').val());
					postData.push($('select.emp_dept').val());

					var url = '<?php echo base_url().''.$getController ?>/updateEmployeeManager/'+personId;
					$.post(url, {postData : postData}, function(data){
						window.location.reload();
					});	
				}
			});
		});


		// datatable employe
		$('table#datatable-emp').DataTable({
	    	pageLength : 25,
	    	"columnDefs" : [
		        { "searchable": false, "targets": [4,5,6]},
		        { "orderable": false, "targets": [4,5,6]}
			],
		  	"order": [[ 0, "asc" ]],
		  	responsive: false
		});		
	});
</script>
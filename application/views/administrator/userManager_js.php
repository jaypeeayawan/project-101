<script type="text/javascript">
	$(document).ready(function(){

		$('.existing').hide(); // hide existing row
		$('#option1').attr('checked','checked'); // set option 1 as the defauls selected row

		$('#option2').on('click',function(){
			$('.new').hide(); // .new row
			$('.existing').fadeIn(); // .existing row
		});

		$('#option1').on('click',function(){
			$('.existing').hide(); // .existing row
			$('.new').fadeIn(); // .new row
		});

		//create existing
		$('#user-create-form .myFormBtnSubmit').on('click', function(){
			var form = $('#user-create-form');
			form.parsley().validate();
			if(form.parsley().isValid()){
				var url = '<?php echo base_url().''.$getController; ?>/createUserManager/';

				var values = $('#multiple-account').val();
				values = values.split('_');
				var personId = values[0];
				var idNumber = values[1];

				var postData = new Array();
				postData.push(personId);
				postData.push(idNumber);

				$.post(url, { postData:postData }, function(data){
					window.location.reload();
				});
			}
		});

		$('#user-create-form .myFormBtnCancel').on('click', function(){
			var form = $('#user-create-form');
			form.parsley().reset();
			form.trigger('reset');
		});

		//create new
		$('#user-new-create-form .myFormBtnSubmit').on('click', function(){
			var form = $('#user-new-create-form');
			form.parsley().validate();
			if(form.parsley().isValid()){
				var url = '<?php echo base_url().''.$getController; ?>/createNewUserManager/';
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

		$('#user-new-create-form .myFormBtnCancel').on('click', function(){
			var form = $('#user-new-create-form');
			form.parsley().reset();
			form.trigger('reset');
		});

		$('.deactivateBtn').on('click', function(){
			var userId = new String($(this).attr('id'));

			$('#deactivate-modal').modal({ show: 'show', backdrop: 'static' });
			$('#deactivate-user').on('click', function(){
				var url = '<?php echo base_url().''.$getController; ?>/deactivateUserManager/'+userId;
				$.post(url, {}, function(){
					window.location.reload();
				});
			});
		});

		$('.activateBtn').on('click', function(){
			var userId = new String($(this).attr('id'));

			$('#activate-modal').modal({ show: 'show', backdrop: 'static' });
			$('#activate-user').on('click', function(){
				var url = '<?php echo base_url().''.$getController; ?>/activateUserManager/'+userId;
				$.post(url, {}, function(){
					window.location.reload();
				});
			});
		});

		$('table#datatable-user').DataTable({
			"columnDefs" : [
        { "searchable": false, "targets": [4]},
        { "orderable": false, "targets": [4]}
			],
      	//pageLength : 50,
      	"order": [[ 0, "asc" ]],
      	responsive: false
		});

	});
</script>

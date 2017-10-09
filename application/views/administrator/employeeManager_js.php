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
	});
</script>
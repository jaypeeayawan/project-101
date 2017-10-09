<script type="text/javascript">

	$(document).ready(function(){
		
		$('#change-pass-form .myFormBtnSubmit').on('click', function(e){		
			
			e.preventDefault();
			e.stopPropagation();
			
			var form = $('#change-pass-form');
			form.parsley().validate();
			
			if(form.parsley().isValid()){
				var url = '<?php echo base_url().''.$getController; ?>/changePasswordManager/';
				var postData = new Array();
				postData.push($('#current-password').val());
				postData.push($('#new-password').val());
				postData.push($('#confirm-new-password').val());
				
				$.ajax({
					type: 'POST',
					url: url,
					data: { postData },
					success: function(result){
						window.location.href = '<?php echo base_url().''.$getController; ?>/logout/';
					},
					error: function(xhr, ajaxOptions, thrownError){
						
					}
				});
			}
		});
		
		$('#change-pass-form .myFormBtnClear').on('click', function(){
			var form = $('#change-pass-form');
			form.parsley().reset();
			form.trigger('reset');
		});
		
	});

</script>
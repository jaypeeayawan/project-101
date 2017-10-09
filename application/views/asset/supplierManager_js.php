<script type="text/javascript">
	$(document).ready(function(){

		//update	
		$('.updateBtn').on('click', function(e){
			var ids = new String($(this).attr('id'));
			ids = ids.split('_');

			var supplier_id = ids[0];
			var supplier_name = ids[1];
			var supplier_address = ids[2];
			var supplier_email = ids[3];
			var supplier_contact = ids[4];

			$('#update-modal').modal({ show: 'show', backdrop: 'static' });
			$('#sup_name').html($('<input type="text" id="sup-name" class="form-control" value="'+supplier_name+'" data-parsley-required-message="This field is required" required="required">'));
			$('#sup_address').html($('<input type="text" id="sup-address" class="form-control" value="'+supplier_address+'" data-parsley-required-message="This field is required" required="required">'));
			$('#sup_email').html($('<input type="text" id="sup-email" class="form-control" value="'+supplier_email+'" data-parsley-type="email" data-parsley-type="email" data-parsley-required-message="This field is required" required="required">'));
			$('#sup_contact').html($('<input type="text" id="sup-contact" class="form-control" value="'+supplier_contact+'" data-parsley-type="digits" data-parsley-required-message="This field is required" required="required">'));
			
			$('#sup-update-form .myFormBtnSubmit').on('click', function(e){
				var form = $('#sup-update-form');
				form.parsley().validate();
				if(form.parsley().isValid()){
					var url = '<?php echo base_url().''.$getController; ?>/updateSupplierManager/'+supplier_id+'/';
					var postData = new Array();
					postData.push($('#sup_name #sup-name').val());
					postData.push($('#sup_address #sup-address').val());
					postData.push($('#sup_email #sup-email').val());
					postData.push($('#sup_contact #sup-contact').val());
					$.post(url,{ postData:postData }, function(data){
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
		$('.deleteBtn').on('click', function(){
			var ids = new String($(this).attr('id'));
			ids = ids.split('_');

			var supplier_id = ids[0];
			var supplier_name = ids[1];
			var supplier_address = ids[2];
			var supplier_contact = ids[3];
			var supplier_email = ids[4];

			$('#delete-modal').modal({ show: 'show', backdrop: 'static' });
			$('#sup').html($('<p style="color: #800000; text-indent: 1em;">'+supplier_name+' - '+supplier_address+'</p>'));		

			$('#delete-sup').on('click', function() {
				var url = '<?php echo base_url().''.$getController; ?>/deleteSupplierManager/'+supplier_id+'/';
				$.post(url, {}, function(){
					window.location.reload();
				});
			});
			e.preventDefault();
			e.stopPropagation();			
		});

		//create
		$('#sup-create-form .myFormBtnSubmit').on('click', function(e){
			var form = $('#sup-create-form');
			form.parsley().validate();
			if(form.parsley().isValid()){
				var url = '<?php echo base_url().''.$getController; ?>/createSupplierManager/';
				var postData = new Array();
				postData.push($('#sup-name').val());
				postData.push($('#sup-address').val());
				postData.push($('#sup-email').val());
				postData.push($('#sup-contact').val());
				$.post(url,{ postData:postData }, function(data){
					window.location.reload();
				});
			}
			e.preventDefault();
			e.stopPropagation();
		});

		$('#sup-create-form .myFormBtnCancel').on('click', function(){
			var form = $('#sup-create-form');
			form.parsley().reset();
			form.trigger('reset');
		});

	});
</script>
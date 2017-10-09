<script type="text/javascript">
	$(document).ready(function (){

		//redirect to items
		$('.redirectToItemBtn').on('click', function(){
			var categoryId = new String($(this).attr('id'));

			var url = '<?php echo base_url().''.$getController; ?>/itemManager/'+categoryId+'/';
			$.post(url, { }, function(){
				window.location.replace(url);
			});

		});	
	
		//update	
		$('.updateBtn').on('click', function(e){
			var ids = new String($(this).attr('id'));
			ids = ids.split('_');

			var category_id = ids[0];
			var category_name = ids[1];

			$('#update-modal').modal({ show: 'show', backdrop: 'static' });
			$('#cat_name').html($('<input type="text" id="cat-name" class="form-control" value="'+category_name+'" data-parsley-required-message="This field is required" required="required">'));

			$('#cat-update-form .myFormBtnSubmit').on('click', function(e){
				var form = $('#cat-update-form');
				form.parsley().validate();
				if(form.parsley().isValid()){
					var url = '<?php echo base_url().''.$getController; ?>/updateCategoryManager/'+category_id+'/';
					var postData = new Array();
					postData.push($('#cat_name #cat-name').val());
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
		$('.deleteBtn').on('click', function(e){
			var ids = new String($(this).attr('id'));
			ids = ids.split('_');

			var category_id = ids[0];
			var category_name = ids[1];

			$('#delete-modal').modal({ show: 'show', backdrop: 'static' });
			$('#cat').html($('<p style="color: #800000; text-indent: 1em;">'+category_name+'.</p>'));		

			$('#delete-cat').on('click', function() {
				var url = '<?php echo base_url().''.$getController; ?>/deleteCategoryManager/'+category_id+'/';
				$.post(url, {}, function(){
					window.location.reload();
				});
			});
			e.preventDefault();
			e.stopPropagation();			
		});

		//create
		$('#cat-create-form .myFormBtnSubmit').on('click', function(e){
			var form = $('#cat-create-form');
			form.parsley().validate();
			if(form.parsley().isValid()){
				var url = '<?php echo base_url().''.$getController; ?>/createCategoryManager/';
				var postData = new Array();
				postData.push($('#cat-name').val());
				$.post(url,{ postData:postData }, function(data){
					window.location.reload();
				});
			}
			e.preventDefault();
			e.stopPropagation();
		});

		$('#cat-create-form .myFormBtnCancel').on('click', function(){
			var form = $('#cat-create-form');
			form.parsley().reset();
			form.trigger('reset');
		});	
		
		$('#datatable-display').DataTable({
			'order': [[ 1, 'asc' ]],
			responsive: true,
			autoWidth: true
		});		
	});	

</script>
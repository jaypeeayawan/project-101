<script type="text/javascript">
	$(document).ready(function(){

		$('#date-purchased').datetimepicker({  });

		//item add comments
		$('.commentsAddBtn').on('click', function(e){
			var itemId = new String($(this).attr('id'));

			$('#item-add-comments-modal').modal({ show: 'show', backdrop: 'static' });
			$('#item-add-comment-form .myFormBtnSubmit').on('click', function(){
				var form = $('#item-add-comment-form');
				form.parsley().validate();
				if(form.parsley().isValid()){
					var url = '<?php echo base_url().''.$getController; ?>/addCommentItemManager/'+itemId;
					var postData = $('#item-add-comment').val();
					$.post(url, { postData:postData }, function(){
						window.location.reload();
					});
				}
			});
			e.preventDefault();
			e.stopPropagation();
		});

		//item view comments
		$('.commentsViewBtn').on('click', function(){
			var itemId = new String($(this).attr('id'));

			$('#item-view-comments-modal').modal({ show: 'show', backdrop: 'static' });
			var url = '<?php echo base_url().''.$getController; ?>/viewCommentItemManager/'+itemId;
			$.ajax({
				type: 'POST',
				url: url,
				data: '',
				dataType: 'json',
				success: function(results){
					for( var i in results) {
						$("#itemCommentsContainer").append('<li><div class="block"><div class="block_content"><h2 class="title"><a>'+results[i].commentDate+'</a></h2><div class="byline"><span><a></a></span></div><p>'+results[i].comment+'</p></div></div></li>');
					}
				},
				error: function(xhr, ajaxOptions, thrownError){
				}
			});

			$('button[name="closeBtn"]').on('click', function(){
				$("#itemCommentsContainer").html('');
			});

			$('#item-view-comments-modal').on("hide.bs.modal",function(){
				$("#itemCommentsContainer").html('');
			});
		});

		//view item history
		$('.historyViewBtn').on('click', function(){
			var itemId = new String($(this).attr('id'));

			$('#item-view-history-modal').modal({ show: 'show', backdrop: 'static' });
			var url = '<?php echo base_url().''.$getController; ?>/viewHistoryItemManager/'+itemId;
			$.ajax({
				type: 'POST',
				url: url,
				data: '',
				dataType: 'json',
				success: function(results){
					console.log(results);
					for( var i in results) {
						$("#itemHistoryContainer").append('<li><div class="block"><div class="block_content"><h2 class="title"><a>'+results[i].date+'</a></h2><div class="byline"><span><a></a></span></div><p>'+results[i].remarks+' '+results[i].employee+'</p></div></div></li>');
					}
				},
				error: function(xhr, ajaxOptions, thrownError){
				}
			});

			$('button[name="closeBtn"]').on('click', function(){
				$("#itemHistoryContainer").html('');
			});

			$('#item-view-history-modal').on("hide.bs.modal",function(){
				$("#itemHistoryContainer").html('');
			});
		});

		//item information
		$('.informationBtn').on('click', function(){
			var ids = new String($(this).attr('id'));
			ids = ids.split('_');
			var itemId = ids[0];
			var supplierId = ids[1];
			var supplierName = ids[2];
			var categoryId = ids[3];
			var categoryName = ids[4];
			var itemCode = ids[5];
			var itemBrand = ids[6];
			var itemDescription = ids[7];
			var orNumber = ids[8];
			var itemUnitPrice = ids[9];
			var datePurchased = ids[10];

			$('#item-information-modal').modal({ show: 'show', backdrop: 'static' });

			var info = '<form class="form-horizontal" role="form">';
				info += '<div class="form-group">';
					info += '<label class="col-sm-4 control-label">Supplier</label>';
					info += '<div class="col-sm-8">';
						info += '<p class="form-control-static">'+supplierName+'</p>';
					info += '</div>';
				info += '</div>';
				info += '<div class="form-group">';
					info += '<label class="col-sm-4 control-label">Name/Categoy</label>';
					info += '<div class="col-sm-8">';
						info += '<p class="form-control-static">'+categoryName+'</p>';
					info += '</div>';
				info += '</div>';
				info += '<div class="form-group">';
					info += '<label class="col-sm-4 control-label">Item ID</label>';
					info += '<div class="col-sm-8">';
						info += '<p class="form-control-static">'+itemCode+'</p>';
					info += '</div>';
				info += '</div>';
				info += '<div class="form-group">';
					info += '<label class="col-sm-4 control-label">Item Brand</label>';
					info += '<div class="col-sm-8">';
						info += '<p class="form-control-static">'+itemBrand+'</p>';
					info += '</div>';
				info += '</div>';
				info += '<div class="form-group">';
					info += '<label class="col-sm-4 control-label">Item Description</label>';
					info += '<div class="col-sm-8">';
						info += '<p class="form-control-static">'+itemDescription+'</p>';
					info += '</div>';
				info += '</div>';
				info += '<div class="form-group">';
					info += '<label class="col-sm-4 control-label">OR Number</label>';
					info += '<div class="col-sm-8">';
						info += '<p class="form-control-static">'+orNumber+'</p>';
					info += '</div>';
				info += '</div>';
				info += '<div class="form-group">';
					info += '<label class="col-sm-4 control-label">Unit Price</label>';
					info += '<div class="col-sm-8">';
						info += '<p class="form-control-static">'+itemUnitPrice+'</p>';
					info += '</div>';
				info += '</div>';
				info += '<div class="form-group">';
					info += '<label class="col-sm-4 control-label">Date Purchased</label>';
					info += '<div class="col-sm-8">';
						info += '<p class="form-control-static">'+datePurchased+'</p>';
					info += '</div>';
				info += '</div>';
				info += '</form>';

			$('.itemInfoContainer').html(info);
		});

		//update
		$('.updateBtn').on('click', function(e){
			var ids = new String($(this).attr('id'));
			ids = ids.split('_');
			var itemId = ids[0];
			var supplierId = ids[1];
			var supplierName = ids[2];
			var categoryId = ids[3];
			var categoryName = ids[4];
			var itemCode = ids[5];
			var itemBrand = ids[6];
			var itemDescription = ids[7];
			var orNumber = ids[8];
			var itemUnitPrice = ids[9];
			var datePurchased = ids[10];

			$('#update-modal').modal({ show: 'show', backdrop: 'static' });

			$('select#item-sup').prepend('<option value="'+supplierId+'" selected="selected">'+supplierName+'</option>');
			$('select#item-cat').prepend('<option value="'+categoryId+'" selected="selected">'+categoryName+'</option>');
			$('#item_code').html($('<input type="text" id="itemCode" class="form-control" value="'+itemCode+'" data-parsley-required-message="This field is required" required="required">'));
			$('#item_brand').html($('<input type="text" id="itemBrand" class="form-control" value="'+itemBrand+'" data-parsley-required-message="This field is required" required="required">'));
			$('#item_description').html($('<textarea id="itemDescription" class="form-control" data-parsley-required-message="This field is required" required="required">'+itemDescription+'</textarea>'));
			$('#or_number').html($('<input type="text" id="orNumber" class="form-control" value="'+orNumber+'" data-parsley-required-message="This field is required" required="required">'));
			$('#item_unit_price').html($('<input type="text" id="itemUnitPrice" class="form-control" value="'+itemUnitPrice+'" data-parsley-required-message="This field is required" required="required">'));
			$('#date_purchased').html($('<input type="text" id="itemPurchased" class="form-control" value="'+datePurchased+'" data-parsley-required-message="This field is required" required="required">'));

			$('#itemPurchased').datetimepicker();

			$('#item-update-form .myFormBtnSubmit').on('click', function(){
				var form = $('#item-update-form');
				form.parsley().validate();
				if(form.parsley().isValid()){
					var url = '<?php echo base_url().''.$getController; ?>/updateItemManager/'+itemId;
					var postData = new Array();
					postData.push($('select.item_sup').val());
					postData.push($('select.item_cat').val());
					postData.push($('#itemCode').val());
					postData.push($('#itemBrand').val());
					postData.push($('#itemDescription').val());
					postData.push($('#orNumber').val());
					postData.push($('#itemUnitPrice').val());
					postData.push($('#itemPurchased').val());
					$.post(url, { postData:postData }, function(){
						window.location.reload();
					});
				}
			});
			e.preventDefault();
			e.stopPropagation();
		});

		//trash
		$('.deleteBtn').on('click', function(e){
			var ids = new String($(this).attr('id'));
			ids = ids.split('_');
			var itemId = ids[0];
			var supplierId = ids[1];
			var supplierName = ids[2];
			var categoryId = ids[3];
			var categoryName = ids[4];
			var itemCode = ids[5];
			var itemBrand = ids[6];
			var itemDescription = ids[7];
			var orNumber = ids[8];
			var itemUnitPrice = ids[9];
			var datePurchased = ids[10];
			var stockQuantity = ids[11];

			$('#delete-modal').modal({ show: 'show', backdrop: 'static' });
			$('#item').html($('<p style="color: #800000; text-indent: 2em;">'+categoryName+' - '+itemBrand+'</p>'));

			$('#delete-item').on('click', function() {
				var url = '<?php echo base_url().''.$getController; ?>/trashItemManager/'+itemId+'/';
				$.post(url, {}, function(){
					window.location.reload();
				});
			});

			e.preventDefault();
			e.stopPropagation();
		});

		//$('.inputConsumable').hide();
		$('input[name="is-consumable"]').on('click', function(){
			alert();
			/*if($(this).prop('checked')){
				$('.inputConsumable').show();
			}else{
				$('.inputConsumable').hide();
			}*/
		});

		//create
		$('#item-create-form .myFormBtnSubmit').on('click', function(e){
			var form = $('#item-create-form');
			form.parsley().validate();
			if(form.parsley().isValid()){
				var url = '<?php echo base_url().''.$getController; ?>/createItemManager/';
				var postData = new Array();
				postData.push($('#item-sup').val());
				postData.push($('#item-cat').val());
				postData.push($('#item-code').val());
				postData.push($('#item-brand').val());
				postData.push($('#item-description').val());
				postData.push($('#or-number').val());
				postData.push($('#item-unit-price').val());
				postData.push($('#date-purchased').val());

				($('#is-consumable').is(':checked') ? postData.push(1): postData.push(0));

				$.post(url,{ postData:postData }, function(){
					window.location.reload();
				});
			}
			e.preventDefault();
			e.stopPropagation();
		});

		$('#item-create-form .myFormBtnCancel').on('click', function(){
			var form = $('#item-create-form');
			form.parsley().reset();
			form.trigger('reset');
		});

		$('table#datatable-item').DataTable({
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

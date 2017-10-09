<script type="text/javascript">
	$(document).ready(function(){
		
		function pageReload(){
			return window.location.reload();
		}
		
		$('.commentsBtn').on('click', function(){
			var itemId = new String($(this).attr('id'));
			//item view comments
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
		
		$('.disposeItemBtn').on('click', function(){
			var ids = new String($(this).attr('id'));
			ids = ids.split('_');
			var stockId = ids[0];
			var itemCode = ids[1];
			var itemName = ids[2];
			var itemBrand = ids[3];
			
			$('#dispose-item-modal').modal({ show: 'show', backdrop: 'static' });
			$('#disposeItemContainer').html($('<p style="color: #800000; text-indent: 2em;">['+itemCode+'] '+itemName+' - '+itemName+'</p>'));
			$('#dispose-item').on('click', function(){
				var url = '<?php echo base_url().''.$getController; ?>/disposeStockManager/'+stockId;
				$.post(url, { }, function(){
					pageReload();
				});
			});
		});
		
		// datatable
		var table = $('table.stock-item').DataTable({
			dom: "Bfrtip",
			buttons: [
				{
					extend: "copy",
					className: "btn-sm"
				},
				{
					extend: "csv",
					className: "btn-sm"
				},
				{
					extend: "excel",
					className: "btn-sm"
				},
				{
					extend: "pdfHtml5",
					className: "btn-sm"
				},			
				{
					extend: "print",
					className: "btn-sm"
				},
			],			
          	pageLength : 50,
          	"columnDefs" : [
	            { "searchable": false, "targets": [4]},
	            { "orderable": false, "targets": [4]}
			],
          	"order": [[ 0, "asc" ]],
          	responsive: false
		});
		
		$('a.toggle-vis').css({
			'cursor' : 'pointer',
			'color': '#3174c7',
			'text-decoration': 'none'
		});		
		
		$('a.toggle-vis').on( 'click', function (e){
			e.preventDefault();
	 
			// Get the column API object
			var column = table.column($(this).attr('data-column'));
	 
			// Toggle the visibility
			column.visible(!column.visible());
		});	
		
	});
</script>
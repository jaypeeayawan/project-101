<script type="text/javascript">
	$(document).ready(function(){

		$('#date-assign').datetimepicker();

		//return item
		$('.returnItemBtn').on('click', function(e){
			var ids = new String($(this).attr('id'));
			ids = ids.split('_');

			var assignedItemId = ids[0];
			var itemId = ids[1];
			var itemCode = ids[2];
			var itemBrand = ids[3];
			var categoryName = ids[4];

			$('#return-assigned-item-modal').modal({ show: 'show', backdrop: 'static' });
			$('#returnItemContainer').html($('<p style="color: #800000; text-indent: 1em;">['+itemCode+'] '+categoryName+' - '+itemBrand+'</p>'));

			$('#return-item').on('click', function(){
				var url = '<?php echo base_url().''.$getController; ?>/returnAssignedNonConsumableItemManager/'+assignedItemId+'/'+itemId;
				$.post(url, { }, function(){
					window.location.reload();
				});
			});
			e.preventDefault();
			e.stopPropagation();
		});

		//assign item
		$('#assign-item-form .myFormBtnSubmit').on('click', function(e){
			var form = $('#assign-item-form');
			form.parsley().validate();
			if(form.parsley().isValid()){
				var url = '<?php echo base_url().''.$getController; ?>/createAssignNonConsumableItemManager/';
				var postData = new Array();
				postData.push($('#emp-name').val());
				postData.push($('#item-name').val());
				postData.push($('#location').val());

				$.post(url, { postData:postData }, function(data){
					window.location.reload();
				});
			}
			e.preventDefault();
			e.stopPropagation();
		});

		$('#assign-item-form .myFormBtnCancel').on('click', function(){
			var form = $('#assign-item-form');
			form.parsley().reset();
			form.trigger('reset');
		});

		$('table#datatable-assigned-item').DataTable({
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
          	pageLength : 25,
          	"columnDefs" : [
	            { "searchable": false, "targets": [6]},
	            { "orderable": false, "targets": [6]}
			],
          	"order": [[ 0, "asc" ]],
          	responsive: false
		});

	});
</script>

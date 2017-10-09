<script type="text/javascript">
	$(document).ready(function(){

		$('#date-assign').datetimepicker();

		//assign item
		$('#assign-item-form .myFormBtnSubmit').on('click', function(e){
			var form = $('#assign-item-form');
			form.parsley().validate();
			if(form.parsley().isValid()){
				var url = '<?php echo base_url().''.$getController; ?>/createAssignedItem/';
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
	            // { "searchable": false, "targets": [7]},
	            // { "orderable": false, "targets": [7]}
			],
          	"order": [[ 0, "asc" ]],
          	responsive: false
		});

	});
</script>

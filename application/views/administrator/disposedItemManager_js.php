<script type="text/javascript">
	$(document).ready(function(){
		
		$('table#disposed-item').DataTable({
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
          	"order": [[ 0, "asc" ]],
          	responsive: true
		});

	});
</script>
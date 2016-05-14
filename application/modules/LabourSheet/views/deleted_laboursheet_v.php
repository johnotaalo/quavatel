<style>
	.no-projects
	{
		vertical-align: middle;
		text-align: center;
	}

	.no-projects h1 i
	{
		font-size: 150px;
	}
</style>
<?php if($laboursheet_table == ""){?>
	<div class = "no-projects">
		<h1><i class = "zmdi zmdi-alert-circle-o"></i></h1>
		<h2>There are no Items to Restore</h2>
		<a href = "<?php echo base_url();?>LabourSheet"><i class = "zmdi zmdi-arrow-left"></i> Go Back To Labour sheet</a>
	</div>
<?php } else { ?>
	<div class="card">
		<div class="card-header ch-alt m-b-20">
			<h2>Deleted Labour Sheet Items<small>All deleted labour sheet items come here. You can restore them from here</small></h2>
		</div>
		<div class="card-body card-padding">
			<table class = "table table-bordered table-stripped data-table">
				<thead>
					<th>#</th>
					<th>Full Name</th>
					<th>ID</th>
					<th>Date</th>
					<th>Project</th>
					<th>Type of Work</th>
					<th>Wage Amount</th>
					<th>Restore</th>
				</thead>
				<tbody>
					<?php echo $laboursheet_table; ?>
				</tbody>
			</table>
		</div>
	
	</div>
<?php }?>


<script type="text/javascript">
	$(document).ready(function(){
		restore_button = $('.restore_laboursheet_item');

		restore_button.click(function(event){
			event.preventDefault();

			var response = confirm("You are about to restore this labour sheet entry. Continue?");

			if(response == true)
			{
				window.location = $(this).attr("href");
			}
		});
	});
</script>
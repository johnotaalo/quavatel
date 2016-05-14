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
<?php if($awaiting_table == ""){?>
	<div class = "no-projects">
		<h1><i class = "zmdi zmdi-alert-circle-o"></i></h1>
		<h2>There are no Projects to Approve</h2>
		<a href = "<?php echo base_url();?>Project"><i class = "zmdi zmdi-arrow-left"></i> Go Back To Projects</a>
	</div>
<?php } else { ?>
	<div class="card">
		<div class="card-header ch-alt m-b-20">
			<h2>Projects Awaiting Approval<small>All projects awaiting approval come here. You can approve them from here</small></h2>
		</div>
		<div class="card-body card-padding">
			<table class = "table table-bordered table-stripped">
				<thead>
					<th>#</th>
					<th>Project Name</th>
					<th>Start Date</th>
					<th>End Date</th>
					<th>Created By</th>
					<th>Approval</th>
					<th>Deletion</th>
				</thead>
				<tbody>
					<?php echo $awaiting_table; ?>
				</tbody>
			</table>
		</div>
	
	</div>
<?php }?>

<script type="text/javascript">
	$(document).ready(function(){
		restore_button = $('.approve_project');

		restore_button.click(function(event){
			event.preventDefault();

			var response = confirm("You are about to approve this project. Continue?");

			if(response == true)
			{
				window.location = $(this).attr("href");
			}
		});
	});
</script>
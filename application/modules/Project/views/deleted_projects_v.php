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
<?php if($projects_table == ""){?>
	<div class = "no-projects">
		<h1><i class = "zmdi zmdi-alert-circle-o"></i></h1>
		<h2>There are no Projects to Restore</h2>
		<a href = "<?php echo base_url();?>Project"><i class = "zmdi zmdi-arrow-left"></i> Go Back To Projects</a>
	</div>
<?php } else { ?>
	<div class="card">
		<div class="card-header ch-alt m-b-20">
			<h2>Deleted Projects<small>All deleted projects come here. You can restore them from here</small></h2>
		</div>
		<div class="card-body card-padding">
			<table class = "table table-bordered table-stripped">
				<thead>
					<th>#</th>
					<th>Project Name</th>
					<th>Deleted on</th>
					<th>Restore</th>
				</thead>
				<tbody>
					<?php echo $projects_table; ?>
				</tbody>
			</table>
		</div>
	
	</div>
<?php }?>


<script type="text/javascript">
	$(document).ready(function(){
		restore_button = $('.restore_project');

		restore_button.click(function(event){
			event.preventDefault();

			var response = confirm("You are about to restore this project. Continue?");

			if(response == true)
			{
				window.location = $(this).attr("href");
			}
		});
	});
</script>
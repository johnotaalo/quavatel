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
<?php if($deleted_reports_table == ""){?>
	<div class = "no-projects">
		<h1><i class = "zmdi zmdi-alert-circle-o"></i></h1>
		<h2>There are no Reports to Restore</h2>
		<a href = "<?php echo base_url();?>Project/data/<?php echo $project_type; ?>"><i class = "zmdi zmdi-arrow-left"></i> Go Back To Reports</a>
	</div>
<?php } else { ?>
	<div class="card">
		<div class="card-header ch-alt m-b-20">
			<h2>Deleted Reports<small>All deleted reports come here. You can restore them from here</small></h2>
		</div>
		<div class="card-body card-padding">
			<table class = "table table-bordered table-stripped">
				<thead>
					<th>#</th>
					<th>Project Name</th>
					<th>Client</th>
					<th>Added on</th>
					<th>Restore</th>
				</thead>
				<tbody>
					<?php echo $deleted_reports_table; ?>
				</tbody>
			</table>
		</div>
	
	</div>
<?php }?>
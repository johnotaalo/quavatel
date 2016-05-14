<style type="text/css">
	#content
	{
		padding-left: 250px;
	}

	#main
	{
		padding-top: 90px;
	}

	.container
	{
		padding-right: 0;
	}

	body
	{
		background-color: #f2f2f2;
	}
</style>

<div class = "row">
	<div class = "col-sm-12">
		<div class="card">
			<div class="card-body card-padding">
				<div class = "row">
					<div class = "col-sm-6"></div>
					<div class = "col-sm-6 pull-right">
						<a class = "btn bgm-teal btn-icon-text"><i class = "zmdi zmdi-filter-list"></i> Advanced Filter</a>
						&nbsp;
						<a class = "btn bgm-teal btn-icon-text"><i class = "zmdi zmdi-download"></i> Advanced Filter</a>
						&nbsp;
						<a class = "btn bgm-teal btn-icon-text"><i class = "zmdi zmdi-view-web"></i> View Report</a>
					</div>
				</div>
				<div class = "row" style = "padding: 15px 0;">
					<p>Showing Details from: 1st January 2016 to 8th January 2016</p>
				</div>
				<div class = "row">
					<table class = "table table-bordered" id = "labour_sheet">
						<thead>
							<th>#</th>
							<th>Name</th>
							<th>ID No</th>
							<th>Mobile No</th>
							<th>Unpaid Wages</th>
							<th>Details</th>
							<th>Options</th>
						</thead>
						<tbody>
							<?php echo $labourers_table; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		var labour_sheet_table = $("#labour_sheet");
		labour_sheet_table.DataTable();
	});
</script>
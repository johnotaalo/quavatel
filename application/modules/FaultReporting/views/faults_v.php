<div class="row m-t-25 p-0 m-b-25">
	<div class="col-xs-3">
		<a href = "#" data-type = 'all' class = "status-box">
			<div class="bgm-blue brd-2 p-15">
				<div class="c-white m-b-5">ALL FAULTS</div>
				<h2 class="m-0 c-white f-300"><?php echo $fault_counter['all']; ?></h2>
			</div>
		</a>
	</div>
	
	<div class="col-xs-3">
		<a href = "#" data-type = 'open' class = "status-box">
			<div class="bgm-red brd-2 p-15">
				<div class="c-white m-b-5">OPEN FAULTS</div>
				<h2 class="m-0 c-white f-300"><?php echo $fault_counter['open']; ?></h2>
			</div>
		</a>
	</div>                              
	
	<div class="col-xs-3">
		<a href = "#" data-type = 'confirmed' class = "status-box">
			<div class="bgm-amber brd-2 p-15">
				<div class="c-white m-b-5">CONFIRMED FAULTS</div>
				<h2 class="m-0 c-white f-300"><?php echo $fault_counter['confirmed']; ?></h2>
			</div>
		</a>
	</div>
	<div class="col-xs-3">
		<a href = "#" data-type = 'cleared' class = "status-box">
			<div class="bgm-green brd-2 p-15">
				<div class="c-white m-b-5">CLEARED FAULTS</div>
				<h2 class="m-0 c-white f-300"><?php echo $fault_counter['cleared']; ?></h2>
			</div>
		</a>
	</div>
</div>
<div class = "card">
	<div class = "card-header">
		<h2>Fault Reports <small>Find a list of all the fault reports</small></h2>
		<button class="btn bgm-red btn-float waves-effect custom-anchor" id = 'add-fault' data-href = "<?php echo base_url(); ?>FaultReporting/AddReport"><i class="zmdi zmdi-notifications-add"></i></button>
	</div>
	<div class = "card-body">
		<div class = "row">
			<div class = "col-md-12">
				<label>Filter Fault List by Status</label>
				<select id = "status">
					<option value = "all">All Faults</option>
					<option value = "open">Open Faults</option>
					<option value = "confirmed">Confirmed Faults</option>
					<option value = "cleared">Cleared Faults</option>
				</select>
			</div>
		</div>
		<table class = "table table-bordered table-striped">
			<thead>
				<th>#</th>
				<th>Ticket No</th>
				<th>Link</th>
				<th>Time Reported</th>
				<th>Time Confirmed</th>
				<th>Status</th>
				<th>Option</th>
			</thead>
			<tbody>
				<?php echo $faults_table; ?>
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
	$('#add-fault').click(function(){
		location.href = $(this).attr('data-href');
	});

	$('.status-box').click(function(){
		status = $(this).attr('data-type');
		var element = document.getElementById('status');
   		element.value = status;
   		var search_text = $('.dataTables_filter input');
   		if (status == "all")
		{
			search_text.val("").keyup();
		}
		else
		{
			search_text.val(status).keyup();
		}
	});

	$('table').dataTable();

	$('#status').change(function(){
		var search_text = $('.dataTables_filter input');
		if ($(this).val() == "all")
		{
			search_text.val("").keyup();
		}
		else
		{
			search_text.val($(this).val()).keyup();
		}
	});
</script>
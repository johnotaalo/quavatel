<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<style>
	#show_calendar
	{
		position: absolute;
		display: none;
		background: #fff;
		padding: 4px;
	}
	
	#picker
	{
		padding: 8px;
		
	}
</style>
<div class = "card">
	<div class = "card-header ch-alt">
		<h2>Labour Sheet for <?php echo date('dS M, Y', strtotime($date)); ?></h2>
		<ul class="actions">
			<li>
				<a href="#" title = "Choose another Date" id = "calendar_link">
					<i class="zmdi zmdi-calendar"></i>
				</a>
			</li>
			<li class = "dropdown">
				<a href="#" data-toggle="dropdown">
				<i class="zmdi zmdi-download"></i>
				</a>
				<ul class="dropdown-menu dropdown-menu-right">
					<li>
					<a href="<?php echo base_url(); ?>LabourSheet/export/daily/pdf/<?php echo $date; ?>">Download PDF</a>
					</li>
					<li>
					<a href="<?php echo base_url(); ?>LabourSheet/export/daily/excel/<?php echo $date; ?>">Download Excel</a>
					</li>
				</ul>
			</li>
			<li class="dropdown">
				<a href="#" data-toggle="dropdown">
					<i class="zmdi zmdi-more-vert"></i>
				</a>
				
				<ul class="dropdown-menu dropdown-menu-right">
					<li>
						<a href="<?php echo base_url(); ?>LabourSheet/weekly">View Week Data</a>
					</li>
					<li>
						<a href="<?php echo base_url(); ?>LabourSheet/deleted">View Deleted Items</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
	<div class = "card-body card-padding">
		
		<div class="row m-t-25 p-0 m-b-25">
			<div class="col-xs-3">
				<div class="bgm-amber brd-2 p-15">
					<div class="c-white m-b-5">Today's Wage Bill</div>
					<h2 class="m-0 c-white f-300">Ksh. <?php echo number_format($daily_total); ?></h2>
				</div>
			</div>
			
			<div class="col-xs-3">
				<div class="bgm-blue brd-2 p-15">
					<div class="c-white m-b-5">Date</div>
					<h2 class="m-0 c-white f-300"><?php echo date('d/m/Y', strtotime($date)); ?></h2>
				</div>
			</div>                              
			
			<div class="col-xs-3">
				<a href = "<?php echo base_url(); ?>LabourSheet/export/daily/pdf/<?php echo $date; ?>">
					<div class="bgm-red brd-2 p-15">
						<div class="c-white m-b-5">Export As</div>
						<h2 class="m-0 c-white f-300">PDF</h2>
					</div>
				</a>
			</div>
			<div class="col-xs-3">
				<a href = "<?php echo base_url(); ?>LabourSheet/export/daily/excel/<?php echo $date; ?>">
					<div class="bgm-green brd-2 p-15">
						<div class="c-white m-b-5">Export As</div>
						<h2 class="m-0 c-white f-300">Excel</h2>
					</div>
				</a>
			</div>
		</div>
		<?php
			$data = array();
			$data['date'] = $date;
			$data['daily_table'] = $daily_table;
			$this->load->view('LabourSheet/daily_table_v', $data);
		?>
	</div>
</div>


<div id = "show_calendar">
	<div id = "date_picker"></div>
	<div id = "picker">
		<input name = "date_picked" id = "date_picked"/><button id = "get_data_btn">Get Data</button>
	</div>
</div>
<script>
	$('table').DataTable({
		"lengthMenu": [[20, 40, 60, -1], [20, 40, 60, "All"]]
	});
	
	$('#date_picker').datepicker({
		altField : '#date_picked'
	});
	
	$( "#date_picker" ).datepicker( "option", "dateFormat", "yy-mm-dd");
	$("#date_picker").datepicker( "setDate", "<?php echo $date; ?>" );
	$('#calendar_link').click(function() {
		$('#show_calendar').css({
			left: $(this).offset().left / 1.1 + 'px',
			top: ($(this).offset().top + $(this).height()) + 'px'
		}).toggle();
	});
	
	$("#get_data_btn").click(function(){
		window.location.href = "<?php echo base_url(); ?>LabourSheet/daily/" + $("input[name='date_picked']").val();
	});
</script>
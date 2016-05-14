<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<style type="text/css">
	body
	{
		background-color: #f2f2f2;
	}
</style>

<div class = "row">
	<div class = "col-sm-12">
		<div class="card">
			<div class="card-body card-padding">
				<div class = "row m-t-25 p-0 m-b-25">
					<div class = "col-sm-6">
						<a data-toggle="modal" href="#modalDefault">
							<div class="bgm-teal brd-2 p-15" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="To Change The Date Range, click on this box" style = "cursor: pointer;">
								<div class="c-white m-b-5">Displaying Details for week</div>
								<h2 class="m-0 c-white f-300"><?php echo date('d/m/Y', strtotime($from)) . " TO " . date('d/m/Y', strtotime($to)); ?></h2>
							</div>
						</a>
					</div>
					<div class = "col-sm-3">
						<div class="bgm-amber brd-2 p-15">
							<div class="c-white m-b-5">Wage Bill</div>
							<h2 class="m-0 c-white f-300">Ksh. <?php echo $total_payment; ?></h2>
						</div>
					</div>
					<a href = "<?php echo base_url(); ?>LabourSheet/export/weekly/excel/<?php echo $from; ?>/<?php echo $to; ?>">
						<div class = "col-sm-3">
							<div class="bgm-green brd-2 p-15">
								<div class="c-white m-b-5">Export to</div>
								<h2 class="m-0 c-white f-300">Excel</h2>
							</div>
						</div>
					</a>
				</div>
				<div class = "row">
					<table class = "table table-bordered" id = "labour_sheet">
						<thead>
							<th>#</th>
							<th>Name</th>
							<th>ID No</th>
							<th>Mobile No</th>
							<th>Unpaid Wages</th>
							<th>Supervisor Name</th>
							<th>Details</th>
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

<div class="modal fade" id="modalDefault" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
	    <div class="modal-content">
	        <div class="modal-header">
	            <h4 class="modal-title">Set Data Range</h4>
	        </div>
	        <div class="modal-body">
	        	<div class="col-sm-6">
                            <div class="form-group fg-line">
                                <label class="sr-only" for="from">From</label>
                                <input type="text" class="form-control input-sm" id="from" placeholder="From">
                            </div>
                        </div>
	        	<div class="col-sm-6">
                            <div class="form-group fg-line">
                                <label class="sr-only" for="to">To</label>
                                <input type="text" class="form-control input-sm" id="to" placeholder="To">
                            </div>
                        </div>
	        </div>
	        <div class="modal-footer">
	            <button type="button" class="btn btn-link" id = "get-range-data">Get Range Data</button>
	            <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
	        </div>
	    </div>
	</div>
</div>
<script type="text/javascript">
	Date.prototype.yyyymmdd = function() {
		var yyyy = this.getFullYear().toString();
		var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
		var dd  = this.getDate().toString();
		return yyyy + "-" + (mm[1]?mm:"0"+mm[0]) + "-" +(dd[1]?dd:"0"+dd[0]); // padding
	};
	$(document).ready(function(){
		var labour_sheet_table = $("#labour_sheet");
		labour_sheet_table.DataTable();
		$( "#from" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1,
			onClose: function( selectedDate ) {
				$( "#to" ).datepicker( "option", "minDate", selectedDate );
			}
		});
		$( "#to" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1,
			onClose: function( selectedDate ) {
				$( "#from" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
		
		$('#get-range-data').click(function(){
			var from = $("#from").val();
			var to = $("#to").val();
			
			if(from === "" || to === ""){
				if(from === "")
				{
					alert("From field is empty");
				}
				else if(to === "")
				{
					alert("To field is empty");
				}
			}else{
				f = new Date(from);
				t = new Date(to);
				window.location.href = "<?php echo base_url(); ?>LabourSheet/weekly/" + f.yyyymmdd()+"/"+t.yyyymmdd();
			}
		});
		/**/
	});
</script>
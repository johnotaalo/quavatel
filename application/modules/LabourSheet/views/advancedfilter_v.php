<div class = 'row'>
	<div class = "col-md-8">
		<form action = "<?php echo base_url(); ?>LabourSheet/filterdata" method = "POST">
			<div class="col-sm-5">
				<div class="form-group fg-line">
					<label>From Date</label>
					<input autocomplete="off" maxlength="10" class="form-control date-picker" type="text">
				</div>
			</div>
			<div class = "col-sm-5">
				<div class="form-group fg-line">
					<label>To Date</label>
					<input autocomplete="off" maxlength="10" class="form-control date-picker" type="text">
				</div>
			</div>
			<div class = "col-sm-2">
				<div class="form-group fg-line">
					<p></p>
					<button class="btn btn-primary btn-block waves-effect">FILTER</button>
				</div>
			</div>
		</form>
	</div>
</div>
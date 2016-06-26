<style type="text/css">
	.select2
	{
		width: 100% !important;
	}
</style>
<div class="card">
	<div class = "card-header">
		<h2>Create Ticket</h2>
	</div>
	<div class = "card-body card-padding">
		<form method="POST" action = "<?php echo base_url(); ?>FaultReporting/AddReport">
			<div class="form-group fg-float">
				<div class="fg-line">
					<input type="text" class="form-control fg-input" name = "station_name" required>
					<label class="fg-label">Link</label>
				</div>
			</div>

			<div class = "form-group">
				<label>Assigned Technician</label>
				<select name = "technician_id" class = "form-control" id = "staff_members" required>
					
				</select>
			</div>

			<div class = "form-group">
				<label>Client</label>
				<select name = "company_id" id = "clients" class = "form-control" required>
					
				</select>
			</div>

			<button class="btn btn-primary btn-lg waves-effect">Create Ticket</button>
		</form>
	</div>
</div>

<script type="text/javascript">
	$('#staff_members').select2({
		data: <?php echo $technical_staff; ?>
	});

	$('#clients').select2({
		data: <?php echo $clients; ?>
	});
</script>
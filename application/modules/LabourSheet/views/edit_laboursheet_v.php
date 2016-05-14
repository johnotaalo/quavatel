<p>
	<b>Labourer:</b> <?php echo $labourer_lastname ." ".$labourer_firstname; ?><br/><br/>
	<b>Date:</b> <?php echo date("dS F Y", strtotime($wage_date)); ?><br/><br/>
	<b>Purpose:</b> <?php echo $wagestructure_task; ?><br/><br/>

	<input type = "hidden" value="<?php echo base_url(); ?>LabourSheet/edit/<?php echo $wage_id; ?>/<?php echo $wage_date; ?>" name = "form_action">
</p>

<?php
	if(($wagestructure_unit != "" && $wagestructure_unit != NULL && $wagestructure_unit != "null" && $wagestructure_unit != "NULL") && ($wage_structure_length != 0 && $wage_structure_length != NULL))
	{ ?>
	<div class = "row">
		<div class = "col-md-6">
			<div class="form-group fg-line">
			    <label for= "wagestructure_task">Purpose Quantity(<?php echo $wagestructure_unit; ?>)</label>
			    <input type="text" class="form-control input-sm" name = "wage_structure_length" id="wage_structure_length" placeholder="Wage Structure Length" value = "<?php echo $wage_structure_length; ?>" required="required" >
			</div>
		</div>
		<div class = "col-md-6">
			<div class="form-group fg-line">
			    <label for= "wagestructure_task">Total Amount</label>
			    <input type="text" class="form-control input-sm" name = "wage_amount" id="wage_amount" placeholder="Wage Structure Length" value = "<?php echo $wage_amount; ?>" required="required" readonly>
			</div>
		</div>
	</div>

	<input type="hidden" name = 'wagestructure_rate' value = "<?php echo $wagestructure_rate; ?>">
<?php } else {?>

<div class="form-group fg-line">
    <label for= "wagestructure_task">Total Amount</label>
    <input type="text" class="form-control input-sm" name = "wage_amount" id="wage_amount" placeholder="Wage Structure Length" value = "<?php echo $wage_amount; ?>" required="required">
</div>
<?php } ?>
<script type="text/javascript">
	$('#wage_structure_length').keyup(function(){
		var value = $(this).val();
		var rate = $('input[name="wagestructure_rate"]').val();

		total = value * rate;
		$("#wage_amount").val(total);
	});
</script>
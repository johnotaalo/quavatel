<div class="form-group fg-line">
    <label for= "wagestructure_task">Task Name</label>
    <input type="text" class="form-control input-sm" name = "wagestructure_task" id="wagestructure_task" placeholder="Wage Structure Task" value = "<?php if(isset($structure_details)){echo $structure_details->wagestructure_task;} ?>" required="required">
</div>

<div class="form-group fg-line">
	<label for = "wagestructure_unit">Wage Structure Unit(**should be in plural)</label>
	<input type="text" class="typeahead form-control" name = "wagestructure_unit" placeholder="Units of Measurement" value = "<?php if(isset($structure_details)){echo $structure_details->wagestructure_unit;} ?>">
	<small class="help-block">If the task does not have a unit, leave this blank</small>
</div>
<div class = "row">
	<div class = "col-md-6">
		<div class="form-group fg-line">
		    <label for= "wagestructure_rate" id = "wst">Wage Structure Rate <span></span></label>
		    <input type="text" class="form-control input-sm" id="wagestructure_rate" placeholder="Wage Structure Rate" value = "<?php if(isset($structure_details)){echo $structure_details->wagestructure_rate;} ?>" name = "wagestructure_rate" required="required">
		</div>
	</div>
	<div class = "col-md-6">
		<div class="fg-line">
			
		</div>
	</div>
</div>

<input type = "hidden" value = "<?php if(isset($structure_details)){ echo $structure_details->wagestructure_id; }else{ echo '0'; }?>" name = "wagestructure_id"/>
<input type = "hidden" value = "<?php echo base_url()?>LabourSheet/<?php if(isset($structure_details)){ ?>editstruct<?php } else { ?>addstruct <?php } ?>" name = "form_action"/>

<script>
	var um = $('input[name="wagestructure_unit"]').val();
	console.log(um);
	if(um !== "")
	{
		if(um.slice(-1) === "s")
		{
			um = "Per " + um.substring(0, um.length - 1);
		}
	}

	$('label#wst span').html(um);

	$('input[name="wagestructure_unit"]').change(function(){
		var unit_of_measurement = $(this).val();
		if($(this).val().slice(-1) === "s")
		{
			unit_of_measurement = "Per " + $(this).val().substring(0, $(this).val().length - 1);
		}
		$('label#wst span').html(unit_of_measurement);
	});

	if($('.typeahead')[0]) {
		var substringMatcher = function(strs) {
			return function findMatches(q, cb) {
				var matches, substringRegex;

				// an array that will be populated with substring matches
				matches = [];

				// regex used to determine if a string contains the substring `q`
				substrRegex = new RegExp(q, 'i');

				// iterate through the pool of strings and for any string that
				// contains the substring `q`, add it to the `matches` array
				$.each(strs, function(i, str) {
					if (substrRegex.test(str)) {
						matches.push(str);
					}
				});

				cb(matches);
			};
		};

		var units = $.parseJSON('<?php echo $structure_units; ?>');

		$('.typeahead').typeahead({
			hint: true,
			highlight: true,
			minLength: 1
		},
		{
			name: 'units',
			source: substringMatcher(units)
		});
	}
</script>
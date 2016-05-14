<div class = "pdf-row" >
	<div class = "col-xs-4 pdf-33 pdf-left pull-left pdf-hold-image" style = "height: 100px;">
		<img src = "<?php echo base_url(); ?>assets/logos/quavatel.jpg" style = "max-width: 100%;max-height: 100%"/>
	</div>
	<div class = "col-xs-4 pdf-33 pdf-right pull-right pdf-hold-image" style = "height: 100px;">
		<img src = "<?php echo base_url(); ?>assets/logos/<?php echo $project_responses['company_logo']; ?>" style = "max-width: 100%;max-height: 100%"/>
	</div>
</div>

<?php //echo "<pre>";print_r($project_responses);die; ?>
<div class = "pdf-center text-center" style = "clear:both;">
	<h2>FIBRE OPTIC ACCEPTANCE TEST</h2>
	<h4>OTDR Results for: <?php echo $project_responses['project_name']; ?></h4>
	<?php
	$location = explode(';', $project_responses['project_location']);
	?>
	<p class = "f-500 f-15">From: <?php echo $location[0]; ?> To: <?php echo $location[1]; ?></p>
</div>
<div class = "table-responsive" style = "clear:both;">
	<table class = "table table-striped table-bordered pdf-table">
		<thead>
			<tr>
				<th>Attenuation Test</th>
				<th>Equipment:</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Number of Closures in the test section: <b><?php echo $project_responses['fat_closures']; ?></b> pcs</td>
				<td>Cable Type: <b><?php echo $project_responses['fat_cabletype']; ?></b></td>
			</tr>
			<tr>
				<td>Number of splices in the test section: <b><?php echo $project_responses['fat_splices']; ?></b> pcs</td>
				<td>Wavelength: <b><?php echo $project_responses['fat_wavelength']; ?></b> nm</td>
			</tr>
			<tr rowspan="2">
				<td>Specification for FO-Cable: <b><?php echo $project_responses['fat_cablespecs']; ?></b> dB/km</td>
				<td>Cable Length: <b><?php echo $project_responses['fat_cablelength']; ?></b> km
				<br/>
				Fiber Length: <b><?php echo $project_responses['fat_fibrelength']; ?></b> km
				</td>
			</tr>
			<tr>
				<td>Maximum Splice Loss: <b><?php echo $project_responses['fat_maxspiceloss']; ?></b> dB</td>
				<td>Test Date: <b><?php echo date('jS F, Y', strtotime($project_responses['fat_testdate'])); ?></b></td>
			</tr>
			<tr>
				<td>Connector Loss: <b><?php echo $project_responses['fat_connectorloss']; ?></b> dB</td>
				<td>Maximum Attenuation: <b><?php echo floatval(floatval($project_responses['fat_cablespecs']) + floatval($project_responses['fat_maxspiceloss'] + floatval($project_responses['fat_connectorloss']))); ?></b> dB</td>
			</tr>
		</tbody>
	</table>
</div>
<div class = "table-responsive">
	<table class = "table table-bordered pdf-table">
		<thead>
			<tr>
				<th rowspan = "2">Fibre No.</th>
				<th colspan = "2">Attenuation in dB</th>
				<th>Average Attenuation in dB</th>
				<th>Attenuation Coefficient dB/km</th>
			<tr>
			<tr>
				<td></td>
				<td>A-B</td>
				<td>B-A</td>
				<td></td>
				<td></td>
			</tr>
		</thead>
		<tbody>
			<?php
				$maximum_attenuation = floatval(floatval($project_responses['fat_cablespecs']) + floatval($project_responses['fat_maxspiceloss'] + floatval($project_responses['fat_connectorloss'])));
				foreach($project_responses['Attenuation'] as $key => $value)
				{
					$average_attenuation = (floatval($value['fat_ab']) +  floatval($value['fat_ba']))/2;
					$attenuation_coefficient = $average_attenuation + floatval($project_responses['fat_cablespecs']);
					$class = ($average_attenuation > $maximum_attenuation) ? "red" : "green";
					$style = ($average_attenuation > $maximum_attenuation) ? "style = 'color: red;'" : "";
					
					echo "<tr>";
					echo "<td>{$value['fat_fibrenumber']}</td>";
					echo "<td>{$value['fat_ab']}</td>";
					echo "<td>{$value['fat_ba']}</td>";
					echo "<td class = '{$class}' {$style}>" . $average_attenuation . "</td>";
					echo "<td>" . $attenuation_coefficient . "</td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
	<p class = "f-20"><span class = "question_title">QTL Engineer: </span><?php echo $project_responses["user_firstname"]. ' ' . $project_responses["user_lastname"]; ?></p>
	<p class = "f-20"><span class = "question_title">QTL Engineer Signature: </span>
	<br/>
	<img src = "<?php if(isset($project_responses['user_image'])){echo $project_responses['user_image'];}else{ echo base_url() . '/assets/img/employee.jpg';} ?>" style = "width: 100px;height:50px;"/>
	</p>
	<p class = "f-20"><span class="question_title"><?php echo $project_responses['company_name']; ?> Engineer: </span><?php echo $project_responses["fat_clientengineer"];?></p>
	<p class = "f-20"><span class="question_title"><?php echo $project_responses['company_name']; ?> Engineer Remarks</span></p>
	<p><?php echo $project_responses['fat_clientengineer_remarks']; ?></p>
	<p class = "f-20"><span class="question_title">Date Accepted: </span> <?php echo date('dS F, Y', strtotime($project_responses['fat_accepted_date'])); ?></p>
</div>
<pagebreak />
<div class="card-header">
    <h2>Project Images</h2>
</div>
<div class="lightbox row">
	<?php echo $project_images; ?>
</div>
<div class = "pdf-row" >
	<div class = "col-xs-4 pdf-33 pdf-left pull-left pdf-hold-image" style = "height: 100px;">
		<img src = "<?php echo base_url(); ?>assets/logos/quavatel.jpg" style = "max-width: 100%;max-height: 100%"/>
	</div>
	<div class = "col-xs-4 pdf-33 pdf-right pull-right pdf-hold-image" style = "height: 100px;">
		<img src = "<?php echo base_url(); ?>assets/logos/<?php echo $client->company_logo; ?>" style = "max-width: 100%;max-height: 100%"/>
	</div>
</div>

<h3 style="text-align:center;">FAULT REPORTING AND CLEARANCANCE REPORT</h3>
<table class = "pdf-table">
	<tr>
		<th colspan="4">
			Part 1: FAULT REPORT
		</th>
	</tr>
	<tr>
		<th colspan="4">STATION NAME: <?php echo $report_detail->station_name;?></th>
	</tr>
	<tr>
		<th>Client: </th>
		<td><?php echo $client->company_name; ?></td>
		<th>Time Cleared: </th>
		<td><?php echo $time_cleared; ?></td>
	</tr>
	<tr>
		<th>Date Reported</th>
		<th>Time Reported</th>
		<th>Reported By</th>
		<th>Designation</th>
	</tr>
	<tr>
		<td><?php echo date('d/m/Y', strtotime($report_detail->date_time_reported)); ?></td>
		<td><?php echo date('Hi', strtotime($report_detail->date_time_reported)); ?> Hrs</td>
		<td><?php echo $report_detail->username; ?></td>
		<td>NOC Engineer</td>
	</tr>
</table>
<table class="pdf-table">
	<tr>
		<th colspan = "3">Part 2: Quavatel Staff assigned</th>
	</tr>
	<tr>
		<th></th>
		<th>Name</th>
		<th>Designation</th>
	</tr>
	<?php
		if ($staff_assigned) {
			$counter = 1;
			foreach ($staff_assigned as $staff) {
				echo "<tr><td>{$counter}. </td>";
				echo "<td>{$staff->username}</td>";
				echo "<td>Fibre Technician</td></tr>";
				$counter++;
			}
		} else {
			echo "<td colspan = '6'>There are no technicians assigned for this task</td>";
		}
		
	?>
</table>
<table class = "pdf-table">
	<tr>
		<th colspan="2">Confirmation Information</th>
	</tr>
	<tr>
		<th>Technical Arrival Time</th>
		<th>Confirmation Location</th>
	</tr>
	<tr>
		<td><?php echo date('d-m-Y h:i:s a', strtotime($confirmation_information->location_confirmation_time)); ?></td>
		<td><?php echo $location; ?></td>
	</tr>
</table>
<table class="pdf-table">
	<tr>
		<th colspan = "3">Part 3: Nature of Fault</th>
	</tr>
	<tr>
		<th>No.</th>
		<th>Question</th>
		<th>Response</th>
	</tr>
	<?php echo $question_responses['nature_of_fault']; ?>
</table>

<table class="pdf-table">
	<tr>
		<th colspan = "3">Part 4: Severity of Fault</th>
	</tr>
	<?php echo $question_responses['severity_of_fault']; ?>
</table>

<table class="pdf-table">
	<tr>
		<th colspan = "3">Part 5: Event Log</th>
	</tr>
	<?php echo $event_log; ?>
</table>

<table class="pdf-table">
	<tr>
		<th class = "active" colspan = "2">Part 6. Technician Comments</th>
	</tr>

	<tr>
		<th rowspan="2">1. </th>
		<th>Please provide Detailed Comments on your findings on this fault</th>
	</tr>

	<tr>
		<td><?php echo $comments['detailed']; ?></td>
	</tr>

	<tr>
		<th rowspan="2">2. </th>
		<th>Please highlight any Remedial Action proposed</th>
	</tr>

	<tr>
		<td><?php echo $comments['remedial']; ?></td>
	</tr>
</table>

<table class = "pdf-table">
	<tr>
		<th colspan="3">Part 7: Clearance Information</th>
	</tr>
	<tr>
		<th style = "">Date of Clearance</th>
		<th style = "">Time of Clearance</th>
		<th style = "">Clearance Officer</th>
	</tr>
	<tr>
		<td><?php echo date('dS M Y', strtotime($clearance_information->date_time_cleared)); ?></td>
		<td><?php echo date('H:i:s', strtotime($clearance_information->date_time_cleared)); ?></td>
		<td><?php echo $clearance_information->user_firstname . " " . $clearance_information->user_lastname; ?></td>
	</tr>
</table>

<table class = "pdf-table">
	<tr>
		<th colspan="4">Part 8: Bill of Materials Used/Services</th>
	</tr>
	<tr>
		<th colspan="1"></th>
		<th colspan="2">Materials</th>
		<th colspan="1">Quantity</th>
	</tr>
	<?php echo $materials_used_pdf; ?>
</table>
<pagebreak />
<h2>Fault Reporting Images</h2>
<?php if (is_array($photos)) { ?>
<?php 
	foreach ($photos as $key => $value) {
		if ($key == "before") {
			echo "<h3>Before</h3>";
			echo $value;
			echo "<pagebreak />";
		}
		else
		{
			echo "<h3>After</h3>";
			echo "<div>{$value}</div>";
		}
	}
	?>
<?php } else { ?>
	<?php echo $photos; ?>
<?php } ?>
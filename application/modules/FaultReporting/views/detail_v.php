<?php if (!isset($type)) { ?>
<style type="text/css">
	.table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th
	{
		border-color: grey !important;
	}
	tr[rowspan]
	{
		vertical-align: middle;
	}

	.fault-image-holder img
	{
		width: 50%;
		float: left;
	}
</style>
<div class = "row">
	<div class = 'col-md-12'>
		<div class="card">
			<div class="card-header">
				<h2>FAULT REPORTING AND CLEARANCANCE REPORT</h2>
					<ul class="actions">
						<li>
							<a href="<?php echo base_url('FaultReporting/export/' . $report_detail->id); ?>" title = "Export to PDF">
								<i class="zmdi zmdi-collection-pdf" style = "color: red;"></i>
							</a>
						</li>
						<!-- <li>
						<a href="" title = "Export to Excel" style = "color: green;">
							<i class="zmdi zmdi-file-text"></i>
						</a>
						</li> -->
					</ul>
				<?php if($status == "submitted") { ?>
					<a class="btn bgm-green btn-float waves-effect custom-anchor" href = "<?php echo base_url(); ?>FaultReporting/ConfirmFault/<?php echo $report_id; ?>" title = "Confirm this fault has been restored"><i class="zmdi zmdi-check"></i></a>
				<?php } ?>
			</div>
<?php } ?>
			<div class="table-responsive">
				<table class = "table table-bordered table-hover">
					<?php if($status !== "open"){?>
					<tr class = "active">
						<th colspan = "6">Part 1. Fault Report</th>
					</tr>
					<tr>
						<td colspan = "6"><b>STATION/LINK: </b><?php echo $report_detail->station_name;?></td>
					</tr>
					<tr>
						<th>Client: </th>
						<td colspan = "2"><?php echo $client->company_name; ?></td>
						<th>Time Cleared: </th>
						<td colspan = "2"><?php echo $time_cleared; ?></td>
					</tr>

					<tr>
						<th>Date Reported</th>
						<th>Time Reported</th>
						<th colspan="2">Reported By</th>
						<th colspan="2">Designation</th>
					</tr>

					<tr>
						<td><?php echo date('d/m/Y', strtotime($report_detail->date_time_reported)); ?></td>
						<td><?php echo date('Hi', strtotime($report_detail->date_time_reported)); ?> Hrs</td>
						<td colspan="2"><?php echo $report_detail->username; ?></td>
						<td colspan="2">NOC Engineer</td>
					</tr>
					<tr class = "active">
						<th colspan = "6">Part 2. Quavatel Staff assigned</th>
					</tr>
					<tr>
						<th></th>
						<th colspan="2">Name</th>
						<th colspan = "3">Designation</th>
					</tr>
					<?php
						if ($staff_assigned) {
							$counter = 1;
							foreach ($staff_assigned as $staff) {
								echo "<tr><td>{$counter}. </td>";
								echo "<td colspan = '2'>{$staff->username}</td>";
								echo "<td colspan = '3'>Fibre Technician</td></tr>";
								$counter++;
							}
						} else {
							echo "<td colspan = '6'>There are no technicians assigned for this task</td>";
						}
						
					?>
					<tr class = "active">
						<th colspan = '6'>Confirmation Information</th>
					</tr>
					<tr>
						<th colspan = "3">Technical Arrival Time</th>
						<th colspan = "3">Confirmation Location</th>
					</tr>
					<tr>
						<td colspan = "3"><?php echo date('d-m-Y h:i:s a', strtotime($confirmation_information->location_confirmation_time)); ?></td>
						<td colspan = "3"><?php echo $location; ?></td>
					</tr>
					<tr class = "active">
						<th colspan = "6">Part 3. Nature of Fault</th>
					</tr>

					<tr>
						<th>No.</th>
						<th colspan="2">Question</th>
						<th>Response</th>
						<th colspan="2">Explanation</th>
					</tr>
					<?php echo $question_responses['nature_of_fault']; ?>

					<tr class = "active">
						<th colspan = "6">Part 4. Severity of Fault</th>
					</tr>
					<tr>
						<th>No.</th>
						<th colspan="2">Question</th>
						<th>Response</th>
						<th colspan="2">Explanation</th>
					</tr>
					<?php echo $question_responses['severity_of_fault']; ?>
					<tr class = "active">
						<th colspan = "6">Part 5. Event Log</th>
					</tr>
					<?php echo $event_log; ?>
					<tr>
						<th class = "active" colspan = "6">Part 6. Technician Comments</th>
					</tr>

					<tr>
						<th rowspan="2">1. </th>
						<th colspan="5">Please provide Detailed Comments on your findings on this fault</th>
					</tr>

					<tr>
						<td colspan="5"><?php echo $comments['detailed']; ?></td>
					</tr>

					<tr>
						<th rowspan="2">2. </th>
						<th colspan="5">Please highlight any Remedial Action proposed</th>
					</tr>

					<tr>
						<td colspan="5"><?php echo $comments['remedial']; ?></td>
					</tr>

					<tr class = "active">
						<th colspan = "6">Part 7. Clearance Information</th>
					</tr>
					<tr>
						<th colspan = "2" style = "width: 15%">Date of Clearance</th>
						<th colspan = "2" style = "width: 15%">Time of Clearance</th>
						<th colspan = "2" style = "width: 15%">Clearance Officer</th>
					</tr>
					<tr>
						<td colspan = "2"><?php echo date('dS M Y', strtotime($clearance_information->date_time_cleared)); ?></td>
						<td colspan = "2"><?php echo date('H:i:s', strtotime($clearance_information->date_time_cleared)); ?></td>
						<td colspan = "2"><?php echo $clearance_information->user_firstname . " " . $clearance_information->user_lastname; ?></td>
					</tr>
					<tr class = "active">
						<th colspan = "6">Part 8. Bill of Materials Used/Services</th>
					</tr>
					<tr>
						<th></th>
						<th colspan="4">Materials</th>
						<th>Quantity</th>
					</tr>
					<?php echo $materials_used; ?>
					<?php } else { ?>

					<h1 style="text-align: center;">This report was just opened. Still waiting for the fault technician to confirm a few things</h1>
					<p style="text-align: center;"><a href = "<?php echo base_url(); ?>FaultReporting" class = "btn btn-primary">Back to Fault Reports</a></p>
					<?php } ?>
				</table>
			</div>

			<div class = "fault-image-holder">
				<?php if (is_array($photos)) { ?>
					<?php 
					foreach ($photos as $key => $value) {
						if ($key == "before") {
							echo "<h3>Fault Images</h3>";
							echo "<div>{$value}</div>";
						}
						else
						{
							echo "<h3>After Restoration</h3>";
							echo "<div>{$value}</div>";
						}
					}?>
				<?php } ?>
			</div>
		</div>
</div>
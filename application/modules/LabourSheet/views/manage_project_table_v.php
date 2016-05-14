<table class = "table table-bordered">
	<thead>
		<tr>
			<th>#</th>
			<th style="width: 40%;">Project Name</th>
<!-- 			<th>Project Type</th> -->
			<th>Start Date</th>
			<th>End Date</th>
			<th>Status</th>
			<?php $user_permission = $this->M_Account->get_user_permission('user_id', $this->session->userdata('user_id'));if($user_permission == "admin"){?>
			<th style="width: 5%;">Options</th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php echo $projects_table; ?>
	</tbody>
</table>
<?php $permission = $this->M_Account->get_user_permission('user_id', $this->session->userdata('user_id')); if($type == "pdf") { ?><h2><?php echo $title; ?></h2><?php } ?>
<table class = "table table-bordered">
	<thead>
		<tr>
			<th>No.</th>
			<th>Task</th>
			<th>Rate</th>
			<?php if($type == "html" &&($permission == "admin")){ ?><th>Options</th> <?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php echo $wage_structure_table; ?>
	</tbody>
</table>
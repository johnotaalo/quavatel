<div class="card">
	<div class="card-header ch-alt">
		<h2><span style = "text-transform: uppercase;"><?php $pt = $project_type; if($project_type == "mss") {$project_type = "mis";} echo $project_type; ?></span> Data Table</h2>
		<?php
			$permission = $this->M_Account->get_user_permission('user_id', $this->session->userdata('user_id'));
			if($permission == "admin"){
		?>
			<ul class="actions">
				<li>
					<a href="<?php echo base_url(); ?>Project/deletedreports/<?php echo $pt; ?>">
						<i class="zmdi zmdi-rotate-cw"></i>
					</a>
				</li>
			</ul>
		<?php } ?>
	</div>
	<div class="table-responsive">
		<table id="data-table-basic" class="table table-striped">
			<thead>
				<tr>
					<th>#</th>
					<th data-column-id="project_name">Project Name</th>
					<th data-column-id="company_name">Client Name</th>
					<th data-column-id="site_name">Site Name</th>
					<th data-column-id="start_date">Date Added</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php echo $table_data; ?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		//Basic Example
		$('#data-table-basic').DataTable();
	});
</script>
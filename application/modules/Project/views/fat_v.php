<div class="card">
	<div class="card-header ch-alt">
		<h2> Response for: <?php echo $project_responses['project_name']; ?></h2>
		<ul class="actions">
			<li>
				<a href="">
					<i class="zmdi zmdi-refresh-alt"></i>
				</a>
			</li>
			<li class="dropdown">
				<a href="" data-toggle="dropdown">
					<i class="zmdi zmdi-download"></i>
				</a>
				
				<ul class="dropdown-menu dropdown-menu-right">
					<li>
						<a href="<?php echo base_url(); ?>Project/export/<?php echo $project_responses['project_type'] . '/' .$project_responses['project_acceptanceid'] ; ?>/pdf">Download PDF</a>
					</li>
					<li>
						<a href="<?php echo base_url(); ?>Project/export/<?php echo $project_responses['project_type'] . '/' . $project_responses['project_acceptanceid']; ?>/excel">Download Excel</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
	<div class = "card-body card-padding">
		<?php $this->load->view('Project/fat_table_v', $project_responses); ?>
	</div>
	
</div>
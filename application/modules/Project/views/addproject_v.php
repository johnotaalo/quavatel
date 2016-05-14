<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<?php if(!isset($project_details)){?>
	<input type = "hidden" name = "form_action" value="<?php echo base_url(); ?>Project/addproject"/>
<?php } else {?>
	<input type = "hidden" name = "form_action" value="<?php echo base_url(); ?>Project/editproject"/>
	<input type = "hidden" name = "project_id" value = "<?php echo $project_details->project_id; ?>"/>
<?php } ?>

<div class="form-group">
	<label for="project_name" class="col-sm-3 control-label">Project Name</label>
	<div class="col-sm-9">
		<div class="fg-line">
			<input class="form-control input-sm" id="project_name" placeholder="Project Name" type="text" name = "project_name" value = "<?php if(isset($project_details)){echo $project_details->project_name;}?>">
		</div>
	</div>
</div>
<!-- <div class = "row">
	<div class = "col-md-12">
		<div class="form-group">
			<label for="project_type" class="col-sm-3 control-label">Project Type</label>
			<div class="col-sm-4">
				<div class="fg-line">
					<select name = "project_type" id = "project_type" class = "form-control">
						<?php
							$project_types = array(
									'isp' => "ISP",
									'osp' => "OSP",
									'fat' => "FAT",
									'mss' => "MSS"
							);
							if(isset($project_details))
							{
								$types_options = "";
								foreach ($project_types as $key => $project_type) {
									$types_options .= "<option value = '{$key}'";
									if($project_details->project_type == $key)
									{
										$types_options .= " selected = 'selected'";
									}
									$types_options .= ">{$project_type}</option>";
								}

								echo $types_options;
							}else{
						?>
						<option value="isp">ISP</option>
						<option value="osp">OSP</option>
						<option value="fat">FAT</option>
						<option value="mss">MSS</option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
	</div>
</div> -->
<div class = "row">
	<div class="col-md-12">
		<div class = "col-md-6">
			<div class="form-group">
				<label for="project_startdate" class="col-sm-3 control-label">Start Date</label>
				<div class="col-sm-9">
					<div class="fg-line">
						<input class="form-control input-sm" id="project_startdate" placeholder="Start Date" type="text" name = "project_startdate" value="<?php if(isset($project_details)){echo $project_details->project_startdate;}?>">
					</div>
				</div>
			</div>
		</div>
		<div class = "col-md-6">
			<div class="form-group">
				<label for="project_enddate" class="col-sm-3 control-label">End Date</label>
				<div class="col-sm-9">
					<div class="fg-line">
						<input class="form-control input-sm" id="project_enddate" placeholder="End Date" type="text" name = "project_enddate" value="<?php if(isset($project_details)){echo $project_details->project_enddate;}?>">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	
</script>
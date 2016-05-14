<div class = "pdf-row" >
	<div class = "col-xs-4 pdf-33 pdf-left pull-left pdf-hold-image" style = "height: 100px;">
		<img src = "<?php echo base_url(); ?>assets/logos/quavatel.jpg" style = "max-width: 100%;max-height: 100%"/>
	</div>
	<div class = "col-xs-4 pdf-33 pdf-right pull-right pdf-hold-image" style = "height: 100px;">
		<img src = "<?php echo base_url(); ?>assets/logos/<?php echo $project_responses['company_logo']; ?>" style = "max-width: 100%;max-height: 100%"/>
	</div>
</div>
<div class="text-center" style = "clear: both;">
	<h3>OSP ACCEPTANCE FORM</h3>
</div>

<h4>PROJECT NAME: <?php echo $project_responses['project_name']; ?></h4>
<?php //echo "<pre>";print_r($project_responses);die;?>
<div class = "table-reponsive">
	<table class = "table table-striped">
		<thead>
			<tr>
				<th>SITE NAME:</th>
				<td><?php echo $project_responses['site_name'];?></td>
				<th>LOCATION:</th>
				<td><?php
					$coordinates = explode(";", $project_responses['location']); 
					echo $coordinates[0] . " South" . " " . $coordinates[1] . " East"; 
				?></td>
			</tr>
			<tr>
				<th>FROM:</th>
				<td><?php echo $coordinates[0] . " South"; ?></td>
				<th>TO:</th>
				<td><?php echo $coordinates[1] . " East"; ?></td>
			</tr>
		</thead>
	</table>
</div>


<div class = "table-responsive">
	<table class = "table table-bordered">
		<tr>
			<th>NO</th>
			<th>Description</th>
			<th>Accepted</th>
			<th>Remarks</th>
		</tr>
		<tbody>
			<?php
				$not_questions = array("Date Accepted", "Client Engineer", "Client Engineer Remarks");
				$counter = 0;
				foreach($project_responses['questions'] as $key => $value){
					if(!in_array($key, $not_questions)){
					$counter++;
			?>
			<tr>
				<td><?php echo $counter; ?></td>
				<td><?php echo $key; ?></td>
				<td><?php echo $value['response']; ?></td>
				<td><?php echo $value['remark']; ?></td>
			</tr>
			<?php }}?>
		</tbody>
	</table>
</div>
<p class = "f-20"><span class = "question_title">QTL Engineer: </span><?php echo $project_responses["user_firstname"]. ' ' . $project_responses["user_lastname"]; ?></p>
<p class = "f-20"><span class = "question_title">QTL Engineer Signature: </span>
<br/>
<img src = "<?php if(isset($project_responses['user_image'])){echo $project_responses['user_image'];}else{ echo base_url() . '/assets/img/employee.jpg';} ?>" style = "width: 100px;height:50px;"/>
</p>
<?php foreach ($not_questions as $key => $value){
	if($value != "Client Engineer Remarks"){
?>
<div>
	<p class = "f-20">
		<?php
			if($value != "Client Engineer"){
				echo '<span class = "question_title">' . $value. "</span>: " . $project_responses['questions'][$value]['response'];
			}else{
				echo '<span class = "question_title">' . $project_responses["company_name"] . " Engineer: </span>" . $project_responses["questions"]["Client Engineer"]["response"];
			}
		?>
	</p>
</div>
<?php }
else{
if(!isset($view_type)){?>
				<p class = "f-20"><?php echo $project_responses['company_name']; ?> Engineer Remarks</p>
				<blockquote class="m-b-25">
                                	<p><?php echo $project_responses['questions'][$value]['response']; ?></p>
                            	</blockquote>
                            	<?php } else {?>
                            	<p class = "question_title"><?php echo $project_responses['company_name']; ?> Engineer Remarks</p>
                            	<p class = "question_response"><?php echo $project_responses['questions'][$value]['response'];?></p>
                            	<?php } ?>	
<?php }?> 
<?php } ?>
<pagebreak />
<div class="card-header">
    <h2>Project Images</h2>
</div>
<div class="lightbox row">
	<?php echo $project_images; ?>
</div>


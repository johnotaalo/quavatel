<div class = "pdf-row" >
	<div class = "col-xs-4 pdf-33 pdf-left pull-left pdf-hold-image" style = "height: 100px;">
		<img src = "<?php echo base_url(); ?>assets/logos/quavatel.jpg" style = "max-width: 100%;max-height: 100%"/>
	</div>
	<div class = "col-xs-4 pdf-33 pdf-right pull-right pdf-hold-image" style = "height: 100px;">
		<img src = "<?php echo base_url(); ?>assets/logos/<?php echo $project_responses['company_logo']; ?>" style = "max-width: 100%;max-height: 100%"/>
	</div>
</div>
<div class="table-responsive" style = "clear: both;">
			<table class = "table">
				<tr style = "text-align: center;">
					<td class="f-20 head" colspan = "4">ISP ACCEPTANCE FORM</td>
				</tr>
				<tr class = "project-details">
					<td class="f-500" colspan = "2">PROJECT NAME: <?php echo $project_responses['project_name']; ?></td>
					<td class="f-500" colspan = "2">ACCEPTANCE DATE: <?php echo date('d F Y', strtotime($project_responses['questions']['Date Accepted']['response'])); ?>		</td>
				</tr>
				<tr class = "project-details">
					<td class="f-500" colspan = "2">SITE NAME: <?php echo $project_responses['site_name']; ?></td>
					<td colspan = "2">LOCATION: <?php
						$coordinates = explode(";", $project_responses['location']); 
						echo "<strong>From: </strong>" . $coordinates[0] . "<strong> To: </strong> " . " " . $coordinates[1] ; 
					?></td>
				</tr>
			</table>
			
			<div class = "m-t-10">
				<table class = "table table-bordered pdf-table">
					<tr>
						<th>Question</th>
						<th>Response</th>
						<th>Comments</th>
					</tr>
					<?php
						$not_questions = array("Date Accepted", "Client Engineer", "Client Engineer Remarks");
						foreach($project_responses['questions'] as $key => $value)
						{
							if(!in_array($key, $not_questions)){
								echo "
								<tr>
									<td colspan = '1'>{$key}</td>
									<td colspan = '1'>{$value['response']}</td>
									<td colspan = '2'>{$value['remark']}</td>
								</tr>
							";
							}
						}
					?>
				</table>
			</div>
			<p class = "f-20"><span class = "question_title">Quavatel Engineer: </span><?php echo $project_responses["user_firstname"]. ' ' . $project_responses["user_lastname"]; ?></p>
			<p class = "f-20"><span class = "question_title">QTL Engineer Signature: </span>
			<br/>
			<img src = "<?php if(isset($project_responses['user_image'])){echo $project_responses['user_image'];}else{ echo base_url() . '/assets/img/employee.jpg';} ?>" style = "width: 100px;height:50px;"/>
			</p>
			<?php foreach ($not_questions as $key => $value){
				if($value != "Date Accepted"){
			?>
			<div>
				<?php if($value == "Client Engineer"){?>
				<p class = "f-20"><span class="question_title"><?php echo $project_responses['company_name']; ?> Engineer: </span><?php echo  $project_responses['questions']['Client Engineer']['response'];?></p>
				<?php }else{ ?>
				<?php if(!isset($view_type)){?>
				<p class = "f-20"><?php echo $project_responses['company_name']; ?> Engineer Remarks</p>
				<blockquote class="m-b-25">
                                	<p><?php echo $project_responses['questions'][$value]['response']; ?></p>
                            	</blockquote>
                            	<?php } else {?>
                            	<p class = "question_title"><?php echo $project_responses['company_name']; ?> Engineer Remarks</p>
                            	<p class = "question_response"><?php echo $project_responses['questions'][$value]['response'];?></p>
                            	<?php } ?>
                            	<?php } ?>
			</div>
			<?php } } ?>
		</div>
<pagebreak />
<div class="card-header">
    <h2>Project Images</h2>
</div>
<div class="lightbox row">
	<?php echo $project_images; ?>
</div>
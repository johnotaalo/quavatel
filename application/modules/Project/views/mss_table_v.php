<?php //echo "<pre>";print_r($project_responses);die;?>
<div class = "pdf-row" >
	<div class = "col-xs-4 pdf-33 pdf-left pull-left pdf-hold-image" style = "height: 100px;">
		<img src = "<?php echo base_url(); ?>assets/logos/quavatel.jpg" style = "max-width: 100%;max-height: 100%"/>
	</div>
	<div class = "col-xs-4 pdf-33 pdf-right pull-right pdf-hold-image" style = "height: 100px;">
		<img src = "<?php echo base_url(); ?>assets/logos/<?php echo $project_responses->company_logo; ?>" style = "max-width: 100%;max-height: 100%"/>
	</div>
</div>
<div class = "pdf-center text-center" style = "clear:both;">
	<h3>MANHOLE INFORMATION SHEET</h3>
</div>
<table class = "table table-bordered">
	<tr>
		<th colspan = "4">MANHOLE DETAILS</th>
	</tr>
	<tr>
		<th style = "width: 20%;">Manhole Ref: </th>
		<td colspan = "3"><?php echo $project_responses->project_name; ?></td>
	</tr>
	<tr>
		<th>Location/Address: </th>
		<td colspan = "3"><?php echo $project_responses->site_name; ?></td>
	</tr>
	<tr>
		<?php 
			$locations = explode(';', $project_responses->project_location);
			$latitude = $locations[0] . " South";
			$longitude = $locations[1] . " East";
		?>
		<th>Latitude:</th>
		<td><?php echo $latitude; ?></td>
		
		<th>Longitude:</th>
		<td><?php echo $longitude; ?></td>
	</tr>
</table>
	<!-- hard stuff comes here -->
	<h4>DUCTS</h4>
	<?php if(!isset($view_type)) {?>
	<div class = "ducts_container" style = "width: 100%; height: 300px; border: 1px solid black;">
		<div class = "first-portion" style = "width: 30%;position: relative;margin-left:0;">
			<div class = "inner-portion" style = "position: absolute;height:100%; width: 100%;">
				<div class = "upper-portion" style = "width: 100%;height: 250px;">
					
				</div>
				<div class = "manhole-type" style = "width: 90%;height: 50px;border-top:2px solid black;border-right: 2px solid black;bottom:0;"><strong>Manhole Type: </strong> <?php echo $project_responses->mssduct_manhole;?><br/>
				<strong>Remarks: </strong> <?php if($project_responses->mssduct_remarks == NULL || $project_responses->mssduct_remarks == "NULL" || $project_responses->mssduct_remarks == "Null"){$project_responses->mssduct_remarks = "Not Available";} echo $project_responses->mssduct_remarks;?>
				</div>
			</div>
		</div>
		<div class = "second-portion" style = "width: 40%;position: relative;margin-left:30%;">
			
			<div class = "upper-duct" style = "width: 100%;padding: 10px;margin: 10px 0;height:20px;border:1px solid black;"></div>
			<div class = "main-ducts" style = "width: 100%;padding: 10px;height:210px;background-color: lightblue;margin: 10px 0;">
				<div class = "outer_top" style = "position: absolute;border: 1px solid black;top:45%;left:-70%;right: 110%;text-align: center;background:white;height:20px;"></div>
				<div class = "outer_bottom" style = "position: absolute;border: 1px solid black;top:45%;left:110%;right: -70%;text-align: center;background:white;height:20px;"></div>
				
				<div class = "middle-part" style = "position: absolute;border: 3px solid black;padding: 4px;top:35%;right:30%;left:30%;bottom:35%;"></div>
				<div class = "top-duct" style = "position: absolute;border: 1px solid black;top:20%;left:45%;right:45%;text-align: center;background:white;"><?php echo $project_responses->mssduct_top;?></div>
				<div class = "left-duct" style = "position: absolute;border: 1px solid black;top:45%;left:10%;right: 80%;text-align: center;background:white;"><?php echo $project_responses->mssduct_left;?></div>
				
				<div class = "right-duct" style = "position: absolute;border: 1px solid black;top:45%;right:10%;left: 80%;text-align: center;background:white;"><?php echo $project_responses->mssduct_right;?></div>
				<div class = "bottom-duct" style = "position: absolute;border: 1px solid black;bottom:20%;left:45%;right:45%;text-align: center;background:white;"><?php echo $project_responses->mssduct_bottom;?></div>
			</div>
			<div class = "lower-duct" style = "width: 100%;padding: 10px;margin: 10px 0;height:20px;border:1px solid black;"></div>		
		</div>
		<div class = "third-portion" style = "width: 30%;position: relative;margin-left:70%;"></div>
	</div>
	<?php } else if ($view_type == "pdf"){ ?>
	<div class = "ducts-container">
		<div>
			<p class = "top_duct"><?php echo $project_responses->mssduct_top;?></p>
			<div style = "display: block;">
				<div class = "left_duct">
					<span><?php echo $project_responses->mssduct_left;?></span>
				</div>
				<div class = "right_duct">
					<span><?php echo $project_responses->mssduct_right;?></p>
				</div>
			</div>
			<p class = "bottom_duct"><?php echo $project_responses->mssduct_bottom;?></p>
		</div>
		<div>
			<h5>Manhole Type</h5>
			<p><?php echo $project_responses->mssduct_manhole;?></p>
			<h5>MIS Duct Remarks</h5>
			<p><?php if($project_responses->mssduct_remarks == NULL || $project_responses->mssduct_remarks == "NULL" || $project_responses->mssduct_remarks == "Null"){$project_responses->mssduct_remarks = "Not Available";}echo $project_responses->mssduct_remarks;?></p>
		</div>
	</div>
	
	<?php } ?>
	<h4>FIBRE OPTIC CABLES</h4>
	<?php if(!isset($view_type)) {?>
	<div class = "foc" style = "width: 100%; height: 300px; border: 1px solid black;">
		<div class = "foc_left" style = "width: 40%; height: 300px;float:left;position:relative;">
			<div class = "nofj" style = "text-align: center;vertical-align: middle;position: absolute;bottom: 0;right: 5%;left: 0;padding:15px;border-right: 2px solid black;border-top: 2px solid black;">
				<div class = "nofj_directive" style = "width: 80%;float: left; border-right: 1px solid black;">
					Number of Fibre Jointing Enclosures in manhole
				</div>
				<div class = "nofj_number" style = "width: 20%;float:left;vertical-align: middle;"><p><?php echo $project_responses->mssfibre_enclosures; ?></p></div>
			</div>
		</div>
		<div class = "foc_right" style = "width: 60%; height: 300px;float:left;background-color: lightblue;color:black;">
			
			<div class = "ducts_top" style = "width: 100%;height: 50px;text-align:center;vertical-align: middle;padding:15px;">
				<center>
				
				<?php
					$mssfibre_top = $project_responses->mssfibre_top;
					$top_fibres = explode(',', $mssfibre_top);
					
					if(count($top_fibres) > 0)
					{
						foreach($top_fibres as $fibre)
						{
							echo '<a style = "padding:2px 10px;border:1px solid black;display:inline;margin: 5px;">'.$fibre.'</a>';
						}
					}
				?>
				</center>
			</div>
			<div class = "ducts_middle" style = "width: 100%;height: 200px;">
				<div class = "first_part" style = "width: 20%;  min-height: 100%;float:left;padding: 10px;text-align:center;vertical-align: middle;display: table-cell;">
					<?php
						$mssfibre_left = $project_responses->mssfibre_left;
						$left_fibres = explode(',', $mssfibre_left);
						
						if(count($left_fibres) > 0)
						{
							foreach($left_fibres as $fibre)
							{
								echo '<a style = "width: 100%;padding:2px;border:1px solid black;display:block;margin: 5px;">'.$fibre.'</a>';
							}
						}
					?>
				</div>
				<div style = "width: 60%; min-height: 100%;float:left;">
				</div>
				<div class = "last_part" style = "width: 20%; min-height: 100%;float:left;padding: 10px;text-align:center;vertical-align: middle;display: table-cell;">
					<?php
						$mssfibre_right = $project_responses->mssfibre_right;
						$right_fibres = explode(',', $mssfibre_right);
						
						if(count($right_fibres) > 0)
						{
							foreach($right_fibres as $fibre)
							{
								echo '<a style = "width: 100%;padding:2px;border:1px solid black;display:block;margin: 5px;">'.$fibre.'</a>';
							}
						}
					?>
				</div>
			</div>
			<div class = "ducts_bottom" style = "width: 100%;height: 50px;text-align:center;vertical-align: middle;padding:15px;">
					<?php
						$mssfibre_bottom = $project_responses->mssfibre_bottom;
						$bottom_fibres = explode(',', $mssfibre_bottom);
						
						if(count($bottom_fibres) > 0)
						{
							foreach($bottom_fibres as $fibre)
							{
								echo '<a style = "padding:2px 10px;border:1px solid black;display:inline;margin: 5px;">'.$fibre.'</a>';
							}
						}
					?>
			</div>
		</div>
	</div>
	<?php } else if ($view_type == "pdf"){ ?>
		<div class = "ducts-container">
			<p class = "top_duct"><?php echo $project_responses->mssfibre_top;?></p>
			<div style = "display: block;">
				<div class = "left_duct">
					<span><?php echo $project_responses->mssfibre_left;?></span>
				</div>
				<div class = "right_duct">
					<span><?php echo $project_responses->mssfibre_right;?></p>
				</div>
			</div>
			<p class = "bottom_duct"><?php echo $project_responses->mssfibre_bottom;?></p>
			<center>
				<p><strong>No. of Fibre Jointing Enclosures in manhole:</strong> <?php echo $project_responses->mssfibre_enclosures; ?></p>
			</center>
		</div>
	<?php } ?>
	<!-- hard stuff end here -->
	<pagebreak />
<table class = "table table-bordered">
	<tr>
		<th colspan = "5">SPLICING INFORMATION</th>
	</tr>
	<tr>
		<th style = "">No.</th>
		<th style = "">Entry</th>
		<th style = "">Cable Type</th>
		<th style = "">Exit</th>
		<th style = "">Cable Type</th>
	</tr>
	<?php
		$enclosures = explode(':', $project_responses->mssfibre_enclosures_details);
		$counter = 1;
		foreach ($enclosures as $key => $value) {
			echo "<tr>";
				echo "<td>{$counter}</td>";
				$enclosure_data = explode(";", $value);
			
				foreach ($enclosure_data as $k => $v) {
					
					echo "<td>{$v}</td>";
				}
				$counter++;
			echo "</tr>";
		}
	?>
	</table>
	<table class = "table table-bordered">
	<tr>
		<th colspan="2">Suveyor Name:</th>
		<td colspan="3"><?php echo $project_responses->user_lastname . " " . $project_responses->user_firstname; ?></td>
	</tr>
	<tr>
		<th colspan="2">Survey Date:</th>
		<td colspan="3"><?php echo date('dS F, Y', strtotime($project_responses->project_startdate)); ?></td>
	</tr>
	<tr>
		<th colspan="2">Survey Signature:</th>
		<td colspan="3"><img src = "<?php if(isset($project_responses->user_image)){echo $project_responses->user_image;}else{echo base_url() . '/assets/img/employee.jpg';} ?>" style = "width:100px;height:50px;"/></td>
	</tr>
	</table>
</table>
<pagebreak />
<div class="card-header">
    <h2>Manhole Pictures</h2>
</div>
<div class="lightbox row">
	<?php echo $project_images; ?>
</div>
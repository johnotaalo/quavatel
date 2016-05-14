<style>
	p
	{
		margin-bottom: 5px;
	}
</style>
<div style = "background-color: white; padding: 10px;">
	<legend>Filter using the options below</legend>
	<div class = "row">
		<form action = "<?php echo base_url(); ?>LabourSheet/filter" method = "POST">
			<div class = "col-md-12">
				<div class="col-sm-3">
					<p class="f-500 c-black">Filter By: </p>
					
					<select class="selectpicker" name = "filter_by">
						<option>People</option>
						<option>Projects</option>
					</select>
				</div>
				<div class="col-sm-3">
					<p class="f-500 c-black" id = "projects">Projects: </p>
					<select class="selectpicker" name = "projects" data-live-search="true">
						<?php echo $projects_dropdown; ?>
					</select>
				</div>
				<div class = "col-sm-6">
					<center><p class="f-500 c-black" id = "projects">Date Range: </p><center>
					<div class="col-sm-6">
						<div class="input-group form-group">
							<div class="dtp-container fg-line">
								<input name = "date_from" type='text' class="form-control date-picker date_from" data-date-format="DD-MM-YYYY">
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="input-group form-group">
							<div class="dtp-container fg-line">
								<input name = "date_to" type='text' class="form-control date-picker date_to" data-date-format="DD-MM-YYYY">
							</div>
						</div>
					</div>
				</div>
				<div style = "padding-left: 15px;padding-right: 15px;">
					<button class = "btn bgm-teal">Filter</button>
				</div>
			</div>
		</form>
	</div>
</div>
<div class = "card">
	<div class = "card-body card-padding">
		<table class = "table i-table table-bordered table-responsive" id = "labour_work_sheet">
			<thead>
				<th>No</th>
				<th>Name</th>
				<th>ID NO</th>
				<th>MOBILE NO</th>
				<th>PURPOSE</th>
				<th>PROJECT</th>
				<?php echo $actual_columns; ?>
				<th class = "highlight">TOTAL</th>
				
			</thead>
			<tbody>
				<?php if(is_array($labourer_data)){$counter=1; foreach($labourer_data as $key => $value){?>
					<tr>
					<td><?php echo $counter; ?></td>
					<td><?php echo $value["name"]; ?></td>
					<td><?php echo $value["idno"]; ?></td>
					<td><?php echo $value["mobileno"]; ?></td>
					<td><?php echo $value["purpose"]; ?></td>
					<td><?php echo $value["project"]; ?></td>
					<?php
						foreach($day_columns as $dates){
							if(!array_key_exists($dates['date'], $value['wages']))
							{
								echo '<td>-</td>';
							}
							else
							{
								echo '<td data-value = "'.$value['wages'][$dates['date']]['amount'].'" class = "rowDataSd"><b>'.$value['wages'][$dates['date']]['amount'].'</b></td>';
							}
							
							
						}
						//echo $total;die;
						echo '<td data-value = "'.$value["total_wages"]. '" class = "highlight rowDataSd"><b>'.number_format($value["total_wages"]). '</b></td>';
					?>
					
					</tr>
				<?php $counter++; }} ?>

				<tr class = "totalColumn">
					<td colspan = "6"></td>
					<?php
						//echo "<pre>";print_r($daily_total);die;
						foreach($day_columns as $dates){
							echo "<td class = 'totalCol'></td>";
							/*if(!array_key_exists($dates['date'], $value['wages']))
							{
								//echo $dates['date'] . '<br/>';
								echo '<td>-</td>';
							}
							else
							{
								//echo "Found " . $dates['date'] . '<br />';
								echo '<td><b>'.number_format($daily_total[$dates['date']]).'</b></td>';
							}*/
						}
					?>
					<td class = 'totalCol'></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<button class="btn btn-float bgm-red m-btn waves-effect waves-circle waves-float"><i class="zmdi zmdi-print"></i></button>
<script>
<?php
	$zeros_array = array();
	foreach($day_columns as $dates){
		$zeros_array[] = 0;
	}
?>
var totals=[<?php echo implode(',', $zeros_array); ?>, 0];

$(document).ready(function(){

    var $dataRows=$("#labour_work_sheet tr:not('.totalColumn, .titlerow')");
    $dataRows.each(function() {
        $(this).find('.rowDataSd').each(function(i){
        	var $value =  $(this).attr('data-value');
        	if(isNaN($value))
        	{
        		$value = 0;
        	}       
            totals[i]+=parseInt($value);
            console.log(i +"=>"+totals[i]);
        });
        
    });
    console.log(totals);
    $("#labour_work_sheet td.totalCol").each(function(i){  
        $(this).html("total:"+totals[i]);
    });

});
</script>
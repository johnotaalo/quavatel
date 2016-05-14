<div class = "card">
	<div class = "card-header ch-alt">
		<h2><?php echo $labourer_details["first_name"] . " ".  $labourer_details["last_name"]; ?> Wage Details</h2>
		<ul class="actions">
		        <li class="dropdown">
		            <a href="#" data-toggle="dropdown">
		                <i class="zmdi zmdi-download"></i>
		            </a>
		            
		            <ul class="dropdown-menu dropdown-menu-right">
		                <li>
		                    <a href="<?php echo base_url(); ?>LabourSheet/export/labourdetails/excel/<?php echo $from; ?>/<?php echo $to; ?>/<?php echo $id; ?>">Export To Excel</a>
		                </li>
		                <li>
		                    <a href="<?php echo base_url(); ?>LabourSheet/export/labourdetails/pdf/<?php echo $from; ?>/<?php echo $to; ?>/<?php echo $id; ?>">Export to PDF</a>
		                </li>
		            </ul>
		        </li>
		    </ul>
	</div>
	<div class = "card-body card-padding">
		<div id = "profile-main" style = "min-height: 10px !important;">
			<div class="pmb-block">
				<div class="pmbb-header">
					<h2><i class="zmdi zmdi-account m-r-5"></i> Basic Information</h2>
				</div>
				<div class="pmbb-body p-l-30">
					<div class="pmbb-view">
						<dl class="dl-horizontal">
							<dt>Full Name</dt>
							<dd><?php echo $labourer_details["last_name"] . ", " . $labourer_details["first_name"] . " " . $labourer_details["other_name"]; ?></dd>
						</dl>
						<dl class="dl-horizontal">
							<dt>ID Number</dt>
							<dd><?php echo $labourer_details["idno"]; ?></dd>
						</dl>
						<dl class="dl-horizontal">
							<dt>Mobile No: </dt>
							<dd><?php echo $labourer_details["mobileno"]; ?></dd>
						</dl>
						<dl class="dl-horizontal">
							<dt>Last Worked: </dt>
							<dd><?php echo $labourer_details["latest"]; ?></dd>
						</dl>
					</div>
				
				</div>
			</div>
		</div>
		<hr/>
		
		<table class = "table table i-table m-t-25 m-b-25">
			<thead class = "text-uppercase">
				<tr>
					<th class = "c-gray">No</th>
					<th class = "c-gray">Date</th>
					<th class = "c-gray">Supervisor</th>
					<th class = "c-gray">Project Name</th>
					<th class = "c-gray">Purpose</th>
					<th class = "highlight">Wage Amount</th>
				</tr>
			</thead>
			<thead>
				<?php
					$counter = 1;
					foreach($labourer_details['wages'] as $value)
					{
						echo "<tr>";
						echo "<td>{$counter}</td>";
						echo "<td>{$value['date']}</td>";
						echo "<td>{$value['supervisor']}</td>";
						echo "<td>{$value['project']}</td>";
						echo "<td>{$value['task']}</td>";
						echo "<td class = 'highlight'>Ksh. {$value['amount']}</td>";
						echo "</tr>";
						$counter++;
					}
					
					echo "<tr>";
					echo "<td colspan = '5'></td>";
					echo "<td class = 'highlight'>Ksh. {$labourer_details["wage_total"]}</td>";
					echo "</tr>";
				?>
			</thead>
		</table>
	</div>
</div>
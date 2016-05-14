<h3><?php echo $title; ?></h3>
<hr>
<table class = "table">
	<tbody>
		<tr>
			<th>NAME: </th>
			<td><?php echo strtoupper($pdf_data["name"])?></td>
		</tr>
		<tr>
			<th>ID NUMBER: </th>
			<td><?php echo strtoupper($pdf_data["idno"])?></td>
		</tr>
		<tr>
			<th>MOBILE NUMBER: </th>
			<td><?php echo strtoupper($pdf_data["mobileno"])?></td>
		</tr>
	</tbody>
</table>

<h4 class = "p-5 m-0">DAYS WORKED</h4>
<p><b>Range: </b><?php echo date('jS F Y', strtotime($pdf_data['from'])); ?> To <?php echo date('jS F Y', strtotime($pdf_data['to'])); ?></p>

<table class = "table p-10 table-bordered">
	<thead>
		<tr>
			<th>NO</th>
			<th>DATE</th>
			<th>SUPERVISORS</th>
			<th>PURPOSE</th>
			<th>PROJECT</th>
			<th>AMOUNT</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$counter = 0;
			foreach ($pdf_data["week_details"] as $key => $value) {
				$counter++;
				echo "<tr>";
				echo "<td>{$counter}</td>";
				echo "<td>" . date("d/m/Y" , strtotime($key)). "</td>";
				echo "<td>" . $value["supervisors"] . "</td>";
				echo "<td>{$value['task']}</td>";
				echo "<td>" . strtoupper($value['project']) . "</td>";
				echo "<td>Ksh. " . number_format($value['total_wage']) ."</td>";
				echo "</tr>";
			}
		?>
		<tr class = "last">
			<td colspan="5" class = "hanging align-right item-bold">Total</td>
			<td class="item-bold"><?php echo "Ksh. " . number_format($pdf_data["total_wage"]); ?></td>
		</tr>
	</tbody>
</table>
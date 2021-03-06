<?php if(isset($type) && $type == "pdf"){?>
	<h2>Labour Sheet for: <?php echo date('d/m/Y', strtotime($date)); ?></h2>
<?php } ?>
<table class = "table table-bordered pdf-table pdf-table-m-10">
	<thead>
		<tr>
			<th>No.</th>
			<th>Full Name</th>
			<th>ID</th>
			<th>PROJECT</th>
			<th>TYPE OF WORK</th>
			<th>CELL PHONE NUMBER</th>
			<th>SUPERVISOR</th>
			<th>WAGE</th>
			<?php 
				$permission = $this->M_Account->get_user_permission('user_id', $this->session->userdata('user_id'));
				if($type != "pdf" && ($permission == "admin" || $permission == "finance")){
					echo "<th>ACTION</th>";
				} 
			?>
		</tr>
	</thead>
	<tbody>
		<?php echo $daily_table; ?>
		<?php if(isset($type) && $type == "pdf"){?>
		<tr class = "total-row">
			<td class = "total-cell-title" colspan = "7">TOTAL</td>
			<td class = "total-cell-value">Ksh. <?php echo number_format($daily_total);?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php if(isset($type) && $type == "pdf"){?>
<div class = "prep">
	<p><b><u>Generated By: </u></b></p>
	<p><?php echo $perpared_by; ?></p>
</div>
<?php } ?>

<?php if(!isset($type) && $type != "pdf"){?>
<!-- Modal Small -->	
<div class="modal fade" id="modalNarrower" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Modal title</h4>
        </div>
        <div class="modal-body">
            <form method = "POST" id = "modal_form"></form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-link" id = "save_button">Save changes</button>
            <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
</div>
<?php } ?>

<script type="text/javascript">
	$('.call_modal').click(function(){
		$('#save_button').text = "Save Changes";
		get_request($(this).attr("data-href"), function(data){
			obj = jQuery.parseJSON(data);
			console.log(obj);
			$('#modalNarrower').modal("show");
			$('#modalNarrower .modal-title').text(obj.details.title);
			$('#modalNarrower .modal-body form').html(obj.view);
		});
	});

	$('#save_button').click(function(){
		$("#modal_form").attr("action", $("input[name='form_action']").val());
		$("#modal_form").submit();
	});
	function get_request(url, handleData)
	{
		$.get(url, function(data){
			handleData(data);
		});
	}
</script>
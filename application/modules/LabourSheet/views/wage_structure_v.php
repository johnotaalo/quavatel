<div class="card">
	<div class="card-header ch-alt m-b-20">
	    <h2>Wage Structure<small>Manage the wage structure</small></h2>
	    <ul class="actions">
	        <li class="dropdown">
	            <a href="#" data-toggle="dropdown">
	                <i class="zmdi zmdi-download"></i>
	            </a>
	            
	            <ul class="dropdown-menu dropdown-menu-right">
	                <li>
	                    <a href="<?php echo base_url(); ?>LabourSheet/export/wagestructure/excel">Export To Excel</a>
	                </li>
	                <li>
	                    <a href="<?php echo base_url(); ?>LabourSheet/export/wagestructure/pdf">Export to PDF</a>
	                </li>
	            </ul>
	        </li>
	    </ul>
	    
	    <button data-url = "<?php echo base_url(); ?>LabourSheet/addstruct" id = "add-button" class="btn bgm-teal btn-float waves-effect"><i class="zmdi zmdi-plus"></i></button>
	</div>
	
	<div class="card-body card-padding">
		<?php $data["wage_structure_table"] = $wage_structure_table; ?>
		<?php $this->load->view('LabourSheet/wage_structure_table_v', $data["wage_structure_table"]); ?>
	</div>
</div>

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

<script>
	$(document).ready(function(){
		var table = $("table");
		var edit_button = $(".call_modal");
		var save_button = $('#save_button');
		var add_button = $('#add-button');
		var delete_task = $('.delete_task');
		
		edit_button.css('cursor', 'pointer');
		table.DataTable();
		
		add_button.click(function(){
			save_button.text = "Add New";
			get_request($(this).attr("data-url"), function(data){
				obj = jQuery.parseJSON(data);
				$('#modalNarrower').modal("show");
				$('#modalNarrower .modal-title').text("Add New Task To Wage Structure");
				$('#modalNarrower .modal-body form').html(obj.view);
			});
		});
		edit_button.click(function(){
			save_button.text = "Save Changes";
			get_request($(this).attr("data-url"), function(data){
				obj = jQuery.parseJSON(data);
				$('#modalNarrower').modal("show");
				$('#modalNarrower .modal-title').text(obj.details.wagestructure_task);
				$('#modalNarrower .modal-body form').html(obj.view);
			});
		});
		
		save_button.click(function(){
			$("#modal_form").attr("action", $("input[name='form_action']").val());
			$("#modal_form").submit();
		});
		
		delete_task.click(function(event){
			var confirmed = confirm("Are you sure you want to delete this task?");
			
			if(confirmed == false)
			{
				event.preventDefault();
			}
		});
	});
	
	function get_request(url, handleData)
	{
		$.get(url, function(data){
			handleData(data);
		});
	}
</script>
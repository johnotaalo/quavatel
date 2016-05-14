<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<?php

	$user_persmission = $this->M_Account->get_user_permission('user_id', $this->session->userdata('user_id'));
?>
<div class="card">
	<div class="card-header ch-alt m-b-20">
	    <h2>Project List<small>Manage projects</small></h2>
	    <ul class="actions">
	    	<?php if($user_persmission == "admin"){ ?>
	    	<li>
	    		<a href = "<?php echo base_url();?>Project/awaiting" title = "Awaiting Approval"><i class = "zmdi zmdi-check-all"></i></a>
	    	</li>
	    	<li>
	    		<a href = "<?php echo base_url();?>Project/deleted"><i class = "zmdi zmdi-rotate-cw"></i></a>
	    	</li>

	    	<?php } ?>
	        <li class="dropdown">
	            <a href="#" data-toggle="dropdown">
	                <i class="zmdi zmdi-download"></i>
	            </a>
	            
	            <ul class="dropdown-menu dropdown-menu-right">
	                <li>
	                    <a href="<?php echo base_url(); ?>Project/export_projects/excel">Export To Excel</a>
	                </li>
	                <li>
	                    <a href="<?php echo base_url(); ?>Project/export_projects/pdf">Export to PDF</a>
	                </li>
	            </ul>
	        </li>
	    </ul>
	    
	    <button data-url = "<?php echo base_url(); ?>Project/addproject" id = "add-button" class="btn bgm-teal btn-float waves-effect"><i class="zmdi zmdi-plus"></i></button>
	</div>
	
	<div class="card-body card-padding">
		<div class = "row">
			Show: <select id = "project_status">
				<option value = "" selected>All Projects</option>
				<option value = "Ongoing">Ongoing Projects</option>
				<option value = "Completed">Completed Projects</option>
				<option value = "Awaiting Acceptance">Awaiting Acceptance</option>
			</select>
		</div>
		<?php $data["projects_table"] = $projects_table; ?>
		<?php $this->load->view('LabourSheet/manage_project_table_v', $data["projects_table"]); ?>
	</div>
</div>

<div class="modal fade" id="modalNarrower" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
	    <div class="modal-content">
	        <div class="modal-header">
	            <h4 class="modal-title">Modal title</h4>
	        </div>
	        <div class="modal-body">
	            <form method = "POST" id = "modal_form" class = "form-horizontal"></form>
	        </div>
	        <div class="modal-footer">
	            <button type="button" class="btn btn-link" id = "save_button">Save changes</button>
	            <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
	        </div>
	    </div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		var table = $("table");
		var addbutton = $("#add-button");
		var save_button = $('#save_button');
		var edit_button = $('.edit-button');
		var delete_button = $('.delete-button');

		table.DataTable();
		addbutton.click(function(){
			save_button.text = "Add New";
			get_request($(this).attr("data-url"), function(data){
				obj = jQuery.parseJSON(data);
				$('#modalNarrower').modal("show");
				$('#modalNarrower .modal-title').text("Add New Project");
				$('#modalNarrower .modal-body form').html(obj.view);
				$('#project_startdate').datepicker();
				$('#project_enddate').datepicker();
			});
		});

		save_button.click(function(){
			$("#modal_form").attr("action", $("input[name='form_action']").val());

			var project_name = $("input[name = 'project_name']").val();
			var start_date = $("input[name = 'project_startdate']").val();
			var end_date = $("input[name = 'project_enddate']").val();

			if(project_name === "" || start_date === "" || end_date === "")
			{
				alert("Please fill in all the fields");
			}
			else if(/^[a-zA-Z0-9- ]*$/.test(project_name) == false)
			{
				alert("There are invalid characters in the project name");
				$("input[name = 'project_name']").focus();
			}
			else
			{
				$("#modal_form").submit();
			}
		});

		edit_button.click(function(){
			save_button.text("Save Changes");
			get_request($(this).attr("data-url"), function(data){
				obj = jQuery.parseJSON(data);
				$('#modalNarrower').modal("show");
				$('#modalNarrower .modal-title').text("Edit: " + obj.project_details.project_name + " Project");
				$('#modalNarrower .modal-body form').html(obj.view);
				$('#project_startdate').datepicker();
				$('#project_enddate').datepicker();
			});
		});

		delete_button.click(function(event){
			event.preventDefault();

			var response = confirm("You are about to delete a project. Only the administrator can recover the projects. Are you sure?");

			if(response == true)
			{
				window.location = $(this).attr('href');
			}
		});

		$('#project_status').change(function(){
			var search_text = $('.dataTables_filter input');
			search_text.val($(this).val()).keyup();
		});
	});

	function get_request(url, handleData)
	{
		$.get(url, function(data){
			handleData(data);
		});
	}
</script>
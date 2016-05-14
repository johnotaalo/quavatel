<div class="card">
	<div class="card-header ch-alt m-b-20">
		<h2>User Accounts <small>Manage all the users of the system from here</small></h2>
	
		<button class="btn bgm-red btn-float waves-effect custom-anchor" data-href = "<?php echo base_url(); ?>Account/createuser"><i class="zmdi zmdi-account-add"></i></button>
	</div>
	
	<div class="card-body card-padding">
		<div class = "table-responsive">
			<table class = "table table-bordered table-striped">
				<thead>
					<tr>
						<th>#</th>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Email Address</th>
						<th>Signature</th>
						<th>Activation</th>
						<th>Reset Password</th>
						<th>Edit</th>
						<th>Delete</th>
					</tr>
				</thead>
				<tbody>
					<?php echo $user_table; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	$('table').DataTable();
	$('button.custom-anchor').click(function(){
		window.location.href = $(this).attr("data-href");
	});
	
	$('a.delete_user').click(function(event){
		var r = confirm("Are you sure you want to delete this user?");
		if(r !== true)
		{
			event.preventDefault();
		}
	});
});
</script>
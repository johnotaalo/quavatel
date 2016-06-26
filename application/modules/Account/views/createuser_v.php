<?php $error = $this->session->flashdata('error'); ?>
<div class="card">
	<div class="card-header">
		<h2>Create User <small>Add a new user to the system. Make sure you fill all the fields before clicking on the submit button</small></h2>
	</div>

	<div class="card-body card-padding">
		<form method = "POST" action = "<?php echo base_url(); ?>Account/<?php if(isset($user_details)){?>action/edit/<?php echo $user_details->user_id; } else { ?>adduser <?php } ?>" id = "register_user" enctype = "multipart/form-data">
			<div class="row">
				<div class="col-xs-8">
					<div class="input-group fg-float">
						<span class="input-group-addon"><i class="zmdi zmdi-account"></i></span>
						<div class="fg-line">
							<input type="text" class="form-control" name = "user_firstname" required value = "<?php if ($error){echo $this->session->flashdata('user_firstname');}elseif(isset($user_details)){ echo $user_details->user_firstname; } ?>">
							<label class="fg-label">First Name</label>
						</div>
					</div>
				</div>
			</div>
			<br/><br/>
			<div class = "row">
				<div class="col-xs-8">
					<div class="input-group fg-float">
						<span class="input-group-addon"><i class="zmdi zmdi-account"></i></span>
						<div class="fg-line">    
							<input type="text" class="form-control" name = "user_lastname" required value = "<?php if ($error){echo $this->session->flashdata('user_lastname');}elseif(isset($user_details)){ echo $user_details->user_lastname; } ?>">
							<label class="fg-label">Last Name</label>
						</div>
					</div>
				</div>
			</div>
			<br/><br/>
			<div class = "row">
				<div class="col-xs-8">

					<div class="input-group fg-float mail">
						<span class="input-group-addon"><i class="zmdi zmdi-email"></i></span>
						<div class="fg-line">
							<input type="email" class="form-control" name = "user_emailaddress" value = "<?php if ($error){echo $this->session->flashdata('user_emailaddress');}elseif(isset($user_details)){ echo $user_details->user_emailaddress; } ?>">
							<label class="fg-label">Email Address</label>
						</div>
						
					</div>
					<p style = 'color:red' id = "error_message"><?php echo $this->session->flashdata('error'); ?></p>
				</div>
			</div>
			<div class = "row">
				<div class="col-xs-8">

					<div class="input-group fg-float phone">
						<span class="input-group-addon"><i class="zmdi zmdi-phone"></i></span>
						<div class="fg-line">
							<input type="text" class="form-control" name = "user_phonenumber" value = "<?php if ($error){echo $this->session->flashdata('user_phonenumber');}elseif(isset($user_details)){ echo $user_details->user_phonenumber; } ?>">
							<label class="fg-label">Phone Number</label>
						</div>
						
					</div>
					<p style = 'color:red' id = "error_message"><?php echo $this->session->flashdata('error'); ?></p>
				</div>
			</div>
			<div class = "row">
				<div class="col-xs-8">

					<div class="input-group fg-float mail">
						<span class="input-group-addon"><i class="zmdi zmdi-shield-security"></i></span>
						<div class="fg-line">
							<select name = "user_type" class = "selectpicker">
								<?php 
									$user_types = array(
										'project_manager' => "Project Manager",
										'acceptance' => "Access to Acceptance",
										'finance' => "Finance Department",
										'noc_engineer' => "NOC Engineer"
									);

									foreach ($user_types as $value => $name) {
									 	echo "<option value = '{$value}'";
									 	if(isset($user_details) && $user_details->user_type == $value){
									 		echo " selected = 'selected' ";
									 	}
									 	echo ">{$name}</option>";
									 } 
								?>
							</select>
						</div>
						
					</div>
					<p style = 'color:red' id = "error_message"><?php echo $this->session->flashdata('error'); ?></p>
				</div>
			</div>
			<div class = "row">
				<p>Select the User's Image</p>
				<img id="blah" src="<?php if (isset($user_details)){ if($user_details->user_image){echo $user_details->user_image;}else{ echo base_url() . 'assets/img/employee.jpg';} }else{ echo base_url() . 'assets/img/employee.jpg'; } ?>" name = "user_image"/>
				<input type='file' id="imgInp" name = "user_image"/>
				<?php
					$image_error = $this->session->flashdata('image_error');
					if(count($image_error) > 0)
					{
						echo "<p style = 'color: red;'>The image could not be uploaded because of the following errors</p><ol>";
						foreach($image_error as $error)
						{
							echo "<li style = 'color: red;'>{$error}</li>";
						}
						echo "</ol>";
					}
				?>
			</div>
			<br/>
			<button class="btn btn-default btn-icon-text waves-effect submit"><i class="zmdi zmdi-mail-send"></i> <?php if(isset($user_details)){?>Edit User Details<?php } else { ?>Add User <?php } ?></button>
		</div>
	</form>
</div>
<style>
	.red
	{
		border-color: #f6675d !important;
		
	}

	img#blah
	{
		width: 150px;
		height: 150px;
	}
</style>
<script>
	$(document).ready(function(){
		$("#imgInp").change(function(){
			readURL(this);
		});
	});
	function readURL(input) {

		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#blah').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}
</script>
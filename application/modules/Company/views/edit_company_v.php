<div class="card">
	<div class="card-header">
		<h2><?php echo $page_header; ?><small>Tweak a few details if necessary</small></h2>
	</div>

	<div class="card-body card-padding">
		<div class = "row">
			<div class = "col-sm-6 col-xs-12">
				<form method = "POST" action = "<?php echo base_url(); ?>Company/editCompany" enctype="multipart/form-data">
				<div class="form-group fg-float">
					<div class="fg-line">
						<input class="form-control fg-input" type="text" name = "company_name" value = "<?php echo $company_details->company_name; ?>">
						<label class="fg-label">Company Name</label>
					</div>
				</div>
				<div class="form-group">
					<div class="fg-line">
						<textarea class="form-control" rows="5" placeholder="Company Description" name = "company_description"><?php echo $company_details->company_description; ?></textarea>
					</div>
				</div>
				
				<div class = "form-group">
					<div class = "fg-line">
						<input type = "file" name = "company_logo" placeholder = "Select the company's image" />
					</div>
				</div>
				<input type = "hidden" name = "company_id" value = "<?php echo $company_details->company_id; ?>" />
				<button class="btn bgm-blue waves-effect">Edit Company</button>
				</form>
			</div>
			
		</div>
	</div>
</div>
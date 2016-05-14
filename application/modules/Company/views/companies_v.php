<div class="card">
	<div class="card-header ch-alt m-b-20">
		<h2>Company Profiles <small>Manage all the company profiles in the system from here</small></h2>
	
		<a class="btn bgm-red btn-float waves-effect custom-anchor" data-href = "<?php echo base_url(); ?>Company/newcompany"><i class="zmdi zmdi-collection-plus"></i></a>
	</div>
	
	<div class="card-body card-padding">
		<div class="row">
			<?php echo $company_list; ?>
		</div>
	</div>
</div>

<script>
	$('div.card a').click(function(){
		window.location.href = $(this).attr("data-href");
	});
	
	$('.data-delete').click(function(event){
		event.preventDefault();
		var response = confirm("Are you sure you want to delete: "+$(this).attr('data-displayname')+"?!");
		if (response == true) {
			window.location.href = $(this).attr("data-href");
		}
	});
</script>
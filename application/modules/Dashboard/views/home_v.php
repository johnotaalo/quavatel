<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<div class="block-header">
	<h2>Dashboard</h2>
</div>
<div class = "row">
	<div class = "col-sm-3">
		<div class="mini-charts-item bgm-cyan" data-href = "<?php echo base_url(); ?>Project/data/isp">
			<div class="clearfix">
				<div class="chart chart-pie stats-pie">
					4
				</div>
				<div class="count">
					<small>ISP Projects</small>
					<h2><?php echo $project_count['isp']; ?></h2>
				</div>
			</div>
		</div>
	</div>
	
	<div class = "col-sm-3">
		<div class="mini-charts-item bgm-teal" data-href = "<?php echo base_url(); ?>Project/data/osp">
			<div class="clearfix">
				<div class="chart chart-pie stats-pie">
				</div>
				<div class="count">
					<small>OSP Projects</small>
					<h2><?php echo $project_count['osp']; ?></h2>
				</div>
			</div>
		</div>
	</div>
	
	<div class = "col-sm-3">
		<div class="mini-charts-item bgm-red" data-href = "<?php echo base_url(); ?>Project/data/fat">
			<div class="clearfix">
				<div class="chart chart-pie stats-pie"></div>
				<div class="count">
					<small>FAT Projects</small>
					<h2><?php echo $project_count['fat']; ?></h2>
				</div>
			</div>
		</div>
	</div>
	
	<div class = "col-sm-3">
		<div class="mini-charts-item bgm-blue" data-href = "<?php echo base_url(); ?>Project/data/mss">
			<div class="clearfix">
				<div class="chart chart-pie stats-pie">
					4
				</div>
				<div class="count">
					<small>MIS Projects</small>
					<h2><?php echo $project_count['mss']; ?></h2>
				</div>
			</div>
		</div>
	</div>
</div>

<div class = "row">
	<div class = "col-sm-8">
		<div class = "card" style = "height: 515px;">
		<div class="card-header">
			<h2>Response Data <small>View OSP and ISP reponse data</small></h2>
		</div>
		<div class="card-body card-padding">
			<div class = "row">
				<div class = "col-sm-4">
					Project Type:<br/>
					<select class = "form-control" name = "project_type">
						<option value = "isp">ISP</option>
						<option value = "osp">OSP</option>
					</select>
					<br/>
					Questions:<br/>
					<select class = "form-control" name = "questions">
						<?php //echo $questions; ?>
					</select>
					<button id = "generate" class="btn btn-default btn-icon-text waves-effect"><i class="zmdi zmdi-trending-up"></i> Generate Data</button>
				</div>
				<div class = "col-sm-8">
					<div id="isp-osp-pie" style="min-width: 100%;margin: 0 auto"></div>
				</div>
			</div>
		</div>
		
		</div>
	</div>
	<div class = "col-sm-4">
		<div class = "card">
			<div class = "card-header">
				<center><h2>Latest Project</h2></center>
			</div>
			<div class="card-body card-padding">
				<center>
					<p><?php if($latest_project->project_type == "mss"){ $latest_project->project_type = "mis"; }echo strtoupper($latest_project->project_type); ?>: <?php echo strtoupper($latest_project->project_name); ?></p>
				</center>
			</div>
		</div>
		
		<div class = "card">
			<div class = "card-header">
				<center><h2>Most Collaborative User</h2></center>
			</div>
			<div class="card-body card-padding">
				<center>
					<p><?php echo $collaborative_user->user_firstname; ?> <?php echo $collaborative_user->user_lastname; ?>: <?php echo $collaborative_user->projects; ?> Projects</p>
				</center>
			</div>
		</div>
		
		<div class = "card">
			<div class = "card-header">
				<center><h2>Least Responded Project Type</h2></center>
			</div>
			<div class="card-body card-padding">
				<center>
					<p><?php  if($least_project_type['project_type'] == "mss"){ $least_project_type['project_type'] = "mis"; }echo strtoupper($least_project_type['project_type']); ?>: <?php echo strtoupper($least_project_type['count']); ?> Responses</p>
				</center>
			</div>
		</div>
		
		<div class = "card">
			<div class = "card-header">
				<center><h2>Most Collaborative Company</h2></center>
			</div>
			<div class="card-body card-padding">
				<center>
					<p>Safaricom: 20 Projects</p>
				</center>
			</div>
		</div>
	</div>
</div>

<script>
$(function () {

    $(document).ready(function () {
    	create_questions_combo( $('select[name="project_type"]').val());
    	$("div .mini-charts-item").click(function(){
    		window.location.href = $(this).attr('data-href');
    	});
    	//$('.highcharts-title tspan').html = $('select[name="project_type"]').val() + " RESPONSE DATA" ;
        // Build the chart
        /*console.log($("select[name='questions']").val());
        console.log($('select[name="project_type"]').val() + "=>" + $("select[name='questions']").val($('select[name="questions"] option:first').val()));
        /*get_graph_data(function(data){
        	plot_graph($('select[name="questions"] option:selected').text(), data);
        });*/
        $('#generate').on("click", function(){
        	console.log($('select[name="questions"] option:selected').val());
        	get_graph_data(function(data){
	        	plot_graph($('select[name="questions"] option:selected').text(), data);
	        });
        });
        $("#generate").trigger("click");
    });
    
    $('select[name="project_type"]').change(function(){
    	create_questions_combo($(this).val());
    });
    
    function create_questions_combo(project_type)
    {
		$.ajax({
		url: "<?php echo base_url(); ?>Dashboard/create_questions_dropdown/" + project_type + "/raw",
		beforeSend: function( xhr ) {
			xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
			}
		})
		.done(function( data ) {
			$('select[name="questions"]').html(data);
		});
    }
    
    function get_graph_data(handledata)
    {
    		data_url = "<?php echo base_url(); ?>Analytics/get_project_response_data/" +  $('select[name="project_type"]').val() + "/" + $('select[name="questions"]').val();
    		$.ajax({
		url: data_url,
		beforeSend: function( xhr ) {
			xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
			$('#isp-osp-pie').html("<center>Please wait...</center>");
			}
		})
		.done(function( data ) {
			var obj = JSON.parse(data);
			handledata(obj);
			console.log(obj);
		});
    }
    function plot_graph(title, data)
    {
    	$('#isp-osp-pie').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: title
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Responses',
                colorByPoint: true,
                data: data
            }]
        });
    }
});
</script>
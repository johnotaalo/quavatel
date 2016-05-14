<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
    
<head>
	<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
	<META HTTP-EQUIV="Expires" CONTENT="-1">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>QUAVATEL::<?php echo strtoupper($page_header); ?></title>

        <!-- Vendor CSS -->
        <link href="<?php echo base_url(); ?>assets/vendors/bower_components/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/vendors/bower_components/animate.css/animate.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/vendors/bower_components/bootstrap-sweetalert/lib/sweet-alert.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/vendors/bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css" rel="stylesheet"> 
        <link href="<?php echo base_url(); ?>assets/vendors/bootgrid/jquery.bootgrid.min.css" rel="stylesheet">
        <link href="//cdn.datatables.net/1.10.10/css/jquery.dataTables.min.css"  rel = "stylesheet">
         <link href="<?php echo base_url(); ?>assets/vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">      
            
        <!-- CSS -->
        <link href="<?php echo base_url(); ?>assets/css/app.min.1.css" rel="stylesheet">
        <link href="<?php echo base_url(); ?>assets/css/app.min.2.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/jquery/dist/jquery.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bootgrid/jquery.bootgrid.updated.min.js"></script>
        <script src="//cdn.datatables.net/1.10.10/js/jquery.dataTables.min.js"></script>
        <link href="<?php echo base_url(); ?>assets/vendors/bower_components/lightgallery/light-gallery/css/lightGallery.css" rel="stylesheet">
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/lightgallery/light-gallery/js/lightGallery.min.js"></script>
        <style>
        	ul.main-menu a
        	{
        		cursor: pointer;
        	}

            .dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                display: inline-block;
            }

            .ongoing
            {
                background: #ef6c00;
            }

            .completed
            {
                background: #2e7d32;
            }

            .today
            {
                background: #0277bd;
            }

            .waiting
            {
                background-color: red;
            }
        </style>
    </head>
    <body>
        <header id="header" class="clearfix" data-current-skin="blue">
            <ul class="header-inner">
                <li id="menu-trigger" data-trigger="#sidebar">
                    <div class="line-wrap">
                        <div class="line top"></div>
                        <div class="line center"></div>
                        <div class="line bottom"></div>
                    </div>
                </li>

                <li class="logo hidden-xs">
                    <a href="<?php echo base_url(); ?>">QUAVA-TEL</a>
                </li>

                <li class="pull-right">
                    <ul class="top-menu">
                        <!-- <li id="toggle-width">
                            <div class="toggle-switch">
                                <input id="tw-switch" type="checkbox" hidden="hidden">
                                <label for="tw-switch" class="ts-helper"></label>
                            </div>
                        </li> -->

                        <li id="top-search">
                            <a href="#"><i class="tm-icon zmdi zmdi-search"></i></a>
                        </li>
                    </ul>
                </li>
            </ul>


            <!-- Top Search Content -->
            <div id="top-search-wrap">
                <div class="tsw-inner">
                    <i id="top-search-close" class="zmdi zmdi-arrow-left"></i>
                    <input type="text" id = "search">
                </div>
            </div>
        </header>
        
        <section id="main" data-layout="layout-1">
            <aside id="sidebar" class="sidebar c-overflow">
                <div class="profile-menu">
                    <a href="#">
                        <div class="profile-pic" style = "height: 50px;">
                            <!-- <img src="<?php echo base_url(); ?>assets/img/profile-pics/1.jpg" alt=""> -->
                        </div>

                        <div class="profile-info">
                            <?php echo strtoupper($sidebar_details->user_firstname . ' ' . $sidebar_details->user_lastname); ?>

                            <i class="zmdi zmdi-caret-down"></i>
                        </div>
                    </a>

                    <ul class="main-menu">
                        <!-- <li>
                            <a href="#"><i class="zmdi zmdi-account"></i> View Profile</a>
                        </li>
                        
                        <li>
                            <a href="#"><i class="zmdi zmdi-settings"></i> Settings</a>
                        </li> -->
                        <li>
                            <a data-href="<?php echo base_url(); ?>Account/logout"><i class="zmdi zmdi-time-restore"></i> Logout</a>
                        </li>
                    </ul>
                </div>

                <ul class="main-menu">
                    <li>
                        <a id = "home" href="<?php echo base_url(); ?>"><i class="zmdi zmdi-home"></i> Home</a>
                    </li>
                    <?php if($sidebar_details->user_type == "admin" || $sidebar_details->user_type == "project_manager"){?>
                    <li>
                        <a id = "project" href="<?php echo base_url(); ?>Project"><i class="zmdi zmdi-router"></i> Projects</a>
                    </li>
                    <?php } ?>
                    <?php if($sidebar_details->user_type == "project_manager" || $sidebar_details->user_type == "finance" || $sidebar_details->user_type == "admin"){?>
                    <li class = "sub-menu">
                        <a data-href="#" id="laboursheet"><i class="zmdi zmdi-walk"></i> Labour and Wage</a>
                        <ul class = "labour-sheet">
                        	<li>
                        		<a data-href="<?php echo base_url(); ?>LabourSheet/" id="laboursheet"><i class="zmdi zmdi-walk"></i> Labour Sheet</a>
                        	</li>
                        	<li>
                        		<a data-href="<?php echo base_url(); ?>LabourSheet/wagestructure" id="wage-structure"><i class="zmdi zmdi-money-box"></i> Wage Structure</a>
                        	</li>
                        </ul>
                    </li>
                    <?php } if($sidebar_details->user_type == "admin" || $sidebar_details->user_type == "acceptance" || $sidebar_details->user_type == "project_manager") {?>
                    <li class="sub-menu">
                        <a data-href="#"><i class="zmdi zmdi-assignment-check"></i> Acceptance</a>
                        <ul class = "project-data">
                            <li>
                                <a id = "isp" data-href="<?php echo base_url(); ?>Project/data/isp"><i class="zmdi zmdi-arrow-right"></i> ISP Data</a>
                            </li>
                            <li>
                                <a id = "osp" data-href="<?php echo base_url(); ?>Project/data/osp"><i class="zmdi zmdi-arrow-right"></i> OSP Data</a>
                            </li>
                            <li>
                                <a id = "fat" data-href="<?php echo base_url(); ?>Project/data/fat"><i class="zmdi zmdi-arrow-right"></i> FAT Data</a>
                            </li>
                            
                        </ul>
                    </li>
                    <li> <a id = "mss" data-href="<?php echo base_url(); ?>Project/data/mss"><i class="zmdi zmdi-arrow-right"></i> MIS Data</a></li>
                    <?php } ?>
                    <?php if($sidebar_details->user_type == "admin"){?>
                    <li>
                        <a data-href="<?php echo base_url(); ?>Account/users" id="users"><i class="zmdi zmdi-accounts-list-alt"></i> User Accounts</a>
                    </li>
                     <li>
                        <a data-href="<?php echo base_url(); ?>Company" id="company"><i class="zmdi zmdi-case"></i> Companies</a>
                    </li>
                    <?php } ?>
                    <li>
                        <a data-href="<?php echo base_url(); ?>Account/logout" id="logout"><i class="zmdi zmdi-time-restore"></i> Logout</a>
                    </li>
                </ul>
            </aside>
            
            <section id="content">
                <div class="container">
                    
                    <div class = "content_view">
                        <?php
                            if(isset($content_view))
                            {
                                $this->load->view($content_view);
                            }
                        ?>
                    </div>
                </div>
            </section>
        </section>
        
        <footer id="footer">
            Copyright &copy; 2015 QUAVA-TEL KENYA <br/>
            
         
            <ul class="f-menu">
                <li><a href="#">Home</a></li>
                <li><a href="#">ISP Data</a></li>
                <li><a href="#">OSP Data</a></li>
                <li><a href="#">FAT Data</a></li>
                <li><a href="#">MSS Data</a></li>
            </ul>
            Powered by Symatech Labs Ltd
        </footer>

        <!-- Page Loader -->
        <div class="page-loader">
            <div class="preloader pls-blue">
                <svg class="pl-circular" viewBox="25 25 50 50">
                    <circle class="plc-path" cx="50" cy="50" r="20" />
                </svg>

                <p>Please wait...</p>
            </div>
        </div>
        
        <!-- Older IE warning message -->
        <!--[if lt IE 9]>
            <div class="ie-warning">
                <h1 class="c-white">Warning!!</h1>
                <p>You are using an outdated version of Internet Explorer, please upgrade <br/>to any of the following web browsers to access this website.</p>
                <div class="iew-container">
                    <ul class="iew-download">
                        <li>
                            <a href="http://www.google.com/chrome/">
                                <img src="img/browsers/chrome.png" alt="">
                                <div>Chrome</div>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.mozilla.org/en-US/firefox/new/">
                                <img src="img/browsers/firefox.png" alt="">
                                <div>Firefox</div>
                            </a>
                        </li>
                        <li>
                            <a href="http://www.opera.com">
                                <img src="img/browsers/opera.png" alt="">
                                <div>Opera</div>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.apple.com/safari/">
                                <img src="img/browsers/safari.png" alt="">
                                <div>Safari</div>
                            </a>
                        </li>
                        <li>
                            <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                                <img src="img/browsers/ie.png" alt="">
                                <div>IE (New)</div>
                            </a>
                        </li>
                    </ul>
                </div>
                <p>Sorry for the inconvenience!</p>
            </div>   
        <![endif]-->
        
        <!-- Javascript Libraries -->
        
        
        
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/flot/jquery.flot.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/flot/jquery.flot.resize.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/flot.curvedlines/curvedLines.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/sparklines/jquery.sparkline.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/bootstrap-select/dist/js/bootstrap-select.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/moment/min/moment.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/simpleWeather/jquery.simpleWeather.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/Waves/dist/waves.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bootstrap-growl/bootstrap-growl.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/bootstrap-sweetalert/lib/sweet-alert.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/vendors/bower_components/typeahead.js/dist/typeahead.bundle.min.js"></script>
        <!-- Placeholder for IE9 -->
        <!--[if IE 9 ]>
            <script src="vendors/bower_components/jquery-placeholder/jquery.placeholder.min.js"></script>
        <![endif]-->
        
        <script src="<?php echo base_url(); ?>assets/js/flot-charts/curved-line-chart.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/flot-charts/line-chart.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/charts.js"></script>
        
        <script src="<?php echo base_url(); ?>assets/js/charts.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/functions.js"></script>

        <script type="text/javascript">
            $(document).ready(function(){
                $('a#'+'<?php echo $menu; ?>').addClass("active");
                var sub_menu = '<?php echo $sub_menu; ?>';
                if(sub_menu == '1')
                {
                    $('a#'+'<?php echo $menu; ?>').parent().parent().parent().addClass("active toggled");
                }
                else
                {
                	 $('a#'+'<?php echo $menu; ?>').parent().addClass("active");
                }
                
                $('ul.main-menu a').click(function(){
                	window.location.href = $(this).attr('data-href');
                });
    		$('input[name="date_from"]').focusout(function(){
    			var from_date = moment($(this).val());
    			var to_date = moment($('input[name="date_to"]').val());
    			var week_to = from_date.add(7, 'days');
    			console.log(week_to);
    		});

            $('a.delete-acceptance').click(function(event){
                event.preventDefault();
                var response = confirm("You are about to delete this entry. Continue?");

                if (response == true)
                {
                    window.location.href = $(this).attr('href');
                }
            });

            $('a.restore_button').click(function(event){
                event.preventDefault();
                var response = confirm("You are about to restore this entry. Continue?");

                if (response == true)
                {
                    window.location.href = $(this).attr('href');
                }
            });

            $('a.delete_labour_wage').click(function(event){
                event.preventDefault();
                var response = confirm("You are about to delete this entry. Continue?");
                if (response == true)
                {
                    window.location.href = $(this).attr('data-href');
                }
            });
            $('.data-table').dataTable();
            });
        </script>
    </body>
<head>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
</head>
</html>
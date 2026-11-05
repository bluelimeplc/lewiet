<?php
    $loggeduserdata = BluelimeAuthentication::get_logged_user_data();
    if(empty($loggeduserdata))
        $loggeduserdata = ['role'=>'','name'=>''];
    
    $logged_role = $loggeduserdata['role'] ??'';
    $header = BluelimeSystem::get_company_header();
    $location = explode('/', $_SERVER['REQUEST_URI'])[1];
    //BluelimeSystem::show_and_die($header);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bluelime: <?=$_ENV['BRANCH']??""?><?=explode('/', string: $_SERVER['REQUEST_URI'])[1]!=''?explode('/', string: $_SERVER['REQUEST_URI'])[1]:'Dashboard'?></title>
        <!-- favicon -->
        <link rel="icon" href="/favicon.png" sizes="16x16"> 
        <link href="<?php echo base_url()?>css/hospital/style.css?date=<?= date('Ymd'); ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url()?>css/hospital/style.css" rel="stylesheet" type="text/css"/>
        <script src="<?php echo base_url()?>js/jquery/jquery-3.6.3.js"></script>

       
        <!-- Start Global Mandatory Style
        =====================================================================-->
        <!-- jquery-ui css -->
        <!--link href="<?php echo base_url()?>assets/plugins/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css"/-->
        <!-- Bootstrap -->
        <link href="<?php echo base_url()?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <!-- Bootstrap rtl -->
        <!-- <link href="<?php echo base_url()?>assets/bootstrap-rtl/bootstrap-rtl.min.css" rel="stylesheet" type="text/css"/> -->
        <!-- Lobipanel css -->
        <link href="<?php echo base_url()?>assets/plugins/lobipanel/lobipanel.min.css" rel="stylesheet" type="text/css"/>
        <!-- Pace css -->
        <!-- <link href="<?php echo base_url()?>assets/plugins/pace/flash.css" rel="stylesheet" type="text/css"/> -->
        <!-- Font Awesome -->
        <link href="<?php echo base_url()?>assets/font-awesome/css/all.min.css" rel="stylesheet" type="text/css"/>
        <!-- Pe-icon -->
        <link href="<?php echo base_url()?>assets/pe-icon-7-stroke/css/pe-icon-7-stroke.css" rel="stylesheet" type="text/css"/>
        <!-- Themify icons -->
        <link href="<?php echo base_url()?>assets/themify-icons/themify-icons.css" rel="stylesheet" type="text/css"/>
        <!-- End Global Mandatory Style
        =====================================================================-->

        <!-- DataTables CSS -->
       <link href="<?php echo base_url();?>assets/plugins/datatables/dataTables.min.css" rel="stylesheet" type="text/css"/>
       <!-- datepiker -->
       <link href="<?php echo base_url()?>assets/plugins/ui-datetimepicker/jquery-ui-timepicker-addon.css" rel="stylesheet" type="text/css"/>

       <!-- summernote css -->
        <link href="<?php echo base_url()?>assets/plugins/summernote/summernote.css" rel="stylesheet" type="text/css"/>
        

        <!-- Start page Label Plugins 
        =====================================================================-->
        <!-- Toastr css -->
        <link href="<?php echo base_url()?>assets/plugins/toastr/toastr.css" rel="stylesheet" type="text/css"/>
        <!-- Emojionearea -->
        <link href="<?php echo base_url()?>assets/plugins/emojionearea/emojionearea.min.css" rel="stylesheet" type="text/css"/>
        <!-- Monthly css -->
        <link href="<?php echo base_url()?>assets/plugins/monthly/monthly.css" rel="stylesheet" type="text/css"/>
        <!-- End page Label Plugins 
        =====================================================================-->
        <!-- Start Theme Layout Style
        =====================================================================-->
        <!--link href="<?php echo base_url()?>extra/sliptree-bootstrap-tokenfield/dist/css/tokenfield-typeahead.min.css" rel="stylesheet" type="text/css"/-->
        <link href="<?php echo base_url()?>extra/sliptree-bootstrap-tokenfield/dist/css/bootstrap-tokenfield.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url()?>extra/sliptree-bootstrap-tokenfield/docs-assets/css/pygments-manni.css" rel="stylesheet" type="text/css"/>
        
        <!-- Theme style -->
        <link href="<?php echo base_url()?>assets/dist/css/styleBD.css" rel="stylesheet" type="text/css"/>
        <!--link href="<?php echo base_url()?>assets/dist/css/styleBD-BLUE.css" rel="stylesheet" type="text/css"/-->
        <!--link href="<?php echo base_url()?>assets/dist/css/styleBD-GREEN.css" rel="stylesheet" type="text/css"/-->
        <!--link href="<?php echo base_url()?>assets/dist/css/styleBD-GREEN.css" rel="stylesheet" type="text/css"/-->
        <!--link href="<?php echo base_url()?>extra/style.css" rel="stylesheet" type="text/css"/>
        
        <!-- Theme style rtl -->
        <!-- <link href="assets/dist/css/styleBD-rtl.css" rel="stylesheet" type="text/css"/> -->
        <!-- End Theme Layout Style
        =====================================================================-->
        <!-- jQuery -->

        
        <link href="<?php echo base_url()?>css/bootstrap-tagsinput.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url()?>css/twitter-app.css" rel="stylesheet" type="text/css"/>
        
        
        <script src="<?php echo base_url()?>js/bootstrap-tagsinput.js" type="text/javascript"></script>
        <script src="<?php echo base_url()?>js/twitter-bootstrap.min.js" type="text/javascript"></script>        
        <script src="<?php echo base_url()?>js/typeahead.bundle.min.js" type="text/javascript"></script>        
        <script src="<?php echo base_url()?>js/bloodhound.js" type="text/javascript"></script>        
        <script src="<?php echo base_url()?>js/dental/dental_script.js" type="text/javascript"></script>    

        <link href="/css/autocomplete.css" rel="stylesheet" type="text/css"/>

    </head>

<body class="hold-transition sidebar-mini">
    
    <div class="se-pre-con"></div>
        <!-- Site wrapper -->
        <div class="wrapper">
            <header class="main-header"> 
                <a href="<?php echo base_url();?>" class="logo"> <!-- Logo -->
                    <span class="logo-mini">
                        <!--<b>A</b>BD-->
                        <?php echo '<img src="data:image/jpeg;base64,'.base64_encode( $header['logo_small'] ).'"  />' ?>
                    </span>
                    <span class="logo-lg">
                        <!--<b>Admin</b>BD-->
                        <?php echo '<img src="data:image/jpeg;base64,'.base64_encode( $header['logo'] ).'"  />' ?>
                    </span>
                </a>

                <!-- Header Navbar -->
                <nav class="navbar navbar-static-top">
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"> <!-- Sidebar toggle button-->
                        <span class="sr-only">Toggle navigation</span>
                        <span class="pe-7s-keypad"></span>
                    </a>
                    <label style="padding-top: 17px;padding-left: 20px;font-size: 20px;color: #54c2f0;"><?=strtoupper($_ENV['BRANCH']??"")?></label>
                    <div class="navbar-custom-menu">
                    
                        <ul class="nav navbar-nav">
                            <!-- settings -->
                            <li class="dropdown dropdown-user">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <i class="pe-7s-settings"<?= (($_ENV['DEV_INDICATION'] ?? 0) == 1) ? 'style="background-color: #0f0;"' : ((($_ENV['DEV_INDICATION'] ?? 0) == 2) ? 'style="background-color: #f00;"' : '') ?>></i></a>
                                <ul class="dropdown-menu">
                                    <li <?php echo ($logged_role=="DOCTOR")?"":"hidden"?>>
                                        <a href="<?php echo base_url();?>hospital/doctor/doctorschedule"><i class="pe-7s-users"></i>&nbsp;&nbsp;Schedule</a>
                                    </li>
                                    <li <?php echo str_contains($location, "changepassword")?"hidden":""?>>
                                        <a href="<?php echo base_url();?>changepassword?loc=<?=$location?>"><i class="pe-7s-key"></i>&emsp;Change Password</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo base_url();?>logout"><i class="fa fa-sign-out fa-fw"></i>Logout</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                      
                    </div>
                </nav>
                
            </header>
            <div class="modal fade" data-backdrop="static" id="dentalAlertModal" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header dental-bg-primary">
                            <div id="dentalAlertTitleIcon" class=" dental-flag-icon">
                                <i class="fa fa-info-circle color-info dental-flag-icon"></i>
                                <i class="fa fa-warning color-yellow dental-flag-icon" style="display:none"></i>
                            </div>
                            <h4 class="modal-title" id="dentalAlertTitle">Modal Title</h4>
                        </div>
                        <div class="modal-body">
                            <div id="dentalAlertBody">Body</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="dentalAlertButtonPrimary" data-dismiss="modal" >OK</button>
                            <button type="button" class="btn btn-danger"  id="dentalAlertButtonSecondary" data-dismiss="modal" >No</button>
                        </div>
                    </div>
                </div>
            </div>
<?= view('Include/_modals') ?>
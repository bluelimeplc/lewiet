<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo "Laboratory Dashboard";?></h1>
            <small><?php echo "Laboratory Dashboard";?></small>
            
        </div>
    </section>


    <!-- Main content -->
    
    <section class="content">
        <div class="row">
            
            <div class="col-sm-12">
            <div class="panel panel-bd">
                <div class="panel-body">
                <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            <a href="#">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <h2><span class="count-number"><?php echo $today_orders ?></span> </h2>
                                            <div class="small"> <?php echo "Today Orders";?> </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><i class="fa fa-flask"></i></div>
                                </div>
                            </a>    
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            <a href="#">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <h2><span class="count-number"><?php echo $today_results ?></span> </h2>
                                            <div class="small"> <?php echo "Today Results";?> </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><i class="fa fa-flask"><i class="fa-xs fa-check-circle"></i></i></div>
                                </div>
                            </a>    
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            <a href="#">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <h2><span class="count-number"><?php echo $total_orders ?></span> </h2>
                                            <div class="small"> <?php echo "Total Orders";?> </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><i class="fa fa-flask"></i></div>
                                </div>
                            </a>    
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            <a href="#">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <h2><span class="count-number"><?php echo $total_results ?></span> </h2>
                                            <div class="small"> <?php echo "Total Results";?> </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><i class="fa fa-flask"><i class="fa-xs fa-check-circle"></i></i></div>
                                </div>
                            </a>    
                            </div>
                        </div>
                </div>
                    
                </div>
                
            </div>
            </div>
            
        </div>



    </section> <!-- /.content -->
</div> <!-- /.content-wrapper -->



            <!-- =============================================== -->
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">

                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="header-icon">
                        <i class="pe-7s-world"></i>
                    </div>
                    <div class="header-title">
                        <h1><?php echo lang('Dashboard');?></h1>
                        <small><?php echo lang('Dashboard');?></small>
                        <ol class="breadcrumb">
                            
                            <li class="active"><a href="<?php echo base_url();?>display/"><?php echo lang('Queue Display');?></a></li>
                        </ol>
                    </div>
                </section>
                <?php
                $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
                $logged_role = $loggeduserdata['role'];                
                ?>
                
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 <?=($logged_role==='RECEPTION')?"bg-green":"" ?>">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?>hospital/reception">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                    <h2><span class="count-number">                                        
                                            <?php
                                            $no_of_users = 0;
                                                foreach($users as $user){
                                                    if($user['role']=='RECEPTION'){
                                                        $no_of_users++;
                                                    }
                                                    
                                                }
                                            echo $no_of_users;

                                            ?>
                                        </span></h2>
                                            <div class="small"> Reception </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><img src="/img/reception.png" width="50px"></div>
                                </div>
                            </a>    
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 <?=($logged_role==='NURSE')?"bg-green":"" ?>">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?>hospital/nurse">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                    <h2><span class="count-number">                                        
                                            <?php
                                            $no_of_users = 0;
                                                foreach($users as $user){
                                                    if($user['role']=='NURSE'){
                                                        $no_of_users++;
                                                    }
                                                    
                                                }
                                            echo $no_of_users;

                                            ?>
                                        </span></h2>
                                            <div class="small"> Nurse </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><img src="/img/nurse.png" width="50px"></div>
                                </div>
                            </a>    
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 <?=($logged_role==='ADMIN')?"bg-green":"" ?>">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?>hospital/admin">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                    <h2><span class="count-number">                                        
                                            <?php
                                            $no_of_users = 0;
                                                foreach($users as $user){
                                                    if($user['role']=='ADMIN' || $user['role']=='SUPER_ADMIN'){
                                                        $no_of_users++;
                                                    }
                                                    
                                                }
                                            echo $no_of_users;

                                            ?>
                                        </span></h2>
                                            <div class="small"> Admin </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><img src="/img/admin.png" width="50px"></div>
                                </div>
                            </a>    
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 <?=($logged_role==='DOCTOR')?"bg-green":"" ?>">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?>hospital/doctor">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                    <h2><span class="count-number">                                        
                                            <?php
                                            $no_of_users = 0;
                                                foreach($users as $user){
                                                    if($user['role']=='DOCTOR'){
                                                        $no_of_users++;
                                                    }
                                                    
                                                }
                                            echo $no_of_users;

                                            ?>
                                        </span></h2>
                                            <div class="small"> Doctor </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><img src="/img/doctors.jpeg" width="50px"></div>
                                </div>
                            </a>    
                            </div>
                        </div>
                        <?php if(BLULIME_DEFAULT_APP=="HOSPITAL"): ?>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 <?=($logged_role==='')?"bg-green":"" ?>">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?><?=strtolower(BLULIME_DEFAULT_APP)?>/laboratory">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                    <h2><span class="count-number">                                        
                                            <?php
                                            $no_of_users = 0;
                                                foreach($users as $user){
                                                    if($user['role']=='LABORATORY'){
                                                        $no_of_users++;
                                                    }
                                                    
                                                }
                                            echo $no_of_users;

                                            ?>
                                        </span></h2>
                                            <div class="small"> Laboratory </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><img src="/img/laboratory.png" width="50px"></div>
                                </div>
                            </a>    
                            </div>
                        </div>
                        <?php endif ?>
                        <?php if(BLULIME_DEFAULT_APP=="HOSPITALx"): ?>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 <?=($logged_role==='STORE')?"bg-green":"" ?>">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?>inventory/store">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                    <h2><span class="count-number">                                        
                                            <?php
                                            $no_of_users = 0;
                                                foreach($users as $user){
                                                    if($user['role']=='STORE'){
                                                        $no_of_users++;
                                                    }
                                                    
                                                }
                                            echo $no_of_users;

                                            ?>
                                        </span></h2>
                                            <div class="small"> Store </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><img src="/img/inventory.jpg" width="50px"></div>
                                </div>
                            </a>    
                            </div>
                        </div>
                        <?php endif ?>
                        <?php if(BLULIME_DEFAULT_APP=="HOSPITALx"): ?>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 <?=($logged_role==='FINANCE')?"bg-green":"" ?>">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?>inventory/finance">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                    <h2><span class="count-number">                                        
                                            <?php
                                            $no_of_users = 0;
                                                foreach($users as $user){
                                                    if($user['role']=='FINANCE'){
                                                        $no_of_users++;
                                                    }
                                                    
                                                }
                                            echo $no_of_users;

                                            ?>
                                        </span></h2>
                                            <div class="small"> Finance </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><img src="/img/finance.png" width="50px"></div>
                                </div>
                            </a>    
                            </div>
                        </div>
                        <?php endif ?>
                        <?php if(BLULIME_DEFAULT_APP=="HOSPITALx"): ?>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 <?=($logged_role==='DISPENSARY')?"bg-green":"" ?>">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?>inventory/dispensary">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                    <h2><span class="count-number">                                        
                                            <?php
                                            $no_of_users = 0;
                                                foreach($users as $user){
                                                    if($user['role']=='DISPENSARY'){
                                                        $no_of_users++;
                                                    }
                                                    
                                                }
                                            echo $no_of_users;

                                            ?>
                                        </span></h2>
                                            <div class="small"> Dispensary </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><img src="/img/Pharmacy.png" width="50px"></div>
                                </div>
                            </a>    
                            </div>
                        </div>
                        <?php endif ?>
                        <?php if(BLULIME_DEFAULT_APP=="HOSPITALx"): ?>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 <?=($logged_role==='COMPOUNDING')?"bg-green":"" ?>">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?>inventory/compounding/list">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                    <h2><span class="count-number">                                        
                                            <?php
                                            $no_of_users = 0;
                                                foreach($users as $user){
                                                    if($user['role']=='COMPOUNDING'){
                                                        $no_of_users++;
                                                    }
                                                    
                                                }
                                            echo $no_of_users;

                                            ?>
                                        </span></h2>
                                        <div class="small">Compounding </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><img src="/img/compounding.png" width="50px"></div>
                                </div>
                            </a>    
                            </div>
                        </div>
                        <?php endif ?>

                        <?php if(BLULIME_DEFAULT_APP=="HOSPITALx"): ?>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 <?=($logged_role==='CASHIER')?"bg-green":"" ?>">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?>cashier/">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                    <h2><span class="count-number">                                        
                                            <?php
                                            $no_of_users = 0;
                                                foreach($users as $user){
                                                    if($user['role']=='CASHIER'){
                                                        $no_of_users++;
                                                    }
                                                    
                                                }
                                            echo $no_of_users;

                                            ?>
                                        </span></h2>
                                            <div class="small">Cashier </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><img src="/img/cashier.png" width="50px"></div>
                                </div>
                            </a>    
                            </div>
                        </div>
                        <?php endif ?>
                        
                        <?php if(BLULIME_DEFAULT_APP=="HOSPITALx"): ?>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 <?=($logged_role==='CALL_CENTER')?"bg-green":"" ?>">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?>hospital/callcenter/">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                    <h2><span class="count-number">                                        
                                            <?php
                                            $no_of_users = 0;
                                                foreach($users as $user){
                                                    if($user['role']=='CALL_CENTER'){
                                                        $no_of_users++;
                                                    }
                                                    
                                                }
                                            echo $no_of_users;

                                            ?>
                                        </span></h2>
                                            <div class="small">Call Center </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><img src="/img/callcenter.png" width="50px"></div>
                                </div>
                            </a>    
                            </div>
                        </div>
                        <?php endif ?>
                        <?php if(BLULIME_DEFAULT_APP=="HOSPITALx"): ?>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 <?=($logged_role==='PATHOLOGY')?"bg-green":"" ?>">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?>hospital/pathology/">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                    <h2><span class="count-number">                                        
                                            <?php
                                            $no_of_users = 0;
                                                foreach($users as $user){
                                                    if($user['role']=='PATHOLOGY'){
                                                        $no_of_users++;
                                                    }
                                                    
                                                }
                                            echo $no_of_users;

                                            ?>
                                        </span></h2>
                                            <div class="small">Pathology</div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><img src="/img/pathology.png" width="60px"></div>
                                </div>
                            </a>    
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3 <?=($logged_role==='MIS')?"bg-green":"" ?>">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?>hospital/MIS/">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                    <h2><span class="count-number">                                        
                                            <?php
                                            $no_of_users = 0;
                                                foreach($users as $user){
                                                    if($user['role']=='MIS'){
                                                        $no_of_users++;
                                                    }
                                                    
                                                }
                                            echo $no_of_users;

                                            ?>
                                        </span></h2>
                                            <div class="small">MIS Dashboard</div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><img src="/img/mis.png" width="60px"></div>
                                </div>
                            </a>    
                            </div>
                        </div>
                        <?php endif ?>
                        
                    </div>

                </section> <!-- /.content -->
            </div> <!-- /.content-wrapper -->
<script>


$(document).ready(function () {
    //bar chart
    var ctx = document.getElementById("barChart");
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Last 30 days", "Last 15 days", "Last 7 days", "Today"],
            datasets: [
                {
                    label: "<?php echo lang('appointment_generated');?>",
                    data: [<?php echo @$last_30;?>, <?php echo @$last_15;?>, <?php echo @$last_7;?>, 3],
                    borderColor: "#223F4A",
                    borderWidth: "0",
                    backgroundColor: "#223F4A"
                },
                {
                    label: "<?php echo lang('appointment_deleted');?>",
                    data: [<?php echo @$last_30;?>, <?php echo @$last_15;?>, <?php echo @$last_7;?>, 4],
                    borderColor: "#57787D",
                    borderWidth: "0",
                    backgroundColor: "#57787D"
                }
            ]
        },
        options: {
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
            }
        }
    });

});

</script>            
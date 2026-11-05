<?php
    $selected_date = substr(@$selected_date,0,10);
    $end_date = substr(@$end_date,0,10);
?>
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
            
            <div class="col-sm-12">
                <div class="panel panel-bd">
                <div class="row">&nbsp;</div>
                        <form action="/hospital/laboratory/labdashboard" method="POST">
                            <div class=" col-lg-3" style="margin-bottom: 2px;">
                                <div class="input-group">
                                    <span class="input-group-addon" ><b>From</b></span>
                                    <input type="date" class="form-control" value="<?=$selected_date;?>" name="selected_date">
                                </div>
                            </div>
                            <div class=" col-lg-3">
                                <div class="input-group">
                                    <span class="input-group-addon" ><b>To</b></span>
                                    <input type="date" class="form-control" value="<?=$end_date;?>"  name="end_date">    
                                </div>
                                <input type="submit" style="visibility:hidden">
                            </div>
                            <div class=" col-lg-3"><input type="submit" class="btn btn-success" value="Go"></div>
                        </form>
                    <div class="panel-body" id="printable">
                        
                        <table width="100%" class="table table-bordered table-condensed">
                                <thead>
                                    <tr><th colspan="6" style="text-align:center">Laboratory Result Log (<?= $selected_date . (($selected_date!=$end_date)?' - ' . $end_date: '') ?>) <a role="button" class="btn btn-info btn-xs" onclick="print_printable_document('printable')"><span class="fa fa-print"></span></a></th></tr>
                                    <tr>                     
                                        <th>N<u>o</u></th>
                                        <th>Date</th>
                                        <th>MRN No</th>
                                        <th>Full Name</th>
                                        <th>Age</th>
                                        <th>Sex</th>
                                        
                                    </tr>

                                </thead>

                                <tbody>
                                    <?php
                                        $cnt = 0;
                                        $pid = 0;
                                        foreach ($order_result as $patient) {
                                            
                                            
                                    ?>
                                    <tr style="background-color:aliceblue;">
                                        <?php
                                            if($patient['patient_detail']['patient_id'] != $pid){
                                                $cnt++;
                                        ?>
                                            <td width="15px"><?=$cnt?></td>
                                            <td><?=date('Y-m-d',strtotime($patient['created_at']))?></td>
                                            <td><b><?= sprintf('%05d', $patient['patient_detail']['patient_id']); ?></b></td>
                                            <td><?php echo $patient['patient_detail']['patient_name'];?></td>
                                            <td><?=BluelimeSystem::year_difference($patient['created_at'],$patient['patient_detail']['date_of_birth'])."Y "?><?=BluelimeSystem::year_difference($patient['created_at'],$patient['patient_detail']['date_of_birth']) >=5 ?"":BluelimeSystem::month_difference($patient['created_at'],$patient['patient_detail']['date_of_birth'])."M"?></td>
                                            <td><?php echo $patient['patient_detail']['sex'];?></td>
                                        <?php
                                            }
                                                $pid = $patient['patient_detail']['patient_id'];
                                        ?>
                                        
                                        
                                    </tr>
                                    <tr><td colspan="6"><?=$patient['attribute_name']?>: <?=$patient['result']?></td></tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table> 
                        </div>
                    </div>
                </div>
            </div>
            
        </div>



    </section> <!-- /.content -->
</div> <!-- /.content-wrapper -->
<script>
    function print_printable_document(id){
        var printContents = document.getElementById(id).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
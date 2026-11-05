    <!-- =============================================== -->
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">

                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="header-icon">
                        <i class="pe-7s-world"></i>
                    </div>
                    <div class="header-title">
                        <h1><?php echo "Reports";?></h1>
                        <small><?php echo "Sales Reports";?></small>
                        <ol class="breadcrumb">
                            <?= view("Include/_admin_report_top_menu"); ?>
                        </ol>
                    </div>
                </section>

                
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel">
                                <div class="panel-heading">
                                    <div class="panel-title"><h4>Filter</h4></div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <form action="" method="GET">
                                                <div class=" col-lg-3" style="margin-bottom: 2px;">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" ><b>From</b></span>
                                                        <input type="date" class="form-control" value="<?= substr($from_date,0,10);?>" name="selected_date">
                                                    </div>
                                                </div>
                                                <div class=" col-lg-3">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" ><b>To</b></span>
                                                        <input type="date" class="form-control" value="<?=substr($to_date,0,10);?>"  name="end_date">    
                                                    </div>
                                                    <input type="submit" style="visibility:hidden">
                                                </div>
                                            <div class=" col-lg-3"><input type="submit" class="btn btn-success" value="Filter"></div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-bd">
                                <div class="panel-heading">
                                    <h2>Clinic</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                            <div class="panel panel-bd">
                            
                                <div class="panel-body">
                                    <div id="report_printable">
                                <div class="row">
                                    <div class="col-md-12">
                                        
                                            
                                            <div class="panel-body">
                                                <table class="table table-striped table-bordered">
                                                    <tr style="background-color: purple; color:#fff">
                                                        <th>Payment Type</th><td align="right"><b>Qty</b></td><td align="right"><b>Amount</b></td>
                                                    </tr>
                                                    <?php
                                                        foreach($payment_reportc as $indexc => $reportc){
                                                            
                                                            if( ($indexc == BluelimeConfiguration::PAYMENT_METHOD::CASH) || ($indexc == 'TOTAL') || ($indexc == 'void_reciepts') || ($indexc == BluelimeConfiguration::PAYMENT_METHOD::CREDIT))
                                                                continue;
                                                        if($reportc['amount']!=0){
                                                            echo '
                                                    <tr '. ($indexc == 'BANK TOTAL' ? 'style="background-color: #ccc;font-size: larger; font-weight:bold; border-bottom: solid 2px;"' :'') . '>
                                                        <th>'.($indexc!='BANK TOTAL'?'&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;':'').$indexc.'</th><td align="right">'.$reportc['qty'].'</td><td align="right">'.number_format($reportc['amount'],2).'</td>
                                                    </tr>
                                                            ';
                                                            }
                                                        }
                                                    ?>
                                                    <tr>
                                                        <th><i class="fa fa-money"></i> CASH </th><td align="right"><?= $payment_reportc['Cash']['qty'] ?></td><td align="right"><?= number_format($payment_reportc['Cash']['amount'],2) ?></td>
                                                    </tr>
                                                    <?php
                                                        if($payment_reportc['Credit']['amount'] > 0):
                                                    ?>
                                                    <tr style="font-size: larger; font-weight: bold; border-bottom: solid 2px;">
                                                        <th>SUB TOTAL</th><td align="right"><?= ($payment_reportc['TOTAL']['qty']-$payment_reportc['Credit']['qty']) ?></td><td align="right"><?= number_format($payment_reportc['TOTAL']['amount']-$payment_reportc['Credit']['amount'],2) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th><i>Credit</i></th><td align="right"><i><?= $payment_reportc['Credit']['qty'] ?></i></td><td align="right"><i><?= number_format($payment_reportc['Credit']['amount'],2) ?></i></td>
                                                    </tr>
                                                    <?php endif ?>
                                                    <tr style="font-size: large; font-weight: bold; border-top: solid 2px; border-bottom: solid 2px;">
                                                        <th>TOTAL</th><td align="right"><?= $payment_reportc['TOTAL']['qty'] ?></td><td align="right"><?= number_format($payment_reportc['TOTAL']['amount'],2) ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        
                                    </div>
                                    
                                    

                                    </div>
                            </div>
                                </div>
                                </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                            <div class="panel panel-bd">
                            
                                <div class="panel-body">
                        <div class="col-lg-12">
                            <table class="table table-borderless">
                                
                                            <?php

                                                $totals = [];

                                                foreach ($bill_report_data as $entry) {
                                                    $group = $entry['service_type'];
                                                    $amount = $entry['total'];

                                                    if (!isset($totals[$group])) {
                                                        $totals[$group] = 0;
                                                    }
                                                    $totals[$group] += $amount;
                                                }

                                                // Output results
                                                foreach ($totals as $group => $total) {
                                                    echo '
                                                        <tr>
                                                            <td colspan="6"><b>'.$group.'</b></td><td>'.number_format($total,2).'</td>
                                                        </tr>
                                                ';
                                                }
                                            ?>
                                
                            </table>
                        </div>
                        <a class="btn btn-success" style="float: right;" href="/hospital/report/clinic_bill_groupedby_service_name?selected_date=<?= substr($from_date,0,10);?>&end_date=<?= substr($to_date,0,10);?>">View Detail</a>
                    </div>
                        </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                            <div class="panel panel-bd">
                            
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td align="right"><h2><span class="count-number"><?php echo $number_of_visits?></span></h2></td>
                                                <td><div class="small">Visits</div></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><h2 class="report_list"><span class="count-number"><?php echo $number_of_new_patients?></span></h2></td>
                                                <td><div class="small">New Patients</div></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><h2 class="report_list"><span class="count-number"><?php echo ($number_of_visits - $number_of_new_patients ) ?></span></h2></td>
                                                <td><div class="small">Revisits</div></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="icon"><i class="ti-user"></i></div>
                                </div>
                               
                            </div>
                        </div>
                        </div>
                    <div class="row">
                        

                        

                        

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                            <div class="panel panel-bd">
                            <a href="">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4><a class="" data-toggle="collapse" href="#collapsefreevisit" role="button" aria-expanded="false" aria-controls="collapsefreevisit">
                                                Free Visits <i class="fa fa-angle-double-down"></i>
                                                <span class="badge badge-pill badge-primary pull-right"><?php echo count($free_visits)?></span>
                                            </a>
                                        </h4>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="statistic-box collapse"  id="collapsefreevisit">
                                    
                                        <span class="count-number">
                                            <table class="table table-bordered table-striped" style="font-size: smaller;">
                                                <?php 
                                                foreach($this->data['free_visits'] as $free){
                                                    switch ($free['is_free']) {
                                                        case 1:
                                                            $free['is_free'] = "Transfer from Branch";
                                                            break;
                                                        case 2:
                                                            $free['is_free'] = "Surgery/Procedure Followup";
                                                            break;
                                                        case 4:
                                                            $free['is_free'] = "Approved Patient";
                                                            break;
                                                        case 5:
                                                            $free['is_free'] = "Internally Referred";
                                                            break;
                                                        default:
                                                            $free['is_free'] = "Unknown";
                                                            break;
                                                    }
                                                echo  '<tr><td>'. $free['patient_name'].'</td><td>'. $free['is_free'].'</td></tr>';
                                                }
                                                ?>
                                            </table>
                                        </span>
                                        <div class="sparkline2 text-center"></div>
                                    </div>
                                </div>
                            </a>
                            </div>
                        </div>
                        

                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-bd">
                                <div class="panel-heading">
                                    <h2>Pharmacy</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                            <div class="panel panel-bd">
                            
                                <div class="panel-body">
                                    <div id="report_printable">
                                <div class="row">
                                    <div class="col-md-12">
                                        
                                            
                                            <div class="panel-body">
                                                <table class="table table-striped table-bordered">
                                                    <tr style="background-color: purple; color:#fff">
                                                        <th>Payment Type</th><td align="right"><b>Qty</b></td><td align="right"><b>Amount</b></td>
                                                    </tr>
                                                    <?php
                                                        foreach($payment_report as $index => $report){
                                                            
                                                            if( ($index == BluelimeConfiguration::PAYMENT_METHOD::CASH) || ($index == 'TOTAL') || ($index == 'void_reciepts') || ($index == BluelimeConfiguration::PAYMENT_METHOD::CREDIT))
                                                                continue;
                                                        if($report['amount']!=0){
                                                            echo '
                                                    <tr '. ($index == 'BANK TOTAL' ? 'style="background-color: #ccc;font-size: larger; font-weight:bold; border-bottom: solid 2px;"' :'') . '>
                                                        <th>'.($index!='BANK TOTAL'?'&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;':'').$index.'</th><td align="right">'.$report['qty'].'</td><td align="right">'.number_format($report['amount'],2).'</td>
                                                    </tr>
                                                            ';
                                                            }
                                                        }
                                                    ?>
                                                    <tr>
                                                        <th><i class="fa fa-money"></i> CASH </th><td align="right"><?= $payment_report['Cash']['qty'] ?></td><td align="right"><?= number_format($payment_report['Cash']['amount'],2) ?></td>
                                                    </tr>
                                                    <?php
                                                        if($payment_report['Credit']['amount'] > 0):
                                                    ?>
                                                    <tr style="font-size: larger; font-weight: bold; border-bottom: solid 2px;">
                                                        <th>SUB TOTAL</th><td align="right"><?= ($payment_report['TOTAL']['qty']-$payment_report['Credit']['qty']) ?></td><td align="right"><?= number_format($payment_report['TOTAL']['amount']-$payment_report['Credit']['amount'],2) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th><i>Credit</i></th><td align="right"><i><?= $payment_report['Credit']['qty'] ?></i></td><td align="right"><i><?= number_format($payment_report['Credit']['amount'],2) ?></i></td>
                                                    </tr>
                                                    <?php endif ?>
                                                    <tr style="font-size: large; font-weight: bold; border-top: solid 2px; border-bottom: solid 2px;">
                                                        <th>TOTAL</th><td align="right"><?= $payment_report['TOTAL']['qty'] ?></td><td align="right"><?= number_format($payment_report['TOTAL']['amount'],2) ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        
                                    </div>
                                    

                                    </div>
                            </div>
                                </div>
                                </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                            <div class="panel panel-bd">
                            
                                <div class="panel-body">
                        <div class="col-lg-12">
                            <table class="table">
                                <tr>
                                    <th>Pharmacy Sales:</th><td align="right"><?= number_format($salse_report_data['pharmacy_sales'],2) ?></td>
                                </tr>
                                <tr>
                                    <th>Cosmetics Sales:</th><td align="right"><?= number_format($salse_report_data['cosmetics_sales'],2) ?></td>
                                </tr>
                                <tr>
                                    <th>Cosmetics VAT:</th><td align="right"><?= number_format($salse_report_data['pharmacy_vat'],2) ?></td>
                                </tr>
                                <tr>
                                    <th>TOTAL:</th><td align="right"><b><?= number_format($salse_report_data['pharmacy_vat'] + $salse_report_data['cosmetics_sales'] + $salse_report_data['pharmacy_sales'],2) ?></b></td>
                                </tr>
                                <tr>
                                    <th> </th><td align="right">&nbsp;</td>
                                </tr>

                                
                            </table>
                        </div>
                    </div>
                        </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                            <div class="panel panel-bd">
                            
                                <div class="panel-body">
                        <div class="col-lg-12">
                            <table class="table">
                                

                                <tr>
                                    <th>Compounding Sales:</th><td align="right"><?= number_format($salse_report_data['compounding_sales'],2) ?></td>
                                </tr>
                                <tr>
                                    <th>Compounding Taxable Sales:</th><td align="right"><?= number_format($salse_report_data['compounding_taxable_sales'],2) ?></td>
                                </tr>
                                <tr>
                                    <th>Compounding VAT:</th><td align="right"><?= number_format($salse_report_data['compounding_vat'],decimals: 2) ?></td>
                                </tr>
                                <tr>
                                    <th>TOTAL:</th><td align="right"><b><?= number_format($salse_report_data['compounding_sales'] + $salse_report_data['compounding_taxable_sales'] + $salse_report_data['compounding_vat'],2) ?></b></td>
                                </tr>
                                <tr>
                                    <th> </th><td align="right">&nbsp;</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                        </div>
                        </div>
                    </div>
                    <!-- div class="row">
                         <!-- Bar Chart>
                        <div class="col-sm-12 col-md-12">
                            <div class="panel panel-bd lobidisable">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4><?php echo "Bill Chart"?></h4>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <canvas id="barChart" height="140"></canvas>
                                </div>
                            </div>
                        </div>
                        
                    </div -->

                </section> <!-- /.content -->
            </div> <!-- /.content-wrapper -->
          <script>
    $(document).ready(function() {
        var t = $('#item_sales_report_table').DataTable( {
            //info: false,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'All']
            ]
            //ordering: false,
            //paging: false,
            //scrollCollapse: true,
           // scrollY: '300px'
        } );
    });
</script>
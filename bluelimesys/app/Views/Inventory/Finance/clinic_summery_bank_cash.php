<?php 
//BluelimeSystem::print_associated_array($sales_type_report);
?>
<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?php
    $logged_user = BluelimeAuthentication::get_logged_user_data();
    if($logged_user['role'] == BluelimeConfiguration::USER_ROLE::CASHIER_INVENTORY)
        echo view('Inventory/Finance/_left_sidebar_menu');
    else
        echo view('Inventory/Finance/_left_sidebar_finance');
?>
<?= view('Include/_left_sidebar_bottom') ?>

    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1><a href="/cashier"><?= $cashier_type ?> Cashier</a></h1>
                <small>Dashboard</small>
                <ol class="breadcrumb">
                    <li class="active"> <a href="/cashier/dashboard_detail" role="button">Detail</a></li>
                    <?php
                        if($logged_user['role'] == 'SUPER_ADMIN'){
                    ?>
                            <li class="active"><a href="?i=2"><?php echo "Hospital";?></a></li>
                            <li class="active"><a href="?i=1"><?php echo "Inventory";?></a></li>
                    <?php
                            }
                    ?>
                </ol>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            
            <div class="row">
                <!-- Doctor Assignment -->
                <div class="col-md-12">
                    <div class="panel panel-bd">
                        <div class="panel-body">
                            <div class="row">
                                <form action="/cashier/dashboard" method="post" onsubmit="">
                                    <div class="form-group">
                                        <div class="col-lg-1">
                                            <label for="filter_date">Date: </label>
                                        </div>
                                        <div class="col-lg-3">
                                            <input type="date" class="form-control" value="<?= $filter_date ?>" id="filter_date" name="filter_date" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-lg-1">
                                            <input name= "daily_report" id="daily_report" type="submit" value="Search" hidden />
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <br>
                            <div id="report_printable">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="panel panel-bd">
                                            <div class="panel-heading"><h4>Payment Report &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= $filter_date ?></span></h4></div>
                                            <div class="panel-body">
                                                <table class="table table-striped table-bordered">
                                                    <tr style="background-color: purple; color:#fff">
                                                        <th>Payment Type</th><td align="right"><b>Qty</b></td><td align="right"><b>Amount</b></td>
                                                    </tr>
                                                    <?php
                                                        foreach($payment_report as $index => $report){
                                                            if( ($index == BluelimeConfiguration::PAYMENT_METHOD::CASH) || ($index == 'TOTAL') || ($index == BluelimeConfiguration::PAYMENT_METHOD::CREDIT))
                                                                continue;
                                                            echo '
                                                    <tr '. ($index == 'BANK TOTAL' ? 'style="background-color: #ccc;"' :'') . '>
                                                        <th>'.$index.'</th><td align="right">'.$report['qty'].'</td><td align="right">'.number_format($report['amount'],2).'</td>
                                                    </tr>
                                                            ';
                                                        }
                                                    ?>
                                                    <tr>
                                                        <th>Cash</th><td align="right"><?= $payment_report['Cash']['qty'] ?></td><td align="right"><?= number_format($payment_report['Cash']['amount'],2) ?></td>
                                                    </tr>
                                                    <tr style="font-size: large; font-weight: bold;">
                                                        <th>TOTAL</th><td align="right"><?= $payment_report['TOTAL']['qty'] ?></td><td align="right"><?= number_format($payment_report['TOTAL']['amount'],2) ?></td>
                                                    </tr>
                                                    <?php
                                                        if($payment_report['Credit']['amount'] > 0):
                                                    ?>
                                                    <tr>
                                                        <th><i>Credit</i></th><td align="right"><i><?= $payment_report['Credit']['qty'] ?></i></td><td align="right"><i><?= number_format($payment_report['Credit']['amount'],2) ?></i></td>
                                                    </tr>
                                                    <?php endif ?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    

                                    <!--div class="col-md-4">
                                        <div class="panel panel-bd">
                                            <div class="panel-heading"><h4>Sales Report &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= $filter_date ?></span></h4></div>
                                            <div class="panel-body">
                                                <table class="table table-striped table-bordered">
                                                    <tr style="background-color:darkgreen; color:#fff">
                                                        <th>Sales Type</th><td align="right"><b>Qty</b></td><td align="right"><b>Amount</b></td>
                                                    </tr>
                                                    
                                                    <?php
                                                    /*
                                                        foreach($sales_type_report as $index => $report){
                                                            echo '
                                                    <tr>
                                                        <th>'.$index.'</th><td align="right">'.$report['qty'].'</td><td align="right">'.round($report['amount'],2).'</td>
                                                    </tr>
                                                            ';
                                                        }
                                                    */
                                                    ?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="panel panel-bd">
                                            <div class="panel-heading"><h4>Item Type Report &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= $filter_date ?></span></h4></div>
                                            <div class="panel-body">
                                                <table class="table table-striped table-bordered">
                                                    <tr style="background-color:brown; color:#fff">
                                                        <th>Item Type</th><td align="right"><b>Qty</b></td><td align="right"><b>Amount</b></td>
                                                    </tr>
                                                    <?php
                                                        /*
                                                        foreach($item_type_report as $index => $report){
                                                            echo '
                                                    <tr>
                                                        <th>'.$index.'</th><td align="right">'.$report['qty'].'</td><td align="right">'.round($report['amount'],2).'</td>
                                                    </tr>
                                                            ';
                                                        }
                                                        */
                                                    ?>
                                                </table>
                                            </div>
                                        </div-->
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <a role="button" onclick="print_printable('report_printable')" class="btn btn-success" style="float:right"><span class="fa fa-print"></span> Print</span></a>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            
        </section>
    </div>
    
<?= view('Include/_footer') ?>


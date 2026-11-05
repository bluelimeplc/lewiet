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
        echo view('Inventory/Finance/_left_sidebar_reception');
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
                    <li class="active"></li>
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
                                <form action="/cashier/dashboard_detail" method="post" onsubmit="">
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
                                    <div class="col-md-12">
                                        <div class="panel panel-bd">
                                            <div class="panel-heading"><h4>Payment Report &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= $filter_date ?></span></h4></div>
                                            <div class="panel-body">
                                                <table class="table table-striped table-bordered">
                                                    <tr style="background-color: purple; color:#fff">
                                                        <th></th><th>ID</th><th>FS Number</th><th>Payment Method</th><th>Total</th><th>Transferd Amount</th><th>Payment Reference</th><th>Response date</th>
                                                    </tr>
                                                    <?php
                                                        $sn = 1;
                                                        foreach($pos_bills as $line){
                                                            echo '
                                                    <tr>
                                                        <td>'.$sn++.'<td>'.$line['id'].'</td><td class="'.(trim($line['fs_number'])==''?'bg-red':'').'">'.$line['fs_number'].'</td><td>'.$line['payment_method'].'</td><td align="right">'.number_format($line['total'],2).'</td><td align="right">'.number_format($line['transferred_amount'],2).'</td><td>'.$line['payment_reference'].'</td><td>'.$line['response_date'].'</td>
                                                    </tr>
                                                            ';
                                                        }
                                                    ?>
                                                </table>
                                            </div>
                                        </div>
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


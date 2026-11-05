<?php 
//BluelimeSystem::print_associated_array($sales_type_report);
?>
<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?php
    $logged_user = BluelimeAuthentication::get_logged_user_data();
    if($logged_user['role'] == BluelimeConfiguration::USER_ROLE::CASHIER_INVENTORY)
        echo view('Cashier/_left_sidebar_menu');
    elseif($logged_user['role'] == BluelimeConfiguration::USER_ROLE::FINANCE)
        echo view('Inventory/Finance/_left_sidebar_finance');
    else
        echo view('Hospital/Reception/_left_sidebar_reception');
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
                    <li class="active"> <a href="/cashier/dashboard?filter_date=<?= $filter_date ?>" role="button">Summary</a></li>
                    <?php
                        if(($logged_user['role'] == 'SUPER_ADMIN') || ($logged_user['role'] == BluelimeConfiguration::USER_ROLE::FINANCE)){
                    ?>
                            <li class="active"><a href="?i=2&filter_date=<?= $filter_date ?>"><?php echo "Hospital";?></a></li>
                            <li class="active"><a href="?i=1&filter_date=<?= $filter_date ?>"><?php echo "Inventory";?></a></li>
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
                                <form action="/cashier/dashboard_detail" method="get" onsubmit="">
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
                                                        <th>Sn</th><th>FS Number</th><th>Payment Method</th><th>Total</th><th>Transferd Amount</th><th>Payment Reference</th><th>Response date</th><th>ID</th>
                                                    </tr>
                                                    <?php
                                                        $sn = 1;
                                                        $prv_fs = 0;
                                                        foreach($pos_bills as $line){
                                                            $indicate_problem = false;
                                                            $response_date_is_different = false;

                                                            $current_fs_number = intval($line['fs_number']);
                                                            if($current_fs_number == 0)
                                                                $current_fs_number = intval(trim(str_replace('M','',$line['fs_number'])));
                                                            
                                                            if($prv_fs != 0){
                                                                if(($current_fs_number - $prv_fs) != 1)
                                                                    $indicate_problem = true;
                                                            }
                                                            $prv_fs = $current_fs_number;

                                                           if(($filter_date != $line['response_date']) && ($line['response_date'] != ''))
                                                                $response_date_is_different = true;
                                                            
                                                            

                                                            echo '
                                                    <tr>
                                                        <td '.($indicate_problem?' style="background-color:#f00;" ':'').'>'.$sn++.'</td>
                                                        <td class="'.(trim($line['fs_number'])==''?'bg-warning':'').'">'.$line['fs_number'].'</td>
                                                        <td>'.$line['payment_method'].'</td>
                                                        <td align="right">'.number_format($line['total'],2).'</td>
                                                        <td align="right">'.number_format($line['transferred_amount'],2).'</td>
                                                        <td>'.$line['payment_reference'].'</td>
                                                        <td '.($response_date_is_different?' style="background-color:orange;" ':'').'>'.$line['response_date'].'</td>
                                                        <td>'.$line['id'].'</td>
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


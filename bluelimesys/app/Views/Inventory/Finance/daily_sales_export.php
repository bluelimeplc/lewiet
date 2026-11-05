<?php
    $payment_method_reflection = new ReflectionClass(BluelimeConfiguration::PAYMENT_METHOD);
    $payment_methods = $payment_method_reflection->getConstants();
?>
<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Finance/_left_sidebar_finance') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small><?= $title ?> Daily Sales</small>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Filter</h4>
                </div>
            </div>
            <div class="panel-body">
                <form action="" method="get">
                    <input type="hidden" name="department" value="<?= $department ?>">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-lg-1">
                                <label for="date_filter">Date:</label>
                            </div>
                            <div class="col-lg-3">
                                <input id="date_filter" type="date" name="date_filter" class="form-control" value="<?= $date_filter ?? date("Y-m-d") ?>">
                            </div>
                            <div class="col-lg-1">
                                <button class="btn btn-success">Filter</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-1">
                            <label for="date_filter">Payment Method:</label>
                        </div>
                        <div class="col-lg-3">
                            <select class="form-control" name="payment_method" id="payment_method">
                                <option <?= (($payment_method=='') ? ' selected ' : '') ?> value="">- All -</option>
                                <?php
                                    $payment_method_reflection = new ReflectionClass(BluelimeConfiguration::PAYMENT_METHOD);
                                    $payment_methods = $payment_method_reflection->getConstants();
                                    $cnt = 0;
                                    foreach($payment_methods as $index => $value){
                                        if(($value == BluelimeConfiguration::PAYMENT_METHOD::CASH) || ($value == BluelimeConfiguration::PAYMENT_METHOD::CREDIT))
                                            continue;

                                        echo '<option value="'.$index.'" '.(($index == $payment_method) ? ' selected ' : '').'>'.$value.'</option>';
                                        //echo '<label for="'.$index.'"><input required type="radio" id="'.$index.'" name="payment_method" value="'.$value.'" '.(($value == $payment_method) ? 'checked' : '').' > '.$value.'</label><br>';
                                    }
                                    // echo '<option value="CASH" '.(('CASH' == $payment_method) ? ' selected ' : '').'>Cash</option>';
                                    // echo '<option value="CREDIT" '.(('CREDIT' == $payment_method) ? ' selected ' : '').'>Credit</option>';
                                    // echo '<label for="CASH"><input required type="radio" id="CASH" name="payment_method" value="Cash" '.(($payment_method == 'Cash') ? 'checked' : '').' > Cash</label><br>';
                                ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <button style="float: right;" onclick="maintain_madal_show()"><i class="fa fa-gear"></i> Maintain</button>
                    <h4>Export to Peachtree </h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form onsubmit="return validate_export();" action="/inventory/finance/<?= $department=='Hospital'?'export_daily_sales_clinic':'export_daily_sales_pharmacy' ?>" method="POST" target="_blank" id="form_export">
                            <input type="hidden" name="filter_date" id="export_date_filter" value="<?= $date_filter ?>">
                            <input type="hidden" name="payment_method" id="export_payment_method" value="<?= $payment_method ?>">
                            <input type="hidden" name="department" id="export_department" value="<?= $department ?>">
                            <input type="hidden" name="maintain_prefix" id="export_payment_method" value="<?= $finance_maintain['data']['maintain_prefix']??'' ?>">

                            <table class="table table-bordered">
                                <tr>
                                    <td>
                                        <table class="table">
                                            <tr>
                                                <td width="120"><label for="cashier_name">Cashier Name:</label></td>
                                                <td><input type="text" class="form-control" id="cashier_name" name="cashier_name" value="<?= $finance_maintain['data']['cashier_name']??'' ?>" readonly></td>
                                            </tr>
                                            <tr>
                                                <td width="120">
                                                    <label for="cashier_gl">Cashier G/L:</label>
                                                </td>
                                                <td>
                                                    <input type="hidden" id="cashier_gl" name="cashier_gl" value="<?= $finance_maintain['data']['cashier_gl_account']??'' ?>">
                                                    <input type="text" class="form-control" value="<?= $finance_maintain['data']['cashier_gl_account']??'' ?> | <?= $finance_maintain['data']['cashier_gl_description']??'' ?>" readonly>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="120"><label for="reference">Reference:</label></td>
                                                <td><input type="text" class="form-control" id="reference" name="reference" value="<?= !empty(trim($reference))?$reference:(($finance_maintain['data']['maintain_prefix']??'')."-") ?>"></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td rowspan="2">
                                        <table class="table">
                                            <tr><td width="150">Date:</td><td><b><?= $date_filter ?></b></td></tr>
                                            <tr><td>Payment Method:</td><td><b>Bank and Cash</b></td></tr>
                                            <tr><td>TOTAL:</td><td><b><?= number_format($total,2) ?></b></td></tr>
                                            <tr>
                                                <td colspan="2">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                    <tr style="background-color: purple; color:#fff">
                                                                        <th>Payment Method</th>
                                                                        <th>Qty</th>
                                                                        <th>Amount</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody> 
                                                                    <?php
                                                                        $bank_total = 0;
                                                                        $bank_count = 0;
                                                                        $cash_total = 0;
                                                                        $cash_quantity = 0;

                                                                        foreach($pos_bills_summary as $row){
                                                                            if($row['payment_method'] == BluelimeConfiguration::PAYMENT_METHOD::CASH){
                                                                                $cash_total += $row['total'];
                                                                                $cash_quantity += $row['qty'];
                                                                                continue;
                                                                            }
                                                                            
                                                                            echo '
                                                                    <tr>
                                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;'.$row['payment_method'].'</td>
                                                                        <td align="right">'.number_format($row['qty']).'</td>
                                                                        <td align="right">'.number_format($row['transferred_amount'],2).'</td>
                                                                    </tr>
                                                                            ';
                                                                            $bank_total += $row['transferred_amount'];
                                                                            $bank_count += $row['qty'];
                                                                            $cash_total += $row['total'] - $row['transferred_amount'];
                                                                        }
                                                                    ?>
                                                                    <tr style="background-color: #eee;">
                                                                        <td><b>Bank Total</b></td>
                                                                        <td align="right"><b><?= number_format($bank_count) ?></b></td>
                                                                        <td align="right"><b><?= number_format($bank_total,2) ?></b></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;Cash</td>
                                                                        <td align="right"><?= number_format($cash_quantity) ?></td>
                                                                        <td align="right"><?= number_format($cash_total,2) ?></td>
                                                                    </tr>
                                                                    <tr style="background-color: #ccc;">
                                                                        <td><b>TOTAL</b></td>
                                                                        <td align="right"><b><?= number_format($bank_count + $cash_quantity) ?></b></td>
                                                                        <td align="right"><b><?= number_format($bank_total + $cash_total,2) ?></b></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <table class="table table-bordered">
                                                                <tr>
                                                                    <th>Duplicated FS</th><th>Missing FS</th><th>Void Receipt</th>
                                                                </tr>
                                                                <tr>
                                                                    <th style="color:white; background-color: <?= ( $validation['has_duplicate'] > 0 ? 'red':'green')?>;"><?= ($validation['has_duplicate']==0?'&check;':$validation['has_duplicate']) ?></th>
                                                                    <th style="color:white; background-color: <?= ( $validation['has_missing'] > 0 ? 'red':'green')?>;"><?= ($validation['has_missing']==0?'&check;':$validation['has_missing']) ?></th>
                                                                    <th style="color:white; background-color: <?= ( $validation['has_void'] > 0 ? 'red':'green')?>;"><?= ($validation['has_void']==0?'&check;':$validation['has_void']) ?></th>
                                                                </tr>
                                                            </table>
                                                            <button class="btn btn-success" onclick="export_to_peachtree()" >Export in CSV</button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                
                            </table>
                            <hr>
                            <div style="text-align: right;">
                                
                            </div>
                        </form>
                        <br>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Sales detail</h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-8">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>FS N<u>o</u></u></th>
                                    <th>Payment Method</th>
                                    <th>Total</th>
                                    <th>Services</th>
                                </tr>
                            </thead>
                            <tbody> 
                                <?php
                                    $cnt = 0;
                                    $prv_fs = 0;
                                    foreach($pos_bills as $row){
                                        
                                        $indicate_problem = false;

                                        $current_fs_number = intval($row['fs_number']);
                                        if($current_fs_number == 0)
                                            $current_fs_number = intval(trim(str_replace('M','',$row['fs_number'])));
                                        
                                        if($prv_fs != 0){
                                            if(($current_fs_number - $prv_fs) != 1)
                                                $indicate_problem = true;
                                        }
                                        $prv_fs = $current_fs_number;

                                        echo '
                                <tr '.($row['payment_method']==BluelimeConfiguration::PAYMENT_METHOD::CREDIT?'style="background-color: #aaa;"':'').'>
                                    <td '.($indicate_problem?'style="background-color: red;"':'').'>'.++$cnt.'</td>
                                    <td>'.$row['fs_number'].'</td>
                                    <td>'.$row['payment_method'].'</td>
                                    <td>'.$row['total'].'</td>
                                    <td>
                                        <table class="table table-striped">
                                            <tr>
                                                <th>Service Name</th>
                                                <th style="text-align:right;" title="Total Price">Price</th>
                                            </tr>';
                                        foreach($row['pos_bill_json']['sales_item_list'] as $sales){
                                            echo '
                                            <tr>
                                                <td>'.$sales['item_name'].'</td>
                                                <td align="right">'.number_format($sales['unit_price'] * $sales['quantity'],2).'</td>
                                            </tr>
                                            ';
                                        }
                                        echo '
                                        </table>
                                    </td>
                                <tr>
                                        ';
                                    }
                                ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </section>
</div>

<div>
    <!-- Maintain modal -->
    <div class="modal fade" id="maintain_madal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header dental-bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Maintain <?= $_ENV['BRANCH']??'' ?> <?= $department!='Inventory'?'Clinic':'Pharmacy' ?></h4>
                </div>
                <div class="modal-body">
                    <fieldset class="reset">
                        <legend class="reset">General</legend>
                        <table class="table table-striped">
                            <tr>
                                <td>Prefix</td><td><input type="text" id="maintain_prefix" class="form-control" value="<?= $finance_maintain['data']['maintain_prefix']??''?>"></td>
                            </tr>
                        </table>
                    </fieldset>
                    <br>
                    <fieldset class="reset">
                        <legend class="reset">Cashier</legend>
                        <table class="table table-striped">
                            <tr>
                                <th width="150">Name</th><th width="120">G/L Account</th><th>Description</th>
                            </tr>
                            <tr>
                                <td><input type="text" id="maintain_cashier_name" class="form-control" value="<?= $finance_maintain['data']['cashier_name']??''?>"></td>
                                <td><input type="text" id="maintain_cashier_gl_account" class="form-control" value="<?= $finance_maintain['data']['cashier_gl_account']??''?>"></td>
                                <td><input type="text" id="maintain_cashier_gl_description" class="form-control" value="<?= $finance_maintain['data']['cashier_gl_description']??''?>"></td>
                            </tr>
                        </table>
                    </fieldset>
                    <br>
                    <fieldset class="reset">
                        <legend class="reset">Bank Cash Account</legend>
                        <table class="table table-striped">
                            <tr>
                                <th width="100">Payment method</th><th width="120">Cash Account<br>(G/L)</th><th>Description</th>
                            </tr>
                            <?php
                                foreach($payment_methods as $index => $value){
                                    if(($value == BluelimeConfiguration::PAYMENT_METHOD::CASH) || ($value == BluelimeConfiguration::PAYMENT_METHOD::CREDIT))
                                        continue;

                                    echo '
                            <tr>
                                <td>'.$value.'</td>
                                <td><input type="text" id="maintain_cash_account_'.$index.'" class="form-control" value="'.($finance_maintain['data']['cash_account_'.$index]??'').'"></td>
                                <td><input type="text" id="maintain_description_'.$index.'" class="form-control" value="'.($finance_maintain['data']['description_'.$index]??'').'"></td>
                            </tr>
                                    ';
                                }
                            ?>
                        </table>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" onclick="update_maintain()">
                        Update
                    </button>
                </div>
            </div>
        </div>
    </div>
    
</div>

<script>
    function validate_export(){
        let cashier_name = $('#cashier_name').val().trim();
        let reference = $('#reference').val().trim();
        let cashier_gl = $('#cashier_gl').val().trim();
        
        let is_valid = true;
        if(cashier_name == ''){
            toastr['error']('Cashier name cannot be empty.');
            flash_red_inputbox('cashier_name',true);
            is_valid = false;
        }

        if((reference == '')||(reference == '-')){
            toastr['error']('Reference cannot be empty.');
            flash_red_inputbox('reference',is_valid);
            is_valid = false;
        }
        else if(reference == "<?= trim($finance_maintain['data']['maintain_prefix']??'') ?>-"){
            toastr['error']('Reference cannot be empty.');
            flash_red_inputbox('reference',is_valid);
            is_valid = false;
        }
        else if(reference.length < 8){
            toastr['error']('Reference must be at least eight character.');
            flash_red_inputbox('reference',is_valid);
            is_valid = false;
        }

        if(cashier_gl == ''){
            toastr['error']('Cashier G/L cannot be empty.');
            flash_red_inputbox('cashier_gl',is_valid);
            is_valid = false;
        }

        return is_valid;
    }

    function maintain_madal_show(){
        $('#maintain_madal').modal('show');
    }

    var maintain_data = {};
    function update_maintain(){
        maintain_data = {
            maintain_prefix :  $('#maintain_prefix').val(),
            cashier_name : $('#maintain_cashier_name').val(),
            cashier_gl_account : $('#maintain_cashier_gl_account').val(),
            cashier_gl_description : $('#maintain_cashier_gl_description').val()<?php

            foreach($payment_methods as $index => $value){
                if(($value == BluelimeConfiguration::PAYMENT_METHOD::CASH) || ($value == BluelimeConfiguration::PAYMENT_METHOD::CREDIT))
                    continue;
                echo ",
            cash_account_$index : $('#maintain_cash_account_$index').val(),
            description_$index : $('#maintain_description_$index').val()";
            }
        ?>

        };

        $.ajax({
            url: '/inventory/finance/update_maintain_daily_sales_by_payment_type',
            method: 'POST',
            data:{department:"<?= $department ?>", maintain_data: maintain_data},
            dataType: 'json',
            success:function(result){
                if(result.status == 'success')
                    location.reload();
            },
        });
    }

    $(document).ready(function(){
        if(all_are_manual)
            $('#reference').val("<?= ($finance_maintain['data']['maintain_prefix']??'').'-M'.date_format(date_create($date_filter),'ymd')?>");
    });
    
</script>
<?= view('Include/_footer') ?>
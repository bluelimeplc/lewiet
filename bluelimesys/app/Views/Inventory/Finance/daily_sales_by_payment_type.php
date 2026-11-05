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
            <small>Daily Sales</small>
            <ol class="breadcrumb">
                <li class="active"><a onclick="print_printable_document()">Print</a></li>
            </ol>
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
                                <label for="payment_method">Payment method</label>
                            </div>
                            <div class="col-lg-2">

                                <select class="form-control" name="payment_method" id="payment_method">
                                    <option disabled <?= (($payment_method=='') ? ' selected ' : '') ?>></option>
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
                                        echo '<option value="CASH" '.(('CASH' == $payment_method) ? ' selected ' : '').'>Cash</option>';
                                        echo '<option value="CREDIT" '.(('CREDIT' == $payment_method) ? ' selected ' : '').'>Credit</option>';
                                        //echo '<label for="CASH"><input required type="radio" id="CASH" name="payment_method" value="Cash" '.(($payment_method == 'Cash') ? 'checked' : '').' > Cash</label><br>';
                                    ?>
                                </select>
                            </div>
                            
                            <div class="col-lg-1">
                                <button class="btn btn-success">Filter</button>
                            </div>
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
                        <form action="/inventory/finance/export_daily_sales_bank" method="POST" target="_blank" id="form_export">
                            <input type="hidden" name="filter_date" id="export_date_filter" value="<?= $date_filter ?>">
                            <input type="hidden" name="department" id="export_department" value="<?= $department ?>">
                            <input type="hidden" name="payment_method" id="export_payment_method" value="<?= $payment_method ?>">
                            <input type="hidden" name="maintain_prefix" id="export_payment_method" value="<?= $finance_maintain['data']['maintain_prefix']??'' ?>">

                            <table class="table table-bordered">
                                <tr>
                                    <td width="120"><label for="cashier_name">Cashier Name:</label></td><td><input type="text" class="form-control" id="cashier_name" name="cashier_name" value="<?= $finance_maintain['data']['cashier_name']??'' ?>"></td>
                                    <td width="120"><label for="reference">Reference:</label></td><td><input type="text" class="form-control" id="reference" name="reference" value="<?= $finance_maintain['data']['maintain_prefix']??''?>-"></td>
                                </tr>
                                <tr>
                                    <td width="120">
                                        <label for="cashier_gl">Cashier G/L:</label>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" id="cashier_gl" name="cashier_gl" value="<?= $finance_maintain['data']['cashier_gl_account']??'' ?>">
                                        <?= $finance_maintain['data']['cashier_gl_description']??'' ?>
                                    </td>
                                    <td><label for="cash_account">Cash account:</label></td>
                                    <td>
                                        <input type="text" class="form-control" id="cash_account" name="cash_account" value="<?= $finance_maintain['data']['cash_account_'.$payment_method]??'' ?>">
                                        <?= $finance_maintain['data']['description_'.$payment_method]??'' ?>
                                    </td>
                                </tr>
                            </table>
                            <hr>
                            <div style="text-align: right;">
                                <span style="padding-left: 40px;">Date: <b><?= $date_filter ?></b></span> 
                                <span style="padding-left: 40px;">Payment Method: <b><?= (!empty($payment_method)) ? BluelimeConfiguration::PAYMENT_METHOD::{$payment_method} : '' ?></b></span> 
                                <span style="padding-left: 40px;">TOTAL: <b><?= number_format($total,2) ?></b></span> 
                            </div>
                        </form>
                        <br>
                        <button style="float: right;" class="btn btn-success" onclick="export_to_peachtree()" >Export in CSV</button>

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
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th></th><th>Invoice N<u>o</u></th><th>FS N<u>o</u></th><th>Customer Name</th><th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sn = 1;
                            $all_are_manual = true;
                            foreach($pos_bills as $row){
                                echo '
                        <tr>
                            <td>'.$sn++.'</td><td>'.$row['invoice_no'].'</td><td><a target="_blank" href="/inventory/finance/receipt?fs_no='.$row['fs_number'].'&department='.($row['type']!='Inventory'?'Hospital':'Inventory').'">'.$row['fs_number'].'</a></td><td>'.($row['pos_bill_json']['customer_name'] ?? '').'</td><td>'.number_format($row['transferred_amount'],2).'</td>
                        </tr>
                                ';
                                if($row['response_data'] != 'Manual')
                                    $all_are_manual = false;
                            }

                            if($all_are_manual)
                                echo '<script> var all_are_manual = 1; </script>';
                            else
                                echo '<script> var all_are_manual = 0; </script>';

                        ?>
                    </tbody>
                </table>
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
    function export_to_peachtree(){
        let form = $('#form_export');
        if(validate_export())
            form.submit();
    }

    function validate_export(){
        let cashier_name = $('#cashier_name').val().trim();
        let reference = $('#reference').val().trim();
        let cashier_gl = $('#cashier_gl').val().trim();
        let cash_account = $('#cash_account').val().trim();

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

        if(cash_account == ''){
            toastr['error']('Cash Account cannot be empty.');
            flash_red_inputbox('cash_account',is_valid);
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
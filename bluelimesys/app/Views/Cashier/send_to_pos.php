<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Cashier/_left_sidebar_menu') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <a href="/cashier"><h1>Cashier</h1></a>
            <small>Send to POS</small>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title">

                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-lg-4">
                                            <label class="control-label" for="customer_name">Customer Name</label>
                                        </div>
                                        <div class="col-lg-8">

                                            <div class="input-group">
                                                <input type="text" id="customer_name" class="form-control" value="<?= $pos_bill['pos_bill_json']['customer_name'] ?>">
                                                <a class=" input-group-addon btn btn-success" role="button" onclick="update_name()">Update name</a>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-lg-4">
                                            <label class="control-label" for="tin">TIN</label>
                                        </div>
                                        <div class="col-lg-8">
                                            <input type="text" id="tin" class="form-control" value="<?= $pos_bill['pos_bill_json']['tin'] ?? "" ?>">
                                        </div>
                                    </div>
                                    <!--div class="col-lg-12" style="border: solid 1px #ccc; margin: 15px; padding: 10px;">
                                        <div class="form-group row">
                                            <div class="col-lg-4">
                                                <label class="control-label" for="payment_method">Paymnet Method</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <select class="form-control" id="payment_method" onchange="ready_for_update()">
                                                        <option></option>
                                                        <?php
                                                            $payment_method_reflection = new ReflectionClass(BluelimeConfiguration::PAYMENT_METHOD);
                                                            $payment_methods = $payment_method_reflection->getConstants();
                                                            foreach($payment_methods as $payment_method){
                                                                echo '<option value="'.$payment_method.'" '.(($pos_bill['payment_method'] == $payment_method) ? 'selected' : '' ).'>'.$payment_method.'</option>';
                                                            }
                                                        ?>
                                                    </select>
                                                    <i class="input-group-addon bg-green"></i>
                                                </div>    
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-4">
                                                <label class="control-label" for="payment_reference">Payment Reference</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <input type="text" id="payment_reference" class="form-control" value="<?= $pos_bill['payment_reference'] ?>" onchange="ready_for_update()">
                                                    <i class="input-group-addon bg-violet"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="text-align:right; ">
                                            <button class="btn btn-success" style="margin:15px" onclick="update_payment_method()" id="btn_update_payment" disabled>Update payment method</button>
                                        </div>
                                    </div-->
                                </div>
                                <div class="row">
                                    <br>
                                    <div class="col-lg-12">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr><th>Item</th><th width="50">Quantity</th><th width="40">Price</th></tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $sales_line = $pos_bill['pos_bill_json']['sales_item_list'];
                                                    foreach($sales_line as $line){
                                                        echo '<tr><td>'.$line['item_name'].'</td><td>'.$line['quantity'].'</td><td>'.($line['quantity']*$line['unit_price']).'</td>';
                                                    }
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="2" align="right"><b>No of items</b></td><td><?= $payment_detail['no_of_items'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" align="right"><b>Sub total</b></td><td><?= round($payment_detail['sub_total'],2) ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" align="right"><b>Taxable total</b></td><td><?= round($payment_detail['taxable_total'],2) ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" align="right"><b>VAT</b></td><td><?= round($payment_detail['vat'],2) ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" align="right"><b>Total</b></td><td><b><u><?= round($payment_detail['total'],2) ?></u></b></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="">
                                                        <div class="form-group-sm">
                                                            <?= ($pos_bill['sent_to_pos_date'] != null) ? '<input type="checkbox" onchange="enable_resend(this)"> Enable resend to POS' : '' ?>
                                                        </div>
                                                    </td>
                                                    <td colspan="2" align="right">
                                                        <button id="btn_send_to_pos" class="btn <?= ($pos_bill['sent_to_pos_date'] != null) ? 'btn-warning' : 'btn-danger' ?>" <?= ($pos_bill['sent_to_pos_date'] != null) ? 'disabled' : '' ?> onclick="send_to_pos()"><?= ($pos_bill['sent_to_pos_date'] != null) ? 'Resend to POS' : 'Send to POS' ?></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="">
                                                        <div class="form-group-sm">
                                                            Void will reverse transaction from inventory. <br>Or remove payment from <u>doctors salary</u> after doctors already saw it. <br>
                                                            <div style="text-align: right;"><?= ($pos_bill['sent_to_pos_date'] != null) ? '<input type="checkbox" onchange="enable_void(this)"> I know what I am doing' : '' ?></div>
                                                        </div>
                                                    </td>
                                                    <td colspan="2" align="right">
                                                        <button id="btn_void" class="btn btn-danger" disabled onclick="void_payment()">Void</button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <a role="button" href="/cashier/attachment?pos_bill_id=<?= $pos_bill['id'] ?>" class="btn btn-success">Attachment</a>
                                <?= ''//view('Cashier/_fs_receipt') ?>
                        </div>
                        <div class="col-lg-2">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    function update_name(){
        if(confirm('Are you sure?')){
            let customer_name = $('#customer_name').val();
            let pos_bill_id = <?= $pos_bill['id'] ?>;

            $.ajax({
                url: '/cashier/update_name',
                method: 'POST',
                data:{customer_name:customer_name, pos_bill_id:pos_bill_id},
                dataType: 'JSON',
                success:function(result){
                    window.location = location;
                },
                error: function(jqXHR, textStatus, errorThrown){
                    toastr['error']('Name is not updated. '+errorThrown);
                }
            });
        }
    }

    function send_to_pos(){
        let customer_name = $('#customer_name').val();
        let tin = $('#tin').val();
        let payment_method = $('#payment_method').val();
        let pos_bill_id = <?= $pos_bill['id'] ?>;

        $.ajax({
            url: '/cashier/send_to_pos',
            method: 'POST',
            data:{customer_name:customer_name, tin:tin, paymnet_method:payment_method, pos_bill_id:pos_bill_id},
            dataType: 'JSON',
            success:function(result){
                window.location = location;
            },
            error: function(jqXHR, textStatus, errorThrown){
                toastr['error']('Cannot send to POS.');
            }
        });
    }

    function void_payment(){
        let x = parseInt(Math.random()*10);
        let y = parseInt(Math.random()*10);
        
        while(x<1){
            x = parseInt(Math.random()*10);
        }

        while(y<2){
            y = parseInt(Math.random()*10);
        }

        let z =prompt("Please answer this simple maths question?\n"+x+" + "+y+" = ");

        if(z!=x+y)
            return false;

        let remark = prompt("Please enter remark");

        let pos_bill_id = <?= $pos_bill['id'] ?>;

        $.ajax({
            url: '/cashier/void_payment',
            method: 'POST',
            data:{pos_bill_id:pos_bill_id, remark:remark},
            dataType: 'JSON',
            success:function(result){
                //window.location = "/cashier";
                history.back();
            },
            error: function(jqXHR, textStatus, errorThrown){
                toastr['error']('Cannot void the payment.');
            }
        });
    }

    function enable_resend(checkBox){
        let btnSendToPOS = $('#btn_send_to_pos');
        if(checkBox.checked)
            btnSendToPOS.removeAttr('disabled');
        else
            btnSendToPOS.attr('disabled',1);
    }

    function enable_void(checkBox){
        let btnVoid = $('#btn_void');
        if(checkBox.checked)
            btnVoid.removeAttr('disabled');
        else
            btnVoid.attr('disabled',1);
    }

    function update_payment_method(){
        let payment_method = $('#payment_method').val();
        let payment_reference = $('#payment_reference').val();
        let pos_bill_id = <?= $pos_bill['id'] ?>;
        let btn_update_payment = $('#btn_update_payment');

        $.ajax({
            url: '/cashier/update_payment_method',
            method: 'POST',
            data:{payment_method:payment_method, payment_reference:payment_reference, pos_bill_id:pos_bill_id},
            dataType: 'JSON',
            success:function(result){
                if(result.status == 'success'){
                    btn_update_payment.addClass('btn-success');
                    btn_update_payment.removeClass('btn-primary');
                    btn_update_payment.attr('disabled',1);
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                toastr['error']('Error. Payment method is not updated.');
            }
        });
    }

    function ready_for_update(){
        let btn_update_payment = $('#btn_update_payment');
        btn_update_payment.addClass('btn-primary');
        btn_update_payment.removeClass('btn-success');
        btn_update_payment.removeAttr('disabled');
    }
</script>
<?= view('Include/_footer') ?>
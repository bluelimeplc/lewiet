<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?php
    $logged_user = BluelimeAuthentication::get_logged_user_data();
   
    if($logged_user['role'] == BluelimeConfiguration::USER_ROLE::CASHIER_INVENTORY)
        echo view('Cashier/_left_sidebar_menu');
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
                <small>POS</small>
                <ol class="breadcrumb">
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
                        <div>
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <input type="text" value="<?= $search_key ?>" name="search_key" placeholder="Customer Name" class="form-control refocusable" id="search_key">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div style="text-align: right;"><?= $pager->links() ?></div>
                        <style>
                            .payment_row:hover .print{
                                display: inline-block;
                            }
                            .print{
                                display: none;
                            }
                            .error_input{
                                background-color: red;
                            }
                            .btn-paid{
                                background-color: green;
                                border: 2px solid green;
                            }
                        </style>
                            <table class="table table-bordered table-hover table-striped">
                                <tr>
                                    <th></th><th>Customer name<br>Reference N<u>o</u></th><th width="100">Print</th><th>Total</th><th width="150">Payment Mode</th><th width="200">Remark</th><th>FS Number<br>Invoice date</th><th width="60"></th><th width="100"></th>
                                </tr>
                            
                            <?php
                                $payment_method_color = [
                                    'Awash' => 'blue',
                                    'CBE' => 'purple',
                                    'POS' => 'orange',
                                    'Telebirr' => 'green',
                                    'Credit' => 'red',
                                ];

                                $page = $_GET['page'] ?? 1;
                                $sn = ($page-1) * BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST;
                                $digital_payment_pending = [];
                                foreach($pos_bills as $row){
                                    if(($row['digital_payment_status'] ?? '') == 'pending')
                                        $digital_payment_pending[] = $row['id'];
                                    
                                    $identification = $payment_method_color[$row['payment_method']] ?? '#ddd';
                                    $paid_date = substr(($row['updated_at']??''),0,10);
                                    $locked = false;
                                    if($paid_date < date('Y-m-d')){
                                        $locked = true;
                                    }

                                    if($logged_user['role'] == 'SUPER_ADMIN')
                                        $locked = false;
                                    
                                    $customer_name = $row['pos_bill_json']['customer_name']??'';
                                    if(strtolower(substr($customer_name,0,9)) != 'walk in -')
                                        $customer_name = "<b>$customer_name</b>";
                                    echo '
                                <tr class="payment_row">
                                    <td class="'.($locked?' bg-gray ' : '').'">'.++$sn.'</td>
                                    <td>'.$customer_name.'<br><small>'.($row['pos_bill_json']['invoice_no']??'').'</small></td>
                                    <td>
                                        <a class="print btn btn-info" href="/cashier/attachment?pos_bill_id='.$row['id'].'" target="_blank"><span class="fa fa-print"></span></a>
                                        
                                        <a class="print btn btn-warning" href="/cashier/attachment?pos_bill_id='.$row['id'].'&new=1" target="_blank"><span class="fa fa-print"></span></a>
                                    </td>
                                    <td align="right">'.number_format(($row['total']),2).'</td>
                                    <td>
                                        <div class="input-group">
                                            <a class="input-group-addon" id="payment_identification_'.$row['id'].'" style="padding:5px; background-color:'.$identification.'"></a>
                                            <select '.($locked?' disabled ' : '').' class="form-control" id="payment_method_'.$row['id'].'" onchange="change_identification(\''.$row['id'].'\'); turn_on_off_save(\''.$row['id'].'\', this, \''.$row['payment_method'].'\')">';
                                                $payment_method_reflection = new ReflectionClass(BluelimeConfiguration::PAYMENT_METHOD);
                                                $payment_methods = $payment_method_reflection->getConstants();
                                                $cnt = 0;
                                                foreach($payment_methods as $index => $payment_method){
                                                    $bank_amount = (is_null($row['transferred_amount'])==1)?round(($row['total']),2):$row['transferred_amount'];
                                                
                                                    echo '<option value="'.$payment_method.'" '.(($row['payment_method'] == $payment_method) ? 'selected' : '' ).' '.((($payment_method == 'Credit')||($payment_method == 'POS')) ? ' disabled style="color:#ccc;" ' : '' ).'>'.$payment_method.'</option>';
                                                }
                                    echo '             
                                            </select>
                                        </div>
                                        <input '.($locked?' disabled ' : '').' style="margin-top:5px;" type="number" min="1" step="1" class="form-control '.(($row['payment_method'] == 'Cash') ? ' hidden ' : '' ).'" id="bank_amount_'.$row['id'].'" value="'.$bank_amount.'" onchange="turn_on_off_save(\''.$row['id'].'\', this, \''.$bank_amount.'\'); calc_difference(\''.$row['id'].'\', '.$row['total'].')" onkeyup="turn_on_off_save(\''.$row['id'].'\', this, \''.$bank_amount.'\'); calc_difference(\''.$row['id'].'\', '.$row['total'].')">
                                    </td>
                                    <td>
                                        <input type="text" '.($locked?' disabled ' : '').' class="form-control" id="payment_reference_'.$row['id'].'" value="'.$row['payment_reference'].'" onchange="turn_on_off_save(\''.$row['id'].'\', this, \''.$row['payment_reference'].'\')" onkeyup="turn_on_off_save(\''.$row['id'].'\', this, \''.$row['payment_reference'].'\')"> 
                                    
                                        <button '.($locked?' disabled ' : '').' id="btn_update_payment_method_'.$row['id'].'" class="btn btn-purple hidden" onclick="update_payment_method('.$row['id'].','.round($row['total'],2).')" style="margin-top:5px;">Save</button>
                                    </td>
                                    '.($row['response_date'] != NULL ?'<td title="Paid"><i class="fa fa-money-bill-1-wave"></i> FS '.$row['fs_number'].'<br>'.$paid_date.'</td>':'<td><span id="fs_'.$row['id'].'"></span>'.$paid_date.'</td>').'
                                    <td>
                                        <img src="/assets/images/santim_pay_logo_01.png" class="btn btn-'.($row['digital_payment_status']=='pending'?'warning':($row['digital_payment_status']=='success'?'paid':($row['digital_payment_status']=='failed'?'danger':'default print'))).'" style="height: 35px; padding: 2px;" onclick="digital_payment_show(\'santimpay\','.$row['id'].',\''.$customer_name.'\',\''.number_format(($row['total']),2).'\')" title="Santim Pay">
                                    </td>
                                    <td>'. ($locked?' ' : '<button class="print btn btn-warning" onclick="manual_payment('.round(($row['total']),2).', \''.$row['pos_bill_json']['invoice_no'].'\', \''.$row['id'].'\', \''.$customer_name.'\')">M</button>').'
                                    '. ($locked?' ' : '<a class="print btn" ondblclick="show_detail('.$row['id'].')"><i class="fa fa-edit"></i></a>').'
                                    </td>
                                </tr>
                                    ';
                                }
                            ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- Modals -->
    <div>
        <div class="modal" data-backdrop="static" id="manual_payment_modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                    
                        <h4 class="modal-title">Receipt Posting Manually </h4>
                    </div>
                    <div class="modal-body">
                        <div id="manual_payment_detail">
                            <table class="table table-sm">
                                <tr><td width="150">Invoice N<u>o</u>:</td><td><b id="manual_payment_invoice_no"></b></td></tr>
                                <tr><td>Customer name:</td><td><b id="manual_payment_customer_name"></b></td></tr>
                                <tr><td>Amount:</td><td><b id="manual_payment_amount"></b></td></tr>
                                <tr><td>Reciept N<u>o</u>:</td><td><input type="number" class="form-control" id="manual_payment_reciept_no"> </td></tr>
                                <tr><td>Receipt Type:</td><td><select class="form-control" id="manual_payment_reciept_type"><option value="Manual">Manual</option><option value="Cash Register">Cash Regiter (Machine)</option></select> </td></tr>
                                <tr><td>Payment Date:</td><td><input type="date" <?= ($logged_user['role']=='ADMIN')||($logged_user['role']=='SUPER_ADMIN')?'':' readonly ' ?> id="manual_payment_payment_date" class="form-control" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>"> </td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="recive_payment_manually()">Save</button>
                        <!--button type="button" class="btn btn-danger"  id="dentalAlertButtonSecondary" data-dismiss="modal" >Cancel</button-->
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" data-backdrop="static" id="digital_payment_modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                    
                        <h4 class="modal-title">Digital Payment </h4>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <tr><td>Name:</td><td><b id="digital_payment_customer_name"></b></td></tr>
                            <tr><td>Amount:</td><td><b id="digital_payment_amount"></b></td></tr>
                            <tr><td>Mobile:</td><td><input type="text" class="form-control" id="digital_payment_mobile"></td></tr>
                        </table>
                       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="send_payment_link()" id="btn_send_payment_link" disabled>Send payment link</button>
                        <div id="digital_payment_result" style="word-break: break-all; text-align: left;">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        var digital_payment_pending = <?= BluelimeSystem::convert_array_to_string($digital_payment_pending) ?>;

        function digital_payment_show(method, pos_bill_id, customer_name, amount){
            $('#digital_payment_modal').data('pos_bill_id', pos_bill_id);
            $('#digital_payment_modal').data('method', method);
            $('#digital_payment_mobile').val('');
            $('#digital_payment_customer_name').html(customer_name);
            $('#digital_payment_amount').html(amount);
            $('#digital_payment_result').html('');
            $('#btn_send_payment_link').show();
            $('#digital_payment_modal').modal('show');
        }

        function send_payment_link(){
            let pos_bill_id = $('#digital_payment_modal').data('pos_bill_id');
            let method = $('#digital_payment_modal').data('method');
            let mobile = $('#digital_payment_mobile').val().trim();

            $.ajax({
                url: '/cashier/digital_payment',
                method: 'POST',
                data: {'method':method, 'pos_bill_id':pos_bill_id, 'mobile':mobile},
                dataType: 'json',
                async: false,
                success:function(result){
                    console.log(result);
                    if(result.status == 'success'){
                        if(result.value.status == 'success'){
                            let payment_url = result.value.payment_url;
                            let payment_url_short = result.value.payment_url_short;
                            $('#btn_send_payment_link').hide();
                            let result_html = '<a href="'+payment_url_short+'?t=local" target="_blank">Payment link</a><br>';
                            result_html += '<img src="/api/qrcode?data='+payment_url_short+'?t=qr"><br>';
                            $('#digital_payment_result').html(result_html);
                        }
                        else
                            toastr['error'](result.value.message);
                    }
                    else
                        toastr['error'](result.message);
                },
                error:function(jqXHR,status,error){
                    toastr['error'](error);
                }
            });
        }

        function show_detail(posill_id){
            window.location = "/cashier/payment?sales_id="+posill_id;
        }
        var selected_invoice_pos_bill_id = 0;
        function manual_payment(amount, invoice_number, pos_bill_id, customer_name){
            let modal = $('#manual_payment_modal');
            $('#manual_payment_invoice_no').html(invoice_number);
            $('#manual_payment_customer_name').html(customer_name);
            $('#manual_payment_amount').html(amount);
            selected_invoice_pos_bill_id = pos_bill_id;
            modal.modal('show');
            $('#manual_payment_reciept_no').val('');
            $('#manual_payment_reciept_no').focus();
            $('#manual_payment_reciept_type').val('Manual');
            //setTimeout("$('#manual_payment_reciept_no').focus();",1000);
        }

        function recive_payment_manually(){
            let reciept_no = $('#manual_payment_reciept_no').val().trim();
            let reciept_type = $('#manual_payment_reciept_type').val();
            let payment_date = $('#manual_payment_payment_date').val();

            if(reciept_no == ''){
                toastr['error']('Reciept number cannot be empty');
                flash_red_inputbox('manual_payment_reciept_no', true);
                return false;
            }
            $.ajax({
                url: '/cashier/manual_reciept',
                method: 'POST',
                data: {'pos_bill_id':selected_invoice_pos_bill_id, 'reciept_no':reciept_no, 'reciept_type':reciept_type,'payment_date':payment_date},
                dataType: 'json',
                async: false,
                success:function(result){
                    if(result.status == 'success'){
                        $('#fs_'+selected_invoice_pos_bill_id).html('<i class="fa fa-money"></i> M '+reciept_no + '<br>');
                        let modal = $('#manual_payment_modal');
                        modal.modal('hide');
                    }
                    else
                        toastr['error'](result.message);
                },
                error:function(jqXHR,status,error){
                    toastr['error'](error);
                }
            });
        }

        function update_payment_method(pos_bill_id, total){
            payment_method = $('#payment_method_'+pos_bill_id).val();
            payment_reference = $('#payment_reference_'+pos_bill_id).val();
            transferred_amount = $('#bank_amount_'+pos_bill_id).val();

            $.ajax({
                url: '/cashier/update_payment_method',
                method: 'POST',
                data: {'pos_bill_id':pos_bill_id, 'payment_method':payment_method, 'transferred_amount':transferred_amount, 'payment_reference':payment_reference, 'total':total},
                dataType: 'json',
                async: false,
                success:function(result){
                    if(result.status == 'success')
                        $('#btn_update_payment_method_'+pos_bill_id).addClass('hidden');
                    else
                        toastr['error'](result.message);
                },
                error:function(jqXHR,status,error){
                    toastr['error'](error);
                }
            });
        }
        var payment_method_color = <?= BluelimeSystem::convert_array_to_string($payment_method_color) ?>;

        function change_identification(id){
            let payment_method = $('#payment_method_'+id);
            let payment_identification = $('#payment_identification_'+id);

            let identification = '#ddd';
            if(payment_method_color[payment_method.val()] != undefined)
                identification = payment_method_color[payment_method.val()];
            payment_identification.css('background-color', identification);

            if(payment_method.val() == 'Cash')
                $('#bank_amount_'+id).addClass('hidden');
            else
                $('#bank_amount_'+id).removeClass('hidden');
        }

        var btn;
        function turn_on_off_save(id, changed_object, orginal_data){
            let btn_update_payment_method = $('#btn_update_payment_method_'+id);
            btn = btn_update_payment_method;
            let new_val = changed_object.value;

            if(new_val != orginal_data)
                btn_update_payment_method.removeClass('hidden');
            else
                btn_update_payment_method.addClass('hidden');
        }

        function calc_difference(id, total){
            let remark = $('#payment_reference_'+id);
            let transferred_amount = $('#bank_amount_'+id).val();

            let difference = parseFloat(total - Number(transferred_amount)).toFixed(0);

            if( (difference != 0) && ( (remark.val().trim() == '') || (!isNaN(remark.val())) ) ) {
                remark.val(difference);
            }
            else if( (difference == 0) && ( (!isNaN(remark.val()))) ) {
                remark.val('');
            }

        }

        function open_payment(url){
            location = url;
        }
    </script>
<?= view('Include/_footer') ?>


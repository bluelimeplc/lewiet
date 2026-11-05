<?php
    helper('form');
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
                <h1>Finance</h1>
                <small>Credit Payment Receipt List View</small>
                
            </div>
            
        </section>
        <!--section class="content" style="margin-bottom: -150px !important;">
            <div class="row">
                <div class="col-sm-12 col-md-12">
                    <div class="panel panel-bd">
                        <div class="panel-body">
                            <div class="panel-body">
                                <?php
                                    $attributes = array('role'=>'form', 'method'=>'post','class'=>"form-inline", 'disabled'=>"disabled");
                                    echo form_open_multipart(base_url().'inventory/finance/paymentrequestupdatecollected', $attributes);
                                ?>
                                    <input type="hidden" name="id" value="<?= $paymentRequest['id'] ?>">
                                    <div class="form-group col-md-10">
                                        <label for="remark" class="control-label">Remark</label>
                                        <input type="text" name="remark" class="form-control" id="remark" <?= (empty(trim($paymentRequest['status']))) ? "disabled": "" ?> placeholder="Check number, bank transfter number, tt number ..." style="width: 500px;" required value="<?= trim($paymentRequest['remark']); ?>">
                                        <button class="btn btn-success" type="submit" <?= (empty(trim($paymentRequest['status']))) ? "disabled": "" ?>>Update status to Collected</button><br>
                                        <?= (empty(trim($paymentRequest['status']))) ? "<small>This payment request status must be requested to be collected. Please print the payment request then you can update it to collected status.</small>": "" ?>
                                    </div>
                                    
                                <?php echo form_close() ?>
                                <button class="btn btn-danger" onclick="show_export_to_peachtree()">Export to peachtree</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section-->
                <!-- Main content -->
        <section class="content" style="width:70%;">
            <div class="row">

                <div class="col-sm-12 col-md-12">
                    <div id="printableArea">
                        <div class="panel-body" style="background-color: #fff; height: fit-content;">
                            <div class="row"> 
                                <div class="col-sm-11">
                                    <img src="/img/<?= $paymentRequest['department']=='Clinic'?'new_logo.jpeg':'gelead_logo.jpeg'?>" width="70" class="img" alt="" style="right: 0px; bottom:20px; position: absolute;">
                                    <h4  style="text-align: center;">
                                        <br><?= $paymentRequest['department']=='Clinic'?'RANK Specialized Dermatology Clinic':'Gelead Pharmacy'?> <u>#<?= $branch_no ?></u>
                                        <!--br><br><u>Credit Sales for the Month of 
                                            <span contenteditable="true" id="month_name"><?= $paymentRequest['month_name'] ?></span></u> <a onclick="show_edit_report_info_modal()" style="cursor: pointer; margin-left: 20px; color: orange;" id="btn_edit_text1"><span class="fa fa-edit"></span></a-->
                                    </h4>
                                </div>
                            </div>
                            <hr>
                            <strong> <?php echo "Company:"?></strong>&emsp;<u id="company_title"><?php echo $paymentRequest['company_name'] ?></u> 
                                <!--a onclick="show_edit_report_info_modal()" style="cursor: pointer; margin-left: 20px; color: orange;" id="btn_edit_text2"><span class="fa fa-edit"></span></a><br-->
                            <hr style="border: solid 2px;">
                            <div>
                                <?php 
                                    if($paymentRequest['department']=='Clinic')
                                        echo view('Inventory/Finance/_payment_request_view_clinic', ['receipt_list' => $receipt_list]);
                                    elseif($paymentRequest['department']=='Pharmacy')
                                        echo view('Inventory/Finance/_payment_request_view_pharmacy', ['receipt_list' => $receipt_list]);
                                ?>
                            </div>
                            <div style=" bottom: 0px; text-align: center; width:100%">
                                <div style="text-align: right; padding: 5px;">
                                    <u>Prepared by : <b><?= $paymentRequest['registered_by_name'] ?></b></u><br><br>
                                </div>
                                <small style="color: #eee !important; position: fixed; bottom: -2px; margin-left: -100px;">System Developed By bluelimeplc.com</small>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-left">
                        <!--a class="btn btn-info" href="#" onclick="printContent()"><span class="fa fa-print"></span></a-->
                    </div>
                </div>
            </div>   
        </section>

    </div> <!-- /.content-wrapper -->

    <div>
        <!--  -->
        <div class="modal fade" id="edit_report_info_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Report Info</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped">
                            <tr>
                                <td>Company Name</td>
                                <td><?= $paymentRequest['company_name'] ?></td>
                            </tr>
                            <tr>
                                <td>For the month of</td>
                                <td><input type="text" maxlength="20" class="form-control" id="month_name_edit" value="<?= $paymentRequest['month_name'] ?>"></td>
                            </tr>
                            <tr>
                                <td>Company Title</td>
                                <td><input type="text" maxlength="20" class="form-control" id="company_title_edit" value="<?= ($paymentRequest['company_title']!='')?$paymentRequest['company_title']:$paymentRequest['company_name'] ?>"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" onclick="update_report_info()">
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="export_to_peachtree_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Export to peachtree</h4>
                    </div>
                    <div class="modal-body">
                        <form method="post" target="_blank" action="<?= base_url('inventory/finance/exportpaymentrequest') ?>">
                            <input type="hidden" name="id" value="<?= $paymentRequest['id'] ?>">
                            <table class="table table-striped">
                                <tr>
                                    <td>Document N<u>o</u></td>
                                    <td><input type="text" maxlength="20" class="form-control" name="document_number" placeholder="RANK/xxx/xx" required minlength="6"></td>
                                    <td>
                                        <button class="btn btn-danger">Export</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Date</td>
                                    <td><input type="date" class="form-control" name="date" required></td>
                                    <td>
                                        
                                    </td>
                                </tr>

                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var del = false;
        function delete_undelete(button, type, receipt_id, payment_request_id){
            let button_html = $(button).html();
            $.ajax({
                url:'/inventory/finance/remove_revert_receipt_from_credit_receipt_group',
                method:'post',
                dataType:'json',
                data:{type:type, receipt_id:receipt_id, payment_request_id:payment_request_id},
                beforeSend:function(){
                    $(button).attr('disabled', true);
                    $(button).html('<i class="fa fa-spinner fa-spin"></i>');
                },
                success:function(result){
                    if(result.status == "success"){
                        if(result.value == "deleted"){
                            $(button).closest('tr').css("background-color","#f88");
                            $(button).closest('tr').css("text-decoration","line-through");
                            $(button).html('<i class="fa fa-reply"></i>');
                            $(button).toggleClass('btn-danger btn-primary');
                        }
                        else{
                            $(button).closest('tr').css("background-color","");
                            $(button).closest('tr').css("text-decoration","none");
                            $(button).html('<i class="fa fa-trash"></i>');
                            $(button).toggleClass('btn-danger btn-primary');
                        }
                    }
                    else{
                        $(button).html(button_html);
                        toastr['error'](result.message);
                    }
                },
                error:function(xhr, status, error){
                    console.error('Error generating invoice:', error);
                    toastr['error']('Failed to delete. '+error);
                    $(button).html(button_html);
                },
                complete:function(){
                    $(button).attr('disabled', false);
                }
            });
        }

        function show_export_to_peachtree() {
            $('#export_to_peachtree_modal').modal('show');
        }
        
        var do_after_edit = false;
        function show_edit_report_info_modal(){
            do_after_edit = false;
            show_edit_report_info_modal_only();
        }

        function show_edit_report_info_modal_only(){
            $('#edit_report_info_modal').modal('show');
            setTimeout("$('#month_name_edit').focus()",500);
        }

        function update_report_info(){
            let month_name = $('#month_name_edit').val();
            let company_title = $('#company_title_edit').val();
            if(month_name.trim() == ''){
                flash_red_inputbox('month_name_edit');
                return false;
            }
            if(company_title.trim() == ''){
                flash_red_inputbox('company_title_edit');
                return false;
            }

            $.ajax({
                method: 'post',
                url: "/inventory/finance/updatepaymentrequestreportinfo",
                data: {month_name: month_name, company_title: company_title, id: <?= $paymentRequest['id'] ?>},
                dataType: 'json',
                async: false,
                success: function(result){
                        if(result.status == 'success'){
                            $('#edit_report_info_modal').modal('hide');
                            $('#month_name').html(month_name);
                            $('#company_title').html(company_title);
                            if(do_after_edit === 'doPrint')
                                doPrint();
                            else if(do_after_edit === 'updatePaymentRequestStatus')
                                updatePaymentRequestStatus();
                        }
                        else
                            toastr['error'](result.message, 'Error');
                },
                error:function(jqXHR, textStatus, errorThrown){
                    toastr['error'](failMessage + errorThrown, 'Error');
                }
            });
        }

        function validate_report_info(do_after_edit_param){
            // do_after_edit = do_after_edit_param;
            // let month_name = $('#month_name').html();
            // let company_title = $('#company_title').html();

            // if((month_name.trim() == '') || (company_title.trim() == '')){
            //     show_edit_report_info_modal_only();
            //     return false;
            // }
            return true;
        }

        var body, restorepage;
        function doPrint(){

            if(!validate_report_info('doPrint'))
                return false;
            
            restorepage  = $('body').html();
            $('#btn_edit_text1').css('display', 'none');
            $('#btn_edit_text2').css('display', 'none');
            var printcontent = $('#printableArea').clone();
            
            body = $('body');
            body.empty().html(printcontent);
            setTimeout("window.print();body.html(restorepage);location.reload();", 500);
        }

        var update_status = false;
        function updatePaymentRequestStatus()
        {
            // if(!validate_report_info('updatePaymentRequestStatus'))
            //     return false;
            
            // failMessage = "Error on updating status of payment request to 'Requested'.<br>The payment request cannot be printed.<br><br><u>ERROR</u>:<br>";
            // $.ajax({
            //     method: 'post',
            //     url: "<?php echo base_url(); ?>hospital/ajax/updatepaymentrequeststatus",
            //     data: {"id":<?=$paymentRequest['id']?>, "status":"Requested"},
            //     success: function(result){
            //         if(result != null){
            //             if(result == 1)
            //                 doPrint();
            //             else
            //                 dental_show_alert(failMessage, "OK", "WARNING");
            //         }
            //         else
            //             dental_show_alert(failMessage, "OK", "WARNING");
            //     },
            //     error:function(jqXHR, textStatus, errorThrown){
            //         dental_show_alert(failMessage + errorThrown, "OK", "WARNING");
            //     }
            // });
        }

        function printContent(){
            <?php
                if(!empty(trim($paymentRequest['status'])))
                    echo 'doPrint();';
                else
                    echo 'updatePaymentRequestStatus();';
            ?>
        }
        <?php
            $fail = $_GET['fail'] ?? false;
            if($fail)
                echo 'dental_show_alert("There was an error updating the status.", "OK", "WARNING");';
        ?>
    </script>

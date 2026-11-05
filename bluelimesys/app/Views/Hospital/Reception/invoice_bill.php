<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $patient = $this->data['patient'];
    $patient = $this->data['patient'];
    $previously_payed = $this->data['previously_payed'];
    $bill = $this->data['bill_info'];
    $bill_detail = $this->data['bill_detail'];
    $do_not_open_visit = $this->data['do_not_open_visit'];
    $mrn_no = $bill['patient_id'];
    $formated_mrn_no = sprintf('%05d', $mrn_no);
    //$unpaid_bill = $this->data['unpaid_bill']; 
    $bill_payments = $this->data['bill_payments'];
    //$is_external= @$external_order[0]['is_external'];

    $error_code = $_GET['error_code'] ?? '';
   
?>
<!-- Invoice js -->
<script src="<?php echo base_url()?>assets/my-assets/service.js.php" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/my-assets/invoice.js" type="text/javascript"></script>
<style>
    input[type="text"] {
  background: transparent;
  border: none;
}

    input.form-control:read-only {
        background-color: #fff;
    }
</style>
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>

        <div class="header-title">
            <h1><?php echo 'Reception'; ?></h1>
            <small><?php echo 'Invoice'; ?></small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/reception/pendingbills"><?php echo "Pending Bills"?></a></li>
            </ol>
        </div>
    </section>
    
    
    
    

    <!-- Main content -->
    <section class="content">
	    <div class="row">
            <div class="col-sm-12">

            
                <!--Open Bills ===========================================================================-->
                <div class="panel panel-bd" <?php echo (!empty($open_bills)) && ($bill['patient_id']!=0)?"":"hidden" ?>>
                    <div class="panel-body jump_dental">
                        <?php 
                            foreach($open_bills as $open_bill):
                        ?>
                        <div class="col-md-3">
                            <div class="panel panel-bd">
                                <a href="<?php echo base_url();?>hospital/reception/invoicebill?id=<?=$open_bill['id']?>">
                                    <div class="panel-body">
                                        <div class="statistic-box">
                                            <h4>Due: <span class="count-number"><?= $open_bill['due_amount']?></span> </h4>
                                                <div class="small"><?= $open_bill['billed_date'] ?> </div>
                                            <div class="sparkline1 text-center"></div>
                                            
                                        </div>
                                        <div class="icon"><i class="fa fa-file" style="color:red"></i></div>
                                    </div>
                                    
                                </a>    
                            </div>
                        </div>
                        <?php
                        endforeach;
                        ?>
                    </div>
                </div>
            
                <!--Open bills end ===================================================================-->


                <div class="panel panel-bd ">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <?php
                                if($bill['bill_status'] == BluelimeConfiguration::BILL_PENDING)
                                    echo '<a style="float: right; color:white;" title="Request for deletion" role="button" class="btn btn-warning" onclick="request_for_deletion()"><i class="fa fa-trash"></i></a>';
                                elseif($bill['bill_status'] == BluelimeConfiguration::BILL_REMOVE_REQUESTED )
                                    echo '<a style="float: right; color:white;" title="Cancel for deletion" role="button" class="btn bg-green text-white" onclick="cancel_request_for_deletion()"><i class="fa fa-reply"></i><i class="fa fa-trash-o"></i></a>';
                                else    
                                    echo '<a style="float: right; color:white;" class="btn btn-warning ext-white" title="Cannot request for deletion. Bill is sent to POS." disabled><i class="fa fa-trash"></i></a>';
                            ?>
                            <h5 style="float:right"><?php echo "Order By: ".$bill['registered_by_name']?> &nbsp;&nbsp;&nbsp;</h5>

                            <h4><?php echo "Invoice"?></h4>
                            
                        </div>
                    </div>
                    <?php 
                        helper('form');
                        $att = array('name'=>'save_invoice','class'=>'form-vertical','id'=>'save_invoice','onsubmit'=>'return validatetin()');
                        echo form_open_multipart('/hospital/reception/send_to_pos',attributes: $att);
                    ?>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-7">



<!-- =============================================== -->
<!-- Patient Information -->


                                    <div class="form-group row">

                                        <label  class="col-sm-3 col-form-label"><?php echo "Company Name"?> <i class="text-danger"></i></label>
                                        <div class="col-sm-7">
                                            <input class="form-control" size="50" readonly name="company_name"  value="<?php echo $bill['company_name'] ?>" type="text">
                                            <input name="company_id"  value="<?php echo $bill['company_id'] ?>" type="hidden">
                                        </div>

                                        <label  class="col-sm-3 col-form-label"><?php echo "Card No"?> <i class="text-danger"></i></label>
                                        <div class="col-sm-7">
                                            <input class="form-control" size="50" readonly name=""  value="<?php echo "MRN-".$formated_mrn_no ?>" type="text">
                                        </div>
                                    
                                        <label  class="col-sm-3 col-form-label"><?php echo "Patient Name"?> <i class="text-danger"></i></label>
                                        <div class="col-sm-7">
                                            <input class="form-control" size="50" readonly name="patient_name"  value="<?php echo $bill['patient_name'] ?>" type="text">
                                            <input name="patient_id"  value="<?php echo $bill['patient_id'] ?>" type="hidden">
                                            <input name="payment_type"  value="<?php echo $bill['bill_type'] ?>" type="hidden">
                                        </div>
                                    
                                       
                                    </div>

                                    
                            </div>
                                            



<!-- =============================================== -->
<!-- Bill Information -->

                        
                          
                        	<div class="col-md-5">
                                <div class="form-group row">
                                    <label  class="col-sm-4 col-form-label"><?php echo "Date"?> <i class="text-danger"></i></label>
                                    <div class="col-sm-6">
                                           <input class="form-control" size="50" readonly name="date" id="date"  value="<?php echo $bill['billed_date'] ?>" type="text">
                                    </div>
                                
                                    <label  class="col-sm-4 col-form-label"><?php echo 'Bill Number'?> <i class="text-danger"></i></label>
                                    <div class="col-sm-6">
                                    <input type="hidden" value="<?php echo $bill['id'] ?>" name="bill_id" id="bill_id">
                                    <input type="hidden" value="<?php echo $logged_user_name ?>" name="registered_by_name" >
                                    <input type="hidden" value="<?php echo $logged_user ?>" name="registered_by" >
                                    <input class="form-control" size="50" readonly name="date" id="date"  value="<?php echo sprintf('%05d', $bill['id']); ?>" type="text">
                                    </div>
                                    <label  class="col-sm-4 col-form-label"><?php echo "Bill Type"?> <i class="text-danger"></i></label>
                                    <div class="col-sm-6">
                                        <input class="form-control" size="50" readonly name="mobile"  value="<?php echo $bill['mobile'] ?>" type="hidden">
                                        &nbsp;&nbsp;&nbsp;<?php echo $bill['bill_type'] ?>
                                        <?php
                                            if(($patient['company_id']??0) > 0){
                                                if($bill['bill_type']=='Credit')
                                                    echo '<a role="button" class="btn bg-green text-white" '.(($bill['bill_status']==BluelimeConfiguration::BILL_PENDING)? ' onclick="change_to_cash_credit(1)" ':' disabled ').'>Change to cash <i class="fa fa-money"></i></a>';
                                                else
                                                    echo '<a role="button" class="btn bg-violet text-white" '.(($bill['bill_status']==BluelimeConfiguration::BILL_PENDING)? ' onclick="change_to_cash_credit(0)" ':' disabled ').'><i class="fa fa-reply"></i> Change to credit</a>';
                                            } 
                                        ?>
                                    </div>
                                    <!--label  class="col-sm-4 col-form-label"><?php echo "FS Number"?> <i class="text-danger"></i></label>
                                    <div class="col-sm-6">
                                    <input class="form-control"  name="fs_number" required  type="text" placeholder="xxxxx">
                                    </div--> 
                                    
                                    
                                    <!--label  class="col-sm-4 col-form-label"><?php echo "Payment Mode"?> <i class="text-danger"></i></label> 

                                    <div class="col-sm-6">

                                        <select class="form-control" required name="payment_mode">
                                            <option value="">--</option>
                                            <option value= "CASH">CASH</option>
                                            <option value= "CREDIT">CREDIT</option>
                                            <option value= "POS">POS</option>
                                            <option value= "TELEBIRR">TELEBIRR</option>
                                            <option value= "CBE">CBE</option>
                                            <option value= "BOA">BOA</option>
                                            <option value= "DASHEN">DASHEN</option>
                                            <option value= "ZEMEN">ZEMEN</option>
                                            <option value= "AWASH">AWASH</option>
                                            <option value= "BERHAN">BERHAN</option>
                                            <option value= "COOP">COOP</option>
                                            <option value= "ABAY">ABAY</option>
                                            <option value= "ADDIS">ADDIS</option>
                                            <option value= "AMHARA">AMHARA</option>
                                            <option value= "AHADU">AHADU</option>
                                            <option value= "BUNNA">BUNNA</option>
                                            <option value= "ENAT">ENAT</option>
                                            <option value= "GLOBAL">GLOBAL</option>
                                            <option value= "HIBRET">HIBRET</option>
                                            <option value= "LION">LION</option>
                                            <option value= "NIB">NIB</option>
                                            <option value= "OROMIA">OROMIA</option>
                                            <option value= "GADAA">GADAA</option>
                                            <option value= "TSEDY">TSEDY</option>
                                            <option value= "TSEHAY">TSEHAY</option>
                                            <option value= "WEGAGEN">WEGAGEN</option>
                                            <option value= "SIINQEE">SIINQEE</option>
                                            <option value= "RAMMIS">RAMMIS</option>
                                            <option value= "HIJRA">HIJRA</option>
                                            <option value= "ZAMZAM">ZAMZAM</option>
                                            <option value= "SIKET">SIKET</option>
                                            <option value= "SHABELLE">SHABELLE</option>
                                        </select>
                                    </div-->   
                                </div>
                                </div>
                            </div>
                        </div>



<!-- =============================================== -->
<!-- Invoice list Header -->

                    <div class="col-md-12">
                        <div class="table-responsive" style="margin-top: 10px">
                            <table class="table table-bordered table-hover" >
                                <thead>
                                    <tr>
                                        <th class="text-center"><?php echo 'Service Name'; ?> </th>
                                        <th class="text-center" width="100"><?php echo 'Quantity'; ?> </th>
                                        <th class="text-center"width="100"><?php echo 'Price'; ?> </th>
                                        <?php
                                        $rtdiscount="hidden";
                                        foreach($bill_detail as $detail):
                                            if ( $detail['bill_discount'] >= 1 )
                                            $rtdiscount="";
                                        endforeach;
                                        ?>
                                        <th class="text-center" width="100"<?php echo $rtdiscount ?>><?php echo 'Discount(%)'; ?></th>
                                        <th class="text-center" width="150"><?php echo 'Total'; ?> </th>
                                        
                                    </tr>
                                </thead>



<!-- =============================================== -->
<!-- Invoice list --> 



                                <tbody>
                                    <?php
                                        $total = 0;
                                        $cnt = 0;
                                        $rt = false;
                                        $total_discount=0;
                                        foreach($bill_detail as $detail):
                                            $cnt++;
                                            if ( $detail['service_type'] =="Consultation" ) 
                                            $rt = $detail;
                                           // \BluelimeSystem::show_and_die($rt);   
                                    ?>
                                    <tr class="<?php echo ($detail['service_status']==-1)?"btn danger":"" ?>">
                                        <td class="text-right"><?= $detail['service_name']?></td>
                                        <td class="text-right"><?= $detail['bill_quantity']?></td>
                                        <td class="text-right"><?= $detail['bill_amount']?></td>
                                        <td class="text-right" <?php echo $rtdiscount;  ?>><?= $detail['bill_discount']?></td>
                                        <td class="text-right"><?= ($detail['bill_amount']*$detail['bill_quantity']-(($detail['bill_amount']*$detail['bill_quantity'])*$detail['bill_discount']/100));?></td>
                                        <td class="text-right" width="20"><button style="text-align: right;" class="btn btn-danger" type="button"  
                                        <?= ($detail['service_type']=='Consultation') || ($bill['sent_to_pos']==1)?"disabled":""?> onclick="if(confirm('Are you sure?')) delayService(<?= $detail['id']?>,<?= $detail['service_status']?>);" >
                                        <?php echo ($detail['service_status']==-1)?"Restore":"Delay" ?></button></td>
                                    </tr>
                                    <?php
                                    if($detail['service_status']!=-1){
                                        
                                        $total+= $detail['bill_amount']*$detail['bill_quantity']-(($detail['bill_amount']*$detail['bill_quantity'])*$detail['bill_discount']/100);
                                        $total_discount = (($detail['bill_amount']*$detail['bill_quantity'])*$detail['bill_discount']/100);
                                    }     
                                    endforeach;
                                       // \BluelimeSystem::show_and_die($total)
                                    ?>
                                <tfoot>
                                        
                                        

<!-- =============================================== -->
<!-- Invoice Footer -->



                                    <tr <?php echo ($total_discount==0)?"hidden":" " ?>>
                                        <td colspan="<?=($rtdiscount=="hidden")?3:4 ?>" style="text-align:right;"><b><?php echo 'Total Discount'; ?>:</b></td>
                                        <td class="text-right">
                                            <input class="form-control text-right" name="total_discount" value="<?php echo $total_discount ?>" readonly="readonly" type="text">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="<?=($rtdiscount=="hidden")?3:4 ?>" style="text-align:right;"><b><?php echo 'Grand Total'; ?>:</b></td>
                                        <td class="text-right">
                                            <input id="grandTotal" tabindex="-1" class="form-control text-right" name="grand_total_price" value="<?php echo $total ?>" readonly="readonly" type="text">
                                            <input id="paidAmount" required min="1" max="<?php echo $total-@$previously_payed[0] ?>" onkeyup="invoice_paidamount();" tabindex="-1" class="form-control text-right" <?php echo (($rt==True AND $cnt == 1))?"readonly":"" ; ?> name="paid_amount" value="<?php echo $total-@$previously_payed[0] ?>" type="hidden">
                                            <input id="dueAmmount" class="form-control text-right" name="due_amount" value="0" readonly="readonly" type="hidden">
                                        </td>
                                       
                                    </tr>

                                    <!--tr <?php echo ($previously_payed==0)?"hidden":" " ?>>
                                        <td style="text-align:right;" colspan="<?=($rtdiscount=="hidden")?3:4 ?>"><b><a href="#"   data-toggle='modal' data-target="#previousPayments"><div><i class="fa fa-eye" style="font-size: 20px"></i> &emsp13;<?php echo 'Previous Payment'; ?>:</div></a></b></td>
                                        <td class="text-right">
                                            <input id="PreviousAmount" readonly tabindex="-1" class="form-control text-right" name="previously_payed" value="<?php echo @$previously_payed[0] ?>" type="text">
                                        </td>
                                    </tr-->

                                    <!--tr>
                                        <td style="text-align:right;" colspan="<?=($rtdiscount=="hidden")?3:4 ?>"><b><?php echo 'Paid Amount'; ?>:</b></td>
                                        <td class="text-right">
                                            <input id="paidAmount" required min="1" max="<?php echo $total-@$previously_payed[0] ?>" onkeyup="invoice_paidamount();" tabindex="-1" class="form-control text-right" <?php echo (($rt==True AND $cnt == 1))?"readonly":"" ; ?> name="paid_amount" value="<?php echo $total-@$previously_payed[0] ?>" type="number">
                                        </td>
                                    </tr-->

                                    

                                    <!--tr>
                                        
                                        <td style="text-align:right;" colspan="<?=($rtdiscount=="hidden")?3:4 ?>"><b><?php echo 'Due'; ?>:</b></td>
                                        <td class="text-right">
                                            <input id="dueAmmount" class="form-control text-right" name="due_amount" value="0" readonly="readonly" type="text">
                                        </td>
                                    </tr-->

                                </tfoot>
                            </table>
                        </div>
                    </div>
<!-- =============================================== -->
<!-- Save Invoice Information -->



                        <div class="form-group row">
                            <div class="col-sm-offset-4 col-sm-4">
                                
                                <input type="hidden" name="do_not_open_visit" value = "<?= $do_not_open_visit ?>" />
                                    
                                    <div class="col-sm-6">
                                        <input style="border: 2px solid #555;" class="form-control"  name="tin"   id="tin"   type="text" maxlength="10" pattern="[0-9]+" placeholder="Tin Number">
                                    </div> 
                                <button class="btn btn-<?= ($bill['bill_status'] != BluelimeConfiguration::BILL_PENDING) ? 'warning' : 'success'?>"  type="submit" <?= ($bill['bill_status'] != BluelimeConfiguration::BILL_PENDING) ? 'disabled' : ''?>>Send to cashier</button>
                            </div>
                            <div class="col-md-3" <?= ($order_comment=='')?' style="display: none;" ':'' ?>;>
                                <div class="panel panel-bd ">
                                    <div class="panel-heading" style="background-color: #ff0;">
                                        <div class="panel-title">
                                            <h4>Order Comments</h4>
                                        </div>
                                    </div>
                                    <div class="panel-body" style="background-color: #ffa;">
                                        <?= str_replace("\n","<br>",($order_comment)) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                   <?php echo form_close();?>
                                
                </div>
            </div>
        </div>
    </section>
</div>


                                                    <div class="modal fade" id="previousPayments" tabindex="-1" role="dialog" aria-labelledby="CancelAppointment" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                <div class="panel panel-bd">
                                        <div class="panel-body">
                                                <i>Payment History</i>
                                                <div class="panel panel-bd lobidisable">
                                                        
                                                        <div class="panel-body">
                                                            <div class="form-body">
                                                                <table class="table table-hover">
                                                                <tr><th>Date<th>Amount</th><th>Receipt No</th></th>
                                                                <?php
                                                                    foreach($bill_payments as $payment):
                                                                ?>
                                                                
                                                                <tr>
                                                                    <td><a href="/hospital/reception/viewreceipt?pid=<?php echo $payment['id']?>"><div><?php echo date('d/m/Y', strtotime($payment['payed_date'])) ?></div></a></td>
                                                                    <td><a href="/hospital/reception/viewreceipt?pid=<?php echo $payment['id']?>"><div><?php echo $payment['payed_amount'] ?></div></a></td>
                                                                    <td><a href="/hospital/reception/viewreceipt?pid=<?php echo $payment['id']?>"><div><?php echo sprintf('%05d', $payment['id'])?></div></a></td>
                                                                </tr>
                                                                
                                                                <?php
                                                                endforeach;
                                                                ?>
                                                                </table>
                                                            </div>
                                                        </div>
                                                </div>
                                        </div>
                                    </div>
                                                                        
                                                                    <div class="modal-footer">
                                                                        
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                                                                        
                                                                    </div>
                                                            
                                                            </div>
                                                        </div>
                                                    </div>

<script>


    function delayService(bill_detail_id,service_status){
                    
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/ajax/delayservice",
            data: {"bill_detail_id":bill_detail_id, "service_status":service_status},
            async:false,
            success: function(result){
                location.reload();
            }
        });
        location.reload();
    }
    function validatetin(){
                        let tin = $("#tin").val();
                        
                        if(tin.length == 10){ 
                                return true;
                        }
                        else if(tin == ''){ 
                                return true;
                        }
                        else{               
                            toastr['error']("Incorrect Tin Number");		
                            return false;  
                        }
                        
                        
                    }
    function request_for_deletion(){
        if(confirm("Are you sure?")){
            location = "/hospital/reception/request_for_deletion?bill_id=<?=  $bill['id'] ?>";
        }
    }

    function cancel_request_for_deletion(){
        location = "/hospital/reception/cancel_request_for_deletion?bill_id=<?=  $bill['id'] ?>";
    }

    function change_to_cash_credit(){
        if(confirm("Are you sure?")){
            location = "/hospital/reception/change_to_cash_credit?bill_id=<?=  $bill['id'] ?>&change_to_cash=<?= ($bill['bill_type'] == 'Cash')?'0':'1' ?>";
        }
    }

</script>
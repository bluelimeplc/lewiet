<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $patient = $this->data['patient'];
    $doctors = $this->data['doctors'];
    $bill = $this->data['bill_info'];
    $bill_detail = $this->data['bill_detail'];
    $mrn_no = $bill['patient_id'];
    $formated_mrn_no = sprintf('%05d', $mrn_no);
    $bill_payment = $this->data['bill_payment'];
    $bill_payments = $this->data['bill_payments'];
    $previously_payed = $this->data['previously_payed'];
    //BluelimeSystem::show_and_die($this->data['bill_detail']);
    
   
?>
<!-- Invoice js -->
<script src="<?php echo base_url()?>assets/my-assets/service.js.php" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/my-assets/invoice.js" type="text/javascript"></script>
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

          



            <!--Previous Bills ===========================================================================-->
            <div class="panel panel-bd col-sm-6">
                        <div class="panel-body">
                                        <i>Previous Payments</i>
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
                        </div>
                    </div>
            <!--Previous bills end ===================================================================-->


                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo "Invoice"?></h4>
                        </div>
                    </div>
                    <?php 
                        helper('form');
                        $att = array('name'=>'save_invoice','class'=>'form-vertical','id'=>'save_invoice');
                        echo form_open_multipart('/hospital/reception/recievepayment',$att);
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
                                    
                                        <label  class="col-sm-3 col-form-label"><?php echo "Mobile"?> <i class="text-danger"></i></label>
                                        <div class="col-sm-7">
                                            <input class="form-control" size="50" readonly name="mobile"  value="<?php echo $bill['mobile'] ?>" type="text">
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
                                    <label  class="col-sm-4 col-form-label"><?php echo "FS Number"?> <i class="text-danger"></i></label>
                                    <div class="col-sm-6">
                                    <input class="form-control"  name="fs_number"   type="text" placeholder="xxxxx" readonly ">
                                    </div> 
                                    <label  class="col-sm-4 col-form-label"><?php echo "Tin"?> <i class="text-danger"></i></label>
                                    <div class="col-sm-6">
                                    <input class="form-control"  name="tin"   type="text" readonly placeholder="xxxxxxxxxx" value="">
                                    </div>   
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
                                        <th class="text-center"><?php echo 'Quantity'; ?> </th>
                                        <th class="text-center"><?php echo 'Price'; ?> </th>
                                        <?php
                                        $rtdiscount="hidden";
                                        foreach($bill_detail as $detail):
                                            if ( $detail['bill_discount'] >= 1 )
                                            $rtdiscount="";
                                        endforeach;
                                        ?>
                                        <th class="text-center" ><?php echo 'Discount(%)'; ?></th>
                                        <th class="text-center"><?php echo 'Total'; ?> </th>
                                    </tr>
                                </thead>



<!-- =============================================== -->
<!-- Invoice list --> 



                                <tbody>
                                    <?php
                                        $total = 0;
                                        $rt = false;
                                        foreach($bill_detail as $detail):
                                            if ( $detail['service_type'] =="Consultation" ) 
                                            $rt = $detail;
                                           // \BluelimeSystem::show_and_die($rt);   
                                    ?>
                                    <tr class="<?php echo ($detail['service_status']==-1)?"btn danger":"" ?>">
                                        <td class="text-right"><?= $detail['service_name']?></td>
                                        <td class="text-right col-md-2"><?= $detail['bill_quantity']?></td>
                                        <td class="text-right col-md-2"><?= $detail['bill_amount']?></td>
                                        <td class="text-right col-md-2" <?php echo $rtdiscount ?>><?= $detail['bill_discount']?></td>
                                        <td class="text-right col-md-2"><?= ($detail['bill_amount']*$detail['bill_quantity']-(($detail['bill_amount']*$detail['bill_quantity'])*$detail['bill_discount']/100));?></td>
                                        
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
                                        <td colspan="4" style="text-align:right;"><b><?php echo 'Total Discount'; ?>:</b></td>
                                        <td class="text-right">
                                            <input class="form-control text-right" name="total_discount" value="<?php echo $total_discount ?>" readonly="readonly" type="text">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="text-align:right;"><b><?php echo 'Grand Total'; ?>:</b></td>
                                        <td class="text-right">
                                            <input id="grandTotal" tabindex="-1" class="form-control text-right" name="grand_total_price" value="<?php echo $total ?>" readonly="readonly" type="text">
                                        </td>
                                    </tr>

                                    <tr <?php echo ($previously_payed==0)?"hidden":" " ?>>
                                        <td style="text-align:right;" colspan="4"><b><?php echo 'Previous Payment'; ?>:</td>
                                        <td class="text-right">
                                            <input id="PreviousAmount" readonly tabindex="-1" class="form-control text-right" name="previously_payed" value="<?php echo @$previously_payed[0] ?>" type="text">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="text-align:right;" colspan="4"><b><?php echo 'Remaining Amount'; ?>:</b></td>
                                        <td class="text-right">
                                            <input id="paidAmount" readonly min="1" max="<?php echo $total-@$previously_payed[0] ?>" onkeyup="invoice_paidamount();" tabindex="-1" class="form-control text-right" <?php echo (($rt==True)or$bill['company_id']>=1)?"readonly":"" ; ?> name="paid_amount" value="<?php echo $total-@$previously_payed[0] ?>" type="number">
                                        </td>
                                    </tr>

                                    

                                    <tr>
                                        <td align="center">`    
                                            
                                        </td>
                                        <td style="text-align:right;" colspan="3"><b><?php echo 'Due'; ?>:</b></td>
                                        <td class="text-right">
                                            <input id="dueAmmount" class="form-control text-right" name="due_amount" value="0" readonly="readonly" type="text">
                                        </td>
                                    </tr>

                                </tfoot>
                            </table>
                        </div>
                    </div>                       



<!-- =============================================== -->
<!-- Save Invoice Information -->



                        <div class="form-group row">
                            <div class="col-sm-offset-4 col-sm-4">
                            </div>
                        </div>
                    </div>
                   <?php echo form_close();?>
                                
                </div>
            </div>
        </div>
    </section>
</div>

<script>

function delayService(bill_detail_id,service_status){
                    
                    $.ajax({
                        method: 'post',
                        url: "<?php echo base_url(); ?>hospital/ajax/delayservice",
                        data: {"bill_detail_id":bill_detail_id, "service_status":service_status},
                        success: function(result){
                        }
                    });
                    location.reload();
                    location.reload();
                }

</script>
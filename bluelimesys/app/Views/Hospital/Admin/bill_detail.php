<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];

    $bill = $this->data['bill'] ?? [];
    $bill_detail = $this->data['bill_detail'] ?? [];
    $mrn_no = $bill['patient_id'];
    $formated_mrn_no = sprintf('%05d', $mrn_no);
    $previously_payed = 0;

?>
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo lang(line: 'Admin'); ?></h1>
            <small><?php echo lang('Deletion Requested Pending Bill'); ?></small>
           
        </div>
    </section>
    <section class="content">
        <div  class="panel panel-pd">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo "Invoice"?></h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-7">
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
                        </div>
                    </div>
                </div>

                
                <?php
                    $rtdiscount="hidden";
                    foreach($bill_detail as $detail):
                        if ( $detail['bill_discount'] >= 1 )
                        $rtdiscount="";
                    endforeach;
                ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Service Name</th><th width="20">Quantity</th><th>Price</th><th width="20" <?= $rtdiscount?>>Discount (%)</th><th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $total = 0;
                            $grand_total = 0;
                            $cnt = 0;
                            $rt = false;
                            $total_discount=0;
                            foreach($bill_detail as $detail):
                                $cnt++;
                                if ( $detail['service_type'] =="Consultation" ) 
                                    $rt = $detail;
                        ?>
                        <tr class="<?php echo ($detail['service_status']==-1)?"btn danger":"" ?>">
                            <td class="text-right"><?= $detail['service_name']?></td>
                            <td class="text-right"><?= $detail['bill_quantity']?></td>
                            <td class="text-right"><?= $detail['bill_amount']?></td>
                            <td class="text-right" <?php echo $rtdiscount;  ?>><?= $detail['bill_discount']?></td>
                            <td class="text-right"><?= ($detail['bill_amount']*$detail['bill_quantity']-(($detail['bill_amount']*$detail['bill_quantity'])*$detail['bill_discount']/100));?></td>
                        </tr>
                        <?php
                            $bill_discount = $detail['bill_discount'];

                            if($detail['service_status']!=\BluelimeConfiguration::SERVICE_STATUS_DELAY){
                                $total+= $detail['bill_amount']*$detail['bill_quantity']-(($detail['bill_amount']*$detail['bill_quantity'])*$detail['bill_discount']/100);

                                if($detail['service_id'] != \BluelimeConfiguration::DISCOUNT_PRICE_AMOUNT_ID)
                                    $grand_total += $detail['bill_amount']*$detail['bill_quantity'];
                                else
                                    $bill_discount = -100;

                                $total_discount += (($detail['bill_amount']*$detail['bill_quantity'])*$bill_discount/100);
                            }     
                        endforeach;
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="<?=($rtdiscount=="hidden")?3:4 ?>" style="text-align:right;"><b><?php echo 'Grand Total'; ?>:</b></td>
                            <td class="text-right">
                                <?php echo $grand_total ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="<?=($rtdiscount=="hidden")?3:4 ?>" style="text-align:right;"><b><?php echo 'Discount'; ?>:</b></td>
                            <td class="text-right">
                                <?php echo $total_discount ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="<?=($rtdiscount=="hidden")?3:4 ?>" style="text-align:right;"><b><?php echo 'Total'; ?>:</b></td>
                            <td class="text-right">
                                <?php echo $total ?>
                            </td>
                        </tr>

                        <tr <?php echo ($previously_payed==0)?"hidden":" " ?>>
                            <td style="text-align:right;" colspan="<?=($rtdiscount=="hidden")?3:4 ?>"><b><a href="#"   data-toggle='modal' data-target="#previousPayments"><div><i class="fa fa-eye" style="font-size: 20px"></i> &emsp13;<?php echo 'Previous Payment'; ?>:</div></a></b></td>
                            <td class="text-right">
                                <?php echo @$previously_payed ?>
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align:right;" colspan="<?=($rtdiscount=="hidden")?3:4 ?>"><b><?php echo 'Remaining Amount'; ?>:</b></td>
                            <td class="text-right">
                                <?php echo $total-@$previously_payed ?>
                            </td>
                        </tr>
                        
                    </tfoot>
                </table>
            </div>
            <div class="panel-footer text-right">
                <a href="/hospital/admin/deletependingbill?id=<?= $bill['id']?>" class="btn btn-danger"><i class="fa fa-trash"></i> Delete pending bill</a>
            </div>
        </div>
    </section>
</div>
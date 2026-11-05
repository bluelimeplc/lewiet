            <!-- =============================================== -->
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">

                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="header-icon">
                        <i class="pe-7s-world"></i>
                    </div>
                    <div class="header-title">
                        <h1><?php echo "Reports";?></h1>
                        <small><?php echo "Payment Detail Report";?></small>
                        <ol class="breadcrumb">
                            <?= view("Include/_admin_report_top_menu"); ?>
                        </ol>
                    </div>
                </section>

                
                <!-- Main content -->
                <section class="content">
                <div class="row">
                
                <div class="col-sm-12 col-md-12">
                            <div class="panel panel-bd lobidisable">
                                <?php
                                    $attributes = array('role'=>'form');
                                    helper('form');
                                    echo form_open_multipart('/hospital/report/paymentdetailreport', $attributes);
                                ?>
                               <div class="panel-body">
                                    <div class=" col-sm-4 col-md-4">
                                        <input type="text" class="form-control" value="<?=isset($search_criteria)?$search_criteria:""?>"  placeholder ="Search by Name, Mobile or Card No." name="search_criteria"  onChange="this.form.submit()">
                                        <input type="submit" style="visibility:hidden">
                                    
                                    </div>
                                </div>
                                <?php echo form_close();?>
                                <div class="col-sm-8 col-md-8">
                                    <table class="table table-striped table-bordered">
                                    <?php 
                                    foreach($patient_bill as $bill): 
                                    ?>
                                            <div>
                                            <tr>
                                                <td style="width:150px;"><a href="/hospital/report/paymentdetailreport?pid=<?=$bill['id']?>"><?=$bill['first_name'].' '.$bill['middle_name'].' '.$bill['last_name']?></a></td>
                                                <td><a href="/hospital/report/paymentdetailreport?pid=<?=$bill['id']?>"><?php echo "MRN-".sprintf('%05d', $bill['id']); ?></a></td>
                                                <td><a href="/hospital/report/paymentdetailreport?pid=<?=$bill['id']?>"><?=$bill['mobile']?></a></td>
                                                <td><a href="/hospital/report/paymentdetailreport?pid=<?=$bill['id']?>"><?=$bill['company_name']?></a></td>
                                            </tr>
                                            </div>
                                            
                                            <?php 
                                             endforeach; 
                                            ?>
                                    </table>
                                    
                                <h3><?=@$bills[0]['patient_name']." "?><?= ($remaining > 0)?"<b style='background-color:red; color:white;'>Total Remaining = ".number_format($remaining,2)."</b>":"" ?></h3>
                                </div>
                            </div>
                    
                    
                        <div class="col-sm-12 col-md-12">
                            <?php
                                $attributes = array('role'=>'form');
                                helper('form');
                                echo form_open_multipart('/hospital/report', $attributes);
                            ?>
                            
                               
                                <?php 
                                    foreach($bills as $bill): 
                                ?>
                                <div class="panel panel-bd lobidisable">
                                    
                                <div class="panel-body">
                                <b>Bill Date <?=$bill['billed_date'] ?> </b>
                                <div class="row">
                                    <table class="table table-striped table-bordered">
                                    
                                        <?php 
                                            $total_bill=0;
                                            
                                            foreach($billdetails as $detail): 
                                                if($bill['id']==$detail['bill_id']) {
                                        ?>
                                            <tr>
                                                <td style="width:150px;"></td>
                                                <td><?=$detail['service_name']?> * <?=$detail['bill_quantity']?></td>
                                                <td style="width:150px;"><?=number_format($detail['bill_amount']*$detail['bill_quantity'],2)?></td>
                                            </tr>
                                            
                                            <?php 
                                            $total_bill += $detail['bill_amount']*$detail['bill_quantity'];
                                            } endforeach; 
                                            ?>
                                            <tr>
                                                <td style="width:150px;">Total Bill</td><td></td><td><?=number_format($total_bill,2)?></td>
                                            </tr>
                                            
                                            <?php 
                                                $total_payment=0;
                                                $total_discount=0;
                                                foreach($billpayment as $payment):
                                                    if($payment['bill_id']==$bill['id']) {
                                                        $total_payment += $payment['payed_amount'];
                                                        $total_discount = $payment['payed_discount'];
                                            ?>
                                            <tr>
                                                <td style="width:150px;">Payment</td>
                                                <td style="text-align:right;"><b><?=$payment['payed_date']?></b></td>
                                                <td style="width:150px;"><?=number_format($payment['payed_amount'],2)?></td>
                                            </tr>
                                            <tr>
                                                <td style="width:150px;">Remaining</td><td></td><td><?=$total_bill-$total_payment?></td>
                                            </tr>
                                            
                                            <?php 
                                            
                                            } endforeach; ?>
                                            <?php 
                                                if ($total_discount>0){
                                            ?>
                                            <tr>
                                                <td style="width:150px;">Discount</td><td></td><td><?=$total_discount?></td>
                                            </tr>
                                            <tr>
                                                <td style="width:150px;">Remaining After Discount</td><td></td><td><?=$total_bill-$total_payment-$total_discount?></td>
                                            </tr>
                                            <?php
                                                }
                                            ?>
                                    
                                    </table>
                                </div>
                                </div>
                                </div>
                                <hr>
                                <?php  endforeach; ?>
                                
                            
                        </div>
                        <?php echo form_close(); ?>
                    
                    
                    </div>
                </div>
                </section> <!-- /.content -->
            </div> <!-- /.content-wrapper -->
          
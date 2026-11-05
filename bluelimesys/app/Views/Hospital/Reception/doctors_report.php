<!-- =============================================== -->
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1>Reception</h1>
                <small>Patient List</small>
                <ol class="breadcrumb">
                <a href="<?php echo base_url(); ?>hospital/reception/receptiondashboard"><h4 class="btn btn-primary">Dashboard </h4> <a href="<?php echo base_url(); ?>hospital/reception/doctorsreport"><h4 class="btn btn-primary">Doctors Report </h4></a>  <a href="<?php echo base_url(); ?>hospital/reception/dailypatients"><h4 class="btn btn-primary">Print Patient List</h4></a>
                </ol>
            </div>
        </section>
      
                <!-- Main content -->
        <section class="content">
            <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    echo form_open_multipart('/hospital/reception/dailypatients', $attributes);
            ?>

            <!-- Search Area -->
            <div class="row">
                
                <!--  Patient List area -->
                <div class="col-sm-12">
                
                    <div  class="panel panel-default">
                        <div class="panel-body  table-responsive">
                        <div class="panel-body">
                    <div class="col-sm-6">
                        <table class="table table-borderless" >
                            <tr>
                                <?php
                                    $cnt = 1;
                                    foreach($doctors as $doctor){
                                        echo '<td><a class="btn btn-success"  href="/hospital/reception/doctorsreport?doctor_id='.$doctor['id'].'&date='.$date_filter.'">'.$doctor['name'].'</a></td>';
                                        if($cnt%5==0)
                                            echo '</tr><tr>';
                                        $cnt++;
                                    }
                                ?>
                            </tr>
                            <tr>
                                <td colspan="2">Date: <input type="date" class="form-control" value="<?= $date_filter ?>" onchange="change_date(this)"> </td>
                            </tr>
                        </table>
                    </div>
                </div>
                        <!--div class="form-group">
                            <div class="col-lg-1">
                                <label for="selected_date">Date: </label>
                            </div>
                            <div class="col-lg-3">
                                <input type="date" class="form-control" value="<?=isset($selected_date)?$selected_date:date('Y-m-d');?>" id="selected_date" name="selected_date" onchange="this.form.submit()">
                            </div>
                            <div class="col-lg-1">
                                <input name= "daily_report" id="daily_report" type="submit" value="Search" hidden />
                            </div> 
                            <div class="col-lg-1">
                            <a role="button" onclick="print_printable('report_printable')" class="btn btn-success" style="float:right"><span class="fa fa-print"></span> Print</span></a>
                            </div>
                        </div-->
                        <a role="button" onclick="print_printable('report_printable')" class="btn btn-success" style="float:right"><span class="fa fa-print"></span> Print</span></a>
                        <div>
                            <div width="210mm">
                                    <div class="panel-body">
                                            <div style="float: left">
                                            <table class="table table-bordered table-hover table-condensed" style="font-size: x-small;">
                                            <thead><th colspan="5" style="text-align: center;"><h5><b><?= $selected_doctor['name'] ?? '' ?> (<?=$date_filter?>) <?="Consultation"?></b></h5></th></thead>
                                                <?php
                                                    $paid = 0;
                                                    $unpaid = 0;
                                                    $credit_cnt =0;
                                                    $rows = '';
                                                    foreach($doctor_visits as $patients){
                                                        if(intval($patients['is_free']) == 1)
                                                            $payment_status = "Transfer From Branch";
                                                        elseif (intval($patients['is_free']) == 2)
                                                            $payment_status = "Followup After Surgery";
                                                        elseif (intval($patients['is_free']) == 3)
                                                            $payment_status = "Approved Free Visit";
                                                        elseif (intval($patients['is_free']) == 4)
                                                            $payment_status = "Approved Free Visit";
                                                        elseif (intval($patients['is_free']) == 5)
                                                            $payment_status = "Refer (".$patients['registered_by_name'].")";
                                                            
                                                        else 
                                                            $payment_status =  ($patients['fs']??'') == ""?"Within ".BluelimeConfiguration::UNPAID_VISIT_DEFAULT_DAYS." Days":"";
                                                            if($patients['fs']!==""){
                                                                if($patients['bill_type']=="Credit"){
                                                                    $credit_cnt++;
                                                                }
                                                                else
                                                                    $paid++;                                                    
                                                            }
                                                            else{
                                                                $unpaid++;
                                                            }
                                                    
                                                            $credit = ($patients['bill_type']=="Credit")?"color:#fff;background-color:orange":"";
                                                            $credit_label = ($patients['bill_type']=="Credit")?"- Credit":"";
                                                            $rows .= '
                                                            <tr style="'.$credit.'">
                                                                <td>'.$patients['patient_id'].'</td>
                                                                <td>'.$patients['patient_name'].'</td>
                                                                <td>'.$patients['fs'].' '.$payment_status.' '.$credit_label.'</td>
                                                            </tr>
                                                            ';
                                                    }
                                                ?>
                                                
                                                
                                                <tr>
                                                <th colspan="2" style="text-align: right;">Total Visits</th>
                                                <th><?=$unpaid+$paid+$credit_cnt?></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" style="text-align: right;">Total Credit Consultations</th>
                                                    <th><?=$credit_cnt?></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" style="text-align: right;">Total Cash Consultations</th>
                                                    <th><?=$paid?></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" style="text-align: right;">Total Unpaid Consultations</th>
                                                    <th><?=$unpaid?></th>
                                                </tr>
                                                
                                                <tbody>
                                                    <tr><th>Card No</th><th>Patient Name</th><th>FS No</th></tr>
                                                    <?= $rows ?>
                                                </tbody>
                                            </table>
                                            </div>
                                            <div style="float: left">
                                            <table class="table table-bordered table-hover table-condensed" style="font-size: x-small;">
                                            <thead><th colspan="5" style="text-align: center;"><h5><b><?= $selected_doctor['name']  ?? '' ?> (<?= $date_filter ?>) <?="Procedure"?></b></h5></th></thead>
                                                <?php
                                                    $rows_procedure = '';
                                                    $count = 0;
                                                    $total_amount = 0;
                                                    foreach($procedures as $list){
                                                        
                                                        if($list['bill']['bill_status']==BluelimeConfiguration::BILL_RECIEVED){
                                                            $count++;
                                                            $total_amount += ($list['bill_quantity']*$list['bill_amount']);
                                $rows_procedure .= '<tr>
                                                        <td>'.$list['bill']['patient_id'].'</td>
                                                        <td>'.$list['bill']['patient_name'].'</td>
                                                        <td>'.$list['service_name'].'</td>
                                                        <td>'.number_format(round($list['bill_quantity']*$list['bill_amount'],2),2).'</td>
                                                        <td>'.$list['bill']['fs_number'].'</td>
                                                    </tr>';                                     
                                                        }
                                                    }
                                                ?> 
                                                    <tr>
                                                        <th colspan="4" style="text-align: right;">No.of Procedures</th>
                                                        <th><?=$count?></th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="4" style="text-align: right;">Total Amount</th>
                                                        <th><?=number_format(round($total_amount,2),2)?></th>
                                                    </tr>
                                                    
                                                
                                                <tbody>
                                                    <tr>
                                                        <th>Card No</th>
                                                        <th>Patient Name</th>
                                                        <th>Procedure</th>
                                                        <th>Amount</th>
                                                        <th>FS No</th>
                                                    </tr>
                                                    <?= $rows_procedure ?>
                                                </tbody>
                                                <tfoot>
                                                    
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12" id="report_printable">
                    <div  class="panel panel-default">
                        <div class="panel-body">
                            <hr>
                            <table class="table table-bordered table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th colspan="4" style="text-align: center;"><h4><b><?= $selected_doctor['name'] ??''?> (<?= $date_filter ?>)</b></h4></th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" style="text-align: center;"><h5><b>Consultations</b></h5></th>
                                        <th colspan="2" style="text-align: center;"><h5><b>Procedure</b></h5></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td align="right">Total Visits</td><td><?= $unpaid+$paid+$credit_cnt ?></td>
                                        <td align="right">No.of Procedures</td><td><?= $count ?></td>
                                    </tr>
                                    <tr>
                                        <td align="right">Cash Consultations</td><td><?= $paid ?></td>
                                        <td align="right">Total amount</td><td><?=number_format($total_amount,2)?></td>
                                    </tr>
                                    <tr>
                                        <td align="right">Credit Consultations</td><td><?= $credit_cnt ?></td>
                                    </tr>
                                    <tr>
                                        <td align="right">Unpaid Consultations</td><td><?= $unpaid ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php 
                                $column = 7; 
                            ?>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th colspan="<?= $column ?>"><b>Consultations reciept N<u>o</u></b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $cnt = 0;
                                        
                                        //echo '<tr>';
                                        foreach($doctor_visits as $patients){
                                            if((trim($patients['fs']) == ''))
                                                continue;

                                            if($cnt%$column==0)
                                                echo '<tr>';
                                            echo '<td>'.$patients['fs'].'</td>';
                                            $cnt++;
                                            if($cnt%$column==0)
                                                echo '</tr>';
                                        }
                                        if($cnt%$column != 0)
                                            echo '</tr>';
                                    ?>
                                </tbody>
                            </table>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="<?= $column ?>"><b>Procedure reciept N<u>o</u></b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $cnt = 0;
                                        $column = 4;
                                        foreach($procedures as $list){
                                            if((trim($list['bill']['fs_number']) == '') || ($list['bill']['bill_status']!=BluelimeConfiguration::BILL_RECIEVED ))
                                                continue;

                                            if($cnt%$column==0)
                                                echo '<tr>';
                                            echo '<td>'.$list['bill']['fs_number'].'  <span class="badge">'.$list['service_name'].'</span> <span class="badge">'.number_format($list['bill_quantity']*$list['bill_amount'],2).'</span></td>';
                                            $cnt++;
                                            if($cnt%$column==0)
                                                echo '</tr>';
                                        }
                                        if($cnt%$column != 0)
                                            echo '</tr>';
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <div style="text-align: right; padding: 20px;">
                           
                            <?php 
                                if(empty($approved_report)){
                                    echo "Not Approved";
                                }else{
                                    echo '<img style="height:60px;" src="data:image/jpeg;base64,'.base64_encode(  $doc_sign ?? '' ).'"/>';
                                }
                            ?><br>
                            <b>Signature: ____________________</b><br>
                            <?=$approved_report['created_at']??""?>


                        </div>
                    </div>
                </div>
            </div>
                    
        </section>

    </div> <!-- /.content-wrapper -->

    <script>

        function change_date(date){
            location = '?doctor_id=<?= $_GET['doctor_id']??'' ?>&date='+date.value;
        }

        function focus_search(){
            $("#search_patient").focus().val($("#search_patient").val());

        }
        document.onload(focus_search());
    </script>
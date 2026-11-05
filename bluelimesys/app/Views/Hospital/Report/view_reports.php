<?php
    $selected_date = str_replace("%", "",$this->data['selected_date']);
    $start_date = str_replace("%", "",$this->data['start_date']);
    $end_date = str_replace("%", "",@$this->data['end_date']);
    //\BluelimeSystem::show_and_die($this->data['start_date']);
   
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
                        <h1><?php echo "Reports";?></h1>
                        <small><?php echo "Sales Reports";?></small>
                        <ol class="breadcrumb">
                            <?= view("Include/_admin_report_top_menu"); ?>
                        </ol>
                    </div>
                </section>

                
                <!-- Main content -->
                <section class="content">
                <div class="row">
                <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    echo form_open_multipart('/hospital/report', $attributes);
                ?>
                        <div class="col-sm-12 col-md-12">
                            <div class="panel panel-bd lobidisable">
                               <div class="panel-body">
                                    <input type="date" class="form-control" value="<?=isset($selected_date)?$selected_date:date('Y-m-d');?>" name="report_date"  onChange="this.form.submit()">
                                    <input type="submit" style="visibility:hidden">
                                </div>
                            </div>
                        </div>
                <?php echo form_close();?>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td align="right"><h2><span class="count-number"><?php echo $this->data['number_of_visits']?></span></h2></td>
                                                <td><div class="small">Visits</div></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><h2 class="report_list"><span class="count-number"><?php echo $this->data['number_of_new_patients']?></span></h2></td>
                                                <td><div class="small">New Patients</div></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><h2 class="report_list"><span class="count-number"><?php echo ($this->data['number_of_visits'] - $this->data['number_of_new_patients'] ) ?></span></h2></td>
                                                <td><div class="small">Revisits</div></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="icon"><i class="ti-user"></i></div>
                                </div>
                               
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <table class="table table-borderless">
                                            <tr class="bg-dark">
                                                <td align="right"><h2><span class="count-number"><b><?php echo $this->data['total_consultation_fee']?></b></span></h2></td>
                                                <td><div class="small">Consultation Fee</div></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><h2><span class="count-number"><?php echo $this->data['total_cash_consultation_fee']?></span></h2></td>
                                                <td><div class="small">Cash</div></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><h2><span class="count-number"><?php echo $this->data['total_credit_consultation_fee']?></span></h2></td>
                                                <td><div class="small">Credit</div></td>
                                            </tr>
                                            
                                        </table>
                                    </div>
                                    <div class="icon"><i class="fa fa-money"></i></div>
                                </div>
                               
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <table class="table table-borderless">
                                            <tr class="bg-dark">
                                                <td align="right"><h2><span class="count-number"><b><?php echo $this->data['total_procedures_payment']?></b></span></h2></td>
                                                <td><div class="small">Procedures Payment</div></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><h2><span class="count-number"><?php echo $this->data['total_cash_procedures_payment']?></span></h2></td>
                                                <td><div class="small">Cash</div></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><h2><span class="count-number"><?php echo $this->data['total_credit_procedures_payment']?></span></h2></td>
                                                <td><div class="small">Credit</div></td>
                                            </tr>
                                            
                                        </table>
                                    </div>
                                    <div class="icon"><i class="fa fa-money"></i></div>
                                </div>
                               
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <table class="table table-borderless">
                                            <tr class="bg-dark">
                                                <td align="right"><h2><span class="count-number"><b><?php echo $this->data['total_payment_amount']?></b></span></h2></td>
                                                <td><div class="small">Total Payments</div></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><h2><span class="count-number"><?php echo ($this->data['total_cash_procedures_payment'] + $this->data['total_cash_consultation_fee']) ?></span></h2></td>
                                                <td><div class="small">Total Cash</div></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><h2><span class="count-number"><?php echo ($this->data['total_credit_procedures_payment'] + $this->data['total_credit_consultation_fee']) ?></span></h2></td>
                                                <td><div class="small">Total Credit</div></td>
                                            </tr>
                                            
                                        </table>
                                    </div>
                                    <div class="icon"><i class="fa fa-money"></i></div>
                                </div>
                               
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <table class="table table-borderless">
                                            <tr class="bg-dark">
                                                <td align="right"><h2><span class="count-number"><b><?php echo $this->data['total_payment_amount']?></b></span></h2></td>
                                                <td><div class="small">Total Payments</div></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><h2><span class="count-number"><?php echo $this->data['total_recieved_amount']?></span></h2></td>
                                                <td><div class="small">Recieved</div></td>
                                            </tr>
                                            <tr>
                                                <td align="right"><h2><span class="count-number"><?php echo -($this->data['unpaid_payment'])?></span></h2></td>
                                                <td><div class="small">Payment Difference</div></td>
                                            </tr>
                                            
                                        </table>
                                    </div>
                                    <div class="icon"><i class="fa fa-money"></i></div>
                                </div>
                               
                            </div>
                        </div>

 
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            <a href="">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                    <div class="small"><?php echo "Free Visits"?></div>
                                        <span class="count-number">
                                            <?php 
                                            foreach($this->data['free_visits'] as $free){
                                              echo  '<li>'. $free['patient_name'].'</li>';
                                            }
                                            ?>
                                        </span>
                                        <div class="sparkline2 text-center"></div>
                                    </div>
                                </div>
                            </a>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <div class="panel panel-bd lobidisable">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4><?php echo "Bill Details"?></h4>
                                    </div>
                                </div>
                                <div class="panel-body  table-responsive">
                                    <table class="table table-striped">
                                        <tr><td>No.</td><th>Patient Name</th><th>Payment Type</th><th>Company</th><th>Amount</th><th>Doctor Name</th><th>FS No</th><th>Recieved By</th></tr>
                                    <?php 
                                    $cnt = 0; 
                                    foreach($this->data['billPayements'] as $billPayement): $cnt++
                                    ?>
                                        <tr><td><?=$cnt?></td><td><?=$billPayement['patient_name']?></td><td><?=$billPayement['payment_type']?></td><td><?=$billPayement['company_name']?></td><td><?php echo number_format($billPayement['payed_amount'],2)?></td><td><?=@$billPayement['doctor_name'][0]?></td><td><?=$billPayement['fs_number']?></td><td><?=$billPayement['registered_by_name']?></td></tr>
                                    <?php 
                                    endforeach;
                                    ?>
                                    <tr><td></td><th></th><th></th><th></th><th><?=number_format($this->data['total_payment'],2)?></th><th></th><th></th><th></th></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <?php
                                $attributes = array('role'=>'form');
                                helper('form');
                                echo form_open_multipart('/hospital/report', $attributes);
                            ?>
                            <div class="panel panel-bd lobidisable">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <div class="input-group col-sm-7 col-md-7">
                                            <h4><?php echo "Summerized Income Report"?></h4> 
                                            <span class="input-group-addon"></span> 
                                            From:<input type="date" value="<?=isset($start_date)?$start_date:date('Y-m-d');?>" name="start_date" class="form-control" >
                                            <span class="input-group-addon"></span>
                                            To:<input type="date" value="<?=isset($end_date) ? date('Y-m-d', strtotime($end_date)):date('Y-m-d');?>" name="end_date"  class="form-control">
                                            <span class="input-group-addon"></span>
                                            &nbsp;<button class="form-control btn-success">Search</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body  table-responsive">
                                    <table class="table table-striped">
                                        <tr><td>Date<td>Total Consultation Fee</td><td>Total Procedure</td><td>Payed Procedure</td><td>Total Remaining</td></tr>
                                        <tr><td><?php echo '<b>From </b>'.$start_date.'<b> To </b>'.date('Y-m-d', strtotime($end_date));?><td><?php echo number_format($this->data['total_consultation_fee_summerized'],2)?></td><td><?php echo number_format($this->data['total_procedure_order'],2)?></td><td><?php echo number_format($this->data['total_procedures_summerized'],2)?></td><td><?php echo number_format(($this->data['total_procedure_order'] - $this->data['total_procedures_summerized']),2);?></td></tr>

                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php
                                echo form_close();
                            ?>
                    </div>
                    <div class="row">
                         <!-- Bar Chart -->
                        <div class="col-sm-12 col-md-12">
                            <div class="panel panel-bd lobidisable">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4><?php echo "Bill Chart"?></h4>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <canvas id="barChart" height="140"></canvas>
                                </div>
                            </div>
                        </div>
                        
                    </div>

                </section> <!-- /.content -->
            </div> <!-- /.content-wrapper -->
<script>
    
$(document).ready(function () {
    //bar chart
    var ctx = document.getElementById("barChart");
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [<?php foreach ($this->data['week_date'] as $day_label){
                                    echo '"'.$day_label.'"';
                                    echo ',';
                                }
                            ?>],
            datasets: [
                {
                    label: "<?php echo "Cash";?>",
                    data: [<?php foreach ($this->data['week_cash'] as $cash){
                                    echo $cash;
                                    echo ',';
                                }
                            ?>],
                    borderColor: "#223F4A",
                    borderWidth: "0",
                    backgroundColor: "#223F4A"
                },
                {
                    label: "<?php echo "Credit";?>",
                    data: [<?php foreach ($this->data['week_credit']??[] as $cash){
                                    echo $cash;
                                    echo ',';
                                }
                            ?>],
                    borderColor: "#57787D",
                    borderWidth: "0",
                    backgroundColor: "#57787D"
                }
            ]
        },
        options: {
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
            }
        }
    });

});

</script>            
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo "Reception Dashboard";?></h1>
            <small><?php echo "Reception Dashboard";?></small>
            <ol class="breadcrumb">
            <a href="<?php echo base_url(); ?>hospital/reception/receptiondashboard"><h4 class="btn btn-primary">Dashboard </h4> <a href="<?php echo base_url(); ?>hospital/reception/doctorsreport"><h4 class="btn btn-primary">Doctors Report </h4></a>  <a href="<?php echo base_url(); ?>hospital/reception/dailypatients"><h4 class="btn btn-primary">Print Patient List</h4></a>
             </ol>
        </div>
    </section>


    <!-- Main content -->
    
    <section class="content">
        <div class="row">
            
            <div class="col-sm-12">
            <div class="panel panel-bd">
                <div class="panel-body">
                    <div class="panel-title">
                        <a href="<?php echo base_url(); ?>hospital/reception/doctorschedule"><h4 class="btn btn-primary">Doctors Schedule</h4></a>
                    </div>
                </div>
                <div class="panel panel-bd">
                    
                        <div class="panel-body">
                            <?php
                            //\BluelimeSystem::show_and_die($this->data['doctors']);
                                foreach($this->data['doctors'] as $doctors):
                            ?>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                
                                <h4><span class="count-number"><?php echo $doctors ?></span> </h4>
                                <div  class="col-sm-12">
                                <table class="table table-striped table-hover table-responsive table-bordered">
                                <tr style="font-weight: bold;">
                                    <td>Day</td><td>Start</td><td>End</td><td>/Patient</td>
                                </tr> 
                                    <?php
                                        foreach($this->data['schedules'] as $row):
                                            if($row['doctor_name']==$doctors){
                                    ?>
                                            <tr>
                                                <td><?=\BluelimeSystem::no_to_day($row['day']) ?></td>
                                                <td><?=date('H:i',strtotime($row['start_time'])) ?></td>
                                                <td><?=date('H:i',strtotime($row['end_time'])) ?></td>
                                                <td><?=$row['per_patient_time'] ." min" ?></td>
                                            </tr>
                                    <?php
                                            }
                                        endforeach;
                                    ?>
                                </table> 
                                
                                </div>
                                
                            </div> 
                            <?php
                                endforeach;
                            ?>
                          
                    <div class="row col-sm-12 col-md-12 col-lg-12">  <hr> <hr>
                        <h4>Unavailable Doctors on Scheduled Day</h4>
                        <table class="table table-striped table-bordered">
                                <tr><th><i style="visibility:hidden;">aaa</i>Doctor</th><th><i style="visibility:hidden;">aaa</i>Date</th></tr>
                                
                            <?php 
                                foreach($this->data['skips'] as $skip): 
                                ?>
                                    <tr>
                                        <td><?=$skip['doctor_name']?></td>    
                                        <td><?=$skip['skip_date']?></td>
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

        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd lobidisable">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Cancelled Appointments</h4><span class="cancelledappointments"></span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered">
                            <tr><th><i style="visibility:hidden;">aaa</i>Date</th><th>Time</th><th>Full Name</th><th>Mobile</th><th>App Reason</th><th>Doctor Name</th><th>Cancelled By</th><th>Cancelation Reason</th></tr>
                            <?php 
                            foreach($this->data['cancelled_appointments'] as $cancelled_appointment): 
                            ?>
                                <tr>
                                    <td><?=date('Y-m-d', strtotime($cancelled_appointment['appointment_date']))?></td>
                                    <td><?=$cancelled_appointment['time']?></td>
                                    <td><?=$cancelled_appointment['full_name']?></td>
                                    <td><?=$cancelled_appointment['mobile']?></td>
                                    <td><?=$cancelled_appointment['appointment_reason']?></td>
                                    <td><?php echo $cancelled_appointment['doctor_name']?></td>
                                    <td><?=$cancelled_appointment['cancelled_by']?></td>
                                    <td style="color:red"><?=$cancelled_appointment['cancelation_reason']?></td>
                                </tr>
                            <?php 
                            endforeach;
                            ?>
                        </table>
                        
                    </div>
                    
                </div>
                
            </div>

        </div>

        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd lobidisable">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Today Sales</h4>
                        </div>
                    </div>
                    <div class="panel-body  table-responsive">
                        <table class="table table-striped table-hover table-responsive table-bordered">
                            <thead>
                                <tr><th>No.</th><th><i style="visibility:hidden;">aaa</i>Patient Name</th><th>Payment Type</th><th>Company</th><th>Amount</th><th>Doctor Name</th><th>FS No</th><th>Recieved By</th></tr>
                            </thead>
                            <?php 
                            $cnt = 0; 
                            foreach($this->data['billPayements'] as $billPayement): $cnt++
                            ?>
                                <tr class="sales_<?= $billPayement['bill_id'];?>">
                                    <td><?=$cnt?></td>
                                    <td><?=$billPayement['patient_name']?></td>
                                    <td <?= ($billPayement['payment_type'] == 'ADJUSTMENT') ? 'class="bg-danger"' : '';?>><?=$billPayement['payment_type']?></td>
                                    <td><?=$billPayement['company_name']?></td>
                                    <td><?php echo number_format($billPayement['payed_amount'],2)?></td>
                                    <td><?=@$billPayement['doctor_name'][0]?></td><td><?=$billPayement['fs_number']?></td>
                                    <td><?=$billPayement['registered_by_name']?></td>
                                   
                                </tr>
                            <?php 
                            endforeach;
                            ?>
                            <tr><td></td><td><i style="visibility:hidden;">zzz</i></td><td><i style="visibility:hidden;">zzz</i></td><td><i style="visibility:hidden;">zzz</i></td><td><i style="visibility:hidden;">9</i><?=number_format($this->data['total_payment'],2)?></td><td><i style="visibility:hidden;">zzz</i></td><td><i style="visibility:hidden;">zzz</i></td><td><i style="visibility:hidden;">zzz</i></td></tr>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </section> <!-- /.content -->
</div> <!-- /.content-wrapper -->
<div>
    <div class="modal fade" id="add_adjustment" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header dental-bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add adjustment</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr><th>No.</th><th>Patient Name</th><th>Payment Type</th><th>Company</th><th>Amount</th><th>Doctor Name</th><th>FS No</th><th>Recieved By</th><th></th></tr>
                        </thead>
                        <tbody id='adjustment_content'>

                        </tbody>
                        <tfoot id='adjustment_data'>

                        </tfoot>
                    </table>
                    <div id="addAdjustment_Flash" class="alert alert-danger hide" role="alert">
                            <span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-success" onclick="validateAdjustmentQuantity();">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var paymentId = 0;
    function add_adjustment(class_name, payment_id){
        paymentId = payment_id;
        let content = $('.'+class_name);
        $('#adjustment_content').html('');
        for(i = 0; i < content.length; i++){
            let row = content[i];
            $('#adjustment_content').append(row);
        }
        let patient_name = content.children()[1].innerHTML;
        let company_name = content.children()[3].innerHTML;
        let doctor_name  = content.children()[5].innerHTML;
        let fs_no        = content.children()[6].innerHTML;
        let recieved_by  = '<?= $this->data['loggeduserdata']['name']; ?>';

        let adjustment_row = '<tr><td></td><td>'+ patient_name +'</td><td class="bg-danger">ADJUSTMENT</td>';
        adjustment_row    += '<td>'+ company_name + '</td>';
        adjustment_row    += '<td><input type="number" min="1" class="form-control" id="adjustment_value"/></td>';
        adjustment_row    += '<td>'+ doctor_name + '</td>';
        adjustment_row    += '<td>'+ fs_no + '</td>';
        adjustment_row    += '<td>'+ recieved_by + '</td>';
        adjustment_row    += '<td></td></tr>';
        
        $('#adjustment_data').html(adjustment_row);

        $('#add_adjustment').modal('show');
    }

    function save_adjustment(){
        let adjustment_value = $('#adjustment_value').val();
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/ajax/saveadjustment",
            data: {"payment_id":paymentId, "adjustment_value":-(adjustment_value)},
            success: function(result, textStatus, jqXHR){
                try{
                    if(result == '')
                        throw 'Error';
                    let r = JSON.parse(result);
                    if(r.status == 'success'){
                        location.reload();
                    }
                    else{
                        error_html = "Status: " + r.status + "<br>" + r.result;
                        dental_show_flash('addAdjustment_Flash', error_html, false);
                    }
                }
                catch (e){
                    error_html = "Invalid retun result.<br><u>Result:</u><br>"+result;
                    dental_show_flash('addAdjustment_Flash', error_html, false);
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                error_html = "Cannot add to database. <br><u>Error</u>:<br>" + errorThrown;
                dental_show_flash('addAdjustment_Flash', error_html, false);
            }
        });
    }
    function validateAdjustmentQuantity(){

        var adjustment_value = $("#adjustment_value").val();
        if(adjustment_value < 1){
        
            dental_show_flash("addAdjustment_Flash","Invalid Adjustment Value");
            return false;
        }
        save_adjustment();

    }

$(document).ready(function() {
    //bar chart
    var ctx = document.getElementById("barChart");
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Last 30 days", "Last 15 days", "Last 7 days", "Today"],
            datasets: [{
                    label: "<?php echo "Appointment Generated";?>",
                    data: [<?php echo 30;?>, <?php echo 15;?>, <?php echo 7;?>, <?php echo 10;?>],
                    borderColor: "#223F4A",
                    borderWidth: "0",
                    backgroundColor: "#223F4A"
                },
                {
                    label: "<?php echo "Appointment Missed";?>",
                    data: [<?php echo 30;?>, <?php echo 15;?>, <?php echo 7;?>, <?php echo 1;?>],
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
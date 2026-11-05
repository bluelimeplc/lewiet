<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Admin</h1>
            <small>Appointment Report</small>
            <ol class="breadcrumb">
                            <?= view("Include/_admin_report_top_menu"); ?>
                        </ol>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="panel-title"><h4>Filter</h4></div>
                        <div class="panel-body">
                            <div class="row">
                                <form action="" method="GET">
                                    <div class=" col-lg-3" style="margin-bottom: 2px;">
                                        <div class="input-group">
                                            <span class="input-group-addon" ><b>From</b></span>
                                            <input type="date" class="form-control" value="<?= substr($from_date,0,10);?>" name="selected_date">
                                        </div>
                                    </div>
                                    <div class=" col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" ><b>To</b></span>
                                            <input type="date" class="form-control" value="<?=substr($to_date,0,10);?>"  name="end_date">    
                                        </div>
                                        <input type="submit" style="visibility:hidden">
                                    </div>
                                   <div class=" col-lg-3"><input type="submit" class="btn btn-success" value="Filter"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel">
                  

                    <div class="panel-body">
                        <table class="table table-bordered table-striped" id="item_sales_report_table">
                            
                            <tbody>
                                <?php
                                $canceled_count = 0;
                                $missed_count = 0;
                                $late_count = 0;
                                $visited_count = 0;
                                $all_appointments = 0;
                                $online_appointment = 0;
                                $online_visited = 0;
                                $online_active = 0;
                                $online_missed = 0;
                                $online_canceled = 0;
                                $online_late = 0;
                                    if(isset($appointments) && is_array($appointments)){
                                        foreach($appointments as $row){
                                            if($row['appointment_status'] == BluelimeConfiguration::APPOINTMENT_CANCELED && $row['paid_appointment'] == 1){
                                                $online_canceled++;
                                                $online_appointment++;
                                            }
                                            if($row['appointment_status'] == BluelimeConfiguration::APPOINTMENT_ACTIVE && $row['paid_appointment'] == 1){
                                                $online_active++;
                                                $online_appointment++;
                                            }
                                            if($row['appointment_status'] == BluelimeConfiguration::APPOINTMENT_VISITED && $row['paid_appointment'] == 1){
                                                $online_visited++;
                                                $online_appointment++;
                                            }
                                            if($row['appointment_status'] == BluelimeConfiguration::APPOINTMENT_LATE_VISIT && $row['paid_appointment'] == 1){
                                                $online_late++;
                                                $online_appointment++;
                                            }
                                            if($row['appointment_status'] == BluelimeConfiguration::APPOINTMENT_MISSED && $row['paid_appointment'] == 1){
                                                $online_missed++;
                                                $online_appointment++;
                                            }
                                        }
                                    }

                                    foreach($appointments as $row){
                                        if($row['appointment_status'] == BluelimeConfiguration::APPOINTMENT_CANCELED){
                                            $canceled_count++;
                                            $all_appointments++;
                                        }
                                        if($row['appointment_status'] == BluelimeConfiguration::APPOINTMENT_MISSED){
                                            $missed_count++;
                                            $all_appointments++;
                                        }
                                        if($row['appointment_status'] == BluelimeConfiguration::APPOINTMENT_LATE_VISIT){
                                            $late_count++;
                                            $all_appointments++;
                                        }
                                        if($row['appointment_status'] == BluelimeConfiguration::APPOINTMENT_VISITED){
                                            $visited_count++;
                                            $all_appointments++;
                                        }
                                    }
                                    if($all_appointments !=0){
                                ?>

                                <tr><td>Cancelled Appointments</td><td><?=$canceled_count?></td><td><?=round(($canceled_count*100)/$all_appointments,2)."%"?></td></tr>
                                <tr><td>Missed Appointments</td><td><?=$missed_count?></td><td><?=round(($missed_count*100)/$all_appointments,2)."%"?></td></tr>
                                <tr><td>Late Visit Appointments</td><td><?=$late_count?></td><td><?=round(($late_count*100)/$all_appointments,2)."%"?></td></tr>
                                <tr><td>Visited Appointments</td><td><?=$visited_count?></td><td><?=round(($visited_count*100)/$all_appointments,2)."%"?></td></tr>
                                <tr><td>All Appointments</td><td><?=$all_appointments?></td><td><?=round(($all_appointments*100)/$all_appointments,2)."%"?></td></tr>
                                <tr><td></td><td></td></tr>
                                <tr style="background-color: #000;color: #fff;"><td>Walk In Visits</td><td><?=$walk_in?></td><td><?=round((($walk_in)*100)/($visited_count+$late_count+$walk_in),2)."%"?></td></tr>
                                <tr style="background-color: #000;color: #fff;"><td>Appointment Visits</td><td><?=$visited_count+$late_count?></td><td><?=round((($visited_count+$late_count)*100)/($visited_count+$late_count+$walk_in),2)."%"?></td></tr>
                                <tr style="background-color: #000;color: #fff;"><td>All Visits</td><td><?=$visited_count+$late_count+$walk_in?></td><td><?=round((($visited_count+$late_count+$walk_in)*100)/($visited_count+$late_count+$walk_in),2)."%"?></td></tr>
                                <?php 
                                    if($online_appointment != 0){
                                ?>
                                <tr><td colspan="3"><h3>Online Appointments</h3></td></tr>
                                
                                <tr><td>Active</td><td><?=$online_active?></td><td><?=round(($online_active*100)/$online_appointment,2)."%"?></td><td></td></tr>
                                <tr><td>Visited</td><td><?=$online_visited?></td><td><?=round(($online_visited*100)/$online_appointment,2)."%"?></td><td></td></tr>
                                <tr><td>Late</td><td><?=$online_late?></td><td><?=round(($online_late*100)/$online_appointment,2)."%"?></td></tr>
                                <tr><td>Missed</td><td><?=$online_missed?></td><td><?=round(($online_missed*100)/$online_appointment,2)."%"?></td></tr>
                                <tr><td>Canceled</td><td><?=$online_canceled?></td><td><?=round(($online_canceled*100)/$online_appointment,2)."%"?></td></tr>
                                <tr><td>Appointment Booked</td><td><?=$online_appointment?></td><td><?=round(($online_appointment*100)/$online_appointment,2)."%"?></td><td></td></tr>
                            <?php
                                    }                                  
                                }
                            ?>
                            </tbody>
                        </table>
                       
                    </div>
                </div>

            </div>
            <div class="col-lg-6">
                         <!-- Bar Chart -->
                        <div class="col-sm-12 col-md-12">
                            
                               
                                <div class="col-md-6">
                                    <canvas id="pieChart" class=""></canvas>
                                </div>
                            
                        </div>
                        
            </div>
            
                </div>
    </section>
</div>
<script>
    
$(document).ready(function() {

var ctx = document.getElementById("pieChart");
var myChart = new Chart(ctx, {
    type: 'pie',
    animation: true,

    data: {
        labels: ["Cacelled","Missed","Visited","Late Visit"],
        datasets: [{
                label: "<?php echo "Income";?>",
                data: [<?=$canceled_count?>,<?=$missed_count?>,<?=$visited_count?>,<?=$late_count?>],
                borderWidth: "3",
                backgroundColor: [
                    "#C70039",
                    "#FFC300",
                    "#DAF7A6",
                    "#33fff6"
                ],

            }


        ]

    },
    options: {
        responsive: true,
        legend: {
            display: true,
            position: "top",
            labels: {
                fontColor: "#333"
            }
        }
    }
});


});

</script>  
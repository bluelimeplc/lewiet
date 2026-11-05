<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo "Call Center Dashboard";?></h1>
            <small><?php echo "Call Center Dashboard";?></small>
            
        </div>
    </section>


    <!-- Main content -->
    
    <section class="content">
        <div class="row">
            
            <div class="col-sm-12">
            <div class="panel panel-bd">
                <div class="panel-body">
                    
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

        

    </section> <!-- /.content -->
</div> <!-- /.content-wrapper -->

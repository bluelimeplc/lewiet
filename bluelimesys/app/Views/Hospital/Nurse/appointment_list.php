<?php
    $selected_date = str_replace("%", "",@$this->data['selected_date']);
    //\BluelimeSystem::show_and_die($selected_date);
   
?>
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo lang('Reception');?></h1>
            <small><?php echo lang('Appointments');?></small>
            
        </div>
    </section>
    <section class="content">
            <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    echo form_open_multipart('/hospital/nurse/appointmentlist', $attributes);
            ?>

            <!-- Search Area -->
            <div class="row">
                            
                    <div class="col-sm-12 col-md-12">
                        <div class="panel panel-bd">
                            <div class="panel-body">
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                        <input id="search_appointment" type="text" name="search_appointment" class="form-control" >
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button> 
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
            
                    <?php
                        form_close();
                    ?>
                    <!-- Main content -->
                    
                        
                            <div class="col-md-12">
                            
                                <div class="panel panel-bd">
                                <div class="panel-heading">
                                    <?php
                                        $attributes = array('role'=>'form');
                                        echo form_open_multipart('/hospital/nurse/appointmentlist', $attributes);
                                    ?>
                                        
                                            <div class="input-group col-sm-12 col-md-12">
                                                <span class="input-group-addon"></span> 
                                                <h4>Select Date<b class="pull-right"><?=$no_of_appointment?> Appointments</b></h4>
                                                <input class="form-control" onChange="this.form.submit()" type="date" value="<?=$selected_date;?>" name="appointment_date"> 
                                                <span class="input-group-addon"></span>
                                                <?php
                                                    foreach($this->data['doctors'] ?? [] as $doctor):
                                                    ?>
                                                    &emsp;<input type="radio" name="app_doctor" value="<?=$doctor?>"  onChange="this.form.submit()" > <?=$doctor?>
                                                <?php endforeach; ?>
                                                <input type="submit" style="visibility:hidden">
                                               
                                            </div>
                                            
                                    <?php
                                        form_close();
                                    ?>   
                                        
                                    </div>
                                    <div class="panel-body  table-responsive">
                                        <table width="100%" class="table table-striped table-bordered table-hover dt-responsive" id="appointmentS">
                                            <thead>
                                                <tr>
                                                    <th class="all"><?php echo lang('Status'); ?></th>
                                                    <th class="all"><?php echo lang('Patient Name'); ?></th>
                                                    <th class="all"><?php echo lang('Phone Number'); ?></th>
                                                    <th class="all"><?php echo lang('Doctor'); ?></th>
                                                    <th class="all"><?php echo lang('Date'); ?></th>
                                                    <th class="all"><?php echo lang('Time'); ?></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php 
                                                    foreach ($appointments as $appointment): 
                                                ?>

                                                <tr>
                                                    <?php 
                                                        if($appointment['appointment_status']==-1)
                                                        echo "<td class='bg-danger'>Cancelled</td>";
                                                        elseif($appointment['appointment_status']==2)
                                                        echo "<td class='bg-warning'>Missed</td>";
                                                        else if($appointment['appointment_status']==3)
                                                        echo "<td class='bg-info'>Visited</td>";
                                                        else if($appointment['appointment_status']==4)
                                                        echo "<td class='bg-primary'>Late Visit</td>";  
                                                        else  
                                                        echo "<td class='bg-success'>Active</td>";
                                                    ?>
                                                    </td>
                                                    <td><?php echo $appointment['full_name']?>&emsp;<span class="fa fa<?= ($appointment['patient_id']==0)?"-user-plus":""; ?>" style="color:green; font-size: 25px"></span></td>
                                                    <td><?php echo $appointment['mobile']?></td>
                                                    <td><?php echo $appointment['doctor_name']?></td>
                                                    <td><?php echo $new_date_format = date('Y-m-d', strtotime($appointment['appointment_date']));?></td>
                                                    <td><?php echo $appointment['time']?></td>
                                                    
                                                    
                                                    
                                                    
                                                </tr>
                                                    <div class="modal fade" id="confirm_cancel<?php echo $appointment['id']?>" tabindex="-1" role="dialog" aria-labelledby="CancelAppointment" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">Cancel Appointment</h5>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <h4>Are you Sure you want to cancel Appointment?</h4><br>
                                                                        <p><b>Name&emsp;</b><?php echo @$appointment['full_name']?><br>
                                                                        <b>Doctor&emsp;</b><?php echo $appointment['doctor_name']?><br>
                                                                        <b>Date&emsp;</b><?php echo date('l d-m-Y', strtotime($appointment['appointment_date'])) ?><br>
                                                                        <b>Time&emsp;</b><?php echo $appointment['time']?><br></p>
                                                                    </div>
                                                                        
                                                                    <div class="modal-footer">
                                                                        
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                                                                        <a href="/hospital/reception/cancelappointment?app_id=<?php echo @$appointment['id']?>"><input type="button" class="btn btn-danger"  value="Cancel">
                                                                    </div>
                                                            
                                                            </div>
                                                        </div>
                                                    </div>

                                                <?php
                                                    endforeach
                                                ?>
                                            </tbody>
                                        </table>
                                        
                                    </div>
                                </div>
                            </div>

                        
                            
    </section>
            </div>
</div>

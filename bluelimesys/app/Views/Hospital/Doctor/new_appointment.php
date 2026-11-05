<?php
    $loggeduserdata = \BluelimeAuthentication::get_logged_user_data();
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $patient = $this->data['patient'];
    $patient_appointment = $this->data['patient_appointment'];
    $last_visit_id = $this->data['last_visit_id'];
    $formated_mrn_no = "MRN-".sprintf('%05d', @$patient['id']);
    $app_id = @$_GET['app_id'];
    $app_name= explode(" ", @$appointment[0]['full_name']);
    $fname = (isset($patient['first_name']))?@$patient['first_name']:@$app_name[0];
    $mname = (isset($patient['middle_name']))?@$patient['middle_name']:@$app_name[1];
    $lname = (isset($patient['last_name']))?@$patient['last_name']:@$app_name[2];
    $mobile = (isset($patient['mobile']))?@$patient['mobile']:@$appointment[0]['mobile'];
    $visit_id = @$_GET['vid'];
//\BluelimeSystem::show_and_die($visit_id)
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
            <h1><?php echo "Doctor"?></h1>
            <small><?php echo "New Appointment" ?></small>
            <ol class="breadcrumb">
            <li class="active"><a href="<?=(@$patient['id']!=0)?"patienthistory?visit_id=":"#"?><?=$last_visit_id?>">Patient History</a></li>
             </ol>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
        <!--  form area -->
        <div class="col-sm-6">

            

            
            <div  class="panel panel-bd panel-form">
                

                <div class="panel-body">
                    <div class="portlet-body form">

                    
                    <?php 
                        helper('form');
                        $attributes = array('class' => 'form-horizontal','name'=>'p_info','onsubmit'=>'return validatetime()');
                        echo form_open('/hospital/doctor/addappointment', $attributes);                
                    ?>

                            <div class="form-body"">
                                <div class="form-group col-md-12">
                                    <label for="mrntitle" class="control-label">MRN N<u>o</u></label>
                                    <div class="">
                                        <input id="mrntitle" type="text" name="mrn_no" class="form-control" value="<?php echo $formated_mrn_no; ?>" readonly> 
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="control-label">Full Name</label>
                                    <div class="input-group">
                                        <?php
                                        //\BluelimeSystem::show_and_die($patient['id']);
                                            if(isset($patient['id'])){
                                                $edit = "readonly";
                                            }
                                            else
                                                $edit = ""
                                        ?>
                                        <input type="text" hidden id="patient_id" name="patient_id" value="<?php echo @$patient['id']?>">
                                        <input type="text" hidden name="visit_id" value="<?=@$visit_id?>">
                                        <input type="text" hidden name="app_id" value="<?php echo @$_get['app_id'] ?? ""?>">
                                        <input type="text" hidden name="paid_appointment" value="<?php echo 0?>">
                                        <input type="text" hidden name="appointment_status" value="<?php echo 1; ?>">
                                        <input type="text" hidden name="registered_by" value="<?php echo $logged_user?>">
                                        <input type="text" hidden name="registered_by_name" value="<?php echo $logged_user_name?>">
                                        <input type="text" hidden name="reminder_sent" value="<?php echo 0?>">
                                        <input type="text" required  name="first_name" <?php echo $edit; ?>  class="form-control" placeholder="Name" value="<?php echo @$fname ?? ""  ?>">
                                        <span class="input-group-addon"></span>
                                        <input type="text" required name="middle_name" <?php echo $edit; ?> class="form-control" placeholder="Father Name" value="<?php echo @$mname ?? ""  ?>">
                                        <span class="input-group-addon"></span>
                                        <input type="text" name="last_name"  <?php echo $edit; ?> class="form-control" placeholder="Grand Father" value="<?php echo @$lname ?? ""  ?>">
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="control-label"><?php echo "Mobile"?> :</label>
                                    <div class="">
                                        <input type="txt" <?php echo $edit?>placeholder="09xxxxxxxx"  name="mobile"  <?php echo $edit; ?> value="<?php echo @$mobile ?? ""  ?>"  required class="form-control">
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="control-label"><?php echo "Date"?> :</label>
                                    <div class="">
                                        <input type="date" id="date" min="<?php echo  date('Y-m-d'); ?>" value=""  name="date" class="form-control"  onchange="loadSchedule(this.value);check_active_appointment();"  placeholder="<?php echo lang('date_placeholder')?>" required>
                                    </div>
                                </div>

                                <div class="form-group col-md-12" hidden>
                                    <label class="control-label">Doctor:</label>
                                <div class="" >
                                    <select class="form-control doctor_id" id="doctor_id"   name="doctor_id"  required>
                                        <option value="">--Select doctor--</option>
                                        <?php
                                            foreach($doctors as $doctor):
                                        ?>
                                            <option value="<?= $doctor['id'] ?>" <?php echo ($doctor['id']==$logged_user)?"selected":""?>><?= $doctor['name'] ?></option>;
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                                </div>


                                <div class="form-group col-md-12">
                                    <label class="control-label"></label>
                                    <div class="col-md-12 schedule"> </div>
                                </div>
                                
                                <div class="form-group col-md-12">
                                        <label class="control-label">Reason:</label>
                                    <div class="">
                                        <textarea name="appointment_reason" class="form-control" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="form-group row"></div>
                                <div class="alert alert-warning <?php echo (@$_GET['rt']==1)?"":"hide"; ?>" role="alert">
                                <span></span>
                                </div>
                          </div>  
                            
                        </div>
                        
                    </div>
                </div>
                <div class="panel panel-bd">
                    <div id="active_appointment_alert" class="alert alert-warning hide" role="alert">
                        <span></span>
                    </div>
                    <div class="panel-body">
                        <button type="submit" class="btn btn-success" style="float:right">Save</button>                                        
                    </div>
                </div>
        </div>
        
        <div class="col-sm-6 col-md-6">
                            <div class="panel panel-bd">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4>Appointment History</h4>
                                    </div>
                                </div>
                                <div class="panel-body" <?php echo (isset($patient['id']) OR ($app_id>=1))?"":"hidden" ?>>
                                    
                                    <div class="form-body">
                                        <h5>Active Appointment&emsp;<i data-toggle="tooltip" title="Print Appointment" data-html="true" class="dental-menu-icons-01"><a href="" class="fa fa-print right"   data-target="#print_Appointment" data-toggle="modal" data-html="true"></a></i></h5>
                                        
                                        <table class="table table-striped">
                                        <th>Doctor<td>Date</td><td>Time</td><td>Status</td></th>
                                                <?php
                                                    foreach((isset($patient['id'])?$patient_appointment:$appointment) as $app):
                                                        if ($app['appointment_status']==1){
                                                ?>
                                            <tr> 
                                                <td>
                                                    <?php echo @$app['doctor_name'] ?>
                                                </td>
                                                <td>
                                                    <?php echo date('l d-m-Y', strtotime($app['appointment_date'])) ?>
                                                </td>
                                                <td>
                                                    <?php echo @$app['time'] ?>
                                                </td>
                                                <td class="bg-success">
                                                    Active
                                                </td>
                                            </tr>
                                                <?php
                                                        }
                                                    endforeach
                                                ?>
                                          </table>
                                          <table>
                                          <tr><td><i>Reminder</i></td><td></td><td></td></tr>
                                          <tr>
                                            <td> SMS Sent</td>
                                            <td><?= @$patient_appointment[0]['reminder_sent'] == 1 ? '<i class="glyphicon  glyphicon-ok color-red"></i>' : '<i class="glyphicon  glyphicon-remove color-gray"></i>';?></td>
                                            <td>&emsp;</td>
                                            <td>Called</td>
                                            <td><?= @$patient_appointment[0]['reminder_sent'] == 2 ? '<i class="glyphicon  glyphicon-ok color-red"></i>' : '<i class="glyphicon  glyphicon-remove color-gray"></i>';?></td>
                                          </tr>
                                          </table>
                                            <br><br>
                                            <h5>Previous Appointments</h5>
                                        <table class="table table-striped">
                                        <th>Doctor<td>Date</td><td>Time</td><td>Status</td></th>
                                                <?php
                                                    foreach($patient_appointment as $app):
                                                        if ($app['appointment_status']<>1 && $app['patient_id']<>0){
                                                ?>
                                            <tr> 
                                                <td>
                                                    <?php echo @$app['doctor_name'] ?>
                                                </td>
                                                <td>
                                                    <?php echo date('l d-m-Y', strtotime($app['appointment_date'])) ?>
                                                </td>
                                                <td>
                                                    <?php echo @$app['time'] ?>
                                                </td>
                                                <?php 
                                                    if($app['appointment_status']==-1){
                                                        $status = "Cancelled";
                                                        $bg = "bg-danger";
                                                        }
                                                    elseif($app['appointment_status']==2){
                                                        $status = "Missed";
                                                        $bg = "bg-warning";
                                                    }
                                                    else{
                                                        $status = "Visited";
                                                        $bg = "bg-info'>"; 
                                                    }   
                                                    ?>
                                                <td class="<?php echo $bg ?>">
                                                    <?php echo $status; ?>
                                                </td>
                                            </tr>
                                                <?php
                                                        }
                                                    endforeach
                                                ?>
                                          </table>
                                    </div>
                                </div>
                            </div>
                        </div>
        <?php echo form_close();?>
    </div>            
    </section>
</div>

<style type="text/css">
    .p_name{
        color:red;
    }
</style>


<script>

    function loadSchedule(){
        var doctor_id = $('#doctor_id').val();
        var date     = $('#date').val();
        if (doctor_id!='') {
            $.ajax({ 
                'url': "<?php echo base_url(); ?>hospital/ajax/getschedule",
                'type': 'POST', 
                'data': {'doctor_id': doctor_id, 'date':date},
                'success': function(data) {
                    var container = $(".schedule");
                    container.html(data);
                    //console.log(data);
                }
                });
            };
        }
    function myBooking(data){
        var id = $("#t_" + data).text();
        document.getElementById("msg_c").innerHTML = "<div style=' color:green; font-size:20px;'><?php echo 'Select Time'?> " +id +"</div>";
        document.getElementById('serial_no').value = id;        
    }
    function validatetime(){
        if (document.getElementById('serial_no').value == ''){
        dental_show_alert("Please Select valid Time.");
        return false;
        }
    } 
    function check_active_appointment()
                {

                    var patient_id = $("#patient_id").val();
                    //alert(patient_id);
                    //return false;
                    $.ajax({
                        method: 'post',
                        url: "<?php echo base_url(); ?>hospital/ajax/patientactiveappointment",
                        data: {"patient_id":patient_id},
                        success: function(result){
                            if(result != null){
                                dental_show_flash('active_appointment_alert', result);
                               // dental_show_alert(result);
                            }
                        }
                    });
                } 



    function printContent(el){
        var restorepage  = $('body').html();
            var printcontent = $('#' + el).clone();
            $('body').empty().html(printcontent);
            window.print();
            $('body').html(restorepage);
            location.reload();
    }

    document.onload(check_active_appointment());
</script>






<div class="modal fade  col-md-4 col-sm-4 col-l-4" id="print_Appointment" tabindex="-1" role="dialog" aria-labelledby="CancelAppointment" aria-hidden="true">
    <div class="modal-content">
		<div class="modal-body">
        
            <div id="div1" >
            <div>
            
            </div><hr>
                <div class="container header">
                    <div class="header-right-info pull-left clearfix col-md-12">
                        <div class="single-header-info">
                            <p>
                                <strong><?php echo lang('Patient Name');?> : </strong><?php echo @$fname .' '. @$mname.' '. @$lname ;?></p>
                                <p><strong><?php echo lang('Card No');?> : </strong><?php echo @$formated_mrn_no;?></p>
                                <p><strong><?php echo lang('Phone Number');?> : </strong><?php echo @$mobile;?></p>
                                
</p>
                        </div>
                    </div>
                </div>
            
                <section>
                    <div class="container">
                        <div class="row details-content">
                            <div class="col-sm-4">
                                <div class="table-responsive marg">
                                    <table  class="table table-bordered table-hover" style="font-size:12px; ">

                                        <thead>
                                            <tr>
                                                <th><?php echo lang('Doctor Name');?></th>
                                                <th><?php echo lang('Date');?></th>
                                                <th><?php echo lang('Time');?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        foreach ($patient_appointment as $app1) { 
                                            if ($app1['appointment_status']==1 && $app['patient_id']<>0){
                                        ?>
                                            <tr>
                                                <td>
                                                    <?php echo $app1['doctor_name'];?>
                                                </td>
                                                <td><?php echo date('l d-m-Y', strtotime($app1['appointment_date'])) ?></td>
                                                <td><?php echo $app1['time'];?></td>
                                            </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                        
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="text-center">IT’S TIME TO SMILE AGAIN</a>.</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                    
            </div>
            <div class="modal-footer col-md-12">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                <button type="button" class="btn btn-success" onclick="printContent('div1')">Print</button>
                
            </div>                                                          
        </div>

    </div>
</div>
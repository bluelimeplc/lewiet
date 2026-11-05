<?php
    $loggeduserdata = \BluelimeAuthentication::get_logged_user_data();
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $patient = $this->data['patient'];
    $patient_appointment = $this->data['patient_appointment'];
    $next_appointment_reason = $this->data['next_appointment_reason'];

    $formated_mrn_no = "MRN-".sprintf('%05d', @$patient['id']);
    $app_id = @$_GET['app_id'];
    $app_name= explode(" ", @$appointment[0]['full_name']);
    $fname = (isset($patient['first_name']))?@$patient['first_name']:@$app_name[0];
    $mname = (isset($patient['middle_name']))?@$patient['middle_name']:@$app_name[1];
    $lname = (isset($patient['last_name']))?@$patient['last_name']:@$app_name[2];
    $mobile = (isset($patient['mobile']))?@$patient['mobile']:@$appointment[0]['mobile'];
    
    $company_header = \BluelimeSystem::get_company_header();
//\BluelimeSystem::show_and_die(date("Y-m-d", strtotime($appointment[0]['appointment_date'])));
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
            <h1><?php echo "Callcenter"?></h1>
            <a href="/hospital/callcenter/newappointment?id=0" class="btn btn-success"><?php echo lang('New Appointment')?></a>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/callcenter">Appointment List</a></li>
             </ol>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
        <!--  form area -->
        <div class="col-sm-6">
            <?php 
                helper('form');
                $attributes = array('class' => 'form-horizontal','name'=>'p_info','onsubmit'=>'return validatetime()');
                echo form_open('/hospital/callcenter/addappointment', $attributes);                
            ?>
            <div  class="panel panel-bd panel-form">
                
            <div class="panel-heading"<?= @$patient['id']>0?"":"hidden"?>>
                <div class="panel-title">
                    <h4><b style="color:darkcyan"><?= $formated_mrn_no ?></b></h4>
                </div>
            </div>

                <div class="panel-body">
                    <div class="portlet-body form">

                            <div class="form-body"">
                            <?php
                                        //\BluelimeSystem::show_and_die($patient['id']);
                                            if(isset($patient['id'])){
                                                $edit = "readonly";
                                            }
                                            else
                                                $edit = ""
                                        ?>
                                <input id="mrntitle" type="hidden" name="mrn_no" class="form-control" value="<?php echo $formated_mrn_no; ?>" readonly> 
                                <div class="form-group col-md-12">
                                    <label class="control-label"><?php echo "Mobile"?> :</label>
                                    <div class="">
                                        <input role="presentation" autocomplete="off" type="number" <?php echo $edit?>placeholder="09xxxxxxxx"  name="mobile"  <?php echo $edit; ?> value="<?php echo @$mobile ?? ""  ?>" minlength="10"  maxlength="10" required class="form-control" onload="check_if_mobile_exists()" onchange="check_if_mobile_exists()" id="patient_mobile_input">
                                    </div>
                                    <div id="patient_mobile_alert" role="alert">
                                        <span></span>
                                    </div>
                                </div>    
                                <div class="form-group col-md-12">
                                    <label class="control-label">Full Name</label>
                                    <div class="input-group">
                                        
                                        <input type="text" hidden id="patient_id" name="patient_id" value="<?php echo @$patient['id']?>">
                                        <input type="text" hidden name="app_id" value="<?php echo @$app_id ?? ""?>">
                                        <input type="text" hidden name="additional" value="<?php echo @$_GET['additional'] ?? ""?>">
                                        <input type="text" hidden name="paid_appointment" value="<?php echo 0?>">
                                        <input type="text" hidden name="appointment_status" value="<?php echo 1; ?>">
                                        <input type="text" hidden name="registered_by" value="<?php echo $logged_user?>">
                                        <input type="text" hidden name="registered_by_name" value="<?php echo $logged_user_name?>">
                                        <input type="text" hidden name="visit_id" value="<?php echo 0?>">
                                        <input type="text" hidden name="reminder_sent" value="<?php echo 0?>">
                                        <input role="presentation" autocomplete="off"type="text" required  name="first_name" <?php echo $edit; ?>  class="form-control" placeholder="Name" value="<?php echo @$fname ?? ""  ?>">
                                        <span class="input-group-addon"></span>
                                        <input role="presentation" autocomplete="off" type="text" required name="middle_name" <?php echo $edit; ?> class="form-control" placeholder="Father Name" value="<?php echo @$mname ?? ""  ?>">
                                        <span class="input-group-addon"></span>
                                        <input role="presentation" autocomplete="off" type="text" name="last_name"  <?php echo $edit; ?> class="form-control" placeholder="Grand Father" value="<?php echo @$lname ?? ""  ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group col-md-12">
                                    <label class="control-label"><?php echo "Date"?> :</label>
                                    <div class="">
                                        <input type="date" id="date" min="<?php echo  date('Y-m-d'); ?>" value="<?php echo isset($appointment[0]['appointment_date'])?date("Y-m-d", strtotime($appointment[0]['appointment_date'])) : date('Y-m-d')?>" value="<?php echo set_value('date'); ?>" name="date" class="form-control"  onchange="loadSchedule(this.value);"   placeholder="<?php echo lang('date_placeholder')?>" required>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label class="control-label">Doctor:</label>
                                <div class="">
                                    <select class="form-control doctor_id" id="doctor_id"  onchange="loadSchedule(this.value);check_active_appointment();" name="doctor_id"  required>
                                        <option value="">--Select doctor--</option>
                                        <?php
                                            foreach($doctors as $doctor)
                                            echo '<option value="'. $doctor['id'] .'">'. $doctor['name'] .'</option>';
                                            
                                        ?>
                                    </select>
                                </div>
                                </div>


                                <div class="form-group col-md-12">
                                    <label class="control-label"></label>
                                    <div class="col-md-12 schedule"> </div>
                                </div>
                                
                                <div class="form-group col-md-12">
                                        <label class="control-label">Note:</label>
                                    <div class="">
                                        <input name="appointment_reason" class="form-control">
                                    </div>
                                </div>
                                <div class="alert alert-warning <?php echo (@$_GET['rt']==1)?"":"hide"; ?>" role="alert">
                                <span></span>
                                </div>
                          </div>  
                          
                        </div>
                        
                    </div>
                    <div class="panel-footer" style="text-align: right;">
                        <button type="submit" class="btn btn-success"><?= ($app_id >0)?"Update":"Save"?></button>                                        
                    </div>
                    
            </div>
                <?php echo form_close();?>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="panel panel-bd">
                <div class="panel-body">
                <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    echo form_open_multipart('/hospital/callcenter/newappointment?id='.$_GET['id'], $attributes);
                ?>
                    <div class="col-sm-12 col-md-12">
                        <div class="panel panel-bd">
                            <div class="panel-body">
                                    
                                        <div class="col-sm-8">
                                            <input id="searchkey" type="text" placeholder="<?php echo @$nop. " Records" ?>" name="searchkey" class="form-control" value="<?= $searchkey ?>">
                                        </div>
                                        <div class="col-sm-3">
                                            <button type="submit" class="btn btn-success">Search Patient <i class="fa fa-search"></i></button>
                                        </div>
                            </div>
                            <table width="100%" class="table table-striped table-bordered table-hover table-condensed" id="patient_list" <?=strlen(trim($searchkey))<8?"hidden":""?>>
                                <thead>

                                    <tr>                     
                                        <th>MRN No</th>
                                        <th>Full Name</th>
                                        <th>Phone Number</th>
                                        <th>Sex</th>
                                    </tr>

                                </thead>

                                <tbody>
                                    <?php
                                        foreach ($patients as $value) {
                                    ?>
                                    <tr class="odd gradeX">
                                        <td><a href="/hospital/callcenter/newappointment?id=<?=$value['id']?>"><b><?php echo "MRN-".sprintf('%05d', $value['id']); ?></b></a></td>
                                        <td><a href="/hospital/callcenter/newappointment?id=<?=$value['id']?>"><?php echo $value['first_name'].' '. @$value['middle_name'].' '. @$value['last_name'] ;?></a></td>
                                        <td><a href="/hospital/callcenter/newappointment?id=<?=$value['id']?>"><?php echo $value['mobile'];?></a></td>
                                        <td><a href="/hospital/callcenter/newappointment?id=<?=$value['id']?>"><?php echo $value['sex'];?></a></td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table> 
                        </div>
                    </div>
                <?php echo form_close();?>

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
                                                    <a href="newappointment?app_id=<?=@$app['id']?>&id=<?=@$patient['id']?>"><?php echo @$app['doctor_name'] ?></a>
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
        let mobile = $("#patient_mobile_input").val();
           
        if (document.getElementById('serial_no').value == ''){
                toastr['error']("Please Select valid Time.");
                return false;
        }
        if(mobile.length == 10){ 
                return true;
        }
        else{               
            toastr['error']("Incorrect Mobile Number");		
            return false;  
        }
        
         
    } 
    function check_active_appointment()
                {

                    var patient_id = $("#patient_id").val();
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
    
    function check_if_mobile_exists()
    {
        var mobile = $("#patient_mobile_input").val();
        $.ajax({
            method: 'post',
            url: "/getpatientbymobileforapp",
            data: {"mobile":mobile,'patien_id':1},
            success: function(result){
                if(result != null && mobile.length > 9){
                    dental_show_flash('patient_mobile_alert', result, false);
                }
                else
                    dental_hide_flash('patient_mobile_alert');
            }
        });
    }
    
</script>






<div class="modal fade"   style="height: 150mm; width: 90mm;" id="print_Appointment" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-content">
		<div class="modal-body">
        
            <div id="div1" >
            <div>
            <?php echo '<img  width="100"  style="margin-bottom:10px" src="data:image/jpeg;base64,'.base64_encode( $company_header['logo_printouts'] ).'"/>' ?>
                <span style="font-size:11px; float:right; border-left: 1px solid black; padding-left: 12px; height: 60px;">
                <table>
                    <tr><td>Mob.</td><td><?=$company_header['mobile']?></td></tr> 
                    <tr><td></td><td></td></tr> 
                    <tr><td>Tel.</td><td><?=$company_header['tel']?></td></tr> 
                    <tr><td colspan="2" style="font-size:9px;"><?=$company_header['website']?></td></tr> 
                    <tr><td colspan="2" style="font-size:9px;"><?=$company_header['email']?></td></tr> 
                    
                    </table>
                </span>
            </div><hr style="border-width: 2px;">
                <div class="container header">
                    <div class="row details-content">
                        <div class="single-header-info">
                                <span style="font-size:12px;"><strong><?php echo lang('Patient Name');?> : </strong><?php echo  $fname.' '.$mname.' '.$lname ;?></span><br>
                                <span style="font-size:12px;"><strong><?php echo lang('Card No');?> : </strong><?php echo @$formated_mrn_no;?></span><br>
                                <span style="font-size:12px;"><strong><?php echo lang('Phone Number');?> : </strong><?php echo $mobile;?></span><br>
                        </div>
                    </div>
                </div>
                    <div class="container">
                        <div class="row details-content">
                            <div style="width: 80mm;">
                                <div class="table-responsive marg">
                                <span style="font-size:12px;"><strong>Appointment</strong></span>
                                    <table  border="2px"  style="border-width:2px; border-color:black; font-size:11px; width:80mm">

                                    <thead>
                                            <tr>
                                                <th><?php echo lang('Doctor Name');?></th>
                                                <th><?php echo lang('Date');?></th>
                                                <th><?php echo lang('Time');?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        foreach ($appointment as $app1) { 
                                            if ($app1['appointment_status']==1){
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
                                                <td colspan="5" class="text-center"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <hr>
                 <p style="font-size:8px; text-align: center;">Powered by Bluelime Technologies<br>www.bluelimeplc.com</p>     
            </div>
            <div class="modal-footer col-md-12">
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                <button type="button" class="btn btn-success" onclick="printContent('div1')">Print</button>
                
            </div>                                                          
        </div>

    </div>
</div>
<?php
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

   // \BluelimeSystem::show_and_die(date("Y-m-d", strtotime($appointment[0]['appointment_date'])));
?>  
<?php
    $alert = $this->data['alert'] ?? [];
    if(!empty($alert)){
        echo '
        <script>

            let body_html = "' . $alert['body_html'] . '";
            let command = "' . $alert['command'] . '";
            let alert_type = "' . $alert['alert_type'] . '";
            let title = "' . $alert['title'] . '";
            ';

            echo ($alert['autoHide']) ? 'let autoHide = true' : 'let autoHide = false;';

            echo '
            dental_show_alert(body_html,command,alert_type, title, autoHide);

        </script>
        ';
    }
?>

<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
    <head>

        <link rel="icon" href="/favicon-bluelime.png" sizes="16x16"> 
        <script src="<?php echo base_url()?>js/jquery/jquery-3.6.3.js"></script>
        <script src="<?php echo base_url()?>js/bootstrap-tagsinput.js" type="text/javascript"></script>
        <script src="<?php echo base_url()?>js/twitter-bootstrap.min.js" type="text/javascript"></script>        
        <script src="<?php echo base_url()?>js/typeahead.bundle.min.js" type="text/javascript"></script>        
        <script src="<?php echo base_url()?>js/bloodhound.js" type="text/javascript"></script>        
        <script src="<?php echo base_url()?>js/dental/dental_script.js"></script>
        <link href="<?php echo base_url()?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url()?>extra/style.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url()?>css/dental/style.css" rel="stylesheet" type="text/css"/>
        <style>
            .bdapp {
                  border:0px solid #0088cc;
  border-radius: 20px;
                }
            
        </style>
        
    </head>
     <div class="modal fade" data-backdrop="static" id="dentalAlertModal" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <div id="dentalAlertTitleIcon" class="dental-flag-icon">
                                <i class="glyphicon glyphicon-alert color-info dental-flag-icon"></i>&emsp;
                                <i class="fa fa-warning color-yellow dental-flag-icon" style="display:none"></i>
                            </div>
                            <h3 class="modal-title" id="dentalAlertTitle">Modal Title</h3>
                        </div>
                        <div class="modal-body">
                            <div id="dentalAlertBody">Body</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-Primary" id="dentalAlertButtonPrimary" data-dismiss="modal" >OK</button>
                            <button type="button" class="btn btn-danger"  id="dentalAlertButtonSecondary" data-dismiss="modal" >No</button>
                        </div>
                    </div>
                </div>
            </div>
     


        
        <!--  form area -->
        <div class="col-sm-6">

        <style>
                                    .btn-primary, .btn-primary:hover, .btn-primary:active, .btn-primary:visited {
                                        background-color: #C48D74 !important;
                                        border-color:  #C48D74 !important;
                                    }
                                    
                                    .btn-primary:hover{
                                        background-color:#785342 !important;
                                    }
                                    .form-control:focus {
                                    border-color:rgb(196, 141, 116);
                                    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgb(196, 141, 116);
                                    }
                                    .form-control {
                                        border-color:  #DEC8B8 !important;
                                    }
                                    .input-group-addon {
                                        background-color:#DEC8B8 !important;
                                        border-color: #DEC8B8 !important;
                                        display: flexbox;
                                    }
                                    .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
                                    background-color: #ffffff;
                                    border-color: #ffffff;
                                    color: #785342;
                                    }
                            </style>
            <div  class="panel panel-bd panel-form bdapp">
                

                <div class="panel-body">
                    <div class="portlet-body form">

                    
                    <?php 
                        helper('form');
                        $attributes = array('class' => 'form-horizontal','name'=>'p_info','onsubmit'=>'return validatetime()');
                        echo form_open('/addappointment', $attributes);                
                    ?>

                            <div class="form-body">
                                <div class="form-group col-md-12">
                                    <label class="control-label"  style="color:#C48D74; padding-left: 10px; padding-bottom: 5px;"><?php echo "Mobile*"?></label>
                                    <?php
                                        //\BluelimeSystem::show_and_die($patient['id']);
                                            if(isset($patient['id'])){
                                                $edit = "readonly";
                                            }
                                            else
                                                $edit = ""
                                        ?>
                                    <div class="">
                                        <input style="border-radius: 50px;" autocomplete="nop"  onload="check_if_mobile_exists()" onchange="check_if_mobile_exists()" id="patient_mobile_input"  type="number" <?php echo $edit?>placeholder="09xxxxxxxx" min="700000000" max="999999999" name="mobile"  <?php echo $edit; ?> value="<?php echo @$mobile ?? ""  ?>"  required oninvalid="this.setCustomValidity('Please enter a valid Mobile')" oninput="this.setCustomValidity('')" class="form-control">
                                    </div>
                                    <div id="patient_mobile_alert" class="alert-info hide" style="border-radius: 50px;background-color: #DEC8B8; color:#ffffff">
                                                        <span></span>
                                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="control-label"  style="color:#C48D74; padding-left: 10px; padding-bottom: 5px;">Full Name*</label>
                                    <div class="input-group">
                                        
                                        <input type="text" hidden id="patient_id" name="patient_id" value="<?php echo @$_GET['id']?>">
                                        <input type="text" hidden id="app_id" name="app_id" value="<?php echo @$app_id ?? ""?>">
                                        <input type="text" hidden id="additional"  name="additional" value="<?php echo @$_GET['additional'] ?? ""?>">
                                        <input type="text" hidden id="paid_appointment" name="paid_appointment" value="<?php echo 0?>">
                                        <input type="text" hidden id="appointment_status" name="appointment_status" value="<?php echo 1; ?>">
                                        <input type="text" hidden id="registered_by" name="registered_by" value="<?php echo 0?>">
                                        <input type="text" hidden id="registered_by_name" name="registered_by_name" value="<?php echo ''?>"> 
                                        <input type="text" hidden id="visit_id" name="visit_id" value="<?php echo 0?>">
                                        <input type="text" hidden id="reminder_sent" name="reminder_sent" value="<?php echo 0?>">
                                        <input style="border-top-left-radius: 50px;border-bottom-left-radius: 50px;"  type="text" autocomplete="nop" required  id="first_name"  name="first_name" <?php echo $edit; ?>  class="form-control" placeholder="Name" value="<?php echo @$fname ?? ""  ?>">
                                        <span  class="input-group-addon"></span>
                                        <input type="text" autocomplete="nop" required id="middle_name" name="middle_name" <?php echo $edit; ?> class="form-control" placeholder="F. Name" value="<?php echo @$mname ?? ""  ?>">
                                        <span  class="input-group-addon"></span>
                                        <input style="border-top-right-radius: 50px;border-bottom-right-radius: 50px;" type="text" autocomplete="nop" id="last_name" name="last_name"  <?php echo $edit; ?> class="form-control" placeholder="G. Name" value="<?php echo @$lname ?? ""  ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group col-md-12">
                                    <label class="control-label"  style="color:#C48D74; padding-left: 10px; padding-bottom: 5px;"><?php echo "Date*"?></label>
                                    <div class="">
                                        <input style="border-radius: 50px;" type="date" id="date" min="<?php echo  date('Y-m-d'); ?>" value="<?php echo isset($appointment[0]['appointment_date'])?date("Y-m-d", strtotime($appointment[0]['appointment_date'])) : date('Y-m-d')?>" value="<?php echo set_value('date'); ?>" name="date" class="form-control"  onchange="loadSchedule(this.value);"   placeholder="<?php echo lang('date_placeholder')?>" required>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label class="control-label"  style="color:#C48D74; padding-left: 10px; padding-bottom: 5px;">Doctor* </label><br>
                                <div class="">
                                    <select style="border-radius: 50px;" class="form-control doctor_id" id="doctor_id"  onchange="loadSchedule(this.value);" name="doctor_id"  required>
                                        <option value="">--Select doctor--</option>
                                        <?php
                                            foreach($doctors as $doctor)
                                            echo '<option value="'. $doctor['id'] .'">'. $doctor['name'] .'</option>';
                                            
                                        ?>
                                    </select>
                                    
                                </div>
                                
                                </div>


                                <div class="form-group col-md-12">
                                <?=isset($appointment[0]['time'])&&($appointment[0]['paid_appointment']!=0)?"<span style='color:red'>Preferred Time: ". $appointment[0]['time']."</span":""?>
                                
                                    <label class="control-label"></label>
                                    <div class="col-md-12 schedule"> </div>
                                </div>
                                <!--
                                <div class="form-group col-md-12">
                                        <label class="control-label">Note:</label>
                                    <div class="">
                                        <textarea style="resize: none;" name="appointment_reason" class="form-control" rows="3" required><?= (!empty(trim($appointment['appointment_reason']??''))) ? $appointment['appointment_reason'] : $next_appointment_reason ?></textarea>
                                    </div>
                                </div> 
                               
                                <div class="form-group row"></div>
                                <div class="alert alert-warning <?php echo (@$_GET['rt']==1)?"":"hide"; ?>" role="alert">
                                <span></span>
                                </div> -->
                          </div>  
                           
                        </div>
                         <button id="submit" type="submit" class="btn btn-primary" style="float:right;">Save</button><img id="loadingImage" src="img/Loading.gif"  height="50px" style="display:none;float:right" />
                    </div>
                     
                </div>
               
        </div>
        
        
        <?php echo form_close();?>


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
                'url': "getschedule",
                'type': 'POST', 
                'data': {'doctor_id': doctor_id, 'date':date},
                'success': function(data) {
                    var container = $(".schedule");
                    container.html(data);
                    console.log(data);
                }
                });
            };
        }
    function myBooking(data){
        var id = $("#t_" + data).text();
        document.getElementById("msg_c").innerHTML = "<div style=' color:green; font-size:20px;'><?php echo 'Selected Time'?> " +id +"</div>";
        document.getElementById('serial_no').value = id;  
        document.getElementById('loadingImage').style.display='none';
        document.getElementById('submit').style.display='block';      
    } 
    function validatetime(){
        document.getElementById('loadingImage').style.display='block';
        document.getElementById('submit').style.display='none';
        if (document.getElementById('serial_no').value == ''){
        dental_show_alert("Please Select valid Time.");
        return false;
        } 
        else
        return checkBeforeSubmit();
    } 
   var wasSubmitted = false;    
    function checkBeforeSubmit(){
      if(!wasSubmitted) {
        wasSubmitted = true;
        return wasSubmitted;
      }
      return false;
    }
    
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
    
    function saveappointment() {
    $.ajax({
        url: '/addappointment',
        data: {
                patient_id: $('#patient_id').val(),
                doctor_id: $('#doctor_id').val(),
                date: $('#date').val(),
                first_name: $('#first_name').val(),
                middle_name: $('#middle_name').val(),
                last_name: $('#last_name').val(),
                registered_by: $('#registered_by').val(),
                registered_by_name: $('#registered_by_name').val(),
                visit_id: $('#visit_id').val(),
                reminder_sent: $('#reminder_sent').val(),
                schedule_ids: $('#schedule_ids').val(),
                paid_appointment: $('#paid_appointment').val(),
                mobile: $('#patient_mobile_input').val(),
                sequence: $('#serial_no').val(),
                app_id: $('#app_id').val()
        },
        method: 'POST',
        async: false,
        dataType: 'json',
        success: function(result) {
            if(result.status == "success"){
                toastr['success']('Appointment Saved. You will Recieve text message soon.');
                //location = '/inventory/dispensary/';
                //$('tr#'+prescription_group_id).remove();
            }
            else{
                toastr['error'](result.message);
            }
        },
        error: function(jqXHR, status, error) {
            alert(error);
        }
    });
}
   
</script>

<?php
    $selected_date = str_replace("%", "",@$this->data['selected_date']);
    //\BluelimeSystem::show_and_die($selected_date);

    function getAppointmentDoctorQueueCode($doctorId, $doctorName = '') {
        $doctorId = (int) $doctorId;

        if($doctorId==18){
            $doctorKey = 1; 
        }
        else if($doctorId==19){
            $doctorKey = 2; 
        }
        else if($doctorId==20){
            $doctorKey = 3; 
        }
        else{
            $doctorKey = 0;
        }

        return $doctorKey;
    }

    // Calculate queue numbers per doctor using the doctor code and a fixed priority code for booked appointments
    $appointmentQueue = [];
    $appointmentPriorityCode = '0';
    if (isset($appointments) && is_array($appointments)) {
        foreach ($appointments as $key => $appointment) {
            $doctorId = (int) ($appointment['doctor_id'] ?? 0);
            $doctorCode = getAppointmentDoctorQueueCode($doctorId, $appointment['doctor_name'] ?? '');

            if (!isset($appointmentQueue[$doctorCode])) {
                $appointmentQueue[$doctorCode] = 1;
            } else {
                $appointmentQueue[$doctorCode]++;
            }

            $appointments[$key]['queue_number'] = $doctorCode . $appointmentPriorityCode . str_pad($appointmentQueue[$doctorCode], 2, '0', STR_PAD_LEFT);
            $appointments[$key]['doctor_code'] = $doctorCode;
        }
    }
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
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/reception/newappointment?id=0">New Appointment</a></li>
            </ol>
        </div>
    </section>
    <section class="content">
            <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    echo form_open_multipart('/hospital/reception/appointmentlist', $attributes);
            ?>

            <!-- Search Area -->
            <div class="row">
                            
                    <div class="col-sm-12 col-md-12">
                        <div class="panel panel-bd">
                            <div class="panel-body">
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                        <input id="search_appointment" type="text" name="search_appointment" class="form-control refocusable" value="<?= $searchkey ?>">
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button> 
                                        </div>
                                        <div ><a href="/hospital/reception/newappointment?id=0" class="btn btn-success pull-right"><?php echo lang('New Appointment')?></a></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
            
                    <?php
                        form_close();
                    ?>
                    <!-- Main content -->
                    
                    <div class="row">
                                               
                    </div>
                            <div class="col-md-12">
                                
                            
                                <div class="panel panel-bd">
                                    
                                    <div class="panel-heading">
                                    <?php
                                        $attributes = array('role'=>'form');
                                        echo form_open_multipart('/hospital/reception/appointmentlist', $attributes);
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
                                                <div class="header-icon">
                                    <a href="appointmentlist?online=1"><i class="fa fa-globe" style="color:green; font-size: 35px; float: right;"  title="Online Appointments" data-toggle="tooltip" data-html="true"></i></a>
                                </div>
                                            </div>
                                            
                                            
                                    <?php
                                        form_close();
                                    ?>   
                                        
                                    </div>
                                    
                                    <div class="panel-body  table-responsive">
                                        <table width="100%" class="table table-striped table-bordered table-hover dt-responsive"  id="appointment">
                                            <thead>
                                                <tr>
                                                    <th class="all"><?php echo lang('Status'); ?></th>
                                                    <th class="all"><?php echo lang('Patient Name'); ?></th>
                                                    <th class="all"><?php echo lang('Reason'); ?></th>
                                                    <th class="all"><?php echo lang('Phone Number'); ?></th>
                                                    <th class="all"><?php echo lang('Doctor'); ?></th>
                                                    <th class="all"><?php echo lang('Date'); ?></th>
                                                    <th class="all"><?php echo lang('Time'); ?></th>
                                                    <th class="desktop"><?php echo lang('Action'); ?></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php 
                                                    foreach ($appointments as $appointment): 
                                                ?>

                                                <tr>
                                                    <?php 
                                                    $aid = $appointment['id'];
                                                    $queue_number = $appointment['queue_number'] ?? '?';
                                                    if($appointment['cancelation_reason']!="" || $appointment['custom_note']!=""){
                                                        $comment_color = "style='color: #f0ad4e;cursor:pointer;'";
                                                    }
                                                    else{
                                                        $comment_color = "style='color: #81900c;cursor:pointer;'";
                                                    }
                                                        if($appointment['appointment_status']==\BluelimeConfiguration::APPOINTMENT_CANCELED)
                                                        echo "<td class='bg-danger'>Cancelled<a ".$comment_color." onclick='show_appointment_comment($aid)'> <i class='fa fa-comment fa-2x'></i></a></td>"; 
                                                        elseif($appointment['appointment_status']==\BluelimeConfiguration::APPOINTMENT_LATE_VISIT)
                                                        echo "<td class='bg-primary'>Late Visit<a ".$comment_color." onclick='show_appointment_comment($aid)'> <i class='fa fa-comment fa-2x'></i></a></td>";
                                                        elseif($appointment['appointment_status']==\BluelimeConfiguration::APPOINTMENT_MISSED)
                                                        echo "<td class='bg-warning'>Missed<a ".$comment_color." onclick='show_appointment_comment($aid)'> <i class='fa fa-comment fa-2x'></i></a></td>";
                                                        else if($appointment['appointment_status']==\BluelimeConfiguration::APPOINTMENT_VISITED)
                                                        echo "<td class='bg-info'>Visited<a ".$comment_color." onclick='show_appointment_comment($aid)'> <i class='fa fa-comment-x fa-2x'></i></a></td>";  
                                                        else if($appointment['appointment_status']==\BluelimeConfiguration::APPOINTMENT_PENDING)
                                                        echo "<td style='background-color: black; color:white'>Pending<a ".$comment_color." onclick='show_appointment_comment($aid)'> <i class='fa fa-comment' fa-2x></i></a></td>";
                                                        else  
                                                        echo "<td class='bg-success'>Active<a ".$comment_color." onclick='show_appointment_comment($aid)'> <i class='fa fa-comment fa-2x'></i></a></td>";
                                                    ?>
                                                    
                                                    <td><?php echo $appointment['full_name']?>&emsp;<span class="fa fa<?php 
                                                        if($appointment['paid_appointment']>0){ 
                                                            echo "-globe";
                                                        }
                                                        elseif ($appointment['paid_appointment']==0 && $appointment['patient_id']==0){
                                                            echo "-user-plus";
                                                        }
                                                        else{
                                                            echo "";
                                                        }
                                                        ?>" style="color:green; font-size: 25px"></span></td>
                                                    <td><?php echo $appointment['appointment_reason']?></td>
                                                    <td><?php echo $appointment['mobile']?></td>
                                                    <td><?php echo $appointment['doctor_name']?></td>
                                                    <td><?php echo $new_date_format = date('Y-m-d', strtotime($appointment['appointment_date']));?></td>
                                                    <td><?php echo $appointment['time']?></td>
                                                    
                                                    
                                                    <td>
                                                        <button type="button" class="btn btn-info btn-sm" title="Print Appointment" data-toggle="tooltip" data-html="true" onclick="showAppointmentPrintModal('<?php echo htmlspecialchars($queue_number, ENT_QUOTES, 'UTF-8') ?>', '<?php echo htmlspecialchars($appointment['full_name'], ENT_QUOTES, 'UTF-8') ?>', '<?php echo htmlspecialchars($appointment['doctor_name'], ENT_QUOTES, 'UTF-8') ?>', '<?php echo $appointment['patient_id'] ?? '' ?>', '<?php echo $appointment['mobile'] ?? '' ?>', '<?php echo date('Y-m-d', strtotime($appointment['appointment_date'])).' '.$appointment['time']  ?>')"><i class="fa fa-print"></i></button>
                                                        <a href="newappointment?app_id=<?=$appointment['id']?>&id=<?=$appointment['patient_id']?>" class="btn btn-primary btn-sm" title="Edit Appointment" data-toggle="tooltip" data-html="true"> <i class="fa fa-edit"></i></a>
                                                        <a href="#?id=<?=$appointment['id']?>" class='btn btn-danger btn-sm'  data-toggle='<?php echo($appointment['appointment_status']==3 or $appointment['appointment_status']==-1)? "": "modal"; ?>' data-target="#confirm_cancel<?php echo $appointment['id']?>"<?php echo($appointment['appointment_status']==3 or $appointment['appointment_status']==-1)? "disabled": ""; ?> > <span title="Cancel Appointment" data-toggle="tooltip" data-html="true"><i class="fa fa-trash"></i><span></a>                                                        
                                                        <?php if ($appointment['id']){  ?><a title="Visit" data-toggle="tooltip" data-html="true"<?php echo($appointment['appointment_status']==3 or $appointment['appointment_status']==-1)? "": "href"; ?>="<?php echo($appointment['patient_id']==0)?"index":"revisit"; ?>?app_id=<?=$appointment['id']?>&did=<?=$appointment['doctor_id']?>&id=<?=$appointment['patient_id']?>&name=<?=$appointment['full_name']?>&mob=<?=$appointment['mobile']?>" class="btn btn-info btn-sm" <?php echo($appointment['appointment_status']==3 or $appointment['appointment_status']==-1)? "disabled": ""; ?>><i class="fa fa-eye" ></i></a>   
                                                       <?php }?>   
                                                        </a>
                                                    </td>
                                                    
                                                </tr>
                                                    <div class="modal fade" id="confirm_cancel<?php echo $appointment['id']?>" tabindex="-1" role="dialog" aria-labelledby="CancelAppointment" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="exampleModalLabel">Cancel Appointment</h5>
                                                                    </div>
                                                                    <div style="margin: 20px;" class="row"><p><b>Name&emsp;&nbsp;&nbsp;</b><?php echo @$appointment['full_name']?><br>
                                                                        <b>Doctor&emsp;</b><?php echo $appointment['doctor_name']?><br>
                                                                        <b>Date&emsp;&emsp;</b><?php echo date('l d-m-Y', strtotime($appointment['appointment_date'])) ?><br>
                                                                        <b>Time&emsp;&nbsp;&nbsp;&nbsp;</b><?php echo $appointment['time']?><br></p></div>
                                                                        
                                                                    <div style="margin: 20px;">
                                                                            <select class="form-control" id="cancel_reasonc<?=$aid?>" name="cancel_reason" style="width: 100%;">
                                                                                <option value="">Select Reason</option>
                                                                                <option value="Forget the appointment" <?=$appointment['cancelation_reason']=='Forget the appointment'?"selected":""?>>Forget the appointment</option>
                                                                                <option value="Work Schedule Conflict" <?=$appointment['cancelation_reason']=='Work Schedule Conflict'?"selected":""?>>Work Schedule Conflict</option>
                                                                                <option value="Not Feeling Well" <?=$appointment['cancelation_reason']=='Not Feeling Well'?"selected":""?>>Not Feeling Well</option>
                                                                                <option value="Transportation / Crowded Road" <?=$appointment['cancelation_reason']=='Transportation / Crowded Road'?"selected":""?>>Transportation / Crowded Road</option>
                                                                                <option value="Family emergency" <?=$appointment['cancelation_reason']=='Family emergency'?"selected":""?>>Family emergency</option>
                                                                                <option value="Personal Emergency" <?=$appointment['cancelation_reason']=='Personal Emergency'?"selected":""?>>Personal Emergency</option>
                                                                                <option value="Weather" <?=$appointment['cancelation_reason']=='Weather'?"selected":""?>>Weather</option>
                                                                                <option value="Social Engagement" <?=$appointment['cancelation_reason']=='Social Engagement'?"selected":""?>>Social Engagement</option>
                                                                                <option value="Phone Number not working" <?=$appointment['cancelation_reason']=='Phone Number not working'?"selected":""?>>Phone Number not working</option>
                                                                                <option value="Call not Answered" <?=$appointment['cancelation_reason']=='Call not Answered'?"selected":""?>>Call not Answered</option>
                                                                                <option value="Comming" <?=$appointment['cancelation_reason']=='Comming'?"selected":""?>>Comming</option>
                                                                        </select>
                                                                        <textarea id="custom_reasonc<?=$aid?>" class="form-control" rows="5" style="margin-top: 10px;" placeholder="Enter Reason"><?=$appointment['custom_note']==''?"":$appointment['custom_note']?></textarea>
                                                                        </div>                  
                                                                    <div class="modal-footer">
                                                                        
                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                                                                        <input type="button" class="btn btn-danger"  value="Cancel" onclick="cancel_appointment(<?=$appointment['id']?>)">
                                                                    </div>
                                                            
                                                            </div>
                                                        </div>
                                                    </div>
<div class="modal fade" id="appointment_comment<?=$aid?>" tabindex="-1"
                                    role="dialog" aria-labelledby="appointment_comment" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Reason For Change/Cancelation</h5>
                                            </div>
                                            <div style="margin: 20px;" class="row"><p><b>Name&emsp;&nbsp;&nbsp;</b><?php echo @$appointment['full_name']?><br>
                                                                        <b>Doctor&emsp;</b><?php echo $appointment['doctor_name']?><br>
                                                                        <b>Date&emsp;&emsp;</b><?php echo date('l d-m-Y', strtotime($appointment['appointment_date'])) ?><br>
                                                                        <b>Time&emsp;&nbsp;&nbsp;&nbsp;</b><?php echo $appointment['time']?><br></p></div>
                                            <div style="margin: 20px;">
                                                
                                                <select class="form-control" id="cancel_reason<?=$aid?>" name="cancel_reason" style="width: 100%;">
                                                    <option value="">Select Reason</option>
                                                    <option value="Forget the appointment" <?=$appointment['cancelation_reason']=='Forget the appointment'?"selected":""?>>Forget the appointment</option>
                                                    <option value="Work Schedule Conflict" <?=$appointment['cancelation_reason']=='Work Schedule Conflict'?"selected":""?>>Work Schedule Conflict</option>
                                                    <option value="Not Feeling Well" <?=$appointment['cancelation_reason']=='Not Feeling Well'?"selected":""?>>Not Feeling Well</option>
                                                    <option value="Transportation or Crowded Road" <?=$appointment['cancelation_reason']=='Transportation or Crowded Road'?"selected":""?>>Transportation or Crowded Road</option>
                                                    <option value="Family emergency" <?=$appointment['cancelation_reason']=='Family emergency'?"selected":""?>>Family emergency</option>
                                                    <option value="Personal Emergency" <?=$appointment['cancelation_reason']=='Personal Emergency'?"selected":""?>>Personal Emergency</option>
                                                    <option value="Weather" <?=$appointment['cancelation_reason']=='Weather'?"selected":""?>>Weather</option>
                                                    <option value="Social Engagement" <?=$appointment['cancelation_reason']=='Social Engagement'?"selected":""?>>Social Engagement</option>
                                                    <option value="Phone Number not working" <?=$appointment['cancelation_reason']=='Phone Number not working'?"selected":""?>>Phone Number not working</option>
                                                    <option value="Call not Answered" <?=$appointment['cancelation_reason']=='Call not Answered'?"selected":""?>>Call not Answered</option>
                                                    <option value="Comming" <?=$appointment['cancelation_reason']=='Comming'?"selected":""?>>Comming</option>
                                                    <option value="Visited! but appointment registered with different mobile" <?=$appointment['cancelation_reason']=='Visited! but appointment registered with different mobile'?"selected":""?>>Visited! but appointment registered with different mobile</option>
                                            </select>
                                            <textarea id="custom_reason<?=$aid?>" class="form-control" rows="5" style="margin-top: 10px;" placeholder="Enter Reason"><?=$appointment['custom_note']==''?"":$appointment['custom_note']?></textarea>
                                            </div>
                                            


                                            <div class="modal-footer">
                                                <input type="button" class="btn btn-secondary" data-dismiss="modal" value="Close">
                                                <input type="button" class="btn btn-primary" value="Save" onclick="save_comment(<?=$aid?>)">

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

        <!-- Print Appointment Modal -->
        <div class="modal fade" style="height: 160mm; width: 90mm;" id="printAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="PrintAppointmentNumber" aria-hidden="true">
            <div class="modal-content">
                <div class="modal-body" style="color: black;">
                    <div id="printAppointmentArea">
                        <div>
                            <?php echo '<img width="100" style="margin-bottom:10px" src="data:image/jpeg;base64,'.base64_encode($company_header['logo_printouts']).'"/>'; ?>
                            <span style="font-size:11px; float:right; border-left: 1px solid black; padding-left: 12px; height: 60px;">
                                <table>
                                    
                                    <tr><td></td><td></td></tr>
                                    <tr><td>Tel.</td><td><?= '6797' ?></td></tr>
                                    <tr><td colspan="2" style="font-size:9px;"><?= isset($company_header) && !empty($company_header['website']) ? htmlspecialchars($company_header['website']) : '' ?></td></tr>
                                    <tr><td colspan="2" style="font-size:9px;"><?= isset($company_header) && !empty($company_header['email']) ? htmlspecialchars($company_header['email']) : '' ?></td></tr>
                                </table>
                            </span>
                        </div>
                        <hr style="border-width: 2px;">
                        <div class="container header">
                            <div class="row details-content">
                                <div class="single-header-info">
                                    <span style="font-size:12px;"><strong>Patient Name: </strong><span id="app_patient_name"></span></span><br>
                                    <span style="font-size:12px;"><strong>Card No: </strong><span id="app_mrn_text"></span></span><br>
                                    <span style="font-size:12px;"><strong>Assigned Doctor: </strong><span id="app_doctor_name"></span></span><br>
                                    <span style="font-size:12px;"><strong>Appointment Date: </strong><span id="app_visit_date_text"></span></span><br>
                                    <span style="font-size:14px;"><strong><?= "With Appointment" ?></strong><br>
                                </div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row details-content">
                                <div style="width: 80mm;">
                                    <div class="table-responsive marg"><span style="font-size:14px;"><div class="col-sm-4 col-md-4"><strong>Queue No</strong></div></span>
                                    </div>
                                    <div class="col-sm-12 col-md-12" align="center" style="border-width:2px; text-align:center; font-weight:bold; font-size:40px; text-align:text-center; width:120px">
                                        <p style="border-style: dotted; margin:0; padding:0px;"><span id="app_queue_number_text"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <span style="font-size: smaller; ">Printed on <?= date('Y-m-d H:i') ?></span>
                    <hr>
                    <p style="font-size:8px; text-align: center;">Powered by Bluelime Technologies<br>www.bluelimeplc.com</p>
                </div>
                <div class="modal-footer col-md-12">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                    <button type="button" class="btn btn-success" onclick="printAppointmentContent('printAppointmentArea')">Print</button>
                </div>
            </div>
        </div>

                        
                            
    </section>
            </div>
</div>


<script type="text/javascript">
    function show_appointment_comment(id){
                    $("#appointment_comment"+id).modal({keyboard: false});
                }
    function save_comment(id){
        if(!validatereason(id)){
            return false;
        }
        let cancel_reason = $("#cancel_reason"+id).val();
        let custom_reason = $("#custom_reason"+id).val();
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/reception/save_comment",
            data: {app_id:id, cancel_reason: cancel_reason, custom_reason: custom_reason},
            success: function(result){
                try{
                    let r = JSON.parse(result);
                    if(r.status == "success"){
                        $("#appointment_comment"+id).modal('hide');
                        dental_show_flash('set_reference_flash', r.message);
                    }
                    else
                        dental_show_flash('set_reference_flash', result);
                }
                catch (e){
                    error_html = "Invalid JSON returned.<br><u>Result</u>:<br>" + result + "<hr>Exception:<br>"+e;
                    dental_show_flash('set_reference_flash', error_html);
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                error_html = "Update is not made. <br><u>Error</u>:<br>" + errorThrown;
                dental_show_flash('set_reference_flash', error_html, false);
            }
        });
    }
    function cancel_appointment(id){
        if(!validatereasonc(id)){
            return false;
        }
        let cancel_reason = $("#cancel_reasonc"+id).val();
        let custom_reason = $("#custom_reasonc"+id).val();
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/reception/cancelappointment",
            data: {app_id:id, cancel_reason: cancel_reason, custom_reason: custom_reason},
            success: function(result){
                try{
                    let r = JSON.parse(result);
                    if(r.status == "success"){
                       location.reload();
                    }
                    else
                        dental_show_flash('set_reference_flash', result);
                }
                catch (e){
                    error_html = "Invalid JSON returned.<br><u>Result</u>:<br>" + result + "<hr>Exception:<br>"+e;
                    dental_show_flash('set_reference_flash', error_html);
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                error_html = "Update is not made. <br><u>Error</u>:<br>" + errorThrown;
                dental_show_flash('set_reference_flash', error_html, false);
            }
        });
    }
    function validatereason(id){
        let cancel_reason = $("#cancel_reason"+id).val();
        let custom_reason = $("#custom_reason"+id).val();
           
        if (cancel_reason == '' && custom_reason == ''){
                toastr['error']("Please Enter  a valid Reason for Cancelation or Change");
                return false;
        }
        else{               
            return true;
        }
        
         
    }
    function validatereasonc(id){
        let cancel_reason = $("#cancel_reasonc"+id).val();
        let custom_reason = $("#custom_reasonc"+id).val();
           
        if (cancel_reason == '' && custom_reason == ''){
                toastr['error']("Please Enter  a valid Reason for Cancelation or Change");
                return false;
        }
        else{               
            return true;
        }
        
         
    }
    
    function showAppointmentPrintModal(queueNumber, patientName, doctorName, mrn, phone, appointmentDate) {
        $('#app_queue_number_text').text(queueNumber);
        $('#app_patient_name').text(patientName);
        $('#app_doctor_name').text(doctorName);
        $('#app_mrn_text').text(mrn ? 'MRN-' + String(mrn).padStart(5, '0') : '');
        $('#app_phone_text').text(phone);
        $('#app_visit_date_text').text(appointmentDate);
        $('#printAppointmentModal').modal('show');
    }

    function printAppointmentContent(el) {
        var restorepage  = $('body').html();
        var printcontent = $('#' + el).clone();
        $('body').empty().html(printcontent);
        window.print();
        $('body').html(restorepage);
        location.reload();
    }
    
</script>
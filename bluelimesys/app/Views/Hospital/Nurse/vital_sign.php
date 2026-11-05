<?php

function print_nurse_chief_complaints(array $chief_complaints): void
{
    foreach($chief_complaints as $index => $complaint){
        $title = htmlspecialchars($complaint['title'] ?? '', ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars($complaint['description'] ?? '', ENT_QUOTES, 'UTF-8');
        echo '<div class="encounter_list" id="nurse_chief_complaint_'.$index.'">
                <a class="glyphicon glyphicon-minus-sign remove_encounter color-red" onclick="remove_nurse_chief_complaint(\''.$index.'\')" role="button"></a>'.$title.' | '.$description.'</div>';
    }
}

$nurse_visit_encounter = $visitEncounter['chief_complaints'] ?? [];

foreach($related_visits_encounter as $encounter){

        $related_chief_complaints .= get_related_visits_encounter_table($encounter['chief_complaints'],$encounter['registered_date']);
        $related_physical_finding .= get_related_visits_encounter_table($encounter['physical_finding'],$encounter['registered_date']);
        $related_hpi .= get_related_visits_encounter_table($encounter['hpi'],$encounter['registered_date']);
        $related_past_medical_history .= get_related_visits_encounter_table($encounter['past_medical_history'],$encounter['registered_date']);   
        $related_diagnosis .= get_related_visits_encounter_table($encounter['diagnosis'],$encounter['registered_date']);   
        $related_medication .= get_related_visits_encounter_table($encounter['medication'],$encounter['registered_date']);   
        $related_procedure .= get_related_visits_encounter_table($encounter['procedure'],$encounter['registered_date'],true);   
        $related_post_procedure_diagnosis .= get_related_visits_encounter_table($encounter['post_procedure_diagnosis'],$encounter['registered_date']);
        $related_post_treatment .= get_related_visits_encounter_table($encounter['post_treatment'],$encounter['registered_date']);
    }
    $related_visits_paid_service_html = "";
    $related_visits_paid_service_laboratory = "";
    $related_visits_paid_service_procedure = "";
    $related_visits_paid_service_other = [];
    $there_is_unpaid_service = false;
    foreach($related_visits_paid_service as $paid_service){
        if(($paid_service['service_type'] == 'Consultation'))
            continue;
        $class = 'paid';
        if(intval($paid_service['service_status']) == \BluelimeConfiguration::SERVICE_STATUS_DELAY){
            $class = 'unpaid hidden';
            $there_is_unpaid_service = true;
        }
        
        if($paid_service['service_type'] == BluelimeConfiguration::SERVICE_TYPE::LABORATORY){
            if($related_visits_paid_service_laboratory == '')
                $related_visits_paid_service_laboratory = '<tr><th colspan="3" data-toggle="collapse" href="#lab_payed_services" aria-expanded="true" aria-controls="lab_payed_services" style="cursor:pointer;"><b>Laboratory</b></th></tr><tbody class="collapsed" id="lab_payed_services">';
        
            $related_visits_paid_service_laboratory .= '
                                            <tr class="'.$class.'">
                                                <td><span class="fa fa-circle payment-flag"></span> <span class="service_name">'.$paid_service['service_name'].'</span></td>
                                                <td>'.$paid_service['bill_quantity'].'</td>
                                                <td>'.number_format($paid_service['bill_quantity']*$paid_service['bill_amount']).'</td>
                                            </tr>';
        }
        elseif($paid_service['service_type'] == BluelimeConfiguration::SERVICE_TYPE::PROCEDURE){
            if($related_visits_paid_service_procedure == '')
                $related_visits_paid_service_procedure = '<tr><td colspan="3"><b>Procedure</b></td></tr>';
            $related_visits_paid_service_procedure .= '
                                            <tr class="'.$class.'">
                                                <td><span class="fa fa-circle payment-flag"></span> <span class="service_name">'.$paid_service['service_name'].'</span></td>
                                                <td>'.$paid_service['bill_quantity'].'</td>
                                                <td>'.number_format($paid_service['bill_quantity']*$paid_service['bill_amount']).'</td>
                                            </tr>';
        }
        else{
            if(($related_visits_paid_service_other[$paid_service['service_type']] ?? '') == '')
                $related_visits_paid_service_other[$paid_service['service_type']] = '<tr><td colspan="3"><b>'.$paid_service['service_type'].'</b></td></tr>';
            $related_visits_paid_service_other[$paid_service['service_type']] .= '
                                            <tr class="'.$class.'">
                                                <td><span class="fa fa-circle payment-flag"></span> <span class="service_name">'.$paid_service['service_name'].'</span></td>
                                                <td>'.$paid_service['bill_quantity'].'</td>
                                                <td>'.number_format($paid_service['bill_quantity']*$paid_service['bill_amount']).'</td>
                                            </tr>';
        }
    }

    $related_visits_paid_service_laboratory .= '</tbody>'; 
    $related_visits_paid_service_html = $related_visits_paid_service_procedure . $related_visits_paid_service_laboratory . implode(" ",$related_visits_paid_service_other);

    if($related_visits_paid_service_html != ''){
        $related_visits_paid_service_html = '
                                        <table class="table table-bordered dental-payed-services-table" >
                                            <thead>
                                                <tr>
                                                    <th>Service name</th><th width="20">Qty</th><th width="20">TOTAL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            '. $related_visits_paid_service_html .'
                                            </tbody>
                                        </table>';
    }
    $mrn_no = $patient['id'];
    $formated_mrn_no = sprintf('%05d', $mrn_no);
    $loggeduserdata = $this->data['loggeduserdata'];
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $visit = $this->data['visits'];
    $vitals = $this->data['vitals'];
    $doctors = $this->data['doctors'];
    $date_of_birth = \BluelimeSystem::year_difference($patient['date_of_birth'],date('Y-m-d'));
    $app_id = @$_GET['app_id'];
    $app_name= explode(" ", @$appointment[0]['full_name']);
    $fname = (isset($patient['first_name']))?@$patient['first_name']:@$app_name[0];
    $mname = (isset($patient['middle_name']))?@$patient['middle_name']:@$app_name[1];
    $lname = (isset($patient['last_name']))?@$patient['last_name']:@$app_name[2];
    $mobile = (isset($patient['mobile']))?@$patient['mobile']:@$appointment[0]['mobile'];
    $service_list = $this->data['service_list'];
    $orderOption = $this->data['orderOption'];
    $visit_id = $visit['id'] ?? 0;

                            
    $url_for_mobile_org = 'http://192.168.1.8/hospital/reception/newmanualcard_mobile?id='.$_GET['id'];
    $url_for_mobile = urlencode($url_for_mobile_org);
    

    function generate_order_option_row(string $option_id, int $service_id, string $service_name, float $quantity_val){
        $service_key = 'service_' . $service_id;
        $option_key = "option_" . $option_id;
        if(intval($service_id) < 0)
            $service_key = 'service__' . (-1 * $service_id);
        if(intval($service_id) != 1){
        echo '
            <tr id="order_option_service_' . $option_id . '_' . $service_id .'">
                <td>' . $service_name . '</td>
                <td>
                    <div class="input-group">
                        <span onclick="decrement_order_quantity(this, \''.$option_key.'\', \''.$service_key.'\')" class="input-group-addon" role="button" style="padding:6px 5px !important;"><a class="fa fa-minus"></a></span>
                            <input type="text" onchange="update_order_quantity(this, \''.$option_key.'\', \''.$service_key.'\')" class="form-control text-center quantity" value="' . $quantity_val . '" style="padding:6px 4px !important;">
                        <span onclick="increment_order_quantity(this, \''.$option_key.'\', \''.$service_key.'\')" class="input-group-addon" role="button" style="padding:6px 5px !important;"><a class="fa fa-plus"></a></span>
                </td>
                <td><button class="btn btn-danger" onclick="remove_service_from_order_option(' . $option_id . ', ' . $service_id . ');" type="button"><i class="fa fa-trash" ></i></button></td>
            </tr>';
        }
    }

?>
<link rel="stylesheet" href="/Image-gallery/assets/css/jquery.lightbox.css">
<link rel="stylesheet" href="/Image-gallery/assets/css/demo.css">
<script src="/Image-gallery/assets/js/jquery.lightbox.js"></script>
<style>
    	
@keyframes jump {
  0%{
    font-size: 25px;
    color: orange;
  }
  50%{
    font-size: 45px;
    color: red;
  }
  100%{
    font-size: 25px;
    color: orange;
  }
}
	.jump
	{
 		animation: jump 1.5s ease 0s normal ;
        color: orange;
 		/*animation: jump 1.5s ease 0s infinite normal ;*/
	}

    .dropZoneOverlay, .FileUpload {
            width: 120px;
            height: 130px;
        }

        .dropZoneOverlay {
            font-family: cursive;
            color: #7066fb;
            position: absolute;
            top: 0px;
            text-align: center;
        }

        .FileUpload {
            opacity: 0;
            position: relative;
            z-index: 1;
        }
</style>
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Nurse</h1>
            <small>Vital Sign</small>
           
        </div>
    </section>


    <!-- Main content -->
    <section class="content">

        <div class="row">

            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-2">

                                <h3 style="margin-top: 5px !important;">
                                    <b>
                                        <?= "MRN-".$formated_mrn_no;?>
                                    </b>
                                    <a  class="btn btn-primary btn-sm" title="Edit Patient" data-toggle="tooltip" data-html="true" onclick = "show_edit_patient()"><i class="fa fa-edit"></i> </a>
                                            
                                </h3>

                                <b><?= $patient['first_name'].' '.$patient['middle_name'].' '.$patient['last_name'];?></b><br>
                                <b>Age: <?= $date_of_birth;?></b><br>
                                <?= $patient['mobile'];?>

                                <?php 
                                                if($patient['company_id'] != 0):
                                            ?>
                                <br>
                                <input type="button" class="btn btn-danger" value="<?= $patient['company_name'] ?>">
                                <?php
                                                endif
                                            ?>

                                <!--input type="button"  class="btn-xs btn-success" style="background-color: green; border-0" value="ET"-->
                            </div>
                            <div class="col-sm-4">
                                <?php
                                                helper('form');
                                                $attributes = array('role'=>'form', 'method'=>'post', 'class'=>'inline', 'onsubmit'=>'return confirm("Are you sure you want change Doctor?")');
                                                echo form_open_multipart('/hospital/nurse/changedoctor', $attributes);
                                            ?>
                                <br>


                                <label for="assigned_doctor" class="control-label"><i
                                        style="width: 90px; display: inline-block;"></i><b>Assign to doctor</b></label>
                                <div class="input-group">
                                    <input type="hidden" name="visit_id" value="<?php echo $visit['id']; ?>">
                                    <input type="hidden" name="vital_id" value="<?php echo $_GET['id']; ?>">

                                    <div class="text-center"
                                        style="vertical-align: middle; padding:5px; display: table-cell; width:90px;">
                                        <img src="/img/loading-loading-forever.gif" width="20px;" style="display: none;"
                                            id="waitingBusy">
                                        <span class="label label-warning color-violet" id="waitingNo"
                                            style="display: none;">200 waiting</span>
                                    </div>
                                    <select id="assigned_doctor" name="assigned_doctor" class="form-control" required
                                        onchange="get_no_of_waiting(this.value)">
                                        <option></option>
                                        <?php
                                                                foreach($doctors as $doctor):
                                                            ?>
                                        <option value="<?= $doctor['id'] ?>"
                                            <?= ($doctor['id']==$visit['doctor_id'])?"selected":"" ?>>
                                            <?= $doctor['name'] ?></option>';
                                        <?php
                                                                endforeach
                                                            ?>
                                    </select>
                                    <div class="input-group-btn" style="vertical-align: top;">
                                        <button class="btn btn-success" type="submit">
                                            Change <i class="fa fa-refresh"></i>
                                        </button>
                                    </div>
                                </div>
                                <div id="doctor_schedule_flash" class="alert alert-warning hide" role="alert"
                                    style="margin-top: 5px; margin-left: 90px;">
                                    <span></span>
                                </div>

                                <?php echo form_close(); ?>
                            </div>

                            
                                    <div class="col-sm-5 panel-body" style="font-size: 11px; float: right;">
                                        <div class="col-sm-1 dental-menu-icons-01">
                                            <a class="fa fa-calendar" title="Schedule Appointment"
                                                data-target="#schedule_appointment" data-toggle="tooltip" data-html="true" onclick="show_schedule_appointment()"></a>
                                                
                                            </div>
                                        
                                        <div class="col-sm-11">
                                        
                                        
                                        <table width="100%" class="table table-striped table-bordered table-hover dt-responsive" id="appointmentS">
                                        <thead>
                                            <tr>
                                                <th class="all"><?php echo lang('Status'); ?></th>
                                                <th class="all"><?php echo lang('Reason'); ?></th>
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
                                                    else  
                                                    echo "<td class='bg-success'>Active</td>";
                                                ?>
                                                </td>
                                                <td><?php echo $appointment['appointment_reason']?></td>
                                                <td><?php echo $new_date_format = date('Y-m-d', strtotime($appointment['appointment_date']));?></td>
                                                <td><?php echo $appointment['time']?></td>
                                                
                                            </tr>
                                                

                                            <?php
                                                endforeach;
                                            ?>
                                        </tbody>
                                    </table>
                                   
                                            </div>
                                    
                                </div>
                            
                      

    
                        </div>
                        
                        <a style="bottom: 0;  float: right;" href="/hospital/nurse/newmedicalcertificate?pid=<?=$mrn_no?>&vid=<?=$visit_id?>" title="Medical Certificate" data-toggle="tooltip" data-html="true"><img src="/img/medical-certificate.png" width="35px"></a>
                         <a style="bottom: 0; margin-right: 10px; float: right;" href="/hospital/nurse/newprogresspicture?id=<?php echo $mrn_no ?>&vid=<?php echo $visit_id ?>"><img src="/img/progresspicture.png" width="35px" title="Progress Picture" data-toggle="tooltip" data-html="true"></a>       

                    </div>
                       
                </div>
            </div>


            <!-- Medical Info -->
            <div class="col-sm-6 col-md-6">
                <div class="panel panel-bd ">
                    <div class="panel-heading">
                        <div class="panel-title">
                        <button class="btn btn-danger" style="float: right;" onclick="show_visitclose_modal()">Visit Close</button>
                            <h4>Vital Sign</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php
                                            $attributes = array('role'=>'form', 'method'=>'post');
                                            helper('form');
                                            echo form_open_multipart('/hospital/nurse/addvitalsign', $attributes);
                                        ?>
                        <table class="table table-hover">
                            <input type="hidden" name="visit_id" value="<?= $_GET['id'];?>">
                            <input type="hidden" name="patient_id" value="<?=  $patient['id'];?>">
                            <input type="hidden" name="patient_name"
                                value="<?=  $patient['first_name'].' '.$patient['middle_name'].' '.$patient['last_name'];?>">
                            <input type="hidden" name="registered_by" value="<?=  $logged_user;?>">
                            <input type="hidden" name="registered_by_name" value="<?=  $logged_user_name;?>">
                            <input type="hidden" name="doctor_id" value="<?=  $visit['doctor_id'];?>">
                            <tr>
                                <td>Blood pressure <i style="color:red"><?=$date_of_birth < 19? "":"*"?></i></td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" min="30" max="250" autocomplete="off"
                                            <?=$date_of_birth < 19? "":"required"?> name="systolic" class="form-control"
                                            placeholder="systolic">
                                        <span class="input-group-addon"></span>
                                        <input type="number" min="30" max="150" autocomplete="off"
                                            <?=$date_of_birth < 19? "":"required"?> name="diastolic"
                                            class="form-control" placeholder="diastolic">
                                    </div>
                                </td>

                            </tr>
                            
                            <tr>
                                <td>Weight <i style="color:red"><?=$date_of_birth < 18 && $date_of_birth >= 2? "*":""?></i></td>
                                <td><input type="number" min="1" max="150" autocomplete="off" <?=$date_of_birth < 18 && $date_of_birth >= 2? "required":""?> placeholder="KG"
                                        class="form-control" name="weight"></td>
                            </tr>
                            <tr>
                                <td>Temperature</td>
                                <td><input type="number" min="30" max="50" step=".1" autocomplete="off"
                                        placeholder="°C" class="form-control" name="temperature"></td>
                            </tr>
                            <tr>
                                <td>Pulse rate</td>
                                <td><input type="number" min="30" max="130" autocomplete="off" 
                                        placeholder="Pulse per minute"  class="form-control" name="pulse_rate">
                                </td>
                            </tr>
                            <tr>
                                <td>Respiration rate</td>
                                <td><input type="number" class="form-control" autocomplete="off"
                                        placeholder="Breaths per minute" name="respiration_rate"></td>
                            </tr>
                            <td>Note</td>
                            <td>
                                <textarea placeholder="Additional Note" style="resize: none;" class="form-control"
                                    name="note"></textarea>
                            </td>
                            </tr>

                        </table>
                        
                        <div style="float: right; ">
                                <input type="submit" class="btn btn-success" value="Save">
                            </div>
                    </div>
                    
                    <div class="panel-heading">
                        <div class="panel panel-bd">
                            <div class="panel-body">
                                <table class="table table-striped">
                                    <tr>
                                        <th>B/P</th>
                                        <th>Weight</th>
                                        <th>Temp. °C</th>
                                        <th>P.R</th>
                                        <th>R.R</th>
                                        <th>Note</th>
                                    </tr>
                                    <?php
                                                foreach($vitals as $vital):
                                                   
                                            ?>
                                    <tr>
                                        <td><?php echo $vital['blood_pressure'] ?></td>
                                        <td><?php echo $vital['weight'] ?></td>
                                        <td><?php echo $vital['temperature'] ?></td>
                                        <td><?php echo $vital['pulse_rate'] ?></td>
                                        <td><?php echo $vital['respiration_rate'] ?></td>
                                        <td><?php echo $vital['note'] ?></td>
                                        <td><a role="button" class="fa fa-minus-circle text-danger" onclick="remove_vital(<?=$vital['id']?>)"></a></td>
                                                
                                    </tr>
                                    <?php
                                                endforeach;
                                            ?>
                                </table>

                            </div>
                        </div>
                        <?php echo form_close(); ?>
                        <div class="panel-body">

                        </div>

                    </div>
                </div>

            </div>
            <div class="col-sm-6 col-md-6">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Chief Complaint</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="encounter_container">
                            <table width="100%">
                                <tr>
                                    <td>
                                        <input type="text" id="nurse_chief_complaint_title" class="form-control" list="nurse_chief_complaint_list">
                                        <datalist id="nurse_chief_complaint_list">
                                            <?php foreach($chiefComplaintList as $complaint): ?>
                                                <option value="<?= htmlspecialchars($complaint, ENT_QUOTES, 'UTF-8') ?>">
                                            <?php endforeach; ?>
                                        </datalist>
                                    </td>
                                    <td width="30">
                                        <button type="button" class="btn btn-primary" onclick="add_nurse_chief_complaint()">+</button>
                                    </td>
                                </tr>
                            </table>
                            <textarea id="nurse_chief_complaint_description" class="form-control" placeholder="Description"></textarea>
                        </div>
                        <div class="encounter_list_container" id="nurse_chief_complaint_list_container">
                            <?php print_nurse_chief_complaints($nurse_visit_encounter); ?>
                        </div>
                        <div style="text-align: right; margin-top: 10px;">
                            <button type="button" class="btn btn-success" onclick="save_nurse_chief_complaints()">Save Chief Complaint</button>
                        </div>
                        <div id="nurse_chief_complaint_flash" class="alert alert-danger hide" role="alert"><span></span></div>
                    </div>
                </div>
            </div>
            <link rel="stylesheet" href="/Image-gallery/assets/css/jquery.lightbox.css">
            <script src="/Image-gallery/assets/js/jquery.lightbox.js"></script>
            <div class="manual_card">
                <style>
                    .view_card{
                        position: fixed;
                        top: 50%;
                        z-index: 100;
                        right: -50px;
                        padding: 10px;
                        border-radius: 15px 15px 0 0;
                        background-color: dodgerblue;
                        rotate: -90deg;
                        color: #fff;
                        height: 70px;
                        border: solid 2px #00f;
                        <?= empty($patient['attached_old_card']) ? 'display: none;' : '' ?>
                    }
                    .view_card:hover{
                        right: -40px;
                        color: #ff0;
                    }
                </style>
                <?php
                    $manual_card_image = explode(',',$patient['attached_old_card']);
                    $is_first = true;
                    foreach($manual_card_image as $image_name):
                        if($is_first){
                            echo '<a role="button" class="view_card" href="/image/manual_card?image='.$image_name.'">View Card <i class="fa fa-id-card"></i></a>';
                            $is_first = false;
                        }
                        else
                            echo '<a role="button" class="view_card hidden" href="/image/manual_card?image='.$image_name.'">View Card <i class="fa fa-id-card"></i></a>';

                    endforeach;
                ?>
           <script>
                $(function() {
                    $('.manual_card a').lightbox();
                });
            </script>
            </div>
            

            <!-- Medical Info -->
            <div class="col-sm-6 col-md-6">
                <div class="panel panel-bd ">
                    <div class="panel-heading">
                        <?php
                            $attributes = array('role'=>'form', 'method'=>'post');
                            helper('form');
                            echo form_open_multipart('/hospital/nurse/addpatientmedical', $attributes);
                        ?>
                        <div class="panel-title">
                            <h4>Medical History</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <tr>
                                <td>&nbsp; </td>
                                <input type="hidden" name="pid" value="<?= $patient['id']?>">
                                <input type="hidden" name="vid" value="<?= $_GET['id']?>">
                                <td><i class="glyphicon  glyphicon-ok color-red" title="Yes"></i></td>
                                <td><i class="glyphicon  glyphicon-remove color-gray" title="No"></i></td>
                            </tr>
                            <tr>
                                <td>Blood pressure</td>
                                <td><input required type="radio" name="blood_pressure" value="Yes"
                                        <?php echo (@$patient['blood_pressure']=='Yes')?'checked':'' ?> /></td>
                                <td><input required type="radio" name="blood_pressure" value="No"
                                        <?php echo (@$patient['blood_pressure']=='No')?'checked':'' ?> /></td>
                            </tr>
                            <tr>
                                <td>Bleeding Tendency</td>
                                <td><input required type="radio" name="bleeding_tendency" value="Yes"
                                        <?php echo (@$patient['bleeding_tendency']=='Yes')?'checked':'' ?> /></td>
                                <td><input required type="radio" name="bleeding_tendency" value="No"
                                        <?php echo (@$patient['bleeding_tendency']=='No')?'checked':'' ?> /></td>
                            </tr>
                            <tr style="visibility:<?=@$patient['sex']=='Male'? "hidden":""?>">
                                <td>Pregnancy</td>
                                <td><input  type="radio" name="pregnancy" id="pregnancy" value="Yes"
                                        <?php echo (@$patient['pregnancy']=='Yes')?'checked':'' ?> /></td>
                                <td><input  type="radio" name="pregnancy" id="pregnancy" value="No"
                                        <?php echo (@$patient['pregnancy']=='No') or (@$patient['sex']=='Male')?'checked':'' ?> />
                                </td>
                            </tr>
                            <tr>
                                <td>Cardiac Problem</td>
                                <td><input required type="radio" name="cardiac_problem" value="Yes"
                                        <?php echo (@$patient['cardiac_problem']=='Yes')?'checked':'' ?> /></td>
                                <td><input required type="radio" name="cardiac_problem" value="No"
                                        <?php echo (@$patient['cardiac_problem']=='No')?'checked':'' ?> /></td>
                            </tr>
                            <tr>
                                <td>Diabetics</td>
                                <td><input required type="radio" name="diabetics" value="Yes"
                                        <?php echo (@$patient['diabetics']=='Yes')?'checked':'' ?> /></td>
                                <td><input required type="radio" name="diabetics" value="No"
                                        <?php echo (@$patient['diabetics']=='No')?'checked':'' ?> /></td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle;">Blood Group</td>
                                <td colspan="2">
                                    <select class="form-control" name="blood_group">
                                        <option value=''>--Select Blood Group--</option>
                                        <option value='A+' <?php echo (@$patient['blood_group']=='A+')?'selected':'' ?>>
                                            A+</option>
                                        <option value='A-' <?php echo (@$patient['blood_group']=='A-')?'selected':'' ?>>
                                            A-</option>
                                        <option value='B+' <?php echo (@$patient['blood_group']=='B+')?'selected':'' ?>>
                                            B+</option>
                                        <option value='B-' <?php echo (@$patient['blood_group']=='B-')?'selected':'' ?>>
                                            B-</option>
                                        <option value='O+' <?php echo (@$patient['blood_group']=='O+')?'selected':'' ?>>
                                            O+</option>
                                        <option value='O-' <?php echo (@$patient['blood_group']=='O-')?'selected':'' ?>>
                                            O-</option>
                                        <option value='AB+'
                                            <?php echo (@$patient['blood_group']=='AB+')?'selected':'' ?>>AB+</option>
                                        <option value='AB-'
                                            <?php echo (@$patient['blood_group']=='AB-')?'selected':'' ?>>AB-</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle;">
                                    Drugs Taking
                                    <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new drug"
                                        onclick="show_add_new_nurse_modal('Add New Drug', 'drug', drug_list)"></i>
                                </td>
                                <td colspan="2">
                                    <select name="drugs_taking[]" multiple data-role="tagsinput"
                                        class="form-control tagsinputs" id="drug_list_input">
                                        <?php
                                                            $drugs_taking = $patient['drugs_taking'] ?? [];
                                                            foreach($drugs_taking as $drug){
                                                                echo '<option value="'.$drug.'">'.$drug.'</option>
                                                                ';
                                                            }
                                                        ?>
                                    </select>

                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle;">
                                    Allergy
                                    <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new allergy"
                                        onclick="show_add_new_nurse_modal('Add New Allergy', 'allergy', allergy_list)"></i>
                                </td>
                                <td colspan="2">
                                    <select name="allergy[]" multiple data-role="tagsinput"
                                        class="form-control tagsinputs" id="allergy_list_input">
                                        <?php
                                                            $allergies = $patient['allergy'] ?? [];
                                                            foreach($allergies as $allergy){
                                                                echo '<option value="'.$allergy.'">'.$allergy.'</option>
                                                                ';
                                                            }
                                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle;">
                                    Other Diseases
                                    <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new disease"
                                        onclick="show_add_new_nurse_modal('Add New Disease', 'disease', disease_list)"></i>
                                </td>
                                <td colspan="2">
                                    <select name="other_diseases[]" multiple data-role="tagsinput"
                                        class="form-control tagsinputs" id="disease_list_input">
                                        <?php
                                                            $other_diseases = $patient['other_diseases'] ?? [];
                                                            foreach($other_diseases as $disease){
                                                                echo '<option value="'.$disease.'">'.$disease.'</option>
                                                                ';
                                                            }
                                                        ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <div style="float:right"><button type="submit" class="btn btn-success">Update</button></div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
            <!-- Orders -->
            <div class="col-sm-6 col-md-6">
                    <div class="panel panel-bd">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <h4>Create Orders</h4>
                                </div>
                            </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            

                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <div id="orders" class="tab-pane active"><br>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="bg-info">
                                                                <th width="450">Service</th>
                                                                <th width="100">Quantity</th>
                                                                <th>&nbsp;</th>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <input type="text" class="form-control" id="order_input" />
                                                                </td>
                                                                <td>
                                                                    <input type="number" min="1" value="1" class="form-control" id="order_option_quantity_1"/>
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-info" onclick="add_order_option();" type="button"><i class='fa fa-plus' ></i></button>
                                                                    <!--button class="btn btn-info" onclick="remove_order_option(1,1);" type="button"><i class='fa fa-minus' ></i></button-->
                                                                </td>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="order_option_service_list_1">
                                                                <?php
                                                                    foreach($orderOption as $index => $service){
                                                                        generate_order_option_row('1',$service['service_id'],$service['service_name'],floatval($service['quantity']));
                                                                    }
                                                                ?>
                                                            </tbody>
                                                    </table>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div id="save_order_option_flash" class="alert alert-danger hide" role="alert" style="display:inline-block; min-width: 300px; text-align:left;">
                                        <span></span>
                                    </div>
                                    <button type="button" id="btnSaveOrderOption" class="btn btn-success" onclick="save_order_option()">Save</button>
                                </div>
                    </div>

                        <!--div class="panel panel-bd">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <h4>Medical History (<small><i class="glyphicon  glyphicon-remove" style="color:#ccc;"></i> <i class="glyphicon  glyphicon-ok"  style="color: #faa;"></i></small>)</h4>
                                </div>
                            </div>
                            <div class="panel-body">
                                <table class="table">
                                    <tr>
                                        <td width="120">Blood pressure</td>
                                        <td width="150"><?= $patient['blood_pressure'] === 'Yes' ? '<i class="glyphicon  glyphicon-ok color-red"></i>' : '<i class="glyphicon  glyphicon-remove color-gray"></i>';?></td>
                                        <td>Bleeding Tendency</td>
                                        <td><?= $patient['bleeding_tendency'] === 'Yes' ? '<i class="glyphicon  glyphicon-ok color-red"></i>' : '<i class="glyphicon  glyphicon-remove color-gray"></i>';?></td>
                                    </tr>
                                    <tr>
                                        <td>Pregnancy</td>
                                        <td><?= $patient['pregnancy'] === 'Yes' ? '<i class="glyphicon  glyphicon-ok color-red"></i>' : '<i class="glyphicon  glyphicon-remove color-gray"></i>';?></td>
                                        <td>Cardiac Problem</td>
                                        <td><?= $patient['cardiac_problem'] === 'Yes' ? '<i class="glyphicon  glyphicon-ok color-red"></i>' : '<i class="glyphicon  glyphicon-remove color-gray"></i>';?></td>
                                    </tr>
                                    <tr>
                                        <td>Diabetics</td>
                                        <td><?= $patient['diabetics'] === 'Yes' ? '<i class="glyphicon  glyphicon-ok color-red"></i>' : '<i class="glyphicon  glyphicon-remove color-gray"></i>';?></td>
                                        <td>Blood Group</td>
                                        <td><b><?= $patient['blood_group'];?></b></td>
                                    </tr>
                                </table>
                                <table class="table table-striped">
                                    <tr>
                                        <td width="120">Drugs Taking</td>
                                        <td colspan=3>
                                            <?php
                                                $drugs_taking = $patient['drugs_taking'];
                                                foreach($drugs_taking as $drug)
                                                    echo '<span class="label bg-primary dental-tags-lable">'.$drug.'</span>';
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Allergy</td>
                                        <td colspan=3>
                                            <?php
                                                $allergies = $patient['allergy'];
                                                foreach($allergies as $allergy)
                                                    echo '<span class="label bg-primary dental-tags-lable">'.$allergy.'</span>';
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Other Diseases</td>
                                        <td colspan=3>
                                            <?php
                                                $other_diseases = $patient['other_diseases'];
                                                foreach($other_diseases as $disease)
                                                    echo '<span class="label bg-primary dental-tags-lable">'.$disease.'</span>';
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div-->
                    </div>

            

        </div>
        <div class="col-md-12">
                            <div class="panel panel-bd">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4><a class="" data-toggle="collapse" href="#collapsePrescription" role="button" aria-expanded="false" aria-controls="collapsePrescription">
                                                Upload Document
                                            </a>
                                        </h4>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        helper('form');
                                        $attributes = array('class' => 'form-horizontal','role'=>'form',  'enctype' => 'multipart/form-data');
                                        echo form_open('hospital/nurse/addmanualcard', $attributes);
                                    ?> 
                                        <div class="col-sm-1">
                                            <input type="file" id="drop_zone" class="FileUpload"   accept="image/*" onchange="this.form.submit()"  name="picture"  title="Upload Patient Document" data-toggle="tooltip" data-html="true" >
                                            <input type="hidden" value="<?=$_GET['id']?>" name="patient_id">
                                            <input type="hidden" value="<?=$visit_id?>" name="visit_id">
                                            <div class="dropZoneOverlay">
                                                <img src="/api/qrcode?data=<?= $url_for_mobile ?>" alt="QR Code for mobile photo" title="Scan with mobile to take pictures." />
                                            </div>
                                            <div hidden><input type="submit"></div>
                                            <?php $result = (explode(',', $patient['attached_old_card'])); 
                                            //BluelimeSystem::show_and_die(count($result));
                                            ?>
                                            
                                            
                                        </div>
                                        <div class="col-sm-11">
                                        <div class="manual_card  <?= (empty($patient['attached_old_card']))?" hidden ":""?>" style="margin-left: 30px;">
                                                <style>
                                            div.scroll-container {
                                            white-space: nowrap;
                                            padding: 1px;
                                            }

                                            div.scroll-container img {
                                            padding: 1px;
                                            }
                                            </style>

                                                <div class="scroll-container">
                                                <?php
                                                    $manual_card_image = explode(',',$patient['attached_old_card']);
                                                    $is_first = true;
                                                    foreach($manual_card_image as $image_name):
                                                        echo '<a href="/image/manual_card?image='.$image_name.'" data-image_name="'.$image_name.'"><img src="/image/manual_card?image='.$image_name.'&thumbnail=1" style="height:100px" ></a>';
                                                    endforeach;
                                                ?>
                                                </div>
                                            
                                            </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                </div>
        <!-- Prescription History -->
        <div class="col-md-12">
                            <div class="panel panel-bd">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4><a class="" data-toggle="collapse" href="#collapsePrescription" role="button" aria-expanded="false" aria-controls="collapsePrescription">
                                                Prescription History
                                            </a>
                                        </h4>
                                    </div>
                                </div>
                                <div class="panel-body collapsed" id="collapsePrescription">
                                    <div class="row">
                                        <?php
                                            foreach($prescription_group as $group){
                                                $date = $group['prescriptions'][0]['created_at'] ?? $group['compoundings'][0]['created_at'] ?? '';
                                                //$date = $group['prescriptions'][0]['created_at'] ?? '';
                                                $prescribed_by = $group['prescriptions'][0]['registered_by_name'] ?? $group['compoundings'][0]['created_by_name'] ?? '';
                                                //$prescribed_by = $group['prescriptions'][0]['registered_by_name'] ?? '';

                                                echo '
                                        <div class="col-lg-6">
                                            <fieldset class="reset">
                                                <legend class="reset"><span class="badge badge-qty-warning">'.$date.'</span> <span class=" badge badge-qty-warning">'.$prescribed_by.'</span></legend>';
                                                
                                                if(!empty($group['prescriptions'])){
                                                    $prescription_detail = [];
                                                    foreach($group['prescriptions'] as $prescriptions){
                                                        array_push($prescription_detail, ...$prescriptions['details']);
                                                    }

                                                    echo view('Include/_prescription_print_medicine', ['prescription_detail'=>$prescription_detail]);
                                                }

                                                $compounding_mains = [];
                                                foreach($group['compoundings'] as $compounding){
                                                    array_push($compounding_mains, $compounding);
                                                }

                                                echo view('Include/_prescription_print_compounding', ['compounding_mains'=>$compounding_mains]);

                                                echo '
                                            </fieldset>
                                        </div>';
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
        </div>
        <!-- order History -->
         <div class="row">
             <div class="col-md-12">
                                <div class="form-group col-md-6">
                                    <div class="panel panel-bd">
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                <h4>Paid services</h4>
                                            </div>
                                        </div>                        
                                        <div class="form-body">
                                            <div class="box-title">
                                                <?php
                                                    if($there_is_unpaid_service)
                                                        echo '<a id="dental_unpayed_services_icon" role="button" class="fa fa-eye unhide-icon" title="Show unpaid" onclick="show_hide_payed_services()"> <small id="show_unpaid_lable">Show unpaid</small><small id="hide_unpaid_lable" class="hidden">Hide unpaid</small></a><br>';
                                                ?>
                                            </div>
                                            <div id="dental_payed_services">
                                                
                                                <?= $related_visits_paid_service_html ?>
                                            
                                            </div>
                                            <script>
                                                function show_hide_payed_services(){
                                                    $("#dental_payed_services").find('.unpaid').toggleClass('hidden');
                                                    $("#dental_unpayed_services_icon").toggleClass("fa-eye fa-eye-slash");
                                                    $("#show_unpaid_lable").toggleClass('hidden');
                                                    $("#hide_unpaid_lable").toggleClass('hidden');
                                                }
                                            </script>
                                            
                                            
                                        </div>
                                    </div>
                                </div> 
            </div>
        </div>
        <div class="row">
        <div class="col-md-12">
                <?=view('Include/_encounter_history',['history_follow_up_list'=>$encounter_history])?>
        </div>
        </div>
    </section>
</div>

<div class="modal fade" id="schedule_appointment" tabindex="-1" role="dialog" aria-labelledby="schedule_appointment"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header dental-bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Schedule Appointment</h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <div class="portlet-body form">

                        <?php 
            helper('form');
            $attributes = array('class' => 'form-horizontal','name'=>'p_info','onsubmit'=>'return validatetime()');
            echo form_open('/hospital/nurse/addappointment', $attributes);                
        ?>


                        <div class="form-body">
                            <div>
                                <div class="panel panel-bd panel-form">
                                    <div class="panel-body">
                                        <div class="portlet-body form">

                                            <div class="form-body"">
                                            <div class=" form-group col-md-12" hidden>
                                                <div class="">
                                                    <input id="mrntitle" type="hidden" name="mrn_no"
                                                        class="form-control" value="<?php echo $formated_mrn_no; ?>"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12" hidden>
                                                <label class="control-label">Full Name</label>
                                                <div class="input-group">

                                                    <input type="text" hidden id="patient_id" name="patient_id"
                                                        value="<?php echo @$patient['id']?>">
                                                    <input type="text" hidden id="vital_id" name="vital_id"
                                                        value="<?php echo @$_GET['id']?>">
                                                    <input type="text" hidden name="app_id"
                                                        value="<?php echo @$app_id ?? ""?>">
                                                    <input type="text" hidden name="paid_appointment"
                                                        value="<?php echo 0?>">
                                                    <input type="text" hidden name="appointment_status"
                                                        value="<?php echo 1; ?>">
                                                    <input type="text" hidden name="registered_by"
                                                        value="<?php echo $logged_user?>">
                                                    <input type="text" hidden name="registered_by_name"
                                                        value="<?php echo $logged_user_name?>">
                                                    <input type="text" hidden name="visit_id" value="<?php echo $visit['id']?>">
                                                    <input type="text" hidden name="reminder_sent"
                                                        value="<?php echo 0?>">
                                                    <input type="hidden" name="first_name" class="form-control"
                                                        placeholder="Name" value="<?php echo @$fname ?? ""  ?>">
                                                    <span class="input-group-addon"></span>
                                                    <input type="hidden" name="middle_name" class="form-control"
                                                        placeholder="Father Name" value="<?php echo @$mname ?? ""  ?>">
                                                    <span class="input-group-addon"></span>
                                                    <input type="hidden" name="last_name" class="form-control"
                                                        placeholder="Grand Father" value="<?php echo @$lname ?? ""  ?>">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <div class="">
                                                    <input type="hidden" placeholder="09xxxxxxxx" name="mobile"
                                                        value="<?php echo @$mobile ?? ""  ?>" required
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label class="control-label"><?php echo "Date"?> :</label>
                                                <div class="">
                                                    <input type="date" id="date" min="<?php echo  date('Y-m-d'); ?>"
                                                        value="<?php echo date('Y-m-d'); ?>"
                                                        value="<?php echo set_value('date'); ?>" name="date"
                                                        class="form-control" onchange="loadSchedule(this.value);"
                                                        placeholder="<?php echo lang('date_placeholder')?>" required>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-12">
                                                <label class="control-label">Doctor:</label>
                                                <div class="">
                                                    <select class="form-control doctor_id" id="doctor_id"
                                                        onchange="loadSchedule(this.value);check_active_appointment();"
                                                        name="doctor_id" required>
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
                                                    <textarea name="appointment_reason" class="form-control" rows="3"
                                                        required></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row"></div>
                                            <div class="alert alert-warning <?php echo (@$_GET['rt']==1)?"":"hide"; ?>"
                                                role="alert">
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
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
</div>


<div class="modal fade" id="edit_patient" tabindex="-1" role="dialog" aria-labelledby="edit_patient"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header dental-bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Patient</h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <div class="portlet-body form">

                        <?php 
            helper('form');
            $attributes = array('class' => 'form-horizontal','name'=>'p_info_edit');
            echo form_open('/hospital/nurse/editpatient', $attributes);                
        ?>


                        <div class="form-body">
                            <div>
                                <div class="panel panel-bd panel-form">
                                    <div class="panel-body">
                                    <div class="row" style="margin: 2px;">
                                                <div class="form-group col-md-12">
                                                    <label for="mrntitle" class="control-label">MRN N<u>o</u></label>
                                                    <div class="custom-button-inside-input">
                                                        <input id="mrntitle" type="text" name="mrn_no" class="form-control custom-input" value="<?= $formated_mrn_no; ?>" readonly>
                                                        
                                                        
                                                        <input type="hidden" id="page_type" value="<?=$page_type??"New"?>" >
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label class="control-label">Moblie <i style="color:red">*</i></label>
                                                    <div class="">
                                                        <input role="presentation" autocomplete="off"  onload="check_if_mobile_exists(),check_if_patient_exists()" onchange="check_if_mobile_exists(),check_if_patient_exists()" id="patient_mobile_input" minlength="10"  maxlength="10" type="number" name="mobile" class="form-control" placeholder="09xxxxxxxx" required value="<?= $mobile ?? ""; ?>">
                                                    </div>
                                                    <div id="patient_mobile_alert" role="alert">
                                                        <span></span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label class="control-label">Full Name <i style="color:red">*</i></label>
                                                    <div class="input-group">
                                                        <input type="hidden" name="registered_by" value="<?php echo $logged_user ?>">
                                                        <input type="hidden" name="id" value="<?php echo @$patient['id'] ?? ""?>">
                                                        <input type="hidden" id="new_id" name="new_id" >
                                                        <input type="hidden" name="registered_by_name" value="<?php echo $logged_user_name ?>">
                                                        <input type="hidden" name="app_id" value="<?php echo @$_GET['app_id'] ?>">
                                                        <input type="hidden" name="vital_id" value="<?php echo @$_GET['id'] ?>">
                                                        
                                                        <input  role="presentation" autocomplete="off"  onload="check_if_mobile_exists(),check_if_patient_exists()" onchange="check_if_mobile_exists(),check_if_patient_exists()"  type="text" name="first_name" id="first_name" class="form-control" placeholder="Name" value="<?php echo $fname ?? ""  ?>" required>
                                                        <span class="input-group-addon"></span>
                                                        <input  role="presentation" autocomplete="off"  type="text" name="middle_name" class="form-control" placeholder="Father Name" value="<?php echo $mname ?? ""  ?>" required>
                                                        <span class="input-group-addon"></span>
                                                        <input  role="presentation" autocomplete="off"  type="text" name="last_name" class="form-control" placeholder="Grand Father" value="<?php echo $lname ?? ""  ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group col-l-12">
                                                <div class="col-xs-2" style="padding-right:0">
                                                    <label class="control-label">Age <i style="color:red">*</i></label>
                                                        <?php
                                                        $patient_dob = isset($patient['date_of_birth']) ? $patient['date_of_birth']:'';
                                                        $diff = date_diff(date_create($patient_dob), date_create(date("Y-m-d")));
                                                        $age = $diff->format('%y');
                                                        ?>
                                                        <input class="form-control"  name="old" id="age" min="0" step="0.01" max="100" type="number"  required placeholder="Age"  onload="getdob(this.value)" oninput="getdob(this.value) " value="<?php echo $age ?? ""   ?>">
                                                    </div>
                                                    <div class="col-xs-5"style="padding-left:0">
                                                    <label class="control-label">Date of Birth</label>
                                                        <input tabindex="-1" type="date" id="dob" name="date_of_birth" style="" class="form-control" placeholder="Select Date"  onload="getAge(this.value)" onchange="getAge(this.value)"  value="<?php echo $patient['date_of_birth'] ?? ""  ?>">
                                                    </div>
                                                    
                                                    <div class="col-xs-5">
                                                        <label class="control-label">Sex <i style="color:red">*</i></label>
                                                    
                                                        <select name="sex" id="sex" class="form-control" required style="width: 160px">
                                                            <option  value="">--Select Sex--</option>
                                                            <option value="Female" <?php echo (@$patient['sex']=='Female')?'selected':'' ?>>Female</option>
                                                            <option value="Male" <?php echo (@$patient['sex']=='Male')?'selected':'' ?>>Male</option>
                                                        </select>
                                                    </div>
                                                </div>                                                
                                            </div>

                                    </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-12 col-md-12">
                                <div class="panel panel-bd">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <h4>Survey</h4>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-12">
                                            <label class="control-label">How did you discover our clinic?</label>
                                            <div class="">
                                                <select name="survey" class="form-control">
                                                
                                                <option value="">--Previous Customer--</option>
                                                <option value="TV" <?=(@$patient['survey'] == "TV")?"selected":""  ?>><?php echo "TV" ?></option>
                                                <option value="Tiktok" <?=(@$patient['survey'] == "Tiktok")?"selected":""  ?>><?php echo "Tiktok" ?></option>
                                                <option value="Instagram" <?=(@$patient['survey'] == "Instagram")?"selected":""  ?>><?php echo "Instagram" ?></option>
                                                <option value="Facebook" <?=(@$patient['survey'] == "Facebook")?"selected":""  ?>><?php echo "Facebook" ?></option>
                                                <option value="Youtube" <?=(@$patient['survey'] == "Youtube")?"selected":""  ?>><?php echo "Youtube" ?></option>
                                                <option value="Other Social Media" <?=(@$patient['survey'] == "Other Social Media")?"selected":""  ?>><?php echo "Other Social Media" ?></option>
                                                <option value="Rank Website" <?=(@$patient['survey'] == "Rank Website")?"selected":""  ?>><?php echo "Rank Website" ?></option>
                                                
                                                
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>

                </div>

            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
</div>
<div class="modal fade" id="visitclose" role="dialog">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header dental-bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Close Current Visit</h4>
                                </div>
                                <?php
                                        $attributes = array('role'=>'form', 'method'=>'post', 'class'=>'inline', 'onsubmit'=>'return validate_closevisit();');
                                        echo form_open_multipart('/hospital/nurse/closevisit', $attributes);
                                ?>
                                    <div class="modal-body">
                                        <input type="hidden" name="visit_id" value="<?= $visit_id ?>">
                                        <div class="input-group">
                                            <br>
                                            <label for="close_reason" style="color: red; text-align: center;"><b></b>You are about to close current visit! Please enter Reason</label><br>
                                            <textarea class="form-control" id="close_reason" name="close_reason" required style="width:270px"></textarea>
                                        </div>
                                        <div id="doctor_schedule_flash" class="alert alert-warning hide" role="alert" style="margin-top: 5px;">
                                            <span></span>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-success" type="submit">
                                            Close&nbsp;&nbsp;&nbsp;<i class="glyphicon  glyphicon-remove"></i>
                                        </button>
                                    </div>
                                <?php
                                    echo form_close();
                                ?>
                            </div>
                        </div>
                    </div>
<!-- Modals-->
<?= view('Include/_autocomplete_service') ?>
<?= view('Include/_autocomplete_single_string') ?>
<script>
var nurseEncounterData = <?= \BluelimeSystem::convert_array_to_string($visitEncounter) ?: '{}' ?>;
var nurseChiefComplaints = nurseEncounterData.chief_complaints || {};

function add_nurse_chief_complaint(){
    var titleInput = $('#nurse_chief_complaint_title');
    var descriptionInput = $('#nurse_chief_complaint_description');
    var title = titleInput.val().trim();

    if(title === ''){
        titleInput.css('border-color', 'red').focus();
        return false;
    }

    var index = Object.keys(nurseChiefComplaints).length;
    var key = 'chief_complaints_' + index;
    nurseChiefComplaints[key] = {title: title, description: descriptionInput.val()};
    $('#nurse_chief_complaint_list_container').append(
        '<div class="encounter_list" id="nurse_chief_complaint_' + key + '">' +
        '<a class="glyphicon glyphicon-minus-sign remove_encounter color-red" onclick="remove_nurse_chief_complaint(\'' + key + '\')" role="button"></a>' +
        $('<div>').text(title).html() + ' | ' + $('<div>').text(descriptionInput.val()).html() + '</div>'
    );
    titleInput.val('').css('border-color', '');
    descriptionInput.val('');
    return save_nurse_chief_complaints();
}

function remove_nurse_chief_complaint(key){
    delete nurseChiefComplaints[key];
    $('#nurse_chief_complaint_' + key).remove();
    save_nurse_chief_complaints();
}

function save_nurse_chief_complaints(){
    $.ajax({
        method: 'post',
        url: "<?= base_url(); ?>hospital/ajax/savechiefcomplaint",
        data: {
            visit_id: <?= $visit_id ?>,
            chief_complaints: JSON.stringify(nurseChiefComplaints)
        },
        dataType: 'json',
        success: function(result){
            if(result.status === 'success')
                toastr.success('Chief complaint saved');
            else
                $('#nurse_chief_complaint_flash span').text(result.result || 'Unable to save chief complaint.').parent().removeClass('hide');
        },
        error: function(jqXHR){
            var message = jqXHR.responseJSON?.result || 'Unable to save chief complaint.';
            $('#nurse_chief_complaint_flash span').text(message).parent().removeClass('hide');
        }
    });
    return true;
}

    function show_visitclose_modal(){
                    $("#visitclose").modal("show");
                }
var services = <?= BluelimeSystem::convert_array_to_string($service_list) ?>;
    function set_focus_after_selection(args){
                    $('#'+args[0]).focus();
                }
                
                selected_service_index['order'] = -1;
                var autocomplete_order_option = {
                    input:document.getElementById("order_input"),
                    services:services,
                    selected_index:'order',
                };
    autocomplete_service_by_options(autocomplete_order_option);
    
$(document).on("keydown", ":input:not(drug_list_input, textarea)", function(event) {
    return event.key != "Enter";
});

var substringMatcher = function(strs) {
    return function findMatches(q, cb) {
        var matches, substringRegex;

        // an array that will be populated with substring matches
        matches = [];

        // regex used to determine if a string contains the substring `q`
        substrRegex = new RegExp(q, 'i');

        // iterate through the pool of strings and for any string that
        // contains the substring `q`, add it to the `matches` array
        $.each(strs, function(i, str) {
            if (substrRegex.test(str)) {
                matches.push(str);
            }
        });

        cb(matches);
    };
};

<?php
                    $drugs = $this->data['drugs'];
                    $allergies = $this->data['allergies'];
                    $diseases = $this->data['diseases'];
                ?>

var drug_list = <?= \BluelimeSystem::convert_array_to_string($drugs); ?>;
var allergy_list = <?= \BluelimeSystem::convert_array_to_string($allergies); ?>;
var disease_list = <?= \BluelimeSystem::convert_array_to_string($diseases); ?>;


$('#drug_list_input').tagsinput({
    freeInput: false,
    tagClass: 'tag label dental-tagsinput-tag',
    typeaheadjs: ({
        hint: true,
        highlight: true,
        minLength: 1
    }, {
        name: "drugs",
        source: substringMatcher(drug_list)
    })
});

$('#allergy_list_input').tagsinput({
    typeaheadjs: ({
        hint: true,
        highlight: true,
        minLength: 1
    }, {
        name: "allergy",
        source: substringMatcher(allergy_list)
        
    })
});

$('#disease_list_input').tagsinput({
    typeaheadjs: ({
        hint: true,
        highlight: true,
        minLength: 1
    }, {
        name: "disease",
        source: substringMatcher(disease_list)
    })
});

function get_no_of_waiting(doctor_id) {
    check_if_doctor_is_available_today(doctor_id);

    var busy = document.getElementById("waitingBusy");
    var waitingNo = document.getElementById("waitingNo");

    waitingNo.innerHTML = "";
    waitingNo.style.display = "none";

    if (doctor_id == '') {
        busy.style.display = "none";
        return false;
    }

    busy.style.display = "inline-block";

    $.ajax({
        method: 'post',
        url: "<?php echo base_url(); ?>hospital/ajax/getwaitingpatients",
        data: {
            "doctor_id": doctor_id
        },
        success: function(result) {
            if (result != null) {
                if (!isNaN(result))
                    waitingNo.innerHTML = result + " waiting";
                else {
                    waitingNo.innerHTML = "Error";
                    window.location.reload();
                }
                waitingNo.style.display = "inline-block";
                busy.style.display = "none";
            }
        }
    });

}

function check_if_doctor_is_available_today(doctor_id) {
    //checkifdoctorisavailable

    $.ajax({
        method: 'post',
        url: "<?php echo base_url(); ?>hospital/ajax/checkifdoctorisavailable",
        data: {
            "doctor_id": doctor_id,
            "date": "<?= date("Y-m-d") ?>"
        },
        success: function(result) {
            try {
                let r = JSON.parse(result);
                let html_txt = " Today this doctor is available from " + r.start_time + " to " + r.end_time;
                dental_hide_flash('doctor_schedule_flash', html_txt, false);
            } catch (e) {
                dental_show_flash('doctor_schedule_flash', 'This doctor is not available today.');
            }
        }
    });
}


function loadSchedule() {
    var doctor_id = $('#doctor_id').val();
    var date = $('#date').val();
    if (doctor_id != '') {
        $.ajax({
            'url': "<?php echo base_url(); ?>hospital/ajax/getschedule",
            'type': 'POST',
            'data': {
                'doctor_id': doctor_id,
                'date': date
            },
            'success': function(data) {
                var container = $(".schedule");
                container.html(data);
                //console.log(data);
            }
        });
    };
}

function myBooking(data) {
    var id = $("#t_" + data).text();
    document.getElementById("msg_c").innerHTML =
        "<div style=' color:green; font-size:20px;'><?php echo 'Select Time'?> " + id + "</div>";
    document.getElementById('serial_no').value = id;
}

function validatetime() {
    if (document.getElementById('serial_no').value == '') {
        alert("Please Select valid Time.");
        return false;
    }
}

function check_active_appointment() {

    var patient_id = $("#patient_id").val();
    $.ajax({
        method: 'post',
        url: "<?php echo base_url(); ?>hospital/ajax/patientactiveappointment",
        data: {
            "patient_id": patient_id
        },
        success: function(result) {
            if (result != null) {
                dental_show_flash('active_appointment_alert', result);
                // dental_show_alert(result);
            }
        }
    });
}



function printContent(el) {
    var restorepage = $('body').html();
    var printcontent = $('#' + el).clone();
    $('body').empty().html(printcontent);
    window.print();
    $('body').html(restorepage);
    location.reload();
}

//document.onready(check_active_appointment());
$(document).ready(function(){
    check_active_appointment();
    });

    function add_to_order(obj, idPrefix, id){
                    let title = $('#'+idPrefix+'_'+id+'_title').tagsinput('items');
                    let quantity = $('#'+idPrefix+'_'+id+'_quantity').val();
                    let description = $('#'+idPrefix+'_'+id+'_description').val();

                    $("#order_option_input_1").tagsinput('removeAll');
                    $("#order_option_input_1").tagsinput("add", title[0]);
                    $("#order_option_quantity_1").val(quantity);
                    if(add_order_option() == 1){
                        obj.children[0].style.color = "#ccc";
                        //document.getElementById('').children;
                        obj.removeAttribute("onclick");
                        save_order_option();
                    }
                }

                function set_follow_up_data(key, value){
                    follow_up_data[key] = value;
                    save_encounter(null,true);
                }

                var order_option = {option_1:{}};

                <?php
                    $options_json = '{option_1:{';
                    foreach($orderOption as $service_index => $option_service){
                        $index = $option_service['service_id'];
                        if(intval($index) < 0)
                            $index = "_" . (-1 * intval($index));
                        $options_json .= "service_".$index;
                        $options_json .= ':{service_id:"'.$option_service['service_id'].'", service_name: "'.$option_service['service_name'].'", quantity: "'.$option_service['quantity'].'"}, ';
                    }
                    
                    $options_json .= '}}';

                    echo '
                        order_option = '.$options_json.';';
                ?>

                function add_order_option(){
                    if(selected_service_index['order'] == -1)
                        return 0;
                

                    let option_id = 1;
                    let service_index = selected_service_index['order'];
                    let service_name = services[service_index].service_name;
                    let service_id = services[service_index].id;
                    
                    let option_key = "option_" + option_id;
                    let quantity_val = $("#order_option_quantity_" + option_id).val();

                    if((parseFloat(quantity_val.trim()) <= 0) || (isNaN(parseFloat(quantity_val.trim())))){
                        let order_option_quantity= $("#order_option_quantity_" + option_id);
                        order_option_quantity.css('background-color', '#f00');
                        order_option_quantity.focus();
                        setTimeout("$('#order_option_quantity_1').css('background-color', '#fff')",500);
                        return false;
                    }
                       

                    let new_service = {service_id: parseInt(service_id), service_name: service_name, quantity: parseFloat(quantity_val)};

                    if(order_option[option_key] === undefined)
                        order_option[option_key] = {};

                    service_key = 'service_' + service_id;
                    if(service_id < 0)
                        service_key = 'service__' + (-1*service_id);

                    order_option[option_key][service_key] = new_service;

                   
                    let order_option_service_list = $('#order_option_service_list_' + option_id);

                    order_option_service_list.html(create_order_list_html(option_id));

                    $("#order_input").val('');
                    $("#order_option_quantity_" + option_id).val(1);
                    $("#order_input").focus();
                    selected_service_index['order'] = -1;
                    
                    return 1;
                }

                function create_order_list_html(option_id){
                    let option_key = "option_" + option_id;
                    let new_service_option_row = '';

                    $.each(order_option[option_key], function(index, value) {
                        let quantity_val = value['quantity'];
                        let service_id = value['service_id'];
                        service_key = 'service_' + service_id;
                        if(service_id < 0)
                            service_key = 'service__' + (-1*service_id);
                    
                        
                        new_service_option_row += '<tr id="order_option_service_' + option_id + '_' + service_id + '">'
                                                    +'<td>' + value['service_name'] + '</td>'
                                                    +'<td>'
                                                    +'  <div class="input-group">'
                                                    +'      <span onclick="decrement_order_quantity(this, \''+option_key+'\', \''+service_key+'\')" class="input-group-addon" role="button" style="padding:6px 5px !important;"><a class="fa fa-minus"></a></span>'
                                                    +'      <input type="text" onchange="update_order_quantity(this, \''+option_key+'\', \''+service_key+'\')" class="form-control text-center quantity" value="' + quantity_val + '" style="padding:6px 4px !important;">'
                                                    +'      <span onclick="increment_order_quantity(this, \''+option_key+'\', \''+service_key+'\')" class="input-group-addon" role="button" style="padding:6px 5px !important;"><a class="fa fa-plus"></a></span>'
                                                    +'  </div>'
                                                    +'</td>'
                                                    +'<td><button class="btn btn-danger" onclick="remove_service_from_order_option(' + option_id + ', ' + service_id + ');" type="button"><i class="fa fa-trash" ></i></button></td>'
                                                    +'</tr>';
                    });

                    return new_service_option_row;
                }

                function increment_order_quantity(obj, option_key, service_key){
                    order_option[option_key][service_key]['quantity']++;
                    obj.parentElement.children[1].value = order_option[option_key][service_key]['quantity'];
                }

                function decrement_order_quantity(obj, option_key, service_key){
                    order_option[option_key][service_key]['quantity']--;
                    obj.parentElement.children[1].value = order_option[option_key][service_key]['quantity'];
                }

                function update_order_quantity(obj, option_key, service_key){
                    order_option[option_key][service_key]['quantity'] = parseInt(obj.parentElement.children[1].value);
                    console.log(obj.parentElement.children[1].value);
                }

                function remove_service_from_order_option(option_id, service_id){
                    let option_key = "option_" + option_id;
                    let service_key = "service_" + service_id;
                    if(service_id < 0)
                        service_key = "service__" + (-1 * service_id);
                    delete order_option[option_key][service_key];
                    $('#order_option_service_list_' + option_id).children('#order_option_service_' + option_id + '_' + service_id).remove();
                    
                }

                var order_option_list_html = "";
                function build_order_option_list_html(index, item){
                    let option = {option_1:'', option_2:'', option_3:''};
                    let tag = '';
                    if(!jQuery.isEmptyObject(item)){
                        jQuery.each(item, function(i, val){
                            tag = '<span class="dental-tagsinput-tag label dental-tags-lable">'+val.service_name+'&nbsp;&nbsp;&nbsp;&nbsp;<i>'+val.quantity+'</i></span>';
                            option[index] += tag;
                        });
                    }

                    jQuery.each(option, function(i, val){
                        if(val !== ''){
                            if(i == 'option_1')
                                order_option_list_html += '<div class="dental-treatment-plan-list option_1">' + option['option_1'] + '</div>';
                            else if(i == 'option_2')
                                order_option_list_html += '<div class="dental-treatment-plan-list option_2">' + option['option_2'] + '</div>';
                            else
                                order_option_list_html += '<div class="dental-treatment-plan-list option_3">' + option[i] + '</div>';
                        }
                    });
                }

                function display_order_option(){
                    order_option_list_html = '';
                        jQuery.each(order_option, function(i, val) {
                                build_order_option_list_html(i,val);
                        });
                        $('#order_option_list').html(order_option_list_html);
                }

                $(document).ready(function(){
                    display_order_option();
                });

                function save_order_option(){
                    if(!validate_order_save())
                        return false;
                    
                    //prepare_order_extra_info();
                    let post_data = {
                        visit_id:<?= $visit_id ?>,
                        patient_id:<?= $patient['id']?>,
                        patient_name: "<?= $visit['patient_name']?>", 
                        mobile: "<?= $patient['mobile'] ?>", 
                        company_id: "<?= $patient['company_id'] ?>", 
                        company_name: "<?= $patient['company_name'] ?>", 
                        order_option: order_option, 
                        //order_extra_info: order_extra_info
                    };

                    $.ajax({
                        method: 'post',
                        url: "<?php echo base_url(); ?>hospital/ajax/saveorderoptions",
                        data: post_data,
                        //data: {visit_id:<?= $visit_id ?>, patient_id:<?= $patient['id']?>, patient_name: "<?= $visit['patient_name']?>", mobile: "<?= $patient['mobile'] ?>", company_name: "<?= $patient['company_name'] ?>", order_option: order_option, order_extra_info: order_extra_info},
                        success: function(result){
                            try{
                                let r = JSON.parse(result);
                                if(r.status !== "success")
                                    dental_show_flash('save_order_option_flash', result);
                                else{
                                    display_order_option();
                                    toastr['success']("Order Saved");
                                    close_modal('orderOptionModal');
                                }
                            }
                            catch (e){
                                error_html = "Invalid JSON returned.<br><u>Result</u>:<br>" + result + "<hr>Exception:<br>"+e;
                                dental_show_flash('save_order_option_flash', error_html);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            error_html = "Update is not made. <br><u>Error</u>:<br>" + errorThrown;
                            dental_show_flash('save_order_option_flash', error_html, false);
                        }
                    });
                }

                function validate_order_save(){
                    let order_input = $('#order_input');
                    
                    if(order_input.val().trim() != ''){
                        toastr['error']('Clear service selector before saving.');
                        order_input.css('background-color', '#f00');
                        order_input.focus();
                        setTimeout("$('#order_input').css('background-color', '#fff')",500);
                        return false;
                    }
                    return true;
                }
                
                function remove_vital(vital_id){
                    if(confirm("Are you Sure!")){
                    $.ajax({
                            'url': "<?php echo base_url(); ?>hospital/nurse/remove_vital",
                            'type': 'POST',
                            'data': {
                                'vital_id': vital_id
                            },
                            'success': function(data) {
                                location = '/hospital/nurse/vitalsign?id='+<?=@$_GET['id']?>;
                            }
                        });
                    };
                }

                function show_edit_patient(){
                    $("#edit_patient").modal('show');
                }
                function show_schedule_appointment(){
                    $("#schedule_appointment").modal('show');
                }


                var CurrentYear = new Date().getFullYear();
                var CurrentMonth = new Date().getMonth();
                //var mm = 0
                var CurrentDate = new Date().getDate();
                const zeroPad = (num, places) => String(num).padStart(places, '0');
                
                function getdob(val) {
                    year = Math.floor(val);
                    month = parseInt(val.toString().split('.')[1], 10);
                    
                    month = CurrentMonth - month;
                    if(month < 1){
                        month = 12 - (-1*month);
                        year++;
                    }
                   // mm =month;
                    

                    if (isNaN(month) || month>12)
                        month = CurrentMonth;

                    if(month < 10)
                        month = "0" + month;

                    dob = CurrentYear-year +'-'+ month +'-05';
                    
                    document.getElementById('dob').value = dob;
                }
                
                function getAge(val) {
                    
                    document.getElementById('age').value = CurrentYear - val.slice(0, 4);
                }
                

</script>

<script>
        function preview() {
        frame.src = URL.createObjectURL(event.target.files[0]);
    }
    function clearImage() {
        document.getElementById('formFile').value = null;
        document.getElementById('comment').value = "";
        frame.src = "";
    }

    function delete_image_via_lightbox(plugin){
        let image_name = $(plugin.current).data('image_name');
        if(confirm('Are you sure you want to delete this picture?')){
            $.ajax({
                url: '/hospital/nurse/delete_manual_card',
                method: 'POST',
                data: {image_name:image_name, patient_id:<?= $patient['id'] ?>},
                async: false,
                success:function(result){
                    plugin.current.remove();
                    plugin.items.splice(plugin.getCurrentIndex(),1);
                    plugin.close();
                    location.reload();
                }
            });
        }
    }

    var delete_button_lightbox = {
        "background-image" : "/img/delete_button_01.png",
        callback: delete_image_via_lightbox
    };
  
    $(function() {
        $('.manual_card a').lightbox({custom_button1:delete_button_lightbox}); 
    });
    
    </script>

<?php
                // Include Add new modal with its Ajax js
                echo view('Include/_add_new_general');
            ?>
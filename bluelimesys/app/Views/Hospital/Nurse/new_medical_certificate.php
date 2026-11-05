<?php
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
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $loggeduserpin = $loggeduserdata['pin_code'];
    $loggedusersign = $loggeduserdata['digital_signature'];
    $formated_mrn_no = "MRN-".sprintf('%05d', $_GET['pid']);
    $loggeduserdata = $this->data['loggeduserdata'];
    $patient = $this->data['patient'];
    $medical_certificate = $this->data['medical_certificate'];
    //BluelimeSystem::show_and_die();
    /*$diagnosis_from_encounter = $this->data['diagnosis'];
    $treatment_from_encounter = $this->data['treatment'];
    $procedure_from_encounter = $this->data['procedure'];*/
    
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $fname = $patient['first_name'];
    $mname = $patient['middle_name'];
    $lname = $patient['last_name'];
    $mobile = $patient['mobile'];
    $visit_id = $this->data['visit_idx'];
    $reference = $this->data['reference'];
    $patient_id = $patient['id'];
?>
<link rel="stylesheet" href="/Image-gallery/assets/css/jquery.lightbox.css">
<link rel="stylesheet" href="/Image-gallery/assets/css/demo.css">
<script src="/Image-gallery/assets/js/jquery.lightbox.js"></script>
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1>Nurse</h1>
                <small>Medical Certificate</small>
                            </div>
    </section>


    <!-- Main content -->
    <section class="content">
        

	    <div class="row">
            <div class="col-sm-6">
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

       
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Prepare Certificate</h4>
                        </div>
                    </div>
                    
                    <div class="panel-body">
                    <div class="col-md-12">
                        
                        <?php
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','role'=>'form', 'id' => 'medical_certificate_form');
                            echo form_open('hospital/nurse/addmedicalcertificate', $attributes);
                        ?>                                
                        
                                        <div style="float: left;">
                                            <table class="table table-bordered">
                                                <label for="mrntitle" class="control-label">Card N<u>o</u>:</label> <?=@$patient['id']?><br>
                                                <label class="control-label">Full Name:</label> <?=@$fname.' '. @$mname.' '. @$lname?><br>
                                                <label class="control-label"><?php echo "Mobile"?> :</label> <?=$mobile?>
                                                
                                                <input id="mrntitle" type="hidden" name="mrn_no" class="form-control" value="<?php echo $formated_mrn_no; ?>" readonly> 
                                                <input type="hidden" hidden id="patient_id" name="patient_id" value="<?php echo @$patient['id']?>">
                                                <input type="hidden"  name="visit_id" value="<?=$visit_id ?? @$_GET['svid'] ?>">
                                                <input type="hidden" required  name="first_name"   class="form-control" placeholder="Name" value="<?php echo @$fname ?? ""  ?>">
                                                <input type="hidden" required name="middle_name"  class="form-control" placeholder="Father Name" value="<?php echo @$mname ?? ""  ?>">                                        
                                                <input type="hidden" name="last_name"  class="form-control" placeholder="Grand Father" value="<?php echo @$lname ?? ""  ?>">
                                                <input type="hidden" placeholder="09xxxxxxxx"  name="mobile" value="<?=$mobile?>" required class="form-control">
                                                <div <?=$patient['company_id']>0?"":"hidden"?>>
                                                    <label class="control-label">Referal n<u>o</u>: </label><input type="text" name="reference" readonly class="form-control">
                                                </div>
                                            </table>
                                        </div>
                                        <div style="float: left;margin-left: 10px;">
                                            <table class="table table-bordered table-condensed table-hover">
                                                <tr><th colspan="2" style="text-align: center;"><b>Visits</b></th></tr>
                                                <?php
                                                    foreach($patient_visits as $pv){
                                                ?>
                                                    <tr style="cursor: pointer;background-color: <?=@$_GET['svid']==$pv['id']?"aquamarine":""?>;" onclick="selectvisit(<?=@$patient['id']?>,<?=@$_GET['vid']?>,<?=$pv['id']?>,<?=date('Y-m-d', strtotime($pv['visit_date']))?>)"><td><?=$pv['doctor_name']?></td><td><?=date('Y-m-d', strtotime($pv['visit_date']))?></td></tr>
                                                <?php
                                                    }
                                                ?>
                                                
                                            </table>
                                        </div>
                                        
                               
                                <div class="form-group col-md-12">
                                    <label class="control-label"><?php echo "Diagnosis"?> :<i class="text-danger">*</i></label>
                                    <div class="">
                                        <textarea  placeholder="Diagnosis"  name="diagnosis"  id="diagnosis" required class="form-control" rows="2"><?php 
                                        foreach($encounterHistory as $encounter){
                                            echo \BluelimeSystem::encounter_history($encounter['diagnosis']);
                                            }
                                            ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="control-label"><?php echo "Treatment/Procedure"?> :<i class="text-danger">*</i></label>
                                    <div class="">
                                        <textarea  placeholder="Treatment Given"  name="treatment_given"  id="treatment_given" required class="form-control" rows="2"><?php 
                                        foreach($encounterHistory as $encounter){
                                            echo \BluelimeSystem::encounter_history($encounter['procedure']);
                                            }
                                            foreach($encounterHistory as $encounter){
                                                echo \BluelimeSystem::encounter_history($encounter['medication']);
                                                }
                                            ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="form-group col-md-12">
                                    <label class="control-label"><?php echo "Doctor's Recommendation"?> :</label>
                                    <div class="" >
                                        <textarea placeholder="Recommendation"  name="recommendation" id="recommendation"  class="form-control" cols="3"></textarea>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="control-label"><?php echo "Rest Recommended"?> :</label>
                                    <div class="">
                                        <input type="number"  placeholder="Days" min="0"  name="rest_days"  id="rest_days"  class="form-control">
                                        
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <div style="float: left;"><label class="control-label"><?php echo "Visit Dates"?> &nbsp; </label></div>
                                    <div style="float: left;"> <input type="date" name="visit_date"  id="visit_date"  class="form-control" value="<?=date('Y-m-d',strtotime($visit['visit_date']))??date('Y-m-d')?>"></div>
                                    <div style="float: left;"><a onclick="addvisitdates()" class='btn btn-primary'><i class ='fa fa-plus'></i></a></div>
                                    
                                    <textarea class="form-control" name="visit_dates" id="visit_dates" style="resize: none;"></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    <div style="float: left;"><label class="control-label"><?php echo "Next Appointment"?> &nbsp; </label></div>
                                    <div style="float: left;"> <input type="date" name="next_app"  id="next_app"  class="form-control"></div>
                                    
                                </div>
                            
                        
                        <div class="form-group row">
                            <div class="col-sm-offset-4 col-sm-4" style="float: right;">
                                <input class="btn btn-success" value="Prepare" type="submit">
                            </div>
                        </div>
                       <?php
                            echo form_close();
                       ?>                                     
                    </div>
                    </div>
                   
                    </div>
            
            </div>

            <div class="col-sm-6">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>View Certificates</h4>
                        </div>
                    </div>
                    
                    <div class="panel-body">
                    <div <?=isset($medical_certificate[0]['id'])?"":"hidden"?>>
                    <a href="/hospital/nurse/deletemedicalcertificate?pid=<?= @$patient['id']?>&mid=<?= @$medical_certificate[0]['id']?>&vid=<?= $visit_id?>" class='btn btn-danger' onclick="return confirm('Are you Sure?')"><i class ='fa fa-trash'></i></a>
                    <a class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-print"></span></a>
                    <a class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-telegram"></span></a>    
                </div>    
                    <?=isset($medical_certificate[0]['id'])?view('Include/_medical_certificate_print'):"" ?>
                    </div>
                    </div>
            </div>
        </div>

        <div class="col-md-12">
        <!-- Laboratory Results -->
            <div class="form-group col-md-6">
                    <div class="panel panel-bd">
                        <div class="dental-form-box">
                            <div class="box-title">Laboratory Results  <a style="cursor:pointer;" id="downloadl" download="<?=$fname.'_'.$mname.'_'.$lname?>_lab_result.png" href="" onclick="download_imgl(this);"><i class="fa fa-download"></i></a></div>
                            <div id="lab_results">
                            
                            
                                <table class="table table-bordered"  style="margin-bottom:0px">
                                    <tr><th width="40%">Test</th><th width="40%">Result</th><th>Ref. Range</th></tr>
                                </table>
                                <?php
                                    foreach($laboratory_results_compiled as $service_result){
                                        ?>
                                    <fieldset class="reset">
                                    <legend class="reset"><span class="badge badge-qty-<?=date('Y-m-d',strtotime($service_result['result'][0]['created_at']))==date('Y-m-d')?"success":"warning"?>"><?=$service_result['result'][0]['created_at']??date('y-m-d')?></span></legend>
                                        <?php
                                        echo '<table class="table table-bordered">';
                                        echo '<tr><th colspan="3">'. $service_result['service_name'] .'</th></tr>';
                                        
                                        foreach($service_result['result'] as $result){
                                            echo '<tr style="color:'.@$result['flag_color'].'"><td width="40%">'.$result['attribute_name'].' '.$result['result_flag'].' </td><td width="40%">'.$result['result'].'</td><td>'.$result['reference_range'].'</td></tr>';
                                        }
                                        echo '</table>';
                                        ?>
                                    </fieldset>
                                    <?php
                                    }
                                ?>
                            
                            </div>
                        </div>
                    </div>
            </div>
            
            <!-- Nurse Procedures -->
            <div class="form-group col-md-6">
                    <div class="panel panel-bd">
                        <div class="dental-form-box">
                            <div class="box-title">Nurse Procedures  <a style="cursor:pointer;" id="downloadn" download="<?=$fname.'_'.$mname.'_'.$lname?>_nurse_procedures.png" href="" onclick="download_imgn(this);"><i class="fa fa-download"></i></a></div>
                        <div class="panel panel-body" id="download_nurse_procedure">
                        <div class="row">
                        <?php
                        if($distinct_procedures){
                            foreach($distinct_procedures as $index=>$history){
                        ?>
                        <div class="col-lg-12" style="font-size: smaller;">
                                <div class="dental-form-box">
                                    <div class="box-title">
                                        <span style="float: left;color:green;"><?=$history??""?></span>
                                    </div>
                                    
                                        <table class="table table-hover table-striped table-bordered" style="cursor: pointer;">
                                            
                                                        <?php
                                                            $cnt = 0;
                                                            foreach($patient_procedure_history as $attr){
                                                        
                                                            if($history == $attr['procedure_name']){
                                                                $cnt++;
                                                        ?>
                                                        <tr>

                                                            <?php
                                                                    if($cnt<2){
                                                                    foreach(BluelimeSystem::convert_to_array(str: $attr['result']) as $rt){
                                                                    echo '<th>'.$rt['attribute_name'].'</th>';
                                                                    }
                                                                    echo '<th>Date</th>';
                                                                    echo '<th>Registered By</th>';
                                                                }
                                                                ?>
                                                        </tr>
                                                        <tr>

                                                            <?php
                                                            
                                                                    foreach(BluelimeSystem::convert_to_array($attr['result']) as $rt){
                                                                    
                                                                    echo '<td>'.$rt['result'].'</td>';
                                                                        
                                                                    }
                                                                    echo '<td>'.$attr['created_at'].'</td>';
                                                                    echo '<td>'.$attr['registered_by_name'].'</td>';
                                                                
                                                                ?>
                                                        </tr> 
                                                
                                                <?php
                                                            }
                                                            }
                                                        ?>
                                            
                                        
                                            
                                                
                                        </table>
                                    
                                </div>
                        </div>
                        <?php
                            }
                        }
                        ?>
                        </div>   
                        </div>
                            </div>
                    </div>
            </div>
        </div>
        <div class="col-md-12">
            <!-- order History -->
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
            
            <!-- Prescription History -->
        <div class="col-md-12">
                        <div class="panel panel-bd">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <h4><a class="" data-toggle="collapse" href="#collapsePrescription" role="button" aria-expanded="false" aria-controls="collapsePrescription">
                                            Prescription History  <a style="cursor:pointer;" id="downloadp" download="<?=$fname.'_'.$mname.'_'.$lname?>_prescription_history.png" href="" onclick="download_imgp(this);"><i class="fa fa-download"></i></a>
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
            <!-- Encounter history -->
        <div class="col-md-12">
            <?=@view('Include/_encounter_history',['history_follow_up_list'=>$encounter_history,'patient_name'=>$fname.'_'.$mname.'_'.$lname])?>
        </div>

    </section>

</div>

<script type="text/javascript">
    
$( document ).ready(function() {
    addvisitdates();
});

function close_modal(id) {
                    $(id).modal('hide');
                }
               
function checkpin(){
    var pin = $("#pin_entered").val();
    var user = $("#user_id").val();
    $.ajax({
        method: 'post',
        url: "<?php echo base_url(); ?>hospital/ajax/checkpin",
        data: {"pin_code":pin,'id':user},
        success: function(data) { 
                if(data == 1){
                    document.getElementsByClassName('show')[0].value = data;
                    document.getElementById("medical_certificate_form").submit();
                }
                else{
                    error_html = "Incorrect Pin";
                    dental_show_flash('pinCheck_flash', error_html, false);
                }

        }
    });
    
    

}


function selectvisit(pid,vid,svid,dt){
    location = '/hospital/nurse/newmedicalcertificate?pid='+pid+'&vid='+vid+'&svid='+svid+'&dt='+dt;
}    
function validateForm(){

var treatment_given  = $("#summernote").val();
var diagnosis  = $("#diagnosis").val();
var recommendation  = $("#recommendation").val();
var rest_days = $("#rest_days	").val();

if(treatment_given ==""  || recommendation =="" || diagnosis =="")
    dental_show_alert("Please fill all required fields");
else    
    $('#confirmPin').modal('show');
return false;

}

function addvisitdates(){
    if($("#visit_dates").val()!="")
        visit_date +=   ", " + $("#visit_date").val();
    else
        visit_date = $("#visit_date").val();

    $("#visit_dates").val(visit_date);

}

</script>


<script>
    function print_printable_document(){
        var printContents = document.getElementById('printable').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
<script src="/js/html2canvas.js"></script>
    <script>
        
        

var getCanvas; // global variable
    $('document').ready(function(){
    html2canvas($("#collapsePrescription")[0]).then((canvas) => {
            $("#collapsePrescription").html(canvas);
            getCanvas = canvas;
        });
    });


download_imgp = function (el) {
  var image = getCanvas.toDataURL("image/png")
  el.href = image
}
var getCanvasn; // global variable
    $('document').ready(function(){
    html2canvas($("#download_nurse_procedure")[0]).then((canvasn) => {
            $("#download_nurse_procedure").html(canvasn);
            getCanvasn = canvasn;
        });
    });


download_imgn = function (el) {
  var imagen = getCanvasn.toDataURL("image/png")
  el.href = imagen
}
var getCanvasl; // global variable
    $('document').ready(function(){
    html2canvas($("#lab_results")[0]).then((canvasl) => {
            $("#lab_results").html(canvasl);
            getCanvasl = canvasl;
        });
    });


download_imgl = function (el) {
  var imagel = getCanvasl.toDataURL("image/png")
  el.href = imagel
}

function preview() {
        frame.src = URL.createObjectURL(event.target.files[0]);
    }
</script>

<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $loggeduserpin = $loggeduserdata['pin_code'];
    $loggedusersign = $loggeduserdata['digital_signature'];
    $formated_mrn_no = "MRN-".sprintf('%05d', $_GET['id']);
    $loggeduserdata = $this->data['loggeduserdata'];
    $patient = $this->data['patient'];
    //BluelimeSystem::show_and_die($patient);
    /*$diagnosis_from_encounter = $this->data['diagnosis'];
    $treatment_from_encounter = $this->data['treatment'];
    $procedure_from_encounter = $this->data['procedure'];*/
    
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $fname = $patient['first_name'];
    $mname = $patient['middle_name'];
    $lname = $patient['last_name'];
    $mobile = $patient['mobile'];
    $visit_id = $this->data['visit_id'];
    $reference = $this->data['reference'];
    $patient_id = $patient['id'];

    
    
?>
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1>Doctor</h1>
                <small>Medical Report</small>
                <ol class="breadcrumb">
                    <li class="active"><a href="visit?visit_id=<?=$visit_id?>">Visit Page</a></li>
                </ol>
            </div>
    </section>


    <!-- Main content -->
    <section class="content">
        

	    <div class="row">
            <div class="col-sm-12">

       
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Medical Report</h4>
                        </div>
                    </div>
                    
                    <div class="panel-body">
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
                   
                    <div class="col-md-12">
                        
                        <?php
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','role'=>'form', 'id' => 'medical_certificate_form');
                            echo form_open('hospital/doctor/addmedicalreport', $attributes);
                        ?>                                
                        
                                        <table class="table table-bordered">
                                            <label for="mrntitle" class="control-label">MRN N<u>o</u>:</label> <?=@$patient['id']?><br>
                                            <label class="control-label">Full Name:</label> <?=@$fname.' '. @$mname.' '. @$lname?><br>
                                            <label class="control-label">Sex:</label> <?=@$patient['sex']?><br>
                                            <label class="control-label">Age:</label> <?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."Y "?><?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth']) >=10 ?"":BluelimeSystem::month_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."M"?><br>
                                            
                                            <label class="control-label"><?php echo "Mobile"?> :</label> <?=$mobile?>
                                            
                                            <input id="mrntitle" type="hidden" name="mrn_no" class="form-control" value="<?php echo $formated_mrn_no; ?>" readonly> 
                                            <input type="hidden" hidden id="patient_id" name="patient_id" value="<?php echo @$patient['id']?>">
                                            <input type="hidden"  name="sex" value="<?=@$patient['sex']?>">
                                            <input type="hidden"  name="visit_id" value="<?=$visit_id ?>">
                                            <input type="hidden"  name="mid" value="<?=@$_GET['mid'] ?>">
                                            <input type="hidden"  name="age" value="<?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."Y "?><?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth']) >=10 ?"":BluelimeSystem::month_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."M"?>">
                                            <input type="hidden" required  name="first_name"   class="form-control" placeholder="Name" value="<?php echo @$fname ?? ""  ?>">
                                            <input type="hidden" required name="middle_name"  class="form-control" placeholder="Father Name" value="<?php echo @$mname ?? ""  ?>">                                        
                                            <input type="hidden" name="last_name"  class="form-control" placeholder="Grand Father" value="<?php echo @$lname ?? ""  ?>">
                                            <input type="hidden" placeholder="09xxxxxxxx"  name="mobile" value="<?=$mobile?>" required class="form-control">
                                            <div <?=$patient['company_id']>0?"":"hidden"?>>
                                                <label class="control-label">Referal n<u>o</u>: </label><input type="text" name="reference" readonly class="form-control">
                                            </div>
                                        </table>
                               
                                <div class="form-group col-md-9">
                                    <label class="control-label"><?php echo "History"?> :<i class="text-danger">*</i></label>
                                    <div class="">
                                    <textarea type="text" id="summernote"  name="history" style="resize: none;height: 150px;">
                                            <?php 
                                            if(!empty($medical_report)){
                                                foreach($medical_report as $mr){
                                                    echo $mr['history'];
                                                }
                                            }
                                            else{
                                            foreach($encounterHistory as $encounter){
                                                echo '<b>'.date('Y-m-d',strtotime($encounter['registered_date'])).'</b></br>';
                                                echo 'Chief Complaints: ';  
                                                echo \BluelimeSystem::encounter_history($encounter['chief_complaints']).'</br>';
                                                echo 'HPI: ';  
                                                echo \BluelimeSystem::encounter_history($encounter['hpi']).'</br>'; 
                                                echo 'Physical Findings: ';  
                                                echo \BluelimeSystem::encounter_history($encounter['physical_finding']).'</br>';
                                                echo 'Past Medical History: ';  
                                                echo \BluelimeSystem::encounter_history($encounter['past_medical_history']).'</br>';
                                                echo 'Diagnosis: ';  
                                                echo \BluelimeSystem::encounter_history($encounter['diagnosis']).'</br>';
                                                echo 'Medication: ';  
                                                echo \BluelimeSystem::encounter_history($encounter['medication']).'</br>';
                                                echo 'Procedure: ';  
                                                echo \BluelimeSystem::encounter_history($encounter['procedure']).'</br>';
                                                echo 'Post Procedure Diagnosis: ';  
                                                echo \BluelimeSystem::encounter_history($encounter['post_procedure_diagnosis']).'</br>';
                                                echo 'Post Treatment Outcome: ';  
                                                echo \BluelimeSystem::encounter_history($encounter['post_treatment']).'</br></br>';
                                                }
                                            }
                                                ?>
                                        </textarea>
                                    </div>
                                    
                                </div>
                               
                                
                            
                        
                        <div class="form-group row">
                            <div class="col-sm-offset-4 col-sm-4" style="float: right;">
                                <input class="btn btn-success" value="Prepare" type="button" onclick="show_confirm_pin()">
                            </div>
                        </div>
                       <?php
                            echo form_close();
                       ?>                                     
                    </div>
                    </div>
                   
                    </div>
            
            </div>

            

        </div>
        <form onsubmit="event.preventDefault(); checkpin();">
        <div class="modal fade" id="confirmPin" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Enter Pin Code</h4>
                    </div>
                    <div class="modal-body">
                        <input id="pin_entered" placeholder="Enter pin" type="password" maxlength="4" class="form-control focus_input_pin"
                            autocomplete="off" pattern="[0-9]+" autofocus >
                        <input id="user_id" type="hidden" value="<?=$loggeduserdata['id'] ?>">
                        <div id="pinCheck_flash" class="alert alert-danger hide" role="alert">
                            <span></span>
                        </div>
                        <div style="text-align: center;color:black"><b><br>
                            <?php echo '<img style="height:100px;" src="data:image/jpeg;base64,'.base64_encode( $loggedusersign ).'"/>' ?><br>
                            <?=$loggeduserdata['name'] ?><br>
                            <?=$loggeduserdata['title'] ?></b>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-success" value="Confirm" type="button" onclick="checkpin()">
                    </div>
                </div>
            </div>
        </div>
        </form>


        <div class="col-md-12">
        <!-- Laboratory Results -->
            <div class="form-group col-md-6">
                    <div class="panel panel-bd">
                        <div class="dental-form-box">
                            <div class="box-title">Laboratory Results</div>
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
                            <div class="box-title">Nurse Procedures</div>
                        <div class="panel panel-body">
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
            <!-- Encounter history -->
        <div class="col-md-12">
            <?=@view('Include/_encounter_history',['history_follow_up_list'=>$encounter_history])?>
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
               

function validateForm(){

var summernote  = $("#summernote").val();

    if(summernote ==""){
        summernote.css("borderColor","red"); 
        summernote.focus();
        return false;
    }
    return true;

}

function addvisitdates(){
    if($("#visit_dates").val()!="")
        visit_date +=   ", " + $("#visit_date").val();
    else
        visit_date = $("#visit_date").val();

    $("#visit_dates").val(visit_date);

}

function show_confirm_pin(){
    if(!validateForm()){
                            toastr['error']('Please Enter all required Fields!');
                            return false;
                        }
        
        $('#confirmPin').modal('show');
        $('#confirmPin').on('shown.bs.modal', function () {
        $('#pin_entered').focus();
})
        $('#pin_entered').focus();
    }

    function checkpin(){
        var pin = $("#pin_entered").val();
        var user = $("#user_id").val();
        $.ajax({
            method: 'post',
            url: "/hospital/ajax/checkpin",
            data: {"pin_code":pin,'id':user},
            dataType: 'json',
            async: false,
            success: function(result) { 
                if(result.status == 'success'){
                    document.getElementById("medical_certificate_form").submit();
                }
                else
                    toastr['error'](result.message);
            },
            error:function(jqXHR,status,error){
                    toastr['error'](error);
            }
        });
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

<script>
    $(document).ready(function() {
        $('#summernote').summernote({height: 700,
        focus: true});
    });
  </script>






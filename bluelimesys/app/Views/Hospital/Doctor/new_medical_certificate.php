<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $loggeduserpin = $loggeduserdata['pin_code'];
    $loggedusersign = $loggeduserdata['digital_signature'];
    $formated_mrn_no = "MRN-".sprintf('%05d', $_GET['id']);
    $loggeduserdata = $this->data['loggeduserdata'];
    $patient = $this->data['patient'];
    $medical_certificate = $this->data['medical_certificate'];
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
                <small>Medical Certificate</small>
                <ol class="breadcrumb">
                    <li class="active"><a href="visit?visit_id=<?=$visit_id?>">Visit Page</a></li>
                </ol>
            </div>
    </section>


    <!-- Main content -->
    <section class="content">
        

	    <div class="row">
            <div class="col-sm-6">

       
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
                            echo form_open('hospital/doctor/addmedicalcertificate', $attributes);
                        ?>                                
                        
                                        <table class="table table-bordered">
                                            <label for="mrntitle" class="control-label">Card N<u>o</u>:</label> <?=@$patient['id']?><br>
                                            <label class="control-label">Full Name:</label> <?=@$fname.' '. @$mname.' '. @$lname?><br>
                                            <label class="control-label"><?php echo "Mobile"?> :</label> <?=$mobile?>
                                            
                                            <input id="mrntitle" type="hidden" name="mrn_no" class="form-control" value="<?php echo $formated_mrn_no; ?>" readonly> 
                                            <input type="hidden" hidden id="patient_id" name="patient_id" value="<?php echo @$patient['id']?>">
                                            <input type="hidden"  name="visit_id" value="<?=$visit_id ?>">
                                            <input type="hidden" required  name="first_name"   class="form-control" placeholder="Name" value="<?php echo @$fname ?? ""  ?>">
                                            <input type="hidden" required name="middle_name"  class="form-control" placeholder="Father Name" value="<?php echo @$mname ?? ""  ?>">                                        
                                            <input type="hidden" name="last_name"  class="form-control" placeholder="Grand Father" value="<?php echo @$lname ?? ""  ?>">
                                            <input type="hidden" placeholder="09xxxxxxxx"  name="mobile" value="<?=$mobile?>" required class="form-control">
                                            <div <?=$patient['company_id']>0?"":"hidden"?>>
                                                <label class="control-label">Referal n<u>o</u>: </label><input type="text" name="reference" readonly class="form-control">
                                            </div>
                                        </table>
                               
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
                                    <div style="float: left;"> <input type="date" name="visit_date"  id="visit_date"  class="form-control" value="<?=date('Y-m-d')?>"></div>
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
                    <a href="/hospital/doctor/deletemedicalcertificate?pid=<?= @$patient['id']?>&mid=<?= @$medical_certificate[0]['id']?>&vid=<?= $visit_id?>" class='btn btn-danger' onclick="return confirm('Are you Sure?')"><i class ='fa fa-trash'></i></a>
                    <a class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-print"></span></a>
                    <a class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-telegram"></span></a>    
                </div>    
                    <?=isset($medical_certificate[0]['id'])?view('Include/_medical_certificate_print'):"" ?>
                    </>
                    </div>
                </div>
            </div>

        </div>

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






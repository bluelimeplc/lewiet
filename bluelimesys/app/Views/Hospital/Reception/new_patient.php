<?php
    $formated_mrn_no = "MRN-".sprintf('%05d', $this->data['mrn_no']);
    $loggeduserdata = $this->data['loggeduserdata'];
    $patient = $this->data['patient'];
    $companies = $this->data['companies'];
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $app_name= explode(" ", @$_GET['name']);
    $patient_fname=(isset($patient['first_name']))?$patient['first_name']:@$app_name[0];
    $patient_mname=(isset($patient['middle_name']))?$patient['middle_name']:@$app_name[1];
    $patient_lname=(isset($patient['last_name']))?$patient['last_name']:@$app_name[2];
    $mobile= (isset($patient['mobile']))?$patient['mobile']:@$_GET['mob'];
?>

    
    <script type = "text/javascript">
        
    </script>
    
            <!-- =============================================== -->
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">

                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="header-icon">
                        <i class="pe-7s-world"></i>
                    </div>
                    <div class="header-title">
                        <h1>Reception</h1>
                        <small>New Patient</small>
                        <ol class="breadcrumb">
                            <li class="active"><a href="/hospital/reception/searchpatient">Search</a></li>
                        </ol>
                    </div>
                </section>

                
                <!-- Main content -->
                <section class="content">
                    <?php
                        $attributes = array('role'=>'form', 'method'=>'post','onsubmit'=>'return validatemobile()');
                        helper('form');
                        echo form_open_multipart('/hospital/reception/addpatient', $attributes);
                    ?>
                        <div class="row">
                            <!-- Patient Detail -->
                            <div class="col-sm-6 col-md-6">
                                <div class="panel panel-bd">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <h4>Patient Detail</h4>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="mrntitle" class="control-label">MRN N<u>o</u></label>
                                                    <div class="custom-button-inside-input">
                                                        <input id="mrntitle" type="text" name="mrn_no" class="form-control custom-input" value="<?= $formated_mrn_no; ?>" readonly>
                                                        <?=$page_type=='edit'?'<!-- <button id="discard_button" class="btn btn-danger custom-botton" type="button" onclick="confirmdiscard()">DISCARD</button> -->':""?> 
                                                        
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
                                                        
                                                        <input  role="presentation" autocomplete="off"  onload="check_if_mobile_exists(),check_if_patient_exists()" onchange="check_if_mobile_exists(),check_if_patient_exists()"  type="text" name="first_name" id="first_name" class="form-control" placeholder="Name" value="<?php echo $patient_fname ?? ""  ?>" required>
                                                        <span class="input-group-addon"></span>
                                                        <input  role="presentation" autocomplete="off"  type="text" name="middle_name" class="form-control" placeholder="Father Name" value="<?php echo $patient_mname ?? ""  ?>" required>
                                                        <span class="input-group-addon"></span>
                                                        <input  role="presentation" autocomplete="off"  type="text" name="last_name" class="form-control" placeholder="Grand Father" value="<?php echo $patient_lname ?? ""  ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group col-l-12">
                                                <div class="col-md-2">
                                                    <label class="control-label">Age <i style="color:red">*</i></label>
                                                        <?php
                                                        $patient_dob = isset($patient['date_of_birth']) ? $patient['date_of_birth']:'';
                                                        $diff = date_diff(date_create($patient_dob), date_create(date("Y-m-d")));
                                                        $age = $diff->format('%y');
                                                        ?>
                                                        <input class="form-control"  name="old" id="age" min="0" step="0.01" max="100" type="number"  required placeholder="Age"  onload="getdob(this.value)" oninput="getdob(this.value) " value="<?php echo $age ?? ""   ?>">
                                                    </div>
                                                    <div class="col-md-5"style="padding: left 2px;">
                                                    <label class="control-label">Date of Birth</label>
                                                        <input tabindex="-1" type="date" id="dob" name="date_of_birth" style="" class="form-control" placeholder="Select Date"  onload="getAge(this.value)" onchange="getAge(this.value)"  value="<?php echo $patient['date_of_birth'] ?? ""  ?>">
                                                    </div>
                                                    
                                                    <div class="col-md-5">
                                                        <label class="control-label">Sex <i style="color:red">*</i></label>
                                                    
                                                        <select name="sex" id="sex" class="form-control" required>
                                                            <option  value="">--Select Sex--</option>
                                                            <option value="Female" <?php echo (@$patient['sex']=='Female')?'selected':'' ?>>Female</option>
                                                            <option value="Male" <?php echo (@$patient['sex']=='Male')?'selected':'' ?>>Male</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                
                                                <div class="form-group col-md-12">
                                                    <label class="control-label">Address <i style="color:red">*</i></label>
                                                    <div>
                                                        <input required  role="presentation" autocomplete="off" type="text" placeholder="Eg. Addis Ababa" name="address" id="address" class="form-control" value="<?php echo $patient['address'] ?? ""  ?>">
                                                        
                                                    </div>
                                                </div>

                                                <div class="form-group col-l-12">
                                                        <div class="col-xs-6">
                                                            <label class="control-label">Sub City</label>
                                                            <input  role="presentation"  autocomplete="off" type="text" name="subcity" id="subcity" class="form-control" value="<?php echo $patient['subcity'] ?? ""  ?>">
                                                            
                                                        </div>
                                                        <div class="col-xs-3">
                                                            <label class="control-label">Woreda</label>
                                                            <input  role="presentation"  autocomplete="off" type="text" name="woreda" class="form-control" value="<?php echo $patient['woreda'] ?? ""  ?>">
                                                        </div>
                                                        <div class="col-xs-3">
                                                        
                                                            <label class="control-label">House N<u>o</u></label>
                                                            <input  role="presentation"  autocomplete="off" type="text" name="house_no" class="form-control" value="<?php echo $patient['house_no'] ?? ""  ?>">
                                                        
                                                        </div>
                                                </div>

                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Medical History -->
                            <!--div class="col-sm-6 col-md-6">
                                <div class="panel panel-bd">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <h4>Medical History</h4>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-striped">
                                            <tr>
                                                <td>&nbsp; </td>
                                                <td><i class="glyphicon  glyphicon-ok color-red" title="Yes"></i></td>
                                                <td><i class="glyphicon  glyphicon-remove color-gray" title="No"></i></td>
                                            </tr>
                                            <tr> 
                                                <td>Blood pressure</td>
                                                <td><input  type="radio" name="blood_pressure" value="Yes" <?php echo (@$patient['blood_pressure']=='Yes')?'checked':'' ?>/></td>
                                                <td><input  type="radio" name="blood_pressure" value="No" <?php echo (@$patient['blood_pressure']=='No')?'checked':'' ?>/></td>
                                            </tr>
                                            <tr>
                                                <td>Bleeding Tendency</td>
                                                <td><input  type="radio" name="bleeding_tendency" value="Yes" <?php echo (@$patient['bleeding_tendency']=='Yes')?'checked':'' ?>/></td>
                                                <td><input  type="radio" name="bleeding_tendency" value="No"  <?php echo (@$patient['bleeding_tendency']=='No')?'checked':'' ?>/></td>
                                            </tr>
                                            <tr>
                                                <td>Pregnancy</td>
                                                <td><input  onchange="return disablepregnancy()" type="radio" name="pregnancy" id="pregnant" value="Yes" <?php echo (@$patient['pregnancy']=='Yes')?'checked':'' ?>/></td>
                                                <td><input  type="radio" name="pregnancy" id="not_pregnant" value="No"  <?php echo (@$patient['pregnancy']=='No')?'checked':'' ?>/></td>
                                            </tr>
                                            <tr>
                                                <td>Cardiac Problem</td>
                                                <td><input  type="radio" name="cardiac_problem" value="Yes" <?php echo (@$patient['cardiac_problem']=='Yes')?'checked':'' ?>/></td>
                                                <td><input  type="radio" name="cardiac_problem" value="No"  <?php echo (@$patient['cardiac_problem']=='No')?'checked':'' ?>/></td>
                                            </tr>
                                            <tr>
                                                <td>Diabetics</td>
                                                <td><input  type="radio" name="diabetics" value="Yes" <?php echo (@$patient['diabetics']=='Yes')?'checked':'' ?>/></td>
                                                <td><input  type="radio" name="diabetics" value="No"  <?php echo (@$patient['diabetics']=='No')?'checked':'' ?>/></td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align: middle;">Blood Group</td>
                                                <td colspan="2">
                                                    <select class="form-control" name="blood_group">
                                                        <option value=''>--Select Blood Group--</option>
                                                        <option value='A+' <?php echo (@$patient['blood_group']=='A+')?'selected':'' ?>>A+</option>
                                                        <option value='A-'<?php echo (@$patient['blood_group']=='A-')?'selected':'' ?>>A-</option>
                                                        <option value='B+'<?php echo (@$patient['blood_group']=='B+')?'selected':'' ?>>B+</option>
                                                        <option value='B-'<?php echo (@$patient['blood_group']=='B-')?'selected':'' ?>>B-</option>
                                                        <option value='O+'<?php echo (@$patient['blood_group']=='O+')?'selected':'' ?>>O+</option>
                                                        <option value='O-'<?php echo (@$patient['blood_group']=='O-')?'selected':'' ?>>O-</option>
                                                        <option value='AB+'<?php echo (@$patient['blood_group']=='AB+')?'selected':'' ?>>AB+</option>
                                                        <option value='AB-'<?php echo (@$patient['blood_group']=='AB-')?'selected':'' ?>>AB-</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align: middle;">
                                                    Drugs Taking
                                                    <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new drug" onclick="show_add_new_modal('Add New Drug', 'drug', drug_list)" ></i>
                                                </td>
                                                <td colspan="2">
                                                    <select name="drugs_taking[]" multiple data-role="tagsinput" class="form-control tagsinputs" id="drug_list_input">
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
                                                    <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new allergy" onclick="show_add_new_modal('Add New Allergy', 'allergy', allergy_list)" ></i>
                                                </td>
                                                <td colspan="2">
                                                    <select name="allergy[]" multiple data-role="tagsinput" class="form-control tagsinputs" id="allergy_list_input">
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
                                                    <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new disease" onclick="show_add_new_modal('Add New Disease', 'disease', disease_list)" ></i>
                                                </td>
                                                <td colspan="2">
                                                    <select name="other_diseases[]" multiple data-role="tagsinput" class="form-control tagsinputs" id="disease_list_input">
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
                                    </div>
                                </div>
                            </div-->
                            <!-- Emergency Contact -->
                            <div class="col-sm-6 col-md-6">
                                <div class="panel panel-bd">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <h4>Emergency Contact</h4>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-12">
                                            <label class="control-label">Full Name</label>
                                            <div class="">
                                                <input  role="presentation" autocomplete="off"  type="text" name="emergency_contact_name" class="form-control"  value="<?php echo $patient['emergency_contact_name'] ?? ""  ?>">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="control-label">Mobile</label>
                                            <div class="">
                                                <input  role="presentation" autocomplete="off"  type="text" name="emergency_mobile" class="form-control"  value="<?php echo $patient['emergency_mobile'] ?? ""  ?>">
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            <!-- Credit Company -->
                            <div class="col-sm-6 col-md-6">
                                <div class="panel panel-bd">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <h4>Credit Company</h4>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-12">
                                            <label class="control-label">Company</label>
                                            <div class="">
                                                <select name="company_id" class="form-control">
                                                
                                                <option value="0">--Self Sponsor--</option>
                                                <option value="<?php echo @$patient['company_id']  ?>" selected><?php echo @$patient['company_name'] ?></option>
                                                <?php
                                                    foreach($companies as $company){
                                                    
                                                ?>
                                                        <option value="<?php echo $company['id'] ?>" ><?php echo $company['company_name'] ?></option>
                                                        
                                                <?php
                                                
                                                    }
                                                ?>
                                                
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
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
                                                <option value="Other Hospital" <?=(@$patient['survey'] == "Other Hospital")?"selected":""  ?>><?php echo "Other Hospital" ?></option>
                                                <option value="Word-of-Mouth Support/Referral" <?=(@$patient['survey'] == "Word-of-Mouth Support/Referral")?"selected":""  ?>><?php echo "Word-of-Mouth Support/Referral" ?></option>
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

                            <div class="col-sm-12 col-md-12">
                                <div class="panel panel-bd">
                                    <div class="panel-body" id="patient_save">
                                        <?php 
                                        if($page_type=='edit'){
                                            $btn = "Update";
                                        }
                                        else
                                        $btn = "Register";
                                        ?>
                                        <button type="submit" class="btn btn-success"><?php echo $btn ?></button>
                                        <!--button type="button" onclick="showwaitscreen()" class="btn btn-warning" style="float: right;">Save & Wait</button-->                                        
                                    </div>
                                
                                </div>
                            </div>
                            
                        </div>
                        <div class="modal fade" id="wait_screen" tabindex="-1"
                                    role="dialog" aria-labelledby="wait_screen" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel"><b>Add to Waiting List</b></h5>
                                            </div>
                                            
                                            <div>
                                            <img src="/img/loading-loading-forever.gif" width="20px;" style="display: none;" id="waitingBusy">
                                            <span class="label label-warning color-violet" id="waitingNo" style="display: none;">200 waiting</span>
                                        </div>
                                            <div style="margin: 20px;">
                                            <label for="assigned_doctor" class="control-label"><b>Select Doctor</b></label>    
                                            <select id="assigned_doctor" name="assigned_doctor" class="form-control"  onchange="get_no_of_waiting(this.value)">
                                            <option value="-1"></option>
                                                <?php
                                                    foreach($doctors as $doctor):
                                                ?>
                                                    <option value="<?= $doctor['id'] ?>"><?= $doctor['name'] ?></option>';
                                                    <?php
                                                    endforeach
                                                ?>
                                        </select>
                                            
                                            </div>
                                            


                                            <div class="modal-footer">
                                                <input type="button" class="btn btn-secondary" data-dismiss="modal" value="Close">
                                                <input type="submit" onclick="return validatedoctor()" class="btn btn-primary" value="Wait" name="wait_button">

                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                    <?php
                        echo form_close();
                    ?>
                    
                </div>
                </section> <!-- /.content -->
            </div> <!-- /.content-wrapper -->
            <div class="modal fade" id="confirmdiscard" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Enter OLD Card Number</h4>
                    </div>
                    <div class="modal-body">
                        
                        <input id="entered_card" type="number" class="form-control"
                            autocomplete="off" pattern="[0-9]+">
                        <input id="old_card" type="hidden" value="<?=@$_GET['id'] ?>">
                        <div>
                            <h3>New Card No. <b style="color:brown"><?=@$new_mrn?></b></h3>
                        </div>
                        <div id="pinCheck_flash" class="alert alert-danger hide" role="alert">
                            <span></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-danger" value="Confirm" type="button" onclick="checkoldcard()">

                    </div>
                </div>
            </div>
        </div>
        
            <script>
                $(document).on("keydown", ":input:not(drug_list_input, textarea)", function(event) {
                        return event.key != "Enter";});

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
                    typeaheadjs:({
                        hint: true,
                        highlight: true,
                        minLength: 1
                    }, {
                        name: "drugs",
                        source: substringMatcher(drug_list)
                    })
                });

                $('#allergy_list_input').tagsinput({
                    freeInput: false,
                    typeaheadjs:({
                        hint: true,
                        highlight: true,
                        minLength: 1
                    }, {
                        name: "allergy",
                        source: substringMatcher(allergy_list)
                    })
                });

                $('#disease_list_input').tagsinput({
                    freeInput: false,
                    typeaheadjs:({
                        hint: true,
                        highlight: true,
                        minLength: 1
                    }, {
                        name: "disease",
                        source: substringMatcher(disease_list)
                    })
                });

                function check_if_mobile_exists()
                {
                    var mobile = $("#patient_mobile_input").val();
                    $.ajax({
                        method: 'post',
                        url: "<?php echo base_url(); ?>hospital/ajax/getpatientbymobile",
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
                function check_if_patient_exists()
                {
                    $("#patient_save").css("display","block");
                    $("#first_name").css("borderColor","");
                    var mobile = $("#patient_mobile_input").val();
                    var first_name = $("#first_name").val();
                    $.ajax({
                        method: 'post',
                        url: "<?php echo base_url(); ?>hospital/reception/getexistingpatient",
                        data: {"mobile":mobile,'first_name':first_name},
                        success: function(result){
                            if(result){
                                if($("#page_type").val()!="edit"){
                                    $("#first_name").css("borderColor","red");
                                    $("#patient_save").css("display","none");
                                    $("#first_name").focus();
                                    toastr['error']("Patient Exists with the same name and mobile!");
                                }
                                return result;
                            }
                            
                        }
                    });
                }

                
                function confirmdiscard()
                {
                    ;//$('#confirmdiscard').modal('show');                    
                }
                function checkoldcard(){
                    let entered_card = $("#entered_card").val();
                    let old_card = $("#old_card").val();
                    
                    if(parseFloat(entered_card) == parseFloat(old_card)){
                        discard_patient_card(old_card);
                    }
                    else
                    toastr['error']('Incorrect old card Number');
                           
                }
                function discard_patient_card(old_card){
                    
                    $.ajax({
                        method: 'post',
                        url: "<?php echo base_url(); ?>hospital/reception/discardpatientcard", //this is disabled. The receptionists abuse it to discard patients and create new cards for them. So, this is disabled.
                        data: {"old_card":old_card},
                        success: function(result){
                            $("#mrntitle").val("MRN-"+<?=@$new_mrn??1?>);
                            $("#new_id").val(0);
                            $('#confirmdiscard').modal('hide');
                            $('#discard_button').hide();
                        }
                    });
                           
                }

                
                function disablepregnancy()
                {
                    var sex = document.getElementById("sex").value;
                    
                    if(sex=='Male'){
                        toastr['error']("Patient is Male");
                    document.getElementById("not_pregnant").checked = true;
                    document.getElementById("pregnant").checked = false;
                    }

                }

                var CurrentYear = new Date().getFullYear();
                
                var CurrentMonth = new Date().getMonth()+1;
                //var mm = 0
                var CurrentDate = new Date().getDate();
                const zeroPad = (num, places) => String(num).padStart(places, '0');
                
                function getdob(val) {
                    year = Math.floor(val);
                    
                    month = parseInt(val.toString().split('.')[1], 10);
                    
                    month = (CurrentMonth) - month;
                    
                    if(month < 1){
                        month = 12 - (-1*month);
                        year++;
                    }
                   // mm =month;
                   //alert(month);

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
                
                function validatemobile(){
                        let mobile = $("#patient_mobile_input").val();
                        
                        if(mobile.length == 10){ 
                                return true;
                        }
                        else{               
                            toastr['error']("Incorrect Mobile Number");		
                            return false;  
                        }
                        
                        
                    }
            </script>
            <?= view('Include/_autocomplete_general') ?>
            <script>
                
            var regions = <?= \BluelimeSystem::convert_array_to_string($regions); ?>;
            var subcities = <?= \BluelimeSystem::convert_array_to_string($subcities); ?>;
            autocomplete(document.getElementById("address"), regions);
            autocomplete(document.getElementById("subcity"), subcities);
                
            </script>
            <script type="text/javascript">
                function showwaitscreen(){
                                $("#wait_screen").modal({keyboard: true});
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
            
             function get_no_of_waiting(doctor_id)
                {   
                    var busy = document.getElementById("waitingBusy");
                    var waitingNo = document.getElementById("waitingNo");

                    waitingNo.innerHTML = "";
                    waitingNo.style.display = "none";

                    if(doctor_id == ''){
                        busy.style.display = "none";
                        dental_hide_flash('doctor_schedule_flash');
                        return false;
                    }

                    check_if_doctor_is_available_today(doctor_id);

                    
                    busy.style.display = "inline-block";
                    
                    $.ajax({
                        method: 'post',
                        url: "<?php echo base_url(); ?>hospital/ajax/getwaitingpatients",
                        data: {"doctor_id":doctor_id},
                        success: function(result){
                            if(result != null){
                                if(!isNaN(result))
                                    waitingNo.innerHTML = result + " waiting";
                                else{
                                    waitingNo.innerHTML = "Error";
                                    window.location.reload();
                                }
                                waitingNo.style.display = "inline-block";
                                busy.style.display = "none";
                            }
                        }
                    });

                }
                
                function validatedoctor(){
                    selected_doctor = document.getElementById("assigned_doctor").value;
            
                    if (selected_doctor == -1){
                            toastr['error']("Please Select a Doctor");
                            return false;
                    }
                    else{               
                        return true;
                    }
                    
                    
                }
                    
            </script>

            <?php
                // Include Add new modal with its Ajax js
                echo view('Include/_add_new_general');
            ?>
<?php
    helper('form');

    $loggeduserdata = $this->data['loggeduserdata'];
    $logged_user = $loggeduserdata['id'];
    $patient = $this->data['patient'];
    $visit = $this->data['visit'];
  
    
    $doctors = $this->data['doctors'];

    $GLOBALS['tagsinput_insert_data'] = '';

    function generate_encounter_row(array $encounters, string $preFix, string $title_tagsinput_class_name = ''){
        $row_count = 0;
        foreach($encounters as $row){
            if(is_array($row)){
                $row['title'] = $row['title'] ?? '';
                $row['description'] = $row['description'] ?? '';
            }
            else{
                $new_row = [];
                $new_row['description'] = $row;
                $new_row['title'] = '';
                $row = $new_row;
            }

            if((trim($row['title'])) != '' || (trim($row['description']))){
                echo '
                    <tr id="'.$preFix.'_'.$row_count.'">
                        <td width="">
                            <input autocomplete="off" type="text" id="'.$preFix.'_'.$row_count.'_title" onchange="set_encounter_data_general(\''.$preFix.'\','.$row_count.(($title_tagsinput_class_name != '') ? ',true' : '').')" list="'.$preFix.'_list" class="form-control '.$title_tagsinput_class_name.'" '.(($title_tagsinput_class_name == '') ? 'value="'.$row['title'].'"' : '').'>
                            <input placeholder="Description" type="text" id="'.$preFix.'_'.$row_count.'_description" onchange="set_encounter_data_general(\''.$preFix.'\','.$row_count.(($title_tagsinput_class_name != '') ? ',true' : '').')" class="form-control" value="'.$row['description'].'">
                        </td>
                        <td>';
                    if($row_count > 0)
                            echo '<a class="fa fa-minus-circle color-red" role="button"  onclick="remove_encounter_line_general(\'' . $preFix . '\', \'' . $row_count . '\')"></a>';
                echo    '</td>
                    </tr>';
                if($title_tagsinput_class_name != ''){
                    $tags = \BluelimeSystem::convert_to_array($row['title']);
                    
                    foreach($tags as $tag)
                        $GLOBALS['tagsinput_insert_data'] .= '$("#'.$preFix.'_'.$row_count.'_title").tagsinput("add", '.\BluelimeSystem::convert_array_to_string($tag).');
                        ';
                }
                $row_count++;
            }
        }

        if($row_count == 0){
            echo '
                    <tr id="'.$preFix.'_'.$row_count.'">
                        <td width="">
                            <input type="text" id="'.$preFix.'_'.$row_count.'_title" onchange="set_encounter_data_general(\''.$preFix.'\','.$row_count.(($title_tagsinput_class_name != '') ? ',true' : '').')" list="'.$preFix.'_list" class="form-control '.$title_tagsinput_class_name.'">
                            <input placeholder="Description" type="text" id="'.$preFix.'_'.$row_count.'_description" onchange="set_encounter_data_general(\''.$preFix.'\','.$row_count.(($title_tagsinput_class_name != '') ? ',true' : '').')" class="form-control" >
                        </td>
                    </tr>';
        }
    }

    function generate_treatment_plan_row_disabled(array $plans, string $preFix){
        $row_count = 0;
        foreach($plans as $row){
            if(is_array($row)){
                $row['title'] = $row['title'] ?? '';
                $row['description'] = $row['description'] ?? '';
            }
            else{
                $new_row = [];
                $new_row['description'] = $row;
                $new_row['title'] = '';
                $row = $new_row;
            }

            if((trim($row['title'])) != '' || (trim($row['description']))) {
                $tags = \BluelimeSystem::convert_to_array($row['title']);
                echo '
                    <tr>
                        <td colspan="3">
                            <lable class="label bg-primary dental-tags-lable">' . $tags[0]['service_name'] . '</lable> ' . $row['quantity'] .'
                            <div style="padding-left: 20px;">' . $row['description'] . '</div>
                        </td>
                    </tr>
                ';
            }
        }
    }

    function generate_treatment_plan_row(array $plans, string $preFix){
        
        $row_count = 0;
        foreach($plans as $row){
            if(is_array($row)){
                $row['title'] = $row['title'] ?? '';
                $row['description'] = $row['description'] ?? '';
            }
            else{
                $new_row = [];
                $new_row['description'] = $row;
                $new_row['title'] = '';
                $row = $new_row;
            }

            if((trim($row['title'])) != '' || (trim($row['description']))){
                echo '
                    <tr id="'.$preFix.'_'.$row_count.'">
                        <td>
                            <div class="">
                                <div class="col-sm-10 p-0">
                                    <input type="text" id="'.$preFix.'_'.$row_count.'_title" onchange="set_treatment_plan_data_general(\''.$preFix.'\','.$row_count.')" list="'.$preFix.'_list" class="form-control treatment_plan_input">
                                </div>
                                <div class="col-sm-2 p-0">
                                    <div class="input-group">
                                        <span class="input-group-addon dental-lr-padding-4">
                                            Qty
                                        </span>
                                        <input type="number" min="1" id="'.$preFix.'_'.$row_count.'_quantity" onchange="set_treatment_plan_data_general(\''.$preFix.'\','.$row_count.')" class="form-control dental-lr-padding-2" value="'.($row['quantity']??1).'">
                                    </div>
                                </div>
                                <div class="col-sm-12 p-0">
                                    <div class="input-group">
                                        <span class="input-group-addon dental-lr-padding-2">Description</span>
                                        <input type="text" id="'.$preFix.'_'.$row_count.'_description" onchange="set_treatment_plan_data_general(\''.$preFix.'\','.$row_count.')" class="form-control" value="'.$row['description'].'">
                                        <span class="input-group-addon" role="button"  onclick="add_to_order(this,\''.$preFix.'\',\''.$row_count.'\')"><a class="fa fa-check-circle"></a></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>';
                    if($row_count > 0)
                            echo '<a class="fa fa-minus-circle color-red" role="button" onclick="remove_treatment_plan_line_general(\'' . $preFix . '\', \'' . $row_count . '\')"></a>';
                echo    '
                        </td>
                    </tr>';
                
                $tags = \BluelimeSystem::convert_to_array($row['title']);
                 
                foreach($tags as $tag)
                    $GLOBALS['tagsinput_insert_data'] .= '$("#'.$preFix.'_'.$row_count.'_title").tagsinput("add", '.\BluelimeSystem::convert_array_to_string($tag).');
                    ';
                $row_count++;

            }
        }

        if($row_count == 0){
            echo '
                    <tr id="'.$preFix.'_'.$row_count.'">
                        <td>
                            <div class="">
                                <div class="col-sm-10 p-0">
                                    <input type="text" id="'.$preFix.'_'.$row_count.'_title" onchange="set_treatment_plan_data_general(\''.$preFix.'\','.$row_count.')" list="'.$preFix.'_list" class="form-control treatment_plan_input">
                                </div>
                                <div class="col-sm-2 p-0">
                                    <div class="input-group">
                                        <span class="input-group-addon dental-lr-padding-4">
                                            Qty
                                        </span>
                                        <input type="number" id="'.$preFix.'_'.$row_count.'_quantity" class="form-control dental-lr-padding-2" value="'.($row['quantity']??1).'">
                                    </div>
                                </div>
                                <div class="col-sm-12 p-0">
                                    <div class="input-group">
                                        <span class="input-group-addon dental-lr-padding-2">Description</span>
                                        <input type="text" id="'.$preFix.'_'.$row_count.'_description" onchange="set_treatment_plan_data_general(\''.$preFix.'\','.$row_count.')" class="form-control" value="'.$row['description'].'">
                                        <span class="input-group-addon" role="button"  onclick="add_to_order(this,\''.$preFix.'\',\''.$row_count.'\')"><a class="fa fa-check-circle"></a></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                        </td>
                    </tr>';
        }
    }

    function get_initial_encounter_data(string $encounter_title){
        $data = '{
                    "'.$encounter_title.'_0":{
                        "title":"",
                        "description":""
                    }
                }';
        $data = \BluelimeSystem::convert_to_array($data);
        return $data;
    }

    function get_initial_treatment_plan_remark_data(){
        $data = '{
                    "remark": "",
                    "selected_plan": "",
                    "selected_teeth": []
                }';
        $data = \BluelimeSystem::convert_to_array($data);
        return $data;
    }

    function correct_treatement_plan_remark_data_format($remark){
        if(empty($remark))
            return get_initial_treatment_plan_remark_data();
        
        if(!is_array($remark)){
            $new_remark = get_initial_treatment_plan_remark_data();
            $new_remark['remark'] = $remark;
            $remark = $new_remark;
        }


        $remark['remark'] = $remark['remark'] ?? '';
        $remark['remark'] = (is_string($remark['remark'])) ? $remark['remark'] : ((is_array($remark['remark'])) ? \BluelimeSystem::convert_array_to_string($remark['remark']) : '');
        
        $remark['selected_plan'] = $remark['selected_plan'] ?? '';
        $remark['selected_plan'] = (is_string($remark['selected_plan'])) ? $remark['selected_plan'] : ((is_array($remark['selected_plan'])) ? \BluelimeSystem::convert_array_to_string($remark['selected_plan']) : '');
        
        $remark['selected_teeth'] = $remark['selected_teeth'] ?? []; 
        $remark['selected_teeth'] = (is_array($remark['selected_teeth'])) ? $remark['selected_teeth'] : ((is_string($remark['selected_teeth'])) ? \BluelimeSystem::convert_to_array($remark['selected_teeth']) : []);

        return $remark;
    }

    function correct_encounter_data_format($encounter,string $encounter_title){
        if(empty($encounter))
            return get_initial_encounter_data($encounter_title);
        
        if(!is_array($encounter)){
            $new_encounter = [];
            $new_encounter[0] = $encounter;
            $encounter = $new_encounter;
        }

        foreach($encounter as $index => $row){
            if(is_array($row)){
                $row['title'] = $row['title'] ?? '';
                $row['description'] = $row['description'] ?? '';
            }
            else{
                $new_row = [];
                $new_row['description'] = $row;
                $new_row['title'] = '';
                $row = $new_row;
            }
            $encounter[$index] = $row;
        }
        return $encounter;
    }

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

    
    $visitEncounter['chief_complaints'] = correct_encounter_data_format($visitEncounter['chief_complaints']??[],'chief_complaints');
    $visitEncounter['past_medical_history'] = correct_encounter_data_format($visitEncounter['past_medical_history']??[],'past_medical_history');
    $visitEncounter['past_dental_medical_history'] = correct_encounter_data_format($visitEncounter['past_dental_medical_history']??[],'past_dental_medical_history');
    $visitEncounter['recent_illness'] = correct_encounter_data_format($visitEncounter['recent_illness']??[],'recent_illness');
    $visitEncounter['examination_extra_oral'] = correct_encounter_data_format($visitEncounter['examination_extra_oral']??[],'examination_extra_oral');
    $visitEncounter['examination_intra_oral'] = correct_encounter_data_format($visitEncounter['examination_intra_oral']??[],'examination_intra_oral');
    $visitEncounter['diagnosis'] = correct_encounter_data_format($visitEncounter['diagnosis']??[],'diagnosis');
    $visitEncounter['treatment'] = correct_encounter_data_format($visitEncounter['treatment']??[],'treatment');
    $visitEncounter['procedure'] = correct_encounter_data_format($visitEncounter['procedure']??[],'procedure');
    $visitEncounter['post_procedure_diagnosis'] = correct_encounter_data_format($visitEncounter['post_procedure_diagnosis']??[],'post_procedure_diagnosis');

    $visit['treatment_plans']['plan_1'] = correct_encounter_data_format($visit['treatment_plans']['plan_1']??[],'plan_1');
    $visit['treatment_plans']['plan_2'] = correct_encounter_data_format($visit['treatment_plans']['plan_2']??[],'plan_2');
    $visit['treatment_plans']['plan_3'] = correct_encounter_data_format($visit['treatment_plans']['plan_3']??[],'plan_3');
    //$visit['treatment_plans']['remark'] = correct_encounter_data_format($visit['treatment_plans']['remark']??[],'remark');
    $visit['treatment_plans']['remark'] = correct_treatement_plan_remark_data_format($visit['treatment_plans']['remark']??[]);

    $selected_treatment_plan = $visit['treatment_plans']['remark']['selected_plan'];

    $related_chief_complaints = "";
    $related_past_medical_history = "";
    $related_past_dental_medical_history = "";
    $related_recent_illness = "";
    $related_examination_extra_oral = "";
    $related_examination_intra_oral = "";
    $related_diagnosis = "";
    $related_treatment = "";
    $related_procedure = "";
    $related_post_procedure_diagnosis = "";
    
    
    $visit_id = $visit['id'];
    $mrn_no = $patient['id'];
    $formated_mrn_no = sprintf('%05d', $mrn_no);
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
                        <h1>Doctor</h1>
                        <small>Patient History</small>
                        <a class="btn btn-danger" style="float: right; margin-top: -10px;" href="<?= base_url() ?>hospital/doctor/openvisit?visit_id=<?= $visit_id ?>">Reopen Visit</a>
                    </div>
                </section>

                
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <!-- Doctor Assignment -->
                        
                    <div class="row">
                        <!-- Encounter entry -->
                        
                        <!-- Vital Sign -->
                        
                        <!-- Patient Info -->
                        <div class="col-sm-6 col-md-6">
                            <div class="panel panel-bd lobidisable">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4>Patient Detail</h4>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="form-body">
                                        <table class="table table-striped">
                                            <tr>
                                                <td width="120">Name</td>
                                                <td><?= $patient['first_name'] . ' ' . $patient['middle_name'] . ' ' . $patient['last_name']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Date of Birth</td>
                                                <td><?= $patient['date_of_birth']?> | Age: <b id="patient_age">01</b></td>
                                                <script>
                                                    function getAge(val){
                                                        var CurrentYear = new Date().getFullYear();
                                                        document.getElementById('patient_age').innerHTML = CurrentYear - val.slice(0, 4);
                                                    }
                                                    getAge('<?=$patient['date_of_birth']?>');
                                                </script>
                                            </tr>
                                            <tr>
                                                <td>Sex</td>
                                                <td><?= $patient['sex']?></td>
                                            </tr>
                                            <tr>
                                                <td>Mobile</td>
                                                <td><?= $patient['mobile']?></td>
                                            </tr>
                                            <tr>
                                                <td>Address</td>
                                                <td><?= $patient['address'];?> | <b>Sub city</b> <?= $patient['subcity'];?> | <b>Woreda</b> <?= $patient['woreda'];?> | <b>House&nbsp;N<u>o</u></b>&nbsp;<?= $patient['house_no'];?></td>
                                            </tr>
                                            <tr>
                                                <td>Emergency Contact</td>
                                                <td><b>Name</b> <?= $patient['emergency_contact_name']?> <br>
                                                    <b>Mobile</b> <?= $patient['emergency_mobile']?>
                                                </td>
                                            </tr>
                                            <?php if($patient['company_id'] != 0): ?>
                                                <tr>
                                                    <td>Company</td>
                                                    <td><?= $patient['company_name'];?></td>
                                                </tr>
                                            <?php endif ?>
                                            
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Medical Histors -->
                        <div class="col-sm-6 col-md-6">
                            <div class="panel panel-bd">
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
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-12">
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
                                                
                                                </>
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
                            
                            <?= view('Include/_encounter_history', ['history_follow_up_list'=>$history_follow_up_list, 'visit_id'=>$visit_id]); ?>
                        </div>
                        
                    </div>

                </section> <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <div>
                <div class="container">
                    <!-- Trigger the modal with a button -->
                    <!-- Tarnsfer to doctor another doctor modal -->
                    <div class="modal fade" id="transferDoctor" role="dialog">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header dental-bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Transfer to another doctor</h4>
                                </div>
                                <div class="modal-body">
                                    <?php
                                        $attributes = array('role'=>'form', 'method'=>'post', 'class'=>'inline', 'onsubmit'=>'return validate_assigntodoctor();');
                                        echo form_open_multipart('/hospital/doctor/trasfertodoctor', $attributes);
                                    ?>
                                        <input type="hidden" name="visit_id" value="<?= $visit_id ?>">
                                        <label for="assigned_doctor" class="control-label"><i style="width: 90px; display: inline-block;"></i><b>Assign to doctor</b></label>
                                        <div class="input-group">
                                            
                                            <div class="text-center" style="vertical-align: middle; padding:5px; display: table-cell; width:90px;">
                                                <img src="/img/loading-loading-forever.gif" width="20px;" style="display: none;" id="waitingBusy">
                                                <span class="label label-warning color-violet" id="waitingNo" style="display: none;">200 waiting</span>
                                            </div>
                                            <select id="assigned_doctor" name="assigned_doctor" class="form-control" required onchange="get_no_of_waiting(this.value)">
                                                <option></option>
                                                <?php
                                                    foreach($doctors as $doctor):
                                                ?>
                                                    <option value="<?= $doctor['id'] ?>" ><?= $doctor['name'] ?></option>';
                                                <?php
                                                    endforeach
                                                ?>
                                                    
                                            </select>
                                            <div class="input-group-btn" style="vertical-align: top;">
                                                <button class="btn btn-success" type="submit">
                                                    Transfer <i class="glyphicon  glyphicon-share-alt"></i>
                                                </button>
                                            </div>                                          
                                        </div>
                                    <?php
                                        echo form_close();
                                    ?>
                                    <div id="doctor_schedule_flash" class="alert alert-warning hide" role="alert" style="margin-top: 5px; margin-left: 90px; margin-right: 90px">
                                        <span></span>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                
                </div>
            </div>

            <?php
                // Include Add new modal with its Ajax js
                //echo view('Hospital/Include/_add_new_general');
            ?>
            
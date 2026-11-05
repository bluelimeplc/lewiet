<?php
    helper('form');
    $vital_signs = $this->data['vitalSigns'];
    
    //$GLOBALS['tagsinput_insert_data'] = '';
   

    $visitEncounterTemp = $visitEncounter;
    unset($visitEncounterTemp['id']);
    unset($visitEncounterTemp['visit_id']);
    unset($visitEncounterTemp['registered_date']);
    unset($visitEncounterTemp['patient_id']);
    unset($visitEncounterTemp['patient_name']);
    unset($visitEncounterTemp['doctor_id']);
    unset($visitEncounterTemp['doctor_name']);
    unset($visitEncounterTemp['created_at']);
    unset($visitEncounterTemp['updated_at']);
    unset($visitEncounterTemp['deleted_at']);

    function reset_encounters_id(array $encounters){
        $new_array = [];
        foreach($encounters as $index => $encounter){
            if($index == 'remark'){
                $new_array[$index] = $encounter;
                continue;
            }

            $cnt = 0;
            foreach($encounter as $data){
                $new_array[$index][$index.'_'.$cnt] = $data;
                $cnt++;
            }
        }
        return $new_array;
    }

    $visit_encounter_id_reseted = reset_encounters_id($visitEncounterTemp);
  
    function print_encounter_data(string $encounter_name, array $visit_encounter){
        $encounter_data =  $visit_encounter[$encounter_name] ?? [];
        foreach($encounter_data as $index => $value){
            $index_val = substr($index,(strlen($encounter_name)+1));
        
            echo '<div class="encounter_list" id="encounter_'.$index.'">
                    <a class="glyphicon glyphicon-minus-sign remove_encounter color-red" onclick="remove_encounter(\''.$encounter_name.'\', '.$index_val.')" role="button"></a>'.$value['title'].' | '.$value['description'].'</div>';
        }
    }

    function generate_order_option_row(string $option_id, int $service_id, string $service_name, float $quantity_val, float $price_val, int $bill_status = 0){
        // $service_key = 'service_' . $service_id;
        // $option_key = "option_" . $option_id;
        // if(intval($service_id) < 0)
        //     $service_key = 'service__' . (-1 * $service_id);

        
        $price_str = $price_val.' x '.$quantity_val.' = <b>'.($price_val*$quantity_val).'</b>';
        if($service_id == BluelimeConfiguration::DISCOUNT_PRICE_AMOUNT_ID)
            $price_str = '<b>'.$quantity_val.'</b>';
        elseif($service_id == BluelimeConfiguration::DISCOUNT_PRICE_PERCENT_ID)
            $price_str = '<b>'.$quantity_val.'%</b>';

        if(intval($service_id) != 1){
        echo '
            <tr id="order_option_service_' . $option_id . '_' . $service_id .'">
                <td>' . $service_name . '</td>
                <td colspan="2" align="right">
                    '.$price_str.'
                </td>';
        if($bill_status != 0)
            echo '<td>Cashier</td>';
        else
            echo '<td><button class="btn btn-danger" onclick="remove_service_from_order_option(' . $option_id . ', ' . $service_id . ');" type="button"><i class="fa fa-trash" ></i></button></td>';

        echo '
            </tr>';
        }
    }
 

    $related_chief_complaints = "";
    $related_physical_finding = "";
    $related_past_medical_history = "";
    $related_hpi = "";
    $related_diagnosis = "";
    $related_medication = "";
    $related_procedure = "";
    $related_post_procedure_diagnosis = "";
    $related_post_treatment = "";
    
    function get_related_visits_encounter_table($encounter, $date, $tagsinput=false){
        $is_first = true;
        $table_html = '';
        $row_count = 0;
        foreach($encounter as $row){
            $title = $row['title'];
           
            if($tagsinput){
                $tags = \BluelimeSystem::convert_to_array($row['title']);
                $title = '';
                foreach($tags as $tag){
                    $title .= '<span class="label bg-primary dental-tags-lable">'.$tag['service_name']. '</span>';
                }
            }
            if((trim($row['title'])=='') && (trim($row['description']) == ''))
                continue;
            if($is_first ){
                $table_html .= '<tr> <td style="background-color:#eee; padding:0px 2px;"><small>'.substr($date,0,10).'</small></td></tr>';
                $is_first = false;
            }
            
            $table_html .= '<tr>
                                <td><div style="padding-left:20px;">'.$title.'<div style="padding-left:20px;">'.$row['description'].'</div></div></td>
                            </tr>
                            ';
            $row_count++;
        }
        
        return $table_html;
    }

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

    if($related_chief_complaints != '')
        $related_chief_complaints = '<div><div><i role="button" id="show_related_chief_complaints" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down"></i><span>...</span></div><div class="hide"><table class="table">'.$related_chief_complaints.'</table></div></div>';
    
    if($related_physical_finding != '')
        $related_physical_finding = '<div><div><i role="button" id="show_related_physical_finding" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down"></i><span>...</span></div><div class="hide"><table class="table">'.$related_physical_finding.'</table></div></div>';

    if($related_hpi != '')
        $related_hpi = '<div><div><i role="button" id="show_related_hpi" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down"></i><span>...</span></div><div class="hide"><table class="table">'.$related_hpi.'</table></div></div>';

    if($related_past_medical_history != '')
        $related_past_medical_history = '<div><div><i role="button" id="show_related_past_medical_history" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down"></i><span>...</span></div><div class="hide"><table class="table">'.$related_past_medical_history.'</table></div></div>';
          
    if($related_diagnosis != '')
        $related_diagnosis = '<div><div><i role="button" id="show_related_diagnosis" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down"></i><span>...</span></div><div class="hide"><table class="table">'.$related_diagnosis.'</table></div></div>';        
    
    if($related_medication != '')
        $related_medication = '<div><div><i role="button" id="show_related_medication" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down"></i><span>...</span></div><div class="hide"><table class="table">'.$related_medication.'</table></div></div>';
    
    if($related_procedure != '')
        $related_procedure = '<div><div><i role="button" id="show_related_procedure" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down"></i><span>...</span></div><div class="hide"><table class="table">'.$related_procedure.'</table></div></div>';
    
    if($related_post_procedure_diagnosis != '')
        $related_post_procedure_diagnosis = '<div><div><i role="button" id="show_related_post_procedure_diagnosis" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down"></i><span>...</span></div><div class="hide"><table class="table">'.$related_post_procedure_diagnosis.'</table></div></div>';
    
    if($related_post_treatment != '')
        $related_post_treatment = '<div><div><i role="button" id="show_related_post_treatment" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down"></i><span>...</span></div><div class="hide"><table class="table">'.$related_post_treatment.'</table></div></div>';
    
        

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
                        <small>Visit</small>
                    </div>
                </section>

                
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <!-- Doctor Assignment -->
                        <div class="col-md-12">
                            <div class="panel panel-bd">
                                <div class="panel-body">
                                    
                                    <div class="row">
                                       
                                        <div class="col-sm-7" style="color:red; float:left">
                                            <!--a href="/hospital/doctor/visit?visit_id=<?php echo $visit_id ?>" title="Encounter" data-toggle="tooltip" data-html="true"><img src="/img/encounter.png" width="75px"></a>
                                            <a href="/hospital/doctor/newappointment?id=<?php echo $mrn_no ?>&vid=<?php echo $visit_id ?>" title="Appointment" data-toggle="tooltip" data-html="true"><img src="/img/appointment.png" width="50px"></a>
                                            
                                            <-->
                                            <a href="#" onclick="show_transfer_modal()"><img src="/img/transfer.png" width="50px" title="Refer" data-toggle="tooltip" data-html="true"></a>
                                            <a href="/hospital/doctor/newprescription?visit_id=<?php echo $visit_id ?>"><img src="/img/prescription.png" width="50px" title="Prescription" data-toggle="tooltip" data-html="true"></a>
                                            <a href="/hospital/doctor/newprogresspicture?id=<?php echo $mrn_no ?>&vid=<?php echo $visit_id ?>"><img src="/img/progresspicture.png" width="50px" title="Progress Picture" data-toggle="tooltip" data-html="true"></a>
                                            <a href="/hospital/doctor/newmedicalcertificate?id=<?php echo $mrn_no ?>&vid=<?php echo $visit_id ?>" title="Medical Certificate" data-toggle="tooltip" data-html="true"><img src="/img/certificate.png" width="50px"></a>
                                            <a href="/hospital/doctor/newmedicalreport?id=<?php echo $mrn_no ?>&vid=<?php echo $visit_id ?>" title="Medical Report" data-toggle="tooltip" data-html="true"><img src="/img/medical_summary.png" width="50px"></a>
                                            <a href="#" onclick="show_external_request_modal()"><img src="/img/request.png" width="50px" title="External Requests" data-toggle="tooltip" data-html="true"></a>
                                        <!--div ><a href="/hospital/reception/newappointment?id=<?php echo $mrn_no ?>" class="btn btn-success pull-right">Make Appointment</a></div-->
                                    </div>
                                                                        
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Patient Info -->
                    <div class="col-sm-6 col-md-12">
                        <div class="panel panel-bd">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <button class="btn btn-danger" style="float: right;" onclick="visit_close()">Visit Close</button>
                                    <h4>Patient Detail</h4>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="col-lg-6">
                                    <table class="table table-striped table-condensed">
                                        <tr>
                                            <td width="120">Name</td>
                                            <td><?= $patient['first_name'] . ' ' . $patient['middle_name'] . ' ' . $patient['last_name']; ?></td>
                                        </tr>
                                        <tr>
                                            <td width="120">Card Number</td>
                                            <td><?= "MRN-".sprintf('%05d', $patient['id']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Date of Birth</td>
                                            <td><?= $patient['date_of_birth']?> | Age: <b id="patient_age"><?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."Y "?><?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth']) >=10 ?"":BluelimeSystem::month_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."M"?></b></td>
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
                                        <?php if($patient['company_id'] != 0): ?>
                                            <tr>
                                                <td>Company</td>
                                                <td><?= $patient['company_name'];?></td>
                                            </tr>
                                        <?php endif ?>
                                        
                                    </table>
                                    <div class="col-lg-12 panel <?=empty($vital_signs)?"hidden":""?>">
                                <div class="dental-form-box">
                                    <div class="box-title">
                                        Vital Sign                                                            
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>BP</th>
                                                    <th>Weight</th>
                                                    <th>Temp.</th>
                                                    <th>PR</th>
                                                    <th>RR</th>
                                                    <th>Note</th>
                                                    <th>Taken By</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <?php
                                                foreach($vital_signs as $vital_sign){
                                                    $systolic = explode("/", $vital_sign['blood_pressure'], 2)[0];
                                                    $dystolic = explode("/", $vital_sign['blood_pressure'], 2)[1];
                                                    if($systolic < 90 or $dystolic < 60)
                                                        $color_pressure = "#54B4D3";
                                                    else if($systolic <= 120 and $dystolic <= 80)
                                                        $color_pressure = "success";
                                                    else if (($systolic > 120 && $systolic <= 130) and $dystolic <= 80)
                                                        $color_pressure = "yellow";
                                                    else if (($systolic > 130 && $systolic <= 140) or ($dystolic > 80 && $dystolic <= 90))
                                                        $color_pressure = "orange";
                                                    else if (($systolic > 140) or ($dystolic >= 90))
                                                        $color_pressure = "red";

                                                    if($vital_sign['temperature']<=35.9)
                                                        $color_temperature = "#54B4D3";
                                                    else if($vital_sign['temperature']>=36 and $vital_sign['temperature']<=37)
                                                        $color_temperature = "success";
                                                    else if($vital_sign['temperature']>=37.1 and $vital_sign['temperature']<=38)
                                                        $color_temperature = "orange"; 
                                                    else if($vital_sign['temperature']>=38.1)
                                                        $color_temperature = "red";
                                                    if($vital_sign['visit_id'] == $visit_id){
                                                        echo '
                                                        <tr class="bg-success">
                                                            <td style="background-color:'.$color_pressure.'">'.$vital_sign['blood_pressure'].'</td>
                                                            <td>'.$vital_sign['weight'].'</td>
                                                            <td style="background-color:'.$color_temperature.'">'.$vital_sign['temperature'].'</td>
                                                            <td>'.$vital_sign['pulse_rate'].'</td>
                                                            <td>'.$vital_sign['respiration_rate'].'</td>
                                                            <td>'.$vital_sign['note'].'</td>
                                                            <td style="font-size:smaller;">'.$vital_sign['registered_by_name'].'</td>
                                                            <td>'.date('d/m/y', strtotime($vital_sign['registered_date'])).'</td>
                                                        </tr>
                                                        ';
                                                        break;
                                                    }
                                                }
                                                foreach($vital_signs as $vital_sign){
                                                    if($vital_sign['visit_id'] != $visit_id){
                                                        echo '
                                                        <tr>
                                                            <td>'.$vital_sign['blood_pressure'].'</td>
                                                            <td>'.$vital_sign['weight'].'</td>
                                                            <td>'.$vital_sign['temperature'].'</td>
                                                            <td>'.$vital_sign['pulse_rate'].'</td>
                                                            <td>'.$vital_sign['respiration_rate'].'</td>
                                                            <td>'.$vital_sign['note'].'</td>
                                                            <td style="font-size:smaller;">'.$vital_sign['registered_by_name'].'</td>
                                                            <td>'.substr($vital_sign['registered_date'],0,10).'</td>
                                                        </tr>
                                                        ';
                                                    }
                                                }
                                            ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="dental-form-box">
                                        <div class="box-title">
                                            Medical History (<small><i class="glyphicon  glyphicon-remove" style="color:#ccc;"></i> <i class="glyphicon  glyphicon-ok"  style="color: #faa;"></i></small>) 
                                            <span style="width: 10px; display:inline-block "></span><a onclick="show_edit_medical_history()" role="button">Edit</a>
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
                                    
                                    
                                    <?php
                                        $free_reason = "";
                                        if(intval($visit['is_free']) == 1)
                                            $free_reason = "Transfer from branch";
                                        elseif (intval($visit['is_free']) == 2)
                                            $free_reason = "Followup after surgery / procedure";
                                        elseif (intval($visit['is_free']) == 3)
                                            $free_reason = "Approved free visit";
                                        elseif (intval($visit['is_free']) == 4)
                                            $free_reason = "Approved free visit";
                                        elseif (intval($visit['is_free']) == 5)
                                            $free_reason = "Referred by ".$visit['registered_by_name'];
                                        elseif((intval($visit['is_free']) == 0) && ($visit['fs'] == ""))
                                            $free_reason = "Within ".BluelimeConfiguration::UNPAID_VISIT_DEFAULT_DAYS." Days";
                                        else
                                            $free_reason = NULL;

                                        if($free_reason !== NULL){
                                            echo '
                                        <div class="panel panel-bd ">
                                            <div class="panel-heading" style="background-color: #ff0;">
                                                <div class="panel-title">
                                                    <h4>Free Consultation Reason</h4>
                                                </div>
                                            </div>
                                            <div class="panel-body" style="background-color: #ffa;">
                                            '.$free_reason.'
                                            </div>
                                        </div>
                                            
                                            ';

                                        }                                        
                                    ?>
                                    
                                    <?php
                                        if(trim(($visit['extra']['transfer_reason'] ?? '')) != ''):
                                    ?>
                                        <br>
                                        <div>
                                            <div class="panel panel-bd ">
                                                <div class="panel-heading" style="background-color: #ff0;">
                                                    <div class="panel-title">
                                                        <h4>Transfer reason</h4>
                                                    </div>
                                                </div>
                                                <div class="panel-body" style="background-color: #ffa;">
                                                    <?= str_replace("\n","<br>",($visit['extra']['transfer_reason'] ?? '')) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                        endif
                                    ?>
                                    <?php
                                        if(trim($last_visit_appointment_description) != ''):
                                    ?>
                                        <br>
                                        <div>
                                            <div class="panel panel-bd ">
                                                <div class="panel-heading" style="background-color: #ff0;">
                                                    <div class="panel-title">
                                                        <h4>Appointment Description</h4>
                                                    </div>
                                                </div>
                                                <div class="panel-body" style="background-color: #ffa;">
                                                    <?= str_replace("\n","<br>",$last_visit_appointment_description) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                        endif
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Encounter entry -->
                        <div class="col-md-12">
                            <div class="panel panel-bd">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <?php
                                            if($visit_is_joined_another_follow_up):
                                        ?>
                                                <div style="float:right"><a href="/hospital/doctor/disjoin_visit_from_follow_up?visit_id=<?= $visit_id ?>" title="Disjoin from the follow-up"  data-toggle="tooltip" data-html="true">Disjoin from the follow-up <i class="fa fa-unlink"></i></a></div>
                                        <?php
                                            endif;
                                        ?>
                                        <h4>Encounter</h4>
                                    </div>
                                </div>
                                <div>
                                    <div class="panel-body">
                                        <style>
                                            .encounter_description{
                                                margin-top: 5px;
                                                height: 100px;
                                                transition: height 1s ease, visibility .5s;
                                                transition-delay: .5s;
                                            }
                                            .encounter_description:placeholder-shown{
                                                height: 0px;
                                                visibility: collapse;
                                            }
                                            .encounter_container:focus-within .encounter_description{
                                                visibility: visible;
                                                height: 100px;
                                            }
                                            .encounter_list{
                                                padding: 10px;
                                                border-bottom:solid 1px  #ccc;
                                            }

                                            .encounter_list:last-child{
                                                border-bottom:none;
                                            }

                                            .encounter_list:hover .remove_encounter{
                                                visibility: visible;
                                            }

                                            .encounter_list_description{
                                                padding-left: 60px;
                                            }

                                            .remove_encounter{
                                                margin-right: 10px;
                                                visibility: hidden;
                                            }

                                            .input_box_red{
                                                border: solid red !important;
                                            }

                                        </style>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div style="text-align: center;"><button class="btn btn-sm btn-info" onclick="change_image_to_face_or_body('face_only')">Face</button> <button class="btn btn-sm btn-info" onclick="change_image_to_face_or_body('full_body')">Body</button></div>
                                                <div class="col-lg-12" >
                                                    <div id="full_body_image_container" class="">
                                                        <img id="full_body" src="/img/hospital/<?= ($patient['sex']=='Male')?'man':'woman' ?>_skin.svg" class="image" style="width:100%;"/>
                                                        <div id="full_body_canvas_container"></div>
                                                    </div>
                                                    <div id="face_only_image_container" class="">
                                                        <img id="face_only" src="/img/hospital/<?= ($patient['sex']=='Male')?'man':'woman' ?>_face01.png" class="image" style="width:100%;"/>
                                                        <div id="face_only_canvas_container"></div>
                                                    </div>
                                                    <script>
                                                        var darwing = {};
                                                        <?php
                                                            if(!isset($follow_up_selected_skin['full_body']))
                                                                $follow_up_selected_skin = ['full_body' => $follow_up_selected_skin, 'face_only'=>[], 'default_image_type'=>'full_body'];

                                                            echo 'darwing = '.BluelimeSystem::convert_array_to_string($follow_up_selected_skin).';';
                                                            
                                                        ?>
                                                        
                                                        var full_body_main, full_body_main_context, canvas_transform,full_body,face_only;

                                                        function init_canvas(){
                                                            full_body = $('#full_body');
                                                            let full_body_canvas = '<canvas onclick="canvas_clicked(event,\'full_body\')" id="full_body_main" width="'+full_body.width()+'" height="'+full_body.height()+'" style="position: absolute; left:'+full_body.position().left+'px; top:'+full_body.position().top+'px; cursor: pointer;"><canvas>';
                                                            $('#full_body_canvas_container').append(full_body_canvas);
                                                            full_body_main = document.getElementById('full_body_main');
                                                            full_body_main_context = full_body_main.getContext('2d');

                                                            face_only = $('#face_only');
                                                            let face_only_canvas = '<canvas onclick="canvas_clicked(event,\'face_only\')" id="face_only_main" width="'+face_only.width()+'" height="'+face_only.height()+'" style="position: absolute; left:'+face_only.position().left+'px; top:'+face_only.position().top+'px; cursor: pointer;"><canvas>';
                                                            $('#face_only_canvas_container').append(face_only_canvas);
                                                            face_only_main = document.getElementById('face_only_main');
                                                            face_only_main_context = face_only_main.getContext('2d');

                                                            change_image_to_face_or_body(darwing['default_image_type']);
                                                        }

                                                        function cover_image_with_canvas(image_type){
                                                            let image = $('#'+image_type);
                                                            let canvas = $('#'+image_type+'_main');
                                                            canvas_transform = {
                                                                left : image.position().left,
                                                                top : image.position().top,
                                                                width : image.width(),
                                                                height : image.height()
                                                            };
                                                            canvas.css(canvas_transform);
                                                        }

                                                        function canvas_clicked(e, image_type){
                                                            let image = full_body_main;
                                                            if(image_type=='face_only')
                                                                image = face_only_main;
                                                            let top = image.getBoundingClientRect().top;
                                                            let left = image.getBoundingClientRect().left;
                                                            var currentPoint = { x: e.clientX-left, y: e.clientY-top };
                                                            currentPoint.x = parseInt(currentPoint.x);
                                                            currentPoint.y = parseInt(currentPoint.y);
                                                            if(is_remove(currentPoint))
                                                                return 0;

                                                            let context = full_body_main_context;
                                                            if(image_type == 'face_only')
                                                                context = face_only_main_context;

                                                            context.fillStyle = "#0f0a";
                                                            context.strokeStyle = "#ff0";
                                                            context.beginPath();
                                                            context.arc(currentPoint.x, currentPoint.y, 5, 0, 5 * Math.PI);
                                                            context.fill();
                                                            context.stroke();
                                                            context.closePath();
                                                            darwing[image_type].push([currentPoint.x, currentPoint.y]);
                                                            save_encounter(null,true);
                                                        };
                                                        function is_remove(currentPoint){
                                                            let removed = false;
                                                            for (const key in darwing[darwing['default_image_type']]) {  
                                                                if (Object.prototype.hasOwnProperty.call(darwing[darwing['default_image_type']], key)) {
                                                                    const xy = darwing[darwing['default_image_type']][key];
                                                                    if(xy==null)
                                                                        continue;
                                                                    if((Math.abs(xy[0]-currentPoint.x) <= 4) && (Math.abs(xy[1]-currentPoint.y) <= 4)){
                                                                        remove_rect(xy[0]-7,xy[1]-7,15,15,darwing['default_image_type']);
                                                                        darwing[darwing['default_image_type']].splice(key,1);
                                                                        save_encounter(null,true);
                                                                        removed = true;
                                                                    }
                                                                }
                                                            }
                                                            return removed;
                                                        }

                                                        function daraw_from_array2(arr,image_type){
                                                            let image = full_body_main_context;
                                                            if(image_type == 'face_only')
                                                                image = face_only_main_context;

                                                            for (const key in arr) {  
                                                                if (Object.prototype.hasOwnProperty.call(arr, key)) {
                                                                    const xy = arr[key];
                                                                    image.fillStyle = "#0f0a";
                                                                    image.strokeStyle = "#ff0";
                                                                    image.beginPath();
                                                                    image.arc(xy[0], xy[1], 5, 0, 5 * Math.PI);
                                                                    image.fill();
                                                                    image.stroke();
                                                                }
                                                            }
                                                            //cover_image_with_canvas();
                                                        }

                                                        function daraw_from_array(arr){
                                                            daraw_from_array2(arr['full_body'],'full_body');
                                                            daraw_from_array2(arr['face_only'],'face_only');
                                                        }

                                                        $(document).ready(function(){
                                                            init_canvas();
                                                            setTimeout('daraw_from_array(darwing);',500);
                                                        });

                                                        $(window).resize(function(){isZooming();});

                                                        function isZooming(){
                                                            cover_image_with_canvas('face_only');
                                                            cover_image_with_canvas('full_body');
                                                        }

                                                        function clear_all_skin_selection(){
                                                            darwing[darwing['default_image_type']] = [];
                                                            remove_rect(0,0,1000,1000, darwing['default_image_type']);
                                                            save_encounter(null,true);
                                                        }

                                                        function remove_rect(x,y,width,height, image_type){
                                                            let context = full_body_main_context;
                                                            if(image_type == 'face_only')
                                                                context = face_only_main_context;

                                                            context.clearRect(x,y,width,height);
                                                        }

                                                        // function remove_circle(x,y,r, image_type){
                                                        //     let context = full_body_main_context;
                                                        //     if(image_type == 'face_only')
                                                        //         context = face_only_main_context;
                                                        
                                                        //     context.fillStyle = "#0001";
                                                        //     context.beginPath();
                                                        //     context.arc(x, x, r, 0, r * Math.PI);
                                                        //     context.fill();
                                                        // }

                                                        function change_image_to_face_or_body(image_type){
                                                            let the_other_one = "full_body";
                                                            if(image_type == "full_body")
                                                                the_other_one = "face_only";

                                                            let the_choosen_one_image_container = $("#"+image_type+"_image_container");
                                                            let the_other_one_image_container = $("#"+the_other_one+"_image_container");
                                                            the_other_one_image_container.addClass("hidden");
                                                            the_choosen_one_image_container.removeClass("hidden");

                                                            cover_image_with_canvas(image_type);
                                                            darwing['default_image_type'] = image_type;
                                                        }

                                                        // const image = document.getElementById("scream");
                                                        // image.addEventListener("load", (e) => {
                                                        //     ctx1.drawImage(image, 10, 10);
                                                        // });
                                                    </script>
                                                </div>
                                                <div style="text-align: right;">
                                                    <button class="btn btn-xs btn-warning" onclick="clear_all_skin_selection()">Clear all</button>
                                                </div>
                                                
                                                <label for="follow_up_remark">Remark</label> 
                                                <textarea class="form-control" id="follow_up_remark" onchange="save_encounter(null,true)"><?= $follow_up_remark ?></textarea><br>
                                                <label for="next_appointment_description">Next appointment</label>
                                                <textarea class="form-control" id="next_appointment_description" onchange="save_encounter(null,true)"><?= $visit['extra']['next_appointment_description'] ?? '' ?></textarea>
                                                <div>
                                                    <div class="dental-form-box">
                                                        <div class="box-title">
                                                            Treatment Plan
                                                        </div>
                                                        <div class="encounter_container">
                                                            <table width="100%">
                                                                <tr>
                                                                    <td>
                                                                        <input type="text" id="encounter_treatment_plans_title" class="form-control" tabindex="51">
                                                                    </td>
                                                                    <td width="30">
                                                                        <a class="btn btn-primary" href="#add" role="button" onclick="add_encounter('treatment_plans')" tabindex="53">+</a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <textarea class="encounter_description form-control" id="encounter_treatment_plans_description" placeholder="Description" tabindex="52"></textarea>
                                                        </div>
                                                        <div class="encounter_list_container" id="encounter_list_treatment_plans">
                                                            <?= print_encounter_data( 'treatment_plans', $visit_encounter_id_reseted) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="dental-form-box">
                                                            <div class="box-title">
                                                                Chief Complaint
                                                                <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new Chief Complaint" onclick="show_add_new_modal('Add New Chief Complaints', 'chief_complaints', auto_compelete['chief_complaints'])"></i>
                                                            </div>
                                                            <?= $related_chief_complaints; ?>
                                                            <div class="encounter_container">
                                                                <table width="100%">
                                                                    <tr>
                                                                        <td>
                                                                            <input type="text" id="encounter_chief_complaints_title" class="form-control" tabindex="10">
                                                                        </td>
                                                                        <td width="30">
                                                                            <a class="btn btn-primary" href="#add" role="button" onclick="add_encounter('chief_complaints')" tabindex="13">+</a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <textarea class="encounter_description form-control" id="encounter_chief_complaints_description" placeholder="Description" tabindex="12"></textarea>
                                                            </div>
                                                            <div class="encounter_list_container" id="encounter_list_chief_complaints">
                                                                <?= print_encounter_data( 'chief_complaints', $visit_encounter_id_reseted) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="dental-form-box">
                                                            <div class="box-title">
                                                                Physical Finding
                                                                <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new Physical Finding" onclick="show_add_new_modal('Add New Physical Finding', 'physical_finding', auto_compelete['physical_finding'])"></i>
                                                            </div>
                                                            <?= $related_physical_finding; ?>
                                                            <div class="encounter_container">
                                                                <table width="100%">
                                                                    <tr>
                                                                        <td>
                                                                            <input type="text" id="encounter_physical_finding_title" class="form-control" tabindex="14">
                                                                        </td>
                                                                        <td width="30">
                                                                            <a class="btn btn-primary" href="#add" role="button" onclick="add_encounter('physical_finding')" tabindex="16">+</a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <textarea class="encounter_description form-control" id="encounter_physical_finding_description" placeholder="Description" tabindex="15"></textarea>
                                                            </div>
                                                            <div class="encounter_list_container" id="encounter_list_physical_finding">
                                                                <?= print_encounter_data( 'physical_finding', $visit_encounter_id_reseted) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="dental-form-box">
                                                            <div class="box-title">
                                                                Past Medical History
                                                                <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new Past medical history" onclick="show_add_new_modal('Add New Past Medical History', 'past_medical_history', auto_compelete['past_medical_history'])"></i>
                                                            </div>
                                                            <?= $related_past_medical_history; ?>
                                                            <div class="encounter_container">
                                                                <table width="100%">
                                                                    <tr>
                                                                        <td>
                                                                            <input type="text" id="encounter_past_medical_history_title" class="form-control" tabindex="31">
                                                                        </td>
                                                                        <td width="30">
                                                                            <a class="btn btn-primary" href="#add" role="button" onclick="add_encounter('past_medical_history')" tabindex="33">+</a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <textarea class="encounter_description form-control" id="encounter_past_medical_history_description" placeholder="Description" tabindex="32"></textarea>
                                                            </div>
                                                            <div class="encounter_list_container" id="encounter_list_past_medical_history">
                                                                <?= print_encounter_data( 'past_medical_history', $visit_encounter_id_reseted) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="dental-form-box">
                                                            <div class="box-title">
                                                                HPI
                                                                <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new HPI" onclick="show_add_new_modal('Add New HPI', 'hpi', auto_compelete['hpi'])"></i>
                                                            </div>
                                                            <?= $related_hpi; ?>
                                                            <div class="encounter_container">
                                                                <table width="100%">
                                                                    <tr>
                                                                        <td>
                                                                            <input type="text" id="encounter_hpi_title" class="form-control" tabindex="22">
                                                                        </td>
                                                                        <td width="30">
                                                                            <a class="btn btn-primary" href="#add" role="button" onclick="add_encounter('hpi')" tabindex="23">+</a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <textarea class="encounter_description form-control" id="encounter_hpi_description" placeholder="Description" tabindex="22"></textarea>
                                                            </div>
                                                            <div class="encounter_list_container" id="encounter_list_hpi">
                                                                <?= print_encounter_data( 'hpi', $visit_encounter_id_reseted) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="dental-form-box">
                                                            <div class="box-title">
                                                                Diagnosis
                                                                <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new Diagnosis" onclick="show_add_new_modal('Add New Diagnosis', 'diagnosis', auto_compelete['diagnosis'])"></i>
                                                            </div>
                                                            <?= $related_diagnosis; ?>
                                                            <div class="encounter_container">
                                                                <table width="100%">
                                                                    <tr>
                                                                        <td>
                                                                            <input type="text" id="encounter_diagnosis_title" class="form-control" tabindex="41">
                                                                        </td>
                                                                        <td width="30">
                                                                            <a class="btn btn-primary" href="#add" role="button" onclick="add_encounter('diagnosis')" tabindex="43">+</a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <textarea class="encounter_description form-control" id="encounter_diagnosis_description" placeholder="Description" tabindex="42"></textarea>
                                                            </div>
                                                            <div class="encounter_list_container" id="encounter_list_diagnosis">
                                                                <?= print_encounter_data( 'diagnosis', $visit_encounter_id_reseted) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="dental-form-box">
                                                            <div class="box-title">Medication</div>     
                                                            <?= $related_medication; ?>
                                                            <div class="encounter_container">
                                                                <div class="input-group">
                                                                    <input type="text" id="encounter_medication_title" class="form-control" tabindex="81">
                                                                    <a class="input-group-addon btn btn-primary" href="#add" role="button" onclick="add_encounter('medication')" tabindex="83">+</a>
                                                                </div>
                                                                <textarea class="encounter_description form-control" id="encounter_medication_description" placeholder="Description" rows="3" tabindex="82"></textarea>
                                                            </div>
                                                            <div class="encounter_list_container" id="encounter_list_medication">
                                                                <?= print_encounter_data( 'medication', $visit_encounter_id_reseted) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="dental-form-box">
                                                            <div class="box-title">Procedure</div>
                                                            <datalist id="procedure_list">
                                                                <option value=""></option>
                                                                
                                                            </datalist>        

                                                            <?= $related_procedure;?>
                                                            <div class="encounter_container">
                                                                <div class="input-group">
                                                                    <input type="text" id="encounter_procedure_title" class="form-control" tabindex="91">
                                                                    <a class="input-group-addon btn btn-primary" href="#add" role="button" onclick="add_encounter('procedure')" tabindex="93">+</a>
                                                                </div>
                                                                <textarea class="encounter_description form-control" id="encounter_procedure_description" placeholder="Description" rows="3" tabindex="92"></textarea>
                                                            </div>
                                                            <div class="encounter_list_container" id="encounter_list_procedure">
                                                                <?= print_encounter_data( 'procedure', $visit_encounter_id_reseted) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="dental-form-box">
                                                            <div class="box-title">
                                                                Post Procedure Diagnosis
                                                                <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new Diagnosis" onclick="show_add_new_modal('Add New Diagnosis', 'diagnosis', auto_compelete['diagnosis'])"></i>
                                                            </div>
                                                            <?= $related_post_procedure_diagnosis; ?>
                                                            <div class="encounter_container">
                                                                <table width="100%">
                                                                    <tr>
                                                                        <td>
                                                                            <input type="text" id="encounter_post_procedure_diagnosis_title" class="form-control" tabindex="101">
                                                                        </td>
                                                                        <td width="30">
                                                                            <a class="btn btn-primary" href="#add" role="button" onclick="add_encounter('post_procedure_diagnosis', 'diagnosis')" tabindex="103">+</a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <textarea class="encounter_description form-control" id="encounter_post_procedure_diagnosis_description" placeholder="Description" tabindex="102"></textarea>
                                                            </div>
                                                            <div class="encounter_list_container" id="encounter_list_post_procedure_diagnosis">
                                                                <?= print_encounter_data( 'post_procedure_diagnosis', $visit_encounter_id_reseted) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="dental-form-box">
                                                            <div class="box-title">Post-treatment Result/Outcome</div>

                                                            <?= $related_post_treatment;?>
                                                            <div class="encounter_container">
                                                                <div class="input-group">
                                                                    <input type="text" id="encounter_post_treatment_title" class="form-control" tabindex="91">
                                                                    <a class="input-group-addon btn btn-primary" href="#add" role="button" onclick="add_encounter('post_treatment')" tabindex="93">+</a>
                                                                </div>
                                                                <textarea class="encounter_description form-control" id="encounter_post_treatment_description" placeholder="Description" rows="3" tabindex="92"></textarea>
                                                            </div>
                                                            <div class="encounter_list_container" id="encounter_list_post_treatment">
                                                                <?= print_encounter_data( 'post_treatment', $visit_encounter_id_reseted) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        
                                        
                                        <div class="row">
                                            
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <div style="text-align: right;">
                                            <!--input type="hidden" name="visit_id" value="<?= $visit_id ?>">
                                            <input type="submit" value="Save" class="btn btn-success" -->
                                            <a onclick="save_encounter_manually()" role="link" class="btn btn-success">Save</a>
                                            <br>
                                            <div id="save_encounter_flash" class="alert alert-danger hide" role="alert" style="margin: 5px; display:inline-block; min-width: 300px; text-align:left;">
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <!-- order  -->
                                <div class="col-lg-6">
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
                                                                        <th width="150">Price</th>
                                                                        <th width="70">Qty</th>
                                                                        <th>&nbsp;</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <input type="text" class="form-control" id="order_input" />
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" min="1" value="" class="form-control" id="order_option_price_1"/>
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
                                                                                generate_order_option_row('1',$service['service_id'],$service['service_name'],floatval($service['quantity']),floatval($service['price']), $service['bill_status']);
                                                                            }
                                                                        ?>
                                                                    </tbody>
                                                                    <tfoot id="order_option_service_list_sent_to_pos">
                                                                        <?php
                                                                            foreach($orderOption_Sent_to_pos as $index => $service){
                                                                                generate_order_option_row('1',$service['service_id'],$service['service_name'],floatval($service['quantity']),floatval($service['price']), $service['bill_status']);
                                                                            }
                                                                        ?>
                                                                    </tfoot>
                                                            </table>
                                                        </div>
                                                        <table class="table">
                                                <tr>
                                                    <td width="80%">Order Comment <textarea class="form-control" id="order_comment"><?= trim($order_comments)?></textarea></td>
                                                    <td><br><br><button type="button" id="btnSaveOrderOption" class="btn btn-success" onclick="save_order_option()">Send Order</button></td>
                                                </tr>
                                            </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div id="save_order_option_flash" class="alert alert-danger hide" role="alert" style="display:inline-block; min-width: 300px; text-align:left;">
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
                        
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                            <!-- Laboratory Results -->
                                <div class="form-group col-md-6">
                                    <div class="panel panel-bd">
                                        <div class="dental-form-box">
                                            <div class="box-title">
                                                Laboratory Results
                                            </div>
                                            <div id="lab_results">
                                                <table class="table table-bordered"  style="margin-bottom:0px">
                                                    <tr><th width="40%">Test</th><th width="40%">Result</th><th>Ref. Range</th></tr>
                                                </table>
                                                <?php
                                                    $prev_date = "";
                                                    //BluelimeSystem::print_associated_array($laboratory_results_compiled);
                                                    foreach($laboratory_results_compiled as $service_result){
                                                        $current_date = $service_result['order_main']['result_approved_date'];

                                                        if(($prev_date != $current_date) && ($prev_date != "")){
                                                            echo '
                                                            </fieldset>
                                                            <br>
                                                            ';
                                                        }

                                                        if(($prev_date == "") || ($prev_date != $current_date)){
                                                            echo '
                                                            <fieldset class="reset">
                                                                <legend class="reset">
                                                                    <span class="badge badge-qty-warning">'.$current_date.'</span> <span class="badge">'.($service_result['order_main']['result_approved_by_detail']['name']??'').'</span>
                                                                </legend>
                                                            ';
                                                            if(trim($service_result['order_main']['result_remark']) != ''){
                                                                echo '
                                                                    <div class="panel panel-bd ">
                                                                        <div class="panel-heading" style="background-color: #ff0;">
                                                                            <div class="panel-title">
                                                                                <h4>Lab Tech. Remark</h4>
                                                                            </div>
                                                                        </div>
                                                                        <div class="panel-body" style="background-color: #ffa;">
                                                                            '.str_replace("\n",'<br>',$service_result['order_main']['result_remark']).'
                                                                        </div>
                                                                    </div>
                                                                ';
                                                            }
                                                        }
                                              
                                                        echo '<table class="table table-bordered">';
                                                        echo '<tr><th colspan="3">'. $service_result['service_name'] .'</th></tr>';
                                                        
                                                        foreach($service_result['result'] as $result){
                                                            echo '<tr style="color:'.@$result['flag_color'].'"><td width="40%">'.$result['attribute_name'].' '.$result['result_flag'].' </td><td width="40%">'.$result['result'].'</td><td>'.$result['reference_range'].'</td></tr>';
                                                        }
                                                        echo '</table>';

                                                        $prev_date = $current_date;
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
                                                $edit_prescription = '<a href="/hospital/doctor/newprescription?visit_id='.$visit_id.'&prescription_group_id='.($group['prescriptions'][0]['prescription_group_id'] ?? $group['compoundings'][0]['prescription_group_id']).'"><span class="badge badge-qty">Edit</span></a>';
                                                $edit_prescription_disableds = '<a style="cursor: not-allowed;"><span class="badge bg-gray">Edit</span></a>';
                                                $stvoid = "";
                                                $void="Void";
                                                if(@$group['prescriptions'][0]['is_sold']==1 || @$group['compoundings'][0]['is_sold']==1){
                                                    $edit_prescription = $edit_prescription_disableds;
                                                }

                                                if(@$group['prescriptions'][0]['is_void']==1 || @$group['compoundings'][0]['is_void']==1){
                                                    $stvoid = "text-decoration: line-through red 2px;";
                                                    $void = "";
                                                    $edit_prescription = $edit_prescription_disableds;
                                                }
                                                //BluelimeSystem::show_and_die(@$group['compoundings'][0]['visit_id']);
                                                if(!empty($group['prescriptions'][0])){
                                                    if(($group['prescriptions'][0]['visit_id'] != $visit_id) || ($group['prescriptions'][0]['registered_by'] != $loggeduserdata['id'])){
                                                        $void = "";
                                                        $edit_prescription = $edit_prescription_disableds;
                                                    }
                                                }
                                                if(!empty($group['compoundings'][0])){
                                                    if(($group['compoundings'][0]['visit_id'] != $visit_id) || ($group['compoundings'][0]['created_by'] != $loggeduserdata['id'])){
                                                        $void = "";
                                                        $edit_prescription = $edit_prescription_disableds;
                                                    }
                                                }
                                                $pid = @$group['prescriptions'][0]['prescription_group_id'] ??@$group['compoundings'][0]['prescription_group_id'];
                                                //BluelimeSystem::show_and_die($pid);
                                                
                                                echo '
                                        <div class="col-lg-6"  style=>
                                            <fieldset class="reset" style="'.$stvoid.'">
                                                
                                                <legend class="reset"><span class="badge badge-qty-warning">'.$date.'</span> <span class=" badge badge-qty-warning">'.$prescribed_by.'</span> <a onclick ="void_prescription('.$pid.')"><span class="badge btn-danger" style="cursor:pointer">'.$void .'</span></a> '.$edit_prescription.' </legend>';
                                                
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

                        <!-- Biopsy Result -->
                        <div class="col-md-12">
                            <div class="panel panel-bd">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4><a class="" data-toggle="collapse"  href="#collapseBiopsy" role="button" aria-expanded="false" aria-controls="collapseBiopsy">
                                                Biopsy Result
                                            </a>
                                        </h4>
                                    </div>
                                </div>
                                        <div class="panel-body collapsed" id="collapseBiopsy">
                                    <div class="row">
                                        <?php
                                            foreach($biopsy_result as $index => $rt){
                                                if($index % 2 === 0){
                                                    echo '<div class="row">';
                                                }

                                                $resultDate = date('Y-m-d', strtotime($rt['created_at']));
                                                $orderRow = $order_biopsy['id_'.$rt['order_id']] ?? [];
                                                $approvedByName = $orderRow['result_approved_by_name'] ?? '';
                                                $orderedByName = $orderRow['ordered_by_name'] ?? '';
                                                $orderedDate = $orderRow['created_at'] ?? '';
                                                $approvedBy = !empty($approvedByName) ? '<span class="badge bg-success">Approved by: '.$approvedByName.'</span>' : '';
                                                $orderedBy = !empty($orderedByName) ? '<span class="badge">Ordered by: '.$orderedByName.'</span>' : '';

                                                echo '<div class="col-md-6">
                                                        <div class="panel panel-bd" style="margin-bottom: 10px;">
                                                            <div class="panel-heading" style="background: #f5f7fa; border-bottom: 1px solid #e1e3e6;">
                                                                <div class="panel-title">
                                                                    '.$orderedBy.'</span> <span class="badge badge-qty-warning">'.date('Y-m-d', strtotime($orderedDate)).'</span>
                                                                     '.$approvedBy.'</span> <span class="badge badge-qty-warning">'.$resultDate.'
                                                                </div>
                                                            </div>
                                                            <div class="panel-body">
                                                                <table class="table table-borderless table-condensed" style="margin-bottom: 0;">
                                                                    <tr>
                                                                        <td style="width: 35%; font-weight: bold;">Histology Number:</td>
                                                                        <td>'.($rt['biopsy_result']['biopsy_number'] ?? '').'</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="font-weight: bold;">Gross Description:</td>
                                                                        <td>'.($rt['biopsy_result']['gross_morphology'] ?? '').'</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="font-weight: bold;">Microscopy:</td>
                                                                        <td>'.($rt['biopsy_result']['microscopy'] ?? '').'</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="font-weight: bold;">Diagnosis:</td>
                                                                        <td>'.($rt['biopsy_result']['conclusion'] ?? '').'</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="font-weight: bold;">Comment:</td>
                                                                        <td>'.($rt['biopsy_result']['recommendation'] ?? '').'</td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>';

                                                if($index % 2 === 1){
                                                    echo '</div>';
                                                }
                                            }

                                            if(count($biopsy_result) % 2 !== 0){
                                                echo '</div>';
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
            <link rel="stylesheet" href="/Image-gallery/assets/css/jquery.lightbox.css">
            <script src="/Image-gallery/assets/js/jquery.lightbox.js"></script>
            <div class="manual_card">
                <style>
                    .view_card{
                        position: fixed;
                        top: 50%;
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

            <div>
                <div class="container">
                    <!-- Trigger the modal with a button -->
                    <!-- Tarnsfer to doctor another doctor modal -->
                    <div class="modal fade" id="transferDoctor" role="dialog">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header dental-bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Refer to another doctor</h4>
                                </div>
                                <?php
                                        $attributes = array('role'=>'form', 'method'=>'post', 'class'=>'inline', 'onsubmit'=>'return validate_assigntodoctor();');
                                        echo form_open_multipart('/hospital/doctor/refertodoctor', $attributes);
                                ?>
                                    <div class="modal-body">
                                        <input type="hidden" name="visit_id" value="<?= $visit_id ?>">
                                        <label for="assigned_doctor" class="control-label"><i style="width: 90px; display: inline-block;"></i><b>Assign to doctor</b></label>
                                        <div class="input-group">
                                            <div class="text-center" style="vertical-align: middle; padding:5px; display: table-cell; width:90px;">
                                                <img src="/img/loading-loading-forever.gif" width="20px;" style="display: none;" id="waitingBusy">
                                                <span class="label label-warning color-violet" id="waitingNo" style="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                            </div>
                                            <select id="assigned_doctor" name="assigned_doctor" class="form-control" required onchange="get_no_of_waiting(this.value)">
                                                <option></option>
                                                <?php
                                                    foreach($doctors as $doctor):
                                                ?>
                                                    <option <?= ($loggeduserdata['id'] == $doctor['id']) ? 'disabled' : '' ?> value="<?= $doctor['id'] ?>" ><?= $doctor['name'] ?></option>';
                                                <?php
                                                    endforeach
                                                ?>
                                                    
                                            </select>
                                                                                   
                                        </div>
                                        <div class="input-group">
                                            <br>
                                            <label for="transfer_reason">Transfer reason</label><br>
                                            <textarea class="form-control" id="transfer_reason" name="transfer_reason" required style="width:270px"></textarea>
                                        </div>
                                        <div id="doctor_schedule_flash" class="alert alert-warning hide" role="alert" style="margin-top: 5px;">
                                            <span></span>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-success" type="submit">
                                            Refer&nbsp;&nbsp;&nbsp;<i class="glyphicon  glyphicon-share-alt"></i>
                                        </button>
                                    </div>
                                <?php
                                    echo form_close();
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="externalRequest" role="dialog">
                        <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <!-- Nav tabs -->
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item active">
                                                    <a class="nav-link active  dental-bg-primary" data-toggle="tab" href="#laboratory_request">Laboratory Request</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link  dental-bg-primary" data-toggle="tab" href="#biopsy_request">Biopsy Request</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link  dental-bg-primary" data-toggle="tab" href="#referal_request">Referal Request</a>
                                                </li>
                                            </ul>

                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <div id="laboratory_request" class="tab-pane active"><br>
                                                <div id="" class="tab-pane active"><br>
                                                    <table class="table table-striped table-condensed">
                                                        <tr>
                                                            <td width="120"><b>Hospital/Health Center:</b></td>
                                                            <td colspan="4">
                                                                <input type="text" list="company_list" class="form-control" id="requested_company_name" />
                                                                        <datalist id="company_list">
                                                                           
                                                                            <option value="Rank Dermatology Clinic Wollosefer">
                                                                            <option value="Rank Dermatology Clinic Lebu">
                                                                            <option value="Swiss Diagnostics Ethiopia">
                                                                            <option value="Arsho Medical Laboratories">
                                                                            <option value="International Clinical Laboratories(ICL)">
                                                                            <option value="Pioneer Diagnostic Center">
                                                                            
                                                                        </datalist>
                                                                <input type="hidden" class="form-control" id="request_type" value="LABORATORY_REQUEST" />
                                                                <input type="hidden" class="form-control" id="request_patient_name" value="<?= $patient['first_name'] . ' ' . $patient['middle_name'] . ' ' . $patient['last_name']; ?>" />
                                                                <input type="hidden" class="form-control" id="request_patient_id" value="<?=$patient['id']?>" />
                                                                <input type="hidden" class="form-control" id="request_doctor_id" value="<?=$loggeduserdata['id']?>" />
                                                                <input type="hidden" class="form-control" id="request_registered_by_name" value="<?=$loggeduserdata['name']?>" />
                                                                <input type="hidden" class="form-control" id="request_registered_by" value="<?=$loggeduserdata['id']?>" />
                                                                <input type="hidden" class="form-control" id="request_visit_id" value="<?=$visit_id?>" />
                                                                <input type="hidden" class="form-control" id="request_mobile" value="<?=$patient['mobile']?>" />
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Patient Information:</b></td>  
                                                            <td><?= $patient['first_name'] . ' ' . $patient['middle_name'] . ' ' . $patient['last_name']; ?></td>
                                                            <td><?= "MRN-".sprintf('%05d', $patient['id']); ?></td>
                                                            <td>Age: <?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."Y "?><?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth']) >=10 ?"":BluelimeSystem::month_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."M"?></td>
                                                           
                                                            
                                                            <td><?= $patient['sex']?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Requesting Physician:</b></td>   
                                                            <td colspan="4">
                                                            <?=$loggeduserdata['name']?>
                                                            </td>
                                                        </tr>
                                            
                                                    </table>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="bg-info">
                                                                <th width="350">Test Name</th>
                                                                <th width="300">Note</th>
                                                                <th>&nbsp;</th>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <input list="lab_tests" type="text" class="form-control" id="request_test_name" />
                                                                        <datalist id="lab_tests">
                                                                            <?php
                                                                            foreach($service_list_lab as $service){
                                                                            ?>
                                                                            <option value="<?=$service['service_name']?>">
                                                                            <?php
                                                                            }
                                                                            ?>
                                                                        </datalist>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control" id="request_test_note"/>
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-info" type="button" onclick="add_request_test_line()"><i class='fa fa-plus' ></i></button>
                                                                    <!--button class="btn btn-info" onclick="remove_order_option(1,1);" type="button"><i class='fa fa-minus' ></i></button-->
                                                                </td>
                                                            </tr>
                                                            </thead>
                                                            
                                                            <tbody id="lab_request_detail_line">
                                                            
                                                                  
                                                            </tbody>
                                                            <tfoot>
                                                            <tr>
                                                                    <td></td>
                                                                <td>
                                                                <textarea class="form-control" rows="3" style="width: 500px;resize: none;" placeholder="Remark"  id="request_remark"></textarea></td>
                                                                </tr> 
                                                            </tfoot>
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" id="" class="btn btn-success" onclick="show_confirm_pin_lab()">Save</button>
                                                    
                                                </div>
                                                </div>
                                                
                                                <div id="biopsy_request" class="tab-pane fade"><br>
                                                    <table class="table table-striped table-condensed">
                                                        <tr>
                                                            <td width="200"><b>Hospital/Health Center:</b></td>
                                                            <td colspan="4">
                                                                <input type="text" list="company_list_bio" class="form-control" id="bio_requested_company_name" />
                                                                <datalist id="company_list_bio">
                                                                           
                                                                            <option value="Swiss Diagnostics Ethiopia">
                                                                            
                                                                        </datalist>
                                                                        
                                                    
                                                        <input type="checkbox" id="bio_urgent" /> <label for="bio_urgent" style="color: red; font-weight: bold;">Urgent</label>
                                                    
                                                                <input type="hidden" class="form-control" id="bio_request_type" value="BIOPSY_REQUEST" />
                                                                <input type="hidden" class="form-control" id="bio_request_patient_name" value="<?= $patient['first_name'] . ' ' . $patient['middle_name'] . ' ' . $patient['last_name']; ?>" />
                                                                <input type="hidden" class="form-control" id="bio_request_patient_id" value="<?=$patient['id']?>" />
                                                                <input type="hidden" class="form-control" id="bio_request_doctor_id" value="<?=$loggeduserdata['id']?>" />
                                                                <input type="hidden" class="form-control" id="bio_request_registered_by_name" value="<?=$loggeduserdata['name']?>" />
                                                                <input type="hidden" class="form-control" id="bio_request_registered_by" value="<?=$loggeduserdata['id']?>" />
                                                                <input type="hidden" class="form-control" id="bio_request_visit_id" value="<?=$visit_id?>" />
                                                                <input type="hidden" class="form-control" id="bio_request_mobile" value="<?=$patient['mobile']?>" />
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Patient Information:</b></td>  
                                                            <td><?= $patient['first_name'] . ' ' . $patient['middle_name'] . ' ' . $patient['last_name']; ?></td>
                                                            <td><?= "MRN-".sprintf('%05d', $patient['id']); ?></td>
                                                            <td>Age: <?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."Y "?><?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth']) >=10 ?"":BluelimeSystem::month_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."M"?></td>
                                                           
                                                            
                                                            <td><?= $patient['sex']?></td>
                                                        </tr>
                                                        <tr>
                                                        <td><b>Requesting Physician:</b></td>   
                                                        <td colspan="4">
                                                                                
                                                                                <input type="text" class="form-control" id="bio_request_doctor_name"  value="<?=$loggeduserdata['name']?>" />
                                                                                </td>
                                                        </tr>
                                                        <tr>
                                                        <td><b>Surgeon:</b></td>   
                                                        <td colspan="4">
                                                                                <?=$loggeduserdata['name']?>
                                                                                </td>
                                                        </tr>
                                                        <tr>
                                                        <td><b>Sampling Date:</b></td>   
                                                        <td colspan="4">
                                                                                <?=date('Y-m-d')?>
                                                                                </td>
                                                        </tr>
                                            
                                                </table>
                                                <table>
                                                        <tr>
                                                            <th>History </th>
                                                            <td style="padding: 3px;">
                                                            <textarea class="form-control" rows="2" style="width: 500px;resize: none;border-radius: 10px;" placeholder="" id="bio_request_history"></textarea></td>
                                                        </tr>
                                                            
                                                        <tr>
                                                            <th>Physical Finding </th>
                                                            <td style="padding: 3px;">
                                                            <textarea class="form-control" rows="2" style="width: 500px;resize: none;border-radius: 10px;" placeholder="" id="bio_request_physical_finding"></textarea></td>
                                                        </tr>
                                                            
                                                        <tr>
                                                            <th>Clinical Diagnosis </th>
                                                            <td style="padding: 3px;">
                                                            <textarea class="form-control" rows="2" style="width: 500px;resize: none;border-radius: 10px;" placeholder="" id="bio_request_diagnosis"></textarea></td>
                                                        </tr>
                                                            
                                                        <tr>
                                                            <th>Laboratory & imaging Information </th>
                                                            <td style="padding: 3px;">
                                                            <textarea class="form-control" rows="2" style="width: 500px;resize: none;border-radius: 10px;" placeholder="" id="bio_request_imaging"></textarea></td>
                                                        </tr>
                                                    <table width="100%">
                                                        <tr>
                                                            <th>Biopsy Type</th>
                                                            <th id="size_header" style="display: none;">Size</th>
                                                            <th>Biopsy Site</th>
                                                            <th>Anesthesia</th>
                                                            <th>Fixative</th>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <select name="biopsy_type" id="biopsy_type" class="form-control" required onchange="toggleSizeField()">
                                                                    <option value="">--Select Biopsy Type--</option>
                                                                    <optgroup label="Grade 1">
                                                                    <option value="Grade 1 Punch biopsy">Punch biopsy</option>
                                                                    <option value="Grade 1 Incisional biopsy">Incisional biopsy</option>
                                                                    <option value="Grade 1 Wedge biopsy">Wedge biopsy</option>
                                                                    <option value="Grade 1 Shave biopsy">Shave biopsy</option>
                                                                    <option value="Grade 1 Curettage">Curettage</option>
                                                                    <option value="Grade 1 Electrosurgical">Electrosurgical</option>
                                                                    <option value="Grade 1 Snip or scissors biopsy">Snip or scissors biopsy</option>
                                                                    <option value="Grade 1 Small excisional biopsy">Small excisional biopsy</option>
                                                                    </optgroup>
                                                                    <optgroup label="Grade 2">
                                                                    <option value="Grade 2 Excisional biopsy">Excisional biopsy</option>
                                                                    </optgroup>
                                                                    
                                                                </select>
                                                            </td>
                                                            <td id="size_container" style="display: none;width:100px;">
                                                                <input type="text" class="form-control" id="biopsy_size"  style="width:100px;" />
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control" id="bio_request_site" required />
                                                            </td>
                                                            <td>
                                                                <select class="form-control" id="bio_request_anesthesia" required>
                                                                    <option value="">Select Anesthesia</option>
                                                                    <option value="Local">Local</option>
                                                                    <option value="Topical">Topical</option>
                                                                    <option value="General">General</option>
                                                                    <option value="other">Other</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control" id="bio_request_fixative" required>
                                                                    <option value="">Select Fixative</option>
                                                                    <option value="Formalin 10%">Formalin 10%</option>
                                                                    <option value="Formalin 8%">Formalin 8%</option>
                                                                    <option value="Other">Other</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                                <div class="modal-footer">
                                    <div id="" class="alert alert-danger hide" role="alert" style="display:inline-block; min-width: 300px; text-align:left;">
                                        <span></span>
                                    </div>
                                    <button type="button" id="" class="btn btn-success" onclick="show_confirm_pin_bio()">Save</button>
                                    
                                </div>
                                                </div> 
                                                
                                                <div id="referal_request" class="tab-pane fade"><br>
                                                    <table class="table table-striped table-condensed">
                                                        <tr>
                                                            <td width="120"><b>Hospital/Health Center:</b></td>
                                                            <td colspan="4">
                                                                <input type="text" list="company_list_ref" class="form-control" id="ref_requested_company_name" />
                                                                <datalist id="company_list_ref">
                                                                           
                                                                            <option value="Rank Dermatology Wollosefer">
                                                                            <option value="Rank Dermatology Gazebo">
                                                                            <option value="Rank Dermatology Lebu">
                                                                            <option value="ALERT Hospital">
                                                                            
                                                                        </datalist>
                                                                <input type="hidden" class="form-control" id="ref_request_type" value="REFERAL_REQUEST" />
                                                                <input type="hidden" class="form-control" id="ref_request_patient_name" value="<?= $patient['first_name'] . ' ' . $patient['middle_name'] . ' ' . $patient['last_name']; ?>" />
                                                                <input type="hidden" class="form-control" id="ref_request_patient_id" value="<?=$patient['id']?>" />
                                                                <input type="hidden" class="form-control" id="ref_request_doctor_id" value="<?=$loggeduserdata['id']?>" />
                                                                <input type="hidden" class="form-control" id="ref_request_registered_by_name" value="<?=$loggeduserdata['name']?>" />
                                                                <input type="hidden" class="form-control" id="ref_request_registered_by" value="<?=$loggeduserdata['id']?>" />
                                                                <input type="hidden" class="form-control" id="ref_request_visit_id" value="<?=$visit_id?>" />
                                                                <input type="hidden" class="form-control" id="ref_request_mobile" value="<?=$patient['mobile']?>" />
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><b>Patient Information:</b></td>  
                                                            <td><?= $patient['first_name'] . ' ' . $patient['middle_name'] . ' ' . $patient['last_name']; ?></td>
                                                            <td><?= "MRN-".sprintf('%05d', $patient['id']); ?></td>
                                                            <td>Age: <?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."Y "?><?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth']) >=10 ?"":BluelimeSystem::month_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."M"?></td>
                                                           
                                                            
                                                            <td><?= $patient['sex']?></td>
                                                        </tr>
                                                        <tr>
                                                        <td><b>Requesting Physician:</b></td>   
                                                        <td colspan="4">
                                                                                <?=$loggeduserdata['name']?>
                                                                                </td>
                                                        </tr>
                                            
                                                    </table>
                                                    <table>
                                                    <tr>
                                                        <th>Clinical Finding </th>
                                                        <td style="padding: 3px;">
                                                        <textarea class="form-control" rows="2" style="width: 500px;resize: none;border-radius: 10px;" placeholder="" id="ref_clinical_finding"></textarea></td>
                                                    </tr>
                                                        
                                                    <tr>
                                                        <th>Diagnosis </th>
                                                        <td style="padding: 3px;">
                                                        <textarea class="form-control" rows="2" style="width: 500px;resize: none;border-radius: 10px;" placeholder="" id="ref_diagnosis"></textarea></td>
                                                    </tr>
                                                        
                                                    <tr>
                                                        <th>Investigation Result </th>
                                                        <td style="padding: 3px;">
                                                        <textarea class="form-control" rows="2" style="width: 500px;resize: none;border-radius: 10px;" placeholder="" id="ref_investigation_result"></textarea></td>
                                                    </tr>
                                                        
                                                    <tr>
                                                        <th>Px Given </th>
                                                        <td style="padding: 3px;">
                                                        <textarea class="form-control" rows="2" style="width: 500px;resize: none;border-radius: 10px;" placeholder="" id="ref_px_given"></textarea></td>
                                                    </tr>    
                                                    <tr>
                                                        <th>Reason for Referal </th>
                                                        <td style="padding: 3px;">
                                                        <textarea class="form-control" rows="2" style="width: 500px;resize: none;border-radius: 10px;" placeholder="" id="ref_reason"></textarea></td>
                                                    </tr>
                                                    </table>
                                                <div class="modal-footer">
                                                    <div id="" class="alert alert-danger hide" role="alert" style="display:inline-block; min-width: 300px; text-align:left;">
                                                        <span></span>
                                                    </div>
                                                    <button type="button" id="" class="btn btn-success" onclick="show_confirm_pin_ref()">Save</button>
                                    
                                                </div>
                                                </div>                                              
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <!-- edit_medical_history-->
                    <div class="modal fade" id="edit_medical_history" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header dental-bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Edit Medical History</h4>
                                </div>
                                <form action="/hospital/doctor/update_medical_history" method="POST">
                                    <input type="hidden" name="patient_id" value="<?= $patient['id']?>">
                                    <input type="hidden" name="visit_id" value="<?= $visit_id ?>">
                                    <div class="modal-body">
                                        <table class="table table-striped">
                                            <tr>
                                                <td>&nbsp; </td>
                                                <td width="100"><i class="glyphicon  glyphicon-ok color-red" title="Yes"></i></td>
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
                                            <tr style="<?=@$patient['sex']=='Male'? "visibility:hidden;":""?>">
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
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-success" type="submit">
                                            Update
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

            <form onsubmit="event.preventDefault(); checkpinlab();">
        <div class="modal fade" id="confirmPinlab" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Enter Pin Code</h4>
                    </div>
                    <div class="modal-body">
                        <input id="pin_enteredlab" placeholder="Enter pin" type="password" maxlength="4" class="form-control focus_input_pin"
                            autocomplete="off" pattern="[0-9]+" autofocus >
                        <input id="user_idlab" type="hidden" value="<?=$loggeduserdata['id'] ?>">
                        <div id="pinCheck_flash" class="alert alert-danger hide" role="alert">
                            <span></span>
                        </div>
                        <div style="text-align: center;color:black"><b><br>
                            <?php echo '<img style="height:100px;" src="data:image/jpeg;base64,'.base64_encode( $loggeduserdata['digital_signature'] ).'"/>' ?><br>
                            <?=$loggeduserdata['name'] ?><br>
                            <?=$loggeduserdata['title'] ?></b>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-success" value="Confirm" type="button" onclick="checkpinlab()">
                    </div>
                </div>
            </div>
        </div>
        </form>
        <form onsubmit="event.preventDefault(); checkpinbio();">
        <div class="modal fade" id="confirmPinbio" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Enter Pin Code</h4>
                    </div>
                    <div class="modal-body">
                        <input id="pin_enteredbio" placeholder="Enter pin" type="password" maxlength="4" class="form-control focus_input_pin"
                            autocomplete="off" pattern="[0-9]+" autofocus >
                        <input id="user_idbio" type="hidden" value="<?=$loggeduserdata['id'] ?>">
                        <div id="pinCheck_flash" class="alert alert-danger hide" role="alert">
                            <span></span>
                        </div>
                        <div style="text-align: center;color:black"><b><br>
                            <?php echo '<img style="height:100px;" src="data:image/jpeg;base64,'.base64_encode( $loggeduserdata['digital_signature'] ).'"/>' ?><br>
                            <?=$loggeduserdata['name'] ?><br>
                            <?=$loggeduserdata['title'] ?></b>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-success" value="Confirm" type="button" onclick="checkpinbio()">
                    </div>
                </div>
            </div>
        </div>
        </form>
        <form onsubmit="event.preventDefault(); checkpinref();">
        <div class="modal fade" id="confirmPinref" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Enter Pin Code</h4>
                    </div>
                    <div class="modal-body">
                        <input id="pin_enteredref" placeholder="Enter pin" type="password" maxlength="4" class="form-control focus_input_pin"
                            autocomplete="off" pattern="[0-9]+" autofocus >
                        <input id="user_idref" type="hidden" value="<?=$loggeduserdata['id'] ?>">
                        <div id="pinCheck_flash" class="alert alert-danger hide" role="alert">
                            <span></span>
                        </div>
                        <div style="text-align: center;color:black"><b><br>
                            <?php echo '<img style="height:100px;" src="data:image/jpeg;base64,'.base64_encode( $loggeduserdata['digital_signature'] ).'"/>' ?><br>
                            <?=$loggeduserdata['name'] ?><br>
                            <?=$loggeduserdata['title'] ?></b>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-success" value="Confirm" type="button" onclick="checkpinref()">
                    </div>
                </div>
            </div>
        </div>
        </form>
            <?= view('Include/_add_new_general') ?>
            <script>
                <?php $encounter_data_str = \BluelimeSystem::convert_array_to_string($visit_encounter_id_reseted); ?>
                var encounter_data = <?= ($encounter_data_str=='[]')?'{}':$encounter_data_str ?>;
                
                var follow_up_data =  {'remark':"<?= str_replace("\n",'\n', addslashes($follow_up_remark)) ?>"};
                //var encounter_id_generator = {"chief_complaints":Object.keys(encounter_data['chief_complaints']).length, "hpi":Object.keys(encounter_data['hpi']).length, "past_medical_history":Object.keys(encounter_data['past_medical_history']).length, "past_dental_medical_history":Object.keys(encounter_data['past_dental_medical_history']).length, "examination_extra_oral":Object.keys(encounter_data['examination_extra_oral']).length, "examination_intra_oral":Object.keys(encounter_data['examination_intra_oral']).length,"diagnosis":Object.keys(encounter_data['diagnosis']).length,"treatment":Object.keys(encounter_data['treatment']).length,"procedure":Object.keys(encounter_data['procedure']).length,"post_procedure_diagnosis":Object.keys(encounter_data['post_procedure_diagnosis']).length};
    
                function add_encounter(encounter_name, reference_encounter_name = '', focus=true){
                    let title = $('#encounter_'+encounter_name+'_title').val();
                    let description = $('#encounter_'+encounter_name+'_description').val();

                    let encounter_name_temp = encounter_name;
                    if(reference_encounter_name != '')
                        encounter_name =reference_encounter_name;

                    if((encounter_name == 'diagnosis') && ((title.trim() == '') || (!check_in_autocomplete(title,encounter_name)))){
                        $('#encounter_'+encounter_name_temp+'_title').addClass("input_box_red");
                        return false;
                    }
                    //will be corrected in check_in_autocomplete
                    title = $('#encounter_'+encounter_name+'_title').val();

                    if(title.trim() == ''){
                        $('#encounter_'+encounter_name_temp+'_title').addClass("input_box_red");
                        return false;
                    }
                    else{
                        $('#encounter_'+encounter_name_temp+'_title').removeClass("input_box_red");
                    }

                    encounter_name = encounter_name_temp;

                    let index = 0;
                    if(encounter_data.hasOwnProperty(encounter_name))
                        index = Object.keys(encounter_data[encounter_name]).length;
                    else
                        encounter_data[encounter_name] = {};

                    encounter_data[encounter_name][encounter_name + "_" + index] = {"title":title,"description":description};
                    let encounter_line = '<div class="encounter_list" id="encounter_'+encounter_name+'_'+index+'"><a class="glyphicon glyphicon-minus-sign remove_encounter color-red" onclick="remove_encounter(\''+encounter_name+'\', '+index+')" role="button"></a>'+title+' | '+description+'</div>';
                    let encounter_list_container = $('#encounter_list_'+encounter_name);
                    encounter_list_container.append(encounter_line);
                    clear_encounter_entry(encounter_name,focus);
                    save_encounter(null,true);
                    return true;
                }

                function check_in_autocomplete(search, encounter_name){
                    let title_input = $('#encounter_'+encounter_name+'_title');
                    let title = title_input.val();
                    let found = false;
                    for(key in auto_compelete[encounter_name]){
                        if(auto_compelete[encounter_name][key].toLocaleLowerCase().replaceAll(' ','') == search.toLocaleLowerCase().replaceAll(' ','')){
                            found = true;
                            title_input.val(auto_compelete[encounter_name][key]);
                            return true;
                        }
                    }
                    
                    return found;
                }

                function remove_encounter(encounter_name, index){
                    delete encounter_data[encounter_name][encounter_name + "_" + index];
                    $('#encounter_'+encounter_name+'_'+index).remove();
                    save_encounter(null,true);
                }

                function clear_encounter_entry(encounter_name, focus=true){
                    $('#encounter_'+encounter_name+'_title').val('');
                    $('#encounter_'+encounter_name+'_description').val('');
                    if(focus)
                        $('#encounter_'+encounter_name+'_title').focus();
                }

                function show_collapse_related_encounter(obj){
                    obj.classList.toggle('fa-caret-up');
                    obj.classList.toggle('fa-caret-down');
                    obj.parentElement.parentElement.childNodes[1].classList.toggle('hide');
                    obj.parentElement.parentElement.childNodes[0].childNodes[1].classList.toggle('hide');

                }

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
                        $options_json .= ':{service_id:"'.$option_service['service_id'].'", service_name: "'.$option_service['service_name'].'", quantity: "'.$option_service['quantity'].'", price: "'.$option_service['price'].'"}, ';
                    }
                    
                    $options_json .= '}}';

                    echo '
                        order_option = '.$options_json.';';
                ?>

                function add_order_option(){
                    if(selected_service_index['order'] == -1){
                        flash_red_for_validation('order_input');
                        return 0;
                    }
                    else if($("#order_option_price_1").val().trim() == ''){
                        if(services[selected_service_index['order']] > 0){
                            flash_red_for_validation('order_option_price_1');
                            return 0;
                        }
                    }
                    else if(parseInt($("#order_option_price_1").val()) <= 0){
                        flash_red_for_validation('order_option_price_1');
                        return 0;
                    }
                
                    let option_id = 1;
                    let service_index = selected_service_index['order'];
                    let service_name = services[service_index].service_name;
                    let service_id = services[service_index].id;
                    
                    let option_key = "option_" + option_id;
                    let quantity_val = $("#order_option_quantity_" + option_id).val();
                    let price_val = $("#order_option_price_" + option_id).val();
                    $('#order_option_price_1').attr('readonly', false);

                    if((parseFloat(quantity_val.trim()) <= 0) || (isNaN(parseFloat(quantity_val.trim())))){
                        flash_red_for_validation("order_option_quantity_" + option_id);
                        return false;
                    }
                       
                    let new_service = {service_id: parseInt(service_id), service_name: service_name, quantity: parseFloat(quantity_val), price:parseFloat(price_val)};

                    if(order_option[option_key] === undefined)
                        order_option[option_key] = {};

                    service_key = 'service_' + service_id;
                    if(service_id < 0)
                        service_key = 'service__' + (-1*service_id);

                    order_option[option_key][service_key] = new_service;

                   
                    let order_option_service_list = $('#order_option_service_list_' + option_id);

                    order_option_service_list.html(create_order_list_html(option_id));

                    $("#order_input").val('');
                    $("#order_option_price_" + option_id).val('');
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
                        let price_val = value['price'];
                        let service_id = value['service_id'];
                        service_key = 'service_' + service_id;
                        let price_display = price_val+' x '+quantity_val+' = <b>'+(price_val*quantity_val);

                        if(service_id < 0){
                            service_key = 'service__' + (-1*service_id);
                            price_display = quantity_val;
                            if(service_id == <?= BluelimeConfiguration::DISCOUNT_PRICE_PERCENT_ID ?>)
                                price_display += ' %';
                        }
                        
                        new_service_option_row += '<tr id="order_option_service_' + option_id + '_' + service_id + '">'
                                                    +'<td>' + value['service_name'] + '</td>'
                                                    +'<td colspan="2" align="right">'
                                                    +'  '+ price_display +'</b>'
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
                    //console.log(obj.parentElement.children[1].value);
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
                        order_comment: $('#order_comment').val()
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
                        flash_red_for_validation('order_input');
                        toastr['error']('Clear service selector before saving.');
                        // order_input.css('background-color', '#f00');
                        // order_input.focus();
                        // setTimeout("$('#order_input').css('background-color', '#fff')",500);
                        return false;
                    }
                    return true;
                }

                var encounter_ready_to_be_saved = true;
                var do_not_update_followup_data = false;
                function save_encounter_manually(){
                    if(check_unsaved_work())
                        toastr['error']('Encounter is not saved.');
                    else
                        toastr['success']('Encounter saved!');
                }

                function save_encounter(redirect_to = null, save_only = false){
                    if(!encounter_ready_to_be_saved)
                        return 0;

                    let visit_id = <?= $visit_id ?>;
                    let patient_id = <?= $patient['id'] ?>;
                    let patient_name = "<?= $visit['patient_name'] ?>";
                    let mobile = "<?= $patient['mobile'] ?>";
                    let company_id = "<?= $patient['company_id'] ?>";
                    let company_name = "<?= $patient['company_name'] ?>";
                    //let next_appointment_reason = $('#next_appointment_reason').val();
                    let follow_up_remark = $('#follow_up_remark').val();
                    let next_appointment_description = $('#next_appointment_description').val();
                    
                    let visit_encounter = {
                        "visit_id":visit_id,
                        "patient_id":patient_id,
                        "patient_name":patient_name,
                        "mobile":mobile,
                        "company_id":company_id,
                        "company_name":company_name,
                        "encounter_data":encounter_data,
                        //"follow_up_data":follow_up_data,
                        "follow_up_remark":follow_up_remark,
                        "selected_skin": JSON.stringify(darwing),
                        "do_not_update_followup_data": do_not_update_followup_data,
                        "next_appointment_description": next_appointment_description
                    };

                    $.ajax({
                        method: 'post',
                        url: "<?php echo base_url(); ?>hospital/ajax/savevisitencounter",
                        data: visit_encounter,
                        success: function(result){
                            try{
                                let r = JSON.parse(result);
                                if(r.status == "success"){
                                    if(save_only)//do nothing
                                        ;
                                    else if(redirect_to == null)
                                        document.location = '/hospital/doctor/';
                                    else
                                        document.location = redirect_to;
                                }
                                else
                                    dental_show_flash('save_encounter_flash', result);
                            }
                            catch (e){
                                error_html = "Invalid JSON returned.<br><u>Result</u>:<br>" + result + "<hr>Exception:<br>"+e;
                                dental_show_flash('save_encounter_flash', error_html);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            error_html = "Update is not made. <br><u>Error</u>:<br>" + errorThrown;
                            dental_show_flash('save_encounter_flash', error_html, false);
                        }
                    });
                }

                
                /*
                $(window).bind('beforeunload', function() {
                    save_encounter(null, true);
                });
                */
                
                function validate_assigntodoctor(){
                    if($("#assigned_doctor").val() !== '-1')
                        return true;
                    return false;
                }

                function check_if_doctor_is_available_today(doctor_id){
                    
                    $.ajax({
                        method: 'post',
                        url: "<?php echo base_url(); ?>hospital/ajax/checkifdoctorisavailable",
                        data: {"doctor_id":doctor_id, "date":"<?= date("Y-m-d") ?>"},
                        success: function(result){
                            try{
                                let r = JSON.parse(result);
                                let html_txt = " Today this doctor is available from " + r.start_time + " to " + r.end_time;
                            }
                            catch (e){
                                dental_show_flash('doctor_schedule_flash', 'This doctor is not available today.');
                            }
                        }
                    });
                }
                
                function get_no_of_waiting(doctor_id)
                {
                    dental_hide_flash('doctor_schedule_flash');

                    var busy = document.getElementById("waitingBusy");
                    var waitingNo = document.getElementById("waitingNo");

                    waitingNo.innerHTML = "";
                    waitingNo.style.display = "none";

                    if(doctor_id == ''){
                        busy.style.display = "none";
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

                function show_transfer_modal(){
                    $("#transferDoctor").modal("show");
                }
                function show_external_request_modal(){
                    $("#externalRequest").modal("show");
                }

            </script>
            <?= view('Include/_autocomplete_service') ?>
            <?= view('Include/_autocomplete_single_string') ?>
            <script>
                function set_focus_after_selection(args){
                    $('#'+args[0]).focus();
                }

                var  selected_service;
                function get_default_price(){
                     selected_service = services[selected_service_index['order']];
                    let service_price = selected_service['service_price'];
                    $('#order_option_price_1').val(service_price);
                    $('#order_option_price_1').attr('readonly', true);

                    if(selected_service['service_type'] == "Procedure" ||selected_service['service_type'] == "Pathology"){
                        $('#order_option_price_1').attr('readonly', false);
                        $('#order_option_price_1').focus();
                        $('#order_option_price_1').select();
                    }
                }

                var services = <?= BluelimeSystem::convert_array_to_string($service_list) ?>;
                selected_service_index['order'] = -1;
                var autocomplete_order_option = {
                    input:document.getElementById("order_input"),
                    services:services,
                    selected_index:'order',
                    callback_function: get_default_price,
                };
                autocomplete_service_by_options(autocomplete_order_option);

                selected_service_index['treatment_plan'] = -1;
                var autocomplete_treatment_plan_option = {
                    input:document.getElementById("encounter_treatment_plans_title"),
                    services:services,
                    selected_index:'treatment_plan',
                    callback_function: set_focus_after_selection,
                    callback_function_arguments: ['encounter_treatment_plans_title']
                };
                autocomplete_service_by_options(autocomplete_treatment_plan_option);

                var autocomplete_single_string_options = {
                    'chief_complaints' : {
                        input:document.getElementById("encounter_chief_complaints_title"),
                        services:auto_compelete['chief_complaints'],
                        selected_index:'selected_chief_complaints_index',
                        callback_function: set_focus_after_selection,
                        callback_function_arguments: ['encounter_chief_complaints_title']
                    },
                    'physical_finding' : {
                        input:document.getElementById("encounter_physical_finding_title"),
                        services:auto_compelete['physical_finding'],
                        selected_index:'selected_physical_finding_index',
                        callback_function: set_focus_after_selection,
                        callback_function_arguments: ['encounter_physical_finding_title']
                    },
                    'hpi' : {
                        input:document.getElementById("encounter_hpi_title"),
                        services:auto_compelete['hpi'],
                        selected_index:'selected_hpi_index',
                        callback_function: set_focus_after_selection,
                        callback_function_arguments: ['encounter_hpi_title']
                    },
                    'diagnosis' : {
                        input:document.getElementById("encounter_diagnosis_title"),
                        services:auto_compelete['diagnosis'],
                        selected_index:'selected_diagnosis_index',
                        callback_function: set_focus_after_selection,
                        callback_function_arguments: ['encounter_diagnosis_title']
                    },
                    'past_medical_history' : {
                        input:document.getElementById("encounter_past_medical_history_title"),
                        services:auto_compelete['past_medical_history'],
                        selected_index:'selected_past_medical_history_index',
                        callback_function: set_focus_after_selection,
                        callback_function_arguments: ['encounter_past_medical_history_title']
                    },
                    'post_procedure_diagnosis' : {
                        input:document.getElementById("encounter_post_procedure_diagnosis_title"),
                        services:auto_compelete['diagnosis'],
                        selected_index:'selected_post_procedure_diagnosis_index',
                        callback_function: set_focus_after_selection,
                        callback_function_arguments: ['encounter_post_procedure_diagnosis_title']
                    },
                    'post_treatment' : {
                        input:document.getElementById("encounter_post_treatment_title"),
                        services:auto_compelete['post_treatment'],
                        selected_index:'selected_post_treatment_index',
                        callback_function: set_focus_after_selection,
                        callback_function_arguments: ['encounter_post_treatment_title']
                    }
                };
                autocomplete_single_string_by_options(autocomplete_single_string_options['chief_complaints']);
                autocomplete_single_string_by_options(autocomplete_single_string_options['physical_finding']);
                autocomplete_single_string_by_options(autocomplete_single_string_options['hpi']);
                autocomplete_single_string_by_options(autocomplete_single_string_options['diagnosis']);
                autocomplete_single_string_by_options(autocomplete_single_string_options['past_medical_history']);
                autocomplete_single_string_by_options(autocomplete_single_string_options['post_procedure_diagnosis']);
                autocomplete_single_string_by_options(autocomplete_single_string_options['post_treatment']);

                /*
                selected_item_index['comp_selected_item_index'] = -1;
                autocomplete_item(document.getElementById("encounter_chief_complaints_title"), items, 'comp_selected_item_index',set_focus_after_selection,['encounter_chief_complaints_title']);
                function set_focus_after_selection(args){
                    $('#'+args[0]).focus();
                }
                */
               
                function visit_close(){
                    if(confirm("Are you sure you want to close this visit?"))
                        save_encounter('/hospital/doctor/closevisit?visit_id=<?= $visit_id ?>');
                }

                var encounters = ['chief_complaints', 'physical_finding', 'hpi', 'past_medical_history', 'diagnosis', 'medication', 'post_procedure_diagnosis', 'procedure', 'post_treatment', 'treatment_plans'];

                function check_unsaved_work(){
                    encounter_ready_to_be_saved = false;
                    let unsaved_change_found = false;
                    let title, description, encounter_name;
                    for(key in encounters){
                        encounter_name = encounters[key];
                        title = $('#encounter_'+encounter_name+'_title').val().trim();
                        let success = true;
                        if((encounter_name == 'post_procedure_diagnosis') && (title != ''))
                            success = add_encounter(encounter_name,'diagnosis',false);
                        else if(title != '')
                            success = add_encounter(encounter_name,'',false);

                        if(!success){
                            unsaved_change_found = true;
                            $('#encounter_'+encounter_name+'_title').focus();
                        }

                        description = $('#encounter_'+encounter_name+'_description').val().trim();
                        if(description != ''){
                            unsaved_change_found = true;
                            $('#encounter_'+encounter_name+'_description').css('border','solid 2px #f00');
                            $('#encounter_'+encounter_name+'_description').focus();
                        }
                    }

                    encounter_ready_to_be_saved = true;
                    save_encounter(null, true);
                    return unsaved_change_found;
                }

                $(window).bind('beforeunload', function() {
                    if(check_unsaved_work())
                        return false;
                });                

                function show_all_related_encounter(){
                    let encounters_list = ['chief_complaints', 'physical_finding', 'past_medical_history', 'hpi', 'diagnosis', 'medication', 'procedure', 'post_procedure_diagnosis', 'post_treatment'];
                    let viewer;
                    for(key in encounters_list){
                        viewer = document.getElementById('show_related_'+encounters_list[key]);
                        if(viewer != null)
                            show_collapse_related_encounter(viewer);
                    }
                }

                $(document).ready(function(){
                    show_all_related_encounter();
                });

                $(document).ready(function(){
                    show_hide_payed_services();
                });

                function flash_red_for_validation(objID, focus=true){
                    $('#'+objID).css('background-color', '#f00');
                    setTimeout("$('#"+objID+"').css('background-color', '#fff')", 300);
                    if(focus)
                        $('#'+objID).focus();
                }

                function show_edit_medical_history(){
                    $("#edit_medical_history").modal("show");
                }

                

                function void_prescription(prescription_group_id){
                    //alert(prescription_group_id);
                    if(!confirm('Are you sure you want to void this prescription?'))
                        return 0;
                    $.ajax({
                        method: 'post',
                        url: "<?php echo base_url(); ?>hospital/doctor/voidprescription",
                        data: {prescription_group_id: prescription_group_id},
                        success: function(result){
                            let r = JSON.parse(result);
                                if(r.status == "success"){
                                    location.reload();
                        }
                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            error_html = "Update is not made. <br><u>Error</u>:<br>" + errorThrown;
                        }
                    });
                }

                var number_of_line = <?= ($number_of_line ?? 1)-1?>;
                var lab_request_detail_data = {};
                function add_request_test_line()
                {
                    
                    
                    let request_test_note = document.getElementById("request_test_note").value;
                    let request_test_name = document.getElementById("request_test_name").value;
                    //alert(request_test_name.value);
                    if(!validate_add_lab_request_line())
                        return false; 

                    number_of_line++;


                    
                    let new_data = {
                        "request_test_note":request_test_note, 
                        "request_test_name":request_test_name
                    };
                    
                    lab_request_detail_data['line_'+number_of_line] = new_data;

                    let new_row_html = '<tr id="lab_request_detail_line_tr_'+number_of_line+'">'+
                                            '<td>'+new_data.request_test_name+'</td>'+
                                            '<td>'+new_data.request_test_note+'</td>'+
                                            '<td><a role="button" class="fa fa-minus-circle text-danger" onclick="remove_lab_request_line('+number_of_line+')"></a></td>'+
                                        '</tr>';
                    
                    let lab_request_detail_line = $('#lab_request_detail_line');
                    lab_request_detail_line.append(new_row_html);
                    //calculate_total();
                    clear_lab_request_line();
                }

                function clear_lab_request_line()
                {
                    
                    let request_test_note = $("#request_test_note");
                    let request_test_name = $("#request_test_name");
                    
                    request_test_name.val('');
                    request_test_note.val('');
                   
                    $("#request_test_note").removeAttr("style");
                    $("#request_test_name").removeAttr("style");
                   
                }
                function remove_lab_request_line(line_number)
                {
                    $('#lab_request_detail_line_tr_'+line_number).remove();
                    delete lab_request_detail_data['line_'+line_number];
                }
                function validate_add_lab_request_line(){
                    let request_test_name = $("#request_test_name");

                    if(request_test_name.val() === '')
                    {
                        request_test_name.css("borderColor","red");
                        request_test_name.focus();
                        return false;
                    }
                    
                    return true;
                }

                function save_external_laboratory_request()
                    {
                        if(!validate_save_lab_request())
                            return false
                        
                        let requested_company_name = $('#requested_company_name').val();
                        let request_type = $('#request_type').val();
                        let request_patient_name = $('#request_patient_name').val();
                        let request_patient_id = $('#request_patient_id').val();
                        let request_doctor_id = $('#request_doctor_id').val();
                        let request_registered_by_name = $('#request_registered_by_name').val();
                        let request_registered_by = $('#request_registered_by').val();
                        let request_visit_id = $('#request_visit_id').val();
                        let request_mobile = $('#request_mobile').val();
                        let request_remark = $('#request_remark');

                       
                
                        $.ajax({
                            url: '/hospital/doctor/labrequestsave',
                            data:{ 
                                    request_data: {
                                    requested_company_name: requested_company_name,
                                    request_type: request_type,request_patient_name: request_patient_name,
                                    request_patient_id: request_patient_id,request_doctor_id: request_doctor_id,
                                    request_registered_by_name: request_registered_by_name,request_registered_by: request_registered_by,
                                    request_visit_id: request_visit_id,request_mobile: request_mobile,
                                    lab_request_detail_data: lab_request_detail_data,
                                    request_remark: request_remark.val()
                            
                                },
                            },
                            method:'POST',
                            async: false,
                            dataType: 'json',
                            success:function(result){
                                request_remark.val('');
                                location = '/hospital/doctor/labrequestprint?elid='+result.value+'&id='+request_patient_id;
                            },
                            error:function(jqXHR,status,error){
                                alert(error);
                            }
                        });
                    }
                
                    function save_external_biopsy_request()
                    {
                        if(!validate_save_biopsy_request())
                            return false
                        
                        let bio_requested_company_name = $('#bio_requested_company_name').val();
                        let bio_request_type = $('#bio_request_type').val();
                        let bio_request_patient_name = $('#bio_request_patient_name').val();
                        let bio_request_patient_id = $('#bio_request_patient_id').val();
                        let bio_request_doctor_id = $('#bio_request_doctor_id').val();
                        let bio_request_doctor_name = $('#bio_request_doctor_name').val();
                        let bio_request_registered_by_name = $('#bio_request_registered_by_name').val();
                        let bio_request_registered_by = $('#bio_request_registered_by').val();
                        let bio_request_visit_id = $('#bio_request_visit_id').val();
                        let bio_request_mobile = $('#bio_request_mobile').val();
                        let bio_request_history = $('#bio_request_history').val();
                        let bio_request_physical_finding = $('#bio_request_physical_finding').val();
                        let bio_request_diagnosis = $('#bio_request_diagnosis').val();
                        let bio_request_imaging = $('#bio_request_imaging').val();
                        let bio_request_punch = $("#biopsy_type").val();
                        let bio_request_site = $('#bio_request_site').val();
                        let bio_request_anesthesia = $('#bio_request_anesthesia').val();
                        let bio_request_fixative = $('#bio_request_fixative').val();
                        let biopsy_size = $('#biopsy_size').val();
                        let bio_urgent = $('#bio_urgent').is(':checked') ? 1 : 0;

                       
                
                        $.ajax({
                            url: '/hospital/doctor/biopsyrequestsave',
                            data:{ 
                                    request_data: {
                                        bio_requested_company_name: bio_requested_company_name,
                                        bio_request_type: bio_request_type,
                                        bio_request_patient_name: bio_request_patient_name,
                                        bio_request_patient_id: bio_request_patient_id,
                                        bio_request_doctor_id: bio_request_doctor_id,
                                        bio_request_doctor_name: bio_request_doctor_name,
                                        bio_request_registered_by_name: bio_request_registered_by_name,
                                        bio_request_registered_by: bio_request_registered_by,
                                        bio_request_visit_id: bio_request_visit_id,
                                        bio_request_mobile: bio_request_mobile,
                                        bio_request_mobile: bio_request_mobile,
                                        bio_request_history:bio_request_history,
                                        bio_request_physical_finding:bio_request_physical_finding,
                                        bio_request_diagnosis:bio_request_diagnosis,
                                        bio_request_imaging:bio_request_imaging,
                                        biopsy_type:bio_request_punch,
                                        biopsy_size:biopsy_size,
                                        bio_request_site:bio_request_site,
                                        bio_request_anesthesia:bio_request_anesthesia,
                                        bio_request_fixative:bio_request_fixative,
                                        bio_urgent: bio_urgent
                            
                                },
                            },
                            method:'POST',
                            async: false,
                            dataType: 'json',
                            success:function(result){
                                //request_remark.val('');
                                location = '/hospital/doctor/biopsyrequestprint?ebid='+result.value+'&id='+bio_request_patient_id;
                            },
                            error:function(jqXHR,status,error){
                                alert(error);
                            }
                        });
                    }
                    
                    function save_referal_request()
                    {
                        if(!validate_save_referal_request())
                            return false
                        
                        let ref_requested_company_name = $('#ref_requested_company_name').val();
                        let ref_request_type = $('#ref_request_type').val();
                        let ref_request_patient_name = $('#ref_request_patient_name').val();
                        let ref_request_patient_id = $('#ref_request_patient_id').val();
                        let ref_request_doctor_id = $('#ref_request_doctor_id').val();
                        let ref_request_registered_by_name = $('#ref_request_registered_by_name').val();
                        let ref_request_registered_by = $('#ref_request_registered_by').val();
                        let ref_request_visit_id = $('#ref_request_visit_id').val();
                        let ref_request_mobile = $('#ref_request_mobile').val();
                        let ref_clinical_finding = $('#ref_clinical_finding').val();
                        let ref_diagnosis = $('#ref_diagnosis').val();
                        let ref_investigation_result = $('#ref_investigation_result').val();
                        let ref_px_given = $('#ref_px_given').val();
                        let ref_reason = $('#ref_reason').val();

                       
                
                        $.ajax({
                            url: '/hospital/doctor/referalrequestsave',
                            data:{ 
                                    request_data: {
                                        ref_requested_company_name: ref_requested_company_name,
                                        ref_request_type: ref_request_type,
                                        ref_request_patient_name: ref_request_patient_name,
                                        ref_request_patient_id: ref_request_patient_id,
                                        ref_request_doctor_id: ref_request_doctor_id,
                                        ref_request_registered_by_name: ref_request_registered_by_name,
                                        ref_request_registered_by: ref_request_registered_by,
                                        ref_request_visit_id: ref_request_visit_id,
                                        ref_request_mobile: ref_request_mobile,
                                        ref_clinical_finding: ref_clinical_finding,
                                        ref_diagnosis: ref_diagnosis,
                                        ref_investigation_result:ref_investigation_result,
                                        ref_px_given:ref_px_given,
                                        ref_reason:ref_reason
                            
                                },
                            },
                            method:'POST',
                            async: false,
                            dataType: 'json',
                            success:function(result){
                                //request_remark.val('');
                                location = '/hospital/doctor/referalrequestprint?rrid='+result.value+'&id='+ref_request_patient_id;
                            },
                            error:function(jqXHR,status,error){
                                alert(error);
                            }
                        });
                    }
                    
                    function validate_save_lab_request(){
                        let request_test_name = $('#request_test_name');

                        if(Object.keys(lab_request_detail_data).length == 0)
                        {
                            request_test_name.css("borderColor","red");
                            request_test_name.focus();
                            return false;
                        }


                        return true;
                    }
                    function validate_save_biopsy_request(){
                        let bio_requested_company_name = $('#bio_requested_company_name');

                        if(bio_requested_company_name.val() == "")
                        {
                            bio_requested_company_name.css("borderColor","red");
                            bio_requested_company_name.focus();
                            return false;
                        }

                        if($("#biopsy_type").val() == "")
                        {
                            $('#biopsy_type').css("borderColor","red");
                            $('#biopsy_type').focus();
                            return false;
                        }

                        if($("#bio_request_site").val().trim() == "")
                        {
                            $('#bio_request_site').css("borderColor","red");
                            $('#bio_request_site').focus();
                            return false;
                        }

                        if($("#bio_request_anesthesia").val() == "")
                        {
                            $('#bio_request_anesthesia').css("borderColor","red");
                            $('#bio_request_anesthesia').focus();
                            return false;
                        }

                        if($("#bio_request_fixative").val() == "")
                        {
                            $('#bio_request_fixative').css("borderColor","red");
                            $('#bio_request_fixative').focus();
                            return false;
                        }

                        if($("#biopsy_type").val().includes("Punch") && $('#biopsy_size').val().trim() == "") {
                            $('#biopsy_size').css("borderColor","red");
                            $('#biopsy_size').focus();
                            return false;
                        }

                        return true;
                    }
                    function validate_save_referal_request(){
                        let ref_requested_company_name = $('#ref_requested_company_name');
                        let ref_reason = $('#ref_reason');

                        if(ref_requested_company_name.val() == "")
                        {
                            ref_requested_company_name.css("borderColor","red");
                            ref_requested_company_name.focus();
                            return false;
                        }
                        if(ref_reason.val() == "")
                        {
                            ref_reason.css("borderColor","red");
                            ref_reason.focus();
                            return false;
                        }
                       return true;
                    }
                    
                    function show_confirm_pin_bio(){
                        if(!validate_save_biopsy_request()){
                            toastr['error']('Please Enter all required Fields!');
                            return false;
                        }
                        $('#confirmPinbio').modal('show');
                            $('#confirmPinbio').on('shown.bs.modal', function () {
                            $('#pin_enteredbio').focus();
                        })
                            $('#pin_enteredbio').focus();

                    }
                    function checkpinbio(){
                        var pin = $("#pin_enteredbio").val();
                        var user = $("#user_idbio").val();
                        $.ajax({
                            method: 'post',
                            url: "/hospital/ajax/checkpin",
                            data: {"pin_code":pin,'id':user},
                            dataType: 'json',
                            async: false,
                            success: function(result) { 
                                if(result.status == 'success'){
                                    //alert('dd');
                                    save_external_biopsy_request();
                                }
                                else
                                    toastr['error'](result.message);
                            },
                            error:function(jqXHR,status,error){
                                    toastr['error'](error);
                            }
                        });
                    }
                    function show_confirm_pin_lab(){
                        if(!validate_save_lab_request()){
                            toastr['error']('Please Enter all required Fields!');
                            return false;
                        }
                        $('#confirmPinlab').modal('show');
                            $('#confirmPinlab').on('shown.bs.modal', function () {
                            $('#pin_enteredlab').focus();
                        })
                            $('#pin_enteredlab').focus();

                    }
                    function checkpinlab(){
                        var pin = $("#pin_enteredlab").val();
                        var user = $("#user_idlab").val();
                        $.ajax({
                            method: 'post',
                            url: "/hospital/ajax/checkpin",
                            data: {"pin_code":pin,'id':user},
                            dataType: 'json',
                            async: false,
                            success: function(result) { 
                                if(result.status == 'success'){
                                    //alert('dd');
                                    save_external_laboratory_request();
                                }
                                else
                                    toastr['error'](result.message);
                            },
                            error:function(jqXHR,status,error){
                                    toastr['error'](error);
                            }
                        });
                    }
                    function show_confirm_pin_ref(){
                        if(!validate_save_referal_request()){
                            toastr['error']('Please Enter all required Fields!');
                            return false;
                        }
                        $('#confirmPinref').modal('show');
                            $('#confirmPinref').on('shown.bs.modal', function () {
                            $('#pin_enteredref').focus();
                        })
                            $('#pin_enteredref').focus();

                    }
                    function checkpinref(){
                        var pin = $("#pin_enteredref").val();
                        var user = $("#user_idref").val();
                        $.ajax({
                            method: 'post',
                            url: "/hospital/ajax/checkpin",
                            data: {"pin_code":pin,'id':user},
                            dataType: 'json',
                            async: false,
                            success: function(result) { 
                                if(result.status == 'success'){
                                    //alert('dd');
                                    save_referal_request();
                                }
                                else
                                    toastr['error'](result.message);
                            },
                            error:function(jqXHR,status,error){
                                    toastr['error'](error);
                            }
                        });
                    }

                    function toggleSizeField() {
                        if ($('#biopsy_type').val().includes('Punch')) {
                            $('#size_container').show();
                            $('#size_header').show();
                        } else {
                            $('#size_container').hide();
                            $('#size_header').hide();
                        }
                    }

                    $(document).ready(function() {
                        $('#biopsy_type').change(toggleSizeField);
                        toggleSizeField();
                    });

            </script>

            </script>
            
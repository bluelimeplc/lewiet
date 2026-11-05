<?php
    helper('form');
    $vital_signs = $this->data['vitalSigns'];
    
    //$GLOBALS['tagsinput_insert_data'] = '';
   

    $visitEncounterTemp = $visitEncounter;
    unset($visitEncounterTemp['id']);
    unset($visitEncounterTemp['visit_id']);
    unset($visitEncounterTemp['registered_date']);

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
 

    $related_chief_complaints = "";
    $related_past_medical_history = "";
    $related_hpi = "";
    $related_diagnosis = "";
    $related_treatment_plans = "";
    $related_medication = "";
    $related_procedure = "";
    $related_post_procedure_diagnosis = "";
    
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
        $related_hpi .= get_related_visits_encounter_table($encounter['hpi'],$encounter['registered_date']);
        $related_past_medical_history .= get_related_visits_encounter_table($encounter['past_medical_history'],$encounter['registered_date']);   
        $related_diagnosis .= get_related_visits_encounter_table($encounter['diagnosis'],$encounter['registered_date']);   
        $related_medication .= get_related_visits_encounter_table($encounter['medication'],$encounter['registered_date']);   
        $related_procedure .= get_related_visits_encounter_table($encounter['procedure'],$encounter['registered_date'],true);   
        $related_post_procedure_diagnosis .= get_related_visits_encounter_table($encounter['post_procedure_diagnosis'],$encounter['registered_date']);

        //$related_treatment_plan
    }

    if($related_chief_complaints != '')
        $related_chief_complaints = '<div><div><span>...</span><i role="button" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down" style="float: right; margin-right: 10px;"></i></div><div class="hide"><table class="table">'.$related_chief_complaints.'</table></div></div>';

    if($related_hpi != '')
        $related_hpi = '<div><div><span>...</span><i id="test02" role="button" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down" style="float: right; margin-right: 10px;"></i></div><div class="hide"><table class="table">'.$related_hpi.'</table></div></div>';

    if($related_past_medical_history != '')
        $related_past_medical_history = '<div><div><span>...</span><i id="test02" role="button" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down" style="float: right; margin-right: 10px;"></i></div><div class="hide"><table class="table">'.$related_past_medical_history.'</table></div></div>';
          
    if($related_diagnosis != '')
        $related_diagnosis = '<div><div><span>...</span><i id="test02" role="button" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down" style="float: right; margin-right: 10px;"></i></div><div class="hide"><table class="table">'.$related_diagnosis.'</table></div></div>';        
    
    if($related_medication != '')
        $related_medication = '<div><div><span>...</span><i id="test02" role="button" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down" style="float: right; margin-right: 10px;"></i></div><div class="hide"><table class="table">'.$related_treatment.'</table></div></div>';
    
    if($related_procedure != '')
        $related_procedure = '<div><div><span>...</span><i id="test02" role="button" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down" style="float: right; margin-right: 10px;"></i></div><div class="hide"><table class="table">'.$related_procedure.'</table></div></div>';
    
    if($related_post_procedure_diagnosis != '')
        $related_post_procedure_diagnosis = '<div><div><span>...</span><i id="test02" role="button" onclick="show_collapse_related_encounter(this)" class="fa fa-caret-down" style="float: right; margin-right: 10px;"></i></div><div class="hide"><table class="table">'.$related_post_procedure_diagnosis.'</table></div></div>';
    
        

    $related_visits_paid_service_html = "";
    $related_visits_paid_service_laboratory = "";
    $related_visits_paid_service_procedure = "";
    $there_is_unpaid_service = false;
    foreach($related_visits_paid_service as $paid_service){
        if(($paid_service['service_type'] != 'Laboratory') && ($paid_service['service_type'] != 'Procedure'))
            continue;
        $class = 'paid';
        if(intval($paid_service['service_status']) == \BluelimeConfiguration::SERVICE_STATUS_DELAY){
            $class = 'unpaid hidden';
            $there_is_unpaid_service = true;
        }
        
        if($paid_service['service_type'] == BluelimeConfiguration::SERVICE_TYPE::LABORATORY){
            if($related_visits_paid_service_laboratory == '')
                $related_visits_paid_service_laboratory = '<tr><th colspan="2" data-toggle="collapse" href="#lab_payed_services" aria-expanded="true" aria-controls="lab_payed_services"><b>Laboratory</b></th></tr><tbody class="collapse" id="lab_payed_services">';
        
            $related_visits_paid_service_laboratory .= '
                                            <tr class="'.$class.'">
                                                <td><span class="fa fa-circle payment-flag"></span> <span class="service_name">'.$paid_service['service_name'].'</span></td>
                                                <td>'.$paid_service['bill_quantity'].'</td>
                                            </tr>';
        }
        if($paid_service['service_type'] == BluelimeConfiguration::SERVICE_TYPE::PROCEDURE){
            if($related_visits_paid_service_procedure == '')
                $related_visits_paid_service_procedure = '<tr><td colspan="2"><b>Procedure</b></td></tr>';
            $related_visits_paid_service_procedure .= '
                                            <tr class="'.$class.'">
                                                <td><span class="fa fa-circle payment-flag"></span> <span class="service_name">'.$paid_service['service_name'].'</span></td>
                                                <td>'.$paid_service['bill_quantity'].'</td>
                                            </tr>';
        }
    }

    $related_visits_paid_service_laboratory .= '</tbody>'; 
    $related_visits_paid_service_html = $related_visits_paid_service_procedure . $related_visits_paid_service_laboratory;

    if($related_visits_paid_service_html != ''){
        $related_visits_paid_service_html = '
                                        <table class="table table-bordered dental-payed-services-table" >
                                            <thead>
                                                <tr>
                                                    <th>Service name</th><th width="20">Qty</th>
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
                        
                        <a class="btn btn-danger" style="float: right; margin-top: -10px;" onclick="save_encounter('<?= base_url() ?>hospital/doctor/closevisit?visit_id=<?= $visit_id ?>')">Discharge and Close Visit</a>
                        
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
                                       
                                        <div class="col-sm-7 dental-menu-icons-01" style="color:red; float:left">
                                            <a href="/hospital/doctor/visit?visit_id=<?php echo $visit_id ?>" title="Encounter" data-toggle="tooltip" data-html="true"><img src="/img/encounter.png" width="75px"></a>
                                            <a href="/hospital/doctor/newappointment?id=<?php echo $mrn_no ?>&vid=<?php echo $visit_id ?>" title="Appointment" data-toggle="tooltip" data-html="true"><img src="/img/appointment.png" width="50px"></a>
                                            <a href="/hospital/doctor/newprescription?&visit_id=<?php echo $visit_id ?>" title="Prescription" data-toggle="tooltip" data-html="true"><img src="/img/prescription.png" width="50px"></a>
                                            <a href="/hospital/doctor/newmedicalcertificate?id=<?php echo $mrn_no ?>&vid=<?php echo $visit_id ?>" title="Medical Certificate" data-toggle="tooltip" data-html="true"><img src="/img/certificate.png" width="50px"></a>
                                            <a href="/hospital/doctor/newprogresspicture?id=<?php echo $mrn_no ?>&vid=<?php echo $visit_id ?>" title="Progress Picture" data-toggle="tooltip" data-html="true"><img src="/img/progresspicture.png" width="50px"></a>
                                            <a onclick="show_transfer_modal()"><img src="/img/transfer.png" width="60px"  title="Transfer" data-toggle="tooltip" data-html="true"></a>
                                            
                                            

                                        <!--div ><a href="/hospital/reception/newappointment?id=<?php echo $mrn_no ?>" class="btn btn-success pull-right">Make Appointment</a></div-->
                                    </div>
                                                                        
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Patient Info -->
                    <div class="col-sm-6 col-md-6">
                        <div class="panel panel-bd">
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
                                            <td width="120">Card Number</td>
                                            <td><?= "MRN-".sprintf('%05d', $patient['id']); ?></td>
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
                                                <div class="col-lg-12" >
                                                    <img id="scream" src="/img/hospital/<?= ($patient['sex']=='Male')?'man':'woman' ?>_skin.svg" class="image" style="width:100%;"/>
                                                    <canvas id="cv1" width="245" height="355" style="position: absolute; left:0px; top:0px;"> test </canvas>
                                                    <script>
                                                        var darwing = "";
                                                        var el1 = document.getElementById('cv1');
                                                        var ctx1 = el1.getContext('2d');
                                                        ctx1.lineJoin = "round"
                                                        ctx1.strokeStyle = "#0f05"; 
                                                        //ctx1.globalAlpha = ""; 
                                                        ctx1.lineWidth = 10; 
                                                        ctx1.globalCompositeOperation = "source-over";
                                                        var isDrawing, lastPoint;
                                                        el1.onmousedown = function(e) {
                                                                isDrawing = true;
                                                                let top = el1.getBoundingClientRect().top;
                                                                let left = el1.getBoundingClientRect().left;
                                                                lastPoint = { x: e.clientX-left, y: e.clientY-top };
                                                        };  
                                                        el1.onmousemove = function(e) {
                                                            if (!isDrawing) return;
                                                            let top = el1.getBoundingClientRect().top;
                                                            let left = el1.getBoundingClientRect().left;
                                                            var currentPoint = { x: e.clientX-left, y: e.clientY-top };
                                                            let xDistance = Math.abs(lastPoint.x-currentPoint.x);
                                                            let yDistance = Math.abs(lastPoint.y-currentPoint.y);
                                                            if(xDistance+yDistance <5) return;
                                                            ctx1.beginPath();
                                                            ctx1.moveTo(lastPoint.x, lastPoint.y);
                                                            ctx1.lineTo(currentPoint.x, currentPoint.y);
                                                            ctx1.closePath();
                                                            ctx1.stroke();
                                                            lastPoint = currentPoint;
                                                            darwing += "["+currentPoint.x+","+currentPoint.y+"],";
                                                            console.log([currentPoint.x,currentPoint.y]);
                                                        };
                                                        el1.onmouseup = function() {
                                                            isDrawing = false;
                                                        };

                                                        function daraw_from_array(arr){
                                                            let lastPoint2 = arr[0];
                                                            for (const key in arr) {  
                                                                if (Object.prototype.hasOwnProperty.call(arr, key)) {
                                                                    const xy = arr[key];
                                                                    if((Math.abs(xy[0]-lastPoint2[0]) > 20) || (Math.abs(xy[1]-lastPoint2[1]) > 20))
                                                                        lastPoint2 = arr[key];

                                                                    ctx1.beginPath();
                                                                    ctx1.moveTo(lastPoint2[0], lastPoint2[1]);
                                                                    ctx1.lineTo(xy[0], xy[1]);
                                                                    ctx1.closePath();
                                                                    ctx1.stroke();
                                                                    lastPoint2 = arr[key];
                                                                }
                                                            }
                                                        }

                                                        // const image = document.getElementById("scream");
                                                        // image.addEventListener("load", (e) => {
                                                        //     ctx1.drawImage(image, 10, 10);
                                                        // });
                                                    </script>
                                                </div>
                                                Remark
                                                <textarea class="form-control" id="follow_up_remark" onchange="save_encounter(null,true)"><?= $follow_up_remark ?></textarea>
                                            </div>
                                            <div class="col-lg-9">
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
                                                <div class="col-lg-6">
                                                    <div class="dental-form-box">
                                                        <div class="box-title">
                                                            Past Medical History
                                                            <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new Past medical history" onclick="show_add_new_modal('Add New Past Medical History', 'past_medical_history', auto_compelete['past_medical_history'])"></i>
                                                        </div>
                                                        <?= $related_hpi; ?>
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
                                                            Diagnosis
                                                            <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new Diagnosis" onclick="show_add_new_modal('Add New Diagnosis', 'diagnosis', auto_compelete['diagnosis'])"></i>
                                                        </div>
                                                        <?= $related_hpi; ?>
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
                                                        <div class="box-title">
                                                            Treatment Plan
                                                        </div>
                                                        <?= $related_hpi; ?>
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
                                        </div>
                                        <hr>
                                        
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                
                                                <div class="dental-form-box">
                                                    <div class="box-title">
                                                        Paid services&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <?php
                                                            if($there_is_unpaid_service)
                                                                echo '<a id="dental_unpayed_services_icon" role="button" class="fa fa-eye unhide-icon" title="Show unpaid" onclick="show_hide_payed_services()"> <small>Show unpaid</small></a><br>';
                                                        ?>
                                                    </div>
                                                    <div id="dental_payed_services">
                                                        
                                                        <?= $related_visits_paid_service_html ?>
                                                    
                                                    </div>
                                                    <script>
                                                        function show_hide_payed_services(){
                                                            $("#dental_payed_services").find('.unpaid').toggleClass('hidden');
                                                            $("#dental_unpayed_services_icon").toggleClass("fa-eye fa-eye-slash");
                                                        }
                                                    </script>
                                                    
                                                    
                                                </div>
                                            </div>
                                            <div class="form-group col-lg-6">
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
                                            <div class="form-group col-lg-6">
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
                                            <div class="form-group col-lg-6">
                                                <div class="dental-form-box">
                                                    <div class="box-title">
                                                        Post Procedure Diagnosis
                                                        <i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new Diagnosis" onclick="show_add_new_modal('Add New Diagnosis', 'diagnosis', auto_compelete['diagnosis'])"></i>
                                                    </div>
                                                    <?= $related_hpi; ?>
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
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="dental-form-box">
                                                    <div class="box-title dental-bg-primary">Orders
                                                    </div>
                                                    <i class="glyphicon glyphicon-edit dental-add-icon" title="Edit order options window" data-toggle="modal" data-target="#orderOptionModal" ></i>
                                                    <div id="order_option_list">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="dental-form-box">
                                                    <div class="box-title">Laboratory Results
                                                    </div>
                                                    <div id="lab_results">
                                                        <?php
                                                            foreach($laboratory_results_compiled as $service_result){
                                                                echo '<table class="table table-bordered">';
                                                                echo '<tr><th colspan="2">'. $service_result['service_name'] .'</th></tr>';
                                                                
                                                                foreach($service_result['result'] as $result){
                                                                    echo '<tr><td>'.$result['attribute_name'].'</td><td>'.$result['result'].'</td><td>'.$result['result_flag'].'</td></tr>';
                                                                }
                                                                echo '</table>';
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <div style="text-align: right;">
                                            <input type="hidden" name="visit_id" value="<?= $visit_id ?>">
                                            <!--input style="" type="submit" value="Save" class="btn btn-success" /-->
                                            <a onclick="save_encounter()" role="link" class="btn btn-success">Save</a>
                                            <br>
                                            <div id="save_encounter_flash" class="alert alert-danger hide" role="alert" style="margin: 5px; display:inline-block; min-width: 300px; text-align:left;">
                                                <span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Vital Sign -->
                        <div class="col-md-12">
                            <div class="panel panel-bd lobidisable">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4>Vital Sign</h4>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Blood Pressure</th>
                                                <th>Weight</th>
                                                <th>Temperature</th>
                                                <th>Pulse rate</th>
                                                <th>Respiration rate</th>
                                                <th>Note</th>
                                                <th>Registered date</th>
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
                                                        <td>'.substr($vital_sign['registered_date'],0,10).'</td>
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
                        
                        <div class="col-sm-12 col-md-12">
                            <div class="panel panel-bd ">
                                <div class="panel-heading">
                                            <div class="panel-title">
                                                <h4><a class="" data-toggle="collapse" href="#collapseResult" role="button" aria-expanded="false" aria-controls="collapseResult">
                                                        Laboratory Results
                                                    </a> 
                                                </h4>
                                            </div>
                                </div>
                                <div class="panel-body collapse" id="collapseResult">
                                
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 ">
                            <div class="panel panel-bd ">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4><a class="" data-toggle="collapse" href="#collapseAppointment" role="button" aria-expanded="false" aria-controls="collapseAppointment">
                            Appointment History
                        </a> </h4>
                                    </div>
                                </div>
                                <div class="panel-body collapse" id="collapseAppointment">
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
                        <div class="col-sm-6 col-md-6">
                            <div class="panel panel-bd">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4><a class="" data-toggle="collapse" href="#collapsePrescription" role="button" aria-expanded="false" aria-controls="collapsePrescription">
                            Prescription History
                        </a> </h4>
                                    </div>
                                </div>
                                <div class="panel-body collapse" id="collapsePrescription">
                                <?php
                                            foreach($prescriptions as $prescribed):  
                                        ?>
                                <table class="table table-bordered " style="color:black;" <?= (@$prescribed['is_custom']==0)?"":"hidden"?>>
                                    <thead>
                                        <tr>
                                            <th colspan="5"><?=date('d-m-Y',strtotime($prescribed['registered_date'])).' '.$prescribed['registered_by_name']?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach($prescription_detail as $detail):
                                               
                                        ?>       
                                              <tr>
                                                <td><?=$detail['drug_name'] ?></td>
                                                <td><?=$detail['dose'] ?></td>
                                                <td><?=$detail['route'] ?></td>
                                                <td><?=$detail['direction'] ?></td>
                                                <td><?=$detail['number_of_days'] ?></td>
                                              </tr>      
                                        <?php
                                            endforeach;
                                        ?>    
                                       
                                    </tbody>
                                </table>
                                <?php
                                            endforeach;
                                        ?> 
                                <table class="table table-striped table-hover table-bordered " <?= (@$prescribed['is_custom']==1)?"":"hidden"?>>
                                    <thead>
                                        <tr>
                                        <th colspan="2"><?=date('d-m-Y',strtotime(@$prescribed['registered_date'])).' '.@$prescribed['registered_by_name']?></th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach($prescription_detail as $detail):
                                        ?>       
                                              <tr>
                                                <td><?=$detail['drug_name'] ?></td>
                                                <td><?=$detail['custom_direction'] ?></td>
                                              </tr>      
                                        <?php
                                            endforeach;
                                        ?>    
                                       
                                    </tbody>
                                </table> 
                                </div>
                            </div>
                        </div>
                        <!-- Encounter history -->
                        <div class="col-md-12">
                            <div class="panel panel-bd">
                                <div class="panel-heading">
                                    <h4>Encounter History</h4>
                                </div>
                                <div class="panel-body">
                                    <?php
                                        function print_encounter_history_header($follow_up_id, $visit_id, $date ){
                                            echo '
                                            <div class="panel panel-bd panel-primary">
                                                <div class="panel-heading">
                                                    <div style="float:right"><a href="/hospital/doctor/join_visit_in_follow_up?follow_up_id='.$follow_up_id.'&visit_id='.$visit_id.'" class="text-white">Link to this follow up <i class="fa fa-link"></i></a></div>
                                                    <h4>'.substr($date,0,10).'</a></h4>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="col-lg-12">
                                                    ';
                                        }

                                        function print_encounter_history_footer(){
                                            echo '
                                                    </div>
                                                </div>
                                            </div>';
                                        }

                                        function print_encounter_history($encounter_title, $encounters, $is_taginputs = false, $class = 'col-lg-5'){
                                            echo '
                                                    <div class="dental-encounter-history-container '.$class.'">
                                                        <b class="encounter-title">'.$encounter_title.'</b>
                                                    ';
                                            
                                            $table_opened = false;
                                            foreach($encounters as $encounter){
                                                foreach($encounter as $row) {
                                                    $title = $row['title'] ?? '';
                                                    if($is_taginputs){
                                                        $title = '';
                                                        $tags = \BluelimeSystem::convert_to_array($row['title']);
                                                        foreach($tags as $tag){
                                                            if(trim($tag['service_name']) != '')
                                                                $title .= '<span class="label bg-primary dental-tags-lable">'.$tag['service_name'].'</span>';
                                                                $quantity = $row['quantity'] ?? '';
                                                                if($quantity != '')
                                                                    $title .= '<span class="label bg-primary dental-tags-lable">'.$quantity.'</span>';
                                                        }
                                                    }
                                                    $description = $row['description'] ?? '';

                                                    if((trim($title) != '' ) || (trim($description) != '')){
                                                        if(!$table_opened){
                                                            echo '<table class="table">';
                                                            $table_opened = true;
                                                        }

                                                        echo '
                                                        <tr>
                                                            <td>'. $title .'<div style="padding-left:20px;">'. $description .'</div></td>
                                                        </tr>
                                                        ';
                                                    }
                                                }
                                            }
                                            
                                            if($table_opened){
                                                echo '</table>';
                                            }
                                        
                                            echo '</div>';
                                            

                                        }
                                        

                                        
                                        foreach($follow_up_list as $follow_up){

                                            $first_encounter = true;
                                            $chief_complaints = [];
                                            $past_medical_history = [];
                                            $hpi = [];
                                            $examination_extra_oral = [];
                                            $diagnosis = [];
                                            $medication = [];
                                            $procedure = [];
                                            $registered_date = [];
                                            $post_procedure_diagnosis = [];

                                             
                                            foreach($encounterHistory as $encounter){
                                                if($encounter['follow_up_id'] == $follow_up['id']){

                                                    if($first_encounter){
                                                        print_encounter_history_header($follow_up['id'], $visit_id, $encounter['registered_date']);
                                                        $first_encounter = false;
                                                    }

                                                    array_push($chief_complaints, $encounter['chief_complaints']);
                                                    array_push($past_medical_history, $encounter['past_medical_history']);
                                                    array_push($hpi, $encounter['hpi']);
                                                    array_push($diagnosis, $encounter['diagnosis']);
                                                    array_push($medication, $encounter['medication']);
                                                    array_push($procedure, $encounter['procedure']);
                                                    array_push($post_procedure_diagnosis, $encounter['post_procedure_diagnosis']);
                                                }
                                            }
                                            if($first_encounter)
                                                print_encounter_history_header($follow_up['id'], $visit_id, $follow_up['date_created']);
                                           
                                            echo '<div class="row">';
                                                print_encounter_history('Treatment Plans', []);//$follow_up['treatment_plans']);
                                            echo '</div>';
                                            echo '<div class="row">';
                                                print_encounter_history('Chief Complaints',$chief_complaints);
                                                print_encounter_history('HPI',$hpi);
                                            echo '</div>';
                                            echo '<div class="row">';
                                                print_encounter_history('Past Medical History',$past_medical_history);
                                            echo '</div>';
                                            echo '<div class="row">';
                                                print_encounter_history('Diagnosis',$diagnosis);
                                                print_encounter_history('Medication',$medication);
                                            echo '</div>';
                                            
                                            echo '<div class="row">';
                                                print_encounter_history('Procedure',$procedure,true);
                                                print_encounter_history('Post Procedure Diagnosis',$post_procedure_diagnosis);
                                            echo '</div>';

                                            print_encounter_history_footer();

                                        }
                                    ?>
                                </div>
                            </div>
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

                    <!-- Order Options-->
                    <div class="modal fade" id="orderOptionModal" data-keyboard="false" data-backdrop="static" role="dialog" >
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header dental-bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Create Orders</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <!-- Nav tabs -->
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item active">
                                                    <a class="nav-link active" data-toggle="tab" href="#orders">Orders</a>
                                                </li>
                                            </ul>

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
                        </div>
                    </div>
                </div>
            </div>
            <?= view('Include/_add_new_general') ?>
            <script>
                <?php $encounter_data_str = \BluelimeSystem::convert_array_to_string($visit_encounter_id_reseted); ?>
                var encounter_data = <?= ($encounter_data_str=='[]')?'{}':$encounter_data_str ?>;
                
                var follow_up_data =  {'remark':"<?= str_replace("\n",'\n', addslashes($follow_up_remark)) ?>",'selected_skin': [<?= addslashes($follow_up_selected_skin) ?>]};
                daraw_from_array(follow_up_data.selected_skin);
                darwing = '<?= trim($follow_up_selected_skin)==''?'':$follow_up_selected_skin.',' ?>';
                //var encounter_id_generator = {"chief_complaints":Object.keys(encounter_data['chief_complaints']).length, "hpi":Object.keys(encounter_data['hpi']).length, "past_medical_history":Object.keys(encounter_data['past_medical_history']).length, "past_dental_medical_history":Object.keys(encounter_data['past_dental_medical_history']).length, "examination_extra_oral":Object.keys(encounter_data['examination_extra_oral']).length, "examination_intra_oral":Object.keys(encounter_data['examination_intra_oral']).length,"diagnosis":Object.keys(encounter_data['diagnosis']).length,"treatment":Object.keys(encounter_data['treatment']).length,"procedure":Object.keys(encounter_data['procedure']).length,"post_procedure_diagnosis":Object.keys(encounter_data['post_procedure_diagnosis']).length};
                
                function add_encounter(encounter_name, reference_encounter_name = ''){
                    let title = $('#encounter_'+encounter_name+'_title').val();
                    let description = $('#encounter_'+encounter_name+'_description').val();

                    let encounter_name_temp = encounter_name;
                    if(reference_encounter_name != '')
                        encounter_name =reference_encounter_name;

                    if(auto_compelete[encounter_name] == undefined)
                        ;
                    else if((title.trim() == '') || (!auto_compelete[encounter_name].includes(title.trim()))){
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
                    clear_encounter_entry(encounter_name);
                    save_encounter(null,true);
                }

                function remove_encounter(encounter_name, index){
                    delete encounter_data[encounter_name][encounter_name + "_" + index];
                    $('#encounter_'+encounter_name+'_'+index).remove();
                    save_encounter(null,true);
                }

                function clear_encounter_entry(encounter_name){
                    $('#encounter_'+encounter_name+'_title').val('');
                    $('#encounter_'+encounter_name+'_description').val('');
                    $('#encounter_'+encounter_name+'_title').focus();
                }

                function show_collapse_related_encounter(obj){
                    obj.classList.toggle('fa-caret-up');
                    obj.classList.toggle('fa-caret-down');
                    obj.parentElement.parentElement.childNodes[1].classList.toggle('hide');
                    obj.parentElement.parentElement.childNodes[0].childNodes[0].classList.toggle('hide');
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
                    let new_service = {service_id: parseInt(service_id), service_name: service_name, quantity: parseFloat(quantity_val)};

                    if(order_option[option_key] === undefined)
                        order_option[option_key] = {};

                    service_key = 'service_' + service_id;
                    if(service_id < 0)
                        service_key = 'service__' + (-1*service_id);

                    order_option[option_key][service_key] = new_service;

                    let new_service_option_row =  '<tr id="order_option_service_' + option_id + '_' + service_id + '">'
                                                +'<td>' + service_name + '</td>'
                                                +'<td>'
                                                +'  <div class="input-group">'
                                                +'      <span onclick="decrement_order_quantity(this, \''+option_key+'\', \''+service_key+'\')" class="input-group-addon" role="button" style="padding:6px 5px !important;"><a class="fa fa-minus"></a></span>'
                                                +'      <input type="text" onchange="update_order_quantity(this, \''+option_key+'\', \''+service_key+'\')" class="form-control text-center quantity" value="' + quantity_val + '" style="padding:6px 4px !important;">'
                                                +'      <span onclick="increment_order_quantity(this, \''+option_key+'\', \''+service_key+'\')" class="input-group-addon" role="button" style="padding:6px 5px !important;"><a class="fa fa-plus"></a></span>'
                                                +'  </div>'
                                                +'</td>'
                                                +'<td><button class="btn btn-danger" onclick="remove_service_from_order_option(' + option_id + ', ' + service_id + ');" type="button"><i class="fa fa-trash" ></i></button></td>'
                                                +'</tr>';
                    let order_option_service_list = $('#order_option_service_list_' + option_id);

                    order_option_service_list.append(new_service_option_row);

                    $("#order_input").val('');
                    $("#order_option_quantity_" + option_id).val(1);
                    $("#order_input").focus();
                    
                    return 1;
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

                var encounter_ready_to_be_saved = false;
                function save_encounter(redirect_to = null, save_only = false){
                    
                    /*
                    if(!encounter_ready_to_be_saved)
                        return 0;
                    */

                    let visit_id = <?= $visit_id ?>;
                    let patient_id = <?= $patient['id'] ?>;
                    let patient_name = "<?= $visit['patient_name'] ?>";
                    let mobile = "<?= $patient['mobile'] ?>";
                    let company_id = "<?= $patient['company_id'] ?>";
                    let company_name = "<?= $patient['company_name'] ?>";
                    //let next_appointment_reason = $('#next_appointment_reason').val();
                    let follow_up_remark = $('#follow_up_remark').val();
                    
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
                        "selected_skin": darwing.substr(0,darwing.length-1) //Remove the last comma
                        //"next_appointment_reason": next_appointment_reason
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

            </script>
            <?= view('Include/_autocomplete_service') ?>
            <?= view('Include/_autocomplete_single_string') ?>
            <script>
                function set_focus_after_selection(args){
                    $('#'+args[0]).focus();
                }
                var services = <?= BluelimeSystem::convert_array_to_string($service_list) ?>;
                selected_service_index['order'] = -1;
                var autocomplete_order_option = {
                    input:document.getElementById("order_input"),
                    services:services,
                    selected_index:'order',
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
                    }
                };
                autocomplete_single_string_by_options(autocomplete_single_string_options['chief_complaints']);
                autocomplete_single_string_by_options(autocomplete_single_string_options['hpi']);
                autocomplete_single_string_by_options(autocomplete_single_string_options['diagnosis']);
                autocomplete_single_string_by_options(autocomplete_single_string_options['past_medical_history']);
                autocomplete_single_string_by_options(autocomplete_single_string_options['post_procedure_diagnosis']);

                

                /*
                selected_item_index['comp_selected_item_index'] = -1;
                autocomplete_item(document.getElementById("encounter_chief_complaints_title"), items, 'comp_selected_item_index',set_focus_after_selection,['encounter_chief_complaints_title']);
                function set_focus_after_selection(args){
                    $('#'+args[0]).focus();
                }
                */

                $(window).bind('beforeunload', function() {
                    save_encounter(null, true);
                });
            </script>
            
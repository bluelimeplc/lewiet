<?php
    $visit_id = $visit_id ?? -1;
    $patient_name = $this->data['patient_name']??'';
?>
<div class="panel panel-bd">
    <div class="panel-heading">
        <h4>Encounter History <a style="cursor:pointer;" id="downloade" download="<?=$patient_name?>_encounter_history.png" href="" onclick="download_imge(this);"><i class="fa fa-download <?=($patient_name != "")?"":"hidden"?>"></i></a></h4>
    </div>
    <div class="panel-body"  id="for_telegrame">
        <div class="row">
            <?php
                function print_encounter_history_header($follow_up_id, $visit_id, $date,$doctor ){
                    echo '
                    <div class="col-lg-4">
                        <div class="panel panel-bd panel-primary">
                            <div class="panel-heading">
                                <div style="float:right" class="'.(intval($visit_id)<= 0?'hidden':'').'"><a href="/hospital/doctor/join_visit_in_follow_up?follow_up_id='.$follow_up_id.'&visit_id='.$visit_id.'" class="text-white"   title="Link visit to follow up" data-toggle="tooltip" data-html="true">Link <i class="fa fa-link"></i></a></div>
                                <h5>'.substr($date,0,10).'</a> ('.$doctor.')</h5>
                            </div>
                            <div class="panel-body">
                                <div class="col-lg-12">
                                ';
                }

                function print_encounter_history_footer(){
                    echo '
                                </div>
                            </div>
                        </div>
                    </div>';
                }

                function print_encounter_history($encounter_title, $encounters, $is_taginputs = false, $class = ''){
                    
                    echo '
                            <div class="dental-encounter-history-container '.$class.'">
                                <b class="encounter-title">'.$encounter_title.'</b>
                            ';
                    
                    $table_opened = false;
                    foreach($encounters as $encounter){
                        foreach($encounter as $row) {
                            $title = $row['title'] ?? '';
                            $description = $row['description'] ?? '';

                            if((trim($title) != '' ) || (trim($description) != '')){
                                if(!$table_opened){
                                    echo '<table class="table table-condensed">';
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

                function print_order_history($order_history, $class = ''){
                    
                    echo '
                            <div class="dental-encounter-history-container '.$class.'">
                                <b class="encounter-title">Orders</b>
                            ';
                    
                    $table_opened = false;
                    foreach($order_history as $order){
                        if(!$table_opened){
                            echo '<table class="table table-condensed">';
                            $table_opened = true;
                        }

                        echo '
                        <tr>
                            <td width="2" title="'.($order['payment_id']?($order['result_registered']?'Paid. Result registerd.':'Paid . Result not registerd.'):'Not paid').'"><i class="fa fa-circle color-'.($order['payment_id']?($order['result_registered']?'green':'yellow'):'red').'"></i></td><td>'. ($order['service_detail']['service_name']??'') .'</td>
                            <td>'.(!$order['payment_id']?'<s>':'').number_format($order['bill_detail']['bill_quantity']*$order['bill_detail']['bill_amount']).(!$order['payment_id']?'</s>':'').'</td>
                        </tr>
                        ';
                    }
                    
                    if($table_opened){
                        echo '</table>';
                    }
                    echo '</div>';
                }
                
                foreach($history_follow_up_list as $follow_up){

                    $first_encounter = true;
                    $treatment_plans = [];
                    $chief_complaints = [];
                    $physical_finding = [];
                    $past_medical_history = [];
                    $hpi = [];
                    $examination_extra_oral = [];
                    $diagnosis = [];
                    $medication = [];
                    $procedure = [];
                    $registered_date = [];
                    $post_procedure_diagnosis = [];
                    $post_treatment = [];

                    //array_push($treatment_plans, $follow_up['treatment_plans']);
                    $encounterHistory = $follow_up['encounter_history'];
                    foreach($encounterHistory as $encounter){
                        // if($first_encounter){
                        //     print_encounter_history_header($follow_up['id'], $visit_id, $encounter['registered_date']);
                        //     $first_encounter = false;
                        // }

                        array_push($chief_complaints, $encounter['chief_complaints']);
                        array_push($physical_finding, $encounter['physical_finding']);
                        array_push($past_medical_history, $encounter['past_medical_history']);
                        array_push($hpi, $encounter['hpi']);
                        array_push($diagnosis, $encounter['diagnosis']);
                        array_push($medication, $encounter['medication']);
                        array_push($procedure, $encounter['procedure']);
                        array_push($post_procedure_diagnosis, $encounter['post_procedure_diagnosis']);
                        array_push($post_treatment, $encounter['post_treatment']);
                        
                    }
                    //if($first_encounter)
                    print_encounter_history_header($follow_up['id'], $visit_id, $follow_up['date_created'], $follow_up['doctor_name']);

                    echo '<div class="row">';
                    print_encounter_history('Remark',[[['title'=>$follow_up['remark']??'']]]);
                    print_encounter_history('Treatment Plans',[$follow_up['treatment_plans']]);
                    print_encounter_history('Chief Complaints',$chief_complaints);
                    print_encounter_history('Physical Finding',$physical_finding);
                    print_encounter_history('HPI',$hpi);
                    echo '</div>';
                    echo '<div class="row">';
                    print_encounter_history('Past Medical History',$past_medical_history);
                    print_encounter_history('Diagnosis',$diagnosis);
                    print_encounter_history('Medication',$medication);
                    echo '</div>';
                    echo '<div class="row">';
                    print_encounter_history('Procedure',$procedure,true);
                    print_encounter_history('Post Procedure Diagnosis',$post_procedure_diagnosis);
                    print_encounter_history('Post-Treatment Result/Outcome',$post_treatment);
                    print_order_history($follow_up['order_history']);
                    echo '</div>';
                    print_encounter_history_footer();
                }
            ?>
        </div>
    </div>
</div>
<script src="/js/html2canvas.js"></script>
    
<script>
var getCanvase; // global variable
    $('document').ready(function(){
    html2canvas($("#for_telegrame")[0]).then((canvase) => {
            $("#for_telegrame").html(canvase);
            getCanvase = canvase;
        });
    });


download_imge = function (el) {
  var imagee = getCanvase.toDataURL("image/png")
  el.href = imagee
}
</script>
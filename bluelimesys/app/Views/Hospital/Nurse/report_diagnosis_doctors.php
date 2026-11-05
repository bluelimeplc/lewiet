
<?php
    $date_from = substr($date_from,0,10);
    $date_to = substr($date_to,0,10);

    $age_list = [
        "0-0.99" => ["m"=>0,"f"=>0],
        "1-4" => ["m"=>0,"f"=>0],
        "5-14" => ["m"=>0,"f"=>0],
        "15-29" => ["m"=>0,"f"=>0],
        "30-64" => ["m"=>0,"f"=>0],
        "65-20000" => ["m"=>0,"f"=>0]
    ];

    $report_data = [];

    $diagnosis_list_for_index = [];
    foreach($encounterHistory as $encounter){
        foreach($encounter['diagnosis'] as $diagnosis){
            if(trim(string: ucwords(strtolower($diagnosis['doctors_diagnosis']))) == "")
                continue;
            array_push($diagnosis_list_for_index, [trim(ucwords(strtolower($diagnosis['doctors_diagnosis'] ?? "")))]);
        }
    }
    $diagnosis_list_for_index = BluelimeSystem::sort_array($diagnosis_list_for_index,sorting_fields: [0]);
    $diagnosis_list_for_index_temp = [];
    foreach($diagnosis_list_for_index as $row){
        array_push($diagnosis_list_for_index_temp, $row[0]);
    }
    
    $diagnosis_list_for_index = array_unique($diagnosis_list_for_index_temp);

    //BluelimeSystem::print_associated_array($diagnosis_list_for_index);


    foreach($diagnosis_list_for_index as $diagnosis_title)
        $report_data[$diagnosis_title] = ['age_list'=>$age_list,"dhis_title"=>""];


    foreach($encounterHistory as $encounter){
        $age = $encounter['age']; //\BluelimeSystem::year_difference(date("Y-m-d"),$encounter['date_of_birth']??'');
        $sex = ($encounter['sex'] == "Male") ? "m" : "f";
        
        foreach($encounter['diagnosis'] as $diagnosis){
            $diagnosis_title = trim(ucwords(strtolower($diagnosis['doctors_diagnosis']))) ?? "";
            $dhis_title = trim(ucwords(strtolower($diagnosis['title']))) ?? "";
            if($diagnosis_title == "")
                continue;

            $inserted = false;
            foreach($report_data as $index => $value){
                if($diagnosis_title == $index){
                    $inserted = true;
                    incriment_report_data($report_data, $age_list, $diagnosis_title, $sex, $age);
                    $report_data[$index]['dhis_title'] = $dhis_title;
                }
            }

            if(!$inserted){
                $report_data[$diagnosis_title] = $age_list;
                incriment_report_data($report_data, $age_list, $diagnosis_title, $sex, $age);
                $report_data[$index]['dhis_title'] = $dhis_title;
            }
        }
    }

    function incriment_report_data(&$report_data, array $age_list, $title, $sex, $age){
        $age_range = get_age_range($age_list, $age);
        $report_data[$title]['age_list'][$age_range][$sex]++;
    }

    function get_age_range($age_list, $age){
        $age_range = "65-20000";
        foreach($age_list as $index => $val){
            if($index == "other")
                continue;

            $ages = explode("-", $index);

            if(($age >= $ages[0]) && ($age <= $ages[1])){
                $age_range = $index;
                break;
            }
        }
        return $age_range;
    }

    //BluelimeSystem::print_associated_array($report_data);

    function calculate_total($report):int{
        $total = 0;
        foreach($report as $value){
           $total += intval($value['f']) + intval($value['m']);
        }
        return $total;
    }

?>
            <!-- =============================================== -->
            <!-- =============================================== -->
            <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

                <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Nurse</h1>
            <small>Report / Diagnosis</small>
        </div>
    </section>

        
        <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="panel-title"><h4>Date filter</h4></div>
                        <div class="panel-body">
                            <div class="row">
                                <form action="" method="GET">
                                    <div class=" col-lg-3" style="margin-bottom: 2px;">
                                        <input type="hidden" name="type" value="<?=$_GET['type']??'dhis'?>" >
                                        <div class="input-group">
                                            <span class="input-group-addon" ><b>From</b></span>
                                            <input type="date" class="form-control" value="<?=$date_from;?>" name="date_from">
                                        </div>
                                    </div>
                                    <div class=" col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" ><b>To</b></span>
                                            <input type="date" class="form-control" value="<?=$date_to;?>"  name="date_to">    
                                        </div>
                                        <input type="submit" style="visibility:hidden">
                                    </div>
                                    <div class=" col-lg-3"><input type="submit" class="btn btn-success" value="Go"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>
                                Diagnosis Report <a role="button" class="btn btn-info" onclick="print_printable_document('printable_diagnosis')"><span class="fa fa-print"></span></a> 
                                <a role="button" onclick="show_only_other_on_off()" class="btn btn-primary" ><span class="fa fa-filter"></span> Show <?= ($_GET['show_only_other']??2)==1?'all':' only <span class="badge badge-qty-red text-white">Other</span>'?></a> 
                            </h4> 
                        </div>
                    </div>
                    <div class="panel-body">
                        <div id="printable_diagnosis">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="15"><u>Diagnosis by Age and Sex.</u> Report date <?= $date_from . (($date_from!=$date_to)?' - ' . $date_to: '') ?> <span class="badge badge-qty"><?= count(value: $report_data)?> records.</span> </th>
                                    </tr>
                                    <tr>
                                        <th rowspan="2"></th>
                                        <th rowspan="2">Diagnosis</th>
                                        <th colspan="<?= count($age_list) ?>">Male</th>
                                        <th colspan="<?= count($age_list) ?>">Female</th>
                                        <th rowspan="2">Total</th>
                                    </tr>
                                    <tr>
                                        <?php

                                            foreach($age_list as $age_range => $val){
                                                if($age_range == "65-20000")
                                                    echo "<th>&gt;=65</th>";
                                                elseif($age_range == "0-0.99")
                                                    echo "<th>&lt; 1</th>";
                                                
                                                else
                                                    echo "<th>$age_range</th>";
                                            }

                                            foreach($age_list as $age_range => $val){
                                                if($age_range == "65-20000")
                                                    echo "<th>&gt;=65</th>";
                                                elseif($age_range == "0-0.99")
                                                    echo "<th>&lt; 1</th>";
                                                
                                                else
                                                    echo "<th>$age_range</th>";
                                            }
                                        ?>
                                    </tr>
                                    
                                </thead>
                                <tbody>
                                    <?php
                                        $cnt=1;
                                        foreach($report_data as $diagnosis_title => $report){
                                            if(($report['dhis_title']!="Other.")&&($report['dhis_title']!="Other")&&(($_GET['show_only_other']??2)==1))
                                                continue;

                                            echo '
                                            <tr>
                                                <td>'.$cnt++.'</td>
                                                <td>'.$diagnosis_title.'<br><span class="badge badge-qty'.(($report['dhis_title']=="Other")?'-warning':(($report['dhis_title']=="Other.")?'-red':'')).'">'.$report['dhis_title'].'</span> <span onclick="edit_dhis_translation(\''.$diagnosis_title.'\')" title="Edit DHIS translation" class="badge" style="cursor: pointer;"><i class="fa fa-edit"></i></span></td>';
                                            
                                            $male_data = "";
                                            $female_data = "";
                                            foreach($report['age_list'] as $value){
                                                $male_data   .="<td>".(($value['m'] > 0) ? $value['m'] : " ") ."</td>";
                                                $female_data .="<td>".(($value['f'] > 0) ? $value['f'] : " ") ."</td>";
                                                //$male_data .="<td>".(($value['f'] > 0) ? $value['f'] : " ") ."</td><td>". (($value['m'] > 0) ? $value['m'] : " ") ."</td>";
                                            }
                                            echo $male_data.$female_data;
                                            echo " 
                                                <td>".calculate_total($report['age_list'])."</td>
                                            </tr>
                                            ";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="edit_medical_history" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit DHIS Translation</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <tr>
                                <th width="150">Diagnosis Name:</th><td><b id="diagnosis_name">Test</b></td>
                            </tr>
                            <tr>
                                <th>DHIS Translation:</th><td><input type="text" class="form-control" id="dhis_translation"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" onclick="update_dhis_translation()">
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </section> <!-- /.content -->
</div> <!-- /.content-wrapper -->
<?= view('Include/_autocomplete_single_string') ?>

<script>
    function print_printable_document(id){
        var printContents = document.getElementById(id).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

    function edit_dhis_translation(diagnosis_title){
        $('#dhis_translation').val('');
        $('#diagnosis_name').html(diagnosis_title);
        $('#edit_medical_history').modal('show');
        setTimeout("$('#dhis_translation').focus()", 500);
    }

    function update_dhis_translation(){

        let update_data = {diagnosis_name:$('#diagnosis_name').html(),dhis_translation:$('#dhis_translation').val()}
        $.ajax({
            url: '/hospital/nurse/update_dhis_translation',
            method: 'POST',
            async:false,
            data: update_data,
            dataType: 'json',
            success:function(result){
                if(result.status == 'success')
                    window.location.reload();
                else
                    toastr['error']('Cannot Update');
            },
            error: function(jqXHR, status, error) {
                toastr['error']('Invalid return. JSON was expected.');
            }
        });
    }

    function show_only_other_on_off(){
        let show_only_other = <?= $_GET['show_only_other'] ?? 2 ?>;
        show_only_other = 2/show_only_other;
        let url = '/hospital/nurse/report_diagnosis_by_age_and_sex?type=doctors';
        <?php
            if(isset($_GET['date_from']))
                echo '
        url += "&date_from='.$_GET['date_from'].'";
            ';

            if(isset($_GET['date_to']))
                echo '
        url += "&date_to='.$_GET['date_to'].'";
            ';
        ?>

        url += "&show_only_other="+show_only_other;
        window.location = url;
    }

    let post_treatment = {
                        input:document.getElementById("dhis_translation"),
                        services:<?= BluelimeSystem::convert_array_to_string($dhis_translator) ?>,
                        selected_index:'selected_post_treatment_index'
                        // callback_function: set_focus_after_selection,
                        // callback_function_arguments: ['encounter_post_treatment_title']
                    };
    autocomplete_single_string_by_options(post_treatment);
</script>

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
            if(trim(string: ucwords(strtolower($diagnosis['title']))) == "")
                continue;
            array_push($diagnosis_list_for_index, [trim(ucwords(strtolower($diagnosis['title'] ?? "")))]);
        }
    }

    $diagnosis_list_for_index = BluelimeSystem::sort_array($diagnosis_list_for_index,sorting_fields: [0]);
    $diagnosis_list_for_index_temp = [];
    foreach($diagnosis_list_for_index as $row){
        array_push($diagnosis_list_for_index_temp, $row[0]);
    }
    
    $diagnosis_list_for_index = array_unique($diagnosis_list_for_index_temp);

    foreach($diagnosis_list_for_index as $diagnosis_title)
        $report_data[$diagnosis_title] = ['age_list'=>$age_list,"dhis_title"=>""];

    foreach($encounterHistory as $encounter){
        $age = $encounter['age']; //\BluelimeSystem::year_difference(date("Y-m-d"),$encounter['date_of_birth']??'');
        $sex = ($encounter['sex'] == "Male") ? "m" : "f";
        
        foreach($encounter['diagnosis'] as $diagnosis){
            $diagnosis_title = trim(ucwords(strtolower($diagnosis['title']))) ?? "";
            if($diagnosis_title == "")
                continue;

            $inserted = false;
            foreach($report_data as $index => $value){
                if($diagnosis_title == $index){
                    $inserted = true;
                    incriment_report_data($report_data, $age_list, $diagnosis_title, $sex, $age);
                }
            }

            if(!$inserted){
                $report_data[$diagnosis_title] = ['age_list'=>$age_list,"dhis_title"=>""];
                incriment_report_data($report_data, $age_list, $diagnosis_title, $sex, $age);
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
                            <h4>Diagnosis Report <a role="button" class="btn btn-info" onclick="print_printable_document('printable_diagnosis')"><span class="fa fa-print"></span></a></h4> 
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
                                            if($diagnosis_title == 'Other')
                                                continue;

                                            echo "
                                            <tr>
                                                <td>".$cnt++."</td>
                                                <td>$diagnosis_title</td>";
                                            
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

                                        if(isset($report_data['Other'])){
                                            echo "
                                            <tr>
                                                <td>".$cnt++."</td>
                                                <td>Other</td>";
                                            
                                            $male_data = "";
                                            $female_data = "";
                                            foreach($report_data['Other']['age_list'] as $value){
                                                $male_data   .="<td>".(($value['m'] > 0) ? $value['m'] : " ") ."</td>";
                                                $female_data .="<td>".(($value['f'] > 0) ? $value['f'] : " ") ."</td>";
                                                //$male_data .="<td>".(($value['f'] > 0) ? $value['f'] : " ") ."</td><td>". (($value['m'] > 0) ? $value['m'] : " ") ."</td>";
                                            }
                                            echo $male_data.$female_data;
                                            echo " 
                                                <td>".calculate_total($report_data['Other']['age_list'])."</td>
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
    </section> <!-- /.content -->
</div> <!-- /.content-wrapper -->

<script>
    function print_printable_document(id){
        var printContents = document.getElementById(id).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
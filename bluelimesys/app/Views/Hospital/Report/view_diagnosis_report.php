            <?php
                $selected_date = str_replace("%", "",@$this->data['selected_date']);
                $end_date = substr($this->data['end_date'], 0, strrpos($this->data['end_date'], ' '));
                $selected_doctor = @$this->data['selected_doctor'];
            ?><!-- =============================================== -->
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">

                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="header-icon">
                        <i class="pe-7s-world"></i>
                    </div>
                    <div class="header-title">
                        <h1><?php echo "Reports";?></h1>
                        <small><?php echo "Diagnosis Report";?></small>
                        <ol class="breadcrumb">
                            <?= view("Include/_admin_report_top_menu"); ?>
                        </ol>
                    </div>
                </section>

                
                <!-- Main content -->
                <section class="content">
                <div class="row">
                <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    echo form_open_multipart('/hospital/report/diagnosisreport', $attributes);
                ?>
                        <div class="col-sm-12 col-md-12">
                            <div class="panel panel-bd lobidisable">
                               <div class="panel-body">
                                <div class=" col-sm-8 col-md-8">
                                <div class="input-group col-sm-12 col-md-12">
                                    <span class="input-group-addon"></span>
                                        <b>From:</b>
                                    <input type="date" class="form-control" value="<?=$selected_date;?>" onChange="this.form.submit()"  name="selected_date">
                                        <span class="input-group-addon"></span>
                                        <b>To:</b>
                                    <input type="date" class="form-control" value="<?=$end_date;?>" onChange="this.form.submit()"  name="end_date">    
                                        <span class="input-group-addon"></span>
                                    <input type="submit" style="visibility:hidden">
                                </div>
                                <div>
                                </div>
                            </div>
                        </div>
                <?php echo form_close();?>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <div class="panel panel-bd lobidisable">
                               <div class="panel-body table-responsive">

                                    <?php 
                                        $age_list = [
                                            "0-1" => ["m"=>0,"f"=>0],
                                            "2-4" => ["m"=>0,"f"=>0],
                                            "5-14" => ["m"=>0,"f"=>0],
                                            "15-29" => ["m"=>0,"f"=>0],
                                            "30-64" => ["m"=>0,"f"=>0],
                                            "65-20000" => ["m"=>0,"f"=>0]
                                        ];

                                        $report_data = [];


                                        foreach($encounterHistory as $encounter){
                                            $age = \BluelimeSystem::year_difference(date("Y-m-d"),$encounter['date_of_birth']);
                                            $sex = ($encounter['sex'] == "Male") ? "m" : "f";
                                            
                                            foreach($encounter['diagnosis'] as $diagnosis){
                                                $diagnosis_title = $diagnosis['title'] ?? "";
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
                                                    $report_data[$diagnosis_title] = $age_list;
                                                    incriment_report_data($report_data, $age_list, $diagnosis_title, $sex, $age);
                                                }
                                            }

                                        }


                                        function incriment_report_data(&$report_data, array $age_list, $title, $sex, $age){
                                            $age_range = get_age_range($age_list, $age);
                                            $report_data[$title][$age_range][$sex]++;
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
                                    
                                    ?>

                                    <table class="table table-striped table-bordered  table-responsive">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">Diagnosis</th>
                                                <?php
                                                    foreach($age_list as $age_range => $val){
                                                        if($age_range == "65-20000")
                                                            echo "<th colspan=\"2\">&gt;=65</th>";
                                                        else
                                                            echo "<th colspan=\"2\">$age_range</th>";
                                                    }
                                                ?>
                                            </tr>
                                            <tr>
                                                <?php
                                                    foreach($age_list as $age_range => $val){
                                                        echo "<th>F</th><th>M</th>";
                                                    }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                
                                                foreach($report_data as $diagnosis_title => $report){
                                                    echo "
                                                    <tr>
                                                        <td>$diagnosis_title</td>";
                                                    
                                                    foreach($report as $value){
                                                        echo "<td>".(($value['f'] > 0) ? $value['f'] : " ") ."</td><td>". (($value['m'] > 0) ? $value['m'] : " ") ."</td>";
                                                    }
                                                        
                                                    echo " 
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
                </section> <!-- /.content -->
            </div> <!-- /.content-wrapper -->
          
<?php
    $selected_date= str_replace("%", "",$this->data['selected_date']);
    //BluelimeSystem::show_and_die($selected_date);
    $daily_new_patients = $this->data['daily_new_patients'];
    $nop = $this->data['nop'];
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
                <h1>Reception</h1>
                <small>Patient List</small>
                <ol class="breadcrumb">
                <a href="<?php echo base_url(); ?>hospital/reception/receptiondashboard"><h4 class="btn btn-primary">Dashboard </h4> <a href="<?php echo base_url(); ?>hospital/reception/doctorsreport"><h4 class="btn btn-primary">Doctors Report </h4></a>  <a href="<?php echo base_url(); ?>hospital/reception/dailypatients"><h4 class="btn btn-primary">Print Patient List</h4></a>
                </ol>
            </div>
        </section>
      
                <!-- Main content -->
        <section class="content">
            <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    echo form_open_multipart('/hospital/reception/dailypatients', $attributes);
            ?>

            <!-- Search Area -->
            <div class="row">
                
                <!--  Patient List area -->
                <div class="col-sm-12">
                <div class="panel-body">
                        
                </div>
                    <div  class="panel panel-default">
                        <div class="panel-body  table-responsive">
                        <div class="form-group">
                            <div class="col-lg-1">
                                <label for="selected_date">Date: </label>
                            </div>
                            <div class="col-lg-3">
                                <input type="date" class="form-control" value="<?=isset($selected_date)?$selected_date:date('Y-m-d');?>" id="selected_date" name="selected_date" onchange="this.form.submit()">
                            </div>
                            <div class="col-lg-1">
                                <input name= "daily_report" id="daily_report" type="submit" value="Search" hidden />
                            </div> 
                            <div class="col-lg-1">
                            <a role="button" onclick="print_printable('report_printable')" class="btn btn-success" style="float:right"><span class="fa fa-print"></span> Print</span></a>
                            </div>
                        </div>
                        <div id="report_printable">
                            <table width="100%" class="table table-bordered table-condensed" id="patient_list4">
                                <thead>
                                    <tr><th colspan="6" style="text-align:center"><?= @$selected_date." (".@$nop." Patients)" ?></th></tr>
                                    <tr>                     
                                        <th>N<u>o</u></th>
                                        <th>MRN No</th>
                                        <th>Full Name</th>
                                        <th>Phone Number</th>
                                        <th>Age</th>
                                        <th>Sex</th>
                                        <th>Diagnosis</th>
                                        
                                    </tr>

                                </thead>

                                <tbody>
                                    <?php
                                        $cnt =0;
                                        foreach ($daily_new_patients as $patient) {
                                            $cnt++;
                                    ?>
                                    <tr class="odd gradeX">
                                        <td width="15px"><?=$cnt?></td>
                                        <td><b><?= sprintf('%05d', $patient['id']); ?></b></td>
                                        <td><?php echo $patient['first_name'].' '. @$patient['middle_name'].' '. @$patient['last_name'] ;?></td>
                                        <td><?php echo $patient['mobile'];?></td>
                                        <td><?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."Y "?><?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth']) >=5 ?"":BluelimeSystem::month_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."M"?></td>
                                        <td><?php echo $patient['sex'];?></td>
                                        <td>
                                        <?php
                                        foreach($patient['patient_diagnosis'] as $diagnosis){
                                                echo $diagnosis['title'] .", ";
                                            }
                                            ?></td>
                                        
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>            
        </section>

    </div> <!-- /.content-wrapper -->

    <script>
        function focus_search(){
            $("#search_patient").focus().val($("#search_patient").val());

        }
        document.onload(focus_search());
    </script>
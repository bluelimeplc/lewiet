            <?php
                $selected_date = str_replace("%", "",@$this->data['selected_date']);
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
                        <small><?php echo "Patient History Report";?></small>
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
                    echo form_open_multipart('/hospital/report/medicalhistoryreport', $attributes);
                ?>
                        <div class="col-sm-12 col-md-12">
                            <div class="panel panel-bd lobidisable">
                               <div class="panel-body">
                                <div class=" col-sm-8 col-md-8">
                                <div class="input-group col-sm-12 col-md-12">
                                    <span class="input-group-addon"></span>
                                        <b>By Date:</b>
                                    <input type="date" class="form-control" value="<?=$selected_date;?>" onChange="this.form.submit()"  name="selected_date">
                                        <span class="input-group-addon"></span>
                                        <b>By Doctor:</b>
                                    <select class="form-control"  name="selected_doctor" onChange="this.form.submit()" >
                                    <option value="">--</option>
                                        <?php
                                            foreach($doctors as $doc):
                                        ?>
                                        <option <?=($doc['id']==$selected_doctor)?"selected":""?> value="<?=$doc['id']?>" ><?=$doc['name']?></option>
                                        <?php
                                            endforeach;
                                        ?>
                                    </select>
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
                            <?php
                                $attributes = array('role'=>'form');
                                helper('form');
                                echo form_open_multipart('/hospital/report', $attributes);
                            ?>
                            <div class="panel panel-bd lobidisable">
                               <div class="panel-body table-responsive">
                                    <table class="table table-striped table-bordered  table-responsive">
                                        <tr><td>Doctor</td><td>Patient</td><td>Date</td><td>Chief Complaints</td><td>Past Medical History</td><td>Dx</td><td>Procedure</td><td>Treatment</td><td>Post Dx</td></tr>
                                        <?php
                                            foreach($encounterHistory as $encounter):
                                        ?>
                                        <tr>
                                            <td><?=$encounter['doctor_name'][0];?></td>
                                            <td><?=$encounter['patient_name'][0];?></td>
                                            <td><?=date('d-m-Y',strtotime($encounter['registered_date']));?></td>
                                            <td><?=\BluelimeSystem::encounter_history($encounter['chief_complaints']);?></td>
                                            <td><?=\BluelimeSystem::encounter_history($encounter['past_medical_history']);?></td>
                                            <td><?=\BluelimeSystem::encounter_history($encounter['diagnosis']);?></td>
                                            <td><?=\BluelimeSystem::encounter_history($encounter['procedure'],true);?></td>
                                            <td><?=\BluelimeSystem::encounter_history($encounter['medication']);?></td>
                                            <td><?=\BluelimeSystem::encounter_history($encounter['post_procedure_diagnosis']);?></td>
                                        </tr>
                                        <?php
                                            endforeach;
                                        ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php
                                echo form_close();
                            ?>
                    </div>
                    

                </section> <!-- /.content -->
            </div> <!-- /.content-wrapper -->
          
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
                    echo form_open_multipart('/hospital/report/diagnosisreportdetail', $attributes);
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
                            <?php
                                $attributes = array('role'=>'form');
                                helper('form');
                                echo form_open_multipart('/hospital/report', $attributes);
                            ?>
                            <div class="panel panel-bd lobidisable">
                               <div class="panel-body table-responsive">
                                    <table class="table table-striped table-bordered  table-responsive">
                                        <tr><td>Sex</td><td>Age</td><td>Date</td><td>Diagnosis</td></tr>
                                        <?php
                                            foreach($encounterHistory as $encounter):
                                        ?>
                                        <tr>
                                            <td><?=@$encounter['sex'];?></td>
                                            <td><?=isset($encounter['date_of_birth'])?(\BluelimeSystem::year_difference(date('Y-m-d'),$encounter['date_of_birth'])):"";?></td>
                                            <td><?=date('d-m-Y',strtotime($encounter['registered_date']));?></td>
                                            <td><?=\BluelimeSystem::encounter_history($encounter['diagnosis'], true);?></td>
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
          
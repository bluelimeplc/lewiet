            <?php
                $print_data = [
                    'document_no' => sprintf('%05d',  $medical_certificate[0]['id'] ),
                    'document_title' => 'የህክምና ምስክር ወረቀት<br>Medical Certificate',
                    'document_date' =>$medical_certificate[0]['regisered_date'],
                    
                ];
            ?>
            <?= view('Include/_print_header_A5_Portrait', $print_data) ?>
            <table class="table table-condensed table-bordered" style="margin-top: 5px;  font-size: smaller; margin-bottom: 2px;">
                <tr style="border: none">
                    <td style="border: none" align="right"><b>Patient Name</b></td><td style="border: none"><?= $patient['first_name'].' '.$patient['middle_name'].' '.$patient['last_name'] ?></td>
                    <td align="right" style="border: none"><b>Card Number</b></td><td style="border: none">MRN-<?= sprintf('%05d', $patient['id'] ) ?></td>
                </tr>
                <tr>
                    <td align="right" style="border: none"><b>Age</b></td><td style="border: none"><?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."Y "?><?=BluelimeSystem::month_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."M"?></td>
                    <td align="right" style="border: none"><b>Sex</b></td><td style="border: none"><?= $patient['sex'] ?></td>
                </tr>
            </table><br>

                <div style="min-height: 300px; font-size: smaller;padding-left: 20px;">
                <div style="width: 60mm; float: left; margin-left:4mm">
                        <div class="row"  style="width: 148.5mm;">
                            <div style="width: 50mm; float: left;">
                                <strong> 
                                    <?php echo "Visit/Treatment Dates:" ?>
                                </strong>
                            </div>
                            <div style="width: 130mm; float: left; padding-left:30px">
                                <?php echo $medical_certificate[0]['visit_dates']??date('Y-m-d',strtotime($medical_certificate[0]['regisered_date']))?>
                            </div>
                        </div>
                        <div style="width: 148.5mm;">
                            <br>
                        </div>
                        <div class="row"  style="width: 148.5mm;">
                            <div style="width: 50mm; float: left;">
                                <strong> 
                                    <?php echo "Diagnosis:" ?>
                                </strong>
                            </div>
                            <div style="width: 130mm; float: left; padding-left:30px">
                                <?php echo $medical_certificate[0]['diagnosis']?>
                            </div>
                        </div>
                        <div style="width: 148.5mm;">
                            <br>
                        </div>
                        <div class="row"  style="width: 148.5mm;">
                            <div style="width: 50mm; float: left;">
                                <strong> 
                                    <?php echo "Treatment/Procedure:" ?>
                                </strong>
                            </div>
                            <div style="width: 130mm; float: left; line-height: 1.5; padding-left:30px">
                                <?php echo $medical_certificate[0]['treatment_given']?>
                            </div>
                        </div>
                        <div style="width: 148.5mm;">
                            <br>
                        </div>

                        <div class="row"  style="width: 148.5mm;">
                            <div style="width: 50mm; float: left;">
                                <strong> 
                                    <?php echo "Doctor’s Recommendation:"?>
                                </strong>
                            </div>
                            <div style="width: 130mm; float: left; padding-left:30px">
                                <?php echo $medical_certificate[0]['recommendation'] ?>
                            </div>
                        </div>
                        <div class="row" style="width: 148.5mm;">
                            <br>
                        </div>
                        
                        <div class="row"  style="width: 148.5mm;">
                            <div style="width: 100mm; float: left;">
                                <strong> 
                                    <?php echo "Rest Recommended: ". $medical_certificate[0]['rest_days']?>
                                </strong>
                            </div>
                        </div>
                        <div class="row" style="width: 148.5mm;">
                            <br>
                        </div>
                        
                        <div class="row"  style="width: 148.5mm;">
                            <div style="width: 100mm; float: left;">
                                <strong> 
                                    <?php echo "Next Appointment: ". $medical_certificate[0]['next_appointment']?>
                                </strong>
                            </div>
                        </div>
                    </div>
                    
                    <div style="min-height: 250px;">
                        
                            
                    </div>
                    
                    
                </div>
                <div class="col-md-12" style="text-align: center;">
                <hr>
                
                    <div style="text-align: center; padding: 5px;float: right;">
                    <?php 
                    //BluelimeSystem::show_and_die($doc_id.$medical_certificate[0]['registerd_by']);
                    echo '<img style="height:60px;" src="data:image/jpeg;base64,'.base64_encode(  $doctor['digital_signature'] ?? '' ).'"/>' ?><br>
                        <u><?= $doctor['name'] ?? '' ?></u><br>
                        <?= $doctor['title'] ?? '' ?><br>
                        <b>Doctor's Signature<br></b>
                    </div>
                </div>
                <?php
                    if(@$doc_id != $medical_certificate[0]['registerd_by']){
                ?>
                <div style="text-align: center; position: absolute;bottom: 60px;">
                    <u><?= $medical_certificate[0]['registered_by_name'] ?? '' ?></u><br>
                    <b>Prepared By<br></b>
                </div>
                <?php
                    }
                ?>
                <div class="col-md-12 print-footer" style="position: absolute; bottom: 10px;left: 0px;">
                    System developed by Bluelime Technologies | bluelimeplc.com
                </div>
                </div>
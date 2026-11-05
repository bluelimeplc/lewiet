            <?php
                $print_data = [
                    'document_no' => sprintf('%05d',  $external_referal_request[0]['id'] ),
                    'document_title' => 'የታካሚ ሪፈራል መጠየቂያ<br>Patient Referral Request',
                    'document_date' =>$external_referal_request[0]['created_at'],
                    
                ];
            ?>
            <?= view('Include/_print_header_A4_Portrait', $print_data) ?>
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

                <div style="min-height: 300px; font-size: 12px;padding-left: 20px;">
                <div style="width: 60mm; float: left; margin-left:4mm">
                        
                        
                        <div class="row"  style="width: 148.5mm;">
                            <div style="width: 185mm; float: left;">
                                <strong>    
                                        <table class="table table-bordered">
                                                        <tr>
                                                            <td width="150"><b>Hospital/Health Center:</b></td>
                                                            <td colspan="4">
                                                                <?=$external_referal_request[0]['requested_company_name']?>
                                                            </td>
                                                        </tr>                                            
                                                </table><br>
                                                <table class="table table-bordered">
                                                        <tr>
                                                            <th width="150">Clinical Finding </th>
                                                            <td style="padding: 3px;">
                                                            <?= BluelimeSystem::convert_to_array($external_referal_request[0]['description'])['ref_clinical_finding'] ?></td>
                                                        </tr>
                                                            
                                                        <tr>
                                                            <th width="150">Diagnosis </th>
                                                            <td style="padding: 3px;">
                                                            <?= BluelimeSystem::convert_to_array($external_referal_request[0]['description'])['ref_diagnosis'] ?></td>
                                                        </tr>
                                                            
                                                        <tr>
                                                            <th width="150">Investigation Result </th>
                                                            <td style="padding: 3px;">
                                                            <?= BluelimeSystem::convert_to_array($external_referal_request[0]['description'])['ref_investigation_result'] ?></td>
                                                        </tr>
                                                            
                                                        <tr>
                                                            <th width="150">Px. Given </th>
                                                            <td style="padding: 3px;">
                                                            <?= BluelimeSystem::convert_to_array($external_referal_request[0]['description'])['ref_px_given'] ?></td>
                                                        </tr>    
                                                        <tr>
                                                            <th width="150">Reason for Referral </th>
                                                            <td style="padding: 3px;">
                                                            <?= BluelimeSystem::convert_to_array($external_referal_request[0]['description'])['ref_reason'] ?></td>
                                                        </tr>
                                                </table><br>
                                        

                                    
                                </strong>
                            </div>
                        </div>
                        <div class="row" style="width: 148.5mm;">
                            <br>
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
                        <u><?= $external_referal_request[0]['registered_by_name'] ?? '' ?></u><br>
                        <?= $doctor['title'] ?? '' ?><br>
                        <b>Doctor's Signature<br></b>
                    </div>
                </div>
                
                <div class="col-md-12 print-footer" style="position: absolute; bottom: 10px;left: 0px;">
                    System developed by Bluelime Technologies | bluelimeplc.com
                </div>
                </div>
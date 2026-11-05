            <?php
                $print_data = [
                    'document_no' => sprintf('%05d',  $external_biopsy_request[0]['id'] ),
                    'document_title' => 'የውጭ ባዮፕሲ መጠየቂያ<br>External Biopsy Request',
                    'document_date' =>$external_biopsy_request[0]['created_at'],
                    
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
                                                                <?=$external_biopsy_request[0]['requested_company_name']?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                        <td><b>Requesting Physician:</b></td>   
                                                        <td colspan="4">
                                                        <?= BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_request_doctor_name'] ?>
                                                                                </td>
                                                        </tr>
                                                        <tr>
                                                        <td><b>Surgeon:</b></td>   
                                                        <td colspan="4">
                                                        <?= $external_biopsy_request[0]['registered_by_name'] ?>
                                                                                </td>
                                                        </tr>
                                                        <tr>
                                                        <td><b>Sampling Date:</b></td>   
                                                        <td colspan="4">
                                                        <?= $external_biopsy_request[0]['created_at'] ?>                      
                                                                                </td>
                                                        </tr>
                                                        <?php if(BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_urgent']??-1 == 1): ?>
                                                        <tr>
                                                        <td><b style="color: red;">URGENT</b></td>   
                                                        <td colspan="4" style="color: red; font-weight: bold;">
                                                        This is an urgent biopsy request.                      
                                                                                </td>
                                                        </tr>
                                                        <?php endif; ?>
                                            
                                                </table><br>
                                                <table class="table table-bordered">
                                                        <tr>
                                                            <th width="150">History </th>
                                                            <td style="padding: 3px;">
                                                            <?= BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_request_history'] ?></td>
                                                        </tr>
                                                            
                                                        <tr>
                                                            <th width="150">Physical Finding </th>
                                                            <td style="padding: 3px;">
                                                            <?= BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_request_physical_finding'] ?></td>
                                                        </tr>
                                                            
                                                        <tr>
                                                            <th width="150">Clinical Diagnosis </th>
                                                            <td style="padding: 3px;">
                                                            <?= BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_request_diagnosis'] ?></td>
                                                        </tr>
                                                            
                                                        <tr>
                                                            <th width="150">Laboratory & imaging Information </th>
                                                            <td style="padding: 3px;">
                                                            <?= BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_request_imaging'] ?></td>
                                                        </tr>
                                                </table><br>
                                                <table>
                                                    <thead>
                                                        <tr class="bg-info">
                                                            <th width="750" colspan="6">Biopsy Type</th>
                                                        </tr>
                                                        <?php
                                                            if(empty(BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['biopsy_type'])){
                                                            
                                                        ?>
                                                        <tr>
                                                            <th>Punch</th>
                                                            <th>Incision</th>
                                                            <th>Excision</th>
                                                            <th>Shave</th>
                                                            <th>Curettage</th>
                                                            <th>Other</th>
                                                        </tr>
                                                        
                                                        <tr>
                                                            <td>
                                                            <input style="transform: scale(0.8);"  type="checkbox" onclick="return false;" <?=BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_request_punch']!=""?"checked":"" ?> class="form-control"  id="bio_request_punch" />
                                                            </td>
                                                            <td>
                                                            <input style="transform: scale(0.8);"  style="transform: scale(0.8);"  type="checkbox" onclick="return false;" <?=BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_request_incision']!=""?"checked":"" ?>  class="form-control"  id="bio_request_incision"/>
                                                            </td>
                                                            <td>
                                                            <input style="transform: scale(0.8);"  type="checkbox" onclick="return false;" <?=BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_request_excision']!=""?"checked":"" ?>  class="form-control" id="bio_request_excision"/>
                                                            </td><td>
                                                            <input style="transform: scale(0.8);"  type="checkbox" onclick="return false;" <?=BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_request_excision']!=""?"checked":"" ?>  class="form-control" id="bio_request_shave"/>
                                                            </td><td>
                                                            <input style="transform: scale(0.8);"  type="checkbox" onclick="return false;" <?=BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_request_curettage']!=""?"checked":"" ?>  class="form-control" id="bio_request_curettage"/>
                                                            </td><td>
                                                            <?=BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_request_other']?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                            }else{
                                                        ?>
                                                        <tr>
                                                            <td style="font-size: 14px;">
                                                                <br><?=BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['biopsy_type']. " ". BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['biopsy_size'] ?>
                                                            </td>
                                                          </tr>
                                                          <?php
                                                            }
                                                            ?>  
                                                        
                                                        </thead>
                                                    </table><br>
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th>Biopsy Site</th>
                                                            <th>Anesthesia</th>
                                                            <th>Fixative</th>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                            <?=BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_request_site']?>
                                                            </td>
                                                            <td>
                                                            <?=BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_request_anesthesia']?>
                                                            </td>
                                                            <td>
                                                            <?=BluelimeSystem::convert_to_array($external_biopsy_request[0]['description'])['bio_request_fixative']?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                        

                                    
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
                    
                </div>
                <div style="position: absolute; bottom: 100px;right: 50px;">
                    <?php 
                        //BluelimeSystem::show_and_die($doc_id.$medical_certificate[0]['registerd_by']);
                        echo '<img style="height:60px;" src="data:image/jpeg;base64,'.base64_encode(  $doctor['digital_signature'] ?? '' ).'"/>' 
                    ?><br>
                        <u><?= $external_biopsy_request[0]['registered_by_name'] ?? '' ?></u><br>
                        <?= $doctor['title'] ?? '' ?><br>
                        <b>Doctor's Signature<br></b>
                </div>
                
                <div class="col-md-12 print-footer" style="position: absolute; bottom: 10px;left: 0px;">
                    System developed by Bluelime Technologies | bluelimeplc.com
                </div>
                </div>
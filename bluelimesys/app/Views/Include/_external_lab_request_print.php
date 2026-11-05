            <?php
                $print_data = [
                    'document_no' => sprintf('%05d',  $external_lab_request[0]['id'] ),
                    'document_title' => 'የውጭ ላቦራቶሪ መጠየቂያ<br>External Laboratory Request',
                    'document_date' =>$external_lab_request[0]['created_at'],
                    
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

                <div style="min-height: 300px; font-size: 12px;padding-left: 20px;">
                <div style="width: 60mm; float: left; margin-left:4mm">
                        
                        
                        <div class="row"  style="width: 148.5mm;">
                            <div style="width: 130mm; float: left;">
                                <strong> 
                                    <?php 
                                        echo "<b style='font-size: 13px;'>To: <u>".$external_lab_request[0]['requested_company_name']."</u></b><br><br>";
                                        echo "Request Description <br>";
                                        
                                        echo "<table class='table table-bordered' width='100px'>";
                                        echo "<tr><th width='10px'>SN</th><th>Test Name</th><th>Note</th></tr>";
                                         foreach($external_lab_request as $row){
                                            $i = 1;
                                            foreach(BluelimeSystem::convert_to_array($row['description']) as $row1){
                                               
                                                $request_test_note = $row1['request_test_note'] ?? '';
                                                
                                                $request_test_name = $row1['request_test_name'] ?? '';
                                                if(!empty(trim($request_test_note)) || !empty(trim($request_test_name))){
                                                    echo "<tr><td>".$i."</td><td>".$request_test_name ."</td><td>". $request_test_note  . "</td></tr>";
                                                    
                                                }
                                                $i++;   
                                            }
                                            
                                        }
                                        echo "</table><div style='float:right;'><label>Remark: </label>";
                                        echo BluelimeSystem::convert_to_array($row['description'])['request_remark']."</div>";
                                        

                                    ?>
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
                        <u><?= $external_lab_request[0]['registered_by_name'] ?? '' ?></u><br>
                        <?= $doctor['title'] ?? '' ?><br>
                        <b>Doctor's Signature<br></b>
                    </div>
                </div>
                
                <div class="col-md-12 print-footer" style="position: absolute; bottom: 10px;left: 0px;">
                    System developed by Bluelime Technologies | bluelimeplc.com
                </div>
                </div>
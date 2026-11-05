            <?php
                $print_data = [
                    'document_no' => sprintf('%05d',  $medical_report[0]['id'] ),
                    'document_title' => 'የህክምና ምስክር ወረቀት<br>Medical Report',
                    'document_date' =>$medical_report[0]['created_at'],
                    
                ];
            ?>
            <?= view('Include/_print_header_A4_Portrait', $print_data) ?>
            <table class="table table-condensed table-bordered" style="margin-top: 5px;  font-size: smaller; margin-bottom: 2px;">
                <tr style="border: none">
                    <td style="border: none" align="right"><b>Patient Name</b></td><td style="border: none"><?= $medical_report[0]['patient_name'] ?></td>
                    <td align="right" style="border: none"><b>Card Number</b></td><td style="border: none">MRN-<?= sprintf('%05d', $medical_report[0]['patient_id'] ) ?></td>
                </tr>
                <tr>
                    <td align="right" style="border: none"><b>Age</b></td><td style="border: none"><?=$medical_report[0]['age']?></td>
                    <td align="right" style="border: none"><b>Sex</b></td><td style="border: none"><?= $medical_report[0]['sex'] ?></td>
                </tr>
            </table><br>

                <div style="min-height: 300px; font-size: smaller;padding-left: 20px;">
                <div style="width: 60mm; float: left; margin-left:4mm">
                        
                        <div style="width: 210mm;">
                            <br>
                        </div>
                        <div class="row"  style="width: 210mm;">
                            <div style="width: 50mm; float: left;">
                                <strong> 
                                    <?php echo "History:" ?>
                                </strong>
                            </div>
                            <div style="width: 190mm; float: left; line-height: 1.5; padding-left:30px">
                                <?php echo $medical_report[0]['history']?>
                            </div>
                        </div>
                        
                        <div class="row" style="width: 210mm;">
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
                        <u><?= $doctor['name'] ?? '' ?></u><br>
                        <?= $doctor['title'] ?? '' ?><br>
                        <b>Doctor's Signature<br></b>
                    </div>
                </div>
                
                <div class="col-md-12 print-footer" style="position: absolute; bottom: 10px;left: 0px;">
                    System developed by Bluelime Technologies | bluelimeplc.com
                </div>
                </div>
            <?php
                $print_data = [
                    'document_no' => sprintf('%05d',  $biopsy_result['id'] ),
                    'document_title' => 'Histopathology Report',
                    'document_date' =>$biopsy_result['updated_at'],
                    'revision' =>$biopsy_result['revision'],
                    
                ];
                $url_for_validation = 'http://pathology.rankderma.com/home/validate_result?branch='.strtolower($_ENV['BRANCH']).'&order_id='.md5($order['id']).'&brid='.md5($biopsy_result['id']);
                $url_for_validation = urlencode($url_for_validation);
            ?>
            <?= view('Include/_print_header_A4_Portrait', $print_data) ?>
            <style>
                .table-borderless td,
                .table-borderless th {
                    border: 0 !important;
                    }
            </style>

                <div class="row" style="font-size: 14px;">
                    <div class="col-lg-12">
                        <div class="panel">
                            <div class="panel-body">
                                <table class="table table-borderless table-condensed">
                                    <tr>
                                        <td class="col-5" style="font-weight: bold;">Name</td>
                                        <td class="col-7"><?= $biopsy_result['patient_name'] ?></td>
                                        <td class="col-6" style="font-weight: bold;">Referring Institution</td>
                                        <td><?= BluelimeSystem::convert_to_array($order['additional_info'])['refering_institution']??"" ?></td>
                                        
                                        
                                    </tr>
                                    <tr>
                                        <td class="col-5" style="font-weight: bold;">Age / Sex</td>
                                        <td class="col-7"><?=$biopsy_result['age']?>/<?= $biopsy_result['sex'] ?></td>
                                        <td class="col-6" style="font-weight: bold;">Collection Time</td>
                                        <td><?= $order['sample_collected_date'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-5" style="font-weight: bold;">Mobile</td>
                                        <td><?= $biopsy_result['mobile'] ?></td>
                                        <td class="col-6" style="font-weight: bold;">Reporting Time</td>
                                        <td><?= $order['result_approved_date'] ?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-5" style="font-weight: bold;">Patient Id</td>
                                        <td><?= ($biopsy_result['patient_id']==0)?sprintf('%05d', $order['bill_id']):sprintf('%05d', $biopsy_result['patient_id'] ) ?></td>
                                        <td class="col-5" style="font-weight: bold;">Sample Id</td>
                                        <td><?= $order['id'] ?></td>
                                    </tr>
                                </table>
                                <table class="table table-borderless table-condensed">
                                    <tr>
                                        <td><b>Specimen:</b> <?= BluelimeSystem::convert_to_array($order['additional_info'])['specimen']??"" ?></td>
                                        <td class=""></td>
                                        <td class=""><b>Type of Biopsy:</b> <?= str_replace("Grade 1 ", "", BluelimeSystem::convert_to_array($order['additional_info'])['biopsy_type']??"") ?></td>
                                        <td></td>
                                        <td class=""><b>Site:</b> <?= BluelimeSystem::convert_to_array($order['additional_info'])['site_of_biopsy']??"" ?></td>
                                        
                                        
                                    </tr>
                                    <table class="table table-borderless table-condensed">
                                    <tr>
                                        
                                        <td><b>Clinical Diagnosis: </b><?= BluelimeSystem::convert_to_array($order['additional_info'])['clinical_diagnosis']??"" ?></td>
                                        <td></td>
                                        
                                    </tr>
                                    </table>
                                    
                                </table>
                            </div>
                        </div>
                        
                        
                       
                    </div>

                    
                </div>
        
            <hr style="margin-top: 0px;">
            <?php
                if(!empty($biopsy_result['biopsy_result']['amendment_note'])){
            ?>
                <div style="text-align:center;color:white;">
                    <h4><span style="background-color: #000000;"><?= $biopsy_result['biopsy_result']['amendment_note']?></span></h4>
                </div>
                <?php
                }
                ?>
            <hr>
        <div class="row">
            <div class="col-lg-12">
                        <div class="panel border-0 shadow-sm" style="min-height: 300px;">
                            <div class="panel-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="col-5" style="width: 40%;font-weight: bold;">Histology Number:</td>
                                        <td id="rt_biopsy_number"><?= $biopsy_result['biopsy_result']['biopsy_number']?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-5" style="width: 40%;font-weight: bold;">Gross Description:</td>
                                        <td id="rt_gross_morphology"><?= $biopsy_result['biopsy_result']['gross_morphology']?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-5" style="width: 40%;font-weight: bold;">Microscopy:</td>
                                        <td id="rt_microscopy"><?= $biopsy_result['biopsy_result']['microscopy']?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-5" style="width: 40%;font-weight: bold;">Diagnosis:</td>
                                        <td id="rt_conclusion"><?= $biopsy_result['biopsy_result']['conclusion']?></td>
                                    </tr>
                                    <tr>
                                        <td class="col-5" style="width: 40%;font-weight: bold;">Comment:</td>
                                        <td id="rt_recommendation"><?= $biopsy_result['biopsy_result']['recommendation']?></td>
                                    </tr>  
                                    
                                </table>
                            </div>
                        </div>
                        
                    </div>

        </div>
        
       

<div class="panel-footer" <?=$order['order_status'] <= 5 ? 'hidden' : '' ?>>
            
               

            
           
                                <div class="panel-body" style="font-size: 14px;">
                                    <table class="table table-borderless">
                                        <tr style="vertical-align: middle;">
                                            <td style="width: 50px; text-align: center;">
                                                <img src="/api/qrcode?data=<?= $url_for_validation ?>" style="width: 100px; height: 100px;" />
                                            </td>
                                            <td style="width: 100px;text-align: center; padding: 0;margin-top: 5px; font-size: 14px; line-height: 1.2;">
                                                <b>Pathologist:</b> <?= $doctor['name'] ?? '' ?>(<?= $doctor['title'] ?? '' ?>)<br>
                                                <?= '<img style="height:80px;" src="data:image/jpeg;base64,'.base64_encode(  $doctor['digital_signature'] ?? '' ).'"/>' ?>
                                            </td>
                                            <td style="width: 50px; text-align: center;">
                                                <div>
                                                    
                                                    <div>
                                                        
                                                        <div>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td style="width: 50px; text-align: center;">
                                        
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                           
                                        
                                         
                                        
            
       
    </div>

                
                <div class="col-md-12 print-footer" style="position: absolute; bottom: 10px;left: 0px;">
                    System developed by Bluelime Technologies | bluelimeplc.com
                </div>
                </div>
<section class="content"  style="height: 210mm; width: 148.5mm;">
            <div class="row">
                

                <div  style="height: 210mm; width: 148.5mm;">
                    <div id="printableArea">
                        <div class="panel-body" style="background-color: #fff; height: fit-content;">
                        <?= view('Include/_print_header_A5_Portrait'); ?>
                            <h4 style="text-align: center;"><u>Prescription Order</u></h4>
                            <hr>
                            <div style="float: right; width: 50mm;">                                   
                                <div><?php echo "Presctription No"?>:&emsp;<u><?php echo sprintf('%05d', $prescriptions[0]['id'] )?></u></div>
                                <div><?php echo "Date"?>: &emsp;<u><?php echo substr($prescriptions[0]['registered_date'],0,10); ?></u></div>
                            </div>
                            <div style="width: 148.5mm;"> 
                            <br><br><br>
                                <strong> <?php echo "Card No:"?></strong>&emsp;<u><?php echo "MRN-".sprintf('%05d', $prescriptions[0]['patient_id']) ?></u><br>
                                <strong> <?php echo "Patient Name:"?></strong>&emsp;<u><?php echo $prescriptions[0]['patient_name'] ?></u>&emsp;
                                <strong> <?php echo "Age:"?></strong>&emsp;<u><?php echo \BluelimeSystem::year_difference($patient['date_of_birth'],date("Y/m/d")) ?></u>&emsp;
                                <strong> <?php echo "Sex:"?></strong>&emsp;<u><?php echo $patient['sex'] ?></u><br><br>
                                <strong> <?php echo "Visit Date:"?></strong>&emsp;<u><?php echo $prescriptions[0]['registered_date'] ?></u><br><br>
                            </div>
                            <hr style="border: solid 1px;">
                            <div style="min-height: 150px;"<?= ($prescriptions[0]['is_custom']==1)?"hidden":""?>>
                                <table class="table table-bordered " style="color:black;">
                                    <thead>
                                        <tr>
                                            <th>Drug</th>
                                            <th>Route</th>
                                            <th>Direction</th>
                                            <th>NOD</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach($prescription_detail as $detail):
                                               
                                        ?>       
                                              <tr>
                                                <td><?=$detail['drug_name'] ?></td>
                                                <td><?=$detail['route'] ?></td>
                                                <td><?=$detail['direction'] ?></td>
                                                <td><?=$detail['number_of_days'] ?></td>
                                              </tr>      
                                        <?php
                                            endforeach;
                                        ?>    
                                       
                                    </tbody>
                                </table>
                                    
                            </div>
                            <div style="min-height: 150px;" <?= ($prescriptions[0]['is_custom']==1)?"":"hidden"?>>
                                <table class="table table-striped table-hover ">
                                    <thead>
                                        <tr>
                                            <th>Drug</th>
                                            <th>Rx</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach($prescription_detail as $detail):
                                        ?>       
                                              <tr>
                                                <td><?=$detail['drug_name'] ?></td>
                                                <td><?=$detail['custom_direction'] ?></td>
                                              </tr>      
                                        <?php
                                            endforeach;
                                        ?>    
                                       
                                    </tbody>
                                </table>
                                    
                            </div>
                            <hr style="border: solid 1px;">
                            <div style=" bottom: 0px; text-align: center; width:100%">
                                <div style="text-align: right; padding: 5px;">
                                <?php echo '<img style="height:80px;" src="data:image/jpeg;base64,'.base64_encode( $doc_sign ).'"/>' ?><br>
                                    <u>Prescribed By : <b><?= $prescriptions[0]['registered_by_name'] ?></b></u><br>
                                    
                                    

                                </div>
                                <?= view('Include/_print_footer_A5_Portrait'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-left">
                        <a class="btn btn-info" href="#" onclick="doPrint('printableArea')"><span class="fa fa-print"></span></a>
                        <a class="btn btn-info" href="#" onclick="doPrint('printableArea')"><span class="fa fa-telegram"></span></a>
                    </div>
                </div>
            </div>   
        </section>
<section class="content"  style="height: 210mm; width: 148.5mm;">
    <div class="row">
        <div  style="height: 210mm; width: 148.5mm;">
            <div id="printableArea">
                <div class="panel-body" style="background-color: #fff; height: fit-content;">
                
                <?= view('Hospital/Shared/_company_header'); ?> 
                    <h4 style="text-align: center;"><u>Medical Certificate</u></h4>
                    
                    <div style="float: right; width: 50mm;">                                   
                            <div><?php echo "Certificate No"?>:&emsp;<u><?php echo sprintf('%05d', $medical_certificate[0]['id'] )?></u></div>
                            <div><?php echo "Date"?>: &emsp;<u><?php echo substr($medical_certificate[0]['regisered_date'],0,10); ?></u></div>
                        </div>  <br><br>
                        <div style="width: 148.5mm;">  
                    <strong> <?php echo "Card No:"?></strong>&emsp;<u><?php echo "MRN-".sprintf('%05d', $medical_certificate[0]['patient_id']) ?></u><br>
                    <strong> <?php echo "Patient Name:"?></strong>&emsp;<u><?php echo $medical_certificate[0]['patient_name'] ?></u>&emsp;
                    <strong> <?php echo "Age:"?></strong>&emsp;<u><?php echo \BluelimeSystem::year_difference($patient['date_of_birth'],date("Y/m/d")) ?></u>&emsp;
                    <strong> <?php echo "Sex:"?></strong>&emsp;<u><?php echo $patient['sex'] ?></u><br>
                    <strong> <?php echo "Visit Date:"?></strong>&emsp;<u><?php echo $medical_certificate[0]['regisered_date'] ?></u><br>
                    <strong> <?php echo "Referal N<u>o</u>:"?></strong>&emsp;<u><?php echo $medical_certificate[0]['reference'] ?></u>
                    <hr>  
                    </div>
                    <div style="width: 60mm; float: left; margin-left:4mm">
                        <div class="row"  style="width: 148.5mm;">
                            <div style="width: 50mm; float: left;">
                                <strong> 
                                    <?php echo "Diagnosis:" ?>
                                </strong>
                            </div>
                            <div style="width: 130mm; float: left;">
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
                            <div style="width: 130mm; float: left; line-height: 0.5">
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
                            <div style="width: 130mm; float: left;"">
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
                    </div>
                    
                    <div style="min-height: 250px;">
                        
                            
                    </div>
                    <hr style="border: solid 1px;">
                    <div style=" bottom: 0px; text-align: center; width:100%">
                        <div style="text-align: right; padding: 5px;">
                            
                            <?php echo '<img style="height:80px;" src="data:image/jpeg;base64,'.base64_encode( $doc_sign ).'"/>' ?><br
                            <u>Doctor Name : <b><?= $medical_certificate[0]['registered_by_name'] ?></b></u><br>
                            

                        </div>
                        <div style="color: #000; text-align:center; font-size: x-small;">
                                Developed by Bluelime Technologies bluelimeplc@gmail.com 
                            </div>
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
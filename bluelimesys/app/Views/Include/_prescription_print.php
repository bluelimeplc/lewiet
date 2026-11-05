            <?php
                $prescription_group_id = $_GET['prescription_group_id'];
                $print_data = [
                    'document_no' => sprintf('%05d',  $prescription_group_id ),
                    'document_title' => 'የመድሃኒት ማዘዣ<br>Prescription Order',
                    'document_date' =>$prescription_main['registered_date'],
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
            </table>
                <style>

                </style>
                <?php
                    if($prescription_main['is_void']==1){
                        echo '<h1 style="color: red;">Void</h1>' . $prescription_main['void_date'];
                    }
                ?>
                <?php
                $stvoid="";
                    if($prescription_main['is_void']==1){
                        $stvoid = "text-decoration: line-through red 2px;";
                    }
                ?>
                <div style="min-height: 300px; font-size: smaller; <?=$stvoid?>">
                    <?= view('Include/_prescription_print_medicine') ?>
                    <?= view('Include/_prescription_print_compounding') ?>
                    
                    
                </div>
                
                <div class="col-md-12" style="text-align: center; width:100%">
                    <div style="text-align: center; padding: 5px;float: left;">
                    <br><br><br>
                        
                        <b>Dispensed By</b>
                    </div>
                    <div style="text-align: center; padding: 5px;float: right;">
                    <?php echo '<img style="height:60px;" src="data:image/jpeg;base64,'.base64_encode(  $prescription_main['doctor']['digital_signature'] ?? '' ).'"/>' ?><br>
                        <u><?= $prescription_main['doctor']['name'] ?? '' ?></u><br>
                        <?= $prescription_main['doctor']['title'] ?? '' ?><br>
                        <b>Prescribed By<br></b>
                    </div>
                </div>
                <div class="col-md-12 print-footer" style="position: absolute; bottom: 10px;left: 0px;">
                    System developed by Bluelime Technologies | bluelimeplc.com
                </div>
        </section>
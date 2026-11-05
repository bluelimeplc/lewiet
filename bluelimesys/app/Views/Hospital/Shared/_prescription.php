<section class="content">
        <?php
            $print_data = [
                'document_no' => sprintf('%05d', $prescription['id'] ),
                'document_title' => 'የመድሃኒት ማዘዣ<br>Prescription Order',
                'document_date' =>$prescription['registered_date'],
            ];
        ?>
        <?= view('Include/_print_header_A5_Portrait', $print_data) ?>
        <table class="table table-condensed">
            <tr style="border: none">
                <td style="border: none" align="right"><b>Patient Name</b></td><td style="border: none"><?= $prescription['patient_name'] ?></td>
                <td align="right" style="border: none"><b>Card Number</b></td><td style="border: none">MRN-<?= sprintf('%05d', $patient['id'] ) ?></td>
            </tr>
            <tr>
                <td align="right" style="border: none"><b>Age</b></td><td style="border: none"><?php echo \BluelimeSystem::year_difference($patient['date_of_birth'],date("Y/m/d")) ?></td>
                <td align="right" style="border: none"><b>Sex</b></td><td style="border: none"><?= $patient['sex'] ?></td>
            </tr>
        </table>
        
        
            <div style="min-height: 300px;"<?= ($prescription['is_custom']==1)?"hidden":""?>>
                <table class="table table-bordered "  style="border-width:1px;border-color:black;">
                    <thead>
                        <tr>
                            <th style="border-width:1px;border-color:black;">Drug</th>
                            <th style="border-width:1px;border-color:black;">Route</th>
                            <th style="border-width:1px;border-color:black;">Direction</th>
                            <th style="border-width:1px;border-color:black;">NOD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($prescription_detail as $detail):
                                
                        ?>       
                                <tr>
                                <td style="border-width:1px;border-color:black;"><?=$detail['drug_name'] ?></td>
                                <td style="border-width:1px;border-color:black;"><?=$detail['route'] ?></td>
                                <td style="border-width:1px;border-color:black;"><?=$detail['direction'] ?></td>
                                <td style="border-width:1px;border-color:black;"><?=$detail['number_of_days'] ?></td>
                                </tr>      
                        <?php
                            endforeach;
                        ?>    
                        
                    </tbody>
                </table>
            </div>
            <br>
            <br>
            <div class="col-md-12" style="text-align: center; width:100%">
                <div style="text-align: center; padding-right: 5px;float: right;">
                <?php echo '<img style="height:80px;" src="data:image/jpeg;base64,'.base64_encode( $doc_sign ).'"/>' ?><br>
                    <u><?= $prescription['registered_by_name'] ?></u><br>
                    <b>Prescribed By<br></b>
                </div>
            </div>
            <div class="col-md-12 print-footer" style="position: absolute; bottom: 10px;left: 0px;">
                System developed by Bluelime Technologies | bluelimeplc.com
            </div>
    </section>
    <script>
        function print_printable_document(){
            var printContents = document.getElementById('printable').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
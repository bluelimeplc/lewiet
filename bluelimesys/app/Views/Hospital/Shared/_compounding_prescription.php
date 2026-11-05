<section class="content">
        <?php
            $print_data = [
                'document_no' => sprintf('%05d', $prescription['id'] ),
                'document_title' => 'የመድሃኒት ማዘዣ<br>Prescription Order',
                'document_date' =>$prescription['created_at'],
            ];
        ?>
        <?= view('Include/_print_header_A5_Portrait', $print_data) ?>
        <table class="table table-condensed">
            <tr style="border: none">
                <td style="border: none" align="right"><b>Patient Name</b></td><td style="border: none"><?= $patient['first_name'] ?></td>
                <td align="right" style="border: none"><b>Card Number</b></td><td style="border: none">MRN-<?= sprintf('%05d', $patient['id'] ) ?></td>
            </tr>
            <tr>
                <td align="right" style="border: none"><b>Age</b></td><td style="border: none"><?php echo \BluelimeSystem::year_difference($patient['date_of_birth'],date("Y/m/d")) ?></td>
                <td align="right" style="border: none"><b>Sex</b></td><td style="border: none"><?= $patient['sex'] ?></td>
            </tr>
        </table>
        
        
            <div style="min-height: 300px;">
                <div>
                    <table class="table table-bordered">
                        <tr>
                            <td>Route: <b><?= $prescription['route'] ?></b></td>
                            <td>Direction: <b><?= $prescription['direction'] ?></b></td>
                            <td>N<u>o</u> of days: <b><?= $prescription['no_of_days'] ?></b></td>
                        </tr>
                    </table>
                </div>
                <table class="table table-bordered "  style="border-width:1px;border-color:black;">
                    <thead>
                        <tr>
                            <th style="border-width:1px;border-color:black;">Compounding Item</th>
                            <th style="border-width:1px;border-color:black;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($prescription['compounding_detail'] as $detail):
                        ?>       
                            <tr>
                                <td style="border-width:1px;border-color:black;"><?=$detail['item_detail']['item_generic_name'] ?></td>
                                <td style="border-width:1px;border-color:black;"><?=($detail['in_percent']>0)?$detail['in_percent'].'%': $detail['amount'].' '.$detail['measurement'] ?></td>
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
                    <u><?= $prescription['created_by_name'] ?></u><br>
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
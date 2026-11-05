<?php
    //BluelimeSystem::print_associated_array($sales_detail);
    
    $show_vat ??= false;

    if(empty($pos_bill)){
        $pos_bill = [
            'id' => -1,
            'created_at' => '0',
            'pos_bill_json' => [
                'customer_name' => 'Not found',
                'tin' => 'Not found',
            ],
            'fs_number' => '',
            'payment_method' => '',
            'transferred_amount' => '',
        ];
    }
?>
        <?php
            $headerInformationModel = model('System/HeaderInformation');
            $print_data = [
                'document_no' => $pos_bill['id'],
                'document_title' => 'የሽያጭ አባሪ<br>Sales Attachment',
                'document_date' => $pos_bill['created_at'],
                'registered_by' => $pos_bill['registered_by'] ?? 'Pharma',
                'footer1' => 'Dispensed By',
                'footer2' => 'Cashier',
                'company_header' => $headerInformationModel->where('id', 2)->findAll()[0] ?? [],
            ];
        ?>
<?= view('Include/_print_header_A5_Portrait', $print_data) ?>
        <div style="min-height: 400px; padding:5px;">
            <div class="col-lg-12" class="col-lg-12">
                <?= (($pos_bill['id'] == -1) ? '<h1>POS BILL NOT FOUND</h1>' : '') ?>
                <table class="table table-bordered attachment_table">
                    <tr>
                        <td>
                            <b>Customer Name:</b> <?= $pos_bill['pos_bill_json']['customer_name'] ?><br>
                            <b>TIN:</b> <?= $pos_bill['pos_bill_json']['tin'] ?>
                        </td>
                        <td>
                            <b>Date:</b> <?= trim($pos_bill['response_date']) ?><br>
                            <b>FS N<u>o</u>:</b> <?= trim($pos_bill['fs_number']) == '' ? '<h1 class="bg-red">NOT PAID</h1>' : $pos_bill['fs_number'] ?><br>
                            <b>Machine N<u>o</u></b> <?= trim($pos_bill['machine_number']) ?>
                        </td>
                    </tr>
                    <?php
                        if((($pos_bill['pos_bill_json']['company_name']) != "")){
                    ?>
                    <tr>
                        <td colspan="3"><b>Company:</b> <?= $pos_bill['pos_bill_json']['company_name'] ?></td>
                    </tr>
                    <?php
                        }
                    ?>
                </table>
            </div>
            <div class="col-lg-12">
                <table class="table table-bordered  table-condensed attachment_table"  style="font-size: 12px;">
                    <tr>
                        <th>Item</th><th>Qty</th><th>Unit P.</th><th>Total</th><?= $show_vat?'<th>VAT</th>':''?>
                    </tr>
                

                    <!-- Dispensary -->
                    <?php
                        $dispensary_sales = '';
                        foreach($sales_mains as $sales_main){
                            if($sales_main['sales_type'] != BluelimeConfiguration::SALES_TYPE::DISPENSARY)
                                continue;

                            foreach($sales_main['sales_details'] as $sales_detail){
                                $item = $sales_detail['item_detail']['item_brand_name'] ?? '';
                                if(trim($item) !== '')
                                    $item .= ' ';

                                $item .= '<i>' . ($sales_detail['item_detail']['item_generic_name'] ?? '') . '</i> ' . ($sales_detail['item_detail']['preparation'] ?? '') . ' | ' . ($sales_detail['item_detail']['strength'] ?? '');
                                
                                $item = trim(trim($item), '|');

                                $dispensary_sales .= '
                        <tr>
                            <td>'.$item.'</td><td>' . $sales_detail['quantity'] . ' ' . ($sales_detail['item_detail']['measurement'] ?? '') . '</td><td align="right">' . $sales_detail['unit_price'] . '</td><td align="right">'.number_format(($sales_detail['quantity']*$sales_detail['unit_price']),2).'</td>'
                            .($show_vat?'<td>'. ($sales_detail['item_detail']['is_taxable'] ? (number_format(($sales_detail['quantity']*$sales_detail['unit_price']*.15),2)):'') .'</td>':'').
                        '</tr>
                                ';
                            }
                        }

                        echo $dispensary_sales;


                    ?>
                    <!-- Compounding -->
                    <?php
                    $compound_letter = 65;
                    $compounding_sales = '';
                    foreach($sales_mains as $sales_main){
                        if($sales_main['sales_type'] != BluelimeConfiguration::SALES_TYPE::COMPOUNDING)
                            continue;

                    $compounding_sales .= '
                    <tr>
                        <th class="bg-gray" colspan="5">Compounding '. chr($compound_letter++) .'</th>
                    </tr>
                    ';
                        foreach($sales_main['sales_details'] as $sales_detail){
                            $item = $sales_detail['item_detail']['item_brand_name'] ?? '';
                            if(trim($item) !== '')
                                $item .= ' ';

                            $item .= '<i>' . ($sales_detail['item_detail']['item_generic_name'] ?? '') . '</i> | ' . ($sales_detail['item_detail']['preparation'] ?? '');
                            $item = trim(trim($item), '|');
                            $compounding_sales .= '
                    <tr>
                        <td>'.$item.'</td><td>' . $sales_detail['quantity'] . ' ' . ($sales_detail['item_detail']['compounding_measurement'] ?? '') . '</td><td align="right">' . number_format($sales_detail['unit_price'],2) . '</td><td align="right">'.number_format(($sales_detail['quantity']*$sales_detail['unit_price']),2).'</td>'
                        .($show_vat?'<td>'. ($sales_detail['item_detail']['is_taxable'] ? (number_format(($sales_detail['quantity']*$sales_detail['unit_price']*.15),2)):'') .'</td>':'').
                    '</tr>
                            ';
                        }
                    }

                    echo $compounding_sales;
                ?>
                    
                    <tr>
                        <th  colspan="3" style="border-left: 1px solid #cdd0d4;border-bottom: 1px solid #cdd0d4; text-align: right;" title="None taxable total">Sub total (N)</th><td  title="None taxable total" align="right"><?= number_format($sub_total,2) ?></td>
                    </tr>
                    <tr>
                        <th  colspan="3" style="border-left: 1px solid #cdd0d4;border-bottom: 1px solid #cdd0d4; text-align: right;">Taxable total</th><td colspan="" align="right"><?= number_format($taxable_total,2) ?></td>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align: right;">VAT</th><td align="right"><?= number_format($total_vat, 2) ?></td>
                    </tr>
                    <tr>
                        <th  colspan="2" style="text-align: right;"><?=$pos_bill['payment_method']=="Cash"?"":((intval($pos_bill['transferred_amount'])>0)?$pos_bill['payment_method'].": ".$pos_bill['transferred_amount']." | Cash: ".(round($total,2)-$pos_bill['transferred_amount']):$pos_bill['payment_method'])?></th><th style="text-align: right;">TOTAL</th><td align="right"><?= number_format($total,2) ?></td>
                    </tr>
                    <?php
                        if(trim($pos_bill['payment_reference']) != ''){
                            echo '
                    <tr>
                        <td colspan="5">Payment reference: <b>'.$pos_bill['payment_reference'].'</b></td>
                    </tr>';
                        }
                    ?>
                </table>
            </div>
        </div>
<!-- these DIV are opened in _print_header_A5_Portrait -->
    </div>
</div>
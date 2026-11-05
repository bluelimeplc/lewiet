<?php
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
        'document_no' => $sales_main['id'],
        'document_title' => 'የሽያጭ አባሪ<br>Sales Attachment',
        'document_date' => $sales_main['created_at'],
        'registered_by' => $sales_main['registered_by'],
        'footer1' => 'Ordered By',
        'footer2' => 'Cashier',
        'company_header' => $headerInformationModel->where('id', 1)->findAll()[0] ?? [],
    ];
?>
<?= view('Include/_print_header_A5_Portrait', $print_data) ?>
        <div style="min-height: 400px; padding:5px;">
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
                    if((($sales_main['pos_bill_json']['company_name'] ?? '') != "")){
                ?>
                <tr>
                    <td colspan="3"><b>Company:</b> <?= $sales_main['pos_bill_json']['company_name']??"" ?></td>
                </tr>
                <?php
                }
                ?>
            </table>
            <table class="table table-bordered  table-condensed attachment_table"  style="font-size: 12px;">
                <tr>
                    <th>N<u>o</u></th><th>Type</th><th>Item</th><th>Qty</th><th>Price</th>
                </tr>
                <?php
                    $line_items = $sales_main['pos_bill_json']['sales_item_list'];
                    $sn = 1;
                    foreach($line_items as $row){
                        echo '
                <tr>
                    <td>'.$sn++.'</td><td>'.$row['item_type'].'</td><td>'.$row['item_name'].'</td><td>'.$row['quantity'].'</td><td  style="text-align: right;">'.number_format($row['unit_price'],2).'</td>
                </tr>
                        ';
                    }
                ?>
                <tr>
                    <th style="border-left: 1px solid #cdd0d4;border-bottom: 1px solid #cdd0d4; text-align: right;" colspan="4" >Sub total (N)</th><td colspan="" align="right"><?= number_format($sub_total,2) ?></td>
                </tr>
                <tr>
                    <th colspan="4"  style="text-align: right;">Taxable total</th><td align="right"><?= number_format($taxable_total,2) ?></td>
                </tr>
                <tr>
                    <th colspan="4" style="text-align: right;">VAT</th><td align="right"><?= number_format($total_vat, 2) ?></td>
                </tr>
                <tr>
                    <th style="text-align: right;" colspan="2"><?=$pos_bill['payment_method']=="Cash"?"":((intval(value: $pos_bill['transferred_amount'])>0)?$pos_bill['payment_method'].": ".$pos_bill['transferred_amount']." | Cash: ".(round($total,2)-$pos_bill['transferred_amount']):$pos_bill['payment_method'])?></th><th colspan="2" style="text-align: right;">TOTAL</th><td align="right"><?= number_format($total,2) ?></td>
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

<!-- these DIV are opened in _print_header_A5_Portrait -->
    </div>
</div>
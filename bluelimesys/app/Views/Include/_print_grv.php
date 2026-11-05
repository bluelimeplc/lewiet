        <?php
            $document_date = '<span title="Created at">C: '.$grv_main['created_at'] .'</span>';
            if($grv_main['created_at'] != $grv_main['updated_at'])
                $document_date .= '<br>U: <span title="Updated at">'.$grv_main['updated_at'].'</span>';
            
            $print_data = [
                'document_no' => $grv_main['id'] ?? 0,
                'document_title' => 'የእቃ መቀበያ<br>Goods Receiving Voucher',
                'document_date' => $document_date,
                'footer1' => 'Recieved By',
                'footer2' => 'Authorized By',
                'recieved_by' => $grv_main['recieved_by'],
            ];
        ?>
        <?= view('Include/_print_header_A4_Portrait', $print_data) ?>
        
        <table class="table table-condensed title" style="font-size: smaller;margin-bottom: 0px;">
            <tr>
                <td width="100" align="right"><b>Supplier:</b></td><td><?= $grv_main['supplier_detail']['company_name'] ?? '' ?></td>
                <td width="100" align="right"><b>Store Name:</b></td><td><?= $grv_main['store_name'][0] ?? '' ?></td>
                <td width="100" align="right"><b>GRV Code:</b></td><td><?= $grv_main['grv_code'] ?? '' ?></td>
            </tr>
            <tr>
                <td align="right"><b>Invoice No:</b></td><td><?= $grv_main['invoice_no'] ?? '' ?></td>
                <td align="right"><b>FS Number:</b></td><td><?= $grv_main['fs_no'] ?? '' ?></td>
                <td align="right"><b>Date:</b></td><td><?= substr($grv_main['receipt_date_gc']  ?? '',0,10) ?></td>
            </tr>
        </table>
        <table class="table table-condensed data" style="font-size: smaller; margin-bottom: 1px;">
            <thead>
                <tr style="font-weight: bold;">
                    <td>N<u>o</u></td><td>Item Code</td><td>Item Name</td><td>Meas</td><td>Unit Price</td><td>Qty</td><td>Sub Total</td><td>VAT</td><td>Total Price</td><td>Batch N<u>o</u></td><td width="75">Expiry Date</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sn = 0;
                    $row_tr = '';
                    foreach($grv_detail as $grv_line)
                    {
                        $sn++;
                        $row_tr .= '
                        <tr>
                            <td>'.$sn.'</td><td>'.$grv_line['item_detail']['item_code'].'</td>
                            <td><b>'.$grv_line['item_detail']['item_brand_name'].'</b> '.$grv_line['item_detail']['item_generic_name'].' <span class="badge" style="border:solid 1px #ccc !important;">'.$grv_line['item_detail']['strength'].'</span> <span class="badge badge-qty" style="border:solid 1px #ccc !important;">'.$grv_line['item_detail']['preparation'].'</span></td>
                            <td>'.$grv_line['item_detail']['measurement'].'</td>
                            <td class="blurable">'.number_format(round($grv_line['unit_price'],2),2).'</td>
                            <td class="blurable">'.$grv_line['quantity'].'</td>
                            <td>'.number_format(round($grv_line['total_price'],2),2).'</td>
                            <td>'.number_format(round($grv_line['tax'],2),2).'</td>
                            <td>'.number_format(round($grv_line['total_price'] + $grv_line['tax'],precision: 2),2).'</td>
                            <td>'.$grv_line['batch_number'].'</td>
                            <td>'.$grv_line['expiry_date'].'</td>
                        </tr>';
                    }
                    echo $row_tr;
                ?>
                
            </tbody>
        </table>
        <div style="min-width: 250px; float: right; top:-10px">
            <table class="table table-condensed data"  style="font-weight: bold;">
                <tr>
                    <td width="" align="right"><b>Sub Total:</b></td><td align="right"><?= number_format(round($grv_main['sub_total'],2),2) ?></td>
                </tr>
                <tr>
                    <td align="right"><b>VAT:</b></td><td align="right"><?= number_format(round($grv_main['tax'],2),2) ?></td>
                </tr>
                <tr>
                    <td align="right"><b>Total:</b></td><td align="right"><?= number_format(round($grv_main['total'],2),2) ?></td>
                </tr>
            </table>
        </div>
        <table class="table table-bordered table-condensed" style="text-align: center;">
                <tr>
                    <td><?=$grv_main['recieved_by'][0]?></td><td><?=$grv_main['recieved_by'][0]?></td>
                </tr>
                <tr>
                    <td><b>Recieved By</b></td><td><b>Approved By</b></td>
                </tr>
                
            </table>
        
        <div style="width:210mm;">
        </div>
                </div>
                

        <script>
            function change_title(){
                let win_title = 'Bluelime: <?= $grv_main['grv_code'] ?? '' ?>';
                document.title = win_title;
            }
            $(document).ready(function(){
                change_title();
            });
        </script>
        <?php
            $print_data = [
                'document_no' => $siv_main['id'] ?? 0,
                'document_title' => 'የእቃ ማዉጫ<br>Goods Issue Voucher',
                'document_date' => $siv_main['created_at'] ?? 0,
                'footer1' => 'Recieved By',
                'footer2' => 'Authorized By',
                'recieved_by' => $siv_main['issued_by'],
            ];
        ?>
        <?= view('Include/_print_header_A4_Portrait', $print_data) ?>
        <table class="table table-condensed title" style="font-size: smaller;margin-bottom: 0px;">
            <tr>
                <td width="100" align="right"><b>SIV Code:</b></td><td>SIV-<?= sprintf('%04d',$siv_main['id']??'') ?></td>
                <td width="100" align="right"><b>Issuing Store:</b></td><td><?= $siv_main['issuing_store_name'] ?? '' ?></td>
                <td width="100" align="right"><b>Issue Date:</b></td><td><?= substr($siv_main['created_at'],0,10) ?? '' ?></td>
            </tr>
            <tr>
                <td align="right"><b>SRV Code:</b></td><td>SRV-<?= sprintf('%04d',$siv_main['srv_id']??'') ?></td>
                <td align="right"><b>Requesting Store:</b></td><td><?= $siv_main['requesting_store_name'] ?? '' ?></td>
                <td align="right"><b>Description:</b></td><td><?= $siv_main['description']  ?? ''?></td>
            </tr>
        </table>
       
        <table class="table table-condensed table-bordered" style="font-size: smaller; margin-bottom: 1px;">
            <tad>
                <tr style="font-weight: bold;">
                    <td>N<u>o</u></td><td>Item Code</td><td>Item Name</td><td>Meas</td><td>Quantity<br>Issued</td><td>Unit<br>Cost</td><td>Unit Selling<br>Price</td><td>Batch N<u>o</u></td><td>Expiry Date</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sn = 0;
                    $row_tr = '';
                    foreach($siv_detail as $siv_line)
                    {
                        $sn++;
                        $row_tr .= '
                        <tr>
                            <td>'.$sn.'</td>
                            <td title="'.$siv_line['item_id'].'">'.$siv_line['item_detail']['item_code'].'</td>
                            <td><b>'.$siv_line['item_detail']['item_brand_name'].'</b> '.$siv_line['item_detail']['item_generic_name'].' <span class="badge" style="border:solid 1px #ccc !important;">'.$siv_line['item_detail']['strength'].'</span> <span class="badge badge-qty" style="border:solid 1px #ccc !important;">'.$siv_line['item_detail']['preparation'].'</span></td>
                            <td>'.$siv_line['item_detail']['measurement'].'</td>
                            <td class="blurable">'.$siv_line['quantity'].'</td>
                            <td class="blurable">'.number_format(round($siv_line['unit_cost'],2),2).'</td>
                            <td class="blurable">'.number_format(round($siv_line['unit_price'],2),2).'</td>
                            <td>'.$siv_line['batch_no'].($siv_line['grv_id']>0?' <a target="_blank" href="grv_print?grv_id='.$siv_line['grv_id'].'"><span class="badge">GRV-'.$siv_line['grv_id'].'</span></a>':'').'</td>
                            <td>'.$siv_line['expiry_date'].'</td>
                        </tr>';
                    }
                    echo $row_tr;
                ?>
                
            </tbody>
        </table>
        <table class="table table-bordered table-condensed" style="text-align: center;">
                <tr>
                    <td><?= $siv_main['issued_by_name'] ?></td><td><?= $siv_main['received_by_name'] ?></td>
                </tr>
                <tr>
                    <td><b>Issued By</b></td><td><b>Received By</b></td>
                </tr>
            </table>
        </div>
        <div style="width:210mm;">
        </div>

        <script>
            function change_title(){
                let win_title = 'Bluelime: SIV-<?= sprintf('%04d',$siv_main['id']??'') ?>';
                document.title = win_title;
            }
            $(document).ready(function(){
                change_title();
            });
        </script>
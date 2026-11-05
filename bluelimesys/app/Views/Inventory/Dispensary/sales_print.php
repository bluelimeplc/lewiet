<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Dispensary/_left_sidebar_dispensary') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Pharmacy</h1>
            <small>Sales Attachment</small>
            <ol class="breadcrumb">
                <li class="active"><a onclick="print_printable_document()">Print</a></li>
            </ol>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <?php
            $print_data = [
                'document_no' => $sales_main['id'],
                'document_title' => 'የሽያጭ አባሪ<br>Sales Attachment',
                'document_date' => $sales_main['created_at'],
                'registered_by' => $sales_main['registered_by_name'],
                'footer1' => 'Dispensed By',
                'footer2' => 'Cashier',
            ];
        ?>
        <?= view('Include/_print_header_A5_Portrait', $print_data) ?>
        <table class="table table-bordered" style="font-size: smaller;">
            <tr>
                <td width="200"><b>Customer Name:</b> <?= $sales_main['patient_name'] ?></td>
                <td><b>Tin N<u>o</u>:</b> <?= substr($sales_main['tin_number'],0,10) ?></td>
                <td <?= (trim($sales_main['fs_number']) == ''? 'class="bg-red"' : '' )?> ><b>FS N<u>o</u>:</b> <?= (trim($sales_main['fs_number']) == ''?'<b style="color:#FFF; font-size:large;">NOT PAID</b>' : substr($sales_main['fs_number'],0,10)) ?></td>
            </tr>
        </table>
        
        <table class="table table-bordered" style="text-align: right;">
            <thead>
                <tr>
                    <th>N<u>o</u></th><th>Item Name</th><th>Meas</th><th>Qty</th><th>U.Price</th><th>Total</th><th>Vat</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    
                    $sn = 0;
                    $sum = 0;
                    $t_vat = 0;
                    $row_tr = '';
                    foreach($sales_detail as $sales_line)
                    {
                        $sn++;
                        $sum += ($sales_line['quantity']*$sales_line['unit_price']);
                        $t_vat += ($sales_line['quantity']*$sales_line['vat']);
                        $row_tr .= '
                        <tr>
                            <td>'.$sn.'</td>
                            <td>'.$sales_line['item_detail']['item_generic_name'].' '.$sales_line['item_detail']['item_brand_name'].'<br>
                            <span class="badge">'.($sales_line['item_detail']['strength'] ?? '').'</span> <span class="badge badge-qty">'.($sales_line['item_detail']['preparation'] ?? '').'</span>
                            </td>
                            <td>'.($sales_line['item_detail']['measurement']).'</td>
                            <td>'.round($sales_line['quantity'],2).'</td>
                            <td>'.number_format(round($sales_line['unit_price'],2),2).'</td>
                            <td>'.number_format(round(($sales_line['quantity']*$sales_line['unit_price']),2),2).'</td>
                            <td>'.number_format(round($sales_line['quantity']*$sales_line['vat'],2),2).'</td>
                        </tr>';
                    }
                    echo $row_tr;
                    echo '<tr><td colspan="6" align="right">Total</td><td>'.number_format(round($sum,2),2).'</td></tr>';
                    echo '<tr><td colspan="6" align="right">Vat 15%</td><td>'.number_format(round($t_vat,2),2).'</td></tr>';
                    echo '<tr><td colspan="6" align="right">G. Total</td><td>'.number_format(round($sum + $t_vat,2),2).'</td></tr>';
                ?>
            </tbody>
        </table>
        
        <?= view('Include/_print_footer_A5_Portrait') ?>
    </section>
</div>

<?= view('Include/_footer') ?>
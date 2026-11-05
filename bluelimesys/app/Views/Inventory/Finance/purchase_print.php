<?php
    $purchase_main = $this->data['purchase_main'];
    $purchase_detail = $this->data['purchase_detail'];
?>
<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Finance/_left_sidebar_finance') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Store</h1>
            <small>Purchase Request Voucher</small>
            <ol class="breadcrumb">
                <li class="active"><a onclick="print_printable_document()">Print</a></li>
            </ol>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <?php
            $print_data = [
                'document_no' => $purchase_main['id'] ?? 0,
                'document_title' => 'የእቃ ግዥ መጠየቂያ<br>Purchase Request',
                'document_date' => $purchase_main['created_at'] ?? 0,
                'footer1' => 'Requested By',
                'footer2' => 'Authorized By',
                'recieved_by' => $purchase_main['requested_by'],
            ];
        ?>
        <?= view('Include/_print_header_A4_Landscape', $print_data) ?>
        <table class="table-borderless table">
            <tr>
                <td width="100" align="right"><b>Supplier:</b></td><td><?= $purchase_main['supplier_detail']['company_name'] ?? '' ?></td>
                <td width="100" align="right"><b>Store Name:</b></td><td><?= $purchase_main['store_name'][0] ?? '' ?></td>
                <td width="100" align="right"><b>Purchase Code:</b></td><td><?= $purchase_main['purchase_code'] ?? '' ?></td>
            </tr>
            <tr>
                <td align="right"><b>Date:</b></td><td><?= substr($purchase_main['created_at']  ?? '',0,10) ?></td>
            </tr>
        </table>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>N<u>o</u></th><th>Item Code</th><th>Item Name</th><th>Meas</th><th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sn = 0;
                    $row_tr = '';
                    foreach($purchase_detail as $purchase_line)
                    {
                        $sn++;
                        $row_tr .= '
                        <tr>
                            <td>'.$sn.'</td><td>'.$purchase_line['item_detail']['item_code'].'</td><td><b>'.$purchase_line['item_detail']['item_generic_name'].'</b> '.$purchase_line['item_detail']['item_brand_name'].'</td><td>'.$purchase_line['item_detail']['measurement'].'</td><td>'.$purchase_line['quantity'].'</td>
                        </tr>';
                    }
                    echo $row_tr;
                ?>
                
            </tbody>
        </table>
        

        <?= view('Include/_print_footer_A4_Landscape') ?>
    </section>
</div>

<?= view('Include/_footer') ?>
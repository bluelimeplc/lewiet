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
            <small>Purchase Order</small>
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
                'document_title' => 'የእቃ ግዥ ማዘዣ<br>Purchase Order',
                'document_date' => $purchase_main['ordered_date'] ?? 0,
                'registered_by' => $purchase_main['ordered_by'],
                'footer1' => 'Prepared By',
                'footer2' => 'Authorized By',
                'recieved_by' => $purchase_main['ordered_by'][0],
            ];
        ?>
        <?= view('Include/_print_header_A4_Portrait', $print_data) ?>
        <table class="table-borderless table">
            <tr>
                <td width="100" align="right"><b>Supplier:</b></td><td><?= $purchase_main['supplier_detail']['company_name'] ?? '' ?></td>
                <td width="100" align="right"><b>Purchase Code:</b></td><td><?= 'PO'.sprintf('%04d', $purchase_main['id']) ?? '' ?></td>
            </tr>
            <tr>
                <td align="right"><b>Date:</b></td><td><?= substr($purchase_main['ordered_date']  ?? '',0,10) ?></td>
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
        

        <?= view('Include/_print_footer_A4_Portrait') ?>
    </section>
</div>

<?= view('Include/_footer') ?>
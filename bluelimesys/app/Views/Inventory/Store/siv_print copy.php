<?php
    $siv_main = $this->data['siv_main'];
    $siv_detail = $this->data['siv_detail'];
?>
<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Store/_left_sidebar_store') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Store</h1>
            <small>Store Issue</small>
            <ol class="breadcrumb">
                <li class="active"><a onclick="print_printable_document()">Print</a></li>
            </ol>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <?php
            $print_data = [
                'document_no' => sprintf('%04d',$siv_main['srv_id']??''),
                'document_title' => 'የእቃ ማዉጫ<br>Goods Issue Voucher',
                'document_date' => $siv_main['created_at'],
                'footer1' => 'Issued By',
                'footer2' => 'Authorized By',
                'recieved_by' => $siv_main['issued_by'],
            ];
        ?>
        <?= view('Include/_print_header_A4_Landscape', $print_data) ?>
        <table class="table-borderless table">
            <tr>
                <td width="100" align="right"><b>SIV Code:</b></td><td><?= $siv_main['siv_code'] ?? '' ?></td>
                <td width="100" align="right"><b>Issuing Store:</b></td><td><?= $siv_main['issuing_store_name'] ?? '' ?></td>
                <td width="100" align="right"><b>Issue Date:</b></td><td><?= substr($siv_main['created_at'],0,10) ?? '' ?></td>
            </tr>
            <tr>
                <td align="right"><b>SRV Code:</b></td><td>SRV-<?= sprintf('%04d',$siv_main['srv_id']??'') ?></td>
                <td align="right"><b>Requesting Store:</b></td><td><?= $siv_main['requesting_store_name'] ?? '' ?></td>
                <td align="right"><b>Description:</b></td><td><?= $siv_main['description']  ?? ''?></td>
            </tr>
        </table>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>N<u>o</u></th><th>Item Code</th><th>Item Name</th><th>Measurement</th><th>Quantity</th>
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
                            <td>'.$sn.'</td><td>'.$siv_line['item_detail']['item_code'].'</td><td><b>'.$siv_line['item_detail']['item_brand_name'].'</b> '.$siv_line['item_detail']['item_generic_name'].' <span class="badge">'.$siv_line['item_detail']['strength'].'</span> <span class="badge badge-qty">'.$siv_line['item_detail']['preparation'].'</span></td><td>'.$siv_line['item_detail']['measurement'].'</td><td>'.$siv_line['quantity'].'</td>
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
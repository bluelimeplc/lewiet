<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Cashier/_left_sidebar_menu') ?>
<?= view('Include/_left_sidebar_bottom') ?>
    
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1>Cashier</h1>
                <small>Attachment</small>
            </div>
        </section>

        
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-lg-6">
                    <div>Print</div>
                    <?php
                        $headerInformationModel = model('System/HeaderInformation');
                        $print_data = [
                            'document_no' => $sales_main['id'],
                            'document_title' => 'የሽያጭ አባሪ<br>Sales Attachment',
                            'document_date' => $sales_main['created_at'],
                            'registered_by' => $sales_main['registered_by'],
                            'footer1' => 'Dispensed By',
                            'footer2' => 'Cashier',
                            'company_header' => $headerInformationModel->where('id', 2)->findAll()[0] ?? [],
                        ];
                    ?>
                    <?= view('Include/_print_header_A5_Portrait', $print_data) ?>
                    <div style="min-height: 400px; padding:5px;">
                        <table class="table table-bordered">
                            <tr>
                                <td><b>Customer Name:</b> <?= $sales_main['pos_bill']['Customer_Name'] ?></td>
                                <td><b>Tin N<u>o</u>:</b> <?= $sales_main['pos_bill']['Customer_TIN'] ?></td>
                                <td><b>FS N<u>o</u>:</b> <?= $sales_main['fs_number'] ?></td>
                            </tr>
                        </table>
                        <table class="table table-bordered">
                            <tr>
                                <th>N<u>o</u></th><th>Item</th><th>Amount</th>
                            </tr>
                            <?php
                                $sn = 1;
                                foreach($compounding_prescription as $row){
                                    echo '
                            <tr>
                                <td>'.$sn++.'</td><td>'.$row['item_detail']['item_generic_name'].'</td><td>'.($row['in_percent']>0?$row['in_percent'].'%':$row['amount'].$row['measurement']).'</td>
                            </tr>
                                    ';
                                }
                            ?>
                        </table>
                        
                        <table class="table table-bordered">
                            <tr><th>None taxable sub total:</th><td><?= $payment['none_taxable_sub_total'] ?></td></tr>
                            <tr><th>Taxable sub total:</th><td><?= $payment['taxable_sub_total'] ?></td></tr>
                            <tr><th>VAT:</th><td><?= $payment['vat'] ?></td></tr>
                            <tr><th>TOTAL</th><th><?= $payment['total'] ?></th></tr>
                        </table>
                    </div>
                    <?= view('Include/_print_footer_A5_Portrait') ?>
                </div>
            </div>
        </section>
    </div>
    
<?= view('Include/_footer') ?>
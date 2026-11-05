<?php
    

?>
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1>Reception</h1>
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
                            'company_header' => $headerInformationModel->where('id', 1)->findAll()[0] ?? [],
                        ];
                    ?>
                    <?= view('Include/_print_header_A5_Portrait', $print_data) ?>
                    <div style="min-height: 400px; padding:5px;">
                        <table class="table table-bordered">
                            <tr>
                                <td><b>Customer Name:</b> <?= $sales_main['pos_bill_json']['customer_name'] ?></td>
                                <td><b>Tin N<u>o</u>:</b> <?= $sales_main['pos_bill_json']['tin'] ?></td>
                                <td><b>FS N<u>o</u>:</b> <?= trim($pos_bill['fs_number']) == '' ? '<h1 class="bg-red">NOT PAID</h1>' : $pos_bill['fs_number'] ?></td>
                            </tr>
                        </table>
                        <table class="table table-bordered  table-condensed"  style="font-size: 12px;">
                            <tr>
                                <th>N<u>o</u></th><th>Type</th><th>Item</th><th>Qty</th><th>Price</th>
                            </tr>
                            <?php
                                $line_items = $sales_main['pos_bill_json']['sales_item_list'];
                                $sn = 1;
                                foreach($line_items as $row){
                                    echo '
                            <tr>
                                <td>'.$sn++.'</td><td>'.$row['item_type'].'</td><td>'.$row['item_name'].'</td><td>'.$row['quantity'].'</td><td>'.$row['unit_price'].'</td>
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
                        </table>
                    </div>
                </div>
            </div>
        </section> 
    </div>
    
<?= view('Include/_footer') ?>
<script>

    setTimeout(function() {
        print_printable_document();
    }, 1000);

    function print_printable_document(){
        var printContents = document.getElementById('printable').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        close();
    }
</script>


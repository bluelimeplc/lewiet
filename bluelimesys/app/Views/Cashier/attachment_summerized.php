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
                <span style="padding-left: 300px;">
                    <a class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-print"></span></a>
                </span>
            </div>
        </section>

        
        <!-- Main content -->
        <section class="content">
            <div class="row">
            <div class="col-lg-6">
                
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
                    <d style="min-height: 400px; padding:5px;">
                        <div class="col-lg-12" class="col-lg-12" style="top: -20px;">
                        <?= (($pos_bill['id'] == -1) ? '<h1>POS BILL NOT FOUND</h1>' : '') ?>
                        <table class="table table-bordered">
                            <tr>
                                <td><b>Customer Name:</b> <?= $pos_bill['pos_bill_json']['customer_name'] ?></td>
                                <td><b>TIN:</b> <?= $pos_bill['pos_bill_json']['tin'] ?></td>
                                <td><b>FS N<u>o</u>:</b> <?= trim($pos_bill['fs_number']) == '' ? '<h1 class="bg-red">NOT PAID</h1>' : $pos_bill['fs_number'] ?></td>
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
                        <div class="col-lg-12" style="top: -35px;">
                        <table class="table table-bordered  table-condensed"  style="font-size: 12px;">
                            <tr>
                                <th>Item</th><th>Qty</th><th>Unit P.<th>Total</th>
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
                                <td>'.$item.'</td><td>' . $sales_detail['quantity'] . ' ' . ($sales_detail['item_detail']['measurement'] ?? '') . '</td><td>' . $sales_detail['unit_price'] . '</td><td align="right">'.number_format(($sales_detail['quantity']*$sales_detail['unit_price']),2).'</td>
                            </tr>
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
                                <th class="bg-gray" colspan="3">Compounding '. chr($compound_letter++) .'</th>
                            ';
                                $tp = 0;
                                $vat_t = 0;
                                $t = 0;
                                foreach($sales_main['sales_details'] as $sales_detail){
                                    $tp += $sales_detail['quantity'] * $sales_detail['unit_price']; 
                                    $vat_t += $sales_detail['quantity'] * $sales_detail['vat'];
                                    $t += ($sales_detail['quantity'] * $sales_detail['unit_price'])+($sales_detail['quantity'] * $sales_detail['vat']);
                                }
                                $compounding_sales .= "<td align='right'>".number_format($tp,2)."</td></tr>";
                            }

                            echo $compounding_sales;
                        ?>
                            
                            <tr>
                                <th colspan="3" style="border-left: 1px solid #cdd0d4;border-bottom: 1px solid #cdd0d4; text-align: right;" >Sub total</th><td align="right"><?= number_format($sub_total+$taxable_total,2) ?></td>
                            </tr>
                            <tr>
                                <th colspan="3" style="text-align: right;">VAT</th><td align="right"><?= number_format($total_vat, 2) ?></td>
                            </tr>
                            <tr>
                                <th colspan="1" style="text-align: right;"><?=$pos_bill['payment_method']=="Cash"?"":((intval($pos_bill['transferred_amount'])>0)?$pos_bill['payment_method'].": ".$pos_bill['transferred_amount']." | Cash: ".(round($total,2)-$pos_bill['transferred_amount']):$pos_bill['payment_method'])?></th><th colspan="2" style="text-align: right;">TOTAL</th><td align="right"><?= number_format($total,2) ?></td>
                            </tr>

                        </table>
                        </div>
                        
                        <div class="col-lg-12" style="top: -40px;">
                        <?php // view('Include/_print_footer_A5_Portrait') ?>
                    </div>
                    </div>
                    
                </div>
            </div>
        </section>
    </div>
    
<?= view('Include/_footer') ?>
<script>

/*setTimeout(function() {
    print_printable_document();
    }, 1000);
    */

function print_printable_document(){
    var printContents = document.getElementById('printable').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    //close();
}
</script>
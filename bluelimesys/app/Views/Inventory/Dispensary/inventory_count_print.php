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
            <h1>Despensary</h1>
            <small>Inventory Location</small>
            <ol class="breadcrumb">
                
            </ol>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">  
                    <div class="panel-heading">
                        <a role="button" class="btn btn-success" href="/inventory/dispensary/inventory_count_print">Drugs</a>
                        <a role="button" class="btn btn-warning" href="/inventory/dispensary/inventory_count_print?type=cosmetics">Cosmetics</a>
                        <a role="button"  class="btn btn-danger" href="/inventory/dispensary/inventory_count_print?type=compounding&store_id=3">Compounding</a>
                        <a role="button" class="btn btn-primary pull-right" onclick="print_printable_document();"><i class="fa fa-print"></i> Print</a>
                    </div>
                    <div class="panel-body" style="min-height: 700px;">
                        <div id="printable">
                            <style>
                                #inventory_data_table td{
                                    padding: 2px 5px !important;
                                }
                            </style>
                            <table class="table table-bordered table-condensed" id="inventory_data_table">
                                <thead>
                                    <tr>
                                        <th colspan="8">
                                            <div style="float: left; margin-left: 15px;"><br><br>Fiscal Period: ________________<br><br>Ended: ______________________</div>
                                            <div style="float: right; margin-right: 15px;"><br><br>Page: ____________________<br><br>Date: ____________________</div>
                                            <div class="text-center">
                                                <h2 style="margin: 1px;">Gelead Pharmacy #<?= $branch_number ?></h2>
                                                <h3 style="margin: 1px;">Inventory Count Sheet</h3>
                                                <h3 style="margin: 1px;"><b><?= $store_name ?></b></h3>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th width="20">SN</th><th class="text-center" width="450">Item Description</th><th width="50">Unit</th><th width="100">Qty</th><th width="70">Unit Cost</th><th width="70">Unit Price</th><?= ($_GET['show_balance']??0)==1?'<th>Balance</th>':''?><th width="80">Expiry Date</th><th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sn = 1;
                                        foreach($items as $item){
                                            $item_name = '<b>'.$item['item_brand_name'].'</b> ';
                                            if(trim($item['item_brand_name']) != trim($item['item_generic_name']))
                                                $item_name .= $item['item_generic_name'];
                                            
                                            $item_name .= ' <span class="badge">'.$item['strength'].'</span>  <span class="badge badge-qty">'.$item['preparation'].'</span>';
                                            $unit_cost = $item['unit_cost']>0 ? number_format($item['unit_cost'],2) : '-';
                                            $unit_price = $item['unit_price']>0 ? number_format($item['unit_price'],2) : '-';

                                            echo '
                                    <tr>
                                        <td>' . $sn++ . '</td>
                                        <td>' . $item_name . '</td>
                                        <td>' . $item['measurement'] . '</td>
                                        <td> </td>
                                        <td class="text-right">' . $unit_cost . '</td>
                                        <td class="text-right">' . $unit_price . '</td>';

                                         if(($_GET['show_balance']??0)==1)
                                            echo '<td class="text-right">' . $item['current_balance'] . '</td>';
                                        
                                        echo '
                                        <td class="text-right">' . $item['expiry_date'] . '</td>
                                        <td> </td>
                                    </tr>
                                            ';
                                        }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="8">
                                            <br>
                                            <br>
                                            <br><br>
                                            <br>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div style="display: none; position: fixed; bottom: 10px; left: 10px;" id="footer_signature">
                                <b>&emsp13;&emsp13;Registered By: ____________________ Sign: _________ &emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;  Counted By: __________________________ Sign _____________ 
                                    <br><br>
                                    &emsp13;&emsp13;Checked By: ______________________ Sign: _________ &emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13; <?= $store_name ?> #<?= $branch_number ?> Officer: Name: ___________________ Sign: ____________
                                </b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    
    $(document).ready(function() {
        create_data_table();
    });

    function create_data_table(){
        var t = $('#inventory_data_table').DataTable( {
            info: false,
            ordering: false,
            paging: false,
        } );
    }


    function print_printable_document(){
        let data_table = document.getElementById('inventory_data_table_wrapper');
        data_table.childNodes[0].style.display = 'none';
        var originalContents = document.body.innerHTML;
        //$('a').removeAttr('href');
        $('#footer_signature').show();
        // data_table.childNodes[2].style.display = 'none';

        var printContents = document.getElementById('printable').innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        //create_data_table();
        $('#footer_signature').hide();
    }

    $(document).ready(function () {
        var active_menu = '.<?= $active_menu; ?>';
        if (active_menu != '.') {
            $(active_menu).addClass('active');
        }

        var active_sub_menu = '.<?= $active_sub_menu ?? ''; ?>';
        if (active_sub_menu != '.') {
            $(active_sub_menu).addClass('active');
        }
    });
</script>
<?= view('Include/_footer') ?>

<div class="row">
    <div class="panel">
        <div class="panel-body">
            <h4>
                Store: <?= $store_title?><br>
                Item: <?= '<b>'.$selected_item['item_brand_name'].'</b> '.$selected_item['item_generic_name'].' <span class="badge badge-qty" title="Preparation">'.$selected_item['preparation'].'</span> <span class="badge badge-info" title="Strength">'.$selected_item['strength'].'</span> <span class="badge badge-qty-warning"  title="Pharmacy Code">'.$selected_item['item_code'].'</span> <span class="badge badge-qty-red"  title="Peachtree Code">'.$selected_item['peachtree_id'].'</span>' ?></h4>
        </div>
    </div>
    <?php
        $stock_card_number = 0;
        foreach($stock_cards as $stock_card):
            $stock_card_number ++;
    ?>
        <div class="panel panel-bd">
            <div class="panel-heading">
                    <?= !$stock_card['is_current_fiscal_period']?' <button id="card_show_'.$stock_card_number.'" onclick=(show_stock_card_detail('.$stock_card_number.')) style="float: right; margin-left: 2px;" class="btn btn-warning">Show <i class="fa fa-caret-down"></i></button> ':'' ?>
                    <button id="card_print_<?=$stock_card_number?>" onclick="print_stock_card(<?=$stock_card_number?>)" style="float: right;  margin-left: 2px; <?= !$stock_card['is_current_fiscal_period']?' display:none; ':''?>" class="btn btn-info">Print</button> 
                <h4>
                    Fiscal Period: <?= substr($stock_card['fiscal_beginig'],0,10).' - '.substr($stock_card['fiscal_ending'],0,10) ?>&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13; 
                    <small> <b><?=!$stock_card['is_current_fiscal_period']?'Ending balance: '.number_format($stock_card['ending_balance'],2):''?></b></small></b>
                </h4> 
            </div>
            <div class="panel-body" id="card_printable_<?=$stock_card_number?>">
                <div style="display: none;" id="item_name_<?=$stock_card_number?>">
                    Fiscal Period: <?= substr($stock_card['fiscal_beginig'],0,10).' - '.substr($stock_card['fiscal_ending'],0,10)?><br>
                    Store: <?= $store_title?><br>
                    Item: <?= '<b>'.$selected_item['item_brand_name'].'</b> '.$selected_item['item_generic_name'].' <span class="badge badge-qty">'.$selected_item['preparation'].'</span> <span class="badge badge-info">'.$selected_item['strength'].'</span> <span class="badge badge-qty-warning">'.$selected_item['item_code'].'</span> <span class="badge badge-qty-red">'.$selected_item['peachtree_id'].'</span>' ?>
                    <hr>
                </div>
                <div id="card_detail_<?=$stock_card_number?>" style="<?= !$stock_card['is_current_fiscal_period']?' display:none; ':'' ?>">
                    <table class="table table-bordered" id="report_table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Supplier Name</th>
                                <th>Qty</th>
                                <th>Balance</th>
                                <th>Unit<br>Cost</th>
                                <th>Unit<br>Selling<br>Price</th>
                                <th>Batch No</th>
                                <th>Expiry Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sn = 0;
                                $balance = 0;
                                foreach($stock_card['transactions'] as $transaction)
                                {
                                    $balance = ($balance + floatval($transaction['quantity']));
                                    $sn++;
                                    echo '
                                        <tr>
                                            <td>'.date('Y-m-d', strtotime($transaction['created_at'])).'</td>
                                            <td title="'.$transaction['transaction_type'].'">'.($transaction['transaction_type']==BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::RECEIVE?'<i class="fa fa-arrow-circle-down text-danger"></i> GRV-'.$transaction['transaction_reference']:($transaction['transaction_type']==BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::DISPENSE_INTERNAL?'<i class="fa fa-arrow-circle-up" style="color:green;"></i> SIV-'.$transaction['transaction_reference']:($transaction['transaction_type']==BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::RECEIVE_INTERNAL?'<i class="fa fa-arrow-circle-down text-danger"></i> SIV-'.$transaction['transaction_reference']:((($transaction['transaction_type']==BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::DISPENSE_SALES)||($transaction['transaction_type']==BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::COMPOUNDING_SALES))?'<i class="fa fa-money text-success"></i> '.$transaction['transaction_reference']:$transaction['transaction_reference'].' <span class="badge">'.$transaction['transaction_type'].'</span>')))).'</td>
                                            <td>'.($transaction['supplier_name']??'').'</td>
                                            <td>'.$transaction['quantity'].'</td>
                                            <td>'.$balance.'</td>
                                            <td align="right">'.number_format($transaction['unit_cost'],2).'</td>
                                            <td>'.number_format($transaction['unit_price'],2).'</td>
                                            <td>'.$transaction['batch_number'].'</td>
                                            <td>'.$transaction['expiry_date'].'</td>
                                        </tr>
                                    ';
                                }
                            
                            // <div class="badge badge-qty" style="position: absolute; top: 0; right: 0; margin: 5px;"><?= $sn </div>

                            ?>
                            <tr><td colspan="4" style="text-align: right; font-weight: bolder;">Balance</td><td style="font-weight: bolder;"><?=number_format(round($stock_card['ending_balance'],2),2)?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        endforeach;
    ?>
</div>
<script>
    function show_stock_card_detail(stock_card_number){
        let btn_card_show = $('#card_show_'+stock_card_number);
        let btn_card_print = $('#card_print_'+stock_card_number);
        let card_printable = $('#card_printable_'+stock_card_number);
        let card_detail = $('#card_detail_'+stock_card_number);

        card_detail.show();
        btn_card_print.show();
        btn_card_show.html('Hide <i class="fa fa-caret-up"></i>')
        btn_card_show.attr('onclick', 'hide_stock_card_detail('+stock_card_number+')')
    }

    function hide_stock_card_detail(stock_card_number){
        let btn_card_show = $('#card_show_'+stock_card_number);
        let btn_card_print = $('#card_print_'+stock_card_number);
        let card_printable = $('#card_printable_'+stock_card_number);
        let card_detail = $('#card_detail_'+stock_card_number);

        card_detail.hide();
        btn_card_print.hide();
        btn_card_show.html('Show <i class="fa fa-caret-up"></i>')
        btn_card_show.attr('onclick', 'show_stock_card_detail('+stock_card_number+')')
    }

    function print_stock_card(stock_card_number){
        $('#item_name_'+stock_card_number).show();
        print_printable('card_printable_'+stock_card_number);
        $('#item_name_'+stock_card_number).hide();
    }
</script>
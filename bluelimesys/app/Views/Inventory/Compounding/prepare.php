<?php
    echo view('Include/_header')
        .view('Include/_left_sidebar_top')
        .view('Inventory/Dispensary/_left_sidebar_dispensary')
        .view('Include/_left_sidebar_bottom');
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Compounding</h1>
            <small>Prepare</small>
            <ol class="breadcrumb">
                <li role="button">
                    <a href="/inventory/compounding/list">Compounding Prescriptions List</a>
                </li>
            </ol>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?= view('Include/_cart_menu'); ?>
            <div class="col-lg-12" style="display:<?= (empty($com_pre_detail) ? 'none;':'block;') ?>">
                <style>
                    .compunding_menu{
                        display: inline-block;
                        padding: 10px;
                        border-radius: 5px;
                        margin: 2px;
                    }
                    .compunding_menu_active{
                        font-weight: bold;
                        border:solid 2px purple;
                    }
                </style>
                <div class="panel panel-bd panel-form">
                    <div class="panel-body">
                        <?php
                            $cnt = 1;
                            foreach($com_pre_friends as $row){
                                echo '<a class="compunding_menu bg-';
                                
                                if($row['is_sold'])
                                    echo 'gray';
                                else
                                    echo 'info';

                                if($row['id']== $com_pre_id)
                                    echo ' compunding_menu_active ';
                                
                                echo '" ';
                                if($row['id'] != $com_pre_id)
                                    echo ' href="/inventory/compounding/prepare?id='.$row['id'].'" ';

                                echo '>Compounding '.$cnt++.'</a>';
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel panel-bd panel-form">
                    <div class="panel-body">
                        <div class="col-lg-12">
                            <div style="border-bottom: solid 2px; margin-bottom:40px; <?= (empty($com_pre_detail) ? 'display: none;':'') ?>">
                                <table class="table table-bordered">
                                    <?php
                                        foreach($com_pre_detail as $line){
                                            echo '
                                    <tr id="com_pre_detail_'.$line['item_id'].'">
                                        <td><span class="badge badge-qty" style="float:right">'.$line['item_detail']['preparation'].'</span><b>'.$line['item_detail']['item_brand_name'].'</b><br>'.$line['item_detail']['item_generic_name'].'</td>
                                        <td>'.($line['amount']+$line['in_percent']).' '.$line['measurement'].'</td>
                                        <td><button class="btn btn-info" onclick="add_compounding_item_from_prescription(\''.$line['item_id'].'\', \''.($line['amount']+$line['in_percent']).'\', \''.$line['measurement'].'\')">+ <i class="fa fa-arrow-right"></i></button></td>
                                    </tr>
                                            ';
                                        }
                                    ?>
                                </table>
                            </div>
                            <div>
                                <div class="col-lg-5"  style="padding-right:1; padding-left:0;">
                                    <label for="sales_detail_item">Item</label><br>
                                    <input type="text" class="form-control" id="prescription_item">

                                </div>
                                <div class="col-lg-2"  style="padding-right:1; padding-left:0;">
                                    <label for="current_balance ">Amount</label>
                                    <input type="number" min="0" class="form-control" id="prescription_amount">
                                </div>
                                <div class="col-lg-2"  style="padding-right:1; padding-left:0;">
                                    <label for="sales_detail_unit_price ">Measurement</label>
                                    <select class="form-control" id="prescription_measurment">
                                        <option></option>
                                        <option value="%">%</option>
                                </select>
                                </div>
                                <div class="col-lg-1"  style="padding-right:0; padding-left:0;">
                                    <label for="add_button">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</label>
                                    <button class="form-control btn btn-success" onclick="add_compounding_item()">+ <i class="fa fa-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel panel-bd panel-form">
                    <div class="panel-body">
                        <div class="<?= empty($com_pre_main)?'hide':'' ?>">
                            <table class="table table-bordered bg-warning">
                                <tr>
                                    <td><b>Patient name:</b> <?= $com_pre_main['patient_detail']['name']??'' ?></td>
                                    <td><b>Age:</b> <?= $com_pre_main['patient_detail']['age']??'' ?></td>
                                    <td><b>Mobile:</b> <?= $com_pre_main['patient_detail']['mobile']??'' ?></td>
                                </tr>
                            </table>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th><th width="50">Amount</th><th width="20">Price</th><th width="10"></th>
                                </tr>
                            </thead>
                            <tbody id="compounding_prescription_table">
                                
                            </tbody>
                            <tfoot>     
                                <tr style="border-top: solid 2px;">
                                    <td colspan="2" align="right">
                                        <b>N<u>o</u> of items</b>
                                    </td>
                                    <td colspan="2"><span id="no_of_items">0</span></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">
                                        <b>Sub total</b>
                                    </td>
                                    <td colspan="2"><span id="sub_total">0</span></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">
                                        <b>Taxable total</b>
                                    </td>
                                    <td colspan="2"><span id="taxable_total">0</span></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">
                                        <b>VAT</b>
                                    </td>
                                    <td colspan="2"><span id="vat_total">0</span></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right">
                                        <b>TOTAL</b>
                                    </td>
                                    <td colspan="2"><b><u><span id="total">0</span></u></b></td>
                                </tr>
                                <tr style="border-top: solid 2px;">
                                    <td colspan="2">
                                        <div class="form-group">
                                            <label for="compounding_process">Compounding Service</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="compounding_process">
                                                <a class=" input-group-addon btn btn-success" role="button" onclick="add_compounding_service_and_calulate()">+ <i class="fa fa-arrow-up"></i></a>
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <?php
                            if((trim($com_pre_main['description'] ?? '') != '')){
                                echo '
                                <div>
                                    <b><u>Description:</u></b><br>
                                    '.(str_replace("\n", "<br>", subject: $com_pre_main['description'])).'
                                </div>
                                ';
                            }
                        ?>
                    </div>
                    <div class="panel-footer">
                        <div class="text-right">
                            <button class="btn btn-primary" onclick="save_and_add_to_cart()">Save <i class="fa fa-cart-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>   
    </section>
</div>

<script>
    var compounding_list = {};
    var total_amount = 0;
    var no_of_item = 0;
    var sub_total = 0;
    var taxable_total = 0;
    var vat_total = 0;
    var total_price = 0;

    function add_compounding_item(){
        if(!validate_prescription_form())
            return 0;

        let item = items[selected_item_index['comp_selected_item_index']];
        let amount = $('#prescription_amount').val();
        let measurement = $('#prescription_measurment').val();
        let in_percent = 0;
        if(measurement == '%'){
            in_percent = amount;
            amount = 0;
        }

        let new_line = {'item_id':item.id, 'amount':amount, 'in_percent':in_percent, 'price':0, 'item_detail':item,};
        compounding_list['item_id_'+item.id] = new_line;
        let compounding_service_price = get_compounding_service_price_then_remove();
        calculate_amount_and_price();
        add_compounding_service(compounding_service_price);
        calculate_amount_and_price();
        create_compounding_prescription_line();
        clear_after_add();
        $('#com_pre_detail_'+item.id).addClass('hide');
    }

    function add_compounding_item_from_prescription(item_id, amount, measurement){
        item = items['item_id_'+item_id];
        let in_percent = 0;
        if(measurement == '%'){
            in_percent = amount;
            amount = 0;
        }
        let new_line = {'item_id':item.id, 'amount':amount, 'in_percent':in_percent, 'price':0, 'item_detail':item,};
        compounding_list['item_id_'+item.id] = new_line;
        let compounding_service_price = get_compounding_service_price_then_remove();
        calculate_amount_and_price();
        add_compounding_service(compounding_service_price);
        calculate_amount_and_price();
        create_compounding_prescription_line();
        $('#com_pre_detail_'+item.id).addClass('hide');
    }

    function add_compounding_item_from_prescription_auto(){
        <?php
            foreach($com_pre_detail as $line)
                echo "add_compounding_item_from_prescription('".$line['item_id']."', '".($line['amount']+$line['in_percent'])."', '".$line['measurement']."'); \n";
        ?>
    }

    $(document).ready(function(){
        add_compounding_item_from_prescription_auto();
    });

    function add_compounding_service_and_calulate(){
        let compounding_service = Number($('#compounding_process').val());
        $('#compounding_process').val('');
        add_compounding_service(compounding_service);
        calculate_amount_and_price();
        create_compounding_prescription_line();
    }

    function add_compounding_service(compounding_service_user_entered){
        compounding_service = Number(compounding_service_user_entered);
        if(compounding_service <= 0)
            return 0;

        let compounding_service_old = 0;

        if(compounding_list['compounding_service'] != undefined)
            compounding_service_old = Number(compounding_list['compounding_service']['price']);
        compounding_service_old = compounding_service_old * 1.15;

        total_price -= compounding_service_old;
        let five_fixer = 5 - Number(parseFloat((total_price + compounding_service) % 5).toFixed(2));
        if(five_fixer == 5)
            five_fixer = 0;
        compounding_service += five_fixer;
        
        let before_vat = Number(parseFloat(compounding_service / 1.15).toFixed(2));

        let new_line = {'item_id':0, 'amount':1, 'in_percent':0, 'price':0, 'item_detail':{'item_generic_name':'Taxable', 'item_brand_name':'Compounding service','preparation':'','is_taxable':1,'compounding_unit_price':before_vat,'compounding_measurement':'', 'user_entered':compounding_service_user_entered, 'item_type':'COMPOUNDING'}};

        compounding_list['compounding_service'] = new_line;
    }

    function get_compounding_service_price_then_remove(){
        let compounding_service_price = 0;
        if(compounding_list['compounding_service'] != undefined){
            compounding_service_price = Number(compounding_list['compounding_service']['item_detail']['user_entered']);
            delete compounding_list['compounding_service'];
        }
        return compounding_service_price;
    }

    function calculate_amount_and_price(){
        total_amount = get_total_amount();
        no_of_item = 0;
        sub_total = 0;
        taxable_total = 0;
        vat_total = 0;
        total_price = 0;
        for (let key in compounding_list) {
            if(compounding_list[key]['in_percent'] > 0){
                compounding_list[key]['amount'] = Number(parseFloat(total_amount * compounding_list[key]['in_percent'] / 100 ).toFixed(2));
            }

            compounding_list[key]['price'] = Number(parseFloat(compounding_list[key]['amount'] * compounding_list[key]['item_detail']['compounding_unit_price']).toFixed(2));
            compounding_list[key]['current_balance'] = 0;
            compounding_list[key]['vat'] = 0;
            
            if(compounding_list[key]['item_detail']['is_taxable'] == 1){
                compounding_list[key]['vat'] = Number(parseFloat(compounding_list[key]['price'] * 0.15).toFixed(2));
                vat_total += Number(compounding_list[key]['vat']);
                taxable_total += Number(compounding_list[key]['price']);
            }
            else
                sub_total += Number(compounding_list[key]['price']);
            
            no_of_item++;
        }
        sub_total = Number(parseFloat(sub_total).toFixed(2));
        taxable_total = Number(parseFloat(taxable_total).toFixed(2));
        vat_total = Number(parseFloat(vat_total).toFixed(2));
        total_price = Number(parseFloat(Number(sub_total) + Number(taxable_total) + Number(vat_total)).toFixed(2));
    }

    function get_total_amount(){
        total_amount = 0;

        for (let key in compounding_list) {
            if(compounding_list[key]['item_id'] <= 0)
                continue;
            if(compounding_list[key]['in_percent'] == 0)
                total_amount += Number(parseFloat(compounding_list[key]['amount']));
        }
        return total_amount;
    }

    function create_compounding_prescription_line(){
        let item = null;
        let compounding_prescription_table = $('#compounding_prescription_table');
        let rows = '';

        for (let key in compounding_list) {
            
            item = compounding_list[key]['item_detail'];
            let clr = '';
            if(parseFloat(item.current_balance) < parseFloat(compounding_list[key]['amount'])){
                clr = 'red';
            }
            rows += '<tr>'+
                    '<td><span class="badge badge-qty" style="float:right">' + item.preparation + '</span><span class="badge badge-danger" style="float:right">' + parseFloat(item.current_balance).toFixed(2) + '</span><b>'+ item.item_brand_name +'</b><br>' +  item.item_generic_name + '</td>'+
                    '<td style="background-color:'+clr+'">' + compounding_list[key]['amount'] + ' ' + item['compounding_measurement'];
            if(compounding_list[key]['in_percent'] > 0)
                rows += '<br><span class="badge">'+compounding_list[key]['in_percent'] +'%</span>';

            rows +=  '</td>'+
                    '<td>' + compounding_list[key]['price'] + '</td>'+
                    '<td><a role="button" class="fa fa-minus-circle text-danger" title="Remove line" onclick="remove_compounding_line(\''+key+'\')"></a></td>'+
                  '</tr>';
        }

        compounding_prescription_table.html(rows);

        $('#no_of_items').html(no_of_item);
        $('#sub_total').html(sub_total);
        $('#taxable_total').html(taxable_total);
        $('#vat_total').html(vat_total);
        $('#total').html(parseFloat(total_price).toFixed(0));
    }

    function remove_compounding_line(line_index){
        show_item_on_comp_pre_list(compounding_list[line_index]['item_id']);
        delete compounding_list[line_index];
        let compounding_service_price = get_compounding_service_price_then_remove();
        calculate_amount_and_price();
        add_compounding_service(compounding_service_price);
        calculate_amount_and_price();
        create_compounding_prescription_line();
    }

    function show_item_on_comp_pre_list(item_id){
        $('#com_pre_detail_'+item_id).removeClass('hide');
    }

    function clear_after_add(){
        let prescription_item = $('#prescription_item');
        let prescription_amount = $('#prescription_amount');
        let prescription_measurment = $('#prescription_measurment');

        prescription_item.val('');
        prescription_amount.val('');
        let option = '<option></option><option value="%">%</option>';
        prescription_measurment.html(option);
        selected_item_index['comp_selected_item_index'] = -1;
        prescription_item.focus();
    }

    function validate_prescription_form(){
        let isValid = true;
        if(selected_item_index['comp_selected_item_index'] == -1){
            toastr['error']('Item is not selected');
            isValid = false;
            $('#prescription_item').focus();
        }

        if(Number($('#prescription_amount').val()) <= 0){
            toastr['error']('Amount must not be zero.');
            if(isValid)
                $('#prescription_amount').focus();
            isValid = false;
        }

        if($('#prescription_measurment').val() == ''){
            toastr['error']('Measurement is not selected.');
            if(isValid)
                $('#prescription_measurment').focus();
            isValid = false;
        }

        return isValid;
    }

    function validate_save(){
        let isValid = true;

        if(Object.keys(compounding_list).length == 0){
            toastr['error']('Compounding item are empty.');
            prescription_item.focus();
            isValid = false;
        }

        if(compounding_list['compounding_service'] == undefined){
            toastr['error']('Compunding service is not added.');
            if(isValid)
                $('#compounding_process').focus();
            isValid = false;
        }

        return isValid;
    }

    function change_measurement(){
        let item = items[selected_item_index['comp_selected_item_index']];
        let prescription_amount = $('#prescription_amount');
        let prescription_measurment = $('#prescription_measurment');
        let option = '<option></option><option value="'+item.compounding_measurement+'">'+item.compounding_measurement+'</option><option value="%">%</option>'
        prescription_measurment.html(option);
        prescription_amount.focus();
    }

    function save_and_add_to_cart(){
         if(!validate_save())
             return 0;
        
        let prescription_id = <?= $com_pre_id ?>;

        $.ajax({
            url: '/inventory/compounding/save_in_cart',
            method: 'POST',
            data: {'compounding_list':compounding_list, prescription_id:prescription_id},
            dataType: 'json',
            async: false,
            success:function(result){
                if(result.status == 'success'){
                    if(result.value > 0)
                        location = '/inventory/compounding/prepare?id='+result.value;
                    else
                        location = '/inventory/compounding/prepare';
                }
                else
                    toastr['error'](result.message);

            },
            error:function(jqXHR,status,error){
                toastr['error'](error);
            }
        });
    }
</script>

<?= view('Include/_autocomplete_item') ?>
<script>
    var items = <?= \BluelimeSystem::convert_array_to_string($items) ?>;
    selected_item_index['comp_selected_item_index'] = -1;
    var auto_complete_option = {};
    
    auto_complete_option = {
        input: document.getElementById("prescription_item"),
        items: items,
        selected_index: 'comp_selected_item_index',
        callback_function: change_measurement,
        //show_current_balance: true,
    };
    autocomplete_item_by_options(auto_complete_option);
    //autocomplete_item(document.getElementById("prescription_item"), items, 'comp_selected_item_index', change_measurement);
</script>
<?php
    echo view('Include/_footer');
?>
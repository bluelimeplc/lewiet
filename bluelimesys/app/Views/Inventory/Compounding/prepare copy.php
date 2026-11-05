<?php

    $compounding_service = [
        'GRADE_1' => ['price'=>50, 'title'=>'Compounding Service I (50)'],
        'GRADE_2' => ['price'=>100, 'title'=>'Compounding Service II (100)'],
        'GRADE_3' => ['price'=>200, 'title'=>'Compounding Service III (200)'],
        'GRADE_4' => ['price'=>300, 'title'=>'Compounding Service IV (300)']
    ];

    echo view('Include/_header')
        .view('Include/_left_sidebar_top')
        .view('Inventory//Dispensary/_left_sidebar_dispensary')
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
                
            </ol>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title">
                            Patient Detail
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table">
                            <tr>
                                <td width="80">MRN:</td><td><?= sprintf('%05d',$compounding_prescription['patient_id']) ?></td>
                                <td width="150">Patient Name:</td><td><?= $compounding_prescription['patient_name'] ?></td>
                            </tr>
                            <tr>
                                <td>Mobile:</td><td><?= $compounding_prescription['mobile'] ?></td>
                                <td>Doctor Name:</td><td><?= $compounding_prescription['doctor_name'] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title">
                            Preparation
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Item</th><th width="100">Amount</th><th width="50"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $prescriptions = $compounding_prescription['prescriptions'];
                                            $row_index = 0;
                                            foreach($prescriptions as $row){
                                                $item = $items['item_id_'.$row['item_id']];
                                                $row_index++;
                                                echo '
                                        <tr id="prescription_row_'.$row_index.'">
                                            <input type="hidden" class="item_id" value="'.$row['item_id'].'">
                                            <input type="hidden" class="amount" value="'.$row['amount'].'">
                                            <input type="hidden" class="measurment" value="'.$item['compounding_measurement'].'">

                                            <td>
                                                <b>'.$item['item_generic_name'].'</b> 
                                                '.$item['item_brand_name'].'
                                                <span class="badge">'.$item['item_code'].'</span>
                                            </td>
                                            <td class="amount_td">'.$row['amount'].' '.$item['compounding_measurement'].'<br>
                                                <span class="badge '.(($item['current_balance'] <= 0) ? 'badge-qty-red' : (($item['current_balance'] <= $item['min_balance']) ? 'badge-qty-warning' : 'badge-qty')).'" title="Available quantity">'.$item['current_balance'].' '.$item['compounding_measurement'].'</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-success" onclick="add_prescription_row('.$row_index.')">+</button>
                                            </td>
                                        </tr>
                                                ';
                                            }
                                        ?>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" id="compounding_item">
                                            </td>
                                            <td id="td_compounding_amount">
                                                <input type="number" class="form-control" id="compounding_amount" >&nbsp;<span id="span_compounding_measurement"></span>
                                                <div id="div_compounding_current_balance"></div>
                                            </td>
                                            <td>
                                                <button class="btn btn-success" onclick="add_compounding_row()">+</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    
                                </table>
                            </div>

                            <div class="col-lg-6">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr><th>Item</th><th>Amount</th><th>Price</th><th></th></tr>
                                    </thead>
                                    <tbody id="prepared_items">

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
                                                    <label for="compounding_process">Compounding Process</label>
                                                    <div class="input-group">
                                                        <select id="compounding_process" class="form-control">
                                                            <option></option>
                                                            <option value="GRADE_1">Grade 1 &nbsp;&nbsp;(50)</option>
                                                            <option value="GRADE_2">Grade 2 (100)</option>
                                                            <option value="GRADE_3">Grade 3 (200)</option>
                                                            <option value="GRADE_4">Grade 4 (300)</option>
                                                        </select>
                                                        <a class=" input-group-addon btn btn-success" role="button" onclick="add_compounding_service()">+</a>
        
                                                    </div>
                                                </div>
                                                

                                            </td>
                                            <td colspan="2">
                                                <div class="text-right">
                                                    <button class="btn btn-primary" onclick="save_and_send_to_pos()">Send to cashier</button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </section>
</div>

<script>
    var total_price = 0;
    var sales_item_list = {};
    function add_prescription_row(row_index){
        let prescription_row      = document.getElementById('prescription_row_'+row_index);
        let item_id     = Number(prescription_row.getElementsByClassName('item_id')[0].value);
        let amount      = Number(prescription_row.getElementsByClassName('amount')[0].value);
        let measurment  = prescription_row.getElementsByClassName('measurment')[0].value;
        let item       = items['item_id_'+item_id];

        if(amount > Number(item['current_balance'])){
            prescription_row.getElementsByClassName('amount_td')[0].style.backgroundColor = '#fcc';
            //return 0;
        }
        items['item_id_'+item_id]['current_balance'] -= amount;

        let price = amount * item['compounding_unit_price'];
        price = parseFloat(price).toFixed(2);
        if(price <= 0)
            price = 0.01;
        prescription_row.hidden = true;

        let new_tr = '<tr id="sales_row_'+row_index+'"><td>'+item['item_generic_name']+' ' + item['item_brand_name'] + '</td>'
                     +'<td>' + amount + ' ' + measurment +'</td>'
                     +'<td>' + price + '</td>'
                     +'<td><a role="button" class="fa fa-minus-circle text-danger" onclick="remove_sales_line('+row_index+')"></a></td>'
                     +'</tr>';

        let prepared_items = document.getElementById('prepared_items');
        prepared_items.innerHTML += new_tr;

        let vat = 0;
        if(Number(item['is_taxable']) == 1)
            vat = price * 0.15;
        vat =parseFloat(vat).toFixed(2);
        let sales_item = {'item_id':item_id,'item_name':item['item_generic_name']+' ('+ item['item_brand_name']+')', 'amount':amount,'price':price, 'vat':vat};

        sales_item_list['index_' + row_index] = sales_item;

        calculate_total();
    }

    var prescription_item_count = <?= (count($prescriptions)+1) ?>;
    function add_compounding_row(){
        
        if(selected_item_index['comp_selected_item_index'] == -1)
            return 0;

        let amount      = Number($('#compounding_amount').val());
        let item        = items[selected_item_index['comp_selected_item_index']];
        let item_id     = item['id'];
        let measurment  = item['compounding_measurement'];

        let row_index = prescription_item_count;
        prescription_item_count++;
        
        if(amount > Number(item['current_balance'])){
            $('#td_compounding_amount').css('background-color','#fcc');
            //return 0;
        }
        items[selected_item_index['comp_selected_item_index']]['current_balance'] = item['current_balance'] - amount;

        let price = amount * item['compounding_unit_price'];
        price = parseFloat(price).toFixed(2);
        if(price <= 0)
            price = 0.01;
        
        let new_tr = '<tr id="sales_row_'+row_index+'"><td>'+item['item_generic_name']+' ' + item['item_brand_name'] + '</td>'
                     +'<td>' + amount + ' ' + measurment +'</td>'
                     +'<td>' + price + '</td>'
                     +'<td><a role="button" class="fa fa-minus-circle text-danger" onclick="remove_sales_line('+row_index+')"></a></td>'
                     +'</tr>';

        let prepared_items = document.getElementById('prepared_items');
        prepared_items.innerHTML += new_tr;

        let vat = 0;
        if(Number(item['is_taxable']) == 1)
            vat = price * 0.15;
        vat =parseFloat(vat).toFixed(2);
        let sales_item = {'item_id':item_id,'item_name':item['item_generic_name']+' ('+ item['item_brand_name']+')', 'amount':amount,'price':price, 'vat':vat};

        sales_item_list['index_' + row_index] = sales_item;

        calculate_total();
        clear_new_item();
    }

    var compounding_service = <?= BluelimeSystem::convert_array_to_string($compounding_service) ?>;
    //{'GRADE_1':{'price':100, 'title':'Compounding Service I'}, 'GRADE_2':{'price':200, 'title':'Compounding Service II'}, 'GRADE_3':{'price':300, 'title':'Compounding Service III'}};
    var compounding_service_count = 0;

    function add_compounding_service(){
        let compounding_process = $('#compounding_process').val();
        
        if(compounding_process == '')
            return 0;

        let my_compounding_service = compounding_service[compounding_process];

        let row_index = prescription_item_count;
        prescription_item_count++;
        
        let price = my_compounding_service['price'];
        if((total_price % 5) > 0)
            price = my_compounding_service['price'] + (5 - (total_price % 5));
        let before_vat = price / 1.15;
        let vat = before_vat * 0.15;

        before_vat = parseFloat(before_vat).toFixed(2);
        vat = parseFloat(vat).toFixed(2);
        
        let new_tr = '<tr id="sales_row_'+row_index+'"><td>' + my_compounding_service['title'] + '</td>'
                     +'<td> </td>'
                     +'<td>' + before_vat + '</td>'
                     +'<td><a role="button" class="fa fa-minus-circle text-danger" onclick="remove_sales_line_compounding_service('+row_index+')"></a></td>'
                     +'</tr>';

        let prepared_items = document.getElementById('prepared_items');
        prepared_items.innerHTML += new_tr;

        let sales_item = {'item_id':compounding_process,'item_name':my_compounding_service['title'], 'amount':1,'price':before_vat, 'vat':vat};

        sales_item_list['index_' + row_index] = sales_item;
        compounding_service_count++;
        calculate_total();
    }

    function clear_new_item(){
        $('#compounding_item').val('');
        $('#compounding_amount').val('');
        $('#div_compounding_current_balance').html('');
        $('#span_compounding_measurement').html('');
        selected_item_index['comp_selected_item_index'] = -1;
        $('#compounding_item').focus();
    }

    function remove_sales_line(row_index){
        let sales_row = document.getElementById('sales_row_'+row_index);
        
        items['item_id_'+sales_item_list['index_'+row_index]['item_id']]['current_balance'] += Number(sales_item_list['index_'+row_index]['amount']);

        delete sales_item_list['index_'+row_index];
        sales_row.remove();

        let prescription_row = document.getElementById('prescription_row_'+row_index);
        if(prescription_row != null)
            prescription_row.hidden = false;
        
        calculate_total();
    }

    function remove_sales_line_compounding_service(row_index){
        let sales_row = document.getElementById('sales_row_'+row_index);
        delete sales_item_list['index_'+row_index];
        sales_row.remove();
        compounding_service_count--;
        calculate_total();
    }

    function calculate_total(){
        let no_of_items     = Object.keys(sales_item_list).length;
        let sub_total       = 0;
        let taxable_total   = 0;
        let vat_total       = 0;
        let total           = 0;

        $.each(sales_item_list, function(i, item) {
            if(item['vat'] > 0)
                taxable_total += Number(item['price']);
            else
                sub_total += Number(item['price']);

            vat_total += Number(item['vat']);
            total += Number(Number(item['price']) + Number(item['vat']));
        });
        
        sub_total = parseFloat(sub_total).toFixed(2);
        taxable_total = parseFloat(taxable_total).toFixed(2);
        vat_total = parseFloat(vat_total).toFixed(2);
        total = parseFloat(total).toFixed(2);
        total_price = total;

        $('#no_of_items').html(no_of_items);
        $('#sub_total').html(sub_total);
        $('#taxable_total').html(taxable_total);
        $('#vat_total').html(vat_total);
        $('#total').html(total);
    }

    function show_selected_item_detail(){
        let item = items[selected_item_index['comp_selected_item_index']];
        $('#span_compounding_measurement').html(item['compounding_measurement']);
        let current_balance_badge = '<span title="Available quantity" class="badge ';

        if(Number(item['current_balance']) <= 0)
            current_balance_badge += 'badge-qty-red';
        else if(Number(item['min_balance']) >= Number(item['current_balance']))
            current_balance_badge += 'badge-qty-warning';
        else
            current_balance_badge += 'badge-qty';

        current_balance_badge += '">'+item['current_balance']+' '+item['compounding_measurement']+'</span>';

        $('#div_compounding_current_balance').html(current_balance_badge);
    }

    function save_and_send_to_pos(){
        if(!validate_compounding())
            return false;

        let compounding_prescription_id = <?= $compounding_prescription['id'] ?>;
        let patient_detail = {patient_id:"<?= $compounding_prescription['patient_id']?>",name:"<?= $compounding_prescription['patient_name']?>", mobile:"<?= $compounding_prescription['mobile'] ?>"};
        $.ajax({
            url: '/inventory/compounding/sale_compounding',
            method: 'POST',
            data:{sales_item_list:JSON.stringify(sales_item_list), compounding_prescription_id:compounding_prescription_id, patient_detail:JSON.stringify(patient_detail)},
            dataType: 'JSON',
            success:function(result){
                window.location = '/inventory/dispensary';
            }
        });
    }

    function validate_compounding(){
        let is_valid = true;
        if(Object.keys(sales_item_list).length == 0){
            toastr['error']('There must be at least one item added.');
            is_valid = false;
        }
        if(compounding_service_count == 0){
            toastr['error']('Compounding service is not added.');
            is_valid = false;
            $('#compounding_process').focus();
        }
        
        return is_valid;
    }

    function add_all_prescriptions_at_first(){
        <?php
            $row_index = 0;
            foreach($prescriptions as $row){
                $item = $items['item_id_'.$row['item_id']];
                $row_index++;
                echo "
                add_prescription_row('$row_index');
                ";
            }
        ?>
    }

    $(document).ready(function() {
        add_all_prescriptions_at_first();
    });
</script>

<?= view('Include/_autocomplete_item') ?>
<script>
    var items = <?= \BluelimeSystem::convert_array_to_string($items) ?>;
    //selected_item['comp_selected_item_index'] = -1;
    //autocomplete_item(document.getElementById("compounding_item"), items, 'comp_selected_item_index', show_selected_item_detail);

    //selected_item_index['comp_selected_item_index'] = -1;
    var autocomplete_comp_item_option = {
        input:document.getElementById("compounding_item"),
        items:items,
        selected_index:'comp_selected_item_index',
        callback_function: show_selected_item_detail,
    };
    autocomplete_item_by_options(autocomplete_comp_item_option);
</script>
<?php
    echo view('Include/_footer');
?>
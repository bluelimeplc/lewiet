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
            <h1>Dispensary</h1>
            <small>Checkout</small>
            <ol class="breadcrumb">
                <li class="active" role="button"><a role="button" href="/inventory/dispensary/on_pos">On POS</a></li>
            </ol>
        </div>
    </section>
    <style>
        .list-group-item .badge{
            font-size:unset;
        }
        .list-group-item{
            margin-bottom: 10px !important;
            cursor: pointer;
        }
    </style>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-4">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">Sales list</div>
                    <div class="panel-body">
                        <div class="list-group">
                            <?php
                                foreach($sales_main as $row){
                                    echo '<a class="list-group-item" onclick="select_sales(\''.$row['id'].'\')" id="sales_id_'.$row['id'].'">
                                            '.(trim($row['patient_name'])!='' ?trim($row['patient_name']):'&nbsp');
                                    if($row['sales_type'] == BluelimeConfiguration::SALES_TYPE::COMPOUNDING)
                                        echo '<span class="badge">C</span>';
                                    echo '
                                            <span class="badge" id="sales_sub_total_'.$row['id'].'">'.round($row['sub_total'],2).'</span>
                                        </a>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">Selected sales</div>
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th><th width="100">Qty</th><th width="50">Price</th>
                                </tr>
                            </thead>
                            <tbody id="sales_detail">
                            </tbody>
                            <tfoot>
                                <tr style="border-top:solid 2px;">
                                    <td colspan="2" align="right"><b>TOTAL</b></td><td><b><u id="total_price">0.00</u></b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4" id="customer_info">
                <div class="panel panel-bd panel-form">
                    <div class="panel-body">
                        <input style="transform: scale(1.5);" type="checkbox" value="Credit" name="is_credit" id="is_credit" onchanges="change_invoice_type()" />
                            <label class="form-check-label" for="is_credit"> Credit</label><br>
                        <label id="customer_label"> Customer Name:</label> <div><input type="text" class="form-control" id="customer_name"></div>
                        <label> Company:</label> <div><input type="text" class="form-control" id="company_name"></div>
                        <label> TIN:</label> <input type="number" class="form-control" id="tin" maxlength="10"><br>
                        <div style="padding: 5px; border: solid 1px;">
                            <label> Reciept N<u>o</u>:</label> <input type="number" class="form-control" id="receipt_no" maxlength="20"><br>
                            <label> Date:</label> <input type="date" class="form-control" id="paid_date" ><br>
                            <button class="btn btn-success" id="btn_send_to_cashier" onclick="send_to_pos()">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </section>
</div>

<script>
    var sales_list = <?= BluelimeSystem::convert_array_to_string($sales_main) ?>;
    var selected_sales = {};
    var selected_total = 0;
    function select_sales(sales_id){
        if(selected_sales['sales_id_'+sales_id] != undefined){
            selected_total -= selected_sales['sales_id_'+sales_id]['sub_total'];
            delete selected_sales['sales_id_'+sales_id];
            $('#sales_id_'+sales_id).removeClass('active');
            $('.sales_group_'+sales_id).remove();
        }
        else{
            selected_sales['sales_id_'+sales_id] = sales_list['sales_id_'+sales_id];
            add_sales_detail(sales_id);
            $('#sales_id_'+sales_id).addClass('active');
            
            if((selected_sales['sales_id_'+sales_id]['patient_name'].trim() != '') && (selected_sales['sales_id_'+sales_id]['patient_name'].trim().toLowerCase() != 'walk in'))
                $('#customer_name').val(selected_sales['sales_id_'+sales_id]['patient_name'].trim());
        }
        $('#total_price').html(parseFloat(selected_total).toFixed(2));
    }

    function add_sales_detail(sales_id){
        let sales_detail_table = $('#sales_detail');
        let sales_detail = sales_list['sales_id_'+sales_id]['sales_detail'];

        let html = '';
        let row_cnt = 0;
        let sales_detail_count = Object.keys(sales_detail).length;
        for(let key in sales_detail){
            let price = (Number(sales_detail[key]['unit_price']) + Number(sales_detail[key]['vat'])) * Number(sales_detail[key]['quantity']);

            html += '<tr class="sales_group_'+sales_id+'" id="'+key+'"><td><b>'+sales_detail[key]['item_detail']['item_brand_name']+'</b><br>'+sales_detail[key]['item_detail']['item_generic_name']+'</td>';
            html += '<td>'+sales_detail[key]['quantity']+' ';
            html += (sales_list['sales_id_'+sales_id]['sales_type'] == '<?= BluelimeConfiguration::SALES_TYPE::COMPOUNDING ?>') ? sales_detail[key]['item_detail']['compounding_measurement']:sales_detail[key]['item_detail']['measurement'];
            html += '</td>';
            html += '<td align="right">'+parseFloat(price).toFixed(2)+'</td>';
            if(sales_list['sales_id_'+sales_id]['sales_type'] == '<?= BluelimeConfiguration::SALES_TYPE::DISPENSARY ?>')
                html += '<td><a role="button" class="fa fa-remove text-danger" onclick="remove_sales_line('+sales_id+','+sales_detail[key]['id']+')"></a></td>';
            else if(row_cnt == 0){
                html += '<td style="background-color:#dff;" rowspan="'+(sales_detail_count+1)+'">'
                //html += '   <a role="button" class="fa fa-edit" onclick="edit_compounding('+sales_id+')"></a><br><br><br>';
                html += '   <a role="button" class="fa fa-trash text-danger" onclick="delete_compounding('+sales_id+')"></a>';
                html += '</td>';
            }
            html += '</tr>';
            row_cnt++;
        }
        html += '<tr class="sales_group_'+sales_id+'" style="background-color:#ddd;"><td colspan="2"></td><td align="right"><span id="sales_total_'+sales_id+'">'+parseFloat( selected_sales['sales_id_'+sales_id]['sub_total'] ).toFixed(2)+'</span></td>';
        sales_detail_table.html(html+sales_detail_table.html());
        selected_total += Number(selected_sales['sales_id_'+sales_id]['sub_total']);
    }

    var wait_till_i_finish = false;
    function prevent_sending_again(){
        wait_till_i_finish = true;
        $('#btn_send_to_cashier').attr('disabled',true);
    }

    function enable_sending_again(){
        wait_till_i_finish = false;
        $('#btn_send_to_cashier').removeAttr('disabled');
    }

    function send_to_pos(){
        
        if(!validate_form())
            return false;

        if(wait_till_i_finish)
            return false;

        prevent_sending_again();

        let customer_name = $('#customer_name').val();
        let company_name = $('#company_name').val();
        let tin = $('#tin').val();
        var is_credit = $('#is_credit').is(':checked');
        if(is_credit){
            is_credit = 1;
        }
        else{
            is_credit = 0;
        }
        let receipt_no = $('#receipt_no').val().trim();
        let paid_date = $('#paid_date').val();

        $.ajax({
            url: '/inventory/dispensary/combine_and_send_to_pos_past_posting',
            method: 'POST',
            data: {'sales_list':selected_sales, 'customer_name':customer_name,'company_name':company_name, 'tin':tin, 'is_credit':is_credit, 'receipt_no':receipt_no,'paid_date':paid_date},
            dataType: 'json',
            async: false,
            success:function(result){
                if(result.status=='success')
                    location = '/inventory/dispensary/checkout';
                else{
                    toastr['error'](result.message);
                    enable_sending_again();
                }
            },
            error:function(jqXHR,status,error){
                toastr['error'](error);
                enable_sending_again();
            }
        });
    }

    function validate_form(){
        let is_valid = true;
        if(Object.keys(selected_sales).length == 0){
            is_valid = false;
            toastr['error']('Please select sales from the list.');
        }
        
        // <label> Reciept N<u>o</u>:</label> <input type="number" class="form-control" id="receipt_no" maxlength="20"><br>
        //                     <label> Date:</label> <input type="date" class="form-control" id="paid_date" ><br>
        if($('#receipt_no').val().trim() == ''){
            is_valid = false;
            toastr['error']('Receipt Number cannot be empty.');
            flash_red_inputbox('receipt_no');
        }

        if($('#paid_date').val().trim() == ''){
            is_valid = false;
            toastr['error']('Date cannot be empty.');
            flash_red_inputbox('paid_date');
        }
        return is_valid;
    }

    function remove_sales_line(sales_id, sales_detail_id){
        //remove_sales_line_row(sales_id, sales_detail_id);
        $.ajax({
            url: '/inventory/dispensary/remove_sales_line',
            method: 'POST',
            data: {'sales_id':sales_id, 'sales_detail_id':sales_detail_id},
            dataType: 'json',
            async: false,
            success:function(result){
                if(result.status=='success'){
                    remove_sales_line_row(sales_id, sales_detail_id);
                }
                else
                    toastr['error'](result.message);
            },
            error:function(jqXHR,status,error){
                toastr['error'](error);
            }
        });
    }

    function remove_sales_line_row(sales_id, sales_detail_id){
        let selected_line = sales_list['sales_id_'+sales_id]['sales_detail']['sales_detail_id_'+sales_detail_id];
        let price = (Number(selected_line['unit_price']) + Number(selected_line['vat'])) * Number(selected_line['quantity']);
        let sub_total = sales_list['sales_id_'+sales_id]['sub_total'];

        delete sales_list['sales_id_'+sales_id]['sales_detail']['sales_detail_id_'+sales_detail_id];
        delete selected_sales['sales_id_'+sales_id]['sales_detail']['sales_detail_id_'+sales_detail_id];
        
        selected_total -= price;
        sub_total -= price;
        sales_list['sales_id_'+sales_id]['sub_total'] = sub_total;
        $('#total_price').html(parseFloat(selected_total).toFixed(2));
        $('#sales_total_'+sales_id).html(sub_total);
        $('#sales_sub_total_'+sales_id).html(sub_total);

        $('#sales_detail_id_'+sales_detail_id).remove();

        if(Object.keys(sales_list['sales_id_'+sales_id]['sales_detail']).length == 0){
            $('.sales_group_'+sales_id).remove();
            $('#sales_id_'+sales_id).remove();
            delete selected_sales['sales_id_'+sales_id];
        }
    }

    function delete_compounding(sales_id){
        $.ajax({
            url: '/inventory/dispensary/remove_sales_line',
            method: 'POST',
            data: {'sales_id':sales_id, 'sales_detail_id':"<?= BluelimeConfiguration::SALES_TYPE::COMPOUNDING ?>"},
            dataType: 'json',
            async: false,
            success:function(result){
                if(result.status=='success'){
                    let sub_total = sales_list['sales_id_'+sales_id]['sub_total'];
                    selected_total -= sub_total;

                    $('#total_price').html(parseFloat(selected_total).toFixed(2));
                    
                    delete selected_sales['sales_id_'+sales_id];
                    delete sales_list['sales_id_'+sales_id];
                    $('#sales_id_'+sales_id).remove();
                    $('.sales_group_'+sales_id).remove();
                }
                else
                    toastr['error'](result.message);
            },
            error:function(jqXHR,status,error){
                toastr['error'](error);
            }
        });
    }

    <?php
        $sales_id_list = $_GET['sales_id_list'] ?? '';
        $sales_id_list = explode(',', $sales_id_list);
        
        $auto_selector_script = '';

        foreach($sales_id_list as $sales_id){
            if(intval($sales_id) != 0)
                $auto_selector_script .= "\nselect_sales($sales_id);";
        }
        
        if($auto_selector_script != ''){
            $auto_selector_script = '$( document ).ready(function() {
                '.$auto_selector_script.'
            });
            ';
        }

        echo $auto_selector_script;
    ?>

</script>
<?= view('Include/_autocomplete_customer') ?>
<?= view('Include/_autocomplete_company') ?>
<script>
    
    var customers = <?= \BluelimeSystem::convert_array_to_string($customers) ?>;
    var company = <?= \BluelimeSystem::convert_array_to_string($company) ?>;
    selected_customer['sales_selected_customer_index'] = -1;
    autocomplete_customer(document.getElementById("customer_name"), customers, "sales_selected_customer_index");
    selected_company['sales_selected_company_index'] = -1;
    autocomplete_company(document.getElementById("company_name"), company, "sales_selected_company_index");
    
</script>
<script>
    $(document).ready(function () {
        $("#customer_label").html("Customer Name: ");
    });
    function change_invoice_type(){
        var is_credit = $('#is_credit').is(':checked');
        if(is_credit){
            $("#customer_label").html("Company Name: ");
            $('#customer_info').css('background-color', '#5f1905');
        }
        else{
            $("#customer_label").html("Customer Name: ");
            $('#customer_info').css('background-color', '');
        }
    }
</script>

<?php
    echo view('Include/_footer');
?>
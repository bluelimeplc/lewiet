<?php 
   //BluelimeSystem::show_and_die($prescription_detail); 
?>

<link href="/css/autocomplete.css" rel="stylesheet" type="text/css" />


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Pharmacy</h1>
            <small>Sales</small>
            <ol class="breadcrumb">
                <li class="active" role="button"><a role="button" href="/inventory/dispensary/sales_list">Sales List</a></li>
            </ol>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row">
            <?= view('Include/_cart_menu'); ?>
            <div class="col-lg-6">
                <div class="panel panel-bd panel-form">
                    <div class="panel-body">
                        <div class="portlet-body form">
                            
                            <div class="col-lg-12">
                                <div class="col-lg-5"  style="padding-right:1; padding-left:0;">
                                    <label for="sales_detail_item">Item</label><br>
                                    <input type="text" class="form-control" id="sales_detail_item">

                                </div>
                                <div class="col-lg-2"  style="padding-right:1; padding-left:0;">
                                    <label for="current_balance ">Balance</label>
                                    <input type="text" class="form-control" id="current_balance" readonly>
                                </div>
                                <div class="col-lg-2"  style="padding-right:1; padding-left:0;">
                                    <label for="sales_detail_unit_price ">U.Price</label>
                                    <input type="text" class="form-control" id="sales_unit_price"
                                        readonly>
                                </div>
                                <div class="col-lg-2"  style="padding-right:1; padding-left:0;">
                                    <label for="sales_detail_quantity">Quantity</label><br>
                                    <input type="number" min="1" class="form-control" id="sales_detail_quantity">
                                </div>
                                <div class="col-lg-1"  style="padding-right:0; padding-left:0;">
                                    <label
                                        for="add_button">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</label>
                                    <button id="add_button" type="button" class="form-control btn btn-primary"
                                        onclick="add_sales_line()"><i
                                            class="fa fa-cart-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-bd panel-form">
                    <div id="prescription">
                        <div class="panel-body  table-responsive">
                            <div>
                                <?php
                                helper('form');
                                    $attributes = array('class' => 'form-horizontal','role'=>'form');
                                    echo form_open_multipart('/inventory/dispensary', $attributes);
                                ?>
                                <div class="col-sm-8">
                                    <input id="search_prescription" type="text"
                                        placeholder="<?php echo  "Search Prescription" ?>" name="search_prescription"
                                        class="form-control" value="<?= @$searchkey ?>">
                                    <input type="hidden" id="registered_by" value="<?= $logged_user  ?>">

                                </div>
                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-success">
                                        Search <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <?php
                                echo form_close();
                            ?>
                        </div>

                        <table class="table table-sm" style="font-size:11px;" id="prescription_list">
                            <thead>
                                <tr>
                                    <th>Card No</th>
                                    <th>Patient Name</th>
                                    <th>Mobile</th>
                                    <th>Doctor</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($prescriptions as $printout_list):  ?>
                                <tr id="<?=$printout_list['prescription_group_id']?>">
                                    <td><?php echo sprintf('%05d',$printout_list['patient_id'])?> <i title="Hide Prescription" data-toggle="tooltip" data-html="true" style="color:red;cursor:pointer;" class="fa fa-eye-slash" aria-hidden="true" onclick=prescriptionhide(<?=$printout_list['prescription_group_id']?>)></i></td>
                                    <td><?php echo $printout_list['patient_name']?></span></td>
                                    <td><?php echo $printout_list['mobile']?></td>
                                    <td><?php echo $printout_list['registered_by_name']?></td>
                                    <td><?php echo $new_date_format = date('Y-m-d', strtotime($printout_list['registered_date']));?>
                                    </td>
                                    <td>
                                        <a id="prescription_view_button"
                                            onclick="view_prescription_printout(<?=$printout_list['prescription_group_id']?>, '<?=$printout_list['patient_name']?>', <?=$printout_list['patient_id']?>)"
                                            class="btn btn-primary btn-xs"> <i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach ?>

                            </tbody>
                        </table>

                    </div>
                    <div id="view_prescription">

                    </div>
                    
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <input type="hidden" name="patient_id" id="patient_id" class="form-control" required placeholder="">
                            <input type="hidden" name="prescription_id" id="prescription_id" class="form-control" required placeholder="">
                            <input type="hidden" name="prescription_group_id" id="prescription_group_id" class="form-control" required placeholder="">
                            <input type="hidden" name="patient_name" id="patient_name">
                            <input type="hidden" name="tin_no" id="tin_no" >

                            <!--div class="col-lg-12">
                                <div class="form-group">
                                    <br>
                                    <label for="patient_name" class="col-md-4 control-label"
                                        style="text-align: left;">Customer Name</label>
                                    <div class="col-md-8">
                                        <input type="text" name="patient_name" id="patient_name" class="form-control" required placeholder="Customer Name">
                                        </div>
                                </div>
                                <div class="form-group">
                                    <label for="tin_no" class="col-md-4 control-label" style="text-align: left;">Tin
                                        Number:</label>
                                    <div class="col-md-8">
                                        <input type="text" name="tin_no" id="tin_no" class="form-control" required
                                             placeholder="Tin Number">
                                    </div>
                                </div>
                            </div-->
                        </div>
                    </div>
                    <div class="panel panel-bd panel-form">
                        <div class="panel-body table-responsive">
                            <div class="portlet-body form">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <td width="250">
                                                <label for="sales_detail_item">Item</label><br>
                                            </td>

                                            <td width="100">
                                                <label for="sales_detail_quantity">Quantity</label><br>

                                            </td>
                                            <td width="100">
                                                <label for="sales_detail_unit_price ">U.Price</label>

                                            </td>
                                            <td width="100">
                                                <label for="current_balance ">Total</label>

                                            </td>

                                            <td width="100">
                                                <label for="current_balance ">Vat</label>

                                            </td>
                                            <td>
                                                <label
                                                    for="add_button">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</label>

                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody id="sales_detail_line">

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                        <tr style="background-color: #aaa;">
                                            <td>N<u>o</u> of items: <span id="sales_detail_no_of_items"></span></td>
                                            <td colspan="2">Total price: <span id="sales_detail_total_price"></span></td>
                                        </tr>

                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer" align="right">
                        <button class="btn btn-primary"  id="btn_hold" onclick="save_sales()">Hold <i class="fa fa-cart-plus"></i></button>
                        <button class="btn btn-danger"  id="btn_send_to_payment" onclick="save_sales(true)">Send to payment</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div style="height: 350px;">

            </div>
        </div>
        <div class="row hidden">
            <div class="col-lg-6">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title">
                            Compounding
                        </div>
                    </div>
                    <div class="panel-body">
                        
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="panel panel-bd panel-form">
                    <div class="panel-body">
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div>
    <div class="container">
        <!-- Trigger the modal with a button -->
        <!-- New General modal -->
        <div class="modal fade" id="sendToPOSModal" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><span>Send to POS</span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12" style="margin: 4px;">
                                <div class="col-lg-3"><label for="customer_name">Customer name: </label></div>
                                <div class="col-lg-8"><input type="text" class="form-control" id="customer_name"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12" style="margin: 4px;">
                                <div class="col-lg-3"><label for="customer_name">TIN: </label></div>
                                <div class="col-lg-8"><input type="text" class="form-control" id="customer_name"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="addNewGeneral_BtnAdd" type="button" class="btn btn-success" onclick="">Add</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var auto_compelete = <?= BluelimeSystem::convert_array_to_string(($auto_complete_data ?? [])) ?>;
    function show_add_new_modal(title, name){
        
        $('#sendToPOSModal').modal('show');
    }
</script>



<script>
var item = $('#sales_detail_item');
var unit_price = $('#sales_unit_price');
var quantity = $('#sales_detail_quantity');
var current_balance = $('#current_balance');
var batch_no = $('#sales_detail_batch_no');
var prescription_view = document.getElementById("prescription_view");
var did = 0;


var sales_detail_data = {};
var number_of_line = 0;

function view_prescription_printout(prescription_group_id,patient_name, patient_id) {
        document.getElementById("patient_name").value = patient_name;
        document.getElementById("patient_id").value = patient_id;
        document.getElementById("prescription_group_id").value = prescription_group_id;
        $.ajax({
            url: '/inventory/dispensary/view_prescription',
            data: {prescription_group_id: prescription_group_id},
            method: 'POST',
            async: false,
            success: function(result) {
                var container = $("#prescription_view");
                show_prescription_detail(result);
                $('#btn_send_to_payment').addClass('invisible');
                //alert(result);
            },
            error: function(jqXHR, status, error) {
                alert(error);
            }
        });
}

function hide_prescription_detail() {
    var prescription_detail = document.getElementById("view_prescription");
    var prescription_list = document.getElementById("prescription_list");
    document.getElementById("patient_name").value = "";
    document.getElementById("patient_id").value = "";
    document.getElementById("prescription_id").value = "";
    prescription_list.style.display = "table";
    prescription_detail.style.display = "none";
    $('#btn_send_to_payment').removeClass('invisible');
}

function show_prescription_detail(result)
    {
        let prescription_detail = $('#view_prescription');
        let voidp = "";
        const getAge = birthDate => Math.floor((new Date() - new Date(JSON.parse(result)[0].date_of_birth).getTime()) / 3.15576e+10)
        document.getElementById("prescription_list").style.display = "none";
        document.getElementById("view_prescription").style.display = "block";
        if(JSON.parse(result)[0].is_void==1){
            voidp ="text-decoration: line-through red 2px;";
        }
        let html_table = '<div class="panel panel-bd panel-form">'+
                            '<div class="panal-body">'+
                                '<div class="portlet-body form">'+
                                    '<strong>&emsp;'+JSON.parse(result)[0].patient_name+'&emsp;|&emsp;MRN-'+JSON.parse(result)[0].patient_id+'&emsp;|&emsp;</strong>'+
                                    '<strong>'+JSON.parse(result)[0].sex+'</strong>&emsp;|&emsp;'+
                                    '<strong>Age: '+getAge()+'</strong>&emsp;&emsp;'+
                                    '<button type="button"  data-toggle="tooltip" data-html="true" title="Print Prescription" class="btn btn-success btn-xs" style="float:left" onclick="print_prescription('+JSON.parse(result)[0].prescription_group_id+')"><i class="fa fa fa-print"></i></button>'+
                                    '<button type="button"  data-toggle="tooltip" data-html="true" title="Close Prescription" class="btn btn-danger btn-xs" style="float:right" onclick="hide_prescription_detail()"><i class="fa fa-eye-slash"></i></button>'+
                                '</div>'+
                                '<div class="panel panel-bd panel-form">'+
                                    '<table class="table table-bordered table-hover table-sm" style="font-size: 12px;">'+
                                        '<thead>'+
                                            '<tr><th width="200px">Drug</th><th>Route</th><th>Direction</th><th>Days</th><th>Balance</th><th>Quantity</th>'+
                                        '</thead>'+
                                        '<tbody style="'+voidp+'">';
                    
                                        $.each(JSON.parse(result), function(i, item) {
                                            if(item.type == 'Compounding')
                                               return 0;

                                            html_table += '<tr><td><span style="float:right; text-align:right;"><span class="badge" style="margin:2px;">'+item.strength+'</span><br><span class="badge badge-qty" style="margin:2px;">'+item.preparation+'</span></span><b>'+item.item_brand_name+'</b><br>'+item.item_generic_name+'</td>';
                                            if(item.custom_direction.trim() == '')
                                                html_table += '<td>'+item.route+'</td><td>'+item.direction+'</td><td>'+item.number_of_days+'</td>';
                                            else
                                                html_table += '<td colspan="3">'+item.custom_direction.replaceAll("\n","<br>")+'</td>';
                                            
                                            html_table += '<td><input  value="'+Number(item.current_balance)+'" readonly type="text" class="form-control input-sm" id="prescription_current_balance_'+item.drug_id+'"></td>'+
                                            '<td><input type="number" min="1" class="form-control input-sm" id="prescription_detail_quantity_'+item.drug_id+'" ></td>'+
                                            '<td><button id="add_button" type="button" class="btn btn-primary btn-xs" onclick="add_prescription_line_to_sales_line('+item.drug_id+')"><i class="fa fa-cart-plus"></i></button></td></tr>';
                                        });

                                        $.each(JSON.parse(result), function(i, item) {
                                            if(item.type == 'Compounding'){
                                                html_table += '<tr><td colspan="5" style"padding-left:100px;"><b>Compounding</b></td><td>'+item.compounding_count+'</td><td><a href="/inventory/compounding/prepare?id='+item.compounding_id+'" target="_blank" title="Compounding" class="btn btn-warning btn-xs"><i class="fa fa-bars"></i></a></td><tr>';

                                            }
                                        });

            html_table +=               '</tbody>'+
                                    '</table>'+
                                    '<strong style="float:right;">Prescribed By: '+JSON.parse(result)[0].registered_by_name+'&emsp;</strong>'+
                                '</div>'+
                                
                            '</div>'+
                        '</div>';

        prescription_detail.html(html_table);  
    }

function add_sales_line() {
    if(!validate_add_sales_line())
            return false; 

    number_of_line++;

    let selected_item = items[selected_item_index['sales_selected_item_index']];
    did = selected_item.id;

    let tax = 0;
    if(selected_item.is_taxable > 0){
        tax = 0.15;
    }
    let new_data = {
        "id": selected_item.id,
        "item_code": selected_item.item_code,
        "item_category": selected_item.item_category,
        "item_type": selected_item.item_type,
        "item_generic_name": selected_item.item_generic_name,
        "item_brand_name": selected_item.item_brand_name,
        "measurement": selected_item.measurement,
        "tax": tax,
        "unit_price": selected_item.unit_price,
        "quantity": quantity.val(),
        "batch_no": batch_no.val()
    };

    
        sales_detail_data['line_' + did] = new_data;

        console.log(sales_detail_data);

        let new_row_html = '<tr id="sales_detail_line_tr_' + did + '">' +
            '<td><span style="float:right; text-align:right;"><span class="badge" style="margin:2px;">'+selected_item.strength+'</span><br><span class="badge badge-qty" style="margin:2px;">'+selected_item.preparation+'</span></span><b>'+selected_item.item_brand_name+'</b><br>'+selected_item.item_generic_name+'</td>'+
            '<td>' + parseFloat(new_data.quantity).toFixed( 2 ) + '</td>' +
            '<td>' + parseFloat(new_data.unit_price).toFixed( 2 ) + '</td>' +
            '<td>' + parseFloat((new_data.quantity * new_data.unit_price)).toFixed( 2 ) + '</td>' +
            '<td>' + parseFloat((new_data.quantity * new_data.unit_price) * new_data.tax).toFixed( 2 ) + '<input id="did_'+did+'" type="hidden" value="'+ did +'"></td>' +
            '<td><a role="button" class="fa fa-minus-circle text-danger" onclick="remove_sales_line(' + did +
            ')"></a></td>' +
            '</tr>';

        let sales_detail_line = $('#sales_detail_line');
        if(did == $('#did_'+did).val()){
            $('#sales_detail_line_tr_'+did).remove();
        }
        sales_detail_line.append(new_row_html);
        calculate_total();
        clear_sales_line();
    
}

function add_prescription_line_to_sales_line(drug_id) {
    if(!validate_add_prescription_line(drug_id))
            return false; 
    
    number_of_line++;
    //var prescription_detail_quantity = $('#prescription_detail_quantity');
    var prescription_detail_quantity = document.getElementById("prescription_detail_quantity_"+drug_id).value;
    
    //return alert(prescription_detail_quantity);
        
    let selected_item = items["item_id_"+drug_id];
    did = selected_item.id;

    let tax = 0;
    if(selected_item.is_taxable > 0){
        tax = 0.15;
    }
    let new_data = {
        "id": selected_item.id,
        "item_code": selected_item.item_code,
        "item_category": selected_item.item_category,
        "item_generic_name": selected_item.item_generic_name,
        "item_brand_name": selected_item.item_brand_name,
        "measurement": selected_item.measurement,
        "unit_price": selected_item.unit_price,
        "quantity": prescription_detail_quantity,
        "tax": tax,
        "batch_no": batch_no.val()
    };
    //alert(parseFloat('0'+prescription_detail_quantity));
    
    sales_detail_data['line_' + did] = new_data;

    let new_row_html = '<tr id="sales_detail_line_tr_' + did + '">' +
    
        '<td><span style="float:right; text-align:right;"><span class="badge" style="margin:2px;">'+selected_item.strength+'</span><br><span class="badge badge-qty" style="margin:2px;">'+selected_item.preparation+'</span></span><b>'+selected_item.item_brand_name+'</b><br>'+selected_item.item_generic_name+'</td>' +
        '<td>' + parseFloat(new_data.quantity).toFixed( 2 ) + '</td>' +
        '<td>' + parseFloat(new_data.unit_price).toFixed( 2 ) + '</td>' +
        '<td>' + parseFloat((new_data.quantity * new_data.unit_price)).toFixed( 2 )  + 
        '<td>' + parseFloat((new_data.quantity * new_data.unit_price) * new_data.tax).toFixed( 2 ) + '<input id="did_'+did+'" type="hidden" value="'+ did +'"></td>' +
        '<td><a role="button" class="fa fa-minus-circle text-danger" onclick="remove_sales_line(' + did +
        ')"></a></td>' +
        '</tr>';

    let sales_detail_line = $('#sales_detail_line');
        
        if(did != $('#did_'+did).val()){
            sales_detail_line.append(new_row_html);
        }
        else{
            delete sales_detail_data['line_' + did];
            number_of_line--;
        }
        calculate_total();
        clear_prescription_line(drug_id, true);
}

function validate_add_sales_line(){
        let sales_detail_quantity = $('#sales_detail_quantity');
        let sel_item =  $('#sales_detail_item');
        let curr_val =  $('#current_balance');
        //alert(curr_val.val());
        if(sel_item.val() === '')
        {
            sel_item.css("borderColor","red");
            sel_item.focus();
            return false;
        }
        else if((parseFloat('0'+sales_detail_quantity.val()) <= 0 ) || parseFloat('0'+sales_detail_quantity.val()) > parseFloat('0'+curr_val.val()))
        {
            flash_red_inputbox('current_balance',false);
            sales_detail_quantity.css("borderColor","red");
            sales_detail_quantity.focus();
            return false;
        }
        
        return true;
    }

function validate_add_prescription_line(drug_id){

       
        let prescription_current_balance = $('#prescription_current_balance_'+drug_id);
        let prescription_detail_quantity = $('#prescription_detail_quantity_'+drug_id);
        
        if(parseFloat('0'+prescription_detail_quantity.val()) <= 0 || parseFloat('0'+prescription_detail_quantity.val()) > parseFloat('0'+prescription_current_balance.val()))
        //f(parseFloat('0'+prescription_detail_quantity.val())<=0)
        {
            prescription_detail_quantity.css("borderColor","red");
            prescription_detail_quantity.focus();
            return false;
        }
        
        return true;
    }

function validateDrugList(){
    let sel_item =  $('#sales_detail_item');

    if(parseFloat('0' + number_of_line) < 1){
        toastr['error']('Item is not added');

        sel_item.css("borderColor","red");
        sel_item.focus();
        return false;
    }
    
    return true;

}

function remove_sales_line(line_number) {
    $('#sales_detail_line_tr_' + line_number).remove();
    delete sales_detail_data['line_' + line_number];
    number_of_line--;
    calculate_total();
}
                                            
function clear_sales_line() {
    item.val('');
    unit_price.val('');
    current_balance.val('');
    quantity.val('');
    batch_no.val('');
    document.getElementById("sales_detail_quantity").style.removeProperty('border');
    document.getElementById("sales_detail_item").style.removeProperty('border');
    item.focus();
    selected_item_index['sales_selected_item_index'] = -1;
}

function clear_prescription_line(drug_id, do_not_focus = false) {
    item.val('');
    unit_price.val('');
    current_balance.val('');
    document.getElementById("prescription_detail_quantity_"+drug_id).value = "";
    batch_no.val('');
    document.getElementById("prescription_detail_quantity_"+drug_id).style.removeProperty('border');
    document.getElementById("prescription_current_balance_"+drug_id).style.removeProperty('border');
    if(!do_not_focus)
        item.focus();
    selected_item_index['sales_selected_item_index'] = -1;
}

function calculate_total() {
    let sales_detail_no_of_items = $('#sales_detail_no_of_items');
    let sales_detail_total_price = $('#sales_detail_total_price');

    sales_detail_no_of_items.html(Object.keys(sales_detail_data).length);

    let total_price = 0;
    jQuery.each(sales_detail_data, function(i, val) {
        total_price += (val.unit_price * val.quantity * val.tax) + (val.unit_price * val.quantity);
    });
    
    sales_detail_total_price.html(parseFloat(total_price).toFixed( 2 ));
}

var wait_till_i_finish = false;
function prevent_sending_again(send_to_pos){
    wait_till_i_finish = true;
    if(send_to_pos){
        $('#btn_send_to_payment').html('<i class="fa fa-spinner fa-spin"></i> Sending...');
    }
    else{
        $('#btn_hold').html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    }

    $('#btn_send_to_payment').attr('disabled',true);
    $('#btn_hold').attr('disabled',true);
}

function enable_sending_again(){
    wait_till_i_finish = false;
    $('#btn_send_to_payment').removeAttr('disabled');
    $('#btn_hold').removeAttr('disabled');

    $('#btn_send_to_payment').html('Send to payment');
    $('#btn_hold').html('Hold <i class="fa fa-cart-plus"></i>');
}

function save_sales(send_to_pos = 0) {
    if(!validateDrugList()){
        return false;
    }

    if(wait_till_i_finish)
        return false;

    prevent_sending_again(send_to_pos);
    
    let patient_name = ($('#patient_name').val() === '')?"Walk In":$('#patient_name').val();
    let registered_by = $('#registered_by').val();
    let prescription_id = $('#prescription_id').val();
    let prescription_group_id = $('#prescription_group_id').val();
    let store_id = <?= $logged_user_data['extra']['default_store'] ?? -1 ?>;
    let patient_id = $('#patient_id').val();
    let tin_no = $('#tin_no').val();

    $.ajax({
        url: '/inventory/dispensary/salessave',
        data: {
            sales_main: {
                prescription_id: prescription_id,
                prescription_group_id: prescription_group_id,
                store_id: store_id,
                registered_by: registered_by,
                patient_name: patient_name,
                patient_id: patient_id,
                tin_number: tin_no
            },
            sales_detail: sales_detail_data,
            send_to_pos: send_to_pos
        },
        method: 'POST',
        //async: false,
        dataType: 'json',
        success: function(result) {
            if(result.status == "success"){
                location = '/inventory/dispensary/';
            }
            else{
                toastr['error'](result.message);
                enable_sending_again();
            }
        },
        error: function(jqXHR, status, error) {
            alert(error);
            enable_sending_again();
        }
    });
}

function prescriptionhide(prescription_group_id) {
    

    $.ajax({
        url: '/inventory/dispensary/prescriptionhide',
        data: {
            prescription_group_id: prescription_group_id 
        },
        method: 'POST',
        async: false,
        dataType: 'json',
        success: function(result) {
            if(result.status == "success"){
                //location = '/inventory/dispensary/';
                $('tr#'+prescription_group_id).remove();
            }
            else{
                toastr['error'](result.message);
            }
        },
        error: function(jqXHR, status, error) {
            alert(error);
        }
    });
}

function print_prescription(prescription_group_id) {
        location.href = '/inventory/dispensary/view_prescription_printout?prescription_group_id='+prescription_group_id;
    };
</script>

<?= view('Include/_autocomplete_item') ?>

<script>
    var items = <?= \BluelimeSystem::convert_array_to_string($items) ?>;
    var auto_complete_option = {};
    
    auto_complete_option = {
        input: document.getElementById("sales_detail_item"),
        items: items,
        selected_index: 'sales_selected_item_index',
        show_current_balance: false,
        callback_function: get_unit_price_and_balance
    };
    autocomplete_item_by_options(auto_complete_option);
    //autocomplete(document.getElementById("sales_detail_item"), items);
    
    function get_unit_price_and_balance(){
        let item_unit_price = items[selected_item_index['sales_selected_item_index']]['unit_price'];
        let is_vat = items[selected_item_index['sales_selected_item_index']]['is_taxable'];

        if(parseInt(is_vat) > 0){
            item_unit_price = item_unit_price * 1.15;
        }

        unit_price.val(parseFloat(item_unit_price).toFixed(0));
        current_balance.val(items[selected_item_index['sales_selected_item_index']]['current_balance']);
        if(isNaN(quantity.val()) == 0)
            quantity.val('1');
        
        quantity.focus();
        quantity.select();
    }
</script>
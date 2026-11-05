
<link href="/css/autocomplete.css" rel="stylesheet" type="text/css" />

<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Compounding</h1>
            <small>Store Request</small>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        
        <div class="row">
            <div class="col-sm-6 col-md-6">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>SRV Main</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="portlet-body form">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="srv_code" class="col-md-4 control-label"
                                                style="text-align: left;">Code:</label>
                                            <div class="col-md-8">
                                                <input type="text" name="srv_code" id="srv_code" class="form-control"
                                                    required readonly value="SRV-<?=sprintf('%04d',@$srv_main[0]['id']+1)?>" >
                                                <input type="hidden" name="requested_by" id="requested_by" value="<?=$logged_user?>"  >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="request_date" class="col-md-4 control-label"
                                                style="text-align: left;">Date:</label>
                                            <div class="col-md-8">
                                                <input type="text" name="request_date" id="request_date"
                                                    class="form-control" readonly required value="<?= date('Y-m-d') ?>"
                                                    placeholder="">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="requesting_store" class="col-md-4 control-label"
                                                        style="text-align: left;">Requesting:</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="requesting_store" id="requesting_store"
                                                            class="form-control" required value="Despensary Store"
                                                            placeholder="" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="requested_store" class="col-md-4 control-label"
                                                        style="text-align: left;">Requested:</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="requested_store" id="requested_store"
                                                            class="form-control" required value="Main Store"
                                                            placeholder="" readonly>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-bd panel-form">

                            <div class="panel-body">
                                <div class="portlet-body form">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <td width="250">
                                                    <label for="srv_detail_item">Item</label><br>
                                                    <input type="text" class="form-control" id="srv_detail_item">

                                                </td>

                                                <td width="100">
                                                    <label for="current_balance ">Balance</label>
                                                    <input type="text" class="form-control" id="current_balance"
                                                        readonly>
                                                </td>

                                                <td width="100">
                                                    <label for="srv_detail_quantity">Quantity</label><br>
                                                    <input type="text" class="form-control" id="srv_detail_quantity">
                                                </td>

                                                
                                                <td>
                                                    <label
                                                        for="add_button">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</label>
                                                    <button id="add_button" type="button" class="btn btn-primary"
                                                        onclick="add_srv_line()"><b>+</b></button>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody id="srv_detail_line">

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="6">&nbsp;</td>

                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="panel-footer" align="right">
                                <input type="button" class="btn btn-success" value="Save" onclick="save_srv()">
                            </div>
                        </div>
                    </div>

                </div>



            </div>
            <div class="col-sm-6 col-md-6" style="font-size: 13px;">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Store Request List</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="portlet-body form">
                            <div class="row">
                                <div class="col-sm-12">
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="row">&nbsp;</div>
                                        <div class="col-sm-6">
                                            <input id="search_store_request" type="text" placeholder="Search Store Request"
                                                name="search_key" class="form-control" value="<?= @$searchkey ?>">
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-success">Search <i
                                                    class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                                <table class="table table-striped table-condensed table-bordered">
                                    <tr style="font-weight: bold;">
                                        <td>Code</td>
                                        <td>Date</td>
                                        <td>Request By</td>
                                        <td>Requesting Store</td>
                                        <td>Requested Store</td>
                                    </tr>

                                    <?php
                                        $sn = 0;
                                        foreach($srv_main as $srv)
                                        {
                                            $sn++;
                                            echo '
                                                <tr>
                                                <td>'.$srv['srv_code'].'</td><td>'.date('Y-m-d', strtotime($srv['request_date'])).'</td><td>'.$srv['requested_by__name'].'</td><td>'.$srv['requesting_store'].'</td><td>'.$srv['requested_store'].'</td><td> <a href="/inventory/compounding/srv_print?srv_id='.$srv['id'].'" class="btn btn-warning btn-xs"> <i class="fa fa-eye"></i></a> </td>
                                                </tr>
                                            ';
                                        }
                                    ?>

                                </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
var did = 0;
var item = $('#srv_detail_item');
var quantity = $('#srv_detail_quantity');
var current_balance  = $('#current_balance');

var srv_detail_data = {};
var number_of_line = 0;

function clear_srv_line() {
    item.val('');
    quantity.val('');
    current_balance.val('');
    document.getElementById("srv_detail_quantity").style.removeProperty('border');
    document.getElementById("srv_detail_item").style.removeProperty('border');
    item.focus();
}

function add_srv_line()
    {
        if(!validate_add_srv_line())
            return false; 
        number_of_line++;

        let selected_item = items[selected_item_index['srv_selected_item_index']];
        did = selected_item.id;

        let new_data = {"id":selected_item.id, "item_code":selected_item.item_code, "item_category":selected_item.item_category, "item_generic_name":selected_item.item_generic_name, "item_brand_name":selected_item.item_brand_name, "measurement":selected_item.measurement, "quantity":quantity.val()};
        
        srv_detail_data['line_'+number_of_line] = new_data;

        let new_row_html = '<tr id="srv_detail_line_tr_'+number_of_line+'">'+
                                '<td><span class="badge" style="float:right">' + selected_item.item_code + '</span><b>'+ selected_item.item_generic_name +'</b><br>' +  selected_item.item_brand_name + '</td>'+
                                '<td>'+parseFloat(selected_item.current_balance).toFixed( 2 )+'</td>'+
                                '<td>'+parseFloat(new_data.quantity).toFixed( 2 )+'<input id="did_'+did+'" type="hidden" value="'+ did +'"></td>'+
                                '<td><a role="button" class="fa fa-minus-circle text-danger" onclick="remove_srv_line('+number_of_line+')"></a></td>'+
                            '</tr>';
        
        let srv_detail_line = $('#srv_detail_line');
        if(did != $('#did_'+did).val()){
            srv_detail_line.append(new_row_html);
        }
        else{
            number_of_line--;
        }
        calculate_total();
        clear_srv_line();
    }
    
function validate_add_srv_line(){
    let srv_detail_quantity = $('#srv_detail_quantity');
    let sel_item =  $('#srv_detail_item');
    
    if(sel_item.val() === '')
    {
        document.getElementById("srv_detail_item").style.borderColor = 'red';
        srv_detail_item.focus();
        return false;
    }
    else if(parseFloat('0'+srv_detail_quantity.val()) <= 0)
    {
        document.getElementById("srv_detail_quantity").style.borderColor = 'red';
        srv_detail_quantity.focus();
        return false;
    }
    
    return true;
}

function validateDrugList(){
//alert(number_of_line);

    if(parseFloat('0' + number_of_line) < 1){
        dental_show_alert("Drug List is Empty!");
        return false;
    }
    
    return true;

}

function remove_srv_line(line_number) {
    $('#srv_detail_line_tr_' + line_number).remove();
    delete srv_detail_data['line_' + line_number];
    calculate_total();
}

function calculate_total() {
    let srv_detail_no_of_items = $('#srv_detail_no_of_items');
    let srv_detail_total_price = $('#srv_detail_total_price');

    srv_detail_no_of_items.html(Object.keys(srv_detail_data).length);

    let total_price = 0;
    jQuery.each(srv_detail_data, function(i, val) {
        total_price += val.unit_price * val.quantity;
    });

    srv_detail_total_price.html(parseFloat(total_price).toFixed( 2 ));
}

function save_srv() {
    if(!validateDrugList()){
            return false; 
    }
    let srv_code = $('#srv_code').val();
    let request_date = $('#request_date').val();
    let requested_by = $('#requested_by').val();
    let requesting_store = 3;
    let requested_store = 1;


    $.ajax({
        url: '/inventory/compounding/srvsave',
        data: {
            srv_main: {
                srv_code: srv_code,
                requested_by: requested_by,
                request_date: request_date,
                requesting_store: requesting_store,
                requested_store: requested_store,
            },
            srv_detail: srv_detail_data
        },
        method: 'POST',
        async: false,
        dataType: 'json',
        success: function(result) {
            location = '/inventory/compounding/srv_print?srv_id='+result.value;
        },
        error: function(jqXHR, status, error) {
            alert(error);
        }
    });
}


</script>


<?= view('Include/_autocomplete_item') ?>


<script>
    //var selected_item_index[''] = -1;

    var items = <?= \BluelimeSystem::convert_array_to_string($items) ?>;
    var auto_complete_option = {};
    
    auto_complete_option = {
        input: document.getElementById("srv_detail_item"),
        items: items,
        selected_index: 'srv_selected_item_index',
        show_current_balance: false,
        callback_function: get_unit_price_and_balance
    };
    autocomplete_item_by_options(auto_complete_option);
    
    function get_unit_price_and_balance(){
        //document.getElementById("current_balance").value = selected_item.current_balance;
        document.getElementById("current_balance").value = items[selected_item_index['srv_selected_item_index']]['current_balance'];
    }
    
    


    </script>
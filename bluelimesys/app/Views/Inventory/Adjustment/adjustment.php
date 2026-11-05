<?php
    echo view('Include/_header')
        .view('Include/_left_sidebar_top')
        .view('Inventory/Adjustment/_left_sidebar')
        .view('Include/_left_sidebar_bottom');
?>
<style>
    .blm-badge{
        font-size: large;
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Inventory</h1>
            <small>Adjustment</small>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd panel-form">
                    
                    <div class="panel-body">
                        
                        <table class="table table-borderless">
                            <tr>
                                <td width="100">Item</td>
                                <td>
                                    <input type="text" class="form-control" id="item">
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <div id="selected_item_display" style="display: inline-block;"></div><br><span class="blm-badge badge badge-qty-red" id="qty_badge"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>Quantity</td>
                                <td>
                                    <input type="text" class="form-control" id="quantity">
                                </td>
                            </tr>
                            <tr>
                                <td>Expiry Date</td>
                                <td>
                                    <input type="text" class="form-control" id="expiry_date">
                                </td>
                            </tr>
                        </table>
                        <button class="btn btn-success" onclick="update_quantity()">Update</button>
                        
                    </div>
                </div>
            </div>
        </div>   
    </section>
</div>
<?= view('Include/_autocomplete_item') ?>
<script>
    var items = <?= \BluelimeSystem::convert_array_to_string($items) ?>;
    //selected_item['comp_selected_item_index'] = -1;
    //autocomplete_item(document.getElementById("compounding_item"), items, 'comp_selected_item_index', show_selected_item_detail);

    //selected_item_index['comp_selected_item_index'] = -1;
    var autocomplete_item_option = {
        input:document.getElementById("item"),
        items:items,
        selected_index:'item',
        callback_function: show_selected_item_detail,
    };
    autocomplete_item_by_options(autocomplete_item_option);

    function show_selected_item_detail(){
        let item_index = selected_item_index['item'];
        let selected_item = items[item_index];
        
        let html = '<b>'+selected_item['item_brand_name']+'</b><br>'+selected_item['item_generic_name']+'<br><span class="badge">'+selected_item['item_code']+'</span>';

        $('#selected_item_display').html(html);
        $('#qty_badge').html(selected_item['current_balance']);
        $('#qty_badge').removeClass("badge-qty-warning");
        $('#qty_badge').addClass("badge-qty-red");
        $('#item').val('');
        $('#quantity').focus();
    }

    function update_quantity(){
        let item_index = selected_item_index['item'];
        let selected_item = items[item_index];
        let item_id = selected_item['id'];
        let quantity = $('#quantity').val();
        let expiry_date = $('#expiry_date').val();



        $.ajax({
            url: "/adjustment/adjust_item_quantity_for_main_store?signature=<?= $_GET['signature']??'0' ?>",
            
            method: 'POST',
            data: {item_id:item_id, quantity:quantity,expiry_date:expiry_date},
            dataType: 'JSON',
            success:function(result){
                if(result.status = 'success'){
                    if(result.value == quantity){
                        toastr['success']('Quantity updated.');
                        $('#qty_badge').html(result.value);
                        $('#qty_badge').removeClass("badge-qty-red");
                        $('#qty_badge').addClass("badge-qty-warning");
                        $('#quantity').val('');
                        $('#expiry_date').val('');
                    }
                    else
                        toastr['warning']('There might be an error. Please check the quantity.');
                }
                else
                    toastr['error']('Error. Not updated.');
            }
        });
    }
</script>
<?php
    echo view('Include/_footer');
?>
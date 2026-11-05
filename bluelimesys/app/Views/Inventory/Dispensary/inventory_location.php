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
                <div class="panel" id="printable">
                    <div class="panel-body" style="min-height: 700px;">
                        <table class="table table-borderless">
                            <tr>
                                <td>Store</td>
                                <td>
                                    <select class="form-control" id="store">
                                        <option value="">Select Store</option>
                                        <option value="2">Dispensary</option>
                                        <option value="3">Compounding</option>
                                    </select>
                                </td>
                            </tr>
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
                                <td>Location</td>
                                <td>
                                    <input type="text" class="form-control" id="inventory_location">
                                    <i class="fa fa-check-circle hide" style="color: green; font-size: 34px;" id="success_flag"></i>
                                </td>
                            </tr>
                        </table>
                        <button class="btn btn-success" onclick="save_location()">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= view('Include/_autocomplete_item') ?>
<script>
    var items = <?= \BluelimeSystem::convert_array_to_string($items) ?>;
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
        
        let html = '<b>'+selected_item['item_brand_name']+'</b> '+selected_item['item_generic_name']+'<br><span class="badge">'+selected_item['strength']+'</span> <span class="badge badge-qty">'+selected_item['preparation']+'</span>';

        $('#selected_item_display').html(html);
        $('#item').val('');
        $('#inventory_location').val('');
        $('#success_flag').addClass('hide');
        $('#inventory_location').focus();
    }

    function save_location(){
        let item_index = selected_item_index['item'];
        let selected_item = items[item_index];
        let item_id = selected_item['id'];
        let inventory_location = $('#inventory_location').val();
        let store_id = $('#store').val();
        if(store_id == ''){
            toastr['error']('Please select a store.');
            $('#store').focus();
            return false;
        }
        
        if(inventory_location.trim() == ''){
            toastr['error']('Please enter a location.');
            $('#inventory_location').focus();
            return false;
        }

        $.ajax({
            url: "/inventory/dispensary/save_inventory_location",
            method: 'POST',
            data: {item_id:item_id, inventory_location:inventory_location.trim(), store_id:store_id},
            dataType: 'JSON',
            async: false,
            success:function(result){
                if(result.status = 'success'){
                    toastr['success']('Location saved.');
                    $('#success_flag').removeClass('hide');
                    $('#item').focus();
                }
                else
                    toastr['error']('There might be an error. Contact system administrator.');
            }
        });
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

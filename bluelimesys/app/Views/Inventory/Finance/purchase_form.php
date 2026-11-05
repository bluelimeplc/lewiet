<?php
    $items = $this->data['items'];
    $suppliers = $this->data['suppliers'];
    $default_store = $this->data['default_store'];
?>

<link href="/css/autocomplete.css" rel="stylesheet" type="text/css"/>

<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small>Purchase Order</small></u>
            <ol class="breadcrumb">
                <li class="active" role="button"><a role="button" href="/inventory/finance/purchase_order_list">Purchase List</a></li>
            </ol>
        </div>
    </section>

<!-- Main content -->
    <section class="content">
        <div class="form-horizontal">
            <div class="row">
                <div class="col-sm-5 col-md-5">
                    <div  class="panel panel-bd panel-form">
                        <div class="panel-heading">
                            
                        </div>
                        <div class="panel-body">
                            <div class="portlet-body form">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <div class="form-group">
                                                <label for="purchase_code" class="col-md-6 control-label" style="text-align: left;">Purchase Code:</label>
                                                <div class="col-md-12">
                                                    <input type="text" name="purchasecode" readonly id="purchasecode" class="form-control" required value="<?='PR'.sprintf('%04d', $purchase_main['id'])?>" placeholder=""> 
                                                   <input type="hidden"  name="purchase_id"  id="purchase_id" value="<?=$purchase_main['id']?>" >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="purchase_supplier" class="col-md-4 control-label" style="text-align: left;">Supplier:</label>
                                                <div class="col-md-12">
                                                    
                                                    <input type="text" name="supplier_id" id="purchase_supplier" readonly class="form-control" required value="<?= $supplier?>" placeholder="">
                                                </div>
                                            </div> 
                                            <div class="form-group">
                                                <label for="description" class="col-md-4 control-label" style="text-align: left;">Description:</label>
                                                <div class="col-md-12">
                                                    <textarea class="form-control" name="description" readonly id="description"><?=$purchase_main['description']?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                    <label for="is_urgent" class="col-md-4 control-label" style="text-align: left;">
                                                        <?php 
                                                            if($purchase_main['is_urgent']==1){
                                                        ?>
                                                            <span class="fa jump_dental" style="color: white;background-color: red;"> <h1>URGENT</h1></span>
                                                             
                                                        <?php
                                                            }
                                                        ?>
                                                                                                        
                                                
                                            </div>
                                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-7 col-md-7">
                    <div  class="panel panel-bd panel-form">
                        <div class="panel-heading">
                            
                        </div>
                        <div class="panel-body">
                            <div class="portlet-body form">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <td width="250">
                                                    <label for="purchase_detail_item">Item</label><br>
                                                    <input type="text" class="form-control" id="purchase_detail_item">
                                                
                                            </td>
                                            <td width="100">
                                                <label for="purchase_detail_quantity">Quantity</label><br>
                                                <input type="text" class="form-control" id="purchase_detail_quantity">
                                            </td>
                                            
                                            <td>
                                                <br>
                                                <button type="button" class="btn btn-primary" onclick="add_purchase_line()">Add</button>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody id="purchase_detail_line">
                                        <?php
                                            foreach($purchase_detail as $detail)
                                            {
                                        ?>
                                        <tr>
                                            <td><span class="badge" style="float:right">
                                                    <?=$detail['item_detail']['item_code']?>
                                                </span><b>
                                                    <?=$detail['item_detail']['item_generic_name']?></b><br>
                                                    <?=$detail['item_detail']['item_brand_name']?>
                                            </td>
                                            <td>
                                                <?=$detail['quantity']?>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <?php
                                            }
                                        ?>
                                        
                                    </tbody>
                                    <tfoot>
                                        
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="panel-footer" align="right">
                            <button class="btn btn-success" onclick="save_purchase()">Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>

    var item = $('#purchase_detail_item');
    var quantity = $('#purchase_detail_quantity');
    var did = 0;

    var purchase_detail_data = {};
    var number_of_line = 0;

    function clear_purchase_line()
    {
        item.val('');
        quantity.val('');
        item.focus();
        selected_item_array_id = -1;
        document.getElementById("purchase_detail_item").style.removeProperty('border');
        document.getElementById("purchase_detail_quantity").style.removeProperty('border');
    }

    function add_purchase_line()
    {
        if(!validate_add_purchase_line())
            return false; 

        number_of_line++;

        let selected_item = items[selected_item_array_id];
        did = selected_item.id;

        let new_data = {"id":selected_item.id,
                        "item_code":selected_item.item_code, 
                        "item_category":selected_item.item_category, 
                        "item_generic_name":selected_item.item_generic_name, 
                        "item_brand_name":selected_item.item_brand_name, 
                        "measurement":selected_item.measurement,
                        "quantity":quantity.val()
                    };
        
        purchase_detail_data['line_'+number_of_line] = new_data;

        let new_row_html = '<tr id="purchase_detail_line_tr_'+number_of_line+'">'+
                                '<td><span class="badge" style="float:right">' + selected_item.item_code + '</span><b>'+ selected_item.item_generic_name +'</b><br>' +  selected_item.item_brand_name + '</td>'+
                                '<td>'+new_data.quantity+'</td>'+
                                '<input id="did_'+did+'" type="hidden" value="'+ did +'"><td><a role="button" class="fa fa-minus-circle text-danger" onclick="remove_purchase_line('+number_of_line+')"></a></td>'+
                            '</tr>';
        
        let purchase_detail_line = $('#purchase_detail_line');

        if(did != $('#did_'+did).val()){
            purchase_detail_line.append(new_row_html);
        }
        else{
            number_of_line--;
        }
        clear_purchase_line();
    }

    function validate_add_purchase_line(){
        let purchase_detail_quantity = $('#purchase_detail_quantity');

        
        if(item.val() === '')
        {
            item.css("borderColor","red");
            item.focus();
            return false;
        }
        else if(parseFloat('0'+purchase_detail_quantity.val()) <= 0)
        {
            document.getElementById("purchase_detail_quantity").style.borderColor = 'red';
            purchase_detail_quantity.focus();
            return false;
        }
        
        return true;
    }

    function remove_purchase_line(line_number)
    {
        $('#purchase_detail_line_tr_'+line_number).remove();
        delete purchase_detail_data['line_'+line_number];
       
    }

  
    function save_purchase()
    {
        
        let purchase_id = $('#purchase_id').val();

        $.ajax({
            'url': "/inventory/finance/purchaseupdate",
            'type': 'POST',
            'dataType': 'json',
            'data': {
                'purchase_id': purchase_id
            },
            'success':function(result){
                location = '/inventory/finance/purchase_order_print?purchase_id='+result.value;
            },
            error:function(jqXHR,status,error){
                alert(error);
            }
        });
    }
    
    function validate_save_purchase(){
        
        let purchase_code = $('#purchasecode');
        let purchase_supplier = $('#purchase_supplier');
        let description = $('#description');


        if(Object.keys(purchase_detail_data).length == 0)
        {
            dental_show_alert('Purchase List Empty');
            item.focus();
            return false;
        }
        return true;
    }
</script>
<?= view('Include/_autocomplete_item') ?>
<?= view('Include/_autocomplete_supplier') ?>
<script>
    var items = <?= \BluelimeSystem::convert_array_to_string($items) ?>;
    autocomplete_item(document.getElementById("purchase_detail_item"), items);

    var suppliers = <?= \BluelimeSystem::convert_array_to_string($suppliers) ?>;
    selected_supplier['purchase_selected_supplier_index'] = -1;
    autocomplete_supplier(document.getElementById("purchase_supplier"), suppliers, "purchase_selected_supplier_index");
</script>
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo "Store Dashboard";?></h1>
            <small><?php echo "Store Dashboard";?></small>
            <ol class="breadcrumb">

                <li class="active"><a
                        href="#"><?php echo "Dashboard";?></a></li>
            </ol>
        </div>
    </section>


    <!-- Main content -->
    
    <section class="content">
    <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><a class="" data-toggle="collapse" href="#collapseBalance" role="button" aria-expanded="false" aria-controls="collapseBalance">
                            Low Store Balance <i class="fa fa-angle-double-down"></i></a></h4>
                        </div>
                    </div>
                    <div class="panel-body collapse" id="collapseBalance">
                        <div id="low_balance"  class="col-sm-12">
                            
                                <table class="table table-striped table-hover table-responsive table-bordered">
                                    
                                <tr style="font-weight: bold;">
                                    <td>Item Code</td><td>Item Name</td><td>Item Brand</td><td>Min Balance</td><td>Balance</td><td>Purchase Request</td>
                                </tr> 
                            <?php
                            //\BluelimeSystem::show_and_die($this->data['doctors']);
                                foreach($grv_detail as $detail):
                                    if($detail['min_balance'] >= $detail['current_balance']){
                            ?>
                                <tr>
                                    <td><?=$detail['item_detail']['item_code']?></td>
                                    <td><?=$detail['item_detail']['item_generic_name']?></td>
                                    <td><?=$detail['item_detail']['item_brand_name']?></td>
                                    <td><?=$detail['min_balance']?></td>
                                    <td><?=$detail['current_balance']?></td>
                                    <td style="width:140px">
                                        <div class="col-md-6"><input placeholder="Qty" class="form-control" style="width:70px" type="text" id="purchase_quantity_<?=$detail['item_id']?>" Min="1"></div>
                                        <div class="col-md-6"><button id="add_button" type="button" class="btn btn-primary"
                                            onclick="add_to_purchase_request_list(<?=$detail['item_id']?>)"><b>+</b></button></div>
                                    </td>
                                </tr>
                            <?php
                                    }
                                endforeach;
                            ?>
                             </table> 
                                
                                </div>
                            <div style="float:right;" class="col-sm-5">
                            
                                <table class="table table-bordered">
                                    <tbody id="purchase_detail_line">

                                    </tbody>
                                    <tfoot id="purchase_footer">
                                        <td colspan="2">
                                        <div class="col-md-6">
                                            <input type="text" name="supplier_id" id="purchase_supplier" class="form-control" placeholder="Supplier">
                                            <input type="hidden"  name="purchasecode" id="purchasecode" value="<?='PR'.sprintf('%04d', $last_purchase_id+1)?>" > 
                                            <input type="hidden"  name="registered_by"  id="registered_by" value="<?=$loggeduserdata['id']?>" > 
                                            </div>
                                            <div align="right" class="col-md-3">
                                                <label>Urgent</label>
                                                <label class="switch">
                                                    <input name="is_urgent" id="is_urgent" type="checkbox" >
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                            <div align="right" class="col-md-3">
                                               <button id="add_button" type="button" class="btn btn-success"  onclick="save_purchase()"><b>Request</b></button>
                                            </div>
                                            
                                        </td>
                                    </tfoot>
                                </table>
                            </div>
                            
                          
                    
                        </div>
                </div>
            </div>

        </div>
        <div class="row">
            
            <div class="col-sm-12">
            <div class="panel panel-bd">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4>Near Expiry Items</h4>
                    </div>
                </div>
                <div class="panel panel-bd">
                    
                        <div class="panel-body">
                        <div  class="col-sm-12">
                                <table class="table table-striped table-hover table-responsive table-bordered">
                                <tr style="font-weight: bold;">
                                    <td>Item Code</td><td>Item Name</td><td>Item Brand</td><td>Expiry Date</td><td>Days Left</td>
                                </tr> 
                            <?php
                            //\BluelimeSystem::show_and_die($this->data['doctors']);
                                foreach($grv_detail as $detail):
                                    $today = new DateTime();
                                    $expiry = new DateTime($detail["expiry_date"]);
                                    if(BluelimeSystem::date_difference(date_format($today,"Y-m-d"),date_format($expiry,"Y-m-d")) <= $detail["notify_expiry"] or $today>$expiry){
                                        
                            ?>
                                <tr>
                                    <td><?=$detail['item_detail']['item_code']?></td>
                                    <td><?=$detail['item_detail']['item_generic_name']?></td>
                                    <td><?=$detail['item_detail']['item_brand_name']?></td>
                                    <td><?=$detail['expiry_date']?></td><td><?=$today>$expiry?-(BluelimeSystem::date_difference(date_format($today,"Y-m-d"),date_format($expiry,format: "Y-m-d"))):BluelimeSystem::date_difference(date_format($today,"Y-m-d"),date_format($expiry,"Y-m-d"))?></td>
                                </tr>
                            <?php
                                    }
                                endforeach;
                            ?>
                             </table> 
                                
                                </div>
                          
                    
                        </div>
                        
                    
                </div>
                            </div>
            </div>
            
        </div>



        

    </section> <!-- /.content -->
</div> <!-- /.content-wrapper -->

<script>
    $('#purchase_footer').hide();
    var number_of_line = 0;
    var purchase_detail_data = {};
    var did = 0;
    
    function add_to_purchase_request_list(drug_id) {
        //dental_show_alert(drug_id);
        if(!validate_add_purchase_line(drug_id))
            return false; 
        $("#low_balance").attr("class", "col-sm-7");
        $('#purchase_footer').show();
        let purchase_quantity = $('#purchase_quantity_'+drug_id).val();
        number_of_line++;
        
        
            
        let selected_item = items["item_id_"+drug_id];
        //dental_show_alert(selected_item.id);
        did = selected_item.id;
        let new_data = {"id":selected_item.id,
                        "item_code":selected_item.item_code, 
                        "item_category":selected_item.item_category, 
                        "item_generic_name":selected_item.item_generic_name, 
                        "item_brand_name":selected_item.item_brand_name, 
                        "measurement":selected_item.measurement,
                        "quantity":purchase_quantity,
                    };
        //alert(parseFloat('0'+prescription_detail_quantity));
        
        purchase_detail_data['line_' + number_of_line] = new_data;

        let new_row_html = '<tr id="purchase_detail_line_tr_' + number_of_line + '">' +
            '<td><span class="badge" style="float:right">' + selected_item.item_code + '</span><b>' + selected_item
            .item_generic_name + '</b><br>' + selected_item.item_brand_name + '</td>'+
            '<td>' + purchase_quantity + '<input id="did_'+did+'" type="hidden" value="'+ did +'"></td>'+
            '<td><a role="button" class="fa fa-minus-circle text-danger" onclick="remove_purchase_line(' + number_of_line +
            ')"></a></td>' +
            '</tr>';

        let purchase_detail_line = $('#purchase_detail_line');
        if(did != $('#did_'+did).val()){
            purchase_detail_line.append(new_row_html);
        }
        else{
            number_of_line--;
        }
        clear_purchase_line(drug_id);
    
    }
function remove_purchase_line(line_number) {
    $('#purchase_detail_line_tr_' + line_number).remove();
    delete purchase_detail_data['line_' + line_number];
   number_of_line--; 

    if(number_of_line == 0){
        $('#purchase_footer').hide();
        $("#low_balance").attr("class", "col-sm-12");
    }
}
function clear_purchase_line(drug_id)
    {
        let purchase_quantity = $('#purchase_quantity_'+drug_id);
        purchase_quantity.val('');
        document.getElementById("purchase_quantity_"+drug_id).style.removeProperty('border');
    }
function validate_add_purchase_line(drug_id){
        let purchase_quantity = $('#purchase_quantity_'+drug_id);
        
        if(parseFloat('0'+purchase_quantity.val()) <= 0)
        {
            document.getElementById("purchase_quantity_"+drug_id).style.borderColor = 'red';
            purchase_quantity.focus();
            return false;
        }
        
        return true;
    }
function save_purchase()
    {
        
        let purchase_code = $("#purchasecode").val();
        let registered_by = $("#registered_by").val();
        let purchase_supplier = $('#purchase_supplier').val();
        let default_store = 1;
        let supplier_id = -1;
        if(purchase_supplier != ''){
            suppliers[selected_supplier['purchase_selected_supplier_index']]['id'];
        }
        let is_urgent = 0;
        if($("#is_urgent").is(':checked')){
            is_urgent =1;
        }
        let description = 'Low Balance';
        //alert(purchase_detail_data);

        $.ajax({
            url: '/inventory/store/purchasesave',
            data: {
                    purchase_main:
                    {
                        purchase_code:purchase_code,
                        store_id:default_store,  
                        supplier_id: supplier_id, 
                        is_urgent: is_urgent, 
                        registered_by: registered_by, 
                        description: description
                    }, 
                    purchase_detail: purchase_detail_data
                },
            method:'POST',
            async: false,
            dataType: 'json',
            success:function(result){
                location = '/inventory/store/purchase_print?purchase_id='+result.value;
            },
            error:function(jqXHR,status,error){
                alert(error);
            }
        });
    }
   
    
    
</script>
<?= view('Include/_autocomplete_supplier') ?>
<script>
    
    var items = <?= \BluelimeSystem::convert_array_to_string($items) ?>;
    var suppliers = <?= \BluelimeSystem::convert_array_to_string($suppliers) ?>;
    selected_supplier['purchase_selected_supplier_index'] = -1;
    autocomplete_supplier(document.getElementById("purchase_supplier"), suppliers, "purchase_selected_supplier_index");
</script>


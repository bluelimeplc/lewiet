<?php
    if($search_key == '')
        $search_key = $_GET['search_key'] ?? '';
?>
<link href="/css/autocomplete.css" rel="stylesheet" type="text/css" />
<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Store</h1>
                <small>Price Setup</small>
            
        </div>
    </section>
    

<!-- Main content -->
    <section class="content">
        <div class="row">
                <div class="col-sm-12 col-md-12">
                    <div  class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title" >
                            <?php 
                                helper('form');
                                $attributes = array('class' => 'form-horizontal','method'=>'post',  'enctype' => 'multipart/form-data');
                                echo form_open_multipart('inventory/store/pricesetup_form', $attributes);                
                            ?>
                                <div class="form-group">
                                    <div class="row">&nbsp;</div>
                                    <div class="col-sm-3">
                                        <input id="search_item" type="text" placeholder="Search Item"
                                            name="search_key" class="form-control" value="<?= $search_key ?? '' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success">Search <i
                                                class="fa fa-search"></i></button>
                                    </div>
                                    
                                </div>
                            <?php echo form_close();?>
                        </div>
                    </div>
                    <div class="panel-body table-responsive">
                        <div class="portlet-body form">
                            <div class="row">    
                                <div class="col-sm-12">
                                    <div class="col-sm-3">
                                        <input style="transform: scale(1.9);" type="checkbox" id="based_on_current_price" />
                                            <label class="form-check-label" for="based_on_current_price">&nbsp; Based on current price</label>
                                        
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="col-sm-3">
                                            <input id="margin" type="number" placeholder="%"
                                                name="margin" class="form-control" min="1">
                                        </div>
                                        <div class="col-sm-2" hidden>
                                            <button type="button" class="btn btn-success" onclick="calculate_new_price()">Calculate</button>
                                        </div>
                                    </div>
                                    <div class="form-body">
                                    <div class="col-sm-offset-3 col-sm-9" style="text-align: right;">
                                        <div>
                                            <button id="save_price"  style="float: right; " type="button" class="btn btn-danger" onclick="save_price_list()">Update Price</button>
                                            <input  style="float: right; width:150px" class="form-control" type="date" id="effective_date" min="<?=date('Y-m-d')?>" value="<?=date('Y-m-d')?>">
                                            <button style="float: right; " type="button" class="btn btn-light">Effective Date</button>
                                        </div>
                                    </div>
                                        
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                            <tr style="font-weight: bold;">
                                                <th>ID</th>
                                                <th>Item Code</th>
                                                <th>Generic Name</th>
                                                <th>Brand Name</th>
                                                <th>Measurment</th>
                                                <th>Last Unit Cost</th>
                                                <th>Current Price</th>
                                                <th>New Price</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php 
                                                foreach($items as $item):
                                            ?>
                                            <tr id="item_list_<?=$item['id']?>">
                                                <td><?=$item['id']?></td>
                                                <td><?=$item['item_code']?></td>
                                                <td><?=$item['item_generic_name']?><br><span class="badge bg-success"><?=$item['preparation']?></span></td>
                                                <td><?=$item['item_brand_name']?><br><span class="badge"><?=$item['strength']?></span></td>
                                                <td><?=$item['measurement']?></td>
                                                
                                                <td><input id="last_unit_cost_<?=$item['id']?>" readonly type="number" class="form-control" value="<?=$item['last_unit_cost']?>"></td>
                                                <td><input id="unit_price_<?=$item['id']?>" readonly type="number" class="form-control" value="<?=$item['unit_price']?>"></td>
                                                <td><input id="new_price_<?=$item['id']?>" name="new_price_<?=$item['id']?>" type="number" class="form-control" min="0" value="" onkeyup="calculate_before_tax(this.value)"></td>
                                                <td><input id="before_vat<?=$item['id']?>" name="before_vat<?=$item['id']?>" type="number" class="form-control" min="0" value="" readonly></td>
                                            </tr>
                                                <?php 
                                                endforeach;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
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
    
    var items = <?= \BluelimeSystem::convert_array_to_string($items) ?>; 
    var item_price_list = {};

    function calculate_new_price() {
        if(!validate_margin())
            return false;
        jQuery.each(items, function(i, val) {
                
            let initial_amount;
            let new_price;
            
            let margin = $('#margin').val();
            let item_id = val['id'];
            
            if($("#based_on_current_price").is(':checked')){
                initial_amount= Number($('#unit_price_'+item_id).val());
            }
            else{
                initial_amount= Number($('#last_unit_cost_'+item_id).val());
            }
            
            new_price = Math.ceil((initial_amount + (initial_amount * margin/100))/5)*5;
            //dental_show_alert(new_price);
            
            $('#new_price_'+item_id).val(new_price);
            if(new_price <= 0)
                return 0;
            items[i]['new_price'] = new_price;
            //item_price_list['item_id_'+item_id] = {'item_id':item_id,'unit_price':new_price};
        });
        $("#margin").removeAttr("style");
    }
    function calculate_before_tax() {
        
        jQuery.each(items, function(i, val) {
               
            new_price = $('#new_price_'+val['id']).val();
            if(val['is_taxable']==1){
                $('#before_vat'+val['id']).val(parseFloat(new_price/1.15).toFixed( 2 ));
            }
            else
            $('#before_vat'+val['id']).val(parseFloat(0).toFixed( 2 ));
        });
    }

    function collect_new_price(){
        
        jQuery.each(items, function(i, val) {
            let item_id = val['id'];
            let new_price = $('#new_price_'+item_id).val();
            if(new_price <= 0)
                return 0;
            dental_show_alert(new_price);
            item_price_list['item_id_'+item_id] = {'item_id':item_id,'unit_price':new_price};
        });
    }
    
    function save_price_list(){
        collect_new_price();
        dental_show_alert(item_price_list);
        let effective_date = $('#effective_date').val();
        $.ajax({
            url:'/inventory/store/pricelistsave',
            data:{'item_price_list':JSON.stringify(item_price_list),'effective_date':effective_date},
            method: 'POST',
            async: false,
            dataType: 'json',
            success:function(result){
                if(result != null){
                    dental_show_alert(result.value);
                    location = '/inventory/store/pricesetup_form?search_key=<?= $search_key ?>';
                }
            }
        });
    }

    function validate_margin(){
        let margin = $('#margin');
        
        if(margin.val() <= 0)
        {
            margin.css("borderColor","red");
            margin.focus();
            return false;
        }
        
        return true;
    }
    
</script>
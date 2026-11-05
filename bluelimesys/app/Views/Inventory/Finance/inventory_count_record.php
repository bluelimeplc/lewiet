<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Finance/_left_sidebar_finance') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-credit"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small>Inventory / Inventory Count Record</small>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <h4></h4>
                    </div>
                    <div class="panel-body">
                        <form method="get">
                            <div class="col-lg-6">
                                <table class="table">
                                    <tr>
                                        <th>Title</th><th>Sotre/Department</th><th></th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select name="inventory_count_setup_id" class="form-control" required>
                                                <option value=""></option>
                                                <?php
                                                    foreach($inventory_count_setups as $inventory_count_setup)
                                                        echo '<option value="'.$inventory_count_setup['id'].'" '.($inventory_count_setup_id==$inventory_count_setup['id']?' selected ':'').'>'.$inventory_count_setup['title'].'</option>';
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="store_id" class="form-control" required>
                                                <option value=""></option>
                                                <option value="1" <?=$store_id==1?' selected ':''?>>Main Store</option>
                                                <option value="2" <?=$store_id==2?' selected ':''?>>Dispensary</option>
                                                <option value="3" <?=$store_id==3?' selected ':''?>>Compounding</option>
                                            </select>
                                        </td>
                                        <td><button class="btn btn-success">List</button></td>
                                    </tr>
                                </table>
                            </div>
                        </form>
                    </div>
                </div> 
            </div>
            <div class="col-lg-12" >
                <div class="panel"  style="min-height: 550px;">
                    <div class="panel-body">
                        
                        <?= $store_id > 0 ? '<a style="float: right;" role="button" target="_blank" href="/inventory/finance/export_inventory_count_record?store_id='.$store_id.'&inventory_count_setup_id='.$inventory_count_setup_id.'&type='.$type.'" class="btn btn-danger">Export In CSV</a>' : '' ?>
                        <?= $store_id > 0 ? '<a style="float: right; margin-right: 10px;" role="button" href="/inventory/finance/import_inventory_count_record?store_id='.$store_id.'&inventory_count_setup_id='.$inventory_count_setup_id.'" class="btn btn-info" >Import From CSV</a>' : '' ?>
                        <!--a role="button" class="btn btn-success" href="?inventory_count_setup_id=<?= $inventory_count_setup_id?>&store_id=<?=$store_id?>&type=drug">Drug</a>
                        <a role="button" class="btn btn-success" href="?inventory_count_setup_id=<?= $inventory_count_setup_id?>&store_id=<?=$store_id?>&type=cosmetics">Cosmetics</a>
                        <a role="button" class="btn btn-success" href="?inventory_count_setup_id=<?= $inventory_count_setup_id?>&store_id=<?=$store_id?>">Both</a -->
                    </div>
                    <div class="panel-body">
                        <div class="col-lg-12">
                            <table class="table table-bordered table-striped" id="item_count_record_list">
                                <thead>
                                    <tr>
                                        <th></th><th>Item Code</th><th></th><th>Item Detail</th><th width="">Quantity</th><th>Remark</th><th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $inactive = !($inventory_count_setups['id_'.$inventory_count_setup_id]['active']??true);
                                        $sn = 1;
                                        foreach($items as $item){
                                             $item_name = trim($item['item_brand_name']);
                                            if((trim($item_name) != trim($item['item_generic_name'])) && (trim($item['item_generic_name']) != ''))
                                                $item_name = '<b>'.$item_name.'</b> '.trim($item['item_generic_name']);

                                            $item_name .= ' <span class="badge badge-qty">'.trim($item['preparation']).'</span>';
                                            $item_name .= ' <span class="badge">'.trim($item['strength']).'</span>';
                                            
                                            echo '
                                    <tr>
                                        <td title="'.$item['location'].'">'.$sn++.'</td>
                                        <td title="'.$item['peachtree_detail']['peachtree_description'].'">'.$item['peachtree_detail']['peachtree_id'].'_'.$peachtree_suffix.'</td>
                                        <td><a href="/inventory/finance/stock_card?item_id='.$item['id'].'&store_id='.$store_id.'" target="_blank" title="Stock Card'."\n".'ID: '.$item['id'].'" tabindex="-1"><i class="fa fa-file-text"></i></a></td>
                                        <td>'.$item_name.'</td>
                                        <td>
                                            <input id="quantity_'.$item['id'].'"  type="number" class="form-control" value="'.$item['quantity'].'" '.(!$inactive?' onchange="save_change(\''.$item['id'].'\')':' disabled ').'">
                                        </td>
                                        <td><input id="remark_'.$item['id'].'" type="text" class="form-control"  value="'.$item['remark'].'" tabindex="-1" '.(!$inactive?' onchange="save_change(\''.$item['id'].'\') ':' disabled ').'"></td>
                                        <td>
                                            <i id="spiner_'.$item['id'].'" class="fa fa-spinner fa-spin" style="display:none;"></i>
                                        </td>
                                    </tr>
                                            ';
                                        }
                                    ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>

   
    $(document).ready(function() {
        var t = $('#item_count_record_list').DataTable( {
            info: true,
            lengthMenu: [
                [50, 100,-1],
                [50, 100, 'All']
            ]
            //ordering: false,
            //paging: false,
            //scrollCollapse: true,
           // scrollY: '300px'
        } );
    });

    <?php
        if(!$inactive):
    ?>
        function save_change(item_id){
            let quantity = $('#quantity_'+item_id).val().trim();
            let remark = $('#remark_'+item_id).val().trim();
            let spiner = $('#spiner_'+item_id);
            let store_id = '<?= $store_id ?>';
            let inventory_count_id = '<?= $inventory_count_setup_id ?>';
            spiner.show();

            $.ajax({
                url:'/inventory/finance/inventory_count_record_add',
                data:{item_id:item_id, quantity:quantity, remark:remark, store_id:store_id, inventory_count_id:inventory_count_id},
                method:'post',
                dataType:'json',
                success:function(result){
                    if(result.status != 'success')
                        alert('Error. Data is not updated');

                    spiner.hide();
                }
            });
        }
    <?php
        endif
    ?>
    
</script>
<?= view('Include/_footer') ?>

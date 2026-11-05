<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Dispensary/_left_sidebar_dispensary') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<?php
    $show_zero = $_GET['show_zero'] ?? 0;
    $item_type = $item_type ?? ($_GET['item_type'] ?? '');
    $item_type = strtoupper(trim($item_type));
    if(!in_array($item_type,['DRUG','COSMOTICS']))
        $item_type = '';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?= $store_id==2?'Dispensary':($store_id==3?'Compounding':'Store')?></h1>
            <small style="font-weight: bold; font-size: 16px; color: #007bff;">Inventory - Store: <?= $store_id==2?'Dispensary':($store_id==3?'Compounding':'Store') ?> | Type: <?= $item_type ? ($item_type=='DRUG'?'Drugs':($item_type=='COSMOTICS'?'Cosmetics':'All')) : 'All' ?></small>
            <ol class="breadcrumb">
                <?php
                    $store_q = '&store_id='.$store_id;
                    $type_q = $item_type ? '&item_type='.$item_type : '';
                    if($show_zero == 1){
                        echo '<li><a href="?show_zero=0'.$store_q.$type_q.'">Hide Zero</a></li>';
                    }else{
                        echo '<li><a href="?show_zero=1'.$store_q.$type_q.'">Show Zero</a></li>';
                    }
                ?>
                <li class="active"><a onclick="print_printable_document()">Print</a></li>
            </ol>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel" id="printable">
                    <div class="panel-heading">
                        <a href="?store_id=2&show_zero=<?=$show_zero?><?= $item_type ? '&item_type='.$item_type : '' ?>" class="btn btn-primary btn-sm">Dispensary</a>
                        <a href="?store_id=3&show_zero=<?=$show_zero?><?= $item_type ? '&item_type='.$item_type : '' ?>" class="btn btn-primary btn-sm">Compounding</a>
                        <form class="form-inline" method="get" style="display:inline-block; margin-left:15px;">
                            <input type="hidden" name="store_id" value="<?= $store_id ?>">
                            <input type="hidden" name="show_zero" value="<?= $show_zero ?>">
                            <div class="form-group">
                                <label for="item_type" style="margin-right:5px;">Type</label>
                                <select name="item_type" id="item_type" class="form-control input-sm" style="margin-right:5px;">
                                    <option value=""<?= $item_type=='' ? ' selected' : '' ?>>All</option>
                                    <option value="DRUG"<?= $item_type=='DRUG' ? ' selected' : '' ?>>Drugs</option>
                                    <option value="COSMOTICS"<?= $item_type=='COSMOTICS' ? ' selected' : '' ?>>Cosmetics</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-default btn-sm">Filter</button>
                        </form>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-striped" id="inventory_data_table">
                            <thead>
                                <tr>
                                    <th></th><th>Item Code</th><th></th><th>Item Detail</th><th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sn = 1;
                                    foreach($inventory_data as $row){
                                        $item_name = trim($row['item_brand_name']);
                                        if((trim($item_name) != trim($row['item_generic_name'])) && (trim($row['item_generic_name']) != ''))
                                            $item_name = '<b>'.$item_name.'</b> '.trim($row['item_generic_name']);
                                        if(trim($row['preparation'])!='')
                                            $item_name .= ' <span class="badge badge-qty">'.trim($row['preparation']).'</span>';
                                        if(trim($row['strength'])!='')
                                            $item_name .= ' <span class="badge">'.trim($row['strength']).'</span>';

                                        echo '
                                <tr>
                                    <td>'.$sn++.'</td><td>'.$row['item_code'].'</td><td><a href="/inventory/dispensary/stock_card?stock_item_id='.$row['id'].'&store_id='.$store_id.'" target="_blank" title="Stock Card'."\n".'ID: '.$row['id'].'"><i class="fa fa-file-text"></i></a></td><td>'.$item_name.'</td><td>'.$row['current_balance'].'</td>
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
    </section>
</div>

<script>
    $(document).ready(function() {
        var t = $('#inventory_data_table').DataTable( {
            info: false,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'All']
            ]
            //ordering: false,
            //paging: false,
            //scrollCollapse: true,
           // scrollY: '300px'
        } );
    });


    function print_printable_document(){
        var originalContents = document.body.innerHTML;
        $('a').removeAttr('href');
        var printContents = document.getElementById('printable').innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
<?= view('Include/_footer') ?>

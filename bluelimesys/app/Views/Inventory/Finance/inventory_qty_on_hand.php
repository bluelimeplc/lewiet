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
            <small>Inventory / Quantity On Hand</small>
            <ol class="breadcrumb">
                <?php
                    if(($_GET['show_zero'] ?? 1) == 1){
                        echo '<li><a href="?show_zero=0'.($store_id!=0?'&store_id='.$store_id:'').'">Hide Zero</a></li>';
                    }else{
                        echo '<li><a href="?show_zero=1'.($store_id!=0?'&store_id='.$store_id:'').'">Show Zero</a></li>';
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
                <div class="panel">
                    <div class="panel-body">
                        Choose a store: 
                        <a role="button" href="?store_id=1<?= ($_GET['show_zero']??1)==0?'&show_zero=0':'' ?>" class="btn btn-success">Main Store</a>
                        <a role="button" href="?store_id=2<?= ($_GET['show_zero']??1)==0?'&show_zero=0':'' ?>" class="btn btn-warning">Dispensary</a>
                        <a role="button" href="?store_id=3<?= ($_GET['show_zero']??1)==0?'&show_zero=0':'' ?>" class="btn btn-danger">Compounding</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-12" >
                <div class="panel panel-bd"  style="min-height: 550px;">
                    <div class="panel-heading">
                        <a target="_blank" role="button" href="/inventory/finance/inventory_export_for_counting?store_id=<?= $store_id??'' ?>" class="pull-right btn btn-danger <?= $store_id<=0?' hide ':'' ?>">Export for counting</a>
                        <h4>Selected Store: <b><?= $store_name ?></b></h4>
                    </div>
                    <div class="panel-body" id="printable">
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
                                    <td>'.$sn++.'</td><td>'.$row['peachtree_id'].'</td><td><a href="/inventory/finance/stock_card?item_id='.$row['id'].'&store_id='.$store_id.'" target="_blank" title="Stock Card'."\n".'ID: '.$row['id'].'"><i class="fa fa-file-text"></i></a></td><td title="'.$row['peachtree_description'].'">'.$item_name.'</td><td>'.$row['current_balance'].'</td>
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

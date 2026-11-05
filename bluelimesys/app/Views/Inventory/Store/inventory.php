<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Store/_left_sidebar_store') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Store</h1>
            <small>Inventory</small>
            <ol class="breadcrumb">
                <?php
                    if(($_GET['show_zero'] ?? 0) == 1){
                        echo '<li><a href="?show_zero=0">Hide Zero</a></li>';
                    }else{
                        echo '<li><a href="?show_zero=1">Show Zero</a></li>';
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
                                    <td>'.$sn++.'</td><td>'.$row['item_code'].'</td><td><a href="/inventory/store/stock_card?stock_item_id='.$row['id'].'&store_id=1" target="_blank" title="Stock Card'."\n".'ID: '.$row['id'].'"><i class="fa fa-file-text"></i></a></td><td>'.$item_name.'</td><td>'.$row['current_balance'].'</td>
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

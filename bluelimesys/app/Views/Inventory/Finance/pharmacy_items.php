<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small>Pharmacy Items</small>
            
        </div>
    </section>

<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div  class="panel">
                    <div class="panel-heading">
                        <div class="panel-title" >
                            <h4>
                                <a class="btn btn-danger" href="/inventory/finance/export_pharmacy_items?only_new=1" target="_blank">Export New for Peachtree (csv)</a>
                                <a class="btn btn-success" href="/inventory/finance/export_pharmacy_items?only_new=0" target="_blank">Export All for Peachtree (csv)</a>
                            </h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-hover" id="item_list">
                            <thead>
                                <tr>
                                    <th>Item</th><th>Peachtree ID</th><th>Peachtree description</th><th>Exported to Peachtree</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach($items as $item)
                                    {
                                        $items_name = '<b>'.$item['item_brand_name'] . '</b> ' . $item['item_generic_name'] . ' <span class="badge">' . $item['strength'] . '</span> <span class="badge badge-qty">' . $item['preparation'] . '</span>';
                                        echo '
                                <tr>
                                    <td>'.$items_name.'</td>';
                                    
                                    if(isset($item['peachtree_item']))
                                    {
                                        echo '<td>'.$item['peachtree_item']['peachtree_id'].'</td><td>'.$item['peachtree_item']['peachtree_description'].'</td><td>'.($item['peachtree_item']['exported'] == 1?'Yes':'No').'</td>';
                                    }
                                    else
                                    {
                                        echo '<td class="bg-red"></td><td class="bg-red"></td><td class="bg-red"></td>';
                                    }
                                        echo '
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
        var t = $('#item_list').DataTable( {
            //info: false,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'All']
            ]
            //ordering: false,
            //paging: false,
            //scrollCollapse: true,
           // scrollY: '300px'
        } );
    });
</script>

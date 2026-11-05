<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Dispensary</h1>
            <small>Dashboard</small>
            <ol class="breadcrumb">

                <li class="active"><a
                        href="#"><?php echo "Dashboard";?></a></li>
            </ol>
        </div>
    </section>


    <!-- Main content -->
    
    <section class="content">
    <div class="row">
        <div class="col-sm-12 col-md-12" style="min-height: 600px;">
            <div class="panel">
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#home">Near Expiry Items</a></li>
                        <li><a data-toggle="tab" href="#menu1">Low Store Balance</a></li>
                        <li><a data-toggle="tab" href="#onlyinstore">Only In Store</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="home" class="tab-pane fade in active">
                            <h3>Near Expiry Items</h3>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Batch Number</th>
                                        <th>Expiry Date</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        foreach($near_expiry_items as $item): 
                                            $item_name = $item['item_brand_name'];
                                            if($item['item_brand_name'] != $item['item_generic_name'])
                                                $item_name = '<b>'.$item_name.'</b> '.$item['item_generic_name'];
                                            $item_name .= ' <span class="badge">'.$item['strength'].'</span>';
                                            $item_name .= ' <span class="badge badge-qty">'.$item['preparation'].'</span>';
                                    ?>
                                    <tr>
                                        <td><?php echo $item_name; ?></td>
                                        <td><?php echo $item['batch_number']; ?></td>
                                        <td><?php echo $item['expiry_date'].' <span class="badge '.($item['remaining_days']<0?' badge-qty-red ':'').'">'.$item['remaining_days'].' days</span>'; ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>    
                        
                        </div>
                        <div id="menu1" class="tab-pane fade">
                            <h3>Low Balance Items</h3>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        foreach($low_balance_items as $item): 
                                            $item_name = $item['item_brand_name'];
                                            if($item['item_brand_name'] != $item['item_generic_name'])
                                                $item_name = '<b>'.$item_name.'</b> '.$item['item_generic_name'];
                                            $item_name .= ' <span class="badge">'.$item['strength'].'</span>';
                                            $item_name .= ' <span class="badge badge-qty">'.$item['preparation'].'</span>';
                                    ?>
                                    <tr>
                                        <td><?php echo $item_name; ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="onlyinstore" class="tab-pane fade">
                            <h3>Items Only In Store</h3>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Store Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        foreach($only_in_store_items as $item): 
                                            $item_name = $item['item_brand_name'];
                                            if($item['item_brand_name'] != $item['item_generic_name'])
                                                $item_name = '<b>'.$item_name.'</b> '.$item['item_generic_name'];
                                            $item_name .= ' <span class="badge">'.$item['strength'].'</span>';
                                            $item_name .= ' <span class="badge badge-qty">'.$item['preparation'].'</span>';
                                    ?>
                                    <tr>
                                        <td><?php echo $item_name; ?></td>
                                        <td><?php echo $item['store_quantity']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
    </div>
    

    </section> <!-- /.content -->
</div> <!-- /.content-wrapper -->





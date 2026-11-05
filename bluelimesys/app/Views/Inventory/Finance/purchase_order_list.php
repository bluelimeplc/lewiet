<?php
    $purchase_main = $this->data['purchase_main'];
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
            <small>Purchase Order List</small>
            <ol class="breadcrumb">
                <li class="active" role="button"><a role="button" href="/inventory/finance/pending_purchase_request">Purchase Request List</a></li>
            </ol>
        </div>
    </section>

<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div  class="panel">
                    <div class="panel-heading">
                        <div class="panel-title" >
                            <h4></h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>PO Code</th><th>Store</th><th>Requested By</th><th>Description</th><th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sn = 0;
                                    foreach($purchase_main as $purchase)
                                    {
                                        
                                        $urgent = ($purchase['is_urgent']==1)? "bg-danger":"";
                                        $sn++;
                                        echo '
                                <tr class="'.$urgent.'">
                                    <td>'.'PO'.sprintf('%04d', $purchase['id']).'</td>
                                    <td>'.$purchase['store_name'].'</td>
                                    <td>'.$purchase['name'].'</td>
                                    <td>'.$purchase['description'].'</td>
                                    <td><a href="/inventory/finance/purchase_order_print?purchase_id='.$purchase['id'].'" class="btn btn-primary btn-xs"> <i class="fa fa-eye"></i></a>
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
    </section>
</div>

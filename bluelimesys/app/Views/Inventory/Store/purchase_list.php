<?php
    $purchase_main = $this->data['purchase_main'];
    $search_key = $this->data['search_key'];
?>

<link href="/css/autocomplete.css" rel="stylesheet" type="text/css"/>

<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Store</h1>
            <small>Purchase Request List</small>
            <ol class="breadcrumb">
                <li class="active" role="button"><a role="button" href="/inventory/store/purchase_form">Purchase Form</a></li>
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
                        <div>
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <input type="text" name="search_key" placeholder="Purchase Code" class="form-control">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success">Search <i
                                                class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SN</th><th>Purchase Code</th><th>Description</th><th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sn = 0;
                                    foreach($purchase_main as $purchase)
                                    {
                                        $sn++;
                                        echo '
                                <tr>
                                    <td>'.$sn.'</td><td>'.$purchase['purchase_code'].'</td><td>'.$purchase['description'].'</td><td><a href="/inventory/store/purchase_print?purchase_id='.$purchase['id'].'" class="btn btn-primary btn-xs"> <i class="fa fa-eye"></i></a></td>
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

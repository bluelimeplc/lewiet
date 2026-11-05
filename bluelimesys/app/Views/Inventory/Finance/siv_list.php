<?php
    $siv_main = $this->data['siv_main'];
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
            <h1>Finance</h1>
            <small>Store Issue List</small>
            <ol class="breadcrumb">
                
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
                        <div class="row">
                            <div class="col-sm-10">
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <input type="text" name="search_key" placeholder="SIV Code" class="form-control">
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-success">Search <i
                                                    class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-danger" onclick="show_export_multiple()">Export multiple (CSV)</button>
                            </div>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SN</th><th>SIV Code</th><th>Issue Date</th><th>Requesting Store</th><th>Issuing Store</th><th>Description</th><th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sn = 0;
                                    foreach($siv_main as $siv)
                                    {
                                        $sn++;
                                        echo '
                                <tr>
                                    <td>'.$sn.'</td><td>SIV-'.sprintf('%04d',$siv['id']).'</td><td>'.$siv['created_at'].'</td><td>'.$siv['store_name'].'</td><td>'.$siv['issuing_store'].'</td><td>'.$siv['description'].'</td>
                                    <td><a href="/inventory/finance/siv_print?siv_id='.$siv['id'].'" class="btn btn-'.($siv['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND?'warning':($siv['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_THIRD?'primary':'success')).' btn-sm"> <i class="fa fa-eye"></i></a></td>

                                </tr>
                                        ';
                                    }
                                ?>
                            </tbody>
                        </table>
                        <?= $pager->links() ?>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <div>
        <div class="modal fade" id="modal_export_multiple" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Export Multiple SIV</h4>
                    </div>
                    <form id="form_export_multiple" action="/inventory/finance/export_siv_csv_multiple" method="get" target="_blank">

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="">
                                        <label for="from">From</label><br>
                                        <input type="date" class="form-control" id="from" name="from" value="<?=  date('Y-m-d') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to">To</label><br>
                                        <input type="date" class="form-control" id="to" name="to">
                                    </div>
                                </div>
                                <div class="col-md-12">

                                    <fieldset class="reset" style="margin-bottom:10px; border: solid 1px #ccc; border-radius: 5px;">
                                        <legend class="reset">Stores</legend>
                                        <?php
                                            foreach($stores as $store){
                                                if($store['id'] == 1) continue; // skip main store
                                                echo '<div class="col-md-4"><label><input type="checkbox" name="store[]" value="'.$store['id'].'" '.($store['id']!=4?'checked':'').'> '.$store['store_name'].'</label></div>';
                                            }
                                        ?>
                                    </fieldset>
                                </div>
                            

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="checkbox" class="form-check" id="include_exported" name="include_exported" value="1">
                                        <label for="include_exported">Include exported</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger" onclick="export_multiple_siv()" name="export_button" value="purchase">
                                Export As Purchase
                            </button>
                            <button class="btn btn-warning" onclick="export_multiple_siv()" name="export_button" value="adjustment">
                                Export As Adjustment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function show_export_multiple(){
        $('#modal_export_multiple').modal('show');
    }
</script>

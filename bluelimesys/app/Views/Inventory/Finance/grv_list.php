<?php
    $grv_main = $this->data['grv_main'];
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
            <small>Goods Receiving List</small>
            
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
                                        <input type="text" name="search_key"  value="<?= $search_key ?>" placeholder="GRV Code" class="form-control">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success">Search <iclass="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>SN</th><th>GRV Code</th><th>Supplier</th><th>Invoice N<u>o</u></th><th>FS N<u>o</u></th><th>Recieved Date</th><th>CPV</th><th>Description</th><th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sn = ($page-1) * BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST;
                                    $clr = '';
                                    foreach($grv_main as $grv)
                                    {
                                        if($grv['cpv']==''){
                                            $clr = 'background-color: ;';
                                        }else{
                                            $clr = 'background-color: chocolate;color: white;';
                                        }
                                        $sn++;
                                        echo '
                                <tr id="ClickableRow" style="cursor: pointer;">
                                    <td>'.$sn.'</td><td>'.$grv['grv_code'].'</td><td>'.$grv['supplier_detail']['company_name'].'</td><td>'.$grv['invoice_no'].'</td><td>'.$grv['fs_no'].'</td><td>'. substr($grv['recieved_date'] ,0,10) .'</td><td style="'.$clr.'">'.$grv['cpv'].'</td><td>'.$grv['description'].'</td>
                                    <td><a href="/inventory/finance/grv_print?grv_id='.$grv['id'].'" class="btn btn-'.($grv['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST?'warning':($grv['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND?'primary':'success')).' btn-sm"> <i class="fa fa-eye"></i></a></td>
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
</div>

<script>
    $('tr#ClickableRow').click(function () {
    window.location = $(this).find('a').attr("href");
});
</script>
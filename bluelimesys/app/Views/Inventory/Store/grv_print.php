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
            <small>Goods Receiving</small>
            <ol class="breadcrumb">
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
                        <?php
                            if($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::PENDING){
                                echo '<a href="/inventory/store/approve_grv?grv_id='.$grv_main['id'].'" class="btn btn-success" title="Send to Finance"><i class="fa fa-check-circle-o"></i> Approve</a> ';
                                echo '<a href="/inventory/store/grv_form?grv_id='.$grv_main['id'].'&mode=edit" class="btn btn-pink"><i class="fa fa-edit"></i> Edit GRV</a>';
                                echo ' <a class="btn btn-danger" onclick="delete_grv()"><i class="fa fa-trash"></i> Delete</a> ';
                            }
                            elseif($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST){
                                echo '<a href="/inventory/store/reverse_approved_grv?grv_id='.$grv_main['id'].'" class="btn btn-warning" title="Revert from Finance"><i class="fa fa-reply"></i> Revert</a> ';
                                echo '<a disabled class="btn btn-pink"><i class="fa fa-edit"></i> Edit GRV</a>';
                                echo ' <a disabled class="btn btn-danger"><i class="fa fa-trash"></i> Delete</a> ';
                            }
                            else{
                                echo '<a disabled class="btn btn-warning" title="Revert from Finance"><i class="fa fa-reply"></i> Revert</a> ';
                                echo '<a disabled class="btn btn-pink"><i class="fa fa-edit"></i> Edit GRV</a>';
                                echo ' <a disabled class="btn btn-danger"><i class="fa fa-trash"></i> Delete</a> ';
                            }


                        ?>
                        <div style="float: right;">
                            <table>
                                <tr style="text-align: center;">

                                    <td rowspan="2">Status&nbsp;</td>
                                    <td><?= ($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::PENDING)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::EXPORTED_FOR_PEACHTREE)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-qty">Pending</span></td>
                                    <td>&nbsp;<span class="badge">Approved</span></td>
                                    <td>&nbsp;<span class="badge badge-qty-warning">Finance</span></td>
                                    <td>&nbsp;<span class="badge badge-qty-red">Posted</span></td>
                                </tr>
                            </table>
                        </div>       
                    </div>
                </div>
            </div>
        </div>
        <?= view('Include/_print_grv'); ?>
    </section>
</div>

<script>
    function delete_grv(){
        if(confirm('Are you sure?'))
            window.location = '/inventory/store/delete_grv?grv_id=<?=$grv_main['id']?>';
    }
</script>

<?= view('Include/_footer') ?>

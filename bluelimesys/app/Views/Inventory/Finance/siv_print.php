<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Finance/_left_sidebar_finance') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small>Store Issue</small>
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
                            if($siv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND)
                                echo '<a href="/inventory/finance/receive_siv?siv_id='.$siv_main['id'].'" class="btn btn-success" title=""><i class="fa fa-check-circle-o"></i> Receive</a> ';
                            else
                                echo '<a disabled class="btn btn-success" title=""><i class="fa fa-check-circle-o"></i> Receive</a> ';
                            
                            if($siv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_THIRD)
                                echo '<a href="/inventory/finance/return_siv?siv_id='.$siv_main['id'].'" class="btn btn-warning" title=""><i class="fa fa-reply"></i> Return</a> ';
                            else
                                echo '<a disabled class="btn btn-warning" title=""><i class="fa fa-reply"></i> Return</a> ';

                            if($siv_main['status'] >= BluelimeConfiguration::VOUCHER_SATUS::APPROVED_THIRD)
                                echo '<a target="_blank" href="/inventory/finance/export_siv?siv_id='.$siv_main['id'].'" class="btn btn-danger" title=""><i class="fa fa-apple"></i> Eport CSV (Peachtree)</a> ';
                            else
                                echo '<a disabled class="btn btn-danger" title=""><i class="fa fa-apple"></i> Eport CSV (Peachtree)</a> ';

                        ?>
                        <div style="float: right;">
                            <table>
                                <tr style="text-align: center;">

                                    <td rowspan="2">Status&nbsp;</td>
                                    <td><?= ($siv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::PENDING)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($siv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($siv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($siv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_THIRD)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($siv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::EXPORTED_FOR_PEACHTREE)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-qty">Pending</span></td>
                                    <td>&nbsp;<span class="badge">Approved<br>Store</span></td>
                                    <td>&nbsp;<span class="badge bg-violet">Approved<br>Pharmacy</span></td>
                                    <td>&nbsp;<span class="badge badge-qty-warning">Finance</span></td>
                                    <td>&nbsp;<span class="badge badge-qty-red">Posted</span></td>
                                </tr>
                            </table>
                        </div>       
                    </div>
                </div>
            </div>
        </div>
        <?= view('Include/_print_siv'); ?>
        <script>
            <?php 
                if($siv_main['status'] < BluelimeConfiguration::VOUCHER_SATUS::APPROVED_THIRD)
                    echo '$(document).ready(function(){$(\'.blurable\').css(\'filter\',\'blur(2px)\');});';
            ?>
        </script>
    </section>
</div>

<?= view('Include/_footer') ?>
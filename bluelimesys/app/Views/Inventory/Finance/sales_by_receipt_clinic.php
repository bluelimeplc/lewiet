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
            <small>Sales By Receipt (Clinic)</small>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="panel-title"><h4>Filter</h4></div>
                        <div class="panel-body">
                            <div class="row">
                                <form action="" method="GET">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset class="reset" style="border:solid 1px #ccc; border-radius: 5px; padding: 10px; margin: 10px;">
                                                <legend class="reset">Payment Method</legend>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <input type="radio" name="payment_method" value="Cash" id="payment_method_cash" <?= ((($_GET['payment_method']??'')!='Credit')?' checked ':'') ?>>
                                                        <label for="payment_method_cash">Cash</label>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <input type="radio" name="payment_method" value="Credit" id="payment_method_credit" <?= ((($_GET['payment_method']??'')=='Credit')?' checked ':'') ?>>
                                                        <label for="payment_method_credit">Credit</label>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <fieldset class="reset" style="border:solid 1px #ccc; border-radius: 5px; padding: 10px; margin: 10px;">
                                                <legend class="reset">Receipt Type</legend>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <input type="radio" name="receipt_type" value="Cash Register" id="receipt_type_cash_register" <?= ((($_GET['receipt_type']??'')!='Manual')?' checked ':'') ?>>
                                                        <label for="receipt_type_cash_register">Cash Register</label>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <input type="radio" name="receipt_type" value="Manual" id="receipt_type_manual" <?= ((($_GET['receipt_type']??'')=='Manual')?' checked ':'') ?>>
                                                        <label for="receipt_type_manual">Manual</label>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class=" col-lg-3" style="margin-bottom: 2px;">
                                        <div class="input-group">
                                            <span class="input-group-addon" ><b>From</b></span>
                                            <input type="date" class="form-control" value="<?= substr($from_date,0,10);?>" name="selected_date">
                                        </div>
                                    </div>
                                    <div class=" col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" ><b>To</b></span>
                                            <input type="date" class="form-control" value="<?=substr($to_date,0,10);?>"  name="end_date">    
                                        </div>
                                        <input type="submit" style="visibility:hidden">
                                    </div>
                                    <input type="hidden" name="sales_type" value="<?= ($_GET['sales_type'] ?? BluelimeConfiguration::SALES_TYPE::DISPENSARY) ?>" >
                                    <div class=" col-lg-3"><input type="submit" class="btn btn-success" value="Filter"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><b>Report Date <?= substr($from_date,0,10).((substr($from_date,0,10) != substr($to_date,0,10)) ? ' - '.substr($to_date,0,10) : '')  ?></b></h4>
                        </div>
                        
                    </div>

                    <div class="panel-body">
                        <table class="table table-bordered table-striped" id="item_sales_report_table">
                            <thead>
                                <tr>
                                    <th>Sn</th><th>Receipt N<u>o</u></th><th><?= $payment_method=='Credit' ? 'Company Name':'Payment Method' ?></th><th>Date</th><th>TOTAL<br><b><u><?= number_format($totals['total'],2) ?></u></b></th><th>Bank Transfer Amount<br><b><u><?= number_format($totals['bank_transfer_amount'],2) ?></u></b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    
                                    foreach($salse_report_data as $row){
                                    echo '
                                <tr>
                                    <td></td>
                                    <td><a href="/inventory/finance/receipt?fs_no='.$row['fs_number'].'&department=Hospital" target="_blank">'.$row['fs_number'].'</a></td>
                                    <td>'.($payment_method=='Credit' ? ($row['company_name']??'') : $row['payment_method']).'</td>
                                    <td>'.$row['date'].'</td>
                                    <td>'.number_format($row['total'],2).'</td>
                                    <td>'.(empty($row['bank_transfer_amount']) ? '' : number_format($row['bank_transfer_amount'],2)).'</td>
                                </tr>
                                    ';
                                    }
                                ?>
                            </tbody>
                        </table>
                        <?php
                            if(empty($salse_report_data))
                                echo 'There is no sales in these filter';
                        ?>
                    </div>
                </div>

            </div>
        </div>

        
    </section>
</div>
<script>
    $(document).ready(function() {
        var t = $('#item_sales_report_table').DataTable( {
            //info: false,
            paging: false,
            columnDefs: [
                {
                    searchable: false,
                    orderable: false,
                    targets: 0
                }
            ],
            order: [[1, 'asc']]
        } );

        t
            .on('order.dt search.dt', function () {
                let i = 1;
        
                t
                    .cells(null, 0, { search: 'applied', order: 'applied' })
                    .every(function (cell) {
                        this.data(i++);
                    });
            })
            .draw();

    });
</script>

<?= view('Include/_footer') ?>
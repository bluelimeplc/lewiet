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
            <small>Hospital Bills Report</small>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="panel-title"><h4>Date filter</h4></div>
                        <div class="panel-body">
                            <div class="row">
                                <form action="" method="GET">
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
                                    <div class=" col-lg-3"><input type="submit" class="btn btn-success" value="Go"></div>
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
                            <div style="float: right;">
                                <a <?= !empty($salse_report_data)?'href="/inventory/finance/export_item_sales_report_in_csv?selected_date='.substr($from_date,0,10).'&end_date='.substr($from_date,0,10).'"':' disabled ' ?> target="_blank" class="btn btn-success">Export in CSV</a>
                                <a onclick="print_printable('service_grouped')" class="btn btn-success">Print</a>
                            </div>
                            <h4><b>Report Date <?= substr($from_date,0,10).((substr($from_date,0,10) != substr($to_date,0,10)) ? ' - '.substr($to_date,0,10) : '')  ?></b></h4>
                        </div>
                        
                    </div>

                    <div class="panel-body">
                        <div id="service_grouped">
                            <table class="table table-bordered table-striped" id="item_sales_report_table">
                                <thead>
                                    <tr>
                                        <th>Service Name</th><th>Unit Price</th><th>Qty</th><th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        
                                        
                                        foreach($bill_report_data as $row){
                                        echo '
                                    <tr>
                                        <td><b>'.$row['service_name'].'</b></td>
                                        <td align="right">'.number_format($row['unit_price'],2).'</td>
                                        <td>'.$row['quantity'].'</td>
                                        <td align="right">'.number_format($row['total'],2).'</td>
                                    </tr>
                                        ';
                                        }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" align="right"><b>TOTAL</b></td><td align="right"><b><u><?= number_format($total,2) ?></u></b></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <?php
                            if(empty($salse_report_data))
                                echo 'There is no sales in these date range';
                        ?>
                    </div>
                </div>

            </div>
        </div>

        
    </section>
</div>
<script>
    // $(document).ready(function() {
    //     var t = $('#item_sales_report_table').DataTable( {
    //         //info: false,
    //         lengthMenu: [
    //             [10, 25, 50, -1],
    //             [10, 25, 50, 'All']
    //         ]
    //         //ordering: false,
    //         //paging: false,
    //         //scrollCollapse: true,
    //        // scrollY: '300px'
    //     } );
    // });
</script>

<?= view('Include/_footer') ?>
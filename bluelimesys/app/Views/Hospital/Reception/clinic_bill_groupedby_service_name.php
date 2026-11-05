<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view(name: 'Hospital/Reception/_left_sidebar_reception') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Reception</h1>
            <small>Sales Summary Report</small>
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
                <div id="service_grouped">
                    <div class="panel">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <div style="float: right;">
                                    <a onclick="print_printable('service_grouped')" class="btn btn-success">Print</a>
                                </div>
                                <h4><span class="badge badge"><?= $_ENV['BRANCH'] ?? '' ?><br>Branch</span>  <b>Clinic <u><?= $_GET['payment_method']??'Cash' ?></u> Sales Summary Report<br>Report Date <?= substr($from_date,0,10).((substr($from_date,0,10) != substr($to_date,0,10)) ? ' - '.substr($to_date,0,10) : '')  ?></b></h4>
                            </div>
                            
                        </div>

                        <div class="panel-body">
                            
                            <table class="table table-bordered" id="item_sales_report_table">
                                <thead>
                                    <tr>
                                        <th></th><th></th><th>Service Name</th><th>Unit Price</th><th>Qty</th><th>Total</th><th width="150">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        
                                        $sn=1;
                                        $prv_service_type = '';
                                        foreach($bill_report_data as $row){
                                            if(($prv_service_type == '') || ($prv_service_type != $row['service_type'])){
                                                echo '
                                    <tr style="border-top:solid 2px;">
                                        <td colspan="6" style="background-color:#eee;"><b>'.$row['service_type'].'</b></td><td></td>
                                    </tr>
                                                ';
                                                $sn=1;
                                            }

                                        echo '
                                    <tr>
                                        <td></td>
                                        <td>'.$sn++.'</td>
                                        <td><b>'.$row['service_name'].'</b></td>
                                        <td><b>'.$row['unit_price'].'</b></td>
                                        <td>'.$row['quantity'].'</td>
                                        <td align="right">'.number_format($row['total'],2).'</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                        ';
                                            $prv_service_type = $row['service_type'];
                                        }
                                    ?>
                                    <tr style="border-top:solid 2px;">
                                        <td colspan="5" align="right"><b>TOTAL</b></td><td align="right"><b><u><?= number_format($total,2) ?></u></b></td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php
                                if(empty($bill_report_data))
                                    echo 'There is no sales in these date range';
                            ?>
                        </div>
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
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
            <small>Cash Sales Grouped Item By Unit Price</small>
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

                            <div style="float: right;">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <label for="reference_suggest">Reference:</label>
                                    </div>
                                    <div class="col-sm-5">
                                        <input id="reference_suggest" type="text" maxlength="20" value="<?= $reference ?>" class="form-control">
                                    </div>
                                    <div class="col-sm-4">
                                        <a <?= !empty($salse_report_data)?'': ' disables ' ?> target="_blank" class="btn btn-success" onclick="return set_reference(this);">Export in CSV</a>
                                    </div>
                                </div>
                            </div>
                            <h4><b>Report Date <?= substr($from_date,0,10).((substr($from_date,0,10) != substr($to_date,0,10)) ? ' - '.substr($to_date,0,10) : '')  ?></b></h4>
                        </div>
                        
                    </div>

                    <div class="panel-body">
                        <div class="col-lg-4">
                            <table class="table">
                                <tr>
                                    <th>Pharmacy Sales:</th><td align="right"><?= number_format($salse_report_data['pharmacy_sales'],2) ?></td>
                                </tr>
                                <tr>
                                    <th>Cosmetics Sales:</th><td align="right"><?= number_format($salse_report_data['cosmetics_sales'],2) ?></td>
                                </tr>
                                <tr>
                                    <th>Cosmetics VAT:</th><td align="right"><?= number_format($salse_report_data['pharmacy_vat'],2) ?></td>
                                </tr>
                                <tr>
                                    <th>SUB TOTAL:</th><td align="right"><b><?= number_format($salse_report_data['pharmacy_vat'] + $salse_report_data['cosmetics_sales'] + $salse_report_data['pharmacy_sales'],2) ?></b></td>
                                </tr>
                                <tr>
                                    <th> </th><td align="right">&nbsp;</td>
                                </tr>

                                <tr>
                                    <th>Compounding Sales:</th><td align="right"><?= number_format($salse_report_data['compounding_sales'],2) ?></td>
                                </tr>
                                <tr>
                                    <th>Compounding Taxable Sales:</th><td align="right"><?= number_format($salse_report_data['compounding_taxable_sales'],2) ?></td>
                                </tr>
                                <tr>
                                    <th>Compounding VAT:</th><td align="right"><?= number_format($salse_report_data['compounding_vat'],decimals: 2) ?></td>
                                </tr>
                                <tr>
                                    <th>SUB TOTAL:</th><td align="right"><b><?= number_format($salse_report_data['compounding_sales'] + $salse_report_data['compounding_taxable_sales'] + $salse_report_data['compounding_vat'],2) ?></b></td>
                                </tr>
                                <tr>
                                    <th> </th><td align="right">&nbsp;</td>
                                </tr>
                                <tr>
                                    <th><h4>TOTAL:</h4></th><td align="right"><h4><b><?= number_format($salse_report_data['compounding_sales'] + $salse_report_data['compounding_taxable_sales'] + $salse_report_data['compounding_vat']+$salse_report_data['pharmacy_vat'] + $salse_report_data['cosmetics_sales'] + $salse_report_data['pharmacy_sales'],2) ?></b></h4></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        
    </section>
</div>
<script>
    function set_reference(link2){
        let url = "/inventory/finance/export_item_cash_sales_report_in_csv?from_date=<?=substr($from_date,0,10).'&to_date='.substr($to_date,0,10)?>";
        //let link2 = document.getElementById('aa');
        let reference = $('#reference_suggest').val();
        link2.setAttribute('href',url+'&reference='+reference);
        return true;
    }
</script>

<?= view('Include/_footer') ?>
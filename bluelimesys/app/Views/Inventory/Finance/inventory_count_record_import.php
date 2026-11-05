<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Finance/_left_sidebar_finance') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-credit"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small>Inventory / Inventory Count Record Import</small>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <h4></h4>
                    </div>
                    <div class="panel-body">
                        Count Record : <b><?= $inventory_count_setup['title'] ?? '' ?></b><br>
                        Store/Department : <b><?= $store_name ?? '' ?></b>
                    </div>
                </div> 
            </div>
            <div class="col-lg-12" >
                <div class="panel"  style="min-height: 550px;">
                    <div class="panel-body">
                        Copy and Paste the CSV data below. Make sure the first row is the header row and the column names are correct. Each recored must be in a single line. The columns should be in the following order: <br>
                        <table class="table-bordered table-condensed"><tr><td>SN</td><td>peatchree_id</td><td>Item Description</td><td>Unit</td><td>System Qty</td><td>Count Qty</td><td>Unit Cost</td><td>Expiry Date</td><td>Remark</td></tr></table>
                        <br>When you paste the data the order may not look correcttly arranged, but the system will arrange it.
                        <form method="post" action="/inventory/finance/import_inventory_count_record_from_csv?store_id=<?= $store_id ?>&inventory_count_setup_id=<?= $inventory_count_setup_id ?>">
                            <textarea class="form-control" rows="20" name="pasted_csv_data"></textarea>
                            <button class="btn btn-success" style="float: right; margin-bottom: 10px;">Import</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>

   
    
</script>
<?= view('Include/_footer') ?>

<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Dispensary/_left_sidebar_dispensary') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Dispensary</h1>
            <small>Item Sales Report</small>
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
                                    <th>Item Type</th><th>Item</th><th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    
                                    
                                    foreach($salse_report_data as $row){
                                    echo '
                                <tr> 
                                <td>'.$row['item_detail']['item_type'].'</td>
                                    <td><b>'.$row['item_detail']['item_brand_name'].'</b> '.$row['item_detail']['item_generic_name'].' <span class="badge badge-qty">'.trim($row['item_detail']['preparation']).'</span> <span class="badge">'.trim(($row['item_detail']['strength']??'')).'</span></td>
                                     <td align="right" data-order="'.$row['total'].'">'.number_format($row['total'],2).'</td>
                                </tr>
                                    ';
                                    }
                                ?>
                            </tbody>
                        </table>
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
   $(document).ready(function() {
    var t = $('#item_sales_report_table').DataTable({
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, 'All']
        ], 
        columnDefs: [ 
            { targets: 2, type: 'num' }  
        ], 
            order: [[2, 'desc'], [1, 'asc']]
        // other options...
    });

    // Replace the default search input with a select
    $("#item_sales_report_table_filter").html(`
        <label>
            Item Type:
            <select id="customSearch" class="form-control">
                <option value="Drug">Drug</option>
                <option value="Cosmotics">Cosmotics</option>
            </select>
        </label>
    `);

    // Hook the select to DataTables search
    $('#customSearch').on('change', function() {
        t.search(this.value).draw();
    });
    var firstVal = $('#customSearch option:eq(0)').val(); // "Drug" 
    $('#customSearch').val(firstVal).trigger('change');
});

</script>

<?= view('Include/_footer') ?>
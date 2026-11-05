<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Cashier/_left_sidebar_menu') ?>
<?= view('Include/_left_sidebar_bottom') ?>
    
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1>Cashier</h1>
                <small>Attachment</small>
                <span style="padding-left: 300px;">
                    <a class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-print"></span></a>
                </span>
            </div>
        </section>

        
        <!-- Main content -->
        <section class="content">
            <?= view('Include/_attachment_inventory',['pos_bill'=>$pos_bill, 'sales_mains' => $sales_mains]) ?>
        </section>
    </div>
    
<?= view('Include/_footer') ?>
<script>

setTimeout("print_printable_document();", 1000);

function print_printable_document(){
    var printContents = document.getElementById('printable').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    close();
}
</script>
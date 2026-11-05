<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Dispensary</h1>
            <small>Print Prescription</small>
            
            <span style="padding-left: 300px;">
                <a class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-print"></span></a>
                <a class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-telegram"></span></a>
            </span>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <?= view('Include/_prescription_print') ?>
    </section>
</div>
<script>
    function print_printable_document(){
        var printContents = document.getElementById('printable').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
}
</script>
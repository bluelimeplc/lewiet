<?php
    $prescription_group_id = $_GET['prescription_group_id'] ?? -1;
?>
    <!-- =============================================== -->
    <!-- Content Wrapper. Contains page content -->

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1>Doctor</h1>
                <small>Prescription View</small>
            </div>
            
        </section>
        <section class="content">
            <div>
                <button class="btn btn-danger <?= (($compounding_mains[0]['is_sold']??false) || ($prescription_main['is_sold']??false)) ? ' hide ' : ' hide ' ?>" onclick="delete_prescription(<?= $prescription_group_id ?>)">Delete</button>
            </div>
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

    function delete_prescription(prescription_group_id){
        if(!confirm('Are you sure you want to delete this prescription?'))
            return 0;
        location = '/hospital/doctor/deleteprescription?prescription_group_id=<?= $prescription_group_id ?>';
    }
</script>
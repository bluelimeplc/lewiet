<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?php
    $logged_user = BluelimeAuthentication::get_logged_user_data();
    if($logged_user['role'] == BluelimeConfiguration::USER_ROLE::CASHIER_INVENTORY)
        echo view('Cashier/_left_sidebar_menu');
    else
        echo view('Hospital/Reception/_left_sidebar_reception',['active_menu'=>'list']);
?>
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
            <?= view('Include/_attachment_hospital',
             ['sales_main'=> $sales_main,'sub_total' => $sub_total,
                    'taxable_total' => 0,
                    'total_vat' => 0,
                    'total' => $sub_total,
                    'payment' => $payment,
                    'pos_bill' => $pos_bill]) 
            ?>
        </section> 
    </div>
    
<?= view('Include/_footer') ?>
<script>

    <?php
        $new = $_GET['new'] ?? 0;
        if(!$new):
    ?>
        setTimeout(function() {
            print_printable_document();
        }, 1000);
    <?php endif; ?>

    function print_printable_document(){
        var printContents = document.getElementById('printable').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        close();
    }
</script>
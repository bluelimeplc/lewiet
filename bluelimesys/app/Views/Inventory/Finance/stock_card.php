<?php
echo view('Include/_header')
.view('Include/_left_sidebar_top')
.view('Inventory/Finance/_left_sidebar_finance')
.view('Include/_left_sidebar_bottom');
?>
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<link href="/css/autocomplete.css" rel="stylesheet" type="text/css" />
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small>Stock Card</small>
        </div>
        
    </section>


    <section class="content">
        <?= view('Include/_stock_card') ?>
    </section>
</div>

<?php
echo view('Include/_footer');
?>
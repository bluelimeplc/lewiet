<?php
echo view('Include/_header')
.view('Include/_left_sidebar_top')
.view('Inventory/Store/_left_sidebar_store')
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
            <h1><?php echo lang('Store');?></h1>
            <small><?php echo lang('Stock Card');?></small>
        </div>
        
    </section>


    <section class="content">
        <?= view('Include/_stock_card') ?>
    </section>
</div>

<?php
echo view('Include/_footer');
?>
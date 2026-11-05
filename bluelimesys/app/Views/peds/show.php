<?php 
//BluelimeSystem::print_associated_array($sales_type_report);
?>
<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?php
    
?>
<?= view('Include/_left_sidebar_bottom') ?>

    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <small>Dashboard</small>
                <ol class="breadcrumb">
                    <li class="active"> <a href="/cashier/dashboard_detail" role="button">Detail</a></li>
                </ol>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            
            <div class="row">
                <!-- Doctor Assignment -->
                <div class="col-md-12">
                    <div class="panel panel-bd">
                        <div class="panel-body">
                            <table class="table table-bordered">
                                <tr><th width="200">PaymentType</th><td><input type="text" class="form-control"></td></tr>
                                <tr><th>SoldDate</th><td><input type="text" class="form-control"></td></tr>
                                <tr><th>FsInvoiceNo</th><td><input type="text" class="form-control"></td></tr>
                                <tr><th>InvoiceNumber</th><td><input type="text" class="form-control"></td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
<?= view('Include/_footer') ?>


<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Admin</h1>
            <small>Frequent Customers</small>
            <ol class="breadcrumb">
                            <?= view("Include/_admin_report_top_menu"); ?>
                        </ol>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="row">
            
            <div class="col-lg-12">
                <div class="col-sm-6 col-md-6">
                                <div class="panel panel-bd">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <h4>Frequent Customes Info</h4>
                                        </div>
                                        <div class="col-lg-1" style="float:right; margin-top:-30px;">
                                            <a role="button" onclick="print_printable('item_sales_report_table')" class="btn btn-success" style="float:right"><span class="fa fa-print"></span> Print</span></a>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group col-md-12 table-responsive">
                                            <table class="table table-bordered table-striped table-responsive" id="item_sales_report_table">
                                <thead>
                                    <tr>
                                        <th>S.N</th>
                                        <th>Customer Name</th>
                                        <th>Mobile</th>
                                        <th>No. Of Visits</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sn=0;
                                    foreach($report_data as $row){
                                        $sn++;
                                    ?>
                                    <tr><td><?=$sn?></td></td><td><?=$row['patient_name']?></td><td><?=$row['mobile']?></td><td><?=round($row['No_of_Visit'],2)?></td></tr>
                                <?php
                                        }
                                ?>
                                </tbody>
                            </table>
                                            
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                
            </div>
            
                </div>
    </section>
</div>
<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Dispensary/_left_sidebar_dispensary') ?>
<?= view('Include/_left_sidebar_bottom') ?>

    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1><a href="/cashier">Dispensary</a></h1>
                <small>POS</small>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <!-- Doctor Assignment -->
                <div class="col-md-6">
                    <div class="panel panel-bd">
                        <div class="panel-body table-responsive">
                        <div style="text-align: right;"><?= $pager->links() ?></div>
                            <table class="table table-bordered table-hover table-striped">
                                <tr>
                                    <th></th><th>Reference N<u>o</u></th><th>Customer name</th><th>Total</th><th>Invoice date</th>
                                </tr>
                            
                            <?php
                                $page = $_GET['page'] ?? 1;
                                $sn = ($page-1) * BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST;
                                foreach($pos_bills as $row){
                                    echo '
                                <tr>
                                    <td>'.++$sn.'</td>
                                    <td>'.($row['pos_bill_json']['invoice_no']??'').'</td>
                                    <td>'.($row['pos_bill_json']['customer_name']??'').'</td>
                                    <td>'.round(($row['total']),2).'</td>
                                    <td>'.substr(($row['updated_at']??''),0,10).'</td>';
                                    if($row['response_date'] != NULL)
                                        echo '<td title="Paid"><i class="fa fa-money"></i> FS '.$row['fs_number'].'</td>';
                                    else
                                        echo '<td><a onclick="reverse_invoice(\''.$row['id'].'\')" role="button" class="btn btn-primary" title="Reverse"><i class="fa fa-reply"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="delete_invoice(\''.$row['id'].'\')" role="button" class="btn btn-danger" title="Delete"><i class="fa fa-trash"></i></a></td>';

                                    echo '        
                                </tr>
                                    ';
                                }
                            ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        function reverse_invoice(pos_bill_id){
            $.ajax({
                url: '/inventory/dispensary/reverse_invoice',
                method: 'POST',
                data: {'pos_bill_id':pos_bill_id},
                dataType: 'json',
                async: false,
                success:function(result){
                    if(result.status=='success')
                        location = '/inventory/dispensary/checkout?sales_id_list='+result.value;
                    else
                        toastr['error'](result.message);
                },
                error:function(jqXHR,status,error){
                    toastr['error'](error);
                }
            });
        }

        function delete_invoice(pos_bill_id){
            $.ajax({
                url: '/inventory/dispensary/delete_invoice',
                method: 'POST',
                data: {'pos_bill_id':pos_bill_id},
                dataType: 'json',
                async: false,
                success:function(result){
                    if(result.status=='success')
                        location = location;
                    else
                        toastr['error'](result.message);
                },
                error:function(jqXHR,status,error){
                    toastr['error'](error);
                }
            });
        }
    </script>
<?= view('Include/_footer') ?>
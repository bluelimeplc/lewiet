<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Finance/_left_sidebar_finance') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small>Sales Receipts</small>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="panel panel-bd">
            <div class="panel-body">
                <style>
                    .number_cell{
                        text-align: right;
                    }
                    .error_cell{
                        background-color:lightpink;
                    }
                    thead {
                        position: sticky;
                        top: 0px;  /* 0px if you don't have a navbar, but something is required */
                        background: white; /* If you don't set a background color, you'll be able to see through the header as you scroll */
                    }
                </style>
                <form class="row">
                    <div style="padding: 10px;">
                        <div class="row">
                            <div class="col-md-2">
                                <label>From</label>
                                <input type="date" name="from_date" class="form-control" value="<?= $from_date ?>">
                            </div>
                            <div class="col-md-2">
                                <label>To</label>
                                <input type="date" name="to_date" class="form-control" value="<?= $to_date ?>">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary" style="margin-top: 25px;" name="btn_submit" value="filter"><i class="fa fa-filter"></i> View</button>
                                <button class="btn btn-success" style="margin-top: 2px;" name="btn_submit" value="export"><i class="fa fa-file-excel"></i> Export (CSV)</button>
                            </div>
                            
                            <div class="col-md-1">&nbsp;</div>
                            <div class="col-md-4">
                                <table class="table table-bordered">
                                    <?php
                                        $head = '<tr>';
                                        $indicator = '<tr>';
                                        foreach($api_connection as $api){
                                            $head .='<th><i class="badge bg-'.($api['status']==0?'red" title="Faild to connect"':($api['status']==1?'yellow" title="Connected but data is not fetched"':'green " title="Successfully connected"')).'>&nbsp;</i> '.ucfirst($api['branch']).'</th>';
                                            $indicator .= '<td><i class="badge bg-'.($api['status']==0?'red" title="Faild to connect"':($api['status']==1?'yellow" title="Connected but data is not fetched"':'green " title="Successfully connected"')).'>&nbsp;</i></td>';
                                        }
                                        $head .= '</tr>';
                                        $indicator .='</tr>';
                                        echo "$head";
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered sticky-header table-hover">
                    <thead class="sticky-header">
                        <tr><th>SN</th><th>Receipt N<u>o</u></th><th>Payment<br>Method</th><th>Credit Company</th><th>Amount</th><th>Partial Payment<br>(Cash)</th><th>Date</th><th>Reference</th></tr>
                    </thead>
                    <tbody>
                        <?php
                            $sn = 1;

                            foreach($receipts as $receipt){
                                $partial_payment = $receipt['total'] - $receipt['transferred_amount'];
                                if($partial_payment <= 1)
                                    $partial_payment = '';
                                elseif($receipt['transferred_amount'] <= 1)
                                    $partial_payment = '';

                                echo '
                        <tr>
                            <td>'.$sn++.'</td>
                            <td>'.$receipt['fs_number'].'</td>
                            <td>'.$receipt['payment_method'].'</td>
                            <td>'.$receipt['company']['company_name'].'</td>
                            <td>'.$receipt['total'].'</td>
                            <td>'.$partial_payment.'</td>
                            <td>'.$receipt['response_date'].'</td>
                            <td></td>
                        </tr>
                                ';
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <div>
        <div class="modal fade" id="sales_detail_modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Sales Detail <span id="gl_account_title"></span></h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>FS N<u>o</u></th><th>Payment</th><th>Date</th><th>Description</th><th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="sales_detail_table_body">
                                <tr><td colspan="3" style="text-align:center;">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    function drill_down(branch, gl_account, from, to){
        //alert('drill down '+branch + ', ' + gl_account + ', ' + from + ', ' + to);

        $('#sales_detail_table_body').html('<tr><td colspan="5" style="text-align:center;">Loading...</td></tr>');
        $('#gl_account_title').html(gl_account + ' (' + from + ' to ' + to +')');

        $('#sales_detail_modal').modal('show');
        $.ajax({
            url:'<?= site_url('Inventory/Finance/get_sales_detail') ?>',
            method:'POST',
            data:{
                branch:branch,
                gl_account:gl_account,
                from:from,
                to:to
            },
            dataType:'json',
            success:function(response){
                $('#sales_detail_table_body').html('');
                if(response.status == 'success'){
                    response.data.forEach(function(sale){
                        $('#sales_detail_table_body').append('<tr><td>'+sale.fs_number+'</td><td>'+sale.payment_method+'</td><td>'+sale.paid_date+'</td><td>'+sale.description+'</td><td style="text-align:right;">'+parseFloat(sale.amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})+'</td></tr>');
                    });
                }else{
                    $('#sales_detail_table_body').html('<tr><td colspan="3" style="text-align:center;">No data</td></tr>');
                }
            }
        });
    }


    
    
</script>

<?= view('Include/_footer') ?>
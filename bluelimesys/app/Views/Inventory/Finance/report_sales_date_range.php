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
            <small>Sales Report Date Range</small>
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
                                <input type="date" name="from" class="form-control" value="<?= $from ?>">
                            </div>
                            <div class="col-md-2">
                                <label>To</label>
                                <input type="date" name="to" class="form-control" value="<?= $to ?>">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary" style="margin-top: 25px;"><i class="fa fa-search"></i> Search</button>
                            </div>
                            
                            
                            <div class="col-md-1">&nbsp;</div>
                            <div class="col-md-4">
                                <table class="table table-bordered">
                                    <?php
                                        $head = '<tr>';
                                        //$indicator = '<tr>';
                                        foreach($api_connection as $api){
                                            $head .='<th>
                                                        <i class="badge bg-'.($api['status']==0?'red" title="Faild to connect"':($api['status']==1?'yellow" title="Connected but data is not fetched"':'green " title="Successfully connected"')).'>&nbsp;</i> '.ucfirst($api['branch']).' <input type="checkbox" checked onchange="filter_by_branch(this,'.$api['branch_number'].')"></th>';
                                            //$indicator .= '<td><i class="badge bg-'.($api['status']==0?'red" title="Faild to connect"':($api['status']==1?'yellow" title="Connected but data is not fetched"':'green " title="Successfully connected"')).'>&nbsp;</i></td>';
                                        }
                                        $head .= '</tr>';
                                        //$indicator .='</tr>';
                                        echo $head;
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-bordered sticky-header table-hover">
                    <thead class="sticky-header">
                        <th width="100">GL Account</th><th>Name</th><th>Cash</th><th>Credit</th><th>Total</th>
                    </thead>
                    <tbody>
                        <?php
                            $total_amount   = 0;
                            $total_cash     = 0;
                            $total_credit   = 0;
                            $branch_total = [];
                            foreach($sales_by_gl_account as $sales){
                                $total_amount   += $sales['amount'];
                                $total_cash     += $sales['cash'];
                                $total_credit   += $sales['credit'];

                                if(!isset($branch_total[$sales['branch']]))
                                    $branch_total[$sales['branch']] = ['amount'=>0,'cash'=>0,'credit'=>0];
                                
                                $branch_total[$sales['branch']]['amount'] += $sales['amount'];
                                $branch_total[$sales['branch']]['cash'] += $sales['cash'];
                                $branch_total[$sales['branch']]['credit'] += $sales['credit'];

                                echo '
                                <tr class="branch_'.$sales['branch'].'">
                                    <td>'.$sales['gl_account'].'</td><td>'.$sales['name'].'</td>
                                    
                                    <td class="number_cell">'.($sales['cash']>0?number_format($sales['cash'],2):'').'</td>
                                    <td class="number_cell">'.($sales['credit']>0?number_format($sales['credit'],2):'').'</td>
                                    <td class="number_cell" ondblclick="drill_down(\''.$sales['branch'].'\',\''.$sales['gl_account'].'\',\''.$from.'\',\''.$to.'\')">'.($sales['amount']>0?number_format($sales['amount'],2):'').'</td>
                                </tr>
                                ';
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #eee; font-weight: bold;">
                            <th class="number_cell" colspan="2">TOTAL</th>
                            <td class="number_cell" id="total_chash"><?=  number_format($total_cash,2) ?></td>
                            <td class="number_cell" id="total_credit"><?=  number_format($total_credit,2) ?></td>
                            <td class="number_cell" id="total_amount"><?=  number_format($total_amount,2) ?></td>
                        </tr>
                    </tfoot>
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
    var branch_total = <?= json_encode($branch_total) ?>;
    var total_amount = <?= $total_amount ?>;
    var total_cash = <?= $total_cash ?>;
    var total_credit = <?= $total_credit ?>;

    function filter_by_branch(checkbox, branch_number){
        let branch_total_amount = branch_total[branch_number] ? branch_total[branch_number]['amount'] : 0;
        let branch_total_cash = branch_total[branch_number] ? branch_total[branch_number]['cash'] : 0;
        let branch_total_credit = branch_total[branch_number] ? branch_total[branch_number]['credit'] : 0;

        if(checkbox.checked){
            total_amount += branch_total_amount;
            total_cash += branch_total_cash;
            total_credit += branch_total_credit;
            $('.branch_'+branch_number).show();
        }else{
            total_amount -= branch_total_amount;
            total_cash -= branch_total_cash;
            total_credit -= branch_total_credit;
            $('.branch_'+branch_number).hide();
        }
        $('#total_amount').text(total_amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#total_chash').text(total_cash.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#total_credit').text(total_credit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

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
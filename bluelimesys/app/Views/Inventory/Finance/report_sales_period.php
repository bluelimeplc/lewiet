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
            <small>Sales Report Period</small>
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
                        top: 50px;  /* 0px if you don't have a navbar, but something is required */
                        background: white; /* If you don't set a background color, you'll be able to see through the header as you scroll */
                    }
                </style>
                <form class="row">
                    <div style="padding: 10px;">
                        <label for="ec_year" class="col-md-2">EC Fiscal Year:</label>
                        <div class="col-md-2">
                            <select class="form-control" id="ec_year" name="ec_year" onchange="submit()">
                                <?php
                                    for($i=2017; $i<=2050;$i++)
                                        echo '<option value="'.$i.'" '.($ec_year == $i?' selected ':'').'>'.$i.'</option>';
                                ?>
                            </select>
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
                </form>

                <table class="table table-bordered sticky-header table-hover">
                    <thead class="">
                        <th width="100">GL Account</th><th>Name</th><th>Period 1 Cash</th><th>Period 1 Credit</th><th>Period 1</th><th>Period 2</th><th>Period 3</th><th>Period 4</th><th>Period 5</th><th>Period 6</th><th>Period 7</th><th>Period 8</th><th>Period 9</th><th>Period 10</th><th>Period 11</th><th>Period 12</th><th class="error_cell">Error</th><th>TOTAL</th>
                    </thead>
                    <tbody>
                        <?php
                            $has_error = false;
                            $p_total = ["p1"=>0,"p2"=>0,"p3"=>0,"p4"=>0,"p5"=>0,"p6"=>0,"p7"=>0,"p8"=>0,"p9"=>0,"p10"=>0,"p11"=>0,"p12"=>0,"p0"=>0];
                            foreach($fiscal_year_sales as $sales){
                                if(intval($sales['sales']["p0"]['amount']) < 0){
                                    $has_error = false;
                                }
                                foreach($p_total as $p_total_index => $val){
                                    $p_total[$p_total_index] += $sales['sales'][$p_total_index]['amount']??0;
                                }
                                $total = $sales['sales']["p1"]['amount'] +  $sales['sales']["p2"]['amount'] +  $sales['sales']["p3"]['amount'] +  $sales['sales']["p4"]['amount'] +  $sales['sales']["p5"]['amount'] +  $sales['sales']["p6"]['amount'] +  $sales['sales']["p7"]['amount'] +  $sales['sales']["p8"]['amount'] +  $sales['sales']["p9"]['amount'] +  $sales['sales']["p10"]['amount'] +  $sales['sales']["p11"]['amount'] +  $sales['sales']["p12"]['amount'] +  $sales['sales']["p0"]['amount'];
                                echo '
                                <tr>
                                    <td>'.$sales['gl_account'].'</td><td>'.$sales['name'].'</td>
                                    <td class="number_cell">'.($sales['sales']["p1"]['cash']>0?number_format($sales['sales']["p1"]['cash'],2):'').'</td>
                                    <td class="number_cell">'.($sales['sales']["p1"]['credit']>0?number_format($sales['sales']["p1"]['credit'],2):'').'</td>
                                    <td class="number_cell" ondblclick="drill_down(\''.$sales['branch'].'\',\''.$sales['gl_account'].'\',\''.($sales['sales']["p1"]['from']??'1990-01-01').'\',\''.($sales['sales']["p1"]['to']??'1990-01-01').'\')">'.($sales['sales']["p1"]['amount']>0?number_format($sales['sales']["p1"]['amount'],2):'').'</td>
                                    <td class="number_cell" ondblclick="drill_down(\''.$sales['branch'].'\',\''.$sales['gl_account'].'\',\''.($sales['sales']["p2"]['from']??'1990-01-01').'\',\''.($sales['sales']["p2"]['to']??'1990-01-01').'\')">'.($sales['sales']["p2"]['amount']>0?number_format($sales['sales']["p2"]['amount'],2):'').'</td>
                                    <td class="number_cell" ondblclick="drill_down(\''.$sales['branch'].'\',\''.$sales['gl_account'].'\',\''.($sales['sales']["p3"]['from']??'1990-01-01').'\',\''.($sales['sales']["p3"]['to']??'1990-01-01').'\')">'.($sales['sales']["p3"]['amount']>0?number_format($sales['sales']["p3"]['amount'],2):'').'</td>
                                    <td class="number_cell" ondblclick="drill_down(\''.$sales['branch'].'\',\''.$sales['gl_account'].'\',\''.($sales['sales']["p4"]['from']??'1990-01-01').'\',\''.($sales['sales']["p4"]['to']??'1990-01-01').'\')">'.($sales['sales']["p4"]['amount']>0?number_format($sales['sales']["p4"]['amount'],2):'').'</td>
                                    <td class="number_cell" ondblclick="drill_down(\''.$sales['branch'].'\',\''.$sales['gl_account'].'\',\''.($sales['sales']["p5"]['from']??'1990-01-01').'\',\''.($sales['sales']["p5"]['to']??'1990-01-01').'\')">'.($sales['sales']["p5"]['amount']>0?number_format($sales['sales']["p5"]['amount'],2):'').'</td>
                                    <td class="number_cell" ondblclick="drill_down(\''.$sales['branch'].'\',\''.$sales['gl_account'].'\',\''.($sales['sales']["p6"]['from']??'1990-01-01').'\',\''.($sales['sales']["p6"]['to']??'1990-01-01').'\')">'.($sales['sales']["p6"]['amount']>0?number_format($sales['sales']["p6"]['amount'],2):'').'</td>
                                    <td class="number_cell" ondblclick="drill_down(\''.$sales['branch'].'\',\''.$sales['gl_account'].'\',\''.($sales['sales']["p7"]['from']??'1990-01-01').'\',\''.($sales['sales']["p7"]['to']??'1990-01-01').'\')">'.($sales['sales']["p7"]['amount']>0?number_format($sales['sales']["p7"]['amount'],2):'').'</td>
                                    <td class="number_cell" ondblclick="drill_down(\''.$sales['branch'].'\',\''.$sales['gl_account'].'\',\''.($sales['sales']["p8"]['from']??'1990-01-01').'\',\''.($sales['sales']["p8"]['to']??'1990-01-01').'\')">'.($sales['sales']["p8"]['amount']>0?number_format($sales['sales']["p8"]['amount'],2):'').'</td>
                                    <td class="number_cell" ondblclick="drill_down(\''.$sales['branch'].'\',\''.$sales['gl_account'].'\',\''.($sales['sales']["p9"]['from']??'1990-01-01').'\',\''.($sales['sales']["p9"]['to']??'1990-01-01').'\')">'.($sales['sales']["p9"]['amount']>0?number_format($sales['sales']["p9"]['amount'],2):'').'</td>
                                    <td class="number_cell" ondblclick="drill_down(\''.$sales['branch'].'\',\''.$sales['gl_account'].'\',\''.($sales['sales']["p10"]['from']??'1990-01-01').'\',\''.($sales['sales']["p10"]['to']??'1990-01-01').'\')">'.($sales['sales']["p10"]['amount']>0?number_format($sales['sales']["p10"]['amount'],2):'').'</td>
                                    <td class="number_cell" ondblclick="drill_down(\''.$sales['branch'].'\',\''.$sales['gl_account'].'\',\''.($sales['sales']["p11"]['from']??'1990-01-01').'\',\''.($sales['sales']["p11"]['to']??'1990-01-01').'\')">'.($sales['sales']["p11"]['amount']>0?number_format($sales['sales']["p11"]['amount'],2):'').'</td>
                                    <td class="number_cell" ondblclick="drill_down(\''.$sales['branch'].'\',\''.$sales['gl_account'].'\',\''.($sales['sales']["p12"]['from']??'1990-01-01').'\',\''.($sales['sales']["p12"]['to']??'1990-01-01').'\')">'.($sales['sales']["p12"]['amount']>0?number_format($sales['sales']["p12"]['amount'],2):'').'</td>
                                    <td class="number_cell error_cell">'.($sales['sales']["p0"]['amount']>0?number_format($sales['sales']["p0"]['amount'],2):'').'</td>
                                    <td class="number_cell">'.(number_format($total,2)).'</td>
                                </tr>
                                ';
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #eee; font-weight: bold;">
                            <th colspan="2">TOTAL</th>
                            <td><?=  number_format($p_total['p1'],2) ?></td>
                            <td><?=  number_format($p_total['p2'],2) ?></td>
                            <td><?=  number_format($p_total['p3'],2) ?></td>
                            <td><?=  number_format($p_total['p4'],2) ?></td>
                            <td><?=  number_format($p_total['p5'],2) ?></td>
                            <td><?=  number_format($p_total['p6'],2) ?></td>
                            <td><?=  number_format($p_total['p7'],2) ?></td>
                            <td><?=  number_format($p_total['p8'],2) ?></td>
                            <td><?=  number_format($p_total['p9'],2) ?></td>
                            <td><?=  number_format($p_total['p10'],2) ?></td>
                            <td><?=  number_format($p_total['p11'],2) ?></td>
                            <td><?=  number_format($p_total['p12'],2) ?></td>
                            <td class="error_cell"><?=  number_format($p_total['p0'],2) ?></td>
                            <td><?=  number_format(array_sum($p_total),2) ?></td>
                        </tr>
                    </tfoot>
                </table>
                <?php
                    if(!$has_error):
                ?>
                    <style>
                        .error_cell{
                            display: none;
                        }
                    </style>
                <?php
                    endif
                ?>
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

    $(document).ready(function(){
        $(".sticky-header").floatThead({top:50});
    });
    
</script>

<?= view('Include/_footer') ?>
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
            <small>All Item Transaction</small>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="panel panel-bd">
            <div class="panel-body">
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
                        <th> </th><th width="100">Item ID</th><th>Store</th><th>Transaction type</th><th>Quantity</th><th>Date</th>
                    </thead>
                    <tbody>
                        <?php
                            $sn = 1;
                            foreach($transactions as $row){
                                echo '
                                <tr class="branch_'.$row['branch_number'].'">
                                    <td>'.$sn++.'</td>
                                    <td>'.$row['peachtree_id'].'</td><td>'.$row['store_name'].'</td>
                                    <td>'.$row['transaction_type'].'</td><td>'.$row['quantity'].' <span class="badge badge-qty-red">'.$row['remark'].'</span></td>
                                    <td>'.substr($row['created_at'],0,10).'</td>
                                </tr>
                                ';
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    
</div>
<script>
    function filter_by_branch(checkbox, branch_number){
        
        if(checkbox.checked){
            $('.branch_'+branch_number).show();
        }else{
            $('.branch_'+branch_number).hide();
        }
    }
    
    
</script>

<?= view('Include/_footer') ?>
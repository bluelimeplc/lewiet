<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small>Doctors Work </small>
           
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-body">
                        <form class="row" action="/inventory/finance/doctors_work" method="post">
                            <div class="col-lg-1">
                                <label for="date_from">Date From:</label>
                            </div>
                            <div class="col-lg-3">
                                <input type="date" name="date_from" id="date_from" class="form-control" value="<?= $date_from ?>">
                            </div>

                            <div class="col-lg-1">
                                <label for="date_to">Date To:</label>
                            </div>
                            <div class="col-lg-3">
                                <input type="date" name="date_to" id="date_to" class="form-control" value="<?= $date_to ?>">
                            </div>
                            <div class="col-lg-1">
                                <input type="submit" value="Filter" class="form-control btn-success">
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="panel">
                    <div class="panel-body">
                        -
                        <table class="table table-bordered">
                            <tbody>
                                <?php
                                    foreach($report_data as $row){
                                        echo '
                                <tr><td colspan="6">&nbsp;</td></tr>
                                <tr><td colspan="6">&nbsp;</td></tr>
                                <tr>
                                    <td colspan="6" style="text-align: center; background-color:#C6E0B4;"><b>'.$row['doctor']['name'].' ('.$date_from.' - '.$date_to.')</b></td>
                                </tr>
                                <tr>
                                    <th>SN</th><th>Service Name</th><th>Service Type</th><th>Qty</th><th>Total Price</th>
                                </tr>
                                ';
                                        $cnt = 1;
                                        $total = 0;
                                        foreach($row['works'] as $work){
                                            echo '
                                            <tr '.(($work['bill_type']=='Credit')?' style="background-color:#FFF2CC;" ':'').'>
                                                <td>'.$cnt.'</td><td>'.$work['service_name'].' '.(($work['bill_type']=='Credit')?'(Credit)':'').'</td>
                                                <td>'.$work['service_type'] . (isset($work['bill_amount_group'])?' ($'.$work['bill_amount_group'].')':'') .'</td><td>'.$work['bill_quantity'].'</td><td align="right">'.number_format($work['bill_amount']).'</td>
                                            </tr>
                                            ';
                                            $total += $work['bill_amount'];
                                            $cnt++;
                                        }
                                    
                                        // echo '
                                        // <tr>
                                        //     <td colspan="4" align="right"><b>TOTAL</b></td>
                                        //     <td align="right"><b>'.number_format($total).'</b></td>
                                        // </tr>
                                        // ';

                                    }
                                ?>
                            </tbody>
                        </table>
                        -
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
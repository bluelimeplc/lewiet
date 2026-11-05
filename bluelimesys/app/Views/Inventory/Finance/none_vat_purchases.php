
<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small>None VAT Purchases</small>
        </div>
    </section>

<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="panel-title"><h4>Filter</h4></div>
                    </div>
                    <div class="panel-body">
                        <form action="" method="GET">
                            <div class=" col-lg-3" style="margin-bottom: 2px;">
                                <div class="input-group">
                                    <span class="input-group-addon" ><b>From</b></span>
                                    <input type="date" class="form-control" value="<?= substr($from_date,0,10);?>" name="from_date">
                                </div>
                            </div>
                            <div class=" col-lg-3">
                                <div class="input-group">
                                    <span class="input-group-addon" ><b>To</b></span>
                                    <input type="date" class="form-control" value="<?=substr($to_date,0,10);?>"  name="to_date">    
                                </div>
                                <input type="submit" style="visibility:hidden">
                            </div>
                            <div class=" col-lg-3"><input type="submit" class="btn btn-success" value="Filter"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div  class="panel">
                    <div class="panel-heading">
                        <div class="panel-title" >
                            <h4></h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Seller TIN</th><th>Amount paid</th><th>Receipt Number</th><th>Receipt date</th><th>Calader Type</th><th>MRC (10 Digit)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    
                                    foreach($grv_details as $grv_detail){
                                        echo '
                                <tr>
                                    <td>'.$grv_detail['supplier']['tin_number'].'</td>
                                    <td>'.round($grv_detail['total'],2).'</td>
                                    <td>'.$grv_detail['grv_main']['fs_no'].'</td>
                                    <td>'.$grv_detail['grv_main']['receipt_date'].'</td>
                                    <td>'.($grv_detail['grv_main']['is_ethiopian_calander'] ? 'E' : 'G').'</td>
                                    <td>'.strtoupper($grv_detail['grv_main']['machine_no']).'</td>
                                </tr>
                                        ';
                                    }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

</script>
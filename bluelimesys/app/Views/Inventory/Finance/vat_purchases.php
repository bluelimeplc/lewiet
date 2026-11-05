
<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small>VAT Purchases</small>
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
                                    <th>S/G</th><th>Cal Type</th><th>Type of purchase</th><th>TIN</th><th>Saller Name</th><th>Date of purchase</th><th>MRC Number</th><th>Vat receipt number</th>
                                    <th>Description</th><th>Unit of Measure</th><th>Quantity</th><th>Unit Price</th><th>Total value</th><th>VAT</th><th>Value after VAT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    
                                    foreach($grv_details as $grv_detail){
                                        $text = $grv_detail['item_detail']['item_brand_name'].' '.$grv_detail['item_detail']['item_generic_name'].' '.$grv_detail['item_detail']['preparation'];
                                        $words = preg_split('/\W+/', $text, -1, PREG_SPLIT_NO_EMPTY);
                                        $uniqueWords = array_unique($words);
                                        $discription = implode(' ', $uniqueWords);
                                        
                                        echo '
                                <tr>
                                    <td>G</td>
                                    <td>'.($grv_detail['grv_main']['is_ethiopian_calander'] ? 'E' : 'G').'</td>
                                    <td>3</td>
                                    <td>'.$grv_detail['supplier']['tin_number'].'</td>
                                    <td></td>
                                    <td>'.$grv_detail['grv_main']['receipt_date'].'</td>
                                    <td>'.strtoupper($grv_detail['grv_main']['machine_no']).'</td>
                                    <td>'.$grv_detail['grv_main']['fs_no'].'</td>
                                    <td>'.$discription.'</td>
                                    <td>9</td>
                                    <td>'.$grv_detail['quantity'].'</td>
                                    <td>'.$grv_detail['unit_price'].'</td>
                                    <td>'.round($grv_detail['quantity']*$grv_detail['unit_price'],2).'</td>
                                    <td>'.round($grv_detail['quantity']*$grv_detail['unit_price']*.15,2).'</td>
                                    <td>'.round($grv_detail['quantity']*$grv_detail['unit_price']*1.15,2).'</td>
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
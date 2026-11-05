<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Dispensary/_left_sidebar_dispensary') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Dispensary</h1>
            <small>Credit Receipts</small>
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
                        <div class="row">
                            <form action="" method="GET">
                                <div class=" col-lg-3" style="margin-bottom: 2px;">
                                    <div class="input-group">
                                        <span class="input-group-addon" ><b>From</b></span>
                                        <input type="date" class="form-control" value="<?= substr( $date_from,0,length: 10);?>" name="date_from">
                                    </div>
                                </div>
                                <div class=" col-lg-3">
                                    <div class="input-group">
                                        <span class="input-group-addon" ><b>To</b></span>
                                        <input type="date" class="form-control" value="<?=substr($date_to,0,length: 10);?>"  name="date_to">    
                                    </div>
                                    <input type="submit" style="visibility:hidden">
                                </div>
                                <div class=" col-lg-3"><input type="submit" class="btn btn-success" value="Go"></div>
                            </form>
                        </div>
                    </div>
                    <div class="panel-footer">
                            <div  style="text-align: right;">
                                <button class="btn btn-info" onclick="show_hide_posted()">Show / Hide posted</button>
                                <a onclick="print_printable_new('service_grouped')" class="btn btn-success ">Print</a>
                            </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div id="service_grouped">
                    <div class="panel">
                        <div class="panel-heading">
                            <div class="panel-title text-center">
                                <h4><span class="badge badge"><?= $_ENV['BRANCH'] ?? '' ?><br>Branch</span>  <b>Pharmacy <u>Credit</u> Receipt List<br>Report Date <?= substr($date_from,0,10).((substr($date_from,0,10) != substr($date_to,0,10)) ? ' - '.substr($date_to,0,10) : '')  ?></b></h4>
                            </div>
                            
                        </div>

                        <div class="panel-body">
                            
                            <table class="table table-bordered table-striped" id="item_sales_report_tables">
                                <thead>
                                    <tr>
                                        <th></th><th width="100">Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>FS Number</th><th class="">Company Name</th><th>Customer Name</th><th>Total</th><th></th><th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sn =1;
                                        $total_amount = 0;
                                        $total_receipts = 0;
                                        foreach($sales_mains as $sales_main){
                                            $posted = intval($sales_main['credit_payment_request_id'])==0?false:true;
                                            if(!$posted){
                                                $total_amount += $sales_main['total_amount'];
                                                $total_receipts++;
                                            }
                                            echo '
                                    <tr'.($posted?' class="color-gray posted" ':'').'>
                                        <td>'.(!$posted?$sn++:'').'</td>
                                        <td>'.substr($sales_main['recieved_date'],0,10).'</td>
                                        <td>'.$sales_main['fs_number'].($posted?' <span class="badge bg-info">posted</span>':'').'</td>
                                        <td>'.$sales_main['company_name'].'</td>
                                        <td>'.$sales_main['patient_name'].'</td>
                                        <td align="right">'.number_format($sales_main['total_amount']).'</td>
                                        <td><input type="checkbox" '.(!$posted?'checked':'').' onchange="on_off_receipt('.$sales_main['total_amount'].',this,'.($posted?'true':'false').')"></td>
                                        <td contenteditable="true" width="100">'.($posted?'Posted':'').'</td>
                                    </tr>
                                    ';

                                        }
                                    ?>
                                    <tr class="bg-dark">
                                        <td colspan="5" align="right"><b>TOTAL</b></td><td align="right"><b><u id="total_amount"><?= number_format($total_amount) ?></u></b></td>
                                    </tr>
                                    <tr class="bg-dark">
                                        <td colspan="5" align="right"><b># Receipts</b></td><td align="right"><b id="total_receipts"><?= $total_receipts ?></b></td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php
                                if(empty($bills))
                                    echo 'There is no sales in these date range';
                            ?>
                        </div>
                    </div>
                </div>        
            </div>
        </div>
    </section>
</div>
<script>

    var total_amount = <?= $total_amount ?>;
    var total_receipts = <?= $total_receipts ?>;

    function on_off_receipt(amount, check_box, posted){
        if(check_box.checked){
            total_amount += amount;
            total_receipts++;
        }
        else{
            total_amount -= amount;
            total_receipts--;
        }
        $('#total_amount').html(total_amount.toLocaleString());
        $('#total_receipts').html(total_receipts);

        if(posted)
            $(check_box).closest('tr').toggleClass('posted');
    }

    var are_posted_visible = true;
    function show_hide_posted(){
        if(are_posted_visible)
            $('.posted').css('display','none');
        else
            $('.posted').css('display','');
        are_posted_visible = !are_posted_visible;
    }
    

    $(document).ready(function() {
        show_hide_posted();

        var t = $('#item_sales_report_table').DataTable({
            columnDefs: [
                {
                    searchable: false,
                    orderable: false,
                    targets: 0
                }
            ],
            order: [[1, 'asc']],
            on: {
                draw: (e) => {
                    let start = e.dt.page.info().start;
        
                    e.dt.column(0, {page: 'current'})
                        .nodes()
                        .each((cell, i) => {
                            cell.textContent = start + i + 1;
                        });
                }
            },
            info: false,
            // lengthMenu: [
            //     [ -1],
            //     ['All']
            // ]
            //ordering: false,
            //paging: false,
            //scrollCollapse: true,
           // scrollY: '300px'
        } );
    });
</script>

<?= view('Include/_footer') ?>
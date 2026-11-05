
<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?php
    $logged_user = BluelimeAuthentication::get_logged_user_data();
    if($logged_user['role'] == BluelimeConfiguration::USER_ROLE::CASHIER_INVENTORY)
        echo view('Cashier/_left_sidebar_menu');
    elseif($logged_user['role'] == BluelimeConfiguration::USER_ROLE::FINANCE)
        echo view('Inventory/Finance/_left_sidebar_finance');
    else
        echo view('Hospital/Reception/_left_sidebar_reception');
?>
<?= view('Include/_left_sidebar_bottom') ?>

<link rel="stylesheet" href="/Image-gallery/assets/css/jquery.lightbox.css">
<link rel="stylesheet" href="/Image-gallery/assets/css/demo.css">
<script src="/Image-gallery/assets/js/jquery.lightbox.js"></script>

<script src="assets/js/jquery.min.js"></script>
<style>
    	
    .dropZoneOverlay, .FileUpload {
            width: 120px;
            height: 130px;
        }

        .dropZoneOverlay {
            font-family: cursive;
            color: #7066fb;
            position: absolute;
            top: 0px;
            text-align: center;
        }

        .FileUpload {
            opacity: 0;
            position: relative;
            z-index: 1;
        }
</style>

    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1><a href="/cashier"><?= $cashier_type ?> Cashier</a></h1>
                <small>Dashboard</small>
                <ol class="breadcrumb">
                    <li class="active"> <a href="/cashier/dashboard_detail?filter_date=<?= $filter_date ?>" role="button">Detail</a></li>
                    <?php
                        if(($logged_user['role'] == 'SUPER_ADMIN') || ($logged_user['role'] == BluelimeConfiguration::USER_ROLE::FINANCE)){
                    ?>
                            <li class="active"><a href="?i=2&filter_date=<?= $filter_date ?>&receipt_type=<?= $receipt_type ?>"><?php echo "Hospital";?></a></li>
                            <li class="active"><a href="?i=1&filter_date=<?= $filter_date ?>&receipt_type=<?= $receipt_type ?>"><?php echo "Inventory";?></a></li>
                    <?php
                            }
                    ?>
                </ol>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            
            <div class="row">
                <!-- Doctor Assignment -->
                <div class="col-md-6">
                    <div class="panel panel-bd">
                        <div class="panel-body">
                            
                            <form class="col-lg-12" action="/cashier/dashboard" method="get" onsubmit="">
                                <fieldset class="reset" style="border: solid 1px #ccc; border-radius: 5px; margin: 10px;">
                                    <legend class="reset">Receipt Type</legend>
                                    <div class="col-lg-6">
                                        <input type="radio" name="receipt_type" <?= ($receipt_type??'')=='Manual'?'':' checked '?> id="receipt_type_cash_register" value="Cash Register" onchange="this.form.submit()">
                                        <label for="receipt_type_cash_register">Cash Register</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="radio" name="receipt_type" <?= ($receipt_type??'')=='Manual'?' checked ':''?> id="receipt_type_manual" value="Manual" onchange="this.form.submit()">
                                        <label for="receipt_type_manual">Manual</label>
                                    </div>
                                </fieldset>
                                <div class="form-group">
                                    <div class="col-lg-1">
                                        <label for="filter_date">Date: </label>
                                    </div>
                                    <div class="col-lg-8">
                                        <input type="date" class="form-control" value="<?= $filter_date ?>" id="filter_date" name="filter_date" onchange="this.form.submit()">
                                    </div>
                                    <div class="col-lg-1">
                                        <input name= "daily_report" id="daily_report" type="submit" value="Search" hidden />
                                    </div>
                                </div>
                            </form>
                            
                            <br>&nbsp;
                            <div id="report_printable">
                                <div>
                                    <br>
                                </div>
                                <div class="row">
                                    <div class="col-md-11">
                                        <div class="panel panel-bd">
                                            <div class="panel-heading">
                                                <br>
                                                <h4 style="font-size: medium;"><span class="badge badge"><?= $_ENV['BRANCH'] ?? '' ?><br>Branch</span> <span class="badge"><?= $cashier_type.'<br>'.$receipt_type ?></span>  <b></b> Cashier Report &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= $filter_date ?></span></h4>
                                            </div>
                                            <div class="panel-body">
                                                <?php
                                                    if($error_found)
                                                        echo '<h3 style="color: red; padding: 0px; margin: 0px; text-align:center"><i class="fa fa-times-circle"></i> There may be an FS error. </h3>';
                                                ?>
                                                <table class="table table-striped table-bordered">
                                                    <tr style="background-color: purple; color:#fff">
                                                        <th>Payment Type</th><td align="right"><b>Qty</b></td><td align="right"><b>Amount</b></td><td width="100"></td>
                                                    </tr>
                                                    <?php
                                                        foreach($payment_report as $index => $report){
                                                            if( ($index == BluelimeConfiguration::PAYMENT_METHOD::CASH) || ($index == 'TOTAL') || ($index == 'void_reciepts') || ($index == BluelimeConfiguration::PAYMENT_METHOD::CREDIT))
                                                                continue;
                                                            if(($report['amount'] == 0) && ($report['qty'] == 0))
                                                                continue;
                                                            echo '
                                                    <tr '. ($index == 'BANK TOTAL' ? 'style="background-color: #ccc;font-size: larger; font-weight:bold; border-bottom: solid 2px;"' :'') . '>
                                                        <th>'.($index!='BANK TOTAL'?'&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;&emsp13;':'').$index.'</th><td align="right">'.$report['qty'].'</td><td align="right">'.number_format($report['amount'],2).'</td><td></td>
                                                    </tr>
                                                            ';
                                                        }
                                                    ?>
                                                    <tr>
                                                        <th><i class="fa fa-money"></i> CASH </th><td align="right"><?= $payment_report['Cash']['qty'] ?></td><td align="right"><?= number_format($payment_report['Cash']['amount'],2) ?></td><td></td>
                                                    </tr>
                                                    <tr class="bg-red <?= $payment_report['void_reciepts']['qty']>0?'':'hide' ?>">
                                                        <th>X X X X <u>VOID</u> X X X X </th><td align="right"><?= $payment_report['void_reciepts']['qty'] ?></td><td align="right"><s style="text-decoration-color: #aaa;">&nbsp;&nbsp;<?= number_format($payment_report['void_reciepts']['amount'],2) ?>&nbsp;&nbsp;</s></td><td></td>
                                                    </tr>
                                                    <?php
                                                        if($payment_report['Credit']['amount'] > 0):
                                                    ?>
                                                    <tr style="font-size: larger; font-weight: bold; border-bottom: solid 2px;">
                                                        <th>SUB TOTAL</th><td align="right"><?= ($payment_report['TOTAL']['qty']-$payment_report['Credit']['qty']) ?></td><td align="right"><?= number_format($payment_report['TOTAL']['amount']-$payment_report['Credit']['amount'],2) ?></td><td></td>
                                                    </tr>
                                                    <tr>
                                                        <th><i>Credit</i></th><td align="right"><i><?= $payment_report['Credit']['qty'] ?></i></td><td align="right"><i><?= number_format($payment_report['Credit']['amount'],2) ?></i></td><td></td>
                                                    </tr>
                                                    <?php endif ?>
                                                    <tr style="font-size: large; font-weight: bold; border-top: solid 2px; border-bottom: solid 2px;">
                                                        <th>TOTAL<br><span class="badge <?= ($_GET['receipt_type']??'')=='Manual'?'hide':'' ?>">Z Report</span> </th><td align="right"><?= $payment_report['TOTAL']['qty'] ?></td><td align="right"><u><?= number_format($payment_report['TOTAL']['amount'],2) ?></u></td><td></td>
                                                    </tr>
                                                </table>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    

                                    <!--div class="col-md-4">
                                        <div class="panel panel-bd">
                                            <div class="panel-heading"><h4>Sales Report &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= $filter_date ?></span></h4></div>
                                            <div class="panel-body">
                                                <table class="table table-striped table-bordered">
                                                    <tr style="background-color:darkgreen; color:#fff">
                                                        <th>Sales Type</th><td align="right"><b>Qty</b></td><td align="right"><b>Amount</b></td>
                                                    </tr>
                                                    
                                                    <?php
                                                    /*
                                                        foreach($sales_type_report as $index => $report){
                                                            echo '
                                                    <tr>
                                                        <th>'.$index.'</th><td align="right">'.$report['qty'].'</td><td align="right">'.round($report['amount'],2).'</td>
                                                    </tr>
                                                            ';
                                                        }
                                                    */
                                                    ?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="panel panel-bd">
                                            <div class="panel-heading"><h4>Item Type Report &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?= $filter_date ?></span></h4></div>
                                            <div class="panel-body">
                                                <table class="table table-striped table-bordered">
                                                    <tr style="background-color:brown; color:#fff">
                                                        <th>Item Type</th><td align="right"><b>Qty</b></td><td align="right"><b>Amount</b></td>
                                                    </tr>
                                                    <?php
                                                        /*
                                                        foreach($item_type_report as $index => $report){
                                                            echo '
                                                    <tr>
                                                        <th>'.$index.'</th><td align="right">'.$report['qty'].'</td><td align="right">'.round($report['amount'],2).'</td>
                                                    </tr>
                                                            ';
                                                        }
                                                        */
                                                    ?>
                                                </table>
                                            </div>
                                        </div-->
                                    </div>
                            </div>
                                <div class="col-md-11">
                                    <a role="button" onclick="print_printable('report_printable')" class="btn btn-success" style="float:right"><span class="fa fa-print"></span> Print</span></a>
                                </div>
                        </div>
                            
                    </div>
                </div>
               
                <div class="col-md-6">
                    <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><label><?=$filter_date?></label></h4>
                        </div>
                    </div>
                        <div class="panel-body">
                            <?php
                            
                                $url_for_mobile_org = 'http://192.168.1.8/cashier/newdocumentupload_mobile?filter_date='.$filter_date.'&cashier_type='.$cashier_type;
                                $url_for_mobile = urlencode($url_for_mobile_org);
                            ?>
                            <div class="col-lg-7 <?= ($logged_user['role'] == BluelimeConfiguration::USER_ROLE::FINANCE)?" hidden ":""?>">
                            <label>Upload Z-Report & Deposit Slip</label>
                            
                            <?php
                    helper('form');
                    $attributes = array('class' => 'form-horizontal','role'=>'form',  'enctype' => 'multipart/form-data');
                    echo form_open('/cashier/addcashiersummary', $attributes);
                ?> 
                    <div class="col-lg-7">

                            
                        <input type="file" id="drop_zone" class="FileUpload"   accept="image/*" onchange="this.form.submit()"  name="cashier_document"  title="Click or Scan with Mobile" data-toggle="tooltip" data-html="true" >
                        <input type="hidden" value="<?=$filter_date?>" name="filter_date">
                        <input type="hidden" value="<?= $cashier_type ?>" name="cashier_type">
                        <div class="dropZoneOverlay">
                            <img src="/api/qrcode?data=<?= $url_for_mobile ?>" alt="QR Code for mobile photo" title="Scan with mobile to take pictures." />
                        </div>
                        <div hidden><input type="submit"></div>
                        
                        
                    </div>
                    
                        
                <!--button style="position: absolute; bottom: 0; right: 0;" class="btn btn-xs btn-danger <?= (empty($patient['attached_old_card']))?" hidden ":""?>" type="button" onclick="deleteallcards()">Delete All Cards</button-->
                <?php echo form_close(); ?>
                <?php
                    helper('form');
                    $attributes = array('class' => 'form-horizontal','role'=>'form', 'id' => 'xray',  'enctype' => 'multipart/form-data');
                    echo form_open('/cashier/addcashiersummary', $attributes);
                ?> 
                        </div>
                            <div class="col-lg-5 <?= ($logged_user['role'] == BluelimeConfiguration::USER_ROLE::FINANCE)?" hidden ":""?>">
                                <label>Total Z Amount</label>
                                <input type="hidden" value="<?=$filter_date?>" name="filter_date">
                                <input type="hidden" value="<?= $cashier_type ?>" name="cashier_type">
                            <input type="text" name="z_total" class="form-control" placeholder=""><br>
                            <label>Total Deposit Amount</label>
                            <input type="text" name="deposited_total" class="form-control" placeholder="">
                            <input type="submit" value="Save"class="btn btn-success" style="float: right;" >
                             
                        </div>
                        <div class="col-lg-12">
                        <hr>
                        <?php
                            $registered_date_prv = '';
                            foreach($cashier_summary as $cd): 
                                ?>
                                    <table class="table table-bordered table-striped">
                                        <tr><th>Z Report Amount</th><th>Deposit Amount</th><th>Registered By</th><th>Status</th></tr>
                                        <tr><td><?=$cd['z_total']?></td><td><?=$cd['deposited_total']?></td><td><?=$cd['cashier_name']?></td><td><?=$cd['summary_status']==1?"Approved":"Pending"?></td></tr>
                                    </table>
                                <?php
                                if($registered_date_prv == ''){
                                    echo '
                                    <fieldset class="reset">
                                        <legend class="reset"><h5>Z-Report & Deposit Slip</h5></legend>
                                        <ul class="gallery">';
                                }
                                elseif($registered_date_prv != $cd['summary_date']){
                                    echo '
                                        </ul>
                                    </fieldset>
                                    <fieldset class="reset">
                                        <legend class="reset"><h5>Z-Report & Deposit Slip</h5></legend>
                                        <ul class="gallery">';
                                }

                                $manual_card_image = explode(',',$cd['cashier_document']);
                                $is_first = true;
                                foreach($manual_card_image as $image_name):
                                    echo '<a href="/image/cashierdocument?image='.$image_name.'" data-image_name="'.$image_name.'"><li><img src="/image/cashierdocument?image='.$image_name.'&thumbnail=1" style="width:150px" ></li></a>';
                                endforeach;
                        ?>
                            
                            
                               
                        <?php 
                            $registered_date_prv = $cd['summary_date'];
                            endforeach;
                            if($registered_date_prv != '')
                                echo '
                                    </ul>
                                </fieldset>';
                        ?>
                        
                    </div>
                <input type="button" value="Approve" onclick="approve_sales_summary(<?=$cd['id']??-1?>)" class="btn btn-success  <?= ($logged_user['role'] == BluelimeConfiguration::USER_ROLE::FINANCE) && @$cd['summary_status']==0?"":" hidden "?>" style="float: right;" >
                        </div>
                    </div>
                </div>
                <?php
                    echo form_close();
                ?>
            </div>
        </div>
            
        </section>
    </div>
    
<?= view('Include/_footer') ?>
<script type="text/javascript">


    function approve_sales_summary(sid) {
        if(confirm('Are you sure you want to Approve?')){
            $.ajax({
                url: '/inventory/finance/approvesalessummary',
                method: 'POST',
                data: {sid:sid},
                async: true,
                success:function(result){
                    toastr['success']('Sales Summary Approved');
                    setTimeout(function () { 
                        location.reload(true); 
                    }, 3000);
                    
                }
            });
        }
    }
    function refreshPage() {
        location.reload();
    }
    
    function preview() {
        frame.src = URL.createObjectURL(event.target.files[0]);
    }
    function clearImage() {
        document.getElementById('formFile').value = null;
        frame.src = "";
    }

    function delete_image_via_lightbox(plugin){
        let image_name = $(plugin.current).data('image_name');
        if(confirm('Are you sure you want to delete this picture?')){
            $.ajax({
                url: '/cashier/delete_cashier_document',
                method: 'POST',
                data: {image_name:image_name, filter_date:<?="'$filter_date'" ?>},
                async: false,
                success:function(result){
                    plugin.current.remove();
                    plugin.items.splice(plugin.getCurrentIndex(),1);
                    plugin.close();
                }
            });
        }
    }

    var delete_button_lightbox = {
        "background-image" : "/img/delete_button_01.png",
        callback: delete_image_via_lightbox
    };
  
  $(function() {
    $('.gallery a').lightbox({custom_button1:delete_button_lightbox}); 
  });


</script>


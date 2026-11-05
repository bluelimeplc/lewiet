<?php
//BluelimeSystem::show_and_die($grv_main['scanned_documents']);
?>
<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Finance/_left_sidebar_finance') ?>
<?= view('Include/_left_sidebar_bottom') ?>
<link rel="stylesheet" href="/Image-gallery/assets/css/jquery.lightbox.css">
                        <link rel="stylesheet" href="/Image-gallery/assets/css/demo.css">
                        <script src="/Image-gallery/assets/js/jquery.lightbox.js"></script>
    <style>
                                
                        @keyframes jump {
                          0%{
                            font-size: 25px;
                            color: orange;
                          }
                          50%{
                            font-size: 45px;
                            color: red;
                          }
                          100%{
                            font-size: 25px;
                            color: orange;
                          }
                        }
                            .jump
                            {
                                 animation: jump 1.5s ease 0s normal ;
                                color: orange;
                                 /*animation: jump 1.5s ease 0s infinite normal ;*/
                            }
                        
                            .dropZoneOverlay, .FileUpload {
                                    width: 43px;
                                    height: 30px;
                                }
                        
                            .dropZoneOverlay {
                                font-family: cursive;
                                color: #7066fb;
                                position: absolute;
                                top: -5px;
                                left: 4px;
                                text-align: center;
                                cursor: pointer;
                            }
                        
                            .FileUpload {
                                opacity: 0;
                                position: relative;
                                z-index: 1;
                                cursor: pointer;
                            }
                        </style>    
                        <style>
* {
  box-sizing: border-box;
}

.column {
  float: left;
  width: 33.33%;
  padding: 5px;
}

/* Clearfix (clear floats) */
.row::after {
  content: "";
  clear: both;
  display: table;
}
</style>
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small>Goods Receiving</small>
            <ol class="breadcrumb">
                <li class="active"><a onclick="print_printable_document()">Print</a></li>
            </ol>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
    <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-body">
                        <div style="float: left;">
                        <?php
                        
                            if($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST)
                                echo '<a href="/inventory/finance/receive_grv?grv_id='.$grv_main['id'].'" class="btn btn-success" title=""><i class="fa fa-check-circle-o"></i> Receive</a> ';
                            else
                                echo '<a disabled class="btn btn-success" title=""><i class="fa fa-check-circle-o"></i> Receive</a> ';
                            
                            if($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND)
                                echo '<a href="/inventory/finance/return_grv?grv_id='.$grv_main['id'].'" class="btn btn-warning" title=""><i class="fa fa-reply"></i> Return</a> ';
                            else
                                echo '<a disabled class="btn btn-warning" title=""><i class="fa fa-reply"></i> Return</a> ';

                            if(($grv_main['supplier_detail']['peachtree_id'] == "")||($grv_main['supplier_detail']['payable_account'] == ""))
                                echo '<a onclick="show_supplier_id_modal()" class="btn btn-danger" title=""><i class="fa fa-apple"></i> Eport CSV (Peachtree)</a> ';
                            elseif($grv_main['status'] >= BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND)
                                echo '<a target="_blank" href="/inventory/finance/export_grv?grv_id='.$grv_main['id'].'" class="btn btn-danger" title=""><i class="fa fa-apple"></i> Eport CSV (Peachtree)</a> ';
                            else
                                echo '<a disabled class="btn btn-danger" title=""><i class="fa fa-apple"></i> Eport CSV (Peachtree)</a> ';
                            
                            echo '<a onclick="show_supplier_id_modal()" class="btn btn-info" title="Peachtree Supplier ID"><i class="fa fa-gear"></i> '.(($grv_main['supplier_detail']['peachtree_id'] == "")||($grv_main['supplier_detail']['payable_account'] == "")?'Peachtree supplier id':'').'</a> ';
                            echo '<a onclick="show_grv_edit_modal()" class="btn btn-success"  style="background-color: pink; color:black;" title="Edit">Edit</a> ';
                            echo '<a onclick="show_cpv_modal()" class="btn btn-success"  style="background-color: chocolate;" title="Add CPV">CPV# '.$grv_main['cpv'].'</a> ';


                        ?>
                        </div>
                        <div style="float: left;">
                            <?php
                                        helper('form');
                                        $attributes = array('class' => 'form-horizontal','role'=>'form',  'enctype' => 'multipart/form-data');
                                        echo form_open('inventory/finance/uploaddocument', $attributes);
                                    ?> 
                                        <div class="col-sm-2">
                                            <input  type="file" id="drop_zone" class="FileUpload"   accept="image/*" onchange="this.form.submit()"  name="picture"  title="Upload Document" data-toggle="tooltip" data-html="true" >
                                            <input type="hidden" value="<?=$_GET['grv_id']?>" name="grv_id">
                                            <div class="dropZoneOverlay">
                                                <img src="/img/green-upload.png" width="44px" title="Upload Document" />
                                            
                                            </div>
                                            <div hidden><input type="submit"></div>
                                          </div>  
                                        
                                        <?php $result = (explode(',', $grv_main['scanned_documents'])); 
                                            //BluelimeSystem::show_and_die(count($result));
                                            ?>
                                            
                                            <div class="col-sm-8 manual_card  <?= (empty($grv_main['scanned_documents']))?" hidden ":""?>" style="margin-left: 30px;">
                                                

                                                
                                                <?php
                                                    $manual_card_image = explode(',',$grv_main['scanned_documents']);
                                                    
                                                    
                                                    $is_first = true;
                                                    foreach($manual_card_image as $image_name):
                                                        echo '<a href="/image/cashierdocument?image='.$image_name.'" data-image_name="'.$image_name.'"><div class="column" style="padding-right:5px;"><img src="/image/cashierdocument?image='.$image_name.'&thumbnail=1" style="height:60px" ></div></a>';
                                                    endforeach;
                                                ?>
                                                
                                            
                                            </div>
                                        
                                    <!--button style="position: absolute; bottom: 0; right: 0;" class="btn btn-xs btn-danger <?= (empty($grv_main['scanned_documents']))?" hidden ":""?>" type="button" onclick="deleteallcards()">Delete All Cards</button-->
                                    <?php echo form_close(); ?>
                            
                        </div>
                        
                        <div style="float: right;">
                            <table>
                                <tr style="text-align: center;">

                                    <td rowspan="2">Status&nbsp;</td>
                                    <td><?= ($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::PENDING)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND)?'<i class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                    <td><?= ($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::EXPORTED_FOR_PEACHTREE)?'<i '.($logged_user_data['role']==BluelimeConfiguration::USER_ROLE::FINANCE ? ' id="revert_pointer" title="Revert to Finance state."':'').' class="fa fa-arrow-circle-down"></i>':'' ?></td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-qty">Pending</span></td>
                                    <td>&nbsp;<span class="badge">Approved</span></td>
                                    <td>&nbsp;<span class="badge badge-qty-warning">Finance</span></td>
                                    <td>&nbsp;<span class="badge badge-qty-red">Posted</span></td>
                                    
                                </tr>
                            </table>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
        <div>
            <!-- Maintain supplier id modal -->
            <div class="modal fade" id="supplier_id_modal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header dental-bg-primary">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Maintain Peachtree Supplier id</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped">
                                <tr>
                                    <td>Company Name</td>
                                    <td><?= $grv_main['supplier_detail']['company_name'] ?></td>
                                </tr>
                                <tr>
                                    <td>Peachtree ID</td>
                                    <td><input type="text" maxlength="20" class="form-control" id="peachtree_id" value="<?= $grv_main['supplier_detail']['peachtree_id'] ?>"></td>
                                </tr>
                                <tr>
                                    <td>Accounts Payable Account</td>
                                    <td><input type="text" maxlength="20" class="form-control" id="payable_account" value="<?= $grv_main['supplier_detail']['payable_account'] ?>"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success" onclick="update_supplier_id()">
                                Update
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <!-- CPV modal -->
            <div class="modal fade" id="cpv_modal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header dental-bg-primary">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">CPV Update</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped">
                                <tr>
                                    <td>CPV Number</td>
                                    <td><input type="text" maxlength="20" class="form-control" id="cpv_number" value="<?= $grv_main['cpv'] ?>"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success" onclick="update_cpv()">
                                Update
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRV edit modal -->
            <div class="modal fade" id="grv_edit_modal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header dental-bg-primary">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">GRV Edit</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped">
                                <tr>
                                    <td>FS Number:</td>
                                    <?php
                                        $fs_number = str_replace(['FS','M'],'',$grv_main['fs_no']);
                                    ?>
                                    <td><input type="text" maxlength="8" class="form-control" id="fs_number" value="<?= $fs_number ?>"></td>
                                </tr>
                                <tr>
                                    <td>Machine N<u>o</u>:</td>
                                    <td>
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <input type="text" name="machine_no" id="machine_no" class="form-control" maxlength="10" minlength="10" value="<?= $grv_main['machine_no'] ?? '' ?>" title="Machine Number must be 10 characters long if not manual. Eg. ABD1234567">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="checkbox" id="is_manual_receipt" name="is_manual_receipt" onchange="is_manual_change()"> <label for="is_manual_receipt" class="control-label" style="text-align: left;">Is Manual</label>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Receipt Date:</td>
                                    <?php
                                        $receipt_date_ddmmyy = explode('/', $grv_main['receipt_date']);
                                        $receipt_date_dd = $receipt_date_ddmmyy[0];
                                        $receipt_date_mm = $receipt_date_ddmmyy[1];
                                        $receipt_date_yy = $receipt_date_ddmmyy[2];
                                    ?>
                                    <td>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="col-md-3" style="padding: 4px;">
                                                        <input type="text" id="receipt_date_date" class="form-control" value="<?= $receipt_date_dd ?? '' ?>" onkeypress="return /[0-9]/i.test(event.key)" maxlength="2">
                                                        <label for="receipt_date_date" class="control-label p-0">&nbsp;&nbsp;&nbsp;&nbsp;DD</label>
                                                    </div>
                                                    <div class="col-md-3" style="padding: 4px;">
                                                        <input type="text" id="receipt_date_month" class="form-control" value="<?= $receipt_date_mm ?? '' ?>" onkeypress="return /[0-9]/i.test(event.key)" maxlength="2">
                                                        <label for="receipt_date_month" class="control-label p-0">&nbsp;&nbsp;&nbsp;&nbsp;MM</label>
                                                    </div>
                                                    <div class="col-md-6" style="padding: 4px;">
                                                        <input type="text" id="receipt_date_year" class="form-control" value="<?= $receipt_date_yy ?? '' ?>" onkeypress="return /[0-9]/i.test(event.key)" maxlength="4">
                                                        <label for="receipt_date_year" class="control-label p-0">&nbsp;&nbsp;&nbsp;&nbsp;YYYY</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <br>
                                                    <input type="checkbox" id="is_ethiopian_calander" name="is_ethiopian_calander" title="Is Ethiopian Calander"> <label for="is_ethiopian_calander" class="control-label" style="text-align: left;" title="Is Ethiopian Calander">Eth Calander</label>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success" onclick="update_grv()">
                                Update
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?= view('Include/_print_grv'); ?>
        <script>
            <?php 
                if($grv_main['status'] < BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND)
                    echo '$(document).ready(function(){$(\'.blurable\').css(\'filter\',\'blur(2px)\');});';
            ?>
        </script>
    </section>
</div>
<script>
    var machine_no_val = '';
    function is_manual_change()
    {
        let is_manual = $('#is_manual_receipt');
        let machine_no = $('#machine_no');
        let btn_fetch_last_mrc_number = $('#btn_fetch_last_mrc_number');
        if(is_manual.is(':checked')==true){
            machine_no_val = machine_no.val();
            machine_no.val('');
            machine_no.attr('readonly', true);
            btn_fetch_last_mrc_number.attr('disabled', true);
            btn_fetch_last_mrc_number.attr('onclick', '');
        }
        else{
            machine_no.val(machine_no_val);
            machine_no.removeAttr('readonly');
            btn_fetch_last_mrc_number.removeAttr('disabled');
            btn_fetch_last_mrc_number.attr('onclick', 'fetch_last_mrc_number()');
        }
    }

    function show_supplier_id_modal(){
        $('#supplier_id_modal').modal('show');
        setTimeout("$('#peachtree_id').focus()",500);
    }
    function show_cpv_modal(){
        $('#cpv_modal').modal('show');
        setTimeout("$('#cpv_number').focus()",500);
    }

    function show_grv_edit_modal(){
        $('#grv_edit_modal').modal('show');
    }

    function update_supplier_id(){
        let peachtree_id = $('#peachtree_id').val().trim();
        let payable_account = $('#payable_account').val().trim();
        
        $.ajax({
            url: '/inventory/finance/update_peachtree_supplier_id',
            method: 'POST',
            data:{id:"<?= $grv_main['supplier_detail']['id'] ?>", peachtree_id: peachtree_id, payable_account:payable_account},
            dataType: 'json',
            success:function(result){
                if(result.status == 'success')
                    location.reload();
            },
        });
    }
    function update_cpv(){
        let cpv_number = $('#cpv_number').val().trim();
        
        $.ajax({
            url: '/inventory/finance/update_cpv',
            method: 'POST',
            data:{id:"<?= $grv_main['id'] ?>", cpv_number: cpv_number},
            dataType: 'json',
            success:function(result){
                if(result.status == 'success')
                    location.reload();
            },
        });
    }

    function update_grv(){
        let fs_number = $('#fs_number').val().trim();
        let machine_no = $('#machine_no').val().trim();
        let receipt_date_date = $('#receipt_date_date').val().trim();
        let receipt_date_month = $('#receipt_date_month').val().trim();
        let receipt_date_year = $('#receipt_date_year').val().trim();
        let is_manual_receipt = $('#is_manual_receipt').is(':checked');
        let is_ethiopian_calander = $('#is_ethiopian_calander').is(':checked');
        
        $.ajax({
            url: '/inventory/finance/update_grv',
            method: 'POST',
            data:{id:"<?= $grv_main['id'] ?>", fs_number: fs_number, machine_no: machine_no, receipt_date_date: receipt_date_date, receipt_date_month: receipt_date_month, receipt_date_year: receipt_date_year, is_manual_receipt:is_manual_receipt?1:0, is_ethiopian_calander:is_ethiopian_calander?1:0},
            dataType: 'json',
            success:function(result){
                if(result.status == 'success')
                    location.reload();
                else
                    toastr.error(result.message);
            },
        });
    }

    $('#revert_pointer').on("click", function() {
            if(confirm("Are you sure you want to revert this GRV to Finance?\nYou must unpost the data from peachtree.")){
                $.ajax({
                    url: '/inventory/finance/revert_grv',
                    method: 'POST',
                    data:{grv_id:"<?= $grv_main['id'] ?>"},
                    dataType: 'json',
                    async:false,
                    success:function(result){
                        if(result.status == 'success')
                            location.reload();
                    },
                });
            }
    });

    $('#revert_pointer').on("mouseover", function() {
        $(this).toggleClass("fa-arrow-circle-left fa-arrow-circle-down");
        $(this).css("cursor", "pointer");
    });

    $('#revert_pointer').on("mouseout", function() {
        $(this).toggleClass("fa-arrow-circle-left fa-arrow-circle-down");
    });

    function preview() {
        frame.src = URL.createObjectURL(event.target.files[0]);
    }
    function clearImage() {
        document.getElementById('formFile').value = null;
        document.getElementById('comment').value = "";
        frame.src = "";
    }

    function delete_image_via_lightbox(plugin){
        let image_name = $(plugin.current).data('image_name');
        if(confirm('Are you sure you want to delete this picture?')){
            $.ajax({
                url: '/inventory/finance/delete_document',
                method: 'POST',
                data: {image_name:image_name, grv_id:<?= $grv_main['id'] ?>},
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
        $('.manual_card a').lightbox({custom_button1:delete_button_lightbox}); 
    });

    function is_manual_receipt_on(){
        $('#is_manual_receipt').prop( "checked", true );
        is_manual_change();
    }

    
    <?php
        if($grv_main['is_manual_receipt'] ?? 0){
                echo '
    $( document ).ready(function() {
        is_manual_receipt_on();
    });';
        }
    ?>

    <?php
        if($grv_main['is_ethiopian_calander'] ?? 0){
                echo '
    $( document ).ready(function() {
        $("#is_ethiopian_calander").prop( "checked", true );
    });';
        }
    ?>

</script>
<?= view('Include/_footer') ?>
<?php
    $extra_info = $this->data['extra_info'];
    $order_options = $this->data['order_options'];

    $id = $this->data['id'];
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>

        <div class="header-title">
            <h1>Reception</h1>
            <small>Invoice</small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/reception/pendingbills">Pending Bills</a></li>
            </ol>
        </div>
    </section>
    <!-- Main content -->
    <section class="content">
	    <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd ">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Order Options</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="form-group row">
                                    <label  class="col-sm-3 col-form-label">Company Name</label>
                                    <div class="col-sm-7">
                                        <input class="form-control" size="50" readonly value="<?php echo $order_options['company_name'] ?>" type="text">
                                    </div>

                                    <label  class="col-sm-3 col-form-label">Card No</label>
                                    <div class="col-sm-7">
                                        <input class="form-control" size="50" readonly value="<?php echo "MRN-".sprintf('%05d', $order_options['patient_id']); ?>" type="text">
                                    </div>
                                
                                    <label  class="col-sm-3 col-form-label">Patient Name</label>
                                    <div class="col-sm-7">
                                        <input class="form-control" size="50" readonly value="<?php echo $order_options['patient_name'] ?>" type="text">
                                    </div>
                                </div>
                            </div>
                           
                        	<div class="col-md-5">
                                <div class="form-group row">
                                    <label  class="col-sm-4 col-form-label">Date</label>
                                    <div class="col-sm-6">
                                           <input class="form-control" size="50" readonly  value="<?php echo $order_options['registered_date'] ?>" type="text">
                                    </div>
                                    <label  class="col-sm-4 col-form-label">Mobile</label>
                                    <div class="col-sm-6">
                                           <input class="form-control" size="50" readonly  value="<?php echo $order_options['mobile'] ?>" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="panel panel-bd ">
                    <div class="panel-body">
                        <a class="btn btn-info fa fa-telegram" role="button" onclick="show_sendToTelegramModal()"></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="row">
                    <?php
                        $do_not_open_visit = '';
                        $row_count = -1;
                        if(!empty($extra_info)):
                            if(($extra_info['transferForNextYear']) || (trim($extra_info['remark']) != '')):
                                $row_count = 0;

                                if($extra_info['transferForNextYear'])
                                    $do_not_open_visit = '&do_not_open_visit=1';
                    ?>
                                <div class="col-sm-6">
                                    <div class="panel panel-bd panel-success">
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                <h4>Extra info</h4>
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <?php
                                                if($extra_info['transferForNextYear'])
                                                    echo '<div><i class="fa fa-check-square-o"></i> Transfer for next year</div>';
                                            ?>
                                            <br>
                                            <b>Remark</b>:<br>
                                            <?= $extra_info['remark'] ?>
                                            
                                        </div>
                                    </div>
                                </div>
                    <?php
                            endif;
                        endif;
                    ?>
                    <?php
                        $options = $order_options['options'];
                        if(!empty($options)):
                            
                            foreach($options as $index => $option):
                                $row_count++;
                                if($row_count % 2 == 0){
                                    echo '</div>';
                                    echo '<div class="row">';
                                }
                                
                                $option_title = str_replace('_', ' ', ucfirst($index));
                                $color_indicator = 'info';

                                if($index == 'option_1')
                                    $color_indicator = 'success text-white';
                                elseif($index == 'option_2')
                                    $color_indicator = 'warning';


                    ?>
                                <div class="col-sm-6">
                                    <div class="panel panel-bd">
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                <a href="<?php echo base_url() . "hospital/reception/invoiceorderoption?id=$id&option_index=$index$do_not_open_visit"; ?>" role="button" class="btn btn-<?=$color_indicator?>" style="float: right;"><b>Process <?= $option_title ?></b></a>
                                                <h4><?= $option_title ?></h4>
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr class="bg-<?=$color_indicator?>" style="color:#223F4A !important;"><th>Service Name</th><th>Qty</th><th>Price</th><th>Total</th></tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                $services = $option['services'];
                                                                foreach($services as $single_option):
                                                                    $service_name = $single_option['service_name'];
                                                                    
                                                                    $quantity = $single_option['quantity'];
                                                                    $price = $single_option['price'];
                                                                    $total = $single_option['total'];
                                                                    $discount = $single_option['discount'];
                                                            ?>
                                                                    <tr>
                                                                        <td><?= $service_name; ?></td>
                                                                        <td><?= $quantity; ?></td>
                                                                        <td><?= $price; ?></td>
                                                                        <td><?= $total; ?></td>
                                                                    </tr>
                                                            <?php
                                                                endforeach;
                                                            ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="3" align="right"><b>Grand Total</b></td>
                                                                <td><b><?= $option['grand_total'];?></b></td>
                                                            </tr>
                                                            <?php
                                                                if($option['total_discount'] > 0):
                                                            ?>
                                                                    <tr>
                                                                        <td colspan="3" align="right">Discount <?= $option['discount_percent'] ?>%</td>
                                                                        <td><?= $option['total_discount'] ?></td>
                                                                    </tr>
                                                            <?php
                                                                endif;
                                                            ?>
                                                            <?php
                                                                if($option['other_discount'] > 0):
                                                            ?>
                                                                    <tr>
                                                                        <td colspan="3" align="right">Other discount</td>
                                                                        <td><?= $option['other_discount'] ?></td>
                                                                    </tr>
                                                            <?php
                                                                endif;
                                                            ?>
                                                            <tr>
                                                                <td colspan="3" align="right"><b>Total</b></td>
                                                                <td><b><?= $option['total'];?></b></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    <?php
                            endforeach;
                            if($row_count > 1)
                                echo '</div>';
                        endif;
                    ?>
                </div>
            </div>
        </div>
    </section>      
</div>
<!--   Modal -->
<div>
    <div class="container">
        <!-- Trigger the modal with a button -->
        <!-- New General modal -->
        <div class="modal fade" id="sendToTelegramModal" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><span>Send to telegram</span></h4>
                    </div>
                    <div class="modal-body">
                        <label class="control-label" for="addNewGeneral_name">Mobile</label>
                        <input id="txt_sendToTelegramMobile" type="text" class="form-control" value="<?= $order_options['mobile'];?>">
                        <div id="sendToTelegramModal_Flash" class="alert alert-danger hide" role="alert">
                            <span></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="btn_sendToTelegramModal_Send" type="button" class="btn btn-success" onclick="send_to_telegram();">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function show_sendToTelegramModal(){
        $('#sendToTelegramModal').modal('show');
    }

    function send_to_telegram(){
        let mobile = $('#txt_sendToTelegramMobile').val();
        let message = "Order option 1: 200 ETB.";
        let url = "https://dental.bluelimeplc.com/hospital/store_communication";
        $.ajax({
                method: 'post',
                url: url,
                data: {"mobile":mobile, "message":message},
                success: function(result){
                    try{
                        let r = JSON.parse(result);
                        if(r.status == "success")
                            close_modal('sendToTelegramModal');
                        else
                            dental_show_flash('sendToTelegramModal_Flash', r.message);
                    }
                    catch (e){
                        dental_show_flash('sendToTelegramModal_Flash', e.message);
                    }
                }
            });
        }
    
</script>
<?php
    $treatment_plans = $this->data['treatment_plans'];
    $remark = $this->data['remark'];
    $follow_up = $this->data['follow_up'];
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>

        <div class="header-title">
            <h1>Reception</h1>
            <small>Treatment Plans Choice</small>
        </div>
    </section>
    <section class="content-details">
        <div class="row">
            <div class="col-lg-12">
                <div class="col-sm-12">
                    <div class="panel panel-bd ">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h4>Patient Detail</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-7">
                                    <div class="form-group row">
                                        <label  class="col-sm-3 col-form-label">Company Name</label>
                                        <div class="col-sm-7">
                                            <input class="form-control" size="50" readonly value="<?php echo $follow_up['company_name'] ?>" type="text">
                                        </div>

                                        <label  class="col-sm-3 col-form-label">Card No</label>
                                        <div class="col-sm-7">
                                            <input class="form-control" size="50" readonly value="<?php echo "MRN-".sprintf('%05d', $follow_up['patient_id']); ?>" type="text">
                                        </div>
                                    
                                        <label  class="col-sm-3 col-form-label">Patient Name</label>
                                        <div class="col-sm-7">
                                            <input class="form-control" size="50" readonly value="<?php echo $follow_up['patient_name'] ?>" type="text">
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-md-5">
                                    <div class="form-group row">
                                        <label  class="col-sm-4 col-form-label">Date</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" size="50" readonly  value="<?php echo $follow_up['date_created'] ?>" type="text">
                                        </div>
                                        <label  class="col-sm-4 col-form-label">Mobile</label>
                                        <div class="col-sm-6">
                                            <input class="form-control" size="50" readonly  value="<?php echo $follow_up['mobile'] ?>" type="text">
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
                            <a class="btn btn-info fa fa-print" role="button" onclick="printContent()"></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div  class="panel panel-bd">
                        <?= view('Dental/Shared/_dental_chart'); ?>
                        <script>
                            selectedTeethTemp = <?= \BluelimeSystem::convert_array_to_string($remark['selected_teeth'] ?? []); ?>;
                            selectedTeethTemp.forEach((item, index)=>{
                                selectToothByNumber(item);
                            });
                            $('polygon').removeAttr('onclick');
                            $('path').removeAttr('onclick');
                            $('#Spots').removeAttr('id');
                            $('.SpotsSelected').removeAttr('class');
                            
                        </script>
                    </div>
                </div>
                <div class="col-lg-9" id="plans">
                    
                    <?php
                        $selected_plan = $remark['selected_plan'] ?? '';
                        $plan_cnt = 1;
                        foreach($treatment_plans as $plan_index => $plan){
                            if(!empty($plan)){
                                $total = $plan['total'];
                                $total_discount = $plan['total_discount'];
                                $grand_total = $plan['grand_total'];
                                $has_discount = $plan['has_discount'];

                                $colspan = 3;
                                if($has_discount)
                                    $colspan = 4;
                                unset($plan['total']);
                                unset($plan['grand_total']);
                                unset($plan['total_discount']);
                                unset($plan['has_discount']);
                                echo '
                                    <div class="col-lg-12">
                                        <div  class="panel panel-bd">
                                            <div class="panel-heading">
                                                <div class="panel-title">
                                                    <h4>Plan '. $plan_cnt . (($selected_plan == $plan_index) ? ' <i class="fa fa-check-circle"> Selected</i>' : '') .'</h4>
                                                </div>
                                            </div>
                                            <div class="panel-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr class="bg-success">
                                                            <th>Service Name</th>
                                                            <th>Qty</th>
                                                            <th>Price</th>';
                                                    echo ($has_discount) ? '<th>Discount %</th>':'';
                                                    echo '<th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ';
                                                        
                                                        foreach($plan as $service){
                                                            echo '
                                                        <tr>
                                                            <td>'. $service['service_name'] .'</td><td>'. $service['quantity'] .'</td><td>'. $service['price'] .'</td>
                                                            ';
                                                            echo ($has_discount) ? '<td>' . $service['discount'] . '</td>' : '';
                                                            echo '
                                                            <td>'. $service['total'] .'</td>
                                                        </tr>
                                                    </tbody>
                                                        ';
                                                        }
                                echo '
                                                    <tfoot>
                                                        
                                                    ';
                                if($has_discount){
                                echo '  
                                                        <tr>
                                                            <td colspan="' . $colspan . '" class="text-right"><b>Total Discount</b></td><td><b>-' . $total_discount . '</b></td>
                                                        </tr>            
                                ';
                                }
                                echo '
                                                        <tr>
                                                            <td colspan="' . $colspan . '" class="text-right"><b>Total</b></td><td><b>' . $total . '</b></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                                <div class="text-right" id="noprint'.$plan_cnt.'">
                                                    <a role="button"  class="btn btn-primary" href="/hospital/reception/choosetreatmentplan?follow_up_id='.$follow_up['id'].'&plan_index=plan_'.$plan_cnt.'">Select Plan '.$plan_cnt.'</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ';
                                $plan_cnt++;
                            }
                        }
                        
                    ?>
                </div>
            </div>
        </div>
        <div style="height: 0px; overflow: hidden;">
            <div class="col-lg-5" id="for_telegram">
                <?php
                    $plan_cnt = 1;
                    foreach($treatment_plans as $plan_index => $plan){
                        if(!empty($plan)){
                            $total = $plan['total'];
                            $total_discount = $plan['total_discount'];
                            $grand_total = $plan['grand_total'];
                            $has_discount = $plan['has_discount'];

                            $colspan = 3;
                            if($has_discount)
                                $colspan = 4;
                            unset($plan['total']);
                            unset($plan['grand_total']);
                            unset($plan['total_discount']);
                            unset($plan['has_discount']);
                            echo '
                                <div class="col-lg-12">
                                    <div  class="panel panel-bd">
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                <h4>Plan '. $plan_cnt . '</h4>
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr class="bg-success">
                                                        <th>Service Name</th>
                                                        <th>Qty</th>
                                                        <th>Price</th>';
                                                echo ($has_discount) ? '<th>Dis count %</th>':'';
                                                echo '<th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ';
                                                    
                                                    foreach($plan as $service){
                                                        echo '
                                                    <tr>
                                                        <td>'. $service['service_name'] .'</td><td>'. $service['quantity'] .'</td><td>'. $service['price'] .'</td>
                                                        ';
                                                        echo ($has_discount) ? '<td>' . $service['discount'] . '</td>' : '';
                                                        echo '
                                                        <td>'. $service['total'] .'</td>
                                                    </tr>
                                                </tbody>
                                                    ';
                                                    }
                            echo '
                                                <tfoot>
                                                ';
                            if($has_discount){
                            echo '  
                                                    <tr>
                                                        <td colspan="' . $colspan . '" class="text-right"><b>Total Discount</b></td><td><b>-' . $total_discount . '</b></td>
                                                    </tr>            
                            ';
                            }
                            echo '
                                                    <tr>
                                                        <td colspan="' . $colspan . '" class="text-right"><b>Total</b></td><td><b>' . $total . '</b></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            ';
                            $plan_cnt++;
                        }
                    }
                    
                ?>
            </div>
        </div>
    </section>
</div>

<div>
    <div class="container">
        <!-- Trigger the modal with a button -->
        <!-- New General modal -->
        <div class="modal fade" id="sendToTelegramModal" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><span>Send to telegram</span></h4>
                    </div>
                    <div class="modal-body">
                        <label class="control-label" for="addNewGeneral_name">Mobile</label>
                        <input id="txt_sendToTelegramMobile" type="text" class="form-control" value="<?= $follow_up['mobile'];?>">
                        <div id="sendToTelegramModal_Flash" class="alert alert-danger hide" role="alert">
                            <span></span>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button id="btn_sendToTelegramModal_Send" type="button" class="btn btn-success" onclick="send_to_telegram();">Send</button>
                    </div>
                    <div id="previewImage"></div>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="print_head" hidden>
<div>
<span style="font-size:11px; float:right; border-left: 1px solid black; padding-left: 12px;">
<table><tr><td>Mob.</td><td> </td></tr><tr><td></td><td> </td></tr><tr><td>Tel.</td><td> </td></tr><tr><td colspan="2" style="font-size:9px;"> </td></tr>
</table>
</span>
<img src="/img/logo-black.png" style="width:120px;">
</div>
<hr style="border-width: 2px;margin-top: 0px; margin-bottom: -20px;">
<table style="font-size:13px;margin-top: 0px; margin-bottom: -15px;">
    <tr>
        <td><b>Patient Name: </b></td>
        <td><?php echo $follow_up['patient_name'] ?></td>
    </tr>
    <tr>
        <td><b>Card No: </b></td><td><?php echo "MRN-".sprintf('%05d', $follow_up['patient_id']); ?></td>
    </tr>
    <tr>
        <td><b>Mobile: </b></td><td><?php echo $follow_up['mobile'] ?></td>
    </tr>
</table>
<hr style="margin-top: 0px; margin-bottom: -50px;">       
    <div id="treatment_plan_table"  style="font-size:13px;margin-top: 0px; margin-bottom: -50px;"></div>
        <div style="margin-top: -40px; margin-bottom: -20px; text-align: center;"><img src="/img/telegram_qr02.png" width="100" style="margin-top: 0px; margin-bottom: 0px;"></div>
        <div style="margin-top: -20px; margin-bottom: -20px;font-size:10px; text-align: center;">Developed by www.bluelimeplc.com </div>
</div>

<script src="/js/html2canvas.js"></script>

<script>
    function show_sendToTelegramModal(){
        $('#sendToTelegramModal').modal('show');
    }

    //var element = $("#plans"); // global variable
    var getCanvas; // global variable
    $('document').ready(function(){
        html2canvas($("#for_telegram")[0]).then((canvas) => {
                $("#previewImage").html(canvas);
                getCanvas = canvas;
            });
        });

    /*$
    ("#whatsapp").on('click', function () {
    var imageData = getCanvas.toDataURL("image/png");
    // Now browser starts downloading it instead of just showing it
    var newData = imageData.replace(/^data:image\/png/, "data:application/octet-stream");
    $("#whatsapp").attr("download", "image.png").attr("href", newData);
    });
    */



    <?php
        function count_service_name_line($service_name, $has_discount){
            
            $space_count = 17;
            if($has_discount)
                $space_count = 13;

            $service_name_len = strlen($service_name);
            $line_count = intval($service_name_len / $space_count);
            if(($service_name_len % $space_count) > 0)
                $line_count++;

            return $line_count;
        }

        $message_text = '```\n';
        foreach($treatment_plans as $plan_index => $plan) {
            if(!empty($plan)){
                $total = $plan['total'];
                $total_discount = $plan['total_discount'];
                $grand_total = $plan['grand_total'];
                $has_discount = $plan['has_discount'];
                unset($plan['total']);
                unset($plan['grand_total']);
                unset($plan['total_discount']);
                unset($plan['has_discount']);

                if($has_discount)
                    $message_text .=  $plan_index .'\n ---------------------------------\n| Service Name  | Qty | D%| Price |\n|---------------------------------|\n';
                else
                    $message_text .=  $plan_index .'\n ---------------------------------\n| Service Name      | Qty | Price |\n|---------------------------------|\n';
                
                $space_count = 17;
                if($has_discount)
                    $space_count = 13;

                foreach($plan as $service){
                    $line_count = count_service_name_line($service['service_name'], $has_discount);
                    
                    for($i = 0; $i < $line_count; $i++){
                        $message_text .= '| ' . str_pad(substr($service['service_name'], $i*$space_count,$space_count),$space_count," ",STR_PAD_RIGHT) . ' ';
                        if($i == 0){
                            $message_text .= '| ' . str_pad($service['quantity'],3," ",STR_PAD_LEFT) . ' ';
                            
                            if($has_discount)
                                $message_text .= '| ' . str_pad($service['discount'],2," ",STR_PAD_LEFT) . '';
                            
                            $message_text .= '| ' . str_pad($service['total'],5," ",STR_PAD_LEFT) . ' |\n';   
                        }
                        else{
                            $message_text .= '| ' . str_pad(' ',3," ",STR_PAD_LEFT) . ' ';
                            
                            if($has_discount)
                                $message_text .= '| ' . str_pad(' ',2," ",STR_PAD_LEFT) . '';
                            
                            $message_text .= '| ' . str_pad(' ',5," ",STR_PAD_LEFT) . ' |\n';   
                        }
                    }
                    $message_text .= '|---------------------------------|\n';
                }

                $message_text .= '| TOTAL ';
                $message_text .= str_pad($total,25," ",STR_PAD_LEFT) . ' |\n';
                $message_text .= ' --------------------------------- \n\n\n';
            }
        }
        $message_text .= '\n```';
    ?>

    function send_to_telegram(){
        let message = "<?= $message_text ?>";

        let mobile = $('#txt_sendToTelegramMobile').val();
        let url = "https://api.bluelimeplc.com/rank/save_message";
        let post_data = {
            "phone_number" : mobile,
            "message" : message,
            "image" : getCanvas.toDataURL("image/png")
        };
        $.ajax({
                method: 'post',
                url: url,
                data: post_data,
                success: function(result){
                    try{
                        let r = JSON.parse(result);
                        if(r.status == "success"){
                            close_modal('sendToTelegramModal');
                            setTimeout('printContent();',1000);
                        }
                        else
                            dental_show_flash('sendToTelegramModal_Flash', r.message);
                    }
                    catch (e){
                        dental_show_flash('sendToTelegramModal_Flash', e.message);
                    }
                }
        });
    }
    function printContent(){
        var restorepage  = $('body').html();
        $('#treatment_plan_table').html('<?= trim($message_text,'```') ?>');
        let print_head = $('#print_head').html();
        let printcontent = '<pre>'+print_head + '</pre>';
        //printcontent += '<?= trim($message_text,'```') ?><img src="/img/telegram_qr02.png" width="200"></pre>';
        $('body').empty().html(printcontent);
        window.print();
        $('body').html(restorepage);
        //close_modal('sendToTelegramModal');
        //location.reload();
    }
</script>
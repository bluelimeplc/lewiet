

            <?php
                $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
                $logged_user = $loggeduserdata['id'];
                $loggeduserpin = $loggeduserdata['pin_code'];
                $loggedusersign = $loggeduserdata['digital_signature'];
                $logged_user_name = $loggeduserdata['name'];
            ?>
            <!-- =============================================== -->
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">

                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="header-icon">
                        <i class="pe-7s-world"></i>
                    </div>
                    <div class="header-title">
                        <h1><?php echo "Doctors Dashboard";?></h1>
                        <small><?php echo "Doctors Dashboard";?></small>
                        <ol class="breadcrumb">
                            
                            <li class="active"><a href="#"><?php echo "Dashboard";?></a></li>
                        </ol>
                    </div>
                </section>

                
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                                <div class="panel panel-bd">
                                <a href="#">
                                    <div class="panel-body">
                                        <div class="statistic-box">
                                            <h2><span class="count-number"><?php echo $this->data['today_visit'] ?></span> </h2>
                                                <div class="small"> <?php echo "Today Visits";?> </div>
                                            <div class="sparkline1 text-center"></div>
                                        </div>
                                        <div class="icon"><i class="ti-user"></i></div>
                                    </div>
                                </a>    
                                </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            <a href="#">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <h2><span class="count-number"><?php echo $this->data['no_of_visits'];?></span> </h2>
                                            <div class="small"> Total Visits </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><i class="ti-user"></i></div>
                                </div>
                            </a>    
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                                <a href="#">
                                    <div class="panel-body">
                                        <div class="statistic-box">
                                            <h2><span class="count-number"><?php echo $this->data['no_of_appointments'];?></span> </h2>
                                                <div class="small"> <?php echo "Total Appointments"?> </div>
                                            <div class="sparkline1 text-center"></div>
                                        </div>
                                        <div class="icon"><i class="ti-user"></i></div>
                                    </div>
                                </a>    
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            <a href="#">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <h2><span class="count-number"><?php echo $this->data['no_of_appointments_visit'];?></span> </h2>
                                        <div class="small"><?php echo "Total Visit by Appointment"?></div>
                                        <div class="sparkline2 text-center"></div>
                                    </div>
                                    <div class="icon"><i class="ti-user"></i></div>
                                </div>
                            </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="panel panel-bd">
                            <div class="panel-heading">
                                    <div class="panel-title">
                                        <input type="date" class="form-control" value="<?= date('Y-m-d',strtotime($date_filter)) ?>" onchange="change_date(this)" style="width:250px;">
                                    </div>
                                </div> 
                        <div class="panel panel-body">
                            
                            <div class="col-sm-6 col-md-6">
                                
                                <div class="panel panel-bd">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <h4><?php echo "Today Consultations"?></h4>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-md-12">
                                        <table class="table table-bordered table-hover">
                                            
                                            <?php
                                                $paid = 0;
                                                $unpaid = 0;
                                                $credit_cnt = 0;
                                                $rows = '';
                                                //BluelimeSystem::show_and_die($doctor_visits);
                                            
                                                foreach($doctor_visits as $patients){
                                                    $payment_status = "";
                                                    if(intval($patients['is_free']) == 1)
                                                        $payment_status = "Transfer from branch";
                                                    elseif (intval($patients['is_free']) == 2)
                                                        $payment_status = "Followup after surgery / procedure";
                                                    elseif (intval($patients['is_free']) == 3)
                                                        $payment_status = "Approved Free Visit";
                                                    elseif (intval($patients['is_free']) == 4)
                                                        $payment_status = "Approved Free Visit";
                                                    elseif (intval($patients['is_free']) == 5)
                                                        $payment_status = "Referred by ".$patients['registered_by_name'];
                                                    elseif((intval($patients['is_free']) == 0) && ($patients['fs'] == ""))
                                                        $payment_status = "Within ".BluelimeConfiguration::UNPAID_VISIT_DEFAULT_DAYS." Days";
                                                    
                                                    if($patients['fs']!==""){
                                                        if($patients['bill_type']=="Credit"){
                                                            $credit_cnt++;
                                                        }
                                                        else
                                                            $paid++;                                                    
                                                    }
                                                    else{
                                                        $unpaid++;
                                                    }
                                                    
                                                    $credit = ($patients['bill_type']=="Credit")?"color:#fff;background-color:orange":"";
                                                    $credit_label = ($patients['bill_type']=="Credit")?"- Credit":"";
                                                    $rows .= '
                                                    <tr style="'.$credit.'">
                                                        <td>'.$patients['patient_id'].'</td>
                                                        <td>'.$patients['patient_name'].'</td>
                                                        <td>'.$patients['fs'].' '.$payment_status.' '.$credit_label.'</td>
                                                    </tr>
                                                    ';
                                                }
                                            // BluelimeSystem::show_and_die($credit);
                                            ?>
                                            
                                            <thead>
                                                <tr>
                                                    <th colspan="2" style="text-align: right;">Total Visits</th>
                                                    <th><?=$unpaid+$paid+$credit_cnt?></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" style="text-align: right;">Total Credit Consultations</th>
                                                    <th><?=$credit_cnt?></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" style="text-align: right;">Total Cash Consultations</th>
                                                    <th><?=$paid?></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" style="text-align: right;">Total Unpaid Consultations</th>
                                                    <th><?=$unpaid?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr><th>Card No</th><th>Patient Name</th><th>FS No</th></tr>
                                                <?= $rows ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="panel panel-bd">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <h4><?php echo "Today Procedures"?></h4>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-md-12">
                                        <table class="table table-bordered table-hover" style="font-size: smaller;">
                                        
                                            <?php
                                                $rows_procedure = '';
                                                $count = 0;
                                                $total_amount = 0;
                                                foreach($procedures as $list){
                                                    
                                                    if($list['bill']['bill_status']==BluelimeConfiguration::BILL_RECIEVED && $list['bill']['registered_by']==$logged_user){
                                                        $count++;
                                                        $total_amount += ($list['bill_quantity']*$list['bill_amount']);
                                        $rows_procedure .= '<tr>
                                                    <td>'.$list['bill']['patient_id'].'</td>
                                                    <td>'.$list['bill']['patient_name'].'</td>
                                                    <td>'.$list['service_name'].'</td>
                                                    <td>'.number_format(round($list['bill_quantity']*$list['bill_amount'],2),2).'</td>
                                                    <td>'.$list['bill']['fs_number'].'</td>
                                                </tr>';                                     
                                                    }
                                                }
                                            ?> 
                                            <thead>
                                                <tr>
                                                    <th colspan="4" style="text-align: right;">No.of Procedures</th>
                                                    <th><?=$count?></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" style="text-align: right;">Total Amount</th>
                                                    <th><?=number_format(round($total_amount,2),2)?></th>
                                                </tr>
                                                
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th>Card No</th>
                                                    <th>Patient Name</th>
                                                    <th>Procedure</th>
                                                    <th>Amount</th>
                                                    <th>FS No</th>
                                                </tr>
                                                <?= $rows_procedure ?>
                                            </tbody>
                                            <tfoot>
                                                
                                            </tfoot>
                                        </table>
                                    </div>
                                    </div>
                                    
                                </div>
                                
                            </div>
                            <?php
                                if(empty($approved_report)){
                            ?>
                                    <button type="botton" class="btn btn-primary <?=$date_filter != date('Y-m-d')?"hidden":""?>" onclick="appovereport()">Approve Today's Report</button>
                                    <div style="display: none;">
                                        <a role="button" class="btn btn-info" title="Send to my Telegram" onclick="send_to_telegram()" id="send_to_my_telegram"><i class="fa fa-send"></i></a><img height="20" src="/img/load-32_256.gif" id="send_to_my_telegram_loading" style="display:none;">
                                    </div>

                            <?php
                                }
                                else{
                            ?> 
                                    <div style="text-align: center;font-size: large;">
                                        <?php echo '<img style="height:70px;" src="data:image/jpeg;base64,'.base64_encode( $loggedusersign ).'"/>' ?><br>
                                        <b style="color:brown">Approved</b><br>
                                        <a role="button" class="btn btn-info" title="Send to my Telegram" onclick="send_to_telegram()" id="send_to_my_telegram"><i class="fa fa-send"></i></a><img height="20" src="/img/load-32_256.gif" id="send_to_my_telegram_loading" style="display:none;">
                                    </div>
                            <?php
                                }
                            ?>
                        </div>
                           </div>
                    </div>
                    
                    <div class="row">
                         <!-- Bar Chart -->
                        <div class="col-sm-12 col-md-12">
                            <div class="panel panel-bd">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4><?php echo "Appointment Chart"?></h4>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <canvas id="barChart" height="140"></canvas>
                                </div>
                            </div>
                        </div>
                        
                    </div>

                </section> <!-- /.content -->
            </div> <!-- /.content-wrapper -->

            <form onsubmit="event.preventDefault(); checkpin();">
                <div class="modal fade" id="confirmPin" role="dialog">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header dental-bg-primary">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Enter Pin Code</h4>
                            </div>
                            <div class="modal-body" style="text-align: center;">
                                <span style="color: red;font-weight: bolder;">You are About to apporve today's report and Close all visit! Please enter pin to confirm</span>
                                <input id="pin_entered" placeholder="Enter pin" type="password" maxlength="4" class="form-control focus_input_pin"
                                    autocomplete="off" pattern="[0-9]+" autofocus >
                                <input id="user_id" type="hidden" value="<?=$loggeduserdata['id'] ?>">
                                <div id="pinCheck_flash" class="alert alert-danger hide" role="alert">
                                    <span></span>
                                </div>
                                <div style="text-align: center;color:black"><b><br>
                                    <?php echo '<img style="height:100px;" src="data:image/jpeg;base64,'.base64_encode( $loggedusersign ).'"/>' ?><br>
                                    <?=$loggeduserdata['name'] ?><br>
                                    <?=$loggeduserdata['title'] ?></b>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input class="btn btn-success" value="Confirm" type="button" onclick="checkpin()">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
                <input type="hidden" id="doctor_id" value="<?=$logged_user?>">
                <input type="hidden" id="doctor_name" value="<?=$logged_user_name?>">
                <input type="hidden" id="report_date" value="<?= date('Y-m-d') ?>">;
                <input type="hidden" id="report_data" value="<?="Consultation- Unpaid: ".$unpaid ." Paid: ".$paid ." Credit: ".$credit_cnt." Total Consultation: ".$unpaid+$paid+$credit_cnt. "<br>Procedure- Number of Procedures: ".$count."Total Amount: ".$total_amount?>">
            
            <div  style="height: 0px; overflow: hidden;">
                <div class="col-lg-6" id="for_telegram">
                    <div  class="panel panel-default">
                        <div class="panel-body">
                            <hr>
                            <table class="table table-bordered table-hover table-condensed" style="">
                                <thead>
                                    <tr>
                                        <th colspan="4" style="text-align: center;"><h4><b><?=$logged_user_name?> (<?= date('Y-m-d') ?>)</b></h4></th>
                                        
                                    </tr>
                                    <tr>
                                        <th colspan="2" style="text-align: center;"><h5><b>Consultations</b></h5></th>
                                        <th colspan="2" style="text-align: center;"><h5><b>Procedure</b></h5></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td align="right">Total Visits</td><td><?= $unpaid+$paid+$credit_cnt ?></td>
                                        <td align="right">No.of Procedures</td><td><?= $count ?></td>
                                    </tr>
                                    <tr>
                                        <td align="right">Cash Consultations</td><td><?= $paid ?></td>
                                        <td align="right">Total amount</td><td><?=number_format($total_amount,2)?></td>
                                    </tr>
                                    <tr>
                                        <td align="right">Credit Consultations</td><td><?= $credit_cnt ?></td>
                                    </tr>
                                    <tr>
                                        <td align="right">Unpaid Consultations</td><td><?= $unpaid ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php 
                                $column = 7; 
                            ?>
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th colspan="<?= $column ?>"><b>Consultations receipt N<u>o</u></b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $cnt = 0;
                                        
                                        //echo '<tr>';
                                        foreach($doctor_visits as $patients){
                                            if((trim($patients['fs']) == ''))
                                                continue;

                                            if($cnt%$column==0)
                                                echo '<tr>';
                                            echo '<td>'.$patients['fs'].'</td>';
                                            $cnt++;
                                            if($cnt%$column==0)
                                                echo '</tr>';
                                        }
                                        if($cnt%$column != 0)
                                            echo '</tr>';
                                    ?>
                                </tbody>
                            </table>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="<?= $column ?>"><b>Procedure receipt N<u>o</u></b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $cnt = 0;
                                        $column = 4;
                                        foreach($procedures as $list){
                                            if((trim($list['bill']['fs_number']) == '') || ($list['bill']['bill_status']!=BluelimeConfiguration::BILL_RECIEVED))
                                                continue;

                                            if($cnt%$column==0)
                                                echo '<tr>';
                                            echo '<td>'.$list['bill']['fs_number'].'  <span class="badge">'.$list['service_name'].'</span> <span class="badge">'.number_format($list['bill_quantity']*$list['bill_amount'],2).'</span></td>';
                                            $cnt++;
                                            if($cnt%$column==0)
                                                echo '</tr>';
                                        }
                                        if($cnt%$column != 0)
                                            echo '</tr>';
                                    ?>
                                </tbody>
                            </table>
                        </div>
                                        
                        <div style="text-align: right; padding: 20px;">
                            <?php echo '<img style="height:50px;" src="data:image/jpeg;base64,'.base64_encode( $loggedusersign ).'"/>' ?>
                            <br>
                            Signature ______________
                        </div>
                    </div>
                </div>
            </div>
<script>


$(document).ready(function () {
    //bar chart
    var ctx = document.getElementById("barChart");
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Last 30 days", "Last 15 days", "Last 7 days", "Today"],
            datasets: [
                {
                    label: "<?php echo "Appointment Generated";?>",
                    data: [<?php echo 30;?>, <?php echo 15;?>, <?php echo 7;?>, <?php echo 10;?>],
                    borderColor: "#223F4A",
                    borderWidth: "0",
                    backgroundColor: "#223F4A"
                },
                {
                    label: "<?php echo "Appointment Missed";?>",
                    data: [<?php echo 30;?>, <?php echo 15;?>, <?php echo 7;?>, <?php echo 1;?>],
                    borderColor: "#57787D",
                    borderWidth: "0",
                    backgroundColor: "#57787D"
                }
            ]
        },
        options: {
            scales: {
                yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
            }
        }
    });

});

</script>
<script src="/js/html2canvas.js"></script>
    <script>
        function change_date(date){
            location = '?date='+date.value;
        }

        function focus_search(){
            $("#search_patient").focus().val($("#search_patient").val());

        }
        //document.onload(focus_search());
        var getCanvas; // global variable
        $('document').ready(function(){
        html2canvas($("#for_telegram")[0]).then((canvas) => {
                $("#for_telegram").html(canvas);
                getCanvas = canvas;
            });
        });
        
    function send_to_telegram(){
        let message = "";

        let mobile = '<?=$loggeduserdata['mobile']?>';
        let url = "/hospital/doctor/send_approved_daily_report";
        let post_data = {
            "phone_number" : mobile,
            "message" : message,
            "image" : getCanvas.toDataURL("image/png")
        };
        $('#send_to_my_telegram_loading').css('display','inline');
        $('#send_to_my_telegram').attr('disabled',true);
        $.ajax({
                method: 'post',
                url: url,
                data: post_data,
                async:false,
                success: function(result){
                    try{
                        let r = JSON.parse(result);
                        if(r.status == "success"){
                            toastr['success']('Sent to your Telegram');
                            $('#send_to_my_telegram_loading').css('display','none');
                            $('#send_to_my_telegram').removeAttr('disabled');

                            //location = '/hospital/doctor/doctordashboard';
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
    function appovereport(){
        $('#confirmPin').modal('show');
    }
    
    function saveapproval(){
        var doctor_id = $("#doctor_id").val();
        var doctor_name = $("#doctor_name").val();
        var report_date = $("#report_date").val();
        var report_data = $("#report_data").val();
        $.ajax({
            method: 'post',
            async:false,
            url: "/hospital/doctor/approvedoctorreport",
            data: {"doctor_id":doctor_id,'doctor_name':doctor_name,'report_date':report_date,'report_data':report_data}
        });
    }

    function checkpin(){
        var pin = $("#pin_entered").val();
        var user = $("#user_id").val();
        $.ajax({
            method: 'post',
            url: "/hospital/ajax/checkpin",
            data: {"pin_code":pin,'id':user},
            dataType: 'json',
            async: false,
            success: function(result) { 
                if(result.status == 'success'){
                    saveapproval();
                    send_to_telegram();
                    location = '/hospital/doctor/doctordashboard';
                }
                else
                    toastr['error'](result.message);
            },
            error:function(jqXHR,status,error){
                    toastr['error'](error);
            }
        });
    }
    </script>            
    <!-- =============================================== -->
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1>Reception</h1>
                <small>Prescription View</small>
                <span style="padding-left: 300px;">
                    <a class="btn btn-info" href="#" onclick="print_printable_document()" title="Print"  data-toggle="tooltip" data-html="true"><span class="fa fa-print"></span></a>
                    <i  title="Send Telegram"  data-toggle="tooltip" data-html="true" ><a class="btn btn-info" href="#" title="Send Telegram"  data-target="#sendToTelegramModal"  data-toggle="modal" data-html="true"><span class="fa fa-telegram"></span></a></i>
                    <i  title="Send to Branch"  data-toggle="tooltip" data-html="true" ><a class="btn btn-info" href="#" title="Send to Branch"  data-target="#select_branch"  data-toggle="modal" data-html="true"><span class="fa fa-send-o"></span></a></i>
            </span>
            </div>
            
        </section>
        <section class="content">
            <?= view('Include/_prescription_print') ?>
            <div  style="height: 0mm; overflow: hidden; width: 148.5mm;">
            <div  id="for_telegram">
            <?= view('Include/_prescription_print') ?></div>
        </div>
        </section>

    </div> <!-- /.content-wrapper -->

    <div class="modal fade" id="select_branch" tabindex="-1" role="dialog" aria-labelledby="select_branch" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            
        <div class="modal-header dental-bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Branch Location</h4>
                                    
                                    
                                </div>
                                
            <div class="form-body"">
                <div class="modal-body">
                    <div class="panel-body">
                    <h3 class="modal-title"><?= $patient['first_name'].' '.$patient['middle_name'].' '.$patient['last_name'] ?></h3>
                                    
                                    <div class="tab-pane active">
                                        <label class="control-label">Pharmacy Branch:</label>
                                    <div class="tab-pane active">
                                        <select class="form-control" id="sent_branch"  name="sent_branch"  required>
                                            <option value="">--Select Branch--</option>
                                            <option value="WOLLOSEFER">WOLLO SEFER</option>
                                            <option value="LEBU">LEBU</option>
                                        </select>
                                        <br>
                                    <div style="margin-left: 90px;color:crimson; font-size: large;"><label id="free_service_status"></label></div>
                                    </div>
                                    </div>
                                    
                                    
                                    
                        </div>  
                            
                    </div>
                        
                </div>
                <div class="modal-footer">
                
                <button type="button"  onclick="sendprescriptiontobranch()" class="btn btn-primary" >Send</button>
                
            </div>
                    
            </div>
                
           
            </div>
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
                        <input id="txt_sendToTelegramMobile" type="text" class="form-control" value="<?= $patient['mobile'];?>">
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
<script>
    function print_printable_document(){
        var printContents = document.getElementById('printable').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        // $.ajax({
        //     url: '/hospital/reception/prescriptionprinted',
        //     method: 'POST',
        //     data: {prescription_group_id:'<?= $_GET['prescription_group_id']?>'},
        //     async:false,
        //     success:function(){
        //         ;
        //     }
        // });
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
<script src="/js/html2canvas.js"></script>
    <script>
        
        

    var getCanvas; // global variable
        $('document').ready(function(){
        html2canvas($("#for_telegram")[0]).then((canvas) => {
                $("#for_telegram").html(canvas);
                getCanvas = canvas;
            });
        });
        

    function sendprescriptiontobranch(){
        
        let patient_name = "<?= $patient['first_name'].' '.$patient['middle_name'].' '.$patient['last_name'] ?>";
        let branch_from = "<?=$_ENV['BRANCH']??''?>";
        let prescription_group_id = "<?=$_GET['prescription_group_id']?>";
        //alert(prescription_group_id);
        let branch_to = $('#sent_branch').val();
        let message = "Prescription";
        let doctor_name = "<?= $prescription_main['doctor']['name'] ?? '' ?>";
        let mobile = <?= $patient['mobile'] ?>;
        //let url = "http://api.bluelimeplc.com.local/damota/save_message";
        let url = "https://api.bluelimeplc.com/rank/save_prescription";
        let post_data = {
            "phone_number" : mobile,
            "message" : message,
            "doctor_name" : doctor_name,
            "patient_name" : patient_name,
            "prescription_group_id" : prescription_group_id,
            "branch_from" : branch_from,
            "branch_to" : branch_to,
            "image" : getCanvas.toDataURL("image/png")
        };
        $.ajax({
                method: 'post',
                url: url,
                data: post_data,
                dataType:'JSON',
                async:false,
                success: function(result){
                    
                        let r = JSON.parse(result);
                        if(r.status == "success"){
                            toastr["success"]("Prescription Sent to "+ branch_to);
                            close_modal('select_branch');
                        }
                        else
                            toastr["error"]("Prescription Not Sent");
                }
        });
        $.ajax({
                method: 'post',
                url: '/hospital/reception/save_online_prescription',
                data: post_data,
                dataType:'JSON',
                async:false,
                success: function(result){
                    try{
                        let r = JSON.parse(result);
                        if(r.status == "success"){
                            toastr["success"]("Prescription Sent to "+ branch_to);
                            close_modal('select_branch');
                        }
                        else
                            dental_show_flash('sendToTelegramModal_Flash', r.value);
                    }
                    catch (e){
                        toastr["success"]("Prescription Sent to "+ branch_to);
                        close_modal('select_branch');
                    }
                }
        });
    }
    </script>
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
                function send_to_telegram(){

        let message = "<?= @$message_text??"" ?>";

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
    </script>
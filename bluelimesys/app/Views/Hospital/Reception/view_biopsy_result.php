<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    helper('form');
    $biopsy_result = $this->data['biopsy_result'];
?>

<style>
    .status-timeline {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .status-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 0 15px;
        position: relative;
    }

    .status-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 15px;
        left: 100%;
        width: 20px;
        height: 2px;
        background: #dee2e6;
        z-index: 1;
    }

    .status-icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 5px;
        transition: all 0.3s ease;
    }

    .status-icon.active {
        background: #28a745;
        color: white;
        box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.3);
    }

    .status-icon.inactive {
        background: #6c757d;
        color: white;
    }

    .status-icon.pending {
        background: orange;
        color: white;
        animation: blink 2s infinite;
    }

    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0; }
        100% { opacity: 1; }
    }

    .status-label {
        font-size: 11px;
        font-weight: 600;
        color: #495057;
        text-align: center;
        max-width: 100px;
        word-wrap: break-word;
    }
</style>

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
                <small>Biopsy Report</small>
                <span style="padding-left: 300px;">
                    <input type="text" placeholder="Enter Email" value="<?=(BluelimeSystem::convert_to_array($order['additional_info'])['email'])??"" ?>" class="form-control" id="crt_email" style="width: 250px;display:inline-block;margin-right:10px;">
                    <button title="Send Email"  class="btn btn-info" onclick="send_email()"><span class="fas fa-envelope"></button>
                    <a  title="Print Document" class="btn btn-info" href="#" onclick="print_printable_document()"><span class="fa fa-print"></a>
                    
                    
            </span>
            </div>
            
        </section>
        <section class="content">
            <!-- Status Timeline -->
            <div class="status-timeline" style="width: 100%;">
                <?php
                $statuses = [
                    1 => ['label' => 'Paid', 'icon' => 'fa-money-bill-wave'],
                    2 => ['label' => 'Sample Collected', 'icon' => 'fa-vial'],
                    3 => ['label' => 'Sample Sent', 'icon' => 'fa-paper-plane'],
                    4 => ['label' => 'Slide Received', 'icon' => 'fa-microscope'],
                    5 => ['label' => 'Result Saved', 'icon' => 'fa-save'],
                    6 => ['label' => 'Result Approved', 'icon' => 'fa-check-circle'],
                    7 => ['label' => 'Result Delivered', 'icon' => 'fa-truck']
                ];
                $current_status = $_GET['status'] ?? 0;

                foreach ($statuses as $status => $data) {
                    $isActive = $current_status >= $status;
                    $label = $data['label'];
                    if ($status == 7 && $current_status == 6) {
                        $class = 'pending';
                        $label = 'Click to Deliver';
                    } else {
                        $class = $isActive ? 'active' : 'inactive';
                    }
                    echo '<div class="status-step">';
                    if ($status == 7 && $current_status == 6) {
                        echo '<a href="/hospital/reception/change_sample_status?order_id=' . $_GET['order_id'] . '&brid=' . ($_GET['brid'] ?? '') . '" style="display: flex; flex-direction: column; align-items: center;">';
                        echo '<div class="status-icon ' . $class . '">';
                        echo '<i class="fas ' . $data['icon'] . '"></i>';
                        echo '</div>';
                        echo '<div class="status-label">' . $label . '</div>';
                        echo '</a>';
                    } else {
                        echo '<div class="status-icon ' . $class . '">';
                        echo '<i class="fas ' . $data['icon'] . '"></i>';
                        echo '</div>';
                        echo '<div class="status-label">' . $label . '</div>';

                        if ($status == 6 && $current_status == 6 && ($loggeduserdata['role'] ?? '') === 'SUPER_ADMIN') {
                            echo '<div style="margin-top:5px;"><button class="btn btn-xs btn-warning" onclick="reverseApproved(' . ($_GET['order_id'] ?? 0) . ')">Reverse to Result Saved</button></div>';
                        }
                    }
                    echo '</div>';
                }
                ?>
            </div>
            
            <?= view('Include/_pathology_result_print2') ?>
        </section>
        

    </div> <!-- /.content-wrapper -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        function doPrint(el){
            var restorepage  = $('body').html();
            var printcontent = $('#' + el).clone();
            $('body').empty().html(printcontent);
            window.print();
            $('body').html(restorepage);
            location.reload();
        }

        
        function approveresult(){
           
            location = '/hospital/pathology/approveresult?brid='+<?=$_GET['brid']??0?>+'&order_id='+<?=$_GET['order_id']?>;
        }

        function reverseApproved(orderId){
            if (!confirm('Are you sure you want to reverse approved result to saved status?')) return;
            $.ajax({
                method: 'post',
                url: '/hospital/pathology/reverseresult',
                dataType: 'json',
                data: {
                    order_main_id: orderId
                },
                success: function(response){
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        location = '/hospital/reception/viewbiopsyresult?brid='+<?=$_GET['brid']??0?>+'&status='+5+'&order_id='+<?=$_GET['order_id']?>;
                    } else {
                        toastr.error(response.message || 'Unable to reverse result');
                    }
                },
                error: function(xhr, status, error){
                    toastr.error('Error reversing result: '+error);
                }
            });
        }
    </script>
    <script>
        
    function print_printable_document(){
        var printContents = document.getElementById('printable').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

    function send_email(){
                    let email = $('#crt_email').val();
                    if(!email){
                        toastr.error('Please enter recipient email');
                        return;
                    }

                    let printable = document.querySelector('#printable');
                    if(!printable){
                        toastr.error('Printable preview content not found');
                        return;
                    }

                    if (typeof html2canvas === 'undefined') {
                        toastr.error('html2canvas library not loaded');
                        return;
                    }

                    const sendingToast = toastr.info('Rendering preview to image and sending email...', null, {
                        timeOut: 0,
                        extendedTimeOut: 0,
                        closeButton: true,
                        tapToDismiss: false
                    });

                    // temporarily hide email controls and footer for clean snapshot
                    const emailControls = printable.querySelectorAll('#crt_email, #btn_approve_result, .modal-footer');
                    const prevDisplay = [];
                    emailControls.forEach(function(el, i){
                        prevDisplay[i] = el.style.display;
                        el.style.display = 'none';
                    });

                    html2canvas(printable, {scale: 2, useCORS: true}).then(function(canvas){
                        // restore controls
                        emailControls.forEach(function(el, i){
                            el.style.display = prevDisplay[i] || '';
                        });

                        // Crop whitespace from right side of canvas
                        let ctx = canvas.getContext('2d');
                        let imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        let data = imageData.data;
                        
                        // Find the rightmost non-white pixel
                        let rightEdge = canvas.width;
                        for (let x = canvas.width - 1; x >= 0; x--) {
                            let hasContent = false;
                            for (let y = 0; y < canvas.height; y++) {
                                let index = (y * canvas.width + x) * 4;
                                let r = data[index];
                                let g = data[index + 1];
                                let b = data[index + 2];
                                let a = data[index + 3];
                                // Consider pixel as content if not white/transparent
                                if (r < 250 || g < 250 || b < 250 || a < 128) {
                                    hasContent = true;
                                    break;
                                }
                            }
                            if (hasContent) {
                                rightEdge = x + 1; // Include this column
                                break;
                            }
                        }
                        
                        // Create cropped canvas
                        let croppedCanvas = document.createElement('canvas');
                        let croppedCtx = croppedCanvas.getContext('2d');
                        croppedCanvas.width = rightEdge;
                        croppedCanvas.height = canvas.height;
                        croppedCtx.drawImage(canvas, 0, 0, rightEdge, canvas.height, 0, 0, rightEdge, canvas.height);
                        
                        let dataUrl = croppedCanvas.toDataURL('image/png');

                        $.ajax({
                            url: '/hospital/reception/send_email',
                            method: 'post',
                            dataType: 'json',
                            data: {
                                email: email,
                                image_data: dataUrl,
                                subject: 'Histopathology report for ' + $('#rt_pname').text(),
                                message: 'Attached is the histopathology report for ' + $('#rt_pname').text() + '.'
                            },
                            success: function(result){
                                toastr.clear(sendingToast);
                                if(result.status == 'success'){
                                    toastr.success('Email Sent to '+ result.value);
                                } else {
                                    toastr.error('Error: ' + result.message);
                                }
                            },
                            error: function(xhr, status, error){
                                toastr.clear(sendingToast);
                                toastr.error('Email send failed: ' + error);
                            }
                        });
                    }).catch(function(err){
                        toastr.clear(sendingToast);
                        toastr.error('Failed to render image: ' + err.message);
                    });
                }
</script>

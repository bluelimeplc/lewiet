<?php
    $loggeduserdata = \BluelimeAuthentication::get_logged_user_data();
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $patient = $this->data['patient'];
    $fname = @$patient['first_name'];
    $formated_mrn_no = "MRN-".sprintf('%05d', @$patient['id']);
    $patient_id = @$_GET['pid'];
    $company_header = \BluelimeSystem::get_company_header();

    // Retrieve waiting list from controller data
    $waiting_list = $this->data['waiting_list'] ?? [];
    $doctors = $this->data['doctors'] ?? [];
    
    $waitingList = [];
    $doctorQueue = [];
    $doctorPriorityQueue = [];
    $priorityCodeMap = [
        'emergency' => '1',
        'elderly' => '2',
        'kids_under_3_months' => '3',
    ];

    function getDoctorQueueCode($doctorId, $doctorName = '') {
        $doctorId = (int) $doctorId;

        if($doctorId==18){
            $doctorKey = 1; 
        }
        else if($doctorId==19){
            $doctorKey = 2; 
        }
        else if($doctorId==20){
            $doctorKey = 3; 
        }
        else{
            $doctorKey = 0;
        }

        return $doctorKey;
    }

    // Allow controller to pass average TAT per doctor in minutes via $doctor_avg_tat
    $default_avg_tat = 10; // fallback minutes per patient
    $doctorAvgTat = [];
    if (isset($doctor_avg_tat) && is_array($doctor_avg_tat)) {
        $doctorAvgTat = $doctor_avg_tat;
    } elseif (isset($this->data) && isset($this->data['doctor_avg_tat']) && is_array($this->data['doctor_avg_tat'])) {
        $doctorAvgTat = $this->data['doctor_avg_tat'];
    }
    
    // Retrieve doctor active counts, appointment counts, and average TAT
    $doctor_active_counts = $this->data['doctor_active_counts'] ?? [];
    $doctor_appointment_counts = $this->data['doctor_appointment_counts'] ?? [];

    foreach ($waiting_list as $key => $patientData) {
        $doctorId = $patientData['doctor_id'];

        // if($doctorId==18){
        //     $doctorKey = 1; 
        // }
        // else if($doctorId==19){
        //     $doctorKey = 2; 
        // }
        // else if($doctorId==20){
        //     $doctorKey = 3; 
        // }
        
        $doctorKey = getDoctorQueueCode($doctorId);

        $priorityReason = $patientData['priority_reason'] ?? '';
        $priorityCode = $priorityCodeMap[$priorityReason] ?? '';

        if (!isset($doctorQueue[$doctorKey])) {
            $doctorQueue[$doctorKey] = 1;
        } else {
            $doctorQueue[$doctorKey]++;
        }

        if (!isset($doctorPriorityQueue[$doctorKey][$priorityCode])) {
            $doctorPriorityQueue[$doctorKey][$priorityCode] = 1;
        } else {
            $doctorPriorityQueue[$doctorKey][$priorityCode]++;
        }

        $waiting_number = $doctorQueue[$doctorKey];
        $priority_sequence = $doctorPriorityQueue[$doctorKey][$priorityCode];
        $patients_before = max(0, $waiting_number - 1);
        $active_count_for_doc = $doctor_active_counts[$doctorId] ?? 0;
        $appointment_count_for_doc = $doctor_appointment_counts[$doctorId] ?? 0;
        $before_you_total = $active_count_for_doc + $appointment_count_for_doc + $patients_before;
        $avg = $default_avg_tat;
        $estimated_wait_minutes = round($before_you_total * $avg);
        $doctor_code = getDoctorQueueCode($doctorId, $patientData['doctor_name'] ?? '');
        $queue_code = $doctor_code . $priorityCode .  str_pad($priority_sequence, 2, '0', STR_PAD_LEFT);

        $waitingList[$key] = $patientData;
        $waitingList[$key]['waiting_number'] = $waiting_number;
        $waitingList[$key]['queue_code'] = $queue_code;
        $waitingList[$key]['priority_code'] = $priorityCode;
        $waitingList[$key]['doctor_code'] = $doctor_code;
        $waitingList[$key]['estimated_wait_minutes'] = $estimated_wait_minutes;
        $waitingList[$key]['before_you_total'] = $before_you_total;
        $waitingList[$key]['appointment_count'] = $appointment_count_for_doc;
    }
//\BluelimeSystem::show_and_die($this->data['id'])
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
            <h1><?php echo lang('Reception');?></h1>
            <small><?php echo lang('Waiting List');?></small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/reception/">New Patient</a></li>
            </ol>
        </div>
    </section>
    <section class="content">
            <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    echo form_open_multipart('/hospital/reception/waitinglist', $attributes);
            ?>

            <!-- Search Area -->
            <div class="row">
                            
                    <div class="col-sm-12 col-md-12">
                        <div class="panel panel-bd">
                            <div class="panel-body">
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                        <input id="search_waiting_list" type="text" name="search_waiting_list" class="form-control" >
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button> 
                                        </div>
                                    </div>
                                    
                                </form>
                            </div>
                        </div>
                    </div>
            
                    <?php
                        form_close();
                    ?>
                    
                    <!-- Doctor Statistics Section>
                    <div class="col-md-12">
                        <div class="panel panel-bd">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <h4><?php //echo lang('Doctor Statistics');?></h4>
                                </div>
                            </div>
                            <div class="panel-body table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr style="background-color: #f5f5f5;">
                                            <th><?php echo lang('Doctor Name');?></th>
                                            <th style="text-align: center;">Active Visits</th>
                                            <th style="text-align: center;">Waiting List</th>
                                            <th style="text-align: center;">Total Patients</th>
                                            <th style="text-align: center;">Avg TAT (minutes)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            // Calculate per-doctor statistics
                                            $doctor_stats = [];
                                            foreach($doctors as $doctor) {
                                                $docId = $doctor['id'];
                                                $docName = $doctor['name'] ?? 'N/A';
                                                $active_count = $doctor_active_counts[$docId] ?? 0;
                                                $waiting_count = 0;
                                                
                                                // Count waiting list patients for this doctor
                                                foreach($waiting_list as $patient) {
                                                    if($patient['doctor_id'] == $docId) {
                                                        $waiting_count++;
                                                    }
                                                }
                                                
                                                $total = $active_count + $waiting_count;
                                                $avg_tat = isset($doctorAvgTat[$docId]) && $doctorAvgTat[$docId] !== null ? round($doctorAvgTat[$docId], 1) : 'N/A';
                                                
                                                $doctor_stats[] = [
                                                    'name' => $docName,
                                                    'active' => $active_count,
                                                    'waiting' => $waiting_count,
                                                    'total' => $total,
                                                    'avg_tat' => $avg_tat
                                                ];
                                            }
                                        ?>
                                        <?php foreach($doctor_stats as $stat): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($stat['name'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                            <td style="text-align: center;">
                                                <span class="badge badge-primary"><?php echo $stat['active']; ?></span>
                                            </td>
                                            <td style="text-align: center;">
                                                <span class="badge badge-warning"><?php echo $stat['waiting']; ?></span>
                                            </td>
                                            <td style="text-align: center;">
                                                <strong><?php echo $stat['total']; ?></strong>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php if($stat['avg_tat'] !== 'N/A'): ?>
                                                    <span class="badge badge-info"><?php echo $stat['avg_tat']; ?> min</span>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div -->
                    
                    <!-- Main content -->
                    
                        
                            <div class="col-md-12">
                            
                                <div class="panel panel-bd">
                                    <div class="panel-heading">
                                        <!--div ><a href="" class="btn btn-success pull-right" data-target="#new_waiting_list" data-toggle="modal" data-html="true"> <i class="ti-time"></i> Add New</a></div-->
                                        <div class="panel-title" >
                                            <h4><?php echo lang('Waiting List');?> </h4>
                                        </div>
                                    </div>
                                    <div class="panel-body  table-responsive">
                                        <table width="100%" class="table table-striped table-bordered table-hover dt-responsive" id="waiting_list">
                                            <thead>
                                                <tr>
                                                    <td>W.N<u>o</u></td>
                                                    <th class="all"><?php echo lang('Patient Name'); ?></th>
                                                    <th class="all"><?php echo lang('Card N<u>o</u>'); ?></th>
                                                    <th class="all"><?php echo lang('Phone Number'); ?></th>
                                                    <th class="all"><?php echo lang('Doctor'); ?></th>
                                                    <th class="all"><?php echo lang('Time'); ?></th>
                                                    <th class="all">Est. Wait</th>
                                    <th class="all">Priority</th>
                                                    <th class="desktop"><?php echo lang('Action'); ?></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                        <?php foreach ($waitingList as $key => $patient) { ?>
                                                        <tr>
                                                            <td>
                                                                <span><?php echo htmlspecialchars($patient['queue_code'] ?? $patient['waiting_number']); ?></span>
                                                                <?php
                                                                    $active_count_for_doc = $doctor_active_counts[$patient['doctor_id']] ?? 0;
                                                                    $patients_before = max(0, ($patient['waiting_number'] ?? 1) - 1);
                                                                    $before_you_total = $patient['before_you_total'] ?? ($active_count_for_doc + $patients_before);
                                                                ?>
                                                                <button type="button" class="btn btn-info btn-xs" style="margin-left:8px;" onclick="showQueuePrintModal('<?php echo htmlspecialchars($patient['queue_code'] ?? $patient['waiting_number'], ENT_QUOTES, 'UTF-8') ?>', '<?php echo htmlspecialchars($patient['full_name'], ENT_QUOTES, 'UTF-8') ?>', '<?php echo htmlspecialchars($patient['doctor_name'], ENT_QUOTES, 'UTF-8') ?>', '<?php echo $patient['patient_id'] ?? '' ?>', '<?php echo $patient['mobile'] ?? '' ?>', '<?php echo date('Y-m-d H:i', strtotime($patient['created_at'])) ?>', '<?php echo $patient['estimated_wait_minutes'] ?? 0 ?>', <?php echo $active_count_for_doc ?>, <?php echo $patients_before ?>, <?php echo $patient['appointment_count'] ?? 0 ?>)">
                                                                    <i class="fa fa-print"></i>
                                                                </button>
                                                            </td>
                                                            <td><?php echo $patient['full_name']?>&emsp;<span class="fa fa<?= ($patient['patient_id']==0)?"-user-plus":""; ?>" style="color:green; font-size: 25px"></span></td>
                                                            <td><?php echo $patient['patient_id']?></td>
                                                            <td><?php echo $patient['mobile']?></td>
                                                            <td><?php echo $patient['doctor_name']?></td>
                                                            <td><?php echo $new_date_format = date('Y-m-d H:i', strtotime($patient['created_at']));?></td>
                                                            <?php
                                                                $estimatedWait = $patient['estimated_wait_minutes'] ?? 0;
                                                                $createdAt = isset($patient['created_at']) ? strtotime($patient['created_at']) : null;
                                                                $isOverdue = false;
                                                                $timeLeftText = '';
                                                                if ($createdAt !== null && $estimatedWait > 0) {
                                                                    $elapsedMinutes = max(0, round((time() - $createdAt) / 60));
                                                                    $isOverdue = $elapsedMinutes > $estimatedWait;
                                                                    $timeLeft = max(0, $estimatedWait - $elapsedMinutes);
                                                                    $timeLeftText = $isOverdue
                                                                        ? ''
                                                                        : ' (' . $timeLeft . ' min left)';
                                                                }
                                                            ?>
                                                            <td>
                                                                <?php echo $estimatedWait . ' min' . $timeLeftText; ?>
                                                                <?php if ($isOverdue): ?>
                                                                    <span class="label label-danger" style="margin-left:6px;">Passed <?php echo max(0, $elapsedMinutes - $estimatedWait); ?> min</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                            <?php if (!empty($patient['priority_reason'])): ?>
                                                <?php
                                                    $priorityLabels = [
                                                        'elderly' => 'Elderly',
                                                        'kids_under_3_months' => 'Kids < 3 Months',
                                                        'emergency' => 'Emergency',
                                                    ];
                                                    $label = $priorityLabels[$patient['priority_reason']] ?? ucfirst(str_replace('_', ' ', $patient['priority_reason']));
                                                ?>
                                                <span class="label label-danger"><?= esc($label) ?></span>
                                            <?php else: ?>
                                                <span class="label label-default">Normal</span>
                                            <?php endif; ?>
                                        </td>       
                                                            <td>
                                                            <a onclick="return confirm('Are You Sure you want to remove patient from Waiting List?')" href="/hospital/reception/donewaiting?wlid=<?php echo $patient['id']?>" class="btn btn-danger btn-sm"> <i class="fa fa-stop"></i></a>
                                                            <a href="/hospital/reception/revisit?id=<?php echo $patient['patient_id']?>&did=<?php echo $patient['doctor_id']?>&wlid=<?php echo $patient['id']?>" class="btn btn-primary btn-sm"> <i class="fa fa-eye"></i></a>
                                                            <button type="button" class="btn btn-warning btn-sm" style="margin-left:6px;" onclick="showPriorityModal(<?php echo $patient['id'] ?>, '<?php echo htmlspecialchars($patient['priority_reason'] ?? '', ENT_QUOTES, 'UTF-8') ?>')">
                                                                <i class="fa fa-star"></i>
                                                            </button>
                                                            </td>                                   
                                                        </tr>
                                                        <?php } ?>
                                            </tbody>
                                        </table>
                                        
                                    </div>
                                </div>
                            </div>

                        
                            
    </section>
</div>

<div class="modal fade"   style="height: 200mm; width: 90mm;" id="printWaitingNumber" tabindex="-1" role="dialog" aria-labelledby="PrintWaitingNumber" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-body" style="color: black;">
            <div id="printWaitingNumberArea">
                <div>
                    <?php echo '<img width="100" style="margin-bottom:10px" src="data:image/jpeg;base64,'.base64_encode($company_header['logo_printouts']).'"/>'; ?>
                    <span style="font-size:11px; float:right; border-left: 1px solid black; padding-left: 12px; height: 60px;">
                        <table>
                             <tr><td></td><td></td></tr>
                            <tr><td>Tel.</td><td><?= '6797' ?></td></tr>
                            <tr><td colspan="2" style="font-size:9px;"><?= $company_header['website'] ?></td></tr>
                            <tr><td colspan="2" style="font-size:9px;"><?= $company_header['email'] ?></td></tr>
                        </table>
                    </span>
                </div>
                <hr style="border-width: 2px;">
                <div class="container header">
                    <div class="row details-content">
                        <div class="single-header-info">
                            <span style="font-size:12px;"><strong><?php echo lang('Patient Name');?> : </strong><span id="queue_patient_name"></span></span><br>
                            <span style="font-size:12px;"><strong><?php echo lang('Card No');?> : </strong><span id="queue_mrn_text"></span></span><br>
                             <span style="font-size:12px;"><strong><?php echo lang('Assigned Doctor');?> : </strong><span id="queue_doctor_name"></span></span><br>
                            <span style="font-size:12px;"><strong><?php echo lang('Visit Date');?> : </strong><span id="queue_visit_date_text"></span></span><br>
                            <span style="font-size:12px;"><strong>Before you: </strong>
                                <span id="queue_active_count" class="label label-info" style="margin-right:6px;">0 active</span>
                                <span id="queue_appointment_count" class="label label-primary" style="margin-right:6px;">0 Booked</span>
                                <span id="queue_waiting_ahead" class="label label-warning" style="margin-right:6px;">0 waiting</span>
                                <span id="queue_total_count" class="label label-success">0 total</span>
                            </span><br>
                            <!--span style="font-size:12px;"><strong>Estimated Wait: </strong><span id="queue_estimated_wait_timex"></span></span><br-->
                            <span style="font-size:14px;"><strong><?=($priorityReason??"Normal")." " ."Waiting" ?></strong><br>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row details-content">
                        <div style="width: 80mm;">
                            <div class="table-responsive marg"><span style="font-size:14px;"><div class="col-sm-4 col-md-4"><strong><?php echo lang('Queue No'); ?></strong></div></span>
                            </div>
                            <div class="col-sm-12 col-md-12" align="center" style="border-width:2px; text-align:center; font-weight:bold; font-size:40px; text-align:text-center; width:120px">
                                <p style="border-style: dotted; margin:0; padding:0px;"><span id="queue_number_text"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <span style="font-size: smaller; "><p>To help us serve you best, we prioritize scheduled appointments, guests with disabilities, the elderly, and parents with babies under 3 months. Thank you for your understanding!</p></span>
            <hr>
            <p style="font-size:8px; text-align: center;">Powered by Bluelime Technologies<br>www.bluelimeplc.com</p>
        </div>
        <div class="modal-footer col-md-12">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
            <button type="button" class="btn btn-success" onclick="printContent('printWaitingNumberArea')">Print</button>
        </div>
    </div>
</div>

<div class="modal fade" id="new_waiting_list" tabindex="-1" role="dialog" aria-labelledby="NewWaitingList" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
            <div class="panel-body">
                <div class="portlet-body form">
                    
                
                <?php 
                    helper('form');
                    $attributes = array('class' => 'form-horizontal','name'=>'p_info');
                    echo form_open('/hospital/reception/addwaitinglist', $attributes);                
                ?>

                        <div class="form-body"">
                            <div class="form-group col-md-12">
                                <label class="control-label">Full Name</label>
                                <div class="input-group">
                                    <?php
                                    //\BluelimeSystem::show_and_die($patient['id']);
                                        if(isset($patient['id'])){
                                            $edit = "readonly";
                                        }
                                        else
                                            $edit = ""
                                    ?>
                                    <input type="text" hidden id="patient_id" name="patient_id" value="<?php echo @$patient_id?>">
                                    <input type="text" hidden name="wlid" value="<?php echo @$wlid ?? ""?>">
                                    <input type="text" hidden name="status" value="<?php echo 1; ?>">
                                    <input type="text" hidden name="registered_by" value="<?php echo $logged_user?>">
                                    <input type="text" hidden name="registered_by_name" value="<?php echo $logged_user_name?>">
                                    <input type="text" required  name="first_name" id="first_name" <?php echo $edit; ?>  class="form-control" placeholder="Name" value="<?php echo @$fname ?? ""  ?>">
                                    <span class="input-group-addon"></span>
                                    <input type="text" required name="middle_name" <?php echo $edit; ?> class="form-control" placeholder="Father Name" value="<?php echo @$mname ?? ""  ?>">
                                    <span class="input-group-addon"></span>
                                    <input type="text" name="last_name"  <?php echo $edit; ?> class="form-control" placeholder="Grand Father" value="<?php echo @$lname ?? ""  ?>">
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label"><?php echo "Mobile"?> :</label>
                                <div class="">
                                    <input type="txt" <?php echo $edit?>placeholder="09xxxxxxxx"  name="mobile"  <?php echo $edit; ?> value="<?php echo @$mobile ?? ""  ?>"  required class="form-control">
                                </div>
                            </div>
                            
                           

                            <div class="form-group col-md-12">
                                <label class="control-label">Doctor:</label>
                            
                            </div>
                            
                            
                            <div class="form-group row"></div>
                            <div class="alert alert-warning <?php echo (@$_GET['rt']==1)?"":"hide"; ?>" role="alert">
                            <span></span>
                            </div>
                            
                    </div>  
                        
                    </div>
                    
                </div>
            </div>
                
            <div class="modal-footer">
                
                <button type="submit" class="btn btn-primary" >Save</button>
                
            </div>
    
    </div>
</div>
</div>

<div class="modal fade" id="priorityModal" tabindex="-1" role="dialog" aria-labelledby="PriorityModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="/hospital/reception/prioritize_waiting" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="PriorityModalLabel">Prioritize Waiting Patient</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="priority_wlid" name="wlid" value="">
                    <div class="form-group">
                        <label for="priority_reason">Priority Reason</label>
                        <select id="priority_reason" name="priority_reason" class="form-control" required>
                            <option value="">Select reason</option>
                            <option value="elderly">Elderly</option>
                            <option value="kids_under_3_months">Kids &lt; 3 Months</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Current priority will move this patient ahead in the queue.</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Priority</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function search_patient_list(){
                    
                    var search_key = $('#search_patient').val();
                    $.ajax({
                        method: 'post',
                        url: "<?php echo base_url(); ?>hospital/ajax/searchpatient",
                        data: {"search_key":search_key},
                        success: function(data){
                            try{
                                var container = $(".patient_list");
                                container.html(data);
                                //console.log(data);
                            }
                            catch (e){
                                dental_show_flash('patient_list', 'No patient found.');
                            }
                        }
                    });
                }

                function showPriorityModal(wlid, currentReason) {
                    $('#priority_wlid').val(wlid);
                    $('#priority_reason').val(currentReason);
                    $('#priorityModal').modal('show');
                }

                function showQueuePrintModal(queueNumber, patientName, doctorName, mrn, phone, visitDate, estimatedMinutes, activeCount, waitingAhead, appointmentCount) {
                    $('#queue_number_text').text(queueNumber);
                    $('#queue_patient_name').text(patientName);
                    $('#queue_doctor_name').text(doctorName);
                    $('#queue_mrn_text').text(mrn ? 'MRN-' + String(mrn).padStart(5, '0') : '');
                    $('#queue_phone_text').text(phone);
                    $('#queue_visit_date_text').text(visitDate);
                    activeCount = parseInt(activeCount) || 0;
                    waitingAhead = parseInt(waitingAhead) || 0;
                    appointmentCount = parseInt(appointmentCount) || 0;
                    var total = activeCount + appointmentCount + waitingAhead;
                    $('#queue_active_count').text(activeCount + ' active');
                    $('#queue_appointment_count').text(appointmentCount + ' booked');
                    $('#queue_waiting_ahead').text(waitingAhead + ' waiting');
                    $('#queue_total_count').text(total + ' total');
                    // format estimated minutes to human readable
                    var estText = formatMinutesHuman(estimatedMinutes);
                    $('#queue_estimated_wait_time').text(estText);
                    $('#printWaitingNumber').modal('show');
                }

                function formatMinutesHuman(minutes) {
                    minutes = parseInt(minutes) || 0;
                    if (minutes <= 0) return 'Less than a minute';
                    if (minutes < 60) return minutes + ' min';
                    var hrs = Math.floor(minutes / 60);
                    var mins = minutes % 60;
                    return hrs + ' hr' + (mins ? ' ' + mins + ' min' : '');
                }

                function printContent(el){
                    var restorepage  = $('body').html();
                    var printcontent = $('#' + el).clone();
                    $('body').empty().html(printcontent);
                    window.print();
                    $('body').html(restorepage);
                    location.reload();
                }

</script>
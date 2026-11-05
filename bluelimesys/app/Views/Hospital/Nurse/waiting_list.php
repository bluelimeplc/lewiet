<?php
    $company_header = $this->data['company_header'] ?? [];
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
            <h1>Nurse</h1>
            <small>Waiting List</small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/nurse/index">Dashboard</a></li>
            </ol>
        </div>
    </section>
    <section class="content">
        <?php
            $attributes = array('role'=>'form');
            helper('form');
            echo form_open_multipart('/hospital/nurse/waitinglist', $attributes);
        ?>

        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd">
                    <div class="panel-body">
                        <div class="form-horizontal" role="form">
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <input id="search_waiting_list" type="text" name="search_waiting_list" class="form-control" value="<?= htmlspecialchars($searchkey, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search patient name, mobile or card">
                                </div>
                                <div class="col-sm-2">
                                    <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                        <?php echo form_close(); ?>

            <div class="col-md-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Patient Waiting List</h4>
                        </div>
                    </div>
                    <div class="panel-body table-responsive">
                        <table width="100%" class="table table-striped table-bordered table-hover dt-responsive" id="nurse_waiting_list">
                            <thead>
                                <tr>
                                    <th>W.No</th>
                                    <th class="all">Patient Name</th>
                                    <th class="all">Card No</th>
                                    <th class="all">Phone Number</th>
                                    <th class="all">Doctor</th>
                                    <th class="all">Time</th>
                                    <th class="all">Priority</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($waiting_list as $patient) { ?>
                                    <tr>
                                        <td>
                                            <span><?php echo $patient['waiting_number'] ?></span>
                                            <?php
                                                $active_count_for_doc = $this->data['doctor_active_counts'][$patient['doctor_id']] ?? 0;
                                                $patients_before = max(0, ($patient['waiting_number'] ?? 1) - 1);
                                                $number_before_you = $active_count_for_doc + $patients_before;
                                            ?>
                                            <button type="button" class="btn btn-warning btn-xs" style="margin-left:6px;" onclick="showPriorityModal(<?php echo $patient['id'] ?>, '<?php echo htmlspecialchars($patient['priority_reason'] ?? '', ENT_QUOTES, 'UTF-8') ?>')">
                                                <i class="fa fa-star"></i>
                                            </button>
                                            <button type="button" class="btn btn-info btn-xs" style="margin-left:6px;" onclick="showQueuePrintModal(<?php echo $patient['waiting_number'] ?>, '<?php echo htmlspecialchars($patient['full_name'], ENT_QUOTES, 'UTF-8') ?>', '<?php echo htmlspecialchars($patient['doctor_name'], ENT_QUOTES, 'UTF-8') ?>', '<?php echo $patient['patient_id'] ?? '' ?>', '<?php echo $patient['mobile'] ?? '' ?>', '<?php echo date('Y-m-d H:i', strtotime($patient['created_at'])) ?>', 0, <?php echo $active_count_for_doc ?>, <?php echo $patients_before ?>)">
                                                <i class="fa fa-print"></i>
                                            </button>
                                        </td>
                                        <td><?php echo esc($patient['full_name']) ?></td>
                                        <td><?php echo esc($patient['patient_id']) ?></td>
                                        <td><?php echo esc($patient['mobile']) ?></td>
                                        <td><?php echo esc($patient['doctor_name']) ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($patient['created_at'])); ?></td>
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
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<div class="modal fade" id="printWaitingNumber" tabindex="-1" role="dialog" aria-labelledby="PrintWaitingNumber" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-body" style="color: black;">
            <div id="printWaitingNumberArea">
                <div>
                    <?php if (!empty($company_header['logo_printouts'])): ?>
                        <?php echo '<img width="100" style="margin-bottom:10px" src="data:image/jpeg;base64,'.base64_encode($company_header['logo_printouts']).'"/>'; ?>
                    <?php endif; ?>
                    <span style="font-size:11px; float:right; border-left: 1px solid black; padding-left: 12px; height: 60px;">
                        <table>
                            <tr><td>Mob.</td><td><?= esc($company_header['mobile'] ?? '') ?></td></tr>
                            <tr><td></td><td></td></tr>
                            <tr><td>Tel.</td><td><?= esc($company_header['tel'] ?? '') ?></td></tr>
                            <tr><td colspan="2" style="font-size:9px;"><?= esc($company_header['website'] ?? '') ?></td></tr>
                            <tr><td colspan="2" style="font-size:9px;"><?= esc($company_header['email'] ?? '') ?></td></tr>
                        </table>
                    </span>
                </div>
                <hr style="border-width: 2px;">
                <div class="container header">
                    <div class="row details-content">
                        <div class="single-header-info">
                            <span style="font-size:12px;"><strong>Patient Name: </strong><span id="queue_patient_name"></span></span><br>
                            <span style="font-size:12px;"><strong>Card No: </strong><span id="queue_mrn_text"></span></span><br>
                            <span style="font-size:12px;"><strong>Phone Number: </strong><span id="queue_phone_text"></span></span><br>
                            <span style="font-size:12px;"><strong>Assigned Doctor: </strong><span id="queue_doctor_name"></span></span><br>
                            <span style="font-size:12px;"><strong>Visit Date: </strong><span id="queue_visit_date_text"></span></span><br>
                            <span style="font-size:12px;"><strong>Patients before you: </strong>
                                <span id="queue_active_count" class="label label-info" style="margin-right:6px;">0 active</span>
                                <span id="queue_waiting_ahead" class="label label-warning" style="margin-right:6px;">0 waiting</span>
                                <span id="queue_total_count" class="label label-success">0 total</span>
                            </span><br>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row details-content">
                        <div style="width: 80mm;">
                            <div class="table-responsive marg"><span style="font-size:14px;"><div class="col-sm-4 col-md-4"><strong>Queue No</strong></div></span>
                            </div>
                            <div class="col-sm-4 col-md-4" align="center" style="border-width:2px; text-align:center; font-weight:bold; font-size:70px; text-align:text-center; width:120px">
                                <p style="border-style: dotted; margin:0; padding:10px;"><span id="queue_number_text"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <span style="font-size: smaller; ">Printed on <?= date('Y-m-d H:i') ?></span>
            <hr>
            <p style="font-size:8px; text-align: center;">Powered by Bluelime Technologies<br>www.bluelimeplc.com</p>
        </div>
        <div class="modal-footer col-md-12">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
            <button type="button" class="btn btn-success" onclick="printContent('printWaitingNumberArea')">Print</button>
        </div>
    </div>
</div>

<div class="modal fade" id="priorityModal" tabindex="-1" role="dialog" aria-labelledby="PriorityModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="/hospital/nurse/prioritize_waiting" method="post">
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
                            <option value="emergency">Emergency</option>
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
    function showPriorityModal(wlid, currentReason) {
        $('#priority_wlid').val(wlid);
        $('#priority_reason').val(currentReason);
        $('#priorityModal').modal('show');
    }

    function showQueuePrintModal(queueNumber, patientName, doctorName, mrn, phone, visitDate, estimatedMinutes, activeCount, waitingAhead) {
        $('#queue_number_text').text(queueNumber);
        $('#queue_patient_name').text(patientName);
        $('#queue_doctor_name').text(doctorName);
        $('#queue_mrn_text').text(mrn ? 'MRN-' + String(mrn).padStart(5, '0') : '');
        $('#queue_phone_text').text(phone);
        $('#queue_visit_date_text').text(visitDate);
        activeCount = parseInt(activeCount) || 0;
        waitingAhead = parseInt(waitingAhead) || 0;
        var total = activeCount + waitingAhead;
        $('#queue_active_count').text(activeCount + ' active');
        $('#queue_waiting_ahead').text(waitingAhead + ' waiting');
        $('#queue_total_count').text(total + ' total');
        var estText = formatMinutesHuman(estimatedMinutes || 0);
        $('#queue_estimated_wait_time').text(estText);
        $('#printWaitingNumber').modal('show');
    }

    function printContent(el) {
        var restorepage  = $('body').html();
        var printcontent = $('#' + el).clone();
        $('body').empty().html(printcontent);
        window.print();
        $('body').html(restorepage);
        location.reload();
    }
</script>

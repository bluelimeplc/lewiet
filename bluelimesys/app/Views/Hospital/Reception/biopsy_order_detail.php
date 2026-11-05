<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<?php
            helper('form');
            // Safely decode additional_info JSON string to array
            $additional_info = [];
            if (isset($order_main['additional_info'])) {
                if (is_array($order_main['additional_info'])) {
                    $additional_info = $order_main['additional_info'];
                } elseif (is_string($order_main['additional_info']) && !empty(trim($order_main['additional_info']))) {
                    $decoded = json_decode($order_main['additional_info'], true);
                    if (is_array($decoded)) {
                        $additional_info = $decoded;
                    }
                }
            }
            $order_main['additional_info'] = $additional_info;
        ?>
<script src="<?php echo base_url()?>assets/plugins/summernote/summernote.min.js" type="text/javascript"></script>
<link href="<?php echo base_url()?>assets/plugins/summernote/summernote.css" rel="stylesheet" type="text/css"/>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
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

        .jump {
            animation: jump 1.5s ease 0s normal;
            color: orange;
        }

        .dropZoneOverlay, .FileUpload {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        .dropZoneOverlay {
            color: #7066fb;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            background: transparent;
        }

        .FileUpload {
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

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

        .status-label {
            font-size: 11px;
            font-weight: 600;
            color: #495057;
            text-align: center;
            max-width: 100px;
            word-wrap: break-word;
        }

        .form-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .form-card .card-header {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 15px;
        }

        .form-card .card-header h5 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            font-size: 16px;
        }

        .form-card .card-header i {
            margin-right: 8px;
            font-size: 1.1em;
        }

        .form-card .card-body {
            padding: 15px;
        }

        .form-card .card-footer {
            background: #f8f9fa;
            border: none;
            padding: 12px 15px;
            text-align: right;
        }

        .form-card .card-footer {
            background: #f8f9fa;
            border: none;
            padding: 20px 30px;
            text-align: right;
        }

        .form-group-modern {
            margin-bottom: 15px;
        }

        .form-group-modern label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
            display: block;
            font-size: 14px;
        }

        .urgent-checkbox {
            
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 28px;
        }

        .urgent-checkbox input[type="checkbox"] {
            scale: 2.5;
            accent-color: #dc3545;
            margin: 0 0 0 10px;
        }

        .urgent-checkbox label {
            color: #dc3545;
            font-weight: 1000;
            font-size: 16px;
            margin-bottom: 0;
        }

        .form-control {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 6px 12px;
            font-size: 14px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .clinical-textarea {
            resize: none;
            min-height: 52px;
            max-height: 100px;
            overflow: auto;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .input-group-modern {
            display: flex;
            gap: 10px;
        }

        .input-group-modern .form-group {
            flex: 1;
        }

        .section-divider {
            height: 1px;
            background: #dee2e6;
            margin: 20px 0;
        }

        .btn-modern {
            border-radius: 4px;
            padding: 8px 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .btn-modern:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .action-buttons {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .upload-section {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            min-height: 120px;
        }

        .upload-section:hover {
            border-color: #007bff;
            background: #e9ecef;
        }

        .uploaded-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .uploaded-image {
            position: relative;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .uploaded-image .delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 10;
        }

        .uploaded-image:hover .delete-btn {
            opacity: 1;
        }

        .uploaded-image img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .uploaded-image:hover img {
            transform: scale(1.05);
        }

        .required-field::after {
            content: '*';
            color: #dc3545;
            margin-left: 4px;
        }

        .is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .request-form {
            width: 100%;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }

        .request-form table {
            width: 100% !important;
            table-layout: fixed;
            font-size: 11px;
            margin: 0 !important;
        }

        .request-form th,
        .request-form td {
            word-wrap: break-word;
            overflow-wrap: break-word;
            padding: 4px;
            vertical-align: top;
        }

        .request-form .full-width {
            width: 100% !important;
        }

        .request-form .checkbox-cell {
            text-align: center;
            white-space: nowrap;
        }

        .request-form .print-block {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .request-form .row {
            margin: 0 !important;
        }

        .request-form .row > div {
            padding: 0 !important;
        }
    </style>        

<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo lang('Reception');?></h1>
            <small><?php echo lang('Biopsy Order Detail');?></small>
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

            foreach ($statuses as $status => $data) {
                $isActive = $order_main['order_status'] >= $status;
                $class = $isActive ? 'active' : 'inactive';
                echo '<div class="status-step">';
                echo '<div class="status-icon ' . $class . '">';
                echo '<i class="fas ' . $data['icon'] . '"></i>';
                echo '</div>';
                echo '<div class="status-label">' . $data['label'] . '</div>';
                if ($status == 6 || $status == 7) {
                    $viewLink = '/hospital/reception/viewbiopsyresult?brid=' . ($biopsy_results['id'] ?? 0) . '&status=' . $order_main['order_status'] . '&order_id=' . $order_main['id'];
                    if ($order_main['order_status'] == $status) {
                        echo '<a href="' . $viewLink . '" class="btn btn-sm btn-success mt-2"><i class="fa fa-eye"></i></a>';
                    }
                }
                echo '</div>';
            }
            ?>
        </div> 
        
        <div class="row">
            <!-- Patient Information Card -->
            <div class="col-md-7">
                <div class="form-card">
                    <div class="card-header">
                        <h5><i class="fas fa-user"></i> Patient Information</h5>
                    </div>
                    <div class="panel panel-bd">
                    <div class="panel-body">
                        <!-- Full Name Section -->
                         <?php
                            $name = BluelimeSystem::split_full_name($order_main['patient_name']);
                         ?>
                        <div class="form-group-modern">
                            <label class="required-field">Full Name</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" required value="<?=$name['first_name'] ?>" name="first_name" id="first_name" class="form-control" placeholder="First Name">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" required value="<?=$name['middle_name'] ?>" name="middle_name" id="middle_name" class="form-control" placeholder="Father Name">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" value="<?=$name['last_name'] ?>" name="last_name" id="last_name" class="form-control" placeholder="Grand Father Name">
                                </div>
                            </div>
                        </div>

                        <!-- Age and DOB Section -->
                        <div class="form-group-modern">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="required-field">Age</label>
                                    <?php
                                    $patient_dob = isset($order_main['date_of_birth']) ? $order_main['date_of_birth']:'';
                                    $diff = date_diff(date_create($patient_dob), date_create(date("Y-m-d")));
                                    $age = $diff->format('%y');
                                    ?>
                                    <input class="form-control" name="old" id="age" min="0" step="0.01" max="100" type="number" required placeholder="Age" onload="getdob(this.value)" oninput="getdob(this.value)" value="<?php echo $age ?? "" ?>">
                                </div>
                                <div class="col-md-5">
                                    <label>Date of Birth</label>
                                    <input tabindex="-1" type="date" id="dob" name="date_of_birth" class="form-control" placeholder="Select Date" onload="getAge(this.value)" onchange="getAge(this.value)" value="<?php echo $order_main['date_of_birth'] ?? "" ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="required-field">Sex</label>
                                    <select name="sex" id="sex" class="form-control" required>
                                        <option value="">--Select Sex--</option>
                                        <option value="Female" <?php echo (@$order_main['sex']=='Female')?'selected':'' ?>>Female</option>
                                        <option value="Male" <?php echo (@$order_main['sex']=='Male')?'selected':'' ?>>Male</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div class="form-group-modern">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="required-field">Mobile Number</label>
                                    <input type="tel" value="<?= @$order_main['mobile']?>" placeholder="09xxxxxxxx" name="mobile" id="patient_mobile_input" required class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>Email</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Email Address" value="<?= @$order_main['additional_info']['email'] ?? '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="section-divider"></div>
                        <!-- Medical Information Section -->
                        <div class="form-group-modern">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Referring Institution</label>
                                    <input value="<?= (!empty($order_main['additional_info']['referring_institute'])?$order_main['additional_info']['referring_institute']:$biopsy_request['refering_institution']??"") ?? '' ?>" type="text" placeholder="Referring Institution" name="refering_institution" id="refering_institution" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>MRN</label>
                                    <input type="text" name="mrn" id="mrn" class="form-control" placeholder="Medical Record Number" value="<?= @$order_main['additional_info']['mrn'] ?? '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group-modern">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Previous Histology No</label>
                                    <input value="<?= @$order_main['additional_info']['previous_histology_no'] ?? '' ?>" type="text" placeholder="Previous Histology No" name="previous_histology_no" id="previous_histology_no" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>&nbsp</label>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Requesting Physician</label>
                                    <input type="text" name="requesting_physician" id="requesting_physician" class="form-control" placeholder="Physician Name" value="<?= (!empty($order_main['additional_info']['requesting_physician'])?$order_main['additional_info']['requesting_physician']:BluelimeSystem::convert_to_array($biopsy_request['description']??"")['bio_request_doctor_name']??"") ?? '' ?>">
                                </div>
                                <div class="col-md-6">
                                    <label>Surgeon</label>
                                    <input type="text" name="surgeon" id="surgeon" class="form-control" placeholder="Surgeon Name" value="<?= (!empty($order_main['additional_info']['surgeon'])?$order_main['additional_info']['surgeon']:$biopsy_request['registered_by_name']??"") ?? '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Physician Mobile</label>
                                    <input type="tel" name="physician_mobile" id="physician_mobile" class="form-control" placeholder="Physician Mobile" value="<?= @$order_main['additional_info']['physician_mobile'] ?? '' ?>">
                                </div>
                                <div class="col-md-6">
                                    <label>Surgeon Mobile</label>
                                    <input type="tel" name="surgeon_mobile" id="surgeon_mobile" class="form-control" placeholder="Surgeon Mobile" value="<?= @$order_main['additional_info']['surgeon_mobile'] ?? '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        <!-- Clinical Information Section -->
                        <div class="form-group-modern">
                            <label>Clinical Information</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <textarea name="history" id="history" class="form-control clinical-textarea" rows="2" placeholder="History"><?= (!empty($order_main['additional_info']['history'])?$order_main['additional_info']['history']:BluelimeSystem::convert_to_array($biopsy_request['description']??"")['bio_request_history']??"") ?? '' ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <textarea name="physical_findings" id="physical_findings" class="form-control clinical-textarea" rows="2" placeholder="Physical Findings"><?= (!empty($order_main['additional_info']['physical_fingings'])?$order_main['physical_fingings']['history']:BluelimeSystem::convert_to_array($biopsy_request['description']??"")['bio_request_physical_finding']??"") ?? '' ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <div class="row">
                                <div class="col-md-6">
                                    <textarea name="clinical_diagnosis" id="clinical_diagnosis" class="form-control clinical-textarea" rows="2" placeholder="Clinical Diagnosis/Differential Diagnosis"><?= (!empty($order_main['additional_info']['clinical_diagnosis'])?$order_main['additional_info']['clinical_diagnosis']:BluelimeSystem::convert_to_array($biopsy_request['description']??"")['bio_request_diagnosis']??"") ?? '' ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <textarea name="laboratory_information" id="laboratory_information" class="form-control clinical-textarea" rows="2" placeholder="Laboratory/Imaging Information"><?= @$order_main['additional_info']['laboratory_information'] ?? '' ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php
                            $specimen_order = @$order_main['additional_info']['specimen'];
                            $specimen_request = $biopsy_request['specimen'] ?? "";
                            $selected_specimen = $specimen_order ?: $specimen_request;
                            $bio_urgent_flag = !empty(BluelimeSystem::convert_to_array($biopsy_request['description'] ?? '')['bio_urgent']) 
                                && BluelimeSystem::convert_to_array($biopsy_request['description'] ?? '')['bio_urgent'] == 1;
                            $urgent_specimen_checked = (!empty($order_main['additional_info']['specimen_urgent']) || $bio_urgent_flag) ? 'checked' : '';
                        ?>
                        <div class="form-group-modern">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Specimen</label>
                                    <select name="specimen" id="specimen" class="form-control">
                                        <option value="">--Select Specimen--</option>
                                        <option value="Skin biopsy"  <?= ($selected_specimen == 'Skin biopsy') ? 'selected' : '' ?>>Skin biopsy</option>
                                        <option value="Other"  <?= ($selected_specimen == 'Other') ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="urgent-checkbox">
                                        <input type="checkbox" id="urgent_specimen" name="urgent_specimen" value="1" <?= $urgent_specimen_checked ?> />
                                        <label for="urgent_specimen">Urgent</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        <!-- Biopsy Information Section -->
                        <div class="form-group-modern">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="required-field">Biopsy Type</label>
                                    <?php
                                    $biopsy_type_order = @$order_main['additional_info']['biopsy_type'];
                                    $biopsy_type_request = BluelimeSystem::convert_to_array($biopsy_request['description'] ?? "")['biopsy_type'] ?? "";
                                    $selected_biopsy_type = $biopsy_type_order ?: $biopsy_type_request;
                                    $anesthesia_order = @$order_main['additional_info']['anesthesia'];
                                    $anesthesia_request = BluelimeSystem::convert_to_array($biopsy_request['description'] ?? "")['bio_request_anesthesia'] ?? "";
                                    $selected_anesthesia = $anesthesia_order ?: $anesthesia_request;
                                    $fixative_order = @$order_main['additional_info']['fixative'];
                                    $fixative_request = BluelimeSystem::convert_to_array($biopsy_request['description'] ?? "")['bio_request_fixative'] ?? "";
                                    $selected_fixative = $fixative_order ?: $fixative_request;
                                    $size_order = @$order_main['additional_info']['size'];
                                    $size_request = BluelimeSystem::convert_to_array($biopsy_request['description'] ?? "")['biopsy_size'] ?? "";
                                    $selected_size = $size_order ?: $size_request;
                                    
                                    
                                    ?>
                                    <select name="biopsy_type" id="biopsy_type" class="form-control" required onchange="toggleSizeField()">
                                        <option value="">--Select Biopsy Type--</option>
                                        <optgroup label="Grade 1">
                                        <option value="Grade 1 Punch biopsy" <?= ($selected_biopsy_type == 'Grade 1 Punch biopsy') ? 'selected' : '' ?>>Punch biopsy</option>
                                        <option value="Grade 1 Incisional biopsy" <?= ($selected_biopsy_type == 'Grade 1 Incisional biopsy') ? 'selected' : '' ?>>Incisional biopsy</option>
                                        <option value="Grade 1 Wedge biopsy" <?= ($selected_biopsy_type == 'Grade 1 Wedge biopsy') ? 'selected' : '' ?>>Wedge biopsy</option>
                                        <option value="Grade 1 Shave biopsy" <?= ($selected_biopsy_type == 'Grade 1 Shave biopsy') ? 'selected' : '' ?>>Shave biopsy</option>
                                        <option value="Grade 1 Curettage" <?= ($selected_biopsy_type == 'Grade 1 Curettage') ? 'selected' : '' ?>>Curettage</option>
                                        <option value="Grade 1 Electrosurgical" <?= ($selected_biopsy_type == 'Grade 1 Electrosurgical') ? 'selected' : '' ?>>Electrosurgical</option>
                                        <option value="Grade 1 Snip or scissors biopsy" <?= ($selected_biopsy_type == 'Grade 1 Snip or scissors biopsy') ? 'selected' : '' ?>>Snip or scissors biopsy</option>
                                        <option value="Grade 1 Small excisional biopsy" <?= ($selected_biopsy_type == 'Grade 1 Small excisional biopsy') ? 'selected' : '' ?>>Small excisional biopsy</option>
                                        </optgroup>
                                        <optgroup label="Grade 2">
                                        <option value="Grade 2 Excisional biopsy" <?= ($selected_biopsy_type == 'Grade 2 Excisional biopsy') ? 'selected' : '' ?>>Excisional biopsy</option>
                                        </optgroup>
                                        <option value="N/A" <?= ($selected_biopsy_type == 'N/A') ? 'selected' : '' ?>>N/A</option>
                                        
                                    </select>
                                    <div id="size_container" style="<?= ($selected_biopsy_type == 'Grade 1 Punch biopsy') ? 'display: block; margin-top: 10px;' : 'display: none; margin-top: 10px;' ?>">
                                        <label>Size</label>
                                        <input type="text" name="size" id="size" class="form-control" placeholder="Size" value="<?= ($selected_biopsy_type == 'Grade 1 Punch biopsy') ? $selected_size : '' ?>" <?= ($selected_biopsy_type == 'Grade 1 Punch biopsy') ? 'required' : '' ?> >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="required-field">Site of Biopsy</label>
                                    <input required type="text" name="site_of_biopsy" id="site_of_biopsy" class="form-control" placeholder="Site of Biopsy" value="<?= (!empty($order_main['additional_info']['site_of_biopsy'])?$order_main['additional_info']['site_of_biopsy']:BluelimeSystem::convert_to_array($biopsy_request['description']??"")['bio_request_site']??"") ?? '' ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="required-field">Anesthesia</label>
                                    <select name="anesthesia" id="anesthesia" class="form-control" required>
                                        <option value="">--Select Anesthesia--</option>
                                        <option value="Local" <?= ($selected_anesthesia == 'Local') ? 'selected' : '' ?>>Local</option>
                                        <option value="General" <?= ($selected_anesthesia == 'General') ? 'selected' : '' ?>>General</option>
                                        <option value="Topical" <?= ($selected_anesthesia == 'Topical') ? 'selected' : '' ?>>Topical</option>
                                        <option value="Other" <?= ($selected_anesthesia == 'Other') ? 'selected' : '' ?>>Other</option>
                                        <option value="N/A" <?= ($selected_anesthesia == 'N/A') ? 'selected' : '' ?>>N/A</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="required-field">Fixative</label>
                                    <select name="fixative" id="fixative" class="form-control" required>
                                        <option value="">--Select Fixative--</option>
                                        <option value="Formalin 10%" <?= ($selected_fixative == 'Formalin 10%') ? 'selected' : '' ?>>Formalin 10%</option>
                                        <option value="Formalin 8%" <?= ($selected_fixative == 'Formalin 8%') ? 'selected' : '' ?>>Formalin 8%</option>
                                        <option value="Other" <?= ($selected_fixative == 'Other') ? 'selected' : '' ?>>Other</option>
                                        <option value="N/A" <?= ($selected_fixative == 'N/A') ? 'selected' : '' ?>>N/A</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Sample Taken Date</label>
                                    <input type="date" name="sample_taken_date" id="sample_taken_date" class="form-control" placeholder="Sample Taken Date" value="<?= @$order_main['additional_info']['sample_taken_date'] ?? '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="panel-footer text-right">
                        <button class="btn btn-modern btn-success" type="button" onclick="save_info()">
                            <i class="fas fa-save"></i> Save Information
                        </button>
                    </div>
                </div>
                </div>
            </div>
            
            <!-- Actions and Documents Sidebar -->
            <div class="col-md-5">
                <!-- Action Buttons Card -->
                <div class="form-card">
                    <div class="card-header">
                        <h5><i class="fas fa-tasks"></i> Actions</h5>
                    </div>
                    <div class="card-body action-buttons">
                        <?php
                        if($order_main['order_status'] == 1){
                            echo '<button class="btn btn-modern btn-success" title="Collect Sample" onclick="checkdocumentattached()"><i class="fas fa-vial"></i> Collect Sample</button>';
                        }
                        elseif($order_main['order_status'] == 2){
                            echo '<button class="btn btn-modern btn-success" title="Send Sample" onclick="send_sample()"><i class="fas fa-paper-plane"></i> Send Sample</button>';
                            echo '<a href="/hospital/reception/reverse_sample_status?order_id='.$order_main['id'].'" class="btn btn-modern btn-warning" title="Revert"><i class="fas fa-undo"></i> Revert</a>';
                        }
                        elseif($order_main['order_status'] == 3){
                            echo '<button class="btn btn-modern btn-danger" title="Receive Slide" onclick="show_slidenote_modal()"><i class="fas fa-microscope"></i> Receive Slide</button>';
                            echo '<a href="/hospital/reception/reverse_sample_status?order_id='.$order_main['id'].'" class="btn btn-modern btn-warning" title="Revert"><i class="fas fa-undo"></i> Revert</a>';
                        }
                        elseif($order_main['order_status'] == 4){
                            echo '<a href="/hospital/reception/reverse_sample_status?order_id='.$order_main['id'].'" class="btn btn-modern btn-warning" title="Revert"><i class="fas fa-undo"></i> Revert</a>';
                        }
                        else{
                            echo '<button class="btn btn-modern btn-secondary" disabled title="Revert"><i class="fas fa-undo"></i> Revert</button>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Document Upload Card -->
                 
                <div class="form-card">
                    <div class="card-header">
                        <h5><i class="fas fa-file-upload"></i> Document Upload</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        helper('form');
                        $attributes = array('class' => 'form-horizontal','role'=>'form', 'enctype' => 'multipart/form-data');
                        echo form_open('hospital/reception/uploadbiopsyrequest', $attributes);
                        ?>
                        <div class="upload-section">
                            <input type="file" id="drop_zone" class="FileUpload" accept="image/*" onchange="this.form.submit()" name="picture" title="Upload Request" data-toggle="tooltip" data-html="true">
                            <input type="hidden" value="<?=$_GET['order_main_id']?>" name="order_id">
                            <div class="dropZoneOverlay">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 20px; display: block; margin-bottom: 8px;"></i>
                                <strong style="font-size: 14px;">Upload Document</strong>
                                <br>
                                <small style="font-size: 12px;">Click to select or drag & drop</small>
                            </div>
                            <div hidden><input type="submit"></div>
                        </div>

                        <?php if(!empty($order_main['scanned_documents'])): ?>
                        <div class="uploaded-images">
                            <?php
                            $pathology_image = explode(',',$order_main['scanned_documents']);
                            $index = 0;
                            foreach($pathology_image as $image_name):
                            ?>
                            <div class="uploaded-image">
                                <a href="/image?image=<?=$image_name?>" data-image_name="<?=$image_name?>" onclick="openImagePreview(<?=$index?>, '<?=$image_name?>'); return false;">
                                    <img src="/image?image=<?=$image_name?>&thumbnail=1" alt="Document">
                                </a>
                                <button type="button" class="btn btn-danger btn-sm delete-btn" onclick="deleteImage('<?=$image_name?>')" title="Delete Image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <?php $index++; endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php echo form_close(); ?>
                    </div>
                    
                </div>
                <?php if(!empty($biopsy_request)): ?>
                <div class="form-card">
                        <div class="card-header">
                            <h5 class="card-title">Biopsy Request Details</h5>
                        </div>
                        <div class="panel panel-bd">
                        <div class="card-body" style="padding: 0;">
                        <div class="request-form">


                        <?php
                $print_data = [
                    'document_no' => sprintf('%05d',  $biopsy_request['id'] ),
                    'document_title' => 'External Biopsy Request',
                    'document_date' => date('Y-m-d', strtotime($biopsy_request['created_at'])),
                    
                ];
            ?>
            <?= view('Include/_print_header_A6_Portrait', $print_data) ?>
            <table class="table table-condensed table-bordered full-width" style="margin-top: 5px; font-size: smaller; margin-bottom: 2px;">
                <tr style="border: none">
                    <td style="border: none" align="right"><b>Patient Name</b></td><td style="border: none"><?= $biopsy_request['patient_detail']['first_name'].' '.$biopsy_request['patient_detail']['middle_name'].' '.$biopsy_request['patient_detail']['last_name'] ?></td>
                    <td align="right" style="border: none"><b>Card Number</b></td><td style="border: none">MRN-<?= sprintf('%05d', $biopsy_request['patient_id'] ) ?></td>
                </tr>
                <tr>
                    <td align="right" style="border: none"><b>Age</b></td><td style="border: none"><?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$biopsy_request['patient_detail']['date_of_birth'])."Y "?><?=BluelimeSystem::month_difference(BluelimeSystem::date_now(),$biopsy_request['patient_detail']['date_of_birth'])."M"?></td>
                    <td align="right" style="border: none"><b>Sex</b></td><td style="border: none"><?= $biopsy_request['patient_detail']['sex'] ?></td>
                </tr>
            </table><br>

                <div style="min-height: 300px; font-size: 11px; padding: 0;">
                    <div class="print-block" style="width: 100%; padding: 0; margin: 0;">
                        <div style="width: 100%; padding: 0; margin: 0;">
                            <div style="width: 100%; padding: 0; margin: 0;">
                                <strong>
                                        <table class="table table-bordered full-width">
                                                        <tr>
                                                            <td style="width: 35%;"><b>Hospital/Health Center:</b></td>
                                                            <td colspan="4" style="width: 65%;">
                                                                <?=$biopsy_request['requested_company_name']?>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                        <td><b>Requesting Physician:</b></td>   
                                                        <td colspan="4">
                                                        <?= BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_request_doctor_name'] ?>
                                                                                </td>
                                                        </tr>
                                                        <tr>
                                                        <td><b>Surgeon:</b></td>   
                                                        <td colspan="4">
                                                        <?= $biopsy_request['registered_by_name'] ?>
                                                                                </td>
                                                        </tr>
                                                        <tr>
                                                        <td><b>Sampling Date:</b></td>   
                                                        <td colspan="4">
                                                        <?= $biopsy_request['created_at'] ?>                      
                                                                                </td>
                                                        </tr>
                                                        <?php if(BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_urgent']??-1 == 1): ?>
                                                        <tr>
                                                        <td><b style="color: red;">URGENT</b></td>   
                                                        <td colspan="4" style="color: red; font-weight: bold;">
                                                        This is an urgent biopsy request.                      
                                                                                </td>
                                                        </tr>
                                                        <?php endif; ?>
                                                </table><br>
                                                <table class="table table-bordered full-width">
                                                        <tr>
                                                            <th style="width: 150px;">History </th>
                                                            <td style="padding: 3px;">
                                                            <?= BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_request_history'] ?></td>
                                                        </tr>
                                                            
                                                        <tr>
                                                            <th style="width: 150px;">Physical Finding </th>
                                                            <td style="padding: 3px;">
                                                            <?= BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_request_physical_finding'] ?></td>
                                                        </tr>
                                                            
                                                        <tr>
                                                            <th style="width: 150px;">Clinical Diagnosis </th>
                                                            <td style="padding: 3px;">
                                                            <?= BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_request_diagnosis'] ?></td>
                                                        </tr>
                                                            
                                                        <tr>
                                                            <th style="width: 150px;">Laboratory & imaging Information </th>
                                                            <td style="padding: 3px;">
                                                            <?= BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_request_imaging'] ?></td>
                                                        </tr>
                                                </table><br>
                                                <table class="table table-bordered full-width">
                                                    <thead>
                                                        <tr class="bg-info">
                                                            <th colspan="6">Biopsy Type</th>
                                                        </tr>
                                                        <?php
                                                            if(empty(BluelimeSystem::convert_to_array($biopsy_request['description'])['biopsy_type'])){
                                                            
                                                        ?>
                                                        <tr>
                                                            <th>Punch</th>
                                                            <th>Incision</th>
                                                            <th>Excision</th>
                                                            <th>Shave</th>
                                                            <th>Curettage</th>
                                                            <th>Other</th>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                            <input style="transform: scale(0.8);"  type="checkbox" onclick="return false;" <?=BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_request_punch']!=""?"checked":"" ?> class="form-control"  id="bio_request_punch" />
                                                            </td>
                                                            <td>
                                                            <input style="transform: scale(0.8);"  style="transform: scale(0.8);"  type="checkbox" onclick="return false;" <?=BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_request_incision']!=""?"checked":"" ?>  class="form-control"  id="bio_request_incision"/>
                                                            </td>
                                                            <td>
                                                            <input style="transform: scale(0.8);"  type="checkbox" onclick="return false;" <?=BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_request_excision']!=""?"checked":"" ?>  class="form-control" id="bio_request_excision"/>
                                                            </td><td>
                                                            <input style="transform: scale(0.8);"  type="checkbox" onclick="return false;" <?=BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_request_excision']!=""?"checked":"" ?>  class="form-control" id="bio_request_shave"/>
                                                            </td><td>
                                                            <input style="transform: scale(0.8);"  type="checkbox" onclick="return false;" <?=BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_request_curettage']!=""?"checked":"" ?>  class="form-control" id="bio_request_curettage"/>
                                                            </td><td>
                                                            <?=BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_request_other']?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                            }else{
                                                        ?>
                                                        <tr>
                                                            <td style="font-size: 14px;" colspan="6">
                                                                <br><?=BluelimeSystem::convert_to_array($biopsy_request['description'])['biopsy_type']. " ". BluelimeSystem::convert_to_array($biopsy_request['description'])['biopsy_size'] ?>
                                                            </td>
                                                          </tr>
                                                          <?php
                                                            }
                                                            ?> 
                                                        
                                                        </thead>
                                                    </table><br>
                                                    <table class="table table-bordered full-width">
                                                        <tr>
                                                            <th>Biopsy Site</th>
                                                            <th>Anesthesia</th>
                                                            <th>Fixative</th>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                            <?=BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_request_site']?>
                                                            </td>
                                                            <td>
                                                            <?=BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_request_anesthesia']?>
                                                            </td>
                                                            <td>
                                                            <?=BluelimeSystem::convert_to_array($biopsy_request['description'])['bio_request_fixative']?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                        

                                    
                                </strong>
                            
                        </div>
                        </div>
                <div style="text-align:center;float:right;margin-top:10px;bottom: 100px;margin-right: 50px;">
                    <?php 
                        //BluelimeSystem::show_and_die($doc_id.$medical_certificate[0]['registerd_by']);
                        echo '<img style="height:60px;" src="data:image/jpeg;base64,'.base64_encode(  $doctor['digital_signature'] ?? '' ).'"/>' 
                    ?><br>
                        <u><?= $biopsy_request['registered_by_name'] ?? '' ?></u><br>
                        <?= $doctor['title'] ?? '' ?><br>
                        <b>Doctor's Signature<br></b>
                </div>
                        
                        
                    </div>
                    
                    
                    
                    
                </div>
                
                
               
                </div>


                        
                        
                        </div>
                        </div>
                        </div>
                </div>
                        <?php endif; ?>
            </div>
        </div>
       
        
    </section>
</div>

        <!-- Slide Note Modal -->
        <div class="modal fade" id="slidenote" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background: #007bff; color: white; border: none;">
                        <h5 class="modal-title"><i class="fas fa-microscope"></i> Slide Information</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="padding: 15px;">
                        <input type="hidden" name="visit_id" value="<?= $visit_id??0 ?>">
                        <div class="form-group-modern">
                            <label class="required-field" style="color: #dc3545;"><i class="fas fa-hashtag"></i> Histology Number</label>
                            <textarea class="form-control" id="slide" name="slide" required rows="3" placeholder="Enter histology number and any additional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" style="border: none; padding: 12px 15px;">
                        <button type="button" class="btn btn-secondary btn-modern" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button class="btn btn-success btn-modern" type="button" onclick="recieve_slide()">
                            <i class="fas fa-check"></i> Receive Slide
                        </button>
                    </div>
                </div>
            </div>
        </div>
<script>
    $(document).ready(function() {
        let scannedDocuments = "<?=$order_main['scanned_documents']; ?>";
        let biopsyRequestExists = "<?php echo !empty($biopsy_request) ? 'true' : 'false'; ?>";
        
        if (scannedDocuments.trim() === '' && biopsyRequestExists === 'true') {
            // Capture the biopsy request details as image
            html2canvas(document.querySelector('.request-form'), { scale: 2, useCORS: true, allowTaint: true }).then(function(canvas) {
                canvas.toBlob(function(blob) {
                    let formData = new FormData();
                    formData.append('picture', blob, 'biopsy_request_details.png');
                    formData.append('order_id', <?=$order_main['id']?>);
                    
                    $.ajax({
                        url: '/hospital/reception/uploadbiopsyrequest',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log('Biopsy request details uploaded successfully');
                            // Optionally reload the page to show the uploaded image
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            console.error('Error uploading biopsy request details:', error);
                        }
                    });
                });
            });
        }
    });

    let images = <?php echo json_encode(array_filter(array_map('trim', explode(',', $order_main['scanned_documents'])), function($name) { return !empty($name); })); ?>;
    let currentIndex = 0;
    let currentScale = 1;
    let currentImageName = '';

    function show_slidenote_modal(){
                    $("#slidenote").modal("show");
                }

    function deleteImage(imageName) {
        if (!imageName) {
            toastr.error('Invalid image selected.');
            return;
        }

        if (!confirm('Are you sure you want to delete this picture?')) {
            return;
        }

        $.ajax({
            url: '/hospital/reception/delete_biopsy_document',
            method: 'POST',
            data: { image_name: imageName, order_id: <?= $order_main['id'] ?> },
            success: function(result) {
                images = images.filter(function(name) { return name !== imageName.trim(); });
                var thumbnailLink = $('.uploaded-image a').filter(function() {
                    return $(this).data('image_name') === imageName.trim();
                });
                if (thumbnailLink.length) {
                    thumbnailLink.closest('.uploaded-image').remove();
                }

                if (currentIndex >= images.length) {
                    currentIndex = images.length - 1;
                }

                // Update modal only if it's currently open
                if ($('#imagePreviewModal').hasClass('show')) {
                    if (images.length === 0) {
                        $('#imagePreviewModal').modal('hide');
                    } else {
                        currentImageName = images[currentIndex];
                        $('#imagePreviewModal .modal-title').text('Image Preview' + (currentImageName ? ' - ' + currentImageName : ''));
                        $('#previewImage').attr('src', '/image?image=' + encodeURIComponent(images[currentIndex]));
                        resetZoom();
                        updateNavigationButtons();
                    }
                }
            },
            error: function() {
                toastr.error('Unable to delete image. Please try again.');
            }
        });
    }

    function checkdocumentattached() {
        let doc = "<?=$order_main['scanned_documents']; ?>";
        let pid = "<?=$order_main['patient_id']; ?>";
        let biopsy_request_exists = "<?php echo !empty($biopsy_request) ? 'true' : 'false'; ?>";
        if(biopsy_request_exists === 'false' && doc == '' && pid == 0){
            toastr['error']('Please Attach Request First');
            return;
        }

        // Validate required patient information fields
        let first_name = $('#first_name').val().trim();
        let middle_name = $('#middle_name').val().trim();
        let age = $('#age').val().trim();
        let sex = $('#sex').val();
        let mobile = $('#patient_mobile_input').val().trim();
        let biopsy_type = $('#biopsy_type').val();
        let site_of_biopsy = $('#site_of_biopsy').val().trim();
        let anesthesia = $('#anesthesia').val();
        let fixative = $('#fixative').val();
        let sample_taken_date = $('#sample_taken_date').val();

        // Define required fields
        let requiredFields = [
            {id: 'first_name', value: first_name},
            {id: 'middle_name', value: middle_name},
            {id: 'age', value: age},
            {id: 'sex', value: sex},
            {id: 'patient_mobile_input', value: mobile},
            {id: 'biopsy_type', value: biopsy_type},
            {id: 'site_of_biopsy', value: site_of_biopsy},
            {id: 'anesthesia', value: anesthesia},
            {id: 'fixative', value: fixative}
        ];

        let hasError = false;
        requiredFields.forEach(field => {
            if (!field.value) {
                $('#' + field.id).addClass('is-invalid');
                hasError = true;
            } else {
                $('#' + field.id).removeClass('is-invalid');
            }
        });

        if (biopsy_type == 'Grade 1 Punch biopsy') {
            let size = $('#size').val().trim();
            if (!size) {
                $('#size').addClass('is-invalid');
                hasError = true;
            } else {
                $('#size').removeClass('is-invalid');
            }
        }

        if (hasError) {
            toastr.error('Please save patient information first before collecting sample');
            return;
        }

        collect_sample();
    }
    function preview() {
        frame.src = URL.createObjectURL(event.target.files);
    }
    function clearImage() {
        document.getElementById('formFile').value = null;
        document.getElementById('comment').value = "";
        frame.src = "";
    }

    function openImagePreview(index, imageName) {
        currentIndex = index;
        currentScale = 1;
        currentImageName = imageName || images[index] || '';

        if (!$('#imagePreviewModal').length) {
            $('body').append(`
                <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Image Preview</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <div class="image-container" style="overflow: auto; max-height: 70vh;">
                                    <img id="previewImage" src="" alt="Image preview" style="transform: scale(1); transform-origin: center; cursor: grab; display: inline-block; max-width: 100%;">
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-sm btn-outline-primary" onclick="zoomIn()"><i class="fas fa-search-plus"></i> Zoom In</button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="zoomOut()"><i class="fas fa-search-minus"></i> Zoom Out</button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="resetZoom()"><i class="fas fa-search"></i> Reset</button>
                                </div>
                                <div class="mt-2 text-muted small">Use left/right arrows for previous/next, + / - to zoom, and Esc to close.</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-primary" onclick="previousImage()" id="prevBtn"><i class="fas fa-chevron-left"></i> Previous</button>
                                <button type="button" class="btn btn-outline-primary" onclick="nextImage()" id="nextBtn"><i class="fas fa-chevron-right"></i> Next</button>
                                
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times"></i> Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            $(document).on('wheel', '#imagePreviewModal', function(e) {
                if (!$('#imagePreviewModal').is(':visible')) return;
                e.preventDefault();
                if (e.originalEvent.deltaY < 0) {
                    zoomIn();
                } else {
                    zoomOut();
                }
            });

            $(document).on('keydown', function(e) {
                if (!$('#imagePreviewModal').is(':visible')) return;
                if (e.key === 'ArrowLeft') {
                    previousImage();
                } else if (e.key === 'ArrowRight') {
                    nextImage();
                } else if (e.key === '+' || e.key === '=' || e.key === 'Add') {
                    zoomIn();
                } else if (e.key === '-' || e.key === 'Subtract') {
                    zoomOut();
                } else if (e.key === '0') {
                    resetZoom();
                } else if (e.key === 'Escape') {
                    $('#imagePreviewModal').modal('hide');
                }
            });
        }

        $('#imagePreviewModal .modal-title').text('Image Preview' + (currentImageName ? ' - ' + currentImageName : ''));
        $('#previewImage').attr('src', '/image?image=' + encodeURIComponent(images[currentIndex]));
        $('#previewImage').attr('alt', currentImageName);
        $('#previewImage').css('transform', 'scale(' + currentScale + ')');
        updateNavigationButtons();
        $('#imagePreviewModal').modal('show');
    }

    function zoomIn() {
        currentScale = Math.min(currentScale + 0.2, 4);
        $('#previewImage').css('transform', 'scale(' + currentScale + ')');
    }

    function zoomOut() {
        if (currentScale > 0.2) {
            currentScale = Math.max(currentScale - 0.2, 0.2);
            $('#previewImage').css('transform', 'scale(' + currentScale + ')');
        }
    }

    function resetZoom() {
        currentScale = 1;
        $('#previewImage').css('transform', 'scale(1)');
    }

    function previousImage() {
        if (currentIndex > 0) {
            currentIndex--;
            $('#previewImage').attr('src', '/image?image=' + encodeURIComponent(images[currentIndex]));
            resetZoom();
            updateNavigationButtons();
        }
    }

    function nextImage() {
        if (currentIndex < images.length - 1) {
            currentIndex++;
            $('#previewImage').attr('src', '/image?image=' + encodeURIComponent(images[currentIndex]));
            resetZoom();
            updateNavigationButtons();
        }
    }

    function updateNavigationButtons() {
        $('#prevBtn').prop('disabled', currentIndex === 0);
        $('#nextBtn').prop('disabled', currentIndex === images.length - 1);
    }

    var delete_button_lightbox = {
        "background-image" : "/img/delete_button_01.png",
        callback: delete_image_via_lightbox
    };
  
    function toggleSizeField() {
        const biopsyType = $('#biopsy_type').val();
        const sizeValue = $('#size').val().trim();

        if (biopsyType === 'Grade 1 Punch biopsy' || sizeValue !== '') {
            $('#size_container').show();
            if (biopsyType === 'Grade 1 Punch biopsy') {
                $('#size').attr('required', 'required');
            } else {
                $('#size').removeAttr('required');
            }
        } else {
            $('#size_container').hide();
            $('#size').removeAttr('required');
        }
    }

    $(function() {
        // Lightbox functionality replaced with custom modal
        $('#biopsy_type').change(toggleSizeField);
        // Check initial value
        toggleSizeField();
    });

    function collect_sample(){
            save_info();
                    $.ajax({
                        url:"/hospital/reception/collectsample",
                        method:"post",
                        dataType:"json",
                        data:{
                                order_id:<?=$order_main['id']?>, 
                            },
                        success:function(result){
                            console.log(result);
                            if(result.status == 'success'){
                                toastr.success('Sample Collected');
                                setTimeout(function () {
                                    location.reload(); 
                                }, 1000);
                            }
                            else
                                alert('error ' + result.message);
                        }
                    });

                }
        function send_sample(){
                    $.ajax({
                        url:"/hospital/reception/sendsample",
                        method:"post",
                        dataType:"json",
                        data:{
                                order_id:<?=$order_main['id']?>, 
                            },
                        success:function(result){
                            console.log(result);
                            if(result.status == 'success'){
                                toastr.success('Sample Sent');
                                setTimeout(function () {
                                    location.reload(); 
                                }, 1000);
                            }
                            else
                                alert('error ' + result.message);
                        }
                    });

                }
        function recieve_slide(){
            let note = document.getElementById('slide').value;
                    $.ajax({
                        url:"/hospital/reception/recieveslide",
                        method:"post",
                        dataType:"json",
                        data:{
                                order_id:<?=$order_main['id']?>, 
                                note:note
                            },
                        success:function(result){
                            console.log(result);
                            if(result.status == 'success'){
                                toastr.success('Slide Recieved');
                                setTimeout(function () {
                                    location.reload(); 
                                }, 1000);
                            }
                            else
                                alert('error ' + result.message);
                        }
                    });

                }
        function save_info() {
            // Validate required fields
            let first_name = $('#first_name').val().trim();
            let middle_name = $('#middle_name').val().trim();
            let age = $('#age').val().trim();
            let sex = $('#sex').val();
            let mobile = $('#patient_mobile_input').val().trim();
            let biopsy_type = $('#biopsy_type').val();
            let site_of_biopsy = $('#site_of_biopsy').val().trim();
            let anesthesia = $('#anesthesia').val();
            let fixative = $('#fixative').val();
            let specimen = $('#specimen').val();
            let urgent_specimen = $('#urgent_specimen').is(':checked') ? 1 : 0;
            let sample_taken_date = $('#sample_taken_date').val();

            // Define required fields
            let requiredFields = [
                {id: 'first_name', value: first_name},
                {id: 'middle_name', value: middle_name},
                {id: 'age', value: age},
                {id: 'sex', value: sex},
                {id: 'patient_mobile_input', value: mobile},
                {id: 'biopsy_type', value: biopsy_type},
                {id: 'site_of_biopsy', value: site_of_biopsy},
                {id: 'anesthesia', value: anesthesia},
                {id: 'fixative', value: fixative}
            ];

            let hasError = false;
            requiredFields.forEach(field => {
                if (!field.value) {
                    $('#' + field.id).addClass('is-invalid');
                    hasError = true;
                } else {
                    $('#' + field.id).removeClass('is-invalid');
                }
            });

            if (hasError) {
                toastr.error('Please fill all required fields');
                return;
            }

            // Collect data
            let data = {
                order_id: <?= $order_main['id'] ?>,
                first_name: first_name,
                middle_name: middle_name,
                last_name: $('#last_name').val().trim(),
                age: age,
                date_of_birth: $('#dob').val(),
                sex: sex,
                mobile: mobile,
                additional_info: JSON.stringify({
                    email: $('#email').val().trim(),
                    refering_institution: $('#refering_institution').val().trim(),
                    mrn: $('#mrn').val().trim(),
                    requesting_physician: $('#requesting_physician').val().trim(),
                    previous_histology_no: $('#previous_histology_no').val().trim(),
                    surgeon: $('#surgeon').val().trim(),
                    physician_mobile: $('#physician_mobile').val().trim(),
                    surgeon_mobile: $('#surgeon_mobile').val().trim(),
                    history: $('#history').val().trim(),
                    physical_findings: $('#physical_findings').val().trim(),
                    clinical_diagnosis: $('#clinical_diagnosis').val().trim(),
                    laboratory_information: $('#laboratory_information').val().trim(),
                    specimen: specimen,
                    specimen_urgent: urgent_specimen,
                    biopsy_type: biopsy_type,
                    site_of_biopsy: site_of_biopsy,
                    anesthesia: anesthesia,
                    fixative: fixative,
                    sample_taken_date: sample_taken_date,
                    size: $('#size').val().trim()
                })
            };

            // AJAX
            $.ajax({
                url: "/hospital/reception/save_patient_info",
                method: "post",
                dataType: "json",
                data: data,
                success: function(result) {
                    if (result.status == 'success') {
                        toastr.success('Patient Information Saved');
                    } else {
                        toastr.error('Error: ' + result.message);
                    }
                },
                error: function() {
                    toastr.error('Error saving information');
                }
            });
        }
</script>
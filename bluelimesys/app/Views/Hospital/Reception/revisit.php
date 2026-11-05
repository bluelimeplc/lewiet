<?php
    $loggeduserdata = $this->data['logged_user_data'];
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $patient = $this->data['patient'];
    $doctors = $this->data['doctors'];
    $app_doc=@$_GET['did'];
    $app_id=@$_GET['app_id'];
    $wlid=@$_GET['wlid']??-1;
    $mrn_no = $patient['id'];
    $formated_mrn_no = sprintf('%05d', $mrn_no);
    $days_after_last_visit = $this->data['days_after_last_visit'];
    $upaid_visit_days = $this->data['upaid_visit_days'];
    $patient_has_active_visite = $this->data['patient_has_active_visite'];
    $reference = $this->data['reference'];
    $last_visits_option = $this->data['last_visits_option'];
    $open_visit_queue = $this->data['open_visit_queue'];
    $open_visit_doctor = $this->data['open_visit_doctor'];
    $open_visit_date = $this->data['open_visit_date'];
    $templates = $this->data['templates'];
    
    $company_header = \BluelimeSystem::get_company_header();
    
?> 
<?php
                            
    $url_for_mobile_org = 'http://192.168.1.8/hospital/reception/newmanualcard_mobile?id='.$_GET['id'];
    $url_for_mobile = urlencode($url_for_mobile_org);
    
?>
<script src="<?php echo base_url()?>assets/plugins/summernote/summernote.min.js" type="text/javascript"></script>
<link href="<?php echo base_url()?>assets/plugins/summernote/summernote.css" rel="stylesheet" type="text/css"/>
 
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
            <small>Revisit</small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/reception/searchpatient">Search</a></li>
            </ol>
        </div>
    </section>
    <!-- Main content -->
    <section class="content">

        <?php
            helper('form');
        ?>
        <div class="row">
            <!-- Doctor Assignment -->
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd">
                    <div class="panel-body">
                        
                        <div class="row">
                                <?php
                                    $attributes = array('role'=>'form', 'id'=>'frm_assign_to_doctor', 'method'=>'post', 'class'=>'inline', 'onsubmit'=>'return validate_assigntodoctor();');
                                    echo form_open_multipart('/hospital/reception/assigntodoctor', $attributes);
                                ?>
                            <div class="col-sm-3">
                            <?php if(($days_after_last_visit > $upaid_visit_days) or ($days_after_last_visit==-1)): ?>
                                    <a onclick="show_free_modal()"><img src="/img/free.png" width="40px"  title="Free Visit" data-toggle="tooltip" data-html="true"></a><br>
                            <?php endif; ?>  
                            
                                    <i class="dental-flag-icon">
                                        <i onclick="<?=($days_after_last_visit > $upaid_visit_days)?"":"show_enforce_payment_modal()"?>" style="cursor: pointer;" class="fa fa-check-circle color-<?=($days_after_last_visit > $upaid_visit_days)?"red":"green"?>" data-toggle="tooltip" data-html="true" title="<?= $days_after_last_visit ?> days, Since last visit / <?=$upaid_visit_days?>. Enforce Payment"><span style="font-family: calibri; font-size: medium;"> <?= $days_after_last_visit ?> / <?=$upaid_visit_days?> days</span></i>
                                    </i>
                                

                                <?php if($patient_has_active_visite): ?>
                                    
                                    <i  id="patient_has_active_visite_icon" class="dental-flag-icon" style="color: orange;">
                                        <i class="fa fa-user-md jump_dental" data-toggle="tooltip" data-html="true" title="On Visit (<?=@$open_visit_doctor?>)"></i>
                                    </i>    
                                <?php endif ?>
                            
                                <h3 style="margin-top: 5px !important;">
                                    <b>
                                    <?= "MRN-".$formated_mrn_no;?>
                                    </b>
                                </h3>
                                
                                <b><?= $patient['first_name'].' '.$patient['middle_name'].' '.$patient['last_name'];?></b><br>
                                <?= $patient['mobile'];?>&nbsp;<a href="/hospital/reception/sendsms?mob=<?= $patient['mobile']?>&id=<?= $mrn_no ?>" title="Send sms" data-toggle="tooltip" data-html="true"><img src="/img/sms.svg" width="28px"></a>
                                <?php 
                                    if($patient['company_id'] != 0):
                                ?>
                                        <br>
                                        <input type="button"  class="btn btn-danger" value="<?= $patient['company_name'] ?>">
                                        <div class="input-group">
                                            <span class="input-group-addon">Ref</span>
                                            <input type="text" id="txt_reference" onkeyup="showSetReferenceButton()" onchange="showSetReferenceButton()" name="reference"  value="<?= $reference ?>" class="form-control" form="frm_assign_to_doctor" required placeholder="Reference">
                                            <?php if($patient_has_active_visite) echo '<span id="btn_set_reference" role="button" onclick="setReference()" title="Set reference" class="input-group-addon" style="display:none;"><i class="fa fa-upload"></i></span>'; ?>
                                            
                                        </div>
                                        
                                <?php
                                    endif
                                ?>
                                <div id="set_reference_flash" class="alert alert-danger hide" role="alert" style="margin: 5px; display:inline-block; min-width: 300px; text-align:left;">
                                    <span></span>
                                </div>
                                                                            
                                <!--input type="button"  class="btn-xs btn-success" style="background-color: green; border-0" value="ET"-->
                            </div>
                            <div class="col-sm-5">
                                
                                    <br>
                                    <label for="assigned_doctor" class="control-label"><i style="width: 90px; display: inline-block;"></i><b>Assign to doctor</b></label>
                                    
                                    <div class="input-group">
                                    
                                        <input type="hidden" name="patient_id" value="<?php echo $mrn_no ?>">
                                        <input type="hidden" name="service_id[]" value="<?= $consultation_service['id'] ?>">
                                        <input type="hidden" name="service_name[]" value="<?= $consultation_service['service_name'] ?>">
                                        <input type="hidden" name="service_type[]" value="<?= $consultation_service['service_type'] ?>">
                                        <input type="hidden" name="mobile" value="<?php echo $patient['mobile'] ?>">
                                        <input type="hidden" name="app_id" value="<?= $app_id ?>">
                                        <input type="hidden" name="bill_quantity" value="<?php echo 1 ?>">
                                        <input type="hidden" name="registered_by" value="<?php echo $logged_user ?>">
                                        <input type="hidden" name="company_id" value="<?php echo $patient['company_id'] ?>">
                                        <input type="hidden" name="registered_by" value="<?php echo $logged_user ?>">
                                        
                                        <div class="text-center" style="vertical-align: middle; padding:5px; display: table-cell; width:90px;">
                                            <img src="/img/loading-loading-forever.gif" width="20px;" style="display: none;" id="waitingBusy">
                                            <span class="label label-warning color-violet" id="waitingNo" style="display: none;">200 waiting</span>
                                        </div>
                                        <input type="hidden" name="is_free" id="is_free">
                                        <input type="hidden" name="is_enforced" id="is_enforced">
                                        <input type="hidden" name="wlid" id="wlid" value="<?=$wlid?>">
                                        
                                        <select id="assigned_doctor" name="assigned_doctor" class="form-control" required onchange="get_no_of_waiting(this.value)">
                                            <option></option>
                                                <?php
                                                    foreach($doctors as $doctor):
                                                ?>
                                                    <option value="<?= $doctor['id'] ?>" <?= ($doctor['id']==$app_doc)?"selected":"" ?>><?= $doctor['name'] ?></option>';
                                                    <?php
                                                    endforeach
                                                ?>
                                        </select>
                                        <div class="input-group-btn" style="vertical-align: top;">
                                            <button class="btn btn-success" type="submit">
                                                Send <i class="glyphicon  glyphicon-share-alt"></i>
                                            </button>
                                        </div>                                          
                                    </div>
                                    <br>
                                    <div style="margin-left: 90px;color:crimson; font-size: large;"><label id="free_status"></label></div>
                                    <div id="doctor_schedule_flash" class="alert alert-warning hide" role="alert" style="margin-top: 5px; margin-left: 0px;">
                                        <span></span>
                                    </div>
                                    
                                    
                                    <?php
                                        $last_visit_options_flash_hide = '';
                                        if(($last_visits_option['transferForNextYear'] == false) && empty($last_visits_option['remark']))
                                            $last_visit_options_flash_hide = 'hide';
                                    ?>
                                    <div id="last_visit_options_flash" class="alert alert-info <?= $last_visit_options_flash_hide ?>" role="alert" style="margin-top: 5px;">
                                        <span style="color:#000">
                                            <b><u>Last visit info:</u></b><br>
                                            <?php 
                                                if($last_visits_option['transferForNextYear'] == true)
                                                    echo '<div><i class="fa fa-check-circle"></i> Transfer for next year</div>';
                                                if(!empty($last_visits_option['remark'])){
                                                    echo '<b><i>Remark:</i></b><br>';
                                                    
                                                    foreach($last_visits_option['remark'] as $remark){
                                                        echo $remark['remark'] . ' <i style="color:#005196"><small>[' . $remark['date'] . ']</small></i> <br>';
                                                    }
                                                }
                                                        
                                            ?>                                                                   
                                        </span>
                                    </div>
                                    
                                <?php echo form_close(); ?>
                                
                            </div>
                            <div class="col-sm-3 dental-menu-icons-01">
            
                            <i><a href=""  class="fa fa-cart-plus"  title="Add Order"  data-target="#new_order" data-toggle="modal" data-html="true"></a></i>
                                <a href="/hospital/reception/newappointment?id=<?php echo $mrn_no ?>" class="fa fa-calendar" title="Make Appointment" data-toggle="tooltip" data-html="true"></a>
                               <!--   <a href=""  class="fa fa-hourglass-start"  title="Add to Waiting List"  data-target="#new_waiting_list" data-toggle="modal" data-html="true"></a> -->
                                <i <?= ($patient_has_active_visite)?"":"hidden" ?> data-toggle="tooltip" title="Print card" data-html="true"><a href="" class="fa fa-print"   data-target="#printCard" data-toggle="modal" data-html="true"></a></i>
                                <i data-toggle="tooltip" title="Consent Templete" data-html="true"><a href="" class="fa fa-edit"   data-target="#consent" data-toggle="modal" data-html="true"></a></i>
                                
                                
                            </div>
                            
                            <!--div ><a href="/hospital/reception/newappointment?id=<?php echo $mrn_no ?>" class="btn btn-success pull-right">Make Appointment</a></div-->
                        </div>
                                                            
                    </div>
                    
                </div>
            
            </div>
            <!-- Patient Info -->
            <div class="col-sm-6 col-md-6">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Patient Detail <a  class="btn btn-primary btn-sm" href="<?php echo base_url();?>hospital/reception/editpatient?id=<?php echo $patient['id'];?>"title="Edit Patient" data-toggle="tooltip" data-html="true"><i class="fa fa-edit"></i> </a>
                            </h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        
                        <div class="form-body">
                            <table class="table table-striped table-bordered">
                                <tr>
                                    <td width="120">Name</td>
                                    <td><?= $patient['first_name'] . ' ' . $patient['middle_name'] . ' ' . $patient['last_name']; ?></td>
                                    <td style="position: relative;" rowspan="6">
                                    <?php
                                        helper('form');
                                        $attributes = array('class' => 'form-horizontal','role'=>'form',  'enctype' => 'multipart/form-data');
                                        echo form_open('hospital/reception/addmanualcard', $attributes);
                                    ?> 
                                        <div class="col-sm-1">
                                            <input type="file" id="drop_zone" class="FileUpload"   accept="image/*" onchange="this.form.submit()"  name="picture"  title="Upload Manual Card" data-toggle="tooltip" data-html="true" >
                                            <input type="hidden" value="<?=$_GET['id']?>" name="patient_id">
                                            <div class="dropZoneOverlay">
                                                <img src="/api/qrcode?data=<?= $url_for_mobile ?>" alt="QR Code for mobile photo" title="Scan with mobile to take pictures." />
                                            </div>
                                            <div hidden><input type="submit"></div>
                                            <?php $result = (explode(',', $patient['attached_old_card'])); 
                                            //BluelimeSystem::show_and_die(count($result));
                                            ?>
                                            
                                            <div class="manual_card  <?= (empty($patient['attached_old_card']))?" hidden ":""?>" style="margin-left: 30px;">
                                                

                                                
                                                <?php
                                                    $manual_card_image = explode(',',$patient['attached_old_card']);
                                                    $is_first = true;
                                                    foreach($manual_card_image as $image_name):
                                                        echo '<a href="/image/manual_card?image='.$image_name.'" data-image_name="'.$image_name.'"><img src="/image/manual_card?image='.$image_name.'&thumbnail=1" style="width:50px" ></a>';
                                                    endforeach;
                                                ?>
                                                
                                            
                                            </div>
                                        </div>
                                        
                                    <!--button style="position: absolute; bottom: 0; right: 0;" class="btn btn-xs btn-danger <?= (empty($patient['attached_old_card']))?" hidden ":""?>" type="button" onclick="deleteallcards()">Delete All Cards</button-->
                                    <?php echo form_close(); ?>
                                    </td>
                                    
                                </tr>
                                <tr>
                                    
                                    <td>Date of Birth</td>
                                    <td><?= $patient['date_of_birth']?> | Age: <b><?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."Y "?><?=BluelimeSystem::month_difference(BluelimeSystem::date_now(),$patient['date_of_birth'])."M"?></b></td>
                                    <script>
                                        function getAge(val){
                                            var CurrentYear = new Date().getFullYear();
                                            document.getElementById('patient_age').innerHTML = CurrentYear - val.slice(0, 4);
                                        }
                                        getAge('<?=$patient['date_of_birth']?>');
                                    </script>
                                </tr>
                                <tr>
                                    <td>Sex</td>
                                    <td><?= $patient['sex']?></td>
                                </tr>
                                <tr>
                                    <td>Mobile</td>
                                    <td><?= $patient['mobile']?></td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td><?= $patient['address'];?> | <b>Sub city</b> <?= $patient['subcity'];?> | <b>Woreda</b> <?= $patient['woreda'];?> | <b>House&nbsp;N<u>o</u></b>&nbsp;<?= $patient['house_no'];?></td>
                                </tr>
                                <tr>
                                    <td>Emergency Contact</td>
                                    <td><b>Name</b> <?= $patient['emergency_contact_name']?> <br>
                                        <b>Mobile</b> <?= $patient['emergency_mobile']?>
                                    </td>
                                </tr>
                                <?php if($patient['company_id'] != 0): ?>
                                    <tr>
                                        <td>Company</td>
                                        <td><?= $patient['company_name'];?></td>
                                    </tr>
                                <?php endif ?>
                                
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Medical Histors -->
            <div class="col-sm-6 col-md-6">
                
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Visit History
                            <i class="dental-flag-icon">
                                        <i onclick="show_all_visit_modal()" style="cursor: pointer;" class="fa fa-history color-green" data-toggle="tooltip" data-html="true" title="View All Visits"><span style="font-family: calibri; font-size: medium;"></span></i>
                                    </i>
                            </h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <tr>
                                <td width="120">Last Visit Doctor</td>
                                <td width="150"><?=$previous_visit_doctor?></td>
                            </tr>
                            <tr>
                                <td width="120">Last Paid Visit</td>
                                <td width="150"><?=$last_visit_bill_date?></td>
                            </tr>
                            <tr>
                                <td width="120">Current Visit Doctor</td>
                                <td width="150"><?=$open_visit_doctor??''?></td>
                                
                            </tr>
                            <tr>
                                <td width="120">Active Appointment</td>
                                <td width="150"><?=@$active_apointments?></td>
                            </tr>
                            <tr>
                                <td width="120">Patient Previous Procedures</td>
                                <td width="150">
                                    <table class="table">
                                    <?php
                                        foreach($patient_procedures as $procedure)
                                            {
                                                echo '
                                                <tr>
                                                    <td>'.$procedure['service_name'].'</td><td>'.substr($procedure['bill_date'],0,10).'</td>
                                                </tr>
                                                ';
                                            }
                                    ?>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </section> <!-- /.content -->
</div> <!-- /.content-wrapper -->

<div class="modal fade"   style="height: 160mm; width: 90mm;" id="printCard" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-content">
		<div class="modal-body" style="color: black;">
        
            <div id="div1" >
            <div>
            <?php echo '<img  width="100"  style="margin-bottom:10px" src="data:image/jpeg;base64,'.base64_encode( $company_header['logo_printouts'] ).'"/>' ?>
                <span style="font-size:11px; float:right; border-left: 1px solid black; padding-left: 12px; height: 60px;">
                    <table>
                    <tr><td>Mob.</td><td><?=$company_header['mobile']?></td></tr> 
                    <tr><td></td><td></td></tr> 
                    <tr><td>Tel.</td><td><?=$company_header['tel']?></td></tr> 
                    <tr><td colspan="2" style="font-size:9px;"><?=$company_header['website']?></td></tr> 
                    <tr><td colspan="2" style="font-size:9px;"><?=$company_header['email']?></td></tr> 
                    
                    </table>
                </span>
            </div>
            <hr style="border-width: 2px;">
                <div class="container header">
                    <div class="row details-content">
                        <div class="single-header-info">
                        
                                <span <?php echo ($patient['company_id'] != 0)?"":"hidden"?> style="font-size:12px;"><strong><?php echo lang('Company Name');?> : </strong><?= $patient['company_name'] ?></span><br>
                                <span style="font-size:12px;"><strong><?php echo lang('Patient Name');?> : </strong><?php echo  $patient['first_name'].' '.$patient['middle_name'].' '.$patient['last_name'] ;?></span><br>
                                <span style="font-size:12px;"><strong><?php echo lang('Card No');?> : </strong><?php echo "MRN-".@$formated_mrn_no;?></span><br>
                                <span style="font-size:12px;"><strong><?php echo lang('Phone Number');?> : </strong><?php echo @$patient['mobile'];?></span><br>
                                <span style="font-size:12px;"><strong><?php echo lang('Assigned Doctor');?> : </strong><?php echo @$open_visit_doctor;?></span><br>
                                <span style="font-size:12px;"><strong><?php echo lang('Visit Date');?> : </strong><?php echo $open_visit_date; ?></span><br>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row details-content">
                        <div style="width: 80mm;">
                            <div class="table-responsive marg"><span style="font-size:14px;">
                                <div class="col-sm-4 col-md-4"><strong>Queue No</strong></div></span>
                            </div>
                            <div class="col-sm-4 col-md-4" align="center" style="border-width:2px; text-align:center; font-weight:bold; font-size:70px; text-align:text-center; width:120px">
                                        <p style="border-style: dotted;"><?php echo $open_visit_queue; ?></p>
                            </div>                                    
                        </div>
                    </div>
                </div>
            </div>
                    
                <span style="font-size: smaller; ">Consultation fee is valid for <?= $upaid_visit_days ?> days.</span>       
                 <hr>
                 <p style="font-size:8px; text-align: center;">Powered by Bluelime Technologies<br>www.bluelimeplc.com</p>      
        </div>
        <div class="modal-footer col-md-12">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
            <button type="button" class="btn btn-success" onclick="printContent('div1')">Print</button>
        </div>                                                          
    </div>

</div>


<div class="modal fade" id="new_waiting_list" tabindex="-1" role="dialog" aria-labelledby="NewWaitingList" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header dental-bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">New Waiting List</h4>
                                </div>
            <div class="modal-body">
            <div class="panel-body">
                <div class="portlet-body form">
                    
                
                <?php 
                    helper('form');
                    $attributes = array('class' => 'form-horizontal', 'method'=>'post','name'=>'add_waiting_list');
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
                                    <input type="text" hidden id="patient_id" name="patient_id" value="<?php echo @$patient['id']?>">
                                    <input type="text" hidden name="wlid" value="<?php echo @$wlid ?? ""?>">
                                    <input type="text" hidden name="status" value="<?php echo 1; ?>">
                                    <input type="text" hidden name="registered_by" value="<?php echo $logged_user?>">
                                    <input type="text" hidden name="registered_by_name" value="<?php echo $logged_user_name?>">
                                    <input type="text" required  name="first_name" id="first_name" <?php echo $edit; ?>  class="form-control" placeholder="Name" value="<?php echo @$patient['first_name'] ?? ""  ?>">
                                    <span class="input-group-addon"></span>
                                    <input type="text" required name="middle_name" <?php echo $edit; ?> class="form-control" placeholder="Father Name" value="<?php echo @$patient['middle_name'] ?? ""  ?>">
                                    <span class="input-group-addon"></span>
                                    <input type="text" name="last_name"  <?php echo $edit; ?> class="form-control" placeholder="Grand Father" value="<?php echo @$patient['last_name'] ?? ""  ?>">
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label"><?php echo "Mobile"?> :</label>
                                <div class="">
                                    <input type="txt" <?php echo $edit?>placeholder="09xxxxxxxx"  name="mobile"  <?php echo $edit; ?> value="<?php echo @$patient['mobile'] ?? ""  ?>"  required class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label">Reason:</label>
                            <div class="">
                                <select class="form-control" id="procedure"  name="procedure"  required>
                                    <option value="">--Select Reason--</option>
                                    <?php
                                        foreach($services as $service)
                                        echo '<option value="'. $service['id'] .'">'. $service['service_name'] .'</option>';
                                        echo '<option hidden value="'. $service['service_name'] .'">'. $service['service_name'] .'</option>';
                                        
                                    ?>
                                </select>
                            </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label"><?php echo "Estimated Date"?> :</label>
                                <div class="">
                                    <input type="date" id="estimated_date" min="<?php echo  date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>" value="<?php echo set_value('date'); ?>" name="estimated_date" class="form-control" placeholder="<?php echo lang('date_placeholder')?>" required>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label class="control-label">Doctor:</label>
                            <div class="">
                                <select class="form-control doctor_id" id="doctor_id"  onchange="loadSchedule(this.value);check_active_appointment();" name="doctor_id"  required>
                                    <option value="">--Select doctor--</option>
                                    <?php
                                        foreach($doctors as $doctor)
                                        echo '<option value="'. $doctor['id'] .'">'. $doctor['name'] .'</option>';
                                        
                                    ?>
                                </select>
                            </div>
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
    <?php echo form_close(); ?>
    </div>
    </div>
</div>

<div class="modal fade" id="new_order" tabindex="-1" role="dialog" aria-labelledby="NewOrder" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            
        <div class="modal-header dental-bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">New Service Order</h4>
                                    
                                </div>
                                
            <div class="form-body"">
                <div class="modal-body">
                    <div class="panel-body">
                        <div class="portlet-body form">
                        <?php 
                            helper('form');
                            $attributes = array('class' => 'form-horizontal', 'method'=>'post','name'=>'add_order','id'=>'add_order');
                            echo form_open('/hospital/reception/addorder', $attributes);                
                        ?>
                                
                                <div id="plan_1" class="tab-pane active"><br>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="bg-info">
                                                <th width="450">Service</th>
                                                <th width="100">Quantity</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                <select class="form-control" id="service"   required>
                                                    <option value=""></option>
                                                    <?php
                                                        foreach($services as $service)
                                                        echo '<option value="'. $service['id'] .'">'. $service['service_name'] .'</option>';
                                                        
                                                    ?>
                                                </select>
                                                </td>
                                                <td>
                                                    <input type="number" min="1" value="1" class="form-control" id="order_quantity" />
                                                </td>
                                                <td>
                                                    <button class="btn btn-info" onclick="add_service('order_service_list');" type="button"><i class='fa fa-plus' ></i></button>
                                                    <!--button class="btn btn-info" onclick="remove_treatment_plan(1,1);" type="button"><i class='fa fa-minus' ></i></button-->
                                                </td>
                                            </tr>
                                            </thead>
                                            <tbody id="order_service_list">

                                            </tbody>
                                    </table>
                                </div>
                                    <div class="form-group col-md-12">
                                        <div class="input-group">
                                        
                                            <input type="text" hidden id="patient_id" name="patient_id" value="<?php echo @$patient['id']?>">
                                            <input type="text" hidden name="visit_id" value="<?php echo @$visit_id ?? ""?>">
                                            <input type="text" hidden name="visit_date" value="<?php echo @$visit_date ?? ""?>">
                                            <input type="text" hidden name="is_external" value="<?php echo 0; ?>">
                                            <input type="text" hidden name="registered_by" value="<?php echo $logged_user?>">
                                            <input type="text" hidden name="registered_by_name" value="<?php echo $logged_user_name?>">
                                            <input type="text" hidden name="description" value="<?php echo ''; ?>">
                                            <input type="text"  hidden name="mobile" value="<?php echo @$patient['mobile'] ?? ""  ?>" >
                                            <input type="text"  hidden name="company_id" value="<?php echo @$patient['company_id'] ?? ""  ?>" >
                                            <input type="hidden" name="is_free_service" id="is_free_service">                  
                                        </div>
                                    </div>
                                    
                                    <div class="tab-pane active">
                                        <label class="control-label">Order By Doctor:</label>
                                    <div class="tab-pane active">
                                        <select class="form-control" id="order_by"  name="order_by"  required>
                                            <option value="">--Select doctor--</option>
                                            <option value="-1">Nurse</option>
                                            <?php
                                                foreach($doctors as $doctor)
                                                echo '<option value="'. $doctor['id'] .'">'. $doctor['name'] .'</option>';
                                                
                                            ?>
                                        </select>
                                        <br>
                                    <div style="margin-left: 90px;color:crimson; font-size: large;"><label id="free_service_status"></label></div>
                                    </div>
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
            <a style="float:left" onclick="show_free_order_modal()"><img src="/img/free.png" width="30px"  title="Free Service" data-toggle="tooltip" data-html="true"></a>
                
                <button type="button"  onclick="validateServiceList()" class="btn btn-primary" >Save</button>
                
            </div>
                <?php echo form_close(); ?>
            </div>
    </div>
</div>

<div class="modal fade" id="consent" tabindex="-1" role="dialog" aria-labelledby="consent" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header dental-bg-primary">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add New Template" data-target="#template" data-toggle="modal" data-html="true" ></i> Consent Form Templates</h4>
        </div>
            <div class="modal-body">
                <div class="panel-body">
                    <div class="portlet-body form">
                        <table class="table table-striped">
                            <?php foreach($templates as $template): ?>
                                <tr>
                                    <td>
                                        <a href="viewtemplate?tid=<?=$template['id']?>&pname=<?= $patient['first_name'] . ' ' . $patient['middle_name'] . ' ' . $patient['last_name']; ?>"><?=$template['template_name']?></a>
                                    </td>
                                    <td>
                                        <a  class="btn btn-success btn-sm" href="viewtemplate?tid=<?=$template['id']?>&pname=<?= $patient['first_name'] . ' ' . $patient['middle_name'] . ' ' . $patient['last_name']; ?>"><i class="fa fa-eye"></i> </a>
                                        <a  class="btn btn-primary btn-sm" href="#"><i class="fa fa-edit" title="Edit New Template" data-target="#template" data-toggle="modal" data-html="true" ></i> </a>
                                        <a  class="btn btn-danger btn-sm" href="#"><i class="fa fa-trash" title="Delete Template" data-target="#confirm_delete_template" data-toggle="modal" data-html="true" ></i> </a>                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                            
                    </div>
                </div>
            </div>
            
    <?php echo form_close(); ?>
    </div>
    </div>
</div>

<div class="modal fade" id="template" tabindex="-1" role="dialog" aria-labelledby="template" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header dental-bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">New Templates</h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <div class="portlet-body form">
                    <?php 
                        helper('form');
                        $attributes = array('class' => 'form-horizontal', 'method'=>'post','name'=>'add_waiting_list');
                        echo form_open('/hospital/reception/addtemplate', $attributes);           
                    ?>
                        <div class="tab-pane active">
                                <label class="control-label">Template Name:</label>
                            <div class="tab-pane active">
                                <input type="text" class="form-control" name="template_name" id="template_name" >
                                <input type="hidden" class="form-control" name="pid" id="pid" value="<?=$mrn_no?>">
                            </div>
                        </div>
                        <div class="tab-pane active">
                        <label class="control-label" for="content">Content</label>
                            <div class="tab-pane active">
                                <textarea id="summernote" name="template_content" id="template_content" style="resize: none;" type="text" class="form-control"></textarea>
                            </div>
                            
                        </div>
                                                
                    </div>
                        
                </div>
            </div>
                
            <div class="modal-footer">
                
                <button type="submit" class="btn btn-primary" >Save</button>
                
            </div>
    <?php echo form_close(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="confirm_delete_template" tabindex="-1" role="dialog" aria-labelledby="CancelAppointment" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Template</h5>
                </div>
                <div class="modal-body">
                    <h4>Are you Sure you want to Delete Template?</h4><br>
                </div>
                    
                <div class="modal-footer">
                    
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                    <a href="/hospital/reception/deletetemplate?id=<?=$mrn_no?>&tid=<?=@$template['id']?>"><input type="button" class="btn btn-danger"  value="Delete"></a>
                </div>
        
        </div>
    </div>
</div>
<div class="modal fade" id="freevisit" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        
                       
                        <h4 class="modal-title">Reason for Free Visit</h4>
                    </div>
                    <div class="modal-body">
                        
                        <input id="old_card" type="hidden" value="<?=@$_GET['id'] ?>">
                        <div>
                                <div style="text-align: left;margin-top: 15px;">
                                    <input style="transform: scale(1.5);" type="radio" value="1" name="free" id="rad_transferFromBranch" />&nbsp;
                                    <label class="form-check-label" for="rad_transferFromBranch"> Transfer from Branch</label><br>
                                    <input style="transform: scale(1.5);" type="radio" value="2" name="free" id="rad_afterSurgery" />&nbsp;
                                    <label class="form-check-label" for="rad_afterSurgery"> After Surgery/Procedure Followup </label><br>
                                    <input style="transform: scale(1.5);" type="radio" value="4" name="free" id="rad_approvedFree" />&nbsp;
                                    <label class="form-check-label" for="rad_approvedFree"> Approved Free Consultation</label>
                                </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                    <input style="float: left;" class="btn btn-warning" value="Reset" type="button" onclick="resetradio()">
                       <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="setfreevisittype()">OK</button>
                    </div>
                </div>
            </div>
</div>
<div class="modal fade" id="freeservice" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <h4 class="modal-title">Reason for Free Order</h4>
                    </div>
                    <div class="modal-body">
                        
                        <input id="old_card" type="hidden" value="<?=@$_GET['id'] ?>">
                        <div>
                                <div style="text-align: left;margin-top: 15px;">
                                    <input style="transform: scale(1.5);" type="radio" value="1" name="free" id="rad_approvedFreeService" />&nbsp;
                                    <label class="form-check-label" for="rad_approvedFreeService"> Approved Free Service</label>
                                </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                    <input style="float: left;" class="btn btn-warning" value="Reset" type="button" onclick="resetradio()">
                       <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="setfreeservice()">OK</button>
                    </div>
                </div>
            </div>
</div>
<div class="modal fade" id="enforce_payment" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        
                       
                        <h4 class="modal-title">Enforce Payment</h4>
                    </div>
                    <div class="modal-body">
                        
                        <input id="old_card" type="hidden" value="<?=@$_GET['id'] ?>">
                        <div>
                                <div style="text-align: left;margin-top: 15px;">
                                    <input style="transform: scale(1.5);" type="radio" value="1" name="enforce" id="rad_enforce" />&nbsp;
                                    <label class="form-check-label" for="rad_enforce"> Enforce Consultation Payment</label>
                                </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                    <input style="float: left;" class="btn btn-warning" value="Reset" type="button" onclick="resetradio()">
                       <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="setenforcepayment()">OK</button>
                    </div>
                </div>
            </div>
</div>
<div class="modal fade" id="view_visit_history" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">                       
                        <h4 class="modal-title">All Visits</h4>
                    </div>
                    <div class="modal-body">
                        
                            <table class="table table-bordered table-hover">
                                <?php
                                    foreach($patient_all_visits as $pv){
                                ?>
                                    <tr style="cursor: pointer;" ><td><?=$pv['doctor_name']?></td><td><?=date('Y-m-d', strtotime($pv['visit_date']))?></td></tr>
                                <?php
                                    }
                                ?>
                                
                            </table>
                    </div>
                </div>
            </div>
</div>
                                          
<script>
    var cnt = 0;
    function add_service(t){
        var count = 1;
        var service_id  = $("#service").val();
        var  service_name = $("#service option:selected").text();
        var order_quantity = $("#order_quantity").val();
        var a = "service_name" + count;
            e = document.createElement("tr");
        if(service_name!="" && order_quantity!="" ){

        
        e.innerHTML = "<td style='width: 200px;'><input name='service_name[]' type='text' value='"
        +service_name+"' readonly required class='form-control' id='" + a + "'><input name='service_id[]' type='hidden' value='"
        +service_id+"' class='form-control'></td><td><input name='bill_quantity[]' type='text' value='"
        +order_quantity+"' readonly class='form-control'></td><td><button type='button' onclick='deleteRow(this)'"
        +"class='btn btn-danger'><i class ='fa fa-trash'></i></button></td>",
        document.getElementById(t).appendChild(e), count++, cnt++

        }

        else
        dental_show_alert("Please fill all required fields")
    }
    function deleteRow(t) {

        var a = $("#order_service_list").length;
        var e = t.parentNode.parentNode;
        e.parentNode.removeChild(e);
        
    }

    function printContent(el){
        var restorepage  = $('body').html();
        var printcontent = $('#' + el).clone();
        $('body').empty().html(printcontent);
        window.print();
        $('body').html(restorepage);
        location.reload();
    }

    function check_if_doctor_is_available_today(doctor_id){
        //checkifdoctorisavailable

        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/ajax/checkifdoctorisavailable",
            data: {"doctor_id":doctor_id, "date":"<?= date("Y-m-d") ?>"},
            success: function(result){
                try{
                    let r = JSON.parse(result);
                    let html_txt = " Today this doctor is available from " + r.start_time + " to " + r.end_time;
                    dental_hide_flash('doctor_schedule_flash');
                }
                catch (e){
                    dental_show_flash('doctor_schedule_flash', 'This doctor is not available today.');
                }
            }
        });
    }

    
    function get_no_of_waiting(doctor_id)
    {   
        var busy = document.getElementById("waitingBusy");
        var waitingNo = document.getElementById("waitingNo");

        waitingNo.innerHTML = "";
        waitingNo.style.display = "none";

        if(doctor_id == ''){
            busy.style.display = "none";
            dental_hide_flash('doctor_schedule_flash');
            return false;
        }

        check_if_doctor_is_available_today(doctor_id);

        
        busy.style.display = "inline-block";
        
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/ajax/getwaitingpatients",
            data: {"doctor_id":doctor_id},
            success: function(result){
                if(result != null){
                    if(!isNaN(result))
                        waitingNo.innerHTML = result + " waiting";
                    else{
                        waitingNo.innerHTML = "Error";
                        window.location.reload();
                    }
                    waitingNo.style.display = "inline-block";
                    busy.style.display = "none";
                }
            }
        });

    }
        
    function check_if_patient_is_already_on_visit()
    {
        has_active_visite = <?= ($patient_has_active_visite) ? 'true' : 'false' ?>;
        if(has_active_visite != 0){
            $('#patient_has_active_visite_icon').removeClass('jump');
            //dental_show_alert('This patient is already on visit.');
            toastr['error']('This patient is already on visit.');
            $('#patient_has_active_visite_icon').addClass('jump');
            return true;
        }
        return false;
    }

    function validate_assigntodoctor()
    {
        if(check_if_patient_is_already_on_visit())
            return false;
        return true;
    }

    var reference = "<?= $reference ?>";
    function showSetReferenceButton(){
        let txt_reference = $('#txt_reference').val();
        if(txt_reference != reference)
            $('#btn_set_reference').css('display','table-cell');
        else
            $('#btn_set_reference').css('display','none');
    }

    function setReference(){
        let new_reference = $('#txt_reference').val();
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/ajax/setreference",
            data: {patient_id:<?= $patient['id'] ?>, reference: new_reference},
            success: function(result){
                try{
                    let r = JSON.parse(result);
                    if(r.status == "success"){
                        reference = new_reference;
                        showSetReferenceButton();
                    }
                    else
                        dental_show_flash('set_reference_flash', result);
                }
                catch (e){
                    error_html = "Invalid JSON returned.<br><u>Result</u>:<br>" + result + "<hr>Exception:<br>"+e;
                    dental_show_flash('set_reference_flash', error_html);
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                error_html = "Update is not made. <br><u>Error</u>:<br>" + errorThrown;
                dental_show_flash('set_reference_flash', error_html, false);
            }
        });
    }
    function deleteallcards(){
         $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/reception/deleteallcards",
            data: {patient_id:<?= $patient['id'] ?>},
            success: function(result){
                location.reload();
                }
            }
            
        );
    }
    

    function isfree() {
        var free = document.getElementById("free");
            if (free.checked == true){
                confirm("Are?");
            }
        
    }
    function setfreevisittype() {
            var free = $("input[name='free']:checked").val();
            //alert(free);  
            if(parseInt(free ) == 1){
                $("#free_status").html("Free For Transfer from Branch");
                $("#is_free").val(1);
            }
            else if(parseInt(free ) == 2){
                $("#free_status").html("Free For Surgery/Procedure Followup");
                $("#is_free").val(2);
            }
            // else if(parseInt(free ) == 4){
            //     $("#free_status").html("Free For Medical Certificate");
            //     $("#is_free").val(4);
            // }
            else if(parseInt(free ) == 4){
                $("#free_status").html("Free For Approved Patient");
                $("#is_free").val(4);
            }
            
            else{
                $("#free_status").html("");
                $("#is_free").val(0);
            }
            
        }
        function setfreeservice() {
            var freeservice = $("input[name='free']:checked").val();
            //alert(parseInt(freeservice));  
            if(parseInt(freeservice) == 1){
                $("#free_service_status").html("Free Service");
                $("#is_free_service").val(1);
            }
            
            else{
                $("#free_service_status").html("");
                $("#is_free_service").val(0);
            }
            
        }
        function setenforcepayment() {
            var enforce = $("input[name='enforce']:checked").val();
            //alert(parseInt(enforce));  
            if(parseInt(enforce) == 1){
                $("#free_status").html("Payment will be Enforced");
                $("#is_enforced").val(1);
            }
            
            else{
                $("#free_status").html("");
                $("#is_enforced").val(0);
            }
            
        }


    function get_templete_data(tid){
        let name = $("#template_content").val();
        let description = $("#template_name").val();
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/ajax/addnewitemintocommonlookingtable",
            data: {"tid":tid},
            success: function(result, textStatus, jqXHR){
                    let r = JSON.parse(result);
                    if(r.status == 'success'){
                        inputtag_array.push(r.result);
                        $("#template_name").val('');
                        $("#template_content").val('');
                }
            },
        });
    }
    
    function validateServiceList(){
        let order_by = $('#order_by').val();
        
        if(cnt > 0){
            if((order_by == 0)){
                toastr['error']("Select Order By");
                return false;
            }
            else
                $('#add_order').submit();
        }
        else{
            toastr['error']("Please add Service from the List");
            return false;
        }
       

    }

    function show_free_order_modal(){
                    $("#freeservice").modal({backdrop: "static",keyboard: false});
                }function show_free_modal(){
                    $("#freevisit").modal({backdrop: "static",keyboard: false});
                }
    function show_enforce_payment_modal(){
                    $("#enforce_payment").modal({backdrop: "static",keyboard: false});
                }
    function show_all_visit_modal(){
                    $("#view_visit_history").modal();
                }
    
    const maxChecks = 1
    let selectedCount = 0

    document.querySelector('div').addEventListener('click', event => {
    if (event.target.type === 'checkbox') {
        selectedCount = event.target.checked
        ? selectedCount + 1
        : selectedCount - 1
    }

    if (selectedCount >= maxChecks) {
        [...document.querySelectorAll('input:not(:checked)')].forEach(input => input.disabled = true)
    } else {
        [...document.querySelectorAll('input')].forEach(input => input.disabled = false)
    }
    })
    function resetradio(){
        $('input[name=free]').prop('checked',false);
        $('input[name=enforce]').prop('checked',false);
    }

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
                url: '/hospital/reception/delete_manual_card',
                method: 'POST',
                data: {image_name:image_name, patient_id:<?= $patient['id'] ?>},
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
    $(document).ready(function() {
        $('#summernote').summernote({height: 700,
        focus: true});
    });

</script>
    
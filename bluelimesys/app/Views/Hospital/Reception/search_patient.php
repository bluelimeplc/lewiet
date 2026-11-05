<?php
    $searchkey = $this->data['searchkey'] ?? '';
    $nop = $this->data['nop'];
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
                <h1>Reception</h1>
                <small>Patient List</small>
                <ol class="breadcrumb">
                    <li class="active"><a href="/hospital/reception/index">New Patient</a></li>
                </ol>
            </div>
        </section>
      
                <!-- Main content -->
        <section class="content">
            <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    echo form_open_multipart('/hospital/reception/searchpatient', $attributes);
            ?>

            <!-- Search Area -->
            <div class="row">
                            
                <div class="col-sm-12 col-md-12">
                    <div class="panel panel-bd">
                        <div class="panel-body">
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <input id="search_patient" type="text" placeholder="<?php echo $nop. " Records" ?>" name="search_patient" class="form-control refocusable" value="<?= $searchkey ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button>
                                    </div>
                                    <div style="text-align: left;margin-top: 15px;">
                                    <input style="transform: scale(1.5);" type="radio" value="1" name="crd_no_only" id="crd_no_only" />&nbsp;
                                    <label class="form-check-label" for="rad_transferFromBranch"> Card Number Only</label><br>
                                    
                                </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                

                
                <!--  Patient List area -->
                <div class="col-sm-12">
                    <div  class="panel panel-default">
                        <div class="panel-body  table-responsive">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="patient_list4">
                                <thead>

                                    <tr>                     
                                        <th>Picture</th>
                                        <th>MRN No</th>
                                        <th>Full Name</th>
                                        <th>Company</th>
                                        <th>Phone Number</th>
                                        <th>Sex</th>
                                        
                                        <th><?php echo lang('Action');?></th> 
                                    </tr>

                                </thead>

                                <tbody>
                                <?php
                                        foreach ($patients as $value) {
                                    ?>
                                    <tr class="odd gradeX">

                                        <td>
                                            <div class="profile-userpic">
                                            
                                                <?php 
                                                
                                                    echo '<img width="50" src="'.base_url().'assets/images/patient.png" class="img-responsive" >';
                                                
                                                ?>
                                                
                                            </div>
                                        </td>

                                        <td><b><?php echo "MRN-".sprintf('%05d', $value['id']); ?></b><br>
                                                <i  id="patient_has_active_visite_icon " class="dental-flag-icon" style="color: orange;" <?= (@$value['patient_has_active_visite'])?"":"hidden" ?> >
                                                    <i class="fa fa-user-md jump_dental" data-toggle="tooltip" data-html="true" title="On Visit"></i>
                                                </i> 
                                        </td>
                                        <td><?php echo $value['first_name'].' '. @$value['middle_name'].' '. @$value['last_name'] ;?></td>
                                        <td style="<?php echo $value['company_id']>0?"background: Moccasin;":" ";?>" ><div><?php echo $value['company_name'];?></div></td>
                                        <td><?php echo $value['mobile'];?></td>
                                        <td><?php echo $value['sex'];?></td>
                                        
                                        <td>
                                            <a  class="btn btn-warning btn-sm" onclick="showwaitscreen(<?php echo $value['id']?>)" title="Add to waiting" data-toggle="tooltip" data-html="true"><i class="fa-regular fa-clock"></i> </a>
                                            <a  class="btn btn-primary btn-sm" href="<?php echo base_url();?>hospital/reception/editpatient?id=<?php echo $value['id'];?>"title="Edit Patient" data-toggle="tooltip" data-html="true"><i class="fa fa-edit"></i> </a>
                                            <?php if($value['sex']!=""){ ?>
                                            <a  class="btn btn-info btn-sm" href="<?php echo base_url();?>hospital/reception/revisit?id=<?php echo $value['id'];?>" title="Visit Page" data-toggle="tooltip" data-html="true"><i class="fa fa-eye"></i></a>
                                            <?php } else {?>
                                            <a href="#?id=<?=$value['id']?>" class='btn btn-danger btn-sm'  data-toggle='modal' data-target="#edit_sex<?php echo $value['id']?>" > <span title="Edit Sex" data-toggle="tooltip" data-html="true"><i class="fa fa-eye"></i><span></a>                                                        
                                            <?php }?>   
                                                 
                                        </td> 
                                    </tr>
                                    <div class="modal fade" id="edit_sex<?php echo $value['id']?>" tabindex="-1" role="dialog" aria-labelledby="EditSex" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Sex Not Selected</h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <h4><?=$value['first_name']. " " .$value['middle_name']. " " .$value['last_name']?></h4><br>
                                                            <label class="control-label">Sex</label>
                                                            <select name="sex" id="<?=$value['id'] ?>" class="form-control">
                                                                <option  value="">--Select Sex--</option>
                                                                <option value="Female" <?php echo (@$value['sex']=='Female')?'selected':'' ?>>Female</option>
                                                                <option value="Male" <?php echo (@$value['sex']=='Male')?'selected':'' ?>>Male</option>
                                                            </select>
                                                                <div id="set_invalidage_flash<?=$value['id'] ?>" class="alert alert-danger hide  col-xs-3" role="alert" style="margin: 0px; display:inline-block; min-width: 50px; text-align:left;">
                                                                    <span></span>
                                                                </div>
                                                        </div>    
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                                                            <input type="button" class="btn btn-primary"  onclick="setSex(<?=$value['id'] ?>)"  value="Save">
                                                        </div>
                                                
                                                </div>
                                        </div>
                                        </div>
                                    <div class="modal fade" id="wait_screen<?php echo $value['id']?>" tabindex="-1"
                                    role="dialog" aria-labelledby="wait_screen" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel"><b>Add to Waiting List</b></h5>
                                            </div>
                                            
                                            <div>
                                            <img src="/img/loading-loading-forever.gif" width="20px;" style="display: none;" id="waitingBusy">
                                            <span class="label label-warning color-violet" id="waitingNo" style="display: none;">200 waiting</span>
                                        </div>
                                            <div style="margin: 20px;">
                                            <p><b>Patient Name:</b> <span id="patient_name<?= $value['id']?>"><?= $value['first_name'].' '. @$value['middle_name'].' '. @$value['last_name'] ?></span></p>
                                            <label for="assigned_doctor<?php echo $value['id']?>" class="control-label"><b>Select Doctor</b></label>    
                                            <select id="assigned_doctor<?php echo $value['id']?>" class="form-control">
                                            <option value="-1"></option>
                                                <?php
                                                    foreach($doctors as $doctor):
                                                ?>
                                                    <option value="<?= $doctor['id'] ?>"><?= $doctor['name'] ?></option>';
                                                    <?php
                                                    endforeach
                                                ?>
                                        </select>
                                        <input type="hidden" id="full_name<?= $value['id']?>" value="<?= $value['first_name'].' '. @$value['middle_name'].' '. @$value['last_name'] ?>" />
                                        <input type="hidden" id="mobile<?= $value['id']?>" value="<?= $value['mobile'] ?>" />
                                        <input type="hidden" id="waitingNoOfPatients" value="<?= $nop ?>" />
                                            
                                            </div>
                                            


                                            <div class="modal-footer">
                                                <input type="button" class="btn btn-secondary" data-dismiss="modal" value="Close">
                                                <input type="button" class="btn btn-primary" onclick="save_waiting(<?= $value['id']?>)" value="Wait" name="wait_button">

                                            </div>
                                            
                                        </div>
                                        
                                    </div>
                                </div>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table> 
                        </div>
                    </div>
                </div>
            </div>            
        </section>

    </div> <!-- /.content-wrapper -->
    <script>
        

    
        //document.onload(focus_search());

        function setSex(pid){
            let new_sex = $('#'+pid).val();
            if(checkSex(new_sex,pid)){
                $.ajax({
                    method: 'post',
                    url: "<?php echo base_url(); ?>hospital/reception/editsex",
                    data: {"pid":pid, "sex": new_sex},
                    
                    success: function(result){
                        window.location.href = "<?php echo base_url()?>hospital/reception/revisit?id="+pid;
                    },
                });
            }
        }
        function checkSex(new_sex,pid){
            if(new_sex == ""){
                error_html = "Invalid Sex";
                dental_show_flash('set_invalidage_flash'+pid, error_html, false);
                return 0;
            }
                return 1;
        }
        function showwaitscreen(id){
                                $("#wait_screen"+id).modal({keyboard: true});
                            }
        function save_waiting(id){
                    
                    let selected_doctor = -1;
                    selected_doctor = $("#assigned_doctor"+id).val();
                    if(!validatedoctor(selected_doctor)){
                        return false;
                    }
                    let full_name = $("#full_name"+id).val();
                    let mobile = $("#mobile"+id).val();
                    $.ajax({
                        method: 'post',
                        url: "<?php echo base_url(); ?>hospital/reception/save_waiting",
                        data: {doctor_id:selected_doctor,patient_id:id,full_name:full_name, mobile:mobile},
                        success: function(result){
                            try{
                                let r = JSON.parse(result);
                                if(r.status == "success"){
                                    $("#wait_screen"+id).modal('hide');
                                    toastr['success']("Patient added to waiting list"); 
                                location.href = "<?php echo base_url()?>hospital/reception/waitinglist";
                                }
                                else if(r.status == "error")
                                    toastr['error'](r.message);
                                else
                                    toastr['error']("Duplicate patient on waiting list");
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
        function validatedoctor(selected_doctor){
            
            if (selected_doctor == -1){
                    toastr['error']("Please Select a Doctor");
                    return false;
            }
            else{               
                return true;
            }
            
            
        }
            
    </script>
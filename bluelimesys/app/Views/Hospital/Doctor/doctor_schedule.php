<?php
    $schedules = $this->data['schedules'];
    $skips = $this->data['skips'];
    $loggeduserdata = $this->data['loggeduserdata'];
    $logged_user_name = $loggeduserdata['name'];
    $logged_user = $loggeduserdata['id'];
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
            <h1>Doctor</h1>
            <small>Add Schedule</small>
            <ol class="breadcrumb">
                <li class="active"><a href="#">Dashboard</a></li>
            </ol>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!--  form area -->

            <div class="col-sm-5">
                <div class="panel panel-bd">
                    <div class="panel-heading ">
                        <div class="panel-title">
                            <h4>My Schedule</h4>
                        </div>
                    </div>
                    <div class="panel-body" style="min-height: 260px;">
                        <div class="portlet-body form">
                            <table class="table table-striped">
                                <tr style="font-weight: bold;">
                                    <td>Day</td>
                                    <td>Start</td>
                                    <td>End</td>
                                    <td>Per Patient</td>
                                    <td>Starting From</td>
                                    <td></td>
                                </tr>
                                <?php
                                    foreach($schedules as $schedule):
                                ?>
                                <tr>
                                    <td><?=\BluelimeSystem::no_to_day($schedule['day']) ?></td>
                                    <td><?=$schedule['start_time'] ?></td>
                                    <td><?=$schedule['end_time'] ?></td>
                                    <td><?=$schedule['per_patient_time'] ." min" ?></td>
                                    <td><?=$schedule['schedule_start_date'] ?></td>
                                    <td>
                                        <button  class="btn btn-danger btn-sm button"  class='btn btn-danger btn-sm' data-toggle='modal'
                                            data-target="#confirm_cancel" data-id=<?=$schedule['id']?>> <i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php
                                    endforeach;
                                ?>
                            </table>
                            <span><b>Add Skip Date</b></span>
                            <hr>
                        </div>
                                <?php
                                    helper('form');
                                    $attributes = array('class' => 'form-horizontal','role'=>'form');
                                    echo form_open('hospital/doctor/addskipdate', $attributes);
                                ?>

                        <div class="col-md-8">
                            <input type="date" name="skip_date" class="form-control col-md-12" required
                                min="<?php echo  date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-4">
                            <input type="submit" class="btn btn-success col-md-12" value="Save">
                        </div>

                        <div>

                            <table class="table table-striped table-bordered table-hover data">
                                <tr>
                                    <td><b>Skip Dates</b></td>
                                </tr>
                                <?php
                                    foreach($skips as $skip):
                                ?>
                                <tr>
                                    <td><?= $skip['skip_date'] ?><a href="deleteskipdate?id=<?= $skip['id'] ?>"
                                            onclick="return confirm('Are you Sure?')"><i class="fa fa-trash"
                                                style="float:right; color:red"></i></a></td>
                                </tr>
                                <?php
                                    endforeach;
                                ?>
                            </table>
                            <?php
                                echo form_close();
                            ?>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-sm-7">
                <div class="panel panel-bd">
                    <div class="panel-heading ">
                        <div class="panel-title" style="max-width: calc(100% - 180px);">
                            <h4>Add Schedule</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="portlet-body form">
                            <?php
                                helper('form');
                                $attributes = array('class' => 'form-horizontal','role'=>'form');
                                echo form_open('hospital/doctor/addschedule', $attributes);
                            ?>
                            <div class="form-body">

                                <div class="form-group">
                                    <label class="col-md-5 control-label"> Doctor :
                                        <?php echo $logged_user_name  ?></label>
                                    <input type="hidden" name="doctor_id" value="<?= $logged_user ?>">
                                    

                                </div>
                                
                                <div class="form-group">
                                    <label class="col-md-3 control-label left"> Select Day :</label>
                                    <div class="col-md-9">
                                        <div class="md-checkbox-inline">
                                            <input id="checkbox7" name="day[]" checked id="day" value="1"
                                                class="md-check" type="checkbox">
                                            <label for="checkbox7"> Mon </label>

                                            <input id="checkbox8" name="day[]" id="day" value="2" class="md-check"
                                                type="checkbox">
                                            <label for="checkbox8">Tue </label>


                                            <input id="checkbox9" name="day[]" id="day" value="3" class="md-check"
                                                type="checkbox">
                                            <label for="checkbox9"> Wed </label>

                                            <input id="checkbox10" name="day[]" id="day" value="4" class="md-check"
                                                type="checkbox">
                                            <label for="checkbox10"> Thu </label>

                                            <input id="checkbox11" name="day[]" id="day" value="5" class="md-check"
                                                type="checkbox">
                                            <label for="checkbox11"> Fri </label>

                                            <input id="checkbox5" name="day[]" id="day" value="6" class="md-check"
                                                type="checkbox">
                                            <label for="checkbox5">Sat</label>

                                            <input id="checkbox6" name="day[]" id="day" value="7" class="md-check"
                                                type="checkbox">
                                            <label for="checkbox6"> Sun </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label">Set Time :</label>
                                    <div class="col-md-7">
                                        <div class="input-group  input-daterange">


                                            <div class="input-group ">
                                                <input type="time" class="form-control" placeholder="Start Time"
                                                    name="s_time" required>
                                                <span class="input-group-addon"> to </span>

                                                <input type="time" class="form-control" placeholder="End Time"
                                                    name="e_time" required>

                                            </div>
                                        </div>

                                        <!-- /input-group -->
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Per Patient Time:</label>
                                    <div class="col-md-6">
                                        <div class=" input-daterange">
                                            <input type="text" name="p_time" class="form-control"
                                                placeholder="In minute" required>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="form-group">
                                <label class="col-md-3 control-label"> Start From:</label>
                                <div class="col-md-6">
                                    <input type="date" name="start_date" class="form-control col-md-4" required
                                min="<?php echo  date('Y-m-d'); ?>">
                                </div>
                        </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-offset-3 col-sm-6">
                                    <button type="reset" class="btn btn-danger">Reset</button>

                                    <button type="submit" class="btn btn-success">Save</button>

                                </div>
                            </div>
                            <?php echo form_close();?>
                        </div>
                    </div>
                </div>
            </div>



    </section>
</div>

<div class="modal fade" id="confirm_cancel" tabindex="-1" role="dialog"  data-backdrop="static" aria-labelledby="CancelAppointment"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <table width="100%"  class="table table-striped activeappointments">
                        <tr>
                            <th>Full Name</th>
                            <th>Time</th>
                            <th>Doctor</th>
                        </tr>
                    </table>
                    <br>
            </div>
            <div id="msgdelete" class="msgdelete alert alert-danger hide" role="alert"><span></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"  onclick='reloadafterdelete()'>Back</button>
                <a href="removescheduleday?id=<?= @$schedule_id ?>" ></a><button class="btn btn-danger" onclick='deleteschedule()'>Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
function deleteschedule() {
    var schedule_id = $('#schedule_id').val();
    $.ajax({ 
                'url': "removescheduleday",
                'type': 'POST', 
                'data': {'schedule_id':schedule_id},
                'success': function(data) {
                    if(data != null){
                                dental_show_flash('msgdelete', data);
                            }
                }
                });
    
}
function loadscheduledactiveappointments(){
        var doctor_id = $('#doctor_id').val();
        var schedule_id = $('#schedule_id').val();
        if (doctor_id!='') {
            $.ajax({ 
                'url': "<?php echo base_url(); ?>hospital/ajax/getscheduledactiveappointments",
                'type': 'POST', 
                'data': {'doctor_id': doctor_id,'schedule_id':schedule_id},
                'success': function(data) {
                    var container = $(".activeappointments");
                    container.html(data);
                    //console.log(data);
                }
                });
            };
        }

$( ".button").on("click", function(event){


$( "#confirm_cancel" ).unbind();
var btn = $(this);
var schedule_id = $(this).data("id"); 
$.ajax({ 
                'url': "<?php echo base_url(); ?>hospital/ajax/getscheduledactiveappointments",
                'type': 'POST', 
                'data': {'schedule_id':schedule_id},
                'success': function(data) {
                    var container = $(".activeappointments");
                    container.html(data);
                    //console.log(data);
                }
                });

});
function reloadafterdelete() {
  document.location.reload();
}

</script>
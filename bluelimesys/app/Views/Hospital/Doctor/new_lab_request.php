<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $formated_mrn_no = "MRN-".sprintf('%05d', $_GET['id']);
    $loggeduserdata = $this->data['loggeduserdata'];
    $loggedusersign = $loggeduserdata['digital_signature'];
    $patient = $this->data['patient'];
    $lab_request = $this->data['lab_request'];
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $fname = $patient['first_name'];
    $mname = $patient['middle_name'];
    $lname = $patient['last_name'];
    $mobile = $patient['mobile'];
    $visit_id = $this->data['visit_id'];
    $patient_id = $patient['id'];
//\BluelimeSystem::show_and_die($visit_id);
?>
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<style>
body {
    background: white;
}

.tooth-chart {
    width: 350px;
}

#Spots polygon,
#Spots path {
    -webkit-transition: fill 0.25s;
    transition: fill 0.25s;
}

#Spots polygon:hover,
#Spots polygon:active,
#Spots path:hover,
#Spots path:active {
    fill: #ff0000 !important;
    cursor: pointer;
}
</style>

<div class="content-wrapper">

    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Doctor</h1>
            <small>Lab Request</small>
            <ol class="breadcrumb">
                <li class="active"><a href="visit?visit_id=<?=$visit_id?>">Visit Page</a></li>
            </ol>
        </div>
    </section>


    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Lab Request</h4>
                        </div>
                    </div>
                    <div class="panel-body" style="min-height: 400px;">
                        <?php
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','role'=>'form', 'id' => 'lab_request_form',  'enctype' => 'multipart/form-data');
                            echo form_open('hospital/doctor/addlabrequest', $attributes);
                        ?>
                        <div class="tooth-chart col-sm-6">
                            <?php echo view('Hospital/Shared/_dental_chart'); ?>

                        </div>
                        <div class="col-sm-6">

                            <br>
                            <div class="col-sm-12">
                                <table class="col-sm-12">

                                    <tr>

                                        <input type="hidden" name="patient_id" value="<?=$patient_id ?>">
                                        <input type="hidden" name="first_name" value="<?=$fname ?>">
                                        <input type="hidden" name="middle_name" value="<?=$mname ?>">
                                        <input type="hidden" name="last_name" value="<?=$lname ?>">
                                        <input type="hidden" name="mobile" value="<?=$mobile ?>">
                                        <input type="hidden" name="visit_id" value="<?=@$_GET['vid']?>">
                                        <input type="hidden" id="teeth_selected" readonly class="form-control"
                                            name="teeth_selected" required>
                                        <td><label>Requested Company :<i class="text-danger">*</i></label><input
                                                class="form-control" type="text" required
                                                placeholder="Requested Company Name" name="company_name"
                                                id="company_name"></td>
                                    </tr>
                                    <tr>
                                        <td><label>Description :<i class="text-danger">*</i></label><textarea
                                                placeholder="Description" required name="description" id="description"
                                                class="form-control"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td><label>Upper Tray</label>
                                            <input class="form-control" required type="number" min="0"
                                                placeholder="Upper Tray" name="upper_tray">
                                        </td>

                                    </tr>
                                    <tr>
                                        <td><label>Lower Tray</label>
                                            <input class="form-control" required type="number" min="0"
                                                placeholder="Lower Tray" name="lower_tray">
                                        </td>

                                    </tr>

                                    <tr>
                                        <td><label>Type :<i class="text-danger">*</i></label>
                                            <select class="form-control" name="type" id="type" required
                                                placeholder="Type">
                                                <option value="">-Select Type-</option>
                                                <option value="Acrylic Blended">Acrylic BRPD Blended</option>
                                                <option value="Chromocobalt BRPD">Chromocobalt BRPD</option>
                                                <option value="Acrylic JC">Acrylic JC</option>
                                                <option value="Acrylic Bridge">Acrylic Bridge</option>
                                                <option value="Ceramic Bridge 2Unit">Ceramic Bridge 2Unit</option>
                                                <option value="Ceramic Bridge 3Unit">Ceramic Bridge 3Unit</option>
                                                <option value="Ceramic Bridge 4Unit">Ceramic Bridge 4Unit</option>
                                                <option value="Ceramic Bridge 5Unit">Ceramic Bridge 5Unit</option>
                                                <option value="Ceramic Bridge 6Unit">Ceramic Bridge 6Unit</option>
                                                <option value="Ceramic Bridge 7Unit">Ceramic Bridge 7Unit</option>
                                                <option value="Ceramic Bridge 8Unit">Ceramic Bridge 8Unit</option>
                                                <option value="Ceramic Bridge 9Unit">Ceramic Bridge 9Unit</option>
                                                <option value="Ceramic JC 1Unit">Ceramic JC 1Unit</option>
                                            </select>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td><label>Special Wish</label><input class="form-control" type="text"
                                                placeholder="" name="special_wish" required></td>
                                    </tr>

                                    <tr>
                                        <?php $tod=date('Y-m-d') ?>
                                        <td><label>Delivery Date :<i class="text-danger">*</i></label><input
                                                class="form-control" type="date" min="<?php echo $tod ?>" required
                                                name="delivery_date" id="delivery_date"></td>

                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <div class="modal fade" id="confirmPin" role="dialog">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header dental-bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">Enter Pin Code</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <input id="pin_entered" type="password" maxlength="4"
                                                        class="form-control" autocomplete="off" pattern="[0-9]+">
                                                    <input id="user_id" type="hidden"
                                                        value="<?=$loggeduserdata['id'] ?>">
                                                    <input class="show" type="hidden" class="form-control">
                                                    <div id="pinCheck_flash" class="alert alert-danger hide"
                                                        role="alert">
                                                        <span></span>
                                                    </div>
                                                    <?php echo '<img style="max-height:150px; max-width:150px;" src="data:image/jpeg;base64,'.base64_encode( $loggedusersign ).'"/>' ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger"
                                                        data-dismiss="modal">Close</button>

                                                    <input class="btn btn-success" value="Confirm" type="button"
                                                        onclick="checkpin()">

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <tr>
                                        <td>
                                            <button class="btn btn-success" style="float:right" type="button"
                                                onclick="return validateForm()">Request</button>
                                        </td>
                                    </tr>



                                </table>
                            </div>


                        </div>
                    </div>

                </div>


            </div>


        </div>


        <div class="row">


            <div class="col-sm-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>View Requests</h4>
                        </div>
                    </div>

                    <div class="panel-body" style="min-height: 400px;">

                        <div class="container col-md-12">
                            <table class="table table-bordered table-striped">
                                <tr style="font-weight: bold;">
                                    <td>Company Name</th>
                                    <td>Delivery Date</td>
                                    <td>Status</td>
                                    <td>Action</td>
                                </tr>
                                <?php
                            foreach($lab_request as $request):
                            ?>

                                <tr>
                                    <td><?=$request['company_name']?></td>
                                    <td><?=$request['delivery_date']?></td>
                                    <td><?=$request['status']==0?"REQUESTED":"DELIVERED"?></td>
                                    <td>
                                        <a href="/hospital/doctor/viewlabrequest?id=<?= @$patient['id']?>&lid=<?= $request['id']?>&vid=<?= $request['visit_id']?>"
                                            class='btn btn-primary'><i class='fa fa-eye'></i></a>
                                        <span <?=$request['status']==0?"":"hidden"?>><a
                                                href="/hospital/doctor/deletelabrequest?id=<?= @$patient['id']?>&lid=<?= $request['id']?>&vid=<?= $request['visit_id']?>"
                                                class='btn btn-danger' onclick="return confirm('Are you Sure?')"><i
                                                    class='fa fa-trash'></i></a< /div>
                                    </td>
                                </tr>

                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>




<script type="text/javascript">





function checkpin() {
    var pin = $("#pin_entered").val();
    var user = $("#user_id").val();
    $.ajax({
        method: 'post',
        url: "<?php echo base_url(); ?>hospital/ajax/checkpin",
        data: {
            "pin_code": pin,
            'id': user
        },
        success: function(data) {
            if (data == 1) {
                document.getElementsByClassName('show')[0].value = data;
                document.getElementById("lab_request_form").submit();
            } else {
                error_html = "Incorrect Pin";
                dental_show_flash('pinCheck_flash', error_html, false);
            }

        }
    });
}

function validateForm() {

    var company_name = $("#company_name").val();
    var description = $("#description").val();
    var type = $("#type	").val();
    var delivery_date = $("#delivery_date").val();
    var teeth_selected = $("#teeth_selected").val();

    if (company_name == "" || description == "" || type == "" || delivery_date == "" ){
        dental_show_alert("Please fill all required fields!");
        
    }
    else if(teeth_selected=="")
            dental_show_alert("Please Select teeth!");
    else
        $('#confirmPin').modal('show');
    return false;

}

var selectedTeeth = [];

function selectTooth(el) {
    let tooth = document.getElementById(el.id);
    let color = tooth.getAttribute("fill");
    let toothNumber = parseInt(tooth.getAttribute("data-key"));

    if (color == "#FFFFFF") {
        tooth.setAttribute("fill", "#FF0000");
        selectedTeeth.push(parseInt(toothNumber));
        //document.getElementById("teeth_selected").value = selectedTeeth;
    } else {
        tooth.setAttribute("fill", "#FFFFFF");
        removeTooth(toothNumber);
        //document.getElementById("teeth_selected").value = selectedTeeth;
    }
    el.classList.toggle("SpotsSelected");
}

 
function tooth_clicked(){
    $('#teeth_selected').val(selectedTeeth);
}

function set_polygon_click(){
    $('polygon').click(tooth_clicked);
    $('path').click(tooth_clicked);
}

set_polygon_click();


function selectToothByNumber(toothNumber) {
    let el = document.getElementById('Tooth' + toothNumber);
    selectTooth(el);
}

function removeTooth(toothNumber) {
    let selectedTeethTemp = [];
    selectedTeeth.forEach((item, index) => {
        if (item != toothNumber)
            selectedTeethTemp.push(item);
    });
    selectedTeeth = selectedTeethTemp;
}
</script>
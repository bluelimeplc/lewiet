<?php
    $drugs=$this->data['drugs'];
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $loggeduserpin = $loggeduserdata['pin_code'];
    $loggedusersign = $loggeduserdata['digital_signature'];
    $patient = $this->data['patient'];
    $patient_id = $patient['id'];
    $patient_name = $patient['first_name']." ".$patient['middle_name']." ".$patient['last_name'];
    $mobile=$patient['mobile'];
    $visit_id = $_GET['vid'];
    $age = \BluelimeSystem::year_difference(date("Y-m-d"), $patient['date_of_birth']);
?>
<script src="<?php echo base_url()?>assets/my-assets/md5.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/my-assets/core.min.js" type="text/javascript"></script>
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1>Doctor</h1>
                <small>New Prescription</small>
                <ol class="breadcrumb">
                    <li class="active"><a href="visit?visit_id=<?=$visit_id?>">Visit Page</a></li>
                </ol>
            </div>
    </section>


    <!-- Main content -->
    <section class="content">
        

	    <div class="row">
	    	<div class="row">
            <div class="col-sm-12">

       
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Add Prescription <button onclick="showPrescriptionAutoFill()">Prescription auto fill</button></h4>
                            
                        </div>
                    </div>
                    <div style="float:left">
                        <label for="checkbox7"> Custom </label>
                        <input type="checkbox" class="form-control" id="type_change" onchange="customPrescription()" >
                    </div>
                    
                    
                    <div class="panel-body" id = "default" >
                        
                        <div class="table-responsive" style="margin-top: 10px" >
                        
                            <table class="table table-bordered table-hover" id="prescription">
                                
                                <thead>
                                    <tr>
                                        <th class="text-center" style="font-size: smaller;"><i class="glyphicon glyphicon-plus-sign dental-add-icon" title="Add new drug"  onclick="show_add_new_modal('Add New Drug', 'drug', drug_list)" ></i>Drug Name<i class="text-danger">*</i></th>
                                        
                                        <th class="text-center" style="font-size: smaller;">Dose<i class="text-danger">*</i></th>
                                        <th class="text-center" style="font-size: smaller;">Route<i class="text-danger">*</i></th>
                                        <th class="text-center" style="font-size: smaller;">Direction<i class="text-danger">*</i></th>
                                        <th class="text-center" style="font-size: smaller;">No.(days)<i class="text-danger">*</i></th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="adddrug">

                                    <tr>

                                        <td class="text-right col-md-6">
                                            <input  class="form-control" placeholder="Drug Name"  id="drug_name" type="text" name="drug_name" required>
                                            
                                        </td>

                                       

                                        <td class="text-right col-md-1">
                                            <input name="dose[]" id="dose" class="form-control text-right"  min="1" type="number" size="3">
                                        </td>

                                        <td class="text-right col-md-2">
                                            <select name="route[]"  id="route" class="form-control text-right" >
                                                <option></option>
                                                <option value='PO'>PO</option>
                                                <option value='SL'>SL</option>
                                                <option value='IM'>IM</option>
                                                <option value='IV'>IV</option>
                                                <option value='SQ'>SQ</option>
                                                <option value='INTERDERMAL'>INTERDERMAL</option>
                                                <option value='TRANSDERMAL'>TRANSDERMAL</option>
                                                <option value='TOPICAL'>TOPICAL</option>
                                                <option value='RECTAL'>RECTAL</option>
                                            </select>
                                            
                                        </td>
  
                                        <td class="text-right col-md-2">
                                            <select name="direction[]"  id="direction" class="form-control text-right" >
                                                <option></option>
                                                <option value='OD'>OD</option>
                                                <option value='BID'>BID</option>
                                                <option value='TID'>TID</option>
                                                <option value='QID'>QID</option>
                                                <option value='PRN'>PRN</option>
                                                <option value='STAT'>STAT</option>
                                                
                                            </select>
                                            
                                        </td>
                                       
                                        <td class="text-right col-md-1">
                                            <input class="form-control text-right" name="nod[]" id="nod" tabindex="-1" min="1" type="number">
                                        </td>

                                        <td>
                                        <button id="add-drug" class="btn btn-info" name="add-drug" onclick="addDrug('druglist');" type="button"><i class='fa fa-plus' ></i></button>
                                        </td>

                                    </tr>

                                <tfoot>
                                                
                                

                                </tfoot>
                            </table>

                            
                        </div>
                        <?php
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','role'=>'form', 'id' => 'prescription_form', 'onsubmit' => 'return validateDrugList()');
                            echo form_open('hospital/doctor/addprescription', $attributes);
                        ?>                                
                        <div class="form-group row col-sm-7">
                        <input name="patient_id" value="<?php echo $patient_id ?>" type="hidden">
                        <input name="patient_name" value="<?php echo $patient_name ?>" type="hidden">
                        <input name="mobile" value="<?php echo $mobile ?>" type="hidden">
                        <input name="is_custom" value="0" type="hidden">
                        <input name="visit_id" value="<?php echo $_GET['vid'] ?>" type="hidden">
                        <table class="table table-bordered table-striped"  id="druglist">
                                <tr>
                                    <td style="width: 200px;">Drug</td>
                                    <td>Dose</td>
                                    <td>Route</td>
                                    <td>Direction</td>
                                    <td>NOD</td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                        &emsp;
                        <div class="form-group row col-sm-5">
                            <textarea class="form-control" name="comment" rows="6" style="resize:none" placeholder="Prescription Comment"></textarea>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-sm-offset-4 col-sm-4" style="float: right;">
                                <input class="btn btn-success" value="Order Prescription" type="button"  onclick="return validateDrugList()" >
                            </div>
                        </div>
                       <?php
                            echo form_close();
                       ?>                                     

                    </div>
                        <?php
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','role'=>'form', 'id' => 'prescription_form_custom', 'onsubmit' => 'return validateDrugList()');
                            echo form_open('hospital/doctor/addprescription', $attributes);
                        ?>
                    <div class="panel-body" id = "custom">
                    <div class="col-md-12" style="min-height:100px">
                       
                            <div class="col-md-4" >
                                <input list="item_custom" class="form-control" placeholder="Drug Name"  id="drug_name_custom" type="text" name="drug_name_custom" onchange="resetIfInvalid(this);" required>
                           
                            <datalist id="item_custom">
                                
                                <?php
                                    foreach($drugs as $drug):
                                ?>
                                    <option value="<?php echo $drug['id'] ?>-<?php echo $drug['item_generic_name'] ?>"><?php echo $drug['item_brand_name'] ?></option>
                                    
                                <?php
                                    endforeach
                                ?>
                            </datalist>
                            </div>
                        <div class="col-md-4" >
                    <textarea class="form-control" name="comment_custom" id="comment_custom" rows="3" style="resize:none" placeholder="Rx"></textarea>
                        </div>
                                        <button id="add-drug" class="btn btn-info" name="add-drug" onclick="addDrugCustom('druglist_custom');" type="button"><i class='fa fa-plus' ></i></button>
                                       
                    </div>
                    <div class="form-group row col-md-8">
                        <input name="patient_id" value="<?php echo $patient_id ?>" type="hidden">
                        <input name="patient_name" value="<?php echo $patient_name ?>" type="hidden">
                        <input name="mobile" value="<?php echo $mobile ?>" type="hidden">
                        <input name="visit_id" value="<?php echo $_GET['vid'] ?>" type="hidden">
                        <input name="is_custom" value="1" type="hidden">
                        <table class="table table-striped" id="druglist_custom">
                            <tr>
                                <td>Drug Name</td>
                                <td>Rx</td>
                                <td>Action</td>
                            </tr>
                        </table>
                    </div>
                    <div class="form-group row">
                            <div class="col-sm-offset-4 col-sm-4" style="float: right;">
                                <input class="btn btn-success" value="Order Prescription" type="button" onclick="return validateDrugListCustom()" >
                            </div>
                    </div>
                    </div>
                    <?php echo form_close()?>
                </div>
            </div>
        </div>

	    </div>  
        <div class="modal fade" id="confirmPin" role="dialog">
            <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header dental-bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Enter Pin Code</h4>
                </div>
                <div class="modal-body">
                    <input id="pin_entered" type="password" maxlength="4" class="form-control" autocomplete="new-password" pattern="[0-9]+" >
                    <input id="user_id" type="hidden" value="<?=$loggeduserdata['id'] ?>">
                    <div id="pinCheck_flash" class="alert alert-danger hide" role="alert">
                    <span></span>
                </div>
                    <?php echo '<img style="height:150px;" src="data:image/jpeg;base64,'.base64_encode( $loggedusersign ).'"/>' ?>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                
                <input class="btn btn-success" value="Confirm" type="button"  onclick="checkpin()" >
                    
                </div>
            </div>
            </div>
        </div>

        <div class="modal fade" id="confirmPinCustom" role="dialog">
            <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header dental-bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Enter Pin Code</h4>
                </div>
                <div class="modal-body">
                    <input id="pin_entered_custom" type="password" maxlength="4" class="form-control" autocomplete="new-password" pattern="[0-9]+" >
                    <input id="user_id_custom" type="hidden" value="<?=$loggeduserdata['id'] ?>">
                    <div id="pinCheck_flash_custom" class="alert alert-danger hide" role="alert">
                    <span></span>
                </div>
                    <?php echo '<img style="max-height:150px; max-width:150px;" src="data:image/jpeg;base64,'.base64_encode( $loggedusersign ).'"/>' ?>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                
                <input class="btn btn-success" value="Confirm" type="button"  onclick="checkpincustom()" >
                    
                </div>
            </div>
            </div>
        </div>

        <div class="modal fade" id="prescription_auto_fill" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Prescription auto fill</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="input-group">
                                <span class="input-group-addon">Drug Name</span>
                                <input id="paf_drug_name" list="item" type="text" class="form-control" onchange="fetch_paf()">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">Age range</span>
                                <input id="paf_age_range" type="text" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">Dose</span>
                                <input id="paf_dose" min="1" type="number" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">Route</span>
                                <select id="paf_route" class="form-control text-right" >
                                    <option></option>
                                    <option value='PO'>PO</option>
                                    <option value='SL'>SL</option>
                                    <option value='IM'>IM</option>
                                    <option value='IV'>IV</option>
                                    <option value='SQ'>SQ</option>
                                    <option value='INTERDERMAL'>INTERDERMAL</option>
                                    <option value='TRANSDERMAL'>TRANSDERMAL</option>
                                    <option value='TOPICAL'>TOPICAL</option>
                                    <option value='RECTAL'>RECTAL</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">Direction</span>
                                <select id="paf_direction" class="form-control text-right" >
                                    <option></option>
                                    <option value='OD'>OD</option>
                                    <option value='BID'>BID</option>
                                    <option value='TID'>TID</option>
                                    <option value='QID'>QID</option>
                                    <option value='PRN'>PRN</option>
                                    <option value='STAT'>STAT</option>
                                </select>
                                
                            </div>
                            <div class="input-group">
                                <span class="input-group-addon">Nod</span>
                                <input id="paf_nod" type="number" class="form-control">
                            </div>
                            <button class="form-control btn btn-success" onclick="add_paf()">Add</button>
                        </div>
                        <hr>
                        <div>
                            <div>Drug Name: <b id="paf_drug_name_bold"></b></div>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Age range</th><th>Dose</th><th>Route</th><th>Direction</th><th>N<u>o</u> Days</th>
                                </tr>
                                <tbody id="paf_list">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    var patient_age = <?= $age; ?>;

    function fetch_paf(){
        let paf_drug_name = document.getElementById("paf_drug_name");
        
        if(!resetIfInvalid(paf_drug_name)){
            $("#paf_list").html("");
            $("#paf_drug_name_bold").html("");
            return false;
        }

        let drug_name_with_id = $("#paf_drug_name").val();
        
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/ajax/get_prescription_auto_fill_list",
            data: {drug_name_with_id:drug_name_with_id},
            success: function(result) {
                try{
                    let r = JSON.parse(result);
                    let list_str = "";
                    let drug_name = "";
                    for(let i=0; i< r.length; i++){
                        list_str +=  '<tr id="paf_list_'+r[i].id+'">'
                                    +'  <td>'+ r[i].age_from + '-' + r[i].age_to +'</td>'
                                    +'  <td>'+ r[i].dose + '</td>'
                                    +'  <td>'+ r[i].route + '</td>'
                                    +'  <td>'+ r[i].direction + '</td>'
                                    +'  <td>'+ r[i].nod + '</td>'
                                    +'  <td><button onclick="delete_paf('+r[i].id+')" class="btn btn-danger"><i class="fa fa-trash"></i></button></td>'
                                    +'</tr>';
                        drug_name = r[i].drug_name;
                    }

                    $("#paf_list").html(list_str);
                    $("#paf_drug_name_bold").html(drug_name);
                    
                }
                catch(e){
                    alert("Error\n"+e);
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                error_html = "Error while fetching data.\nError:\n" + errorThrown;
                alert(error_html);
            }
        });
    }

    function delete_paf(paf_id){
        let tr = $("#paf_list_"+paf_id);

        $.ajax({
            method: 'get',
            url: '/hospital/ajax/remove_prescription_auto_fill?paf_id='+paf_id,
            success:function(result){
                try{
                    let r = JSON.parse(result);
                    if(r.status == "success"){
                        tr.remove();
                    }
                }
                catch(e){
                    alert("Error\n"+e);
                }
            }
        });
    }

    function suggest_paf(drug_name_input){
        let drug_name_with_id = drug_name_input.value;

        if(drug_name_with_id == "")
            return false;

        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/ajax/get_prescription_suggestion",
            data: {drug_name_with_id:drug_name_with_id, patient_age:patient_age},
            success: function(result) {
                try{
                    let r = JSON.parse(result);
                    if(r.length > 0){
                        $("#dose").val(r[0].dose);
                        $("#route").val(r[0].route);
                        $("#direction").val(r[0].direction);
                        $("#nod").val(r[0].nod);
                    }
                }
                catch(e){
                    alert("Error\n"+e);
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                error_html = "Error while fetching data.\nError:\n" + errorThrown;
                alert(error_html);
            }
        });
    }

    function add_paf(){
        let drug_name_with_id = $("#paf_drug_name").val();
        let age_range = $("#paf_age_range").val();
        let dose = $("#paf_dose").val();
        let route = $("#paf_route").val();
        let direction = $("#paf_direction").val();
        let nod = $("#paf_nod").val();

        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/ajax/add_prescription_auto_fill",
            data: {drug_name_with_id:drug_name_with_id, age_range:age_range, dose:dose, route:route, direction:direction, nod:nod},
            success: function(data) {
                fetch_paf();
            }
        });
    }

 $(document).on("keydown", ":input:not(drug_list_input, textarea)", function(event) {
                        return event.key != "Enter";});

                var substringMatcher = function(strs){
                    return function findMatches(q, cb) {
                        var matches, substringRegex;
                
                        // an array that will be populated with substring matches
                        matches = [];
                    
                        // regex used to determine if a string contains the substring `q`
                        substrRegex = new RegExp(q, 'i');
                    
                        // iterate through the pool of strings and for any string that
                        // contains the substring `q`, add it to the `matches` array
                        $.each(strs, function(i, str) {
                            if (substrRegex.test(str)) {
                            matches.push(str);
                            }
                        });
                    
                        cb(matches);
                    };
                };
var drug_list = <?= \BluelimeSystem::convert_array_to_string($drugs); ?>;


$('#drug_list_input').tagsinput({
                    freeInput: false,
                    tagClass: 'tag label dental-tagsinput-tag',
                    typeaheadjs:({
                        hint: true,
                        highlight: true,
                        minLength: 1
                    }, {
                        name: "drugs",
                        source: substringMatcher(drug_list)
                    })
                });

function addDrug(t) {
    
    var drug_name  = $("#drug_name").val();
    var drug_id  = $("#drug_id").val();
    var dose = $("#dose").val();
    var route = $("#route").val();
    var direction = $("#direction").val();
    var nod = $("#nod").val();
    var a = "drug_name" + count;
        e = document.createElement("tr");

    if(drug_name!="" && dose!="" && route!="" && direction!="" && nod!=""){

    document.getElementById("drug_name").value = "";    
    document.getElementById("dose").value = "";    
    document.getElementById("route").value = "";    
    document.getElementById("direction").value = "";    
    document.getElementById("nod").value = "";    
    
    e.innerHTML = "<td style='width: 200px;'><input name='drug_name[]' type='text' value='"
    +drug_name+"' readonly required class='form-control' id='" + a + "'><input name='drug_id' type='hidden' value='"
    +drug_id+"' class='form-control'></td><td><input name='dose[]' type='text' value='"
    +dose+"' readonly class='form-control'></td><td><input name='route[]' type='text' value='"
    +route+"' readonly class='form-control'></td><td><input name='direction[]' type='text' value='"
    +direction+"' readonly class='form-control'></td><td><input name='nod[]' type='text' value='"
    +nod+"' readonly class='form-control'></td><td><button type='button' onclick='deleteRow(this)'"
    +"class='btn btn-danger'><i class ='fa fa-trash'></i></button></td>",
    document.getElementById(t).appendChild(e), count++

    }

    else
    dental_show_alert("Please fill all required fields")

}

function addDrugCustom(t) {
    
    var drug_name_custom  = $("#drug_name_custom").val();
    var drug_id_custom  = $("#drug_id_custom").val();
    var comment_custom = $("#comment_custom").val();
    var a = "drug_name_custom" + count_custom;
        e = document.createElement("tr");
    if(drug_name_custom!="" && comment_custom!=""){

    document.getElementById("drug_name_custom").value = "";    
    document.getElementById("comment_custom").value = "";    
    
    e.innerHTML = "<td style='width: 200px;'><input name='drug_name_custom[]' type='text' value='"
    +drug_name_custom+"' readonly required class='form-control' id='" + a + "'><input name='drug_id_custom' type='hidden' value='"
    +drug_id_custom+"' class='form-control'></td><td><input name='comment_custom[]' type='text' value='"
    +comment_custom+"' readonly class='form-control'></td><td><button type='button' onclick='deleteRowCustom(this)'"
    +"class='btn btn-danger'><i class ='fa fa-trash'></i></button></td>",
    document.getElementById(t).appendChild(e), count_custom++

    }

    else
    dental_show_alert("Please fill all required fields")

}

function addDrugCustom(t) {
    
    var drug_name_custom  = $("#drug_name_custom").val();
    var drug_id_custom  = $("#drug_id_custom").val();
    var comment_custom = $("#comment_custom").val();
    var a = "drug_name_custom" + count_custom;
        e = document.createElement("tr");
    if(drug_name_custom!="" && comment_custom!=""){

    document.getElementById("drug_name_custom").value = "";    
    document.getElementById("comment_custom").value = "";    
    
    e.innerHTML = "<td style='width: 200px;'><input name='drug_name_custom[]' type='text' value='"
    +drug_name_custom+"' readonly required class='form-control' id='" + a + "'><input name='drug_id_custom' type='hidden' value='"
    +drug_id_custom+"' class='form-control'></td><td><input name='comment_custom[]' type='text' value='"
    +comment_custom+"' readonly class='form-control'></td><td><button type='button' onclick='deleteRowCustom(this)'"
    +"class='btn btn-danger'><i class ='fa fa-trash'></i></button></td>",
    document.getElementById(t).appendChild(e), count_custom++

    }

    else
    dental_show_alert("Please fill all required fields")

}

               
function validateDrugList(){

    if(count <= 1)
        dental_show_alert("Please Add Drug to the List");
    else
        $('#confirmPin').modal('show');
    return false;

}

function validateDrugListCustom(){

if(count_custom <= 1)
    dental_show_alert("Please Add Drug to the List");
else
    $('#confirmPinCustom').modal('show');
return false;

}

function showPrescriptionAutoFill(){
    $('#prescription_auto_fill').modal('show');
}


function checkpin(){
    var pin = $("#pin_entered").val();
    var user = $("#user_id").val();
    $.ajax({
        method: 'post',
        url: "<?php echo base_url(); ?>hospital/ajax/checkpin",
        data: {"pin_code":pin,'id':user},
        success: function(data) { 
                if(data == 1){
                    document.getElementById("prescription_form").submit();
                }
                else{
                    error_html = "Incorrect Pin";
                    dental_show_flash('pinCheck_flash', error_html, false);
                }

        }
    });
}

function checkpincustom(){
    var pin = $("#pin_entered_custom").val();
    var user = $("#user_id_custom").val();
    $.ajax({
        method: 'post',
        url: "<?php echo base_url(); ?>hospital/ajax/checkpin",
        data: {"pin_code":pin,'id':user},
        success: function(data) { 
                if(data == 1){
                    document.getElementById("prescription_form_custom").submit();
                }
                else{
                    error_html = "Incorrect Pin";
                    dental_show_flash('pinCheck_flash_custom', error_html, false);
                }

        }
    });
}

function deleteRow(t) {
    
    var a = $("#druglist").length;
    var e = t.parentNode.parentNode;
    e.parentNode.removeChild(e);
    
}
function deleteRowCustom(t) {
    
    var a = $("#druglist_custom").length;
    var e = t.parentNode.parentNode;
    e.parentNode.removeChild(e);
    
}

var count_custom = 1
var count = 1,
    limits = 10;
    
function customPrescription() {
  
  var checkBox = document.getElementById("type_change");
 
  var x = document.getElementById("default");
  var y = document.getElementById("custom");

  if (checkBox.checked == true){
    x.style.display = "none";
    y.style.display = "block";
  } else {
    x.style.display = "block";
    y.style.display = "none";
  }
}

//document.onload(customPrescription());
$(document).ready(function(){
    customPrescription();
});

function resetIfInvalid(el){

   if (el.value == "")
       return false;
   var options = el.list.options;
   for (var i = 0; i< options.length; i++) {
       if (el.value == options[i].value)
           return true;
   }
   el.value = "";
   $("#dose").val('');
    $("#route").val('');
    $("#direction").val('');
    $("#nod").val('');
}


</script>

<script>
var item = $('#prescription_detail_item');
var quantity = $('#prescription_detail_quantity');
var current_balance  = $('#current_balance');

var prescription_detail_data = {};
var number_of_line = 0;

function clear_prescription_line() {
    item.val('');
    quantity.val('');
    current_balance.val('');
    document.getElementById("prescription_detail_quantity").style.removeProperty('border');
    document.getElementById("prescription_detail_item").style.removeProperty('border');
    item.focus();
}

function add_prescription_line()
    {
        if(!validate_add_prescription_line())
            return false; 
        number_of_line++;

        let selected_item = items[selected_item_array_id];
        
        let new_data = {"id":selected_item.id, "item_code":selected_item.item_code, "item_category":selected_item.item_category, "item_generic_name":selected_item.item_generic_name, "item_brand_name":selected_item.item_brand_name, "measurement":selected_item.measurement, "quantity":quantity.val()};
        
        prescription_detail_data['line_'+number_of_line] = new_data;

        let new_row_html = '<tr id="prescription_detail_line_tr_'+number_of_line+'">'+
                                '<td><span class="badge" style="float:right">' + selected_item.item_code + '</span><b>'+ selected_item.item_generic_name +'</b><br>' +  selected_item.item_brand_name + '</td>'+
                                '<td>'+selected_item.current_balance+'</td>'+
                                '<td>'+new_data.quantity+'</td>'+
                                '<td><a role="button" class="fa fa-minus-circle text-danger" onclick="remove_prescription_line('+number_of_line+')"></a></td>'+
                            '</tr>';
        
        let prescription_detail_line = $('#prescription_detail_line');
        prescription_detail_line.append(new_row_html);
        calculate_total();
        clear_prescription_line();
    }
    function validate_add_prescription_line(){
        let prescription_detail_quantity = $('#prescription_detail_quantity');
        let sel_item =  $('#prescription_detail_item');
        
        if(sel_item.val() === '')
        {
            document.getElementById("prescription_detail_item").style.borderColor = 'red';
            prescription_detail_item.focus();
            return false;
        }
        else if(parseFloat('0'+prescription_detail_quantity.val()) <= 0)
        {
            document.getElementById("prescription_detail_quantity").style.borderColor = 'red';
            prescription_detail_quantity.focus();
            return false;
        }
        
        return true;
    }

function remove_prescription_line(line_number) {
    $('#prescription_detail_line_tr_' + line_number).remove();
    delete prescription_detail_data['line_' + line_number];
    calculate_total();
}

function calculate_total() {
    let prescription_detail_no_of_items = $('#prescription_detail_no_of_items');
    let prescription_detail_total_price = $('#prescription_detail_total_price');

    prescription_detail_no_of_items.html(Object.keys(prescription_detail_data).length);

    let total_price = 0;
    jQuery.each(prescription_detail_data, function(i, val) {
        total_price += val.unit_price * val.quantity;
    });

    prescription_detail_total_price.html(total_price);
}

function save_prescription() {
    let prescription_code = $('#prescriptioncode').val();
    let request_date = $('#request_date').val();
    let requesting_store = 2;
    let requested_store = 1;


    $.ajax({
        url: '/hospital/doctor/prescriptionsave',
        data: {
            prescription_main: {
                prescription_code: prescription_code,
                request_date: request_date,
                requesting_store: requesting_store,
                requested_store: requested_store,
            },
            prescription_detail: prescription_detail_data
        },
        method: 'POST',
        async: false,
        dataType: 'json',
        success: function(result) {
            location = '/hospital/doctor/viewprescription?pid='+result.value;
        },
        error: function(jqXHR, status, error) {
            alert(error);
        }
    });
}
</script>

<script>
    var selected_item_array_id = -1;
    
    function autocomplete(inp, arr) {
      /*the autocomplete function takes two arguments,
      the text field element and an array of possible autocompleted values:*/
      var currentFocus;
      /*execute a function when someone writes in the text field:*/
      inp.addEventListener("input", function(e) {
          var a, b, i, val = this.value;
          /*close any already open lists of autocompleted values*/
          closeAllLists();
          if (!val) { return false;}
          currentFocus = -1;
          /*create a DIV element that will contain the items (values):*/
          a = document.createElement("DIV");
          a.setAttribute("id", this.id + "bt_autocomplete-list");
          a.setAttribute("class", "bt_autocomplete-items bt_autocomplete");
          /*append the DIV element as a child of the autocomplete container:*/
          this.parentNode.appendChild(a);
          /*for each item in the array...*/
          for (i = 0; i < arr.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            /*+++++++++++++++++++++++++++++++++++++++++++*/
            if ((arr[i].item_generic_name.substr(0, val.length).toUpperCase() == val.toUpperCase()) || (arr[i].item_brand_name.substr(0, val.length).toUpperCase() == val.toUpperCase()) || (arr[i].item_code.substr(0, val.length).toUpperCase() == val.toUpperCase()) ) {
            //if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
              /*create a DIV element for each matching element:*/
              b = document.createElement("DIV");
              /*make the matching letters bold:*/
              let inner_html = '';
              inner_html += '<span class="badge " style="float:right">' + arr[i].item_code + '</span>';
              inner_html += '<b>';
              inner_html += "<span class=\"selected\">" + arr[i].item_generic_name.substr(0, val.length) + "</span>";
              inner_html += arr[i].item_generic_name.substr(val.length);
              inner_html += '</b><br>';
              /*insert a input field that will hold the current array item's value:*/
              inner_html += arr[i].item_brand_name;
              inner_html += '<input type="hidden" value="' + i + '">';
              inner_html += '<input type="hidden" value="' + arr[i].item_brand_name + '">';
              b.innerHTML = inner_html;
              /*execute a function when someone clicks on the item value (DIV element):*/
              b.addEventListener("click", function(e) {
                  /*insert the value for the autocomplete text field:*/
                    inp.value = this.getElementsByTagName("input")[1].value;
                    selected_item_array_id = this.getElementsByTagName("input")[0].value;
                    let selected_item = items[selected_item_array_id];
                    document.getElementById("current_balance").value = selected_item.current_balance;
                  /*close the list of autocompleted values,
                  (or any other open lists of autocompleted values:*/
                  closeAllLists();
              });
              a.appendChild(b);
            }
          }
      });
      /*execute a function presses a key on the keyboard:*/
      inp.addEventListener("keydown", function(e) {
          var x = document.getElementById(this.id + "bt_autocomplete-list");
          if (x) x = x.getElementsByTagName("div");
          if (e.keyCode == 40) {
            /*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
            currentFocus++;
            /*and and make the current item more visible:*/
            addActive(x);
          } else if (e.keyCode == 38) { //up
            /*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
            currentFocus--;
            /*and and make the current item more visible:*/
            addActive(x);
          } else if (e.keyCode == 13) {
            /*If the ENTER key is pressed, prevent the form from being submitted,*/
            e.preventDefault();
            if (currentFocus > -1) {
              /*and simulate a click on the "active" item:*/
              if (x) x[currentFocus].click();
            }
          }
      });
      function addActive(x) {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("bt_autocomplete-active");
      }
      function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++) {
          x[i].classList.remove("bt_autocomplete-active");
        }
      }
      function closeAllLists(elmnt) {
        /*close all autocomplete lists in the document,
        except the one passed as an argument:*/
        var x = document.getElementsByClassName("bt_autocomplete-items");
        for (var i = 0; i < x.length; i++) {
          if (elmnt != x[i] && elmnt != inp) {
            x[i].parentNode.removeChild(x[i]);
          }
        }
      }
      /*execute a function when someone clicks in the document:*/
      document.addEventListener("click", function (e) {
          closeAllLists(e.target);
      });
    }
    var items = <?= \BluelimeSystem::convert_array_to_string($drugs) ?>;

    autocomplete(document.getElementById("drug_name"), items);
    </script>

<?php
    // Include Add new modal with its Ajax js
    echo view('Include/_add_new_general');
?>

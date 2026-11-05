<?php
    $drugs=$this->data['drugs'];
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $loggeduserpin = $loggeduserdata['pin_code'];
    $loggedusersign = $loggeduserdata['digital_signature'];
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $patient = $this->data['patient'];
    $patient_id = $patient['id'];
    $patient_name = $patient['first_name']." ".$patient['middle_name']." ".$patient['last_name'];
    $mobile=$patient['mobile'];
    $company_id=$patient['company_id'];
    $company_name=$patient['company_name'];
    $visit_id = $_GET['visit_id'];
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
                    <div class="row">
                        <div class="col-lg-6">
                        
                            <div class="panel panel-bd">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                    <button class="btn btn-primary" onclick="show_compounding()" style="float: right;">Compounding </button>

                                        <h4>Prescription<a onclick="showPrescriptionAutoFill()" class=""> <i class="fa fa-gear" style="font-size:22px;color:green; cursor:pointer"  title="Manage Auto Fill" data-toggle="tooltip" data-html="true"></i></a></h4>
                                    </div>
                                </div>
                                
                                <div class="panel-body" id="default">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th class="text-center" style="font-size: smaller;" width="200">Drug Name<i class="text-danger">*</i></th><td></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input class="form-control focus_input" id="prescription_detail_item" type="text" name="drug_name" required onchange="check_if_selected_or_new_item()">
                                            </td>
                                            <td>
                                                <label for="new_drug" id="new_drug_lable" class="hide" style="position:absolute;"><input type="checkbox" id="new_drug"> New drug</label>
                                                <div id="drug_detail"></div>
                                            </td>
                                        </tr>
                                    </table>
                                    <div>
                                        <label class="control-" for="is_custom" style="text-align: left;">Custom</label>
                                        <label class="switch">
                                            <input type="checkbox" autocomplete="off" id="is_custom" onchange="is_custom_on_off()">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    <div>
                                        <textarea id="prescription_remark" class="form-control hide" rows="8"></textarea>
                                    </div>
                                    <div id="detail_mode">
                                        <table class="table table-bordered table-hover">
                                            <tr>
                                                <th class="text-center" style="font-size: smaller;">Route<i class="text-danger">*</i></th>
                                                <th class="text-center" style="font-size: smaller;">Direction<i class="text-danger">*</i></th>
                                                <th class="text-center" style="font-size: smaller;">N<u>o</u> days<i class="text-danger">*</i></th>
                                                
                                            </tr>
                                            <tr>
                                                <td>
                                                    <select name="route[]" id="route" class="form-control">
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

                                                <td>
                                                    <select name="direction[]" id="direction" class="form-control">
                                                        <option></option>
                                                        <option value='OD'>OD</option>
                                                        <option value='BID'>BID</option>
                                                        <option value='TID'>TID</option>
                                                        <option value='QID'>QID</option>
                                                        <option value='PRN'>PRN</option>
                                                        <option value='STAT'>STAT</option>
                                                    </select>

                                                </td>

                                                <td width="80">
                                                    <input class="form-control text-right" name="nod[]" id="nod" tabindex="-1" min="1" type="number">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="panel-footer" >
                                    <button  style="float: right;" class="btn btn-success" onclick="add_drug_to_prescription()">Add <i class="fa fa-arrow-right"></i> </button>
                                    <a role="button" onclick="save_prescription_as_auto_fill()" class="text-primary"><i class="fa fa-save fa-2x"></i> Save as auto fill</a>
                                    <br><br><a role="button" onclick="showPrescriptionAutoFill()">Auto fill settings <i class="fa fa-gear"></i> </a>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="panel panel-bd" style="border:solid 1px;">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <div style="text-align: center;">
                                            <b>Prescription<br>
                                                <u>RANK Specialized Dermatology Clinic</u>
                                            </b>
                                            <hr>
                                            <b>Name: </b><?= $patient_name ?> | <b>Sex: </b><?= $patient['sex'] ?> | <b>Age: </b><?= BluelimeSystem::year_difference($patient['date_of_birth'], date('Y-m-d'))  ?> | <b>Date: </b><?= date('Y-m-d') ?>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="panel-body" id="default" style="min-height: 250px;">
                                    <div id="prescription_list"></div>
                                    <div id="compounding_list"></div>
                                </div>
                                <div class="panel-footer">
                                    <button id="send_to_pharmacy_btn" class="btn btn-warning" onclick="send_to_pharmacy()">Send to pharmacy</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form onsubmit="event.preventDefault(); checkpin();">
        <div class="modal fade" id="confirmPin" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Enter Pin Code</h4>
                    </div>
                    <div class="modal-body">
                        <input id="pin_entered" placeholder="Enter pin" type="password" maxlength="4" class="form-control focus_input_pin"
                            autocomplete="off" pattern="[0-9]+" autofocus >
                        <input id="user_id" type="hidden" value="<?=$loggeduserdata['id'] ?>">
                        <div id="pinCheck_flash" class="alert alert-danger hide" role="alert">
                            <span></span>
                        </div>
                        <div style="text-align: center;color:black"><b><br>
                            <?php echo '<img style="height:100px;" src="data:image/jpeg;base64,'.base64_encode( $loggedusersign ).'"/>' ?><br>
                            <?=$loggeduserdata['name'] ?><br>
                            <?=$loggeduserdata['title'] ?></b>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input id="confirm_pin_btn" class="btn btn-success" value="Confirm" type="button" onclick="checkpin()">
                    </div>
                </div>
            </div>
        </div>
        </form>

        <div class="modal fade" id="prescription_auto_fill" role="dialog">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Prescription auto fill</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            <table class="table table-condensed">
                                <tr>
                                    <th width="100">Drug Name</th>
                                    <td>
                                        <input id="paf_drug_name" list="item" type="text" class="form-control focus_input">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Age range</th>
                                    <td>
                                        <input id="paf_age_range" type="text" class="form-control" value="0-100" placeholder="eg. 13-25">
                                    </td>
                                </tr>
                                <tr>
                                    <th><label class="control-" for="is_custom_auto_fill" style="text-align: left;">Custom</label></th>
                                    <td>
                                        <div>
                                            <label class="switch">
                                                <input type="checkbox" autocomplete="off" id="is_custom_paf" onchange="paf_is_custom_on_off()">
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr id="paf_route_row" >
                                    <th>Route</th>
                                    <td>
                                        <select id="paf_route" class="form-control">
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
                                </tr>
                                <tr id="paf_direction_row" >
                                    <th>Direction</th>
                                    <td>
                                        <select id="paf_direction" class="form-control">
                                            <option></option>
                                            <option value='OD'>OD</option>
                                            <option value='BID'>BID</option>
                                            <option value='TID'>TID</option>
                                            <option value='QID'>QID</option>
                                            <option value='PRN'>PRN</option>
                                            <option value='STAT'>STAT</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr  id="paf_no_of_days_row" >
                                    <th>N<u>o</u> days</th>
                                    <td>
                                        <input id="paf_nod" type="number" class="form-control">
                                    </td>
                                </tr>
                                <tr id="paf_remark_row" class="hide">
                                    <th>Remark</th>
                                    <td>
                                        <textarea class="form-control" id="paf_remark" rows="6"></textarea>
                                    </td>
                                </tr>
                            </table>
                            
                            <button class="form-control btn btn-success" onclick="add_paf()">Add</button>
                        </div>
                        <hr>
                        <div>
                            <div id="paf_item_detail"></div>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Age range</th>
                                    <th>Route</th>
                                    <th>Direction</th>
                                    <th>N<u>o</u> Days</th>
                                    <th>Remark</th>
                                </tr>
                                <tbody id="paf_list">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="compounding_modal" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header dental-bg-primary">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Compounding prescription</h4>
                    </div>
                    <div class="modal-body ">
                        <div style="padding:10px">
                            <div class="row">
                                <div class="col-lg-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td>Item</td><td width="100">Amount</td><td>Measurment</td><td width="20"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control focus_input" id="prescription_item" autocomplete="off" name="prescription_item">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" id="prescription_amount">
                                            </td>
                                            <td>
                                                <select class="form-control" id="prescription_measurment">
                                                    <option></option>
                                                    <option value="%">%</option>
                                                </select>
                                            </td>
                                            <td>
                                                <button class="btn btn-success" onclick="add_compounding_item()">+</button>
                                            </td>
                                        </tr>
                                    </table>
                                    <div style="margin-top: 100px;">
                                        <hr>    
                                        <div style="padding-left: 20px;" id="compounding_templet_list">
                                            <table class="table table-bordered table-hover table-condensed" id="templet_list_table">
                                                <thead>
                                                    <tr>
                                                        <th colspan="2">
                                                            Templet name <i class="fa fa-search"></i> 
                                                            <span id="templet_filter_new"></span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        foreach($compounding_templets as $compounding_templet){
                                                            echo '<tr title="Created by '.$compounding_templet['created_by_name'].'" id="compounding_templet_id_'.$compounding_templet['id'].'" class="templet_list"><td style="width:1px; background-color:'.($compounding_templet['created_by']==$loggeduserdata['id']?'#cfc':'#ddd').';"></td><td class="templet_item_td" onclick="load_compounding_templet(\''.$compounding_templet['id'].'\')">'.$compounding_templet['title'].'</td>';
                                                            if($compounding_templet['created_by']==$loggeduserdata['id'])
                                                                echo '<td style="width:20px;"><a title="Delete" class="btn btn-danger delete_templet" onclick="delete_compounding_templet(\''.$compounding_templet['id'].'\')"><i class="fa fa-trash"></i></a></td>';
                                                            else
                                                                echo '<td style="width:20px;"><a class="btn btn-gray delete_templet" disabled><i class="fa fa-trash" ></i></a></td>';


                                                            echo '</tr>';

                                                            //echo '<div id="compounding_templet_id_'.$compounding_templet['id'].'" class="templet_list"><a class="templet_link" onclick="load_compounding_templet(\''.$compounding_templet['id'].'\')">'.$compounding_templet['title'].'</a><a class="btn btn-danger delete_templet" onclick="delete_compounding_templet(\''.$compounding_templet['id'].'\')"><i class="fa fa-trash"></i></a></div>';
                                                        }
                                                    ?>
                                                </tbody>
                                            </table>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6" style="min-height: 500px; border-left: solid 1px">
                                    <div style="text-align: center; padding:5px;"><a role="button" onclick="save_compounding_as_templet()" class="text-primary"><i class="fa fa-save fa-2x"></i> Save as templet</a></div>
                                    <table class="table table-bordered" style="min-height: 100px;">
                                        <thead>
                                            <tr>
                                                <th>Item</th><th width="50">Amount</th><th width="10"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="compounding_prescription_table">
                                            
                                        </tbody>
                                    </table>

                                    <div class="form-group">
                                        <div>
                                            <label>Description</label>
                                        </div>
                                        <div>
                                            <textarea class="form-control" rows="5" id="compounding_description"></textarea>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn btn-success" onclick="save_compounding_to_prescription()">Add to prescription <i class="fa fa-arrow-right"></i></button>
                                        <button class="btn btn-danger btn-xs" onclick="clear_compounding()" style="position:absolute; right: 0px; bottom:0px;">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>

<style>
    .compounding:hover .edit_delete{
        display: inline-block;
    }
    .edit_delete{
        display: none;
    }
    
    .templet_list:hover .delete_templet{
        visibility: visible;
    }
    .delete_templet{
        visibility: hidden;
    }
    .templet_link{
        display: inline-block;
        cursor: pointer;
        width: 80%;
    }
    .templet_link:hover{
        font-weight: bold;
    }

    .templet_item_td{
        vertical-align: middle !important;
        cursor: pointer;
    }

    .focus_input{
        border: solid 2px violet;
    }
    .focus_input_pin{
        border: solid 1px #31565c;
    }
    #templet_list_table_filter{
        float: left;
    }
</style>

<script>

    $(document).ready(function() {

        setTimeout("change_filter()", 1000);

        var t = $('#templet_list_table').DataTable( {
            "columnDefs": [ {
                "searchable": false,
                "targets": 2
            } ],
            info: false,
            ordering: false,
            paging: false,
            scrollCollapse: true,
            scrollY: '300px'
        } );

        //$('#templet_list_table_filter').parent().parent().remove();
} );

function change_filter(){
    $('#templet_filter_new').append($('#templet_list_table_filter').children()[0].childNodes[1]);
    $('#templet_list_table_filter').remove();
}

</script>

<script>
    
    var item = $('#prescription_detail_item');
    var route = $('#route');
    var direction = $('#direction');
    var nod = $('#nod');
    var is_custom = $('#is_custom');
    var prescription_remark = $('#prescription_remark');
    var current_balance = $('#current_balance');

    var prescription_detail_data = {};
    <?php
        if(!empty($prescription_detail_data))
            echo 'prescription_detail_data = '.BluelimeSystem::convert_array_to_string($prescription_detail_data).';';
    ?>
    var number_of_line = 0;

    function check_if_selected_or_new_item(){
        let new_drug_lable = $('#new_drug_lable');
        let selected_item_brand_name = '';

        console.log(items[selected_item_index['prescription_selected_item_index']]);

        if(items[selected_item_index['prescription_selected_item_index']] != undefined)
            selected_item_brand_name = items[selected_item_index['prescription_selected_item_index']]['item_brand_name'];

        if(item.val().trim() != selected_item_brand_name.trim()){
            selected_item_index['prescription_selected_item_index'] = -1;
            $('#drug_detail').html("");
            if(item.val().trim != '')
                new_drug_lable.removeClass('hide');
        }
        else{
            new_drug_lable.addClass('hide');
            $('#new_drug').prop('checked', false);
        }
    }


    function paf_is_custom_on_off(){
        if($('#is_custom_paf').prop('checked')){
            $('#paf_remark_row').removeClass('hide');
            $('#paf_no_of_days_row').addClass('hide');
            $('#paf_direction_row').addClass('hide');
            $('#paf_route_row').addClass('hide');
        }
        else{
            $('#paf_remark_row').addClass('hide');
            $('#paf_no_of_days_row').removeClass('hide');
            $('#paf_direction_row').removeClass('hide');
            $('#paf_route_row').removeClass('hide');
        }
    }

    function is_custom_on_off(){
        if(is_custom.prop('checked')){
            $('#detail_mode').addClass('hide');
            prescription_remark.removeClass('hide');
        }
        else{
            $('#detail_mode').removeClass('hide');
            prescription_remark.addClass('hide');
        }
    }

    var new_drug_count = 0;
    function add_drug_to_prescription(){
        if (!validate_add_prescription_line())
            return false;

        let new_drug = $('#new_drug').prop('checked');

        let selected_item = {};
        if(!new_drug)
            selected_item = items[selected_item_index['prescription_selected_item_index']];
        else{
            new_drug_count++;
            selected_item = {"id": "new_drug_"+new_drug_count, "strength": "", "measurement": "", "preparation": "", "item_brand_name": item.val().trim(), "item_generic_name": ""};
        }

        let prescription_line = {'item': selected_item, 'route':route.val(), 'nod':nod.val(), 'direction':direction.val(), 'remark':'', 'is_custom':is_custom.prop('checked')};
        if(is_custom.prop('checked'))
            prescription_line = {'item': selected_item, 'route':'', 'nod':'', 'direction':'', 'remark':prescription_remark.val(), 'is_custom':is_custom.prop('checked')};
        prescription_detail_data['item_id_'+selected_item['id']] = prescription_line;   
        display_prescription_in_preview();
        clear_prescription_line();
    }

    function display_prescription_in_preview(){
        let table = '<table class="table table-bordered" style="border-top:solid 3px;">'
                        +'<tr><th></th><th>Drug</th><th>Route</th><th>Dir</th><th>#days</th></tr>';

        let sn = 1;
        for (let key in prescription_detail_data){
            let line = prescription_detail_data[key];
            table += '<tr><td>'+(sn++)+'</td><td><span style="float:right"><span class="badge" style="margin:1px">'+line['item']['strength']+'</span><span class="badge badge-qty" style="margin:1px">'+line['item']['preparation']+'</span></span><b>'+line['item']['item_brand_name']+'</b><br>'+line['item']['item_generic_name']+'</td>';
            if(line['is_custom'])
                table += '<td colspan="3">'+line['remark'].replaceAll("\n","<br>")+'</td><td><a role="button" class="fa fa-minus-circle text-danger" title="Remove line" onclick="remove_prescription_line(\''+line['item']['id']+'\')"></a></td></tr>';
            else
                table += '<td>'+line['route']+'</td><td>'+line['direction']+'</td><td>'+line['nod']+'</td><td><a role="button" class="fa fa-minus-circle text-danger" title="Remove line" onclick="remove_prescription_line(\''+line['item']['id']+'\')"></a></td></tr>';
        }
        table += '</table>';

        if(sn == 1)
            table = '';

        // Compounding **************************
        sn = 1;
        for(let key in compounding_prescription){
            let compounding_item_list = compounding_prescription[key]['compounding_list'];
            // let route = compounding_prescription[key]['route'];
            // let direction = compounding_prescription[key]['direction'];
            //let no_of_days = compounding_prescription[key]['no_of_days'];
            let description = compounding_prescription[key]['description'];

            table += '<div class="compounding"><div style="padding: 5px; border-top:solid 3px">Compounding '+ sn++ +'<span class="edit_delete" style="margin-left:50px;"><a role="button" class="fa fa-edit text-success" style="font-size:large;" onclick="edit_compounding(\''+key+'\')"></a> &nbsp;&nbsp;&nbsp;&nbsp;<a role="button" class="fa fa-minus-circle text-danger" style="font-size:large;" onclick="remove_compounding(\''+key+'\')"></a></span></div>';
            //table += '<div><table class="table" style="margin-bottom:0px;"><tr><td><b>Rout:</b> '+route+'</td><td><b>Directoin:</b> '+direction+'</td><td><b>N<u>o</u> days:</b> '+no_of_days+'</td></tr></table></div>';
            let number_of_items = Object.keys(compounding_item_list).length;
            let compunding_table = '<table class="table table-bordered">';
            let first_line = true;
            for(let line in compounding_item_list){
                let amount = (compounding_item_list[line]['measurement'] == '%') ? compounding_item_list[line]['in_percent'] : compounding_item_list[line]['amount'];

                compunding_table += '<tr><td><span style="float:right"><span class="badge badge-qty" style="margin:1px">'+compounding_item_list[line]['item']['preparation']+'</span></span><b>'+compounding_item_list[line]['item']['item_brand_name']+'</b><br>'+compounding_item_list[line]['item']['item_generic_name']+'</td>'
                                    +  '<td>'+amount+' '+ compounding_item_list[line]['measurement'] +'</td>';
                if(first_line){
                    compunding_table += '<td rowspan="'+number_of_items+'"><u>Description</u><br>'+(description.replaceAll("\n","<br>",))+'</td>';
                    first_line = false;
                }
                compunding_table += '</tr>';
            }
            compunding_table += '</table></div>';
            table += compunding_table;
        }

        $('#prescription_list').html(table);
    }

    function clear_prescription_line() {
    item.val('');
    route.val('');
    direction.val('');
    nod.val('');
    prescription_remark.val('');
    $('#drug_detail').html('');
    selected_item_index['prescription_selected_item_index'] = -1;
    document.getElementById("route").style.removeProperty('border');
    document.getElementById("direction").style.removeProperty('border');
    document.getElementById("nod").style.removeProperty('border');
    document.getElementById("prescription_detail_item").style.removeProperty('border');
    $('#new_drug_lable').addClass('hide');
    $('#new_drug').prop('checked', false);
    item.focus();
}
 
    function fetch_paf(){
        let item_id = items[selected_item_index['paf_selected_item_index']]['id'];
        $('#paf_drug_name_bold').html(items[selected_item_index['paf_selected_item_index']]['item_brand_name'] + ' | ' + items[selected_item_index['paf_selected_item_index']]['item_generic_name']);
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/ajax/get_prescription_auto_fill_list",
            data: {item_id:item_id},
            success: function(result) {
                try{
                    let r = JSON.parse(result);
                    let list_str = "";
                    for(let i=0; i< r.length; i++){
                        list_str +=  '<tr id="paf_list_'+r[i].id+'">'
                                    +'  <td>'+ r[i].age_from + '-' + r[i].age_to +'</td>';
                        if(r[i].remark.trim() != '')
                            list_str +=  '<td colspan="3">'+ r[i].remark.replaceAll("\n","<br>") + '</td>';
                        else{
                            list_str += '  <td>'+ r[i].route + '</td>'
                                        +'  <td>'+ r[i].direction + '</td>'
                                        +'  <td>'+ r[i].nod + '</td>'
                        }
                        list_str +=  '<td><button onclick="delete_paf('+r[i].id+')" class="btn btn-danger"><i class="fa fa-trash"></i></button></td>'
                                    +'</tr>';
                    }

                    $("#paf_list").html(list_str);
                    //$("#paf_drug_name_bold").html(drug_name);
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

    function suggest_paf(){
        let item_id = items[selected_item_index['prescription_selected_item_index']]['id'];

        if(item_id == undefined)
            return false;

        $.ajax({
            method: 'post',
            url: "/hospital/ajax/get_prescription_suggestion",
            data: {item_id:item_id, patient_age:<?= intval($age) ?>},
            success: function(result) {
                try{
                    let r = JSON.parse(result);
                    if(r.length > 0){
                        $("#prescription_remark").val(r[0].remark);
                        $("#route").val(r[0].route);
                        $("#direction").val(r[0].direction);
                        $("#nod").val(r[0].nod);
                        if(r[0].remark.trim() != ''){
                            is_custom.prop('checked',true);
                            is_custom_on_off();
                        }
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
        let item_id = items[selected_item_index['paf_selected_item_index']]['id'];
        let item_name = items[selected_item_index['paf_selected_item_index']]['item_brand_name'] + ' | ' + items[selected_item_index['paf_selected_item_index']]['item_generic_name'] ;
        if(item_id == undefined)
            return false;

        let drug_name_with_id = $("#paf_drug_name").val();
        let age_range = $("#paf_age_range").val();
        if(age_range.length == 0) {
            age_range = '1-100';
        }
        else{
            age_range = age_range;
        }
        
        let remark = $("#paf_remark").val();
        let route = $("#paf_route").val();
        let direction = $("#paf_direction").val();
        let nod = $("#paf_nod").val();

        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/ajax/add_prescription_auto_fill",
            data: {item_id:item_id, item_name:item_name, age_range:age_range, route:route, direction:direction, nod:nod, remark:remark},
            success: function(result) {
                fetch_paf();
            }
        });
    }


    function flash_red_for_validation(objID, focus=true){
        $('#'+objID).css('background-color', '#f00');
        setTimeout("$('#"+objID+"').css('background-color', '#fff')", 300);
        if(focus)
            $('#'+objID).focus();
    }

    function validate_add_prescription_line() {
        let nod = $('#nod');
        let route = $('#route');
        let direction = $('#direction');
        let remark = $('#prescription_remark').val().trim();
        let is_custom = $('#is_custom').prop('checked');
        let new_drug = $('#new_drug').prop('checked');

        if((selected_item_index['prescription_selected_item_index'] == -1) && (!new_drug)){
            flash_red_for_validation('prescription_detail_item');
            return false;
        }
        else if(is_custom){
            if(remark == ""){
                flash_red_for_validation('prescription_remark');
                return false;
            }
        }
        else if (route.val() === '') {
            flash_red_for_validation('route');
            return false;
        }
        else if (direction.val() === '') {
            flash_red_for_validation('direction');
            return false;
        } 
        else if (parseFloat('0' + nod.val()) <= 0) {
            flash_red_for_validation('nod');
            return false;
        }
        
        return true;

    }


    function show_compounding(){
        $('#compounding_modal').modal({backdrop: "static",keyboard: false});
    }

    function showPrescriptionAutoFill(){

        $('#prescription_auto_fill').modal('show');
    }

    function remove_prescription_line(item_id) {
        delete prescription_detail_data['item_id_' + item_id];
        display_prescription_in_preview();
    }

    function save_prescription() {
        let logged_user = $('#logged_user').val();
        let logged_user_name = $('#logged_user_name').val();
        let patient_id = $('#patient_id').val();
        let patient_name = $('#patient_name').val();
        let company_name = $('#company_name').val();
        let company_id = $('#company_id').val();
        let mobile = $('#mobile').val();
        let visit_id = <?= $visit_id ?>;
        let comment = $('#comment').val();
        // let compounding_route = $('#compounding_route').val();
        // let compounding_direction = $('#compounding_direction').val();
        // let compounding_no_of_days = $('#compounding_no_of_days').val();

        $.ajax({
            url: '/hospital/doctor/addprescription',
            data: {
                prescription_detail: prescription_detail_data,
                compounding_prescription: compounding_prescription,
                visit_id: visit_id,
                prescription_group_id: <?= ($_GET['prescription_group_id'] ?? -1)?>
            },
            method: 'POST',
            async: false,
            dataType: 'json',
            success: function(result) {
                if(result.status == 'success')
                    location = '/hospital/doctor/visit?visit_id='+ visit_id;
                else {
                    toastr['error'](result.message);
                    // Re-enable buttons on error
                    $('#confirm_pin_btn').prop('disabled', false).val('Confirm');
                    $('#send_to_pharmacy_btn').prop('disabled', false).text('Send to pharmacy');
                }
            },
            error: function(jqXHR, status, error) {
                alert(error);
                // Re-enable buttons on error
                $('#confirm_pin_btn').prop('disabled', false).val('Confirm');
                $('#send_to_pharmacy_btn').prop('disabled', false).text('Send to pharmacy');
            }
        });
    }

    var compounding_prescription = [];
    <?php
        if(!empty($compounding_prescription_data)){
            echo 'compounding_prescription = '.BluelimeSystem::convert_array_to_string($compounding_prescription_data).'; ';
        }
    ?>
    var compounding_list = {};
    
    function add_compounding_item(){
        if(!validate_prescription_form())
            return 0;

        let item = items[selected_item_index['comp_selected_item_index']];
        let amount = $('#prescription_amount').val();
        let measurement = $('#prescription_measurment').val();
        let in_percent = 0;
        if(measurement == '%'){
            in_percent = amount;
            amount = 0;
        }

        let new_line = {'item_id':item.id, item:item, 'amount':amount, 'measurement':measurement, 'in_percent':in_percent};
        compounding_list['item_id_'+item.id] = new_line;
        display_compounding_list();
        clear_compounding_after_add();
    }

    function display_compounding_list(){
        let compounding_prescription_table = $('#compounding_prescription_table');

        let row = '';
        for(key in compounding_list){
            let item_detail = '<div><span style="float:right; display:inlne-block; padding:0px; text-align:right" ><span class="badge badge-qty" style="margin:1px;">' + compounding_list[key]['item']['preparation'] + '</span></span><b>'+ compounding_list[key]['item']['item_brand_name'] +'</b><br>' + compounding_list[key]['item']['item_generic_name']+'</div>';
            let item_id = compounding_list[key]['item_id'];
            let amount = (compounding_list[key]['measurement'] == '%') ? compounding_list[key]['in_percent'] : compounding_list[key]['amount'];
            let measurement = compounding_list[key]['measurement'];

            row += '<tr id="compounding_prescription_line_'+item_id+'">'+
                    '<td>'+item_detail+'</td>'+
                    '<td>'+amount+' '+measurement+'</td>'+
                    '<td><a role="button" class="fa fa-minus-circle text-danger" title="Remove line" onclick="remove_compounding_line('+item_id+')"></a></td>'+
                  '</tr>';
        }
        compounding_prescription_table.html(row);
    }

    function remove_compounding_line(item_id){
        let line = $('#compounding_prescription_line_'+item_id);
        delete compounding_list['item_id_'+item_id];
        line.remove();
    }

    function clear_compounding_after_add(){
        let prescription_item = $('#prescription_item');
        let prescription_amount = $('#prescription_amount');
        let prescription_measurment = $('#prescription_measurment');

        prescription_item.val('');
        prescription_amount.val('');
        let option = '<option></option><option value="%">%</option>';
        prescription_measurment.html(option);
        selected_item_index['comp_selected_item_index'] = -1;
        prescription_item.focus();
    }

    function clear_compounding(){
        compounding_list = {};
        display_compounding_list();
        // $('#compounding_route').val('TOPICAL');
        // $('#compounding_direction').val('PRN');
        // $('#compounding_no_of_days').val('');
        $('#compounding_description').val('');
    }
   
    var edit_compounding_index = -1;
    function edit_compounding(index){
        compounding_list = JSON.parse(JSON.stringify(compounding_prescription[index]['compounding_list']));
        edit_compounding_index = index;
        // $('#compounding_route').val(compounding_prescription[index]['route']);
        // $('#compounding_direction').val(compounding_prescription[index]['direction']);
        // $('#compounding_no_of_days').val(compounding_prescription[index]['no_of_days']);
        $('#compounding_description').val(compounding_prescription[index]['description']);
        display_compounding_list();
        show_compounding();
        //$('#compounding_modal').modal('show');
    }

    $(document).ready(function(){

        display_prescription_in_preview();

        $("#compounding_modal").on('hidden.bs.modal', function(){
            if(edit_compounding_index != -1){
                edit_compounding_index = -1;
                clear_compounding();
            }
        });
    });

    function validate_prescription_form(){
        let isValid = true;
        if(selected_item_index['comp_selected_item_index'] == -1){
            //toastr['error']('Item is not selected');
            isValid = false;
            flash_red_for_validation('prescription_item');
        }

        if(Number($('#prescription_amount').val()) == 0){
            //toastr['error']('Amount must not be zero.');
            flash_red_for_validation('prescription_amount', isValid);
            isValid = false;
        }

        if($('#prescription_measurment').val() == ''){
            //toastr['error']('Measurement is not selected.');
            flash_red_for_validation('prescription_measurment', isValid);
            isValid = false;
        }

        return isValid;
    }

    function validate_compunding_save(){
        let isValid = true;

        if(Object.keys(compounding_list).length === 0){
            toastr['error']('Cannot save empty list.');
            isValid = false;
        }

        let compounding_description = $('#compounding_description').val();
        if(compounding_description.trim().length < 5){
            flash_red_for_validation('compounding_description');
            toastr['error']('Description must has at least five characters.');
            isValid = false;
        }

        return isValid;
    }

    function change_measurement(){
        let item = items[selected_item_index['comp_selected_item_index']];
        let prescription_amount = $('#prescription_amount');
        let prescription_measurment = $('#prescription_measurment');
        let option = '<option></option><option value="'+item.compounding_measurement+'">'+item.compounding_measurement+'</option><option value="%">%</option>'
        prescription_measurment.html(option);
        prescription_amount.focus();
    }

    function save_compounding_to_prescription(){
        if(!validate_compunding_save())
             return 0;

        // let route = $('#compounding_route').val();
        // let direction = $('#compounding_direction').val();
        // let no_of_days = $('#compounding_no_of_days').val();
        let description = $('#compounding_description').val();
        
        if(edit_compounding_index != -1)
            compounding_prescription[edit_compounding_index] = {'compounding_list':compounding_list, 'description':description};
        else
            compounding_prescription.push({'compounding_list':compounding_list, 'description':description});
        clear_compounding();
        display_prescription_in_preview();
        $('#compounding_modal').modal('hide');
    }

    function remove_compounding(compounding_index){
        if(confirm('Are you sure you want to remove this list?')){
            delete compounding_prescription[compounding_index];
            display_prescription_in_preview();
        }
    }

    function save_prescription_as_auto_fill(){
        if (!validate_add_prescription_line())
            return false;

        if((selected_item_index['prescription_selected_item_index'] == -1)){
            flash_red_for_validation('prescription_detail_item');
            return false;
        }
        
        selected_item = items[selected_item_index['prescription_selected_item_index']];

        let auto_fill_data = {'age_range':'0-100','item_id':selected_item.id, 'route':route.val(), 'direction':direction.val(), 'nod':nod.val(), 'remark':prescription_remark.val(), 'item_name': selected_item.item_brand_name+' | '+selected_item.item_generic_name};

        $.ajax({
            method: 'post',
            url: "/hospital/ajax/add_prescription_auto_fill",
            data: auto_fill_data,
            dataType: 'json',
            async: false,
            success: function(result) { 
                if(result.status == 'success'){
                    toastr['success']('Saved');
                    fetch_paf();
                }
                else
                    toastr['error'](result.message);
            },
            error:function(jqXHR,status,error){
                    toastr['error'](error);
            }
        });
    }

    var compounding_templet_title = '';
    function save_compounding_as_templet(){
        if(Object.keys(compounding_list).length === 0){
            toastr['error']('Cannot save empty list.');
            return false;
        }

        compounding_templet_title = prompt('Save compounding templet as', 'Templet ' + Date.now());
        if(compounding_templet_title == null)
            return false;

        if(compounding_templet_title == "")
            compounding_templet_title = 'Templet ' + Date.now();

        let description = $('#compounding_description').val();
        $.ajax({
            method: 'post',
            url: "/hospital/doctor/save_compounding_templet",
            data: {"title":compounding_templet_title,'compounding_list':JSON.stringify(compounding_list), "description":description},
            dataType: 'json',
            async: false,
            success: function(result) { 
                if(result.status == 'success'){
                    append_saved_templet(result.value);
                }
                else
                    toastr['error'](result.message);
            },
            error:function(jqXHR,status,error){
                    toastr['error'](error);
            }
        });
    }

    function delete_compounding_templet(templet_id){
        if(!confirm('Are you sure you want to delete this templet?'))
            return false;

        $.ajax({
            method: 'post',
            url: "/hospital/doctor/delete_compounding_templet",
            data: {"templet_id":templet_id},
            dataType: 'json',
            async: false,
            success: function(result) { 
                if(result.status == 'success'){
                    remove_templet(templet_id);
                }
                else
                    toastr['error'](result.message);
            },
            error:function(jqXHR,status,error){
                    toastr['error'](error);
            }
        });
    }
    <?php
        $compounding_lists = '';
        $is_first = true;
        foreach($compounding_templets as $row){
            if(!$is_first)
                $compounding_lists .= ', ';
            $compounding_lists .= '"'.$row['id'].'": ' . BluelimeSystem::convert_array_to_string($row);   
            $is_first = false;
        }
    ?>
    var compounding_templets = {<?= $compounding_lists ?>};

    function load_compounding_templet(index){
        compounding_list = JSON.parse(JSON.stringify(compounding_templets[index]['compounding_list']));
        $('#compounding_description').val(compounding_templets[index]['description']);
        display_compounding_list();
    }

    function append_saved_templet(templet_id){
        let templet_count = Object.keys(compounding_templets).length;
        let description = $('#compounding_description').val();
        compounding_templets[templet_count] = {'title': compounding_templet_title, 'compounding_list':compounding_list,"description":description};

        let new_temp = '<div id="compounding_templet_id_'+templet_count+'" class="templet_list"><a class="templet_link" onclick="load_compounding_templet(\''+templet_count+'\')">'+compounding_templet_title+'</a><a class="btn btn-danger delete_templet" onclick="delete_compounding_templet(\''+templet_count+'\')"><i class="fa fa-trash"></i></a></div>';

        $('#compounding_templet_list').append(new_temp);
    }

    function remove_templet(templet_id){
        $('#compounding_templet_id_'+templet_id).remove();
    }


    function send_to_pharmacy(){
        if((Object.keys(compounding_prescription).length == 0) && (Object.keys(prescription_detail_data).length == 0)){
            toastr['error']('Cannot send empty prescription.');
            return false;
        }
        
        // Disable the button to prevent multiple clicks
        $('#send_to_pharmacy_btn').prop('disabled', true).text('Sending...');
        
        $('#confirmPin').modal('show');
        $('#confirmPin').on('shown.bs.modal', function () {
            $('#pin_entered').focus();
        })
        $('#confirmPin').on('hidden.bs.modal', function () {
            // Re-enable buttons when modal is closed without confirming
            $('#confirm_pin_btn').prop('disabled', false).val('Confirm');
            $('#send_to_pharmacy_btn').prop('disabled', false).text('Send to pharmacy');
        });
        $('#pin_entered').focus();
    }

    function checkpin(){
        // Disable the confirm button to prevent multiple clicks
        $('#confirm_pin_btn').prop('disabled', true).val('Confirming...');
        
        var pin = $("#pin_entered").val();
        var user = $("#user_id").val();
        $.ajax({
            method: 'post',
            url: "/hospital/ajax/checkpin",
            data: {"pin_code":pin,'id':user},
            dataType: 'json',
            async: false,
            success: function(result) { 
                if(result.status == 'success'){
                    save_prescription();
                }
                else {
                    toastr['error'](result.message);
                    // Re-enable buttons on error
                    $('#confirm_pin_btn').prop('disabled', false).val('Confirm');
                    $('#send_to_pharmacy_btn').prop('disabled', false).text('Send to pharmacy');
                }
            },
            error:function(jqXHR,status,error){
                toastr['error'](error);
                // Re-enable buttons on error
                $('#confirm_pin_btn').prop('disabled', false).val('Confirm');
                $('#send_to_pharmacy_btn').prop('disabled', false).text('Send to pharmacy');
            }
        });
    }
</script>
<?= view('Include/_autocomplete_item') ?>

<script>
    var items = <?= \BluelimeSystem::convert_array_to_string($drugs) ?>;
    var compounding_drugs = <?= \BluelimeSystem::convert_array_to_string($compounding_drugs) ?>;
    var auto_complete_option = {};

    selected_item_index['prescription_selected_item_index'] = -1;
    auto_complete_option = {
        input: document.getElementById("prescription_detail_item"),
        items: items,
        selected_index: 'prescription_selected_item_index',
        callback_function: prescription_item_selected,
        callback_function_arguments: ['prescription_selected_item_index','drug_detail'],
        show_current_balance: false
    };
    autocomplete_item_by_options(auto_complete_option);
    
    selected_item_index['paf_selected_item_index'] = -1;
    auto_complete_option = {
        input: document.getElementById("paf_drug_name"),
        items: items,
        selected_index: 'paf_selected_item_index',
        callback_function: prescription_item_selected,
        callback_function_arguments: ['paf_selected_item_index', 'paf_item_detail'],
        show_current_balance: false
    };
    autocomplete_item_by_options(auto_complete_option);
    
    selected_item_index['comp_selected_item_index'] = -1;
    auto_complete_option = {
        input: document.getElementById("prescription_item"),
        items: compounding_drugs,
        selected_index: 'comp_selected_item_index',
        callback_function: change_measurement,
        show_current_balance: false
    };
    autocomplete_item_by_options(auto_complete_option);

    function prescription_item_selected(args){
        let item_index = selected_item_index[args[0]];
        let selected_item = items[item_index];
        let item_display = '<table class="table table-condensed"><tr>'
                                +'<td><b>'+selected_item['item_brand_name']+'</b><br>'+selected_item['item_generic_name']+'</td>'
                                +'<td><span class="badge" style="margin:1px">'+selected_item['strength']+'</span>'
                                +'<span class="badge badge-qty" style="margin:1px">'+selected_item['preparation']+'</span></span></td>'
                            +'</tr></table>';
        $('#'+args[1]).html(item_display);


        if(args[0] == 'paf_selected_item_index')
            fetch_paf();
        else {
            suggest_paf();
            selected_item_index['paf_selected_item_index'] = selected_item_index['prescription_selected_item_index'];
            prescription_item_selected(['paf_selected_item_index', 'paf_item_detail']);
        }
        
        check_if_selected_or_new_item();
    }

    
</script>

<?php
    // Include Add new modal with its Ajax js
    echo view('Include/_add_new_general');
?>
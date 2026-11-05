<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo lang('Laboratory');?></h1>
            <small><?php echo lang('Order Detail');?></small>
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bd">
                    <div class="panel-body">
                        <div class="col-sm-2">
                            <h3 style="margin-top: 5px !important;">
                                <b>
                                    <?= "MRN-".sprintf('%05d', $order_main[0]['patient_id']);?>
                                </b>
                            </h3>
                                <b><?= $order_main[0]['patient_name']?></b><br>
                                <b>Age: </b><?= BluelimeSystem::year_difference(@$order_main[0]['date_of_birth']??date('Y-m-d'),date('Y-m-d'));  ?>
                                <b> &nbsp; Sex: </b><?= @$order_main[0]['sex'] ?><br>
                                <?= $order_main[0]['mobile'];?>
                        <!--input type="button"  class="btn-xs btn-success" style="background-color: green; border-0" value="ET"-->
                        </div>
                        <div class="col-sm-4">
                            <?php
                                if(trim($order_main[0]['order_comment']??'') != '')
                                    echo '
                                        <div class="panel panel-bd ">
                                            <div class="panel-heading" style="background-color: #ff0;">
                                                <div class="panel-title">
                                                    <h4>Lab order comment</h4>
                                                </div>
                                            </div>
                                            <div class="modal-body" style="background-color: #ffa;">
                                                '.str_replace("\n","<br>",($order_main[0]['order_comment'] ?? '')) .'
                                            </div>
                                        </div>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <h4>Tests</h4>                    
                    </div>
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover dt-responsive" id="appointmentS">
                            <tbody>
                                <?php
                                
                                    $cnt_order = 0;
                                    foreach($order_detail as $detail){
                                ?>
                                <tr>
                                    <td style="cursor: pointer;" onclick="<?= ($detail['sample_taken'] == 0) ? 'view_test_attribute' : 'view_test_attribute'?>(<?=$detail['ordered_service_id']?>,<?=$detail['id']?>)"><h5><?=$detail['test_name']?></h5></td>
                                </tr>
                                    <div class="modal fade" id="takesample<?=$detail['id']?>" role="dialog">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header dental-bg-primary">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">Collect Sample</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <label>Sample Type</label>
                                                    <select class="form-control" id="sample_type">
                                                        <option>--</option>
                                                        <option>Blood</option>
                                                        <option>Urine</option>
                                                    </select>
                                                    <button class="form-control btn btn-success" onclick="collect_sample(<?=$detail['id']?>)">Collect</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                <?php
                                $cnt_order++;
                                    }
                                ?>
                            </tbody>
                        </table>
                        
                        <?php 
                        
                        $cnt_result = 0;
                        $test_id = 0;
                        foreach ($order_result as $rt) {
                            $cnt_result++;
                           // echo $rt['result'];
                           foreach ($order_result as $key => $rt) {
                            $sort[$key] = $rt['test_id'];
                                }
                                if(!empty($order_result)){
                                        array_multisort($sort, SORT_DESC, $order_result);
                                }
                          if($rt['test_id'] != $test_id){
                            $test_id = $rt['test_id'];
                            
                          }
                          
                        }
                            
                            echo '<textarea id="result_remark" class="form-control" placeholder="Remark" '.(($cnt_order != $nor)?' readonly ':'').' rows="4">'.trim($order_main[0]['result_remark']).'</textarea><br>';

                            if($cnt_order == $nor){
                        ?>
                            <button type="button" class="btn btn-primary" onclick="approve_results(<?php echo @$_GET['order_main_id'] ?>);">Approve & Dispatch All Test Results</button>
                        <?php 
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                    <h4>Results
                        <span>
                            <?php if(!empty($order_result)){ ?>
                            <a class="btn btn-info" href="view_lab_printout?order_main_id=<?php echo @$_GET['order_main_id'] ?>"><span class="fa fa-print"></span></a>
                            <?php }?>
                        </span>
                    </h4>
                    </div>
                    <div class="panel-body" id="view_attributes">
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!--div class="modal fade" id="confirmPin" role="dialog">
            <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header dental-bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Enter Pin Code</h4>
                </div>
                <div class="modal-body">
                    <input id="pin_entered" type="password" maxlength="4" class="form-control" autocomplete="off" pattern="[0-9]+" >
                    <input id="user_id" type="hidden" value="<?=$loggeduserdata['id'] ?>">
                    <div id="pinCheck_flash" class="alert alert-danger hide" role="alert">
                    <span></span>
                </div>
                    <?php echo '<img style="height:150px;" src="data:image/jpeg;base64,'.base64_encode( $loggeduserdata['digital_signature'] ).'"/>' ?>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                
                <input class="btn btn-success" value="Confirm" type="button"  onclick="checkpin()" >
                    
                </div>
            </div>
            </div>
        </div-->

<script>
    <?php
        if(isset($_GET['test'])){
    ?>
    $(document).ready(function () {
        view_test_attribute(<?=$_GET['test']?>,<?=$_GET['order']?>)
    });
    <?php
        }

    ?>

    var attributes = [];
    var obj = {}
   
    function view_test_attribute(test_id,order_detail_id) {
        let sex = "<?=$order_main[0]['sex']?>";
        $.ajax({
            url: '/hospital/laboratory/get_test_attribute',
            data: {
                test_id: test_id,
                sex: sex,
                order_detail_id: order_detail_id
            },
            method: 'POST',
            async: false,
            success: function(result) {
                var container = $("#view_attributes");
                show_attribute_detail(result,order_detail_id);
                //alert(result);
            },
            error: function(jqXHR, status, error) {
                alert(error);
            }
        });
    }
    
    function viewtakesample(service_id,id){
        $('#takesample'+id).modal('show');
    }
    

    function show_attribute_detail(result,order_detail_id)
    {
        attributes = [];
        let attribute_detail = $('#view_attributes');
        
        let display = 'block';
        
        let html_table = '<div class="panel panel-bd panel-form">'+
                            '<div class="panal-body">'+
                                
                                
                                    '<table class="table table-bordered table-hover table-sm" style="font-size: 12px;">'+
                                        
                                        '<tbody>';
                                        let last_rt = [];
                                        $.each(JSON.parse(result), function(i, attr) {
                                            let rt_type = (attr.quantifiable==1)?"number":"text";
                                            let flag_icon ="";
                                            let flag_value = 0;
                                            if(attr.saved_result.length > 0){
                                                if(parseFloat(attr.saved_result) < parseFloat(attr.reference_range_from)){
                                                    flag_icon = "<i class='text-danger fa fa-arrow-down'></i>" ;
                                                    flag_value = -1;
                                                }
                                                else if(parseFloat(attr.saved_result) > parseFloat(attr.reference_range_to)){
                                                    flag_icon = "<i class='text-danger fa fa-arrow-up'></i>"
                                                    flag_value = 1;
                                                }
                                            }

                                            html_table += '<div class="form-group">'+
                                            '<label class="col-md-12 control-label" style="text-align: left;">' + attr.attribute_name  + ': '+ flag_icon +'</label>'+
                                            '<div class="col-md-6">'+
                                                '<input type="'+ rt_type +'" id="result' + attr.id +'" step=".01" class="form-control" autocomplete="off" value="'+ attr.saved_result +'">'+ 
                                                
                                                '<input type="hidden"  id= "attribute_name' + attr.id +'" value="'+ attr.attribute_name +'">'+
                                                '<input type="hidden"  id= "test_id' + attr.id +'" value="'+ attr.test_id +'">'+
                                                '<input type="hidden"  id= "order_detail_id' + attr.id +'" value="'+ order_detail_id +'">'+
                                                '<input type="hidden"  id= "order_id' + attr.id +'" value="'+ <?php echo @$_GET['order_main_id'] ?> +'">'+
                                                '<input type="hidden"  id="attribute_id" value="' + attr.id +'">'+
                                                '<input type="hidden"  id="result_flag' + attr.id +'" value="' + flag_value +'">'+
                                                '<input type="hidden"  id="reference_range' + attr.id +'" value="' + attr.reference_range_from + '-' + attr.reference_range_to +' '+  attr.reference_range_unit +'">'+
                                        
                                            '</div>'+
                                            '<div class="col-md-6">'+
                                            attr.reference_range_from + '-' + attr.reference_range_to +' '+  attr.reference_range_unit +
                                            '</div>'+
                                        '</div>';
                                        attributes.push(attr);
                                        last_rt = attr.saved_result;
                                        
                                        });

            html_table +=           '</tbody>'+
                                    '</table>'+
                                    '<button type="button" class="btn btn-'+((last_rt.length==0)?"success":"danger")+'" onclick="save_result()" style="float:right;">'+((last_rt.length==0)?"Save":"Update")+'</button>';  
                                    '</div>'+
                                '</div>';

            attribute_detail.html(html_table);  
            
    }

    function save_result() {
    let rt = '';
    let test_id = '';
    let order_detail_id = '';
    let order_id = '';
    let attribute_name = '';
    let reference_range = '';
    let result_flag = 0;
    let registered_by = <?=$loggeduserdata['id']?>;
    
    let result = [];
    
        $.each(attributes, function(i, attr) {
            
            rt = $('#result' + attr.id).val();
            //alert(rt);
            if(rt.length > 0){
                if(parseFloat(rt) < parseFloat(attr.reference_range_from)){
                    result_flag = -1;
                }
                else if(parseFloat(rt) > parseFloat(attr.reference_range_to)){
                    result_flag = 1;
                }
                else
                    result_flag = 0;
            }
            //alert(result_flag);
            test_id = $('#test_id' + attr.id).val();
            order_detail_id = $('#order_detail_id' + attr.id).val();
            order_id = $('#order_id' + attr.id).val();
            attribute_name = $('#attribute_name' + attr.id).val();
            reference_range = $('#reference_range' + attr.id).val();
            
            let temp_result = {};
            temp_result['attribute_id'] = attr.id;
            temp_result['attribute_name'] = attribute_name;
            temp_result['reference_range'] = reference_range;
            temp_result['test_id'] = test_id;
            temp_result['order_detail_id'] = order_detail_id;
            temp_result['order_id'] = order_id;
            temp_result['registered_by'] = registered_by;
            temp_result['result_flag'] = result_flag;
            temp_result['result'] = rt;
            result.push(temp_result);
            
            
        });
        
        
    if(validateresultattributes(result)){
        $.ajax({
            url: '/hospital/laboratory/saveresult',
            data: {
                result_detail: result
            },
            method: 'POST',
            async: false,
            dataType: 'json',
            success: function(result) {
                //alert(result.test);
                location = '/hospital/laboratory/order_detail?order_main_id='+result.value+'&test='+result.test+'&order='+result.order;
            },
            error: function(jqXHR, status, error) {
                alert(error);
            }
        });
    }
    else{
        toastr["error"]("Please Check All Result Values");
    }
    }
    
    function validateresultattributes(results){
        //console.log(results);
        //return alert(JSON.stringify(attributes));
        let valid = true;
        $.each(results, function(i, value) {
            //const res = Object.values(attr).some(value => value === "");
            valid=(value['result']=="")?false:true;
            if(!valid){
                //This function  is found in Include/_footer
                flash_red_inputbox('result'+value['attribute_id'],true); 
                return false;
            }
        });
        return valid;
    }
    function collect_sample(id){
        var sample_type = $("#sample_type").val();
        $.ajax({
            method: 'post',
            url: "/hospital/laboratory/collectsample",
            data: {"sample_type":sample_type,'id':id},
            success: function(data) { 
                location = '/hospital/laboratory/order_detail?order_main_id='+<?=@$_GET['order_main_id']?>;
                
            },
            error: function(jqXHR, status, error) {
                alert(error);
        }
        });
    }

  
    function approve_results(order_main_id){
        if(confirm("Are you sure? This will Approve and send All result!")){
        let result_remark = $('#result_remark').val();
        $.ajax({
            method: 'post',
            url: "/hospital/laboratory/approveresult",
            data: {"order_main_id":order_main_id, result_remark:result_remark},
            success: function(data) { 
                location = '/hospital/laboratory/view_lab_printout?order_main_id='+order_main_id;
                
            },
            error: function(jqXHR, status, error) {
                alert(error);
        }
        });
    }
    }

</script>
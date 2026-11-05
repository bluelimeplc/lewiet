<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<?php
            helper('form');
        ?>
<script src="<?php echo base_url()?>assets/plugins/summernote/summernote.min.js" type="text/javascript"></script>
<link href="<?php echo base_url()?>assets/plugins/summernote/summernote.css" rel="stylesheet" type="text/css"/>
 
<link rel="stylesheet" href="/Image-gallery/assets/css/jquery.lightbox.css">
<link rel="stylesheet" href="/Image-gallery/assets/css/demo.css">
<script src="/Image-gallery/assets/js/jquery.lightbox.js"></script>       

<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo lang('Pathology');?></h1>
            <small><?php echo lang('Order Detail');?></small>
        </div>
    </section>
    <section class="content">
    <?php 
                        helper('form');
                        $attributes = array('class' => 'form-horizontal', 'method'=>'post','name'=>'add_waiting_list');
                        echo form_open('/hospital/pathology/savebiopsyresult', $attributes);           
                    ?>
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
                        <div class="col-sm-3">
                        <div class="panel-body">
                            <input type="hidden" hidden id="patient_id" name="patient_id" value="<?=$order_main[0]['patient_id']?>">
                            <input type="hidden"  name="sex" value="<?= @$order_main[0]['sex'] ?>">
                            <input type="hidden"  name="visit_id" value="<?=$visit_id??0 ?>">
                            <input type="hidden"  name="mid" value="<?= @$order_main[0]['id'] ?>">
                            <input type="hidden"  name="age" value="<?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),@$order_main[0]['date_of_birth'])."Y "?><?=BluelimeSystem::year_difference(BluelimeSystem::date_now(),@$order_main[0]['date_of_birth']) >=10 ?"":BluelimeSystem::month_difference(BluelimeSystem::date_now(),@$order_main[0]['date_of_birth'])."M"?>">
                            <input type="hidden" required  name="patient_name"   class="form-control" placeholder="Name" value="<?= $order_main[0]['patient_name'] ?? ""  ?>">
                            <input type="hidden" placeholder="09xxxxxxxx"  name="mobile" value="<?=$order_main[0]['mobile']?>" required class="form-control">
                            
                        <table width="100%" class="table table-striped table-bordered table-hover dt-responsive" id="appointmentS">
                            <tbody>
                                
                                <tr>
                                   
                                    <td style="cursor: pointer;"><h5><?=$order_detail[0]['test_name']?></h5></td>
                                    
                                </tr>
                                  
                            </tbody>
                        </table>
                        
                        
                    </div>
                        </div>
                        <div class="col-sm-4">
                        <div class="manual_card  <?= (empty($order_main[0]['scanned_documents']))?" hidden ":""?>" style="margin-left: 30px;">
                                 <label>Request Document</label> <br>              

                                                
                            <?php
                                $manual_card_image = explode(',',$order_main[0]['scanned_documents']);
                                $is_first = true;
                                foreach($manual_card_image as $image_name):
                                    echo '<a href="/image/manual_card?image='.$image_name.'" data-image_name="'.$image_name.'"><img src="/image/manual_card?image='.$image_name.'&thumbnail=1" style="width:100px" ></a>';
                                endforeach;
                            ?>
                           
                        
                        </div>
                        <div style="width: 10px;">
                        <?php
                            if($external_biopsy_request){
                            ?>
                        <a href="" title="view Request" data-toggle="modal"  data-target="#view_request"  data-html="true"><img src="/img/certificate.png" width="100px"></a>    
                                <?php
                            }
                            ?> 
                    </div>
                        </div>
                        <div class="col-sm-3">
                            <?php
                                if(trim($order_main[0]['order_comment']??'') != '')
                                    echo '
                                        <div class="panel panel-bd ">
                                            <div class="panel-heading" style="background-color: #ff0;">
                                                <div class="panel-title">
                                                    <h4>Comment</h4>
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
            
            <div class="col-md-8">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                    <h4>Result
                    <?php
                            foreach($biopsy_results as $biopsy_result){
                                if(!empty($biopsy_results)){
                        ?>
                            <tr><td><a href="/hospital/pathology/viewbiopsyresult?brid=<?=$biopsy_result['id']?>&order_id=<?=$biopsy_result['order_id']?>"><i class="btn btn-primary fa fa-eye"></i></a></td></tr>
                        <?php
                                }
                        }
                        ?>
                    </h4>
                    </div>
                    <div class="panel-body">

                   
                    
                        
                        <div class="tab-pane active">
                            <div class="tab-pane active">
                                <textarea type="text" id="summernote"  name="template_content" style="resize: none;height: 150px;"><?php
                                    if(!empty($biopsy_results)){
                                                foreach($biopsy_results as $br){
                                                    echo $br['biopsy_result']['conclusion'];
                                                }
                                            }
                                    else{
                                        echo $template['template_content'];
                                    }
                                ?></textarea>
                            </div>
                            
                        </div>
                 <input type="submit" class="btn btn-primary" value="Save Result" style="float: right;">
                    </div>
                </div>
            </div>
        </div>
        <?php form_close();?>
    </section>
</div>

<div class="modal fade" id="view_request" tabindex="-1" role="dialog" aria-labelledby="template" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-body">
                <div class="panel-body">
                    <div class="portlet-body form">
                        <?php
                            if($external_biopsy_request)
                            echo view('Include/_external_biopsy_request_print') 
                        ?>                                                
                    </div>                        
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    

    var attributes = [];
    var obj = {}
   
    function view_test_attribute(test_id,order_detail_id) {
        let sex = "<?=$order_main[0]['sex']?>";
        $.ajax({
            url: '/hospital/pathology/get_test_attribute',
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
            url: '/hospital/pathology/saveresult',
            data: {
                result_detail: result
            },
            method: 'POST',
            async: false,
            dataType: 'json',
            success: function(result) {
                //alert(result.test);
                location = '/hospital/pathology/order_detail?order_main_id='+result.value+'&test='+result.test+'&order='+result.order;
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
                return false;
            }
        });
        return valid;
    }
   
    function approve_results(order_main_id){
        $.ajax({
            method: 'post',
            url: "/hospital/pathology/approveresult",
            data: {"order_main_id":order_main_id},
            success: function(data) { 
                location = '/hospital/pathology/view_lab_printout?order_main_id='+order_main_id;
                
            },
            error: function(jqXHR, status, error) {
                alert(error);
        }
        });
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

</script>

<script>
    $(document).ready(function() {
        $('#summernote').summernote({height: 700,
        focus: true});
    });

    function preview() {
        frame.src = URL.createObjectURL(event.target.files[0]);
    }
    function clearImage() {
        document.getElementById('formFile').value = null;
        document.getElementById('comment').value = "";
        frame.src = "";
    }

    
  
    $(function() {
        $('.manual_card a').lightbox(); 
    });

 

  </script>
<?php
    helper('form');
    
    $mrn_no = $patient['id'];
    $formated_mrn_no = sprintf('%05d', $mrn_no);
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
            <small>Procedure Followup</small>

        </div>
    </section>


    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="row">
                <!-- Encounter entry -->
                <div class="col-md-12">
                    <div class="panel panel-bd">
                        <div class="panel-heading">
                            <div class="panel-title">

                                <h4>Procedure Followup</h4>
                            </div>
                        </div>
                        <div>
                            <div class="panel-body">
                               
                                <div class="row">
                                    <div class="col-lg-4">
                                        <?php
                                            if(trim($nurse_order[0]['order_comment']??'') != '')
                                                echo '
                                                    <div class="panel panel-bd ">
                                                        <div class="panel-heading" style="background-color: #ff0;">
                                                            <div class="panel-title">
                                                                <h4>Order Comment</h4>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body" style="background-color: #ffa;">
                                                            '.str_replace("\n","<br>",($nurse_order[0]['order_comment'] ?? '')) .'
                                                        </div>
                                                    </div>';
                                        ?>
                                        <table class="table table-striped table-bordered table-condensed">
                                            <tr>
                                                <td width="120">Name</td>
                                                <td><?= $patient['first_name'] . ' ' . $patient['middle_name'] . ' ' . $patient['last_name']; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="120">Card Number</td>
                                                <td><?= "MRN-".sprintf('%05d', $patient['id']); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Date of Birth</td>
                                                <td><?= $patient['date_of_birth']?> | Age: <b id="patient_age">01</b>
                                                </td>
                                                <script>
                                                function getAge(val) {
                                                    var CurrentYear = new Date().getFullYear();
                                                    document.getElementById('patient_age').innerHTML = CurrentYear - val
                                                        .slice(0, 4);
                                                }
                                                getAge('<?=$patient['date_of_birth']?>');
                                                </script>
                                            </tr>
                                            <tr>
                                                <td>Sex</td>
                                                <td><?= $patient['sex']?></td>
                                            </tr>


                                        </table>
                                        <table class="table  table-bordered table-condensed">
                                            <tr>
                                                <td width="60">Blood pressure</td>
                                                <td width="80">
                                                    <?= $patient['blood_pressure'] === 'Yes' ? '<i class="glyphicon  glyphicon-ok color-red"></i>' : '<i class="glyphicon  glyphicon-remove color-gray"></i>';?>
                                                </td>
                                                <td>Bleeding Tendency</td>
                                                <td><?= $patient['bleeding_tendency'] === 'Yes' ? '<i class="glyphicon  glyphicon-ok color-red"></i>' : '<i class="glyphicon  glyphicon-remove color-gray"></i>';?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Pregnancy</td>
                                                <td><?= $patient['pregnancy'] === 'Yes' ? '<i class="glyphicon  glyphicon-ok color-red"></i>' : '<i class="glyphicon  glyphicon-remove color-gray"></i>';?>
                                                </td>
                                                <td>Cardiac Problem</td>
                                                <td><?= $patient['cardiac_problem'] === 'Yes' ? '<i class="glyphicon  glyphicon-ok color-red"></i>' : '<i class="glyphicon  glyphicon-remove color-gray"></i>';?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Diabetics</td>
                                                <td><?= $patient['diabetics'] === 'Yes' ? '<i class="glyphicon  glyphicon-ok color-red"></i>' : '<i class="glyphicon  glyphicon-remove color-gray"></i>';?>
                                                </td>
                                                <td>Blood Group</td>
                                                <td><b><?= $patient['blood_group'];?></b></td>
                                            </tr>
                                        </table>
                                        <table class="table table-striped  table-bordered table-condensed">
                                            <tr>
                                                <td width="120">Drugs Taking</td>
                                                <td colspan=3>
                                                    <?php
                                                $drugs_taking = $patient['drugs_taking'];
                                                foreach($drugs_taking as $drug)
                                                    echo '<span class="label bg-primary dental-tags-lable">'.$drug.'</span>';
                                            ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Allergy</td>
                                                <td colspan=3>
                                                    <?php
                                                $allergies = $patient['allergy'];
                                                foreach($allergies as $allergy)
                                                    echo '<span class="label bg-primary dental-tags-lable">'.$allergy.'</span>';
                                            ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Other Diseases</td>
                                                <td colspan=3>
                                                    <?php
                                                $other_diseases = $patient['other_diseases'];
                                                foreach($other_diseases as $disease)
                                                    echo '<span class="label bg-primary dental-tags-lable">'.$disease.'</span>';
                                            ?>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                    </div>
                                    <div class="col-lg-8" style="min-height: 200px;">
                                    
                                        <div class="col-lg-6">
                                            <div class="dental-form-box">
                                                <div class="box-title">
                                                    Paid Procedures
                                                </div>
                                                
                                                    <table class="table table-hover table-bordered" style="cursor: pointer;">
                                                        <?php
                                                                    foreach($nurse_order as $order){
                                                                ?>
                                                        <tr>
                                                            <?php
                                                                if($order['service_status'] == BluelimeConfiguration::SERVICE_STATUS_DELAY):
                                                            ?>
                                                            <td style="cursor:not-allowed;"><?=$order['service_name']?> <span class="badge badge-qty-red ">Not paid</span></td>
                                                            <?php
                                                                else:
                                                            ?>
                                                            <td style="cursor: pointer;" onclick="view_procedure_attribute(<?=$order['service_id']?>,<?=$order['id']?>)"><?=$order['service_name']?></td>
                                                            <?php
                                                                endif
                                                            ?>
                                                        </tr>
                                                        <?php
                                                                    }
                                                                ?>
                                                    </table>
                                                
                                            </div>
                                        </div>
                                        <div class="col-lg-6" id="view_attributes">
                                            <div class="dental-form-box">

                                            </div>
                                            
                                        </div>
                                        
                                    </div>
                                    
                                    <div class="col-lg-8" style="min-height: 100px;">
                                    <?php
                                    if($distinct_procedures){
                                        foreach($distinct_procedures as $index=>$history){
                                    ?>
                                    <div class="col-lg-12" style="font-size: smaller;">
                                            <div class="dental-form-box  table-responsive">
                                                <div class="box-title">
                                                    <span style="float: left;color:green"><?=$history??""?></span>
                                                </div>
                                                
                                                    <table class="table table-hover table-striped table-bordered" style="cursor: pointer;">
                                                        
                                                                    <?php
                                                                        $cnt = 0;
                                                                        foreach($patient_procedure_history as $attr){
                                                                       
                                                                        if($history == $attr['procedure_name']){
                                                                            $cnt++;
                                                                    ?>
                                                                    <tr>

                                                                        <?php
                                                                                if($cnt<2){
                                                                                foreach(BluelimeSystem::convert_to_array(str: $attr['result']) as $rt){
                                                                                echo '<th>'.$rt['attribute_name'].'</th>';
                                                                                }
                                                                                echo '<th>Date</th>';
                                                                                echo '<th>Registered By</th>';
                                                                            }
                                                                            ?>
                                                                    </tr>
                                                                    <tr>

                                                                        <?php
                                                                           
                                                                                foreach(BluelimeSystem::convert_to_array($attr['result']) as $rt){
                                                                                  
                                                                                echo '<td>'.$rt['result'].'</td>';
                                                                                    
                                                                                }
                                                                                echo '<td>'.$attr['created_at'].'</td>';
                                                                                echo '<td>'.$attr['registered_by_name'].'</td>';
                                                                            
                                                                            ?>
                                                                             <td><a onclick="deleteresult(<?=$attr['id']?>)" class="btn btn-xs btn-danger <?=date('Y-m-d',strtotime($attr['created_at']))==date('Y-m-d')?"":"hidden"?>" ><i class="fa fa-trash">b</i></a></td>
                                                                    </tr> 
                                                            
                                                            <?php
                                                                        }
                                                                        }
                                                                    ?>
                                                        
                                                       
                                                           
                                                            
                                                    </table>
                                                
                                            </div>
                                    </div>
                                    <?php
                                        }
                                    }
                                    ?>
                                        
                                    </div>
                                
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
                <link rel="stylesheet" href="/Image-gallery/assets/css/jquery.lightbox.css">
            <script src="/Image-gallery/assets/js/jquery.lightbox.js"></script>
            <div class="manual_card">
            <style>
                    .view_card{
                        position: fixed;
                        top: 50%;
                        z-index: 100;
                        right: -50px;
                        padding: 10px;
                        border-radius: 15px 15px 0 0;
                        background-color: dodgerblue;
                        rotate: -90deg;
                        color: #fff;
                        height: 70px;
                        border: solid 2px #00f;
                        <?= empty($patient['attached_old_card']) ? 'display: none;' : '' ?>
                    }
                    .view_card:hover{
                        right: -40px;
                        color: #ff0;
                    }
                </style>
                <?php
                    $manual_card_image = explode(',',$patient['attached_old_card']);
                    $is_first = true;
                    foreach($manual_card_image as $image_name):
                        if($is_first)
                            echo '<a role="button" class="view_card" href="/image/manual_card?image='.$image_name.'">View Card <i class="fa fa-id-card"></i></a>';
                        else
                            echo '<a role="button" class="view_card hidden" href="/image/manual_card?image='.$image_name.'">View Card <i class="fa fa-id-card"></i></a>';

                    endforeach;
                ?>
                
                <script>
                $(function() {
                    $('.manual_card a').lightbox();
                });
            </script>
            </div>
            <!-- Prescription History -->
            <div class="col-md-12">
                            <div class="panel panel-bd">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4><a class="" data-toggle="collapse" href="#collapsePrescription" role="button" aria-expanded="false" aria-controls="collapsePrescription">
                                                Prescription History
                                            </a>
                                        </h4>
                                    </div>
                                </div>
                                <div class="panel-body collapsed" id="collapsePrescription">
                                    <div class="row">
                                        <?php
                                            foreach($prescription_group as $group){
                                                $date = $group['prescriptions'][0]['created_at'] ?? $group['compoundings'][0]['created_at'] ?? '';
                                                //$date = $group['prescriptions'][0]['created_at'] ?? '';
                                                $prescribed_by = $group['prescriptions'][0]['registered_by_name'] ?? $group['compoundings'][0]['created_by_name'] ?? '';
                                                //$prescribed_by = $group['prescriptions'][0]['registered_by_name'] ?? '';

                                                echo '
                                        <div class="col-lg-6">
                                            <fieldset class="reset">
                                                <legend class="reset"><span class="badge badge-qty-warning">'.$date.'</span> <span class=" badge badge-qty-warning">'.$prescribed_by.'</span></legend>';
                                                
                                                if(!empty($group['prescriptions'])){
                                                    $prescription_detail = [];
                                                    foreach($group['prescriptions'] as $prescriptions){
                                                        array_push($prescription_detail, ...$prescriptions['details']);
                                                    }

                                                    echo view('Include/_prescription_print_medicine', ['prescription_detail'=>$prescription_detail]);
                                                }

                                                $compounding_mains = [];
                                                foreach($group['compoundings'] as $compounding){
                                                    array_push($compounding_mains, $compounding);
                                                }

                                                echo view('Include/_prescription_print_compounding', ['compounding_mains'=>$compounding_mains]);

                                                echo '
                                            </fieldset>
                                        </div>';
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                <!-- Encounter history -->
                <div class="col-md-12">
                <?=@view('Include/_encounter_history',['history_follow_up_list'=>$encounter_history])?>
            </div>

    </section> <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<script>
    function deleteresult(result_id) {
    if (confirm('Are you Sure?')) {
        $.ajax({
            'url': "<?php echo base_url(); ?>hospital/nurse/deleteresult",
            'type': 'POST',
            'data': {
                'result_id': result_id
            },
            'success': function(data) {
                reloadafterstatus();
            }
        });
    }
}
function reloadafterstatus() {
    document.location.reload();
}
    <?php
        if(isset($_GET['procedure'])){
    ?>
    $(document).ready(function () {
        view_procedure_attribute(<?=$_GET['procedure']?>,<?=$_GET['order']?>)
    });
    <?php
        }

    ?>
    function view_procedure_attribute(procedure_id,order_detail_id) {
        
        $.ajax({
            url: '/hospital/nurse/get_procedure_attribute',
            data: {
                procedure_id: procedure_id,
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
    function show_attribute_detail(result,order_detail_id)
    {
        attributes = [];
        let attribute_detail = $('#view_attributes');
        
        let display = 'block';
        
        let html_table = '<div class="row">';
                                
                                
                                    
                                        //let last_rt = [];
                                        $.each(JSON.parse(result), function(i, attr) {
                                            html_table += '<div class="box-title"><b>'+
                                                                        attr.attribute_name +
                                                            '<div></b>'+
                                                            '<div class="encounter_container">'+
                                                                '<input type="'+ attr.input_type +'" id="result' + attr.id +'" step=".01" class="form-control" autocomplete="off">'+ 
                                                                '<input type="hidden"  id= "attribute_name' + attr.id +'" value="'+ attr.attribute_name +'">'+
                                                                '<input type="hidden"  id= "procedure_id' + attr.id +'" value="'+ attr.procedure_id +'">'+
                                                                '<input type="hidden"  id= "procedure_name' + attr.id +'" value="'+ attr.procedure_name +'">'+
                                                                '<input type="hidden"  id= "order_detail_id' + attr.id +'" value="'+ order_detail_id +'">'+
                                                                '<input type="hidden"  id= "order_id' + attr.id +'" value="'+ <?php echo @$_GET['oid'] ?> +'">'+
                                                                '<input type="hidden"  id="attribute_id" value="' + attr.id +'">'+
                                                            '</div>';
                                       attributes.push(attr);
                                       //last_rt = attr.saved_result;
                                        
                                        });

            html_table +=           '<br><input type="button" class="btn btn-success" onclick="save_result(this)" style="float:right;" value="Save">';  
                                    '</div>';

            attribute_detail.html(html_table);  
            
    }
    function save_result(button) {
        
    let rt = '';
    let procedure_id = '';
    let procedure_name = '';
    let order_detail_id = '';
    let order_id = '';
    let attribute_name = '';
    let registered_by = <?=$loggeduserdata['id']?>;
    let patient_id = <?=$patient['id']?>;
    
    let result = [];
    
        $.each(attributes, function(i, attr) {
            
            rt = $('#result' + attr.id).val();
            //alert(rt);
            
            //alert(result_flag);
            procedure_id = $('#procedure_id' + attr.id).val();
            procedure_name = $('#procedure_name' + attr.id).val();
            order_detail_id = $('#order_detail_id' + attr.id).val();
            order_id = $('#order_id' + attr.id).val();
            attribute_name = $('#attribute_name' + attr.id).val();
            
            let temp_result = {};
            temp_result['attribute_id'] = attr.id;
            temp_result['attribute_name'] = attribute_name;
            temp_result['procedure_id'] = procedure_id;
            temp_result['procedure_name'] = procedure_name;
            temp_result['order_detail_id'] = order_detail_id;
            temp_result['order_id'] = order_id;
            temp_result['registered_by'] = registered_by;
            temp_result['patient_id'] = patient_id;
            temp_result['result'] = rt;
            result.push(temp_result);
            
            
        });
        
        
    if(validateresultattributes(result)){
        
        $.ajax({
            url: '/hospital/nurse/saveresult',
            data: {
                result_detail: result
            },
            method: 'POST',
            async: false,
            dataType: 'json',
            beforeSend: function() {
                $(button).attr('disabled', true).val('Saving...');
            },
            success: function(result) {
                //alert(result.test);
                location = '/hospital/nurse/procedurefollowup?oid='+result.value+'&id='+result.pid;
            },
            error: function(jqXHR, status, error) {
                alert(error);
                $(button).val('Save').attr('disabled', false);
            },
            complete: function() {
                
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
    

</script>
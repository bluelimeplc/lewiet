<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo lang('Reception');?></h1>
            <small><?php echo lang('Lab Requests');?></small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/reception/index?id=0">New Patient</a></li>
            </ol>
        </div>
    </section>
    
    <section class="content">
     
        <div class="row">


            <!-- Main content -->


            <div class="col-md-12">

                <div class="panel panel-bd">
                    
                        
                    <div class="panel-body   table-responsive">
                    <?php
                            $attributes = array('role'=>'form');
                            helper('form');
                            echo form_open_multipart('/hospital/reception/labrequestlist', $attributes);
                        ?>
                        <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <div class="col-sm-6">
                                <input id="search_appointment" type="text" name="search_labrequest"placeholder="Search Lab requests"
                                    class="form-control">
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-success">Search <i
                                        class="fa fa-search"></i></button>
                            </div>

                        </div>
                    </form>
                    <?php
                        form_close();
                    ?>
                    <br>
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="all"><?php echo lang('Laboratory'); ?></th>
                                    <th class="all"><?php echo lang('Card No'); ?></th>
                                    <th class="all"><?php echo lang('Patient Name'); ?></th>
                                    <th class="all"><?php echo lang('Phone Number'); ?></th>
                                    <th class="all"><?php echo lang('Doctor'); ?></th>
                                    <th class="all"><?php echo lang('Delivery Date'); ?></th>
                                    <th class="desktop"><?php echo lang('Action'); ?></th>
                                </tr>
                            </thead>

                            <tbody>
                                    
                                <?php 
                                    $rid = 0;
                                    foreach ($labRequests as $request): 
                                        $rid = $request['id']; 
                                ?>
                                    
                                <tr>
                                    <td><?php echo $request['company_name']?></td>
                                    <td><?php echo "MRN-".sprintf('%05d',$request['patient_id'])?></td>
                                    <td><?php echo $request['patient_name']?></span></td>
                                    <td><?php echo $request['mobile']?></td>
                                    <td><?php echo $request['registered_by_name']?></td>
                                    <td><?php echo $new_date_format = date('Y-m-d', strtotime($request['delivery_date']));?>
                                    </td>
                                    <td>
                                        <a href="viewlabrequest?lid=<?=$request['id']?>&id=<?=$request['patient_id']?>&vid=<?=$request['visit_id']?>"
                                            class="btn btn-primary btn-sm"> <i class="fa fa-eye"></i></a>
                                        <?php
                                        if($request['paid_amount'] == "" && $request['status']==1)
                                            $btnclr = "info";
                                        else if($request['paid_amount'] > 0 )
                                            $btnclr = "success";
                                        else{
                                            $btnclr = "warning";
                                        }
                                        ?>
                                        <a href="#?id=<?=$request['id']?>" class='btn btn-<?=$btnclr?> btn-sm'
                                            data-toggle='modal' data-target="#change_status<?php echo $request['id']?>">
                                            <i class="ti-package"></i> Followup</a>
                                    </td>
                                </tr>

                                <div class="modal fade" id="change_status<?php echo $request['id']?>" tabindex="-1"
                                    role="dialog" aria-labelledby="change_status" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Recieve
                                                    Delivery</h5>
                                            </div>
                                            <div class="modal-body">
                                                <b>Type:</b> <?php echo $request['type']?><br>
                                                &emsp;&emsp;<b>Upper Tray:</b> <?php echo $request['upper_tray']?><br>
                                                &emsp;&emsp;<b>Lower Tray:</b> <?php echo $request['lower_tray']?><br>
                                                <b>Impression Date:</b>
                                                <?php echo $new_date_format = date('Y-m-d', strtotime($request['date_impression']));?><br>
                                                <b>Delivery Date:</b>
                                                <?php echo $new_date_format = date('Y-m-d', strtotime($request['delivery_date']));?><br>

                                            </div>
                                            <div class="modal-body">
                                                <b>Delivery Status:</b>
                                                <?=($request['status']==1)?"Delivered on ".$request['delivered_date']:"Not Delivered" ?>
                                                <br><br>
                                                <?=($request['paid_amount'] > 0)?"<b>Paid Status:</b> ". $request['paid_amount']." Paid on ".$request['paid_date']:""?><br>
                                                <input style="visibility:<?=($request['status']==1)?"hidden":""?>"
                                                    type="button" class="btn btn-primary" value="Delivered"
                                                    onclick="deliverlab(<?php echo $request['id']?>)">

                                                <br>
                                            </div>

                                            <div class="row"><br></div>
                                            <div class="col-md-4"><br>
                                                <input
                                                    style="visibility:<?=($request['status']==0 or $request['paid_amount'] > 0)?"hidden":""?>"
                                                    type="number" step=".11" autocomplete="off" required
                                                    placeholder="Paid Amount" class="form-control"
                                                    id="paid_amount<?php echo $request['id']?>">
                                            </div>
                                            <div class="col-md-6"><br>

                                                <input
                                                    style="visibility:<?=($request['status']==0 or $request['paid_amount'] > 0)?"hidden":""?>"
                                                    type="button" class="btn btn-primary col-md-3" value="Paid"
                                                    onclick="paylab(<?php echo $request['id']?>)">
                                                <br><br>
                                            </div>


                                            <div class="modal-footer">
                                                <br>

                                            </div>
                                            <div id="msgpaid<?php echo $request['id']?>"
                                                class="msgpaid<?php echo $request['id']?> alert alert-danger hide"
                                                role="alert"><span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                

                                <?php endforeach ?>
                               
                            </tbody>
                            
                        </table>
                        <ul class="pagination"  style="padding: 0px;margin-top:-8px;float:right">
                        <li class="page-item"><a class="page-link" href="/hospital/reception/labrequestlist?pid=<?= $rid?>"><?="First"?></a></li>
                        <li class="page-item"><a class="page-link" href="/hospital/reception/labrequestlist?nid=<?= $rid?>">Next</a></li>
                    </ul>  
                            
                        
                    </div>
                    
                    
                            
                </div>
            </div>
      
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-bd">
            <div class="panel-heading">

                <div class="panel-title" style="background-color: gold;">
                    <h4>Near Delivery Date</h4>
                </div>
            </div>
                <div class="panel-body table-responsive">
                <table width="100%" class="table table-striped table-bordered table-hover dt-responsive"
                            id="appointmentS">
                            <thead>
                                <tr>
                                    <th class="all"><?php echo lang('Laboratory'); ?></th>
                                    <th class="all"><?php echo lang('Card No'); ?></th>
                                    <th class="all"><?php echo lang('Patient Name'); ?></th>
                                    <th class="all"><?php echo lang('Phone Number'); ?></th>
                                    <th class="all"><?php echo lang('Doctor'); ?></th>
                                    <th class="all"><?php echo lang('Delivery Date'); ?></th>
                                    <th class="desktop"><?php echo lang('Action'); ?></th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($labRequests_near_delivery as $request):  ?>
                                <tr>
                                    <td><?php echo $request['company_name']?></td>
                                    <td><?php echo "MRN-".sprintf('%05d',$request['patient_id'])?></td>
                                    <td><?php echo $request['patient_name']?></span></td>
                                    <td><?php echo $request['mobile']?></td>
                                    <td><?php echo $request['registered_by_name']?></td>
                                    <td style="background-color: <?= date('Y-m-d', strtotime($request['delivery_date'])) === date('Y-m-d')?"gold":"" ?>"><?php echo $new_date_format = date('Y-m-d', strtotime($request['delivery_date']));?>
                                    </td>
                                    <td>
                                        <a href="viewlabrequest?lid=<?=$request['id']?>&id=<?=$request['patient_id']?>&vid=<?=$request['visit_id']?>"
                                            class="btn btn-primary btn-sm"> <i class="fa fa-eye"></i></a>
                                        <a href="#?id=<?=$request['id']?>" class='btn btn-warning btn-sm'
                                            data-toggle='modal' data-target="#change_status<?php echo $request['id']?>">
                                            <i class="ti-package"></i> Followup</a>
                                    </td>
                                </tr>

                                <?php endforeach ?>
                            </tbody>
                        </table>
                    
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-bd">
            <div class="panel-heading">

            <div class="panel-title" style="background-color: red;">
                <h4>Passed Delivery Date</h4>
            </div>
            </div>
                <div class="panel-body table-responsive">
                <table width="100%" class="table table-striped table-bordered table-hover dt-responsive"
                            id="appointmentS">
                            <thead>
                                <tr>
                                    <th class="all"><?php echo lang('Laboratory'); ?></th>
                                    <th class="all"><?php echo lang('Card No'); ?></th>
                                    <th class="all"><?php echo lang('Patient Name'); ?></th>
                                    <th class="all"><?php echo lang('Phone Number'); ?></th>
                                    <th class="all"><?php echo lang('Doctor'); ?></th>
                                    <th class="all"><?php echo lang('Delivery Date'); ?></th>
                                    <th class="desktop"><?php echo lang('Action'); ?></th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($labRequests_passed_delivery as $request):  ?>
                                <tr>
                                    <td><?php echo $request['company_name']?></td>
                                    <td><?php echo "MRN-".sprintf('%05d',$request['patient_id'])?></td>
                                    <td><?php echo $request['patient_name']?></span></td>
                                    <td><?php echo $request['mobile']?></td>
                                    <td><?php echo $request['registered_by_name']?></td>
                                    <td style="background-color: <?= date('Y-m-d', strtotime($request['delivery_date'])) < date('Y-m-d')?"red":"" ?>"><?php echo $new_date_format = date('Y-m-d', strtotime($request['delivery_date']));?>
                                    </td>
                                    <td>
                                        <a href="viewlabrequest?lid=<?=$request['id']?>&id=<?=$request['patient_id']?>&vid=<?=$request['visit_id']?>"
                                            class="btn btn-primary btn-sm"> <i class="fa fa-eye"></i></a>
                                        <a href="#?id=<?=$request['id']?>" class='btn btn-warning btn-sm'
                                            data-toggle='modal' data-target="#change_status<?php echo $request['id']?>">
                                            <i class="ti-package"></i> Followup</a>
                                    </td>
                                </tr>
                                <div class="modal fade" id="change_status<?php echo $request['id']?>" tabindex="-1"
                                    role="dialog" aria-labelledby="change_status" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Recieve
                                                    Delivery</h5>
                                            </div>
                                            <div class="modal-body">
                                                <b>Type:</b> <?php echo $request['type']?><br>
                                                &emsp;&emsp;<b>Upper Tray:</b> <?php echo $request['upper_tray']?><br>
                                                &emsp;&emsp;<b>Lower Tray:</b> <?php echo $request['lower_tray']?><br>
                                                <b>Impression Date:</b>
                                                <?php echo $new_date_format = date('Y-m-d', strtotime($request['date_impression']));?><br>
                                                <b>Delivery Date:</b>
                                                <?php echo $new_date_format = date('Y-m-d', strtotime($request['delivery_date']));?><br>

                                            </div>
                                            <div class="modal-body">
                                                <b>Delivery Status:</b>
                                                <?=($request['status']==1)?"Delivered on ".$request['delivered_date']:"Not Delivered" ?>
                                                <br><br>
                                                <?=($request['paid_amount'] > 0)?"<b>Paid Status:</b> ". $request['paid_amount']." Paid on ".$request['paid_date']:""?><br>
                                                <input style="visibility:<?=($request['status']==1)?"hidden":""?>"
                                                    type="button" class="btn btn-primary" value="Delivered"
                                                    onclick="deliverlab(<?php echo $request['id']?>)">

                                                <br>
                                            </div>

                                            <div class="row"><br></div>
                                            <div class="col-md-4"><br>
                                                <input
                                                    style="visibility:<?=($request['status']==0 or $request['paid_amount'] > 0)?"hidden":""?>"
                                                    type="number" step=".11" autocomplete="off" required
                                                    placeholder="Paid Amount" class="form-control"
                                                    id="paid_amount<?php echo $request['id']?>">
                                            </div>
                                            <div class="col-md-6"><br>

                                                <input
                                                    style="visibility:<?=($request['status']==0 or $request['paid_amount'] > 0)?"hidden":""?>"
                                                    type="button" class="btn btn-primary col-md-3" value="Paid"
                                                    onclick="paylab(<?php echo $request['id']?>)">
                                                <br><br>
                                            </div>


                                            <div class="modal-footer">
                                                <br>

                                            </div>
                                            <div id="msgpaid<?php echo $request['id']?>"
                                                class="msgpaid<?php echo $request['id']?> alert alert-danger hide"
                                                role="alert"><span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    
                </div>
            </div>
        </div>
         

    </section>
</div>
</div>

<script>
function deliverlab(request_id) {
    if (confirm('Register Delivery?')) {
        $.ajax({
            'url': "<?php echo base_url(); ?>hospital/reception/changelabrequeststatus",
            'type': 'POST',
            'data': {
                'request_id': request_id
            },
            'success': function(data) {
                //console.log(data);
                if (data != null) {
                    dental_show_flash('msgdelivery', data);
                    reloadafterstatus();
                }
            }
        });
    }
}

function paylab(request_id) {

    var paid_amount_id = '#paid_amount' + request_id;
    var msgpaid = 'msgpaid' + request_id;
    var paid_amount = $(paid_amount_id).val();
    //alert(paid_amount);
    if (paid_amount != '') {
        $.ajax({
            'url': "<?php echo base_url(); ?>hospital/reception/paylab",
            'type': 'POST',
            'data': {
                'request_id': request_id,
                'paid_amount': paid_amount
            },
            'success': function(data) {
                if (data != null) {
                    dental_show_flash(msgpaid, data);
                    reloadafterstatus();
                }
            }
        });
    } else
        dental_show_flash(msgpaid, "Enter Amount");
}

function reloadafterstatus() {
    document.location.reload();
}
</script>
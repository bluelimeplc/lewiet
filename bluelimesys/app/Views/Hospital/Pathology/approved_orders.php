
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo lang('Pathology');?></h1>
            <small><?php echo lang('Approved Results');?></small>
        </div>
    </section>
    <section class="content">
            
        <!-- Search Area -->
        <div class="row">
            <?php
                $attributes = array('role'=>'form');
                helper('form');
                echo form_open_multipart('/hospital/pathology/approved_results', $attributes);
            ?>

        <!-- Search Area -->
                        
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd">
                    <div class="panel-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <div class="col-sm-6">
                                <input type="text" name="search_key" class="form-control" >
                                </div>
                                <div class="col-sm-2">
                                    <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button> 
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    
            <?php
                form_close();
            ?> 
                <!-- Main content -->
                    
            <div class="col-md-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        
                    </div>
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover dt-responsive" id="appointmentS">
                            <thead>
                                <tr>
                                    <th class="all">Patient Name</th>
                                    <th class="all">Card No.</th>
                                    <th class="all">Mobile</th>
                                    <th class="all">Approved By</th>
                                    <th class="desktop" >Approved Date</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php
                                    foreach($orders as $order){
                                ?>
                                <tr><td><?=$order['patient_name']?></td>
                                <td><?="MRN-".sprintf('%05d', $order['patient_id'])?></td>
                                <td><?=$order['mobile']?></td>
                                <td><?=$order['approved_by']?></td>
                                <td><?=$order['result_approved_date']?></td>
                                <td>
                                    <a class="btn btn-primary btn-xs" onclick="viewbiopsyresult(<?=$order['id']?>)"><i class="fa fa-eye"title="View Result" data-toggle="tooltip" data-html="true"></i></a>&emsp14; 
                                    <?php
                                        if(BluelimeSystem::$Authentication::get_logged_user_data()['role'] == "SUPER_ADMIN"){
                                    ?>
                                        <a class="btn btn-danger btn-xs" onclick="reverse_approved(<?=$order['id']?>)" ><i class="fa fa-undo"title="Reverse Result" data-toggle="tooltip" data-html="true"></i></a>
                                    <?php
                                        }
                                    ?> 
                                </td></tr>
                                
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
</div>




<script>
    function viewbiopsyresult(order_main_id){

                    location = '/hospital/pathology/viewbiopsyresult?order_id=' + order_main_id;
                            
                }
    
    function reverse_approved(order_main_id){
        if(confirm('Are you sure you want to Reverse?')){
        $.ajax({
            method: 'post',
            url: "/hospital/pathology/reverseresult",
            data: {"order_main_id":order_main_id},
            success: function(data) { 
                location = '/hospital/pathology/order_detail?order_main_id='+ order_main_id;
                
            },
            error: function(jqXHR, status, error) {
                alert(error);
        }
        });
    }
    }
</script>
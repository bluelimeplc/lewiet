
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo lang('Nurse');?></h1>
            <small><?php echo lang('Pending Biopsy Orders');?></small>
        </div>
    </section>
    <section class="content">
            
        <!-- Search Area -->
        <div class="row">
            <?php
                $attributes = array('role'=>'form');
                helper('form');
                echo form_open_multipart('/hospital/nurse/', $attributes);
            ?>

        
                        
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd">
                    <div class="panel-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <div class="col-sm-6">
                                <input id="search_key" type="text" name="search_key" class="form-control" >
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
                    
            <div class="col-md-6">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        
                    </div>
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover dt-responsive" id="appointmentS">
                            <thead>
                                <tr>
                                    <th class="all">Patient Name</th>
                                    <th class="all">Mobile</th>
                                    <th class="all">Order By</th>
                                    <th class="desktop" >Ordered Date</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php
                                    foreach($orders as $order){
                                ?>
                                <tr>
                                    <td><?=$order['patient_name']?></td>
                                    <td><?=$order['mobile']?></td>
                                    <td><?=$order['doctor']?></td>
                                    <td><?=date('Y-m-d',strtotime($order['created_at']))?></td>
                                    <td><a class="btn btn-primary btn-xs" onclick="view_order_detail(<?=$order['id']?>)"><i class="fa fa-eye"></i></a></td>
                                </tr>
                                <?php
                                    }
                                ?>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        Results Approved
                    </div>
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover dt-responsive" id="appointmentS">
                            <thead>
                                <tr>
                                    <th class="all">Patient Name</th>
                                    <th class="all">Mobile</th>
                                    <th class="all">Order By</th>
                                    <th class="desktop" >Ordered Date</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <?php
                                    foreach($orders_result as $order){
                                ?>
                                <tr>
                                    <td><?=$order['patient_name']?></td>
                                    <td><?=$order['mobile']?></td>
                                    <td><?=$order['doctor']?></td>
                                    <td><?=date('Y-m-d',strtotime($order['created_at']))?></td>
                                    <td><a class="btn btn-primary btn-xs" onclick="view_order_detail(<?=$order['id']?>)"><i class="fa fa-eye"></i></a></td>
                                </tr>
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
    function view_order_detail(order_main_id){

                    location = '/hospital/nurse/order_detail?order_main_id=' + order_main_id;
                            
                }
</script>
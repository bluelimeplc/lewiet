<?php
    $searchkey = $this->data['searchkey'] ?? '';
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
                <small>Orders</small>
                <ol class="breadcrumb">
                    <li class="active"><a href="/hospital/nurse/index">Dashboard</a></li>
                </ol>
            </div>
        </section>
      
                <!-- Main content -->
        <section class="content">
            <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    echo form_open_multipart('/hospital/nurse/orders', $attributes);
            ?>

            <!-- Search Area -->
            <div class="row">
                            
                <div class="col-sm-12 col-md-12">
                    <div class="panel panel-bd">
                        <div class="panel-body">
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <input id="search_patient" type="text" name="search_patient" class="form-control" value="<?= $searchkey ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button> 
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                

                
                <!--  Patient List area -->
                <div class="col-sm-12">
                    <div  class="panel panel-default">
                        <div class="panel-body  table-responsive">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="patient_list4">
                                <thead>

                                    <tr>                     
                                        <th>MRN No </th>
                                        <th>Full Name</th>
                                        <th>services</th>
                                        <th>Date</th>
                                        <th></th> 
                                    </tr>

                                </thead>

                                <tbody>
                                <?php
                                        foreach ($orders as $order) {
                                            
                                    ?>
                                    <tr class="odd gradeX">

                                        

                                        <td style="background-color:<?=$order['patient_id']==0?"khaki":""?>"><?php echo "MRN-".sprintf('%05d', $order['patient_id']); ?><br>
                                                   
                                        </td>
                                        <td style="font-size: larger;"><b><?php echo $order['patient_name'];?></b>
                                        <td style="font-size: small;">
                                            <?php  
                                                foreach($bill_details as $detail){
                                                if($detail['bill_id'] == $order['id'])
                                                    echo $detail['service_name'].'<br>';
                                                }
                                        
                                            ?>
                                        </td>
                                        </td>
                                        <td><?php echo $order['payed_date'];?></td>
                                        
                                        
                                        
                                        <td>
                                            <a  class="btn btn-<?php echo (@$order['_notify']['status']==1 or !isset($order['_notify']['status']))?"info":"warning";?> btn-sm" href="procedurefollowup?id=<?=$order['patient_id']?>&oid=<?=$order['id']?>"><i class="fa fa-eye"></i></a>
                                            
                                        </td> 
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

    </div> <!-- /.content-wrapper -->
    <script>
        function focus_search(){
            $("#search_patient").focus().val($("#search_patient").val());

        }
        document.onload(focus_search());
    </script>
<?php
    $companyModel = model('App\Models\CompanyModel');
    $serviceModel = model('App\Models\ServiceModel');
    $patientModel = model('App\Models\PatientModel');
    
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
                <h1>Reception</h1>
                <small>Pending Bills</small>
                <ol class="breadcrumb">
                    <li class="active"><a href="#">Recieved Bills </a></li>
                </ol>
            </div>
        </section>
      
                <!-- Main content -->
        <section class="content">
            <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    echo form_open_multipart('/hospital/reception/pricelist', $attributes);
            ?>

            
            <div class="row">
                 <!--  Search area -->           
                <div class="col-sm-12 col-md-12">
                    <div class="panel panel-bd">
                        <div class="panel-body">
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <div class="col-sm-8">
                                        <input id="search_patient" type="text" name="search_patient" class="form-control">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success">Search</button> 
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                

                
                <!--  Bill list area -->
                <div class="col-sm-12">
                    <div  class="panel panel-default">
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="patient_list4">
                                <thead>

                                    <tr>                     
                                        <th>Price Type</th>
                                        <th>Company Name</th>
                                        <th>Registered By</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        
                                        <th><?php echo lang('Action');?></th> 
                                    </tr>

                                </thead>

                                <tbody>
                                    <?php
                                    foreach ($bills as $bill) :
                                    ?>
                                    
                                    <tr>

                                        <td><?php echo "MRN-".sprintf('%05d', $bill['patient_id']); ?></td>
                                        <td><?php echo  $bill['full_name'] ?> </td>
                                        <td><?php echo  $bill['company_name'] ?> </td>
                                        <td><?php echo  $bill['billed_date'] ?> </td>
                                        
                                        <td>
                                        
                                            <a  class="btn btn-xs btn-info" href="<?php echo base_url();?>/hospital/reception/invoicebill?id=<?= $bill['id']; ?>"><i class="fa fa-eye"></i> </a>
                                            <a  class="btn btn-xs btn-danger" href="<?php echo base_url();?>/hospital/reception/invoicebills?id=<?php echo $bill['id'];?>" onclick="return confirm('Are you sure you want to delelte?');">
                                            <i class="fa fa-trash"></i> </a>
                                            
                                        </td> 
                                    </tr>
                                    <?php
                                        endforeach
                                    ?>
                                </tbody>
                            </table> 
                        </div>
                    </div>
                </div>

            </div>            
        </section>

    </div> <!-- /.content-wrapper -->
    
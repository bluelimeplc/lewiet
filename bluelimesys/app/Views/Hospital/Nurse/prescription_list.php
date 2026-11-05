
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
            <small><?php echo lang('Prescription Orders');?></small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/nurse/index?id=0">New Patient</a></li>
            </ol>
        </div>
    </section>
    <section class="content">
            
            <!-- Search Area -->
            <div class="row">
            <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    echo form_open_multipart('/hospital/nurse/prescriptionlist', $attributes);
            ?>

            <!-- Search Area -->
                            
                    <div class="col-sm-12 col-md-12">
                        <div class="panel panel-bd">
                            <div class="panel-body">
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                        <input id="search_prescription" type="text" name="search_prescription" class="form-control" >
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
                                        
                                        <div class="panel-title" >
                                            <h4><?php echo lang('Prescriptions');?> </h4>
                                        </div>
                                    </div>
                                    <div class="panel-body  table-responsive">
                                        <table width="100%" class="table table-striped table-bordered table-hover dt-responsive" id="appointmentS">
                                            <thead>
                                                <tr>
                                                    <th class="all"><?php echo lang('Prescription No'); ?></th>
                                                    <th class="all"><?php echo lang('Card No'); ?></th>
                                                    <th class="all"><?php echo lang('Patient Name'); ?></th>
                                                    <th class="all"><?php echo lang('Phone Number'); ?></th>
                                                    <th class="all"><?php echo lang('Doctor'); ?></th>
                                                    <th class="all"><?php echo lang('Date'); ?></th>
                                                    <th class="desktop"><?php echo lang('Action'); ?></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php 
                                                    foreach ($prescriptions as $prescription): 
                                                ?>

                                                <tr>
                                                    
                                                    <td><?php echo sprintf('%05d',$prescription['id'])?></td>
                                                    <td><?php echo "MRN-".sprintf('%05d',$prescription['patient_id'])?></td>
                                                    <td><?php echo $prescription['patient_name']?></span></td>
                                                    <td><?php echo $prescription['mobile']?></td>
                                                    <td><?php echo $prescription['registered_by_name']?></td>
                                                    <td><?php echo $new_date_format = date('Y-m-d', strtotime($prescription['registered_date']));?></td>
                                                    
                                                    
                                                    <td>
                                                        <a href="viewprescription?pid=<?=$prescription['id']?>&id=<?=$prescription['patient_id']?>" class="btn btn-primary btn-sm"> <i class="fa fa-eye"></i></a>
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

                        
                            
    </section>
            </div>
</div>


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
            <small><?php echo lang('Printouts');?></small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/reception/index?id=0">New Patient</a></li>
            </ol>
        </div>
    </section>
    <section class="content">
            
            <!-- Search Area -->
            <div class="row">
            <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    echo form_open_multipart('/hospital/reception/printoutlist', $attributes);
            ?>

            <!-- Search Area -->
                            
                    <div class="col-sm-12 col-md-12">
                        <div class="panel panel-bd">
                            <div class="panel-body">
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                        <input id="search_prescription" type="text" name="search_printout" class="form-control" >
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
                                            <h4><?php echo lang('For Print');?> </h4>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <table width="100%" class="table table-striped table-bordered table-hover dt-responsive" id="appointmentS">
                                            <thead>
                                                <tr>
                                                    <th class="all"><?php echo lang('Document No'); ?></th>
                                                    <th class="all"><?php echo lang('Type'); ?></th>
                                                    <th class="all"><?php echo lang('Card No'); ?></th>
                                                    <th class="all"><?php echo lang('Patient Name'); ?></th>
                                                    <th class="all"><?php echo lang('Phone Number'); ?></th>
                                                    <th class="all"><?php echo lang('Doctor'); ?></th>
                                                    <th class="all"><?php echo lang('Date'); ?></th>
                                                    <th class="desktop"><?php echo lang('Action'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($printout_lists as $printout_list):  ?>
                                                <tr>
                                                    <td><?php echo sprintf('%05d',$printout_list['id'])?></td>
                                                    <td><?php echo $printout_list['type'] ?></td>
                                                    <td><?php echo "MRN-".sprintf('%05d',$printout_list['patient_id'])?></td>
                                                    <td><?php echo $printout_list['patient_name']?></span></td>
                                                    <td><?php echo $printout_list['mobile']?></td>
                                                    <td><?php echo $printout_list['registered_by_name']?></td>
                                                    <td><?php echo $new_date_format = date('Y-m-d', strtotime($printout_list['registered_date']));?></td>
                                                    <td>
                                                        <?php
                                                            if($printout_list['type']==='Prescription'){
                                                                $view = "viewprescription?pid=";
                                                                $button = (@$printout_list['_notifyp']['status']==1 or !isset($printout_list['_notifyp']['status']))?"info":"warning";
                                                            }
                                                            elseif($printout_list['type']==='Medical Certificate'){
                                                                $view = "viewmedicalcertificate?mid=";
                                                                $button = (@$printout_list['_notifym']['status']==1 or !isset($printout_list['_notifym']['status']))?"info":"warning";
                                                            }
                                                            else if($printout_list['type']==='Lab Requests'){
                                                                $view = "viewlabrequest?lid=";
                                                                $button = (@$printout_list['_notifyl']['status']==1 or !isset($printout_list['_notifyl']['status']))?"info":"warning";
                                                            }
                                                        ?>
                                                        <a href="<?=$view.$printout_list['id']?>&id=<?=$printout_list['patient_id']?>&vid=<?=$printout_list['patient_id']?>" class="btn btn-<?=$button?> btn-sm"> <i class="fa fa-eye"></i></a>
                                                    
                                                    </td>
                                                </tr>
                                                <?php endforeach ?>
                                                
                                            </tbody>
                                        </table>
                                        
                                    </div>
                                </div>
                            </div>

                        
                            
    </section>
            </div>
</div>

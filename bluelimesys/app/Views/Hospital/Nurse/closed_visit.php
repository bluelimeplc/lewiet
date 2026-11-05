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
                <small>Closed Visits</small>
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
                    echo form_open_multipart('/hospital/nurse/previousvisit', $attributes);
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
                                        <th>Doctor Name</th>
                                        <th><?php echo lang('Action');?></th> 
                                    </tr>

                                </thead>

                                <tbody>
                                <?php
                                        foreach ($visits as $visit) {
                                    ?>
                                    <tr class="odd gradeX">

                                        

                                        <td><?php echo "MRN-".sprintf('%05d', $visit['patient_id']); ?><br>
                                                <i  id="patient_has_active_visite_icon " class="dental-flag-icon" style="color: orange;" <?= (@$visit['patient_has_active_visite'])?"":"hidden" ?> >
                                                    <i class="fa fa-user-md jump_dental" data-toggle="tooltip" data-html="true" title="On Visit"></i>
                                                </i>     
                                        </td>
                                        <td style="font-size: larger;"><b><?php echo $visit['patient_name'];?></b></td>
                                        
                                        <td><?php echo $visit['doctor_name'];?></td>
                                        
                                        <td>
                                            
                                            <a  class="btn btn-info btn-sm" href="<?php echo base_url();?>hospital/nurse/newmedicalcertificate?pid=<?php echo $visit['patient_id'];?>&vid=<?php echo $visit['id'];?>"><i class="fa fa-eye"></i></a>
                                            
                                            
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
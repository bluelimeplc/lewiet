<?php
    $closed_visits = $this->data['closed_visits'];
    $loggeduserdata = $this->data['loggeduserdata'];
    $active_menu = $this->data['active_menu'];
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
                <h1>Doctor</h1>
                <small>My Patients</small>
            </div>
        </section>
                <!-- Main content -->
        <section class="content">

            <!-- Search Area -->
            <?php
                    $attributes = array('role'=>'form');
                    helper('form');
                    echo form_open_multipart('/hospital/doctor/mypatients', $attributes);
            ?>

            <!-- Search Area -->
            <div class="row">
                            
                    <div class="col-sm-12 col-md-12">
                        <div class="panel panel-bd">
                            <div class="panel-body">
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                        <input id="search_my_patients" type="text" name="search_my_patients" class="form-control" >
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
            <div class="row">
                <!--  Patient List area -->
                <div class="col-sm-12">
                    <div  class="panel panel-default">
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover data" id="patient_list">
                                <thead>
                                    <tr>                     
                                        <th width="100">MRN N<u>o</u></th>
                                        <th width="100">Visit Date</th>
                                        <th>Full Name</th>
                                        <th><?php echo lang('History');?></th> 
                                    </tr>
                                </thead>

                                <tbody>
                                <?php
                                        foreach ($closed_visits as $visit) {
                                            $hasCompany = false;
                                            if(trim($visit['_patient']['company_name']) != '')
                                                $hasCompany = true;
                                    ?>
                                    <tr class="odd gradeX">

                                        <td>
                                            <?php echo "MRN-".sprintf('%05d', $visit['patient_id']); ?>
                                        </td>
                                        <td>
                                            <?php echo date('Y-m-d', strtotime($visit['visit_date'])); ?>
                                        </td>
                                        <td><?php echo $visit['patient_name'] ;?><br>
                                            <?php echo $visit['_patient']['mobile'];?>
                                        </td>

                                        <td <?= ($hasCompany) ? 'rowspan="2"' :''; ?>>
                                            <a  class="btn btn-<?php echo (@$visit['_notify']['status']==1 or !isset($visit['_notify']['status']))?"info":"warning";?> btn-sm" href="<?php echo base_url();?>hospital/doctor/patienthistory?visit_id=<?php echo $visit['id'];?>"><i class="fa fa-eye"></i></a>
                                        </td>
                                        <?= ($hasCompany) ? '</tr><tr><td colspan="3" class="bg-warning">'.$visit['_patient']['company_name'].'</td>' :''; ?>
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
        //document.onload(focus_search());
    </script>
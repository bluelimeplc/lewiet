<?php
    $waithing_visits = $this->data['waithing_visits'];
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
                <small>Waiting Patients</small>
            </div>
        </section>
                <!-- Main content -->
        <section class="content">

            <!-- Search Area -->
            <div class="row">
                <!--  Patient List area -->
                <div class="col-sm-12">
                    <div  class="panel panel-default">
                        <div class="panel-body">
                            <div>
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <input type="text" name="search_key" placeholder="Search Patient" class="form-control" id="search_key">
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <table width="100%" class="table table-striped table-bordered table-hover data" id="patient_list">
                                <thead>
                                    <tr>                     
                                        <th width="50">QNo.</th>
                                        <th width="100">MRN N<u>o</u></th>
                                        <th data-orderable="false">Full Name</th>
                                        <th>Sex</th>
                                        <th><?php echo lang('Action');?></th> 
                                    </tr>
                                </thead>

                                <tbody>
                                <?php
                                
                                        foreach ($waithing_visits as $visit) {
                                            $hasCompany = false;
                                            if(trim($visit['_patient']['company_name']) != '')
                                                $hasCompany = true;
                                    ?>
                                    <tr class="odd gradeX">

                                        

                                        <td style="background-color:<?=(($visit['bill_id']==-1 || $visit['is_free']>=1))?"darksalmon":"";?>">
                                            <?php echo $visit['queue_no']; ?>
                                        </td>
                                        <td>
                                            <?php echo sprintf('%05d', $visit['patient_id']); ?>
                                        </td>
                                        <td><a href="<?php echo base_url();?>hospital/doctor/visit?visit_id=<?php echo $visit['id'];?>"><b style="font-size: larger;"><?php echo $visit['patient_name'] ;?></b></a><br>
                                            <?php echo $visit['_patient']['mobile'];?>
                                        </td>
                                        <td><?php echo $visit['_patient']['sex'];?></td>

                                        <td <?= ($hasCompany) ? 'rowspan="2"' :''; ?>>
                                            <a  class="btn btn-<?php echo (@$visit['_notify']['status']==1 or !isset($visit['_notify']['status']))?"info":"warning";?> btn-sm" href="<?php echo base_url();?>hospital/doctor/visit?visit_id=<?php echo $visit['id'];?>"><i class="fa fa-eye"></i></a>
                                        </td>
                                        <?= ($hasCompany) ? '</tr><tr><td colspan="4" class="bg-warning">'.$visit['_patient']['company_name'].'</td>' :''; ?>
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
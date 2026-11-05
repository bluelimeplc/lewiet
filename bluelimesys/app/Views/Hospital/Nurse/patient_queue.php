<?php

use App\Controllers\Hospital\Doctor;

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
                <small>Patient Queue</small>
                <ol class="breadcrumb">
                    <li class="active"><a href="/hospital/nurse/index">Dashboard</a></li>
                </ol>
            </div>
        </section>
      
                <!-- Main content -->
        <section class="content">    
        <div class="row">            
                <!--  Patient List area -->
                <div class="col-sm-12"><?php foreach ($doctors as $doctor) {
                            ?>
                    <div  class="panel panel-bd col-sm-<?=12/count($doctors)?>">
                        
                        <div class="panel-body">
                        
                            <table  class="table table-striped table-bordered table-hover" style="font-size: smaller;">
                                <thead>
                                <?=$doctor?>
                                    <tr>                     
                                        <th>Q.NO</th>
                                        <th>Card</th>
                                        <th>Full Name</th>
                                        
                                    </tr>

                                </thead>

                                <tbody>
                                <?php
                                        foreach ($visits as $visit) {
                                            if($visit['doctor_name']==$doctor){
                                    ?>
                                    <tr style="background-color: <?= ($visit['view_status']==1) ?"":"burlywood" ?>">

                                        

                                        <td><?php echo $visit['queue_no']; ?>   
                                        <td><?php echo sprintf('%05d', $visit['patient_id']); ?>   
                                        </td>
                                        <td><b><?php echo $visit['patient_name'];?></b></td>
                                        
                                    </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table> 
                            
                        </div>
                        
                    </div><?php
                        }
                        ?>
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
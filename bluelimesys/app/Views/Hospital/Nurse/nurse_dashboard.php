
<?php
    
?>
            <!-- =============================================== -->
            <!-- =============================================== -->
            <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

                <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo "Nurse Dashboard";?></h1>
            <small><?php echo "Nurse Dashboard";?></small>
            
        </div>
    </section>

        
        <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="panel panel-bd lobidisable">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4>Doctors Schedule</h4>
                    </div>
                </div>
            <?php
            //\BluelimeSystem::show_and_die($this->data['doctors']);
                foreach($this->data['doctors'] as $doctors):
            ?>
            
            <div class="col-sm-4">
                <div class="panel panel-bd">
                    <a href="#">
                        <div class="panel-body" style="min-height:400px;">
                            <div class="statistic-box">
                                <h3><span class="count-number"><?php echo $doctors ?></span> </h3>
                                <div  class="col-sm-12">
                                    <table class="table table-striped table-bordered table-hover data">
                                        <tr style="font-weight: bold;">
                                            <td>Day</td><td>Start</td><td>End</td><td>Per Patient</td><td></td>
                                        </tr> 
                                        <?php
                                            foreach($this->data['schedules'] as $row):
                                                if($row['doctor_name']==$doctors){
                                        ?>
                                                <tr>
                                                    <td><?=\BluelimeSystem::no_to_day($row['day']) ?></td>
                                                    <td><?=$row['start_time'] ?></td>
                                                    <td><?=$row['end_time'] ?></td>
                                                    <td><?=$row['per_patient_time'] ." min" ?></td>
                                                </tr>
                                        <?php
                                                }
                                            endforeach;
                                        ?>
                                    </table> 
                                </div>
                                <div class="sparkline1 text-center"></div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <?php
                endforeach;
            ?>
        </div>
    </section> <!-- /.content -->
</div> <!-- /.content-wrapper -->

<script>
    function print_printable_document(id){
        var printContents = document.getElementById(id).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
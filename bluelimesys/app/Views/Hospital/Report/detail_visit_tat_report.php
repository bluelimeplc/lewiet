<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Admin</h1>
            <small>Appointment Report</small>
            <ol class="breadcrumb">
                            <?= view("Include/_admin_report_top_menu"); ?>
                        </ol>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div style="float: right;"><a href="tatreport?selected_date=<?= substr($from_date,0,10);?>&end_date=<?= substr($to_date,0,10);?>" class="btn btn-success">Average</a></div>
                        <div class="panel-title"><h4>Filter</h4></div>
                        
                        <div class="panel-body">
                            <div class="row">
                                <form action="" method="GET">
                                    <div class=" col-lg-3" style="margin-bottom: 2px;">
                                        <div class="input-group">
                                            <span class="input-group-addon" ><b>From</b></span>
                                            <input type="date" class="form-control" value="<?= substr($from_date,0,10);?>" name="selected_date">
                                        </div>
                                    </div>
                                    <div class=" col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" ><b>To</b></span>
                                            <input type="date" class="form-control" value="<?=substr($to_date,0,10);?>"  name="end_date">    
                                        </div>
                                        <input type="submit" style="visibility:hidden">
                                    </div>
                                   <div class=" col-lg-3"><input type="submit" class="btn btn-success" value="Filter"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-12">
                <div class="col-sm-12 col-md-12" style="font-size: x-small;">
                                <div class="panel panel-bd">
                                    <div class="panel-heading">
                                        <div class="panel-title">
                                            <h4>Patient Detail TAT in Minute</h4>
                                        </div>
                                    </div>
                                    <div class="panel-body table-responsive">
                                        <div class="form-group col-md-12">
                                            <table class="table table-bordered table-striped" id="item_sales_report_table">
                                <thead>
                                    <tr>
                                        <th>Visit Type</th>
                                        <th>Doctor Name</th>
                                        <th>Patient Name</th>
                                        <th style="width: 80px;">Visit Date</th>
                                        <th>Waiting/Booked</th>
                                        <th>Assigned Time</th>
                                        <th>Treatment Started</th>
                                        <th>Treatment Finished</th>
                                        <th>Reception Area</th>
                                        <th>UpTo Doctor</th>
                                        <th>Total Stayed Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach($tat_lists as $row){
                                        if($row['TotalStayedTime'] <= 0){
                                            continue;
                                        }
                                    ?>
                                    <tr><td><?=$row['visit_type']?></td>
                                    <td><?=$row['doctor_name']?></td>
                                    <td><?=$row['patient_name']?></td>
                                    <td><?=date('Y-m-d',strtotime($row['AssignedTime']))?></td>
                                    <td><?=date('h:i A',strtotime($row['WaitingRegistrationTime']))?></td>
                                    <td><?=date('h:i A',strtotime($row['AssignedTime']))?></td>
                                    <td><?=date('h:i A',strtotime($row['ViewTime']))?></td>
                                    <td><?=date('h:i A',strtotime($row['FinishedTime']))?></td>
                                    <td><?=round($row['ReceptionArea'],2)?></td>
                                    <td><?=round($row['UpToDoctor'],2)?></td>
                                    <td><?=round($row['TotalStayedTime'],2)?></td></tr>
                                <?php
                                        }
                                ?>
                                </tbody>
                            </table>
                                            
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                
            </div>
            
                </div>
    </section>
</div>
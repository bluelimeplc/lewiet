<?php
    $start_date = str_replace("%", "",$this->data['start_date']);
    $end_date = str_replace("%", "",@$this->data['end_date']);
    //\BluelimeSystem::show_and_die($this->data['start_date']);
   
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
                        <h1><?php echo "Admin Dashboard";?></h1>
                        <small><?php echo "Admin Dashboard";?></small>
                        <ol class="breadcrumb">
                            
                            <li class="active"><a href=""><?php echo "Dashboard";?></a></li>
                        </ol>
                    </div>
                </section>
                
                
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            <a href="#">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <h2><span class="count-number"><?php echo $this->data['no_of_patient'] ?></span> </h2>
                                            <div class="small"> <?php echo "Total Patients";?> </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><i class="ti-user"></i></div>
                                </div>
                            </a>    
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?>admin/doctor/Appointment_controller/today_gate_appointment_list">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <h2><span class="count-number"><?php echo $this->data['no_of_visits'];?></span> </h2>
                                            <div class="small"> Total Visits </div>
                                        <div class="sparkline1 text-center"></div>
                                    </div>
                                    <div class="icon"><i class="ti-user"></i></div>
                                </div>
                            </a>    
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                                <a href="<?php echo base_url();?>admin/doctor/Appointment_controller/today_appointment_list">
                                    <div class="panel-body">
                                        <div class="statistic-box">
                                            <h2><span class="count-number"><?php echo $this->data['no_of_appointments'];?></span> </h2>
                                                <div class="small"> <?php echo "Total Appointments"?> </div>
                                            <div class="sparkline1 text-center"></div>
                                        </div>
                                        <div class="icon"><i class="ti-user"></i></div>
                                    </div>
                                </a>    
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
                            <div class="panel panel-bd">
                            <a href="<?php echo base_url();?>admin/doctor/Appointment_controller/appointment_list">
                                <div class="panel-body">
                                    <div class="statistic-box">
                                        <h2><span class="count-number"><?php echo $this->data['no_of_appointments_visit'];?></span> </h2>
                                        <div class="small"><?php echo "Total Visit by Appointment"?></div>
                                        <div class="sparkline2 text-center"></div>
                                    </div>
                                    <div class="icon"><i class="ti-user"></i></div>
                                </div>
                            </a>
                            </div>
                        </div>
                    </div>
        <div class="row">
<?php
                    $attributes = array('role'=>'form' ,'method' => 'get');
                    helper('form');
                    echo form_open_multipart('/hospital/admin/admindashboard', $attributes);
                ?>
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd lobidisable">
                    <div class="panel-body">
                        <div class="input-group col-sm-6 col-md-6 col-l-6">
                            <span class="input-group-addon"></span>
                            From:<input type="date" value="<?=isset($start_date)?$start_date:date('Y-m-d');?>"
                                name="start_date" class="form-control">
                            <span class="input-group-addon"></span>
                            To:<input type="date"
                                value="<?=isset($end_date) ? date('Y-m-d', strtotime($end_date)):date('Y-m-d');?>"
                                name="end_date" class="form-control">
                            <span class="input-group-addon"></span>
                            &nbsp;<button class="form-control btn-success">Search</button>

                        </div>
                        <div class="input-group col-sm-6 col-md-6 col-l-6">
                            <span class="input-group-addon" style="visibility:hidden;"></span> This Month <input
                                type="radio" onChange="this.form.submit()" name="selected_range" value="this_month">
                            <span class="input-group-addon" style="visibility:hidden;"></span> Last Month <input
                                type="radio" onChange="this.form.submit()" name="selected_range" value="last_month">
                            <span class="input-group-addon" style="visibility:hidden;"></span> This Year <input
                                type="radio" onChange="this.form.submit()" name="selected_range" value="this_year">
                            <span class="input-group-addon" style="visibility:hidden;"></span> All Time <input
                                type="radio" onChange="this.form.submit()" name="selected_range" value="all_time">

                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close();?>
        </div>


                    <div class="row">
                         <!-- Bar Chart -->
                        <div class="col-sm-12 col-md-12">
                            <div class="panel panel-bd lobidisable">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4><?php echo "Appointment Chart"?></h4>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <canvas id="pieChart" class=""></canvas>
                                </div>
                            </div>
                        </div>
                        
                    </div>

                </section> <!-- /.content -->
            </div> <!-- /.content-wrapper -->
<script>
    
$(document).ready(function() {

var ctx = document.getElementById("pieChart");
var myChart = new Chart(ctx, {
    type: 'pie',
    animation: true,

    data: {
        labels: [<?php foreach ($this->data['appointments'] as $doctors){
            if($doctors['appointment_status']==-1)
                $status = "Cancelled";
            else if ($doctors['appointment_status']==2)
                $status = "Missed";
            else if ($doctors['appointment_status']==3)
                $status = "Visited";
                            echo '"'.$status.'"';
                                echo ',';
                        
                            }
                        ?>],
        datasets: [{
                label: "<?php echo "Income";?>",
                data: [<?php foreach ($this->data['appointments'] as $doctors){
                            echo '"'.$doctors['total'].'"';
                                echo ',';
                        
                            }
                        ?>],
                borderWidth: "3",
                backgroundColor: [
                    "#FFA500",
                    "#FF0000",
                    "#00FFFF"
                ],

            }


        ]

    },
    options: {
        responsive: true,
        legend: {
            display: true,
            position: "top",
            labels: {
                fontColor: "#333"
            }
        }
    }
});


});

</script>            
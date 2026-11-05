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
            <h1><?php echo "Reports";?></h1>
            <small><?php echo "Performance Report";?></small>
            <ol class="breadcrumb">
                <?= view("Include/_admin_report_top_menu"); ?>
            </ol>
        </div>
    </section>


    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php
                    $attributes = array('role'=>'form' ,'method' => 'get');
                    helper('form');
                    echo form_open_multipart('/hospital/report/performancereport', $attributes);
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
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd lobidisable">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo "Doctors Performance By Number of visits and Income"?></h4>
                        </div>
                    </div>
                    <div class="panel-body" height=500px>
                        <div class="col-md-6">
                            <canvas id="pieChart" class=""></canvas>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="panel panel-bd lobidisable">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4>Doctors Number of patient Visit</h4>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-striped">
                                        <?php foreach ($this->data['number_of_visits_by_doctor'] as $doctors){?>
                                        <tr>
                                            <td><?=$doctors['doctor_name']?></td>
                                            <td><?=$doctors['total']?></td>
                                        </tr>

                                        <?php }?>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="panel panel-bd lobidisable">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4>Doctors Income</h4>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-striped">
                                        <?php foreach ($this->data['doctors_income'] as $doctors){?>
                                        <tr>
                                            <td><a href="performancereport?doc_id=<?=$doctors['doctor_id']?>&start_date=<?=isset($start_date)?$start_date:date('Y-m-d');?>&end_date=<?=isset($end_date)?$end_date:date('Y-m-d');?>"><?=$doctors['doctor_name']?></a></td>
                                            <td><?=number_format($doctors['total_amount'],2)?></td>
                                        </tr>

                                        <?php }?>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="panel panel-bd lobidisable">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4>Doctors Detail</h4>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-striped">
                                        <th>
                                           <td>Date</td><td>Doctor</td><td>Patient Name</td><td>Service</td><td>Amount</td>
                                        </th>
                                        <?php foreach ($this->data['doctors_income_detail'] as $doctors){?>
                                            
                                        <tr>
                                            <td></td>
                                            <td><?=$doctors['payed_date']?></td>
                                            <td><?=$doctors['doctor_name']?></td>
                                            <td><?=$doctors['patient_name']?></td>
                                            <td><?=$doctors['service_name']?></td>
                                            <td><?=$doctors['payed_amount']?></td>
                                        </tr>

                                        <?php }?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd lobidisable">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo "Patient Visit"?></h4>
                        </div>
                    </div>
                    <div class="panel-body" height=500px>
                        <div class="col-md-12">
                            <canvas id="barChart" class=""></canvas>
                        </div>
                        
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd lobidisable">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo "Income"?></h4>
                        </div>
                    </div>
                    <div class="panel-body" height=500px>
                        <div class="col-md-12">
                            <canvas id="barChartIncome" class=""></canvas>
                        </div>
                        
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
            labels: [<?php foreach ($this->data['number_of_visits_by_doctor'] as $doctors){
                                echo '"'.$doctors['doctor_name'].'"';
                                    echo ',';
                            
                                }
                            ?>],
            datasets: [{
                    label: "<?php echo "Income";?>",
                    data: [<?php 
                             foreach ($this->data['number_of_visits_by_doctor'] as $dv){
                                foreach ($this->data['doctors_income'] as $doctors){
                                    if($dv['doctor_id']==$doctors['doctor_id']){
                                    echo '"'.$doctors['total_amount'].'"';
                                        echo ',';
                                
                                    }
                                }
                                }
                            ?>],
                    borderWidth: "3",
                    backgroundColor: [
                        "#32CD32",
                        "#8B008B",
                        "#FF8C00",
                        "#1E90FF",
                        "#DAA520",
                        "#20B2AA",
                        "#FFA07A",
                        "#800000",
                        "#FF00FF",
                        "#40E0D0",
                        "#ADFF2F",
                    ],

                },
                {
                    label: "<?php echo "Visit";?>",
                    data: [<?php foreach ($this->data['number_of_visits_by_doctor'] as $doctors){
                                echo '"'.$doctors['total'].'"';
                                    echo ',';
                            
                                }
                            ?>],
                    borderWidth: "3",
                    backgroundColor: [
                        "#32CD32",
                        "#8B008B",
                        "#FF8C00",
                        "#1E90FF",
                        "#DAA520",
                        "#20B2AA",
                        "#FFA07A",
                        "#800000",
                        "#FF00FF",
                        "#40E0D0",
                        "#ADFF2F",
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
$(document).ready(function() {

    var ctx = document.getElementById("barChart");
    var myChart = new Chart(ctx, {
        type: 'bar',
        animation: true,

        data: {
            labels: [<?php foreach ($this->data['number_of_visits'] as $doctors){
                                echo '"'.@$doctors['dt'].'"';
                                    echo ',';
                            
                                }
                            ?>],
            datasets: [
                {
                    label: "<?php echo "No of Visit";?>",
                    data: [<?php foreach ($this->data['number_of_visits'] as $doctors){
                                echo '"'.$doctors['total'].'"';
                                    echo ',';
                            
                                }
                            ?>],
                    borderWidth: "3",
                    backgroundColor: [
                        "#32CD32",
                        "#8B008B",
                        "#FF8C00",
                        "#1E90FF",
                        "#DAA520",
                        "#20B2AA",
                        "#FFA07A",
                        "#800000",
                        "#FF00FF",
                        "#40E0D0",
                        "#ADFF2F",
                        "#32CD32",
                        "#8B008B",
                        "#FF8C00",
                        "#1E90FF",
                        "#DAA520",
                        "#20B2AA",
                        "#FFA07A",
                        "#800000",
                        "#FF00FF",
                        "#40E0D0",
                        "#ADFF2F",
                    ],

                }


            ]

        },
        options: {
            responsive: true,
            legend: {
                display: false,
                position: "top",
                labels: {
                    fontColor: "#333"
                }
            }
        }
    });


});
$(document).ready(function() {

    var ctx = document.getElementById("barChartIncome");
    var myChart = new Chart(ctx, {
        type: 'bar',
        animation: true,

        data: {
            labels: [<?php foreach ($this->data['income'] as $doctors){
                                echo '"'.@$doctors['dt'].'"';
                                    echo ',';
                            
                                }
                            ?>],
            datasets: [
                {
                    label: "<?php echo "Income Amount";?>",
                    data: [<?php foreach ($this->data['income'] as $doctors){
                                echo '"'.$doctors['total_amount'].'"';
                                    echo ',';
                            
                                }
                            ?>],
                    borderWidth: "3",
                    backgroundColor: [
                        "#32CD32",
                        "#8B008B",
                        "#FF8C00",
                        "#1E90FF",
                        "#DAA520",
                        "#20B2AA",
                        "#FFA07A",
                        "#800000",
                        "#FF00FF",
                        "#40E0D0",
                        "#ADFF2F",
                        "#32CD32",
                        "#8B008B",
                        "#FF8C00",
                        "#1E90FF",
                        "#DAA520",
                        "#20B2AA",
                        "#FFA07A",
                        "#800000",
                        "#FF00FF",
                        "#40E0D0",
                        "#ADFF2F",
                    ],

                }


            ]

        },
        options: {
            responsive: true,
            legend: {
                display: false,
                position: "top",
                labels: {
                    fontColor: "#333"
                }
            }
        }
    });


});
</script>
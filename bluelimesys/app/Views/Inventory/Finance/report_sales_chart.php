<?= view('Include/_header') ?>
<?= view('Include/_left_sidebar_top') ?>
<?= view('Inventory/Finance/_left_sidebar_finance') ?>
<?= view('Include/_left_sidebar_bottom') ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
            <small>Sales Report Chart</small>
        </div>
    </section>
<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="panel-title"><h4>Monthly sales</h4></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div style="border: solid 1px #ddd; border-radius: 5px; padding: 10px; margin-bottom: 10px;">
                                        <h4>By Branch</h4>
                                        <div>
                                            <canvas id="chartByBranch" style="height: 300px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div  style="border: solid 1px #ddd; border-radius: 5px; padding: 10px; margin-bottom: 10px;">
                                        <h4>By Branch and Department</h4>
                                        <div>
                                            <canvas id="chartByBranchandDepartment" style="height: 300px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="panel-title"><h4>Compare with last year</h4></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div style="border: solid 1px #ddd; border-radius: 5px; padding: 10px; margin-bottom: 10px;">
                                        <h4>By Branch</h4>
                                        <div>
                                            <canvas id="chartByBranch_lastyear" style="height: 300px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div  style="border: solid 1px #ddd; border-radius: 5px; padding: 10px; margin-bottom: 10px;">
                                        <h4>By Branch and Department</h4>
                                        <div>
                                            <canvas id="chartByBranchandDepartment_lastyear" style="height: 300px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </section>
</div>
<script>
    <?php
        $x_axis = '';
        foreach($date_range_list as $date_range){
            $x_axis .= "'".$date_range['month_name']."',";
        }
        $x_axis = trim($x_axis,",");
        $x_axis = "[$x_axis]";

        $color_list = [
            ['backgroundColor' => 'rgba(196, 67, 67, 0.6)', 'borderColor' => 'rgba(192, 75, 75, 1)'],
            ['backgroundColor' => 'rgba(120, 39, 190, 0.6)', 'borderColor' => 'rgba(163, 75, 192, 1)'],
            ['backgroundColor' => 'rgba(192, 130, 75, 0.6)', 'borderColor' => 'rgba(192, 174, 75, 1)'],
            ['backgroundColor' => 'rgba(192, 75, 161, 0.6)', 'borderColor' => 'rgba(192, 75, 167, 1)'],
            ['backgroundColor' => 'rgba(75, 192, 95, 0.6)', 'borderColor' => 'rgba(79, 192, 75, 1)'],
            ['backgroundColor' => 'rgba(75, 75, 192, 0.6)', 'borderColor' => 'rgba(75, 77, 192, 1)'],
            ['backgroundColor' => 'rgba(192, 75, 75, 0.6)', 'borderColor' => 'rgba(192, 75, 75, 1)'],
            ['backgroundColor' => 'rgba(75, 184, 192, 0.6)', 'borderColor' => 'rgba(75, 190, 192, 1)'],
        ];
    ?>

    function render_chartByBranch(){
        const ctx_chartByBranch = document.getElementById('chartByBranch').getContext('2d');
        const chartByBranch = new Chart(ctx_chartByBranch, {
            type: 'bar',
            data: {
                labels: <?= $x_axis ?>,
                datasets: [
                    <?php
                        $first = true;
                        $cnt = 0;
                        foreach($by_branch as $row){
                            $cnt++;
                            if($first)
                                $first = false;
                            else
                                echo ',';

                            echo '
                    {
                        label: "'.$row['branch_name'].'",
                        data: ['. implode(",",$row['data']).'],
                        
                    }
                            ';
                        }
                    ?>
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    function render_chartByBranch_Lastyear(){
        const ctx_chartByBranch = document.getElementById('chartByBranch_lastyear').getContext('2d');
        const chartByBranch = new Chart(ctx_chartByBranch, {
            type: 'bar',
            data: {
                labels: <?= $x_axis ?>,
                datasets: [
                    
                    <?php
                        $color_list2 = [
                            'positive' => ['backgroundColor' => 'rgba(27, 168, 24, 0.66)', 'borderColor' => 'rgba(10, 253, 67, 0.8)'],
                            'negative' => ['backgroundColor' => 'rgba(193, 23, 23, 0.73)', 'borderColor' => 'rgba(196, 1, 1, 0.92)'],
                        ];
                        $first = true;
                        $cnt = 0;
                        foreach($by_branch as $row){
                            $cnt++;
                            if($first)
                                $first = false;
                            else
                                echo ',';

                            echo '
                    {
                        label: "'.$row['branch_name'].' (Last Year)",
                        data: ['. implode(",",$row['data_last_year']).'],
        
                    }
                            ';
                            echo '
                    ,{
                        label: "'.$row['branch_name'].' (This Year)",
                        data: ['. implode(",",$row['data']).'],
                        
                    }
                            ';
                        }
                    ?>
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function render_chartByBranchandDepartment(){
        const ctx_chartByBranchandDepartment = document.getElementById('chartByBranchandDepartment').getContext('2d');
        const chartByBranchandDepartment = new Chart(ctx_chartByBranchandDepartment, {
            type: 'bar',
            data: {
                labels: <?= $x_axis ?>,
                datasets: [
                    <?php
                        $first = true;
                        $cnt = 2;
                        foreach($by_branch_and_department as $row){
                            $cnt++;
                            if($first)
                                $first = false;
                            else
                                echo ',';

                            echo '
                    {
                        label: "'.$row['branch_name'].' Clinic",
                        data: ['. implode(",",$row['data_clinic']).'],
                        
                    },
                    {
                        label: "'.$row['branch_name'].' Pharmacy",
                        data: ['. implode(",",$row['data_pharmacy']).'],
                        
                    }
                            ';
                        }
                    
                    ?>
                    
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    $(document).ready(function() {
        render_chartByBranch();
        render_chartByBranchandDepartment();
        render_chartByBranch_Lastyear();
    });


    
</script>

<?= view('Include/_footer') ?>
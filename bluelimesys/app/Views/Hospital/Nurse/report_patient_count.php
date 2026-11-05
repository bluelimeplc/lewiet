
<?php
    $date_from = substr($date_from,0,10);
    $date_to = substr($date_to,0,10);

    $patient_count_report = [
        "0-4"      => ["m"=>0,"f"=>0,"t"=>0],
        "5-10"      => ["m"=>0,"f"=>0,"t"=>0],
        "11-19"     => ["m"=>0,"f"=>0,"t"=>0],
        "20-29"     => ["m"=>0,"f"=>0,"t"=>0],
        "30-45"     => ["m"=>0,"f"=>0,"t"=>0],
        "45-64"     => ["m"=>0,"f"=>0,"t"=>0],
        "65-20000"  => ["m"=>0,"f"=>0,"t"=>0]
    ];

    foreach($patient_count as $patient){
        $age_range = get_age_range($patient_count_report,$patient['age']);

        if($patient['sex'] == 'Female')
            $patient_count_report[$age_range]['f']++;
        else
            $patient_count_report[$age_range]['m']++;

        $patient_count_report[$age_range]['t']++;
    }

    function get_age_range($age_list, $age){
        $age_range = "65-20000";
        foreach($age_list as $index => $val){
            if($index == "other")
                continue;

            $ages = explode("-", $index);

            if(($age >= $ages[0]) && ($age <= $ages[1])){
                $age_range = $index;
                break;
            }
        }
        return $age_range;
    }

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
            <h1>Nurse</h1>
            <small>Report / Diagnosis</small>
        </div>
    </section>

        
        <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel-heading">
                        <div class="panel-title"><h4>Date filter</h4></div>
                        <div class="panel-body">
                            <div class="row">
                                <form action="" method="GET">
                                    <div class=" col-lg-3" style="margin-bottom: 2px;">
                                        <input type="hidden" name="type" value="<?=$_GET['type']??'dhis'?>" >
                                        <div class="input-group">
                                            <span class="input-group-addon" ><b>From</b></span>
                                            <input type="date" class="form-control" value="<?=$date_from;?>" name="date_from">
                                        </div>
                                    </div>
                                    <div class=" col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" ><b>To</b></span>
                                            <input type="date" class="form-control" value="<?=$date_to;?>"  name="date_to">    
                                        </div>
                                        <input type="submit" style="visibility:hidden">
                                    </div>
                                    <div class=" col-lg-3"><input type="submit" class="btn btn-success" value="Go"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title"><h4>Visit by age <a role="button" class="btn btn-info" onclick="print_printable_document('printable_visit_by_age')"><span class="fa fa-print"></span></a></h4></div>
                    </div>
                    <div class="panel-body">
                        <div id="printable_visit_by_age">
                            <table class="table table-striped table-bordered" style="width:400px">
                                <thead>
                                    <tr>
                                        <th colspan="4"><u>Patient count by Age and Sex.</u><br>
                                        Report date <?= $date_from . (($date_from!=$date_to)?' - ' . $date_to: '') ?></th>
                                    </tr>
                                    <tr>
                                        <th>Age</th>
                                        <th>F</th>
                                        <th>M</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $m_total = 0;
                                        $f_total = 0;
                                        foreach($patient_count_report as $age_range => $row){
                                            if($age_range == "65-20000")
                                                $age_range = "> 65";
                                            echo '
                                    <tr>
                                        <td>'.$age_range.'</td><td>'.$row['f'].'</td><td>'.$row['m'].'</td><td>'.$row['t'].'</td>
                                    </tr>
                                            ';

                                            $m_total += $row['m'];
                                            $f_total += $row['f'];
                                        }
                                    ?>
                                    <tr style="font-weight: bold;">
                                        <td>TOTAL</td><td><?= $f_total ?></td><td><?= $m_total ?></td><td><?= $f_total + $m_total ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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
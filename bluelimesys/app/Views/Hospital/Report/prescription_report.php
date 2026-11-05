<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Admin</h1>
            <small>Prescription Report</small>
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
            <div class="col-lg-6">
                <div class="panel">
                  

                    <div class="panel-body">
                        <table class="table table-bordered table-striped" id="item_sales_report_table">
                            
                            <tbody>
                                <?php
                                    if($compounding_prescription != 0 || $prescriptions != 0 || $compounding_sales != 0 || $prescription_sales != 0){
                                ?>
                                <tr><td colspan="3"><h3>Internal Prescription</h3></td></tr>
                                <tr><td>Compounding Order</td><td><?=$compounding_prescription?></td><td></td></tr>
                                <tr style="background-color: #33fff6;color: #fff;"><td>Compounding Sales</td><td><?=$compounding_sales?></td><td><?=round(($compounding_sales*100)/$compounding_prescription,2)."%"?></td></tr>
                                <tr><td>Prescription Order</td><td><?=$prescriptions?></td><td></td></tr>
                                <tr style="background-color:#729e20;color: #fff;"><td>Prescription Sales</td><td><?=$prescription_sales?></td><td><?=round(($prescription_sales*100)/$prescriptions,2)."%"?></td></tr>
                                <tr><td>All Orders</td><td><?=$compounding_prescription+$prescriptions?></td><td></td></tr>
                                <tr style="background-color: #b533ff;color: #fff;"><td>All Sales</td><td><?=$prescription_sales+$compounding_sales?></td><td><?=round((($prescription_sales+$compounding_sales)*100)/($compounding_prescription+$prescriptions),2)."%"?></td></tr>
                                <tr><td colspan="3"><h3>External Sales</h3></td></tr>
                                <tr><td>WalkIn Compounding</td><td><?=$compounding_sales_external?></td><td></td></tr>
                                <tr><td>WalkIn Dispensary</td><td><?=$prescription_sales_external?></td><td></td></tr>
                           <?php
                                    }
                           ?>
                            </tbody>
                        </table>
                        
                    </div>
                </div>

            </div>
            <div class="col-lg-6">
                         <!-- Bar Chart -->
                        <div class="col-sm-12 col-md-12">
                            
                               
                                <div class="col-md-6">
                                    <canvas id="pieChart" class=""></canvas>
                                </div>
                            
                        </div>
                        
                    </div>
        </div>

        
    </section>
</div>
<script>
    
$(document).ready(function() {

var ctx = document.getElementById("pieChart");
var myChart = new Chart(ctx, {
    type: 'pie',
    animation: true,

    data: {
        labels: ["Unsold","Sold"],
        datasets: [{
                label: "<?php echo "Compounding";?>",
                data: [<?=$compounding_prescription-$compounding_sales?>,<?=$compounding_sales?>],
                borderWidth: "3",
                backgroundColor: [
                    "#C70039",
                    "#33fff6"
                ],

            },
            {
                label: "<?php echo "Prescription";?>",
                data: [<?=$prescriptions-$prescription_sales?>,<?=$prescription_sales?>],
                borderWidth: "3",
                backgroundColor: [
                     "#C70039",
                    "#729e20"
                ],

            },
            {
                label: "<?php echo "All Sales";?>",
                data: [<?=($compounding_prescription-$compounding_sales)+($prescriptions-$prescription_sales)?>,<?=$prescription_sales+$compounding_sales?>],
                borderWidth: "3",
                backgroundColor: [
                     "#C70039",
                    "#b533ff"
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
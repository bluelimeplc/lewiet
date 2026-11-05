<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo "Reception Dashboard";?></h1>
            <small><?php echo "Reception Dashboard";?></small>
            <ol class="breadcrumb">
            <a href="<?php echo base_url(); ?>hospital/reception/receptiondashboard"><h4 class="btn btn-primary">Dashboard </h4> <a href="<?php echo base_url(); ?>hospital/reception/doctorsreport"><h4 class="btn btn-primary">Doctors Report </h4></a>  <a href="<?php echo base_url(); ?>hospital/reception/dailypatients"><h4 class="btn btn-primary">Print Patient List</h4></a>
             </ol>
        </div>
    </section>


    <!-- Main content -->
    
    <section class="content">
       
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd lobidisable">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Today Sales</h4>
                        </div>
                    </div>
                    <div class="panel-body  table-responsive">
                        <table class="table table-striped table-hover table-responsive table-bordered">
                            <thead>
                                <tr><th>No.</th><th>Service Name</th><th>Quantity</th><th>Amount</th></tr>
                            </thead>
                            <?php 
                            $cnt = 0; 
                            $st = ''; 
                            $sn = '';
                            foreach($billDetail as $billPayement): $cnt++
                            ?>
                                    <?php
                                        if($billPayement['service_type'] != $st){
                                    ?>
                                    <tr><td colspan="4"><?=$billPayement['service_type']?></td></tr>
                                    <?php
                                        }
                                    ?>
                                <tr>
                                    <td><?=$cnt?></td>
                                    <td><?=$billPayement['service_name']?></td>
                                    <td><?php echo number_format($billPayement['bill_quantity'],2)?></td>
                                    <td><?php echo number_format($billPayement['bill_amount'],2)?></td>
                                </tr>
                            <?php 
                            $st =$billPayement['service_type'];
                            endforeach;
                            ?>
                       </table>
                    </div>
                </div>
            </div>

        </div>

    </section> <!-- /.content -->
</div> <!-- /.content-wrapper -->

<script>
    var paymentId = 0;
    function add_adjustment(class_name, payment_id){
        paymentId = payment_id;
        let content = $('.'+class_name);
        $('#adjustment_content').html('');
        for(i = 0; i < content.length; i++){
            let row = content[i];
            $('#adjustment_content').append(row);
        }
        let patient_name = content.children()[1].innerHTML;
        let company_name = content.children()[3].innerHTML;
        let doctor_name  = content.children()[5].innerHTML;
        let fs_no        = content.children()[6].innerHTML;
        let recieved_by  = '<?= $this->data['loggeduserdata']['name']; ?>';

        let adjustment_row = '<tr><td></td><td>'+ patient_name +'</td><td class="bg-danger">ADJUSTMENT</td>';
        adjustment_row    += '<td>'+ company_name + '</td>';
        adjustment_row    += '<td><input type="number" min="1" class="form-control" id="adjustment_value"/></td>';
        adjustment_row    += '<td>'+ doctor_name + '</td>';
        adjustment_row    += '<td>'+ fs_no + '</td>';
        adjustment_row    += '<td>'+ recieved_by + '</td>';
        adjustment_row    += '<td></td></tr>';
        
        $('#adjustment_data').html(adjustment_row);

        $('#add_adjustment').modal('show');
    }

    function save_adjustment(){
        let adjustment_value = $('#adjustment_value').val();
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/ajax/saveadjustment",
            data: {"payment_id":paymentId, "adjustment_value":-(adjustment_value)},
            success: function(result, textStatus, jqXHR){
                try{
                    if(result == '')
                        throw 'Error';
                    let r = JSON.parse(result);
                    if(r.status == 'success'){
                        location.reload();
                    }
                    else{
                        error_html = "Status: " + r.status + "<br>" + r.result;
                        dental_show_flash('addAdjustment_Flash', error_html, false);
                    }
                }
                catch (e){
                    error_html = "Invalid retun result.<br><u>Result:</u><br>"+result;
                    dental_show_flash('addAdjustment_Flash', error_html, false);
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                error_html = "Cannot add to database. <br><u>Error</u>:<br>" + errorThrown;
                dental_show_flash('addAdjustment_Flash', error_html, false);
            }
        });
    }
    function validateAdjustmentQuantity(){

        var adjustment_value = $("#adjustment_value").val();
        if(adjustment_value < 1){
        
            dental_show_flash("addAdjustment_Flash","Invalid Adjustment Value");
            return false;
        }
        save_adjustment();

    }

$(document).ready(function() {
    //bar chart
    var ctx = document.getElementById("barChart");
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Last 30 days", "Last 15 days", "Last 7 days", "Today"],
            datasets: [{
                    label: "<?php echo "Appointment Generated";?>",
                    data: [<?php echo 30;?>, <?php echo 15;?>, <?php echo 7;?>, <?php echo 10;?>],
                    borderColor: "#223F4A",
                    borderWidth: "0",
                    backgroundColor: "#223F4A"
                },
                {
                    label: "<?php echo "Appointment Missed";?>",
                    data: [<?php echo 30;?>, <?php echo 15;?>, <?php echo 7;?>, <?php echo 1;?>],
                    borderColor: "#57787D",
                    borderWidth: "0",
                    backgroundColor: "#57787D"
                }
            ]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

});
</script>
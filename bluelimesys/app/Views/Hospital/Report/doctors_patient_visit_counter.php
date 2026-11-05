<?php
    $loggeduserdata = $this->data['loggeduserdata'];
    $selected_doctor = $this->data['selected_doctor'];
    $doctors_list = $this->data['doctors_list'];
    $report_data = $this->data['report_data'];
    $procedure_list = $this->data['procedure_list'];
    $procedure_filter = $this->data['procedure_filter'];
    
    $selected_doctor_id = $selected_doctor['id'] ?? -1;

?>

<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Reports</h1>
            <small>Performance Report Detail</small>
            <ol class="breadcrumb">
                <?= view("Include/_admin_report_top_menu"); ?>
            </ol>
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidisable">
                    <div class="panel-body">
                        <div class="row g-3">
                            <form action="" id="form_list">
                                <div class="col-lg-4">
                                
                                    <label for="doctor_list">Select Doctor</label>
                                    <select name="doctor_id" id="doctor_list" class="form-control" onchange="select_doctor(this)">
                                        <option></option>
                                        <?php
                                            foreach($doctors_list as $doctor){
                                                echo '<option value="'.$doctor['id'].'" '.(($selected_doctor_id == $doctor['id']) ? 'selected' : '').'>'.$doctor['name'].'</option>';
                                            }
                                        ?>
                                    </select>
                                    <script>
                                        function select_doctor(obj){
                                            obj.querySelectorAll('input[type=checkbox]').forEach(el => el.checked = 0);
                                            obj.form.submit();
                                        }
                                    </script>

                                </div>
                                <div class="col-lg-3">
                                    <br>
                                    <b>Name: </b><?= $selected_doctor['name'] ?? '' ?> <br>
                                    <b>Number of patient: </b><?= count($report_data) ?><br>
                                </div>
                                <div class="col-lg-5">
                                    <div class="dental-form-box">
                                        <div class="box-title dental-bg-primary">
                                            <div style="padding: 5px;">
                                                <a style="color: white;" data-toggle="collapse" href="#collapseProcedures" role="button" aria-expanded="false" aria-controls="collapseProcedures" class="collapsed" onclick="service_list_up_down()">
                                                    Service list <i id="service_list_drop_icon" class="glyphicon glyphicon-chevron-down"></i>
                                                </a>
                                            </div>
                                            <script>
                                                function service_list_up_down(){
                                                    let service_list_drop_icon = $("#service_list_drop_icon");
                                                    service_list_drop_icon.toggleClass("glyphicon-chevron-up glyphicon-chevron-down");
                                                }
                                            </script>
                                        </div>    
                                        <div class="collapse" id="collapseProcedures" aria-expanded="false" style="height: 0px;">
                                            <div class="row">
                                                <?php
                                                    foreach($procedure_list as $service){
                                                        $selected = '';
                                                        foreach($procedure_filter as $service_id){
                                                            if($service['service_id'] == $service_id){
                                                                $selected = 'checked';
                                                                break;
                                                            }
                                                        }
                                                    
                                                        echo '<div class="col-sm-6"><input type="checkbox" name="procedures[]" value="'.$service['service_id'].'" id="procedures_'.$service['service_id'].'" '.$selected.' > <label for="procedures_'.$service['service_id'].'">'.$service['service_name'].'</label></div>';
                                                    }
                                                ?>
                                                <div class="col-sm-12"><button onclick="this.form.submit()" class="btn btn-primary">Filter</button></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="panel panel-bd lobidisable">
                    <div class="panel-body">
                        <table class="table table-bordered" id="report_table">
                            <thead>
                                <th></th><th>Patient name</th><th><i class="fa  fa-comment-o"></i></th><th>N<u>o</u> of visits</th><th>Total visits duration</th><th>Days after last visit</th> 
                            </thead>
                            <tbody>
                                <?php
                                    $cnt = 1;
                                    foreach($report_data as $row){
                                        echo '<tr>
                                            <td>'.$cnt++.'</td>
                                            <td><a class="fa fa-picture-o fa-2x"" href="/hospital/report/progresspicture?visit_id='.$row['visit_id'].'" target="_blank" style="text-align: right;"></a> &emsp;<a href="/hospital/doctor/patienthistory?visit_id='.$row['visit_id'].'" target="_blank">'.$row['patient_name'].'</a></td>
                                            <td>'.($row['has_message']?'&nbsp;  ':'&nbsp; ').'<a class="fa fa-comment'.($row['has_message']?'':'-o').'" role="button" onclick="show_message_modal(\''.$row['patient_id'].'\')"></a></td>
                                            <td>'.$row['no_of_visits'].'</td>
                                            <td>'.($row['visit_months'] < 10 ? '&nbsp;&nbsp;' : (($row['visit_months'] < 100 ? '&nbsp;' : ''))).$row['visit_months'].'m '.$row['visit_days'].'d</td>
                                            <td>'.($row['months_after_last_visit'] < 10 ? '&nbsp;&nbsp;' : (($row['months_after_last_visit'] < 100 ? '&nbsp;' : ''))).$row['months_after_last_visit'].'m '.$row['days_after_last_visit'].'d</td>
                                        </tr>
                                        ';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>

    $(document).ready(function() {
    var t = $('#report_table').DataTable( {
        "columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": 0
        } ],
        "order": [[ 0, 'asc' ]]
    } );
                 
    t.on( 'draw.dt', function () {
    var PageInfo = $('#report_table').DataTable().page.info();
         t.column(0, { page: 'current' }).nodes().each( function (cell, i) {
            cell.innerHTML = i + 1 + PageInfo.start;
        } );
    } );
} );
</script>
<?= view('Include/_message'); ?>
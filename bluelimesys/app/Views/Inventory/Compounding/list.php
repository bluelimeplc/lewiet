<?php 
   $compounding_prescriptions = $this->data['compounding_prescriptions'];
?>

<?php
    echo view('Include/_header')
        .view('Include/_left_sidebar_top')
        .view('Inventory/Dispensary/_left_sidebar_dispensary')
        .view('Include/_left_sidebar_bottom');
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Compounding</h1>
            <small>List</small>
            <ol class="breadcrumb">
                <li role="button">
                    <a href="/inventory/compounding/external_order">External order</a>
                </li>
            </ol>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title">

                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="20"></th><th>MRN</th><th>Patient Name</th><th>Mobile</th><th>Doctor</th><th>Date</th><th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sn = 1;
                                    foreach($compounding_prescriptions as $row){
                                        $patient_id = $row['patient_detail']['id'];
                                        $patient_name = $row['patient_detail']['name'];
                                        $patient_mobile = $row['patient_detail']['mobile'];
                                        $created_by_name = $row['created_by_name'];

                                        echo '
                                <tr>
                                    <td>'.$sn++.'</td><td>'.sprintf('%05d',$patient_id).'</td>
                                    <td>'.$patient_name.'</td>
                                    <td>'.$patient_mobile.'</td>
                                    <td>'.$created_by_name.'</td>
                                    <td>'.substr($row['created_at'],0,10).'</td>
                                    <td><a href="/inventory/compounding/prepare?id='.$row['id'].'" class="fa fa-eye"></a></td>
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

<?php
    echo view('Include/_footer');
?>
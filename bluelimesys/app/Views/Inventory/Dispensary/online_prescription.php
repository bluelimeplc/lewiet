<link rel="stylesheet" href="/Image-gallery/assets/css/jquery.lightbox.css">
<link rel="stylesheet" href="/Image-gallery/assets/css/demo.css">
<script src="/Image-gallery/assets/js/jquery.lightbox.js"></script>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Dispensary</h1>
            <small>Online Prescription</small>
            <ol class="breadcrumb">
                <li class="active" role="button"><a role="button" href="/inventory/dispensary">Sales</a></li>
            </ol>
        </div>
    </section>
    <style>
        .list-group-item .badge{
            font-size:unset;
        }
        .list-group-item{
            margin-bottom: 10px !important;
            cursor: pointer;
        }
    </style>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-bd panel-form">
                    <div class="panel-heading">Online Prescription list</div>
                    <div class="panel-body">
                        <div class="list-group">
                                <table class="table table-striped">
                                    <tr>
                                        <td>Name</td>
                                        <td>Mobile</td>
                                        <td>From</td>
                                        <td>Doctor</td>
                                        <td>Date</td>
                                        <td></td>
                                    </tr>
                            <?php
                                foreach($op as $prescription){
                            ?>
                                <tr>
                                    <td><?=$prescription['patient_name']?></td>
                                    <td><?=$prescription['phone_number']?></td>
                                    <td><?=$prescription['branch_from']?></td>
                                    <td><?=$prescription['doctor_name']?></td>
                                    <td><?=$prescription['date_created']?></td>
                                    <td>
                                    <img src="data:image/jpeg;base64,<?=base64_encode(  $prescription['image'] ?? '' )?>" style="width: 50px; cursor: pointer;" onclick="window.open('get_online_prescription?id='+<?=$prescription['id']?>, '_blank', 'toolbar=no,scrollbars=no,resizable=no,top=100,left=300,width=600,height=800')">
                                    </td>
                                </tr>
                            <?php
                                }
                            ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </section>
</div>
<script>
        function preview() {
        frame.src = URL.createObjectURL(event.target.files[0]);
    }
    
</script>
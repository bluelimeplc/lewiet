<?php

$pending_bills = $this->data['pending_bills'] ?? [];

?>
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo lang(line: 'Admin'); ?></h1>
            <small><?php echo lang('Deletion Requested Pending Bills'); ?></small>
        </div>
    </section>
    <section class="content">
        <div class="col-sm-12">
            <div  class="panel panel-default">
                <div class="panel-body">
                    <table width="100%" class="table table-striped table-bordered table-hover" id="patient_list4">
                        <thead>
                            <tr>                     
                                <th>MRN No</th>
                                <th>Full Name</th>
                                <th>Company</th>
                                <th>Date</th>
                                <th>Action</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($pending_bills as $pending_bill) :
                            ?>
                            <tr>
                                    <td><?php echo "MRN-".sprintf('%05d', $pending_bill['patient_id']); ?></td>
                                    <td><?php echo  $pending_bill['patient_name'] ?> </td>
                                    <td><?php echo  $pending_bill['company_name'] ?> </td>
                                    <td><?php echo  $pending_bill['billed_date']  ?> </td>
                                    <td><a href="/hospital/admin/pendingbilldetail?id=<?= $pending_bill['id']?>" class="btn btn-info"><i class="fa fa-eye"></i></a></td>
                            </tr>
                            <?php
                            endforeach
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
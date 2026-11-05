<?php
    $follow_ups = $this->data['follow_ups'];
   
    $search_plan = $this->data['search_plan'] ?? '';
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>

        <div class="header-title">
            <h1>Reception</h1>
            <small>Treatment Plans</small>
        </div>
    </section>
    <section class="content">
           
    <div class="row">      
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-bd">
                <div class="panel-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <div class="col-sm-6">
                                <input id="search_patient" type="text" name="search_plan" class="form-control" value="<?= $search_plan ?>">
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div  class="panel panel-default">
                <div class="panel-body  table-responsive">
                    <table width="100%" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>MRN N<u>o</u></th><th>Full Name</th><th>Company</th><th>Date</th><th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach($follow_ups as $follow_up){
                            ?>
                            <tr>
                                <td><?=$follow_up['patient_id']?></td>
                                <td><?=$follow_up['patient_name']?></td>
                                <td><?=$follow_up['company_name']?></td>
                                <td><?=$follow_up['date_created']?></td>
                                <td><a class="btn btn-<?php echo (@$follow_up['_notify']['status']==1 or !isset($follow_up['_notify']['status']))?"info":"warning";?> btn-sm" href="/hospital/reception/treatmentplanchoice?follow_up_id=<?=$follow_up['id']?>" role="button"><i class="fa fa-eye"></i></a></td>
                            </tr>
                            <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
    


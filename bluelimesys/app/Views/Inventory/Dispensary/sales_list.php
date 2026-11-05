<?php
    $sales_main = $this->data['sales_main'];
?>

<link href="/css/autocomplete.css" rel="stylesheet" type="text/css"/>

<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Pharmacy</h1>
            <small>Sales List</small>
            
        </div>
    </section>

<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div  class="panel">
                    <div class="panel-heading">
                        <div class="panel-title" >
                            <h4></h4>
                        </div>
                    </div>
                    <div class="panel-body table-responsive">
                        <div>
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <input type="text" value="<?= $search_key ?>" name="search_key" placeholder="Customer Name" class="form-control" id="search_key">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <a class=" input-group-addon btn btn-primary" role="button" onclick="filter_by_date('before')">Before</a>
                                            <input type="date" class="form-control" value="<?= $filter_date ?>" name="filter_date" id="filter_date">
                                            <a class=" input-group-addon btn btn-primary" role="button" onclick="filter_by_date('after')">After</a>
                                        </div>  
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div style="text-align: right;"><?= $pager->links() ?></div>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>SN</th><th>Sales Code</th><th>Customer Name</th><th>Created Date</th></th><th>Status</th><th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sn = 0;
                                    foreach($sales_main as $sales)
                                    {
                                        $sn++;
                                        $status = '';
                                        if(trim($sales['fs_number']) != '')
                                            $status = '<i class="fa fa-money" title="Paid"></i> '.$sales['fs_number'];
                                        elseif($sales['sent_to_pos'] == true)
                                            $status = 'POS';
                                            
                                        echo '
                                <tr>
                                    <td>'.$sn.'</td>
                                    
                                    <td>' .(intval($sales['pos_bill_id'])!=0?('GLPH-'.sprintf('%06d', $sales['pos_bill_id'])):'').($sales['sales_type']==BluelimeConfiguration::SALES_TYPE::COMPOUNDING?' <span class="badge">C</span>':'').'</td>
                                    <td>'.$sales['patient_name'].'</td>
                                    <td>'.$sales['created_at'].'</td>
                                    <td>'.$status.'</td>
                                    <td><a href="/inventory/dispensary/sales_print?sales_id='.$sales['id'].'" class="btn btn-primary btn-xs"> <i class="fa fa-eye"></i></a> </td>
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
    function filter_by_date(position){
        let search_key = $('#search_key').val();
        let filter_date = $('#filter_date').val();

        document.location = '?search_key=' + search_key + '&filter_date=' + filter_date + '&position=' + position;
    }
</script>
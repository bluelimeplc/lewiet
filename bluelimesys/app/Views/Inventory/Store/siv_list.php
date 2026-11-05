<?php
    $siv_main = $this->data['siv_main'];
    $search_key = $this->data['search_key'];
?>

<link href="/css/autocomplete.css" rel="stylesheet" type="text/css"/>

<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Store</h1>
            <small>Store Issue List</small>
            <ol class="breadcrumb">
                <li class="active"><a href="/inventory/store/">Request List</a></li>
            </ol>
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
                    <div class="panel-body">
                        <div>
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <input type="text" value="<?= $search_key ?>" name="search_key" placeholder="SIV Code" class="form-control" id="search_key">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <a class=" input-group-addon btn btn-primary" role="button" onclick="filter_by_date('before')">Before</a>
                                            <input type="date" class="form-control" value="<?= $filter_date ?>" id="filter_date">
                                            <a class=" input-group-addon btn btn-primary" role="button" onclick="filter_by_date('after')">After</a>
                                        </div>  
                                    </div>
                                </div>
                            </form>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SN</th><th>SIV Code</th><th>SRV Code</th><th>Issue Date</th><th>Requesting Store</th><th>Description</th><th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sn = ($page-1) * BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST;
                                    foreach($siv_main as $siv)
                                    {
                                        $sn++;
                                        echo '
                                <tr>
                                    <td>'.$sn.'</td>
                                    <td>SIV-'.sprintf('%04d',$siv['id']).'</td>
                                    <td>SRV-'.sprintf('%04d',$siv['srv_id']).'</td>
                                    <td>'.$siv['created_at'].'</td>
                                    <td>'.$siv['store_name'].'</td>
                                    <td>'.$siv['description'].'</td>
                                    <td><a href="/inventory/store/siv_print?siv_id='.$siv['id'].'" class="btn btn-primary btn-xs"> <i class="fa fa-eye"></i></a></td>
                                </tr>
                                        ';
                                    }
                                ?>

                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        <?= $pager->links() ?>
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
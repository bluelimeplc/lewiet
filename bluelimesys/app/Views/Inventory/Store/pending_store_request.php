<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo lang('Store');?></h1>
            <small><?php echo lang('Store Requests');?></small>
            <ol class="breadcrumb">
                <li class="active"><a href="/inventory/store/siv_list">Issue List</a></li>
            </ol>
        </div>
    </section>

    <section class="content">

        <div class="row">


            <!-- Main content -->


            <div class="col-md-12">

                <div class="panel panel-bd">


                    <div class="panel-body">
                        <div>
                            <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <input type="text" value="<?= $search_key ?>" name="search_key" placeholder="SRV Code" class="form-control" id="search_key">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <a class=" input-group-addon btn btn-primary" role="button" onclick="filter_by_date('before')">Before</a>
                                            <input type="date" class="form-control" value="<?= $filter_date ?>"  id="filter_date">
                                            <a class=" input-group-addon btn btn-primary" role="button" onclick="filter_by_date('after')">After</a>
                                        </div>  
                                    </div>
                                </div>
                            </form>
                        </div>
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="10"></th>
                                    <th class="all"><?php echo "SRV Code"; ?></th>
                                    <th class="all"><?php echo "Request Date"; ?></th>
                                    <th class="all"><?php echo "Request By"; ?></th>
                                    <th class="all"><?php echo "Request From"; ?></th>
                                    <th class="all"><?php echo "Request To"; ?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                        $sn = ($page-1) * BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST;
                                        foreach($srv_main as $srv)
                                        {
                                            $sn++;
                                            echo '
                                                <tr>
                                                    <td>'.$sn.'</td>
                                                    <td>'.sprintf('%05d',$srv['id']).'</td>
                                                    <td>'.date('Y-m-d', strtotime($srv['request_date'])).'</td>
                                                    <td>'.$srv['requested_by__name'].'</td>
                                                    <td>'.$srv['requesting_store'].'</td>
                                                    <td>'.$srv['requested_store'].'</td>
                                                    <td> 
                                                        <a href="/inventory/store/srv_print?srv_id='.$srv['id'].'" class="btn btn-primary btn-xs"> <i class="fa fa-eye"></i></a> 
                                                        <a href="/inventory/store/siv_form?srv_id='.$srv['id'].'" class="btn btn-warning btn-xs"> <i class="fa fa-edit"></i></a> 
                                                    </td>
                                                </tr>
                                            ';
                                        }
                                ?>

                            </tbody>

                        </table>

                        </ul>


                    </div>
                    <div class="panel-footer">
                        <?= $pager->links() ?>
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
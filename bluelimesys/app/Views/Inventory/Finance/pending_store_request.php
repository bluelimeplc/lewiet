<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo lang('Finance');?></h1>
            <small><?php echo lang('Store Requests');?></small>
            
        </div>
    </section>
    
    <section class="content">
     
        <div class="row">


            <!-- Main content -->


            <div class="col-md-12">

                <div class="panel panel-bd">
                    
                        
                    <div class="panel-body   table-responsive">
                    <?php
                            $attributes = array('role'=>'form');
                            helper('form');
                            echo form_open_multipart('/hospital/finance/labrequestlist', $attributes);
                        ?>
                        <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <div class="col-sm-6">
                                <input id="search_storerequest" type="text" name="search_storerequest"placeholder="Search Store requests"
                                    class="form-control">
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-success">Search <i
                                        class="fa fa-search"></i></button>
                            </div>

                        </div>
                    </form>
                    <?php
                        form_close();
                    ?>
                    <br>
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="all"><?php echo "SRV Code"; ?></th>
                                    <th class="all"><?php echo "Request Date"; ?></th>
                                    <th class="all"><?php echo "Request By"; ?></th>
                                    <th class="all"><?php echo "Request From"; ?></th>
                                </tr>
                            </thead>

                            <tbody>
                             
                            </tbody>
                            
                        </table>
                        
                    </ul>  
                            
                        
                    </div>
                    
                    
                            
                </div>
            </div>
      


    </section>
</div>
</div>

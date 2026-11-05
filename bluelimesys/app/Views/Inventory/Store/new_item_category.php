<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Store</h1>
                <small>New Category</small>
           
        </div>
    </section>

<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-5 col-md-5">
                <div  class="panel panel-bd panel-form">
                <div class="panel-heading">
                    <div class="panel-title" >
                        <h4>Add New Category</h4>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="portlet-body form">
                        <div class="row">    
                            <div class="col-sm-12">
                        <?php 
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','method'=>'post',  'enctype' => 'multipart/form-data');
                            echo form_open_multipart('inventory/store/save_item_category', $attributes);                
                         ?>
                        
                            <div class="form-body">
                                
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Name:</label>
                                    <div class="col-md-11">
                                        <input type="text" name="category_name" class="form-control" required value="<?=@$category[0]['category_name']?>" placeholder="Category Name"> 
                                        <input type="hidden" name="id" value="<?=@$category[0]['id']?>"> 
                                        <input type="hidden" name="edit" value="<?=@$_GET['edit']?>"> 
                                    </div>
                                </div>
                               
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Description:</label>
                                    <div class="col-md-11">
                                        <input type="text" name="description" class="form-control" required value="<?=@$category[0]['description']?>" placeholder="Description"> 
                                        
                                    </div>
                                </div>                               
                                

                            </div>

                            <div class="form-group row" >
                                <div class="col-sm-offset-3 col-sm-9" style="text-align: right;">
                                    <button type="submit" class="btn btn-success">Save</button>
                                </div>
                            </div>
                        <?php echo form_close();?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <div class="col-sm-7 col-md-7">
                <div  class="panel panel-bd panel-form">
                <div class="panel-heading">
                    <div class="panel-title" >
                        <h4>Category List</h4>
                    </div>
                </div>
                <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <div class="row">&nbsp;</div>
                                    <div class="col-sm-6">
                                        <input id="search_patient" type="text" placeholder="<?php echo  "Search Category" ?>" name="search_patient" class="form-control" value="<?= @$searchkey ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                    <table class="table table-striped">
                        <tr style="font-weight: bold;">
                            <td>Category Name</td>
                            <td>Description</td>
                            
                        </tr>
                        <?php 
                            foreach($categories as $category):
                        ?>
                        <tr>
                            
                            <td><?=$category['category_name']?></td>
                            <td><?=$category['description']?></td>
                           
                            <td><a href="<?php echo base_url();?>inventory/store/new_item_category?cid=<?=$category['id']?>&edit=<?=1?>">
                                    <button type="button" class="fa fa-edit btn-primary" aria-hidden="true">
                                    </button>
                                </a>

                            </td>
                        </tr>
                        <?php 
                           endforeach;
                        ?>
                    </table>

                <div class="panel-body">
                    <div class="portlet-body form">
                        <div class="row">    
                            <div class="col-sm-12">

                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>

        </div>
            
    </section>
  
            
    

</div>
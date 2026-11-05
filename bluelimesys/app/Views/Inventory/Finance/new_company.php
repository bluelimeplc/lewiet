<?php
$companies = $this->data['companies'];
$company = $this->data['company'];
?>
<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
                <small>New Company</small>
            <ol class="breadcrumb">
                <li class="active"><a href="/inventory/finance/">Dashboard</a></li>
            </ol>
        </div>
    </section>

<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-6 col-md-6">
                <div  class="panel panel-bd panel-form">
                <div class="panel-heading">
                    <div class="panel-title" >
                        <h4>Add New Service</h4>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="portlet-body form">
                        <div class="row">    
                            <div class="col-sm-12">
                        <?php 
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','method'=>'post',  'enctype' => 'multipart/form-data');
                            echo form_open_multipart('inventory/finance/addcompany', $attributes);                
                         ?>
                        
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Company Name:</label>
                                    <div class="col-md-11">
                                        <input type="text" name="company_name" class="form-control" required value="<?=@$company[0]['company_name']?>" placeholder="Company Name"> 
                                        <input type="hidden" name="id" value="<?=@$company[0]['id']?>"> 
                                        <input type="hidden" name="edit" value="<?=@$_GET['edit']?>"> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Tin Number:</label>
                                    <div class="col-md-11">
                                        <input type="text" name="tin_number" class="form-control"  value="<?=@$company[0]['tin_number']?>" placeholder="Tin Number"> 
                                        
                                    </div>
                                </div>   

                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Description:</label>
                                    <div class="col-md-11">
                                        <input type="text" name="description" class="form-control"  value="<?=@$company[0]['description']?>" placeholder="Description"> 
                                        
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Unpaid Visit Days:</label>
                                    <div class="col-md-11">
                                        <input type="number" name="unpaid_visit_days" min="1" class="form-control"  value="<?=@$company[0]['unpaid_visit_days']?>" placeholder="Unpaid Visit Days"> 
                                        
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Status:</label>
                                    <div class="col-md-11">
                                    <select name="status" class="form-control " required value="" > 
                                            <option value="1" <?=@$company[0]['is_active']==1?"selected":""?>>Active</option>
                                            <option value="0" <?=@$company[0]['is_active']==0?"selected":""?>>Inactive</option>
                                    </select>
                                </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Payment Type:</label>
                                    <div class="col-md-11">
                                    <select name="is_cash" class="form-control " required value="" > 
                                            <option value="1" <?=@$company[0]['is_cash']==1?"selected":""?>>Cash</option>
                                            <option value="0" <?=@$company[0]['is_cash']==0?"selected":""?>>Credit</option>
                                    </select>
                                </div>
                                </div>

                    
                            </div>

                            <div class="form-group row" >
                                <div class="col-sm-offset-3 col-sm-9" style="text-align: right;">
                                     <button type="reset" class="btn btn-danger">Reset</button>
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        <?php echo form_close();?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <div class="col-sm-6 col-md-6">
                <div  class="panel panel-bd panel-form">
                <div class="panel-heading">
                    <div class="panel-title" >
                        <h4>Company List</h4>
                    </div>
                </div>
                    <table class="table table-striped">
                        <tr style="font-weight: bold;">
                            <td>Company Name</td>
                            <td>Tin Number</td>
                            <td>Description</td>
                            <td>Unpaid Visit Days</td>
                            <td>Status</td>
                        </tr>
                        <?php 
                            foreach($companies as $company):
                        ?>
                        <tr>
                            <td><?=$company['company_name']?></td>
                            <td><?=$company['tin_number']?></td>
                            <td><?=$company['description']?></td>
                            <td><?=$company['unpaid_visit_days']?></td>
                            <td><a href="changecompanystatus?cid=<?=$company['id']?>&st=<?=$company['is_active']==0?1:0?>">
                                    <?php 
                                        if($company['is_active']==1){
                                    ?>
                                        <i class="glyphicon  glyphicon-ok color-green"></i>
                                    <?php
                                        }
                                        else{
                                    ?>
                                        <i class="glyphicon  glyphicon-remove color-red"></i>
                                    <?php
                                        }
                                    ?>
                                </a>
                            </td>
                            <td><a href="newcompany?cid=<?=$company['id']?>&edit=<?=1?>">
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
<?php
    $pricelist = $this->data['pricelist'];
    $price = $this->data['price'];
    $companies = $this->data['companies'];
    $services = $this->data['services'];
    $pricelist_company_name = $this->data['pricelist_company_name'];
    $pricelist_detail = $this->data['pricelist_detail'];
    $loggeduserdata = $this->data['loggeduserdata'];
   // \BluelimeSystem::show_and_die($pricelist_detail)
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Finance</h1>
                <small>Price Setup</small>
            <ol class="breadcrumb">
                <li class="active"><a href="/inventory/finance/">Dashboard</a></li>
            </ol>
        </div>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
    <div class="col-sm-12 col-md-12">
                <div  class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title" >
                            <h4>New Price Main</h4>
                        </div>
                    </div>

                    <div class="panel-body">
                    <div class="portlet-body form">
                        <div class="row">    
                            <div class="col-sm-12">
                        <?php 
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','method'=>'post',  'enctype' => 'multipart/form-data');
                            echo form_open_multipart('inventory/finance/addpricemain', $attributes);                
                         ?>
                        
                            <div class="form-body">
                                <div class="form-group" class="col-md-1">
                                    <label class="control-label col-md-1" style="text-align: left;"> Company:</label>
                                    <div class="col-md-3">
                                        <select class="form-control " name="company_id" required>
                                        <option value="">--</option>
                                            <?php foreach($companies as $company): ?>
                                                <option value="<?=$company['id']?>"><?=$company['company_name']?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <label class="control-label col-md-1" style="text-align: left;"> Description:</label>
                                    <div class="col-md-3">
                                        <input type="text" name="description" class="form-control" required value="" placeholder="Description"> 
                                        
                                    </div>
                                    <label class="control-label col-md-1" style="text-align: left;"> Description:</label>
                                    <div class="col-md-2">
                                        <select type="text" name="status" class="form-control" required> 
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>

                                    <div style="text-align: right;" class="col-md-1">
                                        <button type="submit" class="btn btn-success">Save</button>
                                    </div>
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
                            <h4>Price Main</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="portlet-body form">
                            <div class="row">    
                                <div class="col-sm-12">
                                    <table class="table table-hover">
                                        <tr style="font-weight:bold">
                                            <td>Company</td><td>Description</td><td>Status</td><td></td>
                                        </tr>
                                        <?php foreach($pricelist as $list): ?>
                                        <tr>
                                            <td><?=$list['company_name']?></td> 
                                            <td><?=$list['description']?></td>  
                                            <td><a href="changepricestatus?plid=<?=$list['id']?>&st=<?=$list['status']==0?1:0?>">
                                                    <?php 
                                                        if($list['status']==1){
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
                                            <td><a href="pricesetup?plid=<?=$list['id']?>" <?=($list['status']==1)?"":"hidden"?> >
                                                <button class="btn btn-primary fa fa-arrow-right"></button></a>
                                            </td>  
                                        </tr>
                                        <?php endforeach ?>
                                    </table>
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
                            <?php 
                                    helper('form');
                                    $attributes = array('class' => 'form-horizontal','method'=>'post',  'enctype' => 'multipart/form-data');
                                    echo form_open_multipart('inventory/finance/addpricedetail', $attributes);                
                                ?>
                           <div> <h4>Price Detail</h4></div>
                                <div style="text-align: right;">
                                     <div class="btn btn-danger" style="pointer-events: none;"><?=(@$pricelist_company_name[0] != "")?$pricelist_company_name[0]: "Default Price"?></div><button type="submit" class="btn btn-success">Update</button>
                                </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="portlet-body form">
                            <div class="row">    
                                <div class="col-sm-12">
                                
                                    <table class="table table-hover">
                                        <tr style="font-weight:bold">
                                            <td>No.</td><td>Service Name</td><td>Price</td>
                                        </tr>
                                        <?php 
                                            $i = 1;
                                            foreach($services as $service): 
                                        ?>
                                        <tr>
                                            <td><?=$i?></td>
                                            <td><?=$service['service_name']?></td>
                                            <td>
                                                <input name="service_price[]" style="text-align: right;" class="form-control" type="text" value="<?=@intval($service['_detail']['service_price'])?>">
                                                <input name="status[]"  type="hidden" value="<?=@intval($service['_detail']['status'])?>">
                                                <input name="service_id[]" type="hidden" value="<?=intval(@$service['id'])?>">
                                                <input name="service_name[]" type="hidden" value="<?=$service['service_name']?>">
                                                <input name="service_type[]" type="hidden" value="<?=$service['service_type']?>">
                                                <input name="pricelist_id[]" type="hidden" value="<?=@$_GET['plid']?>">
                                                
                                            </td> 
                                            <td>
                                                <a href="changepricedetailstatus?st=<?=@intval($service['_detail']['status']==0?1:0)?>&pldid=<?=@intval($service['_detail']['id'])?>&plid=<?=@$_GET['plid']?>">
                                                    <?php 
                                                        if(@intval($service['_detail']['status']==1)){
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
                                            
                                        </tr>
                                        <?php 
                                            $i++;
                                            endforeach 
                                        ?>
                                    </table>
                                <?php
                                echo form_close();
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
    </div>
        
    </section>
</div>
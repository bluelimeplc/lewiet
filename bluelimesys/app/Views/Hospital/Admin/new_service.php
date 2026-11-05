<?php
$services = $this->data['services'];
$service = $this->data['service'];
?>
<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Administrator</h1>
                <small>New Service</small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/admin/">Dashboard</a></li>
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
                        <h4><?= (($_GET['edit']??false)==1)? 'Edit Service' :'Add New Service' ?></h4>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="portlet-body form">
                        <div class="row">    
                            <div class="col-sm-12">
                        <?php 
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','method'=>'post',  'enctype' => 'multipart/form-data');
                            echo form_open_multipart('hospital/admin/addservice', $attributes);                
                         ?>
                        
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Service Name:</label>
                                    <div class="col-md-11">
                                        <input type="text" name="service_name" class="form-control" required value="<?=@$service[0]['service_name']?>" placeholder="Service Name"> 
                                        <input type="hidden" name="id" value="<?=@$service[0]['id']?>"> 
                                        <input type="hidden" name="edit" value="<?=@$_GET['edit']?>"> 
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Type:</label>
                                    <div class="col-md-11">
                                        <select name="service_type" class="form-control" required value="" > 
                                            <option value="">--</option>
                                            <option value="Procedure" <?=@$service[0]['service_type']=='Procedure'?"selected":""?>>Procedure</option>
                                            <option value="Procedure (Nurse)" <?=@$service[0]['service_code']=='NURSE'?"selected":""?>>Procedure (Nurse)</option>
                                            <option value="Consultation" <?=@$service[0]['service_type']=='Consultation'?"selected":""?>>Consultation</option>
                                            <option value="Laboratory" <?=@$service[0]['service_type']=='Laboratory'?"selected":""?>>Laboratory</option>
                                            <option value="Pathology" <?=@$service[0]['service_type']=='Pathology'?"selected":""?>>Pathology</option>
                                            <option value="Radiology" <?=@$service[0]['service_type']=='Radiology'?"selected":""?>>Radiology</option>
                                        </select>
                                        
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Measurement:</label>
                                    <div class="col-md-11">
                                    <select name="measurement" class="form-control " required value="<?=@$service[0]['company_name']?>" > 
                                            <option value=""<?=@$service[0]['measurement']==''?"selected":""?>>--</option>
                                            <option value="each"<?=@$service[0]['measurement']=='each'?"selected":""?>>Each</option>
                                    </select>
                                </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> GL Account:</label>
                                    <div class="col-md-11">
                                        <input type="text" name="gl_account" class="form-control" required value="<?=@$service[0]['gl_account']?>" placeholder="GL Account">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Status:</label>
                                    <div class="col-md-11">
                                    <select name="status" class="form-control " required value="" > 
                                            <option value="1"<?=@$service[0]['is_active']==1?"selected":""?>>Active</option>
                                            <option value="0"<?=@$service[0]['is_active']==0?"selected":""?>>Inactive</option>
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
                        <h4>Service List</h4>
                    </div>
                </div>
                    <table class="table table-striped">
                        <tr style="font-weight: bold;">
                            <td>Service Name</td>
                            <td>Type</td>
                            <td>Measurment</td>
                            <td>Status</td>
                            <td></td>
                        </tr>
                        <?php 
                            foreach($services as $service):
                        ?>
                        <tr>
                            <td><?=$service['service_name']?></td>
                            <td><?=$service['service_type']?></td>
                            <td><?=$service['measurement']?></td>
                            <td><a href="changeservicestatus?sid=<?=$service['id']?>&st=<?=$service['is_active']==0?1:0?>">
                                    <?php 
                                        if($service['is_active']==1){
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
                            <td><a href="newservice?sid=<?=$service['id']?>&edit=<?=1?>">
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
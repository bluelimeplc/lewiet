<?php
    $suppliers = $this->data['suppliers'];
    $supplier = $this->data['supplier'];
?>
<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Store</h1>
                <small>New Supplier</small>
            
        </div>
    </section>

<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-6 col-md-6">
                <div  class="panel panel-bd panel-form">
                <div class="panel-heading">
                    <div class="panel-title" >
                        <h4>Add New Supplier</h4>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="portlet-body form">
                        <div class="row">    
                            <div class="col-sm-12">
                        <?php 
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','method'=>'post',  'enctype' => 'multipart/form-data');
                            echo form_open_multipart('inventory/store/save_supplier', $attributes);                
                         ?>
                        
                        <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Supplier Name:</label>
                                    <div class="col-md-11">
                                        <input type="text" name="company_name" class="form-control" required value="<?= $supplier['company_name'] ?? '' ?>" placeholder="Supplier Name"> 
                                        <input type="hidden" name="id" value="<?= $supplier['id'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Mobile:</label>
                                    <div class="col-md-11">
                                        <input  name="mobile" class="form-control"  value="<?= $supplier['address']['mobile'] ?? '' ?>"  type="text"  placeholder="09xxxxxxxx" > 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Phone:</label>
                                    <div class="col-md-11">
                                        <input  name="phone" class="form-control"  value="<?= $supplier['address']['phone'] ?? '' ?>" type="text"> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Email:</label>
                                    <div class="col-md-11">
                                        <input  name="email" class="form-control"  value="<?= $supplier['address']['email'] ?? ''?>"  type="text"> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Web:</label>
                                    <div class="col-md-11">
                                        <input  name="web" class="form-control"  value="<?= $supplier['address']['web'] ?? ''?>"  type="text"> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Tin Number:</label>
                                    <div class="col-md-11">
                                        <input type="text" name="tin_number" class="form-control"  value="<?= $supplier['tin_number'] ?? '' ?>" placeholder="Tin Number" required minlength="10" onkeypress="return /[0-9]/i.test(event.key)" > 
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
                        <h4>Supplier List</h4>
                    </div>
                </div>
                    <table class="table table-striped">
                        <tr style="font-weight: bold;">
                            <td>Supplier Name</td>
                            <td>Address</td>
                            <td>Tin Number</td>
                            
                            
                        </tr>
                        <?php 
                            foreach($suppliers as $row):
                        ?>
                        <tr>
                            <td><?= $row['company_name'] ?></td>
                            <td>
                                <?php 
                                    foreach($row['address'] as $address_name => $value) { 
                                        if($value != '')
                                            echo ucwords($address_name) . ": " . $value . "<br>";
                                    } 
                                ?>
                            </td>
                            <td><?= $row['tin_number'] ?></td>
                            
                            
                            <td><a href="new_supplier?supplier_id=<?=$row['id']?>">
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
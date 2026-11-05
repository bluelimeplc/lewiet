<?php
//\BluelimeSystem::show_and_die($attributes);
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Administrator</h1>
                <small>Procedure Setup</small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/admin/">Dashboard</a></li>
            </ol>
        </div>
    </section>
    <!-- Main content -->
    <section class="content">
    <div class="row">
    
            <div class="col-sm-4 col-md-4">
                <div  class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title" >
                            <h4>Nurse Procedures</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="portlet-body form">
                            <div class="row">    
                                <div class="col-sm-12">
                                    <table class="table table-hover">
                                        <tr style="font-weight:bold">
                                            <td>Procedure Name</td><td>Status</td><td></td>
                                        </tr>
                                        <?php foreach($procedures as $procedure): ?>
                                        <tr>
                                            <td><?=$procedure['service_name']?></td>   
                                            <td>
                                                    <?php 
                                                        if($procedure['is_active']==1){
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
                                            <td><a href="proceduresetup?pname=<?=$procedure['service_name']?>&pid=<?=$procedure['id']?>" <?=($procedure['is_active']==1)?"":"hidden"?> >
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
            <div class="col-sm-8 col-md-8"<?=@$_GET['pid']?"":"hidden"?>>
                <div  class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title" >
                            <?php 
                                    helper('form');
                                    $attributes = array('class' => 'form-horizontal','method'=>'post',  'enctype' => 'multipart/form-data');
                                    echo form_open_multipart('hospital/admin/addProcedureAttribute', $attributes);                
                            ?>
                            <div> <h4>Attributes</h4></div>
                                <div style="text-align: right;">
                                     <div class="btn btn-primary" style="pointer-events: none;"><?= @$_GET['pname'] ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="portlet-body form">
                            <div class="row">    
                                <div class="col-sm-12">
                                
                                    <table class="table table-hover">
                                        <tr style="font-weight:bold">
                                            <td>No.</td><td>Attribute Name</td><td>Result Type</td><td>Is Required</td>
                                        </tr>
                                        <input class="form-control" name="procedure_name" type="hidden" value="<?=@$_GET['pname'] ?>"  >
                                        <input class="form-control" name="procedure_id" type="hidden" value="<?=@$_GET['pid'] ?>" >
                                        <tr>
                                            <td></td>
                                            <td><input class="form-control" name="attribute_name" type="text" required ></td> 
                                            <td>
                                                <select name="input_type" class="form-control">
                                                    <option value="text">Text</option>
                                                    <option value="date">Date</option>
                                                    <option value="number">Number</option>
                                                </select>
                                            </td> 
                                            <td>
                                                <select name="is_required" class="form-control">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </td> 

                                            <?php 
                                                $input_type = "text";
                                                        //if()
                                            ?>
                                            
                                            
                                            <td><button class="btn btn-success fa fa-arrow-down" type="submit" ></button></td> 
                                        </tr>
                                        <?php 
                                            $i = 1;
                                            foreach($attribute as $attr): 
                                        ?>
                                        <tr>
                                            <td><?=$i?></td>
                                            <td><input readonly class="form-control"  type="text" required value="<?=@$attr['attribute_name']?>"></td> 
                                            <td><input readonly class="form-control"  type="text" value="<?=@$attr['input_type']?>"></td> 
                                            <td><input readonly class="form-control"  type="text" value="<?=$attr['is_required']==1?"Yes":"No"?>"></td> 
                                            <td><a href="deleteprocedureattribute?pname=<?=$attr['procedure_name']?>&pid=<?=$attr['procedure_id']?>&attid=<?=$attr['id']?>" class="btn btn-danger fa fa-trash"  type="button" ></a></td> 
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
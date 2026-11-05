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
                <small>Laboratory Setup</small>
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
                            <h4>Laboratory Tests</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="portlet-body form">
                            <div class="row">    
                                <div class="col-sm-12">
                                    <table class="table table-hover">
                                        <tr style="font-weight:bold">
                                            <td>Test Name</td><td>Status</td><td></td>
                                        </tr>
                                        <?php foreach($Lab_tests as $test): ?>
                                        <tr>
                                            <td><?=$test['service_name']?></td>   
                                            <td><a href="changepricestatus?plid=<?=$test['id']?>&st=<?=$test['is_active']==0?1:0?>">
                                                    <?php 
                                                        if($test['is_active']==1){
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
                                            <td><a href="labsetup?tname=<?=$test['service_name']?>&tid=<?=$test['id']?>" <?=($test['is_active']==1)?"":"hidden"?> >
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
            <div class="col-sm-8 col-md-8"<?=@$_GET['tid']?"":"hidden"?>>
                <div  class="panel panel-bd panel-form">
                    <div class="panel-heading">
                        <div class="panel-title" >
                            <?php 
                                    helper('form');
                                    $attributes = array('class' => 'form-horizontal','method'=>'post',  'enctype' => 'multipart/form-data');
                                    echo form_open_multipart('hospital/admin/addAttribute', $attributes);                
                            ?>
                            <div> <h4>Attributes</h4></div>
                                <div style="text-align: right;">
                                     <div class="btn btn-primary" style="pointer-events: none;"><?= @$_GET['tname'] ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="portlet-body form">
                            <div class="row">    
                                <div class="col-sm-12">
                                
                                    <table class="table table-hover">
                                        <tr style="font-weight:bold">
                                            <td>No.</td><td>Attribute Name</td><td>Reference Min</td><td>Reference Max</td><td>Sex</td><td>Unit</td><td>Quntifyable</td><td></td>
                                        </tr>
                                        <input class="form-control" name="test_name" type="hidden" value="<?=@$_GET['tname'] ?>"  >
                                        <input class="form-control" name="test_id" type="hidden" value="<?=@$_GET['tid'] ?>" >
                                        <tr>
                                            <td></td>
                                            <td><input class="form-control" name="attribute_name" type="text" required ></td> 
                                            <td><input class="form-control" name="reference_range_from" type="text" ></td> 
                                            <td><input class="form-control" name="reference_range_to" type="text" ></td> 
                                            <td>
                                                <select class="form-control" name="reference_range_gender" >
                                                    <option value="" selected>For Both</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </td> 
                                            <td><input class="form-control" name="reference_range_unit" type="text" ></td> 
                                            <td>
                                                <input style="transform: scale(1.5);" name="quantifiable" value="1" type="checkbox" checked>
                                            </td>
                                            <td><button class="btn btn-success fa fa-arrow-down" type="submit" ></button></td> 
                                        </tr>
                                        <?php 
                                            $i = 1;
                                            foreach($attribute as $attr): 
                                        ?>
                                        <tr>
                                            <td><?=$i?></td>
                                            <td><input readonly class="form-control"  type="text" required value="<?=@$attr['attribute_name']?>"></td> 
                                            <td><input readonly class="form-control"  type="text" value="<?=@$attr['reference_range_from']?>"></td> 
                                            <td> <input readonly class="form-control"  type="text" value="<?=@$attr['reference_range_to']?>"></td> 
                                            <td><input readonly class="form-control"  type="text"  value="<?=@$attr['reference_range_gender']?>" ></td>
                                            <td><input readonly class="form-control"  type="text"  value="<?=@$attr['reference_range_unit']?>" ></td>
                                            <td><?=@$attr['quantifiable']==1? "<i class='fa fa-check'>":""?></td>
                                            <td><a onclick="delete_attribute('deleteattribute?tname=<?=$attr['test_name']?>&tid=<?=$attr['test_id']?>&attid=<?=$attr['id']?>')" class="btn btn-danger fa fa-trash"  type="button" ></a></td> 
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
<script>
    function delete_attribute(url){
        if(confirm('Are you sure?')){
            window.location = url;
        }
    }
</script>
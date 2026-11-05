<?php
$items = $this->data['items'];
$item = $this->data['item'];
//\BluelimeSystem::show_and_die($item); 
?>
<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Administrator</h1>
                <small>New Item</small>
            
        </div>
    </section>

<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-5 col-md-5">
                <div  class="panel panel-bd panel-form">
                <div class="panel-heading">
                    <div class="panel-title" >
                        <h4>Add New Item</h4>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="portlet-body form">
                        <div class="row">    
                            <div class="col-sm-12">
                        <?php 
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','method'=>'post',  'enctype' => 'multipart/form-data');
                            echo form_open_multipart('inventory/store/save_item', $attributes);                
                         ?>
                        
                            <div class="form-body">
                                <div class="form-group">
                                    
                                        <label class="col-md-4 control-label" style="text-align: left;">Item Type:</label>
                                    <div class="col-md-11">
                                        <select name="item_type" class="form-control" required>
                                            <option value="">--</option>
                                            <option value="COSMOTICS"<?=@$item[0]['item_type']=='COSMOTICS'?"selected":""?>>COSMOTICS</option>                                           
                                            <option value="DRUG"<?=@$item[0]['item_type']=='DRUG'?"selected":""?>>DRUG</option>
                                            <!--option value="MEDICAL EQUIPMENTS"<?=@$item[0]['item_type']=='MEDICAL EQUIPMENTS'?"selected":""?>>Medical Equipments</option>                                           
                                            <option value="MEDICAL SUPPLIES"<?=@$item[0]['item_type']=='MEDICAL SUPPLIES'?"selected":""?>>Medical Supplies</option>                                           
                                            <option value="CONSUMABLE"<?=@$item[0]['item_type']=='CONSUMABLE'?"selected":""?>>Consumable</option-->                                           
                                        </select>
                                    </div>
                               </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Generic Name:</label>
                                    <div class="col-md-11">
                                        <input type="text" name="item_generic_name" class="form-control" required value="<?=@$item[0]['item_generic_name']?>" placeholder="Generic Name"> 
                                        <input type="hidden" name="id" value="<?=@$item[0]['id']?>"> 
                                        <input type="hidden" name="edit" value="<?=@$_GET['edit']?>"> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Brand Name:</label>
                                    <div class="col-md-11">
                                        <input type="text" name="item_brand_name" class="form-control" required value="<?=@$item[0]['item_brand_name']?>" placeholder="Brand Name"> 
                                        <input type="hidden" name="id" value="<?=@$item[0]['id']?>"> 
                                        <input type="hidden" name="edit" value="<?=@$_GET['edit']?>"> 
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="col-xs-6">
                                        <label class="control-label">Preparation:</label>
                                        <select name="preparation" class="form-control">
                                            <option value="">--</option>
                                            <option value="Cachet"<?=@$item[0]['preparation']=='Cachet'?"selected":""?>>Cachet</option>
                                            <option value="Capsule"<?=@$item[0]['preparation']=='Capsule'?"selected":""?>>Capsule</option>
                                            <option value="Cream"<?=@$item[0]['preparation']=='Cream'?"selected":""?>>Cream</option>
                                            <option value="Douche"<?=@$item[0]['preparation']=='Douche'?"selected":""?>>Douche</option>
                                            <option value="Drop"<?=@$item[0]['preparation']=='Drop'?"selected":""?>>Drop</option>
                                            <option value="Elixir"<?=@$item[0]['preparation']=='Elixir'?"selected":""?>>Elixir</option>
                                            <option value="Emulsion"<?=@$item[0]['preparation']=='Emulsion'?"selected":""?>>Emulsion</option>
                                            <option value="Enema"<?=@$item[0]['preparation']=='Enema'?"selected":""?>>Enema</option>
                                            <option value="Gel"<?=@$item[0]['preparation']=='Gel'?"selected":""?>>Gel</option>
                                            <option value="Granule"<?=@$item[0]['preparation']=='Granule'?"selected":""?>>Granule</option>
                                            <option value="Implant"<?=@$item[0]['preparation']=='Implant'?"selected":""?>>Implant</option>
                                            <option value="Inhalers"<?=@$item[0]['preparation']=='Inhalers'?"selected":""?>>Inhalers</option>
                                            <option value="Injection"<?=@$item[0]['preparation']=='Injection'?"selected":""?>>Injection</option>
                                            <option value="Lotion"<?=@$item[0]['preparation']=='Lotion'?"selected":""?>>Lotion</option>
                                            <option value="Lozenge"<?=@$item[0]['preparation']=='Lozenge'?"selected":""?>>Lozenge</option>
                                            <option value="Magma"<?=@$item[0]['preparation']=='Magma'?"selected":""?>>Magma</option>
                                            <option value="Ointment"<?=@$item[0]['preparation']=='Ointment'?"selected":""?>>Ointment</option>
                                            <option value="Paste"<?=@$item[0]['preparation']=='Paste'?"selected":""?>>Paste</option>
                                            <option value="Pill"<?=@$item[0]['preparation']=='Pill'?"selected":""?>>Pill</option>
                                            <option value="Plaster"<?=@$item[0]['preparation']=='Plaster'?"selected":""?>>Plaster</option>
                                            <option value="Poultice"<?=@$item[0]['preparation']=='Poultice'?"selected":""?>>Poultice</option>
                                            <option value="Powder"<?=@$item[0]['preparation']=='Powder'?"selected":""?>>Powder</option>
                                            <option value="Solution"<?=@$item[0]['preparation']=='Solution'?"selected":""?>>Solution</option>
                                            <option value="Spray"<?=@$item[0]['preparation']=='Spray'?"selected":""?>>Spray</option>
                                            <option value="Suppository"<?=@$item[0]['preparation']=='Suppository'?"selected":""?>>Suppository</option>
                                            <option value="Suspension"<?=@$item[0]['preparation']=='Suspension'?"selected":""?>>Suspension</option>
                                            <option value="Syrup"<?=@$item[0]['preparation']=='Syrup'?"selected":""?>>Syrup</option>
                                            <option value="Tablet"<?=@$item[0]['preparation']=='Tablet'?"selected":""?>>Tablet</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6">
                                        <label class="control-label">Strength:</u></label>
                                        <input type="text" name="strength" placeholder="Strength" class="form-control" value="<?php echo @$item[0]['strength'] ?? ""  ?>">
                                    </div>
                               </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Catagory:</label>
                                    <div class="col-md-11">
                                        <select name="item_category" class="form-control" required value="" > 
                                            <option value="">--</option>
                                            <?php
                                                foreach($categories as $category){
                                            ?>
                                                    echo <option value="<?=$category['category_name']?>" <?=@$item[0]['item_category']==$category['category_name']?"selected":""?>><?=$category['category_name']?></option>;

                                            <?php
                                                }
                                            ?>
                                            
                                        </select>
                                        
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Item Code:</label>
                                    <div class="col-md-11">
                                        <input type="text" name="item_code" class="form-control" required value="<?=@$item[0]['item_code']?>" placeholder="Item Code"> 
                                        <input type="hidden" name="id" value="<?=@$item[0]['id']?>"> 
                                        <input type="hidden" name="edit" value="<?=@$_GET['edit']?>"> 
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Measurement:</label>
                                    <div class="col-md-11">
                                    <select name="measurement" class="form-control " required > 
                                            <option value=""<?=@$item[0]['measurement']==''?"selected":""?>>--</option>
                                            <?php
                                                foreach($measurements as $measurement){
                                            ?>
                                                    echo <option value="<?=$measurement['measurement_name']?>" <?=@$item[0]['measurement']==$measurement['measurement_name']?"selected":""?>><?=$measurement['measurement_name']?></option>;

                                            <?php
                                                }
                                            ?>
                                           
                                    </select>
                                </div>
                                </div>
                                
                                
                                <div class="form-group col-l-12">
                                    <div class="col-xs-6">
                                        <label class="control-label">Min Balance:</label>
                                        <input type="text" name="min_balance" class="form-control" value="<?php echo @$item[0]['min_balance'] ?? ""  ?>">
                                    </div>
                                    <div class="col-xs-6">
                                        <label class="control-label">Notify Expiry:</u></label>
                                        <input type="text" name="notify_expiry" class="form-control" value="<?php echo @$item[0]['notify_expiry'] ?? ""  ?>">
                                    </div>
                                    <div class="col-xs-12">
                                        <fieldset class="reset">
                                            <legend class="reset">Compounding</legend>
                                            <div class="col-lg-6">
                                                <label class="control-label">Measurement:</label>
                                                <select class="form-control" name="compounding_measurement">
                                                    <?php
                                                        $compounding_measurement = $item[0]['compounding_measurement'] ?? '';
                                                    ?>
                                                    <option></option>
                                                    <option value="gm" <?= ($compounding_measurement == 'gm') ? 'selected' :'' ?>>gm</option>
                                                    <option value="ml" <?= ($compounding_measurement == 'ml') ? 'selected' :'' ?>>ml</option>
                                                    <option value="ml" <?= ($compounding_measurement == 'Pcs') ? 'selected' :'' ?>>Pcs</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="control-label">Volume/Weight:</label>
                                                <input type="number" name="compounding_volume" class="form-control" value="<?php echo @$item[0]['compounding_volume'] ?? ""  ?>">
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                                
                                <div class="form-group col-lg-12">
                                        <div class="col-xs-4">
                                            <label class="col-md-8 control-label" style="text-align: left;">Active</label>
                                            <label class="switch">
                                            <?php 
                                            $item_status = "checked";
                                            if(@$item[0]["is_active"] == "0"){
                                                $item_status = "";
                                            }
                                            ?>
                                            <input name="is_active" type="checkbox" <?=$item_status?>>
                                            <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <div class="col-xs-4">
                                            <label class="col-md-8 control-label" style="text-align: left;">Taxable</label>
                                            <label class="switch">
                                            <input name="is_taxable" type="checkbox" <?=@$item[0]['is_taxable']==1?"checked":""?>>
                                            <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <div class="col-xs-4">
                                            <label class="col-md-8 control-label" style="text-align: left;">Perishable</label>
                                            <label class="switch">
                                            <?php 
                                            $item_perishablity = "checked";
                                            if(@$item[0]["is_active"] == "0"){
                                                $item_perishablity = "";
                                            }
                                            ?>
                                            <input name="is_perishable" type="checkbox" <?=$item_status?>>
                                            <span class="slider round"></span>
                                            </label>
                                        </div>
                                       
                                </div>
                                

                                <div class="form-group">
                                    
                                </div>
                                <div class="form-group">
                                    
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
                <div class="panel-body">
                    <div class="portlet-body form">
                        <div class="row">   
                             
                            <div class="col-sm-12">
                <form class="form-horizontal" role="form">
                                <div class="form-group">
                                    <div class="row">&nbsp;</div>
                                    <div class="col-sm-6">
                                        <input id="search_key" type="text" placeholder="<?php echo  "Search Item" ?>" name="search_key" class="form-control" value="<?= @$searchkey ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button>
                                        
                                    </div>
                                </div>
                            </form>
                   
                            <div align="right" style="margin: 0px;height: 53px;"> <?= $pager->links() ?></div> 
                    <table class="table table-striped table-bordered table-condensed">
                         
                   
                        <tr style="font-weight: bold;font-size: 13px;">
                            <td>SN</td>
                            <td>Item Code</td>
                            <td>Generic Name</td>
                            <td>Brand Name</td>
                            <td>Unit</td>
                            <td></td>
                        </tr>
                        <?php 
                            $sn = ($page-1) * 15;
                            foreach($items as $item):
                                $sn++;
                            $clr = "";
                            if($item['is_active']==0) {
                                $clr = "background-color: red;color:white";
                            }   
                        ?>
                        <tr style="font-size: 12px;<?=$clr?>">
                            <td><?=$sn?></td>
                            <td><?=$item['item_code']?></td>
                            <td><?=$item['item_generic_name']?></td>
                            <td><?=$item['item_brand_name']?><br><?=$item['strength']?><br><?=$item['preparation']?></td>
                            <td><?=$item['measurement']?></td>
                            
                            <td><a href="<?php echo base_url();?>inventory/store/new_item?iid=<?=$item['id']?>&edit=<?=1?>">
                                    <button type="button" class="fa fa-edit btn-primary" aria-hidden="true">
                                    </button>
                                </a>

                            </td>
                        </tr>
                        <?php 
                           endforeach;
                        ?>
                    </table>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>

        </div>
            
    </section>
  
            
    

</div>
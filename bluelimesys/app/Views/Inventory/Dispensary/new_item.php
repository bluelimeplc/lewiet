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
            <h1>Dispensary</h1>
                <small>Item Status</small>
            
        </div>
    </section>

<!-- Main content -->
    <section class="content">
        <div class="row">
            

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
                                        <input id="search_key" type="text" placeholder="<?php echo  "Search Item" ?>" name="search_key" class="form-control" value="<?= $search_key_input ?>">
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
                            <td>Item</td>
                            <td></td>
                        </tr>
                        <?php 
                            $sn = ($page-1) * 15;
                            foreach($items as $item):
                                $sn++;    
                        ?>
                        <tr style="font-size: 12px;">
                            <td><?=$sn?></td>
                            <td><?='<b>'.$item['item_brand_name'].'</b> '.$item['item_generic_name'].' <span class="badge">'.$item['strength'].'</span> <span class="badge badge-qty">'.$item['preparation'].'</span> '?></td>
                            <td><div class="col-xs-12">
                                            <label class="switch">
                                            <?php 
                                            $item_status = "checked";
                                            if($item["is_active"] == "0"){
                                                $item_status = "";
                                            }
                                            ?>
                                            <input onchange="changestatus(<?=$item['id']?>,<?=$item['is_active']?>)" id="is_active" name="is_active" type="checkbox" <?=$item_status?>>
                                            <span class="slider round"></span>
                                            </label>
                                            <input id="item_is_active"  type="hidden" value="<?=intval($item['is_active'])?>" >
                                        </div></td>
                            
                            
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
<script>
    function changestatus(item_id,is_active)
    {
        
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>inventory/dispensary/changeitemstatus",
            data: {'is_active':is_active,'item_id':item_id},
            success: function(result){
                //return alert(parseInt(result));
                if(result){
                    toastr['success']("Item is Active");
                    }
               
                }
                
        });
    }
</script>
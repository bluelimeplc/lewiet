<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $patient = $this->data['patient'];
    $doctors = $this->data['doctors'];
    $bill = $this->data['bill_info'];
    $bills = $this->data['bills'];
    $bill_detail = $this->data['bill_detail'];
    $mrn_no = $bill['patient_id'];
    $formated_mrn_no = sprintf('%05d', $mrn_no);
    //BluelimeSystem::show_and_die($this->data['bill_detail']);
    
   
?>
<!-- Invoice js -->
<script src="<?php echo base_url()?>assets/my-assets/service.js.php" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/my-assets/invoice.js" type="text/javascript"></script>
<!-- =============================================== -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>

        <div class="header-title">
            <h1><?php echo 'Reception'; ?></h1>
            <small><?php echo 'Invoice'; ?></small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/reception/pendingbills"><?php echo "Pending Bills"?></a></li>
            </ol>
        </div>
    </section>
    
    
    
    <section class="content">
	    <div class="row">
            <div class="col-sm-12">
            <div class="panel panel-bd">
                        
            </div>
            </div>
        </div>
    <div class="panel panel-bd">
        <div class="panel-body">
            <h1>Temp Order</h1>
            <div class="" id="add_service" tabindex="-1" role="dialog" aria-labelledby="addService" aria-hidden="true">
                        <di class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Add Service</h5>
                                </div>
                                <div class="modal-body">
                                
                                            <?php 
                                                helper('form');
                                                $att = array('name'=>'save_invoice','class'=>'form-vertical','id'=>'save_invoice');
                                                echo form_open_multipart('/hospital/reception/addservice',$att);
                                            ?>
                                                <div class="col-sm-3">
                                                    <select class="form form-control" name="bill_id" >

                                                    <?php
                                                        foreach($bills as $b):
                                                    ?>

                                                    <option name="bill_id" value="<?php echo $b['id'] ?>"><?php echo $b['patient_name'] ?></option>
                                                    <?php
                                                    endforeach;
                                                ?>
                                                </select> 
                                                </div>  
                                                    <div class="col-sm-6">
                                                        <input type="hidden" name="bill_discount" value="0">
                                                        <input type="hidden" name="bill_quantity" value="1">
                                                        
                                                        <input type="hidden" name="service_status" value="0">
                                                    <select class="form form-control" name="service_id" >

                                                            <?php
                                                                foreach($services as $service):
                                                            ?>
                                                            
                                                            <option name="service_id" value="<?php echo $service['id'] ?>"><?php echo $service['service_name'] ?></option>
                                                            <?php
                                                            endforeach;
                                                            ?>
                                                        </select>
                                                    </div>
                                
                                                    <div class="col-sm-2">
                                                        
                                                        <button type="submit" form="save_invoice" class="btn btn-success">Add<i class="fa fa-plus"></i></button> 
                                                    </div>
                                                        </div>
                                            <?php
                                                form_close();
                                            ?>
                                        


                                </div>
                                    
                                <div class="modal-footer">
                                    
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
                                    
                                </div>
                        
                        </div>
                    </div>
                </div>

        </div>
    </div>
    </div>
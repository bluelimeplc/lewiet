<?php
   $loggeduserdata = @$this->data['loggeduserdata'];
    $logged_user = @$loggeduserdata['id'];
?>
<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Laboratory</h1>
                <small>Add Result</small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/laboratory/">Home</a></li>
            </ol>
        </div>
    </section>

<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-6 col-md-6"  style="pointer-events: <?= !empty($lab_results)?"none":""?>;">
            <div  class="panel panel-bd panel-form">
             <?php 
                    foreach($orders as $order){
                ?>
                <div class="panel-heading">
                    <div class="panel-title" >
                        <h4></h4>
                    </div>
                </div>
                
                <div class="panel-body">
                        <?php 
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','method'=>'post',  'enctype' => 'multipart/form-data');
                            echo form_open_multipart('hospital/laboratory/addresult', $attributes);                
                         ?>
                        
                        <div class="form-body" disable>
                            <?php 
                                $cnt=1;
                                foreach($attribute as $attr)
                                {
                                    
                            ?>
                                    <div class="form-group">
                                            <label class="col-md-12 control-label" style="text-align: left;"> <?=$attr['attribute_name']?>: </label>
                                            <div class="col-md-6">
                                                <input type="<?=$attr['quantifiable']==1?"number":"text" ?>" name="result[]" step=".01" class="form-control" required value="" autocomplete="off"> 
                                                
                                        <input type="hidden" name="attribute_name[]" value="<?=$attr['attribute_name']?>">
                                        <input type="hidden" name="attribute_id[]" value="<?=$attr['id']?>">
                                            </div>
                                            <div class="col-md-6">
                                            <?=$attr['reference_range_from']?> - <?=$attr['reference_range_to'] ?> <?=$attr['reference_range_unit'] ?>
                                            </div>
                                        </div>
                            <?php
                                    $cnt++;
                                }
                            ?>
                            <div class="form-group row" >
                                <div class="col-sm-offset-3 col-sm-9" style="text-align: right;">
                                        <input type="hidden" name="registered_by" value="<?=$logged_user?>">
                                        <input type="hidden" name="registered_by_name" value="<?=@$loggeduserdata['name']?>">
                                        <input type="hidden" name="test_id" value="<?=$_GET['id']?>">
                                        <input type="hidden" name="bill_id" value="<?=$_GET['bid']?>">
                                        <input type="hidden" name="billdetail_id" value="<?=$_GET['bdid']?>">
                                     <button type="reset" class="btn btn-danger">Reset</button>
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                            <?php echo form_close();?>
                        </div>
                </div>
                <?php
                    }
                ?>
                
            </div>
        </div>
        
        <div class="col-sm-6 col-md-6" >
            
            <div  class="panel panel-bd panel-form">
                <div class="panel-heading">
                    <div class="panel-title" >
                        <h4><?="Saved Results"?></h4>
                    </div>
                </div>
                
                <div class="row" style="font-size:11px; padding: 10px 10px 0px 10px;">
                <div class="col-sm-4 col-md-4">
                    <b><?=$patient['first_name']." ".$patient['middle_name']." ".$patient['middle_name']; ?></b><br>
                    Age: <span id="patient_age">01</span> <br>
                    <script>
                        function getAge(val){
                            var CurrentYear = new Date().getFullYear();
                            document.getElementById('patient_age').innerHTML = CurrentYear - val.slice(0, 4);
                        }
                        getAge('<?=$patient['date_of_birth']?>');
                    </script>
                    Sex: <?=$patient['sex'] ?><br>
                    Patient ID: MRN-<?=sprintf('%05d', $patient['id']) ?>

                </div>
                <div class="col-sm-4 col-md-4">
                    <b>Sample Collected By:</b><br>
                    Ordered By: Dr. Abush Nadew <br>

                </div>
                <div class="col-sm-4 col-md-4">
                    Registered On: <?=$bill['billed_date']?> <br>
                    Collected On: 12/07/2023 10:40<br>
                    Reported On: 12/07/2023 11:30

                </div>  
                </div>  
                <hr style="padding: 0px;">
                <b><center style="padding: 0px;"><?=$attribute[0]['test_name']?></center>     </b>      
                <div class="panel-body">
                <table class="table table-condensed">
                    <th style="font-weight: bolder;">
                        <td>Investigation</td>
                        <td>Result</td>
                        <td>Flag</td>
                        <td>R. Range / N. Value</td>
                        
                    </th>
                <?php
                    foreach($lab_results as $rt){
                ?>
                        <tr style="font-size:12px;">
                            <td></td>
                            <td><?=$rt['attribute_name'];?></td>
                            <td><?=$rt['result'];?></td>
                            <td style="color: red;"><b>
                                <?php 
                                if($rt['quantifiable']!=0)
                                    if($rt['result'] < $rt['reference_from'])
                                        echo "L";
                                    else if($rt['result'] > $rt['reference_to'])
                                        echo "H";
                                    else
                                    echo "";
                                else
                                    
                                    if($rt['reference_from']!==$rt['result'])
                                    echo "Abnormal";

                                ?></b>
                            </td>
                            <td><?=$rt['reference_from'];?> - <?=$rt['reference_to'];?> <?=$rt['reference_unit'];?></td>
                            
                        </tr>
                <?php
                    }
                ?>
                </table>
                <hr>
                <div class="row">
                <div class="col-sm-4 col-md-4">Ordered By</div>  <div class="col-sm-4 col-md-4">Lab Technician</div> Approved By
                </div>
                </div>
            </div>
        </div>

        
        </div>

            


            
    </section>
  
            
    

</div>
<?php
    $loggeduserdata = \BluelimeAuthentication::get_logged_user_data();
    $pending_bills = $this->data['pending_bills'];
    $logged_user = $loggeduserdata['id'];
    $logged_user_name = $loggeduserdata['name'];
    $logged_user_role = $loggeduserdata['role'];
?>
    <!-- =============================================== -->
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="header-icon">
                <i class="pe-7s-world"></i>
            </div>
            <div class="header-title">
                <h1>Reception</h1>
                
                <small>Pending Bills</small>
                <ol class="breadcrumb">
                    <li class="active"><a href="/hospital/reception/receivedbills">Recieved Payments </a></li>
                </ol>
            </div>
        </section>

      
                <!-- Main content -->
        <section class="content">
            <div class="row">
                 <!--  Search area -->           
                <div class="col-sm-12 col-md-12">
                    <div class="panel panel-bd">
                        <div class="panel-body">
                            <form method="get">
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <input id="search_patient" type="text" name="q" class="form-control refocusable" value="<?= $_GET['q'] ?? '' ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-success">Search <i class="fa fa-search"></i></button> 
                                    </div>
                                    <div align="right">
                                    <a href=""  class="btn btn-success"  title="Add Order"  data-target="#new_order" data-toggle="modal" data-html="true">External Order<i class="fa fa-order"></i></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                

                
                <!--  Bill list area -->
                <div class="col-sm-12">
                    <div  class="panel panel-default">
                        <div class="panel-body  table-responsive">
                            <div style="text-align: right;">
                                <?= $pager->links() ?>
                            </div>
                            <table width="100%" class="table table-striped table-bordered table-hover" id="patient_list4">
                                <thead>

                                    <tr>                     
                                        <th>MRN No</th>
                                        <th>Full Name</th>
                                        <th>Company</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        
                                        <th><?php echo lang('Action');?></th> 
                                    </tr>

                                </thead>

                                <tbody>
                                    <?php
                                            if($active_menu=='received_bills') 
                                                $active = 'viewreceipt';  
                                            else
                                                $active = 'invoicebill';
                                    ?>
                                    <?php
                                        foreach ($pending_bills as $pending_bill) :
                                    ?>
                                    
                                    <tr <?= ($pending_bill['bill_status'] == \BluelimeConfiguration::BILL_REMOVE_REQUESTED) ? 'style="background: repeating-linear-gradient( -45deg, #fee, #fee 2px, #ddd 2px, #fdd 15px )"' : '' ?>>

                                        <td><?php echo "MRN-".sprintf('%05d', $pending_bill['patient_id']); ?></td>
                                        <td><?php echo  $pending_bill['patient_name'] ?> </td>
                                        <td><?php echo  $pending_bill['company_name'] ?> </td>
                                        <td><?php echo  $pending_bill['billed_date']  ?> </td>
                                        <td><?php 
                                                if($pending_bill['bill_status']==BluelimeConfiguration::BILL_SENT_TO_POS)
                                                    echo "ON POS" ;
                                                elseif($pending_bill['bill_status']==BluelimeConfiguration::BILL_REMOVE_REQUESTED)
                                                    echo "Delete Requested" ;
                                                else 
                                                    echo "PENDING";
                                            ?> 
                                        </td>
                                        
                                        <td>
                                            
                                            <?php
                                                if($pending_bill['type'] == 'bill'):
                                            ?>
                                            
                                                    <a class="btn  btn-<?php echo (@$pending_bill['_notify']['status']==1 or !isset($pending_bill['_notify']['status']))?"info":"warning";?> btn-sm" href="<?php echo base_url();?>hospital/reception/<?php echo $active?>?id=<?= $pending_bill['id']; ?>">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <?php
                                                        if($pending_bill['bill_status']==BluelimeConfiguration::BILL_SENT_TO_POS){
                                                            echo '
                                                            <a title="Reverse from POS" class="btn btn-warning btn-sm" href="/hospital/reception/reverserecievedbill?bill_id='.$pending_bill['id'].'" onclick="return confirm(\'Are you sure you want to Reverse?\');">
                                                                <i class="fa fa-reply"></i>
                                                            </a>';
                                                        }
                                                        elseif(($loggeduserdata['id'] == $pending_bill['registered_by']) || ($logged_user_role == "SUPER_ADMIN")){
                                                    ?>
                                                            <a class="btn  btn-danger btn-sm" href="<?php echo base_url();?>hospital/reception/deletependingbill?bid=<?php echo $pending_bill['id'];?>" onclick="return confirm('Are you sure you want to delete?');">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                    <?php
                                                        }
                                                        else
                                                            echo '
                                                            <a class="btn bg-gray btn-sm" disabled>
                                                                <i class="fa fa-trash"></i>
                                                            </a>';
                                                    ?>
                                            <?php
                                                else:
                                            ?>
                                                    <a class="btn  btn-<?php echo (@$pending_bill['_notify']['status']==1 or !isset($pending_bill['_notify']['status']))?"success color-yellow btn-sm":"warning color-green btn-sm";?>" href="<?php echo base_url();?>hospital/reception/orderoptions?id=<?= $pending_bill['id']; ?>">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                            <?php
                                                endif;

                                            ?>
                                            
                                        </td> 
                                    </tr>
                                    <?php
                                        endforeach
                                    ?>
                                </tbody>
                            </table> 
                        </div>
                    </div>
                </div>

            </div>            
        </section>

    </div> <!-- /.content-wrapper -->

    <div class="modal fade" id="new_order" tabindex="-1" role="dialog" aria-labelledby="NewOrder" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header dental-bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">New Service Order</h4>
                                </div>
                        <div class="form-body"">
            <div class="modal-body">
            <div class="panel-body">
                <div class="portlet-body form">
                    
                
                <?php 
                    helper('form');
                    $attributes = array('class' => 'form-horizontal', 'method'=>'post','name'=>'add_order','id'=>'add_order');
                    echo form_open('/hospital/reception/addorder', $attributes);                
                ?>
                        
                        <div class="tab-pane active">
                                    <label class="control-label"><?php echo "Mobile"?> :</label>
                                    <div class="">
                                        <input type="txt" placeholder="09xxxxxxxx"  name="mobile" id="patient_mobile_input" required class="form-control">
                                    </div>
                        </div>
                        <div class="tab-pane active">
                                <label class="control-label">Full Name:</label>
                            <div class="tab-pane active">
                                <div class="input-group">
                                    <input type="text" required  name="first_name" id="first_name" class="form-control" placeholder="Name">
                                    <span class="input-group-addon"></span>
                                    <input type="text" required name="middle_name" id="middle_name" class="form-control" placeholder="Father Name">
                                    <span class="input-group-addon"></span>
                                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Grand Father">
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-l-12">
                                                <div class="col-xs-2" style="padding-right:0">
                                                    <label class="control-label">Age <i style="color:red">*</i></label>
                                                        <?php
                                                        $patient_dob = isset($patient['date_of_birth']) ? $patient['date_of_birth']:'';
                                                        $diff = date_diff(date_create($patient_dob), date_create(date("Y-m-d")));
                                                        $age = $diff->format('%y');
                                                        ?>
                                                        <input class="form-control"  name="old" id="age" min="0" step="0.01" max="100" type="number"  required placeholder="Age"  onload="getdob(this.value)" oninput="getdob(this.value) " value="<?php echo $age ?? ""   ?>">
                                                    </div>
                                                    <div class="col-xs-5"style="padding-left:0">
                                                    <label class="control-label">Date of Birth</label>
                                                        <input tabindex="-1" type="date" id="dob" name="date_of_birth" style="" class="form-control" placeholder="Select Date"  onload="getAge(this.value)" onchange="getAge(this.value)"  value="<?php echo $patient['date_of_birth'] ?? ""  ?>">
                                                    </div>
                                                    
                                                    <div class="col-xs-5">
                                                        <label class="control-label">Sex <i style="color:red">*</i></label>
                                                    
                                                        <select name="sex" id="sex" class="form-control" required>
                                                            <option  value="">--Select Sex--</option>
                                                            <option value="Female" <?php echo (@$patient['sex']=='Female')?'selected':'' ?>>Female</option>
                                                            <option value="Male" <?php echo (@$patient['sex']=='Male')?'selected':'' ?>>Male</option>
                                                        </select>
                                                    </div>
                                                </div>
                        <div id="option_1" class="tab-pane active"><br>
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr class="bg-info">
                                                                <th width="450">Service</th>
                                                                <th width="100">Quantity</th>
                                                                <th>&nbsp;</th>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                <select class="form-control" id="service" required>
                                                                    <option value=""></option>
                                                                    <?php
                                                                        foreach($services as $service)
                                                                        if($service['service_type']!="Consultation")
                                                                        echo '<option value="'. $service['id'] .'">'. $service['service_name'] .'</option>';
                                                                        
                                                                    ?>
                                                                </select>
                                                                </td>
                                                                <td>
                                                                    <input type="number" min="1" value="1" class="form-control" id="order_quantity" />
                                                                </td>
                                                                <td>
                                                                    <button class="btn btn-info" onclick="add_service('order_service_list');" type="button"><i class='fa fa-plus' ></i></button>
                                                                    <!--button class="btn btn-info" onclick="remove_treatment_plan(1,1);" type="button"><i class='fa fa-minus' ></i></button-->
                                                                </td>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="order_service_list">

                                                            </tbody>
                                                    </table>
                                                </div>
                            <div class="form-group col-md-12">
                                <div class="input-group">
                                   
                                    <input type="text" hidden name="visit_id" value="<?php echo @$visit_id ?? ""?>">
                                    <input type="text" hidden name="is_external" value="<?php echo 1 ?>">
                                    <input type="text" hidden name="visit_date" value="<?php echo @$visit_date ?? ""?>">
                                    <input type="text" hidden name="registered_by" value="<?php echo $logged_user?>">
                                    <input type="text" hidden name="registered_by_name" value="<?php echo $logged_user_name?>">
                                    <input type="txt"  hidden name="company_id" value="<?php echo @$patient['company_id'] ?? 0  ?>" >
                                    
                                   
                                                          
                            </div>
                            </div>
                            
                            <div class="tab-pane active">
                                <label class="control-label">Order By Doctor:</label>
                            <div class="tab-pane active">
                                <select class="form-control" id="order_by"  name="order_by"  required>
                                    <option value="">--Select doctor--</option>
                                    <option value="-1">Nurse</option>
                                    <?php
                                        foreach($doctors as $doctor)
                                        echo '<option value="'. $doctor['id'] .'">'. $doctor['name'] .'</option>';
                                        
                                    ?>
                                </select>
                            </div>
                            </div>
                            <div class="tab-pane active">
                                <label class="control-label"><br>Description:</label>
                            <div class="tab-pane active">
                                <input type="text" name="description"class="form-control" >
                                
                            </div>
                            </div>
                            
                            
                            
                            <div class="form-group row"></div>
                            <div class="alert alert-warning <?php echo (@$_GET['rt']==1)?"":"hide"; ?>" role="alert">
                            <span></span>
                            </div>
                            
                    </div>  
                        
                    </div>
                    
                </div>
                
            </div>
                
            <div class="modal-footer">
                
                <button type="button" onclick="validateServiceList()" class="btn btn-primary" >Save</button>
                
            </div>
                <?php echo form_close(); ?>
            </div>
            </div>
</div>

<script>
    var cnt = 0;
    function add_service(t){
        
        var count = 1;
        var service_id  = $("#service").val();
        var  service_name = $("#service option:selected").text();
        var order_quantity = $("#order_quantity").val();
        var a = "service_name" + count;
            e = document.createElement("tr");
        if(service_name!="" && order_quantity!="" ){

        
        e.innerHTML = "<td style='width: 200px;'><input name='service_name[]' type='text' value='"
        +service_name+"' readonly required class='form-control' id='" + a + "'><input name='service_id[]' type='hidden' value='"
        +service_id+"' class='form-control'></td><td><input name='bill_quantity[]' type='text' value='"
        +order_quantity+"' readonly class='form-control'></td><td><button type='button' onclick='deleteRow(this)'"
        +"class='btn btn-danger'><i class ='fa fa-trash'></i></button></td>",
        document.getElementById(t).appendChild(e), count++, cnt++

        }

        else
        dental_show_alert("Please fill all required fields")
    }
    function deleteRow(t) {

        var a = $("#order_service_list").length;
        var e = t.parentNode.parentNode;
        e.parentNode.removeChild(e);
        cnt--;
        
    }
    function validateServiceList(){
        if(validateorder()){
            if(cnt > 0)
            $('#add_order').submit();
    
        else
            toastr['error']("Please add Service from the List");
            return false;
        }
    }
    var CurrentYear = new Date().getFullYear();
    var CurrentMonth = new Date().getMonth();
    //var mm = 0
    var CurrentDate = new Date().getDate();
    const zeroPad = (num, places) => String(num).padStart(places, '0');
    function getdob(val) {
        year = Math.floor(val);
        month = parseInt(val.toString().split('.')[1], 10);
        
        month = CurrentMonth - month;
        if(month < 1){
            month = 12 - (-1*month);
            year++;
        }
        // mm =month;
        

        if (isNaN(month) || month>12)
            month = CurrentMonth;

        if(month < 10)
            month = "0" + month;

        dob = CurrentYear-year +'-'+ month +'-05';
        
        document.getElementById('dob').value = dob;
    }
    
    function getAge(val) {
        
        document.getElementById('age').value = CurrentYear - val.slice(0, 4);
    }
    
    function validateorder(){
            let mobile = $("#patient_mobile_input").val();
            let first_name = $("#first_name").val();
            let middle_name = $("#middle_name").val();
            let last_name = $("#last_name").val();
            let order_by = $("#order_by").val();
            let age = $("#age").val();
            let sex = $("#sex").val();
            
            if(mobile.length != 10){ 
                toastr['error']("Incorrect Mobile Number");		
                return false;
            }
            if(first_name == ""){ 
                $("#first_name").css("borderColor","red")
                toastr['error']("Enter First Name");		
                return false;
            }
            if(middle_name == ""){ 
                $("#middle_name").css("borderColor","red");
                toastr['error']("Enter Middle Name");		
                return false;
            }
            if(last_name == ""){ 
                $("#last_name").css("borderColor","red");
                toastr['error']("Enter Last Name");		
                return false;
            }
            if(age <= 0){ 
                $("#age").css("borderColor","red");
                toastr['error']("Enter age");		
                return false;
            }
            if(sex == ""){ 
                $("#sex").css("borderColor","red");
                toastr['error']("Enter Sex");		
                return false;
            }
            if(sex == ""){ 
                $("#sex").css("borderColor","red");
                toastr['error']("Enter Sex");		
                return false;
            }
            if(order_by == ""){ 
                $("#order_by").css("borderColor","red");
                toastr['error']("Select Order By");		
                return false;
            }
            else{ 
                return true;  
            }
            
            
        }
</script>
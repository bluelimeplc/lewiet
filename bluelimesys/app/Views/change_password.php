<?php
    $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
    $loggedusersign = $loggeduserdata['digital_signature'];
    $user = $this->data['user'];
     //\BluelimeSystem::show_and_die($_GET['loc']);
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
            <h1>User Info</h1>
            <small>User Info</small>
            
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-6">
                
                <div  class="panel panel-bd">
                <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Change Password</h4>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="portlet-body form">
                            <?php 
                            $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
                            $role = $loggeduserdata['role'];
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','role'=>'form','onsubmit'=>'return confirmpassword()');
                                echo form_open('updatepassword', $attributes);  
                            ?>
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Old Password</label>
                                        <div class="col-md-8">
                                            <input type="password" class="form-control" name="old_password" required >  
                                            <input type="hidden" class="form-control" name="loc" value="<?=@$_GET['loc']?>" required >  

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">New Password</label>
                                        <div class="col-md-8">
                                            <input type="password" class="form-control"  id="new_password" name="new_password" required >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Confirm Password</label>
                                        <div class="col-md-8">
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required > 
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="form-actions text-right">
                                    <button type="submit" class="text-right btn btn-success">Change</button>
                                </div>
                            <?php echo form_close(); 
                            ?>
                        </div>
                    </div>
                </div>
            </div>

    
            <div class="col-md-6">
                
                <div  class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>Change Pin</h4>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="portlet-body form">
                            <?php 
                            
                            $attributes = array('class' => 'form-horizontal','role'=>'form','onsubmit'=>'return confirmpivn()');
                                echo form_open('updatepin', $attributes);  
                            ?>
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Old Pin</label>
                                        <div class="col-md-8">
                                            <input type="password"  maxlength="4" class="form-control" name="old_pin" required >  
                                            <input type="hidden" class="form-control" name="loc" value="<?=@$_GET['loc']?>" required >  

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">New Pin</label>
                                        <div class="col-md-8">
                                            <input type="password"  maxlength="4" class="form-control"  id="new_pin" name="new_pin" required >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label">Confirm Pin</label>
                                        <div class="col-md-8">
                                            <input type="password"  maxlength="4" class="form-control" id="confirm_pin" name="confirm_pin" required > 
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="form-actions text-right">
                                    <button type="submit" class="text-right btn btn-success">Change</button>
                                </div>
                            <?php echo form_close();?>
                        </div>
                    </div>
                </div>
            </div>
            </div>            
    </section>

    <!--section class="content">
        <div class="row">
            <div class="col-md-6">
                
                <div  class="panel panel-bd">
                    

                    <div class="panel-body">
                        <div class="portlet-body form">
                            <?php 
                            
                            $attributes = array('class' => 'form-horizontal','role'=>'form');
                                echo form_open('hospital/'.strtolower($role).'/userinfo', $attributes);  
                            ?>
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" style="text-align: left;"> Username:</label>
                                        <div class="col-md-11">
                                            <input type="text" name="username" class="form-control" readonly value="<?=@$user[0]['username']?>" placeholder="Username"> 
                                            
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" style="text-align: left;"> Full Name:</label>
                                        <div class="col-md-11">
                                            <input type="text" name="full_name" class="form-control" required value="<?=@$user[0]['name']?>" placeholder="Full Name"> 
                                            <input type="hidden" name="id" value="<?=@$user[0]['id']?>"> 
                                            <input type="hidden" name="edit" value="<?=@$_GET['edit']?>"> 
                                            
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" style="text-align: left;"> Email:</label>
                                        <div class="col-md-11">
                                            <input type="text" name="email" required value="<?=@$user[0]['email']?>" class="form-control" placeholder="Email"> 
                                            
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" style="text-align: left;"> Mobile:</label>
                                        <div class="col-md-11">
                                            <input type="text"  name="mobile" value="<?=@$user[0]['mobile']?>" class="form-control" placeholder="Mobile"> 
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions text-right">
                                    <button type="submit" class="text-right btn btn-success">Save</button>
                                </div>
                            <?php echo form_close();?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                
                <div  class="panel panel-bd">
                    

                    <div class="panel-body" style="min-height: 350px;">
                        <div class="portlet-body form">
                            <?php 
                            
                            $attributes = array('class' => 'form-horizontal','role'=>'form', 'method' => 'post',  'enctype' => 'multipart/form-data');
                                echo form_open('hospital/'.strtolower($role).'/updatesignature', $attributes);  
                            ?>
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" style="text-align: left;">Digital Signature:</label>
                                        <div class="col-md-11">
                                            <input type="file" name="digital_signature" value="" required>       
                                        </div>
                                    </div>
                                    <div>
                                        <?php echo '<img  style="height: 200px;" src="data:image/png;base64,'.base64_encode( $loggedusersign ).'"/>' ?>
                                    </div>
                                </div>
                                <div class="form-actions text-right" style="float:bottom;">
                                    <button type="submit" class="text-right btn btn-success">Save</button>
                                </div>
                            <?php echo form_close();?>
                        </div>
                    </div>
                </div>
            </div>
        </div>            
    </section-->
</div>

<script>
    function confirmpassword(){
        var new_password = $("#new_password").val();
        var confirm_password = $("#confirm_password").val();

        if(new_password != confirm_password){
            dental_show_alert("Passwords do not Match");
            return false;
        }
    }
    function confirmpin(){
        var new_pin = $("#new_pin").val();
        var confirm_ppin = $("#confirm_pin").val();

        if(new_pin != confirm_pin){
            dental_show_alert("Pins do not Match");
            return false;
        }
    }
<?php
    $fail = $_GET['fail'] ?? false;
    if($fail)
        echo 'dental_show_alert("Password Not Changed!");';
?>
</script>
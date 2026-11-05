<?php
$users = $this->data['users'];
$user = $this->data['edituser'];
 //BluelimeSystem::show_and_die($user);
?>
<div class="content-wrapper">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1>Administrator</h1>
                <small>New User</small>
            <ol class="breadcrumb">
                <li class="active"><a href="/hospital/admin/">Dashboard</a></li>
            </ol>
        </div>
    </section>
                        
<!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-5 col-md-5">
                <div  class="panel panel-bd panel-form">
                <div class="panel-heading">
                    <div class="panel-title" >
                        <h4>Add New User</h4>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="portlet-body form">
                        <div class="row">    
                            <div class="col-sm-12">
                        <?php 
                            helper('form');
                            $attributes = array('class' => 'form-horizontal','method'=>'post', 'id' => 'form_user', 'onsubmit' => 'return validate_user_form()' ,'enctype' => 'multipart/form-data');
                            echo form_open_multipart('hospital/admin/adduser', $attributes);                
                         ?>
                        
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Full Name:</label>
                                    <div class="col-md-11">
                                        <input type="text" name="full_name" class="form-control" required value="<?=@$user[0]['name']?>" placeholder="Full Name"> 
                                        <input type="hidden" name="id" value="<?=@$user[0]['id']?>"> 
                                        <input type="hidden" name="edit" value="<?=@$_GET['edit']?>"> 
                                        
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;"> Username:</label>
                                    <div class="col-md-11">
                                        <input type="text" name="username" id="username" class="form-control" required value="<?=@$user[0]['username']?>" placeholder="Username"> 
                                        
                                    </div>
                                </div>

                                <div class="form-group" <?=@$_GET['edit']==1?"hidden":""?>>
                                    <label class="col-md-4 control-label" style="text-align: left;"> Password:</label>
                                    <div class="col-md-11">
                                        <input type="password" name="password" required value="" class="form-control" > 
                                        
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

                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;">Role:</label>
                                    <div class="col-md-12">
                                        <?php
                                            $role_reflection = new ReflectionClass(BluelimeConfiguration::USER_ROLE);
                                            $roles = $role_reflection->getConstants();
                                            $user_role = $edituser[0]['role'] ?? '';
                                            foreach($roles as $role){
                                                echo '
                                                <div class="form-check form-check-inline" style="display: inline-block; margin: 8px; padding: 5px;">
                                                    <input class="form-check-input" type="radio"  name="role" value="'.$role.'" id="role_'.$role.'" onclick="role_is_choosed(this)"';
                                                if($user_role == $role)
                                                    echo ' checked ';
                                                echo ' required>
                                                    <label class="form-check-label" for="role_'.$role.'">
                                                        '. ucfirst(strtolower($role)).'
                                                    </label>
                                                </div>
                                                ';
                                            }
                                        ?>
                                    </div>
                                </div>
                                <div id="default_store_div" <?= (in_array($user_role, $default_store_roles)) ? '' : 'hidden'?> >
                                    <div class="form-group">
                                        <label class="col-md-12 control-label" style="text-align: left;">Default Store:</label>
                                        <div class="col-md-12">
                                            <select id="default_store" class="form-control" name="default_store">
                                                <option value=""></option>
                                                <?php
                                                    $user_default_store = $edituser[0]['extra']['default_store'] ?? -1;
                                                    foreach($stores as $store){
                                                        echo '<option value="'.$store['id'].'"';
                                                        if($user_default_store == $store['id'])
                                                            echo ' selected ';
                                                        echo '>'.$store['store_name'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;">Extra Role:</label>
                                    <div class="col-md-12">
                                        <?php
                                            $user_role_extra_reflection = new ReflectionClass(BluelimeConfiguration::USER_ROLE_EXTRA);
                                            $user_role_extras = $user_role_extra_reflection->getConstants();
                                            foreach($user_role_extras as $role_extra){
                                                echo '
                                                <div class="form-check form-check-inline" style="display: inline-block; margin: 8px; padding: 5px;">
                                                    <input class="form-check-input" type="checkbox"  name="extra_role[]" value="'.$role_extra.'" id="extra_role_'.$role_extra.'">
                                                    <label class="form-check-label" for="extra_role_'.$role_extra.'">
                                                        '.ucfirst(strtolower(str_ireplace('_',' ',$role_extra))).'
                                                    </label>
                                                </div>
                                                ';
                                            }
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label" style="text-align: left;">Digital Signature:</label>
                                    <div class="col-md-11">
                                        <input type="file" name="digital_signature" value="">       
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row" >
                                <div class="col-sm-offset-3 col-sm-9" style="text-align: right;">
                                    <!--button type="reset" class="btn btn-danger">Reset</button-->
                                    <button type="button" onclick="return <?=@$_GET['edit']==1?"submituserform()":"checkusername()"?>" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        <?php echo form_close();?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
                        <style>
                            .user_row{
                                height: 52px;
                            }
                            .user_row:hover .print{
                                display: inline-block;
                            }
                            .print{
                                display: none;
                            }
                            .error_input{
                                background-color: red;
                            }
                        </style>

            <div class="col-sm-7 col-md-7">
                <div  class="panel panel-bd panel-form">
                <div class="panel-heading">
                    <div class="panel-title" >
                        <h4>User List</h4>
                    </div>
                </div>
                <div class="panel-body  table-responsive">
                    <table class="table table-striped" style="font-size: smaller;">
                        <tr style="font-weight: bold;">
                            <td>Full Name</td>
                            <td>Username</td>
                            <td>Role</td>
                            <td>Mobile</td>
                            <td width="150"></td>
                        </tr>
                        <?php 
                            foreach($users as $user):
                        ?>
                        <tr class="user_row" style="<?=$user['active'] == 0?'text-decoration: line-through red 2px;':''?>">
                            
                            <td><?=$user['name']?></td>
                            <td><?=$user['username']?></td>
                            <td><?=$user['role']?></td>
                            <td><?=$user['mobile']?></td>
                            
                            <td>
                            <label class="print switch"  title="<?=$user['active'] == 0?"Activate":"Deactivate"?>" data-toggle="tooltip" data-html="true">
                                    <?php 
                                    $user_status = "checked";
                                    if($user['active'] == 0){
                                        $user_status = "";
                                    }
                                    ?>
                                    <input onchange="changestatus(<?=$user['id']?>,<?=$user['active']?>)" id="is_active" name="is_active" type="checkbox" <?=$user_status?> >
                                    <span class="slider round"></span>
                                </label>
                                    <input id="user_is_active"  type="hidden" value="<?=intval($user['active'])?>" >
                            
                                <a href="admin?uid=<?=$user['id']?>&edit=<?=1?>">
                                    <button type="button" class="print fa fa-edit btn-primary btn-sm" aria-hidden="true"  title="Edit User" data-toggle="tooltip" data-html="true"></button>
                                </a>
                            <a onclick="show_reset_password_modal(<?=$user['id']?>,'<?=$user['name']?>')"><img style="cursor: pointer;" class="print" src="/img/reset_password.png" width="30px"  title="Reset Password" data-toggle="tooltip" data-html="true"></a>
                            </td>
                        </tr>
                        <?php 
                           endforeach;
                        ?>
                    </table>
                </div>

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

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title" >
                            <h4>User Log</h4>
                        </div>
                    </div>
                    <div class="panel-body  table-responsive">
                        <table width="100%" class="table table-striped table-bordered table-hover" style="font-size: smaller;">
                            <thead>
                                <tr>
                                <th>User</th>
                                <th>Login</th>
                                <th>Logout</th>
                                <th>Device</th>
                                <th>Ip Address</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($userLoginfo as $log) {  ?>
                                <tr>
                                <td><?=$log['name']?></td>
                                <td><?=$log['login_time']?></td>
                                <td><?=$log['logout_time']?></td>
                                <td><?=$log['agent_device']?></td>
                                <td><?=$log['ip_address']?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        </div>
            
    </section>
  
            
    

</div>

<div class="modal fade" id="reset_password" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    
                    <div class="modal-footer" text-align="center">
                        <input type="hidden" id="user_reset_id">
                        <p style="text-align: center;"><h5 class="modal-title">Password for <label id="user_reset_name"></label> will be set to <b style="color:red">0000</b></h5></p>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="resetpassword()">Reset</button>
                    </div>
                </div>
            </div>
</div>
<script>
    var default_store_roles = <?= BluelimeSystem::convert_array_to_string($default_store_roles) ?>;
    function role_is_choosed(selected_role){
        let default_store_div = $('#default_store_div');
        let role = selected_role.value;
        if(default_store_roles.includes(role))
            default_store_div.show();
        else
            default_store_div.hide();
    }

    function validate_user_form(){
        let form_user = document.getElementById('form_user');
        let selected_role = form_user.elements['role'].value;
        let selected_default_store = $('#default_store').val();

        if(default_store_roles.includes(selected_role)){
            if(selected_default_store == ''){
                alert('Default Store cannot be empty');
                return true;
            }
        }

        return true;
    }

    function checkusername()
    {
        $("#username").css("borderColor","");
        var username = $("#username").val().trim();
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/admin/getexistingusername",
            data: {'username':username},
            success: function(result){
                if(result){
                    //return alert(result);
                    $("#username").css("borderColor","red");
                    $("#username").focus();
                    toastr['error']("Username Exists!");
                    return false;
                    }
                else{
                    submituserform();
                }
                    
                }
                
                
        });
    }
    function submituserform(){
        $("#form_user").submit();
    }

    function show_reset_password_modal(user_id,user_name){
                    $("#reset_password").modal();
                    $("#user_reset_id").val(user_id);
                    $("#user_reset_name").html(user_name);
                }
    
    function resetpassword(){
        let user_reset_id = $('#user_reset_id').val();
        $.ajax({
            method: 'POST',
            url: "/hospital/admin/resetpassword",
            data: {user_id: user_reset_id},
            async: false,
            dataType: 'json',
            success: function(result) {
            if(result.status == "success"){
                toastr['success']('User Password has been Reset');
                //location = '/inventory/dispensary/';
                //$('tr#'+prescription_group_id).remove();
            }
            else{
                toastr['error'](result.message);
            }
        },
        error: function(jqXHR, status, error) {
            alert(error);
        }
            
        });
    }

function changestatus(user_id,active)
    {
        
        $.ajax({
            method: 'post',
            url: "<?php echo base_url(); ?>hospital/admin/changeuserstatus",
            data: {'active':active,'user_id':user_id},
            success: function(result){
                //return alert(parseInt(result));
                if(!result){
                    
                    toastr['success']("User Status Changed");
                    location.reload();
                    
                    }
               
                }
                
        });
    }

</script>
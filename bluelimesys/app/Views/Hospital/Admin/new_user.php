<?php
$users = $this->data['users'];
$user = $this->data['edituser'];
 //BluelimeSystem::show_and_die($user);
?>
<div class="content-wrapper user-management-page">

<!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="fa fa-users"></i>
        </div>
        <div class="header-title">
            <h1>User Management</h1>
                <small>Create accounts, assign access, and manage active users</small>
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
                        <h4><?=@$_GET['edit']==1 ? 'Edit User' : 'Create User'?></h4>
                        <p>Set identity details and permissions for this account.</p>
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
                                    <label class="control-label" for="full_name">Full name</label>
                                    <div>
                                        <input type="text" name="full_name" class="form-control" required value="<?=@$user[0]['name']?>" placeholder="Full Name"> 
                                        <input type="hidden" name="id" value="<?=@$user[0]['id']?>"> 
                                        <input type="hidden" name="edit" value="<?=@$_GET['edit']?>"> 
                                        
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="username">Username</label>
                                    <div>
                                        <input type="text" name="username" id="username" class="form-control" required value="<?=@$user[0]['username']?>" placeholder="Username"> 
                                        
                                    </div>
                                </div>

                                <div class="form-group" <?=@$_GET['edit']==1?"hidden":""?>>
                                    <label class="control-label" for="password">Temporary password</label>
                                    <div>
                                        <input type="password" name="password" required value="" class="form-control" > 
                                        
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="email">Email address</label>
                                    <div>
                                        <input type="text" name="email" required value="<?=@$user[0]['email']?>" class="form-control" placeholder="Email"> 
                                        
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="control-label" for="mobile">Mobile number</label>
                                    <div>
                                        <input type="text"  name="mobile" value="<?=@$user[0]['mobile']?>" class="form-control" placeholder="Mobile"> 
                                        
                                    </div>
                                </div>

                                <div class="form-group permission-section">
                                    <span class="section-label">Module access</span>
                                    <span class="section-help">Select every module this user is allowed to access.</span>
                                    <div>
                                        <?php
                                            $role_reflection = new ReflectionClass(BluelimeConfiguration::USER_ROLE);
                                            $roles = $role_reflection->getConstants();
                                            $user_role = $edituser[0]['role'] ?? '';
                                            $user_roles = $edituser[0]['extra']['roles'] ?? [$user_role];
                                            $user_roles = is_array($user_roles) ? $user_roles : [$user_role];
                                            $user_extra_roles = $edituser[0]['extra']['extra_role'] ?? [];
                                            $user_extra_roles = is_array($user_extra_roles) ? $user_extra_roles : [];
                                            if ($user_role !== '' && !in_array($user_role, $user_roles, true)) {
                                                array_unshift($user_roles, $user_role);
                                            }
                                            foreach($roles as $role){
                                                echo '
                                                <div class="role-option">
                                                    <input class="form-check-input" type="checkbox" name="roles[]" value="'.$role.'" id="role_'.$role.'" onclick="role_is_choosed()"';
                                                if(in_array($role, $user_roles, true))
                                                    echo ' checked ';
                                                echo '>
                                                    <label class="form-check-label" for="role_'.$role.'">
                                                        '. ucfirst(strtolower(str_replace('_', ' ', $role))).'
                                                    </label>
                                                </div>
                                                ';
                                            }
                                        ?>
                                    </div>
                                </div>
                                <div id="default_store_div" <?= (count(array_intersect($user_roles, $default_store_roles)) > 0) ? '' : 'hidden'?> >
                                    <div class="form-group">
                                        <label class="control-label" for="default_store">Default store</label>
                                        <div>
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
                                <div class="form-group permission-section">
                                    <span class="section-label">Additional permissions</span>
                                    <span class="section-help">Optional privileges that extend the selected module access.</span>
                                    <div>
                                        <?php
                                            $user_role_extra_reflection = new ReflectionClass(BluelimeConfiguration::USER_ROLE_EXTRA);
                                            $user_role_extras = $user_role_extra_reflection->getConstants();
                                            foreach($user_role_extras as $role_extra){
                                                echo '
                                                <div class="role-option">
                                                    <input class="form-check-input" type="checkbox"  name="extra_role[]" value="'.$role_extra.'" id="extra_role_'.$role_extra.'"';
                                                if(in_array($role_extra, $user_extra_roles, true))
                                                    echo ' checked ';
                                                echo '>
                                                    <label class="form-check-label" for="extra_role_'.$role_extra.'">
                                                        '.ucfirst(strtolower(str_ireplace('_',' ',$role_extra))).'
                                                    </label>
                                                </div>
                                                ';
                                            }
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group permission-section">
                                    <label class="control-label" for="digital_signature">Digital signature</label>
                                    <div class="signature-field">
                                        <input type="file" name="digital_signature" value="">       
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                    <!--button type="reset" class="btn btn-danger">Reset</button-->
                                    <button type="button" onclick="return <?=@$_GET['edit']==1?"submituserform()":"checkusername()"?>" class="btn btn-save"><i class="fa fa-check"></i> Save user</button>
                                </div>
                        <?php echo form_close();?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <div class="col-sm-8 col-md-8">
                <div  class="panel panel-bd panel-form">
                <div class="panel-heading">
                    <div class="panel-title" >
                        <h4>Users</h4>
                        <p id="user_count_text"><?=count($users)?> account<?=count($users) == 1 ? '' : 's'?> registered</p>
                    </div>
                </div>
                <div class="panel-body table-responsive">
                    <div class="user-filter-bar">
                        <input type="text" id="user_filter_search" class="form-control" placeholder="Search name, username or mobile" autocomplete="off">
                        <select id="user_filter_role" class="form-control">
                            <option value="">All roles</option>
                            <?php
                                $filter_role_reflection = new ReflectionClass(BluelimeConfiguration::USER_ROLE);
                                foreach($filter_role_reflection->getConstants() as $filter_role):
                            ?>
                                <option value="<?=htmlspecialchars($filter_role, ENT_QUOTES, 'UTF-8')?>"><?=ucfirst(strtolower(str_replace('_', ' ', $filter_role)))?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="user_filter_status" class="form-control">
                            <option value="">All statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <button type="button" id="user_filter_clear" class="btn btn-default">Clear</button>
                    </div>
                    <table class="table user-table">
                        <thead>
                            <tr>
                                <th>Full name</th>
                                <th>Username</th>
                                <th>Access</th>
                                <th>Signature</th>
                                <th>Mobile</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            foreach($users as $user):
                                $signature = $user['digital_signature'] ?? '';
                                $filter_user_roles = $user['extra']['roles'] ?? [$user['role']];
                                $filter_user_roles = is_array($filter_user_roles) ? $filter_user_roles : [$user['role']];
                                $signature_mime = '';
                                if (!empty($signature)) {
                                    $signature_mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($signature) ?: '';
                                    if (strpos($signature_mime, 'image/') !== 0) {
                                        $signature_mime = '';
                                    }
                                }
                        ?>
                        <tr class="user_row <?=$user['active'] == 0 ? 'inactive' : ''?>"
                            data-search="<?=htmlspecialchars(strtolower(($user['name'] ?? '').' '.($user['username'] ?? '').' '.($user['mobile'] ?? '')), ENT_QUOTES, 'UTF-8')?>"
                            data-roles="|<?=htmlspecialchars(implode('|', $filter_user_roles), ENT_QUOTES, 'UTF-8')?>|"
                            data-status="<?=$user['active'] == 0 ? 'inactive' : 'active'?>">
                            
                            <td><span class="user-name"><?=$user['name']?></span></td>
                            <td><?=$user['username']?></td>
                            <td>
                                <?php foreach(($user['extra']['roles'] ?? [$user['role']]) as $assigned_role): ?>
                                    <span class="role-badge"><?=ucfirst(strtolower(str_replace('_', ' ', $assigned_role)))?></span>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <?php if ($signature_mime !== ''): ?>
                                    <img class="signature-thumb" src="data:<?=$signature_mime?>;base64,<?=base64_encode($signature)?>" data-toggle="modal" data-target="#signature_preview" data-signature="<?=htmlspecialchars('data:' . $signature_mime . ';base64,' . base64_encode($signature), ENT_QUOTES, 'UTF-8')?>" title="View signature" alt="User signature">
                                <?php else: ?>
                                    <span class="signature-missing">Not added</span>
                                <?php endif; ?>
                            </td>
                            <td><?=$user['mobile']?></td>
                            
                            <td class="table-actions">
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
                            <button type="button" class="print reset-credentials-button" onclick="show_reset_modal(this)" data-user-id="<?=intval($user['id'])?>" data-user-name="<?=htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8')?>" title="Reset Password or PIN" data-toggle="tooltip" data-html="true"><i class="fa fa-refresh"></i></button>
                            </td>
                                </tr>
                        <?php 
                           endforeach;
                        ?>
                        <tr id="user_filter_empty" class="hidden">
                            <td colspan="6" class="text-center text-muted">No users match the selected filters.</td>
                        </tr>
                                </tbody>
                    </table>
                </div>

                </div>
            </div></div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bd user-log-panel">
                    <div class="panel-heading">
                        <div class="panel-title" >
                            <h4>Recent sign-ins</h4>
                            <p>Latest account activity and access history.</p>
                        </div>
                    </div>
                    <div class="panel-body  table-responsive">
                        <table width="100%" class="table table-striped table-bordered table-hover user-log-table">
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

<div class="modal fade reset-credentials-modal" id="reset_credentials" role="dialog" aria-labelledby="reset_credentials_title">
    <div class="modal-dialog modal-sm reset-credentials-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close reset-modal-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="reset-modal-icon"><i class="fa fa-refresh"></i></div>
                <h4 class="modal-title" id="reset_credentials_title">Reset credentials</h4>
                <p class="reset-modal-subtitle">Choose which access credential to reset.</p>
            </div>
            <div class="modal-body reset-credentials-message">
                <div class="reset-user-summary">
                    <span class="reset-user-label">Account</span>
                    <strong id="user_reset_name"></strong>
                </div>
                <p>Selected credential will be changed to</p>
                <strong class="reset-password-value">0000</strong>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="user_reset_id">
                <button type="button" class="btn reset-password-action" data-dismiss="modal" onclick="resetpassword()"><i class="fa fa-lock"></i> Reset password</button>
                <button type="button" class="btn reset-pin-action" data-dismiss="modal" onclick="resetpin()"><i class="fa fa-key"></i> Reset PIN</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade signature-preview-modal" id="signature_preview" role="dialog" aria-labelledby="signature_preview_title">
    <div class="modal-dialog modal-sm signature-preview-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="signature_preview_title">Digital signature</h4>
            </div>
            <div class="modal-body signature-preview-body">
                <img id="signature_preview_image" src="" alt="Digital signature" class="signature-preview-image">
            </div>
        </div>
    </div>
</div>
<script>
    var default_store_roles = <?= BluelimeSystem::convert_array_to_string($default_store_roles) ?>;
    function role_is_choosed(selected_role){
        let default_store_div = $('#default_store_div');
        let roles = Array.from(document.querySelectorAll('input[name="roles[]"]:checked')).map(input => input.value);
        if(roles.some(role => default_store_roles.includes(role)))
            default_store_div.show();
        else
            default_store_div.hide();
    }

    function validate_user_form(){
        let form_user = document.getElementById('form_user');
        let selected_roles = Array.from(form_user.querySelectorAll('input[name="roles[]"]:checked')).map(input => input.value);
        let selected_default_store = $('#default_store').val();

        if(selected_roles.length === 0){
            alert('Select at least one role');
            return false;
        }

        if(selected_roles.some(role => default_store_roles.includes(role))){
            if(selected_default_store == ''){
                alert('Default Store cannot be empty');
                return false;
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

    function show_reset_modal(button){
        $("#user_reset_id").val(button.getAttribute('data-user-id'));
        $("#user_reset_name").text(button.getAttribute('data-user-name') || 'this account');
        $("#reset_credentials").modal();
    }

    $('#signature_preview').on('show.bs.modal', function(event) {
        let signature = $(event.relatedTarget).data('signature');
        $('#signature_preview_image').attr('src', signature || '');
    });

    function applyUserFilters(){
        let search = $('#user_filter_search').val().trim().toLowerCase();
        let role = $('#user_filter_role').val();
        let status = $('#user_filter_status').val();
        let rows = $('.user-table tbody tr.user_row');
        let visible = 0;

        rows.each(function(){
            let row = $(this);
            let matchesSearch = search === '' || String(row.data('search')).indexOf(search) !== -1;
            let matchesRole = role === '' || String(row.attr('data-roles')).indexOf('|' + role + '|') !== -1;
            let matchesStatus = status === '' || row.data('status') === status;
            let show = matchesSearch && matchesRole && matchesStatus;
            row.toggle(show);
            if(show) visible++;
        });

        $('#user_filter_empty').toggleClass('hidden', visible > 0 || rows.length === 0);
        $('#user_count_text').text(visible === rows.length
            ? rows.length + (rows.length === 1 ? ' account registered' : ' accounts registered')
            : 'Showing ' + visible + ' of ' + rows.length + ' accounts');
    }

    $('#user_filter_search').on('input', applyUserFilters);
    $('#user_filter_role, #user_filter_status').on('change', applyUserFilters);
    $('#user_filter_clear').on('click', function(){
        $('#user_filter_search').val('');
        $('#user_filter_role').val('');
        $('#user_filter_status').val('');
        applyUserFilters();
    });
    
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

    function resetpin(){
        let user_reset_id = $('#user_reset_id').val();
        $.ajax({
            method: 'POST',
            url: "<?=base_url()?>hospital/admin/resetpin",
            data: {user_id: user_reset_id},
            dataType: 'json',
            success: function(result) {
                if(result.status == "success"){
                    toastr['success']('User PIN has been reset to 0000');
                }
                else{
                    toastr['error'](result.message || 'Unable to reset PIN');
                }
            },
            error: function(jqXHR, status, error) {
                toastr['error'](error || 'Unable to reset PIN');
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
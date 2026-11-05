<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="header-icon">
            <i class="pe-7s-world"></i>
        </div>
        <div class="header-title">
            <h1><?php echo lang(line: 'Admin'); ?></h1>
            <small>API Settings</small>
           
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-pd">
                    <!--div class="panel-heading">
                        <div class="panel-title">
                            <h4>API Settings</h4>
                        </div>
                    </div-->
                    <div class="panel-body">
                        <form action="/hospital/admin/api_setting" method="post">
                            <div class="row">
                                <div class="col-lg-1"><label for="api_id">Select API</label></div>
                                <div class="col-lg-3">
                                    <select class="form-control" id="api_id" name="api_id" required onchange="this.form.submit()">
                                        <option value="" title="Create new API"></option>
                                        <?php
                                            foreach($api_list as $api_group_index => $apis){
                                                echo '<optgroup label="'.$api_group_index.'">';
                                                foreach($apis as $api_name_index => $versions){
                                                    
                                                    foreach($versions as $version){
                                                        echo '<option value="'."$api_group_index,$api_name_index,$version".'" '.((($api_name_index==$api_name)&&($api_version==$version))?'selected':'').'>'.$api_name_index.' - '.$version.'</option>';
                                                    }
                                                }
                                                echo '</optgroup>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <hr>

                        <div class="row">
                            <div class="col-lg-2"><label for="api_group">API Group</label></div>
                            <div class="col-lg-3"><input type="text" id="api_group" name="api_group" class="form-control" maxlength="20" required readonly value="<?= $api_group ?>"></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2"><label for="api_name">API Name</label></div>
                            <div class="col-lg-3"><input type="text" id="api_name" name="api_name" class="form-control" maxlength="20" required readonly value="<?= $api_name ?>"></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2"><label for="api_version">API Version</label></div>
                            <div class="col-lg-3"><input type="text" id="api_version" name="api_version" class="form-control" maxlength="20" required readonly value="<?= $api_version ?>"></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2"><label>Enabled</label></div>
                            <div class="col-lg-3"><input type="checkbox" id="api_enabled" name="api_enabled" <?= ($api['active']??false)?'checked':'' ?>></div>
                        </div>
                        <?php
                            // $API_LIST = [
                            //     'FINANCE' => [
                            //         'V1' => [
                            //             'c02fb2e7eef8cc268eb4de927924ec4c_20250808' => [
                            //                 'description' => 'For credit payment request',
                            //                 'expiry_date' => NULL,
                            //                 'ip_start_with_access' => [
                            //                     '196.189' => self::GRANT_ACCESS,
                            //                     //'253.145' => self::DENY_ACCESS,
                            //                 ],
                            //                 'ip_match_access' => [
                            //                     '127.0.0.1' => self::GRANT_ACCESS,
                            //                     '192.168.1.6' => self::GRANT_ACCESS,
                            //                     '0.0.0.0' => self::DENY_ACCESS,
                            //                 ],
                            //             ]
                            //         ],
                            //     ],
                            // ];

                        ?>
                    </div>
                </div>
            </div>
            <div id="api_data">
                <?php
                    function print_api_data($data, $index){
                        echo '
                        <div class="api_data">
                    <div class="col-lg-4">
                        <div class="panel panel-bd" style="border: solid 1px #aaa;">
                            <div class="panel-body">
                                <a role="button" style="float: right; margin-top: -10px;" onclick="remove_api_data(this)"><i class="fa fa-trash color-red"></i></a>

                                <div class="form-group">
                                    <label for="api_key_'.$index.'">API Key:</label>
                                    <input type="text" class="form-control" id="api_key_'.$index.'" value="'.$data['api_key'].'">
                                </div>
                                <div class="form-group">
                                    <label for="api_enabled_'.$index.'">Enabled:</label>
                                    <input type="checkbox" id="api_enabled_'.$index.'" '.(($data['active']??false)?'checked':'').'>
                                </div>
                                <div class="form-group">
                                    <label for="api_description_'.$index.'">Description:</label>
                                    <input type="text" class="form-control" id="api_description_'.$index.'" value="'.$data['description'].'">
                                </div>
                                <div class="form-group">
                                    <label for="api_expiry_date_'.$index.'">Expiry Date:</label>
                                    <input type="date" class="form-control" id="api_expiry_date_'.$index.'" value="'.$data['expiry_date'].'">
                                </div>

                                <fieldset class="reset" style="border: solid 1px #ccc; border-radius: 5px;">
                                    <legend class="reset">IP Start With Access</legend>
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>IP Address</th>
                                                <th width="150">Access</th>
                                                <th width="20"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="api_ip_start_with_access">
                                            ';

                                            foreach($data['ip_start_with_access'] as $ip_access){
                                                echo '
                                                <tr>
                                                    <td><input type="text" class="form-control" value="'.$ip_access['ip_address'].'"></td>
                                                    <td>
                                                        <select class="form-control">
                                                            <option value="deny" '.(($ip_access['access']=='deny')?'selected':'').'>Deny</option>
                                                            <option value="grant" '.(($ip_access['access']=='grant')?'selected':'').'>Grant</option>
                                                        </select>
                                                    </td>
                                                    <td><a role="button" class="btn-sm btn-danger" onclick="$(this).closest(\'tr\').remove()"><i class="fa fa-trash"></i></a></td>
                                                </tr>';
                                            }
                        
                                        
                        echo '
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" style="padding: 0px 10px; text-align: right;"><a role="button" class="btn-sm btn-success" onclick="add_ip_start_with_access(this)">+</a></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </fieldset>

                                <br>

                                <fieldset class="reset" style="border: solid 1px #ccc; border-radius: 5px;">
                                    <legend class="reset">IP Exact Match Access</legend>
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>IP Address</th>
                                                <th width="150">Access</th>
                                                <th width="20"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="api_ip_exact_match_access">
                                        ';
                                        $any_ip_address_access = 'deny';
                                        foreach($data['ip_exact_match_access'] as $ip_access){
                                            if($ip_access['ip_address'] == '0.0.0.0'){
                                                $any_ip_address_access = $ip_access['access'];
                                                continue;
                                            }
                                            echo '
                                            <tr>
                                                <td><input type="text" class="form-control" value="'.$ip_access['ip_address'].'"></td>
                                                <td>
                                                    <select class="form-control">
                                                        <option value="deny" '.(($ip_access['access']=='deny')?'selected':'').'>Deny</option>
                                                        <option value="grant" '.(($ip_access['access']=='grant')?'selected':'').'>Grant</option>
                                                    </select>
                                                </td>
                                                <td><a role="button" class="btn-sm btn-danger" onclick="$(this).closest(\'tr\').remove()"><i class="fa fa-trash"></i></a></td>
                                            </tr>';
                                        }

                        echo '
                                            
                                        </tbody>
                                        <tfoot class="api_ip_exact_match_access">
                                            <tr>
                                                <td colspan="3" style="padding: 0px 10px; text-align: right;"><a role="button" class="btn-sm btn-success" onclick="add_ip_exact_match_access(this)">+</a></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control"  readonly value="0.0.0.0" title="Any IP address">
                                                </td>
                                                <td>
                                                    <select class="form-control">
                                                        <option value="deny" '.(($any_ip_address_access=='deny')?'selected':'').'>Deny</option>
                                                        <option value="grant" '.(($any_ip_address_access=='grant')?'selected':'').'>Grant</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>';
                    }
                    $index = 1;
                    $api_data = $api['data'] ?? [];
                    foreach($api_data as $data){
                        print_api_data($data, $index++);
                    }
                    if(empty($api_data)){
                        $data = [
                            'api_key' => '',
                            'description' => '',
                            'expiry_date' => '',
                            'ip_start_with_access' => [],
                            'ip_exact_match_access' => []
                        ];
                        print_api_data($data, $index++);
                    }
                ?>
            </div>
            <div class="col-lg-1" style="text-align: center;">
                <button class="btn btn-warning" style="margin-bottom: 2px;" onclick="add_api_data()">+</button><br>
                <button class="btn btn-success" onclick="save_api_data()">Save</button><br>
            </div>
            
        </div>
    </section>
</div>

<script>

    function add_ip_exact_match_access(element){
        var newRow = '<tr><td><input type="text" class="form-control" placeholder="IP Address"></td><td><select class="form-control"><option value="deny">Deny</option><option value="grant">Grant</option></select></td><td><a role="button" class="btn-sm btn-danger" onclick="$(this).closest(\'tr\').remove()"><i class="fa fa-trash"></i></a></td></tr>';
        $(element).closest('table').find('tbody').append(newRow);
    }

    function add_ip_start_with_access(element){
        var newRow = '<tr><td><input type="text" class="form-control" placeholder="IP Address"></td><td><select class="form-control"><option value="deny">Deny</option><option value="grant">Grant</option></select></td><td><a role="button" class="btn-sm btn-danger" onclick="$(this).closest(\'tr\').remove()"><i class="fa fa-trash"></i></a></td></tr>';
        $(element).closest('table').find('tbody').append(newRow);
    }

    function collect_api_data(){
        var apiData = [];
        $('.api_data').each(function(index, element){
            var api = {};
            api['api_key'] = $(element).find('input[id^="api_key_"]').val();
            api['description'] = $(element).find('input[id^="api_description_"]').val();
            api['expiry_date'] = $(element).find('input[id^="api_expiry_date_"]').val();
            api['active'] = $(element).find('input[id^="api_enabled_"]').is(':checked');
            api['ip_exact_match_access'] = [];
            $(element).find('.api_ip_exact_match_access tr').each(function(){
                var ipAccess = {};
                ipAccess['ip_address'] = $(this).find('td:eq(0) input').val();
                ipAccess['access'] = $(this).find('td:eq(1) select').val();
                if(ipAccess['ip_address'] && ipAccess['access']){
                    api['ip_exact_match_access'].push(ipAccess);
                }
            });

            $(element).find('.api_ip_exact_match_access_default tr').each(function(){
                var ipAccess = {};
                ipAccess['ip_address'] = $(this).find('td:eq(0) input').val();
                ipAccess['access'] = $(this).find('td:eq(1) select').val();
                if(ipAccess['ip_address'] && ipAccess['access']){
                    api['ip_exact_match_access'].push(ipAccess);
                }
            });
            
            api['ip_start_with_access'] = [];
            $(element).find('.api_ip_start_with_access tr').each(function(){
                var ipAccess = {};
                ipAccess['ip_address'] = $(this).find('td:eq(0) input').val();
                ipAccess['access'] = $(this).find('td:eq(1) select').val();
                if(ipAccess['ip_address'] && ipAccess['access']){
                    api['ip_start_with_access'].push(ipAccess);
                }
            });
            apiData.push(api);
        });
        return apiData;
    }

    var api_data_last_index = 2;

    function add_api_data(){
        api_data_last_index++;
        var newApiData = `
            <div class="api_data">
                    <div class="col-lg-4">
                        <div class="panel panel-bd" style="border: solid 1px #aaa;">
                            <div class="panel-body">
                                <a role="button" style="float: right; margin-top: -10px;" onclick="remove_api_data(this)"><i class="fa fa-trash color-red"></i></a>
                                <div class="form-group">
                                    <label for="api_key_` + api_data_last_index + `">API Key:</label>
                                    <input type="text" class="form-control" id="api_key_` + api_data_last_index + `">
                                </div>
                                <div class="form-group">
                                    <label for="api_enabled_` + api_data_last_index + `">Enabled:</label>
                                    <input type="checkbox" id="api_enabled_` + api_data_last_index + `"'>
                                </div>
                                <div class="form-group">
                                    <label for="api_description_` + api_data_last_index + `">Description:</label>
                                    <input type="text" class="form-control" id="api_description_` + api_data_last_index + `">
                                </div>
                                <div class="form-group">
                                    <label for="api_expiry_date_` + api_data_last_index + `">Expiry Date:</label>
                                    <input type="date" class="form-control" id="api_expiry_date_` + api_data_last_index + `">
                                </div>

                                <fieldset class="reset" style="border: solid 1px #ccc; border-radius: 5px;">
                                    <legend class="reset">IP Start With Access</legend>
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>IP Address</th>
                                                <th width="150">Access</th>
                                                <th width="20"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="api_ip_start_with_access">
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" style="padding: 0px 10px; text-align: right;"><a role="button" class="btn-sm btn-success" onclick="add_ip_start_with_access(this)">+</a></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Eg.<br>
                                                    <input type="text" class="form-control" readonly value="192.168">
                                                </td>
                                                <td><br>
                                                    <input type="text" class="form-control" readonly value="Deny">
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </fieldset>
                                <br>
                                <fieldset class="reset" style="border: solid 1px #ccc; border-radius: 5px;">
                                    <legend class="reset">IP Exact Match Access</legend>
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>IP Address</th>
                                                <th width="150">Access</th>
                                                <th width="20"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="api_ip_exact_match_access">
                                            
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" style="padding: 0px 10px; text-align: right;"><a role="button" class="btn-sm btn-success" onclick="add_ip_exact_match_access(this)">+</a></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control"  readonly value="0.0.0.0" title="Any IP address">
                                                </td>
                                                <td>
                                                    <select class="form-control">
                                                        <option value="deny">Deny</option>
                                                        <option value="grant">Grant</option>
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>`;
        $('#api_data').append(newApiData);
        $('.api_data').last().find('input[type="text"]').first().focus();
    }

    function remove_api_data(element){
        if(confirm("Are you sure you want to remove this API data?")){
            $(element).closest('.api_data').remove();
            let api_data_count = $('.api_data').length;
            if(api_data_count == 0)
                add_api_data();
        }
    }

    function save_api_data(){
        var api_group = $('#api_group').val();
        var api_name = $('#api_name').val();
        var api_version = $('#api_version').val();
        var api_enabled = $('#api_enabled').is(':checked') ? 1 : 0;

        var apiData = collect_api_data();
        if(apiData.length == 0){
            alert("No API data to save.");
            return;
        }

        var api_id = $('#api_id').val();
        if(!api_id){
            alert("Please select an API to save the data.");
            return;
        }

        $.ajax({
            url: '/hospital/admin/api_setting_save',
            type: 'POST',
            data: {
                api_group: api_group,
                api_name: api_name,
                api_version: api_version,
                api_enabled: api_enabled,
                api_data: JSON.stringify(apiData)
            },
            dataType: 'json',
            beforeSend: function() {
                // Optionally show a loading spinner or message
                console.log('Saving API data...');
            },
            success: function(response) {
                if(response.status === 'success'){
                    toastr['success']('API data saved successfully.');
                    //location.reload();
                } else {
                    toastr['error']('Error saving API data: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while saving API data.');
            },
            complete: function() {
                // Optionally hide the loading spinner or message
                console.log('API data save request completed.');
            }
        });
    }

</script>
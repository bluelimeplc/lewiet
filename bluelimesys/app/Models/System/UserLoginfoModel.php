<?php

namespace App\Models\System;

use CodeIgniter\Model;

class UserLoginfoModel extends Model
{
    protected $table      = 'sys_user_log';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['user_id', 'login_time', 'logout_time', 'agent_device', 'ip_address'];
}
<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table      = 'sys_notification';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['notification_type', 'status', 'description','link_id','link_type','user_id'];
}
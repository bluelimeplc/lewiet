<?php

namespace App\Models\POS;

use CodeIgniter\Model;

class POSMachineModel extends Model
{
    protected $table      = 'pos_machine';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['machine_name', 'type', 'extra', 'active'];

    protected function change_model_items_to_array(array $data)
    {
        $columns = ['extra'];
        $data = \BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $allowCallbacks = true;
    protected $afterFind      = ['change_model_items_to_array'];
}
<?php

namespace App\Models\Inventory;

use CodeIgniter\Model;

class FinanceMaintainModel extends Model
{
    protected $table      = 'inventory_finance_maintain';
    protected $primaryKey = 'id';
 
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['department', 'type' , 'data'];

    protected function change_model_items_to_array(array $data)
    {
        $columns = ['data'];
        $data = \BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = ['change_model_items_to_array'];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

}
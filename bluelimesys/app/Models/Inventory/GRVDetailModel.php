<?php

namespace App\Models\Inventory;

use CodeIgniter\Model;

class GRVDetailModel extends Model
{
    protected $table      = 'inventory_grv_detail';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['grv_id','item_id', 'item_detail', 'unit_price', 'quantity', 'manufactured_date', 'expiry_date', 'batch_number', 'tax','average_cost'];

    protected function change_model_items_to_array(array $data)
    {
        $columns = ['item_detail'];
        $data = \BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }

    protected function round_model_item_currency(array $data)
    {
        $columns = ['unit_price', 'average_cost', 'tax', 'quantity'];
        $data = \BluelimeSystem::round_model_item_currency($columns, $data);
        return $data;
    }

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    
    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['round_model_item_currency'];
    protected $beforeInsertBatch   = ['round_model_item_currency'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['round_model_item_currency'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = ['change_model_items_to_array'];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

}
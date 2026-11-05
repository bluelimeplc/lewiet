<?php

namespace App\Models\Inventory;

use CodeIgniter\Model;

class SalesMainModel extends Model
{
    protected $table      = 'inventory_sales_main';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['sales_date','fs_number','patient_id', 'patient_name', 'prescription_id', 'sales_type', 'store_id', 'registered_by', 'tin_number', 'company_id', 'company_name', 'credit_payment_request_id', 'visit_id', 'is_recieved', 'recieved_by', 'recieved_date', 'pos_bill_id','sent_to_pos','compounding_status','compounding_amharic_direction','compounding_done_by'];

    protected function change_model_items_to_array(array $data)
    {
        $columns = [];
        $data = \BluelimeSystem::change_model_items_to_array($columns, $data);
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
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = ['change_model_items_to_array'];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

}
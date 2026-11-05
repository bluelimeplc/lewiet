<?php

namespace App\Models\Inventory;

use BluelimeConfiguration;
use BluelimeSystem;
use CodeIgniter\Model;

class GRVMainModel extends Model
{
    protected $table      = 'inventory_grv_main';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['grv_code','recieved_date','description', 'store_id', 'supplier_id', 'invoice_no', 'recieved_by', 'fs_no', 'machine_no','receipt_date','receipt_date_gc','is_ethiopian_calander','is_manual_receipt', 'status', 'scanned_documents', 'cpv'];

    protected function change_model_items_to_array(array $data)
    {
        $columns = [];
        $data = \BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }

    protected function delete_my_details(array $data){
        $id_list = [...($data['id']??[-1])];
        $gRVDetailModel = model('Inventory/GRVDetailModel');
        $transactionModel = model('Inventory/TransactionModel');

        $gRVDetailModel->whereIn('grv_id',$id_list)->delete();
        $transactionModel->whereIn('transaction_reference',$id_list)->whereIn('transaction_type',[BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::RECEIVE])->delete();
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
    protected $afterFind      = [];
    protected $beforeDelete   = ['delete_my_details'];
    protected $afterDelete    = [];
}
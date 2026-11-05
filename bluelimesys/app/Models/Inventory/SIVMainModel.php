<?php

namespace App\Models\Inventory;

use CodeIgniter\Model;

class SIVMainModel extends Model
{
    protected $table      = 'inventory_siv_main';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['siv_code','srv_id', 'issued_by', 'issuing_store', 'recieved_by', 'requesting_store', 'description','status'];

    protected function change_model_items_to_array(array $data)
    {
        $columns = [];
        $data = \BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }

    protected function delete_my_details(array $data){
        $id_list = [...($data['id']??[-1])];
        $sivDetailModel = model('Inventory/SIVDetailModel');
        $srvMainModel = model('Inventory/SRVMainModel');
        $transactionModel = model('Inventory/TransactionModel');

        $siv_main = $this->whereIn('id',$id_list)->findAll()[0]??[];
        if(!empty($siv_main))
            $srvMainModel->update($siv_main['srv_id'],['is_issued' => 0]);
        
        $sivDetailModel->whereIn('siv_id',$id_list)->delete();
        $transactionModel->whereIn('transaction_reference',$id_list)->whereIn('transaction_type',[\BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::RECEIVE_INTERNAL,\BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::DISPENSE_INTERNAL])->delete();
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
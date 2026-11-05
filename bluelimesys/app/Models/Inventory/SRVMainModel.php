<?php

namespace App\Models\Inventory;

use CodeIgniter\Model;

class SRVMainModel extends Model
{
    protected $table      = 'inventory_srv_main';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['srv_code','request_date','description', 'requested_by', 'requesting_store', 'requested_store', 'is_issued'];

    protected function change_model_items_to_array(array $data)
    {
        $columns = [];
        $data = \BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }

    protected function delete_my_details(array $data){
        $id_list = [...($data['id']??[-1])];
        $srvDetailModel = model('Inventory/SRVDetailModel');
        $srvDetailModel->whereIn('srv_id',$id_list)->delete();
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
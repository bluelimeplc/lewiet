<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class PrescriptionAutoFillModel extends Model
{
    protected $table      = 'hospital_prescription_auto_fill';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['drug_id', 'drug_name', 'age_from','age_to', 'remark', 'route', 'direction', 'nod', 'registered_by', 'deleted_by'];
    
    protected $useSoftDeletes = true;
    protected function change_model_items_to_array(array $data)
    {
        $columns = [];
        $data = \BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }

    protected function add_deleted_by(array $data){
        $logged_user = \BluelimeAuthentication::get_logged_user_data();
        $user_id = $logged_user['id'] ?? -1;
        $id_list = [...($data['id']??[-1])];
        $this->whereIn('id',$id_list)->update(row:['deleted_by'=>$user_id]);
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
    protected $beforeDelete   = ['add_deleted_by'];
    protected $afterDelete    = [];
}

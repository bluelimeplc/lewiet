<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class NurseHistoryModel extends Model
{
    protected $table      = 'hospital_nurse_history';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    

    protected $allowedFields = [
        'patient_id','visit_id', 'chief_complaints', 'past_medical_history', 'hpi','diagnosis', 'medication','procedure','post_procedure_diagnosis','registered_date'
    ];

    protected function change_model_items_to_array(array $data){
        $columns = ['chief_complaints','past_medical_history', 'hpi','diagnosis', 'medication','procedure','post_procedure_diagnosis'];
        $data = \BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }

    public function get_allowed_fields(){
        return $this->allowedFields;
    }

    protected $useSoftDeletes = true;
    
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

    // Callbacks

}
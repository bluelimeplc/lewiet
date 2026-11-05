<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class VisitEncounterModel extends Model
{
    protected $table      = 'hospital_visit_encounter';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    

    protected $allowedFields = [
        'visit_id', 'chief_complaints', 'physical_finding', 'past_medical_history', 'hpi','diagnosis', 'medication','procedure','post_procedure_diagnosis','post_treatment','registered_date','patient_id','patient_name','doctor_id','doctor_name'
    ];

    protected function change_model_items_to_array(array $data){
        $columns = ['chief_complaints','physical_finding','past_medical_history', 'hpi','diagnosis', 'medication','procedure','post_procedure_diagnosis','post_treatment'];
        $data = \BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }

    public function get_allowed_fields(){
        return $this->allowedFields;
    }

    // Callbacks
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
}
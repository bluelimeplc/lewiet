<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class CompoundingPrescriptionMainModel extends Model
{
    protected $table      = 'hospital_compounding_prescription_main';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    

    protected $allowedFields = ['visit_id', 'prescription_group_id', 'patient_detail', 'created_by', 'created_by_name', 'is_sold','is_void', 'external_detail', 'route', 'direction', 'no_of_days', 'description', 'company_id', 'company_name','hide'];

    protected $useSoftDeletes = true;
    protected function change_model_items_to_array(array $data)
    {
        $columns = ['patient_detail', 'external_detail'];
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
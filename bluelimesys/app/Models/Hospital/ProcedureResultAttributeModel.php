<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class ProcedureResultAttributeModel extends Model
{
    protected $table      = 'hospital_procedure_attributes';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['procedure_id','procedure_name','attribute_name', 'attribute_result_options', 'input_type', 'is_required'];
    protected function change_model_items_to_array(array $data)
    {
        $columns = ['attribute_result_options'];
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
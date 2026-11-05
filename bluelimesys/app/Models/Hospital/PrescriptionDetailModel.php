<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class PrescriptionDetailModel extends Model
{
    protected $table      = 'hospital_prescription_detail';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
     protected $useSoftDeletes = true;

    protected $allowedFields = ['prescription_id','drug_id', 'drug_detail', 'dose', 'route', 'direction', 
                                'number_of_days', 'custom_direction'];

    protected function change_model_items_to_array(array $data)
    {
        $columns = ['drug_detail'];
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


<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class BiopsyResultModel extends Model
{
    protected $table      = 'hospital_biopsy_result';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['patient_id','order_id', 'patient_name', 'sex', 'age', 'mobile', 'biopsy_result', 'doctor_id', 
                                'doctor_name','revision','micrscopic_image'
    ];

    protected $useSoftDeletes = true;
    protected function change_model_items_to_array(array $data)
    {
        $columns = ['biopsy_result'];
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
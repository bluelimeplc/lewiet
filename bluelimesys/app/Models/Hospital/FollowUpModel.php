<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class FollowUpModel extends Model
{
    protected $table      = 'hospital_follow_up';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['patient_id','visit_id', 'patient_name', 'mobile', 'company_id', 'company_name', 'remark', 'selected_skin', 'selected_teeth', 'treatment_plans', 'date_created', 'doctor_id', 'doctor_name'
    ];

    protected $useSoftDeletes = true;
    protected function change_model_items_to_array(array $data)
    {
        $columns = ['treatment_plans','selected_skin'];
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

    public function search_follow_up(string $key)
    {
        $keys = explode(' ', $key);
       
        if(!is_array($keys))
            $keys = [];
        
        $keys = $this->db->escapeLikeString($keys);
       
        $where = "";
        foreach($keys as $key)
        {
            $key = trim($key);
            if($key == '')
                continue;
            
            if($where != '')
                $where .= ' AND ';
            $mobile = intval($key);
            $patient_id = intval($key);
            if(($patient_id > 0) && (strlen($key) == 5))
                $this->like('patient_id',$patient_id); //$where .= "(patient_id LIKE '%$patient_id%')";
            elseif($mobile > 0)
                $this->like('mobile',$mobile); //$where .= "(mobile LIKE '%$mobile%')";
            else
                $this->like('patient_name',$key); //$where .= "(patient_name LIKE '%$key%')";
        }
        
        $results = $this->findAll();
        return $results;
    }
}
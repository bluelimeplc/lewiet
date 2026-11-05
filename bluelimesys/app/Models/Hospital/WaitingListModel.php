<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class WaitingListModel extends Model
{
    protected $table      = 'hospital_waiting_list';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['patient_id','doctor_id', 'procedure_name', 
                                'estimated_date', 'full_name','registered_date',
                                'registered_by','registered_by_name','doctor_name',
                                'status','mobile','procedure_id', 'priority_reason', 'priority_level'];
    

    public function search_by_patient(string $key)
    {
        $keys = explode(' ', $key);
        
        if(!is_array($keys))
            $keys = [];
        
        $keys = $this->db->escapeLikeString($keys);
        
        $where = 'status = 0';
        foreach($keys as $key)
        {
            $key = trim($key);
            if($key == '')
                continue;
            
            if($where != '')
                $where .= ' AND ';
            $mobile = intval($key);
            $patient_id = intval($key);
            if($mobile > 0)
            $where .= "(mobile LIKE '%$mobile%' ESCAPE '!' OR patient_id LIKE '%$patient_id%' ESCAPE '!')";
            else
                $where .= "(full_name LIKE '%$key%')";
        }

        if($where != '')
            $where = " WHERE $where ";

        $query_string = "SELECT * FROM " . $this->table . " $where ORDER BY priority_level DESC, doctor_id, id ASC LIMIT 0,200";


        //\BluelimeSystem::show_and_die($query_string);
        
        $query = $this->db->query($query_string);

        $results = $query->getResult('array');

        //\BluelimeSystem::show_and_die($query_string);
        
        return $results;
        
    }

    protected $useSoftDeletes = true;
    protected function change_model_items_to_array(array $data)
    {
        $columns = [];
        $data = \BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }
    public function patientinwaitinglist($patient_id,$doctor_id) {  

        $result = $this->where('patient_id',$patient_id)
                        ->where('doctor_id',$doctor_id)
                        ->where('status',0)
                        ->findAll();
                         //\BluelimeSystem::show_and_die($result);     
        return $result;
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
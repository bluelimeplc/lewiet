<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class PrescriptionModel extends Model
{
    protected $table      = 'hospital_prescriptions';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['registered_by', 'registered_by_name', 'registered_date','patient_id','patient_name','visit_id', 'prescription_group_id', 'mobile','is_custom', 'is_sold', 'is_void', 'company_id', 'company_name','hide'];


    public function search_by_patient(string $key)
    {
        $keys = explode(' ', $key);
        
        if(!is_array($keys))
            $keys = [];
        
        $keys = $this->db->escapeLikeString($keys);
        
        $where = '';
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
                $where .= "(patient_name LIKE '%$key%')";
        }

        if(trim($where) != '')
            $where .= ' AND ';
        $where .= ' deleted_at IS NULL ';

        if($where != '')
            $where = " WHERE $where ";

        $query_string = "SELECT * FROM " . $this->table . " $where ORDER BY registered_date  DESC LIMIT 0,".\BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST;

        //\BluelimeSystem::show_and_die($query_string);
        
        
        $query = $this->db->query($query_string);

        $results = $query->getResult('array');

        //\BluelimeSystem::show_and_die($query_string);
        
        return $results;
        
    }

    public function search_prescription_for_dispensary(string $key)
    {
        $keys = explode(' ', $key);
        
        if(!is_array($keys))
            $keys = [];
        
        $keys = $this->db->escapeLikeString($keys);
        
        $where = '';
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
                $where .= "(patient_name LIKE '%$key%')";
        }

        if($where != '')
            $where = " WHERE  $where ";
        else
            $where = " WHERE  (is_sold = 0) AND (hide = 0) ";

        if(trim($where) != '')
            $where .= ' AND ';
        $where .= ' deleted_at IS NULL ';

        $query_string = "SELECT * FROM " . $this->table . " $where ORDER BY registered_date  DESC LIMIT 0,".\BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST;

        //\BluelimeSystem::show_and_die($query_string);
        
        
        $query = $this->db->query($query_string);

        $results = $query->getResult('array');

        //\BluelimeSystem::show_and_die($query_string);
        
        return $results;
        
    }

    protected function change_model_items_to_array(array $data)
    {
        $columns = [];
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


<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class OrderDetailModel extends Model
{
    protected $table      = 'hospital_order_detail';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
                                'order_main_id', 'ordered_service_id', 'ordered_by', 'order_comment', 'bill_detail_id', 
                                'sample_taken', 'sample_taken_date','sample_type','sample_taken_from','sample_taken_by',
                                'result_registered','result_date','result_registered_by','payment_id'];
    public function search(string $key)
    {

        $key = $this->db->escapeLikeString($key);
        $query_string = "SELECT * FROM " . $this->table . " WHERE order_date LIKE '%$key%'";

        $query = $this->db->query($query_string);

        $results = $query->getResult('array');
        
        return $results;
        
    }

    public function search_order(string $key)
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
            if(($patient_id > 0) && (strlen($key) == 5))
                $where .= "(id LIKE '%$patient_id%' ESCAPE '!')";
            elseif($mobile > 0)
                $where .= "(mobile LIKE '%$mobile%' ESCAPE '!')";
            else
                $where .= "(full_name LIKE '%$key%')";
        }

        if($where != '')
            $where = " WHERE $where ";

        $query_string = "SELECT * FROM " . $this->table . " $where ORDER BY id DESC LIMIT 0,20";


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
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
}
<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class PatientDocumentModel extends Model
{
    protected $table      = 'hospital_patient_document';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['patient_id','patient_name', 'mobile', 
                                'registered_by', 'registered_by_name','registered_date',
                                'visit_id','temlate_id','template_name',
                                'template_content','document_signature'];
    

    public function search_by_patient(string $key)
    {
        $keys = explode(' ', $key);
        
        if(!is_array($keys))
            $keys = [];
        
        $keys = $this->db->escapeLikeString($keys);
        
        $where = 'status = 1';
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

        $query_string = "SELECT * FROM " . $this->table . " $where ORDER BY estimated_date  DESC LIMIT 0,20";


        //\BluelimeSystem::show_and_die($query_string);
        
        $query = $this->db->query($query_string);

        $results = $query->getResult('array');

        //\BluelimeSystem::show_and_die($query_string);
        
        return $results;
        
    }
    
}
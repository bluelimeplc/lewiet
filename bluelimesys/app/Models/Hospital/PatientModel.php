<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table      = 'hospital_patients';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $useSoftDeletes = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $afterFind = ['change_drugs_taking_to_array'];

    protected $allowedFields = [
        'card_location','first_name', 'middle_name', 'last_name', 'date_of_birth', 'sex', 'address', 'subcity', 
        'woreda', 'house_no', 'mobile','email', 'emergency_contact_name','emergency_mobile', 'ragistered_date', 
        'company_id','company_name', 'blood_pressure', 'diabetics','bleeding_tendency', 'drugs_taking', 'allergy', 
        'pregnancy', 'blood_group', 'cardiac_problem','other_diseases', 'registered_by','registered_by_name','discarded_by','attached_old_card','survey'];
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
        protected $beforeDelete   = [];
        protected $afterDelete    = [];
    
        protected function change_drugs_taking_to_array(array $data)
    {
        $columns = ['drugs_taking', 'allergy', 'other_diseases'];
        $data = \BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }
    
    public function search(string $key)
    {

        $key = $this->db->escapeLikeString($key);
        $query_string = "SELECT * FROM " . $this->table . " WHERE first_name LIKE '%$key%'";

        $query = $this->db->query($query_string);

        $results = $query->getResult('array');
        
        return $results;
        
    }

    /*
|------------------------------------------------
|  get_all_patient form patient_tbl
|------------------------------------------------
*/

    public function search_patient(string $key)
    {
        $keys = explode(' ', $key);
       
        if(!is_array($keys))
            $keys = [];
        
        $keys = $this->db->escapeLikeString($keys);
       
        $where = 'deleted_at IS NULL';
        foreach($keys as $key)
        {
            $key = trim($key);
            if($key == '')
                continue;
            
            if($where != '')
                $where .= ' AND ( FALSE ';
            $mobile = intval($key);
            $patient_id = intval($key);
            if($patient_id > 0)
                $where .= " OR (id LIKE '%$patient_id%' ESCAPE '!') ";
            if($mobile > 0)
                $where .= " OR (mobile LIKE '%$mobile%' ESCAPE '!') ";
            else
                $where .= " OR (first_name LIKE '%$key%' OR middle_name LIKE '%$key%' OR last_name LIKE '%$key%')";
            
            if($where != '')
                $where .= ' ) ';
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


    public function get_max_id(): int
    {
        $query_string = "SELECT MAX(id) as max_id FROM " . $this->table;
        $query = $this->db->query($query_string);
        $results = $query->getResult('array');
        
        $max_id = 0;
        if(!empty($results))
            $max_id = $results[0]['max_id'];
        
        return intval($max_id);
    }

    public function select_patient_by_id(string $key)
    {
        $key = $this->db->escapeLikeString($key);
        $query_string = "SELECT first_name as full_name FROM " . $this->table . " WHERE id = '$key'";
        $query = $this->db->query($query_string);
        $results = $query->getResult('array');        
        
        $full_name = "";
        if(!empty($results))
            $full_name = $results[0]['full_name'];
        
        return $full_name;
    }

}
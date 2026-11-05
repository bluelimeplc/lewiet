<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table      = 'hospital_company';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = [
        'company_name','tin_number','description','is_active','registered_by','registered_date','registered_by_name','is_cash','unpaid_visit_days'
    ];

    protected $useSoftDeletes = true;
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


    public function search(string $key) : array
    {

        $key = $this->db->escapeLikeString($key);
        $query_string = "SELECT * FROM " . $this->table . " WHERE company_name LIKE '%$key%'";

        $query = $this->db->query($query_string);

        $results = $query->getResult('array');
        
        return $results;
    }
    
    /*public function select_company_by_id(string $key)
    {

        $key = $this->db->escapeLikeString($key);
        $query_string = "SELECT company_name FROM " . $this->table . " WHERE id = '$key'";
        $query = $this->db->query($query_string);
        $results = $query->getResult('array');        
        
        $company_name = "";
        if(!empty($results))
            $company_name = $results[0]['company_name'];
        
        return $company_name;
    }
    */


    

}
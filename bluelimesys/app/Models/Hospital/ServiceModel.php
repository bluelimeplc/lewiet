<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class ServiceModel extends Model
{
    protected $table      = 'hospital_services';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = [
        'service_name','tag','service_type','service_code','measurement','is_active','registered_by','registered_date','registered_by_name','gl_account'
    ];
    public function search(string $key)
    {

        $key = $this->db->escapeLikeString($key);
        $query_string = "SELECT * FROM " . $this->table . " WHERE service_name LIKE '%$key%'";

        $query = $this->db->query($query_string);

        $results = $query->getResult('array');
        
        return $results;
        
    }

  
    public function select_service_by_id(string $key)
    {

        $key = $this->db->escapeLikeString($key);
        $query_string = "SELECT `service_name` FROM " . $this->table . " WHERE id = '$key'";
        $query = $this->db->query($query_string);
        $results = $query->getResult('array');        
        
        $service_name = "";
        if(!empty($results))
            $service_name = $results[0]['service_name'];
        
        return $service_name;
    }

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

}
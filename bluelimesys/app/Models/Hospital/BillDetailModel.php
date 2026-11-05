<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class BillDetailModel extends Model
{
    protected $table      = 'hospital_bill_details';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['bill_id','service_id', 'service_name','service_type', 
                                'bill_quantity', 'bill_amount','bill_discount',
                                'bill_date','service_status'
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

    

    public function get_max_id(): int
    {
        //I simplified like this ;) 

        $result = $this->findColumn('MAX(id)');
        $max_id = intval($result[0]);
        return $max_id;
        
        
        /*
        $query_string = "SELECT MAX(id) as max_id FROM " . $this->table;
        $query = $this->db->query($query_string);
        $results = $query->getResult('array');
        
        $max_id = 0;
        if(!empty($results))
            $max_id = $results[0]['max_id'];
        
        return intval($max_id);
        */
    }

 

}
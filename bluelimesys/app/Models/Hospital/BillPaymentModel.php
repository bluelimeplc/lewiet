<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class BillPaymentModel extends Model
{
    protected $table      = 'hospital_bill_payments';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['bill_id', 'payed_amount', 'payed_date',
                                'payed_discount', 'registered_by','registered_by_name','patient_name',
                                'patient_id','company_id','company_name','payment_type','mobile','due','tin','fs_number','payment_mode'
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
        $result = $this->findColumn('MAX(id)');
        $max_id = intval($result[0]);
        return $max_id;
    }
    public function check_open_bill($bill_id)
    {
        $result = $this->where('bill_id', $bill_id)->findColumn('COUNT(id)');
        
        return $result[0];

    }
    public function searchrecievedbills(string $key)
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
            $where = " WHERE $where  ";

        $query_string = "SELECT * FROM " . $this->table . " $where ORDER BY id DESC LIMIT 0,20";
       
        $query = $this->db->query($query_string);

        $results = $query->getResult('array');
        
        return $results;
        
    }
  

    

}
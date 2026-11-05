<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class LabRequestModel extends Model
{
    protected $table      = 'hospital_lab_requests';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['doctor_id', 'patient_id', 'company_name', 'company_id', 'description', 'upper_tray', 
                                'lower_tray', 'date_impression','delivery_date','status','special_wish','type','registered_by_name','registered_by','visit_id','patient_name','mobile','teeth_selected','delivered_date','paid_amount','paid_date'
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

        if($where != '')
            $where = " WHERE $where ";

        $query_string = "SELECT * FROM " . $this->table . " $where ORDER BY date_impression  DESC LIMIT 0,20";

        //\BluelimeSystem::show_and_die($query_string);
        
        
        $query = $this->db->query($query_string);

        $results = $query->getResult('array');

        //\BluelimeSystem::show_and_die($query_string);
        
        return $results;
        
    }
    public function search_by_patient_followup(string $key)
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
            $where = " WHERE $where ";

        $query_string = "SELECT * FROM " . $this->table . " $where ORDER BY delivery_date  DESC LIMIT 0,20";

        //\BluelimeSystem::show_and_die($query_string);
        
        
        $query = $this->db->query($query_string);

        $results = $query->getResult('array');

        //\BluelimeSystem::show_and_die($query_string);
        
        return $results;
        
    }
    public function get_tomorrow_delivery(){
        $companyModel = model('App\Models\CompanyModel');
        
        $tomorrow = date_create(date('Y-m-d'));
        date_add($tomorrow,date_interval_create_from_date_string("1 day"));
        $tomorrow = date_format($tomorrow,"Y-m-d");
        $labrequests = $this
                        ->where('delivery_date', $tomorrow)
                        ->findAll();
        foreach($labrequests as $index => $labrequest){
            $labrequests[$index]['company_mobile'] = $companyModel->where('id',$labrequest['company_id'])->findColumn('description')[0];
        }
            
        
        return $labrequests;
    }

    
    
}
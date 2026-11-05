<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class OrderMainModel extends Model
{
    protected $table      = 'hospital_order_main';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    

    protected $allowedFields = [
                                'patient_id','patient_name','mobile','order_comment','result_remark', 'sex', 'date_of_birth', 'visit_id', 'ordered_by','registered_by', 'bill_id', 'order_service_type', 
                                'is_external', 'order_status','sample_collected_date','sample_collected_by','sample_sent_date','sample_sent_by','slide_recieved_date','slide_recieved_by','additional_info','result_status','result_approved_by','order_by_name',
                                'result_approved_date','scanned_documents','hide'];
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
        $columns = ['additional_info'];
        $data = \BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }

    protected function delete_my_details(array $data){
        $id_list = [...($data['id']??[-1])];
        $orderDetailModel = model('Hospital/OrderDetailModel');
        $orderDetailModel->whereIn('order_main_id',$id_list)->delete();
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
    protected $afterDelete    = ['delete_my_details'];
    
}
<?php

namespace App\Models\Hospital;

use BluelimeSystem;
use CodeIgniter\Model;

class BillModel extends Model
{
    protected $table      = 'hospital_bills';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    // protected $useSoftDeletes = true;

    protected $allowedFields = ['patient_id','visit_id','order_id', 'billed_date', 'bill_type', 
                                'registered_by','patient_name','registered_by_name',
                                'company_id', 'company_name','bill_status','mobile',
                                'payment_request_id', 'pos_bill_id', 'sent_to_pos', 'fs_number', 'payed_date','created_by'
    ];

    protected $useSoftDeletes = true;
    protected function change_model_items_to_array(array $data)
    {
        $columns = [];
        $data = BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }

    protected function delete_my_details(array $data){
        $id_list = [...($data['id']??[-1])];
        $billDetailModel = model('Hospital/BillDetailModel');
        $orderMainModel = model('Hospital/OrderMainModel');

        $billDetailModel->whereIn('bill_id',$id_list)->delete();
        $orderMainModel->whereIn('bill_id',$id_list)->delete();
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
    protected $afterDelete    = ['delete_my_details'];

                                

    public function searchpendingbills(string $key)
    {
        
        $keys = explode(' ', $key);
        
        if(!is_array($keys))
            $keys = [];
        
        $keys = $this->db->escapeLikeString($keys);
    
        $where = ' ((bill_status ='.\BluelimeConfiguration::BILL_PENDING.') OR (bill_status ='.\BluelimeConfiguration::BILL_OPEN.') OR (bill_status ='.\BluelimeConfiguration::BILL_SENT_TO_POS.')) ';
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

        $query_string = "SELECT id, patient_id, patient_name, company_name, billed_date,bill_status FROM " . $this->table . " $where ORDER BY id DESC LIMIT 0,20";
       
        $query = $this->db->query($query_string);

        $results = $query->getResult('array');
        
        return $results;
        
    }
    public function searchallbills(string $key)
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

        $query_string = "SELECT id, patient_id, patient_name, company_name, billed_date FROM " . $this->table . " $where ORDER BY id DESC LIMIT 0,20";
       
        $query = $this->db->query($query_string);

        $results = $query->getResult('array');
        
        return $results;
        
    }

    public function findAllWithAmount(){
        $result = $this->findAll();

        $billDetailModel = model("Hospital/BillDetailModel");

        foreach($result as $index => $row){
            $billDetail = $billDetailModel->where('bill_id',$row['id'])->where('service_status',0)->groupBy('bill_id')->findColumn('SUM((bill_amount*bill_quantity)-((bill_amount*bill_quantity)*bill_discount/100))');
            $result[$index]['total_amount'] = $billDetail[0];
        }

        return $result;
    }
    public function findAllWithDueAmount(){
        $result = $this->findAll();

        $viewBillAndPaymentModel = model("Hospital/ViewBillAndPaymentModel");

        foreach($result as $index => $row){
            $billDetail = $viewBillAndPaymentModel->where('bill_id',$row['id'])->groupBy('bill_id')->findColumn('SUM(amount)');
            $result[$index]['due_amount'] = $billDetail[0];
        }

        return $result;
    }
    
    public function get_max_id(): int
    {
        $result = $this->findColumn('MAX(id)');
        $max_id = intval($result[0]);
        return $max_id;
    }

}
<?php

namespace App\Models\Hospital;

use BluelimeSystem;
use CodeIgniter\Model;

class VisitModel extends Model
{
    protected $table      = 'hospital_visits';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = [
                                'patient_id', 'doctor_id', 'visit_date', 'visit_status', 'registered_by', 
                                'bill_id', 'appointment_id','patient_name','doctor_name','registered_by_name',
                                'service_id', 'service_name','treatment_plans', 'related_visit', 'follow_up_id', 'extra','queue_no','visit_closed_date','mobile','is_free','waiting_id'
    ];
                                           
    public function search(string $key)
    {

        $key = $this->db->escapeLikeString($key);
        $query_string = "SELECT * FROM " . $this->table . " WHERE visit_date LIKE '%$key%'";

        $query = $this->db->query($query_string);

        $results = $query->getResult('array');
        
        return $results;
        
    }

    public function search_visit(string $key)
    {
        $keys = explode(' ', $key);
       
        if(!is_array($keys))
            $keys = [];
        
        $keys = $this->db->escapeLikeString($keys);
       
        $where = '(visit_status = ' . \BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN . ' OR ' .'visit_status = ' . \BluelimeConfiguration::VISIT_STATUS_OPEN . ' ) ' ;
        foreach($keys as $key)
        {
            $key = trim($key);
            if($key == '')
                continue;
            
            if($where != '')
                $where .= ' AND ';
            //$mobile = intval($key);
            $patient_id = intval($key);
            if(($patient_id > 0) && (strlen($key) == 5))
                $where .= "(patient_id = $patient_id)";
            else
                $where .= "(patient_name LIKE '%$key%')";
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
    public function search_closed_visit(string $key, $doctor_id)
    {
        $keys = explode(' ', $key);
       
        if(!is_array($keys))
            $keys = [];
        
        $keys = $this->db->escapeLikeString($keys);
       
        $where = '(visit_status = ' . \BluelimeConfiguration::VISIT_STATUS_CLOSED . ' AND ' .'doctor_id = ' . $doctor_id . ' ) ' ;
        foreach($keys as $key)
        {
            $key = trim($key);
            if($key == '')
                continue;
            
            if($where != '')
                $where .= ' AND ';
            //$mobile = intval($key);
            $patient_id = intval($key);
            if(($patient_id > 0) && (strlen($key) == 5))
                $where .= "(patient_id = $patient_id)";
            else
                $where .= "(patient_name LIKE '%$key%')";
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
    
    public function get_open_visit(int $patient_id): array
    {
        $result = $this->where('patient_id', $patient_id)->groupStart()->Where('visit_status <>', \BluelimeConfiguration::VISIT_STATUS_CLOSED)->orWhere('visit_status', \BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN)->groupEnd()->findAll();
        
        return $result;
    }
    public function get_open_visit_queue(int $patient_id): int
    {
        $queue_no = $this->where('patient_id', $patient_id)->where('visit_date >= ',date("Y-m-d%"))->where('visit_status <> ', \BluelimeConfiguration::VISIT_STATUS_CLOSED)->findColumn('max(queue_no)');
        //\BluelimeSystem::show_and_die($queue_no[0]);
        return intval($queue_no[0]);
    }
    public function get_open_visit_doctor(int $patient_id): string
    {
        
        $doctor_name = $this->where('patient_id', $patient_id)->where('visit_date >= ',date("Y-m-d%"))->where('visit_status <> ', \BluelimeConfiguration::VISIT_STATUS_CLOSED)->findColumn('doctor_name');
      
        return $doctor_name[0]?? "";
    }
    public function get_open_visit_date(int $patient_id): string
    {
        
        $visit_date = $this->where('patient_id', $patient_id)->where('visit_date >= ',date("Y-m-d%"))->where('visit_status <> ', \BluelimeConfiguration::VISIT_STATUS_CLOSED)->findColumn('visit_date');
      
        return $visit_date[0]?? \BluelimeSystem::date_now();
    }
    public function get_previous_visit_doctor(int $patient_id): string
    {
        
        $doctor_name = $this->where('patient_id', $patient_id)->where('visit_date <= ',date("Y-m-d%"))->where('visit_status', \BluelimeConfiguration::VISIT_STATUS_CLOSED)->orderBy('id','DESC')->findColumn('doctor_name');
      //BluelimeSystem::show_and_die($doctor_name);
        return $doctor_name[0]?? "";
    }
    public function get_previous_visit_date(int $patient_id): string
    {
        
        $visit_date = $this->where('patient_id', $patient_id)->where('visit_date <= ',date("Y-m-d%"))->where('visit_status', \BluelimeConfiguration::VISIT_STATUS_CLOSED)->orderBy('id','DESC')->findColumn('visit_date');
      
        return $visit_date[0]?? \BluelimeSystem::date_now();
    }

    public function get_patient_last_visit_bill(int $patient_id): array
    {
        $list_of_bill_id = $this->where('patient_id', $patient_id)->where('visit_status >', 1)->findColumn('bill_id');
        if(is_null($list_of_bill_id))
            $list_of_bill_id = [-1];

        $billModel = model('Hospital/BillModel');

        $result = $billModel->where('patient_id', $patient_id)->where('bill_status', \BluelimeConfiguration::BILL_OPEN)->orWhere('bill_status', \BluelimeConfiguration::BILL_CLOSED)->whereIn('id', $list_of_bill_id)->orderBy('payed_date','DESC')->findAll(1);

        $last_visit_bill = !empty($result[0]) ? $result[0] : [];
        
        return $last_visit_bill;
    }

    public function get_last_consultation_bill_date(int $patient_id): array|null{
        $last_consultation_bill = null;

        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');

        $billIdList = $billModel->where('patient_id',$patient_id)->where('bill_status',\BluelimeConfiguration::BILL_RECIEVED)->findColumn('id');

        if(!empty($billIdList)){
            $billDetail = $billDetailModel->whereIn('bill_id', $billIdList)->where('service_type', 'Consultation')->orderBy('bill_date', 'DESC')->limit(1)->findColumn('bill_id');
            $bill_id = $billDetail[0] ?? -1;
            $last_consultation_bill = $billModel->find($bill_id);

        }

        return $last_consultation_bill;
    }

    protected $useSoftDeletes = true;
    protected function change_model_items_to_array(array $data)
    {
        $columns = ['extra'];
        $data = BluelimeSystem::change_model_items_to_array($columns, $data);
        return $data;
    }
    protected function delete_my_details(array $data){
        $id_list = [...($data['id']??[-1])];
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        $visitEncounterModel->whereIn('visit_id',$id_list)->delete();
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
}
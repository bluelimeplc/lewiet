<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table      = 'hospital_appointments';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['patient_id','doctor_id', 'appointment_date', 
                                'appointment_reason', 'appointment_status','full_name',
                                'registered_date','registered_by','registered_by_name',
                                'doctor_name','paid_appointment','visit_id','reminder_sent',
                                'mobile','schedule_id','time','cancelled_by','cancelation_reason','event_id','custom_note'];
    
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
                $where .= "(full_name LIKE '%$key%')";
        }

        if($where != '')
            $where = " WHERE $where ";

        $query_string = "SELECT * FROM " . $this->table . " $where ORDER BY appointment_date  DESC, time LIMIT 0,10";


        //\BluelimeSystem::show_and_die($query_string);
        
        $query = $this->db->query($query_string);

        $results = $query->getResult('array');

        //\BluelimeSystem::show_and_die($query_string);
        
        return $results;
        
    }
    public function appointmentchecker($doctor_id,$date,$time) {  
        
        $result = $this->where('doctor_id',$doctor_id)
                        ->where('appointment_date',$date)
                        ->where('time',$time)
                        ->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)
                        ->findAll();
        return $result;
        
    }
    public function appointmentreason($doctor_id,$date,$time) {  
        
        $result = $this->where('doctor_id',$doctor_id)
                        ->where('appointment_date',$date)
                        ->where('time',$time)
                        ->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)
                        ->findColumn('appointment_reason');
                        //\BluelimeSystem::show_and_die($result);
        return $result;
        
    }
    
    public function patientappointmentexist($patient_id) {  

        $result = $this->where('patient_id',$patient_id)
                        ->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)
                        ->findAll();
                         //\BluelimeSystem::show_and_die($result);     
        return $result;
    }

    public function updatemissed()
    {
        
        $this->where('appointment_date = CURRENT_DATE()')
                        ->WHERE('TIMEDIFF((STR_TO_DATE( `time`, \'%l:%i %p\' ) + INTERVAL 25 MINUTE),CURRENT_TIME()) < ' , 0)
                        ->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)
                        ->set('appointment_status',\BluelimeConfiguration::APPOINTMENT_MISSED)
                        ->update();
    }
    public function updatelatevisit($app_id)
    {
        
        $this->where('appointment_date = CURRENT_DATE()')
                        ->WHERE('TIMEDIFF((STR_TO_DATE( `time`, \'%l:%i %p\' ) + INTERVAL 25 MINUTE),CURRENT_TIME()) < ' , 0)
                        ->WHERE('id',$app_id)
                        ->set('appointment_status',\BluelimeConfiguration::APPOINTMENT_LATE_VISIT)
                        ->update();
    }

    public function get_tomorrow_appointments(){
        $tomorrow = date_create(date('Y-m-d'));
        date_add($tomorrow,date_interval_create_from_date_string("1 day"));
        $tomorrow = date_format($tomorrow,"Y-m-d");
        $appointments = $this
                        ->where('appointment_date', $tomorrow)
                        ->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)
                        ->findAll();
        return $appointments;
    }
    

}
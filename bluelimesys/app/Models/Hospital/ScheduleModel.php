<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table      = 'hospital_schedule';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['doctor_id', 'start_time', 'end_time', 'day', 'per_patient_time','status','doctor_name','schedule_start_date'];

    public function checkdoctorscheduledate($doctor_id,$date){    
        $skipDateModel = model('Hospital/SkipDateModel');

        $doc_skip_dates = $skipDateModel->where('skip_date',$date)
                                        ->where('doctor_id',$doctor_id)
                                        ->findColumn('id');

        $timestamp = strtotime($date);
        $day1 = date('D', $timestamp);
        $day = \BluelimeSystem::day_to_no($day1);
        
        $result = $this->where('doctor_id',$doctor_id)
                        ->where('day',$day)
                        ->where('schedule_start_date <= ', date('Y-m-d', $timestamp).'%')
                        ->findColumn('COUNT(id)');
        
        if($result[0]==0 or !empty($doc_skip_dates))
            return false;
        return true;
    }

    public function checkscheduledate($doctor_id,$date){   

    if(!empty($date)){
        $timestamp = strtotime($date);
        $day1 = date('D', $timestamp); 
        $day = \BluelimeSystem::day_to_no($day1);  
    }
    else{
        $day = '';
    }
        $result = $this->where('doctor_id',$doctor_id)
                        ->where('day',value: $day)
                        ->where('schedule_start_date <= ', date('Y-m-d', $timestamp).'%')
                        ->orderBy('schedule_start_date','desc')
                        ->orderBy('id','desc')
                        ->findAll(1);
                        
        return $result[0]??[];
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
<?php

namespace App\Controllers\Hospital;

use BluelimeAuthentication;
use BluelimeSystem;
use BluelimeConfiguration;

class Nurse extends \App\Controllers\BaseController
{
    public function index()
    
    {
        $search_key= $_POST['search_patient'] ?? "";
        $visitModel = model('Hospital/VisitModel');
        $data['active_menu'] = 'patients_waiting';
        $notificationModel = model('Hospital/NotificationModel');
        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();
        if(trim($search_key) != '')
            $visitModel = $visitModel->groupStart()->where('id LIKE', "%$search_key%")->orWhere('patient_name LIKE',"$search_key%")->orWhere('mobile LIKE',"$search_key%")->groupEnd();
        
        $visits = $visitModel->where('visit_status',BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN)->orWhere('visit_status',BluelimeConfiguration::VISIT_STATUS_OPEN)->orderBy('patient_name asc')->findAll();
       if($visits){
        $notification_visit_id_list = [];

            foreach($visits as $visit){
                array_push($notification_visit_id_list, @$visit['id']);
            }
           
            $new_waiting = $notificationModel->where('notification_type','Nurse Waiting')->where('link_type','visit_id')->whereIn('link_id', $notification_visit_id_list)->findAll();
            
                foreach($visits as $index => $visit){
                    foreach($new_waiting as $waiting){
                        if($visit['id'] == $waiting['link_id']){
                            $visits[$index]['_notify'] = $waiting;
                            break;
                        }
                    }
                }
        }

        $data['searchkey'] = $search_key;
        $data['visits'] = $visits;
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Hospital/Nurse/_left_sidebar_nurse')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Nurse/patient_waiting')

        .view('Include/_footer');
                
    }
    public function waitinglist()
    {
        $search_key = $_POST['search_waiting_list'] ?? "";
        $waitingListModel = model('Hospital/WaitingListModel');
        $visitModel = model('Hospital/VisitModel');
        $userModel = model('System/UserModel');

        if (trim($search_key) != '') {
            $waitings = $waitingListModel->search_by_patient($search_key);
        } else {
            $waitings = $waitingListModel->where('status', 0)->orderBy('priority_level','DESC')->orderBy('doctor_id')->orderBy('id')->findAll();
        }

        $doctorQueue = [];
        foreach ($waitings as $key => $waiting) {
            $doctorId = $waiting['doctor_id'];
            if (!isset($doctorQueue[$doctorId])) {
                $doctorQueue[$doctorId] = 1;
            } else {
                $doctorQueue[$doctorId]++;
            }
            $waitings[$key]['waiting_number'] = $doctorQueue[$doctorId];
        }

        $data['waiting_list'] = $waitings;
        // compute active visit counts per doctor for nurse view using unseen "Doctors Waiting" notifications
        $notificationModel = model('Hospital/NotificationModel');
        $doctors = $userModel->where('role','DOCTOR')->where('active', 1)->findAll() ?? [];
        $doctor_active_counts = [];
        foreach($doctors as $doc){
            $docId = $doc['id'];
            $count = $notificationModel->where('user_id', $docId)
                ->where('link_type', 'visit_id')
                ->where('notification_type', 'Doctors Waiting')
                ->where('status', BluelimeConfiguration::NOTIFICATION_NEW)
                ->findColumn('count(id)');
            $doctor_active_counts[$docId] = intval($count[0] ?? 0);
        }
        $data['doctor_active_counts'] = $doctor_active_counts;
        $data['active_menu'] = 'waiting_list';
        $data['company_header'] = BluelimeSystem::get_company_header();
        $data['searchkey'] = $search_key;

        return view('Include/_header',$data)
            .view('Include/_left_sidebar_top')
            .view('Hospital/Nurse/_left_sidebar_nurse')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Nurse/waiting_list')
            .view('Include/_footer');
    }

    public function prioritize_waiting()
    {
        $waitingListModel = model('Hospital/WaitingListModel');
        $wlid = $_POST['wlid'] ?? 0;
        $priority_reason = $_POST['priority_reason'] ?? '';

        $priority_map = [
            'elderly' => 1,
            'kids_under_3_months' => 2,
            'emergency' => 3,
        ];

        $data = [
            'priority_reason' => $priority_reason,
            'priority_level' => $priority_map[$priority_reason] ?? 0,
        ];

        if (intval($wlid) > 0) {
            $waitingListModel->update($wlid, $data);
        }

        return redirect()->to(site_url('/hospital/nurse/waitinglist'));
    }
    public function previousvisit()
    
    {
        $search_key= $_POST['search_patient'] ?? "";
        $visitModel = model('Hospital/VisitModel');
        

        $data['active_menu'] = 'previousvisit';
        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();
        if(trim($search_key) != '')
            $visitModel = $visitModel->groupStart()->where('patient_id LIKE', "%$search_key%")->orWhere('patient_name LIKE',"$search_key%")->orWhere('mobile LIKE',"$search_key%")->groupEnd();

        $visits = $visitModel->orderBy('visit_date desc')->findAll(20);
        

        $data['searchkey'] = $search_key;
        $data['visits'] = $visits;
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Hospital/Nurse/_left_sidebar_nurse')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Nurse/closed_visit')

        .view('Include/_footer');
                
    }
    
    public function orders()
    {
        $search_key= $_POST['search_patient'] ?? "";
        $data['active_menu'] = 'patients_waiting';
        $billModel = model('Hospital/BillModel');
        $serviceModel = model('Hospital/ServiceModel');
        $billDetailModel = model('Hospital/BillDetailModel');

        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();
        $nurse_procedures = $serviceModel->where('service_code','NURSE')->findColumn('id')??["0"];
        $procedures = $billDetailModel->whereIn('service_id',$nurse_procedures)->findColumn('bill_id')??["0"];

        if(trim($search_key) != '')
        $billModel = $billModel->groupStart()->where('patient_name LIKE', "%$search_key%")->orWhere('mobile LIKE', "%$search_key%")->orWhere('patient_id LIKE', "%$search_key%")->groupEnd();
        $orders = $billModel->whereIn('id',$procedures)->where('bill_status',BluelimeConfiguration::BILL_RECIEVED)->orderBy('payed_date DESC')->findAll(50,0);
        
        $bill_id_list = BluelimeSystem::collect_field_value_from_object($orders, 'id');
        if($bill_id_list)
        $bill_details = $billDetailModel->whereIn('bill_id', $bill_id_list)->where('service_type','procedure')->where('service_status !=', BluelimeConfiguration::SERVICE_STATUS_DELAY)->findAll();
        
        

        $data['searchkey'] = $search_key;
        $data['active_menu'] = 'nurse_orders'; 
        $data['bill_details'] = $bill_details??[]; 
        $data['orders'] = $orders;
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Hospital/Nurse/_left_sidebar_nurse')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Nurse/nurse_orders')

        .view('Include/_footer');
                
    }
    public function nursedashboard()
    {
        $scheduleModel = model('Hospital/ScheduleModel');
        $skipDateModel = model('Hospital/SkipDateModel');

        $skips=$skipDateModel->findAll();

        $data['skips'] = $skips;
        
        $doctors=$scheduleModel->distinct()->findColumn('doctor_name');
        
        $schedules=$scheduleModel->findAll();
        $data['schedules'] = $schedules;
        $data['doctors'] = $doctors;
        $data['active_menu'] = 'nursedashboard';

        $data['active_menu'] = 'reports'; 
        
        //BluelimeSystem::print_associated_array($encounterHistory);
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Hospital/Nurse/_left_sidebar_nurse')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Nurse/nurse_dashboard')
            .view('Include/_footer');
    }

    public function vitalsign()
    {
        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();
        $visitModel = model('Hospital/VisitModel');
        $appointmentModel = model('Hospital/AppointmentModel');
        $patientModel = model('Hospital/PatientModel');
        $vitalSignModel = model('Hospital/VitalSignModel');
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        $userModel = model('System/UserModel');
        $notificationModel = model('Hospital/NotificationModel');
        $notificationModel->where('link_id',$_GET['id'])->where('link_type','visit_id')->where('notification_type','Nurse Waiting')->set('status',\BluelimeConfiguration::NOTIFICATION_SEEN)->update();
        $autoCompleteModel = model('Hospital/AutoCompleteModel');
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');

        $allergies = $autoCompleteModel->where('name','allergy')->findColumn('data')[0] ?? [];
        $diseases = $autoCompleteModel->where('name','disease')->findColumn('data')[0] ?? [];
        $drugs = $autoCompleteModel->where('name','drug')->findColumn('data')[0] ?? [];

        $visit_id= $_GET['id'] ?? "";
       
        $visit = $visitModel->where('id',$visit_id)->find($visit_id);
        $patient = $patientModel->find($visit['patient_id']);
        $visitEncounter = $visitEncounterModel->where('visit_id', $visit_id)->first() ?? [];
        $chiefComplaintList = $autoCompleteModel->where('name','chief_complaints')->findColumn('data')[0] ?? [];
        $vitals = $vitalSignModel->where('patient_id',$visit['patient_id'])->where('visit_id',$visit_id)->findAll();
        $role = array('DOCTOR','NURSE','PATHOLOGY');
        $doctors = $userModel->whereIn('role',$role)->where('active', 1)->findAll();
        $appointments = $appointmentModel->where('appointment_date >=',date('Y-m-d'))->where('patient_id', $visit['patient_id'])->findAll(2);
        //BluelimeSystem::show_and_die($appointments);
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $encounter_history = BluelimeSystem::get_patient_encounter_history($visit['patient_id'])??[];

        $pending_bill_id = $billModel->where('visit_id', $visit_id)->where('bill_status', BluelimeConfiguration::BILL_PENDING)->findColumn('id');
        $pending_bills = [];
        if(!empty($pending_bill_id))
            $pending_bills = $billDetailModel->whereIn('bill_id', $pending_bill_id)->findAll();

        $orderOption = [];
        foreach($pending_bills as $index => $service){
            $service_temp = [
                    "service_id"=> $service['service_id'],
                    "service_name"=> $service['service_name'],
                    "quantity"=> $service['bill_quantity']
                ];
            if(intval($service['service_id']) < 0)
                $service_temp["quantity"] = -1 * floatval($service['bill_amount']);

            array_push($orderOption, $service_temp);
        }

        foreach($pending_bills as $service){
            if($service['bill_discount'] > 0){
                $service_temp = [
                    [
                        "service_id"=> BluelimeConfiguration::DISCOUNT_PRICE_PERCENT_ID,
                        "service_name"=> 'Discount In Percent',
                        "quantity"=> $service['bill_discount']
                    ]
                ];
                array_push($orderOption, ...$service_temp);
                break;
            }
        }
        // Prescription

        $prescription_group = [];
        $prescriptions = $prescriptionModel->where('patient_id', $visit['patient_id'])->orderBy('id', 'DESC')->findAll();
        $prescription_details = BluelimeSystem::gather_objects_by_reference($prescriptions,'id','Hospital/PrescriptionDetailModel','prescription_id');
        $compoundings = $compoundingPrescriptionMainModel->where('JSON_EXTRACT(patient_detail, "$.id")', $visit['patient_id'])->orderBy('id', 'DESC')->findAll();
        $compounding_details = BluelimeSystem::gather_objects_by_reference($compoundings,'id','Hospital/CompoundingPrescriptionDetailModel','main_id');

        foreach($prescriptions as $index =>$row){
            $prescriptions[$index]['details'] = [];
            foreach($prescription_details as $detail){
                if($row['id'] == $detail['prescription_id'])
                    array_push($prescriptions[$index]['details'], $detail);
            }
        }

        foreach($compoundings as $index =>$row){
            $compoundings[$index]['compounding_detail'] = [];
            foreach($compounding_details as $detail){
                if($row['id'] == $detail['main_id'])
                    array_push($compoundings[$index]['compounding_detail'], $detail);
            }
        }

        foreach($prescriptions as $row){
            if(!isset($prescription_group['prescription_group_id_'.$row['prescription_group_id']]))
                $prescription_group['prescription_group_id_'.$row['prescription_group_id']] = ['prescriptions'=>[], 'compoundings'=>[]];
            array_push($prescription_group['prescription_group_id_'.$row['prescription_group_id']]['prescriptions'], $row);
        }

        foreach($compoundings as $row){
            if(!isset($prescription_group['prescription_group_id_'.$row['prescription_group_id']]))
                $prescription_group['prescription_group_id_'.$row['prescription_group_id']] = ['prescriptions'=>[], 'compoundings'=>[]];
            array_push($prescription_group['prescription_group_id_'.$row['prescription_group_id']]['compoundings'], $row);
        }

        $pricelistModel = model('Hospital/PricelistModel');
        $pricelistDetailModel = model('Hospital/PricelistDetailModel');
        $pricelist = $pricelistModel->where('company_id', $patient['company_id'])->find();
        $pricelist_id = $pricelist[0]['id'] ?? BluelimeConfiguration::DEFAULT_PRICE_LIST_ID;
        //$service_list = $pricelistDetailModel->select('service_id as id,service_name')->where('status',1)->where('pricelist_id',$pricelist_id)->orderBy('service_name', 'ASC')->findAll();
        $service_list = $pricelistDetailModel->select('service_id as id,service_name')->where('status',1)->groupBy('service_id, service_name')->orderBy('service_name', 'ASC')->findAll();
        
        $related_visits_encounter = [];
        $related_visits_paid_service = [];
        $collected_related_visits = BluelimeSystem::collect_follow_up_visits($visit['follow_up_id'], $visit_id, false);

        //$related_visits_paid_service = $this->get_visits_paid_services([$visit_id]);

        $related_visits_encounter = BluelimeSystem::get_visits_encounters($collected_related_visits);
        $related_visits_paid_service = $this->get_visits_paid_services([$visit_id, ...$collected_related_visits]);

        $collected_related_visits = BluelimeSystem::collect_follow_up_visits($visit['follow_up_id'], $visit_id, false);

        $related_visits_encounter = BluelimeSystem::get_visits_encounters($collected_related_visits);



        $data['active_menu'] = 'patients_waiting';
        $data['prescription_group'] = $prescription_group;
        $data['related_visits_encounter'] = $related_visits_encounter;
        $data['related_visits_paid_service'] = $related_visits_paid_service;
        $data['patient'] = $patient;
        $data['encounter_history'] = $encounter_history;
        $data['visits'] = $visit;
        $data['vitals'] = $vitals;
        $data['drugs'] = $drugs;
        $data['allergies'] = $allergies;
        $data['diseases'] = $diseases;
        $data['doctors'] = $doctors;
        $data['orderOption'] = $orderOption;
        $data['service_list'] = $service_list;
        $data['appointments'] = $appointments;
        $data['visitEncounter'] = $visitEncounter;
        $data['chiefComplaintList'] = $chiefComplaintList;
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Hospital/Nurse/_left_sidebar_nurse')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Nurse/vital_sign')

        .view('Include/_footer');
                
    }
    private function get_visits_paid_services(array $visit_id_list) : array {
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');

        $paid_services = [];
        $bill_id_list = $billModel->whereIn('visit_id', $visit_id_list)->where('bill_status', BluelimeConfiguration::BILL_RECIEVED)->findColumn('id');
        if(!empty($bill_id_list))
            $paid_services = $billDetailModel->select('service_id, service_name, service_type, bill_quantity, bill_amount, bill_date, service_status')->whereIn('bill_id', $bill_id_list)->where('service_type !=', 'Consultation')->orderBy('bill_date', 'DESC')->findAll();

        return $paid_services;
    }
    public function addvitalsign()
    {
        $vitalSignModel = model('Hospital/VitalSignModel');
        $visitModel = model('Hospital/VisitModel');
        $notificationModel = model('Hospital/NotificationModel');
        
        $blood_pressure     = $_POST['systolic']."/".$_POST['diastolic'];
        $temperature    = $_POST['temperature'];
        $weight    = $_POST['weight'];
        $pulse_rate      = $_POST['pulse_rate'];
        $respiration_rate  = $_POST['respiration_rate'];
        $registered_by            = $_POST['registered_by'];
        $registered_by_name        = $_POST['registered_by_name'];
        $note        = $_POST['note'];
        $visit_id         = $_POST['visit_id'];
        $patient_id       = $_POST['patient_id'];
        $patient_name         = $_POST['patient_name'];
        $doctor_id         = $_POST['doctor_id'];
        

        $data = [
            'blood_pressure'        => $blood_pressure,
            'temperature'           => $temperature,
            'weight'                => $weight,
            'pulse_rate'            => $pulse_rate,
            'respiration_rate'      => $respiration_rate,
            'note'                  => $note,
            'visit_id'              => $visit_id,
            'patient_id'            => $patient_id,
            'patient_name'          => $patient_name,
            'registered_by'         => $registered_by, 
            'registered_by_name'    => $registered_by_name
        ];
        
        
        
        $id = $_POST['id']?? 0;
        if ($id != 0)
            $vitalSignModel->update($id,$data);
        else
            $id = $vitalSignModel->insert($data,true);

        $visitModel -> update($visit_id,['visit_status' => \BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN]);
        
        // $notfiy_data = [
        //     'notification_type'=>  "Doctors Waiting",
        //     'link_type'=>  "visit_id",
        //     'description'=> $patient_name,
        //     'user_id'=> $doctor_id,
        //     'link_id'=> $visit_id,
        //  ];
         
        // $notificationModel->insert($notfiy_data,true);
        return redirect()->to(site_url('/hospital/nurse/vitalsign?id='.$visit_id));
    }
    public function remove_vital(){
        $vitalSignModel = model('Hospital/VitalSignModel');
        $vital_id = $_POST['vital_id'];
        $vitalSignModel->delete($vital_id);
    }
    public function addpatientmedical()
    {
        //BluelimeSystem::show_and_die($_POST);
        $patientModel = model('Hospital/PatientModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        
        $blood_pressure = $_POST['blood_pressure']??"No";
        $bleeding_tendency = $_POST['bleeding_tendency']??"No";
        $pregnancy      = $_POST['pregnancy']??"No";
        $cardiac_problem = $_POST['cardiac_problem']??"No";
        $diabetics      =  $_POST['diabetics']??"";
        $blood_group    = $_POST['blood_group'];
        $drugs_taking   = @$_POST['drugs_taking'];
        $allergy        = @$_POST['allergy'];
        $other_diseases = @$_POST['other_diseases'];
        //$registered_date = date("Y-m-d");
        $regisrered_by = $loggeduserdata['id'];
        $registered_by_name = $loggeduserdata['name'];

        if(is_array($drugs_taking))
            $drugs_taking = json_encode($drugs_taking);

        if(is_array($allergy))
            $allergy = json_encode($allergy);

        if(is_array($other_diseases))
            $other_diseases = json_encode($other_diseases);

        $data = [
            'blood_pressure'=> $blood_pressure,
            'bleeding_tendency' => $bleeding_tendency,
            'pregnancy'     => $pregnancy,
            'cardiac_problem' => $cardiac_problem,
            'diabetics'     => $diabetics,
            'blood_group'   => $blood_group,
            'drugs_taking'  => $drugs_taking,
            'allergy'       => $allergy,
            'other_diseases'=> $other_diseases,
            'registered_by' => $regisrered_by, 
            'registered_by_name' => $registered_by_name
        ];
        
        
        
        $patientModel->update($_POST['pid'],$data);
        

        return redirect()->to(site_url('/hospital/nurse/vitalsign?id='.$_POST['vid']));
    }
    public function addappointment()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
        $userModel = model('System/UserModel');
        $scheduleModel = model('Hospital/ScheduleModel');
        
        $patient_id     = $_POST['patient_id'];
        $vital_id     = $_POST['vital_id'];
        $doctor_id    = $_POST['doctor_id'];
        $appointment_date      = $_POST['date'];
        $appointment_reason  = $_POST['appointment_reason'];
        $appointment_status            = \BluelimeConfiguration::APPOINTMENT_ACTIVE;
        $full_name        = ucwords($_POST['first_name']." ".$_POST['middle_name']." ".$_POST['last_name']);
        $registered_by         = $_POST['registered_by'];
        $registered_by_name       = $_POST['registered_by_name'];
        $doctor = $userModel->where('id', $doctor_id)->findColumn('name');
        $doctor_name         = $doctor[0];
        $paid_appointment = $_POST['paid_appointment'];
        $visit_id = $_POST['visit_id'];
        $reminder_sent     = $_POST['reminder_sent'];
        $schedule_id     = $_POST['schedule_id'];
        $mobile     = $_POST['mobile'];
        $time     = $_POST['sequence'];
        $app_id = $_POST['app_id'];
        $data = [
            'patient_id'    => $patient_id,
            'doctor_id'   => $doctor_id,
            'appointment_date'     => $appointment_date,
            'appointment_reason' => $appointment_reason,
            'appointment_status'           => $appointment_status,
            'full_name'       => $full_name,
            'registered_by'        => $registered_by,
            'registered_by_name'      => $registered_by_name,
            'doctor_name'        => $doctor_name,
            'paid_appointment' => $paid_appointment,
            'visit_id' => $visit_id,
            'reminder_sent'    => $reminder_sent,
            'schedule_id'    => $schedule_id,
            'mobile'    => $mobile,
            'time'    => $time
        ];
        $schedule_min = $scheduleModel->where('id',$schedule_id)->findColumn('per_patient_time');
        
        $patient_appointment_exist = $appointmentModel->patientappointmentexist($patient_id);
        $appointment_time_exist = $appointmentModel->appointmentchecker($doctor_id,$appointment_date,$time);
       // BluelimeSystem::show_and_die($patient_appointment_exist);
       
        if($appointment_time_exist){  
            return redirect()->to(site_url('/hospital/nurse/vitalsign?id='.$patient_id));
        }
        $id = $appointmentModel->insert($data,true);
        // if($doctor_id==31){
        //     BluelimeSystem::create_google_calendar_events($appointment_date,$time,$appointment_reason,$full_name,$schedule_min);
        //     }
        return redirect()->to(site_url('/hospital/nurse/vitalsign?id='.$vital_id));
    }
    public function appointmentlist()
    {
        
        $appointmentModel = model('Hospital/AppointmentModel');
        $appointmentModel->updatemissed();
        
        $selected_date = isset($_POST['appointment_date'])?($_POST['appointment_date'].'%'):date('Y-m-d').'%';
        $searchkey= $_POST['search_appointment'] ?? "";
        //BluelimeSystem::show_and_die($_POST);
        if(!empty($_POST['search_appointment'])){
            $appointments = $appointmentModel->search_by_patient($searchkey);
        }
        else
            if(!empty($_POST['app_doctor'])){
                $appointments = $appointmentModel->where('appointment_date like ',$selected_date)->where('doctor_name',$_POST['app_doctor'])->findAll();
                $doctors = $appointmentModel->distinct()->where('appointment_date like ',$selected_date)->findColumn('doctor_name');
                //BluelimeSystem::show_and_die($doctors);
                $no_of_appointment = $appointmentModel->where('appointment_date like ',$selected_date)->where('doctor_name',$_POST['app_doctor'])->findColumn('count(id)')[0]; 
            }
            else{
                $appointments = $appointmentModel->where('appointment_date like ',$selected_date)->findAll();
                $doctors = $appointmentModel->distinct()->where('appointment_date like ',$selected_date)->findColumn('doctor_name');
                //BluelimeSystem::show_and_die($doctors);
                $no_of_appointment = $appointmentModel->where('appointment_date like ',$selected_date)->findColumn('count(id)')[0];
            }

        $data['selected_date'] = $selected_date;
        $data['appointments'] = $appointments;
        $data['no_of_appointment'] = $no_of_appointment;
        $data['doctors'] = $doctors;

        $data['active_menu'] = 'appointments';
        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Nurse/_left_sidebar_nurse')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Nurse/appointment_list')

            .view('Include/_footer');
    }
    public function changedoctor()
    {
        $visitModel = model('Hospital/VisitModel');
        $userModel = model('System/UserModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $notificationModel = model('Hospital/NotificationModel');
        
        $assigned_doctor = $_POST['assigned_doctor'];
        $registered_by = $loggeduserdata['id'];
        $registered_by_name = $loggeduserdata['name'];        
        $doctor = $userModel->find($assigned_doctor);
        $doctor_name = $doctor['name'];
        $queue_no = $visitModel->where('doctor_id',$assigned_doctor)->where('visit_date >= ',date("Y-m-d%"))->findColumn('max(queue_no)');

        $data = [
        'doctor_id'=> $assigned_doctor,
        'registered_by'=> $registered_by,
        'registered_by_name' => $registered_by_name,
        'doctor_name' => $doctor_name,
        'queue_no' => intval($queue_no[0]) + 1

        ];
        $visit_id = $_POST['visit_id'];
        $vital_id = $_POST['vital_id'];
        //BluelimeSystem::show_and_die($id);
        $visitModel->update($visit_id,$data);
        $visit = $visitModel->find($visit_id);
        $notfiy_data_doctor = [
            'notification_type'=>  "Doctors Waiting",
            'link_type'=>  "visit_id",
            'description'=> $visit['patient_name'],
            'user_id'=> $doctor['id'],
            'link_id'=> $visit_id,
         ];
         
        $notificationModel->insert($notfiy_data_doctor,true);
        
        return redirect()->to(base_url()."hospital/nurse/vitalsign?id=".$vital_id);
    }
    public function printoutlist()
    {
        $searchkey= $_POST['search_printout'] ?? "";
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $notificationModel = model('Hospital/NotificationModel');
        $medicalCertificateModel = model('Hospital/MedicalCertificateModel');
        $medicalReportModel = model('Hospital/MedicalReportModel');
        $labRequestModel = model('Hospital/LabRequestModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $userModel = model('System/UserModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        
        $data['active_menu'] = 'printoutlist';
        $data['loggeduserdata'] = $loggeduserdata;

        $prescriptions = $prescriptionModel->search_by_patient($searchkey);
        $external_lab_request = $externalDocumentsModel->search_by_patient($searchkey);
        $medicalCertificates = $medicalCertificateModel->search_by_patient($searchkey);
        $medicalReports = $medicalReportModel->search_by_patient($searchkey);
        $labRequests = $labRequestModel->search_by_patient($searchkey);
        $orderMainModel = $orderMainModel->groupStart()->Where('patient_name like',"%$searchkey%")->OrWhere('patient_id like',"%$searchkey%");

        $orders = $orderMainModel->select('hospital_order_main.id as id,hospital_order_main.patient_id as patient_id,hospital_order_main.patient_name as patient_name,hospital_order_main.mobile as mobile,hospital_order_main.result_approved_by as result_approved_by,hospital_order_main.ordered_by as ordered_by,hospital_order_main.result_approved_date as result_approved_date')->groupEnd()->where('order_service_type','Laboratory')->where('result_status',BluelimeConfiguration::LAB_RESULT_APPROVED)->orderBy('result_approved_date DESC')->findAll(20,0);
        //BluelimeSystem::show_and_die($orders);
        $prescription_group_id_list = [...BluelimeSystem::collect_field_value_from_object($prescriptions, 'prescription_group_id'), -1];

        $compoundingPrescriptions = $compoundingPrescriptionMainModel->whereNotIn('prescription_group_id', $prescription_group_id_list)->groupStart()->where('CAST(JSON_UNQUOTE(JSON_EXTRACT(patient_detail, "$.name")) as CHAR) like', "%$searchkey%")->orwhere('CAST(JSON_UNQUOTE(JSON_EXTRACT(patient_detail, "$.id")) as CHAR) like', "%$searchkey%")->orwhere('CAST(JSON_UNQUOTE(JSON_EXTRACT(patient_detail, "$.mobile")) as CHAR) like', "%$searchkey%")->groupEnd()->findAll();

        $printout_lists = [];
        // Lab result

        /*if(trim($searchkey) != ''){
            $orderMainModel = $orderMainModel->join('hospital_patients','hospital_patients.id = patient_id')->groupStart();
            $orderMainModel = $orderMainModel->Where('first_name like',"$searchkey%");
            if(intval($searchkey) > 0)
                $orderMainModel = $orderMainModel->orWhere('patient_id', intval($searchkey));

            $orderMainModel = $orderMainModel->groupEnd();
        }*/
        
       //BluelimeSystem::print_associated_array($orders);
       //return '';
       
        $patients = BluelimeSystem::gather_objects_by_reference($orders,'patient_id','Hospital/PatientModel','id',true);
        
        $user_id_list = BluelimeSystem::collect_field_value_from_object($orders, 'ordered_by');
        
        
        if(empty($user_id_list))
            $user_id_list = [-1];
        
        $user_list = $userModel->whereIn('id', $user_id_list)->findAll();

        foreach($orders as $index => $rt){
            $orders[$index]['ordered_by_name'] = ''; 
            foreach($user_list as $user){
                if($user['id'] == $rt['ordered_by'])
                $orders[$index]['ordered_by_name'] = $user['name']; 
            }
            //$orders[$index]['approved_by'] = $userModel->where('id',$rt['result_approved_by'])->findColumn('name')[0];
        }
        

        // Lab result end
        
        
//BluelimeSystem::print_associated_array($orders);
        foreach($orders as $order){
            $row = [
                'id' => $order['id'],
                'type' => 'Laboratory',
                'patient_id' => $order['patient_id'],
                'patient_name' => $order['patient_name'],
                'mobile' => $order['mobile'],
                'registered_by_name' => $order['ordered_by_name'],
                'registered_date' => $order['result_approved_date']
            ];

            array_push($printout_lists, $row);
        }
        foreach($prescriptions as $prescription){
            $row = [
                'id' => $prescription['id'],
                'prescription_group_id' => $prescription['prescription_group_id'],
                'type' => 'Prescription',
                'patient_id' => $prescription['patient_id'],
                'patient_name' => $prescription['patient_name'],
                'mobile' => $prescription['mobile'],
                'registered_by_name' => $prescription['registered_by_name'],
                'registered_date' => $prescription['registered_date']
            ];

            array_push($printout_lists, $row);
        }
        foreach($compoundingPrescriptions as $compoundingPrescription){
            $row = [
                'id' => $compoundingPrescription['id'],
                'prescription_group_id' => $compoundingPrescription['prescription_group_id'],
                'type' => 'Prescription',
                'patient_id' => $compoundingPrescription['patient_detail']['id'],
                'patient_name' => $compoundingPrescription['patient_detail']['name'],
                'mobile' => $compoundingPrescription['patient_detail']['mobile'],
                'registered_by_name' => $compoundingPrescription['created_by_name'],
                'registered_date' => $compoundingPrescription['updated_at']
            ];

            array_push($printout_lists, $row);
        }
        foreach($medicalCertificates as $medicalCertificate){
            $row = [
                'id' => $medicalCertificate['id'],
                'type' => 'Medical Certificate',
                'patient_id' => $medicalCertificate['patient_id'],
                'patient_name' => $medicalCertificate['patient_name'],
                'mobile' => $medicalCertificate['mobile'],
                'registered_by_name' => $medicalCertificate['registered_by_name'],
                'registered_date' => $medicalCertificate['regisered_date']
            ];

            array_push($printout_lists, $row);
        }
        foreach($medicalReports as $medicalReport){
            $row = [
                'id' => $medicalReport['id'],
                'type' => 'Medical Report',
                'patient_id' => $medicalReport['patient_id'],
                'patient_name' => $medicalReport['patient_name'],
                'mobile' => $medicalReport['mobile'],
                'registered_by_name' => $medicalReport['doctor_name'],
                'registered_date' => $medicalReport['created_at']
            ];

            array_push($printout_lists, $row);
        }
        foreach($external_lab_request as $external_lab){
            if($external_lab['request_type']=="LABORATORY_REQUEST"){
                $type = "External Laboratory Request";
            }
            elseif($external_lab['request_type']=="BIOPSY_REQUEST"){
                $type = "External Biopsy Request";
            }
            elseif($external_lab['request_type']=="REFERAL_REQUEST"){
                $type = "Referal Request";
            }
            $row = [
                'id' => $external_lab['id'],
                'type' => $type,
                'patient_id' => $external_lab['patient_id'],
                'visit_id' => $external_lab['visit_id'],
                'patient_name' => $external_lab['patient_name'],
                'mobile' => $external_lab['mobile'],
                'registered_by_name' => $external_lab['registered_by_name'],
                'registered_date' => $external_lab['created_at']
            ];

            array_push($printout_lists, $row);
        }
        foreach($labRequests as $labRequest){
            $row = [
                'id' => $labRequest['id'],
                'type' => 'Lab Requests',
                'patient_id' => $labRequest['patient_id'],
                'patient_name' => $labRequest['patient_name'],
                'mobile' => $labRequest['mobile'],
                'registered_by_name' => $labRequest['registered_by_name'],
                'registered_date' => $labRequest['date_impression']
            ];

            array_push($printout_lists, $row);
        }
        
        foreach ($printout_lists as $key => $date) {
            $sort[$key] = strtotime($date['registered_date']);
       }
       if(!empty($printout_lists)){
            array_multisort($sort, SORT_DESC, $printout_lists);
       }
        
       //BluelimeSystem::show_and_die($printout_lists) ;
       
       if($prescriptions){
        $notification_prescription_id_list = [];

        foreach($prescriptions as $prescription){
            array_push($notification_prescription_id_list, @$prescription['id']);
        }
        
        $new_prescription = $notificationModel->where('link_type','prescription_id')->whereIn('link_id', $notification_prescription_id_list)->findAll();
        
            foreach($printout_lists as $index => $prescription){
                foreach($new_prescription as $newp){
                    if($prescription['id'] === $newp['link_id']){
                        $printout_lists[$index]['_notifyp'] = $newp;
                        break;
                    }
                }
            }
        }
        if($labRequests){
        $notification_lab_id_list = [];

            foreach($labRequests as $labRequest){
                array_push($notification_lab_id_list, @$labRequest['id']);
            }
            
            $new_labRequest = $notificationModel->where('link_type','lab_request_id')->whereIn('link_id', $notification_lab_id_list)->findAll();
            
                foreach($printout_lists as $index => $labRequest){
                    foreach($new_labRequest as $newl){
                        if($labRequest['id'] === $newl['link_id']){
                            $printout_lists[$index]['_notifyl'] = $newl;
                            break;
                        }
                    }
                }
            }
            if($medicalReports){
                $notification_medical_report_id_list = [];
        
                foreach($medicalReports as $medicalReport){
                    array_push($notification_medical_report_id_list, @$medicalReport['id']);
                }
                
                $new_medical_report = $notificationModel->where('link_type','medical_report_id')->whereIn('link_id', $notification_medical_report_id_list)->findAll();
                
                    foreach($printout_lists as $index => $medicalReport){
                        foreach($new_medical_report as $newm){
                            if($medicalReport['id'] === $newm['link_id']){
                                $printout_lists[$index]['_notifymr'] = $newm;
                                break;
                            }
                        }
                    }
                    }
        if($medicalCertificates){
        $notification_medical_certificate_id_list = [];

        foreach($medicalCertificates as $medicalCertificate){
            array_push($notification_medical_certificate_id_list, @$medicalCertificate['id']);
        }
        
        $new_medical_certificate = $notificationModel->where('link_type','medical_certificate_id')->whereIn('link_id', $notification_medical_certificate_id_list)->findAll();
        
            foreach($printout_lists as $index => $medicalCertificate){
                foreach($new_medical_certificate as $newm){
                    if($medicalCertificate['id'] === $newm['link_id']){
                        $printout_lists[$index]['_notifym'] = $newm;
                        break;
                    }
                }
            }
            }
            
            if($orders){
        $notification_order_id_list = [];

        foreach($orders as $order){
            array_push($notification_order_id_list, @$order['id']);
        }
        
        $new_order = $notificationModel->where('link_type','order_main_id')->whereIn('link_id', $notification_order_id_list)->findAll();
        
            foreach($printout_lists as $index => $order){
                foreach($new_order as $newl){
                    if($order['id'] === $newl['link_id']){
                        $printout_lists[$index]['_notifylab'] = $newl;
                        break;
                    }
                }
            }
            }
            if($external_lab_request){
                $notification_external_request_id_list = [];
        
                foreach($external_lab_request as $lab_request){
                    array_push($notification_external_request_id_list, @$lab_request['id']);
                }
                
                $new_external_request = $notificationModel->where('link_type','external_request_id')->whereIn('link_id', $notification_external_request_id_list)->findAll();
                
                    foreach($printout_lists as $index => $lab_request){
                        foreach($new_external_request as $newm){
                            if($lab_request['id'] === $newm['link_id']){
                                $printout_lists[$index]['_notifymr'] = $newm;
                                break;
                            }
                        }
                    }
                    }   
            

        
        
        
        $data['prescriptions'] = $prescriptions;
        $data['orders'] = $prescriptions;
        $data['printout_lists'] = $printout_lists;
        //$data['prescriptionDetail'] = $prescriptionDetail;
        $data['medicalCertificates'] = $medicalCertificates;
        $data['labRequests'] = $labRequests;
        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Nurse/_left_sidebar_nurse')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Nurse/printout_list')

            .view('Include/_footer');
    }
    public function viewmedicalcertificate()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $medicalCertificateModel = model('Hospital/MedicalCertificateModel');
        $patientModel = model('Hospital/PatientModel');
        $visitModel = model('Hospital/VisitModel');
        $userModel = model('System/UserModel');
        $notificationModel = model('Hospital/NotificationModel');
        $notificationModel->where('link_id',$_GET['mid'])->where('notification_type','Medical Certificates')->set('status',BluelimeConfiguration::NOTIFICATION_SEEN)->update();
        
        $patient = $patientModel->find(@$_GET['id']);

        $medical_certificate = $medicalCertificateModel->where('id',$_GET['mid'])->orderBy('id','DESC')-> findAll();
        $doc_sign = $userModel->where('id',$medical_certificate[0]['registerd_by'])->findColumn('digital_signature'); 
        $doctor_id = $visitModel->where('id',$medical_certificate[0]['visit_id'])->findColumn('doctor_id');
        $doctor = $userModel->find($doctor_id);
        //BluelimeSystem::show_and_die($doctor[0]);
               

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'printoutlist';
        $data['patient'] = $patient;
        $data['doctor'] = $doctor[0];
        $data['medical_certificate'] = $medical_certificate;
        $data['doc_sign'] = $doc_sign[0];
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Nurse/_left_sidebar_nurse')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Nurse/view_medical_certificate')

            .view('Include/_footer');
    }

    public function viewmedicalreport()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $medicalReportModel = model('Hospital/MedicalReportModel');
        $patientModel = model('Hospital/PatientModel');
        $visitModel = model('Hospital/VisitModel');
        $userModel = model('System/UserModel');
        $notificationModel = model('Hospital/NotificationModel');
        $notificationModel->where('link_id',$_GET['mid'])->where('notification_type','Medical Report')->set('status',\BluelimeConfiguration::NOTIFICATION_SEEN)->update();
        $userModel = model('System/UserModel');
        

        $patient = $patientModel->find(@$_GET['id']);

        $medical_report = $medicalReportModel->where('id',$_GET['mid'])->orderBy('id','DESC')-> findAll();
        $doc_sign = $userModel->where('id',$medical_report[0]['doctor_id'])->findColumn('digital_signature'); 
            
        $doctor = $userModel->find($medical_report[0]['doctor_id']);
 //BluelimeSystem::show_and_die($doctor);  
        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['doctor'] = $doctor;
        $data['medical_report'] = $medical_report;
        $data['doc_sign'] = $doc_sign[0];
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Nurse/_left_sidebar_nurse')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Nurse/view_medical_report')

            .view('Include/_footer');
    }
    public function viewprescription()
    {
        $prescription_group_id = $_GET['prescription_group_id'] ?? -1;

        $visitModel = model('Hospital/VisitModel');
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $prescriptionDetailModel = model('Hospital/PrescriptionDetailModel');
        $userModel = model('System/UserModel');
        $patientModel = model('Hospital/PatientModel');
        $notificationModel = model('Hospital/NotificationModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        

        //BluelimeSystem::show_and_die($prescriptionModel);
        $prescriptions = $prescriptionModel->where('prescription_group_id',$prescription_group_id)->findAll();
        $prescription_id_list = [...BluelimeSystem::collect_field_value_from_object($prescriptions, 'id'),-1];
        $prescription_detail = $prescriptionDetailModel->whereIn('prescription_id',$prescription_id_list)->findAll();

        $compounding_mains = $compoundingPrescriptionMainModel->where('prescription_group_id', $prescription_group_id)->findAll();
        $compounding_details = BluelimeSystem::gather_objects_by_reference($compounding_mains, 'id', 'Hospital/CompoundingPrescriptionDetailModel', 'main_id');

        foreach($compounding_mains as $index => $compounding_main){
            $compounding_mains[$index]['compounding_detail'] = [];
            foreach($compounding_details as $compounding_detail){
                if($compounding_detail['main_id'] == $compounding_main['id'])
                    array_push($compounding_mains[$index]['compounding_detail'], $compounding_detail);
            }
        }

        $notificationModel->whereIn('link_id',$prescription_id_list)->where('notification_type','Prescriptions')->set('status',BluelimeConfiguration::NOTIFICATION_SEEN)->update();
        $visit_id = $prescriptions[0]['visit_id'] ?? $compounding_mains[0]['visit_id'];
        $visit = $visitModel->find($visit_id);
        
        $patient_id = $visit['patient_id'];
        $registered_date = $visit['visit_date'];
        $registered_by = $prescriptions[0]['registered_by'] ?? $compounding_mains[0]['created_by'];
        $is_void = $prescriptions[0]['is_void'] ?? $compounding_mains[0]['is_void'];
        $void_date = $prescriptions[0]['updated_at'] ?? $compounding_mains[0]['updated_at'];
        $patient = $patientModel->find($patient_id);
        $doctor = $userModel->find($registered_by);

        $data['prescription_main'] = [
            'id' => $patient_id,
            'registered_date' => $registered_date,
            'is_void' => $is_void,
            'void_date' => $void_date,
            'doctor' => $doctor,
        ];

        $data['compounding_mains'] = $compounding_mains;
        $data['prescription_detail'] = $prescription_detail;
        $data['active_menu'] = 'prescriptions';
        //$data['doc_sign'] = $doc_sign[0];
        $data['patient'] = $patient;
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Nurse/_left_sidebar_nurse')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Nurse/view_prescription')

            .view('Include/_footer');
    }
    public function view_lab_printout(): string
    {
        $laboratoryResultsModel = model('Hospital/LaboratoryResultsModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $serviceModel = model('Hospital/ServiceModel');
        $userModel = model('System/UserModel');
        $notificationModel = model('Hospital/NotificationModel');
        
        $notificationModel->where('link_id',@$_GET['order_main_id'])->where('notification_type','Laboratory')->set('status',BluelimeConfiguration::NOTIFICATION_SEEN)->update();
        
        $result_approved_by = $orderMainModel->where('id',@$_GET['order_main_id'])->findColumn('result_approved_by')[0];
        $result_approved_by_name = $userModel->where('id',$result_approved_by)->findColumn('name')[0];
        $order_result = $laboratoryResultsModel->where('order_id',@$_GET['order_main_id'])->orderby('test_id asc')->findAll();
        
        foreach($order_result as $index => $rt){
            $order_result[$index]['patient_detail'] = $orderMainModel->where('id',@$_GET['order_main_id'])->findAll()[0]??[];
            $order_result[$index]['test_name'] = $serviceModel->where('id',$rt['test_id'])->findColumn('service_name')[0];
            $order_result[$index]['approved_by'] = $result_approved_by_name;
        }
        //BluelimeSystem::show_and_die($order_result);
        $data['order_result'] = $order_result;
        
        $data['active_menu'] = 'pending_order';

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Hospital/Nurse/_left_sidebar_nurse')
                .view('Include/_left_sidebar_bottom')
                .view('Hospital/Nurse/view_lab_result')
                .view('Include/_footer');     
    }
    public function viewlabrequest()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $userModel = model('System/UserModel');
        $labRequestModel = model('Hospital/LabRequestModel');
        $patientModel = model('Hospital/PatientModel');
        $notificationModel = model('Hospital/NotificationModel');
        $notificationModel->where('link_id',$_GET['lid'])->where('notification_type','Lab Requests')->set('status',\BluelimeConfiguration::NOTIFICATION_SEEN)->update();
        
        
        $patient = $patientModel->find(@$_GET['id']);

        $lab_request = $labRequestModel->where('id',$_GET['lid'])->orderBy('id','DESC')-> findAll();
        $doc_sign = $userModel->where('id',$lab_request[0]['registered_by'])->findColumn('digital_signature');

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'printoutlist';
        $data['patient'] = $patient;
        $data['lab_request'] = $lab_request;
        $data['doc_sign'] = $doc_sign[0];
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Nurse/_left_sidebar_nurse')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Nurse/view_lab_request')

            .view('Include/_footer');
    }
    public function editpatient()
    {
        //BluelimeSystem::show_and_die($_POST);
        $patientModel = model('Hospital/PatientModel');
        $visitModel = model('Hospital/VisitModel');
        $billModel = model('Hospital/BillModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        
        $first_name     = $_POST['first_name'];
        $middle_name    = $_POST['middle_name'];
        $last_name      = $_POST['last_name'];
        $date_of_birth  = $_POST['date_of_birth'];
        $sex            = $_POST['sex'];
        $mobile         = $_POST['mobile'];        
        $survey         = $_POST['survey'];        
        $regisrered_by = $_POST['registered_by'];
        $registered_by_name = $_POST['registered_by_name'];
        $vital_id = $_POST['vital_id'];

        $data = [
            'first_name'    => ucwords(strtolower($first_name)),
            'middle_name'   => ucwords(strtolower($middle_name)),
            'last_name'     => ucwords(strtolower($last_name)),
            'date_of_birth' => $date_of_birth,
            'sex'           => $sex,
            'mobile'        => $mobile,
            'survey'        => $survey,
            'registered_by' => $regisrered_by, 
            'registered_by_name' => $registered_by_name
        ];
        
        
        $id = $_POST['id']?? 0;
        $new_id = $_POST['new_id']?? 1;
        
        if (intval($id) != 0 && $new_id !=0){
            $patientModel->update($id,$data);
            
            $visitModel ->where('visit_date >=',date('Y-m-d%'))->where('patient_id',$id)->set(['patient_name'    => ucwords($first_name." ".$middle_name." ".$last_name),'mobile'    => ($mobile)])->update();
            $billModel ->where('bill_status',0)->where('patient_id',$id)->set(['patient_name'    => ucwords($first_name." ".$middle_name." ".$last_name),'mobile'    => ($mobile)])->update();
            $orderMainModel ->where('created_at >=',date('Y-m-d%'))->where('patient_id',$id)->set(['patient_name'    => ucwords($first_name." ".$middle_name." ".$last_name),'mobile'    => ($mobile),'sex'    => ($sex)])->update();    
        }
            
        //$website = "\nBook your Appointment @ www.rankderma.com";
        $branch = $_ENV['BRANCH']??"";
        $welcome_message = "Welcome ". ucwords(strtolower($first_name)) ." " . ucwords(strtolower($middle_name)) . " " . ucwords(strtolower($last_name)) ." to RANK Specialized Dermatology Clinic $branch.\nYour card number is MRN-$id\n";
        //$telegram_bot = "Join Telegram: https://t.me/RankDerma_bot";
       

        BluelimeSystem::send_telegram_message($mobile, $welcome_message);
        if(BluelimeSystem::check_sms_service()){
            BluelimeSystem::send_sms_message($mobile, $welcome_message);
        } 
        return redirect()->to(site_url('/hospital/nurse/vitalsign?id='.$vital_id));
    }

    public function newmedicalcertificate()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $medicalCertificateModel = model('Hospital/MedicalCertificateModel');
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        $patientModel = model('Hospital/PatientModel');
        $visitModel = model('Hospital/VisitModel');
        $userModel = model('System/UserModel');
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $pricelistModel = model('Hospital/PricelistModel');
        $pricelistDetailModel = model('Hospital/PricelistDetailModel');
        $laboratoryResults = model('Hospital/LaboratoryResultsModel');
        $laboratoryResultAttributeModel = model('Hospital/LaboratoryResultAttributeModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        
        
        $patient_id = $_GET['pid'] ?? -1;

        //BluelimeSystem::show_and_die($visit_id);
        
        $patient = $patientModel->find($patient_id);
        

        $patient_visits = $visitModel->where('patient_id',$patient_id)->orderBy('id DESC')->findAll(4);

        $reference = '';
        if(!empty($visit))
            $reference = $visit['extra']['reference'] ?? '';
        
            $encounter_history = BluelimeSystem::get_patient_encounter_history(@$_GET['pid']);
            
            $visit_id  = @$_GET['svid'] ?? @$_GET['vid'];
            $visit = $visitModel->find($visit_id);
            $doctor_id = $visitModel->where('id',$visit_id)->findColumn('doctor_id');
            $doctor = $userModel->find($doctor_id);
            $medical_certificate = $medicalCertificateModel->where('patient_id',$patient_id)->where('visit_id', $visit_id)->orderBy('id','DESC')-> findAll();
        
            // Prescription
    
            $prescription_group = [];
            $prescriptions = $prescriptionModel->where('patient_id', $patient_id)->orderBy('id', 'DESC')->findAll();
            $prescription_details = BluelimeSystem::gather_objects_by_reference($prescriptions,'id','Hospital/PrescriptionDetailModel','prescription_id');
            $compoundings = $compoundingPrescriptionMainModel->where('JSON_EXTRACT(patient_detail, "$.id")', $patient_id)->orderBy('id', 'DESC')->findAll();
            $compounding_details = BluelimeSystem::gather_objects_by_reference($compoundings,'id','Hospital/CompoundingPrescriptionDetailModel','main_id');
    
            foreach($prescriptions as $index =>$row){
                $prescriptions[$index]['details'] = [];
                foreach($prescription_details as $detail){
                    if($row['id'] == $detail['prescription_id'])
                        array_push($prescriptions[$index]['details'], $detail);
                }
            }
    
            foreach($compoundings as $index =>$row){
                $compoundings[$index]['compounding_detail'] = [];
                foreach($compounding_details as $detail){
                    if($row['id'] == $detail['main_id'])
                        array_push($compoundings[$index]['compounding_detail'], $detail);
                }
            }
    
            foreach($prescriptions as $row){
                if(!isset($prescription_group['prescription_group_id_'.$row['prescription_group_id']]))
                    $prescription_group['prescription_group_id_'.$row['prescription_group_id']] = ['prescriptions'=>[], 'compoundings'=>[]];
                array_push($prescription_group['prescription_group_id_'.$row['prescription_group_id']]['prescriptions'], $row);
            }
    
            foreach($compoundings as $row){
                if(!isset($prescription_group['prescription_group_id_'.$row['prescription_group_id']]))
                    $prescription_group['prescription_group_id_'.$row['prescription_group_id']] = ['prescriptions'=>[], 'compoundings'=>[]];
                array_push($prescription_group['prescription_group_id_'.$row['prescription_group_id']]['compoundings'], $row);
            }

             //laboratory result

             $pricelist = $pricelistModel->where('company_id', $patient['company_id'])->find();
             $pricelist_id = $pricelist[0]['id'] ?? BluelimeConfiguration::DEFAULT_PRICE_LIST_ID;
             //$service_list = $pricelistDetailModel->select('service_id as id,service_name')->where('status',1)->where('pricelist_id',$pricelist_id)->orderBy('service_name', 'ASC')->findAll();
             
             $service_list_company = $pricelistDetailModel->select('service_id as id,service_name,service_type,service_price')->where('pricelist_id', $pricelist_id)->where('status',1)->where('service_type <>','Consultation')->orderBy('service_name', 'ASC')->findAll();
     
             $service_id_list = [...BluelimeSystem::collect_field_value_from_object($service_list_company, 'id'), -1];
     
             $service_list = $pricelistDetailModel->select('service_id as id,service_name,service_type,service_price')->whereNotIn('service_id', $service_id_list)->where('pricelist_id', BluelimeConfiguration::DEFAULT_PRICE_LIST_ID)->where('status',1)->where('service_type <>','Consultation')->orderBy('service_name', 'ASC')->findAll();
     
             array_push($service_list, ...$service_list_company);
             
             array_push($service_list, ...[['id'=>BluelimeConfiguration::DISCOUNT_PRICE_AMOUNT_ID, 'service_name' => 'Discount In Amount'],['id'=>BluelimeConfiguration::DISCOUNT_PRICE_PERCENT_ID, 'service_name' => 'Discount In Percent']]);
            
            $related_visits_encounter = [];
            $related_visits_paid_service = [];
            $collected_related_visits = BluelimeSystem::collect_follow_up_visits($visit['follow_up_id'], $visit_id, false);

            //$related_visits_paid_service = $this->get_visits_paid_services([$visit_id]);

            $related_visits_encounter = BluelimeSystem::get_visits_encounters($collected_related_visits);
            $related_visits_paid_service = $this->get_visits_paid_services([$visit_id, ...$collected_related_visits]);

            $collected_related_visits = BluelimeSystem::collect_follow_up_visits($visit['follow_up_id'], $visit_id, false);

            $related_visits_encounter = BluelimeSystem::get_visits_encounters($collected_related_visits);


             
            $order_main = $orderMainModel->where('patient_id', $patient_id)->where('result_status', \BluelimeConfiguration::LAB_RESULT_APPROVED)->findAll();
            $order_main_id_list = BluelimeSystem::collect_field_value_from_object($order_main, 'id');
            if(empty($order_main_id_list))
                $order_main_id_list = [-1];
            //$order_detail = $orderDetailModel->whereIn('order_main_id', $order_main_id_list)->findAll();
            $laboratory_results = $laboratoryResults->whereIn('order_id', $order_main_id_list)->orderBy('order_id', 'DESC')->orderBy('order_detail_id', 'ASC')->orderBy('test_id', 'ASC')->findAll();

            $laboratory_service_id_list = BluelimeSystem::collect_field_value_from_object($laboratory_results,'test_id');
            if(empty($laboratory_service_id_list))
                $laboratory_service_id_list = [-1];
            $laboratoryResultAttribute = $laboratoryResultAttributeModel->whereIn('test_id', $laboratory_service_id_list)->findAll();

            $laboratory_results_compiled = [];
            $one_service_results = [];
            $previous_test_id = -1;
            $previous_order_id = -1;
            $previous_order_detail_id = -1;

            foreach($laboratory_results as $result){
            if(($previous_order_id != $result['order_id']) || ($previous_order_detail_id != $result['order_detail_id']) || ($previous_test_id != $result['test_id'])){
                if(!empty($one_service_results)){
                    $service_index = BluelimeSystem::search_index_in_array($previous_test_id, $service_list, 'id');
                    $service_name = $service_list[$service_index]['service_name'] ?? '--';
                    
                    array_push($laboratory_results_compiled, ['service_name' => $service_name, 'result' => $one_service_results]);
                    $one_service_results = [];
                }
            }
            $flag_color = '';
            $flag = ''; //BluelimeSystem::check_labratory_result_reference_range($laboratoryResultAttribute, $result['attribute_id'], $result['result']);

            if($result['result_flag']>0)
                $flag = 'UP';
            elseif($result['result_flag']>0)
                $flag = 'DOWN';

            if($flag == 'UP'){
                $flag = '<i class="glyphicon glyphicon-arrow-up text-danger"></i>';
                $flag_color = 'red';
            }
            elseif($flag == 'DOWN'){
                $flag = '<i class="glyphicon glyphicon-arrow-down text-danger"></i>';
                $flag_color = 'red';
            }
            
            $temp = [
                'attribute_id' => $result['attribute_id'],
                'attribute_name' => $result['attribute_name'],
                'result' => $result['result'],
                'reference_range' => $result['reference_range'],
                'result_flag' => $flag,
                'flag_color' => $flag_color,
                'created_at' => $result['created_at'],
            ];

            array_push($one_service_results, $temp);

            $previous_test_id = $result['test_id'];
            $previous_order_id = $result['order_id'];
            $previous_order_detail_id = $result['order_detail_id'];
        }

        if(!empty($one_service_results)){
            $service_index = BluelimeSystem::search_index_in_array($previous_test_id, $service_list, 'id');
            $service_name = $service_list[$service_index]['service_name'] ?? '--';
            array_push($laboratory_results_compiled, ['service_name' => $service_name, 'result' => $one_service_results]);
        }
        //BluelimeSystem::show_and_die($laboratory_results_compiled[0]);
        $procedureResultsModel = model('Hospital/ProcedureResultsModel');
        $patient_procedure_history = $procedureResultsModel->where('patient_id',$patient_id)->findAll();
        $distinct_procedures = $procedureResultsModel->distinct()->where('patient_id',$patient_id)->findColumn('procedure_name');
        
        foreach($patient_procedure_history as $index=>$user){
            $patient_procedure_history[$index]['registered_by_name'] = $userModel->where('id', $user['registered_by'])->findColumn('name')[0];
        }
        $encounterHistory = $visitEncounterModel->where('visit_id',$visit_id)->findAll();

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'previousvisit';
        $data['patient'] = $patient;
        $data['visit_idx'] = $visit_id;
        $data['visit'] = $visit;
        $data['patient_visits'] = $patient_visits;
        $data['reference'] = $reference;
        $data['doctor'] = $doctor[0];
        $data['medical_certificate'] = $medical_certificate;
        $data['encounterHistory'] = $encounterHistory;
        $data['prescription_group'] = $prescription_group;
        $data['encounter_history'] = $encounter_history;
        $data['laboratory_results_compiled'] = $laboratory_results_compiled;
        $data['distinct_procedures'] = $distinct_procedures;
        $data['patient_procedure_history'] = $patient_procedure_history;
        $data['related_visits_encounter'] = $related_visits_encounter;
        $data['related_visits_paid_service'] = $related_visits_paid_service;
        

        return view('Include/_header', $data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/Nurse/_left_sidebar_nurse')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Nurse/new_medical_certificate')
        .view('Include/_footer');
    }
    public function addmedicalcertificate()
    {
        $medicalCertificateModel = model('Hospital/MedicalCertificateModel');
        $notificationModel = model('Hospital/NotificationModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];


        $patient_id     = $_POST['patient_id'];
        $patient_name    = ucwords($_POST['first_name']." ".$_POST['middle_name']." ".$_POST['last_name']);
        $treatment_given      = $_POST['treatment_given'] ??"";
        $reference      = $_POST['reference'];
        $diagnosis      = $_POST['diagnosis'];
        $registerd_by  = $logged_user;
        $registered_by_name            = $logged_user_name;
        $visit_id         = $_POST['visit_id'];
        $visit_dates         = $_POST['visit_dates'];
        $next_appointment         = $_POST['next_app'];
        $recommendation = $_POST['recommendation'];
        $mobile = $_POST['mobile'];
        $rest_days = !empty($_POST['rest_days'])? $_POST['rest_days'].' Days' : "";
        $rest_date = !empty($_POST['start_date'])? ' From ' .$_POST['start_date']. ' To ' .$_POST['end_date']:"";

        $data = [
            'patient_id'    => $patient_id,
            'patient_name'   => $patient_name,
            'reference'   => $reference,
            'treatment_given'     => $treatment_given,
            'diagnosis'     => $diagnosis,
            'registerd_by' => $registerd_by,
            'registered_by_name'           => $registered_by_name,
            'visit_id'       => $visit_id,
            'visit_dates'       => $visit_dates,
            'next_appointment'       => $next_appointment,
            'recommendation'      => $recommendation,  
            'mobile'      => $mobile , 
            'rest_days'      => $rest_days .' '. $rest_date , 
        ];

            $id=$medicalCertificateModel->insert($data,true);
            $notfiy_data = [
                'notification_type'=>  "Medical Certificates",
                'description'=> $patient_name,
                'link_type'=> 'medical_certificate_id',
                'link_id'=> $id,
             ];
            $notificationModel->insert($notfiy_data,true);
        
            return redirect()->to(site_url('/hospital/nurse/newmedicalcertificate?pid='.$_POST['patient_id'].'&vid='.$visit_id.'&mid='.$id));
        
    }

    public function deletemedicalcertificate()
    {
        $medicalCertificateModel = model('Hospital/MedicalCertificateModel');
        
        $medicalCertificateModel->where('id',$_GET['mid'])->delete($_GET['mid']);
          
        return redirect()->to(site_url('/hospital/nurse/newmedicalcertificate?vid='.$_GET['vid'].'&pid='.$_GET['pid']));
        
    }
    public function deleteresult()
    {
        
        $procedureResultsModel = model('Hospital/ProcedureResultsModel');
        
        $results = $procedureResultsModel->where('id',$_POST['result_id'])->find($_POST['result_id']);
        //BluelimeSystem::show_and_die($results);
        $procedureResultsModel->where('id',intval($_POST['result_id']))->delete(intval($_POST['result_id']));
          
        return redirect()->to(site_url('/hospital/nurse/procedurefollowup?oid='.$results['order_id'].'&id='.$results['patient_id']));
        
    }
    public function procedurefollowup()
    {
        $order_id = $_GET['oid'] ?? -1;

        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $visitModel = model('Hospital/VisitModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $procedureResultsModel = model('Hospital/ProcedureResultsModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
       
        //$laboratoryResultAttribute = model('Hospital/LaboratoryResultAttribute');
       
        //$notificationModel = model('Hospital/NotificationModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $encounter_history = BluelimeSystem::get_patient_encounter_history($_GET['id']);
        // BluelimeSystem::show_and_die($encounter_history);
       
        //$notificationModel->where('link_id',$_GET['visit_id'])->where('link_type','visit_id')->where('notification_type','Doctors Waiting')->set('status',\BluelimeConfiguration::NOTIFICATION_SEEN)->update();

        $visit_id = $_GET['visit_id'] ?? -1;
        //$doctor_id = $loggeduserdata['id'];
        $patient_id = $_GET['id'];

        $visit = $visitModel->find($visit_id);
        $order_main = $orderMainModel->where('bill_id',$_GET['oid'])->findAll();
        $nurse_order = $billDetailModel->where('bill_id',$_GET['oid'])->where('service_type','Procedure')->findAll();
        foreach($nurse_order as $index => $detail){
            foreach($order_main as $main){
                if($main['bill_id'] == $detail['bill_id']){
                    $nurse_order[$index]['order_comment'] = $main['order_comment'];
                    break;
                }
            }       
        }
        
        $patient_procedure_history = $procedureResultsModel->where('patient_id',$patient_id)->orderBy('id DESC')->findAll()??[];
        $distinct_procedures = $procedureResultsModel->distinct()->where('patient_id',$patient_id)->findColumn('procedure_name');
       
        foreach($patient_procedure_history as $index=>$user){
            $patient_procedure_history[$index]['registered_by_name'] = $userModel->where('id', $user['registered_by'])->findColumn('name')[0];
        }

        $patient = $patientModel->find($patient_id);

        // Prescription

        $prescription_group = [];
        $prescriptions = $prescriptionModel->where('patient_id', $patient_id)->orderBy('id', 'DESC')->findAll();
        $prescription_details = BluelimeSystem::gather_objects_by_reference($prescriptions,'id','Hospital/PrescriptionDetailModel','prescription_id');
        $compoundings = $compoundingPrescriptionMainModel->where('JSON_EXTRACT(patient_detail, "$.id")', $patient_id)->orderBy('id', 'DESC')->findAll();
        $compounding_details = BluelimeSystem::gather_objects_by_reference($compoundings,'id','Hospital/CompoundingPrescriptionDetailModel','main_id');

        foreach($prescriptions as $index =>$row){
            $prescriptions[$index]['details'] = [];
            foreach($prescription_details as $detail){
                if($row['id'] == $detail['prescription_id'])
                    array_push($prescriptions[$index]['details'], $detail);
            }
        }

        foreach($compoundings as $index =>$row){
            $compoundings[$index]['compounding_detail'] = [];
            foreach($compounding_details as $detail){
                if($row['id'] == $detail['main_id'])
                    array_push($compoundings[$index]['compounding_detail'], $detail);
            }
        }

        foreach($prescriptions as $row){
            if(!isset($prescription_group['prescription_group_id_'.$row['prescription_group_id']]))
                $prescription_group['prescription_group_id_'.$row['prescription_group_id']] = ['prescriptions'=>[], 'compoundings'=>[]];
            array_push($prescription_group['prescription_group_id_'.$row['prescription_group_id']]['prescriptions'], $row);
        }

        foreach($compoundings as $row){
            if(!isset($prescription_group['prescription_group_id_'.$row['prescription_group_id']]))
                $prescription_group['prescription_group_id_'.$row['prescription_group_id']] = ['prescriptions'=>[], 'compoundings'=>[]];
            array_push($prescription_group['prescription_group_id_'.$row['prescription_group_id']]['compoundings'], $row);
        }
     
        //BluelimeSystem::show_and_die($nurse_order);
        //$service_list = $serviceModel->select('id,service_name')->where('is_active',1)->findAll();

        $data['patient'] = $patient;
        $data['prescription_group'] = $prescription_group;
        $data['loggeduserdata'] = $loggeduserdata;
        $data['encounter_history'] = $encounter_history;
        $data['visit'] = $visit;
        $data['nurse_order'] = $nurse_order;
        $data['patient_procedure_history'] = $patient_procedure_history;
        $data['distinct_procedures'] = $distinct_procedures;
        $data['active_menu'] = 'nurse_orders';
       

        return view('Include/_header', $data)
                .view('Include/_left_sidebar_top')
                .view('Hospital/Nurse/_left_sidebar_nurse')
                .view('Include/_left_sidebar_bottom')
                .view('Hospital/Nurse/nurse_procedure_followup')
                .view('Include/_footer');
    }
    public function get_procedure_attribute()
    {
        $procedure_id = $_POST['procedure_id']??-1;
        $order_detail_id = $_POST['order_detail_id']??-1;
        
        
        $procedureResultAttributeModel = model('Hospital/ProcedureResultAttributeModel');
        $procedureResultsModel = model('Hospital/ProcedureResultsModel');

        
        $attributes = $procedureResultAttributeModel->where('procedure_id',$procedure_id)->findAll();
       
        // foreach($attributes as $index => $rt){
        //     $attributes[$index]['saved_result'] = $procedureResultsModel->where('procedure_id',$rt['procedure_id'])->where('order_detail_id',$order_detail_id)->where('attribute_id',$rt['id'])->findColumn('result')[0]??"";
        // }
        //BluelimeSystem::show_and_die($attributes);
        
        return BluelimeSystem::convert_array_to_string($attributes);
       
    }
    public function saveresult()
    {
        $result = $_POST['result_detail'] ?? [];
        $procedureResultsModel = model('Hospital/ProcedureResultsModel');
        $notificationModel = model('Hospital/NotificationModel');
        $orderDetailModel = model('Hospital/OrderDetailModel');
        $billModel = model('Hospital/BillModel');

        $loggeduser = BluelimeAuthentication::get_logged_user_data();
        
        $result_detail_data = [];

        foreach($result as $rt_line)
        {
            $rt_line_temp = [
                
                'attribute_name' => $rt_line['attribute_name'],
                'attribute_id' => $rt_line['attribute_id'],
                'result' => $rt_line['result']
            ];
            

            array_push($result_detail_data, $rt_line_temp);
        }
       
        //$ordered_by = $orderMainModel->where('id', $rt_line['order_id'])->findColumn('ordered_by');
        $ordered_by = $billModel->where('id', $rt_line['order_id'])->findColumn('registered_by')[0]??0;
        //BluelimeSystem::show_and_die($ordered_by);
        
        $procedure_result = BluelimeSystem::convert_array_to_string($result_detail_data);
        $data = ['order_detail_id' => $rt_line['order_detail_id'],
                'order_id' => $rt_line['order_id'],
                'patient_id' => $rt_line['patient_id'],
                'procedure_id' => $rt_line['procedure_id'],
                'procedure_name' => $rt_line['procedure_name'],
                'registered_by' => $rt_line['registered_by'],
                'result'    => $procedure_result];
        $notfiy_data = [
            'notification_type'=>  "Nurse Procedure Result",
            'description'=> BluelimeSystem::patient_information($rt_line['patient_id']),
            'link_type'=> 'order_main_id',
            'link_id'=> $rt_line['order_id'],
            'user_id'=> $ordered_by,
            ];
        $notificationModel->insert($notfiy_data,true);
        //BluelimeSystem::show_and_die($result_detail_data);
        if(!empty($result_detail_data)){

            // if(!empty($this->checkresult($result_detail_data))){
            //     foreach($result_detail_data as $rt_detail){
            //         $procedureResultsModel->where('order_detail_id',intval($rt_detail['order_detail_id']))->where('procedure_id',intval($rt_detail['procedure_id']))->delete();
            //     }
            //     //BluelimeSystem::show_and_die($rt_detail);
            //     $procedureResultsModel->insertBatch($result_detail_data);
            // }
            // else{
            //     foreach($result_detail_data as $rt_detail){
            //         $procedureResultsModel->where('order_detail_id',$rt_detail['order_detail_id'])->where('procedure_id',$rt_detail['procedure_id'])->where('deleted_at IS NULL')->findAll();
            //     }
            //     $procedureResultsModel->insertBatch($result_detail_data);
            // }
            $procedureResultsModel->insert($data);
            $orderDetailModel->where('bill_detail_id', $rt_line['order_detail_id'])->update(row:['result_registered'=>1, 'result_date'=>BluelimeSystem::date_now(),'result_registered_by'=>$loggeduser['id']]);
        return '{"status":"success", "value":"'.$rt_line['order_id'].'", "pid":"'.$rt_line['patient_id'].'", "procedure_name":"'.$rt_line['procedure_name'].'", "procedure_id":"'.$rt_line['procedure_id'].'"}';
        }
        
    }
    private function checkresult($result)
    {
        $procedureResultsModel = model('Hospital/ProcedureResultsModel');  
        $rt = $procedureResultsModel->where('order_detail_id',$result[0]['order_detail_id'])->findAll();
        //BluelimeSystem::show_and_die($rt);
        return $rt;

    }  
    public function patientqueue(){
        $visitModel = model('Hospital/VisitModel');
        $notificationModel = model('Hospital/NotificationModel');
        $data['active_menu'] = 'patientqueue';
        
        $visits = $visitModel->where('visit_status',BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN)->orWhere('visit_status',BluelimeConfiguration::VISIT_STATUS_OPEN)->orderBy('doctor_name asc')->orderBy('queue_no asc')->findAll();
        foreach($visits as $index => $visit){
            $visits[$index]['view_status'] = $notificationModel->where('link_id',$visit['id'])->where('user_id',$visit['doctor_id'])->findColumn('status')[0]??0;
        }
        $doctors = $visitModel->distinct()->where('visit_status',BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN)->orWhere('visit_status',BluelimeConfiguration::VISIT_STATUS_OPEN)->findColumn('doctor_name')??[];
        //BluelimeSystem::show_and_die($doctors);
        $data['visits'] = $visits;
        $data['doctors'] = $doctors;
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Hospital/Nurse/_left_sidebar_nurse')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Nurse/patient_queue')

        .view('Include/_footer');
    }

    public function closevisit(){
        $visitModel = model('Hospital/VisitModel');
        $visit_id = $_POST['visit_id'] ?? -1;
        $extra = $_POST['close_reason'] ?? -1;
        
        $data = [
            'visit_status' => BluelimeConfiguration::VISIT_STATUS_CLOSED,
            'visit_closed_date' => date('Y-m-d H:i:s'),
            'extra' => $extra
        ];
        //BluelimeSystem::show_and_die($visit_id);

        if(!$visitModel->update(intval($visit_id), $data))
            BluelimeSystem::set_alert('Update not made on the database',"OK","WARNING");
        return redirect()->to(base_url(). "hospital/nurse");
    }
    public function report_daily_new_patients()
    {
        
        $patientModel = model('Hospital/PatientModel');
        $visitModel = model('Hospital/VisitModel');
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        $selected_date_from = $_POST['selected_date_from'] ?? date('Y-m-d');
        $selected_date_to = $_POST['selected_date_to'] ?? date('Y-m-d');

        $selected_date_from = date('Y-m-d', strtotime($selected_date_from)). " 00:00:00";
        $selected_date_to = date('Y-m-d', strtotime($selected_date_to)). " 23:59:59";

        $daily_new_patients = $patientModel->where('created_at >=', $selected_date_from)->where('created_at <=', $selected_date_to)->findAll();

        $patient_ids = BluelimeSystem::collect_field_value_from_object($daily_new_patients, 'id');
        $patient_new_visit = $visitModel->whereIn('patient_id',[...$patient_ids,-99])->orderBy('id DESC')->findAll();
        $patient_diagnosis = $visitEncounterModel->whereIn('visit_id',[...array_column($patient_new_visit, 'id'),-99])->orderBy('id DESC')->findAll();

        $patient_new_visit = BluelimeSystem::index_object_list_array_by_unique_field($patient_new_visit, 'patient_id');
        $patient_diagnosis = BluelimeSystem::index_object_list_array_by_unique_field($patient_diagnosis, 'visit_id');

        foreach($daily_new_patients as $index => $patient){
            $daily_new_patients[$index]['patient_diagnosis'] = $patient_diagnosis['visit_id_'.($patient_new_visit['patient_id_'.$patient['id']]['id']??-1)]['diagnosis']??[];

            // $daily_new_patients[$index]['patient_new_visit'] = $visitModel->where('patient_id like',$patient['id'])->orderBy('id ASC')->findColumn('id')[0]??-1;
            // $daily_new_patients[$index]['patient_diagnosis'] = $visitEncounterModel->where('visit_id',$daily_new_patients[$index]['patient_new_visit'])->findColumn('diagnosis')[0]??[];
        }
       // BluelimeSystem::show_and_die($daily_new_patients);
       // $daily_new_visit = $visitModel->whereIn('patient_id like',$daily_new_patients)->orderBy('id ASC')->findAll();

        //$nop = $patientModel->where('registered_date like',$selected_date)->findColumn('count(id)');
        
        $data['daily_new_patients'] = $daily_new_patients??[];
        $data['selected_date_from'] = substr($selected_date_from,0,10);
        $data['selected_date_to'] = substr($selected_date_to,0,10);
        //$data['nop'] = $nop[0];
        $data['active_menu'] = 'report';
        $data['active_sub_menu'] = 'report_daily_new_patient';
        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Nurse/_left_sidebar_nurse')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Nurse/daily_patients')

            .view('Include/_footer');
    }

    public function report_diagnosis_by_age_and_sex()
    {
        $type = $_GET['type'] ?? 'dhis';

        $diagnosisModel = model('Hospital/DiagnosisModel');

        $visitEncounterModel = model('Hospital/VisitEncounterModel');

        $date_from = ($_GET['date_from'] ?? date('Y-m-d')). ' 00:00:00';
        $date_to = ($_GET['date_to'] ?? date('Y-m-d')). ' 23:59:59';

        $dhis_translator = $diagnosisModel->findAll();

        $encounterHistory = $visitEncounterModel->where('created_at >= ',$date_from)->where('created_at <= ',$date_to)->findAll();
        $visits = BluelimeSystem::gather_objects_by_reference($encounterHistory,'visit_id','Hospital/VisitModel','id',true);
        $patients = BluelimeSystem::gather_objects_by_reference($visits,'patient_id','Hospital/PatientModel','id',true);
        foreach($encounterHistory as $index => $history){
            $patient_id = $visits['id_'.$history['visit_id']]['patient_id'] ?? -99;
            $patient = $patients['id_'.$patient_id] ?? [];
            if(empty($patient)){
                unset($encounterHistory[$index]);
                continue;
            }
            $encounterHistory[$index]['age'] = BluelimeSystem::year_difference($patient['date_of_birth'],$history['created_at']);
            $encounterHistory[$index]['sex'] = $patient['sex'];

            //if($type == 'dhis'){
            foreach($encounterHistory[$index]['diagnosis'] as $diagnosis_index => $diagnosis){
                $title = $encounterHistory[$index]['diagnosis'][$diagnosis_index]['title'];
                $dhis_name = 'Other.';
                if($type == 'dhis')
                    $dhis_name = 'Other';

                foreach($dhis_translator as $dhis_diagnosis){
                    if(trim(strtolower($title)) == trim(strtolower($dhis_diagnosis['description']))){
                        $dhis_name = $dhis_diagnosis['name'];
                        break;
                    }
                }
                $encounterHistory[$index]['diagnosis'][$diagnosis_index]['title'] = $dhis_name;
                $encounterHistory[$index]['diagnosis'][$diagnosis_index]['doctors_diagnosis'] = $title;
            }
            //}
        }

        $dhis_translator = $diagnosisModel->groupBy('name')->findColumn('name');
        
        $data['active_menu'] = 'reports'; 
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['encounterHistory'] = $encounterHistory;
        $data['dhis_translator'] = $dhis_translator;
        $data['active_menu'] = 'report';
        $data['active_sub_menu'] = 'report_diagnosis_dhis';
        if($type != 'dhis')
            $data['active_sub_menu'] = 'report_diagnosis_doctors';

        //BluelimeSystem::print_associated_array($encounterHistory);
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Hospital/Nurse/_left_sidebar_nurse')
            .view('Include/_left_sidebar_bottom')
            .(($type != 'dhis') ? view('Hospital/Nurse/report_diagnosis_doctors'):view('Hospital/Nurse/report_diagnosis'))
            .view('Include/_footer');
    }
  
    public function report_patient_count_by_age()
    {
        $visitModel = model('Hospital/VisitModel');

        $date_from = ($_GET['date_from'] ?? date('Y-m-d')). ' 00:00:00';
        $date_to = ($_GET['date_to'] ?? date('Y-m-d')). ' 23:59:59';

        // Patient count
        $visits = $visitModel->select('patient_id, MAX(created_at) as created_at')->where('created_at >=', $date_from)->where('created_at <=',$date_to)->where('visit_status !=', BluelimeConfiguration::VISIT_STATUS_NEW)->groupBy('patient_id')->findAll();
        $patients = BluelimeSystem::gather_objects_by_reference($visits,'patient_id','Hospital/PatientModel','id',true);

        foreach($visits as $visit){
            $patient_id = $visit['patient_id'];
            $patient = $patients['id_'.$patient_id] ?? [];
            if(empty($patient)){
                continue;
            }
            $patients['id_'.$visit['patient_id']]['age'] = BluelimeSystem::year_difference( $visit['created_at'], $patients['id_'.$visit['patient_id']]['date_of_birth']);
        }
        
        $data['patient_count'] = $patients;
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['active_menu'] = 'report';
        $data['active_sub_menu'] = 'report_patient_count';

        //BluelimeSystem::print_associated_array($encounterHistory);
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Hospital/Nurse/_left_sidebar_nurse')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Nurse/report_patient_count')
            .view('Include/_footer');
    }

    public function update_dhis_translation()
    {
        $diagnosisModel = model('Hospital/DiagnosisModel');
        $diagnosis_name = $_POST['diagnosis_name'] ?? '';
        $dhis_translation = $_POST['dhis_translation'] ?? '';

        $diagnosisModel->where('description', $diagnosis_name)->delete();
        
        if(trim($dhis_translation) != '')
            $diagnosisModel->insert(['name'=>ucfirst(trim($dhis_translation)), 'description'=>$diagnosis_name]);

        return BluelimeSystem::get_success_ajax_return('');
    }

    public function srv_form(): string
    {
        $srvMainModel = model('Inventory/SRVMainModel');
        $sivMainModel = model('Inventory/SIVMainModel');
        $sivDetailModel = model('Inventory/SIVDetailModel');
        $itemModel = model('/Inventory/ItemModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');
        $transactionModel = model('Inventory/TransactionModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user_id = $loggeduserdata['id'];
        $default_store_id = $loggeduserdata['extra']['default_store'] ?? -1;
        $search_key = $_GET['search_key'] ?? '';
        $position = $_GET['position'] ?? '';
        
        if(trim($search_key) != '')
            $srvMainModel = $srvMainModel->where('id LIKE', "%$search_key%")->orWhere('srv_code LIKE', "%$search_key%");

        $srv_main = $srvMainModel->where('is_issued' ,0)->where('requesting_store' ,4)->orderBy('id','DESC')->findAll(10,0);
        foreach($srv_main as $index => $store){
            $srv_main[$index]['requesting_store'] = $storeModel->where('id',$store['requesting_store'])->findColumn('store_name')[0];
            $srv_main[$index]['requested_store'] = $storeModel->where('id',$store['requested_store'])->findColumn('store_name')[0];
        }
        foreach($srv_main as $index => $user){
            $srv_main[$index]['requested_by__name'] = $userModel->where('id',$user['requested_by'])->findColumn('name')[0];
        }

        
        //BluelimeSystem::show_and_die($siv_main);

        $transactions = $transactionModel->select('item_id, SUM(quantity) as quantity')->where('store_id', 1)->groupBy('item_id')->findAll();
        $items = $itemModel->findAll();
        foreach($items as $index => $item){
            $items[$index]['current_balance'] = 0;
            foreach($transactions as $transaction){
                if($transaction['item_id'] == $item['id']){
                    $items[$index]['current_balance'] = $transaction['quantity'];
                    break;
                }
            }
        }

        //issued

        if((!empty($position)) && (!empty($filter_date))){
            if($position == 'before')
                $sivMainModel = $sivMainModel->where('created_at <=', $filter_date);
            else
                $sivMainModel = $sivMainModel->where('created_at >=', $filter_date);
        }

        if(trim($search_key) != '')
            $sivMainModel = $sivMainModel->groupStart()->where('srv_id LIKE', "%$search_key%")->orWhere('id LIKE', "%$search_key%")->orWhere('description LIKE', "%$search_key%")->groupEnd();

        $siv_main = $sivMainModel->where('requesting_store' ,4)->where('status',BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST)->orderBy('id','DESC')->paginate(BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST);
        $pager = $sivMainModel->pager;

        $stores = $storeModel->findAll();
        
        foreach($siv_main as $index => $siv){
            foreach($stores as $store){
                if($store['id'] == $siv['requesting_store']){
                    $siv_main[$index]['store_name'] = $store['store_name'];
                    break;
                }
            }
            foreach($stores as $store){
                if($store['id'] == $siv['issuing_store']){
                    $siv_main[$index]['issuing_store'] = $store['store_name'];
                    break;
                }
            }
        }

        $data['active_menu'] = 'srv_form';
        $data['srv_main'] = $srv_main;
        $data['siv_main'] = $siv_main;
        $data['stores'] = $stores;
        $data['items'] = $items;
        $data['logged_user'] = $loggeduserdata;
        $data['position'] = $position;
        $data['pager'] = $pager;
        $data['page'] = $pager->getCurrentPage();

        return  view('Include/_header',$data)
            .view('Include/_left_sidebar_top')
            .view('Hospital/Nurse/_left_sidebar_nurse')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Nurse/srv_form')
            .view('Include/_footer');     
    }

    public function srvsave()
    {
        $srv_main = $_POST['srv_main'] ?? [];
        
        $srv_detail = $_POST['srv_detail'] ?? [];
        
        $srvMainModel = model('Inventory/SRVMainModel');
        $srvDetailModel = model('Inventory/SRVDetailModel');

        $srv_main_id = $srvMainModel->insert($srv_main);
        
        $srv_detail_data = [];

        foreach($srv_detail as $srv_line)
        {
            $srv_line_temp = [
                'srv_id' => $srv_main_id,
                'item_id' => $srv_line['id'],
                'item_detail' => BluelimeSystem::convert_array_to_string(['item_code'=> $srv_line['item_code'],'item_category'=> $srv_line['item_category'],'item_generic_name'=> $srv_line['item_generic_name'],'item_brand_name'=> $srv_line['item_brand_name'],'measurement'=> $srv_line['measurement']]),
                'quantity' => $srv_line['quantity']
            ];

            array_push($srv_detail_data, $srv_line_temp);
        }

        if(!empty($srv_detail_data))
            $srvDetailModel->insertBatch($srv_detail_data);

        return '{"status":"success", "value":"'.$srv_main_id.'"}';
        
    }
    public function siv_print(): string
    {
        $siv_id = $_GET['siv_id'] ?? -1;
        $sivMainModel = model('Inventory/SIVMainModel');
        $sivDetailModel = model('Inventory/SIVDetailModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');

        $siv_main = $sivMainModel->find($siv_id);

        
        $issuing_store_name = $storeModel->where('id',$siv_main['issuing_store'])->findColumn('store_name');
        $requesting_store_name = $storeModel->where('id',$siv_main['requesting_store'])->findColumn('store_name');
        
        $siv_detail = $sivDetailModel->where('siv_id', $siv_id)->findAll();

        $issued_by = $userModel->where('id',$siv_main['issued_by'])->findColumn('name');
        $received_by = $userModel->where('id',$siv_main['recieved_by'])->findColumn('name');
        $siv_main['issued_by_name'] = $issued_by[0] ?? '';
        $siv_main['received_by_name'] = $received_by[0] ?? '';
        
        $siv_main['issuing_store_name'] = $issuing_store_name[0];
        $siv_main['requesting_store_name'] = $requesting_store_name[0];
        $data = [
            'siv_main' => $siv_main,
            'siv_detail' => $siv_detail,
        ];

        $data['active_menu'] = 'srv_form';
        
        return view('Hospital/Nurse/siv_print', $data);
    }

    public function delete_srv(){
        $srv_id = $_GET['srv_id'];
        $srvMainModel = model('Inventory/SRVMainModel');

        $srv = $srvMainModel->find($srv_id);

        $is_issued = $srv['is_issued'] ?? 1;

        if(!$is_issued)
            $srvMainModel->delete($srv_id);
        
        return redirect()->to('/hospital/nurse/srv_form'); 
    }

    public function srv_print(): string
    {
        $srv_id = $_GET['srv_id'] ?? -1;
        $srvMainModel = model('Inventory/SRVMainModel');
        $srvDetailModel = model('Inventory/SRVDetailModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');
        

        $stores = $storeModel->findAll();

        $srv_main = $srvMainModel->find($srv_id);
        $srv_detail = $srvDetailModel->where('srv_id', $srv_id)->findAll();
        $requested_by = $userModel->where('id',$srv_main['requested_by'])->findColumn('name');

        foreach($stores as $store){
            if($store['id'] == $srv_main['requesting_store'])
                $srv_main['requesting_store'] = $store['store_name'];

            if($store['id'] == $srv_main['requested_store'])
                $srv_main['requested_store'] = $store['store_name'];
        }

        $srv_main['requested_by'] = $requested_by;

        $data = [
            'srv_main' => $srv_main,
            'srv_detail' => $srv_detail,
        ];
        $data['active_menu'] = 'srv_form';
       
        
        return view('Hospital/Nurse/srv_print', $data);
    }

    public function approve_siv(){
        $siv_id = $_GET['siv_id'] ?? -1;
        $sivMainModel = model('Inventory/SIVMainModel');
        $sivDetailModel = model('Inventory/SIVDetailModel');
        $transactionModel = model('Inventory/TransactionModel');
        $itemModel = model('Inventory/ItemModel');
        $loggeduserdata = BluelimeAuthentication::get_logged_user_data();

        $siv = $sivMainModel->find($siv_id);
        if($siv['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST){
            $sivMainModel->update($siv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND, 'recieved_by' => $loggeduserdata['id']]);

            $siv_details = $sivDetailModel->where('siv_id', $siv_id)->findAll();
            $transaction_data = [];
            $total_quantity = 0;
            foreach($siv_details as $siv_line){
                $compunding_quantity = $siv_line['quantity'];
                if($siv['requesting_store'] == 3)
                    $compunding_quantity = $siv_line['quantity'] * $siv_line['item_detail']['compounding_volume'];

                $transaction_data[] = [
                    'store_id' => $siv['issuing_store'],
                    'item_id' => $siv_line['item_id'],
                    'batch_number' => $siv_line['batch_no'],
                    'expiry_date' => ($siv_line['expiry_date']=='0000-00-00' ? NULL : $siv_line['expiry_date']),
                    'transaction_type' => BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::DISPENSE_INTERNAL,
                    'transaction_reference' => $siv['id'],
                    'quantity' => round(-1 * $siv_line['quantity'], 2)
                ];

                $transaction_data[] = [
                    'store_id' => $siv['requesting_store'],
                    'item_id' => $siv_line['item_id'],
                    'batch_number' => $siv_line['batch_no'],
                    'expiry_date' => ($siv_line['expiry_date']=='0000-00-00' ? NULL : $siv_line['expiry_date']),
                    'transaction_type' => BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::RECEIVE_INTERNAL,
                    'transaction_reference' => $siv['id'],
                    'quantity' => round( $compunding_quantity, 2)
                ];
                $total_quantity += $siv_line['quantity'];
            }

            if(!empty($transaction_data))
                $transactionModel->insertBatch($transaction_data);

            //$issued_items_id_list = BluelimeSystem::collect_field_value_from_object($siv_details,'item_id');
            //$itemModel->whereIn('id', $issued_items_id_list)->update(row:['is_active' => 1]);

            $message = 'You have approved SIV '.$siv['id']."\n# Items: ".count($siv_details)."\n# Total qty: ".$total_quantity;
            $message_detail = '';
            foreach($siv_details as $siv_line){
                $message_detail .= '> '.$siv_line['quantity'].' '.$siv_line['item_detail']['item_brand_name'].' '.$siv_line['item_detail']['item_generic_name']."\n";
            }
            BluelimeSystem::send_sms_message($loggeduserdata['mobile'], $message);
            BluelimeSystem::send_telegram_message($loggeduserdata['mobile'], $message."\n\n".$message_detail);
        }
        return redirect()->to('/hospital/nurse/siv_print?siv_id='.$siv_id);
    }

    public function reverse_approved_siv(){
        $siv_id = $_GET['siv_id'] ?? -1;
        $sivMainModel = model('Inventory/SIVMainModel');
        $transactionModel = model('Inventory/TransactionModel');

        $siv = $sivMainModel->find($siv_id);

        if($siv['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND){
            $sivMainModel->update($siv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST]);

            $transactionModel->whereIn('transaction_type', [BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::DISPENSE_INTERNAL, BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::RECEIVE_INTERNAL])
                ->where('transaction_reference', $siv['id'])
                ->delete();
        }
        return redirect()->to('/hospital/nurse/siv_print?siv_id='.$siv_id);
    }
    public function referalrequestprint()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        
        $patient = $patientModel->find(@$_GET['id']);

        $external_referal_request = $externalDocumentsModel->where('id',$_GET['rrid'])->where('request_type','REFERAL_REQUEST')->orderBy('id','DESC')-> findAll();
        //BluelimeSystem::show_and_die($external_lab_request[0]['doctor_id']);
        $doc_sign = $loggeduserdata['digital_signature'];
        $doc_id = $loggeduserdata['id'];
        $doctor = $userModel->find($external_referal_request[0]['doctor_id']);

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['external_referal_request'] = $external_referal_request;
        $data['doc_sign'] = $doc_sign;
        $data['doctor'] = $doctor;
        $data['doc_id'] = $doc_id;
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Nurse/_left_sidebar_nurse')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Nurse/view_referal_request')

            .view('Include/_footer');
    }
    public function biopsyrequestprint()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        
        $patient = $patientModel->find(@$_GET['id']);

        $external_biopsy_request = $externalDocumentsModel->where('id',$_GET['ebid'])->where('request_type','BIOPSY_REQUEST')->orderBy('id','DESC')-> findAll();
        //BluelimeSystem::show_and_die($external_lab_request[0]['doctor_id']);
        $doc_sign = $loggeduserdata['digital_signature'];
        $doc_id = $loggeduserdata['id'];
        $doctor = $userModel->find($external_biopsy_request[0]['doctor_id']);

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['external_biopsy_request'] = $external_biopsy_request;
        $data['doc_sign'] = $doc_sign;
        $data['doctor'] = $doctor;
        $data['doc_id'] = $doc_id;
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Nurse/_left_sidebar_nurse')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Nurse/view_external_biopsy')

            .view('Include/_footer');
    }    
    public function labrequestprint()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        
        $patient = $patientModel->find(@$_GET['id']);

        $external_lab_request = $externalDocumentsModel->where('id',$_GET['elid'])->where('request_type','LABORATORY_REQUEST')->orderBy('id','DESC')->findAll();
        //BluelimeSystem::show_and_die($external_lab_request[0]['doctor_id']);
        $doc_sign = $loggeduserdata['digital_signature'];
        $doc_id = $loggeduserdata['id'];
        $doctor = $userModel->find($external_lab_request[0]['doctor_id']);

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['external_lab_request'] = $external_lab_request;
        $data['doc_sign'] = $doc_sign;
        $data['doctor'] = $doctor;
        $data['doc_id'] = $doc_id;
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Nurse/_left_sidebar_nurse')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Nurse/view_external_laboratory')

            .view('Include/_footer');
    }

    public function biopsyorders()
    {
        
        $orderMainModel = model('Hospital/OrderMainModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
       
        
        $orders = $orderMainModel->where('order_service_type','Pathology')->where('order_status >=',1)->where('order_status <=',5)->findAll();
        
        foreach($orders as $index => $rt){
            $orders[$index]['patient_detail'] = $patientModel->where('id',$rt['patient_id'])->findAll()[0]??[];
            $orders[$index]['doctor'] = $userModel->where('id',$rt['ordered_by'])->findColumn('name')[0]??"Reception";
        } 
        
        $orders_result = $orderMainModel->where('order_service_type','Pathology')->where('order_status',6)->findAll();
        
        foreach($orders_result as $index => $rt){
            $orders_result[$index]['patient_detail'] = $patientModel->where('id',$rt['patient_id'])->findAll()[0]??[];
            $orders_result[$index]['doctor'] = $userModel->where('id',$rt['ordered_by'])->findColumn('name')[0]??"Reception";
        }
        //BluelimeSystem::show_and_die($orders);
        $data['active_menu'] = 'biopsy_followup';
        $data['orders'] = $orders;
        $data['orders_result'] = $orders_result;
        
        return 
        view('Include/_header')
        .view('Include/_left_sidebar_top', $data)

        .view('Hospital/Nurse/_left_sidebar_nurse')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Nurse/pending_biopsy_orders.php')

        .view('Include/_footer');
    }
    public function order_detail()
    {
        $order_main_id = $_GET['order_main_id'];
        $biopsyResultModel = model('Hospital/BiopsyResultModel');
        $orderDetailModel = model('Hospital/OrderDetailModel');
        $serviceModel = model('Hospital/ServiceModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $templateModel = model('Hospital/TemplateModel');
        
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
       
        
        $biopsy_results = $biopsyResultModel->where('order_id',$order_main_id)->findAll();
        $orders = $orderMainModel->where('id',$order_main_id)->findAll();
        $templates = $templateModel->findAll();
        
        foreach($orders as $index => $rt){
            $orders[$index]['patient_detail'] = $patientModel->where('id',$rt['patient_id'])->findAll()[0]??[];
        }
        
        $services = $serviceModel->where('service_type','Pathology')->findAll();

        $order_detail = $orderDetailModel->where('order_main_id',$order_main_id)->findAll();
        foreach($order_detail as $index => $detail){
            foreach($services as $service){
                if($service['id'] == $detail['ordered_service_id']){
                    $order_detail[$index]['test_name'] = $service['service_name'];
                    break;
                }
            }       
        }
        $template=$templateModel->where('template_type','PATHOLOGY')->findAll(1)[0];
        
        
        $data['biopsy_results'] = $biopsy_results;
        $data['template'] = $template;
        //BluelimeSystem::show_and_die($order_detail);
        $data['active_menu'] = 'pending_order';
        $data['templates'] = $templates;
        $data['loggeduserdata'] = $loggeduserdata;
        $data['order_detail'] = $order_detail;
        $data['order_main'] = $orders;
        
        return 
        view('Include/_header')
        .view('Include/_left_sidebar_top', $data)

        .view('Hospital/Nurse/_left_sidebar_nurse')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Nurse/biopsy_order_detail')

        .view('Include/_footer');
    }
    public function change_sample_status(){
        $order_id = $_GET['order_id'] ?? -1;
        $note = $_GET['note'] ?? '';
        $orderMainModel = model('Hospital/OrderMainModel');

        $order = $orderMainModel->find($order_id);
       // BluelimeSystem::show_and_die($order['order_status']);
        $orderMainModel->update($order_id, ['order_status' => (intval($order['order_status'])+1),'order_comment'=>$note]);
        return redirect()->to('/hospital/nurse/order_detail?order_main_id='.$order_id);
    }

    public function reverse_sample_status(){
        $order_id = $_GET['order_id'] ?? -1;
        $orderMainModel = model('Hospital/OrderMainModel');

        $order = $orderMainModel->find($order_id);
        $orderMainModel->update($order_id, ['order_status' => (intval($order['order_status'])-1)]);
        return redirect()->to('/hospital/nurse/order_detail?order_main_id='.$order_id);
    }
    public function viewbiopsyresult()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $biopsyResultModel = model('Hospital/BiopsyResultModel');
        
        $userModel = model('System/UserModel');
        $notificationModel = model('Hospital/NotificationModel');
        $notificationModel->where('link_id',$_GET['brid']??-1)->where('notification_type','Medical Report')->set('status',\BluelimeConfiguration::NOTIFICATION_SEEN)->update();
        $userModel = model('System/UserModel');

        //$patient = $patientModel->find(@$_GET['id']);

        $biopsy_result = $biopsyResultModel->where('id like',$_GET['brid']??'%')->where('doctor_id',$loggeduserdata['id'])->orderBy('id','DESC')-> findAll();
        //BluelimeSystem::show_and_die($biopsy_result);  
        $doc_sign = $userModel->where('id',$biopsy_result[0]['doctor_id'])->findColumn('digital_signature'); 
            
        $doctor = $userModel->find($biopsy_result[0]['doctor_id']);

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        //$data['patient'] = $patient;
        $data['doctor'] = $doctor;
        $data['biopsy_result'] = $biopsy_result;
        $data['doc_sign'] = $doc_sign[0];
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Nurse/_left_sidebar_nurse')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Nurse/view_biopsy_result')

            .view('Include/_footer');
    } 
    public function uploadbiopsyrequest()
    {
        $order_id     = $_POST['order_id'];
        
        $image_list = $this->upload_biopsy_request();

        return redirect()->to(site_url('/hospital/reception/order_detail?order_main_id='.$order_id.'&image_list='.$image_list));
    } 
    private function upload_biopsy_request($quality = -1){
        $orderMainModel = model('Hospital/OrderMainModel');
        
        $image_list = trim($_POST['image_list'] ?? '');
        
        $order_id     = $_POST['order_id'];
       
        $originalImage = $_FILES['picture']['tmp_name'];
        
        $fl1Name = sprintf('%05d', $order_id).'_'.date('Ymdhms');
        
        $file_path = $_ENV['MANUAL_CARD_PATH'];

        if(is_uploaded_file($originalImage))
        {
            $i = 0;
            
            while(file_exists($file_path.$fl1Name.'.jpg'))
                $fl1Name = $fl1Name.'_'.$i;
            
            //move_uploaded_file($originalImage,$_ENV['MANUAL_CARD_PATH'].$fl1Name.'.jpg');

            switch (exif_imagetype($originalImage)) {
                case IMAGETYPE_PNG:
                    $imageTmp=imagecreatefrompng($originalImage);
                    break;
                case IMAGETYPE_JPEG:
                    $imageTmp=imagecreatefromjpeg($originalImage);
                    break;
                case IMAGETYPE_GIF:
                    $imageTmp=imagecreatefromgif($originalImage);
                    break;
                case IMAGETYPE_BMP:
                    $imageTmp=imagecreatefrombmp($originalImage);
                    break;
                // Defaults to JPG
                default:
                    $imageTmp=imagecreatefromjpeg($originalImage);
                    break;
            }
            // TODO Rotet image if it is not correct

            imagejpeg($imageTmp,$file_path.$fl1Name.'.jpg',$quality);
            imagedestroy($imageTmp);
            $img = $orderMainModel->where('id',$order_id)->find()[0];
            $upload_img = $img['scanned_documents']==""?$fl1Name.'.jpg':$img['scanned_documents'].','.$fl1Name.'.jpg';
            
        
            $rt = $orderMainModel->update($order_id, ['scanned_documents' => $upload_img]);
           // BluelimeSystem::show_and_die($rt);
            if($image_list != '')
                $image_list .= ',';

            $image_list .= $fl1Name.'.jpg';

        }
        return $image_list;
    }


     public function newmanualcard_mobile()
    {
        $loggeduserdata = BluelimeAuthentication::get_logged_user_data();
        $patientModel = model('Hospital/PatientModel');
        $patient = $patientModel->find($_GET['id']);
        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        return view('Hospital/Nurse/new_manualcard_mobile', $data);
    }
    public function addmanualcard()
    {
        $patient_id     = $_POST['patient_id'];
        $visit_id     = $_POST['visit_id'];
        
        $image_list = $this->upload_manual_card();

        return redirect()->to(site_url('/hospital/nurse/vitalsign?id='.$visit_id.'&image_list='.$image_list));
    } 
     
    public function addmanualcard_mobile()
    {
        $patient_id     = $_POST['patient_id'];
        
        $image_list = $this->upload_manual_card(10);

        return redirect()->to(site_url('/hospital/nurse/newmanualcard_mobile?id='.$patient_id.'&image_list='.$image_list));
    } 
    private function upload_manual_card($quality = -1){
        $patientModel = model('Hospital/PatientModel');
        
        $image_list = trim($_POST['image_list'] ?? '');
        
        $patient_id     = $_POST['patient_id'];
       
        $originalImage = $_FILES['picture']['tmp_name'];
        
        $fl1Name = sprintf('%05d', $patient_id).'_'.date('Ymdhms');

        $file_path = $_ENV['MANUAL_CARD_PATH'];

        if(is_uploaded_file($originalImage))
        {
            $i = 0;
            
            while(file_exists($file_path.$fl1Name.'.jpg'))
                $fl1Name = $fl1Name.'_'.$i;
            
            //move_uploaded_file($originalImage,$_ENV['MANUAL_CARD_PATH'].$fl1Name.'.jpg');

            switch (exif_imagetype($originalImage)) {
                case IMAGETYPE_PNG:
                    $imageTmp=imagecreatefrompng($originalImage);
                    break;
                case IMAGETYPE_JPEG:
                    $imageTmp=imagecreatefromjpeg($originalImage);
                    break;
                case IMAGETYPE_GIF:
                    $imageTmp=imagecreatefromgif($originalImage);
                    break;
                case IMAGETYPE_BMP:
                    $imageTmp=imagecreatefrombmp($originalImage);
                    break;
                // Defaults to JPG
                default:
                    $imageTmp=imagecreatefromjpeg($originalImage);
                    break;
            }
            // TODO Rotet image if it is not correct

            imagejpeg($imageTmp,$file_path.$fl1Name.'.jpg',$quality);
            imagedestroy($imageTmp);
            $img = $patientModel->where('id',$patient_id)->find()[0];
            $upload_img = $img['attached_old_card']==""?$fl1Name.'.jpg':$img['attached_old_card'].','.$fl1Name.'.jpg';
            
            $patientModel->update($patient_id, ['attached_old_card' => $upload_img]);
            if($image_list != '')
                $image_list .= ',';

            $image_list .= $fl1Name.'.jpg';

        }
        return $image_list;
    }
    public function manualcard(){
        ob_clean();
        header('Content-type: image');

        $image = $_GET['image'];
        $thumbnail = $_GET['thumbnail'] ?? false;
        $file_path = $file_path = $_ENV['MANUAL_CARD_PATH'];

        $im = imagecreatefromjpeg($file_path.$image);
        if($thumbnail)
            $im = imagescale($im,1000);
        imagejpeg($im, null,100);
        imagedestroy($im);
        die();
    }

    function delete_manual_card(){
        $image_name = $_POST['image_name'];
        $patient_id = $_POST['patient_id'] ?? -1;
        $patientModel = model('Hospital/PatientModel');
        $patient = $patientModel->find($patient_id);

        $attached_old_card = $patient['attached_old_card'];
        $attached_old_card = trim(str_replace($image_name,'',$attached_old_card),',');

        $image_path = $_ENV['PROGRESS_IMAGE_PATH'].$image_name;

        $patientModel->update($patient_id, ['attached_old_card'=>$attached_old_card]);

        if(is_file($image_path)){
            unlink($image_path);
        }
        return BluelimeSystem::get_success_ajax_return('');
    }

     public function addprogresspicture()
    {
        $visit_id = $_POST['visit_id'];
        $patient_id     = $_POST['patient_id'];
        $this->upload_progress_picture();

        return redirect()->to(site_url('/hospital/nurse/newprogresspicture?id='.$patient_id.'&vid='.$visit_id));
    }

    public function addprogresspicture_mobile()
    {
        $visit_id = $_POST['visit_id'];
        $logged_user_id = $_POST['logged_user_id'] ?? -1;
        $patient_id     = $_POST['patient_id'];
        
        $image_list = $this->upload_progress_picture();

        return redirect()->to(site_url('/hospital/nurse/newprogresspicture_mobile?id='.$patient_id.'&vid='.$visit_id.'&logged_user_id='.$logged_user_id.'&image_list='.$image_list));
    }

    private function upload_progress_picture(){
        $progressPictureModel = model('Hospital/ProgressPictureModel');
        $userModel = model('System/UserModel');
       
        $image_list = trim($_POST['image_list'] ?? '');
        $visit_id = $_POST['visit_id'];
        $logged_user_id = $_POST['logged_user_id'] ?? -1;
        $user = $userModel->find($logged_user_id);
        $registered_by_name     = $user['name'] ?? '';
        $registered_by     = $logged_user_id;
        $patient_id     = $_POST['patient_id'];
        $comment     = $_POST['comment'];
        $data = [
            //'picture_location' => $picture_location,
            'registered_by_name' => $registered_by_name,
            'visit_id' => $visit_id,
            'registered_by'    => $registered_by,
            'patient_id'    => $patient_id,
            'comment'    => $comment
        ];

        $id = $progressPictureModel->insert($data,true);

        $originalImage = $_FILES['picture']['tmp_name'];
        
        $fl1Name = sprintf('%05d', $patient_id).'_'.date('Ymd').'_'.sprintf('%05d', $id);

        $file_path = $_ENV['PROGRESS_IMAGE_PATH'];

        if(is_uploaded_file($originalImage))
        {
            $i = 0;
            
            while(file_exists($file_path.$fl1Name.'.jpg'))
                $fl1Name = $fl1Name.'_'.$i;
            
            //move_uploaded_file($originalImage,$_ENV['PROGRESS_IMAGE_PATH'].$fl1Name.$extension);

            switch (exif_imagetype($originalImage)) {
                case IMAGETYPE_PNG:
                    $imageTmp=imagecreatefrompng($originalImage);
                    break;
                case IMAGETYPE_JPEG:
                    $imageTmp=imagecreatefromjpeg($originalImage);
                    break;
                case IMAGETYPE_GIF:
                    $imageTmp=imagecreatefromgif($originalImage);
                    break;
                case IMAGETYPE_BMP:
                    $imageTmp=imagecreatefrombmp($originalImage);
                    break;
                // Defaults to JPG
                default:
                    $imageTmp=imagecreatefromjpeg($originalImage);
                    break;
            }
            // TODO Rotet image if it is not correct

            imagejpeg($imageTmp,$file_path.$fl1Name.'.jpg');
            imagedestroy($imageTmp);
            $progressPictureModel->update($id, ['picture_location' => $fl1Name.'.jpg']);
            if($image_list != '')
                $image_list .= ',';

            $image_list .= $fl1Name.'.jpg';

        }
        return $image_list;
    }
    public function progresspicture(){
        ob_clean();
        header('Content-type: image');

        $image = $_GET['image'];
        $thumbnail = $_GET['thumbnail'] ?? false;
        $file_path = $file_path = $_ENV['PROGRESS_IMAGE_PATH'];

        $im = imagecreatefromjpeg($file_path.$image);
        if($thumbnail)
            $im = imagescale($im,1000);
        imagejpeg($im, null,100);
        imagedestroy($im);
        die();
    }

    private function get_image_content($image_path, $file_name){
        
    }

    public function newprogresspicture()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $progressPictureModel = model('Hospital/ProgressPictureModel');
        $patientModel = model('Hospital/PatientModel');
        
        $patient = $patientModel->find(@$_GET['id']);
        $progress_picture = $progressPictureModel->where('patient_id',$_GET['id'])->orderBy('registered_date','DESC')-> findAll();

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['visit_id']=$_GET['vid'];
        $data['progress_picture'] = $progress_picture;
        
        return view('Include/_header', $data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/Nurse/_left_sidebar_nurse')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Nurse/new_progress_picture')
        .view('Include/_footer');
    }
    public function newprogresspicture_mobile()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $progressPictureModel = model('Hospital/ProgressPictureModel');
        $patientModel = model('Hospital/PatientModel');
        
        $patient = $patientModel->find(@$_GET['id']);
        $progress_picture = $progressPictureModel->where('patient_id',$_GET['id'])->orderBy('id','DESC')-> findAll();

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['visit_id']=$_GET['vid'];
        $data['progress_picture'] = $progress_picture;
        
        return view('Hospital/Nurse/new_progress_picture_mobile', $data);
    }

    public function delete_progress_picutre(){
        $progress_picture_id = $_POST['progress_picture_id'] ?? -1;
        $progressPictureModel = model('Hospital/ProgressPictureModel');
        $progress_picture = $progressPictureModel->find($progress_picture_id);

        $image_path = $progress_picture['picture_location'] ?? time()."null";

        $image_path = $_ENV['PROGRESS_IMAGE_PATH'].$image_path;

        if(is_file($image_path)){
            unlink($image_path);
        }

        if($progressPictureModel->delete($progress_picture_id))
            BluelimeSystem::get_success_ajax_return($progress_picture_id);
        BluelimeSystem::get_error_ajax_return($progress_picture_id);
    }

}

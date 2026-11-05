<?php

namespace App\Controllers\Hospital;

use BluelimeAuthentication;
use BluelimeConfiguration;
use BluelimePOS;
use BluelimeSystem;
class Reception extends \App\Controllers\BaseController
{
    
    public function index()
    {
        $data['active_menu'] = 'new_patient';
        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();

        $patientModel = model('Hospital/PatientModel');
        $companyModel = model('Hospital/CompanyModel');
        $drugModel = model('Hospital/DrugModel');
        $allergyModel = model('Hospital/AllergyModel');
        $diseaseModel = model('Hospital/DiseaseModel');
        $autoCompleteModel = model('Hospital/AutoCompleteModel');
        $userModel = model('System/UserModel');

        $drugs = $drugModel->findColumn('name');
        $data['drugs'] = $drugs;
        $allergies = $allergyModel->findColumn('name');
        $data['allergies'] = $allergies;
        $diseases = $diseaseModel->findColumn('name');
        $data['diseases'] = $diseases;

        $companies = $companyModel->orderBy('company_name', 'ASC')->where('is_active',1)->findAll();

        $regions = $autoCompleteModel->where('name','region')->findColumn('data')[0] ?? [];
        $subcities = $autoCompleteModel->where('name','subcity')->findColumn('data')[0] ?? [];
        $doctors = $userModel->where('role','DOCTOR')->where('active', 1)->findAll();
        

        //BluelimeSystem::show_and_die($auto_complete);
        $data['companies'] = $companies;

        $data['patient'] =  NULL;
        $max_id = $patientModel->get_max_id();
        $data['mrn_no'] = $max_id + 1;
        $data['page_type'] = 'new';
        $data['regions'] = $regions;
        $data['subcities'] = $subcities;
        $data['doctors'] = $doctors;

        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Hospital/Reception/_left_sidebar_reception')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Reception/new_patient')
            .view('Include/_footer');
    }

    public function receptiondashboard()
    {
        $scheduleModel = model('Hospital/ScheduleModel');
        $skipDateModel = model('Hospital/SkipDateModel');
        $billModel = model('Hospital/BillModel'); 
        $billPayementModel = model('Hospital/BillPaymentModel'); 
        $userModel = model('System/UserModel'); 
        $visitModel = model('Hospital/VisitModel'); 
        $appointmentModel = model('Hospital/AppointmentModel');
        
        $selected_date = date('Y-m-d').'%';

        $skips=$skipDateModel->where('skip_date >=',$selected_date)->findAll();
        foreach($skips as $index => $doctor){
            $skips[$index]['doctor_name'] = $userModel->where('id',$doctor['doctor_id'])->findColumn('name')[0];
        }

        $doctors=$scheduleModel->distinct()->findColumn('doctor_name');
        
        $schedules=$scheduleModel->findAll();
        
        $billPayements = $billPayementModel->where('payed_date LIKE', $selected_date)->orderBy('patient_name','ASC')->orderBy('id','ASC')->findAll();
        $total_payment = $billPayementModel->where('payed_date LIKE', $selected_date)->findColumn('sum(payed_amount)');
        foreach($billPayements as $index => $billPayement){
            $visit_id = $billModel->where('id',$billPayement['bill_id'])->findColumn('visit_id');
            $billPayements[$index]['doctor_name'] = $visitModel->where('id',$visit_id)->findColumn('doctor_name');
        }
        $cancelled_appointments = $appointmentModel->where('appointment_date >= ',$selected_date)->where('appointment_status',-1)->where('Cancelled_by like', 'Dr.%')->orderBy('appointment_date DESC')->findAll();

        $data['cancelled_appointments'] = $cancelled_appointments;
        $data['billPayements'] = $billPayements;
        $data['total_payment'] = $total_payment[0];
        $data['schedules'] = $schedules;
        $data['doctors'] = $doctors??[];
        $data['skips'] = $skips;
        $data['active_menu'] = 'receptiondashboard';
        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/reception_dashboard')

            .view('Include/_footer');
    } 
    public function categoryreport()
    {
        $billModel = model('Hospital/BillModel'); 
        $billDetailModel = model('Hospital/BillDetailModel'); 
        
        $selected_date = date('Y-m-d',strtotime('2024-11-26')).'%';

        
        $billDetail = $billDetailModel->where('bill_date LIKE', $selected_date)->orderBy('service_name','DESC')->orderBy('id','DESC')->findAll();
        
        
        $data['billDetail'] = $billDetail;
        $data['active_menu'] = 'receptiondashboard';
        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/category_report')

            .view('Include/_footer');
    }
    public function dailypatients()
    {
        
        $patientModel = model('Hospital/PatientModel');
        $visitModel = model('Hospital/VisitModel');
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        $selected_date = (!empty($_POST['selected_date'])?$_POST['selected_date']:date('Y-m-d')).'%';
        
       
        
        $daily_new_patients = $patientModel->where('registered_date like',$selected_date)->findAll();
        foreach($daily_new_patients as $index => $patient){
            $daily_new_patients[$index]['patient_new_visit'] = $visitModel->where('patient_id like',$patient['id'])->orderBy('id ASC')->findColumn('id')[0]??-1;
            $daily_new_patients[$index]['patient_diagnosis'] = $visitEncounterModel->where('visit_id',$daily_new_patients[$index]['patient_new_visit'])->findColumn('diagnosis')[0]??[];
        }
       // BluelimeSystem::show_and_die($daily_new_patients);
       // $daily_new_visit = $visitModel->whereIn('patient_id like',$daily_new_patients)->orderBy('id ASC')->findAll();

        $nop = $patientModel->where('registered_date like',$selected_date)->findColumn('count(id)');
        
        $data['daily_new_patients'] = $daily_new_patients??[];
        $data['selected_date'] = $selected_date;
        $data['nop'] = $nop[0];
        $data['active_menu'] = 'receptiondashboard';
        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/daily_patients')

            .view('Include/_footer');
    }

    public function doctorsreport()
    {
        $visitModel = model('Hospital/VisitModel'); 
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model(name: 'Hospital/BillDetailModel');
        $approvedDoctorsReportModel = model('Hospital/ApprovedDoctorsReportModel');
        $userModel = model('System/UserModel');
        $notificationModel = model('Hospital/NotificationModel');

        $doctor_id = $_GET['doctor_id'] ?? -1;
        $date_filter = $_GET['date'] ?? date('Y-m-d');
       
        $selected_doctor = $userModel->find($doctor_id);

        $notificationModel->where('notification_type','Daily Doctor Report Approved')->where('description',$selected_doctor['name']??'')->set('status',BluelimeConfiguration::NOTIFICATION_SEEN)->update();

        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();

        $bills = $billModel->where('registered_by', $doctor_id)->where('payed_date >=', "$date_filter 00:00:01")->where('payed_date <=', "$date_filter 23:59:59")->findAll();

        $bill_id_list = [...BluelimeSystem::collect_field_value_from_object($bills,'id'), -1];

        $procedures = $billDetailModel->whereIn('bill_id', $bill_id_list)->whereIn('service_type',[BluelimeConfiguration::SERVICE_TYPE::PROCEDURE,BluelimeConfiguration::SERVICE_TYPE::PATHOLOGY])->where('service_status',0)->findAll();

        $doctors_procedure = [];
        foreach($procedures as $index => $procedure){
            foreach($bills as $bill){
                if($bill['id'] == $procedure['bill_id']){
                    $procedure['bill'] = $bill;
                    array_push($doctors_procedure, $procedure);
                }
            }
        }
        
        $doctor_visits = $visitModel->where('doctor_id',$doctor_id)->whereIn('visit_status', [BluelimeConfiguration::VISIT_STATUS_CLOSED, BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN])->where('visit_date >=', "$date_filter 00:00:01")->where('visit_date <=', "$date_filter 23:59:59")->findAll();
        $bill_id_list = [...BluelimeSystem::collect_field_value_from_object($doctor_visits,'bill_id'),-1];
       
        $visits_bill = $billModel->whereIn('id', $bill_id_list)->findAll();
        //->where('payed_date >=', "$date_filter 00:00:01")->where('payed_date <=', "$date_filter 23:59:59")->findAll();

        foreach($doctor_visits as $index => $dvisit){
            $doctor_visits[$index]['fs'] = "";
            $doctor_visits[$index]['bill_type'] = "";
            foreach($visits_bill as $dbill){
                if($dvisit['bill_id'] == $dbill['id']){
                    $doctor_visits[$index]['fs'] = $dbill['fs_number'];
                    $doctor_visits[$index]['bill_type'] = $dbill['bill_type'];
                    break;
                }
            }
        }
        foreach ($doctor_visits as $key => $date) {
            $sort[$key] = $date['fs']??'';
        }
        if(!empty($doctor_visits)){
            array_multisort($sort, SORT_ASC, $doctor_visits);
        }
        $approved_report = $approvedDoctorsReportModel->where('doctor_id',$selected_doctor['id']??'')->where('report_date',date('Y-m-d',strtotime($date_filter)))->findAll()[0]??[];
       // $userModel->where('id',$approved_report['doctor_id']??-1)->findColumn('digital_signature')??"";
        $doc_sign = $selected_doctor['digital_signature'] ?? null;

        $doctors = $userModel->select('id, name')->where('role', BluelimeConfiguration::USER_ROLE::DOCTOR)->where('active',1)->findAll();
       
        $data['doctor_visits'] = $doctor_visits;
        $data['procedures'] = $doctors_procedure;

        $data['approved_report'] = $approved_report;
        $data['doc_sign'] = $doc_sign;
        $data['doctors'] = $doctors;
        $data['selected_doctor'] = $selected_doctor;
        $data['logged_user'] = $loggeduserdata['id'];
        $data['date_filter'] = $date_filter;
        $data['active_menu'] = 'receptiondashboard';
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/doctors_report')

            .view('Include/_footer');
    }

    public function newappointment()
    {
        //sleep(15);
        $searchkey= $_POST['searchkey'] ?? "";
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $appointmentModel = model('Hospital/AppointmentModel');
        $visitModel = model('Hospital/VisitModel');

        $patient_id = $_GET['id'] ?? -1;
        $patients = $patientModel->search_patient($searchkey);
        $nop = $patientModel->findColumn('count(id)');

        $patient_appointment=$appointmentModel->where('patient_id',@$_GET['id'])->findAll();
        $appointment=$appointmentModel->where('id',@$_GET['app_id'])->findAll();
        $data['patient_appointment'] = $patient_appointment;
        //BluelimeSystem::show_and_die($appointment);
        $data['appointment'] = $appointment;
        $data['active_menu'] = 'appointment';
      
       $patient = $patientModel->find(@$_GET['id']);
       
       $role = array('DOCTOR','NURSE');
       $doctors = $userModel->whereIn('role',$role)->where('active', 1)->findAll();

       $visits = $visitModel->where('patient_id', $patient_id)->orderBy('id', 'DESC')->findAll();

       $last_visit = $visits[0] ?? [];

       $next_appointment_reason = $last_visit['extra']['next_appointment_reason'] ?? '';
        
        //$data['patient'] = $patients;
        $data['doctors'] = $doctors;
        $data['patient'] = $patient;
        $data['searchkey'] = $searchkey;
        $data['patients'] = $patients;
        $data['nop'] = $nop[0];
        $data['next_appointment_reason'] = $next_appointment_reason;

        /*
        $aa= $appointmentModel->where('appointment_date >= CURRENT_DATE()')->where('appointment_date <' ,'2023-09-17%')->where('doctor_id',31)->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)->findAll();
        //$aa= $appointmentModel->where('appointment_date >= ','2023-09-17%')->where('doctor_id',31)->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)->findAll();
        
        foreach($aa as $y){
            BluelimeSystem::create_google_calendar_events(date('Y-m-d', strtotime($y['appointment_date'])),$y['time'],$y['appointment_reason'],$y['full_name'],30);

        }*/

        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/new_appointment')

            .view('Include/_footer');
    }
    
    public function editpatient()
    {
        $data['active_menu'] = 'new_patient';
        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();
        
        $id = $_GET['id'];
        $patientModel = model('Hospital/PatientModel');
        $companyModel = model('Hospital/CompanyModel');
        $drugModel = model('Hospital/DrugModel');
        $allergyModel = model('Hospital/AllergyModel');
        $diseaseModel = model('Hospital/DiseaseModel');
        $autoCompleteModel = model('Hospital/AutoCompleteModel');
        $userModel = model('System/UserModel');

        $drugs = $drugModel->findColumn('name');
        $data['drugs'] = $drugs;
        $allergies = $allergyModel->findColumn('name');
        $data['allergies'] = $allergies;
        $diseases = $diseaseModel->findColumn('name');
        $data['diseases'] = $diseases;
        
        $companies = $companyModel->orderBy('company_name', 'ASC')->where('is_active',1)->findAll();

        $regions = $autoCompleteModel->where('name','region')->findColumn('data')[0];
        $subcities = $autoCompleteModel->where('name','subcity')->findColumn('data')[0];
        $doctors = $userModel->where('role','DOCTOR')->where('active', 1)->findAll();

        $data['companies'] = $companies;
        
        $patient = $patientModel->find($id);

        $data['patient'] =  $patient;
        $data['doctors'] = $doctors;
        $data['mrn_no'] = $id;
        $data['new_mrn'] = $patientModel->get_max_id()+1;
        $data['page_type'] = 'edit';
        $data['regions'] = $regions;
        $data['subcities'] = $subcities;
        
        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/new_patient')

            .view('Include/_footer');
    }

    public function searchpatient()
    {
        $searchkey= $_POST['search_patient'] ?? "";
        $crd_no_only= $_POST['crd_no_only'] ?? "";
        $patientModel = model('Hospital/PatientModel');
        $data['active_menu'] = 'search_patient';
        $visitModel = model('Hospital/VisitModel');
        $userModel = model('System/UserModel');
        if($crd_no_only==1){
            $patients = $patientModel->where('id',$searchkey)->findAll();
        }
        else{
            $patients = $patientModel->search_patient($searchkey);
        }
        $nop = $patientModel->findColumn('count(id)');
        $doctors = $userModel->where('role','DOCTOR')->where('active', 1)->findAll();

        //BluelimeSystem::show_and_die($crd_no_only);
        
        foreach($patients as $index => $patient)
            $patients[$index]['patient_has_active_visite'] = $visitModel->get_open_visit($patient['id']);
    
        $data['searchkey'] = $searchkey;
        $data['patients'] = $patients;
        $data['doctors'] = $doctors;
        $data['nop'] = $nop[0];
        
        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/search_patient')
            .view('Include/_footer');
    }

    public function appointmentlist()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
        $appointmentModel->updatemissed();
        //\BluelimeSystem::import_appointment_new();
        
        $selected_date = isset($_POST['appointment_date'])?($_POST['appointment_date'].'%'):date('Y-m-d').'%';
        $searchkey= $_POST['search_appointment'] ?? "";
        //\BluelimeSystem::show_and_die($_POST);
        if(!empty($_POST['search_appointment'])){
            $appointments = $appointmentModel->search_by_patient($searchkey);
        }
        else
        
            if(!empty($_POST['app_doctor'])){
                $appointments = $appointmentModel->where('appointment_date like ',$selected_date)->where('doctor_name',$_POST['app_doctor'])->orderBy("STR_TO_DATE(time,'%h:%i%p')")->findAll();
                $doctors = $appointmentModel->distinct()->where('appointment_date like ',$selected_date)->findColumn('doctor_name');
                //\BluelimeSystem::show_and_die($doctors);
                $no_of_appointment = $appointmentModel->where('appointment_date like ',$selected_date)->where('doctor_name',$_POST['app_doctor'])->findColumn('count(id)')[0]; 
            }
            else{
                $appointments = $appointmentModel->where('appointment_date like ',$selected_date)->orderBy("STR_TO_DATE(time,'%h:%i%p')")->findAll();
                $doctors = $appointmentModel->distinct()->where('appointment_date like ',$selected_date)->findColumn('doctor_name');
                //\BluelimeSystem::show_and_die($doctors);
                $no_of_appointment = $appointmentModel->where('appointment_date like ',$selected_date)->findColumn('count(id)')[0];
            }
            if(@$_GET['online']==1){
                $appointments = $appointmentModel->where('appointment_date >= ',$selected_date)->where('paid_appointment >',0)->orderBy('appointment_date','desc')->findAll();
            
            }
        $data['active_menu'] = 'appointment';
        $data['selected_date'] = $selected_date;
        $data['appointments'] = $appointments;
        $data['no_of_appointment'] = @$no_of_appointment;
        $data['doctors'] = @$doctors;
        $data['searchkey'] = $searchkey;
        $data['company_header'] = BluelimeSystem::get_company_header();
        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/appointment_list', $data)

            .view('Include/_footer');
    }
    public function waitinglist()
    {
        
        $searchkey= $_POST['search_waiting_list'] ?? "";
        $waitingListModel = model('Hospital/WaitingListModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $visitModel = model('Hospital/VisitModel');
        $notificationModel = model('Hospital/NotificationModel');
        $appointmentModel = model('Hospital/AppointmentModel');
        
        $patient = $patientModel->find(@$_GET['id']??" ");
        
        $waiting_list = $waitingListModel->search_by_patient($searchkey);

        $doctors = $userModel->where('role','DOCTOR')->where('active', 1)->findAll() ?? [];
        $doctor_avg_tat = [];
        $doctor_active_counts = [];
        $since = date('Y-m-d', strtotime('-30 days')) . ' 00:00:00';

        foreach ($doctors as $doc) {
            $docId = $doc['id'];
            // Calculate average doctor room time from encounter open to encounter finish
            $avgRow = $visitModel->join('hospital_visit_encounter', 'hospital_visit_encounter.visit_id = hospital_visits.id', 'inner')
                ->select("AVG(TIMESTAMPDIFF(MINUTE, hospital_visit_encounter.created_at, hospital_visit_encounter.updated_at)) as avg_minutes")
                ->where('hospital_visits.doctor_id', $docId)
                ->where('hospital_visits.visit_status', BluelimeConfiguration::VISIT_STATUS_CLOSED)
                ->where('hospital_visit_encounter.updated_at >=', $since)
                ->first();
            $avg_minutes = $avgRow['avg_minutes'] ?? null;
            $doctor_avg_tat[$docId] = $avg_minutes !== null ? round((float)$avg_minutes, 1) : null;

            $count = $notificationModel->where('user_id', $docId)
                ->where('link_type', 'visit_id')
                ->where('notification_type', 'Doctors Waiting')
                ->where('status', BluelimeConfiguration::NOTIFICATION_NEW)
                ->findColumn('count(id)');
            $doctor_active_counts[$docId] = intval($count[0] ?? 0);
        }

        $todayFilter = date('Y-m-d') . '%';
        $doctor_appointment_counts = [];
        $appointmentsByDoctor = $appointmentModel
            ->select('doctor_id, COUNT(id) as appointment_count')
            ->where('appointment_date like', $todayFilter)
            ->where('appointment_status', BluelimeConfiguration::APPOINTMENT_ACTIVE)
            ->groupBy('doctor_id')
            ->findAll();

        foreach ($appointmentsByDoctor as $row) {
            $doctor_appointment_counts[$row['doctor_id']] = intval($row['appointment_count']);
        }

        $data['doctor_avg_tat'] = $doctor_avg_tat;
        $data['doctor_active_counts'] = $doctor_active_counts;
        $data['doctor_appointment_counts'] = $doctor_appointment_counts;
        $data['doctors'] = $doctors;

        $data['waiting_list'] = $waiting_list;
        $data['patient'] = $patient;
        $data['active_menu'] = 'patientwaitinglist';
        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/waiting_list')

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

        return redirect()->to(site_url('/hospital/reception/waitinglist'));
    }
    
    public function prescriptionlist()
    {
        /*
        Removed on 2024-10-21
        
        $searchkey= $_POST['search_prescription'] ?? "";
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
       // BluelimeSystem::show_and_die($searchkey);
        
        $data['active_menu'] = 'prescriptions';
        $data['loggeduserdata'] = $loggeduserdata;

        $doctor = $loggeduserdata['id'];
        $prescriptions = $prescriptionModel->search_by_patient($searchkey);

        $data['prescriptions'] = $prescriptions;
        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/prescription_list')

            .view('Include/_footer');
        */
    }

    public function prescriptionprinted() {
        // $prescription_group_id = $_POST['prescription_group_id'];
        // return \BluelmeHospital::set_prescription_is_sold($prescription_group_id);
        return BluelimeSystem::get_success_ajax_return('ok');
    }
            
    public function printoutlist()
    {
        $searchkey= $_POST['search_printout'] ?? "";
        $filter_type = $_POST['filter_type'] ?? "";
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $notificationModel = model('Hospital/NotificationModel');
        $medicalCertificateModel = model('Hospital/MedicalCertificateModel');
        $medicalReportModel = model('Hospital/MedicalReportModel');
        $labRequestModel = model('Hospital/LabRequestModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $userModel = model('System/UserModel');
        $orderMainModel = model('Hospital/OrderMainModel');

        $data['active_menu'] = 'printoutlist';
        $data['loggeduserdata'] = $loggeduserdata;

        $external_lab_request = $externalDocumentsModel->search_by_patient($searchkey);
        
        $prescriptions = $prescriptionModel->search_by_patient($searchkey);
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
       
        //$patients = BluelimeSystem::gather_objects_by_reference($orders,'patient_id','Hospital/PatientModel','id',true);
        
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
                'registered_date' => $compoundingPrescription['created_at']
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
            
    //BluelimeSystem::show_and_die($printout_lists);

        // Filter by type if filter_type is set
        if (!empty($filter_type)) {
            $printout_lists = array_filter($printout_lists, function($item) use ($filter_type) {
                return $item['type'] === $filter_type;
            });
        }
        
        
        
        $data['prescriptions'] = $prescriptions;
        $data['orders'] = $prescriptions;
        $data['printout_lists'] = $printout_lists;
        //$data['prescriptionDetail'] = $prescriptionDetail;
        $data['medicalCertificates'] = $medicalCertificates;
        $data['labRequests'] = $labRequests;
        $data['searchkey'] = $searchkey;
        $data['filter_type'] = $filter_type;
        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/printout_list')

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
                .view('Hospital/Reception/_left_sidebar_reception')
                .view('Include/_left_sidebar_bottom')
                .view('Hospital/Reception/view_lab_result')
                .view('Include/_footer');     
    }

    public function labrequestlist()
    {
        
        $searchkey= $_POST['search_labrequest'] ?? "";
        $labRequestModel = model('Hospital/LabRequestModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $minid = $labRequestModel->findColumn('id')[0]??[];
        
        $data['active_menu'] = 'labrequestlist';
        $data['loggeduserdata'] = $loggeduserdata;
        if(!empty($searchkey)){
                $labRequests = $labRequestModel->search_by_patient_followup($searchkey);
            }
        else{
                if(@!empty($_GET['nid'])){
                    $labRequests = $labRequestModel->where('id > ',$_GET['nid'])->findAll(10);
                }
                else{
                    $labRequests = $labRequestModel->findAll(10);
                }
        }

        $labRequests_near_delivery = $labRequestModel->where('delivery_date >= ',date('Y-m-d').'%')->where('status',0)->orderBy('delivery_date')->findAll();
        $labRequests_passed_delivery = $labRequestModel->where('delivery_date < ',date('Y-m-d').'%')->where('status',0)->orderBy('delivery_date','desc')->findAll();
        
        
        

        $data['labRequests'] = $labRequests;
        $data['labRequests_near_delivery'] = $labRequests_near_delivery;
        $data['labRequests_passed_delivery'] = $labRequests_passed_delivery;
        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/labrequest_list')

            .view('Include/_footer');
    }

    
    public function pendingbills()
    {
        $searchkey = $_GET['q'] ?? "";
        $billModel = model('Hospital/BillModel');
        $serviceModel = model('Hospital/ServiceModel');
        $userModel = model('System/UserModel');
        $notificationModel = model('Hospital/NotificationModel');

        $companyModel = model('Hospital/CompanyModel');
        $services = $serviceModel->where('is_active',1)->orderBy('service_name','ASC')->findAll();
        $role = array('DOCTOR','NURSE','PATHOLOGY');
        $doctors = $userModel->whereIn('role',$role)->where('active', 1)->findAll();

        $data['active_menu'] = 'pending_bills';
        //$bills = $billModel->searchpendingbills($searchkey);
        $billModel = $billModel->whereIn('bill_status', [BluelimeConfiguration::BILL_PENDING, BluelimeConfiguration::BILL_OPEN, BluelimeConfiguration::BILL_SENT_TO_POS, BluelimeConfiguration::BILL_REMOVE_REQUESTED]);
        $searchkeys = explode(' ', $searchkey);
        if(trim($searchkey) == '')
            $searchkeys = [];

        
        foreach($searchkeys as $key){
            if(trim($key) == '')
                continue;

            $billModel = $billModel->groupStart();
            if(intval($key) > 0)
                $billModel = $billModel->orWhere('mobile LIKE',"%$key%")->orWhere('patient_id LIKE', "%$key%");
            else
                $billModel = $billModel->orWhere('patient_name LIKE',"%$key%");
            $billModel = $billModel->groupEnd();
        }
        

        $bills = $billModel->orderBy('id','DESC')->paginate(BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST);
        
        //BluelimeSystem::sear
        $company_name = $companyModel->find($searchkey);
        
        $data['company_name'] = $company_name;
        $data['services'] = $services;
        $data['doctors'] = $doctors;

        $pending_bills = [];

        foreach($bills as $bill){
            $row = [
                'id' => $bill['id'],
                'patient_id' => $bill['patient_id'],
                'patient_name' => $bill['patient_name'],
                'company_name' => $bill['company_name'],
                'billed_date' => $bill['billed_date'],
                'bill_status' => $bill['bill_status'],
                'registered_by' => $bill['registered_by'],
                'type' => 'bill'
            ];
            array_push($pending_bills, $row);
        }

        $notification_bill_id_list = [];

        foreach($pending_bills as $bills){
            array_push($notification_bill_id_list, @$bills['id']);
        }
        
        //BluelimeSystem::show_and_die($pending_bills);
        if(!empty($pending_bills)){
            $new_bill = $notificationModel->where('link_type','bill_id')->orWhere('link_type','order_option_id')->whereIn('link_id', $notification_bill_id_list)->findAll();
        
            foreach($pending_bills as $index => $bills){
                foreach($new_bill as $newb){
                    if($bills['id'] === $newb['link_id']){
                        $pending_bills[$index]['_notify'] = $newb;
                        break;
                    }
                }
            }
        }
        $data['pending_bills'] = $pending_bills;
        $data['pager'] = $billModel->pager;
        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)
            .view('Hospital/Reception/_left_sidebar_reception')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Reception/pending_bills')
            .view('Include/_footer');
    }

    public function request_for_deletion(){
        $bill_id = $_GET['bill_id'] ?? -1;
        $billModel = model('Hospital/BillModel');
        $bill = $billModel->find($bill_id);
        if(!empty($bill)){
            if($bill['bill_status'] == BluelimeConfiguration::BILL_PENDING)
                $billModel->update($bill_id, ['bill_status'=>BluelimeConfiguration::BILL_REMOVE_REQUESTED]);
        }

        return redirect()->to('/hospital/reception/pendingbills');
    }

    public function cancel_request_for_deletion(){
        $bill_id = $_GET['bill_id'] ?? -1;
        $billModel = model('Hospital/BillModel');
        $bill = $billModel->find($bill_id);
        if(!empty($bill)){
            if($bill['bill_status'] == BluelimeConfiguration::BILL_REMOVE_REQUESTED)
                $billModel->update($bill_id, ['bill_status'=>BluelimeConfiguration::BILL_PENDING]);
        }

        return redirect()->to('/hospital/reception/invoicebill?id='.$bill_id);
    }

    public function change_to_cash_credit(){
        $bill_id = $_GET['bill_id'] ?? -1;
        $change_to_cash = $_GET['change_to_cash'] ?? -1;

        $billModel = model('Hospital/BillModel');
        $patientModel = model('Hospital/PatientModel');
        
        $bill = $billModel->find($bill_id);
        $patient_id = $bill['patient_id'] ?? -1;
        $patient = $patientModel->find($patient_id);
        $company_id = $patient['company_id'] ?? 0;

        $bill_type = "";
        if($change_to_cash == 1)
            $bill_type = 'Cash';
        elseif($change_to_cash == 0)
            $bill_type = 'Credit';

        if(($bill_type != "") && ($company_id > 0)){
            $billModel->update($bill_id, ['bill_type'=>$bill_type]);
        }
        
        return redirect()->to("/hospital/reception/invoicebill?id=".$bill_id);
    }

    private function followup_has_treatment_plans(array $follow_up):bool{

        $treatment_plans = $follow_up['treatment_plans'] ?? [];
        
        foreach($treatment_plans as $index => $treatment_plan){
            if(($index != 'plan_1') && ($index != 'plan_2') && ($index != 'plan_3'))
                continue;
            foreach($treatment_plan as $plan){
                if(trim($plan['title']) != '')
                    return true;
            }
        }
        return false;
    }
    public function treatmentplans(){
        $followUpModel = model('Hospital/FollowUpModel');
        $notificationModel = model('Hospital/NotificationModel');
        $search_plan = $_GET['search_plan'] ?? '';
        $followUps = [];
        if($search_plan == '')
            $followUps = $followUpModel->where('treatment_plans !=','')->orderBy('id','DESC')->findAll();
        else
            $followUps = $followUpModel->search_follow_up($search_plan);

        $treatment_plans = [];
        
        foreach($followUps as $followUp){
            if($this->followup_has_treatment_plans($followUp))
                array_push($treatment_plans, $followUp);

            if(count($treatment_plans)>= 20)
                break;
        }

        $notification_plan_id_list = [];

            foreach($treatment_plans as $followUp){
                array_push($notification_plan_id_list, @$followUp['id']);
            }
            
            if(!empty($treatment_plans)){
            $new_plan = $notificationModel->where('link_type','followup_id')->whereIn('link_id', $notification_plan_id_list)->findAll();
                foreach($treatment_plans as $index => $followUp){
                    foreach($new_plan as $plan){
                        if($followUp['id'] === $plan['link_id']){
                            $treatment_plans[$index]['_notify'] = $plan;
                            break;
                        }
                    }
                }
            }
               
        $data['follow_ups'] = $treatment_plans;
        $data['active_menu'] = 'treatment_plans';
        $data['search_plan'] = $search_plan;

        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Hospital/Reception/_left_sidebar_reception')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Reception/treatment_plans')
            .view('Include/_footer');
    }

    public function treatmentplanchoice(){
        $follow_up_id = $_GET['follow_up_id'] ?? -1;

        $followUpModel = model('Hospital/FollowUpModel');
        $notificationModel = model('Hospital/NotificationModel');

        $follow_up = $followUpModel->find($follow_up_id);

        $treatment_plans = $follow_up['treatment_plans'] ?? [];
        $company_id = $follow_up['company_id'] ?? 0;

        $notificationModel->where('link_id',$follow_up_id)->where('link_type','followup_id')->where('notification_type','New Plan')->set('status',\BluelimeConfiguration::NOTIFICATION_SEEN)->update();


        $remark = $treatment_plans['remark'] ?? [];
        unset($treatment_plans['remark']);

        $treatment_plans_with_price = [];
        foreach($treatment_plans as $plan_no => $plan){
            $service_list_temp = [];
            foreach($plan as $row){
                $service = BluelimeSystem::convert_to_array($row['title'])[0] ?? '';

                if(empty($service))
                    continue;
                
                $service['service_id'] = $service['id'];
                $service['quantity'] = $row['quantity'] ?? 1;
                array_push($service_list_temp, $service);
            }

            if(empty($service_list_temp))
                continue;

            $service_list_temp = BluelimeSystem::add_price_detail_in_service_list($company_id, $service_list_temp);
            $total = 0;
            $grand_total = 0;
            $total_discount = 0;
            foreach($service_list_temp as $index => $service){
                $service_list_temp[$index]['total'] = floatval($service['quantity']) * (floatval($service['price']) - (floatval($service['price']) * floatval($service['discount'])/100));
                $total += $service_list_temp[$index]['total'];
                $total_discount += floatval($service['quantity']) * ((floatval($service['price']) * floatval($service['discount'])/100));
                if($service['service_type'] != 'Discount')
                    $grand_total += $service_list_temp[$index]['total'];
                else
                    $total_discount += -1 * floatval($service['price']);
            }

            $service_list_temp['total'] = $total;
            $service_list_temp['grand_total'] = $grand_total;
            $service_list_temp['total_discount'] = $total_discount;
            $service_list_temp['has_discount'] = ($total_discount != 0) ? true : false;

            $treatment_plans_with_price[$plan_no] = $service_list_temp;
        }

        $data['treatment_plans'] = $treatment_plans_with_price;
        $data['remark'] = $remark;
        $data['follow_up'] = $follow_up;
        $data['active_menu'] = 'treatment_plans';

        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Hospital/Reception/_left_sidebar_reception')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Reception/treatment_plans_choice')
            .view('Include/_footer');
    }

    public function choosetreatmentplan(){
        $follow_up_id = $_GET['follow_up_id'] ?? -1;
        $plan_index = $_GET['plan_index'] ?? '';

        $followUpModel = model('Hospital/FollowUpModel');

        $follow_up = $followUpModel->find($follow_up_id);

        if(!empty($follow_up)){
            $treatment_plans = $follow_up['treatment_plans'];
            $treatment_plans['remark']['selected_plan'] = $plan_index;

            $data = ['treatment_plans' => BluelimeSystem::convert_array_to_string($treatment_plans)];

            $followUpModel->update($follow_up_id, $data);
        }

        return redirect()->to('/hospital/reception/treatmentplanchoice?follow_up_id='.$follow_up_id);
    }

    public function receivedbills()
    {
        $searchkey= $_GET['search_patient'] ?? "";

        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        
        $data['active_menu'] = 'pending_bills';

        if(trim($searchkey) != '')
            $billModel = $billModel->where('patient_name LIKE', "%$searchkey%")->orWhere('mobile LIKE', "%$searchkey%")->orWhere('patient_id LIKE', "%$searchkey%")->orWhere('fs_number LIKE', "%$searchkey%");
        
        $bills = $billModel->where('fs_number IS NOT', Null)->orderBy('id DESC')->paginate(BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST);

        $bill_id_list = BluelimeSystem::collect_field_value_from_object($bills, 'id');
        if(empty($bill_id_list))
            $bill_id_list = [-1];
        $bill_details = $billDetailModel->whereIn('bill_id', $bill_id_list)->where('service_status !=', BluelimeConfiguration::SERVICE_STATUS_DELAY)->findAll();
        foreach($bills as $index => $bill){
            $bills[$index]['total'] = 0;
            foreach($bill_details as $bill_detail){
                if($bill_detail['bill_id'] == $bill['id'])
                    $bills[$index]['total'] += ($bill_detail['bill_quantity'] * $bill_detail['bill_amount']) - ($bill_detail['bill_quantity'] * $bill_detail['bill_amount'] * $bill_detail['bill_discount'] / 100);
            }
        }
        $data['bills'] = $bills;
        $data['pager'] = $billModel->pager;

        //BluelimeSystem::show_and_die($billPayments);
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)
            .view('Hospital/Reception/_left_sidebar_reception')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Reception/payed_bills')
            .view('Include/_footer');
    }

    public function invoicebill()
    {
        $notificationModel = model('Hospital/NotificationModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $notificationModel->where('link_id',$_GET['id'])->set('status',BluelimeConfiguration::NOTIFICATION_SEEN)->update();

        $data['logged_user_data'] =  BluelimeSystem::$Authentication::get_logged_user_data();

        $do_not_open_visit = $_GET['do_not_open_visit'] ?? 0;
        
        $searchkey = $_GET['id'] ?? "%";
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $patientModel = model('Hospital/PatientModel');
        $billPaymentModel = model('Hospital/BillPaymentModel');
        $viewBillAndPaymentModel = model('Hospital/ViewBillAndPaymentModel');
        $companyModel = model('Hospital/CompanyModel');
        
        $bill = $billModel->find($searchkey);
        $billDetail= $billDetailModel->where('bill_id',$bill['id'])->orderBy('service_status','DESC')->findAll();
        $billPayments=$billPaymentModel->where('bill_id',$searchkey)->findAll();
        $patient = $patientModel->find($bill['patient_id']);

        //$order_id = is_array(BluelimeSystem::convert_to_array($bill['order_id']))?0:BluelimeSystem::convert_to_array($bill['order_id'])[0];
        
        
        $previously_payed = $billPaymentModel->where('bill_id', $bill['id'])->where('bill_id', $bill['id'])->groupBy('bill_id')->findColumn('SUM(payed_amount)');
        
        $bill_id = $searchkey;
        $patient_bill_id_list = $billModel->where('patient_id',$bill['patient_id'])->where('id !=', $bill_id)->findColumn('id');

        $open_bills = [];

        //$patient_bill_id_list  = [2,4,5,6];
        if(!empty($patient_bill_id_list)){
            $open_bill_id_list = $viewBillAndPaymentModel->whereIn('bill_id',$patient_bill_id_list)->groupBy('bill_id')->having('SUM(amount) >',0)->findColumn('bill_id');        
            
            if(!empty($open_bill_id_list))
                $open_bills = $billModel->whereIn('id',$open_bill_id_list)->findAllWithDueAmount();
        }
        
        $order_main = $orderMainModel->where('bill_id', $bill['id'])->where('order_service_type', BluelimeConfiguration::SERVICE_TYPE::PROCEDURE)->findAll()[0] ?? ['order_comment' => ''];

        $order_comment = trim($order_main['order_comment']);

        $company = $companyModel->find($patient['company_id']??-1);
        if(($company['is_active']??0)==0){
            $patient['company_id'] = 0;
        }

        $data['previously_payed'] = $previously_payed;
        $data['bill_detail'] = $billDetail;
        $data['bill_info'] = $bill;
        $data['patient'] = $patient;
        $data['bill_payments'] = $billPayments;
        $data['open_bills'] = $open_bills;
        $data['do_not_open_visit'] = $do_not_open_visit;
        $data['active_menu'] = 'pending_bills';
        $data['order_comment'] = $order_comment;
        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/invoice_bill')

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
        
        $visit_id = $prescriptions[0]['visit_id'] ?? $compounding_mains[0]['visit_id'] ?? -1;

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
            'void_date' => $void_date,
            'doctor' => $doctor,
            'is_void' => $prescriptions[0]['is_void'] ?? $compounding_mains[0]['is_void'],
        ];

        $data['compounding_mains'] = $compounding_mains;
        $data['prescription_detail'] = $prescription_detail;
        $data['active_menu'] = 'printoutlist';
        //$data['doc_sign'] = $doc_sign[0];
        $data['patient'] = $patient;

        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)
            .view('Hospital/Reception/_left_sidebar_reception')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Reception/view_prescription')
            .view('Include/_footer');
    }
    public function viewinvoice()
    {
        $data['logged_user_data'] =  BluelimeSystem::$Authentication::get_logged_user_data();
        
        $bill_id = $_GET['bid'] ?? "%";
        $payment_id = $_GET['pid'] ?? "%";
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $companyModel = model('Hospital/CompanyModel');
        $serviceModel = model('Hospital/ServiceModel');
        $billPaymentModel = model('Hospital/BillPaymentModel');
         
        
        $bill = $billModel->find($bill_id);
        $billDetail= $billDetailModel->where('bill_id',$bill_id)->findAll();
        $billPayment=$billPaymentModel->where('bill_id',$bill_id)->findAll();
        $billPayments=$billPaymentModel->where('id <',$payment_id)->where('bill_id',$bill['id'])->findAll();
        //$billPayments=$billPaymentModel->where('bill_id',$bill_id)->findAll();
        $patient = $patientModel->find($bill['patient_id']);
        $doctors = $userModel->where('role','DOCTOR')->where('active', 1)->findAll();
        $company_name = $companyModel->find($bill['company_id']);
        $services=$serviceModel->findAll();
        $previously_payed = $billPaymentModel->where('bill_id', $bill['id'])->groupBy('bill_id')->findColumn('SUM(payed_amount)');
        
        $data['bill_payment'] = $billPayment;
        $data['bill_payments'] = $billPayments;
        $data['bill_detail'] = $billDetail;
        $data['bill_info'] = $bill;
        $data['company_name'] = $company_name;
        $data['patient'] = $patient;
        $data['doctors'] = $doctors;
        $data['services'] = $services;
        $data['active_menu'] = 'recieved_payments';
        $data['previously_payed'] = $previously_payed;
        
        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/view_invoice')

            .view('Include/_footer');
    }

    public function viewreceipt()
    {
        $pos_bill_id = $_GET['pos_bill_id'];

        $pOSBillModel = model('POS/POSBillModel');
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $bill = $billModel->where('pos_bill_id', $pos_bill_id)->findAll();

        $bill = $bill[0] ?? [];
        $pos_bill = $pOSBillModel->find($pos_bill_id);
        $pos_bill_json = $pos_bill['pos_bill_json'];

        $sales_main = [
            'id' => $pos_bill['id'],
            'created_at'=>$pos_bill['created_at'],
            'registered_by' => $bill['registered_by_name'] ?? '',
            'fs_number' => $pos_bill['fs_number'],
            'pos_bill_json' => $pos_bill_json,
        ];

        $sub_total = 0;
        foreach($pos_bill_json['sales_item_list'] as $row){
            $sub_total += $row['quantity'] * $row['unit_price'];
        }

        $payment = [
            'none_taxable_sub_total' => $sub_total,
            'taxable_sub_total' => 0,
            'vat' => 0,
            'total' => $sub_total,
        ];

        $data = [
            'sales_main' => $sales_main,
            'sub_total' => $sub_total,
            'taxable_total' => 0,
            'total_vat' => 0,
            'total' => $sub_total,
            'payment' => $payment,
            'pos_bill' => $pos_bill,
            'active_menu' => 'recieved_payments'
        ];
        
        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Hospital/Reception/_left_sidebar_reception')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Reception/attachment_hospital')
            .view('Include/_footer');
    }

    public function viewtemplate()
    {
        
        $templateModel = model('Hospital/TemplateModel');
        
        $template_id = $_GET['tid'];
        $template=$templateModel->find($template_id);
        
        
        $data['template'] = $template;
        $data['active_menu'] = 'search_patient';

        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/view_template')

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
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['lab_request'] = $lab_request;
        $data['doc_sign'] = $doc_sign[0];
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/view_lab_request')

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
        $notificationModel->where('link_id',$_GET['mid'])->where('notification_type','Medical Certificates')->set('status',\BluelimeConfiguration::NOTIFICATION_SEEN)->update();
        $userModel = model('System/UserModel');

        $patient = $patientModel->find(@$_GET['id']);

        $medical_certificate = $medicalCertificateModel->where('id',$_GET['mid'])->orderBy('id','DESC')-> findAll();
        $doc_sign = $userModel->where('id',$medical_certificate[0]['registerd_by'])->findColumn('digital_signature'); 
            
        $doctor_id = $visitModel->where('id',$medical_certificate[0]['visit_id'])->findColumn('doctor_id');
        $doctor = $userModel->find($doctor_id);
 //BluelimeSystem::show_and_die($doctor);  
        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['doctor'] = $doctor[0];
        $data['medical_certificate'] = $medical_certificate;
        $data['doc_sign'] = $doc_sign[0];
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/view_medical_certificate')

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

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/view_medical_report')

            .view('Include/_footer');
    }
    public function revisit()
    {
        $data['logged_user_data'] =  BluelimeSystem::$Authentication::get_logged_user_data();
        $patient_id = $_GET['id'];

        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $visitModel = model('Hospital/VisitModel');
        $companyModel = model('Hospital/CompanyModel');
        $appointmentModel = model('Hospital/AppointmentModel');
        $serviceModel = model('Hospital/ServiceModel');
        $templateModel = model('Hospital/TemplateModel');

        $patient = $patientModel->find($patient_id);
        $services = $serviceModel->where('is_active',1)->orderBy('service_name', 'ASC')->findAll();
        $roles = ['DOCTOR'];
        $doctors = $userModel->whereIn('role',$roles)->where('active', 1)->findAll();
        $templates = $templateModel->findAll();
        
        

        $last_visit_bill = $visitModel->get_patient_last_visit_bill($patient_id);
        $last_visit_bill = $visitModel->get_last_consultation_bill_date($patient_id);

        $patient_procedures = BluelimeSystem::get_patient_procedures($patient_id);
        $active_apointment = $appointmentModel->where('patient_id',$patient_id)->where('appointment_status',BluelimeConfiguration::APPOINTMENT_ACTIVE)->findAll()[0]??[];
        
        $today = date("Y-m-d");

        $company_id = $patient['company_id'];
        $company = $companyModel->find($company_id);

        if(($company['is_active']??0) == 0){
            $company_id = 0;
            $patient['company_id'] = 0;
        }

        if (($company_id > 0) && ($company['unpaid_visit_days']) > 0 ){
            $upaid_visit_days = $company['unpaid_visit_days'];
        }
        else{
            $upaid_visit_days = \BluelimeConfiguration::UNPAID_VISIT_DEFAULT_DAYS;
        }

        $data['upaid_visit_days'] = $upaid_visit_days;
            
        $data['days_after_last_visit'] = !empty($last_visit_bill) ? BluelimeSystem::date_difference($today, $last_visit_bill['payed_date']) : -1;
        
        $open_visits = $visitModel->get_open_visit($patient_id);
        $open_visit_queue = $visitModel->get_open_visit_queue($patient_id);
        $open_visit_doctor = $visitModel->get_open_visit_doctor($patient_id);
        $open_visit_date = $visitModel->get_open_visit_date($patient_id);

        $previous_visit_doctor = $visitModel->get_previous_visit_doctor($patient_id);
        $patient_all_visits = $visitModel->where('patient_id',$patient_id)->orderBy('id DESC')->findAll()??[];
        $previous_visit_date = $visitModel->get_previous_visit_date($patient_id);
        $last_visit_bill_date = $last_visit_bill['billed_date']??'';
        if($active_apointment){
        $active_apointments = date('Y-m-d', strtotime($active_apointment['appointment_date']))." ".$active_apointment['time'].'('.$active_apointment['doctor_name'].')'??'';
        }
        //BluelimeSystem::show_and_die($patient_all_visits);
        $data['active_menu'] = 'search_patient';
        $data['patient'] = $patient;
        $data['doctors'] = $doctors;
        $data['services'] = $services;
        $data['templates'] = $templates;
        $data['patient_has_active_visite'] = false;
        $data['open_visit_queue'] = $open_visit_queue;
        $data['patient_all_visits'] = $patient_all_visits;
        $data['open_visit_doctor'] = $open_visit_doctor;
        $data['open_visit_date'] = $open_visit_date;
        $data['previous_visit_doctor'] = $previous_visit_doctor;
        $data['previous_visit_date'] = $previous_visit_date;
        $data['last_visit_bill_date'] = $last_visit_bill_date;
        $data['active_apointments'] = $active_apointments??"";
        $data['patient_procedures'] = $patient_procedures??"";
        $reference = '';
        $last_visits_option = ['transferForNextYear' => false, 'remark' => []];
        if(!empty($open_visits)){
            $data['patient_has_active_visite'] = true;
            $reference = $open_visits[0]['extra']['reference'] ?? '';
        }
        
        $last_visits = $visitModel->where('patient_id', $patient_id)->where('visit_status', \BluelimeConfiguration::VISIT_STATUS_CLOSED)->orderBy('id', 'DESC')->findAll();
        
    
        if(!empty($last_visits)){

            $last_visit = $last_visits[0];
            $related_visit_id_list = BluelimeSystem::collect_follow_up_visits($last_visit['follow_up_id'], $last_visit['id']);
            
            $related_visits = [];
            if(!empty($related_visit_id_list))
                $related_visits = $visitModel->whereIn('id', $related_visit_id_list)->findAll();

            foreach($related_visits as $visit){
                $transferForNextYear = $visit['extra']['extra_info']['transferForNextYear'] ?? '';

                if($transferForNextYear == 'true')
                    $last_visits_option['transferForNextYear'] = true;

                $remark = trim($visit['extra']['extra_info']['remark'] ?? '');
                $date = trim($visit['extra']['extra_info']['date'] ?? '');
                if($remark != '')
                    array_push($last_visits_option['remark'], ['remark' => $remark, 'date' => $date]);
            }
        }

        $consultation_service = $serviceModel->find(BluelimeConfiguration::CONSULTATION_SERVICE_ID);
        
        $data['reference'] = $reference;
        $data['consultation_service'] = $consultation_service;
        $data['last_visits_option'] = $last_visits_option;
        
        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/revisit')

            .view('Include/_footer');
            
    }
    public function getexistingpatient()
    {
        $patientModel = model('Hospital/PatientModel');
        
        $mobile = $_POST['mobile'];
        $first_name = $_POST['first_name'];
        
        $result = $patientModel->where('mobile', $mobile)->where('first_name', $first_name)->where('deleted_at IS NULL')->findAll();
        //BluelimeSystem::show_and_die(empty($result));
        if($result)
            $result = $result[0]['id'];
        return $result;
    }
    public function getexistingpatientmob()
    {
        $patientModel = model('Hospital/PatientModel');
        //BluelimeSystem::show_and_die($_POST);
        $mobile = $_POST['mobile'];
        $first_name = $_POST['first_name'];
        
        $result = $patientModel->where('mobile', $mobile)->where('first_name', $first_name)->where('deleted_at IS NULL')->findAll();
        
        return $result;
    }
    
    public function discardpatientcard()
    {
        // This functionality is disabled to prevent abuse by receptionists
        return false;
        
        $patientModel = model('Hospital/PatientModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        
        $old_card = $_POST['old_card'];
        $patientModel->update($old_card,['discarded_by'=>$loggeduserdata['id']]);
        $result = $patientModel->delete($old_card);
        
        return $result;
    }
  
    public function addpatient()
    {
        //BluelimeSystem::show_and_die($_POST);
        $patientModel = model('Hospital/PatientModel');
        $companyModel = model('Hospital/CompanyModel');
        $visitModel = model('Hospital/VisitModel');
        $billModel = model('Hospital/BillModel');
        $userModel = model('System/UserModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $waitingListModel = model('Hospital/WaitingListModel');

               
        
        $first_name     = trim($_POST['first_name']);
        $card_location     = $_POST['card_location']??'';
        $middle_name    = trim($_POST['middle_name']);
        $last_name      = trim($_POST['last_name']);
        $date_of_birth  = $_POST['date_of_birth'];
        $sex            = $_POST['sex'];
        $address        = $_POST['address'];
        $subcity        = $_POST['subcity'];
        $woreda         = $_POST['woreda'];
        $house_no       = $_POST['house_no'];
        $mobile         = $_POST['mobile'];
        $survey         = $_POST['survey'];
        $emergency_contact_name = $_POST['emergency_contact_name'];
        $emergency_mobile = $_POST['emergency_mobile'];
        $company_id     = $_POST['company_id'];
        $company = $companyModel->find($company_id);
        $company_name = $company['company_name']??'';
        $blood_pressure = $_POST['blood_pressure']??'No';
        $bleeding_tendency = $_POST['bleeding_tendency']??'No';
        $pregnancy      = $_POST['pregnancy']??'No';
        $cardiac_problem = $_POST['cardiac_problem']??'No';
        $diabetics      =  $_POST['diabetics']??'No';
        $blood_group    = $_POST['blood_group']??'';
        $drugs_taking   = @$_POST['drugs_taking'];
        $allergy        = @$_POST['allergy'];
        $other_diseases = @$_POST['other_diseases'];
        //$registered_date = date("Y-m-d");
        $regisrered_by = $_POST['registered_by'];
        $registered_by_name = $_POST['registered_by_name'];
        $app_id = $_POST['app_id'];
        if(is_array($drugs_taking))
            $drugs_taking = json_encode($drugs_taking);

        if(is_array($allergy))
            $allergy = json_encode($allergy);

        if(is_array($other_diseases))
            $other_diseases = json_encode($other_diseases);

        $data = [
            'card_location'    => $card_location,
            'first_name'    => addslashes(ucwords(strtolower($first_name))),
            'middle_name'   => addslashes(ucwords(strtolower($middle_name))),
            'last_name'     => addslashes(ucwords(strtolower($last_name))),
            'date_of_birth' => $date_of_birth,
            'sex'           => $sex,
            'address'       => $address,
            'subcity'       => $subcity,
            'woreda'        => $woreda,
            'house_no'      => $house_no,
            'mobile'        => $mobile,
            'survey'        => $survey,
            'emergency_contact_name' => $emergency_contact_name,
            'emergency_mobile' => $emergency_mobile,
            'company_id'    => $company_id,
            'company_name'    => $company_name,
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
        
        
        $id = $_POST['id']?? 0;
        $new_id = $_POST['new_id']?? 1;
        //echo $id;
        if (intval($id) != 0 && $new_id !=0){
            $patientModel->update($id,$data);
            $is_cash = $companyModel->where('id',$company_id)->findColumn('is_cash')[0]??0;
            //BluelimeSystem::show_and_die($is_cash);
            $visitModel ->where('visit_date >=',date('Y-m-d%'))->where('patient_id',$id)->set(['patient_name'    => ucwords($first_name." ".$middle_name." ".$last_name),'mobile'    => ($mobile)])->update();
            $orderMainModel ->where('created_at >=',date('Y-m-d%'))->where('patient_id',$id)->set(['patient_name'    => ucwords($first_name." ".$middle_name." ".$last_name),'mobile'    => ($mobile),'sex'    => ($sex)])->update();
            $billModel ->where('bill_status',0)->where('patient_id',$id)->set(['patient_name'    => ucwords($first_name." ".$middle_name." ".$last_name),'mobile'    => ($mobile),'company_id'    => ($company_id),'bill_type'    => ((intval($company_id) > 0  && intval($is_cash) == 0)?'Credit':'Cash'),'company_name'    => ($company_name)])->update();
        }
        else{
            if(empty($this->getexistingpatientmob())){
                $id = $patientModel->insert($data,true);
            }
            else 
                return redirect()->to(site_url('/hospital/reception?fail=1'));
        }
    
        //$website = "\nBook your Appointment @ www.rankderma.com";
        $branch = $_ENV['BRANCH']??"";
        $welcome_message = "Welcome ".ucwords($first_name." ".$middle_name." ".$last_name)." to RANK Dermatology Clinic $branch.\nYour card number is MRN-$id\n";
        //$telegram_bot = "Join Telegram: https://t.me/RankDerma_bot";

       // BluelimeSystem::send_telegram_message($mobile, $welcome_message);
        if(BluelimeSystem::check_sms_service()){
            BluelimeSystem::send_sms_message($mobile, $welcome_message);
        }
        BluelimeSystem::send_telegram_message($mobile, $welcome_message);
        $wait_button = $_POST['wait_button'] ?? 0;
        
        
        if($wait_button == 'Wait'){
            //BluelimeSystem::show_and_die($_POST);
            $doctor_id     = trim($_POST['assigned_doctor']);
            $doctor = $userModel->find($doctor_id);
            $doctor_name = @$doctor['name'] ??'';
            $waiting_data = [
            'patient_id'    => $id,
            'doctor_id'    => $doctor_id,
            'full_name'    => addslashes(ucwords(strtolower($first_name))).' '.addslashes(ucwords(strtolower($middle_name))).' '.addslashes(ucwords(strtolower($last_name))),
            'registered_by' => $regisrered_by,
            'mobile'        => $mobile, 
            'doctor_name'        => $doctor_name, 
            'registered_by_name' => $registered_by_name,
            'status' => 0
        ];
            $waitingListModel->insert($waiting_data,true);

            return redirect()->to(site_url('/hospital/reception/waitinglist'));
        }
        else{
            return redirect()->to(site_url('/hospital/reception/revisit?id='.$id.'&app_id='.@$app_id));
        }
    }

    public function addappointment()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
        $scheduleModel = model('Hospital/ScheduleModel');
        $userModel = model('System/UserModel');
        
        $patient_id     = $_POST['patient_id'];
        $doctor_id    = $_POST['doctor_id'];
        $appointment_date      = $_POST['date'];
        $appointment_reason  = $_POST['appointment_reason'];
        $appointment_status            = \BluelimeConfiguration::APPOINTMENT_ACTIVE;
        $full_name        = ucwords(strtolower($_POST['first_name']." ".$_POST['middle_name']." ".$_POST['last_name']));
        $registered_by         = $_POST['registered_by'];
        $registered_by_name       = $_POST['registered_by_name'];
        $doctor = $userModel->where('id', $doctor_id)->findColumn('name');
        $doctor_name         = $doctor[0];
        $paid_appointment = $_POST['paid_appointment'];
        $visit_id = $_POST['visit_id'];
        $reminder_sent     = $_POST['reminder_sent'];
        $schedule_id     = $_POST['schedule_id'];
        
        //$mobile     = (intval($_POST['additional']==1))?'':$_POST['mobile'];
        $mobile     = $_POST['mobile'];
       // BluelimeSystem::show_and_die($mobile);
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
            'visit_id' => $visit_id,
            'reminder_sent'    => $reminder_sent,
            'schedule_id'    => $schedule_id,
            'mobile'    => $mobile,
            'time'    => $time
        ];
        
        $schedule_min = $scheduleModel->where('id',$schedule_id)->findColumn('per_patient_time');
        //BluelimeSystem::show_and_die(intval($schedule_min[0]));
        $patient_appointment_exist = $appointmentModel->patientappointmentexist($patient_id);
        $appointment_time_exist = $appointmentModel->appointmentchecker($doctor_id,$appointment_date,$time);
       // BluelimeSystem::show_and_die($patient_appointment_exist);
       
        if($appointment_time_exist){  
            return redirect()->to(site_url('/hospital/reception/newappointment?id='.$patient_id.'&rt='.'1'));
        }
        $id = $_POST['app_id']?? 0;
        
        if (intval($app_id) != 0)
            $appointmentModel->update($id,$data);
        else
            $id = $appointmentModel->insert($data,true);

        //$website = "\nBook your Appointment @ www.rankderma.com";
        $branch = $_ENV['BRANCH']??"";
        
        $time_from = BluelimeSystem::roundToNearestThirty(($time));
        
        $time_range = BluelimeSystem::formatThirtyMinuteRange($time_from);
        //BluelimeSystem::show_and_die($time_range);
        $appointment_date_w = date('Y-m-d (l)', strtotime($appointment_date));
        $appointment_message = "Appointment date: $appointment_date_w \nTime: $time_range\nDoctor name: $doctor_name\n\nRANK Specialized Dermatology Clinic\n";
        //$telegram_bot = "Join Telegram: https://t.me/RankDerma_bot";
       
        if(BluelimeSystem::check_sms_service()){
            BluelimeSystem::send_sms_message($mobile, $appointment_message.$branch);
        }
        BluelimeSystem::send_telegram_message($mobile, $appointment_message.$branch);


            /*if($doctor_id==31){
            BluelimeSystem::create_google_calendar_events($appointment_date,$time,$appointment_reason,$full_name,intval($schedule_min[0]));
            }
            */
        //BluelimeSystem::export_prescription($mobile, 'Test Prescription');
            
        return redirect()->to(site_url('/hospital/reception/newappointment?additional=1&app_id=&id='.$patient_id));
    }
    public function save_waiting()
    {
        $waitingListModel = model('Hospital/WaitingListModel');
        $userModel = model('System/UserModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        
        
        $patient_id     = $_POST['patient_id'];
        $doctor_id    = $_POST['doctor_id'];
        $full_name        = $_POST['full_name'];
        $regisrered_by         = $loggeduserdata['id'];
        $registered_by_name       = $loggeduserdata['name'];
        $doctor = $userModel->where('id', $doctor_id)->findColumn('name');
        $doctor_name         = $doctor[0];
        $mobile     = $_POST['mobile'];
        
            $doctor = $userModel->find($doctor_id);
            $doctor_name = @$doctor['name'] ??'';
            $waiting_data = [
            'patient_id'    => $patient_id,
            'doctor_id'    => $doctor_id,
            'full_name'    =>  $full_name,
            'registered_by' => $regisrered_by,
            'mobile'        => $mobile, 
            'doctor_name'        => $doctor_name, 
            'registered_by_name' => $registered_by_name,
            'status' => 0
        ];
        
        $waiting_exists = $waitingListModel->patientinwaitinglist($patient_id, $doctor_id);
        $id = -1;
        if(empty($waiting_exists)){
                $id = $waitingListModel->insert($waiting_data,true);
                return '{"status":"success", "value":"'.$id.'"}';
        }
        else{
             return '{"status":"error", "message":"This patient is already in the waiting list for this doctor."}';
        }

        
    }

    
    public function deletetemplate()
    {
        $templateModel = model('Hospital/TemplateModel');
        
        $tid = $_GET['tid'];
        $id = $_GET['id'];
        $templateModel->delete($tid);
        
        return redirect()->to(site_url('/hospital/reception/revisit?id='.$id));
    }
    
    public function donewaiting()
    {
        $waitingListModel = model('Hospital/WaitingListModel');
        
        $wlid = $_GET['wlid'];
        $data = ['status'=> -1];
        $waitingListModel->update($wlid,$data);
        
        return redirect()->to(site_url('/hospital/reception/waitinglist'));
    }

    public function changelabrequeststatus()
    {
        $labRequestModel = model('Hospital/LabRequestModel');
        
        $lab_request_id = $_POST['request_id'];
        $data = ['status'=> 1,'delivered_date'=> date('Y-m-d h:i:s')];
        $labRequestModel->update($lab_request_id,$data);
        
    }
    public function paylab()
    {
        $labRequestModel = model('Hospital/LabRequestModel');
        
        $request_id = intval($_POST['request_id']);
        $paid_amount = floatval($_POST['paid_amount']);
        $data = ['paid_amount'=> $paid_amount,'paid_date'=> date('Y-m-d h:i:s')];
       
        $labRequestModel->update($request_id,$data);
        
    }

    public function assigntodoctor()
    {
        $visitModel = model('Hospital/VisitModel');
        $patientModel = model('Hospital/PatientModel');
        $companyModel = model('Hospital/CompanyModel');
        $userModel = model('System/UserModel');
        $serviceModel = model('Hospital/ServiceModel');
        $appointmentModel = model('Hospital/AppointmentModel');
        $notificationModel = model('Hospital/NotificationModel');
        $waitingListModel = model('Hospital/WaitingListModel');

        
        $patient_id     = $_POST['patient_id'];
        $wlid     = $_POST['wlid']??0;
        $ex_app_id = $appointmentModel->where('patient_id',$patient_id)->where('appointment_date',date('Y-m-d'))->findColumn('id')[0]??'';
        $doctor_id      = $_POST['assigned_doctor'];
        $visit_status   = BluelimeConfiguration::VISIT_STATUS_NEW;
        $registered_by  = $_POST['registered_by'];
        $bill_id        = NULL;

        $appointment_id        = !empty($_POST['app_id'])?$_POST['app_id']:$ex_app_id;
        //BluelimeSystem::show_and_die($appointment_id);

        $openVisit = $visitModel->where('visit_status !=', BluelimeConfiguration::VISIT_STATUS_CLOSED)->where('patient_id',$patient_id)->findAll();
        if(!empty($openVisit))
            return redirect()->to(site_url('/hospital/reception/revisit?id='.$patient_id));

        $patient = $patientModel->find($patient_id);
        $patient_name = @$patient['first_name'].' '.@$patient['middle_name'].' '.@$patient['last_name']??'';
        $mobile = @$patient['mobile'];
        
        $doctor = $userModel->find($doctor_id);
        $doctor_name = @$doctor['name'] ??'';
        
        $service_id = $_POST['service_id'];
        $is_free = @$_POST['is_free'] ?? 0;
        $is_enforced = @$_POST['is_enforced'] ?? 0;

        //BluelimeSystem::show_and_die($is_enforced);
        
        $user = $userModel->find($registered_by);
        $registered_by_name = @$user['name'] ??'';
        
        $service_name = $_POST['service_name'];
        $service_type = $_POST['service_type'];

        $reference = $_POST['reference'] ?? '';

        $extra = '{"reference" : "'. $visitModel->db->escapeString($reference).'"}';  
        
        $queue_no = $visitModel->where('doctor_id',$doctor_id)->where('visit_date >= ',date("Y-m-d%"))->findColumn('max(queue_no)');

        $data = [
            'patient_id'   => $patient_id,
            'doctor_id'    => $doctor_id,
            'visit_status'     => $visit_status,
            'registered_by' => $registered_by,
            'bill_id'=> $bill_id,
            'appointment_id'=> $appointment_id,
            'patient_name'=> $patient_name,
            'mobile'=> $mobile,
            'is_free'=> $is_free,
            'doctor_name'=> $doctor_name,
            'service_id'=> $service_id,
            'registered_by_name'=> $registered_by_name,
            'service_name'=> $service_name,
            'service_type'=> $service_type,
            'extra' => $extra,
            'waiting_id' => $wlid,
            'queue_no' => intval($queue_no[0]) + 1
        ];
        

        $today = date("Y-m-d");        
        //last_visit_bill = $visitModel->get_patient_last_visit_bill($patient_id);
        $last_visit_bill = $visitModel->get_last_consultation_bill_date($patient_id);
        $days_after_last_visit = !empty($last_visit_bill) ? BluelimeSystem::date_difference($today, $last_visit_bill['billed_date']) : -1;
         
        
        $visit_id = $visitModel->insert($data,true);

        $notfiy_data = [
            'notification_type'=>  "Nurse Waiting",
            'description'=> $patient_name,
            'link_type'=> 'visit_id',
            'link_id'=> $visit_id,
         ];
         $notfiy_data_doctor = [
            'notification_type'=>  "Doctors Waiting",
            'link_type'=>  "visit_id",
            'description'=> $patient_name,
            'user_id'=> $doctor_id,
            'link_id'=> $visit_id,
         ];
         
        $notificationModel->insert($notfiy_data_doctor,true);
        $notificationModel->insert($notfiy_data,true);
        
        $appointmentModel -> update($appointment_id,['appointment_status' => BluelimeConfiguration::APPOINTMENT_VISITED,'visit_id'=>$visit_id]);
        $waitingListModel -> update($wlid,['status' => -1]);
        
        $appointmentModel->updatelatevisit($appointment_id);
        $company_id = $patient['company_id'];
        $company = $companyModel->find($company_id);

        if (($company_id > 0) && ($company['unpaid_visit_days']) > 0 ){
            $upaid_visit_days = $company['unpaid_visit_days'];
        }
        else{
            $upaid_visit_days = \BluelimeConfiguration::UNPAID_VISIT_DEFAULT_DAYS;
        }
        

        if(($days_after_last_visit <= $upaid_visit_days) && ($days_after_last_visit != -1) && $is_enforced != 1)
        {
            $visitModel->update($visit_id,['visit_status' => \BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN, 'bill_id'=>-1]);
            
            return redirect()->to(site_url('/hospital/reception/revisit?id='.$patient_id));
        }
       
        if( ($is_free > 0))
        {
            //$visitModel->update($visit_id,['visit_status' => \BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN, 'bill_id'=>$last_visit_bill['id']??-1]);
            $visitModel->update($visit_id,['visit_status' => \BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN, 'bill_id'=>-1]);
            
            return redirect()->to(site_url('/hospital/reception/revisit?id='.$patient_id));
        }
        
        $bill_id=$this->createbill(NULL,$visit_id,$registered_by);
        
        
        //if ($company_id > 0){
        //$visitModel->update($visit_id,['visit_status' => BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN]);
        //return redirect()->to(site_url('/hospital/reception/invoicebill?id='.$bill_id.'&order_id=0'));
        //}
        return redirect()->to(site_url('/hospital/reception/invoicebill?id='.$bill_id.'&order_id=0'));
    }

    public function addorder()
    {
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $serviceModel = model('Hospital/ServiceModel');
        $servicePriceListModel = model('Hospital/PricelistModel');
        $servicePriceListDetailModel = model('Hospital/PricelistDetailModel');
        $companyModel = model('Hospital/CompanyModel');
        
        $patient_id     = @$_POST['patient_id']??0;
        $is_free_service     = @$_POST['is_free_service']??0;
       // BluelimeSystem::show_and_die($is_free_service);
        $visit_id     = @intval($_POST['visit_id'])??0;
        $company_id = 0;
        $company_name = "";
        $bill_type = "Cash";
        $is_external = $_POST['is_external'];
        if($is_external == 1){
            $is_external = true;
            $full_name = ucwords(strtolower($_POST['first_name'].' '.$_POST['middle_name'].' '.$_POST['last_name']));
            $mobile = $_POST['mobile'];
            $sex = $_POST['sex'];
            $date_of_birth = $_POST['date_of_birth'];
            $description = $_POST['description'];
        }
        else{
            $is_external = false; 
            $patient = $patientModel->find($patient_id);
            $full_name = $patient['first_name'].' '.$patient['middle_name'].' '.$patient['last_name'];
            $company_id = $patient['company_id']??0;
            $company_name = $patient['company_name'];
            $company = $companyModel->find($company_id);
            $bill_type = (intval($company_id) > 0 && $company['is_cash']==0)?"Credit":"Cash";
            $mobile = $patient['mobile'];
            $sex = $patient['sex'];
            $date_of_birth = $patient['date_of_birth'];
            $description = '';
        }
        
        $registered_by = intval($_POST['registered_by']);
        $order_by = $_POST['order_by'];
        $doctor = $userModel->find($order_by);
        $order_by_name = $doctor['name'] ?? '';

        $mobile = $_POST['mobile'];
        
        $servicePriceList = $servicePriceListModel->whereIn('company_id',[BluelimeConfiguration::DEFAULT_PRICE_LIST_ID,$company_id])->orderBy('company_id','DESC')->findAll();

        $service_price_list_id = $servicePriceList[0]['id'];

        $service_list = [];
        $service =[];
        for($i=0; $i<count($_POST['service_id']); $i++) {
            
            $service_name = $serviceModel->find($_POST['service_id']);
            $service = [];
            if($_POST['bill_quantity'][$i] > 1){
                $service_price_detail = $servicePriceListDetailModel->where('pricelist_id',$service_price_list_id)->where('service_id',$_POST['service_id'][$i])->findAll();
                $service_price = $service_price_detail[0]['service_price'] ?? 1;
                if($service_price == 1){
                    $service['price'] = $_POST['bill_quantity'][$i];
                    $_POST['bill_quantity'][$i] = 1;
                }
            }
            if($is_free_service == 1){

                $service['price'] = 0;
                //BluelimeSystem::show_and_die($is_free_service);
            }
            $service = [...$service,'service_id'  =>   $_POST['service_id'][$i],'service_name' => $service_name[$i]['service_name'],'bill_quantity' => $_POST['bill_quantity'][$i],'quantity' => $_POST['bill_quantity'][$i]];

            array_push($service_list ,$service);
        }
       
        $bill_id = BluelimeSystem::create_bill($sex,$date_of_birth,$description,$patient_id, $full_name, $mobile, $company_id, $company_name, $visit_id, $bill_type,$order_by, $order_by,$order_by_name, $service_list, $is_external,$is_free_service);
        if($is_free_service == 1){
            return redirect()->to(site_url('/hospital/reception/revisit?id='.$patient_id));
        }
       return redirect()->to(site_url('/hospital/reception/invoicebill?id='.$bill_id));
    }

    private function createbill($oid,$vid,$rid) :int
    {
        $billModel = model('Hospital/BillModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $companyModel = model('Hospital/CompanyModel');
        $notificationModel = model('Hospital/NotificationModel');

        $patient_id  = @$_POST['patient_id']??0;
        $visit_id   = $vid;
        $order_id   = BluelimeSystem::convert_array_to_string($oid);
        $company_id   = @$_POST['company_id'];

        $is_external = @$_POST['is_external'];
        if($is_external == 1){
            $patient_name = $_POST['first_name'].' '.$_POST['middle_name'].' '.$_POST['last_name'];
        }
        else{
            $patient = $patientModel->find($patient_id);
            $patient_name = $patient['first_name'].' '.$patient['middle_name'].' '.$patient['last_name'];
        }
                
        $user = $userModel->find($rid);
        $registered_by_name = $user['name'] ??'';
        $registered_by   = $user['id'];
        $mobile   = $_POST['mobile'];
        
        $bill_status   = 0;

        $company = $companyModel->find($company_id);
        $company_name = $company['company_name'] ??'';

        if(($company['is_active']??0) == 0){
            $company_id = 0;
            $patient['company_id'] = 0;
            $company_name = '';
        }

        $bill_type   = ((intval($company_id) >=1) && boolval($company['is_cash']==0))? "Credit" : "Cash";
        
     $data = [
           'patient_id'=>  $patient_id,
           'visit_id'=> $visit_id,
           'order_id'=> $order_id,
           'bill_type'=> $bill_type,
           'registered_by'=> $registered_by,
           'patient_name'=>$patient_name,
           'registered_by_name'=> $registered_by_name,
           'company_id'=>$company_id,
           'company_name'=>$company_name,
           'mobile'=>$mobile,
           'bill_status'=>$bill_status

        ];
        
        $id = $billModel->insert($data,true);
        $notfiy_data = [
            'notification_type'=>  "New Bill",
            'description'=> $patient_name,
            'link_type'=> 'bill_id',
            'link_id'=> $id,
         ];
        $this->createbilldetail($id, $company_id);//,$patient_id,$company_id);
        $notificationModel->insert($notfiy_data,true);
        
       return $id;
    }

    private function createbilldetail($bid, $company_id)
    {
        //$patientModel = model('Hospital/PatientModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $serviceModel = model('Hospital/ServiceModel');
        

        $bill_id  = $bid;
        //$patient = $patientModel->find($_POST['patient_id']??0);
        //$company_id = intval($patient['company_id']??0);
       
        $bill_detail_data = [];
        
        
        //BluelimeSystem::print_associated_array($_POST);

        for($i=0; $i<count($_POST['service_id']); $i++) {
            $service_id =   $_POST['service_id'][$i];
            $service = $serviceModel->find($service_id);
            $service_type = $service['service_type'];
            $data = array(
                'service_id'=> $_POST['service_id'][$i],
                'service_name'=>$_POST['service_name'][$i],
                'bill_quantity'=>$_POST['bill_quantity'][$i],
                'service_type'=>$service_type[$i]

            );
            array_push($bill_detail_data, $data);
        }
        $bill_price_detail = BluelimeSystem::add_price_detail_in_service_list($company_id, $bill_detail_data);
        $bill_detail_data = [];
        foreach($bill_price_detail as $service){
            $billDetail = [
                'bill_id' => $bill_id,
                'service_id' => $service['service_id'],
                'service_name' => $service['service_name'],
                'service_type' => $service['service_type'],
                'bill_quantity' => $service['bill_quantity'],
                'bill_amount' => $service['price'],
                'bill_discount' => $service['discount'],
                'bill_date' => BluelimeSystem::date_now(),
                'service_status' => 0
            ];

            array_push($bill_detail_data, $billDetail);
           // BluelimeSystem::show_and_die($billDetail);
        }
        $billDetailModel->insertBatch($bill_detail_data);
 
    }

    public function requestbilldeletion(){
        $id = $_GET['id'] ?? '-1';
        $billModel = model('App\Models\BillModel');
        $update_data = ['bill_status' => BluelimeConfiguration::BILL_REMOVE_REQUESTED];
        $billModel->update($id, $update_data);
        return redirect()->to('/dental/reception/pendingbills');
    }

    public function send_to_pos(){
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $pOSBillModel = model('POS/POSBillModel');
        $companyModel = model('Hospital/CompanyModel');

        $bill_id = $_POST['bill_id'];
        $patient_id = $_POST['patient_id'];
        $patient_name = $_POST['patient_name'];
        $company_id = $_POST['company_id'] ?? -1;

        $bill = $billModel->find($bill_id);

        if($bill['sent_to_pos'])
            return redirect()->to(site_url(relativePath: '/hospital/reception/pendingbills'));

        $company = $companyModel->find($company_id);
        $company_name = $company['company_name'] ?? '';
        $company_tin = (($_POST['tin'] !='') ? $_POST['tin'] : $company['tin_number'] ?? '');
        
        $patient_name = preg_replace('/\s+/', ' ', $patient_name);
        $patient_names = explode(' ', $patient_name);
        $patient_name = trim(($patient_names[0] ?? '') . ' ' . ($patient_names[1]));

        $bill_detail_id_list = $billDetailModel->where('bill_id', $bill_id)->where('service_status !=', BluelimeConfiguration::SERVICE_STATUS_DELAY)->findColumn('id');
        if(empty($bill_detail_id_list))
            $bill_detail_id_list = [-1];

        $bill_details = $billDetailModel->whereIn('id', $bill_detail_id_list)->findAll();
        
        $sales_collector = [];
        $total_price = 0;
        foreach($bill_details as $row){
            $sales_line = [
                'item_id' => $row['service_id'],
                'unit_of_measurment' => 'Pcs',
                'item_quantity' => $row['bill_quantity'],
                'item_type' => $row['service_type'],
                'unit_price' => $row['bill_amount'],
                'discount_in_percent' => ($row['bill_discount']),
                'item_description' => $row['service_name'],
                'is_vat' => false,
            ];
            $total_price += ($row['bill_amount'] - ($row['bill_amount']*$row['bill_discount']/100)) * $row['bill_quantity'];
            array_push($sales_collector, $sales_line);
        }

        //BluelimeSystem::show_and_die($total_price);

        $is_credit = ($bill['bill_type'] ?? 'Cash') == 'Credit' ? true : false;

        $pos_bill = BluelimePOS::create_pos_bill('Hospital', $sales_collector, $patient_id, $patient_name,customer_tin:$company_tin, company_name:$company_name,is_credit :$is_credit);
        
        $success = BluelimePOS::send_to_pos($pos_bill);
        if(!$success){
            $pOSBillModel->delete($pos_bill['id']);
            return redirect()->to(site_url('/hospital/reception/invoicebill?id='.$bill_id.'&error_code=E_3_001'));
        }
        else
            $billModel->update($bill_id,['bill_status' => BluelimeConfiguration::BILL_SENT_TO_POS, 'pos_bill_id'=>$pos_bill['id'], 'sent_to_pos' => 1]);
            
        return redirect()->to(site_url(relativePath: '/hospital/reception/pendingbills'));
    }


    /*
    public function recievepayment()
    {
        $billPaymentModel = model('Hospital/BillPaymentModel');
        $visitModel = model('Hospital/VisitModel');
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $orderDetailModel = model('Hospital/OrderDetailModel');
        $notificationModel = model('Hospital/NotificationModel');
        
        $bill_id = $_POST['bill_id'];
        $do_not_open_visit = $_POST['do_not_open_visit'] ?? 0;
        
        $visit_id = $billModel->where('id',$bill_id)->findColumn('visit_id');

        //Check if the bill still exists. It might be removed by the doctors
        if(empty($visit_id))
            return redirect()->to(site_url('/hospital/reception/invoicebill?id='.$bill_id));

        $service_type = $billDetailModel->where('bill_id',$bill_id)->where('service_type',"Consultation")->findColumn('service_type');

        $payed_amount = $_POST['paid_amount'];
        $registered_by = $_POST['registered_by'];
        $registered_by_name = $_POST['registered_by_name'];
        $payed_discount = $_POST['total_discount'];
        //$tin = $_POST['tin'];
        //$fs_number = '';
        $company_name = $_POST['company_name'];
        $company_id = $_POST['company_id'];
        $patient_name = $_POST['patient_name'];
        $patient_id = $_POST['patient_id'];
        $payment_type = $_POST['payment_type'];
        $mobile = $_POST['mobile'];
        $due_amount = $_POST['due_amount'];
        //$payment_mode = $_POST['payment_mode'];
        
     $data = [
                'bill_id'=>  $bill_id,
                'payed_amount'=> $payed_amount,
                'registered_by'=> $registered_by,
                'registered_by_name'=> $registered_by_name,
                'payed_discount'=> $payed_discount,
                //'tin'=> $tin,
                //'fs_number'=> $fs_number,
                'company_name'=> $company_name,
                'company_id'=> $company_id,
                'patient_name'=> $patient_name,
                'patient_id'=> $patient_id,
                'payment_type'=> $payment_type,
                'mobile'=> $mobile,
                'due'=> $due_amount,
                //'payment_mode'=> $payment_mode
            ];
        
        $paymnet_id = $billPaymentModel->insert($data,true);
        
        $orderMainModel->where('bill_id', $bill_id)->update(row:['order_status' => BluelimeConfiguration::LAB_ORDER_PAID]);
        
        $bill_detail_id_list = $billDetailModel->where('bill_id', $bill_id)->where('service_status !=', BluelimeConfiguration::SERVICE_STATUS_DELAY)->findColumn('id');
        if(empty($bill_detail_id_list))
            $bill_detail_id_list = [-1];

        $orderDetailModel->whereIn('bill_detail_id', $bill_detail_id_list)->update(row:['payment_id'=>$paymnet_id]);
        
        $bill_details = $billDetailModel->whereIn('id', $bill_detail_id_list)->findAll();
        
        $sales_collector = [];
        foreach($bill_details as $row){
            $sales_line = [
                'item_id' => $row['service_id'],
                'unit_of_measurment' => 'Pcs',
                'item_quantity' => $row['bill_quantity'],
                'item_type' => $row['service_type'],
                'unit_price' => $row['bill_amount'],
                'item_description' => $row['service_name'],
                'is_vat' => false,
            ];
            
            array_push($sales_collector, $sales_line);
        }
        $pos_bill = BluelimePOS::create_pos_bill('Hospital', $sales_collector, $patient_id, $patient_name);
        $success = BluelimePOS::send_to_pos($pos_bill);
        if(!$success){
            return redirect()->to(site_url('/hospital/reception/invoicebill?id='.$bill_id.'&fail=1&error_code=E_3_001'));
        }
        else
            $billModel->update($bill_id,['bill_status' => BluelimeConfiguration::BILL_SENT_TO_POS, 'pos_bill_id'=>$pos_bill['id']]);

        /*
        if(!empty($service_type)){
            if($do_not_open_visit)
                $visitModel->update($visit_id,['bill_id'=>$bill_id]);            
            else{
                $visitModel->where('visit_status', BluelimeConfiguration::VISIT_STATUS_NEW)->update($visit_id,['visit_status' => BluelimeConfiguration::VISIT_STATUS_OPEN,'bill_id'=>$bill_id]); 
                $notfiy_data = [
                    'notification_type'=>  "Nurse Waiting",
                    'description'=> $patient_name,
                    'link_type'=> 'visit_id',
                    'link_id'=> $visit_id,
                 ];
                $notificationModel->insert($notfiy_data,true);
            }
        }
        

        return redirect()->to(site_url('/hospital/reception/pendingbills'));
    }
    */

    public function pettycash()
    {
        $data['active_menu'] = 'petty_cash';
        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();
        $pettyCashModel = model('Hospital/PettyCashModel');

        $pettycashs = $pettyCashModel->where('status',1 )->orderBy('regisered_date','DESC')->LIMIT(15)->findAll();
        $petty_total = $pettyCashModel->where('status',1 )->findColumn('sum(petty_cash_amount)');
        $expense_total = $pettyCashModel->where('status',1 )->findColumn('sum(expence_amont)');
        
        $data['pettycashs'] = $pettycashs;
        $data['petty_total'] = $petty_total[0];
        $data['expense_total'] = $expense_total[0];


        

        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/petty_cash')

            .view('Include/_footer');
    }
    
    public function addpettycash()
    {
        $pettyCashModel = model('Hospital/PettyCashModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();

        $petty_cash_amount = $_POST['petty_cash_amount'];
        $expence_from = $_POST['expence_from'];
        $reference = $_POST['reference'];
        $registered_by = $loggeduserdata['id'];
        $registered_by_name = $loggeduserdata['name'];
        $expence_amont = $_POST['expence_amont'];
        $expence_reason = $_POST['expence_reason'];
        $status = $_POST['status'];
        $payment_type = $_POST['payment_type'];

        $data['petty_cash_amount'] = $petty_cash_amount;
        $data['expence_from'] = $expence_from;
        $data['reference'] = $reference;
        $data['registered_by'] = $registered_by;
        $data['registered_by_name'] = $registered_by_name;
        $data['expence_amont'] = $expence_amont;
        $data['expence_reason'] = $expence_reason;
        $data['status'] = $status;
        $data['payment_type'] = $payment_type;
       
        $pettyCashModel->insert($data);
        $id = $pettyCashModel->getInsertID();

        if($id == 0){
            $alert_body_html = "There was an error on saving data ";
            BluelimeSystem::set_alert($alert_body_html,"OK", "WARNING");
            return redirect()->to(base_url()."hospital/reception/petty_cash?fail=2");
        }

        return redirect()->to(base_url()."hospital/reception/pettycash");
    }

    public function addtemplate()
    {
        $templateModel = model('Hospital/TemplateModel');

        $template_name = $_POST['template_name'];
        $template_content = $_POST['template_content'];
        $id = $_POST['pid'];
        
        $data = [
            'template_name'=>  $template_name,
            'template_content'=> $template_content,
            
             ];
       
        $templateModel->insert($data);


        return redirect()->to(base_url()."hospital/reception/revisit?id=".$id);
    }

    public function deletepettycash()
    {
        $pettyCashModel = model('Hospital/PettyCashModel');
        
        $id = $_GET['id'];
        $data = ['status'=> 0];
        //BluelimeSystem::show_and_die($id);
        $pettyCashModel->update($id,$data);
        
        return redirect()->to(base_url()."hospital/reception/pettycash");
    }

    public function deletependingbill()
    {
        $billModel = model('Hospital/BillModel');
        
        $id = $_GET['bid'];
        $bill = $billModel->find($id);
        if(!empty($bill))
        {
            $visit_id = $bill['visit_id'];
            $visitModel = model('Hospital/VisitModel');
            $visit = $visitModel->find($visit_id);
            $visit_status = $visit['visit_status'] ?? -1;
            if($visit_status == 0)
                $visitModel->delete($visit_id);
        }
        $data = ['bill_status'=> BluelimeConfiguration::BILL_REMOVED];
        //BluelimeSystem::show_and_die($id);
        $billModel->update($id,$data);
        
        return redirect()->to(base_url()."hospital/reception/pendingbills");
    }
    public function reverserecievedbill()
    {
        $billModel = model('Hospital/BillModel');
        $pOSBillModel = model('POS/POSBillModel');

        $bill_id = $_GET['bill_id'];

        $bill = $billModel->find($bill_id);

        if(!empty($bill['fs_number']))
            return redirect()->to(base_url()."hospital/reception/pendingbills?error_code=E_2_001");
        if($bill['bill_status'] == BluelimeConfiguration::BILL_SENT_TO_POS){
            $pos_bill_id = $bill['pos_bill_id'];
            if(!BluelimePOS::delete_invoice($pos_bill_id))
                return redirect()->to(base_url()."hospital/reception/pendingbills?error_code=E_2_002");
            
            $data = ['bill_status'=> BluelimeConfiguration::BILL_PENDING, 'pos_bill_id' => 0, 'sent_to_pos' => 0];
            $billModel->update($bill_id,$data);
            return redirect()->to(base_url()."hospital/reception/pendingbills");
        }
        return redirect()->to(base_url()."hospital/reception/pendingbills?error_code=E_2_002");
    }

    public function sendsms()
    {
        $patientModel = model('Hospital/PatientModel');
        $id = $_GET['id']??0;
        $patient = $patientModel->find($id);
        
        //$website = "\nBook your Appointment @ www.rankderma.com";
        $branch = $_ENV['BRANCH']??"";
        $welcome_message = "Hello, ".$patient['first_name']." ".$patient['middle_name']." ".$patient['last_name']."\nWelcome to RANK Specialized Dermatology Clinic $branch.\nYour card number is MRN-$id\n";
        //$telegram_bot = "Join Telegram: https://t.me/RankDerma_bot";
       //BluelimeSystem::show_and_die($welcome_message.$telegram_bot.$website);
        BluelimeSystem::send_sms_message(@$_GET['mob'], $welcome_message);
        BluelimeSystem::send_telegram_message(@$_GET['mob'], $welcome_message);

        return redirect()->to(site_url('/hospital/reception/revisit?id='.$id));
    }
    public function viewapp()
    {
        $webAppointmentModel = model('Hospital/WebAppointmentModel');
        //substr("Hello world",1);
        $app = $webAppointmentModel->findColumn('data');
        
        //echo $tr = rtrim($app,"\n");

        $applist = [];

        foreach($app as $ap){
            echo $ap.'<br>';
            echo $dt = substr(substr($ap,17),0,10)."<br>";
            echo $time = substr(substr($ap,28),0,5)."<br>";
            $bs=strpos($ap,"(");
            $be=strpos($ap,")");
            $name_position = strpos($ap,"Name: ");
            $fname_position = strpos($ap,"F. Name: ");
            $gname_position = strpos($ap,"G. Name: ");
            $mobile_position = strpos($ap,"Mobile: ");
            echo $service = substr(substr($ap,41),0,($be-$bs-1))."<br>";
            echo $name = substr(substr($ap,$name_position),6,($fname_position-$name_position-7))."<br>";
            echo $fname = substr(substr($ap,$fname_position),8,($gname_position-$fname_position-9))."<br>";
            echo $gname = substr(substr($ap,$gname_position),8,($mobile_position-$gname_position-9))."<br>";
            echo $mobile = substr(substr($ap,$mobile_position),8,10)."<br>";
        }
        BluelimeSystem::show_and_die($app);
    }

    public function doctorschedule()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $scheduleModel = model('Hospital/ScheduleModel');
        $skipDateModel = model('Hospital/SkipDateModel');
        $userModel = model('System/UserModel');
        
        $selected_doctor = $_REQUEST['did'] ?? 0;
        $skips=$skipDateModel->where('doctor_id',$selected_doctor)->findAll();
        $role = array('DOCTOR','NURSE');
        $doctors = $userModel->whereIn('role',$role)->where('active', 1)->findAll();

        $data['skips'] = $skips;

        $schedules=$scheduleModel->where('doctor_id',$selected_doctor)->findAll();

        $data['loggeduserdata'] = $loggeduserdata;
        $data['schedules'] = $schedules;
        $data['doctors'] = $doctors;
        $data['active_menu'] = 'patient_waiting';

        return view('Include/_header', $data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/Reception/_left_sidebar_reception')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Reception/doctor_schedule')
        .view('Include/_footer');
    }

    public function addschedule()
    {
        $scheduleModel = model('Hospital/ScheduleModel');
        $userModel = model('System/UserModel');

        $schedule_id=$scheduleModel->where('doctor_id',$_POST['doctor_id'])->findColumn('id');
        //if(empty($schedule_id))
           // $schedule_id=-1;
        //$scheduleModel->delete($schedule_id);
       //BluelimeSystem::show_and_die($_POST['doctor_id']);
        for($i=0; $i<count( $_POST['day']); $i++) {
            $doctor_name = $userModel->where('id', $_POST['doctor_id'])->findColumn('name')[0];
            $data = array(
             'doctor_id' => $_POST['doctor_id'] , 
             'doctor_name' => $doctor_name , 
             'start_time' => $_POST['s_time'] , 
             'end_time' => $_POST['e_time'] , 
             'day' => $_POST['day'] [$i] , 
             'per_patient_time' => $_POST['p_time'] , 
             'schedule_start_date' => $_POST['start_date']  
             );
        $scheduleModel->insert($data,true);
        //BluelimeSystem::export_schedule($data);
        }   
        return redirect()->to(site_url('/hospital/reception/doctorschedule?did='.$_POST['doctor_id']));
        
    }
    public function removescheduleday()
    {
        $scheduleModel = model('Hospital/ScheduleModel');
        $appointmentModel = model('Hospital/AppointmentModel');
        $notificationModel = model('Hospital/NotificationModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $today = date('Y-m-d').'%';
        $active_appointments = $appointmentModel->where('appointment_date >= ',$today)->where('schedule_id',$_POST['schedule_id'])->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)->findColumn('count(id)')[0];
        
        $appointmentModel->where('schedule_id',$_POST['schedule_id'])->set('cancelled_by' , $loggeduserdata['name'])->set('cancelation_reason' , "Schedule Removed")->set('appointment_status',\BluelimeConfiguration::APPOINTMENT_CANCELED)->update();
        //BluelimeSystem::show_and_die($active_appointments);
        $scheduleModel->delete($_POST['schedule_id']);
        
        echo "Schedule Day Deleted. All Appointments on the date are Cancelled";
        if($active_appointments >= 1){
        $notfiy_data = [
            'notification_type'=>  "Cancelled Appointments",
            'description'=> "Schedule Day Deleted.",
            'link_type'=> 'schedule_id',
            'link_id'=> $_POST['schedule_id'],
         ];
        $notificationModel->insert($notfiy_data,true);
        
        
        }
        BluelimeSystem::delete_schedule($_POST['schedule_id']);
        
    }
    public function cleardoctorsschedule()
    {
        $scheduleModel = model('Hospital/ScheduleModel');

        $scheduleModel->delete($_GET['sid']);
       
        return redirect()->to(site_url('/hospital/reception/doctorschedule'));
        
    }

    public function addskipdate()
    {
        $skipDateModel = model('Hospital/SkipDateModel');
        $notificationModel = model('Hospital/NotificationModel');
        //$loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $selected_doctor = $_REQUEST['did']??0;
        //BluelimeSystem::show_and_die($selected_doctor);
        $skip_date = $_POST['skip_date'];
        $data = [
            'skip_date'    => $skip_date,
            'doctor_id'   => $selected_doctor
        ];
        if(!empty($selected_doctor)){
        $id = $skipDateModel->insert($data,true);
        $notfiy_data = [
            'notification_type'=>  "Skip Date",
            'description'=> $skip_date,
            'link_type'=> 'skip_id',
            'user_id'=> $selected_doctor,
            'link_id'=> $id,
         ];
        $notificationModel->insert($notfiy_data,true);
        }
        return redirect()->to(site_url('/hospital/reception/doctorschedule?did='.$selected_doctor));
        
    }
    public function deleteskipdate()
    {
        $skipDateModel = model('Hospital/SkipDateModel');
        
        $skipDateModel->where('id',$_GET['id'])->delete($_GET['id']);
          
        return redirect()->to(site_url('/hospital/reception/doctorschedule?did='.$_GET['did']));
        
    }
    public function newmanualcard_mobile()
    {
        $loggeduserdata = BluelimeAuthentication::get_logged_user_data();
        $patientModel = model('Hospital/PatientModel');
        $patient = $patientModel->find($_GET['id']);
        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        return view('Hospital/Reception/new_manualcard_mobile', $data);
    }
    public function addmanualcard()
    {
        $patient_id     = $_POST['patient_id'];
        
        $image_list = $this->upload_manual_card();

        return redirect()->to(site_url('/hospital/reception/revisit?id='.$patient_id.'&image_list='.$image_list));
    } 
    public function uploadbiopsyrequest()
    {
        $order_id     = $_POST['order_id'];
        
        $image_list = $this->upload_biopsy_request();

        return redirect()->to(site_url('/hospital/reception/order_detail?order_main_id='.$order_id.'&image_list='.$image_list));
    } 
    public function addmanualcard_mobile()
    {
        $patient_id     = $_POST['patient_id'];
        
        $image_list = $this->upload_manual_card(10);

        return redirect()->to(site_url('/hospital/reception/newmanualcard_mobile?id='.$patient_id.'&image_list='.$image_list));
    }
    public function deleteallcards()
    {
        $patientModel = model('Hospital/PatientModel');
        $patient_id     = $_POST['patient_id'];
        
        $patientModel->update($patient_id, ['attached_old_card' => ""]);

        return redirect()->to(site_url('/hospital/reception/revisit?id='.$patient_id));
    }
    private function upload_biopsy_request($quality = -1){
        $orderMainModel = model('Hospital/OrderMainModel');
        
        $image_list = trim($_POST['image_list'] ?? '');
        
        $order_id     = $_POST['order_id'];
       
        $originalImage = $_FILES['picture']['tmp_name'];
        
        $fl1Name = sprintf('%05d', $order_id).'_'.date('Ymdhms');
        
        $file_path = $_ENV['IMAGE_PATH_PATHOLOGY'];

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
    function delete_biopsy_document(){
        $image_name = $_POST['image_name'];
        $order_id = $_POST['order_id'] ?? -1;
        $orderMainModel = model('Hospital/OrderMainModel');
        $order = $orderMainModel->find($order_id);

        if (!$order) {
            return BluelimeSystem::get_error_ajax_return('Order not found');
        }

        $scanned_documents = $order['scanned_documents'];
        $scanned_documents_array = explode(',', $scanned_documents);
        $scanned_documents_array = array_filter($scanned_documents_array, function($name) use ($image_name) {
            return trim($name) !== trim($image_name);
        });
        $scanned_documents = implode(',', $scanned_documents_array);

        $image_path = $_ENV['IMAGE_PATH_PATHOLOGY'].$image_name;

        $update_result = $orderMainModel->update($order_id, ['scanned_documents'=>$scanned_documents]);

        if (!$update_result) {
            return BluelimeSystem::get_error_ajax_return('Failed to update order');
        }

        if(is_file($image_path)){
            if (!unlink($image_path)) {
                return BluelimeSystem::get_error_ajax_return('Failed to delete file');
            }
        }
        return BluelimeSystem::get_success_ajax_return('');
    }
    public function patientqueue(){
        $visitModel = model('Hospital/VisitModel');
        $notificationModel = model('Hospital/NotificationModel');
        $data['active_menu'] = 'patientqueue';
        
        $visits = $visitModel->whereIn('visit_status',[BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN,BluelimeConfiguration::VISIT_STATUS_OPEN])->orderBy('doctor_name asc')->orderBy('queue_no asc')->findAll();
        foreach($visits as $index => $visit){
            //TODO improve performance by limiting too many calls to DB
            $visits[$index]['view_status'] = $notificationModel->where('link_id',$visit['id'])->where('user_id',$visit['doctor_id'])->findColumn('status')[0]??0;
        }
        $doctors = $visitModel->distinct()->whereIn('visit_status',[BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN,BluelimeConfiguration::VISIT_STATUS_OPEN])->findColumn('doctor_name')??[];
        //BluelimeSystem::show_and_die($doctors);
        $data['visits'] = $visits;
        $data['doctors'] = $doctors;
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Hospital/Reception/_left_sidebar_reception')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Reception/patient_queue')

        .view('Include/_footer');
    }
    public function editsex()
    {
        $patientModel = model('Hospital/PatientModel');
        
        $id = $_POST['pid']??-1;
        $sex = @$_POST['sex'];
        
        $data = ['sex'=> $sex];
        $patientModel->update($id,$data);
    }

    public function clinic_bill_groupedby_service_name(){
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
        $to_date = ($_GET['end_date'] ?? date('Y-m-d')). ' 23:59:59';
        $payment_method = $_GET['payment_method'] ?? 'Cash';

        $billModel = model('Hospital/BillModel');

        $bills = $billModel->where('bill_type', $payment_method)->where('payed_date >=', $from_date)->where('payed_date <=', $to_date)->where('bill_status',BluelimeConfiguration::BILL_RECIEVED)->findAll();

        $bill_details = BluelimeSystem::gather_objects_by_reference($bills,'id','Hospital/BillDetailModel','bill_id');
        
        $bill_report_data = [];
        $total = 0;
        foreach($bill_details as $bill_detail){
            if($bill_detail['service_status'] == BluelimeConfiguration::SERVICE_STATUS_DELAY)
                continue;
            
            $index = 'item_'.$bill_detail['service_id'].'_'.$bill_detail['bill_amount'];
            if(!isset($bill_report_data[$index])){
                $bill_report_data[$index] = [
                    'unit_price' => $bill_detail['bill_amount'],
                    'service_name' => $bill_detail['service_name'],
                    'service_type' => $bill_detail['service_type'],
                    'quantity' => 0,
                    'sub_total' => 0,
                    'vat' => 0,
                    'total' => 0,
                ];
            }

            $sub_total = $bill_detail['bill_quantity'] * $bill_detail['bill_amount'];
            $bill_report_data[$index]['quantity'] += $bill_detail['bill_quantity'];
            $bill_report_data[$index]['sub_total'] += $sub_total;
            $bill_report_data[$index]['total'] += $sub_total;
            $total += $sub_total;
        }

        $bill_report_data = BluelimeSystem::sort_array($bill_report_data,['service_type','service_name']);

        $data = [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'bill_report_data' => $bill_report_data,
            'total' => $total,
            'active_menu'   => "sales_summary_report",
            'active_sub_menu'   => ""
        ];

        return view('Hospital/Reception/clinic_bill_groupedby_service_name', $data);
    }
    public function sendprescription()
    {
        $prescription_group_id = $_POST['prescription_group_id'] ?? -1;

        $prescriptionModel = model('Hospital/PrescriptionModel');
        $prescriptionDetailModel = model('Hospital/PrescriptionDetailModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');


        

        BluelimeSystem::show_and_die($prescriptionModel->WHERE('prescription_group_id',$prescription_group_id)->findAll()[0]);
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

       

        $data['compounding_mains'] = $compounding_mains;
        $data['prescription_detail'] = $prescription_detail;
        $data['active_menu'] = 'printoutlist';

        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)
            .view('Hospital/Reception/_left_sidebar_reception')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Reception/view_prescription')
            .view('Include/_footer');
        
    }
    public function save_online_prescription(){

        $onlinePrescriptionModel = model('Hospital/OnlinePrescriptionModel');
        $phone_number = $_POST['phone_number'] ?? '';
        $message = $_POST['message'] ?? '';
        $patient_name = $_POST['patient_name'] ?? '';
        $branch_from = $_POST['branch_from'] ?? '';
        $branch_to = $_POST['branch_to'] ?? '';
        $doctor_name = $_POST['doctor_name'] ?? '';
        $prescription_group_id = $_POST['prescription_group_id'];
        $image = $_POST['image'] ?? '';

        $image = substr($image,22);
        $image = base64_decode($image);
        
        $status = 0;
        $date_created = BluelimeSystem::date_now();

        $phone_number = BluelimeSystem::correct_mobile($phone_number);
        $data = [
            'branch' => 'Rank',
            'phone_number' => $phone_number,
            'message' => esc($message),
            'image' => $image,
            'status' => $status,
            'patient_name' => $patient_name,
            'branch_from' => $branch_from,
            'branch_to' => $branch_to,
            'doctor_name' => $doctor_name,
            'prescription_group_id' => $prescription_group_id,
            'date_created' => $date_created
        ];
        

        $pid = $onlinePrescriptionModel->where('prescription_group_id',$prescription_group_id)->where('branch_to',$branch_to)->findColumn('count(id)')[0];
        
        if(intval($pid) == 0){
            //die('xx');
            $onlinePrescriptionModel->insert($data);
            return '{"status":"success", "value":"'.$prescription_group_id.'"}';
        }
        else{
            //die('yy');
            return '{"status":"success", "value":"'.$prescription_group_id.'"}';
            
        }
    }

    public function referalrequestprint()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $notificationModel = model('Hospital/NotificationModel');
        $notificationModel->where('link_id',$_GET['rrid'])->where('notification_type','Referal Request')->set('status',BluelimeConfiguration::NOTIFICATION_SEEN)->update();
        
        
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

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/view_referal_request')

            .view('Include/_footer');
    }
    public function biopsyrequestprint()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $notificationModel = model('Hospital/NotificationModel');
        $notificationModel->where('link_id',$_GET['ebid'])->where('notification_type','External Biopsy Request')->set('status',BluelimeConfiguration::NOTIFICATION_SEEN)->update();
         
        
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

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/view_external_biopsy')

            .view('Include/_footer');
    }    
    public function labrequestprint()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $notificationModel = model('Hospital/NotificationModel');
        $notificationModel->where('link_id',$_GET['elid'])->where('notification_type','External Laboratory Request')->set('status',BluelimeConfiguration::NOTIFICATION_SEEN)->update();
        
        
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

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/view_external_laboratory')

            .view('Include/_footer');
    }
    public function biopsyorders()
    {
        $filter_name = $_GET['filter_name'] ?? '';
        $filter_mobile = $_GET['filter_mobile'] ?? '';
        $filter_status = $_GET['filter_status'] ?? '';
        $filter_histology_number = $_GET['filter_histology_number'] ?? '';

        $orderMainModel = model('Hospital/OrderMainModel');
        $biopsyResultModel = model('Hospital/BiopsyResultModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');

        
        $orders_query = $orderMainModel->where('order_service_type','Pathology')->where('order_status >=',1)->where('order_status <=',5)->where('hide',0);
        
        // Apply filters for pending orders
        if (!empty($filter_name)) {
            $orders_query->like('patient_name', $filter_name);
        }
        if (!empty($filter_mobile)) {
            $orders_query->like('mobile', $filter_mobile);
        }
        if (!empty($filter_status)) {
            $orders_query->where('order_status', $filter_status);
        }
        if (!empty($filter_histology_number)) {
            $orders_query->like('order_comment', $filter_histology_number);
        }
        
        $orders = $orders_query->findAll();
       
        
        foreach($orders as $index => $rt){
            $orders[$index]['patient_detail'] = $patientModel->where('id',$rt['patient_id'])->findAll()[0]??[];
            $orders[$index]['doctor'] = $userModel->where('id',$rt['ordered_by'])->findColumn('name')[0]??"Reception";
        }
       
        // 
        // Get approved orders with filters
        $approved_orders_query = $orderMainModel->where('order_service_type','Pathology')->where('order_status',6);
        
        // Apply filters for approved results
        if (!empty($filter_name)) {
            $approved_orders_query->like('patient_name', $filter_name);
        }
        if (!empty($filter_mobile)) {
            $approved_orders_query->like('mobile', $filter_mobile);
        }
        if (!empty($filter_histology_number)) {
            $approved_orders_query->like('order_comment', $filter_histology_number);
        }
        
        $approved_orders = $approved_orders_query->findAll();
        
        $approved_order_ids = array_column($approved_orders, 'id');
        
        $orders_result = [];
        $notificationModel = model('Hospital/NotificationModel');
        $new_result_notifications = [];
        if (!empty($approved_order_ids)) {
            $orders_result = $biopsyResultModel->whereIn('order_id', $approved_order_ids)->findAll();
            $notifications = $notificationModel->whereIn('link_id', $approved_order_ids)
                ->where('notification_type', 'Pathology')
                ->findAll();
            foreach ($notifications as $notification) {
                if (intval($notification['status']) === 0) {
                    $new_result_notifications[$notification['link_id']] = true;
                }
            }
        }
        
        foreach($orders_result as $index => $result){
            $order = $approved_orders[array_search($result['order_id'], array_column($approved_orders, 'id'))];
            $orders_result[$index]['id'] = $result['order_id'];
            $orders_result[$index]['result_id'] = $result['id'];
            $orders_result[$index]['updated_at'] = $result['updated_at'];
            $orders_result[$index]['has_new_notification'] = isset($new_result_notifications[$result['order_id']]);
            // patient_name, mobile already in result
        }
        
        $orders_delivered = [];
        if ($filter_status == '7') {
            $orders_delivered_query = $orderMainModel->where('order_service_type','Pathology')->where('order_status',7);
            
            if (!empty($filter_name)) {
                $orders_delivered_query->like('patient_name', $filter_name);
            }
            if (!empty($filter_mobile)) {
                $orders_delivered_query->like('mobile', $filter_mobile);
            }
            
            $orders_delivered = $orders_delivered_query->findAll();
            
            foreach($orders_delivered as $index => $rt){
                $orders_delivered[$index]['patient_detail'] = $patientModel->where('id',$rt['patient_id'])->findAll()[0]??[];
                $orders_delivered[$index]['doctor'] = $userModel->where('id',$rt['ordered_by'])->findColumn('name')[0]??"Reception";
                $orders_delivered[$index]['result_id'] = $biopsyResultModel->where('order_id',$rt['id'])->findColumn('id')[0]??'';
            }
        }

        
        
        $data['active_menu'] = 'biopsy_followup';
        $data['orders'] = $orders;
        $data['orders_result'] = $orders_result;
        $data['orders_delivered'] = $orders_delivered;
        
        return 
        view('Include/_header')
        .view('Include/_left_sidebar_top', $data)

        .view('Hospital/Reception/_left_sidebar_reception')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Reception/pending_biopsy_orders.php')

        .view('Include/_footer');
    }

    private function get_biopsy_from_api($api, array $filter){
        $url = $api['url'] . 'get_biopsy_orders';
        $response = BluelimeSystem::communicate_api2(__METHOD__ ,$url,$api['api_key'], $filter);
        //BluelimeSystem::show_and_die($response);
        $response = json_decode($response,true);
        
        if(($response['status']??'') != 'success')
            return [];

        $data = $response['data'] ?? [];
        $signature = $response['signature'] ?? '';

        if(!BluelimeSystem::check_jwt_signature($response['data'],$api['api_key'],$signature))
            return [];

        return $data;
    }
    private function get_order_detail($api, array $filter){
        $url = $api['url'] . 'get_order_detail';
        $response = BluelimeSystem::communicate_api2(__METHOD__ ,$url,$api['api_key'], $filter);
        BluelimeSystem::show_and_die($response);
        $response = json_decode($response,true);
        
        if(($response['status']??'') != 'success')
            return [];

        $data = $response['data'] ?? [];
        $signature = $response['signature'] ?? '';

        if(!BluelimeSystem::check_jwt_signature($response['data'],$api['api_key'],$signature))
            return [];

        return $data;
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
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        
       
       
        
        $biopsy_results = $biopsyResultModel->where('order_id',$order_main_id)->findAll();
         //BluelimeSystem::show_and_die($biopsy_results);
        $orders = $orderMainModel->where('id',$order_main_id)->findAll();
        
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
       
       
        if($orders[0]['patient_id']>0 AND $orders[0]['visit_id']>0){
            $biopsy_request = $externalDocumentsModel->where('patient_id', $orders[0]['patient_id'])->where('visit_id', $orders[0]['visit_id'])->where('request_type', 'BIOPSY_REQUEST')->orderBy('id desc')->first();
            if(!empty($biopsy_request)){
            $biopsy_request['patient_detail'] = $patientModel->where('id',$rt['patient_id'])->first();
            $biopsy_request['specimen'] = "Skin biopsy";
            $biopsy_request['refering_institution'] = "Rank ".$_ENV['BRANCH'];
            $doctor = isset($biopsy_request['doctor_id']) && !empty($biopsy_request['doctor_id']) ? $userModel->find($biopsy_request['doctor_id']) : null;
            
        }}    //BluelimeSystem::show_and_die(BluelimeSystem::convert_to_array($biopsy_request['description']));
        
//BluelimeSystem::show_and_die($biopsy_request);
        
        $data['biopsy_results'] = $biopsy_results[0]??[];
        
        $data['active_menu'] = 'pending_order';
        $data['biopsy_request'] = $biopsy_request??[];
        $data['loggeduserdata'] = $loggeduserdata;
        $data['order_detail'] = $order_detail;
        $data['doctor'] = $doctor??[];
        // Ensure additional_info is always an array for the view
        if (isset($orders[0]['additional_info']) && is_string($orders[0]['additional_info']) && !empty($orders[0]['additional_info'])) {
            $decoded = json_decode($orders[0]['additional_info'], true);
            if (is_array($decoded)) {
                $orders[0]['additional_info'] = $decoded;
            }
        }
        $data['order_main'] = $orders[0];
        
        return 
        view('Include/_header')
        .view('Include/_left_sidebar_top', $data)

        .view('Hospital/Reception/_left_sidebar_reception')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Reception/biopsy_order_detail')

        .view('Include/_footer');
    }
    public function order_detail_view_only()
    {
        $order_main_id = $_GET['order_main_id'];
        $biopsyResultModel = model('Hospital/BiopsyResultModel');
        $orderDetailModel = model('Hospital/OrderDetailModel');
        $serviceModel = model('Hospital/ServiceModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        
       
       
        
        $biopsy_results = $biopsyResultModel->where('order_id',$order_main_id)->findAll();
         //BluelimeSystem::show_and_die($biopsy_results);
        $orders = $orderMainModel->where('id',$order_main_id)->findAll();
        
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
       
       
        if($orders[0]['patient_id']>0 AND $orders[0]['visit_id']>0){
            $biopsy_request = $externalDocumentsModel->where('patient_id', $orders[0]['patient_id'])->where('visit_id', $orders[0]['visit_id'])->where('request_type', 'BIOPSY_REQUEST')->orderBy('id desc')->first();
            if(!empty($biopsy_request)){
            $biopsy_request['patient_detail'] = $patientModel->where('id',$rt['patient_id'])->first();
            $biopsy_request['specimen'] = "Skin biopsy";
            $biopsy_request['refering_institution'] = "Rank ".$_ENV['BRANCH'];
            $doctor = isset($biopsy_request['doctor_id']) && !empty($biopsy_request['doctor_id']) ? $userModel->find($biopsy_request['doctor_id']) : null;
            
        }}    //BluelimeSystem::show_and_die(BluelimeSystem::convert_to_array($biopsy_request['description']));
        
//BluelimeSystem::show_and_die($biopsy_request);
        
        $data['biopsy_results'] = $biopsy_results[0]??[];
        
        $data['active_menu'] = 'pending_order';
        $data['biopsy_request'] = $biopsy_request??[];
        $data['loggeduserdata'] = $loggeduserdata;
        $data['order_detail'] = $order_detail;
        $data['doctor'] = $doctor??[];
        // Ensure additional_info is always an array for the view
        if (isset($orders[0]['additional_info']) && is_string($orders[0]['additional_info']) && !empty($orders[0]['additional_info'])) {
            $decoded = json_decode($orders[0]['additional_info'], true);
            if (is_array($decoded)) {
                $orders[0]['additional_info'] = $decoded;
            }
        }
        $data['order_main'] = $orders[0];
        
        return 
        view('Include/_header')
        .view('Include/_left_sidebar_top', $data)

        .view('Hospital/Reception/_left_sidebar_reception')

        .view('Include/_left_sidebar_bottom')

        .view('Hospital/Reception/biopsy_order_detail_view')

        .view('Include/_footer');
    }
    public function change_sample_status(){
        $order_id = $_GET['order_id'] ?? -1;
        $brid = $_GET['brid'] ?? '';
        $note = $_GET['note'] ?? '';
        $orderMainModel = model('Hospital/OrderMainModel');

        $order = $orderMainModel->find($order_id);
       // BluelimeSystem::show_and_die($order['order_status']);
        $new_status = intval($order['order_status']) + 1;
        $orderMainModel->update($order_id, ['order_status' => $new_status,'order_comment'=>$note]);
        return redirect()->to('/hospital/reception/viewbiopsyresult?order_id='.$order_id.'&brid='.$brid.'&status='.$new_status);
    }
    public function collectsample(){
        
        $order_id = $_POST['order_id'];
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $user_id = $loggeduserdata['id'];
        //BluelimeSystem::show_and_die($user_id);
        $orderMainModel = model('Hospital/OrderMainModel');
        $orderMainModel->update($order_id, ['order_status' => 2,'sample_collected_date'=>BluelimeSystem::date_now(),'sample_collected_by'=>$user_id]);
        $result = '{"status":"success","result":"saved"}';

        return $result;
    }
    public function sendsample(){
        
        $order_id = $_POST['order_id'];
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $user_id = $loggeduserdata['id'];
        //BluelimeSystem::show_and_die($user_id);
        $orderMainModel = model('Hospital/OrderMainModel');
        $orderMainModel->update($order_id, ['order_status' => 3,'sample_sent_date'=>BluelimeSystem::date_now(),'sample_sent_by'=>$user_id]);
        $result = '{"status":"success","result":"saved"}';

        return $result;
    }
    public function recieveslide(){
        
        $order_id = $_POST['order_id'];
        $note = $_POST['note'];
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $user_id = $loggeduserdata['id'];
        //BluelimeSystem::show_and_die($user_id);
        $orderMainModel = model('Hospital/OrderMainModel');
        $orderMainModel->update($order_id, ['order_status' => 4,'slide_recieved_date'=>BluelimeSystem::date_now(),'slide_recieved_by'=>$user_id,'order_comment'=>$note]);
        $result = '{"status":"success","result":"saved"}';

        return $result;
    } 

    public function save_patient_info(){
        $order_id = $_POST['order_id'];
        $first_name = $_POST['first_name'];
        $middle_name = $_POST['middle_name'];
        $last_name = $_POST['last_name'];
        $age = $_POST['age'];
        $date_of_birth = $_POST['date_of_birth'];
        $sex = $_POST['sex'];
        $mobile = $_POST['mobile'];
        $additional_info = $_POST['additional_info'];

        $patient_name = trim($first_name . ' ' . $middle_name . ' ' . $last_name);

        $orderMainModel = model('Hospital/OrderMainModel');
        $update_data = [
            'patient_name' => $patient_name,
            'date_of_birth' => $date_of_birth,
            'sex' => $sex,
            'mobile' => $mobile,
            'additional_info' => $additional_info
        ];

        $orderMainModel->update($order_id, $update_data);
        $result = '{"status":"success","message":"Patient information saved"}';

        return $result;
    }

    public function reverse_sample_status(){
        $order_id = $_GET['order_id'] ?? -1;
        $orderMainModel = model('Hospital/OrderMainModel');

        $order = $orderMainModel->find($order_id);
        $orderMainModel->update($order_id, ['order_status' => (intval($order['order_status'])-1)]);
        return redirect()->to('/hospital/reception/order_detail?order_main_id='.$order_id);
    }
    public function viewbiopsyresult()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $biopsyResultModel = model('Hospital/BiopsyResultModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $brid = $_GET['brid'];
        $order_id = $_GET['order_id'];

        $userModel = model('System/UserModel');
        $notificationModel = model('Hospital/NotificationModel');
        $userModel = model('System/UserModel');

        

        $biopsy_result = $biopsyResultModel->where('id',$brid)->orderBy('id','DESC')-> findAll();
        $order = $orderMainModel->where('id',$biopsy_result[0]['order_id'])->findAll();
        //BluelimeSystem::show_and_die(BluelimeSystem::convert_to_array($order[0]['additional_info'])['email']);  
        //$doc_sign = $userModel->where('id',$biopsy_result[0]['doctor_id'])->findColumn('digital_signature'); 
        
        $notificationModel->where('link_id',$order_id)->where('notification_type','Pathology')->set('status',BluelimeConfiguration::NOTIFICATION_SEEN)->update();   
        $doctor = $userModel->find($order[0]['result_approved_by']);
        //BluelimeSystem::show_and_die($doctor);
        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        //$data['patient'] = $patient;
        $data['doctor'] = $doctor;
        $data['biopsy_result'] = $biopsy_result[0];
        $data['order'] = $order[0];
        $data['current_status'] = $order[0]['order_status'];
        
        //$data['doc_sign'] = $doc_sign[0];
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Reception/_left_sidebar_reception')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Reception/view_biopsy_result')

            .view('Include/_footer');
    }
    public function save_comment()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $cancel_reason = $_POST['cancel_reason'] ?? '';
        $custom_reason = $_POST['custom_reason'] ?? '';
        $app_id = $_POST['app_id'] ?? '';

        $appointmentModel->update($app_id, ['cancelled_by' => $loggeduserdata['name'], 'cancelation_reason' => $cancel_reason, 'custom_note' => $custom_reason]);
        $result = '{"status":"success","result":"saved"}';

        return $result;
    }
    public function cancelappointment()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $cancel_reason = $_POST['cancel_reason'] ?? '';
        $custom_reason = $_POST['custom_reason'] ?? '';

        $app_id = $_POST['app_id'] ?? '';
        $data = [
                    'appointment_status'=> \BluelimeConfiguration::APPOINTMENT_CANCELED,
                    'cancelled_by'=> $loggeduserdata['name'],
                    'cancelation_reason'=> $cancel_reason,
                    'custom_note'=> $custom_reason,
                ];
        $app = $appointmentModel->where('id',$app_id)->findAll()[0];
        $appointmentModel->update($app_id,$data);

        //$website = "\nBook your Appointment @ www.rankderma.com";
        $branch = $_ENV['BRANCH']??"";
        $appointment_message = "Your Appointment on ".date('l d-m-Y', strtotime($app['appointment_date']))." " .$app['time']." is Canceled\n\nRANK Specialized Dermatology Clinic";
        if(BluelimeSystem::check_sms_service()){
            BluelimeSystem::send_sms_message($app['mobile'], $appointment_message.$branch);
        }
            BluelimeSystem::send_telegram_message($app['mobile'], $appointment_message.$branch);
        $result = '{"status":"success","result":"saved"}';

        return $result;
    }

    public function credit_receipts(){
        $date_from = $_GET['date_from'] ?? date('Y-m-d');
        $date_to = $_GET['date_to'] ?? date('Y-m-d');
        // $show_all = $_GET['show_all'] ?? false;

        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $billModel = $billModel->select('*, "0" as total_amount')->where('bill_type', 'Credit')->where('bill_status',4)->where('payed_date >=',"$date_from 00:00:00")->where('payed_date <=',"$date_to 23:59:59");
        // if($show_all === false)
        //     $billModel = $billModel->groupStart()->where('payment_request_id',null)->orWhere('payment_request_id',0)->groupEnd();
        $bills = $billModel->orderBy('fs_number')->findAll();
        $bill_details = $billDetailModel->whereIn('bill_id',[-99,...array_column($bills,'id')])->where('service_status !=', -1)->findAll();
        $bills = BluelimeSystem::index_object_list_array_by_unique_field($bills,'id');
        foreach($bill_details as $bill_detail){
            $bills['id_'.$bill_detail['bill_id']]['total_amount'] += ($bill_detail['bill_amount'] * $bill_detail['bill_quantity']);
        }

        $view_data = [
            'active_menu' => 'credit_receipts',
            'bills' => $bills,
            'date_from' => $date_from,
            'date_to' => $date_to,
        ];

        return view('Hospital/Reception/credit_receipts',$view_data);
    }
    public function send_email(){
        $email = $_POST['email'] ?? '';
        $subject = $_POST['subject'] ?? 'Histopathology Result';
        $message = $_POST['message'] ?? 'Please find the attached Histopathology Result.';
        $image_data = $_POST['image_data'] ?? '';

        if(empty($email)){
            return $this->response->setJSON(['status'=>'error','message'=>'Recipient email is required']);
        }

        $attachments = [];

        if(!empty($image_data)){
            if(strpos($image_data, 'data:image/') === 0 && strpos($image_data, 'base64,') !== false){
                $image_data = explode('base64,', $image_data, 2)[1];
            }

            $decoded = base64_decode($image_data);
            if($decoded === false){
                return $this->response->setJSON(['status'=>'error','message'=>'Invalid image data']);
            }

            $uploadsDir = WRITEPATH . 'uploads';
            if(!is_dir($uploadsDir)){
                mkdir($uploadsDir, 0777, true);
            }

            $filename = $uploadsDir . '/histopathology_report_' . time() . '.png';
            file_put_contents($filename, $decoded);
            $attachments[] = $filename;
        }

        try {
            $body = '<p>' . htmlspecialchars($message) . '</p>';
            if(!empty($image_data)){
                $body .= '<p>Document is attached.</p>';
            }

           $emailResult = BluelimeSystem::send_simple_email($email, $subject, $body, null, $attachments);

            // cleanup temporary file(s)
            foreach($attachments as $attachPath){
                if(file_exists($attachPath)){
                    unlink($attachPath);
                }
            }

            if ($emailResult !== true) {
                return $this->response->setJSON(['status' => 'error', 'message' => $emailResult]);
            }

            return $this->response->setJSON(['status'=>'success','value' => $email]);
        } catch(\Exception $e) {
            return $this->response->setJSON(['status'=>'error','message' => $e->getMessage()]);
        }
    }
}
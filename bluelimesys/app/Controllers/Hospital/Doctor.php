<?php

namespace App\Controllers\Hospital;

use BluelimeAuthentication;
use BluelimeConfiguration;
use BluelimeSystem;

class Doctor extends \App\Controllers\BaseController
{
    public function index()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $visitModel = model('Hospital/VisitModel');
        $patientModel = model('Hospital/PatientModel');
        $notificationModel = model('Hospital/NotificationModel');
        
        $search_key = $_GET['search_key'] ?? '';

        if(trim($search_key) != '')
            $visitModel = $visitModel->where('id LIKE', "%$search_key%")->orWhere('patient_name LIKE',"$search_key%");

        $doctor_id = @$loggeduserdata['id'];
        if($loggeduserdata['role']==='SUPER_ADMIN'){
            $waithing_visits = $visitModel->where('visit_status', \BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN)->orderBy('queue_no desc')->findAll();
            //BluelimeSystem::show_and_die($waithing_visits);
        }
        else
        $waithing_visits = $visitModel->where('doctor_id', $doctor_id)->where('visit_status', \BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN)->orderBy('queue_no desc')->findAll();
        
        /**
         * Extra data of patient is collected here. We tried to avoid the INNER JOIN
         */
        if($waithing_visits){
         
            $patient_id_list = [];

            foreach($waithing_visits as $visit){
                array_push($patient_id_list, @$visit['patient_id']);
            }

            $patients = $patientModel->whereIn('id', $patient_id_list)->findAll();

            $waithing_visits_new = [];
            foreach($waithing_visits as $index => $visit){
                foreach($patients as $patient){
                    if($visit['patient_id'] === $patient['id']){
                        $waithing_visits[$index]['_patient'] = $patient;
                        $waithing_visits_new[$index] = $waithing_visits[$index];
                        break;
                    }
                }
            }

            $waithing_visits = $waithing_visits_new;
            $notification_visit_id_list = [-1];

            foreach($waithing_visits as $visit){
                array_push($notification_visit_id_list, @$visit['id']);
            }
            
            $new_waiting = $notificationModel->where('link_type','visit_id')->where('notification_type','Doctors Waiting')->whereIn('link_id', $notification_visit_id_list)->findAll();
        
            foreach($waithing_visits as $index => $visit){
                foreach($new_waiting as $waiting){
                    if($visit['id'] === $waiting['link_id']){
                        $waithing_visits[$index]['_notify'] = $waiting;
                        break;
                    }
                }
            }
        }
        
        
        //BluelimeSystem::show_and_die($waithing_visits);

        $alert = BluelimeSystem::get_alert();

        $data['search_key'] = $search_key;
        $data['waithing_visits'] = $waithing_visits;
        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['alert'] = $alert;


        return view('Include/_header', $data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/Doctor/_left_sidebar_doctor')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Doctor/waiting_patients')
        .view('Include/_footer');
    }
    public function manual()
    {
        
        $data['active_menu'] = 'manual';
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Doctor/_left_sidebar_doctor')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Doctor/doctor_user_manual')

            .view('Include/_footer');
    }
    public function mypatients()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $visitModel = model('Hospital/VisitModel');
        $patientModel = model('Hospital/PatientModel');
        $searchkey= $_POST['search_my_patients'] ?? "";

        $doctor_id = @$loggeduserdata['id'];
        $closed_visits = $visitModel->search_closed_visit($searchkey,$doctor_id);
                                   //BluelimeSystem::show_and_die($closed_visits);
        
        if($closed_visits){
         
            $patient_id_list = [];

        foreach($closed_visits as $visit){
            array_push($patient_id_list, @$visit['patient_id']);
        }

        $patients = $patientModel->whereIn('id', $patient_id_list)->findAll();
       
            foreach($closed_visits as $index => $visit){
                foreach($patients as $patient){
                    if($visit['patient_id'] === $patient['id']){
                        $closed_visits[$index]['_patient'] = $patient;
                        break;
                    }
                }
            }
           
        }
        
        $alert = BluelimeSystem::get_alert();


        $data['closed_visits'] = $closed_visits;
        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'mypatients';
        $data['alert'] = $alert;


        return view('Include/_header', $data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/Doctor/_left_sidebar_doctor')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Doctor/my_patients')
        .view('Include/_footer');
    }
    public function doctordashboard()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $appointmentModel = model('Hospital/AppointmentModel'); 
        $approvedDoctorsReportModel = model('Hospital/ApprovedDoctorsReportModel');
        $visitModel = model('Hospital/VisitModel'); 
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $userModel = model('System/UserModel');

        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();

        $doctor_id = $loggeduserdata['id'];
        $date_filter = $_GET['date'] ?? date('Y-m-d');
       
        $selected_doctor = $userModel->find($doctor_id);

        $bills = $billModel->where('registered_by', $doctor_id)->where('payed_date >=', "$date_filter 00:00:01")->where('payed_date <=', "$date_filter 23:59:59")->findAll();

        $bill_id_list = [...BluelimeSystem::collect_field_value_from_object($bills,'id'), -1];

        $procedures = $billDetailModel->whereIn('bill_id', $bill_id_list)->whereIn('service_type',[BluelimeConfiguration::SERVICE_TYPE::PROCEDURE,BluelimeConfiguration::SERVICE_TYPE::PATHOLOGY])->where('service_status',0)->findAll();

        // $procedures = $billDetailModel->whereIn('service_type',[BluelimeConfiguration::SERVICE_TYPE::PROCEDURE,BluelimeConfiguration::SERVICE_TYPE::PATHOLOGY])->where('service_status',0)->where('payed_date >=', "$date_filter 00:00:01")->where('payed_date <=', "$date_filter 23:59:59")->findAll();
        // $procedures_bill_id_list = [...BluelimeSystem::collect_field_value_from_object($procedures,'bill_id'), -1];
        
        // $procedure_bill_list = $billModel->whereIn('id', $procedures_bill_id_list)->where('registered_by', $doctor_id)->findAll();

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
       
        //$visits_bill = $billModel->whereIn('id', $bill_id_list)->findAll();

        $visits_bill = $billModel->whereIn('id', $bill_id_list)->where('payed_date >=', "$date_filter 00:00:01")->where('payed_date <=', "$date_filter 23:59:59")->findAll();

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

//==================================================================================================
        $today_visit = $visitModel->where('doctor_id',$doctor_id)->where('visit_date >=',date('Y-m-d'))->findColumn('count(id)')[0];
        $no_of_appointments = $appointmentModel->where('doctor_id',$doctor_id)->findColumn('count(id)')[0];
        $no_of_appointments_visit = $appointmentModel->groupStart()->where('appointment_status',3)->orWhere('appointment_status',4)->groupEnd()->where('doctor_id',$doctor_id)->findColumn('count(id)')[0];
        $no_of_visits = $visitModel->where('doctor_id',$doctor_id)->findColumn('count(id)')[0];

        $today = $appointmentModel->select('MAX(appointment_status) as `appointment_status`,  count(appointment_status) as total')->where('appointment_date',date('Y-m-d'))->where('appointment_status <>',1)->where('doctor_id',$doctor_id)->groupBy('appointment_status')->orderBy('appointment_status', 'desc')->findAll(); 
        $yesterday = $appointmentModel->select('MAX(appointment_status) as `appointment_status`,  count(appointment_status) as total')->where('appointment_date',date("Y-m-d", strtotime("-1 days")))->where('appointment_status <>',1)->where('doctor_id',$doctor_id)->groupBy('appointment_status')->orderBy('appointment_status', 'desc')->findAll(); 
        $this_month = $appointmentModel->select('MAX(appointment_status) as `appointment_status`,  count(appointment_status) as total')->where('appointment_date <=',date('Y-m-d'))->where('MONTH(appointment_date)',date('m'))->where('doctor_id',$doctor_id)->where('YEAR(appointment_date)',date('Y'))->where('appointment_status <>',1)->groupBy('appointment_status')->orderBy('appointment_status', 'desc')->findAll(); 
        $last_month = $appointmentModel->select('MAX(appointment_status) as `appointment_status`,  count(appointment_status) as total')->where('appointment_date <=',date('Y-m-d'))->where('MONTH(appointment_date)',date('m')-1)->where('doctor_id',$doctor_id)->where('YEAR(appointment_date)',date('Y'))->where('appointment_status <>',1)->groupBy('appointment_status')->orderBy('appointment_status', 'desc')->findAll(); 
        $this_year = $appointmentModel->select('MAX(appointment_status) as `appointment_status`,  count(appointment_status) as total')->where('appointment_date <=',date('Y-m-d'))->where('YEAR(appointment_date)',date('Y'))->where('appointment_status <>',1)->where('doctor_id',$doctor_id)->groupBy('appointment_status')->orderBy('appointment_status', 'desc')->findAll(); 
        $all_time = $appointmentModel->select('MAX(appointment_status) as `appointment_status`,  count(appointment_status) as total')->where('appointment_date <=',date('Y-m-d'))->where('appointment_status <>',1)->where('doctor_id',$doctor_id)->groupBy('appointment_status')->orderBy('appointment_status', 'desc')->findAll(); 
        
        $appointments = [];
        if(!empty($start_date))
            $appointments = $appointmentModel->select('MAX(appointment_status) as `appointment_status`,  count(appointment_status) as total')->where('appointment_status <>',1)->where('appointment_date >= ',$date_filter)->where('appointment_date <= ',$date_filter)->where('doctor_id',$doctor_id)->groupBy('appointment_status')->orderBy('appointment_status', 'desc')->findAll(); 

        $approved_report = $approvedDoctorsReportModel->where('doctor_id',$loggeduserdata['id'])->where('report_date',$date_filter)->findAll();
        
        $data['doctor_visits'] = $doctor_visits;
        $data['procedures'] = $doctors_procedure;

        // $data['doctor_visits'] = @$doctor_visits;
        // $data['procedures'] = @$procedures;
        $data['approved_report'] = $approved_report;
        $data['appointments'] = $appointments;
        
        $data['today'] = $today;
        $data['yesterday'] = $yesterday;
        $data['this_month'] = $this_month;
        $data['last_month'] = $last_month;
        $data['all_time'] = $all_time;
        $data['this_year'] = $this_year;
        $data['today_visit'] = $today_visit;
        $data['no_of_visits'] = $no_of_visits;
        $data['no_of_appointments'] = $no_of_appointments;
        $data['no_of_appointments_visit'] = $no_of_appointments_visit;
        $data['date_filter'] = $date_filter;
        
        // $data['start_date'] = $start_date;
        // $data['end_date'] = $end_date;
        $data['active_menu'] = 'doctordashboard';
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Doctor/_left_sidebar_doctor')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Doctor/doctor_dashboard')

            .view('Include/_footer');
    }
    public function approvedoctorreport()
    {
        $approvedDoctorsReportModel = model('Hospital/ApprovedDoctorsReportModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $notificationModel = model('Hospital/NotificationModel');
        $visitModel = model('Hospital/VisitModel');
        //BluelimeSystem::show_and_die($_POST);
        $doctor_id = $_POST['doctor_id'];
        $doctor_name = $_POST['doctor_name'];
        $report_date = $_POST['report_date'];
        $report_data = $_POST['report_data'];

        $data = [
            'doctor_id'=>$doctor_id,
            'doctor_name'=>$doctor_name,
            'report_date'=>$report_date,
            'report_data' => $report_data
        ];
        $notfiy_data = [
            'notification_type'=>  "Daily Doctor Report Approved",
            'description'=> $doctor_name,
            'link_type'=> 'doctor_id',
            'link_id'=> $doctor_id,
         ];

        $approvedDoctorsReportModel->insert($data);
        $notificationModel->insert($notfiy_data,true);
        $visitModel->where('doctor_id',$loggeduserdata['id'])->findAll();
        $close_data = [
            'visit_status' => BluelimeConfiguration::VISIT_STATUS_CLOSED,
            'visit_closed_date' => date('Y-m-d H:i:s')
        ];

        if($visitModel->where('visit_date like',date('Y-m-d%'))->where('doctor_id',$doctor_id)->set($close_data)->update());
            return '{"status":"success"}';
        
    }
    public function send_approved_daily_report()
    {
        
        //BluelimeSystem::show_and_die($_POST);
        BluelimeSystem::send_telegram_message($_POST['phone_number'], $_POST['message'], $_POST['image']);
        return BluelimeSystem::get_success_ajax_return("Message sent successfully.");
        
    }
    

    public function visit()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $visitModel = model('Hospital/VisitModel');
        $patientModel = model('Hospital/PatientModel');
        $chiefComplaintModel = model('Hospital/ChiefComplaintModel');
        $medicalHistoryModel = model('Hospital/MedicalHistoryModel');
        $dentalMedicalHistoryModel = model('Hospital/DentalMedicalHistoryModel');
        $diseaseModel = model('Hospital/DiseaseModel');
        $examinationExtraOralModel = model('Hospital/ExaminationExtraOralModel');
        $examinationIntraOralModel = model('Hospital/ExaminationIntraOralModel');
        $diagnosisModel = model('Hospital/DiagnosisModel');
        $procedureModel = model('Hospital/ProcedureModel');
        $drugModel = model('Hospital/DrugModel');
        $userModel = model('System/UserModel');
        $serviceModel = model('Hospital/ServiceModel');
        $pricelistModel = model('Hospital/PricelistModel');
        $pricelistDetailModel = model('Hospital/PricelistDetailModel');
        $followUpModel = model('Hospital/FollowUpModel');
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        $vitalSignModel = model('Hospital/VitalSignModel');
        $appointmentModel = model('Hospital/AppointmentModel');
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        //$prescriptionDetailModel = model('Hospital/PrescriptionDetailModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        //$orderDetailModel = model('Hospital/OrderDetailModel');
        $laboratoryResults = model('Hospital/LaboratoryResultsModel');
        $laboratoryResultAttributeModel = model('Hospital/LaboratoryResultAttributeModel');
        //$laboratoryResultAttribute = model('Hospital/LaboratoryResultAttribute');
        $itemModel = model('Inventory/ItemModel');
        $autoCompleteModel = model('Hospital/AutoCompleteModel');
        
        $notificationModel = model('Hospital/NotificationModel');
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $biopsyResultModel = model('Hospital/BiopsyResultModel');
        
        
        $notificationModel->where('link_id',$_GET['visit_id'])->where('link_type','visit_id')->where('notification_type','Doctors Waiting')->set('status',\BluelimeConfiguration::NOTIFICATION_SEEN)->update();

        $visit_id = $_GET['visit_id'] ?? -1;
        $doctor_id = $loggeduserdata['id'];

        $visit = $visitModel->find($visit_id);

        if(empty($visit)){
            $alert_body_html = "The selected visit is not an open visit.";
            BluelimeSystem::set_alert($alert_body_html);
            return redirect()->to(base_url().'hospital/doctor');
        }

        $pending_bills = $billModel->where('visit_id', $visit_id)->where('bill_status !=', BluelimeConfiguration::BILL_RECIEVED)->findAll();
        $pending_bill_id_list = [...BluelimeSystem::collect_field_value_from_object($pending_bills, 'id'),-1];
        $pending_bill_details = $billDetailModel->whereIn('bill_id', $pending_bill_id_list)->findAll();

        $pending_bills = BluelimeSystem::index_object_list_array_by_unique_field($pending_bills,'id');

        $orderOption = [];
        $orderOption_Sent_to_pos = [];
        foreach($pending_bill_details as $index => $service){
            $service_temp = [
                    "service_id"=> $service['service_id'],
                    "service_name"=> $service['service_name'],
                    "quantity"=> $service['bill_quantity'],
                    "price"=> $service['bill_amount'],
                    "bill_status"=>$pending_bills['id_'.$service['bill_id']]['bill_status'],
                ];
            if(intval($service['service_id']) < 0){
                $service_temp["quantity"] = -1 * floatval($service['bill_amount']);
                $service_temp["price"] = 1;
            }

            if($service_temp['bill_status'] == 0)
                array_push($orderOption, $service_temp);
            else
                array_push($orderOption_Sent_to_pos, $service_temp);       
        }

        foreach($pending_bill_details as $service){
            if(($service['bill_discount'] > 0) && ($pending_bills['id_'.$service['bill_id']]['bill_status'] == 0)){
                $service_temp = [
                    [
                        "service_id"=> BluelimeConfiguration::DISCOUNT_PRICE_PERCENT_ID,
                        "service_name"=> 'Discount In Percent',
                        "quantity"=> $service['bill_discount'],
                        "price"=> 1,
                        "bill_status"=> 0
                    ]
                ];
                array_push($orderOption, ...$service_temp);
                break;
            }
        }

        foreach($pending_bill_details as $service){
            if(($service['bill_discount'] > 0) && ($pending_bills['id_'.$service['bill_id']]['bill_status'] != 0)){
                $service_temp = [
                    [
                        "service_id"=> BluelimeConfiguration::DISCOUNT_PRICE_PERCENT_ID,
                        "service_name"=> 'Discount In Percent',
                        "quantity"=> $service['bill_discount'],
                        "price"=> 1,
                        "bill_status"=> $pending_bills['id_'.$service['bill_id']]['bill_status']
                    ]
                ];
                array_push($orderOption_Sent_to_pos, ...$service_temp);
                break;
            }
        }

        $pending_order_comments = $orderMainModel->whereIn('bill_id', $pending_bill_id_list)->findColumn('order_comment')??[]; 

        $order_comments = implode("\n",$pending_order_comments);

        $patient_id = $visit['patient_id'];

        $doctors = $userModel->where("id !=", $doctor_id)->where('role','DOCTOR')->where('active',1)->findAll();

        $patient = $patientModel->find($patient_id);

        $chief_complaint_list = $chiefComplaintModel->findColumn('name');
        $medical_history_list = $medicalHistoryModel->findColumn('name');
        $dental_medical_history_list = $dentalMedicalHistoryModel->findColumn('name');
        $disease_list = $diseaseModel->findColumn('name');
        $examination_extra_oral_list = $examinationExtraOralModel->findColumn('name');
        $examination_intra_oral_list = $examinationIntraOralModel->findColumn('name');
        $diagnosis_list = $diagnosisModel->findColumn('name');
        $procedure_list = $procedureModel->findColumn('name');
        $drug_list = $drugModel->findColumn('name');
        
        $service_list_lab = $serviceModel->select('id,service_name')->where('is_active',1)->where('service_type','Laboratory')->findAll();

        $pricelist = $pricelistModel->where('company_id', $patient['company_id'])->find();
        $pricelist_id = $pricelist[0]['id'] ?? BluelimeConfiguration::DEFAULT_PRICE_LIST_ID;
        //$service_list = $pricelistDetailModel->select('service_id as id,service_name')->where('status',1)->where('pricelist_id',$pricelist_id)->orderBy('service_name', 'ASC')->findAll();
        $disabled_services_id_list = [...$serviceModel->where('is_active',0)->orWhere('deleted_at IS NOT', NULL)->withDeleted()->findColumn('id')??[],-99];

        $service_list_company = $pricelistDetailModel->select('service_id as id,service_name,service_type,service_price')->whereNotIn('service_id',$disabled_services_id_list)->where('pricelist_id', $pricelist_id)->where('status',1)->orderBy('service_name', 'ASC')->findAll();

        $service_id_list = [...BluelimeSystem::collect_field_value_from_object($service_list_company, 'id'), ...$disabled_services_id_list];

        $service_list = $pricelistDetailModel->select('service_id as id,service_name,service_type,service_price')->whereNotIn('service_id', $service_id_list)->where('pricelist_id', BluelimeConfiguration::DEFAULT_PRICE_LIST_ID)->where('status',1)->orderBy('service_name', 'ASC')->findAll();

        array_push($service_list, ...$service_list_company);
        
        array_push($service_list, ...[['id'=>BluelimeConfiguration::DISCOUNT_PRICE_AMOUNT_ID, 'service_name' => 'Discount In Amount'],['id'=>BluelimeConfiguration::DISCOUNT_PRICE_PERCENT_ID, 'service_name' => 'Discount In Percent']]);

        $all_service = $serviceModel->findAll();
        $all_service = BluelimeSystem::index_object_list_array_by_unique_field($all_service,'id');

        foreach($service_list as $index => $service){
            if(isset($all_service['id_'.$service['id']])){
                $service_list[$index]['service_name'] = $all_service['id_'.$service['id']]['service_name'];
                $service_list[$index]['tag'] = trim("".$all_service['id_'.$service['id']]['tag']);
            }
            else
                $service_list[$index]['tag'] = "";

        }

        $visitEncounter = $visitEncounterModel->where('visit_id', $visit_id)->findAll();

        $visitEncounter = $visitEncounter[0] ?? [];

        $related_visits_encounter = [];
        $related_visits_paid_service = [];
        $collected_related_visits = BluelimeSystem::collect_follow_up_visits($visit['follow_up_id'], $visit_id, false);

        //$related_visits_paid_service = $this->get_visits_paid_services([$visit_id]);

        $related_visits_encounter = BluelimeSystem::get_visits_encounters($collected_related_visits);
        $related_visits_paid_service = $this->get_visits_paid_services([$visit_id, ...$collected_related_visits]);

        $related_visits_extra = [];
        if(!empty($collected_related_visits))
            $related_visits_extra = $visitModel->select('extra')->whereIn('id',$collected_related_visits)->findAll();


        $history_follow_up_list = BluelimeSystem::get_patient_encounter_history($patient_id);

        $visit_list_to_remove = $visitModel->where('patient_id', $patient_id)->whereIn('id',[$visit_id, ...$collected_related_visits])->findAll();

        foreach($visit_list_to_remove as $row){
            foreach($history_follow_up_list as $index => $row2){
                if($row['follow_up_id'] == $row2['id'])
                    unset($history_follow_up_list[$index]);
            }
        }


        $vitalSign = $vitalSignModel->where('patient_id', $patient_id)->orderBy('registered_date', 'DESC')->findAll(2);

        $appointments = $appointmentModel->where('patient_id', $patient_id)->findAll();
        
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

        $followUp = $followUpModel->find($visit['follow_up_id']);

        $visitEncounter['treatment_plans'] = $followUp['treatment_plans'] ?? [];
        $follow_up_remark = $followUp['remark'] ?? '';
        $follow_up_selected_skin = $followUp['selected_skin'] ?? NULL;

        if($follow_up_selected_skin === NULL){
            $last_selected_skin = $followUpModel
                                    ->groupStart()
                                        ->where("JSON_LENGTH( `selected_skin`, '$.face_only' ) >",0)
                                        ->orWhere("JSON_LENGTH( `selected_skin`, '$.full_body' ) >",0)
                                    ->groupEnd()
                                    ->where('patient_id', $patient_id)
                                    ->where('visit_id <', $visit['id'])
                                    ->orderBy('visit_id', 'DESC')
                                    ->findAll(1);
            $last_selected_skin = $last_selected_skin[0]['selected_skin'] ?? [];
            $follow_up_selected_skin = $last_selected_skin;
        }

        $visit_is_joined_another_follow_up = true;
        if($visit['id'] == ($followUp['visit_id'] ?? $visit['id']))
            $visit_is_joined_another_follow_up = false;

        $alert = BluelimeSystem::get_alert();
        $order_main = $orderMainModel->where('patient_id', $patient_id)->where('result_status', BluelimeConfiguration::LAB_RESULT_APPROVED)->findAll();
        $order_main = BluelimeSystem::index_object_list_array_by_unique_field($order_main,'id');
        $all_users = $userModel->select('id, name, role')->findAll();
        $all_users = BluelimeSystem::index_object_list_array_by_unique_field($all_users,'id');
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
                    $order_main['id_'.$previous_order_id]['result_approved_by_detail'] = $all_users['id_'.$order_main['id_'.$previous_order_id]['result_approved_by']];
                    array_push($laboratory_results_compiled, ['service_name' => $service_name, 'result' => $one_service_results, 'order_main' => $order_main['id_'.$previous_order_id]]);
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
            array_push($laboratory_results_compiled, ['service_name' => $service_name, 'result' => $one_service_results, 'order_main' => $order_main['id_'.$previous_order_id]]);
        }
        //BluelimeSystem::show_and_die($laboratory_results_compiled[0]);
        $procedureResultsModel = model('Hospital/ProcedureResultsModel');
        $patient_procedure_history = $procedureResultsModel->where('patient_id',$patient_id)->findAll();
        $distinct_procedures = $procedureResultsModel->distinct()->where('patient_id',$patient_id)->findColumn('procedure_name');
       
        foreach($patient_procedure_history as $index=>$user){
            $patient_procedure_history[$index]['registered_by_name'] = $userModel->where('id', $user['registered_by'])->findColumn('name')[0];
        }

        //biopsy result

        $biopsy_result = $biopsyResultModel->where('patient_id', $patient_id)->orderBy('id','DESC')->findAll();
        $order_ids = [...BluelimeSystem::collect_field_value_from_object($biopsy_result,'order_id'), -1];
        $order_biopsy = $orderMainModel->whereIn('id',$order_ids)->findAll();
        $order_biopsy = BluelimeSystem::index_object_list_array_by_unique_field($order_biopsy,'id');
        foreach($order_biopsy as $order_key => $order_row){
            $approved_by_id = $order_row['result_approved_by'] ?? 0;
            $order_biopsy[$order_key]['result_approved_by_name'] = $userModel->where('id',$approved_by_id)->findColumn('name')[0] ?? '';
            $order_biopsy[$order_key]['ordered_by_name'] = $userModel->where('id',$order_row['registered_by'])->findColumn('name')[0] ?? '';
        }
        $data['biopsy_result'] = $biopsy_result??[];
        $data['order_biopsy'] = $order_biopsy??[]; 


        $items = $itemModel->findAll();
        $auto_complete = $autoCompleteModel->findAll();
        $auto_complete_data = [];
        foreach($auto_complete as $row){
            $auto_complete_data[$row['name']] = $row['data'];
        }

        $last_visit_by_this_doctor = $visitModel->where('patient_id',$patient_id)->where('doctor_id',18)->where('id !=', $visit_id)->orderBy('id','DESC')->findAll(1);

        $last_visit_appointment_description = $last_visit_by_this_doctor[0]['extra']['next_appointment_description'] ?? '';

        $visit_bill = $billModel->find(($visit['bill_id']??-1));
        $visit['fs'] = $visit_bill['fs_number'] ?? '';

        $data['patient_procedure_history'] = $patient_procedure_history;
        $data['distinct_procedures'] = $distinct_procedures;
        $data['prescription_group'] = $prescription_group;
        $data['appointments'] = $appointments;
        $data['patient'] = $patient;
        $data['loggeduserdata'] = $loggeduserdata;
        $data['visit'] = $visit;
        $data['last_visit_appointment_description'] = $last_visit_appointment_description;
        $data['follow_up_remark'] = $follow_up_remark;
        $data['follow_up_selected_skin'] = $follow_up_selected_skin;
        $data['active_menu'] = 'wating_patients';
        $data['chief_complaint_list'] = $chief_complaint_list;
        $data['medical_history_list'] = $medical_history_list;
        $data['dental_medical_history_list'] = $dental_medical_history_list;
        $data['disease_list'] = $disease_list;
        $data['examination_extra_oral_list'] = $examination_extra_oral_list;
        $data['examination_intra_oral_list'] = $examination_intra_oral_list;
        $data['diagnosis_list'] = $diagnosis_list;
        $data['procedure_list'] = $procedure_list;
        $data['drug_list'] = $drug_list;
        $data['doctors'] = $doctors;
        $data['service_list'] = $service_list; // [id, service_name]
        $data['service_list_lab'] = $service_list_lab; 
        
        $data['visitEncounter'] = $visitEncounter;
        $data['orderOption'] = $orderOption;
        $data['orderOption_Sent_to_pos'] = $orderOption_Sent_to_pos;
        $data['order_comments'] = $order_comments;
        $data['related_visits_encounter'] = $related_visits_encounter;
        $data['related_visits_paid_service'] = $related_visits_paid_service;
        $data['related_visits_extra'] = $related_visits_extra;
        //$data['follow_up_list'] = $follow_up_list;
        $data['history_follow_up_list'] = $history_follow_up_list;
        $data['vitalSigns'] = $vitalSign;
        $data['active_menu'] = 'patients_waiting';
        $data['alert'] = $alert;
        $data['visit_is_joined_another_follow_up'] = $visit_is_joined_another_follow_up;
        $data['items'] = $items;
        $data['laboratory_results_compiled'] = $laboratory_results_compiled;
        $data['auto_complete_data'] = $auto_complete_data;

        return view('Include/_header', $data)
        
        .view('Include/_left_sidebar_top')
        .view('Hospital/Doctor/_left_sidebar_doctor')
        .view('Include/_left_sidebar_bottom')
        .(($_GET['new'] ?? 0) == 1 ? view('Hospital/Doctor/visit_ORG2') : view('Hospital/Doctor/visit'))
        .view('Include/_footer');
    }

    public function update_medical_history(){
        $patientModel = model('Hospital/PatientModel');
          
        $blood_pressure = $_POST['blood_pressure']??"No";
        $bleeding_tendency = $_POST['bleeding_tendency']??"No";
        $pregnancy      = $_POST['pregnancy']??"No";
        $cardiac_problem = $_POST['cardiac_problem']??"No";
        $diabetics      =  $_POST['diabetics']??"";
        $blood_group    = $_POST['blood_group'];
        $drugs_taking   = @$_POST['drugs_taking'];
        $allergy        = @$_POST['allergy'];
        $other_diseases = @$_POST['other_diseases'];

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
        ];
        
        $patientModel->update($_POST['patient_id'],$data);

        return redirect()->to(site_url('hospital/doctor/visit?visit_id='.$_POST['visit_id']));
    }


    public function patienthistory()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $visitModel = model('Hospital/VisitModel');
        $patientModel = model('Hospital/PatientModel');
        $userModel = model('System/UserModel');
        $vitalSignModel = model('Hospital/VitalSignModel');
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $pricelistModel = model('Hospital/PricelistModel');
        $pricelistDetailModel = model('Hospital/PricelistDetailModel');
        $laboratoryResults = model('Hospital/LaboratoryResultsModel');
        $laboratoryResultAttributeModel = model('Hospital/LaboratoryResultAttributeModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        
        $visit_id = $_GET['visit_id'] ?? -1;
        $doctor_id = $loggeduserdata['id'];
        $visit = $visitModel->find($visit_id);
        $patient_id = $visit['patient_id'];

        if(empty($visit)){
            $alert_body_html = "The selected visit is not an open visit.";
            BluelimeSystem::set_alert($alert_body_html);
            return redirect()->to(base_url().'hospital/doctor');
        }

        $doctors = $userModel->where("id !=", $doctor_id)->where('role','DOCTOR')->where('active',1)->findAll();

        $patient = $patientModel->find($patient_id);
        $pricelist = $pricelistModel->where('company_id', $patient['company_id'])->find();
        $pricelist_id = $pricelist[0]['id'] ?? BluelimeConfiguration::DEFAULT_PRICE_LIST_ID;
        //$service_list = $pricelistDetailModel->select('service_id as id,service_name')->where('status',1)->where('pricelist_id',$pricelist_id)->orderBy('service_name', 'ASC')->findAll();
        
        $service_list_company = $pricelistDetailModel->select('service_id as id,service_name,service_type,service_price')->where('pricelist_id', $pricelist_id)->where('status',1)->where('service_type <>','Consultation')->orderBy('service_name', 'ASC')->findAll();

        $service_id_list = [...BluelimeSystem::collect_field_value_from_object($service_list_company, 'id'), -1];

        $service_list = $pricelistDetailModel->select('service_id as id,service_name,service_type,service_price')->whereNotIn('service_id', $service_id_list)->where('pricelist_id', BluelimeConfiguration::DEFAULT_PRICE_LIST_ID)->where('status',1)->where('service_type <>','Consultation')->orderBy('service_name', 'ASC')->findAll();

        array_push($service_list, ...$service_list_company);
        
        array_push($service_list, ...[['id'=>BluelimeConfiguration::DISCOUNT_PRICE_AMOUNT_ID, 'service_name' => 'Discount In Amount'],['id'=>BluelimeConfiguration::DISCOUNT_PRICE_PERCENT_ID, 'service_name' => 'Discount In Percent']]);

        
        $vitalSign = $vitalSignModel->where('patient_id', $patient_id)->orderBy('registered_date', 'DESC')->findAll();
        //encounter

        $visitEncounter = $visitEncounterModel->where('visit_id', $visit_id)->findAll();

        $visitEncounter = $visitEncounter[0] ?? [];

        $related_visits_encounter = [];
        $related_visits_paid_service = [];
        $collected_related_visits = BluelimeSystem::collect_follow_up_visits($visit['follow_up_id'], $visit_id, false);

        //$related_visits_paid_service = $this->get_visits_paid_services([$visit_id]);

        $related_visits_encounter = BluelimeSystem::get_visits_encounters($collected_related_visits);
        $related_visits_paid_service = $this->get_visits_paid_services([$visit_id, ...$collected_related_visits]);

        $related_visits_extra = [];
        if(!empty($collected_related_visits))
            $related_visits_extra = $visitModel->select('extra')->whereIn('id',$collected_related_visits)->findAll();


        $history_follow_up_list = BluelimeSystem::get_patient_encounter_history($patient_id);

        $visit_list_to_remove = $visitModel->where('patient_id', $patient_id)->whereIn('id',[$visit_id, ...$collected_related_visits])->findAll();

        foreach($visit_list_to_remove as $row){
            foreach($history_follow_up_list as $index => $row2){
                if($row['follow_up_id'] == $row2['id'])
                    unset($history_follow_up_list[$index]);
            }
        }
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


      

        $visit['treatment_plans'] = $followUp['treatment_plans'] ?? [];
        $alert = BluelimeSystem::get_alert();

        $data['patient_procedure_history'] = $patient_procedure_history;
        $data['distinct_procedures'] = $distinct_procedures;
        $data['prescription_group'] = $prescription_group;
        $data['patient'] = $patient;
        $data['loggeduserdata'] = $loggeduserdata;
        $data['visit'] = $visit;
        $data['doctors'] = $doctors;
        $data['vitalSigns'] = $vitalSign;
        $data['history_follow_up_list'] = $history_follow_up_list;
        $data['laboratory_results_compiled'] = $laboratory_results_compiled;
        $data['active_menu'] = 'mypatients';
        $data['alert'] = $alert;

        return view('Include/_header', $data)
        
        .view('Include/_left_sidebar_top')
        .view('Hospital/Doctor/_left_sidebar_doctor')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Doctor/patient_history')
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

    public function closevisit(){
        $visitModel = model('Hospital/VisitModel');

        $visit_id = $_GET['visit_id'] ?? -1;

        $data = [
            'visit_status' => \BluelimeConfiguration::VISIT_STATUS_CLOSED,
            'visit_closed_date' => date('Y-m-d H:i:s')
        ];


        if(!$visitModel->update($visit_id, $data))
            BluelimeSystem::set_alert('There is no update made on the database',"OK","WARNING");
        return redirect()->to(base_url(). "hospital/doctor");
    }
    public function openvisit(){
        $visitModel = model('Hospital/VisitModel');

        $visit_id = $_GET['visit_id'] ?? -1;
        $visit_date = date('Y-m-d',strtotime($visitModel->where('id',$visit_id)->findColumn('visit_date')[0]));
        if($visit_date===date('Y-m-d')){
        $data = [
            'visit_status' => \BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN,
            'visit_closed_date' => NULL
        ];
    }
    else{
        BluelimeSystem::set_alert('Visit date Expired!',"OK","WARNING");
        return redirect()->to(base_url(). "hospital/doctor/patienthistory?visit_id=$visit_id");
        
    }


        if(!$visitModel->update($visit_id, $data))
            BluelimeSystem::set_alert('There is no update made on the database',"OK","WARNING");
        return redirect()->to(base_url(). "hospital/doctor");
    }

    public function refertodoctor(){
        $visitModel = model('Hospital/VisitModel');
        $userModel = model('System/UserModel');
        $visitModel = model('Hospital/VisitModel');
        $notificationModel = model('Hospital/NotificationModel');
        $loggeduserdata = BluelimeAuthentication::get_logged_user_data();

        $visit_id = $_POST['visit_id'] ?? -1;
        $assigned_doctor_id = $_POST['assigned_doctor'] ?? -1;
        $transfer_reason = $_POST['transfer_reason'] ?? '';

        $assigned_doctor = $userModel->where('id',$assigned_doctor_id)->findAll()[0];
        $visit = $visitModel->find($visit_id);
        $refer_visit = $visit;
        $queue_no = $visitModel->where('doctor_id',$assigned_doctor['id'])->where('visit_date >= ',date("Y-m-d%"))->findColumn('max(queue_no)');
        //BluelimeSystem::show_and_die($queue_no);
        $visit['extra']['transfer_reason'] = $transfer_reason;

        unset($refer_visit['id']);
        unset($refer_visit['bill_id']);
        $refer_visit['visit_status'] = BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN;
        $refer_visit['registered_by'] = $loggeduserdata['id'];
        $refer_visit['doctor_name'] = $assigned_doctor['name'];
        $refer_visit['doctor_id'] = $assigned_doctor['id'];
        $refer_visit['queue_no'] = intval($queue_no[0]) + 1;
        $refer_visit['registered_by_name'] = $loggeduserdata['name'];
        $refer_visit['visit_date'] = BluelimeSystem::date_now();
        $refer_visit['extra'] = BluelimeSystem::convert_array_to_string($visit['extra']);
        $refer_visit['is_free'] = 5;
        $refer_visit['appointment_id'] = 0;
        $refer_visit['follow_up_id'] = 0;
        
        $new_visit_id = $visitModel->insert($refer_visit);

        
        $notfiy_data_doctor = [
            'notification_type'=>  "Doctors Waiting",
            'link_type'=>  "visit_id",
            'description'=> $visit['patient_name'],
            'user_id'=> $assigned_doctor['id'],
            'link_id'=> $new_visit_id,
        ];
         
        $notificationModel->insert($notfiy_data_doctor,true);
        $visitModel->update($visit_id, ['visit_status' => BluelimeConfiguration::VISIT_STATUS_CLOSED, 'visit_closed_date' =>BluelimeSystem::date_now()]);

        return redirect()->to(base_url(). "hospital/doctor");
        /*
        $data = [
            'doctor_id' => $assigned_doctor,
            'doctor_name' => $doctor_name
        ];

        if(!$visitModel->update($visit_id, $data))
            BluelimeSystem::set_alert('There is no update made on the database',"OK","WARNING");
        */
    }

    public function join_visit_in_follow_up() {
        $follow_up_id = $_GET['follow_up_id'] ?? -1;
        $visit_id = $_GET['visit_id'] ?? -1;

        $visitModel = model('Hospital/VisitModel');

        $data = ['follow_up_id'=>$follow_up_id];

        if(intval($follow_up_id) > 0)
            $visitModel->update($visit_id, $data);
        
        return redirect()->to(base_url(). "hospital/doctor/visit?visit_id=".$visit_id);
    }

    public function disjoin_visit_from_follow_up() {
        $visit_id = $_GET['visit_id'] ?? -1;

        $visitModel = model('Hospital/VisitModel');
        $followUpModel = model('Hospital/FollowUpModel');

        $follow_up = $followUpModel->where('visit_id', $visit_id)->findColumn('id');
        
        $follow_up_id = $follow_up[0] ?? 0;

        $data = ['follow_up_id' => $follow_up_id];
        
        $visitModel->update($visit_id, $data);
        
        return redirect()->to(base_url(). "hospital/doctor/visit?visit_id=".$visit_id);
    }

    public function newprescription()
    {
        $visit_id = $_GET['visit_id'] ?? -1;
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $visitModel = model('Hospital/VisitModel');
        $drugModel = model('Inventory/ItemModel');
        $patientModel = model('Hospital/PatientModel');
        $compoundingTempletModel = model('Hospital/CompoundingTempletModel');
        $visit = $visitModel->find($visit_id);

        $patient = $patientModel->find($visit['patient_id']);

        $compounding_drugs = $drugModel->where('is_active', 1)->where('compounding_volume <>', '')->orderBy('item_generic_name')->orderBy('item_brand_name')->findAll();
        $drugs = $drugModel->where('is_active', 1)->orderBy('item_generic_name')->orderBy('item_brand_name')->findAll();

        $drugs_temp = [];
        foreach($drugs as $row)
            $drugs_temp['item_id_'.$row['id']] = $row;
        $drugs = $drugs_temp;

        $compounding_drugs_temp = [];
        foreach($compounding_drugs as $row){
            $row['strength'] = '';
            $compounding_drugs_temp['item_id_'.$row['id']] = $row;
        }
        $compounding_drugs = $compounding_drugs_temp;

        $compounding_templets = $compoundingTempletModel->where('created_by',$loggeduserdata['id'])->orderBy('title')->findAll();
        if($loggeduserdata['role'] == 'SUPER_ADMIN')
            $compounding_templets = $compoundingTempletModel->orderBy('title')->findAll();


        $doctors = BluelimeSystem::gather_objects_by_reference($compounding_templets,'created_by','System/UserModel','id',true);
        foreach($compounding_templets as $index => $row){
            $compounding_templets[$index]['created_by_name'] = $doctors['id_'.$row['created_by']]['name'] ?? '';
        }

    /***** Prescriptoin edit  *******/
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $prescription_group_id = $_GET['prescription_group_id'] ?? -1;
        $prescriptions = $prescriptionModel->where('prescription_group_id', $prescription_group_id)->where('is_sold', 0)->where('is_void', 0)->findAll();
        $compounding_prescriptions = $compoundingPrescriptionMainModel->where('prescription_group_id', $prescription_group_id)->where('is_sold', 0)->where('is_void', 0)->findAll();
        
        $prescription_details = BluelimeSystem::gather_objects_by_reference($prescriptions,'id','Hospital/PrescriptionDetailModel','prescription_id');

        $prescription_detail_data = [];
        foreach($prescription_details as $row){
            $prescription_detail_data['item_id_'.$row['drug_id']] = [
                'direction' => $row['direction'],
                'is_custom' => trim($row['custom_direction']) == '' ? false : true,
                'item'      => $row['drug_detail'],
                'nod'       => $row['number_of_days'],
                'remark'    => $row['custom_direction'],
                'route'     => $row['route']
            ];
        }

        $compounding_prescription_details = BluelimeSystem::gather_objects_by_reference($compounding_prescriptions,'id','Hospital/CompoundingPrescriptionDetailModel','main_id'); 

        $compounding_prescription_data = [];
        foreach($compounding_prescriptions as $index => $compounding_prescription){
            $single_compounding_prescription = [
                'description' => $compounding_prescription['description'],
                'compounding_list' => []
            ];

            foreach($compounding_prescription_details as $index2 => $detail){
                if($detail['main_id'] == $compounding_prescription['id']){
                    $single_compounding_prescription['compounding_list']['item_id_'.$detail['item_id']] = [
                        'amount' => $detail['amount'],
                        'in_percent' => $detail['in_percent'],
                        'item' => $detail['item_detail'],
                        'item_id' => $detail['item_id'],
                        'measurement' => $detail['measurement']
                    ];
                    unset($compounding_prescription_details[$index2]);
                }
            }
            array_push($compounding_prescription_data, $single_compounding_prescription);
        }

        $data['prescription_detail_data'] = $prescription_detail_data;
        $data['compounding_prescription_data'] = $compounding_prescription_data;

    /***** Prescriptoin edit end *******/

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'prescriptions';
        $data['drugs'] = $drugs;
        $data['compounding_drugs'] = $compounding_drugs;
        $data['patient'] = $patient;
        $data['compounding_templets'] = $compounding_templets;
        
        return view('Include/_header', $data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/Doctor/_left_sidebar_doctor')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Doctor/new_prescription2')
        .view('Include/_footer');
    }

    public function deletemedicalreport(){
        $medicalReportModel = model('Hospital/MedicalReportModel');
        
        $medicalReportModel->where('id',$_GET['mid'])->delete();

        return redirect()->to('/hospital/doctor/visit?visit_id='.$_GET['vid']);
    }
    public function deletelaboratoryrequest(){
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        
        $externalDocumentsModel->where('id',$_GET['elid'])->delete();

        return redirect()->to('/hospital/doctor/visit?visit_id='.$_GET['vid']);
    }
    public function deletereferalrequest(){
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        
        $externalDocumentsModel->where('id',$_GET['rrid'])->delete();

        return redirect()->to('/hospital/doctor/visit?visit_id='.$_GET['vid']);
    }
    public function deletebiopsyrequest(){
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        
        $externalDocumentsModel->where('id',$_GET['ebid'])->delete();

        return redirect()->to('/hospital/doctor/visit?visit_id='.$_GET['vid']);
    }
    public function voidprescription(){
        $prescription_group_id = $_POST['prescription_group_id'] ?? -1;
        //BluelimeSystem::show_and_die($prescription_group_id);
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $prescriptionModel->where('prescription_group_id',$prescription_group_id)->set('is_void', 1)->update();
        $compoundingPrescriptionMainModel->where('prescription_group_id', $prescription_group_id)->set('is_void',1)->update();        
        $result = '{"status":"success"}';
        return $result;
    }public function deleteprescription(){
        $prescription_group_id = $_GET['prescription_group_id'] ?? -1;
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $prescriptionDetailModel = model('Hospital/PrescriptionDetailModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $compoundingPrescriptionDetailModel = model('Hospital/CompoundingPrescriptionDetailModel');

        $prescriptions = $prescriptionModel->where('prescription_group_id', $prescription_group_id)->where('is_sold', 0)->findAll();
        $prescription_id_list = [...BluelimeSystem::collect_field_value_from_object($prescriptions,'id'), -1];
        $prescriptionDetailModel->whereIn('prescription_id', $prescription_id_list)->delete();
        $prescriptionModel->where('prescription_group_id',$prescription_group_id)->where('is_sold', 0)->delete();

        $compoundings = $compoundingPrescriptionMainModel->where('prescription_group_id', $prescription_group_id)->where('is_sold', 0)->findAll();
        $compounding_id_list = [...BluelimeSystem::collect_field_value_from_object($compoundings,'id'), -1];
        $compoundingPrescriptionDetailModel->whereIn('main_id', $compounding_id_list)->delete();
        $compoundingPrescriptionMainModel->where('prescription_group_id', $prescription_group_id)->where('is_sold', 0)->delete();        
        return redirect()->to('/hospital/doctor/myprescription');
    }

    public function save_compounding_templet(){
        $title = $_POST['title'];
        $compounding_list = $_POST['compounding_list'];
        $description = $_POST['description'];
        $loggeduser = BluelimeAuthentication::get_logged_user_data();

        $compoundingTempletModel = model('Hospital/CompoundingTempletModel');
        $data = [
            'title'=>$title,
            'compounding_list'=>$compounding_list,
            'description'=>$description,
            'created_by' => $loggeduser['id']
        ];

        $id = $compoundingTempletModel->insert($data);
        return BluelimeSystem::get_success_ajax_return($id);
    }

    public function delete_compounding_templet(){
        $templet_id = $_POST['templet_id'];
    
        $compoundingTempletModel = model('Hospital/CompoundingTempletModel');

        $deleted = $compoundingTempletModel->delete($templet_id);
        return BluelimeSystem::get_success_ajax_return(value: $deleted);
    }

    public function newmedicalcertificate()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $medicalCertificateModel = model('Hospital/MedicalCertificateModel');
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        $patientModel = model('Hospital/PatientModel');
        $visitModel = model('Hospital/VisitModel');
        $userModel = model('System/UserModel');
        
        $visit_id  = $_GET['vid'] ?? -1;
        $patient_id = $_GET['id'] ?? -1;
        $doc_id = $loggeduserdata['id'];
        
        $patient = $patientModel->find($patient_id);
        $medical_certificate = $medicalCertificateModel->where('patient_id',$patient_id)->where('visit_id', $visit_id)->orderBy('id','DESC')-> findAll();
        $encounterHistory = $visitEncounterModel->where('visit_id',$visit_id)->findAll();

        $visit = $visitModel->find($visit_id);
        $doctor_id = $visitModel->where('id',$visit_id)->findColumn('doctor_id');
        $doctor = $userModel->find($doctor_id);

        $reference = '';
        if(!empty($visit))
            $reference = $visit['extra']['reference'] ?? '';

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['visit_id'] = $visit_id;
        $data['reference'] = $reference;
        $data['doc_id'] = $doc_id;

        $data['doctor'] = $doctor[0];
        $data['medical_certificate'] = $medical_certificate;
        $data['encounterHistory'] = $encounterHistory;

        return view('Include/_header', $data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/Doctor/_left_sidebar_doctor')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Doctor/new_medical_certificate')
        .view('Include/_footer');
    }

    public function newmedicalreport()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $medicalReportModel = model('Hospital/MedicalReportModel');
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
        
        $visit_id  = $_GET['vid'] ?? -1;
        $patient_id = $_GET['id'] ?? -1;
        $doc_id = $loggeduserdata['id'];
        
        $patient = $patientModel->find($patient_id);
        $medical_report = $medicalReportModel->where('patient_id',$patient_id)->orderBy('id','DESC')-> findAll(1);

        //BluelimeSystem::show_and_die($medical_report);
        $encounterHistory = $visitEncounterModel->where('patient_id',$patient_id)->findAll();

        $visit = $visitModel->find($visit_id);
        $doctor_id = $visitModel->where('id',$visit_id)->findColumn('doctor_id');
        $doctor = $userModel->find($doctor_id);

        $reference = '';
        if(!empty($visit))
            $reference = $visit['extra']['reference'] ?? '';
        
            $encounter_history = BluelimeSystem::get_patient_encounter_history(@$_GET['id']);
            
        
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

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['visit_id'] = $visit_id;
        $data['reference'] = $reference;
        $data['doc_id'] = $doc_id;

        $data['doctor'] = $doctor[0];
        $data['medical_report'] = $medical_report;
        $data['encounterHistory'] = $encounterHistory;
        $data['encounter_history'] = $encounter_history;

        $data['prescription_group'] = $prescription_group;
        $data['laboratory_results_compiled'] = $laboratory_results_compiled;
        $data['distinct_procedures'] = $distinct_procedures;
        $data['patient_procedure_history'] = $patient_procedure_history;

        return view('Include/_header', $data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/Doctor/_left_sidebar_doctor')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Doctor/new_medical_report')
        .view('Include/_footer');
    }

    public function newappointment()
    {
        $patientModel = model('Hospital/PatientModel');
        $visitModel = model('Hospital/VisitModel');
        $userModel = model('System/UserModel');
        $appointmentModel = model('Hospital/AppointmentModel');

        $patient_appointment=$appointmentModel->where('patient_id',@$_GET['id'])->findAll();
        $appointment=$appointmentModel->where('id',@$_GET['app_id'])->findAll();
        $data['patient_appointment'] = $patient_appointment;
        //BluelimeSystem::show_and_die($appointment);
        $data['appointment'] = $appointment;
        $data['active_menu'] = 'appointment';
      
       $patient = $patientModel->find(@$_GET['id']);
       $last_visit_id = $visitModel->where('patient_id',@$_GET['id'])->orderBy('id DESC')->findColumn('id')[0];
       //BluelimeSystem::show_and_die($last_visit_id);

        $doctors = $userModel->where('role','DOCTOR')->where('active', 1)->findAll();
        
        //$data['patient'] = $patients;
        $data['doctors'] = $doctors;
        $data['patient'] = $patient;
        $data['last_visit_id'] = $last_visit_id;

        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/Doctor/_left_sidebar_doctor')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Doctor/new_appointment')

            .view('Include/_footer');
    }

    public function newxray()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $xrayModel = model('Hospital/XrayModel');
        $patientModel = model('Hospital/PatientModel');
        
        $patient = $patientModel->find(@$_GET['id']);
        $xray = $xrayModel->where('patient_id',$_GET['id'])->where('visit_id',$_GET['vid'])->orderBy('id','DESC')-> findAll();

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['visit_id']=$_GET['vid'];
        $data['xray'] = $xray;

        return view('Include/_header', $data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/Doctor/_left_sidebar_doctor')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Doctor/new_xray')
        .view('Include/_footer');
    }

    public function newlabrequest()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $labRequestModel = model('Hospital/LabRequestModel');
        $patientModel = model('Hospital/PatientModel');
        
        $patient = $patientModel->find(@$_GET['id']);
        $lab_request = $labRequestModel->where('patient_id',$_GET['id'])->where('visit_id',$_GET['vid'])->orderBy('id','DESC')-> findAll();
        
        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['visit_id']=$_GET['vid'];
        $data['lab_request'] = $lab_request;

        return view('Include/_header', $data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/Doctor/_left_sidebar_doctor')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Doctor/new_lab_request')
        .view('Include/_footer');
    }

    public function addprogresspicture()
    {
        $visit_id = $_POST['visit_id'];
        $patient_id     = $_POST['patient_id'];
        $this->upload_progress_picture();

        return redirect()->to(site_url('/hospital/doctor/newprogresspicture?id='.$patient_id.'&vid='.$visit_id));
    }

    public function addprogresspicture_mobile()
    {
        $visit_id = $_POST['visit_id'];
        $logged_user_id = $_POST['logged_user_id'] ?? -1;
        $patient_id     = $_POST['patient_id'];
        
        $image_list = $this->upload_progress_picture();

        return redirect()->to(site_url('/hospital/doctor/newprogresspicture_mobile?id='.$patient_id.'&vid='.$visit_id.'&logged_user_id='.$logged_user_id.'&image_list='.$image_list));
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
        .view('Hospital/Doctor/_left_sidebar_doctor')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Doctor/new_progress_picture')
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
        
        return view('Hospital/Doctor/new_progress_picture_mobile', $data);
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

    public function myappointment()
    {
        
        $appointmentModel = model('Hospital/AppointmentModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        
        $selected_date = isset($_POST['appointment_date'])?($_POST['appointment_date'].'%'):date('Y-m-d').'%';
        $doctor = $loggeduserdata['id'];

            
        $appointments = $appointmentModel->where('appointment_date like ',$selected_date)->where('doctor_id',$doctor)->orderBy("STR_TO_DATE(time,'%h:%i%p')")->findAll();
        //BluelimeSystem::show_and_die($doctors);
        $no_of_appointment = $appointmentModel->where('appointment_date like ',$selected_date)->where('doctor_id',$doctor)->findColumn('count(id)')[0]; 
            

        $data['selected_date'] = $selected_date;
        $data['appointments'] = $appointments;
        $data['no_of_appointment'] = $no_of_appointment;

        $data['active_menu'] = 'myappointment';
        $data['loggeduserdata'] = $loggeduserdata;

        $appointmentModel->updatemissed();

        
        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Doctor/_left_sidebar_doctor')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Doctor/appointment_list')

            .view('Include/_footer');
    }
    public function myprescription()
    {
        $searchkey= $_POST['search_printout'] ?? "";
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $medicalCertificateModel = model('Hospital/MedicalCertificateModel');
        $labRequestModel = model('Hospital/LabRequestModel');
        $notificationModel = model('Hospital/NotificationModel');
        $medicalReportModel = model('Hospital/MedicalReportModel');
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        
       // BluelimeSystem::show_and_die($searchkey);
        
        $data['active_menu'] = 'printoutlist';
        $data['loggeduserdata'] = $loggeduserdata;

        $prescriptions = $prescriptionModel->search_by_patient($searchkey);
        //$prescriptionDetail = $prescriptionDetailModel->search_by_patient($searchkey);
        $medicalCertificates = $medicalCertificateModel->search_by_patient($searchkey);
        $medicalReports = $medicalReportModel->search_by_patient($searchkey);
        $labRequests = $labRequestModel->search_by_patient($searchkey);
        $external_lab_request = $externalDocumentsModel->search_by_patient($searchkey);

        $printout_lists = [];
        $sort = [];

        foreach($prescriptions as $prescription){

            if($loggeduserdata['id']==$prescription['registered_by']){
                $row = [
                    'id' => $prescription['id'],
                    'type' => 'Prescription',
                    'prescription_group_id' => $prescription['prescription_group_id'],
                    'patient_id' => $prescription['patient_id'],
                    'patient_name' => $prescription['patient_name'],
                    'mobile' => $prescription['mobile'],
                    'registered_by_name' => $prescription['registered_by_name'],
                    'registered_date' => $prescription['registered_date']
                ];
                
                array_push($printout_lists, $row);
            }
        }

        $prescription_group_id_list = [...BluelimeSystem::collect_field_value_from_object($printout_lists, 'prescription_group_id'), -1];
        
        $compounding_prescriptions = $compoundingPrescriptionMainModel->where('created_by', $loggeduserdata['id'])->whereNotIn('prescription_group_id', $prescription_group_id_list)->findAll();

        foreach($compounding_prescriptions as $compounding_prescription){
                $row = [
                    'id' => $compounding_prescription['id'],
                    'type' => 'Prescription',
                    'prescription_group_id' => $compounding_prescription['prescription_group_id'],
                    'patient_id' => $compounding_prescription['patient_detail']['id'],
                    'patient_name' => $compounding_prescription['patient_detail']['name'],
                    'mobile' => $compounding_prescription['patient_detail']['mobile'],
                    'registered_by_name' => $compounding_prescription['created_by_name'],
                    'registered_date' => $compounding_prescription['created_at'],
                ];
                
                array_push($printout_lists, $row);
        }


        foreach($medicalCertificates as $medicalCertificate){
            if($loggeduserdata['id']==$medicalCertificate['registerd_by']){
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
        }
        foreach($medicalReports as $medicalReport){
            if($loggeduserdata['id']==$medicalReport['doctor_id']){
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
            if($loggeduserdata['id']==$labRequest['registered_by']){
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
        }
            foreach ($printout_lists as $key => $date) {
            $sort[$key] = strtotime($date['registered_date']);
       }
       
       if(is_array($sort)){
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
        
        $data['printout_lists'] = $printout_lists;
        $data['medicalCertificates'] = $medicalCertificates;
        $data['labRequests'] = $labRequests;
        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Doctor/_left_sidebar_doctor')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Doctor/print_order')

            .view('Include/_footer');
    }
    public function addxray()
    {
        $xrayModel = model('Hospital/XrayModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];

        $fl1 = $_FILES['xray']['tmp_name'];
        $fl1Name = "";
        if(is_uploaded_file($fl1))
        {
            $fl1Name = $_FILES['xray']['name'];
            $i = 0;
            while(file_exists("XrayResults/$fl1Name"))
                $fl1Name = ++$i . $fl1Name;
            move_uploaded_file($fl1,"XrayResults/$fl1Name");
        }
        
        $xray_location = $fl1Name;
        $visit_id = $_POST['visit_id'];
        $registered_by_name     = $logged_user_name;
        $registered_by     = $logged_user;
        $patient_id     = $_POST['patient_id'];
        $comment     = $_POST['comment'];
        $data = [
            
            'xray_location' => $xray_location,
            'registered_by_name' => $registered_by_name,
            'visit_id' => $visit_id,
            'registered_by'    => $registered_by,
            'patient_id'    => $patient_id,
            'comment'    => $comment
        ];
        
            $id = $xrayModel->insert($data,true);

        return redirect()->to(site_url('/hospital/doctor/newxray?id='.$patient_id.'&vid='.$visit_id));
    }

    public function addappointment()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
        $userModel = model('System/UserModel');
        $notificationModel = model('Hospital/NotificationModel');
        $scheduleModel = model('Hospital/ScheduleModel');
        
        $patient_id     = $_POST['patient_id'];
        $doctor_id    = $_POST['doctor_id'];
        $appointment_date      = $_POST['date'];
        $appointment_reason  = $_POST['appointment_reason'];
        $appointment_status            = $_POST['appointment_status'];
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
        //BluelimeSystem::show_and_die($_POST['app_id']);
        $patient_appointment_exist = $appointmentModel->patientappointmentexist($patient_id);
        $appointment_time_exist = $appointmentModel->appointmentchecker($doctor_id,$appointment_date,$time);
       // BluelimeSystem::show_and_die($patient_appointment_exist);
       
        if($patient_appointment_exist){        
            //return redirect()->to(site_url('/hospital/reception/newappointment?id='.$patient_id));
        }
        if($appointment_time_exist){  
            return redirect()->to(site_url('/hospital/doctor/newappointment?id='.$patient_id.'&rt='.'1'));
        }
        $id = $_POST['app_id']?? 0;
        
        if (intval($app_id) != 0)
            $appointmentModel->update($id,$data);
        else
            $id = $appointmentModel->insert($data,true);
        $notificationModel = model('Hospital/NotificationModel'); $notfiy_data = [
            'notification_type'=>  "New Appointments",
            'description'=> $full_name,
            'link_id'=> $id,
         ];
        $notificationModel->insert($notfiy_data,true);
        if($doctor_id==31){
            ;//BluelimeSystem::create_google_calendar_events($appointment_date,$time,$appointment_reason,$full_name,$schedule_min);
            }
        return redirect()->to(site_url('/hospital/doctor/newappointment?id='.$patient_id.'&app_id='.$id.'&vid='.$visit_id));
    }

    public function addprescription()
    {
        $visit_id = $_POST['visit_id'] ?? -1;
        $prescription_group_id = $_POST['prescription_group_id'] ?? -1;

        $prescription_detail = $_POST['prescription_detail'] ?? [];
        $compounding_prescription = $_POST['compounding_prescription'] ?? [];

        //BluelimeSystem::show_and_die($prescription_main);
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $prescriptionDetailModel = model('Hospital/PrescriptionDetailModel');
        $notificationModel = model('Hospital/NotificationModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $compoundingPrescriptionDetailModel = model('Hospital/CompoundingPrescriptionDetailModel');
        $visitModel = model('Hospital/VisitModel');
        $patientModel = model('Hospital/PatientModel');

        $visit = $visitModel->find($visit_id);

        if(empty($visit))
            return BluelimeSystem::get_error_ajax_return('Visit is not found. Visit id '.$visit_id);
        $patient_id = $visit['patient_id'];
        $patient = $patientModel->find($patient_id);

        if(empty($patient))
            return BluelimeSystem::get_error_ajax_return('Patient is not found with id '.$patient_id);

        $logged_user = BluelimeAuthentication::get_logged_user_data();
        
    /******* Disable old prescription deletion ***/
        $prescription_mains = $prescriptionModel->where('prescription_group_id', $prescription_group_id)->where('is_sold', 0)->where('is_void', 0)->findAll();
        $prescription_main_id_list = [...BluelimeSystem::collect_field_value_from_object($prescription_mains,'id'), -1];
        $prescriptionDetailModel->whereIn('prescription_id', $prescription_main_id_list)->delete();
        $prescriptionModel->where('prescription_group_id', $prescription_group_id)->where('is_sold', 0)->where('is_void', 0)->delete();

        $old_compounding_prescriptions = $compoundingPrescriptionMainModel->where('prescription_group_id', $prescription_group_id)->where('is_sold', 0)->where('is_void', 0)->findAll();
        $old_compounding_prescriptions_id_list = [...BluelimeSystem::collect_field_value_from_object($old_compounding_prescriptions,'id'), -1];
        $compoundingPrescriptionDetailModel->whereIn('main_id', $old_compounding_prescriptions_id_list)->delete();
        $compoundingPrescriptionMainModel->where('prescription_group_id', $prescription_group_id)->where('is_sold', 0)->where('is_void', 0)->delete();
    /************/

        $prescription_main = [
            'registered_by' => $logged_user['id'],
            'registered_by_name' => $logged_user['name'],
            'registered_date' => BluelimeSystem::date_now(),
            'patient_id' => $patient_id,
            'patient_name' => trim($patient['first_name'] . ' ' . $patient['middle_name'] . ' ' .$patient['last_name']),
            'visit_id' => $visit_id,
            'mobile' => $patient['mobile'],
            'company_id' => $patient['company_id'],
            'company_name' => $patient['company_name'],
        ];

        $prescription_group_id = $visit_id;
        if(!empty($prescription_detail)){
            
            $prescription_main_id = $prescriptionModel->insert($prescription_main);
            $prescription_group_id = intval("$visit_id"."$prescription_main_id");
            $prescriptionModel->update($prescription_main_id, ['prescription_group_id'=>$prescription_group_id]);
            $notfiy_data = [
                'notification_type'=>  "Prescriptions",
                'description'=> $prescription_main['patient_name'],
                'link_type'=> 'prescription_id',
                'link_id'=> $prescription_main_id,
            ];
            $notificationModel->insert($notfiy_data,true);
            
            $prescription_detail_data = [];
            foreach($prescription_detail as $prescription_line)
            {
                $prescription_line_temp = [
                    'prescription_id' => $prescription_main_id,
                    'drug_id' => $prescription_line['item']['id'],
                    'drug_detail' => BluelimeSystem::convert_array_to_string($prescription_line['item']),
                    'route' => $prescription_line['route'],
                    'direction' => $prescription_line['direction'],
                    'number_of_days' => $prescription_line['nod'],
                    'custom_direction' => $prescription_line['remark']
                ];

                array_push($prescription_detail_data, $prescription_line_temp);
            }

            if(!empty($prescription_detail_data))
                $prescriptionDetailModel->insertBatch($prescription_detail_data);
        }

        if(!empty($compounding_prescription)){
            $item_id_list = [];
            foreach($compounding_prescription as $row){
                if(empty($row))
                    continue;
                foreach($row['compounding_list'] as $line)
                    array_push($item_id_list, ['item_id' => $line['item_id']]);
            }

            $items = BluelimeSystem::gather_objects_by_reference( $item_id_list, 'item_id', 'Inventory/ItemModel','id',true);

            foreach($compounding_prescription as $row){
                if(empty($row))
                    continue;
                $compounding_main_data = [
                    'visit_id'=> $visit_id,
                    'patient_detail' => BluelimeSystem::convert_array_to_string([
                        'id' => $prescription_main['patient_id'],
                        'name' => $prescription_main['patient_name'],
                        'mobile' => $prescription_main['mobile'],
                        'sex' => $patient['sex'],
                        'age' => BluelimeSystem::calculate_age($patient['date_of_birth']),
                        'date_of_birth' => $patient['date_of_birth']
                    ]),
                    'created_by'=>$prescription_main['registered_by'],
                    'created_by_name' => $prescription_main['registered_by_name'],
                    'is_external' => 0,
                    'company_id' => $prescription_main['company_id'],
                    'company_name' => $prescription_main['company_name'],
                    'route' => $row['route'] ?? '',
                    'direction' => $row['direction'] ?? '',
                    'no_of_days' => $row['no_of_days'] ?? '',
                    'description' => $row['description'] ?? '',
                ];
                $compounding_main_id = $compoundingPrescriptionMainModel->insert($compounding_main_data);
                if($prescription_group_id == $visit_id)
                    $prescription_group_id = intval("$visit_id"."$compounding_main_id");

                $compoundingPrescriptionMainModel->update($compounding_main_id, ['prescription_group_id'=>$prescription_group_id]);
                
                $compounding_detail_data = [];
                foreach($row['compounding_list'] as $line){
                    $temp = [
                        'main_id' => $compounding_main_id,
                        'item_id' => $line['item_id'],
                        'item_detail' => BluelimeSystem::convert_array_to_string($items['id_'.$line['item_id']]),
                        'amount' => $line['amount'],
                        'measurement' => $line['measurement'],
                        'in_percent' => $line['in_percent']
                    ];
                    array_push($compounding_detail_data, $temp);
                }
                $compoundingPrescriptionDetailModel->insertBatch($compounding_detail_data);
            }
        }
        
        if((empty($prescription_detail)) && empty($compounding_prescription))
            return BluelimeSystem::get_error_ajax_return('Nothing is saved');

        return  BluelimeSystem::get_success_ajax_return($visit_id);
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
        $treatment_given      = $_POST['treatment_given'];
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
        
            return redirect()->to(site_url('/hospital/doctor/newmedicalcertificate?id='.$_POST['patient_id'].'&vid='.$visit_id.'&mid='.$id));
        
    }

    public function addmedicalreport()
    {
        $medicalReportModel = model('Hospital/MedicalReportModel');
        $notificationModel = model('Hospital/NotificationModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];


        $patient_id     = $_POST['patient_id'];
        $sex     = $_POST['sex'];
        $age     = $_POST['age'];
        $visit_id     = $_POST['visit_id'];
        $patient_name    = ucwords($_POST['first_name']." ".$_POST['middle_name']." ".$_POST['last_name']);
        $history      = $_POST['history'];
        
        $mobile = $_POST['mobile'];
       
        $data = [
            'patient_id'    => $patient_id,
            'patient_name'   => $patient_name,
            'sex'   => $sex,
            'age'   => $age,
            'history'   => $history,
            
            'doctor_id' => $logged_user,
            'doctor_name'           => $logged_user_name,
            'mobile'      => $mobile , 
        ];
            //BluelimeSystem::show_and_die(empty($_POST['mid']));
            if(!empty($_POST['mid'])){
                $medicalReportModel->update($_POST['mid'],$data);
            $id = $_POST['mid'];
            }
            else{
            $id=$medicalReportModel->insert($data,true);
            $notfiy_data = [
                'notification_type'=>  "Medical Report",
                'description'=> $patient_name,
                'link_type'=> 'medical_report_id',
                'link_id'=> $id,
             ];
            $notificationModel->insert($notfiy_data,true);
            }
        
            return redirect()->to(site_url('/hospital/doctor/viewmedicalreport?id='.$_POST['patient_id'].'&vid='.$visit_id.'&mid='.$id));
        
    }
    public function addlabrequest()
    {
        $labRequestModel = model('Hospital/LabRequestModel');
        $notificationModel = model('Hospital/NotificationModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];


        $patient_id     = $_POST['patient_id'];
        $patient_name    = ucwords($_POST['first_name']." ".$_POST['middle_name']." ".$_POST['last_name']);
        $company_name      = $_POST['company_name'];
        $registerd_by  = $logged_user;
        $registered_by_name            = $logged_user_name;
        $visit_id         = $_POST['visit_id'];
        $description = $_POST['description'];
        $mobile = $_POST['mobile'];
        $upper_tray = $_POST['upper_tray'];
        $lower_tray = $_POST['lower_tray'];
        $type = $_POST['type'];
        $special_wish = $_POST['special_wish'];
        $delivery_date = $_POST['delivery_date'];
        $teeth_selected = preg_split('/ /', $_POST['teeth_selected'], -1, PREG_SPLIT_NO_EMPTY); 
        $status = 0;
        $time = '9:00 AM';
        if(is_array($teeth_selected))
            $teeth_selected = $teeth_selected;
            //BluelimeSystem::show_and_die($teeth_selected);

        $data = [
            'patient_id'    => $patient_id,
            'patient_name'   => $patient_name,
            'company_name'     => $company_name,
            'registered_by' => $registerd_by,
            'doctor_id' => $registerd_by,
            'registered_by_name' => $registered_by_name,
            'visit_id'       => $visit_id,
            'description'      => $description,  
            'mobile'      => $mobile , 
            'upper_tray'      => $upper_tray , 
            'lower_tray'      => $lower_tray , 
            'type'      => $type , 
            'special_wish'      => $special_wish , 
            'delivery_date'      => $delivery_date , 
            'status'      => $status, 
            'teeth_selected'      => $teeth_selected 
        ];

            $id=$labRequestModel->insert($data,true);
            $notfiy_data = [
                'notification_type'=>  "Lab Requests",
                'description'=> $patient_name,
                'link_type'=> 'lab_request_id',
                'link_id'=> $id,
             ];
            $notificationModel->insert($notfiy_data,true);
           
            //BluelimeSystem::create_google_calendar_events($delivery_date,$time,$patient_name,$company_name.' Lab Request Delivery',0);
            
            return redirect()->to(site_url('/hospital/doctor/viewlabrequest?id='.$_POST['patient_id'].'&vid='.$visit_id.'&lid='.$id));
        
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
        $data['active_menu'] = 'prescriptions';
        //$data['doc_sign'] = $doc_sign[0];
        $data['patient'] = $patient;
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Doctor/_left_sidebar_doctor')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Doctor/view_prescription')

            .view('Include/_footer');
    }

    public function viewmedicalcertificate()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $medicalCertificateModel = model('Hospital/MedicalCertificateModel');
        $patientModel = model('Hospital/PatientModel');
        
        $patient = $patientModel->find(@$_GET['id']);

        $medical_certificate = $medicalCertificateModel->where('id',$_GET['mid'])->orderBy('id','DESC')-> findAll();

        $doc_sign = $loggeduserdata['digital_signature'];
        $doc_id = $loggeduserdata['id'];

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['medical_certificate'] = $medical_certificate;
        $data['doc_sign'] = $doc_sign;
        $data['doc_id'] = $doc_id;
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Doctor/_left_sidebar_doctor')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Doctor/view_medical_certificate')

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

        $medical_report = $medicalReportModel->where('id',$_GET['mid'])->where('doctor_id',$loggeduserdata['id'])->orderBy('id','DESC')-> findAll();
        //BluelimeSystem::show_and_die($medical_report);  
        $doc_sign = $userModel->where('id',$medical_report[0]['doctor_id'])->findColumn('digital_signature'); 
            
        $doctor = $userModel->find($medical_report[0]['doctor_id']);
 
        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['doctor'] = $doctor;
        $data['medical_report'] = $medical_report;
        $data['doc_sign'] = $doc_sign[0];
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Doctor/_left_sidebar_doctor')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Doctor/view_medical_report')

            .view('Include/_footer');
    }
    public function viewlabrequest()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $labRequestModel = model('Hospital/LabRequestModel');
        $patientModel = model('Hospital/PatientModel');
        
        $patient = $patientModel->find(@$_GET['id']);
        $doc_sign = $loggeduserdata['digital_signature'];

        $lab_request = $labRequestModel->where('id',$_GET['lid'])->orderBy('id','DESC')-> findAll();

        $data['loggeduserdata'] = $loggeduserdata;
        $data['active_menu'] = 'patients_waiting';
        $data['patient'] = $patient;
        $data['lab_request'] = $lab_request;
        $data['doc_sign'] = $doc_sign;
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Hospital/Doctor/_left_sidebar_doctor')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Doctor/view_lab_request')

            .view('Include/_footer');
    }
    public function deletemedicalcertificate()
    {
        $medicalCertificateModel = model('Hospital/MedicalCertificateModel');
        
        $medicalCertificateModel->where('id',$_GET['mid'])->delete($_GET['mid']);
          
        return redirect()->to(site_url('/hospital/doctor/newmedicalcertificate?id='.$_GET['id'] .'&vid='.$_GET['vid']));
        
    }

    public function doctorschedule()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $scheduleModel = model('Hospital/ScheduleModel');
        $skipDateModel = model('Hospital/SkipDateModel');
        $logged_user = $loggeduserdata['id'];

        $skips=$skipDateModel->where('doctor_id',$logged_user)->findAll();

        $data['skips'] = $skips;

        $schedules=$scheduleModel->where('doctor_id',$loggeduserdata['id'])->findAll();

        $data['loggeduserdata'] = $loggeduserdata;
        $data['schedules'] = $schedules;
        $data['active_menu'] = 'patient_waiting';

        return view('Include/_header', $data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/Doctor/_left_sidebar_doctor')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Doctor/doctor_schedule')
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
        return redirect()->to(site_url('/hospital/doctor/doctorschedule'));
        
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
        //BluelimeSystem::delete_schedule($_POST['schedule_id']);
        
    }
    public function cleardoctorsschedule()
    {
        $scheduleModel = model('Hospital/ScheduleModel');

        $scheduleModel->delete($_GET['sid']);
       
        return redirect()->to(site_url('/hospital/doctor/doctorschedule'));
        
    }

    public function addskipdate()
    {
        $skipDateModel = model('Hospital/SkipDateModel');
        $notificationModel = model('Hospital/NotificationModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
       
        $skip_date = $_POST['skip_date'];
        $data = [
            'skip_date'    => $skip_date,
            'doctor_id'   => $logged_user
        ];
        
        $id = $skipDateModel->insert($data,true);
        $notfiy_data = [
            'notification_type'=>  "Skip Date",
            'description'=> $skip_date,
            'link_type'=> 'skip_id',
            'user_id'=> $logged_user,
            'link_id'=> $id,
         ];
        $notificationModel->insert($notfiy_data,true);
          
        return redirect()->to(site_url('/hospital/doctor/doctorschedule'));
        
    }
    public function deleteskipdate()
    {
        $skipDateModel = model('Hospital/SkipDateModel');
        
        $skipDateModel->where('id',$_GET['id'])->delete($_GET['id']);
          
        return redirect()->to(site_url('/hospital/doctor/doctorschedule'));
        
    }

    public function deletelabrequest()
    {
        $labRequestModel = model('Hospital/LabRequestModel');
        
        $labRequestModel->where('id',$_GET['lid'])->delete($_GET['lid']);
          
        return redirect()->to(site_url('/hospital/doctor/newlabrequest?id='.$_GET['id'] .'&vid='.$_GET['vid']));
        
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
        $page = $_GET['page']??1;
        $page = intval($page);
        $cnt =0;
        foreach($followUps as $followUp){
            if($this->followup_has_treatment_plans($followUp)){
                $cnt++;
                if((($page-1)*10) >= $cnt)
                    continue;

                array_push($treatment_plans, $followUp);
            }

            if(count($treatment_plans)>= 10)
                break;
        }
        //echo $cnt;
        //die();

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
            .view('Hospital/Doctor/_left_sidebar_doctor')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Doctor/treatment_plans')
            .view('Include/_footer');
    }
    public function treatmentplanchoice(){
        $follow_up_id = $_GET['follow_up_id'] ?? -1;

        $followUpModel = model('Hospital/FollowUpModel');
        $notificationModel = model('Hospital/NotificationModel');

        $follow_up = $followUpModel->find($follow_up_id);

        $treatment_plans = $follow_up['treatment_plans'] ?? [];
        $company_id = $follow_up['company_id'] ?? 0;

        //$notificationModel->where('link_id',$follow_up_id)->where('link_type','followup_id')->where('notification_type','New Plan')->set('status',\BluelimeConfiguration::NOTIFICATION_SEEN)->update();


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
            .view('Hospital/Doctor/_left_sidebar_doctor')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Doctor/treatment_plans_choice')
            .view('Include/_footer');
    }

    public function labrequestsave()
    {
        $request_data = $_POST['request_data'];
        //BluelimeSystem::show_and_die($request_data['lab_request_detail_data']);
        
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        $notificationModel = model('Hospital/NotificationModel');

        $request_data['doctor_id'] = $request_data['request_doctor_id'];
        $request_data['patient_id'] = $request_data['request_patient_id'];
        $request_data['registered_by_name'] = $request_data['request_registered_by_name'];
        $request_data['registered_by'] = $request_data['request_registered_by'];
        $request_data['visit_id'] = $request_data['request_visit_id'];
        $request_data['mobile'] = $request_data['request_mobile'];
        $request_data['patient_name'] = $request_data['request_patient_name'];
        $request_data['lab_request_detail_data']['request_remark'] = $request_data['request_remark'];
        $request_data['description'] = BluelimeSystem::convert_array_to_string($request_data['lab_request_detail_data']);
        $request_data['status'] = 1;

        $request_id = $externalDocumentsModel->insert($request_data);
        $notfiy_data = [
            'notification_type'=>  "External Laboratory Request",
            'description'=> $request_data['request_patient_name'],
            'link_type'=> 'external_request_id',
            'link_id'=> $request_id,
         ];
        $notificationModel->insert($notfiy_data,true);
        
        return '{"status":"success", "value":"'.$request_id.'"}';
        
    }

    public function referalrequestsave()
    {
        $request_data = $_POST['request_data'];
        
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        $notificationModel = model('Hospital/NotificationModel');
        
        $request_data['doctor_id'] = $request_data['ref_request_doctor_id'];
        $request_data['patient_id'] = $request_data['ref_request_patient_id'];
        $request_data['registered_by_name'] = $request_data['ref_request_registered_by_name'];
        $request_data['registered_by'] = $request_data['ref_request_registered_by'];
        $request_data['visit_id'] = $request_data['ref_request_visit_id'];
        $request_data['mobile'] = $request_data['ref_request_mobile'];
        $request_data['patient_name'] = $request_data['ref_request_patient_name'];
        $request_data['requested_company_name'] = $request_data['ref_requested_company_name'];
        $request_data['request_type'] = $request_data['ref_request_type'];

        
       
        $request_data['ref_request_detail_data']['ref_clinical_finding'] = $request_data['ref_clinical_finding'];
        $request_data['ref_request_detail_data']['ref_diagnosis'] = $request_data['ref_diagnosis'];
        $request_data['ref_request_detail_data']['ref_investigation_result'] = $request_data['ref_investigation_result'];
        $request_data['ref_request_detail_data']['ref_px_given'] = $request_data['ref_px_given'];
        $request_data['ref_request_detail_data']['ref_reason'] = $request_data['ref_reason'];

        $request_data['description'] = BluelimeSystem::convert_array_to_string($request_data['ref_request_detail_data']);
        $request_data['status'] = 1;
        //BluelimeSystem::show_and_die($request_data['description']);

        $request_id = $externalDocumentsModel->insert($request_data);
        $notfiy_data = [
            'notification_type'=>  "Referal Request",
            'description'=> $request_data['ref_request_patient_name'],
            'link_type'=> 'external_request_id',
            'link_id'=> $request_id,
         ];
        $notificationModel->insert($notfiy_data,true);
        
        return '{"status":"success", "value":"'.$request_id.'"}';
        
    }public function biopsyrequestsave()
    {
        $request_data = $_POST['request_data'];
        
        
        $externalDocumentsModel = model('Hospital/ExternalDocumentsModel');
        $notificationModel = model('Hospital/NotificationModel');

        $request_data['doctor_id'] = $request_data['bio_request_doctor_id'];
        $request_data['patient_id'] = $request_data['bio_request_patient_id'];
        $request_data['registered_by_name'] = $request_data['bio_request_registered_by_name'];
        $request_data['registered_by'] = $request_data['bio_request_registered_by'];
        $request_data['visit_id'] = $request_data['bio_request_visit_id'];
        $request_data['mobile'] = $request_data['bio_request_mobile'];
        $request_data['patient_name'] = $request_data['bio_request_patient_name'];
        $request_data['requested_company_name'] = $request_data['bio_requested_company_name'];
        $request_data['request_type'] = $request_data['bio_request_type'];

        
       
        $request_data['bio_request_detail_data']['bio_request_doctor_name'] = $request_data['bio_request_doctor_name'];
        $request_data['bio_request_detail_data']['bio_request_history'] = $request_data['bio_request_history'];
        $request_data['bio_request_detail_data']['bio_request_physical_finding'] = $request_data['bio_request_physical_finding'];
        $request_data['bio_request_detail_data']['bio_request_diagnosis'] = $request_data['bio_request_diagnosis'];
        $request_data['bio_request_detail_data']['bio_request_imaging'] = $request_data['bio_request_imaging'];
        $request_data['bio_request_detail_data']['biopsy_type'] = $request_data['biopsy_type'];
        $request_data['bio_request_detail_data']['biopsy_size'] = $request_data['biopsy_size'];
        $request_data['bio_request_detail_data']['bio_urgent'] = $request_data['bio_urgent'];
        $request_data['bio_request_detail_data']['bio_request_site'] = $request_data['bio_request_site'];
        $request_data['bio_request_detail_data']['bio_request_anesthesia'] = $request_data['bio_request_anesthesia'];
        $request_data['bio_request_detail_data']['bio_request_fixative'] = $request_data['bio_request_fixative'];

        $request_data['description'] = BluelimeSystem::convert_array_to_string($request_data['bio_request_detail_data']);
        $request_data['status'] = 1;
        //BluelimeSystem::show_and_die($request_data['description']);

        $request_id = $externalDocumentsModel->insert($request_data);
        $notfiy_data = [
            'notification_type'=>  "External Biopsy Request",
            'description'=> $request_data['bio_request_patient_name'],
            'link_type'=> 'external_request_id',
            'link_id'=> $request_id,
         ];
        $notificationModel->insert($notfiy_data,true);
        
        return '{"status":"success", "value":"'.$request_id.'"}';
        
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

            .view('Hospital/Doctor/_left_sidebar_doctor')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Doctor/view_referal_request')

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

            .view('Hospital/Doctor/_left_sidebar_doctor')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Doctor/view_external_biopsy')

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

            .view('Hospital/Doctor/_left_sidebar_doctor')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Doctor/view_external_laboratory')

            .view('Include/_footer');
    }

}

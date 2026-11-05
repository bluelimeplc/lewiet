<?php

namespace App\Controllers\Hospital;

use BluelimeAuthentication;
use BluelimeConfiguration;
use BluelimeSystem;

class Ajax extends \App\Controllers\BaseController
{
    public function index()
    {
        echo 'Welcome to ajax';
    }

    /**
     * Add new item in to diffrent common looking tables. (chief_complaints, dental_medical_history, examinations, diseases ...) 
     * All these tables has only three fields. (id, name, description)
     * The data will be fetch from POST.
     * @param string $_POST['type']    The model or the table will be used to insert the data
     * @param string $_POST['name']     
     * @param string $_POST['description']
     * 
     * @return string   The method will return two type of jsons,
     *                  1: Error: Staus is "Error" and Result is error message
     *                  2: Success: Status is "Success" and Result is the result message (name)
     *                  
     * Autor: Abiy G.
     */

    /*
    public function addnewitemintocommonlookingtable(){

        $modelNames = [
            'drug'      => 'DrugModel',
            'allergy'   => 'AllergyModel',
            'disease'   => 'DiseaseModel',
            'chief_complaints'   => 'ChiefComplaintModel',
            'medical_history'   => 'MedicalHistoryModel',
            'dental_medical_history'   => 'DentalMedicalHistoryModel',
            'examination_extra_oral'   => 'ExaminationExtraOralModel',
            'examination_intra_oral'   => 'ExaminationIntraOralModel',
            'diagnosis'   => 'DiagnosisModel',
            'treatment'   => 'TreatmentModel',
            'procedure'   => 'ProcedureModel'
        ];

        $type = $_POST['type'] ?? '';
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';

        $EMPTY_TYPE = '{"status":"Error","result":"Model type is empty."}';
        $EMPTY_NAME = '{"status":"Error","result":"Name is empty."}';
        $MODEL_NAME_NOT_FOUND = '{"status":"Error","result":"Cannot find model name for type \'' . addslashes($type) . '\'."}';

        $type = trim($type);

        if($type == '')
            return $EMPTY_TYPE;
        
        $modelName = $modelNames[$type] ?? '';

        if($modelName == '')
            return $MODEL_NAME_NOT_FOUND;

        $model = model('Hospital/' . $modelName);

        $MODEL_NOT_FOUND = '{"status":"Error","result":"Model \'Hospital//' . $modelName . '\' returns null."}';
        if($model === null)
            return $MODEL_NOT_FOUND;

        $data['name'] = trim($name);
        $data['description'] = trim($description);
        
        $DATA_EXIST = '{"status":"Error","result":"Recored already exist. Name: ' . addslashes($name) . '"}';


        if($data['name'] == '')
            return $EMPTY_NAME;
        
        if(empty($model->where('name', $data['name'])->findAll()))
            $result = ($model->insert($data) > 0) ? $data['name']: '-1';
        else
            return $DATA_EXIST;

        if($result == '-1')
            $result = '{"status":"Error","result":"Recored was not added to database."}';
        else
            $result = '{"status":"success","result":"' . addslashes($name) . '"}';

        return $result;
    }
    */

    public function add_auto_complete_data(){
        $name = $_POST['name'];
        $value = $_POST['value'];
        $value = ucfirst($value);

        $autoCompleteModel = model('Hospital/AutoCompleteModel');

        $auto_compelete = $autoCompleteModel->where('name', $name)->findAll();
        $auto_compelete = $auto_compelete[0] ?? [];
        $id = $auto_compelete['id'] ?? -1;
        if($id == -1)
            $id = $autoCompleteModel->insert(['name'=>$name, 'data' => ['["'.$value.'"]']]);
        else{
            if(array_search($value,$auto_compelete['data']) === false){
                array_push($auto_compelete['data'], $value);
                $update_data = ['data' => BluelimeSystem::convert_array_to_string($auto_compelete['data'])];
                $autoCompleteModel->update($id,$update_data);
            }
            else
                return BluelimeSystem::get_error_ajax_return($value . ' already exisits.');
        }
        return BluelimeSystem::get_success_ajax_return($value);
    }

    private function followup_has_treatment_plans(array $follow_up):bool{

        $treatment_plans = $follow_up['treatment_plans'] ?? [];
        
        foreach($treatment_plans as $index => $treatment_plan){
            if(($index != 'plan_1') && ($index != 'plan_2') && ($index != 'plan_3')&& ($index != 'plan_4'))
                continue;
            foreach($treatment_plan as $plan){
                if(trim($plan['title']) != '')
                    return true;
            }
        }
        return false;
    }

    public function savetreatmentplan()
    {
        $notificationModel = model('Hospital/NotificationModel');
        $followUpModel = model('Hospital/FollowUpModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();

        $visit_id = $_POST['visit_id'] ?? -1;
        $treatment_plan_data = $_POST['treatment_plan_data'] ?? [];
        $patient_name = $_POST['patient_name'] ?? '';
        $mobile = $_POST['mobile'] ?? '';
        $company_id = $_POST['company_id'] ?? 0;
        $company_name = $_POST['company_name'] ?? '';
        $registered_date = date("Y-m-d");
        
        $visitModel = model('Hospital/VisitModel');
        $visit = $visitModel->find($visit_id);

        $patient_id = $visit['patient_id'] ?? -1;
        $follow_up_id = $visit['follow_up_id'] ?? -1;

                
        foreach($treatment_plan_data as $index => $service_list){

            if(substr($index, 0, 5) != 'plan_'){
                $treatment_plan_data[$index] = $service_list;
                continue;
            }


            $service_list_orginal = [];

            foreach($service_list as $service_index => $value)
            {

                $service_id = BluelimeSystem::convert_to_array($value['title'])[0]['id'] ?? -1;
                
                if($service_id == -1){
                    unset($service_list[$service_index]);
                    continue;
                }

                $service_list[$service_index]['service_id'] = $service_id;
                $service_list_orginal[$service_index] = $service_list[$service_index];
                
            }

            $service_list = BluelimeSystem::add_price_detail_in_service_list($company_id, $service_list);

            /* Reset to old price if there is price change. But did not work 
            
            foreach($service_list as $service_index => $value)
            {
                $old_price = $service_list_orginal[$service_index]['price'] ?? 0;

                if(($old_price > 0) && ($service_list_orginal[$service_index]['service_id'] == $service_list[$service_index]['service_id']))
                    $service_list[$service_index]['price'] = $old_price;
            }
            */

            $treatment_plan_data[$index] = $service_list;
        }

        $data = [
            'treatment_plans' => BluelimeSystem::convert_array_to_string($treatment_plan_data),
            'patient_id' => $patient_id,
            'patient_name' => $patient_name,
            'mobile' => $mobile,
            'company_id' => $company_id,
            'company_name' => $company_name,
            'doctor_id' => $loggeduserdata['id'],
            'doctor_name' => $loggeduserdata['name'],
        ];


        if($follow_up_id > 0)
            $data['id'] = $follow_up_id;
        else{
            $data['visit_id'] = $visit_id;
            $data['date_created'] = $registered_date;
        }

        $followUpModel->save($data);

        $new_follow_up_id = $followUpModel->getInsertID();

        $data = [
            'follow_up_id' => $new_follow_up_id,
        ];

        if(($new_follow_up_id != $follow_up_id) and ($new_follow_up_id > 0))
            $visitModel->update($visit_id, $data);

        $notfiy_data = [
            'notification_type'=>  "New Plan",
            'description'=> $visit_id,
            'link_type'=> 'followup_id',
            'link_id'=> $follow_up_id,
            ];

        $visitFollowups = $followUpModel->where('visit_id',$visit_id)->findAll();
        foreach($visitFollowups as $followUp){
            if($this->followup_has_treatment_plans($followUp)){
                $fid = $notificationModel->where('link_type','followup_id')->where('description',$visit_id)->findAll();
                //BluelimeSystem::show_and_die($fid);
                if(!$fid){
                    $notificationModel->insert($notfiy_data,true);
                }        
                else{
                    $notificationModel->where('link_type','followup_id')->where('description',$visit_id)->set('status',0)->update();
                }
            }
            else{
                $notificationModel->where('link_type','followup_id')->where('description',$visit_id)->set('status',1)->update();
            }
        }
        
        return '{"status":"success", "result":"Treatment paln saved."}';
    }

    public function savechiefcomplaint(){
        $visit_id = $_POST['visit_id'] ?? -1;
        $chief_complaints = json_decode($_POST['chief_complaints'] ?? '{}', true);

        if(!is_array($chief_complaints))
            return $this->response->setJSON(['status' => 'error', 'result' => 'Invalid chief complaint data.']);

        $visitModel = model('Hospital/VisitModel');
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        $visit = $visitModel->find($visit_id);

        if(empty($visit))
            return $this->response->setJSON(['status' => 'error', 'result' => 'Visit was not found.']);

        $visitEncounter = $visitEncounterModel->where('visit_id', $visit_id)->first();
        $chief_complaints_json = BluelimeSystem::convert_array_to_string($chief_complaints);

        if(empty($visitEncounter)){
            $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
            $saved = $visitEncounterModel->insert([
                'visit_id' => $visit_id,
                'registered_date' => date('Y-m-d'),
                'doctor_name' => $loggeduserdata['name'],
                'doctor_id' => $loggeduserdata['id'],
                'patient_id' => $visit['patient_id'],
                'patient_name' => $visit['patient_name'] ?? '',
                'chief_complaints' => $chief_complaints_json
            ]);
        }
        else
            $saved = $visitEncounterModel->update($visitEncounter['id'], ['chief_complaints' => $chief_complaints_json]);

        if(!$saved)
            return $this->response->setJSON(['status' => 'error', 'result' => 'Chief complaint could not be saved.']);

        return $this->response->setJSON(['status' => 'success', 'result' => 'Chief complaint saved.']);
    }

    public function savevisitencounter(){
        
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        $visitModel = model('Hospital/VisitModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        
        $visit_id = $_POST['visit_id'] ?? -1;
        $visit = $visitModel->find($visit_id);

        $patient_id = $visit['patient_id'] ?? -1;
        $follow_up_id = $visit['follow_up_id'] ?? -1;

        $followUpModel = model('Hospital/FollowUpModel');
        
        
        $encounter_data = $_POST['encounter_data'] ?? [];
        //$treatment_plan_data = $_POST['treatment_plan_data'];
        $patient_name = $_POST['patient_name'] ?? '';
        $mobile = $_POST['mobile'] ?? '';
        $company_id = $_POST['company_id'] ?? '';
        $company_name = $_POST['company_name'] ?? '';
        $next_appointment_description = $_POST['next_appointment_description'] ?? '';
        //$follow_up_data = $_POST['follow_up_data'];
        $follow_up_remark = $_POST['follow_up_remark'];
        $selected_skin = $_POST['selected_skin'];

        $registered_date = date("Y-m-d");

        $allowed_fields = $visitEncounterModel->get_allowed_fields();
        $data = [];

        foreach($allowed_fields as $field_name){
            $data[$field_name] = null;
        }

        $data['visit_id'] = $visit_id;
        $data['registered_date'] = $registered_date;
        $data['doctor_name'] = $loggeduserdata['name'];
        $data['doctor_id'] = $loggeduserdata['id'];
        $data['patient_id'] = $patient_id;
        $data['patient_name'] = $patient_name;

        foreach($encounter_data as $index => $value){
            if($index == 'treatment_plans')
                continue;
            $data[$index] = BluelimeSystem::convert_array_to_string($value);
        }
        $visit_encounter = $visitEncounterModel->where('visit_id', $visit_id)->findColumn('id');
        unset($data['id']);
        if(!empty($visit_encounter))
            $data['id'] = $visit_encounter[0];
        
        $visitEncounterModel->save($data);

       

        //BluelimeSystem::show_and_die($follow_up_data);

        $treatment_plans = $encounter_data['treatment_plans'] ?? [];
        if(empty($treatment_plans))
            $treatment_plans = null;
        else
            $treatment_plans = BluelimeSystem::convert_array_to_string($treatment_plans);
        $data = [
            'patient_id' => $patient_id,
            'patient_name' => $patient_name,
            'mobile' => $mobile,
            'company_id' => $company_id,
            'company_name' => $company_name,
            'remark' => $follow_up_remark,
            'treatment_plans' => $treatment_plans,
            'selected_skin' => $selected_skin,
            'doctor_id' => $loggeduserdata['id'],
            'doctor_name' => $loggeduserdata['name']
            //'selected_teeth' => BluelimeSystem::convert_array_to_string($follow_up_data['selected_teeth']?? []),
        ];

        if($follow_up_id > 0)
            $data['id'] = $follow_up_id;
        else{
            $data['visit_id'] = $visit_id;
            $data['date_created'] = $registered_date;
        }

        $followUpModel->save($data);

        $new_follow_up_id = $followUpModel->getInsertID();

        $extra = $visit['extra'] ?? [];
        $extra['next_appointment_description'] = $next_appointment_description;

        $extra = BluelimeSystem::convert_array_to_string($extra);
        
        $data = [
            'follow_up_id' => $new_follow_up_id,
            'extra' => $extra
        ];

        if(($new_follow_up_id != $follow_up_id) and ($new_follow_up_id > 0))
            $visitModel->update($visit_id, $data);
        else
            $visitModel->update($visit_id, ['extra' => $extra]);

        return '{"status":"success", "result":"Encounter saved."}';
    }
 
    public function saveorderoptions(){
        $logged_user = BluelimeAuthentication::get_logged_user_data();
        $notificationModel = model('Hospital/NotificationModel');
        $billModel = model('Hospital/BillModel');
        $orderMainModel = model('Hospital/OrderMainModel');
        $orderDetailModel = model('Hospital/OrderDetailModel');
        $patientModel = model('Hospital/PatientModel');
        $visitModel = model('Hospital/VisitModel');
        $companyModel = model('Hospital/CompanyModel');

        $visit_id = $_POST['visit_id'] ?? -1;
        $patient_id = $_POST['patient_id'] ?? -1;
        $patient = $patientModel->where('id',$patient_id)->find()[0];
        //BluelimeSystem::show_and_die($patient);
        $date_of_birth = $patient['date_of_birth'];
        $sex = $patient['sex'];
        $patient_name = $_POST['patient_name'] ?? '';
        $description = $_POST['order_comment'] ?? '';
        $mobile = $_POST['mobile'] ?? '';
        $company_id = intval($_POST['company_id'] ?? 0);
        $company_name = $_POST['company_name'] ?? '';
        $registered_by = $logged_user['id'] ?? -1;
        $registered_by_name = $logged_user['name'] ?? '';
        $visit = $visitModel->find($visit_id);
        $ordered_by = $visit['doctor_id'] ?? -1;
        $options = $_POST['order_option'] ?? [];
        $order_extra_info = $_POST['order_extra_info'] ?? [];
        $is_external_order = $_POST['is_external_order'] ?? false;
        $is_free_service = $_POST['is_free_service'] ?? false;
        
        $order_id_list = '';
        $is_cash = $companyModel->where('id',$company_id)->findColumn('is_cash')[0]??0;
        //BluelimeSystem::show_and_die($is_cash);
        $bill_type   = (($company_id >=1 && intval($is_cash ==0)) ? "Credit" : "Cash");

        $service_list = [];
        foreach($options as $option){
            foreach($option as $service){
                $service['bill_quantity'] = $service['quantity'] ?? 0;
                array_push($service_list ,$service);
            }
        }
       
        if(!$is_external_order){
            $old_pending_bills_id_list = $billModel->where('visit_id', $visit_id)->where('bill_status', \BluelimeConfiguration::BILL_PENDING)->findColumn('id');
            
            if(!empty($old_pending_bills_id_list)){
                $order_id_list = $orderMainModel->whereIn('bill_id',$old_pending_bills_id_list)->findColumn('id');
                if(empty($order_id_list))
                    $order_id_list = [-1];
                $orderDetailModel->whereIn('order_main_id', $order_id_list)->delete();
                $orderMainModel->whereIn('bill_id', $old_pending_bills_id_list)->delete();
                $billModel->whereIn('id', $old_pending_bills_id_list)->delete();
                $billDetailModel = model('Hospital/BillDetailModel');
                $billDetailModel->whereIn('bill_id', $old_pending_bills_id_list)->delete();
                $notificationModel->where('notification_type', "New Bill")->whereIn('link_id',$old_pending_bills_id_list)->delete();
            }
        }
        
        if(empty($service_list))
            return BluelimeSystem::get_success_ajax_return('No service added');

        $bill_id = BluelimeSystem::create_bill($sex,$date_of_birth,$description,$patient_id, $patient_name, $mobile, $company_id, $company_name, $visit_id, $bill_type,$ordered_by,$registered_by, $registered_by_name, $service_list, $is_external_order, $is_free_service);
        
        /*
        if($bill_id > 0){
            //BluelimeSystem::create_bill_detail($bill_id,$company_id, $service_list);
            $visitModel->where('id',$visit_id)->set('bill_id',$bill_id)->update();
        }
        */

        $notfiy_data = [
            'notification_type'=>  "New Bill",
            'description'=> $patient_name,
            'link_type'=> 'bill_id',
            'link_id'=> $bill_id,
            ];
        $notificationModel->insert($notfiy_data,true);

        if($is_external_order){
            $result = '{"status":"fail","result":"Bill is not created."}';
            if($bill_id > 0)
                $result = '{"status":"success","result":"'.$bill_id.'"}';
            return $result;
        }
        
        $result = '{"status":"success","result":"Savedd"}';

        /*
        $options = $order_extra_info ?? [];
        if(!empty($options))
            $options['date'] = BluelimeSystem::date_now();
        
        $visitModle = model('Hospital/VisitModel');

        $visit_extra = $visitModle->where('id', $visit_id)->select('extra')->findAll();
        $visit_extra = $visit_extra[0]['extra'] ?? [];
        $visit_extra['extra_info'] = $options;
        
        $visit_extra = BluelimeSystem::convert_array_to_string($visit_extra);
        $visitModle->update($visit_id,['extra'=>$visit_extra]);
        */

        return $result;

    }

    public function setreference(){
        $visitModel = model('Hospital/VisitModel');
        $patient_id = $_POST['patient_id'] ?? -1;
        $reference = $_POST['reference'] ?? '';
        $open_visits = $visitModel->get_open_visit($patient_id);

        $result = '{"status":"error", "result":"Update does not made."}';
        if(!empty($open_visits)){
            $extra = $open_visits[0]['extra'];
            
            $extra['reference'] = $reference;
            $extra = BluelimeSystem::convert_array_to_string($extra);
            
            $visitModel->update($open_visits[0]['id'], ['extra' => $extra]);
            $result = '{"status":"success", "result":"Update made."}';
        }
        return $result;
    }

    public function delayservice(){
        $billDetailModel = model('Hospital/BillDetailModel');
        $billModel = model('Hospital/BillModel');

        $data['id'] = $_POST['bill_detail_id'] ?? '';
        $data['service_status'] = $_POST['service_status'] ?? '';

        $bill_detail = $billDetailModel->find($data['id']);
        $bill_id = $bill_detail['bill_id'] ?? -1;
        $bill = $billModel->find($bill_id);
        $bill_status = $bill['bill_status'] ?? 0;
        if($bill_status != BluelimeConfiguration::BILL_PENDING)
            return '';
        
        if($data['service_status']==-1)
            $service_status=0;
        else
            $service_status=-1;
        // BluelimeSystem::show_and_die($service_status);
        $billDetailModel->update($data['id'],['service_status' => $service_status]);
    }
    
    public function getwaitingpatients(){
        $visitModel = model('Hospital/VisitModel');

        $doctor_id = $_POST['doctor_id'] ?? '1';
        
        $result = $visitModel->where('doctor_id', $doctor_id)
                             ->where('visit_status <>', \BluelimeConfiguration::VISIT_STATUS_CLOSED)
                             ->findColumn('COUNT(patient_id)');
        
        return $result[0];
    }

    public function getschedule(){
        sleep(10);
        
        $scheduleModel = model('Hospital/ScheduleModel');
        $appointmentModel = model('Hospital/AppointmentModel');
        $doctor_id = $_POST['doctor_id'] ?? '-1';
        $date = $_POST['date'] ?? '-1';
        
        $re = $scheduleModel->checkdoctorscheduledate($doctor_id,$date);  
        if($re==TRUE){
    
            $result1 = $scheduleModel->checkscheduledate($doctor_id,$date);
            
            if(isset($result1)){
               
                $start_time = strtotime($result1['start_time']);
                $end_time = strtotime($result1['end_time']);
                $total_m =  round(abs($end_time- $start_time) / 60,2);
    
                $per_patient_time = $total_m / $result1['per_patient_time'];
    
                echo '<div id="msg_c"> '.lang('Select Time').' </div>'; 
                        
                        for ($i = 1; $i <= $per_patient_time; $i++) {
    
                            $m_time = $i-1;
                            $time = ($m_time * $result1['per_patient_time']);
                                                
                            $patient_time =date('h:i A', strtotime($result1['start_time'])+$time*60);
                            
                            $reason = $appointmentModel->appointmentreason($doctor_id,$date,$patient_time);
                            //BluelimeSystem::show_and_die($reason);
                            $button_color = $appointmentModel->appointmentchecker($doctor_id,$date,$patient_time);
                            
                            if ($button_color || ((date('Y-m-d',strtotime($date)) == date('Y-m-d')) && strtotime(date("G:i", strtotime($patient_time))) <= strtotime((date("G:i", strtotime(date("G:i", strtotime(date("h:i A"))))))))) {
                                    echo '<button type="button" disable class="btn btn-danger" style="font-size:12px;height:25px;width: 60px;padding: 1px 0px;margin: 1px 1px;text-align: center;"><p ">'.$patient_time.'</p></button>';
                                    } 
                                else {
                                    echo '<button style="font-size:12px;height:25px;width: 60px;padding: 1px 0px;margin: 1px 1px;text-align: center;" id="t_'.$i.'" type="button" class="btn btn-primary" onclick="myBooking('.$i.')">'.$patient_time.'</button>';
                                    }
                        }
    
                echo '<input type="hidden" name="sequence" id="serial_no" value="">';
                echo '<input type="hidden" name="schedule_id" value="' . $result1['id'] . '">';
            }
            else{
                echo'
                <div class="col-md-12">
                    <div class="alert alert-danger">'.'schedule_msg'.'</div>
                </div>';
            }               
    
        }
        else{
            echo'<div class="col-md-12">
            <div class="alert alert-danger">
                '.lang('No Schedule for this Doctor on the selected Date').'
            </div>
        </div>';
        }
    }

    /**
     * Accepts the doctor id and the date, and returns json of schedule of the specific date.
     * @return string
     */
    public function checkifdoctorisavailable()
    {
        $scheduleModel = model('Hospital/ScheduleModel');
        $doctor_id = $_POST['doctor_id'] ?? 1;
        $date = $_POST['date'] ?? date("Y-m-d");

        $day = BluelimeSystem::day_to_no(date('D', strtotime($date)));
        $schedule = $scheduleModel->where('doctor_id', $doctor_id)->where('day', $day)->where('status', 1)->findAll();
        $schedule = $schedule[0] ?? '';
        
        if($schedule != '')
            $schedule = BluelimeSystem::convert_array_to_string($schedule);
        
        return $schedule;
    }

    public function getpatientbymobile()
    {
        $mobile = $_POST['mobile'];
        $patientModel = model('Hospital/PatientModel');

        $mobile = $patientModel->db->escapeString($mobile);

        $result = $patientModel->where('mobile', $mobile)->where('deleted_at IS NULL')->findAll();
        
        if(!empty($result)){
            echo "<label style='color:red;'>This Mobile is Registered for:</label>";
            echo "<table class='table table-hover table-condensed table-bordered'>";
            foreach($result as $rt){
            echo '<tr><td><a href="/hospital/reception/revisit?id='.$rt['id'].'"><i class="fa fa-solid fa-user"></i>&nbsp;'.$rt['first_name'] . ' ' . $rt['middle_name'] . ' ' . $rt['last_name'].'</a></td></tr>';
            }
            echo "</table>";
        }
    }
    public function getpatientbymobileforapp()
    {
        $mobile = $_POST['mobile'];
        $patientModel = model('Hospital/PatientModel');

        $mobile = $patientModel->db->escapeString($mobile);

        $result = $patientModel->where('mobile', $mobile)->where('deleted_at IS NULL')->findAll();
        
        if(!empty($result)){
            echo "<label style='color:red;'>This Mobile is Registered for:</label>";
            echo "<table class='table table-hover table-condensed table-bordered'>";
            foreach($result as $rt){
            echo '<tr><td><a href="newappointment?id='.$rt['id'].'"><i class="fa fa-solid fa-user"></i>&nbsp;'.$rt['first_name'] . ' ' . $rt['middle_name'] . ' ' . $rt['last_name'].'</a></td></tr>';
            }
            echo "</table>";
        }
    }
    
    public function patientactiveappointment()
    {
        $patient_id = $_POST['patient_id'];
        $appointmentModel = model('Hospital/AppointmentModel');
        $result = $appointmentModel->patientappointmentexist($patient_id);
        $new_date_format = date('l d-m-Y', strtotime($result[0]['appointment_date'] ?? ''));
        if(!empty($result)){
            echo 'This patient has active appointment on '.$new_date_format.' at '.$result[0]['time'];
        }
    }

    public function updatepaymentrequeststatus()
    {
        $paymentRequestModel = model('Hospital/PaymentRequestModel');
        $id = $_POST['id'] ?? -1;
        $status = $_POST['status'] ?? null;
        
        if($id == -1)
            return "Payment request id cannot be -1.";
        
        $data = ["status" => $status];
        
        if($paymentRequestModel->update($id, $data))
            return '1';
        return "Failed on updating payment request status.";
    }
    public function checkpin():string
    {
        $userModel = model('System/UserModel');
        $loggeduser = BluelimeAuthentication::get_logged_user_data();
        $id = $loggeduser['id'];
        $pin_code = md5($_POST['pin_code']);
        
        $saved_pin = $userModel->where('id',$id)->findColumn('pin_code');
        //BluelimeSystem::show_and_die($saved_pin);
        if($pin_code==$saved_pin[0]){
            return BluelimeSystem::get_success_ajax_return(1);
        }
        else
            return BluelimeSystem::get_error_ajax_return('Wrong PIN');
    }
    public function searchpatient()
    {
        $searchkey= $_POST['search_key'] ?? "";
        $patientModel = model('Hospital/PatientModel');
        
        $patients = $patientModel->search_patient($searchkey);
        echo '<table class="table table-striped">';
        echo  '<div id="waitingListContent">';
        foreach($patients as $patient)
            echo '<div><tr><td><a href="waitinglist?pid='. $patient['id'] .'">MRN-'.$patient['id'].'</a></td><td>'.$patient['first_name'].' '.$patient['middle_name'].' '.$patient['last_name'].'</td></tr><div>';
        echo '</table>';
    }
    public function gettodatappointments()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
        $userModel = model('System/UserModel');
        $approvedDoctorsReportModel = model('Hospital/ApprovedDoctorsReportModel');

        $today = date('Y-m-d').'%';
        $drs = $userModel->where('role','DOCTOR')->findColumn('id')??[];
        $approved = $approvedDoctorsReportModel->where('created_at like',$today)->findColumn('doctor_id')??[0];
        
        $appointments = $appointmentModel->where('appointment_date like',$today)->whereNotIn('doctor_id',$approved)->whereIn('doctor_id',$drs)->groupStart()->where('appointment_status',BluelimeConfiguration::APPOINTMENT_ACTIVE)->orWhere('appointment_status',BluelimeConfiguration::APPOINTMENT_MISSED)->groupEnd()->orderBy("STR_TO_DATE(time,'%h:%i%p')")->findAll()??[];
        
        $doctors = $appointmentModel->distinct()->where('appointment_date like',$today)->whereNotIn('doctor_id',$approved)->whereIn('doctor_id',$drs)->groupStart()->where('appointment_status',BluelimeConfiguration::APPOINTMENT_ACTIVE)->orWhere('appointment_status',BluelimeConfiguration::APPOINTMENT_MISSED)->groupEnd()->orderBy("STR_TO_DATE(time,'%h:%i%p')")->findColumn('doctor_name')??[];
        //BluelimeSystem::show_and_die($doctors);
        $no_of_appointments = count($appointments)??0;
        
          echo  '<h2>የዛሬ ቀጠሮ ('. $no_of_appointments .' ታካሚዎች)</h2>';
        foreach ($doctors as $doctor) {
        
        echo '<table width="100%" class="today_appointment">
                <tr>
                    <th colspan="3" style="font-size:25px;padding:2px;border-spacing: 0px;">'.$doctor.'</th>
                </tr>
                ';
        foreach($appointments as $appointment)
            if($appointment['doctor_name']==$doctor){
                $sh = "visible";
                        if($_ENV['BRANCH']=="Wollosefer")
                        $sh = "hidden";
            echo '<tr>
                        
                        <td style="font-size:25px;padding:2px;border-spacing: 0px;background-color:'.($appointment['appointment_status'] == BluelimeConfiguration::APPOINTMENT_MISSED?"yellow":"").';color:'.($appointment['appointment_status'] == BluelimeConfiguration::APPOINTMENT_MISSED?"red":"").'"><b>'. implode(' ',array_slice(explode(' ', strtoupper($appointment['full_name']), 4), 0, 1))." ".substr(implode(' ',array_slice(explode(' ', strtoupper($appointment['full_name']), 4), 1, 1)),0,3) .'**</b></td>
                        <td '.$sh.' style="font-size:25px;padding:2px;border-spacing: 0px;background-color:'.($appointment['appointment_status'] == BluelimeConfiguration::APPOINTMENT_MISSED?"yellow":"").';color:'.($appointment['appointment_status'] == BluelimeConfiguration::APPOINTMENT_MISSED?"red":"").'"><b>'.substr($appointment['time'], 0, strpos($appointment['time'], ' ')).'</b></td>
                 </tr>';
            }
        
        echo '</table>';
        echo '</div>';
        }
        echo '<table style="border: 0px"><tr style="border: 0px"><td style="border: 0px">&nbsp;</td><td style="border: 0px">&nbsp;</td></tr></table>';
    } 
    public function gettodaywaitings()
    {
        $waitingListModel = model('Hospital/WaitingListModel');
        $today = date('Y-m-d').'%';

        $waitings = $waitingListModel->where('created_at like',$today)->where('status',BluelimeConfiguration::APPOINTMENT_PENDING)->orderBy('priority_level','DESC')->orderBy('doctor_id')->orderBy('id')->findAll()??[];
        $doctors = $waitingListModel->distinct()->where('created_at like',$today)->where('status',BluelimeConfiguration::APPOINTMENT_PENDING)->findColumn('doctor_name')??[];
       
        $no_of_waitings = count($waitings)??0;

        echo  '<h2>ተጠባባቂ ('. $no_of_waitings .' ታካሚዎች)</h2>';
        echo  '<div id="waitingListContent">';
        
        foreach ($doctors as $doctor) {            
       echo '<table width="100%" class="today_waiting" style="font-size:25px;">
                <tr>
                    <th colspan="4" style="font-size:25px;padding:2px;border-spacing: 0px;">'.$doctor.'</th>
                </tr>
                ';
                
        foreach($waitings as $key => $waiting){
            if($waiting['doctor_name']==$doctor){
            
           
             $doctorName = $waiting["doctor_id"];

                                                        // Assign and increment waiting number per doctor
                                                        if (!isset($doctorQueue[$doctorName])) {
                                                            $doctorQueue[$doctorName] = 1;
                                                        } else {
                                                            $doctorQueue[$doctorName]++;
                                                        }

                                                        // Add the waiting number to the patient entry
                                                        $waitingList[$key]["waiting_number"] = $doctorQueue[$doctorName];

            // Determine priority badge with icon
            $priorityBadge = '';
            if (!empty($waiting['priority_reason'])) {
                $priorityConfig = [
                    'elderly' => ['label' => 'Elderly', 'icon' => 'fa-wheelchair-alt'],
                    'kids_under_3_months' => ['label' => 'Kids < 3M', 'icon' => 'fa-baby'],
                    'emergency' => ['label' => 'Emergency', 'icon' => 'fa-exclamation-circle'],
                ];
                $config = $priorityConfig[$waiting['priority_reason']] ?? ['label' => ucfirst(str_replace('_', ' ', $waiting['priority_reason'])), 'icon' => 'fa-star'];
                $priorityBadge = '<span style="margin-left:10px; padding:3px 7px; background-color:red; color:white; border-radius:3px; font-size:14px;"><i class="fa '.$config['icon'].'" style="margin-right:5px;"></i><b>'.$config['label'].'</b></span>';
            }

            echo '<tr><td style="font-size:25px;padding:2px;border-spacing: 0px;"><b>'.$waitingList[$key]['waiting_number'].'</b></td><td style="font-size:25px;padding:0px;border-spacing: 0px;"><b>'. implode(' ',array_slice(explode(' ', strtoupper($waiting['full_name']), 4), 0, 1)) ." ".substr(implode(' ',array_slice(explode(' ', strtoupper($waiting['full_name']), 4), 1, 1)),0,3) .'**</b></td><td style="padding:2px;">'.$priorityBadge.'</td></tr>';
        }
    }
            echo '</table>';
            echo '</div>';
    }
            echo '<table style="border: 0px"><tr style="border: 0px"><td style="border: 0px">&nbsp;</td><td style="border: 0px">&nbsp;</td></tr></table>';
    } 
    public function getdoctorqueues()
    {
        $visitModel = model('Hospital/VisitModel');
        $visits = $visitModel->whereIn('visit_status',[\BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN,\BluelimeConfiguration::VISIT_STATUS_OPEN])->orderBy('doctor_name asc')->orderBy('queue_no asc')->findAll() ?? [];

        // Exclude visits that have a notification with link_type='visit_id' and status = 1
        $notificationModel = model('Hospital/NotificationModel');
        $seenVisitIds = $notificationModel->where('link_type','visit_id')->where('status',1)->findColumn('link_id') ?? [];
        if(!empty($seenVisitIds)){
            $visits = array_values(array_filter($visits, function($v) use ($seenVisitIds){
                return !in_array($v['id'], $seenVisitIds);
            }));
        }

        // Recompute doctors and counts from filtered visits
        $doctors = array_values(array_unique(array_map(function($v){ return $v['doctor_name']; }, $visits)));
        $no_of_visits = count($visits) ?? 0;

        echo '<h2 style="font-size:28px; margin-bottom: 16px;">ወደ ዶ/ር የተላኩ ታካሚዎች ('. $no_of_visits .' )</h2>';
        echo '<div id="doctorQueueContent">';
        $doctorCounts = [];
        foreach ($visits as $visit) {
            $doctorCounts[$visit['doctor_name']] = ($doctorCounts[$visit['doctor_name']] ?? 0) + 1;
        }
        foreach ($doctors as $doctor) {
            $count = $doctorCounts[$doctor] ?? 0;
            echo '<table width="100%" class="doctor_queue" style="font-size:25px;margin-bottom:20px;">
                    <tr>
                        <th colspan="3" style="font-size:25px;padding:2px;border-spacing: 0px;background:#c68e75;color:#fff;">'.htmlspecialchars($doctor).' ('.$count.' ታካሚዎች)</th>
                    </tr>';
            foreach ($visits as $visit) {
                if ($visit['doctor_name'] == $doctor) {
                    $nameParts = explode(' ', strtoupper($visit['patient_name']), 4);
                    $maskedName = implode(' ', array_slice($nameParts, 0, 1)) . " " . substr(implode(' ', array_slice($nameParts, 1, 1)), 0, 3) . '**';
                    echo '<tr>
                            <td style="font-size:25px;padding:2px;border-spacing: 0px;"><b>'.$visit['queue_no'].'</b></td>
                            <td style="font-size:25px;padding:2px;border-spacing: 0px;"><b>'.$maskedName.'</b></td>
                            <td style="font-size:25px;padding:2px;border-spacing: 0px;">MRN '.sprintf('%05d',$visit['patient_id']).'</td>
                        </tr>';
                }
            }
            echo '</table>';
        }
        echo '</div>';
    }
    public function getscheduledactiveappointments()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
        $today = date('Y-m-d').'%';
        $schedule_id = $_POST['schedule_id'] ?? '-1';
        $appointments = $appointmentModel->where('appointment_date >= ',$today)->where('schedule_id',$schedule_id)->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)->findAll();
        $count = $appointmentModel->where('appointment_date >= ',$today)->where('schedule_id',$schedule_id)->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)->findColumn('count(id)')[0];
        if($count >= 1){
            echo '<tdcolspan="3"><h4 class="alert alert-danger">You have <b>'.$count.'</b> appointments on the selected Schedule date!
            If you continue all appointments will be cancelled</h4><td>';
        }
        else{
            echo '<tdcolspan="3"><h4 class="alert alert-danger">
            Are you sure you want to delete the schedule?</h4><td>';
        }
        echo '<table width="100%"  class="table table-striped">
        
                <tr>
                    <th>Full Name</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>';
        foreach($appointments as $appointment){
            echo '<tr>
                    <td>'. $appointment['full_name'] .'</td>
                    <td>'.date('Y-m-d', strtotime($appointment['appointment_date'])).'</td>
                    <td>'.$appointment['time'].'</td>
                </tr>';
        }
        
        
        echo '<input type="hidden" name="schedule_id" id="schedule_id" value="'.$schedule_id.'"><input type="hidden" name="count" id="count" value="'.$count.'"></table>';
    }
    public function getnotificationappointments()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
        $today = date('Y-m-d').'%';

        $appointments = $appointmentModel->where('appointment_date like ',$today)->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)->findAll();
        $no_of_appointments = count($appointments);
        if($no_of_appointments>0)
        echo '<b class="fa fa- jump_dental" style="color: #ffbb33; font-size: 16px;"><span class="label label-pill label-warning count" style="border-radius:12px;font-size: 10px;">'.$no_of_appointments.'</span></b>';
    }
    public function getnotificationpendingappointments()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
       

        $appointments = $appointmentModel->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_PENDING)->findAll();
        $no_of_appointments = count($appointments);
        if($no_of_appointments>0)
        echo '<b class="fa fa- jump_dental" style="color: #ffbb33; font-size: 16px;"><span class="label label-pill label-warning count" style="border-radius:12px;font-size: 10px;">'.$no_of_appointments.' Pending</span></b>';
    }
    public function getnotificationlabdelivery()
    {
        $labRequestModel = model('Hospital/LabRequestModel');
        $today = date('Y-m-d').'%';

        $labrequest = $labRequestModel->where('delivery_date like ',$today)->where('status',0)->findAll();
        $no_of_delivery = count($labrequest);
        if($no_of_delivery>0)
        echo '<b class="fa fa- jump_dental" style="color: #ffbb33; font-size: 16px;"><span class="label label-pill label-warning count" style="border-radius:12px;font-size: 10px;">'.$no_of_delivery.'</span></b>';
    }
    public function getnotificationcancelledappointments()
    {
        $notificationModel = model('Hospital/NotificationModel');
        $count = $notificationModel->where('status',\BluelimeConfiguration::NOTIFICATION_NEW)->where('notification_type',"Cancelled Appointments")->findColumn('count(id)')[0];
        if($count>0)
        echo '<b class="fa fa- jump_dental" style="color: #ffbb33; font-size: 16px;"><span class="label label-pill label-warning count" style="border-radius:12px;font-size: 10px;">'.$count.'</span></b>';

    }
    public function getnotifyreportapproval()
    {
        $notificationModel = model('Hospital/NotificationModel');
        $count = $notificationModel->where('status',\BluelimeConfiguration::NOTIFICATION_NEW)->where('notification_type',"Daily Doctor Report Approved")->findColumn('description')[0]??0;
        if($count>0)
        echo '<b class="fa fa- jump_dental" style="color: #ffbb33; font-size: 16px;"><span class="label label-pill label-warning count" style="border-radius:12px;font-size: 10px;">'.$count.'</span></b>';

    }
    public function getmynotificationappointments()
    {
        $appointmentModel = model('Hospital/AppointmentModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        
        $today = date('Y-m-d').'%';

        $appointments = $appointmentModel->where('appointment_date like ',$today)->where('doctor_id like ',$loggeduserdata['id'])->where('appointment_status',\BluelimeConfiguration::APPOINTMENT_ACTIVE)->findAll();
        $no_of_appointments = count($appointments);
        if($no_of_appointments>0)
        echo '<b class="fa fa- jump_dental" style="color: #ffbb33; font-size: 16px;"><span class="label label-pill label-warning count" style="border-radius:12px;font-size: 10px;">'.$no_of_appointments.'</span></b>';
    }
    public function getnotificationwaiting()
    {
        $notificationModel = model('Hospital/NotificationModel');
        $visitModel = model('Hospital/VisitModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();

        $visit_ids = $visitModel->where('doctor_id',$loggeduserdata['id'])->where('visit_status',BluelimeConfiguration::VISIT_STATUS_VITAL_TAKEN)->findColumn('id');
        //BluelimeSystem::show_and_die($visit_ids);

        $count = $notificationModel->whereIn('link_id',$visit_ids)->where('link_type','visit_id')->where('user_id',$loggeduserdata['id'])->where('status',BluelimeConfiguration::NOTIFICATION_NEW)->where('notification_type',"Doctors Waiting")->findColumn('count(id)')[0];
        if($count>0)
        echo '<b class="fa fa- jump_dental" style="color: #ffbb33; font-size: 16px;"><span class="label label-pill label-warning count" style="border-radius:12px;font-size: 10px;">'.$count.'</span></b>';

    }
    public function getnotificationwaitingnurse()
        {
            $notificationModel = model('Hospital/NotificationModel');

            $count = $notificationModel->where('status',\BluelimeConfiguration::NOTIFICATION_NEW)->where('notification_type',"Nurse Waiting")->findColumn('count(id)')[0];
            if($count>0)
            echo '<b class="fa fa- jump_dental" style="color: #ffbb33; font-size: 16px;"><span class="label label-pill label-warning count" style="border-radius:12px;font-size: 10px;">'.$count.'</span></b>';

        }
    public function getnotificationwaitinglist()
    {
        $waitingListModel = model('Hospital/WaitingListModel');
        $notificationModel = model('Hospital/NotificationModel');
        $visitModel = model('Hospital/VisitModel');
        $userModel = model('System/UserModel');

        $doctors = $userModel->where('role','DOCTOR')->where('active', 1)->findAll() ?? [];
        $doctorActiveCounts = [];
        $defaultAvgTat = 10;
        $since = date('Y-m-d', strtotime('-30 days')) . ' 00:00:00';
        $doctorAvgTat = [];

        foreach ($doctors as $doc) {
            $docId = $doc['id'];
            $count = $notificationModel->where('user_id', $docId)
                ->where('link_type', 'visit_id')
                ->where('notification_type', 'Doctors Waiting')
                ->where('status', BluelimeConfiguration::NOTIFICATION_NEW)
                ->findColumn('count(id)');
            $doctorActiveCounts[$docId] = intval($count[0] ?? 0);

            $avgRow = $visitModel->join('hospital_visit_encounter', 'hospital_visit_encounter.visit_id = hospital_visits.id', 'inner')
                ->select("AVG(TIMESTAMPDIFF(MINUTE, hospital_visit_encounter.created_at, hospital_visit_encounter.updated_at)) as avg_minutes")
                ->where('hospital_visits.doctor_id', $docId)
                ->where('hospital_visits.visit_status', BluelimeConfiguration::VISIT_STATUS_CLOSED)
                ->where('hospital_visit_encounter.updated_at >=', $since)
                ->first();
            $avg_minutes = $avgRow['avg_minutes'] ?? null;
            $doctorAvgTat[$docId] = $avg_minutes !== null ? round((float)$avg_minutes, 1) : null;
        }

        $waitingList = $waitingListModel->where('status', 0)
            ->orderBy('priority_level', 'DESC')
            ->orderBy('doctor_id')
            ->orderBy('id')
            ->findAll() ?? [];

        $doctorQueue = [];
        $overdueCount = 0;

        foreach ($waitingList as $patientData) {
            $doctorId = $patientData['doctor_id'];
            if (!isset($doctorQueue[$doctorId])) {
                $doctorQueue[$doctorId] = 1;
            } else {
                $doctorQueue[$doctorId]++;
            }

            $waitingNumber = $doctorQueue[$doctorId];
            $patientsBefore = max(0, $waitingNumber - 1);
            $activeCountForDoc = $doctorActiveCounts[$doctorId] ?? 0;
            $beforeYouTotal = $activeCountForDoc + $patientsBefore;
            $avg = isset($doctorAvgTat[$doctorId]) && is_numeric($doctorAvgTat[$doctorId]) ? (float)$doctorAvgTat[$doctorId] : $defaultAvgTat;
            $estimatedWaitMinutes = round($beforeYouTotal * $avg);

            $createdAt = isset($patientData['created_at']) ? strtotime($patientData['created_at']) : null;
            if ($createdAt !== null) {
                $elapsedMinutes = max(0, round((time() - $createdAt) / 60));
                if ($estimatedWaitMinutes > 0 && $elapsedMinutes > $estimatedWaitMinutes) {
                    $overdueCount++;
                }
            }
        }

        if ($overdueCount > 0) {
            echo '<b class="fa fa- jump_dental" style="color: #ffbb33; font-size: 16px;"><span class="label label-pill label-danger count" style="border-radius:12px;font-size: 10px;">'.$overdueCount.'</span></b>';
        }
    }
    public function getnotificationbills()
    {
        $notificationModel = model('Hospital/NotificationModel');

        $count = $notificationModel->where('status',\BluelimeConfiguration::NOTIFICATION_NEW)->where('notification_type',"New Bill")->findColumn('count(id)')[0];
        if($count>0)
        echo '<b class="fa fa- jump_dental" style="color: #ffbb33; font-size: 16px;"><span class="label label-pill label-warning count" style="border-radius:12px;font-size: 10px;">'.$count.'</span></b>';


    }
    public function getnotificationplans()
    {
        $notificationModel = model('Hospital/NotificationModel');

        $count = $notificationModel->where('status',\BluelimeConfiguration::NOTIFICATION_NEW)->where('link_id <>',0)->where('notification_type',"New plan")->findColumn('count(id)')[0];
        if($count>0)
        echo '<b class="fa fa- jump_dental" style="color: #ffbb33; font-size: 16px;"><span class="label label-pill label-warning count" style="border-radius:12px;font-size: 10px;">'.$count.'</span></b>';

    }
    public function getnotificationprints()
    {
        $notificationModel = model('Hospital/NotificationModel');
        $prints = array('Prescriptions','Medical Certificates','Lab Requests','Laboratory');
        $count = $notificationModel->where('status',\BluelimeConfiguration::NOTIFICATION_NEW)->whereIn('notification_type',$prints)->findColumn('count(id)')[0];
        if($count>0)
        echo '<b class="fa fa- jump_dental" style="color: #ffbb33; font-size: 16px;"><span class="label label-pill label-warning count" style="border-radius:12px;font-size: 10px;">'.$count.'</span></b>';
    }
    public function getnotificationbiopsy()
    {
        $notificationModel = model('Hospital/NotificationModel');
        $prints = array('Pathology');
        $count = $notificationModel->where('status',\BluelimeConfiguration::NOTIFICATION_NEW)->whereIn('notification_type',$prints)->findColumn('count(id)')[0];
        if($count>0)
        echo '<b class="fa fa- jump_dental" style="color: #ffbb33; font-size: 16px;"><span class="label label-pill label-warning count" style="border-radius:12px;font-size: 10px;">'.$count.'</span></b>';
    }
    public function getnotificationlabresult()
    {
        $notificationModel = model('Hospital/NotificationModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $prints = array('Laboratory');
        $new_result = $notificationModel->where('status',\BluelimeConfiguration::NOTIFICATION_NEW)->where('user_id', $loggeduserdata['id'])->whereIn('notification_type',$prints)->orderBy('notification_date ASC')->findAll(1)[0]??[];
       
        if (!empty($new_result)){        
            echo '<table class="table table striped"><tr><td  style="text-align:center;"><h3>'.$new_result['description'].'</h3></td><td><h3><button onclick="viewlabnotification('.$new_result['id'].')" type="button" class="btn btn-success" id="ok" data-dismiss="modal" >OK</button><h3></td>';
            echo '<tr>Result Time: '.$new_result['notification_date'].'</table>';
        }
        
    }
    public function getnotificationnurseresult()
    {
        $notificationModel = model('Hospital/NotificationModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        
        $new_result = $notificationModel->where('status',\BluelimeConfiguration::NOTIFICATION_NEW)->where('user_id', $loggeduserdata['id'])->where('notification_type','Nurse Procedure Result')->orderBy('notification_date ASC')->findAll(1)[0]??[];
       
        if (!empty($new_result)){        
            echo '<table class="table table striped"><tr><td  style="text-align:center;"><h3>'.$new_result['description'].'</h3></td><td><h3><button onclick="viewnursenotification('.$new_result['id'].')" type="button" class="btn btn-success" id="ok" data-dismiss="modal" >OK</button><h3></td>';
            echo '<tr>Result Time: '.$new_result['notification_date'].'</table>';
        }
        
    }
    public function updatenotifylabresult()
    {
        $notificationModel = model('Hospital/NotificationModel');
        
        $notification_id = $_POST['notification_id'];
        $data = ['status'=> 1];
        $notificationModel->update($notification_id,$data);
    }
    public function updatenotifynurseresult()
    {
        $notificationModel = model('Hospital/NotificationModel');
        
        $notification_id = $_POST['notification_id'];
        $data = ['status'=> 1];
        $notificationModel->update($notification_id,$data);
    }

    

    public function saveadjustment(){
        $payment_id = $_POST['payment_id'] ?? 0;
        $adjustment_value = $_POST['adjustment_value'] ?? 0;
        $date = BluelimeSystem::date_now();

        $logged_user = BluelimeAuthentication::get_logged_user_data();

        $billPaymentModel = model('Hospital/BillPaymentModel');
        $billModel = model('Hospital/BillModel');

        $billPayment = $billPaymentModel->find($payment_id);

        $adjustment_data = [
            'bill_id' => $billPayment['bill_id'],
            'payed_date' => $date,
            'payed_amount' => $adjustment_value,
            'payed_discount' => 0,
            'registered_by' => $logged_user['id'],
            'registered_by_name' => $logged_user['name'],
            'tin' => $billPayment['tin'],
            'fs_number' => $billPayment['fs_number'],
            'patient_name' => $billPayment['patient_name'],
            'patient_id' => $billPayment['patient_id'],
            'company_id' => $billPayment['company_id'],
            'company_name' => $billPayment['company_name'],
            'payment_type' => 'ADJUSTMENT',
            'mobile' => $billPayment['mobile'],
            'due' => $billPayment['due']
        ];
        
        $new_id = $billPaymentModel->insert($adjustment_data);

        $due = $billPayment['due'] - $adjustment_value;

        $result = '{"status":"error", "result":"Update does not made."}';
        
        if($new_id != false){
            $billPaymentModel->update($payment_id,['due' => $due]);
            $result = '{"status":"success", "result":"Update made."}';
        }
        $billModel->update($billPayment['bill_id'],['bill_status' => \BluelimeConfiguration::BILL_OPEN]);

        return $result;
    }

    public function get_prescription_auto_fill_list(){
        $item_id = $_POST['item_id'] ?? -1;
        $loggeduser = BluelimeAuthentication::get_logged_user_data();
        $prescriptionAutoFillModel = model('Hospital/PrescriptionAutoFillModel');

        $prescriptionAutoFillList = $prescriptionAutoFillModel->where('drug_id',$item_id)->where('registered_by',$loggeduser['id'])->findAll();

        $jsonValue = json_encode($prescriptionAutoFillList);

        return $jsonValue;
    }

    public function add_prescription_auto_fill(){
        $drug_id = $_POST['item_id'] ?? '';
        $drug_name = $_POST['item_name'] ?? '';
        $age_range = $_POST['age_range'] ?? '';
        $remark = $_POST['remark'] ?? '';
        $route = $_POST['route'] ?? '';
        $direction = $_POST['direction'] ?? '';
        $nod = $_POST['nod'] ?? '';

        $age_range = explode('-', $age_range,2);
        $age_from = intval($age_range[0]?? "0");
        $age_to = intval($age_range[1]?? "0");

        if($age_from > $age_to){
            $temp = $age_from;
            $age_from = $age_to;
            $age_to = $temp;
        }

        $loggeduser = BluelimeAuthentication::get_logged_user_data();

        $data = [
            "drug_id" => $drug_id,
            "drug_name" => $drug_name,
            "age_from" => $age_from,
            "age_to" => $age_to,
            "remark" => $remark,
            "route" => $route,
            "direction" => $direction,
            "nod" => $nod,
            "registered_by" => $loggeduser['id']
        ];

        $prescriptionAutoFillModel = model('Hospital/PrescriptionAutoFillModel');

        $prescriptionAutoFillModel->insert($data);

        $result = '{"status":"success","result":"saved"}';

        return $result;
    }

    public function remove_prescription_auto_fill(){
        $paf_id = $_GET['paf_id'] ?? '-1';

        $prescriptionAutoFillModel = model('Hospital/PrescriptionAutoFillModel');

        $result = $prescriptionAutoFillModel->delete($paf_id);

        if($result)
            $result = '{"status":"success","result":"Deleted"}';
        else 
            $result = '{"status":"error","result":"Not deleted"}';
            
        return $result;
    }

    public function get_prescription_suggestion(){
        $item_id = $_POST['item_id'] ?? -1;
        $age = $_POST['patient_age'] ?? 0;
        $loggeduser = BluelimeAuthentication::get_logged_user_data();
         
        $prescriptionAutoFillModel = model('Hospital/PrescriptionAutoFillModel');

        $result = $prescriptionAutoFillModel->where('age_from <=' , $age)->where('age_to >=', $age)->where('drug_id',$item_id)->where('registered_by',$loggeduser['id'])->orderBy('id','DESC')->findAll();
        $result = json_encode($result);

        return $result;
    }
}
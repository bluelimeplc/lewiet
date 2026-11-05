<?php

namespace App\Controllers\API\API2\Finance;

use App\ThirdParty\Bluelime\SMS\BluelimeSMS;
use BluelimeSystem;
use BluelimeConfiguration;

class V1 extends \App\Controllers\BaseController
{
    public function index(){
        $response = [
            'message' => 'Welcome to finance v1 api.',
            'status' => 'success',
            'data' => []
        ];
        return $this->response->setJSON($response);
    }

    public function a(){
        $response = [
            'message' => 'Welcome to finance v1 api.',
            'status' => 'success',
            'data' => []
        ];
        return $this->response->setJSON($response);
    }

    
    public function get_credit_payment_receipt_group(){
        $api_key = $_GET['api_key'] ?? '';
        $paymentRequestModel = model('Hospital/PaymentRequestModel');
        $payment_receipt_groups = $paymentRequestModel->select('id, department, company_id, company_name, bill_id_list, total_amount, from_date, to_date, created_at')->where('status', '')->findAll();
        foreach($payment_receipt_groups as $index => $row){
            $payment_receipt_groups[$index]['number_of_receipts'] = count(BluelimeSystem::convert_to_array($row['bill_id_list']));
            unset($payment_receipt_groups[$index]['bill_id_list']);
        }
        $signature = BluelimeSystem::create_jwt_signature($payment_receipt_groups, $api_key);

        $response = ['status'=>'success','data'=>$payment_receipt_groups, 'signature'=>$signature];
        return $this->response->setJSON($response);
    }

    public function get_credit_payment_invoice_data(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);

        $api_key = $_GET['api_key'] ?? '';
        $paymentRequestModel = model('Hospital/PaymentRequestModel');
        $credit_receipt_group_id_list = $POST_VAL['credit_receipt_group_id_list'] ?? [];
        //$credit_receipt_group_id_list = BluelimeSystem::convert_to_array($credit_receipt_group_id_list);
        
        
        if(empty($credit_receipt_group_id_list)){
            return $this->response->setJSON(['status' => 'error', 'message' => 'No receipt group ID provided']);
        }

        $payment_requests = $paymentRequestModel->whereIn('id', $credit_receipt_group_id_list)->findAll();

        $clinic_receipt_id_list = [];
        $pharmacy_receipt_id_list = [];
        foreach($payment_requests as $payment_request){
            $bill_id_list = BluelimeSystem::convert_to_array($payment_request['bill_id_list']);
            if(empty($bill_id_list)){
                continue;
            }

            if($payment_request['department'] == 'Clinic'){
                $clinic_receipt_id_list = array_merge($clinic_receipt_id_list, $bill_id_list);
            } elseif($payment_request['department'] == 'Pharmacy'){
                $pharmacy_receipt_id_list = array_merge($pharmacy_receipt_id_list, $bill_id_list);
            }
        }
        $clinic_receipt_id_list = array_unique($clinic_receipt_id_list);
        $pharmacy_receipt_id_list = array_unique($pharmacy_receipt_id_list);

        $clinic_receipt_data = $this->get_credit_sales_detail_data_clinic($clinic_receipt_id_list);
        $pharmacy_receipt_data = $this->get_credit_sales_detail_data_pharmacy($pharmacy_receipt_id_list);

        $all_receipt_data = [...$clinic_receipt_data, ...$pharmacy_receipt_data];
        $signature = BluelimeSystem::create_jwt_signature($all_receipt_data, $api_key);

        //return $this->response->setJSON(['data'=>$all_receipt_data]);
        return $this->response->setJSON(['status'=>'success', 'data'=>$all_receipt_data, 'signature'=>$signature]);
    }

    private function get_credit_sales_detail_data_clinic(array $bill_id_list): array{
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $serviceModel = model('Hospital/ServiceModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        
        $finance_maintain = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_integration = $finance_maintain[0]['data'];


        $services = $serviceModel->findAll();
        $services = BluelimeSystem::index_object_list_array_by_unique_field($services, 'id');

        if(empty($bill_id_list))
            $bill_id_list = [0];
        
        $bills = $billModel->whereIn('id',$bill_id_list)->orderBy('payed_date', 'ASC')->findAll();//->findAllWithDetail_GroupedByType();
        $bill_details = $billDetailModel->whereIn('bill_id',$bill_id_list)->where('service_status !=', -1)->findAll();

        $receipt_list = [];

        foreach($bills as $bill){
            $fs_index = 'R'.$peachtree_integration['branch']['number'].'_'.$bill['fs_number'];
            if(!isset($receipt_list[$fs_index])){
                $receipt_list[$fs_index] = [
                    'date' => $bill['payed_date'],
                    'fs_number' => $bill['fs_number'],
                    'patient_name' => $bill['patient_name'],
                    'department' => 'Clinic',
                    'sales_detail' => []
                ];
            }

            $bill_details_temp = array_filter($bill_details, function($detail) use ($bill){
                return $detail['bill_id'] == $bill['id'];
            });

            foreach($bill_details_temp as $sales){
                $receipt_list[$fs_index]['sales_detail'][] = [
                    'peachtree_id' => '',
                    'description' => $sales['service_name'],
                    'gl_account' => $services['id_'.$sales['service_id']]['gl_account'],
                    'quantity' => $sales['bill_quantity'],
                    'unit_price' => round($sales['bill_amount'],2),
                    'vat' => 0, // Assuming no VAT for clinic services
                    'item_type' => $sales['service_type'],
                ];
            }
        }
        
        return $receipt_list;
    }

    private function get_credit_sales_detail_data_pharmacy(array $bill_id_list): array{
        $pOSBillModel = model('POS/POSBillModel');
        $itemModel = model('Inventory/ItemModel');
        $peachtreeItemModel = model('Inventory/PeachtreeItemModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $bill_id_list = [...$bill_id_list, -99];
        $pos_bills = $pOSBillModel->whereIn('id',$bill_id_list)->findAll();

        $receipt_list = [];
        if(!empty($pos_bills)){
            $item_id_list = [-99];
            foreach($pos_bills as $pos_bill){
                foreach($pos_bill['pos_bill_json']['sales_item_list'] as $sales){
                    $item_id_list[] = intval($sales['item_id']);
                }
            }
            $item_id_list = array_unique($item_id_list);
            $items = $itemModel->whereIn('id', $item_id_list)->findAll();
            $peachtree_items = $peachtreeItemModel->whereIn('item_id', $item_id_list)->findAll();
            $finance_maintain = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
            $peachtree_integration = $finance_maintain[0]['data'];

            $items = BluelimeSystem::index_object_list_array_by_unique_field($items, 'id');
            $peachtree_items = BluelimeSystem::index_object_list_array_by_unique_field($peachtree_items, 'item_id');

            foreach($pos_bills as $pos_bill){
                $fs_index = $peachtree_integration['code_suffix']['dispensary'].'_'.$pos_bill['fs_number'];
                foreach($pos_bill['pos_bill_json']['sales_item_list'] as $sales){
                    if(!isset($receipt_list[$fs_index])){
                        $receipt_list[$fs_index] = [
                            'date' => $pos_bill['response_date'],
                            'fs_number' => $pos_bill['fs_number'],
                            'patient_name' => $pos_bill['pos_bill_json']['customer_name'],
                            'department' => 'Pharmacy',
                            'sales_detail' => []
                        ];
                    }

                    $gl_account = $peachtree_integration['gl_account']['sales']['drug'];
                    $item_id = intval($sales['item_id']);
                    $item = $items['id_'.$sales['item_id']] ?? [];
                    $description = $sales['item_name'];
                    $item_type = $sales['item_type'];

                    if($item_id == 0){ //Compounding item
                        if($sales['vat'] == 0){
                            $gl_account = $peachtree_integration['gl_account']['compounding_sales']['drug'];
                            $item_type = 'Compounding';
                            $description = 'Compounding Sales';
                        }
                        else{
                            $gl_account = $peachtree_integration['gl_account']['compounding_sales']['cosmetics'];
                            $item_type = 'Compounding Taxable';
                            $description = 'Compounding Taxable Sales';
                        }
                    } else {
                        if($sales['item_type'] != 'DRUG')
                            $gl_account = $peachtree_integration['gl_account']['sales']['cosmetics'];
                    }

                    $gl_account .= '-'.$peachtree_integration['code_suffix']['dispensary'];

                    $peachtree_id = $peachtree_items['item_id_'.$sales['item_id']]['peachtree_id'] ?? '';
                    if(!empty($peachtree_id))
                        $peachtree_id .= '_'. $peachtree_integration['code_suffix']['dispensary'];


                    $receipt_list[$fs_index]['sales_detail'][] = [
                        'peachtree_id' => $peachtree_id,
                        'description' => $description,
                        'gl_account' => $gl_account,
                        'quantity' => $sales['quantity'],
                        'unit_price' => round($sales['unit_price'],2),
                        'vat' => $sales['vat']?1:0,
                        'measurment' => $sales['measurment'],
                        'item_type' => $item_type,
                    ];
                }
            }
        }

        return $receipt_list;
    }

    public function update_receipt_group_status(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);

        $api_key = $_GET['api_key'] ?? '';
        $paymentRequestModel = model('Hospital/PaymentRequestModel');
        $credit_receipt_group_id_list = $POST_VAL['credit_receipt_group_id_list'] ?? [];
        $status = $POST_VAL['status'] ?? null;
        //$credit_receipt_group_id_list = BluelimeSystem::convert_to_array($credit_receipt_group_id_list);
        
        if(empty($credit_receipt_group_id_list)){
            return $this->response->setJSON(['status' => 'error', 'message' => 'No receipt group ID provided']);
        }

        $paymentRequestModel->whereIn('id', $credit_receipt_group_id_list)->update(row: ['status'=>$status]);

        $result = ['updated'];
        $signature = BluelimeSystem::create_jwt_signature($result, $api_key);
        return $this->response->setJSON(['status'=>'success', 'data'=>$result, 'signature'=>$signature]);
    }

    public function get_sales_report_data(){
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);

        $api_key = $_GET['api_key'] ?? '';
        $from_date = $POST_VAL['from_date'] ?? '';
        $to_date = $POST_VAL['to_date'] ?? '';
        
        $pOSBillModel = model('POS/POSBillModel');
        $sales_list = $pOSBillModel->select('SUM(total) as total, type, MONTH(response_date) AS month, 0 AS last_year_total')->where('response_date >=', $from_date)->where('response_date <=', $to_date)->groupBy('type, MONTH(response_date)')->findAll();
        
        $from_date_array = explode('-', $from_date);
        $from_date_array[0] = intval($from_date_array[0])-1;
        $last_year_from_date = implode('-', $from_date_array);

        $to_date_array = explode('-', $to_date);
        $to_date_array[0] = intval($to_date_array[0])-1;
        $to_date_array[2] = date('t', strtotime($to_date_array[0].'-'.$to_date_array[1].'-01'));
        $last_year_to_date = implode('-', $to_date_array);

        $last_year_sales_list = $pOSBillModel->select('SUM(total) as total, type, MONTH(response_date) as month')->where('response_date >=', $last_year_from_date)->where('response_date <=', $last_year_to_date)->groupBy('type, MONTH(response_date)')->findAll();
       
        foreach($last_year_sales_list as $index => $row){
            $found = false; 
            foreach($sales_list as $index2 => $row2){
                if(($row2['month'] == $row['month']) && ($row2['type'] == $row['type'])){
                    $sales_list[$index2]['last_year_total'] = $row['total'];
                    $found = true;
                    break;
                }
            }

            if(!$found){
                $sales_list[] = [
                    'total' => 0,
                    'type' => $row['type'],
                    'month' => $row['month'],
                    'last_year_total' => $row['total']
                ];
                // $sales_list[$index]['total'] = 0;
                // $sales_list[$index]['type'] = $row['type'];
                // $sales_list[$index]['month'] = $row['month'];
                // $sales_list[$index]['last_year_total'] = $row['total'];
            }
        }

        $result = ['report_data'=>$sales_list];
        $signature = BluelimeSystem::create_jwt_signature($result, $api_key);
        return $this->response->setJSON(['status'=>'success', 'data'=>$result, 'signature'=>$signature]);
    }

    public function get_fiscal_year_sales_report_data(){
        $api_key = $_GET['api_key'] ?? '';
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);

        $ec_year = $POST_VAL['ec_fiscal_year'] ?? intval(date('Y'))-7;
        $period_list = BluelimeSystem::get_fiscal_period_list($ec_year);
        $fiscal_year_date_range = BluelimeSystem::get_fiscal_year_range($period_list["p2"]['from']); //First date of second period. Just to get sample date in the fiscal year
        $clinic_sales = $this->get_clinic_sales_by_period($fiscal_year_date_range[0], $fiscal_year_date_range[1],$period_list);
        $pharmacy_sales = $this->get_pharmacy_sales_by_period($fiscal_year_date_range[0], $fiscal_year_date_range[1],$period_list);
        $fiscal_year_sales = $clinic_sales;
        foreach($pharmacy_sales as $period => $sales){
            if(!isset($sales['sales']))
                continue;

            $fiscal_year_sales[$period]['sales'] = [...$fiscal_year_sales[$period]['sales'] ?? [], ...$sales['sales']];
        }
        
        $signature = BluelimeSystem::create_jwt_signature($fiscal_year_sales, $api_key);
        return $this->response->setJSON(['status'=>'success', 'data'=>$fiscal_year_sales, 'signature'=>$signature]);
    }

    public function get_date_range_sales_report_data(){
        $api_key = $_GET['api_key'] ?? '';
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);

        $ec_year = $POST_VAL['ec_fiscal_year'] ?? intval(date('Y'))-7;
        $from = $POST_VAL['from'];
        $to   = $POST_VAL['to'];

        $period_list = BluelimeSystem::get_fiscal_period_list($ec_year);
        //$fiscal_year_date_range = BluelimeSystem::get_fiscal_year_range($period_list["p2"]['from']); //First date of second period. Just to get sample date in the fiscal year
        $clinic_sales = $this->get_clinic_sales_by_period($from, $to,$period_list);
        $pharmacy_sales = $this->get_pharmacy_sales_by_period($from, $to,$period_list);
        $fiscal_year_sales = $clinic_sales;
        foreach($pharmacy_sales as $period => $sales){
            if(!isset($sales['sales']))
                continue;

            $fiscal_year_sales[$period]['sales'] = [...$fiscal_year_sales[$period]['sales'] ?? [], ...$sales['sales']];
        }
        
        $signature = BluelimeSystem::create_jwt_signature($fiscal_year_sales, $api_key);
        return $this->response->setJSON(['status'=>'success', 'data'=>$fiscal_year_sales, 'signature'=>$signature]);
    }

    private function get_clinic_sales_by_period($from, $to, $period_list){
        $serviceModel = model('Hospital/ServiceModel');
        $services = $serviceModel->findAll();
        $services = BluelimeSystem::index_object_list_array_by_unique_field($services,'id');

        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $from = substr($from, 0, 10) . ' 00:00:00';
        $to = substr($to, 0, 10) . ' 23:59:59';

        $bills = $billModel->where('payed_date >=', $from)->where('payed_date <=', $to)->where('bill_status', BluelimeConfiguration::BILL_RECIEVED)->findAll();
        $bills_id_list = BluelimeSystem::collect_field_value_from_object($bills, 'id');
        //sort($bills_id_list);
        $bills_id_list = [...($bills_id_list??[]),-99];
        $bill_details = $billDetailModel->whereIn('bill_id',$bills_id_list)->where('service_status !=',BluelimeConfiguration::SERVICE_STATUS_DELAY)->findAll();

        $bills = BluelimeSystem::index_object_list_array_by_unique_field($bills, 'id');

        $sales_by_period = $period_list;
        foreach($bill_details as $row_index => $service_sales){
            $gl_account = $services['id_'.$service_sales['service_id']]['gl_account']??'ERR-NO_GL';
            $paid_date = $bills['id_'.$service_sales['bill_id']]['payed_date'];
            $period = $this->get_period($paid_date, $period_list);
            if(!isset($sales_by_period[$period]['sales']))
                $sales_by_period[$period]['sales'] = [];
            if(!isset($sales_by_period[$period]['sales'][$gl_account]))
                $sales_by_period[$period]['sales'][$gl_account] = ['gl_account'=>$gl_account, 'name'=>$service_sales['service_name'], 'amount'=>0, 'credit'=>0, 'cash'=>0];
            
            $amount = $service_sales['bill_quantity'] * $service_sales['bill_amount'];
            $sales_by_period[$period]['sales'][$gl_account]['amount'] += $amount;
            
            if(strtoupper($bills['id_'.$service_sales['bill_id']]['bill_type']) == 'CREDIT')
                $sales_by_period[$period]['sales'][$gl_account]['credit'] += $amount;
            else
                $sales_by_period[$period]['sales'][$gl_account]['cash'] += $amount;
           
        }
        return $sales_by_period;
    }

    private function get_pharmacy_sales_by_period($from, $to, $period_list){
        $pOSBillModel = model("POS/POSBillModel");
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $department = 'Inventory';

        $financeMaintains = $financeMaintainModel->where('department',$department)->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtreeIntegration = $financeMaintains[0]['data'];
        $vat_gl_account = $peachtreeIntegration['gl_account']['vat'];

        $pOSBillModel = $pOSBillModel->where('response_date >=', $from)->where('response_date <=', $to)->where("type", $department);
        $pos_bills = $pOSBillModel->findAll();

        $sales_by_period = $period_list;
        //$csv_data = "period\tfs_number\titem_id\tgl_account\tamount\tpaid_date\n";
        foreach($pos_bills as $row){
            $sales_item_list = $row['pos_bill_json']['sales_item_list'];
            
            foreach($sales_item_list as $sales){
                $paid_date = $row['response_date'];
                $period = $this->get_period($paid_date, $period_list);
                $item_id = intval($sales['item_id']);
                $gl_account = '';
                $name = '';

                if($item_id == 0){
                    $gl_account = $sales['vat'] == 1 ? $peachtreeIntegration['gl_account']['compounding_sales']['cosmetics'] : $peachtreeIntegration['gl_account']['compounding_sales']['drug'];   
                    $gl_account .= '-'.$peachtreeIntegration['code_suffix']['dispensary'];
                    if($sales['vat'] == 0 ){
                        $name = 'Compounding sales';
                    }
                    else{
                        $name = 'Compounding taxable';
                        
                        if(!isset($sales_by_period[$period]['sales'][$vat_gl_account])){
                            $sales_by_period[$period]['sales'][$vat_gl_account] = ['gl_account'=>$vat_gl_account, 'name'=>'VAT', 'amount'=>0, 'cash'=>0, 'credit'=>0];
                        }
                        $vat = $sales['unit_price'] * $sales['quantity'] * BluelimeConfiguration::VAT;
                        $sales_by_period[$period]['sales'][$vat_gl_account]['amount'] += $vat;
                        if(strtoupper($row['payment_method']) == 'CREDIT')
                            $sales_by_period[$period]['sales'][$vat_gl_account]['credit'] += $vat;
                        else
                            $sales_by_period[$period]['sales'][$vat_gl_account]['cash'] += $vat;
                    }
                }
                else{
                    $gl_account = $sales['vat'] == 1 ? $peachtreeIntegration['gl_account']['sales']['cosmetics'] : $peachtreeIntegration['gl_account']['sales']['drug'];   
                    $gl_account .= '-'.$peachtreeIntegration['code_suffix']['dispensary'];
                    if($sales['vat'] == 0 ){
                        $name = 'Pharmaceutical sales';
                    }
                    else{
                        $name = 'Cosmotics sales';
                        if(!isset($sales_by_period[$period]['sales'][$vat_gl_account])){
                            $sales_by_period[$period]['sales'][$vat_gl_account] = ['gl_account'=>$vat_gl_account, 'name'=>'VAT', 'amount'=>0, 'cash'=>0, 'credit'=>0];
                        }
                        
                        $vat = $sales['unit_price'] * $sales['quantity'] * BluelimeConfiguration::VAT;
                        $sales_by_period[$period]['sales'][$vat_gl_account]['amount'] += $vat;

                        if(strtoupper($row['payment_method']) == 'CREDIT')
                            $sales_by_period[$period]['sales'][$vat_gl_account]['credit'] += $vat;
                        else
                            $sales_by_period[$period]['sales'][$vat_gl_account]['cash'] += $vat;
                    }
                }

                if(!isset($sales_by_period[$period]['sales'][$gl_account]))
                    $sales_by_period[$period]['sales'][$gl_account] = ['gl_account'=>$gl_account, 'name'=>$name, 'amount'=>0, 'credit'=>0,'cash'=>0];

                $amount = $sales['unit_price'] * $sales['quantity'];
                $sales_by_period[$period]['sales'][$gl_account]['amount'] += $amount;
                
                if(strtoupper($row['payment_method']) == 'CREDIT')
                    $sales_by_period[$period]['sales'][$gl_account]['credit'] += $amount;
                else
                    $sales_by_period[$period]['sales'][$gl_account]['cash'] += $amount;
                //$csv_data .= $period."\t".$row['fs_number']."\t".$sales['item_id']."\t".$gl_account."\t".($sales['unit_price'] * $sales['quantity'])."\t".$paid_date."\n";
            }
        }

        //BluelimeSystem::write_log('finance_api', 'fiscal_year_sales_report_v3.csv', $csv_data);

        return $sales_by_period;
    }

    private function get_period($date, $period_list){
        foreach($period_list as $period => $date_range){
            if((strcmp(substr($date,0,10), substr($date_range['from'],0,10))>=0) && (strcmp(substr($date,0,10),substr($date_range['to'],0,10))<=0))
                return $period;
        }
        return "p0";
    }

    public function get_sales_detail_report_data(){
        $api_key = $_GET['api_key'] ?? '';
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);

        $from_date = $POST_VAL['from_date'] ?? '1990-01-01';
        $to_date = $POST_VAL['to_date'] ?? '1990-01-01';
        $gl_account = $POST_VAL['gl_account'] ?? '';

        if(empty($gl_account)){
            return $this->response->setJSON(['status'=>'error', 'message'=>'GL account is required']);
        }

        $sales_detail = [];

        if(substr($gl_account, -3,2) == 'CR'){
            $sales_detail = $this->get_clinic_sales_detail($from_date, $to_date, $gl_account);
        }
        else{
            $sales_detail = $this->get_pharmacy_sales_detail($from_date, $to_date, $gl_account);
        }

        $signature = BluelimeSystem::create_jwt_signature($sales_detail, $api_key);

        return $this->response->setJSON(['status'=>'success', 'data'=>$sales_detail, 'signature'=>$signature]);
    }

    private function get_clinic_sales_detail($from, $to, $gl_account){
        $serviceModel = model('Hospital/ServiceModel');
        $service = $serviceModel->where('gl_account', $gl_account)->findColumn('id');
        $service_id = [...$service, -99];
        
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
       
        $bills = $billModel->where('payed_date >=', $from)->where('payed_date <=', $to)->where('bill_status', BluelimeConfiguration::BILL_RECIEVED)->where('pos_bill_id >',0)->findAll();
        $bills_id_list = BluelimeSystem::collect_field_value_from_object($bills, 'id');
        //sort($bills_id_list);
        $bills_id_list = [...($bills_id_list??[]),-99];
        $bill_details = $billDetailModel->whereIn('bill_id',$bills_id_list)->whereIn('service_id', $service_id)->where('service_status !=',BluelimeConfiguration::SERVICE_STATUS_DELAY)->findAll();

        $bills = BluelimeSystem::index_object_list_array_by_unique_field($bills, 'id');

        $sales_detail = [];
        foreach($bill_details as $row_index => $service_sales){
            $sales_detail[] = [
                'description' => $service_sales['service_name'],
                'quantity' => $service_sales['bill_quantity'],
                'unit_price' => round($service_sales['bill_amount'],2),
                'amount' => round($service_sales['bill_quantity'] * $service_sales['bill_amount'],2),
                'pos_bill_id' => $bills['id_'.$service_sales['bill_id']]['pos_bill_id'],
            ];
        }

        $posbill_id_list = BluelimeSystem::collect_field_value_from_object($sales_detail, 'pos_bill_id');
        $posbill_id_list = [...array_unique($posbill_id_list), -99];
        $pOSBillModel = model('POS/POSBillModel');
        $pos_bills = $pOSBillModel->select('id, payment_method, fs_number, response_date')->whereIn('id', $posbill_id_list)->findAll();
        $pos_bills = BluelimeSystem::index_object_list_array_by_unique_field($pos_bills, 'id');

        $sales_detail_ = [];
        foreach($sales_detail as $index => $detail){
            if(!isset($pos_bills['id_'.$detail['pos_bill_id']]))
                continue;
            $sales_detail[$index]['payment_method'] = $pos_bills['id_'.$detail['pos_bill_id']]['payment_method'];
            $sales_detail[$index]['fs_number'] = $pos_bills['id_'.$detail['pos_bill_id']]['fs_number'];
            $sales_detail[$index]['paid_date'] = $pos_bills['id_'.$detail['pos_bill_id']]['response_date'];
            $sales_detail_[] = $sales_detail[$index];
        }

        return $sales_detail_;
    }

    private function get_pharmacy_sales_detail($from_date, $to_date, $gl_account){
        $gl_type = "drug";
        if(substr($gl_account, 0, 4) == '40CM')
            $gl_type = "compounding";
        elseif(substr($gl_account, 0, 4) == '40CT')
            $gl_type = "compounding_taxable";
        elseif(substr($gl_account, 0, 4) == '40CO')
            $gl_type = "cosmetics";

        $salseMainModel = model('Inventory/SalesMainModel');
        $salseMainModel = $salseMainModel->where('recieved_date >=', $from_date)->where('recieved_date <=', $to_date);
        if(($gl_type == "drug") || ($gl_type == "cosmetics"))
            $salseMainModel = $salseMainModel->where('sales_type', BluelimeConfiguration::SALES_TYPE::DISPENSARY);
        else
            $salseMainModel = $salseMainModel->where('sales_type', BluelimeConfiguration::SALES_TYPE::COMPOUNDING);

        $sales = $salseMainModel->where('fs_number IS NOT',NULL)->where('pos_bill_id >',0)->findAll();

        $sales_main_id_list = BluelimeSystem::collect_field_value_from_object($sales, 'id');
        $sales_main_id_list = [...$sales_main_id_list, -99];
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $salesDetailModel = $salesDetailModel->whereIn('sales_id', $sales_main_id_list);
        if(($gl_type == "drug") || ($gl_type == "compounding"))
            $salesDetailModel = $salesDetailModel->where('vat', 0);
        else
            $salesDetailModel = $salesDetailModel->where('vat !=', 0);

        if(($gl_type == "compounding") || ($gl_type == "compounding_taxable"))
            $salesDetailModel = $salesDetailModel->select("sales_id, SUM(quantity*unit_price) AS amount")->groupBy('sales_id');
        

        $sales_details = $salesDetailModel->findAll();

        $posBillModel = model('POS/POSBillModel');
        $pos_bill_id_list = BluelimeSystem::collect_field_value_from_object($sales, 'pos_bill_id');
        $pos_bill_id_list = [...array_unique($pos_bill_id_list), -99];
        $pos_bills = $posBillModel->select('id, payment_method, fs_number, response_date')->whereIn('id', $pos_bill_id_list)->findAll();
        $pos_bills = BluelimeSystem::index_object_list_array_by_unique_field($pos_bills, 'id');

        $sales = BluelimeSystem::index_object_list_array_by_unique_field($sales, 'id');
        $sales_detail_ = [];

        foreach($sales_details as $index => $detail){
            if(!isset($pos_bills['id_'.$sales['id_'.$detail['sales_id']]['pos_bill_id']]))
                continue;
            $temp = [];
            // 'description' => $service_sales['service_name'],
            // 'quantity' => $service_sales['bill_quantity'],
            // 'unit_price' => round($service_sales['bill_amount'],2),
            // 'amount' => round($service_sales['bill_quantity'] * $service_sales['bill_amount'],2),
            // 'pos_bill_id' => $bills['id_'.$service_sales['bill_id']]['pos_bill_id'],

            // $sales_detail[$index]['payment_method'] = $pos_bills['id_'.$detail['pos_bill_id']]['payment_method'];
            // $sales_detail[$index]['fs_number'] = $pos_bills['id_'.$detail['pos_bill_id']]['fs_number'];
            // $sales_detail[$index]['paid_date'] = $pos_bills['id_'.$detail['pos_bill_id']]['response_date'];

            if(($gl_type == "compounding") || ($gl_type == "compounding_taxable")){
                $temp['description'] = 'Compounding';
                $temp['quantity'] = 0;
                $temp['unit_price'] = 0;
                $temp['amount'] = round($detail['amount'],2);
            }
            else{
                $temp['description'] = $detail['item_detail']['item_brand_name'];
                if(empty($temp['description']))
                    $temp['description'] = $detail['item_detail']['item_generic_name'];

                $temp['quantity'] = round($detail['quantity'],2);
                $temp['unit_price'] = round($detail['unit_price'],2);
                $temp['amount'] = round($detail['quantity'] * $detail['unit_price'],2);
            }
            
            $temp['pos_bill_id'] = $sales['id_'.$detail['sales_id']]['pos_bill_id'];
            $temp['payment_method'] = $pos_bills['id_'.$temp['pos_bill_id']]['payment_method'];
            $temp['fs_number'] = $pos_bills['id_'.$temp['pos_bill_id']]['fs_number'];
            $temp['paid_date'] = $pos_bills['id_'.$temp['pos_bill_id']]['response_date'];
            $sales_detail_[] = $temp;
        }
        
        return $sales_detail_;
    }

    public function get_all_item_transaction(){
        $api_key = $_GET['api_key'] ?? '';
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);

        $from_date = $POST_VAL['from_date'] ?? '1990-01-01';
        $to_date = $POST_VAL['to_date'] ?? '1990-01-01';
        $compress_data = $POST_VAL['compress_data'] ?? 1;

        if(strtotime($from_date) > strtotime($to_date)){
            $temp = $from_date;
            $from_date = $to_date;
            $to_date = $temp;
        }

        $from_date = substr($from_date, 0, 10) . ' 00:00:00';
        $to_date = substr($to_date, 0, 10) . ' 23:59:59';

        $transactionModel = model('Inventory/TransactionModel');
        [$fiscal_year_begining, $fiscal_year_ending] = BluelimeSystem::get_fiscal_year_range($from_date);
        $begining_date = date("Y-m-d 00:00:00", strtotime($from_date)-(60*60*24));
        $begining_transaction = $transactionModel->select('\'0\' AS id, store_id, item_id, SUM(quantity) as quantity, \'_BEGINING_\' AS transaction_type, \''.$begining_date.'\' AS created_at')->where('created_at >=', $fiscal_year_begining)->where('created_at <', $from_date)->groupBy('store_id, item_id')->findAll();
        
        //  echo $transactionModel->db->lastQuery;
        //  die();

        $transactions = $transactionModel->where('created_at >=', $from_date)->where('created_at <=', $to_date)->orderBy('store_id, item_id, id')->findAll();

        // $begining_transaction_map = [];
        // foreach($begining_transaction as $row){
        //     $begining_transaction_map[] = [
        //         'store_id' => $row['store_id'],
        //         'item_id' => $row['item_id'],
        //         'quantity' => $row['quantity'],
        //         'transaction_type' => 'BEGINING'
        //     ];
        // }

        $transactions = [...$begining_transaction, ...$transactions];

        $peachtreeItemModel = model('Inventory/PeachtreeItemModel');
        $peachtree_items = $peachtreeItemModel->findAll();
        $peachtree_items = BluelimeSystem::index_object_list_array_by_unique_field($peachtree_items, 'item_id');

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $peachtree_integration = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->first()['data'];
        
        $itemModel = model('Inventory/ItemModel');
        $compounding_items = $itemModel->where('compounding_volume !=', 0)->where('compounding_volume IS NOT', null)->findAll();
        $compounding_items = BluelimeSystem::index_object_list_array_by_unique_field($compounding_items, 'id');

        $compressed_transaction = [];
        $sales_collector = [];
        foreach($transactions as &$row){
            //Compunding service
            if($row['item_id']==0)
                continue;

            $row['remark'] = '';
            $row['created_at'] = substr($row['created_at'],0,10);
            $row['branch_number'] = $peachtree_integration['branch']['number'];

            //Fill peachtree ID
            $row['peachtree_id'] = $peachtree_items['item_id_'.$row['item_id']]['peachtree_id'] ?? 'ERROR';
            $row['peachtree_id'] .= '_';
            if($row['store_id']==1){
                $row['peachtree_id'] .= $peachtree_integration['code_suffix']['store'];
                $row['store_name'] = 'ST'.$peachtree_integration['branch']['number'];
            }
            elseif($row['store_id']==2){
                $row['peachtree_id'] .= $peachtree_integration['code_suffix']['dispensary'];
                $row['store_name'] = 'PH'.$peachtree_integration['branch']['number'];
            }
            elseif($row['store_id']==3){
                $row['peachtree_id'] .= $peachtree_integration['code_suffix']['compounding'];
                $row['store_name'] = 'CM'.$peachtree_integration['branch']['number'];
            }
            elseif($row['store_id']==4){
                $row['peachtree_id'] .= $peachtree_integration['code_suffix']['clinic'];
                $row['store_name'] = 'CL'.$peachtree_integration['branch']['number'];
            }
            else{
                $row['store_name'] = 'UNKNOWN'.$peachtree_integration['branch']['number'];
            }

            //Convert compunding item transaction quantity to item amount
            if($row['store_id']==3){
                if(floatval($compounding_items['id_'.$row['item_id']]['compounding_volume']??0)>0)
                    $row['quantity'] = floatval($row['quantity']) / floatval($compounding_items['id_'.$row['item_id']]['compounding_volume']);
                else
                    $row['remark'] = 'Has no compounding volume';
            }

            //If do not compress forget the rest
            if(!$compress_data)
                continue;
            
            //If compress, merge all consucative sales transaction
            if((empty($row['remark'])) && (($row['transaction_type'] == BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::DISPENSE_SALES) || ($row['transaction_type'] == BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::COMPOUNDING_SALES))){
                if(empty($sales_collector)){
                    $sales_collector = $row;
                }
                elseif(($sales_collector['store_id'] == $row['store_id']) && ($sales_collector['item_id'] == $row['item_id'])){
                    $sales_collector['quantity'] += $row['quantity'];
                    $sales_collector['created_at'] = $row['created_at'];
                }
                else{
                    $compressed_transaction[] = $sales_collector;
                    $sales_collector = $row;
                }
            }
            else{
                if(!empty($sales_collector)){
                    $compressed_transaction[] = $sales_collector;
                    $sales_collector = [];
                }
                $compressed_transaction[] = $row;
            }
        }

        if(!empty($sales_collector)){
            $compressed_transaction[] = $sales_collector;
        }

        $signature = BluelimeSystem::create_jwt_signature($compressed_transaction, $api_key);

        return $this->response->setJSON(['status'=>'success', 'data'=>$compressed_transaction, 'signature'=>$signature]);
    }

    public function get_compounding_sales_detail(){
        set_time_limit(600);
        $api_key = $_GET['api_key'] ?? '';
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);
        $compress_data = $POST_VAL['compress_data'] ?? 1;

        $from_date = $POST_VAL['from_date'] ?? '2026-01-01';
        $to_date = $POST_VAL['to_date'] ?? '2026-01-10';

        if(strtotime($from_date) > strtotime($to_date)){
            $temp = $from_date;
            $from_date = $to_date;
            $to_date = $temp;
        }

        $from_date = substr($from_date, 0, 10) . ' 00:00:00';
        $to_date = substr($to_date, 0, 10) . ' 23:59:59';

        $salesMainModel = model('Inventory/SalesMainModel');
        $salesMainModel = $salesMainModel->select('id, fs_number, recieved_date')->where('recieved_date >=', $from_date)->where('recieved_date <=', $to_date)->where('sales_type', BluelimeConfiguration::SALES_TYPE::COMPOUNDING);
        $sales_mains = $salesMainModel->where('fs_number IS NOT',NULL)->where('pos_bill_id >',0)->findAll();
        $sales_mains = BluelimeSystem::index_object_list_array_by_unique_field($sales_mains, 'id');

        $sales_main_id_list = BluelimeSystem::collect_field_value_from_object($sales_mains, 'id');
        $sales_main_id_list = [...$sales_main_id_list, -99];
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $sales_details = $salesDetailModel->whereIn('sales_id', $sales_main_id_list)->findAll();
        $item_id_list = BluelimeSystem::collect_field_value_from_object($sales_details, 'item_id');
        $item_id_list = [...array_unique($item_id_list), -99];
        $item_id_list = array_unique($item_id_list);

        $peachtreeItemModel = model('Inventory/PeachtreeItemModel');
        $peachtree_items = $peachtreeItemModel->whereIn('item_id', $item_id_list)->findAll();
        $peachtree_items = BluelimeSystem::index_object_list_array_by_unique_field($peachtree_items, 'item_id');

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $peachtree_integration = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->first()['data'];
        $branch_number = $peachtree_integration['branch']['number'];
        $branch_name = $peachtree_integration['branch']['name'];

        $transactionModel = model('Inventory/TransactionModel');
        $grvDetailModel = model('Inventory/GRVDetailModel');

        $transactions = $transactionModel->whereIn('item_id', $item_id_list)->where('unit_cost >',0)->where('created_at <=', $to_date)->orderBy('created_at','DESC')->findAll();
        $grv_details = $grvDetailModel->select('item_id,average_cost,created_at')->whereIn('item_id', $item_id_list)->where('average_cost >',0)->where('created_at <=', $to_date)->orderBy('created_at','DESC')->findAll();

        $sales_detail = [];
        foreach($sales_details as $index => $detail){
            $temp = [];
            $temp['fs'] = $sales_mains['id_'.$detail['sales_id']]['fs_number'];
            $temp['date'] = substr($sales_mains['id_'.$detail['sales_id']]['recieved_date'],0,10);
            $temp['sales_id'] = $detail['sales_id'];
            $temp['peachtree_id'] = $peachtree_items['item_id_'.$detail['item_id']]['peachtree_id'] ?? '';
            
            if(empty($temp['peachtree_id']))
                $temp['peachtree_id'] = '_Compounding_Service';

            $temp['peachtree_id'] .= '_'.$peachtree_integration['code_suffix']['compounding'];
            $temp['description'] = $peachtree_items['item_id_'.$detail['item_id']]['peachtree_description'] ?? '';
            $temp['compounding_volume'] = $detail['item_detail']['compounding_volume']??1;
            //$temp['quantity_gram'] = $detail['quantity'];
            $temp['quantity_volume'] = $detail['quantity']/$temp['compounding_volume'];
            //$temp['quantity__'] = round($temp['quantity_'],2);
            $temp['unit_price'] = round($detail['unit_price'],2);
            $temp['branch_number'] = $branch_number;
            $temp['count'] = 1;
            $temp['cost'] = 0;
            if($detail['item_id']!=0){
                $temp['cost'] = $this->get_item_cost($detail['item_id'], $detail['created_at'], $transactions, $grv_details);
            }
            $temp['cost'] = $temp['quantity_volume'] * $temp['cost'];
            $sales_detail[] = $temp;
        }

        if($compress_data){
            $compressed_sales_detail = [];
            foreach($sales_detail as $detail){
                if(!isset($compressed_sales_detail[$detail['peachtree_id']])){
                    $compressed_sales_detail[$detail['peachtree_id']] = ['count'=>1, ...$detail];
                }
                else{
                    $compressed_sales_detail[$detail['peachtree_id']]['count']++;
                    //$compressed_sales_detail[$detail['peachtree_id']]['quantity_gram'] += $detail['quantity_gram'];
                    $compressed_sales_detail[$detail['peachtree_id']]['quantity_volume'] += $detail['quantity_volume'];
                    //$compressed_sales_detail[$detail['peachtree_id']]['quantity__'] += $detail['quantity__'];
                    $compressed_sales_detail[$detail['peachtree_id']]['cost'] += $detail['cost'];
                }
            }
            $sales_detail = array_values($compressed_sales_detail);
        }

        $signature = BluelimeSystem::create_jwt_signature($sales_detail, $api_key);
        return $this->response->setJSON(['status'=>'success', 'data'=>$sales_detail, 'signature'=>$signature]);
    }
    
    private function get_item_cost($item_id, $date, $transactions, $grv_details){
       
        foreach($transactions as $transaction){
            if(($transaction['item_id'] == $item_id) && (strcmp(substr($transaction['created_at'],0,10), substr($date,0,10))<=0)){
                return $transaction['unit_cost'];
            }
        }

        foreach($grv_details as $grv_detail){
            if(($grv_detail['item_id'] == $item_id) && (strcmp(substr($grv_detail['created_at'],0,10), substr($date,0,10))<=0)){
                return $grv_detail['average_cost'];
            }
        }
        return 0;
    }

    public function get_sales_receipt_data(){
        $api_key = $_GET['api_key'] ?? '';
        $POST_VAL = file_get_contents('php://input');
        $POST_VAL = BluelimeSystem::convert_to_array($POST_VAL);
        $from_date = $POST_VAL['from_date'];
        $to_date   = $POST_VAL['to_date'];
        $branch_number   = $POST_VAL['branch_number'];

        $pOSBillModel = model('POS/POSBillModel');
        $pos_bills = $pOSBillModel->select('id, total, transferred_amount, type, fs_number,response_data, payment_method, payment_reference, response_date')->where('response_date >=', $from_date)->where('response_date <=', $to_date)->findAll();
        $pos_bill_id_list = array_column($pos_bills, 'id');
        $salesMainModel = model('Inventory/SalesMainModel');
        $billMainModel = model('Hospital/BillModel');

        $inventory_salses = $salesMainModel->select('pos_bill_id, MAX(company_id) as company_id, MAX(company_name) as company_name')->where('company_id >',0)->whereIn('pos_bill_id', $pos_bill_id_list)->groupBy('pos_bill_id')->findAll();
        $hospital_salses = $billMainModel->select('pos_bill_id, MAX(company_id) as company_id, MAX(company_name) as company_name')->where('company_id >',0)->whereIn('pos_bill_id', $pos_bill_id_list)->groupBy('pos_bill_id')->findAll();

        $pos_bill_companies = [...$inventory_salses, ...$hospital_salses];

        $pos_bill_companies = BluelimeSystem::index_object_list_array_by_unique_field($pos_bill_companies, 'pos_bill_id');

        foreach($pos_bills as &$pos_bill){
            $pos_bill['company'] = $pos_bill_companies['pos_bill_id_'.$pos_bill['id']] ?? ['company_id'=>'', 'company_name'=>''];
            $recept_prefix = "R$branch_number";
            if($pos_bill['type'] == 'Inventory')
                $recept_prefix = "G$branch_number";

            if($pos_bill['response_data'] != 'Manual')
                $recept_prefix .= '-FS';
            else //Manual 'M' already there
                $recept_prefix .= '-';

            $pos_bill['fs_number'] = $recept_prefix . $pos_bill['fs_number'];
            $pos_bill['fs_number'] = str_replace(' ','',$pos_bill['fs_number']);
            unset($pos_bill['response_data']);
        }


        $signature = BluelimeSystem::create_jwt_signature($pos_bills, $api_key);
        return $this->response->setJSON(['status'=>'success', 'data'=>$pos_bills, 'signature'=>$signature]);
    }

}
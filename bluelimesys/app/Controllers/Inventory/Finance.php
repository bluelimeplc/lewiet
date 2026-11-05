<?php

namespace App\Controllers\Inventory;

use App\ThirdParty\Bluelime\SMS\BluelimeJasmin;
use App\ThirdParty\Bluelime\SMS\BluelimeSMS;
use BluelimeAuthentication;
use BluelimeConfiguration;
use BluelimeError;
use BluelimePOS;
use BluelimeSystem;
use ReflectionClass;

class Finance extends \App\Controllers\BaseController
{
    public function index()
    {
        $search_key = $_GET['search_key'] ?? '';

        $grvMainModel = model('Inventory/GRVMainModel');
        
        if(trim($search_key) != '')
            $grvMainModel = $grvMainModel->groupStart()->where('grv_code LIKE', "%$search_key%")->orWhere('invoice_no LIKE', "%$search_key%")->orWhere('fs_no LIKE', "%$search_key%")->groupEnd();

        $grv_main =  $grvMainModel->where('status !=',BluelimeConfiguration::VOUCHER_SATUS::PENDING)->orderBy('id','DESC')->paginate(BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST);

        $suppliers = BluelimeSystem::gather_objects_by_reference($grv_main, 'supplier_id', 'Inventory/SupplierModel', 'id', true);
    
        foreach($grv_main as $index => $row){
            $grv_main[$index]['supplier_detail'] = $suppliers['id_'.$row['supplier_id']];
        }

        $data['active_menu'] = 'grv';
        $data['active_sub_menu'] = 'grv_list';
        $data['grv_main'] = $grv_main;
        $data['search_key'] = $search_key;
        $data['pager'] = $grvMainModel->pager;
        $data['page'] = $grvMainModel->pager->getCurrentPage();

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Finance/_left_sidebar_finance')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Finance/grv_list')
                .view('Include/_footer');    
    }

    public function financedashboard()
    {
        
        $data['loggeduserdata'] = BluelimeSystem::$Authentication::get_logged_user_data();
        
        $data['active_menu'] = 'financedashboard';
        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Inventory/finance/_left_sidebar_finance')

            .view('Include/_left_sidebar_bottom')

            .view('Inventory/finance/finance_dashboard')

            .view('Include/_footer');
    }
    
    public function pricesetup_form()
    {
        $priceListModel = model('/Inventory/PriceListModel');
        $itemModel = model('Inventory/ItemModel');
        $grvDetailModel = model('Inventory/GRVDetailModel');
        

        $item_code = @$_POST['search_item'] != "" ? @$_POST['search_item']:"%";
        $today = date('Y-m-d').'%';
        $items = $itemModel->where('item_code like',$item_code)->orderBy('item_generic_name','ASC')->findAll();
        $price_lists = $priceListModel->where('effective_date <= ',$today)->orderBy('id','DESC')->findAll();
        $grv_list = $grvDetailModel->orderBy('created_at','DESC')->findAll();

        foreach($items as $index => $item){
            $items[$index]['unit_price'] = 0;
            foreach($price_lists as $price_list){
                if($price_list['item_id'] == $item['id']){
                    $items[$index]['unit_price'] = $price_list['unit_price'];
                    break;
                }
            }
            $items[$index]['last_unit_cost'] = 0;
            foreach($grv_list as $grv){
                if($grv['item_id'] == $item['id']){
                    $items[$index]['last_unit_cost'] = $grv['unit_price'];
                    break;
                }
            }
           
        }
        
        $items_j = [];
        foreach($items as $index => $item){
            $items_j['item_id_'.$item['id']] = $item;
        }
        
        $data['active_menu'] = 'pricesetup';
        $data['items'] = $items_j;

        return  view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Finance/_left_sidebar_finance')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Finance/pricesetup_form')
                .view('Include/_footer');    
    }

    public function pricelistsave()
    {
        $item_price_list = $_POST['item_price_list'] ?? [];
        $effective_date = $_POST['effective_date'] ?? date('Y-m-d');
        $store_id = $_POST['store_id'] ?? 0;
        $loggeduser = BluelimeAuthentication::get_logged_user_data();
        
        //BluelimeSystem::print_associated_array($item_price_list,false);

        $priceListModel = model('Inventory/PriceListModel');

        $item_price_list = BluelimeSystem::convert_to_array($item_price_list);
        if(empty($item_price_list))
            return BluelimeSystem::get_success_ajax_return('Price List Not Saved!');

        foreach($item_price_list as $index => $row){
            $row['store_id'] = $store_id;
            $row['registered_by'] = $loggeduser['id'];
            $row['effective_date'] = $effective_date;
            $item_price_list[$index] = $row;
        }

        $priceListModel->insertBatch($item_price_list);

        //BluelimeSystem::write_log('neba','price_list',BluelimeSystem::convert_array_to_string($item_price_list),'pricelistsave');
        
        return BluelimeSystem::get_success_ajax_return('Price List Saved successfully');
    }

    public function creditpayment()
    {
        $companyModel = model('Hospital/CompanyModel');
        $paymentRequestModel = model('Hospital/PaymentRequestModel');
        
        $data['active_menu'] = 'creditpayment';
        //$data['bill_info'] = $billModel->searchpendingbills($searchkey);
        
        $company_id = $_POST['company_id'] ?? "";
        $date_from = $_POST['date_from'] ?? "";
        $date_to = $_POST['date_to'] ?? "";        

        $paymentRequests = [];
        if($company_id != ''){
            $paymentRequestModel = $paymentRequestModel->where('company_id', $company_id);
            if($date_from != '')
                $paymentRequestModel = $paymentRequestModel->where('registered_date >=', $date_from);

            if($date_to != '')
                $paymentRequestModel = $paymentRequestModel->where('registered_date <=', $date_to);
    
            $paymentRequests = $paymentRequestModel->orderBy('id','DESC')->findAll();
        }
        else
            $paymentRequests = $paymentRequestModel->orderBy('id','DESC')->findAll(0,20);
        

        $companies = $companyModel->findAll();

        $alert = BluelimeSystem::get_alert();

        $data['companies'] = $companies;
        $data['active_menu'] = 'creditpayment';
        $data['active_sub_menu'] = 'receipt_list';
        $data['company_id'] = $company_id;
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['paymentRequests'] = $paymentRequests;
        $data['loggeduserdata'] = BluelimeAuthentication::get_logged_user_data();
        
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Inventory/Finance/_left_sidebar_finance')

            .view('Include/_left_sidebar_bottom')

            .view('Inventory/Finance/payment_request')

            .view('Include/_footer');
    }

    public function change_credit_receipt_group_status(){
        $payment_requet_id = $_POST['payment_requet_id'];
        $paymentRequestModel = model('Hospital/PaymentRequestModel');
        $payment_requet = $paymentRequestModel->find($payment_requet_id);
        $update_data = ['status' => 'Requested'];
        if($payment_requet['status'] == 'Requested')
            $update_data = ['status' => null];
        $paymentRequestModel->update($payment_requet_id, $update_data);
        return BluelimeSystem::get_success_ajax_return($update_data['status']);
    }

    public function newcreditpaymentrequest()
    {

        $companyModel = model('Hospital/CompanyModel');
        $companies = $companyModel->where('is_active', 1)->findAll();
        $companies = BluelimeSystem::sort_array($companies,['company_name']);
        $company_id = $_GET['company_id'] ?? "";
        $date_from = $_GET['date_from'] ?? "";
        $date_to = $_GET['date_to'] ?? "";
        $department = $_GET['department'] ?? "";
        
        $bills = [];
        if($department == 'Clinic')
            $bills = $this->get_credit_bill_for_clinic($company_id, $date_from, $date_to);
        elseif($department == 'Pharmacy')
            $bills = $this->get_credit_bill_for_pharmacy($company_id, $date_from, $date_to);

        $data['company_name'] = $bills[0]['company_name'] ?? '';
        $data['active_menu'] = 'creditpayment';
        $data['active_sub_menu'] = 'receipt_list';
        $data['companies'] = $companies;
        $data['company_id'] = $company_id;
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['department'] = $department;
        $data['bills'] = $bills;
        
        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Inventory/Finance/_left_sidebar_finance')

            .view('Include/_left_sidebar_bottom')

            .view('Inventory/Finance/new_credit_payment_request')
            
            .view('Include/_footer');
    }

    private function get_credit_bill_for_clinic($company_id, $date_from, $date_to): array {
        $bills = [];
        //If all filter value are not empty fetch bills
        if($company_id != ''){
            $billModel = model('Hospital/BillModel');
            $billDetailModel = model('Hospital/BillDetailModel');
            $billModel = $billModel->where('company_id',$company_id);
            if(($date_from != '' )){
                $billModel = $billModel->where('payed_date >=', $date_from . " 00:00:00");
            }
            if(($date_to != '' )){
                $billModel = $billModel->where('payed_date <=', $date_to . " 23:59:59");
            }
            $bills = $billModel->select('*, "0" as total_amount')->whereNotIn('bill_status', [BluelimeConfiguration::BILL_PENDING,BluelimeConfiguration::BILL_REMOVED,BluelimeConfiguration::BILL_REMOVE_REQUESTED])->groupStart()->where('payment_request_id',0)->orWhere('payment_request_id')->groupEnd()->where('bill_type','Credit')->findAll();
            $bills = BluelimeSystem::index_object_list_array_by_unique_field($bills,'id');
            $bill_details = $billDetailModel->whereIn('bill_id',[-99,...array_column($bills, 'id')])->where('service_status !=', -1)->findAll();
            foreach($bill_details as $bill_detail){
                $bills['id_'.$bill_detail['bill_id']]['total_amount'] += ($bill_detail['bill_amount'] * $bill_detail['bill_quantity']);
            }
            foreach($bills as $index => $bill){
                $bills[$index]['paid_date'] = $bill['payed_date'];
            }
        }
       return $bills;
    }
    private function get_credit_bill_for_pharmacy($company_id, $date_from, $date_to): array {
        $credit_bills = [];
        //If all filter value are not empty fetch bills
        if($company_id != ''){
            $salesMainModel = model('Inventory/SalesMainModel');
            $pOSBillModel = model('POS/POSBillModel');
            $salesMainModel = $salesMainModel->where('company_id',$company_id);

            if(($date_from != '' )){
                $salesMainModel = $salesMainModel->where('recieved_date >=', $date_from . " 00:00:00");
            }
            if(($date_to != '' )){
                $salesMainModel = $salesMainModel->where('recieved_date <=', $date_to . " 23:59:59");
            }

            $sales_mains = $salesMainModel->where('credit_payment_request_id',0)->findAll();
            //$bills = BluelimeSystem::index_object_list_array_by_unique_field($bills,'id');
            $pos_bills = $pOSBillModel->whereIn('id',[-99,...array_column($sales_mains, 'pos_bill_id')])->findAll();
            foreach($pos_bills as $pos_bill){
                $patient_id = '';
                $patient_name = '';
                $has_credit_payment = false;

                foreach($sales_mains as $sales_main){
                    if($sales_main['pos_bill_id'] == $pos_bill['id']){
                        $patient_id = $sales_main['patient_id'];
                        $patient_name = $sales_main['patient_name'];
                        $has_credit_payment = true;
                        break;
                    }
                }

                if(!$has_credit_payment)
                    continue;

                $credit_bills[] = [
                    'patient_id' => $patient_id,
                    'patient_name' => $patient_name,
                    'paid_date' => $pos_bill['response_date'],
                    'fs_number' => $pos_bill['fs_number'],
                    'total_amount' => $pos_bill['total'],
                    'id' => $pos_bill['id']
                ];
            }
        }
       return $credit_bills;
    }

    public function createcreditpaymentrequest()
    {
        $paymentRequestModel = model('Hospital/PaymentRequestModel');

        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();

        $bill_id_list = $_POST['selected_bills'] ?? [];
        $company_id = $_POST['company_id'] ?? '';
        $company_name = $_POST['company_name'] ?? '';
        $total_amount = $_POST['total_amount'] ?? 0;
        $from_date = $_POST['date_from'];
        $to_date = $_POST['date_to'];
        $department = $_POST['department'];

        if(empty($from_date))
            $from_date = NULL;
        if(empty($to_date))
            $to_date = NULL;
        
        $registered_by = $loggeduserdata['id'];
        $registered_by_name = $loggeduserdata['name'];

        $data['department'] = $department;
        $data['company_id'] = $company_id;
        $data['company_name'] = $company_name;
        $data['total_amount'] = $total_amount;
        $data['bill_id_list'] = BluelimeSystem::convert_array_to_string($bill_id_list);
        $data['registered_by'] = $registered_by;
        $data['registered_by_name'] = $registered_by_name;
        $data['registered_date'] = date("Y-m-d");
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['active_menu'] = 'creditpayment';

        if(empty($bill_id_list)){
            $alert_body_html = "There was no bill selected.<br>There is no update made on the database.";
            BluelimeSystem::set_alert($alert_body_html,"OK", "WARNING");
            return redirect()->to(base_url()."inventory/finance/newcreditpaymentrequest?fail=1");
        }
        
        $paymentRequestModel->insert($data);
        $id = $paymentRequestModel->getInsertID();

        if($id == 0){
            $alert_body_html = "There was an error creating Credit Payment Request.";
            BluelimeSystem::set_alert($alert_body_html,"OK", "WARNING");
            return redirect()->to(base_url()."inventory/finance/newcreditpaymentrequest?fail=2");
        }

        if($department == 'Clinic'){
            $billModel = model('Hospital/BillModel');
            $data = ["payment_request_id" => $id];
            $billModel->update($bill_id_list, $data);
        }
        elseif($department == 'Pharmacy'){
            $salesMainModel = model('Inventory/SalesMainModel');
            $data = ["credit_payment_request_id" => $id];
            $salesMainModel->whereIn('pos_bill_id',$bill_id_list)->update(row:$data);
        }

        return redirect()->to(base_url()."inventory/finance/creditpayment");
    }

    public function deletepaymentrequest()
    {
        $id = $_GET['id'] ?? 0;
        if($id == 0){
            $alert_body_html = "Payment Request is not deleted.<br>ID is not set.";
            BluelimeSystem::set_alert($alert_body_html);
            return redirect()->to(base_url()."inventory/finance/creditpayment?fail=1");
        }
        
        $paymentRequestModel = model('Hospital/PaymentRequestModel');
        
        $paymentRequest = $paymentRequestModel->find($id);

        if(empty($paymentRequest)){
            $alert_body_html = "Payment Request is not deleted.<br>ID $id cannot be found.";
            BluelimeSystem::set_alert($alert_body_html,"OK","WARNING");
            return redirect()->to(base_url()."inventory/finance/creditpayment?fail=2");
        }
        if(!empty($paymentRequest['status'])){
            $alert_body_html = "Payment Request is not deleted.<br>Status is ". $paymentRequest['status'] ." cannot be found.";
            BluelimeSystem::set_alert($alert_body_html,"OK","WARNING");
            return redirect()->to(base_url()."inventory/finance/creditpayment?fail=2");
        }

        $bill_id_list = BluelimeSystem::convert_to_array($paymentRequest['bill_id_list']);

        if(!empty($bill_id_list)){
            if($paymentRequest['department'] == 'Clinic'){
                $billModel = model('Hospital/BillModel');
                $update_data = ['payment_request_id' => 0];
                $updated = $billModel->update($bill_id_list, $update_data);
            }
            elseif($paymentRequest['department'] == 'Pharmacy'){
                $salesMainModel = model('Inventory/SalesMainModel');
                $update_data = ['credit_payment_request_id' => 0];
                $updated = $salesMainModel->whereIn('pos_bill_id',$bill_id_list)->update(row:$update_data);
            }
            else{
                $alert_body_html = "Payment Request is not deleted.<br>Invalid department.";
                BluelimeSystem::set_alert($alert_body_html);
                return redirect()->to(base_url()."inventory/finance/creditpayment?fail=5");
            }
            
            
            if(!$updated){
                $alert_body_html = "Payment Request is not deleted.<br>Cannot update/remove Payment Request ID from Bills.";
                BluelimeSystem::set_alert($alert_body_html);
                return redirect()->to(base_url()."inventory/finance/creditpayment?fail=3");
            }
        }
        else{
            $alert_body_html = "Payment Request is not deleted.<br>It has invalid bill id list.";
            BluelimeSystem::set_alert($alert_body_html);
            return redirect()->to(base_url()."inventory/finance/creditpayment?fail=4");
        }

        $paymentRequestModel->delete($id);

        return redirect()->to(base_url()."inventory/finance/creditpayment");
    }

    public function viewcreditpaymentrequest()
    {
        $paymentRequestModel = model('Hospital/PaymentRequestModel');
       
        $viewBillAndPaymentModel = model('Hospital/ViewBillAndPaymentModel');
        $visitModel = model('Hospital/VisitModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $finance_maintain = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_integration = $finance_maintain[0]['data'];
        
        $payment_request_id = $_GET['id'] ?? 0;

        $paymentRequest = $paymentRequestModel->find($payment_request_id);
        $department = $paymentRequest['department'] ?? '';
        $bill_id_list = BluelimeSystem::convert_to_array($paymentRequest['bill_id_list']);
        $receipt_list = [];

        if($department == 'Clinic')
            $receipt_list = $this->get_credit_report_data_clinic($bill_id_list);
        elseif($department == 'Pharmacy')
            $receipt_list = $this->get_credit_report_data_pharmacy($bill_id_list);


        $data['receipt_list'] = $receipt_list;
        $data['paymentRequest'] = $paymentRequest;
        $data['branch_no'] = $peachtree_integration['branch']['number'];
        $data['active_menu'] = 'creditpayment';
        $data['active_sub_menu'] = 'receipt_list';

        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)
            .view('Inventory/Finance/_left_sidebar_finance')
            .view('Include/_left_sidebar_bottom')
            .view('Inventory/Finance/payment_request_view')
            .view('Include/_footer');
    }

    private function get_credit_report_data_clinic(array $bill_id_list): array{

        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');

        if(empty($bill_id_list))
            $bill_id_list = [0];
        
        $bills = $billModel->whereIn('id',$bill_id_list)->orderBy('payed_date', 'ASC')->findAll();//->findAllWithDetail_GroupedByType();
        $bill_details = $billDetailModel->whereIn('bill_id',$bill_id_list)->where('service_status !=', -1)->findAll();

        $receipt_list = [];

        foreach($bills as $bill){
            $receipt_list_temp = [
                'id' => $bill['id'],
                'date' => $bill['payed_date'],
                'fs_number' => $bill['fs_number'],
                'patient_name' => $bill['patient_name'],
                'services' => [],
                'total' => 0
            ];

            $bill_details_temp = array_filter($bill_details, function($detail) use ($bill){
                return $detail['bill_id'] == $bill['id'];
            });

            $lab_service = ['service_name'=>'LAB', 'bill_quantity'=>0, 'bill_amount'=>0];
            foreach($bill_details_temp as $index => $row){
                if($row['service_type'] == BluelimeConfiguration::SERVICE_TYPE::LABORATORY){
                    $lab_service['bill_quantity'] = 1;
                    $lab_service['bill_amount']   += $row['bill_quantity'] * $row['bill_amount'];
                    unset($bill_details_temp[$index]);
                }
            }

            if($lab_service['bill_amount'] > 0){
                $bill_details_temp[] = $lab_service;
            }

            $receipt_list_temp['services'] = $bill_details_temp;

            foreach($bill_details_temp as $bill_detail){
                $receipt_list_temp['total'] += $bill_detail['bill_quantity'] * $bill_detail['bill_amount'];
            }

            $receipt_list[] = $receipt_list_temp;
        }

        return $receipt_list;
    }

    private function get_credit_report_data_pharmacy(array $bill_id_list): array{

        $pOSBillModel = model('POS/POSBillModel');
        $pos_bills = $pOSBillModel->whereIn('id',$bill_id_list)->orderBy('response_date', 'DESC')->findAll();

        $receipt_list = [];
        foreach($pos_bills as $pos_bill){
            $receipt_list[] = [
                'id' => $pos_bill['id'],
                'date' => $pos_bill['response_date'],
                'fs_number' => $pos_bill['fs_number'],
                'patient_name' => $pos_bill['pos_bill_json']['customer_name'],
                'total_amount' => $pos_bill['total']
            ];
        }
        
        return $receipt_list;
    }

    public function remove_revert_receipt_from_credit_receipt_group(){
        $type = $_POST['type'];
        $receipt_id = $_POST['receipt_id'];
        $payment_request_id = $_POST['payment_request_id'];

        $result = -1;
        if($type == 'clinic')
            $result = $this->remove_revert_receipt_from_credit_receipt_group_clinic($receipt_id, $payment_request_id);
        elseif($type == 'pharmacy')
            $result = $this->remove_revert_receipt_from_credit_receipt_group_pharmacy($receipt_id, $payment_request_id);
        
        $return_result = '';
        if($result == -1)
            $return_result = BluelimeSystem::get_error_ajax_return('Cannot update the data');
        elseif($result > 0)
            $return_result = BluelimeSystem::get_success_ajax_return('');
        else
            $return_result = BluelimeSystem::get_success_ajax_return('deleted');

        return $return_result;
    }

    private function remove_revert_receipt_from_credit_receipt_group_clinic($receipt_id, $payment_request_id){
        $billModel = model('Hospital/BillModel');
        $paymentRequestModel = model('Hospital/PaymentRequestModel');

        $bill = $billModel->find($receipt_id);
        if(empty($bill))
            return -1;

        $payment_request = $paymentRequestModel->find($payment_request_id);
        $receipt_id_list = BluelimeSystem::convert_to_array($payment_request['bill_id_list']);
        $receipt_id_list[] = $receipt_id;
        $receipt_id_list = array_unique($receipt_id_list);

        $update_data = ['payment_request_id' => $payment_request_id];
        if(intval($bill['payment_request_id']) == $payment_request_id){
            $index = array_search($receipt_id, $receipt_id_list);
            array_splice($receipt_id_list, $index, 1); 
            $update_data = ['payment_request_id' => null];
        }
        elseif(intval($bill['payment_request_id']) != 0 )
            return -1;

        if($billModel->update($receipt_id, $update_data)){
            $bllDetailModel = model('Hospital/BillDetailModel');
            $bill_details = $bllDetailModel->select('SUM(bill_quantity*bill_amount) AS total')->whereIn('bill_id', [...$receipt_id_list,-99])->where('service_status',0)->findAll();
            $total_amount = $bill_details[0]['total'] ?? 0;

            $receipt_id_list = BluelimeSystem::convert_array_to_string($receipt_id_list);
            $paymentRequestModel->update($payment_request_id, ['bill_id_list' => $receipt_id_list, 'total_amount' => $total_amount]);
            return $update_data['payment_request_id'];
        }
        return -1;
    }
    private function remove_revert_receipt_from_credit_receipt_group_pharmacy($receipt_id, $payment_request_id){
        //$pOSBillModel = model('POS/POSBillModel');
        $salesMainModel = model('Inventory/SalesMainModel');
        $paymentRequestModel = model('Hospital/PaymentRequestModel');

        $sales_bill = $salesMainModel->where('pos_bill_id', $receipt_id)->findAll(1);
        $sales_bill = $sales_bill[0] ?? [];

        if(empty($sales_bill))
            return -1;

        $payment_request = $paymentRequestModel->find($payment_request_id);
        $receipt_id_list = BluelimeSystem::convert_to_array($payment_request['bill_id_list']);
        $receipt_id_list[] = $receipt_id;
        $receipt_id_list = array_unique($receipt_id_list);

        $update_data = ['credit_payment_request_id' => $payment_request_id];
        if(intval($sales_bill['credit_payment_request_id']) == $payment_request_id){
            $index = array_search($receipt_id, $receipt_id_list);
            array_splice($receipt_id_list, $index, 1); 
            $update_data = ['credit_payment_request_id' => null];
        }
        elseif(intval($sales_bill['credit_payment_request_id']) != 0 )
            return -1;

        if($salesMainModel->where('pos_bill_id', $receipt_id)->update(row:$update_data)){
            $sales_id_list = $salesMainModel->whereIn('pos_bill_id', [...$receipt_id_list,-99])->findColumn('id');
            $salesDetailModel = model('Inventory/SalesDetailModel');
            $sales = $salesDetailModel->select('SUM((quantity*unit_price)+(quantity*vat)) AS total')->whereIn('sales_id', [...$sales_id_list,-99])->findAll();
            $total_amount = floatval($sales[0]['total'] ?? 0);
            //log_message('info', "salesDetailModel: \n". $salesDetailModel->db->getLastQuery()."\n$total_amount");
            $receipt_id_list = BluelimeSystem::convert_array_to_string($receipt_id_list);
            $paymentRequestModel->update($payment_request_id, ['bill_id_list' => $receipt_id_list, 'total_amount' => $total_amount]);
            return $update_data['credit_payment_request_id'];
        }
        return -1;
    }

    public function updatepaymentrequestreportinfo(){
        $month_name = $_POST['month_name'];
        $company_title = $_POST['company_title'];
        $id = $_POST['id'];

        $paymentRequestModel = model('Hospital/PaymentRequestModel');

        $data = [
            'month_name' => $month_name,
            'company_title' => $company_title,
        ];

        if(trim($month_name) == '' || trim($company_title) == ''){
            return BluelimeSystem::get_error_ajax_return('Month Name and Company Title cannot be empty!');
        }

        if(!$paymentRequestModel->update($id, $data)){
            return BluelimeSystem::get_error_ajax_return('Failed to update Payment Request Report Info!');
        }
        return BluelimeSystem::get_success_ajax_return('Payment Request Report Info updated successfully!');
    }

    public function credit_payment_invoice_list(){
        $creditPaymentInvoiceModel = model('Inventory/CreditPaymentInvoiceModel');
        $creditPaymentInvoices = $creditPaymentInvoiceModel->select('id, company_title, month, data,status,letter_number, payment_number, created_at')->orderBy('id','DESC')->findAll();

        foreach($creditPaymentInvoices as &$row){
            $total_amount = 0;
            $from_date = '';
            $to_date = '';
            foreach($row['data'] as $data){
                $receipt_date = substr($data['date'], 0, 10);

                $receipt_date = date_create($receipt_date);
                if($from_date == '' || $receipt_date < date_create($from_date))
                    $from_date = date_format($receipt_date, 'Y-m-d');
                
                if($to_date == '' || $receipt_date > date_create($to_date))
                    $to_date = date_format($receipt_date, 'Y-m-d');

                foreach($data['sales_detail'] as $sales_detail){
                    $total_amount += $sales_detail['quantity'] * $sales_detail['unit_price'];
                    $total_amount += $sales_detail['quantity'] * $sales_detail['unit_price'] * $sales_detail['vat'] * BluelimeConfiguration::VAT;
                }
            }

            $row['total_amount'] = $total_amount;
            $row['from_date'] = $from_date;
            $row['to_date'] = $to_date;
            unset($row['data']);
        }

        $data['creditPaymentInvoices'] = $creditPaymentInvoices;
        $data['active_menu'] = 'creditpayment';
        $data['active_sub_menu'] = 'credit_payment_invoice_list';
        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Inventory/Finance/_left_sidebar_finance')
            .view('Include/_left_sidebar_bottom')
            .view('Inventory/Finance/credit_payment_invoice_list')
            .view('Include/_footer');
    }

    public function newcreditpaymentinvoice(){
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        //$receipt_group_list = $paymentRequestModel->select("*, '2' AS branch")->findAll();

        $finance_maintain = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::CREDIT_INVOICE_API)->findAll();
        $credit_invoice_api = $finance_maintain[0]['data'];

        $api_success = [];
        $receipt_group_list = [];
        foreach($credit_invoice_api as $api){
            $branch_number = $api['branch_number'];
            $branch_name = $api['branch_name'];
            $ip_addess = $api['ip_addess'];
            $api_key = $api['api_key'];

            $url = "http://$ip_addess/api2/finance/v1/get_credit_payment_receipt_group";

            $result = BluelimeSystem::communicate_api2("finance/newcreditpaymentinvoice", $url, $api_key);
            $result = BluelimeSystem::convert_to_array($result);

            $api_success_temp = [
                'branch_number' => $branch_number,
                'branch_name' => $branch_name,
                'ip_addess' => $ip_addess,
                'api_key' => $api_key,
                'status' => ($result['status'] ?? '') == 'success' ? 'success':'error',
                'error_message' => ''
            ];

            if(($result['status']??'')=='success'){
                $data = $result['data'] ?? [];
                if(BluelimeSystem::check_jwt_signature($data, $api_key, $result['signature'])){
                    foreach($data as $index => $row){
                        $data[$index]['branch'] = $branch_number;
                        $data[$index]['branch_name'] = $branch_name;
                    }
                    $receipt_group_list =[...$data, ...$receipt_group_list];
                }
                else{
                    $api_success_temp['status'] = 'error';
                    $api_success_temp['error_message'] = 'Invalid JWT Signature!';
                }
            }
            else{
                $api_success_temp['error_message'] = $result['message'] ?? 'Unknown error occurred!';
            }
            $api_success[] = $api_success_temp;

        }

        //BluelimeSystem::print_associated_array($receipt_group_list);

        $receipt_group_list = BluelimeSystem::sort_array($receipt_group_list, ['company_name']);

        $data = [];
        $data['api_success'] = $api_success;
        $data['receipt_group_list'] = $receipt_group_list;
        $data['active_menu'] = 'creditpayment';
        $data['active_sub_menu'] = 'credit_payment_invoice_list';
        return
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Inventory/Finance/_left_sidebar_finance')
            .view('Include/_left_sidebar_bottom')
            .view('Inventory/Finance/new_credit_payment_invoice')
            .view('Include/_footer');
    }

    public function generate_credit_invoice(){
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $finance_maintain = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::CREDIT_INVOICE_API)->findAll();
        $credit_invoice_api = $finance_maintain[0]['data'];
        
        $invoice_data_raw = $_POST['invoice_data'] ?? '[]';
        $invoice_data = BluelimeSystem::convert_to_array($invoice_data_raw);

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $finance_maintain = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::CREDIT_INVOICE_API)->findAll();
        if(empty($finance_maintain)){
            return BluelimeSystem::get_error_ajax_return('Finance Maintain record not found for Credit Invoice API!');
        }

        $receipt_group_list = $invoice_data['receipt_group_list'];

        $branch_receipt_group_list = [];
        foreach($receipt_group_list as $receipt_group){
            $branch_number = $receipt_group['branch'] ?? '';
            if(!isset($branch_receipt_group_list[$branch_number])){
                $branch_receipt_group_list[$branch_number] = [];
            }
            $branch_receipt_group_list[$branch_number][] = $receipt_group['receipt_group_id'];
        }

        $receipt_detail = [];
        foreach($credit_invoice_api as $api){
            $branch_number = $api['branch_number'] ?? '';
            if(!isset($branch_receipt_group_list[$branch_number])){
                continue; // Skip if branch number not found in receipt group list
            }

            $ip_address = $api['ip_addess'] ?? '';
            $api_key = $api['api_key'] ?? '';

            if(empty($ip_address) || empty($api_key)){
                return BluelimeSystem::get_error_ajax_return('Failed to generate credit invoice: IP address or API key is not set');
                //continue; // Skip if IP address or API key is not set
            }

            $url = "http://$ip_address/api2/finance/v1/get_credit_payment_invoice_data";

            $result = BluelimeSystem::communicate_api2("finance/generate_credit_invoice", $url, $api_key, ['credit_receipt_group_id_list'=>$branch_receipt_group_list[$branch_number]]);
            $result = BluelimeSystem::convert_to_array($result);

            if(($result['status']??'') != 'success'){
                return BluelimeSystem::get_error_ajax_return('Failed to generate credit invoice: '.($result['message'] ?? 'Unknown error ').' for branch '.$branch_number);
            }

            $result_data = $result['data'] ?? [];
            $signature = $result['signature'] ?? '';

            if(!BluelimeSystem::check_jwt_signature($result_data, $api_key, $signature)){
                return BluelimeSystem::get_error_ajax_return('Failed to generate credit invoice: Invalid JWT Signature for branch '.$branch_number);
            }

            foreach($result_data as $index => $value){
                $result_data[$index]['branch_number'] = $branch_number;
            }

            $receipt_detail = [...$receipt_detail, ...$result_data];

        }
        
        $creditPaymentInvoiceModel = model('Inventory/CreditPaymentInvoiceModel');
        $invoice_id = $creditPaymentInvoiceModel->insert([
            'company_title' => $invoice_data['company_title'] ?? '',
            'month' => $invoice_data['month'] ?? '',
            'invoice_data_raw' => $invoice_data_raw,
            'status' => 0,
            'data' => json_encode($receipt_detail)
        ]);

        if($invoice_id <= 0){
            return BluelimeSystem::get_error_ajax_return('Failed to create credit invoice record in the database!');
        }

        foreach($credit_invoice_api as $api){
            $branch_number = $api['branch_number'] ?? '';
            if(!isset($branch_receipt_group_list[$branch_number])){
                continue; // Skip if branch number not found in receipt group list
            }

            $ip_address = $api['ip_addess'] ?? '';
            $api_key = $api['api_key'] ?? '';

            $url = "http://$ip_address/api2/finance/v1/update_receipt_group_status";
            $result = BluelimeSystem::communicate_api2("finance/generate_credit_invoice", $url, $api_key, ['credit_receipt_group_id_list'=>$branch_receipt_group_list[$branch_number],'status'=>'Requested']);
            $result = BluelimeSystem::convert_to_array($result);
            if(($result['status']??'') != 'success'){
                $creditPaymentInvoiceModel->delete($invoice_id);
                return BluelimeSystem::get_error_ajax_return('Failed to update receipt group status: '.($result['message'] ?? 'Unknown error ').' for branch '.$branch_number);
            }
        }

        $creditPaymentInvoiceModel->update($invoice_id, ['status'=>1]);

        return BluelimeSystem::get_success_ajax_return(['invoice_id' => $invoice_id]);
    }

    public function credit_payment_invoice_view(){
        $invoice_id = $_GET['invoice_id'] ?? 0;
        if($invoice_id <= 0){
            return redirect()->to(base_url()."inventory/finance/credit_payment_invoice_list");
        }

        $creditPaymentInvoiceModel = model('Inventory/CreditPaymentInvoiceModel');
        $invoice = $creditPaymentInvoiceModel->find($invoice_id);
        if(empty($invoice)){
            return redirect()->to(base_url()."inventory/finance/credit_payment_invoice_list");
        }

        $receipts = [];

        $invoice_detail = $invoice['data'];

        foreach($invoice_detail as $row){
            $sales_detail = $row['sales_detail'];
            $sales_group = [];
            $total_amount = 0;
            foreach($sales_detail as $sales){
                $item_type = $sales['description'];
                if($row['department']=='Pharmacy')
                    $item_type = 'Medicine';
                elseif($sales['item_type'] == 'Consultation')
                    $item_type = 'Consultation';
                elseif($sales['item_type'] == 'Laboratory')
                    $item_type = 'Lab';

                if(!isset($sales_group[$item_type])){
                    $sales_group[$item_type] = 0;
                }
                $amount_with_vat = ($sales['quantity'] * $sales['unit_price']) + ($sales['quantity'] * $sales['unit_price'] * $sales['vat'] * BluelimeConfiguration::VAT);
                $sales_group[$item_type] += $amount_with_vat;
                $total_amount += $amount_with_vat;
            }
            $branch = $row['branch_number'];
            if($row['department'] == 'Pharmacy')
                $branch = 'G'.$row['branch_number'];
            else
                $branch = 'R'.$row['branch_number'];

            $receipts[] = [
                'patient_name' => $row['patient_name'],
                'fs_no' => $row['fs_number'],
                'date' => substr($row['date'],0,10),
                'total' => $total_amount,
                'sales_detail' => $sales_group,
                'branch' => $branch
            ];
        }

        //BluelimeSystem::print_associated_array($receipts);
        $receipts = BluelimeSystem::sort_array($receipts, ['patient_name','date']);

        $invoice['date'] = substr($invoice['created_at'],0,10);
        unset($invoice['data']);

        $peachtreeCustomerModel = model('Inventory/PeachtreeCustomerModel');
        $peachtree_customers = $peachtreeCustomerModel->orderBy('customer_id')->findAll();

        $data['invoice'] = $invoice; //['company_title'=>$invoice['company_title'], 'month'=>$invoice['month'], 'date'=>substr($invoice['created_at'],0,10), 'letter_number'=>$invoice['letter_number'], 'payment_number'=>$invoice['payment_number']];
        $data['receipts'] = $receipts;
        $data['active_menu'] = 'creditpayment';
        $data['active_sub_menu'] = 'credit_payment_invoice_list';
        $data['peachtree_customers'] = $peachtree_customers;

        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Inventory/Finance/_left_sidebar_finance')
            .view('Include/_left_sidebar_bottom')
            .view('Inventory/Finance/credit_payment_invoice_view')
            .view('Include/_footer');
    }

    public function update_credit_invoice_info(){
        $company_title = $_POST['company_title'];
        $month = $_POST['month'];
        $letter_number = $_POST['letter_number'];
        $payment_number = $_POST['payment_number'];
        $id = $_POST['id'];

        $creditPaymentInvoiceModel = model('Inventory/CreditPaymentInvoiceModel');

        $update_data = [
            'company_title' => $company_title,
            'month' => $month,
            'letter_number' => $letter_number,
            'payment_number' => $payment_number
        ];

        $creditPaymentInvoiceModel->update($id, $update_data);

        $credit_invoice = $creditPaymentInvoiceModel->find($id);

        if(
            ($company_title == $credit_invoice['company_title']) && 
            ($month == $credit_invoice['month']) &&
            ($letter_number == $credit_invoice['letter_number']) &&
            ($payment_number == $credit_invoice['payment_number'])
        )
            return BluelimeSystem::get_success_ajax_return([...$update_data]);

        return BluelimeSystem::get_error_ajax_return('Invoice info is not updated');
    }

    public function export_credit_payment_invoice_csv(){
        $invoice_id = $_GET['invoice_id'];
        $peachtree_id = $_GET['peachtree_id'] ?? '';

        $creditPaymentInvoiceModel = model('Inventory/CreditPaymentInvoiceModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $finance_mainatain = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_intergration = $finance_mainatain[0]['data'];
        $vat_gl_account = $peachtree_intergration['gl_account']['vat'];

        $invoice = $creditPaymentInvoiceModel->find($invoice_id);

        $invoice_detail = $invoice['data'];
        //BluelimeSystem::print_associated_array($invoice_detail);

        $peachtree_csv = [];
        foreach($invoice_detail as $row){
            $branch_index = 'R'.$row['branch_number'];
            if($row['department'] == 'Pharmacy')
                $branch_index = 'G'.$row['branch_number'];

            $receipt_number = str_replace(' ','', $row['fs_number']);
            if(strtoupper(substr($receipt_number,0,1)) == 'M')
                $receipt_number = "{$branch_index}-{$receipt_number}";
            else
                $receipt_number = "{$branch_index}-FS{$receipt_number}";

            $number_of_distribution = \count($row['sales_detail']);
            foreach($row['sales_detail'] as $detail){
                //$number_of_distribution += $detail['vat'] > 0 ? 1 : 0;
                if($detail['vat'] > 0){
                    $number_of_distribution++;
                    break;
                }
            }
            $peachtree_sales = $this->get_peachtree_sales_data_common($peachtree_id,$receipt_number, date_format(date_create($row['date']),"m/d/Y"), $number_of_distribution);
            $cnt = 1;
            $vat = 0;
            foreach($row['sales_detail'] as $detail){
                $peachtree_sales['Invoice/CM Distribution'] = $cnt++;
                $peachtree_sales['Item ID'] = $detail['peachtree_id'];
                $peachtree_sales['Description'] = $detail['description'];
                $peachtree_sales['G/L Account'] = $detail['gl_account'];
                $peachtree_sales['Quantity'] = $detail['quantity'];
                $peachtree_sales['Unit Price'] = $detail['unit_price'];
                $peachtree_sales['Amount'] =  -1 * $detail['unit_price'] * $detail['quantity'];
                $peachtree_sales['Stocking Quantity'] = $detail['quantity'];
                $peachtree_sales['Stocking Unit Price'] = $detail['unit_price'];
                $peachtree_csv[] = $peachtree_sales;
                if($detail['vat'] > 0){
                    $vat += $detail['quantity'] * $detail['unit_price'] * BluelimeConfiguration::VAT;
                }
            }

            if($vat > 0){
                $peachtree_sales['Invoice/CM Distribution'] = $cnt++;
                $peachtree_sales['Item ID'] = '';
                $peachtree_sales['Description'] = 'VAT';
                $peachtree_sales['G/L Account'] = $vat_gl_account;
                $peachtree_sales['Quantity'] = 1;
                $peachtree_sales['Unit Price'] = $vat;
                $peachtree_sales['Amount'] =  -1 * $vat;
                $peachtree_sales['Stocking Quantity'] = 1;
                $peachtree_sales['Stocking Unit Price'] = $vat;
                $peachtree_csv[] = $peachtree_sales;
            }
        }

        BluelimeSystem::export_arra_in_csv($peachtree_csv, 'credit_invoice'.$invoice['letter_number'].'.csv');
    }

    public function delete_peachtree_customer_id(){
        $peachtreeCustomerModel = model('Inventory/PeachtreeCustomerModel');
        $id = $_POST['id'] ?? 0;
        if($id <= 0){
            return BluelimeSystem::get_error_ajax_return('Invalid Peachtree Customer ID');
        }

        $peachtree_customer = $peachtreeCustomerModel->find($id);
        if(empty($peachtree_customer)){
            return BluelimeSystem::get_error_ajax_return('Peachtree Customer not found');
        }

        if(!$peachtreeCustomerModel->delete($id)){
            return BluelimeSystem::get_error_ajax_return('Failed to delete Peachtree Customer');
        }

        return BluelimeSystem::get_success_ajax_return('Peachtree Customer deleted successfully');
    }

    public function add_peachtree_customer_id(){
        $peachtreeCustomerModel = model('Inventory/PeachtreeCustomerModel');
        $customer_id = $_POST['customer_id'] ?? '';
        $customer_name = $_POST['name'] ?? '';

        if(trim($customer_id) == '' || trim($customer_name) == ''){
            return BluelimeSystem::get_error_ajax_return('Customer ID and Customer Name cannot be empty');
        }

        $existing_customer = $peachtreeCustomerModel->where('customer_id', $customer_id)->first();
        if(!empty($existing_customer)){
            return BluelimeSystem::get_error_ajax_return('Customer ID already exists');
        }

        $data = [
            'customer_id' => $customer_id,
            'name' => $customer_name
        ];

        $id = $peachtreeCustomerModel->insert($data);
        if(!$id){
            return BluelimeSystem::get_error_ajax_return('Failed to add Peachtree Customer');
        }

        return BluelimeSystem::get_success_ajax_return($id);
    }

    public function export_credit_payment_invoice_csvOLD(){
        $invoice_id = $_GET['invoice_id'];

        $creditPaymentInvoiceModel = model('Inventory/CreditPaymentInvoiceModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $finance_mainatain = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_intergration = $finance_mainatain[0]['data'];
        $vat_gl_account = $peachtree_intergration['gl_account']['vat'];

        $invoice = $creditPaymentInvoiceModel->find($invoice_id);

        $invoice_detail = $invoice['data'];
        //BluelimeSystem::print_associated_array($invoice_detail);

        $branch_gorup = [];
        foreach($invoice_detail as $row){
            $branch_index = 'R'.$row['branch_number'];
            if($row['department'] == 'Pharmacy')
                $branch_index = 'G'.$row['branch_number'];

            $accounting_period = BluelimeSystem::get_accounting_period($row['date']);
            $branch_index .= "_P{$accounting_period}";
            $period_last_date = BluelimeSystem::get_accounting_period_last_date($row['date']);

            if(!isset($branch_gorup[$branch_index]))
                $branch_gorup[$branch_index] = ['period_last_date'=>$period_last_date, 'sales_detail'=>[]];

            if($row['department'] == 'Clinic')
                $branch_gorup[$branch_index]['sales_detail'] = $this->group_credit_sales_detail_clinic($branch_gorup[$branch_index]['sales_detail'],$row['sales_detail'],$vat_gl_account);
            else
                $branch_gorup[$branch_index]['sales_detail'] = $this->group_credit_sales_detail_pharmacy($branch_gorup[$branch_index]['sales_detail'],$row['sales_detail'],$vat_gl_account);
        }

        $peachtree_csv = [];
        foreach($branch_gorup as $branch_index => $sales_group){

            $peachtree_sales = $this->get_peachtree_sales_data_common($invoice['letter_number'].'_'.$branch_index, date_format(date_create($sales_group['period_last_date']),"m/d/Y"), count($sales_group['sales_detail']));
            $cnt = 1;
            $sales_detail = $sales_group['sales_detail'];
            $sales_detail = BluelimeSystem::sort_array($sales_detail, ['sorting','description']);
            foreach($sales_detail as $detail){
                $peachtree_sales['Invoice/CM Distribution'] = $cnt++;
                $peachtree_sales['Item ID'] = $detail['peachtree_id'];
                $peachtree_sales['Description'] = $detail['description'];
                $peachtree_sales['G/L Account'] = $detail['gl_account'];
                $peachtree_sales['Quantity'] = $detail['quantity'];
                $peachtree_sales['Unit Price'] = $detail['unit_price'];
                $peachtree_sales['Amount'] =  -1 * $detail['unit_price'] * $detail['quantity'];
                $peachtree_sales['Stocking Quantity'] = $detail['quantity'];
                $peachtree_sales['Stocking Unit Price'] = $detail['unit_price'];
                $peachtree_csv[] = $peachtree_sales;
            }
        }

        BluelimeSystem::export_arra_in_csv($peachtree_csv, 'credit_invoice'.$invoice['letter_number'].'.csv');

        //BluelimeSystem::print_associated_array($peachtree_csv, false);

    }

    public function delete_credit_invoice(){
        $invoice_id = $_POST['invoice_id'];
        $creditPaymentInvoiceModel = model('Inventory/CreditPaymentInvoiceModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $finance_maintain = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::CREDIT_INVOICE_API)->findAll();
        
        $credit_invoice_api = $finance_maintain[0]['data'];
        $creditPaymentInvoiceModel->update($invoice_id, ['status'=>-1]);
        $credit_invoice = $creditPaymentInvoiceModel->find($invoice_id);
        $invoice_data_raw = $credit_invoice['invoice_data_raw'];
        $invoice_data = $invoice_data_raw;

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $finance_maintain = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::CREDIT_INVOICE_API)->findAll();
        if(empty($finance_maintain)){
            return BluelimeSystem::get_error_ajax_return('Finance Maintain record not found for Credit Invoice API!');
        }

        $receipt_group_list = $invoice_data['receipt_group_list'];

        $branch_receipt_group_list = [];
        foreach($receipt_group_list as $receipt_group){
            $branch_number = $receipt_group['branch'] ?? '';
            if(!isset($branch_receipt_group_list[$branch_number])){
                $branch_receipt_group_list[$branch_number] = [];
            }
            $branch_receipt_group_list[$branch_number][] = $receipt_group['receipt_group_id'];
        }

        foreach($credit_invoice_api as $api){
            $branch_number = $api['branch_number'] ?? '';
            if(!isset($branch_receipt_group_list[$branch_number])){
                continue; // Skip if branch number not found in receipt group list
            }

            $ip_address = $api['ip_addess'] ?? '';
            $api_key = $api['api_key'] ?? '';

            if(empty($ip_address) || empty($api_key)){
                return BluelimeSystem::get_error_ajax_return('Failed to update receipt group status: IP address or API key is not set');
                //continue; // Skip if IP address or API key is not set
            }

            $url = "http://$ip_address/api2/finance/v1/update_receipt_group_status";

            $result = BluelimeSystem::communicate_api2("finance/delete_credit_invoice", $url, $api_key, ['credit_receipt_group_id_list'=>$branch_receipt_group_list[$branch_number],'status'=>null]);
            $result = BluelimeSystem::convert_to_array($result);

            if(($result['status']??'') != 'success'){
                return BluelimeSystem::get_error_ajax_return('Failed to update receipt group status: '.($result['message'] ?? 'Unknown error ').' for branch '.$branch_number);
            }
        }

        $creditPaymentInvoiceModel->delete($invoice_id);
        return BluelimeSystem::get_success_ajax_return('Credit Invoice is deleted successfully!');
    }

    private function group_credit_sales_detail_clinic(array $sales_detail_group, array $sales_detail, string $vat_gl_account):array{
        foreach($sales_detail as $sales){
            $index = $sales['description'].$sales['gl_account'].$sales['unit_price'];
            if(!isset($sales_detail_group[$index]))
                $sales_detail_group[$index] = ['gl_account'=>$sales['gl_account'], 'unit_price'=>$sales['unit_price'], 'description'=>$sales['description'], 'quantity'=>0, 'peachtree_id'=>'', 'sorting'=>'11'];
            $sales_detail_group[$index]['quantity'] += $sales['quantity'];

            if($sales['vat']!=0){
                if(!isset($sales_detail_group['clinic_vat']))
                    $sales_detail_group['clinic_vat'] = ['gl_account'=>$vat_gl_account, 'unit_price'=>0, 'description'=>'Clinic VAT', 'quantity'=>1, 'peachtree_id'=>'', 'sorting'=>'12'];

                $sales_detail_group['clinic_vat']['unit_price'] += $sales['quantity'] * $sales['unit_price'] * BluelimeConfiguration::VAT;
            }
        }
        return $sales_detail_group;
    }

    private function group_credit_sales_detail_pharmacy(array $sales_detail_group, array $sales_detail, string $vat_gl_account):array{
        foreach($sales_detail as $sales){
            $index = $sales['peachtree_id'].$sales['unit_price'];
            $is_compounding = false;

            if(str_starts_with($sales['item_type'],'Compounding') && (empty(trim($sales['peachtree_id']))))
                $is_compounding = true;

            if($is_compounding){
                $index = $sales['description'];
                if(!isset($sales_detail_group[$index]))
                    $sales_detail_group[$index] = ['gl_account'=>$sales['gl_account'], 'unit_price'=>0, 'description'=>$sales['description'], 'quantity'=>0, 'peachtree_id'=>$sales['peachtree_id'], 'sorting'=>'15'];
            }
            elseif(!isset($sales_detail_group[$index]))
                $sales_detail_group[$index] = ['gl_account'=>$sales['gl_account'], 'unit_price'=>$sales['unit_price'], 'description'=>$sales['description'], 'quantity'=>0, 'peachtree_id'=>$sales['peachtree_id'], 'sorting'=>'12'];
            
            $sales_detail_group[$index]['quantity'] += $sales['quantity'];
            
            if($is_compounding){
                $sales_detail_group[$index]['quantity'] = 1;
                $sales_detail_group[$index]['unit_price'] += $sales['quantity'] * $sales['unit_price'];
            }


            if($sales['vat']!=0){
                $vat_index = 'pharmacy_vat';
                $vat_initial = ['gl_account'=>$vat_gl_account, 'unit_price'=>0, 'description'=>'Pharmacy VAT', 'quantity'=>1, 'peachtree_id'=>'', 'sorting'=>'13'];
                if($is_compounding){
                    $vat_index = 'compounding_vat';
                    $vat_initial = ['gl_account'=>$vat_gl_account, 'unit_price'=>0, 'description'=>'Compounding VAT', 'quantity'=>1, 'peachtree_id'=>'', 'sorting'=>'16'];
                }

                if(!isset($sales_detail_group[$vat_index]))
                    $sales_detail_group[$vat_index] = $vat_initial;
                $sales_detail_group[$vat_index]['unit_price'] += $sales['quantity'] * $sales['unit_price'] * BluelimeConfiguration::VAT;
            }
        }
        return $sales_detail_group;
    }
    

    private function get_peachtree_grv_data_common(string $supplier, $document_number, $date, $supplier_payable_account){
        $date = date_format(date_create($date),'m/d/Y');
        return [
                'Vendor ID' => $supplier,
                'Invoice/CM #' => $document_number, //'GRV'.$peachtree_config['branch']['number'].'#'.$grv_id,
                'Apply to Invoice Number' => '',
                'Credit Memo' => 'FALSE',
                'Date' => $date,
                'Drop Ship' => 'FALSE',
                'Customer SO #' => '',
                'Waiting on Bill' => 'FALSE',
                'Customer ID' => '',
                'Customer Invoice #' => '',
                'Ship to Name' => 'RANK Family PLC '.date('Y').' G.C',
                'Ship to Address-Line One' => '',
                'Ship to Address-Line Two' => '',
                'Ship to City' => '',
                'Ship to State' => '',
                'Ship to Zipcode' => '',
                'Ship to Country' => '',
                'Date Due' => '',
                'Discount Date' => $date,
                'Discount Amount' => 0,
                'Accounts Payable Account' => $supplier_payable_account,
                'Ship Via' => 'Airborne',
                'P.O. Note' => '',
                'Note Prints After Line Items' => 'FALSE',
                'Beginning Balance Transaction' => 'FALSE',
                'Applied To Purchase Order' => 'FALSE',
                'Number of Distributions' => 'Will be set later', //Will be calc later after checking vat
                'Invoice/CM Distribution' => 'Will be set later',
                'Apply to Invoice Distribution' => 0,
                'PO Number' => '',
                'PO Distribution' => 0,
                'Quantity' => 'Will be set later',
                'Stocking Quantity' => 'Will be set later',
                'Item ID' => 'Will be set later', //$peachtreeItems['item_id_'.$row['item_id']]['peachtree_id'].'_'.$peachtree_config['code_suffix']['store'],
                'Serial Number' => '',
                'U/M ID' => '<Each>',
                'U/M No. of Stocking Units' => 1,
                'Description' => 'Will be set later', //$peachtreeItems['item_id_'.$row['item_id']]['peachtree_description'],
                'G/L Account' => 'Will be set later', //(($row['item_detail']['item_type'] == 'DRUG') ? $peachtree_config['gl_account']['inventory']['drug'] : $peachtree_config['gl_account']['inventory']['cosmetics']).'-'.$peachtree_config['code_suffix']['store'],
                'Unit Price' => 'Will be set later', //$row['unit_price'],
                'Stocking Unit Price' => 'Will be set later', //$row['unit_price'],
                'UPC / SKU' => '',
                'Weight' => 0,
                'Amount' => 'Will be set later', //$row['unit_price'] * $row['quantity'],
                'Job ID' => '',
                'Used for Reimbursable Expense' => 'FALSE',
                'Transaction Period' => '',
                'Transaction Number' => '',
                'Displayed Terms' => 'Net 30 Days',
                'Return Authorization' => '',
                'Row Type' => 0,
                'Recur Number' => 0,
                'Recur Frequency' => 0
            ];
    }

    private function get_peachtree_sales_data_common($peachtree_id,$document_number, $date, $number_of_distributions): array {
        return [
                'Customer ID' => $peachtree_id,
                'Invoice/CM #' => trim($document_number),
                'Apply to Invoice Number' => '',
                'Credit Memo' => 'FALSE',
                'Progress Billing Invoice' => 'FALSE',
                'Date' => date_format(date_create($date),'m/d/Y'),
                'Ship By' => '',
                'Quote' => 'FALSE',
                'Quote #' => '',
                'Quote Good Thru Date' => '',
                'Drop Ship' => 'FALSE',
                'Ship to Name' => '',
                'Ship to Address-Line One' => '',
                'Ship to Address-Line Two' => '',
                'Ship to City' => '',
                'Ship to State' => '',
                'Ship to Zipcode' => '',
                'Ship to Country' => '',
                'Customer PO' => '',
                'Ship Via' => 'Airborne',
                'Ship Date' => '',
                'Date Due' => '',
                'Discount Amount' => 0,
                'Discount Date' => '',
                'Displayed Terms' => 'Net 30 Days',
                'Sales Representative ID' => '',
                'Accounts Receivable Account' => '1005-000',
                'Sales Tax ID' => '',
                'Invoice Note' => '',
                'Note Prints After Line Items' => 'FALSE',
                'Statement Note' => '',
                'Stmt Note Prints Before Ref' => 'FALSE',
                'Internal Note' => '',
                'Beginning Balance Transaction' => 'FALSE',
                'Number of Distributions' => $number_of_distributions,
                'Invoice/CM Distribution' => '',
                'Apply to Invoice Distribution' => 0,
                'Apply To Sales Order' => 'FALSE',
                'Apply to Proposal' => 'FALSE',
                'Quantity' => 1,
                'SO/Proposal Number' => '',
                'Item ID' => '',
                'Serial Number' => '',
                'SO/Proposal Distribution' => 0,
                'Description' => '',
                'G/L Account' => '',
                'Unit Price' => 0,
                'Tax Type' => 1,
                'UPC / SKU' => '',
                'Weight' => 0,
                'Amount' => '',
                'U/M ID' => '',
                'U/M No. of Stocking Units' => 1,
                'Stocking Quantity' => 0,
                'Stocking Unit Price' => 0,
                'Job ID' => '',
                'Sales Tax Agency ID' => '',
                'Transaction Period' => '',
                'Transaction Number' => '',
                'Return Authorization' => '',
                'Voided by Transaction' => '',
                'Retainage Percent' => 0,
                'Recur Number' => 0,
                'Recur Frequency' => 0
            ];
    }

    public function exportpaymentrequest(){
        $paymentRequestModel = model('Hospital/PaymentRequestModel');
        $payment_request_id = $_POST['id'];
        $document_number = $_POST['document_number'];
        $date = $_POST['date'];

        $paymentRequest = $paymentRequestModel->find($payment_request_id);
        $bill_id_list = BluelimeSystem::convert_to_array($paymentRequest['bill_id_list']);

        $peachtree_data = [];        
        if($paymentRequest['department'] == 'Clinic')
            $peachtree_data = $this->get_credit_sales_peachtree_data_clinic($bill_id_list);
        elseif($paymentRequest['department'] == 'Pharmacy')
            $peachtree_data = $this->get_credit_sales_peachtree_data_pharmacy($bill_id_list);


        $csv_data = [];
        $number_of_distributions = count($peachtree_data);
        $cnt =0;
        foreach($peachtree_data as $data){
            $cnt++;
            $csv_data_temp = $this->get_peachtree_sales_data_common($document_number, $date, $number_of_distributions);
            $csv_data_temp['Customer ID'] = 'SYSTEM_BLUELIME';
            //$csv_data_temp['Invoice/CM #'] = trim($document_number);
            //$csv_data_temp['Date'] = date_format(date_create($date),'m/d/Y');
            //$csv_data_temp['Number of Distributions'] = $number_of_distributions;
            $csv_data_temp['Invoice/CM Distribution'] = $cnt;
            $csv_data_temp['Description'] = $data['service_name'];
            $csv_data_temp['G/L Account'] = $data['gl_account'];
            $csv_data_temp['Quantity'] = $data['quantity'];
            //$csv_data_temp['Unit Price'] = $data['unit_price'];
            $csv_data_temp['Stocking Quantity'] = $data['quantity'];
            $csv_data_temp['Stocking Unit Price'] = $data['unit_price'];
            $csv_data_temp['Amount'] = -1 * ($data['quantity'] * $data['unit_price']);
            $csv_data [] = $csv_data_temp;
        }

        BluelimeSystem::export_arra_in_csv($csv_data, 'Credit Payment Request - '.$payment_request_id.'.csv');
        //BluelimeSystem::print_associated_array($csv_data,false);
    }

    private function get_credit_sales_peachtree_data_clinic($bill_id_list): array{

        $serviceModel = model('Hospital/ServiceModel');
        $services = $serviceModel->findAll();
        $services = BluelimeSystem::index_object_list_array_by_unique_field($services,'id');

        $receipt_list = $this->get_credit_report_data_clinic($bill_id_list);

        $peachtree_data = [];
        foreach($receipt_list as $receipt){
            $sold_services = $receipt['services'] ?? [];

            foreach($sold_services as $service){
                if(!isset($peachtree_data['id_'.$service['service_id'].'_'.$service['bill_amount']]))
                    $peachtree_data['id_'.$service['service_id'].'_'.$service['bill_amount']] = ['service_id' => $service['service_id'], 'service_name' => $service['service_name'], 'unit_price' => $service['bill_amount'], 'quantity'=>0, 'gl_account'=>$services['id_'.$service['service_id']]['gl_account']];
                $peachtree_data['id_'.$service['service_id'].'_'.$service['bill_amount']]['quantity'] += $service['bill_quantity'];
            }
        }

        return $peachtree_data;
    }

    private function get_credit_sales_peachtree_data_pharmacy($bill_id_list): array{
        $salesDetailModel = model('Inventory/SalesDetailModel');
        $peachtreeItemModel = model('Inventory/PeachtreeItemModel');
        $itemModel = model('Inventory/ItemModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $finance_maintain = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_integration = $finance_maintain[0]['data'];
        
        $peachtree_items = $peachtreeItemModel->findAll();
        $peachtree_items = BluelimeSystem::index_object_list_array_by_unique_field($peachtree_items,'item_id');
        $items = $itemModel->findAll();
        $items = BluelimeSystem::index_object_list_array_by_unique_field($items,'id');

        $salses_details = $salesDetailModel->join('inventory_sales_main', 'inventory_sales_main.id = inventory_sales_detail.sales_id', 'inner')
            ->whereIn('inventory_sales_main.pos_bill_id', $bill_id_list)->select('inventory_sales_detail.*, inventory_sales_main.sales_type')->findAll();

        $compounding_sales = [];
        $peachtree_data = [];
        $dispensary_vat = 0;
        foreach($salses_details as $salses_detail){
            if($salses_detail['sales_type'] == 'COMPOUNDING'){
                $compounding_sales[] = $salses_detail;
                continue;
            }

            $gl_account = '';

            if($items['id_'.$salses_detail['item_id']]['item_type'] == 'DRUG')
                $gl_account = $peachtree_integration['gl_account']['sales']['drug'];
            else
                $gl_account = $peachtree_integration['gl_account']['sales']['cosmetics'];
            
            $gl_account .= '-'.$peachtree_integration['code_suffix']['dispensary'];

            if(!isset($peachtree_data['id_'.$salses_detail['item_id'].'_'.$salses_detail['unit_price']]))
                $peachtree_data['id_'.$salses_detail['item_id'].'_'.$salses_detail['unit_price']] = ['service_id' => $peachtree_items['item_id_'.$salses_detail['item_id']]['peachtree_id'], 'service_name' => $peachtree_items['item_id_'.$salses_detail['item_id']]['peachtree_description'], 'unit_price' => $salses_detail['unit_price'], 'quantity'=>0, 'gl_account'=>$gl_account];

            $peachtree_data['id_'.$salses_detail['item_id'].'_'.$salses_detail['unit_price']]['quantity'] += $salses_detail['quantity'];
            $dispensary_vat += $salses_detail['quantity'] * $salses_detail['vat'];
        }

         if($dispensary_vat > 0)
            $peachtree_data['dispensary_vat'] = ['service_id' => '', 'service_name' => 'Compounding VAT', 'unit_price' => $dispensary_vat, 'quantity'=>1, 'gl_account'=>$peachtree_integration['gl_account']['vat']];
        

        $compounding_drugs =  ['service_id' => '', 'service_name' => 'Compounding Sales', 'unit_price' => 0, 'quantity'=>1];
        $compounding_taxables =  ['service_id' => '', 'service_name' => 'Compounding Taxable Sales', 'unit_price' => 0, 'quantity'=>1];
        $compounding_vat = 0;
        foreach($compounding_sales as $compounding_sale){
            if($compounding_sale['vat'] == 0)
                $compounding_drugs['unit_price'] += $compounding_sale['unit_price'] * $compounding_sale['quantity'];
            else
                $compounding_taxables['unit_price'] += $compounding_sale['unit_price'] * $compounding_sale['quantity'];
            $compounding_vat += $compounding_sale['vat'] * $compounding_sale['quantity'];
        }


        if($compounding_drugs['unit_price'] > 0){
            $compounding_drugs['gl_account'] = $peachtree_integration['gl_account']['compounding_sales']['drug'].'-'.$peachtree_integration['code_suffix']['dispensary'];
            $peachtree_data['compounding_drugs'] = $compounding_drugs;
        }

        if($compounding_taxables['unit_price'] > 0){
            $compounding_taxables['gl_account'] = $peachtree_integration['gl_account']['compounding_sales']['cosmetics'].'-'.$peachtree_integration['code_suffix']['dispensary'];
            $peachtree_data['compounding_cosmetics'] = $compounding_taxables;
        }

        if($compounding_vat > 0)
            $peachtree_data['dispensary_vat'] = ['service_id' => '', 'service_name' => 'Compounding VAT', 'unit_price' => $compounding_vat, 'quantity'=>1, 'gl_account'=>$peachtree_integration['gl_account']['vat']];
        

        return $peachtree_data;
    }

    public function paymentrequestupdatecollected()
    {
        $id = $_POST['id'] ?? -1;
        $remark = $_POST['remark'] ?? '';
        $status = "Collected";

        $paymentRequestModel = model('Hospital/PaymentRequestModel');

        $data = [
            'status' => $status,
            'remark' => $remark,
        ];

        $fail = '';
        if(!$paymentRequestModel->update($id, $data))
            $fail = "&fail=1";

        return redirect()->to(base_url()."inventory/finance/viewcreditpaymentrequest?id=$id$fail");
        
    }

    public function pending_purchase_request(): string
    {
        $search_key = $_GET['search_key'] ?? '';

        $purchaseMainModel = model('Inventory/PurchaseMainModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');
        
        if(trim($search_key) != '')
            $purchaseMainModel = $purchaseMainModel->where('purchase_code LIKE', "%$search_key%");

        $purchase_main = $purchaseMainModel->orderBy('id','DESC')->findAll(20,0);
        
        $stores = $storeModel->findAll();
        $users = $userModel->findAll();
        
        foreach($purchase_main as $index => $purchase){
            foreach($stores as $store){
                if($store['id'] == $purchase['store_id']){
                    $purchase_main[$index]['store_name'] = $store['store_name'];
                    break;
                }
            }
            foreach($users as $user){
                if($user['id'] == $purchase['requested_by']){
                    $purchase_main[$index]['name'] = $user['name'];
                    break;
                }
            }
           
        }

        $data['active_menu'] = 'pendingpurchase';
        $data['purchase_main'] = $purchase_main;

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Finance/_left_sidebar_finance')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Finance/pending_purchase_request')
                .view('Include/_footer');     
    }

    public function purchase_print(): string
    {
        $purchase_id = $_GET['purchase_id'] ?? -1;
        $purchaseMainModel = model('Inventory/PurchaseMainModel');
        $purchaseDetailModel = model('Inventory/PurchaseDetailModel');
        $supplierModel = model('Inventory/SupplierModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');


        $purchase_main = $purchaseMainModel->find($purchase_id);
        $purchase_detail = $purchaseDetailModel->where('purchase_id', $purchase_id)->findAll();
        $store_name = $storeModel->where('id',$purchase_main['store_id'])->findColumn('store_name')[0];
        $requested_by = $userModel->where('id',$purchase_main['requested_by'])->findColumn('name');

        

        $supplier = $supplierModel->find($purchase_main['supplier_id']);
        $purchase_main['supplier_detail'] = $supplier;
        $purchase_main['store_name'] = $store_name;
        $purchase_main['requested_by'] = $requested_by;

        $data = [
            'purchase_main' => $purchase_main,
            'purchase_detail' => $purchase_detail,
        ];
        $data['active_menu'] = 'purchase_form';
        
        return view('Inventory/Finance/purchase_print', $data);
    }

    public function purchase_order(): string
    {
        $logged_user_data = BluelimeAuthentication::get_logged_user_data();
        $purchaseMainModel = model('Inventory/PurchaseMainModel');
        $purchaseDetailModel = model('Inventory/PurchaseDetailModel');
        $itemModel = model('Inventory/ItemModel');
        $supplierModel = model('Inventory/SupplierModel');
        $storeModel = model('Inventory/StoreModel');

        $purchase_id = $_GET['purchase_id'] ??0;        
        $purchase_main = $purchaseMainModel->find($purchase_id);
        $purchase_detail = $purchaseDetailModel->where('purchase_id',$purchase_id)->findAll();

        $items = $itemModel->findAll();
        $suppliers = $supplierModel->findAll();
        $supplier = $supplierModel->where('id',$purchase_main['supplier_id'])->find('company_name');
        $store = $storeModel->find($logged_user_data['extra']['default_store'] ?? 0);
        $default_store = $store ?? ['store_name' => '', 'location'=>''];
       
        
            
           
        
        $item_j = [];
        foreach($items as $item)
            $item_j['item_id_'.$item['id']] = $item;

        $data['active_menu'] = 'purchase_form';
        $data['items'] = $item_j;
        $data['purchase_main'] = $purchase_main;
        $data['purchase_detail'] = $purchase_detail;
        $data['suppliers'] = $suppliers;
        $data['supplier'] = $supplier;
        $data['logged_user_data'] = $logged_user_data;
        $data['default_store'] = $default_store;

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Finance/_left_sidebar_finance')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Finance/purchase_form')
                .view('Include/_footer');     
    }

    public function purchaseupdate()
    {
        $logged_user_data = BluelimeAuthentication::get_logged_user_data();
        $purchaseMainModel = model('Inventory/PurchaseMainModel');
        $purchase_id = intval($_POST['purchase_id']);


        $data = [
                'is_ordered' => 1,
                'ordered_date' => date('Y-m-d h:i:s'),
                'ordered_by' => intval($logged_user_data['id'])
        ];
        
        //BluelimeSystem::show_and_die($data,$purchase_id);
        $purchaseMainModel->update($purchase_id, $data);        

        return '{"status":"success", "value":"'.$purchase_id.'"}';
    }

    public function purchase_order_print(): string
    {
        $purchase_id = $_GET['purchase_id'] ?? -1;
        $purchaseMainModel = model('Inventory/PurchaseMainModel');
        $purchaseDetailModel = model('Inventory/PurchaseDetailModel');
        $supplierModel = model('Inventory/SupplierModel');
        $userModel = model('System/UserModel');

        $purchase_main = $purchaseMainModel->find($purchase_id);
        $purchase_detail = $purchaseDetailModel->where('purchase_id', $purchase_id)->findAll();
        $ordered_by = $userModel->where('id',$purchase_main['ordered_by'])->findColumn('name');
        

        $supplier = $supplierModel->find($purchase_main['supplier_id']);
        $purchase_main['supplier_detail'] = $supplier;
        $purchase_main['ordered_by'] = $ordered_by;

        $data = [
            'purchase_main' => $purchase_main,
            'purchase_detail' => $purchase_detail,
        ];
        $data['active_menu'] = 'pendingpurchase';
        
        return view('Inventory/Finance/purchase_order_print', $data);
    } 
        public function purchase_order_list(): string
    {
        $purchaseMainModel = model('Inventory/PurchaseMainModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');
        
        
        $purchase_main = $purchaseMainModel->where('is_ordered', 1)->orderBy('id','DESC')->findAll(20,0);
        $stores = $storeModel->findAll();
        $users = $userModel->findAll();
        
        foreach($purchase_main as $index => $purchase){
            foreach($stores as $store){
                if($store['id'] == $purchase['store_id']){
                    $purchase_main[$index]['store_name'] = $store['store_name'];
                    break;
                }
            }
            foreach($users as $user){
                if($user['id'] == $purchase['requested_by']){
                    $purchase_main[$index]['name'] = $user['name'];
                    break;
                }
            }
           
        }

        $data['active_menu'] = 'pendingpurchase';
        $data['purchase_main'] = $purchase_main;

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Finance/_left_sidebar_finance')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Finance/purchase_order_list')
                .view('Include/_footer');     
    }  

    public function receive_grv(){
        $grv_id = $_GET['grv_id'] ?? -1;
        $grvMainModel = model('Inventory/GRVMainModel');
        $grv_main = $grvMainModel->find($grv_id);

        if($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST){
            $grvMainModel->update($grv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND]);
            return redirect()->to('/inventory/finance/grv_print?grv_id='.$grv_id);
        }
        return redirect()->to('/inventory/finance');
    }

    public function return_grv(){
        $grv_id = $_GET['grv_id'] ?? -1;
        $grvMainModel = model('Inventory/GRVMainModel');
        $grv_main = $grvMainModel->find($grv_id);

        if($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND){
            $grvMainModel->update($grv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST]);
        }
        return redirect()->to('/inventory/finance');
    }

    public function revert_grv(){
        $grv_id = $_POST['grv_id'] ?? -1;
        $grvMainModel = model('Inventory/GRVMainModel');
        $grv_main = $grvMainModel->find($grv_id);

        if($grv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::EXPORTED_FOR_PEACHTREE){
            $grvMainModel->update($grv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND]);
            return BluelimeSystem::get_success_ajax_return('Done');
        }
        return BluelimeSystem::get_error_ajax_return('GRV status was not Exported to peachtree');
    }

    public function export_grv(){
        $grv_id = $_GET['grv_id'] ?? -1;
        $grvMainModel = model('Inventory/GRVMainModel');
        $grv_main = $grvMainModel->find($grv_id);

        if($grv_main['status'] >= BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND){
            $grvMainModel->update($grv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::EXPORTED_FOR_PEACHTREE]);
            $this->export_grv_csv($grv_id);
        }
        echo 'You shold not see this unless there is an error.';
    }

    private function export_grv_csv($grv_id){
        $grvMainModel = model('Inventory/GRVMainModel');
        $grvDetailModel = model('Inventory/GRVDetailModel');
        $supplierModel = model('Inventory/SupplierModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $financeMaintain = $financeMaintainModel->where('department', 'inventory')->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
       
        $grv_main = $grvMainModel->find($grv_id);
        $supplier = $supplierModel->find($grv_main['supplier_id']);

        $grv_detail = $grvDetailModel->where('grv_id', $grv_id)->findAll();

        $peachtreeItems = BluelimeSystem::gather_objects_by_reference($grv_detail,'item_id','Inventory/PeachtreeItemModel','item_id',true);
        $peachtree_config = $financeMaintain[0]['data'];

        //$received_date = date_format(date_create($grv_main['recieved_date']),'m/d/Y');

        $csv_data = [];
        //BluelimeSystem::print_associated_array($grv_detail);
        $vat = 0;
        $cnt = 1;
        foreach($grv_detail as $row){
             
            /* $temp = [
                'Vendor ID' => $supplier['peachtree_id'],
                'Invoice/CM #' => 'GRV'.$peachtree_config['branch']['number'].'#'.$grv_id,
                'Apply to Invoice Number' => '',
                'Credit Memo' => 'FALSE',
                'Date' => $received_date,
                'Drop Ship' => 'FALSE',
                'Customer SO #' => '',
                'Waiting on Bill' => 'FALSE',
                'Customer ID' => '',
                'Customer Invoice #' => '',
                'Ship to Name' => 'RANK Family PLC '.date('Y').' G.C',
                'Ship to Address-Line One' => '',
                'Ship to Address-Line Two' => '',
                'Ship to City' => '',
                'Ship to State' => '',
                'Ship to Zipcode' => '',
                'Ship to Country' => '',
                'Date Due' => '',
                'Discount Date' => $received_date,
                'Discount Amount' => 0,
                'Accounts Payable Account' => $supplier['payable_account'],
                'Ship Via' => 'Airborne',
                'P.O. Note' => '',
                'Note Prints After Line Items' => 'FALSE',
                'Beginning Balance Transaction' => 'FALSE',
                'Applied To Purchase Order' => 'FALSE',
                'Number of Distributions' => 1, //Will be calc later after checking vat
                'Invoice/CM Distribution' => $cnt,
                'Apply to Invoice Distribution' => 0,
                'PO Number' => '',
                'PO Distribution' => 0,
                'Quantity' => $row['quantity'],
                'Stocking Quantity' => $row['quantity'],
                'Item ID' => $peachtreeItems['item_id_'.$row['item_id']]['peachtree_id'].'_'.$peachtree_config['code_suffix']['store'],
                'Serial Number' => '',
                'U/M ID' => '<Each>',
                'U/M No. of Stocking Units' => 1,
                'Description' => $peachtreeItems['item_id_'.$row['item_id']]['peachtree_description'],
                'G/L Account' => (($row['item_detail']['item_type'] == 'DRUG') ? $peachtree_config['gl_account']['inventory']['drug'] : $peachtree_config['gl_account']['inventory']['cosmetics']).'-'.$peachtree_config['code_suffix']['store'],
                'Unit Price' => $row['unit_price'],
                'Stocking Unit Price' => $row['unit_price'],
                'UPC / SKU' => '',
                'Weight' => 0,
                'Amount' => $row['unit_price'] * $row['quantity'],
                'Job ID' => '',
                'Used for Reimbursable Expense' => 'FALSE',
                'Transaction Period' => '',
                'Transaction Number' => '',
                'Displayed Terms' => 'Net 30 Days',
                'Return Authorization' => '',
                'Row Type' => 0,
                'Recur Number' => 0,
                'Recur Frequency' => 0
            ];
            */

            $documnet_number = 'GRV'.$peachtree_config['branch']['number'].'#'.$grv_id;
            $temp = $this->get_peachtree_grv_data_common($supplier['peachtree_id'], $documnet_number, $grv_main['receipt_date_gc'], $supplier['payable_account']);
            $temp['Invoice/CM Distribution'] = $cnt;
            $temp['Quantity'] = $row['quantity'];
            $temp['Stocking Quantity'] = $row['quantity'];
            $temp['Item ID'] = $peachtreeItems['item_id_'.$row['item_id']]['peachtree_id'].'_'.$peachtree_config['code_suffix']['store'];
            $temp['Description'] = $peachtreeItems['item_id_'.$row['item_id']]['peachtree_description'];
            $temp['G/L Account'] = (($row['item_detail']['item_type'] == 'DRUG') ? $peachtree_config['gl_account']['inventory']['drug'] : $peachtree_config['gl_account']['inventory']['cosmetics']).'-'.$peachtree_config['code_suffix']['store'];
            $temp['Unit Price'] = $row['unit_price'];
            $temp['Stocking Unit Price'] = $row['unit_price'];
            $temp['Amount'] = $row['unit_price'] * $row['quantity'];

            array_push($csv_data, $temp);
            $vat += $row['tax'];
            $cnt++;
        }

        if($vat>0){
            $temp = $csv_data[0];
            $temp['Invoice/CM Distribution'] = $cnt;
            $temp['Quantity'] = 0;
            $temp['Stocking Quantity'] = 0;
            $temp['Item ID'] = '';
            $temp['U/M ID'] = '';
            $temp['Description'] = '';
            $temp['G/L Account'] = '2200-000'; // VAT
            $temp['Unit Price'] = 0;
            $temp['Stocking Unit Price'] = 0;
            $temp['Amount'] = $vat;
            array_push($csv_data, $temp);
        }

        foreach($csv_data as $index => $value){
            $csv_data[$index]['Number of Distributions'] = count($csv_data);
        }

        BluelimeSystem::export_arra_in_csv($csv_data, "GRV-$grv_id ".$_ENV['BRANCH'].".csv");
    }

    public function grv_print(): string
    {
        $grv_id = $_GET['grv_id'] ?? -1;
        $grvMainModel = model('Inventory/GRVMainModel');
        $grvDetailModel = model('Inventory/GRVDetailModel');
        $supplierModel = model('Inventory/SupplierModel');
        $storeModel = model('Inventory/StoreModel');
        //$itemModel = model('Inventory/ItemModel');
        $userModel = model('System/UserModel');
        $logged_user_data = BluelimeAuthentication::get_logged_user_data();

        $grv_main = $grvMainModel->find($grv_id);
        $recieved_by = $userModel->where('id',$grv_main['recieved_by'])->findColumn('name');
        $store_name = $storeModel->where('id',$grv_main['store_id'])->findColumn('store_name');
        
        $grv_detail = $grvDetailModel->where('grv_id', $grv_id)->findAll();

        $items = BluelimeSystem::gather_objects_by_reference($grv_detail,'item_id','Inventory/ItemModel','id',true);

        $sub_total = 0;
        $tax = 0;
        $total = 0;
        foreach($grv_detail as $index => $grv_line){
            $total_price = ($grv_line['quantity'] * $grv_line['unit_price']);
            $grv_detail[$index]['total_price'] = $total_price;
            $grv_detail[$index]['item_detail'] = $items['id_'.$grv_line['item_id']] ?? $grv_line['item_detail'];
            $sub_total += $total_price;
            $tax += $grv_line['tax'];
            $total += $total_price;
        }

        $grv_main['recieved_by'] = $recieved_by;
        $grv_main['store_name'] = $store_name;
        $grv_main['sub_total'] = $sub_total;
        $grv_main['tax'] = $tax;
        $grv_main['total'] = $total + $tax;

        $supplier = $supplierModel->find($grv_main['supplier_id']);
        $grv_main['supplier_detail'] = $supplier;
        
        $data = [
            'grv_main' => $grv_main,
            'grv_detail' => $grv_detail,
            'logged_user_data' => $logged_user_data,
        ];
        
        $data['active_menu'] = 'grv';
        $data['active_sub_menu'] = 'grv_list';
        
        return view('Inventory/Finance/grv_print', $data);
    }
    public function vat_purchases(): string
    {
        $from_date = $_GET['from_date'] ?? date('Y-m-d');
        $to_date =  $_GET['to_date'] ?? date('Y-m-d');

        $grvMainModel = model('Inventory/GRVMainModel');
        $grvDetailModel = model('Inventory/GRVDetailModel');
        $supplierModel = model('Inventory/SupplierModel');

        $grv_mains = $grvMainModel->where('status >=', BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND)
            ->where('receipt_date_gc >=', $from_date)
            ->where('receipt_date_gc <=', $to_date)
            ->orderBy('receipt_date_gc', 'DESC')
            ->findAll();
        $grv_mains = BluelimeSystem::index_object_list_array_by_unique_field($grv_mains, 'id');
        
        $grv_details = $grvDetailModel->whereIn('grv_id', [-99, ...array_column($grv_mains,'id')])->where('tax >',0)->findAll();
        $suppliers = $supplierModel->findAll();
        $suppliers = BluelimeSystem::index_object_list_array_by_unique_field($suppliers, 'id');

        foreach($grv_details as $index => $grv_detail){
            $grv_details[$index]['grv_main'] = $grv_mains['id_'.$grv_detail['grv_id']];
            $grv_details[$index]['supplier'] = $suppliers['id_'.$grv_details[$index]['grv_main']['supplier_id']];
        }

        $data['grv_details'] = $grv_details;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['active_menu'] = 'grv';
        $data['active_sub_menu'] = 'vat_purchases';

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Finance/_left_sidebar_finance')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Finance/vat_purchases')
                .view('Include/_footer'); 
    }

    public function none_vat_purchases(): string
    {
        $from_date = $_GET['from_date'] ?? date('Y-m-d');
        $to_date =  $_GET['to_date'] ?? date('Y-m-d');

        $grvMainModel = model('Inventory/GRVMainModel');
        $grvDetailModel = model('Inventory/GRVDetailModel');
        $supplierModel = model('Inventory/SupplierModel');

        $grv_mains = $grvMainModel->where('status >=', BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND)
            ->where('receipt_date_gc >=', $from_date)
            ->where('receipt_date_gc <=', $to_date)
            ->orderBy('receipt_date_gc', 'DESC')
            ->findAll();
        $grv_mains = BluelimeSystem::index_object_list_array_by_unique_field($grv_mains, 'id');
        
        $grv_details = $grvDetailModel->select('grv_id, SUM(unit_price*quantity) as total')->whereIn('grv_id', [-99, ...array_column($grv_mains,'id')])->where('tax =',0)->groupBy('grv_id')->findAll();
        $suppliers = $supplierModel->findAll();
        $suppliers = BluelimeSystem::index_object_list_array_by_unique_field($suppliers, 'id');

        foreach($grv_details as $index => $grv_detail){
            $grv_details[$index]['grv_main'] = $grv_mains['id_'.$grv_detail['grv_id']];
            $grv_details[$index]['supplier'] = $suppliers['id_'.$grv_details[$index]['grv_main']['supplier_id']];
        }

        $data['grv_details'] = $grv_details;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['active_menu'] = 'grv';
        $data['active_sub_menu'] = 'none_vat_purchases';

        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Finance/_left_sidebar_finance')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Finance/none_vat_purchases')
                .view('Include/_footer'); 
    }

    public function uploaddocument()
    {
        $grv_id     = $_POST['grv_id'];
        
        $image_list = $this->upload_document();

        return redirect()->to(site_url('/inventory/finance/grv_print?grv_id='.$grv_id.'&image_list='.$image_list));
    } 
    private function upload_document($quality = -1){
        $grvMainModel = model('Inventory/GRVMainModel');
        
        $image_list = trim($_POST['image_list'] ?? '');
        
        $grv_id     = $_POST['grv_id'];
       
        $originalImage = $_FILES['picture']['tmp_name'];
        
        $fl1Name = sprintf('%05d', $grv_id).'_'.date('Ymdhms');
        
        $file_path = $_ENV['CASHIER_DOCUMENT_PATH'];

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
            $img = $grvMainModel->where('id',$grv_id)->find()[0];
            $upload_img = $img['scanned_documents']==""?$fl1Name.'.jpg':$img['scanned_documents'].','.$fl1Name.'.jpg';
            
        
            $rt = $grvMainModel->update($grv_id, ['scanned_documents' => $upload_img]);
           // BluelimeSystem::show_and_die($rt);
            if($image_list != '')
                $image_list .= ',';

            $image_list .= $fl1Name.'.jpg';

        }
        return $image_list;
    }
    function delete_document(){
       $image_name = $_POST['image_name'];
        $grv_id = $_POST['grv_id'] ?? -1;
        $grvMainModel = model('Inventory/GRVMainModel');
        $grv = $grvMainModel->find($grv_id);

        $scanned_documents = $grv['scanned_documents'];
        $scanned_documents = trim(str_replace($image_name,'',$scanned_documents),',');

        $image_path = $_ENV['PROGRESS_IMAGE_PATH'].$image_name;

        $grvMainModel->update($grv_id, ['scanned_documents'=>$scanned_documents]);

        if(is_file($image_path)){
            unlink($image_path);
        }
        return BluelimeSystem::get_success_ajax_return('');
    }
    public function cashierdocument(){
        ob_clean();
        header('Content-type: image');

        $image = $_GET['image'];
        $thumbnail = $_GET['thumbnail'] ?? false;
        $file_path = $file_path = $_ENV['CASHIER_DOCUMENT_PATH'];

        $im = imagecreatefromjpeg($file_path.$image);
        if($thumbnail)
            $im = imagescale($im,1000);
        imagejpeg($im, null,100);
        imagedestroy($im);
        die();
    }

    public function update_peachtree_supplier_id(){
        $id = $_POST['id'];
        $peachtree_id = $_POST['peachtree_id'];
        $payable_account = $_POST['payable_account'];
        $supplierModel = model('Inventory/SupplierModel');

        $supplierModel->update($id,['peachtree_id'=>$peachtree_id, 'payable_account'=>$payable_account]);
        return BluelimeSystem::get_success_ajax_return('');
    }
    public function update_cpv(){
        $grvMainModel = model('Inventory/GRVMainModel');
        $id = $_POST['id'];
        $cpv_number = $_POST['cpv_number'];
        
        $data = [
            'cpv'=>$cpv_number,
        ];

        $id = $grvMainModel->update($id,$data);
        //BluelimeSystem::show_and_die($id);
        return BluelimeSystem::get_success_ajax_return('');
    }

    public function update_grv(){
        $grvMainModel = model('Inventory/GRVMainModel');
        $id = $_POST['id'];
        
        $fs_number = $_POST['fs_number'];
        $machine_no = $_POST['machine_no'];
        $is_manual_receipt = ($_POST['is_manual_receipt'] ?? 0)?1:0;
        $receipt_date_date = $_POST['receipt_date_date'];
        $receipt_date_month = $_POST['receipt_date_month'];
        $receipt_date_year = $_POST['receipt_date_year'];
        $is_ethiopian_calander = ($_POST['is_ethiopian_calander'] ?? 0)?1:0;

        $receipt_date = "$receipt_date_date/$receipt_date_month/$receipt_date_year";
        $receipt_date_gc = "$receipt_date_year-$receipt_date_month-$receipt_date_date";
        if($is_ethiopian_calander){
            $receipt_date_gc = BluelimeSystem::convert_ec_to_gc($receipt_date_year, $receipt_date_month, $receipt_date_date);
        }

        $grv_date = [
            'fs_no' => $fs_number,
            'machine_no' => $machine_no,
            'is_manual_receipt' => $is_manual_receipt,
            'receipt_date' => $receipt_date,
            'receipt_date_gc' => $receipt_date_gc,
            'is_ethiopian_calander' => $is_ethiopian_calander
        ];

        if($grvMainModel->update($id, $grv_date)){
            return BluelimeSystem::get_success_ajax_return('done');
        }

        return BluelimeSystem::get_error_ajax_return('Update is not done.');
    }

    public function siv_list(): string
    {
        $search_key = $_GET['search_key'] ?? '';

        $sivMainModel = model('Inventory/SIVMainModel');
        $storeModel = model('Inventory/StoreModel');
        
        
        if(trim($search_key) != '')
            $sivMainModel = $sivMainModel->groupStart()->where('srv_id LIKE', "%$search_key%")->orWhere('id LIKE', "%$search_key%")->orWhere('description LIKE', "%$search_key%")->groupEnd();

        $siv_main = $sivMainModel->where('status >', BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST)->orderBy('id','DESC')->paginate(BluelimeConfiguration::NUMBER_OF_ROW_OF_LIST);
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

        $data['active_menu'] = 'pendingsiv';
        $data['stores'] = $stores;
        $data['siv_main'] = $siv_main;
        $data['search_key'] = $search_key;
        $data['pager'] = $sivMainModel->pager;


        return   view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Finance/_left_sidebar_finance')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Finance/siv_list')
                .view('Include/_footer');     
    }

    public function siv_print(): string
    {
        $siv_id = $_GET['siv_id'] ?? -1;
        $sivMainModel = model('Inventory/SIVMainModel');
        $sivDetailModel = model('Inventory/SIVDetailModel');
        $storeModel = model('Inventory/StoreModel');
        $userModel = model('System/UserModel');
       
        $siv_main = $sivMainModel->find($siv_id);

        //$issued_by = $userModel->where('id',$siv_main['issued_by'])->findColumn('name');
        $issuing_store_name = $storeModel->where('id',$siv_main['issuing_store'])->findColumn('store_name');
        $requesting_store_name = $storeModel->where('id',$siv_main['requesting_store'])->findColumn('store_name');
        
        $siv_detail = $sivDetailModel->where('siv_id', $siv_id)->findAll();

       
        $issued_by = $userModel->where('id',$siv_main['issued_by'])->findColumn('name');
        $received_by = $userModel->where('id',$siv_main['recieved_by'])->findColumn('name');
        $siv_main['issued_by_name'] = $issued_by[0] ?? '';
        $siv_main['received_by_name'] = $received_by[0] ?? '';
        
        $siv_main['issuing_store_name'] = $issuing_store_name[0];
        $siv_main['requesting_store_name'] = $requesting_store_name[0];

        $items = BluelimeSystem::gather_objects_by_reference($siv_detail,'item_id','Inventory/ItemModel','id',true);

        
        foreach($siv_detail as $index => $siv_line){
            $siv_detail[$index]['item_detail'] = $items['id_'.$siv_line['item_id']] ?? $siv_line['item_detail'];
        }

        $data = [
            'siv_main' => $siv_main,
            'siv_detail' => $siv_detail,
        ];
       
        
        $data['active_menu'] = 'pendingsiv';
        
        return view('Inventory/Finance/siv_print', $data);
    }

    public function receive_siv(){
        $siv_id = $_GET['siv_id'] ?? -1;
        $sivMainModel = model('Inventory/SIVMainModel');
        $siv_main = $sivMainModel->find($siv_id);

        if($siv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND){
            $sivMainModel->update($siv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::APPROVED_THIRD]);
            return redirect()->to('/inventory/finance/siv_print?siv_id='.$siv_id);
        }
        return redirect()->to('/inventory/finance/siv_list');
    }

    public function return_siv(){
        $siv_id = $_GET['siv_id'] ?? -1;
        $sivMainModel = model('Inventory/SIVMainModel');
        $siv_main = $sivMainModel->find($siv_id);

        if($siv_main['status'] == BluelimeConfiguration::VOUCHER_SATUS::APPROVED_THIRD){
            $sivMainModel->update($siv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::APPROVED_SECOND]);
        }
        return redirect()->to('/inventory/finance/siv_list');
    }

    public function export_siv(){
        $siv_id = $_GET['siv_id'] ?? -1;
        $sivMainModel = model('Inventory/SIVMainModel');
        $siv_main = $sivMainModel->find($siv_id);

        if($siv_main['status'] >= BluelimeConfiguration::VOUCHER_SATUS::APPROVED_THIRD){
            $sivMainModel->update($siv_id, ['status' => BluelimeConfiguration::VOUCHER_SATUS::EXPORTED_FOR_PEACHTREE]);
            $this->export_siv_csv_single($siv_id);
        }
        echo 'You shold not see this unless there is an error.';
    }

     private function create_siv_csv($siv_id, $peachtree_config){
        //get_peachtree_sales_data_common
        $sivMainModel = model('Inventory/SIVMainModel');
        $sivDetailModel = model('Inventory/SIVDetailModel');
        $storeModel = model('Inventory/StoreModel');
        //$financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $siv_main = $sivMainModel->find($siv_id);
        $siv_detail = $sivDetailModel->where('siv_id', $siv_id)->findAll();
        //$financeMaintain = $financeMaintainModel->where('department', 'inventory')->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();

        $peachtreeItems = BluelimeSystem::gather_objects_by_reference($siv_detail,'item_id','Inventory/PeachtreeItemModel','item_id',true);
        //$peachtree_config = $financeMaintain[0]['data'];

        $issuing_store_suffix = $peachtree_config['code_suffix']['store'];
        $requesting_store_name =  $storeModel->where('id',$siv_main['requesting_store'])->findColumn('store_name')[0];
        $requesting_store_suffix = $peachtree_config['code_suffix'][strtolower($requesting_store_name)];
         
        //$received_date = date_format(date_create($siv_main['created_at']),'m/d/Y');

        $csv_data = [];
        
        //s$sales_line = $this->get_peachtree_sales_data_common($siv_no, $received_date, count($siv_detail)*2);
        $customer_id = 'Gelead Dispensary'.$peachtree_config['branch']['number'];
        $siv_no = 'ISIT'.$peachtree_config['branch']['number'].'#'.$siv_id;
        
        $sales_line = $this->get_peachtree_grv_data_common($customer_id, $siv_no, $siv_main['created_at'],'2000-000');
        
        $cnt = 1;
        foreach($siv_detail as $row){

            $sales_line['Invoice/CM Distribution'] = $cnt++;
            $sales_line['Quantity'] = $row['quantity'];
            $sales_line['Stocking Quantity'] = $row['quantity'];
            $sales_line['Stocking Unit Price'] = $row['average_cost'];
            $sales_line['Item ID'] = $peachtreeItems['item_id_'.$row['item_id']]['peachtree_id'].'_'.$requesting_store_suffix;
            $sales_line['Description'] = $peachtreeItems['item_id_'.$row['item_id']]['peachtree_description'];
            $sales_line['G/L Account'] = (($row['item_detail']['item_type'] == 'DRUG') ? $peachtree_config['gl_account']['inventory']['drug'] : $peachtree_config['gl_account']['inventory']['cosmetics']).'-'.$peachtree_config['code_suffix']['dispensary'];
            $sales_line['Unit Price'] = $row['average_cost'];
            $sales_line['Amount'] = $row['average_cost'] * $row['quantity'];

            if(strtolower($requesting_store_name) == 'compounding')
                $sales_line['G/L Account'] = $peachtree_config['gl_account']['inventory']['compounding'].'-'.$peachtree_config['code_suffix']['dispensary'];
           
            $csv_data[] = $sales_line;

            $sales_line['Invoice/CM Distribution'] = $cnt++;
            $sales_line['Quantity'] = -1 * $row['quantity'];
            $sales_line['Stocking Quantity'] = -1 * $row['quantity'];
            $sales_line['Stocking Unit Price'] = $row['average_cost'];
            $sales_line['Item ID'] = $peachtreeItems['item_id_'.$row['item_id']]['peachtree_id'].'_'.$issuing_store_suffix;
            $sales_line['Description'] = $peachtreeItems['item_id_'.$row['item_id']]['peachtree_description'];
            $sales_line['G/L Account'] = (($row['item_detail']['item_type'] == 'DRUG') ? $peachtree_config['gl_account']['inventory']['drug'] : $peachtree_config['gl_account']['inventory']['cosmetics']).'-'.$peachtree_config['code_suffix']['store'];
            $sales_line['Unit Price'] = $row['average_cost'];
            $sales_line['Amount'] = -1 * $row['average_cost'] * $row['quantity'];

            $csv_data[] = $sales_line;
        }

        foreach($csv_data as $index => $value){
            $csv_data[$index]['Number of Distributions'] = count($csv_data);
        }

        return $csv_data;
    }

     private function export_siv_csv_single($siv_id){
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $financeMaintain = $financeMaintainModel->where('department', 'inventory')->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_config = $financeMaintain[0]['data'];
        $csv_data = $this->create_siv_csv($siv_id, $peachtree_config);
        BluelimeSystem::export_arra_in_csv($csv_data, "SIV".$peachtree_config['branch']['number']."-$siv_id ".$_ENV['BRANCH'].".csv");
     }

     public function export_siv_csv_multiple(){
        $export_button = $_GET['export_button'];
        if($export_button == "adjustment"){
            $this->export_siv_csv_multiple_via_adjustment();
        }
        else{
            $this->export_siv_csv_multiple_via_purchase();
        }
     }

     private function export_siv_csv_multiple_via_purchase(){
        $from = $_GET['from'];
        $to = $_GET['to'] ?? '';
        $include_exported = $_GET['include_exported'] ?? 0;
        $store = $_GET['store'] ?? [-99];

        if(!empty($to) && (strtotime($to) < strtotime($from))){
            $temp = $from;
            $from = $to;
            $to = $temp;
        }

        $from .= ' 00:00:00';
        if(!empty($to))
            $to .= ' 23:59:59';

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $financeMaintain = $financeMaintainModel->where('department', 'inventory')->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_config = $financeMaintain[0]['data'];

        $sivMainModel = model('Inventory/SIVMainModel');
        
        $sivMainModel = $sivMainModel->whereIn('requesting_store',$store)->where('created_at >=', $from);

        if(!empty($to))
            $sivMainModel = $sivMainModel->where('created_at <=', $to);

        if(!$include_exported){
            $sivMainModel = $sivMainModel->where('status <', BluelimeConfiguration::VOUCHER_SATUS::EXPORTED_FOR_PEACHTREE);
        }

        $sivMainModel = $sivMainModel->where('status >', BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST);

        $siv_main_ids = $sivMainModel->findColumn('id');

        //BluelimeSystem::show_and_die($sivMainModel->db->getLastQuery());

        if(empty($siv_main_ids)){
            BluelimeSystem::export_arra_in_csv([['EMPTY'=>""]], "SIV".$peachtree_config['branch']['number']."_EMPTY_".$_ENV['BRANCH'].".csv");
            die();
        }

        $sivMainModel->whereIn('id', $siv_main_ids)->update(row:['status' => BluelimeConfiguration::VOUCHER_SATUS::EXPORTED_FOR_PEACHTREE]);
        
        $csv_datas = [];
        foreach($siv_main_ids as $siv_id){
            $csv_data = $this->create_siv_csv($siv_id, $peachtree_config);
            $csv_datas = array_merge($csv_datas, $csv_data);
        }
        
        BluelimeSystem::export_arra_in_csv($csv_datas, "SIV".$peachtree_config['branch']['number']."_".$siv_main_ids[0]."-".$siv_main_ids[count($siv_main_ids)-1]." ".$_ENV['BRANCH'].".csv");
     }

     private function export_siv_csv_multiple_via_adjustment(){
        $from = $_GET['from'];
        $to = $_GET['to'] ?? '';
        $include_exported = $_GET['include_exported'] ?? 0;
        $store = $_GET['store'] ?? [-99];

        if(!empty($to) && (strtotime($to) < strtotime($from))){
            $temp = $from;
            $from = $to;
            $to = $temp;
        }

        $from .= ' 00:00:00';
        if(!empty($to))
            $to .= ' 23:59:59';

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $financeMaintain = $financeMaintainModel->where('department', 'inventory')->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_config = $financeMaintain[0]['data'];

        $sivMainModel = model('Inventory/SIVMainModel');
        
        $sivMainModel = $sivMainModel->whereIn('requesting_store',$store)->where('created_at >=', $from);

        if(!empty($to))
            $sivMainModel = $sivMainModel->where('created_at <=', $to);

        if(!$include_exported){
            $sivMainModel = $sivMainModel->where('status <', BluelimeConfiguration::VOUCHER_SATUS::EXPORTED_FOR_PEACHTREE);
        }

        $sivMainModel = $sivMainModel->where('status >', BluelimeConfiguration::VOUCHER_SATUS::APPROVED_FIRST);

        $siv_mains = $sivMainModel->findAll();

        //BluelimeSystem::show_and_die($sivMainModel->db->getLastQuery());

        if(empty($siv_mains)){
            BluelimeSystem::export_arra_in_csv([['EMPTY'=>""]], "SIV".$peachtree_config['branch']['number']."_EMPTY_".$_ENV['BRANCH'].".csv");
            die();
        }

        $siv_main_ids = array_column($siv_mains, 'id');

        $sivMainModel->whereIn('id', $siv_main_ids)->update(row:['status' => BluelimeConfiguration::VOUCHER_SATUS::EXPORTED_FOR_PEACHTREE]);

        $sivDetailModel = model('Inventory/SIVDetailModel');
        $siv_details = $sivDetailModel->whereIn('siv_id', $siv_main_ids)->findAll();

        $siv_mains = BluelimeSystem::index_object_list_array_by_unique_field($siv_mains, 'id');

        foreach($siv_details as $detail){
            $siv_mains['id_'.$detail['siv_id']]['details'] ??=[];
            $siv_mains['id_'.$detail['siv_id']]['details'][] = $detail;
        }

        $storeModel = model('Inventory/StoreModel');
        $stores = $storeModel->findAll();
        $stores = BluelimeSystem::index_object_list_array_by_unique_field($stores, 'id');

        $peachtreeItems = BluelimeSystem::gather_objects_by_reference($siv_details,'item_id','Inventory/PeachtreeItemModel','item_id',true);

        $issuing_store_suffix = $peachtree_config['code_suffix']['store'];


        $csv_data = [];
        foreach($siv_mains as $siv_main){
            $received_date = date_format(date_create($siv_main['created_at']),'m/d/Y');
            $siv_no = 'SIV'.$peachtree_config['branch']['number'].'#'.$siv_main['id'];
            
            $issuing_store_name = $stores['id_'.$siv_main['issuing_store']]['store_name'];
            $issuing_store_suffix = $peachtree_config['code_suffix'][strtolower($issuing_store_name)] ?? $peachtree_config['code_suffix']['store'];
            $requesting_store_name =  $stores['id_'.$siv_main['requesting_store']]['store_name'];
            $requesting_store_suffix = $peachtree_config['code_suffix'][strtolower($requesting_store_name)];

            $my_siv_details = $siv_main['details'];
            foreach($my_siv_details as $row){
                $temp = [
                    'Item ID' => $peachtreeItems['item_id_'.$row['item_id']]['peachtree_id'].'_'.$issuing_store_suffix,
                    'Reference' =>  $siv_no,
                    'Date' => $received_date,
                    'Job ID' => '',
                    'Reason to Adjust' => 'Issue to dispensary',
                    'Number of Distributions' => 1,
                    'G/L Source Account' => (($row['item_detail']['item_type'] == 'DRUG') ? $peachtree_config['gl_account']['inventory']['drug'] : $peachtree_config['gl_account']['inventory']['cosmetics']).'-'.$peachtree_config['code_suffix']['store'],
                    'Unit Cost' => '',
                    'Quantity' => -1*$row['quantity'],
                    'Amount' => '',
                    'Transaction Period' => '',
                    'Transaction Number' => '',
                    'Serial Number' => '',
                ];

                $csv_data[] = $temp;

                $temp['Item ID'] = $peachtreeItems['item_id_'.$row['item_id']]['peachtree_id'].'_'.$requesting_store_suffix;
                $temp['Quantity'] = $row['quantity'];
                $temp['Unit Cost'] = $row['unit_cost'];
                $temp['Amount'] = -1 * $row['unit_cost'] * $row['quantity'];
                $temp['Reason to Adjust'] = 'Receive from store';

                $csv_data[] = $temp;
            }
        
        }
        BluelimeSystem::export_arra_in_csv($csv_data, "SIV".$peachtree_config['branch']['number']."_".$siv_main_ids[0]."-".$siv_main_ids[count($siv_main_ids)-1]." ".$_ENV['BRANCH'].".csv");
     }
     


     private function export_siv_csv_old_2($siv_id){
        //get_peachtree_sales_data_common
        $sivMainModel = model('Inventory/SIVMainModel');
        $sivDetailModel = model('Inventory/SIVDetailModel');
        $storeModel = model('Inventory/StoreModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $siv_main = $sivMainModel->find($siv_id);
        $siv_detail = $sivDetailModel->where('siv_id', $siv_id)->findAll();
        $financeMaintain = $financeMaintainModel->where('department', 'inventory')->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();

        $peachtreeItems = BluelimeSystem::gather_objects_by_reference($siv_detail,'item_id','Inventory/PeachtreeItemModel','item_id',true);
        $peachtree_config = $financeMaintain[0]['data'];

        $issuing_store_suffix = $peachtree_config['code_suffix']['store'];
        $requesting_store_name =  $storeModel->where('id',$siv_main['requesting_store'])->findColumn('store_name')[0];
        $requesting_store_suffix = $peachtree_config['code_suffix'][strtolower($requesting_store_name)];
         
        $received_date = date_format(date_create($siv_main['created_at']),'m/d/Y');

        $csv_data = [];
        
        $siv_no = 'SIV'.$peachtree_config['branch']['number'].'#'.$siv_id;
        $sales_line = $this->get_peachtree_sales_data_common($siv_no, $received_date, count($siv_detail)*2);
        $sales_line['Customer ID'] = 'Gelead Dispensary'.$peachtree_config['branch']['number'];
        
        $cnt = 1;
        foreach($siv_detail as $row){
            $sales_line['Invoice/CM Distribution'] = $cnt++;
            $sales_line['Item ID'] = $peachtreeItems['item_id_'.$row['item_id']]['peachtree_id'].'_'.$issuing_store_suffix;
            $sales_line['G/L Account'] = (($row['item_detail']['item_type'] == 'DRUG') ? $peachtree_config['gl_account']['inventory']['drug'] : $peachtree_config['gl_account']['inventory']['cosmetics']).'-'.$peachtree_config['code_suffix']['store'];
            $sales_line['Quantity'] = $row['quantity'];
            $sales_line['Unit Price'] = $row['unit_price'];
            $sales_line['Amount'] = -1 * $row['unit_price'] * $row['quantity'];
            $sales_line['Stocking Quantity'] = $row['quantity'];
            $sales_line['Stocking Unit Price'] = $row['unit_price'];
            $csv_data[] = $sales_line;

            $sales_line['Invoice/CM Distribution'] = $cnt++;
            $sales_line['Item ID'] = $peachtreeItems['item_id_'.$row['item_id']]['peachtree_id'].'_'.$requesting_store_suffix;
            $sales_line['G/L Account'] = (($row['item_detail']['item_type'] == 'DRUG') ? $peachtree_config['gl_account']['inventory']['drug'] : $peachtree_config['gl_account']['inventory']['cosmetics']).'-'.$peachtree_config['code_suffix']['dispensary'];
            $sales_line['Quantity'] = -1 * $row['quantity'];
            $sales_line['Unit Price'] = $row['unit_price'];
            $sales_line['Amount'] = $row['unit_price'] * $row['quantity'];
            $sales_line['Stocking Quantity'] = -1 * $row['quantity'];
            $sales_line['Stocking Unit Price'] = $row['unit_price'];
            $csv_data[] = $sales_line;
        }

        BluelimeSystem::export_arra_in_csv($csv_data, "SIV".$peachtree_config['branch']['number']."-$siv_id ".$_ENV['BRANCH'].".csv");
     }

    /*
    This function is commented out because it is not used in the current version.
    */
    private function export_siv_csv_old($siv_id){
        $sivMainModel = model('Inventory/SIVMainModel');
        $sivDetailModel = model('Inventory/SIVDetailModel');
        $storeModel = model('Inventory/StoreModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $siv_main = $sivMainModel->find($siv_id);
        $siv_detail = $sivDetailModel->where('siv_id', $siv_id)->findAll();

        $financeMaintain = $financeMaintainModel->where('department', 'inventory')->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
       
        $peachtreeItems = BluelimeSystem::gather_objects_by_reference($siv_detail,'item_id','Inventory/PeachtreeItemModel','item_id',true);
        $peachtree_config = $financeMaintain[0]['data'];

        $issuing_store_suffix = $peachtree_config['code_suffix']['store'];
        $requesting_store_name =  $storeModel->where('id',$siv_main['requesting_store'])->findColumn('store_name')[0];
        $requesting_store_suffix = $peachtree_config['code_suffix'][strtolower($requesting_store_name)];
         
        $received_date = date_format(date_create($siv_main['created_at']),'m/d/Y');

        $csv_data = [];
        
        $cnt = 1;
        foreach($siv_detail as $row){
            $temp = [
                'Item ID' => $peachtreeItems['item_id_'.$row['item_id']]['peachtree_id'].'_'.$issuing_store_suffix,
                'Reference' =>  'SIV'.$peachtree_config['branch']['number'].'#'.$siv_id,
                'Date' => $received_date,
                'Job ID' => '',
                'Reason to Adjust' => '',
                'Number of Distributions' => 1,
                'G/L Source Account' => (($row['item_detail']['item_type'] == 'DRUG') ? $peachtree_config['gl_account']['inventory']['drug'] : $peachtree_config['gl_account']['inventory']['cosmetics']).'-'.$peachtree_config['code_suffix']['store'],
                'Unit Cost' => '',
                'Quantity' => -1*$row['quantity'],
                'Amount' => '',
                'Transaction Period' => '',
                'Transaction Number' => '',
                'Serial Number' => '',
            ];
            array_push($csv_data, $temp);

            $temp['Item ID'] = $peachtreeItems['item_id_'.$row['item_id']]['peachtree_id'].'_'.$requesting_store_suffix;
            $temp['Quantity'] = $row['quantity'];
            array_push($csv_data, $temp);
            $cnt++;
        }

        BluelimeSystem::export_arra_in_csv($csv_data, "SIV-$siv_id ".$_ENV['BRANCH'].".csv");
    }

    public function daily_sales_by_payment_type(): string{
        $date_filter = $_GET['date_filter'] ?? date("Y-m-d");
        $payment_method = $_GET['payment_method'] ?? '';
        $department = $_GET['department'] ?? 'Hospital';

        $payment_method_title = '';
        if(!empty($payment_method))
            $payment_method_title = BluelimeConfiguration::PAYMENT_METHOD::{$payment_method};

        $pOSBillModel = model("POS/POSBillModel");

        $pos_bills = $pOSBillModel->where('response_date', $date_filter)->where("type", $department)->where('payment_method', $payment_method_title)->orderBy('fs_number')->findAll();
        $total = 0;
        foreach($pos_bills as $row){
            //$row['transferred_amount'] = round($row['transferred_amount'],0);
            if(($row['payment_method'] == 'Cash') || ($row['payment_method'] == 'Credit'))
                $total += $row['total'];
            else
                $total += $row['transferred_amount'];
        }

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $finance_maintain = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::DAILY_SALES_BY_PAYMENT_TYPE)->where('department', $department)->findAll()[0]??[];

        $data = [
            'pos_bills'     => $pos_bills,
            'total'         => $total,
            'date_filter'   => $date_filter,
            'department'    => $department,
            'payment_method' => $payment_method,
            'finance_maintain' => $finance_maintain,
            'active_menu'   => "other_links",
            'active_sub_menu'   => "daily_sales_by_payment_type",
        ];

        if($department == 'Hospital')
            $data['active_menu'] = 'sales_report_clinic';

        return view('Inventory/Finance/daily_sales_by_payment_type', $data);
    }

    private function create_daily_sales_data($date_filter, $department, $payment_method = false): array{
        $pOSBillModel = model("POS/POSBillModel");

        $pOSBillModel = $pOSBillModel->select('payment_method, SUM(total) as total, SUM(transferred_amount) as transferred_amount, COUNT(id) as qty')->where('response_date', $date_filter)->where("type", $department)->where('payment_method !=', BluelimeConfiguration::PAYMENT_METHOD::CREDIT);
        if(($payment_method !== false) && (!empty($payment_method)))
            $pOSBillModel = $pOSBillModel->where('payment_method',BluelimeConfiguration::PAYMENT_METHOD::{$payment_method});

        $pos_bills_summary = $pOSBillModel->groupBy('payment_method')->findAll();

        $total = 0;
        foreach($pos_bills_summary as $row)
            $total += $row['total'];
           
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $finance_maintain = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::DAILY_SALES_BY_PAYMENT_TYPE)->where('department', $department)->findAll()[0]??[];

        $pos_bills = $pOSBillModel->where('response_date', $date_filter)->where("type", $department)->findAll();

        $pos_bills = BluelimeSystem::sort_array($pos_bills, ['fs_number']);

        $validation = ['has_duplicate'=>0, 'has_missing'=>0, 'has_void'=>0];

        $prev_fs = NULL;
        foreach($pos_bills as $row){
            $current_fs_number = intval($row['fs_number']);
            if($current_fs_number == 0)
                $current_fs_number = intval(trim(str_replace('M','',$row['fs_number'])));
            
            if($prev_fs == NULL){
                $prev_fs = $current_fs_number;
                continue;
            }

            if($current_fs_number - $prev_fs == 0){
                $validation['has_duplicate']++;
            }
            else if($current_fs_number - $prev_fs > 1){
                $validation['has_missing']++;
            }
            $prev_fs = $current_fs_number;
        }

        $dailySalesExportModel = model('Inventory/DailySalesExportModel');
        $dailySalesExport = $dailySalesExportModel->where('date', $date_filter)->where('department', $department)->first();
        $reference = $dailySalesExport['reference'] ?? '';

        return [$pos_bills_summary, $pos_bills, $total, $finance_maintain, $validation, $reference];

        // $data = [
        //     'pos_bills_summary'     => $pos_bills_summary,
        //     'pos_bills'     => $pos_bills,
        //     'total'         => $total,
        //     'date_filter'   => $date_filter,
        //     'department'    => $department,
        //     'finance_maintain' => $finance_maintain,
        //     'validation' => $validation,
        //     'active_menu'   => "sales_report_pharmacy",
        //     'active_sub_menu'   => "daily_sales_by_payment_type",
        // ];

        // if($department == 'Hospital')
        //     $data['active_menu'] = 'sales_report_clinic';

        // return view('Inventory/Finance/clinic_daily_sales_by_payment_type', $data);
    }

    public function pharmacy_daily_sales_export(){
        $date_filter = $_GET['date_filter'] ?? date('Y-m-d');
        $payment_method = $_GET['payment_method'] ?? false;
        $department = 'Inventory';

        [$pos_bills_summary, $pos_bills, $total, $finance_maintain, $validation, $reference] = $this->create_daily_sales_data($date_filter, $department, $payment_method);

        $data = [
            'pos_bills_summary'     => $pos_bills_summary,
            'pos_bills'     => $pos_bills,
            'total'         => $total,
            'date_filter'   => $date_filter,
            'payment_method'  => $payment_method,
            'department'    => $department,
            'title'         => 'Pharmacy',
            'finance_maintain' => $finance_maintain,
            'validation' => $validation,
            'active_menu'   => "sales_report_pharmacy",
            'active_sub_menu'   => "daily_sales_export",
            'reference' => $reference
        ];

        return view('Inventory/Finance/daily_sales_export', $data);
    }

    public function clinic_daily_sales_export(){
        $date_filter = $_GET['date_filter'] ?? date('Y-m-d');
        $payment_method = $_GET['payment_method'] ?? false;

        $department = 'Hospital';

        [$pos_bills_summary, $pos_bills, $total, $finance_maintain, $validation, $reference] = $this->create_daily_sales_data($date_filter, $department, $payment_method);

        $data = [
            'pos_bills_summary'     => $pos_bills_summary,
            'pos_bills'     => $pos_bills,
            'total'         => $total,
            'date_filter'   => $date_filter,
            'payment_method'  => $payment_method,
            'department'    => $department,
            'title'         => 'Clinic',
            'finance_maintain' => $finance_maintain,
            'validation' => $validation,
            'active_menu'   => "sales_report_clinic",
            'active_sub_menu'   => "daily_sales_export",
            'reference' => $reference
        ];

        return view('Inventory/Finance/daily_sales_export', $data);
    }

    public function update_maintain_daily_sales_by_payment_type(){
        $department = $_POST['department'];
        $maintain_data = $_POST['maintain_data'];
        $maintain_data = BluelimeSystem::convert_array_to_string($maintain_data);
        $type = BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::DAILY_SALES_BY_PAYMENT_TYPE;

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $finance_maintain = $financeMaintainModel->where('department', $department)->where('type', $type)->findColumn('id');
        $id = $finance_maintain[0] ?? -1;

        $data = ['department'=>$department, 'type'=>$type, 'data'=>$maintain_data];
        if($id > 0)
            $data['id'] = $id;

        $financeMaintainModel->save($data);

        return BluelimeSystem::get_success_ajax_return('done');
    }

    private function create_pharmacy_grouped_item_report_data($from_date, $to_date, $sales_type, $receipt_type, $cash_credit = 'Cash'): array{
        $salesMainModel = model('Inventory/SalesMainModel');
        $pOSBillModel = model('POS/POSBillModel');
        $peachtreeItemModle = model('Inventory/PeachtreeItemModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $financeMaintains = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtreeIntegration = $financeMaintains[0]['data'];

        $saleses = $salesMainModel->where('recieved_date >=', $from_date)->where('recieved_date <=', $to_date)->where('sales_type', $sales_type)->findAll();
        $sales_details = BluelimeSystem::gather_objects_by_reference($saleses,'id','Inventory/SalesDetailModel','sales_id');
        
        //$pos_bill_id_list = [...BluelimeSystem::collect_field_value_from_object($saleses, 'pos_bill_id'),-1];

        $pOSBillModel = $pOSBillModel->where('response_date >=', substr($from_date,0,10))->where('response_date <=', substr($to_date,0,10))->where('type','Inventory');
        if($cash_credit == 'Credit')
            $pOSBillModel = $pOSBillModel->where('payment_method', 'Credit');
        else
            $pOSBillModel = $pOSBillModel->where('payment_method !=', 'Credit');

        $pos_bills = $pOSBillModel->findAll();
        $pos_bills = BluelimeSystem::index_object_list_array_by_unique_field($pos_bills, 'id');
        $saleses = BluelimeSystem::index_object_list_array_by_unique_field($saleses,'id');

        $item_id_list = [...BluelimeSystem::collect_field_value_from_object($sales_details,'item_id'),-99];

        $peachtreeItems = $peachtreeItemModle->whereIn('item_id', $item_id_list)->findAll();
        $peachtreeItems = BluelimeSystem::index_object_list_array_by_unique_field($peachtreeItems, 'item_id');

        $salse_report_data = [];
        foreach($sales_details as $sales_detail){
            $index = 'item_'.$sales_detail['item_id'].'_'.$sales_detail['unit_price'];
            $pos_bill = $pos_bills['id_'.$saleses['id_'.$sales_detail['sales_id']]['pos_bill_id']] ?? [];

            if(empty($pos_bill))
                continue;

            $this_receipt_type = "Cash Register";
            if($pos_bill['response_data'] == 'Manual')
                $this_receipt_type = "Manual";
            
            if($this_receipt_type != $receipt_type)
                continue;

            $payment_method = $pos_bill['payment_method']=='Credit'?'Credit':'Cash';

            if($sales_detail['item_id'] == 0){
                $index = 'item_'.$sales_detail['item_id'].'__';
                $sales_detail['item_detail']['item_code'] = "CO_".$peachtreeIntegration['code_suffix']['dispensary'];
            }
            else
                $sales_detail['item_detail']['item_code'] = $peachtreeItems['item_id_'.$sales_detail['item_id']]['peachtree_id'].'_'.$peachtreeIntegration['code_suffix']['dispensary'];

            
            if(!isset($salse_report_data[$index])){                
                $salse_report_data[$index] = [
                    'item_detail'=>$sales_detail['item_detail'],
                    'unit_price' => $sales_detail['unit_price'],
                    'quantity' => 0,
                    'sub_total' => 0,
                    'vat' => 0,
                    'total' => 0,
                    'receipt_type' => $this_receipt_type,
                    'payment_method' => $payment_method
                ];
            }

            if($sales_detail['item_id'] == 0){
                $salse_report_data[$index]['quantity'] = 0;
                if($salse_report_data[$index]['sub_total']>0)
                    $salse_report_data[$index]['unit_price'] += $sales_detail['unit_price'];
            }

            $salse_report_data[$index]['quantity'] += $sales_detail['quantity'];

            $salse_report_data[$index]['sub_total'] = $salse_report_data[$index]['quantity'] * $salse_report_data[$index]['unit_price'];
            if($sales_detail['item_detail']['is_taxable'])
                $salse_report_data[$index]['vat'] = $salse_report_data[$index]['sub_total'] * 0.15;
            
            $salse_report_data[$index]['total'] = $salse_report_data[$index]['sub_total'] + $salse_report_data[$index]['vat'];
        }
        return $salse_report_data;
    }

    public function pharmacy_grouped_item(){
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
        $to_date = ($_GET['end_date'] ?? date('Y-m-d')). ' 23:59:59';
        $sales_type = $_GET['sales_type']?? BluelimeConfiguration::SALES_TYPE::DISPENSARY;
        $receipt_type = $_GET['receipt_type'] ?? 'Cash Register';
        $payment_method = $_GET['payment_method'] ?? 'Cash';

        $salse_report_data = $this->create_pharmacy_grouped_item_report_data($from_date, $to_date, $sales_type, $receipt_type, $payment_method);

        $data = [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'salse_report_data' => $salse_report_data,
            'active_menu'   => "other_links",
            'active_sub_menu'   => "dispensary_grouped_item"
        ];

        if($sales_type == BluelimeConfiguration::SALES_TYPE::COMPOUNDING)
            $data['active_sub_menu'] = 'compounding_grouped_item';

        return view('Inventory/Finance/pharmacy_grouped_item', $data);
    }

    private function create_cash_sales_report_data($from_date, $to_date): array{
        $salesMainModel = model('Inventory/SalesMainModel');
        $pOSBillModel = model('POS/POSBillModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $peachtreeItemModle = model('Inventory/PeachtreeItemModel');

        $financeMaintains = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtreeIntegration = $financeMaintains[0]['data'];

        $saleses = $salesMainModel->where('recieved_date >=', $from_date)->where('recieved_date <=', $to_date)->findAll();
        $sales_details = BluelimeSystem::gather_objects_by_reference($saleses,'id','Inventory/SalesDetailModel','sales_id');
        
        //$pos_bill_id_list = [...BluelimeSystem::collect_field_value_from_object($saleses, 'pos_bill_id'),-1];

        $pos_bills = $pOSBillModel->where('response_date >=', substr($from_date,0,10))->where('response_date <=', substr($to_date,0,10))->where('type','Inventory')->where('payment_method !=', BluelimeConfiguration::PAYMENT_METHOD::CREDIT)->findAll();
        $pos_bills = BluelimeSystem::index_object_list_array_by_unique_field($pos_bills, 'id');
        $saleses = BluelimeSystem::index_object_list_array_by_unique_field($saleses,'id');

        $item_id_list = [...BluelimeSystem::collect_field_value_from_object($sales_details,'item_id'),-99];

        $peachtreeItems = $peachtreeItemModle->whereIn('item_id', $item_id_list)->findAll();
        $peachtreeItems = BluelimeSystem::index_object_list_array_by_unique_field($peachtreeItems, 'item_id');

        $pharmacy_vat = 0;
        $compounding_vat = 0;
        $compounding_sales = 0;
        $compounding_taxable_sales = 0;

        $pharma_qty = 0;
        $sales_qty = [];

        $salse_report_data = [];
        foreach($sales_details as $sales_detail){
            $index = 'item_'.$sales_detail['item_id'].'_'.$sales_detail['unit_price'];
            $pos_bill = $pos_bills['id_'.$saleses['id_'.$sales_detail['sales_id']]['pos_bill_id']] ?? [];

            if(empty($pos_bill))
                continue;

            $sales_main = $saleses['id_'.$sales_detail['sales_id']];
            
            if($sales_detail['item_id'] == 0){
                $compounding_taxable_sales += $sales_detail['quantity'] * $sales_detail['unit_price'];
                $compounding_vat += $sales_detail['quantity'] * $sales_detail['vat'];
                continue;
            }
            elseif($sales_main['sales_type'] == BluelimeConfiguration::SALES_TYPE::COMPOUNDING){
                if($sales_detail['vat'] > 0)
                    $compounding_taxable_sales += $sales_detail['quantity'] * $sales_detail['unit_price'];
                else
                    $compounding_sales += $sales_detail['quantity'] * $sales_detail['unit_price'];

                $compounding_vat += $sales_detail['quantity'] * $sales_detail['vat'];
                continue;
            }            
               
            $sales_detail['item_detail']['item_code'] = $peachtreeItems['item_id_'.$sales_detail['item_id']]['peachtree_id'].'_'.$peachtreeIntegration['code_suffix']['dispensary'];
            $sales_detail['item_detail']['peachtree_description'] = $peachtreeItems['item_id_'.$sales_detail['item_id']]['peachtree_description'];
            
            if(!isset($salse_report_data[$index])){
                $salse_report_data[$index] = [
                    'item_detail'=> $sales_detail['item_detail'],
                    'unit_price' => $sales_detail['unit_price'],
                    'quantity' => 0,
                    'total' => 0,
                ];
            }

            $salse_report_data[$index]['quantity'] += $sales_detail['quantity'];

            $salse_report_data[$index]['total'] += $sales_detail['quantity'] * $sales_detail['unit_price'];
            $pharmacy_vat += $sales_detail['quantity'] * $sales_detail['vat'];
            
            $sales_qty[$sales_detail['sales_id']] = 1;
            $pharma_qty++;
        }

        return [$salse_report_data, $pharmacy_vat, $compounding_vat, $compounding_sales, $compounding_taxable_sales];
    }

    public function cash_sales_grouped_item(){
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
        $to_date = ($_GET['end_date'] ?? date('Y-m-d')). ' 23:59:59';
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $financeMaintains = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtreeIntegration = $financeMaintains[0]['data'];

        
        [$salse_report_data, $pharmacy_vat, $compounding_vat, $compounding_sales, $compounding_taxable_sales] = $this->create_cash_sales_report_data($from_date, $to_date);

        $pharmacy_sales = 0;
        $cosmetics_sales = 0;

        foreach($salse_report_data as $row){
            if($row['item_detail']['is_taxable'])
                $cosmetics_sales += $row['total'];
            else
                $pharmacy_sales += $row['total'];
        }

        $salse_report_data = [
            'pharmacy_sales' => $pharmacy_sales,
            'compounding_sales' => $compounding_sales,
            'cosmetics_sales' => $cosmetics_sales,
            'compounding_taxable_sales' => $compounding_taxable_sales,
            'pharmacy_vat' => $pharmacy_vat,
            'compounding_vat' => $compounding_vat,
        ];

        $amharic_month = ['Tir', 'Yekatit', 'Megabit', 'Miazia', 'Ginbot', 'Sene', 'Hamle', 'Nehase', 'Meskerem', 'Tikimt', 'Hidar', 'Tahisas'];

        $month = intval(date_format(date_create($from_date), "m"))-1;

        $year_diff = 8;
        if($month > 7)
            $year_diff = 7;

        $reference = 'G'.$peachtreeIntegration['branch']['number'].'-'.$amharic_month[$month].'-'.(intval(date('Y'))-$year_diff);

        $data = [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'salse_report_data' => $salse_report_data,
            'active_menu'   => "other_links",
            'active_sub_menu'   => "cash_sales_grouped_item",
            'reference' => $reference
        ];

        return view('Inventory/Finance/cash_sales_grouped_item', $data);
    }
    private function get_receipt_initial():array {
        $temp = [
            'Deposit Ticket ID' => '',
            'Customer ID' => '',
            'Customer Name' => '',
            'Reference' => '',
            'Date' => '',
            'Payment Method' => '',
            'Cash Account' => '',
            'Cash Amount' => '',
            'Sales Representative ID' => '',
            'Sales Tax ID' => '',
            'Total Paid on Invoice(s)' => 0,
            'Discount Account' => '',
            'Prepayment' => 'FALSE',
            'Vendor Receipt' => 'FALSE',
            'AR Date Cleared in Bank Rec' => '',
            'Number of Distributions' => '',
            'Invoice Paid' => '',
            'Discount Amount' => 0,
            'Quantity' => '',
            'Stocking Quantity' => '',
            'Item ID' => '',
            'Serial Number' => '',
            'U/M ID' => '<Each>',
            'U/M No. of Stocking Units' => 1,
            'Description' => '',
            'G/L Account' => '', //($row['item_detail']['is_taxable']?$peachtreeIntegration['gl_account']['sales']['cosmetics']:$peachtreeIntegration['gl_account']['sales']['drug']).'-'.$peachtreeIntegration['code_suffix']['dispensary'],
            'Unit Price' => '',
            'Stocking Unit Price' => '',
            'Tax Type' => 1,
            'UPC / SKU' => '',
            'Weight' => 0,
            'Amount' => '',
            'Job ID' => '',
            'Sales Tax Agency ID' => '',
            'Transaction Period' => '',
            'Transaction Number' => '',
            'Receipt Number' => '',
            'Card/Check Holder\'s Name' => '',
            'Card/Check Address Line 1' => '',
            'Card/Check Address Line 2' => '',
            'Card/Check City' => '',
            'Card/Check State' => '',
            'Card/Check Zip Code' => '',
            'Card/Check Country' => '',
            'Credit Card Number' => '',
            'Credit Card Expiration Date' => '',
            'Card/Check Auth. Code' => '',
            'Card/Check Authorized Status' => 0,
            'Credit Card Comment' => '',
            'Authorized Amount' => 0
        ];

        return $temp;
    }
    public function export_item_cash_sales_report_in_csv(){
        $from_date = ($_GET['from_date'] ?? date('Y-m-d')). ' 00:00:00';
        $to_date = ($_GET['to_date'] ?? date('Y-m-d')). ' 23:59:59';

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $financeMaintains = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtreeIntegration = $financeMaintains[0]['data'];

        $financeMaintains = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::DAILY_SALES_BY_PAYMENT_TYPE)->findAll();
        $gl_config = $financeMaintains[0]['data'];
        
        [$salse_report_data, $pharmacy_vat, $compounding_vat, $compounding_sales, $compounding_taxable_sales] = $this->create_cash_sales_report_data($from_date, $to_date);

        $cashier_name = $gl_config['cashier_name'];
        $reference = $_GET['reference'];
        $cashier_gl_account = $gl_config['cashier_gl_account'];
        $total_sales = 0;
        $csv_data = [];
        $cnt=1;
        $total_line = count($salse_report_data);
        if(($compounding_vat + $pharmacy_vat) > 0)
            $total_line++;
        if($compounding_sales > 0)
            $total_line++;
        if($compounding_taxable_sales > 0)
            $total_line++;
        
        $temp_common = $this->get_receipt_initial();
        $temp_common['Customer Name'] = $cashier_name;
        $temp_common['Reference'] = $reference;
        $temp_common['Date'] = date_format(date_create($to_date), "m/d/Y");
        $temp_common['Payment Method'] = 'Cash';
        $temp_common['Cash Account'] = $cashier_gl_account;
        $temp_common['Cash Amount'] = $total_sales;
        $temp_common['Number of Distributions'] = $total_line;
        $temp_common['Receipt Number'] = $reference;

        foreach($salse_report_data as $row){
            $temp = [
                ...$temp_common,
                'Quantity' => $row['quantity'],
                'Stocking Quantity' => $row['quantity'],
                'Item ID' => $row['item_detail']['item_code'],
                'Description' => $row['item_detail']['peachtree_description'],
                'G/L Account' => ($row['item_detail']['is_taxable']?$peachtreeIntegration['gl_account']['sales']['cosmetics']:$peachtreeIntegration['gl_account']['sales']['drug']).'-'.$peachtreeIntegration['code_suffix']['dispensary'],
                'Unit Price' => $row['unit_price'],
                'Stocking Unit Price' => $row['unit_price'],
                'Amount' => -1*$row['total'],
            ];
            $cnt++;

            array_push($csv_data,$temp);
        }

        if($compounding_sales > 0){
            $temp = [
                ...$temp_common,
                'Quantity' => 1,
                'Stocking Quantity' => 1,
                'Item ID' => '',
                'Description' => 'Compounding Drugs',
                'G/L Account' => $peachtreeIntegration['gl_account']['compounding_sales']['drug'].'-'.$peachtreeIntegration['code_suffix']['dispensary'],
                'Unit Price' => $compounding_sales,
                'Stocking Unit Price' =>$compounding_sales,
                'Amount' => -1*$compounding_sales,
            ];
            array_push($csv_data,$temp);
        }
            
        if($compounding_taxable_sales > 0){
            $temp = [
                ...$temp_common,
                'Quantity' => 1,
                'Stocking Quantity' => 1,
                'Item ID' => '',
                'Description' => 'Compounding Cosme',
                'G/L Account' => $peachtreeIntegration['gl_account']['compounding_sales']['cosmetics'].'-'.$peachtreeIntegration['code_suffix']['dispensary'],
                'Unit Price' => $compounding_taxable_sales,
                'Stocking Unit Price' =>$compounding_taxable_sales,
                'Amount' => -1*$compounding_taxable_sales,
            ];
            array_push($csv_data,$temp);
        }

        if(($compounding_vat + $pharmacy_vat) > 0)
        {
            $temp = [
                ...$temp_common,
                'Quantity' => 0,
                'Stocking Quantity' => 0,
                'Item ID' => '',
                'Description' => 'VAT',
                'G/L Account' => $peachtreeIntegration['gl_account']['vat'],
                'Unit Price' => 0,
                'Stocking Unit Price' =>0,
                'Amount' => -1*($compounding_vat + $pharmacy_vat),
            ];
            array_push($csv_data,$temp);
        }
        
        
        //BluelimeSystem::show_and_die($csv_data,false);
        BluelimeSystem::export_arra_in_csv($csv_data, $peachtreeIntegration['branch']['name'].' '.$reference.'.csv');
        // '_Summary_sales_'.substr($from_date,0,10).(substr($from_date,0,10) != substr($to_date,0,10) ? ' - '.substr($to_date,0,10):'').'.csv');

    }

    // public function clinic_bill_groupedby_service_type(){
    //     $from_date = ($_GET['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
    //     $to_date = ($_GET['end_date'] ?? date('Y-m-d')). ' 23:59:59';

    //     $billModel = model('Hospital/BillModel');

    //     $bills = $billModel->where('billed_date >=', $from_date)->where('billed_date <=', $to_date)->findAll();

    //     $bill_details = BluelimeSystem::gather_objects_by_reference($bills,'id','Hospital/BillDetailModel','bill_id');
        
    //     $bill_report_data = [];
    //     $total = 0;
    //     foreach($bill_details as $bill_detail){
    //         $index = 'item_'.$bill_detail['service_id'];
    //         if(!isset($bill_report_data[$index])){
    //             $bill_report_data[$index] = [
    //                 'unit_price' => $bill_detail['bill_amount'],
    //                 'service_name' => $bill_detail['service_name'],
    //                 'quantity' => 0,
    //                 'sub_total' => 0,
    //                 'vat' => 0,
    //                 'total' => 0,
    //             ];
    //         }

    //         $bill_report_data[$index]['quantity'] += $bill_detail['bill_quantity'];
    //         $bill_report_data[$index]['sub_total'] += $bill_detail['bill_quantity'] * $bill_detail['bill_amount'];
    //         $bill_report_data[$index]['total'] += $bill_report_data[$index]['sub_total'] + $bill_report_data[$index]['vat'];
    //         $total += $bill_report_data[$index]['sub_total'] + $bill_report_data[$index]['vat'];
    //     }

    //     $bill_report_data = BluelimeSystem::sort_array($bill_report_data,['service_name']);

    //     $data = [
    //         'from_date' => $from_date,
    //         'to_date' => $to_date,
    //         'bill_report_data' => $bill_report_data,
    //         'total' => $total,
    //         'active_menu'   => "sales_report_clinic",
    //         'active_sub_menu'   => "clinic_bill_groupedby_service_type"
    //     ];

    //     return view('Inventory/Finance/clinic_bill_groupedby_service_type', $data);

    // }

    public function export_item_sales_report_in_csv(){
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
        $to_date = ($_GET['end_date'] ?? date('Y-m-d')). ' 23:59:59';
        $sales_type = $_GET['sales_type']?? BluelimeConfiguration::SALES_TYPE::DISPENSARY;
        $receipt_type = $_GET['receipt_type'] ?? 'Cash Register';
        $payment_method = $_GET['payment_method'] ?? 'Cash';

        $salse_report_data = $this->create_pharmacy_grouped_item_report_data($from_date, $to_date, $sales_type, $receipt_type, $payment_method);

        $receipt_type_lable = ($payment_method=='Cash'?'CA_':'CR_').$receipt_type;

        $csv_data = [];
        $sn =1;
        foreach($salse_report_data as $row){
            $item_name = trim($row['item_detail']['item_brand_name']);
            if((trim($item_name) != trim($row['item_detail']['item_generic_name'])) && (trim($row['item_detail']['item_generic_name']) != ''))
                $item_name .= ' / '.trim($row['item_detail']['item_generic_name']);
            
            if(trim($row['item_detail']['preparation'])!='')
                $item_name .= ' / '.trim($row['item_detail']['preparation']);
            if(trim(($row['item_detail']['strength']??''))!='')
                $item_name .= ' / '.trim($row['item_detail']['strength']);

            $temp = [
                'SN' => $sn++,
                //'Id' => $row['item_detail']['id'],
                'Receipt Type' => $receipt_type_lable,
                'Item Code' => $row['item_detail']['item_code'],
                'Item Detail' => $item_name,
                'Unit Price' => $row['unit_price'],
                'Quantity' => $row['quantity'],
                'Sub Total' => $row['sub_total'],
                'VAT' => $row['vat'],
                'Total' => $row['total']
            ];

            array_push($csv_data, $temp);
        }
        //BluelimeSystem::show_and_die($csv_data);
        BluelimeSystem::export_arra_in_csv($csv_data,$receipt_type_lable.'_Item sales report '.substr($from_date,0,10).(substr($from_date,0,10) != substr($to_date,0,10) ? ' - '.substr($to_date,0,10):'').'.csv');
    }

    public function export_daily_sales_bank(){
        $filter_date = $_POST['filter_date'];
        $department = $_POST['department'];
        $payment_method = $_POST['payment_method'];
        $maintain_prefix = $_POST['maintain_prefix'];
        $cashier_name = $_POST['cashier_name'];
        $reference = $_POST['reference'];
        $cashier_gl = $_POST['cashier_gl'];
        $cash_account = $_POST['cash_account'];

        $pOSBillModel = model("POS/POSBillModel");

        $payment_method_title = '';
        if(!empty($payment_method))
            $payment_method_title = BluelimeConfiguration::PAYMENT_METHOD::{$payment_method};

        $pos_bills = $pOSBillModel->where('response_date', $filter_date)->where("type", $department)->where('payment_method', $payment_method_title)->findAll();
       
        //$csv = [['Customer Name','Reference','Date','Payment Method','Cash Account','Cash Amount','Number of Distributions','Invoice Paid','Quantity','Item ID','U/M ID','U/M No. of Stocking Units','Description','G/L Account','Unit Price','Stocking Unit Price','Amount','Receipt Number']];
        $csv = [];
        $cnt = 1;
        foreach($pos_bills as $row){
            $row['transferred_amount'] = round($row['transferred_amount'],0);
            $row['fs_number'] = str_replace(' ', '', $row['fs_number']);
            if($row['response_data'] != 'Manual')
                $row['fs_number'] = 'FS'.$row['fs_number'];
            $row['fs_number'] = $maintain_prefix .'-'.$row['fs_number'];
            $temp = [
                'Deposit Ticket ID' => '',
                'Customer ID' => '',
                'Customer Name' => $cashier_name,
                'Reference' => $reference,
                'Date' => date_format(date_create($filter_date), "m/d/Y"),
                'Payment Method' => 'Cash',
                'Cash Account' => $cash_account,
                'Cash Amount' => $row['transferred_amount'],
                'Sales Representative ID' => '',
                'Sales Tax ID' => '',
                'Total Paid on Invoice(s)' => 0,
                'Discount Account' => '',
                'Prepayment' => 'FALSE',
                'Vendor Receipt' => 'FALSE',
                'AR Date Cleared in Bank Rec' => '',
                'Number of Distributions' => 1,
                'Invoice Paid' => '',
                'Discount Amount' => 0,
                'Quantity' => 0,
                'Stocking Quantity' => 0,
                'Item ID' => '',
                'Serial Number' => '',
                'U/M ID' => '',
                'U/M No. of Stocking Units' => 1,
                'Description' => '',
                'G/L Account' => $cashier_gl,
                'Unit Price' => 0,
                'Stocking Unit Price' => 0,
                'Tax Type' => 1,
                'UPC / SKU' => '',
                'Weight' => '',
                'Amount' => -1*$row['total'],
                'Job ID' => '',
                'Sales Tax Agency ID' => '',
                'Transaction Period' => '',
                'Transaction Number' => $cnt,
                'Receipt Number' => $row['fs_number'],
                'Card/Check Holder\'s Name' => '',
                'Card/Check Address Line 1' => '',
                'Card/Check Address Line 2' => '',
                'Card/Check City' => '',
                'Card/Check State' => '',
                'Card/Check Zip Code' => '',
                'Card/Check Country' => '',
                'Credit Card Number' => '',
                'Credit Card Expiration Date' => '',
                'Card/Check Auth. Code' => '',
                'Card/Check Authorized Status' => 0,
                'Credit Card Comment' => '',
                'Authorized Amount' => 0
            ];
            if(($row['total'] - $row['transferred_amount'])>1)
                $temp['Number of Distributions'] = 2;
            else
                $temp['Amount'] = -1*round($row['total'],0);

            array_push($csv, $temp);

            if(($row['total'] - $row['transferred_amount'])>1){
                $temp['Amount'] = $row['total'] - $row['transferred_amount'];
                array_push($csv, $temp);
            }

            $cnt++;
        }

        //BluelimeSystem::print_associated_array($csv,false);

        $file_name = $maintain_prefix . '-' . $payment_method .'-'. str_replace('-','',$filter_date).'.csv';

        BluelimeSystem::export_arra_in_csv($csv,$file_name);
    }

    public function export_daily_sales_clinic(){
        $filter_date = $_POST['filter_date'];
        $department = 'Hospital'; //$_POST['department'];
        $maintain_prefix = $_POST['maintain_prefix'];
        $cashier_name = $_POST['cashier_name'];
        $payment_method = $_POST['payment_method'];
        $reference = $_POST['reference'];
        $cashier_gl = $_POST['cashier_gl'];

        $pOSBillModel = model("POS/POSBillModel");
        $serviceModel = model("Hospital/ServiceModel");

        $dailySalesExportModel = model("Inventory/DailySalesExportModel");
        $dailySalesExportModel->where('department', $department)->where('date', $filter_date)->delete();
        $dailySalesExportModel->insert([
            'department' => $department,
            'date' => $filter_date,
            'reference' => $reference
        ]);

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $financeMaintains = $financeMaintainModel->where('department',$department)->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::DAILY_SALES_BY_PAYMENT_TYPE)->findAll();
        $gl_config = $financeMaintains[0]['data'];

        $services = $serviceModel->findAll();
        $services = BluelimeSystem::index_object_list_array_by_unique_field($services, 'id');

        $pos_bills = $pOSBillModel->where('response_date', $filter_date)->where("type", $department);
        if(!empty(trim($payment_method)))
            $pOSBillModel = $pOSBillModel->where('payment_method', BluelimeConfiguration::PAYMENT_METHOD::{$payment_method});
        else
            $pOSBillModel = $pOSBillModel->where('payment_method !=', BluelimeConfiguration::PAYMENT_METHOD::CREDIT);
            //$pOSBillModel = $pOSBillModel->where('payment_method !=', BluelimeConfiguration::PAYMENT_METHOD::CASH)->where('payment_method !=', BluelimeConfiguration::PAYMENT_METHOD::CREDIT);

        $pos_bills = $pOSBillModel->findAll();
        
        $payment_method_reflection = new ReflectionClass(BluelimeConfiguration::PAYMENT_METHOD);
        $payment_methods = $payment_method_reflection->getConstants();
        
        //$csv = [['Customer Name','Reference','Date','Payment Method','Cash Account','Cash Amount','Number of Distributions','Invoice Paid','Quantity','Item ID','U/M ID','U/M No. of Stocking Units','Description','G/L Account','Unit Price','Stocking Unit Price','Amount','Receipt Number']];
        $temp_receipt = $this->get_receipt_initial();
        $temp_receipt['Customer Name'] = $cashier_name;
        $temp_receipt['Reference'] = $reference;
        $temp_receipt['Date'] = date_format(date_create($filter_date), "m/d/Y");
        $temp_receipt['Payment Method'] = 'Cash';

        $csv = [];
        $partial_cash_payment_total = 0;
        // $cnt = 1;
        // $transfer_amount_total_cash_reg = 0;
        // $transfer_amount_total_manual = 0;
        // $total_cash_reg = 0;
        // $total_manual = 0;
        foreach($pos_bills as $row){
            // $row['transferred_amount'] = round($row['transferred_amount'],0);
            // if($row['response_data'] == 'Manual'){
            //     $transfer_amount_total_manual += $row['transferred_amount'];
            //     $total_manual += $row['total'];
            // }
            // else{
            //     $transfer_amount_total_cash_reg += $row['transferred_amount'];
            //     $total_cash_reg += $row['total'];
            // }

            $row['fs_number'] = str_replace(' ', '', $row['fs_number']);
            if($row['response_data'] != 'Manual')
                $row['fs_number'] = 'FS'.$row['fs_number'];
            $row['fs_number'] = $maintain_prefix .'-'.$row['fs_number'];

            $sales_item_list = $row['pos_bill_json']['sales_item_list'];
            $Number_of_Distributions = count($sales_item_list);
            
            if(($row['total'] - $row['transferred_amount'] > 1) && ($row['transferred_amount'] > 1))
                $Number_of_Distributions++;

            $payment_method_code = '';
            foreach($payment_methods as $code => $value){
                if($row['payment_method'] == $value){
                    $payment_method_code = $code;
                    break;
                }
            }

            $cash_account = $gl_config['cash_account_'.$payment_method_code] ?? 'NOT_MAINTAINED';
            if($payment_method_code == 'CASH')
                $cash_account = $cashier_gl;

            $temp_receipt['Cash Account'] = $cash_account;
            $temp_receipt['Number of Distributions'] = $Number_of_Distributions;
            $temp_receipt['Receipt Number'] = $row['fs_number'];

            foreach($sales_item_list as $sales){
                $temp = [...$temp_receipt];
                $temp['Cash Amount'] = ($sales['quantity'] * $sales['unit_price']);
                $temp['Quantity'] = $sales['quantity'];
                $temp['Description'] = $sales['item_name'];
                $temp['G/L Account'] = $services['id_'.$sales['item_id']]['gl_account'];
                $temp['Unit Price'] = $sales['unit_price'];
                $temp['Stocking Unit Price'] = $sales['unit_price'];
                $temp['Amount'] = -1*($sales['quantity'] * $sales['unit_price']);

                $csv[] = $temp;
            }

            if(($row['transferred_amount'] < $row['total']) && ($row['transferred_amount'] > 1)){
                $temp = [...$temp_receipt];
                $temp['Cash Amount'] = -1*($row['total'] - $row['transferred_amount']);
                $temp['Quantity'] = 0;
                $temp['Description'] = 'Cash payment';
                $temp['G/L Account'] = $cashier_gl;
                $temp['Unit Price'] = '';
                $temp['Stocking Unit Price'] = '';
                $temp['Amount'] = ($row['total'] - $row['transferred_amount']);

                $csv[] = $temp;
                $partial_cash_payment_total += ($row['total'] - $row['transferred_amount']);
            }
        }

        /* Old style

        if(empty(trim($payment_method))){
            $cash_sales_csv_cash_reg = $this->get_csv_daily_sales_cash_only_clinic(
                                        filter_date: $filter_date, 
                                        department: $department, 
                                        gl_config: $gl_config,
                                        services: $services,
                                        reference: $reference, 
                                        cashier_name: $cashier_name,
                                        cashier_gl: $cashier_gl, 
                                        total: $total_cash_reg, 
                                        transfer_amount_total: $transfer_amount_total_cash_reg,
                                        manual_sales_only: false
                                    );

                $cash_sales_csv_manual = $this->get_csv_daily_sales_cash_only_clinic(
                                        filter_date: $filter_date, 
                                        department: $department, 
                                        gl_config: $gl_config,
                                        services: $services,
                                        reference: $reference, 
                                        cashier_name: $cashier_name,
                                        cashier_gl: $cashier_gl, 
                                        total: $total_manual, 
                                        transfer_amount_total: $transfer_amount_total_manual,
                                        manual_sales_only: true
                                    );

            $csv = [...$csv, ...$cash_sales_csv_cash_reg, ...$cash_sales_csv_manual];
         }
        */

        if($partial_cash_payment_total > 0){
            $temp = [...$temp_receipt];

            $temp['Cash Account'] = $cashier_gl;
            $temp['Number of Distributions'] = 1;
            $temp['Receipt Number'] = "{$maintain_prefix}-PARTIAL-".date("ymd",strtotime($filter_date));

            $temp['Cash Amount'] = $partial_cash_payment_total;
            $temp['Quantity'] = 0;
            $temp['Description'] = 'Partial Cash Payment';
            $temp['G/L Account'] = $cashier_gl;
            $temp['Unit Price'] = '';
            $temp['Stocking Unit Price'] = '';
            $temp['Amount'] = -1*$partial_cash_payment_total;
            $csv[] = $temp;
        }

        $file_name = $maintain_prefix . ' - daily sales - '. str_replace('-','',$filter_date).'.csv';

        BluelimeSystem::export_arra_in_csv($csv,$file_name);
    }

    private function is_sales_has_vat(array $sales_item_list): bool{
        foreach($sales_item_list as $sales){
            if($sales['vat'] == 1)
                return true;
        }
        return false;
    }
    public function export_daily_sales_pharmacy(){
        $filter_date = $_POST['filter_date'];
        $payment_method = $_POST['payment_method'];
        $department = 'Inventory'; //$_POST['department'];
        $maintain_prefix = $_POST['maintain_prefix'];
        $cashier_name = $_POST['cashier_name'];
        $reference = $_POST['reference'];
        $cashier_gl = $_POST['cashier_gl'];

        $pOSBillModel = model("POS/POSBillModel");

        $dailySalesExportModel = model("Inventory/DailySalesExportModel");
        $dailySalesExportModel->where('department', $department)->where('date', $filter_date)->delete();
        $dailySalesExportModel->insert([
            'department' => $department,
            'date' => $filter_date,
            'reference' => $reference
        ]);

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $financeMaintains = $financeMaintainModel->where('department',$department)->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::DAILY_SALES_BY_PAYMENT_TYPE)->findAll();
        $gl_config = $financeMaintains[0]['data'];
        
        $financeMaintains = $financeMaintainModel->where('department',$department)->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtreeIntegration = $financeMaintains[0]['data'];

        $pOSBillModel = $pOSBillModel->where('response_date', $filter_date)->where("type", $department);
        
        if(!empty(trim($payment_method)))
            $pOSBillModel = $pOSBillModel->where('payment_method', BluelimeConfiguration::PAYMENT_METHOD::{$payment_method});
        else
            $pOSBillModel = $pOSBillModel->where('payment_method !=', BluelimeConfiguration::PAYMENT_METHOD::CREDIT);
            //$pOSBillModel = $pOSBillModel->where('payment_method !=', BluelimeConfiguration::PAYMENT_METHOD::CASH)->where('payment_method !=', BluelimeConfiguration::PAYMENT_METHOD::CREDIT);

        $pos_bills = $pOSBillModel->findAll();
        
        $payment_method_reflection = new ReflectionClass(BluelimeConfiguration::PAYMENT_METHOD);
        $payment_methods = $payment_method_reflection->getConstants();

        $itemModel = model('Inventory/ItemModel');
        $items = $itemModel->findAll();
        $items = BluelimeSystem::index_object_list_array_by_unique_field($items,'id');

        $peachtreeItemModel = model('Inventory/PeachtreeItemModel');
        $peachtree_items = $peachtreeItemModel->findAll();
        $peachtree_items = BluelimeSystem::index_object_list_array_by_unique_field($peachtree_items, 'item_id');
        
        //$csv = [['Customer Name','Reference','Date','Payment Method','Cash Account','Cash Amount','Number of Distributions','Invoice Paid','Quantity','Item ID','U/M ID','U/M No. of Stocking Units','Description','G/L Account','Unit Price','Stocking Unit Price','Amount','Receipt Number']];
        $temp_receipt = $this->get_receipt_initial();
        $temp_receipt['Customer Name'] = $cashier_name;
        $temp_receipt['Reference'] = $reference;
        $temp_receipt['Date'] = date_format(date_create($filter_date), "m/d/Y");
        $temp_receipt['Payment Method'] = 'Cash';

        $csv = [];
        $partial_cash_payment_total = 0;
        // $cnt = 1;
        // $transfer_amount_total_cash_reg = 0;
        // $transfer_amount_total_manual = 0;
        // $total_cash_reg = 0;
        // $total_manual = 0;
        foreach($pos_bills as $row){
            $row['transferred_amount'] = round($row['transferred_amount'],0);

            // if($row['id'] == '258880')
            //     BluelimeSystem::print_associated_array($row);

            // if($row['response_data'] == 'Manual'){
            //     $transfer_amount_total_manual += $row['transferred_amount'];
            //     $total_manual += $row['total'];
            // }
            // else{
            //     $transfer_amount_total_cash_reg += $row['transferred_amount'];
            //     $total_cash_reg += $row['total'];
            // }

            $row['fs_number'] = str_replace(' ', '', $row['fs_number']);
            if($row['response_data'] != 'Manual')
                $row['fs_number'] = 'FS'.$row['fs_number'];
            $row['fs_number'] = $maintain_prefix .'-'.$row['fs_number'];

            $sales_item_list = $row['pos_bill_json']['sales_item_list'];
            $Number_of_Distributions = count($sales_item_list);
            
            if(($row['total'] - $row['transferred_amount'] > 1) && ($row['transferred_amount'] > 1))
                $Number_of_Distributions++;

            if($this->is_sales_has_vat($sales_item_list))
                $Number_of_Distributions++;

            $payment_method_code = '';
            foreach($payment_methods as $code => $value){
                if($row['payment_method'] == $value){
                    $payment_method_code = $code;
                    break;
                }
            }

            $cash_account = $gl_config['cash_account_'.$payment_method_code] ?? 'NOT_MAINTAINED';
            if($payment_method_code == 'CASH')
                $cash_account = $cashier_gl;

            $temp_receipt['Cash Account'] = $cash_account;
            $temp_receipt['Number of Distributions'] = $Number_of_Distributions;
            $temp_receipt['Receipt Number'] = $row['fs_number'];
            $vat_total = 0;
            foreach($sales_item_list as $sales){
                $item_id = intval($sales['item_id']);
                $peachtree_item_id = '';
                $gl_account = '';
                //Is compounding 
                if($item_id == 0){
                    $gl_account = $sales['vat'] == 1 ? $peachtreeIntegration['gl_account']['compounding_sales']['cosmetics'] : $peachtreeIntegration['gl_account']['compounding_sales']['drug'];   
                    $gl_account .= '-'.$peachtreeIntegration['code_suffix']['dispensary'];
                }
                else{
                    $gl_account = $sales['vat'] == 1 ? $peachtreeIntegration['gl_account']['sales']['cosmetics'] : $peachtreeIntegration['gl_account']['sales']['drug'];   
                    $gl_account .= '-'.$peachtreeIntegration['code_suffix']['dispensary'];
                    $peachtree_item_id = $peachtree_items['item_id_'.$sales['item_id']]['peachtree_id'].'_'.$peachtreeIntegration['code_suffix']['dispensary'];
                }

                if($sales['vat']==1)
                    $vat_total += ($sales['quantity'] * $sales['unit_price']) * BluelimeConfiguration::VAT;

                $temp = [...$temp_receipt];
                $temp['Cash Amount'] = ($sales['quantity'] * $sales['unit_price']);
                $temp['Quantity'] = $sales['quantity'];
                $temp['Item ID'] = $peachtree_item_id;
                $temp['Description'] = $sales['item_name'];
                $temp['G/L Account'] = $gl_account;
                $temp['Unit Price'] = $sales['unit_price'];
                $temp['Stocking Unit Price'] = $sales['unit_price'];
                $temp['Amount'] = -1*($sales['quantity'] * $sales['unit_price']);
                $csv[] = $temp;
            }

            if($vat_total > 0){
                $temp = [...$temp_receipt];
                $temp['Cash Amount'] = $vat_total;
                $temp['Quantity'] = 0;
                $temp['Description'] = 'VAT';
                $temp['G/L Account'] = $peachtreeIntegration['gl_account']['vat'];
                $temp['Unit Price'] = '';
                $temp['Stocking Unit Price'] = '';
                $temp['Amount'] = -1*$vat_total;
                $csv[] = $temp;
            }

            if(($row['total'] - $row['transferred_amount'] > 1) && ($row['transferred_amount'] > 1)){
                $temp = [...$temp_receipt];
                $temp['Cash Amount'] = -1*($row['total'] - $row['transferred_amount']);
                $temp['Quantity'] = 0;
                $temp['Description'] = 'Cash payment';
                $temp['G/L Account'] = $cashier_gl;
                $temp['Unit Price'] = '';
                $temp['Stocking Unit Price'] = '';
                $temp['Amount'] = ($row['total'] - $row['transferred_amount']);

                $csv[] = $temp;
                $partial_cash_payment_total += $row['total'] - $row['transferred_amount'];
            }
        }

        /* Cash sales will be included as separate receipts in the CSV file. The old style is changed.
            The old style is difficult to audit and reconcile with peachtree.

        if(empty(trim($payment_method))){
            $cash_sales_csv_cash_reg = $this->get_csv_daily_sales_cash_only_pharmacy(
                                        filter_date: $filter_date, 
                                        department: $department, 
                                        gl_config: $gl_config,
                                        peachtreeIntegration: $peachtreeIntegration,
                                        peachtree_items: $peachtree_items,
                                        reference: $reference, 
                                        cashier_name: $cashier_name,
                                        cashier_gl: $cashier_gl, 
                                        total: $total_cash_reg, 
                                        transfer_amount_total: $transfer_amount_total_cash_reg,
                                        manual_sales_only: false);
                            
            $cash_sales_csv_manual = $this->get_csv_daily_sales_cash_only_pharmacy(
                                        filter_date: $filter_date, 
                                        department: $department, 
                                        gl_config: $gl_config,
                                        peachtreeIntegration: $peachtreeIntegration,
                                        peachtree_items: $peachtree_items,
                                        reference: $reference, 
                                        cashier_name: $cashier_name,
                                        cashier_gl: $cashier_gl, 
                                        total: $total_manual, 
                                        transfer_amount_total: $transfer_amount_total_manual,
                                        manual_sales_only: true);

            $csv = [...$csv, ...$cash_sales_csv_cash_reg, ...$cash_sales_csv_manual];
         }

        */

         
        if($partial_cash_payment_total > 0){
            $temp = [...$temp_receipt];

            $temp['Cash Account'] = $cashier_gl;
            $temp['Number of Distributions'] = 1;
            $temp['Receipt Number'] = "{$maintain_prefix}-PARTIAL-".date("ymd",strtotime($filter_date));

            $temp['Cash Amount'] = $partial_cash_payment_total;
            $temp['Quantity'] = 0;
            $temp['Description'] = 'Partial Cash Payment';
            $temp['G/L Account'] = $cashier_gl;
            $temp['Unit Price'] = '';
            $temp['Stocking Unit Price'] = '';
            $temp['Amount'] = -1*$partial_cash_payment_total;
            $csv[] = $temp;
        }

        $file_name = $maintain_prefix . ' - daily sales - '. str_replace('-','',$filter_date).'.csv';

        BluelimeSystem::export_arra_in_csv($csv,$file_name);
    }

    private function get_csv_daily_sales_cash_only_pharmacy(
        $filter_date, 
        $department, 
        $gl_config, 
        $peachtreeIntegration, 
        $peachtree_items, 
        $reference,
        $cashier_name,
        $cashier_gl,
        $total,
        $transfer_amount_total,
        bool $manual_sales_only): array{

        $pOSBillModel = model("POS/POSBillModel");
        
        $pOSBillModel = $pOSBillModel->where('response_date', $filter_date)->where("type", $department)->where('payment_method', BluelimeConfiguration::PAYMENT_METHOD::CASH);
        if($manual_sales_only)
            $pOSBillModel = $pOSBillModel->where('response_data', 'Manual');
        else
            $pOSBillModel = $pOSBillModel->where('response_data !=', 'Manual');

        $pos_bills_cash = $pOSBillModel->findAll();
        
        $temp_receipt = $this->get_receipt_initial();
        $temp_receipt['Customer Name'] = $cashier_name;
        $temp_receipt['Reference'] = $reference;
        $temp_receipt['Date'] = date_format(date_create($filter_date), "m/d/Y");
        $temp_receipt['Payment Method'] = 'Cash';

        $csv = [];
        $cash_sales = [];
        $vat_drug_total = 0;
        $vat_compounding_total = 0;

        $manual_sales_receipt_range = ['start' => 0, 'end' => 0];
        foreach($pos_bills_cash as $row){
            $sales_item_list = $row['pos_bill_json']['sales_item_list'];
            if($manual_sales_only){
                $fs_number = intval(str_replace([' ','M','m'], '', $row['fs_number']));
                if(empty($manual_sales_receipt_range['start']))
                    $manual_sales_receipt_range['start'] = $fs_number;
                if(empty($manual_sales_receipt_range['end']))
                    $manual_sales_receipt_range['end'] = $fs_number;

                if($fs_number < $manual_sales_receipt_range['start'])
                    $manual_sales_receipt_range['start'] = $fs_number;
                if($fs_number > $manual_sales_receipt_range['end'])
                    $manual_sales_receipt_range['end'] = $fs_number;
            }

            foreach($sales_item_list as $sales){
                $item_id = intval($sales['item_id']);
                $gl_account = '';
                if($item_id == 0){
                    $gl_account = $sales['vat'] == 1 ? $peachtreeIntegration['gl_account']['compounding_sales']['cosmetics'] : $peachtreeIntegration['gl_account']['compounding_sales']['drug'];   
                    $gl_account .= '-'.$peachtreeIntegration['code_suffix']['dispensary'];

                    if($sales['vat'] == 0 ){
                        if(!isset($cash_sales['compounding_drug']))
                            $cash_sales['compounding_drug'] = ['total_price' => 0, 'quantity' => 0, 'unit_price'=>0, 'item_name' => 'Compounding Sales', 'item_id' => '', 'gl_account' => $gl_account, 'peachtree_item_id'=>''];
                        $cash_sales['compounding_drug']['total_price'] += $sales['unit_price'] * $sales['quantity'];
                    }
                    else{
                        if(!isset($cash_sales['compounding_taxable']))
                            $cash_sales['compounding_taxable'] = ['total_price' => 0, 'unit_price'=>0, 'quantity' => 0, 'item_name' => 'Compounding taxable', 'item_id' => '', 'gl_account' => $gl_account, 'peachtree_item_id'=>''];
                        $cash_sales['compounding_taxable']['total_price'] += $sales['unit_price'] * $sales['quantity'];
                        $vat_compounding_total += $sales['unit_price'] * $sales['quantity'] * BluelimeConfiguration::VAT;
                    }
                    continue;
                }
                
                $gl_account = $sales['vat'] == 1 ? $peachtreeIntegration['gl_account']['sales']['cosmetics'] : $peachtreeIntegration['gl_account']['sales']['drug'];   
                $gl_account .= '-'.$peachtreeIntegration['code_suffix']['dispensary'];
                $peachtree_item_id = $peachtree_items['item_id_'.$sales['item_id']]['peachtree_id'].'_'.$peachtreeIntegration['code_suffix']['dispensary'];
               
                if(!isset($cash_sales[$sales['item_id'].$sales['unit_price']]))
                    $cash_sales[$sales['item_id'].$sales['unit_price']] = ['unit_price' => 0, 'quantity' => 0, 'item_name' => $sales['item_name'], 'item_id' => $sales['item_id'], 'gl_account' => $gl_account, 'peachtree_item_id'=>$peachtree_item_id];

                $cash_sales[$sales['item_id'].$sales['unit_price']]['unit_price'] = $sales['unit_price'];
                $cash_sales[$sales['item_id'].$sales['unit_price']]['quantity'] += $sales['quantity'];

                if($sales['vat']==1)
                    $vat_drug_total += $sales['unit_price'] * $sales['quantity'] * BluelimeConfiguration::VAT;
            }
        }

        $Number_of_Distributions = count($cash_sales);
        if($transfer_amount_total < $total)
            $Number_of_Distributions++;
        
        if($vat_compounding_total > 0)
            $Number_of_Distributions++;

        if($vat_drug_total > 0)
            $Number_of_Distributions++;

        $receipt_no = 'CA-'.$reference;
        if($manual_sales_only)
            $receipt_no = 'CA-M-'.$reference;

        if(substr($reference,0,2)==$gl_config['maintain_prefix']){
            $receipt_no = $gl_config['maintain_prefix'] . '-CA'.substr($reference,2);
            if($manual_sales_only)
                $receipt_no = $gl_config['maintain_prefix'] . '-CA-M:'.$manual_sales_receipt_range['start'].'-'.$manual_sales_receipt_range['end'];
        }

        $temp_receipt['Cash Account'] = $cashier_gl;
        $temp_receipt['Number of Distributions'] = $Number_of_Distributions;
        $temp_receipt['Receipt Number'] = $receipt_no;
        
        $compounding_saleses = [];
        foreach($cash_sales as $index => $sales){
            $temp = [...$temp_receipt];
            $temp['Cash Amount'] = ($sales['quantity'] * $sales['unit_price']);
            $temp['Quantity'] = $sales['quantity'];
            $temp['Item ID'] = $sales['peachtree_item_id'];
            $temp['Description'] = $sales['item_name'];
            $temp['G/L Account'] = $sales['gl_account'];
            $temp['Unit Price'] = $sales['unit_price'];
            $temp['Stocking Unit Price'] = $sales['unit_price'];
            $temp['Amount'] = -1*($sales['quantity'] * $sales['unit_price']);

            if(($index == 'compounding_drug') || ($index == 'compounding_taxable')){
                $temp['Amount'] = -1*$sales['total_price'];
                $compounding_saleses[] = $temp;
                continue;
            }

            $csv[] = $temp;
        }

        if($transfer_amount_total < $total){
            
            $temp = [...$temp_receipt];
            $temp['Cash Amount'] = $total - $transfer_amount_total;
            $temp['Quantity'] = 0;
            $temp['Description'] = 'Partial Cash Paymentpayment';
            $temp['G/L Account'] = $cashier_gl;
            $temp['Unit Price'] = '';
            $temp['Stocking Unit Price'] = '';
            $temp['Amount'] = -1*($total - $transfer_amount_total);

            $csv[] = $temp;
        }

        $csv = [...$csv, ...$compounding_saleses];

        if($vat_drug_total > 0){
            $temp = [...$temp_receipt];
            $temp['Cash Amount'] = $vat_drug_total;
            $temp['Quantity'] = 0;
            $temp['Description'] = 'VAT Despensary';
            $temp['G/L Account'] = $peachtreeIntegration['gl_account']['vat'];
            $temp['Unit Price'] = '';
            $temp['Stocking Unit Price'] = '';
            $temp['Amount'] = -1*$vat_drug_total;
            $csv[] = $temp;
        }

        if($vat_compounding_total > 0){
            $temp = [...$temp_receipt];
            $temp['Cash Amount'] = $vat_compounding_total;
            $temp['Quantity'] = 0;
            $temp['Description'] = 'VAT Compounding';
            $temp['G/L Account'] = $peachtreeIntegration['gl_account']['vat'];
            $temp['Unit Price'] = '';
            $temp['Stocking Unit Price'] = '';
            $temp['Amount'] = -1*$vat_compounding_total;
            $csv[] = $temp;
        }

        return $csv;
    }

    private function get_csv_daily_sales_cash_only_clinic(
        $filter_date, 
        $department, 
        $gl_config, 
        $services, 
        $reference, 
        $cashier_name, 
        $cashier_gl, 
        $total, 
        $transfer_amount_total,
        bool $manual_sales_only): array{
        $pOSBillModel = model("POS/POSBillModel");

        $pOSBillModel = $pOSBillModel->where('response_date', $filter_date)->where("type", $department)->where('payment_method', BluelimeConfiguration::PAYMENT_METHOD::CASH);

        if($manual_sales_only)
            $pOSBillModel = $pOSBillModel->where('response_data', 'Manual');
        else
            $pOSBillModel = $pOSBillModel->where('response_data !=', 'Manual');

        $pos_bills_cash = $pOSBillModel->findAll();

        $cash_sales = [];
        $manual_sales_receipt_range = ['start' => 0, 'end' => 0];
        foreach($pos_bills_cash as $row){
            $sales_item_list = $row['pos_bill_json']['sales_item_list'];
            if($manual_sales_only){
                $fs_number = intval(str_replace([' ','M','m'], '', $row['fs_number']));
                if(empty($manual_sales_receipt_range['start']))
                    $manual_sales_receipt_range['start'] = $fs_number;
                if(empty($manual_sales_receipt_range['end']))
                    $manual_sales_receipt_range['end'] = $fs_number;

                if($fs_number < $manual_sales_receipt_range['start'])
                    $manual_sales_receipt_range['start'] = $fs_number;
                if($fs_number > $manual_sales_receipt_range['end'])
                    $manual_sales_receipt_range['end'] = $fs_number;
            }

            foreach($sales_item_list as $sales){
                if(!isset($cash_sales[$sales['item_id'].$sales['unit_price']]))
                    $cash_sales[$sales['item_id'].$sales['unit_price']] = ['unit_price' => 0, 'quantity' => 0, 'item_name' => $sales['item_name'], 'item_id' => $sales['item_id']];

                $cash_sales[$sales['item_id'].$sales['unit_price']]['unit_price'] = $sales['unit_price'];
                $cash_sales[$sales['item_id'].$sales['unit_price']]['quantity'] += $sales['quantity'];
            }
        }

        $Number_of_Distributions = count($cash_sales);
        if($transfer_amount_total < $total)
            $Number_of_Distributions++;

        $receipt_no = 'CA-'.$reference;
        if($manual_sales_only)
            $receipt_no = 'CA-M-'.$reference;

        if(substr($reference,0,2)==$gl_config['maintain_prefix']){
            $receipt_no = $gl_config['maintain_prefix'] . '-CA'.substr($reference,2);
            if($manual_sales_only)
                $receipt_no = $gl_config['maintain_prefix'] . '-CA-M:'.$manual_sales_receipt_range['start'].'-'.$manual_sales_receipt_range['end'];
        }

        $temp_receipt = $this->get_receipt_initial();
        $temp_receipt['Customer Name'] = $cashier_name;
        $temp_receipt['Reference'] = $reference;
        $temp_receipt['Date'] = date_format(date_create($filter_date), "m/d/Y");
        $temp_receipt['Payment Method'] = 'Cash';
        $temp_receipt['Cash Account'] = $cashier_gl;
        $temp_receipt['Number of Distributions'] = $Number_of_Distributions;
        $temp_receipt['Receipt Number'] = $receipt_no;

        $csv = [];
        foreach($cash_sales as $sales){
            $temp = [...$temp_receipt];
            $temp['Cash Amount'] = ($sales['quantity'] * $sales['unit_price']);
            $temp['Quantity'] = $sales['quantity'];
            $temp['Description'] = $sales['item_name'];
            $temp['G/L Account'] = $services['id_'.$sales['item_id']]['gl_account'];
            $temp['Unit Price'] = $sales['unit_price'];
            $temp['Stocking Unit Price'] = $sales['unit_price'];
            $temp['Amount'] = -1*($sales['quantity'] * $sales['unit_price']);

            $csv[] = $temp;
        }

        if($transfer_amount_total < $total){
            $temp = [...$temp_receipt];
            $temp['Cash Amount'] = ($total - $transfer_amount_total);
            $temp['Quantity'] = 0;
            $temp['Description'] = 'Partial Cash Paymentpayment';
            $temp['G/L Account'] = $cashier_gl;
            $temp['Unit Price'] = '';
            $temp['Stocking Unit Price'] = '';
            $temp['Amount'] = -1*($total - $transfer_amount_total);
            $csv[] = $temp;
        }

        return $csv;
    }

    public function sales_by_receipt_pharmacy(){
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d'));
        $to_date = ($_GET['end_date'] ?? date('Y-m-d'));
        $receipt_type = $_GET['receipt_type'] ?? 'Cash Register';
        $payment_method = $_GET['payment_method'] ?? 'Cash';

        $pOSBillModel = model('POS/POSBillModel');
        $salesMainModel = model('Inventory/SalesMainModel');
        $salesDetailModel = model('Inventory/SalesDetailModel');

        $pOSBillModel = $pOSBillModel->where('type', 'Inventory')->where('response_date >=', $from_date)->where('response_date <=', $to_date);
        
        if($receipt_type == 'Cash Register')
            $pOSBillModel = $pOSBillModel->where('response_data !=', 'Manual');
        else
            $pOSBillModel = $pOSBillModel->where('response_data', 'Manual');

        if($payment_method == 'Cash')
            $pOSBillModel = $pOSBillModel->where('payment_method !=', 'Credit');
        else
            $pOSBillModel = $pOSBillModel->where('payment_method', 'Credit');

        $pos_bills = $pOSBillModel->findAll();
        $pos_bills = BluelimeSystem::index_object_list_array_by_unique_field($pos_bills,'id');

        //$pos_bills_id_list = BluelimeSystem::collect_field_value_from_object($pos_bills, '')
        $sales_mains = $salesMainModel->select('id, pos_bill_id, fs_number')->whereIn('pos_bill_id',[...array_column($pos_bills,'id'),-99])->findAll();
        
        //$sales_mains = BluelimeSystem::gather_objects_by_reference($pos_bills,'id','Inventory/SalesMainModel','pos_bill_id');

        //$sales_details = BluelimeSystem::gather_objects_by_reference($sales_mains,'id','Inventory/SalesDetailModel','sales_id');
        $sales_details = $salesDetailModel->select('id, sales_id, quantity, unit_price, vat')->whereIn('sales_id', [...array_column($sales_mains,'id'),-99])->findAll();
        $sales_mains = BluelimeSystem::index_object_list_array_by_unique_field($sales_mains,'id');

        $totals = ['drug'=>0, 'cosmetics'=>0, 'vat'=>0];
        $salse_report_data = [];
        foreach($sales_details as $row){
            $fs_number = $sales_mains['id_'.$row['sales_id']]['fs_number'];
            $response_date = $pos_bills['id_'.$sales_mains['id_'.$row['sales_id']]['pos_bill_id']]['response_date'];
            $payment_method = $pos_bills['id_'.$sales_mains['id_'.$row['sales_id']]['pos_bill_id']]['payment_method'];

            if(!isset($salse_report_data[$fs_number]))
                $salse_report_data[$fs_number] = ['fs_number'=>$fs_number,'drug'=>0,'cosmetics'=>0,'vat'=>0,'total'=>0, 'date'=>$response_date,'payment_method'=>$payment_method];

            if($row['vat'] > 0){
                $salse_report_data[$fs_number]['cosmetics'] += $row['quantity'] * $row['unit_price'];
                $salse_report_data[$fs_number]['vat'] += $row['quantity'] * $row['vat'];
                $totals['vat'] += $row['quantity'] * $row['vat'];
                $totals['cosmetics'] += $row['quantity'] * $row['unit_price'];
            }
            else{
                $salse_report_data[$fs_number]['drug'] += $row['quantity'] * $row['unit_price'];
                $totals['drug'] += $row['quantity'] * $row['unit_price'];
            }

            $salse_report_data[$fs_number]['total'] = $salse_report_data[$fs_number]['drug'] + $salse_report_data[$fs_number]['cosmetics'] + $salse_report_data[$fs_number]['vat'];
        }

        $data = [
            'totals' => $totals,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'salse_report_data' => $salse_report_data,
            'active_menu'   => "sales_report_pharmacy",
            'active_sub_menu'   => "sales_by_receipt_pharmacy"
        ];


        return view('Inventory/Finance/sales_by_receipt', $data);
    }

    public function sales_by_receipt_clinic(){
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d'));
        $to_date = ($_GET['end_date'] ?? date('Y-m-d'));
        $receipt_type = $_GET['receipt_type'] ?? 'Cash Register';
        $payment_method = $_GET['payment_method'] ?? 'Cash';

        $pOSBillModel = model('POS/POSBillModel');

        $pOSBillModel = $pOSBillModel->where('type', 'Hospital')->where('response_date >=', $from_date)->where('response_date <=', $to_date);

        if($receipt_type == 'Cash Register')
            $pOSBillModel = $pOSBillModel->where('response_data !=', 'Manual');
        else
            $pOSBillModel = $pOSBillModel->where('response_data', 'Manual');

        if($payment_method == 'Cash')
            $pOSBillModel = $pOSBillModel->where('payment_method !=', 'Credit');
        else
            $pOSBillModel = $pOSBillModel->where('payment_method', 'Credit');

        $pos_bills = $pOSBillModel->findAll();
        $pos_bills = BluelimeSystem::index_object_list_array_by_unique_field($pos_bills,'id');

        $sales_mains = BluelimeSystem::gather_objects_by_reference($pos_bills,'id','Hospital/BillModel','pos_bill_id');
        $sales_mains = BluelimeSystem::index_object_list_array_by_unique_field($sales_mains,'pos_bill_id');

        //$sales_details = BluelimeSystem::gather_objects_by_reference($sales_mains,'id','Inventory/SalesDetailModel','sales_id');

        $totals = ['total'=>0, 'bank_transfer_amount'=>0];
        $salse_report_data = [];
        foreach($pos_bills as $pos_bill){
            $fs_number = $pos_bill['fs_number'];
            $response_date = $pos_bill['response_date'];
            //$salse_report_data[$fs_number]['fs_number'] = $fs_number;
            $salse_report_data[] = [
                'fs_number'=>$fs_number,
                'total'=>$pos_bill['total'],
                'bank_transfer_amount'=>$pos_bill['transferred_amount'],
                'date'=>$response_date, 
                'payment_method'=>$pos_bill['payment_method'],
                'company_name'=>$sales_mains['pos_bill_id_'.$pos_bill['id']]['company_name'] ?? '',
                ];
            $totals['total'] += $pos_bill['total'];
            $totals['bank_transfer_amount'] += $pos_bill['transferred_amount'];
        }

        $data = [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'totals' => $totals,
            'payment_method' => $payment_method,
            'salse_report_data' => $salse_report_data,
            'active_menu'   => "sales_report_clinic",
            'active_sub_menu'   => "sales_by_receipt_clinic"
        ];


        return view('Inventory/Finance/sales_by_receipt_clinic', $data);
    }

    public function dashboard(){
        $logged_user = BluelimeAuthentication::get_logged_user_data();
        $response_date = $_POST['filter_date'] ?? date('Y-m-d');

        $pOSBillModel = model('POS/POSBillModel');

        $type = '';
        if($logged_user['role'] == 'SUPER_ADMIN'){
            $i = $_GET['i'] ?? 0;
            if($i==1)
                $_SESSION['show_inventory'] = true;
            elseif($i==2)
                $_SESSION['show_inventory'] = false;

            $inventory = $_SESSION['show_inventory'] ?? false;
            if($inventory)
                $type = 'Inventory';
            else
                $type = 'Hospital';
        }
        elseif($logged_user['role'] == BluelimeConfiguration::USER_ROLE::CASHIER_INVENTORY)
            $type = 'Inventory';
        else
            $type = 'Hospital';

        $payment_method_reflection = new \ReflectionClass(BluelimeConfiguration::PAYMENT_METHOD);
        $payment_methods = $payment_method_reflection->getConstants();
        
        $payment_report = [];
        foreach($payment_methods as $payment_method){
            $payment_report[$payment_method] = ['qty' => 0, 'amount' => 0];
        }

        $pos_bills = $pOSBillModel->where('type', $type)->where('response_date', $response_date)->findAll();

        $total_count = 0;
        $total_amount = 0;
        $cash_partial = 0;
        foreach($pos_bills as $pos_bill){
            $payment_method = $pos_bill['payment_method'];
            if(!isset($payment_report[$payment_method]))
                $payment_report[$payment_method] = ['qty' => 0, 'amount' => 0];
            
            $total = $this->calculate_total($pos_bill);
            $payment_report[$payment_method]['qty']++;
            $transferred_amount = floatval($pos_bill['transferred_amount']);
            if($transferred_amount > 0){
                $cash_partial += $total - floatval($pos_bill['transferred_amount']);
                $total = $transferred_amount;
            }
            $payment_report[$payment_method]['amount'] += $total;
            $total_amount += $total;
            $total_count++;
        }
        $payment_report['Cash']['amount'] += $cash_partial;
        $total_amount += $cash_partial;

        $payment_report['TOTAL'] = ['qty' => $total_count, 'amount' => $total_amount];

        $payment_report_temp = [];
        $cashless_total = 0;
        $cashless_count = 0;
        foreach($payment_report as $index => $line){
            if(($index == BluelimeConfiguration::PAYMENT_METHOD::CASH) || ($index == 'TOTAL') || ($index == BluelimeConfiguration::PAYMENT_METHOD::CREDIT))
                continue;
            $payment_report_temp[$index] = $line;
            $cashless_count += $line['qty'];
            $cashless_total += $line['amount'];
        }

        $payment_report_temp['BANK TOTAL'] = ['qty' => $cashless_count, 'amount' => $cashless_total];

        $payment_report_temp['Credit'] = $payment_report['Credit'];
        $payment_report_temp['Cash'] = $payment_report['Cash'];
        $payment_report_temp['TOTAL'] = $payment_report['TOTAL'];

        $payment_report = $payment_report_temp;


        $pos_bill_id_list = BluelimeSystem::collect_field_value_from_object($pos_bills,'id');
        if(empty($pos_bill_id_list))
            $pos_bill_id_list = [-1];

        //$inventory_saleses = $this->get_inventory_saleses($pos_bill_id_list);
        //$sales_type_report = $this->generate_inventory_sales_type_report($inventory_saleses);
        
        //$item_type_report = $this->generate_inventory_item_type_report($inventory_saleses);

        $cashier_type = 'Clinic';
        if($type == 'Inventory')
            $cashier_type = 'Pharmacy';

        $data = [
            'active_menu'=>'dashboard',
            'filter_date' => $response_date,
            'payment_report' => $payment_report,
            'cashier_type' => $cashier_type
        ];

        return view('Inventory/Finance/clinic_summery_bank_cash', $data);
    }
    
    public function dashboard_detail(){
        $logged_user = BluelimeAuthentication::get_logged_user_data();
        $response_date = $_POST['filter_date'] ?? date('Y-m-d');

        $pOSBillModel = model('POS/POSBillModel');

        $type = '';
        if($logged_user['role'] == 'SUPER_ADMIN'){
            $i = $_GET['i'] ?? 0;
            if($i==1)
                $_SESSION['show_inventory'] = true;
            elseif($i==2)
                $_SESSION['show_inventory'] = false;

            $inventory = $_SESSION['show_inventory'] ?? false;
            if($inventory)
                $type = 'Inventory';
            else
                $type = 'Hospital';
        }
        elseif($logged_user['role'] == BluelimeConfiguration::USER_ROLE::CASHIER_INVENTORY)
            $type = 'Inventory';
        else
            $type = 'Hospital';


        $pos_bills = $pOSBillModel->where('type', $type)->groupStart()->where('response_date', $response_date)->orLike('updated_at', "$response_date")->groupEnd()->orderBy('id','DESC')->findAll();

        foreach($pos_bills as $index => $pos_bill){
            $total = $this->calculate_total($pos_bill);
            $pos_bills[$index]['total'] = $total;
        }
        

        $cashier_type = 'Clinic';
        if($type == 'Inventory')
            $cashier_type = 'Pharmacy';

        $data = [
            'active_menu'=>'dashboard',
            'filter_date' => $response_date,
            'pos_bills' => $pos_bills,
            'cashier_type' => $cashier_type
        ];

        return view('Inventory/Finance/clinic_bank_cash_detail', $data);
    }

    private function calculate_total($pos_bill):float{
        $total = 0.0;

        foreach($pos_bill['pos_bill_json']['sales_item_list'] as $line)
            $total += $line['quantity'] * ($line['unit_price'] + ($line['vat'] * ($line['unit_price'] * .15)));
        
        return $total;
    }

    public function receipt(){
        $fs_no = $_GET['fs_no'] ?? '';        
        $department = $_GET['department'] ?? 'Hospital';

        $pOSBillModel = model('POS/POSBillModel');

        $fs_no = trim($fs_no);

        $pos_bills = [];
        if($fs_no != ''){
            if(strlen($fs_no) == 5)
                $pOSBillModel = $pOSBillModel->groupStart()->where('fs_number LIKE', "M $fs_no")->orWhere('fs_number LIKE', "%$fs_no%")->groupEnd();
            else
                $pOSBillModel = $pOSBillModel->where('fs_number LIKE', "%$fs_no%");

            $pos_bills = $pOSBillModel->where('type', $department)->findAll();
        }
        
        $attachments = [];
        foreach($pos_bills as $pos_bill){
            $data = [];
            $attachment = '';
            if($department == 'Hospital'){
                [$pos_bill, $sales_main, $sub_total, $payment, $bill, $patient] = BluelimePOS::get_attachmwnt_data_hospital($pos_bill['id']);
                $data = [
                    'sales_main' => $sales_main,
                    'sub_total' => $sub_total,
                    'taxable_total' => 0,
                    'total_vat' => 0,
                    'total' => $sub_total,
                    'payment' => $payment,
                    'pos_bill' => $pos_bill
                ];
                $patient ??= [
                    'first_name' => '',
                    'middle_name' => '',
                    'last_name' => '',
                    'company_id' => '',
                    'company_name' => ''
                    ];
                $full_name = trim($patient['first_name'].' '.$patient['middle_name'].' '.$patient['last_name']);
                $editor_data = [
                    'name'          => ['data'=>$full_name, 'has_difference'=>false],
                    'company_id'    => ['data'=>$patient['company_id'], 'has_difference'=>false],
                    'company_name'  => ['data'=>$patient['company_name'], 'has_difference'=>false],
                    'payment_method' =>['data'=>$bill['bill_type'], 'has_difference'=>false]
                ];
                if(trim($pos_bill['pos_bill_json']['customer_name']) !=  substr(BluelimePOS::fix_name_for_reciept($full_name),0,strlen(trim($pos_bill['pos_bill_json']['customer_name']))))
                    $editor_data['name']['has_difference'] = true;
                
                if(trim($pos_bill['pos_bill_json']['company_name']) != trim(BluelimePOS::fix_name_for_reciept($patient['company_name'])))
                    $editor_data['company_name']['has_difference'] = true;

                if(trim($pos_bill['pos_bill_json']['payment_type']) != $bill['bill_type'])
                    $editor_data['payment_method']['has_difference'] = true;

                $attachment_detail_editor = view('Include/_attachment_detail_editor',['editor_data'=>$editor_data]);
                $attachment_body =  view('Include/_attachment_hospital',$data);
            }
            else {
                [$pos_bill, $sales_mains, $sub_total, $taxable_total, $total_vat, $total] = BluelimePOS::get_attachmwnt_data_invetory($pos_bill['id']);
                $data = [
                    'pos_bill' => $pos_bill,
                    'sales_mains' => $sales_mains,
                    'sub_total' => $sub_total,
                    'taxable_total' => $taxable_total,
                    'total_vat' => $total_vat,
                    'total' => $total,
                    'show_vat' => true,
                ];
                $attachment_detail_editor = '';
                $attachment_body = view('Include/_attachment_inventory',$data);
            }
            array_push($attachments,['body'=>$attachment_body,'detail_editor'=>$attachment_detail_editor]);
        }

        $data = [
            'fs_no' => $fs_no,
            'department' => $department,
            'attachments' => $attachments,
            'active_menu'   => "receipt"
        ];

        return view('Inventory/Finance/receipt', $data);
    }


    public function newservice()
    
    {
        $serviceModel = model('Hospital/ServiceModel');

        
        $services = $serviceModel->orderBy('service_type','ASC')->orderBy('service_name','ASC')->findAll();
        $service = $serviceModel->where('id',@$_GET['sid'])->findAll();

        $data['active_menu'] = 'setupdata';
        $data['services'] = $services;
        $data['service'] = $service;
        
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Inventory/Finance/_left_sidebar_finance')

        .view('Include/_left_sidebar_bottom')

        .view('Inventory/Finance/new_service')

        .view('Include/_footer');
                
    }

    public function newcompany()
    
    {
        $companyModel = model('Hospital/CompanyModel');

        
        $companies = $companyModel->orderBy('company_name')->findAll();
        $company = $companyModel->where('id',@$_GET['cid'])->findAll();

        $data['active_menu'] = 'setupdata';
        $data['companies'] = $companies;
        $data['company'] = $company;
        
        
        return view('Include/_header',$data)
        .view('Include/_left_sidebar_top')

        .view('Inventory/Finance/_left_sidebar_finance')

        .view('Include/_left_sidebar_bottom')

        .view('Inventory/Finance/new_company')

        .view('Include/_footer');
                
    }
    
    public function pricesetup()
    {   
        $pricelistModel = model('Hospital/PricelistModel');
        $pricelistDetailModel = model('Hospital/PricelistDetailModel');
        $serviceModel = model('Hospital/ServiceModel');
        $companyModel = model('Hospital/CompanyModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
                
        $pricelist = $pricelistModel->orderBy('company_name')->findAll();
        $price = $pricelistModel->where('id',@$_GET['plid'])->findAll();
        $companies = $companyModel->where('is_active','1')->orderBy('company_name')->findAll();
        $services = $serviceModel->where('is_active','1')->where('id >',0)->orderBy('service_name','ASC')->orderBy('service_name','ASC')->findAll();
        $pricelist_id = $_GET['plid'] ?? 1;
        $service_id_list = [];

        foreach($services as $service){
            array_push($service_id_list, $service['id']);
        }
        if($services){
        $pricelist_detail = $pricelistDetailModel->where('pricelist_id', $pricelist_id)->whereIn('service_id', $service_id_list)->findAll();

            foreach($services as $index => $service){
                foreach($pricelist_detail as $detail){
                    if($service['id'] === $detail['service_id']){
                        $services[$index]['_detail'] = $detail;
                        break;
                    }
                }
            }
        }
        $pricelist_company_name = $pricelistModel->where('id',$pricelist_id)->findColumn('company_name');

        $companies_with_price_list = $pricelistModel->select('MAX(id) as price_list_id, company_id, MAX(company_name) as company_name')->where('status',1)->groupBy('company_id')->orderBy('company_name')->findAll();
        if(($companies_with_price_list[0]['company_id']??-99) == 1)
            $companies_with_price_list[0]['company_name'] = '- DEFAULT -';

        $companies_with_out_price_list = [];
        //BluelimeSystem::print_associated_array($companies);
        foreach($companies as $company){
            $ignore = false;
            foreach($companies_with_price_list as $company2){
                if($company['id'] == $company2['company_id']){
                    $ignore = true;
                    break;
                }
            }
            if(!$ignore)
                $companies_with_out_price_list[] = $company;
        }

        $data['active_menu'] = 'setupdata';
        $data['pricelist'] = $pricelist;
        $data['pricelist_detail'] = $pricelist_detail??[];
        $data['companies_with_price_list'] = $companies_with_price_list;
        $data['companies_with_out_price_list'] = $companies_with_out_price_list;
        $data['price'] = $price;
        $data['companies'] = $companies;
        $data['services'] = $services;
        $data['pricelist_company_name'] = $pricelist_company_name??"";
        $data['loggeduserdata'] = $loggeduserdata;

        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)
            .view('Inventory/Finance/_left_sidebar_finance')
            .view('Include/_left_sidebar_bottom')
            #.view('Inventory/Finance/new_price_setup')
            .view('Hospital/Admin/new_price_setup')
            .view('Include/_footer');
    }

    public function addpricemain()
    {
        $pricelistModel = model('Hospital/PricelistModel');
        $companyModel = model('Hospital/CompanyModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];

        
        $company_id     = $_POST['company_id'];
        $company_name = $companyModel->where('id',$company_id )->findColumn('company_name');
        $registered_by    = $logged_user;
        $description    = $_POST['description'];
        $company_name  = $company_name[0];
        $status            = $_POST['status'];
        $registered_by_name        = $logged_user_name;

        $data = [
            'company_id'        => $company_id,
            'registered_by'           => $registered_by,
            'description'           => $description,
            'company_name'            => $company_name,
            'status'              => $status, 
            'registered_by_name'    => $registered_by_name
        ];
        
        $id = $_POST['id']?? 0;
        if ($id != 0)
            $pricelistModel->update($id,$data);
        else
            $id = $pricelistModel->insert($data,true);

        return redirect()->to(site_url('/inventory/finance/pricesetup?plid='.$id));
    }

    public function addpricedetail()
    {
        $pricelistDetailModel = model('Hospital/PricelistDetailModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];

        $pricelistDetailModel->where('pricelist_id',$_POST['pricelist_id'] [0])->delete();
        for($i=0; $i<count( $_POST['service_id']); $i++) {
            $service_price = $_POST['service_price'] [$i];
            $service_id = $_POST['service_id'] [$i];
            $service_name = $_POST['service_name'] [$i];
            $service_type = $_POST['service_type'] [$i];
            $pricelist_id = $_POST['pricelist_id'] [$i];
            $status = $_POST['status'] [$i];
            $registered_by = $logged_user;
            $registered_by_name = $logged_user_name;

            $data_detail= array(
            'service_price' => $service_price, 
            'service_id' => $service_id,  
            'service_name' => $service_name,
            'service_type' => $service_type,
            'pricelist_id' => $pricelist_id, 
            'registered_by' => $registered_by, 
            'registered_by_name' => $registered_by_name, 
            'status' => $status 
            );
            
            if(intval($service_price) > 0){
            $pricelistDetailModel->insert($data_detail,true);    
            }
        }
        return redirect()->to(site_url('/inventory/finance/pricesetup?plid='.$_POST['pricelist_id'] [0]));
    }

    public function addservice()
    {
        $serviceModel = model('Hospital/ServiceModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];

        $service_name    = $_POST['service_name'];
        $service_type    = $_POST['service_type'];
        $measurement     = $_POST['measurement'];
        $gl_account       = $_POST['gl_account'];
        $is_active       = $_POST['status'];
        $registered_by_name        = $logged_user_name;
        $registered_by        = $logged_user;

        $data = [
            'service_name'        => $service_name,
            'service_type'           => $service_type,
            'measurement'           => $measurement,
            'is_active'            => $is_active,
            'registered_by'              => $registered_by, 
            'registered_by_name'    => $registered_by_name,
            'gl_account'    => $gl_account,
            'service_code' => ''
        ];

        if($data['service_type'] == 'Procedure (Nurse)'){
            $data['service_type'] = 'Procedure';
            $data['service_code'] = 'NURSE';
        }
        
        $id = $_POST['id']?? 0;
        $edit = intval($_POST['edit'])?? 0;
        if ($id != 0 && $edit == 1)
            $serviceModel->update($id,$data);
        else
            $serviceModel->insert($data);

        return redirect()->to(site_url('/inventory/finance/newservice'));
    }

    public function addcompany()
    {
        $companyModel = model('Hospital/CompanyModel');
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $logged_user = $loggeduserdata['id'];
        $logged_user_name = $loggeduserdata['name'];

        
        $company_name    = $_POST['company_name'];
        $tin_number    = $_POST['tin_number'];
        $description    = $_POST['description'];
        $unpaid_visit_days    = $_POST['unpaid_visit_days'];
        $is_active            = $_POST['status'];
        $is_cash            = $_POST['is_cash'];
        $registered_by_name        = $logged_user_name;
        $registered_by        = $logged_user;

        $data = [
            'company_name'        => $company_name,
            'tin_number'           => $tin_number,
            'description'           => $description,
            'unpaid_visit_days'           => $unpaid_visit_days,
            'is_active'            => $is_active,
            'is_cash'            => $is_cash,
            'registered_by'              => $registered_by, 
            'registered_by_name'    => $registered_by_name
        ];
        
        $id = $_POST['id']?? 0;
        $edit = intval($_POST['edit'])?? 0;
        if ($id != 0 && $edit == 1)
            $companyModel->update($id,$data);
        else
            $id = $companyModel->insert($data,true);

        return redirect()->to(site_url('/inventory/finance/newcompany'));
    }

    public function changepricestatus()
    {
        $pricelistModel = model('Hospital/PricelistModel');
        
        $price_list_id = $_GET['plid'];
        $status = $_GET['st'];
        $data = [
            'status' => $status
        ];

        $pricelistModel->update($price_list_id,$data);
       return redirect()->to(site_url('/inventory/finance/pricesetup?plid='.$price_list_id ));
    }
    public function changecompanystatus()
    {
        $companyModel = model('Hospital/CompanyModel');
        
        $company_id = $_GET['cid'];
        $status = $_GET['st'];
        $data = [
            'is_active' => $status
        ];

        $companyModel->update($company_id,$data);
       return redirect()->to(site_url('/inventory/finance/newcompany?cid='.$company_id ));
    }
    public function changeservicestatus()
    {
        $serviceModel = model('Hospital/ServiceModel');
        
        $service_id = $_GET['sid'];
        $status = $_GET['st'];
        $data = [
            'is_active' => $status
        ];

        $serviceModel->update($service_id,$data);
       return redirect()->to(site_url('/inventory/finance/newservice?sid='.$service_id ));
    }
    public function changepricedetailstatus()
    {
        $pricelistDetailModel = model('Hospital/PricelistDetailModel');
        
        $price_list_detail_id = $_GET['pldid'];
        $status = $_GET['st'];
        $data = [
            'status' => $status
        ];

        $pricelistDetailModel->update($price_list_detail_id,$data);
       return redirect()->to(site_url('/inventory/finance/pricesetup?plid='.@$_GET['plid'] ));
    }

    
    public function doctors_work(){
        $date_from = $_POST['date_from'] ?? date('Y-m-d');
        $date_to = $_POST['date_to'] ?? date('Y-m-d');

        $date_from = substr($date_from,0,10).' 00:00:00';
        $date_to = substr($date_to,0,10).' 23:59:59';

        $userModel = model('System/UserModel');
        $billModel = model('Hospital/BillModel');
        $billDetailModel = model('Hospital/BillDetailModel');
        $visitModel = model('Hospital/VisitModel');
        
        $doctors = $userModel->select('id, name')->where('role', BluelimeConfiguration::USER_ROLE::DOCTOR)->findAll();
        $doctors_id_list = BluelimeSystem::collect_field_value_from_object($doctors,'id');
        $doctors = BluelimeSystem::index_object_list_array_by_unique_field($doctors, 'id');
        
        $bills = $billModel->whereIn('registered_by', $doctors_id_list)->where('bill_status', BluelimeConfiguration::BILL_RECIEVED)->where("payed_date >=", $date_from)->where("payed_date <=", $date_to)->findAll();
        $bills_id_list = [...BluelimeSystem::collect_field_value_from_object($bills,'id'), -1];
        $bill_details = $billDetailModel->whereIn('bill_id', $bills_id_list)->whereIn('service_type', [BluelimeConfiguration::SERVICE_TYPE::PATHOLOGY, BluelimeConfiguration::SERVICE_TYPE::PROCEDURE])->where('service_status !=',BluelimeConfiguration::SERVICE_STATUS_DELAY)->findAll();
        $bills = BluelimeSystem::index_object_list_array_by_unique_field($bills, 'id');

        $report_data = [];
        foreach($bill_details as $bill_detail){
            $doctor_id = $bills['id_'.$bill_detail['bill_id']]['registered_by'];
            if(!isset($report_data['doctor_'.$doctor_id])){
                $report_data['doctor_'.$doctor_id] = [];
                $report_data['doctor_'.$doctor_id]['doctor'] = ['name' => $doctors['id_'.$doctor_id]['name']];
                $report_data['doctor_'.$doctor_id]['works'] = [];
            }

            $service_id = 'service_id_'.$bill_detail['service_id'];
            // if($bills['id_'.$bill_detail['bill_id']]['bill_type'] == 'Credit')
            //     $service_id .= '_c';

            if(!isset($report_data['doctor_'.$doctor_id]['works'][$service_id])){
                $report_data['doctor_'.$doctor_id]['works'][$service_id] = [
                    'service_name'  => $bill_detail['service_name'],
                    'service_type'  => $bill_detail['service_type'],
                    'bill_quantity' => 0,
                    'bill_amount'   => 0,
                    'bill_type'     => 'Cash',
                    'service_type_sort'  => ($bill_detail['service_type'] == BluelimeConfiguration::SERVICE_TYPE::CONSULTATION)?'0':'2'
                ];

                // if($bills['id_'.$bill_detail['bill_id']]['bill_type'] == 'Credit')
                //     $report_data['doctor_'.$doctor_id]['works'][$service_id]['bill_type'] = 'Credit';
            }

            $report_data['doctor_'.$doctor_id]['works'][$service_id]['bill_quantity']++; //= $bill_detail['bill_quantity'];
            $report_data['doctor_'.$doctor_id]['works'][$service_id]['bill_amount'] += $bill_detail['bill_amount'] * $bill_detail['bill_quantity'];
        }

        $paid_visits = $visitModel->select('MAX(doctor_id) AS doctor_id, bill_id')->where('bill_id >',0)->where('visit_status !=',BluelimeConfiguration::VISIT_STATUS_NEW)->where("visit_date >=", $date_from)->where("visit_date <=", $date_to)->groupBy('bill_id')->findAll();
        $consultation_bill_id_list = [...BluelimeSystem::collect_field_value_from_object($paid_visits,'bill_id'),-1];
        $consultation_bills = $billModel->select('id, bill_type')->whereIn('id', $consultation_bill_id_list)->where('bill_status', BluelimeConfiguration::BILL_RECIEVED)->findAll() ?? [['id'=>-99]];
        //$consultation_bills = $billModel->select('id, bill_type')->whereIn('id', $consultation_bill_id_list)->where('bill_status', BluelimeConfiguration::BILL_RECIEVED)->where("payed_date >=", $date_from)->where("payed_date <=", $date_to)->findAll() ?? [['id'=>-99]];
        $paid_consultation_bill_id_list = [...BluelimeSystem::collect_field_value_from_object($consultation_bills,'id'),-99];
        $consultation_bill_details = $billDetailModel->select('bill_id, bill_amount, bill_quantity, service_name, service_type')->whereIn('bill_id', $paid_consultation_bill_id_list)->where('service_id', BluelimeConfiguration::CONSULTATION_SERVICE_ID)->findAll();

        $paid_visits = BluelimeSystem::index_object_list_array_by_unique_field($paid_visits, 'bill_id');
        $consultation_bills = BluelimeSystem::index_object_list_array_by_unique_field($consultation_bills, 'id');
        $consultation_bill_details = BluelimeSystem::index_object_list_array_by_unique_field($consultation_bill_details, 'bill_id');

        foreach($consultation_bill_details as $bill_detail){
            $doctor_id = $paid_visits['bill_id_'.$bill_detail['bill_id']]['doctor_id'];
            if(!isset($report_data['doctor_'.$doctor_id])){
                $report_data['doctor_'.$doctor_id] = [];
                $report_data['doctor_'.$doctor_id]['doctor'] = ['name' => $doctors['id_'.$doctor_id]['name']];
                $report_data['doctor_'.$doctor_id]['works'] = [];
            }

            $service_id = 'service_id_'.BluelimeConfiguration::CONSULTATION_SERVICE_ID.$bill_detail['bill_amount'];
            if($consultation_bills['id_'.$bill_detail['bill_id']]['bill_type'] == 'Credit')
                $service_id .= '_c';

            if(!isset($report_data['doctor_'.$doctor_id]['works'][$service_id])){
                $report_data['doctor_'.$doctor_id]['works'][$service_id] = [
                    'service_name'  => $bill_detail['service_name'],
                    'service_type'  => $bill_detail['service_type'],
                    'bill_quantity' => 0,
                    'bill_amount'   => 0,
                    'bill_type'     => 'Cash',
                    'service_type_sort'  => ($bill_detail['service_type'] == BluelimeConfiguration::SERVICE_TYPE::CONSULTATION)?'0':'1',
                    'bill_amount_group' => $bill_detail['bill_amount']
                ];

                if($consultation_bills['id_'.$bill_detail['bill_id']]['bill_type'] == 'Credit'){
                    $report_data['doctor_'.$doctor_id]['works'][$service_id]['bill_type'] = 'Credit';
                    $report_data['doctor_'.$doctor_id]['works'][$service_id]['service_type_sort'] = '1';
                }
            }

            $report_data['doctor_'.$doctor_id]['works'][$service_id]['bill_quantity']++; //= $bill_detail['bill_quantity'];
            $report_data['doctor_'.$doctor_id]['works'][$service_id]['bill_amount'] += $bill_detail['bill_amount'] * $bill_detail['bill_quantity'];
        }

        foreach($report_data as $index => $row){
            $report_data[$index]['works'] = BluelimeSystem::sort_array($report_data[$index]['works'],['service_type_sort']);
        }

        //BluelimeSystem::print_associated_array($report_data,false);

        $data['report_data'] = $report_data;
        $data['date_from'] = substr($date_from,0,10);
        $data['date_to'] = substr($date_to,0,10);
        $data['active_menu'] = 'doctors_work';

        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Inventory/Finance/_left_sidebar_finance')
            .view('Include/_left_sidebar_bottom')
            .view('Inventory/Finance/doctors_work')
            .view('Include/_footer');
    }
    public function approvesalessummary(){
        $sid = $_POST['sid'] ?? -1;
        $cashierSummaryModel = model('POS/CashierSummaryModel');
        $cashierSummary = $cashierSummaryModel->find($sid);
        //BluelimeSystem::show_and_die($cashierSummary);
        if(intval($cashierSummary['summary_status']) == 0){
            $cashierSummaryModel->update($sid, ['summary_status' => 1]);
        }
        $result = '{"status":"success","result":"saved"}';

        return $result;
    }

    public function pharmacy_items(){
        $peachtreeItemModel = model('Inventory/PeachtreeItemModel');
        $itemModel = model('Inventory/ItemModel');

        $items = $itemModel->findAll();
        $items = BluelimeSystem::index_object_list_array_by_unique_field($items, 'id');
        $peachtreeItems = $peachtreeItemModel->findAll();

        foreach($peachtreeItems as $peachtreeItem){
            if(isset($items['id_'.$peachtreeItem['item_id']])){
                $items['id_'.$peachtreeItem['item_id']]['peachtree_item'] = $peachtreeItem;
            }
        }
        
        $data = ['active_menu'=>'pharmacy_items', 'items'=>$items];
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)
            .view('Inventory/Finance/_left_sidebar_finance')
            .view('Include/_left_sidebar_bottom')
            #.view('Inventory/Finance/new_price_setup')
            .view('Inventory/Finance/pharmacy_items')
            .view('Include/_footer');
    }

    public function export_pharmacy_items(){
        $only_new = $_GET['only_new'] ?? 0;
        $peachtreeItemModel = model('Inventory/PeachtreeItemModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $peachtree_integration = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_integration = $peachtree_integration[0]['data'];

        if($only_new == 1){
            $peachtreeItemModel = $peachtreeItemModel->where('exported', 0);
        }
        $peachtreeItems = $peachtreeItemModel->findAll();

        $peachtreeItemModel->where('id >',0)->update(row:['exported'=>1]);

        $items = BluelimeSystem::gather_objects_by_reference($peachtreeItems,'item_id','Inventory/ItemModel','id', true);
        $csv_data = [];
        foreach($peachtreeItems as $peachtreeItem){
            if(!isset($items['id_'.$peachtreeItem['item_id']]))
                continue;
            
            $item = $items['id_'.$peachtreeItem['item_id']];
            $gl_sales_account = $peachtree_integration['gl_account']['sales']['drug'];
            $gl_inventory_account = $peachtree_integration['gl_account']['inventory']['drug'];
            $gl_cost_account = $peachtree_integration['gl_account']['cost_of_sales']['drug'];
            if($item['item_type'] == 'COSMOTICS'){
                $gl_sales_account = $peachtree_integration['gl_account']['sales']['cosmetics'];
                $gl_inventory_account = $peachtree_integration['gl_account']['inventory']['cosmetics'];
                $gl_cost_account = $peachtree_integration['gl_account']['cost_of_sales']['cosmetics'];
            }

            $csv_data_temp = [
                'peachtree_code' => $peachtreeItem['peachtree_id'].'_'.$peachtree_integration['code_suffix']['store'],
                'peachtree_discription' => $peachtreeItem['peachtree_description'],
                'Item Class' => 1,
                'Cost method' => 0, //Avarage
                'G/L Sales Account' => $gl_sales_account.'-'.$peachtree_integration['code_suffix']['store'],
                'G/L Inventory Account' => $gl_inventory_account.'-'.$peachtree_integration['code_suffix']['store'],
                'G/L COGS/Salary Acct' => $gl_cost_account.'-'.$peachtree_integration['code_suffix']['store']
            ];

            $csv_data[] = $csv_data_temp;
            $csv_data_temp['peachtree_code'] = $peachtreeItem['peachtree_id'].'_'.$peachtree_integration['code_suffix']['dispensary'];
            $csv_data_temp['G/L Sales Account'] = $gl_sales_account.'-'.$peachtree_integration['code_suffix']['dispensary'];
            $csv_data_temp['G/L Inventory Account'] = $gl_inventory_account.'-'.$peachtree_integration['code_suffix']['dispensary'];
            $csv_data_temp['G/L COGS/Salary Acct'] = $gl_cost_account.'-'.$peachtree_integration['code_suffix']['dispensary'];
            $csv_data[] = $csv_data_temp;

            if(!empty($item['compounding_volume'])){
                $csv_data_temp['peachtree_code'] = $peachtreeItem['peachtree_id'].'_'.$peachtree_integration['code_suffix']['compounding'];
                $csv_data_temp['peachtree_discription'] = $peachtreeItem['peachtree_description'];
                $csv_data_temp['G/L Sales Account'] = $peachtree_integration['gl_account']['sales']['compounding'].'-'.$peachtree_integration['code_suffix']['dispensary'];
                $csv_data_temp['G/L Inventory Account'] = $peachtree_integration['gl_account']['inventory']['compounding'].'-'.$peachtree_integration['code_suffix']['dispensary'];
                $csv_data_temp['G/L COGS/Salary Acct'] = $peachtree_integration['gl_account']['cost_of_sales']['compounding'].'-'.$peachtree_integration['code_suffix']['dispensary'];

                $csv_data[] = $csv_data_temp;
            }

        }

        if(empty($csv_data)){
            $csv_data[] = [
                'peachtree_code' => '',
                'peachtree_discription' => '',
                'Item Class' => '',
                'Cost method' => '',
                'G/L Sales Account' => '',
                'G/L Inventory Account' => '',
                'G/L COGS/Salary Acct' => ''
            ];
        }

        return BluelimeSystem::export_arra_in_csv($csv_data, 'pharmacy_items_'.$peachtree_integration['branch']['name'].'_'.date('Y-m-d').'.csv');
    }

    public function inventory_quantity_on_hand(){
        $store_id = $_GET['store_id'] ?? 0;
        $show_zero = $_GET['show_zero'] ?? 1;

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $peachtree_integration = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_integration = $peachtree_integration[0]['data'];

        $store_name = '';
        $peachtree_suffix = '';
        switch ($store_id){
            case 1:
                $store_name = 'Main Store';
                $peachtree_suffix = $peachtree_integration['code_suffix']['store'];
                break;
            case 2:
                $store_name = 'Dispensary';
                $peachtree_suffix = $peachtree_integration['code_suffix']['dispensary'];
                break;
            case 3:
                $store_name = 'Compounding';
                $peachtree_suffix = $peachtree_integration['code_suffix']['compounding'];
                break;
        }

        $items = [];
        if($store_id > 0)
            $items = BluelimeSystem::get_all_items_quantity($store_id);

        $inventory_data = [];
        foreach($items as $item){
            if(($item['current_balance'] == 0) && !$show_zero)
                continue;

            //Compounding
            if(($store_id == 3) && (floatval($item['compounding_volume']) == 0))
                continue;

            $inventory_data[] = $item;
        }

        $peachtree_items = BluelimeSystem::gather_objects_by_reference($inventory_data,'id','Inventory/PeachtreeItemModel','item_id',true);
        foreach($inventory_data as $index => $row){
            $inventory_data[$index]['peachtree_id'] = $peachtree_items['item_id_'.$row['id']]['peachtree_id'].'_'.$peachtree_suffix;
            $inventory_data[$index]['peachtree_description'] = $peachtree_items['item_id_'.$row['id']]['peachtree_description'];
        }

        $data = [
            'store_name' => $store_name,
            'inventory_data' => $inventory_data,
            'store_id' => $store_id,
            'active_menu'=>'inventory',
            'active_sub_menu'=>'quantity_on_hand'
        ];
        return view('Inventory/Finance/inventory_qty_on_hand', $data);
    }

    public function inventory_export_for_counting(){
        $store_id = $_GET['store_id'];
        $inventoryCountSetupModel = model('Inventory/InventoryCountSetupModel');
        $itemModel = model('Inventory/ItemModel');
        $peachtreeItemModel = model('Inventory/PeachtreeItemModel');
        $itemLocationModel = model('Inventory/ItemLocationModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $finance_maintain = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_integration = $finance_maintain[0]['data'];

        $store_type = '';
        if($store_id == 1)
            $store_type = 'store';
        elseif($store_id == 2)
            $store_type = 'dispensary';
        elseif($store_id == 3)
            $store_type = 'compounding';

        $items = [];
        if($store_id > 0)
            $items = BluelimeSystem::get_all_items_with_price_and_quantity($store_id,true);

        //$items = $itemModel->findAll();

        $peachtree_items = $peachtreeItemModel->findAll();
        $peachtree_items = BluelimeSystem::index_object_list_array_by_unique_field($peachtree_items,'item_id');
        $item_locations = $itemLocationModel->where('store_id', $store_id)->findAll();
        $item_locations = BluelimeSystem::index_object_list_array_by_unique_field($item_locations,'item_id');

        $inventory_data = [];
        $sn = 1;
        foreach($items as $item){
            // Compounding store only show items with compounding_volume
            if(($store_id == 3) && (floatval($item['compounding_volume']) == 0))
                continue;

            if((!isset($item_locations['item_id_'.$item['id']]['location'])) && ($item['current_balance'] == 0))
                continue;

            $item_description = $item['item_brand_name'];
            if(trim($item_description) != $item['item_generic_name'])
                $item_description .= " ".$item['item_generic_name'];
            if(!empty(trim($item['preparation'])))
                $item_description .= " ".$item['preparation'];
            if(!empty(trim($item['strength'])))
                $item_description .= " ".$item['strength'];
            $item_description = ucwords(strtolower($item_description));
            $item_description = explode(" ",$item_description);
            $item_description = array_unique($item_description);
            $item_description = implode(" ", $item_description);

            $inventory_data[] = [
                'id' => $item['id'],
                'peatchree_id' => $peachtree_items['item_id_'.$item['id']]['peachtree_id'].'_'.$peachtree_integration['code_suffix'][$store_type],
                'Item Description' => $item_description, //$peachtree_items['item_id_'.$item['id']]['peachtree_description'],
                'Unit' => $item['measurement'],
                'Unit Price' => $item['unit_price'],
                'System Qty' => $item['current_balance'],
                'Count Qty' => '',
                'location' => $item_locations['item_id_'.$item['id']]['location'] ?? 'ZZLast',
            ];
        }

        $inventory_data = BluelimeSystem::sort_array($inventory_data, ['location','id']);

        $csv_data = [];
        $sn = 1;
        foreach($inventory_data as $row){
            $csv_data[] = [
                'SN' => $sn++,
                'peatchree_id' => $row['peatchree_id'],
                'Item Description' => $row['Item Description'],
                'Unit' => $row['Unit'],
                'Unit Price' => $row['Unit Price'],
                'System Qty' => $row['System Qty'],
                'Count Qty' => $row['Count Qty'],
                'Remark' => ''
            ];
        }


        BluelimeSystem::export_arra_in_csv($csv_data, 'Inventory Export For Counting '.$peachtree_integration['branch']['name'].' '.$store_type.' '.date('Y-m-d').'.csv');
    }

    public function stock_card(){
        $item_id = $_GET['item_id'];
        $store_id = $_GET['store_id'];

        $store_name = '';
        switch ($store_id){
            case 1:
                $store_name = 'Main Store';
                break;
            case 2:
                $store_name = 'Dispensary';
                break;
            case 3:
                $store_name = 'Compounding';
                break;
        }


        $item_id = $_GET['item_id'];
        $itemModel = model('Inventory/ItemModel');
        $peachtreeItemModel = model('Inventory/PeachtreeItemModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        
        $stock_cards = BluelimeSystem::get_stock_cards($item_id, $store_id);
        $selected_item = $itemModel->find($item_id);
        $peachtree_items = $peachtreeItemModel->where('item_id',$item_id)->findAll(1);
        $peachtree_id = $peachtree_items[0]['peachtree_id'];

        $finance_maintain = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_ingegratoin = $finance_maintain[0]['data'];
        $peachtree_id .= '_'.$peachtree_ingegratoin['code_suffix']['store'];
        $selected_item['peachtree_id'] = $peachtree_id;

        $store_title = $store_name.' #'.$peachtree_ingegratoin['branch']['number'].' ('.$peachtree_ingegratoin['branch']['name'].')';

        $data['stock_cards'] = $stock_cards;
        $data['selected_item'] = $selected_item;
        $data['store_title'] = $store_title;
        $data['active_menu'] = 'inventory';
        
        return view('Inventory/Finance/stock_card',$data);
    }

    public function inventory_count_setup(){
        $inventoryCountSetupModel = model('Inventory/InventoryCountSetupModel');

        $inventory_count_setups = $inventoryCountSetupModel->orderBy('id','DESC')->findAll();

        $data['inventory_count_setups'] = $inventory_count_setups;
        $data['active_menu'] = 'inventory';
        $data['active_sub_menu'] = 'count_setup';
        return view('Inventory/Finance/inventory_count_setup',$data);
    }

    public function add_inventory_count_setup(){
        $title = $_POST['title'];
        $is_year_end = $_POST['is_year_end'] ?? false;
        $from_date = $_POST['from_date'] ?? '';
        $to_date = $_POST['to_date'] ?? '';

        $inventoryCountSetupModel = model('Inventory/InventoryCountSetupModel');

        $data = [
            'title' => $title,
            'is_year_end' => boolval($is_year_end)
        ];

        if(!empty($from_date) && !empty($to_date)){
            $data['from_date'] = $from_date;
            $data['to_date'] = $to_date;
        }

        $inventoryCountSetupModel->insert($data);

        return redirect()->to('/inventory/finance/inventory_count_setup');
    }

    public function inventory_count_setup_active_inactive(){
        $id = $_POST['id'];
        $active = $_POST['active'];
        $inventoryCountSetupModel = model('Inventory/InventoryCountSetupModel');
        $inventoryCountSetupModel->update($id,['active'=>$active]);
        return $inventoryCountSetupModel->update($id,['active'=>$active]);
    }

    public function inventory_count_record(){
        $store_id = $_GET['store_id'] ?? 0;
        $type = $_GET['type']?? '';

        $inventory_count_setup_id = $_GET['inventory_count_setup_id'] ?? 0;
        $inventoryCountSetupModel = model('Inventory/InventoryCountSetupModel');
        $itemModel = model('Inventory/ItemModel');
        $peachtreeItemModel = model('Inventory/PeachtreeItemModel');
        $itemLocationModel = model('Inventory/ItemLocationModel');
        $inventoryItemCountRecordModel = model('Inventory/InventoryItemCountRecordModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $finance_maintain = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_integration = $finance_maintain[0]['data'];
        
        $inventory_count_setups = $inventoryCountSetupModel->orderBy('title','ASC')->findAll();
        $inventory_count_setups = BluelimeSystem::index_object_list_array_by_unique_field($inventory_count_setups,'id');
        
        $items= [];
        $store_type='';
        if($store_id > 0){
            $items = $itemModel->findAll();
            $peachtree_items = $peachtreeItemModel->findAll();
            $peachtree_items = BluelimeSystem::index_object_list_array_by_unique_field($peachtree_items,'item_id');
            $item_locations = $itemLocationModel->where('store_id', $store_id)->findAll();
            $item_locations = BluelimeSystem::index_object_list_array_by_unique_field($item_locations,'item_id');
            $item_count_record = $inventoryItemCountRecordModel->where('store_id', $store_id)->where('inventory_count_id', $inventory_count_setup_id)->findAll();
            $item_count_record = BluelimeSystem::index_object_list_array_by_unique_field($item_count_record,'item_id');
            
            if($store_id == 1)
                $store_type = 'store';
            elseif($store_id == 2)
                $store_type = 'dispensary';
            elseif($store_id == 3)
                $store_type = 'compounding';
        }

        $report_data = [];
        foreach($items as $index => $item){
            if(($type=='drug') && ($item['item_type'] != 'DRUG'))
                continue;
            //Cosmetic
            if(($type=='cosmetics') && ($item['item_type'] != 'COSMOTICS'))
                continue;

            $items[$index]['location'] = $item_locations['item_id_'.$item['id']]['location'] ?? 'ZZLast';
            $items[$index]['peachtree_detail'] = $peachtree_items['item_id_'.$item['id']];
            $items[$index]['quantity'] = $item_count_record['item_id_'.$item['id']]['quantity'] ?? '';
            $items[$index]['remark'] = $item_count_record['item_id_'.$item['id']]['remark'] ?? '';

            $report_data[] = $items[$index];
        }
       
        $items = BluelimeSystem::sort_array($report_data, ['location','item_brand_name']);

        $data['inventory_count_setups'] = $inventory_count_setups;
        $data['inventory_count_setup_id'] = $inventory_count_setup_id;
        $data['store_id'] = $store_id;
        $data['peachtree_suffix'] = $peachtree_integration['code_suffix'][$store_type] ?? '';
        $data['items'] = $items;
        $data['type'] = $type;
        $data['active_menu'] = 'inventory';
        $data['active_sub_menu'] = 'count_record';
        return view('Inventory/Finance/inventory_count_record',$data);
    }

    public function inventory_count_record_add(){
        $item_id = $_POST['item_id'];
        $quantity = $_POST['quantity'];
        $remark = $_POST['remark'];
        $store_id = $_POST['store_id'];
        $inventory_count_id = $_POST['inventory_count_id'];

        $inventoryItemCountRecordModel = model('Inventory/InventoryItemCountRecordModel');
        $inventoryItemCountRecordModel->where('item_id',$item_id)->where('store_id',$store_id)->where('inventory_count_id', $inventory_count_id)->delete();

        $inventoryItemCountSetupModel = model('Inventory/InventoryCountSetupModel');
        $inventoryItemCountSetup = $inventoryItemCountSetupModel->find($inventory_count_id);

        //$unit_price = BluelimeSystem::get_latest_price_for_item($item_id);
        $transactionModel = model('Inventory/TransactionModel');
        $last_unit_cost = $transactionModel->select('unit_cost')->where('store_id', $store_id)->where('item_id', $item_id)->where('unit_cost >',0)->where('created_at >=', $inventoryItemCountSetup['from_date'])->where('created_at <=', $inventoryItemCountSetup['to_date'])->orderBy('id','DESC')->first();
        $unit_cost = $last_unit_cost['unit_cost'] ?? 0;

        $data = [
            'item_id'   => $item_id,
            'quantity'  => $quantity,
            //'unit_price' => $unit_price,
            'unit_cost' => $unit_cost,
            'remark'    => $remark,
            'store_id'  => $store_id,
            'inventory_count_id' => $inventory_count_id
        ];

        $id = -1;

        if(($inventoryItemCountSetup['active']??false))
            $id = $inventoryItemCountRecordModel->insert($data);

        if($id > 0)
            return BluelimeSystem::get_success_ajax_return('inserted');

        return BluelimeSystem::get_error_ajax_return('Not inserted');
    }

    public function export_inventory_count_record(){
        $store_id = $_GET['store_id'] ?? 0;
        $type = $_GET['type'] ?? '';
        $inventory_count_setup_id = $_GET['inventory_count_setup_id'] ?? 0;
        $inventoryCountSetupModel = model('Inventory/InventoryCountSetupModel');
        $itemModel = model('Inventory/ItemModel');
        $peachtreeItemModel = model('Inventory/PeachtreeItemModel');
        $itemLocationModel = model('Inventory/ItemLocationModel');
        $inventoryItemCountRecordModel = model('Inventory/InventoryItemCountRecordModel');
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $transactionModel = model('Inventory/TransactionModel');

        $finance_maintain = $financeMaintainModel->where('type',BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::PEACHTREE_INTEGRATION)->findAll();
        $peachtree_integration = $finance_maintain[0]['data'];
        
        $inventory_count_setup = $inventoryCountSetupModel->find($inventory_count_setup_id);

        $item_transactions = $transactionModel->select('item_id, SUM(quantity) as sys_quantity, NULL as unit_cost')->where('store_id', $store_id)->where('created_at >=', $inventory_count_setup['from_date'])->where('created_at <=', $inventory_count_setup['to_date'])->groupBy('item_id')->findAll();
        $last_unit_cost_id = $transactionModel->select('MAX(id) as id, item_id')->where('store_id', $store_id)->where('unit_cost >',0)->where('created_at >=', $inventory_count_setup['from_date'])->where('created_at <=', $inventory_count_setup['to_date'])->groupBy('item_id')->findAll();
        $last_unit_cost_transaction = $transactionModel->whereIn('id',[...array_column($last_unit_cost_id,'id'),-1])->findAll();

        $item_transactions = BluelimeSystem::index_object_list_array_by_unique_field($item_transactions, 'item_id');

        foreach($last_unit_cost_transaction as $unit_cost_transaction){
            $item_transactions['item_id_'.$unit_cost_transaction['item_id']]['unit_cost'] = $unit_cost_transaction['unit_cost'];
        }

        
        $store_type = '';
        $items= [];
        if($store_id > 0){
            $items = $itemModel->findAll();
            $peachtree_items = $peachtreeItemModel->findAll();
            $peachtree_items = BluelimeSystem::index_object_list_array_by_unique_field($peachtree_items,'item_id');
            $item_locations = $itemLocationModel->where('store_id', $store_id)->findAll();
            $item_locations = BluelimeSystem::index_object_list_array_by_unique_field($item_locations,'item_id');
            $item_count_record = $inventoryItemCountRecordModel->where('store_id', $store_id)->where('inventory_count_id', $inventory_count_setup_id)->findAll();
            $item_count_record = BluelimeSystem::index_object_list_array_by_unique_field($item_count_record,'item_id');
            
            if($store_id == 1)
                $store_type = 'store';
            elseif($store_id == 2)
                $store_type = 'dispensary';
            elseif($store_id == 3)
                $store_type = 'compounding';
        }

        $counted_items = [];
        $sn = 1;
        foreach($items as $index => $item){
            if(!isset($item_locations['item_id_'.$item['id']]['location']))
                continue;

            if(($type=='drug') && ($item['item_type'] != 'DRUG'))
                continue;
            
            if(($type=='cosmetics') && ($item['item_type'] != 'COSMOTICS'))
                continue;

            $items[$index]['location'] = $item_locations['item_id_'.$item['id']]['location'] ?? 'ZZLast';
            $items[$index]['peachtree_detail'] = $peachtree_items['item_id_'.$item['id']];
            $items[$index]['quantity'] = $item_count_record['item_id_'.$item['id']]['quantity'] ?? null;
            $items[$index]['remark'] = $item_count_record['item_id_'.$item['id']]['remark'] ?? '';
            $items[$index]['unit_cost'] = $item_transactions['item_id_'.$item['id']]['unit_cost'] ?? null;
            $items[$index]['sys_quantity'] = $item_transactions['item_id_'.$item['id']]['sys_quantity'] ?? null;
            $items[$index]['expiry_date'] = $item_count_record['item_id_'.$item['id']]['expiry_date'] ?? '';

            //$item_count_record['item_id_'.$item['id']]['unit_cost'] ?? '';
            //$items[$index]['unit_price'] = $item_count_record['item_id_'.$item['id']]['unit_price'] ?? '';

            //if(($items[$index]['quantity'] !== null) || !empty($items[$index]['remark'])){
                $counted_items[] = $items[$index];
            //}
        }

        $counted_items = BluelimeSystem::sort_array($counted_items, ['location','item_brand_name']);

        $csv_data = [];
        foreach($counted_items as $row){
            $csv_data[] = [
                    'SN' => $sn++,
                    'Peachtree Id' => $row['peachtree_detail']['peachtree_id'].'_'.$peachtree_integration['code_suffix'][$store_type],
                    'Item Name' => $row['peachtree_detail']['peachtree_description'],
                    'Unit' => $row['measurement'],
                    'Sys Qty' => $row['sys_quantity'],
                    'Qty' => $row['quantity'],
                    'Unit Cost' => $row['unit_cost'],
                    //'Unit Price' => $row['unit_price'],
                    'Expiry Date' => $row['expiry_date'],
                    'Remark' => $row['remark'],
                ];
        }

        BluelimeSystem::export_arra_in_csv($csv_data,'Inventory Count '.$inventory_count_setup['title'].'.csv');
    }

    public function import_inventory_count_record(){
        $store_id = $_GET['store_id'] ?? 0;
        $inventory_count_setup_id = $_GET['inventory_count_setup_id'] ?? 0;

        if($store_id <= 0 || $inventory_count_setup_id <= 0)
            return redirect()->to('/inventory/finance/inventory_count_record?store_id='.$store_id.'&inventory_count_setup_id='.$inventory_count_setup_id);

        $store_name = $store_id == 1 ? 'Main Store' : ($store_id == 2 ? 'Dispensary' : ($store_id == 3 ? 'Compounding' : ''));
        $inventoryCountSetupModel = model('Inventory/InventoryCountSetupModel');
        $inventory_count_setup = $inventoryCountSetupModel->find($inventory_count_setup_id);

        $data = [
            'store_id' => $store_id,
            'inventory_count_setup_id' => $inventory_count_setup_id,
            'inventory_count_setup' => $inventory_count_setup,
            'store_name' => $store_name,
            'active_menu'=>'inventory',
            'active_sub_menu'=>'count_record'
        ];
        return view('Inventory/Finance/inventory_count_record_import',$data);
    }

    public function import_inventory_count_record_from_csv(){
        $store_id = $_GET['store_id'] ?? 0;
        $inventory_count_setup_id = $_GET['inventory_count_setup_id'] ?? 0;

        if($store_id <= 0 || $inventory_count_setup_id <= 0)
            return redirect()->to('/inventory/finance/inventory_count_record?store_id='.$store_id.'&inventory_count_setup_id='.$inventory_count_setup_id);

        $pasted_csv_data = $_POST['pasted_csv_data'];
        $csv_lines = explode("\n", trim($pasted_csv_data));
        $csv_headers = str_getcsv($csv_lines[0],"\t");
        $csv_headers_must_have = ['SN','peatchree_id','Item Description','Unit','System Qty','Count Qty','Unit Cost','Expiry Date',' Remark'];

        $header_diff_found = false;
        foreach($csv_headers_must_have as $index => $header){
            $csv_headers[$index] = trim($csv_headers[$index]);
            if(trim($csv_headers[$index]) != trim($header)){
                $header_diff_found = true;
                break;
            }
        }

        //BluelimeSystem::print_associated_array($csv_headers,false);

        if($header_diff_found){
            echo '
            <table border="1" cellpadding="5" cellspacing="0">
                <tr>
                    <th>CSV Headers</th>
                    <th>CSV Headers Must Have</th>
                </tr>
                <tr>
                    <td>';
                        BluelimeSystem::print_associated_array($csv_headers,false);
            echo '</td>
                  <td>';
                        BluelimeSystem::print_associated_array($csv_headers_must_have,false);
            echo '</td>
                </tr>
            </table>
            ';
            //BluelimeSystem::print_associated_array(['csv_headers'=>$csv_headers, 'csv_headers_must_have'=>$csv_headers_must_have]);
            return BluelimeSystem::get_error_ajax_return('Invalid CSV format. Please make sure the headers are correct.');
        }

        $csv_data = [];
        foreach (array_slice($csv_lines, 1) as $line) {
            $values = str_getcsv($line, "\t");
            if (count($values) == count($csv_headers)) {
                $csv_data[] = array_combine($csv_headers, $values);
            }
        }

        $inventory_item_count_record_data = [];
        foreach($csv_data as $row){
            if(empty($row['peatchree_id']))
                continue;

            $item_id = intval(substr($row['peatchree_id'], -8, 4));
            $inventory_item_count_record_data[] = [
                'inventory_count_id' => $inventory_count_setup_id,
                'store_id' => $store_id,
                'item_id' => $item_id,
                'quantity' => floatval($row['Count Qty']),
                'unit_cost' => floatval($row['Unit Cost']),
                'expiry_date' => !empty($row['Expiry Date'] ?? '') ? $row['Expiry Date'] : null,
                'remark' => substr($row['Remark'] ?? '',0,50)
            ];
        }

        $inventoryItemCountRecordModel = model('Inventory/InventoryItemCountRecordModel');
        $inventoryItemCountRecordModel->where('store_id',$store_id)->where('inventory_count_id',$inventory_count_setup_id)->delete();
        $inventoryItemCountRecordModel->insertBatch($inventory_item_count_record_data);
        return redirect()->to('/inventory/finance/inventory_count_record?store_id='.$store_id.'&inventory_count_setup_id='.$inventory_count_setup_id);
    }

    public function create_begining_balance_for_next_year(){
        $inventory_count_setup_id = $_GET['inventory_count_setup_id'];
        $inventoryCountSetupModel = model('Inventory/InventoryCountSetupModel');
        $inventoryItemCountRecordModel = model('Inventory/InventoryItemCountRecordModel');
        $transactionModel = model('Inventory/TransactionModel');

        $inventory_count_setup = $inventoryCountSetupModel->find($inventory_count_setup_id);

        $last_date = $inventory_count_setup['to_date'];
        //$year = date_format(date_create($last_date),"Y");
        $begining_date = date_add(date_create($last_date),date_interval_create_from_date_string('1 day'));
        $begining_date = date_format($begining_date,"Y-m-d");

        $batch_quantities = $transactionModel->select('store_id, item_id, batch_number, MIN(expiry_date) as expiry_date, SUM(quantity) as batch_quantity')->where('created_at >=',$inventory_count_setup['from_date'])->where('created_at <=',$inventory_count_setup['to_date'])->groupBy('store_id, item_id, batch_number')->having('batch_quantity >',0)->findAll();
        $batch_quantities = BluelimeSystem::sort_array($batch_quantities, ['item_id']);

        $item_batch_quantities = [];
        foreach($batch_quantities as $batch_quantity){
            if(!isset($item_batch_quantities['item_id_'.$batch_quantity['item_id'].'_store_id_'.$batch_quantity['store_id']]))
                $item_batch_quantities['item_id_'.$batch_quantity['item_id'].'_store_id_'.$batch_quantity['store_id']] = [];

            $item_batch_quantities['item_id_'.$batch_quantity['item_id'].'_store_id_'.$batch_quantity['store_id']][] = $batch_quantity;
        }

        foreach($item_batch_quantities as $index => $item_batch_quantity){
            $item_batch_quantities[$index] = BluelimeSystem::sort_array($item_batch_quantity, ['expiry_date']);
        }

        $inventory_count_record = $inventoryItemCountRecordModel->where('inventory_count_id', $inventory_count_setup_id)->findAll();
        $transaction_type = BluelimeConfiguration::INVENTORY_TRANSACTION_TYPE::BEGINNING;
        $begining_balance = [];
        foreach($inventory_count_record as $count_recored){
            $quantity = $count_recored['quantity'];

            foreach(($item_batch_quantities['item_id_'.$count_recored['item_id'].'_store_id_'.$count_recored['store_id']]??[]) as $batch_quantity){
                $current_quantity = $quantity;
                if($batch_quantity['batch_quantity'] <= 0)
                    continue;

                if($current_quantity > $batch_quantity['batch_quantity']){
                    $current_quantity = $batch_quantity['batch_quantity'];
                }
                $quantity -= $current_quantity;

                $begining_balance[] = [
                    'store_id' => $count_recored['store_id'],
                    'item_id'  => $count_recored['item_id'],
                    'batch_number' => $batch_quantity['batch_number'],
                    'expiry_date' => $batch_quantity['expiry_date'],
                    'transaction_type' => $transaction_type,
                    'transaction_reference' => $count_recored['id'],
                    'quantity' => $current_quantity,
                    'unit_cost' => $count_recored['unit_cost'],
                    'created_at' => $begining_date
                ];
                
                if($quantity <= 0)
                    break;
            }

            if($quantity > 0){
                $begining_balance[] = [
                    'store_id' => $count_recored['store_id'],
                    'item_id'  => $count_recored['item_id'],
                    'batch_number' => 'DEFAULT',
                    'expiry_date' => '2100-01-01',
                    'transaction_type' => $transaction_type,
                    'transaction_reference' => $count_recored['id'],
                    'quantity' => $quantity,
                    'unit_cost' => $count_recored['unit_cost'],
                    'created_at' => $begining_date
                ];
            }
        }
        $transactionModel->where('created_at =',$begining_date)->where('transaction_type', $transaction_type)->delete();
        $inventoryCountSetupModel->update($inventory_count_setup_id, ['year_end_committed'=>1,'active'=>0]);
        $transactionModel->insertBatch($begining_balance);

        $compunding_item_quantity_updater = "UPDATE `inventory_transactions` `inventory_transactions` INNER JOIN `inventory_items` ON `inventory_transactions`.`item_id` = `inventory_items`.`id`

                                            SET `inventory_transactions`.`quantity` = `inventory_transactions`.`quantity`*`inventory_items`.`compounding_volume`, 
                                                `inventory_transactions`.`updater` = 'Compounding Volume Updater'
                                            WHERE `inventory_transactions`.`store_id` = 3 AND
                                            `inventory_transactions`.`deleted_at` IS NULL AND
                                            `inventory_transactions`.`created_at` = '".$begining_date."' AND
                                            `inventory_transactions`.`transaction_type` = 'BEGINNING';
                                            ";

        $transactionModel->db->query($compunding_item_quantity_updater);
        
        return redirect()->to('inventory/finance/inventory_count_setup');
    }

    public function report_sales_chart(){
        $number_of_months = $_GET['number_of_months']??6;
        if($number_of_months > 12)
            $number_of_months = 12;

        $date_range_list = [];
        $this_month = date('m');
        $this_year = date('Y');
        for($i = $number_of_months-1; $i >= 0 ; $i--){
            $m = $this_month - $i;
            $y = $this_year;
            if($m <= 0){
                $m = 12 + $m;
                $y--;
            }
            if($m < 10)
                $m = '0'.$m;
            $month_name = date('M',strtotime($y.'-'.$m.'-01'));
            $date_range_list[] = ['month_number'=>intval($m),'month_name'=>$month_name,'from'=>"$y-$m-01", 'to'=>"$y-$m-".date('t',strtotime($y.'-'.$m.'-01'))];
            //$date_range_list[] = ['value'=>$y.'-'.$m.'-01 to '.$y.'-'.$m.'-'.date('t',strtotime($y.'-'.$m.'-01')), 'text'=>date('F, Y',strtotime($y.'-'.$m.'-01'))];
        }
        $first_date = $date_range_list[0]['from'];
        $last_date = $date_range_list[count($date_range_list)-1]['to'];
        
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');

        $finance_maintain = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::CREDIT_INVOICE_API)->findAll();
        $credit_invoice_api = $finance_maintain[0]['data'];

        $api_success = [];
        $report_data = [];
        $by_branch = [];
        $by_branch_and_department = [];

        foreach($credit_invoice_api as $api){
            $branch_number = $api['branch_number'];
            $branch_name = $api['branch_name'];
            $ip_addess = $api['ip_addess'];
            $api_key = $api['api_key'];

            $url = "http://$ip_addess/api2/finance/v1/get_sales_report_data";

            $result = BluelimeSystem::communicate_api2("finance/report_sales", $url, $api_key,['from_date'=>$first_date,'to_date'=>$last_date]);
            $result = BluelimeSystem::convert_to_array($result);

            $api_success_temp = [
                'branch_number' => $branch_number,
                'branch_name' => $branch_name,
                'ip_addess' => $ip_addess,
                'api_key' => $api_key,
                'status' => ($result['status'] ?? '') == 'success' ? 'success':'error',
                'error_message' => ''
            ];

            if(($result['status']??'')=='success'){
                $data = $result['data'] ?? [];
                if(BluelimeSystem::check_jwt_signature($data, $api_key, $result['signature'])){
                    $data = $data['report_data'];

                    $by_branch_temp = [];
                    $by_branch_lastyear_temp = [];
                    $by_branch_and_department_clinic = [];
                    $by_branch_and_department_pharmacy = [];
                    foreach($date_range_list as $date_range){
                        $by_branch_temp[$date_range['month_number']] = 0;
                        $by_branch_lastyear_temp[$date_range['month_number']] = 0;
                        $by_branch_and_department_clinic[$date_range['month_number']] = 0;
                        $by_branch_and_department_pharmacy[$date_range['month_number']] = 0;
                        foreach($data as $row){
                            if(intval($row['month']) == $date_range['month_number']){
                                $by_branch_temp[$date_range['month_number']] += intval($row['total']);
                                $by_branch_lastyear_temp[$date_range['month_number']] += intval($row['last_year_total']);
                                if($row['type'] == 'Inventory')
                                    $by_branch_and_department_pharmacy[$date_range['month_number']] += intval($row['total']);
                                else
                                    $by_branch_and_department_clinic[$date_range['month_number']] += intval($row['total']);
                            }
                        }
                    }

                    // foreach($by_branch_lastyear_temp as $index => $value){
                    //     $by_branch_lastyear_temp[$index] = $by_branch_temp[$index];
                    // }

                    $by_branch[] = [
                        'branch_name' => $branch_name,
                        'data' => $by_branch_temp,
                        'data_last_year' => $by_branch_lastyear_temp
                    ];

                    $by_branch_and_department[] = [
                        'branch_name' => $branch_name,
                        'data_clinic' => $by_branch_and_department_clinic,
                        'data_pharmacy' => $by_branch_and_department_pharmacy
                    ];
                }
                else{
                    $api_success_temp['status'] = 'error';
                    $api_success_temp['error_message'] = 'Invalid JWT Signature!';
                }
            }
            else{
                $api_success_temp['error_message'] = $result['message'] ?? 'Unknown error occurred!';
            }
            $api_success[] = $api_success_temp;
        }

        
        $view_data = [
            'by_branch' => $by_branch,
            'by_branch_and_department' => $by_branch_and_department,
            'date_range_list' => $date_range_list,
            'active_menu' => 'reports',
            'active_sub_menu' => 'report_sales_chart',
        ];
        return view('Inventory/Finance/report_sales_chart',$view_data);
    }

    public function report_sales_receipts(){
        $from_date = $_GET['from_date'] ?? date('Y-m-d');
        $to_date = $_GET['to_date'] ?? date('Y-m-d');

        if(strtotime($from_date) > strtotime($to_date)){
            $temp = $from_date;
            $from_date = $to_date;
            $to_date = $temp;
        }

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $finance_maintain = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::CREDIT_INVOICE_API)->findAll();
        $finance_api = $finance_maintain[0]['data'];

        $api_connection = [];
        $receipts = [];
        foreach($finance_api as $api){
            $ip_addess = $api['ip_addess'];
            $api_key = $api['api_key'];
            
            $url = "http://$ip_addess/api2/finance/v1/get_sales_receipt_data";
            
            $result = BluelimeSystem::communicate_api2(__METHOD__, $url, $api_key,['from_date'=>$from_date, 'to_date'=>$to_date, 'branch_number'=>$api['branch_number']]);

            $result = BluelimeSystem::convert_to_array($result);

            if(!empty($result))
                $api_connection[$api['branch_name']] = ['branch'=>$api['branch_name'], 'status'=>1];
            else
                $api_connection[$api['branch_name']] = ['branch'=>$api['branch_name'], 'status'=>0];

            $data = $result['data'] ?? [];
            
            if(BluelimeSystem::check_jwt_signature($data, $api_key, ($result['signature']??''))){
                if($result['status'] == 'success'){
                    $api_connection[$api['branch_name']] = ['branch'=>$api['branch_name'], 'status'=>2];
                    $receipts = [...$receipts, ...$data];
                }
            }
        }

        $btn_submit = $_GET['btn_submit'] ?? 'view';

        if($btn_submit == 'export'){
            return $this->export_report_sales_receipts($receipts, $from_date, $to_date);
        }

        $view_data = [
            'receipts' => $receipts,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'api_connection' => $api_connection,
            'active_menu' => 'reports',
            'active_sub_menu' => 'report_sales_receipts',
        ];
        return view('Inventory/Finance/report_sales_receipts',$view_data);
        
    }

    private function export_report_sales_receipts($receipts, $from_date, $to_date){
        $csv = [];
        $sn = 1;
        foreach($receipts as $receipt){

            $partial_payment = $receipt['total'] - $receipt['transferred_amount'];
            if($partial_payment <= 1)
                $partial_payment = '';
            elseif($receipt['transferred_amount'] <= 1)
                $partial_payment = '';

            $csv[] = [
                'SN' => $sn++,
                'Receipt Number'    => $receipt['fs_number'],
                'Payment Method'    => $receipt['payment_method'],
                'Credit Company Name'    => $receipt['company']['company_name'],
                'Amount'            => $receipt['total'],
                'Partial Payment (Cash)' => $partial_payment,
                'Date' => $receipt['response_date'],
            ];
        }
        return BluelimeSystem::export_arra_in_csv($csv, "sales_receipts_{$from_date} - {$to_date}.csv");
    }

    public function report_sales_period(){
        $ec_year = $_GET['ec_year'] ?? 2018;

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $finance_maintain = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::CREDIT_INVOICE_API)->findAll();
        $finance_api = $finance_maintain[0]['data'];

        $api_connection = [];
        $branch_sales = [];
        foreach($finance_api as $api){
            $ip_addess = $api['ip_addess'];
            $api_key = $api['api_key'];
            
            $url = "http://$ip_addess/api2/finance/v1/get_fiscal_year_sales_report_data";
            
            $result = BluelimeSystem::communicate_api2(__METHOD__, $url, $api_key,['ec_fiscal_year'=>$ec_year]);

            $result = BluelimeSystem::convert_to_array($result);

            if(!empty($result))
                $api_connection[$api['branch_name']] = ['branch'=>$api['branch_name'], 'status'=>1];
            else
                $api_connection[$api['branch_name']] = ['branch'=>$api['branch_name'], 'status'=>0];

            $data = $result['data'] ?? [];
            
            if(BluelimeSystem::check_jwt_signature($data, $api_key, ($result['signature']??''))){
                if($result['status'] == 'success')
                    $api_connection[$api['branch_name']] = ['branch'=>$api['branch_name'], 'status'=>2];

                foreach($data as $period_index => $row){
                    if(!isset($row['sales']))
                        continue;
                    foreach($row['sales'] as $gl_account => $sale){
                        $data[$period_index]['sales'][$gl_account]['branch'] = $api['branch_number'];
                    }
                }

                if(empty($branch_sales)){
                    $branch_sales = $data;
                }
                else{
                    foreach($data as $period_index => $sales){
                        if(!isset($sales['sales']))
                            continue;

                        if(!isset($branch_sales[$period_index]))
                            $branch_sales[$period_index] = ['sales'=>[]];

                        foreach($sales['sales'] as $sale){
                            if(!isset($branch_sales[$period_index]['sales'][$sale['gl_account']])){
                                $branch_sales[$period_index]['sales'][$sale['gl_account']] = [
                                    'gl_account' => $sale['gl_account'],
                                    'name'       => $sale['name'],
                                    'branch'     => $api['branch_number'],
                                    'amount'     => 0,
                                    'credit'     => 0,
                                    'cash'       => 0,
                                ];

                            }
                            $branch_sales[$period_index]['sales'][$sale['gl_account']]['amount']   += $sale['amount'];
                            $branch_sales[$period_index]['sales'][$sale['gl_account']]['credit']   += $sale['credit'];
                            $branch_sales[$period_index]['sales'][$sale['gl_account']]['cash']     += $sale['cash'];
                        }
                    }
                }
            }
        }


        $fiscal_year_sales = [];
        foreach($branch_sales as $period => $sales_list){
            if(!isset($sales_list['sales']))
                continue;

            foreach($sales_list['sales'] as $sales){
                if(!isset($fiscal_year_sales[$sales['gl_account']])){
                    $fiscal_year_sales[$sales['gl_account']] = [
                        'gl_account' => $sales['gl_account'],
                        'name'       => $sales['name'],
                        'branch'     => $sales['branch'],
                        'sales'     => [
                            "p0"  => ['amount'=>0,'cash'=>0, 'credit'=>0],
                            "p1"  => ['amount'=>0,'cash'=>0, 'credit'=>0],
                            "p2"  => ['amount'=>0,'cash'=>0, 'credit'=>0],
                            "p3"  => ['amount'=>0,'cash'=>0, 'credit'=>0],
                            "p4"  => ['amount'=>0,'cash'=>0, 'credit'=>0],
                            "p5"  => ['amount'=>0,'cash'=>0, 'credit'=>0],
                            "p6"  => ['amount'=>0,'cash'=>0, 'credit'=>0],
                            "p7"  => ['amount'=>0,'cash'=>0, 'credit'=>0],
                            "p8"  => ['amount'=>0,'cash'=>0, 'credit'=>0],
                            "p9"  => ['amount'=>0,'cash'=>0, 'credit'=>0],
                            "p10" => ['amount'=>0,'cash'=>0, 'credit'=>0],
                            "p11" => ['amount'=>0,'cash'=>0, 'credit'=>0],
                            "p12" => ['amount'=>0,'cash'=>0, 'credit'=>0]
                        ]
                    ];
                }

                $fiscal_year_sales[$sales['gl_account']]['sales'][$period]['amount']   += $sales['amount'];
                $fiscal_year_sales[$sales['gl_account']]['sales'][$period]['cash']     += $sales['cash'];
                $fiscal_year_sales[$sales['gl_account']]['sales'][$period]['credit']   += $sales['credit'];
                $fiscal_year_sales[$sales['gl_account']]['sales'][$period]['from']      = $sales_list['from']??'1990-01-01';
                $fiscal_year_sales[$sales['gl_account']]['sales'][$period]['to']        = $sales_list['to']??'1990-01-01';
            }
        }

        $fiscal_year_sales = BluelimeSystem::sort_array($fiscal_year_sales,['name']);

        $view_data = [
            'fiscal_year_sales' => $fiscal_year_sales,
            'ec_year' => $ec_year,
            'api_connection' => $api_connection,
            'active_menu' => 'reports',
            'active_sub_menu' => 'report_sales_period',
        ];
        return view('Inventory/Finance/report_sales_period',$view_data);
    }

    public function report_sales_date_range(){
        $from = $_GET['from'] ?? date('Y-m-d');
        $to   = $_GET['to'] ?? date('Y-m-d');

        if(strtotime($from) > strtotime($to)){
            $temp = $from;
            $from = $to;
            $to = $temp;
        }

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $finance_maintain = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::CREDIT_INVOICE_API)->findAll();
        $finance_api = $finance_maintain[0]['data'];

        $api_connection = [];
        $branch_sales = [];
        foreach($finance_api as $api){
            $ip_addess = $api['ip_addess'];
            $api_key = $api['api_key'];
            
            $url = "http://$ip_addess/api2/finance/v1/get_date_range_sales_report_data";
            
            $result = BluelimeSystem::communicate_api2(__METHOD__, $url, $api_key,['from'=>$from,'to'=>$to]);

            $result = BluelimeSystem::convert_to_array($result);

            $api_connection[$api['branch_name']] = ['branch'=>$api['branch_name'], 'status'=>0, 'branch_number'=>$api['branch_number']];

            if(!empty($result))
                $api_connection[$api['branch_name']]['status'] = 1;
            
            $data = $result['data'] ?? [];
            
            if(BluelimeSystem::check_jwt_signature($data, $api_key, ($result['signature']??''))){
                if($result['status'] == 'success')
                    $api_connection[$api['branch_name']]['status'] = 2;

                foreach($data as $period_index => $row){
                    if(!isset($row['sales']))
                        continue;
                    foreach($row['sales'] as $gl_account => $sale){
                        $data[$period_index]['sales'][$gl_account]['branch'] = $api['branch_number'];
                    }
                }

                if(empty($branch_sales)){
                    $branch_sales = $data;
                }
                else{
                    foreach($data as $period_index => $sales){
                        if(!isset($sales['sales']))
                            continue;

                        if(!isset($branch_sales[$period_index]))
                            $branch_sales[$period_index] = ['sales'=>[]];

                        foreach($sales['sales'] as $sale){
                            if(!isset($branch_sales[$period_index]['sales'][$sale['gl_account']])){
                                $branch_sales[$period_index]['sales'][$sale['gl_account']] = [
                                    'gl_account' => $sale['gl_account'],
                                    'name'       => $sale['name'],
                                    'branch'     => $api['branch_number'],
                                    'amount'     => 0,
                                    'credit'     => 0,
                                    'cash'       => 0,
                                ];

                            }
                            $branch_sales[$period_index]['sales'][$sale['gl_account']]['amount']   += $sale['amount'];
                            $branch_sales[$period_index]['sales'][$sale['gl_account']]['credit']   += $sale['credit'];
                            $branch_sales[$period_index]['sales'][$sale['gl_account']]['cash']     += $sale['cash'];
                        }
                    }
                }
            }
        }

        $sales_by_gl_account = [];
        foreach($branch_sales as $period => $sales_list){
            if(!isset($sales_list['sales']))
                continue;

            foreach($sales_list['sales'] as $sales){
                if(!isset($sales_by_gl_account[$sales['gl_account']])){
                    $sales_by_gl_account[$sales['gl_account']] = [
                        'gl_account' => $sales['gl_account'],
                        'name'       => $sales['name'],
                        'branch'     => $sales['branch'],
                        'amount'     => 0,
                        'credit'     => 0,
                        'cash'       => 0,
                    ];
                }

                $sales_by_gl_account[$sales['gl_account']]['amount']   += $sales['amount'];
                $sales_by_gl_account[$sales['gl_account']]['credit']   += $sales['credit'];
                $sales_by_gl_account[$sales['gl_account']]['cash']     += $sales['cash'];
            }
        }

        $sales_by_gl_account = BluelimeSystem::sort_array($sales_by_gl_account,['name']);

        $view_data = [
            'sales_by_gl_account' => $sales_by_gl_account,
            'from' => $from,
            'to' => $to,
            'api_connection' => $api_connection,
            'active_menu' => 'reports',
            'active_sub_menu' => 'report_sales_date_range',
        ];
        return view('Inventory/Finance/report_sales_date_range',$view_data);
    }

    public function get_sales_detail(){
        $branch = $_POST['branch'];
        $gl_account = $_POST['gl_account'];
        $from = $_POST['from'];
        $to = $_POST['to'];
        
        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $finance_maintain = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::CREDIT_INVOICE_API)->findAll();
        $finance_api = $finance_maintain[0]['data'];
        $ip_addess = '';
        $api_key = '';
        foreach($finance_api as $api){
            if($api['branch_number'] == $branch){
                $ip_addess = $api['ip_addess'];
                $api_key = $api['api_key'];
                break;
            }
        }
        $url = "http://$ip_addess/api2/finance/v1/get_sales_detail_report_data";
        $result = BluelimeSystem::communicate_api2(__METHOD__, $url, $api_key,['from_date'=>$from,'to_date'=>$to,'gl_account'=>$gl_account]);
        echo $result;
        die();
        $result = BluelimeSystem::convert_to_array($result);
        $sales_detail = ['null'];
        if(BluelimeSystem::check_jwt_signature($result['data']??[], $api_key, ($result['signature']??''))){
            if($result['status'] == 'success')
                $sales_detail = $result['data'];
        }
        return $this->response->setJSON(['status'=>'success','sales_detail'=>$sales_detail]);
    }

    public function compounding_sales_detail(){
        $from = $_GET['from'] ?? date('Y-m-d');
        $to   = $_GET['to'] ?? date('Y-m-d');

        if(strtotime($from) > strtotime($to)){
            $temp = $from;
            $from = $to;
            $to = $temp;
        }

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $finance_maintain = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::CREDIT_INVOICE_API)->findAll();
        $finance_api = $finance_maintain[0]['data'];

        $api_connection = [];
        $sales_detail = [];
        foreach($finance_api as $api){
            // if($api['branch_number'] != 4)
            //     continue;
            $ip_addess = $api['ip_addess'];
            $api_key = $api['api_key'];
            
            $url = "http://$ip_addess/api2/finance/v1/get_compounding_sales_detail";
            
            $result = BluelimeSystem::communicate_api2(__METHOD__, $url, $api_key,['from_date'=>$from,'to_date'=>$to]);

            $result = BluelimeSystem::convert_to_array($result);

            $api_connection[$api['branch_name']] = ['branch'=>$api['branch_name'], 'status'=>0, 'branch_number'=>$api['branch_number']];

            if(!empty($result))
                $api_connection[$api['branch_name']]['status'] = 1;
            
            $data = $result['data'] ?? [];
            
            if(BluelimeSystem::check_jwt_signature($data, $api_key, ($result['signature']??''))){
                if($result['status'] == 'success'){
                    $api_connection[$api['branch_name']]['status'] = 2;
                    $sales_detail =[...$sales_detail, ...$data];
                }
            }
        }

        //$transactions = BluelimeSystem::sort_array($transactions, ['peachtree_id','created_at','id']);

        $view_data = [
            'from' => $from,
            'to' => $to,
            'api_connection' => $api_connection,
            'sales_detail' => $sales_detail,
            'active_menu' => 'reports',
            'active_sub_menu' => 'report_compounding_sales_detail',
        ];

        //BluelimeSystem::export_arra_in_csv($transactions,'compounding_sales_detail_'.$from.'_to_'.$to.'.csv');
        return view('Inventory/Finance/report_compounding_sales_detail',$view_data);
    }

    public function compounding_sales_detail_export_in_csv(){
        $from = $_GET['from'] ?? date('Y-m-d');
        $to   = $_GET['to'] ?? date('Y-m-d');

        if(strtotime($from) > strtotime($to)){
            $temp = $from;
            $from = $to;
            $to = $temp;
        }

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $finance_maintain = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::CREDIT_INVOICE_API)->findAll();
        $finance_api = $finance_maintain[0]['data'];

        $POST_VAL = ['from_date'=>$from,'to_date'=>$to, 'compress_data'=>0];
        $api_connection = [];
        $sales_detail = [];
        foreach($finance_api as $api){
            // if($api['branch_number'] != 4)
            //     continue;
            $ip_addess = $api['ip_addess'];
            $api_key = $api['api_key'];
            
            $url = "http://$ip_addess/api2/finance/v1/get_compounding_sales_detail";
            
            $result = BluelimeSystem::communicate_api2(__METHOD__, $url, $api_key,$POST_VAL);

            $result = BluelimeSystem::convert_to_array($result);

            $api_connection[$api['branch_name']] = ['branch'=>$api['branch_name'], 'status'=>0, 'branch_number'=>$api['branch_number']];

            if(!empty($result))
                $api_connection[$api['branch_name']]['status'] = 1;
            
            $data = $result['data'] ?? [];
            
            if(BluelimeSystem::check_jwt_signature($data, $api_key, ($result['signature']??''))){
                if($result['status'] == 'success'){
                    $api_connection[$api['branch_name']]['status'] = 2;
                    $sales_detail =[...$sales_detail, ...$data];
                }
            }
        }
        BluelimeSystem::export_arra_in_csv($sales_detail,'compounding_sales_detail_'.$from.'_to_'.$to.'.csv');
    }

    public function all_item_transaction(){
        $from = $_GET['from'] ?? date('Y-m-d');
        $to   = $_GET['to'] ?? date('Y-m-d');

        if(strtotime($from) > strtotime($to)){
            $temp = $from;
            $from = $to;
            $to = $temp;
        }

        $financeMaintainModel = model('Inventory/FinanceMaintainModel');
        $finance_maintain = $financeMaintainModel->where('type', BluelimeConfiguration::FINANCE_MAINTAIN_TYPE::CREDIT_INVOICE_API)->findAll();
        $finance_api = $finance_maintain[0]['data'];

        $api_connection = [];
        $transactions = [];
        foreach($finance_api as $api){
            $ip_addess = $api['ip_addess'];
            $api_key = $api['api_key'];
            
            $url = "http://$ip_addess/api2/finance/v1/get_all_item_transaction";
            
            $result = BluelimeSystem::communicate_api2(__METHOD__, $url, $api_key,['from_date'=>$from,'to_date'=>$to]);

            $result = BluelimeSystem::convert_to_array($result);

            $api_connection[$api['branch_name']] = ['branch'=>$api['branch_name'], 'status'=>0, 'branch_number'=>$api['branch_number']];

            if(!empty($result))
                $api_connection[$api['branch_name']]['status'] = 1;
            
            $data = $result['data'] ?? [];
            
            if(BluelimeSystem::check_jwt_signature($data, $api_key, ($result['signature']??''))){
                if($result['status'] == 'success'){
                    $api_connection[$api['branch_name']]['status'] = 2;
                    $transactions =[...$transactions, ...$data];
                }
            }
        }

        $transactions = BluelimeSystem::sort_array($transactions, ['peachtree_id','created_at','id']);


        // BluelimeSystem::print_associated_array($api_connection,false);
        // BluelimeSystem::print_associated_array($transactions);

        $view_data = [
            'from' => $from,
            'to' => $to,
            'api_connection' => $api_connection,
            'transactions' => $transactions,
            'active_menu' => 'reports',
            'active_sub_menu' => 'report_all_item_transaction',
        ];
        return view('Inventory/Finance/report_all_item_transaction',$view_data);
    }


    // private function get_clinic_sales_by_period($from, $to, $period_list){
    //     $serviceModel = model('Hospital/ServiceModel');
    //     $services = $serviceModel->findAll();
    //     $services = BluelimeSystem::index_object_list_array_by_unique_field($services,'id');

    //     $billModel = model('Hospital/BillModel');
    //     $billDetailModel = model('Hospital/BillDetailModel');
       
    //     $bills = $billModel->where('payed_date >=', $from)->where('payed_date <=', $to)->where('bill_status', BluelimeConfiguration::BILL_RECIEVED)->findAll();
    //     $bills_id_list = BluelimeSystem::collect_field_value_from_object($bills, 'id');
    //     //sort($bills_id_list);
    //     $bills_id_list = [...($bills_id_list??[])];
    //     $bill_details = $billDetailModel->whereIn('bill_id',$bills_id_list)->where('service_status !=',BluelimeConfiguration::SERVICE_STATUS_DELAY)->findAll();

    //     $bills = BluelimeSystem::index_object_list_array_by_unique_field($bills, 'id');

    //     $sales_by_period = $period_list;
    //     foreach($bill_details as $row_index => $service_sales){
    //         $gl_account = $services['id_'.$service_sales['service_id']]['gl_account']??'ERR-NO_GL';
    //         $paid_date = $bills['id_'.$service_sales['bill_id']]['payed_date'];
    //         $period = $this->get_period($paid_date, $period_list);
    //         if(!isset($sales_by_period[$period]['sales']))
    //             $sales_by_period[$period]['sales'] = [];
    //         if(!isset($sales_by_period[$period]['sales'][$gl_account]))
    //             $sales_by_period[$period]['sales'][$gl_account] = ['gl_account'=>$gl_account, 'name'=>$service_sales['service_name'], 'amount'=>0, 'quantity'=>0];
            
    //         $sales_by_period[$period]['sales'][$gl_account]['amount'] += $service_sales['bill_quantity'] * $service_sales['bill_amount'];
    //         $sales_by_period[$period]['sales'][$gl_account]['quantity'] += $service_sales['bill_quantity'];
           
    //     }
    //     return $sales_by_period;
    // }

    // private function get_period($date, $period_list){
    //     foreach($period_list as $period => $date_range){
    //         if((strcmp($date, $date_range[0])>=0) && (strcmp($date,$date_range[1])<=0))
    //             return $period;
    //     }
    //     return 0;
    // }

    // private function prepare_list_for_query($id_list){
    //     $bill_id_range = [];
    //     $prv = 0;
    //     $last_hold_id = null;
    //     $single = [];
    //     $multiple = [];
    //     foreach($id_list as $index => $id){
    //         if($index == 0){
    //             $prv = $id;
    //             continue;
    //         }

    //         if($id - $prv == 1){
    //             if($last_hold_id==null)
    //                 $last_hold_id = $id - 1; //prv
    //             $prv = $id;
    //             continue;
    //         }

    //         if($prv !== 0){
    //             if($last_hold_id === null)
    //                 $single[] = $prv;
    //                 //$bill_id_range[] = ['type' => 'single', 'from'=>$prv, 'to'=>$prv];
    //             else{
    //                 $multiple[] = ['from'=>$last_hold_id, 'to'=>$prv];
    //                 //$bill_id_range[] = ['type' => 'multiple', 'from'=>$last_hold_id, 'to'=>$prv];
    //                 $last_hold_id = null;
    //             }
    //         }
            
    //         $prv = $id;
    //     }

    //     //BluelimeSystem::print_associated_array($bill_id_range);

    // }


}

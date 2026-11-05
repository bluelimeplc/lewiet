<?php

namespace App\Controllers\Inventory;

use BluelimeAuthentication;
use BluelimeSystem;

class Finance extends \App\Controllers\BaseController
{
    public function index()
    {
        $data['active_menu'] = 'pendingrequest';

        return  view('Include/_header',$data)
                .view('Include/_left_sidebar_top')
                .view('Inventory/Finance/_left_sidebar_finance')
                .view('Include/_left_sidebar_bottom')
                .view('Inventory/Finance/pending_store_request')
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
        $data['company_id'] = $company_id;
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['paymentRequests'] = $paymentRequests;
        $data['alert'] = $alert;
        
    
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Inventory/Finance/_left_sidebar_finance')

            .view('Include/_left_sidebar_bottom')

            .view('Inventory/Finance/payment_request')

            .view('Include/_footer');
    }

    public function newcreditpaymentrequest()
    {

        $companyModel = model('Hospital/CompanyModel');
        $companies = $companyModel->findAll();
        $company_id = $_POST['company_id'] ?? "";
        $date_from = $_POST['date_from'] ?? "";
        $date_to = $_POST['date_to'] ?? "";
        
        $bills = [];
        //If all filter value are not empty fetch bills
        if($company_id != ''){
            $billModel = model('Hospital/BillModel');
            $billModel = $billModel->where('company_id',$company_id);
            if(($date_from != '' )){
                $billModel = $billModel->where('billed_date >=', $date_from . " 00:00:00");
            }
            if(($date_to != '' )){
                $billModel = $billModel->where('billed_date <=', $date_to . " 23:59:59");
            }
            $bills = $billModel->where('bill_status <>', \BluelimeConfiguration::BILL_PENDING)->where('bill_status <>', \BluelimeConfiguration::BILL_REMOVED)->groupStart()->where('payment_request_id',0)->orWhere('payment_request_id')->groupEnd()->findAllWithAmount();
        }

       
        $alert = BluelimeSystem::get_alert();

        $data['active_menu'] = 'creditpayment';
        $data['companies'] = $companies;
        $data['company_id'] = $company_id;
        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['bills'] = $bills;
        $data['alert'] = $alert;
        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Inventory/Finance/_left_sidebar_finance')

            .view('Include/_left_sidebar_bottom')

            .view('Inventory/Finance/new_credit_payment_request')
            
            .view('Include/_footer');
    }

    public function createcreditpaymentrequest()
    {
        $paymentRequestModel = model('Hospital/PaymentRequestModel');
        $billModel = model('Hospital/BillModel');

        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();

        $bill_id_list = $_POST['selected_bills'] ?? [];
        $company_id = $_POST['company_id'] ?? '';
        $company_name = $_POST['company_name'] ?? '';
        $total_amount = $_POST['total_amount'] ?? 0;

        $registered_by = $loggeduserdata['id'];
        $registered_by_name = $loggeduserdata['name'];

        $data['company_id'] = $company_id;
        $data['company_name'] = $company_name;
        $data['total_amount'] = $total_amount;
        $data['bill_id_list'] = BluelimeSystem::convert_array_to_string($bill_id_list);
        $data['registered_by'] = $registered_by;
        $data['registered_by_name'] = $registered_by_name;
        $data['registered_date'] = date("Y-m-d");
        $data['active_menu'] = 'creditpayment';

        if(empty($bill_id_list)){
            $alert_body_html = "There was no bill selected.<br>There is no update made on the database.";
            BluelimeSystem::set_alert($alert_body_html,"OK", "WARNING");
            return redirect()->to(base_url()."hospital/finance/newcreditpaymentrequest?fail=1");
        }
        
        $paymentRequestModel->insert($data);
        $id = $paymentRequestModel->getInsertID();

        if($id == 0){
            $alert_body_html = "There was an error creating Credit Payment Request.";
            BluelimeSystem::set_alert($alert_body_html,"OK", "WARNING");
            return redirect()->to(base_url()."hospital/finance/newcreditpaymentrequest?fail=2");
        }

        $data = ["payment_request_id" => $id];
        $billModel->update($bill_id_list, $data);
        return redirect()->to(base_url()."hospital/finance/creditpayment");
    }

    public function deletepaymentrequest()
    {
        $id = $_GET['id'] ?? 0;
        if($id == 0){
            $alert_body_html = "Payment Request is not deleted.<br>ID is not set.";
            BluelimeSystem::set_alert($alert_body_html);
            return redirect()->to(base_url()."hospital/finance/creditpayment?fail=1");
        }
        
        $paymentRequestModel = model('Hospital/PaymentRequestModel');
        $billModel = model('Hospital/BillModel');
        
        $paymentRequest = $paymentRequestModel->find($id);

        if(empty($paymentRequest)){
            $alert_body_html = "Payment Request is not deleted.<br>ID $id cannot be found.";
            BluelimeSystem::set_alert($alert_body_html,"OK","WARNING");
            return redirect()->to(base_url()."hospital/finance/creditpayment?fail=2");
        }

        $bill_id_list = BluelimeSystem::convert_to_array($paymentRequest['bill_id_list']);

        if(!empty($bill_id_list)){
            $update_data = ['payment_request_id' => NULL];
            $updated = $billModel->update($bill_id_list, $update_data);
            
            if(!$updated){
                $alert_body_html = "Payment Request is not deleted.<br>Cannot update/remove Payment Request ID from Bills.";
                BluelimeSystem::set_alert($alert_body_html);
                return redirect()->to(base_url()."hospital/finance/creditpayment?fail=3");
            }
        }
        else{
            $alert_body_html = "Payment Request is not deleted.<br>It has invalid bill id list.";
            BluelimeSystem::set_alert($alert_body_html);
            return redirect()->to(base_url()."hospital/finance/creditpayment?fail=4");
        }

        $paymentRequestModel->delete($id);

        return redirect()->to(base_url()."hospital/finance/creditpayment");
    }

    public function viewcreditpaymentrequest()
    {
        $paymentRequestModel = model('Hospital/PaymentRequestModel');
        $billModel = model('Hospital/BillModel');
        $viewBillAndPaymentModel = model('Hospital/ViewBillAndPaymentModel');
        $visitModel = model('Hospital/VisitModel');
        
        $data['active_menu'] = 'creditpayment';
        //$data['bill_info'] = $billModel->searchpendingbills($searchkey);
        
        $payment_request_id = $_GET['id'] ?? 0;

        $paymentRequest = $paymentRequestModel->find($payment_request_id);
        
        $bill_id_list = BluelimeSystem::convert_to_array($paymentRequest['bill_id_list']);
        if(empty($bill_id_list))
            $bill_id_list = [0];
        
        $bills = $billModel->select('id, visit_id, CONCAT(patient_name, patient_id) AS sorter, patient_id, patient_name, billed_date')->whereIn('id',$bill_id_list)->where('bill_status <>',0)->orderBy('sorter', 'ASC')->orderBy('billed_date', 'ASC')->findAll();//->findAllWithDetail_GroupedByType();
        
        $flat_data = [];

        foreach($bills as $bill){
            $visit = $visitModel->where('id', $bill['visit_id'])->findAll();
            $reference = $visit[0]['extra']['reference'] ?? '';
            $patient_id = $bill['patient_id'];
            $patient_name = $bill['patient_name'];
            $date = $bill['billed_date'];
            $details = $viewBillAndPaymentModel->where('bill_id', $bill['id'])->where('type', 'BILL')->findAll();
            
            foreach($details as $detail){
                $flat_data_temp = [
                    'patient_id' => $patient_id,
                    'patient_name' => $patient_name,
                    'reference' => $reference,
                    'date' => $date,
                    'service_type' => $detail['service_type'],
                    'price' => $detail['amount'],
                ];

                array_push($flat_data,$flat_data_temp);
            }
        }

        $grouped_bills = [];
        $grouped_bills_temp = ['total' => 0, 'detail' => []];
        $previous_patient_id = -1;
        foreach($flat_data as $row){
            if($previous_patient_id == -1)
                $previous_patient_id = $row['patient_id'];
            
            if($previous_patient_id != $row['patient_id']){
                array_push($grouped_bills, $grouped_bills_temp);
                $grouped_bills_temp = ['total' => 0, 'detail' => []];
            }

            $grouped_bills_temp['patient_id'] = $row['patient_id'];
            $grouped_bills_temp['patient_name'] = $row['patient_name'];
            $grouped_bills_temp['total'] += $row['price'];

            $detail = [
                'date' => $row['date'],
                'service_type' => $row['service_type'],
                'reference' => $row['reference'],
                'price' => $row['price']
            ];
            array_push($grouped_bills_temp['detail'], $detail);

            $previous_patient_id = $row['patient_id'];
        }

        array_push($grouped_bills, $grouped_bills_temp);

/*
        foreach($bills as $index => $row){
            $details = $viewBillAndPaymentModel->where('bill_id', $row['id'])->where('type', 'BILL')->findAll();
            $bills[$index]['details'] = $details;

            $billTotalPrice = 0;
            foreach($details as $detail)
                $billTotalPrice += $detail['amount'];

            $bills[$index]['total'] = $billTotalPrice;

            $bills[$index]['reference'] = '';
            $visit = $visitModel->where('id', $row['visit_id'])->findAll();
            if(!empty($visit))
                $bills[$index]['reference'] = $visit[0]['extra']['reference'] ?? '';
        }

        $grouped_bills = [];
        $grouped_bills_temp = [];
        $previous_patient_id = -1;
        $bill_group_detail = [];


        foreach($bills as $bill){
            $patient_name = $bill['patient_name'] ?? '';
            $detail = $bill['details'] ?? [];
            $reference = $bill['reference'] ?? '';
            $date = $bill['billed_date'];

            if($previous_patient_id == -1)
                $grouped_bills_temp = ['patient_name' => $patient_name, 'total' => 0, 'group_detail' => []];
            elseif($bill['patient_id'] != $previous_patient_id){
                $grouped_bills_temp['group_detail'] = $bill_group_detail;
                array_push($grouped_bills, $grouped_bills_temp);
                
                $grouped_bills_temp = ['patient_name' => $patient_name, 'total' => 0, 'group_detail' => []];
            }

            array_push($bill_group_detail, ['reference' => $reference, 'detail' => $detail, 'date' => $date]);

            $total = 0;
            foreach($detail as $bill_detail)
                $total += $bill_detail['amount'];
            
            $grouped_bills_temp['total'] += $total;

            $previous_patient_id = $bill['patient_id'];
        }

        $grouped_bills_temp['group_detail'] = $bill_group_detail;
        array_push($grouped_bills, $grouped_bills_temp);

        */

        
        /*$grouped_bills = [];
        $grouped_bills_temp = [];
        $previous_patient_id = -1;
        
        foreach($bills as $bill){
            if(($bill['patient_id'] != $previous_patient_id) && ($previous_patient_id != -1)){
                array_push($grouped_bills, $grouped_bills_temp);
                $grouped_bills_temp = [];
                array_push($grouped_bills_temp, $bill);
            }
            
            array_push($grouped_bills_temp, $bill);
            
            $previous_patient_id = $bill['patient_id'];
        }

        array_push($grouped_bills, $grouped_bills_temp);

        

        $data['grouped_bills'] = $grouped_bills;
        $data['bills'] = $bills;
        */

        $data['grouped_bills'] = $grouped_bills;
        $data['paymentRequest'] = $paymentRequest;
        $data['active_menu'] = 'creditpayment';

        
        return 
            view('Include/_header')
            .view('Include/_left_sidebar_top', $data)

            .view('Inventory/Finance/_left_sidebar_finance')

            .view('Include/_left_sidebar_bottom')

            .view('Inventory/Finance/payment_request_view')

            .view('Include/_footer');
            
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

        return redirect()->to(base_url()."hospital/finance/viewcreditpaymentrequest?id=$id$fail");
        
    }

    public function pending_purchase_request(): string
    {
        $purchaseMainModel = model('Inventory/PurchaseMainModel');
        
        
        $purchase_main = $purchaseMainModel->orderBy('id','DESC')->findAll(20,0);

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

        $purchase_main = $purchaseMainModel->find($purchase_id);
        $purchase_detail = $purchaseDetailModel->where('purchase_id', $purchase_id)->findAll();

        

        $supplier = $supplierModel->find($purchase_main['supplier_id']);
        $purchase_main['supplier_detail'] = $supplier;

        $data = [
            'purchase_main' => $purchase_main,
            'purchase_detail' => $purchase_detail,
        ];
        $data['active_menu'] = 'pendingpurchase';
        
        return view('Inventory/Finance/purchase_print', $data);
    }
}

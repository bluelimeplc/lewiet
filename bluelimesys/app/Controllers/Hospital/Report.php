<?php
namespace App\Controllers\Hospital;

use BluelimeSystem;
use BluelimeConfiguration;
use BluelimePOS;

class Report extends \App\Controllers\BaseController
{
    public function index()
    { 
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
        $to_date = ($_GET['end_date'] ?? date('Y-m-d')). ' 23:59:59';
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }
        
        
        
        $visitModel = model('Hospital/VisitModel'); 
        $patientModel = model('Hospital/PatientModel'); 
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $role = $loggeduserdata['role'];

        
        
        
        $number_of_visits = $visitModel->where('visit_date >=', $from_date)->where('visit_date <=', $to_date)->findColumn('count(id)');
        $free_visits = $visitModel->where('visit_date >=', $from_date)->where('visit_date <=', $to_date)->where('is_free >=', 1)->findAll();
        $number_of_new_patients = $patientModel->where('registered_date >=', $from_date)->where('registered_date <=', $to_date)->findColumn('count(id)');
        
        
        //Grouped clinic

        $payment_method = $_GET['payment_method'] ?? 'Cash';

        $billModel = model('Hospital/BillModel');

        $bills = $billModel->where('payed_date >=', $from_date)->where('payed_date <=', $to_date)->where('bill_status',BluelimeConfiguration::BILL_RECIEVED)->findAll();

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


    //BluelimeSystem::show_and_die($bill_report_data);
        
        //Clinic

        $pOSBillModelc = model('POS/POSBillModel');
        $typec = 'Hospital';
        $payment_method_reflectionc = new \ReflectionClass(BluelimeConfiguration::PAYMENT_METHOD);
        $payment_methodsc = $payment_method_reflectionc->getConstants();
        
        $payment_reportc = [];
        foreach($payment_methodsc as $payment_methodc){
            $payment_reportc[$payment_methodc] = ['qty' => 0, 'amount' => 0];
        }

        $pOSBillModelc = $pOSBillModelc->where('type', $typec)->where('response_date >=', $from_date)->where('response_date <=', $to_date);

        

        $pos_billsc = $pOSBillModelc->findAll();

        $total_countc = 0;
        $total_amountc = 0;
        $cash_partialc = 0;
        foreach($pos_billsc as $pos_billc){
            $payment_methodc = $pos_billc['payment_method'];
            if(!isset($payment_reportc[$payment_methodc]))
                $payment_reportc[$payment_methodc] = ['qty' => 0, 'amount' => 0];
            
            $totalc = $this->calculate_total($pos_billc);
            $payment_reportc[$payment_methodc]['qty']++;
            $transferred_amountc = floatval($pos_billc['transferred_amount']);
            if($transferred_amountc > 0){
                $cash_partialc += $totalc - floatval($pos_billc['transferred_amount']);
                $totalc = $transferred_amountc;
            }
            $payment_reportc[$payment_methodc]['amount'] += $totalc;
            $total_amountc += $totalc;
            $total_countc++;
        }
        $payment_reportc['Cash']['amount'] += $cash_partialc;
        $total_amountc += $cash_partialc;

        $payment_reportc['TOTAL'] = ['qty' => $total_countc, 'amount' => $total_amountc];

        $payment_report_tempc = [];
        $cashless_totalc = 0;
        $cashless_countc = 0;
        foreach($payment_reportc as $indexc => $linec){
            if(($indexc == BluelimeConfiguration::PAYMENT_METHOD::CASH) || ($indexc == 'TOTAL') || ($indexc == BluelimeConfiguration::PAYMENT_METHOD::CREDIT))
                continue;
            $payment_report_tempc[$indexc] = $linec;
            $cashless_countc += $linec['qty'];
            $cashless_totalc += $linec['amount'];
        }

        $payment_report_tempc['BANK TOTAL'] = ['qty' => $cashless_countc, 'amount' => $cashless_totalc];

        $payment_report_tempc['Credit'] = $payment_reportc['Credit'];
        $payment_report_tempc['Cash'] = $payment_reportc['Cash'];
        $payment_report_tempc['TOTAL'] = $payment_reportc['TOTAL'];

        $payment_reportc = $payment_report_tempc;

        
        
        

        //Pharmacy

        
        $pOSBillModel = model('POS/POSBillModel');
        $type = 'Inventory';
        $payment_method_reflection = new \ReflectionClass(BluelimeConfiguration::PAYMENT_METHOD);
        $payment_methods = $payment_method_reflection->getConstants();
        
        $payment_report = [];
        foreach($payment_methods as $payment_method){
            $payment_report[$payment_method] = ['qty' => 0, 'amount' => 0];
        }

        $pOSBillModel = $pOSBillModel->where('type', $type)->where('response_date >=', $from_date)->where('response_date <=', $to_date);

        

        $pos_bills = $pOSBillModel->findAll();

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

        
        $data = [
            'payment_report' => $payment_report
        ];

        //grouped pharmacy sales

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
    


        $data['salse_report_data'] = $salse_report_data;
        $data['payment_reportc'] = $payment_reportc;
        $data['bill_report_data'] = $bill_report_data;

        
        $data['number_of_visits'] =intval($number_of_visits[0]);
        $data['number_of_new_patients'] = intval($number_of_new_patients[0]);
        $data['free_visits'] = $free_visits;
        $data['active_menu'] = 'reports';
        
        $data['from_date'] = $from_date; 
        $data['to_date'] = $to_date; 



        
        if($role==="SUPER_ADMIN"){
            $role = "ADMIN";
        }
      
       return 
            view('Include/_header', $data)

            .view('Include/_left_sidebar_top')

            .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Report/view_periodic_sales_reports')

            .view('Include/_footer');
            
            
    }

    public function audittrailreport()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $role = $loggeduserdata['role'];
        if ($role !== 'SUPER_ADMIN' && $role !== 'ADMIN' && $role !== 'MIS') {
            return redirect()->to(site_url('/?permission=0'));
        }

        $fromDate = $_GET['from_date'] ?? date('Y-m-d');
        $toDate = $_GET['to_date'] ?? date('Y-m-d');
        $tableName = $_GET['table_name'] ?? '';
        $modelName = $_GET['model'] ?? '';
        $userName = $_GET['user_name'] ?? '';
        $actionName = $_GET['action'] ?? '';

        $builder = db_connect()->table('sys_audit_trail');
        if ($fromDate !== '') {
            $builder->where('created_at >=', $fromDate . ' 00:00:00');
        }
        if ($toDate !== '') {
            $builder->where('created_at <=', $toDate . ' 23:59:59');
        }
        if ($tableName !== '') {
            $builder->where('table_name', $tableName);
        }
        if ($modelName !== '') {
            $builder->where('model', $modelName);
        }
        if ($userName !== '') {
            $builder->where('user_name', $userName);
        }
        if ($actionName !== '') {
            $builder->where('action', $actionName);
        }

        try {
            $rows = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
        } catch (\Throwable $e) {
            $rows = [];
        }

        foreach ($rows as &$row) {
            $row['difference'] = $this->buildTrailDifference($row);
        }
        unset($row);

        try {
            $tableNames = db_connect()->table('sys_audit_trail')->select('table_name')->distinct()->orderBy('table_name', 'ASC')->get()->getResultArray();
            $modelNames = db_connect()->table('sys_audit_trail')->select('model')->distinct()->orderBy('model', 'ASC')->get()->getResultArray();
            $users = db_connect()->table('sys_audit_trail')->select('user_name')->distinct()->orderBy('user_name', 'ASC')->get()->getResultArray();
            $actions = db_connect()->table('sys_audit_trail')->select('action')->distinct()->orderBy('action', 'ASC')->get()->getResultArray();
        } catch (\Throwable $e) {
            $tableNames = [];
            $modelNames = [];
            $users = [];
            $actions = [];
        }

        $data = [
            'filters' => [
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'table_name' => $tableName,
                'model' => $modelName,
                'user_name' => $userName,
                'action' => $actionName,
            ],
            'rows' => $rows,
            'table_names' => array_values(array_filter(array_map(static function ($item) { return $item['table_name'] ?? null; }, $tableNames))),
            'models' => array_values(array_filter(array_map(static function ($item) { return $item['model'] ?? null; }, $modelNames))),
            'users' => array_values(array_filter(array_map(static function ($item) { return $item['user_name'] ?? null; }, $users))),
            'actions' => array_values(array_filter(array_map(static function ($item) { return $item['action'] ?? null; }, $actions))),
            'active_menu' => 'reports',
            'active_sub_menu' => 'audit_trail_report',
        ];

        if ($role === 'SUPER_ADMIN') {
            $role = 'ADMIN';
        }

        return view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Hospital/' . ucfirst(strtolower($role)) . '/_left_sidebar_' . strtolower($role))
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Report/audit_trail_report')
            .view('Include/_footer');
    }

    public function exportaudittrailreport()
    {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $role = $loggeduserdata['role'];
        if ($role !== 'SUPER_ADMIN' && $role !== 'ADMIN' && $role !== 'MIS') {
            return redirect()->to(site_url('/?permission=0'));
        }

        $fromDate = $_GET['from_date'] ?? date('Y-m-d');
        $toDate = $_GET['to_date'] ?? date('Y-m-d');
        $tableName = $_GET['table_name'] ?? '';
        $modelName = $_GET['model'] ?? '';
        $userName = $_GET['user_name'] ?? '';
        $actionName = $_GET['action'] ?? '';

        $builder = db_connect()->table('sys_audit_trail');
        if ($fromDate !== '') {
            $builder->where('created_at >=', $fromDate . ' 00:00:00');
        }
        if ($toDate !== '') {
            $builder->where('created_at <=', $toDate . ' 23:59:59');
        }
        if ($tableName !== '') {
            $builder->where('table_name', $tableName);
        }
        if ($modelName !== '') {
            $builder->where('model', $modelName);
        }
        if ($userName !== '') {
            $builder->where('user_name', $userName);
        }
        if ($actionName !== '') {
            $builder->where('action', $actionName);
        }

        try {
            $rows = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
        } catch (\Throwable $e) {
            $rows = [];
        }

        $csvData = [];
        foreach ($rows as $row) {
            $diff = $this->buildTrailDifference($row);
            $diffText = [];
            foreach ($diff as $change) {
                $diffText[] = $change['field'] . ' (' . $change['action'] . '): ' . $change['old_value'] . ' => ' . $change['new_value'];
            }

            $csvData[] = [
                'created_at' => $row['created_at'] ?? '',
                'action' => $row['action'] ?? '',
                'table_name' => $row['table_name'] ?? '',
                'model' => $row['model'] ?? '',
                'user_name' => $row['user_name'] ?? '',
                'row_id' => $row['row_id'] ?? '',
                'difference' => implode(' | ', $diffText),
            ];
        }

        $fileName = 'audit_trail_report_' . date('Ymd_His') . '.csv';
        BluelimeSystem::export_arra_in_csv($csvData, $fileName);
    }

    private function buildTrailDifference(array $row): array
    {
        $oldData = json_decode($row['old_data'] ?? '[]', true);
        $newData = json_decode($row['new_data'] ?? '[]', true);

        if (!is_array($oldData)) {
            $oldData = [];
        }
        if (!is_array($newData)) {
            $newData = [];
        }

        $allKeys = array_unique(array_merge(array_keys($oldData), array_keys($newData)));
        $changes = [];

        foreach ($allKeys as $field) {
            $oldValue = $oldData[$field] ?? null;
            $newValue = $newData[$field] ?? null;

            if ($oldValue === $newValue) {
                continue;
            }

            if ($oldValue === null) {
                $action = 'added';
            } elseif ($newValue === null) {
                $action = 'removed';
            } else {
                $action = 'changed';
            }

            $changes[] = [
                'field' => $field,
                'action' => $action,
                'old_value' => $this->normalizeAuditValue($oldValue),
                'new_value' => $this->normalizeAuditValue($newValue),
            ];
        }

        return $changes;
    }

    private function normalizeAuditValue($value): string
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return '—';
        }

        return (string) $value;
    }

    public function clinic_bill_groupedby_service_name(){
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
        $to_date = ($_GET['end_date'] ?? date('Y-m-d')). ' 23:59:59';
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }
        $payment_method = $_GET['payment_method'] ?? 'Cash';

        $billModel = model('Hospital/BillModel');

        $bills = $billModel->where('payed_date >=', $from_date)->where('payed_date <=', $to_date)->where('bill_status',BluelimeConfiguration::BILL_RECIEVED)->findAll();

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
        if($role==="SUPER_ADMIN"){
            $role = "ADMIN";
        }

        return view('Include/_header', $data)

            .view('Include/_left_sidebar_top')

            .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Report/clinic_bill_groupedby_service_name')

            .view('Include/_footer');
    }
    private function create_cash_sales_report_data($from_date, $to_date): array{
        $salesMainModel = model('Inventory/SalesMainModel');
        $pOSBillModel = model('POS/POSBillModel');
        $peachtreeItemModle = model('Inventory/PeachtreeItemModel');

       
        $saleses = $salesMainModel->where('recieved_date >=', $from_date)->where('recieved_date <=', $to_date)->findAll();
        $sales_details = BluelimeSystem::gather_objects_by_reference($saleses,'id','Inventory/SalesDetailModel','sales_id');
        
        //$pos_bill_id_list = [...BluelimeSystem::collect_field_value_from_object($saleses, 'pos_bill_id'),-1];

        $pos_bills = $pOSBillModel->where('response_date >=', substr($from_date,0,10))->where('response_date <=', substr($to_date,0,10))->where('type','Inventory')->findAll();
        $pos_bills = BluelimeSystem::index_object_list_array_by_unique_field($pos_bills, 'id');
        $saleses = BluelimeSystem::index_object_list_array_by_unique_field($saleses,'id');

        $item_id_list = [...BluelimeSystem::collect_field_value_from_object($sales_details,'item_id'),-99];

       
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
    private function calculate_total($pos_bill):float{
        $total = 0.0;

        foreach($pos_bill['pos_bill_json']['sales_item_list'] as $line)
            $total += $line['quantity'] * ($line['unit_price'] + ($line['vat'] * ($line['unit_price'] * .15)));
        
        return $total;
    }
    public function indexold()
    { 
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }
        
        $billDetailModel = model('Hospital/BillDetailModel'); 
        $billModel = model('Hospital/BillModel'); 
        $billPayementModel = model('Hospital/BillPaymentModel'); 
        $visitModel = model('Hospital/VisitModel'); 
        $patientModel = model('Hospital/PatientModel'); 
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $role = $loggeduserdata['role'];

        $selected_date = (!empty($_POST['report_date'])?$_POST['report_date']:date('Y-m-d')).'%';
        $start_date = (!empty($_POST['start_date'])?$_POST['start_date']:date('Y-m-d')).'%';
        $end_date = (!empty($_POST['end_date'])?$_POST['end_date']:date('Y-m-d')).' 23:59:59';
        
        $consultations = $billDetailModel->where('service_id',1)->findColumn('bill_id')??["0"];
        $procedures = $billDetailModel->where('service_id<>',1)->findColumn('bill_id')??["0"];
        

        $billPayements = $billPayementModel->where('payed_date LIKE', $selected_date)->findAll();
        $total_payment = $billPayementModel->where('payed_date LIKE', $selected_date)->findColumn('sum(payed_amount)');
        foreach($billPayements as $index => $billPayement){
            $visit_id = $billModel->where('id',$billPayement['bill_id'])->findColumn('visit_id');
            $billPayements[$index]['doctor_name'] = $visitModel->where('id',$visit_id)->findColumn('doctor_name');
        }

        
        $number_of_visits = $visitModel->where('visit_date LIKE', $selected_date)->findColumn('count(id)');
        $free_visits = $visitModel->where('visit_date LIKE', $selected_date)->where('is_free >=', 1)->findAll();
        $number_of_new_patients = $patientModel->where('registered_date LIKE', $selected_date)->findColumn('count(id)');
        
        $bill_id_credit_consultation = $billPayementModel->where('payed_date LIKE ',$selected_date)-> where('payment_type', 'Credit')->whereIn('bill_id',$consultations)->findColumn('bill_id')??[0];
        $total_credit_consultation_fee = $billDetailModel->where('service_type','consultation')->whereIn('bill_id',$bill_id_credit_consultation)->findColumn('sum((bill_amount*bill_quantity)-(bill_amount*bill_quantity*bill_discount/100))');
         //BluelimeSystem::show_and_die($total_credit_consultation_fee);
        $bill_id_cash_consultation = $billPayementModel->where('payed_date LIKE ',$selected_date)-> where('payment_type', 'Cash')->whereIn('bill_id',$consultations)->findColumn('bill_id')??[0];
        $total_cash_consultation_fee = $billDetailModel->where('service_type','consultation')->whereIn('bill_id',$bill_id_cash_consultation)->findColumn('sum((bill_amount*bill_quantity)-(bill_amount*bill_quantity*bill_discount/100))');
        
        $total_consultation_fee = $total_cash_consultation_fee[0] + $total_credit_consultation_fee[0];
        
        $bill_id_credit_procedure = $billPayementModel->where('payed_date LIKE ',$selected_date)-> where('payment_type', 'Credit')->whereIn('bill_id',$procedures)->findColumn('bill_id')??[0];
        $total_credit_procedures_payment = $billDetailModel->where('service_type','procedure')->whereIn('bill_id',$bill_id_credit_procedure)->findColumn('sum((bill_amount*bill_quantity)-(bill_amount*bill_quantity*bill_discount/100))');
        
        $bill_id_cash_procedure = $billPayementModel->where('payed_date LIKE ',$selected_date)-> where('payment_type', 'Cash')->whereIn('bill_id',$procedures)->findColumn('bill_id')??[0];
        $total_cash_procedures_payment = $billDetailModel->where('service_type','procedure')->whereIn('bill_id',$bill_id_cash_procedure)->findColumn('sum((bill_amount*bill_quantity)-(bill_amount*bill_quantity*bill_discount/100))');
         
        $total_procedures_payment = $total_cash_procedures_payment[0] + $total_credit_procedures_payment[0];
        
        $total_payment_amount = $total_credit_consultation_fee[0] + $total_cash_consultation_fee[0] + $total_cash_procedures_payment[0] + $total_credit_procedures_payment[0];
        $total_recieved_amount = $total_payment[0];
        $unpaid_payment = $total_payment_amount - $total_recieved_amount;
        
       
             
        $week_cash = $billPayementModel->where('date(payed_date) <= ',str_replace("%", "",$selected_date))->where('payed_date >= ',date('Y-m-d', strtotime("-7 day", strtotime(str_replace("%", "",$selected_date)))))->where('payment_type', 'Cash')->groupBy('date(payed_date)')->findColumn('sum(payed_amount)');
        $week_date = $billPayementModel->where('date(payed_date) <= ',str_replace("%", "",$selected_date))->where('payed_date >= ',date('Y-m-d', strtotime("-7 day", strtotime(str_replace("%", "",$selected_date)))))->where('payment_type', 'Cash')->groupBy('date(payed_date)')->findColumn('date(payed_date)');
        $week_credit = $billPayementModel->where('date(payed_date) <= ',str_replace("%", "",$selected_date))->where('payed_date >= ',date('Y-m-d', strtotime("-7 day", strtotime(str_replace("%", "",$selected_date)))))->where('payment_type', 'Credit')->groupBy('date(payed_date)')->findColumn('sum(payed_amount)');
        //BluelimeSystem::show_and_die($week_date);
        
        if(empty($week_cash))
            $week_cash = [];
        if(empty($week_date))
            $week_date = [];
        if(empty($week_credit))
            $week_credit = [];
        
        $total_consultation_fee_summerized = $billPayementModel->where('payed_date >= ',$start_date)->where('payed_date <= ',$end_date)->whereIn('bill_id',$consultations)->findColumn('sum(payed_amount)');
        $total_procedures_summerized = $billPayementModel->where('payed_date >= ',$start_date)->where('payed_date <= ',$end_date)->whereIn('bill_id',$procedures)->findColumn('sum(payed_amount)');
        //BluelimeSystem::show_and_die($total_consultation_fee_summerized);
        
        $removed_bill_ids = $billModel->where('billed_date >= ',$start_date)->where('billed_date <= ',$end_date)->where('bill_status', \BluelimeConfiguration::BILL_REMOVED)->findColumn('id') ?? [-1];
      
        $total_procedure_order = $billDetailModel->where('bill_date >= ',$start_date)->where('bill_date <= ',$end_date)->where('service_type <>','Consultation')->whereNotIn('bill_id',$removed_bill_ids)->findColumn('sum((bill_amount*bill_quantity)-(bill_amount*bill_quantity*bill_discount/100))');
        //BluelimeSystem::show_and_die($start_date);
        $data['billPayements'] = $billPayements;
        $data['selected_date'] = $selected_date;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['number_of_visits'] =intval($number_of_visits[0]);
        $data['number_of_new_patients'] = intval($number_of_new_patients[0]);
        $data['total_credit_consultation_fee'] = intval($total_credit_consultation_fee[0]);
        $data['total_cash_consultation_fee'] = intval($total_cash_consultation_fee[0]);
        $data['total_consultation_fee'] = $total_consultation_fee;
        $data['total_cash_procedures_payment'] = intval($total_cash_procedures_payment[0]);
        $data['total_credit_procedures_payment'] = intval($total_credit_procedures_payment[0]);
        $data['total_procedures_payment'] = $total_procedures_payment;
        $data['total_payment_amount'] = $total_payment_amount;
        $data['total_recieved_amount'] = $total_recieved_amount;
        $data['total_payment'] = $total_payment[0];
        $data['unpaid_payment'] = $unpaid_payment;
        $data['week_cash'] = $week_cash;
        $data['week_credit'] = $week_credit;
        $data['week_date'] = $week_date;
        $data['total_consultation_fee_summerized'] = $total_consultation_fee_summerized[0];
        $data['total_procedures_summerized'] = $total_procedures_summerized[0];
        $data['total_procedure_order'] = $total_procedure_order[0];
        $data['free_visits'] = $free_visits;
        $data['active_menu'] = 'reports';
        
        if($role==="SUPER_ADMIN"){
            $role = "ADMIN";
        }
      
       return 
            view('Include/_header', $data)

            .view('Include/_left_sidebar_top')

            .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Report/view_reports')

            .view('Include/_footer');
            
            
    }
    public function prescriptionreport()
    { 
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
        $to_date = ($_GET['end_date'] ?? date('Y-m-d')). ' 23:59:59';

        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $user_name = $loggeduserdata['username'];
        $prescriptionModel = model('Hospital/PrescriptionModel');
        $compoundingPrescriptionMainModel = model('Hospital/CompoundingPrescriptionMainModel');
        $salesMainModel = model('Inventory/SalesMainModel');

        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }
        
        $prescriptions = $prescriptionModel->where('created_at >=', $from_date)->where('created_at <=', $to_date)->findColumn('count(id)');

     
        $compounding_prescription = $compoundingPrescriptionMainModel->where('created_at >=', $from_date)->where('created_at <=', $to_date)->findColumn('count(id)');
        $compoundings = $compoundingPrescriptionMainModel->where('created_at >=', $from_date)->where('created_at <=', $to_date)->findColumn('id')??[1,1];
//BluelimeSystem::show_and_die($compoundings);
        $compounding_sales = $salesMainModel->where('created_at >=', $from_date)->where('created_at <=', $to_date)->whereIn('prescription_id',$compoundings)->where('sales_type','COMPOUNDING')->findColumn('count(id)');
        $compounding_sales_external = $salesMainModel->where('created_at >=', $from_date)->where('created_at <=', $to_date)->where('prescription_id',-1)->where('sales_type','COMPOUNDING')->findColumn('count(id)')??0;
        $prescription_sales = $salesMainModel->where('created_at >=', $from_date)->where('created_at <=', $to_date)->where('patient_id <>',0)->where('sales_type','DISPENSARY')->findColumn('count(id)');
        $prescription_sales_external = $salesMainModel->where('created_at >=', $from_date)->where('created_at <=', $to_date)->where('patient_id',0)->where('sales_type','DISPENSARY')->findColumn('count(id)')??0;

        
        $data['active_menu'] = 'reports'; 
        $data['from_date'] = $from_date; 
        $data['to_date'] = $to_date; 
        $data['prescriptions'] = $prescriptions[0]??0; 
        $data['compounding_prescription'] = $compounding_prescription[0]??0; 
        $data['prescription_sales'] = $prescription_sales[0]??0; 
        $data['compounding_sales_external'] = $compounding_sales_external[0]??0; 
        $data['compounding_sales'] = $compounding_sales[0]??0; 
        $data['prescription_sales_external'] = $prescription_sales_external[0]??0; 
        
        if($role==="SUPER_ADMIN"){
            $role = "ADMIN";
        }
        
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Report/prescription_report')

            .view('Include/_footer');
    }
    public function apointmentreport()
    { 
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
        $to_date = ($_GET['end_date'] ?? date('Y-m-d')). ' 23:59:59';

        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $appointmentModel = model('Hospital/AppointmentModel');
        $visitModel = model('Hospital/VisitModel');

        $walk_in = $visitModel->where('appointment_id <= 0')->where('visit_date >=', $from_date)->where('visit_date <=', $to_date)->findColumn('count(id)');
        

        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }
        
        $appointments = $appointmentModel->where('appointment_date >=', $from_date)->where('appointment_date <=', $to_date)->findAll();
        

        //BluelimeSystem::show_and_die($walk_in);
        $data['active_menu'] = 'reports'; 
        $data['from_date'] = $from_date; 
        $data['to_date'] = $to_date; 
        $data['appointments'] = $appointments; 
        $data['walk_in'] = $walk_in[0]??0; 
        
        
        
        
        if($role==="SUPER_ADMIN"){
            $role = "ADMIN";
        }
        
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Report/appointment_report')

            .view('Include/_footer');
    }
    public function tatreport()
    { 
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
        $to_date = ($_GET['end_date'] ?? date('Y-m-d')). ' 23:59:59';

        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $user_name = $loggeduserdata['username'];
        $visitModel = model('Hospital/VisitModel');
        $waitingListModel = model('Hospital/WaitingListModel');
        $visitModel2 = model('Hospital/VisitModel');

        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }
        
        $tat_lists = [];

        $visitModel = $visitModel->join('hospital_visit_encounter','hospital_visit_encounter.visit_id = hospital_visits.id','inner');
        $visit_tat = $visitModel->select('"Appointment" AS visit_type,hospital_visits.doctor_name, AVG(TIMESTAMPDIFF(MINUTE, hospital_visits.created_at,hospital_visit_encounter.created_at)) AS UpToDoctor,AVG(TIMESTAMPDIFF(MINUTE,hospital_visits.created_at,hospital_visit_encounter.updated_at)) AS TotalStayedTime')->where('hospital_visits.visit_date >=', $from_date)->where('hospital_visits.visit_date <=', $to_date)->where('hospital_visits.appointment_id <>', 0)->groupBy('hospital_visits.doctor_name')->findAll();
        
        $visitModel2 = $visitModel2->join('hospital_visit_encounter','hospital_visit_encounter.visit_id = hospital_visits.id','left');
        $visitModel2 = $visitModel2->join('hospital_waiting_list','hospital_waiting_list.id = hospital_visits.waiting_id','left');
        //$visitModel2->where('hospital_waiting_list.created_at', 0;
       // $visit_tat_walkIn = $visitModel2->findAll();
       // BluelimeSystem::show_and_die($visit_tat_walkIn);
        $visit_tat_walkIn = $visitModel2->select('"Walk In" AS visit_type,hospital_visits.doctor_name, 
                                                    if(
                                                        CAST(hospital_visits.waiting_id AS SIGNED) = -1,
                                                            AVG(TIMESTAMPDIFF(MINUTE, hospital_visits.created_at,hospital_visit_encounter.created_at)),
                                                            AVG(TIMESTAMPDIFF(MINUTE, hospital_waiting_list.created_at,hospital_visit_encounter.created_at))
                                                    ) AS UpToDoctor,
                                                     if(
                                                        CAST(hospital_visits.waiting_id AS SIGNED) = -1,
                                                            AVG(TIMESTAMPDIFF(MINUTE,hospital_visits.created_at,hospital_visit_encounter.updated_at)),
                                                            AVG(TIMESTAMPDIFF(MINUTE,hospital_waiting_list.created_at,hospital_visit_encounter.updated_at))
                                                        ) AS TotalStayedTime')
                                                    ->where('hospital_visits.visit_date >=', $from_date)
                                                    ->where('hospital_visits.visit_date <=', $to_date)
                                                    ->where('hospital_visits.appointment_id', 0)
                                                    ->groupBy('hospital_visits.doctor_name')->findAll();
        
        foreach($visit_tat as $tat){
            $row = [
                'visit_type' => 'Appointment',
                'doctor_name' => $tat['doctor_name'],
                'UpToDoctor' => $tat['UpToDoctor'],
                'TotalStayedTime' => $tat['TotalStayedTime']
            ];

            array_push($tat_lists, $row);
        }
        foreach($visit_tat_walkIn as $tat){
            $row = [
                'visit_type' => 'Walk In',
                'doctor_name' => $tat['doctor_name'],
                'UpToDoctor' => $tat['UpToDoctor'],
                'TotalStayedTime' => $tat['TotalStayedTime']
            ];

            array_push($tat_lists, $row);
        }
        foreach ($tat_lists as $key => $doctor) {
            $sort[$key] = $doctor['doctor_name'];
        }
        if(!empty($tat_lists)){
            array_multisort($sort, SORT_DESC, $tat_lists);
       }

        //BluelimeSystem::show_and_die($visit_tat_walkIn);
        $data['active_menu'] = 'reports'; 
        $data['from_date'] = $from_date; 
        $data['to_date'] = $to_date; 
        $data['tat_lists'] = $tat_lists;  
        
        
        
        
        if($role==="SUPER_ADMIN"){
            $role = "ADMIN";
        }
        
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Report/tat_report')

            .view('Include/_footer');
    }
    public function detail_visit_tat()
    { 
        $from_date = ($_GET['selected_date'] ?? date('Y-m-d')). ' 00:00:00';
        $to_date = ($_GET['end_date'] ?? date('Y-m-d')). ' 23:59:59';

        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $user_name = $loggeduserdata['username'];
        $visitModel = model('Hospital/VisitModel');
        $waitingListModel = model('Hospital/WaitingListModel');
        $visitModel2 = model('Hospital/VisitModel');

        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }
        
        $tat_lists = [];

        $visitModel = $visitModel->join('hospital_visit_encounter','hospital_visit_encounter.visit_id = hospital_visits.id','inner');
        $visitModel2 = $visitModel2->join('hospital_appointments','hospital_appointments.id = hospital_visits.appointment_id','left');
        $visit_tat = $visitModel->select('"Appointment" AS visit_type,hospital_visits.doctor_name, 
                                            hospital_visits.patient_name, 
                                            hospital_appointments.time as WaitingRegistrationTime,
                                            hospital_visits.created_at as AssignedTime,
                                             hospital_visit_encounter.created_at AS ViewTime,
                                             hospital_visit_encounter.updated_at AS FinishedTime,
                                            (TIMESTAMPDIFF(MINUTE, hospital_visits.created_at,hospital_visits.created_at)) AS ReceptionArea, 
                                            (TIMESTAMPDIFF(MINUTE, hospital_visits.created_at,hospital_visit_encounter.created_at)) AS UpToDoctor,
                                            (TIMESTAMPDIFF(MINUTE,hospital_visits.created_at,hospital_visit_encounter.updated_at)) AS TotalStayedTime')
                                            ->where('hospital_visits.visit_date >=', $from_date)->where('hospital_visits.visit_date <=', $to_date)
                                            ->where('hospital_visits.appointment_id <>', 0)->findAll();
        
        $visitModel2 = $visitModel2->join('hospital_visit_encounter','hospital_visit_encounter.visit_id = hospital_visits.id','left');
        $visitModel2 = $visitModel2->join('hospital_waiting_list','hospital_waiting_list.id = hospital_visits.waiting_id','left');
        //$visitModel2->where('hospital_waiting_list.created_at', 0;
       // $visit_tat_walkIn = $visitModel2->findAll();
       // BluelimeSystem::show_and_die($visit_tat_walkIn);
        $visit_tat_walkIn = $visitModel2->select('"Walk In" AS visit_type,hospital_visits.doctor_name, hospital_visits.patient_name, 
                                                    hospital_waiting_list.created_at AS WaitingRegistrationTime,
                                                    hospital_visits.created_at AS AssignedTime,
                                                    hospital_visit_encounter.created_at AS ViewTime,
                                                    hospital_visit_encounter.updated_at AS FinishedTime,

                                                    if(
                                                        CAST(hospital_visits.waiting_id AS SIGNED) = -1,
                                                            (TIMESTAMPDIFF(MINUTE, hospital_visits.created_at,hospital_visits.created_at)),
                                                            (TIMESTAMPDIFF(MINUTE, hospital_waiting_list.created_at,hospital_visits.created_at))
                                                    ) AS ReceptionArea,
                                                    if(
                                                    CAST(hospital_visits.waiting_id AS SIGNED) = -1,
                                                        (TIMESTAMPDIFF(MINUTE,hospital_visits.created_at,hospital_visit_encounter.created_at)),
                                                        (TIMESTAMPDIFF(MINUTE,hospital_waiting_list.created_at,hospital_visit_encounter.created_at))
                                                    ) AS UpToDoctor,                                                     
                                                    if(
                                                        CAST(hospital_visits.waiting_id AS SIGNED) = -1,
                                                            (TIMESTAMPDIFF(MINUTE,hospital_visits.created_at,hospital_visit_encounter.updated_at)),
                                                            (TIMESTAMPDIFF(MINUTE,hospital_waiting_list.created_at,hospital_visit_encounter.updated_at))
                                                        ) AS TotalStayedTime')
                                                    ->where('hospital_visits.visit_date >=', $from_date)
                                                    ->where('hospital_visits.visit_date <=', $to_date)
                                                    ->where('hospital_visits.appointment_id', 0)
                                                    ->findAll();
        //BluelimeSystem::show_and_die($visit_tat_walkIn);
        foreach($visit_tat as $tat){
            $row = [
                'visit_type' => 'Appointment',
                'patient_name' => $tat['patient_name'],
                'doctor_name' => $tat['doctor_name'],
                'WaitingRegistrationTime' => $tat['WaitingRegistrationTime'],
                'AssignedTime' => $tat['AssignedTime'],
                'ViewTime' => $tat['ViewTime'],
                'FinishedTime' => $tat['FinishedTime'],
                'ReceptionArea' => $tat['ReceptionArea'],
                'UpToDoctor' => $tat['UpToDoctor'],
                'TotalStayedTime' => $tat['TotalStayedTime']
            ];

            array_push($tat_lists, $row);
        }
        foreach($visit_tat_walkIn as $tat){
            $row = [
                'visit_type' => 'Walk In',
                'patient_name' => $tat['patient_name'],
                'doctor_name' => $tat['doctor_name'],
                'WaitingRegistrationTime' => $tat['WaitingRegistrationTime'],
                'AssignedTime' => $tat['AssignedTime'],
                'ViewTime' => $tat['ViewTime'],
                'FinishedTime' => $tat['FinishedTime'],
                'ReceptionArea' => $tat['ReceptionArea'],
                'UpToDoctor' => $tat['UpToDoctor'],
                'TotalStayedTime' => $tat['TotalStayedTime']
            ];

            array_push($tat_lists, $row);
        }
        $sort = array();
        foreach ($tat_lists as $key => $doctor) {
            $sort['doctor_name'][$key] = $doctor['doctor_name'];
            $sort['visit_type'][$key] = $doctor['visit_type'];
            $sort['AssignedTime'][$key] = $doctor['AssignedTime'];
        }
        if(!empty($tat_lists)){
            array_multisort($sort['visit_type'], SORT_ASC,$sort['doctor_name'], SORT_DESC, $sort['AssignedTime'], SORT_DESC,$tat_lists);
       }

        //BluelimeSystem::show_and_die($visit_tat_walkIn);
        $data['active_menu'] = 'reports'; 
        $data['from_date'] = $from_date; 
        $data['to_date'] = $to_date; 
        $data['tat_lists'] = $tat_lists;  
        
        
        
        
        if($role==="SUPER_ADMIN"){
            $role = "ADMIN";
        }
        
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Report/detail_visit_tat_report')

            .view('Include/_footer');
    }
    public function performancereport()
    { 
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }
        
        $billDetailModel = model('Hospital/BillDetailModel'); 
        $billModel = model('Hospital/BillModel'); 
        $viewDoctorVisitIncomes = model('Hospital/ViewDoctorVisitIncomes'); 
        $billPayementModel = model('Hospital/BillPaymentModel'); 
        $visitModel = model('Hospital/VisitModel'); 
        $patientModel = model('Hospital/PatientModel'); 
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $role = $loggeduserdata['role'];

        $number_of_visits_by_doctor = [];
        $doctors_income = [];
        $start_date = (!empty($_GET['start_date'])?$_GET['start_date']:date('Y-m-d')).'%';
        $end_date = (!empty($_GET['end_date'])?$_GET['end_date']:date('Y-m-d')).' 23:59:59';

        //BluelimeSystem::show_and_die($_POST);
        if(!empty($_GET['selected_range'])){
           // BluelimeSystem::show_and_die($_POST['selected_range']);
            if($_GET['selected_range']==='this_month'){
                $number_of_visits_by_doctor = $visitModel->select('MAX(doctor_name) as `doctor_name`, doctor_id, count(id) as total')->where('MONTH(visit_date)',date('m'))->where('YEAR(visit_date)',date('Y'))->groupBy('doctor_id')->findAll();
                $doctors_income = $viewDoctorVisitIncomes->select('MAX(doctor_name) as `doctor_name`, doctor_id, sum(payed_amount) as total_amount')->where('MONTH(payed_date)',date('m'))->where('YEAR(payed_date)',date('Y'))->groupBy('doctor_id')->findAll();
                
            }
            else if($_GET['selected_range']==='this_year'){
                $number_of_visits_by_doctor = $visitModel->select('MAX(doctor_name) as `doctor_name`, doctor_id, count(id) as total')->where('YEAR(visit_date)',date('Y'))->groupBy('doctor_id')->findAll();
                $doctors_income = $viewDoctorVisitIncomes->select('MAX(doctor_name) as `doctor_name`, doctor_id, sum(payed_amount) as total_amount')->where('YEAR(payed_date)',date('Y'))->groupBy('doctor_id')->findAll();
                
            }
            else if($_GET['selected_range']==='all_time'){
                $number_of_visits_by_doctor = $visitModel->select('MAX(doctor_name) as `doctor_name`, doctor_id,  count(id) as total')->groupBy('doctor_id')->findAll();
                $doctors_income = $viewDoctorVisitIncomes->select('MAX(doctor_name) as `doctor_name`, doctor_id,  sum(payed_amount) as total_amount')->groupBy('doctor_id')->findAll();
                
            }
        }
        else{           
            $number_of_visits_by_doctor = $visitModel->select('MAX(doctor_name) as `doctor_name`, doctor_id,  count(id) as total')->where('visit_date >= ',$start_date)->where('visit_date <= ',$end_date)->groupBy('doctor_id')->findAll();
            $doctors_income = $viewDoctorVisitIncomes->select('MAX(doctor_name) as `doctor_name`, doctor_id,  sum(payed_amount) as total_amount')->where('payed_date >= ',$start_date)->where('payed_date <= ',$end_date)->groupBy('doctor_id')->findAll();  
        }
       
        if(!empty($_GET['selected_range'])){
            
             if($_GET['selected_range']==='this_month'){
                 $number_of_visits = $visitModel->select('DISTINCT DAY(visit_date) as dt,count(id) as total')->where('YEAR(visit_date)',date('Y'))->where('MONTH(visit_date)',date('m'))->groupBy('YEAR(visit_date),MONTH(visit_date),DAY(visit_date)')->findAll();
                 $income = $billPayementModel->select('DISTINCT DAY(payed_date) as dt, sum(payed_amount) as total_amount')->where('YEAR(payed_date)',date('Y'))->where('MONTH(payed_date)',date('m'))->groupBy('YEAR(payed_date),MONTH(payed_date),DAY(payed_date)')->findAll();
               
             }
             else if($_GET['selected_range']==='this_year'){
                $number_of_visits = $visitModel->select('DISTINCT MONTH(visit_date) as dt,count(id) as total')->where('YEAR(visit_date)',date('Y'))->groupBy('YEAR(visit_date),MONTH(visit_date)')->findAll();
                $income = $billPayementModel->select('DISTINCT MONTH(payed_date) as dt, sum(payed_amount) as total_amount')->where('YEAR(payed_date)',date('Y'))->groupBy('YEAR(payed_date),MONTH(payed_date)')->findAll();
                
             }
             else if($_GET['selected_range']==='all_time'){
                 $number_of_visits = $visitModel->select('DISTINCT YEAR(visit_date) as dt,count(id) as total')->groupBy('YEAR(visit_date)')->orderBy('YEAR(visit_date)')->findAll();
                 $income = $billPayementModel->select('DISTINCT YEAR(payed_date) as dt, sum(payed_amount) as total_amount')->orderBy('YEAR(payed_date)')->groupBy('YEAR(payed_date)')->findAll();
                //BluelimeSystem::show_and_die($income);
             }
         }
         else{           
             $number_of_visits = $visitModel->select('DAY(visit_date), count(id) as total')->where('visit_date >= ',$start_date)->where('visit_date <= ',$end_date)->groupBy('DAY(visit_date)')->orderBy('DAY(visit_date)')->findAll();
             $income = $billPayementModel->select('DAY(payed_date), sum(payed_amount) as total_amount')->where('payed_date >= ',$start_date)->where('payed_date <= ',$end_date)->groupBy('DAY(payed_date)')->orderBy('DAY(payed_date)')->findAll();  
         }

        $doctors_income_detail = $viewDoctorVisitIncomes->where('doctor_id',@$_GET['doc_id']??66)->where('payed_date >= ',$start_date)->where('payed_date <= ',$end_date)->findAll();  
        foreach($doctors_income_detail as $index => $billDetail){
            $doctors_income_detail[$index]['service_name'] = $billDetailModel->where('bill_id',$billDetail['bill_id'])->findColumn('service_name')[0];
            
        }

        //BluelimeSystem::show_and_die($doctors_income_detail);

        
       
        $data['active_menu'] = 'reports'; 
        $data['number_of_visits_by_doctor'] = $number_of_visits_by_doctor;
        $data['number_of_visits'] = $number_of_visits;
        $data['doctors_income'] = $doctors_income;
        $data['doctors_income_detail'] = $doctors_income_detail;
        $data['income'] = $income;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        
        if($role==="SUPER_ADMIN"){
            $role = "ADMIN";
        }
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Report/view_performance_reports')

            .view('Include/_footer');
    }
    public function paymentdetailreport()
    { 
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }
        
        $billDetailModel = model('Hospital/BillDetailModel'); 
        $patientModel = model('Hospital/PatientModel'); 
        $billModel = model('Hospital/BillModel'); 
        $billPayementModel = model('Hospital/BillPaymentModel');

        $search_criteria = (!empty($_POST['search_criteria'])?$_POST['search_criteria']:'*');
        $patient_bill = $patientModel->search_patient($search_criteria);
        //BluelimeSystem::show_and_die($patient_bill);
        $patient_id = @$_GET['pid'];
        $bill_id = $billModel->where('patient_id',$patient_id)->findColumn('id');
        $bills = $billModel->where('patient_id',$patient_id)->findAll();
        if($bill_id){
        $billdetails = $billDetailModel->whereIn('bill_id',$bill_id)->findAll();
        
        $due=0;
        @$remaining = $billModel->whereIn('id', $bill_id)->findAllWithDueAmount();
            foreach($remaining as $rem)
            {
                $due  += $rem['due_amount'];
            }

        $billpayment = $billPayementModel->whereIn('bill_id',$bill_id)->findAll();       
        
        
        
        
        $data['billdetails'] = $billdetails;
        $data['billpayment'] = $billpayment;
        }
        $data['active_menu'] = 'reports'; 
        
        $data['patient_bill'] = $patient_bill;
        $data['search_criteria'] = $search_criteria;
        $data['bills'] = $bills;
        $data['remaining'] = $due??0;
    
        if($role==="SUPER_ADMIN"){
            $role = "ADMIN";
        }

      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Report/view_patient_detail_report')

            .view('Include/_footer');
    }
    public function medicalhistoryreport()
    { 
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        $userModel = model('System/UserModel');
        $visitModel = model('Hospital/VisitModel');
        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }
        $selected_date = isset($_POST['selected_date'])?($_POST['selected_date'].'%'):date('Y-m-d').'%';
        $selected_doctor = !empty($_POST['selected_doctor'])?($_POST['selected_doctor']):'%';
        
        $role = $loggeduserdata['role'];
        
        $visit_id = $visitModel->where('visit_date like',$selected_date)->where('doctor_id like',$selected_doctor)->findColumn('id');
        $doctors = $userModel->where('role','DOCTOR')->where('active',1)->findAll();
        
        if($visit_id){
        $encounterHistory = $visitEncounterModel->whereIn('visit_id',$visit_id)->findAll();
        foreach($encounterHistory as $index => $history){
            $encounterHistory[$index]['doctor_name'] = $visitModel->where('id',$history['visit_id'])->findColumn('doctor_name');
            $encounterHistory[$index]['doctor_id'] = $visitModel->where('id',$history['visit_id'])->findColumn('doctor_id');
            $encounterHistory[$index]['patient_name'] = $visitModel->where('id',$history['visit_id'])->findColumn('patient_name');
        }
        }
        else{
            $encounterHistory =[]; 
        }

       
        $data['active_menu'] = 'reports'; 
        $data['selected_date'] = $selected_date;
        $data['selected_doctor'] = $selected_doctor;
        $data['encounterHistory'] = $encounterHistory;
        $data['doctors'] = $doctors;

        
        if($role==="SUPER_ADMIN"){
            $role = "ADMIN";
        }
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Report/view_medical_history_report')

            .view('Include/_footer');
    }
    public function diagnosisreport()
    { 
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        $patientModel = model('Hospital/PatientModel');
        $visitModel = model('Hospital/VisitModel');
        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }
        $selected_date = !empty($_POST['selected_date'])?($_POST['selected_date'].'%'):date('Y-m-d').'%';
        $end_date = (!empty($_POST['end_date'])?$_POST['end_date']:date('Y-m-d')).' 23:59:59';
        
        $role = $loggeduserdata['role'];
        
        $encounterHistory = $visitEncounterModel->where('registered_date >= ',$selected_date)->where('registered_date <= ',$end_date)->findAll();
        foreach($encounterHistory as $index => $history){
            $patient_id = $visitModel->where('id',$history['visit_id'])->findColumn('patient_id');
            if($patient_id){
            $encounterHistory[$index]['date_of_birth'] = $patientModel->where('id',$patient_id)->findColumn('date_of_birth')[0];
            $encounterHistory[$index]['sex'] = $patientModel->where('id',$patient_id)->findColumn('sex')[0];
            }
            
        }
       
        //BluelimeSystem::show_and_die($encounterHistory);
       
        $data['active_menu'] = 'reports'; 
        $data['selected_date'] = $selected_date;
        $data['end_date'] = $end_date;
        $data['encounterHistory'] = $encounterHistory;

        //BluelimeSystem::print_associated_array($encounterHistory);
        
        if($role==="SUPER_ADMIN"){
            $role = "ADMIN";
        }
      
       return     view('Include/_header', $data)
            .view('Include/_left_sidebar_top')
            .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Report/view_diagnosis_report')
            .view('Include/_footer');
    }

    public function diagnosisreportdetail()
    { 
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $visitEncounterModel = model('Hospital/VisitEncounterModel');
        $patientModel = model('Hospital/PatientModel');
        $visitModel = model('Hospital/VisitModel');
        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }
        $selected_date = !empty($_POST['selected_date'])?($_POST['selected_date'].'%'):date('Y-m-d').'%';
        $end_date = (!empty($_POST['end_date'])?$_POST['end_date']:date('Y-m-d')).' 23:59:59';
        
        $role = $loggeduserdata['role'];
        
        $encounterHistory = $visitEncounterModel->where('registered_date >= ',$selected_date)->where('registered_date <= ',$end_date)->findAll();
        foreach($encounterHistory as $index => $history){
            $patient_id = $visitModel->where('id',$history['visit_id'])->findColumn('patient_id');
            if($patient_id){
            $encounterHistory[$index]['date_of_birth'] = $patientModel->where('id',$patient_id)->findColumn('date_of_birth')[0];
            $encounterHistory[$index]['sex'] = $patientModel->where('id',$patient_id)->findColumn('sex')[0];
            }
            
        }
        
       

        //BluelimeSystem::show_and_die($encounterHistory);
       
        $data['active_menu'] = 'reports'; 
        $data['selected_date'] = $selected_date;
        $data['end_date'] = $end_date;
        $data['encounterHistory'] = $encounterHistory;

        
       
      
       return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')

            .view('Include/_left_sidebar_bottom')

            .view('Hospital/Report/view_diagnosis_report_detail')

            .view('Include/_footer');
    }

    public function performancereport_detail() {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }

        $doctor_id = $_GET['doctor_id'] ?? -1;
        if(empty($doctor_id))
            $doctor_id = -1;

        $userModel = model('System/UserModel');

        $selectedDoctor = $userModel->find($doctor_id);
        $doctors_list = $userModel->where('role', 'DOCTOR')->findAll();

        $bill_join = 'LEFT OUTER JOIN `hospital_bills` ON `hospital_visits`.`id` = `hospital_bills`.`visit_id`
                      LEFT OUTER JOIN `hospital_bill_details` ON `hospital_bills`.`id` = `hospital_bill_details`.`bill_id`';
                             

        $procedures_filter = $_GET['procedures'] ?? [];

        $procedures = BluelimeSystem::convert_array_to_string($procedures_filter);

        $procedures = trim($procedures,"[]");

        $procedure_filter = ' AND `hospital_bill_details`.`service_id` IN ('.$procedures.')';
        
        if(empty(trim($procedures))){
            $bill_join = '';
            $procedure_filter = '';
        }

        $query = 'SELECT MAX(`hospital_visits`.`patient_name`) `patient_name`, `hospital_visits`.`patient_id`,
                MIN(`hospital_visits`.`visit_date`) as `from` , 
                MAX(`hospital_visits`.`visit_date`) as `to`,
                FLOOR( DATEDIFF(MAX(`hospital_visits`.`visit_date`), MIN(`hospital_visits`.`visit_date`))/30)as `visit_months`,
                MOD(DATEDIFF(MAX(`hospital_visits`.`visit_date`), MIN(`hospital_visits`.`visit_date`)),30) as `visit_days`,
                FLOOR(DATEDIFF("'.date("Y-m-d").'", MAX(`hospital_visits`.`visit_date`))/30) `months_after_last_visit`,
                MOD(DATEDIFF("'.date("Y-m-d").'", MAX(`hospital_visits`.`visit_date`)),30) as `days_after_last_visit`,
                COUNT(`hospital_visits`.`id`) as `no_of_visits`,
                MAX(`hospital_visits`.`id`) as `visit_id`
                FROM `hospital_visits` 
                '.$bill_join.'
                WHERE `doctor_id` = '.$doctor_id.' 
                '.$procedure_filter.'
                GROUP BY `patient_id`';
        
        $visitModel = model('Hospital/VisitModel'); 

        $result = $visitModel->db->query($query);
        
        $report_data = [];
        foreach ($result->getResult('array') as $row) {
            array_push($report_data, $row);
        }

        $procedure_list = [];
        if($doctor_id != -1){

            $visit_list = $visitModel->where('doctor_id', $doctor_id)->findColumn('id');

            $visit_list = BluelimeSystem::convert_array_to_string($visit_list);

            $visit_list = trim($visit_list,"[]");
            if(!empty(trim($visit_list))){
                $query = 'SELECT `service_id`, `service_name` FROM `hospital_bill_details` INNER JOIN `hospital_bills` ON `bill_id` = `hospital_bills`.`id`
                        WHERE `hospital_bills`.`visit_id` IN ('.$visit_list.') AND `hospital_bill_details`.`service_name` NOT LIKE \'IMPORTED_%\' 
                        GROUP BY `service_id`, `service_name`
                        ORDER BY `service_name`';
                        
                $result = $visitModel->db->query($query);
                
                foreach ($result->getResult('array') as $row) {
                    array_push($procedure_list, $row);
                }
            }
        }

        $patient_ids = [-1];
        foreach($report_data as $index => $row){
            $report_data[$index]['has_message'] = false;
            array_push($patient_ids, $row['patient_id']);
        }

        $messageModel = model('Hospital/MessageModel');
        $message_for_patient = $messageModel->select('DISTINCT(patient_id)')->whereIn('patient_id',$patient_ids)->findAll();

        //BluelimeSystem::show_and_die($message_for_patient);

        foreach($message_for_patient as $message){
            foreach($report_data as $index => $report_row){
                if($report_row['patient_id'] == $message['patient_id'])
                    $report_data[$index]['has_message'] = true;
            }
        }

        
        $data = [
            'loggeduserdata' => $loggeduserdata,
            'selected_doctor' => $selectedDoctor,
            'doctors_list' => $doctors_list,
            'report_data' => $report_data,
            'procedure_list' => $procedure_list,
            'procedure_filter' => $procedures_filter
        ];

        if($role==="SUPER_ADMIN"){
            $role = "ADMIN";
        }
        $data['active_menu'] = 'reports'; 
        return 
            view('Include/_header', $data)
            .view('Include/_left_sidebar_top')

            .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')
        
            .view('Include/_left_sidebar_bottom')
            .view('Hospital/Report/doctors_patient_visit_counter')
            .view('Include/_footer');
    }

    public function frequentcustomers() {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }
        
$query = 'SELECT `patient_name`, mobile, COUNT(id) AS No_of_Visit
                FROM `hospital_visits` 
                
                GROUP BY `patient_id` HAVING
                COUNT(id) > 3 ORDER BY COUNT(id) DESC';
        
        $visitModel = model('Hospital/VisitModel'); 

        $result = $visitModel->db->query($query);
        
        $report_data = [];
        foreach ($result->getResult('array') as $row) {
            array_push($report_data, $row);
        }
    //BluelimeSystem::show_and_die($report_data);
        $data['report_data'] = $report_data;

        $data['active_menu'] = 'reports'; 
        if($role==="SUPER_ADMIN"){
            $role = "ADMIN";
        }

        return view('Include/_header', $data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Report/frequent_customers')
        .view('Include/_footer');
    }
    
    public function progresspicture() {
        $loggeduserdata = BluelimeSystem::$Authentication::get_logged_user_data();
        $progressPictureModel = model('Hospital/ProgressPictureModel');
        $visitModel = model('Hospital/VisitModel');

        $patient_id = $visitModel->where('id',@$_GET['visit_id'])->findColumn('patient_id')[0];
        $progress_picture = $progressPictureModel->where('patient_id',$patient_id)->orderBy('id','DESC')->findAll();

        $data['active_menu'] = 'patients_waiting';
        $data['progress_picture'] = $progress_picture;

        $role = $loggeduserdata['role'];
        if($role != "SUPER_ADMIN"){
            if($role != "ADMIN"){
                if($role != "MIS"){
                    return redirect()->to(site_url('/?permission=0'));
                }
            }
        }
        $data['active_menu'] = 'reports'; 

        return view('Include/_header', $data)
        .view('Include/_left_sidebar_top')
        .view('Hospital/'.ucfirst(strtolower($role)).'/_left_sidebar_'.strtolower($role).'')
        .view('Include/_left_sidebar_bottom')
        .view('Hospital/Report/view_progress_picture')
        .view('Include/_footer');
    }

    public function survey_report(){
        $from = $_GET['from'] ?? '';
        $to   = $_GET['to'] ?? '';

        // if(strtotime($from) > strtotime($to)){
        //     $temp = $from;
        //     $from = $to;
        //     $to = $temp;
        // }

        $branch_api = [
            "Wollosefer" => [
                        'url' => 'http://196.189.125.153/api2/report/v1/get_survey_report',
                        'api_key' => '79d3ccd39e45eef0833f2bdfad05b2fa_20260521',
                        'branch_name' => 'Wollosefer',
                   ],
            "Lebu" => [
                        'url' => 'http://196.189.0.108/api2/report/v1/get_survey_report',
                        'api_key' => '02d77fe71ef27b98eb76205e3daad831_20260521',
                        'branch_name' => 'Lebu',
                   ],
            "Gazebo" => [
                        'url' => 'http://196.189.239.210/api2/report/v1/get_survey_report',
                        'api_key' => '8badc4c11a16950801e989e238c6854f_20260502',
                        'branch_name' => 'Gazebo',
                   ],
            // "AbiyDev" => [
            //             'url' => 'http://rank.dev2/api2/report/v1/get_survey_report',
            //             'api_key' => '8b67fbad38ab7ab93af95f0c125e54d7_20260521',
            //             'branch_name' => 'AbiyDev',
            //        ]
        ];

        $api_connection = [];
        $survey_report = [];
        foreach($branch_api as $api){
            //$ip_addess = $api['ip_addess'];
            $api_key = $api['api_key'];
            $url = $api['url'];
            $result = BluelimeSystem::communicate_api2(__METHOD__, $url, $api_key,['from_date'=>$from,'to_date'=>$to]);

            $result = BluelimeSystem::convert_to_array($result);

            $api_connection[$api['branch_name']] = ['branch'=>$api['branch_name'], 'status'=>0];

            if(!empty($result))
                $api_connection[$api['branch_name']]['status'] = 1;
            
            $data = $result['data'] ?? [];
            
            if(BluelimeSystem::check_jwt_signature($data, $api_key, ($result['signature']??''))){
                if($result['status'] == 'success')
                    $api_connection[$api['branch_name']]['status'] = 2;

                foreach($data as $report){
                    $survey_report[$report['survey']] ??= ['_total_' => 0];

                    $survey_report[$report['survey']][$api['branch_name']] = $report['total'];
                    $survey_report[$report['survey']]['_total_'] += $report['total'];
                }
            }
        }

        
        $view_data = [
            'survey_report' => $survey_report,
            'from' => $from,
            'to' => $to,
            'api_connection' => $api_connection,
            'active_menu' => 'reports',
            'active_sub_menu' => 'survey_report',
        ];
        return view('Hospital/Report/survey_report',$view_data);
    }
}
<?php

namespace App\ThirdParty\Bluelime\POS\PEDS;

use App\ThirdParty\Bluelime\POS\BluelimePOSInterface;
use BluelimeSystem;
use Exception;

class PEDSPOS implements BluelimePOSInterface
{
    public function send_invoice_to_pos(array $pos_bill_json, array $pos_machine):bool{

        $invoice    = $this->create_request_body($pos_bill_json);
        $result     = $this->deliver_to_pos($invoice, $pos_machine);

        $result     = BluelimeSystem::convert_to_array($result);

        $success    = $result['Success'] ?? false;
        $recive_manual = $pos_machine['receive_manual'] ?? false;
        if($recive_manual)
            $success = true;

        return $success;
    }

    private function create_request_body(array $pos_bill_json){
        $payment_type = ($pos_bill_json['payment_type'] == \BluelimeConfiguration::PAYMENT_METHOD::CREDIT) ? 2 : 0;
        $invoice = [
            "HoldSalesIdentifierId" => $pos_bill_json['invoice_no'],
            "TransactionType"       => 0,
            "InvoiceNo"             => $pos_bill_json['invoice_no'],
            "PaymentType"           => $payment_type,
            "TableNumber"           => "",
            "SalesPerson"           => $pos_bill_json['username'],
            "HoldMemo"              => $pos_bill_json['company_name'] ?? "",
            "Date"                  => date("Y-m-d H:i:s"),
            "CustomerName"          => $pos_bill_json['customer_name'],
            "CustomerTIN"           => is_numeric($pos_bill_json['tin']) ? $pos_bill_json['tin'] : "",
            "CustomerVAT"           => "",
            "CashierUpdated"        => "Cashier",
            "POSId"                 => "POS-001",
            "HoldSalesItems"        => []
        ];

        $sales_item_list = $pos_bill_json['sales_item_list'] ?? [];

        $cnt = 1;
        foreach($sales_item_list as $row){
            if(trim($row['item_type']) == '')
                $row['item_type'] = 'ITEM TYPE';

            $sales_item = [
                "HoldSalesItemIdentifierId" => $pos_bill_json['invoice_no'] . '-' . $cnt++,
                "CategoryIdentifierId"      => 'CAT-'.$row['item_type'],
                "CategoryName"              => $row['item_type'],
                "ItemIdentifierId"          => $row['item_id'],
                "ItemDescription"           => $row['item_name'],
                "ItemCode"                  => $row['item_id'],
                "UomIdentifierId"           => $row['measurment'],
                "UomName"                   => $row['measurment'],
                "Quantity"                  => $row['quantity'],
                "SalesUnitPrice"            => $row['unit_price'],
                "TaxType"                   => $row['vat'] ? 1 : 4,
            ];

            array_push($invoice['HoldSalesItems'], $sales_item);
        }

        //$invoice = BluelimeSystem::convert_array_to_string($invoice);
        //$invoice = addslashes($invoice);
        return $invoice;
    }
   
    private function deliver_to_pos(array $json_data, array $pos_machine, string $api_url = 'Add'){
        $url = $pos_machine['extra']['url'] . $api_url;
        $username = base64_decode($pos_machine['extra']['username']);
        $password = base64_decode($pos_machine['extra']['password']);

        //Debug 
        if(BluelimeSystem::bluelime_debug_is_on())
            $verbose_file = BluelimeSystem::write_log('Bluelime/POS','PEDS','','CURL',true,true);
        try
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36");
            $headers = ["Accept: application/json",'Content-Type: application/json'];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

            $json_data = json_encode($json_data);

            curl_setopt($ch, CURLOPT_POSTFIELDS,$json_data);

            //Debug 
            if(\BluelimeSystem::bluelime_debug_is_on())
            {
                $log = "\ninvoice_json\n".$json_data."\n\n";
                fwrite($verbose_file,data: $log);

                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_STDERR, $verbose_file);
            }

            $result = curl_exec($ch);
            
            curl_close($ch);
            
            if(\BluelimeSystem::bluelime_debug_is_on())
            {
                $log = "\nResult\n".$result."\n";
                fwrite(stream: $verbose_file,data: $log);
            }
        } 
        catch(Exception $e)
        {
            $result = $e->getMessage();
            
            if(\BluelimeSystem::bluelime_debug_is_on())
            {
                $log = "\n\Error\n".$result."\n";
                fwrite(stream: $verbose_file,data: $log);
            }
        }
        
        if(BluelimeSystem::bluelime_debug_is_on())
            fclose(stream: $verbose_file);

        return $result;
    }

    public function resend_invoice_to_pos(array $pos_bill_json, array $pos_machine):bool{
        return $this->send_invoice_to_pos($pos_bill_json, $pos_machine);
    }

    public function void_invoice(string $reference_no, array $pos_machine):bool{
        //return $this->deliver_to_pos($void_invoice, $pos_machine, 'Void');
        //$void_invoice = BluelimeSystem::convert_array_to_string($void_invoice);
        return $this->void_by_my_self($reference_no, $pos_machine);
    }

    private function void_by_my_self(string $reference_no, array $pos_machine){
        $url = $pos_machine['extra']['url_removehold'];
        try
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36");
            $headers = ["Accept: application/json",'Content-Type: application/json'];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            //curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NONE);

            $data = ["InvoiceNo"=> $reference_no];
            $json_data = json_encode($data);

            curl_setopt($ch, CURLOPT_POSTFIELDS,$json_data);

            $result = curl_exec($ch);
            $result = BluelimeSystem::convert_to_array($result);
            $status = $result['status'] ?? 'fail';
            curl_close($ch);

            if($status == 'success')
                return true;
            
        } 
        catch(Exception $e)
        {
            return false;
        }
        return false;
    }

    public function update_invoice(array $pos_bill_json, array $pos_machine):bool{
        return $this->send_invoice_to_pos($pos_bill_json, $pos_machine);
    }

    public function read_response(array $pos_machine){
        $respose_data = file_get_contents('php://input');
        BluelimeSystem::write_log('PEDS','incoming',$respose_data,'$_POST');
        BluelimeSystem::write_log('PEDS','incoming',file_get_contents('php://input'),'php://input');
        //$respose_data = BluelimeSystem::convert_array_to_string($respose_data);
        $response_data_array = BluelimeSystem::convert_to_array($respose_data);
        $invoice_number = $response_data_array['InvoiceNumber'] ?? '';
        $fs_number = $response_data_array['FsInvoiceNo'] ?? '';
        $machine_number = $response_data_array['MachineID'] ?? '';
        $success_acknowledgment = '{
    "Ex":null,
    "Message":"Successfully Posted.",
    "Success":true,
    "Content":"'.$invoice_number.'"
}';

    $fail_acknowledgment = '{
    "Ex":null,
    "Message":"Failed to post sales.",
    "Success":false,
    "Content":""
}';

       return [$invoice_number, $fs_number, $machine_number, $respose_data, $success_acknowledgment, $fail_acknowledgment];
    }
 
    public function acknowledge_response(array $acknowledge_list, array $pos_machine){
    }

}
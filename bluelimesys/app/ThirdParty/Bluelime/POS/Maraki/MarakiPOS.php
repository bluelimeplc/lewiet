<?php

namespace App\ThirdParty\Bluelime\POS\Maraki;

use App\ThirdParty\Bluelime\POS\BluelimePOSInterface;
use BluelimeSystem;
use Exception;

class MarakiPOS implements BluelimePOSInterface
{

// <Invoice>
//   <Plant_Code>RANK FAMILY PLC</Plant_Code>
//   <Plant_Name>_</Plant_Name>
//   <Username>Mr Rank Admin N/A</Username>
//   <Invoice_Type>CASH</Invoice_Type>
//   <Reference_Number>RECN-006938</Reference_Number>
//   <Invoice_Date>2024-05-21T06:42:59.840258+00:00</Invoice_Date>
//   <Customer_Code>dbdb6ef4-817e-4670-9ef6-2ab4589f6977</Customer_Code>
//   <Customer_Name>Mr Berhanu Ayalew  Tadesse</Customer_Name>
//   <Customer_TIN>
//   </Customer_TIN>
//   <Payment_Type>Cash</Payment_Type>
//   <Invoice_DiscOrAdd_Amount>0</Invoice_DiscOrAdd_Amount>
//   <Line_Items>
//     <Item_ID>col</Item_ID>
//     <Item_Description>Dermatology Consultation</Item_Description>
//     <Item_Quantity>1</Item_Quantity>
//     <Item_UOM>no unit</Item_UOM>
//     <Item_Unit_Price>750</Item_Unit_Price>
//     <Item_Tax_Percent>0</Item_Tax_Percent>
//     <Item_DiscOrAdd_Amount>0</Item_DiscOrAdd_Amount>
//     <Store>
//     </Store>
//     <Line_Item_Type>Common Service</Line_Item_Type>
//   </Line_Items>
// </Invoice>

    public function send_invoice_to_pos(array $pos_bill_json, array $pos_machine):bool{
        if(!($pos_machine['active']??0))
            return true;

        $xml = $this->create_invoice_xml($pos_bill_json);
        $xml_root_path = $pos_machine['extra']['xml_root_path'];
        $reference_no = $pos_bill_json['invoice_no'];
        
        return $this->create_invoice_file($xml, $xml_root_path, $reference_no, $pos_machine);
    }

    private function create_invoice_file(string $xml, string $xml_root_path, string $reference_no, array $pos_machine, bool $again = false):bool{
        if(!($pos_machine['active']??0))
            return true;

        $file_name = 'Bluelime - '.$reference_no.'.xml';
        $success = false;
        try{
            \BluelimeSystem::move_file_safely($xml_root_path . 'Source/' . $file_name, $xml_root_path . 'Bluelime/from_source/' . 'Bluelime - '.$reference_no.'.xml');
            \BluelimeSystem::move_file_safely($xml_root_path . 'Processed/' . $file_name, $xml_root_path . 'Bluelime/from_processed/' . 'Bluelime - '.$reference_no.'.xml');

            $fl = fopen($xml_root_path . 'Source/' . $file_name,'w');
            fwrite($fl, $xml);
            fclose($fl);
            $success = true;
        }
        catch(Exception $e){
            if(!$again){
                $output = null;
                $result_code = null;
                exec('/sbin/bluelime_pos_dir', $output, $result_code);
                \BluelimeSystem::write_bluelime_debug_log('abiy_test','create_invoice_file',['output'=>$output, 'result_code'=>$result_code]);
                sleep(1);
                $success = $this->create_invoice_file($xml, $xml_root_path, $reference_no, $pos_machine, true);
            }
        }
        return $success;
    }

    private function create_invoice_xml(array $pos_bill_json):string{
        $xml = '';

        foreach($pos_bill_json as $index => $value){
            if($index == 'sales_item_list')
                continue;

            $tag = '';
            if($index == 'username')
                $tag = 'Username';
            elseif($index == 'tin')
                $tag = 'Customer_TIN';
            elseif($index == 'invoice_type')
                $tag = 'Invoice_Type';
            elseif($index == 'payment_type')
                $tag = 'Payment_Type';
            elseif($index == 'customer_code')
                $tag = 'Customer_Code';
            elseif($index == 'customer_name')
                $tag = 'Customer_Name';
            elseif($index == 'invoice_no')
                $tag = 'Reference_Number';
            else
                continue;
            
            $xml .= "  <$tag>$value</$tag>\n";
        }

        $xml .= "  <Plant_Code>RANK FAMILY PLC</Plant_Code>\n";
        $xml .= "  <Plant_Name>Lebu</Plant_Name>\n";
        $xml .= "  <Invoice_Date>".date(DATE_ATOM)."</Invoice_Date>\n";
        $xml .= "  <Invoice_DiscOrAdd_Amount>0</Invoice_DiscOrAdd_Amount>\n";


        $line_items = $pos_bill_json['sales_item_list'];

        foreach($line_items as $line){
            $xml .= "  <Line_Items>\n";
            $xml .= "    <Store></Store>\n";
            $xml .= "    <Item_ID>".$line['item_id']."</Item_ID>\n";
            $xml .= "    <Item_UOM>".$line['measurment']."</Item_UOM>\n";
            $xml .= "    <Item_Quantity>".$line['quantity']."</Item_Quantity>\n";
            $xml .= "    <Line_Item_Type>".$line['item_type']."</Line_Item_Type>\n";
            $xml .= "    <Item_Unit_Price>".$line['unit_price']."</Item_Unit_Price>\n";
            $xml .= "    <Item_Description>".$line['item_name']."</Item_Description>\n";
            $xml .= "    <Item_Tax_Percent>".($line['vat'] != 0 ? '15' : '0')."</Item_Tax_Percent>\n";
            $xml .= "    <Item_DiscOrAdd_Amount>0</Item_DiscOrAdd_Amount>\n";
            $xml .= "  </Line_Items>\n";
        }

        $xml = "<Invoice>\n$xml</Invoice>";

        return $xml;
    }

    public function resend_invoice_to_pos(array $pos_bill_json, array $pos_machine):bool{
        return true;
    }

    public function void_invoice(string $reference_no, array $pos_machine):bool{

        
//         $xml = '<Invoice>
//   <Username></Username>
//   <Customer_TIN></Customer_TIN>
//   <Invoice_Type>CASH</Invoice_Type>
//   <Payment_Type>Cash</Payment_Type>
//   <Customer_Code></Customer_Code>
//   <Customer_Name>DELETED - '.intval(substr($reference_no,5)).'</Customer_Name>
//   <Reference_Number>'.$reference_no.'</Reference_Number>
//   <Plant_Code>RANK FAMILY PLC</Plant_Code>
//   <Plant_Name>Lebu</Plant_Name>
//   <Invoice_Date>1900-01-01T00:00:01+00:01</Invoice_Date>
//   <Invoice_DiscOrAdd_Amount>0</Invoice_DiscOrAdd_Amount>
//   <Line_Items>
//     <Store></Store>
//     <Item_ID>0</Item_ID>
//     <Item_UOM>None</Item_UOM>
//     <Item_Quantity>1</Item_Quantity>
//     <Line_Item_Type>Empty</Line_Item_Type>
//     <Item_Unit_Price>0.01</Item_Unit_Price>
//     <Item_Description>Nothing</Item_Description>
//     <Item_Tax_Percent>0</Item_Tax_Percent>
//     <Item_DiscOrAdd_Amount>0</Item_DiscOrAdd_Amount>
//   </Line_Items>
// </Invoice>';

//         $xml_root_path = $pos_machine['extra']['xml_root_path'];
//         $success = $this->create_invoice_file($xml, $xml_root_path, $reference_no);
//         if($success)
//             $this->store_void_on_db($pos_machine['machine_name'], $reference_no);


        $success = true;
        $this->store_void_on_db($pos_machine['machine_name'], $reference_no);
        $this->void_on_pos_machine_auto($pos_machine);
        return $success;
    }

    private function store_void_on_db(string $pos_machine_name, string $invoice_no){
        try{
            $db = \Config\Database::connect();
            $sql = 'INSERT INTO  pos_maraki_void (pos_machine_name, invoice_no, void_time, void_date) VALUES (' . $db->escape(str: $pos_machine_name) . ', ' . $db->escape(str: $invoice_no) . ', ' . time() .',  ' . date("Y-m-d") .')';
            $db->query($sql);
        }
        catch(Exception $e){
            
        }
    }
    public function update_invoice(array $pos_bill_json, array $pos_machine): bool{
        return false;
    }

    public function read_response(array $pos_machine){
      
        
        $xml_root_path = $pos_machine['extra']['xml_root_path'];
        
        $response_file_names = [];
        $file_path = $xml_root_path . 'Response/';
        if(is_dir($file_path)){
            $dir = dir($file_path);

            while($file_name = $dir->read()){
                if(strtolower(substr($file_name,-4)) == '.xml'){
                    if(is_file($file_path.$file_name))
                        array_push($response_file_names, $file_name);
                }
            }
            if(!empty($response_file_names))
                \BluelimeSystem::write_bluelime_debug_log('abiy_test','cron',['machine'=>$pos_machine['machine_name'],'response_file_names'=>$response_file_names]);
        }
        else{
            //try to connect again
            $output = null;
            $result_code = null;
            exec('/sbin/bluelime_pos_dir', $output, $result_code);
            \BluelimeSystem::write_bluelime_debug_log('abiy_test','cron',['output'=>$output, 'result_code'=>$result_code]);
        }
        
        $response_list = $this->read_response_xml_content($response_file_names, $xml_root_path);
        
        return $response_list;
    }

    private function read_response_xml_content(array $response_file_names, string $xml_root_path):array{
        $response_list = [];
        $file_path = $xml_root_path.'Response/';
        foreach($response_file_names as $response_file_name){
            $xml_str = file_get_contents($file_path.$response_file_name);
            $xml_parser = xml_parser_create();
            
            xml_parse_into_struct($xml_parser,$xml_str,$xml_obj);
            
            $xml_data = [];
            foreach($xml_obj as $row)
                $xml_data[$row['tag']] = $row['value']??'';

            //BluelimeSystem::print_associated_array($xml_data,false);

            $response = [
                'file_name' => $response_file_name,
                'response_data' => $xml_str,
                'fs_number' => $xml_data['FS_NUMBER'] ?? '',
                'machine_code' => $xml_data['MACHINE_CODE'] ?? '',
                'reference_number' => $xml_data['REFERENCE_NUMBER'] ?? '',
            ];

            array_push($response_list, $response);
        }

        return $response_list;
    }

    public function acknowledge_response(array $acknowledge_list, array $pos_machine){
        $xml_root_path = $pos_machine['extra']['xml_root_path'];
        $file_path = $xml_root_path . 'Response/';
        foreach($acknowledge_list as $row){
            BluelimeSystem::move_file_safely( from_path: $file_path.$row['file_name'], to_path: $xml_root_path.'Bluelime/from_response/'.$row['file_name']);
            //rename(from: $file_path.$row['file_name'], to:$xml_root_path.'Bluelime/from_response/'.$row['file_name']);
        }
        $this->void_on_pos_machine_auto($pos_machine);
    }

    private function void_on_pos_machine_auto(array $pos_machine){
        if(!($pos_machine['active']??0))
            return true;
        $hour = intval(date('G'));
        $minute = intval(date('i'));

        if(($hour < 8) || ($hour > 20))
            return 0;
        
        if(($hour == 20) && ($minute > 35))
            return 0;

        try{
            $db = \Config\Database::connect();

            $query   = $db->query('SELECT * FROM pos_maraki_void');
            $results = $query->getResultArray();
            
            $now = time();
            $voided = [];
            foreach ($results as $row) {
                if(($now - intval($row['void_time'])) >= 0){
                    array_push($voided, $row['invoice_no']);
                }
            }

            //echo base64_encode(string: 'maraki') . '<br>' .base64_encode(string: 'root') . '<br>' . base64_encode('pass');
            //die();

            $db_name = base64_decode($pos_machine['extra']['db_name']);
            $db_username = base64_decode($pos_machine['extra']['db_username']);
            $db_password = base64_decode($pos_machine['extra']['db_password']);

            $maraki_db_conf = [
                'DSN'      => '',
                'hostname' => $pos_machine['extra']['ip_address'],
                'username' => $db_username,
                'password' => $db_password,
                'database' => $db_name,
                'DBDriver' => 'MySQLi',
                'DBPrefix' => '',
                'pConnect' => false,
                'DBDebug'  => true,
                'charset'  => 'utf8',
                'DBCollat' => 'utf8_general_ci',
                'swapPre'  => '',
                'encrypt'  => false,
                'compress' => false,
                'strictOn' => false,
                'failover' => [],
                'port'     => 3306,
            ];


            $maraki_db = \Config\Database::connect($maraki_db_conf);
            foreach($voided as $ref_note){
                $voider_query = "UPDATE pos_oh SET is_active = 0 WHERE ref_note = '". $ref_note ."' ";
                $maraki_db->query( $voider_query);
                $updated = $maraki_db->affectedRows();
                if($updated >= 1)
                    $db->query("DELETE FROM pos_maraki_void WHERE invoice_no = '". $ref_note ."' ");
            }
        }
        catch(Exception $e){
            //\BluelimeSystem::write_log('')
            //echo $e->getMessage();
        }
    }
}
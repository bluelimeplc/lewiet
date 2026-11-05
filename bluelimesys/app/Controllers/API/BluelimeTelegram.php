<?php

namespace App\Controllers\API;

class BluelimeTelegram extends \App\Controllers\BaseController
{
    private const TELEGRAM_API_TOKEN = '7853839921:AAHzdNjFCYllt41hk1hDiJ35U2zuW0tCARo';
    private const API_BASE_URL = __DIR__;

    private const VERSION = "1.0.2\nVersoin added.";

    public function index(){
        return '{"result":"Welcome to Bluelime Technologies Telegram API"}';
    }

    public function incoming() {
        //$inputJSON = '{"update_id":253044350,"message":{"message_id":26,"from":{"id":446416677,"is_bot":false,"first_name":"Abiy","last_name":"G","username":"abiygd","language_code":"en"},"chat":{"id":446416677,"first_name":"Abiy","last_name":"G","username":"abiygd","type":"private"},"date":1601757108,"text":"Hello"}}';
        $inputJSON = file_get_contents('php://input');
        $this->write_log('incoming_message.txt', $inputJSON);
        
        $json_obj = json_decode($inputJSON,true);
        $chat_id = intval($json_obj['message']['chat']['id'] ?? -1);
        $content = null;
        
        $message = $json_obj['message']['text'] ?? '';
        if(trim(strtolower($message)) == 'version'){
            $content = $this->create_message_content($chat_id, $this::VERSION);
            $this->send_message($content);
        }

        $phone_number = $this->check_if_message_is_contact_share($json_obj);

        if($phone_number === false){
            $address = $this->check_if_user_is_stored_in_address($chat_id);
            if($address === false)
                $content = $this->get_contact_request_content($chat_id);
            else{
                $mobile = $address[0]['extra'];
                $content = $this->get_message_conten_from_system($chat_id, $mobile);
            }
                
        }
        else{
            $this->save_address($chat_id, $phone_number);
            $content = $this->get_message_conten_from_system($chat_id, $phone_number);
        }
        
        $this->send_message($content);
        
        return '{"return_message":"'.$content.'", "inputJSON":"'.$inputJSON.'", "Date":'.date("Y-m-d H:i:s").'"}';
    }

    
    public function save_address($chat_id, $phone_number){
        $addressModel = model('App\Models\AddressModel');

        $data = [
            'address' => $chat_id,
            'type'=> 'Telegram',
            'extra'=> $phone_number
        ];
        $addressModel->insert($data);
    }
    public function get_reply_message($mobile){
        $communicationModel = model('App\Models\CommunicationModel');
        $communication = $communicationModel->where('type','Telegram')->where('address', $mobile)->findAll();
        $communication = $communication[0] ?? ["message"=>""];
        
        return $communication["message"];
    }
    private function check_if_message_is_contact_share($json_obj){
        $from_id = $json_obj['message']['from']['id'] ?? -1;
        $contact_user_id = $json_obj['message']['contact']['user_id'] ?? -2;
        $phone_number = $json_obj['message']['contact']['phone_number'] ?? -1;
        
        if($from_id == $contact_user_id)
            return $phone_number;

        return false;
    }

    private function check_if_user_is_stored_in_address(int $telegram_id){
        $addressModel = model('App\Models\AddressModel');
        $address = $addressModel->where('type','Telegram')->where('address',$telegram_id)->findAll();
        if(!empty($address))
            return $address;
        return false;
    }

    private function create_message_content($chat_id, $message) {
        $reply_markup = ["remove_keyboard"=> true];
        
        $content = http_build_query(array(
            'chat_id' => $chat_id,
            'text' => $message,
            "parse_mode"=> "Markdown",
            "reply_markup" => $reply_markup
        ));

        return $content;
    }

    private function get_message_conten_from_system($chat_id, $mobile){
        $reply_message = $this->get_reply_message($mobile);
        if(trim($reply_message) != '')
            $content = $this->create_message_content($chat_id,$reply_message);
        else
            $content = $this->create_message_content($chat_id,'Hello, I cannot find documents to share with you. Please contact the receptionist and get back to me.');
        return $content;
    }
    private function get_contact_request_content($chat_id) {
        $message = "It seams you are new to this bot.\n Please share your contact.";

        $reply_markup = [
            "one_time_keyboard"=> true,
            "keyboard"=> [[['text'=>'Share phone number', 'request_contact' => true]]]
                    ];

        $reply_markup = json_encode($reply_markup);

        $content = http_build_query(array(
            'chat_id' => $chat_id,
            'text' => $message,
            "parse_mode"=> "Markdown",
            "reply_markup" => $reply_markup
        ));

        return $content;
    }

    private function send_message($content)
    {
        $ch = curl_init();

        /////////////////////// LOG   ///////////////
        $verbose_file = fopen($this::API_BASE_URL."/log/log.txt",'a');

        $log_text = "\n------------------------------------------------------------------\n";
        $log_text .= date("Y-m-d H:i:s")."\n";
        fwrite($verbose_file,$log_text);
        ////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////

        $url = 'https://api.telegram.org/bot'.$this::TELEGRAM_API_TOKEN.'/sendMessage';
        //$url = 'http://localhost/bot'.API_TOKEN.'/sendMessage';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36");
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER,array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        
        curl_setopt($ch, CURLOPT_STDERR, $verbose_file);        

        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        
        $result = curl_exec($ch);

        curl_close($ch);
        fwrite($verbose_file, "\n-----\n" . $content . "\n-----\n");
        fclose($verbose_file);
    }

    private function write_log($file_name, $log_text)
    {
        try{
            $handl = fopen($this::API_BASE_URL."/log/$file_name", 'a');
        }
        catch (\Exception $e){
            $handl = fopen($this::API_BASE_URL."/log/$file_name", 'w');
        }

        $text = "\n-----------------------------------------------------------------\n";
        $text .= date("Y-m-d H:i:s")."\n";
        $text .= $log_text."\n";

        fwrite($handl,$text);
        fclose($handl);
    }
}
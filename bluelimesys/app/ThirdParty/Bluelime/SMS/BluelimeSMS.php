<?php

namespace App\ThirdParty\Bluelime\SMS;

use BluelimeSystem;

class BluelimeSMS extends BluelimeJasmin
{
    public static function index(): string
    {
        return "I am using <b>" . parent::index() ."</b>";
    }

    public static function fetch_incoming_sms(): array
    {
        $incoming_sms = parent::fetch_incoming_sms();
        BluelimeSystem::write_bluelime_debug_log("Bluelime/SMS","incoming_sms", $incoming_sms, "Incoming SMS");
        return $incoming_sms;
    }

    public static function fetch_incoming_delivery_report(): array
    {
        $incoming_sms = parent::fetch_incoming_delivery_report();
        BluelimeSystem::write_bluelime_debug_log("Bluelime/SMS","delivery_report", $incoming_sms, "Delivery Report");
        return $incoming_sms;
    }

    public static function send_sms(array $to_list, string $text):array
    {
        if(BluelimeSMSConfig::BLUELIME_SMS_DRIVER == BluelimeSMSDriver::RELAY_01)
        {
            self::send_sms_via_relay($to_list, $text);
            return [true];
        }

        $valid_mobile = self::create_valid_mobile_list($to_list);
        $sms_data = parent::create_sms_data($text);

        $result_list = [];
        foreach($valid_mobile as $mobile)
        {
            $sms_data['to'] = $mobile;
            $result = parent::send_single($sms_data, original_text: $text);
            $sms_id = '';
            //$result = {"data": "Success \"ddeafe86-1f60-4ba6-ac38-ca411da9b6e7"}
            if($result)
                if(substr($result,0,17) == '{"data": "Success')
                    $sms_id = substr($result, 20, 36);

            array_push($result_list, ['to'=>$mobile, 'sms_id'=>$sms_id]);
        }
        return $result_list;
    }

    private static function send_sms_via_relay(array $to_list, string $text)
    {
        $signature = $to_list[0].','.$text.','.date('Y-m-d').',BluelimePlc';
        $signature_md5 = md5($signature);
        $sms_data = [
            'mobile' => $to_list[0],
            'message' => $text,
            'signature' => $signature_md5
        ];
        //BluelimeSystem::show_and_die($signature);

        if(BluelimeSystem::bluelime_debug_is_on())
            $verbose_file = BluelimeSystem::write_log('Bluelime/SMS','outgoing_sms','','CURL',true,true);
        
        try
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, BluelimeSMSConfig::BLUELIME_SMS_RELAY_URL);
            //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36");

            //curl_setopt($ch, CURLOPT_HEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            //curl_setopt($ch, CURLOPT_USERPWD, BluelimeSMSConfig::BLUELIME_SMS_USERNAME.':'.BluelimeSMSConfig::BLUELIME_SMS_PASSWORD);
            //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

            $sms_json_string = json_encode($sms_data);

            curl_setopt($ch, CURLOPT_POSTFIELDS,$sms_data);

            
            //Debug 
            if(BluelimeSystem::bluelime_debug_is_on())
            {
                $log = "\nsms_json_string\n".$sms_json_string."\n\n";
                $log .= "* * * Sending Via relay * * *\n\n";
                fwrite($verbose_file,$log);

                curl_setopt($ch, CURLOPT_VERBOSE, true);
                curl_setopt($ch, CURLOPT_STDERR, $verbose_file);
            }

            $result = curl_exec($ch);
            curl_close($ch);
            
            if(BluelimeSystem::bluelime_debug_is_on())
            {
                $log = "\nResult\n".$result."\n";
                fwrite($verbose_file,$log);
            }
        } 
        catch(\Exception $e)
        {
            $result = $e->getMessage();
            
            if(BluelimeSystem::bluelime_debug_is_on())
            {
                $log = "\n\Error\n".$result."\n";
                fwrite($verbose_file,$log);
            }
        }
        
        if(BluelimeSystem::bluelime_debug_is_on())
            fclose($verbose_file);

        return $result;
    }

    public static function send_test_sms(array $to_list):array
    {
        $valid_mobile = self::create_valid_mobile_list($to_list);
        $sms_data = parent::create_sms_data('MSG NO ');

        $cnt = 1;
        $result_list = [];
        foreach($valid_mobile as $mobile)
        {
            $sms_data['content'] = ' --MSH NO ' . $cnt++ . ' -- ';
            $sms_data['to'] = $mobile;
            $result = parent::send_single($sms_data);
            array_push($result_list, ['to'=>$mobile, 'sms_id'=>$result]);
        }
        return $result_list;
    }


    private static function create_valid_mobile_list(array $mobile_list)
    {
        $valid_mobile_list = array();
        if(is_array($mobile_list))
        {
            foreach($mobile_list as $mobile)
            {
                if(self::is_mobile_not_valide($mobile))
                    array_push($valid_mobile_list, $mobile);
            }
        }
        return $valid_mobile_list;
    }

    private static function is_mobile_not_valide(string $mobile) : bool
    {
        if(strlen($mobile) === 10)
            if(is_numeric($mobile) && ((substr($mobile,0,2) === '09') || (substr($mobile,0,2) === '07')))
                return true;
        return false;
    }
    

}
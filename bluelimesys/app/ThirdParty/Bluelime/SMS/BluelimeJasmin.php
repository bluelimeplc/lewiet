<?php

namespace App\ThirdParty\Bluelime\SMS;

use BluelimeSystem;

class BluelimeJasmin
{
    protected static function index(): string 
    {
        return "Bluelime Jasimin connector 2.0.1";
    }

    protected static function fetch_incoming_sms()
    {
        $content = file_get_contents('php://input');
        $content = BluelimeSystem::convert_to_array($content);
        return $content;
    }

    protected static function fetch_incoming_delivery_report():array
    {
        $content = file_get_contents('php://input');
        $content = BluelimeSystem::convert_to_array($content);
        return $content;
    }

    protected static function send_single(array $sms_data, $original_text = '')
    {
        //Debug 
        if(BluelimeSystem::bluelime_debug_is_on())
            $verbose_file = BluelimeSystem::write_log('Bluelime/SMS','outgoing_sms','','CURL',true,true);
        
        try
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, BluelimeSMSConfig::BLUELIME_SMS_GATEWAY_SECURE."/secure/send");
            //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36");

            //curl_setopt($ch, CURLOPT_HEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_USERPWD, BluelimeSMSConfig::BLUELIME_SMS_USERNAME.':'.BluelimeSMSConfig::BLUELIME_SMS_PASSWORD);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

            $sms_json_string = json_encode($sms_data);

            curl_setopt($ch, CURLOPT_POSTFIELDS,$sms_json_string);

            //Debug 
            if(BluelimeSystem::bluelime_debug_is_on())
            {
                $log = "\nsms_json_string\n".$sms_json_string."\n\n";
                $log .= "* * * Original text * * *\n\n".$original_text."\n\n* * * * * * * * * * * * * * * * * * * * * * \n\n";
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

    protected static function create_sms_data(string $message)
    {
        $coding = 1;
        $content = 'content';
        $encoding = self::get_message_encoding_type($message);

        $encoded_message = self::encode_message($message, $encoding);

        if($encoding === 'UCS2')
        {
            $coding = 8;
            $content = 'hex_content';
        }
        //$data['from']       = BluelimeSMSConfig::BLUELIME_SMS_FROM;
        $data['to']         = "";
        $data[$content]     = $encoded_message;
        $data['coding']     = $coding;
        // $data['dlr']        = "yes";
        // $data['dlr-url']    = BluelimeSMSConfig::BLUELIME_SMS_DLR_URL;
        // $data['dlr-method'] = 'POST';
        // $data['dlr-level']  = 2;
        // $data['validity-period'] = BluelimeSMSConfig::BLUELIME_SMS_VALIDITY_PERIOD;

        return $data;
    }

    private static function get_message_encoding_type(string $message)
    {
        // $sms_unicode = array("|","^","{","}","€","\\","[","~","]","_");

        // $char_array = \preg_split('//u', $message, -1, PREG_SPLIT_NO_EMPTY);
        // foreach($char_array as $char)
        // {
        //     if(\mb_ord($char,"utf-8") > 255)
        //         return 'UCS2';
        //     foreach($sms_unicode as $especial_char)
        //     {
        //         if($char === $especial_char)
        //             return 'UCS2';
        //     }
        // }
        //return 'SMSC';
        //Always unicode
        return 'UCS2';
    }

    private static function encode_message(string $message, string $encoding)
    {
        $encoded_message = "";
        //UTF-8
        if($encoding === 'UCS2')
        {
            $encoded_message = mb_convert_encoding($message,'UTF-16','UTF-8');
            $encoded_message = \bin2hex($encoded_message);
        }
        else
        {
            $encoded_message = $message;
        }
        return $encoded_message;
    }
}
<?php

namespace App\Controllers\Tools;

use Exception;

class SMS extends \App\Controllers\BaseController
{
    public function index(){
        $url = "http://192.168.1.20:9090/";

        $client = \Config\Services::curlrequest();

        if(!is_dir(WRITEPATH."logs/Tools"))
            mkdir(WRITEPATH."logs/Tools",0777,true);
        
        $log_file_path = WRITEPATH."logs/Tools/sms_server_test.txt";
        
        $data['status'] = '';
        $data['log'] = '';

        $file_name = "sms" . '_' . date('Y-m-d') . '.txt';   
        try{
            $log = file_get_contents(WRITEPATH."logs/Telegram/$file_name");
            $data['log'] = $log;
        }
        catch(Exception $e){
            $data['log'] = $e->getMessage();
        }

        try{
            $fl = fopen($log_file_path,'w');
            fclose($fl);

            $response = $client->request('GET', $url, ['debug' => $log_file_path, 'http_errors' => false]);

            $body = $response->getBody();

            if(str_replace("\r","", str_replace("\n","",$body)) == '<html><body>Welcome to SMS Gateway<br/></body></html>')
                $data['status'] .= '<div style="height:20px;width:20px; background-color:green;"></div>';
            else
                $data['status'] .= '<div style="height:20px;width:20px; background-color:orange;"></div>';

            $data['status'] .= "URL: $url<br>\n";
            $data['status'] .= $body;

            foreach ($response->headers() as $name => $value) {
                $data['status'] .= $name . ': ' . $response->getHeaderLine($name) . "<br>\n";
            }
            
        }
        catch(Exception $e){
            $data['status'] .= '<div style="height:20px;width:20px; background-color:red;"></div>';
            $data['status'] .= "URL: $url<br>\n";
            $data['status'] .= $e->getMessage();
        }
        finally{
            $data['status'] .= "<br><br>\n\n";
            $data['status'] .= str_replace("\n","\n<br>", file_get_contents($log_file_path));
        }

        return  view('Tools/include/header')
                .view('Tools/SMS/status', $data)
                .view('Tools/include/footer');

    }

    public function test_sms(){
        $mobile = $_POST['mobile'] ?? '';
        $message = $_POST['message'] ?? '';
        $branch = $_ENV['BRANCH']??"";

        $data['result'] = '';
        if(!empty(trim($mobile)) && !empty(trim($message))){
            $result = \BluelimeSystem::send_sms_message($mobile, $message." ".$branch);
            if($result !== false){
                $data['result'] = str_replace("\n","\n<br>",$result);
            }
            else{
                $data['result'] = "Message is not sent.";
            }
        }

        return view('Tools/include/header', $data)
                .view('Tools/SMS/test_sms')
                .view('Tools/include/footer');
    }

    public function run_reboot(){
        echo '<pre>';
        $command = [
            'ECHO OFF',
            'ECHO Runing ADB SHELL REBOOT',
            'ECHO . . . .',
            'CD D:\SW\platform-tools && adb shell reboot',
            'ECHO:',
            'ECHO Done.'
        ];

        foreach($command as $cmd)
            echo shell_exec($cmd);
        echo '</pre>';
    }

    public function check_adb_is_alive(){
        echo '<pre>';
        $command = [
            'ECHO Runing adb shell ifconfig',
            'ECHO . . . .',
            'CD D:\SW\platform-tools && adb shell ifconfig',
            'ECHO:',
            'ECHO Done.'
        ];

        foreach($command as $cmd)
            echo shell_exec($cmd);
        echo '</pre>';
    }

    public function adb_kill_server(){
        echo '<pre>';
        $command = [
            'ECHO Runing adb kill-server',
            'ECHO . . . .',
            'CD D:\SW\platform-tools && adb kill-server',
            'ECHO:',
            'ECHO Done.'
        ];

        foreach($command as $cmd)
            echo shell_exec($cmd);
        echo '</pre>';
    }

    public function system_update(){
        echo '<pre>';
        $command = [
            'ECHO OFF',
            'ECHO Updating System',
            'ECHO . . . .',
            'ssh bluelime@rank.local',
            'ECHO:',
            'pass',
            'ECHO:',
            'sh gitpull.sh',
            'ECHO:',
            'ECHO Done.'
        ];

        foreach($command as $cmd)
            echo shell_exec($cmd);
        echo '</pre>';
    }
}
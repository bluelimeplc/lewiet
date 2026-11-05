<?php

namespace App\Controllers\Tools;

use BluelimePOS;
use BluelimeSystem;

class Index extends \App\Controllers\BaseController
{
    public function index(){
        return  view('Tools/include/header')
                .view("Tools/index")
                .view('Tools/include/footer');
    }

    public function dashboard(){
        return view('Tools/sample');
    }

    public function temp(){
        return  view('Tools/template/NiceAdmin/index');
    }
    
    public function phpinfo(){
        return phpinfo();
    }
    
    public function servervariable(){
        BluelimeSystem::print_associated_array($_SERVER);
        return '';
    }

    public function logs() {
        
        echo WRITEPATH."logs/";

        $file_name_list = $this->read_file_list(path: WRITEPATH."logs/");

        //BluelimeSystem::print_associated_array($file_name_list);

        $data = [
            'file_name_list' => [$file_name_list]
        ];
        return view("Tools/logs", data: $data);
    }

    private function read_file_list($path): array{
        $base = basename($path);
        $file_name_list = ['name'=>$base, 'type'=>'dir', 'children' =>[]];
        $dir = dir($path);
        while($file_name = $dir->read()){
            if(($file_name == '.') || ($file_name == '..'))
                continue;
            
            if(is_dir($path . "/$file_name"))
                array_push($file_name_list['children'],  $this->read_file_list($path . "/$file_name"));
            else
                array_push($file_name_list['children'], ['name'=>$file_name, 'type'=>'file', 'path' => $path]);
        }
        $file_name_list['children'] = BluelimeSystem::sort_array($file_name_list['children'], ['name','type'],true);
        return $file_name_list;
    }

    public function read_log_content(){
        $file_name = $_POST['file_name'];

        $contetn = file_get_contents($file_name);
        echo $contetn;
        die();
    }



}
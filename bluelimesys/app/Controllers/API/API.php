<?php

namespace App\Controllers\API;

class API extends \App\Controllers\BaseController
{
    public function index(){
        return "Welcome to api.";
    }
    public function qrcode(){
        $data = $_GET['data'];
        $eclevel = $_GET['eclevel'] ?? 0;
        $size = $_GET['size'] ?? 3;
        $margin = $_GET['margin'] ?? 4;

        if($eclevel > 3)
            $eclevel = 3;

        include_once(APPPATH.'/ThirdParty/phpqrcode/qrlib.php');
        \QRcode::png(text: $data, level: $eclevel, size: $size,margin: $margin);
        die();
    }
    
}
<?php

namespace App\ThirdParty\Bluelime\POS;

use App\ThirdParty\Bluelime\POS\Maraki\MarakiPOS;
use App\ThirdParty\Bluelime\POS\PEDS\PEDSPOS;

class BluelimePOSMachine
{
    private static $posMachine = null;

    private static function init_pos_type(String $pos_type){
        if($pos_type == 'PEDS')
            self::$posMachine = new PEDSPOS();
        else
            self::$posMachine = new MarakiPOS();
    }

    public static function send_invoice_to_pos(array $pos_bill_json, array $pos_machine):bool{
        if(!($pos_machine['active']??0))
            return true;
        self::init_pos_type($pos_machine['type']);
        return self::$posMachine->send_invoice_to_pos($pos_bill_json, $pos_machine);
    }

    public static function void_invoice(string $invoice_number, array $pos_machine):bool{
        if(!($pos_machine['active']??0))
            return true;
        self::init_pos_type($pos_machine['type']);
        return self::$posMachine->void_invoice($invoice_number, $pos_machine);
    }

    public static function read_response(array $pos_machine){
        if(!($pos_machine['active']??0))
            return [];
        self::init_pos_type($pos_machine['type']);
        return self::$posMachine->read_response($pos_machine);
    }

    public static function acknowledge_response(array $acknowledge_list,array $pos_machine){
        if(!($pos_machine['active']??0))
            return true;
        self::init_pos_type($pos_machine['type']);
        return self::$posMachine->acknowledge_response($acknowledge_list, $pos_machine);
    }
}
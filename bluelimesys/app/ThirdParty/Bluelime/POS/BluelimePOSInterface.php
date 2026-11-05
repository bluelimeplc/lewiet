<?php

namespace App\ThirdParty\Bluelime\POS;

interface BluelimePOSInterface
{
    /**
     * Create nesesary files and informations for the POS machine
     * @param array $pos_bill
     * @param array $extra
     * @return bool
     */
    public function send_invoice_to_pos(array $pos_bill_json, array $pos_machine):bool;

    /**
     * Resend the pos
     * @param array $pos_bill
     * @return bool
     */
    public function resend_invoice_to_pos(array $pos_bill_json, array $pos_machine):bool;
    
    public function void_invoice(string $invoice_number, array $pos_machine):bool;
    public function update_invoice(array $pos_bill_json, array $pos_machine):bool;
    public function read_response(array $pos_machine);
    public function acknowledge_response(array $acknowledge_list, array $pos_machine);
}
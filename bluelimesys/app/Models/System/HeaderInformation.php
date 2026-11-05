<?php

namespace App\Models\System;

use CodeIgniter\Model;

class HeaderInformation extends Model
{
    protected $table      = 'sys_header_information';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['company_name', 'mobile', 'tel', 'email', 'tin_no', 'location', 'logo', 'logo_small', 'active', 'logo_printouts'];
    

}
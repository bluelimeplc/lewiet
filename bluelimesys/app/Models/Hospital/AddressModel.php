<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class AddressModel extends Model
{
    protected $table      = 'hospital_address';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['address','type','extra'];

}
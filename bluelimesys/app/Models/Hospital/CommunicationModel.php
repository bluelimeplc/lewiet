<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class CommunicationModel extends Model
{
    protected $table      = 'hospital_communication';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['type','direction','address','message','extra'];

}
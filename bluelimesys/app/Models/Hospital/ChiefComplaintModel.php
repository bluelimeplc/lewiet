<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class ChiefComplaintModel extends Model
{
    protected $table      = 'hospital_chief_complaints';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['name', 'description'];
}

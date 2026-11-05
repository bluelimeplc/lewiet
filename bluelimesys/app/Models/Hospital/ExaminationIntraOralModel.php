<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class ExaminationIntraOralModel extends Model
{
    protected $table      = 'hospital_examination_intra_oral';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['name', 'description'];
}

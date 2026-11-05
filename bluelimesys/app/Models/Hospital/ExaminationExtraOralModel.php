<?php

namespace App\Models\Hospital;

use CodeIgniter\Model;

class ExaminationExtraOralModel extends Model
{
    protected $table      = 'hospital_examination_extra_oral';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['name', 'description'];
}

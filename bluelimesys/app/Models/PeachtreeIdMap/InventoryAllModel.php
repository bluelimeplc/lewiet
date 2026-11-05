<?php

namespace App\Models\PeachtreeIdMap;

use CodeIgniter\Model;

class InventoryAllModel extends Model
{
    protected $table      = '_peachtree_items_all';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    //  protected $useSoftDeletes = true;

    protected $allowedFields = ['peachtree_code','peachtree_discription','location','bluelime_code', 'remark'];
}
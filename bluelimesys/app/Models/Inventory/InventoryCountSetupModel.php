<?php

namespace App\Models\Inventory;

use CodeIgniter\Model;

class InventoryCountSetupModel extends Model
{
    protected $table      = 'inventory_inventory_count_setup';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'title',
        'is_year_end',
        'from_date',
        'to_date',
        'active',
        'year_end_committed'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
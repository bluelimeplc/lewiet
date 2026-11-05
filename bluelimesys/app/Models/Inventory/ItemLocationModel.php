<?php

namespace App\Models\Inventory;

use CodeIgniter\Model;

class ItemLocationModel extends Model
{
    protected $table      = 'inventory_item_location';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'item_id',
        'store_id',
        'location',
        'registered_by',
        'registered_by_name'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
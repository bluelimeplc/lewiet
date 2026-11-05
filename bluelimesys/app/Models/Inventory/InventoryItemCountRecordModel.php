<?php

namespace App\Models\Inventory;

use CodeIgniter\Model;

class InventoryItemCountRecordModel extends Model
{
    protected $table      = 'inventory_item_count_record';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'inventory_count_id',
        'store_id',
        'item_id',
        'quantity',
        'unit_cost',
        'unit_price',
        'expiry_date',
        'remark'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
<?php

namespace App\Models\Inventory;

use CodeIgniter\Model;

class PeachtreeCustomerModel extends Model
{
    protected $table      = 'inventory_peachtree_customers';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['customer_id', 'name'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPriorityToWaitingList extends Migration
{
    public function up()
    {
        $fields = [
            'priority_reason' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'default' => null,
            ],
            'priority_level' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 0,
            ],
        ];

        $this->forge->addColumn('hospital_waiting_list', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('hospital_waiting_list', ['priority_reason', 'priority_level']);
    }
}

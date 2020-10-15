<?php

declare(strict_types=1);

namespace Ravisdo\Bot\Migrations;

use Phinx\Migration\AbstractMigration;

class CreateBotLogEntriesTable extends AbstractMigration
{

    public function up(): void
    {
        $table = $this->table('bot_log_entries');
        $table
            ->addColumn('message', 'text')
            ->addColumn('context', 'text', ['null' => true])
            ->addColumn('level', 'smallinteger')
            ->addColumn('level_name', 'string', ['limit' => 192])
            ->addColumn('channel', 'string', ['limit' => 192])
            ->addColumn('record_datetime', 'string', ['limit' => 192])
            ->addColumn('extra', 'string', ['limit' => 192, 'null' => true])
            ->addColumn('formatted', 'text')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->create();
    }

    public function down(): void
    {
        $this->table('bot_log_entries')->drop();
    }
}

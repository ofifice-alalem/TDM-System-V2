<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateTablesCommand extends Command
{
    protected $signature = 'db:truncate-except-users';
    protected $description = 'إفراغ جميع الجداول باستثناء roles و users';

    public function handle()
    {
        if (!$this->confirm('هل أنت متأكد من إفراغ جميع الجداول (ما عدا roles و users)؟')) {
            $this->info('تم الإلغاء');
            return;
        }

        $excludedTables = ['roles', 'users', 'migrations'];
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $tableKey = "Tables_in_{$dbName}";

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            
            if (!in_array($tableName, $excludedTables)) {
                DB::table($tableName)->truncate();
                $this->info("تم إفراغ جدول: {$tableName}");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('تم إفراغ جميع الجداول بنجاح!');
    }
}

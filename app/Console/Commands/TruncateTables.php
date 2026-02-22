<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateTables extends Command
{
    protected $signature = 'db:truncate-tables';
    protected $description = 'إفراغ جميع الجداول باستثناء roles و users';

    public function handle()
    {
        $excludedTables = ['roles', 'users', 'migrations'];
        
        Schema::disableForeignKeyConstraints();
        
        $tables = DB::select('SHOW TABLES');
        $dbName = 'Tables_in_' . env('DB_DATABASE');
        
        foreach ($tables as $table) {
            $tableName = $table->$dbName;
            
            if (!in_array($tableName, $excludedTables)) {
                DB::table($tableName)->truncate();
                $this->info("تم إفراغ جدول: {$tableName}");
            }
        }
        
        Schema::enableForeignKeyConstraints();
        
        $this->info('تم إفراغ جميع الجداول بنجاح!');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class BackupController extends Controller
{
    public function create()
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        
        $backupName = 'backup_' . date('Y-m-d_H-i-s');
        $sqlFile = storage_path("app/{$backupName}.sql");
        $zipFile = storage_path("app/backups/{$backupName}.zip");
        
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }
        
        // تصدير قاعدة البيانات
        $sql = "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        foreach (DB::select('SHOW TABLES') as $table) {
            $tableName = array_values((array)$table)[0];
            
            // Structure
            $create = DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $create->{'Create Table'} . ";\n\n";
            
            // Data
            DB::table($tableName)->orderBy(DB::raw('1'))->chunk(500, function($rows) use (&$sql, $tableName) {
                foreach ($rows as $row) {
                    $values = collect((array)$row)->map(function($v) {
                        if (is_null($v)) return 'NULL';
                        return "'" . str_replace("'", "''", $v) . "'";
                    })->implode(',');
                    
                    $sql .= "INSERT INTO `{$tableName}` VALUES ({$values});\n";
                }
            });
            
            $sql .= "\n";
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        file_put_contents($sqlFile, $sql);
        
        // ضغط كل شيء
        $zip = new ZipArchive;
        $zip->open($zipFile, ZipArchive::CREATE);
        
        // إضافة SQL
        $zip->addFile($sqlFile, 'database.sql');
        
        // إضافة الملفات
        $this->addDirectory(storage_path('app/public'), $zip, 'storage');
        
        $zip->close();
        unlink($sqlFile);
        
        return response()->download($zipFile);
    }
    
    public function index()
    {
        $backups = collect(glob(storage_path('app/backups/*.zip')))
            ->map(function($file) {
                return [
                    'name' => basename($file),
                    'size' => $this->formatBytes(filesize($file)),
                    'date' => date('Y-m-d H:i:s', filemtime($file)),
                    'path' => $file
                ];
            })
            ->sortByDesc('date')
            ->values();
        
        return view('admin.backups.index', compact('backups'));
    }
    
    public function restore($filename)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        
        $zipFile = storage_path("app/backups/{$filename}");
        
        if (!file_exists($zipFile)) {
            return back()->with('error', 'الملف غير موجود');
        }
        
        $extractPath = storage_path('app/restore_temp');
        
        // استخراج الملفات
        $zip = new ZipArchive;
        $zip->open($zipFile);
        $zip->extractTo($extractPath);
        $zip->close();
        
        // استعادة قاعدة البيانات
        $sqlFile = $extractPath . '/database.sql';
        if (file_exists($sqlFile)) {
            DB::unprepared(file_get_contents($sqlFile));
        }
        
        // استعادة الملفات
        $storagePath = $extractPath . '/storage';
        if (is_dir($storagePath)) {
            $this->copyDirectory($storagePath, storage_path('app/public'));
        }
        
        // تنظيف
        $this->deleteDirectory($extractPath);
        
        return back()->with('success', 'تم استعادة النسخة الاحتياطية بنجاح');
    }
    
    public function download($filename)
    {
        $file = storage_path("app/backups/{$filename}");
        
        if (!file_exists($file)) {
            return back()->with('error', 'الملف غير موجود');
        }
        
        return response()->download($file);
    }
    
    public function delete($filename)
    {
        $file = storage_path("app/backups/{$filename}");
        
        if (file_exists($file)) {
            unlink($file);
        }
        
        return back()->with('success', 'تم حذف النسخة الاحتياطية');
    }
    
    private function addDirectory($path, $zip, $base)
    {
        if (!is_dir($path)) return;
        
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $base . '/' . substr($filePath, strlen($path) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
    
    private function copyDirectory($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst, 0755, true);
        
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    $this->copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        
        closedir($dir);
    }
    
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) return;
        
        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        
        rmdir($dir);
    }
    
    private function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class BackupController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'note' => 'nullable|string|max:500'
        ]);
        
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        
        $type = $request->input('type', 'full');
        $backupName = 'backup_' . date('Y-m-d_H-i-s') . '_' . $type;
        $sqlFile = storage_path("app/{$backupName}.sql");
        $backupDir = storage_path("app/backups/{$type}");
        $zipFile = "{$backupDir}/{$backupName}.zip";
        
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $zip = new ZipArchive;
        $zip->open($zipFile, ZipArchive::CREATE);
        
        // Add note if provided
        if ($request->filled('note')) {
            $zip->addFromString('note.txt', $request->input('note'));
        }
        
        // Database backup
        if ($type === 'full' || $type === 'database') {
            $sql = "SET FOREIGN_KEY_CHECKS=0;\nSET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO';\n\n";
            
            foreach (DB::select('SHOW TABLES') as $table) {
                $tableName = array_values((array)$table)[0];
                
                $create = DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $create->{'Create Table'} . ";\n\n";
                
                $insertValues = [];
                DB::table($tableName)->orderBy(DB::raw('1'))->chunk(500, function($rows) use (&$insertValues, $tableName, &$sql) {
                    foreach ($rows as $row) {
                        $values = collect((array)$row)->map(function($v) {
                            if (is_null($v)) return 'NULL';
                            return "'" . str_replace("'", "''", $v) . "'";
                        })->implode(',');
                        
                        $insertValues[] = "({$values})";
                    }
                    
                    if (!empty($insertValues)) {
                        $sql .= "INSERT INTO `{$tableName}` VALUES " . implode(',', $insertValues) . ";\n";
                        $insertValues = [];
                    }
                });
                
                $sql .= "\n";
            }
            
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            file_put_contents($sqlFile, $sql);
            $zip->addFile($sqlFile, 'database.sql');
        }
        
        // Files backup
        if ($type === 'full' || $type === 'files') {
            $this->addDirectory(storage_path('app/public'), $zip, 'storage');
        }
        
        $zip->close();
        if (file_exists($sqlFile)) unlink($sqlFile);
        
        return response()->download($zipFile);
    }
    
    public function index()
    {
        set_time_limit(120);
        
        $backups = collect();
        
        foreach (['full', 'database', 'files'] as $type) {
            $dir = storage_path("app/backups/{$type}");
            if (is_dir($dir)) {
                $files = glob("{$dir}/*.zip");
                foreach ($files as $file) {
                    // Read note from zip if exists
                    $note = null;
                    $zip = new ZipArchive;
                    if ($zip->open($file) === TRUE) {
                        if ($zip->locateName('note.txt') !== false) {
                            $note = $zip->getFromName('note.txt');
                        }
                        $zip->close();
                    }
                    
                    $backups->push([
                        'name' => basename($file),
                        'size' => $this->formatBytes(filesize($file)),
                        'date' => date('Y-m-d H:i:s', filemtime($file)),
                        'path' => $file,
                        'type' => $type,
                        'note' => $note
                    ]);
                }
            }
        }
        
        $backups = $backups->sortByDesc('date')->values();
        
        return view('admin.backups.index', compact('backups'));
    }
    
    public function restore($filename)
    {
        set_time_limit(0);
        ini_set('memory_limit', '1G');
        ini_set('max_execution_time', '0');
        
        // البحث عن الملف في المجلدات الثلاثة
        $zipFile = null;
        foreach (['full', 'database', 'files'] as $type) {
            $path = storage_path("app/backups/{$type}/{$filename}");
            if (file_exists($path)) {
                $zipFile = $path;
                break;
            }
        }
        
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
            $this->restoreLargeSQL($sqlFile);
        }
        
        // استعادة الملفات
        $storagePath = $extractPath . '/storage';
        if (is_dir($storagePath)) {
            // مسح الملفات القديمة أولاً
            $publicPath = storage_path('app/public');
            if (is_dir($publicPath)) {
                $this->deleteDirectory($publicPath);
            }
            $this->copyDirectory($storagePath, $publicPath);
        }
        
        // تنظيف
        $this->deleteDirectory($extractPath);
        
        return back()->with('success', 'تم استعادة النسخة الاحتياطية بنجاح');
    }
    
    public function download($filename)
    {
        // البحث عن الملف في المجلدات الثلاثة
        foreach (['full', 'database', 'files'] as $type) {
            $file = storage_path("app/backups/{$type}/{$filename}");
            if (file_exists($file)) {
                return response()->download($file);
            }
        }
        
        return back()->with('error', 'الملف غير موجود');
    }
    
    public function delete($filename)
    {
        // البحث عن الملف في المجلدات الثلاثة
        foreach (['full', 'database', 'files'] as $type) {
            $file = storage_path("app/backups/{$type}/{$filename}");
            if (file_exists($file)) {
                unlink($file);
                return back()->with('success', 'تم حذف النسخة الاحتياطية');
            }
        }
        
        return back()->with('error', 'الملف غير موجود');
    }
    
    public function upload(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip|max:512000'
        ]);
        
        $file = $request->file('backup_file');
        $filename = $file->getClientOriginalName();
        
        // تحديد نوع النسخة من اسم الملف
        $type = 'full';
        if (str_contains($filename, '_database')) {
            $type = 'database';
        } elseif (str_contains($filename, '_files')) {
            $type = 'files';
        }
        
        $backupDir = storage_path("app/backups/{$type}");
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $file->move($backupDir, $filename);
        
        return back()->with('success', 'تم رفع النسخة الاحتياطية بنجاح');
    }
    
    private function addDirectory($path, $zip, $base)
    {
        if (!is_dir($path)) return;
        
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = $base . '/' . substr($filePath, strlen($path) + 1);
            $relativePath = str_replace('\\', '/', $relativePath);
            
            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
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
    
    private function restoreLargeSQL($sqlFile)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('SET SESSION sql_mode="NO_AUTO_VALUE_ON_ZERO"');

        $pdo = DB::getPdo();
        $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

        $sql = file_get_contents($sqlFile);

        // تقسيم الـ SQL إلى statements منفصلة بشكل صحيح
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = '';
        $len = strlen($sql);

        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];

            if ($inString) {
                $current .= $char;
                if ($char === $stringChar && ($i === 0 || $sql[$i - 1] !== '\\')) {
                    $inString = false;
                }
            } elseif ($char === "'" || $char === '"') {
                $inString = true;
                $stringChar = $char;
                $current .= $char;
            } elseif ($char === ';') {
                $current = trim($current);
                if ($current !== '') {
                    $statements[] = $current;
                }
                $current = '';
            } else {
                $current .= $char;
            }
        }

        if (trim($current) !== '') {
            $statements[] = trim($current);
        }

        foreach ($statements as $statement) {
            if (empty(trim($statement))) continue;
            try {
                $pdo->exec($statement);
            } catch (\Exception $e) {
                \Log::error('Restore SQL error: ' . $e->getMessage() . ' | Statement: ' . substr($statement, 0, 200));
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}

<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupController extends Controller
{
    protected $backupPath = 'backups';

    public function index()
    {
        // Ensure directory exists
        if (!Storage::exists($this->backupPath)) {
            Storage::makeDirectory($this->backupPath);
        }

        $files = Storage::files($this->backupPath);
        $backups = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $backups[] = [
                    'name' => basename($file),
                    'size' => $this->formatBytes(Storage::size($file)),
                    'date' => Carbon::createFromTimestamp(Storage::lastModified($file))->format('d M Y - H:i'),
                    'status' => 'Sukses',
                    'raw_date' => Storage::lastModified($file)
                ];
            }
        }

        // Sort by date latest
        usort($backups, fn($a, $b) => $b['raw_date'] <=> $a['raw_date']);

        $lastBackup = !empty($backups) ? $backups[0]['date'] . ' WIB' : 'Belum pernah';
        
        // Calculate storage: Current Database Size vs 3GB
        $dbPath = database_path('database.sqlite');
        $dbSize = File::exists($dbPath) ? File::size($dbPath) : 0;
        $maxCapacity = 3 * 1024 * 1024 * 1024; // 3GB

        $capacityInfo = [
            'used' => $this->formatBytes($dbSize),
            'total' => '3,0 GB',
            'percent' => round(($dbSize / $maxCapacity) * 100, 2)
        ];

        return view('koordinator.backup', compact('backups', 'lastBackup', 'capacityInfo'));
    }

    public function store()
    {
        try {
            $tables = \DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            $sqlDump = "-- Database Backup\n-- Date: " . now()->toDateTimeString() . "\n\n";
            $sqlDump .= "PRAGMA foreign_keys=OFF;\n";

            foreach ($tables as $table) {
                $tableName = $table->name;
                
                // Get Create Table Statement
                $createTable = \DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$tableName]);
                $sqlDump .= "\n-- Table: $tableName\n";
                $sqlDump .= "DROP TABLE IF EXISTS \"$tableName\";\n";
                $sqlDump .= $createTable->sql . ";\n";

                // Get Data
                $rows = \DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $rowArray = (array)$row;
                    $columns = implode('", "', array_keys($rowArray));
                    $values = array_map(function($value) {
                        if (is_null($value)) return 'NULL';
                        return "'" . str_replace("'", "''", $value) . "'";
                    }, array_values($rowArray));
                    
                    $sqlDump .= "INSERT INTO \"$tableName\" (\"$columns\") VALUES (" . implode(', ', $values) . ");\n";
                }
            }

            $sqlDump .= "\nPRAGMA foreign_keys=ON;";

            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = "Backup_SidangKP_{$timestamp}.sql";
            
            Storage::put($this->backupPath . '/' . $backupName, $sqlDump);

            return back()->with('success', 'Backup database (.sql) berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $path = $this->backupPath . '/' . $filename;
        if (Storage::exists($path)) {
            return Storage::download($path);
        }
        return back()->with('error', 'File tidak ditemukan.');
    }

    public function destroy($filename)
    {
        $path = $this->backupPath . '/' . $filename;
        if (Storage::exists($path)) {
            Storage::delete($path);
            return back()->with('success', 'Backup berhasil dihapus.');
        }
        return back()->with('error', 'Gagal menghapus file.');
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

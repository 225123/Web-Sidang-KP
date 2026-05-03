<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class RunMigrationManual extends Command
{
    protected $signature = 'run:migration-manual';
    protected $description = 'Run manual migration';

    public function handle()
    {
        if (!Schema::hasColumn('mahasiswa', 'tahun_ajaran_id')) {
            Schema::table('mahasiswa', function (Blueprint $table) {
                $table->unsignedBigInteger('tahun_ajaran_id')->nullable();
            });
            
            $activeId = TahunAjaran::where('is_active', true)->value('id');
            if ($activeId) {
                DB::table('mahasiswa')->update(['tahun_ajaran_id' => $activeId]);
            }
            
            $this->info("Migration Success: Added tahun_ajaran_id to mahasiswa");
        } else {
            $this->info("Migration already run");
        }
    }
}

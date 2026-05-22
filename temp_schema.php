<?php
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('pendaftaran_sidang');
$foreign = \Illuminate\Support\Facades\DB::select("PRAGMA foreign_key_list('pendaftaran_sidang')");
$nullable = \Illuminate\Support\Facades\DB::select("PRAGMA table_info('pendaftaran_sidang')");
echo json_encode(compact('columns', 'foreign', 'nullable'), JSON_PRETTY_PRINT);

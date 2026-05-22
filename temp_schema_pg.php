<?php
$nullable = \Illuminate\Support\Facades\DB::select("SELECT column_name, is_nullable FROM information_schema.columns WHERE table_name = 'pendaftaran_sidang' LIMIT 15");
echo json_encode($nullable, JSON_PRETTY_PRINT);

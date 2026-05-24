<?php
$db = DB::connection()->getPdo();

$tablesQuery = $db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE' AND table_name NOT IN ('migrations', 'personal_access_tokens', 'failed_jobs', 'password_reset_tokens')");
$tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

$dbml = "// Use DBML to define your database structure\n// Docs: https://dbml.dbdiagram.io/docs\n\n";

foreach ($tables as $table) {
    $dbml .= "Table {$table} {\n";
    $columnsQuery = $db->prepare("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_schema = 'public' AND table_name = ? ORDER BY ordinal_position");
    $columnsQuery->execute([$table]);
    $columns = $columnsQuery->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        $type = $col['data_type'];
        if (strpos($type, 'timestamp') !== false) $type = 'timestamp';
        else if (strpos($type, 'time without time zone') !== false) $type = 'time';
        else if (strpos($type, 'character varying') !== false) $type = 'varchar';
        else if ($type == 'boolean') $type = 'boolean';
        else if ($type == 'integer') $type = 'integer';
        else if ($type == 'bigint') $type = 'integer'; // Normalized for DBML
        else if ($type == 'smallint') $type = 'integer';
        else if ($type == 'numeric') $type = 'numeric';
        else if ($type == 'text') $type = 'text';
        else if ($type == 'date') $type = 'date';
        else if ($type == 'json') $type = 'json';
        else $type = 'varchar'; // Fallback for unexpected types
        
        $settings = [];
        if (strpos((string)$col['column_default'], 'nextval') !== false || $col['column_name'] === 'id') {
            $settings[] = 'primary key';
        } else if ($col['is_nullable'] == 'NO') {
            $settings[] = 'not null';
        }
        
        $setStr = count($settings) > 0 ? " [" . implode(', ', $settings) . "]" : "";
        $dbml .= "  {$col['column_name']} {$type}{$setStr}\n";
    }
    $dbml .= "}\n\n";
}

$fkQuery = $db->query("
    SELECT
        tc.table_name, kcu.column_name,
        ccu.table_name AS foreign_table_name,
        ccu.column_name AS foreign_column_name
    FROM
        information_schema.table_constraints AS tc
        JOIN information_schema.key_column_usage AS kcu
          ON tc.constraint_name = kcu.constraint_name
          AND tc.table_schema = kcu.table_schema
        JOIN information_schema.constraint_column_usage AS ccu
          ON ccu.constraint_name = tc.constraint_name
          AND ccu.table_schema = tc.table_schema
    WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_schema='public';
");

$fks = $fkQuery->fetchAll(PDO::FETCH_ASSOC);
foreach ($fks as $fk) {
    $dbml .= "Ref: {$fk['table_name']}.{$fk['column_name']} > {$fk['foreign_table_name']}.{$fk['foreign_column_name']}\n";
}

file_put_contents('dbml_export.txt', $dbml);
echo "DBML syntax fully normalized and generated.\n";

<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToArray, WithHeadingRow
{
    public function array(array $array)
    {
        // Data tidak diproses di sini lagi.
        // Data akan diekstrak menggunakan Excel::toArray() di Controller untuk dimasukkan ke session (Preview Feature).
        return $array;
    }
}

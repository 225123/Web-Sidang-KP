<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UsersTemplateExport implements FromArray, ShouldAutoSize, WithEvents, WithHeadings
{
    public function array(): array
    {
        // Menyediakan beberapa baris contoh kosong atau dummy terstruktur
        return [
            ['Joko Susilo', '1234567890', 'joko@ukrida.ac.id', 'Dosen'],
            ['Budi Santoso', '987654321', 'budi@civitas.ukrida.ac.id', 'Mahasiswa'],
            ['', '', '', ''],
            ['', '', '', ''],
            ['', '', '', ''],
        ];
    }

    public function headings(): array
    {
        return [
            'nama',
            'id',
            'email',
            'role',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = 100; // Siapkan hingga baris 100 untuk input

                // 1. Desain Header & Tabel yang Menarik
                $headerStyle = [
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => Color::COLOR_WHITE],
                        'size' => 12,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF4285F4'], // Biru ala Google
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];

                $bodyStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFCCCCCC'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ];

                // Terapkan style
                $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
                $sheet->getStyle('A2:D'.$highestRow)->applyFromArray($bodyStyle);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // Freeze Baris Pertama
                $sheet->freezePane('A2');

                // 2. Data Validation (Dropdown) di Kolom Role (Kolom D)
                for ($row = 2; $row <= $highestRow; $row++) {
                    $validation = $sheet->getCell('D'.$row)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input Error');
                    $validation->setError('Role tidak valid. Silakan pilih dari Dropdown.');
                    $validation->setPromptTitle('Pilih Role');
                    $validation->setPrompt('Silakan pilih salah satu role yang tersedia.');
                    // Pilihan Role
                    $validation->setFormula1('"Koordinator KP,Dosen,Mahasiswa"');
                }
            },
        ];
    }
}

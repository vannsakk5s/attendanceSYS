<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Students');

// Headers
$sheet->setCellValue('A1', 'student_code');
$sheet->setCellValue('B1', 'full_name');

// Sample rows
$sampleData = [
    ['STU-00101', 'Sophea Chan'],
    ['STU-00102', 'Bopha Kim'],
    ['STU-00103', 'Rathana Seng'],
    ['STU-00104', 'Sreymom Meas'],
    ['STU-00105', 'Dara Sok'],
    ['STU-00106', 'Pisey Heng'],
    ['STU-00107', 'Vicheka Roeun'],
    ['STU-00108', 'Kolab Keo'],
    ['STU-00109', 'Samnang Ouk'],
    ['STU-00110', 'Channary Phan'],
];

$row = 2;
foreach ($sampleData as $data) {
    $sheet->setCellValue('A' . $row, $data[0]);
    $sheet->setCellValue('B' . $row, $data[1]);
    $row++;
}

// Styling
$headerStyle = [
    'font' => [
        'name' => 'Arial',
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
        'size' => 11,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4F46E5'], // Indigo-600
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'CBD5E1'],
        ],
    ],
];

$sheet->getStyle('A1:B1')->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(26);

$dataStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'E2E8F0'],
        ],
    ],
    'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];
$sheet->getStyle('A2:B' . ($row - 1))->applyFromArray($dataStyle);

$sheet->getColumnDimension('A')->setWidth(18);
$sheet->getColumnDimension('B')->setWidth(28);

$templateDir = __DIR__ . '/../public/templates';
if (!file_exists($templateDir)) {
    mkdir($templateDir, 0777, true);
}

$targetPath = $templateDir . '/students_import_template.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($targetPath);

// Also copy to root for quick user access
copy($targetPath, __DIR__ . '/../students_import_sample.xlsx');

echo "EXCEL_FILE_SAVED: " . realpath($targetPath) . "\n";

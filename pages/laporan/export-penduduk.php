<?php
session_start();
require '../../vendor/autoload.php';
require_once '../../config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Get parameters
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$wilayah = $_GET['wilayah'] ?? '';

// Build query
$query = "
    SELECT 
        p.*,
        kk.nomor_kk,
        kk.alamat,
        kk.rt_rw,
        w.nama_wilayah
    FROM 
        penduduk p
        LEFT JOIN kartu_keluarga kk ON p.id_kk = kk.id
        LEFT JOIN wilayah w ON kk.id_wilayah = w.id
";

$conditions = [];
$conditions[] = "MONTH(p.dibuat_pada) = '$bulan'";
$conditions[] = "YEAR(p.dibuat_pada) = '$tahun'";

if ($wilayah) {
    $conditions[] = "w.id = " . intval($wilayah);
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY kk.nomor_kk, p.status_keluarga";
$result = mysqli_query($koneksi, $query);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()
    ->setCreator($_SESSION['nama_lengkap'])
    ->setLastModifiedBy($_SESSION['nama_lengkap'])
    ->setTitle('Laporan Data Penduduk')
    ->setSubject('Laporan Data Penduduk Periode ' . $bulan . '/' . $tahun);

// Common styles
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4F46E5'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];

$dataStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'alignment' => [
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];

// Sheet 1: Data Dasar
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('Data Dasar');
$headers1 = [
    'No',
    'No. KK',
    'NIK',
    'Nama Lengkap',
    'Wilayah',
    'RT/RW',
    'Alamat'
];

$sheet1->fromArray([$headers1], NULL, 'A1');
$sheet1->getStyle('A1:G1')->applyFromArray($headerStyle);
$sheet1->freezePane('A2');

// Sheet 2: Data Pribadi
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('Data Pribadi');
$headers2 = [
    'No',
    'NIK',
    'Nama Lengkap',
    'Tempat Lahir',
    'Tanggal Lahir',
    'Jenis Kelamin',
    'Agama'
];

$sheet2->fromArray([$headers2], NULL, 'A1');
$sheet2->getStyle('A1:G1')->applyFromArray($headerStyle);
$sheet2->freezePane('A2');

// Sheet 3: Data Tambahan
$sheet3 = $spreadsheet->createSheet();
$sheet3->setTitle('Data Tambahan');
$headers3 = [
    'No',
    'NIK',
    'Nama Lengkap',
    'Status Perkawinan',
    'Pekerjaan',
    'Status dalam Keluarga',
    'Status Kependudukan'
];

$sheet3->fromArray([$headers3], NULL, 'A1');
$sheet3->getStyle('A1:G1')->applyFromArray($headerStyle);
$sheet3->freezePane('A2');

// Add data to sheets
$row1 = $row2 = $row3 = 2;
$no = 1;

mysqli_data_seek($result, 0);
while ($data = mysqli_fetch_assoc($result)) {
    // Sheet 1: Data Dasar
    $sheet1->setCellValue('A' . $row1, $no);
    $sheet1->setCellValueExplicit('B' . $row1, $data['nomor_kk'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    $sheet1->setCellValueExplicit('C' . $row1, $data['nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    $sheet1->setCellValue('D' . $row1, $data['nama_lengkap']);
    $sheet1->setCellValue('E' . $row1, $data['nama_wilayah']);
    $sheet1->setCellValue('F' . $row1, $data['rt_rw']);
    $sheet1->setCellValue('G' . $row1, $data['alamat']);
    $sheet1->getStyle('A' . $row1 . ':G' . $row1)->applyFromArray($dataStyle);

    // Sheet 2: Data Pribadi
    $sheet2->setCellValue('A' . $row2, $no);
    $sheet2->setCellValueExplicit('B' . $row2, $data['nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    $sheet2->setCellValue('C' . $row2, $data['nama_lengkap']);
    $sheet2->setCellValue('D' . $row2, $data['tempat_lahir']);
    $sheet2->setCellValue('E' . $row2, date('d/m/Y', strtotime($data['tanggal_lahir'])));
    $sheet2->setCellValue('F' . $row2, $data['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan');
    $sheet2->setCellValue('G' . $row2, $data['agama']);
    $sheet2->getStyle('A' . $row2 . ':G' . $row2)->applyFromArray($dataStyle);

    // Sheet 3: Data Tambahan
    $sheet3->setCellValue('A' . $row3, $no);
    $sheet3->setCellValueExplicit('B' . $row3, $data['nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    $sheet3->setCellValue('C' . $row3, $data['nama_lengkap']);
    $sheet3->setCellValue('D' . $row3, $data['status_kawin']);
    $sheet3->setCellValue('E' . $row3, $data['pekerjaan']);
    $sheet3->setCellValue('F' . $row3, $data['status_keluarga']);
    $sheet3->setCellValue('G' . $row3, $data['status']);
    $sheet3->getStyle('A' . $row3 . ':G' . $row3)->applyFromArray($dataStyle);

    $row1++;
    $row2++;
    $row3++;
    $no++;
}

// Auto size columns for all sheets
foreach ($spreadsheet->getAllSheets() as $sheet) {
    foreach (range('A', 'G') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Set print settings
    $sheet->getPageSetup()
        ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
        ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
        ->setFitToWidth(1)
        ->setFitToHeight(0);
}

// Set the header for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan_Penduduk_' . $bulan . '_' . $tahun . '.xlsx"');
header('Cache-Control: max-age=0');

// Save file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

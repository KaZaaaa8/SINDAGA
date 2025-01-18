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
        kk.*,
        w.nama_wilayah,
        (SELECT COUNT(*) FROM penduduk WHERE id_kk = kk.id) as jumlah_anggota
    FROM 
        kartu_keluarga kk
        LEFT JOIN wilayah w ON kk.id_wilayah = w.id
";

$conditions = [];
$conditions[] = "MONTH(kk.dibuat_pada) = '$bulan'";
$conditions[] = "YEAR(kk.dibuat_pada) = '$tahun'";

if ($wilayah) {
    $conditions[] = "w.id = " . intval($wilayah);
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY kk.dibuat_pada DESC";
$result = mysqli_query($koneksi, $query);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Data Kartu Keluarga');

// Set document properties
$spreadsheet->getProperties()
    ->setCreator($_SESSION['nama_lengkap'])
    ->setLastModifiedBy($_SESSION['nama_lengkap'])
    ->setTitle('Laporan Data Kartu Keluarga')
    ->setSubject('Laporan KK Periode ' . $bulan . '/' . $tahun);

// Set headers
$headers = [
    'No',
    'Nomor KK',
    'Kepala Keluarga',
    'Alamat',
    'RT/RW',
    'Wilayah',
    'Jumlah Anggota',
    'Tanggal Dibuat'
];

// Style the header
$sheet->fromArray([$headers], NULL, 'A1');
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
$sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(30);

// Add data
$row = 2;
$no = 1;
while ($data = mysqli_fetch_assoc($result)) {
    // Get Kepala Keluarga
    $query_kepala = "SELECT nama_lengkap FROM penduduk 
                     WHERE id_kk = {$data['id']} 
                     AND status_keluarga = 'KEPALA KELUARGA' 
                     LIMIT 1";
    $kepala = mysqli_fetch_assoc(mysqli_query($koneksi, $query_kepala));

    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValueExplicit('B' . $row, $data['nomor_kk'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
    $sheet->setCellValue('C' . $row, $kepala['nama_lengkap'] ?? '-');
    $sheet->setCellValue('D' . $row, $data['alamat']);
    $sheet->setCellValue('E' . $row, $data['rt_rw']);
    $sheet->setCellValue('F' . $row, $data['nama_wilayah']);
    $sheet->setCellValue('G' . $row, $data['jumlah_anggota'] . ' orang');
    $sheet->setCellValue('H' . $row, date('d/m/Y', strtotime($data['dibuat_pada'])));

    // Style data rows
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
    $sheet->getStyle('A' . $row . ':H' . $row)->applyFromArray($dataStyle);
    $sheet->getRowDimension($row)->setRowHeight(25);

    $row++;
}

// Auto size columns
foreach (range('A', 'H') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set print settings
$sheet->getPageSetup()
    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
    ->setFitToWidth(1)
    ->setFitToHeight(0);

// Freeze panes
$sheet->freezePane('A2');

// Set the header for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan_KK_' . $bulan . '_' . $tahun . '.xlsx"');
header('Cache-Control: max-age=0');

// Save file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

<?php
require 'config.php';
$tab = $_GET['tab'];
$filename = "Laporan_" . ucfirst($tab) . "_" . date('Y-m-d') . ".csv";

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"$filename\"");

$output = fopen("php://output", "w");

if ($tab == 'gu') {
    fputcsv($output, array('No', 'No Gambar Ukur', 'Tahun', 'Kecamatan', 'Desa', 'Rak', 'Status'));
    $res = mysqli_query($conn, "SELECT no_gambar_ukur, tahun, kecamatan, desa_kelurahan, lemari_rak, status FROM gambar_ukur");
} else {
    fputcsv($output, array('No', 'No Gambar Ukur', 'Nama Peminjam', 'Tgl Pinjam', 'Tgl Kembali', 'Keperluan', 'Status'));
    $st = ($tab == 'peminjaman') ? 'Dipinjam' : 'Dikembalikan';
    $res = mysqli_query($conn, "SELECT no_gambar_ukur, nama_peminjam, tgl_pinjam, tgl_kembali, keperluan, status FROM transaksi WHERE status='$st'");
}

$i = 1;
while($row = mysqli_fetch_assoc($res)) {
    array_unshift($row, $i++);
    fputcsv($output, $row);
}
fclose($output);
?>
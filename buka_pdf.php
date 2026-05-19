<?php
require 'config.php';

// Proteksi Keamanan
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Perbaikan: Membaca parameter 'view' dan mendekripsinya kembali
if (isset($_GET['view'])) {
    $nama_file = basename(base64_decode($_GET['view'])); 
    $jalur_file = 'uploads/' . $nama_file;

    if (file_exists($jalur_file)) {
        // Mengirimkan instruksi resmi agar browser membuka file secara INLINE (di dalam tab)
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $nama_file . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Membaca dan menembakkan isi file fisik PDF
        readfile($jalur_file);
        exit();
    } else {
        echo "<script>alert('Waduh Bro, berkas fisik PDF tidak ditemukan!'); window.close();</script>";
    }
} else {
    header("Location: data_gambar_ukur.php");
    exit();
}
?>
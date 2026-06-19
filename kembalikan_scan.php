<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) { 
    header("Location: index.php"); 
    exit(); 
}

if (isset($_GET['no_gu'])) {
    $no_gu = mysqli_real_escape_string($conn, $_GET['no_gu']);
    
    // Cari status transaksi dokumen ini yang masih berstatus 'Dipinjam'
    $cek_trx = mysqli_query($conn, "SELECT id FROM transaksi WHERE no_gambar_ukur='$no_gu' AND status='Dipinjam' ORDER BY id DESC LIMIT 1");
    
    if (mysqli_num_rows($cek_trx) > 0) {
        $trx = mysqli_fetch_assoc($cek_trx);
        $id = $trx['id'];
        
        // Membebaskan dokumen (Update Database)
        mysqli_query($conn, "UPDATE transaksi SET status='Dikembalikan' WHERE id='$id'");
        mysqli_query($conn, "UPDATE gambar_ukur SET status='Tersedia' WHERE no_gambar_ukur='$no_gu'");
        
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Sukses!',
                    text: 'Gambar Ukur $no_gu berhasil dikembalikan.',
                    icon: 'success',
                    confirmButtonColor: '#110B45'
                }).then(() => { window.location.href = 'transaksi.php?tab=dikembalikan'; });
            });
        </script>";
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Gagal Memproses!',
                    text: 'Gambar Ukur $no_gu tidak sedang dipinjam atau nomor tidak terdaftar.',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                }).then(() => { window.location.href = 'transaksi.php'; });
            });
        </script>";
    }
} else {
    header("Location: transaksi.php");
}
?>
<?php
require 'config.php';
session_start();

// Proteksi Login
if (!isset($_SESSION['user_id'])) { 
    header("Location: index.php"); 
    exit(); 
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Ambil nomor gambar ukur dari transaksi
    $query_trx = mysqli_query($conn, "SELECT no_gambar_ukur FROM transaksi WHERE id='$id'");
    
    if (mysqli_num_rows($query_trx) > 0) {
        $trx = mysqli_fetch_assoc($query_trx);
        $no_gu = $trx['no_gambar_ukur'];
        
        // Ubah status di Transaksi
        $update_trx = mysqli_query($conn, "UPDATE transaksi SET status='Dikembalikan' WHERE id='$id'");
        // Kembalikan status di Master Data
        $update_gu = mysqli_query($conn, "UPDATE gambar_ukur SET status='Tersedia' WHERE no_gambar_ukur='$no_gu'");

        if ($update_trx && $update_gu) {
            // Menampilkan Pop-Up Sukses dengan SweetAlert2
            echo "<!DOCTYPE html>
            <html lang='id'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <link href='https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&display=swap' rel='stylesheet'>
                <style>body{font-family: 'Inter', sans-serif; background-color: #F8FAFC;}</style>
            </head>
            <body>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Dokumen Gambar Ukur berhasil dikembalikan ke arsip.',
                        icon: 'success',
                        confirmButtonColor: '#110B45',
                        customClass: {
                            popup: 'rounded-[1.5rem]',
                            confirmButton: 'px-5 py-2.5 rounded-xl text-xs font-bold font-[\"Inter\"] text-white'
                        }
                    }).then(() => {
                        window.location.href = 'transaksi.php?tab=dikembalikan';
                    });
                });
            </script>
            </body>
            </html>";
            exit(); // Hentikan eksekusi script agar pop-up bisa muncul
        }
    }
}

// Jika terjadi error atau tidak ada id, kembalikan ke transaksi
header("Location: transaksi.php");
?>
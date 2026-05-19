<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $trx = mysqli_fetch_assoc(mysqli_query($conn, "SELECT no_gambar_ukur FROM transaksi WHERE id='$id'"));
    
    // Ubah status di Transaksi
    mysqli_query($conn, "UPDATE transaksi SET status='Dikembalikan' WHERE id='$id'");
    // Kembalikan status di Master Data
    mysqli_query($conn, "UPDATE gambar_ukur SET status='Tersedia' WHERE no_gambar_ukur='".$trx['no_gambar_ukur']."'");
}
// Lempar otomatis ke tab dikembalikan
header("Location: transaksi.php?tab=dikembalikan");
?>
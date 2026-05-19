<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT file_pdf FROM gambar_ukur WHERE id = '$id'"));

// Hapus file dari folder
if(file_exists("uploads/".$data['file_pdf'])) {
    unlink("uploads/".$data['file_pdf']);
}

// Hapus data dari database
mysqli_query($conn, "DELETE FROM gambar_ukur WHERE id = '$id'");
header("Location: data_gambar_ukur.php");
?>
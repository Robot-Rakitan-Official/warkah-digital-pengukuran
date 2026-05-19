<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM transaksi WHERE id='$id'");
}
header("Location: transaksi.php");
?>
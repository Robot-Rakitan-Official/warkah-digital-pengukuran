<?php
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Keamanan: Cegah menghapus akun milik sendiri agar tidak terkunci keluar
    if ($id != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    }
}
header("Location: pengaturan.php");
exit();
?>
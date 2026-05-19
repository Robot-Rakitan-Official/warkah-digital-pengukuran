<?php
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Validasi email duplikat
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' OR nama_lengkap='$nama'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Nama Lengkap atau Email tersebut sudah terdaftar di sistem!";
    } else {
        $query = "INSERT INTO users (nama_lengkap, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            header("Location: pengaturan.php");
            exit();
        } else {
            $error = "Gagal mendaftarkan akun baru!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Akun Baru - Warkah Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; } .bg-navy { background-color: #110B45; } </style>
</head>
<body class="flex items-center justify-center p-10 min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-sm border w-full max-w-2xl">
        <h2 class="text-2xl font-bold text-[#110B45] mb-2 font-['Public_Sans']">Tambah Akun Baru</h2>
        <p class="text-sm text-gray-400 mb-8 font-medium">Silahkan lengkapi data di bawah ini untuk mendaftarkan hak akses akun baru.</p>

        <?php if(isset($error)) echo "<p class='text-red-500 text-sm mb-4 bg-red-50 p-3 rounded-xl border border-red-100'>$error</p>"; ?>

        <form method="POST">
            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Username</label>
                <input type="text" name="nama_lengkap" placeholder="Masukkan username" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45]" required>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Alamat Email</label>
                <input type="email" name="email" placeholder="Masukkan email pengguna" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45]" required>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Kata sandi(Password)</label>
                <input type="password" name="password" placeholder="Minimum 8 kata, termasuk nomor dan simbol" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45]" required>
            </div>

            <div class="mb-8">
                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Status Akun (Role)</label>
                <select name="role" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45]" required>
                    <option value="karyawan">Karyawan</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="pengaturan.php" class="px-6 py-2.5 border border-gray-200 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 transition-all">Batal</a>
                <button type="submit" name="simpan" class="bg-navy text-white px-8 py-2.5 rounded-xl text-sm font-bold hover:bg-opacity-90 transition-all shadow-md">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>
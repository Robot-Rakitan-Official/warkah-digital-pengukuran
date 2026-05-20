<?php
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'"));

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $password_baru = mysqli_real_escape_string($conn, $_POST['password_baru']);

    if (!empty($password_baru)) {
        // Enkripsi password baru dengan MD5 sebelum disimpan
        $password_enkripsi = md5($password_baru);
        $query = "UPDATE users SET nama_lengkap='$nama', role='$role', password='$password_enkripsi' WHERE id='$id'";
    } else {
        // Jika kosong, pertahankan password lama
        $query = "UPDATE users SET nama_lengkap='$nama', role='$role' WHERE id='$id'";
    }

    if (mysqli_query($conn, $query)) {
        // Jika mengedit akun sendiri, update nama session agar sinkron
        if ($id == $_SESSION['user_id']) {
            $_SESSION['nama'] = $nama;
            $_SESSION['role'] = $role;
        }
        header("Location: pengaturan.php");
        exit();
    } else {
        $error = "Gagal memperbarui informasi akun!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Akun - Warkah Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; } .bg-navy { background-color: #110B45; } </style>
</head>
<body class="flex items-center justify-center p-10 min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-sm border w-full max-w-2xl">
        <h2 class="text-2xl font-bold text-[#110B45] mb-2 font-['Public_Sans']">Edit Akun</h2>
        <p class="text-sm text-gray-400 mb-8 font-medium">Perbarui informasi akun pengguna di bawah ini.</p>

        <?php if(isset($error)) echo "<p class='text-red-500 text-sm mb-4 bg-red-50 p-2 rounded'>$error</p>"; ?>

        <form method="POST">
            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($data['nama_lengkap']); ?>" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45]" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Status Akun (Role)</label>
                    <select name="role" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45]" required>
                        <option value="karyawan" <?= $data['role'] == 'karyawan' ? 'selected' : ''; ?>>Karyawan</option>
                        <option value="admin" <?= $data['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Password Baru</label>
                    <input type="password" name="password_baru" placeholder="Isi untuk mengganti password" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45]">
                    <p class="text-[10px] text-gray-400 mt-1 font-semibold">Kosongkan jika tidak ingin mengubah password.</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 bg-gray-50 -mx-8 -mb-8 px-8 py-5 border-t border-gray-100 rounded-b-2xl">
                <a href="pengaturan.php" class="px-6 py-2.5 border border-gray-200 bg-white rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 transition-all">Batal</a>
                <button type="submit" name="simpan" class="bg-navy text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-opacity-90 transition-all shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</body>
</html>

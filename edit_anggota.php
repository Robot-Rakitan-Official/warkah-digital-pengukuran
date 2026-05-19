<?php
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM anggota WHERE id = '$id'"));

if (isset($_POST['simpan'])) {
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "UPDATE anggota SET nip='$nip', nama_lengkap='$nama', jabatan='$jabatan', email='$email', no_telepon='$telepon', status='$status' WHERE id='$id'";
              
    if (mysqli_query($conn, $query)) {
        header("Location: data_anggota.php");
        exit();
    } else {
        $error = "Gagal memperbarui data anggota!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Anggota - Warkah Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; } .bg-navy { background-color: #110B45; } </style>
</head>
<body class="flex items-center justify-center p-10 min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-sm border w-full max-w-4xl">
        <h2 class="text-2xl font-bold text-[#110B45] mb-2 font-['Public_Sans']">Edit Anggota</h2>
        <p class="text-sm text-gray-400 mb-8 font-medium">Silahkan sesuaikan formulir di bawah ini untuk memperbarui data anggota.</p>

        <?php if(isset($error)) echo "<p class='text-red-500 text-sm mb-4 bg-red-50 p-2 rounded'>$error</p>"; ?>

        <form method="POST">
            <div class="grid grid-cols-2 gap-6 mb-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">NIP</label>
                    <input type="text" name="nip" value="<?= htmlspecialchars($data['nip']); ?>" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45]" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($data['nama_lengkap']); ?>" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45]" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Jabatan</label>
                    <input type="text" name="jabatan" value="<?= htmlspecialchars($data['jabatan']); ?>" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45]" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($data['email']); ?>" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45]" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">No. Telepon</label>
                    <input type="text" name="no_telepon" value="<?= htmlspecialchars($data['no_telepon']); ?>" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45]" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Status</label>
                    <select name="status" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45]" required>
                        <option value="Aktif" <?= $data['status'] == 'Aktif' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="Tidak Atif" <?= $data['status'] == 'Tidak Aktif' ? 'selected' : ''; ?>>Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 bg-gray-50 -mx-8 -mb-8 px-8 py-5 border-t border-gray-100 rounded-b-2xl">
                <a href="data_anggota.php" class="px-6 py-2.5 border border-gray-200 bg-white rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 transition-all">Batal</a>
                <button type="submit" name="simpan" class="bg-navy text-white px-8 py-2.5 rounded-xl text-sm font-bold hover:bg-opacity-90 transition-all shadow-md">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>
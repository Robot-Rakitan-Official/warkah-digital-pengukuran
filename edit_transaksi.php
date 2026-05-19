<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM transaksi WHERE id = '$id'"));

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_peminjam']);
    $nip = mysqli_real_escape_string($conn, $_POST['nip_nik']);
    $status_peminjam = mysqli_real_escape_string($conn, $_POST['status_peminjam']);
    $tgl_pinjam = mysqli_real_escape_string($conn, $_POST['tgl_pinjam']);
    $tgl_kembali = mysqli_real_escape_string($conn, $_POST['tgl_kembali']);
    $keperluan = mysqli_real_escape_string($conn, $_POST['keperluan']);

    $q_update = "UPDATE transaksi SET nama_peminjam='$nama', nip_nik='$nip', status_peminjam='$status_peminjam', tgl_pinjam='$tgl_pinjam', tgl_kembali='$tgl_kembali', keperluan='$keperluan' WHERE id='$id'";

    if (mysqli_query($conn, $q_update)) {
        header("Location: transaksi.php"); exit();
    } else {
        $error = "Gagal memperbarui data!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Peminjaman</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Public+Sans:wght@600;700&display=swap" rel="stylesheet">
    <style> body { background-color: #F8FAFC; font-family: 'Inter', sans-serif;} .bg-navy { background-color: #110B45; } </style>
</head>
<body class="flex items-center justify-center p-10 min-h-screen">
    <div class="bg-white rounded-xl shadow-sm border w-full max-w-4xl overflow-hidden">
        <div class="p-8 pb-4">
            <h2 class="text-2xl font-bold font-['Public_Sans'] text-[#110B45] mb-1">Edit Peminjaman</h2>
            <p class="text-sm text-gray-500 mb-6">Silakan perbarui formulir di bawah ini.</p>
            <?php if(isset($error)) echo "<p class='text-red-500 text-sm mb-4 bg-red-50 p-3 rounded-lg border border-red-100'>$error</p>"; ?>
        </div>
        <form method="POST" class="p-8 pt-0">
            <div class="mb-8">
                <h3 class="flex items-center gap-2 text-sm font-bold text-navy mb-4 border-b pb-2">Informasi Peminjam</h3>
                <div class="grid grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Nama Peminjam</label>
                        <input type="text" name="nama_peminjam" value="<?= $data['nama_peminjam']; ?>" class="w-full bg-[#F8FAFC] border p-2.5 rounded-lg text-sm focus:border-[#110B45]" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">NIP/NIK</label>
                        <input type="text" name="nip_nik" value="<?= $data['nip_nik']; ?>" class="w-full bg-[#F8FAFC] border p-2.5 rounded-lg text-sm focus:border-[#110B45]" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Status</label>
                    <select name="status_peminjam" class="w-full bg-[#F8FAFC] border p-2.5 rounded-lg text-sm focus:border-[#110B45]" required>
                        <option value="ASN BPN" <?= $data['status_peminjam']=='ASN BPN'?'selected':''; ?>>ASN BPN</option>
                        <option value="Non-ASN BPN" <?= $data['status_peminjam']=='Non-ASN BPN'?'selected':''; ?>>Non-ASN BPN</option>
                        <option value="Masyarakat / Umum" <?= $data['status_peminjam']=='Masyarakat / Umum'?'selected':''; ?>>Masyarakat / Umum</option>
                    </select>
                </div>
            </div>
            <div class="mb-8">
                <h3 class="flex items-center gap-2 text-sm font-bold text-navy mb-4 border-b pb-2">Detail Dokumen & Waktu</h3>
                <div class="grid grid-cols-3 gap-6 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">No. Gambar Ukur (GU)</label>
                        <input type="text" value="<?= $data['no_gambar_ukur']; ?>" class="w-full bg-gray-200 border p-2.5 rounded-lg text-sm text-gray-500 cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Tanggal Pinjam</label>
                        <input type="date" name="tgl_pinjam" value="<?= $data['tgl_pinjam']; ?>" class="w-full bg-[#F8FAFC] border p-2.5 rounded-lg text-sm focus:border-[#110B45]" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Estimasi Tgl. Kembali</label>
                        <input type="date" name="tgl_kembali" value="<?= $data['tgl_kembali']; ?>" class="w-full bg-[#F8FAFC] border p-2.5 rounded-lg text-sm focus:border-[#110B45]" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Keperluan</label>
                    <textarea name="keperluan" rows="3" class="w-full bg-[#F8FAFC] border p-3 rounded-lg text-sm focus:border-[#110B45]" required><?= $data['keperluan']; ?></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-6 bg-gray-50 -mx-8 -mb-8 px-8 py-5 border-t">
                <a href="transaksi.php" class="px-6 py-2.5 border bg-white rounded-lg text-sm font-bold text-gray-700">Batal</a>
                <button type="submit" name="simpan" class="bg-navy text-white px-6 py-2.5 rounded-lg text-sm font-bold">Simpan Peminjaman</button>
            </div>
        </form>
    </div>
</body>
</html>
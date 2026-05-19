<?php
require 'config.php';

// Proteksi Keamanan: Hanya Admin yang boleh melakukan aksi hapus
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Variabel penanda status untuk memunculkan desain pop-up
$status_hapus = "";
$pesan_modal = "";

if (isset($_GET['id'])) {
    $id_anggota = mysqli_real_escape_string($conn, $_GET['id']);

    // LANGKAH 1: Ambil data email anggota
    $cari_anggota = mysqli_query($conn, "SELECT email FROM anggota WHERE id = '$id_anggota'");
    
    if (mysqli_num_rows($cari_anggota) === 1) {
        $data = mysqli_fetch_assoc($cari_anggota);
        $email_anggota = $data['email'];

        // LANGKAH 2: Hapus akun login di tabel `users`
        mysqli_query($conn, "DELETE FROM users WHERE email = '$email_anggota'");

        // LANGKAH 3: Hapus data profil fisik di tabel `anggota`
        $eksekusi_hapus = mysqli_query($conn, "DELETE FROM anggota WHERE id = '$id_anggota'");

        if ($eksekusi_hapus) {
            $status_hapus = "sukses";
            $pesan_modal = "Data profil anggota beserta akun aksesnya telah terhapus dari sistem secara permanen.";
        } else {
            $status_hapus = "error";
            $pesan_modal = "Terjadi kesalahan sistem. Gagal menghapus data profil anggota.";
        }
    } else {
        $status_hapus = "error";
        $pesan_modal = "Data anggota tidak ditemukan di database!";
    }
} else {
    header("Location: data_anggota.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Anggota - Warkah Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&family=Public+Sans:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        /* Animasi pop-up muncul perlahan */
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-50/50">

    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm animate-fade-in no-print">
        <div class="bg-white rounded-[1.5rem] p-8 max-w-sm w-full mx-4 shadow-2xl border border-gray-100 text-center relative overflow-hidden">
            
            <?php if ($status_hapus === 'sukses'): ?>
                <div class="absolute top-0 left-0 w-full h-2 bg-green-500"></div>
                <div class="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-5 border-[4px] border-white shadow-sm ring-1 ring-green-100">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="font-extrabold font-['Public_Sans'] text-[#110B45] text-2xl mb-2">Berhasil Dihapus!</h3>
            <?php else: ?>
                <div class="absolute top-0 left-0 w-full h-2 bg-red-500"></div>
                <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5 border-[4px] border-white shadow-sm ring-1 ring-red-100">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <h3 class="font-extrabold font-['Public_Sans'] text-[#110B45] text-2xl mb-2">Oops, Gagal!</h3>
            <?php endif; ?>
            
            <p class="text-sm text-gray-500 font-medium mb-8 leading-relaxed"><?= $pesan_modal; ?></p>
            
            <button onclick="window.location.href='data_anggota.php'" class="w-full py-3.5 bg-[#110B45] hover:bg-opacity-90 text-white rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2">
                Oke, Kembali
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
            
        </div>
    </div>

</body>
</html>
<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$page = 'pengaturan';
$sub_page = ''; 
$user_role = $_SESSION['role']; 
$user_id_aktif = $_SESSION['user_id'];

$pesan_sukses = null;
$pesan_error = null;

if (isset($_POST['ganti_password'])) {
    $pass_lama = md5($_POST['password_lama']);
    $pass_baru = md5($_POST['password_baru']);
    $konfirmasi = md5($_POST['konfirmasi_password']);

    $cek_db = mysqli_query($conn, "SELECT password FROM users WHERE id = '$user_id_aktif'");
    $data_user = mysqli_fetch_assoc($cek_db);

    if ($data_user['password'] === $pass_lama) {
        if ($pass_baru === $konfirmasi) {
            $update = mysqli_query($conn, "UPDATE users SET password = '$pass_baru' WHERE id = '$user_id_aktif'");
            if ($update) {
                $pesan_sukses = "Password berhasil diperbarui.";
            } else {
                $pesan_error = "Sistem gagal memperbarui password.";
            }
        } else {
            $pesan_error = "Konfirmasi password baru tidak sama!";
        }
    } else {
        $pesan_error = "Password lama yang dimasukkan salah!";
    }
}

$query_users = null;
if ($user_role === 'admin') {
    $query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Warkah Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&family=Public+Sans:wght@700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; } 
        .bg-navy { background-color: #110B45; } 
        .text-navy { color: #110B45; }
        .bg-yellow-brand { background-color: #FACC15; }
        ::-webkit-scrollbar { height: 6px; width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.2s ease-out forwards; }
    </style>
</head>
<body class="flex flex-col md:flex-row h-screen overflow-hidden text-gray-800">
    
    <div class="md:hidden bg-[#110B45] text-white p-4 flex justify-between items-center shadow-md z-50 no-print">
        <div class="flex items-center gap-3">
            <img src="assets/img/logo.png" alt="Logo" class="w-8 h-8" onerror="this.style.display='none'">
            <h2 class="text-sm font-bold font-['Public_Sans'] tracking-wide">Warkah Digital</h2>
        </div>
        <button onclick="document.getElementById('sidebar').classList.toggle('hidden')" class="text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
        </button>
    </div>

    <div id="sidebar" class="hidden md:flex w-full md:w-[280px] bg-[#110B45] text-white flex-col justify-between shadow-2xl z-40 shrink-0 absolute top-[64px] left-0 md:relative md:top-0 h-[calc(100vh-64px)] md:h-screen overflow-y-auto no-print">
        <div>
            <div class="p-8 text-center mt-4 hidden md:block">
                <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 border-2 border-white/20">
                    <img src="assets/img/logo.png" alt="Logo" class="w-14 h-14" onerror="this.style.display='none'">
                </div>
                <h2 class="text-xl font-bold font-['Public_Sans'] leading-tight tracking-wide">Warkah Digital<br>Pengukuran</h2>
            </div>
            
            <nav class="px-6 py-6 md:py-0 space-y-2">
                <a href="dashboard.php" class="flex items-center gap-3 py-3 px-4 rounded-xl text-sm transition-all text-gray-400 hover:text-white font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>
                
                <div>
                    <button onclick="toggleMaster()" class="flex items-center justify-between w-full py-3 px-4 rounded-xl text-sm transition-all text-gray-400 hover:text-white font-semibold focus:outline-none">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                            Master Data
                        </div>
                        <svg id="chevron-icon" class="w-4 h-4 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="master-submenu" class="hidden pl-12 py-1 space-y-1">
                        <?php if ($user_role == 'admin'): ?>
                            <a href="data_anggota.php" class="block py-2 text-sm text-gray-400 hover:text-white transition-colors">Data Anggota</a>
                        <?php endif; ?>
                        <a href="data_gambar_ukur.php" class="block py-2 text-sm text-gray-400 hover:text-white transition-colors">Data Gambar Ukur</a>
                    </div>
                </div>

                <a href="transaksi.php" class="flex items-center gap-3 py-3 px-4 rounded-xl text-sm transition-all text-gray-400 hover:text-white font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Transaksi
                </a>

                <a href="laporan.php" class="flex items-center gap-3 py-3 px-4 rounded-xl text-sm transition-all text-gray-400 hover:text-white font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Laporan
                </a>

                <a href="pengaturan.php" class="flex items-center gap-3 py-3 px-4 rounded-xl text-sm transition-all text-gray-400 hover:text-white font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Pengaturan
                </a>
            </nav>
        </div>
        
        <div class="p-6 md:p-8">
            <a href="logout.php" class="flex items-center gap-3 text-gray-400 hover:text-white text-sm font-semibold transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Keluar
            </a>
        </div>
    </div>

    <div class="flex-1 flex flex-col overflow-hidden w-full relative z-0">
        <header class="bg-white px-4 md:px-8 py-4 md:py-5 flex justify-between items-center shadow-sm z-0">
            <h1 class="text-base md:text-xl font-bold font-['Public_Sans'] text-navy uppercase tracking-tight truncate hidden sm:block">Warkah Digital Pengukuran</h1>
            <div class="flex items-center gap-2 md:gap-3 font-semibold bg-gray-100 px-3 md:px-4 py-1.5 rounded-full text-[10px] md:text-xs text-gray-600 ml-auto sm:ml-0">
                <span>Halo, <?= htmlspecialchars($_SESSION['nama']); ?></span>
                <span class="bg-[#110B45] text-white text-[8px] md:text-[9px] px-2 py-0.5 rounded-full uppercase font-bold"><?= htmlspecialchars($user_role); ?></span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-10 bg-gray-50/30 w-full">
            <h2 class="text-xl md:text-2xl font-bold font-['Public_Sans'] text-navy">Pengaturan Akun</h2>
            <p class="text-xs md:text-sm text-gray-500 mb-6 md:mb-8 mt-1 font-medium">Kelola preferensi keamanan dan akses sistem.</p>

            <div class="flex flex-col xl:flex-row gap-6 md:gap-8 items-start w-full">
                
                <div class="bg-white p-5 md:p-8 rounded-[1.5rem] shadow-sm border border-gray-100 w-full xl:w-1/3 shrink-0">
                    <h3 class="text-base md:text-lg font-bold text-navy font-['Public_Sans'] mb-1">Ganti Password</h3>
                    <p class="text-[10px] md:text-[11px] text-gray-400 mb-5 md:mb-6 font-medium leading-relaxed">Amankan akunmu dengan mengganti password secara berkala.</p>

                    <?php if($pesan_sukses): ?>
                        <p class="text-green-600 text-[10px] md:text-[11px] font-bold mb-5 bg-green-50 border border-green-100 p-3 md:p-3.5 rounded-xl flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <?= $pesan_sukses ?>
                        </p>
                    <?php endif; ?>
                    <?php if($pesan_error): ?>
                        <p class="text-red-500 text-[10px] md:text-[11px] font-bold mb-5 bg-red-50 border border-red-100 p-3 md:p-3.5 rounded-xl flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                            <?= $pesan_error ?>
                        </p>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-4 md:mb-5">
                            <label class="block text-[9px] md:text-[10px] font-bold text-gray-500 mb-1.5 md:mb-2 uppercase tracking-wide">Password Saat Ini</label>
                            <div class="relative">
                                <input type="password" name="password_lama" id="pass_lama" placeholder="Masukkan password lama" class="w-full bg-[#F8FAFC] border border-gray-200 p-2.5 md:p-3 pr-10 md:pr-12 rounded-xl text-xs md:text-sm focus:outline-none focus:border-navy focus:ring-1 focus:ring-navy transition-all" required>
                                <button type="button" onclick="togglePassword('pass_lama', 'eye_lama')" class="absolute inset-y-0 right-0 pr-3 md:pr-4 flex items-center text-gray-400 hover:text-navy focus:outline-none">
                                    <svg id="eye_lama" class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4 md:mb-5">
                            <label class="block text-[9px] md:text-[10px] font-bold text-gray-500 mb-1.5 md:mb-2 uppercase tracking-wide">Password Baru</label>
                            <div class="relative">
                                <input type="password" name="password_baru" id="pass_baru" placeholder="Minimal 6 karakter" class="w-full bg-[#F8FAFC] border border-gray-200 p-2.5 md:p-3 pr-10 md:pr-12 rounded-xl text-xs md:text-sm focus:outline-none focus:border-navy focus:ring-1 focus:ring-navy transition-all" required>
                                <button type="button" onclick="togglePassword('pass_baru', 'eye_baru')" class="absolute inset-y-0 right-0 pr-3 md:pr-4 flex items-center text-gray-400 hover:text-navy focus:outline-none">
                                    <svg id="eye_baru" class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                </button>
                            </div>
                        </div>

                        <div class="mb-6 md:mb-8">
                            <label class="block text-[9px] md:text-[10px] font-bold text-gray-500 mb-1.5 md:mb-2 uppercase tracking-wide">Konfirmasi Password</label>
                            <div class="relative">
                                <input type="password" name="konfirmasi_password" id="pass_konfirm" placeholder="Ketik ulang password baru" class="w-full bg-[#F8FAFC] border border-gray-200 p-2.5 md:p-3 pr-10 md:pr-12 rounded-xl text-xs md:text-sm focus:outline-none focus:border-navy focus:ring-1 focus:ring-navy transition-all" required>
                                <button type="button" onclick="togglePassword('pass_konfirm', 'eye_konfirm')" class="absolute inset-y-0 right-0 pr-3 md:pr-4 flex items-center text-gray-400 hover:text-navy focus:outline-none">
                                    <svg id="eye_konfirm" class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" name="ganti_password" class="w-full bg-navy hover:bg-opacity-95 text-white py-2.5 md:py-3 rounded-xl text-xs md:text-sm font-bold transition-all shadow-md">
                            Simpan Password Baru
                        </button>
                    </form>
                </div>

                <?php if ($user_role === 'admin'): ?>
                <div class="bg-white p-5 md:p-8 rounded-[1.5rem] shadow-sm border border-gray-100 w-full xl:w-2/3">
                    <h3 class="text-base md:text-lg font-bold text-navy font-['Public_Sans'] mb-1">Kelola Akses Pengguna</h3>
                    <p class="text-[10px] md:text-[11px] text-gray-400 mb-5 md:mb-6 font-medium">Hanya Admin yang dapat mengelola hak akses akun di bawah ini.</p>
                    
                    <div class="bg-indigo-50/50 border border-indigo-100 p-3 md:p-4 rounded-xl flex items-start gap-2 md:gap-3 mb-5 md:mb-6">
                        <svg class="w-4 h-4 md:w-5 md:h-5 text-indigo-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <p class="text-xs md:text-[13px] font-bold text-indigo-800 mb-1">Manajemen Akun Terpusat</p>
                            <p class="text-[9px] md:text-[11px] text-indigo-600 leading-relaxed">Penambahan akun baru saat ini dilakukan secara otomatis saat Anda mendaftarkan Anggota Baru di menu <strong>Master Data > Data Anggota</strong>.</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto border border-gray-100 rounded-xl w-full">
                        <table class="w-full text-left text-sm whitespace-nowrap md:whitespace-normal">
                            <thead class="bg-gray-50/50 text-gray-400 text-[9px] md:text-[10px] uppercase tracking-widest font-bold border-b border-gray-100">
                                <tr>
                                    <th class="py-3 md:py-4 px-4 md:px-5">NO</th>
                                    <th class="py-3 md:py-4 px-4 md:px-5">NAMA LENGKAP</th>
                                    <th class="py-3 md:py-4 px-4 md:px-5">USERNAME / EMAIL</th>
                                    <th class="py-3 md:py-4 px-4 md:px-5 text-center">ROLE</th>
                                    <th class="py-3 md:py-4 px-4 md:px-5 text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-[11px] md:text-xs">
                                <?php $no = 1; while($row_user = mysqli_fetch_assoc($query_users)): ?>
                                <tr class="border-b border-gray-50 last:border-b-0 hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3 md:py-4 px-4 md:px-5 text-gray-400 font-bold"><?= $no++; ?>.</td>
                                    <td class="py-3 md:py-4 px-4 md:px-5 font-bold text-navy"><?= htmlspecialchars($row_user['nama_lengkap']); ?></td>
                                    <td class="py-3 md:py-4 px-4 md:px-5">
                                        <span class="block font-bold text-gray-700"><?= htmlspecialchars($row_user['username']); ?></span>
                                        <span class="text-[9px] md:text-[10px] text-gray-400 block mt-0.5"><?= htmlspecialchars($row_user['email']); ?></span>
                                    </td>
                                    <td class="py-3 md:py-4 px-4 md:px-5 text-center">
                                        <?php if($row_user['role'] == 'admin'): ?>
                                            <span class="bg-[#110B45] text-white py-1 px-2 md:px-3 rounded-full text-[8px] md:text-[9px] font-bold tracking-widest uppercase">Admin</span>
                                        <?php else: ?>
                                            <span class="bg-gray-100 text-gray-600 py-1 px-2 md:px-3 rounded-full text-[8px] md:text-[9px] font-bold tracking-widest uppercase">Karyawan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 md:py-4 px-4 md:px-5 text-center flex justify-center gap-2 md:gap-3">
                                        <a href="edit_akun.php?id=<?= $row_user['id']; ?>" class="text-gray-400 hover:text-navy transition-colors bg-white border border-gray-100 p-1 md:p-1.5 rounded-lg shadow-sm" title="Edit Akun">
                                            <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <?php if($row_user['id'] != $user_id_aktif): ?>
                                        <a href="#" onclick="bukaModalHapusAkun(event, 'hapus_akun.php?id=<?= $row_user['id']; ?>')" class="text-gray-400 hover:text-red-600 transition-colors bg-white border border-gray-100 p-1 md:p-1.5 rounded-lg shadow-sm" title="Hapus Akun">
                                            <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </a>
                                        <?php else: ?>
                                        <span class="w-6 h-6 md:w-8 md:h-8 inline-block" title="Tidak bisa hapus akun sendiri"></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <div id="modalHapusAkun" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 backdrop-blur-sm transition-all no-print">
        <div class="bg-white rounded-[1.5rem] p-6 max-w-sm w-full mx-4 shadow-2xl border border-gray-100 animate-fade-in text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-red-600"></div>

            <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 border-[4px] border-white shadow-sm ring-1 ring-red-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            
            <h3 class="font-extrabold text-[#110B45] text-lg mb-2 font-['Public_Sans']">Hapus Akun Login?</h3>
            <p class="text-[11px] text-gray-500 font-medium mb-6 leading-relaxed">Anda yakin ingin menghapus akun akses ini? Pengguna tersebut tidak akan bisa login lagi ke dalam sistem.</p>
            
            <div class="flex justify-center gap-3">
                <button onclick="tutupModalHapusAkun()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-all w-1/2">
                    Tidak, Batal
                </button>
                <button onclick="eksekusiHapusAkun()" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition-all shadow-md w-1/2">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleMaster() {
            const submenu = document.getElementById('master-submenu');
            const chevron = document.getElementById('chevron-icon');
            submenu.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />`;
            }
        }

        let urlTargetHapusAkun = '';
        function bukaModalHapusAkun(event, url) {
            event.preventDefault(); 
            urlTargetHapusAkun = url; 
            document.getElementById('modalHapusAkun').classList.remove('hidden'); 
        }
        function tutupModalHapusAkun() {
            document.getElementById('modalHapusAkun').classList.add('hidden'); 
            urlTargetHapusAkun = ''; 
        }
        function eksekusiHapusAkun() {
            if (urlTargetHapusAkun) {
                window.location.href = urlTargetHapusAkun; 
            }
        }
    </script>
</body>
</html>
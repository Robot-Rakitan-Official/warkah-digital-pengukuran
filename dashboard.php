<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$page = 'dashboard';
$sub_page = '';
$user_role = $_SESSION['role'];

$total_anggota = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM anggota"))['total'];
$total_gu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM gambar_ukur"))['total'];
$total_pinjam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE status = 'Dipinjam'"))['total'];
$total_telat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE tgl_kembali < CURDATE() AND status = 'Dipinjam'"))['total'];

function hitungTrenBulanIni($conn, $tabel, $kolom_tanggal, $kondisi_tambahan = "") {
    $q_bulan_ini = "SELECT COUNT(*) as total FROM $tabel WHERE MONTH($kolom_tanggal) = MONTH(CURDATE()) AND YEAR($kolom_tanggal) = YEAR(CURDATE()) $kondisi_tambahan";
    $bulan_ini = mysqli_fetch_assoc(mysqli_query($conn, $q_bulan_ini))['total'];
    $q_bulan_lalu = "SELECT COUNT(*) as total FROM $tabel WHERE MONTH($kolom_tanggal) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR($kolom_tanggal) = YEAR(CURDATE() - INTERVAL 1 MONTH) $kondisi_tambahan";
    $bulan_lalu = mysqli_fetch_assoc(mysqli_query($conn, $q_bulan_lalu))['total'];
    if ($bulan_lalu == 0) return $bulan_ini > 0 ? "+100" : "0";
    $tren = (($bulan_ini - $bulan_lalu) / $bulan_lalu) * 100;
    return ($tren > 0 ? "+" : "") . round($tren);
}

$tren_anggota = hitungTrenBulanIni($conn, 'anggota', 'id'); 
$tren_gu = "+5";
$tren_pinjam = hitungTrenBulanIni($conn, 'transaksi', 'tgl_pinjam', "AND status = 'Dipinjam'");
$tren_telat = hitungTrenBulanIni($conn, 'transaksi', 'tgl_pinjam', "AND tgl_kembali < CURDATE() AND status = 'Dipinjam'");

$batas_data = 5; 
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman_aktif - 1) * $batas_data;

$query_count_trx = "SELECT COUNT(*) AS total FROM transaksi";
$total_trx = mysqli_fetch_assoc(mysqli_query($conn, $query_count_trx))['total'];
$total_halaman = ceil($total_trx / $batas_data);

$q_terbaru = mysqli_query($conn, "SELECT * FROM transaksi ORDER BY id DESC LIMIT $batas_data OFFSET $offset");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Warkah Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Inter:wght@400;500;600&family=Public+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        .font-heading { font-family: 'Public Sans', sans-serif; }
        .bg-navy { background-color: #110B45; }
        .bg-yellow-brand { background-color: #FACC15; }
        .text-navy { color: #110B45; }
    </style>
</head>
<body class="flex flex-col md:flex-row h-screen overflow-hidden text-gray-800">

    <div class="md:hidden bg-[#110B45] text-white p-4 flex justify-between items-center shadow-md z-50">
        <div class="flex items-center gap-3">
            <img src="assets/img/logo.png" alt="Logo" class="w-8 h-8" onerror="this.style.display='none'">
            <h2 class="text-sm font-bold font-heading tracking-wide">Warkah Digital</h2>
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
                <h2 class="text-xl font-bold font-heading leading-tight tracking-wide">Warkah Digital<br>Pengukuran</h2>
            </div>

            <nav class="px-6 py-6 md:py-0 space-y-2">
                <a href="dashboard.php" class="flex items-center gap-3 py-3 px-4 rounded-xl text-sm transition-all bg-yellow-brand text-navy font-bold shadow-lg">
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

    <div class="flex-1 flex flex-col overflow-hidden relative z-0 w-full">
        <header class="bg-white px-4 md:px-8 py-4 md:py-5 flex justify-between items-center shadow-sm z-0">
            <h1 class="text-base md:text-xl font-bold font-heading text-navy uppercase tracking-tight truncate hidden sm:block">Warkah Digital Pengukuran</h1>
            <div class="flex items-center gap-2 md:gap-3 font-semibold bg-gray-100 px-3 md:px-4 py-1.5 rounded-full text-[10px] md:text-xs text-gray-600 ml-auto sm:ml-0">
                <span>Halo, <?= htmlspecialchars($_SESSION['nama']); ?></span>
                <span class="bg-[#110B45] text-white text-[8px] md:text-[9px] px-2 py-0.5 rounded-full uppercase font-bold"><?= htmlspecialchars($user_role); ?></span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-10 bg-gray-50/30 w-full">
            <h2 class="text-xl md:text-2xl font-bold font-heading text-navy">Dashboard</h2>
            <p class="text-xs md:text-sm text-gray-500 mb-6 md:mb-8 mt-1 font-medium">Selamat datang di Aplikasi Warkah Digital Pengukuran</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-10 w-full">
                <div class="bg-white rounded-[1.25rem] p-5 md:p-6 border border-gray-100 shadow-sm relative overflow-hidden flex flex-col justify-between h-[120px] md:h-[140px] hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <p class="text-[10px] md:text-[11px] font-bold text-gray-500 tracking-widest uppercase">Total Anggota</p>
                        <div class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-[#EEF2FF] text-indigo-600 flex items-center justify-center">
                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path></svg>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <h3 class="text-3xl md:text-4xl font-extrabold text-navy leading-none font-heading"><?= number_format($total_anggota); ?></h3>
                        <div class="relative w-9 h-9 md:w-11 md:h-11 rounded-full border-[2px] md:border-[3px] border-dashed border-indigo-200 flex items-center justify-center bg-white shadow-sm">
                            <span class="text-[8px] md:text-[9px] font-extrabold text-indigo-600"><?= $tren_anggota; ?>%</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[1.25rem] p-5 md:p-6 border border-gray-100 shadow-sm relative overflow-hidden flex flex-col justify-between h-[120px] md:h-[140px] hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <p class="text-[10px] md:text-[11px] font-bold text-gray-500 tracking-widest uppercase leading-snug">Total Gambar<br>Ukur</p>
                        <div class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-[#FEF9C3] text-yellow-600 flex items-center justify-center">
                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 1.586l-4 1.5v12.828l4-1.5V1.586zM3.707 3.293A1 1 0 002 4v10a1 1 0 00.293.707L6 18.414V5.586L3.707 3.293zM17.707 5.293L14 1.586v12.828l2.293 2.293A1 1 0 0018 16V6a1 1 0 00-.293-.707z" clip-rule="evenodd"></path></svg>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <h3 class="text-3xl md:text-4xl font-extrabold text-navy leading-none font-heading"><?= number_format($total_gu); ?></h3>
                        <div class="relative w-9 h-9 md:w-11 md:h-11 rounded-full border-[2px] md:border-[3px] border-dashed border-yellow-300 flex items-center justify-center bg-white shadow-sm">
                            <span class="text-[8px] md:text-[9px] font-extrabold text-yellow-600"><?= $tren_gu; ?>%</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[1.25rem] p-5 md:p-6 border border-gray-100 shadow-sm relative overflow-hidden flex flex-col justify-between h-[120px] md:h-[140px] hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <p class="text-[10px] md:text-[11px] font-bold text-gray-500 tracking-widest uppercase leading-snug">Sedang<br>Dipinjam</p>
                        <div class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-[#FFEDD5] text-orange-600 flex items-center justify-center">
                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <h3 class="text-3xl md:text-4xl font-extrabold text-navy leading-none font-heading"><?= number_format($total_pinjam); ?></h3>
                        <div class="relative w-9 h-9 md:w-11 md:h-11 rounded-full border-[2px] md:border-[3px] border-dashed border-gray-300 flex items-center justify-center bg-white shadow-sm">
                            <span class="text-[8px] md:text-[9px] font-extrabold text-gray-800"><?= $tren_pinjam; ?>%</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[1.25rem] p-5 md:p-6 border border-red-100 shadow-sm relative overflow-hidden flex flex-col justify-between h-[120px] md:h-[140px] hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <p class="text-[10px] md:text-[11px] font-bold text-gray-500 tracking-widest uppercase">Terlambat</p>
                        <div class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-[#FEE2E2] text-red-600 flex items-center justify-center">
                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        </div>
                    </div>
                    <div class="flex justify-between items-end">
                        <h3 class="text-3xl md:text-4xl font-extrabold text-red-600 leading-none font-heading"><?= number_format($total_telat); ?></h3>
                        <div class="relative w-9 h-9 md:w-11 md:h-11 rounded-full border-[2px] md:border-[3px] border-dashed border-red-300 flex items-center justify-center bg-white shadow-sm">
                            <span class="text-[8px] md:text-[9px] font-extrabold text-red-600"><?= $tren_telat; ?>%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-[1.25rem] shadow-sm overflow-hidden mb-6 md:mb-10 w-full">
                <div class="flex justify-between items-center p-4 md:p-6 border-b border-gray-50 bg-gray-50/30">
                    <h3 class="font-bold text-xs md:text-[13px] text-navy uppercase tracking-wide font-heading">Transaksi Terbaru</h3>
                    <a href="transaksi.php" class="text-[10px] md:text-xs font-bold text-gray-400 hover:text-navy flex items-center gap-1 transition-colors">
                        Lihat Semua <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
                
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left text-sm whitespace-nowrap md:whitespace-normal">
                        <thead class="bg-gray-50/50 text-gray-400 text-[10px] uppercase tracking-widest font-bold border-b border-gray-100">
                            <tr>
                                <th class="py-3 md:py-4 px-4 md:px-6">ID</th>
                                <th class="py-3 md:py-4 px-4 md:px-6">AKTIVITAS</th>
                                <th class="py-3 md:py-4 px-4 md:px-6">PEMINJAM</th>
                                <th class="py-3 md:py-4 px-4 md:px-6">TANGGAL</th>
                                <th class="py-3 md:py-4 px-4 md:px-6 text-center">STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-[11px] md:text-xs">
                            <?php while($row = mysqli_fetch_assoc($q_terbaru)): ?>
                                <?php 
                                    $status_badge = '';
                                    $aktivitas = '';
                                    if ($row['status'] == 'Dipinjam') {
                                        if ($row['tgl_kembali'] < date('Y-m-d')) {
                                            $status_badge = '<span class="bg-red-50 text-red-600 py-1 md:py-1.5 px-3 md:px-4 rounded-full font-bold border border-red-100 tracking-wide text-[8px] md:text-[9px] uppercase">Terlambat</span>';
                                        } else {
                                            $status_badge = '<span class="bg-[#FEFCF3] text-yellow-600 py-1 md:py-1.5 px-3 md:px-4 rounded-full font-bold border border-yellow-100 tracking-wide text-[8px] md:text-[9px] uppercase">Dipinjam</span>';
                                        }
                                        $aktivitas = "Peminjaman Gambar Ukur " . $row['no_gambar_ukur'];
                                    } else {
                                        $status_badge = '<span class="bg-green-50 text-green-600 py-1 md:py-1.5 px-3 md:px-4 rounded-full font-bold border border-green-100 tracking-wide text-[8px] md:text-[9px] uppercase">Selesai</span>';
                                        $aktivitas = "Pengembalian Gambar Ukur " . $row['no_gambar_ukur'];
                                    }
                                ?>
                                <tr class="border-b border-gray-50 last:border-b-0 hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3 md:py-4 px-4 md:px-6 text-gray-400 font-bold">#TRX-<?= sprintf("%03d", $row['id']); ?></td>
                                    <td class="py-3 md:py-4 px-4 md:px-6 font-bold text-gray-700"><?= $aktivitas; ?></td>
                                    <td class="py-3 md:py-4 px-4 md:px-6 font-medium"><?= htmlspecialchars($row['nama_peminjam']); ?></td>
                                    <td class="py-3 md:py-4 px-4 md:px-6 font-medium"><?= date('d M Y', strtotime($row['tgl_pinjam'])); ?></td>
                                    <td class="py-3 md:py-4 px-4 md:px-6 text-center"><?= $status_badge; ?></td>
                                </tr>
                            <?php endwhile; ?>

                            <?php if(mysqli_num_rows($q_terbaru) == 0): ?>
                                <tr>
                                    <td colspan="5" class="py-8 md:py-10 text-center text-gray-400 font-bold italic text-xs">Belum ada aktivitas transaksi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_halaman > 1): ?>
                <div class="flex justify-center items-center py-4 bg-gray-50/30 gap-1 md:gap-2">
                    <?php if ($halaman_aktif > 1): ?>
                        <a href="?halaman=<?= $halaman_aktif - 1 ?>" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-200 transition-colors">
                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                        <a href="?halaman=<?= $i ?>" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center rounded-lg text-[10px] md:text-xs font-bold transition-all <?= $i == $halaman_aktif ? 'bg-yellow-brand text-[#110B45] shadow-sm ring-1 ring-yellow-400' : 'text-gray-500 hover:bg-gray-200' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($halaman_aktif < $total_halaman): ?>
                        <a href="?halaman=<?= $halaman_aktif + 1 ?>" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-200 transition-colors">
                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function toggleMaster() {
            const submenu = document.getElementById('master-submenu');
            const chevron = document.getElementById('chevron-icon');
            submenu.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }
    </script>
</body>
</html>
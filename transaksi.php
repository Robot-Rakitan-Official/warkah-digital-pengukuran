<?php
require 'config.php';

// Proteksi Login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$page = 'transaksi';
$sub_page = ''; 
$user_role = $_SESSION['role'];

// =========================================================================
// FITUR FILTER TAB, PENCARIAN & PAGINATION
// =========================================================================
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dipinjam';
$status_filter = ($tab == 'dikembalikan') ? 'Dikembalikan' : 'Dipinjam';

$search = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';
$where = "WHERE t.status = '$status_filter'";
if ($search) {
    $where .= " AND (t.no_gambar_ukur LIKE '%$search%' OR t.nama_peminjam LIKE '%$search%')";
}

$batas_data = 5; 
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman_aktif - 1) * $batas_data;

$query_count = "SELECT COUNT(*) AS total FROM transaksi t $where";
$result_count = mysqli_query($conn, $query_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_data = $row_count['total'];
$total_halaman = ceil($total_data / $batas_data);

$query = "SELECT t.*, g.tahun FROM transaksi t LEFT JOIN gambar_ukur g ON t.no_gambar_ukur = g.no_gambar_ukur $where ORDER BY t.id DESC LIMIT $batas_data OFFSET $offset";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Warkah Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&family=Public+Sans:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        .font-heading { font-family: 'Public Sans', sans-serif; }
        .bg-navy { background-color: #110B45; }
        .bg-yellow-brand { background-color: #FACC15; }
        .text-navy { color: #110B45; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.2s ease-out forwards; }
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

                <a href="transaksi.php" class="flex items-center gap-3 py-3 px-4 rounded-xl text-sm transition-all bg-yellow-brand text-navy font-bold shadow-lg">
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
            <h1 class="text-base md:text-xl font-bold font-heading text-navy uppercase tracking-tight truncate hidden sm:block">Warkah Digital Pengukuran</h1>
            <div class="flex items-center gap-2 md:gap-3 font-semibold bg-gray-100 px-3 md:px-4 py-1.5 rounded-full text-[10px] md:text-xs text-gray-600 ml-auto sm:ml-0">
                <span>Halo, <?= htmlspecialchars($_SESSION['nama']); ?></span>
                <span class="bg-[#110B45] text-white text-[8px] md:text-[9px] px-2 py-0.5 rounded-full uppercase font-bold"><?= htmlspecialchars($user_role); ?></span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-10 bg-gray-50/30 w-full">
            <h2 class="text-xl md:text-2xl font-bold font-heading text-navy">Transaksi</h2>
            <p class="text-xs md:text-sm text-gray-500 mb-6 font-medium">Kelola data peminjaman dan pengembalian warkah ukur.</p>

            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm pb-4 w-full">
                <div class="flex flex-col xl:flex-row justify-between items-stretch xl:items-center p-4 md:p-6 border-b border-gray-50 gap-4">
                    
                    <div class="flex bg-gray-100 p-1.5 rounded-xl w-full sm:w-max overflow-x-auto">
                        <a href="transaksi.php?tab=dipinjam" class="<?= $tab=='dipinjam' ? 'bg-white shadow text-navy font-bold' : 'text-gray-500 hover:text-gray-700' ?> px-4 md:px-6 py-2 rounded-lg text-xs md:text-sm transition-all flex items-center justify-center gap-2 flex-1 sm:flex-none whitespace-nowrap">
                            Dipinjam
                        </a>
                        <a href="transaksi.php?tab=dikembalikan" class="<?= $tab=='dikembalikan' ? 'bg-white shadow text-navy font-bold' : 'text-gray-500 hover:text-gray-700' ?> px-4 md:px-6 py-2 rounded-lg text-xs md:text-sm transition-all flex items-center justify-center gap-2 flex-1 sm:flex-none whitespace-nowrap">
                            Dikembalikan
                        </a>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
                        <form method="GET" class="relative w-full sm:w-64">
                            <input type="hidden" name="tab" value="<?= $tab ?>">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="cari" value="<?= htmlspecialchars($search); ?>" placeholder="Cari transaksi..." class="w-full pl-10 p-2.5 border border-gray-200 rounded-xl text-xs md:text-sm focus:outline-none focus:border-navy transition-all">
                        </form>
                        <a href="tambah_transaksi.php" class="bg-navy text-white px-4 md:px-5 py-2.5 rounded-xl text-xs md:text-sm font-bold hover:bg-opacity-90 transition-all shadow-md flex items-center justify-center gap-2 whitespace-nowrap">
                            <span class="text-lg">+</span> Peminjaman Baru
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-50/50 text-gray-500 text-[10px] uppercase font-bold tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="py-4 px-4 md:px-6">NO</th>
                                <th class="py-4 px-4 md:px-6">NO. GAMBAR UKUR</th>
                                <th class="py-4 px-4 md:px-6 text-center">TAHUN</th>
                                <th class="py-4 px-4 md:px-6">PEMINJAM</th>
                                <th class="py-4 px-4 md:px-6">TGL. PINJAM</th>
                                <th class="py-4 px-4 md:px-6">TGL. KEMBALI</th>
                                <th class="py-4 px-4 md:px-6">KEPERLUAN</th>
                                <th class="py-4 px-4 md:px-6 text-center">STATUS</th>
                                <th class="py-4 px-4 md:px-6 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-[11px] md:text-xs font-medium">
                            <?php 
                            $no = $offset + 1; 
                            while($row = mysqli_fetch_assoc($result)): 
                            ?>
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                <td class="py-3 md:py-4 px-4 md:px-6 text-gray-400 font-bold"><?= $no++; ?>.</td>
                                <td class="py-3 md:py-4 px-4 md:px-6 font-bold text-navy text-xs md:text-sm"><?= htmlspecialchars($row['no_gambar_ukur']); ?></td>
                                <td class="py-3 md:py-4 px-4 md:px-6 text-center text-gray-500"><?= $row['tahun'] ? $row['tahun'] : '-'; ?></td>
                                <td class="py-3 md:py-4 px-4 md:px-6">
                                    <span class="block font-bold text-gray-700"><?= htmlspecialchars($row['nama_peminjam']); ?></span>
                                    <span class="text-[8px] md:text-[9px] text-gray-400 font-bold mt-0.5 block tracking-wider"><?= htmlspecialchars($row['nip_nik']); ?></span>
                                </td>
                                <td class="py-3 md:py-4 px-4 md:px-6 text-gray-500"><?= date('d M Y', strtotime($row['tgl_pinjam'])); ?></td>
                                <td class="py-3 md:py-4 px-4 md:px-6 <?= ($row['status'] == 'Dipinjam' && $row['tgl_kembali'] < date('Y-m-d')) ? 'text-red-600 font-bold' : 'text-gray-500' ?>">
                                    <?= date('d M Y', strtotime($row['tgl_kembali'])); ?>
                                </td>
                                <td class="py-3 md:py-4 px-4 md:px-6 max-w-[150px] md:max-w-[200px] truncate" title="<?= htmlspecialchars($row['keperluan']); ?>"><?= htmlspecialchars($row['keperluan']); ?></td>
                                <td class="py-3 md:py-4 px-4 md:px-6 text-center">
                                    <?php 
                                        if($row['status'] == 'Dipinjam') {
                                            if($row['tgl_kembali'] < date('Y-m-d')) {
                                                echo '<span class="bg-red-50 text-red-600 border border-red-100 py-1.5 px-3 rounded-md text-[8px] md:text-[9px] font-bold tracking-widest uppercase">Terlambat</span>';
                                            } else {
                                                echo '<span class="bg-orange-50 text-orange-600 border border-orange-100 py-1.5 px-3 rounded-md text-[8px] md:text-[9px] font-bold tracking-widest uppercase">Dipinjam</span>';
                                            }
                                        } else {
                                            echo '<span class="bg-green-50 text-green-600 border border-green-100 py-1.5 px-3 rounded-md text-[8px] md:text-[9px] font-bold tracking-widest uppercase">Selesai</span>';
                                        }
                                    ?>
                                </td>
                                <td class="py-3 md:py-4 px-4 md:px-6 text-center flex justify-center gap-2 md:gap-3">
                                    <?php if($row['status'] == 'Dipinjam'): ?>
                                        <a href="#" onclick="bukaModalKembalikan(event, 'kembalikan.php?id=<?= $row['id']; ?>')" class="text-gray-400 hover:text-navy transition-colors bg-white border border-gray-100 p-1.5 rounded-lg shadow-sm" title="Kembalikan Dokumen">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="edit_transaksi.php?id=<?= $row['id']; ?>" class="text-gray-400 hover:text-navy transition-colors bg-white border border-gray-100 p-1.5 rounded-lg shadow-sm" title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    
                                    <a href="#" onclick="bukaModalHapus(event, 'hapus_transaksi.php?id=<?= $row['id']; ?>')" class="text-gray-400 hover:text-red-600 transition-colors bg-white border border-gray-100 p-1.5 rounded-lg shadow-sm" title="Hapus Riwayat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            
                            <?php if(mysqli_num_rows($result) == 0): ?>
                            <tr>
                                <td colspan="9" class="py-10 text-center text-gray-400 font-bold italic text-xs">Belum ada riwayat transaksi.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_halaman > 1): ?>
                <div class="flex justify-center items-center pt-6 pb-2 gap-1 md:gap-2 border-t border-gray-50 mt-2">
                    <?php if ($halaman_aktif > 1): ?>
                        <a href="?tab=<?= $tab ?>&halaman=<?= $halaman_aktif - 1 ?><?= $search ? '&cari='.$search : '' ?>" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-200 transition-colors">
                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                        <a href="?tab=<?= $tab ?>&halaman=<?= $i ?><?= $search ? '&cari='.$search : '' ?>" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center rounded-lg text-[10px] md:text-xs font-bold transition-all <?= $i == $halaman_aktif ? 'bg-yellow-brand text-[#110B45] shadow-sm ring-1 ring-yellow-400' : 'text-gray-500 hover:bg-gray-200' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($halaman_aktif < $total_halaman): ?>
                        <a href="?tab=<?= $tab ?>&halaman=<?= $halaman_aktif + 1 ?><?= $search ? '&cari='.$search : '' ?>" class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-200 transition-colors">
                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="px-4 md:px-6 py-2 flex items-center gap-2 text-gray-400 text-[9px] md:text-[11px] font-bold uppercase tracking-wider">
                    <svg class="w-3 h-3 md:w-4 md:h-4 text-[#110B45]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Klik ikon mata untuk mengembalikan Gambar Ukur
                </div>

            </div>
        </main>
    </div>

    <div id="modalKembalikan" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm transition-all no-print">
        <div class="bg-white rounded-[1.5rem] p-6 max-w-sm w-full mx-4 shadow-2xl border border-gray-100 animate-fade-in text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-[#110B45]"></div>
            <div class="w-16 h-16 bg-indigo-50 text-[#110B45] rounded-full flex items-center justify-center mx-auto mb-4 border-[4px] border-white shadow-sm ring-1 ring-indigo-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="font-extrabold text-[#110B45] text-lg mb-2 font-['Public_Sans']">Proses Pengembalian?</h3>
            <p class="text-[11px] text-gray-500 font-medium mb-6 leading-relaxed">Apakah Anda yakin ingin menyelesaikan transaksi ini dan memproses pengembalian warkah ke arsip?</p>
            <div class="flex justify-center gap-3">
                <button onclick="tutupModalKembalikan()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-all w-1/2">
                    Tidak, Batal
                </button>
                <button onclick="eksekusiKembalikan()" class="px-5 py-2.5 bg-[#110B45] hover:bg-opacity-90 text-white rounded-xl text-xs font-bold transition-all shadow-md w-1/2">
                    Ya, Proses
                </button>
            </div>
        </div>
    </div>

    <div id="modalHapus" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm transition-all no-print">
        <div class="bg-white rounded-[1.5rem] p-6 max-w-sm w-full mx-4 shadow-2xl border border-gray-100 animate-fade-in text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-red-600"></div>
            <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 border-[4px] border-white shadow-sm ring-1 ring-red-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <h3 class="font-extrabold text-[#110B45] text-lg mb-2 font-['Public_Sans']">Hapus Riwayat?</h3>
            <p class="text-[11px] text-gray-500 font-medium mb-6 leading-relaxed">Anda yakin ingin menghapus data riwayat transaksi ini secara permanen?</p>
            <div class="flex justify-center gap-3">
                <button onclick="tutupModalHapus()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-all w-1/2">
                    Tidak, Batal
                </button>
                <button onclick="eksekusiHapus()" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition-all shadow-md w-1/2">
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

        let urlTargetKembalikan = '';
        function bukaModalKembalikan(event, url) {
            event.preventDefault(); 
            urlTargetKembalikan = url; 
            document.getElementById('modalKembalikan').classList.remove('hidden'); 
        }
        function tutupModalKembalikan() {
            document.getElementById('modalKembalikan').classList.add('hidden'); 
            urlTargetKembalikan = ''; 
        }
        function eksekusiKembalikan() {
            if (urlTargetKembalikan) {
                window.location.href = urlTargetKembalikan; 
            }
        }

        let urlTargetHapus = '';
        function bukaModalHapus(event, url) {
            event.preventDefault(); 
            urlTargetHapus = url; 
            document.getElementById('modalHapus').classList.remove('hidden'); 
        }
        function tutupModalHapus() {
            document.getElementById('modalHapus').classList.add('hidden'); 
            urlTargetHapus = ''; 
        }
        function eksekusiHapus() {
            if (urlTargetHapus) {
                window.location.href = urlTargetHapus; 
            }
        }
    </script>
</body>
</html>
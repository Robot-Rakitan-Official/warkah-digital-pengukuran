<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$page = 'laporan';
$sub_page = ''; 
$user_role = $_SESSION['role'];

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'gu'; 
$search = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';

$batas_data = 5; 
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman_aktif - 1) * $batas_data;

if ($tab == 'gu') {
    $where = $search ? "WHERE no_gambar_ukur LIKE '%$search%' OR kecamatan LIKE '%$search%'" : "";
    $query_count = "SELECT COUNT(*) AS total FROM gambar_ukur $where";
    $query = "SELECT * FROM gambar_ukur $where ORDER BY id DESC LIMIT $batas_data OFFSET $offset";
} elseif ($tab == 'peminjaman') {
    $where = "WHERE t.status = 'Dipinjam'";
    if ($search) $where .= " AND (t.no_gambar_ukur LIKE '%$search%' OR t.nama_peminjam LIKE '%$search%')";
    $query_count = "SELECT COUNT(*) AS total FROM transaksi t $where";
    $query = "SELECT t.*, g.tahun, g.kecamatan, g.desa_kelurahan FROM transaksi t LEFT JOIN gambar_ukur g ON t.no_gambar_ukur = g.no_gambar_ukur $where ORDER BY t.id DESC LIMIT $batas_data OFFSET $offset";
} else { 
    $where = "WHERE t.status = 'Dikembalikan'";
    if ($search) $where .= " AND (t.no_gambar_ukur LIKE '%$search%' OR t.nama_peminjam LIKE '%$search%')";
    $query_count = "SELECT COUNT(*) AS total FROM transaksi t $where";
    $query = "SELECT t.*, g.tahun, g.kecamatan, g.desa_kelurahan FROM transaksi t LEFT JOIN gambar_ukur g ON t.no_gambar_ukur = g.no_gambar_ukur $where ORDER BY t.id DESC LIMIT $batas_data OFFSET $offset";
}

$result_count = mysqli_query($conn, $query_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_data = $row_count['total'];
$total_halaman = ceil($total_data / $batas_data);

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Warkah Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Inter:wght@400;500;600&family=Public+Sans:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        .font-heading { font-family: 'Public Sans', sans-serif; }
        .bg-navy { background-color: #110B45; }
        .bg-yellow-brand { background-color: #FACC15; }
        .text-navy { color: #110B45; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .print-area { width: 100%; padding: 0; }
            .bg-white { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="flex flex-col md:flex-row h-screen overflow-hidden text-gray-800">
    
    <div class="md:hidden bg-[#110B45] text-white p-4 flex justify-between items-center shadow-md z-50 no-print">
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

                <a href="transaksi.php" class="flex items-center gap-3 py-3 px-4 rounded-xl text-sm transition-all text-gray-400 hover:text-white font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Transaksi
                </a>

                <a href="laporan.php" class="flex items-center gap-3 py-3 px-4 rounded-xl text-sm font-bold transition-all bg-yellow-brand text-navy shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Laporan
                </a>

                <a href="pengaturan.php" class="flex items-center gap-3 py-3 px-4 rounded-xl text-sm transition-all text-gray-400 hover:text-white font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Pengaturan
                </a>
            </nav>
        </div>
        
        <div class="p-6 md:p-8 no-print">
            <a href="logout.php" class="flex items-center gap-3 text-gray-400 hover:text-white text-sm font-semibold transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Keluar
            </a>
        </div>
    </div>

    <div class="flex-1 flex flex-col overflow-hidden w-full relative z-0 print-area">
        <header class="bg-white px-4 md:px-8 py-4 md:py-5 flex justify-between items-center shadow-sm z-0 no-print">
            <h1 class="text-base md:text-xl font-bold font-heading text-navy uppercase tracking-tight truncate hidden sm:block">Warkah Digital Pengukuran</h1>
            <div class="flex items-center gap-2 md:gap-3 font-semibold bg-gray-100 px-3 md:px-4 py-1.5 rounded-full text-[10px] md:text-xs text-gray-600 ml-auto sm:ml-0">
                <span>Halo, <?= htmlspecialchars($_SESSION['nama']); ?></span>
                <span class="bg-[#110B45] text-white text-[8px] md:text-[9px] px-2 py-0.5 rounded-full uppercase font-bold"><?= htmlspecialchars($user_role); ?></span>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 bg-gray-50/30 w-full">
            <h2 class="text-xl md:text-2xl font-bold font-heading text-navy">Laporan</h2>
            <p class="text-xs md:text-sm text-gray-500 mb-6 font-medium no-print">Kelola dan unduh riwayat aktivitas dokumen pengukuran.</p>

            <div class="bg-white border rounded-xl shadow-sm p-4 md:p-6 w-full">
                <div class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center mb-6 no-print gap-4">
                    
                    <div class="flex bg-gray-100 p-1.5 rounded-xl overflow-x-auto w-full lg:w-auto">
                        <a href="laporan.php?tab=gu" class="<?= $tab=='gu' ? 'bg-white shadow text-navy font-bold' : 'text-gray-500 hover:text-gray-700' ?> px-4 md:px-5 py-2.5 rounded-lg text-[10px] md:text-xs transition-all whitespace-nowrap text-center flex-1 lg:flex-none">Data Gambar Ukur</a>
                        <a href="laporan.php?tab=peminjaman" class="<?= $tab=='peminjaman' ? 'bg-white shadow text-navy font-bold' : 'text-gray-500 hover:text-gray-700' ?> px-4 md:px-5 py-2.5 rounded-lg text-[10px] md:text-xs transition-all whitespace-nowrap text-center flex-1 lg:flex-none">Peminjaman</a>
                        <a href="laporan.php?tab=pengembalian" class="<?= $tab=='pengembalian' ? 'bg-white shadow text-navy font-bold' : 'text-gray-500 hover:text-gray-700' ?> px-4 md:px-5 py-2.5 rounded-lg text-[10px] md:text-xs transition-all whitespace-nowrap text-center flex-1 lg:flex-none">Pengembalian</a>
                    </div>

                    <div class="flex gap-2 w-full lg:w-auto">
                        <button onclick="window.print()" class="flex-1 lg:flex-none flex justify-center items-center gap-2 border border-gray-300 px-4 py-2.5 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak
                        </button>
                        <a href="export_csv.php?tab=<?= $tab ?>" class="flex-1 lg:flex-none flex justify-center items-center gap-2 bg-navy text-white px-4 py-2.5 rounded-lg text-xs font-bold hover:bg-opacity-90 transition-all shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Download CSV
                        </a>
                    </div>
                </div>

                <form method="GET" class="relative w-full mb-6 no-print">
                    <input type="hidden" name="tab" value="<?= $tab ?>">
                    <div class="absolute inset-y-0 left-0 pl-3 md:pl-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="cari" value="<?= htmlspecialchars($search); ?>" placeholder="Cari nomor gambar ukur atau peminjam..." class="w-full pl-9 md:pl-11 p-2.5 md:p-3 border border-gray-200 rounded-xl text-xs focus:outline-none focus:border-navy transition-all">
                </form>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left text-[10px] md:text-[11px] whitespace-nowrap">
                        <thead class="bg-gray-50 text-gray-500 font-bold tracking-widest uppercase border-y">
                            <tr>
                                <th class="py-3 md:py-4 px-3 md:px-4">NO</th>
                                <th class="py-3 md:py-4 px-3 md:px-4">NO. GAMBAR UKUR</th>
                                <th class="py-3 md:py-4 px-3 md:px-4 text-center">TAHUN</th>
                                <?php if($tab != 'gu'): ?>
                                    <th class="py-3 md:py-4 px-3 md:px-4">PEMINJAM</th>
                                    <th class="py-3 md:py-4 px-3 md:px-4 text-center">TGL. PINJAM</th>
                                    <th class="py-3 md:py-4 px-3 md:px-4 text-center">TGL. KEMBALI</th>
                                    <th class="py-3 md:py-4 px-3 md:px-4">KEPERLUAN</th>
                                <?php else: ?>
                                    <th class="py-3 md:py-4 px-3 md:px-4">KECAMATAN</th>
                                    <th class="py-3 md:py-4 px-3 md:px-4">DESA/KELURAHAN</th>
                                    <th class="py-3 md:py-4 px-3 md:px-4 text-center">LEMARI RAK</th>
                                <?php endif; ?>
                                <th class="py-3 md:py-4 px-3 md:px-4 text-center">STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 font-medium">
                            <?php 
                            $no = $offset + 1;
                            while($row = mysqli_fetch_assoc($result)): 
                            ?>
                            <tr class="border-b hover:bg-gray-50 transition-colors">
                                <td class="py-3 md:py-4 px-3 md:px-4 text-gray-400"><?= $no++; ?>.</td>
                                <td class="py-3 md:py-4 px-3 md:px-4 font-bold text-navy"><?= htmlspecialchars($row['no_gambar_ukur']); ?></td>
                                <td class="py-3 md:py-4 px-3 md:px-4 text-center"><?= $row['tahun'] ? $row['tahun'] : '-'; ?></td>
                                
                                <?php if($tab != 'gu'): ?>
                                    <td class="py-3 md:py-4 px-3 md:px-4"><?= htmlspecialchars($row['nama_peminjam']); ?></td>
                                    <td class="py-3 md:py-4 px-3 md:px-4 text-center"><?= date('d M Y', strtotime($row['tgl_pinjam'])); ?></td>
                                    <td class="py-3 md:py-4 px-3 md:px-4 text-center <?= ($tab=='peminjaman' && $row['tgl_kembali'] < date('Y-m-d')) ? 'text-red-500 font-bold' : '' ?>">
                                        <?= date('d M Y', strtotime($row['tgl_kembali'])); ?>
                                    </td>
                                    <td class="py-3 md:py-4 px-3 md:px-4 italic max-w-[150px] md:max-w-[250px] truncate" title="<?= htmlspecialchars($row['keperluan']); ?>"><?= htmlspecialchars($row['keperluan']); ?></td>
                                <?php else: ?>
                                    <td class="py-3 md:py-4 px-3 md:px-4"><?= htmlspecialchars($row['kecamatan']); ?></td>
                                    <td class="py-3 md:py-4 px-3 md:px-4"><?= htmlspecialchars($row['desa_kelurahan']); ?></td>
                                    <td class="py-3 md:py-4 px-3 md:px-4 text-center">
                                        <span class="bg-gray-100 text-gray-500 px-2 md:px-3 py-1 rounded text-[9px] md:text-[10px] font-bold"><?= htmlspecialchars($row['lemari_rak']); ?></span>
                                    </td>
                                <?php endif; ?>

                                <td class="py-3 md:py-4 px-3 md:px-4 text-center">
                                    <?php 
                                        if($tab == 'gu') {
                                            $color = ($row['status'] == 'Tersedia') ? 'green' : 'red';
                                            echo "<span class='bg-{$color}-50 text-{$color}-600 border border-{$color}-100 py-1 px-3 md:px-4 rounded-full text-[8px] md:text-[9px] font-bold tracking-widest'>".strtoupper($row['status'])."</span>";
                                        } elseif($tab == 'peminjaman') {
                                            if($row['tgl_kembali'] < date('Y-m-d')) echo '<span class="bg-red-50 text-red-600 border border-red-100 py-1 px-3 md:px-4 rounded-full text-[8px] md:text-[9px] font-bold tracking-widest">TERLAMBAT</span>';
                                            else echo '<span class="bg-orange-50 text-orange-600 border border-orange-100 py-1 px-3 md:px-4 rounded-full text-[8px] md:text-[9px] font-bold tracking-widest">DIPINJAM</span>';
                                        } else {
                                            echo '<span class="bg-indigo-50 text-indigo-600 border border-indigo-100 py-1 px-3 md:px-4 rounded-full text-[8px] md:text-[9px] font-bold tracking-widest">KEMBALI</span>';
                                        }
                                    ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>

                            <?php if(mysqli_num_rows($result) == 0): ?>
                            <tr>
                                <td colspan="8" class="py-8 md:py-12 text-center text-gray-400 font-bold italic text-xs">Data tidak ditemukan.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_halaman > 1): ?>
                <div class="flex justify-center items-center pt-4 md:pt-6 pb-2 gap-1 md:gap-2 border-t border-gray-50 mt-2 no-print">
                    
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
<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_peminjam']);
    $nip = mysqli_real_escape_string($conn, $_POST['nip_nik']);
    $status_peminjam = mysqli_real_escape_string($conn, $_POST['status_peminjam']);
    
    $no_gu = mysqli_real_escape_string($conn, $_POST['no_gambar_ukur']);
    $tgl_pinjam = mysqli_real_escape_string($conn, $_POST['tgl_pinjam']);
    $tgl_kembali = mysqli_real_escape_string($conn, $_POST['tgl_kembali']);
    $keperluan = mysqli_real_escape_string($conn, $_POST['keperluan']);

    // Cek apakah Gambar Ukur ada dan Tersedia
    $cek_gu = mysqli_query($conn, "SELECT status FROM gambar_ukur WHERE no_gambar_ukur = '$no_gu'");
    
    if (mysqli_num_rows($cek_gu) > 0) {
        $data_gu = mysqli_fetch_assoc($cek_gu);
        if ($data_gu['status'] == 'Tersedia') {
            // Masukkan ke tabel transaksi
            $q_insert = "INSERT INTO transaksi (no_gambar_ukur, nama_peminjam, nip_nik, status_peminjam, tgl_pinjam, tgl_kembali, keperluan) 
                         VALUES ('$no_gu', '$nama', '$nip', '$status_peminjam', '$tgl_pinjam', '$tgl_kembali', '$keperluan')";
            
            // Ubah status gambar ukur jadi Dipinjam
            $q_update = "UPDATE gambar_ukur SET status = 'Dipinjam' WHERE no_gambar_ukur = '$no_gu'";

            if (mysqli_query($conn, $q_insert) && mysqli_query($conn, $q_update)) {
                header("Location: transaksi.php"); exit();
            }
        } else {
            $error = "Gambar Ukur tersebut sedang dipinjam atau tidak tersedia!";
        }
    } else {
        $error = "Nomor Gambar Ukur tidak ditemukan di sistem!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peminjaman - Warkah Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Public+Sans:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style> body { background-color: #F8FAFC; font-family: 'Inter', sans-serif;} .bg-navy { background-color: #110B45; } </style>
</head>
<body class="flex items-center justify-center p-4 md:p-10 min-h-screen">
    <div class="bg-white rounded-xl shadow-sm border w-full max-w-4xl overflow-hidden">
        
        <div class="p-6 md:p-8 pb-4">
            <h2 class="text-xl md:text-2xl font-bold font-['Public_Sans'] text-[#110B45] mb-1">Tambah Peminjaman Baru</h2>
            <p class="text-xs md:text-sm text-gray-500 mb-6">Silakan lengkapi formulir di bawah ini untuk mencatat transaksi peminjaman dokumen gambar ukur baru dari sistem pengarsipan.</p>
            <?php if(isset($error)) echo "<p class='text-red-500 text-xs md:text-sm mb-4 bg-red-50 p-3 rounded-lg border border-red-100'>$error</p>"; ?>
        </div>

        <form method="POST" class="p-6 md:p-8 pt-0">
            
            <div class="mb-8">
                <h3 class="flex items-center gap-2 text-sm font-bold text-navy mb-4 border-b pb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Informasi Peminjam
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Nama Peminjam</label>
                        <input type="text" name="nama_peminjam" placeholder="Masukkan nama lengkap" class="w-full bg-[#F8FAFC] border border-gray-200 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45]" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">NIP/NIK</label>
                        <input type="text" name="nip_nik" placeholder="Masukkan NIP atau NIK" class="w-full bg-[#F8FAFC] border border-gray-200 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45]" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Status</label>
                    <select name="status_peminjam" class="w-full bg-[#F8FAFC] border border-gray-200 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45]" required>
                        <option value="">Pilih status</option>
                        <option value="ASN BPN">ASN BPN</option>
                        <option value="Non-ASN BPN">Non-ASN BPN</option>
                        <option value="Masyarakat / Umum">Masyarakat / Umum</option>
                    </select>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="flex items-center gap-2 text-sm font-bold text-navy mb-4 border-b pb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Detail Dokumen & Waktu
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">No. Gambar Ukur (GU)</label>
                        <div class="flex gap-2">
                            <input type="text" name="no_gambar_ukur" id="no_gambar_ukur" placeholder="Scan QR / Ketik" class="w-full bg-[#F8FAFC] border border-gray-200 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45]" required>
                            <button type="button" onclick="bukaScanner('no_gambar_ukur')" class="bg-[#110B45] text-white px-3 rounded-lg hover:bg-opacity-90 transition-colors flex items-center justify-center shrink-0 shadow-sm" title="Scan QR Code">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Tanggal Pinjam</label>
                        <input type="date" name="tgl_pinjam" class="w-full bg-[#F8FAFC] border border-gray-200 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45]" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Estimasi Tgl. Kembali</label>
                        <input type="date" name="tgl_kembali" class="w-full bg-[#F8FAFC] border border-gray-200 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45]" required>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Keperluan</label>
                    <textarea name="keperluan" rows="3" placeholder="Jelaskan alasan atau tujuan peminjaman dokumen secara mendetail..." class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-lg text-sm focus:outline-none focus:border-[#110B45]" required></textarea>
                </div>
            </div>

            <div class="flex flex-col-reverse md:flex-row justify-end gap-3 pt-6 bg-gray-50 -mx-6 md:-mx-8 -mb-6 md:-mb-8 px-6 md:px-8 py-5 border-t">
                <a href="transaksi.php" class="text-center px-6 py-2.5 border border-gray-300 bg-white rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" name="simpan" class="justify-center bg-navy text-white px-6 py-2.5 rounded-lg text-sm font-bold hover:bg-opacity-90 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan Peminjaman
                </button>
            </div>
        </form>
    </div>

    <div id="modalScanner" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm transition-all">
        <div class="bg-white rounded-[1.5rem] p-6 w-full max-w-sm mx-4 shadow-2xl relative">
            <h3 class="text-lg font-bold text-[#110B45] mb-4 text-center font-['Public_Sans']">Scan QR Code</h3>
            <div id="qr-reader" class="w-full mb-4 rounded-xl overflow-hidden border-[3px] border-dashed border-[#110B45]"></div>
            <p class="text-center text-xs text-gray-500 mb-4 font-medium">Arahkan kamera ke QR Code pada dokumen fisik Gambar Ukur.</p>
            <button type="button" onclick="tutupScanner()" class="w-full bg-gray-100 text-gray-700 font-bold py-3 rounded-xl hover:bg-gray-200 transition-colors">Tutup Kamera</button>
        </div>
    </div>

    <script>
        let html5QrcodeScanner;
        let targetInputId = '';

        function bukaScanner(inputId) {
            targetInputId = inputId;
            document.getElementById('modalScanner').classList.remove('hidden');
            
            html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader",
                { fps: 10, qrbox: { width: 250, height: 250 } },
                false
            );
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }

        function tutupScanner() {
            document.getElementById('modalScanner').classList.add('hidden');
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().catch(error => console.error("Gagal menutup scanner.", error));
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            document.getElementById(targetInputId).value = decodedText;
            tutupScanner(); 
        }

        function onScanFailure(error) {
            // Diabaikan agar console tidak penuh saat kamera sedang mencari QR
        }
    </script>
</body>
</html>
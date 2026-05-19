<?php
require 'config.php';

// Proteksi Keamanan Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Variabel penanda untuk memunculkan pop-up modal figma style
$tampil_modal_sukses = false;
$username_akun_baru = "";
$error = null;

if (isset($_POST['simpan'])) {
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Tangkap inputan username baru
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    // Validasi: Cek apakah username sudah dipakai orang lain di tabel users
    $cek_username = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    
    if (mysqli_num_rows($cek_username) > 0) {
        $error = "Username '$username' sudah terpakai! Silakan gunakan username lain.";
    } else {
        // 1. Simpan Biodata ke tabel Anggota
        $query_anggota = "INSERT INTO anggota (nip, nama_lengkap, jabatan, email, no_telepon, status) 
                          VALUES ('$nip', '$nama_lengkap', '$jabatan', '$email', '$no_telepon', '$status')";
        $eksekusi_anggota = mysqli_query($conn, $query_anggota);

        if ($eksekusi_anggota) {
            // 2. Buat Akun di tabel Users
            $password_default = md5('123456'); 
            
            // PERBAIKAN: Masukkan $username ke tabel users. 
            // Pastikan tabel 'users' milikmu di database memiliki kolom 'username'
            $query_akun = "INSERT INTO users (username, nama_lengkap, email, password, role) 
                           VALUES ('$username', '$nama_lengkap', '$email', '$password_default', 'karyawan')";
            mysqli_query($conn, $query_akun);

            // Nyalakan trigger modal pop-up
            $tampil_modal_sukses = true;
            $username_akun_baru = $username;
        } else {
            $error = "Terjadi kesalahan sistem. Gagal menyimpan data anggota!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Anggota - Warkah Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&family=Public+Sans:wght@700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; } 
        .bg-navy { background-color: #110B45; } 
        /* Animasi pop-up muncul perlahan */
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    </style>
</head>
<body class="flex items-center justify-center p-10 min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 w-full max-w-4xl relative z-10">
        <h2 class="text-2xl font-bold text-[#110B45] mb-2 font-['Public_Sans']">Tambah Anggota & Akun</h2>
        <p class="text-sm text-gray-400 mb-8 font-medium">Lengkapi biodata anggota dan tentukan username login untuk sistem.</p>

<?php if(isset($error) && $error != null) echo "<p class='text-red-500 text-sm font-bold mb-6 bg-red-50 border border-red-100 p-4 rounded-xl flex items-center gap-2'><svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'></path></svg>$error</p>"; ?>

        <form method="POST">
            <div class="mb-2"><span class="bg-gray-100 text-gray-500 px-3 py-1 rounded text-[10px] font-bold uppercase tracking-wider">1. Biodata Profil</span></div>
            <div class="bg-gray-50/50 border border-gray-100 p-5 rounded-xl mb-6">
                <div class="grid grid-cols-2 gap-6 mb-5">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-wide">NIP</label>
                        <input type="text" name="nip" placeholder="Masukkan NIP" class="w-full bg-white border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45] focus:ring-1 focus:ring-[#110B45]" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-wide">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" placeholder="Masukkan Nama Lengkap Sesuai KTP/ID" class="w-full bg-white border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45] focus:ring-1 focus:ring-[#110B45]" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-5">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-wide">Jabatan</label>
                        <input type="text" name="jabatan" placeholder="Posisi / Jabatan Pekerjaan" class="w-full bg-white border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45] focus:ring-1 focus:ring-[#110B45]" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-wide">Email</label>
                        <input type="email" name="email" placeholder="Alamat Email Aktif" class="w-full bg-white border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45] focus:ring-1 focus:ring-[#110B45]" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-wide">No. Telepon</label>
                        <input type="text" name="no_telepon" placeholder="Nomor HP/WhatsApp" class="w-full bg-white border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45] focus:ring-1 focus:ring-[#110B45]" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-500 mb-2 uppercase tracking-wide">Status Karyawan</label>
                        <select name="status" class="w-full bg-white border border-gray-200 p-3 rounded-xl text-sm focus:outline-none focus:border-[#110B45] focus:ring-1 focus:ring-[#110B45]" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-2"><span class="bg-indigo-50 text-indigo-500 border border-indigo-100 px-3 py-1 rounded text-[10px] font-bold uppercase tracking-wider">2. Pengaturan Akun Login</span></div>
            <div class="bg-indigo-50/30 border border-indigo-50 p-5 rounded-xl mb-8">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-bold text-indigo-500 mb-2 uppercase tracking-wide">Username Login <span class="text-red-500">*</span></label>
                        <input type="text" name="username" placeholder="cth: yusuf123 (tanpa spasi)" class="w-full bg-white border border-indigo-100 p-3 rounded-xl text-sm font-bold text-[#110B45] focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400" required>
                        <p class="text-[10px] text-gray-400 mt-1 italic">Username ini akan digunakan anggota untuk login.</p>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-indigo-500 mb-2 uppercase tracking-wide">Password Akun Baru</label>
                        <input type="text" value="123456" class="w-full bg-gray-100 border border-gray-200 p-3 rounded-xl text-sm font-bold text-gray-500 cursor-not-allowed" readonly>
                        <p class="text-[10px] text-gray-400 mt-1 italic">Password default sistem. Anggota bisa menggantinya nanti.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <a href="data_anggota.php" class="px-6 py-2.5 border border-gray-200 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 transition-all">Batal</a>
                <button type="submit" name="simpan" class="bg-navy text-white px-8 py-2.5 rounded-xl text-sm font-bold hover:bg-opacity-90 transition-all shadow-md">Simpan Anggota & Akun</button>
            </div>
        </form>
    </div>

    <?php if ($tampil_modal_sukses): ?>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm animate-fade-in no-print">
        <div class="bg-white rounded-[1.5rem] p-8 max-w-sm w-full mx-4 shadow-2xl border border-gray-100 text-center relative overflow-hidden">
            
            <div class="absolute top-0 left-0 w-full h-2 bg-green-500"></div>

            <div class="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-5 border-[4px] border-white shadow-sm ring-1 ring-green-100">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </div>
            
            <h3 class="font-extrabold text-[#110B45] text-2xl mb-2">Berhasil!</h3>
            <p class="text-sm text-gray-500 font-medium mb-6 leading-relaxed">Data anggota tersimpan dan Akun Login otomatis berhasil dibuat.</p>
            
            <div class="bg-gray-50 rounded-xl p-5 mb-8 text-left border border-gray-100 shadow-inner">
                <div class="mb-3">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Username Login</p>
                    <p class="text-sm font-bold text-[#110B45] bg-white px-3 py-1.5 rounded-lg border border-gray-200"><?= htmlspecialchars($username_akun_baru); ?></p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Password Default</p>
                    <p class="text-sm font-bold text-[#110B45] bg-white px-3 py-1.5 rounded-lg border border-gray-200 tracking-wider">123456</p>
                </div>
            </div>

            <button onclick="window.location.href='data_anggota.php'" class="w-full py-3.5 bg-[#110B45] hover:bg-opacity-90 text-white rounded-xl text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2">
                Oke, Mengerti
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>
    </div>
    <?php endif; ?>

</body>
</html>
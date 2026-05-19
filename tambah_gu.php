<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

if (isset($_POST['simpan'])) {
    $no_gu = mysqli_real_escape_string($conn, $_POST['no_gambar_ukur']);
    $tahun = mysqli_real_escape_string($conn, $_POST['tahun']);
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
    $desa = mysqli_real_escape_string($conn, $_POST['desa_kelurahan']);
    $rak = mysqli_real_escape_string($conn, $_POST['lemari_rak']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $user_id = $_SESSION['user_id'];

    $nama_file = $_FILES['file_pdf']['name'];
    $tmp_file = $_FILES['file_pdf']['tmp_name'];
    $ekstensi = strtolower(end(explode('.', $nama_file)));
    $nama_file_baru = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", $nama_file);

    if ($ekstensi === 'pdf') {
        if(move_uploaded_file($tmp_file, 'uploads/' . $nama_file_baru)){
            $query = "INSERT INTO gambar_ukur (no_gambar_ukur, tahun, kecamatan, desa_kelurahan, lemari_rak, file_pdf, keterangan, created_by) 
                      VALUES ('$no_gu', '$tahun', '$kecamatan', '$desa', '$rak', '$nama_file_baru', '$keterangan', '$user_id')";
            if (mysqli_query($conn, $query)) {
                header("Location: data_gambar_ukur.php"); exit();
            }
        }
    } else {
        $error = "Gagal! File harus berformat PDF.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Gambar Ukur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Public+Sans:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        .bg-navy { background-color: #110B45; }
        .dash-border { border: 2px dashed #CBD5E1; background-color: #F8FAFC; }
    </style>
</head>
<body class="flex items-center justify-center p-10 min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-sm border w-full max-w-3xl">
        <h2 class="text-2xl font-bold font-['Public_Sans'] text-[#110B45] mb-2">Tambah Data Gambar Ukur</h2>
        <p class="text-sm text-gray-500 mb-6">Silahkan lengkapi form di bawah ini untuk menambahkan data gambar ukur baru.</p>
        
        <?php if(isset($error)) echo "<p class='text-red-500 text-sm mb-4 bg-red-50 p-2 rounded'>$error</p>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-2 gap-6 mb-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Nomor Gambar Ukur</label>
                    <input type="text" name="no_gambar_ukur" placeholder="Masukkan nomor gambar ukur" class="w-full border border-gray-300 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45]" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Tahun Gambar Ukur</label>
                    <input type="number" name="tahun" placeholder="YYYY" class="w-full border border-gray-300 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45]" required>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-6 mb-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Kecamatan</label>
                    <input type="text" name="kecamatan" placeholder="Masukkan kecamatan" class="w-full border border-gray-300 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45]" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Desa / Kelurahan</label>
                    <input type="text" name="desa_kelurahan" placeholder="Masukkan desa/kelurahan" class="w-full border border-gray-300 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45]" required>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-700 mb-2">Lemari Rak</label>
                <input type="text" name="lemari_rak" placeholder="Contoh: R-12/A" class="w-full border border-gray-300 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45]" required>
            </div>

            <div class="mb-5">
    <label class="block text-xs font-bold text-gray-700 mb-2">File pdf</label>
    <div class="dash-border rounded-xl p-8 text-center cursor-pointer hover:bg-gray-50 transition relative">
        <input type="file" name="file_pdf" id="file_pdf" accept="application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required onchange="tampilkanNamaFile()">
        
        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
        
        <p id="text-utama" class="text-sm font-bold text-gray-700 mb-1">Klik untuk upload file .pdf</p>
        <p id="text-sub" class="text-xs text-gray-400">atau seret file ke area ini (Maks. 5MB)</p>
    </div>
</div>

            <div class="mb-8">
                <label class="block text-xs font-bold text-gray-700 mb-2">Status Keterangan</label>
                <input type="text" name="keterangan" placeholder="Tambahkan keterangan status jika perlu" class="w-full border border-gray-300 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45]">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="data_gambar_ukur.php" class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" name="simpan" class="bg-navy text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-opacity-90">Simpan</button>
            </div>
        </form>
    </div>
    <script>
function tampilkanNamaFile() {
    const inputBerkas = document.getElementById('file_pdf');
    const textUtama = document.getElementById('text-utama');
    const textSub = document.getElementById('text-sub');

    // Cek apakah ada file yang dipilih oleh user
    if (inputBerkas.files.length > 0) {
        const namaFile = inputBerkas.files[0].name; // Mengambil nama file asli
        
        // Mengubah teks UI figma menjadi informasi file terpilih
        textUtama.innerHTML = "File Berhasil Dipilih!";
        textSub.innerHTML = `<span class="inline-block bg-red-50 text-red-600 font-bold text-xs px-3 py-1.5 rounded-lg border border-red-100 mt-2 tracking-wide uppercase">${namaFile}</span>`;
    } else {
        // Kembalikan ke setelan pabrik jika batal memilih file
        textUtama.innerText = "Klik untuk upload file .pdf";
        textSub.innerText = "atau seret file ke area ini (Maks. 5MB)";
    }
}
</script>
</body>
</html>
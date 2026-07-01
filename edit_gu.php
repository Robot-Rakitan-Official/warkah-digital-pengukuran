<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM gambar_ukur WHERE id = '$id'"));

if (isset($_POST['simpan'])) {
    $no_gu = mysqli_real_escape_string($conn, $_POST['no_gambar_ukur']);
    $tahun = mysqli_real_escape_string($conn, $_POST['tahun']);
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
    $desa = mysqli_real_escape_string($conn, $_POST['desa_kelurahan']);
    $rak = mysqli_real_escape_string($conn, $_POST['lemari_rak']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    
    // Logika Edit File PDF
    $nama_file = $_FILES['file_pdf']['name'];
    $tmp_file = $_FILES['file_pdf']['tmp_name'];
    
    if ($nama_file != "") { // Jika ada file baru diupload
        $ekstensi = strtolower(end(explode('.', $nama_file)));
        if ($ekstensi === 'pdf') {
            unlink('uploads/'.$data['file_pdf']); // Hapus file lama
            $nama_file_baru = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", $nama_file);
            move_uploaded_file($tmp_file, 'uploads/' . $nama_file_baru);
            $query = "UPDATE gambar_ukur SET no_gambar_ukur='$no_gu', tahun='$tahun', kecamatan='$kecamatan', desa_kelurahan='$desa', lemari_rak='$rak', keterangan='$keterangan', file_pdf='$nama_file_baru' WHERE id='$id'";
        } else {
            $error = "File harus berformat PDF!";
        }
    } else { // Jika file tidak diganti
        $query = "UPDATE gambar_ukur SET no_gambar_ukur='$no_gu', tahun='$tahun', kecamatan='$kecamatan', desa_kelurahan='$desa', lemari_rak='$rak', keterangan='$keterangan' WHERE id='$id'";
    }

    if (!isset($error) && mysqli_query($conn, $query)) {
        header("Location: data_gambar_ukur.php"); exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Gambar Ukur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Public+Sans:wght@600;700&display=swap" rel="stylesheet">
    <style> body { background-color: #F8FAFC; } .bg-navy { background-color: #110B45; } .dash-border { border: 2px dashed #CBD5E1; } </style>
</head>
<body class="flex items-center justify-center p-10 min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-sm border w-full max-w-3xl">
        <h2 class="text-2xl font-bold font-['Public_Sans'] text-[#110B45] mb-2">Edit Data Gambar Ukur</h2>
        <p class="text-sm text-gray-500 mb-6">Silahkan sesuaikan form di bawah ini.</p>
        
        <?php if(isset($error)) echo "<p class='text-red-500 text-sm mb-4'>$error</p>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-2 gap-6 mb-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Nomor Gambar Ukur</label>
                    <input type="text" name="no_gambar_ukur" value="<?= $data['no_gambar_ukur']; ?>" class="w-full border p-2.5 rounded-lg text-sm focus:border-[#110B45]" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Tahun Gambar Ukur</label>
                    <input type="number" name="tahun" value="<?= $data['tahun']; ?>" class="w-full border p-2.5 rounded-lg text-sm focus:border-[#110B45]" required>
                </div>
            </div>
            
            <div class="mb-4 md:mb-5">
                <label class="block text-[10px] md:text-[11px] font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Kecamatan</label>
                <select name="kecamatan" id="kecamatan" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-xs focus:outline-none focus:border-navy cursor-pointer" required>
                    <option value="">-- Pilih Kecamatan --</option>
                </select>
            </div>
            <div class="mb-4 md:mb-5">
                <label class="block text-[10px] md:text-[11px] font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Desa / Kelurahan</label>
                <select name="desa_kelurahan" id="desa_kelurahan" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 rounded-xl text-xs focus:outline-none focus:border-navy cursor-pointer" required>
                    <option value="">-- Pilih Desa/Kelurahan --</option>
                </select>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-700 mb-2">Lemari Rak</label>
                <input type="text" name="lemari_rak" value="<?= $data['lemari_rak']; ?>" class="w-full border p-2.5 rounded-lg text-sm focus:border-[#110B45]" required>
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-700 mb-2">File pdf</label>
                <div class="dash-border rounded-xl p-8 text-center cursor-pointer hover:bg-gray-50 transition relative bg-[#F8FAFC]">
                    <input type="file" name="file_pdf" id="file_pdf" accept="application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="tampilkanNamaFile()">
                    
                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 30 003-3v-1m-4-81-4-4m0 0L8 8m4-4v12"></path></svg>
                    
                    <p id="text-utama" class="text-sm font-bold text-gray-700 mb-1">Klik untuk mengganti file .pdf yang sudah ada</p>
                    <p id="text-sub" class="text-xs text-gray-400">File saat ini: <?= $data['file_pdf']; ?></p>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-xs font-bold text-gray-700 mb-2">Status Keterangan</label>
                <input type="text" name="keterangan" value="<?= $data['keterangan']; ?>" class="w-full border border-gray-300 p-2.5 rounded-lg text-sm focus:border-[#110B45]">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="data_gambar_ukur.php" class="px-6 py-2 border rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" name="simpan" class="bg-navy text-white px-6 py-2 rounded-lg text-sm font-bold">Simpan</button>
            </div>
        </form>
    </div>

<script>
function tampilkanNamaFile() {
    const inputBerkas = document.getElementById('file_pdf');
    const textUtama = document.getElementById('text-utama');
    const textSub = document.getElementById('text-sub');
    
    // Mengunci nama file lama dari database sebagai cadangan visual
    const fileLama = "<?= htmlspecialchars($data['file_pdf']); ?>";

    // Cek apakah ada file baru yang dipilih oleh user
    if (inputBerkas.files.length > 0) {
        const namaFileBaru = inputBerkas.files[0].name; // Mengambil nama file baru
        
        // Mengubah teks UI secara dinamis
        textUtama.innerHTML = "File Baru Terpilih!";
        textSub.innerHTML = `<span class="inline-block bg-red-50 text-red-600 font-bold text-xs px-3 py-1.5 rounded-lg border border-red-100 mt-2 tracking-wide uppercase">${namaFileBaru}</span>`;
    } else {
        // Jika user membatalkan pilihan berkas, kembalikan teks ke file lama
        textUtama.innerText = "Klik untuk mengganti file .pdf yang sudah ada";
        textSub.innerText = "File saat ini: " + fileLama;
    }
}
</script>

<script>
const dataLumajang = {
    "Pronojiwo": ["Supiturang", "Tamanayu", "Sidomulyo", "Sumberurip", "Oro-oro Ombo", "Pronojiwo"],
    "Candipuro": ["Jugosari", "Sumberwuluh", "Sumbermujur", "Penanggal", "Tambahrejo", "Sumberejo", "Candipuro", "Jarit", "Kloposawit", "Tumpeng"],
    "Pasirian": ["Selok Awar-awar", "Bago", "Bades", "Gondoruso", "Kalibendo", "Pasirian", "Condro", "Madurejo", "Sememu", "Nguter", "Selok Anyar"],
    "Tempeh": ["Pandanwangi", "Sumberjati", "Tempeh Kidul", "Lempeni", "Tempeh Tengah", "Kaliwungu", "Tempeh Lor", "Besuk", "Jatisari", "Pulo", "Gesang", "Jokarto", "Pandanarum"],
    "Kunir": ["Jatimulyo", "Jatirejo", "Jatigono", "Kunir Kidul", "Kunir Lor", "Sukosari", "Sukorejo", "Karanglo", "Kedungmoro", "Dorogowok", "Kabuaran"],
    "Tekung": ["Wonogriyo", "Mangunsari", "Wonosari", "Tekung", "Wonokerto", "Karangbendo", "Klampokarum", "Tukum"],
    "Yosowilangun": ["Karangrejo", "Karanganyar", "Krai", "Kraton", "Tunjungrejo", "Yosowilangun Lor", "Munder", "Kebonsari", "Kalipepe", "Yosowilangun Kidul", "Wotgalih", "Darungan"],
    "Jatiroto": ["Banyuputih Kidul", "Rojopolo", "Kaliboto Lor", "Kaliboto Kidul", "Sukosari", "Jatiroto"],
    "Lumajang": ["Banjarwaru", "Labruk Lor", "Citrodiwangsan", "Ditotrunan", "Jogotrunan", "Denok", "Blukon", "Boreng", "Jogoyudan", "Tompokersan", "Rogotrunan", "Kepuharjo"],
    "Senduro": ["Purworejo", "Sarikemuning", "Pandansari", "Senduro", "Burno", "Kandangtepus", "Kandangan", "Bedayu", "Bedayu Talang", "Wonocepoko Ayu", "Argosari", "Ranupane"],
    "Sukodono": ["Uranggantung", "Selokgondang", "Sumberejo", "Bondoyudo", "Selokbesuki", "Kutorenon", "Dawuhan Lor", "Karangsari", "Kebonagung", "Klanting"],
    "Randuagung": ["Kalidilem", "Tunjung", "Gedang Mas", "Randuagung", "Banyuputih Lor", "Pejarakan", "Buwek", "Ledoktempuro", "Ranuwurung", "Ranulogong", "Kalipenggung", "Salak"],
    "Gucialit": ["Wonokerto", "Pakel", "Kenongo", "Gucialit", "Dadapan", "Kertowono", "Tunjung", "Jeruk", "Sombo"],
    "Klakah": ["Kebonan", "Kudus", "Duren", "Sumberweringin", "Papringan", "Ranupakis", "Tegalrandu", "Klakah", "Mlawang", "Sawaran Lor", "Sruni", "Tegalciut"],
    "Ranuyoso": ["Alun Alun", "Ranubedali", "Sumberpetung", "Tegalbangsri", "Ranuyoso", "Meninjo", "Jenggrong", "Penawungan", "Wonoayu", "Wates Wetan", "Wates Kulon"],
    "Tempursari": ["Bulurejo", "Purorejo", "Tempurejo", "Tempursari", "Pudungsari", "Kaliuling", "Tegalrejo"],
    "Kedungjajang": ["Kedungjajang", "Grobogan", "Sawaran Kulon", "Curahpetung", "Pandansari", "Krasak", "Bence", "Jatisari", "Bandaran", "Tempursari", "Umbul", "Wonorejo"],
    "Rowokangkung": ["Dawuhan Wetan", "Sumbersari", "Kedungrejo", "Sidorejo", "Rowokangkung", "Nogosari", "Sumberanyar"],
    "Padang": ["Padang", "Mojo", "Babakan", "Barat", "Bodang", "Kedawung", "Kalisemut", "Merakan", "Tanggung"],
    "Pasrujambe": ["Pasrujambe", "Jambekumbu", "Sukorejo", "Jambearum", "Pagowan", "Kertosari", "Karanganom"],
    "Sumbersuko": ["Labruk Kidul", "Sumbersuko", "Grati", "Mojosari", "Kebonsari", "Petahunan", "Purwosono", "Sentul"]
};

const kecSelect = document.getElementById('kecamatan');
const desaSelect = document.getElementById('desa_kelurahan');

// Perbaikan: Sebelumnya $row, diganti menjadi $data agar sesuai dengan pemanggilan database di PHP atas
const selectedKec = "<?= htmlspecialchars($data['kecamatan'] ?? '') ?>";
const selectedDesa = "<?= htmlspecialchars($data['desa_kelurahan'] ?? '') ?>";

for (let kec in dataLumajang) {
    let option = new Option(kec, kec);
    if (kec === selectedKec) option.selected = true;
    kecSelect.add(option);
}

function updateDesa(kecamatanVal, preselectDesa = '') {
    desaSelect.innerHTML = '<option value="">-- Pilih Desa/Kelurahan --</option>';
    if (kecamatanVal && dataLumajang[kecamatanVal]) {
        desaSelect.disabled = false;
        dataLumajang[kecamatanVal].forEach(function(desa) {
            let option = new Option(desa, desa);
            if (desa === preselectDesa) option.selected = true;
            desaSelect.add(option);
        });
    } else {
        desaSelect.disabled = true;
    }
}

// Jalankan fungsi saat pertama kali halaman di-load agar memunculkan desa yang tersimpan
updateDesa(selectedKec, selectedDesa);

kecSelect.addEventListener('change', function() {
    updateDesa(this.value);
});
</script>
</body>
</html>
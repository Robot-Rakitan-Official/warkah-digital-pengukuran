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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Gambar Ukur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Public+Sans:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        .bg-navy { background-color: #110B45; }
        .dash-border { border: 2px dashed #CBD5E1; background-color: #F8FAFC; }
    </style>
</head>
<body class="flex items-center justify-center p-4 md:p-10 min-h-screen relative">
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border w-full max-w-3xl z-10">
        <h2 class="text-xl md:text-2xl font-bold font-['Public_Sans'] text-[#110B45] mb-2">Tambah Data Gambar Ukur</h2>
        <p class="text-xs md:text-sm text-gray-500 mb-6">Silahkan lengkapi form di bawah ini untuk menambahkan data gambar ukur baru.</p>
        
        <?php if(isset($error)) echo "<p class='text-red-500 text-sm mb-4 bg-red-50 p-2 rounded'>$error</p>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Nomor Gambar Ukur</label>
                    <div class="flex gap-2">
                        <input type="text" name="no_gambar_ukur" id="no_gambar_ukur" placeholder="Ketik manual atau Scan QR" class="w-full border border-gray-300 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45]" required>
                        <button type="button" onclick="bukaScanner('no_gambar_ukur')" class="bg-[#110B45] text-white px-3 md:px-4 rounded-lg hover:bg-opacity-90 transition-colors flex items-center justify-center shrink-0 shadow-sm" title="Scan QR Code">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-2">Tahun Gambar Ukur</label>
                    <input type="number" name="tahun" placeholder="YYYY" class="w-full border border-gray-300 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45]" required>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-5">
                <div>
                    <label class="block text-[10px] md:text-[11px] font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Kecamatan</label>
                    <select name="kecamatan" id="kecamatan" class="w-full bg-[#F8FAFC] border border-gray-200 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45] cursor-pointer" required>
                        <option value="">-- Pilih Kecamatan --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] md:text-[11px] font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Desa / Kelurahan</label>
                    <select name="desa_kelurahan" id="desa_kelurahan" class="w-full bg-[#F8FAFC] border border-gray-200 p-2.5 rounded-lg text-sm focus:outline-none focus:border-[#110B45] cursor-pointer disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                        <option value="">-- Pilih Desa/Kelurahan --</option>
                    </select>
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

            <div class="flex flex-col md:flex-row justify-end gap-3 pt-4 border-t">
                <a href="data_gambar_ukur.php" class="text-center px-6 py-2.5 md:py-2 border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" name="simpan" class="bg-navy text-white px-6 py-2.5 md:py-2 rounded-lg text-sm font-bold hover:bg-opacity-90">Simpan</button>
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
// Fungsi Menampilkan Nama File PDF
function tampilkanNamaFile() {
    const inputBerkas = document.getElementById('file_pdf');
    const textUtama = document.getElementById('text-utama');
    const textSub = document.getElementById('text-sub');

    if (inputBerkas.files.length > 0) {
        const namaFile = inputBerkas.files[0].name; 
        textUtama.innerHTML = "File Berhasil Dipilih!";
        textSub.innerHTML = `<span class="inline-block bg-red-50 text-red-600 font-bold text-xs px-3 py-1.5 rounded-lg border border-red-100 mt-2 tracking-wide uppercase">${namaFile}</span>`;
    } else {
        textUtama.innerText = "Klik untuk upload file .pdf";
        textSub.innerText = "atau seret file ke area ini (Maks. 5MB)";
    }
}

// Data Array Desa dan Kecamatan yang Sudah Diperbaiki
document.addEventListener('DOMContentLoaded', function() {
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

    if (kecSelect && desaSelect) {
        kecSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        for (let kec in dataLumajang) {
            kecSelect.add(new Option(kec, kec));
        }

        kecSelect.addEventListener('change', function() {
            desaSelect.innerHTML = '<option value="">-- Pilih Desa/Kelurahan --</option>';
            if (this.value) {
                desaSelect.disabled = false;
                dataLumajang[this.value].forEach(function(desa) {
                    desaSelect.add(new Option(desa, desa));
                });
            } else {
                desaSelect.disabled = true;
            }
        });
    }
});

function toggleMaster() {
    const submenu = document.getElementById('master-submenu');
    const chevron = document.getElementById('chevron-icon');
    if (submenu && chevron) {
        submenu.classList.toggle('hidden');
        chevron.classList.toggle('rotate-180');
    }
}

// Fungsi Konfigurasi QR Code Scanner
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
    // Diabaikan agar tidak menampilkan error terus menerus saat kamera mencari QR
}
</script>
</body>
</html>
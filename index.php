<?php
require 'config.php';
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
$error = null;
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    $query = "SELECT * FROM users WHERE (username = '$username' OR email = '$username') AND password = '$password'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['nama'] = $row['nama_lengkap'];
        $_SESSION['role'] = $row['role'];

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username/Email atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Warkah Digital Pengukuran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css?family=Inter:wght@400;500;600;700&family=Public+Sans:wght@700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        .bg-navy { background-color: #110B45; }
        .text-navy { color: #110B45; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-[#110B45] relative overflow-hidden p-4 sm:p-6">

    <div class="absolute top-[-5%] left-[-10%] w-64 h-64 md:w-96 md:h-96 bg-indigo-600 rounded-full mix-blend-multiply filter blur-[90px] md:blur-[128px] opacity-50"></div>
    <div class="absolute bottom-[-5%] right-[-10%] w-64 h-64 md:w-96 md:h-96 bg-yellow-500 rounded-full mix-blend-multiply filter blur-[90px] md:blur-[128px] opacity-20"></div>

    <div class="bg-white p-8 md:p-10 rounded-[2rem] shadow-2xl w-full max-w-md relative z-10 border border-white/20">

        <div class="text-center mb-6 md:mb-8">
            <div class="w-16 h-16 md:w-20 md:h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-5 border border-gray-100 shadow-sm">
                <img src="assets/img/logo.png" alt="Logo" class="w-10 h-10 md:w-12 md:h-12" onerror="this.style.display='none'">
            </div>
            <h2 class="text-xl md:text-2xl font-extrabold text-[#110B45] font-['Public_Sans'] leading-tight">Warkah Digital<br>Pengukuran</h2>
            <p class="text-[10px] md:text-xs text-gray-400 font-medium mt-2">Silakan login untuk mengakses sistem</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 border border-red-100 text-red-600 text-[10px] md:text-xs font-bold p-3 rounded-xl mb-5 md:mb-6 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4 md:mb-5">
                <label class="block text-[10px] md:text-[11px] font-bold text-gray-500 mb-1.5 md:mb-2 uppercase tracking-wide">Username / Email</label>
                <input type="text" name="username" placeholder="Masukkan username atau email" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 md:p-3.5 rounded-xl text-xs md:text-sm focus:outline-none focus:border-[#110B45] focus:ring-1 focus:ring-[#110B45] transition-all" required autofocus>
            </div>

            <div class="mb-6 md:mb-8">
                <label class="block text-[10px] md:text-[11px] font-bold text-gray-500 mb-1.5 md:mb-2 uppercase tracking-wide">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" placeholder="Masukkan password" class="w-full bg-[#F8FAFC] border border-gray-200 p-3 md:p-3.5 pr-12 rounded-xl text-xs md:text-sm focus:outline-none focus:border-[#110B45] focus:ring-1 focus:ring-[#110B45] transition-all" required>
                    
                    <button type="button" onclick="togglePassword('password', 'eye-icon')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-[#110B45] transition-colors focus:outline-none">
                        <svg id="eye-icon" class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" name="login" class="w-full bg-[#FACC15] hover:bg-[#EAB308] text-[#110B45] py-3 md:py-3.5 rounded-xl text-xs md:text-sm font-extrabold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2 tracking-wide">
                Masuk ke Sistem
                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>
    </div>

    <script>
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
    </script>
</body>
</html>
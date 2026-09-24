<?php
include 'koneksi.php';
session_start();

// Jika user sudah login, langsung alihkan ke dashboard.php [2]
if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) {
    header("Location: dashboard.php");
    exit;
}

$success_message = "";
$error_message = "";

// Memproses data ketika tombol "Daftar" diklik (Form disubmit) [2]
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['nama_lengkap']) && isset($_POST['email']) && isset($_POST['password'])) {
        $namaInput  = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
        $emailInput = mysqli_real_escape_string($koneksi, trim($_POST['email']));
        $passInput  = trim($_POST['password']);

        // 1. Validasi: Cek apakah email sudah pernah terdaftar di tabel database
        $cekQuery  = "SELECT email FROM users WHERE email = '$emailInput'";
        $cekResult = mysqli_query($koneksi, $cekQuery);

        if (mysqli_num_rows($cekResult) > 0) {
            $error_message = "Email sudah terdaftar! Silakan gunakan email lain.";
        } else {
            // 2. Memasukkan data user baru ke tabel database.
            // Kolom 'username' diisi dengan '$namaInput' sesuai struktur data Anda.
            $insertQuery = "INSERT INTO users (username, email, password) VALUES ('$namaInput', '$emailInput', '$passInput')";
            
            if (mysqli_query($koneksi, $insertQuery)) {
                $success_message = "Akun berhasil dibuat! Silakan <a href='index.php' style='color: #22c55e; font-weight: bold;'>Masuk sekarang</a>.";
            } else {
                $error_message = "Gagal mendaftarkan akun: " . mysqli_error($koneksi);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - IoT Monitoring Tanaman</title>
    <!-- Hubungkan ke file CSS Anda yang sudah ada -->
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <div class="login-wrapper">
        
        <!-- Sisi Kiri: Banner & Ilustrasi (HTML Asli Anda) -->
        <div class="left-banner">
            <div class="brand">
                <div class="brand-logo">
                    <img src="Logo_utama.png" alt="Logo Tanaman">
                </div>
                <div class="brand-text">
                    <h2>IoT Monitoring <span class="green-text">Tanaman</span></h2>
                </div>
            </div>

            <div class="banner-content">
                <h1>Bergabung bersama IoT Monitoring</h1>
                <p>Buat akun baru sekarang untuk mulai memantau perkembangan dan kesehatan tanaman Anda secara akurat.</p>
            </div>

            <div class="illustration-area">
                <img src="Logo_bawah.png" alt="Ilustrasi Monitoring Tanaman" class="banner-img">
            </div>
        </div>

        <!-- Sisi Kanan: Form Register -->
        <div class="right-form">
            <div class="form-container">
                <div class="form-header">
                    <h2>Buat Akun Baru</h2>
                    <p>Silakan isi data diri Anda untuk mendaftar</p>
                </div>

                <!-- NOTIFIKASI ERROR: Muncul jika email sudah terdaftar/terjadi gangguan -->
                <?php if (!empty($error_message)): ?>
                    <div style="color: #ef4444; background: #fee2e2; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; text-align: center; border: 1px solid #fca5a5;">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <!-- NOTIFIKASI SUKSES: Muncul jika akun berhasil tersimpan ke database -->
                <?php if (!empty($success_message)): ?>
                    <div style="color: #15803d; background: #dcfce7; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; text-align: center; border: 1px solid #bbf7d0;">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <!-- PERUBAHAN FORM: Menggunakan POST dan mengarah ke file ini sendiri -->
                <form action="register.php" method="POST" class="login-form">
                    
                    <div class="input-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <div class="input-field">
                            <!-- PERBAIKAN: Ditambahkan name="nama_lengkap" agar terbaca oleh PHP -->
                            <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap Anda" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="email">Email</label>
                        <div class="input-field">
                            <!-- PERBAIKAN: Ditambahkan name="email" agar terbaca oleh PHP -->
                            <input type="email" id="email" name="email" placeholder="Masukkan email Anda" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="input-field">
                            <!-- PERBAIKAN: Ditambahkan name="password" agar terbaca oleh PHP -->
                            <input type="password" id="password" name="password" placeholder="Buat password Anda" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">Daftar</button>
                </form>

                <!-- PERUBAHAN TAUTAN: Diarahkan ke index.php halaman login utama -->
                <p class="register-text" style="margin-top: 20px;">
                    Sudah punya akun? <a href="index.php">Masuk sekarang</a>
                </p>
            </div>
        </div>

    </div>

</body>
</html>

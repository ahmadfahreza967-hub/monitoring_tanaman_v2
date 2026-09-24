<?php
include 'koneksi.php';
session_start();

$error_message = "";
$success_message = "";
$user_found = false;
$email_session = "";

// Tahap 1: Memeriksa apakah Email terdaftar di database
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cek_email'])) {
    $emailInput = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    
    $query = "SELECT * FROM users WHERE email = '$emailInput'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $user_found = true;
        $email_session = $row['email'];
        // Menyimpan email sementara di session agar tidak hilang saat input password baru
        $_SESSION['reset_email'] = $email_session; 
    } else {
        $error_message = "Email tidak terdaftar dalam sistem!";
    }
}

// Tahap 2: Memproses pengubahan Password Baru di database
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    if (isset($_SESSION['reset_email']) && isset($_POST['new_password'])) {
        $emailTarget = $_SESSION['reset_email'];
        $newPass = mysqli_real_escape_string($koneksi, trim($_POST['new_password']));

        // Memperbarui password lama dengan yang baru berdasarkan email akun pengguna
        $updateQuery = "UPDATE users SET password = '$newPass' WHERE email = '$emailTarget'";
        
        if (mysqli_query($koneksi, $updateQuery)) {
            $success_message = "Password berhasil diperbarui! Silakan <a href='index.php' style='color: #22c55e; font-weight: bold;'>Masuk di sini</a>.";
            unset($_SESSION['reset_email']); // Bersihkan session reset
        } else {
            $error_message = "Gagal memperbarui password: " . mysqli_error($koneksi);
        }
    } else {
        $error_message = "Sesi reset kadaluarsa. Silakan masukkan email kembali.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - IoT Monitoring Tanaman</title>
    <!-- Menggunakan CSS style.css yang sama dengan halaman login agar desain seragam -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="login-wrapper">
        
        <!-- Sisi Kiri: Banner & Ilustrasi (Desain Asli Anda) -->
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
                <h1>Pulihkan Akses Akun Anda</h1>
                <p>Kami akan membantu Anda mengatur ulang kata sandi agar dapat kembali mengakses sistem monitoring.</p>
            </div>

            <div class="illustration-area">
                <img src="Logo_bawah.png" alt="Ilustrasi Monitoring Tanaman" class="banner-img">
            </div>
        </div>

        <!-- Sisi Kanan: Form Pemulihan -->
        <div class="right-form">
            <div class="form-container">
                <div class="form-header">
                    <h2>Lupa Password?</h2>
                    <p>Ikuti langkah di bawah untuk memperbarui kata sandi akun lama Anda</p>
                </div>

                <!-- Tampilkan Kotak Notifikasi Merah (Error) -->
                <?php if (!empty($error_message)): ?>
                    <div style="color: #ef4444; background: #fee2e2; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; text-align: center; border: 1px solid #fca5a5;">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <!-- Tampilkan Kotak Notifikasi Hijau (Sukses) -->
                <?php if (!empty($success_message)): ?>
                    <div style="color: #15803d; background: #dcfce7; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; text-align: center; border: 1px solid #bbf7d0;">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <!-- FORM LANGKAH 1: Masukkan Email (Muncul jika user belum diverifikasi) -->
                <?php if (!$user_found && empty($success_message) && !isset($_SESSION['reset_email'])): ?>
                    <form action="forgot_password.php" method="POST" class="login-form">
                        <input type="hidden" name="cek_email" value="1">
                        <div class="input-group">
                            <label for="email">Email Terdaftar</label>
                            <div class="input-field">
                                <input type="email" id="email" name="email" placeholder="Masukkan email Anda" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">Verifikasi Email</button>
                    </form>
                <?php endif; ?>

                <!-- FORM LANGKAH 2: Masukkan Password Baru (Muncul secara otomatis jika email ditemukan di database) -->
                <?php if ($user_found || isset($_SESSION['reset_email'])): ?>
                    <form action="forgot_password.php" method="POST" class="login-form">
                        <input type="hidden" name="reset_password" value="1">
                        <div style="background: #eff6ff; color: #1e40af; padding: 10px; border-radius: 6px; font-size: 13px; margin-bottom: 15px; text-align: center; border: 1px solid #bfdbfe;">
                            Email Terverifikasi: <strong><?php echo isset($_SESSION['reset_email']) ? $_SESSION['reset_email'] : $email_session; ?></strong>
                        </div>
                        <div class="input-group">
                            <label for="new_password">Password Baru</label>
                            <div class="input-field">
                                <input type="password" id="new_password" name="new_password" placeholder="Buat password baru Anda" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">Perbarui Password</button>
                    </form>
                <?php endif; ?>

                <p class="register-text" style="margin-top: 20px;">
                    <a href="index.php">Kembali ke halaman Masuk</a>
                </p>
            </div>
        </div>

    </div>

</body>
</html>

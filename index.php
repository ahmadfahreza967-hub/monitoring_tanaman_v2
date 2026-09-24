<?php
include 'koneksi.php';
session_start();

// Jika user SUDAH login, langsung alihkan ke dashboard.php (Aman)
if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $emailInput = mysqli_real_escape_string($koneksi, trim($_POST['email']));
        $passInput  = trim($_POST['password']);

        $query  = "SELECT * FROM users WHERE email = '$emailInput'";
        $result = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            
            if ($passInput === $row['password']) {
                $_SESSION['isLoggedIn'] = true;
                $_SESSION['userEmail']  = $row['email'];
                $_SESSION['username']   = $row['username'];
                
                header("Location: dashboard.php");
                exit;
            } else {
                $error_message = "Email atau password salah!";
            }
        } else {
            $error_message = "Email atau password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - IoT Monitoring Tanaman</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="left-banner">
            <div class="brand">
                <div class="brand-logo"><img src="Logo_utama.png" alt="Logo Tanaman"></div>
                <div class="brand-text">
                    <h2>IoT Monitoring <span class="green-text">Tanaman</span></h2>
                </div>
            </div>
            <div class="banner-content">
                <h1>Pantau kondisi tanaman <br><span class="green-text">secara real-time</span></h1>
                <p>Sistem monitoring IoT membantu Anda memantau kelembapan tanah, suhu, kadar air.</p>
            </div>
            <div class="illustration-area"><img src="Logo_bawah.png" alt="Ilustrasi" class="banner-img"></div>
        </div>

        <div class="right-form">
            <div class="form-container">
                <div class="form-header">
                    <h2>Selamat Datang!</h2>
                    <p>Masuk untuk melanjutkan ke dashboard monitoring</p>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div style="color: #ef4444; background: #fee2e2; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; text-align: center; border: 1px solid #fca5a5;">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form action="index.php" method="POST" class="login-form">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <div class="input-field">
                            <input type="email" id="email" name="email" placeholder="Masukkan email Anda" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="input-field">
                            <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
                            <span class="eye-icon" style="cursor:pointer;">👁️</span>
                        </div>
                        <a href="forgot_password.php" class="forgot-link">Lupa password?</a>
                    </div>
                    <button type="submit" class="btn-primary">Masuk</button>
                </form>

                <!-- 🛠️ BAGIAN YANG DIKEMBALIKAN (TOMBOL GOOGLE & DAFTAR BARU) -->
                <div class="divider">
                    <span>atau masuk dengan</span>
                </div>

               <!--  GANTI MENJADI KODE INLINE SVG DI BAWAH INI: -->
                <button type="button" class="btn-google">
                    <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" xmlns="http://w3.org">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                    </svg>
                    Masuk dengan Google
                </button>


                <p class="register-text">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
                
            </div>
        </div>
    </div>
   <script>
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.querySelector('.eye-icon');
    if (eyeIcon && passwordInput) {
        eyeIcon.addEventListener('click', () => {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        });
    }
   </script>
</body>
</html>

<?php
include 'koneksi.php';
session_start(); // Wajib ada di baris paling atas!

if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: index.php");
    exit;
}

 //2. Ambil data sensor paling baru dari database
$querySensus = "SELECT * FROM sensor_data ORDER BY waktu DESC LIMIT 1";
$resultSensus = mysqli_query($koneksi, $querySensus);

$dataSensor = [
    'kelembapan' => 0,
    'suhu'       => 0,
    'kadar_air'  => 0,
    'waktu'      => 'Belum ada data'
];

if ($resultSensus && mysqli_num_rows($resultSensus) === 1) {
    $dataSensor = mysqli_fetch_assoc($resultSensus);
}

// AMBIL DATA SENSOR TERBARU BERDASARKAN WAKTU INPUT TERAKHIR
$querySensus = "SELECT * FROM sensor_data ORDER BY waktu DESC LIMIT 1";
$resultSensus = mysqli_query($koneksi,$querySensus);

// Set data bawaan (default) jika tabel database masih kosong
$dataSensor = [
    'kelembapan' => 0,
    'suhu'       => 0,
    'kadar_air'  => 0,
    'waktu'      => 'Belum ada data'
];

if ($resultSensus && mysqli_num_rows($resultSensus) === 1) {
    $dataSensor = mysqli_fetch_assoc($resultSensus);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Monitoring Tanaman</title>
    <!-- Font Google: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

    <div class="dashboard-container">
        
        <!-- Header / Baris Atas -->
        <header class="top-row">
            <!-- Title & Branding -->
            <div class="brand-section">
                <div class="plant-avatar">
                    <img src="Logo_utama.png" alt="Logo Utama Tanaman">
                </div>
                <div class="brand-text">
                    <h1>IoT Monitoring <span class="green-text">Tanaman</span> 
                        <svg class="leaf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.4 19 2c1 2 2 4.1 2 7 0 6-4.5 11-10 11Z"/>
                            <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                        </svg>
                    </h1>
                    <p>Pantau kondisi tanah secara real-time untuk pertumbuhan tanaman yang optimal</p>
                    <p>Selamat Datang, <strong><?php echo isset($_SESSION['username']) ?$_SESSION['username'] : 'User'; ?></strong>!</p>
                </div>
            </div>

            <div class="header-right-group">
                <!-- FITUR NOTIFIKASI -->
                <div class="notification-wrapper">
                    <button class="notif-btn" id="notifBtn" type="button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        <span class="notif-badge">2</span>
                    </button>

                    <!-- Dropdown Daftar Notifikasi -->
                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <h3>Riwayat Notifikasi</h3>
                            <span class="mark-read">Tandai dibaca</span>
                        </div>
                        <div class="notif-list">
                            <div class="notif-item warning">
                                <div class="notif-content">
                                    <strong>Tanah Sangat Kering</strong>
                                    <p>Kelembapan tanah turun ke 20%. Perlu penyiraman segera.</p>
                                    <small>5 menit yang lalu</small>
                                </div>
                            </div>
                            <div class="notif-item success">
                                <div class="notif-content">
                                    <strong>Suhu Normal</strong>
                                    <p>Suhu tanah stabil di angka 28.4°C.</p>
                                    <small>1 jam yang lalu</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Status Alat -->
                <div class="status-card">
                    <div class="status-top">
                        <div class="status-info">
                            <span class="status-title">Status Pompa</span>
                            <h2 class="status-value"></h2>
                            <p class="status-sub"></p>
                        </div>
                        
                        <!-- Mode Selector (OFF / AUTO / ON) -->
                        <div class="mode-selector">
                            <input type="radio" id="modeOff" name="deviceMode" value="off">
                            <label for="modeOff">OFF</label>

                            <input type="radio" id="modeAuto" name="deviceMode" value="auto" checked>
                            <label for="modeAuto">AUTO</label>

                            <input type="radio" id="modeOn" name="deviceMode" value="on">
                            <label for="modeOn">ON</label>
                        </div>
                    </div>
                    <div class="status-bottom-bar">
                        <span class="dot"></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </header>

        <!-- FITUR TEI & RPL: Kalibrasi RTC, Cuaca, & Board Library -->
        <section class="tei-rpl-section">
            
            <!-- Card Kalibrasi RTC & Waktu Server -->
            <div class="feature-card">
                <div class="card-title-group">
                    <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <h3>Sinkronisasi Modul RTC</h3>
                </div>
                <div class="time-display-grid">
                    <div class="time-box">
                        <span>Waktu Server (Web)</span>
                        <strong id="serverTimeDisplay">12:04:00 WIB</strong>
                    </div>
                    <div class="time-box rtc-box">
                        <span>Waktu Modul RTC</span>
                        <strong id="rtcTimeDisplay">11:34:00 WIB</strong>
                    </div>
                </div>
                <button class="btn-calibrate" id="btnSyncRtc" type="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                    </svg>
                    Kalibrasi RTC ke Waktu Server
                </button>
            </div>

            <!-- Card Mode Cuaca Google/Open-Meteo & Execution Void -->
            <div class="feature-card">
                <div class="card-title-group">
                    <svg class="feature-icon orange-text" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/>
                    </svg>
                    <h3>Status Cuaca & Mode Operasi</h3>
                </div>
                <div class="weather-info">
                    <div class="weather-status">
                        <span class="sub-label">Prediksi Cuaca:</span>
                        <strong id="weatherDisplay" class="blue-text">Memuat Cuaca... ⏳</strong>
                    </div>
                    <div class="void-status">
                        <span class="sub-label">Fungsi Program Aktif:</span>
                        <span class="code-badge" id="activeVoidBadge">voidMusimHujan()</span>
                    </div>
                </div>
            </div>

            <!-- Card Info Library Board (Request RPL) -->
            <div class="feature-card">
                <div class="card-title-group">
                    <svg class="feature-icon green-text" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="2" width="20" height="20" rx="4"/>
                        <path d="M6 12h12M12 6v12"/>
                    </svg>
                    <h3>Spesifikasi Board & Library (RPL)</h3>
                </div>
                <ul class="tech-specs-list">
                    <li><strong>Board:</strong> <span>ESP32 Dev Module (WROOM-32)</span></li>
                    <li><strong>RTC Library:</strong> <span>RTClib.h / Wire.h</span></li>
                    <li><strong>Weather API:</strong> <span>Open-Meteo / Google Weather</span></li>
                    <li><strong>Protokol:</strong> <span>HTTP REST API / MQTT</span></li>
                </ul>
            </div>

        </section>

        <!-- Monitoring Metrics Grid -->
        <section class="metrics-grid">
            
            <!-- Kelembapan Tanah -->
            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon">
                        <img src="Kelembapan.png" alt="Ikon Kelembapan Tanah">
                    </div>
                    <span class="metric-title">Kelembapan Tanah</span>
                </div>
                <div class="metric-body">
                    <div class="value-group">
                        <span class="metric-value blue-text" id="valKelembapan"><?php echo $dataSensor['kelembapan']; ?>%</span>
                        <span class="metric-status blue-text" id="statusKelembapan">
                            <?php echo ($dataSensor['kelembapan'] < 30) ? 'Kering' : (($dataSensor['kelembapan'] <= 70) ? 'Normal' : 'Basah'); ?>
                        </span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-fill bg-blue" id="fillKelembapan" style="width: <?php echo $dataSensor['kelembapan']; ?>%;"></div>
                    </div>
                    <div class="scale-labels">
                        <span>0%</span>
                        <span>50%</span>
                        <span>100%</span>
                    </div>
                </div>
            </div>

            <!-- Suhu Tanah -->
            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon">
                        <img src="Suhu.png" alt="Ikon Suhu Tanah">
                    </div>
                    <span class="metric-title">Suhu Tanah</span>
                </div>
                <div class="metric-body">
                    <div class="value-group">
                        <span class="metric-value orange-text" id="valSuhu"><?php echo $dataSensor['suhu']; ?> °C</span>
                       <span class="metric-status orange-text" id="statusSuhu">
                        <?php echo ($dataSensor['suhu'] < 20) ? 'Dingin' : (($dataSensor['suhu'] <= 30) ? 'Normal' : 'Panas'); ?>
                        </span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-fill bg-orange" id="fillSuhu" style="width: <?php echo min(100, ($dataSensor['suhu'] / 50) * 100); ?>%;"></div>
                    </div>
                    <div class="scale-labels">
                        <span>0°C</span>
                        <span>25°C</span>
                        <span>50°C</span>
                    </div>
                </div>
            </div>

            <!-- Kadar Air -->
            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon">
                        <img src="Kadar_air.png" alt="Ikon Kadar Air">
                    </div>
                    <span class="metric-title">Kadar Air</span>
                </div>
                <div class="metric-body">
                    <div class="value-group">
                        <span class="metric-value green-text" id="valKadarAir"><?php echo $dataSensor['kadar_air']; ?>%</span>
                        <span class="metric-status green-text" id="statusKadarAir">
                            <?php echo ($dataSensor['kadar_air'] < 30) ? 'Rendah' : (($dataSensor['kadar_air'] <= 70) ? 'Sedang' : 'Tinggi'); ?>
                        </span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-fill bg-green" id="fillKadarAir" style="width: <?php echo $dataSensor['kadar_air']; ?>%;"></div>
                    </div>
                    <div class="scale-labels">
                        <span>0%</span>
                        <span>50%</span>
                        <span>100%</span>
                    </div>
                </div>
            </div>

        </section>

        <!-- Bottom Row (Banner Ilustrasi + Update & Logout) -->
        <footer class="bottom-section">
            <div class="illustration-banner">
                <img src="Logo_bawah.png" alt="Visualisasi Ilustrasi Tanah dan Sensor" class="banner-img">
            </div>
            
            <div class="right-action-group">
                <div class="last-update-card">
                    <div class="update-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="update-text">
                        <span>Update terakhir:</span>
                        <strong id="valWaktuUpdate">
                            <?php 
                                echo ($dataSensor['waktu'] !== 'Belum ada data') 
                                    ? date('d F Y, H:i:s', strtotime($dataSensor['waktu'])) 
                                    : 'Belum ada data'; 
                            ?>
                        </strong>
                    </div>
                </div>

                <!-- Tombol Logout -->
                <a href="logout.php" class="btn-logout">LOGOUT</a>
            </div>
        </footer>

    </div>

    <!-- Script Interactive Tampilan & Auto-Update Realtime -->
    <script>
        const LATITUDE = -7.2575;  
        const LONGITUDE = 112.7521;

        // 1. Toggle Dropdown Notifikasi
        const notifBtn = document.querySelector('.notif-btn');
        const notifDropdown = document.querySelector('.notif-dropdown');

        if (notifBtn && notifDropdown) {
            notifBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                notifDropdown.classList.toggle('active');
            });

            document.addEventListener('click', (e) => {
                if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                    notifDropdown.classList.remove('active');
                }
            });
        }

        // 2. Live Waktu Server & Kalibrasi RTC
        function updateServerTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID') + ' WIB';
            document.getElementById('serverTimeDisplay').innerText = timeString;
        }
        setInterval(updateServerTime, 1000);

        document.getElementById('btnSyncRtc').addEventListener('click', () => {
            const serverTime = document.getElementById('serverTimeDisplay').innerText;
            document.getElementById('rtcTimeDisplay').innerText = serverTime;
            
            const rtcBox = document.querySelector('.rtc-box');
            rtcBox.style.backgroundColor = '#dcfce7';
            rtcBox.style.borderColor = '#86efac';
            document.querySelector('.rtc-box strong').style.color = '#15803d';
            
            alert('Modul RTC Berhasil Dikalibrasi sesuai Waktu Server (' + serverTime + ')!');
        });

        // 3. Fungsi Ambil Data Cuaca Real-Time
        async function checkWeatherAPI() {
            try {
                const response = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${LATITUDE}&longitude=${LONGITUDE}&current_weather=true`);
                const data = await response.json();
                const weatherCode = data.current_weather.weathercode;
                return weatherCode >= 50; 
            } catch (error) {
                console.error("Gagal mengambil data cuaca dari API:", error);
                return false;
            }
        }

        // 4. Penentuan Execution Void Berdasarkan Mode
        const modeRadios = document.querySelectorAll('input[name="deviceMode"]');
        const voidBadge = document.getElementById('activeVoidBadge');
        const weatherDisplay = document.getElementById('weatherDisplay');

        async function updateProgramVoid() {
            const selectedMode = document.querySelector('input[name="deviceMode"]:checked').value;
            
            if (selectedMode === 'on' || selectedMode === 'off') {
                voidBadge.innerText = 'Mode Manual';
                voidBadge.style.color = '#facc15';
            } else {
                const isRaining = await checkWeatherAPI();
                if (isRaining) {
                    weatherDisplay.innerText = 'Musim Hujan 🌧️';
                    voidBadge.innerText = 'Mode Musim Hujan';
                    voidBadge.style.color = '#38bdf8';
                } else {
                    weatherDisplay.innerText = 'Musim Kemarau ☀️';
                    voidBadge.innerText = 'Mode Musim Kemarau';
                    voidBadge.style.color = '#f97316';
                }
            }
        }

        modeRadios.forEach(radio => radio.addEventListener('change', updateProgramVoid));
        // updateProgramVoid(); // Dimatikan sementara saat muat awal agar loading login terasa instan

        // 5. Fungsi Mengambil Data Sensor Terbaru via AJAX (Auto Update Realtime)
        function loadSensorRealtime() {
            fetch('get_sensor.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Update Kelembapan
                        document.getElementById('valKelembapan').innerText = data.kelembapan + '%';
                        document.getElementById('fillKelembapan').style.width = data.kelembapan + '%';
                        document.getElementById('statusKelembapan').innerText = 
                            (data.kelembapan < 30) ? 'Kering' : ((data.kelembapan <= 70) ? 'Normal' : 'Basah');

                        // Update Suhu
                        document.getElementById('valSuhu').innerText = data.suhu + ' °C';
                        document.getElementById('fillSuhu').style.width = Math.min(100, (data.suhu / 50) * 100) + '%';
                       document.getElementById('statusSuhu').innerText = 
                        (data.suhu < 20) ? 'Dingin' : ((data.suhu <= 30) ? 'Normal' : 'Panas');

                        // Update Kadar Air
                        document.getElementById('valKadarAir').innerText = data.kadar_air + '%';
                        document.getElementById('fillKadarAir').style.width = data.kadar_air + '%';
                        document.getElementById('statusKadarAir').innerText = 
                            (data.kadar_air < 30) ? 'Rendah' : ((data.kadar_air <= 70) ? 'Sedang' : 'Tinggi');

                        // Update Waktu
                        const updateElem = document.getElementById('valWaktuUpdate');
                        if (updateElem) updateElem.innerText = data.waktu;
                    }
                })
                .catch(err => console.error('Gagal mengambil data sensor:', err));
        }

        // Jalankan fungsi update otomatis setiap 3 detik
        setInterval(loadSensorRealtime, 3000);
    </script>

</body>
</html>
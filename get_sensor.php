<?php
include 'koneksi.php';
header('Content-Type: application/json');

// Ambil 1 data sensor paling baru berdasarkan waktu
$query = "SELECT * FROM sensor_data ORDER BY waktu DESC LIMIT 1";
$result = mysqli_query($koneksi, $query);

if ($result && mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    
    // Kirim data dalam format JSON agar bisa dibaca oleh JavaScript di dashboard
    echo json_encode([
        'status'     => 'success',
        'kelembapan' => (int)$row['kelembapan'],
        'suhu'       => (float)$row['suhu'],
        'kadar_air'  => (int)$row['kadar_air'],
        'waktu'      => date('d F Y, H:i:s', strtotime($row['waktu']))
    ]);
} else {
    // Tanggapan jika tabel di database masih kosong
    echo json_encode([
        'status'  => 'empty',
        'message' => 'Belum ada data sensor di database'
    ]);
}
?>
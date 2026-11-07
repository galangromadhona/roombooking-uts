<?php
/**
 * database/setup.php
 * Script setup otomatis: membuat database, tabel, dan data awal.
 * Jalankan SEKALI di browser: http://localhost/roombooking/database/setup.php
 * HAPUS FILE INI SETELAH SETUP SELESAI.
 */

// Konfigurasi
define('DB_HOST',   'localhost');
define('DB_USER',   'root');
define('DB_PASS',   '');
define('DB_NAME',   'roombooking');

$adminPassword = 'admin123'; // Ganti sesuai kebutuhan

// Koneksi tanpa database dulu
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn->connect_error) {
    die('<p style="color:red"><strong>Koneksi MySQL gagal:</strong> ' . $conn->connect_error . '<br>Pastikan XAMPP MySQL sudah berjalan.</p>');
}
$conn->set_charset('utf8mb4');

$log = [];

// Buat database
$conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$log[] = '✅ Database <strong>' . DB_NAME . '</strong> berhasil dibuat/diperiksa.';

$conn->select_db(DB_NAME);

// Buat tabel users
$conn->query("CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)  NOT NULL,
  `username`   VARCHAR(50)   NOT NULL UNIQUE,
  `password`   VARCHAR(255)  NOT NULL,
  `role`       ENUM('admin') NOT NULL DEFAULT 'admin',
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$log[] = '✅ Tabel <strong>users</strong> siap.';

// Buat tabel rooms
$conn->query("CREATE TABLE IF NOT EXISTS `rooms` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)  NOT NULL,
  `capacity`    INT UNSIGNED  NOT NULL,
  `facilities`  TEXT          NULL,
  `description` TEXT          NULL,
  `status`      ENUM('available','unavailable') NOT NULL DEFAULT 'available',
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$log[] = '✅ Tabel <strong>rooms</strong> siap.';

// Buat tabel bookings
$conn->query("CREATE TABLE IF NOT EXISTS `bookings` (
  `id`           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `room_id`      INT UNSIGNED  NOT NULL,
  `booker_name`  VARCHAR(100)  NOT NULL,
  `booking_date` DATE          NOT NULL,
  `start_time`   TIME          NOT NULL,
  `end_time`     TIME          NOT NULL,
  `purpose`      TEXT          NOT NULL,
  `status`       ENUM('active','cancelled') NOT NULL DEFAULT 'active',
  `created_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_bookings_room`
    FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$log[] = '✅ Tabel <strong>bookings</strong> siap.';

// Hapus dan insert ulang user admin
$conn->query("DELETE FROM `users` WHERE username='admin'");
$hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO `users` (name, username, password, role) VALUES ('Administrator', 'admin', ?, 'admin')");
$stmt->bind_param('s', $hashedPassword);
$stmt->execute();
$stmt->close();
$log[] = '✅ Akun admin berhasil dibuat. <strong>Username: admin | Password: ' . htmlspecialchars($adminPassword) . '</strong>';

// Insert data ruangan jika belum ada
$check = $conn->query("SELECT COUNT(*) FROM rooms")->fetch_row()[0];
if ($check == 0) {
    $rooms = [
        ['Ruang Rapat Utama',   20, 'Proyektor, Whiteboard, AC, WiFi, Sound System', 'Ruang rapat utama dengan kapasitas besar, cocok untuk rapat pleno dan presentasi resmi.'],
        ['Ruang Rapat A',       10, 'Proyektor, Whiteboard, AC, WiFi',               'Ruang rapat berukuran sedang, ideal untuk pertemuan divisi atau tim kecil.'],
        ['Ruang Rapat B',       10, 'TV 55 inch, Whiteboard, AC, WiFi',              'Ruang rapat dengan layar TV besar, cocok untuk presentasi dan diskusi tim.'],
        ['Ruang Seminar',       50, 'Proyektor, Sound System, AC, WiFi, Podium',     'Aula seminar berkapasitas besar dengan fasilitas lengkap.'],
        ['Ruang Diskusi Kecil',  6, 'Whiteboard, AC, WiFi',                          'Ruang diskusi kecil dan intim untuk brainstorming atau rapat cepat.'],
        ['Ruang Training',      30, 'Proyektor, Whiteboard, AC, WiFi, Komputer Peserta', 'Ruang pelatihan dengan komputer masing-masing peserta, cocok untuk workshop IT.'],
    ];
    $stmtR = $conn->prepare("INSERT INTO rooms (name, capacity, facilities, description, status) VALUES (?, ?, ?, ?, 'available')");
    foreach ($rooms as $r) {
        $stmtR->bind_param('siss', $r[0], $r[1], $r[2], $r[3]);
        $stmtR->execute();
    }
    $stmtR->close();
    $log[] = '✅ 6 data ruangan contoh berhasil ditambahkan.';

    // Insert contoh bookings
    $today = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $after2 = date('Y-m-d', strtotime('+2 days'));
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    $bookings = [
        [1, 'Budi Santoso',   $today,     '09:00:00', '11:00:00', 'Rapat Koordinasi Bulanan',           'active'],
        [2, 'Siti Rahayu',    $today,     '13:00:00', '15:00:00', 'Diskusi Proyek Digitalisasi',        'active'],
        [3, 'Ahmad Fauzi',    $tomorrow,  '10:00:00', '12:00:00', 'Rapat Tim Pengembangan',             'active'],
        [1, 'Dewi Lestari',   $after2,    '14:00:00', '16:00:00', 'Presentasi Laporan Keuangan',        'active'],
        [5, 'Rudi Hermawan',  $tomorrow,  '08:00:00', '09:30:00', 'Briefing Pagi Tim Sales',            'active'],
        [2, 'Maya Sari',      $yesterday, '09:00:00', '10:00:00', 'Review Desain UI',                   'cancelled'],
    ];
    $stmtB = $conn->prepare("INSERT INTO bookings (room_id, booker_name, booking_date, start_time, end_time, purpose, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($bookings as $b) {
        $stmtB->bind_param('issssss', $b[0], $b[1], $b[2], $b[3], $b[4], $b[5], $b[6]);
        $stmtB->execute();
    }
    $stmtB->close();
    $log[] = '✅ Data pemesanan contoh berhasil ditambahkan.';
} else {
    $log[] = 'ℹ️ Tabel rooms sudah berisi data, skip insert contoh.';
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Setup RoomBooking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">
          <i class="bi bi-gear me-2"></i>Setup RoomBooking – Selesai!
        </div>
        <div class="card-body">
          <div class="alert alert-success fw-semibold mb-3">
            🎉 Setup database berhasil! Aplikasi siap digunakan.
          </div>
          <ul class="list-group list-group-flush mb-4">
            <?php foreach ($log as $l): ?>
            <li class="list-group-item" style="font-size:.92rem;"><?= $l ?></li>
            <?php endforeach; ?>
          </ul>
          <div class="alert alert-warning small">
            <strong>⚠️ Penting:</strong> Hapus file <code>database/setup.php</code> setelah setup selesai untuk keamanan.
          </div>
          <div class="d-flex gap-3 mt-3">
            <a href="/roombooking/" class="btn btn-primary">Buka Situs</a>
            <a href="/roombooking/admin/" class="btn btn-outline-primary">Buka Admin</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>


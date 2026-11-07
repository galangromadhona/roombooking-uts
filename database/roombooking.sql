-- ============================================================
-- DATABASE: roombooking
-- Deskripsi: Database untuk aplikasi Sistem Booking Ruang Rapat
-- ============================================================

CREATE DATABASE IF NOT EXISTS `roombooking`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `roombooking`;

-- ============================================================
-- TABEL: users
-- Menyimpan data akun admin
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)     NOT NULL,
  `username`   VARCHAR(50)      NOT NULL UNIQUE,
  `password`   VARCHAR(255)     NOT NULL,
  `role`       ENUM('admin')    NOT NULL DEFAULT 'admin',
  `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL: rooms
-- Menyimpan data ruangan
-- ============================================================
CREATE TABLE IF NOT EXISTS `rooms` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)     NOT NULL,
  `capacity`    INT UNSIGNED     NOT NULL,
  `facilities`  TEXT             NULL,
  `description` TEXT             NULL,
  `status`      ENUM('available','unavailable') NOT NULL DEFAULT 'available',
  `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL: bookings
-- Menyimpan data pemesanan ruangan
-- ============================================================
CREATE TABLE IF NOT EXISTS `bookings` (
  `id`           INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `room_id`      INT UNSIGNED     NOT NULL,
  `booker_name`  VARCHAR(100)     NOT NULL,
  `booking_date` DATE             NOT NULL,
  `start_time`   TIME             NOT NULL,
  `end_time`     TIME             NOT NULL,
  `purpose`      TEXT             NOT NULL,
  `status`       ENUM('active','cancelled') NOT NULL DEFAULT 'active',
  `created_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_bookings_room`
    FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATA AWAL: users
-- Password: admin123  (di-hash menggunakan PASSWORD_DEFAULT PHP)
-- ============================================================
INSERT INTO `users` (`name`, `username`, `password`, `role`) VALUES
('Administrator', 'admin', '$2y$10$/skSt.bl5Y0z1qM4o9ywiuV/U/qbFw1wu6J9mrBzGo3DpCybIE896', 'admin');
-- ============================================================
-- Password di atas adalah: admin123
-- Hash ini di-generate dengan PHP: password_hash('admin123', PASSWORD_DEFAULT)
-- CARA TERMUDAH: Gunakan http://localhost/roombooking/database/setup.php
-- ============================================================

-- ============================================================
-- DATA AWAL: rooms
-- ============================================================
INSERT INTO `rooms` (`name`, `capacity`, `facilities`, `description`, `status`) VALUES
('Ruang Rapat Utama',       20, 'Proyektor, Whiteboard, AC, WiFi, Sound System',    'Ruang rapat utama dengan kapasitas besar, cocok untuk rapat pleno dan presentasi resmi.', 'available'),
('Ruang Rapat A',           10, 'Proyektor, Whiteboard, AC, WiFi',                  'Ruang rapat berukuran sedang, ideal untuk pertemuan divisi atau tim kecil.',              'available'),
('Ruang Rapat B',           10, 'TV 55 inch, Whiteboard, AC, WiFi',                 'Ruang rapat dengan layar TV besar, cocok untuk presentasi dan diskusi tim.',              'available'),
('Ruang Seminar',           50, 'Proyektor, Sound System, AC, WiFi, Podium',        'Aula seminar berkapasitas besar dengan fasilitas lengkap.',                               'available'),
('Ruang Diskusi Kecil',      6, 'Whiteboard, AC, WiFi',                             'Ruang diskusi kecil dan intim untuk brainstorming atau rapat cepat.',                    'available'),
('Ruang Training',          30, 'Proyektor, Whiteboard, AC, WiFi, Komputer Peserta','Ruang pelatihan dengan komputer masing-masing peserta, cocok untuk workshop IT.',        'available');

-- ============================================================
-- DATA AWAL: bookings
-- ============================================================
INSERT INTO `bookings` (`room_id`, `booker_name`, `booking_date`, `start_time`, `end_time`, `purpose`, `status`) VALUES
(1, 'Budi Santoso',   CURDATE(),              '09:00:00', '11:00:00', 'Rapat Koordinasi Bulanan',           'active'),
(2, 'Siti Rahayu',    CURDATE(),              '13:00:00', '15:00:00', 'Diskusi Proyek Digitalisasi',        'active'),
(3, 'Ahmad Fauzi',    DATE_ADD(CURDATE(),INTERVAL 1 DAY), '10:00:00', '12:00:00', 'Rapat Tim Pengembangan', 'active'),
(1, 'Dewi Lestari',   DATE_ADD(CURDATE(),INTERVAL 2 DAY), '14:00:00', '16:00:00', 'Presentasi Laporan Keuangan', 'active'),
(5, 'Rudi Hermawan',  DATE_ADD(CURDATE(),INTERVAL 1 DAY), '08:00:00', '09:30:00', 'Briefing Pagi Tim Sales', 'active'),
(2, 'Maya Sari',      DATE_SUB(CURDATE(),INTERVAL 1 DAY), '09:00:00', '10:00:00', 'Review Desain UI',           'cancelled');

<?php
/**
 * process/save-booking.php
 * Proses penyimpanan pemesanan baru dari form publik.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/roombooking/pages/booking.php');
}

// Ambil dan sanitasi input
$bookerName  = trim($_POST['booker_name']  ?? '');
$roomId      = (int)($_POST['room_id']     ?? 0);
$bookingDate = trim($_POST['booking_date'] ?? '');
$startTime   = trim($_POST['start_time']   ?? '');
$endTime     = trim($_POST['end_time']     ?? '');
$purpose     = trim($_POST['purpose']      ?? '');

// Simpan input lama untuk repopulate form
$_SESSION['old_input'] = compact('bookerName', 'roomId', 'bookingDate', 'startTime', 'endTime', 'purpose');
$_SESSION['old_input']['booker_name']  = $bookerName;
$_SESSION['old_input']['room_id']      = $roomId;
$_SESSION['old_input']['booking_date'] = $bookingDate;
$_SESSION['old_input']['start_time']   = $startTime;
$_SESSION['old_input']['end_time']     = $endTime;
$_SESSION['old_input']['purpose']      = $purpose;

// ---- Validasi Server-Side ----
$errors = [];

if ($bookerName === '') {
    $errors[] = 'Nama pemesan wajib diisi.';
}
if ($roomId <= 0) {
    $errors[] = 'Ruangan wajib dipilih.';
}
if ($bookingDate === '') {
    $errors[] = 'Tanggal pemesanan wajib diisi.';
} elseif ($bookingDate < date('Y-m-d')) {
    $errors[] = 'Tanggal pemesanan tidak boleh lebih awal dari hari ini.';
}
if ($startTime === '') {
    $errors[] = 'Jam mulai wajib diisi.';
}
if ($endTime === '') {
    $errors[] = 'Jam selesai wajib diisi.';
} elseif ($endTime <= $startTime) {
    $errors[] = 'Jam selesai harus lebih besar daripada jam mulai.';
}
if ($purpose === '') {
    $errors[] = 'Keperluan rapat wajib diisi.';
}

// Validasi ruangan ada dan tersedia
if ($roomId > 0) {
    $stmtRoom = $conn->prepare("SELECT id FROM rooms WHERE id=? AND status='available'");
    $stmtRoom->bind_param('i', $roomId);
    $stmtRoom->execute();
    $stmtRoom->store_result();
    if ($stmtRoom->num_rows === 0) {
        $errors[] = 'Ruangan yang dipilih tidak ditemukan atau tidak tersedia.';
    }
    $stmtRoom->close();
}

// Jika ada error validasi dasar, kembalikan ke form
if (!empty($errors)) {
    setFlash('danger', implode('<br>', $errors));
    redirect('/roombooking/pages/booking.php');
}

// ---- Cek Jadwal Bentrok ----
if (hasBookingConflict($conn, $roomId, $bookingDate, $startTime, $endTime)) {
    setFlash('danger', '⚠️ Jadwal Bentrok! Ruangan sudah dipesan pada waktu yang sama. Silakan pilih waktu atau ruangan lain.');
    redirect('/roombooking/pages/booking.php');
}

// ---- Simpan ke Database ----
$stmt = $conn->prepare(
    "INSERT INTO bookings (room_id, booker_name, booking_date, start_time, end_time, purpose, status)
     VALUES (?, ?, ?, ?, ?, ?, 'active')"
);
$stmt->bind_param('isssss', $roomId, $bookerName, $bookingDate, $startTime, $endTime, $purpose);

if ($stmt->execute()) {
    unset($_SESSION['old_input']);
    setFlash('success', '✅ Pemesanan berhasil disimpan! Ruangan telah dipesan untuk Anda.');
    redirect('/roombooking/pages/my-bookings.php');
} else {
    setFlash('danger', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
    redirect('/roombooking/pages/booking.php');
}
$stmt->close();


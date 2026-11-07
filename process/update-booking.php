<?php
/**
 * process/update-booking.php
 * Proses update/edit pemesanan dari form publik.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/roombooking/pages/my-bookings.php');
}

$id          = (int)($_POST['id']           ?? 0);
$bookerName  = trim($_POST['booker_name']   ?? '');
$roomId      = (int)($_POST['room_id']      ?? 0);
$bookingDate = trim($_POST['booking_date']  ?? '');
$startTime   = trim($_POST['start_time']    ?? '');
$endTime     = trim($_POST['end_time']      ?? '');
$purpose     = trim($_POST['purpose']       ?? '');

// Simpan input lama
$_SESSION['old_input'] = [
    'booker_name'  => $bookerName,
    'room_id'      => $roomId,
    'booking_date' => $bookingDate,
    'start_time'   => $startTime,
    'end_time'     => $endTime,
    'purpose'      => $purpose,
];

$backUrl = "/roombooking/pages/edit-booking.php?id={$id}";

if ($id <= 0) {
    setFlash('danger', 'ID pemesanan tidak valid.');
    redirect('/roombooking/pages/my-bookings.php');
}

// Pastikan booking ada dan aktif
$stmtCheck = $conn->prepare("SELECT id FROM bookings WHERE id=? AND status='active'");
$stmtCheck->bind_param('i', $id);
$stmtCheck->execute();
$stmtCheck->store_result();
if ($stmtCheck->num_rows === 0) {
    $stmtCheck->close();
    setFlash('danger', 'Pemesanan tidak ditemukan atau sudah dibatalkan.');
    redirect('/roombooking/pages/my-bookings.php');
}
$stmtCheck->close();

// Validasi
$errors = [];
if ($bookerName === '')  $errors[] = 'Nama pemesan wajib diisi.';
if ($roomId <= 0)        $errors[] = 'Ruangan wajib dipilih.';
if ($bookingDate === '') $errors[] = 'Tanggal pemesanan wajib diisi.';
elseif ($bookingDate < date('Y-m-d')) $errors[] = 'Tanggal pemesanan tidak boleh lebih awal dari hari ini.';
if ($startTime === '')   $errors[] = 'Jam mulai wajib diisi.';
if ($endTime === '')     $errors[] = 'Jam selesai wajib diisi.';
elseif ($endTime <= $startTime) $errors[] = 'Jam selesai harus lebih besar daripada jam mulai.';
if ($purpose === '')     $errors[] = 'Keperluan rapat wajib diisi.';

// Validasi ruangan
if ($roomId > 0) {
    $stmtRoom = $conn->prepare("SELECT id FROM rooms WHERE id=? AND status='available'");
    $stmtRoom->bind_param('i', $roomId);
    $stmtRoom->execute();
    $stmtRoom->store_result();
    if ($stmtRoom->num_rows === 0) $errors[] = 'Ruangan tidak ditemukan atau tidak tersedia.';
    $stmtRoom->close();
}

if (!empty($errors)) {
    setFlash('danger', implode('<br>', $errors));
    redirect($backUrl);
}

// Cek bentrok (kecualikan booking ini sendiri)
if (hasBookingConflict($conn, $roomId, $bookingDate, $startTime, $endTime, $id)) {
    setFlash('danger', '⚠️ Jadwal Bentrok! Ruangan sudah dipesan pada waktu tersebut. Silakan pilih waktu lain.');
    redirect($backUrl);
}

// Update
$stmt = $conn->prepare(
    "UPDATE bookings SET room_id=?, booker_name=?, booking_date=?, start_time=?, end_time=?, purpose=?
     WHERE id=? AND status='active'"
);
$stmt->bind_param('isssssi', $roomId, $bookerName, $bookingDate, $startTime, $endTime, $purpose, $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    unset($_SESSION['old_input']);
    setFlash('success', '✅ Pemesanan berhasil diperbarui.');
    redirect('/roombooking/pages/my-bookings.php');
} else {
    setFlash('danger', 'Gagal memperbarui pemesanan. Silakan coba lagi.');
    redirect($backUrl);
}
$stmt->close();


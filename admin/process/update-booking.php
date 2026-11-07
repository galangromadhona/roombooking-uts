<?php
/**
 * admin/process/update-booking.php
 * Proses update/edit pemesanan oleh admin.
 */
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/roombooking/admin/bookings.php');
}

$id          = (int)($_POST['id']           ?? 0);
$bookerName  = trim($_POST['booker_name']   ?? '');
$roomId      = (int)($_POST['room_id']      ?? 0);
$bookingDate = trim($_POST['booking_date']  ?? '');
$startTime   = trim($_POST['start_time']    ?? '');
$endTime     = trim($_POST['end_time']      ?? '');
$purpose     = trim($_POST['purpose']       ?? '');
$status      = $_POST['status'] ?? 'active';

$backUrl = "/roombooking/admin/edit-booking.php?id={$id}";
$_SESSION['old_input'] = compact('bookerName', 'roomId', 'bookingDate', 'startTime', 'endTime', 'purpose', 'status');
$_SESSION['old_input']['booker_name']  = $bookerName;
$_SESSION['old_input']['room_id']      = $roomId;
$_SESSION['old_input']['booking_date'] = $bookingDate;
$_SESSION['old_input']['start_time']   = $startTime;
$_SESSION['old_input']['end_time']     = $endTime;

if ($id <= 0) {
    setFlash('danger', 'ID tidak valid.');
    redirect('/roombooking/admin/bookings.php');
}

$errors = [];
if ($bookerName === '')  $errors[] = 'Nama pemesan wajib diisi.';
if ($roomId <= 0)        $errors[] = 'Ruangan wajib dipilih.';
if ($bookingDate === '') $errors[] = 'Tanggal wajib diisi.';
if ($startTime === '')   $errors[] = 'Jam mulai wajib diisi.';
if ($endTime === '')     $errors[] = 'Jam selesai wajib diisi.';
elseif ($endTime <= $startTime) $errors[] = 'Jam selesai harus lebih besar dari jam mulai.';
if ($purpose === '')     $errors[] = 'Keperluan wajib diisi.';
if (!in_array($status, ['active','cancelled'])) $errors[] = 'Status tidak valid.';

if (!empty($errors)) {
    setFlash('danger', implode('<br>', $errors));
    redirect($backUrl);
}

// Cek bentrok hanya jika status aktif
if ($status === 'active' && hasBookingConflict($conn, $roomId, $bookingDate, $startTime, $endTime, $id)) {
    setFlash('danger', '⚠️ Jadwal Bentrok! Ruangan sudah dipesan pada waktu tersebut.');
    redirect($backUrl);
}

$stmt = $conn->prepare(
    "UPDATE bookings SET room_id=?, booker_name=?, booking_date=?, start_time=?, end_time=?, purpose=?, status=? WHERE id=?"
);
$stmt->bind_param('issssssi', $roomId, $bookerName, $bookingDate, $startTime, $endTime, $purpose, $status, $id);

if ($stmt->execute()) {
    unset($_SESSION['old_input']);
    setFlash('success', '✅ Pemesanan berhasil diperbarui.');
    redirect('/roombooking/admin/bookings.php');
} else {
    setFlash('danger', 'Gagal memperbarui pemesanan.');
    redirect($backUrl);
}
$stmt->close();


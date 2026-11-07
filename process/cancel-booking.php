<?php
/**
 * process/cancel-booking.php
 * Proses pembatalan pemesanan oleh pengguna publik.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    setFlash('danger', 'ID pemesanan tidak valid.');
    redirect('/roombooking/pages/my-bookings.php');
}

// Cek dan update status
$stmt = $conn->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND status='active'");
$stmt->bind_param('i', $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    setFlash('success', '✅ Pemesanan berhasil dibatalkan.');
} else {
    setFlash('warning', 'Pemesanan tidak ditemukan atau sudah dibatalkan sebelumnya.');
}
$stmt->close();

redirect('/roombooking/pages/my-bookings.php');


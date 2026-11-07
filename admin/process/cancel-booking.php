<?php
/**
 * admin/process/cancel-booking.php
 * Proses pembatalan pemesanan oleh admin.
 */
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdminLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    setFlash('danger', 'ID tidak valid.');
    redirect('/roombooking/admin/bookings.php');
}

$stmt = $conn->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND status='active'");
$stmt->bind_param('i', $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    setFlash('success', '✅ Pemesanan berhasil dibatalkan.');
} else {
    setFlash('warning', 'Pemesanan sudah dibatalkan sebelumnya atau tidak ditemukan.');
}
$stmt->close();
redirect('/roombooking/admin/bookings.php');


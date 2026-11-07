<?php
/**
 * admin/process/delete-booking.php
 * Proses hapus permanen pemesanan oleh admin.
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

$stmt = $conn->prepare("DELETE FROM bookings WHERE id=?");
$stmt->bind_param('i', $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    setFlash('success', '✅ Data pemesanan berhasil dihapus.');
} else {
    setFlash('danger', 'Gagal menghapus data atau data tidak ditemukan.');
}
$stmt->close();
redirect('/roombooking/admin/bookings.php');


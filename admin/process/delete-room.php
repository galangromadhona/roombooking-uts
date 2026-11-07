<?php
/**
 * admin/process/delete-room.php
 * Proses hapus ruangan (dan cascade bookings).
 */
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdminLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    setFlash('danger', 'ID ruangan tidak valid.');
    redirect('/roombooking/admin/rooms.php');
}

// Ambil nama untuk pesan konfirmasi
$stmtName = $conn->prepare("SELECT name FROM rooms WHERE id=?");
$stmtName->bind_param('i', $id);
$stmtName->execute();
$row = $stmtName->get_result()->fetch_assoc();
$stmtName->close();

if (!$row) {
    setFlash('danger', 'Ruangan tidak ditemukan.');
    redirect('/roombooking/admin/rooms.php');
}

// Hapus (cascade akan menghapus bookings terkait via FK)
$stmt = $conn->prepare("DELETE FROM rooms WHERE id=?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    setFlash('success', '✅ Ruangan "' . $row['name'] . '" berhasil dihapus.');
} else {
    setFlash('danger', 'Gagal menghapus ruangan.');
}
$stmt->close();

redirect('/roombooking/admin/rooms.php');


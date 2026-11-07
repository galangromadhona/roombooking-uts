<?php
/**
 * admin/process/save-room.php
 * Proses penyimpanan ruangan baru.
 */
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/roombooking/admin/rooms.php');
}

$name        = trim($_POST['name']        ?? '');
$capacity    = (int)($_POST['capacity']   ?? 0);
$facilities  = trim($_POST['facilities']  ?? '');
$description = trim($_POST['description'] ?? '');
$status      = $_POST['status'] ?? 'available';

// Simpan input lama
$_SESSION['old_input'] = compact('name', 'capacity', 'facilities', 'description', 'status');

// Validasi
$errors = [];
if ($name === '')    $errors[] = 'Nama ruangan wajib diisi.';
if ($capacity <= 0) $errors[] = 'Kapasitas harus berupa angka lebih dari 0.';
if (!in_array($status, ['available', 'unavailable'])) $errors[] = 'Status tidak valid.';

if (!empty($errors)) {
    setFlash('danger', implode('<br>', $errors));
    redirect('/roombooking/admin/add-room.php');
}

$stmt = $conn->prepare(
    "INSERT INTO rooms (name, capacity, facilities, description, status) VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param('sisss', $name, $capacity, $facilities, $description, $status);

if ($stmt->execute()) {
    unset($_SESSION['old_input']);
    setFlash('success', '✅ Ruangan "' . $name . '" berhasil ditambahkan.');
    redirect('/roombooking/admin/rooms.php');
} else {
    setFlash('danger', 'Gagal menyimpan ruangan. Silakan coba lagi.');
    redirect('/roombooking/admin/add-room.php');
}
$stmt->close();


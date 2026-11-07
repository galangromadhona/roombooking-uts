<?php
/**
 * admin/process/update-room.php
 * Proses update/edit data ruangan.
 */
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/roombooking/admin/rooms.php');
}

$id          = (int)($_POST['id']           ?? 0);
$name        = trim($_POST['name']          ?? '');
$capacity    = (int)($_POST['capacity']     ?? 0);
$facilities  = trim($_POST['facilities']    ?? '');
$description = trim($_POST['description']   ?? '');
$status      = $_POST['status'] ?? 'available';

$backUrl = "/roombooking/admin/edit-room.php?id={$id}";
$_SESSION['old_input'] = compact('name', 'capacity', 'facilities', 'description', 'status');

if ($id <= 0) {
    setFlash('danger', 'ID tidak valid.');
    redirect('/roombooking/admin/rooms.php');
}

$errors = [];
if ($name === '')    $errors[] = 'Nama ruangan wajib diisi.';
if ($capacity <= 0) $errors[] = 'Kapasitas harus berupa angka lebih dari 0.';
if (!in_array($status, ['available', 'unavailable'])) $errors[] = 'Status tidak valid.';

if (!empty($errors)) {
    setFlash('danger', implode('<br>', $errors));
    redirect($backUrl);
}

$stmt = $conn->prepare(
    "UPDATE rooms SET name=?, capacity=?, facilities=?, description=?, status=? WHERE id=?"
);
$stmt->bind_param('sisssi', $name, $capacity, $facilities, $description, $status, $id);

if ($stmt->execute()) {
    unset($_SESSION['old_input']);
    setFlash('success', '✅ Data ruangan berhasil diperbarui.');
    redirect('/roombooking/admin/rooms.php');
} else {
    setFlash('danger', 'Gagal memperbarui data. Silakan coba lagi.');
    redirect($backUrl);
}
$stmt->close();


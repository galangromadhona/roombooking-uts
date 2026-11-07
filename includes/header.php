<?php
/**
 * includes/header.php
 * Header HTML untuk halaman publik.
 * Gunakan: require_once __DIR__ . '/../includes/header.php';
 * Variabel opsional: $pageTitle
 */
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = $pageTitle ?? 'RoomBooking';
// Deteksi halaman aktif
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> – RoomBooking</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link href="/roombooking/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-roombooking sticky-top">
  <div class="container">
    <a class="navbar-brand" href="/roombooking/">
      <i class="bi bi-building me-2"></i>RoomBooking
    </a>
    <button class="navbar-toggler border-0 text-white" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarMain">
      <i class="bi bi-list fs-4"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav ms-auto gap-1">
        <li class="nav-item">
          <a class="nav-link <?= $currentPage==='index.php' ? 'active' : '' ?>"
             href="/roombooking/"><i class="bi bi-house me-1"></i>Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage==='rooms.php' ? 'active' : '' ?>"
             href="/roombooking/pages/rooms.php"><i class="bi bi-grid me-1"></i>Ruangan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage==='booking.php' ? 'active' : '' ?>"
             href="/roombooking/pages/booking.php"><i class="bi bi-calendar-plus me-1"></i>Pesan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage==='my-bookings.php' ? 'active' : '' ?>"
             href="/roombooking/pages/my-bookings.php"><i class="bi bi-list-check me-1"></i>Daftar Pemesanan</a>
        </li>
        <li class="nav-item ms-lg-2">
          <a class="nav-link btn btn-sm btn-outline-light px-3"
             href="/roombooking/admin/"><i class="bi bi-shield-lock me-1"></i>Admin</a>
        </li>
      </ul>
    </div>
  </div>
</nav>


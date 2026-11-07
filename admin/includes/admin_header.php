<?php
/**
 * admin/includes/admin_header.php
 * Layout header + sidebar untuk halaman admin.
 * Variabel yang dibutuhkan: $pageTitle, $activePage
 */
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$adminInitial = strtoupper(substr($adminName, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Admin') ?> – RoomBooking Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/roombooking/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="admin-wrapper">

  <!-- Sidebar Overlay (mobile) -->
  <div class="sidebar-overlay" id="sidebar-overlay"></div>

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <a class="sidebar-brand" href="/roombooking/admin/">
      <span class="brand-icon"><i class="bi bi-building"></i></span>
      RoomBooking
    </a>

    <nav class="sidebar-nav">
      <div class="nav-label">Menu Utama</div>
      <a href="/roombooking/admin/"
         class="<?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
      <a href="/roombooking/admin/rooms.php"
         class="<?= ($activePage ?? '') === 'rooms' ? 'active' : '' ?>">
        <i class="bi bi-grid-3x3-gap"></i> Data Ruangan
      </a>
      <a href="/roombooking/admin/bookings.php"
         class="<?= ($activePage ?? '') === 'bookings' ? 'active' : '' ?>">
        <i class="bi bi-calendar-check"></i> Data Pemesanan
      </a>

      <div class="nav-label mt-2">Lainnya</div>
      <a href="/roombooking/" target="_blank">
        <i class="bi bi-box-arrow-up-right"></i> Lihat Situs
      </a>
      <a href="/roombooking/admin/logout.php"
         data-confirm="Yakin ingin logout?"
         style="color:#fca5a5;">
        <i class="bi bi-box-arrow-left"></i> Logout
      </a>
    </nav>

    <div class="sidebar-footer">
      Masuk sebagai <strong><?= e($adminName) ?></strong>
    </div>
  </aside>

  <!-- Topbar -->
  <header class="admin-topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-sm btn-outline-secondary border-0" id="sidebar-toggle">
        <i class="bi bi-list fs-5"></i>
      </button>
      <span class="topbar-title"><?= e($pageTitle ?? 'Dashboard') ?></span>
    </div>
    <div class="topbar-user">
      <div class="topbar-avatar"><?= $adminInitial ?></div>
      <span class="d-none d-sm-inline"><?= e($adminName) ?></span>
    </div>
  </header>

  <!-- Konten Utama -->
  <main class="admin-content">


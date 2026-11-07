<?php
/**
 * admin/add-room.php
 * Halaman admin: form tambah ruangan baru.
 */
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$pageTitle  = 'Tambah Ruangan';
$activePage = 'rooms';
require_once __DIR__ . '/includes/admin_header.php';

$flash = getFlash();
$old   = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
?>

<?= $flash ?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-plus-square me-2"></i>Tambah Ruangan</h1>
    <p class="page-subtitle">Tambahkan ruangan baru ke dalam sistem</p>
  </div>
  <a href="/roombooking/admin/rooms.php" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Kembali
  </a>
</div>

<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header">
        <i class="bi bi-pencil-square me-2"></i>Formulir Ruangan
      </div>
      <div class="card-body p-4">
        <form method="POST" action="/roombooking/admin/process/save-room.php">

          <div class="mb-3">
            <label class="form-label" for="name">
              Nama Ruangan <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="name" name="name"
                   value="<?= e($old['name'] ?? '') ?>"
                   placeholder="Contoh: Ruang Rapat A" required>
          </div>

          <div class="mb-3">
            <label class="form-label" for="capacity">
              Kapasitas (orang) <span class="text-danger">*</span>
            </label>
            <input type="number" class="form-control" id="capacity" name="capacity"
                   value="<?= e($old['capacity'] ?? '') ?>"
                   min="1" max="500" placeholder="Contoh: 20" required>
          </div>

          <div class="mb-3">
            <label class="form-label" for="facilities">Fasilitas</label>
            <input type="text" class="form-control" id="facilities" name="facilities"
                   value="<?= e($old['facilities'] ?? '') ?>"
                   placeholder="Proyektor, Whiteboard, AC, WiFi (pisahkan dengan koma)">
            <div class="form-text">Pisahkan setiap fasilitas dengan tanda koma.</div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="description">Deskripsi</label>
            <textarea class="form-control" id="description" name="description" rows="3"
                      placeholder="Deskripsi singkat tentang ruangan ini..."><?= e($old['description'] ?? '') ?></textarea>
          </div>

          <div class="mb-4">
            <label class="form-label" for="status">Status</label>
            <select class="form-select" id="status" name="status">
              <option value="available"   <?= ($old['status'] ?? 'available') === 'available'   ? 'selected' : '' ?>>Tersedia</option>
              <option value="unavailable" <?= ($old['status'] ?? '') === 'unavailable' ? 'selected' : '' ?>>Tidak Tersedia</option>
            </select>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
              <i class="bi bi-check-circle me-2"></i>Simpan Ruangan
            </button>
            <a href="/roombooking/admin/rooms.php" class="btn btn-outline-secondary px-4">
              Batal
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>


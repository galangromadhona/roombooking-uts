<?php
/**
 * admin/edit-room.php
 * Halaman admin: form edit data ruangan.
 */
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    setFlash('danger', 'ID ruangan tidak valid.');
    redirect('/roombooking/admin/rooms.php');
}

// Ambil data ruangan
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$room) {
    setFlash('danger', 'Ruangan tidak ditemukan.');
    redirect('/roombooking/admin/rooms.php');
}

$pageTitle  = 'Edit Ruangan';
$activePage = 'rooms';
require_once __DIR__ . '/includes/admin_header.php';

$flash = getFlash();
$old   = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
?>

<?= $flash ?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-pencil-square me-2"></i>Edit Ruangan</h1>
    <p class="page-subtitle">Ubah data ruangan: <?= e($room['name']) ?></p>
  </div>
  <a href="/roombooking/admin/rooms.php" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Kembali
  </a>
</div>

<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header">
        <i class="bi bi-pencil me-2"></i>Edit Ruangan #<?= $id ?>
      </div>
      <div class="card-body p-4">
        <form method="POST" action="/roombooking/admin/process/update-room.php">
          <input type="hidden" name="id" value="<?= $id ?>">

          <div class="mb-3">
            <label class="form-label" for="name">
              Nama Ruangan <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="name" name="name"
                   value="<?= e($old['name'] ?? $room['name']) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label" for="capacity">
              Kapasitas (orang) <span class="text-danger">*</span>
            </label>
            <input type="number" class="form-control" id="capacity" name="capacity"
                   value="<?= e($old['capacity'] ?? $room['capacity']) ?>"
                   min="1" max="500" required>
          </div>

          <div class="mb-3">
            <label class="form-label" for="facilities">Fasilitas</label>
            <input type="text" class="form-control" id="facilities" name="facilities"
                   value="<?= e($old['facilities'] ?? $room['facilities']) ?>"
                   placeholder="Proyektor, Whiteboard, AC (pisahkan koma)">
          </div>

          <div class="mb-3">
            <label class="form-label" for="description">Deskripsi</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= e($old['description'] ?? $room['description']) ?></textarea>
          </div>

          <div class="mb-4">
            <label class="form-label" for="status">Status</label>
            <select class="form-select" id="status" name="status">
              <?php $curStatus = $old['status'] ?? $room['status']; ?>
              <option value="available"   <?= $curStatus === 'available'   ? 'selected' : '' ?>>Tersedia</option>
              <option value="unavailable" <?= $curStatus === 'unavailable' ? 'selected' : '' ?>>Tidak Tersedia</option>
            </select>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
              <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
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


<?php
/**
 * pages/rooms.php
 * Halaman publik: daftar semua ruangan.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'Daftar Ruangan';
require_once __DIR__ . '/../includes/header.php';

$rooms = $conn->query("SELECT * FROM rooms ORDER BY status ASC, name ASC");
?>

<div class="container py-5">
  <div class="page-header">
    <div>
      <h1 class="page-title"><i class="bi bi-grid me-2"></i>Daftar Ruangan</h1>
      <p class="page-subtitle">Informasi kapasitas dan fasilitas seluruh ruangan</p>
    </div>
    <a href="/roombooking/pages/booking.php" class="btn-primary-custom">
      <i class="bi bi-calendar-plus"></i> Buat Pemesanan
    </a>
  </div>

  <?php if ($rooms && $rooms->num_rows > 0): ?>
  <div class="row g-4">
    <?php while ($room = $rooms->fetch_assoc()): ?>
    <div class="col-md-6 col-lg-4">
      <div class="room-card h-100 <?= $room['status']==='unavailable' ? 'opacity-75' : '' ?>">
        <div class="room-card-header" style="<?= $room['status']==='unavailable' ? 'background:linear-gradient(135deg,#64748b,#94a3b8)' : '' ?>">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h5 class="mb-0 fw-bold"><?= e($room['name']) ?></h5>
            <?= roomStatusBadge($room['status']) ?>
          </div>
          <span class="room-capacity-badge">
            <i class="bi bi-people-fill"></i> <?= $room['capacity'] ?> orang
          </span>
        </div>
        <div class="room-card-body d-flex flex-column">
          <?php if (!empty($room['description'])): ?>
          <p class="text-muted small mb-3"><?= e($room['description']) ?></p>
          <?php endif; ?>

          <?php if (!empty($room['facilities'])): ?>
          <div class="mb-3">
            <div class="text-muted small fw-semibold mb-1">
              <i class="bi bi-tools me-1"></i>Fasilitas:
            </div>
            <?php foreach (explode(',', $room['facilities']) as $f): ?>
            <span class="facility-tag"><?= e(trim($f)) ?></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <div class="mt-auto">
            <?php if ($room['status'] === 'available'): ?>
            <a href="/roombooking/pages/booking.php?room_id=<?= $room['id'] ?>"
               class="btn btn-primary btn-sm w-100">
              <i class="bi bi-calendar-plus me-1"></i>Pesan Ruangan Ini
            </a>
            <?php else: ?>
            <button class="btn btn-secondary btn-sm w-100" disabled>
              <i class="bi bi-dash-circle me-1"></i>Tidak Tersedia
            </button>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
  <?php else: ?>
  <div class="empty-state card">
    <i class="bi bi-building-slash d-block"></i>
    <h5>Belum ada data ruangan</h5>
    <p>Silakan hubungi administrator untuk menambah ruangan.</p>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


<?php
/**
 * admin/index.php
 * Dashboard utama admin.
 */
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
require_once __DIR__ . '/includes/admin_header.php';

$flash = getFlash();
$stats = getDashboardStats($conn);

// Pemesanan terbaru (10 terakhir)
$recentBookings = $conn->query(
    "SELECT b.*, r.name AS room_name
     FROM bookings b JOIN rooms r ON b.room_id=r.id
     ORDER BY b.created_at DESC LIMIT 10"
);

// Pemesanan hari ini
$todayBookings = $conn->query(
    "SELECT b.*, r.name AS room_name
     FROM bookings b JOIN rooms r ON b.room_id=r.id
     WHERE b.booking_date=CURDATE() AND b.status='active'
     ORDER BY b.start_time ASC"
);
?>

<?= $flash ?>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="bi bi-grid-3x3-gap"></i></div>
      <div>
        <div class="stat-label">Ruangan Tersedia</div>
        <div class="stat-value"><?= $stats['rooms'] ?></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon green"><i class="bi bi-calendar-check"></i></div>
      <div>
        <div class="stat-label">Pemesanan Aktif</div>
        <div class="stat-value"><?= $stats['active_bookings'] ?></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon orange"><i class="bi bi-calendar-day"></i></div>
      <div>
        <div class="stat-label">Pemesanan Hari Ini</div>
        <div class="stat-value"><?= $stats['today_bookings'] ?></div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon red"><i class="bi bi-x-circle"></i></div>
      <div>
        <div class="stat-label">Dibatalkan</div>
        <div class="stat-value"><?= $stats['cancelled_bookings'] ?></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Pemesanan Hari Ini -->
  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-calendar-day me-2"></i>Pemesanan Hari Ini</span>
        <span class="badge bg-primary"><?= $stats['today_bookings'] ?></span>
      </div>
      <div class="card-body p-0">
        <?php if ($todayBookings->num_rows > 0): ?>
        <div class="list-group list-group-flush">
          <?php while ($b = $todayBookings->fetch_assoc()): ?>
          <div class="list-group-item border-0 py-3 px-4">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="fw-semibold small"><?= e($b['booker_name']) ?></div>
                <div class="text-muted" style="font-size:.8rem;">
                  <i class="bi bi-building me-1"></i><?= e($b['room_name']) ?>
                </div>
              </div>
              <div class="text-end">
                <div class="badge bg-primary-subtle text-primary small" style="font-size:.75rem;">
                  <?= formatTime($b['start_time']) ?> – <?= formatTime($b['end_time']) ?>
                </div>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
          <i class="bi bi-calendar-x d-block"></i>
          <p class="small">Tidak ada pemesanan hari ini.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Pemesanan Terbaru -->
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-clock-history me-2"></i>Pemesanan Terbaru</span>
        <a href="/roombooking/admin/bookings.php" class="btn btn-sm btn-outline-primary">
          Lihat Semua
        </a>
      </div>
      <div class="card-body p-0">
        <?php if ($recentBookings->num_rows > 0): ?>
        <div class="table-responsive">
          <table class="table table-roombooking mb-0">
            <thead>
              <tr>
                <th>Pemesan</th>
                <th>Ruangan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($b = $recentBookings->fetch_assoc()): ?>
              <tr data-status="<?= e($b['status']) ?>">
                <td class="fw-semibold"><?= e($b['booker_name']) ?></td>
                <td><?= e($b['room_name']) ?></td>
                <td><?= formatDate($b['booking_date']) ?></td>
                <td><?= formatTime($b['start_time']) ?>–<?= formatTime($b['end_time']) ?></td>
                <td><?= statusBadge($b['status']) ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
          <i class="bi bi-calendar-x d-block"></i>
          <p class="small">Belum ada data pemesanan.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>


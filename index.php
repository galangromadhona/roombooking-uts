<?php
/**
 * index.php
 * Halaman beranda / landing page publik.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Beranda';
require_once __DIR__ . '/includes/header.php';

// Ambil ruangan yang tersedia
$rooms = $conn->query("SELECT * FROM rooms WHERE status='available' ORDER BY name LIMIT 6");

// Statistik cepat
$stats = getDashboardStats($conn);
?>

<!-- Hero Section -->
<section class="hero-section">
  <div class="container">
    <h1><i class="bi bi-building me-2"></i>RoomBooking</h1>
    <p>Sistem pemesanan ruang rapat yang mudah, cepat, dan transparan untuk kebutuhan organisasi Anda.</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="/roombooking/pages/booking.php"
         class="btn btn-light btn-lg fw-semibold px-4">
        <i class="bi bi-calendar-plus me-2"></i>Pesan Sekarang
      </a>
      <a href="/roombooking/pages/rooms.php"
         class="btn btn-outline-light btn-lg px-4">
        <i class="bi bi-grid me-2"></i>Lihat Ruangan
      </a>
    </div>
  </div>
</section>

<!-- Statistik -->
<section class="py-5 bg-white border-bottom">
  <div class="container">
    <div class="row g-4 text-center">
      <div class="col-6 col-md-3">
        <div class="fw-bold fs-2 text-primary"><?= $stats['rooms'] ?></div>
        <div class="text-muted small">Ruangan Tersedia</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="fw-bold fs-2 text-success"><?= $stats['active_bookings'] ?></div>
        <div class="text-muted small">Pemesanan Aktif</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="fw-bold fs-2 text-warning"><?= $stats['today_bookings'] ?></div>
        <div class="text-muted small">Pemesanan Hari Ini</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="fw-bold fs-2" style="color:var(--primary)"><?= $stats['total_bookings'] ?></div>
        <div class="text-muted small">Total Pemesanan</div>
      </div>
    </div>
  </div>
</section>

<!-- Daftar Ruangan Tersedia -->
<section class="py-5">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h2 class="fw-bold mb-1" style="color:var(--primary)">Ruangan Tersedia</h2>
        <p class="text-muted mb-0 small">Pilih ruangan yang sesuai kebutuhan rapat Anda</p>
      </div>
      <a href="/roombooking/pages/rooms.php" class="btn btn-outline-primary btn-sm">
        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>

    <?php if ($rooms && $rooms->num_rows > 0): ?>
    <div class="row g-4">
      <?php while ($room = $rooms->fetch_assoc()): ?>
      <div class="col-md-6 col-lg-4">
        <div class="room-card h-100">
          <div class="room-card-header">
            <div class="d-flex justify-content-between align-items-start">
              <h5 class="mb-1 fw-bold"><?= e($room['name']) ?></h5>
              <span class="room-capacity-badge">
                <i class="bi bi-people-fill"></i> <?= $room['capacity'] ?> orang
              </span>
            </div>
          </div>
          <div class="room-card-body">
            <?php if (!empty($room['description'])): ?>
            <p class="text-muted small mb-3"><?= e($room['description']) ?></p>
            <?php endif; ?>
            <?php if (!empty($room['facilities'])): ?>
            <div class="mb-3">
              <?php foreach (explode(',', $room['facilities']) as $f): ?>
              <span class="facility-tag"><?= e(trim($f)) ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <a href="/roombooking/pages/booking.php?room_id=<?= $room['id'] ?>"
               class="btn btn-primary btn-sm w-100">
              <i class="bi bi-calendar-plus me-1"></i>Pesan Ruangan Ini
            </a>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <i class="bi bi-building-slash d-block"></i>
      <h5>Belum ada ruangan tersedia</h5>
      <p>Silakan hubungi administrator.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Cara Penggunaan -->
<section class="py-5 bg-white">
  <div class="container">
    <h2 class="fw-bold text-center mb-5" style="color:var(--primary)">Cara Memesan Ruangan</h2>
    <div class="row g-4 text-center">
      <div class="col-md-4">
        <div class="p-4">
          <div class="mb-3">
            <span style="width:56px;height:56px;background:#dbeafe;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;color:#2563eb;">
              <i class="bi bi-grid"></i>
            </span>
          </div>
          <h5 class="fw-bold">1. Pilih Ruangan</h5>
          <p class="text-muted small">Lihat daftar ruangan, kapasitas, dan fasilitas yang tersedia.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4">
          <div class="mb-3">
            <span style="width:56px;height:56px;background:#dcfce7;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;color:#16a34a;">
              <i class="bi bi-calendar-plus"></i>
            </span>
          </div>
          <h5 class="fw-bold">2. Isi Form Pemesanan</h5>
          <p class="text-muted small">Masukkan nama, tanggal, jam, dan keperluan rapat Anda.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4">
          <div class="mb-3">
            <span style="width:56px;height:56px;background:#ffedd5;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;color:#ea580c;">
              <i class="bi bi-check-circle"></i>
            </span>
          </div>
          <h5 class="fw-bold">3. Konfirmasi</h5>
          <p class="text-muted small">Sistem memvalidasi jadwal, dan pemesanan langsung tercatat.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


<?php
/**
 * pages/my-bookings.php
 * Halaman publik: daftar semua pemesanan yang ada.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Daftar Pemesanan';
require_once __DIR__ . '/../includes/header.php';

$flash = getFlash();

// Filter (opsional)
$filterStatus = $_GET['status'] ?? '';
$filterDate   = $_GET['date']   ?? '';
$search       = $_GET['q']      ?? '';

$sql = "SELECT b.*, r.name AS room_name
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        WHERE 1=1";
$params = [];
$types  = '';

if ($filterStatus !== '' && in_array($filterStatus, ['active', 'cancelled'])) {
    $sql     .= " AND b.status = ?";
    $params[] = $filterStatus;
    $types   .= 's';
}
if ($filterDate !== '') {
    $sql     .= " AND b.booking_date = ?";
    $params[] = $filterDate;
    $types   .= 's';
}
if ($search !== '') {
    $sql     .= " AND (b.booker_name LIKE ? OR b.purpose LIKE ?)";
    $like     = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $types   .= 'ss';
}

$sql .= " ORDER BY b.booking_date DESC, b.start_time ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$bookings = $stmt->get_result();
$stmt->close();
?>

<div class="container py-5">
  <div class="page-header">
    <div>
      <h1 class="page-title"><i class="bi bi-list-check me-2"></i>Daftar Pemesanan</h1>
      <p class="page-subtitle">Lihat dan kelola pemesanan ruang rapat</p>
    </div>
    <a href="/roombooking/pages/booking.php" class="btn-primary-custom">
      <i class="bi bi-calendar-plus"></i>Buat Pemesanan
    </a>
  </div>

  <?= $flash ?>

  <!-- Filter -->
  <div class="card mb-4">
    <div class="card-body py-3">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-4">
          <label class="form-label small mb-1">Cari nama / keperluan</label>
          <input type="text" class="form-control form-control-sm" name="q"
                 value="<?= e($search) ?>" placeholder="Cari...">
        </div>
        <div class="col-sm-3">
          <label class="form-label small mb-1">Status</label>
          <select class="form-select form-select-sm" name="status">
            <option value="">Semua Status</option>
            <option value="active"    <?= $filterStatus==='active'    ? 'selected' : '' ?>>Aktif</option>
            <option value="cancelled" <?= $filterStatus==='cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
          </select>
        </div>
        <div class="col-sm-3">
          <label class="form-label small mb-1">Tanggal</label>
          <input type="date" class="form-control form-control-sm" name="date"
                 value="<?= e($filterDate) ?>">
        </div>
        <div class="col-sm-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary btn-sm flex-fill">
            <i class="bi bi-search me-1"></i>Filter
          </button>
          <a href="/roombooking/pages/my-bookings.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-x"></i>
          </a>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabel -->
  <div class="card">
    <div class="card-body p-0">
      <?php if ($bookings->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-roombooking mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Nama Pemesan</th>
              <th>Ruangan</th>
              <th>Tanggal</th>
              <th>Jam Mulai</th>
              <th>Jam Selesai</th>
              <th>Keperluan</th>
              <th>Status</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; while ($b = $bookings->fetch_assoc()): ?>
            <tr data-status="<?= e($b['status']) ?>">
              <td class="text-muted"><?= $no++ ?></td>
              <td class="fw-semibold"><?= e($b['booker_name']) ?></td>
              <td><?= e($b['room_name']) ?></td>
              <td><?= formatDate($b['booking_date']) ?></td>
              <td><?= formatTime($b['start_time']) ?></td>
              <td><?= formatTime($b['end_time']) ?></td>
              <td style="max-width:200px;">
                <span title="<?= e($b['purpose']) ?>" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;max-width:180px;">
                  <?= e($b['purpose']) ?>
                </span>
              </td>
              <td><?= statusBadge($b['status']) ?></td>
              <td class="text-center">
                <?php if ($b['status'] === 'active'): ?>
                <a href="/roombooking/pages/edit-booking.php?id=<?= $b['id'] ?>"
                   class="btn btn-warning btn-sm me-1">
                  <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="/roombooking/process/cancel-booking.php?id=<?= $b['id'] ?>"
                   class="btn btn-danger btn-sm"
                   data-confirm="Yakin ingin membatalkan pemesanan ini?">
                  <i class="bi bi-x-circle"></i> Batal
                </a>
                <?php else: ?>
                <span class="text-muted small">—</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-calendar-x d-block"></i>
        <h5>Belum ada pemesanan</h5>
        <p class="small">Klik tombol "Buat Pemesanan" untuk memesan ruangan.</p>
        <a href="/roombooking/pages/booking.php" class="btn btn-primary btn-sm mt-2">
          <i class="bi bi-calendar-plus me-1"></i>Buat Pemesanan
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


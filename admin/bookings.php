<?php
/**
 * admin/bookings.php
 * Halaman admin: manajemen seluruh data pemesanan.
 */
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$pageTitle  = 'Data Pemesanan';
$activePage = 'bookings';
require_once __DIR__ . '/includes/admin_header.php';

$flash = getFlash();

// Filter
$filterStatus = $_GET['status'] ?? '';
$filterRoom   = $_GET['room']   ?? '';
$filterDate   = $_GET['date']   ?? '';
$search       = $_GET['q']      ?? '';

$sql = "SELECT b.*, r.name AS room_name
        FROM bookings b JOIN rooms r ON b.room_id=r.id
        WHERE 1=1";
$params = [];
$types  = '';

if ($filterStatus !== '' && in_array($filterStatus, ['active','cancelled'])) {
    $sql     .= " AND b.status=?";
    $params[] = $filterStatus;
    $types   .= 's';
}
if ($filterRoom !== '') {
    $filterRoomInt = (int)$filterRoom;
    $sql     .= " AND b.room_id=?";
    $params[] = $filterRoomInt;
    $types   .= 'i';
}
if ($filterDate !== '') {
    $sql     .= " AND b.booking_date=?";
    $params[] = $filterDate;
    $types   .= 's';
}
if ($search !== '') {
    $like     = '%' . $search . '%';
    $sql     .= " AND (b.booker_name LIKE ? OR b.purpose LIKE ?)";
    $params[] = $like;
    $params[] = $like;
    $types   .= 'ss';
}
$sql .= " ORDER BY b.booking_date DESC, b.start_time ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$bookings = $stmt->get_result();
$stmt->close();

// Ambil ruangan untuk filter dropdown
$allRooms = $conn->query("SELECT id, name FROM rooms ORDER BY name");
?>

<?= $flash ?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-calendar-check me-2"></i>Data Pemesanan</h1>
    <p class="page-subtitle">Kelola seluruh pemesanan ruang rapat</p>
  </div>
</div>

<!-- Filter -->
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-sm-3">
        <label class="form-label small mb-1">Cari</label>
        <input type="text" class="form-control form-control-sm" name="q"
               value="<?= e($search) ?>" placeholder="Nama / keperluan...">
      </div>
      <div class="col-sm-2">
        <label class="form-label small mb-1">Status</label>
        <select class="form-select form-select-sm" name="status">
          <option value="">Semua</option>
          <option value="active"    <?= $filterStatus==='active'    ? 'selected':'' ?>>Aktif</option>
          <option value="cancelled" <?= $filterStatus==='cancelled' ? 'selected':'' ?>>Dibatalkan</option>
        </select>
      </div>
      <div class="col-sm-3">
        <label class="form-label small mb-1">Ruangan</label>
        <select class="form-select form-select-sm" name="room">
          <option value="">Semua Ruangan</option>
          <?php while ($r = $allRooms->fetch_assoc()): ?>
          <option value="<?= $r['id'] ?>" <?= $filterRoom==$r['id'] ? 'selected':'' ?>>
            <?= e($r['name']) ?>
          </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-sm-2">
        <label class="form-label small mb-1">Tanggal</label>
        <input type="date" class="form-control form-control-sm" name="date"
               value="<?= e($filterDate) ?>">
      </div>
      <div class="col-sm-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm flex-fill">
          <i class="bi bi-search me-1"></i>Filter
        </button>
        <a href="/roombooking/admin/bookings.php" class="btn btn-outline-secondary btn-sm">
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
            <td style="max-width:180px;">
              <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                    title="<?= e($b['purpose']) ?>">
                <?= e($b['purpose']) ?>
              </span>
            </td>
            <td><?= statusBadge($b['status']) ?></td>
            <td class="text-center" style="white-space:nowrap;">
              <a href="/roombooking/admin/edit-booking.php?id=<?= $b['id'] ?>"
                 class="btn btn-warning btn-sm me-1">
                <i class="bi bi-pencil"></i>
              </a>
              <?php if ($b['status'] === 'active'): ?>
              <a href="/roombooking/admin/process/cancel-booking.php?id=<?= $b['id'] ?>"
                 class="btn btn-secondary btn-sm me-1"
                 data-confirm="Yakin ingin membatalkan pemesanan ini?">
                <i class="bi bi-x-circle"></i>
              </a>
              <?php endif; ?>
              <a href="/roombooking/admin/process/delete-booking.php?id=<?= $b['id'] ?>"
                 class="btn btn-danger btn-sm"
                 data-confirm="Yakin ingin MENGHAPUS permanen pemesanan ini?">
                <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <i class="bi bi-calendar-x d-block"></i>
      <h5>Tidak ada data pemesanan</h5>
      <p class="small">Belum ada pemesanan yang sesuai dengan filter.</p>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>


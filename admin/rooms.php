<?php
/**
 * admin/rooms.php
 * Halaman admin: manajemen data ruangan.
 */
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$pageTitle  = 'Data Ruangan';
$activePage = 'rooms';
require_once __DIR__ . '/includes/admin_header.php';

$flash = getFlash();

// Ambil semua ruangan
$rooms = $conn->query("SELECT * FROM rooms ORDER BY name ASC");
?>

<?= $flash ?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-grid-3x3-gap me-2"></i>Data Ruangan</h1>
    <p class="page-subtitle">Kelola daftar ruangan yang tersedia</p>
  </div>
  <a href="/roombooking/admin/add-room.php" class="btn-primary-custom">
    <i class="bi bi-plus-lg"></i> Tambah Ruangan
  </a>
</div>

<div class="card">
  <div class="card-body p-0">
    <?php if ($rooms->num_rows > 0): ?>
    <div class="table-responsive">
      <table class="table table-roombooking mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Nama Ruangan</th>
            <th>Kapasitas</th>
            <th>Fasilitas</th>
            <th>Deskripsi</th>
            <th>Status</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; while ($room = $rooms->fetch_assoc()): ?>
          <tr>
            <td class="text-muted"><?= $no++ ?></td>
            <td class="fw-semibold"><?= e($room['name']) ?></td>
            <td>
              <span class="badge bg-primary-subtle text-primary">
                <i class="bi bi-people me-1"></i><?= $room['capacity'] ?> orang
              </span>
            </td>
            <td style="max-width:220px;">
              <?php foreach (explode(',', $room['facilities'] ?? '') as $f): ?>
              <span class="facility-tag"><?= e(trim($f)) ?></span>
              <?php endforeach; ?>
            </td>
            <td style="max-width:200px;">
              <span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:190px;"
                    title="<?= e($room['description'] ?? '') ?>">
                <?= e($room['description'] ?? '—') ?>
              </span>
            </td>
            <td><?= roomStatusBadge($room['status']) ?></td>
            <td class="text-center">
              <a href="/roombooking/admin/edit-room.php?id=<?= $room['id'] ?>"
                 class="btn btn-warning btn-sm me-1">
                <i class="bi bi-pencil"></i> Edit
              </a>
              <a href="/roombooking/admin/process/delete-room.php?id=<?= $room['id'] ?>"
                 class="btn btn-danger btn-sm"
                 data-confirm="Yakin ingin menghapus ruangan '<?= e($room['name']) ?>'? Semua data pemesanan terkait juga akan terhapus.">
                <i class="bi bi-trash"></i> Hapus
              </a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <i class="bi bi-building-slash d-block"></i>
      <h5>Belum ada data ruangan</h5>
      <p class="small">Klik tombol "Tambah Ruangan" untuk mulai menambah data.</p>
      <a href="/roombooking/admin/add-room.php" class="btn btn-primary btn-sm mt-2">
        <i class="bi bi-plus-lg me-1"></i>Tambah Ruangan
      </a>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>


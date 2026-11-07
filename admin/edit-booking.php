<?php
/**
 * admin/edit-booking.php
 * Halaman admin: form edit pemesanan.
 */
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    setFlash('danger', 'ID pemesanan tidak valid.');
    redirect('/roombooking/admin/bookings.php');
}

// Ambil data booking
$stmt = $conn->prepare("SELECT b.*, r.name AS room_name FROM bookings b JOIN rooms r ON b.room_id=r.id WHERE b.id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    setFlash('danger', 'Pemesanan tidak ditemukan.');
    redirect('/roombooking/admin/bookings.php');
}

$pageTitle  = 'Edit Pemesanan';
$activePage = 'bookings';
require_once __DIR__ . '/includes/admin_header.php';

$flash = getFlash();
$old   = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
$today = date('Y-m-d');

// Ambil ruangan
$rooms = $conn->query("SELECT id, name, capacity FROM rooms ORDER BY name");
?>

<?= $flash ?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-pencil-square me-2"></i>Edit Pemesanan</h1>
    <p class="page-subtitle">Ubah detail pemesanan #<?= $id ?></p>
  </div>
  <a href="/roombooking/admin/bookings.php" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Kembali
  </a>
</div>

<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header">
        <i class="bi bi-pencil me-2"></i>Edit Pemesanan #<?= $id ?>
      </div>
      <div class="card-body p-4">
        <form method="POST" action="/roombooking/admin/process/update-booking.php"
              id="booking-form" novalidate>
          <input type="hidden" name="id" value="<?= $id ?>">

          <div class="mb-3">
            <label class="form-label" for="booker_name">
              Nama Pemesan <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="booker_name" name="booker_name"
                   value="<?= e($old['booker_name'] ?? $booking['booker_name']) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label" for="room_id">
              Ruangan <span class="text-danger">*</span>
            </label>
            <select class="form-select" id="room_id" name="room_id" required>
              <option value="">— Pilih Ruangan —</option>
              <?php while ($room = $rooms->fetch_assoc()):
                $selId = $old['room_id'] ?? $booking['room_id'];
                $sel = ($selId == $room['id']) ? 'selected' : '';
              ?>
              <option value="<?= $room['id'] ?>" <?= $sel ?>>
                <?= e($room['name']) ?> (<?= $room['capacity'] ?> orang)
              </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label" for="booking_date">
              Tanggal <span class="text-danger">*</span>
            </label>
            <input type="date" class="form-control" id="booking_date" name="booking_date"
                   value="<?= e($old['booking_date'] ?? $booking['booking_date']) ?>"
                   min="<?= $today ?>" required>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-6">
              <label class="form-label" for="start_time">
                Jam Mulai <span class="text-danger">*</span>
              </label>
              <input type="time" class="form-control" id="start_time" name="start_time"
                     value="<?= e(formatTime($old['start_time'] ?? $booking['start_time'])) ?>" required>
            </div>
            <div class="col-6">
              <label class="form-label" for="end_time">
                Jam Selesai <span class="text-danger">*</span>
              </label>
              <input type="time" class="form-control" id="end_time" name="end_time"
                     value="<?= e(formatTime($old['end_time'] ?? $booking['end_time'])) ?>" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="status">Status</label>
            <select class="form-select" id="status" name="status">
              <?php $curStatus = $old['status'] ?? $booking['status']; ?>
              <option value="active"    <?= $curStatus==='active'    ? 'selected':'' ?>>Aktif</option>
              <option value="cancelled" <?= $curStatus==='cancelled' ? 'selected':'' ?>>Dibatalkan</option>
            </select>
          </div>

          <div class="mb-4">
            <label class="form-label" for="purpose">
              Keperluan <span class="text-danger">*</span>
            </label>
            <textarea class="form-control" id="purpose" name="purpose" rows="3" required><?= e($old['purpose'] ?? $booking['purpose']) ?></textarea>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
              <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
            </button>
            <a href="/roombooking/admin/bookings.php" class="btn btn-outline-secondary px-4">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>


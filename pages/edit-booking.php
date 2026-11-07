<?php
/**
 * pages/edit-booking.php
 * Halaman publik: form edit pemesanan yang sudah ada.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Edit Pemesanan';
require_once __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    setFlash('danger', 'ID pemesanan tidak valid.');
    redirect('/roombooking/pages/my-bookings.php');
}

// Ambil data booking
$stmt = $conn->prepare("SELECT b.*, r.name AS room_name FROM bookings b JOIN rooms r ON b.room_id=r.id WHERE b.id=? AND b.status='active'");
$stmt->bind_param('i', $id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    setFlash('danger', 'Pemesanan tidak ditemukan atau sudah dibatalkan.');
    redirect('/roombooking/pages/my-bookings.php');
}

// Ambil ruangan tersedia
$rooms = $conn->query("SELECT id, name, capacity FROM rooms WHERE status='available' ORDER BY name");

$flash = getFlash();
$old   = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);
$today = date('Y-m-d');
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="page-header mb-4">
        <div>
          <h1 class="page-title"><i class="bi bi-pencil-square me-2"></i>Edit Pemesanan</h1>
          <p class="page-subtitle">Ubah detail pemesanan ruang rapat</p>
        </div>
        <a href="/roombooking/pages/my-bookings.php" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
      </div>

      <?= $flash ?>

      <div class="card">
        <div class="card-header">
          <i class="bi bi-pencil me-2"></i>Edit Pemesanan #<?= $id ?>
        </div>
        <div class="card-body p-4">
          <form method="POST" action="/roombooking/process/update-booking.php"
                id="booking-form" novalidate>
            <input type="hidden" name="id" value="<?= $id ?>">

            <!-- Nama Pemesan -->
            <div class="mb-3">
              <label class="form-label" for="booker_name">
                Nama Pemesan <span class="text-danger">*</span>
              </label>
              <input type="text" class="form-control" id="booker_name" name="booker_name"
                     value="<?= e($old['booker_name'] ?? $booking['booker_name']) ?>"
                     required>
            </div>

            <!-- Pilih Ruangan -->
            <div class="mb-3">
              <label class="form-label" for="room_id">
                Ruangan <span class="text-danger">*</span>
              </label>
              <select class="form-select" id="room_id" name="room_id" required>
                <option value="">— Pilih Ruangan —</option>
                <?php while ($room = $rooms->fetch_assoc()):
                  $selectedRoomId = $old['room_id'] ?? $booking['room_id'];
                  $sel = ($selectedRoomId == $room['id']) ? 'selected' : '';
                ?>
                <option value="<?= $room['id'] ?>" <?= $sel ?>>
                  <?= e($room['name']) ?> (Kapasitas: <?= $room['capacity'] ?> orang)
                </option>
                <?php endwhile; ?>
              </select>
            </div>

            <!-- Tanggal -->
            <div class="mb-3">
              <label class="form-label" for="booking_date">
                Tanggal Pemesanan <span class="text-danger">*</span>
              </label>
              <input type="date" class="form-control" id="booking_date" name="booking_date"
                     min="<?= $today ?>"
                     value="<?= e($old['booking_date'] ?? $booking['booking_date']) ?>"
                     required>
            </div>

            <!-- Jam Mulai & Selesai -->
            <div class="row g-3 mb-3">
              <div class="col-6">
                <label class="form-label" for="start_time">
                  Jam Mulai <span class="text-danger">*</span>
                </label>
                <input type="time" class="form-control" id="start_time" name="start_time"
                       value="<?= e(formatTime($old['start_time'] ?? $booking['start_time'])) ?>"
                       required>
              </div>
              <div class="col-6">
                <label class="form-label" for="end_time">
                  Jam Selesai <span class="text-danger">*</span>
                </label>
                <input type="time" class="form-control" id="end_time" name="end_time"
                       value="<?= e(formatTime($old['end_time'] ?? $booking['end_time'])) ?>"
                       required>
              </div>
            </div>

            <!-- Keperluan -->
            <div class="mb-4">
              <label class="form-label" for="purpose">
                Keperluan Rapat <span class="text-danger">*</span>
              </label>
              <textarea class="form-control" id="purpose" name="purpose" rows="3"
                        required><?= e($old['purpose'] ?? $booking['purpose']) ?></textarea>
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
              </button>
              <a href="/roombooking/pages/my-bookings.php"
                 class="btn btn-outline-secondary">
                <i class="bi bi-x me-1"></i>Batal
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


<?php
/**
 * pages/booking.php
 * Halaman publik: form pemesanan ruangan baru.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Pesan Ruangan';
require_once __DIR__ . '/../includes/header.php';

// Ambil daftar ruangan yang tersedia
$stmt = $conn->prepare("SELECT id, name, capacity FROM rooms WHERE status='available' ORDER BY name");
$stmt->execute();
$rooms = $stmt->get_result();
$stmt->close();

// Pre-select room jika ada query param
$preSelectedRoom = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

// Ambil nilai lama jika ada (untuk repopulate setelah error)
$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);

$flash = getFlash();
$today = date('Y-m-d');
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="page-header mb-4">
        <div>
          <h1 class="page-title"><i class="bi bi-calendar-plus me-2"></i>Pesan Ruangan</h1>
          <p class="page-subtitle">Isi formulir di bawah untuk memesan ruang rapat</p>
        </div>
        <a href="/roombooking/pages/my-bookings.php" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-list-check me-1"></i>Daftar Pemesanan
        </a>
      </div>

      <?= $flash ?>

      <div class="card">
        <div class="card-header">
          <i class="bi bi-pencil-square me-2"></i>Formulir Pemesanan
        </div>
        <div class="card-body p-4">
          <form method="POST" action="/roombooking/process/save-booking.php"
                id="booking-form" novalidate>

            <!-- Nama Pemesan -->
            <div class="mb-3">
              <label class="form-label" for="booker_name">
                Nama Pemesan <span class="text-danger">*</span>
              </label>
              <input type="text" class="form-control" id="booker_name" name="booker_name"
                     placeholder="Masukkan nama lengkap Anda"
                     value="<?= e($old['booker_name'] ?? '') ?>" required>
            </div>

            <!-- Pilih Ruangan -->
            <div class="mb-3">
              <label class="form-label" for="room_id">
                Ruangan <span class="text-danger">*</span>
              </label>
              <select class="form-select" id="room_id" name="room_id" required>
                <option value="">— Pilih Ruangan —</option>
                <?php
                $rooms->data_seek(0);
                while ($room = $rooms->fetch_assoc()):
                  $sel = (($old['room_id'] ?? $preSelectedRoom) == $room['id']) ? 'selected' : '';
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
                     value="<?= e($old['booking_date'] ?? '') ?>" required>
            </div>

            <!-- Jam Mulai & Selesai -->
            <div class="row g-3 mb-3">
              <div class="col-6">
                <label class="form-label" for="start_time">
                  Jam Mulai <span class="text-danger">*</span>
                </label>
                <input type="time" class="form-control" id="start_time" name="start_time"
                       value="<?= e($old['start_time'] ?? '') ?>" required>
              </div>
              <div class="col-6">
                <label class="form-label" for="end_time">
                  Jam Selesai <span class="text-danger">*</span>
                </label>
                <input type="time" class="form-control" id="end_time" name="end_time"
                       value="<?= e($old['end_time'] ?? '') ?>" required>
                <div class="form-text">Harus lebih besar dari jam mulai.</div>
              </div>
            </div>

            <!-- Keperluan -->
            <div class="mb-4">
              <label class="form-label" for="purpose">
                Keperluan Rapat <span class="text-danger">*</span>
              </label>
              <textarea class="form-control" id="purpose" name="purpose" rows="3"
                        placeholder="Contoh: Rapat koordinasi bulanan divisi IT"
                        required><?= e($old['purpose'] ?? '') ?></textarea>
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                <i class="bi bi-check-circle me-2"></i>Simpan Pemesanan
              </button>
              <a href="/roombooking/" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
              </a>
            </div>
          </form>
        </div>
      </div>

      <!-- Info validasi -->
      <div class="alert alert-info d-flex gap-2 align-items-start mt-3">
        <i class="bi bi-info-circle-fill fs-5 mt-1"></i>
        <div>
          <strong>Ketentuan Pemesanan:</strong>
          <ul class="mb-0 mt-1 small">
            <li>Ruangan yang sama tidak boleh dipesan pada waktu yang bertabrakan.</li>
            <li>Pemesanan berurutan diperbolehkan (09:00–10:00 dan 10:00–11:00).</li>
            <li>Tanggal pemesanan tidak boleh lebih awal dari hari ini.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


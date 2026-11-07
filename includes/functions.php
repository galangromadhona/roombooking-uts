<?php
/**
 * functions.php
 * Fungsi-fungsi pembantu yang digunakan di seluruh aplikasi.
 */

/**
 * Redirect ke URL tertentu.
 */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Escape output HTML untuk mencegah XSS.
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Simpan pesan flash ke session.
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Ambil dan hapus pesan flash dari session.
 * Mengembalikan string HTML alert Bootstrap atau string kosong.
 */
function getFlash(): string {
    if (!isset($_SESSION['flash'])) return '';
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    $icons = [
        'success' => 'bi-check-circle-fill',
        'danger'  => 'bi-x-circle-fill',
        'warning' => 'bi-exclamation-triangle-fill',
        'info'    => 'bi-info-circle-fill',
    ];
    $icon = $icons[$flash['type']] ?? 'bi-info-circle-fill';

    return '<div class="alert alert-' . e($flash['type']) . ' alert-dismissible fade show d-flex align-items-center" role="alert">
              <i class="bi ' . $icon . ' me-2 fs-5"></i>
              <div>' . e($flash['message']) . '</div>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
}

/**
 * Periksa apakah waktu dua pemesanan bertabrakan.
 * Bertabrakan jika: start1 < end2 AND end1 > start2
 * Pemesanan berurutan (09:00-10:00 & 10:00-11:00) DIPERBOLEHKAN.
 *
 * @param string $newStart   Format H:i:s atau H:i
 * @param string $newEnd     Format H:i:s atau H:i
 * @param string $existStart Format H:i:s atau H:i
 * @param string $existEnd   Format H:i:s atau H:i
 */
function isTimeOverlap(string $newStart, string $newEnd, string $existStart, string $existEnd): bool {
    return ($newStart < $existEnd) && ($newEnd > $existStart);
}

/**
 * Periksa jadwal bentrok di database.
 * Mengembalikan true jika ada bentrok, false jika aman.
 *
 * @param mysqli     $conn
 * @param int        $roomId
 * @param string     $date
 * @param string     $startTime
 * @param string     $endTime
 * @param int|null   $excludeBookingId  ID booking yang dikecualikan (untuk edit)
 */
function hasBookingConflict(
    mysqli $conn,
    int $roomId,
    string $date,
    string $startTime,
    string $endTime,
    ?int $excludeBookingId = null
): bool {
    $sql = "SELECT id FROM bookings
            WHERE room_id      = ?
              AND booking_date = ?
              AND status       = 'active'
              AND start_time   < ?
              AND end_time     > ?";

    $params = [$roomId, $date, $endTime, $startTime];
    $types  = 'isss';

    if ($excludeBookingId !== null) {
        $sql     .= " AND id != ?";
        $params[] = $excludeBookingId;
        $types   .= 'i';
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->store_result();
    $found = $stmt->num_rows > 0;
    $stmt->close();
    return $found;
}

/**
 * Format tanggal dari Y-m-d ke d/m/Y.
 */
function formatDate(string $date): string {
    if (empty($date)) return '-';
    return date('d/m/Y', strtotime($date));
}

/**
 * Format waktu dari H:i:s ke H:i.
 */
function formatTime(string $time): string {
    if (empty($time)) return '-';
    return substr($time, 0, 5);
}

/**
 * Kembalikan badge HTML Bootstrap berdasarkan status booking.
 */
function statusBadge(string $status): string {
    return match($status) {
        'active'    => '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>',
        'cancelled' => '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Dibatalkan</span>',
        default     => '<span class="badge bg-secondary">' . e($status) . '</span>',
    };
}

/**
 * Kembalikan badge HTML Bootstrap berdasarkan status ruangan.
 */
function roomStatusBadge(string $status): string {
    return match($status) {
        'available'   => '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Tersedia</span>',
        'unavailable' => '<span class="badge bg-danger"><i class="bi bi-dash-circle me-1"></i>Tidak Tersedia</span>',
        default       => '<span class="badge bg-secondary">' . e($status) . '</span>',
    };
}

/**
 * Hitung statistik dashboard.
 */
function getDashboardStats(mysqli $conn): array {
    $stats = [];

    $result = $conn->query("SELECT COUNT(*) FROM rooms WHERE status='available'");
    $stats['rooms'] = $result->fetch_row()[0];

    $result = $conn->query("SELECT COUNT(*) FROM bookings");
    $stats['total_bookings'] = $result->fetch_row()[0];

    $result = $conn->query("SELECT COUNT(*) FROM bookings WHERE status='active'");
    $stats['active_bookings'] = $result->fetch_row()[0];

    $result = $conn->query("SELECT COUNT(*) FROM bookings WHERE status='cancelled'");
    $stats['cancelled_bookings'] = $result->fetch_row()[0];

    $result = $conn->query("SELECT COUNT(*) FROM bookings WHERE status='active' AND booking_date=CURDATE()");
    $stats['today_bookings'] = $result->fetch_row()[0];

    return $stats;
}


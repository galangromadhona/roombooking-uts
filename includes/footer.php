<?php
/**
 * includes/footer.php
 * Footer HTML untuk halaman publik.
 */
?>
<!-- Footer -->
<footer class="mt-auto py-4" style="background:var(--primary);color:rgba(255,255,255,.75);">
  <div class="container text-center">
    <div class="mb-1">
      <i class="bi bi-building me-1"></i>
      <strong class="text-white">RoomBooking</strong> &mdash; Sistem Pemesanan Ruang Rapat
    </div>
    <div style="font-size:.8rem;opacity:.7;">
      &copy; <?= date('Y') ?> RoomBooking. All rights reserved.
    </div>
  </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="/roombooking/assets/js/app.js"></script>
</body>
</html>


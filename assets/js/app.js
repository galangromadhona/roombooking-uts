/* ==========================================================
   assets/js/app.js
   JavaScript utama aplikasi RoomBooking
   ========================================================== */

document.addEventListener('DOMContentLoaded', function () {

  /* ---- Sidebar Toggle (mobile) ---- */
  const sidebarEl  = document.getElementById('sidebar');
  const toggleBtn  = document.getElementById('sidebar-toggle');
  const overlay    = document.getElementById('sidebar-overlay');

  function openSidebar() {
    sidebarEl?.classList.add('show');
    overlay?.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
  function closeSidebar() {
    sidebarEl?.classList.remove('show');
    overlay?.classList.remove('show');
    document.body.style.overflow = '';
  }

  toggleBtn?.addEventListener('click', openSidebar);
  overlay?.addEventListener('click', closeSidebar);

  /* ---- Auto-dismiss alerts after 5 s ---- */
  document.querySelectorAll('.alert.alert-dismissible').forEach(function (alert) {
    setTimeout(function () {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
      bsAlert?.close();
    }, 5000);
  });

  /* ---- Confirm delete / cancel ---- */
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      const msg = el.dataset.confirm || 'Apakah Anda yakin?';
      if (!confirm(msg)) e.preventDefault();
    });
  });

  /* ---- Validasi form pemesanan (client-side tambahan) ---- */
  const bookingForm = document.getElementById('booking-form');
  if (bookingForm) {
    bookingForm.addEventListener('submit', function (e) {
      let valid = true;
      const today     = new Date().toISOString().split('T')[0];
      const dateField = bookingForm.querySelector('[name="booking_date"]');
      const startField= bookingForm.querySelector('[name="start_time"]');
      const endField  = bookingForm.querySelector('[name="end_time"]');

      // Tanggal tidak boleh lebih awal dari hari ini
      if (dateField && dateField.value < today) {
        showFieldError(dateField, 'Tanggal tidak boleh lebih awal dari hari ini.');
        valid = false;
      } else {
        clearFieldError(dateField);
      }

      // Jam selesai harus > jam mulai
      if (startField && endField && startField.value && endField.value) {
        if (endField.value <= startField.value) {
          showFieldError(endField, 'Jam selesai harus lebih besar dari jam mulai.');
          valid = false;
        } else {
          clearFieldError(endField);
        }
      }

      if (!valid) e.preventDefault();
    });
  }

  function showFieldError(field, msg) {
    clearFieldError(field);
    field.classList.add('is-invalid');
    const fb = document.createElement('div');
    fb.className = 'invalid-feedback';
    fb.textContent = msg;
    field.parentNode.appendChild(fb);
  }

  function clearFieldError(field) {
    if (!field) return;
    field.classList.remove('is-invalid');
    const fb = field.parentNode.querySelector('.invalid-feedback');
    if (fb) fb.remove();
  }

  /* ---- Set min date pada input date ke hari ini ---- */
  document.querySelectorAll('input[name="booking_date"]').forEach(function (el) {
    const today = new Date().toISOString().split('T')[0];
    if (!el.min) el.min = today;
  });

  /* ---- Highlight baris tabel sesuai status ---- */
  document.querySelectorAll('tr[data-status]').forEach(function (row) {
    if (row.dataset.status === 'cancelled') {
      row.style.opacity = '.65';
    }
  });

});


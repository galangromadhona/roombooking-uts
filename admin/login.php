<?php
/**
 * admin/login.php
 * Halaman login administrator.
 */
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Sudah login → redirect ke dashboard
if (!empty($_SESSION['admin_id'])) {
    redirect('/roombooking/admin/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        // Cari user berdasarkan username
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE username = ? AND role = 'admin'");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            // Login berhasil
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            setFlash('success', 'Selamat datang, ' . $user['name'] . '!');
            redirect('/roombooking/admin/');
        } else {
            $error = 'Username atau password salah. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin – RoomBooking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/roombooking/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="login-wrapper">
  <div class="login-card">
    <div class="login-logo">
      <span class="logo-icon"><i class="bi bi-building"></i></span>
      <div>
        <div class="fw-bold fs-5" style="color:var(--primary)">RoomBooking</div>
        <div class="text-muted small" style="margin-top:-2px">Admin Panel</div>
      </div>
    </div>

    <h5 class="fw-bold text-center mb-1" style="color:var(--text-main)">Masuk ke Dashboard</h5>
    <p class="text-muted text-center small mb-4">Gunakan akun administrator untuk masuk</p>

    <?php if ($error): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2">
      <i class="bi bi-x-circle-fill"></i>
      <?= e($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-3">
        <label class="form-label" for="username">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" class="form-control" id="username" name="username"
                 placeholder="Masukkan username" autocomplete="username"
                 value="<?= e($_POST['username'] ?? '') ?>" required>
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label" for="password">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" class="form-control" id="password" name="password"
                 placeholder="Masukkan password" autocomplete="current-password" required>
          <button class="btn btn-outline-secondary" type="button" id="togglePass"
                  onclick="togglePassword()">
            <i class="bi bi-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
      </button>
    </form>

    <div class="text-center mt-4">
      <a href="/roombooking/" class="text-muted small">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Situs
      </a>
    </div>

    <div class="mt-3 p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
      <div class="text-muted small text-center">
        <i class="bi bi-info-circle me-1"></i>
        Akun default: <strong>admin</strong> / <strong>password</strong>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword() {
  const pwd  = document.getElementById('password');
  const icon = document.getElementById('eyeIcon');
  if (pwd.type === 'password') {
    pwd.type = 'text';
    icon.className = 'bi bi-eye-slash';
  } else {
    pwd.type = 'password';
    icon.className = 'bi bi-eye';
  }
}
</script>
</body>
</html>


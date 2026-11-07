<?php
/**
 * database/generate_password.php
 * Jalankan sekali dari browser atau CLI untuk melihat hash password.
 * Contoh: http://localhost/roombooking/database/generate_password.php
 *
 * HAPUS FILE INI SETELAH DIGUNAKAN agar tidak menjadi celah keamanan.
 */

$password = 'admin123';
$hash     = password_hash($password, PASSWORD_DEFAULT);

echo "<pre>";
echo "Password  : {$password}\n";
echo "Hash (bcrypt): {$hash}\n\n";
echo "Gunakan hash di atas untuk UPDATE tabel users:\n";
echo "UPDATE users SET password='{$hash}' WHERE username='admin';\n";
echo "</pre>";


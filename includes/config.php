<?php
/**
 * config.php
 * Konfigurasi koneksi database MySQL menggunakan MySQLi dengan prepared statement.
 */

define('DB_HOST',   'localhost');
define('DB_USER',   'root');
define('DB_PASS',   '');
define('DB_NAME',   'roombooking');
define('DB_CHARSET','utf8mb4');

// Buat koneksi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Tangani error koneksi
if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:20px;color:#721c24;background:#f8d7da;border:1px solid #f5c6cb;border-radius:4px;">
         <strong>Koneksi Database Gagal!</strong><br>
         ' . htmlspecialchars($conn->connect_error) . '<br><br>
         Pastikan XAMPP MySQL sudah berjalan dan database <strong>roombooking</strong> sudah diimport.
         </div>');
}

// Set charset UTF-8
$conn->set_charset(DB_CHARSET);

// Set timezone PHP
date_default_timezone_set('Asia/Jakarta');


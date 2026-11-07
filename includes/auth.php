<?php
/**
 * auth.php
 * Fungsi untuk memeriksa status login admin.
 * Redirect ke halaman login jika belum login.
 */

function requireAdminLogin(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . getAdminBase() . '/login.php');
        exit;
    }
}

function getAdminBase(): string {
    // Hitung path relatif ke folder admin
    return '/roombooking/admin';
}

function isLoggedIn(): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return !empty($_SESSION['admin_id']);
}


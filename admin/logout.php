<?php
/**
 * admin/logout.php
 * Proses logout admin.
 */
session_start();
require_once __DIR__ . '/../includes/functions.php';
$_SESSION = [];
session_destroy();
redirect('/roombooking/admin/login.php');


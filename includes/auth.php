<?php
// includes/auth.php
session_start(); // Mulai sesi

if (!isset($_SESSION['pemprov_logged_in']) || $_SESSION['pemprov_logged_in'] !== true) {
    header("Location: index.php"); // Arahkan ke halaman login jika belum login
    exit();
}

// Opsional: Anda bisa mengambil data user yang login dari sesi
$loggedInUsername = $_SESSION['pemprov_username'] ?? 'Admin';
?>
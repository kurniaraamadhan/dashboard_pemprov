<?php
// includes/header.php
// Pastikan auth.php sudah di-include di halaman yang memanggil header ini
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #009688;">
    <a class="navbar-brand" href="dashboard.php">Dashboard Pemprov</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="dashboard.php">Beranda</a>
            </li>
            <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'pendaftar.php') ? 'active' : ''; ?>"> <a class="nav-link" href="pendaftar.php">Pendaftar Beasiswa</a>
            </li>
            <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'berkas_ditolak.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="berkas_ditolak.php">Berkas Ditolak</a>
            </li>
            <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'register_user.php') ? 'active' : ''; ?>">
                <a class="nav-link" href="register_user.php">Registrasi Pengguna</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Halo, <?php echo htmlspecialchars($_SESSION['pemprov_username'] ?? 'Admin'); ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#">Pengaturan Akun</a> <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
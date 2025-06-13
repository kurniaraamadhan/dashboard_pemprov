<?php
// index.php (Halaman Login untuk Staff Pemprov)
session_start();
include '../my_api_android/koneksi.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        
        $stmt = $koneksi->prepare("SELECT id_user, username, password, kampus, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                // Periksa role untuk login via web
                if ($row['role'] == 'admin' || $row['role'] == 'developer') {
                    $_SESSION['pemprov_logged_in'] = true;
                    $_SESSION['pemprov_username'] = $row['username'];
                    $_SESSION['pemprov_role'] = $row['role'];
                    $_SESSION['pemprov_id_user'] = $row['id_user'];
                    $_SESSION['pemprov_kampus'] = $row['kampus'];
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $message = "Akses ditolak. Role tidak diizinkan untuk dashboard web.";
                }
            } else {
                $message = "Password salah.";
            }
        } else {
            $message = "Username tidak ditemukan";
        }
        $stmt->close();
    } else {
        $message = "Username dan Password tidak boleh kosong.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard Pemprov</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="text-center mb-4">
                <img src="https://via.placeholder.com/100/009688/FFFFFF?text=P" alt="Pemprov Logo" class="logo-animation">
                <h3>Dashboard Admin Pemprov</h3>
                <p class="text-muted">Login untuk melanjutkan</p>
            </div>
            <?php if ($message): ?>
                <div class="alert alert-danger animate__animated animate__shakeX"><?php echo $message; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group animate__animated animate__fadeInUp">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control login-input" id="username" name="username" required>
                </div>
                <div class="form-group animate__animated animate__fadeInUp animate__delay-1s">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control login-input" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block login-button animate__animated animate__pulse animate__infinite">Login</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <script>
        $(document).ready(function() {
            $('.login-input').focus(function() {
                $(this).addClass('is-focused');
            }).blur(function() {
                if ($(this).val() === '') {
                    $(this).removeClass('is-focused');
                }
            });
            $('.login-input').each(function() {
                if ($(this).val() !== '') {
                    $(this).addClass('is-focused');
                }
            });
        });
    </script>
</body>
</html>
<?php
// register_user.php (Halaman untuk admin mendaftarkan pengguna baru)
include 'includes/auth.php'; 
if ($_SESSION['pemprov_role'] !== 'developer' && $_SESSION['pemprov_role'] !== 'admin') { 
    header("Location: dashboard.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pengguna - Dashboard Pemprov</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <h2>Registrasi Pengguna Baru</h2>
        <p class="text-muted">Formulir pendaftaran akun Staff TU atau Admin Pemprov.</p>

        <div class="card p-4 mt-4">
            <div id="responseMessage" class="alert d-none" role="alert"></div>
            
            <form id="registerForm">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="instansi">Nama Instansi:</label> <input type="text" class="form-control" id="instansi" name="instansi" placeholder="Contoh: Universitas ABC / Dinas Pendidikan" required> </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="">Pilih Role</option>
                        <option value="android">Staff TU Kampus (Aplikasi Android)</option>
                        <option value="admin">Staff Pemprov (Dashboard Web)</option>
                        </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Daftarkan Pengguna</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();

                const username = $('#username').val();
                const password = $('#password').val();
                const instansi = $('#instansi').val(); 
                const role = $('#role').val();
                const $messageDiv = $('#responseMessage');

                $messageDiv.removeClass('alert-success alert-danger').addClass('d-none').text('');

                if (!username || !password || !instansi || !role) { 
                    $messageDiv.text('Semua field wajib diisi.').removeClass('d-none').addClass('alert-danger');
                    return;
                }

                $.ajax({
                    url: '../my_api_android/api-register.php',
                    method: 'POST',
                    dataType: 'json', 
                    data: {
                        username: username,
                        password: password,
                        kampus: instansi, 
                        role: role
                    },
                    success: function(response) {
                        if (response.success) {
                            $messageDiv.text(response.message).removeClass('d-none').addClass('alert-success');
                            $('#registerForm')[0].reset();
                        } else {
                            $messageDiv.text(response.message).removeClass('d-none').addClass('alert-danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr.responseText);
                        let errorMessage = 'Terjadi kesalahan jaringan atau server.';
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.message) {
                                errorMessage = errorResponse.message;
                            }
                        } catch (e) {
                            errorMessage = 'Server mengembalikan respons tidak terduga: ' + xhr.responseText.substring(0, 100) + '...';
                        }
                        $messageDiv.text(errorMessage).removeClass('d-none').addClass('alert-danger');
                    }
                });
            });
        });
    </script>
</body>
</html>
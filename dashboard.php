<?php
// dashboard.php
include 'includes/auth.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Pemprov</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .card-stats {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .card-stats h4 {
            color: #009688;
            font-weight: bold;
        }
        .card-stats .display-4 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['pemprov_username'] ?? 'Admin'); ?>!</h2>
        <p class="text-muted">Ringkasan status beasiswa terkini.</p>

        <div id="loadingMessage" class="alert alert-info text-center">Memuat statistik...</div>
        <div id="errorMessage" class="alert alert-danger text-center d-none">Gagal memuat statistik.</div>

        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card-stats">
                    <h4 class="mb-3">Pendaftar Baru</h4>
                    <p class="display-4" id="new_applicants">...</p>
                    <p class="text-muted">Mahasiswa menunggu proses awal</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-stats">
                    <h4 class="mb-3">Berkas Pending</h4>
                    <p class="display-4" id="pending_files">...</p>
                    <p class="text-muted">Berkas perlu diverifikasi</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-stats">
                    <h4 class="mb-3">Berkas Disetujui</h4>
                    <p class="display-4" id="approved_files">...</p>
                    <p class="text-muted">Berkas sudah diverifikasi & valid</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-stats">
                    <h4 class="mb-3">Berkas Ditolak</h4>
                    <p class="display-4" id="rejected_files">...</p>
                    <p class="text-muted">Berkas tidak memenuhi syarat</p>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card-stats">
                    <h4 class="mb-3">Total Mahasiswa Terdaftar</h4>
                    <p class="display-4" id="total_students">...</p>
                    <p class="text-muted">Total data mahasiswa dalam sistem</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-stats">
                    <h4 class="mb-3">Pengumuman & Notifikasi</h4>
                    <p>Fungsionalitas untuk pengumuman umum dan notifikasi terkait deadline akan dikembangkan di sini.</p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            const API_URL_DASHBOARD_STATS = '../my_api_android/dashboard_stats.php'; // SESUAIKAN PATH API ANDA
            const userRole = '<?php echo htmlspecialchars($_SESSION['pemprov_role'] ?? 'guest'); ?>';
            const userKampus = '<?php echo htmlspecialchars($_SESSION['pemprov_kampus'] ?? ''); ?>';

            let apiUrl = API_URL_DASHBOARD_STATS + '?';
            apiUrl += `role=${encodeURIComponent(userRole)}&`;

            if (apiUrl.endsWith('&') || apiUrl.endsWith('?')) {
                apiUrl = apiUrl.slice(0, -1);
            }

            $.ajax({
                url: apiUrl,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#loadingMessage').addClass('d-none');
                    if (response.success) {
                        $('#new_applicants').text(response.data.new_applicants);
                        $('#pending_files').text(response.data.pending_files);
                        $('#approved_files').text(response.data.approved_files);
                        $('#rejected_files').text(response.data.rejected_files);
                        $('#total_students').text(response.data.total_students);
                    } else {
                        $('#errorMessage').text('Gagal memuat statistik: ' + response.message).removeClass('d-none');
                        $('#new_applicants').text('N/A');
                        $('#pending_files').text('N/A');
                        $('#approved_files').text('N/A');
                        $('#rejected_files').text('N/A');
                        $('#total_students').text('N/A');
                    }
                },
                error: function(xhr, status, error) {
                    $('#loadingMessage').addClass('d-none');
                    $('#errorMessage').text('Error jaringan saat memuat statistik. Cek konsol browser untuk detail.').removeClass('d-none');
                    console.error('AJAX Error:', status, error, xhr.responseText);
                    $('#new_applicants').text('N/A');
                    $('#pending_files').text('N/A');
                    $('#approved_files').text('N/A');
                    $('#rejected_files').text('N/A');
                    $('#total_students').text('N/A');
                }
            });
        });
    </script>
</body>
</html>
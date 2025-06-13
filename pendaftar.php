<?php
// pendaftar.php
include 'includes/auth.php';

// Cek user role yang diizinkan untuk melihat halaman ini
if ($_SESSION['pemprov_role'] !== 'admin' && $_SESSION['pemprov_role'] !== 'developer') {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pendaftar Beasiswa - Dashboard Pemprov</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .table-responsive {
            margin-top: 20px;
        }
        .action-buttons .btn {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <h2>Daftar Pendaftar Beasiswa</h2>
        <p class="text-muted">Data lengkap mahasiswa yang mendaftar beasiswa.</p>

        <div class="card p-4 mt-4">
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan NIM atau Nama">
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="Menunggu Verifikasi Pemprov">Menunggu Verifikasi Pemprov</option>
                        <option value="Diverifikasi Pemprov">Diverifikasi Pemprov</option>
                        <option value="Ditolak Pemprov">Ditolak Pemprov</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-secondary btn-block" id="resetFilterButton">Reset Filter</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>NIM</th>
                            <th>Nama Lengkap</th>
                            <th>Program Studi</th>
                            <th>Angkatan</th>
                            <th>Kampus</th>
                            <th>Status Pendaftaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pendaftarTableBody">
                        <tr><td colspan="7" class="text-center">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
            <div id="emptyState" class="alert alert-info text-center mt-3 d-none">
                Tidak ada pendaftar yang ditemukan.
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            const API_URL_MAHASISWA = '../my_api_android/mahasiswa_crud.php';

            function fetchPendaftarData() {
                const searchValue = $('#searchInput').val().toLowerCase();
                const statusValue = $('#statusFilter').val();

                const userRole = '<?php echo htmlspecialchars($_SESSION['pemprov_role'] ?? 'guest'); ?>';
                const userKampus = '<?php echo htmlspecialchars($_SESSION['pemprov_kampus'] ?? ''); ?>';

                let apiUrl = API_URL_MAHASISWA + '?';
                apiUrl += `role=${encodeURIComponent(userRole)}&`;
                // Filter kampus hanya berlaku jika role adalah 'android' (Staff TU)
                // Jadi, HANYA KIRIM PARAMETER KAMPUS JIKA ANDROID
                if (userRole === 'android') { 
                    apiUrl += `kampus=${encodeURIComponent(userKampus)}&`;
                }
                
                if (searchValue) {
                    apiUrl += `search=${encodeURIComponent(searchValue)}&`;
                }
                if (statusValue) {
                    apiUrl += `status=${encodeURIComponent(statusValue)}&`;
                }

                if (apiUrl.endsWith('&') || apiUrl.endsWith('?')) {
                    apiUrl = apiUrl.slice(0, -1);
                }
                
                console.log("Fetching Pendaftar Data from URL:", apiUrl);

                $('#pendaftarTableBody').html('<tr><td colspan="7" class="text-center">Memuat data...</td></tr>');
                $('#emptyState').addClass('d-none');

                $.ajax({
                    url: apiUrl,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log("Pendaftar API Response:", response);
                        let tableRows = '';
                        if (response.success && response.mahasiswa.length > 0) {
                            response.mahasiswa.forEach(function(pendaftar) {
                                const pendaftarName = pendaftar.nama_lengkap.toLowerCase();
                                const pendaftarNIM = pendaftar.nim.toLowerCase();
                                const pendaftarStatus = pendaftar.status_pendaftaran;
                                const pendaftarKampus = pendaftar.kampus ? pendaftar.kampus.toLowerCase() : '';

                                const matchesSearch = pendaftarName.includes(searchValue) || pendaftarNIM.includes(searchValue);
                                const matchesStatus = (statusValue === '' || pendaftarStatus === statusValue);

                                if (matchesSearch && matchesStatus) { 
                                    let statusBadgeClass = '';
                                    if (pendaftar.status_pendaftaran === 'Menunggu Verifikasi Pemprov') {
                                        statusBadgeClass = 'badge-warning';
                                    } else if (pendaftar.status_pendaftaran === 'Diverifikasi Pemprov') {
                                        statusBadgeClass = 'badge-success';
                                    } else if (pendaftar.status_pendaftaran === 'Ditolak Pemprov') {
                                        statusBadgeClass = 'badge-danger';
                                    } else {
                                        statusBadgeClass = 'badge-info';
                                    }

                                    tableRows += `
                                        <tr>
                                            <td>${pendaftar.nim}</td>
                                            <td>${pendaftar.nama_lengkap}</td>
                                            <td>${pendaftar.program_studi}</td>
                                            <td>${pendaftar.angkatan}</td>
                                            <td>${pendaftar.kampus}</td>
                                            <td><span class="badge ${statusBadgeClass}">${pendaftar.status_pendaftaran}</span></td>
                                            <td>
                                                <a href="detail_pendaftar.php?nim=${pendaftar.nim}" class="btn btn-info btn-sm">Lihat Detail</a>
                                            </td>
                                        </tr>
                                    `;
                                }
                            });
                            $('#pendaftarTableBody').html(tableRows);
                            if (tableRows === '') {
                                $('#emptyState').removeClass('d-none');
                            }
                        } else {
                            $('#pendaftarTableBody').html('');
                            $('#emptyState').removeClass('d-none');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr.responseText);
                        $('#pendaftarTableBody').html('<tr><td colspan="7" class="text-center text-danger">Gagal memuat data.</td></tr>');
                        $('#emptyState').addClass('d-none');
                    }
                });
            }

            fetchPendaftarData();

            $('#searchInput').on('keyup', fetchPendaftarData);
            $('#statusFilter').on('change', fetchPendaftarData);
            $('#resetFilterButton').on('click', function() {
                $('#searchInput').val('');
                $('#statusFilter').val('');
                fetchPendaftarData();
            });
        });
    </script>
</body>
</html>
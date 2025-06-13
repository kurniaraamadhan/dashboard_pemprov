<?php
// detail_pendaftar.php
include 'includes/auth.php'; 

// Cek user role yang diizinkan
if ($_SESSION['pemprov_role'] !== 'admin' && $_SESSION['pemprov_role'] !== 'developer') {
    header("Location: dashboard.php"); // Arahkan jika tidak diizinkan
    exit();
}

$nim = $_GET['nim'] ?? '';
if (empty($nim)) {
    // Tampilkan pesan error dan redirect jika NIM tidak ada
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Error</title><link href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet'></head><body>";
    echo "<div class='container mt-5'><div class='alert alert-danger'>NIM pendaftar tidak ditemukan.</div><a href='pendaftar.php' class='btn btn-primary'>Kembali ke Daftar Pendaftar</a></div>";
    echo "</body></html>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pendaftar: <?php echo htmlspecialchars($nim); ?> - Dashboard Pemprov</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        
        .card-detail {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-detail h4 {
            color: #009688;
            font-weight: bold;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
        }
        .info-row {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .berkas-item {
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
        }
        .berkas-item i {
            font-size: 24px;
            margin-right: 15px;
            color: #009688;
        }
        .berkas-item .status-badge {
            margin-left: auto;
        }
        .berkas-actions button {
            margin-left: 10px;
        }
        .berkas-actions textarea {
            width: 100%;
            margin-top: 10px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100"> <?php include 'includes/header.php'; ?>

    <div class="container mt-4 flex-grow-1"> <a href="pendaftar.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Pendaftar</a>
        <h2 id="pendaftar_nama_lengkap">Detail Pendaftar: Memuat...</h2>
        <p class="text-muted">Informasi lengkap dan berkas pendaftar beasiswa.</p>

        <div id="responseMessage" class="alert d-none" role="alert"></div>
        <div id="loadingMessage" class="alert alert-info text-center">Memuat detail pendaftar...</div>
        <div id="errorMessage" class="alert alert-danger text-center d-none">Gagal memuat detail pendaftar.</div>

        <div class="card-detail" id="infoPribadiCard" style="display:none;">
            <h4>Informasi Pribadi</h4>
            <div class="row">
                <div class="col-md-6 info-row"><span class="info-label">NIM:</span> <span id="detail_nim"></span></div>
                <div class="col-md-6 info-row"><span class="info-label">Nama Lengkap:</span> <span id="detail_nama_lengkap"></span></div>
                <div class="col-md-6 info-row"><span class="info-label">Tanggal Lahir:</span> <span id="detail_tanggal_lahir"></span></div>
                <div class="col-md-6 info-row"><span class="info-label">Jenis Kelamin:</span> <span id="detail_jenis_kelamin"></span></div>
                <div class="col-md-6 info-row"><span class="info-label">Nomor Telepon:</span> <span id="detail_nomor_telepon"></span></div>
                <div class="col-md-6 info-row"><span class="info-label">Email:</span> <span id="detail_email"></span></div>
                <div class="col-md-12 info-row"><span class="info-label">Alamat:</span> <span id="detail_alamat"></span></div>
            </div>
        </div>

        <div class="card-detail" id="infoAkademikCard" style="display:none;">
            <h4>Informasi Akademik</h4>
            <div class="row">
                <div class="col-md-6 info-row"><span class="info-label">Program Studi:</span> <span id="detail_program_studi"></span></div>
                <div class="col-md-6 info-row"><span class="info-label">Angkatan:</span> <span id="detail_angkatan"></span></div>
                <div class="col-md-6 info-row"><span class="info-label">IPK Terakhir:</span> <span id="detail_ipk"></span></div>
                <div class="col-md-6 info-row"><span class="info-label">Tanggal Daftar:</span> <span id="detail_tanggal_daftar"></span></div>
            </div>
        </div>

        <div class="card-detail" id="statusPendaftaranCard" style="display:none;">
            <h4>Status Pendaftaran</h4>
            <div class="row">
                <div class="col-md-12 info-row">
                    <span class="info-label">Status Global:</span>
                    <span id="detail_status_global" class="badge"></span>
                </div>
            </div>
        </div>

        <div class="card-detail" id="berkasUploadedCard" style="display:none;">
            <h4>Berkas yang Diunggah</h4>
            <div id="berkasListContainer">
                <p class="text-center text-muted" id="berkasLoadingMessage">Memuat berkas...</p>
                <div id="berkasActualList">
                    </div>
                <div id="noBerkasMessage" class="alert alert-info text-center mt-3 d-none">Belum ada berkas diunggah untuk pendaftar ini.</div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script>
        $(document).ready(function() {
            const NIM = '<?php echo htmlspecialchars($nim); ?>';
            const API_URL_MAHASISWA = '../my_api_android/mahasiswa_crud.php';
            const API_URL_BERKAS = '../my_api_android/upload_berkas.php';
            const API_URL_UPDATE_BERKAS_STATUS = '../my_api_android/update_berkas_status.php';

            // --- FUNGSI MUAT DATA MAHASISWA ---
            function fetchPendaftarDetails() {
                $('#loadingMessage').removeClass('d-none');
                $('#errorMessage').addClass('d-none');
                $('#infoPribadiCard').hide();
                $('#infoAkademikCard').hide();
                $('#statusPendaftaranCard').hide();
                $('#berkasUploadedCard').hide();

                $.ajax({
                    url: `${API_URL_MAHASISWA}?nim=${NIM}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#loadingMessage').addClass('d-none');
                        if (response.success && response.mahasiswa.length > 0) {
                            const pendaftar = response.mahasiswa[0];
                            $('#pendaftar_nama_lengkap').text(`Detail Pendaftar: ${pendaftar.nama_lengkap}`);

                            $('#detail_nim').text(pendaftar.nim);
                            $('#detail_nama_lengkap').text(pendaftar.nama_lengkap);
                            $('#detail_tanggal_lahir').text(pendaftar.tanggal_lahir);
                            $('#detail_jenis_kelamin').text(pendaftar.jenis_kelamin);
                            $('#detail_nomor_telepon').text(pendaftar.nomor_telepon);
                            $('#detail_email').text(pendaftar.email);
                            $('#detail_alamat').text(pendaftar.alamat);
                            $('#detail_program_studi').text(pendaftar.program_studi);
                            $('#detail_angkatan').text(pendaftar.angkatan);
                            $('#detail_ipk').text(pendaftar.ipk);
                            $('#detail_tanggal_daftar').text(pendaftar.tanggal_daftar);

                            let globalStatusClass = '';
                            if (pendaftar.status_pendaftaran === 'Menunggu Verifikasi Pemprov') { globalStatusClass = 'badge-warning'; }
                            else if (pendaftar.status_pendaftaran === 'Diverifikasi Pemprov') { globalStatusClass = 'badge-success'; }
                            else if (pendaftar.status_pendaftaran === 'Ditolak Pemprov') { globalStatusClass = 'badge-danger'; }
                            $('#detail_status_global').text(pendaftar.status_pendaftaran).removeClass().addClass(`badge ${globalStatusClass}`);

                            $('#infoPribadiCard').show();
                            $('#infoAkademikCard').show();
                            $('#statusPendaftaranCard').show();
                            $('#berkasUploadedCard').show();

                            fetchBerkasData(NIM); // Setelah detail pendaftar dimuat, muat berkasnya
                        } else {
                            $('#errorMessage').text('Data pendaftar tidak ditemukan atau terjadi kesalahan: ' + (response.message || '')).removeClass('d-none');
                            $('#pendaftar_nama_lengkap').text('Pendaftar Tidak Ditemukan');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#loadingMessage').addClass('d-none');
                        $('#errorMessage').text('Error jaringan saat memuat detail pendaftar.').removeClass('d-none');
                        console.error('AJAX Error fetching pendaftar details:', status, error, xhr.responseText);
                        $('#pendaftar_nama_lengkap').text('Gagal Memuat Detail Pendaftar');
                    }
                });
            }

            // --- FUNGSI MUAT DATA BERKAS ---
            function fetchBerkasData(nim) {
                $('#berkasLoadingMessage').removeClass('d-none');
                $('#berkasActualList').html('');
                $('#noBerkasMessage').addClass('d-none');

                $.ajax({
                    url: `${API_URL_BERKAS}?nim=${nim}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#berkasLoadingMessage').addClass('d-none');
                        if (response.success && response.berkas.length > 0) {
                            let berkasHtml = '';
                            response.berkas.forEach(function(berkas) {
                                let statusClass = '';
                                if (berkas.status_verifikasi === 'Menunggu Verifikasi Pemprov') { statusClass = 'badge-warning'; }
                                else if (berkas.status_verifikasi === 'Diverifikasi Pemprov') { statusClass = 'badge-success'; }
                                else if (berkas.status_verifikasi === 'Ditolak Pemprov') { statusClass = 'badge-danger'; }

                                berkasHtml += `
                                    <div class="berkas-item" data-id="${berkas.id}">
                                        <i class="fas fa-file-alt"></i>
                                        <div>
                                            <strong>${berkas.jenis_berkas}</strong><br>
                                            <small class="text-muted">Diunggah: ${berkas.tanggal_upload}</small><br>
                                            <span class="badge ${statusClass}">${berkas.status_verifikasi}</span>
                                            ${berkas.status_verifikasi === 'Ditolak Pemprov' && berkas.alasan_ditolak ? 
                                                `<small class="text-danger d-block mt-1">Alasan: ${berkas.alasan_ditolak}</small>` : ''}
                                        </div>
                                        <div class="berkas-actions ml-auto">
                                            <a href="${berkas.url_berkas}" target="_blank" class="btn btn-info btn-sm">Lihat Berkas</a>
                                            ${berkas.status_verifikasi === 'Menunggu Verifikasi Pemprov' ? `
                                                <button type="button" class="btn btn-success btn-sm btn-setujui" data-id="${berkas.id}">Setujui</button>
                                                <button type="button" class="btn btn-danger btn-sm btn-tolak" data-id="${berkas.id}">Tolak</button>
                                                <textarea class="form-control mt-2 d-none alasan-tolak-field" placeholder="Alasan penolakan"></textarea>
                                            ` : ''}
                                        </div>
                                    </div>
                                `;
                            });
                            $('#berkasActualList').html(berkasHtml);
                            attachBerkasEventListeners(); // Lampirkan event listener setelah HTML dimuat
                        } else {
                            $('#noBerkasMessage').removeClass('d-none');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error fetching berkas data:', status, error, xhr.responseText);
                        $('#berkasLoadingMessage').addClass('d-none');
                        $('#berkasActualList').html('<div class="alert alert-danger text-center">Gagal memuat berkas.</div>');
                    }
                });
            }

            // --- FUNGSI UPDATE STATUS BERKAS ---
            function updateBerkasStatus(berkasId, newStatus, alasan) {
                $.ajax({
                    url: API_URL_UPDATE_BERKAS_STATUS,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        berkas_id: berkasId,
                        status: newStatus,
                        alasan_ditolak: alasan,
                        admin_username: '<?php echo htmlspecialchars($_SESSION['pemprov_username'] ?? 'unknown_admin'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Status berkas berhasil diperbarui: ' + response.message);
                            fetchBerkasData(NIM); // Refresh daftar berkas setelah update
                            fetchPendaftarDetails(); // Juga refresh detail pendaftar (untuk status global)
                        } else {
                            alert('Gagal memperbarui status berkas: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr.responseText);
                        alert('Error jaringan saat memperbarui status berkas. Cek konsol browser.');
                    }
                });
            }

            // --- FUNGSI UNTUK MENGHUBUNGKAN EVENT LISTENER SETELAH AJAX ---
            function attachBerkasEventListeners() {
                $('.btn-tolak').off('click').on('click', function() {
                    const berkasId = $(this).data('id');
                    const $berkasItem = $(this).closest('.berkas-item');
                    const $alasanField = $berkasItem.find('.alasan-tolak-field');

                    $alasanField.toggleClass('d-none');
                    if (!$alasanField.hasClass('d-none')) {
                        $alasanField.focus();
                    }

                    $alasanField.off('blur keydown').on('blur keydown', function(e) {
                        if (e.type === 'blur' || (e.type === 'keydown' && e.key === 'Enter')) {
                            const alasan = $alasanField.val().trim();
                            if (alasan) {
                                if (confirm('Anda yakin ingin menolak berkas ini dengan alasan: ' + alasan + '?')) {
                                    updateBerkasStatus(berkasId, 'Ditolak Pemprov', alasan);
                                }
                            } else {
                                alert('Alasan penolakan tidak boleh kosong.');
                                $alasanField.focus();
                            }
                        }
                    });
                });

                $('.btn-setujui').off('click').on('click', function() {
                    const berkasId = $(this).data('id');
                    if (confirm('Anda yakin ingin menyetujui berkas ini?')) {
                        updateBerkasStatus(berkasId, 'Diverifikasi Pemprov', '');
                    }
                });
            }

            // Panggil saat halaman dimuat
            fetchPendaftarDetails();
        });
    </script>
</body>
</html>
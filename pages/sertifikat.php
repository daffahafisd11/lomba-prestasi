<?php
require_once '../config/database.php';
require_once '../functions/helpers.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$id_prestasi = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_prestasi <= 0) {
    die('ID prestasi tidak valid');
}

$prestasi = getPrestasi($id_prestasi);

if (!$prestasi || empty($prestasi['nomor_sertifikat'])) {
    die('Sertifikat tidak ditemukan');
}

$sertifikat = getSertifikat($prestasi['id_sertifikat']);

$data = [
    'id_prestasi' => $prestasi['id_prestasi'],
    'id_sertifikat' => $prestasi['id_sertifikat'],
    'nomor' => $prestasi['nomor_sertifikat'],
    'nama_pemuda' => $prestasi['nama_pemuda'],
    'nama_prestasi' => $prestasi['nama_prestasi'],
    'tingkat' => $prestasi['tingkat'],
    'penyelenggara' => $prestasi['penyelenggara'],
    'tahun' => $prestasi['tahun'],
    'tanggal_terbit' => formatTanggal(date('Y-m-d'))
];

$successMessage = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Sertifikat - Pemuda Berprestasi</title>
    <link rel="icon" type="image/png" href="../assets/img/icon-1.png" sizes="32x32">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-image: url('../assets/img/background-index.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            color: #2D3E30;
        }
        #sidebar-wrapper {
            min-height: 100vh;
            width: 250px;
            flex-shrink: 0;
            transition: all 0.3s;
            background: #6B8F71;
            border-right: 2px solid #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .sidebar-heading {
            padding: 30px 20px !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3) !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .sidebar-logo {
            width: 150px;
            height: auto;
            margin-bottom: 15px;
            overflow: hidden;
        }
        .sidebar-logo img {
            width: 100%;
            height: auto;
            display: block;
        }
        .sidebar-heading h6 {
            font-weight: 700;
            color: #fff;
            margin-bottom: 2px;
            font-size: 1.2rem;
        }
        .sidebar-heading small {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
        }
        #sidebar-wrapper .list-group-item {
            border: none;
            padding: 15px 20px;
            border-radius: 10px;
            margin: 6px 12px;
            color: rgba(255, 255, 255, 0.85);
            transition: all 0.3s;
            font-weight: 500;
        }
        #sidebar-wrapper .list-group-item.active {
            background: rgba(255, 255, 255, 0.2);
            border-left: 3px solid #FFD700;
            color: #fff;
            font-weight: 600;
        }
        #sidebar-wrapper .list-group-item.text-danger {
            color: #ff4d4d;
            font-weight: 600;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.85) !important;
            border-bottom: 2px solid #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 15px 20px !important;
        }
        .navbar h5 {
            font-weight: 700;
            color: #6B8F71;
            margin: 0;
        }
        .bg-white.rounded-4 {
            background-color: rgba(255, 255, 255, 0.85) !important;
            border: 2px solid #fff !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        .btn-primary {
            background-color: #6B8F71 !important;
            border-color: #6B8F71 !important;
            color: #fff !important;
        }
        .btn-primary:hover {
            background-color: #5A7A5F !important;
            border-color: #5A7A5F !important;
        }
        .btn-success {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }
        .btn-success:hover {
            background-color: #218838 !important;
            border-color: #1e7e34 !important;
        }
        .btn-outline-secondary:hover {
            background-color: #6c757d !important;
            color: #fff !important;
        }
        .text-primary {
            color: #6B8F71 !important;
        }

        .sertifikat-preview {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border: 8px solid #6B8F71;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 50px;
            position: relative;
            min-height: 600px;
            background-size: cover;
            background-position: center;
        }
        .sertifikat-preview::before {
            content: "";
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border: 2px solid rgba(107, 143, 113, 0.3);
            border-radius: 8px;
            pointer-events: none;
        }
        .sertifikat-preview .border-corner {
            position: absolute;
            width: 30px;
            height: 30px;
            border-color: #6B8F71;
            border-style: solid;
            border-width: 0;
        }
        .sertifikat-preview .border-corner.tl { top: 25px; left: 25px; border-top-width: 4px; border-left-width: 4px; }
        .sertifikat-preview .border-corner.tr { top: 25px; right: 25px; border-top-width: 4px; border-right-width: 4px; }
        .sertifikat-preview .border-corner.bl { bottom: 25px; left: 25px; border-bottom-width: 4px; border-left-width: 4px; }
        .sertifikat-preview .border-corner.br { bottom: 25px; right: 25px; border-bottom-width: 4px; border-right-width: 4px; }
        .sertifikat-preview .logo {
            position: absolute;
            top: 40px;
            left: 50px;
            width: 80px;
            height: auto;
        }
        .sertifikat-preview .logo img {
            width: 80px;
            height: auto;
        }
        .sertifikat-preview .nomor {
            position: absolute;
            top: 45px;
            right: 50px;
            font-size: 13px;
            color: #666;
            background: #f0f0f0;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
        .sertifikat-preview .title {
            font-size: 32px;
            font-weight: bold;
            color: #2D3E30;
            margin-top: 60px;
            letter-spacing: 4px;
            text-transform: uppercase;
            text-align: center;
        }
        .sertifikat-preview .title-decoration {
            width: 80px;
            height: 3px;
            background: #6B8F71;
            margin: 10px auto;
        }
        .sertifikat-preview .subtitle {
            font-size: 16px;
            color: #666;
            margin-top: 5px;
            text-align: center;
        }
        .sertifikat-preview .nama-pemuda {
            font-size: 48px;
            font-weight: bold;
            color: #2D3E30;
            margin: 25px 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: center;
        }
        .sertifikat-preview .prestasi-text {
            font-size: 20px;
            color: #4A6B5A;
            margin: 5px 0;
            text-align: center;
        }
        .sertifikat-preview .prestasi-text strong {
            color: #2D3E30;
        }
        .sertifikat-preview .detail-info {
            font-size: 15px;
            color: #555;
            margin: 4px 0;
            text-align: center;
        }
        .sertifikat-preview .detail-info span {
            color: #6B8F71;
            font-weight: 600;
        }
        .sertifikat-preview .footer {
            position: absolute;
            bottom: 50px;
            left: 50px;
            right: 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e0e0e0;
            padding-top: 20px;
        }
        .sertifikat-preview .footer .date {
            font-size: 13px;
            color: #888;
        }
        .sertifikat-preview .footer .stamp {
            font-family: "Brush Script MT", cursive;
            font-size: 22px;
            color: #6B8F71;
            opacity: 0.7;
        }

        .verifikasi-data {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .verifikasi-data .label {
            font-weight: 600;
            color: #6B8F71;
        }
        .verifikasi-data .row {
            padding: 4px 0;
            border-bottom: 1px solid #eee;
        }
        .verifikasi-data .row:last-child {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            #sidebar-wrapper {
                width: 0;
                overflow: hidden;
                position: fixed;
                z-index: 1050;
                height: 100vh;
            }
            #sidebar-wrapper.show {
                width: 250px;
            }
            .sertifikat-preview {
                padding: 20px;
                min-height: 400px;
            }
            .sertifikat-preview .logo {
                position: relative;
                top: 0;
                left: 0;
                margin: 0 auto 10px;
                display: block;
            }
            .sertifikat-preview .nomor {
                position: relative;
                top: 0;
                right: 0;
                display: inline-block;
                margin: 10px auto;
            }
            .sertifikat-preview .nama-pemuda {
                font-size: 28px;
            }
            .sertifikat-preview .title {
                font-size: 22px;
                margin-top: 20px;
            }
            .sertifikat-preview .footer {
                position: relative;
                bottom: 0;
                left: 0;
                right: 0;
                margin-top: 20px;
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="d-flex" id="wrapper">
        <div class="sidebar text-white" id="sidebar-wrapper">
            <div class="sidebar-heading text-center">
                <div class="sidebar-logo">
                    <img src="../assets/img/logo.png" alt="Logo">
                </div>
                <h6>Dashboard</h6>
                <small>Admin Panel</small>
            </div>
            <div class="list-group list-group-flush mt-3">
                <a href="dashboard.php" class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
                </a>
                <a href="pemuda.php" class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="bi bi-people-fill me-2"></i> Pemuda
                </a>
                <a href="prestasi.php" class="list-group-item list-group-item-action bg-transparent text-white active">
                    <i class="bi bi-trophy-fill me-2"></i> Prestasi
                </a>
                <a href="kategori.php" class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="bi bi-tags-fill me-2"></i> Kategori
                </a>
                <div class="border-top mt-3 pt-3">
                    <a href="../logout.php" class="list-group-item list-group-item-action bg-transparent text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Keluar
                    </a>
                </div>
            </div>
        </div>

        <div id="page-content-wrapper" class="w-100">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link d-lg-none" id="menu-toggle">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <h5 class="mb-0 fw-bold text-primary">Preview Sertifikat</h5>
                </div>
                <div class="ms-auto d-flex align-items-center">
                    <a href="prestasi.php" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 35px; height: 35px;">
                                <i class="bi bi-person"></i>
                            </div>
                            <span class="ms-2 d-none d-sm-inline"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">
                <?php if ($successMessage): ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                <?= $successMessage ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">
                                    <i class="bi bi-file-pdf text-success me-2"></i>
                                    Preview Sertifikat
                                </h5>
                                <span class="badge bg-success"><?= $data['nomor'] ?></span>
                            </div>

                            <!-- Sertifikat Preview dengan background template -->
                            <div class="sertifikat-preview" style="background-image: url('../assets/img/sertifikat.png');">
                                <div class="border-corner tl"></div>
                                <div class="border-corner tr"></div>
                                <div class="border-corner bl"></div>
                                <div class="border-corner br"></div>

                                <div class="logo">
                                    <img src="../assets/img/logo.png" alt="Logo">
                                </div>
                                <div class="nomor">No: <?= $data['nomor'] ?></div>

                                <!-- # SERTIFIKAT -->
                                <div class="title">Sertifikat</div>
                                
                                <!-- ## PENGHARGAAN -->
                                <div class="title-decoration"></div>
                                <div style="font-size: 26px; font-weight: 700; color: #6B8F71; letter-spacing: 6px; text-transform: uppercase; text-align: center;">Penghargaan</div>
                                
                                <!-- Diberikan kepada -->
                                <div class="subtitle">Diberikan kepada</div>

                                <!-- Nama Pemuda -->
                                <div class="nama-pemuda"><?= strtoupper($data['nama_pemuda']) ?></div>

                                <!-- Prestasi -->
                                <div style="font-size: 18px; color: #4A6B5A; text-align: center;">Atas prestasi</div>
                                <div class="prestasi-text"><strong>"<?= $data['nama_prestasi'] ?>"</strong></div>

                                <!-- Detail -->
                                <div class="detail-info">
                                    Tingkat: <span><?= $data['tingkat'] ?></span> &nbsp;|&nbsp; 
                                    Penyelenggara: <span><?= $data['penyelenggara'] ?></span>
                                </div>
                                <div class="detail-info">
                                    Tahun: <span><?= $data['tahun'] ?></span>
                                </div>

                                <div class="footer">
                                    <div class="date">Diterbitkan: <?= $data['tanggal_terbit'] ?></div>
                                    <div class="stamp">~ Pemuda Berprestasi ~</div>
                                </div>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="text-center mt-4">
                                <button type="button" class="btn btn-success btn-lg px-5" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalVerifikasiDownload">
                                    <i class="bi bi-file-pdf me-2"></i> Download Sertifikat
                                </button>
                                <a href="prestasi.php" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="bi bi-arrow-left me-2"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center text-secondary small mt-4">
                    <p class="mb-0">&copy; <?= date('Y') ?> Pemuda Berprestasi - Made by <strong>Daffa Hafisd P</strong></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Verifikasi Download -->
    <div class="modal fade" id="modalVerifikasiDownload" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-shield-check me-2 text-success"></i>
                        Verifikasi Sebelum Download
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="Download_sertifikat.php" method="POST">
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Perhatian!</strong> Harap isi data di bawah ini untuk verifikasi.
                        </div>

                        <div class="verifikasi-data">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nomor Sertifikat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="verifNomor" 
                                        placeholder="Masukkan nomor sertifikat" required>
                                <small class="text-muted">Masukkan: <strong><?= $data['nomor'] ?></strong></small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Pemuda <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="verifNama" 
                                        placeholder="Masukkan nama pemuda" required>
                                <small class="text-muted">Masukkan: <strong><?= $data['nama_pemuda'] ?></strong></small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Prestasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="verifPrestasi" 
                                        placeholder="Masukkan nama prestasi" required>
                                <small class="text-muted">Masukkan: <strong><?= $data['nama_prestasi'] ?></strong></small>
                            </div>
                        </div>

                        <input type="hidden" name="id_prestasi" value="<?= $data['id_prestasi'] ?>">
                        <input type="hidden" name="id_sertifikat" value="<?= $data['id_sertifikat'] ?>">

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="verifikasiCheck" required>
                            <label class="form-check-label" for="verifikasiCheck">
                                Saya menyatakan bahwa data yang saya isi sudah benar dan sesuai
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success" id="btnDownloadSertifikat" disabled>
                            <i class="bi bi-file-pdf me-1"></i> Verifikasi & Download
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const sidebarWrapper = document.getElementById('sidebar-wrapper');
            
            if (menuToggle) {
                menuToggle.addEventListener('click', function() {
                    sidebarWrapper.classList.toggle('show');
                });
            }

            document.addEventListener('click', function(event) {
                const isClickInside = sidebarWrapper.contains(event.target) || menuToggle.contains(event.target);
                if (!isClickInside && window.innerWidth <= 768) {
                    sidebarWrapper.classList.remove('show');
                }
            });

            const verifNomor = document.getElementById('verifNomor');
            const verifNama = document.getElementById('verifNama');
            const verifPrestasi = document.getElementById('verifPrestasi');
            const verifikasiCheck = document.getElementById('verifikasiCheck');
            const btnDownload = document.getElementById('btnDownloadSertifikat');

            const dataAsli = {
                nomor: '<?= $data['nomor'] ?>',
                nama: '<?= $data['nama_pemuda'] ?>',
                prestasi: '<?= $data['nama_prestasi'] ?>'
            };

            function validateForm() {
                const nomorValid = verifNomor.value.trim() === dataAsli.nomor;
                const namaValid = verifNama.value.trim() === dataAsli.nama;
                const prestasiValid = verifPrestasi.value.trim() === dataAsli.prestasi;
                const checkboxChecked = verifikasiCheck.checked;

                if (nomorValid && namaValid && prestasiValid && checkboxChecked) {
                    btnDownload.disabled = false;
                } else {
                    btnDownload.disabled = true;
                }
            }

            verifNomor.addEventListener('input', validateForm);
            verifNama.addEventListener('input', validateForm);
            verifPrestasi.addEventListener('input', validateForm);
            verifikasiCheck.addEventListener('change', validateForm);

            const modalVerifikasi = document.getElementById('modalVerifikasiDownload');
            if (modalVerifikasi) {
                modalVerifikasi.addEventListener('show.bs.modal', function() {
                    verifNomor.value = '';
                    verifNama.value = '';
                    verifPrestasi.value = '';
                    verifikasiCheck.checked = false;
                    btnDownload.disabled = true;
                });
            }
        });
    </script>

    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
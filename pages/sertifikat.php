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
    'id_prestasi'    => $prestasi['id_prestasi'],
    'id_sertifikat'  => $prestasi['id_sertifikat'],
    'nomor'          => $prestasi['nomor_sertifikat'],
    'nama_pemuda'    => $prestasi['nama_pemuda'],
    'nama_prestasi'  => $prestasi['nama_prestasi'],
    'tingkat'        => $prestasi['tingkat'],
    'penyelenggara'  => $prestasi['penyelenggara'],
    'tahun'          => $prestasi['tahun'],
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

        /* Sidebar */
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

        /* Navbar */
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

        /* Card */
        .bg-white.rounded-4 {
            background-color: rgba(255, 255, 255, 0.85) !important;
            border: 2px solid #fff !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* Buttons */
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

        /* SERTIFIKAT PREVIEW */
        .sertifikat-wrapper {
            width: 100%;
            overflow-x: auto;
            padding: 20px 0;
            display: flex;
            justify-content: center;
        }
        .sertifikat-preview {
            width: 297mm;
            height: 210mm;
            background-image: url('../assets/img/section-2.png');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            flex-shrink: 0;
            transform-origin: top center;
        }
        .sertifikat-nomor {
            position: absolute;
            top: 15mm;
            right: 18mm;
            font-size: 11pt;
            color: #333;
            background: rgba(255, 255, 255, 0.9);
            padding: 2mm 5mm;
            border-radius: 10mm;
            font-weight: 600;
            border: 0.5px solid rgba(107, 143, 113, 0.4);
            z-index: 10;
        }
        .sertifikat-content {
            position: absolute;
            inset: 0;
            padding: 25mm 30mm;
            text-align: center;
        }
        .sertifikat-title {
            font-size: 32pt;
            font-weight: 900;
            color: #2D3E30;
            letter-spacing: 10pt;
            text-transform: uppercase;
            margin-top: 8mm;
            line-height: 1.1;
        }
        .sertifikat-subtitle {
            font-size: 18pt;
            font-weight: 700;
            color: #6B8F71;
            letter-spacing: 6pt;
            text-transform: uppercase;
            margin-top: 1mm;
        }
        .sertifikat-decoration {
            width: 25mm;
            height: 1mm;
            background: #6B8F71;
            margin: 3mm auto 5mm auto;
        }
        .sertifikat-diberikan {
            font-size: 12pt;
            color: #777;
            letter-spacing: 2pt;
            margin-bottom: 2mm;
        }
        .sertifikat-nama {
            font-size: 34pt;
            font-weight: 900;
            color: #2D3E30;
            text-transform: uppercase;
            letter-spacing: 3pt;
            line-height: 1.15;
            margin: 2mm 0 4mm 0;
        }
        .sertifikat-atas {
            font-size: 12pt;
            color: #4A6B5A;
            margin-bottom: 1mm;
        }
        .sertifikat-prestasi {
            font-size: 18pt;
            font-weight: 700;
            color: #2D3E30;
            margin-bottom: 4mm;
            line-height: 1.2;
        }
        .sertifikat-detail {
            font-size: 11pt;
            color: #555;
            line-height: 1.6;
            margin-bottom: 0.5mm;
        }
        .sertifikat-detail span {
            color: #6B8F71;
            font-weight: 600;
        }
        .sertifikat-footer {
            position: absolute;
            bottom: 15mm;
            left: 30mm;
            right: 30mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 0.5px solid rgba(0, 0, 0, 0.1);
            padding-top: 3mm;
        }
        .sertifikat-footer .date {
            font-size: 10pt;
            color: #888;
        }
        .sertifikat-footer .stamp {
            font-family: "Brush Script MT", "Segoe Script", cursive;
            font-size: 16pt;
            color: #6B8F71;
            opacity: 0.85;
        }

        /* Responsive scale */
        @media (max-width: 1400px) {
            .sertifikat-preview { transform: scale(0.85); margin-bottom: -30mm; }
        }
        @media (max-width: 1200px) {
            .sertifikat-preview { transform: scale(0.7); margin-bottom: -60mm; }
        }
        @media (max-width: 992px) {
            .sertifikat-preview { transform: scale(0.55); margin-bottom: -95mm; }
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
            .sertifikat-preview { transform: scale(0.38); margin-bottom: -130mm; }
        }
        @media (max-width: 576px) {
            .sertifikat-preview { transform: scale(0.3); margin-bottom: -148mm; }
        }

        .verifikasi-data {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
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

        <!-- Page Content -->
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
                                <?= htmlspecialchars($successMessage) ?>
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
                                    <i class="bi bi-file-earmark-text text-success me-2"></i>
                                    Preview Sertifikat
                                </h5>
                                <span class="badge bg-success"><?= htmlspecialchars($data['nomor']) ?></span>
                            </div>

                            <!-- SERTIFIKAT PREVIEW -->
                            <div class="sertifikat-wrapper">
                                <div class="sertifikat-preview">
                                    <div class="sertifikat-nomor">No: <?= htmlspecialchars($data['nomor']) ?></div>

                                    <div class="sertifikat-content">
                                        <div class="sertifikat-title">Sertifikat</div>
                                        <div class="sertifikat-subtitle">Penghargaan</div>
                                        <div class="sertifikat-decoration"></div>

                                        <div class="sertifikat-diberikan">Diberikan kepada</div>
                                        <div class="sertifikat-nama"><?= strtoupper(htmlspecialchars($data['nama_pemuda'])) ?></div>

                                        <div class="sertifikat-atas">Atas prestasi</div>
                                        <div class="sertifikat-prestasi">"<?= htmlspecialchars($data['nama_prestasi']) ?>"</div>

                                        <div class="sertifikat-detail">
                                            Tingkat: <span><?= htmlspecialchars($data['tingkat']) ?></span> &nbsp;|&nbsp; 
                                            Penyelenggara: <span><?= htmlspecialchars($data['penyelenggara']) ?></span>
                                        </div>
                                        <div class="sertifikat-detail">
                                            Tahun: <span><?= htmlspecialchars($data['tahun']) ?></span>
                                        </div>
                                    </div>

                                    <div class="sertifikat-footer">
                                        <div class="date">Diterbitkan: <?= htmlspecialchars($data['tanggal_terbit']) ?></div>
                                        <div class="stamp">~ Pemuda Berprestasi ~</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="text-center mt-4">
                                <button type="button" class="btn btn-success btn-lg px-5" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalVerifikasiDownload">
                                    <i class="bi bi-download me-2"></i> Download Sertifikat
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
                <form action="Download_sertifikat.php" method="POST" target="_blank">
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Perhatian!</strong> Harap isi data di bawah ini untuk verifikasi.
                        </div>

                        <div class="verifikasi-data">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nomor Sertifikat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="verifNomor" name="verif_nomor"
                                        placeholder="Masukkan nomor sertifikat" required>
                                <small class="text-muted">Masukkan: <strong><?= htmlspecialchars($data['nomor']) ?></strong></small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Pemuda <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="verifNama" name="verif_nama"
                                        placeholder="Masukkan nama pemuda" required>
                                <small class="text-muted">Masukkan: <strong><?= htmlspecialchars($data['nama_pemuda']) ?></strong></small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Prestasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="verifPrestasi" name="verif_prestasi"
                                        placeholder="Masukkan nama prestasi" required>
                                <small class="text-muted">Masukkan: <strong><?= htmlspecialchars($data['nama_prestasi']) ?></strong></small>
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
                            <i class="bi bi-download me-1"></i> Verifikasi & Download
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
                if (!menuToggle || !sidebarWrapper) return;
                const isClickInside = sidebarWrapper.contains(event.target) || menuToggle.contains(event.target);
                if (!isClickInside && window.innerWidth <= 768) {
                    sidebarWrapper.classList.remove('show');
                }
            });

            // VALIDASI FORM VERIFIKASI
            const verifNomor = document.getElementById('verifNomor');
            const verifNama = document.getElementById('verifNama');
            const verifPrestasi = document.getElementById('verifPrestasi');
            const verifikasiCheck = document.getElementById('verifikasiCheck');
            const btnDownload = document.getElementById('btnDownloadSertifikat');

            const dataAsli = {
                nomor: '<?= addslashes($data['nomor']) ?>',
                nama: '<?= addslashes($data['nama_pemuda']) ?>',
                prestasi: '<?= addslashes($data['nama_prestasi']) ?>'
            };

            function validateForm() {
                const nomorValid = verifNomor.value.trim() === dataAsli.nomor;
                const namaValid = verifNama.value.trim() === dataAsli.nama;
                const prestasiValid = verifPrestasi.value.trim() === dataAsli.prestasi;
                const checkboxChecked = verifikasiCheck.checked;

                btnDownload.disabled = !(nomorValid && namaValid && prestasiValid && checkboxChecked);
            }

            if (verifNomor && verifNama && verifPrestasi && verifikasiCheck && btnDownload) {
                verifNomor.addEventListener('input', validateForm);
                verifNama.addEventListener('input', validateForm);
                verifPrestasi.addEventListener('input', validateForm);
                verifikasiCheck.addEventListener('change', validateForm);
            }

            const modalVerifikasi = document.getElementById('modalVerifikasiDownload');
            if (modalVerifikasi) {
                modalVerifikasi.addEventListener('show.bs.modal', function() {
                    if (verifNomor) verifNomor.value = '';
                    if (verifNama) verifNama.value = '';
                    if (verifPrestasi) verifPrestasi.value = '';
                    if (verifikasiCheck) verifikasiCheck.checked = false;
                    if (btnDownload) btnDownload.disabled = true;
                });
            }
        });
    </script>

    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
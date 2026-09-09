<?php
require_once '../config/database.php';
require_once '../functions/helpers.php';

// Cek login
if (!isLoggedIn()) {
    redirect('login.php');
}

// Ambil data statistik
$totalPemuda = getTotal('tb_pemuda');
$totalPrestasi = getTotal('tb_prestasi');
$totalKategori = getTotal('tb_kategori');

// Ambil data terbaru pemuda
$pemudaTerbaru = getPemuda();
$pemudaTerbaru = array_slice($pemudaTerbaru, 0, 5);

// Ambil data prestasi terbaru
$prestasiTerbaru = getPrestasi();
$prestasiTerbaru = array_slice($prestasiTerbaru, 0, 5);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pemuda Berprestasi</title>
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

        /* ===== SIDEBAR (Hijau Sage Solid + Border Putih) ===== */
        #sidebar-wrapper {<link rel="icon" type="image/png" href="../assets/img/icon-1.png" sizes="32x32">
            min-height: 100vh;
            width: 250px;
            flex-shrink: 0;
            transition: all 0.3s;
            background: #6B8F71;
            border-right: 2px solid #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        /* ===== HEADER SIDEBAR ===== */
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

        /* ===== ITEM SIDEBAR ===== */
        #sidebar-wrapper .list-group-item {
            border: none;
            padding: 15px 20px;
            border-radius: 10px;
            margin: 6px 12px;
            color: rgba(255, 255, 255, 0.85);
            transition: all 0.3s;
            font-weight: 500;
        }

        /* ===== ACTIVE ===== */
        #sidebar-wrapper .list-group-item.active {
            background: rgba(255, 255, 255, 0.2);
            border-left: 3px solid #FFD700;
            color: #fff;
            font-weight: 600;
        }

        /* ===== LOGOUT SELALU MERAH ===== */
        #sidebar-wrapper .list-group-item.text-danger {
            color: #ff4d4d;
            font-weight: 600;
        }

        /* ===== NAVBAR ===== */
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

        /* ===== CARDS (BORDER PUTIH + TRANSPARAN) ===== */
        .bg-white.rounded-4 {
            background-color: rgba(255, 255, 255, 0.85) !important;
            border: 2px solid #fff !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* ===== TABEL (TRANSPARAN) ===== */
        .table th {
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #5A6B5E;
            background-color: rgba(247, 244, 237, 0.7) !important;
            border-bottom: 2px solid #6B8F71;
        }

        .table td {
            vertical-align: middle;
            font-size: 0.9rem;
            background-color: transparent;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(107, 143, 113, 0.08) !important;
        }

        /* ===== TOMBOL ===== */
        .btn-primary {
            background-color: #6B8F71 !important;
            border-color: #6B8F71 !important;
            color: #fff !important;
        }

        .btn-primary:hover {
            background-color: #5A7A5F !important;
            border-color: #5A7A5F !important;
        }

        .btn-outline-primary {
            color: #6B8F71 !important;
            border-color: #6B8F71 !important;
        }

        .btn-outline-primary:hover {
            background-color: #6B8F71 !important;
            color: #fff !important;
        }

        .text-primary {
            color: #6B8F71 !important;
        }

        .bg-primary.bg-opacity-10 {
            background-color: rgba(107, 143, 113, 0.1) !important;
        }

        /* ===== RESPONSIVE ===== */
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
                <a href="dashboard.php" class="list-group-item list-group-item-action bg-transparent text-white active">
                    <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
                </a>
                <a href="pemuda.php" class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="bi bi-people-fill me-2"></i> Pemuda
                </a>
                <a href="prestasi.php" class="list-group-item list-group-item-action bg-transparent text-white">
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
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link d-lg-none" id="menu-toggle">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <h5 class="mb-0 fw-bold text-primary">Dashboard</h5>
                </div>
                <div class="ms-auto d-flex align-items-center">
                    <span class="text-secondary me-3 d-none d-sm-inline">
                        <i class="bi bi-calendar3 me-1"></i> <?= date('d F Y') ?>
                    </span>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown">
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

            <!-- Main Content -->
            <div class="container-fluid px-4 py-4">
                <!-- Welcome -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="bg-white p-4 rounded-4 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="fw-bold mb-1">Selamat datang, <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?>! 👋</h4>
                                    <p class="text-secondary mb-0">Kelola data pemuda berprestasi dengan mudah dan cepat.</p>
                                </div>
                                <div class="d-none d-md-block">
                                    <span class="badge bg-primary bg-opacity-10 text-primary p-2">
                                        <i class="bi bi-calendar3 me-1"></i> <?= date('d F Y') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="bg-white p-3 rounded-4 shadow-sm border-start border-4 border-primary h-100">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="bi bi-people text-primary fs-2"></i>
                                </div>
                                <div>
                                    <h6 class="text-secondary mb-1">Total Pemuda</h6>
                                    <h3 class="fw-bold mb-0"><?= $totalPemuda ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="bg-white p-3 rounded-4 shadow-sm border-start border-4 border-success h-100">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="bi bi-trophy text-success fs-2"></i>
                                </div>
                                <div>
                                    <h6 class="text-secondary mb-1">Total Prestasi</h6>
                                    <h3 class="fw-bold mb-0"><?= $totalPrestasi ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="bg-white p-3 rounded-4 shadow-sm border-start border-4 border-warning h-100">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="bi bi-tags text-warning fs-2"></i>
                                </div>
                                <div>
                                    <h6 class="text-secondary mb-1">Total Kategori</h6>
                                    <h3 class="fw-bold mb-0"><?= $totalKategori ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="bg-white p-3 rounded-4 shadow-sm border-start border-4 border-info h-100">
                            <div class="d-flex align-items-center">
                                <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                                    <i class="bi bi-database text-info fs-2"></i>
                                </div>
                                <div>
                                    <h6 class="text-secondary mb-1">Total Data</h6>
                                    <h3 class="fw-bold mb-0"><?= $totalPemuda + $totalPrestasi ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Terbaru -->
                <div class="row g-4">
                    <!-- Pemuda Terbaru -->
                    <div class="col-xl-6">
                        <div class="bg-white rounded-4 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                <h6 class="fw-bold mb-0"><i class="bi bi-people me-2 text-primary"></i>Pemuda Terbaru</h6>
                                <a href="pemuda.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                            </div>
                            <div class="p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama</th>
                                                <th>Instansi</th>
                                                <th>Prestasi</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pemudaTerbaru as $p): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <strong><?= htmlspecialchars($p['nama_pemuda']) ?></strong>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($p['asal_instansi'] ?? '-') ?></td>
                                                <td><span class="badge bg-primary rounded-pill"><?= $p['total_prestasi'] ?? 0 ?></span></td>
                                                <td>
                                                    <a href="pemuda.php?edit=<?= $p['id_pemuda'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($pemudaTerbaru)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-secondary py-3">
                                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                                    Belum ada data pemuda
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prestasi Terbaru -->
                    <div class="col-xl-6">
                        <div class="bg-white rounded-4 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                <h6 class="fw-bold mb-0"><i class="bi bi-trophy me-2 text-warning"></i>Prestasi Terbaru</h6>
                                <a href="prestasi.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                            </div>
                            <div class="p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Prestasi</th>
                                                <th>Pemuda</th>
                                                <th>Tahun</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($prestasiTerbaru as $p): ?>
                                            <tr>
                                                <td>
                                                    <i class="bi bi-award text-warning me-1"></i>
                                                    <?= htmlspecialchars($p['nama_prestasi']) ?>
                                                </td>
                                                <td><?= htmlspecialchars($p['nama_pemuda']) ?></td>
                                                <td><span class="badge bg-secondary"><?= $p['tahun'] ?></span></td>
                                                <td>
                                                    <a href="prestasi.php?edit=<?= $p['id_prestasi'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($prestasiTerbaru)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-secondary py-3">
                                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                                    Belum ada data prestasi
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center text-secondary small mt-4">
                    <p class="mb-0">&copy; <?= date('Y') ?> Pemuda Berprestasi - Made by <strong>Daffa Hafisd P</strong></p>
                </div>
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

            // Click outside to close sidebar
            document.addEventListener('click', function(event) {
                const isClickInside = sidebarWrapper.contains(event.target) || menuToggle.contains(event.target);
                if (!isClickInside && window.innerWidth <= 768) {
                    sidebarWrapper.classList.remove('show');
                }
            });
        });
    </script>

    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
require_once '../config/database.php';
require_once '../functions/helpers.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$message = '';
$messageType = '';
$dataEdit = null;

// ============================================================
// PROSES TAMBAH
// ============================================================
if (isset($_POST['tambah'])) {
    $id_pemuda = (int)$_POST['id_pemuda'];
    $id_kategori = (int)$_POST['id_kategori'];
    $nama_prestasi = sanitize($_POST['nama_prestasi']);
    $tingkat = sanitize($_POST['tingkat']);
    $penyelenggara = sanitize($_POST['penyelenggara']);
    $tahun = (int)$_POST['tahun'];

    $sql = "INSERT INTO tb_prestasi (id_pemuda, id_kategori, nama_prestasi, tingkat, penyelenggara, tahun) 
            VALUES ($id_pemuda, $id_kategori, '$nama_prestasi', '$tingkat', '$penyelenggara', $tahun)";

    if (mysqli_query($conn, $sql)) {
        $id_prestasi = mysqli_insert_id($conn);
        $pemuda = getPemuda($id_pemuda);

        $sertifikatData = [
            'id_prestasi'   => $id_prestasi,
            'nama_prestasi' => $nama_prestasi,
            'nama_pemuda'   => $pemuda['nama_pemuda'],
            'tingkat'       => $tingkat,
            'penyelenggara' => $penyelenggara,
            'tahun'         => $tahun,
            'bukti'         => ''
        ];

        $id_sertifikat = createSertifikat($sertifikatData);

        if ($id_sertifikat) {
            mysqli_query($conn, "UPDATE tb_prestasi SET id_sertifikat = $id_sertifikat WHERE id_prestasi = $id_prestasi");
        }

        $_SESSION['success_message'] = 'Data prestasi berhasil ditambahkan! Silakan download sertifikat.';
        redirect('sertifikat.php?id=' . $id_prestasi);
    } else {
        $message = 'Gagal menambahkan data: ' . mysqli_error($conn);
        $messageType = 'danger';
    }
}

// ============================================================
// PROSES EDIT
// ============================================================
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $dataEdit = getPrestasi($id);
}

// ============================================================
// PROSES UPDATE
// ============================================================
if (isset($_POST['update'])) {
    $id = (int)$_POST['id_prestasi'];
    $id_pemuda = (int)$_POST['id_pemuda'];
    $id_kategori = (int)$_POST['id_kategori'];
    $nama_prestasi = sanitize($_POST['nama_prestasi']);
    $tingkat = sanitize($_POST['tingkat']);
    $penyelenggara = sanitize($_POST['penyelenggara']);
    $tahun = (int)$_POST['tahun'];
    $id_sertifikat = isset($_POST['id_sertifikat']) ? (int)$_POST['id_sertifikat'] : 0;

    $sql = "UPDATE tb_prestasi SET 
            id_pemuda = $id_pemuda, 
            id_kategori = $id_kategori, 
            nama_prestasi = '$nama_prestasi',
            tingkat = '$tingkat', 
            penyelenggara = '$penyelenggara', 
            tahun = $tahun
            WHERE id_prestasi = $id";

    if (mysqli_query($conn, $sql)) {
        if ($id_sertifikat > 0) {
            $pemuda = getPemuda($id_pemuda);
            $sertifikatData = [
                'nama_prestasi' => $nama_prestasi,
                'nama_pemuda'   => $pemuda['nama_pemuda'],
                'tingkat'       => $tingkat,
                'penyelenggara' => $penyelenggara,
                'tahun'         => $tahun,
                'bukti'         => $_POST['bukti_lama'] ?? ''
            ];
            updateSertifikat($id_sertifikat, $sertifikatData);
        }

        $message = 'Data prestasi berhasil diupdate!';
        $messageType = 'success';
        $dataEdit = null;
    } else {
        $message = 'Gagal mengupdate data: ' . mysqli_error($conn);
        $messageType = 'danger';
    }
}

// ============================================================
// PROSES HAPUS
// ============================================================
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $data = getPrestasi($id);

    if ($data && !empty($data['id_sertifikat'])) {
        $sertifikat = getSertifikat($data['id_sertifikat']);
        if ($sertifikat && !empty($sertifikat['bukti'])) {
            $fileBukti = '../uploads/bukti/' . $sertifikat['bukti'];
            if (file_exists($fileBukti)) {
                unlink($fileBukti);
            }
        }
        deleteSertifikat($data['id_sertifikat']);
    }

    $sql = "DELETE FROM tb_prestasi WHERE id_prestasi = $id";

    if (mysqli_query($conn, $sql)) {
        $message = 'Data prestasi dan sertifikat berhasil dihapus!';
        $messageType = 'success';
    } else {
        $message = 'Gagal menghapus data: ' . mysqli_error($conn);
        $messageType = 'danger';
    }
}

// ============================================================
// AMBIL DATA
// ============================================================
$prestasiList = getPrestasi();
$pemudaList = getPemudaList();
$kategoriList = getKategoriList();
$nama_user = $_SESSION['nama'] ?? 'Admin';
$isEdit = isset($dataEdit) && $dataEdit !== null;

if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    $messageType = 'success';
    unset($_SESSION['success_message']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Prestasi - Pemuda Berprestasi</title>
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
        .btn-success {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }
        .btn-success:hover {
            background-color: #218838 !important;
            border-color: #1e7e34 !important;
        }
        .btn-outline-danger {
            border: 1px solid #dc3545 !important;
            color: #dc3545;
        }
        .btn-outline-danger:hover {
            background-color: #dc3545 !important;
            color: #fff !important;
        }
        .btn-action {
            padding: 7px 10px !important;
            font-size: 0.85rem;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 !important;
            line-height: 1;
        }
        .icon-circle {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(107, 143, 113, 0.1);
        }

        .bukti-card {
            background-color: rgba(255, 255, 255, 0.7) !important;
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
            border-radius: 8px !important;
            padding: 4px 8px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .bukti-card .bukti-thumb {
            width: 35px;
            height: 35px;
            border-radius: 6px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #dc3545;
            font-size: 1.2rem;
        }
        .bukti-card .bukti-thumb:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(107, 143, 113, 0.3);
            border-color: #6B8F71;
            background: #fff;
        }

        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        .modal-dialog-scrollable .modal-content {
            max-height: calc(100vh - 3.5rem);
        }
        .modal-content > form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
        }
        .modal-header {
            border-bottom: 2px solid #6B8F71;
            padding: 15px 20px;
        }
        .modal-body {
            padding: 20px;
            overflow-y: auto;
        }
        .modal-footer {
            border-top: 1px solid #dee2e6;
            padding: 15px 20px;
            background: #f8f9fa;
            border-radius: 0 0 16px 16px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }

        .modal-detail-bukti .modal-content {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border: 2px solid rgba(107, 143, 113, 0.3) !important;
            border-radius: 16px !important;
        }
        .modal-detail-bukti .modal-header {
            border-bottom: 2px solid #6B8F71;
            padding: 15px 20px;
            background-color: rgba(107, 143, 113, 0.05) !important;
            border-radius: 14px 14px 0 0;
        }
        .modal-detail-bukti .modal-title {
            color: #2D3E30;
            font-weight: 600;
        }
        .modal-detail-bukti .modal-body {
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 400px;
            background-color: #f8f9fa !important;
        }
        .modal-detail-bukti .modal-body iframe {
            width: 100%;
            height: 75vh;
            border: none;
            background: white;
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
            .table th, .table td {
                font-size: 0.75rem;
                padding: 6px 8px;
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
                    <h5 class="mb-0 fw-bold text-primary">Kelola Prestasi</h5>
                </div>
                <div class="ms-auto d-flex align-items-center">
                    <span class="text-secondary me-3 d-none d-sm-inline">
                        <i class="bi bi-trophy me-1"></i> <?= count($prestasiList) ?> Prestasi
                    </span>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 35px; height: 35px;">
                                <i class="bi bi-person"></i>
                            </div>
                            <span class="ms-2 d-none d-sm-inline"><?= htmlspecialchars($nama_user) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">
                <!-- Toolbar -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="bg-white p-3 rounded-4 shadow-sm">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div class="input-group" style="max-width: 300px;">
                                    <input type="text" class="form-control form-control-sm" placeholder="Cari prestasi..." id="searchInput">
                                    <button class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                                </div>
                                <select class="form-select form-select-sm" style="max-width: 150px;" id="filterKategori">
                                    <option value="">Semua Kategori</option>
                                    <?php foreach ($kategoriList as $k): ?>
                                    <option value="<?= htmlspecialchars($k['nama_katgeori']) ?>"><?= htmlspecialchars($k['nama_katgeori']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Prestasi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($message): ?>
                    <div class="row mb-3">
                        <div class="col-12">
                            <?= showAlert($message, $messageType) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tabel Prestasi -->
                <div class="row">
                    <div class="col-12">
                        <div class="bg-white rounded-4 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                                <h6 class="fw-bold mb-0"><i class="bi bi-trophy me-2 text-warning"></i>Daftar Prestasi</h6>
                                <span class="badge bg-primary rounded-pill"><?= count($prestasiList) ?> Data</span>
                            </div>
                            <div class="p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>Nama Prestasi</th>
                                                <th>Pemuda</th>
                                                <th>Kategori</th>
                                                <th>No. Sertifikat</th>
                                                <th>Tingkat</th>
                                                <th>Tahun</th>
                                                <th style="width: 140px;">Bukti</th>
                                                <th style="width: 130px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($prestasiList)): ?>
                                            <tr>
                                                <td colspan="9" class="text-center text-secondary py-4">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                    <p class="mb-0">Belum ada data prestasi</p>
                                                    <small>Klik tombol "Tambah Prestasi" untuk menambahkan data</small>
                                                </td>
                                            </tr>
                                            <?php else: ?>
                                            <?php $no = 1; foreach ($prestasiList as $p): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-circle me-2">
                                                            <i class="bi bi-award text-primary"></i>
                                                        </div>
                                                        <strong><?= htmlspecialchars($p['nama_prestasi']) ?></strong>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($p['nama_pemuda']) ?></td>
                                                <td>
                                                    <span class="badge bg-primary rounded-pill">
                                                        <?= htmlspecialchars($p['nama_katgeori'] ?? 'Tanpa Kategori') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($p['nomor_sertifikat'])): ?>
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle me-1"></i>
                                                            <?= htmlspecialchars($p['nomor_sertifikat']) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><span class="badge bg-secondary rounded-pill"><?= htmlspecialchars($p['tingkat']) ?></span></td>
                                                <td><span class="badge bg-dark rounded-pill"><?= $p['tahun'] ?></span></td>

                                                <!-- KOLOM BUKTI -->
                                                <td>
                                                    <?php 
                                                    $buktiFile = trim($p['bukti_sertifikat'] ?? '');
                                                    $buktiPath = '../uploads/bukti/' . $buktiFile;
                                                    $fileExists = !empty($buktiFile) && file_exists($buktiPath);
                                                    ?>
                                                    
                                                    <?php if ($fileExists): ?>
                                                        <div class="bukti-card">
                                                            <div class="bukti-thumb" 
                                                                 onclick="detailBukti('<?= htmlspecialchars($buktiFile, ENT_QUOTES) ?>')" 
                                                                 title="Klik untuk melihat detail">
                                                                <i class="bi bi-file-earmark-text"></i>
                                                            </div>
                                                            <a href="<?= htmlspecialchars($buktiPath) ?>" 
                                                               download="<?= htmlspecialchars($buktiFile) ?>" 
                                                               class="btn btn-sm btn-outline-primary" 
                                                               title="Download">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                        </div>
                                                    <?php elseif (!empty($buktiFile)): ?>
                                                        <span class="text-warning small" title="File tercatat di database tapi tidak ada di server">
                                                            <i class="bi bi-exclamation-triangle"></i> File hilang
                                                        </span>
                                                    <?php else: ?>
                                                        <a href="sertifikat.php?id=<?= $p['id_prestasi'] ?>" 
                                                           class="btn btn-sm btn-outline-success" 
                                                           title="Buka sertifikat & download">
                                                            <i class="bi bi-download"></i> <small>Download</small>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <div class="d-flex gap-1 justify-content-start">
                                                        <a href="sertifikat.php?id=<?= $p['id_prestasi'] ?>" 
                                                            class="btn btn-success btn-action" 
                                                            title="Lihat Sertifikat">
                                                            <i class="bi bi-file-pdf"></i>
                                                        </a>
                                                        <a href="?edit=<?= $p['id_prestasi'] ?>" 
                                                            class="btn btn-outline-primary btn-action" 
                                                            title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="?hapus=<?= $p['id_prestasi'] ?>" 
                                                            class="btn btn-outline-danger btn-action" 
                                                            title="Hapus" 
                                                            onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
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

    <!-- Modal Tambah/Edit Prestasi -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-<?= $isEdit ? 'pencil-square' : 'plus-circle' ?> me-2 text-primary"></i>
                        <?= $isEdit ? 'Edit Prestasi' : 'Tambah Prestasi' ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <form action="" method="POST">
                    <div class="modal-body">
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id_prestasi" value="<?= $dataEdit['id_prestasi'] ?>">
                            <input type="hidden" name="id_sertifikat" value="<?= $dataEdit['id_sertifikat'] ?? 0 ?>">
                            <input type="hidden" name="bukti_lama" value="<?= htmlspecialchars($dataEdit['bukti_sertifikat'] ?? '') ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Prestasi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_prestasi" class="form-control" placeholder="Masukkan nama prestasi" 
                                    value="<?= $isEdit ? htmlspecialchars($dataEdit['nama_prestasi']) : '' ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pemuda <span class="text-danger">*</span></label>
                            <select name="id_pemuda" class="form-select" required>
                                <option value="">Pilih Pemuda</option>
                                <?php foreach ($pemudaList as $p): ?>
                                <option value="<?= $p['id_pemuda'] ?>" <?= $isEdit && $dataEdit['id_pemuda'] == $p['id_pemuda'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nama_pemuda']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="id_kategori" class="form-select">
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($kategoriList as $k): ?>
                                <option value="<?= $k['id_kategori'] ?>" <?= $isEdit && $dataEdit['id_kategori'] == $k['id_kategori'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nama_katgeori']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tingkat</label>
                                    <input type="text" name="tingkat" class="form-control" placeholder="Nasional/Internasional/Lokal" 
                                            value="<?= $isEdit ? htmlspecialchars($dataEdit['tingkat']) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                                    <input type="number" name="tahun" class="form-control" placeholder="2026" 
                                            value="<?= $isEdit ? $dataEdit['tahun'] : date('Y') ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Penyelenggara</label>
                            <input type="text" name="penyelenggara" class="form-control" placeholder="Nama penyelenggara" 
                                    value="<?= $isEdit ? htmlspecialchars($dataEdit['penyelenggara']) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </button>
                        <button type="submit" name="<?= $isEdit ? 'update' : 'tambah' ?>" class="btn btn-primary">
                            <i class="bi bi-<?= $isEdit ? 'check-lg' : 'plus-lg' ?> me-1"></i>
                            <?= $isEdit ? 'Update' : 'Simpan & Lihat Sertifikat' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail Bukti -->
    <div class="modal fade modal-detail-bukti" id="modalDetailBukti" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-file-earmark-text me-2 text-success"></i> Detail Bukti Sertifikat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailBuktiBody">
                    <div class="text-center text-secondary py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat file...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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

            document.addEventListener('click', function(event) {
                const isClickInside = sidebarWrapper.contains(event.target) || menuToggle.contains(event.target);
                if (!isClickInside && window.innerWidth <= 768) {
                    sidebarWrapper.classList.remove('show');
                }
            });

            // Search
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const searchText = this.value.toLowerCase();
                    const rows = document.querySelectorAll('table tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchText) ? '' : 'none';
                    });
                });
            }

            // Filter Kategori
            const filterKategori = document.getElementById('filterKategori');
            if (filterKategori) {
                filterKategori.addEventListener('change', function() {
                    const filterText = this.value.toLowerCase();
                    const rows = document.querySelectorAll('table tbody tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = (filterText === '' || text.includes(filterText)) ? '' : 'none';
                    });
                });
            }

            <?php if ($isEdit): ?>
                var modal = new bootstrap.Modal(document.getElementById('modalTambah'));
                modal.show();
            <?php endif; ?>
        });

        // ===== FUNGSI DETAIL BUKTI =====
        function detailBukti(filename) {
            const modalEl = document.getElementById('modalDetailBukti');
            const modal = new bootstrap.Modal(modalEl);
            const body = document.getElementById('detailBuktiBody');
            
            const filePath = '../uploads/bukti/' + filename;
            const lower = filename.toLowerCase();
            
            body.innerHTML = `
                <div class="text-center text-secondary py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat file...</p>
                </div>
            `;
            
            modal.show();
            
            setTimeout(function() {
                if (lower.endsWith('.html') || lower.endsWith('.htm') || lower.endsWith('.pdf')) {
                    body.innerHTML = `
                        <iframe src="${filePath}" style="width:100%; height:75vh; border:none; background: white;"></iframe>
                    `;
                } else if (/\.(jpg|jpeg|png|gif|webp)$/i.test(lower)) {
                    body.innerHTML = `
                        <div style="padding: 20px; text-align:center;">
                            <img src="${filePath}" style="max-width: 100%; max-height: 70vh; border-radius: 8px;">
                        </div>
                    `;
                } else {
                    body.innerHTML = `
                        <div class="text-center text-secondary py-5">
                            <i class="bi bi-file-earmark-x fs-1 d-block mb-3"></i>
                            <p>Preview tidak tersedia untuk tipe file ini.</p>
                            <a href="${filePath}" download class="btn btn-primary">
                                <i class="bi bi-download me-1"></i> Download File
                            </a>
                        </div>
                    `;
                }
            }, 300);
        }
    </script>

    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
require_once '../config/database.php';
require_once '../functions/helpers.php';

// Cek login
if (!isLoggedIn()) {
    redirect('login.php');
}

$message = '';
$messageType = '';

// Proses Tambah
if (isset($_POST['tambah'])) {
    $nama = sanitize($_POST['nama_katgeori']);
    $deskripsi = sanitize($_POST['deskripsi']);
    
    $sql = "INSERT INTO tb_kategori (nama_katgeori, deskripsi) VALUES ('$nama', '$deskripsi')";
    
    if (mysqli_query($conn, $sql)) {
        $message = 'Data kategori berhasil ditambahkan!';
        $messageType = 'success';
    } else {
        $message = 'Gagal menambahkan data: ' . mysqli_error($conn);
        $messageType = 'danger';
    }
}

// Proses Edit
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $dataEdit = getKategori($id);
}

if (isset($_POST['update'])) {
    $id = (int)$_POST['id_kategori'];
    $nama = sanitize($_POST['nama_katgeori']);
    $deskripsi = sanitize($_POST['deskripsi']);
    
    $sql = "UPDATE tb_kategori SET nama_katgeori = '$nama', deskripsi = '$deskripsi' WHERE id_kategori = $id";
    
    if (mysqli_query($conn, $sql)) {
        $message = 'Data kategori berhasil diupdate!';
        $messageType = 'success';
        unset($dataEdit);
    } else {
        $message = 'Gagal mengupdate data: ' . mysqli_error($conn);
        $messageType = 'danger';
    }
}

// Proses Hapus
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $sql = "DELETE FROM tb_kategori WHERE id_kategori = $id";
    
    if (mysqli_query($conn, $sql)) {
        $message = 'Data kategori berhasil dihapus!';
        $messageType = 'success';
    } else {
        $message = 'Gagal menghapus data: ' . mysqli_error($conn);
        $messageType = 'danger';
    }
}

// Ambil data
$kategoriList = getKategori();

// Warna untuk badge
$colors = ['primary', 'success', 'danger', 'info', 'warning', 'secondary', 'dark', 'purple'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Pemuda Berprestasi</title>
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
        #sidebar-wrapper {
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

        /* ===== NAVBAR (Transparan + Border Putih) ===== */
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

        /* ===== CARDS (Border Putih + Transparan) ===== */
        .bg-white.rounded-4 {
            background-color: rgba(255, 255, 255, 0.85) !important;
            border: 2px solid #fff !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* ===== TABEL (Semi-Transparan) ===== */
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
                <a href="dashboard.php" class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
                </a>
                <a href="pemuda.php" class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="bi bi-people-fill me-2"></i> Pemuda
                </a>
                <a href="prestasi.php" class="list-group-item list-group-item-action bg-transparent text-white">
                    <i class="bi bi-trophy-fill me-2"></i> Prestasi
                </a>
                <a href="kategori.php" class="list-group-item list-group-item-action bg-transparent text-white active">
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
                    <h5 class="mb-0 fw-bold text-primary">Kelola Kategori</h5>
                </div>
                <div class="ms-auto d-flex align-items-center">
                    <span class="text-secondary me-3 d-none d-sm-inline">
                        <i class="bi bi-tags me-1"></i> <?= count($kategoriList) ?> Kategori
                    </span>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 35px; height: 35px;">
                                <i class="bi bi-person"></i>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid px-4 py-4">
                <!-- Toolbar -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="bg-white p-3 rounded-4 shadow-sm">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div class="input-group" style="max-width: 300px;">
                                    <input type="text" class="form-control form-control-sm" placeholder="Cari kategori..." id="searchInput">
                                    <button class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                                </div>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
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

                <!-- Tabel Kategori -->
                <div class="row">
                    <div class="col-12">
                        <div class="bg-white rounded-4 shadow-sm">
                            <div class="p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0"><i class="bi bi-tags me-2 text-primary"></i>Daftar Kategori</h6>
                                    <span class="badge bg-primary rounded-pill"><?= count($kategoriList) ?> Data</span>
                                </div>
                            </div>
                            <div class="p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50px;">#</th>
                                                <th>Nama Kategori</th>
                                                <th>Deskripsi</th>
                                                <th>Prestasi</th>
                                                <th style="width: 120px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($kategoriList)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-secondary py-4">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                    <p class="mb-0">Belum ada data kategori</p>
                                                    <small>Klik tombol "Tambah Kategori" untuk menambahkan data</small>
                                                </td>
                                            </tr>
                                            <?php else: ?>
                                            <?php $no = 1; foreach ($kategoriList as $k): 
                                                $color = $colors[($no-1) % count($colors)];
                                            ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-<?= $color ?> bg-opacity-10 rounded-2 p-2 me-2">
                                                            <i class="bi bi-tag-fill text-<?= $color ?>"></i>
                                                        </div>
                                                        <span class="badge bg-<?= $color ?>"><?= htmlspecialchars($k['nama_katgeori']) ?></span>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($k['deskripsi'] ?? '-') ?></td>
                                                <td><span class="badge bg-primary rounded-pill"><?= $k['total_prestasi'] ?? 0 ?></span></td>
                                                <td>
                                                    <a href="?edit=<?= $k['id_kategori'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="?hapus=<?= $k['id_kategori'] ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
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

                <!-- Footer -->
                <div class="text-center text-secondary small mt-4">
                    <p class="mb-0">&copy; <?= date('Y') ?> Pemuda Berprestasi - Made by <strong>Daffa Hafisd P</strong></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-<?= isset($dataEdit) ? 'pencil' : 'plus-circle' ?> me-2 text-primary"></i>
                        <?= isset($dataEdit) ? 'Edit Kategori' : 'Tambah Kategori' ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <?php if (isset($dataEdit)): ?>
                            <input type="hidden" name="id_kategori" value="<?= $dataEdit['id_kategori'] ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_katgeori" class="form-control" placeholder="Masukkan nama kategori" 
                                    value="<?= isset($dataEdit) ? htmlspecialchars($dataEdit['nama_katgeori']) : '' ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="2" placeholder="Deskripsi kategori"><?= isset($dataEdit) ? htmlspecialchars($dataEdit['deskripsi']) : '' ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="<?= isset($dataEdit) ? 'update' : 'tambah' ?>" class="btn btn-primary">
                            <i class="bi bi-<?= isset($dataEdit) ? 'check-lg' : 'plus-lg' ?> me-1"></i>
                            <?= isset($dataEdit) ? 'Update' : 'Simpan' ?>
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

            // Search functionality
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
        });
    </script>

    <?php if (isset($dataEdit)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = new bootstrap.Modal(document.getElementById('modalTambah'));
            modal.show();
        });
    </script>
    <?php endif; ?>

    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
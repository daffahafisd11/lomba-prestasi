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
    $nama = sanitize($_POST['nama_pemuda']);
    $tanggal = sanitize($_POST['tanggal_lahir']);
    $jk = sanitize($_POST['jenis_kelamin']);
    $instansi = sanitize($_POST['asal_instansi']);
    $kecamatan = sanitize($_POST['kecamatan']);
    $alamat = sanitize($_POST['alamat']);
    $deskripsi = sanitize($_POST['deskripsi']);
    
    // Upload Foto
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "../uploads/foto_pemuda/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $foto;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);
    }
    
    $sql = "INSERT INTO tb_pemuda (nama_pemuda, tanggal_lahir, jenis_kelamin, asal_instansi, kecamatan, alamat, deskripsi, foto) 
            VALUES ('$nama', '$tanggal', '$jk', '$instansi', '$kecamatan', '$alamat', '$deskripsi', '$foto')";
    
    if (mysqli_query($conn, $sql)) {
        $message = 'Data pemuda berhasil ditambahkan!';
        $messageType = 'success';
    } else {
        $message = 'Gagal menambahkan data: ' . mysqli_error($conn);
        $messageType = 'danger';
    }
}

// Proses Edit
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $dataEdit = getPemuda($id);

    if (!$dataEdit) {
        $message = 'Data pemuda tidak ditemukan.';
        $messageType = 'danger';
        unset($dataEdit);
    }
}

if (isset($_POST['update'])) {
    $id = (int)$_POST['id_pemuda'];
    $nama = sanitize($_POST['nama_pemuda']);
    $tanggal = sanitize($_POST['tanggal_lahir']);
    $jk = sanitize($_POST['jenis_kelamin']);
    $instansi = sanitize($_POST['asal_instansi']);
    $kecamatan = sanitize($_POST['kecamatan']);
    $alamat = sanitize($_POST['alamat']);
    $deskripsi = sanitize($_POST['deskripsi']);
    
    // Upload Foto
    $foto = $_POST['foto_lama'] ?? '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "../uploads/foto_pemuda/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        // Hapus foto lama jika ada
        if (!empty($foto) && file_exists($target_dir . $foto)) {
            unlink($target_dir . $foto);
        }
        $file_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $foto;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);
    }
    
    $sql = "UPDATE tb_pemuda SET 
            nama_pemuda = '$nama', 
            tanggal_lahir = '$tanggal', 
            jenis_kelamin = '$jk', 
            asal_instansi = '$instansi', 
            kecamatan = '$kecamatan', 
            alamat = '$alamat', 
            deskripsi = '$deskripsi',
            foto = '$foto'
            WHERE id_pemuda = $id";
    
    if (mysqli_query($conn, $sql)) {
        $message = 'Data pemuda berhasil diupdate!';
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
    
    // Hapus foto jika ada
    $data = getPemuda($id);
    if ($data && !empty($data['foto']) && file_exists('../uploads/foto_pemuda/' . $data['foto'])) {
        unlink('../uploads/foto_pemuda/' . $data['foto']);
    }
    
    $sql = "DELETE FROM tb_pemuda WHERE id_pemuda = $id";
    
    if (mysqli_query($conn, $sql)) {
        $message = 'Data pemuda berhasil dihapus!';
        $messageType = 'success';
    } else {
        $message = 'Gagal menghapus data: ' . mysqli_error($conn);
        $messageType = 'danger';
    }
}

// Ambil data pemuda
$pemudaList = getPemuda();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pemuda - Pemuda Berprestasi</title>
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

        /* ===== SIDEBAR ===== */
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

        /* ===== CARDS ===== */
        .bg-white.rounded-4 {
            background-color: rgba(255, 255, 255, 0.85) !important;
            border: 2px solid #fff !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* ===== TABEL ===== */
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

        /* ===== FOTO THUMBNAIL ===== */
        .foto-thumb {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #6B8F71;
            background: #f0f0f0;
        }

        .foto-thumb-placeholder {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e9ecef;
            color: #6c757d;
            font-size: 1.2rem;
            border: 2px dashed #6B8F71;
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

        .modal-dialog-scrollable .modal-content {
            max-height: calc(100vh - 3.5rem);
        }

        .modal-content > form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
        }

        .modal-body {
            overflow-y: auto;
        }

        .modal-footer {
            flex-shrink: 0;
        }

        .bg-primary.bg-opacity-10 {
            background-color: rgba(107, 143, 113, 0.1) !important;
        }

        /* ===== PREVIEW FOTO ===== */
        .foto-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #6B8F71;
            display: block;
            margin: 0 auto 10px;
            background: #f8f9fa;
        }

        .foto-preview-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e9ecef;
            color: #6c757d;
            font-size: 3rem;
            border: 3px dashed #6B8F71;
            margin: 0 auto 10px;
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
                <a href="pemuda.php" class="list-group-item list-group-item-action bg-transparent text-white active">
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
                    <h5 class="mb-0 fw-bold text-primary">Kelola Pemuda</h5>
                </div>
                <div class="ms-auto d-flex align-items-center">
                    <span class="text-secondary me-3 d-none d-sm-inline">
                        <i class="bi bi-people me-1"></i> <?= count($pemudaList) ?> Pemuda
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
                                    <input type="text" class="form-control form-control-sm" placeholder="Cari pemuda..." id="searchInput">
                                    <button class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                    <i class="bi bi-plus-lg me-1"></i> Tambah Pemuda
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

                <!-- Tabel Pemuda -->
                <div class="row">
                    <div class="col-12">
                        <div class="bg-white rounded-4 shadow-sm">
                            <div class="p-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0"><i class="bi bi-people me-2 text-primary"></i>Daftar Pemuda</h6>
                                    <span class="badge bg-primary rounded-pill"><?= count($pemudaList) ?> Data</span>
                                </div>
                            </div>
                            <div class="p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 60px;">Foto</th>
                                                <th>Nama</th>
                                                <th>Instansi</th>
                                                <th>Kecamatan</th>
                                                <th>Prestasi</th>
                                                <th style="width: 120px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($pemudaList)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-secondary py-4">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                    <p class="mb-0">Belum ada data pemuda</p>
                                                    <small>Klik tombol "Tambah Pemuda" untuk menambahkan data</small>
                                                </td>
                                            </tr>
                                            <?php else: ?>
                                            <?php foreach ($pemudaList as $p): ?>
                                            <tr>
                                                <td>
                                                    <?php if (!empty($p['foto']) && file_exists('../uploads/foto_pemuda/' . $p['foto'])): ?>
                                                        <img src="../uploads/foto_pemuda/<?= htmlspecialchars($p['foto']) ?>" alt="Foto" class="foto-thumb">
                                                    <?php else: ?>
                                                        <div class="foto-thumb-placeholder">
                                                            <i class="bi bi-person"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <strong><?= htmlspecialchars($p['nama_pemuda']) ?></strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($p['asal_instansi'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($p['kecamatan'] ?? '-') ?></td>
                                                <td><span class="badge bg-primary rounded-pill"><?= $p['total_prestasi'] ?? 0 ?></span></td>
                                                <td>
                                                    <a href="?edit=<?= $p['id_pemuda'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="?hapus=<?= $p['id_pemuda'] ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
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
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-<?= isset($dataEdit) ? 'pencil' : 'plus-circle' ?> me-2 text-primary"></i>
                        <?= isset($dataEdit) ? 'Edit Pemuda' : 'Tambah Pemuda' ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <?php if (isset($dataEdit)): ?>
                            <input type="hidden" name="id_pemuda" value="<?= $dataEdit['id_pemuda'] ?>">
                            <input type="hidden" name="foto_lama" value="<?= $dataEdit['foto'] ?? '' ?>">
                        <?php endif; ?>
                        
                        <!-- Foto -->
                        <div class="mb-3 text-center">
                            <label class="form-label fw-semibold">Foto Pemuda</label>
                            <?php if (isset($dataEdit) && !empty($dataEdit['foto']) && file_exists('../uploads/foto_pemuda/' . $dataEdit['foto'])): ?>
                                <img src="../uploads/foto_pemuda/<?= htmlspecialchars($dataEdit['foto']) ?>" alt="Foto" class="foto-preview" id="fotoPreview">
                            <?php else: ?>
                                <div class="foto-preview-placeholder" id="fotoPreviewPlaceholder">
                                    <i class="bi bi-person"></i>
                                </div>
                                <img src="" alt="Preview" class="foto-preview" id="fotoPreview" style="display: none;">
                            <?php endif; ?>
                            <input type="file" name="foto" class="form-control" accept="image/*" id="fotoInput" onchange="previewFoto(this)">
                            <small class="text-muted">Format: JPG, PNG, GIF (Max: 2MB)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pemuda" class="form-control" placeholder="Masukkan nama pemuda" 
                                    value="<?= isset($dataEdit) ? htmlspecialchars($dataEdit['nama_pemuda']) : '' ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="form-control" 
                                            value="<?= isset($dataEdit) ? $dataEdit['tanggal_lahir'] : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-select">
                                        <option value="Laki-laki" <?= isset($dataEdit) && $dataEdit['jenis_kelamin'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="Perempuan" <?= isset($dataEdit) && $dataEdit['jenis_kelamin'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Asal Instansi</label>
                            <input type="text" name="asal_instansi" class="form-control" placeholder="Masukkan instansi/sekolah" 
                                    value="<?= isset($dataEdit) ? htmlspecialchars($dataEdit['asal_instansi']) : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kecamatan</label>
                            <input type="text" name="kecamatan" class="form-control" placeholder="Masukkan kecamatan" 
                                    value="<?= isset($dataEdit) ? htmlspecialchars($dataEdit['kecamatan']) : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2" placeholder="Masukkan alamat lengkap"><?= isset($dataEdit) ? htmlspecialchars($dataEdit['alamat']) : '' ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="2" placeholder="Deskripsi tentang pemuda"><?= isset($dataEdit) ? htmlspecialchars($dataEdit['deskripsi']) : '' ?></textarea>
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

        // Preview foto sebelum upload
        function previewFoto(input) {
            const preview = document.getElementById('fotoPreview');
            const placeholder = document.getElementById('fotoPreviewPlaceholder');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                    preview.style.display = 'block';
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                if (placeholder) {
                    placeholder.style.display = 'flex';
                }
                preview.style.display = 'none';
                preview.src = '';
            }
        }
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
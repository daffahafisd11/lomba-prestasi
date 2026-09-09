<?php
// functions/helpers.php

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

function showAlert($message, $type = 'success') {
    $types = [
        'success' => 'alert-success',
        'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info'
    ];
    $class = $types[$type] ?? 'alert-info';
    return "<div class='alert $class alert-dismissible fade show' role='alert'>
                $message
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
}

function getPemuda($id = null) {
    global $conn;
    if ($id) {
        $sql = "SELECT * FROM tb_pemuda WHERE id_pemuda = $id";
        $result = mysqli_query($conn, $sql);
        return mysqli_fetch_assoc($result);
    } else {
        $sql = "SELECT p.*, 
                (SELECT COUNT(*) FROM tb_prestasi WHERE id_pemuda = p.id_pemuda) as total_prestasi 
                FROM tb_pemuda p 
                ORDER BY p.id_pemuda DESC";
        $result = mysqli_query($conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

function getPrestasi($id = null) {
    global $conn;
    if ($id) {
        $sql = "SELECT pr.*, p.nama_pemuda, k.nama_katgeori, s.nomor_sertifikat, s.id_sertifikat, s.bukti as bukti_sertifikat
                FROM tb_prestasi pr 
                JOIN tb_pemuda p ON pr.id_pemuda = p.id_pemuda 
                LEFT JOIN tb_kategori k ON pr.id_kategori = k.id_kategori 
                LEFT JOIN tb_sertifikat s ON pr.id_sertifikat = s.id_sertifikat
                WHERE pr.id_prestasi = $id";
        $result = mysqli_query($conn, $sql);
        return mysqli_fetch_assoc($result);
    } else {
        $sql = "SELECT pr.*, p.nama_pemuda, k.nama_katgeori, s.nomor_sertifikat, s.id_sertifikat, s.bukti as bukti_sertifikat
                FROM tb_prestasi pr 
                JOIN tb_pemuda p ON pr.id_pemuda = p.id_pemuda 
                LEFT JOIN tb_kategori k ON pr.id_kategori = k.id_kategori 
                LEFT JOIN tb_sertifikat s ON pr.id_sertifikat = s.id_sertifikat
                ORDER BY pr.id_prestasi DESC";
        $result = mysqli_query($conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

function getKategori($id = null) {
    global $conn;
    if ($id) {
        $sql = "SELECT * FROM tb_kategori WHERE id_kategori = $id";
        $result = mysqli_query($conn, $sql);
        return mysqli_fetch_assoc($result);
    } else {
        $sql = "SELECT k.*, 
                (SELECT COUNT(*) FROM tb_prestasi WHERE id_kategori = k.id_kategori) as total_prestasi 
                FROM tb_kategori k 
                ORDER BY k.id_kategori DESC";
        $result = mysqli_query($conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

function getPemudaList() {
    global $conn;
    $sql = "SELECT id_pemuda, nama_pemuda FROM tb_pemuda ORDER BY nama_pemuda ASC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getKategoriList() {
    global $conn;
    $sql = "SELECT id_kategori, nama_katgeori FROM tb_kategori ORDER BY nama_katgeori ASC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getTotal($table) {
    global $conn;
    $sql = "SELECT COUNT(*) as total FROM $table";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

// =============================================
// FUNGSI SERTIFIKAT
// =============================================

function generateNomorSertifikat($id_prestasi, $tahun) {
    return 'SRT-' . $tahun . '-' . str_pad($id_prestasi, 4, '0', STR_PAD_LEFT);
}

function createSertifikat($data) {
    global $conn;
    
    $nomor_sertifikat = generateNomorSertifikat($data['id_prestasi'], $data['tahun']);
    $tanggal_terbit = date('Y-m-d');
    $bukti = $data['bukti'] ?? '';
    
    $sql = "INSERT INTO tb_sertifikat (
        nomor_sertifikat, 
        nama_prestasi, 
        nama_pemuda, 
        tanggal_terbit, 
        tingkat, 
        penyelenggara, 
        tahun,
        bukti
    ) VALUES (
        '$nomor_sertifikat',
        '{$data['nama_prestasi']}',
        '{$data['nama_pemuda']}',
        '$tanggal_terbit',
        '{$data['tingkat']}',
        '{$data['penyelenggara']}',
        {$data['tahun']},
        '$bukti'
    )";
    
    if (mysqli_query($conn, $sql)) {
        return mysqli_insert_id($conn);
    }
    return false;
}

function getSertifikat($id_sertifikat) {
    global $conn;
    if ($id_sertifikat <= 0) return null;
    $sql = "SELECT * FROM tb_sertifikat WHERE id_sertifikat = $id_sertifikat";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

function updateSertifikat($id_sertifikat, $data) {
    global $conn;
    $bukti = $data['bukti'] ?? '';
    
    $sql = "UPDATE tb_sertifikat SET 
            nama_prestasi = '{$data['nama_prestasi']}',
            nama_pemuda = '{$data['nama_pemuda']}',
            tingkat = '{$data['tingkat']}',
            penyelenggara = '{$data['penyelenggara']}',
            tahun = {$data['tahun']},
            bukti = '$bukti'
            WHERE id_sertifikat = $id_sertifikat";
    return mysqli_query($conn, $sql);
}

function deleteSertifikat($id_sertifikat) {
    global $conn;
    $sql = "DELETE FROM tb_sertifikat WHERE id_sertifikat = $id_sertifikat";
    return mysqli_query($conn, $sql);
}

function formatTanggal($date) {
    $bulan = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    ];
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = date('F', $timestamp);
    $year = date('Y', $timestamp);
    return $day . ' ' . $bulan[$month] . ' ' . $year;
}
?>
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
        'danger'  => 'alert-danger',
        'warning' => 'alert-warning',
        'info'    => 'alert-info'
    ];
    $class = $types[$type] ?? 'alert-info';
    return "<div class='alert $class alert-dismissible fade show' role='alert'>
                $message
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
}

// =============================================
// PEMUDA
// =============================================
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

// =============================================
// PRESTASI
// =============================================
function getPrestasi($id = null) {
    global $conn;
    if ($id) {
        $sql = "SELECT pr.*, p.nama_pemuda, k.nama_katgeori, 
                       s.nomor_sertifikat, s.id_sertifikat, 
                       s.bukti as bukti_sertifikat
                FROM tb_prestasi pr 
                JOIN tb_pemuda p ON pr.id_pemuda = p.id_pemuda 
                LEFT JOIN tb_kategori k ON pr.id_kategori = k.id_kategori 
                LEFT JOIN tb_sertifikat s ON pr.id_sertifikat = s.id_sertifikat
                WHERE pr.id_prestasi = $id";
        $result = mysqli_query($conn, $sql);
        return mysqli_fetch_assoc($result);
    } else {
        $sql = "SELECT pr.*, p.nama_pemuda, k.nama_katgeori, 
                       s.nomor_sertifikat, s.id_sertifikat, 
                       s.bukti as bukti_sertifikat
                FROM tb_prestasi pr 
                JOIN tb_pemuda p ON pr.id_pemuda = p.id_pemuda 
                LEFT JOIN tb_kategori k ON pr.id_kategori = k.id_kategori 
                LEFT JOIN tb_sertifikat s ON pr.id_sertifikat = s.id_sertifikat
                ORDER BY pr.id_prestasi DESC";
        $result = mysqli_query($conn, $sql);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

// =============================================
// KATEGORI
// =============================================
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
// SERTIFIKAT
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
        'January'   => 'Januari',
        'February'  => 'Februari',
        'March'     => 'Maret',
        'April'     => 'April',
        'May'       => 'Mei',
        'June'      => 'Juni',
        'July'      => 'Juli',
        'August'    => 'Agustus',
        'September' => 'September',
        'October'   => 'Oktober',
        'November'  => 'November',
        'December'  => 'Desember'
    ];
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = date('F', $timestamp);
    $year = date('Y', $timestamp);
    return $day . ' ' . $bulan[$month] . ' ' . $year;
}

// =============================================
// GENERATE BUKTI SERTIFIKAT (HTML FILE)
// =============================================
function saveBuktiSertifikat($id_sertifikat, $data) {
    $dir = __DIR__ . '/../uploads/bukti/';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $namaFile = 'sertifikat-' . $data['nomor'] . '.pdf';
    $filePath = $dir . $namaFile;
    
    $backgroundPath = __DIR__ . '/../assets/img/section-2.png';
    $backgroundData = file_exists($backgroundPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($backgroundPath))
        : '';
    $bgUrl = $backgroundData;
    $logoUrl = '../../assets/img/logo.png';
    
    $html = '<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Sertifikat ' . htmlspecialchars($data['nomor']) . '</title>
<link rel="icon" type="image/png" href="' . $logoUrl . '" sizes="32x32">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: "Segoe UI", Arial, sans-serif;
        background: #e9ecef;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
    }
    
    /* ===== SERTIFIKAT ===== */
    .sertifikat-page {
        width: 297mm;
        height: 210mm;
        background-image: url("' . $bgUrl . '");
        background-size: 100% 100%;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
        box-shadow: 0 10px 40px rgba(0,0,0,0.25);
        overflow: hidden;
        flex-shrink: 0;
    }
    .nomor {
        position: absolute;
        top: 15mm; right: 18mm;
        font-size: 11pt; color: #333;
        background: rgba(255,255,255,0.9);
        padding: 2mm 5mm;
        border-radius: 10mm;
        font-weight: 600;
        border: 0.5px solid rgba(107,143,113,0.4);
    }
    .content {
        position: absolute;
        inset: 0;
        padding: 25mm 30mm;
        text-align: center;
    }
    .title {
        font-size: 32pt; font-weight: 900;
        color: #2D3E30; letter-spacing: 10pt;
        text-transform: uppercase; margin-top: 8mm;
    }
    .subtitle {
        font-size: 18pt; font-weight: 700;
        color: #6B8F71; letter-spacing: 6pt;
        text-transform: uppercase; margin-top: 1mm;
    }
    .decoration {
        width: 25mm; height: 1mm;
        background: #6B8F71;
        margin: 3mm auto 5mm;
    }
    .diberikan { font-size: 12pt; color: #777; letter-spacing: 2pt; margin-bottom: 2mm; }
    .nama {
        font-size: 34pt; font-weight: 900;
        color: #2D3E30; text-transform: uppercase;
        letter-spacing: 3pt; margin: 2mm 0 4mm 0;
        line-height: 1.15;
    }
    .atas { font-size: 12pt; color: #4A6B5A; margin-bottom: 1mm; }
    .prestasi {
        font-size: 18pt; font-weight: 700;
        color: #2D3E30; margin-bottom: 4mm;
        line-height: 1.2;
    }
    .detail { font-size: 11pt; color: #555; line-height: 1.6; margin-bottom: 0.5mm; }
    .detail span { color: #6B8F71; font-weight: 600; }
    .footer {
        position: absolute;
        bottom: 15mm; left: 30mm; right: 30mm;
        display: flex; justify-content: space-between;
        align-items: center;
        border-top: 0.5px solid rgba(0,0,0,0.1);
        padding-top: 3mm;
    }
    .footer .date { font-size: 10pt; color: #888; }
    .footer .stamp {
        font-family: "Brush Script MT", "Segoe Script", cursive;
        font-size: 16pt; color: #6B8F71; opacity: 0.85;
    }
    
    /* ===== TOMBOL AKSI ===== */
    .btn-group {
        margin-top: 30px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .btn-group .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
        font-family: inherit;
    }
    .btn-print {
        background: #28a745;
        color: white;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    .btn-print:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        color: white;
    }
    .btn-back {
        background: #6c757d;
        color: white;
        box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    }
    .btn-back:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
        color: white;
    }
    .info-text {
        margin-top: 15px;
        color: #555;
        font-size: 14px;
        text-align: center;
        max-width: 600px;
        line-height: 1.6;
    }
    
    /* ===== PRINT STYLES ===== */
    @page { size: A4 landscape; margin: 0; }
    @media print {
        .no-print { display: none !important; }
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
            width: 297mm !important;
            height: 210mm !important;
            overflow: hidden !important;
        }
        .sertifikat-page {
            box-shadow: none !important;
            width: 297mm !important;
            height: 210mm !important;
            page-break-after: avoid;
            page-break-inside: avoid;
        }
    }
</style>
</head>
<body>

    <!-- Sertifikat -->
    <div class="sertifikat-page">
        <div class="nomor">No: ' . htmlspecialchars($data['nomor']) . '</div>
        <div class="content">
            <div class="title">Sertifikat</div>
            <div class="subtitle">Penghargaan</div>
            <div class="decoration"></div>
            <div class="diberikan">Diberikan kepada</div>
            <div class="nama">' . strtoupper(htmlspecialchars($data['nama_pemuda'])) . '</div>
            <div class="atas">Atas prestasi</div>
            <div class="prestasi">"' . htmlspecialchars($data['nama_prestasi']) . '"</div>
            <div class="detail">
                Tingkat: <span>' . htmlspecialchars($data['tingkat']) . '</span> &nbsp;|&nbsp;
                Penyelenggara: <span>' . htmlspecialchars($data['penyelenggara']) . '</span>
            </div>
            <div class="detail">Tahun: <span>' . htmlspecialchars($data['tahun']) . '</span></div>
        </div>
        <div class="footer">
            <div class="date">Diterbitkan: ' . htmlspecialchars($data['tanggal_terbit']) . '</div>
            <div class="stamp">~ Pemuda Berprestasi ~</div>
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="btn-group no-print">
        <button onclick="window.print()" class="btn btn-print">
            <i class="bi bi-printer"></i> Cetak / Save as PDF
        </button>
        <button onclick="kembaliKePrestasi()" class="btn btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </button>
    </div>
    
    <div class="info-text no-print">
        <i class="bi bi-info-circle"></i> 
        Klik <strong>"Cetak / Save as PDF"</strong>, pilih <strong>"Save as PDF"</strong> 
        dengan orientasi <strong>Landscape</strong>. Centang <strong>"Background graphics"</strong>.
    </div>

    <script>
        function kembaliKePrestasi() {
            // Kalau dibuka dari iframe modal, tutup modal
            if (window.parent && window.parent !== window) {
                // Coba tutup modal di parent
                try {
                    if (window.parent.bootstrap && window.parent.document.getElementById("modalDetailBukti")) {
                        var modalEl = window.parent.document.getElementById("modalDetailBukti");
                        var modalInstance = window.parent.bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) {
                            modalInstance.hide();
                        } else {
                            new window.parent.bootstrap.Modal(modalEl).hide();
                        }
                    }
                } catch(e) {
                    // Fallback: arahkan parent ke prestasi.php
                    try { window.parent.location.href = "prestasi.php"; } catch(e2) {}
                }
            } else {
                // Dibuka di tab baru → langsung ke prestasi.php
                window.location.href = "../../pages/prestasi.php";
            }
        }
    </script>

</body>
</html>';
    
    // PDF hanya berisi sertifikat, tanpa tombol dan petunjuk halaman HTML.
    $pdfStyles = '
        @page { size: A4 landscape; margin: 0; }
        html, body {
            width: 297mm;
            height: 210mm;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden;
        }
        .sertifikat-page {
            width: 297mm !important;
            height: 210mm !important;
            margin: 0 !important;
            box-shadow: none !important;
        }
        .no-print { display: none !important; }
    ';
    $html = str_replace('</style>', $pdfStyles . '</style>', $html);

    require_once __DIR__ . '/../vendor/autoload.php';
    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', false);
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    file_put_contents($filePath, $dompdf->output());
    
    // Update DB dengan nama file bukti
    $namaFileEscaped = mysqli_real_escape_string($GLOBALS['conn'], $namaFile);
    mysqli_query($GLOBALS['conn'], "UPDATE tb_sertifikat SET bukti = '$namaFileEscaped' WHERE id_sertifikat = " . (int)$id_sertifikat);
    
    return $namaFile;
}
?>
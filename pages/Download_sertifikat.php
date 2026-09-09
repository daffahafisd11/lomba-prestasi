<?php
require_once '../config/database.php';
require_once '../functions/helpers.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$id_prestasi = isset($_POST['id_prestasi']) ? (int)$_POST['id_prestasi'] : 0;
$id_sertifikat = isset($_POST['id_sertifikat']) ? (int)$_POST['id_sertifikat'] : 0;

if ($id_prestasi <= 0 || $id_sertifikat <= 0) {
    die('Data tidak valid');
}

$prestasi = getPrestasi($id_prestasi);

if (!$prestasi || empty($prestasi['nomor_sertifikat'])) {
    die('Sertifikat tidak ditemukan');
}

$data = [
    'nomor' => $prestasi['nomor_sertifikat'],
    'nama_pemuda' => $prestasi['nama_pemuda'],
    'nama_prestasi' => $prestasi['nama_prestasi'],
    'tingkat' => $prestasi['tingkat'],
    'penyelenggara' => $prestasi['penyelenggara'],
    'tahun' => $prestasi['tahun'],
    'tanggal_terbit' => formatTanggal(date('Y-m-d'))
];

$namaFile = 'sertifikat-' . $data['nomor'] . '.pdf';

// Update database - Simpan nama file sebagai bukti
$updateBukti = "UPDATE tb_sertifikat SET bukti = '$namaFile' WHERE id_sertifikat = $id_sertifikat";
mysqli_query($conn, $updateBukti);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - <?= $data['nomor'] ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            width: 100%;
        }
        
        /* Sertifikat dengan template background */
        .sertifikat {
            width: 100%;
            padding: 60px 50px;
            background-image: url('../assets/img/sertifikat.png');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            text-align: center;
            position: relative;
            min-height: 650px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border: none;
        }
        
        .sertifikat::before {
            display: none;
        }
        
        /* Logo */
        .logo {
            position: absolute;
            top: 30px;
            left: 40px;
        }
        .logo img { width: 80px; height: auto; }
        
        /* Nomor Sertifikat */
        .nomor {
            position: absolute;
            top: 35px;
            right: 45px;
            font-size: 14px;
            color: #555;
            background: rgba(255,255,255,0.85);
            padding: 6px 18px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        /* # SERTIFIKAT */
        .title {
            font-size: 38px;
            font-weight: 900;
            color: #2D3E30;
            margin-top: 60px;
            letter-spacing: 8px;
            text-transform: uppercase;
        }
        
        /* ## PENGHARGAAN */
        .subtitle-main {
            font-size: 26px;
            font-weight: 700;
            color: #6B8F71;
            margin-top: 5px;
            letter-spacing: 6px;
            text-transform: uppercase;
        }
        
        /* Garis dekorasi */
        .title-decoration {
            width: 100px;
            height: 3px;
            background: #6B8F71;
            margin: 15px auto;
        }
        
        /* Diberikan kepada: */
        .subtitle {
            font-size: 18px;
            color: #777;
            margin-top: 15px;
            letter-spacing: 2px;
        }
        
        /* NAMA PENERIMA */
        .nama-pemuda {
            font-size: 52px;
            font-weight: 900;
            color: #2D3E30;
            margin: 20px 0 15px 0;
            text-transform: uppercase;
            letter-spacing: 4px;
        }
        
        /* Atas prestasi */
        .prestasi-label {
            font-size: 18px;
            color: #4A6B5A;
            margin-top: 10px;
        }
        
        /* Nama Prestasi */
        .prestasi-text {
            font-size: 26px;
            font-weight: 700;
            color: #2D3E30;
            margin: 8px 0;
        }
        
        /* Detail */
        .detail {
            font-size: 16px;
            color: #555;
            margin: 4px 0;
        }
        .detail span {
            color: #6B8F71;
            font-weight: 600;
        }
        
        /* Footer */
        .footer {
            margin-top: 45px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(0,0,0,0.08);
            padding-top: 15px;
        }
        .footer .date { font-size: 14px; color: #888; }
        .footer .stamp {
            font-family: "Brush Script MT", cursive;
            font-size: 24px;
            color: #6B8F71;
            opacity: 0.7;
        }
        
        /* Tombol */
        .btn-group {
            margin-top: 20px;
            text-align: center;
        }
        .btn-group .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            margin: 5px;
        }
        .btn-print {
            background: #28a745;
            color: white;
        }
        .btn-print:hover {
            background: #218838;
        }
        .btn-back {
            background: #6c757d;
            color: white;
        }
        .btn-back:hover {
            background: #5a6268;
        }
        
        .info-text {
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }
        
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .container { max-width: 100% !important; }
            .sertifikat {
                border-radius: 0 !important;
                box-shadow: none !important;
                padding: 40px !important;
                min-height: auto !important;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="sertifikat">
            <div class="logo">
                <img src="../assets/img/logo.png" alt="Logo">
            </div>
            <div class="nomor">No: <?= $data['nomor'] ?></div>
            
            <!-- # SERTIFIKAT -->
            <div class="title">Sertifikat</div>
            
            <!-- ## PENGHARGAAN -->
            <div class="subtitle-main">Penghargaan</div>
            
            <!-- Garis dekorasi -->
            <div class="title-decoration"></div>
            
            <!-- Diberikan kepada: -->
            <div class="subtitle">Diberikan kepada</div>
            
            <!-- NAMA PENERIMA -->
            <div class="nama-pemuda"><?= strtoupper($data['nama_pemuda']) ?></div>
            
            <!-- Atas prestasi -->
            <div class="prestasi-label">Atas prestasi</div>
            
            <!-- Nama Prestasi -->
            <div class="prestasi-text">"<?= $data['nama_prestasi'] ?>"</div>
            
            <!-- Detail -->
            <div class="detail">
                Tingkat: <span><?= $data['tingkat'] ?></span> &nbsp;|&nbsp; 
                Penyelenggara: <span><?= $data['penyelenggara'] ?></span>
            </div>
            <div class="detail">
                Tahun: <span><?= $data['tahun'] ?></span>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                <div class="date">Diterbitkan: <?= $data['tanggal_terbit'] ?></div>
                <div class="stamp">~ Pemuda Berprestasi ~</div>
            </div>
        </div>
        
        <!-- Tombol -->
        <div class="btn-group no-print">
            <button onclick="window.print()" class="btn btn-print">
                <i class="bi bi-printer"></i> Cetak / Save as PDF
            </button>
            <a href="prestasi.php" class="btn btn-back">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
        
        <div class="info-text no-print">
            <i class="bi bi-info-circle"></i> 
            Klik <strong>"Cetak / Save as PDF"</strong>, lalu pilih <strong>"Save as PDF"</strong>
        </div>
    </div>

</body>
</html>
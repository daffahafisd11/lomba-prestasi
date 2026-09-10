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
    'nomor'          => $prestasi['nomor_sertifikat'],
    'nama_pemuda'    => $prestasi['nama_pemuda'],
    'nama_prestasi'  => $prestasi['nama_prestasi'],
    'tingkat'        => $prestasi['tingkat'],
    'penyelenggara'  => $prestasi['penyelenggara'],
    'tahun'          => $prestasi['tahun'],
    'tanggal_terbit' => formatTanggal(date('Y-m-d'))
];

// Simpan file bukti (HTML) ke folder uploads/bukti/
$namaFile = saveBuktiSertifikat($id_sertifikat, $data);

// Redirect ke file bukti
header('Location: ../uploads/bukti/' . $namaFile);
exit;
?>
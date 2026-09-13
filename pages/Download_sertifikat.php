<?php
require_once '../config/database.php';
require_once '../functions/helpers.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$id_prestasi = isset($_POST['id_prestasi']) ? (int)$_POST['id_prestasi'] : 0;
$id_sertifikat = isset($_POST['id_sertifikat']) ? (int)$_POST['id_sertifikat'] : 0;
$verifNomor = trim($_POST['verif_nomor'] ?? '');
$verifNama = trim($_POST['verif_nama'] ?? '');
$verifPrestasi = trim($_POST['verif_prestasi'] ?? '');

if ($id_prestasi <= 0 || $id_sertifikat <= 0) {
    die('Data tidak valid');
}

$prestasi = getPrestasi($id_prestasi);

if (!$prestasi || empty($prestasi['nomor_sertifikat'])) {
    die('Sertifikat tidak ditemukan');
}

if (
    $id_sertifikat !== (int)$prestasi['id_sertifikat'] ||
    $verifNomor !== $prestasi['nomor_sertifikat'] ||
    $verifNama !== $prestasi['nama_pemuda'] ||
    $verifPrestasi !== $prestasi['nama_prestasi']
) {
    die('Data verifikasi tidak sesuai');
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

// Simpan file bukti dalam format PDF ke folder uploads/bukti/
$namaFile = saveBuktiSertifikat($id_sertifikat, $data);

if (!$namaFile) {
    die('Gagal membuat file PDF sertifikat');
}

// Paksa browser mengunduh PDF, bukan membukanya sebagai preview.
$filePath = __DIR__ . '/../uploads/bukti/' . $namaFile;
if (!is_file($filePath)) {
    die('File PDF sertifikat tidak ditemukan');
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($namaFile) . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-store, no-cache, must-revalidate');
readfile($filePath);
exit;
?>
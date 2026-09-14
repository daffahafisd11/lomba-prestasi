<?php
require_once 'config/database.php';
require_once 'functions/helpers.php';

// Ambil data dari database
$totalPemuda   = getTotal('tb_pemuda');
$totalPrestasi = getTotal('tb_prestasi');
$totalKategori = getTotal('tb_kategori');

// Ambil semua pemuda dan prestasi untuk ditampilkan di beranda
$pemudaList = getPemuda();
$prestasiList = getPrestasi();

$prestasiByPemuda = [];
foreach ($prestasiList as $prestasi) {
    $prestasiByPemuda[$prestasi['id_pemuda']][] = $prestasi;
}

// ===== FILTER: HANYA PEMUDA YANG PUNYA PRESTASI =====
$pemudaWithPrestasi = [];
foreach ($pemudaList as $pemuda) {
    if (isset($prestasiByPemuda[$pemuda['id_pemuda']]) && count($prestasiByPemuda[$pemuda['id_pemuda']]) > 0) {
        $pemudaWithPrestasi[] = $pemuda;
    }
}

// Ambil semua kategori
$kategoriList = getKategori();

// Warna dan ikon untuk kategori
$colors = ['success', 'success', 'success', 'success', 'success', 'success'];
$icons  = ['bi-book', 'bi-dribbble', 'bi-palette', 'bi-laptop', 'bi-bag', 'bi-people'];

// Ambil 6 pemuda pertama yang punya prestasi untuk ditampilkan di halaman utama
$pemudaLimit = array_slice($pemudaWithPrestasi, 0, 6);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemuda Berprestasi - Beranda</title>

    <link rel="icon" type="image/png" href="assets/img/icon-1.png" sizes="32x32">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda+SC:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* ============================================ */
        /* ===== GLOBAL ===== */
        /* ============================================ */
        a, a:hover, a:focus, a:active,
        .nav-link, .nav-link:hover, .nav-link:focus, .nav-link:active,
        .navbar-brand, .navbar-brand:hover, .navbar-brand:focus,
        .btn, .btn:hover, .btn:focus, .btn:active,
        .btn-link, .btn-link:hover,
        .text-muted, .text-secondary,
        .badge, .card-title, .category-card h6,
        .hero-badge, .hero-scroll-indicator a,
        footer a, footer a:hover,
        .section-title, .section-title::after,
        .card-prestasi .card-title, .card-prestasi .badge,
        .card-prestasi .text-muted, .category-card small,
        #tentang .btn-outline-primary, #tentang .text-secondary,
        .btn-primary, .btn-primary:hover,
        .btn-outline-primary, .btn-outline-primary:hover,
        h1, h2, h3, h4, h5, h6,
        h1::after, h2::after, h3::after, h4::after, h5::after, h6::after {
            text-decoration: none !important;
        }

        h1, h2, h3, h4, h5, h6 {
            text-align: center !important;
        }

        :root {
            --sage-primary: #6B8F71;
            --sage-primary-dark: #5A7A5F;
            --sage-primary-light: #8BA888;
            --sage-bg: #F7F4ED;
            --sage-text: #2D3E30;
            --sage-text-light: #5A6B5E;
            --sage-border: #C5DCC0;
            --sage-shadow: rgba(107, 143, 113, 0.15);
            --sage-gradient: linear-gradient(135deg, #8BA888, #6B8F71);
            --font-primary: 'Bodoni Moda SC', 'Poppins', 'Times New Roman', serif;
            --font-secondary: 'Poppins', 'Segoe UI', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: var(--font-secondary);
            background-color: #F7F4ED;
            color: var(--sage-text);
            line-height: 1.6;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #F7F4ED;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--sage-primary);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--sage-primary-dark);
        }

        /* ============================================ */
        /* ===== NAVBAR ===== */
        /* ============================================ */
        .navbar-custom {
            background: var(--sage-gradient);
            padding: 16px 0;
            transition: all 0.4s ease;
            box-shadow: 0 2px 15px var(--sage-shadow);
        }

        .navbar-custom.navbar-scrolled {
            background: rgba(107, 143, 113, 0.92) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 10px 0 !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.12) !important;
        }

        .navbar-custom .navbar-brand {
            color: #fff !important;
            font-family: var(--font-primary);
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            padding: 0;
        }

        .navbar-custom .navbar-brand img {
            transition: all 0.3s ease;
        }

        .navbar-custom .navbar-brand:hover img {
            transform: scale(1.05);
        }

        .navbar-custom .navbar-nav {
            align-items: center;
            gap: 4px;
        }

        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-family: var(--font-secondary);
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 8px 18px;
            border-radius: 10px;
            position: relative;
            margin: 0 2px;
        }

        .navbar-custom .nav-link:hover {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-2px);
        }

        .navbar-custom .nav-link.active {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.15);
        }

        .navbar-custom .nav-link:active {
            transform: scale(0.95);
        }

        .navbar-custom .nav-link.btn-light {
            color: var(--sage-primary) !important;
            background: #fff;
            font-family: var(--font-secondary);
            font-weight: 600;
            padding: 8px 28px;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            margin-left: 8px;
        }

        .navbar-custom .nav-link.btn-light:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            background: #f8fff8;
            color: var(--sage-primary) !important;
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.5);
            padding: 8px 12px;
            transition: all 0.3s ease;
            border-radius: 10px;
        }

        .navbar-toggler:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(90deg);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,0.9)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        @media (max-width: 991px) {
            .navbar-custom .navbar-nav {
                gap: 2px;
                padding-top: 10px;
            }
            .navbar-custom .nav-link {
                padding: 10px 16px;
                width: 100%;
                text-align: center;
            }
            .navbar-custom .nav-link.btn-light {
                margin-left: 0;
                margin-top: 8px;
                width: 100%;
            }
            .navbar-custom .navbar-brand img {
                width: 80px;
            }
        }

        /* ============================================ */
        /* ===== HERO SECTION ===== */
        /* ============================================ */
        .hero-section {
            padding: 140px 0 100px;
            margin-top: 0;
            background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)), url('../assets/img/background-index.png') center center/cover no-repeat;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            text-align: center;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -10%;
            width: 700px;
            height: 700px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50%;
            pointer-events: none;
            animation: floatBubble 18s ease-in-out infinite;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            pointer-events: none;
            animation: floatBubble 22s ease-in-out infinite reverse;
        }

        @keyframes floatBubble {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(40px, -30px) scale(1.08); }
        }

        .hero-section .container {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-family: var(--font-primary);
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 20px;
            animation: fadeInUp 0.8s ease 0.1s both;
        }

        .hero-title span {
            color: #FFD700;
            text-shadow: 0 2px 30px rgba(255, 215, 0, 0.25);
            position: relative;
            display: inline-block;
        }

        .hero-section .lead {
            font-family: var(--font-secondary);
            font-size: 1.3rem;
            font-weight: 300;
            opacity: 0.9;
            max-width: 620px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .hero-buttons {
            margin-top: 32px;
            animation: fadeInUp 0.8s ease 0.4s both;
        }

        .hero-section .btn-primary {
            font-family: var(--font-secondary);
            background: #fff;
            color: var(--sage-primary);
            border: none;
            padding: 16px 48px;
            font-weight: 700;
            border-radius: 50px;
            transition: all 0.4s ease;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .hero-section .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transition: left 0.6s ease;
        }

        .hero-section .btn-primary:hover::before {
            left: 100%;
        }

        .hero-section .btn-primary:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
            background: #ffffff;
            color: var(--sage-primary);
        }

        .hero-section .btn-outline-light {
            font-family: var(--font-secondary);
            color: #fff;
            border: 2px solid rgba(255, 255, 255, 0.4);
            padding: 14px 40px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.4s ease;
            margin-left: 12px;
        }

        .hero-section .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: #fff;
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .hero-scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            animation: bounceDown 2s ease-in-out infinite;
            cursor: pointer;
        }

        .hero-scroll-indicator a {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.5rem;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .hero-scroll-indicator a:hover {
            color: #fff;
            transform: translateY(5px);
        }

        @keyframes bounceDown {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(10px); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 991px) {
            .hero-section { min-height: auto; padding: 130px 0 70px; }
            .hero-title { font-size: 3.5rem; }
            .hero-section .lead { font-size: 1.1rem; }
            .hero-section .btn-primary { padding: 14px 35px; }
            .hero-section .btn-outline-light { padding: 12px 30px; }
        }

        @media (max-width: 768px) {
            .hero-section { padding: 120px 0 60px; }
            .hero-title { font-size: 2.8rem; }
            .hero-section .lead { font-size: 1rem; padding: 0 15px; }
            .hero-buttons { display: flex; flex-direction: column; align-items: center; gap: 12px; }
            .hero-section .btn-primary { display: block; width: 100%; max-width: 300px; padding: 14px 20px; }
            .hero-section .btn-outline-light { display: block; width: 100%; max-width: 300px; padding: 12px 20px; margin-left: 0; }
            .hero-scroll-indicator { display: none; }
        }

        @media (max-width: 576px) {
            .hero-section { padding: 110px 0 50px; }
            .hero-title { font-size: 2.2rem; }
            .hero-section .btn-primary { max-width: 100%; }
            .hero-section .btn-outline-light { max-width: 100%; }
        }

        /* ============================================ */
        /* ===== SECTION WRAPPER ===== */
        /* ============================================ */
        .section-wrapper {
            padding: 80px 0;
            background-color: #F7F4ED !important;
        }

        .section-title {
            text-align: center !important;
            font-family: var(--font-primary);
            font-size: 3rem;
            font-weight: 800;
            color: var(--sage-text);
            margin-bottom: 16px;
            position: relative;
            display: block;
            width: 100%;
        }

        .section-title::after {
            display: none !important;
        }

        .section-subtitle {
            font-family: var(--font-secondary);
            color: var(--sage-text-light);
            font-size: 1.1rem;
            font-weight: 300;
            margin-bottom: 48px;
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ============================================ */
        /* ===== PEMUDA SECTION ===== */
        /* ============================================ */
        #pemuda {
            background-color: #FAF7F2 !important;
            padding-top: 120px !important;
            padding-bottom: 120px !important;
        }

        #pemuda .pemuda-summary-card {
            background: #fff;
            border: 1px solid var(--sage-border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 25px var(--sage-shadow);
            height: 100%;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        #pemuda .pemuda-summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px var(--sage-shadow);
        }

        #pemuda .pemuda-summary-card .card-title {
            font-family: var(--font-primary);
            font-weight: 700;
            color: var(--sage-text);
            font-size: 1.2rem;
            margin-bottom: 4px;
            text-align: left !important;
        }

        #pemuda .pemuda-summary-card .text-muted {
            font-size: 0.9rem;
            margin-bottom: 12px;
        }

        /* ============================================ */
        /* ===== KATEGORI SECTION ===== */
        /* ============================================ */
        #kategori {
            background-color: #F7F4ED !important;
            padding-top: 100px !important;
            padding-bottom: 100px !important;
        }

        #kategori .category-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px 16px;
            text-align: center;
            box-shadow: 0 4px 25px var(--sage-shadow);
            transition: all 0.3s ease;
            border: 2px solid transparent;
            height: 100%;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        #kategori .category-card::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--sage-gradient);
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 0.4s ease;
        }

        #kategori .category-card:hover::before {
            transform: scaleX(1);
        }

        #kategori .category-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px var(--sage-shadow);
            border-color: var(--sage-border);
        }

        #kategori .category-card.active {
            border-color: var(--sage-primary);
            box-shadow: 0 12px 40px var(--sage-shadow);
            transform: translateY(-6px);
        }

        #kategori .category-card.active::before {
            transform: scaleX(1);
        }

        #kategori .category-icon {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 2rem;
            color: #fff;
            transition: all 0.4s ease;
            background: var(--sage-gradient) !important;
            flex-shrink: 0;
        }

        #kategori .category-card:hover .category-icon {
            transform: scale(1.08) rotate(5deg);
        }

        #kategori .category-card h6 {
            font-family: var(--font-primary);
            font-weight: 700;
            color: var(--sage-text);
            margin-bottom: 2px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        #kategori .category-card:hover h6 {
            color: var(--sage-primary);
        }

        #kategori .category-card small {
            color: var(--sage-text-light);
            font-size: 0.8rem;
        }

        #kategori .category-card .badge-prestasi {
            margin-top: 6px;
            font-size: 0.7rem;
            padding: 2px 10px;
            border-radius: 20px;
            background: var(--sage-primary);
            color: #fff;
        }

        /* ============================================ */
        /* ===== CARD HASIL KATEGORI ===== */
        /* ============================================ */
        #categoryResultsCard {
            margin-top: 40px;
            display: none;
            animation: fadeInUp 0.5s ease;
        }

        #categoryResultsCard.active {
            display: block;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .results-card-wrapper {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 8px 40px var(--sage-shadow);
            border: 1px solid var(--sage-border);
        }

        .results-card-wrapper .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--sage-border);
            margin-bottom: 20px;
        }

        .results-card-wrapper .results-header .title-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .results-card-wrapper .results-header .title-section h5 {
            font-family: var(--font-primary);
            font-weight: 700;
            color: var(--sage-text);
            margin: 0;
            text-align: left !important;
        }

        .results-card-wrapper .results-header .title-section h5 span {
            color: var(--sage-primary);
        }

        .results-card-wrapper .results-header .title-section .badge-count {
            background: var(--sage-primary);
            color: #fff;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .results-card-wrapper .results-header .search-section {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .results-card-wrapper .results-header .search-section .search-box {
            display: flex;
            gap: 8px;
            background: #f5f5f5;
            border-radius: 10px;
            padding: 4px;
            border: 1px solid #e8e8e8;
            transition: all 0.3s ease;
        }

        .results-card-wrapper .results-header .search-section .search-box:focus-within {
            border-color: var(--sage-primary);
            box-shadow: 0 0 0 0.25rem rgba(107, 143, 113, 0.15);
            background: #fff;
        }

        .results-card-wrapper .results-header .search-section .search-box input {
            border: none;
            background: transparent;
            padding: 8px 12px;
            font-size: 0.9rem;
            width: 200px;
            outline: none;
        }

        .results-card-wrapper .results-header .search-section .search-box input::placeholder {
            color: #aaa;
        }

        .results-card-wrapper .results-header .search-section .search-box .btn-search {
            background: var(--sage-primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 14px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .results-card-wrapper .results-header .search-section .search-box .btn-search:hover {
            background: var(--sage-primary-dark);
            transform: scale(1.02);
        }

        .results-card-wrapper .results-header .search-section .btn-reset {
            background: transparent;
            border: 1px solid #dc3545;
            color: #dc3545;
            border-radius: 10px;
            padding: 8px 16px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .results-card-wrapper .results-header .search-section .btn-reset:hover {
            background: #dc3545;
            color: #fff;
            transform: translateY(-2px);
        }

        /* ===== PRESTASI CARD DI DALAM CARD ===== */
        .prestasi-item-card-wrapper.is-hidden {
            display: none;
        }

        .prestasi-item-card {
            background: #fafafa;
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            padding: 16px 20px;
            transition: all 0.3s ease;
            height: 100%;
        }

        .prestasi-item-card:hover {
            background: #fff;
            border-color: var(--sage-border);
            box-shadow: 0 4px 20px var(--sage-shadow);
            transform: translateY(-3px);
        }

        .prestasi-item-card .prestasi-title {
            font-family: var(--font-primary);
            font-weight: 700;
            color: var(--sage-text);
            font-size: 1.05rem;
            margin-bottom: 2px;
        }

        .prestasi-item-card .prestasi-title i {
            color: #FFD700;
        }

        .prestasi-item-card .prestasi-pemuda {
            font-weight: 600;
            color: var(--sage-primary);
            font-size: 0.9rem;
        }

        .prestasi-item-card .prestasi-meta {
            font-size: 0.8rem;
            color: var(--sage-text-light);
        }

        .prestasi-item-card .prestasi-meta .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-right: 12px;
        }

        .prestasi-item-card .prestasi-meta .meta-item i {
            font-size: 0.8rem;
        }

        .prestasi-item-card .btn-download-prestasi {
            font-size: 0.75rem;
            padding: 4px 14px;
            border-radius: 20px;
            background: var(--sage-primary);
            color: #fff;
            border: none;
            transition: all 0.3s ease;
        }

        .prestasi-item-card .btn-download-prestasi:hover {
            background: var(--sage-primary-dark);
            transform: scale(1.05);
        }

        .prestasi-item-card .badge-kategori {
            font-size: 0.7rem;
            padding: 3px 10px;
            border-radius: 20px;
            background: var(--sage-primary);
            color: #fff;
        }

        .empty-results-card {
            text-align: center;
            padding: 40px 20px;
            color: var(--sage-text-light);
        }

        .empty-results-card i {
            font-size: 3rem;
            display: block;
            margin-bottom: 12px;
            opacity: 0.3;
        }

        /* ============================================ */
        /* ===== MODAL ===== */
        /* ============================================ */
        .modal-pemuda-list .modal-content,
        .modal-detail-pemuda .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .modal-pemuda-list .modal-header,
        .modal-detail-pemuda .modal-header {
            background: var(--sage-gradient);
            border-radius: 20px 20px 0 0;
            color: #fff;
            border-bottom: none;
            padding: 20px 24px;
        }

        .modal-pemuda-list .modal-header .modal-title,
        .modal-detail-pemuda .modal-header .modal-title {
            color: #fff;
            font-family: var(--font-primary);
            font-weight: 700;
        }

        .modal-pemuda-list .modal-header .btn-close,
        .modal-detail-pemuda .modal-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        .modal-pemuda-list .modal-header .btn-close:hover,
        .modal-detail-pemuda .modal-header .btn-close:hover {
            opacity: 1;
        }

        .modal-pemuda-list .modal-body,
        .modal-detail-pemuda .modal-body {
            padding: 24px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-pemuda-list .search-wrapper {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #fff;
            padding-bottom: 16px;
            margin-bottom: 16px;
            border-bottom: 1px solid #eee;
        }

        .modal-pemuda-list .search-wrapper .input-group {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        }

        .modal-pemuda-list .search-wrapper .input-group .form-control {
            border: 1px solid var(--sage-border);
            padding: 12px 16px;
            font-size: 0.95rem;
            border-radius: 12px 0 0 12px;
        }

        .modal-pemuda-list .search-wrapper .input-group .form-control:focus {
            border-color: var(--sage-primary);
            box-shadow: 0 0 0 0.25rem rgba(107, 143, 113, 0.15);
        }

        .modal-pemuda-list .search-wrapper .input-group .btn {
            border-radius: 0 12px 12px 0;
            padding: 12px 24px;
            background: var(--sage-primary);
            border-color: var(--sage-primary);
            color: #fff;
        }

        .modal-pemuda-list .search-wrapper .input-group .btn:hover {
            background: var(--sage-primary-dark);
            border-color: var(--sage-primary-dark);
        }

        .modal-pemuda-list .search-info {
            font-size: 0.85rem;
            color: var(--sage-text-light);
            margin-top: 8px;
            text-align: left !important;
        }

        .modal-pemuda-list .pemuda-item {
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
            background: #fafafa;
        }

        .modal-pemuda-list .pemuda-item:hover {
            background: #fff;
            border-color: var(--sage-border);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .modal-pemuda-list .pemuda-item .pemuda-name {
            font-family: var(--font-primary);
            font-weight: 700;
            color: var(--sage-text);
            font-size: 1.1rem;
        }

        .modal-pemuda-list .pemuda-item .pemuda-detail {
            font-size: 0.9rem;
            color: var(--sage-text-light);
        }

        .modal-pemuda-list .pemuda-item .pemuda-prestasi-badge {
            background: var(--sage-primary);
            color: #fff;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .modal-pemuda-list .pemuda-item .prestasi-list {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #e0e0e0;
        }

        .modal-pemuda-list .pemuda-item .prestasi-list .prestasi-item {
            font-size: 0.85rem;
            padding: 4px 0;
            color: var(--sage-text);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }

        .modal-pemuda-list .pemuda-item .prestasi-list .prestasi-item .badge-sertifikat {
            background: #e8f5e9;
            color: var(--sage-primary-dark);
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 500;
        }

        .modal-pemuda-list .no-result {
            text-align: center;
            padding: 40px 20px;
            color: var(--sage-text-light);
        }

        .modal-pemuda-list .no-result i {
            font-size: 3rem;
            display: block;
            margin-bottom: 16px;
            opacity: 0.3;
        }

        .modal-detail-pemuda .prestasi-detail {
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            background: #fafafa;
            transition: all 0.2s ease;
        }

        .modal-detail-pemuda .prestasi-detail:hover {
            background: #fff;
            border-color: var(--sage-border);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        /* ============================================ */
        /* ===== TENTANG ===== */
        /* ============================================ */
        #tentang {
            background-color: #F7F4ED !important;
        }

        #tentang .btn-outline-primary {
            color: var(--sage-primary);
            border-color: var(--sage-primary);
            border-radius: 50px;
            padding: 12px 28px;
            font-weight: 500;
            transition: all 0.4s ease;
        }

        #tentang .btn-outline-primary:hover {
            background: var(--sage-primary);
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 6px 25px var(--sage-shadow);
        }

        /* ============================================ */
        /* ===== FOOTER ===== */
        /* ============================================ */
        footer {
            background-color: #F7F4ED !important;
            padding: 40px 0;
        }

        footer p {
            color: var(--sage-text);
            opacity: 0.9;
            font-weight: 500;
        }

        /* ============================================ */
        /* ===== TOMBOL UMUM ===== */
        /* ============================================ */
        .btn-primary {
            background-color: var(--sage-primary);
            border-color: var(--sage-primary);
            color: #fff;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--sage-primary-dark);
            border-color: var(--sage-primary-dark);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px var(--sage-shadow);
        }

        .btn-outline-primary {
            color: var(--sage-primary);
            border-color: var(--sage-primary);
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: var(--sage-primary);
            border-color: var(--sage-primary);
            color: #fff;
            transform: translateY(-2px);
        }

        .text-muted, .text-secondary {
            color: var(--sage-text-light) !important;
        }

        /* ============================================ */
        /* ===== RESPONSIVE ===== */
        /* ============================================ */
        @media (max-width: 991px) {
            .section-title { font-size: 2.6rem; }
            .section-wrapper { padding: 60px 0; }
            #pemuda { padding-top: 80px !important; padding-bottom: 80px !important; }
            #kategori { padding-top: 80px !important; padding-bottom: 80px !important; }
            .results-card-wrapper .results-header { flex-direction: column; align-items: stretch; }
            .results-card-wrapper .results-header .search-section .search-box input { width: 150px; }
        }

        @media (max-width: 768px) {
            .section-title { font-size: 2.2rem; }
            .section-wrapper { padding: 50px 0; }
            .section-subtitle { font-size: 1rem; margin-bottom: 32px; }
            #pemuda { padding-top: 60px !important; padding-bottom: 60px !important; }
            #kategori { padding-top: 60px !important; padding-bottom: 60px !important; }
            #kategori .category-card { min-height: 140px; padding: 16px 12px; }
            #kategori .category-icon { width: 55px; height: 55px; font-size: 1.6rem; }
            #kategori .category-card h6 { font-size: 0.9rem; }
            .modal-pemuda-list .modal-body { padding: 16px; max-height: 60vh; }
            .modal-detail-pemuda .modal-body { padding: 16px; max-height: 60vh; }
            .results-card-wrapper .results-header .search-section .search-box input { width: 120px; }
            .results-card-wrapper { padding: 16px; }
        }

        @media (max-width: 576px) {
            .section-title { font-size: 1.8rem; }
            .section-wrapper { padding: 40px 0; }
            .section-subtitle { font-size: 0.95rem; margin-bottom: 24px; }
            #kategori .category-card { min-height: 120px; padding: 12px 8px; }
            #kategori .category-icon { width: 45px; height: 45px; font-size: 1.3rem; margin-bottom: 8px; }
            #kategori .category-card h6 { font-size: 0.8rem; }
            #kategori .category-card small { font-size: 0.7rem; }
            #pemuda { padding-top: 50px !important; padding-bottom: 50px !important; }
            #kategori { padding-top: 50px !important; padding-bottom: 50px !important; }
            .modal-pemuda-list .modal-body { padding: 12px; max-height: 50vh; }
            .modal-detail-pemuda .modal-body { padding: 12px; max-height: 50vh; }
            .results-card-wrapper { padding: 12px; }
            .results-card-wrapper .results-header .search-section { flex-direction: column; align-items: stretch; }
            .results-card-wrapper .results-header .search-section .search-box input { width: 100%; }
            .results-card-wrapper .results-header .search-section .btn-reset { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="../assets/img/logo.png" alt="Logo" width="100px">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php" data-section="beranda">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pemuda" data-section="pemuda">Pemuda Berprestasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kategori" data-section="kategori">Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang" data-section="tentang">Tentang</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="beranda">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1 class="hero-title">
                        Muda Berprestasi, <br><span>Menginspirasi Negeri</span>
                    </h1>
                    <p class="lead mb-4">
                        Platform pendataan dan apresiasi pemuda berprestasi untuk menampilkan karya, 
                        pencapaian, dan kontribusi generasi muda.
                    </p>
                    <div class="hero-buttons">
                        <a href="#pemuda" class="btn btn-primary btn-lg">
                            <i class="bi bi-eye me-2"></i>Jelajahi Prestasi
                        </a>
                        <a href="#tentang" class="btn btn-outline-light">
                            <i class="bi bi-info-circle me-2"></i>Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-scroll-indicator">
            <a href="#pemuda">
                <i class="bi bi-chevron-double-down"></i>
            </a>
        </div>
    </section>

    <!-- Pemuda Berprestasi -->
    <section id="pemuda" class="section-wrapper" style="background-color: #FAF7F2 !important;">
        <div class="container">
            <h2 class="text-center section-title">Pemuda Berprestasi</h2>
            <p class="text-center section-subtitle">
                Mengenal pemuda inspiratif yang telah mengukir prestasi melalui karya dan kontribusinya.
            </p>

            <?php if (empty($pemudaWithPrestasi)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-secondary"></i>
                    <p class="text-secondary mt-2">Belum ada pemuda yang memiliki prestasi.</p>
                </div>
            <?php else: ?>
                <div class="row g-4" id="pemudaList">
                    <?php foreach ($pemudaLimit as $pemuda): ?>
                        <?php $prestasiPemuda = $prestasiByPemuda[$pemuda['id_pemuda']] ?? []; ?>
                        <div class="col-md-6">
                            <article class="pemuda-summary-card" data-bs-toggle="modal" data-bs-target="#modalPemuda<?= (int)$pemuda['id_pemuda'] ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5 class="card-title"><?= htmlspecialchars($pemuda['nama_pemuda']) ?></h5>
                                    <span class="badge bg-primary"><?= count($prestasiPemuda) ?> Prestasi</span>
                                </div>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-building me-1"></i><?= htmlspecialchars($pemuda['asal_instansi'] ?? '-') ?>
                                    <span class="mx-1">|</span>
                                    <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($pemuda['kecamatan'] ?? '-') ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="text-muted small">
                                        <i class="bi bi-eye"></i> Klik untuk detail
                                    </span>
                                </div>
                            </article>
                        </div>

                        <!-- Modal Detail Pemuda -->
                        <div class="modal fade modal-detail-pemuda" id="modalPemuda<?= (int)$pemuda['id_pemuda'] ?>" tabindex="-1" aria-labelledby="modalPemudaLabel<?= (int)$pemuda['id_pemuda'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalPemudaLabel<?= (int)$pemuda['id_pemuda'] ?>">
                                            <i class="bi bi-person-circle me-2"></i><?= htmlspecialchars($pemuda['nama_pemuda']) ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Instansi:</strong> <?= htmlspecialchars($pemuda['asal_instansi'] ?? '-') ?></p>
                                                <p class="mb-1"><strong>Kecamatan:</strong> <?= htmlspecialchars($pemuda['kecamatan'] ?? '-') ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Jenis Kelamin:</strong> <?= htmlspecialchars($pemuda['jenis_kelamin'] ?? '-') ?></p>
                                                <p class="mb-1"><strong>Tanggal Lahir:</strong> <?= !empty($pemuda['tanggal_lahir']) ? date('d F Y', strtotime($pemuda['tanggal_lahir'])) : '-' ?></p>
                                            </div>
                                        </div>
                                        <?php if (!empty($pemuda['alamat'])): ?>
                                            <p class="mb-1"><strong>Alamat:</strong> <?= htmlspecialchars($pemuda['alamat']) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($pemuda['deskripsi'])): ?>
                                            <p class="mb-3"><strong>Deskripsi:</strong> <?= htmlspecialchars($pemuda['deskripsi']) ?></p>
                                        <?php endif; ?>

                                        <hr>
                                        <h6 class="fw-bold mb-3"><i class="bi bi-trophy text-warning me-2"></i>Daftar Prestasi</h6>
                                        <?php if (!empty($prestasiPemuda)): ?>
                                            <?php foreach ($prestasiPemuda as $prestasi): ?>
                                                <div class="prestasi-detail">
                                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                        <div>
                                                            <p class="fw-semibold mb-1">
                                                                <i class="bi bi-award text-warning me-1"></i>
                                                                <?= htmlspecialchars($prestasi['nama_prestasi']) ?>
                                                            </p>
                                                            <p class="small mb-1">
                                                                Kategori: <?= htmlspecialchars($prestasi['nama_katgeori'] ?? 'Tanpa Kategori') ?> |
                                                                Tingkat: <?= htmlspecialchars($prestasi['tingkat'] ?? '-') ?> |
                                                                Tahun: <?= (int)$prestasi['tahun'] ?>
                                                            </p>
                                                            <p class="small text-muted mb-1">Penyelenggara: <?= htmlspecialchars($prestasi['penyelenggara'] ?? '-') ?></p>
                                                            <p class="small text-muted mb-0">
                                                                <strong>No. Sertifikat:</strong> <?= htmlspecialchars($prestasi['nomor_sertifikat'] ?? '-') ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted text-center py-3">Belum ada prestasi tercatat.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Tombol Lihat Semua Pemuda -->
                <?php if (count($pemudaWithPrestasi) > 6): ?>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-primary btn-lg px-5" data-bs-toggle="modal" data-bs-target="#modalSemuaPemuda">
                            <i class="bi bi-people me-2"></i>Lihat Semua Pemuda (<?= count($pemudaWithPrestasi) ?>)
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Modal Lihat Semua Pemuda -->
    <div class="modal fade modal-pemuda-list" id="modalSemuaPemuda" tabindex="-1" aria-labelledby="modalSemuaPemudaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSemuaPemudaLabel">
                        <i class="bi bi-people me-2"></i>Semua Pemuda Berprestasi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="search-wrapper">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchPemuda" placeholder="Cari berdasarkan nama pemuda, prestasi, atau nomor sertifikat...">
                            <button class="btn btn-primary" id="btnSearchPemuda">
                                <i class="bi bi-search me-1"></i>Cari
                            </button>
                        </div>
                        <div class="search-info" id="searchInfo">
                            Menampilkan <strong id="resultCount"><?= count($pemudaWithPrestasi) ?></strong> data pemuda
                        </div>
                    </div>

                    <div id="pemudaListContainer">
                        <?php foreach ($pemudaWithPrestasi as $pemuda): ?>
                            <?php $prestasiPemuda = $prestasiByPemuda[$pemuda['id_pemuda']] ?? []; ?>
                            <div class="pemuda-item" data-search="<?= strtolower($pemuda['nama_pemuda'] . ' ' . implode(' ', array_column($prestasiPemuda, 'nama_prestasi')) . ' ' . implode(' ', array_column($prestasiPemuda, 'nomor_sertifikat'))) ?>">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <div class="pemuda-name"><?= htmlspecialchars($pemuda['nama_pemuda']) ?></div>
                                        <div class="pemuda-detail">
                                            <i class="bi bi-building me-1"></i><?= htmlspecialchars($pemuda['asal_instansi'] ?? '-') ?>
                                            <span class="mx-1">|</span>
                                            <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($pemuda['kecamatan'] ?? '-') ?>
                                        </div>
                                    </div>
                                    <span class="pemuda-prestasi-badge">
                                        <i class="bi bi-trophy me-1"></i><?= count($prestasiPemuda) ?> Prestasi
                                    </span>
                                </div>

                                <?php if (!empty($prestasiPemuda)): ?>
                                    <div class="prestasi-list">
                                        <?php foreach ($prestasiPemuda as $prestasi): ?>
                                            <div class="prestasi-item">
                                                <i class="bi bi-award text-warning me-1"></i>
                                                <strong><?= htmlspecialchars($prestasi['nama_prestasi']) ?></strong>
                                                <span class="badge-sertifikat">
                                                    <i class="bi bi-card-text me-1"></i><?= htmlspecialchars($prestasi['nomor_sertifikat'] ?? 'No Sertifikat') ?>
                                                </span>
                                                <span class="text-muted small">
                                                    <?= htmlspecialchars($prestasi['tingkat'] ?? '-') ?> | <?= $prestasi['tahun'] ?? '-' ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted small mt-2">Belum ada prestasi tercatat</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="no-result" id="noResult" style="display: none;">
                        <i class="bi bi-search"></i>
                        <h6>Tidak ditemukan</h6>
                        <p class="text-muted">Coba gunakan kata kunci lain untuk mencari pemuda atau prestasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kategori -->
    <section id="kategori" class="section-wrapper">
        <div class="container">
            <h2 class="text-center section-title">Kategori</h2>
            <p class="text-center section-subtitle">
                Pilih kategori untuk melihat daftar prestasi di bidang tersebut
            </p>

            <!-- Grid Kategori -->
            <div class="row g-4 justify-content-center" id="kategoriContainer">
                <?php 
                    $i = 0;
                    foreach ($kategoriList as $kategori):
                        $color = $colors[$i % count($colors)];
                        $icon  = $icons[$i % count($icons)];
                        $totalKat = $kategori['total_prestasi'] ?? 0;
                ?>
                    <div class="col-md-3 col-4">
                        <button type="button" class="category-card w-100 border" data-category-filter="<?= (int)$kategori['id_kategori'] ?>" data-category-name="<?= htmlspecialchars($kategori['nama_katgeori'], ENT_QUOTES, 'UTF-8') ?>">
                            <div class="category-icon bg-<?= $color ?>">
                                <i class="<?= $icon ?>"></i>
                            </div>
                            <h6><?= htmlspecialchars($kategori['nama_katgeori']) ?></h6>
                            <small><?= $totalKat ?> Prestasi</small>
                            <?php if ($totalKat > 0): ?>
                                <span class="badge-prestasi"><?= $totalKat ?> Data</span>
                            <?php endif; ?>
                        </button>
                    </div>
                <?php 
                    $i++; 
                    endforeach; 
                ?>
            </div>

            <!-- Card Hasil Kategori -->
            <div id="categoryResultsCard">
                <div class="results-card-wrapper">
                    <!-- Header -->
                    <div class="results-header">
                        <div class="title-section">
                            <h5>
                                <i class="bi bi-trophy text-warning me-2"></i>
                                Prestasi: <span id="selectedCategoryName">-</span>
                            </h5>
                            <span class="badge-count" id="resultCountLabel">0 data</span>
                        </div>
                        <div class="search-section">
                            <div class="search-box">
                                <input type="text" id="searchPrestasiKategori" placeholder="Cari prestasi atau nomor sertifikat...">
                                <button class="btn-search" id="btnSearchPrestasiKategori">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <button class="btn-reset" id="resetCategoryFilter">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </button>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="categoryResultsEmpty" style="display: none;">
                        <div class="empty-results-card">
                            <i class="bi bi-inbox"></i>
                            <h6>Belum ada prestasi pada kategori ini</h6>
                            <p class="text-muted small">Silakan pilih kategori lain untuk melihat prestasi.</p>
                        </div>
                    </div>

                    <!-- Grid Prestasi -->
                    <div class="row g-3" id="categoryResultsList">
                        <?php foreach ($prestasiList as $prestasi): ?>
                            <div class="col-md-6 prestasi-item-card-wrapper is-hidden" data-category-result="<?= (int)($prestasi['id_kategori'] ?? 0) ?>" data-search="<?= strtolower($prestasi['nama_prestasi'] . ' ' . ($prestasi['nomor_sertifikat'] ?? '')) ?>">
                                <div class="prestasi-item-card">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                        <div>
                                            <div class="prestasi-title">
                                                <i class="bi bi-award me-1"></i>
                                                <?= htmlspecialchars($prestasi['nama_prestasi']) ?>
                                            </div>
                                            <div class="prestasi-pemuda">
                                                <i class="bi bi-person me-1"></i>
                                                <?= htmlspecialchars($prestasi['nama_pemuda']) ?>
                                            </div>
                                        </div>
                                        <span class="badge-kategori"><?= htmlspecialchars($prestasi['nama_katgeori'] ?? 'Tanpa Kategori') ?></span>
                                    </div>
                                    <div class="prestasi-meta mt-2">
                                        <span class="meta-item"><i class="bi bi-trophy"></i><?= htmlspecialchars($prestasi['tingkat'] ?? '-') ?></span>
                                        <span class="meta-item"><i class="bi bi-calendar"></i><?= (int)$prestasi['tahun'] ?></span>
                                        <span class="meta-item"><i class="bi bi-building"></i><?= htmlspecialchars($prestasi['penyelenggara'] ?? '-') ?></span>
                                        <div class="mt-1">
                                            <strong>No. Sertifikat:</strong> <?= htmlspecialchars($prestasi['nomor_sertifikat'] ?? '-') ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($prestasi['bukti']) && file_exists('uploads/bukti/' . $prestasi['bukti'])): ?>
                                        <div class="mt-2">
                                            <a href="uploads/bukti/<?= rawurlencode($prestasi['bukti']) ?>" download class="btn btn-download-prestasi">
                                                <i class="bi bi-download me-1"></i>Download Sertifikat
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang -->
    <section id="tentang" class="section-wrapper" style="background-color: #F7F4ED !important;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="section-title text-start" style="text-align: left !important;">Tentang Pemuda Berprestasi</h2>
                    <p class="text-secondary mb-4" style="text-align: left;">
                        Pemuda Berprestasi merupakan platform informasi dan pendataan yang bertujuan untuk 
                        mengenalkan, mendokumentasikan, dan mengapresiasi pencapaian generasi muda di berbagai bidang.
                    </p>
                    <p class="text-secondary mb-4" style="text-align: left;">
                        Banyak pemuda memiliki prestasi di berbagai bidang, namun informasi mengenai 
                        pencapaian tersebut belum terdokumentasi secara terpusat. Sistem ini dibuat untuk 
                        memudahkan pendataan, pengelolaan, dan publikasi prestasi pemuda agar dapat dikenal 
                        dan diapresiasi oleh masyarakat.
                    </p>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0 text-center">
                    <img src="../assets/img/start-up.jpg" alt="Tentang Pemuda Berprestasi" class="img-fluid rounded-4 shadow" style="max-width: 100%; border-radius: 20px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-white py-4" style="background-color: #F7F4ED !important;">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Pemuda Berprestasi - Made by <strong>Daffa Hafisd P</strong></p>
        </div>
    </footer>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navbar Scroll Effect
            const navbar = document.getElementById('mainNavbar');
            let isScrolled = false;

            window.addEventListener('scroll', function() {
                const scrollY = window.scrollY;
                if (scrollY > 30 && !isScrolled) {
                    isScrolled = true;
                    navbar.classList.add('navbar-scrolled');
                } else if (scrollY <= 30 && isScrolled) {
                    isScrolled = false;
                    navbar.classList.remove('navbar-scrolled');
                }
            }, { passive: true });

            // Active nav link
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link[data-section]');

            function updateActiveLink() {
                let current = 'beranda';
                const scrollPosition = window.scrollY + 120;

                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.offsetHeight;
                    if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                        current = section.getAttribute('id');
                    }
                });

                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('data-section') === current) {
                        link.classList.add('active');
                    }
                });
            }

            window.addEventListener('scroll', updateActiveLink, { passive: true });
            updateActiveLink();

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const targetId = this.getAttribute('href');
                    if (targetId && targetId.startsWith('#')) {
                        e.preventDefault();
                        const targetSection = document.querySelector(targetId);
                        if (targetSection) {
                            targetSection.scrollIntoView({ behavior: 'smooth' });
                        }
                    }
                });
            });

            // ===== SEARCH PEMUDA DI MODAL =====
            const searchInput = document.getElementById('searchPemuda');
            const searchBtn = document.getElementById('btnSearchPemuda');
            const pemudaItems = document.querySelectorAll('.pemuda-item');
            const noResult = document.getElementById('noResult');
            const resultCount = document.getElementById('resultCount');

            function filterPemuda() {
                const keyword = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                pemudaItems.forEach(item => {
                    const searchData = item.dataset.search || '';
                    const matches = keyword === '' || searchData.includes(keyword);
                    item.style.display = matches ? 'block' : 'none';
                    if (matches) visibleCount++;
                });

                if (noResult) noResult.style.display = visibleCount === 0 ? 'block' : 'none';
                if (resultCount) resultCount.textContent = visibleCount;
            }

            if (searchInput) {
                searchInput.addEventListener('keyup', filterPemuda);
                searchInput.addEventListener('search', filterPemuda);
            }
            if (searchBtn) searchBtn.addEventListener('click', filterPemuda);

            const modalSemuaPemuda = document.getElementById('modalSemuaPemuda');
            if (modalSemuaPemuda) {
                modalSemuaPemuda.addEventListener('show.bs.modal', function() {
                    if (searchInput) {
                        searchInput.value = '';
                        filterPemuda();
                    }
                });
            }

            // ===== KATEGORI FILTER =====
            const categoryCards = document.querySelectorAll('.category-card');
            const prestasiWrappers = document.querySelectorAll('.prestasi-item-card-wrapper');
            const resultsCard = document.getElementById('categoryResultsCard');
            const resultsEmpty = document.getElementById('categoryResultsEmpty');
            const selectedCategoryName = document.getElementById('selectedCategoryName');
            const resultCountLabel = document.getElementById('resultCountLabel');
            const resetBtn = document.getElementById('resetCategoryFilter');
            
            // Search dalam kategori
            const searchPrestasi = document.getElementById('searchPrestasiKategori');
            const btnSearchPrestasi = document.getElementById('btnSearchPrestasiKategori');

            let currentCategoryId = null;
            let currentCategoryName = '';

            function filterByCategory(categoryId, categoryName) {
                currentCategoryId = categoryId;
                currentCategoryName = categoryName;

                // Highlight active category
                categoryCards.forEach(card => {
                    const cardId = card.dataset.categoryFilter;
                    card.classList.toggle('active', cardId === categoryId);
                });

                // Filter prestasi berdasarkan kategori
                let resultCount = 0;
                prestasiWrappers.forEach(item => {
                    const itemCategory = item.dataset.categoryResult;
                    // Hanya tampilkan jika kategori sesuai
                    const matches = itemCategory === categoryId;
                    item.classList.toggle('is-hidden', !matches);
                    if (matches) resultCount++;
                });

                // Update header
                if (selectedCategoryName) {
                    selectedCategoryName.textContent = categoryName;
                }
                if (resultCountLabel) {
                    resultCountLabel.textContent = resultCount + ' data';
                }

                // Show/hide empty
                if (resultsEmpty) {
                    resultsEmpty.style.display = resultCount === 0 ? 'block' : 'none';
                }

                // Show card with animation
                if (resultsCard) {
                    resultsCard.classList.add('active');
                    resultsCard.style.display = 'block';
                    // Reset search
                    if (searchPrestasi) {
                        searchPrestasi.value = '';
                        filterPrestasiKategori();
                    }
                    // Scroll ke results
                    setTimeout(() => {
                        resultsCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 300);
                }
            }

            // Filter prestasi dalam kategori berdasarkan search (hanya mencari di kategori yang aktif)
            function filterPrestasiKategori() {
                const keyword = searchPrestasi.value.toLowerCase().trim();
                let visibleCount = 0;

                prestasiWrappers.forEach(item => {
                    // Cek apakah item termasuk dalam kategori yang aktif
                    const itemCategory = item.dataset.categoryResult;
                    
                    // Jika tidak sesuai kategori, sembunyikan
                    if (itemCategory !== currentCategoryId) {
                        item.classList.add('is-hidden');
                        return;
                    }

                    // Jika sesuai kategori, cek search keyword
                    const searchData = item.dataset.search || '';
                    const matches = keyword === '' || searchData.includes(keyword);
                    item.classList.toggle('is-hidden', !matches);
                    if (matches) visibleCount++;
                });

                // Update count
                if (resultCountLabel) {
                    resultCountLabel.textContent = visibleCount + ' data';
                }

                // Show/hide empty
                if (resultsEmpty) {
                    resultsEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            }

            // Click handler for category cards
            categoryCards.forEach(card => {
                card.addEventListener('click', function() {
                    const categoryId = this.dataset.categoryFilter;
                    const categoryName = this.dataset.categoryName;
                    filterByCategory(categoryId, categoryName);
                });
            });

            // Search handler
            if (searchPrestasi) {
                searchPrestasi.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') {
                        filterPrestasiKategori();
                    }
                });
            }
            if (btnSearchPrestasi) {
                btnSearchPrestasi.addEventListener('click', filterPrestasiKategori);
            }

            // Reset filter
            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    // Remove active class from all category cards
                    categoryCards.forEach(card => card.classList.remove('active'));
                    
                    // Hide results card
                    if (resultsCard) {
                        resultsCard.classList.remove('active');
                        resultsCard.style.display = 'none';
                    }
                    
                    // Reset search
                    if (searchPrestasi) {
                        searchPrestasi.value = '';
                    }
                    
                    // Reset all items to hidden
                    prestasiWrappers.forEach(item => item.classList.add('is-hidden'));
                    
                    currentCategoryId = null;
                    currentCategoryName = '';

                    // Scroll back to kategori section
                    document.getElementById('kategori').scrollIntoView({ behavior: 'smooth' });
                });
            }
        });

    </script>
</body>
</html>
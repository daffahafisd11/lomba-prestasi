<?php
require_once 'config/database.php';

// Hapus semua session
session_destroy();

// Redirect ke halaman login
header("Location: pages/login.php");
exit();
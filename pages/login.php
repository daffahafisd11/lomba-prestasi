<?php
require_once '../config/database.php';
require_once '../functions/helpers.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = sanitize($_POST['password']);
    
    // Hash password MD5 (sesuai database)
    $hashed_password = md5($password);
    
    // Gunakan prepared statement untuk mencegah SQL Injection
    $stmt = mysqli_prepare($conn, "SELECT * FROM tb_user WHERE username = ? AND password = ?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        redirect('dashboard.php');
    } else {
        $error = 'Username atau password salah!';
    }
    
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pemuda Berprestasi</title>
    <link rel="icon" type="image/png" href="../assets/img/icon-1.png" sizes="32x32">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-image: url('../assets/img/background-login.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            margin: 0;
            padding: 20px 0;
        }
        
        .login-card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1),
                        inset 0 1px 0 rgba(255, 255, 255, 0.4);
        }
        
        .login-header {
            background: linear-gradient(135deg, #8BA888, #6B8F71);
            padding: 30px;
            color: white;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .login-body {
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .login-footer {
            padding: 20px 40px;
            background: rgba(255, 255, 255, 0.05);
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .login-logo {
            margin-bottom: 15px;
        }
        
        .login-logo img {
            max-width: 150px;
            height: auto;
            display: block;
            margin: 0 auto;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }
        
        .form-control-lg {
            padding: 12px 15px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }
        
        .form-control-lg:focus {
            background: rgba(255, 255, 255, 0.9);
            border-color: #8BA888;
            box-shadow: 0 0 0 0.25rem rgba(107, 143, 113, 0.25);
        }
        
        .input-group-text {
            border-radius: 10px 0 0 10px;
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
            color: #4a674f;
        }
        
        .btn-login {
            padding: 12px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #8BA888, #6B8F71);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            box-shadow: 0 4px 15px rgba(107, 143, 113, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #7a9a77, #5a7f60);
            box-shadow: 0 8px 25px rgba(107, 143, 113, 0.4);
            color: white;
        }
        
        .btn-login:active {
            transform: translateY(0px);
        }
        
        .form-label {
            color: #2d3d30;
            font-weight: 600;
        }
        
        .login-header h4 {
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .login-header p {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .alert {
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .alert-danger {
            color: #721c24;
            border-color: rgba(220, 53, 69, 0.3);
        }
        
        /* ===== COPYRIGHT DI LUAR CARD (RAPI) ===== */
        .copyright {
            text-align: center;
            margin-top: 30px;
            padding-bottom: 20px;
            color: rgba(0, 0, 0, 0.6);
            font-size: 0.8rem;
        }
        
        .copyright strong {
            color: rgba(0, 0, 0, 0.6);
        }
        
        /* ===== LINK KEMBALI KE BERANDA ===== */
        .login-footer a {
            color: #6B8F71;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .login-footer a:hover {
            color: #5A7A5F;
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-body {
                padding: 25px;
            }
            
            .login-header {
                padding: 20px;
            }
            
            .login-footer {
                padding: 15px 20px;
            }
            
            .login-logo img {
                max-width: 100px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card">
                    <!-- Header -->
                    <div class="login-header">
                        <!-- Logo tanpa bundaran -->
                        <div class="login-logo">
                            <img src="../assets/img/logo.png" alt="Logo Pemuda Berprestasi">
                        </div>
                        <h4 class="fw-bold mb-1">Pemuda Berprestasi</h4>
                        <p class="mb-0 opacity-75">Login</p>
                    </div>
                    
                    <!-- Body -->
                    <div class="login-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" 
                                            class="form-control form-control-lg" 
                                            id="username" 
                                            name="username" 
                                            placeholder="Masukkan username" 
                                            required 
                                            autofocus>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" 
                                            class="form-control form-control-lg" 
                                            id="password" 
                                            name="password" 
                                            placeholder="Masukkan password" 
                                            required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-login w-100">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>
                        </form>
                    </div>
                    
                    <!-- Footer -->
                    <div class="login-footer">
                        <a href="../index.php" class="text-decoration-none small">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
                
                <!-- Copyright di luar card -->
                <div class="copyright">
                    <p class="mb-0">&copy; <?= date('Y') ?> Pemuda Berprestasi - Made by <strong>Daffa Hafisd P</strong></p>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
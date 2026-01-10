<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";
$success = "";

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $success = "Anda telah logout!";
}
if (isset($_GET['registered']) && $_GET['registered'] == 1) {
    $success = "Registrasi berhasil! Silakan login.";
}
if (isset($_GET['expired']) && $_GET['expired'] == 1) {
    $error = "Session telah expired. Silakan login kembali.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = trim($_POST['username'] ?? ''); 
    $password = $_POST['password'] ?? '';
    
    if (empty($input) || empty($password)) {
        $error = "Username/email dan password harus diisi!";
    } else {
        try {
            require_once 'config/database.php';
            $database = new Database();
            $db = $database->getConnection();
            
            // Query dengan prepared statement
            $query = "SELECT id, username, password FROM users WHERE username = ? OR email = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$input, $input]); 
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verifikasi password
                if (password_verify($password, $user['password'])) {
            
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['login_time'] = time();
                    
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Password salah!";
                }
            } else {
                $error = "Username atau email tidak ditemukan!";
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Daftar Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="text-center mb-4">
            <h3 class="text-primary">🛒 Daftar Belanja</h3>
            <p class="text-muted">Aplikasi Manajemen Belanja Sederhana</p>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center mb-4">Masuk ke Akun</h4>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Username atau Email</label>
                        <input type="text" class="form-control" name="username" required 
                               placeholder="Masukkan username atau email"
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required 
                               placeholder="Masukkan password">
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p class="mb-0">Belum punya akun?</p>
                    <a href="register.php" class="btn btn-outline-primary mt-2">
                        Daftar Akun Baru
                    </a>
                </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
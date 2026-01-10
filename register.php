<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'config/database.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validasi input
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Cek duplikasi username/email
            $checkQuery = "SELECT id FROM users WHERE username = ? OR email = ?";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->execute([$username, $email]);
            
            if ($checkStmt->rowCount() > 0) {
                $error = "Username atau email sudah terdaftar!";
            } else {

                // Hash password dengan bcrypt
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert ke database
                $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                $stmt = $db->prepare($query);
                
                if ($stmt->execute([$username, $email, $hashed_password])) {
                    header("Location: login.php?registered=1");
                    exit();
                }
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
    <title>Register - Daftar Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-box {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="register-box">
        <div class="text-center mb-4">
            <h3 class="text-primary">🛒 Daftar Belanja</h3>
            <p class="text-muted">Buat akun baru untuk mulai</p>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center mb-4">Daftar Akun Baru</h4>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required 
                               placeholder="Pilih username">
                        <small class="text-muted">Minimal 3 karakter</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required 
                               placeholder="nama@contoh.com">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required 
                               placeholder="Minimal 6 karakter">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" name="confirm_password" required 
                               placeholder="Ketik ulang password">
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            Daftar Sekarang
                        </button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p class="mb-0">Sudah punya akun?</p>
                    <a href="login.php" class="btn btn-outline-secondary mt-2">
                        Kembali ke Login
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
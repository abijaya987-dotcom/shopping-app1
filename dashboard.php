<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';

$username = $_SESSION['username'] ?? 'Pengguna';
$stats = ['total' => 0, 'belum' => 0, 'sudah' => 0];

try {
    $database = new Database();
    $db = $database->getConnection();
        https://github.com/abijaya987-dotcom/shopping-app
    if ($db) {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN is_purchased = 0 THEN 1 ELSE 0 END) as belum,
            SUM(CASE WHEN is_purchased = 1 THEN 1 ELSE 0 END) as sudah
            FROM shopping_items WHERE user_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$user_id]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$stats) {
            $stats = ['total' => 0, 'belum' => 0, 'sudah' => 0];
        }
    }
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Daftar Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f5; padding-top: 20px; }
        .card { border-radius: 8px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-4">
            <h3 class="text-primary">📝 Daftar Belanja</h3>
            <p class="text-muted">List Belanja Agar Tidak Lupa</p>
            <hr>
        </div>

        <div class="alert alert-info">
            <strong>Halo <?php echo htmlspecialchars($username); ?>!</strong> 
            Selamat datang di aplikasi daftar belanja.
        </div>

        <div class="row mb-3">
            <div class="col-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5><?php echo $stats['total']; ?></h5>
                        <small>Total</small>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-warning"><?php echo $stats['belum']; ?></h5>
                        <small>Belum</small>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-success"><?php echo $stats['sudah']; ?></h5>
                        <small>Sudah</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6>Menu:</h6>
                <div class="d-grid gap-2">
                    <a href="add-item.php" class="btn btn-primary">➕ Tambah Barang</a>
                    <a href="items.php" class="btn btn-outline-primary">📋 Lihat Daftar</a>
                    <a href="logout.php" class="btn btn-outline-danger">🚪 Logout</a>
                </div>
            </div>
        </div>
        
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
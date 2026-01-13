<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';

$error = "";
$success = "";
$is_edit = false;
$item_data = null;
$item_id = 0;

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $item_id = $_GET['edit'];
    $user_id = $_SESSION['user_id'];
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT * FROM shopping_items WHERE id = ? AND user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$item_id, $user_id]);
        
        if ($stmt->rowCount() == 1) {
            $item_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $is_edit = true;
        } else {
            header("Location: items.php");
            exit();
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = trim($_POST['item_name'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');
    $category = $_POST['category'] ?? 'food';
    $notes = trim($_POST['notes'] ?? '');
    $user_id = $_SESSION['user_id'];
    
    $is_edit_form = isset($_POST['item_id']) && is_numeric($_POST['item_id']);
    $item_id_form = $is_edit_form ? $_POST['item_id'] : 0;
    
    if (empty($item_name)) {
        $error = "Nama barang harus diisi!";
    } else {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($is_edit_form) {
                $query = "UPDATE shopping_items SET item_name = ?, quantity = ?, category = ?, notes = ? WHERE id = ? AND user_id = ?";
                $stmt = $db->prepare($query);
                
                if ($stmt->execute([$item_name, $quantity, $category, $notes, $item_id_form, $user_id])) {
                    $success = "Barang berhasil diupdate!";
                    $is_edit = true;
                }
            } else {
                $query = "INSERT INTO shopping_items (user_id, item_name, quantity, category, notes) VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                
                if ($stmt->execute([$user_id, $item_name, $quantity, $category, $notes])) {
                    $success = "Barang berhasil ditambahkan ke daftar!";
                    $item_name = $quantity = $notes = '';
                    $category = 'food';
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
    <title><?php echo $is_edit ? 'Edit Barang' : 'Tambah Barang'; ?> - Daftar Belanja</title>
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
            <p class="text-muted"><?php echo $is_edit ? 'Edit barang belanja' : 'Tambah Barang Baru'; ?></p>
            <hr>
        </div>

        <div class="mb-3">
            <a href="items.php" class="btn btn-sm btn-outline-secondary">← Kembali ke Daftar</a>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <?php echo $is_edit ? '✎ Edit Barang' : '➕ Tambah Barang'; ?>
                </h5>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">❌ <?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">✅ <?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <?php if ($is_edit && isset($item_data['id'])): ?>
                        <input type="hidden" name="item_id" value="<?php echo $item_data['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Barang *</label>
                        <input type="text" class="form-control" name="item_name" 
                               value="<?php echo isset($item_data['item_name']) ? htmlspecialchars($item_data['item_name']) : ($_POST['item_name'] ?? ''); ?>" 
                               required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="text" class="form-control" name="quantity" 
                                   value="<?php echo isset($item_data['quantity']) ? htmlspecialchars($item_data['quantity']) : ($_POST['quantity'] ?? ''); ?>" 
                                   placeholder="">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select class="form-control" name="category">
                                <option value="food" <?php echo (isset($item_data['category']) && $item_data['category'] == 'food') || ($_POST['category'] ?? 'food') == 'food' ? 'selected' : ''; ?>>🍔 Makanan</option>
                                <option value="drinks" <?php echo (isset($item_data['category']) && $item_data['category'] == 'drinks') || ($_POST['category'] ?? '') == 'drinks' ? 'selected' : ''; ?>>🥤 Minuman</option>
                                <option value="toiletries" <?php echo (isset($item_data['category']) && $item_data['category'] == 'toiletries') || ($_POST['category'] ?? '') == 'toiletries' ? 'selected' : ''; ?>>🧼 Toilet</option>
                                <option value="cleaning" <?php echo (isset($item_data['category']) && $item_data['category'] == 'cleaning') || ($_POST['category'] ?? '') == 'cleaning' ? 'selected' : ''; ?>>🧹 Pembersih Rumah</option>
                                <option value="others" <?php echo (isset($item_data['category']) && $item_data['category'] == 'others') || ($_POST['category'] ?? '') == 'others' ? 'selected' : ''; ?>>📦 Lainnya</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea class="form-control" name="notes" rows="2"><?php echo isset($item_data['notes']) ? htmlspecialchars($item_data['notes']) : ($_POST['notes'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $is_edit ? '💾 Update' : '✅ Simpan'; ?>
                        </button>
                        <a href="items.php" class="btn btn-outline-secondary">❌ Batal</a>
                    </div>
                </form>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
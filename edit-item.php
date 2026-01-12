<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
$error = "";
$success = "";
$is_edit = false;
$item_data = null;

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $item_id = $_GET['edit'];
    $user_id = $_SESSION['user_id'];
    
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
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = trim($_POST['item_name']);
    $quantity = trim($_POST['quantity']);
    $category = $_POST['category'];
    $notes = trim($_POST['notes']);
    $user_id = $_SESSION['user_id'];
    
    if (empty($item_name)) {
        $error = "Item name is required!";
    } else {
        try {
            if ($is_edit && isset($_POST['item_id'])) {
                $item_id = $_POST['item_id'];
                $query = "UPDATE shopping_items SET item_name = ?, quantity = ?, category = ?, notes = ? WHERE id = ? AND user_id = ?";
                $stmt = $db->prepare($query);
                
                if ($stmt->execute([$item_name, $quantity, $category, $notes, $item_id, $user_id])) {
                    $success = "Item updated successfully!";
                }
            } else {
                $query = "INSERT INTO shopping_items (user_id, item_name, quantity, category, notes) VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                
                if ($stmt->execute([$user_id, $item_name, $quantity, $category, $notes])) {
                    $success = "Item added to shopping list!";
                }
            }
        } catch(PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="row mb-3">
    <div class="col-12">
        <a href="items.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Shopping List
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header <?php echo $is_edit ? 'bg-warning text-dark' : 'bg-primary text-white'; ?>">
                <h4 class="mb-0">
                    <i class="bi bi-<?php echo $is_edit ? 'pencil' : 'cart-plus'; ?>"></i>
                    <?php echo $is_edit ? 'Edit Item' : 'Add Shopping Item'; ?>
                </h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <br>
                        <a href="items.php" class="btn btn-sm btn-success mt-2">
                            <i class="bi bi-list-check"></i> Lihat Shopping List
                        </a>
                        <a href="add-item.php" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="bi bi-plus-circle"></i> Tambah Lainnya
                        </a>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="item_id" value="<?php echo $item_data['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Items *</label>
                        <input type="text" class="form-control" name="item_name" 
                               value="<?php echo $is_edit ? htmlspecialchars($item_data['item_name']) : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kuantitas</label>
                        <input type="text" class="form-control" name="quantity" 
                               value="<?php echo $is_edit ? htmlspecialchars($item_data['quantity']) : ''; ?>" 
                               placeholder="e.g., 2 kg, 5 pieces, 1 pack">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-control" name="category">
                            <option value="food" <?php echo ($is_edit && $item_data['category'] == 'food') ? 'selected' : ''; ?>>Makanan</option>
                            <option value="drinks" <?php echo ($is_edit && $item_data['category'] == 'drinks') ? 'selected' : ''; ?>>Minuman</option>
                            <option value="toiletries" <?php echo ($is_edit && $item_data['category'] == 'toiletries') ? 'selected' : ''; ?>>Perlengkapan Mandi</option>
                            <option value="cleaning" <?php echo ($is_edit && $item_data['category'] == 'cleaning') ? 'selected' : ''; ?>>Perlengkapan Kebersihan</option>
                            <option value="others" <?php echo ($is_edit && $item_data['category'] == 'others') ? 'selected' : ''; ?>>Lainnya</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan (optional)</label>
                        <textarea class="form-control" name="notes" rows="2" 
                                  placeholder="Brand, specific type, etc."><?php echo $is_edit ? htmlspecialchars($item_data['notes']) : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn <?php echo $is_edit ? 'btn-warning' : 'btn-primary'; ?>">
                        <i class="bi bi-<?php echo $is_edit ? 'save' : 'cart-plus'; ?>"></i>
                        <?php echo $is_edit ? 'Update Item' : 'Add to List'; ?>
                    </button>
                    <a href="items.php" class="btn btn-secondary">Cancel</a>
                    
                    <?php if ($is_edit && $item_data['is_purchased']): ?>
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle"></i> Barang ini ditandai sebagai sudah dibeli. Pengeditan tidak akan mengubah status pembelian.
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <?php if (!$is_edit): ?>
        <div class="card mt-3">
            <div class="card-body">
                <h6><i class="bi bi-lightbulb"></i> Contoh:</h6>
                <ul class="mb-0">
                    <li><strong>Item:</strong> Susu | <strong>Kuantitas:</strong> 2 liter | <strong>Kategori:</strong> Makanan</li>
                    <li><strong>Item:</strong> Pasta Gigi | <strong>Kuantitas:</strong> 1 tabung | <strong>Kategori:</strong> Toiletries</li>
                    <li><strong>Item:</strong> Deterjen Laundry | <strong>Kuantitas:</strong> 1 botol | <strong>Kategori:</strong> Pembersihan</li>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
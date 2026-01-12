<?php
require_once 'includes/auth-check.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
$user_id = $_SESSION['user_id'];

if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $item_id = $_GET['toggle'];
    $checkQuery = "SELECT is_purchased FROM shopping_items WHERE id = ? AND user_id = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([$item_id, $user_id]);
    
    if ($checkStmt->rowCount() == 1) {
        $item = $checkStmt->fetch(PDO::FETCH_ASSOC);
        $new_status = $item['is_purchased'] ? 0 : 1;
        
        $updateQuery = "UPDATE shopping_items SET is_purchased = ? WHERE id = ?";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->execute([$new_status, $item_id]);
    }
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $item_id = $_GET['delete'];
    $checkQuery = "SELECT id FROM shopping_items WHERE id = ? AND user_id = ?";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([$item_id, $user_id]);
    
    if ($checkStmt->rowCount() == 1) {
        $deleteQuery = "DELETE FROM shopping_items WHERE id = ?";
        $deleteStmt = $db->prepare($deleteQuery);
        $deleteStmt->execute([$item_id]);
    }
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$query = "SELECT * FROM shopping_items WHERE user_id = ?";

if ($filter == 'active') {
    $query .= " AND is_purchased = 0";
} elseif ($filter == 'purchased') {
    $query .= " AND is_purchased = 1";
}

$query .= " ORDER BY is_purchased, category, item_name";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$query_all = "SELECT COUNT(*) as count FROM shopping_items WHERE user_id = ?";
$stmt_all = $db->prepare($query_all);
$stmt_all->execute([$user_id]);
$total_items = $stmt_all->fetch(PDO::FETCH_ASSOC)['count'];

$query_active = "SELECT COUNT(*) as count FROM shopping_items WHERE user_id = ? AND is_purchased = 0";
$stmt_active = $db->prepare($query_active);
$stmt_active->execute([$user_id]);
$active_items = $stmt_active->fetch(PDO::FETCH_ASSOC)['count'];

$query_purchased = "SELECT COUNT(*) as count FROM shopping_items WHERE user_id = ? AND is_purchased = 1";
$stmt_purchased = $db->prepare($query_purchased);
$stmt_purchased->execute([$user_id]);
$purchased_items = $stmt_purchased->fetch(PDO::FETCH_ASSOC)['count'];
?>

<?php include 'includes/header.php'; ?>

<div class="mb-3">
    <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">← Kembali</a>
    <a href="add-item.php" class="btn btn-sm btn-primary float-end">+ Baru</a>
</div>

<div class="btn-group w-100 mb-3">
    <a href="items.php?filter=all" class="btn <?php echo $filter == 'all' ? 'btn-primary' : 'btn-outline-primary'; ?>">
        Semua (<?php echo $total_items; ?>)
    </a>
    <a href="items.php?filter=active" class="btn <?php echo $filter == 'active' ? 'btn-warning' : 'btn-outline-warning'; ?>">
        Belum (<?php echo $active_items; ?>)
    </a>
    <a href="items.php?filter=purchased" class="btn <?php echo $filter == 'purchased' ? 'btn-success' : 'btn-outline-success'; ?>">
        Sudah (<?php echo $purchased_items; ?>)
    </a>
</div>

<?php if (count($items) == 0): ?>
    <div class="alert alert-warning text-center">
        <p class="mb-0">Daftar masih kosong</p>
        <a href="add-item.php" class="btn btn-sm btn-primary mt-2">Tambah barang pertama</a>
    </div>
<?php else: ?>
    <?php foreach ($items as $item): ?>
    <div class="card <?php echo $item['category']; ?>">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 <?php echo $item['is_purchased'] ? 'purchased' : ''; ?>">
                        <?php echo htmlspecialchars($item['item_name']); ?>
                        <?php if ($item['quantity']): ?>
                            <small class="text-muted">(<?php echo htmlspecialchars($item['quantity']); ?>)</small>
                        <?php endif; ?>
                    </h6>
                    <?php if ($item['notes']): ?>
                        <small class="text-muted"><?php echo htmlspecialchars($item['notes']); ?></small>
                    <?php endif; ?>
                </div>
                
                <div class="btn-group">
                    <a href="items.php?toggle=<?php echo $item['id']; ?>&filter=<?php echo $filter; ?>" 
                       class="btn btn-sm <?php echo $item['is_purchased'] ? 'btn-success' : 'btn-outline-secondary'; ?>">
                        <?php echo $item['is_purchased'] ? '✓' : '○'; ?>
                    </a>
                    <?php if (!$item['is_purchased']): ?>
                    <a href="add-item.php?edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary">✎</a>
                    <?php endif; ?>
                    <a href="items.php?delete=<?php echo $item['id']; ?>&filter=<?php echo $filter; ?>" 
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Hapus?')">🗑️</a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <div class="alert alert-light text-center mt-3">
        <small>
            Total: <?php echo count($items); ?> barang | 
            <a href="add-item.php">Tambah lagi</a>
        </small>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
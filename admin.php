<?php
session_start();
include 'db.php';

// Auth guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Optional: show logged-in admin name
$current_admin = $_SESSION['admin_username'] ?? 'Admin';

// handle add item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = (float) $_POST['price'];
    $quantity    = (int)   $_POST['quantity'];
    $image       = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        $ext         = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $safe_name   = uniqid('item_') . '.' . $ext;
        $target_file = $target_dir . $safe_name;
        if (getimagesize($_FILES['image']['tmp_name']) !== false) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $safe_name;
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO items (name, description, price, quantity, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $name, $description, $price, $quantity, $image);
    $stmt->execute();
    $stmt->close();
    $_SESSION['admin_msg'] = ['type' => 'success', 'text' => "Item <strong>$name</strong> added successfully."];
    header("Location: admin.php");
    exit();
}

// handle edit items
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_item'])) {
    $id          = (int)   $_POST['item_id'];
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price       = (float) $_POST['price'];
    $quantity    = (int)   $_POST['quantity'];

    // Get current image
    $stmt = $conn->prepare("SELECT image FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row   = $stmt->get_result()->fetch_assoc();
    $image = $row['image'];
    $stmt->close();

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
        $ext         = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $safe_name   = uniqid('item_') . '.' . $ext;
        $target_file = $target_dir . $safe_name;
        if (getimagesize($_FILES['image']['tmp_name']) !== false) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $safe_name;
            }
        }
    }

    $stmt->bind_param("ssdisi", $name, $description, $price, $quantity, $image, $id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['admin_msg'] = ['type' => 'success', 'text' => "Item <strong>$name</strong> updated successfully."];
    header("Location: admin.php");
    exit();
}

// ── Handle Delete ────────────────────────────────────────────
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id   = (int) $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['admin_msg'] = ['type' => 'danger', 'text' => 'Item deleted.'];
    header("Location: admin.php");
    exit();
}

// ── Handle Logout ────────────────────────────────────────────
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// ── Fetch Items ──────────────────────────────────────────────
$items = $conn->query("SELECT * FROM items ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

// ── Fetch item for edit modal ────────────────────────────────
$edit_item = null;
if (isset($_GET['edit_id'])) {
    $eid  = (int) $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $edit_item = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – Sample Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: #ff5722;
            --brand-dark: #e64a19;
            --brand-light: #fff3f0;
            --sidebar-bg: #111;
            --sidebar-w: 230px;
            --bg: #f4f4f4;
            --card: #fff;
            --border: #e8e8e8;
            --text: #1a1a1a;
            --muted: #888;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            padding: 24px 0;
            display: flex;
            flex-direction: column;
            z-index: 200;
            box-shadow: 4px 0 24px rgba(0,0,0,.2);
        }
        .sidebar-brand {
            padding: 0 20px 24px;
            border-bottom: 1px solid #222;
        }
        .sidebar-brand .icon {
            width: 36px; height: 36px;
            background: var(--brand);
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: .9rem;
            margin-bottom: 10px;
        }
        .sidebar-brand h2 {
            font-size: .95rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.1;
        }
        .sidebar-brand small {
            font-size: .65rem;
            color: #555;
            letter-spacing: .1em;
            text-transform: uppercase;
        }
        .sidebar-nav { padding: 20px 12px; flex: 1; }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            color: #aaa;
            text-decoration: none;
            font-size: .88rem;
            font-weight: 500;
            transition: all .2s;
            margin-bottom: 4px;
        }
        .nav-item:hover, .nav-item.active {
            background: rgba(255,87,34,.15);
            color: var(--brand);
        }
        .nav-item i { width: 18px; text-align: center; font-size: .85rem; }
        .sidebar-footer { padding: 16px 12px; border-top: 1px solid #222; }
        .btn-logout {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 9px 12px;
            border-radius: 8px;
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.2);
            color: #f87171;
            font-size: .83rem;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-logout:hover { background: rgba(239,68,68,.2); color: #f87171; }

        /* ── Main Content ── */
        .main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
        }
        .topbar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topbar h1 {
            font-size: 1.15rem;
            font-weight: 800;
        }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .btn-add-new {
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 9px 18px;
            font-size: .82rem;
            font-weight: 700;
            letter-spacing: .04em;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: background .2s, transform .15s;
            box-shadow: 0 2px 10px rgba(255,87,34,.25);
        }
        .btn-add-new:hover { background: var(--brand-dark); transform: translateY(-1px); }

        .content { padding: 28px 32px; }

        /* ── Stats ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px 22px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .stat-icon.orange { background: rgba(255,87,34,.12); color: var(--brand); }
        .stat-icon.blue   { background: rgba(59,130,246,.12); color: #3b82f6; }
        .stat-icon.green  { background: rgba(34,197,94,.12);  color: #22c55e; }
        .stat-val { font-size: 1.5rem; font-weight: 800; line-height: 1; }
        .stat-lbl { font-size: .75rem; color: var(--muted); margin-top: 3px; }

        /* ── Items Table ── */
        .table-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }
        .table-card table { width: 100%; border-collapse: collapse; }
        .table-card thead th {
            background: #fafafa;
            padding: 13px 18px;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
        }
        .table-card tbody tr {
            transition: background .15s;
            border-bottom: 1px solid var(--border);
        }
        .table-card tbody tr:last-child { border-bottom: none; }
        .table-card tbody tr:hover { background: #fafafa; }
        .table-card tbody td {
            padding: 14px 18px;
            font-size: .88rem;
            vertical-align: middle;
        }
        .item-thumb {
            width: 44px; height: 44px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border);
        }
        .item-thumb-ph {
            width: 44px; height: 44px;
            border-radius: 8px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            font-size: .85rem;
        }
        .item-name { font-weight: 600; }
        .item-desc { font-size: .78rem; color: var(--muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px; }
        .badge-stock {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 600;
        }
        .badge-stock.ok   { background: rgba(34,197,94,.12);  color: #16a34a; }
        .badge-stock.low  { background: rgba(245,158,11,.12); color: #d97706; }
        .badge-stock.zero { background: rgba(239,68,68,.12);  color: #dc2626; }

        .action-btns { display: flex; gap: 8px; }
        .btn-edit-row {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1.5px solid #f59e0b;
            background: rgba(245,158,11,.08);
            color: #d97706;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all .15s;
        }
        .btn-edit-row:hover { background: rgba(245,158,11,.18); color: #d97706; }
        .btn-del-row {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1.5px solid #ef4444;
            background: rgba(239,68,68,.08);
            color: #dc2626;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all .15s;
        }
        .btn-del-row:hover { background: rgba(239,68,68,.18); color: #dc2626; }

        /* ── Modals ── */
        .modal-content {
            border-radius: 16px;
            border: 1px solid var(--border);
        }
        .modal-header {
            border-bottom: 1px solid var(--border);
            padding: 18px 24px;
        }
        .modal-title { font-weight: 700; }
        .modal-body  { padding: 24px; }
        .modal-footer { border-top: 1px solid var(--border); padding: 16px 24px; }
        .form-label {
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            border: 1.5px solid var(--border);
            border-radius: 9px;
            font-family: 'DM Sans', sans-serif;
            font-size: .92rem;
            padding: 10px 13px;
            transition: border-color .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(255,87,34,.1);
        }
        .btn-primary {
            background: var(--brand);
            border-color: var(--brand);
            font-weight: 700;
            border-radius: 8px;
        }
        .btn-primary:hover { background: var(--brand-dark); border-color: var(--brand-dark); }


        .alert { border-radius: 10px; font-size: .88rem; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main    { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- ── SIDEBAR ── -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="icon"><i class="fas fa-shopping-bag"></i></div>
        <h2>Sample Shop</h2>
        <small>Admin Panel</small>
    </div>
    <nav class="sidebar-nav">
        <a href="admin.php" class="nav-item active"><i class="fas fa-boxes"></i> Products</a>
    </nav>
    <div class="sidebar-footer">
        <a href="admin.php?logout=1" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
    </div>
</aside>

<!-- ── MAIN ── -->
<div class="main">
    <div class="topbar">
        <h1>Product Management</h1>
        <div class="topbar-right">
            <button class="btn-add-new" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Add Item
            </button>
        </div>
    </div>

    <div class="content">

        <?php if (isset($_SESSION['admin_msg'])): $m = $_SESSION['admin_msg']; unset($_SESSION['admin_msg']); ?>
        <div class="alert alert-<?= $m['type'] ?> alert-dismissible fade show mb-4" role="alert">
            <?= $m['text'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Stats -->
        <?php
            $total_items = count($items);
            $total_stock = array_sum(array_column($items, 'quantity'));
            $total_value = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
        ?>
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-boxes"></i></div>
                <div>
                    <div class="stat-val"><?= $total_items ?></div>
                    <div class="stat-lbl">Total Products</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-layer-group"></i></div>
                <div>
                    <div class="stat-val"><?= $total_stock ?></div>
                    <div class="stat-lbl">Total Stock Units</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-peso-sign"></i></div>
                <div>
                    <div class="stat-val">₱<?= number_format($total_value, 0) ?></div>
                    <div class="stat-lbl">Inventory Value</div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th style="width:52px"></th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?php if ($item['image']): ?>
                                <img src="uploads/<?= htmlspecialchars($item['image']) ?>" class="item-thumb" alt="">
                            <?php else: ?>
                                <div class="item-thumb-ph"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="item-desc"><?= htmlspecialchars($item['description']) ?></div>
                        </td>
                        <td><strong>₱<?= number_format($item['price'], 2) ?></strong></td>
                        <td>
                            <?php
                                $q = $item['quantity'];
                                $cls = $q == 0 ? 'zero' : ($q <= 5 ? 'low' : 'ok');
                                $lbl = $q == 0 ? 'Out of Stock' : "$q units";
                            ?>
                            <span class="badge-stock <?= $cls ?>"><?= $lbl ?></span>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="admin.php?edit_id=<?= $item['id'] ?>" class="btn-edit-row"><i class="fas fa-pen"></i> Edit</a>
                                <a href="admin.php?delete=1&id=<?= $item['id'] ?>" class="btn-del-row" onclick="return confirm('Delete this item?')"><i class="fas fa-trash"></i> Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                    <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted);">No products yet. Add your first item!</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div><!-- /content -->
</div><!-- /main -->

<!-- ── ADD ITEM MODAL ── -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2" style="color:var(--brand)"></i> Add New Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Price (₱)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" required min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_item" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── EDIT ITEM MODAL ── -->
<?php if ($edit_item): ?>
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-pen me-2" style="color:#f59e0b"></i> Edit Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="item_id" value="<?= $edit_item['id'] ?>">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($edit_item['name']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Price (₱)</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?= $edit_item['price'] ?>" required min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="<?= $edit_item['quantity'] ?>" required min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($edit_item['description']) ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Image <span style="font-size:.7rem;color:var(--muted);text-transform:none;font-weight:400;">(leave empty to keep current)</span></label>
                            <?php if ($edit_item['image']): ?>
                                <div class="mb-2">
                                    <img src="uploads/<?= htmlspecialchars($edit_item['image']) ?>" style="height:70px;border-radius:8px;border:1px solid var(--border);">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_item" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(a => {
            try { new bootstrap.Alert(a).close(); } catch(e) {}
        });
    }, 4000);

    <?php if ($edit_item): ?>
    // Auto-open edit modal
    new bootstrap.Modal(document.getElementById('editModal')).show();
    <?php endif; ?>
</script>
</body>
</html>

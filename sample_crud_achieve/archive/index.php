<?php
session_start();
include 'db.php';

// Handle add item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_item'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = basename($_FILES["image"]["name"]);
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO items (name, description, price, quantity, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $name, $description, $price, $quantity, $image);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// Get items
$result = $conn->query("SELECT * FROM items");
$items = $result->fetch_all(MYSQLI_ASSOC);

// Handle edit modal
$edit_item = null;
if (isset($_GET['edit']) && isset($_GET['id'])) {
    $edit_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_item = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample CRUD - Shopee Style</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 25px;
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
            transition: all 0.4s ease;
            width: 100%;
            max-width: 280px;
            margin: 0 auto;
            overflow: hidden;
            background: white;
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
            border-color: #ff5722;
        }
        .product-img {
            height: 180px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-top-left-radius: 25px;
            border-top-right-radius: 25px;
            overflow: hidden;
        }
        .btn-add-cart {
            background: white;
            color: #ff5722;
            border: 2px solid #ff5722;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        .btn-add-cart:hover {
            background: #ff5722;
            color: white;
            transform: scale(1.05);
        }
        .btn-add-cart i {
            color: #ff5722;
        }
        .btn-add-cart:hover i {
            color: white;
        }
        .product-img img {
            transition: transform 0.4s ease;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-card:hover .product-img img {
            transform: scale(1.1);
        }
        .product-card .card-body {
            padding: 1rem;
            background: linear-gradient(to bottom, #ffffff, #f9f9f9);
        }
        .product-card .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }
        .product-card .card-text {
            font-size: 0.9rem;
            color: #666;
        }
        .navbar {
            background-color: #ff5722;
        }
        .navbar-brand {
            color: white !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-shopping-bag"></i> Sample Shop</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="cart.php"><i class="fas fa-shopping-bag"></i> Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addItemModal"><i class="fas fa-plus"></i> Add New Item</button>
            </div>
        </div>

        <div class="row">
            <?php foreach ($items as $item): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card product-card">
                    <div class="product-img">
                        <?php if ($item['image']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid" style="height: 180px; width: 100%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-image fa-3x text-muted"></i>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?>...</p>
                        <p class="text-success fw-bold">₱<?php echo number_format($item['price'], 2); ?></p>
                        <p class="text-muted">Stock: <?php echo $item['quantity']; ?></p>
                        <div class="d-flex justify-content-between">
                            <form action="add_to_cart.php" method="post" class="d-inline">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantity" value="1" min="1" max="<?php echo $item['quantity']; ?>" class="form-control d-inline" style="width: 60px;">
                                <button type="submit" class="btn btn-add-cart btn-sm ms-1"><i class="fas fa-cart-plus"></i> Add to Cart</button>
                            </form>
                        </div>
                        <div class="mt-2">
                            <a href="index.php?edit=1&id=<?php echo $item['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                            <a href="delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i> Delete</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_item" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <?php if ($edit_item): ?>
    <div class="modal fade" id="editItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" enctype="multipart/form-data" action="edit.php?id=<?php echo $edit_item['id']; ?>">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="edit_name" name="name" value="<?php echo htmlspecialchars($edit_item['name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_price" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="edit_price" name="price" value="<?php echo $edit_item['price']; ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="edit_quantity" name="quantity" value="<?php echo $edit_item['quantity']; ?>" required min="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_image" class="form-label">Image</label>
                                <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                                <small class="form-text text-muted">Leave empty to keep current image.</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="2"><?php echo htmlspecialchars($edit_item['description']); ?></textarea>
                        </div>
                        <?php if ($edit_item['image']): ?>
                        <div class="mb-3">
                            <label class="form-label">Current Image</label><br>
                            <img src="uploads/<?php echo htmlspecialchars($edit_item['image']); ?>" alt="Current Image" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alert after 3 seconds
        setTimeout(function() {
            var alertElement = document.querySelector('.alert');
            if (alertElement) {
                var bsAlert = new bootstrap.Alert(alertElement);
                bsAlert.close();
            }
        }, 3000);
    </script>
    <?php if ($edit_item): ?>
    <script>
        var editModal = new bootstrap.Modal(document.getElementById('editItemModal'));
        editModal.show();
    </script>
    <?php endif; ?>
</body>
</html>
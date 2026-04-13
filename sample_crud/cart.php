<?php
session_start();
include 'db.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart – Sample Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f5f5f5; }
        .navbar { background-color: #ff5722; }
        .navbar-brand { color: white !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-arrow-left"></i> Back to Shop</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2><i class="fas fa-shopping-bag"></i> Shopping Cart</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (empty($cart_items)): ?>
            <div class="text-center mt-5">
                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                <p class="text-muted">Your cart is empty.</p>
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="mb-3">
                <a href="cart.php?view=grid" class="btn btn-outline-primary me-2">Grid View</a>
                <a href="cart.php?view=list" class="btn btn-outline-primary">List View</a>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <?php if (isset($_GET['view']) && $_GET['view'] == 'list'): ?>
                    <!-- List View -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Image</th>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart_items as $index => $item): ?>
                                        <tr>
                                            <td>
                                                <?php if ($item['image']): ?>
                                                    <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>"
                                                         class="img-thumbnail"
                                                         style="width:60px;height:60px;object-fit:cover;">
                                                <?php else: ?>
                                                    <i class="fas fa-image fa-2x text-muted"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td class="fw-bold">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            <td>
                                                <a href="remove_from_cart.php?index=<?php echo $index; ?>"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Remove this item?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php $total += $item['price'] * $item['quantity']; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php else: ?>
                    <!-- Grid View -->
                    <div class="row">
                        <?php foreach ($cart_items as $index => $item): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-4">
                                            <?php if ($item['image']): ?>
                                                <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>"
                                                     class="img-fluid rounded"
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                                            <?php else: ?>
                                                <i class="fas fa-image fa-2x text-muted"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-8">
                                            <h6 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h6>
                                            <p class="card-text">₱<?php echo number_format($item['price'], 2); ?> × <?php echo $item['quantity']; ?></p>
                                            <p class="fw-bold">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                                            <a href="remove_from_cart.php?index=<?php echo $index; ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Remove this item?')">
                                                <i class="fas fa-trash"></i> Remove
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $total += $item['price'] * $item['quantity']; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Order Summary -->
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h5 class="card-title">Order Summary</h5>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Subtotal:</span>
                                <span>₱<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Shipping:</span>
                                <span class="text-success">Free</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span>₱<?php echo number_format($total, 2); ?></span>
                            </div>
                            <!-- UPDATED: links to checkout.php -->
                            <a href="checkout.php" class="btn btn-success w-100 mt-3">
                                <i class="fas fa-credit-card me-1"></i> Proceed to Checkout
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-store me-1"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(function() {
            var alertEl = document.querySelector('.alert');
            if (alertEl) new bootstrap.Alert(alertEl).close();
        }, 3500);
    </script>
</body>
</html>

<?php
session_start();
include 'db.php';

// Get items
$result = $conn->query("SELECT * FROM items");
$items  = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- bs js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <style>
        body { background-color: #f5f5f5; }

        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 25px;
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
            transition: all 0.4s ease;
            width: 100%;
            height: 500px;
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
        .btn-add-cart i { color: #ff5722; }
        .btn-add-cart:hover i { color: white; }
        .product-img img {
            transition: transform 0.4s ease;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-card:hover .product-img img { transform: scale(1.1); }
        .product-card .card-body {
            padding: 1rem;
            background: linear-gradient(to bottom, #ffffff, #f9f9f9);
        }
        .product-card .card-title { font-size: 1.1rem; font-weight: 600; color: #333; }
        .product-card .card-text  { font-size: 0.9rem; color: #666; }
        .navbar {
            background-color: #ff5722;
            height: 75px;
        }

        .carousel-item img {
            height: 400px;
            object-fit: contain;
            transform: translateY(10px);
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 50px;
            height: 50px;
            background-color: rgba(0,0,0,0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .carousel-control-prev {
            left: 350px;
            transform: translateY(160px);
        }

        .carousel-control-next {
            right: 350px;
            transform: translateY(160px);
        }
        .navbar-brand { color: white !important; }
        .admin-header {
            ali
            text-align: center;
            padding: 0px 15px 0px;
            font-size: .75rem;
            color: #ffffff;
            
        }
        .admin-header a {
            color: #ffffff;
            text-decoration: none;
            transition: color .2s;
            margin-top: -5px;
        }
        .admin-header a:hover { color: #afafaf; border-color: #afafaf; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-shopping-bag"></i> Sample Shop</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="cart.php">
                    <i class="fas fa-shopping-bag"></i> Cart
                    <span class="badge bg-white text-danger ms-1">
                        <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                    </span>
                            <div class="admin-header">
                            <a href="admin_login.php"><i class="fas fa-lock me-1"></i>Admin
                        </div>
                    </a>
                </a>
            </div>
        </div>
    </nav>
<!-- carousel -->
<div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
  
  <div class="carousel-inner">
    
    <div class="carousel-item active">
      <img src="uploads/slide1.png" class="d-block w-100" alt="Slide 1">
    </div>
    
    <div class="carousel-item">
      <img src="uploads/slide2.png" class="d-block w-100" alt="Slide 2">
    </div>
    
    <div class="carousel-item">
      <img src="uploads/slide 3.png" class="d-block w-100" alt="Slide 3">
    </div>
    
  </div>

  <!-- Controls -->
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>

  <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>

</div>

    <div class="container mt-4">

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= strpos($_SESSION['message'], 'Could not') !== false ? 'warning' : 'success' ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="row">
            <?php foreach ($items as $item): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card product-card">
                    <div class="product-img">
                        <?php if ($item['image']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>"
                                 alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <?php else: ?>
                            <i class="fas fa-image fa-3x text-muted"></i>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p class="card-text">
                            <?php echo htmlspecialchars(substr($item['description'], 0, 80)); ?>...
                        </p>
                        <p class="text-success fw-bold">₱<?php echo number_format($item['price'], 2); ?></p>
                        <p class="text-muted" style="font-size:.82rem;">
                            <?php if ($item['quantity'] > 0): ?>
                                <i class="fas fa-check-circle text-success me-1"></i> <?php echo $item['quantity']; ?> in stock
                            <?php else: ?>
                                <i class="fas fa-times-circle text-danger me-1"></i> Out of stock
                            <?php endif; ?>
                        </p>
                        <?php if ($item['quantity'] > 0): ?>
                        <form action="add_to_cart.php" method="post" class="d-flex align-items-center gap-2 mt-2">
                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1"
                                   max="<?php echo $item['quantity']; ?>"
                                   class="form-control form-control-sm" style="width:62px;">
                            <button type="submit" class="btn btn-add-cart btn-sm flex-grow-1">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                        <?php else: ?>
                        <button class="btn btn-secondary btn-sm w-100 mt-2" disabled>Out of Stock</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($items)): ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">No products available yet. Check back soon!</p>
            </div>
            <?php endif; ?>
        </div>
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

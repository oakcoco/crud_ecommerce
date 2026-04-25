<?php
session_start();
include 'db.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Redirect if cart is empty and not already on success page
$order_placed = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    // In a real app you'd save the order to DB here
    $_SESSION['cart'] = [];
    $_SESSION['order_name'] = htmlspecialchars($_POST['full_name']);
    $order_placed = true;
}

if (empty($cart_items) && !$order_placed && !isset($_SESSION['order_name'])) {
    header("Location: index.php");
    exit();
}

$just_ordered = $order_placed || isset($_SESSION['order_name']);
$order_name = $_SESSION['order_name'] ?? '';
if ($order_placed) {
    // keep name for display, will be cleared on next visit
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout – Sample Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand:       #ff5722;
            --brand-dark:  #e64a19;
            --brand-light: #fff3f0;
            --success:     #22c55e;
            --bg:          #fafafa;
            --card:        #ffffff;
            --border:      #ebebeb;
            --text:        #1a1a1a;
            --muted:       #888;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── Navbar ── */
        .top-bar {
            background: var(--brand);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(255,87,34,.25);
        }
        .top-bar .inner {
            max-width: 1100px;
            margin: auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .top-bar a {
            color: #fff;
            text-decoration: none;
            font-weight: 100;
            font-size: 1.15rem;
            letter-spacing: .5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .top-bar a:hover { opacity: .85; }
        .top-bar .breadcrumb-sep { color: rgba(255,255,255,.5); font-size: .85rem; }
        .top-bar .step-label { color: rgba(255,255,255,.9); font-size: .85rem; font-family: 'DM Sans', sans-serif; font-weight: 300; }

        /* ── Layout ── */
        .checkout-wrap {
            max-width: 1100px;
            margin: 48px auto 80px;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 32px;
            align-items: start;
        }
        @media (max-width: 820px) {
            .checkout-wrap { grid-template-columns: 1fr; }
        }

        /* ── Cards ── */
        .card-block {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px 32px;
            box-shadow: 0 2px 16px rgba(0,0,0,.04);
        }
        .card-block + .card-block { margin-top: 20px; }

        .section-title {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--brand);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--brand-light);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── Form ── */
        .form-label {
            font-size: .8rem;
            font-weight: 500;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 11px 14px;
            font-family: 'DM Sans', sans-serif;
            font-size: .95rem;
            transition: border-color .2s, box-shadow .2s;
            background: #fdfdfd;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(255,87,34,.12);
            outline: none;
        }

        /* Payment options */
        .payment-options { display: flex; gap: 12px; flex-wrap: wrap; }
        .pay-opt {
            flex: 1;
            min-width: 110px;
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 14px 10px;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
            background: #fdfdfd;
            position: relative;
        }
        .pay-opt input[type="radio"] { display: none; }
        .pay-opt:hover { border-color: var(--brand); background: var(--brand-light); }
        .pay-opt.selected { border-color: var(--brand); background: var(--brand-light); }
        .pay-opt.selected::after {
            content: '✓';
            position: absolute;
            top: 6px; right: 8px;
            font-size: .7rem;
            font-weight: 700;
            color: var(--brand);
        }
        .pay-opt i { font-size: 1.4rem; color: var(--brand); margin-bottom: 6px; display: block; }
        .pay-opt span { font-size: .78rem; font-weight: 500; color: var(--text); }

        /* ── Order Summary ── */
        .summary-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }
        .summary-item:last-child { border-bottom: none; }
        .summary-thumb {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid var(--border);
            flex-shrink: 0;
        }
        .summary-thumb-placeholder {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #ccc;
        }
        .summary-info { flex: 1; min-width: 0; }
        .summary-name { font-weight: 500; font-size: .92rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .summary-qty { font-size: .78rem; color: var(--muted); margin-top: 2px; }
        .summary-price { font-weight: 700; font-size: .92rem; color: var(--brand); flex-shrink: 0; }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            font-size: .9rem;
        }
        .totals-row.grand {
            font-size: 1.05rem;
            font-weight: 700;
            padding-top: 14px;
            border-top: 2px solid var(--border);
            margin-top: 4px;
        }
        .totals-row .label { color: var(--muted); }
        .totals-row.grand .label { color: var(--text); }
        .totals-row .value { font-weight: 600; }
        .totals-row.grand .value { color: var(--brand); font-size: 1.15rem; }

        /* ── Place Order Button ── */
        .btn-place-order {
            width: 100%;
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: .05em;
            cursor: pointer;
            transition: background .2s, transform .15s, box-shadow .2s;
            margin-top: 20px;
            box-shadow: 0 4px 14px rgba(255,87,34,.3);
        }
        .btn-place-order:hover {
            background: var(--brand-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255,87,34,.35);
        }
        .btn-place-order:active { transform: translateY(0); }

        /* ── Success State ── */
        .success-wrap {
            max-width: 520px;
            margin: 80px auto;
            padding: 0 24px;
            text-align: center;
        }
        .success-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--success), #16a34a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            box-shadow: 0 8px 32px rgba(34,197,94,.3);
            animation: popIn .5s cubic-bezier(.34,1.56,.64,1) both;
        }
        .success-icon i { font-size: 2.2rem; color: #fff; }
        @keyframes popIn {
            from { transform: scale(0); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }
        .success-wrap h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 12px;
        }
        .success-wrap p { color: var(--muted); font-size: 1rem; line-height: 1.6; margin-bottom: 32px; }
        .btn-back-shop {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--brand);
            color: #fff;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            box-shadow: 0 4px 18px rgba(255,87,34,.3);
            transition: all .2s;
        }
        .btn-back-shop:hover {
            background: var(--brand-dark);
            transform: translateY(-2px);
            color: #fff;
            box-shadow: 0 8px 24px rgba(255,87,34,.35);
        }
        .order-details-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 28px;
            text-align: left;
        }
        .order-ref {
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px;
        }
        .order-ref span { color: var(--brand); font-size: 1rem; display: block; margin-top: 2px; }

        /* ── Security note ── */
        .security-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: .75rem;
            color: var(--muted);
            margin-top: 14px;
        }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="inner">
        <a href="index.php"><i class="fas fa-shopping-bag"></i> Sample Shop</a>
        <?php if (!$just_ordered): ?>
        <span class="breadcrumb-sep">/</span>
        <span class="step-label">Checkout</span>
        <?php endif; ?>
    </div>
</div>

<?php if ($just_ordered): ?>
<!-- ── SUCCESS STATE ── -->
<?php
    $ref = strtoupper(substr(md5(time()), 0, 8));
    unset($_SESSION['order_name']);
?>
<div class="success-wrap">
    <div class="success-icon"><i class="fas fa-check"></i></div>
    <h1>Order Placed! 🎉</h1>
    <p>Thank you, <strong><?= htmlspecialchars($order_name) ?></strong>! Your order has been received and is being processed. You'll get a confirmation soon.</p>

    <div class="order-details-card">
        <div class="order-ref">Order Reference <span>#<?= $ref ?></span></div>
        <div style="margin-top:14px; font-size:.85rem; color:var(--muted); display:flex; flex-direction:column; gap:6px;">
            <div><i class="fas fa-truck" style="color:var(--brand);width:18px"></i> Estimated delivery: 3–5 business days</div>
            <div><i class="fas fa-shield-alt" style="color:var(--brand);width:18px"></i> Your payment info is secure</div>
        </div>
    </div>

    <a href="index.php" class="btn-back-shop">
        <i class="fas fa-store"></i> Continue Shopping
    </a>
</div>

<?php else: ?>
<!-- ── CHECKOUT FORM ── -->
<div class="checkout-wrap">

    <div>
        <form method="POST" action="checkout.php" id="checkoutForm">

            <!-- Contact -->
            <div class="card-block">
                <div class="section-title"><i class="fas fa-user"></i> Contact Details</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" placeholder="09xx xxx xxxx (11 digits)" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email address" required>
                    </div>
                </div>
            </div>

            <!-- Shipping -->
            <div class="card-block">
                <div class="section-title"><i class="fas fa-map-marker-alt"></i> Shipping Address</div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Street / Barangay</label>
                        <input type="text" name="address" class="form-control" placeholder="Enter your street or barangay" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City / Municipality</label>
                        <input type="text" name="city" class="form-control" placeholder="Enter your city or municipality" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Province</label>
                        <input type="text" name="province" class="form-control" placeholder="Enter your province" required>
                    </div>
                    <div class="c">
                        <label class="form-label">ZIP Code</label>
                        <input type="text" name="zip" class="form-control" placeholder="Enter your ZIP code" required>
                    </div>
                </div>
            </div>

            <!-- Payment -->
            <div class="card-block">
                <div class="section-title"><i class="fas fa-credit-card"></i> Payment Method</div>
                <div class="payment-options" id="paymentOptions">
                    <label class="pay-opt selected">
                        <input type="radio" name="payment" value="cod" checked>
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Cash on Delivery</span>
                    </label>
                    <label class="pay-opt">
                        <input type="radio" name="payment" value="gcash">
                        <i class="fas fa-mobile-alt"></i>
                        <span>GCash</span>
                    </label>
                    <label class="pay-opt">
                        <input type="radio" name="payment" value="card">
                        <i class="fas fa-credit-card"></i>
                        <span>Credit / Debit</span>
                    </label>
                    <label class="pay-opt">
                        <input type="radio" name="payment" value="bank">
                        <i class="fas fa-university"></i>
                        <span>Bank Transfer</span>
                    </label>
                </div>
            </div>

            <!-- Notes -->
            <div class="card-block">
                <div class="section-title"><i class="fas fa-sticky-note"></i> Order Notes <span style="font-size:.75rem;color:var(--muted);text-transform:none;font-family:'DM Sans',sans-serif;font-weight:400;">(optional)</span></div>
                <textarea name="notes" class="form-control" rows="3" placeholder="Special instructions, landmark, etc."></textarea>
            </div>

            <button type="submit" name="place_order" class="btn-place-order">
                <i class="fas fa-lock me-2"></i> Place Order
            </button>
            <div class="security-note">
                <i class="fas fa-shield-alt"></i> Your information is encrypted and secure
            </div>

        </form>
    </div>

    <!-- RIGHT: Summary -->
    <div>
        <div class="card-block">
            <div class="section-title"><i class="fas fa-receipt"></i> Order Summary</div>

            <?php foreach ($cart_items as $item): ?>
            <div class="summary-item">
                <?php if ($item['image']): ?>
                    <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="summary-thumb">
                <?php else: ?>
                    <div class="summary-thumb-placeholder"><i class="fas fa-image"></i></div>
                <?php endif; ?>
                <div class="summary-info">
                    <div class="summary-name"><?= htmlspecialchars($item['name']) ?></div>
                    <div class="summary-qty">Qty: <?= $item['quantity'] ?></div>
                </div>
                <div class="summary-price">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
            </div>
            <?php endforeach; ?>

            <div style="margin-top:16px;">
                <div class="totals-row">
                    <span class="label">Subtotal</span>
                    <span class="value">₱<?= number_format($total, 2) ?></span>
                </div>
                <div class="totals-row">
                    <span class="label">Shipping</span>
                    <span class="value" style="color:var(--success);">Free</span>
                </div>
                <div class="totals-row grand">
                    <span class="label">Total</span>
                    <span class="value">₱<?= number_format($total, 2) ?></span>
                </div>
            </div>
        </div>

        <div style="margin-top:14px; font-size:.78rem; color:var(--muted); text-align:center; line-height:1.6;">
            <i class="fas fa-undo me-1"></i> Free returns within 7 days &nbsp;·&nbsp;
            <i class="fas fa-headset me-1"></i> 24/7 support
        </div>
    </div>

</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Payment option selection highlight
    document.querySelectorAll('.pay-opt').forEach(opt => {
        opt.addEventListener('click', () => {
            document.querySelectorAll('.pay-opt').forEach(o => o.classList.remove('selected'));
            opt.classList.add('selected');
        });
    });
</script>
</body>
</html>

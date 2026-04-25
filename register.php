<?php
session_start();
include 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username        = trim($_POST['username'] ?? '');
    $password        = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->fetch_assoc()) {
            $stmt->close();
            $error = 'Username is already taken.';
        } else {
            $stmt->close();
            // Insert new user with bcrypt hash
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hash);
            if ($stmt->execute()) {
                $success = 'Account created successfully! You can now sign in.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>

    body {
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 90vh;
        background-color: #f0f0f0;
        font-family: 'DM Sans', sans-serif;
    }

    .main-card {
        display: flex;
        flex-direction: row;
        width: 100%;
        max-width: 1100px;
        min-height: 70vh;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-radius: 15px;
        overflow: hidden;
        background-color: white;
    }
    .value-proposition {
    flex: 1.3;
    display: flex;
    flex-direction: column;
    justify-content: top;
    padding: 40px;
    background-color: #ff5722;
    color: white;
    border-top-left-radius: 15px;
    border-bottom-left-radius: 15px;
    }

    .brand-icon {
            width: 25px;
            height: 25px;
            background: var(--brand);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #fff;

        }
        .brand-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        }
    .brand-header h1 {
            margin: 0;
        }
    .brand-icon {
            font-size: 1.5rem;
        }

    .login-side {
        flex: 0.9;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    form {
        display: flex;
        flex-direction: column;
        width: 300px;
    }

    input {
        margin-bottom: 15px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .btn-login {
    width: 100%;
    background: var(--brand);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 14px;
    font-size: .95rem;
    font-weight: 700;
    letter-spacing: .05em;
    cursor: pointer;
    transition: background .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 18px rgba(255,87,34,.3);
    margin-top: 8px;
    }
    .btn-login:hover {
        background: var(--brand-dark);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(255,87,34,.4);
     }
     :root {
            --brand: #ff5722;
            --brand-dark: #e64a19;
            --text: #f5f5f5;
            --muted: #888;
        }
    .social-login {
    display: flex;
    gap: 10px;
    }

    .social-login button {
        flex: 1;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 14px;
    }

    .btn-google i { color: #DB4437; }
    .btn-facebook i { color: #4267B2; }
    .social-login button:hover {
        background: #f0f0f0;
    }

    .carousel-item img {
    height: 300px;
    object-fit:contain;
    }

    .carousel-item h5 {
        font-weight: 700;
        margin-top: 10px;
    }

    .carousel-item p {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .carousel-indicators [button] {
        background-color: white;
    }
    #propCarousel {
    margin-top: 30px;
    }
    #or-signup {
        text-align: center;
        margin-top: 20px;
        font-size: .85rem;
        color: var(--muted);
    }

    .error-msg, .success-msg {
        background: rgba(239,68,68,.12);
        border: 1px solid rgba(239,68,68,.3);
        color: #dc2626;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: .85rem;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        width: 300px;
    }

    .success-msg {
        background: rgba(34,197,94,.12);
        border-color: rgba(34,197,94,.3);
        color: #16a34a;
    }

</style>

<div class="main-card">
    <div class="value-proposition">
    <div class="brand-header">
        <div class="brand-icon"><i class="fas fa-shopping-bag"></i></div>
        <h1>Sample Shop</h1>
    </div>

    <div id="propCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/box.png" class="d-block w-100 rounded" alt="Feature 1">
                <div class="mt-3">
                    <h5>Shop the Latest Trends</h5>
                    <p>Access exclusive collections and new arrivals every week!</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/fast_delivery.png" class="d-block w-100 rounded" alt="Feature 2">
                <div class="mt-3">
                    <h5>Fast & Secure Delivery</h5>
                    <p>Free shipping nationwide with real-time tracking!</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="assets/customer_support.png" class="d-block w-100 rounded" alt="Feature 3">
                <div class="mt-3">
                    <h5>24/7 Customer Support</h5>
                    <p>Our team is always here to help you with your shopping needs!</p>
                </div>
            </div>
        </div>

        <div class="carousel-indicators" style="position: relative; margin-top: 20px;">
            <button type="button" data-bs-target="#propCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#propCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#propCarousel" data-bs-slide-to="2"></button>
        </div>
    </div>
</div>

    <div class="login-side">
        <?php if ($error): ?>
        <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="success-msg"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <h2><i class="fas fa-user-plus"></i> Sign Up</h2>
            <label>Create Username:</label>
            <input type="text" name="username" placeholder="Username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">

            <label>Create Password:</label>
            <input type="password" name="password" placeholder="Password" required>

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" class="btn-login"><i class="fas fa-user-plus"></i>Sign Up</button>
            <center>
            <h6 id="or-signup">Or sign up with:</h6>
            <div class="social-login">
                <button class="btn-google"><i class="fab fa-google"></i>Google</button>
                <button class="btn-facebook"><i class="fab fa-facebook"></i>Facebook</button>
            </div>
            <br><center>Already have an account? <a href="login.php">Log in here</a>
            <br></center>
        </form>
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
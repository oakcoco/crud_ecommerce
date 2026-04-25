<?php
session_start();

// redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit();
}

$error = '';
// default. change in production
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($user === ADMIN_USERNAME && $pass === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = 'Incorrect username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – Sample Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --brand: #ff5722;
            --brand-dark: #e64a19;
            --bg: #0f0f0f;
            --card: #1a1a1a;
            --border: #2a2a2a;
            --text: #f5f5f5;
            --muted: #888;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        /* Background decoration */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 600px 400px at 80% 20%, rgba(255,87,34,.1) 0%, transparent 70%),
                radial-gradient(ellipse 400px 300px at 10% 80%, rgba(255,87,34,.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .login-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 44px 40px;
            width: 100%;
            max-width: 420px;
            position: relative;
            box-shadow: 0 24px 80px rgba(0,0,0,.5);
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 30px; right: 30px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--brand), transparent);
            border-radius: 0 0 4px 4px;
        }

        .brand-mark {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 32px;
        }
        .brand-icon {
            width: 42px;
            height: 42px;
            background: var(--brand);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #fff;
        }
        .brand-text {
            font-weight: 800;
            font-size: 1.1rem;
            line-height: 1.1;
        }
        .brand-text small {
            display: block;
            font-size: .65rem;
            font-weight: 400;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
        }

        h1 {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 6px;
        }
        .subtitle {
            color: var(--muted);
            font-size: .88rem;
            margin-bottom: 32px;
        }

        .field { margin-bottom: 18px; }
        .field label {
            display: block;
            font-size: .75rem;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 8px;
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: .85rem;
        }
        .field input {
            width: 100%;
            background: #111;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px 12px 38px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: .95rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .field input:focus {
            outline: none;
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(255,87,34,.15);
        }
        .field input::placeholder { color: #444; }

        .error-msg {
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.3);
            color: #f87171;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: .85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
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

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: .82rem;
            color: var(--muted);
            text-decoration: none;
            transition: color .2s;
        }

        .back-link:hover {
        color: var(--brand);
        }


    </style>
</head>
<body>
<div class="login-card">
    <div class="brand-mark">
        <div class="brand-icon"><i class="fas fa-shopping-bag"></i></div>
        <div class="brand-text">
            Sample Shop
            <small>Admin Panel</small>
        </div>
    </div>

    <h1>Sign In</h1>
    <p class="subtitle">Access the admin dashboard to manage your store.</p>

    <?php if ($error): ?>
    <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="admin_login.php">
        <div class="field">
            <label>Username</label>
            <div class="input-wrap">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Enter username" autocomplete="username" required>
            </div>
        </div>
        <div class="field">
            <label>Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Enter password" autocomplete="current-password" required>
            </div>
        </div>
        <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt me-2"></i>Sign In</button>
    </form>

    <a href="index.php" class="back-link"><i class="fas fa-arrow-left me-1"></i> Back to Shop</a>

    
</div>
</body>
</html>

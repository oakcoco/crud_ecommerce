<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$success = '';
$error = '';

// profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_username = trim($_POST['username'] ?? '');

    if (empty($new_username)) {
        $error = 'Username cannot be empty.';
    } elseif (strlen($new_username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } else {
        // username validation
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $new_username, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->fetch_assoc()) {
            $stmt->close();
            $error = 'Username is already taken.';
        } else {
            $stmt->close();
            $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->bind_param("si", $new_username, $user_id);
            if ($stmt->execute()) {
                $_SESSION['username'] = $new_username;
                $username = $new_username;
                $success = 'Profile updated successfully!';
            } else {
                $error = 'Failed to update profile.';
            }
            $stmt->close();
        }
    }
}

// password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'Please fill in all password fields.';
    } elseif (strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New passwords do not match.';
    } else {
        // verification
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($current_password, $user['password'])) {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hash, $user_id);
            if ($stmt->execute()) {
                $success = 'Password changed successfully!';
            } else {
                $error = 'Failed to change password.';
            }
            $stmt->close();
        } else {
            $error = 'Current password is incorrect.';
        }
    }
}


$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --brand: #ff5722;
            --brand-dark: #e64a19;
            --muted: #888;
        }
        body { background-color: #f5f5f5; }
        .nav-link {
            margin: 0px;
            transition: color 0.3s ease;
        }
        .nav-link:hover { color: #afafaf !important; transition: 0.3s; }
        .navbar { background-color: #ff5722; height: 75px; }
        .navbar-brand { color: white !important; }
        .profile-card {
            border: 1px solid #e0e0e0;
            border-radius: 15px;
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
            background: white;
            overflow: hidden;
        }
        .profile-header {
            background: linear-gradient(135deg, #ff5722, #e64a19);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 2.5rem;
            color: #ff5722;
        }
        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        .profile-email {
            opacity: 0.9;
            margin-top: 5px;
        }
        .nav-tabs {
            border-bottom: 2px solid #e0e0e0;
        }
        .nav-tabs .nav-link {
            color: #666;
            border: none;
            padding: 15px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .nav-tabs .nav-link:hover {
            color: #ff5722;
            border: none;
        }
        .nav-tabs .nav-link.active {
            color: #ff5722;
            border: none;
            border-bottom: 3px solid #ff5722;
            background: transparent;
        }
        .tab-content {
            padding: 30px;
        }
        .form-label {
            font-weight: 500;
            color: #333;
        }
        .form-control:focus {
            border-color: #ff5722;
            box-shadow: 0 0 0 0.2rem rgba(255,87,34,0.25);
        }
        .btn-update {
            background: #ff5722;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-update:hover {
            background: #e64a19;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255,87,34,0.4);
            color: white;
        }
        .btn-update:disabled {
            background: #ccc;
            transform: none;
            box-shadow: none;
        }
        .alert-success {
            background: rgba(34,197,94,0.12);
            border: 1px solid rgba(34,197,94,0.3);
            color: #16a34a;
            border-radius: 8px;
            padding: 12px 15px;
        }
        .alert-danger {
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.3);
            color: #dc2626;
            border-radius: 8px;
            padding: 12px 15px;
        }
        .account-stat {
            text-align: center;
            padding: 20px;
            border-right: 1px solid #e0e0e0;
        }
        .account-stat:last-child { border-right: none; }
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ff5722;
        }
        .stat-label {
            color: #666;
            font-size: 0.85rem;
            margin-top: 5px;
        }
        .member-since {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container d-flex align-items-center">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-shopping-bag"></i> Sample Shop
            </a>
            <div class="d-flex ms-auto">
                <a class="nav-link text-white ms-3" href="cart.php">
                    <i class="fas fa-shopping-cart"></i> Cart
                    <span class="badge bg-white text-danger ms-1">
                        <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                    </span>
                </a>
                <a class="nav-link text-white ms-3" href="profile.php">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($username); ?>
                </a>
                <a class="nav-link text-white ms-3" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="profile-name"><?php echo htmlspecialchars($user['username']); ?></h3>
                        <p class="member-since"><i class="fas fa-calendar-alt me-1"></i> Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                    </div>
                    <div class="d-flex">
                        <div class="account-stat">
                            <div class="stat-number"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></div>
                            <div class="stat-label">Cart Items</div>
                        </div>
                        <div class="account-stat">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Orders</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="profile-card">
                    <ul class="nav nav-tabs" id="profileTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#edit-profile">
                                <i class="fas fa-user-edit me-2"></i>Edit Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#change-password">
                                <i class="fas fa-lock me-2"></i>Change Password
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="edit-profile">
                            <form method="POST" action="profile.php">
                                <input type="hidden" name="update_profile" value="1">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control"
                                           value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Account Created</label>
                                    <p class="form-control-plaintext text-muted mb-0">
                                        <?php echo date('F j, Y g:i A', strtotime($user['created_at'])); ?>
                                    </p>
                                </div>
                                <button type="submit" class="btn btn-update">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="change-password">
                            <form method="POST" action="profile.php">
                                <input type="hidden" name="change_password" value="1">
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <div class="input-group">
                                        <input type="password" name="current_password" class="form-control"
                                               placeholder="Enter current password" required>
                                        <button type="button" class="btn btn-outline-secondary toggle-password" onclick="togglePassword(this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <div class="input-group">
                                        <input type="password" name="new_password" class="form-control"
                                               placeholder="Enter new password" required>
                                        <button type="button" class="btn btn-outline-secondary toggle-password" onclick="togglePassword(this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <div class="input-group">
                                        <input type="password" name="confirm_password" class="form-control"
                                               placeholder="Confirm new password" required>
                                        <button type="button" class="btn btn-outline-secondary toggle-password" onclick="togglePassword(this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-update">
                                    <i class="fas fa-key me-2"></i>Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(btn) {
            const input = btn.closest('.input-group').querySelector('input');
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        setTimeout(function() {
            var alertEl = document.querySelector('.alert');
            if (alertEl) new bootstrap.Alert(alertEl).close();
        }, 4000);
    </script>
</body>
</html>

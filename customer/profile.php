<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

include("../includes/db.php");

$user_id = $_SESSION["user_id"];

// Handle Profile Update
if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);

    if (empty($name) || empty($email)) {
        $_SESSION['error_message'] = "Name and Email are required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format.";
    } else {
        $stmt_check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt_check_email->bind_param("si", $email, $user_id);
        $stmt_check_email->execute();
        $result_check_email = $stmt_check_email->get_result();
        
        if ($result_check_email->num_rows > 0) {
            $_SESSION['error_message'] = "This email is already registered to another account.";
        } else {
            $stmt_update = $conn->prepare("UPDATE users SET name = ?, email = ?, contact = ? WHERE id = ?");
            $stmt_update->bind_param("sssi", $name, $email, $contact, $user_id);

            if ($stmt_update->execute()) {
                $_SESSION['success_message'] = "Profile updated successfully!";
                $_SESSION['user_name'] = $name; // Update session name immediately
            } else {
                $_SESSION['error_message'] = "Error updating profile: " . $stmt_update->error;
            }
            $stmt_update->close();
        }
        $stmt_check_email->close();
    }
    header("Location: profile.php");
    exit();
}

// Handle Password Change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
        $_SESSION['error_message'] = "All password fields are required.";
    } elseif ($new_password !== $confirm_new_password) {
        $_SESSION['error_message'] = "New password and confirm password do not match.";
    } elseif (strlen($new_password) < 6) {
        $_SESSION['error_message'] = "New password must be at least 6 characters long.";
    } else {
        $stmt_fetch_pass = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_fetch_pass->bind_param("i", $user_id);
        $stmt_fetch_pass->execute();
        $result_fetch_pass = $stmt_fetch_pass->get_result();
        $user_pass_data = $result_fetch_pass->fetch_assoc();
        $stmt_fetch_pass->close();

        if ($user_pass_data && password_verify($current_password, $user_pass_data['password'])) {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt_update_pass = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt_update_pass->bind_param("si", $hashed_new_password, $user_id);
            if ($stmt_update_pass->execute()) {
                $_SESSION['success_message'] = "Password changed successfully!";
            } else {
                $_SESSION['error_message'] = "Error changing password: " . $stmt_update_pass->error;
            }
            $stmt_update_pass->close();
        } else {
            $_SESSION['error_message'] = "Incorrect current password.";
        }
    }
    header("Location: profile.php");
    exit();
}

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['error_message'] = "User data not found.";
    header("Location: ../logout.php");
    exit();
}

// Get cart count for the navbar badge
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - FoodZone</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts & Icons for new navbar -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* General Body Styling */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            padding-top: 70px; /* Navbar එකට ඉඩ තැබීමට */
        }
        
        /* ========= NEW NAVIGATION BAR STYLING ========= */
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 0.75rem 2rem;
            font-weight: 500;
        }
        .navbar-custom .navbar-brand {
            font-weight: 700;
            color: #ffc107 !important;
        }
        .navbar-custom .nav-link {
            color: #555 !important;
            margin: 0 0.5rem;
            transition: color 0.2s;
        }
        .navbar-custom .nav-link:hover, .navbar-custom .nav-link.active {
            color: #ffc107 !important;
        }
        .navbar-custom .cart-link {
            position: relative;
        }
        .navbar-custom .cart-count-badge {
            position: absolute;
            top: -5px;
            right: -10px;
            font-size: 0.7rem;
            padding: 0.2em 0.5em;
        }
        /* ========= END OF NAVBAR STYLING ========= */

        /* Your original card styling (if any) can go here */
        .card {
            border-radius: 12px;
        }
    </style>
</head>
<body class="bg-light">

<!-- ========= UPDATED NAVIGATION BAR ========= -->
<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand fs-4" href="menu.php"><i class="bi bi-egg-fried"></i> FoodZone</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link active" href="profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="track_order.php"><i class="bi bi-truck"></i> Track Orders</a></li>
                <li class="nav-item">
                    <a class="nav-link cart-link" href="cart.php">
                        <i class="bi bi-cart3"></i> Cart
                        <?php if ($cart_count > 0): ?>
                            <span class="badge rounded-pill bg-danger cart-count-badge"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../logout.php" class="btn btn-sm btn-outline-danger ms-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- ========= END OF UPDATED NAVIGATION BAR ========= -->


<!-- The rest of your page content remains exactly as you provided it -->
<div class="container mt-4">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="card shadow-sm p-4">
        <h3 class="mb-4">👤 My Profile</h3>
        <form method="POST" action="profile.php">
             <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Contact Number:</label>
                <input type="text" class="form-control" id="contact" name="contact" value="<?= htmlspecialchars($user['contact'] ?? '') ?>">
            </div>
            <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
        </form>
        
        <hr class="my-4">

        <h4 class="mb-3">🔐 Change Password</h4>
        <form method="POST" action="profile.php">
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password:</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password:</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_new_password" class="form-label">Confirm New Password:</label>
                <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
            </div>
            <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
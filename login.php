<?php

include("includes/db.php");
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $email    = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            header("Location: customer/index.php");
            exit();
        } else {
            $message = "Incorrect password!";
        }
    } else {
        $message = "No user found with that email!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodZone - Login</title>
 
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    

    <style>
      
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff; 
            color: #333333;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0;
            box-sizing: border-box;
        }

        .register-container {
            background-color: #f8f9fa; 
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            border: 1px solid #e0e0e0; 
        }

    
        h2 {
            color: #ffc107; 
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }

      
        .form-label {
            color: #333333;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control {
            background-color: #ffffff; 
            color: #333333;
            border: 1px solid #ced4da; 
            border-radius: 8px;
            padding: 12px 15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus {
            background-color: #ffffff;
            color: #333333;
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }
        .form-control::placeholder {
            color: #888;
        }

       
        .btn-custom {
            background-color: #ffc107;
            border: none;
            color: #1a1a1a;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 20px;
        }
        .btn-custom:hover {
            background-color: #e0a800;
            transform: translateY(-2px);
            color: #1a1a1a;
        }

        /* Link Styling */
        a {
            color: #007bff; /* Standard blue link */
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }

        /* Alert Messages */
        .alert {
            border-radius: 8px;
            font-size: 0.95rem;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert-danger { /* Style for error messages */
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>

<!-- Changed container to match register.php structure -->
<div class="register-container">
    <h2>Login</h2>
    <?php if ($message): ?>
        <!-- Using alert-danger for login errors -->
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="POST" class="mt-3">
        <div class="mb-3">
            <!-- Added form-label class -->
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" required class="form-control" placeholder="Enter your email">
        </div>
        <div class="mb-3">
            <!-- Added form-label class -->
            <label for="password" class="form-label">Password:</label>
            <input type="password" name="password" id="password" required class="form-control" placeholder="Enter your password">
        </div>
        <!-- Changed button class to btn-custom for matching style -->
        <button type="submit" class="btn btn-custom">Login</button>
    </form>
    <!-- Centered the bottom link -->
    <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register</a></p>
</div>

<!-- Bootstrap JS Bundle (for any future JS components) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
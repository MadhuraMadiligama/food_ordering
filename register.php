<?php
include("includes/db.php"); // Database

$message = "";

$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get inputs from POST request
    // Using null coalescing operator (?? '') to prevent "Undefined array key" warnings
    $name              = $_POST["name"] ?? '';
    $nic               = $_POST["nic"] ?? '';
    $email             = $_POST["email"] ?? '';
    $password_raw      = $_POST["password"] ?? ''; // Get raw password for hashing
    $contact           = $_POST["contact"] ?? '';
    $address           = $_POST["address"] ?? ''; // Fixed: line 13 error
    $city              = $_POST["city"] ?? '';
    $registration_date = $_POST["registration_date"] ?? ''; // Get registration date from form

    // Check if password is provided before hashing
    if (empty($password_raw)) {
        $message = "Password cannot be empty!";
        // You might want to skip the rest of the registration logic here
    } else {
        $password = password_hash($password_raw, PASSWORD_DEFAULT); // Hash the password

        // Check if email already exists using prepared statement
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result(); // Store result to check num_rows

        if ($stmt_check->num_rows > 0) {
            $message = "Email already registered!";
        } else {
            // Insert new user record using prepared statement
            // Note: Using NOW() for created_at, and using provided $registration_date
            $stmt_insert = $conn->prepare("INSERT INTO users (name, nic, email, password, contact, address, city, registration_date) 
                                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            // "ssssssss" -> Each 's' represents a string parameter
            $stmt_insert->bind_param("ssssssss", $name, $nic, $email, $password, $contact, $address, $city, $registration_date);
            
           if ($stmt_insert->execute()) {
    $message = "Registration successful! You can now login";
} else {
    $message = "Error: " . $stmt_insert->error;
}
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodZone - Register</title>
    <!-- Google Fonts - Inter for a modern look -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS for basic layout and components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Body Styling */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff; /* White background */
            color: #333333; /* Dark text for contrast */
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0;
            box-sizing: border-box;
        }

        /* Container for the form */
        .register-container {
            background-color: #f8f9fa; /* Light gray background for container */
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); /* Lighter shadow */
            max-width: 500px;
            width: 100%;
            border: 1px solid #e0e0e0; /* Light border */
        }

        /* Heading Styling */
        h2 {
            color: #ffc107; /* Yellow heading */
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }

        /* Form Labels and Inputs */
        .form-label {
            color: #333333;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control {
            background-color: #ffffff; /* White input background */
            color: #333333;
            border: 1px solid #ced4da; /* Light gray border */
            border-radius: 8px;
            padding: 12px 15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus {
            background-color: #ffffff;
            color: #333333;
            border-color: #ffc107; /* Yellow focus border */
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }
        .form-control::placeholder {
            color: #888;
        }
        textarea.form-control {
            min-height: 80px;
            resize: vertical;
        }

        /* Button Styling */
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
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
        }
    </style>
</head>
<body>
<div class="register-container">
    <h2>Register</h2>
    <?php if ($success_message): ?>
    <!-- For success message, we DON'T use htmlspecialchars, so the link works. -->
    <div class="alert alert-success"><?= $success_message ?></div>
<?php elseif ($message): ?>
    <!-- For error messages, we STILL use htmlspecialchars for security. -->
    <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" name="name" id="name" required class="form-control">
        </div>
        <div class="mb-3">
            <label for="nic" class="form-label">NIC:</label>
            <input type="text" name="nic" id="nic" required class="form-control" placeholder="Enter your NIC number">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" required class="form-control">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" name="password" id="password" required class="form-control">
        </div>
        <div class="mb-3">
            <label for="contact" class="form-label">Contact:</label>
            <input type="text" name="contact" id="contact" required class="form-control" placeholder="Enter your phone number">
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address:</label>
            <textarea name="address" id="address" required class="form-control" placeholder="Enter your address"></textarea>
        </div>
        <div class="mb-3">
            <label for="city" class="form-label">City:</label>
            <input type="text" name="city" id="city" required class="form-control" placeholder="Enter your city">
        </div>
        <div class="mb-3">
            <label for="registration_date" class="form-label">Registration Date:</label>
            <input type="date" name="registration_date" id="registration_date" class="form-control" value="<?= date('Y-m-d') ?>">
             <!-- Default value set to current date for convenience -->
        </div>
        
        <button type="submit" class="btn btn-custom">Register</button>
    </form>
    <p class="mt-3 text-center">Already have an account? <a href="login.php">Login</a></p>
</div>
<!-- Bootstrap JS Bundle (for alert dismiss if added) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

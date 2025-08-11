<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}


unset($_SESSION["cart"]);


$cart_count = 0; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - FoodZone</title>
  
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
     
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        
     
        .success-card {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            max-width: 550px; 
            width: 100%;
            text-align: center;
            border-top: 8px solid #28a745; /* Green top border for success */
        }
        
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 1.5rem;
            animation: pop-in 0.5s ease-out;
        }
        
        .success-card h2 {
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .success-card p {
            font-size: 1.1rem;
            color: #666;
            line-height: 1.6;
        }
        
        /* Custom Button Styling */
        .btn-payment {
            background-color: #28a745; /* Green color for payment */
            border-color: #28a745;
            color: #ffffff;
            font-weight: 600;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        .btn-payment:hover {
            background-color: #218838;
            border-color: #1e7e34;
            color: #ffffff;
            transform: translateY(-2px);
        }

        .btn-menu {
            font-weight: 500;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
        }

        @keyframes pop-in {
            0% { transform: scale(0.5); opacity: 0; }
            80% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="success-card">
                <div class="success-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h2 class="mb-3">Order Placed Successfully!</h2>
                <p class="mb-4">
                    Your order has been confirmed and is now being prepared. Please proceed to payment to complete your purchase.
                </p>
                
                <!-- Your original process buttons are here, styled differently -->
                <div class="mt-4">
                    <a href="payment.php" class="btn btn-payment btn-lg">
                        <i class="bi bi-credit-card-fill"></i> Proceed to Payment
                    </a>
                </div>
                
                <div class="mt-3">
                    <a href="menu.php" class="btn btn-outline-secondary btn-menu">Back to Menu</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Database connection is included, which is good practice.
include("includes/db.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodZone - Welcome</title>
    <!-- Google Fonts - Inter (Consistent with other pages) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- CSS Styles adapted from Login/Register pages -->
    <style>
        /* General Body Styling (from login/register page) */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff; /* White background */
            color: #333333; /* Dark text for contrast */
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-sizing: border-box;
        }

        /* Container for the home page content (Styled like login/register container) */
        .home-container {
            background-color: #f8f9fa; /* Light gray background for container */
            padding: 50px 40px; /* Generous padding */
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); /* Lighter shadow */
            max-width: 800px; /* Wider for home page content */
            width: 100%;
            border: 1px solid #e0e0e0; /* Light border */
            text-align: center;
            animation: fadeIn 1s ease-in-out; /* Simple fade-in animation */
        }

        /* Main heading styling */
        h1 {
            font-size: 3.2em;
            font-weight: 700;
            margin-bottom: 25px;
            color: #ffc107; /* Yellow color for the heading, matching theme */
        }

        /* Image slider container */
        .image-slider-container {
            width: 100%;
            max-width: 600px;
            height: 350px;
            margin: 0 auto 30px auto;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Softer shadow for light background */
            background-color: #e9ecef; /* Light gray background for image loading */
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* Slider images */
        .slider-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 2s ease-in-out; /* Smooth fade transition */
            position: absolute;
            opacity: 0;
        }
        .slider-image.active {
            opacity: 1; /* Makes the active image visible */
        }

        /* Button group styling */
        .btn-group-custom {
            display: flex;
            flex-direction: column; /* Stack buttons on smaller screens */
            justify-content: center;
            align-items: center;
            gap: 15px; /* Space between buttons */
            margin-top: 20px;
        }
        
        /* Responsive for larger screens */
        @media (min-width: 576px) {
            .btn-group-custom {
                flex-direction: row; /* Buttons side-by-side on larger screens */
                gap: 25px;
            }
        }

        /* Base button styling */
        .btn {
            padding: 12px 35px;
            font-size: 1.1em;
            border-radius: 8px; /* Consistent border radius */
            transition: all 0.3s ease;
            font-weight: 600;
            width: 200px; /* Fixed width for buttons */
            max-width: 100%;
        }

        /* Primary button style (for Login) - matches register button */
        .btn-custom-primary {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #1a1a1a;
        }
        .btn-custom-primary:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: #1a1a1a;
            transform: translateY(-2px);
        }

        /* Secondary button style (for Register) - outline style */
        .btn-custom-secondary {
            background-color: transparent;
            border: 2px solid #ffc107;
            color: #ffc107;
        }
        .btn-custom-secondary:hover {
            background-color: #ffc107;
            color: #1a1a1a;
            transform: translateY(-2px);
        }

        /* Keyframe animation for fade-in effect */
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.98); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

<div class="home-container">
    <h1>Welcome to FoodZone</h1>
    
    <div class="image-slider-container">
        <!-- Images from your JS will appear here -->
    </div>
    
    <div class="btn-group-custom">
        <!-- Updated button classes for new styling -->
        <a href="login.php" class="btn btn-custom-primary">Login</a>
        <a href="register.php" class="btn btn-custom-secondary">Register</a>
    </div>
</div>

<!-- Your JavaScript for the slider remains unchanged -->
<script>
    const images = [
        'assets/images/Food-fried-bun_2560x1600.jpg',
        'assets/images/Great Buns 1.jpg',
        'assets/images/l-intro-1693940201.jpg',
        'assets/images/Bak Kwa fried rice 011.jpg',
        'assets/images/file.png'
    ];
    let currentIndex = 0;
    const sliderContainer = document.querySelector('.image-slider-container');
    let currentImageElement = null;

    function changeImage() {
        const newImageElement = document.createElement('img');
        newImageElement.src = images[currentIndex];
        newImageElement.classList.add('slider-image');
        newImageElement.alt = 'Food Image ' + (currentIndex + 1);
        sliderContainer.appendChild(newImageElement);

        setTimeout(() => {
            newImageElement.classList.add('active');
        }, 50);

        if (currentImageElement) {
            currentImageElement.classList.remove('active');
            setTimeout(() => {
                if (currentImageElement && currentImageElement.parentNode) {
                    currentImageElement.parentNode.removeChild(currentImageElement);
                }
            }, 2000); // Corresponds to CSS transition duration
        }
        
        currentImageElement = newImageElement;
        currentIndex = (currentIndex + 1) % images.length;
    }

    changeImage();
    setInterval(changeImage, 50000);
</script>

</body>
</html>
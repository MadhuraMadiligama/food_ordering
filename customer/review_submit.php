<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION["user_id"])) {
    die("Unauthorized.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $food_id = (int)$_POST["food_id"];
    $rating = (int)$_POST["rating"];
    $comment = trim($_POST["comment"]);

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, food_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $food_id, $rating, $comment);
    $stmt->execute();
    $stmt->close();

    header("Location: menu.php");
    exit();
}
?>

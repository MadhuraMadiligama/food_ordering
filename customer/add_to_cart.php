<?php
session_start();
include("../includes/db.php"); // Ensure this path is correct

// Redirect to login if user not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

// Get food item ID and quantity from GET
$food_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$quantity = isset($_GET["qty"]) ? (int)$_GET["qty"] : 1;
if ($quantity < 1) $quantity = 1; // Ensure quantity is at least 1

// 1. Get item details from DB, including current stock
$stmt = $conn->prepare("SELECT id, name, price, image, stock FROM food_items WHERE id = ?");
$stmt->bind_param("i", $food_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    // Food item not found in database
    $_SESSION['error_message'] = "Food item not found!";
    header("Location: menu.php"); // Redirect back to menu
    exit();
}

$available_stock = $item['stock'];
$item_name = $item['name']; // To use in messages

// Initialize cart session if not exists
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

$current_cart_quantity = 0;
$found_in_cart = false;

// Check current quantity of this item in session cart
foreach ($_SESSION["cart"] as $cart_item) {
    if ($cart_item["id"] == $food_id) {
        $current_cart_quantity = $cart_item["quantity"];
        $found_in_cart = true;
        break;
    }
}

// Calculate the total quantity if this addition is made
$requested_total_quantity = $current_cart_quantity + $quantity;

// 2. Check if enough stock is available before adding to cart session
// (This is a check before adding to cart, not for ordering)
if ($requested_total_quantity > $available_stock) {
    // Not enough stock
    $_SESSION['error_message'] = "Sorry, only " . $available_stock . " of " . htmlspecialchars($item_name) . " are available. You currently have " . $current_cart_quantity . " in your cart.";
    header("Location: menu.php"); // Or wherever you want to redirect
    exit();
}

// Proceed with adding/updating item in SESSION cart
if ($found_in_cart) {
    // Update quantity if item already exists in session cart
    foreach ($_SESSION["cart"] as &$cart_item) { // Use & to modify the original array item
        if ($cart_item["id"] == $food_id) {
            $cart_item["quantity"] = $requested_total_quantity; // Set to the new total quantity
            break;
        }
    }
    unset($cart_item); // Unset the reference to avoid issues
} else {
    // If not found, add new item to session cart
    $_SESSION["cart"][] = [
        "id" => $item["id"],
        "name" => $item["name"],
        "price" => $item["price"],
        "image" => $item["image"],
        "quantity" => $quantity // Only add the requested quantity for a new item
    ];
}

// --- IMPORTANT: STOCK IS NOT DECREMENTED HERE! ---
// Stock decrement happens ONLY when the order is placed at checkout.

$_SESSION['success_message'] = htmlspecialchars($item_name) . " added to cart successfully!";

// Redirect to cart or menu
header("Location: cart.php"); // Redirect to cart
exit();
?>

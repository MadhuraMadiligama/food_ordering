<?php
// Run this once to insert a hashed admin password
//include("../includes/db.php");

include(__DIR__ . "/../includes/db_connect.php"); 

$email = "admin@example.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);

$conn->query("INSERT INTO admins (email, password) VALUES ('$email', '$password')");
echo "Admin created!";
?>

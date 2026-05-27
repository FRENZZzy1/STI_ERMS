<?php
session_start();
include("../connect.php"); 

$user_id = $_SESSION['user_id'];

$update_stmt = $conn->prepare("UPDATE users SET status = 'offline' WHERE user_id = ?");
$update_stmt->bind_param("i", $user_id);
$update_stmt->execute();
$update_stmt->close();


session_unset(); // Remove all session variables
session_destroy(); // Destroy the session
header("Location: ../login.php");
exit();
?>

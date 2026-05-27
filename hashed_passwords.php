<?php
include("connect.php");

// Fetch all users
$sql = "SELECT user_id, password FROM users";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("No users found.");
}

while ($row = $result->fetch_assoc()) {

    $user_id = $row['user_id'];
    $plainPassword = $row['password'];

    // Skip if already hashed (hashed passwords always start with $2y$ or $2a$)
    if (strpos($plainPassword, '$2y$') === 0 || strpos($plainPassword, '$2a$') === 0) {
        echo "User $user_id already hashed — skipped.<br>";
        continue;
    }

    // Hash the password
    $hashed = password_hash($plainPassword, PASSWORD_DEFAULT);

    // Update the user record
    $update = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $update->bind_param("si", $hashed, $user_id);

    if ($update->execute()) {
        echo "User $user_id password hashed successfully.<br>";
    } else {
        echo "Failed to hash password for user $user_id.<br>";
    }

    $update->close();
}

$conn->close();

echo "<br>✔ DONE — All plain-text passwords converted to hashed passwords.";
?>

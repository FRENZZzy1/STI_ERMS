<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include("../../connect.php");

// Check if ID is passed
if (!isset($_GET['id'])) {
    echo "<script>alert('No equipment ID provided.'); window.history.back();</script>";
    exit;
}

$id = intval($_GET['id']);

try {
    // Update the status to Archived
    $stmt = $conn->prepare("UPDATE users SET status = 'offline' WHERE user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Redirect back to equipment list (change the filename below if needed)
    header("Location: archived_user_retrieve.php?archived=success");
    exit;
} catch (Exception $e) {
    echo "<script>alert('Failed to archive equipment: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    exit;
}
?>

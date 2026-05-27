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
$changed_by = $_SESSION['user_id'] ?? 'System'; // or whoever is logged in

try {
    // Begin transaction
    $conn->begin_transaction();

    // Get the old status before updating
    $stmt = $conn->prepare("SELECT status FROM equipment WHERE equipment_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $oldStatus = $result->fetch_assoc()['status'] ?? null;

    // Update the status to 'Archived'
    $update = $conn->prepare("UPDATE equipment SET status = 'Archived' WHERE equipment_id = ?");
    $update->bind_param("i", $id);
    $update->execute();

    // Insert a log into equipment_history
    $insert = $conn->prepare("
        INSERT INTO equipment_history (equipment_id, action, old_status, new_status, changed_by, changed_at)
        VALUES (?, 'Removed', ? , 'Archived', ?, NOW())
    ");
    $insert->bind_param("iss", $id, $oldStatus, $changed_by);
    $insert->execute();

    //  Commit transaction
    $conn->commit();

    //  Redirect
    header("Location: Equipment-retrieve.php?archived=success");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    echo "<script>alert('Failed to archive equipment: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    exit;
}
?>

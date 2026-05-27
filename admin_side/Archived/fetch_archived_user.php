<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include("../../connect.php");
include("../pass-down-method.php");

//  Only select 'Archived' users
$status = 'Archived';
$role = isset($_GET['role']) ? strtolower(trim($_GET['role'])) : 'all';

if ($role === 'all') {
    $stmt = $conn->prepare("SELECT * FROM users WHERE status = ?");
    $stmt->bind_param("s", $status);
} else {
    $stmt = $conn->prepare("SELECT * FROM users WHERE status = ? AND LOWER(role) = ?");
    $stmt->bind_param("ss", $status, $role);
}

$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

if (empty($rows)) {
    echo "<p style='text-align:center; color:blue;'>No archived users found for this filter.</p>";
    exit;
}
?>

<table>
  <tr>
    <th>User ID</th>
    <th>Username</th>
    <th>Password</th>
    <th>Role</th>
    <th>Status</th>
    <th>Time Registered</th>
    <th>Actions</th>
  </tr>

  <?php foreach ($rows as $row): ?>
    <tr>
      <td><?= htmlspecialchars($row["user_id"]) ?></td>
      <td><?= htmlspecialchars($row["username"]) ?></td>
      <td><?= htmlspecialchars($row["password"]) ?></td>
      <td><?= htmlspecialchars($row["role"]) ?></td>
      <td><?= htmlspecialchars($row["status"]) ?></td>
      <td><?= htmlspecialchars($row["created_at"]) ?></td>
      <td>
        <a href="restore_archived_user.php?id=<?= urlencode($row["user_id"]) ?>" onclick="return confirm('Are you sure you want to restore this user?');">
          <button class="btn btn-delete">Restore</button>
        </a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

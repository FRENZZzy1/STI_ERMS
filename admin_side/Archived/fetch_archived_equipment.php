<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include("../../connect.php");
include("../pass-down-method.php");




// Only select 'Good' equipment
$status = 'Archived';
$stmt = $conn->prepare("SELECT * FROM equipment WHERE status = ?");
$stmt->bind_param("s",$status);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

if (empty($rows)) {
    echo "<p style='text-align:center; color:blue;'>No equipment found for this lab.</p>";
    exit;
}
?>

<table>
  <tr>
    <th>Equipment ID</th>
    <th>Barcode</th>
    <th>Name</th>
    <th>Description</th>
    <th>Location</th>
    <th>Condition</th>
    <th>Time Registered</th>
    <th>Actions</th>
  </tr>

  <?php foreach ($rows as $row): ?>
    <tr>
      <td><?= htmlspecialchars($row["equipment_id"]) ?></td>
      <td><?= htmlspecialchars($row["barcode"]) ?></td>
      <td><?= htmlspecialchars($row["name"]) ?></td>
      <td><?= htmlspecialchars($row["description"]) ?></td>
      <td><?= htmlspecialchars(passDownLocation($row["lab_id"])) ?></td>
      <td><?= htmlspecialchars($row["status"]) ?></td>
      <td><?= htmlspecialchars($row["registered_at"]) ?></td>
      <td>
        <a href="restore_archived_equipment.php?id=<?= urlencode($row["equipment_id"]) ?>" onclick="return confirm('Are you sure you want to restore this equipment?');">
          <button class="btn btn-delete">Restore</button>
        </a>

      </td>
    </tr>
  <?php endforeach; ?>
</table>

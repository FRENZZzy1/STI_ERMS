<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include("../../connect.php");
include("../pass-down-method.php");

// Get current lab from session
$lab = $_SESSION['selected_lab'] ?? null;

if (!$lab) {
    echo "<p style='color:red; text-align:center;'>No lab selected.</p>";
    exit;
}


// Only select 'Good' equipment
$status = 'Archived';
$stmt = $conn->prepare("SELECT * FROM equipment WHERE lab_id = ? AND status != ?");
$stmt->bind_param("is", $lab, $status);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);

if (empty($rows)) {
    echo "<p style='text-align:center; color:blue;'>No equipment found for this lab.</p>";
    exit;
}
?>

<style>
.action-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
    margin: 0 4px;
    border-radius: 6px;
    transition: all 0.3s ease;
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.action-btn:hover {
    transform: translateY(-2px);
}

.action-btn svg {
    width: 20px;
    height: 20px;
}

.btn-update {
    color: #3498db;
}

.btn-update:hover {
    background-color:rgb(57, 228, 228);
    color:rgb(25, 77, 112);
}

.btn-archive {
    color: #e74c3c;
}

.btn-archive:hover {
    background-color: #fde8e7;
}

.btn-history {
    color: #155E95;
}

.btn-history:hover {
    background-color: #e3f0f7;
}

.tooltip {
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #333;
    color: white;
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
    margin-bottom: 5px;
    z-index: 1000;
}

.tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: #333;
}

.action-btn:hover .tooltip {
    opacity: 1;
}

.actions-cell {
    white-space: nowrap;
}
</style>

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
      <td class="actions-cell">
        <button class="action-btn btn-update"
        data-id="<?= $row['equipment_id'] ?>"
        data-barcode="<?= htmlspecialchars($row['barcode']) ?>"
        data-name="<?= htmlspecialchars($row['name']) ?>"
        data-description="<?= htmlspecialchars($row['description']) ?>"
        data-lab="<?= htmlspecialchars($row['lab_id']) ?>"
        data-status="<?= htmlspecialchars($row['status']) ?>">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          <span class="tooltip">Update</span>
        </button>

        <a href="archive_equipment.php?id=<?= urlencode($row["equipment_id"]) ?>" 
           onclick="return confirm('Are you sure you want to archive this equipment?');"
           style="text-decoration: none;">
          <button class="action-btn btn-archive">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            <span class="tooltip">Archive</span>
          </button>
        </a>

        <a href="equipment_history.php?id=<?= urlencode($row["equipment_id"]) ?>"
           style="text-decoration: none;">
          <button class="action-btn btn-history">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="tooltip">History</span>
          </button>
        </a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
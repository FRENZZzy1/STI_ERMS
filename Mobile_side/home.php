<?php
include("mobile_page.php");
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: ../login.php");
  exit();
}

include("../connect.php");

$user_id = $_SESSION['user_id'];

// Get user role
$queryRole = "SELECT role FROM users WHERE user_id = ?";
$stmtRole = $conn->prepare($queryRole);
$stmtRole->bind_param("i", $user_id);
$stmtRole->execute();
$resultRole = $stmtRole->get_result();
$rowRole = $resultRole->fetch_assoc();
$role = $rowRole['role'];
$stmtRole->close();

// Fetch tasks (maintenance only)
$tasks = [];
if ($role === 'maintenance') {
  $queryTasks = "
      SELECT report_id, equipment_id, equipment_condition, remarks, location 
      FROM reports
      WHERE assigned_to = ?";
  $stmt = $conn->prepare($queryTasks);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
  }
  $stmt->close();
}

// Fetch recent history for ANY user
$history = [];
$stmtHist = $conn->prepare("
    SELECT h.equipment_id, e.name, e.lab_id, h.action, h.changed_at 
    FROM equipment_history h
    INNER JOIN equipment e ON h.equipment_id = e.equipment_id
    WHERE changed_by = ?
    ORDER BY changed_at DESC 
    LIMIT 20
");
$stmtHist->bind_param("i", $user_id);
$stmtHist->execute();
$resultHist = $stmtHist->get_result();

while ($rowHist = $resultHist->fetch_assoc()) {
    $history[] = $rowHist;
}
$stmtHist->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Maintenance Dashboard</title>
  <link rel="stylesheet" href="css/home.css">

  <style>
    .show-more-btn {
      display: block;
      width: 100%;
      background: #1e6bb8;
      color: white;
      border: none;
      border-radius: 10px;
      padding: 14px 20px;
      margin-top: 16px;
      font-size: 0.9375rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      font-family: inherit;
    }

    .show-more-btn:hover {
      background: #165a9c;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(30, 107, 184, 0.3);
    }

    .history-hidden {
      display: none;
    }

    .show-less-btn {
      background: #718096 !important;
    }
  </style>

</head>

<body>
  <div class="content">
    <div class="logo">
      <img src="sti-logo.png" alt="STI Logo" width="120">
    </div>
    <h2>Welcome to STI</h2>
    <h2>Equipment Reporting</h2>
    <h3>Monitoring System</h3>

    <!-- TASKS SECTION (Maintenance only) -->
    <?php if ($role === 'maintenance'): ?>
      <div class="section">
        <h3>Your To-Do Tasks</h3>

        <?php if (count($tasks) === 0): ?>
          <p class="no-task">No tasks assigned to you yet.</p>
        <?php else: ?>
          <?php foreach ($tasks as $task):
            $barcode = null;
            $stmt2 = $conn->prepare("SELECT barcode FROM equipment WHERE equipment_id = ?");
            $stmt2->bind_param("i", $task['equipment_id']);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            if ($row2 = $result2->fetch_assoc()) {
              $barcode = $row2['barcode'];
            }
            $stmt2->close();
          ?>
            <form method="POST" action="output.php" class="task-form">
              <input type="hidden" name="barcode" value="<?= htmlspecialchars($barcode) ?>">
              <button type="submit" class="task">
                <h4>Equipment #<?= htmlspecialchars($task['equipment_id']) ?></h4>
                <p><span class="label">Condition:</span> <?= htmlspecialchars($task['equipment_condition']) ?></p>
                <p><span class="label">Remarks:</span> <?= htmlspecialchars($task['remarks']) ?></p>
                <p><span class="label">Location:</span> <?= htmlspecialchars($task['location']) ?></p>
              </button>
            </form>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- HISTORY SECTION (ALL roles can see) -->
    <div class="section">
      <h3>Recent History</h3>

      <?php if (count($history) === 0): ?>
        <p class="no-task">No recent changes made by you.</p>
      <?php else: ?>

        <?php $initialShow = 2; ?>

        <div id="historyContainer">
          <?php foreach ($history as $index => $item): ?>
            <div class="history-item <?= $index >= $initialShow ? 'history-hidden' : '' ?>">
              <h4>Equipment: <?= htmlspecialchars($item['name']) ?></h4>
              <p><span class="label">Location:</span> <?= htmlspecialchars($item['lab_id']) ?></p>
              <p><span class="label">Change:</span> <?= htmlspecialchars($item['action']) ?></p>
              <p class="status"><span class="label">Date:</span> <?= htmlspecialchars($item['changed_at']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if (count($history) > $initialShow): ?>
          <button type="button" class="show-more-btn" id="toggleHistoryBtn" onclick="toggleHistory()">
            Show More (<?= count($history) - $initialShow ?> more)
          </button>
        <?php endif; ?>

      <?php endif; ?>
    </div>

  </div>

  <script>
    let isShowingAll = false;

    function toggleHistory() {
      const items = document.querySelectorAll('.history-hidden');
      const btn = document.getElementById('toggleHistoryBtn');

      isShowingAll = !isShowingAll;

      items.forEach(item => {
        item.style.display = isShowingAll ? 'block' : 'none';
      });

      if (isShowingAll) {
        btn.textContent = "Show Less";
        btn.classList.add("show-less-btn");
      } else {
        btn.textContent = "Show More (<?= count($history) - $initialShow ?> more)";
        btn.classList.remove("show-less-btn");
      }
    }
  </script>

</body>
</html>

<?php
include("../sidebar.php");
include("../../connect.php");
include("../pass-down-method.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


//eto ay manggagaling sa equipment_retieve
$user_id = $_GET['id'] ?? null;

//eto naman sa analytics nati
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['equipment_id'])) {
    $user_id = $_POST['equipment_id'];
}

if (!$user_id) {
    echo "<p style='color:red;text-align:center;'>Invalid user ID or equipment ID.</p>";
    exit;
}




try {
    // Get user info
    $stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Get history records
    $stmt = $conn->prepare("
        SELECT equipment_id, action, old_status, new_status, changed_at
        FROM equipment_history
        WHERE equipment_id = ?
        ORDER BY changed_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);

    // Get lab id
    $stmt = $conn->prepare("SELECT lab_id FROM equipment WHERE equipment_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $lab = $result->fetch_assoc()['lab_id'] ?? null;

    // Count totals
    $totals = [
        'Not Functional' => 0,
        'Replacement' => 0,
        'Good' => 0
    ];

    foreach ($rows as $r) {
        $status = strtolower($r['new_status']);
        if (strpos($status, 'not functional') !== false) $totals['Not Functional']++;
        if (strpos($status, 'replacement') !== false) $totals['Replacement']++;
        if (strpos($status, 'good') !== false || strpos($status, 'fixed') !== false) $totals['Good']++;
    }

    // Prepare chart data (Status Over Time)
    $dates = [];
    $statuses = [];
    $status_map = ['Not Functional' => 1, 'Replacement' => 2, 'Good' => 3];
    foreach ($rows as $row) {
        $dates[] = date("M d, Y", strtotime($row['changed_at']));
        $statuses[] = $status_map[$row['new_status']] ?? 0;
    }

    // Compute average time between reports
    $average_days = null;
    $dates_only = array_column($rows, 'changed_at');
    if (count($dates_only) > 1) {
        $diffs = [];
        for ($i = 1; $i < count($dates_only); $i++) {
            $diff = abs(strtotime($dates_only[$i]) - strtotime($dates_only[$i - 1]));
            $diffs[] = $diff / (60 * 60 * 24); // convert to days
        }
        $average_days = round(array_sum($diffs) / count($diffs), 1);
    }

} catch (Exception $e) {
    echo "<p style='color:red;text-align:center;'>Error loading history.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Equipment History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #e8e8e4, #d8e2dc, #ece4db);
            font-family: "Poppins", Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .main-content {
            flex-grow: 1;
            margin-left: 250px;
            padding: 30px;
            background: linear-gradient(135deg, #e8e8e4, #d8e2dc, #ece4db);
            min-height: 100vh;
        }

        h1 {
            text-align: center;
            color: #333;
            font-weight: 600;
        }

        .summary {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 25px;
            margin-bottom: 25px;
        }

        .summary-card {
            background: #fff;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 16px;
            font-weight: 500;
        }

        .summary-card i {
            font-size: 22px;
        }

        .not-functional i { color: #e53935; }
        .replacement i { color: #fb8c00; }
        .good i { color: #43a047; }
        .avg-time i { color: #1868b9; }

        .table-container {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: linear-gradient(135deg, #2196f3, #64b5f6);
            color: white;
            padding: 12px;
        }

        td {
            text-align: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        tr:nth-child(even) td {
            background: #f9fbff;
        }

        .btn-back {
            background: #666;
            color: #fff;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #555;
        }

        .chart-container {
            background: #fff;
            margin-top: 30px;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        canvas {
            width: 100% !important;
            height: 400px !important;
        }

         /* ✅ Dark mode */
       body.dark-mode {
    background: #121212;
    color: #e0e0e0;
}

body.dark-mode .main-content {
    background: #000000;
}

body.dark-mode .summary-card,
body.dark-mode .table-container,
body.dark-mode .chart-container {
    background: #16181c;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.03);
}

body.dark-mode th {
    background: #16181c;
}

body.dark-mode td {
    color: #dcdcdc;
}

body.dark-mode tr:nth-child(even) td {
    background: #292929;
}

body.dark-mode .btn-back {
    background: #333;
    color: #fff;
}

body.dark-mode .btn-back:hover {
    background: #444;
}

body.dark-mode h1 {
    color: #ffffff;
}






    </style>
</head>
<body>

<div class="main-content">
    <h1>
        History of <?= htmlspecialchars(passDownBarcode($user_id)) ?><br>
        (<?= htmlspecialchars(passDownLabName($lab)) ?>) - (<?= htmlspecialchars(passDownEquipmentName($user_id)) ?>)
    </h1>

    <div style="margin-bottom:15px;">
        <a href="equipment-retrieve.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back to Equipments</a>
    </div>

    <!-- Summary Cards -->
    <div class="summary">
        <div class="summary-card not-functional">
            <i class="fa-solid fa-triangle-exclamation"></i> Not Functional: <?= $totals['Not Functional'] ?>
        </div>
        <div class="summary-card replacement">
            <i class="fa-solid fa-repeat"></i> Replacement: <?= $totals['Replacement'] ?>
        </div>
        <div class="summary-card good">
            <i class="fa-solid fa-check-circle"></i> Good / Fixed: <?= $totals['Good'] ?>
        </div>
        <div class="summary-card avg-time">
            <i class="fa-solid fa-clock"></i> Avg. Time Between Reports:
            <?= $average_days !== null ? $average_days . ' days' : 'N/A' ?>
        </div>
    </div>

    <!-- Status Over Time Chart -->
    <div class="chart-container">
        <h3 style="text-align:center; color:#1868b9; margin-bottom:15px;">
            <i class="fa-solid fa-chart-line"></i> Status Over Time
        </h3>
        <canvas id="statusChart"></canvas>
    </div>

    <!-- History Table -->
    <div class="table-container">
        <?php if (!empty($rows)): ?>
            <table>
                <tr>
                    <th>Action</th>
                    <th>Equipment ID</th>
                    <th>Old Status</th>
                    <th>New Status</th>
                    <th>Changed At</th>
                </tr>
                <?php foreach ($rows as $row): ?>
                <tr>
                    <td>
                        <?php
                        $icon = '<i class="fa-solid fa-circle-info" style="color:#2196f3;"></i>';
                        if (stripos($row["action"], 'update') !== false) $icon = '<i class="fa-solid fa-pen-to-square" style="color:#4caf50;"></i>';
                        elseif (stripos($row["action"], 'delete') !== false) $icon = '<i class="fa-solid fa-trash" style="color:#f44336;"></i>';
                        elseif (stripos($row["action"], 'add') !== false) $icon = '<i class="fa-solid fa-plus-circle" style="color:#2196f3;"></i>';
                        ?>
                        <?= $icon ?> <?= htmlspecialchars($row["action"]) ?>
                    </td>
                    <td><?= htmlspecialchars($row["equipment_id"]) ?></td>
                    <td><?= htmlspecialchars($row["old_status"]) ?></td>
                    <td><?= htmlspecialchars($row["new_status"]) ?></td>
                    <td><?= htmlspecialchars($row["changed_at"]) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p style="text-align:center; color:blue;">No history found for this equipment.</p>
        <?php endif; ?>
    </div>
</div>

<script>
const ctx = document.getElementById('statusChart');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: <?= json_encode($dates) ?>,
    datasets: [{
      label: 'Status Trend',
      data: <?= json_encode($statuses) ?>,
      borderColor: '#1868b9',
      backgroundColor: '#64b5f6',
      fill: false,
      tension: 0.3,
      pointBackgroundColor: '#1868b9',
      borderWidth: 3
    }]
  },
  options: {
    scales: {
      y: {
        min: 0,
        max: 3,
        ticks: {
          stepSize: 1,
          callback: function(value) {
            const map = {1: 'Not Functional', 2: 'Replacement', 3: 'Good'};
            return map[value] || '';
          }
        },
        title: {
          display: true,
          text: 'Status'
        }
      },
      x: {
        title: {
          display: true,
          text: 'Date'
        }
      }
    },
    plugins: {
      legend: { display: false }
    }
  }
});
</script>

<script src="../darkmode.js"></script>

</body>
</html>

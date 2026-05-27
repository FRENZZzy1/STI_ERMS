<?php
include("../sidebar.php");
include("../../connect.php");
include("../pass-down-method.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    echo "<p style='color:red;text-align:center;'>Invalid user ID.</p>";
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
        WHERE changed_by = ?
        ORDER BY changed_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    echo "<p style='color:red;text-align:center;'>Error loading history.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User History</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(135deg, #e8e8e4, #d8e2dc, #ece4db);
            margin: 0;
            padding: 0;
            display: flex;
        }

        .main-content {
            flex-grow: 1;
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
        }

        h1 {
            text-align: center;
            color: #333;
            font-weight: 600;
        }

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

/* ===== DARK MODE (Matching Dashboard Colors) ===== */
body.dark-mode {
  background-color:rgb(0, 0, 0);
  color: #eaeaea;
  transition: all 0.3s ease;
}

body.dark-mode .main-content {
  background-color:rgb(0, 0, 0);
  color: #ffffff;
  transition: all 0.3s ease;
}

body.dark-mode h1 {
  color: #ffffff;
  transition: all 0.3s ease;
}

body.dark-mode .table-container {
  background-color: #1f1f1f;
  color: #ffffff;
  box-shadow: 0 2px 10px rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
}

body.dark-mode table {
  color: #ffffff;
}

body.dark-mode th {
  background: #16181c;
  color: #fff;
  transition: all 0.3s ease;
}

body.dark-mode td {
  background-color: #1f1f1f;
  border-bottom: 1px solid #16181c;
  transition: all 0.3s ease;
}

body.dark-mode tr:nth-child(even) td {
  background-color: #262626;
}

body.dark-mode .btn-back {
  background-color: #4da3ff;
  color: #000;
  transition: all 0.3s ease;
}

body.dark-mode .btn-back:hover {
  background-color: #66b4ff;
}

body.dark-mode p {
  color: #eaeaea;
  transition: all 0.3s ease;
}


    </style>
</head>
<body>

<div class="main-content">
    <h1>History of <?= htmlspecialchars($user['username']) ?></h1>
    <div style="margin-bottom:15px;">
        <a href="users.php" class="btn-back">⬅ Back to Users</a>
    </div>

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
                    <td><?= htmlspecialchars($row["action"]) ?></td>
                    <td><?= htmlspecialchars($row["equipment_id"]) ?></td>
                    <td><?= htmlspecialchars($row["old_status"]) ?></td>
                    <td><?= htmlspecialchars($row["new_status"]) ?></td>
                    <td><?= htmlspecialchars($row["changed_at"]) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p style="text-align:center; color:blue;">No history found for this user.</p>
        <?php endif; ?>
    </div>
</div>
<script src="../darkmode.js"></script>
</body>
</html>

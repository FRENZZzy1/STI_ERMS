<?php
include("../sidebar.php"); // Sidebar navigation

// Enable exceptions for mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include("../../connect.php");
include("../pass-down-method.php");

// 🔹 Handle reassignment request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reassign'])) {
    $reportId = $_POST['report_id'];
    $newAssignee = $_POST['new_assignee'];
    
    try {
        $stmt = $conn->prepare("UPDATE reports SET assigned_to = ? WHERE report_id = ?");
        $stmt->bind_param("ss", $newAssignee, $reportId);
        $stmt->execute();
        
        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (mysqli_sql_exception $e) {
        // Handle error silently
    }
}

// 🔹 Fetch maintenance users
$maintenanceUsers = [];
try {
    $userResult = mysqli_query($conn, "SELECT * FROM users WHERE role = 'maintenance'");
    $maintenanceUsers = mysqli_fetch_all($userResult, MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    // ignore errors silently
}

// 🔹 Function to get number of tasks assigned to user today
function getTodayTaskCount($conn, $userId) {
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reports WHERE assigned_to = ? AND DATE(reported_at) = ?");
    $stmt->bind_param("ss", $userId, $today);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    return $count;
}

// 🔹 Assign unassigned reports automatically (persistent round-robin)
try {
    $unassignedResult = mysqli_query($conn, "SELECT report_id FROM reports WHERE (assigned_to IS NULL OR assigned_to = '') AND State != 'Good' ORDER BY reported_at ASC");
    $unassignedReports = mysqli_fetch_all($unassignedResult, MYSQLI_ASSOC);

    if (!empty($maintenanceUsers) && !empty($unassignedReports)) {
        $userCount = count($maintenanceUsers);

        // fetch last assigned index
        $lastIndexResult = mysqli_query($conn, "SELECT last_index FROM task_assign_state WHERE id=1");
        $lastIndexRow = mysqli_fetch_assoc($lastIndexResult);
        $index = isset($lastIndexRow['last_index']) ? intval($lastIndexRow['last_index']) : 0;

        foreach ($unassignedReports as $report) {
            $userId = $maintenanceUsers[$index]['user_id'];

            $stmt = $conn->prepare("UPDATE reports SET assigned_to = ? WHERE report_id = ?");
            $stmt->bind_param("ss", $userId, $report['report_id']);
            $stmt->execute();

            // move to next user
            $index = ($index + 1) % $userCount;
        }

        // save last assigned index
        $stmt = $conn->prepare("UPDATE task_assign_state SET last_index = ? WHERE id = 1");
        $stmt->bind_param("i", $index);
        $stmt->execute();
    }
} catch (mysqli_sql_exception $e) {
    // silently fail if DB issue
}

try {
    $result = mysqli_query($conn, "SELECT * FROM reports WHERE State != 'Good' ORDER BY reported_at DESC");
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="reports.css">
    <meta charset="UTF-8">
    <title>Reports</title>
    <style>
        /* Reassign Button Styles */
        .reassign-btn {
            background: #4A90E2;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .reassign-btn:hover {
            background: #357ABD;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(74, 144, 226, 0.3);
        }

        .reassign-btn svg {
            width: 16px;
            height: 16px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .modal-body {
            margin-bottom: 25px;
        }

        .modal-body label {
            display: block;
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }

        .modal-body select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
            background: #f9f9f9;
            transition: all 0.3s ease;
        }

        .modal-body select:focus {
            outline: none;
            border-color: #4A90E2;
            background: #fff;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .confirm-btn {
            background: #4A90E2;
            color: white;
        }

        .confirm-btn:hover {
            background: #357ABD;
            transform: translateY(-2px);
        }

        .cancel-btn {
            background: #e0e0e0;
            color: #555;
        }

        .cancel-btn:hover {
            background: #d0d0d0;
        }

        /* Dark Mode Modal Styles */
        body.dark-mode .modal-content {
            background-color: #1f1f1f;
            color: #eaeaea;
        }

        body.dark-mode .modal-header {
            color: #eaeaea;
        }

        body.dark-mode .modal-body label {
            color: #cfcfcf;
        }

        body.dark-mode .modal-body select {
            background: #2a2a2a;
            border-color: #444;
            color: #eaeaea;
        }

        body.dark-mode .modal-body select:focus {
            border-color: #5B8DBE;
            background: #333;
        }

        body.dark-mode .reassign-btn {
            background: #5B8DBE;
        }

        body.dark-mode .reassign-btn:hover {
            background: #4A7CA8;
            box-shadow: 0 4px 8px rgba(91, 141, 190, 0.3);
        }

        body.dark-mode .confirm-btn {
            background: #5B8DBE;
        }

        body.dark-mode .confirm-btn:hover {
            background: #4A7CA8;
        }

        body.dark-mode .cancel-btn {
            background: #333;
            color: #cfcfcf;
        }

        body.dark-mode .cancel-btn:hover {
            background: #444;
        }
    </style>
</head>
<body>

<div class="main-content">
    <h1>Report List</h1>
    <div id="last-updated">Last updated: <span id="update-time"></span></div>

    <div class="table-container" id="report-table">
        <?php if (!empty($rows)): ?>
            <table>
                <tr>
                    <th>Report ID</th>
                    <th>Equipment ID</th>
                    <th>Reported By</th>
                    <th>Equipment Condition</th>
                    <th>Remarks</th>
                    <th>Time Reported</th>
                    <th>Location</th>
                    <th>Assigned to</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["report_id"]) ?></td>
                        <td><?= htmlspecialchars(passDownBarcode($row["equipment_id"])) ?></td>
                        <td><?= htmlspecialchars(passDownName($row["reported_by"])) ?></td>
                        <td><?= htmlspecialchars($row["equipment_condition"]) ?></td>
                        <td><?= htmlspecialchars($row["remarks"]) ?></td>
                        <td><?= htmlspecialchars($row["reported_at"]) ?></td>
                        <td><?= htmlspecialchars($row["location"]) ?></td>
                        <td><?= htmlspecialchars(passDownName($row["assigned_to"])) ?></td>
                        <td>
                            <button class="reassign-btn" onclick="openReassignModal('<?= htmlspecialchars($row['report_id']) ?>', '<?= htmlspecialchars($row['assigned_to']) ?>')">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                Reassign
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p style="text-align:center; color:blue;">No report data found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Reassign Modal -->
<div id="reassignModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">Reassign Report</div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="report_id" id="modal-report-id">
                <label for="new_assignee">Select New Assignee:</label>
                <select name="new_assignee" id="new_assignee" required>
                    <option value="">-- Select Maintenance User --</option>
                    <?php foreach ($maintenanceUsers as $user): ?>
                        <option value="<?= htmlspecialchars($user['user_id']) ?>">
                            <?= htmlspecialchars(passDownName($user['user_id'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="modal-btn cancel-btn" onclick="closeReassignModal()">Cancel</button>
                <button type="submit" name="reassign" class="modal-btn confirm-btn">Confirm</button>
            </div>
        </form>
    </div>
</div>

<script src="Notifpopup.js"></script>

<script>
function openReassignModal(reportId, currentAssignee) {
    document.getElementById('modal-report-id').value = reportId;
    document.getElementById('new_assignee').value = currentAssignee;
    document.getElementById('reassignModal').style.display = 'block';
}

function closeReassignModal() {
    document.getElementById('reassignModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('reassignModal');
    if (event.target === modal) {
        closeReassignModal();
    }
}

function refreshTable() {
    fetch("<?php echo $_SERVER['PHP_SELF']; ?>")
        .then(response => response.text())
        .then(data => {
            let parser = new DOMParser();
            let doc = parser.parseFromString(data, "text/html");
            let newTable = doc.querySelector("#report-table");
            document.querySelector("#report-table").innerHTML = newTable.innerHTML;

            const now = new Date();
            document.querySelector("#update-time").textContent = now.toLocaleTimeString();
        })
        .catch(err => console.error("Refresh failed:", err));
}

// auto refresh every 3 seconds
setInterval(refreshTable, 3000);

// set initial timestamp
document.querySelector("#update-time").textContent = new Date().toLocaleTimeString();
</script>

<script src="../darkmode.js"></script>

</body>
</html>
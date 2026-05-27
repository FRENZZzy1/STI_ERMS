<?php
session_start();
include("../connect.php"); // Database connection


//  PAGKUHA NG USER DETAILS

$userName = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];


//  INITIALIZATION NG VARIABLES

$barcode = $Name = $desc = $location = $item_status = "";
$error_message = "";
$success_message = "";


// UNANG PART: PAG SEARCH NG EQUIPMENT GAMIT ANG BARCODE

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['barcode'])) {
    $barcode = $_POST["barcode"];
    $_SESSION['barcode'] = $barcode;

    // Hanapin ang equipment sa database gamit ang barcode
    $sql = "SELECT * FROM equipment WHERE barcode = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $barcode);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Kung merong equipment na nahanap
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $eq_id = $row["equipment_id"];
        $Name = $row["name"];
        $desc = $row["description"];
        $lab_id = $row["lab_id"];
        $item_status = $row["status"];

        // Kunin din ang location ng laboratory base sa lab_id
        $sql = "SELECT location FROM laboratories WHERE lab_id = ?";
        $stmt2 = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt2, "s", $lab_id);
        mysqli_stmt_execute($stmt2);
        $res2 = mysqli_stmt_get_result($stmt2);

        if ($res2 && mysqli_num_rows($res2) > 0) {
            $loc_row = mysqli_fetch_assoc($res2);
            $location = $loc_row["location"];
        } else {
            $location = "Unknown Location";
        }
    } else {
        // Kapag walang nahanap na equipment
        $error_message = "⚠️ No equipment found for barcode: " . htmlspecialchars($barcode);
    }
}


//  PAG SUBMIT NG REPORT NG USER

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reason']) && empty($error_message)) {
    $reason = $_POST['reason'];

    

    // Siguraduhin na may laman ang barcode at reason
    if (!empty($barcode) && !empty($reason)) {
        // Maglagay ng bagong report sa 'reports' table
        $sql = "INSERT INTO reports (equipment_id, reported_by, equipment_condition, remarks, reported_at, location)
                VALUES (?, ?, ?, ?, NOW(), ?)";
        $stmt = mysqli_prepare($conn, $sql);
        $remarks = $_POST['remarks']; // user’s input
        mysqli_stmt_bind_param($stmt, "sssss", $eq_id, $user_id, $reason, $remarks, $lab_id);
        
        if (mysqli_stmt_execute($stmt)) {


            //  KUNIN ANG LUMANG STATUS NG EQUIPMENT BAGO I-UPDATE
            $stmt = $conn->prepare("SELECT status FROM equipment WHERE equipment_id = ?");
            $stmt->bind_param("i", $eq_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $oldStatus = $result->fetch_assoc()['status'] ?? null;


            // I-UPDATE ANG EQUIPMENT STATUS BASE SA REPORT
            $update_sql = "UPDATE equipment SET status = ? WHERE equipment_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "ss", $reason, $eq_id);
            mysqli_stmt_execute($update_stmt);

            //  MAGLAGAY NG LOG SA EQUIPMENT_HISTORY TABLE
            if ($reason != 'Good') {
                $insert = $conn->prepare("
                INSERT INTO equipment_history (equipment_id, action, old_status, new_status, changed_by, changed_at)
                VALUES (?, 'Reported', ?, ?, ?, NOW())
            ");
                $insert->bind_param("isss", $eq_id, $oldStatus, $reason, $user_id);
                $insert->execute();
            } else if ($reason == 'Good') {
                $insert = $conn->prepare("
                INSERT INTO equipment_history (equipment_id, action, old_status, new_status, changed_by, changed_at)
                VALUES (?, 'Fixed', ?, ?, ?, NOW())
            ");
                $insert->bind_param("isss", $eq_id, $oldStatus, $reason, $user_id);
                $insert->execute();
            }


            //  I-UPDATE LAHAT NG REPORTS PARA SA EQUIPMENT NA ITO
            $update_sql = "UPDATE reports SET equipment_condition = ? WHERE equipment_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "ss", $reason, $eq_id);
            mysqli_stmt_execute($update_stmt);


            // BURAHIN ANG MGA REPORTS NA 'GOOD' PARA MALINIS ANG DATA
            $delete_sql = "DELETE FROM reports WHERE equipment_condition = 'Good' AND equipment_id = ?";
            $delete_stmt = mysqli_prepare($conn, $delete_sql);
            mysqli_stmt_bind_param($delete_stmt, "s", $eq_id);
            mysqli_stmt_execute($delete_stmt);

            // Mensahe kapag matagumpay ang pag-submit
            $success_message = "✅ Report submitted successfully!";
        } else {
            $error_message = "❌ Database error: " . mysqli_error($conn);
        }
    } else {
        // Kapag kulang ang fields
        $error_message = "⚠️ Please provide all required fields.";
    }
}

// Isara ang database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Details</title>
    <link rel="stylesheet" href="../css/output.css">
</head>

<body>
    <!--USER INFORMATION-->
    <div class="user-info">
        👤 <?php echo htmlspecialchars($userName); ?>
    </div>

    <!--EQUIPMENT DETAILS CARD-->
    <div class="container">
        <div class="card">
            <?php if (!empty($error_message)): ?>
                <!-- Kung may error -->
                <div class="alert error"><?php echo $error_message; ?></div>
                <a href="search.php" class="btn back" style="width:100%;margin-top:10px;">⬅ Back</a>
                <script>
                    // Auto-redirect pabalik sa search page
                    setTimeout(() => window.location.href = "search.php", 2500);
                </script>
            <?php else: ?>
                <?php if (!empty($success_message)): ?>
                    <!-- Kung may success message -->
                    <div class="alert success"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <!--Ipakita ang detalye ng equipment-->
                <h2>Equipment Details</h2>
                <div class="info"><label>Barcode:</label><span><?php echo htmlspecialchars($barcode); ?></span></div>
                <div class="info"><label>Name:</label><span><?php echo htmlspecialchars($Name); ?></span></div>
                <div class="info"><label>Description:</label><span><?php echo htmlspecialchars($desc); ?></span></div>
                <div class="info"><label>Location:</label><span><?php echo htmlspecialchars($location); ?></span></div>
                <div class="info"><label>Status:</label><span class="status"><?php echo htmlspecialchars($item_status); ?></span></div>

                <!--Buttons para sa actions-->
                <div class="buttons">
                    <a href="search.php" class="btn back">⬅ Back</a>
                    <button class="btn report-btn" onclick="toggleReportForm(event)">📝 File Report</button>
                </div>

                <!--Form para sa pag-file ng report-->
                <form class="report-form" id="reportForm" action="output.php" method="POST">
                    <input type="hidden" name="barcode" value="<?php echo htmlspecialchars($barcode); ?>">
                    <label for="reason">Reason for filing the report</label>
                    <label for="reason">Reason for filing the report</label>
                    <select id="reason" name="reason" required>
                        <option value="">-- Select reason --</option>
                        <option value="missing">Missing part</option>
                        <option value="replacement">Needs part replacement</option>
                        <option value="not functional">Not functional</option>
                        <?php if ($role === 'maintenance'): ?>
                            <option value="Good">Good</option>
                        <?php endif; ?>
                    </select>

                    <label for="remarks">Describe the issue</label>
                    <textarea id="remarks" name="remarks" rows="4" placeholder="Example: The CPU fan is noisy or not spinning."></textarea>
                    <button type="submit" class="btn submit">Submit Report</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!--JS FUNCTION PARA SA FORM TOGGLE-->
    <script>
        function toggleReportForm(event) {
            event.preventDefault();
            const form = document.getElementById("reportForm");
            form.style.display = form.style.display === "block" ? "none" : "block";
        }
    </script>
</body>

</html>
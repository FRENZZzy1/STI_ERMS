<?php
include("../../connect.php"); // database connection

// ======= Add Lab Logic =======
if (isset($_POST['add_lab'])) {
    $lab_id = $_POST['lab_id'];
    $lab_name = $_POST['lab_name'];
    $floor = $_POST['floor'];

    $stmt = $conn->prepare("INSERT INTO Laboratories (lab_id, lab_name, location) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $lab_id, $lab_name, $floor);

    if ($stmt->execute()) {
        // success: reload the page to show the new lab
        echo "<script>alert('Laboratory added successfully!'); window.location='labs.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error adding laboratory.');</script>";
    }
}

// ======= Retrieve Labs =======
try {
    $sql = "SELECT * FROM Laboratories ORDER BY lab_name ASC";
    $result = mysqli_query($conn, $sql);
    $labs = mysqli_fetch_all($result, MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Error retrieving labs: " . $e->getMessage());
}
?>

<?php include("../sidebar.php"); // Sidebar navigation ?>
 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Laboratories</title>
  <link rel="stylesheet" href="../sidebar.css">
  <link rel="stylesheet" href="labs.css">
  <!--  Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
 
</head>
 
<body>
  <div class="main-content">
    <div class="container">
      <div class="header-row">
        <h1><i class="fa-solid fa-flask"></i> Laboratories</h1>
        <button class="btn" onclick="openModal()"><i class="fa-solid fa-plus"></i> New Lab</button>

      </div>
 
      <div class="lab-container">
        <?php if (!empty($labs)): ?>
          <?php foreach ($labs as $lab): ?>
            <?php
              $status = 'Archived';
              $stmt = $conn->prepare("SELECT COUNT(*) AS good_count FROM equipment WHERE lab_id = ? AND status != ?");
              $stmt->bind_param("is", $lab['lab_id'], $status);
              $stmt->execute();
              $result = $stmt->get_result();
              $good_count = $result->fetch_assoc()['good_count'] ?? 0;
            ?>
            <div class="lab-card"
                 onclick="goToEquipments('<?= htmlspecialchars($lab['lab_id']); ?>')"
                 onkeydown="if(event.key==='Enter') goToEquipments('<?= htmlspecialchars($lab['lab_id']); ?>')"
                 tabindex="0">
             
              <div class="lab-info">
                <div class="lab-title">
                  <i class="fa-solid fa-flask"></i> <?= htmlspecialchars($lab['lab_name']); ?>
                </div>
                <div class="lab-location">
                  <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($lab['location'] ?? '—'); ?>
                </div>
              </div>
 
              <div class="lab-footer">
                <div class="badge"><i class="fa-solid fa-microchip"></i> <?= htmlspecialchars($good_count); ?></div>
                <div class="view-text">
                  View Equipments <i class="fa-solid fa-arrow-right"></i>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="no-labs"><i class="fa-solid fa-triangle-exclamation"></i> No laboratories found in the database.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
 

    <!-- Hidden pop-up -->

<div id="addLabModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <h2><i class="fa-solid fa-flask"></i> Add New Laboratory</h2>
    <form method="POST">
      <label for="lab_id">Laboratory ID:</label>
      <input type="text" id="lab_id" name="lab_id" placeholder="e.g., LAB101" required>

      <label for="lab_name">Laboratory Name:</label>
      <input type="text" id="lab_name" name="lab_name" placeholder="e.g., Computer Laboratory 1" required>

      <label for="floor">Floor:</label>
      <select id="floor" name="floor" required>
        <option value="">-- Select Floor --</option>
        <option value="1st Floor">1st Floor</option>
        <option value="2nd Floor">2nd Floor</option>
        <option value="3rd Floor">3rd Floor</option>
        <option value="4th Floor">4th Floor</option>
        <option value="5th Floor">5th Floor</option>
        <option value="6th Floor">6th Floor</option>
        <option value="7th Floor">7th Floor</option>
      </select>

      <button type="submit" name="add_lab">Add Laboratory</button>
    </form>
  </div>
</div>



  <!-- Hidden POST form -->
  <form id="labForm" action="Equipment-retrieve.php" method="POST" style="display:none;">
    <input type="hidden" name="lab_id" id="labIdInput">
  </form>
 
  <script src="Notifpopup.js"></script>
  <script>
    function goToEquipments(labId) {
      document.getElementById('labIdInput').value = labId;
      document.getElementById('labForm').submit();
    }
  </script>
<script src="../darkmode.js"></script>

<script>
  function openModal() {
    document.getElementById("addLabModal").style.display = "flex";
  }

  function closeModal() {
    document.getElementById("addLabModal").style.display = "none";
  }

  // Close modal when clicking outside
  window.onclick = function(event) {
    const modal = document.getElementById("addLabModal");
    if (event.target === modal) closeModal();
  }
</script>


</body>
</html>

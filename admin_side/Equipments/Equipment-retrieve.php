<?php
include("../sidebar.php");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include("../../connect.php");

// Only set session if POST is sent (when lab is selected)
if (!empty($_POST['lab_id'])) {
  $_SESSION['selected_lab'] = $_POST['lab_id'];
}

$lab = $_SESSION['selected_lab'] ?? null;
$_SESSION['lab_id_equipment'] = $lab;

if (!$lab) {
  echo "<p style='color:red; text-align:center; margin-top:20px;'>No lab selected.</p>";
  exit;
}

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['equipment_id'])) {
  $id = intval($_POST['equipment_id']);
  $barcode = trim($_POST['barcode']);
  $name = trim($_POST['name']);
  $description = trim($_POST['description']);
  $lab_id = trim($_POST['lab_id']);
  $status = trim($_POST['status']);

  $stmt = $conn->prepare("UPDATE equipment SET barcode=?, name=?, description=?, lab_id=?, status=? WHERE equipment_id=?");
  $stmt->bind_param("sssssi", $barcode, $name, $description, $lab_id, $status, $id);
  $stmt->execute();
}

// Handle add equipment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']) && !isset($_POST['equipment_id'])) {
  $barcode = trim($_POST['barcode']);
  $name = trim($_POST['name']);
  $description = trim($_POST['description']);
  $lab_id = trim($_POST['lab_id']);
  $status = trim($_POST['status']);

  try {
    $stmt = $conn->prepare("INSERT INTO equipment (barcode, name, description, lab_id, status, registered_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $barcode, $name, $description, $lab_id, $status);
    $stmt->execute();
  } catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
      echo "<script>alert('⚠️ Duplicate entry detected!');</script>";
    } else {
      echo "<script>alert('❌ Error: " . addslashes($e->getMessage()) . "');</script>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Equipment Reports</title>
  <link rel="stylesheet" href="../sidebar.css">
  <link rel ="stylesheet" href = "equipments.css">
 
</head>

<body>
  <div class="main-content">
    <h1>Equipment List</h1>
    <h1><?= htmlspecialchars($lab) ?></h1>
    <div id="last-updated">Last updated: <span id="update-time"></span></div>

    <div style="text-align:left; margin-bottom:15px;">
      <a href="labs.php"><button class="btn btn-back" style="padding:10px 20px;">← Back to Labs</button></a>
      <button class="btn btn-add" style="padding:10px 20px;" onclick="openAddModal()">Add Equipment</button>

     
  <input 
    type="text" 
    id="searchInput" 
    placeholder="Search by barcode or name..."
  >

    </div>

   


    <div class="table-container" id="report-table"></div>
  </div>

  <!-- UPDATE MODAL -->
  <div id="updateModal">
    <form id="updateForm" method="POST" onsubmit="return confirmUpdate();">
      <h3>Update Equipment</h3>
      <input type="hidden" name="equipment_id" id="modal_equipment_id">

      <label>Barcode</label>
      <input type="text" name="barcode" id="modal_barcode" required>
      <label>Equipment Name</label>
      <input type="text" name="name" id="modal_name" required>
      <label>Description</label>
      <textarea name="description" id="modal_description" rows="3" required></textarea>
      <label>Lab ID</label>
      <input type="text" name="lab_id" id="modal_lab_id" required>
      <label>Status</label>
      <select name="status" id="modal_status" required>
        <option value="">-- Select Status --</option>
        <option value="Good">Good</option>
        <option value="Not functional">Not functional</option>
        <option value="Missing">Missing</option>
      </select>
      <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px;">
        <button type="submit">Update Equipment</button>
        <button id="cancelBtn" type="button" onclick="closeModal()" style="background:#f44336; color:white; border:none;">Cancel</button>
      </div>
    </form>
  </div>

  <!-- ADD MODAL -->
  <div id="addModal">
    <form id="addForm" method="POST" onsubmit="return confirmAdd();">
      <h3>Add Equipment</h3>
      <label>Barcode</label>
      <input type="text" name="barcode" id="add_barcode" required>
      <label>Equipment Name</label>
      <input type="text" name="name" id="add_name" required>
      <label>Description</label>
      <textarea name="description" id="add_description" rows="3" required></textarea>
      <label>Lab ID</label>
      <input type="text" name="lab_id" id="add_lab_id" value="<?= htmlspecialchars($lab) ?>" readonly required>
      <label>Status</label>
      <select name="status" id="add_status" required>
        <option value="">-- Select Status --</option>
        <option value="Good">Good</option>
        <option value="Archived">Archived</option>
        <option value="not functional">not functional</option>
      </select>
      <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px;">
        <button type="submit">Add Equipment</button>
        <button type="button" onclick="closeAddModal()" style="background:#f44336; color:white; border:none;">Cancel</button>
      </div>
    </form>
  </div>

  <div id="overlay" onclick="closeModal()"></div>
  <div id="overlayAdd" onclick="closeAddModal()"></div>

  <script>
    function refreshTable() {
      fetch("fetch_equipment.php")
        .then(response => response.text())
        .then(data => {
          const tableContainer = document.querySelector("#report-table");
          tableContainer.innerHTML = data;
          document.querySelector("#update-time").textContent = new Date().toLocaleTimeString();

          const modal = document.getElementById('updateModal');
          const overlay = document.getElementById('overlay');

          tableContainer.querySelectorAll('.btn-update').forEach(btn => {
            btn.addEventListener('click', function(e) {
              e.preventDefault();
              document.getElementById('modal_equipment_id').value = btn.dataset.id;
              document.getElementById('modal_barcode').value = btn.dataset.barcode;
              document.getElementById('modal_name').value = btn.dataset.name;
              document.getElementById('modal_description').value = btn.dataset.description;
              document.getElementById('modal_lab_id').value = btn.dataset.lab;
              document.getElementById('modal_status').value = btn.dataset.status;

              modal.style.display = 'block';
              overlay.style.display = 'block';
            });
          });

          window.closeModal = function() {
            modal.style.display = 'none';
            overlay.style.display = 'none';
          };
        }).catch(err => console.error("Refresh failed:", err));
    }

    function openAddModal() {
      document.getElementById('addModal').style.display = 'block';
      document.getElementById('overlayAdd').style.display = 'block';
    }

    function closeAddModal() {
      document.getElementById('addModal').style.display = 'none';
      document.getElementById('overlayAdd').style.display = 'none';
    }

    function confirmUpdate() {
      return confirm("Are you sure you want to update this equipment?");
    }

    function confirmAdd() {
      return confirm("Are you sure you want to add this equipment?");
    }

    // Initial load + auto-refresh every 3 seconds
    refreshTable();
    

    // Filter table by barcode or name
document.getElementById('searchInput').addEventListener('input', function() {
  const filter = this.value.toLowerCase();
  const tableRows = document.querySelectorAll('#report-table table tr');

  tableRows.forEach((row, index) => {
    if(index === 0) return; // skip header row
    const barcode = row.cells[1].textContent.toLowerCase();
    const name = row.cells[2].textContent.toLowerCase();

    if (barcode.includes(filter) || name.includes(filter)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
});




  </script>
<script src="../darkmode.js"></script>
</body>

</html>
<?php
session_start();

// Protect the page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /UPDATED/login.php");
    exit();
}

$userName = $_SESSION['username'];

//  Detect the current page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>STI ERMS</title>
  <link rel="stylesheet" href="../sidebar.css">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-header">
      <img src="side_logo.png" alt="STI Logo">
      <h2>STI ERMS</h2>
    </div>

    <div class="sidebar-menu">
      <a href="../Dashboard/samp.php" class="<?= $current_page === 'samp.php' ? 'active' : '' ?>">
        <span class="material-symbols-outlined">dashboard</span>
        <span>Dashboard</span>
      </a>

      <a href="../Equipments/labs.php" class="<?= $current_page === 'labs.php' ? 'active' : '' ?>">
        <span class="material-symbols-outlined">devices</span>
        <span>Equipments</span>
      </a>

      <a href="../Reports/Reports-retrieve.php" class="<?= $current_page === 'Reports-retrieve.php' ? 'active' : '' ?>">
        <span class="material-symbols-outlined">description</span>
        <span>Reports</span>
      </a>

      <a href="../Users/users.php" class="<?= $current_page === 'users.php' ? 'active' : '' ?>">
        <span class="material-symbols-outlined">group</span>
        <span>Users</span>
      </a>

      <a href="../Analytics/anayltic.php" class="<?= $current_page === 'anayltic.php' ? 'active' : '' ?>">
        <span class="material-symbols-outlined">analytics</span>
        <span>Analytics</span>
      </a>

      <a href="../Archived/archived_user_equipment.php" class="<?= $current_page === 'archived_user_equipment.php' ? 'active' : '' ?>">
        <span class="material-symbols-outlined">inventory_2</span>
        <span>Archived</span>
      </a>
    </div>

    <div class="sidebar-footer">
      <a href="../logout.php" class="logout">
        <span class="material-symbols-outlined">logout</span>
        <span>Logout</span>
      </a>
    </div>
  </div>



</body>
</html>

<?php
include("../sidebar.php");
include("../../connect.php");

// Ensure session has username
$userName = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Item Maintenance Reporting</title>
  <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="../sidebar.css">

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    .dashboard-cards {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 30px;
    }

    .card {
      background: #ffffff;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: space-between;
      /* remove flex: 1 1 220px; */
    }


    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    }

    .card h3 {
      font-size: 1.1rem;
      color: #333;
      margin-bottom: 5px;
    }

    .card p {
      font-size: 1.8rem;
      font-weight: bold;
      color: #003d73;
    }

    .card-icon {
      font-size: 2.5rem;
      color: #003d73;
      margin-right: 15px;
    }

    .card-content {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }

    .card-link {
      text-decoration: none;
      color: inherit;
      display: flex;
      /* important */
      flex: 1 1 220px;
      /* same as the original card flex */
    }


    .card-link .card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card-link .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    }
  </style>
</head>

<body>
  <div class="layout">

    <!-- ===== Main Content ===== -->
    <div class="content" id="main-content">
      <!-- Dark Mode Toggle Switch -->
<div class="dark-mode-toggle">
  <div class="toggle-switch" id="toggleDarkMode">
    <div class="toggle-slider">
      <i class="fa-solid fa-sun"></i>
    </div>
  </div>
</div>
      <h2>Welcome to Equipments Reporting Monitoring System</h2>
      <p>Select an option from the navigation bar or sidebar to get started.</p>
      <p>WELCOME BACK <strong><?php echo htmlspecialchars($userName); ?></strong></p>
    </div>

    <!-- ===== Dashboard Summary Cards ===== -->
    <div class="dashboard-cards">

      <!-- Total Equipments -->
      <div class="card">
        <i class="fa-solid fa-toolbox card-icon"></i>
        <div class="card-content">
          <h3>Total Equipments</h3>
          <p>
            <?php
            $equipResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM equipment");
            $equipRow = mysqli_fetch_assoc($equipResult);
            echo $equipRow['total'];
            ?>
          </p>
        </div>
      </div>

      <!-- Total Laboratories -->
      <a href="../Equipments/labs.php" class="card-link">
        <div class="card">
          <i class="fa-solid fa-door-open card-icon"></i>
          <div class="card-content">
            <h3>Total Laboratories</h3>
            <p>
              <?php
              $labResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM laboratories");
              $labRow = mysqli_fetch_assoc($labResult);
              echo $labRow['total'];
              ?>
            </p>
          </div>
        </div>
      </a>

      <!-- Total Users -->
      <a href="../Users/users.php" class="card-link">
        <div class="card">
          <i class="fa-solid fa-users card-icon"></i>
          <div class="card-content">
            <h3>Total Users</h3>
            <p>
              <?php
              $userResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
              $userRow = mysqli_fetch_assoc($userResult);
              echo $userRow['total'];
              ?>
            </p>
          </div>
        </div>
      </a>

      <!-- Today's Reports (Auto-refresh) -->
      <a href="../Reports/Reports-retrieve.php" class="card-link">
        <div class="card">
          <i class="fa-solid fa-file-circle-check card-icon"></i>
          <div class="card-content">
            <h3>Today's Reports</h3>
            <p id="today-reports-count">
              <?php
              $todayQuery = "SELECT COUNT(*) AS total FROM reports WHERE DATE(reported_at) = CURDATE()";
              $todayResult = mysqli_query($conn, $todayQuery);
              $todayRow = mysqli_fetch_assoc($todayResult);
              echo $todayRow['total'];
              ?>
            </p>
          </div>
        </div>
      </a>

    </div> <!-- end dashboard-cards -->
  </div>

  <script src="Notifpopup.js"></script>

  <!-- ===== Auto-Update for Today's Reports Only ===== -->
  <script>
    function updateTodaysReports() {
      fetch('fetch_today_reports.php')
        .then(response => response.json())
        .then(data => {
          document.getElementById('today-reports-count').textContent = data.today_reports;
        })
        .catch(err => console.error('Error updating today\'s reports:', err));
    }

    // Update every second (1000 ms)
    setInterval(updateTodaysReports, 1000);
  </script>


  <script src="../darkmode.js"></script>





</body>

</html>
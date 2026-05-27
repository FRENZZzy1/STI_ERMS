<?php include("../sidebar.php") ?>
<?php
include("../../connect.php");


// Count archived equipment
$archived_equipment_count = 0;
$sql = "SELECT COUNT(*) AS total FROM equipment WHERE status = 'archived'";
$result = mysqli_query($conn, $sql);
if ($result && $row = mysqli_fetch_assoc($result)) {
  $archived_equipment_count = $row['total'];
}

// Count archived users
$archived_user_count = 0;
$user_sql = "SELECT COUNT(*) AS total FROM users WHERE status = 'archived'";
$user_result = mysqli_query($conn, $user_sql);
if ($user_result && $urow = mysqli_fetch_assoc($user_result)) {
  $archived_user_count = $urow['total'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Archived Records</title>

  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

  <style>
    :root {
      --primary: #0f66c3;
      --bg: linear-gradient(135deg, #e8e8e4, #d8e2dc, #ece4db);
      --card: #ffffff;
      --muted: #555;
      --shadow: rgba(16, 24, 40, 0.08);
      --success: #16a34a;
    }

    html,
    body {
      height: 100%;
    }

    body {
      margin: 0;
      font-family: "Poppins", Arial, sans-serif;
      background: var(--bg);
    }

    .main-content {
      margin-left: 250px;
      padding: 50px;
      min-height: 100vh;
      box-sizing: border-box;
    }

    .container {
      max-width: 1300px;
      margin: 0 auto;
      text-align: center;
    }

    .header-row {
      margin-bottom: 30px;
    }

    h1 {
      font-size: 34px;
      color: var(--primary);
      font-weight: 700;
      margin: 0;
      text-align: center;
    }

    /* ===== Centered Card Container ===== */
    .lab-container {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      gap: 40px;
      padding-top: 80px;
      padding-bottom: 50px;
    }

    /* ===== Individual Card Styling ===== */
    .lab-card {
      background: var(--card);
      border-radius: 18px;
      padding: 28px 24px;
      box-shadow: 0 6px 22px var(--shadow);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      cursor: pointer;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 20px;
      border-top: 6px solid rgba(15, 102, 195, 0.15);
      width: 460px;
      min-height: 440px;
    }

    .lab-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 30px rgba(16, 24, 40, 0.15);
      border-top-color: var(--primary);
    }

    .lab-icon {
      font-size: 100px;
      color: var(--primary);
    }

    .lab-title {
      font-size: 28px;
      font-weight: 700;
      color: var(--primary);
    }

    .lab-stats {
      margin-top: auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
    }

    .stat {
      font-size: 24px;
      color: #222;
      font-weight: 600;
    }

    .stat small {
      display: block;
      font-weight: 500;
      color: var(--muted);
      font-size: 18px;
    }

    .badge {
      background: var(--success);
      color: #fff;
      padding: 10px 16px;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 600;
      display: inline-block;
      min-width: 45px;
      text-align: center;
    }

    .view-text {
      margin-top: 12px;
      font-size: 20px;
      color: var(--muted);
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 30px 15px;
      }

      .lab-card {
        width: 90%;
        min-height: 300px;
      }

      .lab-icon {
        font-size: 80px;
      }
    }


  /* ===== DARK MODE STYLING ===== */
body.dark-mode {
  background-color: #000000ff; /* true dark background */
  color: #eaeaea; /* soft text color */
  transition: all 0.3s ease;W
}

body.dark-mode .main-content {
  background-color: #121212; /* match body */
}

body.dark-mode .container {
  color: #eaeaea;
}

body.dark-mode h1 {
  color: #ffffff; /* dashboard heading color */
}

body.dark-mode .lab-card {
  background-color: #1f1f1f; /* dark card background */
  color: #eaeaea;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
  border-top: 6px solid #0f66c3;
  transition: all 0.3s ease;
}

body.dark-mode .lab-card:hover {
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.7);
  border-top-color: #4da3ff;
}

body.dark-mode .lab-icon {
  color: #4da3ff;
}

body.dark-mode .lab-title {
  color: #ffffff;
}

body.dark-mode .stat {
  color: #ffffff;
}

body.dark-mode .stat small {
  color: #cfcfcf;
}

body.dark-mode .badge {
  background-color: #0f66c3; /* blue badge like dashboard */
  color: #fff;
}

body.dark-mode .view-text {
  color: #cfcfcf;
}

  </style>

</head>

<body>
  <div class="main-content">
    <div class="container">
      <div class="header-row">
        <h1>Archived</h1>
      </div>

      <div class="lab-container">
        <!-- Archived Equipment card -->
        <div class="lab-card" onclick="goToEquipments()" tabindex="0">
          <i class="fa-solid fa-box-archive lab-icon"></i>
          <div class="lab-title">Archived Equipments</div>
          <div class="lab-stats">
            <span class="badge"><?= $archived_equipment_count ?></span>
            <small>Total Equipment</small>
          </div>
          <div class="view-text">View archived equipments →</div>
        </div>

        <!-- Archived Users card -->
        <div class="lab-card" onclick="goToUsers()" tabindex="0">
          <i class="fa-solid fa-user-slash lab-icon"></i>
          <div class="lab-title">Archived Users</div>
          <div class="lab-stats">
            <span class="badge"><?= $archived_user_count ?></span>
            <small>Total Users</small>
          </div>
          <div class="view-text">View archived users →</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Hidden POST form -->
  <form id="hiddenForm" method="POST" style="display:none;">
    <input type="hidden" name="lab_id" id="labIdInput">
  </form>

  <script src="Notifpopup.js"></script>

  <script>
    function goToEquipments() {
      const form = document.getElementById('hiddenForm');
      form.action = "archived_equipment_retrieve.php";
      form.submit();
    }

    function goToUsers() {
      const form = document.getElementById('hiddenForm');
      form.action = "archived_user_retrieve.php";
      form.submit();
    }
  </script>
  <script src="../darkmode.js"></script>
</body>

</html>
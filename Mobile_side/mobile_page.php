<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: /UPDATED/login.php");
  exit();
}
$userName = $_SESSION['username'];
?>

<!-- eto ung mag aact na side bar natin -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>STI Equipment Reporting Monitoring System</title>
  <link rel="stylesheet" href="mobile.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <!-- Navbar -->
  <div class="navbar">
    <div class="menu-icon" onclick="toggleMenu()"><i class="fa-solid fa-bars"></i></div>
    <div class="user-info">
      <a href="account.php" style="text-decoration:none; color:inherit; display:flex; align-items:center; gap:8px;">
        <span><?php echo htmlspecialchars($userName); ?></span>
        <div class="user-icon"><i class="fa-solid fa-user"></i></div>
      </a>
    </div>
  </div>

  <!-- Side Menu -->
  <div id="sideMenu" class="side-menu">

    <div class="side-header">
      <img src="sidebarlogo.png" alt="STI Logo">
      <h2>STI ERMS</h2>
    </div>

    <a href="home.php" class="btn"><i class="fa-solid fa-house"></i> Home</a>
    <a href="Search.php" class="btn"><i class="fa-solid fa-magnifying-glass"></i> Report Equipment</a>
    <a href="report-status.php" class="btn"><i class="fa-solid fa-file-lines"></i> Report Status</a>
    <a href="logout.php" class="logout-button"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>

  </div>

  <script>
    //eto ung script na kapag clinick  ung side menu lalaki ang sidebar natin
    function toggleMenu() {
      const sideMenu = document.getElementById("sideMenu");
      const overlay = document.getElementById("overlay");
      if (sideMenu.style.left === "0px") {
        sideMenu.style.left = "-280px";
        overlay.style.display = "none";
      } else {
        sideMenu.style.left = "0px";
        overlay.style.display = "block";
      }
    }

    //eto ung script na maglalagay ng contents sa sidebar natin
    function initPopupEvents() {
      document.querySelectorAll(".btn.view").forEach(button => {
        button.addEventListener("click", () => {
          const popup = document.getElementById("popup");
          if (popup) popup.style.display = "flex";
        });
      });

      const closeBtn = document.querySelector(".close-btn");
      if (closeBtn) {
        closeBtn.addEventListener("click", () => {
          document.getElementById("popup").style.display = "none";
        });
      }
    }
  </script>

</body>

</html>

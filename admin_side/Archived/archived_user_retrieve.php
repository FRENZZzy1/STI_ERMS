<?php include("../sidebar.php"); ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Archived Users</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: "Poppins", Arial, sans-seri;
      background: linear-gradient(135deg, #e8e8e4, #d8e2dc, #ece4db);;
      display: flex;
      flex-direction: row;
      height: 100vh;
      overflow-x: hidden;
    }

    .main-content {
      flex-grow: 1;
      margin-left: 250px;
      padding: 30px;
      min-height: 100vh;
      transition: all 0.3s ease;
      overflow-y: auto;
    }

    h1 {
      text-align: center;
      color: #333;
      margin-bottom: 10px;
      font-weight: 600;
    }

    #last-updated {
      text-align: center;
      color: #666;
      font-size: 14px;
      margin-bottom: 20px;
    }

    .table-container {
      width: 100%;
      overflow-x: auto;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      border-radius: 12px;
      background: #fff;
      padding: 20px;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      border-radius: 12px;
      overflow: hidden;
    }

    th {
      background: linear-gradient(135deg, #4c8bfd, #6fb1fc);
      color: #fff;
      text-align: center;
      padding: 14px 20px;
      font-weight: 600;
      font-size: 15px;
    }

    td {
      text-align: center;
      padding: 14px 20px;
      font-size: 14px;
      color: #333;
      border-bottom: 1px solid #eee;
    }

    tr:nth-child(even) td {
      background: #f9fbff;
    }

    tr:hover td {
      background: #e8f0ff;
      transition: 0.3s ease;
    }

    /* ===== BUTTONS ===== */
    .btn {
      border: none;
      padding: 8px 14px;
      border-radius: 6px;
      cursor: pointer;
      color: #fff;
      font-size: 13px;
      transition: background 0.3s ease;
    }

    .btn-update {
      background: #4caf50;
      /* original green */
    }

    .btn-update:hover {
      background: #45a049;
    }

    .btn-delete {
      background: #f44336;
    }

    .btn-back {
      background: #0f66c3;
      margin-right: 10px;
    }

    .btn-back:hover {
      background: #0d57a4;
    }

    /* Change to blue only when clicked */
    .filter-btn.btn-active {
      background: #0f66c3 !important;
      /* active blue */
    }

    .filter-btn.btn-active:hover {
      background: #0d57a4 !important;
      /* darker blue on hover */
    }

    .button-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      flex-wrap: wrap;
    }

    .right-buttons {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      justify-content: flex-end;
    }

    /* ============ FILTER BUTTONS ============ */
    .filter-btn {
      background: #e9eef6;
      color: #333;
      border: 1px solid #cdd5e0;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.25s ease;
    }

    .filter-btn:hover {
      background: #d7e5ff;
      color: #1e63e9;
      transform: translateY(-2px);
    }

    .btn-active {
      background: linear-gradient(135deg, #1e63e9, #3f8efc);
      color: #fff !important;
      border: none;
      box-shadow: 0 4px 12px rgba(30, 99, 233, 0.3);
      transform: scale(1.05);
    }



    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 15px;
      }

      .button-row {
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
      }

      .right-buttons {
        justify-content: center;
      }

      th,
      td {
        padding: 10px;
        font-size: 12px;
      }
    }

    /* ===== DARK MODE STYLING ===== */
body.dark-mode {
  background-color: #000000ff;
  color: #eaeaea;
  transition: all 0.3s ease;
}

body.dark-mode .main-content {
  background-color: #000000ff;
}

body.dark-mode h1 {
  color: #4da3ff;
}

body.dark-mode #last-updated {
  color: #b3b3b3;
}

body.dark-mode .table-container {
 background-color: #1f1f1f;
  box-shadow: 0 4px 20px rgba(255, 255, 255, 0.08);
}

body.dark-mode th {
    background: #16181c;
  color: #fff;
}

body.dark-mode td {
    background-color: #1f1f1f;
    color: #eaeaea;
    border-bottom: 1px solid #444;
}

body.dark-mode tr:nth-child(even) td {
    background-color: #1f1f1f;

}

body.dark-mode tr:hover td {
  background: #3a3a55;
}

body.dark-mode .filter-btn {
  background: #33354a;
  color: #fff;
  border: 1px solid #555;
}

body.dark-mode .filter-btn:hover {
  background: #4da3ff;
  color: #000;
}

body.dark-mode .btn-active {
  background: linear-gradient(135deg, #4da3ff, #3a78ff);
  color: #fff !important;
  border: none;
}

body.dark-mode .btn-back {
  background: #4da3ff;
  color: #000;
}

body.dark-mode .btn-back:hover {
  background: #3a78ff;
}


body.dark-mode .btn-delete {
  background: #e53935;
}



    
  </style>
</head>

<body>
  <div class="main-content">
    <h1>Archived User List</h1>
    <div id="last-updated">Last updated: <span id="update-time"></span></div>

    <div class="button-row">
      <div class="left-button">
        <a href="archived_user_equipment.php">
          <button class="btn btn-back" style="padding: 10px 20px;">← Back to Archived</button>
        </a>
      </div>

      <div class="right-buttons">
        <button class="btn btn-update filter-btn" data-role="admin" style="padding: 10px 20px; font-size: 14px;">Admins</button>
        <button class="btn btn-update filter-btn" data-role="staff" style="padding: 10px 20px; font-size: 14px;">Staffs</button>
        <button class="btn btn-update filter-btn" data-role="maintenance" style="padding: 10px 20px; font-size: 14px;">Maintenance</button>
        <button class="btn btn-update filter-btn" data-role="all" style="padding: 10px 20px; font-size: 14px;">Show All</button>
      </div>
    </div>

    <div class="table-container" id="report-table">
      <!-- Dynamic table -->
    </div>
  </div>

  <script>
    const tableContainer = document.querySelector("#report-table");
    const updateTime = document.querySelector("#update-time");

    function loadUsers(role = "all") {
      fetch(`fetch_archived_user.php?role=${role}`)
        .then(res => res.text())
        .then(data => {
          tableContainer.innerHTML = data;
          updateTime.textContent = new Date().toLocaleTimeString();
        })
        .catch(err => {
          tableContainer.innerHTML = "<p style='color:red; text-align:center;'>Failed to load data.</p>";
          console.error(err);
        });
    }

    // Default load
    loadUsers("all");

    const buttons = document.querySelectorAll(".filter-btn");
    buttons.forEach(btn => {
      btn.addEventListener("click", () => {
        buttons.forEach(b => b.classList.remove("btn-active"));
        btn.classList.add("btn-active");
        loadUsers(btn.dataset.role);
      });
    });
  </script>
  <script src="Notifpopup.js"></script>
  <script src="../darkmode.js"></script>
</body>

</html>
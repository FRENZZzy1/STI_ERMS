<?php include("../sidebar.php"); ?>





<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Equipment Reports</title>
  <style>
    /* ===== GLOBAL RESET ===== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: "Poppins", Arial, sans-seri;
      background: linear-gradient(135deg, #e8e8e4, #d8e2dc, #ece4db);
      display: flex;
      flex-direction: row;
      height: 100vh;
      overflow-x: hidden;
    }

    /* ===== MAIN CONTENT ===== */
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
    }

    .btn-delete {
      background: #f44336;
    }

    .btn-back {
      background: #0f66c3;
      margin-right: 10px;
    }

    .btn-update:hover {
      background: #45a049;
    }

    .btn-delete:hover {
      background: #e53935;
    }

    .btn-back:hover {
      background: #0d57a4;
    }



    /* ===== RESPONSIVE ADJUSTMENTS ===== */
    @media (max-width: 1024px) {
      body {
        flex-direction: column;
      }

      .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        flex-direction: row;
        justify-content: space-around;
        padding: 10px;
      }

      .main-content {
        margin-left: 0;
        padding: 20px;
      }

      table {
        font-size: 13px;
      }
    }

    @media (max-width: 768px) {
      .sidebar {
        flex-wrap: wrap;
      }

      .sidebar a {
        font-size: 14px;
        padding: 8px;
      }

      .main-content {
        padding: 15px;
      }

      th,
      td {
        padding: 10px;
        font-size: 12px;
      }
    }

    /* ===== DARK MODE ===== */
    /* ===== DARK MODE ===== */
body.dark-mode {
  background-color: #000000ff; /* darker, modern background */
  color: #e0e0e0; /* softer text */
  transition: all 0.3s ease;
}

body.dark-mode h1 {
  color: #4da3ff; /* brighter heading */
}

body.dark-mode #last-updated {
  color: #aaaaaa; /* softer subtext */
}

body.dark-mode .main-content {
  background-color: #000000ff; /* slightly lighter than body for contrast */
}

body.dark-mode .table-container {
     background-color: #1f1f1f;

  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
}

body.dark-mode table {
  color: #e0e0e0;
    background-color: #1f1f1f;

}

body.dark-mode th {
  background: #16181c;
  color: #ffffff; /* brighter for contrast */
}

body.dark-mode td {
  color: #dcdcdc;
  border-bottom: 1px solid #000000ff;
}

body.dark-mode tr:nth-child(even) td {
    background-color: #1f1f1f;

}

body.dark-mode tr:hover td {
    background-color: #1f1f1f;
  transition: 0.3s ease;
}

/* Buttons in dark mode */
body.dark-mode .btn-back {
  background-color: #4da3ff;
  color: #fff;
}

body.dark-mode .btn-update {
  background-color: #27ae60;
}

body.dark-mode .btn-delete {
  background-color: #e74c3c;
}

body.dark-mode .btn-back:hover {
  background-color: #75b7ff;
}

body.dark-mode .btn-update:hover {
  background-color: #1e8449;
}

body.dark-mode .btn-delete:hover {
  background-color: #c0392b;
}

  </style>
</head>

<body>

  <div class="main-content">
    <h1>Archived Equipment List</h1>
    <div id="last-updated">Last updated: <span id="update-time"></span></div>

    <!--  Back + Add Buttons -->
    <div style="text-align: left; margin-bottom: 15px;">
      <a href="archived_user_equipment.php">
        <button class="btn btn-back" style="padding: 10px 20px;">← Back to Archived</button>
      </a>
    </div>

    <div class="table-container" id="report-table">
      <!-- dito malalagay ung finetch na table galing fetch -->
    </div>
  </div>

  <script>
    //  Function to refresh equipment table
    function refreshTable() {
      fetch("fetch_archived_equipment.php") // use new fetch file
        .then(response => response.text())
        .then(data => {
          document.querySelector("#report-table").innerHTML = data;
          document.querySelector("#update-time").textContent = new Date().toLocaleTimeString();
        })
        .catch(err => console.error("Refresh failed:", err));
    }

    // Initial load + every 3 seconds
    refreshTable();
    setInterval(refreshTable, 3000);
  </script>

  <script src="Notifpopup.js"></script>
  <script src="../darkmode.js"></script>

</body>

</html>
<?php
include("../sidebar.php"); // Sidebar navigation

// Enable exceptions for mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include("../../connect.php");
include("../pass-down-method.php");

// =====================
// Handle Add User POST
// =====================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["username"]) && !isset($_POST["action"])) {
  header('Content-Type: application/json; charset=utf-8');
  ob_clean(); // clear unwanted output

  $username = trim($_POST["username"]);
  $password = trim($_POST["password"]);
  $role = trim($_POST["role"]);
  $status = "offline"; // Default status to active

  // Hash password BEFORE storing
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  try {
    $stmt = $conn->prepare("
      INSERT INTO users (user_id, username, password, role, status, created_at)
      VALUES (NULL, ?, ?, ?, ?, NOW())
    ");
    
    // Use hashed password here
    $stmt->bind_param("ssss", $username, $hashedPassword, $role, $status);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["success" => true]);

  } catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
  }

  exit;
}



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "update_user") {
  header('Content-Type: application/json; charset=utf-8');
  ob_clean();

  $id = $_POST["user_id"];
  $username = trim($_POST["username"]);
  $password = trim($_POST["password"]);
  $role = trim($_POST["role"]);


  try {

    // STEP 1: Get the existing password from DB
    $get = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $get->bind_param("i", $id);
    $get->execute();
    $result = $get->get_result();
    $row = $result->fetch_assoc();
    $currentPasswordDB = $row["password"];
    $get->close();

    // STEP 2: Determine final password to store
    if (empty($password)) {
        // Admin did not enter a new password → keep old hashed password
        $finalPassword = $currentPasswordDB;

    } elseif (strpos($password, '$2y$') === 0) {
        // Provided password is already hashed → keep it
        $finalPassword = $password;

    } else {
        // Provided password is plain → hash it
        $finalPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    // STEP 3: Update user record
    $stmt = $conn->prepare("
      UPDATE users 
      SET username = ?, password = ?, role = ?
      WHERE user_id = ?
    ");
    $stmt->bind_param("sssi", $username, $finalPassword, $role, $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["success" => true]);

  } catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
  }

  exit;
}





// =====================
// Fetch Users Table
// =====================
$roleFilter = isset($_GET['role']) ? $_GET['role'] : null;
try {
  if ($roleFilter && $roleFilter !== 'all') {
  $stmt = $conn->prepare("SELECT * FROM users WHERE role = ? AND status != 'archived'");
  $stmt->bind_param("s", $roleFilter);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $result = mysqli_query($conn, "SELECT * FROM users WHERE status != 'archived'");
}

  $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
} catch (mysqli_sql_exception $e) {
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>User Reports</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="users.css">
  <style>
    /* Action Button Icons */
    .action-btn {
      background: none;
      border: none;
      cursor: pointer;
      padding: 8px;
      margin: 0 4px;
      border-radius: 6px;
      transition: all 0.3s ease;
      position: relative;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .action-btn:hover {
      transform: translateY(-2px);
    }

    .action-btn i {
      font-size: 18px;
    }

    .btn-update-icon {
      color: #3498db;
    }

    .btn-update-icon:hover {
      background-color: #e8f4f8;
    }

    .btn-archive-icon {
      color: #e74c3c;
    }

    .btn-archive-icon:hover {
      background-color: #fde8e7;
    }

    .btn-history-icon {
      color: #155E95;
    }

    .btn-history-icon:hover {
      background-color: #e3f0f7;
    }

    .tooltip {
      position: absolute;
      bottom: 100%;
      left: 50%;
      transform: translateX(-50%);
      background-color: #333;
      color: white;
      padding: 6px 10px;
      border-radius: 4px;
      font-size: 12px;
      white-space: nowrap;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease;
      margin-bottom: 5px;
      z-index: 1000;
    }

    .tooltip::after {
      content: '';
      position: absolute;
      top: 100%;
      left: 50%;
      transform: translateX(-50%);
      border: 5px solid transparent;
      border-top-color: #333;
    }

    .action-btn:hover .tooltip {
      opacity: 1;
    }

    .actions-cell {
      white-space: nowrap;
    }

    /* Search Bar Styles */
    .search-container {
      position: relative;
      margin-bottom: 0;
      vertical-align: middle;
    }

    .search-box {
      width: 280px;
      padding: 10px 40px 10px 15px;
      font-size: 14px;
      border: 2px solid #ddd;
      border-radius: 25px;
      outline: none;
      transition: all 0.3s ease;
    }

    .search-box:focus {
      border-color: #4c8bfd;
      box-shadow: 0 0 8px rgba(76, 139, 253, 0.3);
    }

    .search-icon {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #999;
      font-size: 16px;
      pointer-events: none;
    }

    .no-results {
      text-align: center;
      padding: 40px;
      color: #e74c3c;
      font-size: 16px;
    }

       body.dark-mode .search-box {
      background-color: #2d2d2d;
      color: #ffffff;
      border: 2px solid #444;
    }

  </style>
</head>

<body>
  <div class="main-content">
    <h1>User List</h1>
    <div id="last-updated">Last updated: <span id="update-time"></span></div>

    <div class="button-row">
      <button class="btn btn-update" id="openAddUserBtn">+ Add User</button>
      

      <div class="right-buttons">
        <!-- Search Bar -->
        <div class="search-container" style="display: inline-block; margin-right: 15px;">
          <input type="text" 
                 id="searchInput" 
                 class="search-box" 
                 placeholder="Search by username, role, or status...">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>
        
        <button class="btn filter-btn" data-role="admin">Admins</button>
        <button class="btn filter-btn" data-role="staff">Staffs</button>
        <button class="btn filter-btn" data-role="maintenance">Maintenance</button>
        <button class="btn filter-btn btn-active" data-role="all">Show All</button>
        
      </div>
    </div>

    <div class="table-container" id="report-table">
      <?php if (!empty($rows)): ?>
        <table id="userTable">
          <thead>
            <tr>
              <th>User ID</th>
              <th>Username</th>
              <th>Password</th> 
              <th>Role</th>
              <th>Status</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row["user_id"]) ?></td>
                <td><?= htmlspecialchars($row["username"]) ?></td>
                <td>
                  <span class="pwd" data-pwd="<?= htmlspecialchars($row["password"]) ?>">••••••</span>
                  <button class="eye-btn" style="border:none;background:none;cursor:pointer;font-size:16px;color:#4c8bfd;">
                    <i class="fa-solid fa-eye"></i>
                  </button>
                </td>
                <td><?= htmlspecialchars($row["role"]) ?></td>
                <td><?= htmlspecialchars($row["status"]) ?></td>
                <td><?= htmlspecialchars($row["created_at"]) ?></td>
                <td class="actions-cell">
                  <a href="update_user.php?id=<?= urlencode($row["user_id"]) ?>" style="text-decoration: none;">
                    <button class="action-btn btn-update-icon btn-update">
                      <i class="fa-solid fa-pen-to-square"></i>
                      <span class="tooltip">Update</span>
                    </button>
                  </a>
                  
                  <a href="archive_user.php?id=<?= urlencode($row["user_id"]) ?>" 
                     onclick="return confirm('Are you sure you want to delete this user?');"
                     style="text-decoration: none;">
                    <button class="action-btn btn-archive-icon">
                      <i class="fa-solid fa-box-archive"></i>
                      <span class="tooltip">Archive</span>
                    </button>
                  </a>
                  
                  <a href="user_history.php?id=<?= urlencode($row["user_id"]) ?>" style="text-decoration: none;">
                    <button class="action-btn btn-history-icon">
                      <i class="fa-solid fa-clock-rotate-left"></i>
                      <span class="tooltip">History</span>
                    </button>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p style="text-align:center; color:blue;">No user data found.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Add User Modal -->
  <div id="addUserModal">
    <form id="addUserForm">
      <h2>Add User</h2>
      <label>Username</label>
      <input type="text" name="username" required>
      <label>Password</label>
      <input type="password" name="password" required pattern=".{8,}" title="Password must be at least 8 characters">
      <label>Role</label>
      <select name="role" required>
        <option value="">-- Select Role --</option>
        <option value="admin">Admin</option>
        <option value="maintenance">Maintenance</option>
        <option value="staff">Staff</option>
      </select>
      <div style="display:flex;flex-direction:column;gap:10px;margin-top:10px;">
        <button type="submit" style="background:#4c8bfd;">Add User</button>
        <button type="button" onclick="closeAddUserModal()" style="background:#f44336;color:white;border:none;">Cancel</button>
      </div>
    </form>
  </div>

  
  <div id="addUserOverlay" onclick="closeAddUserModal()"></div>

  
  <!-- ============================== -->
  <!-- UPDATE USER MODAL (RE-DESIGNED) -->
  <!-- ============================== -->
  
  <div id="updateUserOverlay"></div>

  <div id="updateUserModal">
    <form id="updateUserForm">
      <h2>Update User</h2>

      <input type="hidden" name="user_id" id="update_user_id">

      <label for="update_username">Username</label>
      <input type="text" name="username" id="update_username" required>

      <label for="update_password">Password</label>
      <input type="text" name="password" id="update_password" required>

      <label for="update_role">Role</label>
      <select name="role" id="update_role" required>
        <option value="">-- Select Role --</option>
        <option value="admin">Admin</option>
        <option value="maintenance">Maintenance</option>
        <option value="staff">Staff</option>
      </select>

      <div class="button-group">
        <button type="submit">Save Changes</button>
        <button type="button" onclick="closeUpdateUserModal()">Cancel</button>
      </div>
    </form>
  </div>

  <script>
  // ===========================
  // Timestamp Auto-Updater
  // ===========================
  setInterval(() => {
    document.querySelector("#update-time").textContent = new Date().toLocaleTimeString();
  }, 3000);
  document.querySelector("#update-time").textContent = new Date().toLocaleTimeString();


  // ===========================
  // Toggle Password Visibility
  // ===========================
  document.addEventListener("click", e => {
    if (e.target.closest(".eye-btn")) {
      const btn = e.target.closest(".eye-btn");
      const span = btn.previousElementSibling;
      const icon = btn.querySelector("i");
      const hidden = span.textContent.includes("•");
      span.textContent = hidden ? span.dataset.pwd : "••••••";
      icon.classList.toggle("fa-eye");
      icon.classList.toggle("fa-eye-slash");
    }
  });


  // ===========================
  // Track Current Filter State
  // ===========================
  let currentRoleFilter = "all"; // Default = show all users


  // ===========================
  // SEARCH FUNCTIONALITY
  // ===========================
  function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    const userTable = document.getElementById('userTable');
    
    if (!searchInput || !userTable) return;

    // Remove all existing keyup listeners
    searchInput.replaceWith(searchInput.cloneNode(true));
    const newSearchInput = document.getElementById('searchInput');
    
    newSearchInput.addEventListener('keyup', function() {
      const searchTerm = this.value.toLowerCase().trim();
      const tbody = userTable.querySelector('tbody');
      
      if (!tbody) return;
      
      const rows = tbody.querySelectorAll('tr');
      let visibleCount = 0;

      rows.forEach(row => {
        const userId = row.cells[0].textContent.toLowerCase();
        const username = row.cells[1].textContent.toLowerCase();
        const role = row.cells[3].textContent.toLowerCase();
        const status = row.cells[4].textContent.toLowerCase();
        
        const isMatch = userId.includes(searchTerm) || 
                        username.includes(searchTerm) || 
                        role.includes(searchTerm) || 
                        status.includes(searchTerm);
        
        if (isMatch) {
          row.style.display = '';
          visibleCount++;
        } else {
          row.style.display = 'none';
        }
      });

      // Show "No results" message if nothing matches
      let noResultsMsg = document.getElementById('noResultsMsg');
      if (visibleCount === 0 && searchTerm !== '') {
        if (!noResultsMsg) {
          noResultsMsg = document.createElement('div');
          noResultsMsg.id = 'noResultsMsg';
          noResultsMsg.className = 'no-results';
          noResultsMsg.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> No users found matching "' + searchTerm + '"';
          userTable.parentElement.appendChild(noResultsMsg);
        }
        userTable.style.display = 'none';
      } else {
        if (noResultsMsg) {
          noResultsMsg.remove();
        }
        userTable.style.display = '';
      }
    });
  }

  // Initialize search on page load
  initializeSearch();


  // ===========================
  // Filter Buttons Logic
  // ===========================
  document.querySelectorAll(".filter-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("btn-active"));
      btn.classList.add("btn-active");

      // ✅ Remember which filter is active
      currentRoleFilter = btn.dataset.role;

      // Clear search when changing filters
      const searchInput = document.getElementById('searchInput');
      if (searchInput) searchInput.value = '';

      const url = currentRoleFilter === "all"
        ? "<?= $_SERVER['PHP_SELF']; ?>"
        : "<?= $_SERVER['PHP_SELF']; ?>?role=" + currentRoleFilter;

      fetch(url)
        .then(res => res.text())
        .then(data => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(data, "text/html");
          document.querySelector("#report-table").innerHTML =
            doc.querySelector("#report-table").innerHTML;
          document.querySelector("#update-time").textContent = new Date().toLocaleTimeString();
          
          // ✅ Re-initialize search after table refresh
          setTimeout(() => {
            initializeSearch();
          }, 100);
        })
        .catch(err => console.error("Filter load error:", err));
    });
  });


  // ===========================
  // Modal: Add User
  // ===========================
  const addModal = document.getElementById("addUserModal");
  const addOverlay = document.getElementById("addUserOverlay");

  document.getElementById("openAddUserBtn").addEventListener("click", () => {
    addModal.style.display = "block";
    addOverlay.style.display = "block";
  });

  function closeAddUserModal() {
    addModal.style.display = "none";
    addOverlay.style.display = "none";
    document.getElementById("addUserForm").reset(); // 🧹 always reset when closed
  }

  // ===========================
  // Reusable Table Refresh
  // ===========================
  function refreshUserTable() {
    const url = currentRoleFilter === "all"
      ? "<?= $_SERVER['PHP_SELF']; ?>"
      : "<?= $_SERVER['PHP_SELF']; ?>?role=" + currentRoleFilter;

    fetch(url)
      .then(res => res.text())
      .then(data => {
        const doc = new DOMParser().parseFromString(data, "text/html");
        document.querySelector("#report-table").innerHTML =
          doc.querySelector("#report-table").innerHTML;
        document.querySelector("#update-time").textContent = new Date().toLocaleTimeString();
        
        // Clear search after refresh
        const searchInput = document.getElementById('searchInput');
        if (searchInput) searchInput.value = '';
        
        // ✅ Re-initialize search after table refresh
        setTimeout(() => {
          initializeSearch();
        }, 100);
      })
      .catch(err => console.error("Refresh error:", err));
  }

  // ===========================
  // AJAX Add User
  // ===========================
  document.getElementById("addUserForm").addEventListener("submit", function (e) {
    e.preventDefault();

    if (!confirm("Are you sure you want to add this user?")) return;

    const formData = new FormData(this);

    fetch("<?= $_SERVER['PHP_SELF']; ?>", {
      method: "POST",
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(async res => {
        const text = await res.text();
        try {
          return JSON.parse(text);
        } catch (e) {
          console.error("❌ Invalid JSON response:", text);
          throw new Error("Server did not return valid JSON");
        }
      })
      .then(data => {
        if (data.success) {
          refreshUserTable(); // ✅ reload table with current filter
          document.getElementById("addUserForm").reset(); // 🧹 clear all fields
          closeAddUserModal();
        } else {
          alert("⚠️ Failed to add user: " + (data.error || "Unknown error"));
        }
      })
      .catch(err => {
        console.error("Add user error:", err);
        alert("❌ An error occurred while adding user. Check console for details.");
      });
  });


  // ===========================
  // UPDATE USER MODAL
  // ===========================
  const updateModal = document.getElementById("updateUserModal");
  const updateOverlay = document.getElementById("updateUserOverlay");

  function openUpdateUserModal(data) {
    document.getElementById("update_user_id").value = data.user_id;
    document.getElementById("update_username").value = data.username;
    document.getElementById("update_password").value = data.password;
    document.getElementById("update_role").value = data.role;
    updateModal.style.display = "block";
    updateOverlay.style.display = "block";
  }

  function closeUpdateUserModal() {
    updateModal.style.display = "none";
    updateOverlay.style.display = "none";
    document.body.style.overflow = "auto";
    document.getElementById("updateUserForm").reset(); // 🧹 reset on close too
  }

  // Handle Update Button Clicks
  document.addEventListener("click", e => {
    if (e.target.closest(".btn-update-icon") && e.target.closest("a[href^='update_user.php']")) {
      e.preventDefault();
      const row = e.target.closest("tr");
      const userData = {
        user_id: row.cells[0].textContent.trim(),
        username: row.cells[1].textContent.trim(),
        password: row.querySelector(".pwd").dataset.pwd,
        role: row.cells[3].textContent.trim()
      };
      openUpdateUserModal(userData);
    }
  });

  // Handle Update Form Submission
  document.getElementById("updateUserForm").addEventListener("submit", function (e) {
    e.preventDefault();
    if (!confirm("Save changes to this user?")) return;

    const formData = new FormData(this);
    formData.append("action", "update_user");

    fetch("<?= $_SERVER['PHP_SELF']; ?>", {
      method: "POST",
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(async res => {
        const text = await res.text();
        try {
          return JSON.parse(text);
        } catch (e) {
          console.error("❌ Invalid JSON response:", text);
          throw new Error("Invalid JSON from server.");
        }
      })
      .then(data => {
        if (data.success) {
          closeUpdateUserModal(); // ✅ close modal
          refreshUserTable();     // ✅ reload current filter view
        } else {
          alert("⚠️ Update failed: " + (data.error || "Unknown error"));
        }
      })
      .catch(err => {
        console.error("Update error:", err);
        alert("❌ An error occurred while updating user. Check console for details.");
      });
  });
</script>

<script src="../darkmode.js"></script>

</body>

</html>
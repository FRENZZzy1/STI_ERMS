<?php
session_start();
include("../connect.php");

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: /UPDATED/login.php");
  exit();
}

$username = $_SESSION['username'];

// Fetch user details
$stmt = $conn->prepare("SELECT username, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle username update
$message = "";
$messageType = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["update_username"])) {
        $newUsername = trim($_POST["username"]);
        
        if (!empty($newUsername)) {
            $update = $conn->prepare("UPDATE users SET username = ? WHERE username = ?");
            $update->bind_param("ss", $newUsername, $username);
            $update->execute();
            $update->close();

            $_SESSION["username"] = $newUsername;
            $username = $newUsername;
            $user["username"] = $newUsername;

            $message = "Username updated successfully!";
            $messageType = "success";
        } else {
            $message = "Please enter a valid username.";
            $messageType = "error";
        }
    }
    
    // Handle password change
    if (isset($_POST["change_password"])) {
        $currentPassword = trim($_POST["current_password"]);
        $newPassword = trim($_POST["new_password"]);
        $confirmPassword = trim($_POST["confirm_password"]);
        
        // Verify current password (check if hashed or plain)
        $passwordMatch = false;
        if (password_verify($currentPassword, $user['password'])) {
            $passwordMatch = true;
        } elseif ($currentPassword === $user['password']) {
            $passwordMatch = true;
        }
        
        if (!$passwordMatch) {
            $message = "Current password is incorrect.";
            $messageType = "error";
        } elseif ($newPassword !== $confirmPassword) {
            $message = "New passwords do not match.";
            $messageType = "error";
        } elseif (strlen($newPassword) < 8) {
            $message = "New password must be at least 8 characters.";
            $messageType = "error";
        } else {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $update->bind_param("ss", $hashedPassword, $username);
            $update->execute();
            $update->close();
            
            $user['password'] = $hashedPassword;
            
            $message = "Password changed successfully!";
            $messageType = "success";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Account Settings</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    :root {
      --primary: #1868b9;
      --hover: #145a99;
      --bg: #f3f4f6;
      --text: #333;
      --radius: 14px;
    }

    body {
      font-family: "Segoe UI", Arial, sans-serif;
      background-color: var(--bg);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
    }

    .logo {
      text-align: center;
      margin-bottom: 20px;
    }

    .logo img {
      width: 150px;
      height: auto;
      max-width: 80%;
    }

    .account-container {
      width: 100%;
      max-width: 420px;
      background: #fff;
      padding: 30px;
      border-radius: var(--radius);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s ease;
    }

    .account-container:hover {
      transform: translateY(-2px);
    }

    h2 {
      text-align: center;
      color: var(--primary);
      margin-bottom: 25px;
      font-size: 1.6rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      font-weight: 600;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 6px;
    }

    input {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
      background-color: #f9fafb;
      transition: border-color 0.2s;
      box-sizing: border-box;
    }

    input:focus {
      outline: none;
      border-color: var(--primary);
      background-color: #fff;
    }

    .icon {
      width: 20px;
      height: 20px;
      stroke: var(--primary);
      stroke-width: 2;
      fill: none;
    }

    button {
      width: 100%;
      background-color: var(--primary);
      color: #fff;
      border: none;
      padding: 12px;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: var(--hover);
    }

    .password-field {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .password-display {
      flex: 1;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      background-color: #f9fafb;
      color: #666;
      font-family: monospace;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .change-password-btn {
      width: auto;
      padding: 10px 16px;
      background-color: #f59e0b;
      display: flex;
      align-items: center;
      gap: 6px;
      white-space: nowrap;
    }

    .change-password-btn:hover {
      background-color: #d97706;
    }

    .message {
      text-align: center;
      margin-top: 15px;
      padding: 10px;
      border-radius: 8px;
      font-weight: 500;
    }

    .message.success {
      color: #059669;
      background-color: #d1fae5;
    }

    .message.error {
      color: #dc2626;
      background-color: #fee2e2;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 25px;
      text-decoration: none;
      color: var(--primary);
      font-weight: 600;
      transition: color 0.3s;
    }

    .back-link:hover {
      color: var(--hover);
      text-decoration: underline;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      animation: fadeIn 0.3s ease;
    }

    .modal.show {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background-color: #fff;
      padding: 30px;
      border-radius: 12px;
      width: 90%;
      max-width: 450px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
      animation: slideIn 0.3s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideIn {
      from { transform: translateY(-50px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
      font-size: 20px;
      font-weight: 600;
      color: #333;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .modal-footer {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
    }

    .modal-btn {
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .cancel-btn {
      background: #e0e0e0;
      color: #555;
    }

    .cancel-btn:hover {
      background: #d0d0d0;
    }

    .submit-btn {
      background: var(--primary);
      color: white;
    }

    .submit-btn:hover {
      background: var(--hover);
    }

    @media (max-width: 600px) {
      body {
        padding: 10px;
      }

      .account-container {
        padding: 20px;
        border-radius: 10px;
        max-width: 100%;
      }

      h2 {
        font-size: 1.3rem;
      }

      input {
        font-size: 0.9rem;
        padding: 12px;
      }

      button {
        padding: 12px;
        font-size: 0.95rem;
      }

      .logo img {
        width: 100px;
      }

      .password-field {
        flex-direction: column;
        gap: 8px;
      }

      .password-display {
        width: 100%;
        text-align: center;
      }

      .change-password-btn {
        width: 100%;
        justify-content: center;
      }

      .modal-content {
        width: 95%;
        max-width: 95%;
        padding: 20px;
        margin: 10px;
      }

      .modal-header {
        font-size: 18px;
      }

      .modal-footer {
        flex-direction: column;
        gap: 8px;
      }

      .modal-btn {
        width: 100%;
      }

      .form-group {
        margin-bottom: 16px;
      }

      label {
        font-size: 0.9rem;
      }

      .message {
        font-size: 0.9rem;
        padding: 8px;
      }

      .back-link {
        font-size: 0.9rem;
        margin-top: 20px;
      }
    }

    @media (max-width: 400px) {
      .account-container {
        padding: 16px;
      }

      h2 {
        font-size: 1.2rem;
        gap: 6px;
      }

      .icon {
        width: 18px;
        height: 18px;
      }

      input {
        font-size: 0.85rem;
        padding: 10px;
      }

      button {
        padding: 10px;
        font-size: 0.9rem;
      }

      .modal-content {
        padding: 16px;
      }

      .password-display {
        font-size: 0.85rem;
      }
    }
  </style>
</head>
<body>

  <div class="logo">
    <img src="sti-logo.png" alt="STI Logo">
  </div>

  <div class="account-container">
    <h2>
      <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>
      </svg>
      Account Settings
    </h2>

    <form method="POST">
      <div class="form-group">
        <label>
          <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>
          </svg>
          Username
        </label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
      </div>

      <div class="form-group">
        <label>
          <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <rect x="3" y="11" width="18" height="11" rx="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
          Password
        </label>
        <div class="password-field">
          <div class="password-display">••••••••••••</div>
          <button type="button" class="change-password-btn" onclick="openPasswordModal()">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Change
          </button>
        </div>
      </div>

      <button type="submit" name="update_username">Save Username</button>
    </form>

    <?php if ($message): ?>
      <p class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <a href="home.php" class="back-link">⬅ Back to Home</a>
  </div>

  <!-- Password Change Modal -->
  <div id="passwordModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <rect x="3" y="11" width="18" height="11" rx="2"/>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        Change Password
      </div>
      
      <form method="POST">
        <div class="form-group">
          <label>Current Password</label>
          <input type="password" name="current_password" required>
        </div>
        
        <div class="form-group">
          <label>New Password</label>
          <input type="password" name="new_password" minlength="8" required>
        </div>
        
        <div class="form-group">
          <label>Confirm New Password</label>
          <input type="password" name="confirm_password" minlength="8" required>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="modal-btn cancel-btn" onclick="closePasswordModal()">Cancel</button>
          <button type="submit" name="change_password" class="modal-btn submit-btn">Change Password</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openPasswordModal() {
      document.getElementById('passwordModal').classList.add('show');
    }

    function closePasswordModal() {
      document.getElementById('passwordModal').classList.remove('show');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('passwordModal');
      if (event.target === modal) {
        closePasswordModal();
      }
    }

    <?php if ($message && isset($_POST['change_password'])): ?>
      // Auto-close modal on success
      <?php if ($messageType === 'success'): ?>
        closePasswordModal();
      <?php else: ?>
        openPasswordModal();
      <?php endif; ?>
    <?php endif; ?>
  </script>
</body>
</html>
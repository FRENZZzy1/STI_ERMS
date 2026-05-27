<?php
// Enhanced session security - MUST be set BEFORE session_start()
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS, 0 for local development
ini_set('session.use_only_cookies', 1);

session_start();

include("connect.php");

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Rate limiting setup (simple implementation)
$max_attempts = 5;
$lockout_time = 900; // 15 minutes in seconds

// Initialize rate limiting session variables
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Check if user is locked out
if ($_SESSION['login_attempts'] >= $max_attempts) {
    $time_passed = time() - $_SESSION['last_attempt_time'];
    if ($time_passed < $lockout_time) {
        $minutes_left = ceil(($lockout_time - $time_passed) / 60);
        $error = "🔒 Too many failed attempts. Please try again in {$minutes_left} minutes.";
    } else {
        // Reset after lockout period
        $_SESSION['login_attempts'] = 0;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // CSRF Token Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "❌ Invalid request. Please refresh and try again.";
    }
    // Check if not locked out
    elseif ($_SESSION['login_attempts'] < $max_attempts) {
        
        if (!empty($_POST['username']) && !empty($_POST['password'])) {

            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            // Input validation
            if (strlen($username) > 50 || strlen($password) > 100) {
                $error = "❌ Invalid credentials.";
            } else {

                // Prepare query
                $stmt = $conn->prepare("SELECT user_id, username, password, role, status FROM users WHERE BINARY username = ? LIMIT 1");
                
                if ($stmt) {
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();

                        // Check if account is archived
                        if ($row['status'] === 'Archived') {
                            $error = "❌ Invalid credentials."; // Don't reveal account status
                            $_SESSION['login_attempts']++;
                            $_SESSION['last_attempt_time'] = time();
                        }
                        // Verify password
                        elseif (password_verify($password, $row['password'])) {
                            
                            // Successful login - reset attempts
                            $_SESSION['login_attempts'] = 0;
                            
                            // Regenerate session ID to prevent session fixation
                            session_regenerate_id(true);

                            // Set session variables
                            $_SESSION['logged_in'] = true;
                            $_SESSION['username'] = $row['username'];
                            $_SESSION['user_id'] = $row['user_id'];
                            $_SESSION['role'] = $row['role'];
                            $_SESSION['login_time'] = time();

                            // Update status to Online
                            $update_stmt = $conn->prepare("UPDATE users SET status = 'Online' WHERE user_id = ?");
                            $update_stmt->bind_param("i", $row['user_id']);
                            $update_stmt->execute();
                            $update_stmt->close();

                            // Redirect based on role
                            $role = $row['role'];
                            
                            if ($role === 'admin') {
                                header("Location: admin_side/Dashboard/samp.php");
                                exit();
                            } elseif ($role === 'Staff' || $role === 'maintenance') {
                                header("Location: Mobile_side/home.php");
                                exit();
                            } else {
                                // Fallback for unknown roles
                                header("Location: dashboard.php");
                                exit();
                            }

                        } else {
                            // Invalid password
                            $_SESSION['login_attempts']++;
                            $_SESSION['last_attempt_time'] = time();
                            $remaining = $max_attempts - $_SESSION['login_attempts'];
                            
                            if ($remaining > 0) {
                                $error = "❌ Invalid credentials. {$remaining} attempts remaining.";
                            } else {
                                $error = "🔒 Account locked. Too many failed attempts. Try again in 15 minutes.";
                            }
                        }
                        
                    } else {
                        // Username not found - don't reveal this info
                        $_SESSION['login_attempts']++;
                        $_SESSION['last_attempt_time'] = time();
                        $remaining = $max_attempts - $_SESSION['login_attempts'];
                        
                        if ($remaining > 0) {
                            $error = "❌ Invalid credentials. {$remaining} attempts remaining.";
                        } else {
                            $error = "🔒 Account locked. Too many failed attempts. Try again in 15 minutes.";
                        }
                    }

                    $stmt->close();
                } else {
                    $error = "❌ System error. Please try again later.";
                }
            }
        } else {
            $error = "❌ Please fill in all fields.";
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - STI ERMS</title>
  <link rel="stylesheet" href="css/Login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>

  <!-- Mobile Navbar -->
  <nav class="navbar">
    <div class="nav-logo">
      <img src="Sti_Logo.png" alt="STI Logo" class="logo-img">
      <span>STI ERMS</span>
    </div>
    <div class="nav-toggle" id="nav-toggle" aria-label="Toggle navigation" role="button" tabindex="0">☰</div>
    <ul class="nav-links" id="nav-links" role="navigation">
   
    </ul>
  </nav>

  <div class="wrapper">

    <!-- Background image for desktop -->
    <div class="bg-image"></div>

    <!-- Desktop header placeholder -->
    <div id="desktop-header-pc">
      <img src="sti-logos.png" alt="STI Logo" class="header-logo">
      <h1 class="Sub" id="form-sub1">STI Equipment Maintenance <br>and Reporting System</h1>
    </div>

    <div class="container">
      <img src="sti-logos.png" alt="STI Logo" class="form-logo">
      <h1 class="Sub" id="form-sub">STI Equipment Maintenance and Reporting System</h1>

      <form class="login-container" action="login.php" method="POST" id="loginForm">
        <h2>Login</h2>

        <?php if (!empty($error)): ?>
          <div class="error-msg" role="alert">
            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>

        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="form-input">
          <i class="fa-solid fa-user" aria-hidden="true"></i>
          <input 
            type="text" 
            name="username" 
            id="username"
            placeholder="Username" 
            required 
            autocomplete="username"
            maxlength="50"
            aria-label="Username">
        </div>

        <div class="form-input password-input">
          <i class="fa-solid fa-lock" aria-hidden="true"></i>
          <input 
            type="password" 
            name="password" 
            id="password"
            placeholder="Password" 
            required 
            autocomplete="current-password"
            maxlength="100"
            aria-label="Password">
          <button type="button" class="toggle-password" id="togglePassword" aria-label="Show password" tabindex="0">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>

        <div class="form-submit">
          <button type="submit" id="submitBtn">
            <span id="btnText">Log In</span>
            <span id="btnLoader" class="loader" style="display: none;"></span>
          </button>
        </div>
      </form>
    </div>

  </div>

  <script>
    // Navbar toggle
    const navToggle = document.getElementById("nav-toggle");
    const navLinks = document.getElementById("nav-links");
    
    navToggle.addEventListener("click", function() {
      if (navLinks.style.display === "flex") {
        navLinks.style.display = "none";
      } else {
        navLinks.style.display = "flex";
        navLinks.style.flexDirection = "column";
      }
    });

    // Password visibility toggle
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");
    
    togglePassword.addEventListener("click", function() {
      const type = passwordInput.type === "password" ? "text" : "password";
      passwordInput.type = type;
      
      const icon = this.querySelector("i");
      icon.classList.toggle("fa-eye");
      icon.classList.toggle("fa-eye-slash");
    });

    // Form submission with loading state
    const loginForm = document.getElementById("loginForm");
    const submitBtn = document.getElementById("submitBtn");
    const btnText = document.getElementById("btnText");
    const btnLoader = document.getElementById("btnLoader");

    loginForm.addEventListener("submit", function() {
      submitBtn.disabled = true;
      btnText.style.display = "none";
      btnLoader.style.display = "inline-block";
    });

    // Keyboard accessibility for nav toggle
    navToggle.addEventListener("keypress", function(e) {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        this.click();
      }
    });
  </script>

</body>
</html>
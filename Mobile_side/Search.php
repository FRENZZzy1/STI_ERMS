<?php include("mobile_page.php"); if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) { header("Location: /oct10/UPDATED/login.php"); exit(); } $userName = $_SESSION['username']; $role = $_SESSION['role']; ?>
<!-- Scoped style -->
<style>
  /* Mobile-first modern design */
  /* Make body and html take full viewport height */
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

body {
    background: linear-gradient(135deg, #e8e8e4, #d8e2dc, #ece4db);

}

.search-container {
    width: 90%;
    margin-bottom: 100px;
    max-width: 360px;
    background: #ffffff;
    padding: 22px;
    border-radius: 16px;
    text-align: center;
    margin: 40px auto;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    border: none;
    font-family: "Segoe UI", Arial, sans-serif;
}
.search-container p {
    font-size: 14px;
    color: #666;
    margin-bottom: 6px;
}
.search-container h2 {
    font-size: 20px;
    font-weight: 600;
    margin: 12px 0 18px;
    color: #0d47a1;
    letter-spacing: 0.5px;
}
/* Input wrapper with camera icon */
.form-input {
    position: relative;
    display: flex;
    align-items: center;
}
.search-container .form-input input {
    width: 100%;
    padding: 12px 45px 12px 12px;
    font-size: 16px;
    text-align: left;
    border: 1px solid #ccc;
    border-radius: 12px;
    color: #333;
    box-sizing: border-box;
}
.search-container .form-input input:focus {
    border-color: #1565c0;
    box-shadow: 0 0 6px rgba(21, 101, 192, 0.3);
}
/* Camera icon inside textbox */
.camera-icon-link {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    text-decoration: none;
    cursor: pointer;
    font-size: 20px;
    color: #1565c0;
    transition: all 0.3s ease;
    padding: 4px;
}
.camera-icon-link:hover {
    color: #0d47a1;
    transform: translateY(-50%) scale(1.15);
}
/* Submit button */
.search-container .form-submit input {
    width: 100%;
    background: linear-gradient(135deg, #1565c0, #42a5f5);
    color: #fff;
    font-weight: 600;
    padding: 14px;
    font-size: 16px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    margin-top: 16px;
    transition: all 0.3s ease;
}
.search-container .form-submit input:hover {
    background: linear-gradient(135deg, #0d47a1, #1565c0);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
}
/* Responsive for very small phones */
@media (max-width: 400px) {
    .search-container {
        padding: 18px;
        border-radius: 12px;
    }
    .search-container h2 {
        font-size: 18px;
    }
    .search-container .form-input input,
    .search-container .form-submit input {
        font-size: 15px;
        padding: 12px;
    }
    .camera-icon-link {
        font-size: 18px;
    }
}
</style>
<!-- Content -->
<div class="search-container">
    <p><?php echo htmlspecialchars($userName);?> (<?php echo htmlspecialchars($role);?>)</p>
    <h2>SCAN QR code</h2>
    
    <form action="output.php" method="POST">
        <div class="form-input">
            <input type="text" name="barcode" placeholder="Type or scan QR code..." required>
            <a href="qrscanner.php" class="camera-icon-link" title="Open QR Scanner">📷</a>
        </div>
        <div class="form-submit">
            <input type="submit" value="Submit">
        </div>
    </form>
    <br>
</div>
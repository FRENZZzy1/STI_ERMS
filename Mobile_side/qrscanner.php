<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: /oct10/UPDATED/login.php");
  exit();
}

$userName = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QR Code Scanner</title>

  <!-- ✅ ZXing Library -->
  <script src="https://unpkg.com/@zxing/library@0.18.6/umd/index.min.js"></script>

  <style>
    /* ==== RESET ==== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    /* ==== NAVBAR ==== */
    .navbar {
      width: 100%;
      background-color: #1868b9;
      padding: 12px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 1000;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .nav-logo {
      display: flex;
      align-items: center;
      gap: 8px;
      color: white;
      font-size: 14px;
      font-weight: bold;
    }

    .logo-img {
      height: 35px;
      width: auto;
    }

    /* ==== BODY ==== */
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to top, #dbefff, #f0f2f5);
      background-attachment: fixed;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding-top: 100px;
      overflow-x: hidden;
    }

    /* ==== SCANNER CONTAINER ==== */
    .scanner-container {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 380px;
      padding: 30px 25px;
      text-align: center;
      animation: fadeSlideUp 1s ease-out forwards;
    }

    .scanner-header h2 {
      color: #1868b9;
      font-size: 22px;
      margin-bottom: 10px;
    }

    .user-info {
      font-size: 13px;
      color: #666;
      margin-bottom: 18px;
    }

    video {
      width: 100%;
      height: 300px;
      border-radius: 16px;
      border: 3px solid #1868b9;
      background: #000;
      margin-bottom: 16px;
    }

    #result {
      padding: 12px;
      border-radius: 10px;
      background: #f5f5f5;
      min-height: 25px;
      font-weight: 600;
      margin-bottom: 16px;
      color: #333;
    }

    #result.scanning { color: #1868b9; }
    #result.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    #result.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    /* ==== BUTTONS ==== */
    .button-group {
      display: flex;
      justify-content: center;
      gap: 10px;
    }

    button {
      padding: 11px 18px;
      font-size: 15px;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    #backBtn {
      background: #1868b9;
      color: #fff;
    }
    #backBtn:hover {
      background: #0f4f91;
    }

    /* ==== ANIMATION ==== */
    @keyframes fadeSlideUp {
      0% { opacity: 0; transform: translateY(40px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    /* ==== RESPONSIVE ==== */
    @media (max-width: 420px) {
      .scanner-container {
        padding: 22px;
      }
      video {
        height: 250px;
      }
      .scanner-header h2 {
        font-size: 20px;
      }
    }

  </style>
</head>
<body>
  <!-- ✅ NAVBAR -->
  <div class="navbar">
    <div class="nav-logo">
      <img src="../tes.png" class="logo-img" alt="Logo">
      <span>Equipment Tracker</span>
    </div>
  </div>

  <!-- ✅ SCANNER BODY -->
  <div class="scanner-container">
    <div class="scanner-header">
      <h2>📷 QR Code Scanner</h2>
      <div class="user-info">Logged in as <strong><?php echo htmlspecialchars($userName); ?></strong></div>
    </div>

    <video id="video"></video>
    <div id="result" class="scanning">Scanning...</div>

    <form id="scanForm" method="POST" action="output.php">
      <input type="hidden" id="barcodeInput" name="barcode" value="">
    </form>

    <div class="button-group">
      <button type="button" id="backBtn">← Back</button>
    </div>
  </div>

  <!-- ✅ SCANNER SCRIPT -->
  <script>
    const resultElement = document.getElementById('result');
    const barcodeInput = document.getElementById('barcodeInput');
    const scanForm = document.getElementById('scanForm');
    const backBtn = document.getElementById('backBtn');
    let scannedCode = null;

    backBtn.addEventListener('click', () => {
      window.history.back();
    });

    window.addEventListener('load', () => {
      const codeReader = new ZXing.BrowserMultiFormatReader();
      const videoElement = document.getElementById('video');

      async function startScanner() {
        try {
          const devices = await codeReader.listVideoInputDevices();
          if (devices.length === 0) {
            resultElement.textContent = "❌ No camera found";
            resultElement.className = "error";
            return;
          }

          const backCamera = devices.find(d => d.label.toLowerCase().includes('back')) || devices[0];
          await codeReader.decodeFromVideoDevice(backCamera.deviceId, videoElement, (result, err) => {
            if (result) {
              scannedCode = result.text;
              barcodeInput.value = scannedCode;
              resultElement.textContent = "✅ QR Code: " + scannedCode;
              resultElement.className = "success";

              setTimeout(() => { scanForm.submit(); }, 1000);
              codeReader.reset();
            } else if (err && !(err instanceof ZXing.NotFoundException)) {
              console.error(err);
            }
          });
        } catch (error) {
          resultElement.textContent = "❌ Error: " + error.message;
          resultElement.className = "error";
          console.error(error);
        }
      }

      startScanner();
    });
  </script>
</body>
</html>

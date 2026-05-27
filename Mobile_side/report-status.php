<?php
include("../connect.php");
include("pass-down-method.php");
include("mobile_page.php");

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /UPDATED/login.php");
    exit();
}
$user = $_SESSION['user_id'];

// Fetch reports
$sql = "SELECT report_id, equipment_id, location, equipment_condition, reported_by FROM reports WHERE reported_by = $user && State != 'Good'"; 
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>  
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report Status</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="report-status.css">
</head>
<body>
  <!-- Navigation from mobile_page.php will appear here untouched -->
  
  <div class="report-status-page">
    <div class="container">
      <div class="header">
        <div class="logo">
          <img src="sti-logo.png" alt="STI LOGO">
        </div>
        <div class="title">Report Status</div>
        <div class="subtitle">Track your equipment reports in real-time</div>
      </div>

      <div class="reports">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="card">
                  <div class="card-header">
                    <h3><i class="fa-solid fa-file-lines"></i> Report #<?php echo htmlspecialchars($row['report_id']); ?></h3>
                    <span class="report-badge">Active</span>
                  </div>
                  
                  <div class="report-number">
                    <i class="fa-solid fa-barcode"></i>
                    <?php echo htmlspecialchars(passDownBarcode($row['equipment_id'])); ?>
                  </div>
                  
                  <div class="btn-container">
                    <div class="btn fixed">
                      <i class="fa-solid fa-circle-check"></i>
                      <?php echo htmlspecialchars($row['equipment_condition']); ?>
                    </div>

                    <div 
                      class="btn view" 
                      data-location="<?php echo htmlspecialchars($row['location']); ?>"
                      data-issue="<?php echo htmlspecialchars($row['equipment_condition']); ?>"
                      data-personnel="<?php echo htmlspecialchars(passDownName($row['reported_by'])); ?>"
                    >
                      <i class="fa-solid fa-eye"></i>
                      View
                    </div>
                  </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="empty-state">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <h3>No Reports Found</h3>
                    <p>You don\'t have any active reports at the moment.</p>
                  </div>';
        }
        $conn->close();
        ?>
      </div>
      
      <!-- Popup -->
      <div id="popup" class="popup">
        <div class="popup-content">
          <h2><i class="fa-solid fa-circle-info"></i> Report Details</h2>
          <p>
            <strong><i class="fa-solid fa-location-dot"></i> Location</strong>
            <span id="popup-location"></span>
          </p>
          <p>
            <strong><i class="fa-solid fa-triangle-exclamation"></i> Issue</strong>
            <span id="popup-issue"></span>
          </p>
          <p>
            <strong><i class="fa-solid fa-user"></i> Personnel</strong>
            <span id="popup-personnel"></span>
          </p>
          <button class="close-btn">
            <i class="fa-solid fa-xmark"></i> Close
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- End of .report-status-page wrapper -->

  <script>
    // Attach listeners to view divs
    document.querySelectorAll('.report-status-page .view').forEach(btn => {
      btn.addEventListener('click', function() {
        const popup = document.querySelector('.report-status-page .popup');
        document.getElementById('popup-location').textContent = this.dataset.location;
        document.getElementById('popup-issue').textContent = this.dataset.issue;
        document.getElementById('popup-personnel').textContent = this.dataset.personnel;
        popup.classList.add('show');
      });
    });

    // Close button
    document.querySelector('.report-status-page .close-btn').addEventListener('click', function() {
      document.querySelector('.report-status-page .popup').classList.remove('show');
    });

    // Close popup when clicking outside
    const popup = document.querySelector('.report-status-page .popup');
    if (popup) {
      popup.addEventListener('click', function(e) {
        if (e.target === this) {
          this.classList.remove('show');
        }
      });
    }
  </script>
</body>
</html>
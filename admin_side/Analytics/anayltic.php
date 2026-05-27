<?php
// ================================
// analytics-module.php (Enhanced Unique Design + Breakdown Modal + Most Reported Equipment)
// ================================

include("../../connect.php");

try {
  // Lab Performance Query
  $sql = "
  SELECT 
    l.lab_id,
    l.lab_name,
    l.location,
    COUNT(e.equipment_id) AS total_equipment,
    SUM(CASE WHEN e.status = 'Good' THEN 1 ELSE 0 END) AS good_count,
    SUM(CASE WHEN e.status = 'not functional' THEN 1 ELSE 0 END) AS repair_count,
    SUM(CASE WHEN e.status = 'Replacement' THEN 1 ELSE 0 END) AS replacement_count,
    SUM(CASE WHEN e.status = 'Missing' THEN 1 ELSE 0 END) AS missing_count,
    ROUND(
      (
        (SUM(CASE WHEN e.status = 'Good' THEN 1 ELSE 0 END) * 1.0) +
        (SUM(CASE WHEN e.status = 'Needs Repair' THEN 1 ELSE 0 END) * 0.5) +
        (SUM(CASE WHEN e.status = 'Replacement' THEN 1 ELSE 0 END) * 0.2) +
        (SUM(CASE WHEN e.status = 'Missing' THEN 1 ELSE 0 END) * 0.0)
      ) / COUNT(e.equipment_id) * 100, 2
    ) AS performance_percentage
  FROM laboratories l
  LEFT JOIN equipment e ON l.lab_id = e.lab_id
  GROUP BY l.lab_id, l.lab_name, l.location
  ORDER BY performance_percentage DESC;
  ";

  $result = mysqli_query($conn, $sql);
  if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
  }

  $labs = mysqli_fetch_all($result, MYSQLI_ASSOC);

  // Most Reported Equipment Query
  $reportedSql = "
  SELECT h.equipment_id, e.name, e.lab_id, l.lab_name, COUNT(h.action) AS reports_number 
  FROM equipment_history h
  INNER JOIN equipment e ON h.equipment_id = e.equipment_id
  LEFT JOIN laboratories l ON e.lab_id = l.lab_id
  WHERE h.action = 'Reported'
  GROUP BY h.equipment_id, e.name, e.lab_id, l.lab_name
  ORDER BY COUNT(h.action) DESC
  LIMIT 10
  ";

  $reportedResult = mysqli_query($conn, $reportedSql);
  if (!$reportedResult) {
    die("SQL Error: " . mysqli_error($conn));
  }

  $reportedEquipment = mysqli_fetch_all($reportedResult, MYSQLI_ASSOC);
} catch (Exception $e) {
  die("Error retrieving analytics: " . $e->getMessage());
}
?>

<?php include("../sidebar.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Lab Performance Analytics</title>
  <link rel="stylesheet" href="analytics.css">
  <link rel="stylesheet" href="../sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    

  </style>
</head>

<body>
  <div class="main-content">
    <div class="container">
      <div class="header-row">
        <h1><i class="fa-solid fa-chart-line"></i> Laboratory Performance Analytics</h1>
        <button class="formula-btn" onclick="openFormulaModal()">
          <i class="fa-solid fa-square-root-variable"></i> Show Formula
        </button>
      </div>

      <div class="lab-container">
        <?php if (!empty($labs)): ?>
          <?php 
            $rank = 1;
            foreach ($labs as $lab): 
              $percent = $lab['performance_percentage'] ?? 0;
              if ($percent >= 80) $badge_color = "var(--success)";
              elseif ($percent >= 50) $badge_color = "var(--warning)";
              else $badge_color = "var(--danger)";
          ?>
            <div class="lab-card" 
                 onclick="showBreakdown('<?= htmlspecialchars($lab['lab_name']); ?>',
                                         <?= $lab['good_count'] ?>,
                                         <?= $lab['repair_count'] ?>,
                                         <?= $lab['replacement_count'] ?>,
                                         <?= $lab['missing_count'] ?>,
                                         <?= $lab['total_equipment'] ?>,
                                         <?= $lab['performance_percentage'] ?>)">
              <div class="lab-info">
                <div class="lab-title">
                  <i class="fa-solid fa-flask"></i> <?= htmlspecialchars($lab['lab_name']); ?>
                </div>
                <div class="lab-location">
                  <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($lab['location'] ?? '—'); ?>
                </div>
              </div>
              <div class="lab-footer">
                <div class="badge" style="background: <?= $badge_color ?>;">
                  <i class="fa-solid fa-percent"></i> <?= htmlspecialchars($percent); ?>%
                </div>
                <div class="rank">Rank #<?= $rank++; ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="no-labs"><i class="fa-solid fa-triangle-exclamation"></i> No laboratory data found.</div>
        <?php endif; ?>
      </div>

      <!-- ===== MOST REPORTED EQUIPMENT SECTION ===== -->
      <div class="reported-section">
        <div class="section-header">
          <h2><i class="fa-solid fa-triangle-exclamation"></i> Most Reported Equipment</h2>
        </div>

        <div class="reported-grid">
          <?php if (!empty($reportedEquipment)): ?>
            <?php 
$reportRank = 1;
foreach ($reportedEquipment as $equipment): 
?>
  <form action="../Equipments/equipment_history.php" method="POST" 
        class="reported-card" 
        style="cursor:pointer;" 
        onclick="this.submit();">

      <input type="hidden" name="equipment_id" value="<?= $equipment['equipment_id']; ?>">

      <div class="reported-rank">#<?= $reportRank++; ?></div>
      
      <div class="equipment-name">
        <i class="fa-solid fa-wrench"></i>
        <?= htmlspecialchars($equipment['name']); ?>
      </div>
      
      <div class="equipment-lab">
        <i class="fa-solid fa-flask"></i>
        <?= htmlspecialchars($equipment['lab_name'] ?? 'Unknown Lab'); ?>
      </div>

      <div class="report-count">
        <span class="report-count-label">Total Reports</span>
        <span class="report-count-value">
          <i class="fa-solid fa-flag"></i>
          <?= htmlspecialchars($equipment['reports_number']); ?>
        </span>
      </div>
  </form>
<?php endforeach; ?>

          <?php else: ?>
            <div class="no-reports">
              <i class="fa-solid fa-circle-check"></i> No reported equipment found.
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>

  <!-- 📊 Breakdown Modal -->
  <div id="breakdownModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeBreakdown()">&times;</span>
      <h2 id="modalLabName"></h2>
      <ul class="breakdown-list">
        <li>🟢 Good: <span id="goodCount"></span></li>
        <li>🟡 Needs Repair: <span id="repairCount"></span></li>
        <li>🟠 Replacement: <span id="replacementCount"></span></li>
        <li>🔴 Missing: <span id="missingCount"></span></li>
      </ul>
      <p><strong>Total Equipment:</strong> <span id="totalEq"></span></p>
      <p><strong>Performance:</strong> <span id="performancePercent"></span>%</p>
    </div>
  </div>

  <!-- Formula Modal -->
  <div id="formulaModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeFormulaModal()">&times;</span>
      <h2>Performance Percentage Formula</h2>
      <pre>
Performance % = 
(
  (Good * 1.0) +
  (Needs Repair * 0.5) +
  (Replacement * 0.2) +
  (Missing * 0.0)
) / Total Equipment * 100
      </pre>
    </div>
  </div>

  <script>
    function showBreakdown(name, good, repair, replace, missing, total, percent) {
      document.getElementById('modalLabName').innerText = name;
      document.getElementById('goodCount').innerText = good;
      document.getElementById('repairCount').innerText = repair;
      document.getElementById('replacementCount').innerText = replace;
      document.getElementById('missingCount').innerText = missing;
      document.getElementById('totalEq').innerText = total;
      document.getElementById('performancePercent').innerText = percent;
      document.getElementById('breakdownModal').style.display = 'flex';
    }
    function closeBreakdown() {
      document.getElementById('breakdownModal').style.display = 'none';
    }

    function openFormulaModal() {
      document.getElementById('formulaModal').style.display = 'flex';
    }
    function closeFormulaModal() {
      document.getElementById('formulaModal').style.display = 'none';
    }

    window.onclick = function(e) {
      if (e.target == document.getElementById('breakdownModal')) closeBreakdown();
      if (e.target == document.getElementById('formulaModal')) closeFormulaModal();
    }
  </script>
  <script src="../darkmode.js"></script>

</body>
</html>
<?php
include("../../connect.php");

$query = "SELECT COUNT(*) AS total FROM reports WHERE DATE(reported_at) = CURDATE()";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

echo json_encode(["today_reports" => $row['total']]);
?>

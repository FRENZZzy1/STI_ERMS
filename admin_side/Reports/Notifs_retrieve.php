<?php
include("../../connect.php");

// Kunin yung pinaka-latest notification gamit ang ReportID
$sql = "SELECT * FROM reports ORDER BY report_id DESC LIMIT 1";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$response = array();

if ($row = mysqli_fetch_assoc($result)) {
    $response["id"]        = $row["report_id"];
    $response["equipment"] = $row["equipment_id"];
    $response["location"]  = $row["location"];
    $response["reason"]    = $row["equipment_condition"];
    $response["username"]  = $row["reported_by"];
}   

echo json_encode($response);
mysqli_close($conn);
?>

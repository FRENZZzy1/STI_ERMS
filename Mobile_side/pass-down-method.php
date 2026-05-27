<?php

include("../connect.php");


    function passDownBarcode($equipment_id){

        global $conn;
        $sql = "SELECT * FROM equipment WHERE equipment_id = '$equipment_id'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
        return $row["barcode"];
        } else {
        return "Unknown Equipment";
    }

    }

    function passDownLocation($lab_id){
        
        global $conn;
        $sql = "SELECT * FROM laboratories WHERE lab_id = '$lab_id'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
        return $row["lab_name"];
    } else {
        return "Unknown Laboratory";
    }

    }

    function passDownName($reported_by) {
    global $conn;
    $sql = "SELECT * FROM users WHERE user_id = '$reported_by'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        return $row["username"];
    } else {
        return "Unknown User";
    }

    
}

function passDownEquipmentName($eq_name) {
    global $conn;
    $sql = "SELECT * FROM equipment WHERE equipment_id = '$eq_name'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        return $row["name"];
    } else {
        return "Unknown Equipment";
    }
}

function passDownLabName($Lab_id) {
    global $conn;
    $sql = "SELECT * FROM laboratories WHERE lab_id = '$Lab_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        return $row["lab_name"];
    } else {
        return "Unknown Equipment";
    }
}

?>



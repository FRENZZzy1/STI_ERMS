<?php
    //so eto ung ginagamit naming centralized connection ng aming database
    $db_server = "localhost";
    $db_user = "root";
    $db_password = ""; 
    $db_name = "sti_erms";
    $conn = "";

    $conn = mysqli_connect($db_server,  $db_user, $db_password, $db_name);



?>
<?php

require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    $tech_name     = $mysqli->real_escape_string(trim($_POST['technician'] ?? ''));
    $day           = intval($_POST['day'] ?? 0);
    $month         = intval($_POST['month'] ?? 0);
    $year          = intval($_POST['year'] ?? 0);
    $start_date    = sprintf("%04d-%02d-%02d", $year, $month, $day);

    $start_hour    = str_pad(intval($_POST['start_hour'] ?? 0), 2, '0', STR_PAD_LEFT);
    $start_minute  = str_pad(intval($_POST['start_minute'] ?? 0), 2, '0', STR_PAD_LEFT);
    $end_hour      = str_pad(intval($_POST['end_hour'] ?? 0), 2, '0', STR_PAD_LEFT);
    $end_minute    = str_pad(intval($_POST['end_minute'] ?? 0), 2, '0', STR_PAD_LEFT);

    $start_time    = "$start_hour:$start_minute";
    $end_time      = "$end_hour:$end_minute";

    $fault_ref     = $mysqli->real_escape_string(trim($_POST['fault_ref'] ?? ''));
    $fault_desc    = $mysqli->real_escape_string(trim($_POST['fault_description'] ?? ''));
    $fault_status  = $mysqli->real_escape_string(trim($_POST['fault_status'] ?? ''));
    $work_summary  = $mysqli->real_escape_string(trim($_POST['work_summary'] ?? ''));
    $material_used = $mysqli->real_escape_string(trim($_POST['material_used'] ?? ''));

    //SQL
    $sql = "INSERT INTO work_host 
        (tech_name, start_date, fault_ref, fault_description, start_time, end_time, fault_status, work_summary, material_used)
        VALUES
        ('$tech_name','$start_date','$fault_ref','$fault_desc','$start_time','$end_time','$fault_status','$work_summary','$material_used')";

    // Execute and redirect
    if ($mysqli->query($sql) === TRUE) {
        header("Location: admin.php");
        exit;
    } else {
        die("❌ SQL Error: " . $mysqli->error);
    }

} else {
    die("❌ Invalid request method.");
}

$mysqli->close();
?>

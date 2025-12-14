<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tech_name     = $mysqli->real_escape_string($_POST['tech_name']);
    $day           = $mysqli->real_escape_string($_POST['day']);
    $month         = $mysqli->real_escape_string($_POST['month']);
    $year          = $mysqli->real_escape_string($_POST['year']);
    $weekday       = $mysqli->real_escape_string($_POST['weekday']);

    $work_date     = "$year-$month-$day";

    $start_time    = $_POST['start_hour'] . ":" . $_POST['start_minute'];
    $end_time      = $_POST['end_hour'] . ":" . $_POST['end_minute'];

    $normal_hours  = floatval($_POST['normal_hours']);
    $normal_ot     = floatval($_POST['normal_ot']);
    $total_hours   = floatval($_POST['total_hours']);

    $ref           = $mysqli->real_escape_string($_POST['ref_number']);
    $status        = $mysqli->real_escape_string($_POST['fault_status']);

    $start_km      = intval($_POST['start_mileage']);
    $end_km        = intval($_POST['end_mileage']);
    $total_km      = intval($_POST['total_kms']);

    $comments      = $mysqli->real_escape_string($_POST['comments']);

    $sql = "INSERT INTO gp_maintenance 
        (tech_name, work_date, day, start_time, end_time, normal_hours, normal_ot,
         total_hours, ref_number, fault_status, start_mileage, end_mileage,
         total_kms, comments)
        VALUES
        ('$tech_name','$work_date','$weekday','$start_time','$end_time',
         '$normal_hours','$normal_ot','$total_hours','$ref','$status',
         '$start_km','$end_km','$total_km','$comments')";

    if ($mysqli->query($sql)) {
        header("Location: gp_admin.php");
        exit;
    } else {
        die("DB Error: " . $mysqli->error);
    }

}
?>

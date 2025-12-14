<?php
require_once 'config.php';

// sanitize string input
function sanitize($value) {
    return htmlspecialchars(trim($value));
}

// Collect POST data
$data = [
    'technician' => sanitize($_POST['technician'] ?? ''),
    'start_day' => $_POST['start_day'] ?? '',
    'start_month' => $_POST['start_month'] ?? '',
    'start_year' => $_POST['start_year'] ?? '',
    'start_hour' => $_POST['start_hour'] ?? '',
    'start_minute' => $_POST['start_minute'] ?? '',
    'end_hour' => $_POST['end_hour'] ?? '',
    'end_minute' => $_POST['end_minute'] ?? '',
    'link_ref' => sanitize($_POST['link_ref'] ?? ''),
    'link_description' => sanitize($_POST['link_description'] ?? ''),
    'percentage' => $_POST['percentage'] ?? '',
    'status' => $_POST['status'] ?? '',
    'atp' => $_POST['atp'] ?? '',
    'length' => $_POST['length'] ?? '',
    'link_type' => $_POST['link_type'] ?? '',
    'as_build' => $_POST['as_build'] ?? '',
    'comp_day' => $_POST['comp_day'] ?? '',
    'comp_month' => $_POST['comp_month'] ?? '',
    'comp_year' => $_POST['comp_year'] ?? '',
    'start_mileage' => $_POST['start_mileage'] ?? '',
    'end_mileage' => $_POST['end_mileage'] ?? '',
    'total_km' => $_POST['total_km'] ?? '',
    'comments' => sanitize($_POST['comments'] ?? ''),
];

// =====================
// VALIDATION
// =====================
$errors = [];

foreach ($data as $key => $value) {
    if ($key === 'length' || $key === 'start_mileage' || $key === 'end_mileage' || $key === 'total_km') {
        if ($value === '' || !is_numeric($value)) $errors[] = "$key must be numeric";
    } elseif ($key === 'percentage') {
        if ($value === '' || !is_numeric($value)) $errors[] = "$key is required";
    } else {
        if ($value === '' && !in_array($key, ['comp_day','comp_month','comp_year'])) $errors[] = "$key is required";
    }
}

// Start and end mileage logic
if(is_numeric($data['start_mileage']) && is_numeric($data['end_mileage']) && $data['start_mileage'] > $data['end_mileage']){
    $errors[] = "Start mileage cannot be greater than end mileage";
}
// Date completed and percentage logic
if($data['percentage'] == 100){
    if(!$data['comp_day'] || !$data['comp_month'] || !$data['comp_year']){
        $errors[] = "Completed date required when percentage is 100%";
    } else {
        if(!checkdate($data['comp_month'], $data['comp_day'], $data['comp_year'])){
            $errors[] = "Completed date is invalid";
        }
    }
}

// Show errors
if($errors){
    echo "<h2>Form submission errors:</h2><ul>";
    foreach($errors as $e){
        echo "<li>$e</li>";
    }
    echo "</ul><a href='build.html'>Go Back</a>";
    exit();
}

// =====================
// =====================

// Dynamic placeholders
$columns = implode(", ", array_keys($data));
$placeholders = implode(", ", array_fill(0, count($data), "?"));

$sql = "INSERT INTO build ($columns) VALUES ($placeholders)";
$stmt = $mysqli->prepare($sql);

// Determine types 
$types = '';
foreach ($data as $key => $value) {
    if (in_array($key, ['percentage'])) $types .= 'i';
    elseif (in_array($key, ['length','start_mileage','end_mileage','total_km'])) $types .= 'd';
    else $types .= 's';
}

// Bind parameters dynamically
$stmt->bind_param($types, ...array_values($data));

// Execute
if($stmt->execute()){
    header("Location: buildAdmin.php?msg=success");
    exit();
} else {
    error_log("Build insert error: ".$stmt->error);
    echo "<h2>Submission failed. Please try again.</h2>";
}

$stmt->close();
$mysqli->close();
?>

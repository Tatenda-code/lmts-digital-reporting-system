<?php
require_once 'config.php';

// ===============================
// FILTER HANDLING (case-insensitive)
// ===============================

$filter_tech        = $_GET['filter_tech'] ?? '';
$filter_date        = $_GET['filter_date'] ?? '';
$filter_month_name  = $_GET['filter_month_name'] ?? '';

$conditions = [];

// Technician filter
if (!empty($filter_tech)) {
    $safe = $mysqli->real_escape_string($filter_tech);
    $conditions[] = "LOWER(tech_name) LIKE LOWER('%$safe%')";
}

// Date filter
if (!empty($filter_date)) {
    $safe_date = $mysqli->real_escape_string($filter_date);
    $conditions[] = "work_date = '$safe_date'";
}

// Month filter
if (!empty($filter_month_name)) {
    $month_map = [
        "January" => "01","February" => "02","March" => "03",
        "April" => "04","May" => "05","June" => "06",
        "July" => "07","August" => "08","September" => "09",
        "October" => "10","November" => "11","December" => "12"
    ];

    if (isset($month_map[$filter_month_name])) {
        $month_num = $month_map[$filter_month_name];
        $conditions[] = "MONTH(work_date) = '$month_num'";
    }
}

$where = "";
if (!empty($conditions)) {
    $where = "WHERE " . implode(" AND ", $conditions);
}

// If no filters  show only 3 latest entries
if (empty($conditions)) {
    $sql = "SELECT * FROM gp_maintenance ORDER BY id DESC LIMIT 3";
} else {
    $sql = "SELECT * FROM gp_maintenance $where ORDER BY id DESC";
}

$result = $mysqli->query($sql);

if (!$result) {
    die("Query failed: " . $mysqli->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LMTS Admin Panel (GP)</title>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
body {
    background: radial-gradient(circle at top, #725e5eff, #0f0f0fff);
}

.inner-box {
    background: rgba(0,0,0,0.3);
    border: 1px solid #f00;
    border-radius: 1.5rem;
    padding: 2rem;
    box-shadow: 0 0 2rem rgba(255,0,0,0.5);
    color: white;
}

.btn-red {
    background: linear-gradient(to right, #b91c1c, #f87171);
    color: white;
    padding: 0.75rem 1.5rem;
    font-weight: bold;
    border-radius: 0.75rem;
    text-align: center;
    display: inline-block;
}
.btn-red:hover {
    background: linear-gradient(to right, #ef4444, #fca5a5);
}

.btn-gray {
    background: linear-gradient(to right, #374151, #1f2937);
    color: white;
    padding: 0.75rem 1.5rem;
    font-weight: bold;
    border-radius: 0.75rem;
    text-align: center;
    display: inline-block;
}
.btn-gray:hover {
    background: linear-gradient(to right, #4b5563, #111827);
}

input, select {
    background-color: #000;
    color: #fff;
    border: 1px solid #f00;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    font-weight: bold;
    height: 3rem;
    width: 100%;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

th, td {
    border: 1px solid #f00;
    padding: 0.5rem;
}

thead th {
    background: #000;
    color: white;
    border-bottom: 2px solid #f00;
}

tbody tr:hover {
    background: rgba(255,0,0,0.2);
}

.flatpickr-input[readonly] {
    cursor: pointer;
}
</style>
</head>

<body>

<div class="max-w-6xl mx-auto inner-box">

    <div class="flex flex-col sm:flex-row justify-between gap-4 mb-6">
        <h1 class="text-3xl font-extrabold text-white">LMTS Admin Panel (GP)</h1>

        <div class="flex gap-3">
            <a href="gp_maintenance.html" class="btn-red">Go to Form</a>
            <a href="selection.html" class="btn-gray">Home</a>
        </div>
    </div>

    <!-- FILTERS -->
    <form method="get" class="flex flex-col gap-3 mb-6">

        <div class="flex gap-3">

            <div class="flex-1">
                <input type="text" name="filter_tech"
                       value="<?php echo htmlspecialchars($filter_tech); ?>"
                       placeholder="Filter by Technician...">
            </div>

            <div class="flex-1">
                <input type="text" id="filter_date" name="filter_date"
                       value="<?php echo htmlspecialchars($filter_date); ?>"
                       placeholder="Filter by Date..." readonly>
            </div>

            <div class="flex-1">
                <select name="filter_month_name">
                    <option value="">Filter by Month...</option>
                    <?php
                    $months = [
                        "January","February","March","April","May","June",
                        "July","August","September","October","November","December"
                    ];

                    foreach ($months as $m) {
                        $sel = ($filter_month_name === $m) ? "selected" : "";
                        echo "<option $sel>$m</option>";
                    }
                    ?>
                </select>
            </div>

        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-red">Search</button>
            <a href="gp_admin.php" class="btn-gray">Reset</a>
        </div>

    </form>

    <!-- TABLE -->
    <div class="overflow-x-auto">

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Technician</th>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Start time</th>
                    <th>End time</th>
                    <th>Normal (hrs)</th>
                    <th>Normal (OT)</th>
                    <th>Total (hrs)</th>
                    <th>Start km</th>
                    <th>End km</th>
                    <th>Total km</th>
                    <th>Link Ref</th>
                    <th>Link Status</th>
                    <th>Comments</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($result->num_rows > 0): ?>

                <?php while ($row = $result->fetch_assoc()): ?>

                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['tech_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['work_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['day']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['normal_hours']); ?></td>
                        <td><?php echo htmlspecialchars($row['normal_ot']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_hours']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_mileage']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_mileage']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_kms']); ?></td>
                        <td><?php echo htmlspecialchars($row['ref_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['fault_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['comments']); ?></td>
                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="15" class="text-center text-gray-400 p-4">
                        No records found
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#filter_date", {
    dateFormat: "Y-m-d",
    allowInput: true,
    altInput: true,
    altFormat: "F j, Y"
});
</script>

<?php $mysqli->close(); ?>

</body>
</html>

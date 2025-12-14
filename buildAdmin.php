<?php
require_once 'config.php';

// ===============================
// Case insensitive filter handling
// ===============================
$filter_tech  = $_GET['filter_tech'] ?? '';
$filter_month = $_GET['filter_month'] ?? '';
$filter_date  = $_GET['filter_date'] ?? '';

$conditions = [];

if (!empty($filter_tech)) {
    $safe = $mysqli->real_escape_string($filter_tech);
    $conditions[] = "LOWER(technician) LIKE LOWER('%$safe%')";
}

if (!empty($filter_month)) {
    $map = [
        "January" => "01","February" => "02","March" => "03",
        "April" => "04","May" => "05","June" => "06",
        "July" => "07","August" => "08","September" => "09",
        "October" => "10","November" => "11","December" => "12"
    ];

    if (isset($map[$filter_month])) {
        $m = $map[$filter_month];
        $conditions[] = "start_month = '$m'";
    }
}

if (!empty($filter_date)) {
    $d = explode('-', $filter_date);
    if (count($d) === 3) {
        $y = $mysqli->real_escape_string($d[0]);
        $m = $mysqli->real_escape_string($d[1]);
        $day = $mysqli->real_escape_string($d[2]);

        $conditions[] = "(start_year = '$y' AND start_month = '$m' AND start_day = '$day')";
    }
}

$where = "";
if (!empty($conditions)) {
    $where = "WHERE " . implode(" AND ", $conditions);
}

if (empty($conditions)) {
    $sql = "SELECT * FROM build ORDER BY id DESC LIMIT 3";
} else {
    $sql = "SELECT * FROM build $where ORDER BY id DESC";
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
<title>LMTS Admin Panel (Build)</title>
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

.btn-red, .btn-gray {
    padding: 0.75rem 1.5rem;
    font-weight: bold;
    border-radius: 0.75rem;
    display: inline-block;
    transition: 0.2s;
}
.btn-red {
    background: linear-gradient(to right, #b91c1c, #f87171);
    color: white;
}
.btn-red:hover {
    background: linear-gradient(to right, #ef4444, #fca5a5);
}
.btn-gray {
    background: linear-gradient(to right, #374151, #1f2937);
    color: white;
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
    height: 3rem;
    width: 100%;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

thead th, tbody td {
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

/* Mobile stacked table */
@media (max-width: 768px) {
    table, thead, tbody, th, td, tr {
        display: block;
    }
    thead {
        display: none;
    }
    tbody tr {
        margin-bottom: 1rem;
        border: 1px solid #f00;
        border-radius: 0.75rem;
        padding: 0.5rem;
        background: rgba(0,0,0,0.2);
    }
    tbody td {
        border: none;
        display: flex;
        justify-content: space-between;
        padding: 0.25rem 0.5rem;
    }
    tbody td::before {
        content: attr(data-label);
        font-weight: bold;
    }
}
</style>
</head>
<body>
<div class="max-w-7xl mx-auto inner-box">

    <div class="flex flex-col sm:flex-row justify-between gap-4 items-start sm:items-center mb-6">
        <h1 class="text-3xl font-extrabold text-white drop-shadow-lg">
            LMTS Admin Panel (Link Build)
        </h1>
        <div class="flex gap-3 w-full sm:w-auto">
            <a href="build.html" class="btn-red">Go to Form</a>
            <a href="selection.html" class="btn-gray">Home</a>
        </div>
    </div>

    <form method="get" class="flex flex-col gap-3 mb-6">
        <div class="flex gap-3 flex-wrap">
            <div class="flex-1">
                <input type="text" name="filter_tech"
                       value="<?php echo htmlspecialchars($filter_tech); ?>"
                       placeholder="Filter by Technician...">
            </div>
            <div class="flex-1">
                <select name="filter_month">
                    <option value="">Filter by Month...</option>
                    <?php
                        $months = [
                            "January","February","March","April","May","June",
                            "July","August","September","October","November","December"
                        ];
                        foreach ($months as $m) {
                            $sel = ($filter_month === $m) ? "selected" : "";
                            echo "<option $sel>$m</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="flex-1">
                <input type="text" id="filter_date" name="filter_date"
                       value="<?php echo htmlspecialchars($filter_date); ?>"
                       placeholder="Filter by Date..." readonly>
            </div>
        </div>
        <div class="flex gap-3 flex-wrap">
            <button type="submit" class="btn-red">Search</button>
            <a href="buildAdmin.php" class="btn-gray">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Technician</th>
                    <th>Start Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Link Ref</th>
                    <th>Link Description</th>
                    <th>% Complete</th>
                    <th>Status</th>
                    <th>ATP Done</th>
                    <th>Link Length (km)</th>
                    <th>Type</th>
                    <th>A.S Build Submitted</th>
                    <th>Completed Date</th>
                    <th>Start Mileage (km)</th>
                    <th>End Mileage (km)</th>
                    <th>Total km</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="ID"><?php echo $row['id']; ?></td>
                        <td data-label="Technician"><?php echo htmlspecialchars($row['technician']); ?></td>
                        <td data-label="Start Date"><?php echo $row['start_year']."-".$row['start_month']."-".$row['start_day']; ?></td>
                        <td data-label="Start Time"><?php echo $row['start_hour'].":".$row['start_minute']; ?></td>
                        <td data-label="End Time"><?php echo $row['end_hour'].":".$row['end_minute']; ?></td>
                        <td data-label="Link Ref"><?php echo htmlspecialchars($row['link_ref']); ?></td>
                        <td data-label="Link Description"><?php echo htmlspecialchars($row['link_description']); ?></td>
                        <td data-label="% Complete"><?php echo htmlspecialchars($row['percentage']); ?></td>
                        <td data-label="Status"><?php echo htmlspecialchars($row['status']); ?></td>
                        <td data-label="ATP Done"><?php echo htmlspecialchars($row['atp']); ?></td>
                        <td data-label="Link Length (km)"><?php echo (int)$row['length']; ?></td>
                        <td data-label="Type"><?php echo htmlspecialchars($row['link_type']); ?></td>
                        <td data-label="A.S Build Submitted"><?php echo htmlspecialchars($row['as_build']); ?></td>
                        <td data-label="Completed Date"><?php echo ($row['comp_year'] && $row['comp_month'] && $row['comp_day']) ? $row['comp_year']."-".$row['comp_month']."-".$row['comp_day'] : "-"; ?></td>
                        <td data-label="Start Mileage (km)"><?php echo (int)$row['start_mileage']; ?></td>
                        <td data-label="End Mileage (km)"><?php echo (int)$row['end_mileage']; ?></td>
                        <td data-label="Total km"><?php echo (int)$row['total_km']; ?></td>
                        <td data-label="Comments"><?php echo htmlspecialchars($row['comments']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="18" class="text-center text-gray-400 p-4">
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
        allowInput: true
    });
</script>

<?php $mysqli->close(); ?>
</body>
</html>

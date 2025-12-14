<?php
require_once 'config.php';

// --------------------------
// Handle Filters
// --------------------------
$filter_date      = $_GET['filter_date'] ?? '';
$filter_ref       = $_GET['filter_ref'] ?? '';
$filter_material  = $_GET['filter_material'] ?? '';

$conditions = [];

if ($filter_date) {
    $safe = $mysqli->real_escape_string($filter_date);
    $conditions[] = "start_date = '$safe'";
}

if ($filter_ref) {
    $safe = $mysqli->real_escape_string($filter_ref);
    $conditions[] = "fault_ref LIKE '%$safe%'";
}

if ($filter_material) {
    $safe = $mysqli->real_escape_string($filter_material);
    $conditions[] = "material_used LIKE '%$safe%'";
}

$where = "";
if (!empty($conditions)) {
    $where = "WHERE " . implode(" AND ", $conditions);
}

// Query
$sql = !empty($conditions)
    ? "SELECT * FROM work_host $where ORDER BY id DESC"
    : "SELECT * FROM work_host ORDER BY id DESC LIMIT 3";

$result = $mysqli->query($sql);
if (!$result) {
    die("Query Error: " . $mysqli->error);
}

// Material list 
$materials = ["204B","Fist","1x2 Splitter","1x4 Splitter","1x16 Splitter","1x32 Splitter","Patch Panel","Patch Cord","ONT"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KZN LMTS Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
body {background: radial-gradient(circle at top, #725e5eff, #0f0f0fff);}
.inner-box {background: rgba(0,0,0,0.3); padding: 2rem; border-radius: 1.5rem; box-shadow: 0 0 2rem rgba(255,0,0,0.5); border: 1px solid #f00; color: white;}
input, select {background-color: #1f1f1f; color: #fff; font-weight: bold; border: 1px solid #f00; padding: 0.5rem; border-radius: 0.75rem;}
select option {background-color: #1f1f1f; color: #fff;}
input:focus, select:focus {outline: none; box-shadow: 0 0 0 2px rgba(255,0,0,0.5);}
.btn-primary {background: linear-gradient(to right, #b91c1c, #f87171); color: white; font-weight:bold; border-radius:0.5rem;}
.btn-primary:hover {background: linear-gradient(to right, #ef4444, #fca5a5);}
.btn-secondary {background: linear-gradient(to right, #374151, #1f2937); color:white; font-weight:bold; border-radius:0.5rem;}
.btn-secondary:hover {background: linear-gradient(to right, #4b5563, #111827);}
h1 {color: white; font-size: 2rem; font-weight: bold;}
table {width: 100%; border-collapse: collapse; margin-top: 1rem;}
th, td {border: 1px solid #f00; padding: 0.5rem; text-align: left;}
th {background-color: rgba(255,0,0,0.2);}
input[type="date"]::-webkit-calendar-picker-indicator {filter: invert(1); cursor: pointer;}
/* Autocomplete dropdown style */
.autocomplete-items {
  position: absolute;
  background-color: #1f1f1f;
  border: 1px solid #f00;
  border-radius: 0.5rem;
  z-index: 99;
  max-height: 200px;
  overflow-y: auto;
  color: white;
}
.autocomplete-items div {
  padding: 8px;
  cursor: pointer;
}
.autocomplete-items div:hover {
  background-color: rgba(255,0,0,0.2);
}
</style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

<div class="w-full max-w-7xl inner-box">

    <!-- Top row: Heading + Go to Form + Home -->
    <div class="flex justify-between items-center mb-4">
        <div class="flex-1">
            <h1>KZN LMTS ADMIN PANEL</h1>
        </div>
        <div class="flex gap-3">
            <a href="form.html" class="btn-primary px-6 py-2">Go to Form</a>
            <a href="selection.html" class="btn-secondary px-6 py-2">Home</a>
        </div>
    </div>

    <!-- Filter inputs -->
    <form id="filterForm" method="GET" class="grid grid-cols-3 gap-3 mb-4">
        <input type="date" name="filter_date" class="p-2 rounded w-full"
               value="<?= htmlspecialchars($filter_date ?? '') ?>">

        <input type="text" name="filter_ref" placeholder="Fault Ref"
               class="p-2 rounded w-full" value="<?= htmlspecialchars($filter_ref ?? '') ?>">

        <div class="relative">
            <input type="text" name="filter_material" id="filter_material"
                   placeholder="Material Used" class="p-2 rounded w-full pr-8"
                   value="<?= htmlspecialchars($filter_material ?? '') ?>" autocomplete="off">

            <!--  dropdown arrow -->
            <span id="arrowBtn"
                  class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-white text-lg select-none">
                ▼
            </span>

            <div id="material_list" class="autocomplete-items"></div>
        </div>
    </form>

    <!-- Filter buttons -->
    <div class="flex gap-3 mb-4">
        <button type="submit" form="filterForm" class="btn-primary w-1/6 py-2">Search</button>
        <a href="admin.php" class="btn-secondary w-1/6 py-2">Reset</a>
    </div>

    <!-- Table -->
    <table class="text-white">
        <tr>
            <th>ID</th>
            <th>Technician</th>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Fault Ref</th>
            <th>Fault Description</th>
            <th>Fault Status</th>
            <th>Work Summary</th>
            <th>Material Used</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php $counter = 1; ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $counter++; ?></td>
                    <td><?= htmlspecialchars($row['tech_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['start_date'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['start_time'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['end_time'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['fault_ref'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['fault_description'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['fault_status'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['work_summary'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['material_used'] ?? '') ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="10" class="text-center">No records found.</td></tr>
        <?php endif; ?>
    </table>

</div>

<script>
const materials = <?= json_encode($materials) ?>;

const input = document.getElementById("filter_material");
const listDiv = document.getElementById("material_list");

//  filtered list
function closeAllLists() {
    listDiv.innerHTML = "";
}

function showList(value) {
    closeAllLists();
    if (!value) return;
    const filtered = materials.filter(m => m.toLowerCase().includes(value.toLowerCase()));
    filtered.forEach(item => {
        const div = document.createElement("div");
        div.textContent = item;
        div.addEventListener("click", () => {
            input.value = item;
            closeAllLists();
        });
        listDiv.appendChild(div);
    });
}

// Input events
input.addEventListener("mouseenter", () => showList(""));
input.addEventListener("input", () => showList(input.value));
document.addEventListener("click", (e) => {
    if (e.target !== input && e.target.id !== "arrowBtn") closeAllLists();
});

// Dropdown arrow event
document.getElementById("arrowBtn").addEventListener("click", () => {
    showList(""); // Show all the material
});
</script>

</body>
</html>

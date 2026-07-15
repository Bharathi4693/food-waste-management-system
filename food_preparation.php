<?php
session_start();
include("db.php");

$success = "";
$error = "";

if(isset($_POST['submit'])) {

    $date = $_POST['date'];
    $food_name = trim($_POST['food_name']);
    $quantity = $_POST['quantity'];

    if(empty($date) || empty($food_name) || empty($quantity)){
        $error = "All fields are required!";
    } else {

        $day = date('l', strtotime($date));

        if(isset($_POST['function_day'])) {
            $day_type = "Function";
        }
        elseif($day == "Saturday" || $day == "Sunday") {
            $day_type = "Weekend";
        }
        else {
            $day_type = "Normal";
        }

        $stmt = $conn->prepare("INSERT INTO food_preparation (date, day_type, food_name, quantity_prepared) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssd", $date, $day_type, $food_name, $quantity);

        if($stmt->execute()){
            $success = "Food preparation saved successfully!";
        } else {
            $error = "Something went wrong!";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Food Preparation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

body {
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:#0f2027;
    color:white;
}

/* Sidebar */
.sidebar {
    width:220px;
    height:100vh;
    position:fixed;
    background:#111;
    transition:0.3s;
    overflow:hidden;
}

.sidebar.collapsed {
    width:70px;
}

.sidebar h2 {
    text-align:center;
    padding:20px 0;
    font-size:18px;
}

.sidebar a {
    display:block;
    padding:15px 20px;
    color:white;
    text-decoration:none;
    transition:0.3s;
}

.sidebar a:hover {
    background:#222;
}

/* Toggle Button */
.toggle-btn {
    position:absolute;
    top:15px;
    right:-15px;
    background:#00c6ff;
    border-radius:50%;
    width:30px;
    height:30px;
    text-align:center;
    line-height:30px;
    cursor:pointer;
    font-weight:bold;
}

/* Content */
.content {
    margin-left:220px;
    padding:40px;
    transition:0.3s;
}

.content.shift {
    margin-left:70px;
}

/* Card */
.card {
    background:#1c1c1c;
    padding:30px;
    border-radius:15px;
    width:400px;
}

input, button {
    width:100%;
    padding:10px;
    margin-bottom:15px;
    border:none;
    border-radius:8px;
}

input[type="checkbox"] {
    width:auto;
}

button {
    background:#00c6ff;
    color:white;
    cursor:pointer;
}

button:hover {
    background:#0072ff;
}

/* Table */
table {
    width:100%;
    margin-top:30px;
    border-collapse:collapse;
}

th, td {
    padding:12px;
    text-align:center;
}

th {
    background:#222;
}

tr:nth-child(even){
    background:#1a1a1a;
}

.success {
    color:#00ff9d;
}

.error {
    color:#ff4d4d;
}

</style>
</head>

<body>

<div class="sidebar" id="sidebar">
    <div class="toggle-btn" onclick="toggleSidebar()">☰</div>
    <h2>Menu</h2>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="food_preparation.php">🍽 Preparation</a>
    <a href="consumption.php">🍴 Consumption</a>
    <a href="forecast.php">📊 Forecast</a>
    <a href="reports.php">📄 Reports</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="content" id="content">

<h2>🍽 Food Preparation Entry</h2>

<?php if($success) echo "<p class='success'>$success</p>"; ?>
<?php if($error) echo "<p class='error'>$error</p>"; ?>

<div class="card">
<form method="POST">

<label>Select Date</label>
<input type="date" name="date" required>

<div style="margin:15px 0;">
    <input type="checkbox" name="function_day" id="function_day">
    <label for="function_day" style="display:inline;"> Function / Marriage Day</label>
</div>

<label>Food Name</label>
<input type="text" name="food_name" placeholder="Enter Food Name" required>

<label>Quantity Prepared (kg)</label>
<input type="number" step="0.01" name="quantity" required>

<button type="submit" name="submit">Save</button>

</form>
</div>

<h3>📋 Saved Records</h3>

<?php
$result = mysqli_query($conn, "SELECT * FROM food_preparation ORDER BY id DESC");

if(mysqli_num_rows($result) > 0) {
?>

<table>
<tr>
    <th>ID</th>
    <th>Date</th>
    <th>Day Type</th>
    <th>Food Name</th>
    <th>Quantity (kg)</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['date']; ?></td>
    <td><?php echo $row['day_type']; ?></td>
    <td><?php echo $row['food_name']; ?></td>
    <td><?php echo $row['quantity_prepared']; ?></td>
</tr>
<?php } ?>

</table>

<?php } else {
    echo "No records found";
} ?>

</div>

<script>
function toggleSidebar(){
    document.getElementById("sidebar").classList.toggle("collapsed");
    document.getElementById("content").classList.toggle("shift");
}
</script>

</body>
</html>
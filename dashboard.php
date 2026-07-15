<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'db.php';

/* OVERALL TOTALS */
$sql = "
SELECT 
    SUM(fp.quantity_prepared) AS total_prepared,
    SUM(c.morning + c.afternoon + c.evening + c.night) AS total_consumed
FROM food_preparation fp
JOIN consumption c ON fp.id = c.preparation_id
";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$total_prepared = $row['total_prepared'] ?? 0;
$total_consumed = $row['total_consumed'] ?? 0;

$total_waste = $total_prepared - $total_consumed;
$waste_percent = $total_prepared > 0 ? round(($total_waste / $total_prepared) * 100, 2) : 0;

/* TODAY WASTE */
$today_sql = "
SELECT 
    SUM(fp.quantity_prepared) AS today_prepared,
    SUM(c.morning + c.afternoon + c.evening + c.night) AS today_consumed
FROM food_preparation fp
JOIN consumption c ON fp.id = c.preparation_id
WHERE fp.date = CURDATE()
";

$today_result = mysqli_query($conn, $today_sql);
$today_row = mysqli_fetch_assoc($today_result);
$today_waste = ($today_row['today_prepared'] ?? 0) - ($today_row['today_consumed'] ?? 0);

/* MONTH WASTE */
$month_sql = "
SELECT 
    SUM(fp.quantity_prepared) AS month_prepared,
    SUM(c.morning + c.afternoon + c.evening + c.night) AS month_consumed
FROM food_preparation fp
JOIN consumption c ON fp.id = c.preparation_id
WHERE MONTH(fp.date) = MONTH(CURDATE())
AND YEAR(fp.date) = YEAR(CURDATE())
";

$month_result = mysqli_query($conn, $month_sql);
$month_row = mysqli_fetch_assoc($month_result);
$month_waste = ($month_row['month_prepared'] ?? 0) - ($month_row['month_consumed'] ?? 0);

/* SMART MESSAGE */
if ($waste_percent < 10) {
    $status_message = "Excellent! Waste is under control.";
} elseif ($waste_percent < 25) {
    $status_message = "Moderate Waste. Try to optimize preparation.";
} else {
    $status_message = "High Waste Alert! Immediate action required.";
}

/* FORECAST */
$average_consumption = $total_consumed > 0 ? round($total_consumed, 2) : 0;
$suggested_preparation = round($average_consumption * 1.10, 2);
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background: linear-gradient(135deg,#1e3c72,#2a5298);
    color:#fff;
}

.main-container{
    display:flex;
    transition:0.4s ease;
}

/* SIDEBAR */
.sidebar{
    width:250px;
    height:100vh;
    background:rgba(0,0,0,0.4);
    backdrop-filter:blur(15px);
    padding:20px;
    transition:width 0.4s ease;
    overflow:hidden;
}

.sidebar h2{
    text-align:center;
    margin-bottom:30px;
}

.sidebar ul{
    list-style:none;
    padding:0;
}

.sidebar ul li{
    margin:15px 0;
}

.sidebar ul li a{
    text-decoration:none;
    color:#fff;
    display:flex;
    align-items:center;
    gap:10px;
    padding:10px;
    border-radius:10px;
    transition:0.3s;
}

.sidebar ul li a:hover{
    background:rgba(255,255,255,0.2);
}

/* COLLAPSE MODE */
.sidebar.collapsed{
    width:80px;
}

.sidebar.collapsed h2{
    display:none;
}

.sidebar.collapsed ul li a span{
    display:none;
}

.sidebar.collapsed ul li a{
    justify-content:center;
}

/* CONTENT */
.content{
    flex:1;
    padding:20px;
}

/* Stylish Toggle Button */
.toggle-btn{
    font-size:18px;
    background:rgba(255,255,255,0.2);
    border:none;
    color:white;
    padding:8px 12px;
    border-radius:8px;
    cursor:pointer;
    transition:0.3s;
    margin-bottom:15px;
}

.toggle-btn:hover{
    background:rgba(255,255,255,0.4);
}

.welcome{
    font-size:22px;
    margin-bottom:20px;
}

.stats-container{
    display:grid;
    grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
    gap:25px;
}

.stat-box{
    padding:25px;
    border-radius:18px;
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(15px);
    text-align:center;
    transition:0.4s;
    box-shadow:0 8px 25px rgba(0,0,0,0.3);
}

.stat-box:hover{
    transform:translateY(-8px);
}

.stat-box h3{ margin:0; font-size:18px; }
.stat-box p{ font-size:26px; margin-top:12px; font-weight:bold; }

.prepared{ border-left:6px solid #00c6ff; }
.consumed{ border-left:6px solid #00ff9d; }
.waste{ border-left:6px solid #ff4e50; }
.percent{ border-left:6px solid #c471f5; }
.today{ border-left:6px solid #f7971e; }
.month{ border-left:6px solid #00d2ff; }
.forecast{ border-left:6px solid #34495e; }

.status-box{
    margin:30px 0;
    padding:18px;
    border-radius:15px;
    text-align:center;
    background:rgba(255,255,255,0.2);
}

.chart-container{
    background:rgba(255,255,255,0.15);
    padding:30px;
    border-radius:20px;
}
.stat-box {
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
}
/* Mini Insight Card */
.mini-insight{
    margin-top:25px;
    padding:20px;
    border-radius:18px;
    background:rgba(255,255,255,0.12);
    backdrop-filter:blur(12px);
    display:flex;
    justify-content:space-between;
    align-items:center;
    transition:0.3s ease;
}

.mini-insight:hover{
    transform:translateY(-5px);
}

.mini-insight .left{
    font-size:15px;
}

.mini-insight .right{
    font-size:32px;
    font-weight:bold;
}
</style>
</head>

<body>

<div class="main-container">

    <div class="sidebar" id="sidebar">
        <h2>🍽 FoodSys</h2>
        <ul>
            <li><a href="dashboard.php"><i class="fa-solid fa-house"></i><span>Dashboard</span></a></li>
            <li><a href="food_preparation.php"><i class="fa-solid fa-kitchen-set"></i><span>Preparation</span></a></li>
            <li><a href="consumption.php"><i class="fa-solid fa-chart-column"></i><span>Consumption</span></a></li>
            <li><a href="forecast.php"><i class="fa-solid fa-chart-line"></i><span>Forecast</span></a></li>
            <li><a href="reports.php"><i class="fa-solid fa-file"></i><span>Reports</span></a></li>
            <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a></li>
        </ul>
    </div>

    <div class="content">

        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="welcome">
            Welcome <?php echo $_SESSION['username']; ?>
        </div>

        <div class="stats-container">
            <div class="stat-box prepared">
                <h3><i class="fa-solid fa-box"></i> Total Prepared</h3>
                <p><?php echo $total_prepared; ?> kg</p>
            </div>

            <div class="stat-box consumed">
                <h3><i class="fa-solid fa-utensils"></i> Total Consumed</h3>
                <p><?php echo $total_consumed; ?> kg</p>
            </div>

            <div class="stat-box waste">
                <h3><i class="fa-solid fa-trash"></i> Total Waste</h3>
                <p><?php echo $total_waste; ?> kg</p>
            </div>

            <div class="stat-box percent">
                <h3><i class="fa-solid fa-chart-pie"></i> Waste %</h3>
                <p><?php echo $waste_percent; ?>%</p>
            </div>

            <div class="stat-box today">
                <h3><i class="fa-solid fa-calendar-day"></i> Today's Waste</h3>
                <p><?php echo $today_waste; ?> kg</p>
            </div>

            <div class="stat-box month">
                <h3><i class="fa-solid fa-calendar"></i> This Month Waste</h3>
                <p><?php echo $month_waste; ?> kg</p>
            </div>

            <div class="stat-box forecast">
                <h3><i class="fa-solid fa-lightbulb"></i> Tomorrow Suggestion</h3>
                <p><?php echo $suggested_preparation; ?> kg</p>
            </div>
        </div>

        <div class="status-box">
            <?php echo $status_message; ?>
        </div>

        <div class="chart-container">
            <canvas id="wasteChart"></canvas>
        </div>
        <div class="mini-insight">
    <div class="left">
        <i class="fa-solid fa-seedling"></i> Efficiency Score
        <br>
        <small>Based on waste control</small>
    </div>
    <div class="right">
        <?php echo 100 - $waste_percent; ?>%
    </div>
</div>
    </div>
</div>

<script>
document.querySelectorAll('.stat-box p').forEach(el => {
    let text = el.innerText.replace("kg","").replace("%","");
    let value = parseFloat(text);
    let suffix = el.innerText.includes("%") ? "%" : " kg";

    let count = 0;
    let increment = value / 50;

    let interval = setInterval(() => {
        count += increment;
        if (count >= value) {
            el.innerText = value.toFixed(2) + suffix;
            clearInterval(interval);
        } else {
            el.innerText = count.toFixed(0) + suffix;
        }
    }, 20);
});
</script>

</body>
</html>
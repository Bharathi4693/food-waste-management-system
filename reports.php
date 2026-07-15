<?php
include("db.php");

$daily_total = 0;
$weekly_total = 0;
$monthly_total = 0;
$time_report = [];
$date_range_total = 0;
$range_data = [];

/* DAILY */
if(isset($_POST['daily']) && !empty($_POST['date'])){
    $date = mysqli_real_escape_string($conn, $_POST['date']);

    $sql = "SELECT fp.quantity_prepared,
            c.morning,c.afternoon,c.evening,c.night
            FROM food_preparation fp
            JOIN consumption c ON fp.id=c.preparation_id
            WHERE fp.date='$date'";

    $result = mysqli_query($conn,$sql);
    while($row=mysqli_fetch_assoc($result)){
        $consumed = $row['morning'] + $row['afternoon'] + $row['evening'] + $row['night'];
        $daily_total += ($row['quantity_prepared'] - $consumed);
    }
}

/* WEEKLY */
if(isset($_POST['weekly']) && !empty($_POST['week'])){
    $week = intval($_POST['week']);
    $year = date("Y");

    $sql = "SELECT fp.quantity_prepared,
            c.morning,c.afternoon,c.evening,c.night
            FROM food_preparation fp
            JOIN consumption c ON fp.id=c.preparation_id
            WHERE WEEK(fp.date,1)='$week'
            AND YEAR(fp.date)='$year'";

    $result = mysqli_query($conn,$sql);
    while($row=mysqli_fetch_assoc($result)){
        $consumed = $row['morning'] + $row['afternoon'] + $row['evening'] + $row['night'];
        $weekly_total += ($row['quantity_prepared'] - $consumed);
    }
}

/* MONTHLY */
if(isset($_POST['monthly']) && !empty($_POST['month'])){
    $month = intval($_POST['month']);
    $year = date("Y");

    $sql = "SELECT fp.quantity_prepared,
            c.morning,c.afternoon,c.evening,c.night
            FROM food_preparation fp
            JOIN consumption c ON fp.id=c.preparation_id
            WHERE MONTH(fp.date)='$month'
            AND YEAR(fp.date)='$year'";

    $result = mysqli_query($conn,$sql);
    while($row=mysqli_fetch_assoc($result)){
        $consumed = $row['morning'] + $row['afternoon'] + $row['evening'] + $row['night'];
        $monthly_total += ($row['quantity_prepared'] - $consumed);
    }
}

/* TIMEWISE */
if(isset($_POST['timewise']) && !empty($_POST['time_date'])){
    $date = mysqli_real_escape_string($conn, $_POST['time_date']);

    $sql="SELECT c.morning,c.afternoon,c.evening,c.night
          FROM food_preparation fp
          JOIN consumption c ON fp.id=c.preparation_id
          WHERE fp.date='$date'";

    $result=mysqli_query($conn,$sql);
    while($row=mysqli_fetch_assoc($result)){
        $time_reports['morning']   = ($time_report['morning'] ?? 0) + $row['morning'];
        $time_reports['afternoon'] = ($time_report['afternoon'] ?? 0) + $row['afternoon'];
        $time_reports['evening']   = ($time_report['evening'] ?? 0) + $row['evening'];
        $time_reports['night']     = ($time_report['night'] ?? 0) + $row['night'];
    }
}

/* DATE RANGE */
if(isset($_POST['daterange']) && !empty($_POST['from_date']) && !empty($_POST['to_date'])){
    $from = mysqli_real_escape_string($conn,$_POST['from_date']);
    $to   = mysqli_real_escape_string($conn,$_POST['to_date']);

    $sql="SELECT fp.date,fp.quantity_prepared,
          c.morning,c.afternoon,c.evening,c.night
          FROM food_preparation fp
          JOIN consumption c ON fp.id=c.preparation_id
          WHERE fp.date BETWEEN '$from' AND '$to'";

    $result=mysqli_query($conn,$sql);
    while($row=mysqli_fetch_assoc($result)){
        $consumed = $row['morning'] + $row['afternoon'] + $row['evening'] + $row['night'];
        $waste = $row['quantity_prepared'] - $consumed;

        $date_range_total += $waste;
        $range_data[$row['date']] = $waste;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Reports</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
margin:0;
font-family:'Segoe UI',sans-serif;
background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
color:white;
}

.sidebar{
width:220px;
height:100vh;
position:fixed;
background:rgba(0,0,0,0.4);
padding-top:30px;
}

.sidebar a{
display:block;
color:white;
padding:15px 20px;
text-decoration:none;
transition:0.3s;
}

.sidebar a:hover{
background:rgba(255,255,255,0.1);
}

.content{
margin-left:240px;
padding:40px;
display:flex;
flex-wrap:wrap;
gap:20px;
}

.card{
background:rgba(255,255,255,0.08);
padding:20px;
border-radius:15px;
backdrop-filter:blur(10px);
width:320px;
}

.fullwidth{
width:700px;
}

input,button{
padding:8px;
border-radius:8px;
border:none;
margin-top:5px;
}

button{
background:#00c6ff;
color:white;
cursor:pointer;
width:100%;
}

button:hover{
background:#0072ff;
}

.result{
margin-top:10px;
font-weight:bold;
color:#00ff9d;
}
</style>
</head>
<body>

<div class="sidebar">
<a href="/food_waste/dashboard.php">🏠 Dashboard</a>
<a href="/food_waste/food_preparation.php">🍽 Preparation</a>
<a href="/food_waste/consumption.php">🍴 Consumption</a>
<a href="/food_waste/forecast.php">📊 Forecast</a>
<a href="/food_waste/reports.php">📄 Reports</a>
<a href="/food_waste/logout.php">🚪 Logout</a>
</div>

<div class="content">

<!-- DAILY -->
<div class="card">
<h3>Daily Waste</h3>
<form method="POST">
<input type="date" name="date" required>
<button name="daily">Check</button>
</form>
<div class="result">Total Waste: <?php echo round($daily_total,2); ?> kg</div>
</div>

<!-- WEEKLY -->
<div class="card">
<h3>Weekly Waste</h3>
<form method="POST">
<input type="number" name="week" min="1" max="52" required>
<button name="weekly">Check</button>
</form>
<div class="result">Total Waste: <?php echo round($weekly_total,2); ?> kg</div>
</div>

<!-- MONTHLY -->
<div class="card">
<h3>Monthly Waste</h3>
<form method="POST">
<input type="number" name="month" min="1" max="12" required>
<button name="monthly">Check</button>
</form>
<div class="result">Total Waste: <?php echo round($monthly_total,2); ?> kg</div>
</div>

<!-- TIMEWISE -->
<div class="card">
<h3>Time-wise Consumption</h3>
<form method="POST">
<input type="date" name="time_date" required>
<button name="timewise">Check</button>
</form>

<?php if(!empty($time_report)){ ?>
<canvas id="timeChart"></canvas>
<script>
new Chart(document.getElementById('timeChart'),{
type:'bar',
data:{
labels:['Morning','Afternoon','Evening','Night'],
datasets:[{
label:'Consumption (kg)',
data:[
<?php echo $time_reports['morning'] ?? 0; ?>,
<?php echo $time_reports['afternoon'] ?? 0; ?>,
<?php echo $time_reports['evening'] ?? 0; ?>,
<?php echo $time_reports['night'] ?? 0; ?>
]
}]
}
});
</script>
<?php } ?>
</div>

<!-- DATE RANGE -->
<div class="card fullwidth">
<h3>Date Range Waste Report</h3>
<form method="POST">
From:<input type="date" name="from_date" required>
To:<input type="date" name="to_date" required>
<button name="daterange">Generate</button>
</form>

<div class="result">Total Waste: <?php echo round($date_range_total,2); ?> kg</div>

<?php if(!empty($range_data)){ ?>
<canvas id="rangeChart"></canvas>
<script>
new Chart(document.getElementById('rangeChart'),{
type:'bar',
data:{
labels: <?php echo json_encode(array_keys($range_data)); ?>,
datasets:[{
label:'Waste (kg)',
data: <?php echo json_encode(array_values($range_data)); ?>
}]
}
});
</script>
<?php } ?>
</div>

</div>
</body>
</html>
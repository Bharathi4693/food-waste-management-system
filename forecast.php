<?php
session_start();
include("db.php");

$suggested = "";
$average = "";
$day_type = "";
$buffer = 0;

if(isset($_POST['check'])){

    $selected_date = $_POST['date'];

    $day_name = date('l', strtotime($selected_date));

    if($day_name == "Saturday" || $day_name == "Sunday"){
        $day_type = "Weekend";
        $buffer = 0.20;
        $day_type_id = 2;
    } else {
        $day_type = "Normal";
        $buffer = 0.10;
        $day_type_id = 1;
    }

    $sql = "SELECT AVG(total_consumed) AS avg_qty 
            FROM consumption 
            WHERE day_type_id = '$day_type_id'";

    $result = mysqli_query($conn, $sql);

    if($result){
        $row = mysqli_fetch_assoc($result);
        $average = $row['avg_qty'];

        if($average == NULL){
            $average = 0;
        }

        $suggested = $average + ($average * $buffer);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Demand Forecast</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    color:white;
}

/* Sidebar */
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

/* Content */
.content{
    margin-left:240px;
    padding:40px;
}

/* Glass Card */
.card{
    background:rgba(255,255,255,0.08);
    padding:30px;
    border-radius:15px;
    backdrop-filter:blur(10px);
    width:500px;
}

/* Button */
button{
    background:#00c6ff;
    border:none;
    padding:10px;
    border-radius:8px;
    color:white;
    cursor:pointer;
}

button:hover{
    background:#0072ff;
}

input{
    padding:10px;
    border-radius:8px;
    border:none;
    margin-right:10px;
}

/* Result Box */
.result{
    margin-top:20px;
    padding:20px;
    border-radius:12px;
    background:rgba(0,255,255,0.1);
}

/* Animation */
body{
    animation:fadeIn 0.6s ease-in;
}
@keyframes fadeIn{
    from{opacity:0;transform:translateY(10px);}
    to{opacity:1;transform:translateY(0);}
}
</style>

</head>
<body>

<div class="sidebar">
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="food_preparation.php">🍽 Preparation</a>
    <a href="consumption.php">🍴 Consumption</a>
    <a href="forecast.php">📊 Forecast</a>
    <a href="reports.php">📄 Reports</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="content">

<h2>📊 Demand Forecasting</h2>

<div class="card">

<form method="POST">
    <label>Select Date:</label><br><br>
    <input type="date" name="date" required>
    <button type="submit" name="check">Check Forecast</button>
</form>

<?php if($suggested !== ""){ ?>

<div class="result">
    <h3>Day Type: <?php echo $day_type; ?></h3>
    <h3>Average Consumption (<?php echo $day_type; ?> Days): 
        <?php echo round($average,2); ?> kg</h3>
    <h3>Buffer Added: <?php echo ($buffer * 100); ?>%</h3>
    <h3>Suggested Preparation Quantity: 
        <?php echo round($suggested,2); ?> kg</h3>
</div>

<?php } ?>

</div>

</div>
</body>
</html>
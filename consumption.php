<?php
include("db.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html>
<head>
<title>Consumption Entry</title>

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

/* Form */
form{
    background:rgba(255,255,255,0.08);
    padding:30px;
    border-radius:15px;
    backdrop-filter:blur(10px);
    width:500px;
}

input,select{
    width:100%;
    padding:10px;
    margin-bottom:15px;
    border-radius:8px;
    border:none;
}

button{
    background:#00c6ff;
    border:none;
    padding:10px;
    border-radius:8px;
    color:white;
    cursor:pointer;
    width:100%;
}

button:hover{
    background:#0072ff;
}

/* Success Box */
.result-box{
    margin-top:20px;
    padding:20px;
    border-radius:10px;
    background:rgba(0,255,0,0.15);
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

<script>
function calculateTotal(){
    let m = parseFloat(document.getElementById("morning").value) || 0;
    let a = parseFloat(document.getElementById("afternoon").value) || 0;
    let e = parseFloat(document.getElementById("evening").value) || 0;
    let n = parseFloat(document.getElementById("night").value) || 0;

    let total = m + a + e + n;
    document.getElementById("total_display").innerText = total.toFixed(2) + " kg";
}

function validateForm(){
    return true;
}
</script>

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

<h2>🍴 Enter Consumption</h2>

<form method="POST" onsubmit="return validateForm()">

<label>Day Type:</label>
<select name="day_type_id" required>
<option value="">Select</option>
<?php
$result = mysqli_query($conn, "SELECT * FROM day_type");
while($row = mysqli_fetch_assoc($result)){
    echo "<option value='".$row['id']."'>".$row['day_type']."</option>";
}
?>
</select>

<label>Waste Type:</label>
<select name="waste_type">
<option value="">Select</option>
<option value="Edible">Edible</option>
<option value="Partially Edible">Partially Edible</option>
<option value="Non-Edible">Non-Edible</option>
</select>

<label>Select Preparation:</label>
<select name="preparation" required>
<option value="">Select</option>
<?php
$prepQuery = "SELECT * FROM food_preparation";
$prepResult = mysqli_query($conn, $prepQuery);
while($row = mysqli_fetch_assoc($prepResult)){
echo "<option value='".$row['id']."'>".$row['food_name']." (".$row['quantity_prepared']." kg)</option>";
}
?>
</select>

<label>Morning (kg):</label>
<input type="number" step="0.01" id="morning" name="morning" onkeyup="calculateTotal()" required>

<label>Afternoon (kg):</label>
<input type="number" step="0.01" id="afternoon" name="afternoon" onkeyup="calculateTotal()" required>

<label>Evening (kg):</label>
<input type="number" step="0.01" id="evening" name="evening" onkeyup="calculateTotal()" required>

<label>Night (kg):</label>
<input type="number" step="0.01" id="night" name="night" onkeyup="calculateTotal()" required>

<h3>Total Consumption: <span id="total_display">0 kg</span></h3>

<button type="submit" name="save">Save</button>

</form>

<?php
if(isset($_POST['save'])){

$prep_id=$_POST['preparation'];
$day_type_id=$_POST['day_type_id'];
$waste_type=$_POST['waste_type'];
$morning=$_POST['morning'];
$afternoon=$_POST['afternoon'];
$evening=$_POST['evening'];
$night=$_POST['night'];

$getPrep="SELECT quantity_prepared FROM food_preparation WHERE id='$prep_id'";
$result=mysqli_query($conn,$getPrep);
$row=mysqli_fetch_assoc($result);
$total_prepared=$row['quantity_prepared'];

$total_consumed=$morning+$afternoon+$evening+$night;
$leftover=$total_prepared-$total_consumed;

if($leftover<=0){
$leftover=0;
$waste_type="No Waste";
$recommendation="No action required - All food consumed.";
}
else{

if($waste_type==""){
echo "<p style='color:red;'>Please select waste type.</p>";
exit();
}

if($leftover<=20){
$recommendation=($waste_type=="Edible")?
"Reuse in next meal (Low Quantity)":
(($waste_type=="Partially Edible")?
"Use as small animal feed":
"Send to compost unit");
}
elseif($leftover<=100){
$recommendation=($waste_type=="Edible")?
"Donate immediately (Medium Quantity)":
(($waste_type=="Partially Edible")?
"Priority animal feed distribution":
"Compost processing required");
}
else{
$recommendation=($waste_type=="Edible")?
"URGENT: Bulk donation required!":
(($waste_type=="Partially Edible")?
"Bulk animal feed supply":
"Send for Bio-Gas Production");
}
}

$sql="INSERT INTO consumption
(preparation_id,day_type_id,morning,afternoon,evening,night,total_consumed,leftover_quantity,waste_type,recommendation)
VALUES
('$prep_id','$day_type_id','$morning','$afternoon','$evening','$night','$total_consumed','$leftover','$waste_type','$recommendation')";

if(mysqli_query($conn,$sql)){
echo "<div class='result-box'>
<strong>Saved Successfully!</strong><br><br>
Total Consumed: $total_consumed kg <br>
Leftover: $leftover kg <br>
Waste Type: $waste_type <br>
Recommendation: $recommendation
</div>";
}else{
echo "Error: ".mysqli_error($conn);
}
}
?>

</div>
</body>
</html>
<?php
session_start();

if(isset($_POST['confirm_logout'])){
    session_destroy();
    echo "<script>var loggedOut=true;</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Logout</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#141e30,#243b55);
    overflow:hidden;
}

/* Glass Card */
.logout-box{
    padding:40px;
    width:350px;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(15px);
    border-radius:20px;
    text-align:center;
    box-shadow:0 8px 32px rgba(0,0,0,0.4);
    color:white;
}

button{
    padding:10px 20px;
    margin:10px;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
}

.logout-btn{
    background:#ff4b2b;
    color:white;
}

.cancel-btn{
    background:#00c6ff;
    color:white;
}

.logout-btn:hover{
    transform:scale(1.05);
}
</style>
</head>

<body>

<div class="logout-box">
    <h2>⚠ Are you sure?</h2>
    <p style="opacity:0.8;">Do you really want to logout?</p>
    <form method="POST">
        <button type="submit" name="confirm_logout" class="logout-btn">Yes, Logout</button>
        <button type="button" onclick="window.location='dashboard.php'" class="cancel-btn">Cancel</button>
    </form>
</div>

<!-- Logout Sound -->
<audio id="logoutSound">
    <source src="https://www.soundjay.com/buttons/sounds/button-3.mp3" type="audio/mpeg">
</audio>

<script>
if(typeof loggedOut !== 'undefined'){
    document.getElementById("logoutSound").play();

    Swal.fire({
        title: 'Logged Out!',
        text: 'You have been successfully logged out.',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    });

    setTimeout(function(){
        window.location = "index.php";
    },2000);
}
</script>

</body>
</html>
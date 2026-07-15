<?php
session_start();
include("db.php");

$error = "";

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $_SESSION['username'] = $username;
        echo "<script>
                document.addEventListener('DOMContentLoaded', function(){
                    document.getElementById('loader').style.display='flex';
                    setTimeout(function(){
                        window.location='dashboard.php';
                    },2000);
                });
              </script>";
    } else {
        $error = "Invalid Login!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Food Waste Management - Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    height:100vh;
    overflow:hidden;
    background:#0f2027;
}

/* Particles canvas */
#particles-js{
    position:absolute;
    width:100%;
    height:100%;
    z-index:-1;
}

/* Login Card */
.login-box{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    width:350px;
    padding:40px;
    background:rgba(255,255,255,0.1);
    backdrop-filter:blur(15px);
    border-radius:20px;
    box-shadow:0 8px 32px rgba(0,0,0,0.4);
    color:white;
    text-align:center;
}

h2{
    margin-bottom:25px;
}

input{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border:none;
    border-radius:10px;
    outline:none;
}

button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:#00c6ff;
    color:white;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#0072ff;
    transform:scale(1.05);
}

.error{
    color:#ff6b6b;
    margin-bottom:10px;
}

/* Loader */
#loader{
    position:fixed;
    width:100%;
    height:100%;
    background:#0f2027;
    display:none;
    justify-content:center;
    align-items:center;
    flex-direction:column;
    color:white;
    z-index:999;
}

.spinner{
    width:50px;
    height:50px;
    border:5px solid rgba(255,255,255,0.2);
    border-top:5px solid #00c6ff;
    border-radius:50%;
    animation:spin 1s linear infinite;
    margin-bottom:15px;
}

@keyframes spin{
    0%{transform:rotate(0deg);}
    100%{transform:rotate(360deg);}
}
</style>
</head>

<body>

<div id="particles-js"></div>

<div class="login-box">
<h2>🍽 Food Waste Management</h2>

<?php if($error!=""){ ?>
<div class="error"><?php echo $error; ?></div>
<?php } ?>

<form method="POST">
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit" name="login">Login</button>
</form>
</div>

<!-- Loader Screen -->
<div id="loader">
<div class="spinner"></div>
<p>Logging in...</p>
</div>

<!-- Particles JS CDN -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

<script>
particlesJS("particles-js", {
  "particles": {
    "number": {"value": 80},
    "size": {"value": 3},
    "move": {"speed": 2},
    "line_linked": {"enable": true}
  }
});
</script>

</body>
</html>
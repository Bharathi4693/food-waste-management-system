<?php
$conn = mysqli_connect("localhost", "root", "", "food_waste",3306);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

?>
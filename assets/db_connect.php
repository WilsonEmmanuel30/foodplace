<?php
$conn = mysqli_connect('localhost', 'root','','foodplace_db');

if(!$conn) {
    die("Database Error: " . mysqli_error($conn));
}
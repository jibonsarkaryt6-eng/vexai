<?php
session_start();
$host = "sql305.infinityfree.com";
$user = "if0_42898622";
$pass = "ArifYt09"; // <-- এখানে নতুন Password দিবে Change করার পর
$dbname = "if0_42898622_VEXAI";

$conn = new mysqli($host, $user, $pass, $dbname);
if($conn->connect_error){ die("DB Connection Failed"); }

// base url
$base_url = "https://vexal.infinityfreeapp.com";
?>
<?php
$host = "";
$user = "";
$pass = "";
$db   = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Помилка з'єднання: " . $conn->connect_error);
}
session_start();
?>
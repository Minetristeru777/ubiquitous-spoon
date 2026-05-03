<?php
require_once("../config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $file = null;

    if (!empty($_FILES["music"]["name"])) {
        $targetDir = "../uploads/music/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $file = time() . "_" . basename($_FILES["music"]["name"]);
        move_uploaded_file($_FILES["music"]["tmp_name"], $targetDir . $file);
    }

    $stmt = $conn->prepare("INSERT INTO music (user_id, title, file) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_SESSION["user_id"], $title, $file);
    $stmt->execute();

    header("Location: view.php");
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Завантажити музику</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<header><h1>Minetcord</h1></header>
<div class="container">
  <h2>Завантажити трек</h2>
  <form method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Назва треку" required><br>
    <input type="file" name="music" accept="audio/*" required><br>
    <input type="submit" value="Завантажити">
  </form>
</div>
</body>
</html>
<?php
require_once("../config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST["content"];
    $image = null;

    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "../uploads/images/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetDir . $image);
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_SESSION["user_id"], $content, $image);
    $stmt->execute();

    header("Location: ../index.php");
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Новий пост</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<header><h1>Minetcord</h1></header>
<div class="container">
  <h2>Створити пост</h2>
  <form method="post" enctype="multipart/form-data">
    <textarea name="content" placeholder="Текст поста..." required></textarea><br>
    <input type="file" name="image"><br>
    <input type="submit" value="Опублікувати">
  </form>
</div>
</body>
</html>
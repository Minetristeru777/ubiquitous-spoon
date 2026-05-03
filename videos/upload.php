<?php
require_once("../config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $video = null;

    if (!empty($_FILES["video"]["name"])) {
        $targetDir = "../uploads/videos/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $video = time() . "_" . basename($_FILES["video"]["name"]);
        $ext = pathinfo($video, PATHINFO_EXTENSION);

        if (in_array(strtolower($ext), ["mp4","webm","ogg"])) {
            move_uploaded_file($_FILES["video"]["tmp_name"], $targetDir . $video);

            $sql = "INSERT INTO videos (user_id, title, filename) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $_SESSION["user_id"], $title, $video);
            $stmt->execute();

            header("Location: view.php");
        } else {
            echo "<p style='color:red;'>Дозволені формати: mp4, webm, ogg</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Завантажити відео</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<header><h1>Minetcord</h1></header>
<div class="container">
  <h2>Завантажити відео</h2>
  <form method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Назва відео" required><br>
    <input type="file" name="video" required><br>
    <input type="submit" value="Завантажити">
  </form>
</div>
</body>
</html>
<?php
require_once("../config.php");

$sql = "SELECT videos.*, users.username FROM videos 
        JOIN users ON videos.user_id = users.id 
        ORDER BY videos.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Відео – Minetcord</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<header><h1>Minetcord</h1></header>
<nav>
  <a href="../index.php">Головна</a>
  <a href="upload.php">Завантажити відео</a>
</nav>

<div class="container">
  <h2>Відео</h2>
  <?php while ($row = $result->fetch_assoc()) { ?>
    <div class="card">
      <h3><?= htmlspecialchars($row["title"]) ?></h3>
      <p>Автор: <?= htmlspecialchars($row["username"]) ?></p>
      <video width="100%" controls>
        <source src="../uploads/videos/<?= htmlspecialchars($row["filename"]) ?>" type="video/mp4">
        Ваш браузер не підтримує відео.
      </video>
    </div>
  <?php } ?>
</div>
</body>
</html>
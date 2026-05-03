<?php
require_once("config.php");

if (!isset($_SESSION["user_id"])) {
    echo "<div class='container' style='margin-top:20px;text-align:center;'>
            <a href='auth/login.php'>Увійти</a> | <a href='auth/register.php'>Реєстрація</a>
          </div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Minetcord</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header><h1>Minetcord</h1></header>
<nav>
  <a href="index.php">Головна</a>
  <a href="posts/create.php">+ Пост</a>
  <a href="videos/view.php">Відео</a>
  <a href="music/view.php">Музика</a>
  <a href="messages/index.php">Повідомлення</a>
  <a href="desktop.php">PC</a>
  <?php if ($_SESSION['user_id'] == 1) { ?>
      <a href="admin/index.php">Адмін</a>
  <?php } ?>
  <a href="auth/logout.php">Вийти</a>
</nav>

<div class="container">
  <h2>Стрічка</h2>
  <?php
  $sql = "SELECT posts.*, users.username 
          FROM posts 
          JOIN users ON posts.user_id = users.id 
          ORDER BY posts.created_at DESC";
  $result = $conn->query($sql);

  while ($row = $result->fetch_assoc()) {
      echo "<div class='card'>
              <h3>{$row['username']}</h3>
              <p>{$row['content']}</p>";
      if ($row['image']) {
          echo "<img src='uploads/images/{$row['image']}' style='max-width:100%;border-radius:5px;'>";
      }
      echo "<br><a href='posts/view.php?id={$row['id']}'>Коментарі</a>
            </div>";
  }
  ?>
</div>
</body>
</html>

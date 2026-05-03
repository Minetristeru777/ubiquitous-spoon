<?php
require_once("../config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

$result = $conn->query("SELECT id, username FROM users WHERE id != " . intval($_SESSION['user_id']));
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Повідомлення</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<header><h1>Minetcord</h1></header>
<nav>
  <a href="../index.php">Головна</a>
  <a href="../posts/create.php">+ Пост</a>
  <a href="../videos/view.php">Відео</a>
  <a href="../music/view.php">Музика</a>
  <a href="index.php">Повідомлення</a>
  <a href="../auth/logout.php">Вийти</a>
</nav>

<div class="container">
  <h2>Список користувачів</h2>
  <?php while($row = $result->fetch_assoc()) { ?>
    <div class="card">
      <a href="dialog.php?user_id=<?= $row['id'] ?>"><?= htmlspecialchars($row['username']) ?></a>
    </div>
  <?php } ?>
</div>
</body>
</html>
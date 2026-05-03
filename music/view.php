<?php

require_once("../config.php");



$result = $conn->query("SELECT music.*, users.username FROM music JOIN users ON music.user_id = users.id ORDER BY created_at DESC");

?>



<!DOCTYPE html>

<html lang="uk">

<head>

  <meta charset="UTF-8">

  <title>Музика</title>

  <link rel="stylesheet" href="../style.css">

</head>

<body>

<header><h1>Minetcord</h1></header>

<nav>

  <a href="../index.php">Головна</a>

  <a href="../posts/create.php">+ Пост</a>

  <a href="../videos/view.php">Відео</a>

  <a href="view.php">Музика</a>

  <a href="../messages/index.php">Повідомлення</a>

    <?php if ($_SESSION['user_id'] == 1) { ?>

      <a href="../admin/index.php">Адмін</a>

  <?php } ?>

  <a href="../auth/logout.php">Вийти</a>

</nav>



<div class="container">

  <h2>Музика</h2>

  <a href="upload.php">+ Завантажити трек</a>

  <?php while ($row = $result->fetch_assoc()) { ?>

    <div class="card">

      <b><?= htmlspecialchars($row["username"]) ?></b> — <?= htmlspecialchars($row["title"]) ?><br>

      <audio controls>

        <source src="../uploads/music/<?= htmlspecialchars($row["file"]) ?>" type="audio/mpeg">

        Ваш браузер не підтримує аудіо.

      </audio>

    </div>

  <?php } ?>

</div>

</body>

</html>
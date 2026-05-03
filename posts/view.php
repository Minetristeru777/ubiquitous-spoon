<?php
require_once("../config.php");

$id = intval($_GET["id"]);
$sql = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id=$id";
$result = $conn->query($sql);
$post = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["user_id"])) {
    $comment = $_POST["comment"];
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id, $_SESSION["user_id"], $comment);
    $stmt->execute();
}
$comments = $conn->query("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id=$id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Пост</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<header><h1>Minetcord</h1></header>
<div class="container">
  <h2><?= htmlspecialchars($post["username"]) ?></h2>
  <p><?= htmlspecialchars($post["content"]) ?></p>
  <?php if ($post["image"]) { ?>
    <img src="../uploads/images/<?= htmlspecialchars($post["image"]) ?>" style="max-width:100%;">
  <?php } ?>
</div>

<div class="container">
  <h3>Коментарі</h3>
  <?php while ($row = $comments->fetch_assoc()) { ?>
    <div class="card">
      <b><?= htmlspecialchars($row["username"]) ?>:</b> <?= htmlspecialchars($row["comment"]) ?>
    </div>
  <?php } ?>

  <?php if (isset($_SESSION["user_id"])) { ?>
  <form method="post">
    <textarea name="comment" placeholder="Ваш коментар..." required></textarea><br>
    <input type="submit" value="Надіслати">
  </form>
  <?php } else { ?>
    <p><a href="../auth/login.php">Увійдіть</a>, щоб коментувати</p>
  <?php } ?>
</div>
</body>
</html>
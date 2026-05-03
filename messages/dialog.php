<?php
require_once("../config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit;
}

$my_id = $_SESSION["user_id"];
$other_id = intval($_GET["user_id"]);

# Відправка повідомлення
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["message"])) {
    $msg = $_POST["message"];
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $my_id, $other_id, $msg);
    $stmt->execute();
}

# Завантажуємо чат
$sql = "SELECT messages.*, users.username FROM messages JOIN users ON messages.sender_id = users.id
        WHERE (sender_id=$my_id AND receiver_id=$other_id) OR (sender_id=$other_id AND receiver_id=$my_id)
        ORDER BY created_at ASC";
$result = $conn->query($sql);

$other_user = $conn->query("SELECT username FROM users WHERE id=$other_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Чат з <?= htmlspecialchars($other_user['username']) ?></title>
  <link rel="stylesheet" href="../style.css">
  <style>
    .chat-box {max-height:400px;overflow-y:scroll;border:1px solid #ccc;padding:10px;background:#fafafa;}
    .msg {padding:8px;margin:5px 0;border-radius:5px;}
    .me {background:#d1ffd1;text-align:right;}
    .other {background:#fff;border:1px solid #ddd;}
  </style>
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
  <h2>Чат з <?= htmlspecialchars($other_user['username']) ?></h2>
  <div class="chat-box">
    <?php while($row = $result->fetch_assoc()) { 
      $cls = $row['sender_id']==$my_id ? "me" : "other"; ?>
      <div class="msg <?= $cls ?>">
        <b><?= htmlspecialchars($row["username"]) ?>:</b> <?= htmlspecialchars($row["message"]) ?><br>
        <small><?= $row["created_at"] ?></small>
      </div>
    <?php } ?>
  </div>

  <form method="post" style="margin-top:10px;">
    <textarea name="message" placeholder="Ваше повідомлення..." required></textarea><br>
    <input type="submit" value="Надіслати">
  </form>
</div>
</body>
</html>
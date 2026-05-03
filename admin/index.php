<?php
require_once("../config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    die("? Доступ заборонено! Тільки для адміна.");
}

// Видалення користувача
if (isset($_GET['del_user'])) {
    $id = intval($_GET['del_user']);
    if ($id != 1) { // захист: не можна видалити адміна
        $conn->query("DELETE FROM users WHERE id=$id");
    }
}

// Видалення поста
if (isset($_GET['del_post'])) {
    $id = intval($_GET['del_post']);
    $conn->query("DELETE FROM posts WHERE id=$id");
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Адмін-панель</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<header><h1>Адмін-панель</h1></header>
<div class="container">
  <h2>Користувачі</h2>
  <ul>
    <?php
    $users = $conn->query("SELECT * FROM users");
    while ($u = $users->fetch_assoc()) {
        echo "<li>{$u['username']} ({$u['email']}) ";
        if ($u['id'] != 1) { // адміна не можна видалити
            echo "<a href='?del_user={$u['id']}' style='color:red;'>[видалити]</a>";
        }
        echo "</li>";
    }
    ?>
  </ul>

  <h2>Пости</h2>
  <ul>
    <?php
    $posts = $conn->query("SELECT posts.id, posts.content, users.username 
                           FROM posts JOIN users ON posts.user_id=users.id");
    while ($p = $posts->fetch_assoc()) {
        echo "<li><b>{$p['username']}:</b> {$p['content']} 
              <a href='?del_post={$p['id']}' style='color:red;'>[видалити]</a></li>";
    }
    ?>
  </ul>
</div>
</body>
</html>

<?php
require_once("../config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $pass = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $pass);
    if ($stmt->execute()) {
        header("Location: login.php");
    } else {
        $error = "Помилка: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Реєстрація</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<header><h1>Minetcord</h1></header>
<div class="container">
  <h2>Реєстрація</h2>
  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <form method="post">
    <input type="text" name="username" placeholder="Ім'я" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Пароль" required><br>
    <input type="submit" value="Зареєструватись">
  </form>
</div>
</body>
</html>
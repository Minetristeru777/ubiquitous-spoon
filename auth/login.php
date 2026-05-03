<?php
require_once("../config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $pass = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($pass, $row["password"])) {
            $_SESSION["user_id"] = $row["id"];
            header("Location: ../index.php");
            exit;
        } else {
            $error = "Невірний пароль";
        }
    } else {
        $error = "Користувач не знайдений";
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <title>Вхід</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<header><h1>Minetcord</h1></header>
<div class="container">
  <h2>Вхід</h2>
  <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <form method="post">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Пароль" required><br>
    <input type="submit" value="Увійти">
  </form>
</div>
</body>
</html>
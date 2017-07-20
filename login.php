<?php
include_once 'login_query.php';

if (isLogged()) {
  redirect('index.php');
}

if (isSignIn()) {
  if (login($db, $login, $password)) {
    redirect('index.php');
  } else {
    $errors[] = 'Неверный логин или пароль';
  }
}

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
</head>
<body>
  <p>Введите данные для регистрации или войдите, если уже регистрировались:</p>

  <?php foreach ($errors as $error): ?>
    <p><?= $error; ?></p>
  <?php endforeach; ?>

  <p>Авторизация:</p>

  <form method="POST">
    <input type="text" name="login" placeholder="Логин">
    <input type="password" name="password" placeholder="Пароль">
    <input type="submit" name="sign_in" value="Вход">
  </form>

  <p>Регистрация:</p>

  <form method="POST">
    <input type="text" name="login" placeholder="Логин">
    <input type="password" name="password" placeholder="Пароль">
    <input type="submit" name="register" value="Регистрация">
  </form>

</body>
</html>
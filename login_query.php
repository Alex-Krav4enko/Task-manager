<?php
require_once 'database/DataBase.php';
include_once 'functions.php';

$login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

$errors = [];

if (isset($_POST['register'])) {
  if (!empty($login && $password)) {
    $sql_select = "SELECT login FROM user WHERE login = :exist_login";
    $statement = $db->prepare($sql_select);
    $statement->execute([
      'exist_login' => $login
    ]);
    foreach ($statement as $loginArr) {
      $existLogin = $loginArr['login'];
    }

    if (empty($existLogin)) {
      $sql_insert = "INSERT INTO user (login, password) VALUES (:prepare_login, :prepare_password)";
      $statement = $db->prepare($sql_insert);
      $statement->execute([
        'prepare_login' => $login,
        'prepare_password' => password_hash($password, PASSWORD_DEFAULT)
      ]);
    } else {
      $errors[] = 'Такое имя пользователя уже существует';
    }
  }

  if (empty($login)) {
    $errors[] = 'Вы не указали имя пользователя';
  }

  if (empty($password)) {
    $errors[] = 'Вы не указали пароль';
  }
}

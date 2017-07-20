<?php
session_start();

function getUser($pdo, $login)
{
  $sql_select = "SELECT * FROM user WHERE login = :login";
  $statement = $pdo->prepare($sql_select);
  $statement->execute([
    'login' => $login
  ]);
  $user = $statement->fetch(PDO::FETCH_LAZY);
  if (strcmp($user['login'], $login) === 0) {
    return $user;
  }
  return null;
}

function login($pdo, $login, $password) {
  $user = getUser($pdo, $login);
  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user']['id'] = $user['id'];
    $_SESSION['user']['login'] = $user['login'];
    return true;
  }
  return false;
}

function isLogged() {
  return !empty($_SESSION['user']);
}

function redirect($location) {
  header("Location: $location");
  die;
}

function isPost() {
  return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function isSignIn() {
  return isset($_POST['sign_in']);
}

function logOut() {
  session_destroy();
}